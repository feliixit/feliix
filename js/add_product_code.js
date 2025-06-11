var app = new Vue({
  // vuetify: new Vuetify(),
  el: "#app",
  data: {
    submit: false,

    category: "",
    sub_category: "",
    sub_cateory_item: [],

    //
    cateory_item: [],

    special_infomation: [],
    special_infomation_detail: [],

    accessory_infomation: [],
    sub_accessory_item: [],

    //
    accessory_item: [],

    edit_mode: false,

    title: [],

    title_id: 0,

    url1: null,
    url2: null,
    url3: null,

    // data
    brand: "",
    code: "",
    price_ntd: "",
    price: "",
    price_ntd_change: "",
    price_change: "",
    moq: "",
    description: "",
    out:'',
    notes: "",
    accessory_mode: false,
    variation_mode: false,

    currency:"NTD",

    quoted_price:"",
    quoted_price_change:"",
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

    variation_product: [],

    // product set
    p1_code : "",
    p1_qty : "",
    p1_id : "",

    p2_code : "",
    p2_qty : "",
    p2_id : "",

    p3_code : "",
    p3_qty : "",
    p3_id : "",


    // info
    name :"",
    title: "",
    is_manager: "",
    
    // bulk insert
    code_checked:'',
    bulk_code:'',
    price_ntd_checked:'',
    bulk_price_ntd:'',
    price_ntd_action :'',
    price_checked:'',
    quoted_price_checked:'',
    bulk_quoted_price:'',
    bulk_price:'',
    price_action:'',
    quoted_price_action:'',
    image_checked:'',
    bulk_url:'',
    status_checked:'',
    bulk_status:'',



    price_ntd_last_change_checked:'',
    bulk_price_ntd_last_change: '',

    price_last_change_checked:'',
    bulk_price_last_change:'',

    quoted_price_last_change_checked:'',
    bulk_quoted_price_last_change:'',

    submit: false,

    tag_group : [],

    brand_handler: '',

    cost_lighting : false,
    cost_furniture : false,

    // replacement
    replacement_json: [],

  },

  created() {
    this.product_get_category_item();
    this.accessory_get_category_item();
    this.getUserName();
    this.getTagGroup();
    this.getProductControl();
  },

  computed: {
    show_ntd : function() {
      // if(this.name.toLowerCase() ==='dereck' || this.name.toLowerCase() ==='ariel lin' || this.name.toLowerCase() ==='kuan' || this.name.toLowerCase() ==='testmanager')
      if((this.cost_lighting == true && this.category == '10000000') || (this.cost_furniture == true && this.category == '20000000'))
       return true;
      else
      return false;
    }
  },

  mounted() {},

  watch: {
    category() {
      if (this.category == "") return;

      this.sub_cateory_item = this.shallowCopy(
        this.cateory_item.find((element) => element.cat_id == this.category)
      ).lv2[0];

      this.sub_accessory_item = this.shallowCopy(
        this.accessory_item.find((element) => element.cat_id == this.category)
      ).lv2[0];

      // if(this.category == "10000000")
      //   this.sub_category = "10010000";
    },

    sub_category() {
      if (this.sub_category == "") return;

      this.special_infomation = this.shallowCopy(
        this.sub_cateory_item.find(
          (element) => element.cat_id == this.sub_category
        )
      ).lv3[0];

      this.accessory_infomation = this.shallowCopy(
        this.sub_accessory_item.find(
          (element) => element.cat_id == this.sub_category
        )
      ).lv3[0];
    },

    price_ntd() {
      this.price_ntd_change = new Date().toISOString().slice(0, 10);
    },

    price () {
      this.price_change = new Date().toISOString().slice(0, 10);
    },

    quoted_price () {
      this.quoted_price_change = new Date().toISOString().slice(0, 10);
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

    
    auto_complete_product_code: async function(key) {
      let _this = this;
      let token = localStorage.getItem("accessToken");
      let params = {
        code: key,
      };

      let ret = await axios.get("api/product_code_auto_complete", { params, headers: {"Authorization" : `Bearer ${token}`} });

      return ret.data;
    },

    getTagGroup: function() {
      let _this = this;
        
          let token = localStorage.getItem('accessToken');
          const params = {

        };
          axios
              .get('api/tag_mgt_get', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.tag_group = res.data;

              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
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

    // search() {
    //   this.filter_apply();
    // },

    edit_category() {
      this.edit_mode = true;
$("#tag01").selectpicker("refresh");
$("#tag0102").selectpicker("refresh");
      console.log("edit category");

      if(this.category == '10000000')
      {
        this.brand_handler = "";
        this.currency = "NTD";
      }

      if(this.category == '20000000')
      {
        this.brand_handler = "PH";
        this.currency = "PHP";
      }
    },

    product_get_category_item: function() {
      let _this = this;

      const params = {
        id: "",
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/product_get_category_item", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.cateory_item = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    generate_product_variants: function() {
      this.variation1_text =
        this.variation1 === "custom" ? this.variation1_custom : this.variation1;
      this.variation2_text =
        this.variation2 === "custom" ? this.variation2_custom : this.variation2;
      this.variation3_text =
        this.variation3 === "custom" ? this.variation3_custom : this.variation3;
      this.variation4_text =
        this.variation4 === "custom" ? this.variation4_custom : this.variation4;

      let variation1_value = document
        .getElementById("variation1_value")
        .value.split(",");
      let variation2_value = document
        .getElementById("variation2_value")
        .value.split(",");
      let variation3_value = document
        .getElementById("variation3_value")
        .value.split(",");
      let variation4_value = document
        .getElementById("variation4_value")
        .value.split(",");

        let variation_product_preserved = JSON.parse(JSON.stringify(this.variation_product));

        this.variation_product = [];

      sn = 0;
      for (let i = 0; i < variation1_value.length; i++) {
        for (let j = 0; j < variation2_value.length; j++) {
          for (let k = 0; k < variation3_value.length; k++) {
            for (let l = 0; l < variation4_value.length; l++) {
              sn = sn + 1;

              pre_url = "";
              pre_price_ntd = "";
              pre_price = "";
              pre_price_change = "";
              pre_price_ntd_change = "";
              pre_price_org = "";
              pre_price_ntd_org = "";
              pre_photo = "";
              pre_quoted_price = "";
              pre_quoted_price_org = "";
              pre_quoted_price_change = "";

              for(let m=0; m<variation_product_preserved.length; m++)
              {
                if(variation_product_preserved[m].k1 != '' && variation_product_preserved[m].k2 != '' && variation_product_preserved[m].k3 != '' && variation_product_preserved[m].k4 != '')
                {
                  if(variation_product_preserved[m].v1 == variation1_value[i] && variation_product_preserved[m].v2 == variation2_value[j] && variation_product_preserved[m].v3 == variation3_value[k] && variation_product_preserved[m].v4 == variation4_value[l])
                  {
                    pre_url = variation_product_preserved[m].url;
                    pre_price_ntd = variation_product_preserved[m].price_ntd;
                    pre_price = variation_product_preserved[m].price;
                    pre_price_change = variation_product_preserved[m].price_change;
                    pre_price_ntd_change = variation_product_preserved[m].price_ntd_change;
                    pre_price_org = variation_product_preserved[m].price_org;
                    pre_price_ntd_org = variation_product_preserved[m].price_ntd_org;
                    pre_photo = variation_product_preserved[m].photo;
                    pre_quoted_price = variation_product_preserved[m].quoted_price;
                    pre_quoted_price_org = variation_product_preserved[m].quoted_price_org;
                    pre_quoted_price_change = variation_product_preserved[m].quoted_price_change;
    
                    break;
                  }
                }

                if(variation_product_preserved[m].k1 != '' && variation_product_preserved[m].k2 != '' && variation_product_preserved[m].k3 != '' && variation_product_preserved[m].k4 == '')
                  {
                    if(variation_product_preserved[m].v1 == variation1_value[i] && variation_product_preserved[m].v2 == variation2_value[j] && variation_product_preserved[m].v3 == variation3_value[k])
                    {
                      pre_url = variation_product_preserved[m].url;
                      pre_price_ntd = variation_product_preserved[m].price_ntd;
                      pre_price = variation_product_preserved[m].price;
                      pre_price_change = variation_product_preserved[m].price_change;
                      pre_price_ntd_change = variation_product_preserved[m].price_ntd_change;
                      pre_price_org = variation_product_preserved[m].price_org;
                      pre_price_ntd_org = variation_product_preserved[m].price_ntd_org;
                      pre_photo = variation_product_preserved[m].photo;
                      pre_quoted_price = variation_product_preserved[m].quoted_price;
                      pre_quoted_price_org = variation_product_preserved[m].quoted_price_org;
                      pre_quoted_price_change = variation_product_preserved[m].quoted_price_change;
      
                      break;
                    }
                  }

                if(variation_product_preserved[m].k1 != '' && variation_product_preserved[m].k2 != '' && variation_product_preserved[m].k3 == '' && variation_product_preserved[m].k4 == '')
                {
                  if(variation_product_preserved[m].v1 == variation1_value[i] && variation_product_preserved[m].v2 == variation2_value[j])
                  {
                    pre_url = variation_product_preserved[m].url;
                    pre_price_ntd = variation_product_preserved[m].price_ntd;
                    pre_price = variation_product_preserved[m].price;
                    pre_price_change = variation_product_preserved[m].price_change;
                    pre_price_ntd_change = variation_product_preserved[m].price_ntd_change;
                    pre_price_org = variation_product_preserved[m].price_org;
                    pre_price_ntd_org = variation_product_preserved[m].price_ntd_org;
                    pre_photo = variation_product_preserved[m].photo;
                    pre_quoted_price = variation_product_preserved[m].quoted_price;
                    pre_quoted_price_org = variation_product_preserved[m].quoted_price_org;
                    pre_quoted_price_change = variation_product_preserved[m].quoted_price_change;
    
                    break;
                  }
                }

                if(variation_product_preserved[m].k1 != '' && variation_product_preserved[m].k2 == '' && variation_product_preserved[m].k3 == '' && variation_product_preserved[m].k4 == '')
                {
                  if(variation_product_preserved[m].v1 == variation1_value[i])
                  {
                    pre_url = variation_product_preserved[m].url;
                    pre_price_ntd = variation_product_preserved[m].price_ntd;
                    pre_price = variation_product_preserved[m].price;
                    pre_price_change = variation_product_preserved[m].price_change;
                    pre_price_ntd_change = variation_product_preserved[m].price_ntd_change;
                    pre_price_org = variation_product_preserved[m].price_org;
                    pre_price_ntd_org = variation_product_preserved[m].price_ntd_org;
                    pre_photo = variation_product_preserved[m].photo;
                    pre_quoted_price = variation_product_preserved[m].quoted_price;
                    pre_quoted_price_org = variation_product_preserved[m].quoted_price_org;
                    pre_quoted_price_change = variation_product_preserved[m].quoted_price_change;
    
                    break;
                  }
                }
              }

              variation_item = {
                id: sn,
                checked: 0,
                k1: this.variation1_text,
                k2: this.variation2_text,
                k3: this.variation3_text,
                k4: this.variation4_text,
                v1: variation1_value[i],
                v2: variation2_value[j],
                v3: variation3_value[k],
                v4: variation4_value[l],
                url: pre_url,
                file: {
                  name: "",
                },
                code: "",
                price_ntd: pre_price_ntd,
                price: pre_price,
                price_change: pre_price_change,
                price_ntd_change: pre_price_ntd_change,
                price_org : pre_price_org,
                price_ntd_org : pre_price_ntd_org,
                photo: pre_photo,
                status: "1",
                quoted_price: pre_quoted_price,
                quoted_price_org: pre_quoted_price_org,
                quoted_price_change: pre_quoted_price_change,
              };

              this.variation_product.push(variation_item);
            }
          }
        }
      }
    },

    product_price_ntd_changed: function(item_id) {
      for (var i = 0; i < this.variation_product.length; i++) {
        if (this.variation_product[i].id == item_id) 
          this.variation_product[i].price_ntd_change = new Date().toISOString().slice(0, 10);
      }
    },

    product_price_changed: function(item_id) {
      for (var i = 0; i < this.variation_product.length; i++) {
        if (this.variation_product[i].id == item_id) 
          this.variation_product[i].price_change = new Date().toISOString().slice(0, 10);
      }
    },

    product_quoted_price_changed: function(item_id) {
      for (var i = 0; i < this.variation_product.length; i++) {
        if (this.variation_product[i].id == item_id) 
          this.variation_product[i].quoted_price_change = new Date().toISOString().slice(0, 10);
      }
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

    clear_variation_item: function(id) {
      for (i = 0; i < this.variation_product.length; i++) {
        if (this.variation_product[i].id == id) {
          this.variation_product[i].url = "";
          document.getElementById('variation_'+id).value = "";
        }
      }
    },

    accessory_get_category_item: function() {
      let _this = this;

      const params = {
        id: "",
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/accessory_get_category_item", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.accessory_item = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    check_ics(e)
    {
      // check extension and file size
      let files = e.target.files;
      for (var i = 0; i < files.length; i++)
      {
        let file = files[i];
        if(file.name.split('.').pop().toLowerCase() != 'ies')
        {
          Swal.fire({
            text: "The extension of all selected files need to be “.ies”.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          e.target.value = '';
          return;
        }

        if(file.size > 1024 * 1024 * 10)
        {
          Swal.fire({
            text: "The size of selected file should be less than 10MB.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          e.target.value = '';
          return;
        }
      }
    },

    check_skp(e)
    {
      // check extension and file size
      let files = e.target.files;
      for (var i = 0; i < files.length; i++)
      {
        let file = files[i];
        if(file.name.split('.').pop().toLowerCase() != 'skp')
        {
          Swal.fire({
            text: "The extension of all selected files need to be “.skp”.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          e.target.value = '';
          return;
        }

        if(file.size > 1024 * 1024 * 10)
        {
          Swal.fire({
            text: "The size of selected file should be less than 10MB.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          e.target.value = '';
          return;
        }
      }
    },

    check_manual(e)
    {
      let files = e.target.files;
      for (var i = 0; i < files.length; i++)
      {
        let file = files[i];

        if(file.name.split('.').pop() != 'zip' && file.name.split('.').pop() != 'rar' && 
          file.name.split('.').pop() != '7z' && file.name.split('.').pop() != 'pdf' && 
          file.name.split('.').pop() != 'doc' && file.name.split('.').pop() != 'docx' && 
          file.name.split('.').pop() != 'xls' && file.name.split('.').pop() != 'xlsx' && 
          file.name.split('.').pop() != 'ppt' && file.name.split('.').pop() != 'pptx' && 
          file.name.split('.').pop() != 'jpg' && file.name.split('.').pop() != 'jpeg' && 
          file.name.split('.').pop() != 'png' && file.name.split('.').pop() != 'gif' && 
          file.name.split('.').pop() != 'bmp' && file.name.split('.').pop() != 'tiff' && 
          file.name.split('.').pop() != 'svg')
        {
          Swal.fire({
            text: "Each selected file needs to be picture, Microsoft office document, pdf or compressed file.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          e.target.value = '';
          return;
        }
        
        if(file.size > 1024 * 1024 * 10)
        {
          Swal.fire({
            text: "The size of selected file should be less than 10MB.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          e.target.value = '';
          return;
        }
      }

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

    onFileChangeVariation(e, id) {
      const file = e.target.files[0];

      let url = URL.createObjectURL(file);

      for (i = 0; i < this.variation_product.length; i++) {
        if (this.variation_product[i].id == id) {
          this.variation_product[i].url = url;
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

      document.getElementById('photo'+num).value = "";
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

    bulk_apply: function(){
      if(this.code_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          this.variation_product[i].code = this.bulk_code;
        }
      }

      if(this.price_ntd_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          if(this.price_ntd_action == "assign")
            this.variation_product[i].price_ntd = this.bulk_price_ntd;
          if(this.price_ntd_action == "add")
            this.variation_product[i].price_ntd = Number(this.variation_product[i].price_ntd) + Number(this.bulk_price_ntd);
          if(this.price_ntd_action == "multiply")
            this.variation_product[i].price_ntd = (this.variation_product[i].price_ntd * this.bulk_price_ntd);

            this.variation_product[i].price_ntd_change = new Date().toISOString().slice(0, 10);
        }
      }

      if(this.price_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          if(this.price_action == "assign")
            this.variation_product[i].price = this.bulk_price;
          if(this.price_action == "add")
            this.variation_product[i].price = Number(this.variation_product[i].price) + Number(this.bulk_price);
          if(this.price_action == "multiply")
            this.variation_product[i].price = (this.variation_product[i].price * this.bulk_price);

            this.variation_product[i].price_change = new Date().toISOString().slice(0, 10);
        }
      }

      if(this.quoted_price_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          if(this.quoted_price_action == "assign")
            this.variation_product[i].quoted_price = this.bulk_quoted_price;
          if(this.quoted_price_action == "add")
            this.variation_product[i].quoted_price = Number(this.variation_product[i].quoted_price) + Number(this.bulk_quoted_price);
          if(this.quoted_price_action == "multiply")
            this.variation_product[i].quoted_price = (this.variation_product[i].quoted_price * this.bulk_quoted_price);

          this.variation_product[i].quoted_price_change = new Date().toISOString().slice(0, 10);
        }
      }

      if(this.price_ntd_last_change_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          this.variation_product[i].price_ntd_change = this.bulk_price_ntd_last_change;
        }
      }

      if(this.price_last_change_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          this.variation_product[i].price_change = this.bulk_price_last_change;
        }
      }

      if(this.quoted_price_last_change_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          this.variation_product[i].quoted_price_change = this.bulk_quoted_price_last_change;
        }
      }

      if(this.image_checked == true) {
        let file = document.getElementById('bulk_image').files[0];
        if(typeof file !== 'undefined') 
        {
          for (let i=0; i<this.variation_product.length; i++) {
            if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
              continue;
            this.variation_product[i].url = this.bulk_url;
            document.getElementById('variation_' + this.variation_product[i].id).files[0] = file;
          }
        }
      }

      if(this.status_checked == true) {
        for (let i=0; i<this.variation_product.length; i++) {
          if(this.variation_product[i].checked === "" || this.variation_product[i].checked === 0 || this.variation_product[i].checked == false)
            continue;
          this.variation_product[i].status = this.bulk_status;
        }
      }

      window.jQuery(".mask").toggle();
      window.jQuery("#modal_bulk_apply").toggle();

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

    is_code_existed: async function(code) {
      let ret = await axios.get("api/product_code_existed_check", { params: { code: code, id: 0 } });

      return ret.data;
    },

    is_code_existed_in_product_set: async function(code) {
      let ret = await axios.get("api/product_code_existed_in_product_set_check", { params: { code: code, id: 0 } });

      return ret.data;
    },


    save: async function() {
      let _this = this;
      let reason = this.check_input();
      if (reason !== "") {
        Swal.fire({
          text: reason,
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      let replacement = [];
      let replacement_product = $('#replacement_product').val();
      
      if(replacement_product != undefined)
      {
        replacement = replacement_product.split(",");
        replacement = replacement.filter(function (el) {
          return el != "";
        });
      }

      let err = '';
      let replacement_data = [];
      this.replacement_id = [];

      for (let index = 0; index < replacement.length; ++index) {
        const element = replacement[index];

        replacement_data = await this.is_code_existed(element.trim());
        if(replacement_data.length > 0)
          this.replacement_json.push({code: element.trim(), id: replacement_data[0].id});
        else
          err = err + element.trim() + '<br> ';
      }

      if(err.trim() != '')
      {
        Swal.fire({
          html: "The code of replacement product doesn’t exist.<br>" + err.trim(),
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      if(this.sub_category == '10020000')
      {
        if(this.p1_code.trim() == '' || this.p2_code.trim() == '')
        {
          Swal.fire({
            text: "You have to input the code and qty for Product 1 and Product 2. Qty should be greater than 0.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }
  
        // p1_qty and p2_qty string to number > 0
        this.p1_qty = Number(this.p1_qty);
        this.p2_qty = Number(this.p2_qty);
        
        if(this.p1_qty <= 0 || this.p2_qty <= 0)
        {
          Swal.fire({
            text: "You have to input the code and qty for Product 1 and Product 2. Qty should be greater than 0.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }

        this.p3_qty = Number(this.p3_qty);

        if(this.p3_code.trim() != '' && this.p3_qty <= 0)
        {
          Swal.fire({
            text: "Qty for Product 3 should be greater than 0.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }

        
  
        let p1_data = [];
        let p2_data = [];
        let p3_data = [];
  
        this.p1_id = "";
        this.p2_id = "";
        this.p3_id = "";
  
        let error_msg = '';
        let err_product_set = '';
  
        if(this.p1_code.trim() != '')
        {
          p1_data = await this.is_code_existed(this.p1_code.trim());
          if(p1_data.length > 0)
            this.p1_id = p1_data[0].id;
          else
            error_msg = error_msg + 'Product 1, ';

          p1_data = await this.is_code_existed_in_product_set(this.p1_code.trim());
          if(p1_data.length > 0)
            err_product_set = err_product_set + 'Product 1, ';
        }
  
        if(this.p2_code.trim() != '')
        {
          p2_data = await this.is_code_existed(this.p2_code.trim());
          if(p2_data.length > 0)
            this.p2_id = p2_data[0].id;
          else
            error_msg = error_msg + 'Product 2, ';

          p2_data = await this.is_code_existed_in_product_set(this.p2_code.trim());
          if(p2_data.length > 0)
            err_product_set = err_product_set + 'Product 2, ';
        }
  
        if(this.p3_code.trim() != '')
        {
          p3_data = await this.is_code_existed(this.p3_code.trim());
          if(p3_data.length > 0)
            this.p3_id = p3_data[0].id;
          else
            error_msg = error_msg + 'Product 3, ';

          p3_data = await this.is_code_existed_in_product_set(this.p3_code.trim());
          if(p3_data.length > 0)
            err_product_set = err_product_set + 'Product 3, ';

        }

        // trim the last comma
        error_msg = error_msg.replace(/,\s*$/, "");
        err_product_set = err_product_set.replace(/,\s*$/, "");
  
        if(error_msg != '')
        {
          Swal.fire({
            text: error_msg + ' that you input is not an existing product in the product database. Please check product code again!!',
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }

        if(err_product_set != '')
        {
          Swal.fire({
            text: 'User is not allowed to input any product belonging to "Product Set" sub category into Product 1/2/3. Please revise the code of ' + err_product_set + '.',
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }
      }

      
      if(this.sub_category == '10010000')
        {
  
          // check if file_ics file ext is .ics
          for (var i = 0; i < this.$refs.file_ics.files.length; i++)
            {
              let file = this.$refs.file_ics.files[i];
              if(file.name.split('.').pop() != 'ies')
              {
                Swal.fire({
                  text: "The extension of all selected files need to be “.ies”.",
                  icon: "warning",
                  confirmButtonText: "OK",
                });
                return;
              }
            }
    
            for (var i = 0; i < this.$refs.file_manual.files.length; i++)
              {
                let file = this.$refs.file_manual.files[i];
                if(file.name.split('.').pop() != 'zip' && file.name.split('.').pop() != 'rar' && 
                  file.name.split('.').pop() != '7z' && file.name.split('.').pop() != 'pdf' && 
                  file.name.split('.').pop() != 'doc' && file.name.split('.').pop() != 'docx' && 
                  file.name.split('.').pop() != 'xls' && file.name.split('.').pop() != 'xlsx' && 
                  file.name.split('.').pop() != 'ppt' && file.name.split('.').pop() != 'pptx' && 
                  file.name.split('.').pop() != 'jpg' && file.name.split('.').pop() != 'jpeg' && 
                  file.name.split('.').pop() != 'png' && file.name.split('.').pop() != 'gif' && 
                  file.name.split('.').pop() != 'bmp' && file.name.split('.').pop() != 'tiff' && 
                  file.name.split('.').pop() != 'svg')
                {
                  Swal.fire({
                    text: "Each selected file needs to be picture, Microsoft office document, pdf or compressed file.",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
                  return;
                }
              }
    
            // check if file size < 10MB
            for (var i = 0; i < this.$refs.file_ics.files.length; i++)
            {
              let file = this.$refs.file_ics.files[i];
              if(file.size > 10 * 1024 * 1024)
              {
                Swal.fire({
                  text: "The size of each selected file needs to be lower than “10MB”.",
                  icon: "warning",
                  confirmButtonText: "OK",
                });
                return;
              }
            }
    
            for (var i = 0; i < this.$refs.file_manual.files.length; i++)
              {
                let file = this.$refs.file_manual.files[i];
                if(file.size > 10 * 1024 * 1024)
                {
                  Swal.fire({
                    text: "The size of each selected file needs to be lower than “10MB”.",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
                  return;
                }
              }
        }

        if(this.sub_category == '20000000')
          {
    
            // check if file_skp file ext is .ics
            for (var i = 0; i < this.$refs.file_skp.files.length; i++)
              {
                let file = this.$refs.file_skp.files[i];
                if(file.name.split('.').pop().toLowerCase() != 'skp')
                {
                  Swal.fire({
                    text: "The extension of all selected files need to be “.skp.",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
                  return;
                }
              }
      
              for (var i = 0; i < this.$refs.file_manual.files.length; i++)
                {
                  let file = this.$refs.file_manual.files[i];
                  if(file.name.split('.').pop() != 'zip' && file.name.split('.').pop() != 'rar' && 
                    file.name.split('.').pop() != '7z' && file.name.split('.').pop() != 'pdf' && 
                    file.name.split('.').pop() != 'doc' && file.name.split('.').pop() != 'docx' && 
                    file.name.split('.').pop() != 'xls' && file.name.split('.').pop() != 'xlsx' && 
                    file.name.split('.').pop() != 'ppt' && file.name.split('.').pop() != 'pptx' && 
                    file.name.split('.').pop() != 'jpg' && file.name.split('.').pop() != 'jpeg' && 
                    file.name.split('.').pop() != 'png' && file.name.split('.').pop() != 'gif' && 
                    file.name.split('.').pop() != 'bmp' && file.name.split('.').pop() != 'tiff' && 
                    file.name.split('.').pop() != 'svg')
                  {
                    Swal.fire({
                      text: "Each selected file needs to be picture, Microsoft office document, pdf or compressed file.",
                      icon: "warning",
                      confirmButtonText: "OK",
                    });
                    return;
                  }
                }
      
              // check if file size < 10MB
              for (var i = 0; i < this.$refs.file_skp.files.length; i++)
              {
                let file = this.$refs.file_skp.files[i];
                if(file.size > 10 * 1024 * 1024)
                {
                  Swal.fire({
                    text: "The size of each selected file needs to be lower than “10MB”.",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
                  return;
                }
              }
      
              for (var i = 0; i < this.$refs.file_manual.files.length; i++)
                {
                  let file = this.$refs.file_manual.files[i];
                  if(file.size > 10 * 1024 * 1024)
                  {
                    Swal.fire({
                      text: "The size of each selected file needs to be lower than “10MB”.",
                      icon: "warning",
                      confirmButtonText: "OK",
                    });
                    return;
                  }
                }
          }


      let show_confirm = true;

      if(this.code.trim() != '')
      {
        let ret = await axios.get("api/product_code_check", { params: { code: this.code.trim(), id: 0 } });

        if(ret.data.length > 0)
        {
          show_confirm = false;
          // sweet alert whith yes no and html product code list
          let html = '<div class="text-left">This code already existed in the product database. Please check the below link first before you save the current product info into the product database.';
          for(let i=0; i<ret.data.length; i++)
          {
            html += '<div><a href="product_display_code?id=' + ret.data[i].id + '" target="_blank">' + ret.data[i].code + '</a></div>';
          }
          html += '</div>';

          const alert =  await Swal.fire({
            title: "Warning",
            html: html,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Continue Saving",
            cancelButtonText: "Cancel Saving",
          });

          if(!(alert.value && alert.value == true))
            return;
          else
          {
            _this.save_confirm();

          }
        }
      }
      

      if(!show_confirm)  return;

      Swal.fire({
        title: "Submit",
        text: "Are you sure to save?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          _this.save_confirm();
        } else {
          return;
        }
      });

    },

    save_confirm: async function() {
      let _this = this;
      if (_this.submit == true) return;

          _this.submit = true;

          var token = localStorage.getItem("token");
          var form_Data = new FormData();

          let attributes = [];
          if(this.sub_category != '10020000')
          {
            // special_infomation -> attributes
            for (var i = 0; i < this.special_infomation.length; i++) {
              let category = this.special_infomation[i].category;
              let cat_id = this.special_infomation[i].cat_id;
              let value = this.$refs[cat_id][0].value
              
              var obj = {
                category: category,
                cat_id: cat_id,
                value: value
              };
              attributes.push(obj);
            }
          }

          let accessory = [];

            for(var i = 0; i < this.accessory_infomation.length; i++) {
              let category = this.accessory_infomation[i].category;
              let cat_id = this.accessory_infomation[i].cat_id;
              let detail = this.accessory_infomation[i].detail[0];
              
              let item = [];
              for(var j = 0; j < detail.length; j++) {
                let id = detail[j].id;
                let code = detail[j].code;
                let name = detail[j].name;
                let price = detail[j].price;
                let price_ntd = detail[j].price_ntd;
                let url = detail[j].url;
                
                let file = document.getElementById('accessory_' + cat_id + '_' + id).files[0];
                if(typeof file !== 'undefined') 
                  form_Data.append('accessory_' + cat_id + '_' + id, file);
  
                var obj = {
                  id: id,
                  code: code,
                  name: name,
                  price: price,
                  price_ntd: price_ntd,
                  url: url,
                };
                item.push(obj);
              }
  
              var obj = {
                category: category,
                cat_id: cat_id,
                detail: item,
              };
  
              accessory.push(obj);
  
            }
          

          let variation = [];
          // variation
          for(var i = 0; i < this.variation_product.length; i++) {
            let id = this.variation_product[i].id;
            let checked = this.variation_product[i].checked;
            let code = this.variation_product[i].code;
            let k1 = this.variation_product[i].k1;
            let k2 = this.variation_product[i].k2;
            let k3 = this.variation_product[i].k3;
            let k4 = this.variation_product[i].k4;
            let v1 = this.variation_product[i].v1;
            let v2 = this.variation_product[i].v2;
            let v3 = this.variation_product[i].v3;
            let v4 = this.variation_product[i].v4;
            let price = this.variation_product[i].price;
            let quoted_price = this.variation_product[i].quoted_price;
            let price_ntd = this.variation_product[i].price_ntd;
       
            let price_change = this.variation_product[i].price_change;
            let quoted_price_change = this.variation_product[i].quoted_price_change;
            let price_ntd_change = this.variation_product[i].price_ntd_change;
            let status = this.variation_product[i].status;

            if((price * 1.15 > quoted_price) && _this.category == '10000000')
            {
              quoted_price = (price * 1.15).toFixed(2);
              quoted_price_change = new Date().toISOString().slice(0, 10);
            }

            let file = document.getElementById('variation_' + id).files[0];
              if(typeof file !== 'undefined') 
                form_Data.append('variation_' + id, file);
              else{
                if(this.image_checked == true) {
                  let file = document.getElementById('bulk_image').files[0];
                  if(typeof file !== 'undefined' && this.variation_product[i].url !== '') 
                  {
                    form_Data.append('variation_' + id, file);
                  }
                }
              }

            var obj = {
              id: id,
              checked: checked,
              code: code,
              k1: k1,
              k2: k2,
              k3: k3,
              k4: k4,
              v1: v1,
              v2: v2,
              v3: v3,
              v4: v4,
              price: price,
              quoted_price: quoted_price,
              price_ntd: price_ntd,
              price_change: price_change,
              quoted_price_change: quoted_price_change,
              price_ntd_change: price_ntd_change,
              status: status,
            };

            variation.push(obj);
            
          }

          if((_this.price * 1.15 > _this.quoted_price) && _this.category == '10000000')
          {
            _this.quoted_price = (_this.price * 1.15).toFixed(2);
            _this.quoted_price_change = new Date().toISOString().slice(0, 10);
          }
          
          form_Data.append("jwt", token);
          form_Data.append("category", _this.category);
          form_Data.append("sub_category", _this.sub_category);
          form_Data.append("brand", _this.brand.toUpperCase().trim());

          if(this.sub_category == '10020000')
          {
            let tag0102 = $('#tag0102').val();
            form_Data.append("tags", tag0102.join());
          }
          else
          {
            let tag01 = $('#tag01').val();
            form_Data.append("tags", tag01.join());
          }

          form_Data.append("code", _this.code);
          form_Data.append("price_ntd", _this.price_ntd);
          form_Data.append("price", _this.price);
          form_Data.append("quoted_price", _this.quoted_price);
          form_Data.append("price_ntd_change", _this.price_ntd_change);
          form_Data.append("price_change", _this.price_change);
          form_Data.append("quoted_price_change", _this.quoted_price_change);
          form_Data.append("moq", _this.moq);
          form_Data.append("description", _this.description);

          form_Data.append("out", _this.out);
          form_Data.append("currency", _this.currency);

          let related_product = $('#related_product').val();
          form_Data.append("related_product", related_product);

          let replacement_product = $('#replacement_product').val();
          form_Data.append("replacement_product", replacement_product);
          form_Data.append("replacement_json", JSON.stringify(_this.replacement_json));

          form_Data.append("notes", _this.notes);

          form_Data.append("accessory_mode", _this.accessory_mode === true || _this.accessory_mode === "1" ? 1 : 0);
          form_Data.append("variation_mode", _this.variation_mode === true || _this.variation_mode === "1" ? 1 : 0);

          form_Data.append("attributes", JSON.stringify(attributes));
          form_Data.append("accessory", JSON.stringify(accessory));
          form_Data.append("variation", JSON.stringify(variation));

          form_Data.append("p1_code", _this.p1_code);
          form_Data.append("p1_qty", _this.p1_qty);
          form_Data.append("p1_id", _this.p1_id);

          form_Data.append("p2_code", _this.p2_code);
          form_Data.append("p2_qty", _this.p2_qty);
          form_Data.append("p2_id", _this.p2_id);

          form_Data.append("p3_code", _this.p3_code);
          form_Data.append("p3_qty", _this.p3_qty);
          form_Data.append("p3_id", _this.p3_id);

          form_Data.append("brand_handler", _this.brand_handler);

          for (var i = 1; i < 4; i++) {
            let file = document.getElementById('photo' + i).files[0];
            if(typeof file !== 'undefined') 
              form_Data.append('photo' + i, file);
          }

          // ics
          if(this.$refs.file_ics != undefined)
          {
            for (var i = 0; i < this.$refs.file_ics.files.length; i++) {
              let file = this.$refs.file_ics.files[i];
              form_Data.append("file_ics[" + i + "]", file);
            }
          }

          // skp
          if(this.$refs.file_skp != undefined)
            {
              for (var i = 0; i < this.$refs.file_skp.files.length; i++) {
                let file = this.$refs.file_skp.files[i];
                form_Data.append("file_skp[" + i + "]", file);
              }
            }

          // manual
          if(this.$refs.file_manual != undefined)
          {
            for (var i = 0; i < this.$refs.file_manual.files.length; i++) {
              let file = this.$refs.file_manual.files[i];
              form_Data.append("file_manual[" + i + "]", file);
            }
          }
          

          //for (var i = 0; i < this.$refs.file.files.length; i++) {
          //  let file = this.$refs.file.files[i];
          //   form_Data.append("files[" + i + "]", file);
          // }

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/add_product_insert_code",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.reset();

            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.submit = false;

            });
    },

    reset: function() {
      this.category = "";
      this.sub_category = "";
      this.sub_cateory_item = [];
      this.special_infomation = [];
      this.special_infomation_detail = [];
      this.clear_accessory();
      this.sub_accessory_item = [];

      this.edit_mode = false;
      this.accessory_mode = false;
      this.variation_mode = false;

      // data for
      this.code = "";
      this.brand = "";

      this.currency = "NTD";

      $('#tag01').val('default');
      $('#tag01').selectpicker('refresh');

      $('#tag0102').val('default');
      $('#tag0102').selectpicker('refresh');

     // $('#tag02').val('default');
     // $('#tag02').selectpicker('refresh');
      
      this.price = "";
      this.quoted_price = "";
      this.price_ntd = "";
      this.price_change = "";
      this.quoted_price_change = "";
      this.price_ntd_change = "";
      this.moq = "";
      this.description = "";
      this.out = "";
      this.notes = "";
      this.url1 = null;
      this.url2 = null;
      this.url3 = null;

      // variation_product
      this.variation_product = [];
      this.variation1_text = '';
      this.variation2_text = '';
      this.variation3_text = '';
      this.variation4_text = '';

      this.variation4 = '';
      this.variation3 = '';
      this.variation2 = '';
      this.variation1 = '';

      this.variation1_value = '';
      $('#variation1_value').tagsinput('removeAll');
      this.variation2_value = '';
      $('#variation2_value').tagsinput('removeAll');
      this.variation3_value = '';
      $('#variation3_value').tagsinput('removeAll');
      this.variation4_value = '';
      $('#variation4_value').tagsinput('removeAll');

      $('#related_product').tagsinput('removeAll');
      $('#replacement_product').tagsinput('removeAll');

      this.variation1_custom = '';
      this.variation2_custom = '';
      this.variation3_custom = '';
      this.variation4_custom = '';

      this.submit = false;

      $("#variation_mode").bootstrapToggle("off");
      $("#accessory_mode").bootstrapToggle("off");

      this.code_checked = '';
      this.bulk_code = '';
      this.price_ntd_checked = '';
      this.quoted_price_checked = '';
      this.bulk_price_ntd = '';
      this.price_ntd_action = '';
      this.price_checked = '';
      this.bulk_price = '';
      this.price_action = '';
      this.image_checked = '';
      this.bulk_url = '';
      this.bulk_status = '';
      this.status_checked = '';

      this.price_ntd_last_change_checked = '';
      this.bulk_price_ntd_last_change = '';
  
      this.price_last_change_checked = '';
      this.bulk_price_last_change = '';
  
      this.quoted_price_last_change_checked = '';
      this.bulk_quoted_price_last_change = '';

      document.getElementById('select_all_product').checked = false;
      document.getElementById('bulk_select_all_product').checked = false;

      var f1 = document.getElementById('photo1');
      if(f1) f1.value = "";
      var f2 = document.getElementById('photo2');
      if(f2) f2.value = "";
      var f3 = document.getElementById('photo3');
      if(f3) f3.value = "";

      var f_ics = this.$refs.file_ics;
      if(f_ics) f_ics.value = "";
      var f_skp = this.$refs.file_skp;
      if(f_skp) f_skp.value = "";
      var f_manual = this.$refs.file_manual;
      if(f_manual) f_manual.value = "";

      this.p1_code = '';
      this.p1_qty = '';
      this.p1_id = '';

      this.p2_code = '';
      this.p2_qty = '';
      this.p2_id = '';

      this.p3_code = '';
      this.p3_qty = '';
      this.p3_id = '';

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
      this.quoted_price_checked = toogle;
      this.image_checked = toogle;
      this.status_checked = toogle;
      this.price_ntd_last_change_checked = toogle;
      this.price_last_change_checked = toogle;
      this.quoted_price_last_change_checked = toogle;
      
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
