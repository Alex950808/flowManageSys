<template>
  <div class="batchDetails_b">
    <!-- 采购批次详情页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="batchDetails">
                        <el-row class="title_left tableTitleStyle">
                            <el-col :span="20">
                                <router-link  to='/expectedToArrive'><span class="bgButton">返回上一级</span></router-link>
                                <span class="date">{{title}}</span>
                                <input class="inputStyle" placeholder="请输入搜索关键字" type="text"/><i class="el-icon-search search"></i>
                            </el-col>
                            <el-col :span="4" class="title_right">
                                <span class="notBgButton" @click="d_table()" style="cursor: pointer;">下载该需求表格</span>
                            </el-col>
                        </el-row>
                        <div v-if="isShow" class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th style="width:300px;">商品名称</th>
                                                <th class="widthTwoHundred">商品代码</th>
                                                <th class="widthTwoHundred">商家编码</th>
                                                <th class="widthTwoHundred">商品规格码</th>
                                                <th class="widthTwoHundred">实采数量</th>
                                                <th class="widthTwoHundred">备注</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr v-for="item in tableData">
                                        <td class="overOneLinesHeid" style="width:300px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td class="widthTwoHundred">{{item.erp_prd_no}}</td>
                                        <td class="widthTwoHundred">{{item.erp_merchant_no}}</td>
                                        <td class="widthTwoHundred">{{item.spec_sn}}</td>
                                        <td class="widthTwoHundred">{{item.day_buy_num}}</td>
                                        <td class="widthTwoHundred">{{item.remark}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <notFound v-if="isNotFound"></notFound>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import { tableStyleByDataLength } from '../../filters/publicMethods.js'//引入有公用方法的jsfile
export default {
    components:{
        notFound,
    },
    data() {
        return {
            url: `${this.$baseUrl}`,
            tableData:[],  //详情数据
            isShow:false,
            title:'',
            headersStr:'',
            isNotFound:false,
            isShow:true,
        };
    },
    mounted(){ 
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getDetailsData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getDetailsData(){
            let vm=this;
            vm.title=vm.$route.query.real_purchase_sn;
            axios.post(vm.url+vm.$batchDetailURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "real_purchase_sn":vm.$route.query.real_purchase_sn, 
                    "group_sn":vm.$route.query.group_sn,
                    "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close();
                if(res.data.code=='1000'){
                    vm.tableData=res.data.data;
                    vm.isNotFound=false;
                    vm.isShow=true;
                    tableStyleByDataLength(vm.tableData.length,15);
                }else{
                    vm.isNotFound=true;
                    vm.isShow=false;
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //下载表格 
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
             window.open(vm.url+vm.$downloadBatchListURL+'?real_purchase_sn='+vm.$route.query.real_purchase_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&is_mother="+vm.$route.query.is_mother+'&token='+headersToken);
        }
    }
}
</script>
<style>
.title_right{
    float: right;
}
.batchDetails_b .t_i{width:100%; height:auto;}
.batchDetails_b .t_i_h{width:100%; overflow-x:hidden; background:#fafafa;}
.batchDetails_b .ee{width:100%!important; width:100%; text-align: center;}
.batchDetails_b .t_i_h table{width:100%;}
.batchDetails_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.batchDetails_b .cc{width:100.6%; height:659px; border-bottom:1px solid #ccc; overflow:auto;}
.batchDetails_b .cc table{width:100%; }
.batchDetails_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>