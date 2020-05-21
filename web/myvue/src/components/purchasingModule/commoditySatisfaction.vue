<template>
  <div class="commoditySatisfaction_b">
    <!-- 商品动态满足率页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="commoditySatisfaction bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="title tableTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;商品动态满足率</span><input type="text" class="inputStyle" placeholder="请输入搜索关键字" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                        </div> 
                        <table style="width:100%;text-align: center;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>商品规格码</th>
                                <th>商品代码</th>
                                <th>商家编码</th>
                                <th>商品需求总量</th>
                                <th>商品可采总量</th>
                                <th>商品实采总量</th>
                                <th>实采率</th>
                                <th>缺失率</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in tableData">
                                <td class="ellipsis" style="width:280px;">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td>{{item.spec_sn}}</td>
                                <td >{{item.erp_prd_no}}</td>
                                <td>{{item.erp_merchant_no}}</td>
                                <td>{{item.goods_num}}</td>
                                <td>{{item.may_buy_num}}</td>
                                <td>{{item.real_buy_num}}</td>
                                <td>{{item.real_buy_rate}}%</td>
                                <td>{{item.miss_buy_rate}}%</td>
                            </tr>
                            </tbody>
                        </table>
                        <div v-if="isShow" style="text-align: center;line-height: 50px;"><img class="notData" src="../../image/notData.png"/></div>
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
import axios from 'axios';
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
        tableData: [],
        search:'',
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        loading:'',
        isShow:false,
        headersStr:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getTimeOutClearanceData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        //获取待清关订单
        getTimeOutClearanceData(){
            let vm=this;
            if(vm.search!=''){
                vm.page=1
            }
            axios.get(vm.url+vm.$dataGoodsListURL+"?page="+vm.page+"&pagesize="+vm.pagesize+"&query_sn="+vm.search,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.tableData=res.data.data;
                vm.total=res.data.data_num;
                if(res.data.code==1002){
                    vm.isShow=true;
                }
                vm.loading.close()
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
            vm.getTimeOutClearanceData();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getTimeOutClearanceData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getTimeOutClearanceData()
        },
    }
}
</script>
<style>
/* @import '../../css/common.css'; */
.commoditySatisfaction .ellipsis span{
    width: 280px;
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
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