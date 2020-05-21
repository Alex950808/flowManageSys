<template>
  <div class="distributionOrderDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config' @Download="Download" @confirmUpData="confirmUpData" ></tableTitle>
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
      tableData:[],
      tableTitle:'配货单详情',
      config:'[{"Add":"false"},{"download":"下载配货单"},{"search":"false"},{"back":"/distributionOrderList"},{"upDataStr":"上传配货单详情"}]',
      tableContent:'',
      TCTitle:['商品名称','商品规格码','美金原价','发货数量','实际发货量','销售折扣'],
      tableField:['goods_name','spec_sn','spec_price','pre_ship_num','real_ship_num','sale_discount'],//表格字段
    //   pageName:'isViewDetail',
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
        axios.get(vm.url+vm.$distributionOrderDetailURL+'?deliver_order_sn='+vm.$route.query.deliver_order_sn,
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
    //下载配货单
    Download(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$downloadDistributionOrderURL+'?deliver_order_sn='+vm.$route.query.deliver_order_sn+'&token='+headersToken);
    },
    //确认上传
    confirmUpData(formDate){
        let vm=this;
        // var formDate = new FormData($("#forms")[0]);
        $.ajax({
        url: vm.url+vm.$doUploadDistributionOrderURL+"?deliver_order_sn="+vm.$route.query.deliver_order_sn,
        type: "POST",
        async: false,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            if(res.code==1000){
                // vm.dialogVisibleUp = false;
                vm.getDataList();
                $("#file1").val('');
                vm.fileName='';
                vm.$message(res.msg);
            }else{
                vm.$message(res.msg);
                $("#file1").val('');
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
    },
  }
}
</script>

<style>
</style>
