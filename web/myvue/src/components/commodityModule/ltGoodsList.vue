<template>
  <div class="somePageName">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="3"><div><span class="fontWeight"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;乐天商品列表</span></div></el-col>
                            <el-col :span="3"><div>乐天商品码：</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="lt_prd_no" placeholder="请输入乐天商品码"></el-input></div></el-col>
                            <el-col :span="3"><div>品牌名称：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="brand_name" placeholder="请输入品牌名称"></el-input></div></el-col>
                            <el-col :span="3"><div>商品名称：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                            <el-col :span="3">
                                <div>
                                    <span class="bgButton" @click="getDataList()">点击搜索</span>&nbsp;&nbsp;&nbsp;
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="fontCenter MB_ten lineHeightForty">
                            <el-col :span="3" :offset="3"><div>商品代码：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_prd_no" placeholder="请输入商品代码"></el-input></div></el-col>
                            <el-col :span="3"><div>参考码：</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_ref_no" placeholder="请输入参考码"></el-input></div></el-col>
                            <!-- <el-col :span="3"><div>其他：</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="query_sn" placeholder="请输入商家编码"></el-input></div></el-col> -->
                        </el-row>
                        <table class="fontCenter w_ratio" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <th style="width:200px;">商品名称</th>
                                <th class="widthTwoHundred">商品分类</th>
                                <th style="width:100px;">品牌名称</th>
                                <th style="width:200px;">品牌中文名</th>
                                <!-- <th class="widthTwoHundred">获取日期</th> -->
                                <th class="widthOneFiveHundred">乐天商品码</th>
                                <th class="widthOneHundred">参考码</th>
                                <th class="widthOneHundred">商品代码</th>
                                <th class="widthOneFiveHundred">人民币价格</th>
                                <th class="widthTwoHundred">美金原价</th>
                                <th class="widthOneHundred">美金折扣价</th>
                                <th class="widthTwoHundred">商品重量</th>
                                <th style="width:100px;">操作</th>
                            </tr>
                            <tr  v-for="item in tableData">
                                <td class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                    <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                </td>
                                <td class="widthTwoHundred">
                                    <span v-if="item.goods_type==1">护肤</span>
                                    <span v-else-if="item.goods_type==2">彩妆</span>
                                    <span v-else-if="item.goods_type==3">香水</span>
                                </td>
                                <td class="overOneLinesHeid" style="width:100px;" :title="item.brand_name">
                                    <span style="-webkit-box-orient: vertical;width:100px;">{{item.brand_name}}</span>
                                </td>
                                <td class="widthTwoHundred">{{item.brand_cn_name}}</td>
                                <!-- <td class="widthTwoHundred">{{item.download_date}}</td> -->
                                <td class="widthOneFiveHundred">{{item.lt_prd_no}}</td>
                                <td class="widthOneHundred">{{item.erp_ref_no}}</td>
                                <td class="widthOneHundred">{{item.erp_prd_no}}</td>
                                <td class="widthOneFiveHundred">{{item.cny_price}}</td>
                                <td class="widthTwoHundred">{{item.spec_price}}</td>
                                <td class="widthOneHundred">{{item.spec_discount_price}}</td>
                                <td class="widthTwoHundred">{{item.spec_weight}}</td>
                                <td style="width:100px;">
                                    <img class="Cursor" @click="getChartData(item.lt_prd_no)" src="../../image/Statistics.png"/>
                                </td>
                            </tr>
                        </table>
                        <el-dialog title="乐天商品美金原价信息" :visible.sync="dialogVisiblespecPrice" width="800px">
                            <div id="myChart" v-show="!show" :style="{width: '750px', height: '300px'}"></div>
                            <div id="main" v-show="show" style="width: 750px; height: 300px;line-height:300px;text-align: center;"><i class="el-icon-loading"></i></div>
                        </el-dialog>
                        <notFound v-if="isShow"></notFound>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
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
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //搜索字段
      lt_prd_no:'',
      brand_name:'',
      goods_name:'',
    //   query_sn:'',
      erp_prd_no:'',
      erp_ref_no:'',
      //美金原价信息展示
      dialogVisiblespecPrice:false,
      chartData:'',
      show:false,
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$ltGoodsListURL+"?lt_prd_no="+vm.lt_prd_no+"&brand_name="+vm.brand_name+"&goods_name="+vm.goods_name
        +"&erp_prd_no="+vm.erp_prd_no+"&erp_ref_no="+vm.erp_ref_no,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.goods_info;
                vm.total=res.data.data.total_num;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.tableData=[];
                vm.total=0;
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            loadltGoodsListltGoodsList.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    getChartData(number){
        let vm = this;
        // let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        vm.dialogVisiblespecPrice=true;
        // $("#myChart").hide()
        vm.show=!vm.show;
        axios.get(vm.url+vm.$ltGoodsSpecPriceInfoURL+"?lt_prd_no="+number,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            console.log(res);
            // load.close();
            if(res.data.code=="1000"){
                vm.chartData=res.data.data;
                vm.drawLine()
            }else{
                $("#myChart").html(res.data.msg)
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    drawLine(){
        let vm = this;
        // 基于准备好的dom，初始化echarts实例
        // $("#myChart").show()
        console.log(1);
        vm.show=!vm.show;
        let myChart = this.$echarts.init(document.getElementById('myChart'))
        myChart.clear();
        // 绘制图表
        myChart.setOption({
            title: { text: vm.chartData.goods_info.goods_name },
            tooltip: {
                trigger: 'axis'
            },
            // legend: {
            //     data:['渠道积分','渠道余款']
            // },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: vm.chartData.date_arr
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name:'美金原价',
                    type:'line',
                    stack: '总量',
                    data:vm.chartData.spec_price_arr
                }
                // {
                //     name:'渠道余款',
                //     type:'line',
                //     stack: '总量',
                //     data:vm.money_log
                // },
            ]
        });
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
  }
}
</script>

<style>
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
