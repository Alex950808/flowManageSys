<template>
  <div class="allocatedPage_b">
    <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="allocatedPage">
                        <el-row class="purchaseTitle listTitleStyle">
                            <el-col :span="24">
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购期需求汇总</span>
                                <input type="text" class="inputStyle" placeholder="请输入搜索关键字" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                            </el-col>
                        </el-row>
                        <div class="tableTitle">
                            <ul>
                                <li class="purchaseName">
                                    <span class="xuanzhong" @click="getUserNameData('',0)">全部<input type="text" name="0" style="display:none"/></span>
                                    <span class="weixuanzhong" v-for="(item,index) in purchaseName" @click="getUserNameData(item,index+1)">{{item}}<input type="text" :name="index+1" style="display:none"/></span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <div class="t_n">
                                <span style="border-top: 1px solid #000;">商品名称</span>
                            <div class="dd" id="dd">
                                <table cellpadding="0" cellspacing="0" border="0" class="t_number">
                                    <tbody v-if="isWhole">
                                        <tr v-for="(item,index) in  tableData" >
                                            <td class="ellipsis" style="width:200px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-if="isPart">
                                        <tr v-for="(item,index) in  tableData" >
                                            <td class="ellipsis" style="width:200px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                            <div class="t_i whole MB_twenty">
                                <div class="t_i_h" id="hh">
                                    <div class="ee">
                                        <table cellpadding="0" cellspacing="0" border="0" v-if="isWhole" :style='wholeTableWidth()'>
                                        <thead>
                                            <tr>
                                                <th style="width:150px">商品代码</th>
                                                <th style="width:150px">erp编码</th>
                                                <th style="width:150px">商品规格码</th>
                                                <th style="width:150px">商品需求数量</th>
                                                <th style="width:150px">待分配数量</th>
                                                <th style="width:150px">可采数</th>
                                                <th style="width:170px" v-for="(item,index) in purchaseName">{{item}}</th>
                                                
                                            </tr>
                                        </thead>
                                        </table>
                                        <table cellpadding="0" cellspacing="0" border="0" v-if="isPart" :style='tableWidth()'>
                                        <thead>
                                            <tr>
                                                <th width="180px">采购期单号</th>
                                                <th width="180px">需求单号</th>
                                                <th width="170px">商品代码</th>
                                                <th width="170px">商家编码</th>
                                                <th width="170px">商品规格码</th>
                                                <th width="100px">可分配数</th>
                                                <th width="100px">需求数</th>
                                                <th :width='thWidth()'>采购折扣</th>
                                                
                                            </tr>
                                        </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="cc" id="cc" @scroll="scrollEvent()">
                                    <table cellpadding="0" cellspacing="0" border="0" v-if="isWhole" :style='wholeTableWidth()'>
                                        <tr v-for="(item,index) in tableData">
                                            <td style="width:150px">{{item.erp_prd_no}}</td>
                                            <td style="width:150px">{{item.erp_merchant_no}}</td>
                                            <td style="width:150px">{{item.spec_sn}}</td>
                                            <td style="width:150px">{{item.goods_num}}</td>
                                            <td style="width:150px">{{item.diff_num}}</td>
                                            <td style="width:150px">{{item.total_may_num}}</td>
                                            <td style="width:170px" v-for="(itemO,index) in purchaseName">{{item[itemO]}}</td>
                                        </tr>
                                    </table>
                                    <table cellpadding="0" cellspacing="0" border="0" v-if="isPart" :style='tableWidth()'>
                                        <tr v-for="(item,index) in tableData">
                                            <td  width="180px">{{item.purchase_sn}}</td>
                                            <td  width="180px">{{item.demand_sn}}</td>
                                            <td  width="170px">{{item.erp_prd_no}}</td>
                                            <td  width="170px">{{item.erp_merchant_no}}</td>
                                            <td  width="170px">{{item.spec_sn}}</td>
                                            <td  width="100px">{{item.allot_num}}</td>
                                            <td  width="100px">{{item.goods_num}}</td>
                                            <td :width='thWidth()' style="text-align:left;">
                                                <span v-for="(itemO,index) in item.discount_info" class="spanWidth"><span class="Serial">{{index+1}}</span><span title="渠道名称">{{itemO.brand_channel}}&nbsp&nbsp&nbsp&nbsp</span> <span title="品牌折扣">{{itemO.brand_discount}}&nbsp&nbsp&nbsp&nbsp</span><span title="可采数量">{{itemO.may_num}}&nbsp&nbsp&nbsp&nbsp</span></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
export default {
  name: 'App',
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      purchaseName:[],
      search:'',
      Num:'',
      isWhole:true,
      isPart:false,
      e:'',
      dialogVisible: false,
      manipulate:'',
      spec_sn:'',
      demand_sn:'',
      purchase_sn:'',
      discount_info:'',
      //   may_num:'',
      headersStr:'',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getUpPageData();
  },
  methods:{
        getUpPageData(){
            let vm=this;
            let tableData=JSON.parse(sessionStorage.getItem("tableData"));
            if(tableData=='1'){
                vm.Allocated();
                return false;
            };
            vm.tableData=tableData.goods_list;
            vm.purchaseName=tableData.purchase_list;
            sessionStorage.setItem("tableData",'1');
        },
        //进入已分配列表
        Allocated(demand_sn){
            let vm = this;
            let headersToken=sessionStorage.getItem("token");
            axios.post(vm.url+vm.$demandAlreadyDetailURL,
                {
                    "demand_sn":vm.$route.query.demand_sn,
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.tableData=res.data.data.goods_list;
                vm.purchaseName=res.data.data.purchase_list;
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("hh").scrollLeft=b;
        },
       //根据采购单号查找数据
        getUserNameData(purchase_sn,index){
            let vm=this;
            vm.title=vm.$route.query.demand_sn;
            // vm.userId=id;
            $(".purchaseName input[name="+index+"]").parent().addClass("xuanzhong")
            $(".purchaseName input[name!="+index+"]").parent().addClass("weixuanzhong")
            $(".purchaseName input[name="+index+"]").parent().removeClass("weixuanzhong")
            $(".purchaseName input[name!="+index+"]").parent().removeClass("xuanzhong")
            if(index==0){
                vm.isWhole=true;
                vm.isPart=false;
                 axios.post(vm.url+vm.$demandAlreadyDetailURL,
                    {
                        // "purchase_sn":purchase_sn,
                        "demand_sn":vm.$route.query.demand_sn,
                        // 'query_sn':vm.search,
                    },
                    {
                        headers:vm.headersStr,
                    }
                ).then(function(res){
                    vm.tableData=res.data.data.goods_list;
                }).catch(function (error) {
                    if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                    }
                });
            }else{
                vm.isWhole=false;
                vm.isPart=true;
                 axios.post(vm.url+vm.$purchaseDemandAlreadyURL,
                {
                    "purchase_sn":purchase_sn,
                    "demand_sn":vm.$route.query.demand_sn,
                    'query_sn':vm.search,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
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
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
            }
        },
        thWidth(){
            let vm=this;
            var lengthStr=250*vm.discount_info;
            return lengthStr+"px";
        },
        tableWidth(){
            let vm=this;
            var widthLength=1160+(320*vm.discount_info);
            if(widthLength<1530){
                widthLength=1530;
            }
            return "width:"+widthLength+"px"
        },
        wholeTableWidth(){
            let vm=this;
            var widthLength=1000+(180*vm.purchaseName.length);
            return "width:"+widthLength+"px"
        },
        //搜索框
        searchFrame(){
            let vm=this;
            vm.Allocated();
            vm.isShow=false;
        },
  }
}
</script>

<style>
.allocatedPage_b .purchaseTitle{
    width: 100%;
    height: 100px;
    line-height: 70px;
    font-size: 18px;
    color: #000;
    font-weight: bold;
    position: relative;
}
.allocatedPage_b .purchaseTitle input{
    width: 200px;
    height: 20px;
    outline: none;
    -webkit-appearance: none;
    border-radius: 50px;
    -moz-border-radius: 50px;
    -webkit-border-radius: 50px;
    padding-left: 30px;
    margin-left: 16px;
}
.allocatedPage_b .Serial{
    display: inline-block;
    border-radius: 50%;
    background-color: #00C1DE;
    color: #fff;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
}
.allocatedPage_b .spanWidth{
    display: inline-block;
    width:300px;
}
.allocatedPage_b .purchaseTitle .search{
    position: absolute;
    left: 170px;
    top: 29px;
    color: #ccc;
}
.allocatedPage_b li{
    list-style: none;
}
.allocatedPage_b .purchaseName span{
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
    background-color: #ccc !important;
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
.allocatedPage_b .t_n{width:19%; height:695px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.allocatedPage_b .t_n span{display:block; text-align:center; line-height:35px}
.allocatedPage_b .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.allocatedPage_b .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.allocatedPage_b .dd{height:658px!important; height:658px}
.allocatedPage_b .t_i{width:80%; height:auto; float:left; border-right:1px solid #ccc;border-left: 1px solid #ccc;border-top:1px solid #ccc}
.allocatedPage_b .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.allocatedPage_b .ee{width:110%!important; width:110%; text-align: center;height:35px;line-height: 35px;}
.allocatedPage_b .t_i_h table{width:1630px;}
.allocatedPage_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.allocatedPage_b .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.allocatedPage_b .cc table{width:1630px; }
.allocatedPage_b .cc table td{height:35px; border-bottom:1px solid #ccc; text-align:center}
.allocatedPage_b .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
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
    background-color: #00C1DE;
    cursor: pointer;
}
</style>
<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>
