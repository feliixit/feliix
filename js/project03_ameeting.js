Vue.component('v-select', VueSelect.VueSelect)

var app = new Vue({
  el: '#app',
  data: {
  
    receive_records: [],
    record: {},
    record_r: {},

    record: [],
    project03_other_task_r: [],

    users: [],
    users_del: [],
    users_del_r: [],

    submit: false,
    submit_r:false,

    is_Editing: false,

    contactor: '',
    username: '',
    client_type: '',
    category: '',
    category_id: 0,
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

    // data
    id:0,
    stage_id:0,
    project_id:0,
    down_payment_date: '',
    account_executive: '',
    pic: '',
    quotation: '',
    client_name : '',
    contact_person : '',
    contact_number : '',
    delivery_address_within : '',
    delivery_address_outside : '',
    exact_delivery_address : '',
    detail_delivery_address : '',
    attached_layout : '',
    delivery_permit : '',
    work_permit : '',
    permit_processing_note : '',
    other_request : '',
    date_of_delivery : '',
    client_deadline : '',
    delivery_1st : '',
    delivery_1st_items : '',
    delivery_2nd : '',
    delivery_2nd_items : '',
    os_delivery_only : '',
    os_delivery_install : '',
    lt_delivery_only : '',
    lt_delivery_install : '',
    delivery_install : '',
    scope_attached_layout : '',
    timeline_check : '',
    timeline : '',
    data_check : '',
    data : '',
    electrical_check : '',
    electrical : '',
    flooring_check : '',
    flooring : '',
    type_and_ceiling : '',
    painting_check : '',
    painting : '',
    ceiling_electrical_check : '',
    ceiling_electrical : '',
    manpower_check : '',
    manpower : '',
    materials_check : '',
    materials : '',
    trucking_services : '',
    purchasing_of_special_products_check : '',
    purchasing_of_special_products : '',
    tools_check : '',
    tools : '',
    status : '',

    updator : '',
    updated_at: '',

    // TASKS
    title: '',
    priority: 0,
    assignee: [],
    collaborator: [],
    due_date: '',
    due_time: '',
    detail: '',


    attached_layout_file: [],
    scope_attached_layout_file: [],
    other_attached_layout_file: [],

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

    editfileArray: [],


    task_id_to_dup_r:0,
    task_id_to_del_r:0,
    task_id_to_load_r:0,

    editfileArray_r: [],

    // filter
    fil_priority:0,
    fil_status:0,
    fil_due_date:'',
    opt_due_date:[],

    // calendar
    attendee:[],

    // project_name
    project_id : 0,
    project_name : '',
    stage: '',

    special : '',
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
        _this.getProjectAMeeting(_this.stage_id);

        _this.getProjectInfo(_this.stage_id);
        _this.getUsersDeleted(_this.stage_id);
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
  

  },



  methods: {

    cancel() {
      this.getProjectAMeeting(this.stage_id);
      this.is_Editing = false;
    },

    edit() {
      this.is_Editing = true;
    },

    change_attached_layout_file() {
      var fileTarget = this.$refs.attached_layout_file;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.attached_layout_file.indexOf(fileTarget.files[i]) == -1 ||  this.attached_layout_file.length == 0) {
      
            var obj = {
              id:0,
              checked: true,
              file: fileTarget.files[i],
              gcp_name : '',
              name: fileTarget.files[i].name,
            };
           
            this.attached_layout_file.push(obj);
        } else {
          fileTarget.value = "";
        }
      }
    },

    change_scope_attached_layout_file() {
      var fileTarget = this.$refs.scope_attached_layout_file;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.scope_attached_layout_file.indexOf(fileTarget.files[i]) == -1 ||  this.scope_attached_layout_file.length == 0) {
      
            var obj = {
              id:0,
              checked: true,
              file: fileTarget.files[i],
              gcp_name : '',
              name: fileTarget.files[i].name,
            };
           
            this.scope_attached_layout_file.push(obj);
        } else {
          fileTarget.value = "";
        }
      }
    },

    change_other_attached_layout_file() {
      var fileTarget = this.$refs.other_attached_layout_file;

      for (i = 0; i < fileTarget.files.length; i++) {
        // remove duplicate
        if (
          this.other_attached_layout_file.indexOf(fileTarget.files[i]) == -1 ||  this.other_attached_layout_file.length == 0) {
      
            var obj = {
              id:0,
              checked: true,
              file: fileTarget.files[i],
              gcp_name : '',
              name: fileTarget.files[i].name,
            };
           
            this.other_attached_layout_file.push(obj);
        } else {
          fileTarget.value = "";
        }
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

    getProjectAMeeting(stage_id) {
      let _this = this;

      if (stage_id == 0)
        return;

      const params = {
        stage_id: stage_id,
        status: this.fil_status,
        priority: this.fil_priority,
        duedate: this.fil_due_date,
      };

      let token = localStorage.getItem('accessToken');

      axios
        .get('api/project03_ameeting', { params, headers: { "Authorization": `Bearer ${token}` } })
        .then(
          (res) => {
            _this.record = res.data;
            if(_this.record.length > 0)
            {
              _this.id = _this.record[0].id;
              _this.stage = _this.record[0].stage;
              _this.project_id = _this.record[0].project_id;
              _this.category = _this.record[0].category;
              _this.category_id = _this.record[0].category_id;
              _this.down_payment_date = _this.record[0].down_payment_date;
              _this.account_executive = _this.record[0].account_executive;
              _this.pic = _this.record[0].pic;
              _this.quotation = _this.record[0].quotation;
              _this.client_name = _this.record[0].client_name;
              _this.contact_person  = _this.record[0].contact_person;
              _this.contact_number = _this.record[0].contact_number;
              _this.delivery_address_within = _this.record[0].delivery_address_within;
              _this.delivery_address_outside = _this.record[0].delivery_address_outside;
              _this.exact_delivery_address = _this.record[0].exact_delivery_address;
              _this.detail_delivery_address = _this.record[0].detail_delivery_address;
              _this.attached_layout = _this.record[0].attached_layout;
              _this.delivery_permit = _this.record[0].delivery_permit;
              _this.work_permit = _this.record[0].work_permit;
              _this.permit_processing_note = _this.record[0].permit_processing_note;
              _this.other_request = _this.record[0].other_request;
              _this.date_of_delivery = _this.record[0].date_of_delivery;
              _this.client_deadline = _this.record[0].client_deadline;
              _this.delivery_1st = _this.record[0].delivery_1st;
              _this.delivery_1st_items = _this.record[0].delivery_1st_items;
              _this.delivery_2nd = _this.record[0].delivery_2nd;
              _this.delivery_2nd_items = _this.record[0].delivery_2nd_items;
              _this.os_delivery_only = _this.record[0].os_delivery_only;
              _this.os_delivery_install = _this.record[0].os_delivery_install;
              _this.lt_delivery_only = _this.record[0].lt_delivery_only;
              _this.lt_delivery_install = _this.record[0].lt_delivery_install;
              _this.delivery_install = _this.record[0].delivery_install;
              _this.scope_attached_layout = _this.record[0].scope_attached_layout;
              _this.timeline_check = _this.record[0].timeline_check;
              _this.timeline = _this.record[0].timeline;
              _this.data_check = _this.record[0].data_check;
              _this.data = _this.record[0].data;
              _this.electrical_check = _this.record[0].electrical_check;
              _this.electrical = _this.record[0].electrical;
              _this.flooring = _this.record[0].flooring;
              _this.flooring_check = _this.record[0].flooring_check;
              _this.type_and_ceiling = _this.record[0].type_and_ceiling;
              _this.painting = _this.record[0].painting;
              _this.painting_check = _this.record[0].painting_check;
              _this.ceiling_electrical_check = _this.record[0].ceiling_electrical_check;
              _this.ceiling_electrical = _this.record[0].ceiling_electrical;
              _this.manpower = _this.record[0].manpower;
              _this.manpower_check = _this.record[0].manpower_check;
              _this.materials_check = _this.record[0].materials_check;
              _this.materials = _this.record[0].materials;
              _this.trucking_services = _this.record[0].trucking_services;
              _this.purchasing_of_special_products_check = _this.record[0].purchasing_of_special_products_check;
              _this.purchasing_of_special_products = _this.record[0].purchasing_of_special_products;
              _this.tools_check = _this.record[0].tools_check;
              _this.tools = _this.record[0].tools;
              _this.status = _this.record[0].status;

              _this.updator = _this.record[0].updator;
              _this.updated_at = _this.record[0].updated_at;

              _this.attached_layout_file = _this.record[0].attached_layout_files;
              _this.scope_attached_layout_file = _this.record[0].scope_attached_layout_files;
              _this.other_attached_layout_file = _this.record[0].other_attached_layout_files;

              // app.$forceUpdate();
            }
          },
          (err) => {
            alert(err.response);
          },
        )
        .finally(() => {
          _this.is_Editing = false;
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

    getUsersDeleted(id) {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      const params = {
        pid : id,
       
      };

      axios
        .get('api/project03_user_other_deleted', { params, headers: { "Authorization": `Bearer ${token}` } })
        .then(
          (res) => {
            _this.users_del = res.data;
          },
          (err) => {
            alert(err.response);
          },
        )
        .finally(() => {

        });
    },

    getUsersDeleted_R(id) {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      const params = {
        pid : id,
       
      };

      axios
        .get('api/project03_user_other_deleted_r', { params, headers: { "Authorization": `Bearer ${token}` } })
        .then(
          (res) => {
            _this.users_del_r = res.data;
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
            _this.stage = res.data[0].stage;
            _this.special = res.data[0].special;
          },
          (err) => {
            alert(err.response);
          },
        )
        .finally(() => {

        });
    },

    getDueDate(stage_id) {

      let _this = this;

      if (stage_id == 0)
        return;

      const params = {
        stage_id: stage_id,

      };

      let token = localStorage.getItem('accessToken');

      axios
        .get('api/project03_other_task_due_date', {params, headers: { "Authorization": `Bearer ${token}` } })
        .then(
          (res) => {
            _this.opt_due_date = res.data;
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


    task_del_r() {
      if(this.task_id_to_del_r != 0)
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
              
                _this.do_task_delete_r(_this.task_id_to_del_r); // <--- submit form programmatically
              
            } else {
              // swal("Cancelled", "Your imaginary file is safe :)", "error");
            }
          });
      }
    },

    do_task_delete_r(task_id_to_del_r) {
      var token = localStorage.getItem('token');
        var form_Data = new FormData();
        let _this = this;

        form_Data.append('jwt', token);
        form_Data.append('task_id_to_del', task_id_to_del_r);

        axios({
            method: 'post',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            url: 'api/project03_other_task_del_r',
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

            _this.getProjectOtherTask_r(_this.stage_id);
        });

        _this.task_clear_r();
    },

    task_clear() {

      this.detail = "";

      document.getElementById('dialog_a1').classList.remove("focus");
      document.getElementById('add_a1').classList.remove("show");
    },

    task_edit_clear() {

      document.getElementById('dialog_red_edit').classList.remove("show");
      document.getElementById('edit_red').classList.remove("focus");
    },

    task_edit_clear_r() {

      document.getElementById('dialog_blue_edit').classList.remove("show");
      document.getElementById('edit_blue').classList.remove("focus");
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

    msg_delete(message_id, item_id, mid, uid) {
      if(mid !== uid)
        return;

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

    msg_delete_r(message_id, item_id, mid, uid) {
      if(mid !== uid)
        return;
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


      if (this.due_date.trim() == '' && this.due_time.trim() != '') {
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

      form_Data.append('stage_id', this.stage_id);
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

    a_meeting_edit_create() {
      let _this = this;
/*
      if (this.id == 0) {
        Swal.fire({
          text: 'Please select a task to edit',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }
*/
      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('id', this.id);
      form_Data.append('down_payment_date', this.down_payment_date);
      form_Data.append('stage_id', this.stage_id);
      form_Data.append('account_executive', this.account_executive);
      form_Data.append('pic', this.pic);
      form_Data.append('quotation', this.quotation);
      form_Data.append('client_name', this.client_name);
      form_Data.append('contact_person', this.contact_person);
      form_Data.append('contact_number', this.contact_number);
      form_Data.append('delivery_address_within', this.delivery_address_within);
      form_Data.append('delivery_address_outside', this.delivery_address_outside);
      form_Data.append('exact_delivery_address', this.exact_delivery_address);
      form_Data.append('detail_delivery_address', this.detail_delivery_address);
      form_Data.append('attached_layout', this.attached_layout);
      form_Data.append('delivery_permit', this.delivery_permit);
      form_Data.append('work_permit', this.work_permit);
      form_Data.append('permit_processing_note', this.permit_processing_note);
      form_Data.append('other_request', this.other_request);
      form_Data.append('date_of_delivery', this.date_of_delivery);
      form_Data.append('client_deadline', this.client_deadline);
      form_Data.append('delivery_1st', this.delivery_1st);
      form_Data.append('delivery_1st_items', this.delivery_1st_items);
      form_Data.append('delivery_2nd', this.delivery_2nd);
      form_Data.append('delivery_2nd_items', this.delivery_2nd_items);
      form_Data.append('os_delivery_only', this.os_delivery_only);
      form_Data.append('os_delivery_install', this.os_delivery_install);
      form_Data.append('lt_delivery_only', this.lt_delivery_only);
      form_Data.append('lt_delivery_install', this.lt_delivery_install);
      form_Data.append('delivery_install', this.delivery_install);
      form_Data.append('scope_attached_layout', this.scope_attached_layout);
      form_Data.append('timeline_check', this.timeline_check);
      form_Data.append('timeline', this.timeline);
      form_Data.append('data_check', this.data_check);
      form_Data.append('data', this.data);
      form_Data.append('electrical_check', this.electrical_check);
      form_Data.append('electrical', this.electrical);
      form_Data.append('flooring_check', this.flooring_check);
      form_Data.append('flooring', this.flooring);
      form_Data.append('type_and_ceiling', this.type_and_ceiling);
      form_Data.append('painting_check', this.painting_check);
      form_Data.append('painting', this.painting);
      form_Data.append('ceiling_electrical_check', this.ceiling_electrical_check);
      form_Data.append('ceiling_electrical', this.ceiling_electrical);
      form_Data.append('manpower_check', this.manpower_check);
      form_Data.append('manpower', this.manpower);
      form_Data.append('materials_check', this.materials_check);
      form_Data.append('materials', this.materials);
      form_Data.append('trucking_services', this.trucking_services);
      form_Data.append('purchasing_of_special_products_check', this.purchasing_of_special_products_check);
      form_Data.append('purchasing_of_special_products', this.purchasing_of_special_products);
      form_Data.append('tools_check', this.tools_check);
      form_Data.append('tools', this.tools);
      form_Data.append('status', this.status);

      form_Data.append("attached_layout_file", JSON.stringify(this.attached_layout_file));

      for(var j = 0; j < this.attached_layout_file.length; j++) {
        let file = this.attached_layout_file[j].file;
        if(typeof file !== 'undefined' && file !== null) 
          form_Data.append('attached_layout_file_' + j, file);
      }

      form_Data.append("scope_attached_layout_file", JSON.stringify(this.scope_attached_layout_file));

      for(var j = 0; j < this.scope_attached_layout_file.length; j++) {
        let file = this.scope_attached_layout_file[j].file;
        if(typeof file !== 'undefined' && file !== null) 
          form_Data.append('scope_attached_layout_file_' + j, file);
      }

      form_Data.append("other_attached_layout_file", JSON.stringify(this.other_attached_layout_file));

      for(var j = 0; j < this.other_attached_layout_file.length; j++) {
        let file = this.other_attached_layout_file[j].file;
        if(typeof file !== 'undefined' && file !== null) 
          form_Data.append('other_attached_layout_file_' + j, file);
      }

      const token = sessionStorage.getItem('token');

      if (this.id == 0) 
        url = "api/project03_ameeting_add";
      else
        url = "api/project03_ameeting_edit";

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: url,
        data: form_Data
      })
        .then(function (response) {
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });

          _this.reset();
         
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          // _this.reset();
        });
    },

    export_word: function () {
      var form_Data = new FormData();

      const filename = "attendance";
      let _this = this;

      const token = sessionStorage.getItem("token");

      axios({
              method: "get",
              url: "project03_ameeting_word?stage_id=" + _this.stage_id,
              data: form_Data,
              responseType: "blob", // important
          })
          .then(function (response) {
              const url = window.URL.createObjectURL(new Blob([response.data]));
              const link = document.createElement("a");
              link.href = url;

              link.setAttribute("download", "PROJECT TURNOVER CHECKLIST.docx");

              document.body.appendChild(link);
              link.click();
          })
          .catch(function (response) {
              //handle error
              console.log(response);
          });
  },


    reset: function() {
      this.submit = false;
      this.is_Editing = false;
      this.getProjectAMeeting(this.stage_id);
    },

    task_edit_create() {
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

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task_edit',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.task_edit_upload(response.data['batch_id']);
          }
          else {
            _this.task_edit_clear();

          }

          if (_this.editfileArray.length == 0) {
            _this.getProjectOtherTask(_this.stage_id);
            _this.task_edit_clear();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_edit_clear() });
    },

    task_edit_create_r() {
      let _this = this;

      if (this.task_id_to_load_r == 0) {
        Swal.fire({
          text: 'Please select a task to edit',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('task_id', this.record_r.task_id);
      form_Data.append('title', this.record_r.title.trim());
      form_Data.append('priority', 0);
      form_Data.append('status', 0);
      form_Data.append('assignee', Array.prototype.map.call(this.record_r.assignee_id, function(item) { return item.id; }).join(","));
      form_Data.append('collaborator', '');
      form_Data.append('due_date', '');
      form_Data.append('detail', this.record_r.detail.trim());

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_other_task_edit_r',
        data: form_Data
      })
        .then(function (response) {
          if (response.data['batch_id'] != 0) {
            _this.task_edit_upload_r(response.data['batch_id']);
          }
          else {
            _this.task_edit_clear_r();

          }

          if (_this.editfileArray_r.length == 0) {
            _this.getProjectOtherTask_r(_this.stage_id);
            _this.task_edit_clear_r();
          }
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () { _this.task_edit_clear_r() });
    },

    task_edit_upload(batch_id) {

      this.canSub = false;
      var myArr = this.editfileArray;
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
                vm.$set(vm.editfileArray, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.editfileArray, index, myArr[index]);
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
              vm.$set(vm.editfileArray, index, myArr[index]);
              console.log(vm.editfileArray, index);
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

    task_edit_upload_r(batch_id) {

      this.canSub = false;
      var myArr = this.editfileArray_r;
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
                vm.$set(vm.editfileArray_r, index, myArr[index]);
              } else {
                myArr[index].progress = 0.99;
                vm.$set(vm.editfileArray_r, index, myArr[index]);
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
              vm.$set(vm.editfileArray_r, index, myArr[index]);
              console.log(vm.editfileArray_r, index);
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
      form_Data.append('priority', 0);
      form_Data.append('assignee',  Array.prototype.map.call(this.assignee_r, function(item) { return item.id; }).join(","));
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

          if (_this.arrMsg[item_id] === undefined) {
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

      if(myArr === undefined)
      { 
        _this.getProjectOtherTask(_this.stage_id);
        _this.msg_clear(item_id);
        return;
      }
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

          if (_this.arrMsg_r[item_id] === undefined) {
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

      if(myArr === undefined)
      { 
        _this.getProjectOtherTask_r(_this.stage_id);
        _this.msg_clear_r(item_id);
        return;
      }

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