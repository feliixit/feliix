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
    project_relative: {},
    project_approves: {},
    project_quotes: {},
    project_action_detials: {},
    project_specials: {},

    project_groups: {},
    categorys : {},
    client_types : {},
    priorities : {},
    statuses : {},
    stages : {},
    users : {},
    users_del : {},

    uid:0,
    org_uid:0,

    title:'',

    stage_id_to_edit:0,

    baseURL:'https://storage.cloud.google.com/',

    pageURL:'https://feliix.myvnc.com/',
  

    project_name: '',
    project_group: '',
    group_id:0,
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
    client:'',
    edit_reason:'',


    special:'',

    // extend
    designer:'',
    type:'',
    scope:'',
    scope_other:'',
    office_location:'',
    background_client:'',
    background_project:'',
    contractor:'',

    party_contactor : [],
    key_person : [],

    // + sign
    stage_sequence:'',
    project_stage:'',
    stage_status:'',
    stage_title: '',

    // Change Project Status
    project_status:'',
    project_status_edit:'',
    project_status_reason:'',

    // Edit Project Info
    edit_group:'0',
    edit_category:'',
    edit_client_type:'',
    edit_priority: '',
    edit_contactor:'',
    edit_contact_number:'',
    edit_client:'',
    edit_location:'',
    edit_project_reason:'',
    edit_edit_reason:'',

    edit_special:'',

    uid_pic1:0,
    uid_pic2:0,

    org_pic1:0,
    org_pic2:0,

    pic1:'',
    pic2:'',

    edit_target_date:'',
    target_date:'',

    edit_real_date:'',
    real_date:'',

    // extend
    edit_project_name:'',
    edit_designer:'',
    edit_type:'',
    edit_scope:'',
    edit_scope_other:'',
    edit_office_location:'',
    edit_background_client:'',
    edit_background_project:'',
    edit_contractor:'',

    edit_send_mail:'',

    edit_party_contactor : [],
    edit_key_person : [],

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
    stage_edit_title:'',

    // Downpayment Proof
    prof_remark:'',
    prof_fileArray: [],
    prof_canSub: true,
    prof_finish: false,

    // approve plan
    approve_remark:'',
    approve_fileArray: [],
    approve_canSub: true,
    approve_finish: false,

    // Downpayment Quotation
    quote_remark:'',
    quote_fileArray: [],
    quote_canSub: true,
    quote_finish: false,

    // expense record
    expense_record: [],

    // Quotation name
    quotation_name: '',

        // special agreement
        special_remark:'',
        special_fileArray: [],
        special_canSub: true,
        special_finish: false,

    verified_quotation: false,

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

    // 20220407
    quotation_type:'n',

    price_record: [],
    price_record_total: -1,

    project_orders: [],

    // transmittal
    transmittal: [],

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
        _this.getProjectQuotation(_this.project_id);
        _this.getProjectApprove(_this.project_id);
        _this.getProjectActionDetails(_this.project_id);
        _this.getProjectSpecial(_this.project_id);
        _this.getUsers();
        _this.getUsersDeleted(_this.project_id);
        _this.getFileManagement(_this.project_id);
        _this.getProjectInfo(_this.project_id);
        _this.getKeyPerson(_this.project_id);
        _this.getPartyContactor(_this.project_id);
        _this.getExpenseRecord(_this.project_id);
        
        _this.getOrderRecord(_this.project_id);
        _this.getTransmittal(_this.project_id);
      });
    }

    this.getProjectGroups();
    this.getProjectCategorys();
    this.getClientTypes();
    this.getPrioritys();
    this.getStatuses();

    this.getUserName();
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

    edit_scope() {
      if(this.edit_scope !== "Other")
        this.edit_scope_other = "";
    },

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

    quote_fileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.quote_fileArray.length) {
          this.quote_finish = true;
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.quote_finish = true;
            _this.getProjectActionDetails(_this.project_id);

          });;
          this.quote_clear();
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

    approve_fileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.approve_fileArray.length) {
          this.approve_finish = true;
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.approve_finish = true;
            _this.getProjectActionDetails(_this.project_id);

          });;
          this.approve_clear();
        }
      },
      deep: true
    },

    special_fileArray: {
      handler(newValue, oldValue) {
        var _this = this;
        console.log(newValue);
        var finish = newValue.find(function(currentValue, index) {
          return currentValue.progress != 1;
        });
        if (finish === undefined && this.special_fileArray.length) {
          this.special_finish = true;
          Swal.fire({
            text: "upload finished",
            type: "success",
            duration: 1 * 1000,
            customClass: "message-box",
            iconClass: "message-icon"
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            _this.special_finish = true;
            _this.getProjectActionDetails(_this.project_id);

          });;
          this.special_clear();
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

    open_prob() {
      if(this.project_status !== 'Completed')
        {
          document.getElementById('prob_dialog').classList.add("show");
            document.getElementById('status_fn4').classList.add("focus");
        }
    },

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

    approve_deleteFile(index) {
      this.approve_fileArray.splice(index, 1);
      var fileTarget = this.$refs.approve_file;
      fileTarget.value = "";
    },

    special_deleteFile(index) {
      this.special_fileArray.splice(index, 1);
      var fileTarget = this.$refs.special_file;
      fileTarget.value = "";
    },

    quote_deleteFile(index) {
      this.quote_fileArray.splice(index, 1);
      var fileTarget = this.$refs.quote_file;
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

    approve_changeFile() {
      var fileTarget = this.$refs.approve_file;

      for (i = 0; i < fileTarget.files.length; i++) {
          // remove duplicate
          if (
            this.approve_fileArray.indexOf(fileTarget.files[i]) == -1 ||
            this.approve_fileArray.length == 0
          ) {
            var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
            this.approve_fileArray.push(fileItem);
          }else{
            fileTarget.value = "";
          }
        }
    },

    special_changeFile() {
      var fileTarget = this.$refs.special_file;

      for (i = 0; i < fileTarget.files.length; i++) {
          // remove duplicate
          if (
            this.special_fileArray.indexOf(fileTarget.files[i]) == -1 ||
            this.special_fileArray.length == 0
          ) {
            var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
            this.special_fileArray.push(fileItem);
          }else{
            fileTarget.value = "";
          }
        }
    },

    quote_changeFile() {
      var fileTarget = this.$refs.quote_file;

      for (i = 0; i < fileTarget.files.length; i++) {
          // remove duplicate
          if (
            this.quote_fileArray.indexOf(fileTarget.files[i]) == -1 ||
            this.quote_fileArray.length == 0
          ) {
            var fileItem = Object.assign(fileTarget.files[i], { progress: 0 });
            this.quote_fileArray.push(fileItem);
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

      getOrderRecord: function(keyword) {
        let _this = this;
  
        if(keyword == 0)
          return;
  
        const params = {
                pid : keyword,
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project_order', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.project_orders = res.data;
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },

      export_petty: function(id) {
   
        let _this = this;
        var form_Data = new FormData();
  
        form_Data.append('id', id)
       
        const filename = "leave";
  
        const token = sessionStorage.getItem('token');
  
        axios({
                method: 'post',
                url: 'expense_type2_application',
                data: form_Data,
                responseType: 'blob', // important
            })
            .then(function(response) {
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;
                 
                    link.setAttribute('download', 'Expense Application Voucher_' + _this.record['request_no'] + '.docx');
                 
                  document.body.appendChild(link);
                  link.click();
  
            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });
    },

    getPriceRecord: function(keyword) {
      let _this = this;

       var form_Data = new FormData();

       form_Data.append('action', 9);
        form_Data.append('project_name', keyword);
      
        const token = sessionStorage.getItem('token');

        axios({
                method: 'post',
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                },
                url: 'api/add_or_edit_price_record',
                data: form_Data
            })
            .then(function(response) {
                //handle success
                //this.$forceUpdate();
                _this.price_record = response.data;
                // sum price_record cash_out - cash_in
                var price_record_cash_out = 0;
                var price_record_cash_in = 0;
                var price_record_cash_balance = 0;
                for (let index = 0; index < _this.price_record.length; index++) {
                    price_record_cash_out += parseFloat(_this.price_record[index].cash_out);
                    price_record_cash_in += parseFloat(_this.price_record[index].cash_in);
                }
                price_record_cash_balance = price_record_cash_out - price_record_cash_in;
                // _this.price_record_total = price_record_cash_out;
            
            })
            .catch(function(response) {
                //handle error
                console.log(response)
            });
      },


      getExpenseRecord: function(keyword) {
        let _this = this;
  
        if(keyword == 0)
          return;
  
        const params = {
                pid : keyword,
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project_expense', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.price_record_total = 0;
                    _this.expense_record = res.data;
                    for(let i = 0; i < _this.expense_record.length; i++) {
                      if(_this.expense_record[i].status == 9)
                      {
                        if(_this.expense_record[i].request_type == 1)
                        _this.price_record_total += parseFloat(_this.expense_record[i].amount_verified);
                        if(_this.expense_record[i].request_type == 2)
                        _this.price_record_total += parseFloat(_this.expense_record[i].amount_applied);
                      }
                    }
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },

        getTransmittal: function(keyword) {
          let _this = this;
    
          if(keyword == 0)
            return;
    
          const params = {
                  pid : keyword,
                };
    
              let token = localStorage.getItem('accessToken');
        
              axios
                  .get('api/project_transmittal', { params, headers: {"Authorization" : `Bearer ${token}`} })
                  .then(
                  (res) => {

                      _this.transmittal = res.data;

                  },
                  (err) => {
                      alert(err.response);
                  },
                  )
                  .finally(() => {
                      
                  });
          },

      apply_for_expense : function() {
        location.href = "apply_for_expense?prj_id=" + this.project_id;
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
/*
      getProjectQuotation: function(keyword) {
        let _this = this;
  
        if(keyword == 0)
          return;
  
        const params = {
                pid : keyword,
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project_quotation_approval', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    if(res.data.length > 0)
                      _this.verified_quotation = res.data[0].status;
  
                     // _this.getStages(_this.verified_downpayment);
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },
*/
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

      getProjectRelative: function(pid, group_id) {
        let _this = this;
  
        if(pid == 0 || group_id == 0)
        {
          this.project_relative = [];
          return;
        }

  
        const params = {
                pid : pid,
                group_id: group_id,
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project_relative', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.project_relative = res.data;
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },

      getProjectQuotation: function(keyword) {
        let _this = this;
  
        if(keyword == 0)
          return;
  
        const params = {
                pid : keyword,
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project_quotation', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.project_quotes = res.data;
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },

        getProjectApprove: function(keyword) {
          let _this = this;
    
          if(keyword == 0)
            return;
    
          const params = {
                  pid : keyword,
                };
    
              let token = localStorage.getItem('accessToken');
        
              axios
                  .get('api/project_approve_get', { params, headers: {"Authorization" : `Bearer ${token}`} })
                  .then(
                  (res) => {
                      _this.project_approves = res.data;
                  },
                  (err) => {
                      alert(err.response);
                  },
                  )
                  .finally(() => {
                      
                  });
          },

        getProjectSpecial: function(keyword) {
          let _this = this;
    
          if(keyword == 0)
            return;
    
          const params = {
                  pid : keyword,
                };
    
              let token = localStorage.getItem('accessToken');
        
              axios
                  .get('api/project_special_agreement_get', { params, headers: {"Authorization" : `Bearer ${token}`} })
                  .then(
                  (res) => {
                      _this.project_specials = res.data;
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

             _this.getPriceRecord(_this.projectname);
            },
            (err) => {
              alert(err.response);
            },
          )
          .finally(() => {
  
          });
      },

      getKeyPerson: function(keyword) {
        let _this = this;
  
        if(keyword == 0)
          return;
  
        const params = {
                pid : keyword,
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project_key_person', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.key_person = res.data;
                    _this.edit_key_person = res.data;
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },

        getPartyContactor: function(keyword) {
          let _this = this;
    
          if(keyword == 0)
            return;
    
          const params = {
                  pid : keyword,
                };
    
              let token = localStorage.getItem('accessToken');
        
              axios
                  .get('api/project_party_contactor', { params, headers: {"Authorization" : `Bearer ${token}`} })
                  .then(
                  (res) => {
                      _this.party_contactor = res.data;
                      _this.edit_party_contactor = res.data;
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
                  _this.project_name = res.data[0].project_name;
                  _this.project_group = res.data[0].project_group;
                  _this.category = res.data[0].category;
                  _this.client_type = res.data[0].client_type;
                  _this.priority = res.data[0].priority;
                  _this.uid = res.data[0].uid;
                  _this.org_uid = res.data[0].uid;
                  _this.username = res.data[0].username;
                  _this.stage = res.data[0].stage;
                  _this.project_status = res.data[0].project_status;

                  _this.group_id = res.data[0].group_id;
                  _this.category_id = res.data[0].category_id;
                  _this.client_type_id = res.data[0].client_type_id;
                  _this.priority_id = res.data[0].priority_id;
                  _this.contactor = res.data[0].contactor;
                  _this.location = res.data[0].location;
                  _this.contact_number = res.data[0].contact_number;
                  _this.client = res.data[0].client;
                  _this.edit_reason = res.data[0].edit_reason;

                  _this.special = res.data[0].special;

                  _this.edit_project_name = res.data[0].project_name;
                  _this.edit_group = res.data[0].group_id;
                  _this.edit_category = res.data[0].category_id;
                  _this.edit_client_type = res.data[0].client_type_id;
                  _this.edit_priority = res.data[0].priority_id;
             
                  _this.edit_contactor = res.data[0].contactor;
                  _this.edit_location = res.data[0].location;
                  _this.edit_contact_number = res.data[0].contact_number;
                  _this.edit_client = res.data[0].client;
                  _this.edit_edit_reason = res.data[0].edit_reason;

                  _this.edit_special = res.data[0].special;

                  // extend
                  _this.designer = res.data[0].designer;
                  _this.type = res.data[0].type;
                  _this.scope = res.data[0].scope;
                  _this.scope_other = res.data[0].scope_other;
                  _this.office_location = res.data[0].office_location;
                  _this.background_client = res.data[0].background_client;
                  _this.background_project = res.data[0].background_project;
                  _this.contractor = res.data[0].contractor;

                  _this.edit_designer = res.data[0].designer;
                  _this.edit_type = res.data[0].type;
                  _this.edit_scope = res.data[0].scope;
                  _this.edit_scope_other = res.data[0].scope_other;
                  _this.edit_office_location = res.data[0].office_location;
                  _this.edit_background_client = res.data[0].background_client;
                  _this.edit_background_project = res.data[0].background_project;
                  _this.edit_contractor = res.data[0].contractor;
                  _this.edit_send_mail = res.data[0].send_mail;

                  _this.created_at = res.data[0].created_at;
                  _this.end_at = res.data[0].updated_at;

                  _this.getProjectRelative(keyword, _this.group_id);

                  _this.pic1 = res.data[0].pic1;
                  _this.pic2 = res.data[0].pic2;
                  _this.uid_pic1 = res.data[0].uid_pic1;
                  _this.uid_pic2 = res.data[0].uid_pic2;

                  _this.org_pic1 = res.data[0].uid_pic1;
                  _this.org_pic2 = res.data[0].uid_pic2;

                  _this.target_date = res.data[0].target_date;
                  _this.real_date = res.data[0].real_date;

                  _this.edit_target_date = res.data[0].target_date;
                  _this.edit_real_date = res.data[0].real_date;

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

      getProjectGroups () {

        let _this = this;
  
        let token = localStorage.getItem('accessToken');
  
        axios
            .get('api/project_grouping', { headers: {"Authorization" : `Bearer ${token}`} })
            .then(
            (res) => {
                _this.project_groups = res.data;
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

          this.stage_edit_title = this.record.title;
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

      getUsersDeleted (id) {

        let _this = this;
  
        let token = localStorage.getItem('accessToken');

        const params = {
          pid : id,
         
        };
  
        axios
            .get('api/project02_user_with_deleted', { params, headers: {"Authorization" : `Bearer ${token}`} })
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
            _this.title = response.data.title.toLowerCase();

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
        this.special = '';
        
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

        quotation_add() {
          let _this = this;

          if (this.quotation_name.trim() == '') {
            Swal.fire({
              text: 'Please Encode Quotation Name!',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
              
              //$(window).scrollTop(0);
              return;
          }

          _this.submit = true;
            var form_Data = new FormData();

            var token = localStorage.getItem("token");

            form_Data.append("jwt", token);
            form_Data.append('title', _this.quotation_name.trim());
            form_Data.append('project_id', _this.project_id);

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

            form_Data.append("pages", JSON.stringify([]));

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/quotation_insert',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.quotation_clear();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        quotation_clear() {

          this.quotation_name = '';
       

          //this.getProjectDetail(this.project_id);
          this.getProjectQuotation(this.project_id);
          this.canSub = true;
          
          document.getElementById('dlg_fn7').classList.remove("show");
          document.getElementById('a_fn7').classList.remove("focus");
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

        approve_clear() {

          this.approve_remark = '';
          this.approve_fileArray = [];
          this.$refs.approve_file.value = '';

          //this.getProjectDetail(this.project_id);
          this.approve_canSub = true;

          this.getProjectApprove(this.approve_id);
          
          document.getElementById('approve_dialog').classList.remove("show");
          document.getElementById('status_fn8').classList.remove("focus");
      },

        special_clear() {

          this.special_remark = '';
          this.special_fileArray = [];
          this.$refs.special_file.value = '';

          //this.getProjectDetail(this.project_id);
          this.special_canSub = true;

          this.getProjectSpecial(this.project_id);
          
          document.getElementById('special_dialog').classList.remove("show");
          document.getElementById('status_fn10').classList.remove("focus");
      },

        quote_clear() {

          this.quote_remark = '';
          this.quote_fileArray = [];
          this.$refs.quote_file.value = '';

          //this.getProjectDetail(this.project_id);
          this.quote_canSub = true;

          this.getProjectQuotation(this.project_id);
          
          document.getElementById('dlg_fn7').classList.remove("show");
          document.getElementById('a_fn7').classList.remove("focus");
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
            this.edit_group = this.group_id;
            this.edit_category = this.category_id;
            this.edit_client_type = this.client_type_id;
            this.edit_priority = this.priority_id;
            this.edit_contactor = this.contactor;
            this.edit_location = this.location;
            this.edit_contact_number = this.contact_number;
            this.edit_client = this.client;

            this.edit_special = this.special;

            // extend
            this.edit_designer = this.designer;
            this.edit_type = this.type;
            this.edit_scope = this.scope;
            this.edit_scope_other = this.scope_other;
            this.edit_office_location = this.office_location;
            this.edit_background_client = this.background_client;
            this.edit_background_project = this.background_project;
            this.edit_contractor = this.contractor;
            this.edit_send_mail = this.send_mail;

            this.edit_key_person = this.key_person;
            this.edit_party_contactor = this.party_contactor;

            this.uid_pic1 = this.org_uid_pic1;
            this.uid_pic2 = this.org_uid_pic2;

            this.edit_target_date = this.target_date;
            this.edit_real_date = this.real_date;
            
            document.getElementById('project_dialog').classList.remove("show");
            document.getElementById('project_fn2').classList.remove("focus");
        },

        stage_clear() {
            this.stage_sequence = '';
            this.project_stage = '';
            this.stage_status = '';
            this.stage_title = '';
            
            document.getElementById('stage_dialog').classList.remove("show");
            document.getElementById('stage_fn1').classList.remove("focus");

            this.receive_stage_records = [];

            this.getRecordsStage(this.project_id);
        },

        edit_stage_clear() {

            this.record = {};
            this.stage_edit_reason = '';
            this.stage_edit_title = '';
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

        approve_create() {
          let _this = this;

          if (this.approve_remark.trim() == '') {
            Swal.fire({
              text: 'Please enter description!',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
              
              //$(window).scrollTop(0);
              return;
          }

          var token = localStorage.getItem("token");

          _this.submit = true;
          var form_Data = new FormData();

          form_Data.append('pid', this.project_id);
          form_Data.append('remark', this.approve_remark.trim());
          form_Data.append("jwt", token);

          for (var i = 0; i < this.approve_fileArray.length; i++) {
            let file = this.approve_fileArray[i];
            form_Data.append("files[" + i + "]", file);
          }

          axios({
                  method: 'post',
                  headers: {
                      'Content-Type': 'multipart/form-data',
                      Authorization: `Bearer ${token}`
                  },
                  url: 'api/project_approve',
                  data: form_Data
              })
              .then(function(response) {
                  Swal.fire({
                    text: response.data.message,
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                  _this.approve_clear();
                  _this.getProject(_this.project_id);
                  _this.getFileManagement(_this.project_id);
                  _this.getProjectApprove(_this.project_id);
              })
              .catch(function(response) {
                  //handle error
                  Swal.fire({
                    text: response.data,
                    icon: "info",
                    confirmButtonText: "OK",
                  });
              });
      },

        special_create() {
          let _this = this;

          if (this.special_remark.trim() == '') {
            Swal.fire({
              text: 'Description is required, please input the content.',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
              
              //$(window).scrollTop(0);
              return;
          }

          var token = localStorage.getItem("token");

          _this.submit = true;
          var form_Data = new FormData();

          form_Data.append('pid', this.project_id);
          form_Data.append('remark', this.special_remark.trim());
          form_Data.append("jwt", token);

          for (var i = 0; i < this.special_fileArray.length; i++) {
            let file = this.special_fileArray[i];
            form_Data.append("files[" + i + "]", file);
          }

          axios({
                  method: 'post',
                  headers: {
                      'Content-Type': 'multipart/form-data',
                      Authorization: `Bearer ${token}`
                  },
                  url: 'api/project_special',
                  data: form_Data
              })
              .then(function(response) {
                  Swal.fire({
                    text: response.data.message,
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                  _this.special_clear();
                  _this.getProject(_this.project_id);

                  _this.getProjectSpecial(_this.project_id);
              })
              .catch(function(response) {
                  //handle error
                  Swal.fire({
                    text: response.data,
                    icon: "info",
                    confirmButtonText: "OK",
                  });
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


        quote_create() {
          let _this = this;
          if (this.quote_remark.trim() == '') {
            Swal.fire({
              text: 'Please enter description!',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
              
              //$(window).scrollTop(0);
              return;
          }


          _this.submit = true;
          var form_Data = new FormData();

          form_Data.append('pid', this.project_id);
          form_Data.append('remark', this.quote_remark.trim());

          const token = sessionStorage.getItem('token');

          axios({
                  method: 'post',
                  headers: {
                      'Content-Type': 'multipart/form-data',
                      Authorization: `Bearer ${token}`
                  },
                  url: 'api/project_quote',
                  data: form_Data
              })
              .then(function(response) {
                  //handle success
                  if(response.data['batch_id'] != 0)
                  {
                      _this.quote_upload(response.data['batch_id']);
                  }
                  else
                  {
                    _this.quote_clear();
                
                  }

                  if(_this.quote_fileArray.length == 0)
                    _this.quote_clear();

                   // _this.sendNotifyEmail(response.data['batch_id']);

                  
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

        onChange_key_type() {
          var type = this.$refs.key_type;
          if(type.value === 'Other')
          {
            this.$refs.key_type_other.style.display = 'inline';
          }
          else
          {
            this.$refs.key_type_other.style.display = 'none';
          }
        },

        onChange_party_type() {
          var type = this.$refs.party_type;
          if(type.value === 'Other')
          {
            this.$refs.party_type_other.style.display = 'inline';
          }
          else
          {
            this.$refs.party_type_other.style.display = 'none';
          }
        },

        add_key_person() {
            var type = this.$refs.key_type;
            var name = this.$refs.key_name;
            var number = this.$refs.key_number;

            var s_type = '';

            if(type.value === 'Other')
              s_type = this.$refs.key_type_other.value;
            else
              s_type = type.value;

            obj = { type: s_type, name : name.value, number : number.value};
            this.edit_key_person.push(obj);
        },

        add_party_contactor() {
          var type = this.$refs.party_type;
          var name = this.$refs.party_name;
          var number = this.$refs.party_number;

          var s_type = '';

          if(type.value === 'Other')
            s_type = this.$refs.party_type_other.value;
          else
            s_type = type.value;

          obj = { type: s_type, name : name.value, number : number.value};
          this.edit_party_contactor.push(obj);
      },

        remove_key_person (index) {
          this.edit_key_person.splice(index, 1);
        },

        remove_party_contactor (index) {
          this.edit_party_contactor.splice(index, 1);
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

        quote_upload(batch_id) {
            
          this.quote_canSub = false;
          var myArr = this.quote_fileArray;
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
                    vm.$set(vm.quote_fileArray, index, myArr[index]);
                  } else {
                    myArr[index].progress = 0.99;
                    vm.$set(vm.quote_fileArray, index, myArr[index]);
                  }
                }
              }
            };
            var data = myArr[index];
            var myForm = new FormData();
            myForm.append('batch_type', 'quote');
            myForm.append('batch_id', batch_id);
            myForm.append("file", data);
   
            axios
              .post("api/uploadFile_gcp", myForm, config)
              .then(function(res) {
                if (res.data.code == 0) {
             
                  myArr[index].progress = 1;
                  vm.$set(vm.quote_fileArray, index, myArr[index]);
                  console.log(vm.quote_fileArray, index);
                } else {
                  alert(JSON.stringify(res.data));
                }
              })
              .catch(function(err) {
                console.log(err);
              });
          });

          this.quote_canSub = true;
        
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
            form_Data.append('stage_edit_title', this.stage_edit_title);
           
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

            if (this.edit_priority === 0 || this.edit_priority === undefined) {
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
            form_Data.append('edit_group', this.edit_group);
            form_Data.append('edit_category', this.edit_category);
            form_Data.append('edit_client_type', this.edit_client_type);
            form_Data.append('edit_special', this.edit_special);
            form_Data.append('edit_priority', this.edit_priority);
            form_Data.append('edit_contactor', this.edit_contactor);
            form_Data.append('creator', this.uid);
            form_Data.append('edit_location', this.edit_location);
            form_Data.append('edit_contact_number', this.edit_contact_number);
            form_Data.append('edit_client', this.edit_client);
            form_Data.append('edit_edit_reason', this.edit_edit_reason);

            // extend
            form_Data.append('edit_project_name', this.edit_project_name);
            form_Data.append('edit_designer', this.edit_designer);
            form_Data.append('edit_type', this.edit_type);
            form_Data.append('edit_scope', this.edit_scope);
            if(this.edit_scope !== "Other")
              this.edit_scope_other = "";
            form_Data.append('edit_scope_other', this.edit_scope_other);
            form_Data.append('edit_office_location', this.edit_office_location);
            form_Data.append('edit_background_client', this.edit_background_client);
            form_Data.append('edit_background_project', this.edit_background_project);
            form_Data.append('edit_contractor', this.edit_contractor);
            form_Data.append('edit_send_mail', this.edit_send_mail);

            form_Data.append('edit_key_person', JSON.stringify(this.edit_key_person));
            form_Data.append('edit_party_contactor', JSON.stringify(this.edit_party_contactor));

            form_Data.append('pic1', this.uid_pic1);
            form_Data.append('pic2', this.uid_pic2);

            form_Data.append('target_date', this.edit_target_date);
            form_Data.append('real_date', this.edit_real_date);

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
                    _this.group_id = _this.edit_group;
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
                text: 'Please enter reason!',
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
                    _this.getProjectProbs(_this.project_id);
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },


        
        update_status(record) {
          let _this = this;

          _this.submit = true;
          var form_Data = new FormData();

          form_Data.append('stage_id', record.id);
            form_Data.append('sequence', record.sequence);
            form_Data.append('project_stage_id', record.project_stage_id);
            form_Data.append('stages_status_id', record.stages_status_id);
            form_Data.append('stage_edit_reason', '');
            form_Data.append('stage_edit_title', record.title);

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
            form_Data.append('title', this.stage_title);

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