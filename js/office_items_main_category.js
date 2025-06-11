var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    level1: [],
   
    org_level1: [],
    
    editing: false,

    pid : 0,
    
    sn : 0,
    code : "",
    category : "",

    org_id : 0,

  },

  created() {

    this.getLevel1();
    
  },

  computed: {

  },

  mounted() {},

  watch: {
    
    
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
        .get("api/office_items_main_category", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.level1 = res.data;

            _this.org_level1 = JSON.parse(JSON.stringify(_this.level1));

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
        url: "api/office_items_main_category_update",
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


    reset: function() {
      this.submit = false;

      this.getLevel1();

      this.editing = false;

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
        this.code.trim() == "" || this.category.trim() == ""
      ) {
        Swal.fire({
          text: "Code and Main Category are required.",
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
          code: this.code,
          category: this.category,
          status : 0,
        };
        this.level1.push(ad);
      
        this.clear_edit();
    },

    _cancel_criterion: function() {

      this.clear_edit();
    },

    setTwoNumberDecimal: function() {
        this.code = this.code.toString().padStart(2, '0')
        },

    _update_criterion: function() {
      if (this.code.trim() == "" || this.category.trim() == "" ) {
        Swal.fire({
            text: "Code and Main Category are required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.level1.find(({ sn }) => sn === this.org_id);

      element.code = this.code;
      element.category = this.category;
          
      this.clear_edit();
    },


    clear_edit: function() {

      this.code = "";
      this.category = "";

      this.editing = false;

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
      this.code = element.code;
      this.category = element.category;
      this.sn = element.sn;
    
      this.editing = true;
    },

    _del: function(eid) {
      var index = this.level1.findIndex(({ sn }) => sn === eid);
      if (index > -1) {

        // var element = this.level1.find(({ sn }) => sn === eid);
        // if(element.items.length > 0) {
        //   Swal.fire({
        //     text: "User only allows to delete the group which doesn’t contain any tag.",
        //     icon: 'warning',
        //     confirmButtonText: 'OK'
        //   });
          
        //   return;
        // }

        this.level1.splice(index, 1);
      }
    },

  },
});
