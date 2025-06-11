<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<style>
    *{
        padding:0;
        margin:0;
        list-style:none;
    }
    .wrap {
        width:100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding-top: 120px;
    }

    .main {
        display: flex;
        flex-direction: column;
        width: 90%;
        height: 540px;
        border: 2px solid;
        border-color: #CDC8C5;
        border-radius: 20px;
        justify-content: center;
        align-items: center;
    }
    .main .logo{
        width: 165px;
        height: 40px;
        padding-bottom : 30px;
    }

    .main .user {
        padding-bottom : 15px;
    }

    .main .user input{
        width: 280px;
        border-radius: 5px;
        line-height: 30px;
        border: 2px solid #CDC8C5;
        text-indent: 10px;
    }

    .main .password {
        padding-bottom : 15px;
    }
    .main .password input{
        width: 280px;
        border-radius: 5px;
        line-height: 30px;
        border: 2px solid #CDC8C5;
        text-indent: 10px;
    }
    .main .forgot{
        display: flex;
        padding-bottom : 15px;
        justify-content: flex-end;
        width: 280px;
    }

    .main .forgot input{
        color: #CDC8C5;
        border: none;
        font-weight: 500;
        background-color: #fff;
        font-size: 12px;
    }

    .main .submit{
        width: 100px;
        border-radius: 5px;
        line-height: 30px;
        border: none;
        font-weight: bold;
        color: #fff;
        background-color: #CDC8C5;
    }

    ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: #CDC8C5;
        opacity: 1; /* Firefox */
    }

    .main .submit:hover{
        cursor: pointer;
    }

    .main .forgot input:hover{
        cursor: pointer;
    }

</style>

<body>
    <div id="app">
        <div class="wrap">
            <div class="main">
                <div class="logo"><img src="images/ui/logo_dark.svg" alt=""></div>
                <div class="user"><input type="text" placeholder="Full Name" v-model='registDetails.username' ref="username"></div>
                <div class="user"><input type="email" placeholder="Email" v-model='registDetails.email' ref="email"></div>

                <div class="password"><input type="password" placeholder="Password" v-model='registDetails.password1' ref="password1"></div>
                <div class="password"><input type="password" placeholder="Password Confirm" v-model='registDetails.password2' ref="password2"></div>

               <!-- <div class="user"><input type="file" id="file" ref="file" v-on:change="onChangeFileUpload()" accept="image/*" capture="camera"></div> -->
                
                <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                <div><input type="button" class="submit" value="Sign Up" @click="sign_up();"> <input type="button" class="submit" value="Cancel" @click="cancel();"></div>
            </div>
        </div>
    </div>
</body>
<script defer src="js/npm/vue/dist/vue.js"></script> 
<script defer src="js/axios.min.js"></script> 
<script defer src="js/create_user.js"></script>
<script src="https://www.google.com/recaptcha/api.js?render=6Le2uvQUAAAAAOhI5CxFxFMMn1oiQCy5YZQFUu5j"></script>
<script>
    //grecaptcha.ready(function () {
    //        grecaptcha.execute('6Le2uvQUAAAAAOhI5CxFxFMMn1oiQCy5YZQFUu5j', { action: 'api/login.php' }).then(function (token) {
    //            var recaptchaResponse = document.getElementById('recaptchaResponse');
    //            recaptchaResponse.value = token;
    //        });
    //    });

</script>
</html>