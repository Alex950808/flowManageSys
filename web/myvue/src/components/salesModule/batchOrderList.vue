<template>
  <div class="batchOrderList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;"> 
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="purchaseDiscount listTitleStyle">
                        <span><span class="coarseLine MR_twenty"></span>批次单分货列表</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>
                    </div>
                    <table class="tableTitle MB_twenty">
                        <thead>
                            <th width='10%'>实采单号</th>
                            <th width='10%'>自提/邮寄</th>
                            <th width='10%'>实际批次提货日</th>
                            <!-- <th width='10%'>实采单状态</th> -->
                            <th width='10%'>是否分完</th>
                            <th width='14%'>创建时间</th>
                            <th width='10%'>操作</th>
                        </thead>
                    </table>
                    <table class="tableTitleTwo MB_twenty">
                        <thead>
                            <th width='10%'>实采单号</th>
                            <th width='10%'>自提/邮寄</th>
                            <th width='10%'>实际批次提货日</th>
                            <!-- <th width='10%'>实采单状态</th> -->
                            <th width='10%'>是否分完</th>
                            <th width='14%'>创建时间</th>
                            <th width='10%'>操作</th>
                        </thead>
                    </table>
                    <el-row class="content PT_ten PB_ten MB_twenty" v-for="(item,index) in tableData" :key="index">
                        <div class="lineHeightForty border_B">
                            <span class="number">采购期期数：</span>
                            <span class="stage">{{item.purchase_info.id}}</span>
                            <span class="number ML_twenty">合单号：</span>
                            <span class="stage">{{item.purchase_info.sum_demand_sn}}</span>
                            <span class="number ML_twenty">创建时间：</span>
                            <span class="stage">{{item.purchase_info.create_time}}</span>
                            <!-- <span class="bgButton floatRight ML_twenty"
                                v-if="item.countSortData==0" @click="openViewBox('1',item.purchase_info.sum_demand_sn);">
                                生成合单分货数据
                            </span>  -->
                            <span class="blueFont Cursor floatRight" 
                                v-if="item.countSortData>0" @click="seeDetail(item.purchase_info.sum_demand_sn)">
                                查看合单分货数据
                            </span>
                        </div>
                        <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                            <tr v-for="itemO in item.real_data">
                                <td width='10%'>{{itemO.real_purchase_sn}}</td>                                 
                                <td width='10%'>{{itemO.path_way}}</td>
                                <td width='10%'>{{itemO.delivery_time}}</td>
                                <!-- <td width='10%'>{{itemO.status_desc}}</td> -->
                                <td width='10%'>
                                    <span v-if="itemO.canSortNum>0">未分完</span>
                                    <span v-if="itemO.canSortNum===0">已分完</span>
                                    <span v-else></span>
                                </td>
                                <td width='14%'>{{itemO.create_time}}</td>                                    
                                <td width='10%'>
                                    <!-- 已分货之后才能查看分货数据 -->
                                    <i class="blueFont Cursor NotStyleForI" 
                                        v-if="item.countSortData>0&&itemO.batchSortCount>0" @click="seePiciDetail(item.purchase_info.sum_demand_sn,itemO.real_purchase_sn)">
                                        查看批次单分货数据
                                    </i>
                                    <span class="bgButton" 
                                        v-if="item.countSortData>0&&itemO.batchSortCount==0" @click="openViewBox('2',item.purchase_info.sum_demand_sn,itemO.real_purchase_sn)">
                                        生成批次单分货数据
                                    </span>
                                </td> 
                            </tr>   
                        </table> 
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '../UiAssemblyList/searchBox';
import notFound from '@/components/UiAssemblyList/notFound';
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
  components:{
      notFound,
      searchBox,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      search:'',
      isShow:false,
      sum_demand_sn:'',
      contentStr:"",
      real_purchase_sn:'',
      index:'',
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
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
        axios.get(vm.url+vm.$batchOrderListURL+"?keywords="+vm.search+"&page="+vm.page+"&page_size="+vm.pagesize,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.total_num!=0){
                vm.tableData=res.data.batchList;
                vm.total=res.data.total_num;
                vm.isShow=false;
            }else{
                vm.tableData=[];
                vm.isShow=true;
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
    //搜索框
    searchFrame(e){
        let vm=this;
        vm.search=e;
        vm.page=1;
        vm.getDataList();
    },
    tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=250;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".tableTitleTwo").addClass("addclass");
                $(".tableTitleTwo").width($(".purchaseDiscount").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".tableTitleTwo").removeClass("addclass");
            }
        })
    },
    //打开商品部对部门分货-生成分货数据弹框
    openViewBox(index,sum_demand_sn,real_purchase_sn){
        let vm = this;
        vm.sum_demand_sn=sum_demand_sn;
        vm.real_purchase_sn=real_purchase_sn;
        vm.index=index;
        $(".confirmPopup_b").fadeIn()
        if(index=='1'){
            vm.contentStr = "请您确认是否要生成合单分货数据？"
        }else if(index=='2'){
            vm.contentStr = "请您确认是否要生成批次单分货数据？"
        }
    },
    //确认弹框否 
    determineIsNo(){
        $(".confirmPopup_b").fadeOut()
    },
    //商品部对部门分货-生成分货数据
    confirmationAudit(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        $(".confirmPopup_b").fadeOut();
        let url;
        let data;
        if(vm.index=='1'){//生成合单分货数据
            url = vm.$generalSortDataURL;
            data = {"sum_demand_sn":vm.sum_demand_sn};
        }else if(vm.index=='2'){//生成批次单分货数据
            url = vm.$makeBatchOrdSortDataURL;
            data = {"sum_demand_sn":vm.sum_demand_sn,"real_purchase_sn":vm.real_purchase_sn};
        }
        axios.post(vm.url+url,data,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code=='2024'){ 
                vm.getDataList();
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
    seeDetail(sum_demand_sn){
        let vm = this;            
        vm.$router.push('/sumSortDataList?sum_demand_sn='+sum_demand_sn);
    },
    //查看批次数据详情 
    seePiciDetail(sum_demand_sn,real_purchase_sn){
        let vm = this;            
        vm.$router.push('/batchOrdSortData?sum_demand_sn='+sum_demand_sn+'&real_purchase_sn='+real_purchase_sn);
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
.batchOrderList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.batchOrderList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.batchOrderList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.batchOrderList .content{
    border:1px solid #d9e3f3;
    -webkit-border-radius: 10px;
    padding-left: 10px;
    padding-right: 10px;
}
</style>
