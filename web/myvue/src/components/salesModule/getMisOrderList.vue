<template>
  <div class="getMisOrderList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="lineHeightSixty MT_twenty select">
                        <span class="fontWeight MR_twenty">总单统计 ：</span>
                        <span class="fontWeight MR_twenty">BD数量({{orderStatistics.bdNum}}件)</span>
                        <span class="fontWeight MR_twenty">BD->DD转换率({{orderStatistics.bd_dd_Rate}})</span>
                        <span class="fontWeight MR_twenty" title="所有总单商品数量总和">商品总量({{orderStatistics.totalGoodsNum}}件)</span>
                        <span class="fontWeight MR_twenty">YD数量({{orderStatistics.ydNum}}件)</span>
                        <span class="fontWeight MR_twenty">DD数量({{orderStatistics.ddNum}}件)</span>
                        <span class="fontWeight MR_twenty">YD->BD转换率({{orderStatistics.yd_bd_Rate}})</span>
                    </div>
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>大YD单列表</span></div></el-col>
                        <el-col :span="3"><div>MIS订单号：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="mis_order_sn" placeholder="请输入MIS订单号"></el-input></div></el-col>
                        <el-col :span="3"><div>商家编码：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div>
                        </el-col>
                        <el-col :span="3"><div>销售用户：</div></el-col>
                        <el-col :span="3">
                            <div>
                                <template>
                                    <el-select v-model="sale_user_id" clearable placeholder="请选择销售用户">
                                        <el-option v-for="item in saleUserInfo" :key="item.id" :label="item.user_name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                        </el-col>
                    </el-row>
                    <el-row class="fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" :offset="3"><div>商品名称：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                        <el-col :span="3"><div>商品规格码：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                    </el-row>
                    <el-row class="MB_ten">
                        <el-col :span="24">
                            <div class="fontRight">
                                <span class="bgButton MR_ten" @click="searchFrame()">搜索</span>
                                <span class="bgButton MR_ten" @click="importOrderPage();clear()">导入总单
                                    <span style="display: none" class="el-icon-loading upDataLoading"></span>
                                </span>
                                <el-dialog title="导入总单" :visible.sync="dialogVisible" width="800px">
                                    <span class="redFont F_S_Sixteen" style="position: absolute;top: 24px;left: 104px;">*请确认sku的exw折扣是否为最新折扣</span>
                                    <span class="notBgButton" style="position: absolute;top: 306px;right: 371px;" @click="Download()">下载模板</span>
                                    <el-row class="lineHeightForty MB_twenty">
                                        <el-col :span="4"><div class="floatLift"><span class="redFont">*</span>销售客户：</div></el-col>
                                        <el-col :span="6">
                                            <div>
                                                <template>
                                                    <el-select v-model="saleUserid" filterable placeholder="请选择销售客户">
                                                        <el-option v-for="item in saleUseridS" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                                    </el-select>
                                                </template>
                                            </div>
                                        </el-col>
                                        <el-col :span="4" :offset="1"><div class="floatLift"><span class="redFont">*</span>备注：</div></el-col>
                                        <el-col :span="6">
                                            <div>
                                                <el-input v-model="mark" placeholder="请输入xx月x周"></el-input>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <el-row class="lineHeightForty MB_twenty">
                                        <el-col :span="4"><div class="floatLift"><span class="redFont">*</span>线上/线下：</div></el-col>
                                        <el-col :span="6">
                                            <div>
                                                <template>
                                                    <el-select v-model="orderType" filterable placeholder="请选择线上/线下">
                                                        <el-option v-for="item in orderTypeS" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                                    </el-select>
                                                </template>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <el-row class="lineHeightForty MB_twenty">
                                        <el-col :span="4"><div class="floatLift"><span class="redFont">*</span>请选择导入文件：</div></el-col>
                                        <el-col :span="6">
                                            <div>
                                                <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                                    <div class="file">
                                                    <img src="../../image/upload.png"/>
                                                    <span v-if="fileName==''">点击上传文件</span>
                                                    <span v-if="fileName!=''">{{fileName}}</span>
                                                    <input class="w_h_ratio" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                                    </div>
                                                </form>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <div slot="footer" class="dialog-footer">
                                        <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                        <el-button type="primary" @click="confirmUpData()">确 定 </el-button>
                                    </div>
                                </el-dialog>
                                
                            </div>
                        </el-col>
                    </el-row>
                    <el-row>
                        <table class="tableTitle">
                            <thead>
                                <th width="20%">商品名称</th>
                                <th width="10%">商品规格码</th>
                                <th width="10%">商家编码</th>
                                <th width="10%">需求量</th>
                                <th width="10%">商品实时库存</th>
                                <th width="10%">
                                    待采数量
                                    <template>
                                        <el-popover  placement="top-start" width="200" trigger="click"
                                            content="需求量-当前库存量，如果为负数则显示为0，会随库存而变化">
                                            <i class="el-icon-question redFont" slot="reference"></i>
                                        </el-popover>
                                    </template>

                                </th>
                                <th width="10%">
                                    预判采购数量
                                    <template>
                                        <el-popover  placement="top-start" width="200" trigger="click"
                                            content="采购部预先判断的数量,默认值为生成总单时的待采数量，不随库存而变化">
                                            <i class="el-icon-question redFont" slot="reference"></i>
                                        </el-popover>
                                    </template>
                                </th>
                                <th width="10%">销售折扣</th>
                            </thead>
                        </table>
                    </el-row>
                    <table class="tableTitleTwo">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="10%">商品规格码</th>
                            <th width="10%">商家编码</th>
                            <th width="10%">需求量</th>
                            <th width="10%">商品实时库存</th>
                            <th width="10%">
                                待采数量
                                <template>
                                    <el-popover  placement="top-start" width="200" trigger="click"
                                        content="需求量-当前库存量，如果为负数则显示为0，会随库存而变化">
                                        <i class="el-icon-question redFont" slot="reference"></i>
                                    </el-popover>
                                </template>

                            </th>
                            <th width="10%">
                                预判采购数量
                                <template>
                                    <el-popover  placement="top-start" width="200" trigger="click"
                                        content="采购部预先判断的数量,默认值为生成总单时的待采数量，不随库存而变化">
                                        <i class="el-icon-question redFont" slot="reference"></i>
                                    </el-popover>
                                </template>
                            </th>
                            <th width="10%">销售折扣</th>
                        </thead>
                    </table>
                    <el-row>
                        <div v-for="(item,index) in tableData" :class="['content_text','content_text'+index]">
                            <el-row class="userInformation">
                                <el-col :span="5"><div style="color:#508cee">订单标记：{{item.type_desc+'-'+item.mark}}</div></el-col>
                                <el-col :span="6"><div class="">订单编号：{{item.mis_order_sn}}</div></el-col>
                                <el-col :span="4"><div class="">用户名：{{item.user_name}}</div></el-col>
                                <el-col :span="4"><div class="">部门：{{item.de_name}}</div></el-col>
                                <el-col :span="5" class="fontRight">
                                    <span class="notBgButton" @click.once="viewDetails(item.mis_order_sn)">大YD单详情</span>
                                    <i class="el-icon-arrow-down notBgButton" :title="'点击展示更多 共:'+item.goods_data.length+'条商品'" @click="pullOrUp(index,$event,item.goods_data.length)"></i>  
                                </el-col>
                            </el-row>
                            <el-row class="userInformation">
                                <el-col :span="5">
                                    <div v-if="item.is_advance==0" class="redFont">是否预判：{{item.advance_desc}}</div>
                                    <div v-if="item.is_advance==1" class="D_greenFont">是否预判：{{item.advance_desc}}</div>
                                </el-col>
                                <el-col :span="6">
                                    <div v-if="item.is_offer==0" class="redFont">是否报价：{{item.offer_desc}}</div>
                                    <div v-if="item.is_offer==1" class="D_greenFont">是否报价：{{item.offer_desc}}</div>
                                </el-col>
                                <!-- <el-col :span="5"><div class="">订单状态：{{item.status}}</div></el-col> -->
                                <el-col :span="4"><div class="">商品数量：{{item.goods_num}}</div></el-col>
                                <el-col :span="4"><div class="">新品数量：{{item.isReplenish.newGoodsNum}}</div></el-col>
                                <el-col :span="5"><div class="">创建时间：{{item.create_time}}</div></el-col>
                            </el-row>
                            <table style="width:100%;text-align: center">
                                <tr v-for="good in item.goods_data">
                                    <td width="20%" class="overOneLinesHeid fontLift">
                                        <el-tooltip class="item" effect="light" :content="good.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{good.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td width="10%">{{good.spec_sn}}</td>
                                    <td width="10%">{{good.erp_merchant_no}}</td>
                                    <td width="10%">{{good.goods_number}}</td>
                                    <td width="10%">{{good.stock_num}}</td>
                                    <td width="10%">
                                        <span v-if="good.buy_num<0">0</span>
                                        <span v-else>{{good.buy_num}}</span>                                    
                                    </td>
                                    <td width="10%">{{good.wait_buy_num}}</td>
                                    <td width="10%">{{good.sale_discount}}</td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
                    <el-dialog title="提示" :visible.sync="tipsDialogVisible" width="800px">
                        <div v-for="(item,index) in tipsStr" style="display:inline-block"><span>{{item}}<span v-if="index!=tipsStr.length-1">,</span></span></div>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="tipsDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="tipsDialogVisible = false">确 定</el-button>
                        </span>
                    </el-dialog>
                    <notFound v-if="isShow"></notFound>
                    <el-row>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :current-page="page" :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <successfulOperation :msgStr="msgStr"></successfulOperation>
      <operationFailed :msgStr="msgStr"></operationFailed>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
import upDataButton from '@/components/UiAssemblyList/upDataButton'
import notFound from '@/components/UiAssemblyList/notFound';
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
export default {
  components:{
      notFound,
      upDataButton,
      fontWatermark,
      operationFailed,
      successfulOperation,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      headersStr:'',
      tableData:[],
      statusInfo:[],
      saleUserInfo:[],
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      search:'',//用户输入数据
      isShow:false,
      upDataStr:'导入总单',
      dialogVisible:false,
      regionLoading:false,
      tipsDialogVisible:false,
      tipsStr:'',
      orderStatistics:'',
      //搜索字段 
      mis_order_sn:'',
      status:'',
      erp_merchant_no:'',
      goods_name:'',
      spec_sn:'',
      sale_user_id:'',
      //导入总单弹出框 
      saleUseridS:[],
      saleUserid:'',
      fileName:'',
      index:0,
      mark:'',
      orderTypeS:[],
      orderType:'',

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
    getDataList(loa){
        let vm = this;
        $(".el-icon-arrow-up").hide();
        window.addEventListener('beforeunload', e => {
            sessionStorage.setItem("state",'1');
        });
        let state = sessionStorage.getItem("state");
        if(state=='1'){
            vm.$router.push('/getMisOrderList');
            sessionStorage.setItem("state",'null');
        }
        if(vm.$route.query.misOrderSn!=undefined){
            vm.mis_order_sn=vm.$route.query.misOrderSn
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            vm.erp_merchant_no=vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.goods_name!=undefined){
            vm.goods_name=vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            vm.spec_sn=vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            vm.sale_user_id=parseInt(vm.$route.query.sale_user_id)
        }
        if(vm.$route.query.page!=undefined){ 
            vm.page=vm.$route.query.page
        }
        vm.orderTypeS.splice(0);
        axios.post(vm.url+vm.$getMisOrderListURL,
            {
                "mis_order_sn":vm.mis_order_sn,
                "status":vm.status,
                "erp_merchant_no":vm.erp_merchant_no,
                "goods_name":vm.goods_name,
                "spec_sn":vm.spec_sn,
                "page":vm.page,
                "pageSize":vm.pagesize,
                "sale_user_id":vm.sale_user_id,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(loa!=null){
                loa.close();
            }
            if(res.data.orderList.total>0){
                vm.tableData=res.data.orderList.data;
                vm.total=res.data.orderList.total;
                vm.orderStatistics=res.data.orderStatistics;
                vm.statusInfo=res.data.status;
                vm.saleUserInfo=res.data.saleUserInfo;
                let orderType=res.data.orderType;
                for(var key in orderType){
                    vm.orderTypeS.push({"label":orderType[key],"id":key})
                }
                vm.isShow=false;
            }else if(res.data.orderList.total==0){
                vm.isShow=true;
                vm.tableData=[];
                let orderType=res.data.orderType;
                for(var key in orderType){
                    vm.orderTypeS.push({"label":orderType[key],"id":key})
                }
                console.log(vm.orderTypeS)
                vm.total=0;
            }else{
                vm.$message(res.data.msg);
            }
            if(vm.$route.query.page!=undefined){ 
                vm.recordPageSize(vm.$route.query.page)
            }
        }).catch(function (error) {
            vm.loading.close();
            if(loa!=null){
                loa.close();
            }
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
            var thisHeight=370;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".getMisOrderList .tableTitleTwo").addClass("addclass");
                $(".getMisOrderList .tableTitleTwo").width($(".getMisOrderList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".getMisOrderList .tableTitleTwo").removeClass("addclass");
            }
        })
    },
    //打开导入总单弹框
    importOrderPage(){
        let vm = this;
        vm.saleUseridS.splice(0);
        vm.$router.push('/getMisOrderList');
        vm.page=1;
        if(vm.index==1){
            return false;
        }
        $(".upDataLoading").show();
        vm.index=1;
        axios.get(vm.url+vm.$importOrderPageURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.dialogVisible=true;
            res.data.saleUserInfo.forEach(element => {
                vm.saleUseridS.push({"label":element.user_name,"id":element.id});
            });
            vm.index=0;
            $(".upDataLoading").hide();
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    SelectFile(){
        let vm = this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    //确认上传
    confirmUpData(){
        this.regionLoading=true;
        let vm=this;
        var formDate = new FormData($("#forms")[0]);
        if(vm.fileName==''){
            vm.$message('上传文件不能为空');
            return false;
        }else if(vm.saleUserid==''){
            vm.$message('销售客户id不能为空');
            return false;
        }else if(vm.orderType==''){
            vm.$message('线上/线下不能为空');
            return false;
        }else if(vm.mark.trim('') ==''){
            vm.$message('备注不能为空');
            return false;
        }
        vm.dialogVisible = false;
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$importOrderURL+"?sale_user_id="+vm.saleUserid+"&order_type="+vm.orderType+"&mark="+vm.mark,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code==2000){
                vm.dialogVisible = false;
                vm.msgStr=res.msg;
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                vm.getDataList();
            }else if(res.code==2059){
                vm.dialogVisible = false;
                vm.tipsDialogVisible=true;
                let msg=res.msg.split(',')
                vm.tipsStr=msg;
            }else{
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
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
    //清除掉已填表单 
    clear(){
        let vm = this;
        $("#file1").val('');
        vm.fileName='';
        vm.saleUserid='';
        vm.orderType='';
        vm.mark='';
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
    //查看详情
    viewDetails(mis_order_sn){
        let vm = this;
        let urlParam = '&page='+vm.page;
        if(vm.mis_order_sn!=''){
            urlParam='&misOrderSn='+vm.mis_order_sn
        }
        if(vm.erp_merchant_no!=''){
            urlParam+='&erp_merchant_no='+vm.erp_merchant_no
        }
        if(vm.goods_name!=''){
            urlParam+='&goods_name='+vm.goods_name
        }
        if(vm.spec_sn!=''){
            urlParam+='&spec_sn='+vm.spec_sn
        }
        if(vm.sale_user_id!=''){
            urlParam+='&sale_user_id='+vm.sale_user_id
        }
        this.$router.push('/MISorderListDetails?mis_order_sn='+mis_order_sn+'&isMISOrder=isMISOrder'+urlParam);
    },
    //下载总单
    Download(){
        let vm = this;
        window.open(vm.downloadUrl+'/总单导入模板.xlsx');
    },
    //搜索 
    searchFrame(){
        let vm = this;
        vm.page=1;
        $(".el-pager li").eq(vm.$route.query.page-1).removeClass("active")
        $(".el-pager li").eq(0).addClass("active")
        vm.$router.push('/getMisOrderList');
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        vm.getDataList(loa);
    },
    //分页 
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        $(".el-pager li").eq(vm.$route.query.page-1).removeClass("active")
        let urlParam = '';
        if(vm.$route.query.misOrderSn!=undefined){
            urlParam='&mis_order_sn='+vm.$route.query.misOrderSn
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            urlParam+='&erp_merchant_no='+vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.goods_name!=undefined){
            urlParam+='&goods_name='+vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            urlParam+='&spec_sn='+vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            urlParam+='&sale_user_id='+vm.$route.query.sale_user_id
        }
        vm.$router.push('/getMisOrderList?isfirst=isfirst'+urlParam);
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        $(".el-pager li").eq(vm.$route.query.page-1).removeClass("active")
        let urlParam = '';
        if(vm.$route.query.misOrderSn!=undefined){
            urlParam='&mis_order_sn='+vm.$route.query.misOrderSn 
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            urlParam+='&erp_merchant_no='+vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.goods_name!=undefined){
            urlParam+='&goods_name='+vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            urlParam+='&spec_sn='+vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            urlParam+='&sale_user_id='+vm.$route.query.sale_user_id
        }
        vm.$router.push('/getMisOrderList?isfirst=isfirst'+urlParam);
        vm.page=val
        vm.getDataList()
    },
  },
  computed:{
      recordPageSize(){
          return function(p){
              setTimeout(function(){
                $(".el-pager li").eq(0).removeClass("active")
                $(".el-pager li").eq(parseInt(p)-1).addClass("active")
              },1000)
          }
      }
  }
}
</script>

<style>
.getMisOrderList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.getMisOrderList .content_text{
    width: 100%;
    height: 160px;
    overflow: hidden;
    border: 1px solid #ebeef5;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
}
.getMisOrderList .content_text table{
    width: 100%;
}
.getMisOrderList .content_text table tr{
    height: 50px;
    line-height: 50px;
    line-height: 40px;
}
.getMisOrderList .content_text table table  tr:nth-child(even){
    background:#fafafa;
}
.getMisOrderList .content_text table tr:hover{
    background:#ced7e6;
}
.getMisOrderList .userInformation{
    text-align:center;
    height: 50px;
    line-height: 50px;
    background-color: #f8fafb;
    font-weight: bold;
}
.getMisOrderList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.getMisOrderList .file {
    position: relative;
    display: inline-block;
    text-align: center;
}
.getMisOrderList .file img{
  padding-top: 20px;
}
.getMisOrderList .file span{
  display: inline-block;
  font-size: 15px;
}
.getMisOrderList .file input {
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;
}
.getMisOrderList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.getMisOrderList .el-icon-arrow-up{
    display: inline-block !important;
}
.getMisOrderList .is-show-close{
    display: inline-block !important;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>

