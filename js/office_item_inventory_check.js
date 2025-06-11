var app = new Vue({
  el: "#app",
  data: {
    name: "",
    request_no: "",

    baseURL: "https://storage.googleapis.com/feliiximg/",

    date_requested: "",
    request_type: "",
    reason1: "",
    reason: "",
    payable_to: 1,
    payable_other: "",
    remark: "",
    pid:0,

    petty_list: [],

    item_list: [],

    submit: false,

    list_payee: "",
    list_particulars: "",
    list_price: 0,
    list_qty: 0,

    prj_id:0,
    prj_name:"",

    list_sn:0,
    e_sn: 0,

    e_org_payee: "",
    e_org_particulars: "",
    e_org_price: 0,
    e_org_qty: 0,

    e_org_id: 0,

    e_editing: false,

    e_org_rtype: "",
    e_org_dept_name: "",

    projects: [],

    rtype : '',
    dept_name : '',


    // office category
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

    befor_reset : true,

    id: 0,
    notes:"",
    notes2:"",
    notes3:"",
    notes4:"",

    record: {},

    it_page: 1,
    it_perPage: 20,
    it_pages: [],
    it_pages_10: [],

    phase1: [],
    it_total : 0,

    is_toIndex: "",
  },

  created() {
    let _this = this;
    let uri = window.location.href.split("?");
    if (uri.length == 2) {
      let vars = uri[1].split("&");
      let getVars = {};
      let tmp = "";
      vars.forEach(function(v) {
        tmp = v.split("=");
        if (tmp.length == 2) {
          switch (tmp[0]) {
            case "pid":
              _this.pid = tmp[1];
              _this.getProjectInfo(_this.pid);
              break;
            case "prj_id":
              _this.prj_id = tmp[1];
              _this.getProjectName(_this.prj_id);
              break;
            case "id":
              _this.id = tmp[1];
              _this.getRecord(_this.id);
              break;
          }

        } 
        else 
          _this.getRequestNo();
      });
    } else _this.getRequestNo();

    // this.getProjectNames();
    this.getLevel1();
    this.getItems();
  },

  computed: {
    displayedRecord() {
      return this.receive_records;
    },

    // showExtra: function() {
    //   return this.leave_type == "B";
    // },

    // list_amonut: function() {
    //   return this.list_price * this.list_qty;
    // },

    // sum_amonut: function() {
    //   let sum = 0.0;
    //   for (i = 0; i < this.petty_list.length; i++) {
    //     sum += this.petty_list[i].qty * this.petty_list[i].price;
    //   }
    //   return sum;
    // },

    phase() {
      this.it_total = this.phase1.length;
      this.it_setPages();
      return this.it_paginate(this.phase1);
    },


  },

  mounted() {},

  watch: {
    // request_type: function(val) {
    //   if (val == "3") {
    //     this.reason = "Petty Cash Replenishment";
      
    //   } 
    // },

    

  },

  methods: {

    can_access: async function(id) {
      let ret = "";
      let token = localStorage.getItem("accessToken");

      const params = {
        id: id,
      };

      try {
        let res = await axios.get("api/office_item_can_access", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });

        ret = res.data;

      } catch (err) {
        console.log(err);
      }

      return ret;
    }
    ,

    getRecord: async function(id) {
        let _this = this;

        if(id == 0)
          return;

        let ret = await this.can_access(id);

        if(ret != "")
        {
          await Swal.fire({
            text: ret,
            icon: "warning",
            confirmButtonText: "OK",
          });

          window.location = "office_item_inventory_check_mgt";
          return;
        }
  
        const params = {
          pg: 1,
          id: id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/office_item_inventory_check", {
              params,
              headers: { Authorization: `Bearer ${token}` },
            })
          .then(function(response) {
            console.log(response.data);

            if (response.data.length > 0) {
            
              _this.record = response.data[0];
              _this.phase1 = JSON.parse(JSON.stringify(_this.record.phase1));
              _this.it_total = _this.phase1.length;
              _this.notes = _this.record.note_1;
              _this.notes2 = _this.record.note_2;
              _this.notes3 = _this.record.note_3;
              _this.notes4 = _this.record.note_4;

              //_this.it_setPages();
            }
          })
          .catch(function(error) {
            console.log(error);
          });
  
      },

      save: function(stage, note) {
        let _this = this;

        var form_Data = new FormData();
        var token = localStorage.getItem("token");
        form_Data.append("jwt", token);
        form_Data.append("id", _this.id);
        form_Data.append("stage", stage);
        form_Data.append("notes", note);
        form_Data.append("phase", JSON.stringify(_this.phase1));
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${token}`,
          },
          url: "api/office_item_inventory_check_save",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            //this.$forceUpdate();
            Swal.fire({
              text: response.data.message,
              icon: "info",
              confirmButtonText: "OK",
            });

          })
          .catch(function(response) {
            //handle error
            Swal.fire({
              text: response.data,
              icon: "warning",
              confirmButtonText: "OK",
            });
          });
        
      },


    do_goto_phase4: function() {
      let _this = this;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);
      form_Data.append("id", _this.id);
      form_Data.append("notes", _this.notes3);
      form_Data.append("phase", JSON.stringify(_this.phase1));
      form_Data.append("stage", 3);
      form_Data.append("status", 4);
      form_Data.append("request_no", _this.record.request_no);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/office_item_inventory_check_action",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          // yes then go to next phase
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          }).then(function() {
            _this.getRecord(_this.id);
            _this.it_page = 1;
          });

        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "warning",
            confirmButtonText: "OK",
          });
        });
    },


    goto_phase3: function() {
      // all the pahse1.qty1 should not be empty
      for (i = 0; i < this.phase1.length; i++) {
        if (this.phase1[i].qty1 === "" || this.phase1[i].qty1 === undefined) {
          Swal.fire({
            text: "Qty counted is required for every item in the checking list.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return false;
        }
      }

      for (i = 0; i < this.phase1.length; i++) {
        if (parseInt(this.phase1[i].qty1) < 0) {
          Swal.fire({
            text: "Qty is not allowed to be negative.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return false;
        }
      }
      
      let _this = this;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);
      form_Data.append("id", _this.id);
      form_Data.append("notes", _this.notes2);
      form_Data.append("phase", JSON.stringify(_this.phase1));
      form_Data.append("stage", 2);
      form_Data.append("status", 3);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/office_item_inventory_check_action",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          // yes then go to next phase
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          }).then(function() {
            _this.getRecord(_this.id);
            _this.it_page = 1;
          });

        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "warning",
            confirmButtonText: "OK",
          });
        });
    },

    goto_phase2: function() {
      if (this.phase1.length == 0) {
        Swal.fire({
          text: "Checking list needs to contain at least one item before going to the next phase!",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return false;
      }

      let _this = this;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);
      form_Data.append("id", _this.id);
      form_Data.append("notes", _this.notes);
      form_Data.append("phase", JSON.stringify(_this.phase1));
      form_Data.append("status", 2);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/office_item_inventory_check_action",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          // yes then go to next phase
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          }).then(function() {
            _this.getRecord(_this.id);
            _this.it_page = 1;
          });

        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "warning",
            confirmButtonText: "OK",
          });
        });
    },

    export_list: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
        form_Data.append("list", JSON.stringify(_this.phase1));
  
      axios({
        method: "post",
        url: "api/office_item_inventory_check_export",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "Office_Item_Application_" + _this.record.request_no + ".xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        });
      },

      
    export_list3: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
        form_Data.append("list", JSON.stringify(_this.phase1));
  
      axios({
        method: "post",
        url: "api/office_item_inventory_check_stage3_export",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "Office_Item_Application_" + _this.record.request_no + ".xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        });
      },
      
    export_list4: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

        var form_Data = new FormData();

        form_Data.append("jwt", token);
        form_Data.append("id", this.id);
        form_Data.append("list", JSON.stringify(_this.phase1));
  
      axios({
        method: "post",
        url: "api/office_item_inventory_check_stage4_export",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "Office_Item_Application_" + _this.record.request_no + ".xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        });
      },

    goto_phase1: function() {
      let _this = this;
        Swal.fire({
          title: "Previous Phase",
          text: "When you click the button of “Previous Phase”, then all the encoded or saved content in Columns “Notes”, “Qty Checked” and “Remarks” will be discarded and unrecoverable. Are you sure to continue this action?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          cancelButtonText: "No",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.value) {
            _this.do_goto_phase1(); // <--- submit form programmatically
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });

    },

    goto_phase4: function() {
      let _this = this;
        Swal.fire({
          title: "Approve",
          text: "Are you sure to approve this inventory check result?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          cancelButtonText: "No",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.value) {

            for (i = 0; i < _this.phase1.length; i++) {
              if (parseInt(_this.phase1[i].qty2) < 0) {
                Swal.fire({
                  text: "Qty is not allowed to be negative.",
                  icon: "warning",
                  confirmButtonText: "OK",
                });
                return false;
              }
            }

            _this.do_goto_phase4(); // <--- submit form programmatically
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });

    },

    
    
    backto_phase2: function() {
      let _this = this;
        Swal.fire({
          title: "“Reject”",
          text: "When you click the button of “Reject”, then all your encoding content except for “Notes” will be discarded and unrecoverable. Are you sure to continue this action?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          cancelButtonText: "No",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.value) {
            _this.do_goto_phase2(); // <--- submit form programmatically
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });

    },

    go_to_check_mgt: function() {
      window.location = "office_item_inventory_check_mgt";
    },

    
    do_goto_phase2() {
      let _this = this;

      for (var i = 0; i < this.phase1.length; i++) {
        this.phase1[i].qty2 = "";
        this.phase1[i].comment = "";
      }

      var form_Data = new FormData();
      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);
      form_Data.append("id", _this.id);
      form_Data.append("notes", _this.notes3);
      form_Data.append("phase", JSON.stringify(_this.phase1));
      form_Data.append("stage", 3);
      form_Data.append("status", 2);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/office_item_inventory_check_action",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          // yes then go to next phase
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          }).then(function() {
            _this.getRecord(_this.id);
            _this.it_page = 1;
          });

        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "warning",
            confirmButtonText: "OK",
          });
        });
    },

    
    do_goto_phase1() {
      let _this = this;

      this.reset2();

      var form_Data = new FormData();
      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);
      form_Data.append("id", _this.id);
      form_Data.append("notes", "");
      form_Data.append("phase", JSON.stringify(_this.phase1));
      form_Data.append("stage", 2);
      form_Data.append("status", 1);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/office_item_inventory_check_action",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          // yes then go to next phase
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          }).then(function() {
            _this.getRecord(_this.id);
            _this.it_page = 1;
          });

        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "warning",
            confirmButtonText: "OK",
          });
        });
    },

    reject: function() {
    },

    approve: function() {
    },

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

    it_setPages () {
      console.log('setPages');
      this.it_pages = [];

      let numberOfPages = Math.ceil(this.it_total / this.it_perPage);

      // if(this.fil_keyword != '')
      //   numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if(numberOfPages == 1)
        this.it_page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.it_pages.push(index);
      }

      //this.it_paginate(this.receive_records);
    },

    it_paginate: function (posts) {
      console.log('paginate');
      if(this.it_page < 1)
        this.it_page = 1;
      if(this.it_page > this.it_pages.length)
        this.it_page = this.it_pages.length;

        let tenPages = Math.floor((this.it_page - 1) / 10);
        if(tenPages < 0)
          tenPages = 0;
        this.it_pages_10 = [];
        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;

        this.it_pages_10 = this.it_pages.slice(from, to);

      // if(this.fil_keyword != '')

      let page = this.it_page;
      let perPage = this.it_perPage;
      let it_from = (page * perPage) - perPage;
      let it_to = (page * perPage);
      return  this.phase1.slice(it_from, it_to);
      
      //return this.phase1.slice(from, to);
      // else
        //return  this.receive_records;
    },

    it_pre_page: function(){
      let tenPages = Math.floor((this.it_page - 1) / 10) + 1;

        this.it_page = parseInt(this.it_page) - 10;
        if(this.it_page < 1)
          this.it_page = 1;
 
        this.it_pages_10 = [];

        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;

        this.it_pages_10 = this.it_pages.slice(from, to);
      
    },

    it_nex_page: function(){
      let tenPages = Math.floor((this.it_page - 1) / 10) + 1;

      this.it_page = parseInt(this.it_page) + 10;
      if(this.it_page > this.it_pages.length)
        this.it_page = this.it_pages.length;

      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      let pages_10 = this.it_pages.slice(from, to);

      if(pages_10.length > 0)
        this.it_pages_10 = pages_10;

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
      .finally(() => { console.log('getItems') });
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

    filter_apply_async: async function() {
      let data = [];
      let token = localStorage.getItem("accessToken");

      const params = {
        level: 1,
        parent: this.lv1 + this.lv2 + this.lv3 + this.lv4,
        page: 1,
        size: 100000,
      };

      try {
        let res = await axios.get("api/office_items_catalog", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });

        data = res.data;
      } catch (err) {
        console.log(err);
        alert("error");
      }

      return data;
    },


    clear_projectname: function() {
      this.reason = "";
    },

    validateNumber: function(obj) {
      var number = obj;

      if (isNaN(number)) {
        return false;
      }
      return true;
    },

    check_input: function() {
      if (this.date_requested.trim() == "") {
        Swal.fire({
          text: "Date Needed is required.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return false;
      }

      if (this.list_particulars.trim() == "") {
        Swal.fire({
          text: "Please input Particulars.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return false;
      }

      if (!this.validateNumber(this.list_price)) {
        Swal.fire({
          text: "Please input Price.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return false;
      }

      if (this.list_qty < 1 || !this.validateNumber(this.list_qty)) {
        Swal.fire({
          text: "Please input Quantity.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return false;
      }

      return true;
    },

    add_list: function() {
      if (this.check_input() == false) return;

      this.petty_list.push({
        is_checked: 0,
        payee: this.list_payee,
        particulars: this.list_particulars,
        price: this.list_price,
        qty: this.list_qty,
      });
      this.list_payee = "";
      this.list_particulars = "";
      this.list_price = 0;
      this.list_qty = 0;
    },

    remove_list: function() {
      for (i = 0; i < this.petty_list.length; i++) {
        if (this.petty_list[i].is_checked == 1) {
          this.petty_list.splice(i, 1);
          i = i - 1;
        }
      }
    },

    getProjectInfo: function(pid) {
      let _this = this;
      let token = localStorage.getItem("accessToken");
      const params = {
        pid: pid,
      };
      axios
        .get("api/apply_for_office_item_edit", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          _this.request_no = response.data[0].request_no;
          _this.date_requested = response.data[0].date_requested.replaceAll(
            "/",
            "-"
          );

          _this.reason = response.data[0].reason;
          _this.item_list = response.data[0].attachment;
          _this.remark = response.data[0].remarks;

          _this.petty_list = response.data[0].list;
        })
        .catch(function(error) {
          //handle error
        });
    },

  
    getRequestNo: function() {
      let _this = this;
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/apply_for_office_item_request_no",
      })
        .then(function(response) {
          _this.request_no = response.data.request_no;
        })
        .catch(function(error) {
          //handle error
        });
    },

    validateForm() {
      if (this.date_requested == "") {
        Swal.fire({
          text: "Date Needed is required.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch Type';
        //$(window).scrollTop(0);
        return false;
      }

      if (this.reason == "") {
        Swal.fire({
          text: "Reason is required.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch location';
        //$(window).scrollTop(0);
        return false;
      }

      if (this.petty_list.length == 0) {
        Swal.fire({
          text: "At least one office item should be included in Listing.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch location';
        //$(window).scrollTop(0);
        return false;
      }

      // every qty in petty_list should be a number and greater than 0
      for (i = 0; i < this.petty_list.length; i++) {
        if (!this.validateNumber(this.petty_list[i].amount) || this.petty_list[i].amount < 1) {
          Swal.fire({
            text: "Qty should be at least 1.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return false;
        }
      }

      // every qty in petty_list should not greater than stock
      var html = "";
      for (i = 0; i < this.petty_list.length; i++) {
        if (this.petty_list[i].amount > this.petty_list[i].qty - this.petty_list[i].reserve_qty) {
          html += "(" + Number(i+1) + ")" + this.petty_list[i].cat1 + " >> " + this.petty_list[i].cat2 + " >> " + this.petty_list[i].cat3 + " >> " + this.petty_list[i].cat4 + ": " + this.petty_list[i].amount + " (Stock qty is " + this.petty_list[i].qty + ", Reserve qty is " + this.petty_list[i].reserve_qty + ")" + "<br>";
        }
      }

      if (html != "") {
        Swal.fire({
          html: "<h2>warning: Stock Not Enough</h2>" + "<br>" + html,
          confirmButtonText: "OK",
        });
        return false;
      }

      


      return true;
    },

    validateForm_edit() {
      if (this.date_requested == "") {
        Swal.fire({
          text: "Choose request date",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch Type';
        //$(window).scrollTop(0);
        return false;
      }

      if (this.request_type == "") {
        Swal.fire({
          text: "Choose request type",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch location';
        //$(window).scrollTop(0);
        return false;
      }

      if (this.reason == "" && this.reason1 == "") {
        Swal.fire({
          text: "Please Input project name or reason",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch location';
        //$(window).scrollTop(0);
        return false;
      }

      if (this.payable_to == 0) {
        Swal.fire({
          text: "Please select payable to",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch location';
        //$(window).scrollTop(0);
        return false;
      }

      if (this.payable_to == 2 && this.payable_other == "") {
        Swal.fire({
          text: "Please specific other payable",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Choose Punch location';
        //$(window).scrollTop(0);
        return false;
      }

      if (!this.sum_amonut > 0) {
        Swal.fire({
          text: "Petty list required",
          icon: "warning",
          confirmButtonText: "OK",
        });
        //this.err_msg = 'Location Photo required';
        //$(window).scrollTop(0);
        return false;
      }

      return true;
    },

    apply: function() {
      if (!this.validateForm()) return;

      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("request_no", this.request_no);
      form_Data.append("date_requested", this.date_requested);
      form_Data.append("reason", this.reason);
      form_Data.append("remark", this.remark);

      for (var i = 0; i < this.$refs.file.files.length; i++) {
        let file = this.$refs.file.files[i];
        form_Data.append("files[" + i + "]", file);
      }

      form_Data.append("petty_list", JSON.stringify(this.petty_list));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/apply_for_office_item",
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

          _this.reset();
        });

    },

    apply_edit: function() {
      if (!this.validateForm()) return;

      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("request_no", this.request_no);
      form_Data.append("date_requested", this.date_requested);
      form_Data.append("reason", this.reason);
      form_Data.append("remark", this.remark);
      form_Data.append("pid", this.pid);

      var favorite = [];
      for(var i = 0; i < this.item_list.length; i++)
      {
        if(this.item_list[i].is_checked === false)
          favorite.push(this.item_list[i].id);
      }
      form_Data.append("items_to_delete", JSON.stringify(favorite));

      for (var i = 0; i < this.$refs.file.files.length; i++) {
        let file = this.$refs.file.files[i];
        form_Data.append("files[" + i + "]", file);
      }

      form_Data.append("petty_list", JSON.stringify(this.petty_list));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/apply_for_office_item_edit",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          }).then(function() {
            window.location = "office_item_application_records";
            //_this.reset();
          });

      })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
        });

    },

    reset2: function() {
      for (var i = 0; i < this.phase1.length; i++) {
        this.phase1[i].qty1 = "";
        this.phase1[i].note = "";
      }

      // this.notes2 = this.record.note_2;
      this.notes2 = "";
    },

    reset3: function() {
      for (var i = 0; i < this.phase1.length; i++) {
        this.phase1[i].qty2 = "";
        this.phase1[i].comment = "";
      }

      // this.notes2 = this.record.note_2;
      this.notes3 = "";
    },

    reset: function() {
      this.list_payee = "";
      this.list_particulars = "";
      this.list_price = 0;
      this.list_qty = 0;
      this.petty_list = [];

      
      this.date_requested = "";
      this.request_type = "";
      this.reason = "";
      this.reason1 = "";
      this.payable_to = 1;
      // this.$refs.payable_other.style.display = "none";
      //this.$refs.file.value = "";
      this.payable_other = "";
      
      this.remark = "";
      
      this.rtype = "";
      this.dept_name = "";
      
      // if(this.pid == 0)
      // {
      //   this.request_no = "";
      //   this.pid = 0;
      //   this.getRequestNo();
      //   this.item_list = [];
      // }

      // if(this.pid != 0)
      // {
      //   this.befor_reset = false;
      //   for(var i = 0; i < this.item_list.length; i++)
      //     this.item_list[i].is_checked = false;
      // }
      // else
      //   this.befor_reset = true;

      this.phase1 = JSON.parse(JSON.stringify(this.record.phase1));
      this.it_total = this.phase1.length;
      this.notes = this.record.note_1;
      
      this.list_sn = 0;

      this.submit = false;
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    e_set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.petty_list.find(({ id }) => id === eid);
      this.petty_list.splice(fromIndex, 1);
      this.petty_list.splice(toIndex, 0, element);
    },

    e_set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.petty_list.length - 1)
        toIndex = this.petty_list.length - 1;

      var element = this.petty_list.find(({ id }) => id === eid);
      this.petty_list.splice(fromIndex, 1);
      this.petty_list.splice(toIndex, 0, element);
    },

    e_edit: function(eid) {
      this.scrollMeTo('porto');
      var element = this.petty_list.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_payee = element.payee;
      this.e_org_particulars = element.particulars;
      this.e_org_price = element.price;
      this.e_org_qty = element.qty;
      this.e_org_check_remark = element.check_remark;

      this.e_org_rtype = element.rtype;
      this.e_org_dept_name = element.dept_name;

      this.list_id = eid;
      this.list_payee = element.payee;
      this.list_particulars = element.particulars;
      this.list_price = element.price;
      this.list_qty = element.qty;
      this.list_check_remark = element.check_remark;

      this.e_editing = true;
    },

    e_del: function(eid) {
      var index = this.petty_list.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.petty_list.splice(index, 1);
      }
    },


    _set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.phase1.find(({ id }) => id === eid);
      this.phase1.splice(fromIndex, 1);
      this.phase1.splice(toIndex, 0, element);

      let page = toIndex / this.it_perPage;
      this.it_page = parseInt(page) + 1;

      this.is_toIndex = toIndex;

      setTimeout(() => {
        this.is_toIndex = "";
      }, 500)
    },

    _set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.phase1.length - 1)
        toIndex = this.phase1.length - 1;

      var element = this.phase1.find(({ id }) => id === eid);
      this.phase1.splice(fromIndex, 1);
      this.phase1.splice(toIndex, 0, element);

      let page = toIndex / this.it_perPage;
      this.it_page = parseInt(page) + 1;

      this.is_toIndex = toIndex;

      setTimeout(() => {
        this.is_toIndex = "";
      }, 500)
    },

    _edit: function(eid) {
      this.scrollMeTo('porto');
      var element = this.petty_list.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_payee = element.payee;
      this.e_org_particulars = element.particulars;
      this.e_org_price = element.price;
      this.e_org_qty = element.qty;
      this.e_org_check_remark = element.check_remark;

      this.e_org_rtype = element.rtype;
      this.e_org_dept_name = element.dept_name;

      this.list_id = eid;
      this.list_payee = element.payee;
      this.list_particulars = element.particulars;
      this.list_price = element.price;
      this.list_qty = element.qty;
      this.list_check_remark = element.check_remark;

      this.e_editing = true;
    },

    _del: function(eid) {
      var index = this.phase1.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.phase1.splice(index, 1);
      }
    },



    e_add_criterion: function() {
      if (
        this.list_payee.trim() == "" ||
        this.list_particulars.trim() == "" 
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

        var ad = {
          id: ++this.list_sn,
          sn: ++this.list_sn,
          payee: this.list_payee,
          particulars: this.list_particulars,
          price: this.list_price,
          qty: this.list_qty,
          status : 1,
          check_remark: '',
        };
        this.petty_list.push(ad);

        this.e_clear_edit();

    },

    e_cancel_criterion: function() {
      this.list_payee = this.e_org_payee;
      this.list_particulars = this.e_org_particulars;
      this.list_price = this.e_org_price;
      this.list_qty = this.e_org_qty;

      this.e_clear_edit();
    },

    e_update_criterion: function() {
      if (this.list_payee.trim() == "" || this.list_particulars.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.petty_list.find(({ id }) => id === this.e_org_id);
      element.payee = this.list_payee;
      element.particulars = this.list_particulars;
      element.price = this.list_price;
      element.qty = this.list_qty;
    
      this.e_clear_edit();
    },

    e_clear_edit: function() {

      this.e_org_id = 0;
      this.e_org_payee = "";
      this.e_org_particulars = "";
      this.e_org_price = 0;
      this.e_org_qty = 0;
      this.e_org_check_remark = "";

      this.list_payee = "";
      this.list_particulars = "";
      this.list_price = 0;
      this.list_qty = 0;

      this.e_editing = false;
    },


    _add_criterion: function(item) {

      var id = 0;
      for (i = 0; i < this.phase1.length; i++) {
        if (this.phase1[i].id > id) id = this.phase1[i].id;

        if(this.phase1[i].code1 == item.code1 && this.phase1[i].code2 == item.code2 && this.phase1[i].code3 == item.code3 && this.phase1[i].code4 == item.code4)
        {
          // Swal.fire({
          //   text: "Same-code item cannot be added into Listing twice.",
          //   icon: "warning",
          //   confirmButtonText: "OK",
          // });

          return;
        }
      }

        var ad = {
          id: ++id,
          code1: item.code1,
          code2: item.code2,
          code3: item.code3,
          code4: item.code4,
          cat1: item.cat1,
          cat2: item.cat2,
          cat3: item.cat3,
          cat4: item.cat4,
          url: item.url,
          amount : item.amount,
          qty: item.qty,
          qty1: "",
          note: "",
          qty2: "",
          comment: "",
          reserve_qty : item.reserve_qty,
          item_id: item.id,
        };
        this.phase1.push(ad);
      
        this.e_clear_edit();
    },

    
    add_filtered: async function() {

      var id = 0;
      var duplicate = false;

      var items = await this.filter_apply_async();

      for(j=0; j < items.length; j++)
      {
        duplicate = false;
        
        for (i = 0; i < this.phase1.length; i++) {
          if (this.phase1[i].id > id) id = this.phase1[i].id;
  
          if(this.phase1[i].code1 == items[j].code1 && this.phase1[i].code2 == items[j].code2 && this.phase1[i].code3 == items[j].code3 && this.phase1[i].code4 == items[j].code4)
          {
            // Swal.fire({
            //   text: "Same-code items[j] cannot be added into Listing twice.",
            //   icon: "warning",
            //   confirmButtonText: "OK",
            // });
  
            duplicate = true;
            break;
          }

        }

        if(duplicate == true)
          continue;

        var ad = {
          id: ++id,
          code1: items[j].code1,
          code2: items[j].code2,
          code3: items[j].code3,
          code4: items[j].code4,
          cat1: items[j].cat1,
          cat2: items[j].cat2,
          cat3: items[j].cat3,
          cat4: items[j].cat4,
          url: items[j].url,
          amount : items[j].amount,
          qty: items[j].qty,
          qty1: "",
          note: "",
          qty2: "",
          comment: "",
          reserve_qty : items[j].reserve_qty,
          item_id: items[j].id,
        };
        this.phase1.push(ad);

      }
        this.e_clear_edit();
    },

    _cancel_criterion: function() {
      this.list_payee = this.e_org_payee;
      this.list_particulars = this.e_org_particulars;
      this.list_price = this.e_org_price;
      this.list_qty = this.e_org_qty;

      this.e_clear_edit();
    },

    _update_criterion: function() {
      if (this.list_payee.trim() == "" || this.list_particulars.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.petty_list.find(({ id }) => id === this.e_org_id);
      element.payee = this.list_payee;
      element.particulars = this.list_particulars;
      element.price = this.list_price;
      element.qty = this.list_qty;
    
      this.e_clear_edit();
    },

    _clear_edit: function() {

      this.e_org_id = 0;
      this.e_org_payee = "";
      this.e_org_particulars = "";
      this.e_org_price = 0;
      this.e_org_qty = 0;
      this.e_org_check_remark = "";

      this.e_org_rtype = "";
      this.e_org_dept_name = "";

      this.list_payee = "";
      this.list_particulars = "";
      this.list_price = 0;
      this.list_qty = 0;

      this.e_editing = false;
    },

    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: 'smooth' });
  
    },

  },
});
