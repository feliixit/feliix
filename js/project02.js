var app = new Vue({
  el: '#app',
  data:{
    project_id: 0,
    receive_records: [],
    receive_stage_records: [],
    record: {},

    keyword: '',

    project_comments: {},
    project_probs: {},
    project_action_detials: {},

    categorys : {},
    client_types : {},
    priorities : {},
    statuses : {},
    stages : {},
    users : {},

    uid:0,
    org_uid:0,

    stage_id_to_edit:0,

    baseURL:'https://storage.cloud.google.com/',

    pageURL:'https://feliix.myvnc.com/',

    category: '',
    category_id:0,
    client_type : '',
    client_type_id:0,
    priority:'',
    priority_id:0,
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

    // Edit Project Info
    edit_category:'',
    edit_client_type:'',
    edit_priority: '',
    edit_contactor:'',
    edit_contact_number:'',
    edit_location:'',
    edit_project_reason:'',

    //  Action to Comments
    comment : '',
    file1: '',
    file2: '',
    comm_fileArray: [],
    comm_canSub: true,
    comm_finish: false,

    startValue: 0,

    //Acton to Est. Closing Prob.
    probability : 0,
    prob_reason :'',

    // Acton to Project Details
    detail_type: '',
    detail_desc: '',
    fileArray: [],
    canSub: true,
    finish: false,
    verified_downpayment: false,

    // Edit/Delete Stage
    record : {},
    stage_edit_reason:'',

    // Downpayment Proof
    prof_remark:'',
    prof_fileArray: [],
    prof_canSub: true,
    prof_finish: false,

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

    file_management: [],

    projectname:'',

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
        _this.getProjectComments(_this.project_id);
        _this.getProjectProbs(_this.project_id);
        _this.getProjectProof(_this.project_id);
        _this.getProjectActionDetails(_this.project_id);
        _this.getUsers();
        _this.getFileManagement(_this.project_id);
        _this.getProjectInfo(_this.project_id);
      });
    }

    this.getProjectCategorys();
    this.getClientTypes();
    this.getPrioritys();
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

    keyword () {
        console.log('Vue watch keyword');
        this.getFileManagement(this.project_id);
      },

  fileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.fileArray.length) {
          
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish = true;
            _this.getProjectActionDetails(_this.project_id);

          });
          this.detail_clear();

        }
      },
      deep: true
    },

    prof_fileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.prof_fileArray.length) {
          this.prof_finish = true;
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.prof_finish = true;
            _this.getProjectActionDetails(_this.project_id);

          });;
          this.prof_clear();
        }
      },
      deep: true
    },

    comm_fileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.comm_fileArray.length) {
          this.comm_finish = true;
          
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.comm_finish = true;

            _this.getProjectComments(_this.project_id);
          });
          this.comment_clear();
        }
      },
      deep: true
    },

  },



  methods:{

    deleteFile(index) {
      this.fileArray.splice(index, 1);
      var fileTarget = this.$refs.file;
      fileTarget.value = "";
    },

    prof_deleteFile(index) {
      this.prof_fileArray.splice(index, 1);
      var fileTarget = this.$refs.prof_file;
      fileTarget.value = "";
    },

    prof_changeFile() {
      var fileTarget = this.$refs.prof_file;

      for (i = 0; i < fileTarget.files.length; i++) {
          // remove duplicate
          if (
            this.prof_fileArray.indexOf(fileTarget.files[i]) == -1 ||
            this.prof_fileArray.length == 0
          ) {
            var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
            this.prof_fileArray.push(fileItem);
          }else{
            fileTarget.value = "";
          }
        }
    },

    comm_deleteFile(index) {
      this.comm_fileArray.splice(index, 1);
      var fileTarget = this.$refs.comm_file;
      fileTarget.value = "";
    },

    comm_changeFile() {
      var fileTarget = this.$refs.comm_file;

      for (i = 0; i < fileTarget.files.length; i++) {
          // remove duplicate
          if (
            this.comm_fileArray.indexOf(fileTarget.files[i]) == -1 ||
            this.comm_fileArray.length == 0
          ) {
            var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
            this.comm_fileArray.push(fileItem);
          }else{
            fileTarget.value = "";
          }
        }
    },

    changeFile() {
      var fileTarget = this.$refs.file;
      /*
      if (this.fileArray.length >= 10) {
        Swal.fire({
          text: "ten files",
          type: "success",
          duration: 1 * 1000,
          customClass: "message-box",
          iconClass: "message-icon"
        });
        fileTarget.value = "";
      } else {
        if (fileTarget.files[0].size > 31457280) {
          Swal.fire({
            text: "30M",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          });
          fileTarget.value = "";
        } else {
            
        */
        for (i = 0; i < fileTarget.files.length; i++) {
            // remove duplicate
            if (
              this.fileArray.indexOf(fileTarget.files[i]) == -1 ||
              this.fileArray.length == 0
            ) {
              var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
              this.fileArray.push(fileItem);
            }else{
              fileTarget.value = "";
            }
          }
    },

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

      getProjectComments: function(keyword) {
      let _this = this;

      if(keyword == 0)
        return;

      const params = {
              pid : keyword,
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project_comments', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.project_comments = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      change_project_creator: function() {

      let _this = this;

      if(this.uid == this.org_uid)
        return;

       var form_Data = new FormData();

       form_Data.append('pid', this.project_id);
        form_Data.append('new_id', this.uid);
      
        const token = sessionStorage.getItem('token');

        axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                },
                url: 'api/project_change_creator',
                data: form_Data
            })
            .then(function(response) {
                //handle success
                //this.$forceUpdate();
                

                if(response.data['batch_id'] != 0)
                {
                  _this.org_uid = _this.uid;
                  
                    Swal.fire({
                      text: "user changed",
                      icon: 'success',
                      confirmButtonText: 'OK'
                    })
                }
            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });
      },

      getProjectProof: function(keyword) {
      let _this = this;

      if(keyword == 0)
        return;

      const params = {
              pid : keyword,
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project_proof_approval', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  if(res.data.length > 0)
                    _this.verified_downpayment = res.data[0].status;

                    _this.getStages(_this.verified_downpayment);
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getProjectProbs: function(keyword) {
      let _this = this;

      if(keyword == 0)
        return;

      const params = {
              pid : keyword,
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project_est_prob', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.project_probs = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getProjectInfo(pid) {

        let _this = this;
  
        if (pid == 0)
          return;
  
        const params = {
          pid: pid,
  
        };
  
        let token = localStorage.getItem('accessToken');
  
        axios
          .get('api/project02_get_project_name_by_project_id', {params, headers: { "Authorization": `Bearer ${token}` } })
          .then(
            (res) => {
              _this.projectname = res.data;
            },
            (err) => {
              alert(err.response);
            },
          )
          .finally(() => {
  
          });
      },

      getProjectActionDetails: function(keyword) {
      let _this = this;

      if(keyword == 0)
        return;

      const params = {
              pid : keyword,
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project_action_detail', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.project_action_detials = res.data;
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
                  _this.uid = res.data[0].uid;
                  _this.org_uid = res.data[0].uid;
                  _this.username = res.data[0].username;
                  _this.stage = res.data[0].stage;
                  _this.project_status = res.data[0].project_status;

                  _this.category_id = res.data[0].category_id;
                  _this.client_type_id = res.data[0].client_type_id;
                  _this.priority_id = res.data[0].priority_id;
                  _this.contactor = res.data[0].contactor;
                  _this.location = res.data[0].location;
                  _this.contact_number = res.data[0].contact_number;

                  _this.edit_category = res.data[0].category_id;
                  _this.edit_client_type = res.data[0].client_type_id;
                  _this.edit_priority = res.data[0].priority_id;
                  _this.edit_contactor = res.data[0].contactor;
                  _this.edit_location = res.data[0].location;
                  _this.edit_contact_number = res.data[0].contact_number;

                  _this.created_at = res.data[0].created_at;
                  _this.end_at = res.data[0].updated_at;

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

      stage_load () {
        if(this.stage_id_to_edit != 0)
        {
          this.record = {};
          this.record = this.shallowCopy(this.receive_stage_records.find(element => element.id == this.stage_id_to_edit));
        }
      },

      shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
        },

      stage_delete () {
        let _this = this;
        if(this.stage_id_to_edit != 0)
        {
          Swal.fire({
              title: "Delete",
              text: "Are you sure to delete?",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
              if (result.value) {
               
                  _this.do_stage_delete(); // <--- submit form programmatically
                
              } else {
                // swal("Cancelled", "Your imaginary file is safe :)", "error");
              }
            });
        }
      },

      do_stage_delete() {
        var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;

        form_Data.append('jwt', token);
        form_Data.append('pid', this.project_id);
        form_Data.append('stage_id_to_edit', this.stage_id_to_edit);

        axios({
            method: 'post',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/project02_delete_stage',
            data: form_Data
        })
        .then(function(response) {
            //handle success
            if(response.data['ret'] != 0)
            {
              _this.org_uid = _this.uid;
              
                Swal.fire({
                  text: "Deleted",
                  icon: 'success',
                  confirmButtonText: 'OK'
                })

                _this.getRecordsStage(_this.project_id);
            }

            _this.edit_stage_clear();

        })
        .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: 'error',
              confirmButtonText: 'OK'
            });

            _this.edit_stage_clear();
        });
      },

      getUsers () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project02_user', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.users = res.data;
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

      getStages (keyword) {

          let _this = this;
          val = 0;

          if(keyword == false)
            val = 1;

          const params = {
            keyword : val,
           
          };
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/admin/project_stage', { params, headers: {"Authorization" : `Bearer ${token}`} })
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

        comment_clear() {
            this.comment = '';

            this.comm_fileArray = [];
            this.$refs.comm_file.value = '';

            this.getProjectComments(this.project_id);

            document.getElementById('comment_dialog').classList.remove("show");
            document.getElementById('project_fn3').classList.remove("focus");
        },

        file_dialog_clear() {

          this.keyword = '';
          document.getElementById('file_stage_dialog').classList.remove("show");
          document.getElementById('f_stage_fn1').classList.remove("focus");
      },


        detail_clear() {

            this.detail_type = '';
            this.detail_desc = '';

            this.fileArray = [];
            this.$refs.file.value = '';

            //this.getProjectDetail(this.project_id);
            this.getProjectActionDetails(this.project_id);
            this.canSub = true;
            
            document.getElementById('detail_dialog').classList.remove("show");
            document.getElementById('status_fn5').classList.remove("focus");
        },


        prof_clear() {

            this.prof_remark = '';
            this.prof_fileArray = [];
            this.$refs.prof_file.value = '';

            //this.getProjectDetail(this.project_id);
            this.prof_canSub = true;

            this.getProjectActionDetails(this.project_id);
            
            document.getElementById('prof_dialog').classList.remove("show");
            document.getElementById('status_fn6').classList.remove("focus");
        },


        prob_clear() {

            this.probability = 0;
            this.prob_reason = '';

            this.getProjectProbs(this.project_id);
            
            document.getElementById('prob_dialog').classList.remove("show");
            document.getElementById('status_fn4').classList.remove("focus");
        },

        project_clear() {

            this.uid = this.org_uid;
            this.edit_category = this.category_id;
            this.edit_client_type = this.client_type_id;
            this.edit_priority = this.priority_id;
            this.edit_contactor = this.contactor;
            this.edit_location = this.location;
            this.edit_contact_number = this.contact_number;
            
            document.getElementById('project_dialog').classList.remove("show");
            document.getElementById('project_fn2').classList.remove("focus");
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

        edit_stage_clear() {

            this.record = {};
            this.stage_edit_reason = '';
            document.getElementById('edit_stage_dialog').classList.remove("show");
            document.getElementById('edit_stage_fn1').classList.remove("focus");

        
        },

        status_clear() {
            this.project_status_edit = '';
            this.project_status_reason = '';
      
            document.getElementById('status_dialog').classList.remove("show");
            document.getElementById('status_fn1').classList.remove("focus");
            //this.receive_stage_records = [];

            //this.getRecordsStage(this.project_id);
        },

        prob_create() {
            let _this = this;

            if (this.prob_reason.trim() == '') {
              Swal.fire({
                text: 'Please enter probability reason!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }


            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('pid', this.project_id);
            form_Data.append('probability', this.probability);
            form_Data.append('prob_reason', this.prob_reason.trim());

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project_est_prob',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.prob_clear();
                    _this.getProject(_this.project_id);
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },


        prof_create() {
            let _this = this;


            if (this.prof_remark.trim() == '') {
              Swal.fire({
                text: 'Please enter remark!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }


            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('pid', this.project_id);
            form_Data.append('remark', this.prof_remark.trim());

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project_proof',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    if(response.data['batch_id'] != 0)
                    {
                        _this.prof_upload(response.data['batch_id']);
                    }
                    else
                    {
                      _this.prof_clear();
                  
                    }

                    if(_this.prof_fileArray.length == 0)
                      _this.prof_clear();

                     _this.sendNotifyEmail(response.data['batch_id']);

                    
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        sendNotifyEmail(batch_id) {
            var form_Data = new FormData();

            form_Data.append('bid', batch_id);
           
            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project_proof_mail',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        detail_create() {
            let _this = this;

            if (this.detail_type.trim() == '') {
              Swal.fire({
                text: 'Please select detail type!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.detail_desc.trim() == '') {
              Swal.fire({
                text: 'Please enter detail description!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }


            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('pid', this.project_id);
            form_Data.append('detail_type', this.detail_type);
            form_Data.append('detail_desc', this.detail_desc.trim());

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project_action_detail',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    if(response.data['batch_id'] != 0)
                    {
                        _this.upload(response.data['batch_id']);
                    }
                    else
                    {
                      _this.detail_clear();
                  
                    }

                    if(_this.fileArray.length == 0)
                      _this.detail_clear();
                 
                    
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        prof_upload(batch_id) {
            
              this.prof_canSub = false;
              var myArr = this.prof_fileArray;
              var vm = this;
             
              //循环文件数组挨个上传
              myArr.forEach((element, index) => {
                var config = {
                  headers: { "Content-Type": "multipart/form-data" },
                  onUploadProgress: function(e) {
         
                    if (e.lengthComputable) {
                      var rate = e.loaded / e.total; 
                      console.log(index, e.loaded, e.total, rate);
                      if (rate < 1) {
                        
                        myArr[index].progress = rate;
                        vm.$set(vm.prof_fileArray, index, myArr[index]);
                      } else {
                        myArr[index].progress = 0.99;
                        vm.$set(vm.prof_fileArray, index, myArr[index]);
                      }
                    }
                  }
                };
                var data = myArr[index];
                var myForm = new FormData();
                myForm.append('batch_type', 'proof');
                myForm.append('batch_id', batch_id);
                myForm.append("file", data);
       
                axios
                  .post("api/uploadFile_gcp", myForm, config)
                  .then(function(res) {
                    if (res.data.code == 0) {
                 
                      myArr[index].progress = 1;
                      vm.$set(vm.prof_fileArray, index, myArr[index]);
                      console.log(vm.prof_fileArray, index);
                    } else {
                      alert(JSON.stringify(res.data));
                    }
                  })
                  .catch(function(err) {
                    console.log(err);
                  });
              });

              this.prof_canSub = true;
            
          },

          comm_upload(batch_id) {
            
              this.comm_canSub = false;
              var myArr = this.comm_fileArray;
              var vm = this;
             
              //循环文件数组挨个上传
              myArr.forEach((element, index) => {
                var config = {
                  headers: { "Content-Type": "multipart/form-data" },
                  onUploadProgress: function(e) {
         
                    if (e.lengthComputable) {
                      var rate = e.loaded / e.total; 
                      console.log(index, e.loaded, e.total, rate);
                      if (rate < 1) {
                        
                        myArr[index].progress = rate;
                        vm.$set(vm.comm_fileArray, index, myArr[index]);
                      } else {
                        myArr[index].progress = 0.99;
                        vm.$set(vm.comm_fileArray, index, myArr[index]);
                      }
                    }
                  }
                };
                var data = myArr[index];
                var myForm = new FormData();
                myForm.append('batch_type', 'comment');
                myForm.append('batch_id', batch_id);
                myForm.append("file", data);
       
                axios
                  .post("api/uploadFile_gcp", myForm, config)
                  .then(function(res) {
                    if (res.data.code == 0) {
                 
                      myArr[index].progress = 1;
                      vm.$set(vm.comm_fileArray, index, myArr[index]);
                      console.log(vm.comm_fileArray, index);
                    } else {
                      alert(JSON.stringify(res.data));
                    }
                  })
                  .catch(function(err) {
                    console.log(err);
                  });
              });

              this.comm_canSub = true;
            
          },


        upload(batch_id) {
            
              this.canSub = false;
              var myArr = this.fileArray;
              var vm = this;
             
              //循环文件数组挨个上传
              myArr.forEach((element, index) => {
                var config = {
                  headers: { "Content-Type": "multipart/form-data" },
                  onUploadProgress: function(e) {
         
                    if (e.lengthComputable) {
                      var rate = e.loaded / e.total; 
                      console.log(index, e.loaded, e.total, rate);
                      if (rate < 1) {
                        
                        myArr[index].progress = rate;
                        vm.$set(vm.fileArray, index, myArr[index]);
                      } else {
                        myArr[index].progress = 0.99;
                        vm.$set(vm.fileArray, index, myArr[index]);
                      }
                    }
                  }
                };
                var data = myArr[index];
                var myForm = new FormData();
                myForm.append('batch_type', 'action_detail');
                myForm.append('batch_id', batch_id);
                myForm.append("file", data);
       
                axios
                  .post("api/uploadFile_gcp", myForm, config)
                  .then(function(res) {
                    if (res.data.code == 0) {
                 
                      myArr[index].progress = 1;
                      vm.$set(vm.fileArray, index, myArr[index]);
                      console.log(vm.fileArray, index);
                    } else {
                      alert(JSON.stringify(res.data));
                    }
                  })
                  .catch(function(err) {
                    console.log(err);
                  });
              });
              
              this.canSub = true;
          },

        comment_create() {
            let _this = this;

            if (this.comment.trim() == '') {
              Swal.fire({
                text: 'Please input Comment!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('pid', this.project_id);
            form_Data.append('comment', this.comment);
  
            
            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    
                    url: 'api/project02_action_comment',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    if(response.data['batch_id'] != 0)
                    {
                        _this.comm_upload(response.data['batch_id']);
                    }else
                    {
                      _this.comment_clear();
                      _this.getProject(_this.project_id);
                    }

                    if(_this.comm_fileArray.length == 0)
                      _this.comment_clear();

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },


        save_edit_stage() {
            let _this = this;

            if(this.stage_id_to_edit == 0)
              return;

            if (this.record.sequence.trim() == '') {
              Swal.fire({
                text: 'Please edit Statge Sequence!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.record.project_stage_id.trim() == 0) {
              Swal.fire({
                text: 'Please select Stage!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.record.stages_status_id.trim() == 0) {
              Swal.fire({
                text: 'Please select Stage Status!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.stage_edit_reason.trim() == '') {
              Swal.fire({
                text: 'Please input edit reason!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }


            _this.submit = true;
            var form_Data = new FormData();

            form_Data.append('stage_id', this.stage_id_to_edit);
            form_Data.append('sequence', this.record.sequence);
            form_Data.append('project_stage_id', this.record.project_stage_id);
            form_Data.append('stages_status_id', this.record.stages_status_id);
            form_Data.append('stage_edit_reason', this.stage_edit_reason);
           
            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project02_edit_project_stage',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.getRecordsStage(_this.project_id);
                    _this.edit_stage_clear();

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        project_create() {
            let _this = this;

            if (this.edit_category.trim() == 0) {
              Swal.fire({
                text: 'Please select Project Category!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.edit_client_type.trim() == 0) {
              Swal.fire({
                text: 'Please select Client Type!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.edit_priority.trim() == 0) {
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

            form_Data.append('pid', this.project_id);
            form_Data.append('edit_category', this.edit_category);
            form_Data.append('edit_client_type', this.edit_client_type);
            form_Data.append('edit_priority', this.edit_priority);
            form_Data.append('edit_contactor', this.edit_contactor);
            form_Data.append('creator', this.uid);
            form_Data.append('edit_location', this.edit_location);
            form_Data.append('edit_contact_number', this.edit_contact_number);
            form_Data.append('edit_project_reason', this.edit_project_reason);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project02_edit_project_info',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.project_clear();
                    _this.getProject(_this.project_id);
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
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

        getFileManagement: function(id) {
          let _this = this;
    
          if(id == 0)
            return;
    
          const params = {
                  pid : id,
                  keyword : this.keyword,
                };
    
              let token = localStorage.getItem('accessToken');
        
              axios
                  .get('api/project_file_management', { params, headers: {"Authorization" : `Bearer ${token}`} })
                  .then(
                  (res) => {
                      _this.file_management = res.data;
                  },
                  (err) => {
                      alert(err.response);
                  },
                  )
                  .finally(() => {
                      
                  });
          },


  }
});