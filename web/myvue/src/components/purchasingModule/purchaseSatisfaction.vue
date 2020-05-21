<template>
  <div class="purchaseSatisfaction_b">
    <!-- 采购单满足率页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="purchaseSatisfaction bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="purchaseDiscount listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购满足率</span><input class="inputStyle" placeholder="请输入搜索关键字" type="text" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                        </div>
                        <ul>
                            <li v-for="item in UploadData">
                                <div class="content">
                                    <div class="content_text">
                                        <span class="stage">{{item.purchase_id}}期</span><span class="number">{{item.purchase_sn}}</span>
                                        <!-- <span class="operation">剩余{{item.surplus_date}}天</span> -->
                                        <!-- <span class="status"><i class="el-icon-goods"></i>&nbsp&nbsp查看数据汇总</span> -->
                                    </div>
                                    <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr>
                                        <th>采购期单号</th>
                                        <th>商品总需求量</th>
                                        <th>商品可采数量</th>
                                        <th>商品实采数量</th>
                                        <th>实时采满率</th>
                                        <!-- <th></th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        <td>{{item.purchase_sn}}</td>
                                        <td>{{item.goods_num}}</td>
                                        <td>{{item.may_buy_num}}</td>
                                        <td>{{item.real_buy_num}}</td>
                                        <td>{{item.real_buy_rate}}%</td>
                                        <!-- <td @click="goDetails(item.purchase_sn)" style="cursor: pointer;"><el-tag>上传实时实采数量</el-tag></td> -->
                                        </tr>
                                    </tbody>
                                    </table>
                                </div>
                            </li>
                        <li v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></li>
                        </ul>
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
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
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
        headersStr:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getUploadData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getUploadData(){
            let vm=this;
            axios.get(vm.url+vm.$dataManagementListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&purchase_sn="+vm.search,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.UploadData=res.data.data
                vm.total=res.data.data_num
                vm.loading.close()
                if(res.data.code==1002){
                    vm.loading.close()
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
        // goDetails(purchase_sn){
        //     let vm=this;
        //     let headersToken=sessionStorage.getItem("token");
        //     axios.get(vm.url+vm.$checkPurchaseDatePassURL+"?purchase_sn="+purchase_sn,
        //        {
        //             headers:{
        //                 'Authorization': 'Bearer ' + headersToken,
        //                 'Accept': 'application/vnd.jmsapi.v1+json',
        //             }
        //         } 
        //     ).then(function(res){
        //         if(res.data.code==1000){
        //             vm.$router.push('/confirmUpData?purchase_sn='+purchase_sn);
        //         }else{
        //             vm.$message(res.data.msg);
        //         }
        //     }).catch(function (error) {
        //         vm.loading.close()
        //         if(error.response.status=="401"){
        //         vm.$message('登录过期,请重新登录!');
        //         sessionStorage.setItem("token","");
        //         vm.$router.push('/');
        //         }
        //     });
        // },
        //搜索框
      searchFrame(){
          let vm=this;
              vm.getUploadData();
              vm.isShow=false;
              vm.UploadData.splice(0)
          
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
    }
}
</script>
<style>
/* @import '../../css/common.css'; */
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