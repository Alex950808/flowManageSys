<template>
  <div class="demandManageDetails_b">
      <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="demandManageDetails">
                        <div class="tableTitleStyle">
                            <span class="bgButton" @click="backUpPage()">返回上一级</span>
                        </div>
                        <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>商品标签</th>
                                    <th class="ellipsis" rowspan="2">商品名称</th>
                                    <th rowspan="2">商品代码</th>
                                    <th rowspan="2">商家编码</th>
                                    <th rowspan="2">商品规格码</th>
                                    <th rowspan="2">商品总需求量</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in tableData">
                                    <td>
                                        <span v-for="labelInfo in item.goods_label_list" class="PR_ten">
                                            <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                        </span>
                                    </td>
                                    <td class="overOneLinesHeid widthThreeFiveHundred">
                                        <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                        <span class="widthThreeFiveHundred" style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td>{{item.erp_prd_no}}</td>
                                    <td>{{item.erp_merchant_no}}</td>
                                    <td>{{item.spec_sn}}</td>
                                    <td>{{item.goods_num}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                    </div>
                </el-col>
            </el-row>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  data(){
    return{
        url: `${this.$baseUrl}`,
        tableData:[],
        titleData:'',//title数据
        search:'',//用户输入数据
        total:0,//页数默认为0
        saleMarRate:'',
        spec_sn:'',
        radio:2,
        index:'',
        Cost:'',
        isShow:false,
        dialogVisible:false,
        headersStr:'',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDatailsData();
  },
  methods:{
    //获取列表数据
    getDatailsData(){
        let vm = this;
        axios.get(vm.url+vm.$getDemandDetialURL+"?demand_sn="+vm.$route.query.demand_sn,
            {
              headers:vm.headersStr,
            }
        ).then(function(res){
            vm.tableData=res.data.data;
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //回上一级页面
    backUpPage(){
        let vm=this;
        if(vm.$route.query.isPurchaseList){
            vm.$router.push('/waitAllotDemand');
        }
        if(vm.$route.query.isAleradyAll){
            vm.$router.push('/alreadyAllotDemand');
        }
    },
    labelStyle(color){
        return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
    }
  }
}
</script>
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
