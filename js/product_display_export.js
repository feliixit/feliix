var app = new Vue({
  el: "#app",
  data: {
    
    submit: false,

    //baseURL: "https://storage.googleapis.com/feliiximg/",
    baseURL: 'https://storage.googleapis.com/feliiximg/',

    category: "",
    sub_category: "",
    sub_category_name: "",
    sub_cateory_item: [],

    //
    id:-1,
    record: [],
    tags: [],

    cateory_item: [],

    special_infomation: [],
    special_infomation_detail: [],

    accessory_infomation: [],
    sub_accessory_item: [],
    attributes:[],

    //
    accessory_item: [],

    edit_mode: true,

    title: [],

    title_id: 0,

    url: null,
    url1: null,
    url2: null,
    url3: null,

    // data
    brand: "",
    code: "",
    price_ntd: "",
    price: "",
    price_quoted: "",
    price_ntd_change: "",
    price_change: "",
    price_ntd_org: "",
    price_org: "",
    description: "",
    notes: "",
    accessory_mode: false,
    variation_mode: false,

    quoted_price:"",
    quoted_price_org:"",
    quoted_price_change:"",
    moq:"",
    currency: "",

    // accessory

    // variation
    variation1: "",
    variation2: "",
    variation3: "",
    variation4: "",
    variation1_custom: "",
    variation2_custom: "",
    variation3_custom: "",
    variation4_custom: "",

    variation1_text: "1st Variation",
    variation2_text: "2nd Variation",
    variation3_text: "3rd Variation",
    variation4_text: "4th Variation",

    variation1_value: [],
    variation2_value: [],
    variation3_value: [],
    variation4_value: [],

    variation_product: [],

    related_product : [],
    nColumns: 4,
    groupedItems: [],

    replacement_product : [],
    is_replacement_product: [],

    show_accessory: false,

    v1:"",
    v2:"",
    v3:"",
    v4:"",

    // bulk insert
    code_checked:'',
    bulk_code:'',
    price_ntd_checked:'',
    bulk_price_ntd:'',
    price_ntd_action :'',
    price_checked:'',
    bulk_price:'',
    price_action:'',
    image_checked:'',
    bulk_url:'',
    status_checked:'',
    bulk_status:'',
    currency:'',

    submit: false,

    specification: [],

    // info
    name :"",
    title: "",
    is_manager: "",
    cost_lighting : false,
    cost_furniture : false,
  },

  created() {
    let _this = this;
    let uri = window.location.href.split('?');

    if (uri.length >= 2)
    {
      let vars = uri[1].split('&');
      
      let tmp = '';
      vars.forEach(function(v){
        tmp = v.split('=');
        if(tmp.length == 2)
        {
          switch (tmp[0]) {
            case "id":
              id = tmp[1];
              _this.id = id;
              
              break;
   
            default:
              console.log(`Too many args`);
          }
          //_this.proof_id = tmp[1];
        }
      });
    }

    this.get_records(this.id);
    this.getUserName();
    this.getProductControl();
  },

  computed: {
    show_ntd : function() {
      //if(this.name.toLowerCase() ==='dereck' || this.name.toLowerCase() ==='ariel lin' || this.name.toLowerCase() ==='kuan' || this.name.toLowerCase() ==='testmanager')
      if((this.cost_lighting == true && this.category == 'Lighting') || (this.cost_furniture == true && this.category == 'Systems Furniture'))
       return true;
      else
      return false;
    }
  },

  mounted() {
   
  },

  watch: {
    price_ntd() {
      if(this.price_ntd != this.price_ntd_org)
        this.price_ntd_change = new Date().toISOString().slice(0, 10);
    },

    price () {
      if(this.price != this.price_org)
        this.price_change = new Date().toISOString().slice(0, 10);
    },

    quoted_price () {
      if(this.quoted_price != this.quoted_price_org)
        this.quoted_price_change = new Date().toISOString().slice(0, 10);
    },

    accessory_infomation() {
      for(var i = 0; i < this.accessory_infomation.length; i++)
      {
        $('#tag' + i).selectpicker('refresh');
      } 
    },
  },

  methods: {
    getProductControl: function() {
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

      form_Data.append('jwt', token);

      axios({
          method: 'get',
          headers: {
              'Content-Type': 'multipart/form-data',
          },
          url: 'api/product_control',
          data: form_Data
      })
      .then(function(response) {
          //handle success
          _this.cost_lighting = response.data.cost_lighting;
          _this.cost_furniture = response.data.cost_furniture;

      })
      .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: 'error',
            confirmButtonText: 'OK'
          })
      });
    },
    chunk: function(arr, size, item) {
      var newArr = [];
      for (var i=0; i<arr.length; i+=size) {
        newArr.push(arr.slice(i, i+size));
      }
      item.groupedItems  = newArr;
    },

    getUserName: function() {
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

      form_Data.append('jwt', token);

      axios({
          method: 'post',
          headers: {
              'Content-Type': 'multipart/form-data',
          },
          url: 'api/on_duty_get_myname',
          data: form_Data
      })
      .then(function(response) {
          //handle success
          _this.name = response.data.username;
          _this.is_manager = response.data.is_manager;
          _this.title = response.data.title.toLowerCase();

      })
      .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: 'error',
            confirmButtonText: 'OK'
          })
      });
    },

    search() {
      this.filter_apply();
    },

    edit_category() {
      this.edit_mode = true;

      console.log("edit category");
    },

    change_v(item){
      let item_product = this.shallowCopy(
        item.product.find((element) => element.v1 == item.v1 && element.v2 == item.v2 && element.v3 == item.v3 && element.v4 == item.v4)
      )

      if(item_product.id != undefined)
      {
        if(item_product.photo != "")
          item.url = this.baseURL + item_product.photo;
        else
        item.url = "";
        
        item.price = "PHP " + Number(item_product.price).toLocaleString();
        item.quoted_price = "PHP " + Number(item_product.quoted_price).toLocaleString();
      }
      else
      {
        item.url = item.url1;
       
        item.price = item['price'];
        item.quoted_price = item['quoted_price'];
      }

    },

    set_up_specification(special_infomation, item) {
       let k1 = '';
       let k2 = '';

       let v1 = '';
       let v2 = '';


      for(var i=0; i < special_infomation.length; i++)
      {
        if(special_infomation[i].value != "")
        {
          if(k1 == "")
          {
            k1 = special_infomation[i].category;
            v1 = special_infomation[i].value;
          }else if(k1 !== "" && k2 == "")
          {
            k2 = special_infomation[i].category;
            v2 = special_infomation[i].value;

            obj = {k1: k1, v1: v1, k2: k2, v2: v2};
            item['specification'].push(obj);
            k1  = '';
            k2  = '';
            v1  = '';
            v2  = '';
          }
        }
      }

      if(k1 == "" && item['moq'] !== "")
      {
        k1 = 'MOQ';
        v1 = item['moq'];
      }else if(k1 !== "" && k2 == "" && item['moq'] !== "")
      {
        k2 = 'MOQ';
        v2 = item['moq'];
    
        obj = {k1: k1, v1: v1, k2: k2, v2: v2};
        item['specification'].push(obj);
        k1  = '';
        k2  = '';
        v1  = '';
        v2  = '';
      }
/*
      if(k1 == "" && this.record[0]['notes'] !== "")
      {
        k1 = 'Notes';
        v1 = this.record[0]['notes'];
      }else if(k1 !== "" && k2 == "" && this.record[0]['notes'] !== "")
      {
        k2 = 'Notes';
        v2 = this.record[0]['notes'];
    
        obj = {k1: k1, v1: v1, k2: k2, v2: v2};
        this.specification.push(obj);
        k1  = '';
        k2  = '';
        v1  = '';
        v2  = '';
      }
*/
      if(k1 !== "" && k2 == "")
      {
        k2 = '';
        v2 = '';
    
        obj = {k1: k1, v1: v1, k2: k2, v2: v2};
        item['specification'].push(obj);
      
      }
    },

    set_up_variants() {
      for(var i=0; i<this.variation1_value.length; i++)
      {
        $('#variation1_value').tagsinput('add', this.variation1_value[i]);
      }
      for(var i=0; i<this.variation2_value.length; i++)
      {
        $('#variation2_value').tagsinput('add', this.variation2_value[i]);
      }
      for(var i=0; i<this.variation3_value.length; i++)
      {
        $('#variation3_value').tagsinput('add', this.variation3_value[i]);
      }
      for(var i=0; i<this.variation4_value.length; i++)
      {
        $('#variation4_value').tagsinput('add', this.variation4_value[i]);
      }
      
    },

    set_special_attributes() {
      for (let i = 0; i < this.attributes.length; i++) {
        let cat_id = this.attributes[i].cat_id;
        let value = this.attributes[i].value;
        for (let j = 0; j < this.special_infomation.length; j++) {
          if(this.special_infomation[j].cat_id == cat_id)
            this.$refs[cat_id][0].value = value;
        }
      }
    },

    change_url: function(url) {
        this.url = url;
    },

    get_records: function(id) {
        let _this = this;

        if(id === -1)
            return;

        const params = {
          id: this.id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/product_display_export", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(function(response) {
            console.log(response.data);
            _this.record = response.data;

            for (let i = 0; i < _this.record.products.length; i++) {
              _this.set_up_specification(_this.record.products[i].special_information[0].lv3[0], _this.record.products[i]);
              //_this.set_up_variants(_this.record.products[i]);

              _this.chunk(_this.record.products[i].related_product, 4, _this.record.products[i]);
            }

          })
          .catch(function(error) {
            console.log(error);
          });
      },

    add_accessory_item: function(cat_id) {
      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      let sn = 0;
      for (let i = 0; i < items.length; i++) {
        if (items[i].id > sn) {
          sn = items[i].id;
        }
      }

      sn = sn + 1;

      item = {
        id: sn,
        cat_id: cat_id,
        url: "",
        file: {
          name: "",
        },
        code: "",
        name: "",
        price_ntd: "",
        price: "",
        photo:"",
      };

      items.push(item);
    },

    remove_accessory_item: function(cat_id, id) {
      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      for (i = 0; i < items.length; i++) {
        if (items[i].id == id) {
          items.splice(i, 1);
          i = i - 1;
        }
      }
    },

    clear_accessory_item: function(cat_id, id) {
      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      for (i = 0; i < items.length; i++) {
        if (items[i].id == id) {
          items[i].code = "";
          items[i].name = "";
          items[i].price_ntd = "";
          items[i].price = "";
          items[i].url = "";
          items[i].file.value = "";
        }
      }
    },

    print_page() {
      window.print();
    },

    onFileChange(e, num) {
      const file = e.target.files[0];

      if (num === 1) {
        this.url1 = URL.createObjectURL(file);
      }
      if (num === 2) {
        this.url2 = URL.createObjectURL(file);
      }
      if (num === 3) {
        this.url3 = URL.createObjectURL(file);
      }
    },

    onFileChangeBulkImage(e) {
      const file = e.target.files[0];
      this.bulk_url = URL.createObjectURL(file);
    },

    onFileChangeAccessory(e, cat_id, id) {
      const file = e.target.files[0];

      let items = this.shallowCopy(
        this.accessory_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      let url = URL.createObjectURL(file);

      for (i = 0; i < items.length; i++) {
        if (items[i].id == id) {
          items[i].url = url;
        }
      }
    },

    clear_photo(num) {
      if (num === 1) {
        this.url1 = null;
      }
      if (num === 2) {
        this.url2 = null;
      }
      if (num === 3) {
        this.url3 = null;
      }
    },

    clear_bulk_image() {
      this.url = null;
    },

    get_special_infomation_detail: function(cat_id) {
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.cat_id == cat_id)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign").toggle();
    },

    get_special_infomation_detail_variantion1: function() {
      if(this.variation1 == "" || this.variation1 == "custom") 
        return;
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.category == this.variation1)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_1").toggle();
    },

    get_special_infomation_detail_variantion2: function() {
      if(this.variation2 == "" || this.variation2 == "custom") 
        return;
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.category == this.variation2)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_2").toggle();
    },

    get_special_infomation_detail_variantion3: function() {
      if(this.variation3 == "" || this.variation3 == "custom") 
        return;
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.category == this.variation3)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_3").toggle();
    },

    get_special_infomation_detail_variantion4: function() {
      if(this.variation4 == "" || this.variation4 == "custom") 
        return;
      this.special_infomation_detail = this.shallowCopy(
        this.special_infomation.find((element) => element.category == this.variation4)
      ).detail[0];

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_4").toggle();
    },

    apply_special_infomation_detail: function(cat_id, option) {
      this.$refs[cat_id][0].value = option;

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign").toggle();
    },

    apply_special_infomation_detail_variantion1: function() {
      var checkboxes = document.getElementsByName("apply_special_infomation_1");
 
      checkboxes.forEach(function(box) {
        if (box.checked) 
        {
          $('#variation1_value').tagsinput('add', box.value);
          box.checked = false;
        }
      })

      //let variation_value = document
      //  .getElementById("variation1_value")
      //  .value.split(",");

      //variation_value.push(values);

      //document.getElementById("variation1_value").value = variation_value.join(",");

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_1").toggle();

      //$('variation1_value').tagsinput('refresh');
    },

    apply_special_infomation_detail_variantion2: function() {
      var checkboxes = document.getElementsByName("apply_special_infomation_2");

      checkboxes.forEach(function(box) {
        if (box.checked) 
        {
          $('#variation2_value').tagsinput('add', box.value);
          box.checked = false;
        }
      })

   
      // let variation_value = document
      //   .getElementById("variation2_value")
      //   .value.split(",");

      // variation_value.push(values);

      // document.getElementById("variation2_value").value = variation_value.join(",");

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_2").toggle();

      //$('#variation2_value').tagsinput('refresh');
    },

    apply_special_infomation_detail_variantion3: function() {
      var checkboxes = document.getElementsByName("apply_special_infomation_3");
      
      checkboxes.forEach(function(box) {
        if (box.checked) 
        {
          $('#variation3_value').tagsinput('add', box.value);
          box.checked = false;
        }
      })

      // let variation_value = document
      //   .getElementById("variation3_value")
      //   .value.split(",");

      // variation_value.push(values);

      // document.getElementById("variation3_value").value = variation_value.join(",");

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_3").toggle();

      //$('variation3_value').tagsinput('refresh');
    },

    apply_special_infomation_detail_variantion4: function() {
      var checkboxes = document.getElementsByName("apply_special_infomation_4");
      
      checkboxes.forEach(function(box) {
        if (box.checked) 
        {
          $('#variation4_value').tagsinput('add', box.value);
          box.checked = false;
        }
      })

      // let variation_value = document
      //   .getElementById("variation3_value")
      //   .value.split(",");

      // variation_value.push(values);

      // document.getElementById("variation3_value").value = variation_value.join(",");

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_quick_assign2_4").toggle();

      //$('variation3_value').tagsinput('refresh');
    },

    variation_mode_change: function(e) {
      const chk = e.target.checkbox[0];

      if (chk.checked) {
        this.variation_mode = true;
      }
      if (!chk.checked) {
        this.variation_mode = false;
      }
    },


    accessory_mode_change: function(e) {
      const chk = e.target.checkbox[0];

      if (chk.checked) {
        this.accessory_mode = true;
      }
      if (!chk.checked) {
        this.accessory_mode = false;
      }
    },


    get_positions: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/position_get", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.position = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: "smooth" });
    },

    cancel: function() {
      this.reset();
    },

    check_input: function(){
      return "";
    },


    reset: function() {
      return;

    },

    toggle_product() {
      let toogle = document.getElementById('select_all_product').checked;
        for(var i = 0; i < this.variation_product.length; i++) {
          this.variation_product[i].checked = toogle;
      }
    },

    bulk_toggle_product() {
      let toogle = document.getElementById('bulk_select_all_product').checked;
      this.code_checked = toogle;
      this.price_ntd_checked = toogle;
      this.price_checked = toogle;
      this.image_checked = toogle;
      this.status_checked = toogle;
      
    },

    clear_accessory() {
      for (var i in this.accessory_infomation) {
        this.accessory_infomation[i].detail[0] = [];
      }
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },
  },
});
