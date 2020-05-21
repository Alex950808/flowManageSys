<template>
  <div class="passageRealPurchaseDetail demandPurchaseDetail_b">
    <!-- 清点页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="demandPurchaseDetail">
                        <el-row>
                            <el-col :span="24" class="title_left">
                                <span class="bgButton" @click="backUpPage()">返回上一级</span>
                                <span class="date"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;{{`${this.$route.query.real_purchase_sn}`}}</span>
                                <!-- <span class="date"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;{{`${this.$route.query.demand_sn}`}}</span> -->
                            </el-col>
                        </el-row>
                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th style="width:300px;">商品名称</th>
                                            <th style="width:150px">商品代码</th>
                                            <th style="width:150px">erp编码</th>
                                            <th style="width:150px">商品规格码</th>
                                            <th style="width:150px">人民币总金额</th>
                                            <th style="width:100px">实采数量</th>
                                            <th style="width:100px">美金原价</th>
                                            <th style="width:100px">美金总金额</th>
                                        </tr>
                                    </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr v-for="(item,index) in tableData">
                                        <td style="width:300px;" class="ellipsis">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span>{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td style="width:150px">{{item.erp_prd_no}}</td>
                                        <td style="width:150px">{{item.erp_merchant_no}}</td> 
                                        <td style="width:150px">{{item.spec_sn}}</td> 
                                        <td style="width:150px">{{item.cny_total_price}}</td> 
                                        <td style="width:100px">{{item.day_buy_num}}</td>
                                        <td style="width:100px">{{item.spec_price}}</td>
                                        <td style="width:100px">{{item.spec_total_price}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
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
            let tableData=JSON.parse(sessionStorage.getItem("detailData"));
            vm.tableData=tableData.data;
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
            let purchase_sn=vm.$route.query.purchase_sn
            vm.$router.push('/passageRealPurchaseList?purchase_sn='+purchase_sn);
        },
    }
}
</script>
<style>
.demandPurchaseDetail_b .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.passageRealPurchaseDetail .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.passageRealPurchaseDetail .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.passageRealPurchaseDetail .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.passageRealPurchaseDetail .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.passageRealPurchaseDetail .dd{height:659px!important; height:659px; overflow-y:hidden;}
.passageRealPurchaseDetail .t_i{width:100%; height:auto;}
.passageRealPurchaseDetail .t_i_h{width:100%; overflow-x:hidden;}
.passageRealPurchaseDetail .ee{ text-align: center;}
.passageRealPurchaseDetail .t_i_h table{width:100%;}
.passageRealPurchaseDetail .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.passageRealPurchaseDetail .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.passageRealPurchaseDetail .cc table{width:100%;}
.passageRealPurchaseDetail .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}

.demandPurchaseDetail_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}

.demandPurchaseDetail_b .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 19px;
}
.demandPurchaseDetail_b .file input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
}
.demandPurchaseDetail_b .Detailed{
    width: 120px;
    height: 50px;
    cursor: pointer;
    background-color: #00C1DE;
    line-height: 50px;
    vertical-align: 19px;
    color: #fff;
    border-radius: 10px;
    text-align: center;
    display: inline-block;
    margin-right: 10px;
}
.title_left {
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    /* font-weight:bold; */
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
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}          
</style>

<style scoped lang=less>
@import '../../css/logisticsModule.less';
</style>