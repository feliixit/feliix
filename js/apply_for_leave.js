
var app = new Vue({
  el: '#app',
  data:{
    name: "",

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

    ab_taken: 0,
    ab_approval: 0,

    manager_leave: 0,

    is_manager: 0,

    file: '',
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

    if ($('#start').val()  === undefined)
        return;

      if ($('#start').val() === '')
        return;

      if ($('#end').val()  === undefined)
        return;

      if ($('#end').val() === '')
        return;


    var sdate1 = '';
    var edate1 = '';

    var sdate2 = '';
    var edate2 = '';

    if($('#start').val())
    {
      var d1 = new Date($('#start').val() + '-01');
      sdate1 = d1.toISOString().slice(0,10).replace(/-/g,"");
      var newDate1 = new Date(d1.setMonth(d1.getMonth()+1));
      edate1 = newDate1.toISOString().slice(0,10).replace(/-/g,"");
    }

    if($('#end').val())
    {
      var today = new Date($('#end').val());
      var d2 = new Date(today.getFullYear(), today.getMonth()+1, 0);

      sdate2 = today.toISOString().slice(0,10).replace(/-/g,"");
    
      edate2 = this.formatDate(d2).replace(/-/g,"");
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

      _this.ab_taken = response.data[0].ab_taken;
      _this.ab_approval = response.data[0].ab_approval;

      _this.manager_leave = response.data[0].manager_leave;

    })
    .catch(function(error) {
      console.log(error);
    });
  },

   formatDate: function(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
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

    if(this.apply_start === this.apply_end)
    {
      this.period = 0;
      return;
    }

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


  setUpLeavePeriod(){
    var d1 = new Date();

    var today = new Date();
    var d2 =  new Date(today.getFullYear(), today.getMonth()+1, 0);

    var d_manager =  new Date(today.getFullYear(), "11", 0);

    if(this.is_manager == 1)
    {
      if(d1 > d_manager)
      {
        $('#start').val(d1.toISOString().slice(0,4) + "-12");
        $('#end').val(d2.toISOString().slice(0,7));
      }
      else
      {
        var manager_start_date = new Date(today.getFullYear() -1, "12", 0);
        $('#start').val(manager_start_date.toISOString().slice(0,7));
        $('#end').val(d1.toISOString().slice(0,4) + "-11");
      }
    }
    else
    {
      $('#start').val(d1.toISOString().slice(0,4) + "-01");
      $('#end').val(d2.toISOString().slice(0,7));
    }

    this.getLeaveCredit();
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

            // for is manager period
            _this.setUpLeavePeriod();
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


    this.apply_start = '';
    this.apply_end = '';
    this.period = 0;
    this.leave_type = '';
    this.reason = '';
    this.submit = false;
    this.getLeaveCredit();
    this.getRecords();
  },

}
});