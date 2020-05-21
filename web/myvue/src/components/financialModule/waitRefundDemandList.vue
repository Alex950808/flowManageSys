<template>
  <div class="waitRefundDemandList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig' @ViewDetail="ViewDetail"></tableContent>
                        <notFound v-if="isShow"></notFound>
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
import notFound from '@/components/UiAssemblyList/notFound'
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
      tableTitle:'待回款资金需求单列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/waitRefundlist"}]',
      tableContent:'',
      TCTitle:['配货单单号','提货时间','需求数','订单总价','订单状态','预计回款时间','查看详情'],
      tableField:['deliver_order_sn','delivery_time','goods_num','order_amount','order_sta','pre_refund_time'],//表格字段
      contentConfig:'[{"isShow":"isViewDetail"},{"parameter":"deliver_order_sn"}]',
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
        let tableData=sessionStorage.getItem("tableData");
        if(tableData=='1'){
            vm.getlistData();
            return false;
        };
        vm.tableContent=JSON.stringify(JSON.parse(tableData).data.data);
        vm.total=JSON.parse(tableData).data.total;
        sessionStorage.setItem("tableData",'1');
        vm.loading.close();
    },
    getlistData(){
        let vm = this;
        axios.get(vm.url+vm.$waitRefundDemandListURL+"?sale_user_id="+vm.$route.query.id+"&pagesize="+vm.pagesize+"&page="+vm.page,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableContent=JSON.stringify(res.data.data.data);
                vm.total=res.data.data.total;
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
    ViewDetail(sn){
        let vm = this;
        axios.get(vm.url+vm.$waitRefundDemandDetailURL+"?deliver_order_sn="+sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code==1000){
                sessionStorage.setItem("tableDetail",JSON.stringify(res.data.data));
                vm.$router.push('/waitRefundDemandDetail?deliver_order_sn='+sn);
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
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getlistData()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getlistData()
    },
  }
}
</script>

<style>
</style>
