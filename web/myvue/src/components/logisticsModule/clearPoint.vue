<template>
  <div class="clearPoint_b">
    <!-- 清点页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="clearPoint">
                        <el-row class="tableTitleStyle">
                            <el-col :span="10">
                                <span class="bgButton" @click="backUpPage()">返回上一级</span>
                                <span class="date">{{`${this.$route.query.purchase_sn}`}}</span>
                            </el-col>
                            <el-col :span="14" style="text-align: right;">
                                <div class="bgButton" @click="upDataBox()">批量上传</div>
                                <div style="float: right;" class="pos">
                                    <span class="bgButton ML_twenty MR_twenty" @click="d_table()">下载清点批次表</span>
                                </div>
                                
                            </el-col>
                        </el-row>
                        <el-row>
                            <div class="t_i">
                                <div class="t_i_h" id="hh">
                                    <div class="ee">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th style="width:200px;">商品标签</th>
                                                <th style="width:300px;">商品名称</th>
                                                <th style="width:150px">商品规格码</th>
                                                <th style="width:150px">商家编码</th>
                                                <th style="width:100px">实采数量</th>
                                                <th style="width:200px">清点数量</th>
                                                <th style="width:80px">差异值</th>
                                                <th style="width:100px">备注</th>
                                            </tr>
                                        </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="cc" id="cc" @scroll="scrollEvent()">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr v-for="(item,index) in tableData">
                                            <td style="width:200px;">
                                                <span v-for="labelInfo in item.goods_label_list" class="PR_ten">
                                                    <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                                </span>
                                            </td>
                                            <td class="overOneLinesHeid" style="width:300px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:150px">{{item.spec_sn}}</td>
                                            <td style="width:150px">{{item.erp_merchant_no}}</td>  
                                            <td style="width:100px">{{item.day_buy_num}}</td> 
                                            <td style="width:200px;" class="cb_one">
                                                <input type="text" style="width: 50%;" :name="index" :value="item.allot_num"/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<i style="color:green" @click="uploadNum(item.spec_sn,item.day_buy_num,index)" class="el-icon-success"></i>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<i @click="changeMode(index)" class="el-icon-edit"></i>
                                            </td>
                                            <td style="width:80px" class="cb_Three"><span>{{item.diff_num}}</span><input type="text" :name="index" style="display:none"/></td>
                                            <td style="width:100px" class="cb_two"><input style="width:70%;" type="text" @change="uploadNum(item.spec_sn,item.day_buy_num,index)" :name="index" :value="item.remark"/></td> 
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="fontCenter lineHeightSixty">
                                <span @click="confirmShow()" class="bgButton">确认清点</span>
                            </div>
                        </el-row>
                    </div>
                </el-col>
            </el-row>
        </el-col>
        <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
        <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        customConfirmationBoxes,
        upDataButtonByBox,
        fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        title:[],
        search:'',
        show:false,
        fileName:'',
        headersStr:'',
        contentStr:"请您确认是否要清点？",
        titleStr:'批量上传',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getclearPointData()
    },
    methods: {
        getclearPointData(){
            let vm=this;
            let tableData=JSON.parse(sessionStorage.getItem("tableData"));
            tableStyleByDataLength(tableData.length,15)
            if(tableData=='1'){
            vm.getclearPointList();
                return false;
            };
            vm.tableData=tableData;
            sessionStorage.setItem("tableData",'1');
            // vm.loading.close();
        },
        getclearPointList(){
            let vm=this;
            axios.get(vm.url+vm.$allotGoodsURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&is_mother="+vm.$route.query.is_mother+"&keywords="+vm.search,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.tableData=res.data.realPurchaseInfo.goods_list;
                tableStyleByDataLength(vm.tableData.length,15)
                vm.title={"CGQdanhao":res.data.realPurchaseInfo.purchase_sn,"SPdanhao":res.data.realPurchaseInfo.real_purchase_sn}
            }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //单表提交
        uploadNum(spec_sn,day_buy_num,index){
            let vm=this;
            var allot_num=$(".cb_one input[name="+index+"]").val();
            var remark=$(".cb_two input[name="+index+"]").val();
            if(parseInt(allot_num)>parseInt(day_buy_num)){
                vm.$message('清点数量必须小于或等于对应采购单的实采数量!');
                vm.getclearPointList();
                return false;
            }
            axios.post(vm.url+vm.$allotGoodsNumURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "real_purchase_sn":vm.$route.query.real_purchase_sn,
                    "spec_sn":spec_sn,
                    "allot_num":allot_num,
                    "remark":remark,
                    "group_sn":vm.$route.query.group_sn,
                    "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:vm.headersStr
                }
            ).then(function(res){
                if(res.data.code==2024){
                    $(".cb_Three input[name="+index+"]").prev().html(res.data.updateInfo.diff_num)
                    $(".cb_one  input[name="+index+"]").attr("disabled","true")
                }else{
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //修改input框为读写模式 
        changeMode(index){
            $(".cb_one  input[name="+index+"]").attr('disabled',false);
        },
        //搜索框
        searchFrame(){
            let vm=this;
            vm.getclearPointData();
        },
        //确认审核
        confirmationAudit(){
            let vm=this;
            $(".confirmPopup_b").fadeOut();
            axios.post(vm.url+vm.$sureAllotURL,
                {
                   "purchase_sn":vm.$route.query.purchase_sn,
                   "real_purchase_sn":vm.$route.query.real_purchase_sn, 
                   "group_sn":vm.$route.query.group_sn,
                   "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:vm.headersStr
                }
            ).then(function(res){
                if(res.data.code==2024){
                    vm.open()
                    vm.$router.push('/pendingPurchaseOrder');
                }else{
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("hh").scrollLeft=b;
        },
        //消息弹框
        open() {
            this.$message('确认审核成功');
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
             window.open(vm.url+vm.$downloadAllotGoodsURL+'?real_purchase_sn='+vm.$route.query.real_purchase_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&is_mother="+vm.$route.query.is_mother+'&token='+headersToken);
        },
        //回上一级页面
        backUpPage(){
            let vm=this;
            if(vm.$route.query.isPending){
                vm.$router.push('/pendingPurchaseOrder');
            }
            if(vm.$route.query.isTimeOutCheck){
                vm.$router.push('/timeoutCheck');
            }
        },
        //批量上传选择文件
        // SelectFile(){
        //     let vm=this;
        //     var r = new FileReader();
        //     var f = document.getElementById("file1").files[0];
        //     vm.fileName=f.name;
        //     r.readAsDataURL(f);
        //     var res = this.result;
        // },
        upDataBox(){
            $(".upDataButtonByBox_b").fadeIn();
        },
        // 确定批量上传文件
        GFFconfirmUpData(formDate){
            let vm=this;
            var formDate = new FormData($("#forms")[0]);
            vm.determineIsNo();
            // if(vm.fileName==''){
            //     vm.$message('请选择文件！');
            //     return false;
            // }
            $.ajax({
            url: vm.url+vm.$uploadAllotDataURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&is_mother="+vm.$route.query.is_mother,
            type: "POST",
            async: false,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.code==1000){
                    vm.$message('上传成功！');
                    vm.getclearPointList();
                }else{
                    vm.$message('上传失败！'+res.msg);
                }
            }
            });
        },
        //确认弹框否
        determineIsNo(){
            $(".confirmPopup_b").fadeOut();
        },
        //确认弹框是
        confirmShow(){
            $(".confirmPopup_b").fadeIn();
        },
        labelStyle(color){
            return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
        }
    }
}
</script>
<style scoped>
.clearPoint .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.title{
    /* height: 75px; */
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    padding-left: 35px;
    margin-top: 20px;
}
.clearPoint .t_i{width:100%; height:auto;}
.clearPoint .t_i_h{width:100%; overflow: auto;}
.clearPoint .ee{text-align: center;}
.clearPoint .t_i_h table{width:100%;}
.clearPoint .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.clearPoint .cc{width:100.6%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.clearPoint .cc table{width:100%; }
.clearPoint .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}

.clearPoint_b .file {
    position: relative;
    display: inline-block;
    text-align: center;
}
.clearPoint_b .file img{
  padding-top: 20px;
}
.clearPoint_b .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 5px;
}
.clearPoint_b .file input {
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;
}
.clearPoint_b .Detailed{
    display: inline-block;
    width: 115px;
    height: 50px;
    background-color: #4677c4;
    color: #fff;
    line-height: 50px;
    text-align: center;
    border-radius: 10px;
    -moz-border-radius: 10px;
    -webkit-border-radius: 10px;
    cursor: pointer;
}
.clearPoint_b .confirmTitle{
    display: inline-block;
    width: 400px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.titleText{
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.confirmPopup_b .el-icon-view{
    color: #fff;
}
.clearPoint_b .el-icon-close{
    margin-left: 270px;
}
.upLoad{
  margin-left: 20px;
  font-size: 29px;
}
</style>

<style scoped lang=less>
@import '../../css/logisticsModule.less';
</style>