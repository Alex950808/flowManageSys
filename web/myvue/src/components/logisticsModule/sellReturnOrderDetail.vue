<template>
  <div class="sellReturnOrderDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config' @Download="Download" @confirmUpData="confirmUpData"></tableTitle>
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
import { Loading } from 'element-ui'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
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
      tableTitle:'退货单详情',
      config:'[{"Add":"false"},{"download":"下载退货单数据"},{"search":"false"},{"back":"/sellReturnOrderList"},{"upDataStr":"上传退货清点数据"}]',
      tableContent:'',
      TCTitle:['商品名称','商品规格码','商家编码','销售客户id','清点数量','订单总价','发货数量','实际到货数量','实际待回款金额','发货数量','退货数量','销售折扣','美金原价'],
      tableField:['goods_name','spec_sn','erp_merchant_no','sale_user_id','allot_num','order_amount','pre_ship_num','real_arrival_num','real_order_amount','real_ship_num','return_num','sale_discount','spec_price'],//表格字段
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
        axios.get(vm.url+vm.$sellReturnOrderDetailURL+"?deliver_order_sn="+vm.$route.query.deliver_order_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableContent=JSON.stringify(res.data.data);
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
    //下载退货单表
    Download(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$downloadSellReturnGoodsURL+'?deliver_order_sn='+vm.$route.query.deliver_order_sn+'&token='+headersToken);
    },
    //上传清点后的退货单
    confirmUpData(formDate){
        let vm=this;
        $.ajax({
        url: vm.url+vm.$doUploadSellReturnGoodsURL+"?deliver_order_sn="+vm.$route.query.deliver_order_sn,
        type: "POST",
        async: false,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            if(res.data.code==1000){
                $("#file").val('');
                vm.fileName='';
                vm.dialogVisible = false;
                vm.getDataList();
                vm.$message(res.msg);
            }else{
                vm.$message(res.msg);
                $("#file").val('');
                vm.fileName='';
            }
        }
        }).catch(function (error) {
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
