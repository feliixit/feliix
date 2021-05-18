var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    employee: "",
    employees: [],

    templates: [],
    template: "",

    review_month: "",
    review_next_month: "",

    editing: false,

    // paging
    page: 1,
    //perPage: 5,
    pg: 0,
    pages: [],

    perPage: 10,

    receive_records: [],
    view_detail: false,
    record: {},
    proof_id: 0,


    // evaluate
    evals:{},
    avg:10.0,
    avg1:10.0,

    comment1:"",
    comment2:"",
    comment3:"",
    comment4:"",
    comment5:"",

    // I&AM
    department:'',
    title: '',
    username:'',
    user_id:'',

    // view
    views:{},
    emp_avg:10.0,
    mag_avg:10.0,
    emp_avg1:10.0,
    mag_avg1:10.0,

    // search
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
            case "pg":
              _this.pg = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
        }
      });
    }

    this.getEmployees();
    this.getUserName();
    this.getLeaveCredit();
  },

  computed: {
    displayedRecord() {
      if (this.pg == 0) this.filter_apply();

      this.setPages();
      return this.paginate(this.receive_records);
    },
  },

  mounted() {},

  watch: {
    employee() {
      this.getTemplatesByTitle();
    },
    
    receive_records() {
      console.log("Vue watch receive_records");
      this.setPages();
    },

    review_month(d) {
      var d = new Date(d + '-01');
      var d_next = new Date(d.setMonth(d.getMonth() + 1));
      this.review_next_month = moment(d_next).format('YYYY-MM');
    },
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
          _this.username = response.data.username;
          _this.is_manager = response.data.is_manager;
          _this.department = response.data.department;
          _this.title = response.data.title;
          _this.user_id = response.data.user_id;
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

    filter_apply: function() {
      let _this = this;

      window.location.href =
        "performance_review?" + "kw=" + _this.keyword + "&pg=" + _this.page;
    },

    on_grade_change:function(event) {
      console.log(event.target.value);
      var grade = this.$refs.grade;

      var score =0.0;
      for (i = 0; i < grade.length; i++) {
        score += parseInt(grade[i].value);
      }

      this.avg = (score / i).toFixed(1);

    },

    on_grade1_change:function(event) {
      console.log(event.target.value);
      var grade = this.$refs.grade1;

      var score =0.0;
      for (i = 0; i < grade.length; i++) {
        score += parseInt(grade[i].value);
      }

      this.avg1 = (score / i).toFixed(1);

    },

    open_review: function() {
      if(this.is_add_review_privilege())
      {
        window.jQuery(".mask").toggle();
        window.jQuery("#Modal_1").toggle();
      }
    },

    add_review: function() {
      if (
        typeof this.employee.title_id == 'undefined' ||
        this.review_month.trim() == "" ||
        typeof this.template.id == 'undefined'
      ) {
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
      form_Data.append("user_id", this.employee.id);
      form_Data.append("review_month", this.review_month);
      form_Data.append("template_id", this.template.id);


      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/performance_review_insert",
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
      window.jQuery("#Modal_1").toggle();
    },

    review_submit() {

      let _this = this;

      Swal.fire({
        title: "Submit",
        text: "Are you sure to submit? Once submitted, you cannot revise the evaluation result anymore.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {
          if (_this.submit == true) return;

          _this.submit = true;

          var qid = _this.$refs.qid;
          var opt = _this.$refs.opt;

          var qid1 = _this.$refs.qid1;
          var opt1 = _this.$refs.opt1;

          var grade = _this.$refs.grade;
          var grade1 = _this.$refs.grade1;

          let temp = [];
          for(var i=0; i<grade.length; i++) {
            var obj = {
                id: qid[i].value,
                grade: grade[i].value,
                opt: opt[i].value
            };
            temp.push(obj);
          }

          for(var i=0; i<grade1.length; i++) {
            var obj = {
                id: qid1[i].value,
                grade: grade1[i].value,
                opt: opt1[i].value
            };
            temp.push(obj);
          }

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          form_Data.append("jwt", token);
          form_Data.append("pid", _this.record[0].id);
          form_Data.append("answers", JSON.stringify(temp));
          form_Data.append("commet1", _this.comment1);
          form_Data.append("commet2", _this.comment2);
          form_Data.append("commet3", _this.comment3);
          form_Data.append("commet4", _this.comment4);
          form_Data.append("commet5", _this.comment5);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/performance_evaluate_insert",
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
            window.jQuery("#Modal_2").toggle();
        } else {
          return;
        }
      });
    },


    getTemplatesByTitle: function() {
      let _this = this;

      const params = {
        title: _this.employee.title_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_get_template_by_title", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.templates = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });

      _this.proof_id = 0;
    },

    getLeaveCredit: function() {
      let _this = this;

      const params = {
        kw: _this.keyword,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_review", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          if (_this.receive_records.length > 0) {
            //_this.proof_id = _this.receive_records[0].id;
            //_this.detail();
            if (_this.pg !== 0) {
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

    view: function() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select row to view",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      } else {

      let _this = this;
      let _window = window;

      const params = {
        id: this.proof_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_evaluate", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.record = response.data;
          if (_this.record.length > 0) {
            _this.views = _this.record[0];

            var e_score = 0.0;
            var m_score = 0.0;
            for(var i = 0; i < _this.views.agenda.length; i++)
            {
              e_score += parseInt((_this.views.agenda[i].emp_score) === '' ? "0" : _this.views.agenda[i].emp_score);
              m_score += parseInt((_this.views.agenda[i].mag_score) === '' ? "0" : _this.views.agenda[i].mag_score);
            }

            _this.emp_avg = (e_score / i).toFixed(1);
            _this.mag_avg = (m_score / i).toFixed(1);

            var e_score1 = 0.0;
            var m_score1 = 0.0;
            for(var i = 0; i < _this.views.agenda1.length; i++)
            {
              e_score1 += parseInt((_this.views.agenda1[i].emp_score) === '' ? "0" : _this.views.agenda1[i].emp_score);
              m_score1 += parseInt((_this.views.agenda1[i].mag_score) === '' ? "0" : _this.views.agenda1[i].mag_score);
            }

            _this.emp_avg1 = (e_score1 / i).toFixed(1);
            _this.mag_avg1 = (m_score1 / i).toFixed(1);

            _window.jQuery(".mask").toggle();
            _window.jQuery("#Modal_3").toggle();
          }
        })
        .catch(function(error) {
          console.log(error);
        });
      }
    },

    evalua: function() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select row to evaluate",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      } else {

      var record = this.shallowCopy(
          this.receive_records.find((element) => element.id == this.proof_id)
        );

      if(record.user_id == this.user_id && record.user_complete_at != "")
        return;

      if(record.create_id == this.user_id && record.manager_complete_at != "")
        return;

      if(record.create_id != this.user_id && record.user_id != this.user_id)
        return;

      let _this = this;
      let _window = window;

      const params = {
        id: this.proof_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_review", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.record = response.data;
          if (_this.record.length > 0) {
            _this.evals = _this.record[0];
            _window.jQuery(".mask").toggle();
            _window.jQuery("#Modal_2").toggle();

            _this.reset_evaluate();
          }
        })
        .catch(function(error) {
          console.log(error);
        });
      }
    },

    edit_detai: function() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select row to edit",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      } else {
        window.jQuery(".mask").toggle();
        window.jQuery("#Modal_3").toggle();
      }
    },

    detail: function() {
      let _this = this;

      if (this.proof_id == 0) {
        //this.view_detail = false;
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      this.e_title = this.shallowCopy(
        this.position.find((element) => element.did == this.record.did)
      ).items;

      this.e_department = this.record.did;
      this.e_tid = this.record.tid;

      this.e_sn = this.record.agenda.length;
      this.e_sn1 = this.record.agenda1.length;
    },

    can_delete(manager){
      var can_save = false;

      if(manager === this.user_id)
        can_save = true;

      if(this.department.trim() == '')
      { 
        if(this.title.trim() == 'Owner')
          can_save = true;

        if(this.title.trim() == 'Managing Director')
          can_save = true;

        if(this.title.trim() == 'Chief Advisor')
          can_save = true;
      }
      
      return can_save;
    },

    is_add_review_privilege() {
      var can_save = false;

      if(this.department.trim() == 'SALES')
      { 
        if(this.title.trim() == 'ASSISTANT SALES MANAGER')
          can_save = true;

        if(this.title.trim() == 'SALES MANAGER')
          can_save = true;
      }

      if(this.department.trim() == 'LIGHTING')
      { 
        if(this.title.trim() == 'LIGHTING MANAGER')
          can_save = true;
      }

      if(this.department.trim() == 'OFFICE')
      { 
        if(this.title.trim() == 'OFFICE SYSTEMS MANAGER')
          can_save = true;
      }

      if(this.department.trim() == 'DESIGN')
      { 
        if(this.title.trim() == 'ASSISTANT BRAND MANAGER')
          can_save = true;

        if(this.title.trim() == 'BRAND MANAGER')
          can_save = true;
      }

      if(this.department.trim() == 'SERVICE')
      { 
        if(this.title.trim() == 'ENGINERING MANAGER')
          can_save = true;
      }

      if(this.department.trim() == 'ADMIN')
      { 
        if(this.title.trim() == 'OPERATIONS MANAGER')
          can_save = true;
      }

      if(this.department.trim() == 'TW')
      { 
        if(this.title.trim() == 'Supply Chain Manager')
          can_save = true;
      }

      if(this.department.trim() == '')
      { 
        if(this.title.trim() == 'Owner')
          can_save = true;

        if(this.title.trim() == 'Managing Director')
          can_save = true;

        if(this.title.trim() == 'Chief Advisor')
          can_save = true;
      }
      
      return can_save;
    },


    remove() {
      if (this.proof_id == 0) {
        Swal.fire({
          text: "Please select a record to delete",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      if(!this.can_delete(this.record.create_id))
    {
      Swal.fire({
        text: "Permission denied. ",
        icon: "warning",
        confirmButtonText: "OK",
      });

      return;
    };

    if(this.record.status != "Nobody cares" || !this.can_delete(this.record.create_id))
    {
      Swal.fire({
        text: "Partially or completely done performance evaluation cannot be deleted. ",
        icon: "warning",
        confirmButtonText: "OK",
      });

      return;
    };

      let _this = this;

      Swal.fire({
        title: "Delete",
        text: "Are you sure to delete?",
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
          form_Data.append("pid", _this.record.id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/performance_review_delete",
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
        } else {
          return;
        }
      });
    },


    getEmployees: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_employees", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.employees = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    reset_evaluate: function() {
      this.comment1 = "";
      this.comment2 = "";
      this.comment3 = "";
      this.comment5 = "";
      this.comment4 = "";

      var opt = this.$refs.opt;
      for (i = 0; i < opt.length; i++) {
        opt[i].value = "";
      }

      var opt1 = this.$refs.opt1;
      for (i = 0; i < opt1.length; i++) {
        opt1[i].value = "";
      }

      var grade1 = this.$refs.grade1;
      for (i = 0; i < grade1.length; i++) {
        grade1[i].value = 10;
      }

      var grade = this.$refs.grade;
      for (i = 0; i < grade.length; i++) {
        grade[i].value = 10;
      }

      this.avg = 10.0;
      this.avg1 = 10.0;

    },


    reset: function() {
      this.submit = false;

      this.editing = false;

      this.comment1 = "";
      this.comment2 = "";
      this.comment3 = "";
      this.comment5 = "";
      this.comment4 = "";

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
