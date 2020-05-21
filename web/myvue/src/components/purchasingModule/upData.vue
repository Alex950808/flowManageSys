<template>
  <div class="upData_b">
    <!-- 实时采购数据上传页面 -->
    <el-col :span="24" class="PR Height" style="background-color: #fff;">
        <div class="upData bgDiv">
            <fontWatermark :zIndex="false"></fontWatermark>
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="purchaseDiscount listTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购数据管理</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>
                    </div>
                    <ul>
                        <li v-for="item in UploadData">
                            <div class="content">
                                <div class="content_text lineHeightForty">
                                    <el-col :span="21">
                                    <span class="stage">{{item.purchase_id}}期</span><span class="number">{{item.purchase_sn}}</span>
                                    <span class="number">提货日 ：{{item.delivery_time}}</span>
                                    </el-col>
                                    <el-col :span="3">
                                    <span class="notBgButton" @click="goSummaryPage(item.purchase_sn)"><i class="el-icon-view"></i>&nbsp;&nbsp;查看数据汇总</span>
                                    </el-col>
                                </div>
                                <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                    <!-- <th class="w_t_ratio">商品需求数量</th> -->
                                    <th class="w_t_ratio">商品可采数量</th>
                                    <th class="w_t_ratio">预采数量</th>
                                    <th class="w_t_ratio">实采数</th>
                                    <th class="w_t_ratio">实采总数</th>
                                    <th class="w_t_ratio">采满率</th>
                                    <th class="w_t_ratio"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <!-- <td class="w_t_ratio">{{item.goods_num}}</td> -->
                                    <td class="w_t_ratio">{{item.may_buy_num}}</td>
                                    <td class="w_t_ratio">{{item.predict_num}}</td>
                                    <td class="w_t_ratio">{{item.real_buy_num}}</td>
                                    <td class="w_t_ratio">{{item.total_real_buy_num}}</td>
                                    <td class="w_t_ratio">{{item.real_buy_rate}}%</td>
                                    <td class="w_t_ratio">
                                        <span class="bgButton" @click="goDetails(item.purchase_sn)">
                                            上传实采数量
                                            <span style="display: none" class="el-icon-loading confirmLoading"></span>
                                        </span>
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </li>
                    <notFound v-if="isShow"></notFound>
                    </ul>
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
import { bgHeight } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '@/components/UiAssemblyList/searchBox';
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        notFound,
        fontWatermark,
    },
    data() {
      return {
        url: `${this.$baseUrl}`,
        UploadData:'',
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据 
        isShow:false,
        loading:'',
      }
    },
    mounted(){
        this.getUploadData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getUploadData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            if(vm.$route.query.purchase_sn!=undefined){
                vm.search=vm.$route.query.purchase_sn;
            }
            axios.get(vm.url+vm.$dataListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.$router.push('/upData');
                if(res.data.code==1000){
                    vm.UploadData=res.data.data.data;
                    vm.total=res.data.data.total;
                    vm.isShow=false;
                }else if(res.data.code==1002){
                    vm.UploadData='';
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
        goDetails(purchase_sn){
            let vm=this;
            let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$uploadDataURL+"?purchase_sn="+purchase_sn,
               {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                } 
            ).then(function(res){
                loa.close();
                if(res.data.code==1000){
                    sessionStorage.setItem("uploadingData",JSON.stringify(res.data.data));
                    vm.$router.push('/confirmUpData?purchase_sn='+purchase_sn);
                }else{
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                loa.close();
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
            // vm.$router.push('/confirmUpData?purchase_sn='+purchase_sn);
        },
        //搜索框
      searchFrame(e){//e为子组件载荷 
          let vm=this;
          vm.search=e;
          vm.getUploadData(); 
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.UploadData.splice(0)
          vm.pagesize=val
          vm.getUploadData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.UploadData.splice(0)
          vm.page=val
          vm.getUploadData()
      },
      //去往汇总页面
      goSummaryPage(purchase_sn){
          this.$router.push('/procurementSummary?purchase_sn='+purchase_sn+'&isUpData=isUpData');
      },
    }
}
</script>
<style>
.upData_b .status{
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