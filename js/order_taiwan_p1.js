var app = new Vue({
    el: "#app",
    data: {
      submit: false,

      // id of the quotation
      l_id:0,
      id:0,

      //img_url: 'https://storage.cloud.google.com/feliiximg/',

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

        //
        fil_project_category:'',
        fil_project_creator: '',
        fil_kind: '',
        fil_creator: '',
        fil_keyword: '',

        users: [],
        creators: [],

        items: [],
        uid: 0,

        message: "",
        arrTask: [],
        taskCanSub: [],
        taskFinish: [],
        current_task_id: 0,
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

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/order_taiwan_p1_delete_message",
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

      item_delete(item) {

        let id = item.id;
    
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
          .get("api/order_taiwan_p1_message", {
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
  
        const token = sessionStorage.getItem("token");
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${token}`,
          },
          url: "api/order_taiwan_p1_message",
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
            item.is_edit = false;

            items = [];

            item = {
                id: item.id,
                sn: item.sn,
                confirm: item.confirm,
                confirm_text: "",
                brand:item.brand,
                brand_other:item.brand_other,
                photo1:item.photo1,
                photo2:item.photo2,
                photo3:item.photo3,
                code:item.code,
                brief:item.brief,
                listing:item.listing,
                qty:item.qty,
                srp:item.srp,
                date_needed:item.date_needed,
                status:item.status,
                notes:[]
              };

              items.push(item);

            var token = localStorage.getItem("token");
            var form_Data = new FormData();

            form_Data.append("jwt", token);
            form_Data.append("od_id", this.id);
            form_Data.append("block", JSON.stringify(items));

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
              url: "api/order_taiwan_p1_item_update",
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
                if (this.items[i].id > sn) {
                  sn = this.items[i].id;
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
                srp:"",
                date_needed:"",
                status:"",
                notes:[]
              };

              items.push(item);

              var token = localStorage.getItem("token");
              var form_Data = new FormData();

              form_Data.append("jwt", token);
              form_Data.append("od_id", this.id);
              form_Data.append("block", JSON.stringify(items));

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

        this.items.splice(fromIndex, 1);
        this.items.splice(toIndex, 0, element);
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
        
        this.items.splice(fromIndex, 1);
        this.items.splice(toIndex, 0, element);
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


      print_me(item) {

        var cls = '.print_area_' + item.id;
        let _this = this;
        let _item_id = item.id;
 
        html2canvas(document.querySelector(cls), { proxy: "html2canvasproxy", useCORS: false, logging: true, allowTaint: true}).then(canvas => {
          //document.body.appendChild(canvas)
          let dataurl = canvas.toDataURL();
          let data = { image: dataurl, item_id: item.id };
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

    }
  
  });
  