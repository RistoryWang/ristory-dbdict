<template>
  <transition name="fade">
  <div class="toast" v-if="toastText">
    <div class="toast-content">
     {{toastText}}
    </div>
  </div>
  </transition>
</template>

<script>
  export default {
    name: "Toast",
    data(){
      return{

      }
    },
    created(){

    },
    props:['toastText'],
    computed:{

    },
  }
</script>

<style scoped>
  .toast-content {
    padding: 15px 20px;
    border-radius: 3px;
    line-height: 24px;
    font-size: 14px;
    background-color: rgba(0, 0, 0, .6);
    color: #fff;
  }
.toast {
  position: absolute;
  z-index: 99;
  width: 100%;
  height: 100%;
  display: flex;
  display: -webkit-flex;
  justify-content: center;
  align-items: center;
}
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
  }
  .fade-enter, .fade-leave-to {
    opacity: 0;
  }
</style>
