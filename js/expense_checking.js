var app = new Vue({
  el: "#app",
  data: {
    name: "",
    month1: "",

    picked: "A",
    view_detail: false,

    submit: false,

    receive_records: [],
    record: {},

    baseURL: "https://storage.googleapis.com/feliiximg/",

    proof_remark: "",
    reject_reason: "",

    proof_id: 0,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    perPage: 10000,

    is_approval: false,

    // editing
    e_title: [],

    e_sn: 0,

    e_org_payee: "",
    e_org_particulars: "",
    e_org_price: 0,
    e_org_qty: 0,
    e_org_check_remark: "",

    e_payee: "",
    e_particulars: "",
    e_price: 0,
    e_qty: 0,
    e_check_remark: "",
   
    e_org_id: 0,

    e_editing: false,
  },

  created() {
    this.getUserName();
  },

  computed: {
    displayedRecord() {
      this.setPages();
      return this.paginate(this.receive_records);
    },

    wrongNumber: function () {
      return this.isNumeric(this.record.total) === false
    },
    
  },

  mounted() {
    var d1 = new Date();
    this.month1 = d1;

    $("#start").val(
      d1
        .toISOString()
        .slice(0, 7)
        .replace(/-/g, "-")
    );

    this.getLeaveCredit();
  },

  watch: {
    picked() {
      this.getLeaveCredit();
    },

    proof_id() {
        this.detail();
    }
    
  },

  methods: {
    setPages() {
      console.log("setPages");
      this.pages = [];
      let numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if (numberOfPages == 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function(posts) {
      console.log("paginate");
      if (this.page < 1) this.page = 1;
      if (this.page > this.pages.length) this.page = this.pages.length;

      let page = this.page;
      let perPage = this.perPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      return this.receive_records.slice(from, to);
    },

    isNumeric: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },

    getLeaveCredit: function() {
      let _this = this;

      axios
        .get("api/expense_checking")
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if(_this.receive_records.length > 0)
          {
              //_this.proof_id = _this.receive_records[0].id;
              //_this.detail();
              //_this.proof_id = 0;
          }
        })
        .catch(function(error) {
          console.log(error);
        });

        _this.proof_id = 0;
    },

    getUserName: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/on_duty_get_myname",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.name = response.data.username;
          _this.is_manager = response.data.is_manager;
          if (_this.name === "Glendon Wendell Co") _this.is_approval = true;
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

    unCheckCheckbox() {
      for (i = 0; i < this.receive_records.length; i++) {
        this.receive_records[i].is_checked = false;
      }
      //$(".alone").prop("checked", false);
      //this.clicked = false;
    },

    showPic(pic) {
      Swal.fire({
        title: "Certificate of Diagnosis",
        text: "Click to close",
        imageUrl: "img/" + pic,
      });
    },

    approveReceiveRecord_OP: function(id) {
      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Send To OP");
      form_Data.append("id", id);
      form_Data.append("remark", "");
      form_Data.append("info_account", this.record.info_account);
      form_Data.append("info_category", this.record.info_category);
      if(this.record.info_category == 'Marketing' || this.record.info_category == 'Office Needs' || this.record.info_category == 'Others' || this.record.info_category == 'Projects' || this.record.info_category == 'Store')
        form_Data.append("sub_category", this.record.sub_category);
      else
        form_Data.append("sub_category", "");
      form_Data.append("info_remark", this.record.info_remark);
      form_Data.append("info_remark_other", this.record.info_remark_other);

      form_Data.append("petty_list", JSON.stringify(this.record.list));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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
          _this.resetForm();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    
    approveReceiveRecord_OP_ONLY: function(id) {
      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Send To OP ONLY");
      form_Data.append("id", id);
      form_Data.append("remark", "");
      form_Data.append("info_account", this.record.info_account);
      form_Data.append("info_category", this.record.info_category);
      if(this.record.info_category == 'Marketing' || this.record.info_category == 'Office Needs' || this.record.info_category == 'Others' || this.record.info_category == 'Projects' || this.record.info_category == 'Store')
        form_Data.append("sub_category", this.record.sub_category);
      else
        form_Data.append("sub_category", "");
      form_Data.append("info_remark", this.record.info_remark);
      form_Data.append("info_remark_other", this.record.info_remark_other);

      form_Data.append("petty_list", JSON.stringify(this.record.list));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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
          _this.resetForm();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    approveReceiveRecord_MD: function(id) {
      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Send To MD");
      form_Data.append("id", id);
      form_Data.append("remark", "");
      form_Data.append("info_account", this.record.info_account);
      form_Data.append("info_category", this.record.info_category);
      if(this.record.info_category == 'Marketing' || this.record.info_category == 'Office Needs' || this.record.info_category == 'Others' || this.record.info_category == 'Projects' || this.record.info_category == 'Store')
        form_Data.append("sub_category", this.record.sub_category);
      else
        form_Data.append("sub_category", "");
      form_Data.append("info_remark", this.record.info_remark);
      form_Data.append("info_remark_other", this.record.info_remark_other);

      form_Data.append("petty_list", JSON.stringify(this.record.list));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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
          _this.resetForm();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    rejectReceiveRecord: function(id) {
      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);

      form_Data.append("crud", "Checking Reject");
      form_Data.append("id", id);
      form_Data.append("remark", this.reject_reason);

      form_Data.append("petty_list", JSON.stringify(this.record.list));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/petty_cash_action",
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
          _this.resetForm();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: response.data,
            icon: "info",
            confirmButtonText: "OK",
          });
        });
    },

    get_lastest_record_status : async function(id) {
      // get api/expense_status
      let status = await axios.get("api/expense_status", {
        params: {
          id: id
        }
      });

      return status.data;
    },


    detail: async function() {
      let _this = this;

      //let favorite = [];

      //for (i = 0; i < this.receive_records.length; i++)
      //{
      //  if(this.receive_records[i].is_checked == 1)
      //    favorite.push(this.receive_records[i].sid);
      //}

      if (this.proof_id == 0) {
        //Swal.fire({
        //  text: "Please select row to see the detail!",
        //  icon: "warning",
        //  confirmButtonText: "OK",
        //});

        //$(window).scrollTop(0);
        this.view_detail = false;
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      var status = await this.get_lastest_record_status(this.proof_id);

      if(status != 1 && status != 2)
        {
          await Swal.fire({
            text: 'The status of the chosen expense application has changed and was not "For Check". System will refresh the content of the table.',
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          this.getLeaveCredit();
          return;
        }

      if(this.record.request_type == 'Petty Cash Replenishment')
        this.record.info_account = "Security Bank => Office Petty Cash";

      this.reject_reason = "";
      this.view_detail = true;

      this.e_sn = this.record.list.length;

    },

    approve_op: async function() {
      let _this = this;

      var status = await this.get_lastest_record_status(this.proof_id);

      if(status != 1 && status != 2)
        {
          await Swal.fire({
            text: 'The status of the chosen expense application has changed and was not "For Check". System will refresh the content of the table.',
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          this.getLeaveCredit();
          return;
        }

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be approved!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.record.info_account.trim() === "") {
        Swal.fire({
          text: "Please select account!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.record.info_category.trim() === "") {
        Swal.fire({
          text: "Please select category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if ((this.record.info_category.trim() === "Marketing" || this.record.info_category.trim() === "Office Needs" || this.record.info_category.trim() === "Others" || this.record.info_category.trim() === "Projects" || this.record.info_category.trim() === "Store")) {
        if(this.record.sub_category.trim() === "") 
        {Swal.fire({
          text: "Please select sub category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;}
        
      }

      if (this.record.info_remark.trim() === "") {
        Swal.fire({
          text: "Please select remark!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.record.info_remark.trim() === "Other" && this.record.info_remark_other.trim() === "") {
        Swal.fire({
          text: "Please enter other remark!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Send to OP for approve",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          _this.submit = true;
          _this.approveReceiveRecord_OP(this.proof_id);

          _this.resetForm();

        }
      });
    },


    
    approve_op_only: function() {
      let _this = this;

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be approved!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.record.info_account.trim() === "") {
        Swal.fire({
          text: "Please select account!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.record.info_category.trim() === "") {
        Swal.fire({
          text: "Please select category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if ((this.record.info_category.trim() === "Marketing" || this.record.info_category.trim() === "Office Needs" || this.record.info_category.trim() === "Others" || this.record.info_category.trim() === "Projects" || this.record.info_category.trim() === "Store")) {
        if(this.record.sub_category.trim() === "") 
        {Swal.fire({
          text: "Please select sub category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;}
        
      }

      if (this.record.info_remark.trim() === "") {
        Swal.fire({
          text: "Please select remark!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.record.info_remark.trim() === "Other" && this.record.info_remark_other.trim() === "") {
        Swal.fire({
          text: "Please enter other remark!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      // if(this.record.total > 5000)
      // {
      //   Swal.fire({
      //     text: 'Button of "Send OP" is only applicable to total amount requested is equal to or less than P5000.',
      //     icon: "warning",
      //     confirmButtonText: "OK",
      //   });
      //   return;
      // }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Send to OP for approve",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          _this.submit = true;
          _this.approveReceiveRecord_OP_ONLY(this.proof_id);

          _this.resetForm();

        }
      });
    },

    approve_md: function() {
      let _this = this;

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be approved!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.record.info_category.trim() === "") {
        Swal.fire({
          text: "Please select category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if ((this.record.info_category.trim() === "Marketing" || this.record.info_category.trim() === "Office Needs" || this.record.info_category.trim() === "Others" || this.record.info_category.trim() === "Projects" || this.record.info_category.trim() === "Store")) {
        if(this.record.sub_category.trim() === "") 
        {Swal.fire({
          text: "Please select sub category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;}
        
      }

      if (this.record.info_account.trim() === "") {
        Swal.fire({
          text: "Please select account!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.record.info_remark.trim() === "") {
        Swal.fire({
          text: "Please select remark!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.record.info_remark.trim() === "Other" && this.record.info_remark_other.trim() === "") {
        Swal.fire({
          text: "Please enter other remark!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.record.total <= 5000)
      {
        Swal.fire({
          text: 'Button of "Send MD" is only applicable to total amount requested is more than P5000.',
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Send to MD for approve",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          _this.submit = true;
          _this.approveReceiveRecord_MD(this.proof_id);

          _this.resetForm();

        }
      });
    },

    reject: function() {
      let _this = this;

      if (this.proof_id < 1) {
        Swal.fire({
          text: "Please select applicant to be rejected!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.reject_reason.trim() === "") {
        Swal.fire({
          text: "Please enter reject reason!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Reject and send back to requestor",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          _this.submit = true;
          _this.rejectReceiveRecord(this.proof_id);

          _this.resetForm();
        }
      });
    },
   

    resetForm: function() {
      this.record = [];
      this.reject_reason = "";
      this.proof_id = 0;
      this.getLeaveCredit();
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

      var element = this.record.list.find(({ id }) => id === eid);
      this.record.list.splice(fromIndex, 1);
      this.record.list.splice(toIndex, 0, element);
    },

    e_set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.list.length - 1)
        toIndex = this.record.list.length - 1;

      var element = this.record.list.find(({ id }) => id === eid);
      this.record.list.splice(fromIndex, 1);
      this.record.list.splice(toIndex, 0, element);
    },

    e_edit: function(eid) {
      this.scrollMeTo('porto');
      var element = this.record.list.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_payee = element.payee;
      this.e_org_particulars = element.particulars;
      this.e_org_price = element.price;
      this.e_org_qty = element.qty;
      this.e_org_check_remark = element.check_remark;

      this.e_id = eid;
      this.e_payee = element.payee;
      this.e_particulars = element.particulars;
      this.e_price = element.price;
      this.e_qty = element.qty;
      this.e_check_remark = element.check_remark;

      this.e_editing = true;
    },

    e_del: function(eid) {
      var index = this.record.list.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.list.splice(index, 1);
      }

      this.record.total = this.count_total(this.record.list);
    },

    e_add_criterion: function() {
      if (
        this.e_payee.trim() == "" ||
        this.e_particulars.trim() == "" 
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

        var ad = {
          id: ++this.e_sn,
          sn: ++this.e_sn,
          payee: this.e_payee,
          particulars: this.e_particulars,
          price: this.e_price,
          qty: this.e_qty,
          status : 1,
          check_remark: this.e_check_remark,
        };
        this.record.list.push(ad);
      
        this.record.total = this.count_total(this.record.list);

        this.e_clear_edit();

    },

    count_total: function(list) {
      var total = 0.0;
      for (let index = 0; index < list.length; index++) {
        total += list[index].qty * list[index].price;
      }
      return total;
    },

    e_cancel_criterion: function() {
      this.e_payee = this.e_org_payee;
      this.e_particulars = this.e_org_particulars;
      this.e_price = this.e_org_price;
      this.e_qty = this.e_org_qty;
      this.e_check_remark = this.e_org_check_remark;

      this.e_clear_edit();
    },

    e_update_criterion: function() {
      if (this.e_payee.trim() == "" || this.e_particulars.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.record.list.find(({ id }) => id === this.e_org_id);
      element.payee = this.e_payee;
      element.particulars = this.e_particulars;
      element.price = this.e_price;
      element.qty = this.e_qty;
      element.check_remark = this.e_check_remark;

      this.record.total = this.count_total(this.record.list);
    
      this.e_clear_edit();
    },

    e_clear_edit: function() {

      this.e_org_id = 0;
      this.e_org_payee = "";
      this.e_org_particulars = "";
      this.e_org_price = 0;
      this.e_org_qty = 0;
      this.e_org_check_remark = "";

      this.e_payee = "";
      this.e_particulars = "";
      this.e_price = 0;
      this.e_qty = 0;
      this.e_check_remark = "";

      this.e_editing = false;
    },

    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: 'smooth' });
  
    },
  },
});
