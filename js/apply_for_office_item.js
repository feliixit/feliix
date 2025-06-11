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

    showExtra: function() {
      return this.leave_type == "B";
    },

    list_amonut: function() {
      return this.list_price * this.list_qty;
    },

    sum_amonut: function() {
      let sum = 0.0;
      for (i = 0; i < this.petty_list.length; i++) {
        sum += this.petty_list[i].qty * this.petty_list[i].price;
      }
      return sum;
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
      this.$refs.file.value = "";
      this.payable_other = "";
      
      this.remark = "";
      
      this.rtype = "";
      this.dept_name = "";
      
      if(this.pid == 0)
      {
        this.request_no = "";
        this.pid = 0;
        this.getRequestNo();
        this.item_list = [];
      }

      if(this.pid != 0)
      {
        this.befor_reset = false;
        for(var i = 0; i < this.item_list.length; i++)
          this.item_list[i].is_checked = false;
      }
      else
        this.befor_reset = true;
      
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

      var element = this.petty_list.find(({ id }) => id === eid);
      this.petty_list.splice(fromIndex, 1);
      this.petty_list.splice(toIndex, 0, element);
    },

    _set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.petty_list.length - 1)
        toIndex = this.petty_list.length - 1;

      var element = this.petty_list.find(({ id }) => id === eid);
      this.petty_list.splice(fromIndex, 1);
      this.petty_list.splice(toIndex, 0, element);
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
      var index = this.petty_list.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.petty_list.splice(index, 1);
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
      for (i = 0; i < this.petty_list.length; i++) {
        if (this.petty_list[i].id > id) id = this.petty_list[i].id;

        if(this.petty_list[i].code1 == item.code1 && this.petty_list[i].code2 == item.code2 && this.petty_list[i].code3 == item.code3 && this.petty_list[i].code4 == item.code4)
        {
          Swal.fire({
            text: "Same-code item cannot be added into Listing twice.",
            icon: "warning",
            confirmButtonText: "OK",
          });

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
          reserve_qty : item.reserve_qty,
          item_id: item.id,
        };
        this.petty_list.push(ad);
      
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
