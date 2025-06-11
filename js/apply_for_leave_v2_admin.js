
var app = new Vue({
  el: '#app',
  data:{
    name: "",

    apply_start:'',
    apply_end:'',
    period : 0,

    sil_consume: 0,
    vl_consume:0,
    sl_consume:0,
    vlsl_consume:0,

    leave_type : '',
    reason : '',
    receive_records: [],
    submit: false,

    sil_credit: 0,
    sil_taken: 0,
    sil_approval: 0,

    vlsl_credit: 0,
    vlsl_taken: 0,
    vlsl_approval: 0,

    vl_credit: 0,
    vl_taken: 0,
    vl_approval: 0,

    sl_credit: 0,
    sl_taken: 0,
    sl_approval: 0,

    message: '',

    ul_taken: 0,
    ul_approval: 0,

    manager_leave: 0,

    is_manager: 0,

    leave_level: '',

    file: '',

    min_start_date: '',
    max_start_date: '',

    uid : 0,

  },

  created () {

    let _this = this;
      let uri = window.location.href.split("?");
      if (uri.length >= 2) {
        let vars = uri[1].split("&");

        let tmp = "";
        vars.forEach(async function(v) {
          tmp = v.split("=");
          if (tmp.length == 2) {
            switch (tmp[0]) {
              case "uid":
                _this.uid = tmp[1];
                _this.getRecords();
                _this.getUserName(); 
                break;
              default:
                console.log(`Too many args`);
            }
          }
        });
      }

    
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
let _this = this;

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

          if(this.leave_type == 'H' && this.period != 0.5)
          {
            Swal.fire({
              text: 'When using Vacation Leave --- Manager Halfday Planning, leave length per application is only allowed to be halfday.',
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

          if(this.message != "") 
          {
            Swal.fire({
              text: this.message,
              icon: 'error',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Location Photo required';
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

          
          var message = _this.sil_consume > 0 ? "Service Incentive Leave: " + _this.sil_consume + " day(s)<br>" : "";
          message += _this.vl_consume > 0 ? "Vacation Leave: " + _this.vl_consume + " day(s)<br>" : "";
          message += _this.sl_consume > 0 ? "Sick Leave: " + _this.sl_consume + " day(s)<br>" : "";
          message += _this.vlsl_consume > 0 ? "Vacation Leave/Sick Leave: " + _this.vlsl_consume + " day(s)<br>" : "";
          message = "Your current leave application will consume<br>" + message + "<br>Do you want to continue?";

          if(this.leave_type == 'U') message = "Confirm to apply?";
            
          if(this.leave_type == 'H') message = "Your current leave application will consume<br>Manager Halfday Planning: 0.5 day(s)<br><br>Do you want to continue?";

          // sweet alter to confirm submit yes no
          Swal.fire({
            title: 'Sure to Apply',
            html: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
          }).then((result) => {
            if (result.value) {
              _this.apply();
            }
          })


        },

        apply: function() {

 
          this.submit = true;


          var timeStart = '';
          var amStart = 'A';
          var timeEnd = '';
          var amEnd = 'P';

          this.apply_start = this.normalize(this.apply_start);
          this.apply_end = this.normalize(this.apply_end);


          // if(this.leave_level == 'B' || this.leave_level == 'C' || this.leave_type == 'H' || this.leave_type == 'U')
          if(this.leave_level == 'B' || this.leave_level == 'C' || this.leave_type == 'H')
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

            form_Data.append('uid', this.uid);


          axios({
            method: 'post',
            headers: {
              'Content-Type': 'multipart/form-data'
            },
            url: 'api/apply_for_leave_v2_admin',
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
                    text: JSON.stringify(error.data.message),
                    icon: 'warning',
                    confirmButtonText: 'OK'
                  })
                });

          this.submit = false;
        },

   getRecords: function() {
    axios.get('api/attendance_admin?uid=' + this.uid)
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

    axios.get('api/leave_credit_v2_admin?uid='+ this.uid + '&sdate1=' + sdate1 + '&edate1=' + edate1 + '&sdate2=' + sdate2 + '&edate2=' + edate2)
    .then(function(response) {
      console.log(response.data);
      _this.sil_credit = response.data[0].sil_credit;
      _this.sil_taken = response.data[0].sil_taken;
      _this.sil_approval = response.data[0].sil_approval;

      _this.vlsl_credit = response.data[0].vl_sl_credit;
      _this.vlsl_taken = response.data[0].vl_sl_taken;
      _this.vlsl_approval = response.data[0].vl_sl_approval;

      _this.vl_credit = response.data[0].vl_credit;
      _this.vl_taken = response.data[0].vl_taken;
      _this.vl_approval = response.data[0].vl_approval;

      _this.sl_credit = response.data[0].sl_credit;
      _this.sl_taken = response.data[0].sl_taken;
      _this.sl_approval = response.data[0].sl_approval;

      _this.halfday_credit = response.data[0].halfday_credit;
      _this.halfday_taken = response.data[0].halfday_taken;
      _this.halfday_approval = response.data[0].halfday_approval;

      _this.ul_taken = response.data[0].ul_taken;
      _this.ul_approval = response.data[0].ul_approval;

      _this.leave_level = response.data[0].leave_level;
      
      _this.message = response.data[0].message;

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


    // if(this.leave_level == 'B' || this.leave_level == 'C' || this.leave_type == 'H' || this.leave_type == 'U')
    if(this.leave_level == 'B' || this.leave_level == 'C' || this.leave_type == 'H')
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

    form_Data.append('uid', this.uid);

    axios({
      method: 'post',
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      url: 'api/leave_caculate_v2_admin',
      data: form_Data
    })
    .then(function(response) {
            //handle success
            _this.period = response.data.period;
            _this.sil_consume = response.data.sil_consume;
            _this.vlsl_consume = response.data.vl_sl_consume;
            _this.vl_consume = response.data.vl_consume;
            _this.sl_consume = response.data.sl_consume;

            _this.message = response.data.message;
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
    var d_next_year =  new Date(today.getFullYear() + 1, 10, 0);

    // if(this.leave_level == 'B' || this.leave_level == 'C' || this.leave_type == 'H' || this.leave_type == 'U')
    if(this.leave_level == 'B' || this.leave_level == 'C' || this.leave_type == 'H')
    {
      if(d1 > d_manager)
      {
        $('#start').val(d1.toISOString().slice(0,4) + "-12");
        

        this.min_start_date = d1.toISOString().slice(0,4) + "-12-01 00:00:00";

        this.max_start_date = d_next_year.toISOString().slice(0,4) + "-11-30 23:59:59";
       
        $('#end').val(d_next_year.toISOString().slice(0,4) + "-11");
      }
      else
      {
        var manager_start_date = new Date(today.getFullYear() -1, "12", 0);
        $('#start').val(manager_start_date.toISOString().slice(0,7));
        

        this.min_start_date = manager_start_date.toISOString().slice(0,7) + '-01 00:00:00';

        this.max_start_date = today.toISOString().slice(0,4) + "-11-30 23:59:59";

        $('#end').val(today.toISOString().slice(0,4) + "-11");
      }
    }
    else
    {
      $('#start').val(d1.toISOString().slice(0,4) + "-01");
      

      this.min_start_date = d1.toISOString().slice(0,4) + "-01-01 00:00:00";

      // max start date is last year's nov 30
      this.max_start_date = d1.toISOString().slice(0,4) + "-12-31 23:59:59";

      $('#end').val(d1.toISOString().slice(0,4) + "-12");
    }

    this.getLeaveCredit();
  },


  getUserName: function() {
    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    let _this = this;

    form_Data.append('jwt', token);
    form_Data.append('uid', this.uid);

    axios({
      method: 'post',
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      url: 'api/on_duty_get_myname_admin',
      data: form_Data
    })
    .then(function(response) {
            //handle success
            _this.name = response.data.username;
            _this.is_manager = response.data.is_manager;
            _this.manager_leave = response.data.manager_leave;
            //_this.al_credit = response.data.annual_leave;
            //_this.sl_credit = response.data.sick_leave;

            _this.leave_level = response.data.leave_level;

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

    this.sil_consume = 0;
    this.vlsl_consume = 0;
    this.vl_consume = 0;
    this.sl_consume = 0;

    this.leave_type = '';
    this.reason = '';
    this.submit = false;
    this.getLeaveCredit();
    this.getRecords();
  },

}
});