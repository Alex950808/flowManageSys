<template>
  <div class="demandPurchaseList_b">
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="demandPurchaseList">
                <div class="purchaseDiscount">
                    <span class="back" @click="backUpPage()">返回上一级</span>
                </div>
                <ul>
                     <li v-for="item in tableData">
                        <div class="content">
                            <div class="content_text">
                                <span class="stage">{{item.purchase_id}}期</span><span class="number">{{item.purchase_sn}}</span>
                            </div>
                            <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                              <thead>
                                <tr>
                                  <th>采购期单号</th>
                                  <th>需求总数</th>
                                  <th>商品可采数量</th>
                                  <th>实采总数</th>
                                  <th>实时采满率</th>
                                  <th>查看详情</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>{{item.purchase_sn}}</td>
                                  <td>{{item.goods_num}}</td>
                                  <td>{{item.may_buy_num}}</td>
                                  <td>{{item.real_buy_num}}</td>
                                  <td>{{item.real_buy_rate}}%</td>
                                  <td style="cursor: pointer;"><i class="iconfont notBgButton" @click="viewDetails(item.purchase_sn)">&#xe631;</i></td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </li>
                   <li v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></li>
                </ul>
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
export default {
    data() {
      return {
        url: `${this.$baseUrl}`,
        tableData:'',
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
    },
    methods: {
        getUploadData(){
            let vm=this;
            vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
            // let headersToken=sessionStorage.getItem("token");
            // axios.get(vm.url+vm.$demandPurchaseListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&purchase_sn="+vm.search,
            //     {
            //         headers:{
            //             'Authorization': 'Bearer ' + headersToken,
            //             'Accept': 'application/vnd.jmsapi.v1+json',
            //         }
            //     }
            // ).then(function(res){
            //     vm.UploadData=res.data.data
            //     vm.total=res.data.data_num
            //     vm.loading.close()
            //     if(res.data.code==1002){
            //         vm.loading.close()
            //         vm.isShow=true;
            //     }
            // }).catch(function (error) {
            //     vm.loading.close()
            //     if(error.response.status!=''&&error.response.status=="401"){
            //     vm.$message('登录过期,请重新登录!');
            //     sessionStorage.setItem("token","");
            //     vm.$router.push('/');
            //     }
            // });
        },
        //去往详情页面
        viewDetails(purchase_sn){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$demandPurchaseDetailURL+"?purchase_sn="+purchase_sn+"&demand_sn="+vm.$route.query.demand_sn,
               {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                } 
            ).then(function(res){
                if(res.data.code==1000){
                    sessionStorage.setItem("purchaseData",JSON.stringify(res.data.data));
                    vm.$router.push('/demandPurchaseDetail?purchase_sn='+purchase_sn+"&demand_sn="+vm.$route.query.demand_sn);
                }else{
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
            // vm.$router.push('/confirmUpData?purchase_sn='+purchase_sn);
        },
        backUpPage(){
            let vm=this;
            vm.$router.push('/demandAllotList');
        },
        //搜索框
      searchFrame(){
          let vm=this;
          if(vm.UploadData==undefined){
              vm.getUploadData();
              vm.isShow=false;
          }else{
              vm.UploadData.splice(0)
              vm.getUploadData();
              vm.isShow=false;
          }
          
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
.demandPurchaseList_b .status{
    cursor: pointer;
}
li{
    list-style: none;
}
.demandPurchaseList_b .back{
    display: inline-block;
    width: 200px;
    height: 50px;
    background-color: #00C1DE;
    color: #fff;
    line-height: 50px;
    text-align: center;
    margin-top: 25px;
    border-radius: 10px;
    cursor: pointer
}
</style>

<style scoped lang=less>
@import '../../css/demo.less';
</style>