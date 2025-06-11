var app = new Vue({
  el: "#app",
  data: {
    name: "",
    request_no: "",

    baseURL: "https://storage.googleapis.com/feliiximg/",

    date_requested: "",
    request_type: "",
    project_name1: "",
    project_name: "",
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

    this.getProjectNames();
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
    request_type: function(val) {
      if (val == "3") {
        this.project_name = "Petty Cash Replenishment";
      
      } 
    },


  },

  methods: {
    clear_projectname: function() {
      this.project_name = "";
    },

    validateNumber: function(obj) {
      var number = obj;

      if (isNaN(number)) {
        return false;
      }
      return true;
    },

    check_input: function() {
      if (this.list_payee.trim() == "") {
        Swal.fire({
          text: "Please input Payee.",
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
        .get("api/apply_for_petty_edit", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          _this.request_no = response.data[0].request_no;
          _this.date_requested = response.data[0].date_requested.replaceAll(
            "/",
            "-"
          );
          _this.request_type = response.data[0].request_type;
          _this.project_name1 = response.data[0].project_name1;
          _this.project_name = response.data[0].project_name;
          _this.payable_to = response.data[0].payable_to;
          _this.rtype = response.data[0].rtype;
          _this.dept_name = response.data[0].dept_name;

          if (_this.payable_to == 1) {
            document.getElementById("specific_payableto").style.display =
              "none";
          } else {
            document.getElementById("specific_payableto").value = "";
            document.getElementById("specific_payableto").style.display = "";
          }

          _this.item_list = response.data[0].items;
          _this.payable_other = response.data[0].payable_other;
          _this.remark = response.data[0].remark;

          _this.petty_list = response.data[0].list;
        })
        .catch(function(error) {
          //handle error
        });
    },

    
    getProjectNames: function() {
      let _this = this;
      let token = localStorage.getItem("accessToken");

      const params = {
        pid: 0,
      };

      axios
      .get("api/expense_get_project_names", {
        params,
        headers: { Authorization: `Bearer ${token}` },
      })
        .then(function(response) {
          _this.projects = response.data;
        })
        .catch(function(error) {
          //handle error
        });
    },

    getProjectName: function(prj_id) {
      let _this = this;
      let token = localStorage.getItem("accessToken");

      const params = {
        pid: prj_id,
      };

      axios
      .get("api/project02_get_project_name_by_project_id", {
        params,
        headers: { Authorization: `Bearer ${token}` },
      })
        .then(function(response) {
          _this.project_name1 = response.data;
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
        url: "api/apply_for_petty_request_no",
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

      if (this.project_name == "" && this.project_name1 == "") {
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

      if (this.project_name == "" && this.project_name1 == "") {
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
      form_Data.append("request_type", this.request_type);
      form_Data.append("project_name", this.project_name);
      form_Data.append("project_name1", this.project_name1);
      form_Data.append("payable_to", this.payable_to);
      form_Data.append("payable_other", this.payable_other);
      form_Data.append("remark", this.remark);

      form_Data.append("rtype", this.rtype);
      form_Data.append("dept_name", this.dept_name);

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
        url: "api/apply_for_petty",
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
      if (!this.validateForm_edit()) return;

      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("pid", this.pid);
      form_Data.append("date_requested", this.date_requested.replaceAll('-', '/'));
      form_Data.append("request_type", this.request_type);
      form_Data.append("project_name", this.project_name);
      form_Data.append("project_name1", this.project_name1);
      form_Data.append("payable_to", this.payable_to);
      form_Data.append("payable_other", this.payable_other);
      form_Data.append("remark", this.remark);

      form_Data.append("rtype", this.rtype);
      form_Data.append("dept_name", this.dept_name);

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
        url: "api/apply_for_petty_edit",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          }).then(function() {
            window.location = "apply_for_expense";
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

      this.request_no = "";

      this.date_requested = "";
      this.request_type = "";
      this.project_name = "";
      this.project_name1 = "";
      this.payable_to = 1;
      this.$refs.payable_other.style.display = "none";
      this.payable_other = "";

      this.remark = "";

      this.rtype = "";
      this.dept_name = "";

      this.pid = 0;
      this.list_sn = 0;
      this.item_list = [];

      this.submit = false;
      this.getRequestNo();
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


    _add_criterion: function() {
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
