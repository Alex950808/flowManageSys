<template>
  <div class="sumSortDataList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle select">
                        <router-link  to='/batchOrderList'><span class="bgButton">返回上一级</span></router-link>
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;合单号对应的总分货数据</span>
                    </div>
                    <el-row class="t_i fontCenter">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th style="width:200px">商品名称</th>
                                            <th style="width:160px">商品规格码</th>
                                            <th style="width:160px">商家编码</th>
                                            <th style="width:180px">需求单号</th>
                                            <th style="width:160px">用户</th>
                                            <th style="width:110px">需求数</th>
                                            <th style="width:110px">默认值</th>
                                            <th style="width:110px">还需分配数</th>
                                            <th style="width:110px">已分配数</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc"> 
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr v-for="item in tableData">
                                    <td class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                        <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                    </td>
                                    <td style="width:160px">{{item.spec_sn}}</td>
                                    <td style="width:160px">{{item.erp_merchant_no}}</td>
                                    <td style="width:180px">{{item.demand_sn}}</td>
                                    <td style="width:160px">{{item.sale_user_name}}</td>
                                    <td style="width:110px">{{item.goods_num}}</td>
                                    <td style="width:110px">{{item.default_num}}</td>
                                    <td style="width:110px">{{item.still_need_num}}</td>
                                    <td style="width:110px">{{item.yet_num}}</td>
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
      isShow:false,
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
        axios.post(vm.url+vm.$sumSortDataListURL,
            {
                "sum_demand_sn":vm.$route.query.sum_demand_sn,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.sortDataList.length!=0){
                vm.tableData=res.data.sortDataList;
                tableStyleByDataLength(res.data.sortDataList.length,15);
                vm.isShow=false;
            }else{
                vm.isShow=true;
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
  }
}
</script>

<style scoped>
.sumSortDataList .t_i{width:100%; height:auto;}
.sumSortDataList .t_i_h{width:100%; overflow-x:hidden;}
.sumSortDataList .ee{width:100%!important; text-align: center;}
.sumSortDataList .t_i_h table{width:100%;}
.sumSortDataList .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.sumSortDataList .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.sumSortDataList .cc table{width:100%; }
.sumSortDataList .cc table td{height:25px; text-align:center}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
