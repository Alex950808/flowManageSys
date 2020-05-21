<template>
  <div class="waitIntegralList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="select listTitleStyle">
                        <span><span class="coarseLine MR_ten"></span>待返积分列表</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                      <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>待返积分列表</span></div></el-col>
                      <el-col :span="3"><div>合单单号</div></el-col>
                      <el-col :span="3"><div><el-input v-model="purchase_sn" placeholder="请输入合单单号"></el-input></div></el-col>
                      <el-col :span="3"><div>开始时间</div></el-col>
                      <el-col :span="3"><div>
                          <el-date-picker style="width:190px;" v-model="start_time" type="date" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                          </div></el-col>
                      <el-col :span="3"><div>结束时间</div></el-col>
                      <el-col :span="3"><div>
                          <el-date-picker style="width:190px;" v-model="end_time" type="date" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                          </div></el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" :offset="3"><div>批次单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="real_purchase_sn" placeholder="请输入批次单号"></el-input></div></el-col>
                    </el-row>
                    <table class="tableTitle">
                        <thead>
                            <th width="20%">实采单号</th>
                            <th width="10%">实采总数</th>
                            <th width="10%">提货日</th>
                            <th width="10%">自提/邮寄</th>
                            <th width="10%">渠道名称</th>
                            <th width="10%">方式名称</th>
                            <th width="10%">是否提交</th>
                            <th width="10%">总积分</th>
                            <th width="10%">操作</th>
                        </thead>
                    </table>
                    <table class="tableTitleTwo">
                        <thead>
                            <th width="20%">实采单号</th>
                            <th width="10%">实采总数</th>
                            <th width="10%">提货日</th>
                            <th width="10%">自提/邮寄</th>
                            <th width="10%">渠道名称</th>
                            <th width="10%">方式名称</th>
                            <th width="10%">是否提交</th>
                            <th width="10%">总积分</th>
                            <th width="10%">操作</th>
                        </thead>
                    </table>
                    <div class="border B_R MB_ten" v-for="item in tableData.batch_total_list">
                        <div class="fontWeight lineHeightForty border_B B_R">
                            <el-col :span="21">
                            <span class="ML_twenty">名称：{{item.title_info.title_name}}</span>
                            <span class="ML_twenty">单号：{{item.title_info.purchase_sn}}</span>
                            <!-- <span class="ML_twenty">提货日期：{{item.pd_delivery_time}}</span> -->
                            </el-col>
                        </div>
                        <table class="w_n_ratio ML_F_R lineHeightForty fontCenter MT_ten MB_ten">
                            <tr v-for="(real,index) in item.batch_list">
                                <td width="20%">{{real.real_purchase_sn}}</td>
                                <td width="10%">{{real.total_buy_num}}件</td>
                                <td>{{real.rp_delivery_time}}</td>
                                <td width="10%">
                                    <span v-if="real.path_way==0">自提</span>
                                    <span v-if="real.path_way==1">邮寄</span>
                                    <span v-else></span>
                                </td>
                                <td width="10%">{{real.channels_name}}</td>
                                <td width="10%">{{real.method_name}}</td>
                                <td width="10%">
                                    <span v-if="real.is_integral==0">未提交</span>
                                    <span v-if="real.is_integral==1">已提交</span>
                                    <span v-else></span>
                                </td>
                                <td width="10%">{{real.total_integral}}</td>
                                <td width="10%">
                                    <span class="bgButton" @click="viewDetails(real.real_purchase_sn,real.channels_method_sn,real.purchase_sn)">查看详情</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '../UiAssemblyList/searchBox'
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      notFound,
      searchBox
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      search:'',
      //搜索
      start_time:'',
      end_time:'',
      purchase_sn:'',
      real_purchase_sn:'',
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
        axios.get(vm.url+vm.$waitIntegralListURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&query_sn="+vm.search+"&start_time="+vm.start_time
        +"&end_time="+vm.end_time+"&purchase_sn="+vm.purchase_sn+"&real_purchase_sn="+vm.real_purchase_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                vm.total=res.data.data.total_num;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.tableData=[];
                vm.total=0;
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
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
    viewDetails(real_purchase_sn,channels_method_sn,purchase_sn){
        let vm = this;
        // axios.get(vm.url+vm.$waitIntegralDetailURL,
        //     {
        //         headers:vm.headersStr,
        //     },
        // ).then(function(res){
        // }).catch(function (error) {
        //     if(error.response.status!=''&&error.response.status=="401"){
        //       vm.$message('登录过期,请重新登录!');
        //       sessionStorage.setItem("token","");
        //       vm.$router.push('/');
        //     }
        // }); 
        vm.$router.push('/waitIntegralDetail?real_purchase_sn='+real_purchase_sn+"&channels_method_sn="+channels_method_sn+"&purchase_sn="+purchase_sn);
    },
    tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=295;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".waitIntegralList .tableTitleTwo").addClass("addclass");
                $(".waitIntegralList .tableTitleTwo").width($(".waitIntegralList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".waitIntegralList .tableTitleTwo").removeClass("addclass");
            }
        })
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
    //搜索框
    searchFrame(e){
        let vm=this;
        vm.search=e;
        vm.page=1;
        vm.getDataList();
    },
  }
}
</script>

<style>
.waitIntegralList .tableTitle{
    width:100%;
    padding-left: 5%;
    padding-right: 5%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.waitIntegralList .tableTitleTwo{
    width:100%;
    padding-left: 5%;
    padding-right: 5%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.waitIntegralList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
</style>
