new Vue({
  el: '#app',

  components: {
 
  },
  data(){
      return { 
        email:'',
        
       }
  },

  computed: {
    
  },

  created () {

  },

  watch: {

  },

  methods: {
    keymonitor: function(event) {
          if(event.key == "Enter"){
            app.forget_password();
          }
        },

    forget_password: function(){
      if (this.email === '')
        return false;
     
      var recaptcha = document.getElementById('recaptchaResponse');

      var form_Data = new FormData();

       form_Data.append('email', this.email)
       form_Data.append('recaptcha_response', recaptcha)

       const _this = this

      axios.post('api/forget_password', form_Data)
        .then(function(response){
          if(response.data['error']){
            Swal.fire({
              text: response.data['error'],
              icon: 'error',
              confirmButtonText: 'OK'
            })
          }
          else{
         
              setTimeout(function(){
                window.location.href="pending";
              },1000);
            
 
          }
        });
    },

  }

});


