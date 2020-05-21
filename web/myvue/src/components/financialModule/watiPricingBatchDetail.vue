<template>
  <div class="watiPricingBatchDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <tableTitle :tableTitle='tableTitle,config'  @Download="Download" @confirmUpData="confirmUpData"></tableTitle> -->
                    <div class="tableTitleStyle">
                        <backToTheUpPage :back='back'></backToTheUpPage>
                        <span class="upTitle">{{tableTitle}}</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="confirmShow()">上传核价数据</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="Download()">下载待核价数据表</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="dialogVisible=true">提交批次核价</span>
                    </div>
                        <el-dialog title="提交批次核价" :visible.sync="dialogVisible" width="800px">
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4" :offset="1"><div class=""><i class="redFont NotStyleForI">*</i>运费：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="post_amount" placeholder="请输入运费"></el-input>
                                    </div>
                                </el-col>
                                <!-- <el-col :span="4" :offset="1"><div class=""><i class="redFont NotStyleForI">*</i>美金汇率：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="usa_rate" placeholder="请输入美金汇率"></el-input>
                                    </div>
                                </el-col> -->
                            </el-row>
                            <!-- <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4" :offset="1"><div class=""><i class="redFont NotStyleForI">*</i>韩币汇率：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="krw_rate" placeholder="请输入韩币汇率"></el-input>
                                    </div>
                                </el-col>
                            </el-row> -->
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;confirmPricing()">确 定</el-button>
                            </span>
                        </el-dialog>
                    <div class="t_i">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                <thead>
                                    <tr>
                                        <th :style="labelWidth()">商品标签</th>
                                        <th style="width:200px;">商品名称</th>
                                        <th style="width:150px">商品代码</th>
                                        <th style="width:150px">商家编码</th>
                                        <th style="width:150px">商品规格码</th>

                                        <!-- <th style="width:130px">清点数量</th> -->
                                        <th style="width:100px">实采数量</th>
                                        <!-- <th style="width:100px">差异数量</th> -->
                                        <th style="width:100px">美金原价</th>
                                        <th style="width:100px">成本价</th>
                                        <th style="width:100px">商品重量</th>
                                        <!-- <th style="width:100px">采购备注</th>
                                        <th style="width:100px">物流备注</th> -->
                                    </tr>
                                </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc" id="cc" @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr v-for="(item,index) in tableData">
                                    <td :style="labelWidth()">
                                        <span v-for="labelInfo in item.goods_label_list" class="PR_ten">
                                            <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                        </span>
                                    </td>
                                    <td  class="overOneLinesHeid" style="width:200px;">
                                        <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td style="width:150px">{{item.erp_prd_no}}</td>
                                    <td style="width:150px">{{item.erp_merchant_no}}</td>
                                    <td style="width:150px">{{item.spec_sn}}</td>

                                    <!-- <td style="width:130px">{{item.allot_num}}</td> -->
                                    <td style="width:100px">{{item.day_buy_num}}</td>
                                    <!-- <td style="width:100px">{{item.diff_num}}</td> -->
                                    <td style="width:100px">{{item.spec_price}}</td>

                                    <td style="width:100px">{{item.cost_amount}}</td>
                                    <td style="width:100px">{{item.spec_weight}}</td>
                                    <!-- <td style="width:100px">{{item.purchase_remark}}</td>
                                    <td style="width:100px">{{item.remark}}</td> -->
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="lineHeighteighty fontCenter"><span class="bgButton" @click="openPricing()">确认核价</span></div>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import notFound from '@/components/UiAssemblyList/notFound'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import { someDataLongByJson } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import backToTheUpPage from '@/components/UiAssemblyList/backToTheUpPage'
export default {
  components:{
      notFound,
      upDataButtonByBox,
      backToTheUpPage,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      tableTitle:this.$route.query.real_purchase_sn+'_待核价批次详情',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/watiPricingBatchList"}]',
      back:'/watiPricingBatchList',
      isShow:false,
      contentStr:"请您确认是否要进行核价？",
      dialogVisible:false,
      krw_rate:'',
      usa_rate:'',
      post_amount:'',
      titleStr:'上传核价数据',
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
        axios.get(vm.url+vm.$watiPricingBatchDetailURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&is_mother="+vm.$route.query.is_mother,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.data;
            tableStyleByDataLength(vm.tableData.length,15);
            if(res.data.code==1000){
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
    //下载待核价数据
    Download(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$downloadPricingBatchDetailURL+'?real_purchase_sn='+vm.$route.query.real_purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&is_mother="+vm.$route.query.is_mother+'&token='+headersToken);
    },
    confirmShow(){
        $(".upDataButtonByBox_b").fadeIn();
    },
    //确认上传
    GFFconfirmUpData(formDate){
        let vm=this;
        $(".upDataButtonByBox_b").fadeOut();
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$doUploadPricingBatchURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&is_mother="+vm.$route.query.is_mother,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code==1000){
                vm.getDataList();
                $("#file").val('');
                vm.fileName='';
                vm.$message(res.msg);
            }else{
                vm.$message(res.msg);
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
    //打开核价弹出框
    openPricing(){
        $(".confirmPopup_b").fadeIn();
    },
    //确认核价
    confirmationAudit(){
        let vm = this;
        $(".confirmPopup_b").fadeOut();
        axios.post(vm.url+vm.$changePriceStatusURL,
            {
                "real_purchase_sn":vm.$route.query.real_purchase_sn,
                "status":3,
                "group_sn":vm.$route.query.group_sn,
                "purchase_sn":vm.$route.query.purchase_sn,
                "is_mother":vm.$route.query.is_mother,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.$router.push('/watiPricingBatchList');
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
    //取消核价 
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    //提交批次核价
    confirmPricing(){
        let vm = this;
        if(vm.post_amount==''){
            vm.$message('运费不能为空！');
            return false;
        }
        // if(vm.usa_rate==''){
        //     vm.$message('美金汇率不能为空！');
        //     return false;
        // }
        // if(vm.krw_rate==''){
        //     vm.$message('韩币汇率不能为空！');
        //     return false;
        // }
        axios.post(vm.url+vm.$doPricingBatchURL,
            {
                "real_purchase_sn":vm.$route.query.real_purchase_sn,
                "group_sn":vm.$route.query.group_sn,
                "purchase_sn":vm.$route.query.purchase_sn,
                "is_mother":vm.$route.query.is_mother,
                "path_way":vm.$route.query.path_way,
                "post_amount":vm.post_amount,
                // "usa_rate":vm.usa_rate,
                // "krw_rate":vm.krw_rate,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
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
    labelStyle(color){
        return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
    },
    labelWidth(){
        let vm = this;
        let width=someDataLongByJson(vm.tableData,'goods_label_list')*60;
        return "width:"+width+"px";
    },
    tableWidth(){
        let vm = this;
        let width = someDataLongByJson(vm.tableData,'goods_label_list')*60+1500;
        return "width:"+width+"px";
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
  }
}
</script>

<style>
.Location{
    position: relative;
    top: -52px;
    left: 460px;
}
.watiPricingBatchDetail .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.watiPricingBatchDetail .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.watiPricingBatchDetail .ee{width:100%!important; width:100%; text-align: center;}
.watiPricingBatchDetail .t_i_h table{width:100%;}
.watiPricingBatchDetail .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.watiPricingBatchDetail .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.watiPricingBatchDetail .cc table{width:100%; }
.watiPricingBatchDetail .cc table td{ text-align:center}
</style>
<style scoped lang=less>
table{
    border: 1px solid #ebeef5;
    tr{
        line-height: 40px;
        th,td{
            border-bottom: 1px solid #ebeef5;
            border-left: 1px solid #ebeef5;
        }
    }
    tr:nth-child(even){
        background:#fafafa;
    }
    tr:hover{
        background:#ced7e6;
    }
}
</style>
