var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    level1: [],
    level2: [],
    level3: [],
    level4: [],

    org_level3: [],
    
    editing: false,

    pid : 0,
    
    sn : 0,
    code : "",
    category : "",

    org_id : 0,

    lv1:"",
    lv1_item : {},

    lv2:"",
    lv2_item : {},

    lv3:"",
    lv3_item : {},

    view_detail: false,

    lv4: "",
    lv4_item : {},

    page: 1,
    perPage: 20,
    pages: [],
    pages_10: [],

    items: [],
    total : 0,
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

            case "page":
              _this.page = tmp[1];
              break;
            case "size":
              _this.perPage = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
        }
      });
    }

    this.getLevel1();
    this.getItems();
    
  },

  computed: {

  },

  mounted() {},

  watch: {
    
    department() {
      this.title = this.shallowCopy(
        this.position.find((element) => element.did == this.department)
      ).items;

    },
  },

  methods: {

    setPages () {
      console.log('setPages');
      this.pages = [];

      let numberOfPages = Math.ceil(this.total / this.perPage);

      // if(this.fil_keyword != '')
      //   numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if(numberOfPages == 1)
        this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }

      this.paginate(this.receive_records);
    },

    paginate: function (posts) {
      console.log('paginate');
      if(this.page < 1)
        this.page = 1;
      if(this.page > this.pages.length)
        this.page = this.pages.length;

        let tenPages = Math.floor((this.page - 1) / 10);
        if(tenPages < 0)
          tenPages = 0;
        this.pages_10 = [];
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;

        this.pages_10 = this.pages.slice(from, to);

      // if(this.fil_keyword != '')
      //   return this.receive_records.slice(from, to);
      // else
        return  this.receive_records;
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

    getItems: function() {
        let _this = this;

        let token = localStorage.getItem("accessToken");

      const params = {
        level: 1,
        parent: '',
        
        page: _this.page,
        size: _this.perPage,
      };

      axios
        .get("api/office_items_catalog", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.items = res.data;

            if(_this.items.length > 0)
            {
              _this.total = _this.items[0].cnt;
              _this.setPages();
            }
            else
              _this.total = 0;

          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    print: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("parent", this.lv1 + this.lv2 + this.lv3 + this.lv4);

      axios({
        method: "post",
        url: "api/office_items_catalog_print",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "office_items_catalog.xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        });
    },

    clear: function() {
      this.lv1 = "";
      this.lv1_item = {};
      this.lv2 = "";
      this.lv2_item = {};
      this.lv3 = "";
      this.lv3_item = {};
      this.lv4 = "";
      this.lv4_item = {};

      this.level2 = [];
      this.level3 = [];
      this.level4 = [];

      this.view_detail = false;
      this.filter_apply_new();
    },

    filter_apply_new: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

    const params = {
      level: 1,
      parent: this.lv1 + this.lv2 + this.lv3 + this.lv4,
      
      page: _this.page,
      size: _this.perPage,
    };

    axios
      .get("api/office_items_catalog", {
        params,
        headers: { Authorization: `Bearer ${token}` },
      })
      .then(
        (res) => {
          _this.items = res.data;

          if(_this.items.length > 0)
          {
            _this.total = _this.items[0].cnt;
            _this.setPages();
          }
          else
            _this.total = 0;

        },
        (err) => {
          alert(err.response);
        }
      )
      .finally(() => {});
  },

    getLevel1: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      const params = {
        level: 1,
        parent: '',
      };

      axios
        .get("api/office_items_main_category", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.level1 = res.data;

            _this.org_level1 = JSON.parse(JSON.stringify(_this.level1));

            _this.lv1 = "";
            _this.lv1_item = {};
            _this.lv2 = "";
            _this.lv2_item = {};
            _this.lv3 = "";
            _this.lv3_item = {};

            _this.level2 = [];
            _this.level3 = [];
            _this.level4 = [];

          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    detail: function() {

        if (this.lv1 == "") {
          Swal.fire({
              text: "Please choose a main category",
              icon: "warning",
              confirmButtonText: "OK",
            });
          return;
        }
        else if(this.lv2 == "") {
          Swal.fire({
              text: "Please choose a sub category",
              icon: "warning",
              confirmButtonText: "OK",
            });
          return;
        }
        else if(this.lv3 == "") {
          Swal.fire({
            text: "Please choose a brand",
            icon: "warning",
            confirmButtonText: "OK",
          });
        return;
      }
        else
        {

         this.lv4 = this.lv1 + this.lv2 + this.lv3;

        this.lv3_item = this.level3.find(({ code }) => code === this.lv3);

          this.getLevel4(this.lv1 + this.lv2 + this.lv3);

          this.view_detail = true;
  
        }
      },

      
      async getLevel4 () {
        let cat_id = this.lv1 + this.lv2 + this.lv3;
        if(cat_id == "") 
          return;
  
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        const params = {
          key: cat_id,
        };
  
        try {
          let res = await axios.get("api/office_items_description", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });
          
          _this.level4 = res.data;
          _this.org_level4 = JSON.parse(JSON.stringify(_this.level4));
  
        } catch (err) {
          console.log(err)
          alert('error')
        }
    },

      async getLevel3 () {
        let cat_id = this.lv1 + this.lv2;
        if(cat_id == "") 
          return;
  
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        const params = {
          key: cat_id,
        };
  
        try {
          let res = await axios.get("api/office_items_brand", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });
          
          this.lv2_item = this.level2.find(({ code }) => code === this.lv2);
          _this.level3 = res.data;
          _this.org_level3 = JSON.parse(JSON.stringify(_this.level3));

            _this.lv3 = "";
            _this.lv3_item = {};

          _this.level4 = [];
  
        } catch (err) {
          console.log(err)
          alert('error')
        }
    },
    
    async getLevel2 () {
        let cat_id = this.lv1;
        if(cat_id == "") 
          return;
  
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        const params = {
          key: cat_id,
        };
  
        try {
          let res = await axios.get("api/office_items_sub_category", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });
          
          this.lv1_item = this.level1.find(({ code }) => code === this.lv1);
          _this.level2 = res.data;
          _this.org_level2 = JSON.parse(JSON.stringify(_this.level2));


            _this.lv2 = "";
            _this.lv2_item = {};
            _this.lv3 = "";
            _this.lv3_item = {};

          _this.level3 = [];
          _this.level4 = [];
  
        } catch (err) {
          console.log(err)
          alert('error')
        }
    },

    apply: function() {
      if(this.submit == true) return;
      if(this.lv1 == "") return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("code", this.lv4);
      form_Data.append("level1", JSON.stringify(this.level4));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/office_items_description_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          
          _this.detail();
          //_this.reset();
          
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.detail();
          //_this.reset();
        });

    },

    reset_org: function() {
      this.level4 = JSON.parse(JSON.stringify(this.org_level4));
    },

    reset: function() {
      this.submit = false;

      this.getLevel1();
      this.view_detail = false;
      this.lv1 = "";
      this.lv1_item = {};
      this.lv2 = "";
      this.lv2_item = {};
      this.lv3 = "";
      this.lv3_item = {};
      this.level3 = [];
      this.level2 = [];
      this.level4 = [];
      this.editing = false;

    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    _add_criterion: function() {
      if (
        this.code.trim() == "" || this.category.trim() == ""
      ) {
        Swal.fire({
          text: "Code and Description are required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      // get the largest sn from level1
      var max_sn = 0;
      this.level4.forEach(function(item, index, array) {
        if (item.sn > max_sn) max_sn = item.sn;
      });

        var ad = {
          id: 0,
          sn: ++max_sn,
          code: this.code,
          category: this.category,
          status : 0,
        };
        this.level4.push(ad);
      
        this.clear_edit();
    },

    _cancel_criterion: function() {

      this.clear_edit();
    },

    setTwoNumberDecimal: function() {
        this.code = this.code.toString().padStart(2, '0')
        },

    _update_criterion: function() {
      if (this.code.trim() == "" || this.category.trim() == "" ) {
        Swal.fire({
            text: "Code and Description are required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.level4.find(({ sn }) => sn === this.org_id);

      element.code = this.code;
      element.category = this.category;
          
      this.clear_edit();
    },


    clear_edit: function() {

      this.code = "";
      this.category = "";

      this.editing = false;

    },

    _set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.level4.find(({ sn }) => sn === eid);
      this.level4.splice(fromIndex, 1);
      this.level4.splice(toIndex, 0, element);
    },

    _set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.level4.length - 1)
        toIndex = this.level4.length - 1;

      var element = this.level4.find(({ sn }) => sn === eid);
      this.level4.splice(fromIndex, 1);
      this.level4.splice(toIndex, 0, element);
    },

    _edit: function(eid) {
 
      var element = this.level4.find(({ sn }) => sn === eid);

      this.org_id = eid;
      this.code = element.code;
      this.category = element.category;
      this.sn = element.sn;
    
      this.editing = true;
    },

    _del: function(eid) {
        let _this = this;
      var index = this.level4.findIndex(({ sn }) => sn === eid);
      if (index > -1) {

        // var element = this.level4.find(({ sn }) => sn === eid);
        // if(element.items.length > 0) {
        // CHOOSE TO DELETE OR NOT

          Swal.fire({
            title: "Delete",
            text: "Are you sure to delete this description?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
          }).then((result) => {
            if (result.value) {
                _this.level4.splice(index, 1);
            }
          });

          
        //   return;
        // }

        //this.level4.splice(index, 1);
      }
    },

  },
});
