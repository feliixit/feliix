
let mainState = {

    // edit state
    isEditing: false,

    // table
    clicked : 0,

    // data
    is_checked: false,
    id: 0,
    username: '',
    title: '',
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
    record: {},

    view_data: {},

    error_username: '',

    department: "",
    position : "",
    first_name : "",
    middle_name : "",
    surname : "",

    user : {},

    edit_emp: false,

};

var app = new Vue({
	el: '#app',

	data: mainState,

	created () {
      console.log('Vue created');
      this.getReceiveRecords();

      this.getUserName();
      this.getAccess();
    },

 
	methods:{

        getAccess: async function() {
            var token = localStorage.getItem('token');
            var form_Data = new FormData();
      
            let res = await axios.get('api/access_control_kind_get', { headers: { "Authorization": `Bearer ${token}` }, params: { kind: 'edit_basic' } });
            this.edit_emp = res.data.edit_basic;
          },

		getReceiveRecords: function(keyword) {
            let _this = this;
          console.log("getReceiveRecords");
            axios.get('api/employee_basic_info')
                .then(function(response) {
                    console.log(response.data);
                    _this.user_records = response.data;

                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        
        getUserName: function() {
            var token = localStorage.getItem("token");
            var form_Data = new FormData();
            let _this = this;

            form_Data.append("jwt", token);
    
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
                _this.username = response.data.username;
                _this.title = response.data.title.trim();

                if(_this.title == "Chief Advisor" || _this.title == "Operations Manager")
                    _this.edit_emp = true;
                else
                    _this.edit_emp = false;
    
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
    
        editRecord() {
          console.log("editRecord");
            var favorite = [];
            this.resetError();

            for (i = 0; i < this.user_records.length; i++) 
            {
              if(this.user_records[i].is_checked == 1)
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
            this.record.updated_str = new Date().toISOString().slice(0,10);

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

        to_long_date(date) {
            var d = new Date(date);
            var month = new Array();
            month[0] = "JANUARY";
            month[1] = "FEBRUARY";
            month[2] = "MARCH";
            month[3] = "APRIL";
            month[4] = "MAY";
            month[5] = "JUNE";
            month[6] = "JULY";
            month[7] = "AUGUST";
            month[8] = "SEPTEMBER";
            month[9] = "OCTOBER";
            month[10] = "NOVEMBER";
            month[11] = "DECEMBER";
            var n = month[d.getMonth()];
            return n + " " + d.getDate() + ", " + d.getFullYear();
        },

        viewRecord() {

            var favorite = [];
            this.resetError();

            for (i = 0; i < this.user_records.length; i++) 
            {
              if(this.user_records[i].is_checked == 1)
                favorite.push(this.user_records[i].id);
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

            this.view_data = JSON.parse(JSON.stringify(this.record));

            if(this.view_data.updated_at != '')
                this.view_data.updated_str = this.record.updated_at.substring(0, 10);

            this.view_data.emp_number = this.record.emp_number.toUpperCase();
            this.view_data.first_name = this.record.first_name.toUpperCase();
            this.view_data.middle_name = this.record.middle_name.toUpperCase();
            this.view_data.surname = this.record.surname.toUpperCase();
            this.view_data.emp_status = this.record.emp_status.toUpperCase();
            this.view_data.company = this.record.company.toUpperCase();
            this.view_data.emp_category = this.record.emp_category.toUpperCase();
            this.view_data.superior = this.record.superior.toUpperCase();

            this.view_data.department = this.record.department.toUpperCase();
            this.view_data.title = this.record.title.toUpperCase();

            // convert to long date format like DECEMBER 12, 2023
            if(this.record.date_hired != '')
                this.view_data.date_hired = this.to_long_date(this.record.date_hired);

            if(this.record.regular_hired != '')
                this.view_data.regular_hired = this.to_long_date(this.record.regular_hired);

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

        toggle_view: function() {
            window.jQuery(".mask").toggle();
            window.jQuery("#Modal_view").toggle();
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
                    url: 'api/employee_basic_info_reset',
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

        save_input: function(event) {
            let _this = this;

            targetId = this.record.id;
            let formData = new FormData();

            formData.append('record', JSON.stringify(this.record));
            formData.append('id', this.record.id);

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/employee_basic_info_save',
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
        },

 
	}
});