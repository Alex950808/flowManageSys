<template>
  <div class="exchangeRateList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;汇率列表
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openImportOffer()">导入汇率</span>
                        <div class="d_I_B F_Z_N ML_twenty">
                            <span>开始时间 ：</span>
                            <template>
                                <el-date-picker v-model="start_date" type="date" value-format="yyyy-MM-dd" placeholder="选择日期"></el-date-picker>
                            </template>
                            <span>结束时间 ：</span>
                            <template>
                                <el-date-picker v-model="end_date" type="date" value-format="yyyy-MM-dd" placeholder="选择日期"></el-date-picker>
                            </template>
                            <span class="bgButton" @click="searchFrame()">搜索</span>
                        </div>
                    </div>
                    <table class="fontCenter" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>时间</th>
                                <th>美金对人民币汇率</th>
                                <th>美金对韩币汇率</th>
                                <th>韩币对人民币汇率</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData">
                                <td>{{item.day_time}}</td>
                                <td>{{item.usd_cny_rate}}</td>
                                <td>{{item.usd_krw_rate}}</td>
                                <td>{{item.krw_cny_rate}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <div class="beijing">
          <span @click="d_table()" class="d_table blueFont Cursor">汇率表模板下载</span>
      </div>
      <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
      <successfulOperation :msgStr="msgStr"></successfulOperation>
      <operationFailed :msgStr="msgStr"></operationFailed>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
export default {
  components:{
      notFound,
      upDataButtonByBox,
      operationFailed,
      successfulOperation,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      //导入报价销售折扣 
      titleStr:'导入汇率表',
      msgStr:'',
      //搜索
      start_date:'',
      end_date:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        vm.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$exchangeRateListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&start_time="+vm.start_date+"&end_time="+vm.end_date,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
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
    openImportOffer(){
        let vm = this;
        $(".upDataButtonByBox_b").fadeIn();
        $(".d_table").fadeIn();
        $(".beijing").fadeIn();
    },
    //确认上传 
    GFFconfirmUpData(formDate){
        let vm=this;
        $(".upDataButtonByBox_b").fadeOut();
        $(".d_table").fadeOut();
        $(".beijing").fadeOut();
        let headersToken=sessionStorage.getItem("token");
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$uploadExchangeRateURL,
        type: "POST",
        async: true,
        cache: false,
        headers:{
            'Authorization': 'Bearer ' + headersToken,
            'Accept': 'application/vnd.jmsapi.v1+json',
        },
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code=='1000'){
                // let msg = res.msg.split(',')
                vm.msgStr=res.msg;
                $(".successfulOperation_b").fadeIn();
                vm.tableData=res.data;
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                $("#file").val('');
                vm.fileName='';
            }else{
                // let msg = res.msg.split(',') 
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
                // vm.$message(res.msg);
                $("#file").val('');
                vm.fileName='';
            }
        }
        }).catch(function (error) {
                loa.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //模板表下载
    d_table(){
        let vm=this;
        window.open(vm.downloadUrl+'/核价汇率维护.xls');
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
    searchFrame(){
        let vm = this;
        if(vm.start_date==null){
            vm.start_date='';
        }
        if(vm.end_date==null){
            vm.end_date='';
        }
        vm.getDataList();
    }
  }
}
</script>

<style>
.beijing{
    width: 100%;
    height: 100%;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    display: none;
}
.d_table{
    position: fixed;
    z-index: 10039;
    width: 385px;
    height: 27px;
    margin: 250px auto;
    top: 56px;
    left: 15px;
    bottom: 0;
    right: 0;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>