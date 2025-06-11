Vue.component('v-select', VueSelect.VueSelect)

var filter = new Vue({
    el: '#filter', 
    data: {
        users:[],
        username: "",
        user_id : 0,
        my_id : 0,
        department: "",
        title: "",
        

    },created() {
    
        this.getUserName();
        
    },
    methods: {
        

        getUserName: function() {
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;

            form_Data.append("jwt", token);
    
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
                _this.username = response.data.username;
                _this.user_id = response.data.user_id;
                _this.my_id = response.data.user_id;
                _this.department = response.data.department;
                _this.title = response.data.title.trim();

                if(_this.username == "dereck" || _this.username == "Dennis Lin" || _this.username == "Kristel Tan" || _this.username == "Kuan")
                    _this.getAllMan();
                else if(_this.title == "Customer Value Director" || 
                        _this.title == "Lighting Value Creation Director" || 
                        _this.title == "Office Space Value Creation Director" || 
                        _this.title == "Brand Manager" || 
                        _this.title == "Service Manager" || 
                        _this.title == "Operations Manager" ||
                        _this.title == "Store Manager" ||
                        _this.title == "Engineering Manager" ||
                        _this.title == "Supply Chain Manager" )
                    _this.getLeadMan(_this.department);
                else
                    _this.users.push({id: _this.user_id, username: _this.username});

                _this.getInitial();
    
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

        getAllMan() {
  
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

          getLeadMan(department) {
    
              let _this = this;
              var form_Data = new FormData();
              let token = localStorage.getItem('accessToken');

            form_Data.append('dept', department);
        
            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    url: 'api/project02_user_lead_dep',
                    data: form_Data
                })
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


            async getInitial() {
                //刪除當前在日曆上的所有任務資訊
                calendar_task.removeAllEvents();

                //從資料庫中取出符合當前條件的任務

                let temp = [];
                //將符合條件的任務加入到日曆中
                // task status = Pending，則該任務顏色為 gray
                // task status = Close，則該任務顏色為 green
                // task status = Ongoing 且 開啟頁面的時間 <= 該任務的due date ，則該任務顏色為 blue
                // task status = Ongoing 且 開啟頁面的時間 > 該任務的due date ，則該任務顏色為 red
                var token = localStorage.getItem('token');

                localStorage.getItem('token');

                var form_Data0 = new FormData();
                form_Data0.append('uid', this.user_id);
                form_Data0.append('sdate', $('#sdate').val());
                form_Data0.append('edate', $('#edate').val());

                var form_Data1 = new FormData();
                form_Data1.append('jwt', token);
                form_Data1.append('action', 12);
                form_Data1.append('uid', this.user_id);
                form_Data1.append('sdate', $('#sdate').val());
                form_Data1.append('edate', $('#edate').val());

                var form_Data2 = new FormData();
                form_Data2.append('jwt', token);
                form_Data2.append('action', 12);
                form_Data2.append('uid', this.user_id);
                form_Data2.append('sdate', $('#sdate').val());
                form_Data2.append('edate', $('#edate').val());


                axios.all([
                    axios({
                        method: 'post',
                        url: 'api/project03_other_task_calendar_idv',
                        data: form_Data0,
                        headers: {
                          "Content-Type": "multipart/form-data",
                        },
                      }), 
                      axios({
                        method: 'post',
                        url: 'api/work_calender_meetings',
                        data: form_Data1,
                        headers: {
                          "Content-Type": "multipart/form-data",
                        },
                      }), 
                      axios({
                        method: 'post',
                        url: 'api/work_calender_notes',
                        data: form_Data2,
                        headers: {
                          "Content-Type": "multipart/form-data",
                        },
                      })
                  ])
                  .then(axios.spread((data1, data2, data3) => {
                    // output of req.
                    //console.log('data1', data1, 'data2', data2, 'data3', data3)
                    var obj = data1.data;

                  if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        //console.log(obj[i]);

                        var obj_meeting = {
                            id: 't' + obj[i].id,
                            title: obj[i].title,
                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                            backgroundColor: obj[i].color,
                            borderColor: obj[i].color,
                            extendedProps: {
                                create_id:obj[i].create_id,
                                category:obj[i].category,
                                level:obj[i].level,
                                task_status:obj[i].task_status,
                                stage_id:obj[i].stage_id,
                            },
                        };

                        temp.push(obj_meeting);
                    }

                    if(arrayLength > 0)
                    {
                        my_level = obj[0].my_l;
                        my_id = obj[0].my_i;
                        my_department = obj[0].my_d;
                    }
                }


                var obj = data2.data;

                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var title = "";
                        if(obj[i].project_name.trim() === '')
                            title = obj[i].subject.trim();
                        else
                            title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                        var attach = "";
                        for(var j = 0; j < obj[i].attach.length; j++)
                        {
                            attach += obj[i].attach[j].filename + ",";
                        }

                        if(attach !== "")
                            attach = attach.slice(0, -1);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            project_name: obj[i].project_name.trim(),
                            color: obj[i].color.trim(),
                            color_other: obj[i].color_other.trim(),
                            text_color: obj[i].text_color.trim(),
                            attendee: obj[i].attendee.trim(),
                            items: obj[i].items,
                            attach:attach,
                            location: obj[i].location.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: 'm' + obj[i].id,
                            title: title,
                            borderColor: obj[i].color.trim(),
                            backgroundColor: obj[i].color.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        temp.push(obj_meeting);
                    }
                }

                var obj = data3.data;

                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var title = "";
                        if(obj[i].project_name.trim() === '')
                            title = obj[i].subject.trim();
                        else
                            title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                        var attach = "";
                        for(var j = 0; j < obj[i].attach.length; j++)
                        {
                            attach += obj[i].attach[j].filename + ",";
                        }

                        if(attach !== "")
                            attach = attach.slice(0, -1);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            project_name: obj[i].project_name.trim(),
                            color: obj[i].color.trim(),
                            color_other: obj[i].color_other.trim(),
                            text_color: obj[i].text_color.trim(),
                            attendee: obj[i].attendee.trim(),
                            items: obj[i].items,
                            attach:attach,
                            location: obj[i].location.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: 'n' + obj[i].id,
                            title: title,
                            borderColor: obj[i].color.trim(),
                            backgroundColor: obj[i].color.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        temp.push(obj_meeting);
                    }
                }

                event_array_task = temp;

                calendar_task.addEventSource(temp);

                  }));
/*
                let res = await axios({
                    method: 'post',
                    url: 'api/project03_other_task_calendar_idv',
                    data: form_Data0,
                    headers: {
                      "Content-Type": "multipart/form-data",
                    },
                  });

                  var obj = res.data;

                  if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        //console.log(obj[i]);

                        var obj_meeting = {
                            id: 't' + obj[i].id,
                            title: obj[i].title,
                            //url: 'https://feliix.myvnc.com/project03_other?sid=' + obj[i].stage_id,
                            start: moment(obj[i].due_date).format('YYYY-MM-DD'),
                            backgroundColor: obj[i].color,
                            borderColor: obj[i].color,
                            extendedProps: {
                                create_id:obj[i].create_id,
                                category:obj[i].category,
                                level:obj[i].level,
                                task_status:obj[i].task_status,
                                stage_id:obj[i].stage_id,
                            },
                        };

                        temp.push(obj_meeting);
                    }

                    if(arrayLength > 0)
                    {
                        my_level = obj[0].my_l;
                        my_id = obj[0].my_i;
                        my_department = obj[0].my_d;
                    }
                }

                var form_Data1 = new FormData();

                form_Data1.append('jwt', token);
                form_Data1.append('action', 12);
                form_Data1.append('uid', this.user_id);
                form_Data1.append('sdate', $('#sdate').val());
                form_Data1.append('edate', $('#edate').val());

                let meet = await axios({
                    method: 'post',
                    url: 'api/work_calender_meetings',
                    data: form_Data1,
                    headers: {
                      "Content-Type": "multipart/form-data",
                    },
                  });

                  var obj = meet.data;

                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var title = "";
                        if(obj[i].project_name.trim() === '')
                            title = obj[i].subject.trim();
                        else
                            title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                        var attach = "";
                        for(var j = 0; j < obj[i].attach.length; j++)
                        {
                            attach += obj[i].attach[j].filename + ",";
                        }

                        if(attach !== "")
                            attach = attach.slice(0, -1);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            project_name: obj[i].project_name.trim(),
                            color: obj[i].color.trim(),
                            color_other: obj[i].color_other.trim(),
                            text_color: obj[i].text_color.trim(),
                            attendee: obj[i].attendee.trim(),
                            items: obj[i].items,
                            attach:attach,
                            location: obj[i].location.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: 'm' + obj[i].id,
                            title: title,
                            borderColor: obj[i].color.trim(),
                            backgroundColor: obj[i].color.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        temp.push(obj_meeting);
                    }
                }

                var form_Data2 = new FormData();

                form_Data2.append('jwt', token);
                form_Data2.append('action', 12);
                form_Data2.append('uid', this.user_id);
                form_Data2.append('sdate', $('#sdate').val());
                form_Data2.append('edate', $('#edate').val());

                let notes = await axios({
                    method: 'post',
                    url: 'api/work_calender_notes',
                    data: form_Data2,
                    headers: {
                      "Content-Type": "multipart/form-data",
                    },
                  });

                  var obj = notes.data;

                if (obj !== undefined) {
                    var arrayLength = obj.length;
                    for (var i = 0; i < arrayLength; i++) {
                        console.log(obj[i]);

                        var title = "";
                        if(obj[i].project_name.trim() === '')
                            title = obj[i].subject.trim();
                        else
                            title = '[ ' + obj[i].project_name.trim() + ' ] ' + obj[i].subject.trim();

                        var attach = "";
                        for(var j = 0; j < obj[i].attach.length; j++)
                        {
                            attach += obj[i].attach[j].filename + ",";
                        }

                        if(attach !== "")
                            attach = attach.slice(0, -1);

                        var obj_description = {
                            title: obj[i].subject.trim(),
                            project_name: obj[i].project_name.trim(),
                            color: obj[i].color.trim(),
                            color_other: obj[i].color_other.trim(),
                            text_color: obj[i].text_color.trim(),
                            attendee: obj[i].attendee.trim(),
                            items: obj[i].items,
                            attach:attach,
                            location: obj[i].location.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            content: obj[i].message.trim(),
                            creator: obj[i].created_by.trim(),
                        };

                        var obj_meeting = {
                            id: 'n' + obj[i].id,
                            title: title,
                            borderColor: obj[i].color.trim(),
                            backgroundColor: obj[i].color.trim(),
                            start: moment(obj[i].start_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].start_time).format('HH:mm'),
                            end: moment(obj[i].end_time).format('YYYY-MM-DD') + 'T' + moment(obj[i].end_time).format('HH:mm'),
                            description: obj_description,
                        };

                        temp.push(obj_meeting);
                    }
                }
*/
                

            
            },
        }
    });


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
    

      
var memo = new Vue({
    el: '#memo',
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

      access3 : false,

      orders: [],
    
    },
  
    created() {
   
  
      this.getUsers();
      //this.getMeetings();
      this.getAccess3();
      this.getOrders();
    },
  
    computed: {
   
      },
  
  
    mounted() {
  
  
    },
  
    watch: {
     
  
    },
  
  
  
    methods: {
        getAccess3: async function() {
            var token = localStorage.getItem('token');

            let res = await axios.get('api/access_control_kind_get', { headers: { "Authorization": `Bearer ${token}` }, params: { kind: 'access3' } });
            this.access3 = res.data.access3;
        },

        getOrders() {
  
            let _this = this;
      
            let token = localStorage.getItem('accessToken');
      
            axios
              .get('api/order_taiwan_to_approve', { headers: { "Authorization": `Bearer ${token}` } })
              .then(
                (res) => {
                  _this.orders = res.data;
                },
                (err) => {
                  alert(err.response);
                },
              )
              .finally(() => {
      
              });
          },

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
                    url: 'api/work_calender_notes',
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