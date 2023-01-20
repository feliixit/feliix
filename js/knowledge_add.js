var app = new Vue({
    el: "#app",
    data: {
        submit: false,
        
        knowledge: [],
        tags: [],
        users:[],
        
        edit_mode: false,
        
        name : '',
        
        // data
        title: "",
        category: [],
        access: [],
        type: "",
        file1: "",
        link: "",
        duration: "",
        watch: "read",
        description: "",
        
        url: null,
        
        submit: false,
        
        id : 0, 
        
    },
    
    async created() {
        let _this = this;

        //await _this.tags_data();
        //await _this.get_all_user();

        let uri = window.location.href.split("?");
        if (uri.length >= 2) {
            let vars = uri[1].split("&");
            
            let tmp = "";
            vars.forEach(async function(v) {
                tmp = v.split("=");
                if (tmp.length == 2) {
                    switch (tmp[0]) {
                        case "id":
                        _this.id = tmp[1];
                 
                        await _this.knowledge_add_get(_this.id);
                        console.log("start refresh");
                        $('#category').selectpicker('refresh');
                        $('#access').selectpicker('refresh');
                        break;
                    }
                }
            });
        }

        this.getUserName();
    },
    
    computed: {
        
    },
    
    mounted() {

        
        
    },
    
    watch: {
        
        
    },
    
    methods: {
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
        
        get_all_user: async function() {
            let _this = this;
            
            const params = {
                id: "",
            };
            
            let token = localStorage.getItem("accessToken");

            try {
                let res = await axios.get("api/get_all_users", {
                    params,
                    headers: { Authorization: `Bearer ${token}` },
                });
                
                _this.users = res.data;
                console.log("get_all_user");
            } catch (err) {
                console.log(err)

            }
        },
        
        tags_data: async function() {
            let _this = this;
            
            const params = {
                id: "",
            };
            
            let token = localStorage.getItem("accessToken");

            try {
                let res = await axios.get("api/tags_data", {
                    params,
                    headers: { Authorization: `Bearer ${token}` },
                });
                
                _this.tags = res.data;
                console.log("tags_data");
            } catch (err) {
                console.log(err)

            }
    
        },
        
        knowledge_add_get: async function(_id) {
            if(_id == 0){
                return;
            }

            let _this = this;
        
            const params = {
                id: _id,
            };
            
            let token = localStorage.getItem("accessToken");
            
            let res = await axios.get("api/knowledge_add", {
                params,
                headers: { Authorization: `Bearer ${token}` },
            });

            _this.knowledge = res.data;

            if(_this.knowledge.length > 0){
                _this.title = _this.knowledge[0].title;
                _this.category = _this.knowledge[0].category;
                _this.access = _this.knowledge[0].access;
                _this.type = _this.knowledge[0].type;
                _this.link = _this.knowledge[0].link;

                _this.duration = _this.knowledge[0].duration;
                _this.watch = _this.knowledge[0].watch;
                _this.description = _this.knowledge[0].description;
                _this.url = _this.knowledge[0].cover;

                _this.edit_mode = true;
                console.log("knowledge_add_get");
            }


        },
        
        
        onFileChange(e) {
            const file = e.target.files[0];
            this.url = URL.createObjectURL(file);
        },
        
        
        clear_photo() {
            this.url = null;
            
            document.getElementById('photo').value = "";
        },
        
        
        
        
        get_positions: function() {
            let _this = this;
            
            let token = localStorage.getItem("accessToken");
            
            axios
            .get("api/position_get", {
                headers: { Authorization: `Bearer ${token}` },
            })
            .then(
                (res) => {
                    _this.position = res.data;
                },
                (err) => {
                    alert(err.response);
                }
                )
                .finally(() => {});
            },
            
            scrollMeTo(refName) {
                var element = this.$refs[refName];
                element.scrollIntoView({ behavior: "smooth" });
            },
            
            cancel: function() {
                this.reset();
            },
            
            check_input: function(){
                ret = "";
                reason = "Please fill in title, access, type and the corresponding file/web link.";
                
                if(this.title == ""){
                    ret = reason;
                }
                
                if(this.access.length == 0){
                    ret = reason;
                }
                
                if(this.type == ""){
                    ret = reason;
                }
                
                if(this.type == "file"){
                    if(document.getElementById('file1').files[0] == 'undefined'){
                        ret = reason;
                    }
                }
                
                if(this.type == "link" || this.type == "video"){
                    if(this.link == ""){
                        ret = reason;
                    }
                }
                
                return ret;
                
            },
            
            save: function() {
                let _this = this;
                let reason = this.check_input();
                if (reason !== "") {
                    Swal.fire({
                        text: reason,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
                
                Swal.fire({
                    title: "Submit",
                    text: "Are you sure to save?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                }).then((result) => {
                    if (result.value) {
                        if (_this.submit == true) return;
                        
                        _this.submit = true;
                        
                        var token = localStorage.getItem("token");
                        var form_Data = new FormData();
                        
                        form_Data.append("jwt", token);
                        
                        form_Data.append("title", _this.title);
                        
                        let category01 = $('#category').val();
                        form_Data.append("category", category01.join());
                        
                        let access01 = $('#access').val();
                        form_Data.append("access", access01.join());
                        
                        form_Data.append("type", _this.type);
                        form_Data.append("link", _this.link);
                        
                        let file1 = document.getElementById('file1').files[0];
                        if(typeof file1 !== 'undefined') 
                        form_Data.append('file1', file1);
                        
                        let photo = document.getElementById('photo').files[0];
                        if(typeof photo !== 'undefined') 
                        form_Data.append('photo', photo);
                        
                        
                        form_Data.append("watch", _this.watch);
                        form_Data.append("duration", _this.duration);
                        
                        
                        
                        axios({
                            method: "post",
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                            url: "api/knowledge_add_insert",
                            data: form_Data,
                        })
                        .then(function(response) {
                            //handle success
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
                            
                            _this.submit = false;
                            
                        });
                    } else {
                        return;
                    }
                });
                
            },

            edit: function() {
                let _this = this;
                let reason = this.check_input();
                if (reason !== "") {
                    Swal.fire({
                        text: reason,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return;
                }
                
                Swal.fire({
                    title: "Submit",
                    text: "Are you sure to save?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                }).then((result) => {
                    if (result.value) {
                        if (_this.submit == true) return;
                        
                        _this.submit = true;
                        
                        var token = localStorage.getItem("token");
                        var form_Data = new FormData();
                        
                        form_Data.append("jwt", token);
                        
                        form_Data.append("id", _this.id);
                        form_Data.append("title", _this.title);
                        
                        let category01 = $('#category').val();
                        form_Data.append("category", category01.join());
                        
                        let access01 = $('#access').val();
                        form_Data.append("access", access01.join());
                        
                        form_Data.append("type", _this.type);
                        form_Data.append("link", _this.link);
                        
                        let file1 = document.getElementById('file1').files[0];
                        if(typeof file1 !== 'undefined') 
                        form_Data.append('file1', file1);
                        
                        let photo = document.getElementById('photo').files[0];
                        if(typeof photo !== 'undefined') 
                        form_Data.append('photo', photo);
                        
                        
                        form_Data.append("watch", _this.watch);
                        form_Data.append("duration", _this.duration);
                        
                        
                        
                        axios({
                            method: "post",
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                            url: "api/knowledge_add_update",
                            data: form_Data,
                        })
                        .then(function(response) {
                            //handle success
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
                            
                            _this.submit = false;
                            
                        });
                    } else {
                        return;
                    }
                });
                
            },
            
            reset: function() {
                this.title = "";
                this.category = [];
                this.access = [];
                this.type = "";
                this.file1 = "";
                this.link = "";
                this.duration = "";
                this.watch = "";
                this.description = "";
                this.url = null;
                
                this.edit_mode = false;
                
                
                $('#category').val('default');
                $('#category').selectpicker('refresh');
                $('#access').val('default');
                $('#access').selectpicker('refresh');
                
                
            },
            
            toggle_product() {
                let toogle = document.getElementById('select_all_product').checked;
                for(var i = 0; i < this.variation_product.length; i++) {
                    this.variation_product[i].checked = toogle;
                }
            },
            
            bulk_toggle_product() {
                let toogle = document.getElementById('bulk_select_all_product').checked;
                this.code_checked = toogle;
                this.price_ntd_checked = toogle;
                this.price_checked = toogle;
                this.quoted_price_checked = toogle;
                this.image_checked = toogle;
                this.status_checked = toogle;
                this.price_ntd_last_change_checked = toogle;
                this.price_last_change_checked = toogle;
                this.quoted_price_last_change_checked = toogle;
                
            },
            
            clear_accessory() {
                for (var i in this.accessory_infomation) {
                    this.accessory_infomation[i].detail[0] = [];
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
        },
    });
    