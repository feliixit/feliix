
var app = new Vue({
  el: '#app',
  data:{
    name: "",

    request_no:'',

    date_requested:'',
    request_type:'',
    project_name:'',
    payable_to:1,
    payable_other:'',
    remark:'',

    petty_list: [],

    submit: false,

    list_payee:'',
    list_particulars:'',
    list_price:0,
    list_qty:0,

  
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
      if (this.date_requested == '') 
      {
        Swal.fire({
          text: 'Choose request date',
          icon: 'warning',
          confirmButtonText: 'OK'
        })
            //this.err_msg = 'Choose Punch Type';
            //$(window).scrollTop(0);
            return false;
          } 

          if (this.request_type == "") 
          {
            Swal.fire({
              text: 'Choose request type',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Choose Punch location';
            //$(window).scrollTop(0);
            return false;
          } 

          if (this.project_name == "") 
          {
            Swal.fire({
              text: 'Please Input project name (reason)',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Choose Punch location';
            //$(window).scrollTop(0);
            return false;
          } 

          if (this.payable_to == 0) 
          {
            Swal.fire({
              text: 'Please select payable to',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Choose Punch location';
            //$(window).scrollTop(0);
            return false;
          } 

          if (this.payable_to == 2 && this.payable_other == "") 
          {
            Swal.fire({
              text: 'Please specific other payable',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Choose Punch location';
            //$(window).scrollTop(0);
            return false;
          } 

          if (!this.$refs.file.files[0])
          {
            Swal.fire({
              text: 'File Attachment required',
              icon: 'warning',
              confirmButtonText: 'OK'
            })
            //this.err_msg = 'Location Photo required';
            //$(window).scrollTop(0);
            return false;
          }

          if (!this.sum_amonut > 0)
          {
            Swal.fire({
              text: 'Petty list required',
              icon: 'warning',
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


          form_Data.append('jwt', token);
          form_Data.append('request_no', this.request_no);
          form_Data.append('date_requested', this.date_requested);
          form_Data.append('request_type', this.request_type);
          form_Data.append('project_name', this.project_name);
          form_Data.append('payable_to', this.payable_to);
          form_Data.append('payable_other', this.payable_other);
          form_Data.append('remark', this.remark);

          for( var i = 0; i < this.$refs.file.files.length; i++ ){
            let file = this.$refs.file.files[i];
            form_Data.append('files[' + i + ']', file);
          }
          
          form_Data.append('petty_list', JSON.stringify(this.petty_list));
         
          axios({
            method: 'post',
            headers: {
              'Content-Type': 'multipart/form-data'
            },
            url: 'api/apply_for_petty',
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
    this.payable_to=1;
    this.$refs.payable_other.style.display = 'none';
    this.payable_other='';

    this.remark='';

    this.submit = false;
    this.getRequestNo();

  },

}
});