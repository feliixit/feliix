
var app = new Vue({
    el: '#app',
    data:{
        
        
        receive_records: [],
        record: {},
        
        colors: [
          "#FF000088",
          "#FF7F0088",
          "#FFFF0088",
          "#00FF0088",
          "#00FFFF88",
          "#0000FF88",
          "#8B00FF88",
      ],
        
        submit : false,
    
        inventory: [
            {name: '10', id: 10},
            {name: '25', id: 25},
            {name: '50', id: 50},
            {name: '100', id: 100},
            {name: 'All', id: 10000}
        ],
        perPage: 8,
        
        orders: [],
        order: {},
        temp_order: {},
        
        
        // search
        fil_title: '',
        fil_creator: [],
        fil_updater: [],
        fil_tag: [],

        fil_type:'',
        fil_watch: '',

        fil_dur_from: '',
        fil_dur_to: '',

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
            case "fta":
                _this.fil_tag = decodeURI(tmp[1]).split(",");
                break;
            case "fty":
                _this.fil_type = decodeURI(tmp[1]);
                break;
            case "fw":
                _this.fil_watch = decodeURI(tmp[1]);
                break;
            case "fcf":
              _this.fil_create_from = decodeURI(tmp[1]);
              break;
            case "fct":
              _this.fil_create_to = decodeURI(tmp[1]);
              break;
            case "fdf":
              _this.fil_dur_from = decodeURI(tmp[1]);
              break;
            case "fdt":
              _this.fil_dur_to = decodeURI(tmp[1]);
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
                            fta: _this.fil_tag.join(','),
                            fty: _this.fil_type,
                            fw: _this.fil_watch,
                            fcf: _this.fil_create_from,
                            fct: _this.fil_create_to,
                            fdf: _this.fil_dur_from,
                            fdt: _this.fil_dur_to,
                     
            
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
        .get('api/knowledge_display', { params, headers: {"Authorization" : `Bearer ${token}`} })
        .then(
            (res) => {
                _this.receive_records = res.data;
                                _this.total = _this.receive_records[0].cnt;

                                if(_this.pg !== 0)
                                { 
                                    _this.page = _this.pg;
                                    _this.setPages();

                                    $('#creator').selectpicker('refresh');
                                    $('#tag').selectpicker('refresh');
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
        
        clear: function() {
            this.getRecords();
            
            
        },
    
                        clear_orders: function() {
                            this.od_opt1 = '';
                            this.od_ord1 = '';
                            this.od_opt2 = '';
                            this.od_ord2 = '';

                            this.page = 1;
                            
                            let _this = this;
                            
                            window.location.href =
                            "knowledge_display?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fta=" +
                            _this.fil_tag +
                            "&fty=" +
                            _this.fil_type +
                            "&fw=" +
                            _this.fil_watch +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fdf=" +
                            _this.fil_dur_from +
                            "&fdt=" +
                            _this.fil_dur_to +
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
                            this.fil_tag = [];
                            this.fil_type = '';
                            this.fil_watch = '';
                            this.fil_create_from = '';
                            this.fil_create_to = '';
                            this.fil_dur_from = '';
                            this.fil_dur_to = '';
                            
                            this.page = 1;
                            
                            let _this = this;
                            
                            window.location.href =
                            "knowledge_display?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fta=" +
                            _this.fil_tag +
                            "&fty=" +
                            _this.fil_type +
                            "&fw=" +
                            _this.fil_watch +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fdf=" +
                            _this.fil_dur_from +
                            "&fdt=" +
                            _this.fil_dur_to +
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
                        
                        apply_filters: function(pg) {
                            let _this = this;

                            if(pg != undefined) this.page = pg;
                            
                            window.location.href =
                            "knowledge_display?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fta=" +
                            _this.fil_tag +
                            "&fty=" +
                            _this.fil_type +
                            "&fw=" +
                            _this.fil_watch +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fdf=" +
                            _this.fil_dur_from +
                            "&fdt=" +
                            _this.fil_dur_to +
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
                            "knowledge_display?" +
                            
                            "ft=" +
                            _this.fil_title +
                            "&fc=" +
                            _this.fil_creator +
                            "&fta=" +
                            _this.fil_tag +
                            "&fty=" +
                            _this.fil_type +
                            "&fw=" +
                            _this.fil_watch +
                            "&fcf=" +
                            _this.fil_create_from +
                            "&fct=" +
                            _this.fil_create_to +
                            "&fdf=" +
                            _this.fil_dur_from +
                            "&fdt=" +
                            _this.fil_dur_to +
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

        select: function(link) {

            window.open(link, '_blank');
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