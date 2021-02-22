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

    baseURL: "https://storage.cloud.google.com/feliiximg/",

    proof_remark: "",
    reject_reason: "",

    proof_id: 0,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    perPage: 10000,

    inventory: [
      {name: 'Allowance', id: 'Allowance'},
      {name: 'Commission', id: 'Commission'},
      {name: 'Delivery', id: 'Delivery'},
      {name: 'Maintenance', id: 'Maintenance'},
      {name: 'Meals', id: 'Meals'},
      {name: 'Misc', id: 'Misc'},
      {name: 'Others', id: 'Others'},
      {name: 'Outsource', id: 'Outsource'},
      {name: 'Petty cash', id: 'Petty cash'},
      {name: 'Products', id: 'Products'},
      {name: 'Supplies', id: 'Supplies'},
      {name: 'Tools and Materials', id: 'Tools and Materials'},
      {name: 'Transportation', id: 'Transportation'},
    ],

    is_approval: false,
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
              _this.proof_id = 0;
          }
        })
        .catch(function(error) {
          console.log(error);
        });
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
            icon: "warning",
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
            icon: "warning",
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
            icon: "warning",
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
      
      this.reject_reason = "";
      this.view_detail = true;
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

      if ((this.record.sub_category.trim() === "") && (this.record.info_category.trim() === "Marketing" || this.record.info_category.trim() === "Office Needs" || this.record.info_category.trim() === "Others" || this.record.info_category.trim() === "Projects" || this.record.info_category.trim() === "Store")) {
        Swal.fire({
          text: "Please select sub category!",
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

      if (this.record.info_category.trim() === "") {
        Swal.fire({
          text: "Please select category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.record.sub_category.trim() === "") {
        Swal.fire({
          text: "Please select sub category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

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

      if (this.record.info_remark.trim() === "") {
        Swal.fire({
          text: "Please select remark!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
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
  },
});
