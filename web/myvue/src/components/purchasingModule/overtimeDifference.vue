<template>
  <div class="overtimeDifference_b">
    <!-- 超时差异处理页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="overtimeDifference bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="purchaseDiscount listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;超时差异处理</span><input class="inputStyle" placeholder="请输入搜索关键字" type="text" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                        </div>
                        <div class="content" v-for="item in tableData">
                            <div class="content_text">
                                <!-- <span class="stage">{{item.id}}期</span><span class="number">{{item.purchase_sn}}</span> -->
                                <span v-if="item.title_info.purchase_id!=null" class="stage">{{item.title_info.purchase_id}}期</span>
                                <span v-if="item.title_info.purchase_sn!=null" class="stage ML_twenty">汇总需求单号：{{item.title_info.purchase_sn}}</span>
                                <span v-if="item.title_info.sum_demand_name!=null" class="stage ML_twenty">合期名称：{{item.title_info.sum_demand_name}}</span>
                                <span v-if="item.title_info.delivery_time!=null" class="stage ML_twenty">提货日期：{{item.title_info.delivery_time}}</span>
                                <span class="status" @click="goSummaryPage(item.purchase_sn)"><i class="el-icon-view"></i>&nbsp;&nbsp;查看数据汇总</span>
                            </div>
                            <el-row>
                                <el-col :span="12">
                                    <table class="table-one">
                                        <tr>
                                            <td>实采总数({{item.title_info.real_buy_num}})</td>
                                            <td>自提数量({{item.title_info.zt_num}}批({{item.title_info.zt_goods_num}}件))</td>
                                            <td>邮寄数量({{item.title_info.yj_num}}批({{item.title_info.yj_goods_num}}件))</td>
                                        </tr>
                                    </table>
                                </el-col>
                                <el-col :span="4" :offset="8">
                                </el-col>
                            </el-row>
                            <table class="table-two" v-for="realList in item.real_list">
                                <tr v-for="(real,index) in realList">
                                    <td class="widthThreeHundred">{{real.real_purchase_sn}}</td>
                                    <td class="widthOneFiveHundred">{{real.total_buy_num}}件</td>
                                    <td class="widthOneFiveHundred">{{real.port_name}}</td>
                                    <td class="widthOneFiveHundred">
                                        <span v-if="real.path_way==0">自提</span>
                                        <span v-if="real.path_way==1">邮寄</span>
                                        <span v-else></span>
                                    </td>
                                    <td class="widthOneFiveHundred">{{real.channels_name}}</td>
                                    <td class="widthOneFiveHundred">{{real.method_name}}</td>
                                    <td class="widthOneFiveHundred"  v-if="real.batch_cat!='2'||index==0">
                                        <span v-if="real.is_display==1" @click="goDetails(real.real_purchase_sn,item.title_info.purchase_sn,real.group_sn,real.is_mother)" style="cursor: pointer;">确认差异</span>
                                        <span v-if="real.is_display==0" style="cursor: pointer;background:#ccc;">确认差异</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-col>
                </el-row>
            </div>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
      fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        isShow:false,
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据
        headersStr:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getOverTimeDifference();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        //获取超时差异处理数据
        getOverTimeDifference(){
            let vm=this;
            axios.get(vm.url+vm.$expireDiffListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close()
                if(res.data.code==1000){
                    vm.tableData=res.data.data
                    vm.total=res.data.data_num;
                }
                if(res.data.code==1002){
                    vm.isShow=true
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
         //搜索框
        searchFrame(){
            let vm=this;
            if(vm.tableData==undefined){
                vm.getBatchData();
            }else{
                vm.getOverTimeDifference();
            }
        },
      //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getOverTimeDifference()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getOverTimeDifference()
        },
        //页面跳转
        goDetails(real_purchase_sn,purchase_sn,group_sn,is_mother){
            let vm=this;
            vm.title=vm.$route.query.real_purchase_sn;//页面title显示上页带来的real_purchase_sn
            axios.post(vm.url+vm.$diffDetailURL,
                {
                    "real_purchase_sn":real_purchase_sn,
                    "group_sn":group_sn,
                    "purchase_sn":purchase_sn,
                    "is_mother":is_mother,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                sessionStorage.setItem("tableData",'');
                sessionStorage.setItem("tableData",JSON.stringify(res.data.data));
                vm.$router.push('/differenceConfirmation?real_purchase_sn='+real_purchase_sn+'&purchase_sn='+purchase_sn+"&group_sn="+group_sn+"&is_mother="+is_mother+'&isOverTime=isOverTime');
            }).catch(function (error) {
                // vm.loading.close()
                if(error.response.status=="1020"){
                    vm.$message(error.response.msg); 
                }
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //去往汇总页面
        goSummaryPage(purchase_sn){
            this.$router.push('/differenceSummary?purchase_sn='+purchase_sn+'&isOverTime=isOverTime');
        },
    },
}
</script>
<style>
.el-table .warning-row {
    font-weight: bold;
}
.el-table{
    text-align: center;
    margin-bottom: 40px;
}
.el-table th>.cell {
    text-align: center;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>