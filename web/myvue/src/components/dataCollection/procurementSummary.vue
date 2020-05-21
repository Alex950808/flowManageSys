<template>
  <div class="procurementSummary_b">
    <!-- 实时采购数据上传数据汇总页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="procurementSummary viewSummary">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <el-row>
                            <el-col :span="20" class="title_left">
                                <span class="bgButton" @click=" backUpPage()">返回上一级</span>
                                <span class="stage">{{`${this.$route.query.purchase_sn}`}}</span>
                                <!-- <span class="date">需求单</span> -->
                            </el-col>
                            <el-col :span="4" class="title_left">
                                <span class="notBgButton" @click="d_table()">下载该需求总表</span>
                            </el-col>
                        </el-row>
                        <div class="t_i MB_twenty">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th style="width:300px;">商品名称</th>
                                                <th style="width:150px">商品代码</th>
                                                <th style="width:150px">商品规格码</th>
                                                <th style="width:150px">商家编码</th>
                                                <th style="width:130px">商品数量</th>
                                                <th style="width:130px">实采数量</th>
                                                <th style="width:130px">差异数</th>
                                                <!-- <th style="width:130px">采购备注</th>
                                                <th style="width:130px">物流备注</th> -->
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr v-for="item in tableData">
                                        <td class="overOneLinesHeid" style="width:300px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;300px">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td style="width:150px">{{item.erp_prd_no}}</td>
                                        <td style="width:150px">{{item.spec_sn}}</td>
                                        <td style="width:150px">{{item.erp_merchant_no}}</td>
                                        <td style="width:130px">{{item.goods_num}}</td>
                                        <td style="width:130px">{{item.day_buy_num}}</td>
                                        <td style="width:130px">{{item.diff_num}}</td>
                                        <!-- <td style="width:130px">{{item.purchase_remark}}</td>
                                        <td style="width:130px">{{item.remark}}</td> -->
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </el-col>
                    <div v-if="isShow" style="text-align: center;line-height:50px;"><img class="notData" src="../../image/notData.png"/></div>
                </el-row>
            </div>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        loading:'',
        isShow:false,
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
            axios.post(vm.url+vm.$dataTotalDetailURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                },
                {
                    headers:vm.headersStr
                }
            ).then(function(res){
                if(res.data.code==1002){
                    vm.isShow=true;
                }
                vm.tableData=res.data.data.purchase_goods_info;
                tableStyleByDataLength(res.data.data.goods_num,15);
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
            if(vm.$route.query.isUpData){
                vm.$router.push('/upData');
            }
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downloadDataTotalListURL+'?purchase_sn='+vm.$route.query.purchase_sn+'&token='+headersToken);
        },
    }
}
</script>
<style>
/* @import '../../css/common.css'; */
.procurementSummary .ellipsis span{
    /* width: 350px; */
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.procurementSummary .d_table{
    float: right;
    margin-right: 20px;
    cursor: pointer;
    color: #4677c4;
}
.procurementSummary .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.procurementSummary .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.procurementSummary .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.procurementSummarccc td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.procurementSummary .dd{height:659px!important; height:659px; overflow-y:hidden;}
.procurementSummary .t_i{width:100%; height:auto;}
.procurementSummary .t_i_h{width:100%; overflow-x:hidden; background:#fafafa;}
.procurementSummary .ee{width:100%!important; width:100%; text-align: center;}
.procurementSummary .t_i_h table{width:100%;}
.procurementSummary .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.procurementSummary .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.procurementSummary .cc table{width:100%; }
.procurementSummary .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>

<style scoped lang=less>
@import '../../css/dataCollection.less';
</style>