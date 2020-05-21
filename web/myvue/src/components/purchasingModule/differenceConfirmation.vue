<template>
  <div class="differenceConfirmation_b">
    <!-- 差异确认页面 -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="differenceConfirmation bgDiv">
                <el-row>
                    <el-col :span="22" :offset="1">
                        <el-row>
                            <el-col :span="10" class="title_left">
                                <span class="bgButton" @click="backUpPage()">返回上一级</span>
                                <span class="stage"></span>&nbsp;&nbsp;<span>{{title}}</span><span class="date"></span>
                            </el-col>
                            <el-col :span="14" class="title_right">
                                <div style="float: right;position: relative">
                                <input type="text" class="inputStyle" placeholder="请输入搜索关键字" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                                <span class="d_table" @click="d_table()">下载确认差异表</span>
                                </div>
                            </el-col>
                        </el-row>
                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <thead>
                                        <tr>
                                            <th :style="labelWidth()">商品标签</th>
                                            <th style="width:200px;">商品名称</th>
                                            <th style="width:150px">商品代码</th>
                                            <th style="width:150px">商家编码</th>
                                            <th style="width:150px">商品规格码</th>
                                            <th style="width:130px">商品采购数量</th>
                                            <th style="width:100px">清点数量</th>
                                            <th style="width:100px">差异数量</th>
                                            <th style="width:150px">物流备注</th>
                                            <th style="width:200px">采购备注</th>
                                        </tr>
                                    </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <tr v-for="(item,index) in tableData">
                                        <td :style="labelWidth()">
                                            <span v-for="labelInfo in item.goods_label_list" class="PR_ten">
                                                <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                            </span>
                                        </td>
                                        <td class="ellipsis" style="width:200px;" :title="item.goods_name"><span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span></td>
                                        <td style="width:150px">{{item.erp_prd_no}}</td>
                                        <td style="width:150px">{{item.erp_merchant_no}}</td>
                                        <td style="width:150px">{{item.spec_sn}}</td>
                                        <td style="width:130px">{{item.day_buy_num}}</td>
                                        <td style="width:100px">{{item.allot_num}}</td>
                                        <td style="width:100px">{{item.diff_num}}</td>
                                        <td style="width:150px">{{item.remark}}</td>
                                        <td class="cb_three" style="width:200px"><input type="text" @change="Remarks(item.spec_sn,index)" :name="index"/></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div  class="examine">
                            <span class="infoBgButton" style="background-color:#82848a" @click="confirmShow(1)">驳回差异</span>
                            <span class="bgButton" @click="confirmShow(6)">确认差异</span>
                            <input name="save" class="childBatch" id="save" type="checkbox"/>生成子批次单
                        </div>
                    </el-col>
                </el-row>
            </div>
        </el-col>
        <div class="confirmPopup_b" v-if="show">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要确认差异？
        <div class="confirm"><el-button @click="Reject()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { someDataLongByJson } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
      data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        title:'',
        is_Generate:'',
        search:'',//用户输入数据
        show:false,
        isInspect:'',//驳回记录1，确认记录2
        headersStr:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getDetailsData()
        // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        //获取详情数据
        getDetailsData(){
            let vm=this;
            let tableData=JSON.parse(sessionStorage.getItem("tableData"));
            tableStyleByDataLength(tableData.length,15)
            if(tableData=='1'){
            vm.getDetailsList();
                return false;
            };
            vm.tableData=tableData;
            sessionStorage.setItem("tableData",'1');
        },
        getDetailsList(){
            let vm = this;
            let headersToken=sessionStorage.getItem("token");
            vm.title=vm.$route.query.real_purchase_sn;//页面title显示上页带来的real_purchase_sn
            axios.post(vm.url+vm.$diffDetailURL+"?query_sn="+vm.search,
                {
                    "real_purchase_sn":vm.$route.query.real_purchase_sn,
                    "group_sn":vm.$route.query.group_sn,
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken, 
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.tableData=res.data.data;
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //搜索框
        searchFrame(){
            let vm=this;
            vm.getDetailsData();
        },
        //驳回差异/确认差异
        Reject(e){
            let vm=this;
            vm.show=false;
            var isGenerate=$("input[name=save]").prop("checked");
                if(isGenerate){
                    vm.is_Generate=1
                }else{
                    vm.is_Generate=0
                }
            axios.post(vm.url+vm.$changeDiffStatusURL,
                {
                    "real_purchase_sn":vm.$route.query.real_purchase_sn,
                    "status":vm.isInspect,
                    "create_child_sn":vm.is_Generate,
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "group_sn":vm.$route.query.group_sn,
                    "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                if(vm.isInspect==1){
                    vm.$message('驳回差异成功!');
                    if(vm.$route.query.isOverTime){
                        vm.$router.push('/overtimeDifference');
                    }
                    if(vm.$route.query.isConfirm){
                        vm.$router.push('/confirmDifference');
                    }
                }else if(vm.isInspect==6){
                    vm.$message('确认差异成功!');
                    if(vm.$route.query.isOverTime){
                        vm.$router.push('/overtimeDifference');
                    }
                    if(vm.$route.query.isConfirm){
                        vm.$router.push('/confirmDifference');
                    }
                }
                vm.isInspect='';
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //采购部单条备注
        Remarks(spec_sn,index){
            let vm=this;
            var allot_num=$(".cb_three input[name="+index+"]").val();
            axios.post(vm.url+vm.$addDiffRemarkURL,
                {
                    "real_purchase_sn":vm.$route.query.real_purchase_sn,
                    "spec_sn":spec_sn,
                    "purchase_remark":allot_num,
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "group_sn":vm.$route.query.group_sn,
                    "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
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
        //回上一级页面
        backUpPage(){
            let vm=this;
            if(vm.$route.query.isOverTime){
                vm.$router.push('/overtimeDifference');
            }
            if(vm.$route.query.isConfirm){
                vm.$router.push('/confirmDifference');
            }
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            let tmpWin=window.open(vm.url+vm.$downloadDiffListURL+'?real_purchase_sn='+vm.$route.query.real_purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&is_mother="+vm.$route.query.is_mother+'&token='+headersToken);
        },
        //确认弹框否
        determineIsNo(){
            let vm=this;
            vm.show=false;
        },
        //确认弹框是
        confirmShow(a){
            let vm=this;
            vm.isInspect=a;
            vm.show=true;
        },
        labelStyle(color){
            return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
        },
        labelWidth(){
            let vm = this;
            let width=someDataLongByJson(vm.tableData,'goods_label_list')*60;
            return "width:"+width+"px";
        },
        tableWidth(){
            let vm = this;
            let width = someDataLongByJson(vm.tableData,'goods_label_list')*60+1400;
            return "width:"+width+"px";
        },
    }
}
</script>
<style scoped>
.el-table{
    text-align: center;
}
.el-table th>.cell {
    text-align: center;
}
.el-table thead{
    font-weight: bold;
    color: #000;
    background-color: #ccc !important;
}
.el-table th{
    background-color: #ccc !important;
}
.childBatch{
    cursor: pointer;
}
.differenceConfirmation_b .ellipsis span{
    width: 200px;
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.differenceConfirmation_b .d_table{
    cursor: pointer;
}
.differenceConfirmation .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.differenceConfirmation .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.differenceConfirmation .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.differenceConfirmation .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.differenceConfirmation .dd{height:659px!important; height:659px; overflow-y:hidden;}
.differenceConfirmation .t_i{width:100%; height:auto;}
.differenceConfirmation .t_i_h{width:100%; overflow-x:hidden; background:#f5f7fa;}
.differenceConfirmation .ee{width:100%!important; width:100%; text-align: center;}
.differenceConfirmation .t_i_h table{width:1530px;}
.differenceConfirmation .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.differenceConfirmation .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.differenceConfirmation .cc table{width:1530px; }
.differenceConfirmation .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
.differenceConfirmation_b .confirmPopup_b{
    width: 100%;
    height: 100%;
    /* background-color:rgba(0, 0, 0, 0.2); */
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
}
.differenceConfirmation_b .confirmPopup{
  width: 400px;
  height: 189px;
  /* padding-top: 20px; */
  margin: 375px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  /* margin-top: 500px; */
  z-index: 999;
  background-color: #fff;
  /* border-radius: 20px; */
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.differenceConfirmation_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.differenceConfirmation_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.differenceConfirmation_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE;
    color: #fff; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.differenceConfirmation_b .confirmTitle{
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
.differenceConfirmation .el-icon-close{
    margin-left: 270px;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>