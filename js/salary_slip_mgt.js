var app = new Vue({
  el: "#app",
  data: {
    submit: false,
    sn: 0,
    sn1: 0,
    sn2: 0,
    agenda: [],
    agenda1: [],
    agenda2: [],

    position: [],
    title: [],
    department: "",
    title_id: 0,

    confirmed: false,

    // data
    type: 0,
    version: "",
    category: "",
    criterion: "",

    org_category: "",
    org_criterion: "",
    org_id: 0,
    org_type: 0,

    editing: false,

    // paging
    page: 1,
    //perPage: 5,
    pg:0,
    pages: [],

    perPage: 10,

    receive_records: [],

    salary_records:[],
    salary_mgt:[],
    employee:[],
    date_start:"",
    date_end:"",

    salary_per_month:"",
    salary_per_day:"",
    salary_per_minute:"",

    detail_plus:[
      {
        "category": "Salary",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 1,
      }, 
      {
        "category": "Commission",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 2,
      }, 
      {
        "category": "Overtime",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 3,
      }, 
      {
        "category": "Night Differencial",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 4,
      }, 
      {
        "category": "Bonus",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 5,
      }, 
      {
        "category": "Allowance",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 6,
      }, 
      {
        "category": "Borrow Money",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 7,
      }, 
    ],

    p_order : 7,

    detail_minus:[
      {
        "category": "SSS",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 1,
      }, 
      {
        "category": "Philhealth",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 2,
      }, 
      {
        "category": "Pag-IBIG",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 3,
      }, 
      {
        "category": "Tax Withheld",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 4,
      }, 
      {
        "category": "Late",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 5,
      }, 
      {
        "category": "Undertime",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 6,
      }, 
      {
        "category": "Absent",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 7,
      }, 
      {
        "category": "Half Day",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 8,
      }, 
      {
        "category": "Pay Borrowed Money",
        "amount": "",
        "type" : 1,
        "remark":"",
        "order" : 9,
      }, 
    ],

    m_order : 9,

    other:[],

    o_order : 0,   

    cust_category:"",
    cust_amount:"",
    cust_remark:"",

    view_detail: false,
    record: {},
    proof_id: 0,


    // editing
    e_title: [],

    e_sn: 0,
    e_sn1: 0,
    e_sn2: 0,

    e_org_category: "",
    e_org_criterion: "",
    e_org_id: 0,
    e_org_type: 0,

    e_type: 0,
    e_version: "",
    e_category: "",
    e_criterion: "",

    e_department: "",

    e_tid: 0,

    e_editing: false,

    // search
    sdate:"",
    edate:"",
    keyword: "",
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
            case "kw":
              _this.keyword = decodeURI(tmp[1]);
              break;
            case "sdate":
              _this.sdate = tmp[1];
              break;
            case "edate":
              _this.edate = tmp[1];
              break;
            case "pg":
              _this.pg = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
        }
      });
    }

    this.getSalaryMgt();
    this.get_positions();
    this.getLeaveCredit();
  },

  computed: {
    displayedRecord() {
    if(this.pg == 0)
        this.filter_apply();

      this.setPages();
      return this.paginate(this.receive_records);
    },

    detail_plus_sum() {
      var num = 0;
      for(var i = 0; i < this.detail_plus.length; i++) {
        if(!isNaN(this.detail_plus[i]['amount']) && this.detail_plus[i]['amount'] != "")
          num += parseFloat(this.detail_plus[i]['amount']);
      }
      
      return num.toLocaleString('en-US', {maximumFractionDigits:2});
    },

    detail_minus_sum() {
      var num = 0;
      for(var i = 0; i < this.detail_minus.length; i++) {
        if(!isNaN(this.detail_minus[i]['amount']) && this.detail_minus[i]['amount'] != "")
          num += parseFloat(this.detail_minus[i]['amount']);
      }
      
      return num.toLocaleString('en-US', {maximumFractionDigits:2});
    },

    detail_sum() {
      var num = 0;

      for(var i = 0; i < this.detail_plus.length; i++) {
        if(!isNaN(this.detail_plus[i]['amount']) && this.detail_plus[i]['amount'] != "")
          num += parseFloat(this.detail_plus[i]['amount']);
      }
        

      for(var i = 0; i < this.detail_minus.length; i++) {
        if(!isNaN(this.detail_minus[i]['amount']) && this.detail_minus[i]['amount'] != "")
          num -= parseFloat(this.detail_minus[i]['amount']);
      }
      
      return num.toLocaleString('en-US', {maximumFractionDigits:2});
    },



  },

  mounted() {
    
  },

  watch: {

    receive_records() {
      console.log("Vue watch receive_records");
      this.setPages();
    },
    
    department() {
      this.title = this.shallowCopy(
        this.position.find((element) => element.did == this.department)
      ).items;
    },

    e_department() {
      this.e_title = this.shallowCopy(
        this.position.find((element) => element.did == this.e_department)
      ).items;
    },

    proof_id() {
      this.detail();
    },

    

    employee() {

      if(this.confirmed == true) {
        return;
      }

      this.salary_per_month = "";
      this.salary_per_day = "";
      this.salary_per_minute = "";

      this.salary_mgt = this.shallowCopy(
        this.salary_records.find((element) => element.uid == this.employee)
      );

      if(this.salary_mgt['salary'] != null)
      {
        this.salary_per_month = this.salary_mgt['salary'].toLocaleString('en-US', {maximumFractionDigits:2});
        this.salary_per_day = (this.salary_mgt['salary'] * 12 / 313).toLocaleString('en-US', {maximumFractionDigits:2});
        this.salary_per_minute = (this.salary_mgt['salary'] * 12 / 150240).toLocaleString('en-US', {maximumFractionDigits:2});
      }
    }
  
  },

  methods: {
    search() {
        this.filter_apply();
    },

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

    duplicate: function() {
      this.confirmed = false;
      this.ToggleModal(4, 'o');
    },

    revise: function() {
      this.ToggleModal(3, 'o');
    },

    ToggleModal: function(tag, action) {
      if(tag == "1")
      {
        if(action == "o")
        {
          this.$refs.Modal_1.style.display = 'block';
          this.$refs.Modal_2.style.display = 'none';
          this.$refs.Modal_3.style.display = 'none';
          this.$refs.Modal_4.style.display = 'none';
          this.$refs.mask.style.display = 'block';
        }
      }

      if(tag == "2")
      {
        if(action == "o")
        {
          this.$refs.Modal_1.style.display = 'none';
          this.$refs.Modal_2.style.display = 'block';
          this.$refs.Modal_3.style.display = 'none';
          this.$refs.Modal_4.style.display = 'none';
          this.$refs.mask.style.display = 'block';
        }
      }

      if(tag == "3")
      {
        if(action == "o")
        {
          this.$refs.Modal_1.style.display = 'none';
          this.$refs.Modal_2.style.display = 'none';
          this.$refs.Modal_3.style.display = 'block';
          this.$refs.Modal_4.style.display = 'none';
          this.$refs.mask.style.display = 'block';
        }
      }

      if(tag == "4")
      {
        if(action == "o")
        {
          this.$refs.Modal_1.style.display = 'none';
          this.$refs.Modal_2.style.display = 'none';
          this.$refs.Modal_3.style.display = 'none';
          this.$refs.Modal_4.style.display = 'block';
          this.$refs.mask.style.display = 'block';
        }
      }

      if(action == "c")
      {
        this.$refs.Modal_1.style.display = 'none';
        this.$refs.Modal_2.style.display = 'none';
        this.$refs.Modal_3.style.display = 'none';
        this.$refs.Modal_4.style.display = 'none';
        this.$refs.mask.style.display = 'none';

        this.reset_all();
      }
    },

    reload_detail: function() {
      this.ToggleModal(2, 'o');
    },

    filter_apply: function() {
        let _this = this;
  
        window.location.href =
          "salary_slip_mgt?" +
          "kw=" +
          _this.keyword +
          "&sdate=" +
          _this.sdate +
          "&edate=" +
          _this.edate +
          "&pg=" +
          _this.page;
      },

      cancel: function() {
        this.ToggleModal(0, 'c');
      },

      cancel_3: function() {
        this.ToggleModal(0, 'c');
      },

      cancel_4: function() {
        this.ToggleModal(0, 'c');
      },

      reload: function() {
        this.getSalaryMgt();
        this.get_positions();
        this.getLeaveCredit();
      },

      reset_all: function() {
        this.detail_plus = [
          {
            "category": "Salary",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 1,
          }, 
          {
            "category": "Commission",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 2,
          }, 
          {
            "category": "Overtime",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 3,
          }, 
          {
            "category": "Night Differencial",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 4,
          }, 
          {
            "category": "Bonus",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 5,
          }, 
          {
            "category": "Allowance",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 6,
          }, 
          {
            "category": "Borrow Money",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 7,
          }, 
        ];
    
        this.p_order = 7;
    
        this.detail_minus = [
          {
            "category": "SSS",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 1,
          }, 
          {
            "category": "Philhealth",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 2,
          }, 
          {
            "category": "Pag-IBIG",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 3,
          }, 
          {
            "category": "Tax Withheld",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 4,
          }, 
          {
            "category": "Late",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 5,
          }, 
          {
            "category": "Undertime",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 6,
          }, 
          {
            "category": "Absent",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 7,
          }, 
          {
            "category": "Half Day",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 8,
          }, 
          {
            "category": "Pay Borrowed Money",
            "amount": "",
            "type" : 1,
            "remark":"",
            "order" : 9,
          }, 
        ];
    
        this.m_order = 9;
    
        this.other = [];
    
        this.o_order = 0;

        this.salary_mgt = [];
        this.date_start = "";
        this.date_end = "";
        this.employee = "";



    this.editing = false;
      this.submit = false;
      this.confirmed = false;

      },

      refresh_other() {
    
        for(var i = 0; i < this.other.length; i++) {
          var num = "";
          //if((!isNaN(this.other[i]['previous'])) && (!isNaN(this.other[i]['payment'])) && (this.other[i]['previous'] != "" && this.other[i]['payment'] != ""))
          if(this.other[i]['previous'] == "" && this.other[i]['payment'] == "")
          {
            this.other[i]['remark'] = "";
          }
          else
          {
            num = parseFloat(this.other[i]['previous'] == "" ? 0 : this.other[i]['previous']) - parseFloat(this.other[i]['payment'] == "" ? 0 : parseFloat(this.other[i]['payment']));
            this.other[i]['remark'] = num.toLocaleString('en-US', {maximumFractionDigits:2});  
          }
        }
  
        return "";
      },

    getLeaveCredit: function() {
      let _this = this;

      const params = {
        kw: _this.keyword,
        sdate: _this.sdate,
        edate: _this.edate,
        pg: _this.page,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/salary_slip_mgt", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if (_this.receive_records.length > 0) {
            //_this.proof_id = _this.receive_records[0].id;
            //_this.detail();
            if(_this.pg !== 0)
            { 
              _this.page = _this.pg;
              _this.setPages();
            }
            
          }
        })
        .catch(function(error) {
          console.log(error);
        });

      _this.proof_id = 0;
    },

    view_detai:function(){
        if (this.proof_id == 0) {
            Swal.fire({
                text: "Please select row to view",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }else
          {
            window.jQuery(".mask").toggle();
            window.jQuery('#Modal_2').toggle();
          }
        
    },

    edit_detai:function(){
        if (this.proof_id == 0) {
            Swal.fire({
                text: "Please select row to edit",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }
          
          if(this.record.cited != 0)
          {
            Swal.fire({
              text: "This template has been used by someone's evaluation, so it cannot be edited. Instead, user might try to duplicate it and do editing on the duplicated template.",
              icon: "warning",
              confirmButtonText: "OK",
            });
            return;
          };

          
          window.jQuery(".mask").toggle();
          window.jQuery('#Modal_3').toggle();
          
        
    },

    detail: function() {
      let _this = this;

      if (this.proof_id == 0) {
        //this.view_detail = false;
        return;
      }

      this.confirmed = true;

      this.salary_per_month = "";
      this.salary_per_day = "";
      this.salary_per_minute = "";

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      this.employee = this.shallowCopy(
        this.salary_records.find((element) => element.uid == this.record.uid)
      ).uid;

      let salary = this.record['salary_then'];

      if(salary != null && salary != '')
      {
        this.salary_per_month = salary.toLocaleString('en-US', {maximumFractionDigits:2});
        this.salary_per_day = (salary * 12 / 313).toLocaleString('en-US', {maximumFractionDigits:2});
        this.salary_per_minute = (salary * 12 / 150240).toLocaleString('en-US', {maximumFractionDigits:2});
      }

      this.date_start = this.record.start_date;
      this.date_end = this.record.end_date;

      this.detail_plus = this.record.detail_plus;
      this.p_order = this.detail_plus.length;

      this.detail_minus = this.record.detail_minus;
      this.m_order = this.detail_minus.length;

      this.other = this.record.other;
      this.o_order = this.other.length;


      this.ToggleModal(2, 'o');
    },

    create_slip() {
      if(this.salary_mgt['uid'] == undefined) {
        Swal.fire({
          text: "Please select Employee Name",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.date_start == '') {
        Swal.fire({
          text: "Please select Salary for Start Date",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.date_end == '') {
        Swal.fire({
          text: "Please select Salary for End Date",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("uid", this.salary_mgt['uid']);
      form_Data.append("start_date", this.date_start);
      form_Data.append("end_date", this.date_end);

      form_Data.append("salary", this.salary_mgt['salary']);
      form_Data.append("title", this.salary_mgt['title']);
      form_Data.append("department", this.salary_mgt['department']);

      form_Data.append("detail_plus", JSON.stringify(this.detail_plus));
      form_Data.append("detail_minus", JSON.stringify(this.detail_minus));
      form_Data.append("other", JSON.stringify(this.other));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/salary_slip_mgt_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.cancel();
          _this.reload();

        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

        });
    },

    edit_slip() {
      if(this.salary_mgt['uid'] == undefined) {
        Swal.fire({
          text: "Please select Employee Name",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.date_start == '') {
        Swal.fire({
          text: "Please select Salary for Start Date",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if(this.date_end == '') {
        Swal.fire({
          text: "Please select Salary for End Date",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("pid", this.proof_id);
      form_Data.append("uid", this.salary_mgt['uid']);
      form_Data.append("start_date", this.date_start);
      form_Data.append("end_date", this.date_end);

      form_Data.append("detail_plus", JSON.stringify(this.detail_plus));
      form_Data.append("detail_minus", JSON.stringify(this.detail_minus));
      form_Data.append("other", JSON.stringify(this.other));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/salary_slip_mgt_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.cancel();
          _this.reload();

        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

        });
    },

    getSalaryMgt() {
      let _this = this;

      const params = {
        kw: '',
        dp: '',
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/salary_mgt", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
        .then(function(response) {
          console.log(response.data);
          _this.salary_records = response.data;
   
        })
        .catch(function(error) {
          console.log(error);
        });

    },


    remove(status) {
      if(status == -1)
        wording = 'delete';
      if(status == 3)
        wording = 'withdraw';

      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select a record to " + wording,
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      let _this = this;
      
      Swal.fire({
        title: "Delete",
        text: "Are you sure to " + wording + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          if (_this.submit == true) return;

          _this.submit = true;

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          form_Data.append("jwt", token);
          form_Data.append("pid", _this.proof_id);
          form_Data.append("type", status);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/salary_slip_mgt_delete",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              Swal.fire({
                html: response.data.message,
                icon: "info",
                confirmButtonText: "OK",
              });

              _this.cancel();
              _this.reload();
            })
            .catch(function(error) {
              //handle error
              Swal.fire({
                text: JSON.stringify(error),
                icon: "info",
                confirmButtonText: "OK",
              });

        
            });
        } else {
          return;
        }
      });
    },

    update_template() {
      if (this.e_tid == 0 || this.record.version.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("pid", this.record.id);
      form_Data.append("title_id", this.e_tid);
      form_Data.append("version", this.record.version);

      form_Data.append("agenda", JSON.stringify(this.record.agenda));
      form_Data.append("agenda1", JSON.stringify(this.record.agenda1));
      form_Data.append("agenda2", JSON.stringify(this.record.agenda2));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/performance_template_update",
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

        window.jQuery(".mask").toggle();
        window.jQuery('#Modal_3').toggle();
        
    },

    add_criterion: function() {
      if (
        this.type == 0 ||
        this.category.trim() == "" ||
        this.criterion.trim() == ""
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.type == 1) {
        var ad = {
          id: ++this.sn,
          category: this.category,
          criterion: this.criterion,
        };
        this.agenda.push(ad);
      }

      if (this.type == 2) {
        var ad = {
          id: ++this.sn1,
          category: this.category,
          criterion: this.criterion,
        };
        this.agenda1.push(ad);
      }

      if (this.type == 3) {
        var ad = {
          id: ++this.sn2,
          category: this.category,
          criterion: this.criterion,
        };
        this.agenda2.push(ad);
      }

      this.criterion = "";
    },

    clear_edit: function() {
      this.org_type = 0;
      this.org_id = 0;
      this.org_category = "";
      this.org_criterion = "";
      this.editing = false;
    },

    cancel_criterion: function() {
      this.type = this.org_type;
      this.category = "";
      this.criterion = "";

      this.clear_edit();
    },

    del_plus_detail : function(id) {
      var index = this.detail_plus.findIndex(({ order }) => order === id);
      if (index > -1) {
        this.detail_plus.splice(index, 1);
      }
    },

    add_plus_detail: function() {
      this.p_order = this.p_order + 1;
      obj = {
        "category": '',
        "amount": '',
        "type" : 0,
        "remark": '',
        "order" : this.p_order
      }, 

      this.detail_plus.push(obj);
    },

    add_minus_detail: function() {
      this.m_order = this.m_order + 1;
      obj = {
        "category": '',
        "amount": '',
        "type" : 0,
        "remark": '',
        "order" : this.m_order
      }, 

      this.detail_minus.push(obj);
    },

    add_other_detail: function() {
      this.o_order = this.o_order + 1;
      obj = {
        "category": '',
        "previous" : '',
        "payment": '',
        "type" : 0,
        "remark": '',
        "order" : this.o_order
      }, 

      this.other.push(obj);
    },

    del_other_detail : function(id) {
      var index = this.other.findIndex(({ order }) => order === id);
      if (index > -1) {
        this.other.splice(index, 1);
      }
    },

    del_minus_detail : function(id) {
      var index = this.detail_minus.findIndex(({ order }) => order === id);
      if (index > -1) {
        this.detail_minus.splice(index, 1);
      }
    },

    update_criterion: function() {
      if (this.category.trim() == "" || this.criterion.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.org_type == 1) {
        var element = this.agenda.find(({ id }) => id === this.org_id);
        element.category = this.category;
        element.criterion = this.criterion;
      }

      if (this.org_type == 2) {
        var element = this.agenda1.find(({ id }) => id === this.org_id);
        element.category = this.category;
        element.criterion = this.criterion;
      }

      if (this.org_type == 3) {
        var element = this.agenda2.find(({ id }) => id === this.org_id);
        element.category = this.category;
        element.criterion = this.criterion;
      }

      this.criterion = "";

      this.clear_edit();
    },

    get_positions: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/position_get", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.position = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    set_agenda: function() {
      this.agenda = [];
      this.agenda1 = [];
      this.agenda2 = [];
    },

    set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.agenda.find(({ id }) => id === eid);
      this.agenda.splice(fromIndex, 1);
      this.agenda.splice(toIndex, 0, element);
    },

    set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.agenda.length - 1) toIndex = this.agenda.length - 1;

      var element = this.agenda.find(({ id }) => id === eid);
      this.agenda.splice(fromIndex, 1);
      this.agenda.splice(toIndex, 0, element);
    },

    edit: function(eid) {
      this.scrollMeTo('addto');
      this.type = 1;
      var element = this.agenda.find(({ id }) => id === eid);

      this.org_id = eid;
      this.org_category = element.category;
      this.org_criterion = element.criterion;

      this.category = element.category;
      this.criterion = element.criterion;

      this.org_type = 1;

      this.editing = true;
    },

    del: function(eid) {
      var index = this.agenda.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.agenda.splice(index, 1);
      }
    },

    set_up1: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.agenda1.find(({ id }) => id === eid);
      this.agenda1.splice(fromIndex, 1);
      this.agenda1.splice(toIndex, 0, element);
    },

    set_down1: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.agenda1.length - 1) toIndex = this.agenda1.length - 1;

      var element = this.agenda1.find(({ id }) => id === eid);
      this.agenda1.splice(fromIndex, 1);
      this.agenda1.splice(toIndex, 0, element);
    },

    edit1: function(eid) {
      this.scrollMeTo('addto');
      this.type = 2;
      var element = this.agenda1.find(({ id }) => id === eid);

      this.org_id = eid;
      this.org_category = element.category;
      this.org_criterion = element.criterion;

      this.category = element.category;
      this.criterion = element.criterion;

      this.org_type = 2;
      
      this.editing = true;
    },

    del1: function(eid) {
      var index = this.agenda1.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.agenda1.splice(index, 1);
      }
    },

    set_up2: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.agenda2.find(({ id }) => id === eid);
      this.agenda2.splice(fromIndex, 1);
      this.agenda2.splice(toIndex, 0, element);
    },

    set_down2: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.agenda2.length - 1) toIndex = this.agenda2.length - 1;

      var element = this.agenda2.find(({ id }) => id === eid);
      this.agenda2.splice(fromIndex, 1);
      this.agenda2.splice(toIndex, 0, element);
    },

    edit2: function(eid) {
      this.scrollMeTo('addto');
      this.type = 3;
      var element = this.agenda2.find(({ id }) => id === eid);

      this.org_id = eid;
      this.org_category = element.category;
      this.org_criterion = element.criterion;

      this.category = element.category;
      this.criterion = element.criterion;

      this.org_type = 3;
      
      this.editing = true;
    },

    del2: function(eid) {
      var index = this.agenda2.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.agenda2.splice(index, 1);
      }
    },

    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: 'smooth' });
  
    },

    reset: function() {
      this.submit = false;

      this.agenda = [];
      this.agenda1 = [];
      this.agenda2 = [];

      this.sn = 0;
      this.sn1 = 0;
      this.sn2 = 0;

      this.type = 0;
      this.version = "";
      this.category = "";
      this.criterion = "";

      this.org_category = "";
      this.org_criterion = "";
      this.org_id = 0;
      this.org_type = 0;

      this.editing = false;
      this.submit = false;

      this.e_type = 0;
      this.e_category = "";
      this.e_criterion = "";

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

    // editing

    e_add_criterion: function() {
      if (
        this.e_type == 0 ||
        this.e_category.trim() == "" ||
        this.e_criterion.trim() == ""
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.e_type == 1) {
        var ad = {
          id: ++this.e_sn,
          category: this.e_category,
          criterion: this.e_criterion,
        };
        this.record.agenda.push(ad);
      }

      if (this.e_type == 2) {
        var ad = {
          id: ++this.e_sn1,
          category: this.e_category,
          criterion: this.e_criterion,
        };
        this.record.agenda1.push(ad);
      }

      if (this.e_type == 3) {
        var ad = {
          id: ++this.e_sn2,
          category: this.e_category,
          criterion: this.e_criterion,
        };
        this.record.agenda2.push(ad);
      }

      this.e_criterion = "";
    },

    e_cancel_criterion: function() {
      this.e_type = this.e_org_type;
      this.e_category = "";
      this.e_criterion = "";

      this.e_clear_edit();
    },

    e_update_criterion: function() {
      if (this.e_category.trim() == "" || this.e_criterion.trim() == "") {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      if (this.e_org_type == 1) {
        var element = this.record.agenda.find(({ id }) => id === this.e_org_id);
        element.category = this.e_category;
        element.criterion = this.e_criterion;
      }

      if (this.e_org_type == 2) {
        var element = this.record.agenda1.find(
          ({ id }) => id === this.e_org_id
        );
        element.category = this.e_category;
        element.criterion = this.e_criterion;
      }

      if (this.e_org_type == 3) {
        var element = this.record.agenda2.find(
          ({ id }) => id === this.e_org_id
        );
        element.category = this.e_category;
        element.criterion = this.e_criterion;
      }

      this.e_criterion = "";

      this.e_clear_edit();
    },

    e_clear_edit: function() {
      this.e_org_type = 0;
      this.e_org_id = 0;
      this.e_org_category = "";
      this.e_org_criterion = "";

      this.e_type = 0;
      this.e_category = "";
      this.e_criterion = "";

      this.e_editing = false;
    },

    e_set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.record.agenda.find(({ id }) => id === eid);
      this.record.agenda.splice(fromIndex, 1);
      this.record.agenda.splice(toIndex, 0, element);
    },

    e_set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.agenda.length - 1)
        toIndex = this.record.agenda.length - 1;

      var element = this.record.agenda.find(({ id }) => id === eid);
      this.record.agenda.splice(fromIndex, 1);
      this.record.agenda.splice(toIndex, 0, element);
    },

    e_edit: function(eid) {
      this.scrollMeTo('porto');
      var element = this.record.agenda.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_category = element.category;
      this.e_org_criterion = element.criterion;

      this.e_category = element.category;
      this.e_criterion = element.criterion;

      this.e_org_type = 1;
      this.e_type = 1;

      this.e_editing = true;
    },

    e_del: function(eid) {
      var index = this.record.agenda.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.agenda.splice(index, 1);
      }
    },

    e_set_up1: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.record.agenda1.find(({ id }) => id === eid);
      this.record.agenda1.splice(fromIndex, 1);
      this.record.agenda1.splice(toIndex, 0, element);
    },

    e_set_down1: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.agenda1.length - 1)
        toIndex = this.record.agenda1.length - 1;

      var element = this.record.agenda1.find(({ id }) => id === eid);
      this.record.agenda1.splice(fromIndex, 1);
      this.record.agenda1.splice(toIndex, 0, element);
    },

    e_edit1: function(eid) {
      this.scrollMeTo('porto');
      var element = this.record.agenda1.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_category = element.category;
      this.e_org_criterion = element.criterion;

      this.e_category = element.category;
      this.e_criterion = element.criterion;

      this.e_org_type = 2;
      this.e_type = 2;

      this.e_editing = true;
    },

    e_del1: function(eid) {
      var index = this.record.agenda1.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.agenda1.splice(index, 1);
      }
    },


    e_set_up2: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.record.agenda2.find(({ id }) => id === eid);
      this.record.agenda2.splice(fromIndex, 1);
      this.record.agenda2.splice(toIndex, 0, element);
    },

    e_set_down2: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.record.agenda2.length - 1)
        toIndex = this.record.agenda2.length - 1;

      var element = this.record.agenda2.find(({ id }) => id === eid);
      this.record.agenda2.splice(fromIndex, 1);
      this.record.agenda2.splice(toIndex, 0, element);
    },

    e_edit2: function(eid) {
      this.scrollMeTo('porto');
      var element = this.record.agenda2.find(({ id }) => id === eid);

      this.e_org_id = eid;
      this.e_org_category = element.category;
      this.e_org_criterion = element.criterion;

      this.e_category = element.category;
      this.e_criterion = element.criterion;

      this.e_org_type = 3;
      this.e_type = 3;

      this.e_editing = true;
    },

    e_del2: function(eid) {
      var index = this.record.agenda2.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.record.agenda2.splice(index, 1);
      }
    },
  },
});
