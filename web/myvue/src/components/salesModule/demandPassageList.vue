<template>
  <div class="demandAllotList_b">
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="demandAllotList">
                        <el-row class="purchaseTitle listTitleStyle">
                            <el-col :span="24">
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;在途需求单列表</span>
                                <searchBox @searchFrame='searchFrame'></searchBox>
                            </el-col>
                        </el-row>
                        <ul>
                            <li class="purchaseContent" v-for="item in tableData">
                                <el-row>
                                    <el-col :span="24">
                                        <div class="demandTitle">需求单号:{{item.demand_sn}}
                                            <!-- <span class="status" @click="goSummaryPage(item.demand_sn)"><i class="el-icon-goods"></i>&nbsp&nbsp查看数据汇总</span> -->
                                        </div>
                                        
                                        <table style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <!-- <th>需求单号</th> -->
                                                    <th>部门<span v-if="item.department==1">(批发部)</span><span v-if="item.department==2">(零售部)</span></th>
                                                    <th>采购截止日期({{item.expire_time}})</th>
                                                    <th>可采美金原价({{item.may_total_price}})</th>
                                                    <th>实采美金原价({{item.real_total_price}})</th>
                                                    <th>sku数量({{item.sku_num}})</th>
                                                    <th>商品总需求量({{item.goods_num}})</th>
                                                    <th>状态<span v-if="item.status==1">(待挂期)</span><span v-if="item.status==2">(待分配)</span><span v-if="item.status==3">(已分配)</span></th>
                                                </tr>
                                            </thead>
                                        </table>
                                        <table>
                                            <tr>
                                                <td>采购期号</td>
                                                <td>需求数量</td>
                                                <td>可采数量</td>
                                                <td>已采数量</td>

                                                <td>可采美金原价</td>
                                                <td>可采人民币总额</td>
                                                <td>实采美金原价</td>
                                                <td>实采人民币总额</td>
                                                <td>实采满足率</td>
                                                <td>金额满足率</td>
                                                <td>缺失率</td>
                                                <td>查看详情</td>
                                            </tr>
                                            <tr v-for="itemO in item.purchase_info">
                                                <td>{{itemO.purchase_sn}}</td>
                                                <td>{{itemO.goods_num}}</td>
                                                <td>{{itemO.may_buy_num}}</td>
                                                <td>{{itemO.real_buy_num}}</td>

                                                <td>{{itemO.may_total_price}}</td>
                                                <td>{{itemO.may_cny_total_price}}</td>
                                                <td>{{itemO.real_total_price}}</td>
                                                <td>{{itemO.real_cny_total_price}}</td>
                                                <td>{{itemO.real_buy_rate}}%</td>
                                                <td>{{itemO.price_rate}}%</td>
                                                <td>{{itemO.miss_buy_rate}}%</td>
                                                <td><i class="el-icon-view notBgButton" @click="viewDetails(itemO.purchase_sn,item.demand_sn)"></i></td>
                                            </tr>
                                        </table>
                                    </el-col>
                                </el-row>
                            </li>
                        </ul>
                    </div>
                    <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox';
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        fontWatermark,
    },
  data(){
    return{
        tableData: [],
        url: `${this.$baseUrl}`,
        search:'',//用户输入数据
        total:0,//页数默认为0
        page:1,//分页页数
        pagesize:15,//每页的数据条数
        isShow:false,
        dialogVisible: false,
        //多选内容
        checkAll: false,
        checkedCities: [],//选中的数据
        cities: [],//所有要选择的选项
        isIndeterminate: true,
        cityOptions:[],
        //参数
        demand_sn:'',
        department:'',
    }
  },
  mounted(){
      this.getDemandData();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
     //获取在途商品列表数据
      getDemandData(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$demandPassageListURL+'?start_page='+vm.page+'&page_size='+vm.pagesize+"&query_sn="+vm.search,
          {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
          }
        ).then(function(res){
          vm.loading.close();
          if(res.data.code==1002){
              vm.isShow=true;
          }
          if(res.data.code==1000){
              vm.tableData=res.data.data.demand_list;
              vm.total=res.data.data.total_num;
              vm.isShow=false;
          }
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //查看详情
      viewDetails(purchase_sn,demand_sn){
          let vm = this;
          let switchoEvent=event;
          switchLoading(event)
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$passageRealPurchaseListURL+"?purchase_sn="+purchase_sn+"&demand_sn="+demand_sn,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              if(res.data.code==1000){
                  sessionStorage.setItem("tableData",JSON.stringify(res.data));
                  vm.$router.push('/passageRealPurchaseList?purchase_sn='+purchase_sn);
              }else{
                  switchoRiginally(switchoEvent)
                  vm.$message(res.data.msg);
              }
          }).catch(function (error) {
                switchoRiginally(switchoEvent)
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      goSummaryPage(demand_sn){
          let vm = this;
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$demandAllotDetailURL+"?&demand_sn="+demand_sn,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              if(res.data.code==1000){
                  sessionStorage.setItem("tableData",JSON.stringify(res.data));
                  vm.$router.push('/demandPurchaseDetail?demand_sn='+demand_sn+"&isSummary=isSummary");
              }
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
        searchFrame(e){
            let vm=this;
            vm.search=e;
            vm.page=1;
            vm.getDemandData();
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
  }
}
</script>

<style>
.demandAllotList_b .purchaseTitle{
    font-weight: bold;
    position: relative;
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    /* font-weight:bold; */
    padding-left: 35px;
    margin-top: 20px;
}
.demandAllotList_b .purchaseTitle input{
    width: 200px;
    height: 20px;
    outline: none;
    -webkit-appearance: none;
    border-radius: 50px;
    -moz-border-radius: 50px;
    -webkit-border-radius: 50px;
    padding-left: 30px;
    margin-left: 16px;
}
.demandAllotList_b .purchaseTitle .search{
    position: absolute;
    left: 171px;
    top: 29px;
    color: #ccc;
}
.demandAllotList_b .purchaseContent{
    width: 100%;
    height: 100%;
    border:1px solid #ebeef5;
    border-radius: 10px;
    margin-bottom: 15px;
}
.demandAllotList_b .purchaseContent table{
    width:100%;
    height:100%;
    line-height: 50px;
    text-align: center;
}
.demandAllotList_b .status{
    display: inline-block;
    height: 30px;
    line-height: 30px;
    border-radius: 10px;
    cursor: pointer;
    float: right;
    margin-top: 10px;
    font-size: 20px;
    font-weight: bolder;
}
.demandAllotList_b .el-dialog__body{
    text-align: left;
}
.titleName{
    width: 19%;
    height: 50px;
    display: inline-block;
    line-height: 50px;
    vertical-align: 64px;
}
li{
    list-style: none;
}
.demandTitle{
    border-bottom: 1px solid #ebeef5;
    line-height: 50px;
    margin-left: 20px;
    margin-right: 20px;
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
@import '../../css/salesModule.less';
</style>
