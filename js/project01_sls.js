
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

    fil_project_category : '',
    fil_client_type : '',
    fil_priority: '',
    fil_status : '',
    fil_stage : '',
    fil_creator : '',
    fil_keyword : '',
    fil_lower : '',
    fil_upper : '',

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
    statuses : {},
    stages : {},
    creators : {},

    submit : false,
    // paging
    page: 1,
    pg:0,
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

    total : 0,

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
            case "fpc":
              _this.fil_project_category = decodeURI(tmp[1]);
              break;
            case "fct":
              _this.fil_client_type = decodeURI(tmp[1]);
              break;
            case "fp":
              _this.fil_priority = decodeURI(tmp[1]);
              break;
            case "fs":
              _this.fil_status = decodeURI(tmp[1]);
              break;
            case "fcs":
              _this.fil_stage = decodeURI(tmp[1]);
              break;
            case "fpt":
              _this.fil_creator = decodeURI(tmp[1]);
              break;
            case "flo":
              _this.fil_lower = decodeURI(tmp[1]);
              break;
            case "fup":
              _this.fil_upper = decodeURI(tmp[1]);
              break;
            case "key":
              _this.fil_keyword = decodeURI(tmp[1]);
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
    this.getProjectCategorys();
    this.getClientTypes();
    this.getPrioritys();
    this.getStatuses();
    this.getStages();
    this.getCreators();
  },

  computed: {
    displayedPosts () {
      if(this.pg == 0)
        this.apply_filters();

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

    receive_records () {
        console.log('Vue watch receive_records');
        this.setPages();
      },
/*
    fil_project_category (value) {
        this.getRecords(value);
        },
    fil_client_type (value) {
        this.getRecords(value);
        },
    fil_priority (value) {
        this.getRecords(value);
        },
    fil_status (value) {
        this.getRecords(value);
        },
    fil_stage (value) {
        this.getRecords(value);
        },
    fil_creator (value) {
        this.getRecords(value);
        },
  */
  },



  methods:{

    setPages () {
          console.log('setPages');
          this.pages = [];
          
          let numberOfPages = Math.ceil(this.total / this.perPage);

          if(this.fil_keyword != '')
            numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

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
          
          if(this.fil_keyword != '')
            return this.receive_records.slice(from, to);
          else
            return  this.receive_records;
        },

    getRecords: function(keyword) {
      let _this = this;

      const params = {
                fpc: _this.fil_project_category,
                fct: _this.fil_client_type,
                fp: _this.fil_priority,
                fs: _this.fil_status,
                fcs: _this.fil_stage,
                fpt: _this.fil_creator,
                flo: _this.fil_lower,
                fup: _this.fil_upper,
                key: _this.fil_keyword,

                op1: _this.od_opt1,
                od1: _this.od_ord1,
                op2: _this.od_opt2,
                od2: _this.od_ord2,

                page: _this.page,
                size: _this.perPage,
            };

            this.total = 0;
    
          let token = localStorage.getItem('accessToken');
    
          if(this.fil_keyword != '')
          {
            axios
                .get('api/project01_sls_org', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.receive_records = res.data;

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
            }
              else
            {
              axios
              .get('api/project01_sls', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.receive_records = res.data;
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
            }
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

      getStages () {

          let _this = this;
    
          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/admin/project_stage', { headers: {"Authorization" : `Bearer ${token}`} })
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

      getCreators () {

        let _this = this;
  
        let token = localStorage.getItem('accessToken');
  
        axios
            .get('api/admin/project_creators', { headers: {"Authorization" : `Bearer ${token}`} })
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

    
    getLeaveCredit: function() {
      let _this = this;

      axios.get('api/ammend')
          .then(function(response) {
              console.log(response.data);
              _this.receive_records = response.data;


          })
          .catch(function(error) {
              console.log(error);
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



    approve: function() {

      let _this = this;

            if (this.project_name.trim() == '') {
              Swal.fire({
                text: 'Please enter Project Name!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.project_category.trim() == '') {
              Swal.fire({
                text: 'Please select Project Category!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.client_type.trim() == '') {
              Swal.fire({
                text: 'Please select Client Type!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

            if (this.priority.trim() == '') {
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

            form_Data.append('project_name', this.project_name);
            form_Data.append('project_category', this.project_category);
            form_Data.append('client_type', this.client_type);
            form_Data.append('priority', this.priority);
            form_Data.append('status', this.status);
            form_Data.append('reason', this.reason);
            form_Data.append('probability', this.probability);
            form_Data.append('special_note', this.special_note);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/project01_insert',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.clear();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
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
        
        document.getElementById('insert_dialog').classList.remove("show");

        this.receive_records = [];

        this.getRecords();
        

      },

      cancel_filters:function() {
        document.getElementById('filter_dialog').classList.remove("show");
      },

      cancel_orders:function() {
        document.getElementById('order_dialog').classList.remove("show");
      },

      clear_orders: function() {
        this.od_opt1 = '';
        this.od_ord1 = '';
        this.od_opt2 = '';
        this.od_ord2 = '';

        let _this = this;

        window.location.href =
          "project01_sls?" +
          "fpc=" +
          _this.fil_project_category +
          "&fct=" +
          _this.fil_client_type +
          "&fp=" +
          _this.fil_priority +
          "&fs=" +
          _this.fil_status +
          "&fcs=" +
          _this.fil_stage +
          "&fpt=" +
          _this.fil_creator +
          "&flo=" +
          _this.fil_lower +
          "&fup=" +
          _this.fil_upper +
          "&key=" +
          _this.fil_keyword +
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
        this.fil_project_category = '';
        this.fil_client_type = '';
        this.fil_priority = '';
        this.fil_status = '';
        this.fil_stage = '';
        this.fil_creator = '';
        this.fil_lower = '';
        this.fil_upper = '';
        this.fil_keyword = '';
        this.page = 1;

        let _this = this;

        window.location.href =
          "project01_sls?" +
          "fpc=" +
          _this.fil_project_category +
          "&fct=" +
          _this.fil_client_type +
          "&fp=" +
          _this.fil_priority +
          "&fs=" +
          _this.fil_status +
          "&fcs=" +
          _this.fil_stage +
          "&fpt=" +
          _this.fil_creator +
          "&flo=" +
          _this.fil_lower +
          "&fup=" +
          _this.fil_upper +
          "&key=" +
          _this.fil_keyword +
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

      apply_filters: function() {
        let _this = this;

        window.location.href =
          "project01_sls?" +
          "fpc=" +
          _this.fil_project_category +
          "&fct=" +
          _this.fil_client_type +
          "&fp=" +
          _this.fil_priority +
          "&fs=" +
          _this.fil_status +
          "&fcs=" +
          _this.fil_stage +
          "&fpt=" +
          _this.fil_creator +
          "&flo=" +
          _this.fil_lower +
          "&fup=" +
          _this.fil_upper +
          "&key=" +
          _this.fil_keyword +
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
          "project01_sls?" +
          "fpc=" +
          _this.fil_project_category +
          "&fct=" +
          _this.fil_client_type +
          "&fp=" +
          _this.fil_priority +
          "&fs=" +
          _this.fil_status +
          "&fcs=" +
          _this.fil_stage +
          "&fpt=" +
          _this.fil_creator +
          "&flo=" +
          _this.fil_lower +
          "&fup=" +
          _this.fil_upper +
          "&key=" +
          _this.fil_keyword +
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