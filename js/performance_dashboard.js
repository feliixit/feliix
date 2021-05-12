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

    position: [],
    title: [],
    department: "",
    title_id: 0,

  },

  created() {
   
    this.getEmployees();
    this.getUserName();
    this.getLeaveCredit();
    this.get_positions();
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
    
    department() {
      this.title = this.shallowCopy(
        this.position.find((element) => element.did == this.department)
      ).items;

    },
  },

  methods: {
    
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
      let _window = window;

      const params = {
        id: this.proof_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/performance_dashboard", {
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

          }
        })
        .catch(function(error) {
          console.log(error);
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
