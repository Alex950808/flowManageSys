<template>
    <div class="batchSetting_b">
    <!-- 待开单页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="batchSetting bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="purchaseDiscount listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;待开单需求单</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>
                        <div class="content" v-for="item in tableData">
                            <div class="content_text">
                                <!-- <span class="stage">{{item.title_info.purchase_id}}期</span><span class="number">{{item.title_info.purchase_sn}}</span> -->
                                <span v-if="item.title_info.purchase_id!=null" class="stage">{{item.title_info.purchase_id}}期</span>
                                <span v-if="item.title_info.purchase_sn!=null" class="stage ML_twenty">汇总需求单号：{{item.title_info.purchase_sn}}</span>
                                <span v-if="item.title_info.sum_demand_name!=null" class="stage ML_twenty">合期名称：{{item.title_info.sum_demand_name}}</span>
                                <!-- <span v-if="item.title_info.delivery_time!=null" class="stage ML_twenty">提货日期：{{item.title_info.delivery_time}}</span> -->
                                <span class="status" @click="goSummaryPage(item.title_info.purchase_sn)"><i class="el-icon-view"></i>&nbsp;&nbsp;查看数据汇总</span>
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
                                <tr>
                                    <td class="widthThreeHundred">批次单号</td>
                                    <td class="widthOneFiveHundred">渠道</td>
                                    <td class="widthOneFiveHundred">方式</td>
                                    <td class="widthOneFiveHundred">提货日</td>
                                    <td class="widthOneFiveHundred">到货日</td>
                                    <td class="widthOneFiveHundred">仓位</td>
                                    <td class="widthOneFiveHundred">自提/邮寄</td>
                                    <td class="widthOneFiveHundred">实采数</td>
                                    <td class="widthOneFiveHundred">操作</td>
                                </tr>
                                <tr v-for="(real,index) in realList">
                                    <td class="widthThreeHundred">{{real.real_purchase_sn}}</td>
                                    <td class="widthOneFiveHundred">{{real.channels_name}}</td>
                                    <td class="widthOneFiveHundred">{{real.method_name}}</td>
                                    <td class="widthOneFiveHundred">{{real.delivery_time}}</td>
                                    <td class="widthOneFiveHundred">{{real.arrive_time}}</td>
                                    <!-- <td>{{real.create_time}}</td> -->
                                    <td class="widthOneFiveHundred">{{real.port_name}}</td>
                                    <td class="widthOneFiveHundred">
                                        <span v-if="real.path_way==0">自提</span>
                                        <span v-if="real.path_way==1">邮寄</span>
                                        <span v-else></span>
                                    </td>
                                    <td class="widthOneFiveHundred">{{real.total_buy_num}}件</td>
                                    <td class="widthOneFiveHundred"  v-if="real.batch_cat!='2'||index==0">
                                        <span v-if="real.is_display==1" @click="goDetails(real.real_purchase_sn,item.title_info.purchase_id,item.title_info.purchase_sn,real.group_sn,real.is_mother)" class="bgButton">
                                            去开单
                                            <!-- <span style="display: none" class="el-icon-loading confirmLoading"></span> -->
                                        </span>
                                        <span v-if="real.is_display==0" style="cursor: pointer;background:#ccc;">去开单</span>
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
import searchBox from '../UiAssemblyList/searchBox';
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
      searchBox,
      fontWatermark,
    },
    data() {
      return {
        url: `${this.$baseUrl}`,
        tableData:[],  //批次数据
        search:'',//用户输入数据
        isShow:false,
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        headersStr:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getStayOpenData()
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      //获取待开单数据
      getStayOpenData(){
        let vm=this;
        axios.get(vm.url+vm.$billingListURL+"?query_sn="+vm.search+"&start_page="+vm.page+"&page_size="+vm.pagesize,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close()
            if(res.data.code==1002){
                vm.isShow=true;
            }
            vm.tableData=res.data.data;
            vm.total=res.data.data_num;
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
        //搜索框
        searchFrame(){
            let vm=this;
            vm.search=e;
            vm.page=1;
            vm.getStayOpenData();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getStayOpenData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getStayOpenData()
        },
      //查看详情
        goDetails(real_purchase_sn,id,purchase_sn,group_sn,is_mother){
            let vm=this;
            let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
            axios.post(vm.url+vm.$billingDetailURL,
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
                loa.close();
                sessionStorage.setItem("tableData",JSON.stringify(res.data.data));
                vm.$router.push('/confirmOpenBill?real_purchase_sn='+real_purchase_sn+'&id='+id+'&purchase_sn='+purchase_sn+"&group_sn="+group_sn+"&is_mother="+is_mother+'&isStayOpen=isStayOpen');
            }).catch(function (error) {
                loa.close();
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
          this.$router.push('/demandSummary?purchase_sn='+purchase_sn+'&isStayOpen=isStayOpen');
      },
    }
}
</script>
<style>
.stayOpenBill .status{
    cursor: pointer;
}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
</style>

<style scoped lang=less>
@import '../../css/taskModule.less';
</style>