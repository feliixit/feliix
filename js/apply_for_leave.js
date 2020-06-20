
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

    al_credit: 0,
    al_taken: 0,
    al_approval: 0,

    sl_credit: 0,
    sl_taken: 0,
    sl_approval: 0,

    pl_taken: 0,
    pl_approval: 0,

    manager_leave: 0,

    is_manager: 0,

    file: '',
  },

  created () {
    this.getRecords();
    this.getUserName();
    this.getLeaveCredit();
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

      //this.setPeriod();
      this.getUserPeriod();
    },

    apply_start () {
      //this.setPeriod();
      this.getUserPeriod();
    },

    leave_type (){
      this.getUserPeriod();
    },

    month1 () {
      this.getLeaveCredit();
    },

    month2 () {
      this.getLeaveCredit();
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

    normalize: function(str) {
      var mdy = str.slice(-5).replace(/:/g,"");

      if(mdy < '0830')
        return str.slice(0, 10) + "T08:30";
      
      if(mdy > '1730')
        return str.slice(0, 10) + "T17:30";

      return str;

    },

    IsAm: function(str, is_apply_start) {
      var mdy = str.slice(-5).replace(/:/g,"");

      if(is_apply_start && mdy === '1230')
        return "P";

      if(!is_apply_start && mdy === '1230')
        return "A";

      if(mdy > '1230')
        return "P";
      else
        return "A";
    },

    onChangeFileUpload() {
          this.file = this.$refs.file.files[0];
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


          var timeStart = '';
          var amStart = 'A';
          var timeEnd = '';
          var amEnd = 'P';

          this.apply_start = this.normalize(this.apply_start);
          this.apply_end = this.normalize(this.apply_end);

          if(this.is_manager)
          {
            var timeStart = this.apply_start.slice(0, 10);

            var amStart = this.IsAm(this.apply_start, true);

            var timeEnd = this.apply_end.slice(0, 10);

            var amEnd = this.IsAm(this.apply_end, false);

          }
          else
          {
            var timeStart = this.apply_start.slice(0, 10);
            var timeEnd = this.apply_end.slice(0, 10);
          }

          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;

          var sdate = this.sliceDate(this.apply_start).replace(/-/g,"");
          var stime = this.sliceTime(this.apply_start);

          var edate = this.sliceDate(this.apply_end).replace(/-/g,"");
          var etime = this.sliceTime(this.apply_end)

          form_Data.append('jwt', token);
          form_Data.append('leave_type', this.leave_type);
          form_Data.append('start_date', sdate);
          form_Data.append('start_time', stime);
          form_Data.append('end_date', edate);
          form_Data.append('end_time', etime);
          form_Data.append('file', this.file);
          form_Data.append('leave', this.period);
          form_Data.append('reason', this.reason);

            form_Data.append('is_manager', this.is_manager);
            form_Data.append('timeStart', timeStart);
            form_Data.append('amStart', amStart);
            form_Data.append('timeEnd', timeEnd);
            form_Data.append('amEnd', amEnd);


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
                    text: JSON.stringify(response.data.message),
                    icon: 'warning',
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

          if(this.is_manager)
          {
            var timeStart = this.parseDate(this.apply_start);

            var amStart = this.IsAm(this.apply_start, true);

            var timeEnd = this.parseDate(this.apply_end);

            var amEnd = this.IsAm(this.apply_end, false);

            var days = Math.round((timeEnd-timeStart)/(1000*60*60*24)) + 1;

            if(!isNaN(days) && days > 0)
            {
              if(amStart === "P")
                days = days - .5;

              if(amEnd === "A")
                days = days - .5;

              this.period = days;
            }
          }
          else
          {
            var timeStart = this.parseDate(this.apply_start);
            var timeEnd = this.parseDate(this.apply_end);

            var days = Math.round((timeEnd-timeStart)/(1000*60*60*24)) + 1;

            if(!isNaN(days) && days > 0)
              this.period = days;
          }

      //var timeStart = new Date(app.apply_start);
      //var timeEnd = new Date(app.apply_end);

      //var diff = (timeEnd - timeStart) / 60000; //dividing by seconds and milliseconds

      //var minutes = diff % 60;
     //var hours = (diff - minutes) / 60;


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

  getLeaveCredit: function() {
    let _this = this;

    if (this.month1 === undefined && this.month2 === undefined)
      return;

    if (this.month1 === '' && this.month2 === '')
      return;

    var sdate1 = '';
    var edate1 = '';

    var sdate2 = '';
    var edate2 = '';

    if(this.month1)
    {
      var d1 = new Date(this.month1);
      sdate1 = d1.toISOString().slice(0,10).replace(/-/g,"");
      var newDate1 = new Date(d1.setMonth(d1.getMonth()+1));
      edate1 = newDate1.toISOString().slice(0,10).replace(/-/g,"");
    }

    if(this.month2)
    {
      var d2 = new Date(this.month2);
      sdate2 = d2.toISOString().slice(0,10).replace(/-/g,"");
      var newDate2 = new Date(d2.setMonth(d2.getMonth()+1));
      edate2 = newDate2.toISOString().slice(0,10).replace(/-/g,"");
    }

    axios.get('api/leave_credit?sdate1=' + sdate1 + '&edate1=' + edate1 + '&sdate2=' + sdate2 + '&edate2=' + edate2)
    .then(function(response) {
      console.log(response.data);
      _this.al_credit = response.data[0].al_credit;
      _this.al_taken = response.data[0].al_taken;
      _this.al_approval = response.data[0].al_approval;

      _this.sl_credit = response.data[0].sl_credit;
      _this.sl_taken = response.data[0].sl_taken;
      _this.sl_approval = response.data[0].sl_approval;

      _this.pl_taken = response.data[0].pl_taken;
      _this.pl_approval = response.data[0].pl_approval;

      _this.manager_leave = response.data[0].manager_leave;

    })
    .catch(function(error) {
      console.log(error);
    });
  },

  getUserPeriod: function() {
    if (this.apply_start === undefined || this.apply_end === undefined)
      return;

    if (this.apply_start === '' || this.apply_end === '')
      return;

    if(this.leave_type == "")
      return;

    if(this.apply_start > this.apply_end)
    {
      Swal.fire({
        text: JSON.stringify("Apply time no valid."),
        icon: 'warning',
        confirmButtonText: 'OK'
      })
      return;
    }

    this.apply_start = this.normalize(this.apply_start);
    this.apply_end = this.normalize(this.apply_end);

    var timeStart = '';
    var amStart = 'A';
    var timeEnd = '';
    var amEnd = 'P';


    if(this.is_manager)
    {
      var timeStart = this.apply_start.slice(0, 10);

      var amStart = this.IsAm(this.apply_start, true);

      var timeEnd = this.apply_end.slice(0, 10);

      var amEnd = this.IsAm(this.apply_end, false);

    }
    else
    {
      var timeStart = this.apply_start.slice(0, 10);
      var timeEnd = this.apply_end.slice(0, 10);
    }

    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    let _this = this;

    form_Data.append('jwt', token);
    form_Data.append('is_manager', this.is_manager);
    form_Data.append('timeStart', timeStart);
    form_Data.append('amStart', amStart);
    form_Data.append('timeEnd', timeEnd);
    form_Data.append('amEnd', amEnd);
    form_Data.append('leave_type', this.leave_type);

    axios({
      method: 'post',
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      url: 'api/leave_caculate',
      data: form_Data
    })
    .then(function(response) {
            //handle success
            _this.period = response.data.period;
          
          })
    .catch(function(response) {
            //handle error
            _this.period = 0;
            Swal.fire({
              text: JSON.stringify(response.data.message),
              icon: 'warning',
              confirmButtonText: 'OK'
            })
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
            _this.manager_leave = response.data.manager_leave;
            _this.al_credit = response.data.annual_leave;
            _this.sl_credit = response.data.sick_leave;

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