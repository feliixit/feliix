var app = new Vue({
  el: "#app",
  data: {
    name: "",
    month1: "",

    picked: "A",
    view_detail: false,
    who_detail : "",

    submit: false,

    receive_records: [],
    record: {},

    baseURL: "https://storage.googleapis.com/feliiximg/",

    proof_remark: "",

    proof_id: 0,
    id: 0,

    // paging
    page: 1,
    p_page:0,
    //perPage: 10,
    pages: [],

    pages_10: [],

    perPage: 5,

    total: 0,

    fil_keyowrd: "",

    is_approval: false,

    payment_method:'',
    bank_name: '',
    check_number:'',
    bank_account:'',

    view_proof : false,

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
            case "id":
              _this.id = tmp[1];
              break;
            case "fk":
              _this.fil_keyowrd = tmp[1];
              break;
           
            default:
              console.log(`Too many args`);
          }
        }
      });
    }


    this.getUserName();
  },

  computed: {
    displayedRecord() {
      this.setPages();
      return this.paginate(this.receive_records);
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
      console.log(this.proof_id);
      this.detail(this.proof_id);
    },

    page() {
      if(this.page == 0)
        return;

      if(this.p_page != this.page){
        this.getLeaveCredit();
        this.p_page = this.page;
      }
    },
  },

  methods: {
    setPages() {
      console.log("setPages");
      this.pages = [];
      this.pages_10 = [];

      let numberOfPages = Math.ceil(this.total / this.perPage);

      if (numberOfPages == 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },


    paginate: function(posts) {
      console.log("paginate");

      if(this.id == 0)
        this.proof_id = 0;

      if (this.page < 1) this.page = 1;
      if ((this.page > this.pages.length) && this.pages.length > 0) 
        this.page = this.pages.length;

      let tenPages = Math.floor((this.page - 1) / 10);
      if(tenPages < 0)
        tenPages = 0;
      this.pages_10 = [];
      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      this.pages_10 = this.pages.slice(from, to);
/*
      let page = this.page;
      let perPage = this.perPage;
      from = page * perPage - perPage;
      to = page * perPage;
*/
      return this.receive_records;
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

    show_proof : function() {
    
      if(this.record.special == 's' || this.record.special == 'sn')
        this.view_proof = false;
      else
        this.view_proof = true;

      if(this.name.toLowerCase() ==='dennis lin' || this.name.toLowerCase() ==='kristel tan' || this.name.toLowerCase() ==='glendon wendell co' || this.name.toLowerCase() ==='kuan')
        this.view_proof = true;

      if(this.record.username.toLowerCase() == this.name.toLowerCase())
        this.view_proof = true;
    },

    getLeaveCredit: async function(page) {
      let _this = this;

      this.total = 0;

      if(page == 0)
        this.page = 1;

      const params = {
        fk: _this.fil_keyowrd,
        page: _this.page,
        size: _this.perPage,
        pid : _this.id,
      };

      let token = localStorage.getItem("accessToken");

      let u = await axios
        .get("api/downpayment_proof", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if(_this.receive_records.length > 0)
          {
            _this.total = _this.receive_records[0].cnt;
            if(_this.id != 0)
            {
              _this.proof_id = _this.id;
              _this.detail(_this.proof_id);
            }
            
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
          if (_this.name === "Glendon Wendell Co" || 1 == 1)
            _this.is_approval = true;
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

      var finals = document.getElementsByName("record_id");
      for (var i = 0; i < finals.length; i++) {
        if (finals[i].checked)
        {
          finals[i].checked = false;
        }
      }
    },

    showPic(pic) {
      Swal.fire({
        title: "Certificate of Diagnosis",
        text: "Click to close",
        imageUrl: "img/" + pic,
      });
    },

    approveReceiveRecord: function(id) {
      let _this = this;
      //targetId = this.record.id;
      var receive_date = document.getElementById("receive_date").value;
      var amount = document.getElementById("amount").value;
      var invoice = document.getElementById("invoice").value;
      var detail = ""; //document.getElementById("detail").value;
      var remark = document.getElementById("remark").value;

      var form_Data = new FormData();

      form_Data.append("crud", "app");
      form_Data.append("id", id);
      form_Data.append("receive_date", receive_date);
      form_Data.append("amount", amount);
      form_Data.append("invoice", invoice);
      form_Data.append("detail", detail);
      form_Data.append("payment_method", this.payment_method);
      form_Data.append("bank_name", this.bank_name);
      form_Data.append("check_number", this.check_number);
      form_Data.append("bank_account", this.bank_account);
      form_Data.append("remark", remark);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/downpayment_proof_approval",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
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
      //targetId = this.record.id;

      var receive_date = document.getElementById("receive_date").value;
      var amount = document.getElementById("amount").value;
      var invoice = document.getElementById("invoice").value;
      var detail = ""; //document.getElementById("detail").value;
      var remark = document.getElementById("remark").value;

      var form_Data = new FormData();

      form_Data.append("crud", "rej");
      form_Data.append("id", id);
      form_Data.append("receive_date", receive_date);
      form_Data.append("amount", amount);
      form_Data.append("invoice", invoice);
      form_Data.append("detail", detail);
      form_Data.append("remark", remark);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/downpayment_proof_reject",
        data: form_Data,
      })
        .then(function(response) {
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

    detail: function(id) {
      let _this = this;

      this.proof_id = id;

      console.log(this.proof_id);

      //let favorite = [];

      //for (i = 0; i < this.receive_records.length; i++)
      //{
      //  if(this.receive_records[i].is_checked == 1)
      //    favorite.push(this.receive_records[i].sid);
      //}

      if (this.proof_id == 0) {
           //$(window).scrollTop(0);
        this.view_detail = false;

        this.who_detail = "";

        this.unCheckCheckbox();

        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      this.show_proof();

      this.payment_method = '';
      this.bank_name = '';
      this.check_number = '';
      this.bank_account = '';

      this.view_detail = true;

      this.who_detail = '';

      if(this.record.special == 's' && (this.record.kind == '0' || this.record.kind == '1') && this.name == 'Kuan')
        this.who_detail = 's';

      if(this.record.special == 's' && this.record.kind == '2' && this.name == 'Glendon Wendell Co')
        this.who_detail = 'g';

      if(this.record.special == '' && this.name == 'Glendon Wendell Co')
        this.who_detail = 'g';

      if(this.record.special == 'sn' && this.name == 'Manilynne Nicol' && this.record.kind == '0' && this.record.final_amount <= 100000)
        this.who_detail = 't';

      if(this.record.special == 'sn' && this.name == 'Kuan' && this.record.kind == '0' && this.record.final_amount > 100000)
        this.who_detail = 's';

      if(this.record.special == 'sn' && (this.record.kind == '1' || this.record.kind == '2') && this.name == 'Glendon Wendell Co')
        this.who_detail = 'g';

      // if(this.name == 'dereck')
      // this.who_detail = 'g';
    },

    approve: function() {
      let _this = this;

      let favorite = [];

      var approve_record = false;

      for (i = 0; i < this.receive_records.length; i++) {
        if (this.receive_records[i].id == this.proof_id) {
          if (this.receive_records[i].status === "-1") {
            Swal.fire({
              text:
                "Rejected data cannot be approved! Please contact Admin or IT staffs.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          if (this.receive_records[i].status === "1") {
            Swal.fire({
              text:
                "Approved data cannot be approved again! Please contact Admin or IT staffs.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          if(this.receive_records[i].kind !== "2")
          {
            var receive_date = document.getElementById("receive_date").value;
            var amount = document.getElementById("amount").value;
            var invoice = document.getElementById("invoice").value;
            var detail = ""; //document.getElementById("detail").value;
            var remark = document.getElementById("remark").value;

            if (receive_date === "") {
              Swal.fire({
                text:
                  "Date of Receiving required.",
                icon: "warning",
                confirmButtonText: "OK",
              });

              return;
            }
            
            if (amount === "") {
              Swal.fire({
                text:
                  "Amount of Receiving required.",
                icon: "warning",
                confirmButtonText: "OK",
              });

              return;
            }

            if (invoice === "" && this.record.special == "") {
              Swal.fire({
                text:
                  "Invoice Number required.",
                icon: "warning",
                confirmButtonText: "OK",
              });

              return;
            }

            if (this.payment_method === "") {
              Swal.fire({
                text:
                  "Payment Method required.",
                icon: "warning",
                confirmButtonText: "OK",
              });

              return;
            }

            if (this.payment_method === "check" && this.bank_name === "") {
              Swal.fire({
                text:
                  "Bank Name required.",
                icon: "warning",
                confirmButtonText: "OK",
              });

              return;
            }

            if (this.payment_method === "check" && this.check_number === "") {
              Swal.fire({
                text:
                  "Check Number required.",
                icon: "warning",
                confirmButtonText: "OK",
              });

              return;
            }

            if (this.payment_method === "deposit" && this.bank_account === "") {
              Swal.fire({
                text:
                  "Bank Account required.",
                icon: "warning",
                confirmButtonText: "OK",
              });

              return;
            }
          }

          favorite.push(this.receive_records[i].id);
        }
      }

      if (favorite.length < 1) {
        Swal.fire({
          text: "Please select rows to approve!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
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
          _this.approveReceiveRecord(favorite.join(", "));

          _this.resetForm();
          _this.unCheckCheckbox();
        }
      });
    },

    reject: function() {
      let _this = this;

      let favorite = [];

      var approve_record = false;

      for (i = 0; i < this.receive_records.length; i++) {
        if (this.receive_records[i].id == this.proof_id) {
          if (this.receive_records[i].status === "-1") {
            Swal.fire({
              text:
                "Rejected data cannot be rejected again! Please contact Admin or IT staffs.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          if (this.receive_records[i].status === "1") {
            Swal.fire({
              text:
                "Approved data cannot be rejected! Please contact Admin or IT staffs.",
              icon: "warning",
              confirmButtonText: "OK",
            });

            return;
          }

          favorite.push(this.receive_records[i].id);
        }
      }

      if (favorite.length < 1) {
        Swal.fire({
          text: "Please select rows to reject!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      var remark = document.getElementById("remark").value;
      if (remark == "") {
        Swal.fire({
          text: "Please enter Remarks",
          icon: "warning",
          confirmButtonText: "OK",
        });
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
          _this.rejectReceiveRecord(favorite.join(", "));

          _this.resetForm();
          _this.unCheckCheckbox();
        }
      });
    },

    resetForm: function() {
      this.submit = false;
      this.view_detail = false;
      this.who_detail = false;

      this.view_proof = false;

      this.receive_records = [];
      this.record = {};

      this.payment_method="";
      this.bank_name = "";
      this.check_number = "";
      this.bank_account = "";

      this.proof_id = 0;
      this.id = 0;
      this.proof_remark = "";

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
