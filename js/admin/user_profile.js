
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
    date_end_company: '',
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

    error_username: '',

};

var app = new Vue({
	el: '#mainContent',

	data: mainState,

	created () {
      console.log('Vue created');
      this.getReceiveRecords();
      this.perPage = this.inventory.find(i => i.id === this.perPage);

    },

    computed: {
      displayedPosts () {
        console.log('Vue computed');

        this.setPages();
        return this.paginate(this.user_records);
      }
    },

    watch: {
      user_records () {

        this.setPages();
      },

  
    },
 
	methods:{
		getReceiveRecords: function(keyword) {
          console.log("getReceiveRecords");
            axios.get('../api/admin/user_profile')
                .then(function(response) {
                    console.log(response.data);
                    app.user_records = response.data;

                    console.log("getReceiveRecords");

                })
                .catch(function(error) {
                    console.log(error);
                });
        },

		getIndex(index) {
            return ((this.page - 1) * this.perPage.id) + index
        },

        setPages () {
          console.log('setPages');
          this.pages = [];
          let numberOfPages = Math.ceil(this.user_records.length / this.perPage.id);

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
          let perPage = this.perPage.id;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          return  this.user_records.slice(from, to);
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
			app.errorMessage = '';
			app.successMessage = '';
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
            this.record = this.shallowCopy(app.user_records.find(element => element.id == favorite));
            this.isEditing = true;

            this.pic_url = this.record.pic_url !== '' ? '../images/man/' + this.record.pic_url : '';
            this.tel = this.record.tel;
            this.date_start_company = this.record.date_start_company;
            this.date_end_company = this.record.date_end_company;

            $('.block.record').toggleClass('show');

  
            console.log(this.record.date_receive);
            // $( "#upddate" ).value = this.record.date_receive;

            this.unCheckCheckbox();

            //$(".alone").prop("checked", false);
            $(window).scrollTop(0);
        },


        cancelReceiveRecord: function(event) {
            console.log("cancel edit receive_record!")

            app.resetForm();
        },

        editReceiveRecord: function(event) {
            console.log("editReceiveRecord")

            targetId = this.record.id;
            let formData = new FormData();


            formData.append('tel', this.tel)
            formData.append('date_start_company', this.date_start_company)
            formData.append('date_end_company', this.date_end_company)

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
                        //const index = app.user_records.findIndex((e) => e.id === this.record.id);
                        //if (index !== -1) 
                        //    app.user_records[index] = this.record;
                        
                        app.resetForm();
                      
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
                    _this.record = _this.shallowCopy(app.user_records.find(element => element.id == favorite));

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
            this.username = '';
            this.pic_url = '';
            this.tel = '';
            this.date_start_company = '';
            this.date_end_company = '';        

            this.isEditing = false;
            this.record = {};

            this.apartment_id = 0;
            this.title_id = 0;

            this.resetError();
            
            if(!$('.block.record').hasClass('show')) 
              $('.block.record').addClass('show');

            this.getReceiveRecords();
        },

        onFileChange(e) {
            const file = e.target.files[0];

            if (file.size > 1048576) {
              

              Swal.fire({
                html: "Please choose another photo whose size is lower than 1 mb.",
                icon: "info",
                confirmButtonText: "OK",
              });

              document.getElementById('photo').value= null;

              return;
            } 
      
            this.pic_url = URL.createObjectURL(file);
         
          },
 
	}
});