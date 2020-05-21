<template>
  <div class="waitRefundlist">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <!-- <tableTitle :tableTitle='tableTitle,config'></tableTitle> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>待回款资金列表</span></div></el-col>
                      <el-col :span="2"><div>客户</div></el-col>
                      <el-col :span="3" >
                          <template>
                            <el-select v-model="sale_user_id" clearable placeholder="请选择客户">
                              <el-option v-for="item in selectData.sale_user_list" :key="item.id" :label="item.user_name" :value="item.id">
                              </el-option>
                            </el-select>
                          </template>
                      </el-col>
                      <el-col :span="3"><div>资金渠道名称</div></el-col>
                      <el-col :span="3"><div><el-input v-model="fund_channel_name" placeholder="请输入资金渠道名称"></el-input></div></el-col>
                      <el-col :span="3"><div>资金渠道类别</div></el-col>
                      <el-col :span="3">
                        <template>
                            <el-select v-model="fund_cat_id" clearable placeholder="请选择资金渠道类别">
                              <el-option v-for="item in selectData.fund_cat_list" :key="item.id" :label="item.fund_cat_name" :value="item.id">
                              </el-option>
                            </el-select>
                        </template>
                      </el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
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
      fontWatermark
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      selectData:[],
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      tableTitle:'待回款资金列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"false"}]',
      tableContent:'',
      TCTitle:['资金渠道名称','人民币','美金','韩币','折合人民币','截止日期','查看详情'],
      tableField:['fund_channel_name','cny','usd','krw','covert_cny','end_date'],//表格字段
      contentConfig:'[{"isShow":"isViewDetail"},{"parameter":"sale_user_id"}]',
      isShow:false,
      //搜索
      fund_channel_name:'',
      fund_cat_id:'',
      sale_user_id:'',
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
        axios.get(vm.url+vm.$waitRefundListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&fund_channel_name="+vm.fund_channel_name
        +"&fund_cat_id="+vm.fund_cat_id+"&sale_user_id="+vm.sale_user_id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.tableContent=JSON.stringify(res.data.data.fund_channel_info.data);
            vm.selectData=res.data.data;
            vm.total=res.data.data.fund_channel_info.total;
            if(res.data.data.fund_channel_info.data.length!=0){
                // vm.tableData=res.data.data;
                
                vm.isShow=false;
            }else{
                vm.isShow=true;
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    ViewDetail(id){
        let vm = this;
        $(event.target).addClass("disable");
        axios.get(vm.url+vm.$waitRefundDemandListURL+"?sale_user_id="+id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            $(event.target).removeClass("disable");
            if(res.data.code==1000){
                sessionStorage.setItem("tableData",JSON.stringify(res.data));
                vm.$router.push('/waitRefundDemandList?sale_user_id='+id);
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
