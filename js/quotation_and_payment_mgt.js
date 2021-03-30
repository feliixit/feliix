var app = new Vue({
  el: "#app",
  data: {
    project_category: "",
    client_type: "",
    priority: "",
    status: "",
    reason: "",
    project_name: "",
    special_note: "",

    probability: 0,

    receive_records: [],
    record: {},

    categorys: {},
    client_types: {},
    priorities: {},
    statuses: {},
    stages: {},
    creators: {},

    submit: false,
    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      { name: "10", id: 10 },
      { name: "25", id: 25 },
      { name: "50", id: 50 },
      { name: "100", id: 100 },
      { name: "All", id: 10000 },
    ],
    perPage: 10,

    prof_remark: "",
    prof_fileArray: [],

    quote_remark: "",
    quote_fileArray: [],

    payment_type: "",

    fil_category: "",
    fil_status: "",
    fil_creator: "",

    fil_amount_upper: "",
    fil_amount_lower: "",
    fil_payment_upper: "",
    fil_payment_lower: "",
    fil_keyowrd: "",

    od_factor1: "",
    od_factor1_order: "",
    od_factor2: "",
    od_factor2_order: "",
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
            case "fc":
              _this.fil_category = tmp[1];
              break;
            case "fs":
              _this.fil_status = tmp[1];
              break;
            case "ft":
              _this.fil_creator = tmp[1];
              break;
            case "fal":
              _this.fil_amount_lower = tmp[1];
              break;
            case "fau":
              _this.fil_amount_upper = tmp[1];
              break;
            case "fpl":
              _this.fil_payment_lower = tmp[1];
              break;
            case "fpu":
              _this.fil_payment_upper = tmp[1];
              break;
            case "fk":
              _this.fil_keyowrd = tmp[1];
              break;
            case "of1":
              _this.od_factor1 = tmp[1];
              break;
            case "ofd1":
              _this.od_factor1_order = tmp[1];
              break;
            case "of2":
              _this.od_factor2 = tmp[1];
              break;
            case "ofd2":
              _this.od_factor2_order = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
        }
      });
    }
    
    this.getRecords();

    this.getProjectCategorys();
    this.getClientTypes();
    this.getPrioritys();
    this.getStatuses();
    this.getStages();
    this.getCreators();
  },

  computed: {
    displayedPosts() {
      this.setPages();
      return this.paginate(this.receive_records);
    },

    showExtra: function() {
      return this.status == 10;
    },
  },

  mounted() {},

  watch: {
    receive_records() {
      console.log("Vue watch receive_records");
      this.setPages();
    },
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

    filter_apply: function() {
      let _this = this;
      
      window.location.href = 'quotation_and_payment_mgt?fc=' + _this.fil_category 
                                                    + '&fs=' + _this.fil_status
                                                    + '&ft=' + _this.fil_creator
                                                    + '&fal=' + _this.fil_amount_lower
                                                    + '&fau=' + _this.fil_amount_upper
                                                    + '&fpl=' + _this.fil_payment_lower
                                                    + '&fpu=' + _this.fil_payment_upper
                                                    + '&fk=' + _this.fil_keyowrd
                                                    + '&of1=' + _this.od_factor1
                                                    + '&ofd1=' + _this.od_factor1_order
                                                    + '&of2=' + _this.od_factor2
                                                    + '&ofd2=' + _this.od_factor2_order;
    },

    getRecords: function(keyword) {
      let _this = this;

      const params = {
        fc: _this.fil_category,
        fs: _this.fil_status,
        ft: _this.fil_creator,
        fal: _this.fil_amount_lower,
        fau: _this.fil_amount_upper,
        fpl: _this.fil_payment_lower,
        fpu: _this.fil_payment_upper,
        fk: _this.fil_keyowrd,
        of1: _this.od_factor1,
        ofd1: _this.od_factor1_order,
        of2: _this.od_factor2,
        ofd2: _this.od_factor2_order,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/quotation_payment_mgt", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.receive_records = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getProjectCategorys() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_category", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.categorys = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getClientTypes() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_client_type", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.client_types = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getPrioritys() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_priority", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.priorities = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getStatuses() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_status", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.statuses = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getStages() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_stage", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.stages = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getCreators() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_creators", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.creators = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getLeaveCredit: function() {
      let _this = this;

      axios
        .get("api/ammend")
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
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

    approve: function() {
      let _this = this;

      if (this.project_name.trim() == "") {
        Swal.fire({
          text: "Please enter Project Name!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.project_category.trim() == "") {
        Swal.fire({
          text: "Please select Project Category!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.client_type.trim() == "") {
        Swal.fire({
          text: "Please select Client Type!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      if (this.priority.trim() == "") {
        Swal.fire({
          text: "Please select Priority!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append("project_name", this.project_name);
      form_Data.append("project_category", this.project_category);
      form_Data.append("client_type", this.client_type);
      form_Data.append("priority", this.priority);
      form_Data.append("status", this.status);
      form_Data.append("reason", this.reason);
      form_Data.append("probability", this.probability);
      form_Data.append("special_note", this.special_note);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project01_insert",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          //this.$forceUpdate();
          _this.clear();
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        });
    },

    clear: function() {
      this.project_name = "";
      this.project_category = "";
      this.client_type = "";
      this.priority = "";
      this.status = "";
      this.reason = "";
      this.probability = "";
      this.special_note = "";

      document.getElementById("insert_dialog").classList.remove("show");

      this.receive_records = [];

      this.getRecords();
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    order_clear() {
      document.getElementById("dialog_a1").classList.remove("focus");
      document.getElementById("add_a1").classList.remove("show");
    },

    filter_clear() {
      document.getElementById("dialog_f1").classList.remove("focus");
      document.getElementById("add_f1").classList.remove("show");
    },

    quote_canSub() {},

    quote_clear() {},

    quote_create() {},

    prof_canSub() {},

    prof_clear() {},

    prof_create() {},
  },
});
