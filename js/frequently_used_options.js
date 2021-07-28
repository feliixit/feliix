var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    level1: [],
    level2: [],
    level3: [],

    lv1:0,
    lv2:0,
    lv3:0,

    editing: false,

    pid : 0,
    option : '',
    e_org_option: "",

    list_sn:0,


    // paging
    page: 1,
    //perPage: 5,
    pg: 0,
    pages: [],

    perPage: 10,

    petty_list: [],
    view_detail: false,
    record: {},
    proof_id: 0,

    attribute_name:'',

    e_editing: false,


    // evaluate
    evals:{},
    avg:10.0,
    avg1:10.0,
    avg2:10.0,

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
    emp_avg2:10.0,
    mag_avg2:10.0,

    // search
    keyword: "",

    position: [],
    title: [],
    department: "",
    title_id: 0,

    view_promotion: false,

  },

  created() {

    this.getLevel1();
   
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
    lv1() {
      this.getLevel2(this.lv1);
    },

    lv2() {
      this.getLevel3(this.lv2);
    },

    lv3() {
      info = [];
      info = this.shallowCopy(this.level3.find(element => element.cat_id == this.lv3));

      if(info.category !== undefined) { this.attribute_name = info.category; }
    },
    
    department() {
      this.title = this.shallowCopy(
        this.position.find((element) => element.did == this.department)
      ).items;

    },
  },

  methods: {

    getLevel1: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      const params = {
        level: 1,
        parent: '',
      };

      axios
        .get("api/product_category_level_get", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.level1 = res.data;
            _this.lv2 = 0;
            _this.lv3 = 0;
            _this.attribute_name = '';
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getLevel2: function(cat_id) {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      const params = {
        level: 2,
        parent: cat_id,
      };

      axios
        .get("api/product_category_level_get", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.level2 = res.data;
            _this.lv2 = 0;
            _this.lv3 = 0;
            _this.attribute_name = '';
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },
    
    getLevel3: function(cat_id) {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      const params = {
        level: 3,
        parent: cat_id,
      };

      axios
        .get("api/product_category_level_get", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.level3 = res.data;
            _this.lv3 = 0;
            _this.attribute_name = '';
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    apply: function() {
   
      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("cat_id", this.lv3);
    
      form_Data.append("petty_list", JSON.stringify(this.petty_list));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/product_category_attribute_detail",
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

    detail: function() {
      if (this.lv3 == 0) {
        Swal.fire({
            text: "Please choose category to view attribute",
            icon: "warning",
            confirmButtonText: "OK",
          });
        return;
      }else
      {
        
        this.getLeaveCredit();
        this.editing = true;

      }
    },

    getLeaveCredit: function() {
      

      let _this = this;
      let _window = window;

      const params = {
        id: this.lv3,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/product_category_attribute_detail", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(function(response) {
          console.log(response.data);
          _this.petty_list = response.data;
   
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

      this.pid = 0;
      this.list_sn = 0;

      this.lv1 = 0;
      this.lv2 = 0;
      this.lv3 = 0;
      this.attribute_name = '';

      this.option = "";

      this.editing = false;
      this.petty_list = [];
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    _add_criterion: function() {
      if (
        this.option.trim() == ""
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
          cat_id: this.lv3,
          option: this.option,
          status : 1,
        };
        this.petty_list.push(ad);
      
        this.e_clear_edit();
    },

    _cancel_criterion: function() {

      this.option = this.e_org_option;

      this.e_clear_edit();
    },

    _update_criterion: function() {
      if (this.option.trim() == "" ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.petty_list.find(({ id }) => id === this.e_org_id);

      element.option = this.option;
    
      this.e_clear_edit();
    },

    e_add_criterion: function() {
      if (
        this.option.trim() == "" 
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
          cat_id: this.lv3,
          option: this.option,
          status : 1,
        };
        this.petty_list.push(ad);

        this.e_clear_edit();

    },

    e_cancel_criterion: function() {
      this.option = this.e_org_option;
     
      this.e_clear_edit();
    },

    e_clear_edit: function() {

      this.e_org_option = "";
      this.option = "";
      this.e_editing = false;
    },

    e_update_criterion: function() {
      if (this.option.trim() == "" ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.petty_list.find(({ id }) => id === this.e_org_id);

      element.option = this.option;
    
      this.e_clear_edit();
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
      this.e_org_option = element.option;
    
      this.list_id = eid;
      this.option = element.option;
     
      this.e_editing = true;
    },

    _del: function(eid) {
      var index = this.petty_list.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.petty_list.splice(index, 1);
      }
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
      this.e_org_option = element.option;
     
      this.list_id = eid;
      this.option = element.option;
  

      this.e_editing = true;
    },

    e_del: function(eid) {
      var index = this.petty_list.findIndex(({ id }) => id === eid);
      if (index > -1) {
        this.petty_list.splice(index, 1);
      }
    },

    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: 'smooth' });
  
    },

    

  },
});
