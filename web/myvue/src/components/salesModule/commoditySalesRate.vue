<template>
  <div class="commoditySalesRate_b">
    <!-- 商品实时动销率页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="commoditySalesRate">
                        <!-- <div class="title tableTitleStyle">
                            <span><span class="coarseLine MR_twenty"></span>商品实时动销率</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>   -->
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>商品实时动销率</span></div></el-col>
                            <el-col :span="3"><div>商品名称</div></el-col>
                            <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                            <el-col :span="3"><div>品牌名称</div></el-col>
                            <el-col :span="3"><div><el-input v-model="brand_name" placeholder="请输入品牌名称"></el-input></div></el-col>
                            <el-col :span="3"><div>商家编码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                            <el-col :span="2"><span class="bgButton" @click="getCommodityData()">搜索</span></el-col>
                        </el-row>
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="3" :offset="3"><div>商品规格码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                            <el-col :span="3"><div>商品代码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_prd_no" placeholder="请输入商品代码"></el-input></div></el-col>
                            <el-col :span="3"><div>参考码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_ref_no" placeholder="请输入参考码"></el-input></div></el-col>
                        </el-row>
                        <el-row>
                            <table style="width:100%;text-align:center" border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                    <th>商品名称</th>
                                    <th>品牌</th>
                                    <th>商家编码</th>
                                    <th>商品代码</th>
                                    <th>参考码</th>
                                    <th>商品规格码</th>
                                    <th>需求总量</th>
                                    <th>销售总量</th>
                                    <th>动销率</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in tableData">
                                    <td  class="ellipsis fontLift" style="width:380px;">
                                        <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                        <span>&nbsp;&nbsp;{{item.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td>{{item.brand_name}}</td>
                                    <td>{{item.erp_merchant_no}}</td>
                                    <td>{{item.erp_prd_no}}</td>
                                    <td>{{item.erp_ref_no}}</td>
                                    <td>{{item.spec_sn}}</td>
                                    <td>{{item.total_need_num}}</td>
                                    <td>{{item.total_sale_num}}</td>
                                    <td>{{item.rate_of_pin}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </el-row>
                        <div v-if="isShow" style="text-align: center;line-height: 50px;"><img class="notData" src="../../image/notData.png"/></div>
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
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        total:0,
        search:'',
        isShow:false,
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        //搜索
        goods_name:'',
        brand_name:'',
        erp_merchant_no:'',
        spec_sn:'',
        erp_prd_no:'',
        erp_ref_no:'',
      };
    },
    mounted(){
        this.getCommodityData()
        // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        //获取商品实时动销率列表
        getCommodityData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            let load = Loading.service({fullscreen: true, text: '拼命加载中....'})
            axios.get(vm.url+vm.$goodsRtMovePercentURL+"?keywords="+vm.search+"&page="+vm.page+"&pagesize="+vm.pagesize+"&goods_name="+vm.goods_name
            +"&brand_name="+vm.brand_name+"&erp_merchant_no="+vm.erp_merchant_no+"&spec_sn="+vm.spec_sn+"&erp_prd_no="+vm.erp_prd_no+"&erp_ref_no="+vm.erp_ref_no,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                load.close()
                if(res.data.code=='1002'){
                    vm.isShow=true;
                    vm.tableData=[];
                    vm.total=0;
                }else{
                    vm.tableData=res.data.data.data;
                    vm.total=res.data.data.total;
                }
                
            }).catch(function (error) {
                load.close()
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
            vm.getCommodityData();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getCommodityData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getCommodityData()
        },
    }
}
</script>


<style scoped lang=less>
@import '../../css/salesModule.less';
.commoditySalesRate .ellipsis span{
    width: 380px;
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
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