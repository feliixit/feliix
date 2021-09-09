var app = new Vue({
    el: "#app",
    data: {
      receive_records: [],
      is_editing: false,
      project_group:"",
      org_project_group:"",

      record:[],
    },
  
    created() {
      this.getRecords();

    },
  
    computed: {},
  
    mounted() {},
  
    methods: {
      getRecords: function(kind) {
        let _this = this;
        const params = {
          action: 1,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/project_grouping", {
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
  
      add_group: function() {
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("action", 1);
        form_Data.append("project_group", this.project_group);
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/project_grouping",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            Swal.fire({
              text: "Update Success.",
              icon: "success",
              confirmButtonText: "OK",
            });
  
            _this.reset();
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

      save_group: function() {
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("action", 3);
        form_Data.append("project_group", this.project_group);
        form_Data.append("id", this.record.id);
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/project_grouping",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            Swal.fire({
              text: "Update Success.",
              icon: "success",
              confirmButtonText: "OK",
            });
  
            _this.reset();
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

      rename: function(id) {
        this.record = this.shallowCopy(this.receive_records.find(element => element.id == id));
        this.is_editing = true;
        this.project_group = this.record['project_group'];
      },
  
      cancel: function() {
        this.reset();
      },

      delete_me: function(id) {
        Swal.fire({
          title: "Submit",
          text: "Are you sure to delete?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes",
        }).then((result) => {
          if (result.value) {
            var form_Data = new FormData();
            let _this = this;
      
            form_Data.append("action", 2);
            form_Data.append("id", id);
      
            axios({
              method: "post",
              headers: {
                "Content-Type": "multipart/form-data",
              },
              url: "api/project_grouping",
              data: form_Data,
            })
              .then(function(response) {
                //handle success
                Swal.fire({
                  text: "Delete Success.",
                  icon: "success",
                  confirmButtonText: "OK",
                });
      
                _this.reset();
              })
              .catch(function(response) {
                //handle error
                Swal.fire({
                  text: JSON.stringify(response),
                  icon: "error",
                  confirmButtonText: "OK",
                });
              });
            } else {
              return;
            }
          });
      },
  
      reset: function() {
        this.getRecords();
        this.is_editing = false;
        this.project_group = "";
        this.org_project_group = "";
        this.record = [];
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
  