
var app = new Vue({
  el: '#app',
  data:{
    project_category : '',
    client_type : '',
    priority: '',
    status : '',
    reason : '',
    project_name : '',
    special_note: '',

    title: '',
    project_id : 0,
    projects : [],

    ins_title : '',
    ins_project_id : 0,

    is_modifying: false,

    fil_project_category : '',
    fil_client_type : '',
    fil_priority: '',
    fil_status : '',
    fil_stage : '',
    fil_group : '',
    fil_creator : '',
    fil_keyword : '',
    fil_lower : '',
    fil_upper : '',

    od_opt1 : '',
    od_ord1 : '',

    od_opt2 : '',
    od_ord2 : '',

    probability : 0,

    receive_records: [],
    record: {},

    categorys : {},
    client_types : {},
    priorities : {},
    groups : {},
    statuses : {},
    stages : {},
    creators : {},

    submit : false,
    // paging
    page: 1,
    pg:0,
    //perPage: 10,
    pages: [],

    inventory: [
      {name: '10', id: 10},
      {name: '25', id: 25},
      {name: '50', id: 50},
      {name: '100', id: 100},
      {name: 'All', id: 10000}
    ],
    perPage: 20,

  },

  created () {
    let _this = this;
    let uri = window.location.href.split("?");
    if (uri.length >= 2) {
      let vars = uri[1].split("&");

      let tmp = "";
      vars.forEach(async function(v) {
        tmp = v.split("=");
        if (tmp.length == 2) {
          switch (tmp[0]) {
            case "fpt":
              _this.fil_creator = decodeURI(tmp[1]);
              break;
            case "key":
              _this.fil_keyword = decodeURI(tmp[1]);
              break;
            case "op1":
              _this.od_opt1 = decodeURI(tmp[1]);
              break;
            case "od1":
              _this.od_ord1 = decodeURI(tmp[1]);
              break;
            case "op2":
              _this.od_opt2 = decodeURI(tmp[1]);
              break;
            case "od2":
              _this.od_ord2 = decodeURI(tmp[1]);
              break;
            case "id":
              _this.id = tmp[1];
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

    this.getRecords();
    this.getProjects();
    this.getCreators();
  },

  computed: {
    displayedPosts () {
      if(this.pg == 0)
        this.apply_filters();

      this.setPages();
        return this.paginate(this.receive_records);
    },

    showExtra: function(){
      return (this.status==10);
    },
  },

  mounted(){
 
    
  },

  watch: {

    receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },

      status (value) {
        if(value == 9) { 
          this.probability = 100;
          this.$refs.probability.setAttribute('disabled', '');
        }
        else
          this.$refs.probability.removeAttribute('disabled');
      },
/*
    fil_project_category (value) {
        this.getRecords(value);
        },
    fil_client_type (value) {
        this.getRecords(value);
        },
    fil_priority (value) {
        this.getRecords(value);
        },
    fil_status (value) {
        this.getRecords(value);
        },
    fil_stage (value) {
        this.getRecords(value);
        },
    fil_creator (value) {
        this.getRecords(value);
        },
  */
  },



  methods:{

    getCreators () {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      axios
          .get('api/admin/quotation_creators', { headers: {"Authorization" : `Bearer ${token}`} })
          .then(
          (res) => {
              _this.creators = res.data;
          },
          (err) => {
              alert(err.response);
          },
          )
          .finally(() => {
              
          });
  },

    editRow:function(item){
        if(this.is_modifying)
            return;
        else
            this.is_modifying = true;

        item['is_edited'] = 0;

        this.project_id = item['project_id'];
        this.title = item['title'];

        console.log(item);
    },

    duplicateRow: function(item){
      let _id = item['id'];
      let _this = this;

      Swal.fire({
          title: "Duplicate",
          text: "Are you sure to duplicate this quotation?",
          icon: "info",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.value) {


              token = localStorage.getItem('token');
              var form_Data = new FormData();
              form_Data.append('jwt', token);

              form_Data.append('id', _id);

              //DELETE table_name WHERE ID=id;
              $.ajax({
                  url: "api/quotation_duplicate",
                  type: "POST",
                  contentType: 'multipart/form-data',
                  processData: false,
                  contentType: false,
                  data: form_Data,

                  success: function (result) {
                      console.log(result);
                      Swal.fire({
                        html: result.data.message,
                        icon: "info",
                        confirmButtonText: "OK",
                      });
                      _this.clear();
                  },

                  // show error message to user
                  error: function (xhr, resp, text) {

                  }
              });

          } else {

          }
      });
    },

    deleteRow: function(item){
      let _id = item['id'];
      let _this = this;

      Swal.fire({
          title: "Delete",
          text: "Are you sure to delete?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.value) {


              token = localStorage.getItem('token');
              var form_Data = new FormData();
              form_Data.append('jwt', token);

              form_Data.append('id', _id);

              //DELETE table_name WHERE ID=id;
              $.ajax({
                  url: "api/quotation_delete",
                  type: "POST",
                  contentType: 'multipart/form-data',
                  processData: false,
                  contentType: false,
                  data: form_Data,

                  success: function (result) {
                      console.log(result);
                      Swal.fire({
                        html: result.data.message,
                        icon: "info",
                        confirmButtonText: "OK",
                      });
                      _this.clear();
                  },

                  // show error message to user
                  error: function (xhr, resp, text) {

                  }
              });

          } else {

          }
      });
    },

    confirmRow: function(item){
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

        form_Data.append('jwt', token);

        const title = this.title.trim();
        const project_id = item['project_id'];

        const project_name = this.shallowCopy(
                    this.projects.find(
                      (element) => element.id == project_id));

        form_Data.append('title', title);
        form_Data.append('project_id', project_id);
        form_Data.append('id', item['id']);

        axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                },
                url: 'api/quotation_edit_row',
                data: form_Data
            })
            .then(function(response) {
                //handle success
                console.log(response)

                item['title'] = title;
                item['project_name'] = project_name.project_name;
                _this.title = '';
                _this.project_id = 0;

            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });

        item['is_edited'] = 1; 
        
        this.is_modifying = false;
    },

    cancelRow: function(item){
        this.title = '';
     
        item['project_id'] = this.project_id;
        item['is_edited'] = 1; 

        this.project_id = 0;
        this.is_modifying = false;
    },

    setPages () {
          console.log('setPages');
          this.pages = [];
          let numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

          if(numberOfPages == 1)
            this.page = 1;
          for (let index = 1; index <= numberOfPages; index++) {
            this.pages.push(index);
          }
        },

        paginate: function (posts) {
          console.log('paginate');
          if(this.page < 1)
            this.page = 1;
          if(this.page > this.pages.length)
            this.page = this.pages.length;

          let page = this.page;
          let perPage = this.perPage;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return  this.receive_records.slice(from, to);
        },

    getRecords: function(keyword) {
      let _this = this;

      const params = {

                fpt: _this.fil_creator,
       
                key: _this.fil_keyword,

                op1: _this.od_opt1,
                od1: _this.od_ord1,
                op2: _this.od_opt2,
                od2: _this.od_ord2,
            };

      
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/quotation_mgt', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.receive_records = res.data;

                  if(_this.pg !== 0)
                  { 
                    _this.page = _this.pg;
                    _this.setPages();
                  }
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getProjects () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project02_get_project_name_by_keyword', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.projects = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },


    getUserName: function() {
        var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;

        form_Data.append('jwt', token);

        axios({
            method: 'post',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/on_duty_get_myname',
            data: form_Data
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
              icon: 'error',
              confirmButtonText: 'OK'
            })
        });
      },



    approve: function() {

      let _this = this;

            if (this.ins_title.trim() == '') {
              Swal.fire({
                text: 'Please enter Title!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.ins_project_id == 0) {
              Swal.fire({
                text: 'Please select Project!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }


            _this.submit = true;
            var form_Data = new FormData();

            var token = localStorage.getItem("token");

            form_Data.append("jwt", token);
            form_Data.append('title', _this.ins_title);
            form_Data.append('project_id', _this.ins_project_id);

            form_Data.append("first_line", '');
            form_Data.append("second_line", '');
            form_Data.append("project_category", '');
            form_Data.append("quotation_no", '');
            form_Data.append("quotation_date", '');
            form_Data.append("prepare_for_first_line", '');
            form_Data.append("prepare_for_second_line", '');
            form_Data.append("prepare_for_third_line", '');
            form_Data.append("prepare_by_first_line", '');
            form_Data.append("prepare_by_second_line", '');

            form_Data.append("footer_first_line", '');
            form_Data.append("footer_second_line", '');

            form_Data.append("pages", JSON.stringify([]));

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/quotation_insert',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.clear();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
      },

      clear: function() {
        this.ins_title = '';
        this.ins_project_id = 0;
        
        document.getElementById('insert_dialog').classList.remove("show");

        this.receive_records = [];

        this.getRecords();
        

      },

      cancel_filters:function() {
        document.getElementById('filter_dialog').classList.remove("show");
      },

      cancel_orders:function() {
        document.getElementById('order_dialog').classList.remove("show");
      },

      clear_orders: function() {
        this.od_opt1 = '';
        this.od_ord1 = '';
        this.od_opt2 = '';
        this.od_ord2 = '';

        let _this = this;

        window.location.href =
          "quotation_mgt?" +
    
          "fpt=" +
          _this.fil_creator +

          "&key=" +
          _this.fil_keyword +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page;
      },

      clear_filters: function() {
        this.fil_project_category = '';
        this.fil_client_type = '';
        this.fil_priority = '';
        this.fil_group = '';
        this.fil_status = '';
        this.fil_stage = '';
        this.fil_creator = '';
        this.fil_lower = '';
        this.fil_upper = '';
        this.fil_keyword = '';

        let _this = this;

        window.location.href =
          "quotation_mgt?" +
 
          "fpt=" +
          _this.fil_creator +
     
          "&key=" +
          _this.fil_keyword +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page;
      },

      apply_filters: function() {
        let _this = this;

        window.location.href =
          "quotation_mgt?" +
  
          "fpt=" +
          _this.fil_creator +

          "&key=" +
          _this.fil_keyword +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page;
      },

      apply_orders: function() {
        let _this = this;

        window.location.href =
          "quotation_mgt?" +
 
          "fpt=" +
          _this.fil_creator +

          "&key=" +
          _this.fil_keyword +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page;
      },

        shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
        },


  }
});