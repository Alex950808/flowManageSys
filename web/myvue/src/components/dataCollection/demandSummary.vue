<template>
  <div class="demandSummary_b">
    <!-- erp待开单需求数据汇总页面 -->
        <el-col :span="22" :offset="1">
            <div class="demandSummary viewSummary">
                <el-row>
                    <el-col :span="16" class="title_left">
                        <span class="bgButton" @click=" backUpPage()">返回上一级</span>
                        <span class="stage">0001期</span><span class="date">2018-04-12&nbsp;&nbsp;需求单</span>
                    </el-col>
                    <el-col :span="8" class="title_right">
                        <!-- <input type="text"/><i class="el-icon-search search"></i> -->
                        <span class="d_table" @click="d_table()">下载该需求总表</span>
                    </el-col>
                </el-row>
                <div class="t_i">
                    <div class="t_i_h" id="hh">
                        <div class="ee">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <thead>
                                    <tr>
                                        <th style="width:200px;">商品名称</th>
                                        <th style="width:130px">商品代码</th>
                                        <th style="width:130px">商品规格码</th>
                                        <th style="width:130px">商家编码</th>
                                        <th style="width:130px">商品数量</th>
                                        <th style="width:130px">清点数量</th>
                                        <th style="width:130px">差异数</th>
                                        <th style="width:130px">采购备注</th>
                                        <th style="width:130px">物流备注</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="cc" id="cc" @scroll="scrollEvent()">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr v-for="item in tableData">
                                <td class="ellipsis" style="width:200px;">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td style="width:130px">{{item.erp_prd_no}}</td>
                                <td style="width:130px">{{item.spec_sn}}</td>
                                <td style="width:130px">{{item.erp_merchant_no}}</td>
                                <td style="width:130px">{{item.total_buy_num}}</td>
                                <td style="width:130px">{{item.allot_num}}</td>
                                <td style="width:130px">{{item.diff_num}}</td>
                                <td style="width:130px">{{item.purchase_remark}}</td>
                                <td style="width:130px">{{item.remark}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
export default {
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        loading:'',
        headersStr:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getDetailsData()
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getDetailsData(){
            let vm=this;
            axios.post(vm.url+vm.$billingTotalDetailURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                },
                {
                    headers:vm.headersStr
                }
            ).then(function(res){
                vm.tableData=res.data.data;
                vm.loading.close()
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            // document.getElementById("dd").scrollTop=a;
            document.getElementById("hh").scrollLeft=b;
        },
        //回上一级页面
        backUpPage(){
            let vm=this;
            if(vm.$route.query.isStayOpen){
                vm.$router.push('/stayOpenBill');
            }
            if(vm.$route.query.isTimeOutProcc){
                vm.$router.push('/isTimeOutProcc');
            }
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downloadBillingTotalListURL+'?purchase_sn='+vm.$route.query.purchase_sn+'&token='+headersToken);
        },
    }
}
</script>
<style>
.demandSummary .ellipsis span{
    /* width: 350px; */
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.demandSummary .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.demandSummary .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.demandSummary .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.demandSummary .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.demandSummary .dd{height:659px!important; height:659px; overflow-y:hidden;}
.demandSummary .t_i{width:100%; height:auto; border-right:1px solid #ccc;border-left: 1px solid #ccc;border-top:1px solid #ccc}
.demandSummary .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.demandSummary .ee{width:100%!important; width:100%; text-align: center;}
.demandSummary .t_i_h table{width:1530px;}
.demandSummary .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.demandSummary .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.demandSummary .cc table{width:1530px; }
.demandSummary .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>

<style scoped lang=less>
@import '../../css/dataCollection.less';
</style>