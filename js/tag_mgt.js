var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    level1: [],
    level2: [],
    level3: [],

    org_level1: [],
    org_level2: [],

    lv1:{},
    lv2:0,
    lv3:0,

    editing: false,

    pid : 0,
    option : '',
    org_option: "",
    e_option : '',
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

    info:[],

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
    

    lv2() {
      this.getLevel3(this.lv2);
    },

    lv3() {
      
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
        .get("api/tag_mgt_group_get", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.level1 = res.data;

            _this.org_level1 = JSON.parse(JSON.stringify(_this.level1));

            _this.lv2 = 0;
            _this.lv3 = 0;
            _this.info = [];
            _this.attribute_name = '';
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    async getLevel2 (cat_id) {
      if(cat_id == 0) 
        return;

      let _this = this;

      let token = localStorage.getItem("accessToken");

      const params = {
        parent: cat_id,
      };

      try {
        let res = await axios.get("api/tag_mgt_item_get", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });
        
        _this.level2 = res.data;
        _this.org_level2 = JSON.parse(JSON.stringify(_this.level2));
        _this.lv2 = 0;
        _this.lv3 = 0;
        _this.info = [];

      } catch (err) {
        console.log(err)
        alert('error')
      }

      /*
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
        */
    },
    
    getLevel3: function(cat_id) {
      if(cat_id == 0)
        return;

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
            _this.info = [];
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
    
      form_Data.append("level1", JSON.stringify(this.level1));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/tag_mgt_group_update",
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

    
    e_apply: function() {
   
      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("lv1", this.lv1.id);
      form_Data.append("level2", JSON.stringify(this.level2));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/tag_mgt_item_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          
          _this.e_reset();
          
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.e_reset();
        });

    },


    detail: function() {
      if (this.lv1 == 0) {
        Swal.fire({
            text: "Please choose group of tag: to view attribute",
            icon: "warning",
            confirmButtonText: "OK",
          });
        return;
      }
      else
      {
        this.getLevel2(this.lv1.id);
        this.attribute_name = this.lv1.group_name;
        this.view_detail = true;

      }
    },

    

    e_reset: function() {
      this.submit = false;
      this.view_detail = false;
      this.lv1 = {};
      this.level2 = [];
    },

    reset: function() {
      this.submit = false;

      this.pid = 0;
      this.list_sn = 0;

      this.lv1 = 0;
      this.lv2 = 0;
      this.lv3 = 0;
      this.info = [];
      this.attribute_name = '';

      this.option = "";

      this.getLevel1();

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

      // get the largest sn from level1
      var max_sn = 0;
      this.level1.forEach(function(item, index, array) {
        if (item.sn > max_sn) max_sn = item.sn;
      });

        var ad = {
          id: 0,
          sn: ++max_sn,
          group_name: this.option,
          status : 1,
          items: []
        };
        this.level1.push(ad);
      
        this.clear_edit();
    },

    _cancel_criterion: function() {

      this.option = this.org_option;

      this.clear_edit();
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

      var element = this.level1.find(({ sn }) => sn === this.org_id);

      element.group_name = this.option;
    
      this.clear_edit();
    },

    e_add_criterion: function() {
      if (
        this.e_option.trim() == "" 
      ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      // get the largest sn from level1
      var max_sn = 0;
      this.level2.forEach(function(item, index, array) {
        if (item.sn > max_sn) max_sn = item.sn;
      });

        var ad = {
          id: 0,
          sn: ++max_sn,
          group_id: this.lv1.id,
          item_name: this.e_option,
  
          status : 1,
        };
        this.level2.push(ad);

        this.e_clear_edit();

    },

    e_cancel_criterion: function() {
      this.e_option = this.e_org_option;
     
      this.e_clear_edit();
    },

    e_clear_edit: function() {

      this.e_org_option = "";
      this.e_option = "";
      this.e_editing = false;

    },

    clear_edit: function() {

      this.org_option = "";
      this.option = "";
      this.editing = false;

    },

    e_update_criterion: function() {
      if (this.e_option.trim() == "" ) {
        Swal.fire({
          text: "Please enter the required fields",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.level2.find(({ sn }) => sn === this.e_org_id);

      element.item_name = this.e_option;
    
      this.e_clear_edit();
    },

    
    _set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.level1.find(({ sn }) => sn === eid);
      this.level1.splice(fromIndex, 1);
      this.level1.splice(toIndex, 0, element);
    },

    _set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.level1.length - 1)
        toIndex = this.level1.length - 1;

      var element = this.level1.find(({ sn }) => sn === eid);
      this.level1.splice(fromIndex, 1);
      this.level1.splice(toIndex, 0, element);
    },

    _edit: function(eid) {
 
      var element = this.level1.find(({ sn }) => sn === eid);

      this.org_id = eid;
      this.org_option = element.group_name;
    
      this.list_id = eid;
      this.option = element.group_name;
     
      this.editing = true;
    },

    _del: function(eid) {
      var index = this.level1.findIndex(({ sn }) => sn === eid);
      if (index > -1) {

        var element = this.level1.find(({ sn }) => sn === eid);
        if(element.items.length > 0) {
          Swal.fire({
            text: "User only allows to delete the group which doesn’t contain any tag.",
            icon: 'warning',
            confirmButtonText: 'OK'
          });
          
          return;
        }

        this.level1.splice(index, 1);
      }
    },

    e_set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.level2.find(({ sn }) => sn === eid);
      this.level2.splice(fromIndex, 1);
      this.level2.splice(toIndex, 0, element);
    },

    e_set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.level2.length - 1)
        toIndex = this.level2.length - 1;

      var element = this.level2.find(({ sn }) => sn === eid);
      this.level2.splice(fromIndex, 1);
      this.level2.splice(toIndex, 0, element);
    },

    e_edit: function(eid) {

      var element = this.level2.find(({ sn }) => sn === eid);

      this.e_org_id = eid;
      this.e_org_option = element.e_option;
     
      this.list_id = eid;
      this.e_option = element.item_name;
  

      this.e_editing = true;
    },

    e_del: function(eid) {
      var index = this.level2.findIndex(({ sn }) => sn === eid);
      if (index > -1) {
        this.level2.splice(index, 1);
      }
    },


    

  },
});
