<template>
  <div class="expectedToArrive_b">
    <!-- 预计到港时间页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="expectedToArrive bgDiv">
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="title listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;预计到港时间</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>
                        <div class="content" v-for="item in BatchData">
                            <div class="content_text lineHeightForty">
                                <el-col :span="21">
                                    <span v-if="item.title_info.purchase_id!=null" class="stage">{{item.title_info.purchase_id}}期</span>
                                    <span v-if="item.title_info.purchase_sn!=null" class="stage ML_twenty">汇总需求单号：{{item.title_info.purchase_sn}}</span>
                                    <span v-if="item.title_info.sum_demand_name!=null" class="stage ML_twenty">合期名称：{{item.title_info.sum_demand_name}}</span>
                                </el-col>
                                <el-col :span="3">
                                    <span class="notBgButton" @click="goSummaryPage(item.title_info.purchase_sn)"><i class="el-icon-view"></i>&nbsp;&nbsp;查看数据汇总</span>
                                </el-col>
                            </div>
                            <el-row>
                                <el-col :span="12">
                                    <table class="table-one">
                                        <tr>
                                            <td>实采总数</td>
                                            <td>自提数量</td>
                                            <td>邮寄数量</td>
                                        </tr>
                                        <tr>
                                            <td>{{item.title_info.real_buy_num}}</td>
                                            <td>{{item.title_info.zt_num}}批({{item.title_info.zt_goods_num}}件)</td>
                                            <td>{{item.title_info.yj_num}}批({{item.title_info.yj_goods_num}}件)</td>
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
                                    <!-- <td class="widthOneFiveHundred">{{real.create_time}}</td> -->
                                    <td class="widthOneFiveHundred">{{real.port_name}}</td>
                                    <td class="widthOneFiveHundred">
                                        <span v-if="real.path_way==0">自提</span>
                                        <span v-if="real.path_way==1">邮寄</span>
                                        <span v-else></span>
                                    </td>
                                    <td class="widthOneFiveHundred">{{real.channels_name}}</td>
                                    <td class="widthOneFiveHundred">{{real.method_name}}</td>
                                    <td class="widthOneFiveHundred"><span v-if="real.batch_cat!='2'||index==0" @click="goDetails(item.title_info.purchase_sn,real.real_purchase_sn,real.group_sn,real.is_mother)" class="bgButton">查看明细</span></td>
                                </tr>
                            </table>
                            
                        </div>
                        <div v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-col>
                </el-row>
                
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox';
export default {
    components:{
        searchBox,
    },
    data() {
      return {
        url: `${this.$baseUrl}`,
        BatchData:[] ,  //批次信息
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据
        loading:'',
        isShow:false,
        headersStr:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getBatchData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getBatchData(){
            let vm=this;
            axios.get(vm.url+vm.$batchListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search,
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close()
                vm.BatchData=res.data.data;
                vm.total=res.data.data_num;
                if(res.data.code==1000){
                    vm.isShow=false;
                }else if(res.data.code==1002){
                    vm.isShow=true;
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
        //页面跳转
        goDetails(purchase_sn,real_purchase_sn,group_sn,is_mother){
            this.$router.push('/batchDetails?real_purchase_sn='+real_purchase_sn+"&purchase_sn="+purchase_sn+"&group_sn="+group_sn+"&is_mother="+is_mother);
        },
        //搜索框
      searchFrame(e){
          let vm=this;
          vm.search=e;
          vm.page=1;
          vm.getBatchData();
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.pagesize=val
          vm.getBatchData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.page=val
          vm.getBatchData()
      },
      //去往汇总页面
      goSummaryPage(purchase_sn){
          this.$router.push('/batchSummary?purchase_sn='+purchase_sn+'&isEspected=isEspected');
      },
    }
}
</script>
<style>
.status{
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
@import '../../css/purchasingModule.less';
</style>