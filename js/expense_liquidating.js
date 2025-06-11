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
    amount_liquidated: "",

    proof_id: 0,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    perPage: 10000,

    is_approval: false,

    pid:0,
    e_editing: false,

    petty_list: [],

    list_payee: "",
    list_particulars: "",
    list_price: 0,
    list_qty: 0,

    e_org_payee: "",
    e_org_particulars: "",
    e_org_price: 0,
    e_org_qty: 0,

    e_org_id: 0,

    list_sn: 0,

    amount_of_return:"",
    method_of_return:"",
    total_amount_liquidate:"",
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

    list_amonut: function() {
      return this.list_price * this.list_qty;
    },

    sum_amonut: function() {
      let sum = 0.0;
      for (i = 0; i < this.petty_list.length; i++) {
        sum += this.petty_list[i].qty * this.petty_list[i].price;
      }

      this.total_amount_liquidate = sum;
      return sum;
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
        .get("api/expense_liquidating")
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if(_this.receive_records.length > 0)
          {
            
              //_this.proof_id = _this.receive_records[0].id;
              //_this.detail();
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

      form_Data.append("crud", "Liquidated");
      form_Data.append("id", id);
      form_Data.append("remark", this.reject_reason);
      form_Data.append("amount", this.parsenumber(this.amount_liquidated));

      form_Data.append("amount_of_return", this.amount_of_return);
      form_Data.append("method_of_return", this.method_of_return);
      form_Data.append("total_amount_liquidate", this.total_amount_liquidate);
      form_Data.append("items", JSON.stringify(this.petty_list));

      if(this.record.status == 7)
      {
        var favorite = [];
        for(var i = 0; i < this.record.liquidate_items.length; i++)
        {
          if(this.record.liquidate_items[i].is_checked === false)
            favorite.push(this.record.liquidate_items[i].id);
        }
        form_Data.append("items_to_delete", JSON.stringify(favorite));
      }
   
      for( var i = 0; i < this.$refs.file.files.length; i++ ){
        let file = this.$refs.file.files[i];
        form_Data.append('files[' + i + ']', file);
      }

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

      form_Data.append("crud", "Finish Releasing");
      form_Data.append("id", id);
      form_Data.append("remark", "");

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

    rejectReceiveRecord_Checker: function(id) {
        let _this = this;
        targetId = this.record.id;
        var form_Data = new FormData();
  
        var token = localStorage.getItem("token");
        form_Data.append("jwt", token);
  
        form_Data.append("crud", "Void");
        form_Data.append("id", id);
        form_Data.append("remark", this.reject_reason);
  
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

      form_Data.append("crud", "Void");
      form_Data.append("id", id);
      form_Data.append("remark", this.reject_reason);

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

    detail: function() {
      let _this = this;

      //let favorite = [];

      //for (i = 0; i < this.receive_records.length; i++)
      //{
      //  if(this.receive_records[i].is_checked == 1)
      //    favorite.push(this.receive_records[i].sid);
      //}

      if (this.proof_id == 0) {
        // Swal.fire({
        //   text: "Please select row to see the detail!",
        //   icon: "warning",
        //   confirmButtonText: "OK",
        // });

        //$(window).scrollTop(0);
        this.view_detail = false;
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );
      
      if(this.record.status == 7)
        this.reject_reason = this.record.remark_liquidated;

      if(!this.record.amount_liquidated)
        this.amount_liquidated = this.record.amount_liquidated;
      else
        this.amount_liquidated = Number(this.record.amount_liquidated).toLocaleString();

      if(!this.record.total_amount_liquidate)
        this.total_amount_liquidate = this.record.total_amount_liquidate;
      else
        this.total_amount_liquidate = Number(this.record.total_amount_liquidate).toLocaleString();

      if(!this.record.amount_of_return)
        this.amount_of_return = this.record.amount_of_return;
      else
        this.amount_of_return = Number(this.record.amount_of_return).toLocaleString();

        this.method_of_return = this.record.method_of_return;

      this.petty_list = JSON.parse(JSON.stringify(this.record.apply_for_petty_liquidate));

      this.view_detail = true;
    },

    caculate_total: function() {
      var total = Math.min(this.parsenumber(this.record.total), this.parsenumber(this.sum_amonut));

      if(this.parsenumber(this.amount_liquidated) > total)
      {
        Swal.fire({
          text: 'User is not allowed to liquidate the amount more than the minimal of “Total Amount Requested” and “Amount in Liquidation Listing”.',
          icon: 'warning',
          confirmButtonText: 'OK'
        });

        this.amount_liquidated = total;
        this.amount_of_return = this.parsenumber(this.record.total) - this.parsenumber(this.amount_liquidated);

        return false;
      }
      else
      {
        this.amount_of_return = this.parsenumber(this.record.total) - this.parsenumber(this.amount_liquidated);

        return true;
      }

    },
    
    _edit: function(eid) {
      var element = this.petty_list.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_payee = element.payee;
      this.e_org_particulars = element.particulars;
      this.e_org_price = element.price;
      this.e_org_qty = element.qty;
      this.e_org_check_remark = element.check_remark;

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


    e_clear_edit: function() {

      this.e_org_id = 0;
      this.e_org_payee = "";
      this.e_org_particulars = "";
      this.e_org_price = 0;
      this.e_org_qty = 0;

      this.list_payee = "";
      this.list_particulars = "";
      this.list_price = 0;
      this.list_qty = 0;

      this.e_editing = false;
    },

    approve_op: function() {
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

      if(!this.amount_liquidated)
      {
        Swal.fire({
          text: 'Amount format invalid',
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        //this.err_msg = 'Location Photo required';
        //$(window).scrollTop(0);
        return false;
      }

      if(isNaN(this.parsenumber(this.amount_liquidated)))
      {
        Swal.fire({
          text: 'Amount format invalid',
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        //this.err_msg = 'Location Photo required';
        //$(window).scrollTop(0);
        return false;
      }

      var favorite = [];

      if(this.record.status == 7)
      {
        for(var i = 0; i < this.record.liquidate_items.length; i++)
        {
          if(this.record.liquidate_items[i].is_checked === true)
            favorite.push(this.record.liquidate_items[i].id);
        }
      }

      if (!this.$refs.file.files[0] && this.reject_reason.trim() == '' && favorite.length == 0)
          {
            Swal.fire({
              text: 'File Attachment required',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Location Photo required';
            //$(window).scrollTop(0);
            return false;
          }

      if(!this.caculate_total())
        return;

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Finish liquidation and send to verifier for verify",
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

      if (!this.$refs.file.files[0])
        {
          Swal.fire({
            text: 'File Attachment required',
            icon: 'warning',
            confirmButtonText: 'OK'
          })
          //this.err_msg = 'Location Photo required';
          //$(window).scrollTop(0);
          return false;
        }

      Swal.fire({
        title: "Are you sure to approve?",
        text: "Are you sure to approve apply?",
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
        title: "Are you sure to reject?",
        text: "Are you sure to reject apply?",
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

    reject_checker: function() {
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
          title: "Are you sure to reject?",
          text: "Are you sure to reject apply?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.value) {
            _this.submit = true;
            _this.rejectReceiveRecord_Checker(this.proof_id);
  
            _this.resetForm();
          }
        });
      },
   

    resetForm: function() {
      this.record = [];
      this.reject_reason = "";
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

    // parse if string, then remove comma and return as number
    // if number, return as number
    parsenumber: function(nu) {
      if(nu === null || nu === undefined || nu === '')
        return 0;

      if(typeof nu === 'string')
        return parseFloat(nu.replace(/,/g, ""));
      else
        return parseFloat(nu);
    },
    
    validateNumber: function(obj) {
      var number = obj;

      if (isNaN(parseFloat(number))) {
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
          sn: this.list_sn,
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

  },
});
