<template>
  <div class="demandManageDetails_b">
      <el-col :span="24" style="background-color: #fff;">
            <div class="demandManageDetails bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <span class="bgButton" @click="backUpPage()">返回上一级</span>&nbsp;&nbsp;&nbsp;
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;订单详情</span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <span class="upTitle">收货人信息：</span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <el-row class="orderDt">
                            <el-col :span="8" :offset="1"><div><span>收货人姓名：{{titleData.receiver_name}}</span></div></el-col>
                            <el-col :span="8" :offset="1"><div><span>收货人电话：{{titleData.receiver_mobile}}</span>&nbsp;&nbsp;</div></el-col>
                        </el-row>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <span class="upTitle">订单信息：</span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <el-row class="orderDt">
                            <el-col :span="8" :offset="1"><div><span>订单状态&nbsp;&nbsp;&nbsp;：{{titleData.order_status}}</span>&nbsp;&nbsp;</div></el-col>
                            <el-col :span="8" :offset="1"><div><span>店铺名称&nbsp;&nbsp;&nbsp;：{{titleData.shop_name}}</span>&nbsp;&nbsp;</div></el-col>
                        </el-row>
                        <el-row class="orderDt">
                            <el-col :span="8" :offset="1"><div><span>erp订单号&nbsp;&nbsp;：{{titleData.trade_no}}</span>&nbsp;&nbsp;</div></el-col>
                            <el-col :span="8" :offset="1"><div><span>付款时间&nbsp;&nbsp;&nbsp;：{{titleData.pay_time}}</span>&nbsp;&nbsp;</div></el-col>
                        </el-row>
                        <el-row class="orderDt">
                            <el-col :span="8" :offset="1"><div><span>下单时间&nbsp;&nbsp;&nbsp;：{{titleData.trade_time}}</span>&nbsp;&nbsp;</div></el-col>
                        </el-row>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <span class="upTitle">商品信息：</span>
                    </div>
                    <div class="t_i">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th class="ellipsis" style="width:350px;">商品名称</th>
                                            <!-- <th rowspan="2">商品代码</th> -->
                                            <th style="width:130px">商家编码</th>
                                            <th style="width:130px">商品规格码</th>
                                            <!-- <th rowspan="2">商品总需求量</th> -->
                                            <th style="width:130px">商品数量</th>
                                            <th style="width:130px">成交价</th>
                                            <th style="width:130px">商品单价</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc" id="cc" @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tbody>
                                    <tr v-for="(item,index) in tableData">
                                        <td class="ellipsis" style="width:350px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <!-- <td>{{item.erp_prd_no}}</td> -->
                                        <td style="width:130px">{{item.erp_merchant_no}}</td>
                                        <td style="width:130px">{{item.spec_sn}}</td>
                                        <!-- <td>{{item.goods_num}}</td> -->
                                        <td style="width:130px">{{item.num}}</td>
                                        <td style="width:130px">{{item.order_price}}</td>
                                        <td style="width:130px">{{item.price}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- </div> -->
                    <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                </el-col>
            </div>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
export default {
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
        // isOrder:false,
        // isMISOrder:false,
        // //MIS数据
        // MIStableData:[],
        // MIStitleData:'',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getOrderDatailsData();
  },
  methods:{
    // getDatailsData(){
    //     let vm = this;
    //     if(vm.$route.query.isOrder=="isOrder"){
    //         vm.getOrderDatailsData();
    //         vm.isOrder=true;
    //     }else if(vm.$route.query.isMISOrder=="isMISOrder"){
    //         vm.getMISOrderDatailsData();
    //         vm.isMISOrder=true;
    //     }
    // },
    //获取列表数据
    getOrderDatailsData(){
        let vm = this;
        axios.get(vm.url+vm.$orderDetailURL+"?trade_no="+vm.$route.query.trade_no,
            {
              headers:vm.headersStr,
            }
        ).then(function(res){
            vm.tableData=res.data.goodsData;
            vm.titleData=res.data.orderData;
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    // getMISOrderDatailsData(){
    //     let vm = this;
    //     axios.post(vm.url+vm.$getMisOrderDetailURL,
    //         {
    //             "mis_order_sn":vm.$route.query.mis_order_sn
    //         },
    //         {
    //             headers:vm.headersStr,
    //         },
    //     ).then(function(res){
    //         vm.MIStableData=res.data.orderDetail.goodsInfo;
    //         vm.MIStitleData=res.data.orderDetail.orderInfo;
    //     }).catch(function (error) {
    //         if(error.response.status!=''&&error.response.status=="401"){
    //           vm.$message('登录过期,请重新登录!');
    //           sessionStorage.setItem("token","");
    //           vm.$router.push('/');
    //         }
    //     });
    // },
    //回上一级页面
    backUpPage(){
        let vm=this;
        // if(vm.$route.query.isOrder=="isOrder"){
            vm.$router.push('/getOrderList');  
        // }else if(vm.$route.query.isMISOrder=="isMISOrder"){
        //     vm.$router.push('/getMisOrderList');  
        // }
    },
  }
}
</script>

<style>
/* @import '../../css/common.css'; */
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
.demandManageDetails_b .ellipsis span{
    /* width: 350px; */
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
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
