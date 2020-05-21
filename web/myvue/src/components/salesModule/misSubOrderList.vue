<template>
  <div class="misSubOrderList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                    <table class="fontCenter MB_twenty" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                                <tr class="L_H_F">
                                    <td class="widthThreeHundred">子单号</td>
                                    <td class="widthThreeHundred">需求单号</td>
                                    <td class="widthThreeHundred">现货单号</td>
                                    <td class="widthThreeHundred">外部单号</td>
                                    <td class="widthThreeHundred">是否分单</td>
                                    <td class="widthOneFiveHundred">订单状态</td>
                                    <td class="widthOneFiveHundred">需求账号</td>
                                    <td class="widthOneFiveHundred">交货日期</td>
                                    <td class="widthThreeHundred">创建时间</td>
                                </tr>
                                <tr class="L_H_F" v-for="item in tableContent">
                                    <td class="widthThreeHundred"><span class="notBgButton" v-if="item.sub_order_sn!=undefined" @click="goDetail('1',item.sub_order_sn)">{{item.sub_order_sn}}</span></td>
                                    <td class="widthThreeHundred"><span class="notBgButton" v-if="item.demandInfo.demand_sn!=undefined" @click="goDetail('2',item.demandInfo.demand_sn)">{{item.demandInfo.demand_sn}}</span></td>
                                    <td class="widthThreeHundred"><span class="notBgButton" v-if="item.spotInfo.spot_order_sn!=undefined" @click="goDetail('3',item.spotInfo.spot_order_sn)">{{item.spotInfo.spot_order_sn}}</span></td>
                                   <td class="widthThreeHundred">{{item.external_sn}}</td>
                                    <td class="widthThreeHundred">{{item.is_submenu}}</td>
                                    <td class="widthOneFiveHundred">{{item.status}}</td>
                                    <td class="widthOneFiveHundred">{{item.sale_user_account}}</td>
                                    <td class="widthOneFiveHundred">{{item.entrust_time}}</td>
                                    <td class="widthThreeHundred">{{item.create_time}}</td>
                                </tr>
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
import { Loading } from 'element-ui'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
// import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
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
      tableTitle:this.$route.query.mis_order_sn+'_子单列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/MISorderListDetails?isMISOrder=isMISOrder&mis_order_sn='+this.$route.query.mis_order_sn+'"}]',
      tableContent:[],
   //   TCTitle:['子单号','需求单号','现货单号','是否分单','订单状态','需求账号','交货日期','操作'],
    //   tableField:['sub_order_sn','demandInfo.demand_sn','is_submenu','status','sale_user_account','entrust_time'],//表格字段
    //   contentConfig:'[{"isShow":"isSubmenuAndDD"},{"parameter":"sub_order_sn"}]',
      isShow:false,
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
        axios.post(vm.url+vm.$getMisOrderSplitURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
            },
            {
                headers:vm.headersStr,
            }
        )
        .then(function(res){
            vm.loading.close();
            if(res.data.misSubOrderList.length!=0){
                vm.tableContent=res.data.misSubOrderList;
                vm.isShow=false;
            }else{
                vm.isShow=true;
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
    goDetail(index,sn){
        let vm = this;
        if(index==='1'){
            vm.$router.push('/getSubDetail?sub_order_sn='+sn+'&isMisSub=isMisSub&mis_order_sn='+vm.$route.query.mis_order_sn);
        }else if(index==='2'){
            vm.$router.push('/demandManageDetails'+'?demand_sn='+sn+'&demand=demand');
        }else if(index==='3'){
            vm.$router.push('/getSpotDetail'+'?spot_order_sn='+sn);
        }
    }
  }
}
</script>

<style scoped>
@import '../../css/publicCss.css';
</style>
