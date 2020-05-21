<template>
  <div class="batchSummary_b">
    <!-- 预计到港数据汇总页面 -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="batchSummary viewSummary">
                        <el-row>
                            <el-col :span="20" class="title_left">
                                <span class="bgButton" @click="backUpPage()">返回上一级</span>
                                <span class="stage">{{`${this.$route.query.purchase_sn}`}}</span>
                            </el-col>
                            <el-col :span="4" class="title_left">
                                <span class="notBgButton" @click="d_table()">下载该需求总表</span>
                            </el-col>
                        </el-row>
                        <div v-if="isShow" class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th>商品标签</th>
                                                <th style="width:200px;">商品名称</th>
                                                <th style="width:130px">商品代码</th>
                                                <th style="width:150px">商品规格码</th>
                                                <th style="width:150px">商家编码</th>
                                                <th style="width:130px">商品需求总量</th>
                                                <th style="width:130px">实采数量</th>
                                                <th style="width:130px">采购备注</th>
                                                <th style="width:130px">物流备注</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr v-for="item in tableData.purchase_goods_info">
                                        <td>
                                            <span v-for="labelInfo in item.goods_label_list" class="PR_ten">
                                                <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                            </span>
                                        </td>
                                        <td class="overOneLinesHeid" style="width:200px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td style="width:130px">{{item.erp_prd_no}}</td>
                                        <td style="width:150px">{{item.spec_sn}}</td>
                                        <td style="width:150px">{{item.erp_merchant_no}}</td>
                                        <td style="width:130px">{{item.goods_num}}</td>
                                        <td style="width:130px">{{item.day_buy_num}}</td>
                                        <td style="width:130px">{{item.purchase_remark}}</td>
                                        <td style="width:130px">{{item.remark}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <notFound v-if="isNotFound"></notFound>
                    </div>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { someDataLongByJson } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
    components:{
        notFound,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        loading:'',
        headersStr:'',
        isNotFound:false,
        isShow:true,
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
            axios.post(vm.url+vm.$batchTotalDetailURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                },
                {
                    headers:vm.headersStr
                }
            ).then(function(res){
                vm.loading.close()
                if(res.data.code=='1000'){
                    vm.tableData=res.data.data;
                    vm.isNotFound=false;
                    vm.isShow=true;
                    tableStyleByDataLength(res.data.data.goods_num,15);
                }else{
                    vm.isNotFound=true;
                    vm.isShow=false;
                    vm.$message(res.data.msg);
                }
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
            document.getElementById("hh").scrollLeft=b;
        },
        //回上一级页面
        backUpPage(){
            let vm=this;
            if(vm.$route.query.isEspected){
                vm.$router.push('/expectedToArrive');
            }
            if(vm.$route.query.isuploadDiff){
                vm.$router.push('/uploadDiffData');
            }
            if(vm.$route.query.isBatchSte){
                vm.$router.push('/batchSetting');
            }
        },
        //下载表格 
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downloadBatchTotalListURL+'?purchase_sn='+vm.$route.query.purchase_sn+'&token='+headersToken);
        },
        labelStyle(color){
            return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
        },
        // labelWidth(){
        //     let vm = this;
        //     let width=someDataLongByJson(vm.tableData.purchase_goods_info,'goods_label_list')*60; 
        //     return "width:"+width+"px";
        // },
        // tableWidth(){ 
        //     let vm = this;
        //     let width = someDataLongByJson(vm.tableData.purchase_goods_info,'goods_label_list')*60+1500;
        //     return "width:"+width+"px";
        // },
    }
}
</script>
<style>
.batchSummary .d_table{
    float: right;
    margin-right: 20px;
    cursor: pointer;
    color: #4677c4;
}
.batchSummary .t_i{width:100%; height:auto;}
.batchSummary .t_i_h{width:100%; overflow-x:hidden; background:#fafafa;}
.batchSummary .ee{width:100%!important; width:100%; text-align: center;}
.batchSummary .t_i_h table{width:100%;}
.batchSummary .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.batchSummary .cc{width:100.6%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.batchSummary .cc table{width:100%; }
.batchSummary .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>

<style scoped lang=less>
@import '../../css/dataCollection.less';
</style>