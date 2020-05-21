<template>
  <div class="profitDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle"><span class="bgButton MR_twenty" @click="goBack()">返回上一页</span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;毛利数据详情
                        <span class="ML_Four">*共{{tableData.profit_detail.length}}条数据</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="d_table()">下载毛利数据</span>
                    </div>
                    <div class="t_i">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <thead>
                                        <tr>
                                            <th class="widthTwoHundred">毛利单号</th>
                                            <th class="widthTwoHundred">批次单号</th>
                                            <th class="w_110">渠道名称</th>
                                            <th class="widthTwoHundred">商品名称</th>
                                            <th class="widthTwoHundred">商品规格码</th>
                                            <th class="widthTwoHundred">商家编码</th>
                                            <th class="widthTwoHundred">商品代码</th>
                                            <th class="widthOneFiveHundred">参考码</th>
                                            <th class="w_80">美金原价</th>
                                            <th class="w_80">lvip价格</th> 
                                            <th class="w_60">实采数</th>
                                            <th class="widthOneFiveHundred">毛利结算价格类型</th>
                                            <th class="widthOneHundred" v-for="item in tableData.title_field">{{item.cat_name}}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc"  @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                <tbody>
                                    <tr v-for="(item,index) in tableData.profit_detail">
                                        <td class="widthTwoHundred">{{item.profit_sn}}</td>
                                        <td class="widthTwoHundred">{{item.real_purchase_sn}}</td>
                                        <td class="w_110">{{item.channels_name}}</td>
                                        <td class="widthTwoHundred overOneLinesHeid"> 
                                            <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                        </td>
                                        <td class="widthTwoHundred">{{item.spec_sn}}</td>
                                        <td class="widthTwoHundred">{{item.erp_merchant_no}}</td>
                                        <td class="widthTwoHundred">{{item.erp_prd_no}}</td>
                                        <td class="widthOneFiveHundred">{{item.erp_ref_no}}</td>
                                        <td class="w_80">{{item.spec_price}}</td>
                                        <td class="w_80">{{item.lvip_price}}</td>
                                        <td class="w_60">{{item.day_buy_num}}</td>
                                        <td class="widthOneFiveHundred">{{item.margin_payment}}</td>
                                        <td class="widthOneHundred"  v-for="info in tableData.title_field">{{item[info.cat_code]}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                  <notFound v-if="isShow"></notFound>
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
import { tableWidth } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { exportsa } from '@/filters/publicMethods.js'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
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
      selectStr:['商品名称','毛利单号','渠道名称','批次单号','商品规格码','商家编码','商品代码','美金原价','lvip价格','实采数','参考码','毛利结算价格类型']
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.$store.commit('selectList', this.selectStr);
  },
  methods:{
    getDataList(){
        let vm = this;
        let profit_sn_arr = vm.$route.query.profit_sn.split(",")
        axios.post(vm.url+vm.$profitDetailURL,
            {
                "profit_sn_arr":profit_sn_arr
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            tableStyleByDataLength(res.data.data.profit_detail.length,15);
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                res.data.data.title_field.forEach(element => {
                    vm.selectStr.push(element.cat_name)
                });
                vm.isShow=false;
            }else if(res.data.code==1002){
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
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    checkTime(time){
        if(time<10){
            time = '0'+time
        }
        return time
    },
    d_table(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$downLoadProfitInfoURL+'?profit_sn_arr='+vm.$route.query.profit_sn+'&token='+headersToken);
    },
    goBack(){
        let vm = this;
        vm.$router.push('/profitList');
    }
  },
  computed:{
    selectTitle(checkedCities){
       return selectTitle(checkedCities)
    },
    tableWidth(){
        return tableWidth(this.$store.state.select);
    },
  }
}
</script>

<style>
.profitDetail .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.profitDetail .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.profitDetail .ee{width:100%!important; width:100%; text-align: center;}
.profitDetail .t_i_h table{width:1530px;}
.profitDetail .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.profitDetail .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.profitDetail .cc table{width:1530px; }
.profitDetail .cc table td{ text-align:center}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
