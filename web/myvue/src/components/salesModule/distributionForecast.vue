<template>
  <div class="distributionForecast_b">
    <!-- 商品分配预测页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="distributionForecast bgDiv">
                        <div class="title">
                            <span>商品分配预测</span><input class="inputStyle" placeholder="请输入搜索关键字" type="text" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                        </div>
                        <div class="content" v-for="item in BatchData">
                            <div class="content_text">
                                <span class="stage">{{item.title_info.purchase_id}}期</span><span class="number">{{item.title_info.purchase_sn}}</span>
                                <span class="status"  @click="goSummaryPage(item.title_info.purchase_sn)"><i class="el-icon-goods"></i>&nbsp;&nbsp;查看数据汇总</span>
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
                                    <!-- <router-link to='/increaseBatch'><div class="increase">增加到港批次</div></router-link> -->
                                </el-col>
                            </el-row>
                            <table class="table-two">
                                <tr v-for="itemO in item.list_info">
                                    <td>{{itemO.real_purchase_sn}}</td>
                                    <td>{{itemO.total_buy_num}}件</td>
                                    <td>{{itemO.create_time}}</td>
                                    <td>{{itemO.port_name}}</td>
                                    <td>
                                        <span v-if="itemO.path_way==0">自提</span>
                                        <span v-if="itemO.path_way==1">邮寄</span>
                                    </td>
                                    <td><span @click="goDetails(item.title_info.purchase_sn,itemO.real_purchase_sn)" style="cursor: pointer;">查看明细</span></td>
                                </tr>
                            </table>
                            
                        </div>
                        <div v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
export default {
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
      }
    },
    mounted(){
    //   this.getDistributionData();
      this.getBatchData();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      //获取商品预测分配列表
    getBatchData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$getGoodsAllotListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search,
                {
                     headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.loading.close()
                vm.BatchData=res.data.data;
                vm.total=res.data.data_num
                if(vm.total==0){
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
        //去往数据汇总页面
        goSummaryPage(purchase_sn){
            this.$router.push('/seeData?purchase_sn='+purchase_sn);
        },
        //查看明细
        goDetails(purchase_sn,real_purchase_sn){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.title=vm.$route.query.purchase_sn;
            axios.get(vm.url+vm.$getGoodsAllotURL+"?purchase_sn="+purchase_sn+"&real_purchase_sn="+real_purchase_sn,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                sessionStorage.setItem("tableData",JSON.stringify(res.data.data));
                vm.$router.push('/seeData?purchase_sn='+purchase_sn+'&real_purchase_sn='+real_purchase_sn);
            }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
            // this.$router.push('/seeData?purchase_sn='+purchase_sn+'&real_purchase_sn='+real_purchase_sn);
        },
        //搜索框
      searchFrame(){
          let vm=this;
          if(vm.BatchData==undefined){
              vm.getBatchData();
          }else{
              vm.BatchData.splice(0)
              vm.getBatchData();
          }
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
    }
}
</script>
<style>
</style>

<style scoped lang=less>
@import '../../css/salesModule.less';
</style>