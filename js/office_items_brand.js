var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    level1: [],
    level2: [],
    level3: [],

    org_level3: [],
    
    editing: false,

    pid : 0,
    
    sn : 0,
    code : "",
    category : "",

    org_id : 0,

    lv1:"",
    lv1_item : {},

    lv2:"",
    lv2_item : {},
    
    view_detail: false,

    lv3: "",
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

    detail: function() {

        if (this.lv1 == "") {
          Swal.fire({
              text: "Please choose a main category",
              icon: "warning",
              confirmButtonText: "OK",
            });
          return;
        }
        else if(this.lv2 == "") {
          Swal.fire({
              text: "Please choose a sub category",
              icon: "warning",
              confirmButtonText: "OK",
            });
          return;
        }
        else
        {

         this.lv3 = this.lv1 + this.lv2;

        this.lv2_item = this.level2.find(({ code }) => code === this.lv2);

          this.getLevel3(this.lv1 + this.lv2);

          this.view_detail = true;
  
        }
      },

      async getLevel3 (cat_id) {
        if(cat_id == "") 
          return;
  
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        const params = {
          key: cat_id,
        };
  
        try {
          let res = await axios.get("api/office_items_brand", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });
          
          _this.level3 = res.data;
          _this.org_level3 = JSON.parse(JSON.stringify(_this.level3));
  
        } catch (err) {
          console.log(err)
          alert('error')
        }
    },
    
    async getLevel2 () {

        let cat_id = this.lv1;
        if(cat_id == "") 
          return;
  
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        const params = {
          key: cat_id,
        };
  
        try {
          let res = await axios.get("api/office_items_sub_category", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });

          _this.lv2 = "";
          _this.lv2_item = {};
          
          this.lv1_item = this.level1.find(({ code }) => code === this.lv1);
          _this.level2 = res.data;
          _this.org_level2 = JSON.parse(JSON.stringify(_this.level2));
  
        } catch (err) {
          console.log(err)
          alert('error')
        }
    },

    apply: function() {
      if(this.submit == true) return;
      if(this.lv1 == "") return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("code", this.lv3);
      form_Data.append("level1", JSON.stringify(this.level3));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/office_items_brand_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          
          //_this.reset();
          _this.detail();
          
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.detail();
          //_this.reset();
        });

    },

    reset_org: function() {
      this.level3 = JSON.parse(JSON.stringify(this.org_level3));
    },

    reset: function() {
      this.submit = false;

      this.getLevel1();
      this.view_detail = false;
      this.lv1 = "";
      this.lv1_item = {};
      this.lv2 = "";
      this.lv2_item = {};
      this.level3 = [];
      this.level2 = [];
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
          text: "Code and Brand are required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      // get the largest sn from level1
      var max_sn = 0;
      this.level3.forEach(function(item, index, array) {
        if (item.sn > max_sn) max_sn = item.sn;
      });

        var ad = {
          id: 0,
          sn: ++max_sn,
          code: this.code,
          category: this.category,
          status : 0,
        };
        this.level3.push(ad);
      
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
            text: "Code and Brand are required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.level3.find(({ sn }) => sn === this.org_id);

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

      var element = this.level3.find(({ sn }) => sn === eid);
      this.level3.splice(fromIndex, 1);
      this.level3.splice(toIndex, 0, element);
    },

    _set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.level3.length - 1)
        toIndex = this.level3.length - 1;

      var element = this.level3.find(({ sn }) => sn === eid);
      this.level3.splice(fromIndex, 1);
      this.level3.splice(toIndex, 0, element);
    },

    _edit: function(eid) {
 
      var element = this.level3.find(({ sn }) => sn === eid);

      this.org_id = eid;
      this.code = element.code;
      this.category = element.category;
      this.sn = element.sn;
    
      this.editing = true;
    },

    _del: function(eid) {
        let _this = this;
      var index = this.level3.findIndex(({ sn }) => sn === eid);
      if (index > -1) {

        // var element = this.level3.find(({ sn }) => sn === eid);
        // if(element.items.length > 0) {
        // CHOOSE TO DELETE OR NOT

          Swal.fire({
            title: "Delete",
            text: "Are you sure to delete this brand? It will cause the deletion of all the data attached under this brand.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
          }).then((result) => {
            if (result.value) {
                _this.level3.splice(index, 1);
            }
          });

          
        //   return;
        // }

        //this.level3.splice(index, 1);
      }
    },

  },
});
