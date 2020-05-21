<template>
  <div class="quotationDevice_b">
      <el-col :span="22" :offset="1">
            <div class="quotationDevice">
                <div class="purchaseDiscount">
                    <span>报价计算器</span>
                </div>
                <el-row class="text_content" v-for="item in tableData" :key="item.demand_sn">
                    <el-col :span="18" :offset="1">
                        <div class="text_top">
                            <span>0001期</span>&nbsp&nbsp&nbsp&nbsp<span>{{item.demand_sn}}</span>
                        </div>
                        <el-row class="text_bottom">
                            <el-col :span="8"><div class=""><span>客户：</span><span>{{item.users}}</span></div></el-col>
                            <el-col :span="4"><div class=""><span>品牌数：</span><span>{{item.brand_num}}</span></div></el-col>
                            <el-col :span="4"><div class=""><span>SKU：</span><span>{{item.sku_num}}</span></div></el-col>
                            <el-col :span="4"><div class=""><span>商品数：</span><span>{{item.goods_num}}</span></div></el-col>
                        </el-row>
                    </el-col>
                    <el-col :span="5">
                        <div class="text_right">
                            <i class="iconfont notBgButton" @click="goToOffer(item.demand_sn)">&#xe65c;</i>
                            <div>报价计算</div>
                        </div>
                    </el-col>
                </el-row>
                <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                <!-- <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                    :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                </el-pagination> -->
            </div>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
export default {
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      isShow:false,
    }
  },
  mounted(){
    this.getlistData();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
      //获取商品报价列表
      getlistData(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$getNeedGoodsListURL,
              {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
              }
          ).then(function(res){
              vm.loading.close()
              vm.tableData=res.data.needGoodsList.data;
              vm.total=res.data.needGoodsList.total;
              if(vm.total==0){
                  vm.isShow=true;
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
      //去往报价页面
      goToOffer(demand_sn){
        let vm=this;
        // let headersToken=sessionStorage.getItem("token");
        // axios.get(vm.url+vm.$queryNeedGoodsInfoURL+"?demand_sn="+demand_sn,
        //         {
        //             headers:{
        //                 'Authorization': 'Bearer ' + headersToken,
        //                 'Accept': 'application/vnd.jmsapi.v1+json',
        //             }
        //         }
        //   ).then(function(res){
        //     //   if(res.data.code==1000){
        //         // sessionStorage.setItem("tableData",JSON.stringify(res.data));
        //         // vm.tableData=res.data.demandGoodsInfo;
        //         // vm.grossInterestRate=res.data.arrPickRate;
        //         // vm.costItem=res.data.arrCharge;
        //         // vm.tableTitle=res.data.brand_info;
        //         // vm.rateTdWidth=res.data.arrPickRate.length;
        //         vm.$router.push('/quotationDetails?demand_sn='+demand_sn+'&isFirst=isFirst');
        //     //   }
        //   }).catch(function (error) {
        //         vm.loading.close()
        //         if(error.response.status!=''&&error.response.status=="401"){
        //         vm.$message('登录过期,请重新登录!');
        //         sessionStorage.setItem("token","");
        //         vm.$router.push('/');
        //         }
        //     });
            vm.$router.push('/quotationDetails?demand_sn='+demand_sn);
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getlistData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getlistData();
      },
  }
}
</script>

<style>
.quotationDevice_b .purchaseDiscount{
    margin-top: 50px;
    margin-bottom: 50px;
    position: relative;
}
.quotationDevice_b .purchaseDiscount span{
    font-size: 18px;
    font-weight: bold;
}
.quotationDevice_b .text_content{
    height: 150px;
    border: 1px solid #000;
    border-radius: 10px;
    margin-top: 30px;
}
.quotationDevice_b .text_top{
    width: 100%;
    height: 75px;
    line-height: 75px;
    border-bottom: 1px solid #ccc;
}
.quotationDevice_b .text_top span:first-child{
    font-weight: bold;
    font-size: 20px;
}
.quotationDevice_b .text_bottom{
    width: 100%;
    height: 75px;
    line-height: 75px;
}
.quotationDevice_b .text_right{
    width: 100%;
    height: 150px;
    padding-top: 30px;
    text-align: center;
}
.quotationDevice_b .text_right div{
    font-weight: bold;
    font-size: 20px;
}
</style>
