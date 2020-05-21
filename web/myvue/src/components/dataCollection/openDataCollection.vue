<template>
  <div class="openDataCollection_b">
    <!-- 优采推荐查看数据汇总页面 -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="openDataCollection viewSummary">
                        <el-row>
                            <el-col :span="24" class="title_left">
                                <el-col :span="20">
                                    <router-link  to='/excellentRecommend'><span class="bgButton">返回上一级</span></router-link>
                                    <span class="stage">{{`${this.$route.query.purchase_sn}`}}</span>
                                </el-col>
                                <el-col :span="4">
                                    <span class="bgButton" @click="goUpData()">去上传</span>
                                    <span class="bgButton" @click="d_table()">下载该需求总表</span>
                                </el-col>
                            </el-col>
                        </el-row>
                        <div v-if="isShow" class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                        <thead>
                                            <tr>
                                                <th style="width:200px;">商品名称</th>
                                                <th style="width:150px">商品代码</th>
                                                <th style="width:160px">erp编码</th>
                                                <th style="width:160px">商品规格码</th>
                                                <th style="width:100px">需求数量</th>
                                                <th style="width:100px">可采量</th>
                                                <th style="width:100px">已采量</th>
                                                <th style="width:100px">待采量</th>
                                                <th style="width:100px">可采总量</th>
                                                <th style="width:100px">已采总量</th>
                                                <th style="width:100px">外采临界点</th>
                                                <th :style="discountWidth()">销售折扣</th>
                                                <th :style="divWidth()+'text-align: center;'">优采推荐</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <tr v-for="item in tableData">
                                        <td class="overOneLinesHeid" style="width:200px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td style="width:150px">{{item.erp_prd_no}}</td>
                                        <td style="width:160px">{{item.erp_merchant_no}}</td>
                                        <td style="width:160px">{{item.spec_sn}}</td>
                                        <td style="width:100px">{{item.goods_num}}</td>
                                        <td style="width:100px">{{item.may_buy_num}}</td>
                                        <td style="width:100px">{{item.real_buy_num}}</td>
                                        <td style="width:100px">{{item.diff_num}}</td>
                                        <td style="width:100px">{{item.total_may_buy_num}}</td>
                                        <td style="width:100px">
                                            <span v-if="parseInt(item.total_real_buy_num)>=parseInt(item.total_may_buy_num)" class="redFont">{{item.total_real_buy_num}}</span>
                                            <span v-else>{{item.total_real_buy_num}}</span>
                                        </td>
                                        <td style="width:100px">{{item.wai_line_point}}</td>
                                        <td :style="discountWidth()"><span class="discountWidth" v-for="(itemO,index) in item.sale_discount_info"><span class="Serial">{{index+1}}</span><span title="用户名称">{{itemO.name}}</span><span title="销售折扣">{{itemO.sale_discount}}</span></span></td>
                                        <td :style="divWidth()"><span v-for="(itemO,index) in item.discount_info" class="spanWidth"><span class="Serial">{{index+1}}</span><span class="channel" title="采购渠道">{{itemO.brand_channel}}</span>&nbsp;&nbsp;<span class="Discount" title="品牌折扣">{{itemO.brand_discount}}</span>&nbsp;&nbsp;<span title="可采数量">{{itemO.may_channel_num}}</span>&nbsp;&nbsp;<span title="待采数量">{{itemO.diff_channel_num}}</span>&nbsp;&nbsp;</span></td>
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
import { tableStyleByDataLength } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
export default {
    components:{
        notFound,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        loading:'',
        num:'',
        widthLength:'',
        discount_info:'',
        discount:'',
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
            axios.post(vm.url+vm.$recommendTotalDetailURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                },
                {
                    headers:vm.headersStr
                }
            ).then(function(res){
                vm.loading.close()
                if(res.data.code=='1000'){
                    vm.isNotFound=false;
                    vm.isShow=true;
                    vm.tableData=res.data.data;
                    var recLength=[];
                    var discount=[];
                    if(vm.tableData!=''){
                        vm.tableData.forEach(element=>{
                            recLength.push(element.discount_info.length);
                            discount.push(element.sale_discount_info.length);
                        })
                    }
                    var j=recLength[0];
                    for(var i=0;i<=recLength.length;i++){
                            if(recLength[i+1]>=j){
                                j=recLength[i+1]
                            }
                    }
                    vm.discount_info=j;
                    var k=discount[0];
                    for(var i=0;i<=discount.length;i++){
                            if(discount[i+1]>=k){
                                k=discount[i+1]
                            }
                    }
                    vm.discount=k;
                    tableStyleByDataLength(vm.tableData.length,15);
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
            if(vm.$route.query.isExcellent){
                vm.$router.push('/excellentRecommend');
            }
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downloadRecommendTotalListURL+'?purchase_sn='+vm.$route.query.purchase_sn+'&token='+headersToken);
        },
        goUpData(){
            let vm = this;
            vm.$router.push('/upData?purchase_sn='+vm.$route.query.purchase_sn);
        },
        divWidth(){
            let vm=this;
            var widthLength=vm.discount_info*300;
            vm.widthLength=widthLength;
            return "width:"+widthLength+"px;text-align: left;"
        },
        discountWidth(){
            let vm=this;
            var widthLength=vm.discount*130;
            return "width:"+widthLength+"px"
        },
        tableWidth(){
            let vm=this;
            var widthLength=1550+vm.widthLength+vm.discount*130;
            var tiWidth=$(".t_i").width();
            if(widthLength<tiWidth){
                return "width:"+tiWidth+"px"
            }else{
                return "width:"+widthLength+"px"
            } 
        }
    }
}
</script>
<style>

.openDataCollection .Serial{
    display: inline-block;
    border-radius: 50%;
    background-color: #00C1DE;
    color: #fff;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
}
.openDataCollection .spanWidth{
    width: 300px;
    display: inline-block;
}
/* .openDataCollection .channel{
    display: inline-block;
    width: 100px;
    text-align: left;
} */
.openDataCollection .Discount{
    display: inline-block;
    text-align: left;
}
.openDataCollection .discountWidth{
    display: inline-block;
    width: 130px;
    text-align: left;
}
.openDataCollection  .d_table{
    float: right;
    margin-right: 20px;
    cursor: pointer;
    color: #4677c4;
}
.openDataCollection .t_i{width:100%; height:auto;}
.openDataCollection .t_i_h{width:100%; overflow-x:hidden; background:#fafafa;}
.openDataCollection .ee{width:100%!important; width:100%; text-align: center;}
.openDataCollection .t_i_h table{width:1530px;}
.openDataCollection .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.openDataCollection .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.openDataCollection .cc table{width:1530px; }
.openDataCollection .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>

<style scoped lang=less>
@import '../../css/dataCollection.less';
</style>