<template>
  <div class="supplierList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <!-- <tableTitle :tableTitle='tableTitle,config' @Add="Add"></tableTitle> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>供应商列表</span></div></el-col>
                        <el-col :span="3"><div>供应商编号</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="supplierNum" placeholder="请输入供应商编号"></el-input></div></el-col>
                        <el-col :span="3"><div>供应商名称</div></el-col>
                        <el-col :span="3"><div><el-input v-model="supplierName" placeholder="请输入供应商名称"></el-input></div></el-col>
                        <el-col :span="3"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                        <el-col :span="3"><span class="notBgButton" type="text" @click="Add()"><i class="iconfont upDataIcon verticalAlign">&#xea22;</i>新增供应商</span></el-col>
                    </el-row>
                    <el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig'></tableContent>
                    </el-row>
                    <el-dialog title="添加供应商" :visible.sync="dialogVisible" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>供应商名称：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-input v-model="supplier_name" placeholder="请输入供应商名称"></el-input>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div>供应商编号：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-input v-model="supplier_num" placeholder="请输入供应商编号"></el-input>
                            </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info"  @click="dialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="dialogVisible = false;confirmADD()">
                                确 定
                                <span style="display: none" class="el-icon-loading confirmLoading"></span>
                            </el-button>
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
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      tableTitle:'供应商列表',
      config:'[{"Add":"添加供应商"},{"download":"false"},{"search":"false"},{"back":"false"}]',
      tableContent:'',
      TCTitle:['供应商ID','供应商编号','供应商名称','创建时间','修改日期'],
      tableField:['supplier_id','supplier_num','supplier_name','create_time','modify_time'],//表格字段
      contentConfig:'[{"isShow":"false"},{"parameter":"false"}]',
      isShow:false,
      dialogVisible:false,
      supplier_name:'',
      supplier_num:'',
      //搜索字段
      supplierNum:'',
      supplierName:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$supplierListURL+'?supplier_num='+vm.supplierNum+"&supplier_name="+vm.supplierName,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code==1000){
                vm.tableContent=JSON.stringify(res.data.data.data);
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            load.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    Add(){
        let vm = this;
        vm.dialogVisible=true;
    },
    confirmADD(){
        let vm = this;
        $(".confirmLoading").show();
        if(vm.supplier_name==''){
            vm.$message('请输入供应商名称！');
            $(".confirmLoading").hide();
            return
        }
        if(vm.supplier_num==''){
            vm.$message('请输入供应商编号！');
            $(".confirmLoading").hide();
            return
        }
        axios.post(vm.url+vm.$addSupplierURL,
            {
                "supplier_name":vm.supplier_name,
                "supplier_num":vm.supplier_num
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            $(".confirmLoading").hide();
            vm.dialogVisible=false;
            vm.getDataList();
        }).catch(function (error) {
            $(".confirmLoading").hide();
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
