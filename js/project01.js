
var app = new Vue({
  el: '#app',
  data:{
    project_category : '',
    client_type : '',

    receive_records: [],
    record: {},

    categorys : {},
    client_types : {},

  },

  created () {
    
    this.getProjectCategorys();
    this.getClientTypes();

  },

  computed: {
    displayedRecord () {
      return this.receive_records;
    },

  },

  mounted(){
   var d1 = new Date();
    this.month1 = d1;

    $('#start').val(d1.toISOString().slice(0,7).replace(/-/g,"-"));

    this.getLeaveCredit();
    
  },

  watch: {

      picked () {
        this.getLeaveCredit();
      },
  },



  methods:{

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

      unCheckCheckbox()
        {
            for (i = 0; i < this.receive_records.length; i++) 
            {
              this.receive_records[i].is_checked = false;
            }
          //$(".alone").prop("checked", false);
          //this.clicked = false;
        },

      showPic(pic)
      {
        Swal.fire({
          title: 'Certificate of Diagnosis',
          text: 'Click to close',
          imageUrl: 'img/' + pic,
        })
      },

      approveReceiveRecord: function(id) {
            let _this = this;
            //targetId = this.record.id;
            var form_Data = new FormData();

            form_Data.append('crud', "app");
            form_Data.append('id', id);

            var params = {
                'id': id
            }

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/leave_record_approval',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        rejectReceiveRecord: function(id) {
            let _this = this;
            //targetId = this.record.id;
            var form_Data = new FormData();

            form_Data.append('crud', "rej");
            form_Data.append('id', id);

            var params = {
                'id': id
            }

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${token}`
                    },
                    url: 'api/leave_record_reject',
                    data: form_Data
                })
                .then(function(response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.resetForm();
                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },

        detail: function() {
          let _this = this;

        let favorite = [];
          
          for (i = 0; i < this.receive_records.length; i++) 
            {
              if(this.receive_records[i].is_checked == 1)
                favorite.push(this.receive_records[i].id);
            }

            if (favorite.length != 1) {
              Swal.fire({
                text: 'Please select row to see the detail!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                this.view_detail = false;
                return;
            }

            this.record = this.shallowCopy(this.receive_records.find(element => element.id == favorite));

            this.view_detail = true;

        },

    approve: function() {

      let _this = this;

        let favorite = [];

        var approve_record = false;
          
          for (i = 0; i < this.receive_records.length; i++) 
            {
              if(this.receive_records[i].is_checked == 1)
              {
                if(this.receive_records[i].approval === 'R')
                {
                  Swal.fire({
                    text: 'Rejected data cannot be approved! Please contact Admin or IT staffs.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                  });

                  return;
                }

                if(this.receive_records[i].approval === 'A')
                {
                  Swal.fire({
                    text: 'Approved data cannot be approved again! Please contact Admin or IT staffs.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                  });

                  return;
                }

                favorite.push(this.receive_records[i].id);
              }
            }

            if (favorite.length < 1) {
              Swal.fire({
                text: 'Please select rows to approve!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

          

          Swal.fire({
            title: 'Are you sure to approve?',
            text: "Are you sure to approve apply?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
          }).then((result) => {
            if (result.value) {
              _this.submit = true;
              _this.approveReceiveRecord(favorite.join(", "));

              _this.resetForm();
              _this.unCheckCheckbox();
            }
          })

      },

      reject: function() {

      let _this = this;

        let favorite = [];

        var approve_record = false;
          
          for (i = 0; i < this.receive_records.length; i++) 
            {
              if(this.receive_records[i].is_checked == 1)
              {
                if(this.receive_records[i].approval === 'R')
                {
                  Swal.fire({
                    text: 'Rejected data cannot be rejected again! Please contact Admin or IT staffs.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                  });

                  return;
                }

                if(this.receive_records[i].approval === 'A')
                {
                  Swal.fire({
                    text: 'Approved data cannot be rejected! Please contact Admin or IT staffs.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                  });

                  return;
                }

                favorite.push(this.receive_records[i].id);
              }
            }

            if (favorite.length < 1) {
              Swal.fire({
                text: 'Please select rows to reject!',
                icon: 'warning',
                confirmButtonText: 'OK'
              })
                
                //$(window).scrollTop(0);
                return;
            }

          

          Swal.fire({
            title: 'Are you sure to reject?',
            text: "Are you sure to reject apply?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
          }).then((result) => {
            if (result.value) {
              _this.submit = true;
              _this.rejectReceiveRecord(favorite.join(", "));

              _this.resetForm();
              _this.unCheckCheckbox();
            }
          })

      },

      resetForm: function() {
          
            this.submit = false;
            this.view_detail = false;

          this.receive_records = [];
          this.record = {};

          this.getLeaveCredit();

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