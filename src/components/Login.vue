<template>
  <div class="login">
    <div class="login-box">
      <p class="login-title">数据库字典</p>
      <div class="login-form">
          <div class="form-item">
            <input v-model="name" type="text" placeholder="请输入帐号">
          </div>
          <div class="form-item">
            <input v-model="password" type="password" placeholder="请输入密码">
          </div>
          <div class="form-item">
            <input @click="loginBtn" class="login-button" type="button" value="登录">
          </div>
      </div>
    </div>
    <Toast :toastText="toastText"></Toast>
  </div>
</template>

<script>
  const name='admin', password='eAoeC6anYJwHsFCAts3w'
  export default {
    name: "login",
    components: {},
    data(){
      return{
        name:'',
        password:'',
        toastText:'',
      }
    },
    methods:{
      loginBtn(){
        if(this.name===name && this.password===password){
          localStorage.setItem('name',this.name)
          this.$emit('switchTab','Dictionary')
        }else{
          this.toastText='请查看你的账户或者密码是否填写错误！'
          setTimeout(()=>{
            this.toastText=false
          },2000)
        }
      },
    },
  }
</script>

<style scoped>
  *{margin: 0;padding: 0}
  input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 1000px white inset !important;
  }
  .login {
    background: url("../assets/light_noise_diagonal.png");
    height: 100%;
  }

  .login-title{
    font-size: 28px;
    font-weight: 300;
    margin-bottom: 30px;
    text-align: center;
  }
  .login-box{
    margin: auto;
    width: 350px;
    height: 300px;
    padding: 50px 25px 50px;
    background: #fff;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%,-50%);
  }
  .login-button{
    background: #507aa4;
    color: #fff;
    cursor: pointer;
    margin-top: 30px;
  }
  .login-button:hover{
    background: #4b739a;
    border-color: #3c5c7b;
  }
  .login-button:active{
    background-color: #4b739a;
    border-color: #3c5c7b;
   }

  .login-form input {
    width: 100%;
    border-radius: 3px;
    height: 35px;
    outline: none;
    text-indent: 1em;
    border: 1px solid #ccc;
  }
  .form-item{
    margin-bottom: 20px;
  }
</style>
