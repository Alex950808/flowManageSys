<template>
  <div class="purchaseChannelStatisticsList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle title">
                        <span class="coarseLine MR_ten"></span>采购期渠道统计列表
                        <div class="d_I_B F_Z_N ML_twenty">
                            <span>商品名称 ：</span>
                            <span><el-input style="width:200px;" v-model="querySn" placeholder="请输入内容"></el-input></span>
                            <span>开始时间 ：</span>
                            <template>
                                <el-date-picker v-model="startTime" type="date" placeholder="选择日期"></el-date-picker>
                            </template>
                            <span>结束时间 ：</span>
                            <template>
                                <el-date-picker v-model="endTime" type="date" placeholder="选择日期"></el-date-picker>
                            </template>
                            <span class="bgButton" @click="searchFrame('0')">搜索</span>
                            <span class="bgButton" v-if="is_caigoubu==1" @click="searchFrame('1')">合期</span>
                        </div>
                        <span class="bgButton p_a_y"  @click="dialogVisibleTitle = true">隐</span>
                    </div>
                    <el-row class="lineHeightForty">
                        <div class="d_I_B F_Z_N ML_twenty">
                            <span class="bgButton PR MR_twenty" v-for="item in purchaseSumDate">
                                <span @click="retrieval(item.end_time,item.start_time)">{{item.entrust_time}}</span>
                                <i v-if="is_caigoubu==1" @click="deleteTheTime(item.id)" class="el-icon-error p_a whiteFont Cursor"></i>
                            </span>
                        </div>
                    </el-row>

                    <el-row class="t_i fontCenter">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <thead>
                                        <tr>
                                            <th style="width:200px" v-if="selectTitle('商品名称')" rowspan="3">商品名称</th>
                                            <th style="width:100px" v-if="selectTitle('商品品牌')" rowspan="3">商品品牌</th>
                                            <th style="width:170px" v-if="selectTitle('商品规格码')" rowspan="3">商品规格码</th>
                                            <th style="width:170px" v-if="selectTitle('商家编码')" rowspan="3">商家编码</th>
                                            <th style="width:110px" v-if="selectTitle('美金原价')" rowspan="3">美金原价</th>
                                            <th style="width:140px" v-if="selectTitle('商品总重量')" rowspan="3">商品总重量</th>
                                            <th style="width:110px" v-if="selectTitle('商品单重')" rowspan="3">商品单重</th>
                                            <th style="width:140px" v-if="selectTitle('商品需求总量')" rowspan="3">商品需求总量</th>
                                            <th style="width:140px" v-if="selectTitle('商品待采量')" rowspan="3">商品待采量</th>
                                            <th style="width:600px" v-for="item in tableData.purchase_channel_list" colspan="6" v-if="selectTitle(item.channel_name)">{{item.channel_name}}</th>
                                            <th :style="divWidth()+'text-align: center;'" v-if="selectTitle('优采推荐')" rowspan="3">优采推荐</th>
                                        </tr>
                                        <tr>
                                            <th style="width:600px" v-for="item in tableData.purchase_channel_list" colspan="6" v-if="selectTitle(item.channel_name)">
                                                <span>目标金额($)：{{item.channel_may_price}}</span>
                                                <span class="ML_twenty">实际金额($)：{{item.channel_real_price}}</span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th v-for="item in thItem" style="width:100px" v-if="selectTitle(item.titleName)">{{item.thName}}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()"> 
                            <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                <tr v-for="(item,index) in tableData.goods_basic_list">
                                    <td class="overOneLinesHeid" v-if="selectTitle('商品名称')" style="width:200px;" :title="item.goods_name">
                                        <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                    </td>
                                    <td class="overOneLinesHeid" v-if="selectTitle('商品品牌')" style="width:100px;">
                                        <span style="-webkit-box-orient: vertical;width:100px;">{{item.brand_name}}</span>
                                    </td>
                                    <td style="width:170px" v-if="selectTitle('商品规格码')">{{item.spec_sn}}</td>
                                    <td style="width:170px" v-if="selectTitle('商家编码')">{{item.erp_merchant_no}}</td>
                                    <td style="width:110px" v-if="selectTitle('美金原价')">{{item.spec_price}}</td>
                                    <td style="width:140px" v-if="selectTitle('商品总重量')">{{item.total_weight}}</td>
                                    <td style="width:110px" v-if="selectTitle('商品单重')">{{item.spec_weight}}</td>
                                    <td style="width:140px" v-if="selectTitle('商品需求总量')">{{item.goods_num}}</td>
                                    <td style="width:140px" v-if="selectTitle('商品待采量')">{{item.wait_buy_num}}</td>
                                    <td v-for="tdInfo in tdItem[index]" v-if="selectTitle(tdInfo.titleName)" style="width:100px">
                                        {{tdInfo.tdName}}
                                    </td>
                                    <td :style="divWidth()" v-if="selectTitle('优采推荐')">
                                        <span v-for="(itemO,index) in item.discount_info" class="spanWidth">
                                            <span class="Serial">{{index+1}}</span>
                                            <span class="channel" title="采购渠道">{{itemO.channels_name+"-"+itemO.method_name}}</span>&nbsp;&nbsp;
                                            <span title="品牌折扣">{{itemO.brand_discount}}</span>&nbsp;&nbsp;
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
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
      <!-- <selectCol :selectStr="selectStr" @selectTitle="selectTitle"></selectCol> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
// import selectCol from '@/components/UiAssemblyList/selectCol'
// import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import {tableStyleByDataLength} from '@/filters/publicMethods.js'
import searchBox from '@/components/UiAssemblyList/searchBox';
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      searchBox,
      fontWatermark,
    //   selectCol,
  },
  data(){
    return{
      url: `${this.$baseUrl}`, 
      headersStr:'',
      tableData:[],
      purchaseSumDate:'',
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      thItem:[],
      tdItem:[],
      discountLength:'',
    //选择表头所需数据
      dialogVisibleTitle:false,
      checkAll: false,
      checkedCities:['商品名称','商品品牌','商品规格码','商家编码','美金原价','商品总重量','商品单重','商品需求总量','商品待采量','优采推荐'],
      isIndeterminate: true,
      cityOptions:['商品名称','商品品牌','商品规格码','商家编码','美金原价','商品总重量','商品单重','商品需求总量','商品待采量','优采推荐'],
      thIndex:[],
      //搜索
      querySn:'',
      startTime:'',
      endTime:'',
      //合期参数
      is_combine:'',
      is_caigoubu:'',
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
            // this.$message('至少要选择一个字段!');
            this.checkedCities=['商品名称','商品品牌','商品规格码','商家编码','美金原价','商品总重量','商品单重','商品需求总量','商品待采量','优采推荐'];
            return false;
        }
        this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
        let vm = this;
        let checkedCount = value.length;
        if(checkedCount==0){
            this.checkedCities=['商品名称','商品品牌','商品规格码','商家编码','美金原价','商品总重量','商品单重','商品需求总量','商品待采量','优采推荐'];
        }
        this.checkAll = checkedCount === this.cityOptions.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cityOptions.length;
    },
    getDataList(){
        let vm = this;
        vm.thItem.splice(0);
        vm.tdItem.splice(0);
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        vm.cityOptions.splice(0);
        vm.checkedCities.splice(0);
        vm.cityOptions=['商品名称','商品品牌','商品规格码','商家编码','美金原价','商品总重量','商品单重','商品需求总量','商品待采量','优采推荐'];
        vm.checkedCities=['商品名称','商品品牌','商品规格码','商家编码','美金原价','商品总重量','商品单重','商品需求总量','商品待采量','优采推荐'];
        axios.get(vm.url+vm.$purchaseChannelStatisticsListURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&start_time="+vm.startTime+"&end_time="+vm.endTime+"&is_combine="+vm.is_combine+"&query_sn="+vm.querySn+"&sum_date_cat=2",
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            load.close();
            // vm.is_combine='';
            if(res.data.data.total_goods_num>0){
                vm.tableData=res.data.data;
                vm.total=res.data.data.total_goods_num;
                vm.is_caigoubu=res.data.data.purchase_sum_date_list.status
                vm.purchaseSumDate=res.data.data.purchase_sum_date_list.info
                tableStyleByDataLength(vm.tableData.goods_basic_list.length,16);
                let thItem = ['目标采购数','实采数','目标金额','实际金额','目标重量','实际重量'];
                let tdItem = ['may_num','real_num','target_total_price','real_total_price','target_total_weight','real_total_weight']
                vm.tableData.purchase_channel_list.forEach(element => {
                    vm.cityOptions.forEach(item => {
                        if(element.channel_name != item){
                            vm.cityOptions.push(element.channel_name)
                            vm.checkedCities.push(element.channel_name)
                        }
                    })
                    vm.cityOptions=uniq(vm.cityOptions)
                    vm.checkedCities=uniq(vm.checkedCities)
                    thItem.forEach(thInfo=>{
                        vm.thItem.push({"thName":thInfo,"titleName":element.channel_name});
                    })
                });
                vm.tableData.goods_basic_list.forEach(element=>{
                    let tditem = [];
                    if(element.channel_info.length!=0){
                        element.channel_info.forEach(channelInfo=>{
                            tdItem.forEach(aa=>{
                                if(channelInfo.length==1){
                                    tditem.push({'tdName':'-','titleName':channelInfo});
                                }else{
                                    tditem.push({'tdName':channelInfo.channel_info[aa],'titleName':channelInfo.channel_name});
                                }
                            })
                        })
                    }else{
                        vm.tableData.purchase_channel_list.forEach(element => {
                            tdItem.forEach(aa=>{
                                tditem.push({'tdName':'-','titleName':element.channel_name});
                            })
                        });
                    }
                    vm.tdItem.push(tditem);
                })
                vm.isShow=false;
            }else{
                vm.tableData='';
                vm.total='';
                vm.isShow=true;
            }
        }).catch(function (error) {
            load.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    searchFrame(e){
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
        if(e=='1'){//如果为1则可合期
            vm.is_combine='1';
        }else if(e=='0'){//如果为0则为搜索
            vm.is_combine='';
        }
        vm.getDataList();
    },
    //根据时间搜索此时间段位内的数据
    retrieval(end_time,start_time){
        let vm = this;
        vm.startTime = start_time;
        vm.endTime = end_time;
        vm.getDataList();
    },
    //删除此时间段位内的数据
    deleteTheTime(id){
        let vm = this;
        axios.get(vm.url+vm.$delPurchaseSumDateURL+"?id="+id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=='1000'){
                vm.startTime = '',
                vm.endTime = '',
                vm.getDataList();
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
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    divWidth(){
        let vm=this;
        var recLength=[];
        if(vm.tableData!=''){
            vm.tableData.goods_basic_list.forEach(element=>{
                recLength.push(element.discount_info.length);
            })
        }
        var j=recLength[0];
        for(var i=0;i<=recLength.length;i++){
                if(recLength[i+1]>=j){
                    j=recLength[i+1]
                }
        }
        var discountLength=j*300;
        vm.discountLength=discountLength;
        return "width:"+discountLength+"px;text-align: left;"
    },
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
        let widthLength =(vm.checkedCities.length-aa)*200+aa*600;
        let judgeRate=vm.checkedCities.find(function(e){
                return e=='优采推荐';
        });
        if(judgeRate){
                widthLength=widthLength+vm.discountLength;
            }
        return "width:"+widthLength+"px"
    },
  }
}
</script>
<style>
/* .purchaseChannelStatisticsList .t_i{width:100%; overflow-x:auto; height:auto;border-right: 1px solid #ccc;border-left: 1px solid #ccc;} */
.purchaseChannelStatisticsList .t_i{width:100%; height:auto;}
.purchaseChannelStatisticsList .t_i_h{width:100%; overflow-x:hidden;}
.purchaseChannelStatisticsList .ee{width:100%!important; text-align: center;}
.purchaseChannelStatisticsList .t_i_h table{width:100%;}
.purchaseChannelStatisticsList .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.purchaseChannelStatisticsList .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.purchaseChannelStatisticsList .cc table{width:100%; }
.purchaseChannelStatisticsList .cc table td{height:25px; text-align:center}
.selectTitle .el-checkbox+.el-checkbox {
    margin-right: 30px;
    width: 120px;
    height: 40px;
    line-height: 40px;
}
.selectTitle .el-checkbox {
    margin-right: 30px;
    /* margin-left: 30px; */
    width: 120px;
    height: 40px;
    line-height: 40px;
}
.purchaseChannelStatisticsList .p_a{
    position: absolute;
    top: 0;
    right: 0;
}
.purchaseChannelStatisticsList .p_a_y{
    position: absolute;
    top: 140px;
    left: 24px;
}
.purchaseChannelStatisticsList .spanWidth{
    width: 300px;
    display: inline-block;
}
.purchaseChannelStatisticsList .channel{
    width: 190px;
    display: inline-block;
    text-align: left;
}
.purchaseChannelStatisticsList .Serial{
    display: inline-block;
    border-radius: 50%;
    background-color: #00C1DE;
    color: #fff;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>
