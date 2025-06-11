Vue.component('v-select', VueSelect.VueSelect)

var app = new Vue({
  el: '#app',
  data: {
    canSub_r: true,
    finish_r: false,

    fileArray_r: [],
    arrMsg_r: [],

    stage_id: 0,
    receive_records: [],
    record: {},

    project03_client_stage_task: {},

    users : {},

    submit : false,

    contactor : '',
    username : '',
    client_type : '',
    category : '',

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      { name: '10', id: 10 },
      { name: '25', id: 25 },
      { name: '50', id: 50 },
      { name: '100', id: 100 },
      { name: 'All', id: 10000 }
    ],
    perPage: 20,

    baseURL:'https://storage.googleapis.com/feliiximg/',

    // Venue
    venue: '',
    stage_client_venue : {},

    // Sales Assigned
    uid:0,
    org_uid:0,
    stage_client_sales : {},

    // Date
    dt: '',
    stage_client_date : {},

    // Status
    status: 0,
    stage_client_status : {},

    // Priority
    priority: 0,
    stage_client_priority : {},

    // Amount
    amount: '',
    stage_client_amount : {},

    // Competitor
    competitor: '',
    stage_client_competitor : {},

    // Downpayment Proof
    prof_remark:'',
    prof_fileArray: [],
    prof_canSub: true,
    prof_finish: false,
    stage_client_infomation : {},

    // Project Task Tracker
    tid : 0,
    stage_task : '',

    // calendar
    attendee:[],
    old_attendee:[],
    add_id: 0,

    // project_name
    project_id : 0,
    project_name : '',

    client :'',
    project_status:'',
    project_proirity:'',

    project_created_by:'',
    project_created_at:'',

  },

  created() {
    let _this = this;
    let uri = window.location.href.split('?');
    if (uri.length == 2) {
      let vars = uri[1].split('&');
      let getVars = {};
      let tmp = '';
      vars.forEach(function (v) {
        tmp = v.split('=');
        if (tmp.length == 2)
          _this.stage_id = tmp[1];
          _this.get_stage_client_venue(_this.stage_id);
          _this.get_stage_client_sales(_this.stage_id);
          _this.get_stage_client_date(_this.stage_id);
          _this.get_stage_client_status(_this.stage_id);
          _this.get_stage_client_priority(_this.stage_id);
          _this.get_stage_client_amount(_this.stage_id);
          _this.get_stage_client_competitor(_this.stage_id);
          _this.get_stage_client_infomation(_this.stage_id);
          _this.get_stage_client_task(_this.stage_id);

          _this.get_stage_client(_this.stage_id);
          _this.getProjectInfo(_this.stage_id);
      });
    }

    _this.getUsers();

  },

  computed: {
    displayedStagePosts() {
      this.setPages();
      return this.paginate(this.receive_records);
    },



  },

  mounted() {


  },

  watch: {

    receive_records() {
      console.log('Vue watch receive_stage_records');
      this.setPages();
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
              _this.get_stage_client_infomation(_this.stage_id);

            });;
            this.prof_clear();
          }
        },
        deep: true
      },

  },



  methods: {
    deleteMsgFile_r(item_id, index) {
      this.current_msg_item_id_r = item_id;

      this.arrMsg_r[item_id].splice(index, 1);
      var fileTarget = this.$refs['file_msg_r_' + item_id][0];
      fileTarget.value = "";
      Vue.set(this.arrMsg_r, 0, '');
    },
    
    changeMsgFile_r(item_id) {
      this.current_msg_item_id_r = item_id;

      var arr = this.arrMsg_r[item_id];
      if (typeof arr === 'undefined' || arr.length == 0)
        this.arrMsg_r[item_id] = [];

      var fileTarget = this.$refs['file_msg_r_' + item_id][0];

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.arrMsg_r[item_id].indexOf(fileTarget.files[i]) == -1 ||
          this.arrMsg_r[item_id].length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.arrMsg_r[item_id].push(fileItem);
          Vue.set(this.arrMsg_r, 0, '');
        } else {
          fileTarget.value = "";
        }
      }
    },

    msgItems_r(item_id) {
      var arr = this.arrMsg_r[item_id];
      return arr;
    },


    deleteFile_r(index) {
      this.fileArray_r.splice(index, 1);
      var fileTarget = this.$refs.file_r;
      fileTarget.value = "";
    },

    changeFile_r() {
      var fileTarget = this.$refs.file_r;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.fileArray_r.indexOf(fileTarget.files[i]) == -1 ||
          this.fileArray_r.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.fileArray_r.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    setPages() {
      console.log('setPages');
      this.pages = [];
      let numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if (numberOfPages == 1)
        this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function (posts) {
      console.log('paginate');
      if (this.page < 1)
        this.page = 1;
      if (this.page > this.pages.length)
        this.page = this.pages.length;

      let page = this.page;
      let perPage = this.perPage;
      let from = (page * perPage) - perPage;
      let to = (page * perPage);
      return this.receive_records.slice(from, to);
    },

    get_stage_client_venue: function(stage_id) {
      let _this = this;

      if(stage_id == 0)
        return;

      const params = {
              stage_id : stage_id,
              type : 'venue',
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.stage_client_venue = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getProjectInfo(stage_id) {

        let _this = this;
  
        if (stage_id == 0)
          return;
  
        const params = {
          stage_id: stage_id,
  
        };
  
        let token = localStorage.getItem('accessToken');
  
        axios
          .get('api/project03_get_project_name_by_stage_id', {params, headers: { "Authorization": `Bearer ${token}` } })
          .then(
            (res) => {
              _this.project_name = res.data[0].project_name;
              _this.project_id = res.data[0].project_id;
            },
            (err) => {
              alert(err.response);
            },
          )
          .finally(() => {
  
          });
      },

      get_stage_client_task: function(stage_id) {
      let _this = this;

      if(stage_id == 0)
        return;

      const params = {
              stage_id : stage_id,
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_stage_client_task_comment', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.project03_client_stage_task = res.data;
                  _this.tid = _this.project03_client_stage_task.length;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

    get_stage_client: function(sid) {
      let _this = this;

      if(sid == 0)
        return;

      const params = {
              sid : sid,
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_client_stage_data', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.contactor = res.data[0].contactor;
                  _this.username = res.data[0].username;
                  _this.client_type = res.data[0].client_type;
                  _this.category = res.data[0].category;
                  _this.client = res.data[0].client;
                  _this.project_proirity = res.data[0].priority;
                  _this.project_status = res.data[0].project_status;

                  _this.project_created_by = res.data[0].username;
                  _this.project_created_at = res.data[0].created_at;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

    getUsers () {

        let _this = this;
  
        let token = localStorage.getItem('accessToken');
  
        axios
            .get('api/project02_user_online', { headers: {"Authorization" : `Bearer ${token}`} })
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

    venue_clear() {

      this.venue = "";

      document.getElementById('dialog_a1').classList.remove("show");
      document.getElementById('add_a1').classList.remove("focus");
    },

    venue_create() {
      let _this = this;

      if (this.venue.trim() == '') {
        Swal.fire({
          text: 'Please enter venue!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('message', this.venue.trim());
      form_Data.append('type', 'venue');

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_stage_client',
        data: form_Data
      })
        .then(function (response) {
          //handle success
          //this.$forceUpdate();
          _this.get_stage_client_venue(_this.stage_id);
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.venue_clear()});
    },

    sales_clear() {

      this.uid = this.org_uid;

      document.getElementById('dialog_a2').classList.remove("show");
      document.getElementById('add_a2').classList.remove("focus");
    },


    sales_create() {
      let _this = this;

        if (this.uid.trim() == 0) {
          Swal.fire({
            text: 'Please select Sales Assigned!',
            icon: 'warning',
            confirmButtonText: 'OK'
          })
            
            //$(window).scrollTop(0);
            return;
        }

      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('option', this.uid.trim());
      form_Data.append('type', 'sales');

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_stage_client',
        data: form_Data
      })
        .then(function (response) {
          //handle success
          //this.$forceUpdate();
          _this.get_stage_client_sales(_this.stage_id);
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.sales_clear()});
    },

    get_stage_client_sales: function(stage_id) {
      let _this = this;

      if(stage_id == 0)
        return;

      const params = {
              stage_id : stage_id,
              type : 'sales',
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_stage_client_sales', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.stage_client_sales = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      date_clear() {

        this.dt = '';
  
        document.getElementById('dialog_a3').classList.remove("show");
        document.getElementById('add_a3').classList.remove("focus");
      },
  
      date_create() {
        let _this = this;

        if (this.dt.trim() == '') {
          Swal.fire({
            text: 'Please select Target Date of Project!',
            icon: 'warning',
            confirmButtonText: 'OK'
          })
            
            //$(window).scrollTop(0);
            return;
        }
   
        _this.submit = true;
        var form_Data = new FormData();
  
        form_Data.append('stage_id', this.stage_id);
        form_Data.append('message', this.dt);
        form_Data.append('type', 'date');
  
        const token = sessionStorage.getItem('token');
  
        axios({
          method: 'post',
          headers: {
            'Content-Type': 'multipart/form-data',
            Authorization: `Bearer ${token}`
          },
          url: 'api/project03_stage_client',
          data: form_Data
        })
          .then(function (response) {
            //handle success
            //this.$forceUpdate();
            _this.get_stage_client_date(_this.stage_id);
          })
          .catch(function (response) {
            //handle error
            console.log(response)
          }).finally(function () {_this.date_clear()});
      },
  
      get_stage_client_date: function(stage_id) {
        let _this = this;
  
        if(stage_id == 0)
          return;
  
        const params = {
                stage_id : stage_id,
                type : 'date',
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.stage_client_date = res.data;
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },


        status_clear() {

          this.status = '';
    
          document.getElementById('dialog_a4').classList.remove("show");
          document.getElementById('add_a4').classList.remove("focus");
        },
    
        status_create() {
          let _this = this;

          if (this.status.trim() == 0) {
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
    
          form_Data.append('stage_id', this.stage_id);
          form_Data.append('option', this.status.trim());
          form_Data.append('type', 'status');
    
          const token = sessionStorage.getItem('token');
    
          axios({
            method: 'post',
            headers: {
              'Content-Type': 'multipart/form-data',
              Authorization: `Bearer ${token}`
            },
            url: 'api/project03_stage_client',
            data: form_Data
          })
            .then(function (response) {
              //handle success
              //this.$forceUpdate();
              _this.get_stage_client_status(_this.stage_id);
            })
            .catch(function (response) {
              //handle error
              console.log(response)
            }).finally(function () {_this.status_clear()});
        },
    
        get_stage_client_status: function(stage_id) {
          let _this = this;
    
          if(stage_id == 0)
            return;
    
          const params = {
                  stage_id : stage_id,
                  type : 'status',
                };
    
              let token = localStorage.getItem('accessToken');
        
              axios
                  .get('api/project03_stage_client_status', { params, headers: {"Authorization" : `Bearer ${token}`} })
                  .then(
                  (res) => {
                      _this.stage_client_status = res.data;
                  },
                  (err) => {
                      alert(err.response);
                  },
                  )
                  .finally(() => {
                      
                  });
          },


          priority_clear() {

            this.priority = '';
      
            document.getElementById('dialog_a5').classList.remove("show");
            document.getElementById('add_a5').classList.remove("focus");
          },
      
          priority_create() {
            let _this = this;

            if (this.priority.trim() == 0) {
              Swal.fire({
                text: 'Please select Project Priority!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }
       
            _this.submit = true;
            var form_Data = new FormData();
      
            form_Data.append('stage_id', this.stage_id);
            form_Data.append('option', this.priority.trim());
            form_Data.append('type', 'priority');
      
            const token = sessionStorage.getItem('token');
      
            axios({
              method: 'post',
              headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${token}`
              },
              url: 'api/project03_stage_client',
              data: form_Data
            })
              .then(function (response) {
                //handle success
                //this.$forceUpdate();
                _this.get_stage_client_priority(_this.stage_id);
              })
              .catch(function (response) {
                //handle error
                console.log(response)
              }).finally(function () {_this.priority_clear()});
          },
      
          get_stage_client_priority: function(stage_id) {
            let _this = this;
      
            if(stage_id == 0)
              return;
      
            const params = {
                    stage_id : stage_id,
                    type : 'priority',
                  };
      
                let token = localStorage.getItem('accessToken');
          
                axios
                    .get('api/project03_stage_client_priority', { params, headers: {"Authorization" : `Bearer ${token}`} })
                    .then(
                    (res) => {
                        _this.stage_client_priority = res.data;
                    },
                    (err) => {
                        alert(err.response);
                    },
                    )
                    .finally(() => {
                        
                    });
            },

            amount_clear() {

              this.amount = '';
        
              document.getElementById('dialog_a6').classList.remove("show");
              document.getElementById('add_a6').classList.remove("focus");
            },
        
            amount_create() {
              let _this = this;

              if (this.amount.trim() == '') {
                Swal.fire({
                  text: 'Please enter Amount!',
                  icon: 'warning',
                  confirmButtonText: 'OK'
                })
                  
                  //$(window).scrollTop(0);
                  return;
              }
         
              _this.submit = true;
              var form_Data = new FormData();
        
              form_Data.append('stage_id', this.stage_id);
              form_Data.append('message', this.amount.trim());
              form_Data.append('type', 'amount');
        
              const token = sessionStorage.getItem('token');
        
              axios({
                method: 'post',
                headers: {
                  'Content-Type': 'multipart/form-data',
                  Authorization: `Bearer ${token}`
                },
                url: 'api/project03_stage_client',
                data: form_Data
              })
                .then(function (response) {
                  //handle success
                  //this.$forceUpdate();
                  _this.get_stage_client_amount(_this.stage_id);
                })
                .catch(function (response) {
                  //handle error
                  console.log(response)
                }).finally(function () {_this.amount_clear()});
            },
        
            get_stage_client_amount: function(stage_id) {
              let _this = this;
        
              if(stage_id == 0)
                return;
        
              const params = {
                      stage_id : stage_id,
                      type : 'amount',
                    };
        
                  let token = localStorage.getItem('accessToken');
            
                  axios
                      .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
                      .then(
                      (res) => {
                          _this.stage_client_amount = res.data;
                      },
                      (err) => {
                          alert(err.response);
                      },
                      )
                      .finally(() => {
                          
                      });
              },

              dialogshow1() {
                var me = document.getElementById('add_a1');
                var diag = document.getElementById('dialog_a1');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    //this.venue_clear();
                    this.sales_clear();
                    this.date_clear();
                    this.status_clear();
                    this.priority_clear();
                    this.amount_clear();
                    this.competitor_clear();
                    this.prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              dialogshow2() {
                var me = document.getElementById('add_a2');
                var diag = document.getElementById('dialog_a2');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    this.venue_clear();
                    //this.sales_clear();
                    this.date_clear();
                    this.status_clear();
                    this.priority_clear();
                    this.amount_clear();
                    this.competitor_clear();
                    this.prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              dialogshow3() {
                var me = document.getElementById('add_a3');
                var diag = document.getElementById('dialog_a3');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    this.venue_clear();
                    this.sales_clear();
                    //this.date_clear();
                    this.status_clear();
                    this.priority_clear();
                    this.amount_clear();
                    this.competitor_clear();
                    this.prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              dialogshow4() {
                var me = document.getElementById('add_a4');
                var diag = document.getElementById('dialog_a4');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    this.venue_clear();
                    this.sales_clear();
                    this.date_clear();
                    // this.status_clear();
                    this.priority_clear();
                    this.amount_clear();
                    this.competitor_clear();
                    this.prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              dialogshow5() {
                var me = document.getElementById('add_a5');
                var diag = document.getElementById('dialog_a5');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    this.venue_clear();
                    this.sales_clear();
                    this.date_clear();
                    this.status_clear();
                    // this.priority_clear();
                    this.amount_clear();
                    this.competitor_clear();
                    this.prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              dialogshow6() {
                var me = document.getElementById('add_a6');
                var diag = document.getElementById('dialog_a6');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    this.venue_clear();
                    this.sales_clear();
                    this.date_clear();
                    this.status_clear();
                    this.priority_clear();
                    //this.amount_clear();
                    this.competitor_clear();
                    this.prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              dialogshow7() {
                var me = document.getElementById('add_a7');
                var diag = document.getElementById('dialog_a7');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    this.venue_clear();
                    this.sales_clear();
                    this.date_clear();
                    this.status_clear();
                    this.priority_clear();
                    this.amount_clear();
                    //competitor_clear();
                    this.prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              dialogshow8() {
                var me = document.getElementById('add_a8');
                var diag = document.getElementById('dialog_a8');
      
                  if (me.classList.contains('focus')){
                      diag.classList.remove('show');
                      me.classList.remove('focus');
                      
                  } else {
                    this.venue_clear();
                    this.sales_clear();
                    this.date_clear();
                    this.status_clear();
                    this.priority_clear();
                    this.amount_clear();
                    this.competitor_clear();

                    //prof_clear();

                    diag.classList.add("show");
                    me.classList.add('focus');

                    this.clear_all_diag();
                  }
              },

              competitor_clear() {

                this.competitor = '';
          
                document.getElementById('dialog_a7').classList.remove("show");
                document.getElementById('add_a7').classList.remove("focus");
              },
          
              competitor_create() {
                let _this = this;

                if (this.competitor.trim() == '') {
                  Swal.fire({
                    text: 'Please enter Competitors!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                  })
                    
                    //$(window).scrollTop(0);
                    return;
                }
           
                _this.submit = true;
                var form_Data = new FormData();
          
                form_Data.append('stage_id', this.stage_id);
                form_Data.append('message', this.competitor.trim());
                form_Data.append('type', 'competitor');
          
                const token = sessionStorage.getItem('token');
          
                axios({
                  method: 'post',
                  headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                  },
                  url: 'api/project03_stage_client',
                  data: form_Data
                })
                  .then(function (response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.get_stage_client_competitor(_this.stage_id);
                  })
                  .catch(function (response) {
                    //handle error
                    console.log(response)
                  }).finally(function () {_this.competitor_clear()});
              },
          
              get_stage_client_competitor: function(stage_id) {
                let _this = this;
          
                if(stage_id == 0)
                  return;
          
                const params = {
                        stage_id : stage_id,
                        type : 'competitor',
                      };
          
                    let token = localStorage.getItem('accessToken');
              
                    axios
                        .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
                        .then(
                        (res) => {
                            _this.stage_client_competitor = res.data;
                        },
                        (err) => {
                            alert(err.response);
                        },
                        )
                        .finally(() => {
                            
                        });
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

                prof_clear() {

                  this.prof_remark = '';
                  this.prof_fileArray = [];
                  this.$refs.prof_file.value = '';

                  this.get_stage_client_infomation(this.stage_id);
                  this.prof_canSub = true;


                  
                  document.getElementById('dialog_a8').classList.remove("show");
                  document.getElementById('add_a8').classList.remove("focus");
              },


              comment_show(trackid) {

                

                var me = document.getElementById('btn'+trackid);
                 
                  if (me.classList.contains('diashow')){
                    
                      me.classList.remove('diashow');
                      
                  } else {
                    this.venue_clear();
                    this.sales_clear();
                    this.date_clear();
                    this.status_clear();
                    this.priority_clear();
                    this.amount_clear();
                    this.competitor_clear();
                    this.prof_clear();
                    this.clear_all_diag();

                    me.classList.add('diashow')
                  }
               },

              comment_clear(trackid) {

                
                  
                  document.getElementById('btn'+trackid).classList.remove("diashow");
                  this.$refs['comment' + trackid][0].value = "";
               
              },

              task_create(){

                let _this = this;


                  if (this.stage_task.trim() == '') {
                    Swal.fire({
                      text: 'Please enter Task Information!',
                      icon: 'warning',
                      confirmButtonText: 'OK'
                    })
                      
                      //$(window).scrollTop(0);
                      return;
                  }


                  _this.submit = true;
                  var form_Data = new FormData();

                  form_Data.append('stage_id', this.stage_id);
                  form_Data.append('project_id', this.project_id);
                  form_Data.append('message', this.stage_task.trim());
                  form_Data.append('type', 'task');

                  form_Data.append("attached_file", JSON.stringify(this.fileArray_r));

                  for(var j = 0; j < this.fileArray_r.length; j++) {
                    let file = this.fileArray_r[j];
                    if(typeof file !== 'undefined' && file !== null) 
                      form_Data.append('attached_file' + j, file);
                  }

                  const token = sessionStorage.getItem('token');

                  axios({
                          method: 'post',
                          headers: {
                              'Content-Type': 'multipart/form-data',
                              Authorization: `Bearer ${token}`
                          },
                          url: 'api/project03_stage_client_task',
                          data: form_Data
                      })
                      .then(function(response) {
                          //handle success
                      
                            _this.stage_task = "";
                            _this.fileArray_r = [];
                        
                            _this.get_stage_client_task(_this.stage_id);

                          
                      })
                      .catch(function(response) {
                          //handle error
                          console.log(response)
                      });

              },

              clear_all_diag() {
                for (i = 1; i <= this.tid; i++) {
                  var me = this.$refs['dialog' + i][0];
                  me.classList.remove('diashow');
                }
              },

              comment_create(task_id){

                let _this = this;
                var comment = this.$refs['comment' + task_id][0].value;


                  if (comment.trim() == '') {
                    Swal.fire({
                      text: 'Please enter comment!',
                      icon: 'warning',
                      confirmButtonText: 'OK'
                    })
                      
                      //$(window).scrollTop(0);
                      return;
                  }


                  _this.submit = true;
                  var form_Data = new FormData();

                  form_Data.append('task_id', task_id);
                  form_Data.append('project_id', this.project_id);
                  form_Data.append('stage_id', this.stage_id);
                  form_Data.append('message', comment.trim());
                  form_Data.append('type', 'comment');

                  if(this.arrMsg_r[task_id] !== undefined){
                    form_Data.append("attached_file", JSON.stringify(this.arrMsg_r[task_id]));

                  for(var j = 0; j < this.arrMsg_r[task_id].length; j++) {
                    let file = this.arrMsg_r[task_id][j];
                    if(typeof file !== 'undefined' && file !== null) 
                      form_Data.append('attached_file' + j, file);
                  }
                }
                else
                  form_Data.append("attached_file", []);

                  const token = sessionStorage.getItem('token');

                  axios({
                          method: 'post',
                          headers: {
                              'Content-Type': 'multipart/form-data',
                              Authorization: `Bearer ${token}`
                          },
                          url: 'api/project03_stage_client_task_comment',
                          data: form_Data
                      })
                      .then(function(response) {
                          //handle success
                      
                            _this.$refs['comment' + task_id][0].value = "";
                            _this.arrMsg_r[task_id] = [];
                            
                            _this.get_stage_client_task(_this.stage_id);
                            _this.clear_all_diag();

                          
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
                      text: 'Please enter Additional Information!',
                      icon: 'warning',
                      confirmButtonText: 'OK'
                    })
                      
                      //$(window).scrollTop(0);
                      return;
                  }


                  _this.submit = true;
                  var form_Data = new FormData();

                  form_Data.append('stage_id', this.stage_id);
                  form_Data.append('message', this.prof_remark.trim());
                  form_Data.append('type', 'additional');

                  const token = sessionStorage.getItem('token');

                  axios({
                          method: 'post',
                          headers: {
                              'Content-Type': 'multipart/form-data',
                              Authorization: `Bearer ${token}`
                          },
                          url: 'api/project03_stage_client',
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
                  myForm.append('batch_type', 'additional');
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

            get_stage_client_infomation: function(stage_id) {
              let _this = this;
        
              if(stage_id == 0)
                return;
        
              const params = {
                      stage_id : stage_id,
                      type : 'additional',
                    };
        
                  let token = localStorage.getItem('accessToken');
            
                  axios
                      .get('api/project03_stage_client_infomation', { params, headers: {"Authorization" : `Bearer ${token}`} })
                      .then(
                      (res) => {
                          _this.stage_client_infomation = res.data;
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

var app1 = new Vue({
  el: '#meeting',
  data: {
 
    meetings: [],
    users: [],

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      { name: '10', id: 10 },
      { name: '25', id: 25 },
      { name: '50', id: 50 },
      { name: '100', id: 100 },
      { name: 'All', id: 10000 }
    ],
    perPage: 5,

    baseURL: 'https://storage.googleapis.com/feliiximg/',


    // calendar
    attendee:[],
    old_attendee:[],
    add_id: 0,
  
    attachments:[],
  },

  created() {
 

    this.getUsers();
    //this.getMeetings();

  },

  computed: {
 
    },


  mounted() {


  },

  watch: {
   

  },



  methods: {
    getUsers() {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      axios
        .get('api/project02_user_online', { headers: { "Authorization": `Bearer ${token}` } })
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

    getMeetings: function(){ 
        this.action = 1;//select all
        var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;
        form_Data.append('jwt', token);
        form_Data.append('action', this.action);
        axios({
            method: 'post',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/work_calender_meetings',
            data: form_Data
        })
            .then(function (response) {
                //this.addDetails(response.data[0]);
                //handle success
                _this.meetings = response.data
                //console.log(_this.items)
                return response.data;
                
            })
            .catch(function (response) {
                //handle error
                Swal.fire({
                    text: JSON.stringify(response),
                    icon: 'error',
                    confirmButtonText: 'OK'
                })

                return [];
            });
            //this.upload();
          // this.reload();
        },

    warning(txt) {
        Swal.fire({
          text: JSON.stringify(txt),
          icon: 'error',
          confirmButtonText: 'OK'
      })
    },

    addMeetings:function(project_name, subject, message, attendee, start_time, end_time, username){
      this.action = 2;//add
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      var ret = 0;
      let _this = this;
              form_Data.append('jwt', token);
              form_Data.append('subject', subject);
              form_Data.append('project_name', project_name);
              form_Data.append('message', message);
              form_Data.append('attendee', attendee);
              form_Data.append('start_time', start_time);
              form_Data.append('end_time', end_time);
              form_Data.append('is_enabled', true);
              form_Data.append('action', this.action);
              form_Data.append('created_by', username);

              var file_elements = document.getElementsByName("file_elements");

              var item = 0;
              for(let i = 0;i < file_elements.length; i++)
              {
                  if(file_elements[i].checked)
                  {
                      for( var j = 0; j < this.attachments.length; j++ ){
                        let file = this.attachments[j];
                        if(file.name === file_elements[i].value)
                        {
                          form_Data.append('files[' + item++ + ']', file);
                          break;
                        }
                      }
                  }
                      
              }
              
              axios({
                  method: 'post',
                  headers: {
                      'Content-Type': 'multipart/form-data',
                  },
                  url: 'api/work_calender_meetings',
                  data: form_Data
              })
                  .then(function (response) {
                      //this.addDetails(response.data[0]);
                      //handle success
                      //_this.items = response.data
                      //console.log(_this.items)
                      ret = response.data[0];
                      _this.notify_mail(ret, 1);
                      return ret;
                      
                  })
                  .catch(function (response) {
                      //handle error
                      Swal.fire({
                          text: JSON.stringify(response),
                          icon: 'error',
                          confirmButtonText: 'OK'
                      })

                      return 0;
                  });
                  //this.upload();
                 // this.reload();
      },

  

  editMeetings:function(id, subject, message, attendee, start_time, end_time, username){
    this.action = 3;//update
    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    var ret = 0;
    let _this = this;
            form_Data.append('jwt', token);
            form_Data.append('id', id);
            form_Data.append('subject', subject);
            form_Data.append('message', message);
            form_Data.append('attendee', attendee);
            form_Data.append('start_time', start_time);
            form_Data.append('end_time', end_time);
            form_Data.append('is_enabled', true);
            form_Data.append('action', this.action);
            form_Data.append('updated_by', username);
            axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                url: 'api/work_calender_meetings',
                data: form_Data
            })
                .then(function (response) {
                    //this.addDetails(response.data[0]);
                    //handle success
                    //_this.items = response.data
                    //console.log(_this.items)
                    ret = response.data[0];

                    return ret;
                    
                })
                .catch(function (response) {
                    //handle error
                    Swal.fire({
                        text: JSON.stringify(response),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })

                    return 0;
                });
                //this.upload();
               // this.reload();
    },

    notify_mail(batch_id, type){
      var form_Data = new FormData();

      form_Data.append('bid', batch_id);
      form_Data.append('type', type);
      
      const token = sessionStorage.getItem('token');

      axios({
              method: 'post',
              headers: {
                  'Content-Type': 'multipart/form-data',
                  Authorization: `Bearer ${token}`
              },
              url: 'api/project_meeting_mail',
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

  delMeetings:function(id){
    this.action = 7;//add
    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    var ret = 0;
    let _this = this;
    let _id = id;
            form_Data.append('jwt', token);
            form_Data.append('id', id);
         
            form_Data.append('action', this.action);
            form_Data.append('deleted_by', username);
            axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                url: 'api/work_calender_meetings',
                data: form_Data
            })
                .then(function (response) {
                    //this.addDetails(response.data[0]);
                    //handle success
                    //_this.items = response.data
                    //console.log(_this.items)
                    ret = response.data[0];

                    _this.notify_mail(_id, 3);

                    return ret;
                    
                })
                .catch(function (response) {
                    //handle error
                    Swal.fire({
                        text: JSON.stringify(response),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })

                    return 0;
                });
                //this.upload();
               // this.reload();
    },

  },

});