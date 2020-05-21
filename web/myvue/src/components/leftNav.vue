<template>
    <div class="indexLeftNav">
        <el-row class="tac">
            <el-col :span="12">
                <el-menu :default-active="currentMenu" class="el-menu-vertical-demo" background-color="#e1eaf8"
                    text-color="#fff" active-text-color="#ffd04b" router>
                    <router-link to='/indexPage'>
                        <el-menu-item @click="recordRouter('indexPage','首页')" index="indexPage">
                            <img style="vertical-align: -4px;" src="../image/index.png"/>&nbsp;&nbsp;&nbsp;首页
                        </el-menu-item>
                    </router-link>
                    <el-submenu :index="item.display_name" v-for="(item,index) in jurisdictionList" :key="item.display_name">
                        <template slot="title">
                            <img v-if="item.id=='169'" src="../image/Finance.png"/>
                            <img v-if="item.id=='213'" src="../image/setting.png"/>
                            <img v-if="item.id=='203'" src="../image/Statistics.png"/>
                            <img v-if="item.id=='137'" src="../image/goods.png"/>
                            <img v-if="item.id=='256'" src="../image/audit.png"/>
                            <img v-if="item.id=='265'" src="../image/purchaseM.png"/>
                            <img v-if="item.id=='263'" src="../image/Sale.png"/>
                            <span style="margin-right:100px;">&nbsp;&nbsp;&nbsp;{{item.display_name}}</span>
                        </template>
                        
                        <!--
                        <el-submenu :index="(index+1)+'-'+(indexO+1)" v-for="(itemO,indexO) in item.child_info" :key="itemO.display_name" v-if="itemO.child_info!=undefined">
                            <template slot="title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{itemO.display_name}}</template>
                            <router-link :to="{name:itemT.web_name}" v-for="itemT in itemO.child_info" :key="itemT.display_name">
                                <el-menu-item :index="itemT.web_name" @click="recordRouter(itemT.web_name,itemT.display_name,itemT.child_info)">{{itemT.display_name}}</el-menu-item>
                            </router-link>
                        </el-submenu>
                        <router-link :to="{name:itemT.web_name}" v-for="itemT in item.child_info" :key="itemT.display_name" v-if="itemT.child_info==undefined">
                            <el-menu-item :index="itemT.web_name" @click="recordRouter(itemT.web_name,itemT.display_name,itemT.child_info)">{{itemT.display_name}}</el-menu-item>
                        </router-link> -->
                        <span v-for="(itemO,indexO) in item.child_info">
                            <el-submenu :index="(index+1)+'-'+(indexO+1)" :key="itemO.display_name" v-if="itemO.child_info!=undefined">
                                <template slot="title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{itemO.display_name}}</template>
                                <router-link :to="{name:itemT.web_name}" v-for="itemT in itemO.child_info" :key="itemT.display_name">
                                    <el-menu-item style="text-align: left;" :index="itemT.web_name" @click="recordRouter(itemT.web_name,itemT.display_name,itemT.child_info)">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        {{itemT.display_name}}
                                    </el-menu-item>
                                </router-link>
                            </el-submenu>
                            <router-link :to="{name:itemO.web_name}" :key="itemO.display_name" v-if="itemO.child_info==undefined">
                                <el-menu-item style="text-align: left;" :index="itemO.web_name" @click="recordRouter(itemO.web_name,itemO.display_name,itemO.child_info)">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{itemO.display_name}}</el-menu-item>
                            </router-link>
                        </span>
                    </el-submenu>
                </el-menu>
                
            </el-col>
        </el-row>
    </div>
</template>
<script>
import router from '../router'
import axios from 'axios'
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import store from '@/store/store'
export default {
    data () {
        return {
            currentMenu:'',
            navData:[],
            jurisdictionList:[],
            url: `${this.$baseUrl}`,
            webNameList:'',
        }
     },
     mounted(){
        this.getUrl();
        this.dataModule()
     },
    // created(){ 
    //     this.getUrl();
    // },
    methods:{
        handleOpen(key, keyPath) {
            let vm=this;
            vm.currentMenu=key;
        },
        getUrl(){
            let vm = this;
            let web_name=sessionStorage.getItem("web_name");
            vm.currentMenu=web_name;
        },
        recordRouter(web_name,display_name,child_info){
            let vm = this;
            sessionStorage.setItem("web_name",web_name);
            let webNameList=web_name;
            vm.webNameList={"web_name":display_name,"to":web_name};
            vm.$store.commit('webName', vm.webNameList);
            if(child_info!=undefined){
                sessionStorage.setItem("purchaseTaskList",JSON.stringify(child_info));
                sessionStorage.setItem("purchaseTaskListSetat",'1');
            }
            if(child_info==undefined){
                sessionStorage.setItem("purchaseTaskList",child_info);
                sessionStorage.setItem("purchaseTaskListSetat",'2');
            }
        },
        Refresh(){
            location.reload()
        },
        dataModule(){
            let vm=this;
            vm.jurisdictionList=JSON.parse(sessionStorage.getItem("navBar"));
        }
        // isUndefined(statu){
        //     console.log(statu)
        //     if(statu!=undefined){
        //         return true;
        //     }else{
        //         return false;
        //     } 
        // },
        // isNotUndefined(statu){
        //     if(statu==undefined){
        //         return true;
        //     }else{
        //         return false;
        //     }
        // }
     }
}
</script>


<style scoped lang=less>
@import '../css/index.less';
</style>
<style>
.el-menu-item.is-active {
   background-color: #ACC9DF !important;
   color: #fff !important;
   padding: 0 0 !important; 
}
.el-menu-item, .el-submenu__title {
    height: 45px !important;
    line-height: 45px !important;
    position: relative;
    padding: 0px;
    padding-left: 10px !important;
    -webkit-box-sizing: border-box;
    white-space: nowrap;
    list-style: none;
    color: #000 !important;
}
.el-submenu .el-menu-item {
    /* height: 70px;
    line-height: 70px; */
    /* padding: 0 45px !important; */
    /* padding-left: 0 !important; */
    height: 40px !important;
    line-height: 40px !important;
    text-align: center;
    /* min-width: 200px; */
    /* margin-left: 20px;
    margin-right: 20px; */
    /* border-radius: 50px; */
}
.el-submenu .el-submenu__title:hover{
        background-color: #f0f5fd !important;
}
</style>

