<template>
  <div class="waitIntegralDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span class="bgButton ML_ten" @click="goBack()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>待返积分列表</span>
                        <span class="bgButton ML_ten" @click="submitIntegral()">提交积分</span>
                        <!-- <searchBox @searchFrame='searchFrame'></searchBox> -->
                    </div>
                    <el-row v-if="!isShow">
                        <div class="t_i fontCenter">
                            <!-- <div class="t_i_h" id="hh">
                                <div class="ee"> -->
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th class="widthThreeFiveHundred">商品名称</th>
                                                <th class="widthTwoHundred">商家编码</th>
                                                <th class="widthTwoHundred">商品规格码</th>
                                                <th class="widthTwoHundred">商品参考码</th>

                                                <th class="widthOneHundred">美金原价</th>
                                                <th class="widthOneHundred">实付美金</th>
                                                <th class="widthOneHundred">成本折扣</th>
                                                <!-- <th class="widthOneHundred">VIP折扣</th> -->

                                                <th class="widthOneHundred">实采数量</th>
                                                <th class="widthOneHundred">待返积分</th>
                                            </tr>
                                        </thead>
                                    <!-- </table>
                                </div>
                            </div> -->
                            <!-- <div class="cc MB_twenty" id="cc"  @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0"> -->
                                    <tbody>
                                        <tr v-for="item in tableData">
                                            <td class="overOneLinesHeid widthThreeFiveHundred"><span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span></td>
                                            <td class="widthTwoHundred">{{item.erp_merchant_no}}</td>
                                            <td class="widthTwoHundred">{{item.spec_sn}}</td>
                                            <td class="widthTwoHundred">{{item.erp_ref_no}}</td>

                                            <td class="widthOneHundred">{{item.spec_price}}</td>
                                            <td class="widthOneHundred">{{item.pay_price}}</td>
                                            <td class="widthOneHundred">{{item.cost_discount}}</td>
                                            <!-- <td class="widthOneHundred">{{item.vip_discount}}</td> -->

                                            <td class="widthOneHundred">{{item.day_buy_num}}</td>
                                            <td class="widthOneHundred">{{Number(item.total_integral).toFixed(2)}}</td> 
                                        </tr>
                                    </tbody>
                                </table>
                            <!-- </div> -->
                        </div>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      notFound,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      contentStr:'请您确认是否要提交待返积分?'
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
      //
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$waitIntegralDetailURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn+
            "&channels_method_sn="+vm.$route.query.channels_method_sn+"&purchase_sn="+vm.$route.query.purchase_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                tableStyleByDataLength(vm.tableData.length,15)
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //
    submitIntegral(){
        $(".confirmPopup_b").fadeIn();
    },
    //取消新增商品
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    //
    confirmationAudit(){
        let vm = this;
        $(".confirmPopup_b").fadeOut();
        axios.get(vm.url+vm.$submitIntegralURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn+
            "&channels_method_sn="+vm.$route.query.channels_method_sn+"&purchase_sn="+vm.$route.query.purchase_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=="1000"){
                // vm.getDataList();
                vm.$router.push('/waitIntegralList');
            }
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
    goBack(){
        let vm = this;
        vm.$router.push('/waitIntegralList');
    }
  }
}
</script>
<style>
.waitIntegralDetail .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.waitIntegralDetail .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.waitIntegralDetail .ee{width:100%!important; width:100%; text-align: center;}
.waitIntegralDetail .t_i_h table{width:100%;}
.waitIntegralDetail .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.waitIntegralDetail .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.waitIntegralDetail .cc table{width:100%; }
.waitIntegralDetail .cc table td{ text-align:center}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>