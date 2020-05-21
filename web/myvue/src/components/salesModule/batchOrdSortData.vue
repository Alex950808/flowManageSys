<template>
  <div class="batchOrdSortData">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle title">
                        <router-link  to='/batchOrderList'><span class="bgButton MR_ten">返回上一级</span></router-link>
                        <span><span class="coarseLine MR_ten"></span>批次分货详情</span>
                    </div>
                    <el-row style="margin-bottom: 10px;">
                        <span class="fontWeight F_S_twenty">订单信息：</span>
                    </el-row>
                    <div style="margin-bottom: 10px;">
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>合单单号：{{batchTitleData.purchase_sn}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>实采单号：{{batchTitleData.real_purchase_sn}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>到货日期：{{batchTitleData.arrive_time}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>创建时间：{{batchTitleData.create_time}}</span></div></el-col>
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>提货时间：{{batchTitleData.delivery_time}}</span>&nbsp;&nbsp;</div></el-col>
                            <el-col :span="5" :offset="1">
                                <div>
                                    自提/邮寄：
                                    <span v-if="batchTitleData.path_way==0">自提</span>
                                    <span v-if="batchTitleData.path_way==1">邮寄</span>
                                    <span v-else></span>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                    <el-row style="margin-bottom: 10px;">
                        <span class="fontWeight F_S_twenty">商品信息：</span>
                    </el-row>
                    <el-row>
                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <thead>
                                            <tr>
                                                <th style="width:200px">需求单号</th>
                                                <th class="ellipsis" style="width:200px;">商品名称</th>
                                                <th style="width:200px">商家编码</th>
                                                <th style="width:200px">商品规格码</th>
                                                <th style="width:90px">需求量</th>
                                                <th style="width:90px">已分配数</th>
                                                <th style="width:90px">可分货数量</th>
                                                <th style="width:90px">默认值</th>
                                                <th style="width:90px">还需分配数</th>
                                                <th style="width:100px">手动调整分配</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tbody>
                                        <tr v-for="(item,index) in tableData">
                                            <td style="width:200px">{{item.demand_sn}}</td>
                                            <td class="overOneLinesHeid fontLift" style="width:200px;" :rowspan="rowspanList[index]" :style="mergeDisplay(item.goods_name)">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="top">
                                                <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:200px" :rowspan="rowspanList[index]" :style="mergeDisplay(item.goods_name)">{{item.erp_merchant_no}}</td>
                                            <td style="width:200px" :rowspan="rowspanList[index]" :style="mergeDisplay(item.goods_name)">{{item.spec_sn}}</td>
                                            <td style="width:90px">
                                                <span v-if="item.goods_num==''">0</span>
                                                <span v-else-if="item.goods_num==null">0</span>
                                                <span v-else>{{item.goods_num}}</span>
                                            </td>
                                            <td style="width:90px">
                                                <span v-if="item.yet_num==''">0</span>
                                                <span v-else-if="item.yet_num==null">0</span>
                                                <span v-else>{{item.yet_num}}</span>
                                            </td>
                                            <td style="width:90px" :rowspan="rowspanList[index]" :style="mergeDisplay(item.goods_name)">
                                                <span v-if="item.sort_num==''">0</span>
                                                <span v-else-if="item.sort_num==null">0</span>
                                                <span v-else>{{item.sort_num}}</span>
                                            </td>
                                            <td style="width:90px">
                                                <span v-if="item.default_num==''">0</span>
                                                <span v-else-if="item.default_num==null">0</span>
                                                <span v-else>{{item.default_num}}</span>
                                            </td>
                                            <td style="width:90px">
                                                <span v-if="item.still_need_num==''">0</span>
                                                <span v-else-if="item.still_need_num==null">0</span>
                                                <span v-else>{{item.still_need_num}}</span>
                                            </td>
                                            <td style="width:100px" class="handle_num">
                                                <input @change="handleSortNum(item.demand_sn,item.spec_sn,index)" style="width:50%" :name="'sort'+index" value="0"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </el-row>
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
import {tableStyleByDataLength} from '@/filters/publicMethods.js'
import { listSearchRepeat } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      batchTitleData:[],
      isShow:false,
      rowspanList:[],
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
        axios.post(vm.url+vm.$batchOrdSortDataURL,
            {
                "sum_demand_sn":vm.$route.query.sum_demand_sn,
                "real_purchase_sn":vm.$route.query.real_purchase_sn
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.batchSortData.length!=0){
                vm.tableData=res.data.batchSortData;
                vm.batchTitleData=res.data.batchInfo;
                tableStyleByDataLength(vm.tableData.length,15);
                vm.isShow=false;

                let goodsSpecList = [];
                vm.tableData.forEach(element=>{
                    goodsSpecList.push(element.spec_sn);
                })
                vm.rowspanList = listSearchRepeat(goodsSpecList);
                for(var i = 0;i<vm.rowspanList.length;i++){
                    if(vm.rowspanList[i]!=1){
                        for(var j = 0;j<vm.rowspanList[i]-1;j++){
                            vm.tableData[(i+j)+1].goods_name='display'
                        }
                    }
                }
            }else{
                vm.$message(res.data.msg);
                vm.isShow=true;
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    handleSortNum(demand_sn,spec_sn,index){
        let vm = this;
        let handleNum=$(".handle_num input[name=sort"+index+"]").val();
        axios.post(vm.url+vm.$handleSortNumURL,
            {
                "sum_demand_sn":vm.$route.query.sum_demand_sn,
                "real_purchase_sn":vm.$route.query.real_purchase_sn,
                "demand_sn":demand_sn,
                "spec_sn":spec_sn,
                "handle_num":handleNum,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.$message(res.data.msg);
            $(".handle_num input[name=sort"+index+"]").val(0)
            if(res.data.code=='2024'){
                vm.getDataList();
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
  },
  computed:{
     mergeDisplay(){
        return function(goods_name){
            if(goods_name=='display'){
                return "display: none;"
            }
        }
    }
  }
}
</script>

<style>
.batchOrdSortData .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.batchOrdSortData .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.batchOrdSortData .ee{width:100%!important; width:100%; text-align: center;}
.batchOrdSortData .t_i_h table{width:100%;}
.batchOrdSortData .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.batchOrdSortData .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.batchOrdSortData .cc table{width:100%; }
.batchOrdSortData .cc table td{ text-align:center}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
