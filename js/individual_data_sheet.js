
let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    id: 0,
    username: '',
    pic_url: '',
    tel: '',
    date_start_company: '',
    seniority: '',


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
    perPage: 10000,

    user_records: [],
    user_records_1 : [],

    view_data : [],

    record: {},

    error_username: '',

    department: "",
    position : "",
    first_name : "",
    middle_name : "",
    surname : "",

    user : {},

    auth_date : "",

    sig_name: {},
    sig_date: {},

    loading: false,

};

var app = new Vue({
	el: '#app',

	data: mainState,

	created () {
      console.log('Vue created');
      this.getReceiveRecords();

      this.getReceiveRecords_1();

    },

 
	methods:{
		getReceiveRecords: function() {
            let _this = this;
  
               axios.get('api/individual_data_sheet')
                .then(function(response) {
                    console.log(response.data);
                    _this.user_records = response.data;

                    _this.auth_date = _this.user_records[0].auth_date;

                    if(_this.auth_date == "")
                    {
                        _this.authRecord();
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        viewInfo : function() {
            let _this = this;

            axios.get('api/employee_basic_info_idv')
                .then(function(response) {
                    console.log(response.data);
                    if(response.data.length > 0)
                        _this.view_data = response.data[0];

                    console.log("getReceiveRecords");

                    _this.toggle_viewinfo();

                })
                .catch(function(error) {
                    console.log(error);
                });

        },

		getReceiveRecords_1: function() {
            let _this = this;

            axios.get('api/individual_data_sheet_status?status=1')
                .then(function(response) {
                    console.log(response.data);
                    _this.user_records_1 = response.data;


                })
                .catch(function(error) {
                    console.log(error);
                });
        },

		getIndex(index) {
            return ((this.page - 1) * this.perPage.id) + index
        },

		toggleCheckbox()
        {
            var i;
            for (i = 0; i < this.user_records.length; i++) 
            {
              this.user_records[i].is_checked = (this.clicked == 1 ? 0 : 1);
            }

            this.clicked = (this.clicked == 1 ? 0 : 1);
          //$(".alone").prop("checked", !this.clicked);
          //this.clicked = !this.clicked;
        },

        resetError: function() {
          console.log("resetError");
            this.error_username = '';
            this.error_email = '';
            this.error_password = '';
        },

        unCheckCheckbox()
        {
            for (i = 0; i < this.user_records.length; i++) 
            {
              this.user_records[i].is_checked = false;
            }
          //$(".alone").prop("checked", false);
          //this.clicked = false;
        },
 
		toFormData: function(obj){
			var form_data = new FormData();
			for(var key in obj){
				form_data.append(key, obj[key]);
			}
			return form_data;
		},

		shallowCopy(obj) {
          console.log("shallowCopy");
            var result = {};
            for (var i in obj) {
                result[i] = obj[i];
            }
            return result;
        },
 
		clearMessage: function(){
			this.errorMessage = '';
			this.successMessage = '';
    },

    uncheck: function(id) {
        // uncheck all checkboxes except the one passed as parameter
        this.user_records.forEach(function(item){
            if (item.id !== id) item.is_checked = "0"
        })
    },

    reset_auth() {
        this.sig_date.jSignature('reset');
        this.sig_name.jSignature('reset');
    },

    submit_auth() {
        let _this = this;
        var sig = this.sig_date.jSignature('getData', 'image');
        var sig1 = this.sig_name.jSignature('getData', 'image');

        let data = { image_date: sig, image_name: sig1, item_id: this.record.user_id };
        this.loading = true;

        axios
        .post("api/individual_data_sheet_snapshot", data, {
            headers: {
            "Content-Type": "application/json"
            }
        }).then(function(response) {
            console.log(response.data);
            _this.close_auth();
            _this.reset_auth();
                
        })
        .catch(function(error) {
            console.log(error);
        });

    },

    authRecord() {
        this.record = this.shallowCopy(this.user_records[0]);
        this.toggle_auth();
        if(this.auth_date == "")
        {
            if(this.loading == false)
            {
                this.sig_date = $("#signature_date").jSignature();
                this.sig_name = $("#signature_name").jSignature();

                this.loading = true;
            }
        }
    },
    
        editRecord() {
          console.log("editRecord");
            var favorite = [];
            this.resetError();

            if(this.user_records_1.length > 0)
            {
                Swal.fire({
                    html: "Your previously submitting data sheet is still under review by your supervisor and thus the data sheet shown in this window now is old.",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
            }

            for (i = 0; i < this.user_records.length; i++) 
            {
              //if(this.user_records[i].is_checked == 1)
                favorite.push(this.user_records[i].id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                //alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                Swal.fire({
                    html: "Please select one user to edit!",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                return;
            }
            this.record = this.shallowCopy(this.user_records.find(element => element.id == favorite));
            this.isEditing = true;

            // if(this.record.updated_at != '')
            //     this.record.updated_str = this.record.updated_at.substring(0, 10);
            var tzoffset = (new Date()).getTimezoneOffset() * 60000; //offset in milliseconds
            var localISOTime = (new Date(Date.now() - tzoffset)).toISOString().slice(0, -1);
            this.record.updated_str = localISOTime.slice(0,10);

            this.toggle_input();
  
            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            this.unCheckCheckbox();

            //$(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },

        close_view: function() {
            this.toggle_view();
            this.resetForm();
        },

        close_viewinfo: function() {
            this.toggle_viewinfo();
            this.resetForm();
        },

        close_auth: function() {
            this.toggle_auth();
            this.resetForm();
        },

        viewRecord() {

            var favorite = [];
            this.resetError();

            for (i = 0; i < this.user_records.length; i++) 
            {
              //if(this.user_records[i].is_checked == 1)
                favorite.push(this.user_records[i].id);
            }

            if(this.user_records_1.length > 0)
            {
                Swal.fire({
                    html: "Your previously submitting data sheet is still under review by your supervisor and thus the data sheet shown in this window now is old.",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                //alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                Swal.fire({
                    html: "Please select one user to view!",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                return;
            }
            this.record = this.shallowCopy(this.user_records.find(element => element.id == favorite));
            this.isEditing = true;

            if(this.record.updated_at != '')
                this.record.updated_str = this.record.updated_at.substring(0, 10);

            this.toggle_view();

            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            this.unCheckCheckbox();

            //$(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },

        toggle_input: function() {
            window.jQuery(".mask").toggle();
            window.jQuery("#Modal_input").toggle();
        },

        toggle_auth: function() {
            window.jQuery(".mask").toggle();
            window.jQuery("#Modal_authorize").toggle();
        },

        toggle_view: function() {
            window.jQuery(".mask").toggle();
            window.jQuery("#Modal_view").toggle();
        },

        toggle_viewinfo: function() {
            window.jQuery(".mask").toggle();
            window.jQuery("#Modal_basic_info_view").toggle();
        },

        resetRecord: function() {
            let _this = this;
            var favorite = [];
            this.resetError();

            for (i = 0; i < this.user_records.length; i++) 
            {
              if(this.user_records[i].is_checked == 1)
                favorite.push(this.user_records[i].data_id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                //alert("請選一筆資料進行修改 (Please select one row to edit!)");
                //$(window).scrollTop(0);
                Swal.fire({
                    html: "Please select one user to edit!",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                return;
            }

            Swal.fire({
                title: "WARNING",
                text: "Are you sure to erase this record?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
                }).then((result) => {
                if (result.value) {
                    _this.do_reset_record(favorite[0]);
                }
            });

            this.unCheckCheckbox();

            this.resetError();
        },

        cancel_input: function(event) {
            console.log("cancel edit receive_record!")

            this.toggle_input();
            this.resetForm();
        },

        do_reset_record: function(id) {
            let _this = this;

            let formData = new FormData();

            formData.append('id', id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/employee_data_sheet_reset',
                    data: formData
                    
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    Swal.fire({
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                      })
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                })
                .finally(function() {
                    _this.resetForm();
                });
        },

        save_prompt: async function(event) {
            let _this = this;

            await this.getReceiveRecords_1();

            if(this.user_records_1.length == 0)
            {
                _this.save_input();
            }
            else
            {
                Swal.fire({
                    title: "Save",
                    text: "Previously you already submitted a new data sheet but your supervisor hasn’t finished reviewing. If you submit it again, then the data sheet now will replace the previously-submitted data sheet. Are you sure to proceed?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                    }).then((result) => {
                    if (result.value) {
                        _this.save_input();
                    } else {
                        _this.toggle_input();
                        _this.resetForm();
                    }
                }
                );
            }
        },

        save_input: async function(event) {
            let _this = this;

            targetId = this.record.id;
            let formData = new FormData();

            formData.append('record', JSON.stringify(this.record));

            if(this.user_records_1.length > 0)
                formData.append('record1', JSON.stringify(this.user_records_1[0]))
            else
                formData.append('record1', JSON.stringify([]));

            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/individual_data_sheet_save',
                    data: formData
                    
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    Swal.fire({
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                      })
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                })
                .finally(function() {
                    _this.toggle_input();
                    _this.resetForm();
                });
        },

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")
            let _this = this;

            targetId = this.record.id;
            let formData = new FormData();


            formData.append('tel', this.tel)
            formData.append('date_start_company', this.date_start_company)
           
            let file = document.getElementById('photo').files[0];
            if(typeof file !== 'undefined') 
            {
                formData.append('photo', file);
            }
            
            formData.append('crud', "update");
            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: '../api/admin/user_profile',
                    data: formData
                    
                })
                .then(function(response) {
                    //handle success
                    console.log(response)
                    //if (response.data !== "")
                    //{
                        //const index = this.user_records.findIndex((e) => e.id === this.record.id);
                        //if (index !== -1) 
                        //    this.user_records[index] = this.record;
                        
                        _this.resetForm();
                      
                  //}
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        RemoveReceiveRecord: function() {
            var favorite = [];
            this.resetError();

            let _this = this;

            for (i = 0; i < this.user_records.length; i++) 
            {
              if(this.user_records[i].is_checked == 1)
                favorite.push(this.user_records[i].id);
            }

            //$.each($("input[name='record_id']:checked"), function() {
            //    favorite.push($(this).val());
            //});
            if (favorite.length != 1) {
                //alert("請選一筆資料進行刪除 (Please select one row to remove!)");
                //$(window).scrollTop(0);
                Swal.fire({
                    html: "Please select one user to remove!",
                    icon: "info",
                    confirmButtonText: "OK",
                  });
                return;
            }

            Swal.fire({
                title: "Remove",
                text: "Are you sure to remove user's photo?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes",
              }).then((result) => {
                if (result.value) {
                    _this.record = _this.shallowCopy(this.user_records.find(element => element.id == favorite));

                var formData = new FormData();

                formData.append('id', _this.record.id);
                formData.append('pic_url', _this.record.pic_url);
                formData.append('crud', 'del')


                const token = sessionStorage.getItem('token');

                axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        url: '../api/admin/user_profile',
                        data: formData
                    })
                    .then(function(response) {
                        //handle success
                        console.log(response)

                        _this.resetForm();

                    })
                    .catch(function(response) {
                        //handle error
                        console.log(response)
                    });
                }
            });
            
        },

        resetForm: function() {
          console.log("resetForm");
             this.record = {};
            this.resetError();
            this.getReceiveRecords();
            this.getReceiveRecords_1();
        },

 
	}
});