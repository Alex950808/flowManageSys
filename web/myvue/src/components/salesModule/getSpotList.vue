<template>
  <div class="getSpotList">
      <!-- 现货单列表 -->
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                        <el-col :span="2"><div><span class="fontWeight"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;现货单列表</span></div></el-col>
                        <el-col :span="2"><div>现货单号</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="spot_order_sn" placeholder="请输入现货单号"></el-input></div></el-col>
                        <el-col :span="2"><div>子单单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="sub_order_sn" placeholder="请输入子单单号"></el-input></div></el-col>
                        <el-col :span="2"><div>商品名称</div></el-col>
                        <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                        <el-col :span="2"><div>商品规格码</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                        <el-col :span="2"><span class="bgButton" @click="searchFrame()">点击搜索</span></el-col>
                    </el-row>
                    <!-- <el-row class="MB_ten">
                        <el-col :span="24">
                            <div class="fontRight">
                                
                            </div>
                        </el-col>
                    </el-row> -->
                    <table class="tableTitle">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="10%">商品规格码</th>
                            <th width="10%">订单数量</th>
                            <th width="10%">美金原价</th>
                            <th width="10%">商品库存</th>
                        </thead>
                    </table>
                    <table class="tableTitleTwo">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="10%">商品规格码</th>
                            <th width="10%">订单数量</th>
                            <th width="10%">美金原价</th>
                            <th width="10%">商品库存</th>
                        </thead>
                    </table>
                    <el-row>
                        <div v-for="(item,index) in tableData" :class="['content_text','content_text'+index]">
                            <el-row class="userInformation">
                                <span class="MR_Four ML_Thitty">现货单号 ：{{item.spot_order_sn}}</span>
                                <span class="MR_Four ML_Thitty">子订单号 ：{{item.sub_order_sn}}</span>
                                <span class="MR_Four ML_Thitty">是否推送 ：{{item.desc_push}}</span>
                                <!-- <span class="MR_ten ML_ten">订单状态 ：{{item.desc_status}}</span> -->
                                <span class="MR_Four ML_Thitty">创建时间 ：{{item.create_time}}</span>
                                
                                    <span class="MR_Four ML_Thitty">
                                        <span class="notBgButton" @click="viewDetails(item.spot_order_sn)">订单详情</span>
                                    </span>
                                
                                
                                    <i class="el-icon-arrow-down notBgButton" :title="'点击展示更多 共:'+item.goods_data.length+'条商品'" @click="pullOrUp(index,$event,item.goods_data.length)"></i>
                                
                            </el-row>
                            <table class="table-two" style="width:100%;text-align: center">
                                <tr v-for="good in item.goods_data">
                                    <td width="20%" class="overOneLinesHeid fontLift">
                                        <el-tooltip class="item" effect="light" :content="good.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{good.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td width="10%">{{good.spec_sn}}</td>
                                    <td width="10%">{{good.goods_number}}</td>
                                    <td width="10%">{{good.spec_price}}</td>
                                    <td width="10%">{{good.gsStockNum}}</td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-row>
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
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';
export default {
  components:{
      notFound,
      fontWatermark
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      search:'',//用户输入数据
      //搜索参数
      spot_order_sn:'',//现货单号
      goods_name:'',//商品名称
      sub_order_sn:'',//订单状态
      spec_sn:'',//商品规格码 
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.tableTitle();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.post(vm.url+vm.$getSpotListURL,
            {
                "spot_order_sn":vm.spot_order_sn,
                "sub_order_sn":vm.sub_order_sn,
                "erp_merchant_no":vm.erp_merchant_no,
                "goods_name":vm.goods_name,
                "spec_sn":vm.spec_sn,
                "sub_order_sn":vm.sub_order_sn,
                "page":vm.page,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close();
            if(res.data.spotOrderList.total>0){
            vm.tableData=res.data.spotOrderList.data;
            vm.total=res.data.spotOrderList.total;
                vm.isShow=false;
            }else if(res.data.spotOrderList.total==0){
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
    tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=250;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".getSpotList .tableTitleTwo").addClass("addclass");
                $(".getSpotList .tableTitleTwo").width($(".getSpotList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".getSpotList .tableTitleTwo").removeClass("addclass");
            }
        })
    },
    viewDetails(spot_order_sn){
        let vm = this;
        this.$router.push('/getSpotDetail?spot_order_sn='+spot_order_sn);
    },
    //点击下拉或者收起表格
    pullOrUp(index,event,length){
        let vm = this;
        let content_text_height=$(event.target).parent().parent().height();
        if(content_text_height==100){
            $(".content_text"+index+"").css({
                "transition-property:":"height",
                "transition-duration":"5s",
            })
            $(".content_text"+index+"").height('auto');
            $(event.target).addClass("el-icon-arrow-up");
            $(event.target).removeClass("el-icon-arrow-down");
            $(event.target).attr("title","点击收起")
        }else{
            $(".content_text"+index+"").height('100');
            $(event.target).addClass("el-icon-arrow-down");
            $(event.target).removeClass("el-icon-arrow-up");
            $(event.target).attr("title","点击展示更多 共"+length+"条商品");
        }
    },
    //搜索
    searchFrame(){
        let vm = this;
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
  }
}
</script>

<style>
.getSpotList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.getSpotList .content_text{
    width: 100%;
    height: 100px;
    overflow: hidden;
    border: 1px solid #ebeef5;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
}
.getSpotList .content_text table{
    width: 100%;
}
.getSpotList .content_text table tr{
    height: 50px;
    line-height: 50px;
    line-height: 40px;
}
.getSpotList .content_text table table  tr:nth-child(even){
    background:#fafafa;
}
.getSpotList .content_text table tr:hover{
    background:#ced7e6;
}
.getSpotList .userInformation{
    text-align:center;
    height: 50px;
    line-height: 50px;
    background-color: #f8fafb;
    font-weight: bold;
}
.getSpotList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.getSpotList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
</style>
