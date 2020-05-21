<template>
  <div class="distributionPage_b">
    <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="distributionPage">
                        <el-row class="listTitleStyle">
                            <el-col :span="18">
                                <span class="bgButton MR_twenty" @click="backToUpPage()">返回上一页</span>
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购期需求汇总</span>
                            </el-col>
                            <el-col :span="6">
                                <span class="bgButton" v-if="d_isShow" @click="d_table()">下载待分配数据</span>
                                <span class="bgButton" v-if="d_isShow" @click="confirmShow()">上传待分配数据</span>
                            </el-col>
                        </el-row>
                        <div class="tableTitle">
                            <ul>
                                <li class="purchaseName">
                                    <span class="xuanzhong" @click="getUserNameData('',0)">全部
                                        <input type="text" title="0" style="display:none"/>
                                    </span>
                                    <span class="weixuanzhong" v-for="(item,index) in purchaseName" @click="getUserNameData(item,index+1)">{{item}}
                                        <input type="text" :title="index+1" style="display:none"/>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <div class="t_i whole">
                                <div class="t_i_h" id="hh">
                                    <div class="ee">
                                        <table cellpadding="0" cellspacing="0" border="0" v-if="isWhole" :style="wholeTable()">
                                            <thead>
                                                <tr>
                                                    <th :style="labelWidth()">商品标签</th>
                                                    <th style="width:200px;">商品名称</th>
                                                    <th style="width:150px">商品代码</th>
                                                    <th style="width:150px">erp编码</th>
                                                    <th style="width:150px">商品规格码</th>
                                                    <th style="width:100px">商品需求数量</th>
                                                    <th style="width:100px">待分配数量</th>
                                                    <th style="width:100px">可采数</th>
                                                    <th style="width:100px">销售折扣</th>
                                                    <th style="width:150px" v-for="(item,index) in purchaseName">{{item}}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <table cellpadding="0" cellspacing="0" border="0" v-if="isPart" :style="tableWidth()">
                                            <thead>
                                                <tr>
                                                    <th :style="labelWidth()">商品标签</th>
                                                    <th style="width:200px;">商品名称</th>
                                                    <th width="180px">采购期单号</th>
                                                    <th width="180px">需求单号</th>
                                                    <th width="170px">商品代码</th>
                                                    <th width="170px">商家编码</th>
                                                    <th width="170px">商品规格码</th>
                                                    <th width="100px">需求数</th>
                                                    <th width="100px">可分配数</th>
                                                    <th style="width:100px">销售折扣</th>
                                                    <th style="width:100px">外采临界点</th>
                                                    <th class="tableWidth" :width='thWidth()'>采购折扣</th>
                                                    <th width="80px">操作</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="cc" id="cc" @scroll="scrollEvent()">
                                    <table cellpadding="0" cellspacing="0" border="0" v-if="isWhole" :style="wholeTable()">
                                        <tr v-for="(item,index) in tableData">
                                            <td :style="labelWidth()">
                                                <span v-for="labelInfo in item.goods_label_list">
                                                    <i class="NotStyleForI PL_ten PR_ten B_R ML_ten" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</i>
                                                </span>
                                            </td>
                                            <td class="ellipsis" style="width:200px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:150px">{{item.erp_prd_no}}</td>
                                            <td style="width:150px">{{item.erp_merchant_no}}</td>
                                            <td style="width:150px">{{item.spec_sn}}</td>
                                            <td style="width:100px">{{item.goods_num}}</td>
                                            <td style="width:100px">{{item.allot_num}}</td>
                                            <td style="width:100px">{{item.total_may_num}}</td>
                                            <td style="width:100px">{{item.sale_discount}}</td>
                                            <td style="width:150px" v-for="(itemO,index) in purchaseName">{{item[itemO]}}</td>
                                        </tr>
                                    </table>
                                    <table cellpadding="0" cellspacing="0" border="0" v-if="isPart" :style="tableWidth()">
                                        <tr v-for="(item,indexT) in tableData" :style="backgroundColor(item.edit_status)">
                                            <td :style="labelWidth()">
                                                <span v-for="labelInfo in item.goods_label_list">
                                                    <i class="NotStyleForI PL_ten PR_ten B_R ML_ten" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</i>
                                                </span>
                                            </td>
                                            <td class="ellipsis" style="width:200px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td width="180px">{{item.purchase_sn}}</td>
                                            <td width="180px">{{item.demand_sn}}</td>
                                            <td width="170px">{{item.erp_prd_no}}</td>
                                            <td width="170px">{{item.erp_merchant_no}}</td>
                                            <td width="170px">{{item.spec_sn}}</td>
                                            <td width="100px">{{item.goods_num}}</td>
                                            <td width="100px">{{item.allot_num}}</td>
                                            <td width="100px">{{item.sale_discount}}</td>
                                            <td width="100px">{{item.wai_line_point}}</td>
                                            <td :width='thWidth()' style="text-align:left;">
                                                <span v-for="(itemO,index) in item.discount_info" class="spanWidth"><span class="Serial">{{index+1}}</span><span title="渠道名称">{{itemO.brand_channel}}&nbsp;&nbsp;&nbsp;&nbsp;</span> <span title="品牌折扣">{{itemO.brand_discount}}&nbsp;&nbsp;&nbsp;&nbsp;</span><span title="可采数量">{{itemO.may_num}}&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
                                            </td>
                                            <td width="80px">
                                                <i class="iconfont notBgButton" @click="operation(item.demand_sn,item.purchase_sn,item.spec_sn,item.allot_num,item.goods_num)">&#xe60d;</i>
                                            </td>
                                        </tr>
                                    </table>
                                    <el-dialog title="修改可采数" :visible.sync="dialogVisible" width="800px">
                                        <div>
                                            <table style="width:700px;">
                                                <tr>
                                                    <td>
                                                        {{manipulate.goods_name}}
                                                    </td>
                                                    <td>折扣</td>
                                                    <td>需求数</td>
                                                    <td>可分配数</td>
                                                    <td>可采数量</td>
                                                </tr>
                                                <tr  v-for="(itemT,index) in manipulate.goods_discount_list">
                                                    <td>{{itemT.channel_name}}</td>
                                                    <td>{{itemT.brand_discount}}</td>
                                                    <td>{{goods_num}}</td>
                                                    <td>{{allot_num}}</td>
                                                    <td class="inVal">
                                                        <input type="text" :name="index" :value="itemT.may_num"/>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <span slot="footer" class="dialog-footer">
                                            <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                            <el-button type="primary" @click="dialogVisible = false;submitToEditors()">确 定</el-button>
                                        </span>
                                    </el-dialog>
                                </div>
                            </div>
                        </div>
                        <el-dialog title="提示" :visible.sync="tipsDialogVisible" width="800px">
                            <div v-for="(item,index) in tipsStr" style="display:inline-block"><span>{{item}}<span v-if="index!=tipsStr.length-1">，</span></span></div>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="tipsDialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="tipsDialogVisible = false">确 定</el-button>
                            </span>
                        </el-dialog>
                        <div style="width:100%;"><span class="determinationAudit bgButton" @click="determinationAudit()" type="primary">确认提交</span></div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    </el-row>
    <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui'
import backToTheUpPage from '@/components/UiAssemblyList/backToTheUpPage'
import upDataButton from '@/components/UiAssemblyList/upDataButton'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import { someDataLongByJson } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      backToTheUpPage,
      upDataButton,
      upDataButtonByBox,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      purchaseName:[],
      search:'',
      Num:'',
      isWhole:true,
      isPart:false,
      d_isShow:false,
      dialogVisible: false,
      manipulate:'',
      spec_sn:'',
      demand_sn:'',
      discount_info:'',
      tableLength:'',
      index:'',
      purchase_sn:'',
      allot_num:'',
      goods_num:'',
      headersStr:'',
      backStr:'/waitAllotDemand',
      //后台消息确认弹框
      tipsDialogVisible:false,
      tipsStr:'',
      //上传
      upLoadDialogVisible:false,
      upDataStr:'确认上传',
      titleStr:'上传待分配表',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getUpPageData();
  },
  methods:{
      getUpPageData(){
            let vm=this;
            let tableDataOne=JSON.parse(sessionStorage.getItem("tableData"));
          
            if(tableDataOne=='1'){
                // vm.getUserNameData('',0);
                    axios.post(vm.url+vm.$demandAllotURL,
                        {
                            "demand_sn":vm.$route.query.demand_sn
                        },
                        {
                            headers:vm.headersStr,
                        }
                    ).then(function(res){
                        vm.tableData=res.data.data.goods_list;
                        vm.purchaseName=res.data.data.purchase_list;
                    }).catch(function (error) {
                        loa.close();
                        if(error.response.status!=''&&error.response.status=="401"){
                        vm.$message('登录过期,请重新登录!');
                        sessionStorage.setItem("token","");
                        vm.$router.push('/');
                        }
                    });
                    tableStyleByDataLength(vm.tableData.length,15);
                return false;
            };
            vm.tableData=tableDataOne.goods_list;
            vm.purchaseName=tableDataOne.purchase_list;
            tableStyleByDataLength(vm.tableData.length,15);
            sessionStorage.setItem("tableData",'1');
          
      },
      scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
      },
       //根据采购单号查找数据
        getUserNameData(purchase_sn,index){
            let vm=this;
            // vm.tableData='';
            let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
            vm.title=vm.$route.query.demand_sn;
            vm.index=index;
            vm.purchase_sn=purchase_sn;
            $(".purchaseName input[title="+index+"]").parent().addClass("xuanzhong")
            $(".purchaseName input[title!="+index+"]").parent().addClass("weixuanzhong")
            $(".purchaseName input[title="+index+"]").parent().removeClass("weixuanzhong")
            $(".purchaseName input[title!="+index+"]").parent().removeClass("xuanzhong")
            if(index==0){
                vm.isWhole=true;
                vm.isPart=false;
                vm.d_isShow=false;
                axios.post(vm.url+vm.$demandAllotURL,
                    {
                        "purchase_sn":purchase_sn,
                        "demand_sn":vm.$route.query.demand_sn,
                        'query_sn':vm.search,
                    },
                    {
                        headers:vm.headersStr,
                    }
                ).then(function(res){
                    loa.close();
                    if(res.data.code==1000){
                        vm.tableData=res.data.data.goods_list;
                        tableStyleByDataLength(vm.tableData.length,15);
                    }else{
                        vm.$message(res.data.msg);
                    }
                    
                }).catch(function (error) {
                    loa.close();
                    if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                    }
                });
            }else{
                vm.isWhole=false;
                vm.isPart=true;
                vm.d_isShow=true;
                 axios.post(vm.url+vm.$purchaseDemandAllotURL,
                {
                    "purchase_sn":purchase_sn,
                    "demand_sn":vm.$route.query.demand_sn,
                    'query_sn':vm.search,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                loa.close();
                if(res.data.code==1000){
                    vm.tableData=res.data.data;
                    let infoNum=[];
                    res.data.data.forEach(element=>{
                        infoNum.push(element.discount_info.length);
                    })
                    var j=infoNum[0];
                    for(var i=0;i<=infoNum.length;i++){
                            if(infoNum[i+1]>=j){
                                j=infoNum[i+1]
                            }
                    }
                    vm.discount_info=j;
                    tableStyleByDataLength(vm.tableData.length,15);
                }else{
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                loa.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
            }
        },
        // //动态高度
        // dynamicHeight(lenght){
        //     if(length>=15){
        //         $(".cc").css({
        //                     "height":"610px",
        //                     "width":"100.7%",
        //                 })
        //         $(".t_n").css("height","646px")
        //         $(".dd").css("height","610px")
        //     }else{
        //         $(".cc").css({
        //                     "height":"auto",
        //                     "width":"100%",
        //                 })
        //         $(".t_n").css("height","auto")
        //     }
        // },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("hh").scrollLeft=b;
        },
        //打开操作弹框
        operation(demand_sn,purchase_sn,spec_sn,allot_num,goods_num){
            let vm=this;
            axios.post(vm.url+vm.$editDemandAllotURL,
                {
                    "demand_sn":demand_sn,
                    "purchase_sn":purchase_sn,
                    "spec_sn":spec_sn
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                if(res.data.code==1000){
                    vm.dialogVisible = true;
                    vm.manipulate=res.data.data;
                    vm.spec_sn=spec_sn;
                    vm.demand_sn=demand_sn;
                    vm.purchase_sn=purchase_sn;
                    vm.allot_num=allot_num;
                    vm.goods_num=goods_num;
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
        //确定提交编辑
        submitToEditors(demand_sn,purchase_sn){
            let vm=this;
            let channel_discount=[];
            let index=0;
            let num=0;
            vm.manipulate.goods_discount_list.forEach(element => {
                let inVal=$(".inVal input[name="+index+"]").val();
                channel_discount.push({"channel_name":element.channel_name,"brand_discount":element.brand_discount,"may_num":inVal});
                index++;
                num=num+parseInt(inVal);
            });
            if(num>vm.goods_num){
                vm.$message('您提交的可采数总和大于可分配数!');
                return false;
            }
            axios.post(vm.url+vm.$doEditDemandAllotURL,
                {
                    "demand_sn":vm.demand_sn,
                    "purchase_sn":vm.purchase_sn,
                    "spec_sn":vm.spec_sn,
                    "channel_discount":channel_discount,
                    "goods_num":vm.goods_num,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                if(res.data.code==1000){
                    vm.dialogVisible = false;
                    let purchase_sn=vm.purchase_sn;
                    let index=vm.index;
                    vm.getUserNameData(purchase_sn,index);
                    vm.$message(res.data.msg);
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
        //确定审核
        determinationAudit(){
            let vm=this;
            let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
            axios.post(vm.url+vm.$doDemandAllotURL,
                {
                    "demand_sn":vm.$route.query.demand_sn,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                loa.close();
                if(res.data.code==1000){
                    vm.backToUpPage();
                    // vm.$router.push('/waitAllotDemand');
                }else{
                    vm.tipsDialogVisible=true;
                    let msg=res.data.msg.split(',')
                    vm.tipsStr=msg;
                }
            }).catch(function (error) {
                loa.close();
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        thWidth(){
            let vm=this;
            var lengthStr=310*vm.discount_info;
            return "width:"+lengthStr+"px";
        },
        tableWidth(){
            let vm=this;
            let labelwidth = someDataLongByJson(vm.tableData,'goods_label_list')*60
            var widthLength = 1560+(310*vm.discount_info)+labelwidth;
            if(widthLength<1530){
                widthLength=1530;
            }
            return "width:"+widthLength+"px"
        },
        wholeTable(){
            let vm=this;
            var wholeTable=1160+(250*vm.purchaseName.length);
            return "width:"+wholeTable+"px"
        },
        //返回上一页
        backToUpPage(){
            let vm = this;
            if(vm.$route.query.isAdjustment=='isAdjustment'){
                vm.$router.push('/alreadyAllotDemand');
            }else if(vm.$route.query.isWait=='isWait'){
                vm.$router.push('/waitAllotDemand');
            }
        },
        //下载待分配数据
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downLoadWaitAllotGoodsInfoURL+'?demand_sn='+vm.$route.query.demand_sn+'&purchase_sn='+vm.purchase_sn+'&token='+headersToken);
           
        },
        confirmShow(){
            $(".upDataButtonByBox_b").fadeIn();
        },
        //上传待分配数据
        GFFconfirmUpData(formDate){
            let vm=this;
            $(".upDataButtonByBox_b").fadeOut();
            let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
            $.ajax({
                url: vm.url+vm.$uploadWaitAllotGoodsInfoURL+'?demand_sn='+vm.$route.query.demand_sn+'&purchase_sn='+vm.purchase_sn,
                type: "POST",
                async: true,
                cache: false,
                headers:vm.headersStr,
                data: formDate,
                processData: false,
                contentType: false,
                success: function(res) {
                    loa.close();
                    if(res.code==1000){
                        $("#file").val('');
                        vm.fileName='';
                        vm.getUserNameData(vm.purchase_sn,vm.index);
                        vm.$message(res.msg);
                    }else{
                        vm.$message(res.msg);
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
        labelStyle(color){
            return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
        },
        labelWidth(){
            let vm = this;
            let width=someDataLongByJson(vm.tableData,'goods_label_list')*60;
            return "width:"+width+"px";
        },
  },
  computed:{
      backgroundColor(){
            let vm = this;
            return function(edit_status){
                if(edit_status==1){
                    return "color: #34e516";
                }else if(edit_status==0){
                    
                }
            }
        }
  }
}
</script>

<style>
.distributionPage_b .purchaseName span{
    margin-right: 20px;
}
.distributionPage_b .Serial{
    display: inline-block;
    border-radius: 50%;
    background-color: #4677c4;
    color: #fff;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
}
.distributionPage_b .determinationAudit{
    float: right;
    margin-top: 10px;
    margin-bottom: 10px;
    margin-right: 20px;
}
.xuanzhong{
    height: 31px !important;
    background-color: #4677c4 !important;
    font-size: 14px !important;
    padding-left: 15px !important;
    padding-right: 15px !important;
    display: inline-block !important;
    color: #fff !important;
    line-height: 31px !important;
    text-align: center !important;
    border-radius: 5px !important;
    cursor: pointer !important;
}
.weixuanzhong{
    height: 31px !important;
    background-color: #d8dce6 !important;
    font-size: 14px !important;
    padding-left: 15px !important;
    padding-right: 15px !important;
    display: inline-block !important;
    color: #000 !important;
    line-height: 31px !important;
    text-align: center !important;
    border-radius: 5px !important;
    cursor: pointer !important;
}
.distributionPage_b .spanWidth{
    display: inline-block;
    width:300px;
}
.distributionPage_b .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.distributionPage_b .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.distributionPage_b .ee{width:100%!important; width:100%; text-align: center;}
.distributionPage_b .t_i_h table{width:1530px;}
.distributionPage_b .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.distributionPage_b .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.distributionPage_b .cc table{width:1530px; }
.distributionPage_b .cc table td{ text-align:center}
.distributionPage_b .ellipsis span{
    line-height: 41px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.operation{
    width: 100px;
    height: 30px;
    line-height: 30px;
    border-radius: 10px;
    color: #fff;
    display: inline-block;
    background-color: #4677c4;
    cursor: pointer;
}

</style>
<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>
