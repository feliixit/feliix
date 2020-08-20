var app = new Vue({
  el: '#app',
  data:{
    project_id: 0,
    receive_records: [],
    receive_stage_records: [],
    record: {},

    categorys : {},
    client_types : {},
    priorities : {},
    statuses : {},
    stages : {},


    category: '',
    client_type : '',
    priority:'',
    username:'',
    stage:'',
    created_at:'',
    end_at:'',
    location:'',
    contactor:'',
    contact_number:'',
    edit_reason:'',

    // + sign
    stage_sequence:'',
    project_stage:'',
    stage_status:'',


    // Change Project Status
    project_status:'',
    project_status_edit:'',
    project_status_reason:'',



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
    perPage: 20,

  },

  created () {
    let _this = this;
    let uri = window.location.href.split('?');
    if (uri.length == 2)
    {
      let vars = uri[1].split('&');
      let getVars = {};
      let tmp = '';
      vars.forEach(function(v){
        tmp = v.split('=');
        if(tmp.length == 2)
        _this.project_id = tmp[1];
        _this.getRecordsStage(_this.project_id);
        _this.getProject(_this.project_id);
      });
    }

    this.getStages();
    this.getStatuses();
  },

  computed: {
    displayedStagePosts () {
      this.setPages();
        return this.paginate(this.receive_stage_records);
    },

    showExtra: function(){
      return (this.status==10);
    },
  },

  mounted(){
 
    
  },

  watch: {

    receive_stage_records () {
        console.log('Vue watch receive_stage_records');
        this.setPages();
      },

  },



  methods:{

    setPages () {
        console.log('setPages');
        this.pages = [];
        let numberOfPages = Math.ceil(this.receive_stage_records.length / this.perPage);

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
        return  this.receive_stage_records.slice(from, to);
      },

    getRecordsStage: function(keyword) {
      let _this = this;

      if(keyword == 0)
        return;

      const params = {
              pid : keyword,
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project02_stages', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.receive_stage_records = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getProject: function(keyword) {

          let _this = this;

          if(keyword == 0)
            return;

          const params = {
              pid : keyword,
            };
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project02', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.category = res.data[0].category;
                  _this.client_type = res.data[0].client_type;
                  _this.priority = res.data[0].priority;
                  _this.username = res.data[0].username;
                  _this.stage = res.data[0].stage;
                  _this.project_status = res.data[0].project_status;

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
            form_Data.append('probability', this.probability);
            form_Data.append('special_note', this.special_note);

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
                    _this.clear();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
      },

      clear: function() {
        this.project_name = '';
        this.project_category = '';
        this.client_type = '';
        this.priority = '';
        this.status = '';
        this.reason = '';
        this.probability = '';
        this.special_note = '';
        
        document.getElementById('insert_dialog').classList.remove("show");


        this.receive_stage_records = [];

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

        stage_clear() {
            this.stage_sequence = '';
            this.project_stage = '';
            this.stage_status = '';
            
            
            document.getElementById('stage_dialog').classList.remove("show");
            document.getElementById('stage_fn1').classList.remove("focus");

            this.receive_stage_records = [];

            this.getRecordsStage(this.project_id);
        },

        status_clear() {
            this.project_status_edit = '';
            this.project_status_reason = '';
      
            document.getElementById('status_dialog').classList.remove("show");
            document.getElementById('status_fn1').classList.remove("focus");
            //this.receive_stage_records = [];

            //this.getRecordsStage(this.project_id);
        },

        status_create() {
            let _this = this;

            if (this.project_status_reason.trim() == '') {
              Swal.fire({
                text: 'Please enter Stage Sequence!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.project_status_edit.trim() == '') {
              Swal.fire({
                text: 'Please select Project Status!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('pid', this.project_id);
            form_Data.append('project_status_edit', this.project_status_edit);
            form_Data.append('project_status_reason', this.project_status_reason.trim());

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project02_insert_status',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.status_clear();
                    _this.getProject(_this.project_id);
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },


        stage_add() {
          let _this = this;

            if (this.stage_sequence.trim() == '') {
              Swal.fire({
                text: 'Please enter Stage Sequence!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.project_stage.trim() == '') {
              Swal.fire({
                text: 'Please select Project Stage!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.stage_status.trim() == '') {
              Swal.fire({
                text: 'Please select Stage Status!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }


            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('pid', this.project_id);
            form_Data.append('stage_sequence', this.stage_sequence);
            form_Data.append('project_stage', this.project_stage);
            form_Data.append('stage_status', this.stage_status);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project02_insert_stage',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.stage_clear();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },


  }
});