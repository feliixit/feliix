
var app = new Vue({
  el: '#app',
  data:{
    name: "",

    request_no:'',

    date_requested:'',
    request_type:'',
    project_name:'',
    payable_to:0,
    remark:'',
   
    receive_records: [],

    petty_list: [],

    submit: false,

    list_payee:'',
    list_particulars:'',
    list_price:0,
    list_qty:0,
  

    file: '',
  },

  created () {
    this.getRequestNo();

  },

  computed: {
    displayedRecord () {
      return this.receive_records;
    },

    showExtra: function(){
      return (this.leave_type=='B');
    },

    list_amonut: function() {
      return this.list_price * this.list_qty;
    },

    sum_amonut: function() {
      let sum = 0.0;
      for (i = 0; i < this.petty_list.length; i++) 
      {
        sum += this.petty_list[i].qty * this.petty_list[i].price;
      }
      return sum;
    },

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

    validateNumber: function(obj)
    {
      var number = obj;
      
      if(isNaN(number))
      {
        return false;
      }
      return true;
    },

    check_input:function(){
      if(this.list_payee.trim() == '')
      {
        Swal.fire({
          text: 'Please input Payee.',
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        return false;
      }

      if(this.list_particulars.trim() == '')
      {
        Swal.fire({
          text: 'Please input Particulars.',
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        return false;
      }

      if(this.list_price < 1 || !this.validateNumber(this.list_price))
      {
        Swal.fire({
          text: 'Please input Price.',
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        return false;
      }

      if(this.list_qty < 1 || !this.validateNumber(this.list_qty))
      {
        Swal.fire({
          text: 'Please input Quantity.',
          icon: 'warning',
          confirmButtonText: 'OK'
        })
        return false;
      }

      return true;
    },

    add_list: function() {
      if(this.check_input() == false)
        return;

      this.petty_list.push({is_checked : 0, payee : this.list_payee, particulars : this.list_particulars, price: this.list_price, qty: this.list_qty });
      this.list_payee = '';
      this.list_particulars = '';
      this.list_price = 0;
      this.list_qty = 0;
    },

    remove_list: function() {
      for (i = 0; i < this.petty_list.length; i++) 
      {
        if(this.petty_list[i].is_checked == 1)
        {
          this.petty_list.splice(i, 1);
          i = i - 1;
        }
      }
      
    },
    
    getRequestNo: function() {
      let _this = this;
      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        url: 'api/apply_for_petty_request_no',
      })
      .then(function(response) {
             _this.request_no = response.data.request_no;

            })
      .catch(function(error) {
              //handle error
        
            });

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


  reset: function() {

    this.list_payee='';
    this.list_particulars='';
    this.list_price=0;
    this.list_qty=0;
    this.petty_list = [];

    this.request_no='';

    this.date_requested='';
    this.request_type='';
    this.project_name='';
    this.payable_to=0;
    this.remark='';

    this.submit = false;
    this.getRequestNo();

  },

}
});