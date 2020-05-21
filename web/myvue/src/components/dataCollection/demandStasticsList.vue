<template>
  <div class="demandAllotList_b">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv demandAllotList">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <backToTheUpPage class="MR_ten" :back="backStr"></backToTheUpPage>
                        <span class="coarseLine MR_ten"></span>需求单统计列表
                    </div>
                    <div class="purchaseContent" v-for="item in tableData.data.data">
                        <table style="width:100%;">
                            <thead>
                                <tr>
                                    <th width="150px;">需求单号({{item.demand_sn}})</th>
                                    <th width="150px;">需求量({{item.demand_goods_num}})</th>
                                    <!-- <th width="150px;">实际需求量({{item.final_demand_num}})</th> -->
                                    <th width="120px;">实采量({{item.real_buy_num}})</th>
                                    <th width="120px;">溢采量({{item.overflow_num}})</th>
                                    <th width="120px;">sku数({{item.sku_num}})</th>
                                    <th width="150px;">需求单满足率({{item.real_buy_rate}}%)</th>
                                </tr>
                            </thead>
                        </table>
                        <table v-if="item.purchase_info">
                            <tr>
                                <td width="150px;">采购期单号</td>
                                <td width="100px;">需求量</td>
                                <td width="100px;">可采量</td>
                                <td width="150px;">待采量</td>
                                <td width="100px;">实采量</td>
                                <td width="150px;">预采量</td>
                                <td width="100px;">预计分配数量</td>
                                <td width="100px;">采满率</td>
                                <td width="100px;">操作</td>
                            </tr>
                            <tr v-for="purchaseSn in item.purchase_info">
                                <td width="150px;">{{purchaseSn.purchase_sn}}</td>
                                <td width="100px;">{{purchaseSn.goods_num}}</td>
                                <td width="100px;">{{purchaseSn.may_buy_num}}</td>
                                <td width="150px;">{{purchaseSn.wait_buy_num}}</td>
                                <td width="100px;">{{purchaseSn.real_buy_num}}</td>
                                <td width="150px;">{{purchaseSn.predict_goods_num}}</td>
                                <td width="100px;">{{purchaseSn.final_buy_num}}</td>
                                <td width="100px;">{{purchaseSn.real_buy_rate}}%</td>
                                <td width="100px;">
                                    <i title="查看需求单对应采购期的批次列表" class="el-icon-view notBgButton" @click="viewPurchaseDetails(purchaseSn.purchase_sn,item.demand_sn)"></i>
                                    <i title="查看需求单对应采购期中的商品信息" class="el-icon-goods notBgButton" @click="viewGoodsDetails(purchaseSn.purchase_sn,item.demand_sn)"></i>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import backToTheUpPage from '@/components/UiAssemblyList/backToTheUpPage';
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      backToTheUpPage,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数 
      pagesize:15,//每页的数据条数
      backStr:'/misOrderStatisticsList',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
          vm.loading.close();
          if(vm.tableData.data.code=="1002"){
              vm.isShow=true;
          }
        //   if(vm.tableData.length==0){ 
        //       vm.isShow=true;
        //   }
    },
    //查看需求单对应采购期的批次列表 
    viewPurchaseDetails(purchase_sn,demand_sn){
        let vm=this;
        let switchoEvent=event;
        switchLoading(event);
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$demandRealPurchaseListURL+"?purchase_sn="+purchase_sn+"&demand_sn="+demand_sn,
             {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            switchoRiginally(switchoEvent)
            sessionStorage.setItem("purchaseData",JSON.stringify(res.data.data));
            vm.$router.push('/demandRealPurchaseList?purchase_sn='+purchase_sn+"&demand_sn="+demand_sn);
        }).catch(function (error) {
            switchoRiginally(switchoEvent)
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //查看需求单对应采购期中的商品信息
    viewGoodsDetails(purchase_sn,demand_sn){
        let vm=this;
        let switchoEvent=event;
        $(event.target).addClass("el-icon-loading");
        $(event.target).removeClass("el-icon-goods");
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$demandPurchaseGoodsDetailURL+"?purchase_sn="+purchase_sn+"&demand_sn="+demand_sn,
             {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            $(switchoEvent.target).addClass("el-icon-goods");
            $(switchoEvent.target).removeClass("el-icon-loading");
            sessionStorage.setItem("goodsData",JSON.stringify(res.data.data));
            vm.$router.push('/demandPurchaseGoodsDetail?purchase_sn='+purchase_sn+"&demand_sn="+demand_sn+"&isStastics=isStastics");
        }).catch(function (error) {
                switchoRiginally(switchoEvent)
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDataList()
    },
  }
}
</script>
<style>
.demandAllotList_b .purchaseTitle{
    font-weight: bold;
    position: relative;
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    /* font-weight:bold; */
    padding-left: 35px;
    margin-top: 20px;
}
.demandAllotList_b .purchaseTitle input{
    width: 200px;
    height: 20px;
    outline: none;
    -webkit-appearance: none;
    border-radius: 50px;
    -moz-border-radius: 50px;
    -webkit-border-radius: 50px;
    padding-left: 30px;
    margin-left: 16px;
}
.demandAllotList_b .purchaseTitle .search{
    position: absolute;
    left: 171px;
    top: 29px;
    color: #ccc;
}
.demandAllotList_b .purchaseContent{
    width: 100%;
    height: 100%;
    border:1px solid #ebeef5;
    border-radius: 10px;
    margin-bottom: 15px;
}
.demandAllotList_b .purchaseContent table{
    width:100%;
    height:100%;
    line-height: 50px;
    text-align: center;
}
.demandAllotList_b .status{
    display: inline-block;
    height: 30px;
    line-height: 30px;
    border-radius: 10px;
    cursor: pointer;
    float: right;
    margin-top: 10px;
    font-size: 20px;
    font-weight: bolder;
}
.demandAllotList_b .el-dialog__body{
    text-align: left;
}
.titleName{
    width: 19%;
    height: 50px;
    display: inline-block;
    line-height: 50px;
    vertical-align: 64px;
}
li{
    list-style: none;
}
.demandTitle{
    border-bottom: 1px solid #ebeef5;
    line-height: 50px;
    margin-left: 20px;
    margin-right: 20px;
}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
