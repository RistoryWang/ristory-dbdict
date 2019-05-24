<template>
  <div id="app">
    <comment @switchTab="switchTab" :is="commentTab"></comment>
  </div>
</template>

<script type="text/ecmascript-6">
import Dictionary from './components/Dictionary'
import Login from './components/Login'
export default {
  name: 'app',
  data(){
    return{
      commentTab: "Login",
    }
  },
  components: {
	  Dictionary,
    Login,
  },
  created(){
    this.initUser()
  },
  methods:{
    switchTab(){
      this.commentTab='Dictionary'
    },
    initUser(){
      if(localStorage.getItem('name')){
        this.commentTab='Dictionary'
      }
    },
  }
}
</script>
<style>
#app {
	height: 100%;
}
</style>
