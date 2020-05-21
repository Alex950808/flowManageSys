<template>
  <div class="batchSetting_b">
    <!-- 待清点采购单页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" class="Height" style="background-color: #fff;z-index:-2">
            <el-row class="bgDiv" style="z-index:2">
                <fontWatermark></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="batchSetting">
                        <div class="listTitleStyle select">
                            <span><span class="coarseLine MR_ten"></span>清点批次单</span>
                            <!-- <searchBox @searchFrame='searchFrame'></searchBox> -->
                            <div class="d_I_B F_S_Sixteen ML_ten">
                                提货日：<el-date-picker v-model="delivery_time" value-format="yyyy-MM-dd" type="date" placeholder="选择提货日"></el-date-picker>
                                到货日：<el-date-picker v-model="arrive_time" value-format="yyyy-MM-dd" type="date" placeholder="选择到货日"></el-date-picker>
                                <span class="bgButton" @click="searchByTime()">搜索</span>
                            </div>
                        </div>
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
                            <table class="table-two">
                                <thead>
                                    <th style="width:50px">状态</th>
                                    <th class="widthTwoHundred">批次单号</th>
                                    <th class="widthOneFiveHundred">商品数量</th>
                                    <th class="widthOneFiveHundred">提货日</th>
                                    <th class="widthOneFiveHundred">到货日</th>
                                    <th class="widthOneFiveHundred">仓位名称</th>
                                    <th class="widthOneFiveHundred">自提/邮寄 </th>
                                    <th class="widthOneFiveHundred">渠道名称</th>
                                    <th class="widthOneFiveHundred">方式名称</th>
                                    <th  class="widthOneFiveHundred">操作</th>
                                </thead>
                            </table>
                            <table class="table-two" v-for="realList in item.real_list">
                                <tr v-for="(real,index) in realList">
                                    <td style="width:50px">
                                        <span v-if="real.expire_status==1"> 
                                            <el-tooltip class="item" effect="dark" content="超时" placement="top-start">
                                                <i class="el-icon-warning redFont"></i>
                                            </el-tooltip>
                                        </span>
                                        <span v-if="real.expire_status==0">
                                            <el-tooltip class="item" effect="dark" content="待清点" placement="top-start">
                                                <i class="el-icon-warning D_greenFont"></i>
                                            </el-tooltip>
                                        </span>
                                    </td>
                                    <td class="widthTwoHundred">{{real.real_purchase_sn}}</td>
                                    <td class="widthOneFiveHundred">{{real.total_buy_num}}件</td>
                                    <td class="widthOneFiveHundred">{{real.delivery_time}}</td>
                                    <td class="widthOneFiveHundred">{{real.arrive_time}}</td>
                                    <td class="widthOneFiveHundred">{{real.port_name}}</td>
                                    <td class="widthOneFiveHundred">
                                        <span v-if="real.path_way==0">自提</span>
                                        <span v-if="real.path_way==1">邮寄</span>
                                        <span v-else></span>
                                    </td>
                                    <td class="widthOneFiveHundred">{{real.channels_name}}</td>
                                    <td class="widthOneFiveHundred">{{real.method_name}}</td>
                                    <td  class="widthOneFiveHundred" v-if="real.batch_cat!='2'">
                                        <!-- ||index==0 -->
                                        <span v-if="real.is_display==1" @click="goDetails(real.real_purchase_sn,item.title_info.purchase_sn,real.group_sn,real.is_mother)" class="bgButton">
                                            去清点
                                            <span style="display: none" class="el-icon-loading confirmLoading"></span>
                                        </span>
                                        <span v-if="real.is_display==0" class="infoBgButton">去清点</span> 
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </div>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox';
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
        arrive_time:'',
        delivery_time:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getPendingOrderData();
        this.tableTitle();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      //获取待清点采购数据
      getPendingOrderData(){
        let vm=this;
        axios.get(vm.url+vm.$goodsCheckListURL+"?keywords="+vm.search+"&page="+vm.page+"&pageSize="+vm.pagesize+
            "&delivery_time="+vm.delivery_time+"&arrive_time="+vm.arrive_time,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.tableData=res.data.data
            vm.total=res.data.data_num 
            vm.loading.close()
            if(res.data.code==1002){
                vm.isShow=true;
                vm.tableData=[];
                vm.total=0;
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
      //搜索框
    //   searchFrame(e){
    //       let vm=this;
    //       vm.search=e;
    //       vm.page=1;
    //       this.getPendingOrderData();
    //   },
      //通过提货日或者到货日搜索
      searchByTime(){
          let vm=this;
          vm.page=1;
          if(vm.delivery_time==null){
              vm.delivery_time='';
          }
          if(vm.arrive_time==null){
              vm.arrive_time='';
          }
          this.getPendingOrderData();
      },
      //点击前往详情页
      goDetails(real_purchase_sn,purchase_sn,group_sn,is_mother){
            let vm=this;
            let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
            axios.get(vm.url+vm.$allotGoodsURL+"?real_purchase_sn="+real_purchase_sn+"&group_sn="+group_sn+"&purchase_sn="+purchase_sn+"&is_mother="+is_mother,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                    loa.close();
                    sessionStorage.setItem("tableData",JSON.stringify(res.data.realPurchaseInfo.goods_list));
                    vm.$router.push('/clearPoint?real_purchase_sn='+real_purchase_sn+"&purchase_sn="+purchase_sn+"&group_sn="+group_sn+"&is_mother="+is_mother+"&isPending=isPending");
            }).catch(function (error) {
                loa.close();
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
            var thisHeight=370;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".batchSetting_b .tableTitleTwo").addClass("addclass");
                $(".batchSetting_b .tableTitleTwo").width($(".batchSetting_b .tableTitle").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".batchSetting_b .tableTitleTwo").removeClass("addclass");
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
          vm.getPendingOrderData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getPendingOrderData()
      },
    }
}
</script>
<style>
.batchSetting_b .tableTitle{
    width: 89%;
    margin-left: 5%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.batchSetting_b .tableTitleTwo{
    width:89%;
    margin-left: 4.1%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.batchSetting_b .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.status{
    cursor: pointer;
}
.batchSetting_b .xiuzheng{
    width: 88px;
    height: 30px;
    line-height: 30px;
    border-radius: 10px;
    display: inline-block;
    background-color: #4677c4;
    color: #fff;
}
.batchSetting_b .el-icon-close{
    margin-left: 270px;
}
</style>
<style scoped lang=less>
@import '../../css/taskModule.less';
</style>