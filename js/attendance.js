
var app = new Vue({
  el: '#app',
  data:{
    receive_records: [],
    leave_records:{},
    holiday_records:{},
    max_date:'',
    min_date:'',
    today:'',
  },

  created () {
    this.getRecords();
    this.getLeaveRecords();
    this.getHolidayRecords();
    this.getToday();
    this.setDutyDate();
  },

  computed: {
    displayedRecord () {
      return this.receive_records;
    },
  },

  mounted(){
   
  },

  methods:{
    change_date: function() {
      this.getRecords();
    },

    getToday: function() {
      var self = this;
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();

      this.today = yyyy + '-' + mm + '-' + dd;

      //setInterval(self.getToday, 1000 * 60)
    },
    
    setDutyDate: function() {
      var today = new Date();
      var dd = today.getDate();
      var mm = today.getMonth() + 1; //January is 0!
      var yyyy = today.getFullYear();


      if (dd < 10) {
        dd = '0' + dd;
      }

      if (mm < 10) {
        mm = '0' + mm;
      } 

      this.max_date = yyyy + '-' + mm + '-' + dd;
      document.getElementById("duty_date").setAttribute("max", this.max_date);

      // min date is yesterday
      var date = new Date();
      date.setDate(date.getDate() - 1);
      dd = date.getDate();
      mm = date.getMonth() + 1; //January is 0!
      yyyy = date.getFullYear();


      if (dd < 10) {
        dd = '0' + dd;
      }

      if (mm < 10) {
        mm = '0' + mm;
      } 

      this.min_date = yyyy + '-' + mm + '-' + dd;
      document.getElementById("duty_date").setAttribute("min", this.min_date);

    },

    getRecords: function(keyword) {
      let _this = this;
      let token = localStorage.getItem("accessToken");

      const params = {
        start_date: this.today
       };

        axios.get('api/attendance', {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
            .then(function(response) {
                console.log(response.data);
                app.receive_records = response.data;

            })
            .catch(function(error) {
                console.log(error);
            });
    },

    getLeaveRecords: function(keyword) {
        axios.get('api/leave')
            .then(function(response) {
                console.log(response.data);
                app.leave_records = response.data;

            })
            .catch(function(error) {
                console.log(error);
            });
    },

    getHolidayRecords: function(keyword) {
        axios.get('api/holiday')
            .then(function(response) {
                console.log(response.data);
                app.holiday_records = response.data;

            })
            .catch(function(error) {
                console.log(error);
            });
    },

      reset: function() {
          
            this.today = '';
            this.type = '';
            this.location = '';
            this.remark = '';
            this.time = '';
            this.explanation = '';
            this.err_msg = '';

            this.getLocation();
            this.getToday();
            
        },
 
  }
});