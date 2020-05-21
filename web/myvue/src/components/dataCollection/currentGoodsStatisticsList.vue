<template>
  <div class="currentGoodsStatisticsList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle title">
                        <span class="coarseLine MR_ten"></span>本月商品渠道统计列表
                        <span class="bgButton p_a_y"  @click="dialogVisibleTitle = true">隐</span>
                    </div>
                    <el-row class="d_I_B F_Z_N ML_twenty MB_ten">
                        <span>商品名称 ：</span>
                        <span><el-input style="width:200px;" v-model="querySn" placeholder="请输入内容"></el-input></span>
                        <span>开始时间 ：</span>
                        <template>
                            <el-date-picker v-model="startTime" type="date" placeholder="选择日期"></el-date-picker>
                        </template>
                        <span class="ML_twenty">结束时间 ：</span>
                        <template>
                            <el-date-picker v-model="endTime" type="date" placeholder="选择日期"></el-date-picker>
                        </template>
                        <!-- <div class="block"> -->
                        <span class="demonstration ML_twenty">月</span>
                        <el-date-picker v-model="monthTime" type="month" placeholder="选择月"></el-date-picker>
                        <!-- </div> -->
                        <span class="bgButton ML_twenty" @click="searchFrame()">搜索</span>
                    </el-row>
                    <el-row class="t_i fontCenter">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <thead>
                                        <tr>
                                            <th v-if="selectTitle('商品名称')" style="width:200px" rowspan="3">商品名称</th>
                                            <th v-if="selectTitle('商品规格码')" style="width:160px" rowspan="3">商品规格码</th>
                                            <th v-if="selectTitle('商家编码')" style="width:160px" rowspan="3">商家编码</th>
                                            <th v-if="selectTitle('美金原价')" style="width:100px" rowspan="3">美金原价</th>
                                            <th v-for="item in titleData" v-if="selectTitle(item.channel_name)" style="width:1100px">{{item.channel_name}}</th>
                                        </tr>
                                        <tr>
                                            <th v-for="item in titleData" v-if="selectTitle(item.channel_name)" style="width:1100px">
                                                <span style="width:90px;display: inline-block;">实采数</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:130px;display: inline-block;">下单时间金额</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:180px;display: inline-block;">下单时间差异金额</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:180px;display: inline-block;">下单时间差额百分比</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:130px;display: inline-block;">提货时间金额</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:180px;display: inline-block;">提货时间差异金额</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:180px;display: inline-block;">提货时间差额百分比</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th v-for="item in titleData" v-if="selectTitle(item.channel_name)" style="width:1100px">
                                                <span style="width:90px;display: inline-block;">{{item.channel_real_num}}</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:130px;display: inline-block;">{{item.cc_total_price}}</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span :style="fontColor(item.cq_diff_price)" style="width:180px;display: inline-block;">{{item.cq_diff_price}}</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span :style="fontColor(item.create_diff_rate)" style="width:180px;display: inline-block;">{{item.create_diff_rate}}%</span>
                                                <span style="color:#ebeef5;">|</span>
                                                <span style="width:130px;display: inline-block;">{{item.cd_total_price}}</span>
                                                <span style="color:#ebeef5;">|</span> 
                                                <span :style="fontColor(item.dq_diff_price)" style="width:180px;display: inline-block;">{{item.dq_diff_price}}</span>
                                                <span style="color:#ebeef5;">|</span> 
                                                <span :style="fontColor(item.deliver_diff_rate)" style="width:180px;display: inline-block;">{{item.deliver_diff_rate}}%</span>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()"> 
                            <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                <tr v-for="(item,index) in tableData">
                                    <td v-if="selectTitle('商品名称')" class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                        <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </td>
                                    <td v-if="selectTitle('商品规格码')" style="width:160px">{{item.spec_sn}}</td>
                                    <td v-if="selectTitle('商家编码')" style="width:160px">{{item.erp_merchant_no}}</td>
                                    <td v-if="selectTitle('美金原价')" style="width:100px">{{item.spec_price}}</td>
                                    <td v-for="title in titleData" v-if="selectTitle(title.channel_name)" style="width:1100px">
                                        <span v-if="item[title.channel_name]!=undefined" style="width:90px;display: inline-block;">{{item[title.channel_name].day_buy_num}}</span>
                                        <span v-if="item[title.channel_name]==undefined" style="width:90px;display: inline-block;">{{'-'}}</span>
                                        <span style="color:#ebeef5;">|</span>
                                        <span v-if="item[title.channel_name]!=undefined" style="width:130px;display: inline-block;">{{item[title.channel_name].create_total_price}}</span>
                                        <span v-if="item[title.channel_name]==undefined" style="width:130px;display: inline-block;">{{'-'}}</span>
                                        <span style="color:#ebeef5;">|</span>
                                        <span v-if="item[title.channel_name]!=undefined" :style="fontColor(item[title.channel_name].cq_diff_price)" style="width:180px;display: inline-block;">{{item[title.channel_name].cq_diff_price}}</span>
                                        <span v-if="item[title.channel_name]==undefined" style="width:180px;display: inline-block;">{{'-'}}</span>
                                        <span style="color:#ebeef5;">|</span>
                                        <span v-if="item[title.channel_name]!=undefined" :style="fontColor(item[title.channel_name].create_diff_rate)" style="width:180px;display: inline-block;">{{item[title.channel_name].create_diff_rate}}%</span>
                                        <span v-if="item[title.channel_name]==undefined" style="width:180px;display: inline-block;">{{'-'}}</span>
                                        <span style="color:#ebeef5;">|</span>
                                        <span v-if="item[title.channel_name]!=undefined" style="width:130px;display: inline-block;">{{item[title.channel_name].deliver_total_price}}</span>
                                        <span v-if="item[title.channel_name]==undefined" style="width:130px;display: inline-block;">{{'-'}}</span>
                                        <span style="color:#ebeef5;">|</span>
                                        <span v-if="item[title.channel_name]!=undefined" :style="fontColor(item[title.channel_name].dq_diff_price)" style="width:180px;display: inline-block;">{{item[title.channel_name].dq_diff_price}}</span>
                                        <span v-if="item[title.channel_name]==undefined" style="width:180px;display: inline-block;">{{'-'}}</span>
                                        <span style="color:#ebeef5;">|</span>
                                        <span v-if="item[title.channel_name]!=undefined" :style="fontColor(item[title.channel_name].deliver_diff_rate)" style="width:180px;display: inline-block;">{{item[title.channel_name].deliver_diff_rate}}%</span>
                                        <span v-if="item[title.channel_name]==undefined" style="width:180px;display: inline-block;">{{'-'}}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
                    <div class="lineHeightForty">注(差异金额及差额百分比)：<span class="redFont">红色字体代表值大于0</span>
                        <span class="blueFont">蓝色字体代表值小于0</span>
                        <span>黑色字体代表值等于0</span>
                    </div>
                    <el-dialog title="选择要展示的信息" :visible.sync="dialogVisibleTitle" width="800px">
                        <div class="selectTitle">
                            <template>
                                <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                <span class="redFont" style="margin-left: -85px;">*选择列时至少要保留一个展示信息</span>
                                <div style="margin: 15px 0;"></div>
                                <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
                                    <el-checkbox v-for="city in cityOptions" :label="city" :key="city">{{city}}</el-checkbox>
                                </el-checkbox-group>
                            </template>
                        </div>
                    </el-dialog>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound'
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { monthToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import {tableStyleByDataLength} from '@/filters/publicMethods.js'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      titleData:'',
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      //选择表头所需数据
      dialogVisibleTitle:false,
      checkAll: false,
      checkedCities:['商品名称','商品规格码','商家编码','美金原价'],
      isIndeterminate: true,
      cityOptions:['商品名称','商品规格码','商家编码','美金原价'],
      //搜索
      startTime:'',
      endTime:'',
      monthTime:'',
      querySn:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    //选择表头数据  
    handleCheckAllChange(val) {
        this.checkedCities = val ? this.cityOptions : [];
        if(this.checkedCities.length==0){
            this.checkedCities=['商品名称','商品规格码','商家编码','美金原价'];
            return false;
        }
        this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
        let vm = this;
        let checkedCount = value.length;
        if(checkedCount==0){
            this.checkedCities=['商品名称','商品规格码','商家编码','美金原价'];
        }
        this.checkAll = checkedCount === this.cityOptions.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cityOptions.length;
    },
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        vm.cityOptions.splice(0);
        vm.checkedCities.splice(0);
        vm.cityOptions=['商品名称','商品规格码','商家编码','美金原价'];
        vm.checkedCities=['商品名称','商品规格码','商家编码','美金原价'];
        axios.get(vm.url+vm.$currentGoodsStatisticsListURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&start_time="+vm.startTime+"&end_time="+vm.endTime+"&month_time="+vm.monthTime+"&query_sn="+vm.querySn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            load.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.channel_goods_total_list;
                vm.titleData=res.data.data.channel_total_list;
                vm.total=res.data.data.total_goods_num;
                tableStyleByDataLength(vm.tableData.length,16);
                res.data.data.channel_total_list.forEach(element=>{
                    vm.cityOptions.push(element.channel_name)
                    vm.checkedCities.push(element.channel_name)
                })
                vm.isShow=false;
            }else{
                vm.tableData=[];
                vm.titleData='';
                vm.total=0;
                vm.isShow=true;
            }
        }).catch(function (error) {
            vm.loading.close();
            load.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    searchFrame(){
        let vm = this;
        vm.page=1;
        if(vm.startTime!=null){
            if(vm.startTime.constructor==Date){
                vm.startTime = dateToStr(vm.startTime);
            }
        }else{
           vm.startTime=''; 
        }
        if(vm.endTime!=null){
            if(vm.endTime.constructor==Date){
                vm.endTime = dateToStr(vm.endTime);
            }
        }else{
            vm.endTime='';
        }
        if(vm.monthTime!=null){
            if(vm.monthTime.constructor==Date){
                vm.monthTime = monthToStr(vm.monthTime);
            }
        }else{
            vm.monthTime='';
        }
        vm.getDataList();
    },
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDataList()
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    fontColor(num){
        let vm = this;
        if(parseInt(num)<0){
            return "color:#4677c4";
        }else if(parseInt(num)>0){
            return "color:red";
        }
    }
  },
  computed:{
        //选择要展示的表头
        selectTitle(){
            let vm = this;
            return function(str){
                let judgeSelection=vm.checkedCities.find(function(e){
                    return e==str;
                });
                if(judgeSelection){
                    return true;
                }else{
                    return false;
                }
            }
        },
        tableWidth(){
            let vm=this;
            let aa = 0;
            vm.checkedCities.forEach(element=>{
                if(element.search("-") != -1 ){
                    aa++
                }
            })
            let widthLength =(vm.checkedCities.length-aa)*200+aa*1200
            let titleWidth=$(".title").width();
            if(widthLength<titleWidth){
                widthLength=titleWidth;
            }
            return "width:"+widthLength+"px"
        },
    }
}
</script>

<style>
/* .currentGoodsStatisticsList .t_i{width:100%; overflow-x:auto; height:auto;border-right: 1px solid #ccc;border-left: 1px solid #ccc;} */
.currentGoodsStatisticsList .t_i{width:100%; height:auto;}
.currentGoodsStatisticsList .t_i_h{width:100%; overflow-x:hidden;}
.currentGoodsStatisticsList .ee{width:100%!important; text-align: center;}
.currentGoodsStatisticsList .t_i_h table{width:100%;}
.currentGoodsStatisticsList .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.currentGoodsStatisticsList .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.currentGoodsStatisticsList .cc table{width:100%; }
.currentGoodsStatisticsList .cc table td{height:25px; text-align:center}
.currentGoodsStatisticsList .p_a_y{
    position: absolute;
    top: 125px;
    left: -44px;
}
.currentGoodsStatisticsList .selectTitle .el-checkbox+.el-checkbox {
    margin-right: 30px;
    width: 120px;
    height: 40px;
    line-height: 40px;
}
.currentGoodsStatisticsList .selectTitle .el-checkbox {
    margin-right: 30px;
    /* margin-left: 30px; */
    width: 120px;
    height: 40px;
    line-height: 40px;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
