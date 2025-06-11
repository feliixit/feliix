var app = new Vue({
  el: "#app",
  data: {
    
    submit: false,

    baseURL: "https://storage.googleapis.com/feliiximg/",


    //
    receive_records: [],
    brands: [],

    proof_id: 0,

    // paging
    page: 1,
    pg:0,
    //perPage: 10,
    pages: [],

    pages_10: [],

    inventory: [
      {name: '10', id: 10},
      {name: '25', id: 25},
      {name: '50', id: 50},
      {name: '100', id: 100},
      {name: 'All', id: 10000}
    ],

    perPage: 10,

    pc: 0,
    p: 0,

    // info
    name :"",
    title: "",
    is_manager: "",

    toggle: true,

    mode : "write",

    item: {},
    reserved : {},

    show_title : true,
    fil_id : '',
    fil_id_1 : '',
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
            case "sd":
              _this.fil_id = tmp[1];
              break;
            case "d":
              _this.fil_id_1 = tmp[1];
              break;
   
            default:
              console.log(`Too many args`);
          }
          //_this.proof_id = tmp[1];
        }
      });
    }

    this.get_records();
    this.getUserName();
    this.get_brands();
    this.getProductControl();
  },

  computed: {

    displayedPosts() {
      if(this.pg == 0)
        this.filter_apply();

      this.setPages();
      return this.paginate(this.receive_records);
    },

    show_ntd : function() {
      if(this.name.toLowerCase() ==='dereck' || this.name.toLowerCase() ==='ariel lin' || this.name.toLowerCase() ==='kuan' || this.name.toLowerCase() ==='testmanager')
       return true;
      else
      return false;
    }

  },

  mounted() {
  
  },

  watch: {
    
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
    
    toggle_price : function() {
      this.toggle = !this.toggle;
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

    pre_page: function(){
        let tenPages = Math.floor((this.page - 1) / 10) + 1;
  
          this.page = parseInt(this.page) - 10;
          if(this.page < 1)
            this.page = 1;
   
          this.pages_10 = [];
  
          let from = tenPages * 10;
          let to = (tenPages + 1) * 10;
  
          this.pages_10 = this.pages.slice(from, to);
        
      },
  
      nex_page: function(){
        let tenPages = Math.floor((this.page - 1) / 10) + 1;
  
        this.page = parseInt(this.page) + 10;
        if(this.page > this.pages.length)
          this.page = this.pages.length;
  
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;
        let pages_10 = this.pages.slice(from, to);
  
        if(pages_10.length > 0)
          this.pages_10 = pages_10;
  
      },
  
      setPages() {
        console.log("setPages");
        this.pages = [];
        let numberOfPages = Math.ceil(this.total / this.perPage);
  
        if (numberOfPages == 1) this.page = 1;
        if (this.page < 1) this.page = 1;
        for (let index = 1; index <= numberOfPages; index++) {
          this.pages.push(index);
        }
      },
  
      paginate: function(posts) {
        console.log("paginate");
        if (this.page < 1) this.page = 1;
        if (this.page > this.pages.length) this.page = this.pages.length;
  
        let tenPages = Math.floor((this.page - 1) / 10);
        if(tenPages < 0)
          tenPages = 0;
        this.pages_10 = [];
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;
        
        this.pages_10 = this.pages.slice(from, to);
  
        return this.receive_records;
      },

    search() {
      this.filter_apply();
    },

    filter_remove: function() {
      this.fil_id = '';
      this.fil_id_1 = '';
      this.fil_category = '';
      this.fil_code = '';
      this.fil_tag = [];
      this.fil_brand = '';
      this.fil_keyword = '';
   
    
      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");

      //this.receive_records = [];

      //this.getRecords();
      this.filter_apply();
    },

    order_remove: function() {
 
      this.od_factor1 = '';
      this.od_factor1_order = '';
      this.od_factor2 = '';
      this.od_factor2_order = '';
    

      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");

      //this.receive_records = [];

      //this.getRecords();
      this.filter_apply();
    },

    filter_clear() {
      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");
    },

    order_clear() {
      document.getElementById("btn_sort").classList.remove("focus");
      document.getElementById("sort_dialog").classList.remove("show");
    },

    filter_apply_new: function() {
      let _this = this;

      if(_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;
     

      window.location.href =
        "product_catalog_code?" +
        "sd=" +
        _this.fil_id +
        "&d=" +
        _this.fil_id_1 +
        "&g=" +
        _this.fil_category +
        "&c=" +
        _this.fil_code +
        "&t=" +
        JSON.stringify(_this.fil_tag) +
        "&b=" +
        _this.fil_brand +
        "&k=" +
        _this.fil_keyword +
        "&of1=" +
        _this.od_factor1 +
        "&ofd1=" +
        _this.od_factor1_order +
        "&of2=" +
        _this.od_factor2 +
        "&ofd2=" +
        _this.od_factor2_order +
        "&pg=" +
        _this.page +
        "&page=" +
        _this.page +
        "&size=" +
        _this.perPage;
    },


    filter_apply: function() {
      let _this = this;

      if(_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;

      this.page = 1;

      window.location.href =
        "product_catalog_code?" +
        "d=" +
        _this.fil_id +
        "&d1=" +
        _this.fil_id_1 +
        "&g=" +
        _this.fil_category +
        "&c=" +
        _this.fil_code +
        "&t=" +
        JSON.stringify(_this.fil_tag) +
        "&b=" +
        _this.fil_brand +
        "&k=" +
        _this.fil_keyword +
        "&of1=" +
        _this.od_factor1 +
        "&ofd1=" +
        _this.od_factor1_order +
        "&of2=" +
        _this.od_factor2 +
        "&ofd2=" +
        _this.od_factor2_order +
        "&pg=" +
        _this.page +
        "&page=" +
        _this.page +
        "&size=" +
        _this.perPage;
    },

    clear_photo(item, num) {

      if (num === 1) {
        item.photo1 = "";
        var file = document.getElementById('photo_' + item.id + '_1');
        if(file) {
          file.value = "";
        }
      }
      if (num === 2) {
        item.photo2 = "";
        var file = document.getElementById('photo_' + item.id + '_2');
        if(file) {
          file.value = "";
        }
      }
      if (num === 3) {
        item.photo3 = "";
        var file = document.getElementById('photo_' + item.id + '_3');
        if(file) {
          file.value = "";
        }
      }
      if (num === 4) {
        item.photo4 = "";
        var file = document.getElementById('photo_' + item.id + '_4');
        if(file) {
          file.value = "";
        }
      }
      if (num === 5) {
        item.photo5 = "";
        var file = document.getElementById('photo_' + item.id + '_5');
        if(file) {
          file.value = "";
        }
      }
      if (num === 6) {
        item.photo6 = "";
        var file = document.getElementById('photo_' + item.id + '_6');
        if(file) {
          file.value = "";
        }
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
      if (num === 4) {
        item.photo4 = URL.createObjectURL(file);
      }
      if (num === 5) {
        item.photo5 = URL.createObjectURL(file);
      }
      if (num === 6) {
        item.photo6 = URL.createObjectURL(file);
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

    converTextToArray: function(text) {
      if(text == null || text == "") return [];
      // split by newline
      let lines = text.split("\n");
      let result = [];
      
      for(let i = 0;i < lines.length;i++) {
        if(lines[i].trim() == "") continue;
        // split by colon
        let parts = lines[i].split(":");

        // key is first part, value is second part
        let key = parts[0];
        let value = parts[1].split(",");

        // add to result
        result.push({category: key, value: value});

        }

        let attribute_list_by_two = [];
        let two_array = [];
        for(let i=0; i<result.length; i++)
        {
            if(i % 2 == 0)
            {
                two_array = [];
                two_array.push(result[i]);
            }
            else
            {
              two_array.push(result[i]);
                attribute_list_by_two.push(two_array);
            }
        }
        if(two_array.length == 1)
        {
          attribute_list_by_two.push(two_array);
        }

        return attribute_list_by_two;
      },

    print_page() {
      this.show_title = false;
      app.$forceUpdate();
      window.print();
    },
    
    confirmItem() {
      let _this = this;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();

      form_Data.append("jwt", token);
      form_Data.append("item", JSON.stringify(this.item));

      var file = document.getElementById('photo_' + this.item.id + '_1');
      if(file) {
        let f = file.files[0];
        if(typeof f !== 'undefined') 
          form_Data.append('photo_1', f);
      }

      var file = document.getElementById('photo_' + this.item.id + '_2');
      if(file) {
        let f = file.files[0];
        if(typeof f !== 'undefined') 
          form_Data.append('photo_2', f);
      }

      var file = document.getElementById('photo_' + this.item.id + '_3');
      if(file) {
        let f = file.files[0];
        if(typeof f !== 'undefined') 
          form_Data.append('photo_3', f);
      }

      var file = document.getElementById('photo_' + this.item.id + '_4');
      if(file) {
        let f = file.files[0];
        if(typeof f !== 'undefined') 
          form_Data.append('photo_4', f);
      }

      var file = document.getElementById('photo_' + this.item.id + '_5');
      if(file) {
        let f = file.files[0];
        if(typeof f !== 'undefined') 
          form_Data.append('photo_5', f);
      }

      var file = document.getElementById('photo_' + this.item.id + '_6');
      if(file) {
        let f = file.files[0];
        if(typeof f !== 'undefined') 
          form_Data.append('photo_6', f);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/product_spec_sheet_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success

          _this.get_records();
          _this.mode = '';

        })
        .catch(function(error) {
      

        });
  },


    get_records: function() {
        let _this = this;

      const params = {
        sd: _this.fil_id,
        d: _this.fil_id_1
     
      };

      let token = localStorage.getItem("accessToken");


      axios
        .get("api/product_spec_sheet", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          let res = response.data;
          if(res.length > 0) 
          {
            _this.receive_records = response.data;

            _this.item = JSON.parse(JSON.stringify(_this.receive_records[0]));
            _this.reserved = _this.item.reserved;
          }

    
        })
        .catch(function(error) {
          console.log(error);
        });
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

    btnEditClick: function(id) {
        window.open("edit_product_code?id=" + id);
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

    edit: function() {
      this.mode = 'write';
    },

    preview: function() {
      this.item.variation_array = this.converTextToArray(this.item.variation); 
      this.mode = '';
    },

    btnDelClick: function(id) {
      let _this = this;

      Swal.fire({
        title: "Delete",
        text: "Are you sure to delete?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          var form_Data = new FormData();
          var token = localStorage.getItem("token");

          form_Data.append("jwt", token);
          form_Data.append("id", id);
 
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${token}`,
            },
            url: "api/product_calatog_delete",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: "Finished deleting selected record<br>" + response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
              _this.get_records();

            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: response.data,
                icon: "info",
                confirmButtonText: "OK",
              });
            });
        } else {
          return;
        }
      });
  } ,

    btnDuplicateClick: function(id) {
      let _this = this;

      Swal.fire({
        title: "Delete",
        text: "Are you sure to duplicate?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          var form_Data = new FormData();
          var token = localStorage.getItem("token");

          form_Data.append("jwt", token);
          form_Data.append("id", id);
 
          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${token}`,
            },
            url: "api/product_calatog_duplicate",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                text: "Success duplicate selected record(s) " + response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });
              _this.get_records();

            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: response.data,
                icon: "info",
                confirmButtonText: "OK",
              });
            });
        } else {
          return;
        }
      });
  } ,



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
