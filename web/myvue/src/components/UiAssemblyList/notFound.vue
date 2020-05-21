<template>
    <div class="404Box">
        <div style="text-align: center;line-height: 50px;"><img class="notData" src="../../image/notData.png"/></div>
    </div>
</template>
<script>
export default {
    data () {
        return {
            
        }
     },
    methods:{
        myclick(){
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            // this.$emit('searchFrame',this.search)
        }
    }
}
</script>
<style>
</style>


