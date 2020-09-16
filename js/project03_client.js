var app = new Vue({
  el: '#app',
  data: {
    stage_id: 0,
    receive_records: [],
    record: {},

    users : {},

    submit : false,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      { name: '10', id: 10 },
      { name: '25', id: 25 },
      { name: '50', id: 50 },
      { name: '100', id: 100 },
      { name: 'All', id: 10000 }
    ],
    perPage: 20,

    // Venue
    venue: '',
    stage_client_venue : {},

    // Sales Assigned
    uid:0,
    org_uid:0,
    stage_client_sales : {},

    // Date
    dt: '',
    stage_client_date : {},

    // Status
    status: 0,
    stage_client_status : {},

    // Priority
    priority: 0,
    stage_client_priority : {},

    // Amount
    amount: '',
    stage_client_amount : {},

    // Competitor
    competitor: '',
    stage_client_competitor : {},

  },

  created() {
    let _this = this;
    let uri = window.location.href.split('?');
    if (uri.length == 2) {
      let vars = uri[1].split('&');
      let getVars = {};
      let tmp = '';
      vars.forEach(function (v) {
        tmp = v.split('=');
        if (tmp.length == 2)
          _this.stage_id = tmp[1];
          _this.get_stage_client_venue(_this.stage_id);
          _this.get_stage_client_sales(_this.stage_id);
          _this.get_stage_client_date(_this.stage_id);
          _this.get_stage_client_status(_this.stage_id);
          _this.get_stage_client_priority(_this.stage_id);
          _this.get_stage_client_amount(_this.stage_id);
          _this.get_stage_client_competitor(_this.stage_id);
      });
    }

    _this.getUsers();

  },

  computed: {
    displayedStagePosts() {
      this.setPages();
      return this.paginate(this.receive_records);
    },

  },

  mounted() {


  },

  watch: {

    receive_records() {
      console.log('Vue watch receive_stage_records');
      this.setPages();
    },

  },



  methods: {

    setPages() {
      console.log('setPages');
      this.pages = [];
      let numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if (numberOfPages == 1)
        this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function (posts) {
      console.log('paginate');
      if (this.page < 1)
        this.page = 1;
      if (this.page > this.pages.length)
        this.page = this.pages.length;

      let page = this.page;
      let perPage = this.perPage;
      let from = (page * perPage) - perPage;
      let to = (page * perPage);
      return this.receive_records.slice(from, to);
    },

    get_stage_client_venue: function(stage_id) {
      let _this = this;

      if(stage_id == 0)
        return;

      const params = {
              stage_id : stage_id,
              type : 'venue',
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.stage_client_venue = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
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

    venue_clear() {

      this.venue = "";

      document.getElementById('dialog_a1').classList.remove("show");
      document.getElementById('add_a1').classList.remove("focus");
    },

    venue_create() {
      let _this = this;

      if (this.venue.trim() == '') {
        Swal.fire({
          text: 'Please enter venue!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('message', this.venue.trim());
      form_Data.append('type', 'venue');

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_stage_client',
        data: form_Data
      })
        .then(function (response) {
          //handle success
          //this.$forceUpdate();
          _this.get_stage_client_venue(_this.stage_id);
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.venue_clear()});
    },

    sales_clear() {

      this.uid = this.org_uid;

      document.getElementById('dialog_a2').classList.remove("show");
      document.getElementById('add_a2').classList.remove("focus");
    },

    sales_create() {
      let _this = this;

      if(this.uid == this.org_uid)
        return;

      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('option', this.uid);
      form_Data.append('type', 'sales');

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_stage_client',
        data: form_Data
      })
        .then(function (response) {
          //handle success
          //this.$forceUpdate();
          _this.get_stage_client_sales(_this.stage_id);
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.sales_clear()});
    },

    get_stage_client_sales: function(stage_id) {
      let _this = this;

      if(stage_id == 0)
        return;

      const params = {
              stage_id : stage_id,
              type : 'sales',
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_stage_client_sales', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.stage_client_sales = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

      date_clear() {

        this.dt = '';
  
        document.getElementById('dialog_a3').classList.remove("show");
        document.getElementById('add_a3').classList.remove("focus");
      },
  
      date_create() {
        let _this = this;
   
        _this.submit = true;
        var form_Data = new FormData();
  
        form_Data.append('stage_id', this.stage_id);
        form_Data.append('message', this.dt);
        form_Data.append('type', 'date');
  
        const token = sessionStorage.getItem('token');
  
        axios({
          method: 'post',
          headers: {
            'Content-Type': 'multipart/form-data',
            Authorization: `Bearer ${token}`
          },
          url: 'api/project03_stage_client',
          data: form_Data
        })
          .then(function (response) {
            //handle success
            //this.$forceUpdate();
            _this.get_stage_client_date(_this.stage_id);
          })
          .catch(function (response) {
            //handle error
            console.log(response)
          }).finally(function () {_this.date_clear()});
      },
  
      get_stage_client_date: function(stage_id) {
        let _this = this;
  
        if(stage_id == 0)
          return;
  
        const params = {
                stage_id : stage_id,
                type : 'date',
              };
  
            let token = localStorage.getItem('accessToken');
      
            axios
                .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
                .then(
                (res) => {
                    _this.stage_client_date = res.data;
                },
                (err) => {
                    alert(err.response);
                },
                )
                .finally(() => {
                    
                });
        },


        status_clear() {

          this.status = '';
    
          document.getElementById('dialog_a4').classList.remove("show");
          document.getElementById('add_a4').classList.remove("focus");
        },
    
        status_create() {
          let _this = this;
     
          _this.submit = true;
          var form_Data = new FormData();
    
          form_Data.append('stage_id', this.stage_id);
          form_Data.append('option', this.status);
          form_Data.append('type', 'status');
    
          const token = sessionStorage.getItem('token');
    
          axios({
            method: 'post',
            headers: {
              'Content-Type': 'multipart/form-data',
              Authorization: `Bearer ${token}`
            },
            url: 'api/project03_stage_client',
            data: form_Data
          })
            .then(function (response) {
              //handle success
              //this.$forceUpdate();
              _this.get_stage_client_status(_this.stage_id);
            })
            .catch(function (response) {
              //handle error
              console.log(response)
            }).finally(function () {_this.status_clear()});
        },
    
        get_stage_client_status: function(stage_id) {
          let _this = this;
    
          if(stage_id == 0)
            return;
    
          const params = {
                  stage_id : stage_id,
                  type : 'status',
                };
    
              let token = localStorage.getItem('accessToken');
        
              axios
                  .get('api/project03_stage_client_status', { params, headers: {"Authorization" : `Bearer ${token}`} })
                  .then(
                  (res) => {
                      _this.stage_client_status = res.data;
                  },
                  (err) => {
                      alert(err.response);
                  },
                  )
                  .finally(() => {
                      
                  });
          },


          priority_clear() {

            this.priority = '';
      
            document.getElementById('dialog_a5').classList.remove("show");
            document.getElementById('add_a5').classList.remove("focus");
          },
      
          priority_create() {
            let _this = this;
       
            _this.submit = true;
            var form_Data = new FormData();
      
            form_Data.append('stage_id', this.stage_id);
            form_Data.append('option', this.priority);
            form_Data.append('type', 'priority');
      
            const token = sessionStorage.getItem('token');
      
            axios({
              method: 'post',
              headers: {
                'Content-Type': 'multipart/form-data',
                Authorization: `Bearer ${token}`
              },
              url: 'api/project03_stage_client',
              data: form_Data
            })
              .then(function (response) {
                //handle success
                //this.$forceUpdate();
                _this.get_stage_client_priority(_this.stage_id);
              })
              .catch(function (response) {
                //handle error
                console.log(response)
              }).finally(function () {_this.priority_clear()});
          },
      
          get_stage_client_priority: function(stage_id) {
            let _this = this;
      
            if(stage_id == 0)
              return;
      
            const params = {
                    stage_id : stage_id,
                    type : 'priority',
                  };
      
                let token = localStorage.getItem('accessToken');
          
                axios
                    .get('api/project03_stage_client_priority', { params, headers: {"Authorization" : `Bearer ${token}`} })
                    .then(
                    (res) => {
                        _this.stage_client_priority = res.data;
                    },
                    (err) => {
                        alert(err.response);
                    },
                    )
                    .finally(() => {
                        
                    });
            },

            amount_clear() {

              this.amount = '';
        
              document.getElementById('dialog_a6').classList.remove("show");
              document.getElementById('add_a6').classList.remove("focus");
            },
        
            amount_create() {
              let _this = this;
         
              _this.submit = true;
              var form_Data = new FormData();
        
              form_Data.append('stage_id', this.stage_id);
              form_Data.append('message', this.amount);
              form_Data.append('type', 'amount');
        
              const token = sessionStorage.getItem('token');
        
              axios({
                method: 'post',
                headers: {
                  'Content-Type': 'multipart/form-data',
                  Authorization: `Bearer ${token}`
                },
                url: 'api/project03_stage_client',
                data: form_Data
              })
                .then(function (response) {
                  //handle success
                  //this.$forceUpdate();
                  _this.get_stage_client_amount(_this.stage_id);
                })
                .catch(function (response) {
                  //handle error
                  console.log(response)
                }).finally(function () {_this.amount_clear()});
            },
        
            get_stage_client_amount: function(stage_id) {
              let _this = this;
        
              if(stage_id == 0)
                return;
        
              const params = {
                      stage_id : stage_id,
                      type : 'amount',
                    };
        
                  let token = localStorage.getItem('accessToken');
            
                  axios
                      .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
                      .then(
                      (res) => {
                          _this.stage_client_amount = res.data;
                      },
                      (err) => {
                          alert(err.response);
                      },
                      )
                      .finally(() => {
                          
                      });
              },

              competitor_clear() {

                this.competitor = '';
          
                document.getElementById('dialog_a7').classList.remove("show");
                document.getElementById('add_a7').classList.remove("focus");
              },
          
              competitor_create() {
                let _this = this;
           
                _this.submit = true;
                var form_Data = new FormData();
          
                form_Data.append('stage_id', this.stage_id);
                form_Data.append('message', this.competitor);
                form_Data.append('type', 'competitor');
          
                const token = sessionStorage.getItem('token');
          
                axios({
                  method: 'post',
                  headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${token}`
                  },
                  url: 'api/project03_stage_client',
                  data: form_Data
                })
                  .then(function (response) {
                    //handle success
                    //this.$forceUpdate();
                    _this.get_stage_client_competitor(_this.stage_id);
                  })
                  .catch(function (response) {
                    //handle error
                    console.log(response)
                  }).finally(function () {_this.competitor_clear()});
              },
          
              get_stage_client_competitor: function(stage_id) {
                let _this = this;
          
                if(stage_id == 0)
                  return;
          
                const params = {
                        stage_id : stage_id,
                        type : 'competitor',
                      };
          
                    let token = localStorage.getItem('accessToken');
              
                    axios
                        .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
                        .then(
                        (res) => {
                            _this.stage_client_competitor = res.data;
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