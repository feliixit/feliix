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
    project03_other_task: [],

    users : [],
    users_del: [],

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

    priorities: [
        { priority: "No Priority", id: 1 },
        { priority: "Low", id: 2 },
        { priority: "Normal", id: 3 },
        { priority: "High", id: 4 },
        { priority: "Urgent", id: 5 },
      ],
  
      statuses: [
        { project_status: "Ongoing", id: 0 },
        { project_status: "Pending", id: 1 },
        { project_status: "Close", id: 2 },
      ],

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

    canSub: true,
    finish: false,

    // I&AM
    my_department: "",
    my_title: "",
    username: "",
    creator: "",

    // TASKS
    title: "",
    priority: 0,
    assignee: [],
    collaborator: [],
    due_date: "",
    due_time: "",
    detail: "",

    proof_id: 0,
    view_detail: false,

    fileArray: [],

    arrTask: [],
    taskCanSub: [],
    taskFinish: [],
    current_task_id: 0,

    arrMsg: [],
    msgCanSub: [],
    msgFinish: [],
    current_msg_item_id: "",

    // dup task
    task_id_to_dup: 0,
    task_id_to_del: 0,
    task_id_to_load: 0,

    editfileArray: [],

    // filter
    fil_priority: 0,
    fil_status: "",
    fil_due_date: "",
    opt_due_date: [],

    fil_creator: "",
    fil_keyword: "",

    // priorities: {},
    // statuses: {},
    creators: {},

    special: '',

    related_order: "",
    related_tab: "1",

    related_orders: [],

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
        switch (tmp[0]) {
            case "fp":
              _this.fil_priority = tmp[1];
              break;
            case "fs":
              _this.fil_status = tmp[1];
              break;
            case "fc":
              _this.fil_creator = decodeURI(tmp[1]);
              break;
            case "fk":
              _this.fil_keyword = decodeURI(tmp[1]);
              break;
            case "pg":
                _this.pg = tmp[1];
                break;
            case "page":
              _this.page = tmp[1];
              break;
            case "sid":
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
              break;
            case "size":
              _this.perPage = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
          
      });
    }

    _this.getUsers();
    _this.getCreators();
    _this.getUserName();
    _this.getProjectOtherTask(_this.stage_id);
    _this.getDueDate(_this.stage_id);
    _this.getUsersDeleted(_this.stage_id);

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
    msg_clear(item_id) {
      let org_item = item_id.split("_")[1];

      if (org_item == "0")
        this.$refs["task_reply_msg_" + item_id][0].value = "";
      else this.$refs["task_reply_msg_" + item_id][0].value = "";

      document
        .getElementById("task_reply_btn_" + item_id)
        .classList.remove("focus");
      document
        .getElementById("task_reply_dlg_" + item_id)
        .classList.remove("show");

        this.getProjectOtherTask(this.stage_id);

        var cnt = this.$refs['task_reply_msg_cnt_' + item_id][0];
        cnt.innerHTML = "0";
    },

    msg_upload(item_id, msg_id, batch_id) {
      this.current_msg_item_id = item_id;

      this.canSub = false;
      var myArr = this.arrMsg[item_id];
      var _this = this;

      if (myArr === undefined) {
        _this.getProjectOtherTask(_this.stage_id);
        _this.msg_clear(item_id);
        return;
      }
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
                _this.$set(_this.arrMsg[item_id], index, myArr[index]);
                Vue.set(_this.arrMsg, 0, "");
              } else {
                myArr[index].progress = 0.99;
                _this.$set(_this.arrMsg[item_id], index, myArr[index]);
                Vue.set(_this.arrMsg, 0, "");
              }
            }
          },
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append("batch_type", "other_task_msg_rep_c");
        myForm.append("batch_id", batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function(res) {
            if (res.data.code == 0) {
              myArr[index].progress = 1;
              _this.$set(_this.arrMsg[item_id], index, myArr[index]);
              console.log(_this.arrMsg[item_id], index);
              Vue.set(_this.arrMsg, "", "");
              _this.getProjectOtherTask(_this.stage_id);
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function(err) {
            console.log(err);
          });
      });

      this.msgCanSub[item_id] = true;
    },

    msg_create(item_id, msg_id) {
      this.current_msg_item_id = item_id;

      let org_item = item_id.split("_")[1];

      let _this = this;

      var comment = {};
      if (org_item == "0") comment = this.$refs["task_reply_msg_" + item_id][0];
      else comment = this.$refs["task_reply_msg_" + item_id][0];

      if (comment.value.trim() == "") {
        Swal.fire({
          text: "Please enter reply!",
          icon: "warning",
          confirmButtonText: "OK",
        });

        //$(window).scrollTop(0);
        return;
      }

      _this.submit = true;
      var form_Data = new FormData();

      if (org_item == "0") form_Data.append("msg_id", msg_id);
      else {
        form_Data.append("msg_id", org_item);
        form_Data.append("reply_id", msg_id);
      }

      form_Data.append("reply", comment.value.trim());

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_other_task_reply_c",
        data: form_Data,
      })
        .then(function(response) {
          if (response.data["batch_id"] != 0) {
            _this.msg_upload(item_id, msg_id, response.data["batch_id"]);
          } else {
            _this.msg_clear(item_id);
          }

          if (_this.arrMsg[item_id] === undefined) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.msg_clear(item_id);
          }
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        })
        .finally(function() {
          _this.msg_clear(item_id);
        });
    },

    deleteMsgFile(item_id, index) {
      this.current_msg_item_id = item_id;

      this.arrMsg[item_id].splice(index, 1);
      var fileTarget = this.$refs["file_msg_" + item_id][0];
      fileTarget.value = "";
      Vue.set(this.arrMsg, 0, "");
    },

    changeMsgFile(item_id) {
      this.current_msg_item_id = item_id;

      var arr = this.arrMsg[item_id];
      if (typeof arr === "undefined" || arr.length == 0)
        this.arrMsg[item_id] = [];

      var fileTarget = this.$refs["file_msg_" + item_id][0];

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.arrMsg[item_id].indexOf(fileTarget.files[i]) == -1 ||
          this.arrMsg[item_id].length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.arrMsg[item_id].push(fileItem);
          Vue.set(this.arrMsg, 0, "");
        } else {
          fileTarget.value = "";
        }
      }
    },

    openTaskMsgDlg(item_id) {
      this.current_msg_item_id = item_id;
      document
        .getElementById("task_reply_btn_" + item_id)
        .classList.add("focus");
      document
        .getElementById("task_reply_dlg_" + item_id)
        .classList.add("show");
    },

    closeTaskMsgDlg(item_id) {
      document
        .getElementById("task_reply_btn_" + item_id)
        .classList.remove("focus");
      document
        .getElementById("task_reply_dlg_" + item_id)
        .classList.remove("show");
    },

    reload_task(task_id) {
      let _this = this;
      const params = {
        sid: task_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/project03_other_task_c", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.receive_record = res.data[0];
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    msg_delete(message_id, item_id, mid, uid) {
      if (mid !== uid) return;

      let _this = this;
      Swal.fire({
        title: "Delete",
        text: "Are you sure to delete?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          _this.do_msg_delete(message_id, item_id); // <--- submit form programmatically
        } else {
          // swal("Cancelled", "Your imaginary file is safe :)", "error");
        }
      });
    },

    do_msg_delete(message_id, item_id) {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("message_id", message_id);
      form_Data.append("item_id", item_id);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/project03_delete_message_c",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          if (response.data["ret"] != 0) {
            _this.org_uid = _this.uid;

            Swal.fire({
              text: "Deleted",
              icon: "success",
              confirmButtonText: "OK",
            });

            _this.getProjectOtherTask(_this.stage_id);
          }
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

    deleteTaskFile(task_id, index) {
      this.current_task_id = task_id;

      this.arrTask[task_id].splice(index, 1);
      var fileTarget = this.$refs["file_task_" + task_id];
      fileTarget[0].value = "";
      Vue.set(this.arrTask, 0, "");
    },
    
    changeTaskFile(task_id) {
      this.current_task_id = task_id;

      var arr = this.arrTask[task_id];
      if (typeof arr === "undefined" || arr.length == 0)
        this.arrTask[task_id] = [];

      var fileTarget = this.$refs["file_task_" + task_id];

        fileTarget = fileTarget[0];

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.arrTask[task_id].indexOf(fileTarget.files[i]) == -1 ||
          this.arrTask[task_id].length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.arrTask[task_id].push(fileItem);
          Vue.set(this.arrTask, 0, "");
        } else {
          fileTarget.value = "";
        }
      }
    },
    CanAccess(creator_title) {
      var can_save = true;
/*
      var _creator_title = creator_title.trim().toUpperCase();

      if (
        this.my_title == "MANAGING DIRECTOR" ||
        this.my_title == "CHIEF ADVISOR"
      )
        can_save = true;

      if (this.my_title == "BRAND MANAGER") {
        if (
          _creator_title != "MANAGING DIRECTOR" &&
          _creator_title != "CHIEF ADVISOR"
        )
          can_save = true;
      }

      if (this.my_title == "ASSISTANT BRAND MANAGER") {
        if (
          _creator_title != "MANAGING DIRECTOR" &&
          _creator_title != "CHIEF ADVISOR" &&
          _creator_title != "BRAND MANAGER"
        )
          can_save = true;
      }
*/
      return can_save;
    },
    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },

    taskItems(task_id) {
      var arr = this.arrTask[task_id];
      return arr;
    },

    msgItems(item_id) {
      var arr = this.arrMsg[item_id];
      return arr;
    },

    deleteFile(index) {
      this.fileArray.splice(index, 1);
      var fileTarget = this.$refs.file;
      fileTarget.value = "";
    },

    deleteEditFileItems(index) {
      this.record.pre_items.splice(index, 1);
      this.$forceUpdate();

    },

    deleteEditFile(index) {
      this.editfileArray.splice(index, 1);
      var fileTarget = this.$refs.editfile;
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

    changeEditFile() {
      var fileTarget = this.$refs.editfile;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.editfileArray.indexOf(fileTarget.files[i]) == -1 ||
          this.editfileArray.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.editfileArray.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    count_message(task_id) {
      var comment = this.$refs['comment_task_' + task_id][0];

      var cnt = this.$refs['comment_task_cnt' + task_id][0];
      cnt.innerHTML = comment.value.replace(/[^\x00-\xff]/g,"xx").length;
    },

    clear_message(task_id) {
      var cnt = this.$refs['comment_task_cnt' + task_id][0];
      cnt.innerHTML = "0";
    },

    getDueDate(stage_id) {
        let _this = this;
  
        if (stage_id == 0) return;
  
        const params = {
          stage_id: stage_id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/project03_other_task_due_date_c", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(
            (res) => {
              _this.opt_due_date = res.data;
            },
            (err) => {
              alert(err.response);
            }
          )
          .finally(() => {});
      },

    async task_edit_create() {
        let _this = this;
  
        if (this.task_id_to_load == 0) {
          Swal.fire({
            text: "Please select a task to edit",
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          //$(window).scrollTop(0);
          return;
        }
  
        if (this.record.title.trim() == "") {
          Swal.fire({
            text: "Please enter title!",
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          //$(window).scrollTop(0);
          return;
        }
  
        if (this.record.priority == 0) {
          Swal.fire({
            text: "Please select priority!",
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          //$(window).scrollTop(0);
          return;
        }
  
        if(this.record.assignee.length < 1)
        {
          Swal.fire({
            text: "Please select assignee!",
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          //$(window).scrollTop(0);
          return;
        }
  
        if (this.record.due_date.trim() == "") {
          Swal.fire({
            text: "Please enter due date!",
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          //$(window).scrollTop(0);
          return;
        }
  
        if (
          this.record.due_date.trim() == "" &&
          this.record.due_time.trim() != ""
        ) {
          Swal.fire({
            text: "Please enter due date!",
            icon: "warning",
            confirmButtonText: "OK",
          });
  
          //$(window).scrollTop(0);
          return;
        }
  
        _this.submit = true;
        var form_Data = new FormData();
  
        form_Data.append("task_id", this.record.task_id);
        form_Data.append("title", this.record.title.trim());
        form_Data.append("priority", this.record.priority_id);
        form_Data.append("status", this.record.task_status);
        form_Data.append(
          "assignee",
          Array.prototype.map
            .call(this.record.assignee, function(item) {
              return item.id;
            })
            .join(",")
        );
        form_Data.append(
          "collaborator",
          Array.prototype.map
            .call(this.record.collaborator, function(item) {
              return item.id;
            })
            .join(",")
        );
        form_Data.append("due_date", this.record.due_date.trim());
        form_Data.append("due_time", this.record.due_time.trim());
        form_Data.append("detail", this.record.detail.trim());

        form_Data.append('related_order', this.record.related_order);
      form_Data.append('related_tab', this.record.related_tab);

      form_Data.append('pre_items', JSON.stringify(this.record.pre_items));
  
        const token = sessionStorage.getItem("token");
  
        let response = await axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${token}`,
          },
          url: "api/project03_other_task_edit_c",
          data: form_Data,
        });
         
            if (response.data["batch_id"] != 0) {
              await _this.task_edit_upload(response.data["batch_id"]);
            } else {
              _this.task_edit_clear();
            }
  
            Swal.fire({
              text: "Edited",
              icon: "success",
              confirmButtonText: "OK",
            });
  
              _this.getProjectOtherTask(_this.stage_id);
              _this.task_edit_clear();
            
          
      },

      async task_edit_upload(batch_id) {
        this.canSub = false;
        var myArr = this.editfileArray;
        var vm = this;
  
        for(i = 0; i < myArr.length; i++)
          {
          var data = myArr[i];
          var myForm = new FormData();
          myForm.append("batch_type", "other_task_c");
          myForm.append("batch_id", batch_id);
          myForm.append("file", data);

          try {
            let res = await axios({
              method: 'post',
              url: 'api/uploadFile_gcp',
              data: myForm,
              headers: {
                "Content-Type": "multipart/form-data",
              },
          });
            
          } catch (err) {
            console.log(err)
            alert('error')
          }
        }

        this.canSub = true;
      },

    getUsersDeleted(id) {
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        const params = {
          pid: id,
        };
  
        axios
          .get("api/project03_user_other_deleted_c", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(
            (res) => {
              _this.users_del = res.data;
            },
            (err) => {
              alert(err.response);
            }
          )
          .finally(() => {});
      },
    task_load() {
        if (this.task_id_to_load != 0) {
          this.record = {};
          this.record = this.shallowCopy(
            this.project03_other_task.find(
              (element) => element.task_id == this.task_id_to_load
            )
          );

          this.record.pre_items = JSON.parse(JSON.stringify(this.record.items));
  
          if (!this.CanAccess(this.record.creator_title) && this.record.creator != this.username) {
            Swal.fire({
              text:
                "It is not allowed to edit/delete the task which was created by user with higher position.",
              icon: "warning",
              confirmButtonText: "OK",
            });
  
            //$(window).scrollTop(0);
            this.record = {};
            return;
          }
  
          this.getUsersDeleted(this.task_id_to_load);
        }
      },

    getUserName: function() {
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/on_duty_get_myname",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            _this.username = response.data.username;
            _this.my_department = response.data.department.trim().toUpperCase();
            _this.my_title = response.data.title.trim().toUpperCase();
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

      
    task_del() {

        if (this.task_id_to_del != 0) {
            this.record = {};
            this.record = this.shallowCopy(
              this.project03_other_task.find(
                (element) => element.task_id == this.task_id_to_del
              )
            );
  
          if (!this.CanAccess(this.record.creator_title) && this.record.creator != this.username) {
            Swal.fire({
              text:
                "It is not allowed to edit/delete the task which was created by user with higher position.",
              icon: "warning",
              confirmButtonText: "OK",
            });
  
            //$(window).scrollTop(0);
            this.record = {};
            return;
          }
        }
  
        if (this.task_id_to_del != 0) {
          let _this = this;
          Swal.fire({
            title: "Delete",
            text: "Are you sure to delete?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
          }).then((result) => {
            if (result.value) {
              _this.do_task_delete(_this.task_id_to_del); // <--- submit form programmatically
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
          });
        }
      },

      do_task_duplicate(task_id_to_dup) {
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
        form_Data.append("task_id_to_dup", task_id_to_dup);
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/project03_other_task_dup_c",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            if (response.data["ret"] != 0) {
              _this.org_uid = _this.uid;
  
              Swal.fire({
                text: "Duplicated",
                icon: "success",
                confirmButtonText: "OK",
              });
  
              _this.getProjectOtherTask(_this.stage_id);
            }
          })
          .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: "error",
              confirmButtonText: "OK",
            });
  
            _this.getProjectOtherTask(_this.stage_id);
          });
  
        _this.task_clear();
      },
  
      do_task_delete(task_id_to_del) {
        var token = localStorage.getItem("token");
        var form_Data = new FormData();
        let _this = this;
  
        form_Data.append("jwt", token);
        form_Data.append("task_id_to_del", task_id_to_del);
  
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/project03_other_task_del_c",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            if (response.data["ret"] != 0) {
              _this.org_uid = _this.uid;
  
              Swal.fire({
                text: "Deleted",
                icon: "success",
                confirmButtonText: "OK",
              });
  
              _this.getProjectOtherTask(_this.stage_id);
            }
          })
          .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: "error",
              confirmButtonText: "OK",
            });
  
            _this.getProjectOtherTask(_this.stage_id);
          });
  
        _this.task_clear();
      },

    task_dup() {
        if (this.task_id_to_dup != 0) {
          let _this = this;
          Swal.fire({
            title: "Duplicate",
            text: "Are you sure to duplicate?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
          }).then((result) => {
            if (result.value) {
              _this.do_task_duplicate(_this.task_id_to_dup); // <--- submit form programmatically
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
          });
        }
      },

    task_clear() {
      console.log("task_clear");
        this.title= "";
        this.priority= 0;
        this.assignee= [];
        this.collaborator= [];
        this.due_date= "";
        this.due_time= "";
        this.detail= "";
        this.fileArray= [];

        this.record = {};
        this.task_id_to_load = 0;
        this.task_id_to_del = 0;
        this.task_id_to_dup = 0;
        document.getElementById("dialog_red_edit").classList.remove("show");
        document.getElementById("edit_red").classList.remove("focus");
  
        document.getElementById("dialog_a1").classList.remove("focus");
        document.getElementById("add_a1").classList.remove("show");
      },

      task_edit_clear() {
        this.record = {};
        this.task_id_to_load = 0;
        this.task_id_to_del = 0;
        this.task_id_to_dup = 0;
        document.getElementById("dialog_red_edit").classList.remove("show");
        document.getElementById("edit_red").classList.remove("focus");
  
        document.getElementById("dialog_a1").classList.remove("focus");
        document.getElementById("add_a1").classList.remove("show");
      },

    getCreators() {
        let _this = this;
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/admin/project_creators_c", {
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(
            (res) => {
              _this.creators = res.data;
            },
            (err) => {
              alert(err.response);
            }
          )
          .finally(() => {});
      },

    GetPage() {
        for(var _page = 1; ; _page++) {
          let from = _page * this.perPage - this.perPage;
          let to = _page * this.perPage;
          var result = this.project03_other_task.slice(from, to);
  
          if(result.length === 0)
            return 1;
    
          var found = false;
          for(var i = 0; i < result.length; i++) {
              if (result[i].task_id == this.proof_id) {
                  found = true;
                  break;
              }
          }
  
          if(found == true)
            return _page;
        }
      },

    getProjectOtherTask(id) {
        let _this = this;
  
        const params = {
          "fs": this.fil_status,
          "fp": this.fil_priority,
          "fc": this.fil_creator,
          "fk": this.fil_keyword,
          "sid" : this.stage_id,
        };
  
        let token = localStorage.getItem("accessToken");
  
        axios
          .get("api/project03_other_task_c", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
          .then(
            (res) => {
              _this.project03_other_task = res.data;
              if (_this.project03_other_task.length > 0 && id !== 0) {
                _this.proof_id = id;
                var _page = _this.GetPage();
  
                _this.page = _page;
           
                //_this.proof_id = _this.receive_records[0].id;
                //_this.detail();
              } else {
                if(_this.view_detail != true)
                  _this.proof_id = 0;
              }
  
            },
            (err) => {
              alert(err.response);
            }
          )
          .finally(() => {});
      },


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
              _this.special = res.data[0].special;

              _this.getRelatedOrders(_this.project_id);
            },
            (err) => {
              alert(err.response);
            },
          )
          .finally(() => {
  
          });
      },

      
    getRelatedOrders(id) {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      const params = {
        pid : id,
       
      };

      axios
        .get('api/project03_other_get_related_orders', { params, headers: { "Authorization": `Bearer ${token}` } })
        .then(
          (res) => {
            _this.related_orders = res.data;
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
                  _this.creator = res.data[0].username;
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

               count_reply(message_id, ref_id) {
                var comment = this.$refs['task_reply_msg_' + message_id + '_' + ref_id][0];
          
                var cnt = this.$refs['task_reply_msg_cnt_' + message_id + '_' + ref_id][0];
                cnt.innerHTML = comment.value.replace(/[^\x00-\xff]/g,"xx").length;
              },

              comment_clear(trackid) {

                this.arrTask[trackid] = "";
                  
                  //document.getElementById('btn'+trackid).classList.remove("diashow");
                  this.$refs['comment_task_' + trackid][0].value = "";

                  this.clear_message(trackid);
               
              },

              async task_create() {
                let _this = this;
          
                if (this.title.trim() == "") {
                  Swal.fire({
                    text: "Please enter title!",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
          
                  //$(window).scrollTop(0);
                  return;
                }
          
                if (this.priority == 0) {
                  Swal.fire({
                    text: "Please select priority!",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
          
                  //$(window).scrollTop(0);
                  return;
                }
          
                if(this.assignee.length < 1)
                {
                  Swal.fire({
                    text: "Please select assignee!",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
          
                  //$(window).scrollTop(0);
                  return;
                }
          
                if (this.due_date.trim() == "") {
                  Swal.fire({
                    text: "Please enter due date!",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
          
                  //$(window).scrollTop(0);
                  return;
                }
          
          
                if (this.due_date.trim() == "" && this.due_time.trim() != "") {
                  Swal.fire({
                    text: "Please enter due date!",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
          
                  //$(window).scrollTop(0);
                  return;
                }
          
                _this.submit = true;
                var form_Data = new FormData();
          
                form_Data.append("stage_id", this.stage_id);
                form_Data.append("title", this.title.trim());
                form_Data.append("priority", this.priority);
                form_Data.append(
                  "assignee",
                  Array.prototype.map
                    .call(this.assignee, function(item) {
                      return item.id;
                    })
                    .join(",")
                );
                form_Data.append(
                  "collaborator",
                  Array.prototype.map
                    .call(this.collaborator, function(item) {
                      return item.id;
                    })
                    .join(",")
                );
                form_Data.append("due_date", this.due_date.trim());
                form_Data.append("due_time", this.due_time.trim());
                form_Data.append("detail", this.detail.trim());

                form_Data.append('related_order', this.related_order);
                form_Data.append('related_tab', this.related_tab);
          
                const token = sessionStorage.getItem("token");
          
                let response = await axios({
                  method: "post",
                  headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${token}`,
                  },
                  url: "api/project03_other_task_c",
                  data: form_Data,
                });
                  
                  if (response.data["batch_id"] != 0) {
                    await _this.task_upload(response.data["batch_id"]);
                  } else {
                    _this.task_clear();
                  }
        
                  
                    _this.getProjectOtherTask(_this.stage_id);
                    _this.task_clear();
                  
                  
              },

              got_it_message(message_id, reply_id) {
                let _this = this;
          
                _this.submit = true;
                var form_Data = new FormData();
          
                // if reply_id != 0, it means it is a reply
                if (reply_id != 0) {
                  reply_id = message_id;
                  message_id = 0;
                }
          
                form_Data.append('message_id', message_id);
                form_Data.append('reply_id', reply_id);
                form_Data.append('kind', 'c');
          
                const token = sessionStorage.getItem('token');
          
                axios({
                  method: 'post',
                  headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                  },
                  url: 'api/project_got_it',
                  data: form_Data
                })
                  .then(function (response) {
                    _this.getProjectOtherTask(_this.stage_id);
                 
                  })
                  .catch(function (response) {
                    //handle error
                    console.log(response)
                  }).finally(function () {  });
              },

              async task_upload(batch_id) {
                console.log("task_upload");
                this.canSub = false;
                var myArr = this.fileArray;
                var vm = this;

                for (var i = 0; i < myArr.length; i++) {
                  var data = myArr[i];
                  var myForm = new FormData();
                  myForm.append("batch_type", "other_task_c");
                  myForm.append("batch_id", batch_id);
                  myForm.append("file", data);
          

                    try {
                      let res = await axios({
                        method: 'post',
                        url: 'api/uploadFile_gcp',
                        data: myForm,
                        headers: {
                          "Content-Type": "multipart/form-data",
                        },
                    });
                      
                    } catch (err) {
                      console.log(err)
                      alert('error')
                    }
                  }
          
          
                this.canSub = true;
              },

              clear_all_diag() {
                for (i = 1; i <= this.tid; i++) {
                  var me = document.getElementById("dialog_a" + i);
                  //var me = this.$refs['dialog_a' + i][0];
                  if(me != undefined)
                    me.classList.remove('diashow');
                }
              },

              async comment_create(task_id) {
                this.current_task_id = task_id;
          
                let _this = this;
          
                var comment = this.$refs["comment_task_" + task_id][0];
          
                if (comment.value.trim() == "") {
                  Swal.fire({
                    text: "Please enter comment!",
                    icon: "warning",
                    confirmButtonText: "OK",
                  });
          
                  //$(window).scrollTop(0);
                  return;
                }
          
                _this.submit = true;
                var form_Data = new FormData();
          
                form_Data.append("task_id", task_id);
                form_Data.append("message", comment.value.trim());
          
                const token = sessionStorage.getItem("token");
          
                let response = await axios({
                  method: "post",
                  headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${token}`,
                  },
                  url: "api/project_other_task_message_c",
                  data: form_Data,
                });

                    if (response.data["batch_id"] != 0) {
                      await _this.comment_upload(task_id, response.data["batch_id"]);
                    } else {
                      _this.comment_clear(task_id);
                    }
          
                  
                    _this.getProjectOtherTask(_this.stage_id);
                    _this.comment_clear(task_id);
                  
                 
                 
              },
          
              async comment_upload(task_id, batch_id) {
                this.current_task_id = task_id;
          
                this.canSub = false;
                var myArr = this.arrTask[task_id];
                var _this = this;
                
                if(myArr == undefined)
                  return;
          
                for (var i = 0; i < myArr.length; i++) {
                  
                  var data = myArr[i];
                  var myForm = new FormData();
                  myForm.append("batch_type", "other_task_msg_c");
                  myForm.append("batch_id", batch_id);
                  myForm.append("file", data);
          
                  try {
                    let res = await axios({
                      method: 'post',
                      url: 'api/uploadFile_gcp',
                      data: myForm,
                      headers: {
                        "Content-Type": "multipart/form-data",
                      },
                  });
                    
                  } catch (err) {
                    console.log(err)
                    alert('error')
                  }
                }
          
                this.taskCanSub[task_id] = true;
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