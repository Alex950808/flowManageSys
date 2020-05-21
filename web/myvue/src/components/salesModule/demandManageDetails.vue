<template>
  <div class="demandManageDetails_b">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="demandManageDetails">
                        <div class="title listTitleStyle">
                            <span @click="backUpPage()" class="bgButton">返回上一级</span>
                            <span><span class="coarseLine ML_ten MR_ten"></span>{{`${this.$route.query.demand_sn}`}}_需求详情</span>
                            <span v-if="is_Mark" @click="back()" class="bgButton NotStyleForI">去预采</span>
                        </div>
                        <el-row style="margin-bottom: 10px;">
                            <span class="fontWeight F_S_twenty">需求详情：</span>
                        </el-row>
                        <div style="margin-bottom: 10px;">
                            <el-row class="L_H_F grayFont">
                                <el-col :span="5" :offset="1"><div><span>用户:{{titleData.user_name}}</span></div></el-col>
                                <el-col :span="5" :offset="1"><div><span>最低利润:{{titleData.min_profit}}</span></div></el-col>
                                <el-col :span="5" :offset="1"><div><span>创建时间:{{titleData.create_time}}</span></div></el-col>
                                <el-col :span="5" :offset="1"><div><span>采购截止日期:{{titleData.expire_time}}</span></div></el-col>
                            </el-row>
                        </div>
                        <div class="MB_ten">
                            <span class="fontWeight F_S_twenty">商品信息：</span>
                        </div>
                        <el-row>
                            <div class="t_i">
                                <div class="t_i_h" id="hh">
                                    <div class="ee">
                                        <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                            <thead>
                                            <tr>
                                                <th style="width:300px;">商品名称</th>
                                                <th class="widthOneFiveHundred">商品代码</th>
                                                <th class="widthOneFiveHundred">商家编码</th>
                                                <th class="widthOneFiveHundred">商品规格码</th>
                                                <th class="widthOneHundred">商品总需求量</th>
                                                <th class="widthOneHundred">运营毛利</th>
                                                <th class="widthOneHundred">销售折扣</th>
                                                <th class="widthOneHundred">标记预采</th>
                                                <!-- <th v-if="isDelay" class="widthOneHundred">标记延期商品</th> -->
                                                <th class="widthOneHundred" v-for="item in tableData.arrCharge">
                                                    <span v-for="(key,value) in item">{{key}}({{value}})</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="cc" id="cc" @scroll="scrollEvent()">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                        <tr v-for="(item,index) in tableData.demandGoodsInfo">
                                            <td class="overOneLinesHeid fontLift" style="width:300px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;width:300px;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td class="widthOneFiveHundred">{{item.erp_prd_no}}</td>
                                            <td class="widthOneFiveHundred">{{item.erp_merchant_no}}</td>
                                            <td class="widthOneFiveHundred">{{item.spec_sn}}</td>
                                            <td class="widthOneHundred">{{item.goods_num}}</td>
                                            <td class="widthOneHundred" v-if="item.runMarRate>tableData.demOrdInfo.min_profit">{{item.runMarRate}}</td>
                                            <td class="widthOneHundred" v-if="item.runMarRate<=tableData.demOrdInfo.min_profit"><span style="color:red;">{{item.runMarRate}}</span></td>
                                            <td class="widthOneHundred"><span>{{item.sale_discount}}</span><i class="el-icon-edit" title="修改销售折扣" @click="dialogVisible = true;openFrame(item.sale_discount,item.spec_sn,index)"></i></td>
                                            <td class="widthOneHundred">
                                                <span @click="demandGoodsMark(item.spec_sn,item.is_mark,index)">
                                                    <input type="hidden" :name="index"/>
                                                    <i class="iconfont Cursor" v-show="item.is_mark==1" style="color:red" title="标记预采">&#xe60e;</i>
                                                    <i class="iconfont Cursor" v-show="item.is_mark==0" title="标记预采">&#xe60e;</i>
                                                </span>
                                            </td>
                                            <td class="widthOneHundred" v-for="itemO in item.arrChargeRate"><span v-for="(key,value) in itemO">{{key}}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </el-row>
                        <el-dialog title="修改销售折扣" :visible.sync="dialogVisible" width="600px">
                            <el-row>
                                <el-col :span="3" :offset="3"><div class="grid-content bg-purple">销售折扣</div></el-col>
                                <el-col :span="8"><div class="grid-content bg-purple-light"><el-input v-model="saleMarRate" placeholder="请输入内容"></el-input></div></el-col>
                                <el-col :span="8" :offset="1">
                                    <div class="grid-content bg-purple">
                                        <template>
                                            <el-radio-group v-model="radio">
                                                <el-radio :label="1">更新</el-radio>
                                                <el-radio :label="2">不更新</el-radio>
                                            </el-radio-group>
                                        </template>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;confirmSubmit()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                    </div>
                </el-col>
            </el-row>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
import { bgHeight } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { uniq } from '@/filters/publicMethods.js'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
      fontWatermark,
    },
  data(){
    return{
        url: `${this.$baseUrl}`,
        tableData:[],
        titleData:'',//title数据
        search:'',//用户输入数据
        total:0,//页数默认为0
        saleMarRate:'',
        spec_sn:'',
        radio:2,
        index:'',
        Cost:'',
        isShow:false,
        dialogVisible:false,
        len:'',
        is_Mark:false,
        //标记延期
        isDelay:false,
        is_Postpone:'',
    }
  },
  mounted(){
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDatailsData();
  },
  methods:{
    //获取列表数据 
    getDatailsData(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        if(vm.$route.query.Delay=="Delay"){
            vm.isDelay=true;
        }
        axios.get(vm.url+vm.$queryDemDetailURL+"?demand_sn="+vm.$route.query.demand_sn,
            {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
            }
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data;
            vm.len = vm.tableData.arrCharge.length;
            vm.titleData=res.data.demOrdInfo;
            let isMark = [];
            vm.tableData.demandGoodsInfo.forEach(element=>{
                isMark.push(element.is_mark);
            })
            isMark=uniq(isMark);
            if(isMark.length==2){
                vm.is_Mark=true;
            }else if(isMark.length==1){
                if(isMark[0]==0){
                    vm.is_Mark=false;
                }else if(isMark.length==0){
                    vm.is_Mark=true;
                }
            }
            tableStyleByDataLength(vm.tableData.demandGoodsInfo.length,15);
        }).catch(function (error) {
                vm.loading.close();
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    openFrame(saleMarRate,spec_sn,index){
        let vm=this;
        vm.saleMarRate=saleMarRate;
        vm.spec_sn=spec_sn;
        vm.index=index;
    },
    //确认提交
    confirmSubmit(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        axios.post(vm.url+vm.$updateSaleRateURL,
            {
                "demand_sn":vm.$route.query.demand_sn,
                "spec_sn":vm.spec_sn,
                "sale_discount":vm.saleMarRate,
                "is_change":vm.radio,
            },
            {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
            }
        ).then(function(res){
            if(res.data.code!=2023){
                vm.tableData.demandGoodsInfo.forEach(element => {
                    if(vm.spec_sn==element.spec_sn){
                        vm.tableData.demandGoodsInfo.splice(vm.index,1,res.data.goodsInfo); 
                    }
                });
            }
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //标记预采
    demandGoodsMark(spec_sn,is_mark,index){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        let isMark;
        if(is_mark==1){
            isMark=0;
        }else if(is_mark==0){
            isMark=1;
        }
        vm.tableData.demandGoodsInfo[index].is_mark=isMark; 
        axios.get(vm.url+vm.$demandGoodsMarkURL+"?demand_sn="+vm.$route.query.demand_sn+"&spec_sn="+spec_sn+"&is_mark="+isMark,
            {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
            }
        ).then(function(res){
            if(res.data.code==2024){
                let isMark = [];
                vm.tableData.demandGoodsInfo.forEach(element=>{
                    isMark.push(element.is_mark);
                })
                isMark=uniq(isMark);
                if(isMark.length==2){
                    vm.is_Mark=true;
                }else if(isMark.length==1){
                    if(isMark[0]==0){
                        vm.is_Mark=false;
                    }else if(isMark.length==0){
                        vm.is_Mark=true;
                    }
                }                                      
            }else{
                vm.tableData.demandGoodsInfo[index].is_mark=is_mark;
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //标记延期商品
    markGoodsPostpone(spec_sn,is_postpone,index){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        let is_Postpone;
        if(is_postpone==1){
            is_Postpone=2;
        }else if(is_postpone==2){
            is_Postpone=1;
        }
        vm.tableData.demandGoodsInfo[index].is_postpone=is_Postpone; 
        axios.post(vm.url+vm.$markGoodsPostponeURL,
            {
                "demand_sn":vm.$route.query.demand_sn,
                "spec_sn":spec_sn,
                "is_postpone":is_Postpone,
                "sum_demand_sn":vm.$route.query.sum_demand_sn,
            },
            {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
            }
        ).then(function(res){
            if(res.data.code=='1000'){
                let isPostpone = [];
                vm.tableData.demandGoodsInfo.forEach(element=>{
                    isPostpone.push(element.is_mark);
                })
                isPostpone=uniq(isPostpone);
                if(isPostpone.length==2){
                    vm.is_Postpone=true;
                }else if(isPostpone.length==1){
                    if(isPostpone[0]==0){
                        vm.is_Postpone=false;
                    }else if(isPostpone.length==0){
                        vm.is_Postpone=true;
                    }
                }                                      
            }else{
                vm.tableData.demandGoodsInfo[index].is_postpone=is_postpone;
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    back(){
        let vm = this;
        vm.$router.push('/predictDemandList?keywords=' + vm.$route.query.demand_sn);
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    backUpPage(){
        let vm = this;
        if(vm.$route.query.Delay=='Delay'){
            vm.$router.push('/purchaseTaskList');
        }else if(vm.$route.query.demand=='demand'){
            vm.$router.push('/misDemandList');
        }
    }
  },
  computed:{
    tableWidth(){
        let vm = this
        let width = vm.len*100+1400;
        $(".demandManageDetails").width()
        let titWidth = $(".demandManageDetails").width();
        if(titWidth>width){
            width=titWidth
        }
        return "width:"+width+"px";
    },
  }
}
</script>

<style>
.demandManageDetails_b .title{
    margin-bottom: 30px;
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    font-weight:bold;
    padding-left: 35px;
    margin-top: 20px;
}
.demandManageDetails_b .back{
    display: inline-block;
    width: 130px;
    height: 50px;
    background-color: #4677c4;
    color: #fff;
    line-height: 50px;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
}
.demandManageDetails_b .ellipsis span{
    /* width: 350px; */
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.demandManageDetails_b .el-icon-edit{
    float: right;
    margin-top: 10px;
}
.demandManageDetails_b .bg-purple{
    line-height: 40px;
}
.demandManageDetails_b .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.demandManageDetails_b .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.demandManageDetails_b .ee{width:100%!important; width:100%; text-align: center;}
.demandManageDetails_b .t_i_h table{width:1530px;}
.demandManageDetails_b .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.demandManageDetails_b .cc{width:100.6%; border-bottom:1px solid #ebeef5; overflow:auto;margin-bottom: 35px;}
.demandManageDetails_b .cc table{width:1530px; }
.demandManageDetails_b .cc table td{ text-align:center}

</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
