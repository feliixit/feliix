new Vue({
  el: '#app',

  components: {
 
  },
  data(){
      return { 
        token:'',
        password1:'',
        password2:'',
       }
  },

  computed: {
    
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
            case "token":
              _this.token = tmp[1];
              break;
          
          }
        }
      });
    }
  },

  watch: {

  },

  methods: {
    keymonitor: function(event) {
          if(event.key == "Enter"){
            app.new_password();
          }
        },

    new_password: function(){
      if (this.password1 === '' || this.password2 === '' )
        return false;

      if (this.password1 !== this.password2 )
      {
        Swal.fire({
            text: 'Password do not match',
            icon: 'warning',
            confirmButtonText: 'OK'
          })
          return false;
      }
       
      if (this.token.trim() === '')
      {
        Swal.fire({
            text: 'Please reset password by email',
            icon: 'warning',
            confirmButtonText: 'OK'
          })
          return false;
      }
     
      var recaptcha = document.getElementById('recaptchaResponse');

      var form_Data = new FormData();

       form_Data.append('password', this.password1)
       form_Data.append('token', this.token)

       const _this = this

      axios.post('api/new_password', form_Data)
        .then(function(response){
          if(response.data['error'] != ''){
            Swal.fire({
              text: response.data['error'],
              icon: 'error',
              confirmButtonText: 'OK'
            })
          }
          else{
            Swal.fire({
                text: 'Password changed.',
                icon: 'info',
                confirmButtonText: 'OK'
              })

              setTimeout(function(){
                window.location.href="index";
              },1000);
            
          }
        });
    },

  }

});


