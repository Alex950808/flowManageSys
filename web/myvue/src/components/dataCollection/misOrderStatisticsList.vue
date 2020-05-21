<template>
  <div class="misOrderStatisticsList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv demandAllotList">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle"><span class="coarseLine MR_ten"></span>总单统计列表</div>
                    <el-row>
                        <div class="border B_R MB_twenty" v-for="item in tableData">
                            <table class="w_ratio h_ratio lineHeightForty fontCenter">
                                <thead>
                                    <tr>
                                        <th width="250px;">总单号({{item.mis_order_sn}})</th>
                                        <th width="150px;">总单需求量({{item.mis_order_total_num}})</th>
                                        <th width="150px;">预判采购量({{item.mis_order_wait_buy_num}})</th>
                                        <th width="120px;">现货数量({{item.spot_num}})</th>
                                        <th width="120px;">实采量({{item.real_buy_num}})</th>
                                        <th width="110px;">待采量({{item.wait_buy_num}})</th>
                                        <th width="110px;">溢采量({{item.overflow_num}})</th>
                                        <th width="140px;">总单满足率({{item.real_buy_rate}}%)</th>
                                    </tr>
                                </thead>
                            </table>
                            <table class="w_ratio h_ratio lineHeightForty fontCenter" v-if="item.sub_order_info">
                                <tr>
                                    <td width="150px;">子单号</td>
                                    <td width="100px;">sku数</td>
                                    <td width="150px;">子单需求量</td>
                                    <td width="150px;">子单预判数量</td>
                                    <td width="100px;">子单现货数量</td>
                                    <td width="100px;">实采量</td>
                                    <td width="100px;">溢采量</td>
                                    <td width="100px;">子单满足率</td>
                                    <td width="100px;">销售客户账号</td>
                                    <td width="150px;">交付日期</td>
                                    <td width="100px;">查看详情</td>
                                </tr>
                                <tr v-for="orderInfo in item.sub_order_info">
                                    <td width="150px;">{{orderInfo.sub_order_sn}}</td>
                                    <td width="100px;">{{orderInfo.sku_num}}</td>
                                    <td width="150px;">{{orderInfo.mis_order_sub_total_num}}</td>
                                    <td width="150px;">{{orderInfo.mis_order_sub_wait_buy_num}}</td>
                                    <td width="100px;">{{orderInfo.spot_num}}</td>
                                    <td width="100px;">{{orderInfo.real_buy_num}}</td>
                                    <td width="100px;">{{orderInfo.overflow_num}}</td>
                                    <td width="100px;">{{orderInfo.real_buy_rate}}%</td>
                                    <td width="100px;">{{orderInfo.sale_user_account}}</td>
                                    <td width="150px;">{{orderInfo.entrust_time}}</td>
                                    <td width="100px;"><i class="el-icon-view notBgButton" @click="viewDetails(orderInfo.sub_order_sn)"></i></td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
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
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
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
        axios.get(vm.url+vm.$misDemandListURL+"?page="+vm.page+"&page_size="+vm.pagesize,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.tableData='';
                vm.total=0;
                vm.isShow=true;
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
    //查看详情页 
    viewDetails(sub_order_sn){
        let vm=this;
        let switchoEvent=event;
        switchLoading(event);
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$demandStasticsListURL+"?sub_order_sn="+sub_order_sn,
             {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            switchoRiginally(switchoEvent)
            sessionStorage.setItem("tableData",JSON.stringify(res));
            vm.$router.push('/demandStasticsList?sub_order_sn='+sub_order_sn);
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
</style>

