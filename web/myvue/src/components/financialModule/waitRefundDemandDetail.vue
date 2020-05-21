<template>
  <div class="waitRefundDemandDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig'></tableContent>
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
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      tableTitle,
      tableContent,
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableTitle:'待回款资金需求详情',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/waitRefundDemandList"}]',
      tableContent:'',
      TCTitle:['商品名称','平台编码','商品规格码','商家编码','需求量','实际到货数量','实际发货量','销售折扣','美金原价'],
      tableField:['goods_name','goods_sn','spec_sn','erp_merchant_no','goods_number','real_arrival_num','real_ship_num','sale_discount','spec_price'],//表格字段
      contentConfig:'[{"isShow":"false"},{"parameter":"false"}]',
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
        let tableDetail=sessionStorage.getItem("tableDetail");
        if(tableDetail=='1'){
            vm.getlistData();
            return false;
        };
        vm.tableContent=tableDetail;
        sessionStorage.setItem("tableDetail",'1');
        vm.loading.close();
    },
    getlistData(){
        let vm = this;
        axios.get(vm.url+vm.$waitRefundDemandDetailURL+"?deliver_order_sn="+vm.$route.query.deliver_order_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableContent=JSON.stringify(res.data.data);
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
    }
  }
}
</script>

<style>
</style>
