<template>
  <div class="orderCurrentStatisticsList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="d_I_B lineHeightThirty w_ratio fontCenter MT_twenty">
                        <span class="d_I_B w_33_P">
                            <i class="NotStyleForI F_S_T">YD订单数</i><br>
                            <i class="NotStyleForI F_S_twenty fontWeight notBgButton" @click="searchOrder('1')">{{titleInfo.mos_yd_num}}</i>
                        </span>
                        <span class="d_I_B w_33_P">
                            <i class="NotStyleForI F_S_T">BD订单数</i><br>
                            <i class="NotStyleForI F_S_twenty fontWeight notBgButton" @click="searchOrder('2')">{{titleInfo.mos_bd_num}}</i>
                        </span>
                        <span class="d_I_B w_33_P">
                            <i class="NotStyleForI F_S_T">DD订单数</i><br>
                            <i class="NotStyleForI F_S_twenty fontWeight notBgButton" @click="searchOrder('3')">{{titleInfo.mos_dd_num}}</i>
                        </span>
                    </div>
                    <div class="listTitleStyle" style="margin-top: 0px !important;">
                        <span class="coarseLine MR_ten"></span>
                        订单统计列表(实时)
                    </div>
                    <el-row class="d_I_B F_Z_N ML_twenty MB_twenty">
                        <span>单号 ：</span>
                        <span><el-input style="width:200px;" v-model="subOrderSn" placeholder="请输入内容"></el-input></span>
                        <span class="ML_ten">开始时间 ：</span>
                        <template>
                            <el-date-picker v-model="startTime" type="date" placeholder="选择日期"></el-date-picker>
                        </template>
                        <span class="ML_ten">结束时间 ：</span>
                        <template>
                            <el-date-picker v-model="endTime" type="date" placeholder="选择日期"></el-date-picker>
                        </template>
                        <span class="ML_ten">客户 ：</span>
                        <template>
                            <el-select v-model="sale_user_id" clearable placeholder="请选择">
                                <el-option v-for="item in sale_user_info" :key="item.sale_user_id" :label="item.user_name" :value="item.sale_user_id">
                                </el-option>
                            </el-select>
                        </template>
                        <span class="bgButton" @click="searchFrame('0')">搜索</span>
                        <span class="bgButton" @click="clearData()">清空选项</span>
                        <!-- <span class="bgButton" v-if="is_caigoubu==1" @click="searchFrame('1')">合期</span> -->
                    </el-row>
                    <el-row class="MB_twenty">
                        <el-col :span="16">
                            <div class="d_I_B F_Z_N ML_twenty">
                                <span class="bgButton PR MR_twenty heqi" style="margin-bottom: 10px;" v-for="item in purchaseSumDate">
                                    <span @click="retrieval(item.sum_demand_sn)">{{item.sum_demand_name}}</span>
                                    <!-- <i v-if="is_caigoubu==1" @click="deleteTheTime(item.id)" class="el-icon-error p_a whiteFont Cursor"></i> -->
                                </span>
                            </div>
                        </el-col>
                        <el-col :span="3" class="fontCenter">
                            <span v-if="isNotClick" class="bgButton" @click="downLoadDailyInfo()">下载合期</span>
                        </el-col>
                        <el-col :span="5" class="fontRight">
                            统计基数：
                            <span>
                                <span class="d_I_B bgButton B_B_G whiteFont" @click="integralSpan('1')">实采</span>
                                <template>
                                    <el-popover  placement="top-start" width="200" trigger="hover"
                                        content="实采：按照采购期实采数据统计">
                                        <i class="el-icon-question" slot="reference"></i>
                                    </el-popover>
                                </template>
                            </span>
                            <span>
                                <span class="d_I_B bgButton G_B_G whiteFont" @click="integralSpan('2')">分货</span>
                                <template>
                                    <el-popover  placement="top-start" width="200" trigger="hover"
                                        content="分货：按照订单分货量统计">
                                        <i class="el-icon-question" slot="reference"></i>
                                    </el-popover>
                                </template>
                            </span>
                        </el-col>
                    </el-row>
                    <el-row class="lineHeightForty fontWeight MB_ten border B_R fontLift">
                        <div class="lineHeightForty">
                            <el-col :span="6"><span class="ML_twenty"><i class="NotStyleForI F_S_Sixteen">采购缺口总额(美金)</i> ：<i class="NotStyleForI F_S_twenty">{{titleInfo.purchase_diff_total_price}}</i></span></el-col>
                            <el-col :span="6"><span><i class="NotStyleForI F_S_Sixteen">销售缺口总额(美金)</i> ：<i class="NotStyleForI F_S_twenty">{{titleInfo.sell_diff_total_price}}</i></span></el-col>
                            <el-col :span="4"><span><i class="NotStyleForI F_S_Sixteen">平均报价折扣</i> ：<i class="NotStyleForI F_S_twenty">{{titleInfo.avg_sale_discount}}</i></span></el-col>
                            <el-col :span="4"><span><i class="NotStyleForI F_S_Sixteen">报价逻辑毛利</i> ：<i class="NotStyleForI F_S_twenty">{{titleInfo.sub_quote_rate}}%</i></span></el-col>
                            <el-col :span="4"><span><i class="NotStyleForI F_S_Sixteen">实采毛利</i> ：<i class="NotStyleForI F_S_twenty">{{titleInfo.sub_profit_rate}}%</i></span></el-col>
                        </div>
                    </el-row>
                    <div>  
                    </div>
                    <div class="border B_R MB_twenty PR" v-for="(item,index) in tableData"> 
                        <el-row class="Lavender">
                            <el-col :span="7">
                                <span class="F_S_twenty fontWeight ML_twenty lineHeightThirty">
                                    {{item.user_name+"-"+item.sale_user_account+"-"+item.entrust_time}}
                                    <template>
                                        <el-popover  placement="top-start" width="200" trigger="hover"
                                            content="用户-分组-交付时间">
                                            <i class="el-icon-question" slot="reference"></i>
                                        </el-popover>
                                    </template>
                                </span><br>
                                <span class="F_S_Sixteen ML_twenty lineHeightThirty">系统子单号：<i class="NotStyleForI">{{item.sub_order_sn}}</i></span><br>
                                <span class="F_S_Sixteen ML_twenty lineHeightThirty">客户订单号：<i class="NotStyleForI">{{item.external_sn}}</i></span><br>
                                <span class="F_S_Sixteen ML_twenty lineHeightThirty">子单备注：<i class="NotStyleForI">{{item.remark}}</i></span>
                            </el-col>
                            <el-col :span="14" class="fontCenter MT_Thirty"> 
                                <div class="d_I_B lineHeightThirty w_t_ratio">
                                    <i class="NotStyleForI F_Z_N">总需求数</i><br>
                                    <i class="NotStyleForI F_S_twenty fontWeight">{{item.mis_order_sub_total_num}}</i>
                                </div>
                                <span class="verticalBarT"></span>
                                <div class="d_I_B lineHeightThirty w_50_ratio">
                                    <span class="d_I_B w_33_P">
                                        <i class="NotStyleForI F_Z_N">现货数量</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.spot_num}}</i>
                                    </span>
                                    <span class="d_I_B w_33_P">
                                        <i class="NotStyleForI F_Z_N">需求单需求数量</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.demand_goods_num}}</i>
                                    </span>
                                </div>
                                <span class="verticalBarT"></span>
                                <div class="d_I_B lineHeightThirty w_33_P">
                                    <span class="d_I_B w_33_P">
                                        <i class="NotStyleForI F_Z_N">
                                            满足数
                                            <template>
                                                <el-popover  placement="top-start" width="200" trigger="hover"
                                                    content="满足数=现货数量+实采数量">
                                                    <i class="el-icon-question" slot="reference"></i>
                                                </el-popover>
                                            </template>
                                        </i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.total_real_buy_num}}</i>
                                    </span>
                                    <span class="d_I_B w_33_P">
                                        <i class="NotStyleForI F_Z_N">实际采购数</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.real_buy_num}}</i>
                                    </span>
                                </div>
                            </el-col>
                            <el-col :span="3" class="fontRight MT_Thirty">
                                <span @click="viewDetails(item.sub_order_sn,index)" class="notBgButton">展开<i class="el-icon-arrow-down" title="点击展示更多"></i></span>
                            </el-col>
                        </el-row>
                        <el-row class="fontCenter Lavender">
                            <el-col :span="5">
                                <span class="lineHeightThirty"><i class="NotStyleForI F_Z_N">SKU</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight d_I_B widthOneHundred">{{item.sku_num}}</i></span><br>
                                <span class="lineHeightThirty"><i class="NotStyleForI F_Z_N">待采</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight d_I_B widthOneHundred">{{item.wait_buy_num}}</i></span><br>
                                <span class="lineHeightThirty"><i class="NotStyleForI F_Z_N">溢采</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight d_I_B widthOneHundred">{{item.overflow_num}}</i></span>
                            </el-col>
                            <el-col :span="1">
                                <span class="verticalBar"></span>
                            </el-col>
                            <el-col :span="5">
                                <span class="lineHeightThirty"><i class="NotStyleForI F_Z_N">采购缺口总额($)</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight d_I_B widthOneHundred">{{item.sub_purchase_diff_total_price}}</i></span><br>
                                <span class="lineHeightThirty"><i class="NotStyleForI F_Z_N">采购需求总额($)</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight d_I_B widthOneHundred">{{item.sub_purchase_total_price}}</i></span><br>
                                <span class="lineHeightThirty"><i class="NotStyleForI F_Z_N">销售缺口总额($)</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight d_I_B widthOneHundred">{{item.sdt_price}}</i></span>
                            </el-col>
                            <el-col :span="1">
                                <span class="verticalBar"></span>
                            </el-col>
                            <el-col :span="5">
                                <span class="lineHeightThirty">
                                    <i class="NotStyleForI F_Z_N">目标逻辑毛利</i>
                                    <i class="NotStyleForI ML_twenty F_S_twenty fontWeight d_I_B widthOneFiveHundred">{{item.target_rate}}%</i>
                                </span><br>
                                <span class="lineHeightThirty">
                                    <i class="NotStyleForI F_Z_N">报价逻辑毛利</i>
                                    <i v-if="item.sub_quote_rate>item.target_rate" class="NotStyleForI blueFont ML_twenty F_S_twenty fontWeight d_I_B widthOneFiveHundred ">{{item.sub_quote_rate}}%
                                        <i class="iconfont">&#xe6b4;</i>
                                    </i>
                                    <i v-if="item.sub_quote_rate<item.target_rate" class="NotStyleForI redFont ML_twenty F_S_twenty fontWeight d_I_B widthOneFiveHundred ">{{item.sub_quote_rate}}%
                                        <i class="iconfont">&#xe644;</i>
                                    </i>
                                </span><br>
                                <span class="lineHeightThirty">
                                    <i class="NotStyleForI F_Z_N">实采毛利</i>
                                    <i v-if="item.sub_profit_rate>item.target_rate" class="NotStyleForI blueFont ML_twenty F_S_twenty fontWeight d_I_B widthOneFiveHundred">{{item.sub_profit_rate}}%
                                        <i class="iconfont">&#xe6b4;</i>
                                    </i>
                                    <i v-if="item.sub_profit_rate<item.target_rate" class="NotStyleForI redFont ML_twenty F_S_twenty fontWeight d_I_B widthOneFiveHundred">{{item.sub_profit_rate}}%
                                        <i class="iconfont">&#xe644;</i>
                                    </i>
                                </span>
                            </el-col>
                            <el-col :span="1">
                                <span class="verticalBar"></span>
                            </el-col>
                            <el-col :span="6">
                                <span class="lineHeightForty"><i class="NotStyleForI F_Z_N">采购满足率</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight">{{item.sub_real_rate }}%</i></span><br>
                                <span class="lineHeightForty"><i class="NotStyleForI F_Z_N">整单满足率</i><i class="NotStyleForI ML_twenty F_S_twenty fontWeight">{{item.sub_total_real_rate}}%</i></span>
                            </el-col>
                        </el-row>
                        <div class="O_F_A w_ratio PR" v-if="index==indexnum">
                            <span class="bgButton PA_L" @click="selectcol()">隐</span>
                            <table class="fontCenter tabletitle" :style="tableWidth">
                                <tr>
                                    <td v-for="item in tabletitle" rowspan="2" v-if="selectTitle(item)">{{item}}</td>
                                    <td width="180px;" v-for="item in tableDataTwo.batch_info_arr" v-if="selectTitle(item)">{{item}}</td>
                                </tr>
                                <tr>
                                    <td v-for="item in tableDataTwo.batch_info_arr" v-if="selectTitle(item)">
                                        <span style="width:80px;display: inline-block;">手动分配值</span>
                                        <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                                        <span style="width:80px;display: inline-block;">渠道折扣</span>
                                    </td>
                                </tr>
                                <tr v-for="orderInfo in tableDataTwo.sub_order_goods_list">
                                    <td class="overOneLinesHeid" v-if="selectTitle('商品名称')" style="width:200px;line-height: 40px;">
                                        <el-tooltip class="item" effect="light" :content="orderInfo.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">{{orderInfo.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td width="150px;" v-if="selectTitle('商品规格码')">{{orderInfo.spec_sn}}</td>
                                    <td width="150px;" v-if="selectTitle('商家编码')">{{orderInfo.erp_merchant_no}}</td>
                                    <td width="100px;" v-if="selectTitle('美金原价')">{{orderInfo.spec_price}}</td>
                                    <td width="100px;" v-if="selectTitle('商品重量')">{{orderInfo.spec_weight}}</td>
                                    <td width="100px;" v-if="selectTitle('总需求量')">{{orderInfo.goods_number}}</td>
                                    <td width="80px;" v-if="selectTitle('现货数量')">{{orderInfo.spot_num}}</td>
                                    <td width="80px;" v-if="selectTitle('需求量')">{{orderInfo.wait_buy_num}}</td>
                                    <td width="80px;" v-if="selectTitle('待采量')">{{orderInfo.diff_num}}</td>
                                    <td width="80px;" v-if="selectTitle('分货数量')">{{orderInfo.total_sort_num}}</td>

                                    <td width="100px;" v-if="selectTitle('报价exw折扣')">{{orderInfo.cost_exw_discount}}</td>

                                    <td width="100px;" v-if="selectTitle('报价折扣')">{{orderInfo.dd_sale_discount}}</td>
                                    <td width="100px;" v-if="selectTitle('实采exw折扣')">{{orderInfo.purchase_exw_discount}}</td>

                                    <td width="100px;" v-if="selectTitle('重价率')">{{orderInfo.weight_rate}}</td>
                                    <td width="100px;" v-if="selectTitle('报价逻辑毛利')">{{orderInfo.sub_quote_rate}}%</td>
                                    <td width="100px;" v-if="selectTitle('实际逻辑毛利')">{{orderInfo.sub_profit_rate}}%</td>
                                    <td width="180px" v-for="item in tableDataTwo.batch_info_arr" v-if="selectTitle(item)">
                                        <span style="width:80px;display: inline-block;" v-if="orderInfo.batch_info[item]!=undefined">{{orderInfo.batch_info[item].handle_num}}</span>
                                        <span style="width:80px;display: inline-block;" v-if="orderInfo.batch_info[item]==undefined">{{'-'}}</span>
                                        <span v-if="orderInfo.batch_info[item]!=undefined">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                                        <span style="width:80px;display: inline-block;" v-if="orderInfo.batch_info[item]!=undefined">{{orderInfo.batch_info[item].channel_discount}}</span>
                                        <span style="width:80px;display: inline-block;" v-if="orderInfo.batch_info[item]==undefined">{{'-'}}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <selectCol :selectStr="selectStr" @selectTitle="selectTitle"></selectCol>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableWidth } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import selectCol from '@/components/UiAssemblyList/selectCol'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      selectCol,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      tableDataTwo:[],
      tableTitle:['商品名称','商品规格码','商家编码','美金原价','商品重量','总需求量','现货数量','需求量','待采量','分货数量','报价exw折扣','报价折扣','实采exw折扣','重价率','报价逻辑毛利','实际逻辑毛利'],
      tabletitle:[],
      titleInfo:[],
      purchaseSumDate:'',
      isShow:false,
      startTime:'',
      endTime:'',
      subOrderSn:'',
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      indexnum:-1,
      //合期参数 
      is_combine:'',
      is_caigoubu:'',
      //选择列
      selectStr:['商品名称','商品规格码','商家编码','美金原价','商品重量','总需求量','现货数量','需求量','待采量','分货数量','报价exw折扣','报价折扣','实采exw折扣','重价率','报价逻辑毛利','实际逻辑毛利'],
      //搜索订单
      orderType:'',
      sale_user_info:'',
      sale_user_id:'',
      //需求单商品批次类型 
      batch_type:'1',
      sumDemandSn:'',
      //下载合期
      isNotClick:false,
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$orderCurrentStatisticsListURL+"?start_time="+vm.startTime+"&end_time="+vm.endTime+"&query_sn="+vm.subOrderSn+
            "&is_combine="+vm.is_combine+"&sale_user_id="+vm.sale_user_id+"&order_type="+vm.orderType+"&page="+vm.page+"&page_size="+vm.pagesize+
            "&demand_sort_status=1"+"&sum_date_cat=1"+"&batch_type="+vm.batch_type+"&sum_demand_sn="+vm.sumDemandSn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            vm.is_combine='';
            if(res.data.code=='1000'){
                vm.tableData=res.data.data.demand_list_info.data;
                vm.titleInfo=res.data.data.demand_list_info.title_info
                vm.total=res.data.data.demand_list_info.total;
                vm.is_caigoubu=res.data.data.purchase_sum_date.status
                vm.purchaseSumDate=res.data.data.purchase_sum_date
                vm.sale_user_info=res.data.data.sale_user_info
                vm.isShow=false;
            }else if(res.data.code=='1003'){
                vm.tableData=[];
                vm.titleInfo=[];
                vm.isShow=true;
                // vm.$message(res.data.msg);
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            load.close();
            vm.is_combine='';
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
        }
        vm.tableDataTwo=[];
        vm.tabletitle=[];
        $(".tabletitle").removeClass("lineHeightForty");
        vm.getDataList();
    },
    //搜索订单数
    searchOrder(orderType){
        let vm = this;
        vm.orderType=orderType;
        vm.getDataList();
    },
    //根据时间搜索此时间段位内的数据
    retrieval(sum_demand_sn){
        let vm = this;
        vm.sumDemandSn=sum_demand_sn;
        if(vm.sumDemandSn!=''){
            vm.isNotClick=true;
        }
        vm.getDataList();
        $(event.target).parent().addClass('grBgButton').removeClass('bgButton')
        $(event.target).parent().siblings().addClass('bgButton').removeClass('grBgButton')
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
    //查看详情
    viewDetails(sub_order_sn,index){
        let vm = this;
        vm.indexnum=index;
        vm.tableDataTwo=[];
        vm.tabletitle=[];
        vm.selectStr.splice(17);
        let content_text_height=$(event.target).parent().parent().parent().height();
        if(content_text_height<=235){
            let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
            axios.get(vm.url+vm.$subOrderStatisticsListURL+"?sub_order_sn="+sub_order_sn+"&batch_type="+vm.batch_type,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                load.close();
                if(res.data.code=='1000'){
                    vm.tableDataTwo=res.data.data;
                    vm.tabletitle=vm.tableTitle;
                    $(".tabletitle").addClass("lineHeightForty");
                    if(res.data.data.batch_info_arr.length>0){
                        res.data.data.batch_info_arr.forEach(element => {
                            vm.selectStr.push(element)
                        });
                    }
                    vm.selectStr=uniq(vm.selectStr)
                    vm.$store.commit('selectList', vm.selectStr);
                    // sessionStorage.setItem("tableData",JSON.stringify(res.data.data));
                    // vm.$router.push('/subOrderStatisticsList?sub_order_sn='+sub_order_sn+"&status=2");
                }else{
                    vm.$message(res.data.msg)
                }
            }).catch(function (error) {
                load.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        }else{
            vm.tableDataTwo=[];
            vm.tabletitle=[];
            $(".tabletitle").removeClass("lineHeightForty");
        }
        
    },
    // tableWidth(){
    //     let vm = this;
    //     if(vm.tableDataTwo.batch_info_arr!=undefined){
    //         if(vm.tableDataTwo.batch_info_arr.length<1){
    //             return "width:100%";
    //         }else{
    //             let wi = 1500+vm.tableDataTwo.batch_info_arr.length*100;
    //             return "width:"+wi+"px";
    //         }
    //     }else{
    //         return "width:100%";
    //     }
        
    // },
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.tableDataTwo=[];
        vm.tabletitle=[];
        $(".tabletitle").removeClass("lineHeightForty");
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.tableDataTwo=[];
        vm.tabletitle=[];
        $(".tabletitle").removeClass("lineHeightForty");
        vm.getDataList()
    },
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    integralSpan(e){
        let vm = this;
        vm.batch_type=e;
        $(event.target).addClass("B_B_G");
        $(event.target).removeClass ("G_B_G");
        $(event.target).parent().siblings().find('span').eq(0).addClass("G_B_G")
        $(event.target).parent().siblings().find('span').eq(0).removeClass("B_B_G")
        vm.getDataList();
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    //下载合期
    downLoadDailyInfo(){
        let vm = this;
        // let headersToken=sessionStorage.getItem("token");
        // window.open(vm.url+vm.$downLoadDailyInfoURL+'?sum_demand_sn='+vm.sumDemandSn+'&token='+headersToken);
        let active=$(event.target);
        let tableName = "合单统计表_"+vm.sumDemandSn;
        vm.isPortShow=false;
        axios.post(vm.url+vm.$downLoadDailyInfoURL,
            {
                "sum_demand_sn":vm.sumDemandSn,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            if(res.data.code){
                vm.$message(res.data.msg)
            }else{
                exportsa(vm.url+vm.$downLoadDailyInfoURL+"?sum_demand_sn="+vm.sumDemandSn,tableName)
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //清空选项
    clearData(){
        let vm = this;
        vm.startTime='';
        vm.endTime='';
        vm.subOrderSn='';
        vm.sale_user_id='';
        vm.sumDemandSn='';
        vm.isNotClick=false;
        $('.heqi').addClass('bgButton').removeClass('grBgButton')
        vm.getDataList();
    }
  },
  computed:{
    selectTitle(checkedCities){
       return selectTitle(checkedCities);
    },
    //表格宽度计算
    tableWidth(){
        // return tableWidth(this.$store.state.select);
        var widthLength=this.$store.state.select.length*200;
        return "width:"+widthLength+"px";
    },
  }
}
</script>

<style>
.orderCurrentStatisticsList .t_i{width:100%; height:auto;}
.orderCurrentStatisticsList .t_i_h{width:100%; overflow-x:hidden;}
.orderCurrentStatisticsList .ee{width:100%!important; text-align: center;}
.orderCurrentStatisticsList .t_i_h table{width:100%;}
.orderCurrentStatisticsList .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.orderCurrentStatisticsList .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.orderCurrentStatisticsList .cc table{width:100%; }
.orderCurrentStatisticsList .cc table td{height:25px; text-align:center}
.orderCurrentStatisticsList .p_a{
    position: absolute;
    top: 0;
    right: 0;
}
.orderCurrentStatisticsList .l_p_a{
    position: absolute;
    top: 10px;
    right: 50px;
    width: 180px;
}
.verticalBar{
    display: inline-block;
    width:2px;
    height: 60px;
    margin-top: 20px;
    vertical-align: -3px;
    background: #eef0f4;
}
.verticalBarT{
    display: inline-block;
    width:2px;
    height: 40px;
    /* margin-top: 10px; */
    /* vertical-align: -3px; */
    background: #eef0f4;
}
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
