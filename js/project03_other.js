var app = new Vue({
  el: '#app',
  data: {
    stage_id: 0,
    receive_records: [],
    record: {},

    project03_other_task: {},
    project03_other_task_r: [],

    users: {},

    submit: false,
    submit_r:false,

    contactor: '',
    username: '',
    client_type: '',
    category: '',
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

    baseURL: 'https://storage.cloud.google.com/feliiximg/',


    // TASKS
    title: '',
    priority: 0,
    assignee: [],
    collaborator: [],
    due_date: '',
    detail: '',


    fileArray: [],

    arrTask: [],
    taskCanSub: [],
    taskFinish: [],
    current_task_id: 0,


    arrMsg: [],
    msgCanSub: [],
    msgFinish: [],
    current_msg_item_id: '',

    canSub: true,
    finish: false,

    // MESSAGE
    title_r: '',
    priority_r: 0,
    assignee_r: [],
    collaborator_r: [],
    due_date_r: '',
    detail_r: '',


    fileArray_r: [],

    arrTask_r: [],
    taskCanSub_r: [],
    taskFinish_r: [],
    current_task_id_r: 0,


    arrMsg_r: [],
    msgCanSub_r: [],
    msgFinish_r: [],
    current_msg_item_id_r: '',



    canSub_r: true,
    finish_r: false,

    // dup task
    task_id_to_dup:0,
    task_id_to_del:0,
    task_id_to_load:0,


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
        _this.getProjectOtherTask_r(_this.stage_id);

      });
    }

    _this.getUsers();

  },

  computed: {
    displayedStagePosts() {
      this.setPages();
      return this.paginate(this.project03_other_task_r);
    },



  },

  mounted() {


  },

  watch: {

    project03_other_task_r() {
      console.log('Vue watch receive_stage_records');
      this.setPages();
    },

    arrTask: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_task_id].find(function (currentValue, index) {
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

    arrTask_r: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_task_id_r].find(function (currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.arrTask_r[_this.current_task_id_r].length) {

          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish_r[_this.current_task_id_r] = true;
            _this.getProjectOtherTask_r(_this.stage_id);

          });
          this.comment_clear_r(_this.current_task_id_r);

        }
      },
      deep: true
    },

    arrMsg: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_msg_item_id].find(function (currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.arrMsg[_this.current_msg_item_id].length) {

          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish[_this.current_msg_item_id] = true;
            _this.getProjectOtherTask(_this.stage_id);

          });
          this.msg_clear(_this.current_msg_item_id);

        }
      },
      deep: true
    },

    arrMsg_r: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_msg_item_id_r].find(function (currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.arrMsg_r[_this.current_msg_item_id_r].length) {

          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish_r[_this.current_msg_item_id_r] = true;
            _this.getProjectOtherTask_r(_this.stage_id);

          });
          this.msg_clear_r(_this.current_msg_item_id_r);

        }
      },
      deep: true
    },

    fileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function (currentValue, index) {
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

    fileArray_r: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function (currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.fileArray_r.length) {

          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish_r = true;
            _this.getProjectOtherTask_r(_this.stage_id);

          });
          this.task_clear_r();

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
        } else {
          fileTarget.value = "";
        }
      }
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

    taskItems(task_id) {
      var arr = this.arrTask[task_id];
      return arr;
    },

    taskItems_r(task_id) {
      var arr = this.arrTask_r[task_id];
      return arr;
    },

    msgItems(item_id) {
      var arr = this.arrMsg[item_id];
      return arr;
    },

    msgItems_r(item_id) {
      var arr = this.arrMsg_r[item_id];
      return arr;
    },

    deleteTaskFile(task_id, index) {
      this.current_task_id = task_id;

      this.arrTask[task_id].splice(index, 1);
      var fileTarget = this.$refs['file_task_' + task_id][0];
      fileTarget.value = "";
      Vue.set(this.arrTask, 0, '');
    },

    deleteTaskFile_r(task_id, index) {
      this.current_task_id_r = task_id;

      this.arrTask_r[task_id].splice(index, 1);
      var fileTarget = this.$refs['file_task_r_' + task_id][0];
      fileTarget.value = "";
      Vue.set(this.arrTask_r, 0, '');
    },

    deleteMsgFile(item_id, index) {
      this.current_msg_item_id = item_id;

      this.arrMsg[item_id].splice(index, 1);
      var fileTarget = this.$refs['file_msg_' + item_id][0];
      fileTarget.value = "";
      Vue.set(this.arrMsg, 0, '');
    },

    deleteMsgFile_r(item_id, index) {
      this.current_msg_item_id_r = item_id;

      this.arrMsg_r[item_id].splice(index, 1);
      var fileTarget = this.$refs['file_msg_r_' + item_id][0];
      fileTarget.value = "";
      Vue.set(this.arrMsg_r, 0, '');
    },

    openTaskMsgDlg(item_id) {
      this.current_msg_item_id = item_id;
      document.getElementById('task_reply_btn_' + item_id).classList.add("focus");
      document.getElementById('task_reply_dlg_' + item_id).classList.add("show");
    },

    closeTaskMsgDlg(item_id) {
      document.getElementById('task_reply_btn_' + item_id).classList.remove("focus");
      document.getElementById('task_reply_dlg_' + item_id).classList.remove("show");
    },

    openTaskMsgDlg_r(item_id) {
      this.current_msg_item_id_r = item_id;
      document.getElementById('task_reply_btn_r_' + item_id).classList.add("focus");
      document.getElementById('task_reply_dlg_r_' + item_id).classList.add("show");
    },

    closeTaskMsgDlg_r(item_id) {
      document.getElementById('task_reply_btn_r_' + item_id).classList.remove("focus");
      document.getElementById('task_reply_dlg_r_' + item_id).classList.remove("show");
    },

    task_load () {
      if(this.task_id_to_load != 0)
      {
        this.record = {};
        this.record = this.shallowCopy(this.project03_other_task.find(element => element.task_id == this.task_id_to_load));
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

    changeMsgFile(item_id) {
      this.current_msg_item_id = item_id;

      var arr = this.arrMsg[item_id];
      if (typeof arr === 'undefined' || arr.length == 0)
        this.arrMsg[item_id] = [];

      var fileTarget = this.$refs['file_msg_' + item_id][0];

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.arrMsg[item_id].indexOf(fileTarget.files[i]) == -1 ||
          this.arrMsg[item_id].length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.arrMsg[item_id].push(fileItem);
          Vue.set(this.arrMsg, 0, '');
        } else {
          fileTarget.value = "";
        }
      }
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

    changeTaskFile(task_id) {
      this.current_task_id = task_id;

      var arr = this.arrTask[task_id];
      if (typeof arr === 'undefined' || arr.length == 0)
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
        } else {
          fileTarget.value = "";
        }
      }
    },

    changeTaskFile_r(task_id) {
      this.current_task_id_r = task_id;

      var arr = this.arrTask_r[task_id];
      if (typeof arr === 'undefined' || arr.length == 0)
        this.arrTask_r[task_id] = [];

      var fileTarget = this.$refs['file_task_r_' + task_id][0];

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.arrTask_r[task_id].indexOf(fileTarget.files[i]) == -1 ||
          this.arrTask_r[task_id].length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.arrTask_r[task_id].push(fileItem);
          Vue.set(this.arrTask_r, 0, '');
        } else {
          fileTarget.value = "";
        }
      }
    },

    setPages() {
      console.log('setPages');
      this.pages = [];
      let numberOfPages = Math.ceil(this.project03_other_task_r.length / this.perPage);

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
      return this.project03_other_task_r.slice(from, to);
    },

    getProjectOtherTask(stage_id) {
      let _this = this;

      if (stage_id == 0)
        return;

      const params = {
        stage_id: stage_id,

      };

      let token = localStorage.getItem('accessToken');

      axios
        .get('api/project03_other_task', { params, headers: { "Authorization": `Bearer ${token}` } })
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

    getProjectOtherTask_r(stage_id) {
      let _this = this;

      if (stage_id == 0)
        return;

      const params = {
        stage_id: stage_id,

      };

      let token = localStorage.getItem('accessToken');

      axios
        .get('api/project03_other_task_r', { params, headers: { "Authorization": `Bearer ${token}` } })
        .then(
          (res) => {
            _this.project03_other_task_r = res.data;
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
        .get('api/project02_user', { headers: { "Authorization": `Bearer ${token}` } })
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

    OpenAssignee_r() {
      document.getElementById("assignee_r").multiple = true;
    },

    OpenAssignee() {
      document.getElementById("assignee").multiple = true;
    },

    OpenCollaborator() {
      document.getElementById("collaborator").multiple = true;
    },

    task_dup() {
      if(this.task_id_to_dup != 0)
      {
        let _this = this;
        Swal.fire({
            title: "Duplicate",
            text: "Are you sure to duplicate?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
          }).then((result) => {
            if (result.value) {
              
                _this.do_task_duplicate(_this.task_id_to_dup); // <--- submit form programmatically
              
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
          });
      }
    },

    do_task_duplicate(task_id_to_dup) {
      var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;

        form_Data.append('jwt', token);
        form_Data.append('task_id_to_dup', task_id_to_dup);

        axios({
            method: 'post',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/project03_other_task_dup',
            data: form_Data
        })
        .then(function(response) {
            //handle success
            if(response.data['ret'] != 0)
            {
              _this.org_uid = _this.uid;
              
                Swal.fire({
                  text: "Duplicated",
                  icon: 'success',
                  confirmButtonText: 'OK'
                })

                _this.getProjectOtherTask(_this.stage_id);
            }
        })
        .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: 'error',
              confirmButtonText: 'OK'
            });

            _this.getProjectOtherTask(_this.stage_id);
        });

        _this.task_clear();
    },


    task_del() {
      if(this.task_id_to_del != 0)
      {
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
              
                _this.do_task_delete(_this.task_id_to_del); // <--- submit form programmatically
              
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
          });
      }
    },

    do_task_delete(task_id_to_del) {
      var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;

        form_Data.append('jwt', token);
        form_Data.append('task_id_to_del', task_id_to_del);

        axios({
            method: 'post',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/project03_other_task_del',
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

                _this.getProjectOtherTask(_this.stage_id);
            }
        })
        .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: 'error',
              confirmButtonText: 'OK'
            });

            _this.getProjectOtherTask(_this.stage_id);
        });

        _this.task_clear();
    },

    task_clear() {

      this.detail = "";

      document.getElementById('dialog_a1').classList.remove("focus");
      document.getElementById('add_a1').classList.remove("show");
    },

    task_clear_r() {

      this.title_r = "";
      this.assignee_r = [];
      this.detail_r = "";

      this.fileArray_r = [];

      document.getElementById('dialog_a1_r').classList.remove("focus");
      document.getElementById('add_a1_r').classList.remove("show");
    },

    msg_clear(item_id) {

      this.$refs['task_reply_msg_' + item_id][0].value = "";

      document.getElementById('task_reply_btn_' + item_id).classList.remove("focus");
      document.getElementById('task_reply_dlg_' + item_id).classList.remove("show");
    },

    msg_clear_r(item_id) {

      this.$refs['task_reply_msg_r_' + item_id][0].value = "";

      document.getElementById('task_reply_btn_r_' + item_id).classList.remove("focus");
      document.getElementById('task_reply_dlg_r_' + item_id).classList.remove("show");
    },

    msg_delete(message_id, item_id) {

      let _this = this;
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
            
              _this.do_msg_delete(message_id, item_id); // <--- submit form programmatically
            
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });
    },

    do_msg_delete(message_id, item_id) {
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

      form_Data.append('jwt', token);
      form_Data.append('message_id', message_id);
      form_Data.append('item_id', item_id);

      axios({
          method: 'post',
          headers: {
              'Content-Type': 'multipart/form-data',
          },
          url: 'api/project03_delete_message',
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

              _this.getProjectOtherTask(_this.stage_id);
          }

      })
      .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: 'error',
            confirmButtonText: 'OK'
          });

      });
    },

    msg_delete_r(message_id, item_id) {

      let _this = this;
    
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
            
              _this.do_msg_delete_r(message_id, item_id); // <--- submit form programmatically
            
          } else {
            // swal("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });
    
    },

    do_msg_delete_r(message_id, item_id) {
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

      form_Data.append('jwt', token);
      form_Data.append('message_id', message_id);
      form_Data.append('item_id', item_id);

      axios({
          method: 'post',
          headers: {
              'Content-Type': 'multipart/form-data',
          },
          url: 'api/project03_delete_message_r',
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

              _this.getProjectOtherTask_r(_this.stage_id);
          }

      })
      .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: 'error',
            confirmButtonText: 'OK'
          });

      });
    },

    comment_clear(task_id) {
      this.current_task_id = task_id;
      this.arrTask[task_id] = [];
      Vue.set(this.arrTask, 0, '');
      this.$refs['comment_task_' + task_id][0].value = "";
    },

    comment_clear_r(task_id) {
      this.current_task_id_r = task_id;
      this.arrTask_r[task_id] = [];
      Vue.set(this.arrTask_r, 0, '');
      this.$refs['comment_task_r_' + task_id][0].value = "";
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
          if (response.data['batch_id'] != 0) {
            _this.task_upload(response.data['batch_id']);
          }
          else {
            _this.task_clear();

          }

          if (_this.fileArray.length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.task_clear();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_clear() });
    },

    task_upload(batch_id) {

      this.canSub = false;
      var myArr = this.fileArray;
      var vm = this;

      //循环文件数组挨个上传
      myArr.forEach((element, index) => {
        var config = {
          headers: { "Content-Type": "multipart/form-data" },
          onUploadProgress: function (e) {

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
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              vm.$set(vm.fileArray, index, myArr[index]);
              console.log(vm.fileArray, index);
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.canSub = true;

    },


    task_create_r() {
      let _this = this;

      if (this.title_r.trim() == '') {
        Swal.fire({
          text: 'Please enter message!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('title', this.title_r.trim());
      form_Data.append('priority', '');
      form_Data.append('assignee', this.assignee_r);
      form_Data.append('collaborator', '');
      form_Data.append('due_date', '');
      form_Data.append('detail', this.detail_r.trim());

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task_r',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.task_upload_r(response.data['batch_id']);
          }
          else {
            _this.task_clear_r();

          }

          if (_this.fileArray.length == 0) {
            _this.getProjectOtherTask_r(_this.stage_id);
            _this.task_clear_r();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_clear_r() });
    },

    task_upload_r(batch_id) {

      this.canSub_r = false;
      var myArr = this.fileArray_r;
      var vm = this;

      //循环文件数组挨个上传
      myArr.forEach((element, index) => {
        var config = {
          headers: { "Content-Type": "multipart/form-data" },
          onUploadProgress: function (e) {

            if (e.lengthComputable) {
              var rate = e.loaded / e.total;
              console.log(index, e.loaded, e.total, rate);
              if (rate < 1) {

                myArr[index].progress = rate;
                vm.$set(vm.fileArray_r, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.fileArray_r, index, myArr[index]);
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_r');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              vm.$set(vm.fileArray_r, index, myArr[index]);
              console.log(vm.fileArray_r, index);
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.canSub_r = true;

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
          if (response.data['batch_id'] != 0) {
            _this.comment_upload(task_id, response.data['batch_id']);
          }
          else {
            _this.comment_clear(task_id);

          }

          if (_this.arrTask[task_id].length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.comment_clear(task_id);
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.comment_clear(task_id) });
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
          onUploadProgress: function (e) {

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
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              _this.$set(_this.arrTask[task_id], index, myArr[index]);
              console.log(_this.arrTask[task_id], index);
              Vue.set(_this.arrTask, 0, '');
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.taskCanSub[task_id] = true;

    },


    comment_create_r(task_id) {
      this.current_task_id_r = task_id;

      let _this = this;

      var comment = this.$refs['comment_task_r_' + task_id][0];

      if (comment.value.trim() == '') {
        Swal.fire({
          text: 'Please enter comment!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit_r = true;
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
        url: 'api/project_other_task_message_r',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.comment_upload_r(task_id, response.data['batch_id']);
          }
          else {
            _this.comment_clear_r(task_id);

          }

          if (_this.arrTask_r[task_id].length == 0) {
            _this.getProjectOtherTask_r(_this.stage_id);
            _this.comment_clear_r(task_id);
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.comment_clear_r(task_id) });
    },

    comment_upload_r(task_id, batch_id) {

      this.current_task_id_r = task_id;

      this.canSub_r = false;
      var myArr = this.arrTask_r[task_id];
      var _this = this;

      //循环文件数组挨个上传
      myArr.forEach((element, index) => {
        var config = {
          headers: { "Content-Type": "multipart/form-data" },
          onUploadProgress: function (e) {

            if (e.lengthComputable) {
              var rate = e.loaded / e.total;
              console.log(index, e.loaded, e.total, rate);
              if (rate < 1) {

                myArr[index].progress = rate;
                _this.$set(_this.arrTask_r[task_id], index, myArr[index]);
                Vue.set(_this.arrTask_r, 0, '');
              } else {
                myArr[index].progress = 0.99;
                _this.$set(_this.arrTask_r[task_id], index, myArr[index]);
                Vue.set(_this.arrTask_r, 0, '');
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_msg_r');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              _this.$set(_this.arrTask_r[task_id], index, myArr[index]);
              console.log(_this.arrTask_r[task_id], index);
              Vue.set(_this.arrTask_r, 0, '');
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.taskCanSub_r[task_id] = true;

    },

    msg_create(item_id, msg_id) {
      this.current_msg_item_id = item_id;

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
          if (response.data['batch_id'] != 0) {
            _this.msg_upload(item_id, msg_id, response.data['batch_id']);
          }
          else {
            _this.msg_clear(item_id);

          }

          if (_this.arrMsg[item_id].length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.msg_clear(item_id);
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.msg_clear(item_id) });
    },

    msg_upload(item_id, msg_id, batch_id) {

      this.current_msg_item_id = item_id;

      this.canSub = false;
      var myArr = this.arrMsg[item_id];
      var _this = this;

      //循环文件数组挨个上传
      myArr.forEach((element, index) => {
        var config = {
          headers: { "Content-Type": "multipart/form-data" },
          onUploadProgress: function (e) {

            if (e.lengthComputable) {
              var rate = e.loaded / e.total;
              console.log(index, e.loaded, e.total, rate);
              if (rate < 1) {

                myArr[index].progress = rate;
                _this.$set(_this.arrMsg[item_id], index, myArr[index]);
                Vue.set(_this.arrMsg, 0, '');
              } else {
                myArr[index].progress = 0.99;
                _this.$set(_this.arrMsg[item_id], index, myArr[index]);
                Vue.set(_this.arrMsg, 0, '');
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_msg_rep');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              _this.$set(_this.arrMsg[item_id], index, myArr[index]);
              console.log(_this.arrMsg[item_id], index);
              Vue.set(_this.arrMsg, '', '');
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.msgCanSub[item_id] = true;

    },

    msg_create_r(item_id, msg_id) {
      this.current_msg_item_id_r = item_id;

      let _this = this;

      var comment = this.$refs['task_reply_msg_r_' + item_id][0];

      if (comment.value.trim() == '') {
        Swal.fire({
          text: 'Please enter reply!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }

      _this.submit_r = true;
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
        url: 'api/project_other_task_reply_r',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.msg_upload_r(item_id, msg_id, response.data['batch_id']);
          }
          else {
            _this.msg_clear_r(item_id);

          }

          if (_this.arrMsg_r[item_id].length == 0) {
            _this.getProjectOtherTask_r(_this.stage_id);
            _this.msg_clear_r(item_id);
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.msg_clear_r(item_id) });
    },

    msg_upload_r(item_id, msg_id, batch_id) {

      this.current_msg_item_id_r = item_id;

      this.canSub_r = false;
      var myArr = this.arrMsg_r[item_id];
      var _this = this;

      //循环文件数组挨个上传
      myArr.forEach((element, index) => {
        var config = {
          headers: { "Content-Type": "multipart/form-data" },
          onUploadProgress: function (e) {

            if (e.lengthComputable) {
              var rate = e.loaded / e.total;
              console.log(index, e.loaded, e.total, rate);
              if (rate < 1) {

                myArr[index].progress = rate;
                _this.$set(_this.arrMsg_r[item_id], index, myArr[index]);
                Vue.set(_this.arrMsg_r, 0, '');
              } else {
                myArr[index].progress = 0.99;
                _this.$set(_this.arrMsg_r[item_id], index, myArr[index]);
                Vue.set(_this.arrMsg_r, 0, '');
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_msg_rep_r');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              _this.$set(_this.arrMsg_r[item_id], index, myArr[index]);
              console.log(_this.arrMsg_r[item_id], index);
              Vue.set(_this.arrMsg_r, '', '');
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.msgCanSub_r[item_id] = true;

    },

  },
});