<template>
  <div class="distributionOfGoods_b">
      <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="distributionOfGoods">
                        <div class="title">
                            <span class="bgButton" @click="goBack()">返回上一级</span>
                            <span class="upTitle">分配详情</span>
                        </div>
                        <table class="MB_twenty" style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th class="ellipsis">商品名称</th>
                                    <th>商品代码</th>
                                    <th>商家编码</th>
                                    <th>商品规格码</th>

                                    <th>差异数量</th>
                                    <th>采购部备注</th>
                                    <th>开始时间</th>
                                    <th>清点数量</th>
                                    <th>实际清点数</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in tableData">
                                    <td class="ellipsis" style="width:350px;">
                                        <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td>{{item.erp_prd_no}}</td>
                                    <td>{{item.erp_merchant_no}}</td>
                                    <td>{{item.spec_sn}}</td>

                                    <td>{{item.diff_num}}</td>
                                    <td>{{item.purchase_remark}}</td>
                                    <td>{{item.create_time}}</td>
                                    <td>{{item.allot_num}}</td>
                                    <!-- <td>{{item.real_allot_num}}</td> -->
                                    <td style="width:200px" class="cb_three"><input type="text" @change="changingDemandNum(item.spec_sn,index)" :name="index" :value="item.real_allot_num"/></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios';
import notFound from '@/components/UiAssemblyList/notFound';
import { Loading } from 'element-ui';
export default {
    components:{
      notFound,
    },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      isShow:false,
    }
  },
  mounted(){
    this.getUpPageData();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
      getUpPageData(){
        //   let vm=this;
        //   vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$getRealGoodsInfoURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn,
             {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            vm.loading.close()
            if(res.data.realGoodsInfo.lenght!=0){
                vm.tableData = res.data.realGoodsInfo;
                vm.show=false;
            }else{
                vm.tableData = [];
                vm.show=true;
            }
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
       //单个商品可采数量改变
        changingDemandNum(spec_sn,index){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            var real_allot_num=$(".cb_three input[name="+index+"]").val();
            // if(parseInt(allot_num)>parseInt(goods_num)){
            //     vm.open();
            //     vm.gettableData();
            //     return false;
            // }
            axios.get(vm.url+vm.$updateRealAllotNumURL+"?real_purchase_sn="+vm.$route.query.real_purchase_sn+"&spec_sn="+spec_sn+"&real_allot_num="+real_allot_num,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        goBack(){
            let vm = this;
            if(vm.$route.query.isReal){
                vm.$router.push('/realPurList');
            }
            if(vm.$route.query.isdpm){
                vm.$router.push('/purRealList');
            }
        }
  }
}
</script>

<style>
.distributionOfGoods_b .title{
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    font-weight:bold;
    padding-left: 35px;
    margin-top: 20px;
}
.distributionOfGoods_b .back{
    display: inline-block;
    width: 130px;
    height: 50px;
    background-color: #4677c4;
    color: #fff;
    line-height: 50px;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
}
.distributionOfGoods_b .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
.distributionOfGoods_b .ellipsis span{
    width: 350px;
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
