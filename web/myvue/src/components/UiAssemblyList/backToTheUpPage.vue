<template>
    <div class="backToTheUpPage">
        <span class="bgButton" @click="backToTheUpPage()">返回上一页</span>
    </div>
</template>
<script>
export default {
    data () {
        return {
        }
     },
     props:{
        back:{
            type:String,
            required:true,
        }
    },
    methods:{
        backToTheUpPage(){
            let vm=this;
            //当点击子组件的按钮的时候，如何拿到父组件传递过来的func方法，并调用这个方法
            // this.$emit('back');
            vm.$router.push(vm.back);
        }
    }
}
</script>
<style>
.backToTheUpPage{
    display: inline-block;
    position: relative;
}
</style>


