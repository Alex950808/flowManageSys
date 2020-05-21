<template>
  <div class="predictDetail">
    <!-- 预采单详情页面 -->
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <!-- <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig'></tableContent> -->
                    <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>商品标签</th>
                                <th>商品名称</th>
                                <th>商品代码</th>
                                <th>商家编码</th>
                                <th>商品规格码</th>
                                <th>商品总需求量</th>
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
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
import tableTitle from '@/components/UiAssemblyList/tableTitle'
// import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      tableTitle,
      // tableContent,
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      tableTitle:'预采需求单详情',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/predictDemandList"}]',
      // tableContent:'', 
      // TCTitle:['商品名称','商品规格码','商家编码','商品代码','总需求数量'],
      // tableField:['goods_name','spec_sn','erp_merchant_no','erp_prd_no','goods_num'],
      // contentConfig:'[{"isShow":"false"},{"parameter":"false"}]',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
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
