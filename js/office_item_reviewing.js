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
        .get("api/office_item_reviewing")
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

    
    update_qty: async function(record) {
      let _this = this;

      var form_Data = new FormData();
      var token = localStorage.getItem("token");

      form_Data.append("jwt", token);
      form_Data.append("list", JSON.stringify(record.list));

      await axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/office_item_update_qty",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          console.log(response.data);
          _this.record.list = response.data;
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

      form_Data.append("crud", "OP Send To MD");
      form_Data.append("id", id);
      form_Data.append("remark", "");
      form_Data.append("info_account", this.record.info_account);
      form_Data.append("info_category", this.record.info_category);
      form_Data.append("sub_category", this.record.sub_category);
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

      form_Data.append("crud", "Approver Approved");
      form_Data.append("id", id);
      form_Data.append("list", JSON.stringify(this.record.list));
      form_Data.append("remark", "");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/office_item_action",
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

    rejectReceiveRecord_Checker: function(id, status) {
        let _this = this;
        targetId = this.record.id;
        var form_Data = new FormData();
  
        var token = localStorage.getItem("token");
        form_Data.append("jwt", token);
        if(status == 3)
          form_Data.append("crud", "OP Review Reject To Checker");
        if(status == 4)
          form_Data.append("crud", "MD Review Reject To Checker");
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

    rejectReceiveRecord: function(id, status) {
      let _this = this;
      targetId = this.record.id;
      var form_Data = new FormData();

      var token = localStorage.getItem("token");
      form_Data.append("jwt", token);
      if(status == 3)
        form_Data.append("crud", "Approver Reject");
      if(status == 4)
        form_Data.append("crud", "Approver Reject");
      form_Data.append("id", id);
      form_Data.append("list", JSON.stringify(this.record.list));
      form_Data.append("remark", this.reject_reason);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/office_item_action",
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

    detail: async function() {
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

      await this.update_qty(this.record);
      
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

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Approve and then send to MD for approve",
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

      Swal.fire({
        title: "Are you sure to proceed this action?",
        text: "Approve and then go to the releasing step",
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
          _this.rejectReceiveRecord(this.proof_id, this.record.status);

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
          title: "Are you sure to proceed this action?",
          text: "Reject and send back to checker",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.value) {
            _this.submit = true;
            _this.rejectReceiveRecord_Checker(this.proof_id, this.record.status);
  
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
  },
});
