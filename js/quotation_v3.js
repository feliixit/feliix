var app = new Vue({
    el: "#app",
    data: {
      submit: false,

      // id of the quotation
      l_id:0,
      id:0,

      //img_url: 'https://storage.googleapis.com/feliiximg/',

      img_url: 'https://storage.googleapis.com/feliiximg/',
       
      // menu
      show_header: false,
      show_footer: false,
      show_page: false,
      show_subtotal: false,
      show_total: false,
      show_term: false,
      show_payment_term: false,
      show_signature: false,

      // header
      first_line : '',
      second_line : '',
      project_category : '',
      quotation_no : '',
      quotation_date : '',

      prepare_for_first_line : '', 
      prepare_for_second_line : '',
      prepare_for_third_line : '',

      prepare_by_first_line : '',
      prepare_by_second_line : '',

      // _header
      temp_first_line : '',
      temp_second_line : '',
      temp_project_category : '',
      temp_quotation_no : '',
      temp_quotation_date : '',

      temp_prepare_for_first_line : '', 
      temp_prepare_for_second_line : '',
      temp_prepare_for_third_line : '',

      temp_prepare_by_first_line : '',
      temp_prepare_by_second_line : '',

      // footer
      footer_first_line : '',
      footer_second_line : '',

      // _footer
      temp_footer_first_line : '',
      temp_footer_second_line : '',

      // page
      pages : [],
      temp_pages : [],

      

      // block_names
      block_names : [],
      block_value : [],

      // blocks
      blocks : [],

      edit_type_a : false,
      edit_type_b : false,

      temp_block_a : [],
      temp_block_b : [],

      edit_type_a_image : false,
      edit_type_a_noimage : false,

      edit_type_b_noimage : false,

  
      position: [],
      title: [],
      department: "",
      title_id: 0,
  
      // data
     
      editing: false,
  
  
      receive_records: [],

      product_records: [],
      product : {},
  
      title_info: {},
      template: {},
      library: {},
  
      view_detail: false,
      record: {},
  
         // evaluate
         evals:{},
         avg:10.0,
         avg1:10.0,
         avg2:0.0,
     
      total: {
        id:0,
        page: 0,
        discount:'0',
        vat : '',
        show_vat : '',
        valid : '',
        total : '',
        real_total : '',
        back_total : '',
      },

      
      term:
      {
        page: 0,
        item: [],
      },

      payment_term:
      {
        page: 0,
        payment_method: '',
        brief : '',
        item: { 
          bank_name : '',
          first_line : '',
          second_line : '',
          third_line : '',
        },
      },


      sig:
      {
        page: 0,
        item_client: [],
        item_company: [],
      },

      subtotal:0,
      subtotal_novat:0,

      subtotal_novat_a:0,
      subtotal_novat_b:0,

      subtotal_info_not_show_a:0,
      subtotal_info_not_show_b:0,

      show_title : true,

      // version II new parameters

      

        // paging
        product_page: 1,
    pg:0,
    //perPage: 10,
    product_pages: [],

    product_pages_10: [],

    product_total:0,

        inventory: [
        {name: '10', id: 10},
        {name: '25', id: 25},
        {name: '50', id: 50},
        {name: '100', id: 100},
        {name: 'All', id: 10000}
        ],

        perPage: 10,

        url: '',
        url1: '',
        url2: '',
        url3: '',
        code: '',
        brand: '',
        category: '',
        sub_category_name: '',

        tags:[],
        price: '',
        quoted_price: '',
        variation1_value:[],
        variation2_value: [],
        variation3_value: [],
    
        variation_product: [],

        v1:"",
        v2:"",
        v3:"",

        accessory_infomation: [],

        related_product: [],
        specification: [],
        description: "",

        // vat for each product
        product_vat : '',

        // info
        name :"",
        title: "",
        is_manager: "",

        url : "",

        brands: [],
        fil_brand: "",
        fil_code: "",
        fil_tag : [],
        fil_id: "",

        special_infomation: [],
        special_infomation_detail: [],
        attributes:[],

        toggle_type:'',

        groupedItems : [],
    },
  
    created() {
      let _this = this;
      let uri = window.location.href.split("?");
      if (uri.length >= 2) {
        let vars = uri[1].split("&");

        let tmp = "";
        vars.forEach(async function(v) {
          tmp = v.split("=");
          if (tmp.length == 2) {
            switch (tmp[0]) {
              case "id":
                _this.id = tmp[1];

                if(_this.id != 0)
                 _this.l_id = _this.id;

                break;
              default:
                console.log(`Too many args`);
            }
          }
        });
      }
      
      this.getRecord();
      this.get_product_records();
      this.getUserName();
      this.get_brands();
    },
  
    computed: {
        displayedPosts() {
            //if(this.pg == 0)
            //  this.filter_apply_new();
      
            this.setPages();
            return this.paginate(this.product_records);
          },

          show_ntd : function() {
  
            return false;
          }
    },
  
    mounted() {
      
    },
  
    watch: {

      
  
      show_header() {
        if(this.show_header) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_footer() {
        if(this.show_footer) {
          this.show_header = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_page() {
        if(this.show_page) {
          this.show_footer = false;
          this.show_header = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_subtotal() {
        if(this.show_subtotal) {
          this.show_footer = false;
          this.show_page = false;
          this.show_header = false;
          this.show_total = false;
          this.show_term = false;
          this.show_signature = false;
        }
      },

      show_total() {
        if(this.show_total) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_header = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_term() {
        if(this.show_term) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_header = false;
          this.show_payment_term = false;
          this.show_signature = false;
        }
      },

      show_payment_term() {
        if(this.show_payment_term) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_header = false;
          this.show_term = false;
          this.show_signature = false;
        }
      },

      show_signature() {
        if(this.show_signature) {
          this.show_footer = false;
          this.show_page = false;
          this.show_subtotal = false;
          this.show_total = false;
          this.show_term = false;
          this.show_payment_term = false;
          this.show_header = false;
        }
      },
      
      department() {
        this.title = this.shallowCopy(
          this.position.find((element) => element.did == this.department)
        ).items;
  
      },
  
    },
  
    methods: {
      add_with_image(all) {

        var photo = "";
        var price = "";
        var list = "";

        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3)
        )

        if(this.product.product.length > 0 && item_product.id == undefined && all != 'all') {
          alert('Please choose an option for each attribute');
          return;
        }

        if(item_product.id != undefined)
        {
          if(item_product.photo != "")
            photo = item_product.photo;
            // price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
            price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
            if(this.v1 != "")
              list += (item_product.k1 + ': ' + item_product.v1) + "\n";
            if(this.v2 != "")
              list += (item_product.k2 + ': ' + item_product.v2) + "\n";
            if(this.v3 != "")
              list += (item_product.k3 + ': ' + item_product.v3) + "\n";
        }
        else
        {
          photo = this.product.photo1;
          // price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
          price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
          list = "";
        }

        if(all == 'all')
        {
          list = "";
          var k1, k2, k3;
          k1 = this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1;
          k2 = this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2;
          k3 = this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3;

          if(k1 !== '')
            list += this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1 + ': ' + this.product.variation1_value.join(', ') + "\n";
          if(k2 !== '')
            list += this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2 + ': ' + this.product.variation2_value.join(', ') + "\n";
          if(k3 !== '')
            list += this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3 + ': ' + this.product.variation3_value.join(', ') + "\n";

          photo = this.product.photo1;
          if(this.product.srp !== null || this.product.srp_quoted !== null)
            price = this.product.srp_quoted !== null ? this.product.srp_quoted : this.product.srp;
            //price = this.product.srp !== null ? this.product.srp : this.product.srp_quoted;

          if(price == null)
            //price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
            price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
        }

        for(var i=0; i<this.specification.length; i++)
        {
            if(this.specification[i].k1 !== '')
              list += this.specification[i].k1 + ': ' + this.specification[i].v1 + "\n";
            if(this.specification[i].k2 !== '')
              list += this.specification[i].k2 + ': ' + this.specification[i].v2 + "\n";
        }

        if(price == null)
          price = this.product.srp_quoted !== 0 ?  this.product.srp_quoted : this.product.srp;
          // price = this.product.srp !== 0 ?  this.product.srp : this.product.srp_quoted;

        var block_a_image = 'image';
        var sn = 0;
        if(this.toggle_type == 'A')
          var items = this.temp_block_a;

        if(this.toggle_type == 'B')
          var items = this.temp_block_b;

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        list.replace(/\n+$/, "");
        sn = sn + 1;

        if(this.toggle_type == 'A')
        {
          item = {
            id: sn,
            url: photo !== '' ? this.img_url + photo  : '',
            file: {
              name: "",
            },
            type : block_a_image,
            code: this.product.code,
            photo: photo,
            qty: "",
            price: price,
            discount: "0",
            amount: "",
            desc: "",
            list: list,
            num:"",
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
          };
        }

        if(this.toggle_type == 'B')
        {
          item = {
            id: sn,
            
            code: "",
            photo: photo,
            qty: "1",
            price: price,

            discount: "0",
            amount: "",
            desc: "",
            list: list,
            num:"",
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
          };
        }

        items.push(item);

        alert('Add Successfully');
      },

      add_without_image(all) {

        var photo = "";
        var price = "";
        var list = "";

        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3)
        )

        if(this.product.product.length > 0 && item_product.id == undefined && all != 'all') {
          alert('Please choose an option for each attribute');
          return;
        }

        if(item_product.id != undefined)
        {
          if(item_product.photo != "")
            photo = item_product.photo;
            //price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
            price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
            if(this.v1 != "")
              list += (item_product.k1 + ': ' + item_product.v1) + "\n";
            if(this.v2 != "")
              list += (item_product.k2 + ': ' + item_product.v2) + "\n";
            if(this.v3 != "")
              list += (item_product.k3 + ': ' + item_product.v3) + "\n";
        }
        else
        {
          photo = this.product.photo1;
          //price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
          price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
          list = "";
        }

        if(all == 'all')
        {
          list = "";
          var k1, k2, k3;
          k1 = this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1;
          k2 = this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2;
          k3 = this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3;

          if(k1 !== '')
            list += this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1 + ': ' + this.product.variation1_value.join(', ') + "\n";
          if(k2 !== '')
            list += this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2 + ': ' + this.product.variation2_value.join(', ') + "\n";
          if(k3 !== '')
            list += this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3 + ': ' + this.product.variation3_value.join(', ') + "\n";

          photo = this.product.photo1;

          if(this.product.srp !== null || this.product.srp_quoted !== null)
            price = this.product.srp_quoted !== null ? this.product.srp_quoted : this.product.srp;
            //price = this.product.srp !== null ? this.product.srp : this.product.srp_quoted;

          if(price == null)
            //price = this.product.price_org !== null ? this.product.price_org : this.product.quoted_price_org;
            price = this.product.quoted_price_org !== null ? this.product.quoted_price_org : this.product.price_org;
        }

        if(price == null)
          price = this.product.srp_quoted !== 0 ?  this.product.srp_quoted : this.product.srp;
          //price = this.product.srp !== 0 ?  this.product.srp : this.product.srp_quoted;

        for(var i=0; i<this.specification.length; i++)
        {
            if(this.specification[i].k1 !== '')
              list += this.specification[i].k1 + ': ' + this.specification[i].v1 + "\n";
            if(this.specification[i].k2 !== '')
              list += this.specification[i].k2 + ': ' + this.specification[i].v2 + "\n";
        }

        list.replace(/\n+$/, "");

        var block_a_image = 'noimage';
        var sn = 0;
        if(this.toggle_type == 'A')
          var items = this.temp_block_a;

        if(this.toggle_type == 'B')
          var items = this.temp_block_b;

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        sn = sn + 1;

        if(this.toggle_type == 'A')
        {
          item = {
            id: sn,
            url: "",
            file: {
              name: "",
            },
            type : block_a_image,
            code: this.product.code,
            photo: "",
            qty: "",
            price: price,
            discount: "0",
            amount: "",
            desc: "",
            list: list,
            num:"",
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
          };
        }

        if(this.toggle_type == 'B')
        {
          item = {
            id: sn,
            
            code: this.product.code,
            photo: "",
            qty: "1",
            price: price,

            discount: "0",
            amount: "",
            desc: "",
            list: list,
            num:"",
            pid: this.product.id,
            v1: all == 'all' ? '' : this.v1,
            v2: all == 'all' ? '' : this.v2,
            v3: all == 'all' ? '' : this.v3,
          };
        }

        items.push(item);
        alert('Add Successfully');
      },
      
      set_up_specification() {
        let k1 = '';
        let k2 = '';
 
        let v1 = '';
        let v2 = '';

        this.specification = [];
 
       for(var i=0; i < this.special_infomation.length; i++)
       {
         if(this.special_infomation[i].value != "")
         {
           if(k1 == "")
           {
             k1 = this.special_infomation[i].category;
             v1 = this.special_infomation[i].value;
           }else if(k1 !== "" && k2 == "")
           {
             k2 = this.special_infomation[i].category;
             v2 = this.special_infomation[i].value;
 
             obj = {k1: k1, v1: v1, k2: k2, v2: v2};
             this.specification.push(obj);
             k1  = '';
             k2  = '';
             v1  = '';
             v2  = '';
           }
         }
       }
 
       if(k1 == "" && this.product.moq !== "")
       {
         k1 = 'MOQ';
         v1 = this.product.moq;
       }else if(k1 !== "" && k2 == "" && this.product.moq !== "")
       {
         k2 = 'MOQ';
         v2 = this.product.moq;
     
         obj = {k1: k1, v1: v1, k2: k2, v2: v2};
         this.specification.push(obj);
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
         this.specification.push(obj);
       
       }
     },

      get_brands: function() {
        let _this = this;
  
        const params = {
    
        };
  
        let token = localStorage.getItem("accessToken");
        axios
          .get("api/product_brands", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(function(response) {
            console.log(response.data);
            let res = response.data;
          
              _this.brands = response.data;
               
          })
          .catch(function(error) {
            console.log(error);
          });
      },

      change_url: function(url) {
        this.url = (url !== '' ? this.img_url + url : '');
      },

      close_single: function() {
        $('#modal_product_display').modal('toggle');
    },

    getSingleProduct : function(id) {


      let _this = this;


      const params = {
        d: id,
        c: '',
        t: '',
        b: '',
        of1: '',
        ofd1: '',
        of2: '',
        ofd2: '',
        page: 1,
        size: 10,
      };
  
      let token = localStorage.getItem("accessToken");
  
      axios
        .get("api/product_calatog", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          let res = response.data;
          if(res.length > 0) 
          {
            _this.product = response.data[0];
            _this.url = _this.product.photo1 !== '' ? _this.img_url + _this.product.photo1 : '';

            _this.special_infomation = _this.product.special_information[0].lv3[0];
            _this.attributes = _this.product.attribute_list;
    
            _this.related_product  = _this.product.related_product;

            _this.quoted_price = _this.product.quoted_price;
            _this.price = _this.product.price;

            _this.v1 = "";
            _this.v2 = "";
            _this.v3 = "";
    
            _this.chunk(_this.related_product, 4);
    
            _this.set_up_variants();
            _this.set_up_specification();
          }

    
        })
        .catch(function(error) {
          console.log(error);
        });

    },

      btnEditClick: function(product) {
        $('#modal_product_display').modal('toggle');
        this.product = product;
        this.url = (this.product.photo1 !== '' && this.product.photo1 !== undefined) ? this.img_url + this.product.photo1 : '';

        this.special_infomation = product.special_information[0].lv3[0];
        this.attributes = product.attribute_list;

        this.related_product  = product.related_product;

        this.quoted_price = product.quoted_price;
        this.price = product.price;

        this.v1 = "";
        this.v2 = "";
        this.v3 = "";

        this.chunk(this.related_product, 4);

        this.set_up_variants();
        this.set_up_specification();
      },

      chunk: function(arr, size) {
        var newArr = [];
        for (var i=0; i<arr.length; i+=size) {
          newArr.push(arr.slice(i, i+size));
        }
        this.groupedItems  = newArr;
      },

      set_up_variants() {
        for(var i=0; i<this.product.variation1_value.length; i++)
        {
          $('#variation1_value').tagsinput('add', this.product.variation1_value[i]);
        }
        for(var i=0; i<this.product.variation2_value.length; i++)
        {
          $('#variation2_value').tagsinput('add', this.product.variation2_value[i]);
        }
        for(var i=0; i<this.product.variation3_value.length; i++)
        {
          $('#variation3_value').tagsinput('add', this.product.variation3_value[i]);
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
      
    get_product_records: function() {
      let _this = this;

    const params = {
      d: '',
      c: '',
      t: '',
      b: '',
      of1: '',
      ofd1: '',
      of2: '',
      ofd2: '',
      page: 1,
      size: 10,
    };

    let token = localStorage.getItem("accessToken");

    this.product_total = 0;

    axios
      .get("api/product_calatog", {
        params,
        headers: { Authorization: `Bearer ${token}` },
      })
      .then(function(response) {
        console.log(response.data);
        let res = response.data;
        if(res.length > 0) 
        {
          _this.product_records = response.data;
          _this.product_total = _this.product_records[0].cnt;
        }

        if(_this.pg !== 0)
        { 
          _this.product_page = _this.pg;
          _this.setPages();
        }
        
  
      })
      .catch(function(error) {
        console.log(error);
      });
  },
        
    filter_apply_new: function() {
      let _this = this;

      _this.pg = 1;

      const params = {
        d: _this.fil_id,
        c: _this.fil_code,
        t: JSON.stringify(_this.fil_tag),
        b: _this.fil_brand,
        of1: '',
        ofd1: '',
        of2: '',
        ofd2: '',
        page: _this.pg,
        size: 10,
      };
  
      let token = localStorage.getItem("accessToken");
  
      this.product_records = [];
      this.product_total = 0;
  
      axios
        .get("api/product_calatog", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          let res = response.data;
          if(res.length > 0) 
          {
            _this.product_records = response.data;
            _this.product_total = _this.product_records[0].cnt;
          }

          _this.pg = 1;
  
          if(_this.pg !== 0)
          { 
            _this.product_page = _this.pg;
            _this.setPages();
          }
          
    
        })
        .catch(function(error) {
          console.log(error);
        });
  
  
      },

      change_total_amount(row) {
        
        row.discount = Math.floor(row.discount);
        if(row.discount > 100)
          row.discount = 100;

        var total = 0;

        total = ((this.subtotal_info_not_show_a * 1 + this.subtotal_info_not_show_b * 1) * (1 - row.discount * 0.01)).toFixed(2);
        
        if(row.vat == 'Y')
        {
          total = ((this.subtotal_novat_a * 1 + this.subtotal_info_not_show_b * 1) * (1 - row.discount * 0.01)).toFixed(2);
          total =  (total * 1) + (this.subtotal_novat_a * (1 - row.discount * 0.01) * 0.12);
        }

        if(row.vat == 'P')
        {
          total = ((this.subtotal_novat_a * 1 + this.subtotal_info_not_show_b * 1) * (1 - row.discount * 0.01)).toFixed(2);
          total =  (total * 1) + (this.subtotal_novat_a * (1 - row.discount * 0.01) * 0.12);
        }

        total = Number(total).toFixed(2);
        //this.real_total = row.total;
        row.real_total = total;
      },

      print_page() {
        this.show_title = false;
        app.$forceUpdate();
        window.print();
      },

      clear_sig_photo(_id) {
        var item = this.sig.item_company.find(({ id }) => id === _id);
        item.url = "";
  
        document.getElementById('sig_image_'+_id).value = "";
      },
      
      onSigFileChangeImage(e, _id) {
        const file = e.target.files[0];

        var item = this.sig.item_company.find(({ id }) => id === _id);
    
        let url = URL.createObjectURL(file);
  
        item.url = url;
          
      },

      sig_item_company_del: function(fromIndex, eid) {

        var index = this.sig.item_company.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.sig.item_company.splice(index, 1);
        }
      },

      sig_item_company_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.sig.item_company.length - 1) 
          return;
  
        var element = this.sig.item_company.find(({ id }) => id === eid);
        this.sig.item_company.splice(fromIndex, 1);
        this.sig.item_company.splice(toIndex, 0, element);
      },

      sig_item_company_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.sig.item_company.find(({ id }) => id === eid);
        this.sig.item_company.splice(fromIndex, 1);
        this.sig.item_company.splice(toIndex, 0, element);
      },

      sig_item_client_del: function(fromIndex, eid) {

        var index = this.sig.item_client.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.sig.item_client.splice(index, 1);
        }
      },

      sig_item_client_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.sig.item_client.length - 1) 
          return;
  
        var element = this.sig.item_client.find(({ id }) => id === eid);
        this.sig.item_client.splice(fromIndex, 1);
        this.sig.item_client.splice(toIndex, 0, element);
      },

      sig_item_client_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.sig.item_client.find(({ id }) => id === eid);
        this.sig.item_client.splice(fromIndex, 1);
        this.sig.item_client.splice(toIndex, 0, element);
      },

      add_sig_client_item() {
        let order = 0;
        
        if(this.sig.item_client.length != 0)
          order = Math.max.apply(Math, this.sig.item_client.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "type" : 'C',
          "photo" : '',
          "url" : '',
          "name" : '',
          "position" : '',
          "phone" : '',
          "email" : '',
        }, 
  
        this.sig.item_client.push(obj);
      },

      add_sig_company_item() {
        let order = 0;
        
        if(this.sig.item_company.length != 0)
          order = Math.max.apply(Math, this.sig.item_company.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "type" : 'F',
          "photo" : '',
          "url" : '',
          "name" : '',
          "position" : '',
          "phone" : '',
          "email" : '',
        }, 
  
        this.sig.item_company.push(obj);
      },

      sig_save: async function() {
        if (this.submit == true) return;

        //if(this.sig.page == 0) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("detail", JSON.stringify(this.sig));

        for (var i = 0; i < this.sig.item_company.length; i++) {
          let file = document.getElementById('sig_image_' + this.sig.item_company[i].id);
          if(file) {
            let f = file.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('sig_image_' + this.sig.item_company[i].id, f);
          }
        }

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_sig_insert',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
        
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();
      
      
      },

      close_sig() {
        this.show_signature = false;

      },

      term_save: async function() {
        if (this.submit == true) return;

        //if(this.term.page == 0) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("detail", JSON.stringify(this.term));

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_term_insert',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
   
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();
  
      
      },

      
      payment_term_save: async function() {
        if (this.submit == true) return;

        //if(this.payment_term.page == 0) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("detail", JSON.stringify(this.payment_term));

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_payment_term_insert',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
   
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();
  
      
      },

      close_term() {
        this.show_term = false;

      },

      close_payment_term() {
        this.show_payment_term = false;

      },

      term_item_del: function(fromIndex) {

        var index = fromIndex;
        if (index > -1) {
          this.term.item.splice(index, 1);
        }
      },

      payment_term_item_del: function(fromIndex) {

        var index = fromIndex;
        if (index > -1) {
          this.payment_term.item.splice(index, 1);
        }
      },

      term_item_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.term.item.length - 1) 
          return;
  
        var element = this.term.item.find(({ id }) => id === eid);
        this.term.item.splice(fromIndex, 1);
        this.term.item.splice(toIndex, 0, element);
      },

      payment_term_item_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.payment_term.item.length - 1) 
          return;
  
        var element = this.payment_term.item.find(({ id }) => id === eid);
        this.payment_term.item.splice(fromIndex, 1);
        this.payment_term.item.splice(toIndex, 0, element);
      },

      term_item_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.term.item.find(({ id }) => id === eid);
        this.term.item.splice(fromIndex, 1);
        this.term.item.splice(toIndex, 0, element);
      },

      payment_term_item_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.payment_term.item.find(({ id }) => id === eid);
        this.payment_term.item.splice(fromIndex, 1);
        this.payment_term.item.splice(toIndex, 0, element);
      },

      add_term_item() {
        let order = 0;
        
        if(this.term.item.length != 0)
          order = Math.max.apply(Math, this.term.item.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "title" : '',
          "brief" : '',
          "list" : '',
        }, 
  
        this.term.item.push(obj);
      },

      
      add_payment_term_item() {
        let order = 0;
        
        if(this.payment_term.item.length != 0)
          order = Math.max.apply(Math, this.payment_term.item.map(function(o) { return o.id; }))
          
        obj = {
          "id" : order + 1,
          "bank_name" : '',
          "first_line" : '',
          "second_line" : '',
          "third_line" : '',
        }, 
  
        this.payment_term.item.push(obj);
      },

      close_total() {
        this.show_total = false;

        if(this.total.id == 0)
        {
          this.total = {
            id:0,
            page: 0,
            discount:'',
            vat : '',
            show_vat : '',
            valid : '',
            total : '',
          };
        }
      },

      save_total: async function() {
        if (this.submit == true) return;

        //if(this.total.page == 0) return;

        if(this.total.discount > 100 || this.total.discount < 0)
        {
          Swal.fire({
            text: "Discount must between 0 and 100.",
            icon: "info",
            confirmButtonText: "OK",
          });
          return;
        }

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("quotation_id", this.id);
        form_Data.append("page", this.total.page);
        form_Data.append("discount", this.total.discount);
        form_Data.append("vat", this.total.vat);
        form_Data.append("show_vat", this.total.show_vat);
        form_Data.append("valid", this.total.valid);
        form_Data.append("total", this.total.total);
      
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/quotation_total_insert",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            //_this.id = response.data.id;
            
            Swal.fire({
              html: response.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
  
            _this.reload();
            _this.submit = false;
          })
          .catch(function(error) {
            //handle error
            Swal.fire({
              text: JSON.stringify(error),
              icon: "info",
              confirmButtonText: "OK",
            });
  
            _this.reload();
            _this.submit = false;
          });
      
      },

      subtotal_save_changes: async function (id, type_id, temp_block) {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", id);
        form_Data.append("type_id", type_id);
        form_Data.append("block", JSON.stringify(temp_block));
      
        for (var i = 0; i < temp_block.length; i++) {
          let file = document.getElementById('block_image_' + temp_block[i].id);
          if(file) {
            let f = file.files[0];
            if(typeof f !== 'undefined') 
              form_Data.append('block_image_' + temp_block[i].id, f);
          }
        }
    

        try {
          let res = await axios({
            method: 'post',
            url: 'api/quotation_page_type_block_update',
            data: form_Data,
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });

          if(res.status == 200){
            // test for status you want, etc
            _this.block_value = [];
            _this.submit = false;

            Swal.fire({
              html: res.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });
        } 
          
        } catch (err) {
          console.log(err)
          Swal.fire({
            text: err,
            icon: "info",
            confirmButtonText: "OK",
          });

            _this.submit = false;
        }

        this.reload();

        },

      subtotal_save() {
        if(this.block_value.id == undefined)
          return;

        if(this.check_value() == false)
          return;

        var _id = this.block_value.id;
        var _type = this.block_value.type;
        var _page = this.block_value.page;

        var element = this.receive_records[0].pages.find(({ page }) => page === _page);
        var type = element.types.find(({ id }) => id === _id);
        
        if(_type == "A" && this.temp_block_a.length > 0) {
          type.block_a = this.temp_block_a;

          this.subtotal_save_changes(this.id, _id, this.temp_block_a);
        }

        if(_type == "B" && this.temp_block_b.length > 0) {
          type.block_b = this.temp_block_b;

          this.subtotal_save_changes(this.id, _id, this.temp_block_b);
        }
        
        
        this.subtotal_close();

      },

      check_value() {
        for(var i = 0; i < this.temp_block_a.length; i++) {
          if(this.temp_block_a[i].qty < 1 || this.temp_block_a[i].qty === '') {
            Swal.fire({
              text: "Qty must greater then 1.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }

          if(this.temp_block_a[i].discount < 0 || this.temp_block_a[i].discount > 100 || this.temp_block_a[i].discount === '') {
            Swal.fire({
              text: "Discount must between 0 and 100.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }
        }

        for(var i = 0; i < this.temp_block_b.length; i++) {
          if(this.temp_block_b[i].qty < 1 || this.temp_block_b[i].qty === '') {
            Swal.fire({
              text: "Qty must greater then 1.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }

          if(this.temp_block_b[i].discount < 0 || this.temp_block_b[i].discount > 100 || this.temp_block_b[i].discount === '') {
            Swal.fire({
              text: "Discount must between 0 and 100.",
              icon: "info",
              confirmButtonText: "OK",
            });

            return false;
          }
        }

        return true;
      },

      subtotal_close() {
        this.show_subtotal = false;
        this.edit_type_a = false;
        this.edit_type_b = false;
        this.temp_block_a = [];
        this.temp_block_b = [];

        this.edit_type_a_image = false;
        this.edit_type_a_noimage = false;
          
        this.edit_type_b_noimage = false;

        this.block_value = [];
      },

      block_b_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.temp_block_b.find(({ id }) => id === eid);
        this.temp_block_b.splice(fromIndex, 1);
        this.temp_block_b.splice(toIndex, 0, element);
      },

      block_b_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.temp_block_b.length - 1) 
          return;
  
        var element = this.temp_block_b.find(({ id }) => id === eid);
        this.temp_block_b.splice(fromIndex, 1);
        this.temp_block_b.splice(toIndex, 0, element);
      },

      block_b_del: function(eid) {

        var index = this.temp_block_b.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.temp_block_b.splice(index, 1);
        }
      },

      block_a_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.temp_block_a.find(({ id }) => id === eid);
        this.temp_block_a.splice(fromIndex, 1);
        this.temp_block_a.splice(toIndex, 0, element);
      },

      block_a_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.temp_block_a.length - 1) 
          return;
  
        var element = this.temp_block_a.find(({ id }) => id === eid);
        this.temp_block_a.splice(fromIndex, 1);
        this.temp_block_a.splice(toIndex, 0, element);
      },

      block_a_del: function(eid) {

        var index = this.temp_block_a.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.temp_block_a.splice(index, 1);
        }
      },

      clear_photo(_id) {
        var item = this.temp_block_a.find(({ id }) => id === _id);
        item.url = "";
  
        document.getElementById('block_image_'+_id).value = "";
      },
      
      onFileChangeImage(e, _id) {
        const file = e.target.files[0];

        var item = this.temp_block_a.find(({ id }) => id === _id);
    
        let url = URL.createObjectURL(file);
  
        item.url = url;
          
      },

      chang_discount : function(row) {
        if(row.discount > 100)
          row.discount = 100;

        let charge = (Number(row.price) * (100 - Math.floor(row.discount)) / 100).toFixed(2);
          row.amount = charge;
       
      },

      chang_amount: function(row) {
        if(row.qty == '')
          return;

        if(row.price == '')
          return;

        row.qty = Math.floor(row.qty);
        row.discount = Math.floor(row.discount);
        if(row.discount > 100)
          row.discount = 100;

        
        // let charge = this.payment_record.charge;
        let charge = (Number(row.qty)) * Number(row.price) * ((100 - Math.floor(row.discount)) / 100);

        if(this.product_vat == 'P')
          charge = charge * 1.12;

        row.amount = charge.toFixed(2);
      },

      add_block_a() {
        var block_a_image = document.getElementById('with_image').value;
        var sn = 0;
        var items = this.temp_block_a;

        var _list = `Beam Angle:
Lumens:
CRI / RA:
CCT:
Wattage:
IP Rating:
Life Hours:
Color Finish:
Installation:`;

        if(this.project_category != 'Lighting')
          _list = "";

        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        sn = sn + 1;

        item = {
          id: sn,
          url: "",
          file: {
            name: "",
          },
          type : block_a_image,
          code: "",
          photo: "",
          qty: "1",
          price: "",
          discount: "0",
          amount: "",
          desc: "",
          list: _list,
          num:"",
          pid:0,
        };

        items.push(item);

      },

      add_block_b() {
      
        var sn = 0;
        var items = this.temp_block_b;


        for (let i = 0; i < items.length; i++) {
          if (items[i].id > sn) {
            sn = items[i].id;
          }
        }

        var _list = `Beam Angle:
Lumens:
CRI / RA:
CCT:
Wattage:
IP Rating:
Life Hours:
Color Finish:
Installation:`;

        if(this.project_category != 'Lighting')
          _list = "";

        sn = sn + 1;

        item = {
          id: sn,
          
          code: "",
          photo: "",
          qty: "1",
          price: "",
 
          discount: "0",
          amount: "",
          desc: "",
          list: _list,
          num:"",
          pid:0,
        };

        items.push(item);

      },

      load_block() {
        var value = this.block_value;
        
        if(value.type == 'A')
        {
          this.edit_type_a = true;
          this.edit_type_b = false;

          this.edit_type_a_image = false;
          this.edit_type_a_noimage = false;
          
          this.edit_type_b_noimage = false;

          this.temp_block_a = this.block_value.blocks;
        }

        if(value.type == 'B')
        {
          this.edit_type_b = true;
          this.edit_type_a = false;

          this.edit_type_a_image = false;
          this.edit_type_a_noimage = false;

          this.edit_type_b_noimage = false;

          this.temp_block_b = this.block_value.blocks;
        }
      
      },

      filter_apply: function(pg) {
        let _this = this;

        pg !== undefined ? this.pg  = pg : this.pg = this.product_page;
  
        const params = {
          d: _this.fil_id,
          c: _this.fil_code,
          t: JSON.stringify(_this.fil_tag),
          b: _this.fil_brand,
          of1: '',
          ofd1: '',
          of2: '',
          ofd2: '',
          page: _this.pg,
          size: 10,
        };
    
        let token = localStorage.getItem("accessToken");
    
        this.product_total = 0;
    
        axios
          .get("api/product_calatog", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(function(response) {
            console.log(response.data);
            let res = response.data;
            if(res.length > 0) 
            {
              _this.product_records = response.data;
              _this.product_total = _this.product_records[0].cnt;
            }
    
            if(_this.pg !== 0)
            { 
              _this.product_page = _this.pg;
              _this.setPages();
            }
            
      
          })
          .catch(function(error) {
            console.log(error);
          });
      },

      cancel_header() {
        this.show_header = false;
      },

      save_header() {
        this.show_header = false;

        this.first_line = this.temp_first_line;
        this.second_line = this.temp_second_line;
        this.project_category = this.temp_project_category;
        this.quotation_no = this.temp_quotation_no;
        this.quotation_date = this.temp_quotation_date;
        this.prepare_for_first_line = this.temp_prepare_for_first_line;
        this.prepare_for_second_line = this.temp_prepare_for_second_line;
        this.prepare_for_third_line = this.temp_prepare_for_third_line;

        this.prepare_by_first_line = this.temp_prepare_by_first_line;
        this.prepare_by_second_line = this.temp_prepare_by_second_line;

        this.header_save();
        
      },

      cancel_footer() {
        this.show_footer = false;
      },

      save_footer() {
        this.show_footer = false;

        this.footer_first_line = this.temp_footer_first_line;
        this.footer_second_line = this.temp_footer_second_line;

        this.footer_save();
        
      },

      reset: function() {
        this.submit = false;
        // header
        this.first_line = '';
        this.second_line = '';
        this.project_category = '';
        this.quotation_no = '';
        this.quotation_date = '';

        this.prepare_for_first_line = '';
        this.prepare_for_second_line ='';
        this.prepare_for_third_line = '';

        this.prepare_by_first_line = '';
        this.prepare_by_second_line = '';

        // footer
        this.footer_first_line = '';
        this.footer_second_line = '';

        // _header
        this.temp_first_line = '';
        this.temp_second_line = '';
        this.temp_project_category = '';
        this.temp_quotation_no = '';
        this.temp_quotation_date = '';

        this.temp_prepare_for_first_line = '';
        this.temp_prepare_for_second_line ='';
        this.temp_prepare_for_third_line = '';

        this.temp_prepare_by_first_line = '';
        this.temp_prepare_by_second_line = '';

        // _footer
        this.temp_footer_first_line = '';
        this.temp_footer_second_line = '';

        this.subtotal = 0;
        this.subtotal_novat_a = 0;
        this.subtotal_novat_b = 0;

        // page
        this.pages = [];
        this.temp_pages = [];
      },

      getRecord: function() {
        let _this = this;

        if(_this.id == 0)
          return;
  
        const params = {
          id: _this.id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/quotation", {
              params,
              headers: { Authorization: `Bearer ${token}` },
            })
          .then(function(response) {
            console.log(response.data);
            _this.receive_records = response.data;
            if (_this.receive_records.length > 0) {
              _this.id = _this.receive_records[0].id;
              _this.first_line = _this.receive_records[0].first_line;
              _this.second_line = _this.receive_records[0].second_line;
              _this.project_category = _this.receive_records[0].project_category;
              _this.quotation_no = _this.receive_records[0].quotation_no;
              _this.quotation_date = _this.receive_records[0].quotation_date;

              _this.prepare_for_first_line = _this.receive_records[0].prepare_for_first_line;
              _this.prepare_for_second_line = _this.receive_records[0].prepare_for_second_line;
              _this.prepare_for_third_line = _this.receive_records[0].prepare_for_third_line;

              _this.prepare_by_first_line = _this.receive_records[0].prepare_by_first_line;
              _this.prepare_by_second_line = _this.receive_records[0].prepare_by_second_line;

              // footer
              _this.footer_first_line = _this.receive_records[0].footer_first_line;
              _this.footer_second_line = _this.receive_records[0].footer_second_line;

              // page
              _this.pages = _this.receive_records[0].pages;
              _this.temp_pages = JSON.parse(JSON.stringify(_this.pages));

              // block_names(subtotal)
              _this.block_names = _this.receive_records[0].block_names;
              
              _this.subtotal = _this.receive_records[0].subtotal_info;
              _this.subtotal_novat_a = _this.receive_records[0].subtotal_novat_a;
              _this.subtotal_novat_b = _this.receive_records[0].subtotal_novat_b;

              // total
              _this.total = _this.receive_records[0].total_info;
              // get product_vat from total.vat
              _this.total.vat !== undefined ? _this.product_vat = _this.total.vat : _this.product_vat = '';

              _this.subtotal_info_not_show_a = _this.receive_records[0].subtotal_info_not_show_a;
              _this.subtotal_info_not_show_b = _this.receive_records[0].subtotal_info_not_show_b;
              _this.count_subtotal();

              // term
              _this.term = _this.receive_records[0].term_info;

              // term
              _this.payment_term = _this.receive_records[0].payment_term_info;

              // sig
              _this.sig = _this.receive_records[0].sig_info;

              

              // temp
              _this.id = _this.receive_records[0].id;
              _this.temp_first_line = _this.receive_records[0].first_line;
              _this.temp_second_line = _this.receive_records[0].second_line;
              _this.temp_project_category = _this.receive_records[0].project_category;
              _this.temp_quotation_no = _this.receive_records[0].quotation_no;
              _this.temp_quotation_date = _this.receive_records[0].quotation_date;

              _this.temp_prepare_for_first_line = _this.receive_records[0].prepare_for_first_line;
              _this.temp_prepare_for_second_line = _this.receive_records[0].prepare_for_second_line;
              _this.temp_prepare_for_third_line = _this.receive_records[0].prepare_for_third_line;

              _this.temp_prepare_by_first_line = _this.receive_records[0].prepare_by_first_line;
              _this.temp_prepare_by_second_line = _this.receive_records[0].prepare_by_second_line;

              // footer
              _this.temp_footer_first_line = _this.receive_records[0].footer_first_line;
              _this.temp_footer_second_line = _this.receive_records[0].footer_second_line;
              
            }
          })
          .catch(function(error) {
            console.log(error);
          });
  
      },

      count_subtotal() {
        if(this.total.total == '0.00')
        {
          //this.total.total = (this.subtotal * (1 - this.total.discount * 0.01));
          //if(this.total.vat == 'Y')
          //  this.total.total = (this.total.total * 1) + (this.subtotal_novat_a * 0.12);
          this.total.total = "";
        }
        else
          this.total.total = Number(this.total.total).toFixed(2);

        this.total.real_total = ((this.subtotal_info_not_show_a * 1 + this.subtotal_info_not_show_b * 1)  * (1 - this.total.discount * 0.01));

        if(this.total.vat == 'Y')
          this.total.real_total = (this.total.real_total * 1) + (this.subtotal_info_not_show_a * (1 - this.total.discount * 0.01) * 0.12);

          this.total.real_total = Number(this.total.real_total).toFixed(2);
    
      },

      add_page() {
        let order = 0;
        
        if(this.temp_pages.length != 0)
          order = Math.max.apply(Math, this.temp_pages.map(function(o) { return o.id; }))
          
        types = [];

        sig = {
          "page" : order + 1,
          "item_client" : [],
          "item_company" : [],
        }
        
        obj = {
          "id" : order + 1,
          "page" : order + 1,
          "sig" : sig,
          "term" : [],
          "payment_term" : [],
          "total" : [],
          "types" : types,
        }, 
  
        this.temp_pages.push(obj);
      },

      add_item(eid) {
        var element = this.temp_pages.find(({ id }) => id === eid);

        let obj_id = 0;

        if(element.types.length != 0)
          obj_id = Math.max.apply(Math, element.types.map(function(o) { return o.id; }))
        
        obj = {
          "id" : obj_id + 1,
          "org_id" : 0,
          "type" : document.getElementById('block_type_' + eid).value,
          "name" : "",
          "real_amount" : "0.0",
          "not_show" : "",
        }, 

        element.types.push(obj);
      },

      page_up: function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.temp_pages.find(({ id }) => id === eid);
        this.temp_pages.splice(fromIndex, 1);
        this.temp_pages.splice(toIndex, 0, element);
      },

      page_down: function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.temp_pages.length - 1) 
          return;
  
        var element = this.temp_pages.find(({ id }) => id === eid);
        this.temp_pages.splice(fromIndex, 1);
        this.temp_pages.splice(toIndex, 0, element);
      },

      page_del: function(eid) {

        var index = this.temp_pages.findIndex(({ id }) => id === eid);
        if (index > -1) {
          this.temp_pages.splice(index, 1);
        }
      },

      set_up: function(pid, fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) toIndex = 0;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        page.types.splice(toIndex, 0, element);
      },

      set_up_page: function(pid, page_index, fromIndex, eid) {
        var toIndex = page_index - 1;
  
        if (toIndex < 0)
          return;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        this.temp_pages[toIndex].types.splice(this.temp_pages[toIndex].types.length - 1, 0, element);
      },
  
      set_down: function(pid, fromIndex, eid) {
        var toIndex = fromIndex + 1;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        if (toIndex > page.types.length - 1) toIndex = page.types.length - 1;
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        page.types.splice(toIndex, 0, element);
      },

      set_down_page: function(pid, page_index, fromIndex, eid) {
        var toIndex = page_index + 1;

        var page = this.temp_pages.find(({ id }) => id === pid);
  
        if (toIndex > page.types.length - 1) 
          return;
  
        var element = page.types.find(({ id }) => id === eid);
        page.types.splice(fromIndex, 1);
        this.temp_pages[toIndex].types.splice(this.temp_pages[toIndex].types.length - 1, 0, element);
      },

      del_block: function(pid, eid) {
        var page = this.temp_pages.find(({ id }) => id === pid);
        var index = page.types.findIndex(({ id }) => id === eid);
        if (index > -1) {
          page.types.splice(index, 1);
        }
      },

      header_save : function() {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);
        form_Data.append("first_line", this.first_line);
        form_Data.append("second_line", this.second_line);
        form_Data.append("project_category", this.project_category);
        form_Data.append("quotation_no", this.quotation_no);
        form_Data.append("quotation_date", this.quotation_date);
        form_Data.append("prepare_for_first_line", this.prepare_for_first_line);
        form_Data.append("prepare_for_second_line", this.prepare_for_second_line);
        form_Data.append("prepare_for_third_line", this.prepare_for_third_line);
        form_Data.append("prepare_by_first_line", this.prepare_by_first_line);
        form_Data.append("prepare_by_second_line", this.prepare_by_second_line);
    
  
        if(this.id == 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_header_insert",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.id = response.data.id;
              
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }

        if(this.id != 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_header_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }
      
      },

      footer_save : function() {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);

        form_Data.append("footer_first_line", this.footer_first_line);
        form_Data.append("footer_second_line", this.footer_second_line);
          
  
        if(this.id == 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_footer_insert",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.id = response.data.id;
              
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }

        if(this.id != 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_footer_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }
      
      },

      page_save : function() {
        if (this.submit == true) return;

        this.submit = true;
  
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
 
        form_Data.append("id", this.id);

        form_Data.append("first_line", this.first_line);
        form_Data.append("second_line", this.second_line);
        form_Data.append("project_category", this.project_category);
        form_Data.append("quotation_no", this.quotation_no);
        form_Data.append("quotation_date", this.quotation_date);
        form_Data.append("prepare_for_first_line", this.prepare_for_first_line);
        form_Data.append("prepare_for_second_line", this.prepare_for_second_line);
        form_Data.append("prepare_for_third_line", this.prepare_for_third_line);
        form_Data.append("prepare_by_first_line", this.prepare_by_first_line);
        form_Data.append("prepare_by_second_line", this.prepare_by_second_line);

        form_Data.append("footer_first_line", this.footer_first_line);
        form_Data.append("footer_second_line", this.footer_second_line);

        form_Data.append("pages", JSON.stringify(this.temp_pages));
          
  
        if(this.id == 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_insert",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              _this.id = response.data.id;
              
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }

        if(this.id != 0) {
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/quotation_update",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });
    
              _this.reload();
              _this.submit = false;
            });
        }
      
      },

      reload : function() {
        this.close_all();

        if(this.l_id == 0 && this.id != 0) 
        {
          this.l_id = this.id;
          this.filter_apply();
        }
        else
          this.getRecord();
      },

      product_catalog_a() {
        this.toggle_type = 'A';
        $('#modal_product_catalog').modal('toggle');
      },

      product_catalog_b() {
        this.toggle_type = 'B';
        $('#modal_product_catalog').modal('toggle');
    },

      close_all() {
        this.show_signature = false;
        this.show_footer = false;
        this.show_page = false;
        this.show_subtotal = false;
        this.show_total = false;
        this.show_term = false;
        this.show_payment_term = false;
        this.show_header = false;
        console.log("close all");
      },

      search() {
          this.filter_apply();
      },
  
    
      
      getLeaveCredit: function() {
        let _this = this;
  
        const params = {
          id: _this.title_id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/template_library", {
              params,
              headers: { Authorization: `Bearer ${token}` },
            })
          .then(function(response) {
            console.log(response.data);
            _this.receive_records = response.data;
            _this.title_info = _this.receive_records.title;
            _this.template = _this.receive_records.template;
            _this.library = _this.receive_records.library;
  
          })
          .catch(function(error) {
            console.log(error);
          });
  
      },
  
      view_template:function(){
          if (this.title_id == 0) {
              Swal.fire({
                  text: "Please choose position to view template",
                  icon: "warning",
                  confirmButtonText: "OK",
                });
              return;
            }else
            {
              
              this.reset();
  
              this.getLeaveCredit();
              this.view_detail = true;
  
            }
          
      },
  
      detail: function() {
        let _this = this;
  
        if (this.proof_id == 0) {
          //this.view_detail = false;
          return;
        }
  
        this.record = this.shallowCopy(
          this.receive_records.find((element) => element.id == this.proof_id)
        );
  
        this.e_title = this.shallowCopy(
          this.position.find((element) => element.did == this.record.did)
        ).items;
  
        this.e_department = this.record.did;
        this.e_tid = this.record.tid;
  
        this.e_sn = this.record.agenda.length;
        this.e_sn1 = this.record.agenda1.length;
        this.e_sn2 = this.record.agenda2.length;
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
        element.scrollIntoView({ behavior: 'smooth' });
    
      },
  
      on_grade_change:function(event) {
          console.log(event.target.value);
          var grade = this.$refs.grade;
    
          var score =0.0;
          var cnt = 0;
          for (i = 0; i < grade.length; i++) {
            if(grade[i].value === 'N/A')
              score += 0;
            else
            {
              score += parseInt(grade[i].value);
              cnt += 1;
            }
          }
    
          if(cnt === 0)
            this.avg = 0;
          else
            this.avg = (score / cnt).toFixed(1);
    
        },
    
        on_grade1_change:function(event) {
          console.log(event.target.value);
          var grade = this.$refs.grade1;
    
          var score =0.0;
          var cnt = 0;
          for (i = 0; i < grade.length; i++) {
            if(grade[i].value === 'N/A')
              score += 0;
            else
            {
              score += parseInt(grade[i].value);
              cnt += 1;
            }
          }
    
          if(cnt === 0)
            this.avg1 = 0;
          else
            this.avg1 = (score / cnt).toFixed(1);
    
        },
  
        on_grade2_change:function(event) {
          console.log(event.target.value);
          var grade = this.$refs.grade2;
    
          var score =0.0;
          var cnt = 0;
          for (i = 0; i < grade.length; i++) {
            if(grade[i].value === 'N/A')
              score += 0;
            else
            {
              score += parseInt(grade[i].value);
              cnt += 1;
            }
          }
    
          if(cnt === 0)
            this.avg2 = 0;
          else
            this.avg2 = (score / cnt).toFixed(1);
    
        },
  
      reset: function() {
   
        this.receive_records = [];
        this.title_info = {};
        this.template = {};
        this.library = {};
  
        this.evals = {};
        this.avg = 10.0;
        this.avg1 = 10.0;
        this.avg2 = 10.0;
  
        this.view_detail = false;
      },
  
      shallowCopy(obj) {
        console.log("shallowCopy");
        var result = {};
        for (var i in obj) {
          result[i] = obj[i];
        }
        return result;
      },

      pre_page: function(){
        let tenPages = Math.floor((this.product_page - 1) / 10) + 1;
  
          this.product_page = parseInt(this.product_page) - 10;
          if(this.product_page < 1)
            this.product_page = 1;
   
          this.product_pages_10 = [];
  
          let from = tenPages * 10;
          let to = (tenPages + 1) * 10;
  
          this.product_pages_10 = this.product_pages.slice(from, to);
        
      },
  
      nex_page: function(){
        let tenPages = Math.floor((this.product_page - 1) / 10) + 1;
  
        this.product_page = parseInt(this.product_page) + 10;
        if(this.product_page > this.product_pages.length)
          this.product_page = this.product_pages.length;
  
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;
        let pages_10 = this.product_pages.slice(from, to);
  
        if(pages_10.length > 0)
          this.product_pages_10 = pages_10;
  
      },
  
      setPages() {
        console.log("setPages");
        this.product_pages = [];
        let numberOfPages = Math.ceil(this.product_total / this.perPage);
  
        if (numberOfPages == 1) this.product_page = 1;
        if (this.product_page < 1) this.product_page = 1;
        for (let index = 1; index <= numberOfPages; index++) {
          this.product_pages.push(index);
        }
      },
  
      paginate: function(posts) {
        console.log("paginate");
        if (this.product_page < 1) this.product_page = 1;
        if (this.product_page > this.product_pages.length) this.product_page = this.product_pages.length;
  
        let tenPages = Math.floor((this.product_page - 1) / 10);
        if(tenPages < 0)
          tenPages = 0;
        this.product_pages_10 = [];
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;
        
        this.product_pages_10 = this.product_pages.slice(from, to);
  
        return this.product_records;
      },

      change_v(){
        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3)
        )
  
        if(item_product.id != undefined)
        {
          if(item_product.photo != "")
            this.url = this.img_url + item_product.photo;
          else
            this.url = "";
          this.price_ntd = "NTD " + Number(item_product.price_ntd).toLocaleString();
          this.price = "PHP " + Number(item_product.price).toLocaleString();
          this.quoted_price = "PHP " + Number(item_product.quoted_price).toLocaleString();
        }
        else
        {
          this.url = this.img_url + this.product['photo1'];
          this.price_ntd = this.product['price_ntd'];
          this.price = this.product['price'];
          this.quoted_price = this.product['quoted_price'];
        }
  
      },

      print_me() {
 
        html2canvas(document.querySelector(".company_signature"), { proxy: "html2canvasproxy", useCORS: false, logging: true, allowTaint: true}).then(canvas => {
          //document.body.appendChild(canvas)
          return Canvas2Image.saveAsPNG(canvas);

          //const el = this.$refs.printMe;
          // add option type to get the image version
          // if not provided the promise will return 
          // the canvas.
          // const options = {
          //   type: 'dataURL'
          // };
          // (async () => {
          //     html2canvas(document.querySelector('.specific'), {
          //       onrendered: function(canvas) {
          //         // document.body.appendChild(canvas);
          //         return Canvas2Image.saveAsPNG(canvas);
          //       }
          //     });
          // })()
        
      });
      
      /*
        const options = {
          type: 'dataURL'
        };
        (async () => {
            html2canvas(document.querySelector('.company_signature'), {
              useCORS: true,
              allowTaint : true,
              onrendered: function(canvas) {
                //document.body.appendChild(canvas);
                //return Canvas2Image.saveAsPNG(canvas);

                const context = canvas.getContext('2d');
                context.mozImageSmoothingEnabled = false;
                context.webkitImageSmoothingEnabled = false;
                context.msImageSmoothingEnabled = false;
                context.imageSmoothingEnabled = false;
                const src64 = canvas.toDataURL();
                const newImg = document.createElement('img');
                newImg.crossOrigin = 'Anonymous';
                newImg.src = src64;
                document.body.appendChild(newImg);
              },
              logging:true
            });
        })()

*/


      },


    }
  
  });
  