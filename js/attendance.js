
var app = new Vue({
  el: '#app',
  data:{
    receive_records: [],
    leave_records:{},
    holiday_records:{},
  },

  created () {
    this.getRecords();
    this.getLeaveRecords();
    this.getHolidayRecords();
  },

  computed: {
    displayedRecord () {
      return this.receive_records;
    },

    displayedRecordSales () {
      return this.receive_records.filter(record => record.dept == 'Sales');
    },

    displayedRecordLighting () {
      return this.receive_records.filter(record => record.dept == 'Lighting');
    },

    displayedRecordOffice () {
      return this.receive_records.filter(record => record.dept == 'Office');
    },

    displayedRecordEngineering () {
      return this.receive_records.filter(record => record.dept == 'Engineering');
    },

    displayedRecordDesign () {
      return this.receive_records.filter(record => record.dept == 'Design');
    },

    displayedRecordAdmin () {
      return this.receive_records.filter(record => record.dept == 'Admin');
    },

    displayedRecordOthers () {
      // nort belong to any dept
      return this.receive_records.filter(record => record.dept != 'Sales' && record.dept != 'Lighting' && record.dept != 'Office' && record.dept != 'Engineering' && record.dept != 'Design' && record.dept != 'Admin');
    }
  },

  mounted(){
   
  },

  methods:{
    getRecords: function(keyword) {
        axios.get('api/attendance')
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