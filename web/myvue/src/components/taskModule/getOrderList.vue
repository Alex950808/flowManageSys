<template>
  <div class="getOrderList batchSetting_b">
      <el-col :span="24" style="background-color: #fff;">
            <div class="batchSetting bgDiv">
                <el-col :span="22" :offset="1">
                    <el-row class="select">
                        <el-col :span="3"><div class="title"><span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;订单列表</span></div></el-col>
                        <el-col :span="3"><div class="title"><el-input v-model="spec_sn" placeholder="请输入规格码"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div class="title"><el-input v-model="shop_no" placeholder="请输入店铺id"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div class="title"><el-input v-model="trade_no" placeholder="请输入erp订单号"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div class="title"><el-input v-model="order_status" placeholder="请输入系统订单状态"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div class="title"><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                        <el-col :span="3" :offset="3"><div><el-input v-model="trade_status" placeholder="请输入erp订单状态"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div><el-input v-model="receiver_name" placeholder="请输入收货人"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div><el-input v-model="receiver_mobile" placeholder="请输入收货人手机"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                        <el-col :span="3" :offset="1"><div><span class="bgButton" @click="searchFrame()">点击搜索</span></div></el-col>
                    </el-row>
                    <table class="tableTitle">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="10%">商品规格码</th>
                            <th width="10%">商家编码</th>
                            <th width="10%">商品数量</th>
                            <th width="10%">成交单价</th>
                            <th width="10%">商品单价</th>
                            <th width="10%">查看详情</th>
                        </thead>
                    </table>
                    <div class="content_text" v-for="item in BatchData">
                        <el-row class="userInformation">
                            <el-col :span="5"><div class="">收货人姓名:{{item.receiver_name}}</div></el-col>
                            <el-col :span="5"><div class="">收货人电话:{{item.receiver_mobile}}</div></el-col>
                            <el-col :span="5"><div class="">店铺名称:{{item.shop_name}}</div></el-col>
                            <el-col :span="5"><div class="">订单状态:{{item.order_status}}</div></el-col>
                            <el-col :span="4"><div class="">erp订单号:{{item.trade_no}}</div></el-col>
                        </el-row>
                        <el-row class="userInformation">
                            <el-col :offset="5" :span="5"><div class="">erp状态:{{item.trade_status}}</div></el-col>
                            <el-col :span="5"><div class="">下单时间:{{item.trade_time}}</div></el-col>
                            <el-col :span="5"><div class="">购买时间:{{item.pay_time}}</div></el-col>
                        </el-row>
                        <table class="table-two" style="width:100%;text-align: center">
                            <tr v-for="good in item.goods_data">
                                <td width="20%" class="overOneLinesHeid">
                                    <el-tooltip class="item" effect="light" :content="good.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{good.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td width="10%">{{good.spec_sn}}</td>
                                <td width="10%">{{good.erp_merchant_no}}</td>
                                <td width="10%">{{good.num}}</td>
                                <td width="10%">{{good.order_price}}</td>
                                <td width="10%">{{good.price}}</td>
                                <td width="10%"><i class="iconfont viewDetails" @click="viewDetails(item.trade_no)" title="查看详情">&#xe631;</i></td>
                            </tr>
                        </table>
                        
                    </div>
                    <div v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></div>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </div>
      </el-col>
        <div class="confirmPopup_b" v-if="show">
            <div class="confirmPopup">
                <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                    &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
                </div>
            请您确认是否要设置批次？
            <div class="confirm"><el-button  @click.once="batchSetting()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
            </div>
        </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
export default {
  data(){
    return{
        url: `${this.$baseUrl}`,
        BatchData:[] ,  //批次信息
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据
        loading:'',
        isShow:false,
        dialogVisible:false,
        purchase_sn:'',
        real_purchase_sn:'',
        delivery_time:'',
        arrive_time:'',
        batch:'',
        batchs:[],
        show:false,
        headersStr:'',
        //搜索输入内容
        shop_no:'',//店铺id
        trade_no:'',//erp订单号
        order_status:'',//系统订单状态
        erp_merchant_no:'',//商家编码
        trade_status:'',//erp订单状态
        receiver_name:'',//收货人
        receiver_mobile:'',//收货人手机
        goods_name:'',//商品名称
        spec_sn:'',//规格码
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getBatchData();
      this.tableTitle()
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
    getBatchData(){
            let vm=this;
            axios.get(vm.url+vm.$getOrderListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&trade_no="+vm.trade_no+"&shop_no="+vm.shop_no+"&order_status="+
            vm.order_status+"&erp_merchant_no="+vm.erp_merchant_no+"&trade_status="+vm.trade_status+"&receiver_name="+vm.receiver_name+"&receiver_mobile="+vm.receiver_mobile+
            "&goods_name="+vm.goods_name+"&spec_sn="+vm.spec_sn,
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close();
                vm.BatchData=res.data.orderList.data;
                vm.total=res.data.orderList.total;
                if(res.data.code==1002){
                    vm.isShow=true;
                }
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status!=''&&error.response.status=="401"){
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
            var thisHeight=$(".title").height();
            if(scrollTop>thisHeight){
                $(".getOrderList .tableTitle").addClass("addclass");
                $(".getOrderList .tableTitle").width($(".getOrderList .select").width());
            }
            if(scrollTop<thisHeight){
                    $(".getOrderList .tableTitle").removeClass("addclass");
                }
            })
        },
        //查看详情
        viewDetails(trade_no){
            let vm=this;
            this.$router.push('/orderListDetails?trade_no='+trade_no+'&isOrder=isOrder');
        },
        //搜索框
      searchFrame(){
          let vm=this;
          vm.getBatchData();
      },
        //取消设置
        Unset(){
            let vm=this;
            vm.purchase_sn='';
            vm.real_purchase_sn='';
            vm.delivery_time='';
            vm.arrive_time='';
            vm.batch='';
        },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.pagesize=val
          vm.getBatchData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.page=val
          vm.getBatchData()
      },
    
  }
}
</script>

<style scoped>
/* @import '../../css/common.css'; */
.getOrderList .content_text{
    width: 100%;
    border: 1px solid #ebeef5;
    margin-top: 20px;
    margin-bottom: 20px;
}
.getOrderList .content_text table{
    width: 100%;
}
.getOrderList .content_text table tr{
    height: 50px;
    line-height: 50px;
    border: 1px solid #ebeef5;
    line-height: 40px;
}
.getOrderList .title .search {
    left: 103px!important;;
    top: 7px!important;;
}

.getOrderList .content_text table th,td{
        border-bottom: 1px solid #ebeef5;
        border-left: 1px solid #ebeef5;
    }
.getOrderList .content_text table table  tr:nth-child(even){
    background:#fafafa;
}
.getOrderList .content_text table tr:hover{
    background:#ced7e6;
}
        
.getOrderList .tableTitle{
     width:100%;
     text-align: center;
     height: 50px;
     line-height: 50px;
     background-color: rgba(0, 0, 0, 0.1);
}
.getOrderList .userInformation{
    text-align:center;
    height: 50px;
    line-height: 50px;
    background-color: #f8fafb;
}
.getOrderList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.getOrderList .viewDetails{
    cursor: pointer;
}
</style>
<style scoped lang=less>
@import '../../css/taskModule.less';
</style>
