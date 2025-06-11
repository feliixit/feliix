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
        variation4_value: [],
    
        variation_product: [],

        v1:"",
        v2:"",
        v3:"",
        v4:"",

        accessory_infomation: [],

        related_product: [],
        specification: [],
        description: "",
        replacement_product: [],

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

        toggle_type:'A',
        toggle: false,

        groupedItems : [],
        groupedItems_replacement : [],

        //
        fil_project_category:'',
        fil_project_creator: '',
        fil_kind: '',
        fil_creator: '',
        fil_keyword: '',
        fil_category: '',

        users: [],
        creators: [],

        items: [],
        uid: 0,

        message: "",
        arrTask: [],
        taskCanSub: [],
        taskFinish: [],
        current_task_id: 0,

        // quotation information
        receive_records_quo_master:[],
        pg_quo:0,
        page_quo:0,
        pages_quo : [],

        displayedQuoDetailPosts:[],

        // paging
        product_page_quo: 1,
        pg_quo:0,
        //perPage: 10,
        product_pages_quo: [],
        product_pages_10_quo: [],
        comment:'',

        // privledge
        access1 : false,
        access2 : false,
        access3 : false,
        access4 : false,
        access5 : false,
        access6 : false,
        access7 : false,

        od_name : '',
        stage_id : '',
        project_name : '',
        serial_name : '',
        project_id : 0,

        of1:'',
        ofd1:'',

        phased : 0,
        fil_k : '',

        tag_group : [],

        out : "",
        out_cnt : 0,

        quotation_total : 0,

        
        // product information
        p_product : {},

        p_baseURL: "https://storage.googleapis.com/feliiximg/",

        p_category: "",
        p_sub_category: "",
        p_sub_category_name: "",
        p_sub_cateory_item: [],

        //
        p_id:-1,
        p_record: [],
        p_tags: [],

        p_cateory_item: [],

        p_special_infomation: [],
        p_special_infomation_detail: [],

        p_accessory_infomation: [],
        p_sub_accessory_item: [],
        p_attributes:[],

        //
        p_accessory_item: [],

        p_edit_mode: true,

        p_title: [],

        p_title_id: 0,

        p_url: null,
        p_url1: null,
        p_url2: null,
        p_url3: null,

        // data
        p_pid : "",
        p_brand: "",
        p_code: "",
        p_price_ntd: "",
        p_price: "",
        p_price_quoted: "",
        p_price_ntd_change: "",
        p_price_change: "",
        p_price_ntd_org: "",
        p_price_org: "",
        p_description: "",
        p_notes: "",
        p_accessory_mode: false,
        p_variation_mode: false,

        p_quoted_price:"",
        p_quoted_price_org:"",
        p_quoted_price_change:"",
        p_moq:"",
        p_currency: "",

        p_str_quoted_price_change: "",
        p_str_price_ntd_change: "",
        p_str_price_change: "",

        p_phased_out: "",

        p_sheet_url: "",

        // accessory

        // variation
        p_variation1: "",
        p_variation2: "",
        p_variation3: "",
        p_variation4: "",
        p_variation1_custom: "",
        p_variation2_custom: "",
        p_variation3_custom: "",
        p_variation4_custom: "",

        p_variation1_text: "1st Variation",
        p_variation2_text: "2nd Variation",
        p_variation3_text: "3rd Variation",
        p_variation4_text: "4th Variation",

        p_variation1_value: [],
        p_variation2_value: [],
        p_variation3_value: [],
        p_variation4_value: [],

        p_variation_product: [],

        p_related_product : [],
        p_nColumns: 4,
        p_groupedItems: [],

        replacement_product : [],
    is_replacement_product: [],

        p_show_accessory: false,

        p_v1:"",
        p_v2:"",
        p_v3:"",
        p_v4:"",

        // bulk insert
        p_code_checked:'',
        p_bulk_code:'',
        p_price_ntd_checked:'',
        p_bulk_price_ntd:'',
        p_price_ntd_action :'',
        p_price_checked:'',
        p_bulk_price:'',
        p_price_action:'',
        p_image_checked:'',
        p_bulk_url:'',
        p_status_checked:'',
        p_bulk_status:'',
        p_currency:'',

        p_submit: false,

        p_specification: [],

        // info
        p_name :"",
        p_title: "",
        p_is_manager: "",

        p_toggle: true,

        p_print_pid: 'true',
        p_print_brand: 'true',
        p_print_srp: 'true',
        p_print_qp: 'true',

        p_out : "",
        p_out_cnt : 0,
        p_phased_out_text : [],

        p_item_product : {},

        product_set : [],
        show_accessory: false,

        is_last_order : '',
        last_order_name : '',
        last_order_at : '',
        last_order_url : '',
        last_have_spec : true,

        cost_lighting : false,
        cost_furniture : false,
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
                // case "role":
                //   var role = tmp[1];
  
                //   if(role == 1)
                //    _this.access1 = true;
                  
                //    if(role == 2)
                //    _this.access2 = true;
  
                //    if(role == 3)
                //    _this.access3 = true;
  
                //    if(role == 4)
                //    _this.access4 = true;
  
                //    if(role == 5)
                //    _this.access5 = true;
  
                //    if(role == 6)
                //    _this.access6 = true;

                //     if(role == 7)
                //     _this.access7 = true;
  
                //   break;
              default:
                console.log(`Too many args`);
            }
          }
        });
      }
      
      this.get_product_records();
      this.getQuoMasterRecords();
      this.getRecord();
      this.getUserName();
      this.get_brands();
      this.getUsers();
      this.getCreators();
      this.getAccess();
      this.getOdMain();
      this.getTagGroup();
      this.getProductControl();
    },
  
    computed: {
      displayedQuoMasterPosts() {
        //if(this.pg == 0)
        //  this.filter_apply_new();

        this.setPagesQuo();
        return this.paginateQuo(this.receive_records_quo_master);

      },

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

      
      department() {
        this.title = this.shallowCopy(
          this.position.find((element) => element.did == this.department)
        ).items;
  
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

      sort_me(type) {

        if(type == 1) {
          this.of1 = '4';
          this.ofd1 = '1';
        } else if(type == 2) {
          this.of1 = '4';
          this.ofd1 = '2';
        } else if(type == 3) {
          this.of1 = '5';
          this.ofd1 = '1';
        } else if(type == 4) {
          this.of1 = '5';
          this.ofd1 = '2';
        } else if(type == 0) {
          this.fil_id = '';
          this.fil_code = '';
          this.fil_tag = [];
          this.fil_brand = '';
          this.fil_category = '';
          this.fil_k = '';
          this.of1 = '';
          this.ofd1 = '';
          this.pg = 1;
          $("#tag01").val('default');
          $("#tag01").selectpicker("refresh");
        }

        this.filter_apply_new();
      },

      getOdMain: function() {
        let _this = this;
  
        const params = {
  
                  fc : '',
                  fpc: '',
                  fpt: '',
                  fg: '',
                  key: '',
                  kind: '',
  
                  op1: '',
                  od1: '',
                  op2: '',
                  od2: '',
                  id : _this.id,
              };
  
        
      
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/order_sample_mgt', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.od_name = res.data[0].od_name;
                    _this.stage_id = res.data[0].stage_id;
                    _this.project_name = res.data[0].project_name;
                    _this.serial_name = res.data[0].serial_name;
                    _this.project_id = res.data[0].project_id;
  
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
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


      no_privlege() {
        if(this.access1 == false && this.access2 == false && this.access3 == false && this.access4 == false && this.access5 == false && this.access6 == false && this.access7 == false)
          return true;
        else
          return false;
      },

      p2() {
        window.location.href = "order_taiwan_sample_p2?id=" + this.id;
      },

      p3() {
        window.location.href = "order_taiwan_sample_p3?id=" + this.id;
      },

      p4() {
        window.location.href = "order_taiwan_sample_p4?id=" + this.id;
      },

      finish_notes : async function() {
        let element = [];

        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].is_checked == 1) {
              if(this.items[i].status != "1")
              {
                alert("Please only choose the item(s) with the status of “Waiting Notes from TW”.");
                return;
              }
              else
                element.push(this.items[i]);
            }
        }

        if(element.length == 0)
          return;

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("od_id", this.id);
        form_Data.append("items", JSON.stringify(element));
        form_Data.append("comment", this.comment);
        form_Data.append("od_name", this.od_name);
        form_Data.append("project_name", this.project_name);
        form_Data.append("serial_name", this.serial_name);

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_sample_p1_finish_notes',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        this.getRecord();
        this.comment = '';

        Swal.fire({
          text: "Records Finished",
          icon: "info",
          confirmButtonText: "OK",
        });
      },

      check_qty : async function(items) {
        var msg = "";
        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("items", JSON.stringify(items));

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_moq_check',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        msg = res.data.ret;

        return msg;
      },

      approval : async function() {
        let element = [];

        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].is_checked == 1) {
              if(this.items[i].confirm != "C" && this.items[i].confirm != "J")
              {
                alert("Only “Confirmed” item or “From Warehouse” item is allowed to send for approval.");
                return;
              }
              else
                element.push(this.items[i]);
            }
        }

        if(element.length == 0)
          return;

          // check items qty + backup_qty > item.pid.moq
        var msg = await this.check_qty(element);

        if(msg != "")
        {
          let res = await Swal.fire({
            title: 'MOQ Check',
            html: msg + "Are you sure to continue submitting for approval?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
          });

          if(! res.isConfirmed)
          {
            return;
          }
        }

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("od_id", this.id);
        form_Data.append("items", JSON.stringify(element));
        form_Data.append("comment", this.comment);
        form_Data.append("od_name", this.od_name);
        form_Data.append("project_name", this.project_name);
        form_Data.append("serial_name", this.serial_name);

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_sample_p1_approve',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        this.getRecord();
        this.comment = '';

        Swal.fire({
          text: "Records Sent",
          icon: "info",
          confirmButtonText: "OK",
        });
      },

      sendNotesToTw : async function() {
        let element = [];

        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].is_checked == 1) {
              element.push(this.items[i]);
            }
        }

        if(element.length == 0)
          return;

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("od_id", this.id);
        form_Data.append("items", JSON.stringify(element));
        form_Data.append("comment", this.comment);
        form_Data.append("od_name", this.od_name);
        form_Data.append("project_name", this.project_name);
        form_Data.append("serial_name", this.serial_name);

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_sample_p1_send_note_tw',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        this.getRecord();
        this.comment = '';

        Swal.fire({
          text: "Records Send To TW",
          icon: "info",
          confirmButtonText: "OK",
        });
      },

      withdrawNotesToTw : async function() {
        let element = [];

        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].is_checked == 1) {
              element.push(this.items[i]);
            }
        }

        if(element.length == 0)
          return;

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("od_id", this.id);
        form_Data.append("items", JSON.stringify(element));
        form_Data.append("comment", this.comment);
        form_Data.append("od_name", this.od_name);
        form_Data.append("project_name", this.project_name);
        form_Data.append("serial_name", this.serial_name);

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_sample_p1_withdraw_note_tw',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        this.getRecord();
        this.comment = '';

        Swal.fire({
          text: "Records Withdraw",
          icon: "info",
          confirmButtonText: "OK",
        });
      },
      
    getCreators () {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      axios
          .get('api/admin/quotation_creators', { headers: {"Authorization" : `Bearer ${token}`} })
          .then(
          (res) => {
              _this.creators = res.data;
          },
          (err) => {
              alert(err.response);
          },
          )
          .finally(() => {
              
          });
  },

      getUsers () {

        let _this = this;
  
        let token = localStorage.getItem('accessToken');
  
        axios
            .get('api/admin/quotation_project_creators', { headers: {"Authorization" : `Bearer ${token}`} })
            .then(
            (res) => {
                _this.users = res.data;
            },
            (err) => {
                alert(err.response);
            },
            )
            .finally(() => {
                
            });
    },

      quotation_import: async function(item) {
        let _this = this;
        var sn = 0;

        for (let i = 0; i < this.items.length; i++) {
            if (this.items[i].id * 1 > sn) {
              sn = this.items[i].id * 1;
            }
        }
        sn = sn * 1 + 1;
        
        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("od_id", this.id);
        form_Data.append("qid", item.id);
        form_Data.append("sn", sn);

        form_Data.append("access7", this.access7)

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_p1_quotation_import',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        this.getRecord();

        alert('Add Successfully');
      },

      filter_apply: function(pg) {
        let _this = this;

        pg !== undefined ? this.pg  = pg : this.pg = this.product_page;
  
        const params = {
          sd: _this.fil_id,
          c: _this.fil_code,
          t: JSON.stringify(_this.fil_tag),
          b: _this.fil_brand,
          g: _this.fil_category,
          k: _this.fil_k,
          of1: _this.of1,
          ofd1: _this.ofd1,
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
      
      pre_page_quo: function(){
        let tenPages = Math.floor((this.product_page_quo - 1) / 10) + 1;
  
          this.product_page_quo = parseInt(this.product_page_quo) - 10;
          if(this.product_page_quo < 1)
            this.product_page_quo = 1;
   
          this.product_pages_10_quo = [];
  
          let from = tenPages * 10;
          let to = (tenPages + 1) * 10;
  
          this.product_pages_10_quo = this.product_pages_quo.slice(from, to);
        
      },
  
      nex_page_quo: function(){
        let tenPages = Math.floor((this.product_page_quo - 1) / 10) + 1;
  
        this.product_page_quo = parseInt(this.product_page_quo) + 10;
        if(this.product_page_quo > this.product_pages_quo.length)
          this.product_page_quo = this.product_pages_quo.length;
  
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;
        let pages_10 = this.product_pages_quo.slice(from, to);
  
        if(pages_10.length > 0)
          this.product_pages_10_quo = pages_10;
  
      },

      setPagesQuo () {
        console.log('setPagesQuo');
        this.product_pages_quo = [];
        let numberOfPages = Math.ceil(this.quotation_total / this.perPage);

        if(numberOfPages == 1)
          this.product_page_quo = 1;
        for (let index = 1; index <= numberOfPages; index++) {
          this.product_pages_quo.push(index);
        }

        // this.setupChosen();
      },

    paginateQuo: function (posts) {
       
      if (this.product_page_quo < 1) this.product_page_quo = 1;
      if (this.product_page_quo > this.product_pages_quo.length) this.product_page_quo = this.product_pages_quo.length;

      let tenPages = Math.floor((this.product_page_quo - 1) / 10);
      if(tenPages < 0)
        tenPages = 0;
      this.product_pages_10_quo = [];
      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      
      this.product_pages_10_quo = this.product_pages_quo.slice(from, to);

      from = this.product_page_quo * this.perPage - this.perPage;
      to = this.product_page_quo * this.perPage;

      return  this.receive_records_quo_master;
        //return  this.receive_records_quo_master.slice(from, to);
      },

      getQuoMasterRecords: function(keyword) {
        let _this = this;
  
        const params = {
  
                  fc : _this.fil_project_category,
                  fpc: _this.fil_project_creator,
                  fpt: _this.fil_creator,
         
                  key: _this.fil_keyword,
                  kind: _this.fil_kind,
  
                  op1: _this.od_opt1,
                  od1: _this.od_ord1,
                  op2: _this.od_opt2,
                  od2: _this.od_ord2,

                  page: _this.product_page_quo,
                  size: 10,

                  all: 'all',
              };
  
        
      
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/quotation_mgt', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.receive_records_quo_master = res.data;

                    if(_this.receive_records_quo_master.length > 0)
                      _this.quotation_total = _this.receive_records_quo_master[0].cnt;
  
                    if(_this.pg_quo !== 0)
                    { 
                      _this.page_quo = _this.pg_quo;
                      _this.setPagesQuo();
                    }
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },


      change_v(){
        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3 && element.v4 == this.v4)
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
          this.phased = item_product.enabled == 0 ? 1 : 0;

          this.out = item_product.enabled == 1 ? "" : "Y";
          this.out_cnt = 0;

          if(this.product['out'] == 'Y')
          {
              this.out = "Y";
              this.out_cnt = 0;
          }

          this.last_order_name = this.product.last_order_name;
          this.last_order_at = this.product.last_order_at;
          this.last_order_url = this.product.last_order_url;

          this.product.last_order_name = item_product.last_order_name;
          this.product.last_order_at = item_product.last_order_at;
          this.product.last_order_url = item_product.last_order_url;
          this.last_have_spec = false;
          this.product.last_have_spec = false;
        }
        else
        {
          this.url = this.img_url + this.product['photo1'];
          this.price_ntd = this.product['price_ntd'];
          this.price = this.product['price'];
          this.quoted_price = this.product['quoted_price'];
          this.phased = 0;

          this.out = this.product['out'];
          this.out_cnt = this.product['phased_out_cnt'];

          this.product.last_order_name = this.last_order_name;
          this.product.last_order_at = this.last_order_at;
          this.product.last_order_url = this.last_order_url;

          this.last_order_name = "";
          this.last_order_at = "";
          this.last_order_url = "";

          this.product.last_order_url = "";
          this.last_have_spec = true;
          this.product.last_have_spec = true;
        }
  
      },

      PhaseOutAlert(phased_out_text){
        hl = "";
        for(var i = 0; i < phased_out_text.length; i++)
        {
          hl += "(" + Number(i+1) + ") " + phased_out_text[i] + "<br/>";
        }
  
        Swal.fire({
          title: 'Phased Out Variants:',
          html: hl,
          confirmButtonText: 'OK',
          });
        
      },

      incoming_qty_info: function(info) {
        if(info == '')
          return;
        
        Swal.fire({
          title: "<i>Incoming Qty</i>", 
          html: info,  
          confirmButtonText: "Close", 
        });
      },
      
      phased_out_info: function(info) {
        Swal.fire({
          title: "<i>Phased-out Variants:</i>", 
          html: info,  
          confirmButtonText: "Close", 
        });
      },

      replacement_info: function(info) {
        Swal.fire({
          title: "<i>Replacement Product:</i>", 
          html: info,  
          confirmButtonText: "Close", 
        });
      },

      last_order_info: function(info) {
        Swal.fire({
          title: "<h2><i>Last Order History</i></h2><br>",
          html: info,
          confirmButtonText: "Close",
        });
      },

      add_with_image(all) {

        var photo = "";
        var price = "";
        var list = "";

        let _this = this;

        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3 && element.v4 == this.v4)
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
            if(this.v4 != "")
              list += (item_product.k4 + ': ' + item_product.v4) + "\n";
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
          var k1, k2, k3, k4;
          k1 = this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1;
          k2 = this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2;
          k3 = this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3;
          k4 = this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4;

          if(k1 !== '')
            list += (this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1) + ': ' + this.product.variation1_value.join(', ') + "\n";
          if(k2 !== '')
            list += (this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2) + ': ' + this.product.variation2_value.join(', ') + "\n";
          if(k3 !== '')
            list += (this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3) + ': ' + this.product.variation3_value.join(', ') + "\n";
          if(k4 !== '')
            list += (this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4) + ': ' + this.product.variation4_value.join(', ') + "\n";

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

        // add phased out information
        if((this.product.phased_out_cnt > 0 && this.phased == 1) || (this.product.phased_out_cnt > 0 && all == 'all'))
        {
          list += "\n";
          list += "Phased-out Variants:\n";
          list += this.product.phased_out_text.split("<br/>").join("\n");
        }

        list.replace(/\n+$/, "");

        if(price == null)
          price = this.product.srp_quoted !== 0 ?  this.product.srp_quoted : this.product.srp;
          // price = this.product.srp !== 0 ?  this.product.srp : this.product.srp_quoted;

          // 20221004 price only srp
          price = this.product.srp !== 0 ?  this.product.srp : 0;

          var sn = 0;

          for (let i = 0; i < this.items.length; i++) {
              if (this.items[i].id * 1> sn) {
                sn = this.items[i].id * 1;
              }
          }
          sn = sn * 1 + 1;

          items = [];

          item = {
              is_checked:false,
              is_edit: false,
              id: sn,
              sn: sn,
              confirm: "N",
              confirm_text: "Not Yet Confirmed",
              brand:this.product.brand,
              brand_other:"",
              photo1:photo != '' ? photo : '',
              photo2:this.product.photo2 != '' ? this.product.photo2 : '',
              photo3:this.product.photo3 != '' ? this.product.photo3 : '',
              code:this.product.code,
              brief:list,
            listing:"",
              qty:"",
              backup_qty:"",
              unit:"",
              srp:price,
              date_needed:"",
              pid: this.product.id,
              status:"",
              shipping_way:"",
              shipping_number:"",
              notes:[],
              v1:this.v1,
              v2:this.v2,
              v3:this.v3,
              v4:this.v4,
              btn2:"1",
              which_pool:"Stock Pool",
            as_sample:"Yes",
            };

            items.push(item);

            var token = localStorage.getItem("token");
              var form_Data = new FormData();

              form_Data.append("jwt", token);
              form_Data.append("od_id", this.id);
              form_Data.append("block", JSON.stringify(items));

              form_Data.append("access7", this.access7);

              axios({
                method: "post",
                headers: {
                  "Content-Type": "multipart/form-data",
                },
                url: "api/order_taiwan_p1_item_insert",
                data: form_Data,
              })
                .then(function(response) {
                  //handle success

                  _this.getRecord();
                  alert('Add Successfully');
    
                })
                .catch(function(error) {
              
    
                });

        
      },

      add_without_image(all) {

        var photo = "";
        var price = "";
        var list = "";

        let _this = this;

        let item_product = this.shallowCopy(
          this.product.product.find((element) => element.v1 == this.v1 && element.v2 == this.v2 && element.v3 == this.v3 && element.v4 == this.v4)
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
            if(this.v4 != "")
              list += (item_product.k4 + ': ' + item_product.v4) + "\n";
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
          var k1, k2, k3, k4;
          k1 = this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1;
          k2 = this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2;
          k3 = this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3;
          k4 = this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4;

          if(k1 !== '')
            list += (this.product.variation1 === "custom" ? this.product.variation1_custom : this.product.variation1) + ': ' + this.product.variation1_value.join(', ') + "\n";
          if(k2 !== '')
            list += (this.product.variation2 === "custom" ? this.product.variation2_custom : this.product.variation2) + ': ' + this.product.variation2_value.join(', ') + "\n";
          if(k3 !== '')
            list += (this.product.variation3 === "custom" ? this.product.variation3_custom : this.product.variation3) + ': ' + this.product.variation3_value.join(', ') + "\n";
          if(k4 !== '')
            list += (this.product.variation4 === "custom" ? this.product.variation4_custom : this.product.variation4) + ': ' + this.product.variation4_value.join(', ') + "\n";

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

        // 20221004 price only srp
        price = this.product.srp !== 0 ?  this.product.srp : 0;

        for(var i=0; i<this.specification.length; i++)
        {
            if(this.specification[i].k1 !== '')
              list += this.specification[i].k1 + ': ' + this.specification[i].v1 + "\n";
            if(this.specification[i].k2 !== '')
              list += this.specification[i].k2 + ': ' + this.specification[i].v2 + "\n";
        }

        // add phased out information
        if((this.product.phased_out_cnt > 0 && this.phased == 1) || (this.product.phased_out_cnt > 0 && all == 'all'))
        {
          list += "\n";
          list += "Phased-out Variants:\n";
          list += this.product.phased_out_text.split("<br/>").join("\n");
        }

        list.replace(/\n+$/, "");

        var sn = 0;
        
        for (let i = 0; i < this.items.length; i++) {
          if (this.items[i].id * 1 > sn) {
            sn = this.items[i].id * 1;
          }
      }

        sn = sn * 1 + 1;

        items = [];

        item = {
            is_checked:false,
            is_edit: false,
            id: sn,
            sn: sn,
            confirm: "N",
            confirm_text: "Not Yet Confirmed",
            brand:this.product.brand,
            brand_other:"",
            photo1:'',
            photo2:'',
            photo3:'',
            code:this.product.code,
            brief:list,
            listing:"",
            qty:"",
            backup_qty:"",
            unit:"",
            srp:price,
            date_needed:"",
            pid: this.product.id,
            v1: this.v1,
            v2: this.v2,
            v3: this.v3,
            v4: this.v4,
            shipping_way:"",
              shipping_number:"",
            status:"",
            btn2:"1",
            notes:[],
            which_pool:"Stock Pool",
            as_sample:"Yes",
          };

          items.push(item);

            var token = localStorage.getItem("token");
              var form_Data = new FormData();

              form_Data.append("jwt", token);
              form_Data.append("od_id", this.id);
              form_Data.append("block", JSON.stringify(items));

              form_Data.append("access7", this.access7);

              axios({
                method: "post",
                headers: {
                  "Content-Type": "multipart/form-data",
                },
                url: "api/order_taiwan_p1_item_insert",
                data: form_Data,
              })
                .then(function(response) {
                  //handle success

                  _this.getRecord();
                  alert('Add Successfully');
    
                })
                .catch(function(error) {
              
    
                });

        
      },

      close_single: function() {
        $('#modal_product_display').modal('toggle');
    },

      close_single_quo_master: function() {
        $('#modal_quotation_list').modal('toggle');
    },
      
      set_up_specification() {
        let k1 = '';
        let k2 = '';
 
        let v1 = '';
        let v2 = '';

        this.specification = [];
 
        for(var i=0; i < this.attributes.length; i++)
          {
            if(this.attributes[i].type != "custom")
            {
              if(k1 == "")
              {
                k1 = this.attributes[i].category;
                v1 = this.attributes[i].value.join(' ');
              }else if(k1 !== "" && k2 == "")
              {
                k2 = this.attributes[i].category;
                v2 = this.attributes[i].value.join(' ');
    
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
       if(k1 !== "" && k2 == "")
       {
         k2 = '';
         v2 = '';
     
         obj = {k1: k1, v1: v1, k2: k2, v2: v2};
         this.specification.push(obj);
       
       }
     },

      chunk: function(arr, size) {
        var newArr = [];
        for (var i=0; i<arr.length; i+=size) {
          newArr.push(arr.slice(i, i+size));
        }
        this.groupedItems  = newArr;
      },

      chunk_replacement: function(arr, size) {
        var newArr = [];
        for (var i=0; i<arr.length; i+=size) {
          newArr.push(arr.slice(i, i+size));
        }
        this.groupedItems_replacement  = newArr;
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

      
      quotation_mgt: function() {
        $('#modal_quotation_list').modal('toggle');
   
      },

      btnEditClick: async function(product) {
        let _this = this;
        let data = {};
        if(product.sub_category == '10020000')
         {
            data = await this.get_records_prod(product.id);

            this.product = data[0];

            this.product_set = this.product['product_set'];

            for(var i = 0; i < this.product_set.length; i++)
            {
              this.product_set[i]['special_infomation'] = this.product_set[i].record[0]['special_information'][0].lv3[0]
              this.product_set[i]['specification'] = [];
              this.set_up_specification_set(this.product_set[i]);
            }

          
         }
         else
          this.product = product;

        $('#modal_product_display').modal('toggle');
        //this.product = product;
        this.url = (this.product.photo1 !== '' && this.product.photo1 !== undefined) ? this.img_url + this.product.photo1 : '';

        this.special_infomation = product.special_information[0].lv3[0];
        this.attributes = product.attribute_list;

        this.related_product  = product.related_product;
        this.replacement_product = product.replacement_product;

        this.quoted_price = product.quoted_price;
        this.price = product.price;

        this.v1 = "";
        this.v2 = "";
        this.v3 = "";
        this.v4 = "";

        this.out = product.out;
        this.out_cnt = product.phased_out_cnt;

        this.last_order_name = product.last_order_name;
        this.last_order_at = product.last_order_at;
        this.last_order_url = product.last_order_url;

        this.chunk(this.related_product, 4);
        this.chunk_replacement(this.replacement_product, 4);

        this.set_up_variants();
        this.set_up_specification();
      },
      
    filter_apply_new: function() {
      let _this = this;

      _this.pg = 1;

      const params = {
        sd: _this.fil_id,
        c: _this.fil_code,
        t: JSON.stringify(_this.fil_tag),
        b: _this.fil_brand,
        g: _this.fil_category,
        k: _this.fil_k,
        of1: _this.of1,
        ofd1: _this.ofd1,
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


      
    filter_apply_new_quo: function() {
      let _this = this;

      this.product_page_quo = 1;

      const params = {

                fc : _this.fil_project_category,
                fpc: _this.fil_project_creator,
                fpt: _this.fil_creator,
       
                key: _this.fil_keyword,
                kind: _this.fil_kind,

                op1: _this.od_opt1,
                od1: _this.od_ord1,
                op2: _this.od_opt2,
                od2: _this.od_ord2,

                page: _this.product_page_quo,
                  size: 10,

                all: 'all',
            };

      
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/quotation_mgt', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.receive_records_quo_master = res.data;

                  if(_this.receive_records_quo_master.length > 0)
                    _this.quotation_total = _this.receive_records_quo_master[0].cnt;

                  if(_this.pg_quo !== 0)
                    { 
                      _this.page_quo = _this.pg_quo;
                      _this.setPagesQuo();
                    }
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
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

      product_catalog() {
        this.sort_me(0);
        $('#modal_product_catalog').modal('toggle');
        $("#tag01").selectpicker("refresh");
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
      
    do_msg_delete(message_id, task_id) {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("message_id", message_id);

      var element = this.items.find((element) => element.id == task_id);
      form_Data.append("item", JSON.stringify(element));

      form_Data.append("od_id", this.id);
      form_Data.append("od_name", this.od_name);
      form_Data.append("project_name", this.project_name);
      form_Data.append("serial_name", this.serial_name);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/order_taiwan_sample_p1_delete_message",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          if (response.data["ret"] != 0) {
             _this.reload_task(task_id);
          }
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

      msg_delete(message_id, task_id) {
    
        let _this = this;
        Swal.fire({
          title: "Delete",
          text: "Are you sure to delete?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.value) {
            _this.do_msg_delete(message_id, task_id); // <--- submit form programmatically
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });
      },

      got_it_message(message_id, task_id) {
        let _this = this;
  
        _this.submit = true;
        var form_Data = new FormData();

  
        form_Data.append('message_id', message_id);
   
  
        const token = sessionStorage.getItem('token');
  
        axios({
          method: 'post',
          headers: {
            'Content-Type': 'multipart/form-data',
            Authorization: `Bearer ${token}`
          },
          url: 'api/order_taiwan_p1_got_it',
          data: form_Data
        })
          .then(function (response) {
            _this.reload_task(task_id);
         
          })
          .catch(function (response) {
            //handle error
            console.log(response)
          }).finally(function () {  });
      },

      item_delete(item) {

        let id = item.id;

        if(item.notes.length > 0)
        {
          Swal.fire({
            text: "User is not allowed to delete the item already with notes.",
            icon: "Info",
            confirmButtonText: "OK",
          });
          return;
        }
    
        let _this = this;
        Swal.fire({
          title: "Delete",
          text: "Are you sure to delete?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        }).then((result) => {
          if (result.value) {
            _this.do_item_delete(id); // <--- submit form programmatically
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });
      },

      do_item_delete(id) {
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
        form_Data.append("item_id", id);

        form_Data.append("access7", this.access7);
        form_Data.append("od_id", this.id);
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/order_taiwan_p1_delete_item",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            if (response.data["ret"] != 0) {
               _this.getRecord();
            }
          })
          .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: "error",
              confirmButtonText: "OK",
            });
          });
      },

      reload_task(task_id) {
        let _this = this;
        const params = {
          id: task_id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/order_taiwan_sample_p1_message", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(
            (res) => {
              _this.items.find((element) => element.id == task_id).notes = res.data;
            },
            (err) => {
              alert(err.response);
            }
          )
          .finally(() => {});
      },

      comment_clear(task_id) {
        this.current_task_id = task_id;
        this.arrTask= [];
        Vue.set(this.arrTask, 0, "");
        this.$refs["comment_task_" + task_id][0].value = "";
  
        this.reload_task(task_id);
      },

      comment_create(task_id) {
        this.current_task_id = task_id;
  
        let _this = this;
  
        var comment = this.$refs["comment_task_" + task_id][0];
  
        if (comment.value.trim() == "") {
          Swal.fire({
            text: "Please enter comment!",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }
  
        _this.submit = true;
        var form_Data = new FormData();
  
        form_Data.append("task_id", task_id);
        form_Data.append("message", comment.value.trim());

        var element = this.items.find((element) => element.id == task_id);
        form_Data.append("item", JSON.stringify(element));
        
        form_Data.append("access1", this.access1);
        form_Data.append("access2", this.access2);
        form_Data.append("access3", this.access3);
        form_Data.append("access4", this.access4);
        form_Data.append("access5", this.access5);
        form_Data.append("access6", this.access6);
        form_Data.append("access7", this.access7);

        form_Data.append("od_id", this.id);
        form_Data.append("od_name", this.od_name);
        form_Data.append("project_name", this.project_name);
        form_Data.append("serial_name", this.serial_name);

        form_Data.append("page", 1);

        const token = sessionStorage.getItem("token");
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${token}`,
          },
          url: "api/order_taiwan_sample_p1_message",
          data: form_Data,
        })
          .then(function(response) {
            if (response.data["batch_id"] != 0) {
              _this.comment_upload(task_id, response.data["batch_id"]);
            } 
  
          })
          .catch(function(response) {
            //handle error
            console.log(response);
          })
          .finally(function() {
            _this.comment_clear(task_id);
          });
      },
  
      comment_upload(task_id, batch_id) {
        this.current_task_id = task_id;
  
        this.canSub = false;
        var myArr = this.arrTask[task_id];
        var _this = this;

        if(myArr == undefined)
          return;
  
        myArr.forEach((element, index) => {
          var config = {
            headers: { "Content-Type": "multipart/form-data" },
            
          };
          var data = myArr[index];
          var myForm = new FormData();
          myForm.append("batch_type", "od_message");
          myForm.append("batch_id", batch_id);
          myForm.append("file", data);
  
          axios
            .post("api/uploadFile_gcp", myForm, config)
            .then(function(res) {
              if (res.data.code == 0) {
    
              } else {
                alert(JSON.stringify(res.data));
              }

              _this.reload_task(task_id);
            })
            .catch(function(err) {
              console.log(err);
            });
        });
  
        this.taskCanSub[task_id] = true;
      },

      deleteTaskFile(task_id, index) {
        this.current_task_id = task_id;
  
        this.arrTask[task_id].splice(index, 1);
        var fileTarget = this.$refs["file_task_" + task_id];
        fileTarget.value = "";
        Vue.set(this.arrTask, 0, "");
      },

      changeTaskFile(task_id) {
        this.current_task_id = task_id;
  
        var arr = this.arrTask[task_id];
        if (typeof arr === "undefined" || arr.length == 0)
          this.arrTask[task_id] = [];
  
        var fileTarget = this.$refs["file_task_" + task_id][0];
  
        for (i = 0; i < fileTarget.files.length; i++) {
          // remove duplicate
          if (
            this.arrTask[task_id].indexOf(fileTarget.files[i]) == -1 ||
            this.arrTask[task_id].length == 0
          ) {
            var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
            this.arrTask[task_id].push(fileItem);
            Vue.set(this.arrTask, 0, "");
          } else {
            fileTarget.value = "";
          }
        }
      },

      taskItems(task_id) {
        var arr = this.arrTask[task_id];
        return arr;
      },

        editItem(item) {
            item.is_edit = true;
        },

        cancelItem(item) {
            item.is_edit = false;
            this.getRecord();
        },

        confirmItem(item) {
          // qty and backup_qty must be numeric or space
          if((item.qty.trim() != "" && /^-?\d+$/.test(item.qty.trim()) == false) || (item.backup_qty.trim() != "" && /^-?\d+$/.test(item.backup_qty.trim()) == false)) {
            Swal.fire({
              text: 'Valid value for columns "Qty Needed" and "Backup Qty" is blank or numbers. It cannot include texts.',
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }
            item.is_edit = false;
            let _this = this;

            items = [];

            item = {
                id: item.id,
                sn: item.sn,
                confirm: item.confirm,
                confirm_text: "",
                brand:item.brand,
                brand_other:item.brand_other.toUpperCase().trim(),
                photo1:item.photo1,
                photo2:item.photo2,
                photo3:item.photo3,
                code:item.code,
                brief:item.brief,
                listing:item.listing,
                qty:item.qty,
                backup_qty:item.backup_qty,
                unit:item.unit,
                srp:item.srp,
                date_needed:item.date_needed,
                pid:item.pid,
                v1:item.v1,
                v2:item.v2,
                v3:item.v3,
                v4:item.v4,
                ps_var:item.ps_var,
                status:item.status,
                notes:[],
                shipping_way:item.shipping_way,
                shipping_number:item.shipping_number,
                which_pool:item.which_pool,
                as_sample:item.as_sample,
              };

              items.push(item);

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            form_Data.append("jwt", token);
            form_Data.append("od_id", this.id);
            form_Data.append("block", JSON.stringify(items));

            form_Data.append("access7", this.access7);

            var file = document.getElementById('photo_' + item.id + '_1');
            if(file) {
              let f = file.files[0];
              if(typeof f !== 'undefined') 
                form_Data.append('photo_1', f);
            }

            var file = document.getElementById('photo_' + item.id + '_2');
            if(file) {
              let f = file.files[0];
              if(typeof f !== 'undefined') 
                form_Data.append('photo_2', f);
            }

            var file = document.getElementById('photo_' + item.id + '_3');
            if(file) {
              let f = file.files[0];
              if(typeof f !== 'undefined') 
                form_Data.append('photo_3', f);
            }

            axios({
              method: "post",
              headers: {
                "Content-Type": "multipart/form-data",
              },
              url: "api/order_taiwan_sample_p1_item_update",
              data: form_Data,
            })
              .then(function(response) {
                //handle success

                _this.getRecord();
  
              })
              .catch(function(error) {
            
  
              });
        },

        addItem() {
          let _this = this;
            var sn = 0;

            for (let i = 0; i < this.items.length; i++) {
                if (this.items[i].id * 1 > sn) {
                  sn = this.items[i].id * 1;
                }
            }
            sn = sn * 1 + 1;

            items = [];

            item = {
                is_checked:false,
                is_edit: false,
                id: sn,
                sn: sn,
                confirm: "N",
                confirm_text: "Not Yet Confirmed",
                brand:"",
                brand_other:"",
                photo1:"",
                photo2:"",
                photo3:"",
                code:"",
                brief:"",
                listing:"",
                qty:"",
                backup_qty:"",
                unit:"",
                srp:"",
                date_needed:"",
                pid:0,
                v1:"",
                v2:"",
                v3:"",
                v4:"",
                shipping_way:"",
              shipping_number:"",
                status:"",
                notes:[],
                which_pool:"Stock Pool",
                as_sample:"Yes",
              };

              items.push(item);

              var token = localStorage.getItem("token");
              var form_Data = new FormData();

              form_Data.append("jwt", token);
              form_Data.append("od_id", this.id);
              form_Data.append("block", JSON.stringify(items));
              form_Data.append("access7", this.access7);

              axios({
                method: "post",
                headers: {
                  "Content-Type": "multipart/form-data",
                },
                url: "api/order_taiwan_p1_item_insert",
                data: form_Data,
              })
                .then(function(response) {
                  //handle success

                  _this.getRecord();
    
                })
                .catch(function(error) {
              
    
                });
        },
    
        ShipwayRead(item)
        {
          if(!item.is_edit)
            return true;
          else
            return false;
        },
  
        ShipwayWrite(item)
        {
          if(item.is_edit && this.access1 == true)
            return true;
          else
            return false;
        },
        
      getAccess: function() {
        var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append('jwt', token);
  
        axios({
            method: 'get',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/order_taiwan_p1_access_control',
            data: form_Data
        })
        .then(function(response) {
            //handle success
            _this.access1 = response.data.access1;
            _this.access2 = response.data.access2;
            _this.access3 = response.data.access3;
            _this.access4 = response.data.access4;
            _this.access5 = response.data.access5;
            _this.access6 = response.data.access6;
            _this.access7 = response.data.access7;
  
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
            _this.uid = response.data.user_id;
  
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
      
      print_page() {
        this.show_title = false;
        app.$forceUpdate();
        window.print();
      },


      clear_photo(item, num) {

        if (num === 1) {
          item.photo1 = "";
          document.getElementById('photo_' + item.id + '_1').value = "";
        }
        if (num === 2) {
          item.photo2 = "";
          document.getElementById('photo_' + item.id + '_2').value = "";
        }
        if (num === 3) {
          item.photo3 = "";
          document.getElementById('photo_' + item.id + '_3').value = "";
        }
  
        
      },
      
      onFileChangeImage(e, item, num) {
        const file = e.target.files[0];

  
        if (num === 1) {
          item.photo1 = URL.createObjectURL(file);
        }
        if (num === 2) {
          item.photo2 = URL.createObjectURL(file);
        }
        if (num === 3) {
          item.photo3 = URL.createObjectURL(file);
        }
    
          
      },

      getRecord: function() {
        let _this = this;

        if(_this.id == 0)
          return;
  
        const params = {
          pg: 1,
          id: _this.id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/order_taiwan_p1", {
              params,
              headers: { Authorization: `Bearer ${token}` },
            })
          .then(function(response) {
            console.log(response.data);
            _this.items = response.data;
            if (_this.receive_records.length > 0) {
            
              
            }
          })
          .catch(function(error) {
            console.log(error);
          });
  
      },

      page_up: async function(fromIndex, eid) {
        var toIndex = fromIndex - 1;
  
        if (toIndex < 0) 
          return;

        var element = this.items.find(({ id }) => id === eid);
        var selement = this.items[toIndex];

        selement.sn = [element.sn, element.sn = selement.sn][0];

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
        form_Data.append("item_id", element.id);
        form_Data.append("item_sn", element.sn);
        form_Data.append("sitem_id", selement.id);
        form_Data.append("sitem_sn", selement.sn);

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_p1_swap_item',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        this.getRecord();
      },

      page_down: async function(fromIndex, eid) {
        var toIndex = fromIndex + 1;

        if (toIndex > this.items.length - 1) 
          return;
  
        var element = this.items.find(({ id }) => id === eid);
        var selement = this.items[toIndex];

        selement.sn = [element.sn, element.sn = selement.sn][0];

        var token = localStorage.getItem("token");
        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
        form_Data.append("item_id", element.id);
        form_Data.append("item_sn", element.sn);
        form_Data.append("sitem_id", selement.id);
        form_Data.append("sitem_sn", selement.sn);

        let res = await axios({
          method: 'post',
          url: 'api/order_taiwan_p1_swap_item',
          data: form_Data,
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        
        this.getRecord();
      },


      search() {
          this.filter_apply();
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

      do_print_me(item, text){
        let _this = this;
        var cls = '.print_area_' + item.id;

        let _item_id = item.id;
 
        html2canvas(document.querySelector(cls), { proxy: "html2canvasproxy", useCORS: false, logging: true, allowTaint: true}).then(canvas => {
          //document.body.appendChild(canvas)
          let dataurl = canvas.toDataURL();
          let data = { image: dataurl, item_id: item.id, text: text };
          this.loading = true;

          axios
            .post("api/order_taiwan_p1_snapshot", data, {
              headers: {
              "Content-Type": "application/json"
              }
            }).then(function(response) {
              console.log(response.data);
              _this.reload_task(_item_id);
                 
            })
            .catch(function(error) {
              console.log(error);
            });
          });
        },

      print_me(item) {
        let _this = this;
        // sweet alert with input
        Swal.fire({
          title: "Take Snapshot of Product",
          text: 'Input description for the snapshot:',
          input: "text",
          inputAttributes: {
            autocapitalize: "off",
          },
          showCancelButton: true,
          confirmButtonText: "OK",
          showLoaderOnConfirm: true,
          preConfirm: (login) => {
            _this.do_print_me(item, login);
          },
        });
      
      },

      export_petty() {
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
  
  
        axios({
          method: "post",
          url: "api/order_taiwan_sample_p1_print",
          data: form_Data,
          responseType: "blob",
        })
            .then(function(response) {
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;
                 
                    link.setAttribute('download', 'Order Taiwan Report.xlsx');
                 
                  document.body.appendChild(link);
                  link.click();
  
            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });
      },

      
      
      toggle_normal: function(pid, id) {

        this.get_records(pid, id);
        $('#modal_product_display_simple').modal('toggle');

      },

      p_close_single: function() {
        $('#modal_product_display_simple').modal('toggle');
      },

      change_url: function(url) {
        this.p_url = url;
    },

    p_PhaseOutAlert(){
      hl = "";
      for(var i = 0; i < this.p_phased_out_text.length; i++)
      {
        hl += "(" + Number(i+1) + ") " + this.p_phased_out_text[i] + "<br/>";
      }

      Swal.fire({
        title: 'Phased Out Variants:',
        html: hl,
        confirmButtonText: 'OK',
        });
      
    },

    p_change_v(){
      let item_product = this.shallowCopy(
        this.p_product.product.find((element) => element.v1 == this.p_v1 && element.v2 == this.p_v2 && element.v3 == this.p_v3 && element.v4 == this.p_v4)
      )

      if(item_product.id != undefined)
      {
        if(item_product.photo != "")
          this.p_url = this.img_url + item_product.photo;
        else
          this.p_url = "";
        this.p_price_ntd = "NTD " + Number(item_product.price_ntd).toLocaleString();
        this.p_price = "PHP " + Number(item_product.price).toLocaleString();
        this.p_quoted_price = "PHP " + Number(item_product.quoted_price).toLocaleString();
        this.p_phased = item_product.enabled == 0 ? 1 : 0;

        this.p_out = item_product.enabled == 1 ? "" : "Y";
        this.p_out_cnt = 0;

        if(this.p_product['out'] == 'Y')
        {
            this.p_out = "Y";
            this.p_out_cnt = 0;
        }

        this.p_item_product = item_product;
      }
      else
      {
        this.p_url = this.img_url + this.p_product['photo1'];
        this.p_price_ntd = this.p_product['price_ntd'];
        this.p_price = this.p_product['price'];
        this.p_quoted_price = this.p_product['quoted_price'];
        this.p_phased = 0;

        this.p_out = this.p_product['out'];
        this.p_out_cnt = this.p_product['phased_out_cnt'];

        this.p_item_product = {};
      }

    },


    save_single: function() {

      let _this = this;
      var form_Data = new FormData();

      if(this.p_item_product.id == undefined)
      {
        Swal.fire({
          text: "Please choose an option for each attribute.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      if(this.p_out == 'Y')
      {
        Swal.fire({
          text: "What you chose is a phased-out variation!! Please choose another variation.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      var v1 = this.p_item_product.v1;
      var v2 = this.p_item_product.v2;
      var v3 = this.p_item_product.v3;
      var v4 = this.p_item_product.v4;

      form_Data.append('id', this.p_id);
      form_Data.append('v1', v1);
      form_Data.append('v2', v2);
      form_Data.append('v3', v3);
      form_Data.append('v4', v4);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/order_taiwan_p1_normal_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          $('#modal_product_display_simple').modal('toggle');
          _this.getRecord();

        })
        .catch(function(error) {
      

        });
      },

      get_records_prod: async function(id) {
        let _this = this;
        let record = {};
  
        if(id === -1)
            return {};
  
        const params = {
          id: id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        let res = await axios
          .get("api/product_display_code", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });
  
          return res.data;
          
      },

      
    get_records: function(id, item_id) {
      let _this = this;

      this.p_id = item_id;

      if(id === -1)
          return;

      const params = {
        id: id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/product_display_code", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.p_record = response.data;

          _this.p_product = _this.p_record[0];

          _this.p_category = _this.p_record[0]['category'];
          _this.p_sub_category = _this.p_record[0]['sub_category'];
          _this.p_sub_category_name = _this.p_record[0]['sub_category_name'];

          _this.p_sub_cateory_item = _this.p_record[0]['sub_category_item'];

          _this.p_tags = _this.p_record[0]['tags'];

          _this.p_special_infomation = _this.p_record[0]['special_information'][0].lv3[0];
          _this.p_accessory_infomation = _this.p_record[0]['accessory_information'];

          _this.p_pid = _this.p_record[0]['id'];
          _this.p_brand = _this.p_record[0]['brand'];
          _this.p_code = _this.p_record[0]['code'];
          _this.p_price_ntd = _this.p_record[0]['price_ntd'];
          _this.p_price = _this.p_record[0]['price'];
          _this.p_price_quoted = _this.p_record[0]['price_quoted'];
          _this.p_price_ntd_change = _this.p_record[0]['price_ntd_change'];
          _this.p_price_change = _this.p_record[0]['price_change'];
          _this.p_price_ntd_org = _this.p_record[0]['price_ntd_org'];
          _this.p_price_org = _this.p_record[0]['price_org'];
          _this.p_description = _this.p_record[0]['description'];
          _this.p_notes = _this.p_record[0]['notes'];
          _this.p_accessory_mode = _this.p_record[0]['accessory_mode'];
          _this.p_variation_mode = _this.p_record[0]['variation_mode'];

          _this.p_quoted_price = _this.p_record[0]['quoted_price'];
          _this.p_quoted_price_org = _this.p_record[0]['quoted_price_org'];
          _this.p_quoted_price_change = _this.p_record[0]['quoted_price_change'];
          _this.p_moq = _this.p_record[0]['moq'];

          _this.p_currency = _this.p_record[0]['currency'];

          _this.p_str_price_ntd_change = _this.p_record[0]['str_price_ntd_change'];
          _this.p_str_price_change = _this.p_record[0]['str_price_change'];
          _this.p_str_quoted_price_change = _this.p_record[0]['str_quoted_price_change'];

          _this.p_sheet_url = 'product_spec_sheet?sd=' + _this.p_record[0]['id'];

          _this.p_out = _this.p_record[0]['out'];
          _this.p_out_cnt = _this.p_record[0]['phased_out_cnt'];
          _this.p_phased_out_text = _this.p_record[0]['phased_out_text'];

          _this.last_order_name = _this.p_record[0]['last_order_name'];
          _this.last_order_at = _this.p_record[0]['last_order_at'];
          _this.last_order_url = _this.p_record[0]['last_order_url'];

          //var select_items = _this.record[0]['tags'].split(',');

          // if(_this.category === '10000000')
          //   $('#tag01').selectpicker('val', select_items);
          // if(_this.category === '20000000')
          //   $('#tag02').selectpicker('val', select_items);
          
          
          // if(_this.variation_mode == 1)
          //     $("#variation_mode").bootstrapToggle("on");
          // if(_this.accessory_mode == 1)
          //     $("#accessory_mode").bootstrapToggle("on");

          if(_this.p_record[0]['photo1'].trim() !== '')
              _this.p_url1 = _this.p_baseURL + _this.p_record[0]['photo1'];
          if(_this.p_record[0]['photo2'].trim() !== '')
              _this.p_url2 = _this.p_baseURL + _this.p_record[0]['photo2'];
          if(_this.p_record[0]['photo3'].trim() !== '')
              _this.p_url3 = _this.p_baseURL + _this.p_record[0]['photo3'];

          _this.p_url = _this.p_url1;

          _this.p_attributes = JSON.parse(_this.p_record[0]['attributes']);
          _this.p_variation_product = _this.p_record[0]['product'];

          _this.p_variation1_text = _this.p_record[0]['variation1_text'];
          _this.p_variation2_text = _this.p_record[0]['variation2_text'];
          _this.p_variation3_text = _this.p_record[0]['variation3_text'];

          _this.p_variation1_value = _this.p_record[0]['variation1_value'];
          _this.p_variation2_value = _this.p_record[0]['variation2_value'];
          _this.p_variation3_value = _this.p_record[0]['variation3_value'];

          _this.p_related_product = _this.p_record[0]['related_product'];
          _this.p_chunk(_this.p_related_product, 4);

          _this.replacement_product = _this.p_record[0]['replacement_product'];
          _this.chunk_replacement(_this.replacement_product, 4);

          _this.p_variation1 = _this.p_record[0]['variation1'];
          _this.p_variation2 = _this.p_record[0]['variation2'];
          _this.p_variation3 = _this.p_record[0]['variation3'];

          _this.p_variation1_custom = _this.p_record[0]['variation1_custom'];
          _this.p_variation2_custom = _this.p_record[0]['variation2_custom'];
          _this.p_variation3_custom = _this.p_record[0]['variation3_custom'];
          
          _this.p_set_up_variants();
          _this.p_set_up_specification();

          _this.p_v1 = "";
          _this.p_v2 = "";
          _this.p_v3 = "";

          _this.p_change_v();

          _this.p_edit_mode = true;
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    p_chunk: function(arr, size) {
      var newArr = [];
      for (var i=0; i<arr.length; i+=size) {
        newArr.push(arr.slice(i, i+size));
      }
      this.groupedItems  = newArr;
    },

    
    getSingleProduct : function(id) {


      let _this = this;


      const params = {
        sd: id,
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
            _this.replacement_product = _this.product.replacement_product;

            _this.quoted_price = _this.product.quoted_price;
            _this.price = _this.product.price;

            _this.v1 = "";
            _this.v2 = "";
            _this.v3 = "";
            _this.v4 = "";
    
            _this.chunk(_this.related_product, 4);
            _this.chunk_replacement(_this.replacement_product, 4);
    
            _this.set_up_variants();
            _this.set_up_specification();
          }

    
        })
        .catch(function(error) {
          console.log(error);
        });

    },

    p_set_up_variants() {
      for(var i=0; i<this.p_variation1_value.length; i++)
      {
        $('#variation1_value').tagsinput('add', this.p_variation1_value[i]);
      }
      for(var i=0; i<this.p_variation2_value.length; i++)
      {
        $('#variation2_value').tagsinput('add', this.p_variation2_value[i]);
      }
      for(var i=0; i<this.p_variation3_value.length; i++)
      {
        $('#variation3_value').tagsinput('add', this.p_variation3_value[i]);
      }

      
    },

    
    p_set_up_specification() {
      let k1 = '';
      let k2 = '';

      let v1 = '';
      let v2 = '';

     for(var i=0; i < this.p_special_infomation.length; i++)
     {
       if(this.p_special_infomation[i].value != "")
       {
         if(k1 == "")
         {
           k1 = this.p_special_infomation[i].category;
           v1 = this.p_special_infomation[i].value;
         }else if(k1 !== "" && k2 == "")
         {
           k2 = this.p_special_infomation[i].category;
           v2 = this.p_special_infomation[i].value;

           obj = {k1: k1, v1: v1, k2: k2, v2: v2};
           this.p_specification.push(obj);
           k1  = '';
           k2  = '';
           v1  = '';
           v2  = '';
         }
       }
     }

     if(k1 == "" && this.p_record[0]['moq'] !== "")
     {
       k1 = 'MOQ';
       v1 = this.p_record[0]['moq'];
     }else if(k1 !== "" && k2 == "" && this.p_record[0]['moq'] !== "")
     {
       k2 = 'MOQ';
       v2 = this.p_record[0]['moq'];
   
       obj = {k1: k1, v1: v1, k2: k2, v2: v2};
       this.p_specification.push(obj);
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
       this.p_specification.push(obj);
     
     }
   },

   change_url_set: function(set, uid) {
    if(uid == 1)
      set.url = set.url1;
    if(uid == 2)
      set.url = set.url2;
    if(uid == 3)
      set.url = set.url3;
},

PhaseOutAlert_set(phased_out_text){
  hl = "";
  for(var i = 0; i < phased_out_text.length; i++)
  {
    hl += "(" + Number(i+1) + ") " + phased_out_text[i] + "<br/>";
  }

  Swal.fire({
    title: 'Phased Out Variants:',
    html: hl,
    confirmButtonText: 'OK',
    });
  
},

change_v_set(set){
  let item_product = this.shallowCopy(
    set.variation_product.find((element) => element.v1 == set.v1 && element.v2 == set.v2 && element.v3 == set.v3 && element.v4 == set.v4)
  )

  if(item_product.id != undefined)
  {
    if(item_product.photo != "")
    set.url = this.img_url + item_product.photo;
    else
    set.url = "";
    set.price_ntd = item_product.currency + " " + Number(item_product.price_ntd).toLocaleString();
    set.price = "PHP " + Number(item_product.price).toLocaleString();
    set.quoted_price = "PHP " + Number(item_product.quoted_price).toLocaleString();

    set.str_price_ntd_change = (item_product.price_ntd_change != "" ? "(" + item_product.price_ntd_change + ")" : "");
    set.str_price_change = (item_product.price_change != "" ? "(" + item_product.price_change + ")" : "");
    set.str_quoted_price_change = (item_product.quoted_price_change != "" ? "(" + item_product.quoted_price_change + ")" : "");

    set.phased_out = (item_product.enabled == 0 ? "F" : "");

    set.sheet_url = 'product_spec_sheet?sd=' + set.pid + '&d=' + item_product.id;

    set.out = item_product.enabled == 1 ? "" : "Y";
    set.out_cnt = 0;

    if(set.record[0]['out'] == 'Y')
    {
      set.out = "Y";
      set.out_cnt = 0;
    }

    set.last_order_name = item_product.last_order_name;
      set.last_order_at = item_product.last_order_at;
      set.last_order_url = item_product.last_order_url;
      set.last_have_spec = false;
  }
  else
  {
    set.url = set.url1;
    set.price_ntd = set.record[0]['price_ntd'];
    set.price = set.record[0]['price'];
    set.quoted_price = set.record[0]['quoted_price'];

    set.str_price_ntd_change = set.record[0]['str_price_ntd_change'];
    set.str_price_change = set.record[0]['str_price_change'];
    set.str_quoted_price_change = set.record[0]['str_quoted_price_change'];

    set.phased_out = "";

    set.sheet_url = 'product_spec_sheet?sd=' + set.pid;

    set.out = set.record[0]['out'];
    set.out_cnt = set.record[0]['phased_out_cnt'];

    set.last_order_name = "";
      set.last_order_at = "";
      set.last_order_url = "";
      set.last_have_spec = true;
  }

  this.check_all_set();

},

check_all_set(){
  let change = true;
  let price_ntd = 0;
  let price = 0;
  let quoted_price = 0;

  for(var i=0; i < this.product_set.length; i++){
    let item_product = this.shallowCopy(
      this.product_set[i].variation_product.find((element) => element.v1 == this.product_set[i].v1 && element.v2 == this.product_set[i].v2 && element.v3 == this.product_set[i].v3 && element.v4 == this.product_set[i].v4)
    )

    if(item_product.id != undefined)
    {
      price_ntd += item_product.price_ntd * 1;
      price += item_product.price * 1;
      quoted_price += item_product.quoted_price * 1;
    }
    else
      change = false;
  }

  if(change)
  {
    //this.price_ntd = price_ntd;
    this.product.price = "PHP " + Number(price).toLocaleString();;
        this.product.quoted_price = "PHP " + Number(quoted_price).toLocaleString();
  }
  else
      {
        this.product.price = this.price;
          this.product.quoted_price = this.quoted_price;
      }
},


add_with_image_set_select(all) {
  let change = true;
  let price_ntd = 0;
  let price = 0;
  let quoted_price = 0;
  let qty = 0;
  let srp = 0;

  let list = "";
  let ps_var = "";

  let sets = [];
  let _this = this;

  for(var i=0; i < this.product_set.length; i++){
    let item_product = this.shallowCopy(
      this.product_set[i].variation_product.find((element) => element.v1 == this.product_set[i].v1 && element.v2 == this.product_set[i].v2 && element.v3 == this.product_set[i].v3 && element.v4 == this.product_set[i].v4)
    )

    var list_g = "";

    for(var j=0; j<this.product_set[i].specification.length; j++)
    {
        if(this.product_set[i].specification[j].k1 !== '')
          list_g += this.product_set[i].specification[j].k1 + ': ' + this.product_set[i].specification[j].v1 + "\n";
        if(this.product_set[i].specification[j].k2 !== '')
          list_g += this.product_set[i].specification[j].k2 + ': ' + this.product_set[i].specification[j].v2 + "\n";
    }

    // add phased out information
    if((this.product_set[i].phased_out_cnt > 0 && this.phased == 1) || (this.product_set[i].phased_out_cnt > 0 && all == 'all'))
    {
      list_g += "\n";
      list_g += "Phased-out Variants:\n";
      list_g += this.product_set[i].phased_out_text.split("<br/>").join("\n");
    }
    

    if(item_product.id != undefined)
    {
      if(item_product.photo != "")
        this.product_set[i].photo = item_product.photo;

      price_ntd += item_product.price_ntd * 1;
      price += item_product.price * 1;
      quoted_price += item_product.quoted_price * 1;
      qty += this.product_set[i].qty * 1;

      srp = quoted_price != 0 ? quoted_price : price;

      ps_var = ('id: ' + this.product_set[i].id) + "\n";

      if(item_product.v1 != ""){
        list += (item_product.k1 + ': ' + item_product.v1) + "\n";
        ps_var += (item_product.k1 + ': ' + item_product.v1) + "\n";
      }
      if(item_product.v2 != ""){
        list += (item_product.k2 + ': ' + item_product.v2) + "\n";
        ps_var += (item_product.k2 + ': ' + item_product.v2) + "\n";
      }
      if(item_product.v3 != ""){
        list += (item_product.k3 + ': ' + item_product.v3) + "\n";
        ps_var += (item_product.k3 + ': ' + item_product.v3) + "\n";
      }
      if(item_product.v4 != ""){
        list += (item_product.k4 + ': ' + item_product.v4) + "\n";
        ps_var += (item_product.k4 + ': ' + item_product.v4) + "\n";
      }

      sets.push(ps_var);

      list += list_g;

      list += "\n";

    }
    else
      change = false;
  }

  if(change)
  {
    list.replace(/\n+$/, "");

    var sn = 0;

    for (let i = 0; i < this.items.length; i++) {
      if (this.items[i].id * 1 > sn) {
        sn = this.items[i].id * 1;
      }
    }

    sn = sn * 1 + 1;

    items = [];

    item = {
      is_checked:false,
      is_edit: false,
      id: sn,
      sn: sn,
      confirm: "N",
      confirm_text: "Not Yet Confirmed",
      brand:"",
      brand_other:"",
      photo1: this.product_set[0] != undefined ? this.product_set[0].photo : "",
      photo2: this.product_set[1] != undefined ? this.product_set[1].photo : "",
      photo3: this.product_set[2] != undefined ? this.product_set[2].photo : "",
      code: this.product.code,
      brief:list,
      listing:"",
      qty:"",
      backup_qty:"",
      unit:"",
      srp:srp,
      date_needed:"",
      pid: this.product.id,
      status:"",
      shipping_way:"",
      shipping_number:"",
      notes:[],
      v1: "",
      v2: "",
      v3: "",
      v4: "",
      btn2:"1",
      which_pool:"Stock Pool",
      as_sample:"Yes",
      ps_var : sets,
    };

  }
  else{
    alert('Please choose option for each attribute of every sub-product');
    return;
  }

  items.push(item);
  var token = localStorage.getItem("token");
  var form_Data = new FormData();

  form_Data.append("jwt", token);
  form_Data.append("od_id", this.id);
  form_Data.append("block", JSON.stringify(items));

  form_Data.append("access7", this.access7);

  axios({
    method: "post",
    headers: {
      "Content-Type": "multipart/form-data",
    },
    url: "api/order_taiwan_p1_item_insert",
    data: form_Data,
  })
    .then(function(response) {
      //handle success

      _this.getRecord();
      alert('Add Successfully');

    })
    .catch(function(error) {
      alert(error);

    });
},


add_without_image_set_select(all) {
  let change = true;
  let price_ntd = 0;
  let price = 0;
  let quoted_price = 0;
  let qty = 0;
  let srp = 0;

  let list = "";
  let ps_var = "";

  let sets = [];
  let _this = this;

  for(var i=0; i < this.product_set.length; i++){
    let item_product = this.shallowCopy(
      this.product_set[i].variation_product.find((element) => element.v1 == this.product_set[i].v1 && element.v2 == this.product_set[i].v2 && element.v3 == this.product_set[i].v3 && element.v4 == this.product_set[i].v4)
    )

    var list_g = "";

    for(var j=0; j<this.product_set[i].specification.length; j++)
    {
        if(this.product_set[i].specification[j].k1 !== '')
          list_g += this.product_set[i].specification[j].k1 + ': ' + this.product_set[i].specification[j].v1 + "\n";
        if(this.product_set[i].specification[j].k2 !== '')
          list_g += this.product_set[i].specification[j].k2 + ': ' + this.product_set[i].specification[j].v2 + "\n";
    }

    // add phased out information
    if((this.product_set[i].phased_out_cnt > 0 && this.phased == 1) || (this.product_set[i].phased_out_cnt > 0 && all == 'all'))
    {
      list_g += "\n";
      list_g += "Phased-out Variants:\n";
      list_g += this.product_set[i].phased_out_text.split("<br/>").join("\n");
    }

    if(item_product.id != undefined)
    {
      price_ntd += item_product.price_ntd * 1;
      price += item_product.price * 1;
      quoted_price += item_product.quoted_price * 1;
      qty += this.product_set[i].qty * 1;

      srp = quoted_price != 0 ? quoted_price : price;

      ps_var = ('id: ' + this.product_set[i].id) + "\n";

      if(item_product.v1 != ""){
        list += (item_product.k1 + ': ' + item_product.v1) + "\n";
        ps_var += (item_product.k1 + ': ' + item_product.v1) + "\n";
      }
      if(item_product.v2 != ""){
        list += (item_product.k2 + ': ' + item_product.v2) + "\n";
        ps_var += (item_product.k2 + ': ' + item_product.v2) + "\n";
      }
      if(item_product.v3 != ""){
        list += (item_product.k3 + ': ' + item_product.v3) + "\n";
        ps_var += (item_product.k3 + ': ' + item_product.v3) + "\n";
      }
      if(item_product.v4 != ""){
        list += (item_product.k4 + ': ' + item_product.v4) + "\n";
        ps_var += (item_product.k4 + ': ' + item_product.v4) + "\n";
      }

      sets.push(ps_var);

      list += list_g;

      list += "\n";

    }
    else
      change = false;
  }

  if(change)
  {

    list.replace(/\n+$/, "");

    var sn = 0;

    for (let i = 0; i < this.items.length; i++) {
      if (this.items[i].id * 1 > sn) {
        sn = this.items[i].id * 1;
      }
    }

    sn = sn * 1 + 1;

    items = [];

    item = {
      is_checked:false,
      is_edit: false,
      id: sn,
      sn: sn,
      confirm: "N",
      confirm_text: "Not Yet Confirmed",
      brand:"",
      brand_other:"",
      photo1:'',
      photo2:'',
      photo3:'',
      code: this.product.code,
      brief:list,
      listing:"",
      qty:"",
      backup_qty:"",
      unit:"",
      srp:srp,
      date_needed:"",
      pid: this.product.id,
      v1: "",
      v2: "",
      v3: "",
      v4: "",
      shipping_way:"",
      shipping_number:"",
      status:"",
      notes:[],
      btn2:"1",
      which_pool:"Stock Pool",
      as_sample:"Yes",
      ps_var : sets,
    };

  }
  else{
    alert('Please choose option for each attribute of every sub-product');
    return;
  }

  items.push(item);
  var token = localStorage.getItem("token");
  var form_Data = new FormData();

  form_Data.append("jwt", token);
  form_Data.append("od_id", this.id);
  form_Data.append("block", JSON.stringify(items));

  form_Data.append("access7", this.access7);

  axios({
    method: "post",
    headers: {
      "Content-Type": "multipart/form-data",
    },
    url: "api/order_taiwan_p1_item_insert",
    data: form_Data,
  })
    .then(function(response) {
      //handle success

      _this.getRecord();
      alert('Add Successfully');

    })
    .catch(function(error) {
      alert(error);

    });

},


set_up_specification_set(set) {
  let k1 = '';
  let k2 = '';

  let v1 = '';
  let v2 = '';

 for(var i=0; i < set.special_infomation.length; i++)
 {
   if(set.special_infomation[i].value != "")
   {
     if(k1 == "")
     {
       k1 = set.special_infomation[i].category;
       v1 = set.special_infomation[i].value;
     }else if(k1 !== "" && k2 == "")
     {
       k2 = set.special_infomation[i].category;
       v2 = set.special_infomation[i].value;

       obj = {k1: k1, v1: v1, k2: k2, v2: v2};
       set.specification.push(obj);
       k1  = '';
       k2  = '';
       v1  = '';
       v2  = '';
     }
   }
 }

 if(k1 == "" && set.record[0]['moq'] !== "")
 {
   k1 = 'MOQ';
   v1 = set.record[0]['moq'];
 }else if(k1 !== "" && k2 == "" && set.record[0]['moq'] !== "")
 {
   k2 = 'MOQ';
   v2 = set.record[0]['moq'];

   obj = {k1: k1, v1: v1, k2: k2, v2: v2};
   set.specification.push(obj);
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
   set.specification.push(obj);
 
 }
},

add_with_image_set(set, all) {

var photo = "";
var photo2 = "";
var photo3 = "";

var price = "";
var list = "";

var srp = 0;

let _this = this;

let item_product = this.shallowCopy(
  set.product.find((element) => element.v1 == set.v1 && element.v2 == set.v2 && element.v3 == set.v3 && element.v4 == set.v4)
)

if(set.product.length > 0 && item_product.id == undefined && all != 'all') {
  alert('Please choose an option for each attribute');
  return;
}

if(item_product.id != undefined)
{
  if(item_product.photo != "")
    photo = item_product.photo;

    photo2 = set.photo2;
    photo3 = set.photo3;

    // price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
    price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
    srp =  Number(item_product.price);
    if(set.v1 != "")
      list += (item_product.k1 + ': ' + item_product.v1) + "\n";
    if(set.v2 != "")
      list += (item_product.k2 + ': ' + item_product.v2) + "\n";
    if(set.v3 != "")
      list += (item_product.k3 + ': ' + item_product.v3) + "\n";
    if(set.v4 != "")
      list += (item_product.k4 + ': ' + item_product.v4) + "\n";
}
else
{
  photo = set.photo1;
  photo2 = set.photo2;
  photo3 = set.photo3;
  // price = set.price_org !== null ? set.price_org : set.quoted_price_org;
  price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
  srp = Number(set.price_org);
  list = "";
}

if(all == 'all')
{
  list = "";
  var k1, k2, k3;
  k1 = set.variation1 === "custom" ? set.variation1_custom : set.variation1;
  k2 = set.variation2 === "custom" ? set.variation2_custom : set.variation2;
  k3 = set.variation3 === "custom" ? set.variation3_custom : set.variation3;
  k4 = set.variation4 === "custom" ? set.variation4_custom : set.variation4;

  if(k1 !== '')
    list += set.variation1 === "custom" ? set.variation1_custom + ': ' + set.variation1_value.join(', ') + "\n" : set.variation1 + ': ' + set.variation1_value.join(', ') + "\n";
  if(k2 !== '')
    list += set.variation2 === "custom" ? set.variation2_custom + ': ' + set.variation2_value.join(', ') + "\n" : set.variation2 + ': ' + set.variation2_value.join(', ') + "\n";
  if(k3 !== '')
    list += set.variation3 === "custom" ? set.variation3_custom + ': ' + set.variation3_value.join(', ') + "\n" : set.variation3 + ': ' + set.variation3_value.join(', ') + "\n";
  if(k4 !== '')
    list += set.variation4 === "custom" ? set.variation4_custom + ': ' + set.variation4_value.join(', ') + "\n" : set.variation4 + ': ' + set.variation4_value.join(', ') + "\n";

  photo = set.photo1;
  photo2 = set.photo2;
  photo3 = set.photo3;

  if(set.srp !== null || set.srp_quoted !== null)
    price = set.srp_quoted !== null ? set.srp_quoted : set.srp;

  srp = set.srp;
    //price = set.srp !== null ? set.srp : set.srp_quoted;

  if(price == null)
    //price = set.price_org !== null ? set.price_org : set.quoted_price_org;
    price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
    
}

for(var i=0; i<set.specification.length; i++)
{
    if(set.specification[i].k1 !== '')
      list += set.specification[i].k1 + ': ' + set.specification[i].v1 + "\n";
    if(set.specification[i].k2 !== '')
      list += set.specification[i].k2 + ': ' + set.specification[i].v2 + "\n";
}

// add phased out information
if((set.phased_out_cnt > 0 && set.phased == 1) || (set.phased_out_cnt > 0 && all == 'all'))
{
  // if string or is string array
  if(typeof set.phased_out_text === 'string' || set.phased_out_text instanceof String)
  {
    list += "\n";
    list += "Phased-out Variants:\n";
    list += set.phased_out_text.split("<br/>").join("\n");
  }
  else if(Array.isArray(set.phased_out_text))
  {
    for(var i=0; i<set.phased_out_text.length; i++)
    {
      list += "\n";
      list += "Phased-out Variants:\n";
      list += set.phased_out_text[i].split("<br/>").join("\n");
    }
  }
  else
  {
    list += "\n";
    list += "Phased-out Variants:\n";
    list += set.phased_out_text.split("<br/>").join("\n");
  }
  
}

if(price == null)
  price = set.srp_quoted !== 0 ?  set.srp_quoted : set.srp;
  // price = set.srp !== 0 ?  set.srp : set.srp_quoted;

if(srp == null)
  srp = 0;


  var sn = 0;

  for (let i = 0; i < this.items.length; i++) {
      if (this.items[i].id * 1> sn) {
        sn = this.items[i].id * 1;
      }
  }
  sn = sn * 1 + 1;

  items = [];


list.replace(/\n+$/, "");
sn = sn + 1;




item = {
  is_checked:false,
  is_edit: false,
  id: sn,
  sn: sn,
  confirm: "N",
  confirm_text: "Not Yet Confirmed",
  brand:set.brand,
  brand_other:"",
  photo1:photo != '' ? photo : '',
  photo2:set.photo2 != '' ? set.photo2 : '',
  photo3:set.photo3 != '' ? set.photo3 : '',
  code:set.code,
  brief:list,
  listing:"",
  qty:set.qty,
  backup_qty:"",
  unit:"",
  srp:price,
  date_needed:"",
  pid: set.id,
  status:"",
  shipping_way:"",
  shipping_number:"",
  notes:[],
  v1: all == 'all' ? '' : set.v1,
    v2: all == 'all' ? '' : set.v2,
    v3: all == 'all' ? '' : set.v3,
    v4: all == 'all' ? '' : set.v4,
  btn2:"1",
  which_pool:"Stock Pool",
            as_sample:"Yes",
};

items.push(item);

var token = localStorage.getItem("token");
  var form_Data = new FormData();

  form_Data.append("jwt", token);
  form_Data.append("od_id", this.id);
  form_Data.append("block", JSON.stringify(items));

  form_Data.append("access7", this.access7);

  axios({
    method: "post",
    headers: {
      "Content-Type": "multipart/form-data",
    },
    url: "api/order_taiwan_p1_item_insert",
    data: form_Data,
  })
    .then(function(response) {
      //handle success

      _this.getRecord();
      alert('Add Successfully');

    })
    .catch(function(error) {
      alert(error);

    });


},

add_without_image_set(set, all) {

var photo = "";
var price = "";
var list = "";

var srp = 0;

let _this = this;

let item_product = this.shallowCopy(
  set.product.find((element) => element.v1 == set.v1 && element.v2 == set.v2 && element.v3 == set.v3 && element.v4 == set.v4)
)

if(set.product.length > 0 && item_product.id == undefined && all != 'all') {
  alert('Please choose an option for each attribute');
  return;
}

if(item_product.id != undefined)
{
  if(item_product.photo != "")
    photo = item_product.photo;
    //price = Number(item_product.price) != 0 ? Number(item_product.price) : Number(item_product.quoted_price);
    price = Number(item_product.quoted_price) != 0 ? Number(item_product.quoted_price) : Number(item_product.price);
    srp =  Number(item_product.price);
    if(set.v1 != "")
      list += (item_product.k1 + ': ' + item_product.v1) + "\n";
    if(set.v2 != "")
      list += (item_product.k2 + ': ' + item_product.v2) + "\n";
    if(set.v3 != "")
      list += (item_product.k3 + ': ' + item_product.v3) + "\n";
    if(set.v4 != "")
      list += (item_product.k4 + ': ' + item_product.v4) + "\n";
}
else
{
  photo = set.photo1;
  //price = set.price_org !== null ? set.price_org : set.quoted_price_org;
  price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
  srp = Number(set.price_org);
  list = "";
}

if(all == 'all')
{
  list = "";
  var k1, k2, k3;
  k1 = set.variation1 === "custom" ? set.variation1_custom : set.variation1;
  k2 = set.variation2 === "custom" ? set.variation2_custom : set.variation2;
  k3 = set.variation3 === "custom" ? set.variation3_custom : set.variation3;
  k4 = set.variation4 === "custom" ? set.variation4_custom : set.variation4;

  if(k1 !== '')
    list += set.variation1 === "custom" ? set.variation1_custom + ': ' + set.variation1_value.join(', ') + "\n" : set.variation1 + ': ' + set.variation1_value.join(', ') + "\n";
  if(k2 !== '')
    list += set.variation2 === "custom" ? set.variation2_custom + ': ' + set.variation2_value.join(', ') + "\n" : set.variation2 + ': ' + set.variation2_value.join(', ') + "\n";
  if(k3 !== '')
    list += set.variation3 === "custom" ? set.variation3_custom + ': ' + set.variation3_value.join(', ') + "\n" : set.variation3 + ': ' + set.variation3_value.join(', ') + "\n";
  if(k4 !== '')
    list += set.variation4 === "custom" ? set.variation4_custom + ': ' + set.variation4_value.join(', ') + "\n" : set.variation4 + ': ' + set.variation4_value.join(', ') + "\n";

  photo = set.photo1;

  if(set.srp !== null || set.srp_quoted !== null)
    price = set.srp_quoted !== null ? set.srp_quoted : set.srp;

    //price = set.srp !== null ? set.srp : set.srp_quoted;

  if(price == null)
    //price = set.price_org !== null ? set.price_org : set.quoted_price_org;
    price = set.quoted_price_org !== null ? set.quoted_price_org : set.price_org;
    
    
  srp = set.srp;
}

if(price == null)
  price = set.srp_quoted !== 0 ?  set.srp_quoted : set.srp;
  //price = set.srp !== 0 ?  set.srp : set.srp_quoted;

if(srp == null)
  srp = 0;

for(var i=0; i<set.specification.length; i++)
{
    if(set.specification[i].k1 !== '')
      list += set.specification[i].k1 + ': ' + set.specification[i].v1 + "\n";
    if(set.specification[i].k2 !== '')
      list += set.specification[i].k2 + ': ' + set.specification[i].v2 + "\n";
}

// add phased out information
if((set.phased_out_cnt > 0 && set.phased == 1) || (set.phased_out_cnt > 0 && all == 'all'))
{
  // if string or is string array
  if(typeof set.phased_out_text === 'string' || set.phased_out_text instanceof String)
  {
    list += "\n";
    list += "Phased-out Variants:\n";
    list += set.phased_out_text.split("<br/>").join("\n");
  }
  else if(Array.isArray(set.phased_out_text))
  {
    for(var i=0; i<set.phased_out_text.length; i++)
    {
      list += "\n";
      list += "Phased-out Variants:\n";
      list += set.phased_out_text[i].split("<br/>").join("\n");
    }
  }
  else
  {
    list += "\n";
    list += "Phased-out Variants:\n";
    list += set.phased_out_text.split("<br/>").join("\n");
  }
}

list.replace(/\n+$/, "");

var sn = 0;

for (let i = 0; i < this.items.length; i++) {
  if (this.items[i].id * 1 > sn) {
    sn = this.items[i].id * 1;
  }
}

sn = sn * 1 + 1;

items = [];

item = {
    is_checked:false,
    is_edit: false,
    id: sn,
    sn: sn,
    confirm: "N",
    confirm_text: "Not Yet Confirmed",
    brand:set.brand,
    brand_other:"",
    photo1:'',
    photo2:'',
    photo3:'',
    code:set.code,
    brief:list,
    listing:"",
    qty:set.qty,
    backup_qty:"",
    unit:"",
    srp:price,
    date_needed:"",
    pid: set.id,
    v1: all == 'all' ? '' : set.v1,
    v2: all == 'all' ? '' : set.v2,
    v3: all == 'all' ? '' : set.v3,
    v4: all == 'all' ? '' : set.v4,
    shipping_way:"",
      shipping_number:"",
    status:"",
    notes:[],
    btn2:"1",
    which_pool:"Stock Pool",
            as_sample:"Yes",
  };

  items.push(item);

    var token = localStorage.getItem("token");
      var form_Data = new FormData();

      form_Data.append("jwt", token);
      form_Data.append("od_id", this.id);
      form_Data.append("block", JSON.stringify(items));

      form_Data.append("access7", this.access7);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/order_taiwan_p1_item_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success

          _this.getRecord();
          alert('Add Successfully');

        })
        .catch(function(error) {
      

        });

},
    }
  
  });
  