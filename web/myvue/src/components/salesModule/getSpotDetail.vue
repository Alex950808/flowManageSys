<template>
<!-- 现货单详情 -->
  <div class="demandManageDetails_b">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="demandManageDetails bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>现货单详情</span>
                    </div>
                    <el-row style="margin-bottom: 10px;">
                        <span class="upTitle">订单信息：</span>
                        <span class="bgButton" v-if="titleData.order_status!=6" @click="cancelSpot(2)">取消现货单</span>
                        <!-- <span class="bgButton" @click="pustToErp(1)">erp锁库</span> -->
                    </el-row>
                    <div style="margin-bottom: 10px;">
                        <el-row class="orderDt">
                            <el-col :span="8" :offset="1"><div><span>现货单号：{{titleData.spot_order_sn}}</span></div></el-col>
                            <el-col :span="8" :offset="1"><div><span>子订单号;：{{titleData.sub_order_sn}}</span></div></el-col>
                        </el-row>
                        <el-row class="orderDt">
                            <el-col :span="8" :offset="1">
                                <div>
                                    <span>订单状态：{{titleData.desc_status}}</span>
                                </div>
                            </el-col>
                            <el-col :span="8" :offset="1"><div><span>创建时间：{{titleData.create_time}}</span></div></el-col>
                        </el-row>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <span class="upTitle">商品信息：</span>
                    </div>
                    <el-row>
                    <div class="t_i">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th style="width:350px;">商品名称</th>
                                            <th style="width:140px">商品规格码</th>
                                            <th style="width:100px">需求量</th>
                                            <th style="width:130px">美金原价</th>
                                            <th style="width:130px">商品库存</th>
                                            <th style="width:130px">销售折扣</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc" id="cc">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tbody>
                                    <tr v-for="(item,index) in tableData">
                                        <td class="overOneLinesHeid" style="width:350px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;width:350px;">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td style="width:140px">{{item.spec_sn}}</td>
                                        <td style="width:100px">{{item.goods_number}}</td>
                                        <td style="width:130px">{{item.spec_price}}</td>
                                        <td style="width:130px">{{item.gsStockNum}}</td>
                                        <td style="width:130px">{{item.sale_discount}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </el-row>
                    <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                </el-col>
            </div>
      </el-col>
      <div class="confirmPopup_b" v-if="confirmShow">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        {{confirmStr}}
        <div class="confirm"><el-button  @click.once="labelledHeat()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        fontWatermark
    },
  data(){
    return{
        url: `${this.$baseUrl}`,
        tableData:[],
        titleData:'',//title数据
        search:'',//用户输入数据
        total:0,//页数默认为0 
        saleMarRate:'',
        spec_sn:'',
        radio:2,
        index:'',
        Cost:'',
        isShow:false,
        dialogVisible:false,
        headersStr:'',
        confirmStr:'',
        confirmShow:false,
        stateVal:'',//用来判断用户点击了erp锁库还是取消现货单
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
      this.getMISOrderDatailsData();
  },
  methods:{
    getMISOrderDatailsData(){
        let vm = this;
        axios.post(vm.url+vm.$getSpotDetailURL,
            {
                "spot_order_sn":vm.$route.query.spot_order_sn
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.subOrderDetail;
            vm.titleData=res.data.subOrderInfo;
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //打开取消现货单弹框 
    cancelSpot(e){
        let vm = this;
        vm.confirmShow=true;
        vm.confirmStr='请您确认是否取消现货单？';
        vm.stateVal=e;
    },
    //打开erp推送弹框
    pustToErp(e){
        let vm = this;
        vm.confirmShow=true;
        vm.confirmStr='请您确认是否推送erp进行锁库操作？';
        vm.stateVal=e;
    },
    //确认erp推送/取消现货单 
    labelledHeat(){
        let vm = this;
        let stateValStr;
        if(vm.stateVal=='1'){//1代表erp锁库，2代表取消现货单
            stateValStr=vm.$erpOrderPushURL;
        }else if(vm.stateVal=='2'){
            stateValStr=vm.$cancelSpotOrdURL;
        };
        axios.post(vm.url+stateValStr,
            {
                "spot_order_sn":vm.$route.query.spot_order_sn
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.confirmShow=false;
            if(res.data.code==2024){
                vm.$message(res.data.msg);
                if(vm.stateVal=='2'){
                    vm.getMISOrderDatailsData()
                }
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //取消推送
    determineIsNo(){
        let vm = this;
        vm.confirmShow=false;
    },
    //回上一级页面
    backUpPage(){
        let vm=this;
        // if(vm.$route.query.isOrder=="isOrder"){
        //     vm.$router.push('/getOrderList');  
        // }else if(vm.$route.query.isMISOrder=="isMISOrder"){
            vm.$router.push('/getSpotList');  
        // }
    },
  }
}
</script>

<style>
.demandManageDetails_b .title{
    margin-bottom: 30px;
}
.demandManageDetails_b .back{
    display: inline-block;
    width: 200px;
    height: 50px;
    background-color: #00C1DE;
    color: #fff;
    line-height: 50px;
    text-align: center;
    margin-top: 25px;
    border-radius:10px; 
    cursor: pointer;
}
.demandManageDetails_b .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
.demandManageDetails_b .el-icon-edit{
    float: right;
    margin-top: 10px;
}
.demandManageDetails_b .bg-purple{
    line-height: 40px;
}
.orderDt{
    color: #a7a7a7;
    height: 50px;
    line-height: 50px;
}
.demandManageDetails_b .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ebeef5; border-left:1px solid #ebeef5}
.demandManageDetails_b .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ebeef5; width:305px; height:43px}
.demandManageDetails_b .t_number{border-right:1px solid #ebeef5; width:100%; margin-bottom:5px}
.demandManageDetails_b .t_number td{border-bottom:1px solid #ebeef5;width: 293px;height: 40px; text-align:center}
.demandManageDetails_b .dd{ overflow-y:hidden;}
.demandManageDetails_b .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.demandManageDetails_b .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.demandManageDetails_b .ee{width:100%!important; width:100%; text-align: center;}
.demandManageDetails_b .t_i_h table{width:100%;}
.demandManageDetails_b .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.demandManageDetails_b .cc{width:100%; border-bottom:1px solid #ebeef5; background:#fff; overflow:auto;}
.demandManageDetails_b .cc table{width:100%; }
.demandManageDetails_b .cc table td{ text-align:center}

</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
