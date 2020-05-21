<template>
    <div class="searchBox">
        <i class="iconfont searchIcon" @click="show = !show">&#xe604;</i>
        <transition name="slide-fade">
            <input type="text" v-if="show" class="inputStyle" placeholder="请输入查询关键字" v-model="search" @keyup.enter="myclick()"/>
        </transition>
    </div>
</template>
<script>
export default {
    data () {
        return {
            show:false,
            search:'',
        }
     },
    methods:{
        myclick(){
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            this.$emit('searchFrame',this.search)
        }
    }
}
</script>
<style>
.searchBox{
    display: inline-block;
    position: relative;
}
.searchBox .inputstyle{
    border-bottom: 1px solid #ccc;
    border-top: none;
    border-left: none;
    border-right: none;
}
.searchBox .search{
    position: absolute;
    top: 28px;
    left: 22px;
}
.searchBox .searchIcon{
    font-size: 19px;
    color: #4677c4;
    cursor: pointer;
    margin-left: 10px;
}
</style>


