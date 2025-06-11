Vue.component('v-select', VueSelect.VueSelect)

var app1 = new Vue({
    el: '#app',
    data: {
   
      meetings: [],
      users: [],
      users_org: [],
  
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
  
      project_name:'',
    
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
              _this.users_org = res.data;
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
  
        warning(txt) {
          Swal.fire({
            text: JSON.stringify(txt),
            icon: 'error',
            confirmButtonText: 'OK'
        })
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