<template>
  <div class="batchAuditDetail">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle title">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>待审核采购批次详情</span>
                        <span v-if="audit_status==0" class="bgButton ML_twenty" @click="openPricing('1')">提交批次审核</span> 
                        <span v-if="audit_status==1" class="bgButton ML_twenty" @click="openPricing('2')">提交批次数据</span> 
                    </div>
                    <el-row>
                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" style="width:1700px;">
                                        <thead>
                                            <tr>
                                                <th class="overOneLinesHeid fontLift" style="width:200px;">商品名称</th>
                                                <th style="width:180px">商家编码</th>
                                                <th style="width:180px">商品规格码</th>
                                                <th style="width:150px">商品代码</th>
                                                <th style="width:150px">参考码</th>
                                                <th style="width:200px">采购单号</th>
                                                <th style="width:100px">美金原价</th>
                                                <th style="width:100px">lvip价格</th>
                                                <th style="width:100px">实采数量</th>


                                                <th style="width:100px">成本折扣</th>
                                                <th style="width:150px">预计最终折扣</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" style="width:1700px;">
                                    <tbody>
                                        <tr v-for="(item,index) in tableData">
                                            <td class="overOneLinesHeid fontLift" style="width:200px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="top">
                                                <span style="-webkit-box-orient: vertical;width:200px;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:180px">{{item.erp_merchant_no}}</td>
                                            <td style="width:180px">{{item.spec_sn}}</td>
                                            <td style="width:150px">{{item.erp_prd_no}}</td>
                                            <td style="width:150px">{{item.erp_ref_no}}</td>
                                            <td style="width:200px">{{item.purchase_sn}}</td>
                                            <td style="width:100px">{{item.spec_price}}</td>
                                            <td style="width:100px">{{item.lvip_price}}</td>
                                            <td style="width:100px">{{item.day_buy_num}}</td>

                                            <td style="width:100px">{{item.channel_discount}}</td>
                                            <td style="width:150px">{{item.real_discount}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
  components:{
      notFound,
      fontWatermark,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      contentStr:'',
      auditStatus:'',
      audit_status:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
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
        vm.tableData=tableData.batch_goods_info;
        vm.audit_status=tableData.rpa_info.status;
        tableStyleByDataLength(vm.tableData.length,15);
        sessionStorage.setItem("tableData",'1');
    },
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'})
        axios.get(vm.url+vm.$batchAuditDetailURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code=="1000"){
                vm.tableData=res.data.data.batch_goods_info;
                vm.audit_status=res.data.data.rpa_info.status;
                tableStyleByDataLength(vm.tableData.length,15);
                vm.isShow=false;
            }else{
                vm.tableData=[];
                vm.isShow=true;
                // vm.$message(res.data.msg);
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
    //取消审核
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    //打开审核弹出框 
    openPricing(e){
        let vm = this;
        $(".confirmPopup_b").fadeIn();
        vm.auditStatus=e;
        if(e=='1'){//提交批次审核
            vm.contentStr="请您确认是否要提交批次审核？";
        }else if(e=='2'){//提交批次数据
            vm.contentStr="请您确认是否要提交批次数据？";
        }
    },
    confirmationAudit(){
        let vm = this;
        let url;
        if(vm.auditStatus=='1'){//提交批次审核
            url=vm.$doBatchAuditURL;
        }else if(vm.auditStatus=='2'){//提交批次数据
            url=vm.$uploadBatchAuditURL;
        }
        vm.determineIsNo();
        axios.get(vm.url+url+"?real_purchase_sn="+vm.$route.query.real_purchase_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=='1000'){
                vm.$message(res.data.msg);
                vm.getDataList()
                if(vm.auditStatus=='2'){
                    vm.$router.push('/batchAuditList');
                }
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
    //返回上一页 
    backUpPage(){
        let vm = this;
        vm.$router.push('/batchAuditList');
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
.batchAuditDetail .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.batchAuditDetail .t_i_h{width:100%; overflow-x:hidden;}
.batchAuditDetail .ee{width:100%!important; width:100%; text-align: center;}
.batchAuditDetail .t_i_h table{width:100%;}
.batchAuditDetail .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.batchAuditDetail .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.batchAuditDetail .cc table{width:100%; }
.batchAuditDetail .cc table td{ text-align:center}
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
