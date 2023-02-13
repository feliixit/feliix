var app = new Vue({
    el: '#app',
    data:{
        id:0,
        
        receive_records: [],
        record: {},
        
        
        submit : false,
  
        
        inventory: [
            {name: '10', id: 10},
            {name: '25', id: 25},
            {name: '50', id: 50},
            {name: '100', id: 100},
            {name: 'All', id: 10000}
        ],
        perPage: 10,
        
        orders: [],
        order: {},
        temp_order: {},
        
        
        // search
        fil_title: '',
        fil_creator: [],
        fil_updater: [],

        fil_create_from: '',
        fil_create_to: '',
        fil_update_from: '',
        fil_update_to: '',

        // order
        od_opt1 : '',
        od_ord1 : '',
    
        od_opt2 : '',
        od_ord2 : '',
    
        // paging
        page: 1,
        pg:0,
        //perPage: 10,
        pages: [],

        pages_10: [],

        is_special: false,
        
        creators : [],
        updaters : [],
    },
    
    async created () {

    let _this = this;
    let uri = window.location.href.split("?");
    if (uri.length >= 2) {
      let vars = uri[1].split("&");

      let tmp = "";
      vars.forEach(async function(v) {
        tmp = v.split("=");
        if (tmp.length == 2) {
          switch (tmp[0]) {
            case "ft":
                _this.fil_title = decodeURI(tmp[1]);
                break;
            case "fc":
                _this.fil_creator = decodeURI(tmp[1]).split(",");
                break;
            case "fu":
                _this.fil_updater = decodeURI(tmp[1]).split(",");
                break;
            case "fcf":
              _this.fil_create_from = decodeURI(tmp[1]);
              break;
            case "fct":
              _this.fil_create_to = decodeURI(tmp[1]);
              break;
            case "fuf":
              _this.fil_update_from = decodeURI(tmp[1]);
              break;
            case "fut":
              _this.fil_update_to = decodeURI(tmp[1]);
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
              if(_this.page < 1)
                _this.page = 1;
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

    },
    
    computed: {
        displayedPosts () {
            this.setPages();
            return this.paginate(this.receive_records);
        },
        
    },
    
    mounted(){
        
    },
    
    watch: {
        
        receive_records () {
            console.log('Vue watch receive_records');
            this.setPages();
          },
    },
    
    
    
    methods:{
   
        
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
            
            console.log(item);
        },
        
        
        deleteRow: function(item){
            let _id = item['id'];
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
                    
                    let me = _this;
                    
                    token = localStorage.getItem('token');
                    var form_Data = new FormData();
                    form_Data.append('jwt', token);
                    
                    form_Data.append('id', _id);
                    
                    //DELETE table_name WHERE ID=id;
                    $.ajax({
                        url: "api/knowledge_delete",
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
                            me.clear();
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
                    form_Data.append('id', item['id']);
                    
                    axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${token}`
                        },
                        url: 'api/quotation_edit_row',
                        data: form_Data
                    })
                    .then(function(response) {
                        //handle success
                        console.log(response)
                        
                        _this.clear();
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
          
                    if(this.fil_keyword != '')
                      numberOfPages = Math.ceil(this.receive_records.length / this.perPage);
          
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
                    
                    return  this.receive_records.slice(from_d, to_d);
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
                
                edit_load:function(){
                    this.order = JSON.parse(JSON.stringify(this.temp_order));
                },
                
                edit_clear:function(){
                    
                    this.order = {};
                },
                
                edit_save: function(){
                    
                    
                    let _this = this;
                    
                    
                    _this.submit = true;
                    var form_Data = new FormData();
                    
                    var token = localStorage.getItem("token");
                    
                    form_Data.append("jwt", token);
                    form_Data.append('status', _this.order.status);
                    form_Data.append('iq_name', _this.order.iq_name);
                    form_Data.append('id', _this.order.id);
                    
                    
                    axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            Authorization: `Bearer ${token}`
                        },
                        url: 'api/knowledge_mgt_edit',
                        data: form_Data
                    })
                    .then(function(response) {
                        //handle success
                        //this.$forceUpdate();
                        _this.getRecords();
                        _this.cancel_edit();
                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    });
                    
                },
                
                getAllRecords: function() {
                    let _this = this;
                    
                    const params = {};
                    
                    let token = localStorage.getItem('accessToken');
                    
                    axios
                    .get('api/knowledge_mgt_all', { params, headers: {"Authorization" : `Bearer ${token}`} })
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
                    
                    getRecords: function(keyword) {
                        let _this = this;
                        
                        const params = {
                            ft: _this.fil_title,
                            fc: _this.fil_creator.join(','),
                            fu: _this.fil_updater.join(','),
                            fcf: _this.fil_create_from,
                            fct: _this.fil_create_to,
                            fuf: _this.fil_update_from,
                            fut: _this.fil_update_to,
                     
            
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
                        .get('api/knowledge_mgt', { params, headers: {"Authorization" : `Bearer ${token}`} })
                        .then(
                            (res) => {
                                _this.receive_records = res.data;
                                _this.total = _this.receive_records[0].cnt;

                                if(_this.pg !== 0)
                                { 
                                    _this.page = _this.pg;
                                    _this.setPages();

                                    $('#creator').selectpicker('refresh');
                                    $('#updater').selectpicker('refresh');
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
                        
                        
                        
                        approve: function() {
                            
                            let _this = this;
                            
                            if (this.ins_title.trim() == '') {
                                Swal.fire({
                                    text: 'Please Encode Quotation Name!',
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                })
                                
                                //$(window).scrollTop(0);
                                return;
                            }
                            
                            if (this.ins_project_id == 0) {
                                Swal.fire({
                                    text: 'Please Select Project or Task!',
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
                            form_Data.append('title', _this.ins_title);
                            form_Data.append('project_id', _this.ins_project_id);
                            
                            if(this.type == 'task')
                            form_Data.append('kind', _this.kind);
                            
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
                                _this.clear();
                            })
                            .catch(function(response) {
                                //handle error
                                console.log(response)
                            });
                        },
                        
                        clear: function() {
                            this.getRecords();
                            
                            
                        },
                        
                        cancel_edit:function() {
                            document.getElementById('edit_dialog').classList.remove("show");
                            this.order = {};
                            this.temp_order = {};
                        },
                        
                        cancel_filters:function() {
                            document.getElementById('filter_dialog').classList.remove("show");
                            this.is_modifying = false;
                        },
                        
                        cancel_orders:function() {
                            document.getElementById('order_dialog').classList.remove("show");
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
                            "knowledge_mgt?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fu=" +
                            _this.fil_updater +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fuf=" +
                            _this.fil_update_from +
                            "&fut=" +
                            _this.fil_update_to +
                            "&op1=" +
                            _this.od_opt1 +
                            "&od1=" +
                            _this.od_ord1 +
                            "&op2=" +
                            _this.od_opt2 +
                            "&od2=" +
                            _this.od_ord2 +
                            "&page=" +
                            _this.page;
                        },
                        
                        clear_filters: function() {
                            this.fil_title = '';
                            this.fil_creator = [];
                            this.fil_updater = [];
                            this.fil_create_from = '';
                            this.fil_create_to = '';
                            this.fil_update_from = '';
                            this.fil_update_to = '';
                            
                            this.page = 1;
                            
                            let _this = this;
                            
                            window.location.href =
                            "knowledge_mgt?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fu=" +
                            _this.fil_updater +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fuf=" +
                            _this.fil_update_from +
                            "&fut=" +
                            _this.fil_update_to +
                            "&op1=" +
                            _this.od_opt1 +
                            "&od1=" +
                            _this.od_ord1 +
                            "&op2=" +
                            _this.od_opt2 +
                            "&od2=" +
                            _this.od_ord2 +
                            "&page=" +
                            _this.page;
                        },
                        
                        apply_filters: function() {
                            let _this = this;
                            
                            window.location.href =
                            "knowledge_mgt?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fu=" +
                            _this.fil_updater +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fuf=" +
                            _this.fil_update_from +
                            "&fut=" +
                            _this.fil_update_to +
                            "&op1=" +
                            _this.od_opt1 +
                            "&od1=" +
                            _this.od_ord1 +
                            "&op2=" +
                            _this.od_opt2 +
                            "&od2=" +
                            _this.od_ord2 +
                            "&page=" +
                            _this.page;
                        },
                        
                        apply_orders: function() {
                            let _this = this;
                            
                            window.location.href =
                            "knowledge_mgt?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fu=" +
                            _this.fil_updater +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fuf=" +
                            _this.fil_update_from +
                            "&fut=" +
                            _this.fil_update_to +
                            "&op1=" +
                            _this.od_opt1 +
                            "&od1=" +
                            _this.od_ord1 +
                            "&op2=" +
                            _this.od_opt2 +
                            "&od2=" +
                            _this.od_ord2 +
                            "&page=" +
                            _this.page;
                        },
                        
                        shallowCopy(obj) {
                            console.log("shallowCopy");
                            var result = {};
                            for (var i in obj) {
                                result[i] = obj[i];
                            }
                            return result;
                        },

                        cancel_filters:function() {
                            document.getElementById('filter_dialog').classList.remove("show");
                            this.is_modifying = false;
                          },
                    
                          cancel_orders:function() {
                            document.getElementById('order_dialog').classList.remove("show");
                            this.is_modifying = false;
                          },

                    }
                });