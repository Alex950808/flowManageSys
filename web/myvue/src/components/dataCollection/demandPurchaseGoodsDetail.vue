<template>
  <div class="demandAllotList_b">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv demandAllotList">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <backToTheUpPage class="MR_ten" :back="backStr"></backToTheUpPage>
                        <span class="coarseLine MR_ten"></span>{{`${this.$route.query.purchase_sn}`}}_{{tableTitleStr}}
                    </div>
                    <el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig'></tableContent>
                        <notFound v-if="isShow"></notFound>
                    </el-row>
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
import tableContent from '@/components/UiAssemblyList/tableContent'
import backToTheUpPage from '@/components/UiAssemblyList/backToTheUpPage';
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      tableContent,
      backToTheUpPage,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      tableContent:'',
      TCTitle:[],
      tableField:[],
      contentConfig:'[{"isShow":"false"},{"parameter":"false"}]',
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      tableTitleStr:'',
      backStr:'',
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
        console.log(vm.titleStr,vm.fieldStr);//这里不能删掉
        vm.tableData=JSON.parse(sessionStorage.getItem("goodsData"));
          vm.tableContent=JSON.stringify(vm.tableData);
          vm.loading.close();
          if(vm.tableData.length==0){
              vm.isShow=true;
          }
    },
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDataList()
    },
  },
  computed:{
        titleStr(){
            let vm = this;
            if(vm.$route.query.isRealPurchase=="isRealPurchase"){
                vm.TCTitle=['商品名称','商品规格码','商家编码','商品代码','实采数','分配数量']
                vm.tableTitleStr='需求单对应采购期中的商品信息';
                vm.backStr='/demandRealPurchaseList';
            }else if(vm.$route.query.isStastics=="isStastics"){
                vm.TCTitle=['商品名称','商品规格码','商家编码','商品代码','实采数','分配数量']
                vm.tableTitleStr='需求单对应采购期批次中的商品信息';
                vm.backStr='/demandStasticsList';
            }
        },
        fieldStr(){
            let vm = this;
            if(vm.$route.query.isRealPurchase=="isRealPurchase"){
                    vm.tableField=['goods_name','spec_sn','erp_merchant_no','erp_prd_no','real_buy_num','day_buy_num'];//表格字段
            }else if(vm.$route.query.isStastics=="isStastics"){
                    vm.tableField=['goods_name','spec_sn','erp_merchant_no','erp_prd_no','real_buy_num','final_real_buy_num'];//表格字段
            }
        }
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
