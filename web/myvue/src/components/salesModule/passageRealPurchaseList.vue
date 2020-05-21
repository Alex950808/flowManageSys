<template>
  <div class="demandPurchaseDetail_b">
    <!-- 清点页面 -->
    <el-col :span="24" style="background-color: #fff;">
        <el-row>
            <el-col :span="22" :offset="1">
                <div class="demandPurchaseDetail">
                    <el-row>
                        <el-col :span="24" class="title_left">
                            <span class="bgButton" @click="backUpPage()">返回上一级</span>
                            <span class="date"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;{{`${this.$route.query.purchase_sn}`}}</span>
                            </el-col>
                    </el-row>
                    <div class="t_i">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                <thead>
                                    <tr>
                                        <th style="width:150px">实采单号</th>
                                        <th style="width:100px;">方式名称</th>
                                        <th style="width:100px">渠道名称</th>
                                        <th style="width:100px">自提/邮寄</th>
                                        <th style="width:100px">sku数量</th>
                                        <th style="width:100px">实采数量</th>
                                        <th style="width:100px">美金原价</th>
                                        <th style="width:100px">人民币总额</th>
                                        <th style="width:100px">提货日期</th>
                                        <th style="width:100px">到货日期</th>
                                        <th style="width:100px">状态</th>
                                        <th style="width:100px">查看详情</th>
                                    </tr>
                                </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc" id="cc" @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr v-for="(item,index) in tableData">
                                    <td style="width:150px">{{item.real_purchase_sn}}</td> 
                                    <td style="width:100px;">{{item.method_name}}</td>
                                    <td style="width:100px">{{item.channels_name}}</td>
                                    <td style="width:100px">
                                        <span v-if="item.path_way==0">自提</span>
                                        <span v-if="item.path_way==1">邮寄</span>
                                    </td>
                                    <td style="width:100px">{{item.sku_num}}</td>
                                    <td style="width:100px">{{item.day_buy_num}}</td>
                                    <td style="width:100px">{{item.cny_total_price}}</td>
                                    <td style="width:100px">{{item.real_total_price}}</td>
                                    <td style="width:100px">{{item.delivery_time}}</td>
                                    <td style="width:100px">{{item.arrive_time}}</td>
                                    <td style="width:100px">
                                        <span v-if="item.status==1">待清点</span>
                                        <span v-if="item.status==2">待确认差异</span>
                                        <span v-if="item.status==3">待开单</span>
                                        <span v-if="item.status==4">待入库</span>
                                        <span v-if="item.status==5">已完成</span>
                                    </td>
                                    <td style="width:100px">
                                        <i class="el-icon-view notBgButton" @click="viewDetails(item.real_purchase_sn)"></i>
                                    </td>
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
// import $ from 'jquery'
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import notFound from '@/components/UiAssemblyList/notFound'
export default {
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        title:[],
        search:'',
        show:false,
      };
    },
    mounted(){
        this.getclearPointData()
    },
    methods: {
        getclearPointData(){
            let vm=this;
            let tableData=JSON.parse(sessionStorage.getItem("tableData"));
            vm.tableData=tableData.data;
            if(vm.tableData.length==0){
                vm.show=true;
            }
        },
        //搜索框
        searchFrame(){
            let vm=this;
            vm.getclearPointData();
        },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("hh").scrollLeft=b;
        },
        //回上一级页面
        backUpPage(){
            let vm=this;
            vm.$router.push('/demandPassageList');
        },
        //查看详情
        viewDetails(real_purchase_sn){
            let vm = this;
            let switchoEvent=event;
            switchLoading(event)
            let headersToken=sessionStorage.getItem("token");
            let purchase_sn=vm.$route.query.purchase_sn;
            axios.get(vm.url+vm.$passageRealPurchaseDetailURL+"?&real_purchase_sn="+real_purchase_sn,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                if(res.data.code==1000){
                    sessionStorage.setItem("detailData",JSON.stringify(res.data));
                    vm.$router.push('/passageRealPurchaseDetail?real_purchase_sn='+real_purchase_sn+'&purchase_sn='+purchase_sn);
                }else{
                    switchoRiginally(event)
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                switchoRiginally(event)
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        }
    }
}
</script>
<style>
.demandPurchaseDetail_b .t_i{width:100%; height:auto;}
.demandPurchaseDetail_b .t_i_h{width:100%; overflow-x:hidden;}
.demandPurchaseDetail_b .ee{ text-align: center;}
.demandPurchaseDetail_b .t_i_h table{width:100%}
.demandPurchaseDetail_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.demandPurchaseDetail_b .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.demandPurchaseDetail_b .cc table{width:100%}
.demandPurchaseDetail_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}

.title_left {
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    padding-left: 35px;
    margin-top: 20px;
}
.title_left .back{
    display: inline-block;
    width: 130px;
    height: 50px;
    background-color: #4677c4;
    color: #fff;
    line-height: 50px;
    text-align: center;
    border-radius:10px; 
    cursor: pointer;
}
.stage{
    margin-left: 10px;
    font-weight: bold;
    font-size: 18px;
}
.date{
    margin-left: 10px;
    font-weight: bold;
    font-size: 18px;
}          
</style>

<style scoped lang=less>
@import '../../css/logisticsModule.less';
</style>