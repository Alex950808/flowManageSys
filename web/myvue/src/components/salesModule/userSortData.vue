<template>
  <div class="userSortData">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span class="bgButton MR_twenty" @click="backToUpPage()">返回上一页</span>
                        <span class="coarseLine MR_ten"></span>用户分货详情
                        <span class="ML_twenty">分货单号:{{sortData.sort_sn}}</span>
                        <span v-if="isNotFound" class="bgButton MT_twenty floatRight MR_twenty" @click="openPricing()">生成用户分货数据</span>
                    </div>
                    <div>
                        <div v-if="isShow" class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th style="width:300px;">商品名称</th>
                                                <th style="width:150px">商品规格码</th>
                                                <th style="width:100px">用户需求总量</th>
                                                <th style="width:100px">可分配数量</th>
                                                <th style="width:130px">销售用户</th>
                                                <th style="width:130px">手动分配数量</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()"> 
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr v-for="(item,index) in sortData.userSortData" :style="backgroundColor(item.spec_sn,index)">
                                        <td class="overOneLinesHeid fontLift" :style="mergeDisplay(item.goods_name)" :rowspan="mergeOrder(item.spec_sn,index)" style="width:300px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span class="ML_ten">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td :style="mergeDisplay(item.goods_name)" :rowspan="mergeOrder(item.spec_sn,index)" style="width:150px">{{item.spec_sn}}</td>
                                        <td :style="mergeDisplay(item.goods_name)" :rowspan="mergeOrder(item.spec_sn,index)" style="width:100px">{{item.total_num}}</td>
                                        <td :style="mergeDisplay(item.goods_name)" :rowspan="mergeOrder(item.spec_sn,index)" style="width:100px">{{item.can_sort_num}}</td>
                                        <td style="width:130px">{{item.user_name}}</td>
                                        <td style="width:130px" class="handle_num_two">
                                            <input type="text" class="widthOneHundred" :name="index" :value="item.handle_num" @change="modifiedValueByInput(item.depart_id,item.spec_sn,item.sort_sn,item.sale_user_id,index)"/>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <notFound v-if="isNotFound"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import {tableStyleByDataLength} from '@/filters/publicMethods.js'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
  components:{
      notFound,
      tableTitle,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      sortData:[],
      isShow:true,
      isNotFound:false,
      tableTitle:'订单分货数据',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/dpmDivisionGoods?purchase_sn='+this.$route.query.purchase_sn+'&real_purchase_sn='+this.$route.query.real_purchase_sn+'"}]',
      contentStr:'请您确认是否要生成用户分货数据！',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getlistData();
    // this.mergeOrder();
  },
  methods:{
    getlistData(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$getUserSortDataURL+"?depart_id="+vm.$route.query.depart_id+"&sort_sn="+vm.$route.query.sort_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.userSortData.length!=0){
                vm.sortData=res.data;
                vm.isShow=true;
                vm.isNotFound=false;
                tableStyleByDataLength(vm.sortData.userSortData.length,15);
                
            }else{
                vm.isShow=false;
                vm.isNotFound=true;
            }
        }).catch(function (error) {
            load.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    // mergeOrder(){
    //     let vm = this;
    //     for(let i=0;i<vm.sortData.userSortData.length;i++){
    //         if(vm.sortData.userSortData[i].spec_sn==vm.sortData.userSortData[i+1].spec_sn){
    //             $(".order"+i+" .goods_name").attr("rowspan",2);
    //             $(".order"+i+1+" .goods_name").remove();
    //         }
    //     }
    // },
    modifiedValueByInput(depart_id,spec_sn,sort_sn,sale_user_id,index){
        let vm=this;
        let handleNum=$(".handle_num_two input[name="+index+"]").val();
        axios.post(vm.url+vm.$userHandleGoodsURL,
            {
                "depart_id":depart_id,
                "sort_sn":sort_sn,
                "spec_sn":spec_sn,
                "sale_user_id":sale_user_id,
                "handle_num":handleNum,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==2038){
                vm.$message(res.data.msg);
                vm.getlistData();
            }else{
                vm.$message(res.data.msg);
                vm.getlistData();
            }
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
    backToUpPage(){
        let vm = this;
        vm.$router.push("/dpmDivisionGoods?purchase_sn="+this.$route.query.purchase_sn+"&real_purchase_sn="+this.$route.query.real_purchase_sn+"&query_type="+vm.$route.query.query_type);
    },
    //打开生成数据弹出框
    openPricing(){
        $(".confirmPopup_b").fadeIn();
    },
    //取消生成数据
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    confirmationAudit(){
         let vm = this;
        vm.determineIsNo();
        axios.post(vm.url+vm.$generalUserSortDataURL,
            {
                "purchase_sn":vm.$route.query.purchase_sn,
                "sort_sn":vm.$route.query.sort_sn,
                "depart_id":vm.$route.query.depart_id,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
                vm.getGoodsData();
                if(res.data.code=='2065'){
                    vm.$message(res.data.msg);
                }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    }
  },
  computed:{
    mergeOrder(){
        let vm = this;
        return function(spec_sn,index){
            if((index+2)>=vm.sortData.userSortData.length){
                return false
            }else{
                let twoGoods = spec_sn==vm.sortData.userSortData[index+1].spec_sn&&vm.sortData.userSortData[index+1].spec_sn!=vm.sortData.userSortData[index+2].spec_sn;
                let threeGoods = spec_sn==vm.sortData.userSortData[index+1].spec_sn&&vm.sortData.userSortData[index+1].spec_sn==vm.sortData.userSortData[index+2].spec_sn;
                if(twoGoods){
                    vm.sortData.userSortData[index+1].goods_name='display';
                    return 2
                }else if(threeGoods){
                    vm.sortData.userSortData[index+1].goods_name='display';
                    return 3
                }else{
                    return ""
                }
            }  
        }
    },
    backgroundColor(){
        let vm = this;
        return function(spec_sn,index){
            if((index+1)==vm.sortData.userSortData.length){
                return false
            }else{
                let top = spec_sn==vm.sortData.userSortData[index+1].spec_sn;
                let bottom = false;
                if((index-1)>=0){
                    bottom = spec_sn==vm.sortData.userSortData[index-1].spec_sn;
                }
                if(top){
                    return "background-color:rgb(0, 172, 77);color:#fff";
                }else if(bottom){
                    return "background-color:rgb(0, 172, 77);color:#fff";
                }
                else{
                    return ""
                }
            }
        }
    },
    mergeDisplay(){
        return function(goods_name){
            if(goods_name=='display'){
                return "display: none;"
            }
        }
    }
  }
}
</script>

<style scoped lang=less>
table{
    width: 100%;
    border: 1px solid #ebeef5;
    text-align: center;
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
<style>
.userSortData .t_i{width:100%; height:auto;}
.userSortData .t_i_h{width:100%; overflow-x:hidden;}
.userSortData .ee{width:100%!important; text-align: center;}
.userSortData .t_i_h table{width:100%;}
.userSortData .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.userSortData .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.userSortData .cc table{width:100%; }
.userSortData .cc table td{height:25px; text-align:center}
</style>

