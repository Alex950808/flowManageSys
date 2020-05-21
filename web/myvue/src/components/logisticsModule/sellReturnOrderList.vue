<template>
  <div class="sellReturnOrderList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig' @ViewDetail="ViewDetail"></tableContent>
                        <notFound v-if="isShow"></notFound>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      tableTitle,
      tableContent,
      notFound,
      fontWatermark
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableTitle:'退货单列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"false"}]',
      tableContent:'',
      TCTitle:['发货单单号','订单总价','订单状态','实际待回款金额','查看详情'],
      tableField:['deliver_order_sn','order_amount','order_sta','real_order_amount'],//表格字段
      contentConfig:'[{"isShow":"isViewDetail"},{"parameter":"deliver_order_sn"}]',
      isShow:false,
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
        axios.get(vm.url+vm.$sellReturnOrderListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableContent=JSON.stringify(res.data.data.data);
                vm.total=res.data.data.total;
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
    ViewDetail(deliver_order_sn){
        let vm = this;
        vm.$router.push('/sellReturnOrderDetail?deliver_order_sn='+deliver_order_sn);
    }
  }
}
</script>

<style>
</style>
