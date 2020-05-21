<template>
  <div class="auditDemand_b">
    <!-- 待审核需求单页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="auditDemand">
                <div class="purchaseDiscount">
                    <span>待审核需求单</span><input type="text" class="inputStyle" placeholder="请输入搜索关键字" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                </div>
                <ul>
                     <li v-for="item in tableData">
                        <div class="content">
                            <div class="content_text">
                                <span class="stage">{{item.number_sn}}期</span>
                                <span class="number">{{item.purchase_sn}}</span>
                                <!-- <span class="operation">{{item.number_sn}}</span> -->
                                <span class="status" @click="goSummaryPage(item.purchase_sn)"><i class="el-icon-goods"></i>&nbsp&nbsp查看需求汇总</span>
                            </div>
                            <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                              <thead>
                                <tr>
                                  <th>需求日期</th>
                                  <th>需求单次</th>
                                  <th>sku数量</th>
                                  <th>商品数量</th>
                                  <th>状态</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="itemO in item.demand_info">
                                  <td>{{itemO.create_time}}</td>
                                  <td>{{itemO.demand_sn}}</td>
                                  <td>{{itemO.sku_num}}</td>
                                  <td>{{itemO.goods_num}}</td>
                                  <td style="cursor: pointer;"  @click="goGoodsId(item.purchase_sn,itemO.demand_sn)" v-if="itemO.status==1"><el-tag>待审核</el-tag></td>
                                  <td style="cursor: pointer;" v-if="itemO.status==2">已审核</td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </li>
                   <li v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></li>
                   <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </ul>
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import comment from '../../filters/comment'
import { Loading } from 'element-ui';
export default {
    data() {
      return {
        url: `${this.$baseUrl}`,
        tableData: [],
        isShow:false,
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据
        loading:'',
        headersStr:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getDemandData();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      getDemandData(){
        let vm=this;
        axios.get(vm.url+vm.$demandListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search,
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          vm.tableData=res.data.data
          vm.total=res.data.data_num
          vm.loading.close()
          if(res.data.code==1002){
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
      goGoodsId(purchase_sn,demand_sn){
          let vm=this;
            vm.title=vm.$route.query.purchase_sn;
            axios.post(vm.url+vm.$demandDetailURL,
                {
                    "purchase_sn":purchase_sn,
                    "demand_sn":demand_sn,
                    'query_sn':vm.search,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                sessionStorage.setItem("tableData",JSON.stringify(res.data));
                vm.$router.push('/auditDemandDetails?purchase_sn='+purchase_sn+'&demand_sn='+demand_sn);
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        //   vm.$router.push('/auditDemandDetails?purchase_sn='+purchase_sn+'&demand_sn='+demand_sn);
      },
      //搜索框
      searchFrame(){
          let vm=this;
          vm.getDemandData();
          vm.isShow=false;
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getDemandData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getDemandData()
      },
      //去往汇总页面
      goSummaryPage(purchase_sn){
          this.$router.push('/summaryOfRequirements?purchase_sn='+purchase_sn+'&isauditDemand=isauditDemand');
      },
    }
}
</script>
<style>
/* @import '../../css/common.css'; */
.auditDemand_b .status{
    cursor: pointer;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>