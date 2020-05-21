<template>
  <div class="demandFundList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <el-row v-if="isHave">
                        <div class="lineHeightSixty ML_twenty">合单需求资金列表：</div>
                        <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>合期名称</th>
                                    <th>合期单号</th>
                                    <th>sku数</th>
                                    <th>总需求量</th>
                                    <th>总需求额</th>
                                    <th>可采数</th>
                                    <th>实采数</th>
                                    <th>缺口数</th>
                                    <th>缺口额</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in purchaseInfo">
                                    <td>{{item.sum_demand_name}}</td>
                                    <td>{{item.sum_demand_sn}}</td>
                                    <td>{{item.sku_num}}</td>
                                    <td>{{item.goods_num}}</td>
                                    <td>{{item.total_purchase_price}}</td>
                                    <td>{{item.may_num}}</td>
                                    <td>{{item.real_num}}</td>
                                    <td>{{item.diff_num}}</td>
                                    <td>{{item.diff_purchase_price}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <el-pagination class="tablePage" background @size-change="handleSizeChangePurchase" @current-change="handleCurrentChangePurchase"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="purchaseTotal">
                        </el-pagination>
                        <span class="lineHeighteighty ML_twenty">渠道资金列表：</span>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig'></tableContent>
                        <span class="lineHeighteighty ML_twenty">需求单剩余需求资金列表：</span>
                        <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>需求单号</th>
                                    <th>子单单号</th>
                                    <th>sku数</th>
                                    <th>需求量</th>
                                    <th>预采量</th>
                                    <th>预测分配数</th>
                                    <th>缺口数</th>
                                    <th>溢采量</th>
                                    <th>需求资金</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in DemandGoodsInfo">
                                    <td>{{item.demand_sn}}</td>
                                    <td>{{item.sub_order_sn}}</td>
                                    <td>{{item.sku_num}}</td>
                                    <td>{{item.goods_num}}</td>
                                    <td>{{item.predict_num}}</td>
                                    <td>{{item.may_allot_num}}</td>
                                    <td>{{item.diff_num}}</td>
                                    <td>{{item.overflow_num}}</td>
                                    <td>{{item.need_price}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-row>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound';
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      tableTitle,
      tableContent,
      notFound,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      demandInfo:[],
      purchaseInfo:[],
      DemandGoodsInfo:[],
      tableTitle:'需求资金列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"false"}]',
      tableContent:'',
      TCTitle:['资金渠道名称','人民币','美金','韩币','折合人民币','截止日期'],
      tableField:['fund_channel_name','cny','usd','krw','covert_cny','end_date'],//表格字段
      contentConfig:'[{"isShow":"false"},{"parameter":"false"}]',
      isShow:false,
      isHave:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //需求期列表分页
      purchaseTotal:0,
      purchasePagesize:1,//每页条数默认15条
      purchasePage:1,//page默认为1
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.getDemandGoodsFundList();
    this.getDemandPurchaseTaskList();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$demandFundListURL+"?page_size="+vm.purchasePagesize+"&page="+vm.purchasePage,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableContent=JSON.stringify(res.data.data.channel_fund_list);
                // vm.purchaseInfo=res.data.data.purchase_fund_list;
                // vm.purchaseTotal=res.data.data.purchase_fund_list.total_num;
                vm.isShow=false;
                vm.isHave=true;
            }else if(res.data.code==1002){
                vm.isShow=true;
                vm.isHave=false;
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
    //获取需求单剩余需求资金列表
    getDemandGoodsFundList(){
        let vm = this;
        axios.get(vm.url+vm.$demandGoodsFundListURL+"?page="+vm.page+"&page_size="+vm.pagesize,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.total=res.data.data.total;
            if(res.data.code==1000){
                vm.DemandGoodsInfo=res.data.data.data;
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
    //获取合单需求资金
    getDemandPurchaseTaskList(){
        let vm = this;
        axios.get(vm.url+vm.$demandPurchaseTaskListURL+"?page="+vm.page+"&page_size="+vm.pagesize,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.purchaseInfo=res.data.data.demand_list_info.data;
                vm.purchaseTotal=res.data.data.demand_list_info.total;
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
    //查看详情
    viewDetails(){
        let vm = this;
        let switchoEvent=event;
        switchLoading(event);
        axios.get(vm.url+vm.$fundListDetailURL+"?action=purchase",
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            switchoRiginally(switchoEvent)
            if(res.data.code==1000){
                sessionStorage.setItem("tableData",JSON.stringify(res.data.data.data_list));
                vm.$router.push('/fundListDetail');
            }else{
                vm.$message(res.data.msg);
            }
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
        vm.getDemandGoodsFundList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDemandGoodsFundList()
    },
    //采购期需求列表分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChangePurchase(val) {
        let vm=this;
        vm.purchasePagesize=val
        vm.getDataList()
    },
    handleCurrentChangePurchase(val) {
        let vm=this;
        vm.purchasePage=val
        vm.getDataList()
    },
    
  }
}
</script>

<style>
.editGoods{
    font-size: 22px !important;
    vertical-align: -5px;
    color: #ccc !important;
}
/* 通用表格样式 */
/* .tableStyle tr{
    line-height: 40px;
}
.tableStyle tr th,td{
    border-top: 1px solid #ebeef5;
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5;
}
.tableStyle tr:nth-child(even){
    background:#fafafa;
}
.tableStyle tr:hover{
    background:#f5f7fa;
} */
</style>
