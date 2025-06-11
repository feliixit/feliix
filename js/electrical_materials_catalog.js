var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    level1: [],
   
    org_level1: [],
    
    editing: false,

    pid : 0,
    
    particulars : "",
    unit : "",
    price : "",
    sn : "",
    remarks : "",

    ord_particulars : "",
    ord_unit : "",
    ord_price : "",
    ord_remarks : "",

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
        .get("api/electrical_materials_bom", {
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

      if(this.tag_management == false) {
        Swal.fire({
          text: "User is not allowed to add/edit/delete groups of tag.",
          icon: "info",
          confirmButtonText: "OK",
        });
        return;
      }

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
        url: "api/electrical_materials_bom_update",
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
        this.particulars.trim() == "" || this.price.trim() == ""
      ) {
        Swal.fire({
          text: "Particulars and Price are required; Unit and Remarks are optional.",
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
          particulars: this.particulars,
          unit: this.unit,
          price: this.price,
          remarks: this.remarks,
          status : 0,
        };
        this.level1.push(ad);
      
        this.clear_edit();
    },

    _cancel_criterion: function() {

      this.clear_edit();
    },

    _update_criterion: function() {
      if (this.particulars.trim() == "" || this.price.trim() == "" ) {
        Swal.fire({
          text: "Particulars and Price are required; Unit and Remarks are optional.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.level1.find(({ sn }) => sn === this.org_id);

      element.particulars = this.particulars;
      element.unit = this.unit;
      element.price = this.price;
      element.remarks = this.remarks;
          
      this.clear_edit();
    },


    clear_edit: function() {

      this.particulars = "";
      this.unit = "";
      this.price = "";
      this.remarks = "";

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
      this.particulars = element.particulars;
      this.unit = element.unit;
      this.price = element.price;
      this.sn = element.sn;
      this.remarks = element.remarks;
    
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
