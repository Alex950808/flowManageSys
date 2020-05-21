<template>
  <div class="goodsStatisticsList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="listTitleStyle">
                        <el-col :span="5" class="ML_twenty">
                            <span class="coarseLine MR_ten"></span>商品统计列表
                        </el-col>
                        <el-col :span="14" class="ML_twenty fontLift">
                            <span>
                                开始时间：
                                <span class="block">
                                    <el-date-picker v-model="startTime" type="date" placeholder="选择日期">
                                    </el-date-picker>
                                </span>
                            </span>
                            <span>
                                截止时间：
                                <span class="block">
                                    <el-date-picker v-model="endTime" type="date" placeholder="选择日期">
                                    </el-date-picker>
                                </span>
                            </span>
                        <span class="bgButton" @click="dataSearch()">搜索</span>
                        </el-col>
                    </el-row>
                    <el-row class="t_i" v-if="isConter">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <thead>
                                        <tr>
                                            <td :style="labelWidth()">商品标签</td>
                                            <td width="250px;">商品名称</td>
                                            <!-- <td width="200px;">子单单号</td> -->
                                            <td width="150px;">商品规格码</td>
                                            <td width="150px;">商家编码</td>
                                            <td width="100px;">需求量</td>
                                            <td width="100px;">美金原价</td>
                                            <td width="100px;">预判采购量</td>
                                            <td width="100px;">待采量</td>
                                            <td width="100px;">现货数量</td>
                                            <td width="100px;">已采量</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc"  @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                <tbody>
                                    <tr v-for="goodsInfo in tableData">
                                        <td :style="labelWidth()">
                                            <span v-for="labelInfo in goodsInfo.goods_label_list" class="PR_ten">
                                                <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                            </span>
                                        </td>
                                        <td class="overOneLinesHeid" style="width:250px;">
                                            <el-tooltip class="item" effect="light" :content="goodsInfo.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;width:250px;">{{goodsInfo.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <!-- <td width="200px;">{{goodsInfo.sub_order_sn}}</td> -->
                                        <td width="150px;">{{goodsInfo.spec_sn}}</td>
                                        <td width="150px;">{{goodsInfo.erp_merchant_no}}</td>
                                        <td width="100px;">{{goodsInfo.goods_number}}</td>
                                        <td width="100px;">{{goodsInfo.spec_price}}</td>
                                        <td width="100px;">{{goodsInfo.sub_wait_buy_num}}</td>
                                        <td width="100px;">{{goodsInfo.wait_num}}</td>
                                        <td width="100px;">{{goodsInfo.spot_goods_num}}</td>
                                        <td width="100px;">
                                            <el-tooltip placement="top">
                                                <div slot="content">
                                                    需求单号:<br/><span v-for="item in goodsInfo.demand_sn_info">{{item}}<br/></span>
                                                    采购期单号:<br/><span v-for="item in goodsInfo.purchase_sn_info">{{item}}<br/></span>
                                                </div>
                                                <el-button style="width:100px;">{{goodsInfo.alredy_buy_num}}</el-button>
                                            </el-tooltip>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </el-row>
                    <el-row>
                        <notFound v-if="isShow"></notFound>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import { someDataLongByJson } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { dateToStr } from '@/filters/publicMethods.js'
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
      isShow:false,
      isConter:true,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      //时间选择
      startTime:'',
      endTime:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        if(vm.startTime==null||vm.endTime==null){
           vm.startTime=''; 
           vm.endTime='';
        }
        axios.get(vm.url+vm.$goodsStatisticsListURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&start_time="+vm.startTime+"&end_time="+vm.endTime,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            var myDate = new Date();
            dateToStr(myDate)
            let dataStr = dateToStr(myDate).split('-')
            vm.endTime = dateToStr(myDate);
            vm.startTime = ''+dataStr[0]+'-'+dataStr[1]+'-'+'01';
            if(res.data.code==1000){
                vm.tableData=res.data.data.sub_goods_list;
                vm.total=res.data.data.total_goods_num;
                tableStyleByDataLength(vm.tableData.length,15);
                vm.isShow=false;
                vm.isConter=true;
            }else if(res.data.code=='1002'){
                vm.isShow=true;
                vm.isConter=false;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            vm.loading.close();
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
    labelStyle(color){
        return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
    },
    labelWidth(){
        let vm = this;
        let width=someDataLongByJson(vm.tableData,'goods_label_list')*60;
        return "width:"+width+"px";
    },
    tableWidth(){
        let vm = this;
        let width = someDataLongByJson(vm.tableData,'goods_label_list')*60+1300;
        let pagewWidth = $(".listTitleStyle").width();
        if(width<pagewWidth){
            width=pagewWidth;
        }
        return "width:"+width+"px";
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    dataSearch(){
        let vm = this;
        vm.getDataList();
    }
  }
}
</script>
<style>
.goodsStatisticsList .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.goodsStatisticsList .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.goodsStatisticsList .ee{width:100%!important; width:100%; text-align: center;}
.goodsStatisticsList .t_i_h table{width:1630px;}
.goodsStatisticsList .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.goodsStatisticsList .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.goodsStatisticsList .cc table{width:1630px; }
.goodsStatisticsList .cc table td{ text-align:center}
</style>

<style scoped lang=less>
table{
    border: 1px solid #ebeef5;
    tr{
        line-height: 40px;
        th,td{
            border-bottom: 1px solid #ebeef5;
            border-left: 1px solid #ebeef5;
        }
    }
    tr:nth-child(even){
        background:#fafafa;
    }
    tr:hover{
        background:#ced7e6;
    }
}
</style>
