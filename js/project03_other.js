var app = new Vue({
  el: '#app',
  data: {
    stage_id: 0,
    receive_records: [],
    record: {},

    project03_other_task: {},

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

    baseURL:'https://storage.cloud.google.com/feliiximg/',


    // TASKS
    title: '',
    priority: 0,
    assignee: [],
    collaborator: [],
    due_date: '',
    detail:'',


    fileArray: [],

    arrTask: [],
    taskCanSub: [],
    taskFinish:[],
    current_task_id:0,


    arrMsg: [],
    msgCanSub: [],
    msgFinish:[],
    current_msg_id:'',



    canSub: true,
    finish: false,


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
          _this.getProjectOtherTask(_this.stage_id);

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

    arrTask: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_task_id].find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.arrTask[_this.current_task_id].length) {
          
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish[_this.current_task_id] = true;
            _this.getProjectOtherTask(_this.stage_id);

          });
          this.comment_clear(_this.current_task_id);

        }
      },
      deep: true
    },

    arrMsg: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_msg_id].find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.arrMsg[_this.current_msg_id].length) {
          
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish[_this.current_msg_id] = true;
            _this.getProjectOtherTask(_this.stage_id);

          });
          this.comment_clear(_this.current_msg_id);

        }
      },
      deep: true
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
            _this.getProjectOtherTask(_this.stage_id);

          });
          this.task_clear();

        }
      },
      deep: true
    },

  },



  methods: {

    deleteFile(index) {
      this.fileArray.splice(index, 1);
      var fileTarget = this.$refs.file;
      fileTarget.value = "";
    },

    changeFile() {
      var fileTarget = this.$refs.file;

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

    taskItems(task_id) {
      var arr = this.arrTask[task_id];
      return arr;
    },

    msgItems(msg_id) {
      var arr = this.arrMsg[msg_id];
      return arr;
    },

    deleteTaskFile(task_id, index) {
      this.current_task_id = task_id;

      this.arrTask[task_id].splice(index, 1);
      var fileTarget = this.$refs['file_task_' + task_id][0];
      fileTarget.value = "";
      Vue.set(this.arrTask, 0, '');
    },

    deleteMsgFile(msg_id, index) {
      this.current_msg_id = msg_id;

      this.arrMsg[msg_id].splice(index, 1);
      var fileTarget = this.$refs['file_msg_' + msg_id][0];
      fileTarget.value = "";
      Vue.set(this.arrMsg, 0, '');
    },

    openTaskMsgDlg(msg_rep) {
      document.getElementById('task_reply_btn_' + msg_rep).classList.add("focus");
      document.getElementById('task_reply_dlg_' + msg_rep).classList.add("show");
    },

    closeTaskMsgDlg(msg_rep) {
      document.getElementById('task_reply_btn_' + msg_rep).classList.remove("focus");
      document.getElementById('task_reply_dlg_' + msg_rep).classList.remove("show");
    },

    changeMsgFile(msg_id) {
      this.current_msg_id = msg_id;

      var arr = this.arrMsg[msg_id];
      if(typeof arr === 'undefined' || arr.length == 0)
        this.arrMsg[msg_id] = [];

      var fileTarget = this.$refs['file_msg_' + msg_id][0];

        for (i = 0; i < fileTarget.files.length; i++) {
            // remove duplicate
            if (
              this.arrMsg[msg_id].indexOf(fileTarget.files[i]) == -1 ||
              this.arrMsg[msg_id].length == 0
            ) {
              var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
              this.arrMsg[msg_id].push(fileItem);
              Vue.set(this.arrMsg, 0, '');
            }else{
              fileTarget.value = "";
            }
          }
    },

    changeTaskFile(task_id) {
      this.current_task_id = task_id;

      var arr = this.arrTask[task_id];
      if(typeof arr === 'undefined' || arr.length == 0)
        this.arrTask[task_id] = [];

      var fileTarget = this.$refs['file_task_' + task_id][0];

        for (i = 0; i < fileTarget.files.length; i++) {
            // remove duplicate
            if (
              this.arrTask[task_id].indexOf(fileTarget.files[i]) == -1 ||
              this.arrTask[task_id].length == 0
            ) {
              var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
              this.arrTask[task_id].push(fileItem);
              Vue.set(this.arrTask, 0, '');
            }else{
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

    getProjectOtherTask(stage_id) {
      let _this = this;

      if(stage_id == 0)
        return;

      const params = {
              stage_id : stage_id,
           
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_other_task', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.project03_other_task = res.data;
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


    OpenAssignee() {
      document.getElementById("assignee").multiple = true;
    },

    OpenCollaborator() {
      document.getElementById("collaborator").multiple = true;
    },

    task_clear() {

      this.detail = "";

      document.getElementById('dialog_a1').classList.remove("focus");
      document.getElementById('add_a1').classList.remove("show");
    },

    msg_clear(msg_rep) {
      
      this.$refs['task_reply_msg_' + msg_rep][0].value = "";

      document.getElementById('task_reply_btn_' + msg_rep).classList.remove("focus");
      document.getElementById('task_reply_dlg_' + msg_rep).classList.remove("show");
    },

    comment_clear(task_id) {
      this.current_task_id = task_id;
      this.arrTask[task_id] = [];
      //Vue.set(this.arrTask, 0, '');
      this.$refs['comment_task_' + task_id][0].value = "";
    },

    task_create() {
      let _this = this;

      if (this.title.trim() == '') {
        Swal.fire({
          text: 'Please enter title!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('title', this.title.trim());
      form_Data.append('priority', this.priority);
      form_Data.append('assignee', this.assignee);
      form_Data.append('collaborator', this.collaborator);
      form_Data.append('due_date', this.due_date.trim());
      form_Data.append('detail', this.detail.trim());

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task',
        data: form_Data
      })
        .then(function (response) {
          if(response.data['batch_id'] != 0)
          {
              _this.task_upload(response.data['batch_id']);
          }
          else
          {
            _this.task_clear();
        
          }

          if(_this.fileArray.length == 0)
          {
            _this.getProjectOtherTask(_this.stage_id);
            _this.task_clear();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.task_clear()});
    },

    task_upload(batch_id) {
    
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
          myForm.append('batch_type', 'other_task');
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


    comment_create(task_id) {
      this.current_task_id = task_id;

      let _this = this;

      var comment = this.$refs['comment_task_' + task_id][0];

      if (comment.value.trim() == '') {
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
      form_Data.append('message', comment.value.trim());


      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project_other_task_message',
        data: form_Data
      })
        .then(function (response) {
          if(response.data['batch_id'] != 0)
          {
              _this.comment_upload(task_id, response.data['batch_id']);
          }
          else
          {
            _this.comment_clear(task_id);
        
          }

          if(_this.arrTask[task_id].length == 0)
          {
            _this.getProjectOtherTask(_this.stage_id);
            _this.comment_clear(task_id);
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.comment_clear(task_id)});
    },

    comment_upload(task_id, batch_id) {

      this.current_task_id = task_id;
    
        this.canSub = false;
        var myArr = this.arrTask[task_id];
        var _this = this;
       
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
                  _this.$set(_this.arrTask[task_id], index, myArr[index]);
                  Vue.set(_this.arrTask, 0, '');
                } else {
                  myArr[index].progress = 0.99;
                  _this.$set(_this.arrTask[task_id], index, myArr[index]);
                  Vue.set(_this.arrTask, 0, '');
                }
              }
            }
          };
          var data = myArr[index];
          var myForm = new FormData();
          myForm.append('batch_type', 'other_task_msg');
          myForm.append('batch_id', batch_id);
          myForm.append("file", data);
 
          axios
            .post("api/uploadFile_gcp", myForm, config)
            .then(function(res) {
              if (res.data.code == 0) {
           
                myArr[index].progress = 1;
                _this.$set(_this.arrTask[task_id], index, myArr[index]);
                console.log(_this.arrTask[task_id], index);
                Vue.set(_this.arrTask, 0, '');
              } else {
                alert(JSON.stringify(res.data));
              }
            })
            .catch(function(err) {
              console.log(err);
            });
        });

        this.taskCanSub[task_id] = true;
      
    },
  
    msg_create(item_id, msg_id) {
      this.current_msg_id = msg_id;

      let _this = this;

      var comment = this.$refs['task_reply_msg_' + item_id][0];

      if (comment.value.trim() == '') {
        Swal.fire({
          text: 'Please enter reply!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }

      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('msg_id', msg_id);
      form_Data.append('reply', comment.value.trim());


      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project_other_task_reply',
        data: form_Data
      })
        .then(function (response) {
          if(response.data['batch_id'] != 0)
          {
              _this.msg_upload(msg_id, response.data['batch_id']);
          }
          else
          {
            _this.msg_clear(msg_id);
        
          }

          if(_this.arrMsg[msg_id].length == 0)
          {
            _this.getProjectOtherTask(_this.stage_id);
            _this.msg_clear(msg_id);
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.msg_clear(msg_id)});
    },

    msg_upload(msg_id, batch_id) {

      this.current_msg_id = msg_id;
    
        this.canSub = false;
        var myArr = this.arrMsg[msg_id];
        var _this = this;
      
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
                  _this.$set(_this.arrMsg[msg_id], index, myArr[index]);
                  Vue.set(_this.arrMsg, 0, '');
                } else {
                  myArr[index].progress = 0.99;
                  _this.$set(_this.arrMsg[msg_id], index, myArr[index]);
                  Vue.set(_this.arrMsg, 0, '');
                }
              }
            }
          };
          var data = myArr[index];
          var myForm = new FormData();
          myForm.append('batch_type', 'other_task_reply');
          myForm.append('batch_id', batch_id);
          myForm.append("file", data);

          axios
            .post("api/uploadFile_gcp", myForm, config)
            .then(function(res) {
              if (res.data.code == 0) {
          
                myArr[index].progress = 1;
                _this.$set(_this.arrMsg[msg_id], index, myArr[index]);
                console.log(_this.arrMsg[msg_id], index);
                Vue.set(_this.arrMsg, 0, '');
              } else {
                alert(JSON.stringify(res.data));
              }
            })
            .catch(function(err) {
              console.log(err);
            });
        });

        this.msgCanSub[msg_id] = true;
      
    },

  },
});