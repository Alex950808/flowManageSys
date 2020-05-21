<template>
  <div class="marginRateList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <span><span class="coarseLine MR_twenty"></span>自采毛利率列表</span>
                        <span class="bgButton floatRight MT_twenty" @click="openBox()">新增自采毛利率</span>
                    </div>
                    <el-dialog title="新增折扣类型记录" :visible.sync="dialogVisibleADD" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>自采毛利率：</div></el-col>
                            <el-col :span="6"><div><el-input v-model="pick_margin_rate" placeholder="请输入自采毛利率"></el-input></div></el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleADD = false;">取 消</el-button>
                            <el-button type="primary" @click="addMarginRate()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>创建时间</th>
                                <th>毛利率</th>
                                <th>编辑</th>
                            </tr>
                        </thead>
                        <tr v-for="item in tableData">
                            <td>{{item.create_time}}</td>
                            <td>{{item.pick_margin_rate}}</td>
                            <td>
                                <i class="notBgButton NotStyleForI" @click="openPricing();id=item.id">删除</i>
                            </td>
                        </tr>
                    </table>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
  components:{
      notFound,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      contentStr:'请您确认是否删除自采毛利率！',
      id:'',
      //搜索
      pick_margin_rate:'',
      dialogVisibleADD:false,
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
        axios.get(vm.url+vm.$getMarginRateListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code=='1000'){
                vm.tableData=res.data.data;
                vm.isShow=false;
            }else if(res.data.code=='1002'){
                vm.tableData='';
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
    //打开删除确认弹框
    openPricing(){
        $(".confirmPopup_b").fadeIn();
    },
    //删除自采毛利率
    confirmationAudit(){
        let vm = this;
        $(".confirmPopup_b").fadeOut();
        axios.get(vm.url+vm.$delMarginRateURL+"?id="+vm.id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=='1000'){
                vm.getDataList();
                vm.$message(res.data.msg);
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
    //取消删除自采毛利率
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    //打开新增自采毛利率弹出框
    openBox(){
        let vm = this;
        vm.dialogVisibleADD=true;
    },
    // 确认新增自采毛利率
    addMarginRate(){
        let vm = this;
        vm.dialogVisibleADD=false;
        axios.post(vm.url+vm.$goodsAddMarginRateURL,
            {
                "pick_margin_rate":vm.pick_margin_rate,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.$message(res.data.msg);
            vm.getDataList();
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

<style scoped>
@import '../../css/publicCss.css';
</style>
