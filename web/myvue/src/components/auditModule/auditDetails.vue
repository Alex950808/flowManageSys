<template>
  <div class="auditDetails">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="listTitleStyle title">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>折扣上传详情</span>
                    </el-row>
                    <el-row style="margin-bottom: 10px;">
                        <span class="upTitle MR_ten">订单信息：</span>
                    </el-row>
                    <el-row style="margin-bottom: 10px;">
                        <el-row class="orderDt">
                            <el-col :span="6" :offset="1"><div><span>审核单号 ：{{titleData.audit_sn}}</span></div></el-col>
                            <el-col :span="6" :offset="1"><div><span>是否需要审核 ：{{titleData.audit_desc}}</span></div></el-col>
                            <el-col :span="6" :offset="1">
                                <div>
                                    <span>
                                        审核状态 ：{{titleData.status_desc}}
                                    </span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="orderDt">
                            <el-col :span="6" :offset="1"><div><span>配置编号 ：<span>{{titleData.config_sn}}</span></span></div></el-col>
                            <el-col :span="6" :offset="1"><div><span>创建时间 ：{{titleData.create_time}}</span></div></el-col>
                        </el-row>
                    </el-row>
                    <el-row style="margin-bottom: 10px;">
                        <span class="upTitle">审核信息 ：</span>
                        <span class="bgButton" v-if="titleData.status==0" @click="openAudit();auditStatus='1'">确认审核</span>
                        <!-- <span class="infoBgButton">确认审核</span> -->
                        <span class="bgButton" v-if="titleData.canAudit!=0" @click="openAudit();auditStatus='2'">数据提交</span>
                    </el-row>
                    <el-row>
                        <table class="MB_twenty" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>品牌名称</th>
                                    <th>渠道名称</th>
                                    <th>方式名称</th>
                                    <th>供货量</th>
                                    <th>品牌折扣</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in tableData">
                                    <td>{{item.brand_name}}</td>
                                    <td>{{item.purchase_channel}}</td>
                                    <td>{{item.purchase_method}}</td>
                                    <td>{{item.shipment}}</td>
                                    <td class="editDiscount">
                                        <input type="text" @change="editDiscount(index,item.id)" :name="index" :value="item.brand_discount">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </el-row>
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
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      customConfirmationBoxes,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      titleData:[],
      isShow:false,
      auditStatus:'',
      //确认弹框
      contentStr:'请您确认是否要进行审核？'
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getUpPageData();
  },
  methods:{
        getUpPageData(){
            let vm = this;
            let tableData=JSON.parse(sessionStorage.getItem("tableData"));
            if(tableData=='1'){
                vm.getDataList();
                return false;
            };
            vm.tableData=tableData.auditDetail;
            vm.titleData=tableData.auditInfo;
            sessionStorage.setItem("tableData",'1');
            vm.loading.close();
        },
        getDataList(){
            let vm = this;
            axios.post(vm.url+vm.$discountAuditDetailURL,
                {
                    "audit_sn":vm.$route.query.audit_sn
                },
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                vm.loading.close();
                if(res.data.auditDetail!=0){
                    vm.tableData=res.data.auditDetail;
                    vm.titleData=res.data.auditInfo;
                    vm.isShow=false;
                }else{
                    vm.isShow=true;
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
        editDiscount(index,id){
            let vm = this;
            var  discountVal = $(".editDiscount input[name="+index+"]").val();
            if(discountVal<=0||discountVal>=1){
                vm.$message('折扣填写错误，请重新填写!');
                vm.getDataList();
                return false;
            }
            axios.post(vm.url+vm.$modifyBrandDiscountURL,
                {
                    "detail_id":id,
                    "brand_discount":discountVal
                },
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                if(res.data.code=="2024"){
                    vm.$message(res.data.msg);
                }else{
                    vm.getDataList();
                }
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                }
            });
        },
        openAudit(){
            $(".confirmPopup_b").fadeIn();
        },
        //取消审核/数据提交
        determineIsNo(){
            $(".confirmPopup_b").fadeOut();
        },
        //确认审核
        confirmationAudit(){
            let vm = this;
            vm.determineIsNo();
            let url;
            let dataList;
            if(vm.auditStatus=='1'){//1为确认审核 
                url=vm.$auditBrandDiscountURL;
                dataList={"audit_sn":vm.$route.query.audit_sn,"is_pass":"1"}
            }else if(vm.auditStatus=='2'){//2为数据提交
                url=vm.$submitBrandDiscountURL;
                dataList={"audit_sn":vm.$route.query.audit_sn}
            }
            axios.post(vm.url+url,dataList,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                if(res.data.code=="2024"){
                    vm.$message(res.data.msg);
                    vm.getDataList()
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
        backUpPage(){
            let vm = this;
            vm.$router.push('/auditList');
        }
  }
}
</script>

<style>
.auditDetails .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
.auditDetails .bg-purple{
    line-height: 40px;
}
.auditDetails .orderDt{
    color: #a7a7a7;
    height: 50px;
    line-height: 50px;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
