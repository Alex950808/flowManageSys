<template>
  <div class="purchaseChannelStatisticsDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span class="bgButton MR_twenty" @click="backToUpPage()">返回上一页</span>
                        <span class="coarseLine MR_ten"></span>采购期渠道统计详情
                    </div>
                    <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>商品标签</th>
                                <th>商品名称</th>
                                <th>商家编码</th>
                                <th>商品代码</th>
                                <th>商品规格码</th>
                                <th>美金原价</th>
                                <th>实采数量</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>
                                    <span v-for="labelInfo in item.goods_label_list" class="PR_ten">
                                        <span class="PL_ten PR_ten B_R" :style="labelStyle(labelInfo.label_color)">{{labelInfo.label_name}}</span>
                                    </span>
                                </td>
                                <td class="overOneLinesHeid" style="width:250px;">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:250px;">{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td>{{item.erp_merchant_no}}</td>
                                <td>{{item.erp_prd_no}}</td>
                                <td>{{item.spec_sn}}</td>
                                <td>{{item.spec_price}}</td>
                                <td>{{item.total_allot_num}}</td>
                            </tr>
                        </tbody>
                    </table>
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
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getListData();
  },
  methods:{
    getListData(){
        let vm = this;
        let tableData=JSON.parse(sessionStorage.getItem('tableData'));
        if(tableData=='1'){
            vm.getDataList();
            return false;
        };
        vm.tableData=tableData.data.data;
        sessionStorage.setItem("tableData",'1');
    },
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$purchaseChannelStatisticsDetailURL+"?method_id="+vm.$route.query.method_id+"&channels_id="+vm.$route.query.channels_id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.isShow=true;
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
    labelStyle(color){
        return "background:"+color+";color:#fff;padding-top: 5px;padding-bottom: 5px;"
    },
    backToUpPage(){
        let vm = this;
        vm.$router.push('/purchaseChannelStatisticsList');
    },
  }
}
</script>

<style scoped>
@import '../../css/publicCss.css';
</style>
