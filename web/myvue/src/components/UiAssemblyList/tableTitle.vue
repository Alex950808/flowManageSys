<template>
    <el-row class="tableTitleStyle">
        <el-col :span="12">
            <backToTheUpPage v-show="isBack" :back="backStr"></backToTheUpPage>
            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;{{tableTitle}}</span>
            <searchBox v-if="isSearch" @searchFrame='FFsearchFrame'></searchBox>
        </el-col>
        <el-col :span="12" v-if="isShow" class="fontRight">
            <span class="notBgButton" @click="FFAdd()"><i class="iconfont newAddIcon verticalAlign">&#xea22;</i>{{addStr}}</span>
        </el-col>
        <el-col :span="12" v-if="isDownload" class="fontRight">
            <upDataButton @GFFconfirmUpData="GFFconfirmUpData" :upDataStr="upDataStr"></upDataButton>
            <span class="notBgButton" @click="FFDownload()"><i class="el-icon-download newAddIcon verticalAlign"></i>{{downloadStr}}</span>
        </el-col>
    </el-row>
</template>
<script>
import searchBox from './searchBox';
import backToTheUpPage from './backToTheUpPage'
import upDataButton from './upDataButton'
export default {
    components:{
      searchBox,
      backToTheUpPage,
      upDataButton,
    },
    data () {
        return {
            addStr:'',
            downloadStr:'',
            backStr:'',
            upDataStr:'',
        }
     },
     props:{
        tableTitle:{
            type:String,
            required:true,
        },
        config:{
            type:String,
            required:true,
        }
    },
    props:['tableTitle','config'],
    methods:{
        //子组件中取名规范为FF+方法名称   FF:fatherFunction
        FFAdd(){
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            this.$emit('Add');
        },
        FFDownload(){
            this.$emit('Download');
        },
        FFsearchFrame(){
            this.$emit('searchFrame');
        },
        GFFconfirmUpData(formDate){
            this.$emit('confirmUpData',formDate);
        }
    },
    computed:{
        isShow:function(){
            let vm = this;
            let config=JSON.parse(vm.config)
            if(config[0].Add=='false'){
                return false;
            }else{
                vm.addStr=config[0].Add;
                return true;
            }
        },
        isDownload:function(){
            let vm = this;
            let config=JSON.parse(vm.config)
            if(config[1].download=='false'){
                return false;
            }else{
                vm.downloadStr=config[1].download;
                vm.upDataStr=config[4].upDataStr;
                return true;
            }
        },
        isSearch:function(){
            let vm = this;
            let config=JSON.parse(vm.config)
            if(config[2].search=='false'){
                return false;
            }else{
                return true;
            }
        },
        isBack:function(){
            let vm = this;
            let config=JSON.parse(vm.config)
            if(config[3].back=='false'){
                return false;
            }else{
                vm.backStr=config[3].back;
                return true;
            }
        }
    }
}
</script>
<style scoped>
/* 通用表格样式 */
.publicTable tr{
    line-height: 40px;
}
.publicTable tr th,td{
    border-top: 1px solid #ebeef5;
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5;
}
.publicTable tr:nth-child(even){
    background:#fafafa;
}
.publicTable tr:hover{
    background:#ced7e6;
}
.firstRowHeid td:first-child span{
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
</style>


