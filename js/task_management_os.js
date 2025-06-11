Vue.component("v-select", VueSelect.VueSelect);

var app = new Vue({
  el: "#app",
  data: {
    stage_id: 0,

    record: {},
    receive_record: {},

    project03_other_task: [],

    users: [],
    users_del: [],

    submit: false,

    contactor: "",

    client_type: "",
    category: "",
    // paging
    page: 1,
    pg: 0,
    //perPage: 10,
    pages: [],
    pages_10: [],

    inventory: [
      { name: "10", id: 10 },
      { name: "25", id: 25 },
      { name: "50", id: 50 },
      { name: "100", id: 100 },
      { name: "All", id: 10000 },
    ],

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

    perPage: 10,

    baseURL: "https://storage.googleapis.com/feliiximg/",

    // I&AM
    my_department: "",
    my_title: "",
    username: "",

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

    canSub: true,
    finish: false,

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
    fil_type: "",

    // priorities: {},
    // statuses: {},
    creators: {},

    // calendar
    attendee: [],

    // project_name
    project_id: 0,
    project_name: "",

    // order task list
    order: '',
    order_type: '',
    order_category: '',

    canSub_i: true,
    finish_i: false,

    canSub_o: true,
    finish_o: false,

    fileArray_o: [],
    editfileArray_o: [],

    fileArray_i: [],
    editfileArray_i: [],

  },

  created() {
    let _this = this;
    let uri = window.location.href.split("?");

    if (uri.length > 1) {
      let vars = uri[1].split("&");
      let getVars = {};
      let tmp = "";
      let _pid = 0;

      vars.forEach( function(v) {
        tmp = v.split("=");
        if (tmp.length == 2) {
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
            case "ft":
                _this.fil_type = tmp[1];
                break;
            case "pg":
                _this.pg = tmp[1];
                break;
            case "page":
              _this.page = tmp[1];
              break;
            case "sid":
              _pid = tmp[1];
              break;
            case "size":
              _this.perPage = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
        }
        
      });

      _this.stage_id = 0;
      _this.getProjectOtherTask(_pid);
      _this.getDueDate(_this.stage_id);
      _this.getUsersDeleted(_this.stage_id);
    } else {
      _this.getProjectOtherTask(0);
      _this.getDueDate(0);
      _this.getUsersDeleted(0);
    }

    _this.getUsers();
    //_this.getPrioritys();
    //_this.getStatuses();
    _this.getCreators();
    _this.getUserName();
  },

  computed: {
    displayedStagePosts() {

      this.setPages();
      return this.paginate(this.project03_other_task);
    },
  },

  mounted() {},

  watch: {
    proof_id() {
      this.detail_a();
    },

    arrTask: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_task_id].find(function(
          currentValue,
          index
        ) {
          return currentValue.progress != 1;
        });
        if (
          finish === undefined &&
          this.arrTask[_this.current_task_id].length
        ) {
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish[_this.current_task_id] = true;
            _this.getProjectOtherTask(0);
          });
          this.comment_clear(_this.current_task_id);
        }
      },
      deep: true,
    },

    arrMsg: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue[_this.current_msg_item_id].find(function(
          currentValue,
          index
        ) {
          return currentValue.progress != 1;
        });
        if (
          finish === undefined &&
          this.arrMsg[_this.current_msg_item_id].length
        ) {
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish[_this.current_msg_item_id] = true;
            _this.getProjectOtherTask(0);
          });
          this.msg_clear(_this.current_msg_item_id);
        }
      },
      deep: true,
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
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish = true;
            _this.getProjectOtherTask(0);
          });
          this.task_clear();
        }
      },
      deep: true,
    },

    editfileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.editfileArray.length) {
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish = true;
            _this.getProjectOtherTask(0);
          });
          this.task_edit_clear();
        }
      },
      deep: true,
    },


    fileArray_o: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.fileArray_o.length) {
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish = true;
            _this.getProjectOtherTask(0);
          });
          this.task_clear_o();
        }
      },
      deep: true,
    },

    editfileArray_o: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.editfileArray_o.length) {
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish = true;
            _this.getProjectOtherTask(0);
          });
          this.task_edit_clear_o();
        }
      },
      deep: true,
    },

    fileArray_i: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.fileArray_i.length) {
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish = true;
            _this.getProjectOtherTask(0);
          });
          this.task_clear_i();
        }
      },
      deep: true,
    },

    editfileArray_i: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.editfileArray_i.length) {
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon",
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.finish = true;
            _this.getProjectOtherTask(0);
          });
          this.task_edit_clear_i();
        }
      },
      deep: true,
    },
  },

  methods: {

    changeEditFile_o() {
      var fileTarget = this.$refs.editfile_o;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.editfileArray_o.indexOf(fileTarget.files[i]) == -1 ||
          this.editfileArray_o.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.editfileArray_o.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    deleteEditFile_o(index) {
      this.editfileArray_o.splice(index, 1);
      var fileTarget = this.$refs.editfile_o;
      fileTarget.value = "";
    },

    deleteFile_o(index) {
      this.fileArray_o.splice(index, 1);
      var fileTarget = this.$refs.file_o;
      fileTarget.value = "";
    },

    changeFile_o() {
      var fileTarget = this.$refs.file_o;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.fileArray_o.indexOf(fileTarget.files[i]) == -1 ||
          this.fileArray_o.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.fileArray_o.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    changeEditFile_i() {
      var fileTarget = this.$refs.editfile_i;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.editfileArray_i.indexOf(fileTarget.files[i]) == -1 ||
          this.editfileArray_i.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.editfileArray_i.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    deleteEditFile_i(index) {
      this.editfileArray_i.splice(index, 1);
      var fileTarget = this.$refs.editfile_i;
      fileTarget.value = "";
    },

    deleteFile_i(index) {
      this.fileArray_i.splice(index, 1);
      var fileTarget = this.$refs.file_i;
      fileTarget.value = "";
    },

    changeFile_i() {
      var fileTarget = this.$refs.file_i;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.fileArray_i.indexOf(fileTarget.files[i]) == -1 ||
          this.fileArray_i.length == 0
        ) {
          var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
          this.fileArray_i.push(fileItem);
        } else {
          fileTarget.value = "";
        }
      }
    },

    all_clear() {
      this.title = "";
      this.detail = "";
      this.order = "";
      this.order_type = "";

      this.priority = 0;

      this.assignee = [];
      this.collaborator = [];

      this.related_order = "";
      this.related_tab = "1";

      this.related_inquirys = [];
      this.related_orders = [];

      this.due_date = "";
      this.due_time = "";
      this.detail = "";

      this.fileArray = [];
      this.fileArray_i = [];
      this.fileArray_o = [];

      this.record = [];
      this.editfileArray = [];
      this.editfileArray_i = [];
      this.editfileArray_o = [];

      this.order_category = "";
      this.task_id_to_load = 0;

      console.log("all clear");
    },

    task_clear_o() {

      this.all_clear()

      document.getElementById('dialog_a1_o').classList.remove("focus");
      document.getElementById('add_a1_o').classList.remove("show");
    },

    task_clear_i() {

      this.all_clear()

      document.getElementById('dialog_a1_i').classList.remove("focus");
      document.getElementById('add_a1_i').classList.remove("show");
    },


    task_edit_clear_o() {

      this.all_clear()

      document.getElementById('dialog_red_edit_o').classList.remove("show");
      document.getElementById('edit_red_o').classList.remove("focus");
    },

    task_edit_clear_i() {

      this.all_clear()

      document.getElementById('dialog_red_edit_i').classList.remove("show");
      document.getElementById('edit_red_i').classList.remove("focus");
    },

    task_upload_i(batch_id) {

      this.canSub_i = false;
      var myArr = this.fileArray_i;
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
                vm.$set(vm.fileArray_i, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.fileArray_i, index, myArr[index]);
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_o');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              vm.$set(vm.fileArray_i, index, myArr[index]);
              console.log(vm.fileArray_i, index);
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.canSub_i = true;

    },


    task_upload_o(batch_id) {

      this.canSub_o = false;
      var myArr = this.fileArray_o;
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
                vm.$set(vm.fileArray_o, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.fileArray_o, index, myArr[index]);
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_o');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              vm.$set(vm.fileArray_o, index, myArr[index]);
              console.log(vm.fileArray_o, index);
            } else {
              alert(JSON.stringify(res.data));
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.canSub_o = true;

    },

    task_edit_upload_o(batch_id) {

      this.canSub = false;
      var myArr = this.editfileArray_o;
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
                vm.$set(vm.editfileArray_o, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.editfileArray_o, index, myArr[index]);
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_o');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              vm.$set(vm.editfileArray_o, index, myArr[index]);
              console.log(vm.editfileArray_o, index);
            } else {
              alert(JSON.stringify(res.data));
              _this.getProjectOtherTask(_this.stage_id);
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.canSub = true;

    },

    task_edit_upload_i(batch_id) {

      this.canSub = false;
      var myArr = this.editfileArray_i;
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
                vm.$set(vm.editfileArray_i, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.editfileArray_i, index, myArr[index]);
              }
            }
          }
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append('batch_type', 'other_task_o');
        myForm.append('batch_id', batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function (res) {
            if (res.data.code == 0) {

              myArr[index].progress = 1;
              vm.$set(vm.editfileArray_i, index, myArr[index]);
              console.log(vm.editfileArray_i, index);
            } else {
              alert(JSON.stringify(res.data));
              _this.getProjectOtherTask(_this.stage_id);
            }
          })
          .catch(function (err) {
            console.log(err);
          });
      });

      this.canSub = true;

    },



    deleteEditFileItems(index) {
      this.record.pre_items.splice(index, 1);
      this.$forceUpdate();

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

    CanAccess(creator_title) {
      var can_save = false;

      var _creator_title = creator_title.trim().toUpperCase();

      if (
        this.my_title == "MANAGING DIRECTOR" ||
        this.my_title == "CHIEF ADVISOR" || 
        this.my_title == "SUPPLY CHAIN MANAGER"
      )
        can_save = true;

      if (this.my_title == "OFFICE SPACE VALUE CREATION DIRECTOR") {
        if (
          _creator_title != "MANAGING DIRECTOR" &&
          _creator_title != "CHIEF ADVISOR"
        )
          can_save = true;
      }

      if (this.my_title == "ASSISTANT OFFICE SPACE VALUE CREATION DIRECTOR") {
        if (
          _creator_title != "MANAGING DIRECTOR" &&
          _creator_title != "CHIEF ADVISOR" &&
          _creator_title != "OFFICE SPACE VALUE CREATION DIRECTOR"
        )
          can_save = true;
      }

      if(this.username == "dereck")
        can_save = true;

      return can_save;
    },

    filter_remove: function() {
      this.fil_priority = "";
      this.fil_status = "";
      this.fil_creator = "";
      this.fil_keyword = "";
      this.fil_type = "";

      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");

      //this.receive_records = [];

      //this.getRecords();
      this.filter_apply();
    },

    getCreators() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/admin/project_creators_o", {
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

    getPrioritys() {
      this.priorities = this._priority;
    },

    getStatuses() {
      this.statuses = this._status;
    },

    hide_detail: function() {
      this.proof_id = 0;

      this.view_detail = false;
      this.$refs.mask.style.display = "none";
    },

    detail_a: function() {
      let _this = this;

      if (this.proof_id == 0) {
        this.view_detail = false;
        this.$refs.mask.style.display = "none";
        return;
      }

      this.receive_record = this.shallowCopy(
        this.project03_other_task.find(
          (element) => element.task_id == this.proof_id
        )
      );

      this.$refs.mask.style.display = "block";
      this.view_detail = true;
    },

    filter_apply: function(pg) {
      let _this = this;

      if (_this.page < 1) _this.page = 1;
      if (_this.page > _this.pages.length) _this.page = _this.pages.length;

      if(pg != undefined) this.page = pg;

      var uri = "task_management_OS?" +
      "fp=" +
      _this.fil_priority +
      "&fk=" +
      _this.fil_keyword +
      "&ft=" +
      _this.fil_type +
      "&fs=" +
      _this.fil_status +
      "&fc=" +
      _this.fil_creator +
      "&pg=" +
      _this.pg +
      "&page=" +
      _this.page +
      "&size=" +
      _this.perPage;

      window.location.href = encodeURI(uri);
    },

    deleteFile(index) {
      this.fileArray.splice(index, 1);
      var fileTarget = this.$refs.file;
      fileTarget.value = "";
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

    show_detail: function(id) {
      this.proof_id = id;

      this.detail_a();
    },

    taskItems(task_id) {
      var arr = this.arrTask[task_id];
      return arr;
    },

    msgItems(item_id) {
      var arr = this.arrMsg[item_id];
      return arr;
    },

    deleteTaskFile(task_id, index) {
      this.current_task_id = task_id;

      this.arrTask[task_id].splice(index, 1);
      var fileTarget = this.$refs["file_task_" + task_id];
      fileTarget.value = "";
      Vue.set(this.arrTask, 0, "");
    },

    deleteMsgFile(item_id, index) {
      this.current_msg_item_id = item_id;

      this.arrMsg[item_id].splice(index, 1);
      var fileTarget = this.$refs["file_msg_" + item_id][0];
      fileTarget.value = "";
      Vue.set(this.arrMsg, 0, "");
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

    filter_clear() {
      document.getElementById("btn_filter").classList.remove("focus");
      document.getElementById("filter_dialog").classList.remove("show");
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

    changeTaskFile(task_id) {
      this.current_task_id = task_id;

      var arr = this.arrTask[task_id];
      if (typeof arr === "undefined" || arr.length == 0)
        this.arrTask[task_id] = [];

      var fileTarget = this.$refs["file_task_" + task_id];

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

    setPages() {
      console.log("setPages");
      this.pages = [];
      let numberOfPages = Math.ceil(
        this.project03_other_task.length / this.perPage
      );

      if (numberOfPages == 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function(posts) {
      if(posts.length == 0)
        return;

      console.log("paginate");
      if (this.page < 1) this.page = 1;
      if (this.page > this.pages.length) this.page = this.pages.length;

      let page = this.page;
          let perPage = this.perPage;
          let from_d = (page * perPage) - perPage;
          let to_d = (page * perPage);


      let tenPages = Math.floor((this.page - 1) / 10);
      if(tenPages < 0)
        tenPages = 0;
      this.pages_10 = [];
      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      this.pages_10 = this.pages.slice(from, to);

      return this.project03_other_task.slice(from_d, to_d);
    },

    
    pre_page: function(){
      let tenPages = Math.floor((this.page - 1) / 10) + 1;

        this.page = parseInt(this.page) - 10;
        if(this.page < 1)
          this.page = 1;
 
        this.pages_10 = [];

        let from = tenPages * 10;
        let to = (tenPages + 1) * 10;

        this.pages_10 = this.pages.slice(from, to);
      
    },

    nex_page: function(){
      let tenPages = Math.floor((this.page - 1) / 10) + 1;

      this.page = parseInt(this.page) + 10;
      if(this.page > this.pages.length)
        this.page = this.pages.length;

      let from = tenPages * 10;
      let to = (tenPages + 1) * 10;
      let pages_10 = this.pages.slice(from, to);

      if(pages_10.length > 0)
        this.pages_10 = pages_10;

    },


    reload_task(task_id) {
      let _this = this;
      const params = {
        id: task_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/project03_other_task_o", {
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

    getProjectOtherTask(id) {
      let _this = this;

      const params = {
        "fs": this.fil_status,
        "fp": this.fil_priority,
        "fc": this.fil_creator,
        "fk": this.fil_keyword,
        "ft": this.fil_type,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/project03_other_task_o", {
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

    getUsers() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/project02_user_online", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.users = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getUsersDeleted(id) {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      const params = {
        pid: id,
      };

      axios
        .get("api/project03_user_other_deleted", {
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

    getDueDate(stage_id) {
      let _this = this;

      if (stage_id == 0) return;

      const params = {
        stage_id: stage_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/project03_other_task_due_date", {
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

    OpenAssignee() {
      document.getElementById("assignee").multiple = true;
    },

    OpenCollaborator() {
      document.getElementById("collaborator").multiple = true;
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
        url: "api/project03_other_task_dup_o",
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

            _this.getProjectOtherTask(0);
          }
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });

          _this.getProjectOtherTask(0);
        });

      _this.task_clear();
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
        url: "api/project03_other_task_del_o",
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

            _this.getProjectOtherTask(0);
          }
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });

          _this.getProjectOtherTask(0);
        });

      _this.task_clear();
    },

    task_clear() {
      this.all_clear()
      this.task_id_to_load = 0;
      this.task_id_to_del = 0;
      this.task_id_to_dup = 0;
      document.getElementById("dialog_red_edit").classList.remove("show");
      document.getElementById("edit_red").classList.remove("focus");

      document.getElementById("dialog_a1").classList.remove("focus");
      document.getElementById("add_a1").classList.remove("show");
    },

    task_edit_clear() {
      this.all_clear()
      this.task_id_to_load = 0;
      this.task_id_to_del = 0;
      this.task_id_to_dup = 0;
      document.getElementById("dialog_red_edit").classList.remove("show");
      document.getElementById("edit_red").classList.remove("focus");

      document.getElementById("dialog_a1").classList.remove("focus");
      document.getElementById("add_a1").classList.remove("show");
    },

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

        var cnt = this.$refs['task_reply_msg_cnt_' + item_id][0];
        cnt.innerHTML = "0";

      this.reload_task(this.proof_id);
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
        url: "api/project03_delete_message_o",
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

            _this.reload_task(_this.proof_id);
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

    count_reply(message_id, ref_id) {
      var comment = this.$refs['task_reply_msg_' + message_id + '_' + ref_id][0];

      var cnt = this.$refs['task_reply_msg_cnt_' + message_id + '_' + ref_id][0];
      cnt.innerHTML = comment.value.replace(/[^\x00-\xff]/g,"xx").length;
    },

    comment_clear(task_id) {
      this.current_task_id = task_id;
      this.arrTask[task_id] = [];
      Vue.set(this.arrTask, 0, "");
      this.$refs["comment_task_" + task_id].value = "";

      this.clear_message(task_id);

      this.reload_task(task_id);
    },

    task_create() {
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

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project03_other_task_o",
        data: form_Data,
      })
        .then(function(response) {
          if (response.data["batch_id"] != 0) {
            _this.task_upload(response.data["batch_id"]);
          } else {
            _this.task_clear();
          }

          if (_this.fileArray.length == 0) {
            _this.getProjectOtherTask(0);
            _this.task_clear();
          }
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        })
        .finally(function() {
          _this.task_clear();
        });
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
          },
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append("batch_type", "other_task_o");
        myForm.append("batch_id", batch_id);
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
      form_Data.append('kind', 'os');

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
          _this.reload_task(_this.proof_id);
       
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {  });
    },

    task_edit_create() {
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

      form_Data.append('pre_items', JSON.stringify(this.record.pre_items));

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project03_other_task_edit_o",
        data: form_Data,
      })
        .then(function(response) {
          if (response.data["batch_id"] != 0) {
            _this.task_edit_upload(response.data["batch_id"]);
          } else {
            _this.task_edit_clear();
          }

          Swal.fire({
            text: "Edited",
            icon: "success",
            confirmButtonText: "OK",
          });

          if (_this.editfileArray.length == 0) {
            _this.getProjectOtherTask(0);
            _this.task_edit_clear();
          }
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        })
        .finally(function() {
          _this.task_edit_clear();
        });
    },

    task_edit_upload(batch_id) {
      this.canSub = false;
      var myArr = this.editfileArray;
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
                vm.$set(vm.editfileArray, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.editfileArray, index, myArr[index]);
              }
            }
          },
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append("batch_type", "other_task_o");
        myForm.append("batch_id", batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function(res) {
            if (res.data.code == 0) {
              myArr[index].progress = 1;
              vm.$set(vm.editfileArray, index, myArr[index]);
              console.log(vm.editfileArray, index);
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

    count_message(task_id) {
      var comment = this.$refs['comment_task_' + task_id];

      var cnt = this.$refs['comment_task_cnt' + task_id];
      cnt.innerHTML = comment.value.replace(/[^\x00-\xff]/g,"xx").length;
    },

    clear_message(task_id) {
      var cnt = this.$refs['comment_task_cnt' + task_id];
      cnt.innerHTML = "0";
    },

    comment_create(task_id) {
      this.current_task_id = task_id;

      let _this = this;

      var comment = this.$refs["comment_task_" + task_id];

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

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_other_task_message_o",
        data: form_Data,
      })
        .then(function(response) {
          if (response.data["batch_id"] != 0) {
            _this.comment_upload(task_id, response.data["batch_id"]);
          } else {
            _this.comment_clear(task_id);
          }

          if (_this.arrTask[task_id].length == 0) {
            _this.getProjectOtherTask(0);
            _this.comment_clear(task_id);
          }
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        })
        .finally(function() {
          _this.comment_clear(task_id);
        });
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
                Vue.set(_this.arrTask, 0, "");
              } else {
                myArr[index].progress = 0.99;
                _this.$set(_this.arrTask[task_id], index, myArr[index]);
                Vue.set(_this.arrTask, 0, "");
              }
            }
          },
        };
        var data = myArr[index];
        var myForm = new FormData();
        myForm.append("batch_type", "other_task_msg_o");
        myForm.append("batch_id", batch_id);
        myForm.append("file", data);

        axios
          .post("api/uploadFile_gcp", myForm, config)
          .then(function(res) {
            if (res.data.code == 0) {
              myArr[index].progress = 1;
              _this.$set(_this.arrTask[task_id], index, myArr[index]);
              console.log(_this.arrTask[task_id], index);
              Vue.set(_this.arrTask, 0, "");
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
        url: "api/project_other_task_reply_o",
        data: form_Data,
      })
        .then(function(response) {
          if (response.data["batch_id"] != 0) {
            _this.msg_upload(item_id, msg_id, response.data["batch_id"]);
          } else {
            _this.msg_clear(item_id);
          }

          if (_this.arrMsg[item_id] === undefined) {
            _this.getProjectOtherTask(0);
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

    msg_upload(item_id, msg_id, batch_id) {
      this.current_msg_item_id = item_id;

      this.canSub = false;
      var myArr = this.arrMsg[item_id];
      var _this = this;

      if (myArr === undefined) {
        _this.getProjectOtherTask(0);
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
        myForm.append("batch_type", "other_task_msg_rep_o");
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
              _this.reload_task(_this.proof_id);
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

    task_clear_o() {

      this.all_clear()

      document.getElementById('dialog_a1_o').classList.remove("focus");
      document.getElementById('add_a1_o').classList.remove("show");
    },

    task_edit_clear_o() {

      this.all_clear()

      document.getElementById('dialog_red_edit_o').classList.remove("show");
      document.getElementById('edit_red_o').classList.remove("focus");
    },

    task_create_o() {
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

      if (this.order.trim() == '') {
        Swal.fire({
          text: 'Please enter order name!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      if (this.due_date.trim() == '' && this.due_time.trim() != '') {
        Swal.fire({
          text: 'Please enter due date!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }

      if (this.order_category.trim() == '') {
        Swal.fire({
          text: 'Please choose Category!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('order', this.order.trim());
      form_Data.append('order_type', this.order_type.trim());
      form_Data.append('title', this.title.trim());
      form_Data.append('category', this.order_category.trim());
      form_Data.append('priority', this.priority);
      form_Data.append('assignee', Array.prototype.map.call(this.assignee, function(item) { return item.id; }).join(","));
      form_Data.append('collaborator', Array.prototype.map.call(this.collaborator, function(item) { return item.id; }).join(",") );
      form_Data.append('due_date', this.due_date.trim());
      form_Data.append('due_time', this.due_time.trim());
      form_Data.append('detail', this.detail.trim());
  
      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task_order_os',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.task_upload_o(response.data['batch_id']);
          }
          else {
            _this.task_clear_o();

          }

          if (_this.fileArray.length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.task_clear_o();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_clear_o() });
    },

    
    task_edit_create_o() {
      let _this = this;

      if (this.task_id_to_load == 0) {
        Swal.fire({
          text: 'Please select a task to edit',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }

      if (this.record.due_date.trim() == '' && this.record.due_time.trim() != '') {
        Swal.fire({
          text: 'Please enter due date!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('task_id', this.record.task_id);
      form_Data.append('title', this.record.title.trim());
      form_Data.append('priority', this.record.priority_id);
      form_Data.append('status', this.record.task_status);
      form_Data.append('assignee', Array.prototype.map.call(this.record.assignee, function(item) { return item.id; }).join(","));
      form_Data.append('collaborator', Array.prototype.map.call(this.record.collaborator, function(item) { return item.id; }).join(","));
      form_Data.append('due_date', this.record.due_date.trim());
      form_Data.append('due_time', this.record.due_time.trim());
      form_Data.append('detail', this.record.detail.trim());

      form_Data.append('od_name', this.record.od_name.trim());
      form_Data.append('order_type', this.record.od_type.trim());

      form_Data.append('pre_items', JSON.stringify(this.record.pre_items));

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task_edit_order_os',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.task_edit_upload_o(response.data['batch_id']);
          }
          else {
            _this.task_edit_clear_o();

          }

          if (_this.editfileArray.length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.task_edit_clear_o();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_edit_clear_o() });
    },

    
    task_create_i() {
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

      if (this.order.trim() == '') {
        Swal.fire({
          text: 'Please enter inquiry name!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      if (this.due_date.trim() == '' && this.due_time.trim() != '') {
        Swal.fire({
          text: 'Please enter due date!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }

      if (this.order_category.trim() == '') {
        Swal.fire({
          text: 'Please choose Category!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }



      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('order', this.order.trim());
      form_Data.append('category', this.order_category.trim());
      form_Data.append('order_type', this.order_type.trim());
      form_Data.append('title', this.title.trim());
      form_Data.append('priority', this.priority);
      form_Data.append('assignee', Array.prototype.map.call(this.assignee, function(item) { return item.id; }).join(","));
      form_Data.append('collaborator', Array.prototype.map.call(this.collaborator, function(item) { return item.id; }).join(",") );
      form_Data.append('due_date', this.due_date.trim());
      form_Data.append('due_time', this.due_time.trim());
      form_Data.append('detail', this.detail.trim());
 


      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task_inquiry_os',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.task_upload_i(response.data['batch_id']);
          }
          else {
            _this.task_clear_i();

          }

          if (_this.fileArray.length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.task_clear_i();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_clear_i() });
    },


    task_clear_i() {

      this.all_clear()

      document.getElementById('dialog_a1_i').classList.remove("focus");
      document.getElementById('add_a1_i').classList.remove("show");
    },

    task_edit_clear_i() {

      this.all_clear()

      document.getElementById('dialog_red_edit_i').classList.remove("show");
      document.getElementById('edit_red_i').classList.remove("focus");
    },


    task_edit_create_i() {
      let _this = this;

      if (this.task_id_to_load == 0) {
        Swal.fire({
          text: 'Please select a task to edit',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }

      if (this.record.due_date.trim() == '' && this.record.due_time.trim() != '') {
        Swal.fire({
          text: 'Please enter due date!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('task_id', this.record.task_id);
      form_Data.append('title', this.record.title.trim());
      form_Data.append('priority', this.record.priority_id);
      form_Data.append('status', this.record.task_status);
      form_Data.append('assignee', Array.prototype.map.call(this.record.assignee, function(item) { return item.id; }).join(","));
      form_Data.append('collaborator', Array.prototype.map.call(this.record.collaborator, function(item) { return item.id; }).join(","));
      form_Data.append('due_date', this.record.due_date.trim());
      form_Data.append('due_time', this.record.due_time.trim());
      form_Data.append('detail', this.record.detail.trim());

      form_Data.append('iq_name', this.record.iq_name.trim());
      form_Data.append('serial_name', this.record.inquiry[0].serial_name);
      form_Data.append('order_type', this.record.iq_type.trim());

      form_Data.append('pre_items', JSON.stringify(this.record.pre_items));

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task_edit_inquiry_o',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.task_edit_upload_i(response.data['batch_id']);
          }
          else {
            _this.task_edit_clear_i();

          }

          if (_this.editfileArray.length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.task_edit_clear_i();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_edit_clear_i() });
    },


  },
});

var app1 = new Vue({
  el: "#meeting",
  data: {
    meetings: [],
    users: [],

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      { name: "10", id: 10 },
      { name: "25", id: 25 },
      { name: "50", id: 50 },
      { name: "100", id: 100 },
      { name: "All", id: 10000 },
    ],
    perPage: 5,

    baseURL: "https://storage.googleapis.com/feliiximg/",

    // calendar
    attendee: [],
    old_attendee: [],
    add_id: 0,

    attachments: [],
  },

  created() {
    this.getUsers();
    //this.getMeetings();
  },

  computed: {},

  mounted() {},

  watch: {},

  methods: {
    getUsers() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/project02_user_online", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.users = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    getMeetings: function() {
      this.action = 1; //select all
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("action", this.action);
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/work_calender_meetings",
        data: form_Data,
      })
        .then(function(response) {
          //this.addDetails(response.data[0]);
          //handle success
          _this.meetings = response.data;
          //console.log(_this.items)
          return response.data;
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });

          return [];
        });
      //this.upload();
      // this.reload();
    },

    warning(txt) {
      Swal.fire({
        text: JSON.stringify(txt),
        icon: "error",
        confirmButtonText: "OK",
      });
    },

    addMeetings: function(
      project_name,
      subject,
      message,
      attendee,
      start_time,
      end_time,
      username
    ) {
      this.action = 2; //add
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      var ret = 0;
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("subject", subject);
      form_Data.append("project_name", project_name);
      form_Data.append("message", message);
      form_Data.append("attendee", attendee);
      form_Data.append("start_time", start_time);
      form_Data.append("end_time", end_time);
      form_Data.append("is_enabled", true);
      form_Data.append("action", this.action);
      form_Data.append("created_by", username);

      var file_elements = document.getElementsByName("file_elements");

      var item = 0;
      for (let i = 0; i < file_elements.length; i++) {
        if (file_elements[i].checked) {
          for (var j = 0; j < this.attachments.length; j++) {
            let file = this.attachments[j];
            if (file.name === file_elements[i].value) {
              form_Data.append("files[" + item++ + "]", file);
              break;
            }
          }
        }
      }

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/work_calender_meetings",
        data: form_Data,
      })
        .then(function(response) {
          //this.addDetails(response.data[0]);
          //handle success
          //_this.items = response.data
          //console.log(_this.items)
          ret = response.data[0];
          _this.notify_mail(ret, 1);
          return ret;
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });

          return 0;
        });
      //this.upload();
      // this.reload();
    },

    editMeetings: function(
      id,
      subject,
      message,
      attendee,
      start_time,
      end_time,
      username
    ) {
      this.action = 3; //update
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      var ret = 0;
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("id", id);
      form_Data.append("subject", subject);
      form_Data.append("message", message);
      form_Data.append("attendee", attendee);
      form_Data.append("start_time", start_time);
      form_Data.append("end_time", end_time);
      form_Data.append("is_enabled", true);
      form_Data.append("action", this.action);
      form_Data.append("updated_by", username);
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/work_calender_meetings",
        data: form_Data,
      })
        .then(function(response) {
          //this.addDetails(response.data[0]);
          //handle success
          //_this.items = response.data
          //console.log(_this.items)
          ret = response.data[0];

          return ret;
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });

          return 0;
        });
      //this.upload();
      // this.reload();
    },

    notify_mail(batch_id, type) {
      var form_Data = new FormData();

      form_Data.append("bid", batch_id);
      form_Data.append("type", type);

      const token = sessionStorage.getItem("token");

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${token}`,
        },
        url: "api/project_meeting_mail",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
        })
        .catch(function(response) {
          //handle error
          console.log(response);
        });
    },

    delMeetings: function(id) {
      this.action = 7; //add
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      var ret = 0;
      let _this = this;
      let _id = id;
      form_Data.append("jwt", token);
      form_Data.append("id", id);

      form_Data.append("action", this.action);
      form_Data.append("deleted_by", username);
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/work_calender_meetings",
        data: form_Data,
      })
        .then(function(response) {
          //this.addDetails(response.data[0]);
          //handle success
          //_this.items = response.data
          //console.log(_this.items)
          ret = response.data[0];

          _this.notify_mail(_id, 3);

          return ret;
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });

          return 0;
        });
      //this.upload();
      // this.reload();
    },
  },
});
