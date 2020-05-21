<template>
  <div class="confirmOpenBill_b">
    <!-- 确认开单页面 -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="confirmOpenBill">
                        <el-row class="tableTitleStyle">
                            <el-col :span="20">
                                <span class="bgButton" @click="backUpPage()">返回上一级</span>
                                <span><span class="coarseLine MR_twenty"></span>{{`${this.$route.query.real_purchase_sn}`}}</span>
                            </el-col>
                            <el-col :span="4" class="fontCenter">
                                <div>
                                <span class="bgButton" @click="d_table()">下载待开单表</span>
                                </div>
                            </el-col>
                        </el-row>

                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <thead>
                                        <tr>
                                            <th :style="labelWidth()">商品标签</th>
                                            <th style="width:200px;">商品名称</th>
                                            <th style="width:150px">商品代码</th>
                                            <th style="width:150px">商家编码</th>
                                            <th style="width:150px">商品规格码</th>
                                            <th style="width:150px">商品采购数量</th>
                                            <th style="width:100px">成本价</th>
                                            <th style="width:100px">美金原价</th>
                                            <th style="width:100px">清点数量</th>
                                            <th style="width:100px">商品重量</th>
                                            <th style="width:100px">差异数量</th>
                                            <th style="width:150px">备注</th>
                                        </tr>
                                    </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <tr v-for="(item,index) in tableData">
                                        <td :style="labelWidth()">
                                            <span v-for="labelInfo in item.goods_label_list" class="PR_ten">
                                                <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                            </span>
                                        </td>
                                        <td class="overOneLinesHeid" style="width:200px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td style="width:150px">{{item.erp_prd_no}}</td>
                                        <td style="width:150px">{{item.erp_merchant_no}}</td>
                                        <td style="width:150px">{{item.spec_sn}}</td>
                                        <td style="width:150px">{{item.day_buy_num}}</td>
                                        <td style="width:100px">{{item.cost_amount}}</td>
                                        <td style="width:100px">{{item.spec_price}}</td>
                                        <td style="width:100px">{{item.allot_num}}</td>
                                        <td style="width:100px">{{item.spec_weight}}</td>
                                        <td style="width:100px">{{item.diff_num}}</td>
                                        <td class="overOneLinesHeid" style="width:150px"><span style="-webkit-box-orient: vertical;">{{item.remark}}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="fontRight lineHeightSixty">
                            <span class="bgButton" @click="confirmShow()">确认开单</span>
                        </div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    <div class="confirmPopup_b" v-if="show">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
            请您确认是否要开单？
            <div class="confirm"><el-button @click="confirm(4)" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { someDataLongByJson } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        title:'',
        search:'',//用户输入数据
        show:false,
        headersStr:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getOpenBillData()
    },
    methods: {
        //获取待开单详情数据
        getOpenBillData(){
            let vm=this;
            vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
            tableStyleByDataLength(vm.tableData.length,15);
        },
        //确认开单
        confirm(e){
            let vm=this;
            vm.show=false;
            axios.post(vm.url+vm.$changeBillingStatusURL,
                {
                    "real_purchase_sn":vm.$route.query.real_purchase_sn,
                    "status":e,
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "group_sn":vm.$route.query.group_sn,
                    "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                if(res.data.code==1000){
                    vm.$message(res.data.msg);
                    vm.backUpPage()
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
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("hh").scrollLeft=b;
        },
        //回上一级页面
        backUpPage(){
            let vm=this;
            if(vm.$route.query.isTimeOut){
                this.$router.push('/timeoutProcessing');
            }
            if(vm.$route.query.isStayOpen){
                this.$router.push('/stayOpenBill');
            }
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            let tmpWin=window.open(vm.url+vm.$downloadBillingListURL+'?real_purchase_sn='+vm.$route.query.real_purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&is_mother="+vm.$route.query.is_mother+'&token='+headersToken);
        
        },
        //确认弹框否
        determineIsNo(){
            let vm=this;
            vm.show=false;
        },
        //确认弹框是
        confirmShow(){
            let vm=this;
            vm.show=true;
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
    }
}
</script>
<style>
.confirmOpenBill .t_i{width:100%; height:auto;}
.confirmOpenBill .t_i_h{width:100%; overflow-x:hidden; background:#f5f7fa;}
.confirmOpenBill .ee{width:110%!important; width:110%; text-align: center;}
.confirmOpenBill .t_i_h table{width:1530px;}
.confirmOpenBill .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.confirmOpenBill .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.confirmOpenBill .cc table{width:1530px; }
.confirmOpenBill .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
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