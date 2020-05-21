<template>
  <div class="getErpGoodsList">
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="3"><div><span class="fontWeight"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;erp商品列表</span></div></el-col>
                            <el-col :span="3"><div>商品货号：</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="goods_no" placeholder="请输入商品货号"></el-input></div></el-col>
                            <el-col :span="3"><div>品牌名称：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="brand_name" placeholder="请输入品牌名称"></el-input></div></el-col>
                            <el-col :span="3"><div>商品名称：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                        </el-row>
                        <el-row class="fontCenter MB_ten lineHeightForty">
                            <el-col :span="3" :offset="3"><div>货品简称：</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="goods_short_name" placeholder="请输入货品简称"></el-input></div></el-col>
                            <el-col :span="3"><div>商品条码：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="barcode" placeholder="请输入商品条码"></el-input></div></el-col>
                            <el-col :span="3"><div>仓库名：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="warehouse_name" placeholder="请输入仓库名"></el-input></div></el-col>
                            <el-col :span="3">
                                <div>
                                    <span class="bgButton" @click="searchFrame()">点击搜索</span>&nbsp;&nbsp;&nbsp;
                                </div>
                            </el-col>
                        </el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig'></tableContent>
                        <notFound v-if="isShow"></notFound>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-col>
                </el-row>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      tableContent,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      tableContent:'',
      TCTitle:['商品名称','商品品牌','商品货号','货品简称','规格名称','商家编码','条形码','仓库名称','商品库存','成本价'],
      tableField:['goods_name','brand_name','goods_no','goods_short_name','spec_name','spec_no','barcode','warehouse_name','stock_num','cost_price'],//表格字段
      contentConfig:'[{"isShow":"false"},{"parameter":"false"}]',
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      goods_no:'',
      brand_name:'',
      goods_name:'',
      goods_short_name:'',
      barcode:'',
      warehouse_name:'',
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
        axios.post(vm.url+vm.$getErpGoodsListURL,
            {
                "goods_no":vm.goods_no,
                "brand_name":vm.brand_name,
                "goods_name":vm.goods_name,
                "goods_short_name":vm.goods_short_name,
                "barcode":vm.barcode,
                "warehouse_name":vm.warehouse_name,
                "page":vm.page,
                "pageSize":vm.pagesize,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close();
                vm.tableContent=JSON.stringify(res.data.erpGoodsList.data);
                vm.total=res.data.erpGoodsList.total;
            if(res.data.erpGoodsList.total>0){
                vm.isShow=false;
            }else if(res.data.erpGoodsList.total==0){
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    searchFrame(){
        let vm = this;
        vm.page=1;
        vm.getDataList();
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
    }
  }
}
</script>

<style>
</style>
