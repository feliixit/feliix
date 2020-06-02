
var app = new Vue({
  el: '#app',
  data:{
    name: "",
    month1:'',
    month2:'',
    apply_start:'',
    apply_end:'',
    period : 0,
    leave_type : '',
    reason : '',
    receive_records: [],
    submit: false,
  },

  created () {
    this.getRecords();
    this.getUserName();
  },

  computed: {
    displayedRecord () {
      return this.receive_records;
    },

    showExtra: function(){
      return (this.leave_type=='B');
    }
  },

  mounted(){
   
  },

  watch: {
      apply_end () {

        this.setPeriod();
      },

      apply_start () {
        this.setPeriod();
      },
  },



  methods:{

    parseDate: function(str) {
        var mdy = str.slice(0, 10);
        return new Date(mdy);
    },

    sliceTime: function(str) {
        var mdy = str.slice(-5);
        return mdy;
    },

    sliceDate: function(str) {
        var mdy = str.slice(0, 10);
        return mdy;
    },

    validateForm() {
        if (this.period  <= 0) 
        {
          Swal.fire({
            text: 'Choose apply date',
            icon: 'error',
            confirmButtonText: 'OK'
          })
            //this.err_msg = 'Choose Punch Type';
            //$(window).scrollTop(0);
            return false;
        } 

         if (this.leave_type == "") 
        {
          Swal.fire({
            text: 'Choose leave type',
            icon: 'error',
            confirmButtonText: 'OK'
          })
            //this.err_msg = 'Choose Punch location';
            //$(window).scrollTop(0);
            return false;
        } 

         if (this.reason == "") 
        {
          Swal.fire({
            text: 'Please Input reason',
            icon: 'error',
            confirmButtonText: 'OK'
          })
            //this.err_msg = 'Choose Punch location';
            //$(window).scrollTop(0);
            return false;
        } 


        if (this.showExtra && !this.$refs.file.files[0])
        {
          Swal.fire({
            text: 'Sick leave Certificate of Diagnosis required',
            icon: 'error',
            confirmButtonText: 'OK'
          })
            //this.err_msg = 'Location Photo required';
            //$(window).scrollTop(0);
            return false;
        }

        return true;
      
    },

    apply: function() {

        if(!this.validateForm())
          return;

          this.submit = true;

          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;

          var sdate = this.sliceDate(this.apply_start);
          var stime = this.sliceTime(this.apply_start);

          var edate = this.sliceDate(this.apply_end);
          var etime = this.sliceTime(this.apply_end)

          form_Data.append('jwt', token);
          form_Data.append('leave_type', this.leave_type);
          form_Data.append('type', this.type);
          form_Data.append('start_date', sdate);
          form_Data.append('start_time', stime);
          form_Data.append('end_date', edate);
          form_Data.append('end_time', etime);
          form_Data.append('file', this.file);
          form_Data.append('leave', this.period);
          form_Data.append('reason', this.reason);
        
          axios({
                  method: 'post',
                  headers: {
                      'Content-Type': 'multipart/form-data'
                  },
                  url: 'api/apply_for_leave',
                  data: form_Data
              })
              .then(function(response) {
                  //handle success
                  Swal.fire({
                  text: response.data.message,
                  icon: 'success',
                  confirmButtonText: 'OK'
                })

                _this.reset();
 
              })
              .catch(function(error) {
                  //handle error
                  Swal.fire({
                  text: JSON.stringify(error),
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
              });

              this.submit = false;
      },

    setPeriod: function() {

      if (this.apply_start === undefined || this.apply_end === undefined)
        return;

      if (this.apply_start === '' || this.apply_end === '')
        return;

      var timeStart = this.parseDate(this.apply_start);
      var timeEnd = this.parseDate(this.apply_end);

      var days = Math.round((timeEnd-timeStart)/(1000*60*60*24)) + 1;

      //var timeStart = new Date(app.apply_start);
      //var timeEnd = new Date(app.apply_end);

      //var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds

      //var minutes = diff % 60;
     //var hours = (diff - minutes) / 60;

      if(!isNaN(days) && days > 0)
        this.period = days;
    },

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

      reset: function() {
          
            this.month1 = '';
            this.month2 = '';
            this.apply_start = '';
            this.apply_end = '';
            this.period = 0;
            this.leave_type = '';
            this.reason = '';
            this.submit = false;
            this.getRecords();
        },
 
  }
});