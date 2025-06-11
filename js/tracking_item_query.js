
var app = new Vue({
  el: '#app',
  data:{
    project_category : '',
    client_type : '',
    priority: '',
    status : '',
    reason : '',
    project_name : '',
    special_note: '',

    title: '',
    
    type: 'project',

    img_url: 'https://storage.googleapis.com/feliiximg/',

    project_id : 0,
    task_id : 0,
    projects : [],

    orders: [],

    kind: '',
    generate: '',
    task_a : [],
    task_d : [],
    task_l : [],
    task_o : [],
    task_sl : [],
    task_sv : [],

    ins_title : '',
    ins_project_id : 0,

    is_modifying: false,

    fil_tracking : '',
    fil_prod_id : '',
    fil_prod_code : '',
    fil_pool: '',
    fil_project_related : '',
    fil_location : '',
    fil_sample : '',
    fil_status : '',
    fil_order : '',
    fil_date_from : '',
    fil_date_to : '',

    od_opt1 : '',
    od_ord1 : '',

    od_opt2 : '',
    od_ord2 : '',

    probability : 0,

    receive_records: [],
    record: {},

    categorys : {},
    client_types : {},
    priorities : {},
    groups : {},
    statuses : {},
    stages : {},
    creators : {},

    users : {},

    pre_data : {
      title : '',
      kind : '',
      project_id : 0,
    },

    ins_task : [],

    submit : false,
    // paging
    page: 1,
    pg:0,
    //perPage: 10,
    pages: [],

    pages_10: [],

    inventory: [
      {name: '10', id: 10},
      {name: '25', id: 25},
      {name: '50', id: 50},
      {name: '100', id: 100},
      {name: 'All', id: 10000}
    ],
    perPage: 20,

    total : 0,

    // info
    name :"",
    title: "",
    is_manager: "",
    followup: "",

    comm_fileArray: [],
    comm_canSub: true,
    comm_finish: false,

    transmittal: false,

  },

  created () {
    let _this = this;
    let uri = window.location.href.split("?");
    if (uri.length >= 2) {
      let vars = uri[1].split("&");

      let tmp = "";
      vars.forEach(async function(v) {
        tmp = v.split("=");
        if (tmp.length == 2) {
          switch (tmp[0]) {
            case "tid":
              _this.fil_tracking = decodeURI(tmp[1]);
              break;
            case "fpi":
              _this.fil_prod_id = decodeURI(tmp[1]);
              break;
            case "fpc":
              _this.fil_prod_code = decodeURI(tmp[1]);
              break;
            case "fp":
              _this.fil_pool = decodeURI(tmp[1]);
              break;
            case "fpr":
              _this.fil_project_related = decodeURI(tmp[1]);
              break;
            case "loc":
              _this.fil_location = decodeURI(tmp[1]);
              break;
            case "sap":
              _this.fil_sample = decodeURI(tmp[1]);
              break;
            case "fs":
              _this.fil_status = decodeURI(tmp[1]);
              break;
            case "fo":
              _this.fil_order = decodeURI(tmp[1]);
              break;
            case "fdf":
              _this.fil_date_from = decodeURI(tmp[1]);
              break;
            case "fdt":
              _this.fil_date_to = decodeURI(tmp[1]);
              break;
            case "op1":
              _this.od_opt1 = decodeURI(tmp[1]);
              break;
            case "od1":
              _this.od_ord1 = decodeURI(tmp[1]);
              break;
            case "op2":
              _this.od_opt2 = decodeURI(tmp[1]);
              break;
            case "od2":
              _this.od_ord2 = decodeURI(tmp[1]);
              break;
            case "id":
              _this.id = tmp[1];
              break;
            case "pg":
              _this.pg = tmp[1];
              break;
            case "page":
              _this.page = tmp[1];
              break;
            case "size":
              _this.perPage = tmp[1];
              break;
            default:
              console.log(`Too many args`);
          }
        }
      });
    }

    this.getRecords();
    this.getProjects();
    this.getOrders();
    this.getCreators();
    this.getUsers();

    this.getTask('a');
    this.getTask('d');
    this.getTask('l');
    this.getTask('o');
    this.getTask('sv');
    this.getTask('sl');

    this.getAccess();
    this.getUserName();
  },

  computed: {
    displayedPosts () {
      // if(this.pg == 0)
      //   this.apply_filters();

      this.setPages();
        return this.paginate(this.receive_records);
    },

    showExtra: function(){
      return (this.status==10);
    },
  },

  mounted(){
    
  },

  watch: {
  

    type() {
      this.ins_project_id = 0;

      if(!this.is_modifying)
        this.kind = '';

      if(this.pre_data.kind == '' && this.is_modifying) 
      {
        this.kind = "a"
      }
 
      //this.project_id = 0;
      //this.kind = '';
    },

    kind(value) {
      this.ins_project_id = 0;
      if(this.pre_data.kind !== value && this.pre_data.kind !== '') 
      {
        this.task_id = 0;
        
      }

      if(this.pre_data.kind !== value && this.pre_data.kind == '') 
      {
        if(value == 'a')
        {
          var firstValue = this.task_a.filter(element => typeof element!==undefined).shift();
          this.task_id = firstValue !== undefined ? firstValue.id : 0;
        }
        if(value == 'd')
        {
          var firstValue = this.task_d.filter(element => typeof element!==undefined).shift();
          this.task_id = firstValue !== undefined ? firstValue.id : 0;
        }
        if(value == 'l')
        {
          var firstValue = this.task_l.filter(element => typeof element!==undefined).shift();
          this.task_id = firstValue !== undefined ? firstValue.id : 0;
        }
        if(value == 'o')
        {
          var firstValue = this.task_o.filter(element => typeof element!==undefined).shift();
          this.task_id = firstValue !== undefined ? firstValue.id : 0;
        }
        if(value == 'sl')
        {
          var firstValue = this.task_sl.filter(element => typeof element!==undefined).shift();
          this.task_id = firstValue !== undefined ? firstValue.id : 0;
        }
        if(value == 'sv')
        {
          var firstValue = this.task_sv.filter(element => typeof element!==undefined).shift();
          this.task_id = firstValue !== undefined ? firstValue.id : 0;
        }
      }
        
    },

    receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },

      status (value) {
        if(value == 9) { 
          this.probability = 100;
          this.$refs.probability.setAttribute('disabled', '');
        }
        else
          this.$refs.probability.removeAttribute('disabled');
      },

  },



  methods:{

    getUsers () {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      axios
          .get('api/admin/transmittal_project_creators', { headers: {"Authorization" : `Bearer ${token}`} })
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

  getAccess: function() {
    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    let _this = this;

    form_Data.append('jwt', token);

    axios({
        method: 'get',

        headers: {
            'Content-Type': 'multipart/form-data',
        },
        url: 'api/soa_access_control?kind=transmittal',

    })
    .then(function(response) {
        //handle success
        _this.transmittal = response.data.transmittal;
      

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

    setupChosen: function(){
      $(document).ready(function(){
        //Chosen
        $(".limitedNumbChosen").chosen({
          max_selected_options: 2,
          placeholder_text_multiple: "Which are two of most productive days of your week"
        })
        .bind("chosen:maxselected", function (){
          window.alert("You reached your limited number of selections which is 2 selections!");
        })
       
      });
    },

    getCreators () {

      let _this = this;

      let token = localStorage.getItem('accessToken');

      axios
          .get('api/admin/transmittal_creators', { headers: {"Authorization" : `Bearer ${token}`} })
          .then(
          (res) => {
              _this.creators = res.data;
          },
          (err) => {
              alert(err.response);
          },
          )
          .finally(() => {
              
          });
  },

    editRow:function(item){
        if(this.is_modifying)
            return;

        // keep pre data
        this.pre_data.title = item.title;
        this.pre_data.kind = item.kind;
        this.pre_data.project_id = item.project_id; 

        this.is_modifying = true;

        item['is_edited'] = 0;

        this.project_id = 0;
        this.task_id = 0;

        this.title = item['title'];

        this.type = (item['kind'] == '' ? 'project' : 'task');
        
        if(item['kind'] == '')
         this.project_id = item['project_id'];

        if(item['kind'] !== '')
         this.task_id = item['project_id'];

        this.kind = item['kind'];
        this.followup = item['followup'];

        console.log(item);
    },

    duplicateRow: function(item){
      let _id = item['id'];
      let _this = this;

      Swal.fire({
          title: "Duplicate",
          text: "Are you sure to duplicate this quotation?",
          icon: "info",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.value) {

            let me = _this;

              token = localStorage.getItem('token');
              var form_Data = new FormData();
              form_Data.append('jwt', token);

              form_Data.append('id', _id);

              //DELETE table_name WHERE ID=id;
              $.ajax({
                  url: "api/transmittal_duplicate",
                  type: "POST",
                  contentType: 'multipart/form-data',
                  processData: false,
                  contentType: false,
                  data: form_Data,

                  success: function (result) {
                      console.log(result);
                      Swal.fire({
                        html: result.message,
                        icon: "info",
                        confirmButtonText: "OK",
                      });
                      _this.getRecords();
                  },

                  // show error message to user
                  error: function (xhr, resp, text) {

                  }
              });

          } else {

          }
      });
    },

    deleteRow: function(item){
      let _id = item['id'];
      let _this = this;

      if(item['files'].length > 0)
      {
        Swal.fire({
          text: "User is not allowed to delete the transmittal record already with scanned copy. Please delete the scanned copy first.",
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        return;
      }

      if(item['created_by'] != this.name && this.transmittal == false)
      {
        Swal.fire({
          text: "Permission Deny! Please contact the creator of this transmittal record for deleting.",
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        return;
      }

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

            let me = _this;

              token = localStorage.getItem('token');
              var form_Data = new FormData();
              form_Data.append('jwt', token);

              form_Data.append('id', _id);

              //DELETE table_name WHERE ID=id;
              $.ajax({
                  url: "api/transmittal_delete",
                  type: "POST",
                  contentType: 'multipart/form-data',
                  processData: false,
                  contentType: false,
                  data: form_Data,

                  success: function (result) {
                      console.log(result);
                      Swal.fire({
                        html: result.message,
                        icon: "info",
                        confirmButtonText: "OK",
                      });
                      _this.getRecords();
                  },

                  // show error message to user
                  error: function (xhr, resp, text) {

                  }
              });

          } else {

          }
      });
    },

    confirmRow: function(item){
      var token = localStorage.getItem('token');
      var form_Data = new FormData();
      let _this = this;

        form_Data.append('jwt', token);

        const title = this.title.trim();
        //var project_id = item['project_id'];
        var project_id = this.project_id;

        const project_name = this.shallowCopy(
                    this.projects.find(
                      (element) => element.id == project_id));

        if(this.type == 'task') {
          form_Data.append('kind', this.kind);
          project_id = this.task_id;
        }
                    

        form_Data.append('title', title);
        form_Data.append('project_id', project_id);
        form_Data.append('followup', this.followup);
        form_Data.append('id', item['id']);

        axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                },
                url: 'api/transmittal_edit_row',
                data: form_Data
            })
            .then(function(response) {
                //handle success
                console.log(response)

                _this.getRecords();
                _this.title = '';
                _this.project_id = 0;
                _this.task_id = 0;
                _this.kind = '';
                _this.type = 'project';

            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });

        item['is_edited'] = 1; 
        
        this.is_modifying = false;
    },

    cancelRow: function(item){
        this.title = '';
     
        item['project_id'] = this.pre_data.project_id;
        item['title'] = this.pre_data.title;
        item['kind'] = this.pre_data.kind;

        item['is_edited'] = 1; 

        this.type='project';

        this.is_modifying = false;
    },

    setPages () {
      console.log('setPages');
      this.pages = [];

      let numberOfPages = Math.ceil(this.total / this.perPage);


      if(numberOfPages == 1)
        this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }

      this.paginate(this.receive_records);
        },

        paginate: function (posts) {
          console.log('paginate');
          if(this.page < 1)
            this.page = 1;
          if(this.page > this.pages.length)
            this.page = this.pages.length;

            let tenPages = Math.floor((this.page - 1) / 10);
            if(tenPages < 0)
              tenPages = 0;
            this.pages_10 = [];
            let from = tenPages * 10;
            let to = (tenPages + 1) * 10;

            this.pages_10 = this.pages.slice(from, to);

          
            return  this.receive_records;
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


    getRecords: function(keyword) {
      let _this = this;

      const params = {
                tid : _this.fil_tracking,
                fpi: _this.fil_prod_id,
                fpc: _this.fil_prod_code,
       
                fp: _this.fil_pool,
                fpr: _this.fil_project_related,
                loc: _this.fil_location,
                sap: _this.fil_sample,

                fs: _this.fil_status,
                fo: _this.fil_order,
                fdf: _this.fil_date_from,
                fdt: _this.fil_date_to,

                op1: _this.od_opt1,
                od1: _this.od_ord1,
                op2: _this.od_opt2,
                od2: _this.od_ord2,

                page: _this.page,
                size: _this.perPage,
            };

            this.total = 0;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/tracking_item_query', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.receive_records = res.data;

                  if(_this.receive_records.length > 0)
                   _this.total = _this.receive_records[0].cnt;

                  if(_this.pg !== 0)
                  { 
                    _this.page = _this.pg;
                    _this.setPages();
                  }
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getProjects () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/tracking_item_get_project_name_by_keyword', { headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.projects = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      getOrders () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/tracking_item_get_order_by_keyword', { headers: {"Authorization" : `Bearer ${token}`} })
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

      getTask(kind) {

        let _this = this;
  
        let token = localStorage.getItem('accessToken');
        const params = {
          kind : kind,
        };
        
        axios
            .get('api/project02_get_task_by_keyword', { params, headers: {"Authorization" : `Bearer ${token}`} })
            .then(
            (res) => {
              switch (kind) {
                case 'a':
                  _this.task_a = res.data;
                  break;
                case 'd':
                  _this.task_d = res.data;
                  break;
                case 'l':
                  _this.task_l = res.data;
                  break;
                case 'o':
                  _this.task_o = res.data;
                  break;
                case 'sv':
                  _this.task_sv = res.data;
                  break;
                case 'sl':
                  _this.task_sl = res.data;
                  break;
                
              }
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

      async comm_changeFile(id) {
        
        var fileTarget = $("#comm_file_" + id)[0];
        this.comm_fileArray = [];
  
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

          await this.comm_upload(id);

          Swal.fire({
            text: "Uploading Finished!!",
            icon: 'success',
            confirmButtonText: 'OK'
          })
      },

      delete_me(file) {
        let _file = file;
        let _this = this;
        let _id = file.id;

        if(file.username != this.name && this.transmittal == false)
        {
          Swal.fire({
            text: "You don’t have access to delete it.",
            icon: 'warning',
            confirmButtonText: 'OK'
          })
          return;
        }

        Swal.fire({
          title: "Delete",
          text: "Are you sure to delete?",
          icon: "info",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.value) {

            let me = _this;

              token = localStorage.getItem('token');
              var form_Data = new FormData();
              form_Data.append('jwt', token);

              form_Data.append('id', _id);

              //DELETE table_name WHERE ID=id;
              $.ajax({
                  url: "api/transmittal_delete_file",
                  type: "POST",
                  contentType: 'multipart/form-data',
                  processData: false,
                  contentType: false,
                  data: form_Data,

                  success: function (result) {
                      console.log(result);
                      
                      _this.getRecords();
                  },

                  // show error message to user
                  error: function (xhr, resp, text) {

                  }
              });

          } else {

          }
      });
      },

      async comm_upload(batch_id) {
            
        this.comm_canSub = false;
        var myArr = this.comm_fileArray;
        var vm = this;

        // upload files async and wait for all uploads to complete
        await Promise.all(myArr.map(async (file, index) => {
          var form_Data = new FormData();
          form_Data.append('file', file);
          form_Data.append('batch_id', batch_id);
          form_Data.append("batch_type", "transmittal");

          var config = {
            headers: { "Content-Type": "multipart/form-data" },
            onUploadProgress: function(e) {
   
              if (e.lengthComputable) {
                var rate = e.loaded / e.total;
                console.log(rate);
                vm.comm_upload_progress = rate;
              }
            }
          };

          return await axios.post('api/uploadFile_gcp', form_Data, config);
        }));

        this.comm_canSub = true;
        this.getRecords();

        Swal.fire({
          text: "Uploading Finished!!",
          icon: 'success',
          confirmButtonText: 'OK'
        })

      },



    approve: function() {

      let _this = this;

            if (this.ins_title.trim() == '') {
              Swal.fire({
                text: 'Please Encode Transmittal  Name!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            // if (this.ins_project_id == 0) {
            //   Swal.fire({
            //     text: 'Please Select Project or Task!',
            //     icon: 'warning',
            //     confirmButtonText: 'OK'
            //   })
                
            //     //$(window).scrollTop(0);
            //     return;
            // }


            _this.submit = true;
            var form_Data = new FormData();

            var token = localStorage.getItem("token");

            form_Data.append("jwt", token);
            form_Data.append('title', _this.ins_title);
            form_Data.append('project_id', _this.ins_project_id);
            
            if(this.type == 'task')
              form_Data.append('kind', _this.kind);

            form_Data.append("generate", _this.generate);
            form_Data.append("first_line", '');
            form_Data.append("second_line", '');
            form_Data.append("project_category", '');
            form_Data.append("quotation_no", '');
            form_Data.append("quotation_date", '');
            form_Data.append("prepare_for_first_line", '');
            form_Data.append("prepare_for_second_line", '');
            form_Data.append("prepare_for_third_line", '');
            form_Data.append("prepare_by_first_line", '');
            form_Data.append("prepare_by_second_line", '');

            form_Data.append("footer_first_line", '');
            form_Data.append("footer_second_line", '');
            form_Data.append("add_term", 'y');

            form_Data.append("followup", _this.followup);

            form_Data.append("pages", JSON.stringify([]));

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/transmittal_insert',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    var id = response.data.id;

                    window.open('transmittal?id=' + id, '_blank');
                    
                    _this.clear();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
      },

      clear: function() {
        this.ins_title = '';
        this.ins_project_id = 0;
        this.type = 'project';
        
        document.getElementById('insert_dialog').classList.toggle("show");
        $('.list_function .new_project a.add').toggleClass('focus');

        this.is_modifying = false;

        this.getRecords();
        

      },

      cancel_filters:function() {
        document.getElementById('filter_dialog').classList.toggle("show");
        $('.list_function .new_project a.filter').toggleClass('focus');
        this.is_modifying = false;
      },

      cancel_orders:function() {
        document.getElementById('order_dialog').classList.toggle("show");
        $('.list_function .new_project a.sort').toggleClass('focus');
        this.is_modifying = false;
      },

      clear_orders: function() {
        this.od_opt1 = '';
        this.od_ord1 = '';
        this.od_opt2 = '';
        this.od_ord2 = '';
        this.page = 1;

        let _this = this;

        window.location.href =
          "tracking_item_query?" +
          "tid=" +
          _this.fil_tracking +
          "&fpi=" +
          _this.fil_prod_id +
          "&fpc=" +
          _this.fil_prod_code +
          "&fp=" +
          _this.fil_pool +
          "&fpr=" +
          _this.fil_project_related +
          "&loc=" +
          _this.fil_location +
          "&sap=" +
          _this.fil_sample +
          "&fs=" +
          _this.fil_status +
          "&fo=" +
          _this.fil_order +
          "&fdf=" +
          _this.fil_date_from +
          "&fdt=" +
          _this.fil_date_to +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page + 
          "&page=" +
          _this.page +
          "&size=" +
          _this.perPage;
      },

      clear_filters: function() {
        this.fil_tracking = '';
        this.fil_prod_id = '';
        this.fil_prod_code = '';
        this.fil_pool = '';
        this.fil_project_related = '';
        this.fil_location = '';
        this.fil_sample = '';
        this.fil_status = '';
        this.fil_order = '';
        this.fil_date_from = '';
        this.fil_date_to = '';

        this.page = 1;

        let _this = this;

        window.location.href =
          "tracking_item_query?" +
          "tid=" +
          _this.fil_tracking +
          "&fpi=" +
          _this.fil_prod_id +
          "&fpc=" +
          _this.fil_prod_code +
          "&fp=" +
          _this.fil_pool +
          "&fpr=" +
          _this.fil_project_related +
          "&loc=" +
          _this.fil_location +
          "&sap=" +
          _this.fil_sample +
          "&fs=" +
          _this.fil_status +
          "&fo=" +
          _this.fil_order +
          "&fdf=" +
          _this.fil_date_from +
          "&fdt=" +
          _this.fil_date_to +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page + 
          "&page=" +
          _this.page +
          "&size=" +
          _this.perPage;
      },

      apply_filters: function(pg) {
        let _this = this;

        if(pg != undefined) this.page = pg;

        window.location.href =
          "tracking_item_query?" +
          "tid=" +
          _this.fil_tracking +
          "&fpi=" +
          _this.fil_prod_id +
          "&fpc=" +
          _this.fil_prod_code +
          "&fp=" +
          _this.fil_pool +
          "&fpr=" +
          _this.fil_project_related +
          "&loc=" +
          _this.fil_location +
          "&sap=" +
          _this.fil_sample +
          "&fs=" +
          _this.fil_status +
          "&fo=" +
          _this.fil_order +
          "&fdf=" +
          _this.fil_date_from +
          "&fdt=" +
          _this.fil_date_to +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page + 
          "&page=" +
          _this.page +
          "&size=" +
          _this.perPage;
      },

      apply_orders: function() {
        let _this = this;

        window.location.href =
          "tracking_item_query?" +
          "tid=" +
          _this.fil_tracking +
          "&fpi=" +
          _this.fil_prod_id +
          "&fpc=" +
          _this.fil_prod_code +
          "&fp=" +
          _this.fil_pool +
          "&fpr=" +
          _this.fil_project_related +
          "&loc=" +
          _this.fil_location +
          "&sap=" +
          _this.fil_sample +
          "&fs=" +
          _this.fil_status +
          "&fo=" +
          _this.fil_order +
          "&fdf=" +
          _this.fil_date_from +
          "&fdt=" +
          _this.fil_date_to +
          "&op1=" +
          _this.od_opt1 +
          "&od1=" +
          _this.od_ord1 +
          "&op2=" +
          _this.od_opt2 +
          "&od2=" +
          _this.od_ord2 +
          "&pg=" +
          _this.page + 
          "&page=" +
          _this.page +
          "&size=" +
          _this.perPage;
      },

        shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
        },


  }
});