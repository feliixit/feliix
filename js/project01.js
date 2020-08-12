
var app = new Vue({
  el: '#app',
  data:{
    project_category : '',
    client_type : '',
    priority: '',
    status : '',
    reason : '',
    project_name : '',

    fil_project_category : '',
    fil_client_type : '',
    fil_priority: '',
    fil_status : '',
    fil_stage : '',


    receive_records: [],
    record: {},

    categorys : {},
    client_types : {},
    priorities : {},
    statuses : {},
    stages : {},

    submit : false,
    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      {name: '10', id: 10},
      {name: '25', id: 25},
      {name: '50', id: 50},
      {name: '100', id: 100},
      {name: 'All', id: 10000}
    ],
    perPage: 5,

  },

  created () {
    this.getRecords();
    this.getProjectCategorys();
    this.getClientTypes();
    this.getPrioritys();
    this.getStatuses();
    this.getStages();
  },

  computed: {
    displayedPosts () {
      this.setPages();
        return this.paginate(this.receive_records);
    },
  },

  mounted(){
 
    
  },

  watch: {

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
  },



  methods:{

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
                fpc: _this.fil_project_category,
                fct: _this.fil_client_type,
                fp: _this.fil_priority,
                fs: _this.fil_status,
                fcs: _this.fil_stage,
            };

      
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project01', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.receive_records = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

    getProjectCategorys () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/admin/project_category', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.categorys = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getClientTypes () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/admin/project_client_type', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.client_types = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getPrioritys () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/admin/project_priority', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.priorities = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },
    
      getStatuses () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');

          
    
          axios
              .get('api/admin/project_status', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.statuses = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getStages () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/admin/project_stage', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.stages = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

    
    getLeaveCredit: function() {
      let _this = this;

      axios.get('api/ammend')
          .then(function(response) {
              console.log(response.data);
              _this.receive_records = response.data;


          })
          .catch(function(error) {
              console.log(error);
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

            if (this.project_name.trim() == '') {
              Swal.fire({
                text: 'Please enter Project Name!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.project_category.trim() == '') {
              Swal.fire({
                text: 'Please select Project Category!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.client_type.trim() == '') {
              Swal.fire({
                text: 'Please select Client Type!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.priority.trim() == '') {
              Swal.fire({
                text: 'Please select Priority!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('project_name', this.project_name);
            form_Data.append('project_category', this.project_category);
            form_Data.append('client_type', this.client_type);
            form_Data.append('priority', this.priority);
            form_Data.append('status', this.status);
            form_Data.append('reason', this.reason);
      

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project01_insert',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });

              _this.clear();
     

      },

      clear: function() {
        this.project_name = '';
        this.project_category = '';
        this.client_type = '';
        this.priority = '';
        this.status = '';
        this.reason = '';
        
        document.getElementById('insert_dialog').classList.remove("show");

        this.getRecords();
        this.receive_records = [];

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