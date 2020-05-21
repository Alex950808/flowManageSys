<template>
  <div class="financeDeliverOrderList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig' @Edit="Edit"></tableContent>
                    </el-row>
                    <el-dialog
                        title="收款确认" :visible.sync="dialogVisible" width="600px">
                        <span>确认收款？</span>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="dialogVisibleEdit = false;doEdit()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-row>
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
      tableData:[],
      tableTitle:'发货单列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"false"}]',
      tableContent:'',
      TCTitle:['配货单单号','订单状态','订单总价','sku数量','确认收款'],
      tableField:['deliver_order_sn','order_sta','order_amount','sku_num'],//表格字段
      contentConfig:'[{"isShow":"isEditContent"},{"parameter":"id"}]',
      isShow:false,
      deliver_order_sn:'',
      dialogVisible:false,
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
        axios.get(vm.url+vm.$financeDeliverOrderListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                // vm.tableData=res.data.data.data;
                vm.tableContent=JSON.stringify(res.data.data.data);
                vm.isShow=false;
            }else if(res.data.code==1002){
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
    //确认收款
    Edit(deliver_order_sn){
        let vm = this;
        vm.deliver_order_sn=deliver_order_sn;
        vm.dialogVisible=true;
        // axios.get(vm.url+vm.$financeChangeOrderStatusURL+"?deliver_order_sn="+vm.deliver_order_sn,
        //     {
        //         headers:vm.headersStr,
        //     }
        // ).then(function(res){
        // }).catch(function (error) {
        //     if(error.response.status!=''&&error.response.status=="401"){
        //       vm.$message('登录过期,请重新登录!');
        //       sessionStorage.setItem("token","");
        //       vm.$router.push('/');
        //     }
        // });
    },
    doEdit(){
        let vm = this;
        axios.get(vm.url+vm.$financeChangeOrderStatusURL+"?deliver_order_sn="+vm.deliver_order_sn,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogVisible = false;
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
    }
  }
}
</script>

<style>
</style>
