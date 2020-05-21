<template>
  <div class="realPurList_b">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
          <el-row class="bgDiv">
              <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="realPurList">
                        <div class="purchaseDiscount tableTitleStyle">
                            <span><span class="coarseLine MR_twenty"></span>批次单列表</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>
                        <el-row>
                            <div class="t_i">
                                <div class="t_i_h" id="hh">
                                    <div class="ee">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <thead>
                                                <tr>
                                                    <th width="10%">实采单号</th>
                                                    <th width="20%">采购期单号</th>
                                                    <th width="10%">采购方式id</th>
                                                    <th width="10%">提货方式</th>
                                                    <th width="10%">仓位</th>
                                                    <th width="10%">状态</th>
                                                    <th width="10%">创建时间</th>
                                                    <th width="10%">查看详情</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="cc" id="cc" @scroll="scrollEvent()">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr v-for="item in tableData">
                                            <td width="10%">{{item.real_purchase_sn}}</td>
                                            <td width="20%">{{item.purchase_sn}}</td>
                                            <td width="10%">{{item.method_id}}</td>
                                            <td width="10%">
                                                <span v-if="item.path_way==0">自提</span>
                                                <span v-if="item.path_way==1">邮寄</span>
                                                <span v-else></span>
                                            </td>
                                            <td width="10%">{{item.port_name}}</td>
                                            <td width="10%">{{item.status}}</td>
                                            <td width="10%">{{item.create_time}}</td>
                                            <td width="10%"><i class="el-icon-view notBgButton" @click="viewDetails(item.real_purchase_sn)"></i></td>
                                        </tr>
                                        
                                    </table>
                                </div>
                            </div>
                        </el-row>
                    </div>
                    <el-row>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                </el-col>
          </el-row>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import searchBox from '../UiAssemblyList/searchBox';
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
    components:{
      searchBox,
      notFound,
      fontWatermark
    },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      search:'',
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
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
        axios.get(vm.url+vm.$getRealPurListURL+"?keywords="+vm.search+"&page_size="+vm.pagesize+"&page="+vm.page,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.realPurList.data!=''){
                vm.tableData=res.data.realPurList.data;
                vm.total=res.data.realPurList.total;
                tableStyleByDataLength(vm.tableData.length,15);
            }else{
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
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    //搜索框
    searchFrame(e){
        let vm=this;
        vm.search=e;
        vm.getListData();
    },
    viewDetails(real_purchase_sn){
        let vm=this;
        // let switchoEvent=event;
        // switchLoading(event);
        // let headersToken=sessionStorage.getItem("token");
        // axios.get(vm.url+vm.$getRealGoodsInfoURL+"?real_purchase_sn="+real_purchase_sn,
        //      {
        //         headers:{
        //             'Authorization': 'Bearer ' + headersToken,
        //             'Accept': 'application/vnd.jmsapi.v1+json',
        //         }
        //     }
        // ).then(function(res){
        //     switchoRiginally(switchoEvent)
        //     sessionStorage.setItem("tableData",JSON.stringify(res.data.realGoodsInfo));
            vm.$router.push('/distributionOfGoods?real_purchase_sn='+real_purchase_sn+"&isReal=isReal");
        // }).catch(function (error) {
        //         switchoRiginally(switchoEvent)
        //         if(error.response.status!=''&&error.response.status=="401"){
        //         vm.$message('登录过期,请重新登录!');
        //         sessionStorage.setItem("token","");
        //         vm.$router.push('/');
        //         }
        //     });
    },
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getListData()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getListData()
    },
  }
}
</script>

<style>
.realPurList_b .t_i{width:100%; height:auto;}
.realPurList_b .t_i_h{width:100%; overflow-x:hidden;}
.realPurList_b .ee{width:100%!important; width:100%; text-align: center;}
.realPurList_b .t_i_h table{width:100%;}
.realPurList_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.realPurList_b .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.realPurList_b .cc table{width:100%; }
.realPurList_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
