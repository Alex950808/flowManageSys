<template>
  <div class="sellDeliverOrderList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
            <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig' @Download="Download" @upData="upData"></tableContent>
                    <el-dialog title="上传" :visible.sync="dialogVisible" width="30%">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>上传文件：</div></el-col>
                            <el-col :span="20">
                                <div>
                                    <upDataButton @GFFconfirmUpData="GFFconfirmUpData" :upDataStr="upDataStr"></upDataButton>
                                </div>
                            </el-col>
                        </el-row>
                    </el-dialog>
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
import { Loading } from 'element-ui'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
import upDataButton from '@/components/UiAssemblyList/upDataButton'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      tableTitle,
      tableContent,
      notFound,
      upDataButton,
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
      TCTitle:['发货单单号','订单总价','订单状态','sku数','编辑'],
      tableField:['deliver_order_sn','order_amount','order_sta','sku_num'],//表格字段
      contentConfig:'[{"isShow":"upDataAndDownload"},{"parameter":"deliver_order_sn"}]',
      upDataStr:'上传理货报告',
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      dialogVisible:false,
      deliver_order_sn:'',
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
        axios.get(vm.url+vm.$sellDeliverOrderListURL+"?page="+vm.page+"&page_size="+vm.pagesize,
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
    //下载
    Download(deliver_order_sn){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$downloadSellDeliverOrderURL+'?deliver_order_sn='+deliver_order_sn+'&token='+headersToken);
    },
    // //上传数据
    upData(deliver_order_sn){
        let vm=this;
        vm.deliver_order_sn=deliver_order_sn;
        vm.dialogVisible=true;
    },
    //确认上传
    GFFconfirmUpData(formDate){
        let vm=this;
        $.ajax({
        url: vm.url+vm.$doUploadSellDeliverOrderURL+"?deliver_order_sn="+vm.deliver_order_sn,
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

