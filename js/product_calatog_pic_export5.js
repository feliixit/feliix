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

    fil_amount_lower: "",
    fil_amount_upper: "",
    od_factor1: "",
    od_factor1_order: "",
    od_factor2: "",
    od_factor2_order: "",

    fil_id: "",
    fil_id_1: "",
    fil_code: "",
    fil_tag: [],
    fil_brand: "",
    fil_keyword: "",
    fil_category: "",
    fil_subcategory: "",

    // info
    name :"",
    title: "",
    is_manager: "",

    toggle: true,
    tag_group : [],

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
            case "d":
              _this.fil_id = tmp[1];
              break;
            case "d1":
              _this.fil_id_1 = tmp[1];
              break;
            case "c":
              _this.fil_category = tmp[1];
              break;
            case "sc":
              _this.fil_subcategory = tmp[1];
              break;
              case "pg":
                _this.pg = tmp[1];
                break;
              case "page":
                _this.page = tmp[1];
                break;
              case "size":
                _this.perPage = tmp[1];
                break;
            default:
              console.log(`Too many args`);
          }
          //_this.proof_id = tmp[1];
        }
      });
    }

    this.get_records();
    // this.getUserName();
    // this.get_brands();
    // this.getTagGroup();
    // this.getProductControl();
  },

  computed: {

    // async displayedPosts()  {
    //   // if(this.pg == 0)
    //   await  this.filter_apply();

    //   this.setPages();
    //   return this.paginate(this.receive_records);
    // },

    // show_ntd : function() {
    //   // if(this.name.toLowerCase() ==='dereck' || this.name.toLowerCase() ==='ariel lin' || this.name.toLowerCase() ==='kuan' || this.name.toLowerCase() ==='testmanager')
    //   if(this.cost_lighting == true  || this.cost_furniture == true)
    //    return true;
    //   else
    //   return false;
    // }

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
        // this.pages = [];
        // let numberOfPages = Math.ceil(this.total / this.perPage);
  
        // if (numberOfPages == 1) this.page = 1;
        // if (this.page < 1) this.page = 1;
        // for (let index = 1; index <= numberOfPages; index++) {
        //   this.pages.push(index);
        // }
      },
  
      paginate: function(posts) {
        console.log("paginate");
        // if (this.page < 1) this.page = 1;
        // if (this.page > this.pages.length) this.page = this.pages.length;
  
        // let tenPages = Math.floor((this.page - 1) / 10);
        // if(tenPages < 0)
        //   tenPages = 0;
        // this.pages_10 = [];
        // let from = tenPages * 10;
        // let to = (tenPages + 1) * 10;
        
        // this.pages_10 = this.pages.slice(from, to);
  
        return this.receive_records;
      },

    search() {
      this.filter_apply();
    },

    filter_remove: function() {
      this.fil_id = '';
      this.fil_id_1 = '';
      this.fil_category = '';
      this.fil_subcategory = '';
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
        "product_catalog_pic_export?" +
        "d=" +
        _this.fil_id +
        "&d1=" +
        _this.fil_id_1 +
        "&c=" +
        _this.fil_category +
        "&sc=" +
        _this.fil_subcategory +
        "&pg=" +
        _this.page +
        "&page=" +
        _this.page +
        "&size=" +
        _this.perPage;
    },


    filter_apply: async function() {
      let _this = this;



      // if(_this.page < 1) _this.page = 1;
      // if (_this.page > _this.pages.length) _this.page = _this.pages.length;

      // this.page = 1;

      // window.location.href =
      //   "product_catalog_pic_export?" +
      //   "d=" +
      //   _this.fil_id +
      //   "&d1=" +
      //   _this.fil_id_1 +
      //   "&c=" +
      //   _this.fil_category +
      //   "&sc=" +
      //   _this.fil_subcategory +
      //   "&pg=" +
      //   _this.page +
      //   "&page=" +
      //   _this.page +
      //   "&size=" +
      //   _this.perPage;
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

    get_records:  function()  {
        let _this = this;

      const params = {
        d: _this.fil_id,
        d1: _this.fil_id_1,
        c: _this.fil_category,
        sc: _this.fil_subcategory,
        page: _this.page,
        size: _this.perPage,
      };

      let token = localStorage.getItem("accessToken");

      this.total = 0;

      return  axios
        .get("api/product_calatog_pic_export5", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          let res = response.data;
          if(res.length > 0) 
          {
            _this.receive_records = response.data;
            _this.total = _this.receive_records[0].cnt;
          }

          if(_this.pg !== 0)
          { 
            _this.page = _this.pg;
          }

          if(_this.total == 0)
            return;

          // Wait for the DOM to update
          return _this.$nextTick().then(() => {
            _this.do_print_me(_this.receive_records);
          });
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
        title: "Duplicate",
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

  do_print_me(items) {
    let _this = this;
    let promises = []; // Array to hold promises for each html2canvas call
    let data = { images: [] }; // Object to hold all images

    items.forEach(item => {
      var cls = '.tb_product_list_' + item.id;
      const element = document.querySelector(cls); // Get the element

      if (element) { // Check if the element exists
        promises.push(
          html2canvas(element, { proxy: "html2canvasproxy", useCORS: false, logging: true, allowTaint: true }).then(canvas => {
            let dataurl = canvas.toDataURL();
            data.images.push({ image: dataurl, item_id: item.id }); // Add each image to the data object
          })
        );
      } else {
        console.warn(`Element not found for selector: ${cls}`); // Log a warning if the element is not found
      }
    });

    Promise.all(promises).then(() => {
      this.loading = true;

      axios
        .post("api/product_calatog_pic_export_snapshot", data, {
          headers: {
            "Content-Type": "application/json"
          },
          responseType: 'blob' // Set response type to blob for file download
        }).then(function(response) {
          // Create a URL for the blob
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', 'snapshots.zip'); // Set the file name for download
          document.body.appendChild(link);
          link.click(); // Trigger the download
          link.remove(); // Clean up the link element
          window.URL.revokeObjectURL(url); // Release the blob URL
        })
        .catch(function(error) {
          console.log(error);
        });
    });
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
