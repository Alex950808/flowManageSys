<template>
  <div class="timeoutCheck_b">
    <!-- 超时未清点页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row>
                        <div class="timeoutCheck">
                            <div class="purchaseDiscount tableTitleStyle">
                                <span><span class="coarseLine MR_ten"></span>超时未清点</span>
                                <searchBox @searchFrame='searchFrame'></searchBox>
                            </div>
                            <table class="tableTitle">
                                <thead>
                                    <th class="widthTwoHundred">批次单号</th>
                                    <th class="widthTwoHundred">采购数量</th>
                                    <th class="widthTwoHundred">仓位</th>
                                    <th class="widthTwoHundred">交货方式</th>
                                    <th class="widthTwoHundred">采购渠道</th>
                                    <th class="widthTwoHundred">采购方式</th>
                                    <th class="widthTwoHundred">提货日</th>
                                    <th class="widthTwoHundred">到货日</th>
                                    <th class="widthTwoHundred">操作</th>
                                </thead>
                            </table>
                            <table class="tableTitleTwo">
                                <thead>
                                    <th class="widthTwoHundred">批次单号</th>
                                    <th class="widthTwoHundred">采购数量</th>
                                    <th class="widthTwoHundred">仓位</th>
                                    <th class="widthTwoHundred">交货方式</th>
                                    <th class="widthTwoHundred">采购渠道</th>
                                    <th class="widthTwoHundred">采购方式</th>
                                    <th class="widthTwoHundred">提货日</th>
                                    <th class="widthTwoHundred">到货日</th>
                                    <th class="widthTwoHundred">操作</th>
                                </thead>
                            </table>
                            <div class="content" v-for="item in tableData">
                                <div class="content_text">
                                    <!-- <span class="stage">{{item.title_info.purchase_id}}期</span><span class="number">{{item.title_info.purchase_sn}}</span> -->
                                    <span v-if="item.title_info.purchase_id!=null" class="stage">{{item.title_info.purchase_id}}期</span>
                                    <span v-if="item.title_info.purchase_sn!=null" class="stage ML_twenty">汇总需求单号：{{item.title_info.purchase_sn}}</span>
                                    <span v-if="item.title_info.sum_demand_name!=null" class="stage ML_twenty">合期名称：{{item.title_info.sum_demand_name}}</span>
                                </div>
                                <el-row>
                                    <el-col :span="12">
                                        <table class="table-one">
                                            <tr>
                                                <td>实采总数({{item.title_info.real_buy_num}})</td>
                                                <td>自提数量({{item.title_info.zt_num}}批({{item.title_info.zt_goods_num}}件))</td>
                                                <td>邮寄数量({{item.title_info.yj_num}}批({{item.title_info.yj_goods_num}}件))</td>
                                            </tr>
                                        </table>
                                    </el-col>
                                    <el-col :span="4" :offset="8">
                                    </el-col>
                                </el-row>
                                <table class="table-two" v-for="realList in item.real_list">
                                    <tr v-for="(real,index) in realList">
                                        <td class="widthTwoHundred">{{real.real_purchase_sn}}</td>
                                        <td class="widthTwoHundred">{{real.total_buy_num}}件</td>
                                        <!-- <td class="widthTwoHundred">{{real.create_time}}</td> -->
                                        <td class="widthTwoHundred">{{real.port_name}}</td>
                                        <td class="widthTwoHundred">
                                            <span v-if="real.path_way==0">自提</span>
                                            <span v-if="real.path_way==1">邮寄</span>
                                            <span v-else></span>
                                        </td>
                                        <td class="widthTwoHundred">{{real.channels_name}}</td>
                                        <td class="widthTwoHundred">{{real.method_name}}</td>
                                        <td class="widthTwoHundred">{{real.delivery_time}}</td>
                                        <td class="widthTwoHundred">{{real.arrive_time}}</td>
                                        <td style="width:200px;" v-if="real.batch_cat!='2'||index==0">
                                            <span v-if="real.is_display==1" @click="goDetails(real.real_purchase_sn,item.title_info.purchase_sn,real.group_sn,real.is_mother)" class="bgButton">
                                                确认清点
                                                <span style="display: none" class="el-icon-loading confirmLoading"></span>
                                            </span>
                                            <span v-if="real.is_display==0" style="cursor: pointer;color:#ccc;">去清点</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                            </el-pagination>
                        </div>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        search:"",//输入框输入内容
        total:0,//页数默认为0
        page:1,//分页页数
        pagesize:15,//每页的数据条数
        isShow:false,
        headersStr:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getTimeOutCheckData();
        this.tableTitle();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      //获取超时未清点采购数据
      getTimeOutCheckData(){
        let vm=this;
        axios.get(vm.url+vm.$goodsCheckOutListURL+"?keywords="+vm.search+"&is_expire=1"+"&status=1",
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.tableData=res.data.data
            if(res.data.code==1002){
                vm.isShow=true;
            }else if(res.data.code==1000){
                vm.isShow=false;
            }
            vm.loading.close()
            vm.total=res.data.data_num;
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //点击前往详情页
      goDetails(real_purchase_sn,purchase_sn,group_sn,is_mother){
          let vm=this;
            axios.get(vm.url+vm.$allotGoodsURL+"?real_purchase_sn="+real_purchase_sn+"&group_sn="+group_sn+"&purchase_sn="+purchase_sn+"&is_mother="+is_mother,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                    sessionStorage.setItem("tableData",JSON.stringify(res.data.realPurchaseInfo.goods_list));
                    vm.$router.push('/clearPoint?real_purchase_sn='+real_purchase_sn+'&purchase_sn='+purchase_sn+"&group_sn="+group_sn+"&is_mother="+is_mother+'&isTimeOutCheck=isTimeOutCheck');
            }).catch(function (error) {
                if(error.response.status=="1020"){
                    vm.$message(error.response.msg); 
                }
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      tableTitle(){
            let vm=this;
            $(window).scroll(function(){
                var scrollTop = $(this).scrollTop();
                var thisHeight=160;
                if(scrollTop>thisHeight){
                    $('.tableTitleTwo').show();
                    $(".timeoutCheck_b .tableTitleTwo").addClass("addclass");
                    $(".timeoutCheck_b .tableTitleTwo").width($(".timeoutCheck_b .tableTitle").width());
                }else if(scrollTop<thisHeight){
                    $('.tableTitleTwo').hide();
                    $(".timeoutCheck_b .tableTitleTwo").removeClass("addclass");
                }
            })
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getTimeOutCheckData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getTimeOutCheckData()
      },
      //搜索框
      searchFrame(e){
          let vm=this;
          vm.search=e;
          vm.page=1;
          this.getTimeOutCheckData();
      },
    }
}
</script>
<style>
.el-table .warning-row {
    font-weight: bold;
}
.el-table{
    text-align: center;
    margin-bottom: 40px;
}
.el-table th>.cell {
    text-align: center;
}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
.timeoutCheck_b .table-two{
    width: 90%;
    line-height: 50px;
    text-align: center;
    margin-top: 15px;
    margin-left: 5%;
    margin-bottom: 15px;
    border: 1px solid #d9e3f3;
    border-radius: 10px;
}
.timeoutCheck_b .tableTitle{
    width: 88%;
    margin-left: 6%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.timeoutCheck_b .tableTitleTwo{
    width: 88%;
    margin-left: 4.5%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.timeoutCheck_b .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
</style>

<style scoped lang=less>
@import '../../css/logisticsModule.less';
</style>