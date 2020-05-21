<template>
  <div class="offerList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                        <el-col :span="4" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>客户订单报价列表</span></div></el-col>
                        <el-col :span="3"><div>报价单号：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="offer_sn" placeholder="请输入报价单号"></el-input></div></el-col>
                        <el-col :span="3"><div>商品规格码：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                        <el-col :span="3"><div>销售用户：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品规格码"></el-input></div></el-col>
                    </el-row>
                    <el-row class="fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" :offset="4"><div>商家编码：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                        <el-col :span="3"><div>平台条码：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="platform_barcode" placeholder="请输入平台条码"></el-input></div></el-col>
                    </el-row>
                    <el-row class="MB_ten">
                        <el-col :span="24">
                            <div class="fontRight">
                                <span class="bgButton MR_ten" @click="searchFrame()">搜索</span>
                                <span class="bgButton MR_ten" @click="openImportSkuOffer()">上传需要报价的SKU
                                    <!-- <span style="display: none" class="el-icon-loading upDataLoading"></span> -->
                                </span>
                            </div>
                        </el-col>
                    </el-row>
                    <el-row>
                        <table class="tableTitle">
                            <thead>
                                <th width="20%">商品名称</th>
                                <th width="20%">商品规格码</th>
                                <th width="10%">商家编码</th>
                                <th width="20%">平台条码</th>
                                <th width="10%">美金原价</th>
                                <th width="10%">EXW折扣</th>
                                <th width="10%">销售折扣</th>
                            </thead>
                        </table>
                    </el-row>
                    <table class="tableTitleTwo">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="20%">商品规格码</th>
                            <th width="10%">商家编码</th>
                            <th width="20%">平台条码</th>
                            <th width="10%">美金原价</th>
                            <th width="10%">EXW折扣</th>
                            <th width="10%">销售折扣</th>
                        </thead>
                    </table>
                    <el-row>
                        <div v-for="(item,index) in tableData" :class="['content_text','content_text'+index]">
                            <el-row class="userInformation">
                                <el-col :span="7"><div class="notBgButton"  @click.once="viewDetails(item.offer_sn)">报价单号：{{item.offer_sn}}</div></el-col>
                                <el-col :span="6"><div class="">创建时间：{{item.create_time}}</div></el-col>
                                <el-col :span="8"><div class="">备注：{{item.remark}}</div></el-col>
                                <el-col :span="3" class="fontRight">
                                    <span class="notBgButton" @click.once="viewDetails(item.offer_sn)">报价详情</span>
                                    <i class="el-icon-arrow-down notBgButton" :title="'点击展示更多 共:'+item.goods_data.length+'条商品'" @click="pullOrUp(index,$event,item.goods_data.length)"></i>  
                                </el-col>
                            </el-row> 
                            <table style="width:100%;text-align: center">
                                <tr v-for="good in item.goods_data">
                                    <td width="20%" class="overOneLinesHeid fontLift">
                                        <el-tooltip class="item" effect="light" :content="good.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{good.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td width="20%">{{good.spec_sn}}</td>
                                    <td width="10%">{{good.erp_merchant_no}}</td>
                                    <td width="20%" class="overOneLinesHeid">
                                        <!-- {{good.platform_barcode}} -->
                                        <el-tooltip class="item" effect="light" :content="good.platform_barcode" placement="right">
                                        <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{good.platform_barcode}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td width="10%">{{good.spec_price}}</td>
                                    <td width="10%">{{good.exw_discount}}</td>
                                    <td width="10%">
                                        <span>{{good.sale_discount}}</span>                                    
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <div class="beijing">
          <span @click="d_table()" class="d_table blueFont Cursor">报价信息导入模板下载</span>
      </div>
      <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
      <successfulOperation :msgStr="msgStr"></successfulOperation>
      <operationFailed :msgStr="msgStr"></operationFailed>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
export default {
  components:{
      notFound,
      upDataButtonByBox,
      operationFailed,
      successfulOperation,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//默认数据总数 
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //搜索 
      offer_sn:'',
      spec_sn:'',
      goods_name:'',
      erp_merchant_no:'',
      platform_barcode:'',
      //导入报价销售折扣 
      titleStr:'上传需要报价的SKU',
      msgStr:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.tableTitle();
  },
  methods:{
      //
    getDataList(){
        let vm = this;
        axios.post(vm.url+vm.$offerListURL,
            {
                "page_size":vm.pagesize,
                "page":vm.page,
                "offer_sn":vm.offer_sn,
                "spec_sn":vm.spec_sn,
                "goods_name":vm.goods_name,
                "erp_merchant_no":vm.erp_merchant_no,
                "platform_barcode":vm.platform_barcode,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.offerList.data.length!=0){
                vm.tableData=res.data.offerList.data;
                vm.total=res.data.offerList.total;
                vm.isShow=false;
            }else{
                vm.isShow=true;
                // vm.$message(res.data.msg);
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
            var thisHeight=270;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".offerList .tableTitleTwo").addClass("addclass");
                $(".offerList .tableTitleTwo").width($(".offerList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".offerList .tableTitleTwo").removeClass("addclass");
            }
        })
    },
    //查看详情 
    viewDetails(offer_sn){
        let vm = this;
        this.$router.push('/getOfferDetail?offer_sn='+offer_sn);
    },
    //点击下拉或者收起表格 
    pullOrUp(index,event,length){
        let vm = this;
        let content_text_height=$(event.target).parent().parent().parent().height();
        if(content_text_height==160){
            $(".content_text"+index+"").css({
                "transition-property:":"height",
                "transition-duration":"5s",
            })
            $(".content_text"+index+"").height('auto');
            $(event.target).addClass("el-icon-arrow-up");
            $(event.target).removeClass("el-icon-arrow-down");
            $(event.target).attr("title","点击收起")
        }else{
            $(".content_text"+index+"").height('160');
            $(event.target).addClass("el-icon-arrow-down");
            $(event.target).removeClass("el-icon-arrow-up");
            $(event.target).attr("title","点击展示更多 共"+length+"条商品");
        }
    },
    //打开导入报价按钮
    openImportSkuOffer(){
        let vm = this;
        $(".upDataButtonByBox_b").fadeIn();
        $(".d_table").fadeIn();
        $(".beijing").fadeIn();
    },
    //确认上传 
    GFFconfirmUpData(formDate){
        let vm=this;
        $(".upDataButtonByBox_b").fadeOut();
        $(".d_table").fadeOut();
        $(".beijing").fadeOut();
        let headersToken=sessionStorage.getItem("token");
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$offerSkuUploadURL,
        type: "POST",
        async: true,
        cache: false,
        headers:{
            'Authorization': 'Bearer ' + headersToken,
            'Accept': 'application/vnd.jmsapi.v1+json',
        },
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code=='2000'){
                // let msg = res.msg.split(',')
                vm.msgStr=res.msg;
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                vm.getDataList();
                $("#file").val('');
                vm.fileName='';
                // vm.$message(res.msg); 
            }else{
                // let msg = res.msg.split(',')
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
                // vm.$message(res.msg);
                $("#file").val('');
                vm.fileName='';
            }
        }
        }).catch(function (error) {
                loa.close();
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
    searchFrame(){
        let vm = this;
        vm.page=1
        vm.getDataList()
    },
    //模板表下载 
    d_table(){
        let vm=this;
        window.open(vm.downloadUrl+'/报价SKU导入模板.xlsx');
    }
  }
}
</script>

<style>
.offerList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.offerList .content_text{
    width: 100%;
    height: 160px;
    overflow: hidden;
    border: 1px solid #ebeef5;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
}
.offerList .content_text table{
    width: 100%;
}
.offerList .content_text table tr{
    height: 50px;
    line-height: 50px;
    line-height: 40px;
}
.offerList .content_text table table  tr:nth-child(even){
    background:#fafafa;
}
.offerList .content_text table tr:hover{
    background:#ced7e6;
}
.offerList .userInformation{
    text-align:center;
    height: 50px;
    line-height: 50px;
    background-color: #f8fafb;
    font-weight: bold;
}
.offerList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.offerList .file {
    position: relative;
    display: inline-block;
    text-align: center;
}
.offerList .file img{
  padding-top: 20px;
}
.offerList .file span{
  display: inline-block;
  font-size: 15px;
}
.offerList .file input {
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;
}
.offerList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.offerList .el-icon-arrow-up{
    display: inline-block !important;
}
.offerList .is-show-close{
    display: inline-block !important;
}
.offerList .beijing{
    width: 100%;
    height: 100%;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    display: none;
}
.offerList .d_table{
    position: fixed;
    z-index: 10039;
    width: 385px;
    height: 27px;
    margin: 250px auto;
    top: 218px;
    left: 225px;
    bottom: 0;
    right: 0;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>