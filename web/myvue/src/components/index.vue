<template>
    <div class="index">
        <headr></headr>
        <div style="clear: both;"></div>
        <el-row class="indexText">
            <el-col :span="6">
                <div class="leftNav">
                    <leftNav></leftNav>
                </div>
            </el-col>
            <el-col :span="24">
                <div class="Notice" style="margin-left: 255px; margin-top:10px;margin-right:10px;background-color: #ACC9DF;color: #fff;border-radius: 10px;">
                    <el-row>
                    <el-col :span="2">
                        <span class="gonggao">公告：</span>
                    </el-col>
                    <el-col :span="22">
                        <marquee style="height: 52px;" loop="infinite" behavior="scroll" scrollamount="3" direction='left'>
                            <div v-for="item in loggerList" style="height: 50px;line-height: 52px;">
                                <!-- <el-tooltip class="item d_I_B" effect="light" :content="'操作人名称 ：'+item.admin_name+'|业务描述 ：' +item.bus_desc+'|操作时间：'+item.create_time" placement="right"> -->
                                    <span class="ML_twenty MR_twenty">
                                        <!-- <img src="../image/logo_GG.png" style="width:20px;height:20px;">操作人名称 ：{{item.admin_name}}<img src="../image/logo_GG.png" style="width:20px;height:20px;"> -->
                                        <!-- 业务描述 ： {{item.bus_desc}}<img src="../image/logo_GG.png" style="width:20px;height:20px;">
                                        操作时间 ：{{item.create_time}} -->
                                    </span>
                                <!-- </el-tooltip> -->
                            </div>
                        </marquee>
                    </el-col>
                    </el-row>
                </div>
                <div class="yeqian" style="margin-left: 255px; margin-top:10px;margin-right:10px;">
                    <el-col :span="24" class="newTabNav fontLift overTwoLinesHeid witeBg MB_ten">
                        <span @click="toOtherTab('indexPage')" class="bgButton MR_twenty PR_twenty PR">首页</span>
                        <span v-for="item in returnWebName" class="bgButton MR_twenty PR_twenty PR">
                            <span @click="toOtherTab(item.to)">{{item.web_name}}</span>
                            <i @click="closeOtherTab(item.web_name)" class="el-icon-error PA"></i>
                        </span>
                    </el-col>
                </div>
                <div style="clear:both"></div>
                <div class="rightText">
                    <router-view/>
                </div>
                <div style="clear:both"></div>
                <el-row class="dixian" style="margin-left: 255px;margin-right:10px;">
                    <el-col :span="24">
                        <div class="lineHeightSixty Baseline grayFont">
                            <span class="el-icon-minus"></span>
                            我们是有底线的
                            <span class="el-icon-minus"></span>
                        </div>
                    </el-col>
                </el-row>
            </el-col>
            <div class="openOrClose">
                <i class="el-icon-s-fold" @click="openOrClose();pageWidthAdaptation()"></i>
            </div>
        </el-row>
    </div>
</template>
<script>
import headr from './headr'
import leftNav from './leftNav'
import { webName } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import store from '@/store/store'
export default {
    components:{
        headr,
        leftNav
    },
    data () {
        return {
            index:1,
            loggerList:[],
        }
    },
    mounted(){
         this.Notice();
    },
    methods:{
        openOrClose(){
            let vm=this;
            if(vm.index==1){
                $(".leftNav").hide();
                $(".yeqian").css("margin-left","20px");
                $(".rightText").css("margin-left","20px");
                $(".dixian").css("margin-left","20px");
                $(".Notice").css("margin-left","20px");
                // $(".openOrClose .iconfont").css("color","#000");
                $(".openOrClose").css("left","0");
                $(".openOrClose i").removeClass("el-icon-s-fold")
                $(".openOrClose i").addClass("el-icon-s-unfold")
                vm.index=2;
            }else{
                $(".leftNav").show();
                $(".rightText").css("margin-left","255px");
                $(".dixian").css("margin-left","255px");
                $(".yeqian").css("margin-left","255px");
                $(".Notice").css("margin-left","255px");
                // $(".openOrClose .iconfont").css("color","#fff");
                $(".openOrClose").css("left","189px");
                $(".openOrClose i").removeClass("el-icon-s-unfold")
                $(".openOrClose i").addClass("el-icon-s-fold")
                vm.index=1;
            }
        },
        pageWidthAdaptation(){//此方法只是临时用来适配页面（现货单列表，mis订单列表及子单列表的fixed的width）不做其他用途
            $(".getMisOrderList .tableTitleTwo").width($(".getMisOrderList .select").width());
            $(".getSubList .tableTitleTwo").width($(".getSubList .select").width());
            $(".getSpotList .tableTitleTwo").width($(".getSpotList .select").width());
            $(".commodity .tableTitleTwo").width($(".commodity").width());
        },
        toOtherTab(to){
            let vm = this;
            vm.$router.push('/'+to+'');
        },
        closeOtherTab(web_name){
            let vm = this;
            vm.$store.commit('reduceWebName',web_name);
            let to = (vm.$store.state.webNameList[vm.$store.state.webNameList.length-1]).to;
            vm.$router.push('/'+to+'');
        },
        //
        Notice(){
            let vm = this;
            vm.loggerList=JSON.parse(sessionStorage.getItem("loggerList"))
        }
    },
    computed:{
        returnWebName(){
            let vm = this;
            return vm.$store.state.webNameList
        }
    }
}
</script>
<style>
.openOrClose .iconfont{
    font-size:26px;
    font-style:normal;
    color: #fff;
}
.openOrClose{
    position: fixed;
    top: 85px;
    left: 198px;
    z-index: 1000;
    cursor: pointer;
}
.Baseline{
    background-color: #ebeef5;
    
    margin-top: 20px;
}
.newTabNav{
    padding-left: 20px;
    line-height: 50px;
    border:1px solid #fff;
    border-radius: 10px;
}
.gonggao{
    display: inline-block;
    height: 52px;
    line-height: 52px;
    font-size: 20px;
}
</style>
<style scoped lang=less>
@import '../css/index.less';
</style>


