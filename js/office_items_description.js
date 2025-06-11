var app = new Vue({
  el: "#app",
  data: {
    submit: false,

    // data
    level1: [],
    level2: [],
    level3: [],
    level4: [],

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

    lv3:"",
    lv3_item : {},
    
    view_detail: false,

    lv4: "",

    url: "",
    baseURL: "https://storage.googleapis.com/feliiximg/",
    file_array: [],
    item: {},
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

            _this.lv1 = "";
            _this.lv1_item = {};
            _this.lv2 = "";
            _this.lv2_item = {};
            _this.lv3 = "";
            _this.lv3_item = {};

            _this.level2 = [];
            _this.level3 = [];
            _this.level4 = [];

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
        else if(this.lv3 == "") {
          Swal.fire({
            text: "Please choose a brand",
            icon: "warning",
            confirmButtonText: "OK",
          });
        return;
      }
        else
        {

         this.lv4 = this.lv1 + this.lv2 + this.lv3;

        this.lv3_item = this.level3.find(({ code }) => code === this.lv3);

          this.getLevel4(this.lv1 + this.lv2 + this.lv3);

          this.view_detail = true;
  
        }
      },

      
      async getLevel4 (cat_id) {
        if(cat_id == "") 
          return;
  
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        const params = {
          key: cat_id,
        };
  
        try {
          let res = await axios.get("api/office_items_description", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          });
          
          _this.level4 = res.data;
          _this.org_level4 = JSON.parse(JSON.stringify(_this.level4));
  
        } catch (err) {
          console.log(err)
          alert('error')
        }
    },

      async getLevel3 () {
        let cat_id = this.lv1 + this.lv2;
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
          
          this.lv2_item = this.level2.find(({ code }) => code === this.lv2);
          _this.level3 = res.data;
          _this.org_level3 = JSON.parse(JSON.stringify(_this.level3));

            _this.lv3 = "";
            _this.lv3_item = {};

          _this.level4 = [];
  
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
          
          this.lv1_item = this.level1.find(({ code }) => code === this.lv1);
          _this.level2 = res.data;
          _this.org_level2 = JSON.parse(JSON.stringify(_this.level2));


            _this.lv2 = "";
            _this.lv2_item = {};
            _this.lv3 = "";
            _this.lv3_item = {};

          _this.level3 = [];
          _this.level4 = [];
  
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
      form_Data.append("code", this.lv4);
      form_Data.append("level1", JSON.stringify(this.level4));

      for( var i = 0; i < this.file_array.length; i++ ){
        let file = this.file_array[i];
        form_Data.append(file.name, file);
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/office_items_description_update",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          
          _this.detail();


          //_this.reset();
          
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
        }).finally(() => {
          _this.submit = false;
        }
        );

    },

    reset_org: function() {
      this.level4 = JSON.parse(JSON.stringify(this.org_level4));
    },

    reset: function() {
      this.submit = false;

      this.getLevel1();
      this.view_detail = false;
      this.lv1 = "";
      this.lv1_item = {};
      this.lv2 = "";
      this.lv2_item = {};
      this.lv3 = "";
      this.lv3_item = {};
      this.level3 = [];
      this.level2 = [];
      this.level4 = [];
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
          text: "Code and Description are required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      // get the largest sn from level1
      var max_sn = 0;
      this.level4.forEach(function(item, index, array) {
        if (item.sn > max_sn) max_sn = item.sn;
      });

      var fileTarget = document.getElementById("photo");
      var photo = "";

      if (fileTarget.files.length != 0) {
        this.file_array.push(fileTarget.files[0]);
        photo = fileTarget.files[0].name;
      }
        

        var ad = {
          id: 0,
          sn: ++max_sn,
          code: this.code,
          category: this.category,
          url: this.url,
          photo: photo,
          status : 0,
        };
        this.level4.push(ad);
      
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
            text: "Code and Description are required.",
          icon: "warning",
          confirmButtonText: "OK",
        });

        return;
      }

      var element = this.level4.find(({ sn }) => sn === this.org_id);

      element.code = this.code;
      element.category = this.category;

      var fileTarget = document.getElementById("photo");
      var photo = "";

      if (fileTarget.files.length != 0) {
        this.file_array.push(fileTarget.files[0]);
        photo = fileTarget.files[0].name;

        element.url = URL.createObjectURL(fileTarget.files[0]);
      }
      else
      {
        photo = this.item.photo;
        element.url = this.item.url;
      }

      element.photo = photo;
          
      this.clear_edit();
    },


    clear_edit: function() {

      this.code = "";
      this.category = "";
      this.url = "";

      document.getElementById('photo').value = "";

      this.item = {};
      this.editing = false;

    },

    _set_up: function(fromIndex, eid) {
      var toIndex = fromIndex - 1;

      if (toIndex < 0) toIndex = 0;

      var element = this.level4.find(({ sn }) => sn === eid);
      this.level4.splice(fromIndex, 1);
      this.level4.splice(toIndex, 0, element);
    },

    _set_down: function(fromIndex, eid) {
      var toIndex = fromIndex + 1;

      if (toIndex > this.level4.length - 1)
        toIndex = this.level4.length - 1;

      var element = this.level4.find(({ sn }) => sn === eid);
      this.level4.splice(fromIndex, 1);
      this.level4.splice(toIndex, 0, element);
    },

    _edit: function(eid) {
 
      var element = this.level4.find(({ sn }) => sn === eid);

      this.item =JSON.parse(JSON.stringify(element));

      this.org_id = eid;
      this.code = element.code;
      this.category = element.category;
      this.sn = element.sn;

      if(element.url != "")
        this.url = element.url;
    
      this.editing = true;
    },

    _del: function(eid) {
        let _this = this;
      var index = this.level4.findIndex(({ sn }) => sn === eid);
      if (index > -1) {

        // var element = this.level4.find(({ sn }) => sn === eid);
        // if(element.items.length > 0) {
        // CHOOSE TO DELETE OR NOT

          Swal.fire({
            title: "Delete",
            text: "Are you sure to delete this description?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
          }).then((result) => {
            if (result.value) {
                _this.level4.splice(index, 1);
            }
          });

          
        //   return;
        // }

        //this.level4.splice(index, 1);
      }
    },

    onFileChange(e) {
      const file = e.target.files[0];

      this.url = URL.createObjectURL(file);
    },

    clear_photo() {

        this.url = "";
        document.getElementById('photo').value = "";

        if(this.editing == true)
          {
            this.item.url = "";
            this.item.photo = "";
          }
    },

  },
});
