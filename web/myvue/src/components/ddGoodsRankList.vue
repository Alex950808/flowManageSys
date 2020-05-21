<template>
  <div class="ddGoodsRankList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span class="bgButton MR_twenty" @click="backToUpPage()">返回上一页</span>
                        <span><span class="coarseLine MR_ten"></span>DD单品排行</span>
                        <span>
                            <el-input v-model="spec_sn" style="width:190px;" placeholder="请输入商品规格码"></el-input>
                            <el-date-picker style="width:190px;" v-model="start_date" type="date" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                            <el-date-picker style="width:190px;" v-model="end_date" type="date" value-format="yyyy-MM-dd" placeholder="请选择结束时间"></el-date-picker>
                            <span class="bgButton ML_twenty" @click="searchDate()">&nbsp;&nbsp;&nbsp;搜索&nbsp;&nbsp;&nbsp;</span>
                        </span>
                        <!-- <searchBox @searchFrame='searchFrame'></searchBox> -->
                        <span class="floatRight MR_twenty F_S_Sixteen">
                            销售用户搜索：<template>
                                <el-select v-model="userId" @change="idChange()" clearable placeholder="请选择">
                                    <el-option v-for="item in options" :key="item.id" :label="item.user_name" :value="item.id"></el-option>
                                </el-select>
                            </template>
                        </span>
                    </div>
                    <table class="MB_Thirty" style="width:100%;text-align: center;line-height: 35px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="widthThreeHundred">商品名称</th>
                                <th class="w_250">商品规格码</th>
                                <th class="w_250">商家编码</th>
                                <th class="widthTwoHundred">平台条码</th>
                                <th class="widthOneFiveHundred">美金原价</th>
                                <th class="widthOneFiveHundred">
                                    <span>订单数</span>
                                    <span class="d_I_B" style="vertical-align: -9px;line-height: 20px;">
                                        <i class="el-icon-caret-top  Cursor" @click="clickSort('order_num','asc')" title="升序排列"></i><br/>
                                        <i class="el-icon-caret-bottom  Cursor" @click="clickSort('order_num','desc')" title="降序排列"></i> 
                                    </span>
                                </th>
                                <th class="widthOneFiveHundred">
                                    <span>sku数</span>
                                    <span class="d_I_B Cursor" style="vertical-align: -9px;line-height: 20px;">
                                        <i class="el-icon-caret-top  Cursor" @click="clickSort('sku_num','asc')" title="升序排列"></i><br/>
                                        <i class="el-icon-caret-bottom  Cursor redFont" @click="clickSort('sku_num','desc')" title="降序排列"></i> 
                                    </span>
                                </th>
                                <th class="widthOneFiveHundred">实际分货数</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData">
                                <td class="widthThreeHundred overOneLinesHeid">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td class="w_250">{{item.spec_sn}}</td>
                                <td class="w_250">{{item.erp_merchant_no}}</td>
                                <td class="widthTwoHundred overOneLinesHeid">
                                    <el-tooltip class="item" effect="light" :content="item.platform_barcode" placement="right">
                                        <span style="-webkit-box-orient: vertical;" class="widthTwoHundred">{{item.platform_barcode}}</span>
                                    </el-tooltip>
                                </td>
                                <td class="widthOneFiveHundred">{{item.spec_price}}</td>
                                <td class="widthOneFiveHundred">{{item.order_num}}</td>
                                <td class="widthOneFiveHundred">{{item.sku_num}}</td>
                                <td class="widthOneFiveHundred">{{item.sortNum}}</td>
                            </tr>
                        </tbody>
                    </table>
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
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      options:[],
      userId:'',
      orderField:'',
      orderType:'',
      start_date:'',
      end_date:'',
      spec_sn:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.getSaleUser();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.post(vm.url+vm.$ddGoodsRankListURL,
            {
                "sale_user_id":vm.userId,
                "page":vm.page,
                "pageSize":vm.pagesize,
                "spec_sn":vm.spec_sn, 
                "orderField":vm.orderField,
                "orderType":vm.orderType,
                "start_time":vm.start_date,
                "end_date":vm.end_date,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.goodsRankList.data.length!=0){
                vm.tableData=res.data.goodsRankList.data;
                // vm.userId=""+res.data.goodsRankList.data[0].sale_user_id+""
                vm.total=res.data.goodsRankList.total;
                vm.isShow=false;
            }else if(res.data.goodsRankList.data.length==0){
                vm.tableData=res.data.goodsRankList.data;
                vm.total=res.data.goodsRankList.total;
                vm.isShow=true;
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
    getSaleUser(){//
        let vm = this;
        axios.get(vm.url+vm.$getSaleUserURL,
            {
              headers:vm.headersStr,  
            }
        ).then(function(res){
            vm.options=res.data.data.saleUser;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    idChange(){
        let vm = this;
        vm.getDataList();
    },
    clickSort(fie,typ){
        let vm = this;
        vm.orderField=fie;
        vm.orderType=typ;
        $('.el-icon-caret-top').removeClass('redFont');
        $('.el-icon-caret-bottom').removeClass('redFont');
        $(event.target).addClass('redFont');
        $(event.target).siblings().removeClass('redFont')
        $(".el-pager li").eq(0).siblings().removeClass("active")
        $(".el-pager li").eq(0).addClass("active")
        vm.page=1;
        vm.getDataList();
    },
    //分页 
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        // $(".el-pager li").eq(vm.page-1).removeClass("active")
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDataList()
    },
    backToUpPage(){
        let vm = this;
        vm.$router.push('/indexPage');
    },
    searchDate(){
        let vm = this;
        if(vm.start_date==null){
            vm.start_date='';
        }
        if(vm.end_date==null){
            vm.end_date='';
        }
        vm.getDataList();
    }
  }
}
</script>

<style scoped>
@import '../css/publicCss.css';
</style>
<!---->
