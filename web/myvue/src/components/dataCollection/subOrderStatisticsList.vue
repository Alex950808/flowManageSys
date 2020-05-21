<template>
  <div class="subOrderStatisticsList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span class="bgButton MR_twenty" @click="backToUpPage()">返回上一页</span>
                        <span class="coarseLine MR_ten"></span>子单统计列表({{status}})
                    </div>
                    <div class="t_i">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <thead>
                                        <tr>
                                            <td :style="labelWidth()">商品标签</td>
                                            <td width="200px;">商品名称</td>
                                            <td width="200px;">子单单号</td>
                                            <td width="150px;">商品规格码</td>
                                            <td width="150px;">商家编码</td>
                                            <td width="100px;">需求量</td>
                                            <td width="100px;">美金原价</td>
                                            <td width="100px;">待采量</td>
                                            <td width="100px;">差异数</td>
                                            <td width="80px;">分货数量</td>
                                            <td width="80px;">现货数量</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc"  @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                <tbody>
                                    <tr v-for="goodsInfo in tableData">
                                        <td :style="labelWidth()">
                                            <span v-for="labelInfo in goodsInfo.goods_label_list" class="PR_ten">
                                                <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                            </span>
                                        </td>
                                        <td class="overOneLinesHeid" style="width:200px;">
                                            <el-tooltip class="item" effect="light" :content="goodsInfo.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;">{{goodsInfo.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td width="200px;">{{goodsInfo.sub_order_sn}}</td>
                                        <td width="150px;">{{goodsInfo.spec_sn}}</td>
                                        <td width="150px;">{{goodsInfo.erp_merchant_no}}</td>
                                        <td width="100px;">{{goodsInfo.goods_number}}</td>
                                        <td width="100px;">{{goodsInfo.spec_price}}</td>
                                        <td width="100px;">{{goodsInfo.wait_buy_num}}</td>
                                        <td width="100px;">{{goodsInfo.diff_num}}</td>
                                        <td width="80px;">{{goodsInfo.sort_num}}</td>
                                        <td width="80px;">{{goodsInfo.spot_num}}</td>
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
import notFound from '@/components/UiAssemblyList/notFound'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      notFound,
      tableTitle,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      status:'',
      num:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        if(vm.$route.query.status=='1'){
            vm.status='实时'
        }else if(vm.$route.query.status=='2'){
            vm.status='最终'
        }
        let tableData=JSON.parse(sessionStorage.getItem('tableData'));
        if(tableData=='1'){
            vm.getGoodsData();
            return false;
        };
        vm.tableData=tableData;
        var recLength=[];
        if(vm.tableData!=''){
            vm.tableData.forEach(element=>{
                recLength.push(element.goods_label_list.length);
            })
        }      
        var j=recLength[0];
        for(var i=0;i<=recLength.length;i++){
            if(recLength[i+1]>=j){
                j=recLength[i+1]
            }
        } 
        vm.num = j;
        tableStyleByDataLength(vm.tableData.length,15);
        sessionStorage.setItem("tableData",'1');
    },
    getGoodsData(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$subOrderStatisticsListURL+"?sub_order_sn="+vm.$route.query.sub_order_sn+"&demand_sort_status=1",
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code=='1000'){
                vm.tableData=res.data.data;
                var recLength=[];
                if(vm.tableData!=''){
                    vm.tableData.forEach(element=>{
                        recLength.push(element.goods_label_list.length);
                    })
                }      
                var j=recLength[0];
                for(var i=0;i<=recLength.length;i++){
                    if(recLength[i+1]>=j){
                        j=recLength[i+1]
                    }
                } 
                vm.num = j;
                tableStyleByDataLength(vm.tableData.length,15);
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            load.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    backToUpPage(){
        let vm = this;
        if(vm.$route.query.status=='1'){
            vm.$router.push('/orderCurrentStatisticsList');
        }else if(vm.$route.query.status=='2'){
            vm.$router.push('/orderEndStatisticsList');
        }
    },
    labelStyle(color){
        return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
    },
    labelWidth(){
        let vm = this;
        let width=vm.num*60;
        return "width:"+width+"px";
    },
    tableWidth(){
        let vm = this;
        let width = vm.num*60+1500
        return "width:"+width+"px";
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
  }
}
</script>
<style>
.subOrderStatisticsList .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.subOrderStatisticsList .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.subOrderStatisticsList .ee{width:100%!important; width:100%; text-align: center;}
.subOrderStatisticsList .t_i_h table{width:1630px;}
.subOrderStatisticsList .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.subOrderStatisticsList .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.subOrderStatisticsList .cc table{width:1630px; }
.subOrderStatisticsList .cc table td{ text-align:center}
</style>
<style scoped lang=less>
table{
    border: 1px solid #ebeef5;
    tr{
        line-height: 40px;
        th,td{
            border-bottom: 1px solid #ebeef5;
            border-left: 1px solid #ebeef5;
        }
    }
    tr:nth-child(even){
        background:#fafafa;
    }
    tr:hover{
        background:#ced7e6;
    }
}
</style>
