<template>
  <div class="fundListDetail">
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
      tableTitle:'需求资金详情列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/demandFundList"}]',
      tableContent:'',
      TCTitle:['采购期单号','采购单需求数','采购单实采数','采购单sku数量','采购单总需求金额'],
      tableField:['purchase_sn','purchase_may_num','purchase_real_num','sku_num','purchase_total_amount'],//表格字段
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
        let tableData=sessionStorage.getItem("tableData");
        if(tableData=='1'){
            vm.getlistData();
            return false;
        };
        vm.tableContent=tableData;
        sessionStorage.setItem("tableData",'1');
        vm.loading.close();
    },
    getlistData(){
        let vm = this;
        axios.get(vm.url+vm.$fundListDetailURL+"?action=purchase",
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableContent=JSON.stringify(res.data.data.data_list);
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
