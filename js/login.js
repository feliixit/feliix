new Vue({
  el: '#app',

  components: {
 
  },
  data(){
      return { 
         uid:'',
         password:''
       }
  },

  computed: {
    
  },

  created () {
    this.setCookie("jwt", '');
    this.setCookie("uid", '');
    localStorage.token = '';
  },

  watch: {

  },

  methods: {
    keymonitor: function(event) {
          if(event.key == "Enter"){
            app.checkLogin();
          }
        },

    checkLogin: function(){
      if (this.uid === '')
        return false;
      if(this.password === '')
        return false;

      var recaptcha = document.getElementById('recaptchaResponse');

      var form_Data = new FormData();

       form_Data.append('username', this.uid)
       form_Data.append('password', this.password)
       form_Data.append('recaptcha_response', recaptcha)

       const _this = this

      axios.post('api/login', form_Data)
        .then(function(response){
          if(response.data['error']){
            Swal.fire({
              text: response.data['error'],
              icon: 'error',
              confirmButtonText: 'OK'
            })
          }
          else{
            _this.setCookie("jwt", response.data['jwt']);
            _this.setCookie("uid", response.data['uid']);
            localStorage.token = response.data['jwt'];
            setTimeout(function(){
              window.location.href="default";
            },1000);
 
          }
        });
    },

    register:  function(obj){
      window.location.href = "create_user";
    },

    setCookie: function(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    },
  }

});


