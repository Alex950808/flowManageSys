<template>
  <div class="purchaseDemandDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle select">
                        <span @click="backUpPage()" class="bgButton">返回上一级</span>
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;{{titleStr}}</span>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                        <el-col :span="2">
                            <span @click="backUpPage()" class="bgButton">返回上一级</span>
                        </el-col>
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>{{titleStr}}</span></div></el-col>
                        <el-col :span="2"><div>商品名称</div></el-col>
                        <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                        <el-col :span="2"><div>商品规格码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                        <el-col :span="2"><div>商家编码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                        <el-col :span="3"><span class="bgButton" @click="searchFrame()">搜索</span></el-col>
                    </el-row>
                    <el-row class="t_i fontCenter">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th style="width:200px">商品名称</th>
                                            <th style="width:160px">商品规格码</th>
                                            <th style="width:160px">商家编码</th>
                                            <th style="width:180px">商品代码</th>
                                            <th style="width:130px">需求数</th>
                                            <th style="width:130px">实采数</th>
                                            <th style="width:130px">缺口数</th>
                                            <th style="width:130px">状态</th>
                                            <th class="widthOneHundred">标记延期商品</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()"> 
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr v-for="(item,index) in tableData">
                                    <td class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                        <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </td>
                                    <td style="width:160px">{{item.spec_sn}}</td>
                                    <td style="width:160px">{{item.erp_merchant_no}}</td>
                                    <td style="width:180px">{{item.erp_prd_no}}</td>
                                    <td style="width:130px">{{item.goods_num}}</td>
                                    <td style="width:130px">{{item.yet_num}}</td>
                                    <td style="width:130px">{{item.diff_num}}</td>
                                    <td style="width:130px">
                                        <span v-if="item.is_postpone==1">已延期</span>
                                        <span v-if="item.is_postpone==2">未延期</span>
                                    </td>
                                    <td class="widthOneHundred">
                                        <span @click="markGoodsPostpone(item.spec_sn,item.is_postpone,index)">
                                            <input type="hidden" :name="index"/>
                                            <i class="iconfont Cursor" v-show="item.is_postpone==1" style="color:red" title="标记商品延期">&#xe60e;</i>
                                            <i class="iconfont Cursor" v-show="item.is_postpone==2" title="标记商品延期期">&#xe60e;</i>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
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
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      reTableData:[],
      isShow:false,
      isDelay:false,
      titleStr:'',
      //搜索字段
      goods_name:'',
      spec_sn:'',
      erp_merchant_no:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        let data;
        if(vm.$route.query.Delay=="Delay"){
            vm.titleStr='采购任务下的需求单详情'
            vm.isDelay=true;
            data = "?demand_sn="+vm.$route.query.demand_sn+"&sum_demand_sn="+vm.$route.query.sum_demand_sn
        }else if(vm.$route.query.demand=="demand"){
            vm.titleStr='采购需求详情'
            data = "?demand_sn="+vm.$route.query.demand_sn
        }
        axios.get(vm.url+vm.$purchaseDemandDetailURL+data,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code=="1000"){
                vm.tableData=res.data.data;
                vm.reTableData=res.data.data;
                tableStyleByDataLength(vm.tableData.length,15);
                vm.isShow=false;
            }else if(res.data.code=="1002"){
                vm.isShow=true;
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
    //标记延期商品
    markGoodsPostpone(spec_sn,is_postpone,index){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        let is_Postpone;
        if(is_postpone==1){//已标记
            if(vm.$route.query.Delay=="Delay"){
                vm.$message('合单中不允许取消操作');
                return false;
            }
            is_Postpone=2;
        }else if(is_postpone==2){//未标记
            if(vm.$route.query.demand=="demand"){
                vm.$message('需求单中不允许标记延期');
                return false;
            }
            is_Postpone=1;
        }
        vm.tableData[index].is_postpone=is_Postpone; 
        let data;
        if(vm.$route.query.Delay=="Delay"){
            data = {"demand_sn":vm.$route.query.demand_sn,"sum_demand_sn":vm.$route.query.sum_demand_sn,"spec_sn":spec_sn,"is_postpone":is_Postpone};
        }else if(vm.$route.query.demand=="demand"){
            data = {"demand_sn":vm.$route.query.demand_sn,"spec_sn":spec_sn,"is_postpone":is_Postpone};
        }
        axios.post(vm.url+vm.$markGoodsPostponeURL,data,
            // {
            //     "demand_sn":vm.$route.query.demand_sn,
            //     "spec_sn":spec_sn,
            //     "is_postpone":is_Postpone,
            //     "sum_demand_sn":vm.$route.query.sum_demand_sn,
            // },
            {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
            }
        ).then(function(res){
            if(res.data.code=='1000'){
                // let isPostpone = [];
                // vm.tableData.forEach(element=>{
                //     isPostpone.push(element.is_mark);
                // })
                // isPostpone=uniq(isPostpone);
                // if(isPostpone.length==2){
                //     vm.is_Postpone=true;
                // }else if(isPostpone.length==1){
                //     if(isPostpone[0]==0){
                //         vm.is_Postpone=false;
                //     }else if(isPostpone.length==0){
                //         vm.is_Postpone=true;
                //     } 
                // }                                      
            }else{
                vm.tableData[index].is_postpone=is_postpone;
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
    backUpPage(){
        let vm = this;
        if(vm.$route.query.Delay=='Delay'){
            vm.$router.push('/purchaseTaskList');
        }else if(vm.$route.query.demand=='demand'){
            vm.$router.push('/purchaseDemandList');
        }
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    //搜索
    searchFrame(){
        let vm = this;
        // let tableData = vm.tableData;
        vm.tableData=[];
        if(vm.goods_name!=''){
            vm.reTableData.forEach(element => {
                    console.log(element.goods_name.indexOf(vm.goods_name)!=-1)
                if(element.goods_name.indexOf(vm.goods_name)!=-1){
                    vm.tableData.push(element)
                }
            });
        }else if(vm.spec_sn!=''){
            vm.reTableData.forEach(element => {
                if(element.spec_sn.indexOf(vm.spec_sn)!=-1){
                    vm.tableData.push(element)
                }
            });
        }else if(vm.erp_merchant_no!=''){
            vm.reTableData.forEach(element => {
                if(element.erp_merchant_no.indexOf(vm.erp_merchant_no)!=-1){
                    vm.tableData.push(element)
                }
            });
        }else{
            vm.tableData=vm.reTableData;
        }
        console.log(vm.tableData)
    }
  }
}
</script>

<style scoped>
.purchaseDemandDetail .t_i{width:100%; height:auto;}
.purchaseDemandDetail .t_i_h{width:100%; overflow-x:hidden;}
.purchaseDemandDetail .ee{width:100%!important; text-align: center;}
.purchaseDemandDetail .t_i_h table{width:100%;}
.purchaseDemandDetail .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.purchaseDemandDetail .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.purchaseDemandDetail .cc table{width:100%; }
.purchaseDemandDetail .cc table td{height:25px; text-align:center}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
