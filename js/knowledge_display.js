
var app = new Vue({
    el: '#app',
    data:{
        
        
        receive_records: [],
        record: {},
        
        
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
        perPage: 20000,

        // search
        colors: [
            "#FF000088",
            "#FF7F0088",
            "#FFFF0088",
            "#00FF0088",
            "#00FFFF88",
            "#0000FF88",
            "#8B00FF88",
        ],
        
        orders: [],
        order: {},
        temp_order: {},
        
    },
    
    created () {
        let _this = this;
        
        this.getRecords();
        
    },
    
    computed: {
 
        
    },
    
    mounted(){
        
    },
    
    watch: {
        
        
    },
    
    
    
    methods:{
        


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
            
            
        };
        
        
        let token = localStorage.getItem('accessToken');
        
        axios
        .get('api/knowledge_display', { params, headers: {"Authorization" : `Bearer ${token}`} })
        .then(
            (res) => {
                _this.receive_records = res.data;
                
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
        
        
    }
});