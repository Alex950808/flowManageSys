<template>
  <div class="PurOrderList_b">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
        <el-row class="bgDiv">
            <fontWatermark :zIndex="false"></fontWatermark>
            <el-col :span="22" :offset="1">
                <div class="PurOrderList">
                    <div class="purchaseDiscount tableTitleStyle">
                        <span><span class="coarseLine MR_twenty"></span>采购单列表</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>  
                    </div>
                    <el-row>
                        <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>采购期单号</th>
                                    <th>创建时间</th>
                                    <th>提货队人数</th>
                                    <th>提货队</th>
                                    <th>采购渠道</th>
                                    <th>采购方式</th>
                                    <th>开始时间</th>
                                    <th>状态</th>
                                    <th>查看详情</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in tableData">
                                    <td>{{item.purchase_sn}}</td>
                                    <td>{{item.create_time}}</td>
                                    <td>{{item.delivery_pop_num}}</td>
                                    <td>{{item.delivery_team}}</td>
                                    <td class="ellipsis" style="width:300px;" :title="item.channels_list"><span>{{item.channels_list}}</span></td>
                                    <td>{{item.method_info}}</td>
                                    <td>{{item.start_time}}</td>
                                    <td>{{item.status}}</td>
                                    <td><i class="el-icon-view notBgButton" @click="viewDetails(item.purchase_sn)"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-row>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                </div>
            </el-col>
        </el-row>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '@/components/UiAssemblyList/searchBox';
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        notFound,
        fontWatermark,
    },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      search:'',
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      isShow:false,
    }
  },
  mounted(){
    this.getListData();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
    getListData(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$getPurOrderListURL+"?keywords="+vm.search,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            vm.loading.close();
            vm.total=res.data.purOrderList.total;
            if(vm.total>0){
                vm.tableData=res.data.purOrderList.data;
                vm.isShow=false;
            }else if(vm.total=0){
                vm.tableData=res.data.purOrderList.data;
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
    viewDetails(purchase_sn){
        let vm=this;
        let switchoEvent=event;
        switchLoading(event);
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$getPurDemOrdListURL+"?purchase_sn="+purchase_sn,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            switchoRiginally(switchoEvent)
            sessionStorage.setItem("tableData",JSON.stringify(res.data.purDemOrdList));
            vm.$router.push('/purDemOrdList?purchase_sn='+purchase_sn);
        }).catch(function (error) {
                switchoRiginally(switchoEvent)
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
            vm.getListData();
        },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getOutOfTimeData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getOutOfTimeData()
      },
  }
}
</script>

<style>
.PurOrderList_b .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
