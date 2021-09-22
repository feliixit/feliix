var app = new Vue({
  el: "#app",
  data: {
    
    submit: false,

    baseURL: "https://storage.cloud.google.com/feliiximg/",


    //
    receive_records: [],

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
    

  },

  computed: {

    displayedPosts() {
      if(this.pg == 0)
        this.filter_apply_new();

      this.setPages();
      return this.paginate(this.receive_records);
    },

  },

  mounted() {
  
  },

  watch: {
    
  },

  methods: {

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
      this.fil_amount_lower = '';
      this.fil_amount_upper = '';
   
    
      document.getElementById("start").value = "";
      document.getElementById("end").value = "";

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
    
      document.getElementById("start").value = "";
      document.getElementById("end").value = "";

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
      _this.page = 1;

      window.location.href =
        "product_calatog?" +
        "d=" +
        _this.fil_amount_lower +
        "&e=" +
        _this.fil_amount_upper +
          
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


      window.location.href =
        "product_calatog?" +
        "d=" +
        _this.fil_amount_lower +
        "&e=" +
        _this.fil_amount_upper +
          
        "&pg=" +
        _this.page +
        "&page=" +
        _this.page +
        "&size=" +
        _this.perPage;
    },

    get_records: function() {
        let _this = this;

      const params = {
        d: _this.fil_start_date,
        e: _this.fil_end_date,
        p: _this.fil_creator,
        c: _this.fil_category,
      
        page: _this.page,
        size: _this.perPage,
      };

      let token = localStorage.getItem("accessToken");

      this.total = 0;

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
            _this.receive_records = response.data;
            _this.total = _this.receive_records[0].cnt;
          }

          if(_this.pg !== 0)
          { 
            _this.page = _this.pg;
            _this.setPages();
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
        window.open("edit_product?id=" + id);
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
                text: "Finished deleting selected record(s) " + response.data.message,
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
