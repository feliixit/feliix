var app = new Vue({
	el: '#app',
	data:{
		successMessage: "",
		errorMessage: "",
		registDetails: {username: '', email: '', password1: '', password2: '', g_recaptcha_response: ''},
		file: '',
	},
 
	methods:{
		keymonitor: function(event) {
       		if(event.key == "Enter"){
         		app.checkLogin();
        	}
       	},

       	onChangeFileUpload() {
            this.file = this.$refs.file.files[0];
            console.log(this.$refs.file.files[0]);
        },

       	sign_up: function(){
			if (app.registDetails.username === '')
			{
				this.$refs.username.focus();
				return false;
			}
			if(app.registDetails.email === '')
			{
				this.$refs.email.focus();
				return false;
			}
			if(!app.isEmail(app.registDetails.email))
			{
				this.$refs.email.focus();
				return false;
			}
			if (app.registDetails.password1 === '')
			{
				this.$refs.password1.focus();
				return false;
			}
			if(app.registDetails.password2 === '')
			{
				this.$refs.password2.focus();
				return false;
			}
			if (app.registDetails.password1 !== app.registDetails.password2)
			{
				this.$refs.password2.focus();
				return false;
			}

			var recaptcha = document.getElementById('recaptchaResponse');
			
	      	app.registDetails.recaptcha_response = recaptcha;

	        var logForm = app.toFormData(app.registDetails);

	        logForm.append('file', this.file);

			axios.post('api/create_user', logForm)
				.then(function(response){
 					console.log(response);
					if(response.data['error']){
						app.errorMessage = response.data['error'];
					}
					else{
						
						app.registDetails = {username: '', email: '', password1: '', password2: '', g_recaptcha_response: ''};
						app.successMessage = response.data['message'];
						setTimeout(function(){
							window.location.href="index";
						},1000);
 
					}
				});
		},

		setCookie: function(cname, cvalue, exdays) {
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires="+ d.toUTCString();
		    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		},
 

		cancel:  function(obj){
			window.location.href = "index";
		},
 
		toFormData: function(obj){
			var form_data = new FormData();
			for(var key in obj){
				form_data.append(key, obj[key]);
			}
			return form_data;
		},

		clearForm: function(){
			clearMessage();
			registDetails.username = '';
			registDetails.email = '';
			registDetails.password1 = '';
			registDetails.password2 = '';
			this.successMessage = "";
			this.errorMessage = "";
		},
 
		clearMessage: function(){
			app.errorMessage = '';
			app.successMessage = '';
		},

		isEmail: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
 
	}
});