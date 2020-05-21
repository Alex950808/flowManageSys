<template>
  <div class="batchSetting_b">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="batchSetting bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>待核价批次列表</span></div></el-col>
                            <el-col :span="3"><div>合单单号</div></el-col>
                            <el-col :span="3"><div><el-input v-model="purchaseSn" placeholder="请输入合单单号"></el-input></div></el-col>
                            <el-col :span="3"><div>购买日</div></el-col>
                            <el-col :span="3"><div>
                                <el-date-picker style="width:180px;" v-model="buyTime" type="date" value-format="yyyy-MM-dd" placeholder="请选择购买日"></el-date-picker>
                                </div></el-col>
                            <el-col :span="3"><div>提货日</div></el-col>
                            <el-col :span="3"><div>
                                <el-date-picker style="width:180px;" v-model="deliveryTime" type="date" value-format="yyyy-MM-dd" placeholder="请选择提货日"></el-date-picker>
                                </div></el-col>
                            <el-col :span="2"><span class="bgButton" @click="getBatchData()">搜索</span></el-col>
                        </el-row>
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="3" :offset="3"><div>批次单号</div></el-col>
                            <el-col :span="3"><div><el-input v-model="realPurchaseSn" placeholder="请输入批次单号"></el-input></div></el-col>
                            <el-col :span="3"><div>到货日</div></el-col>
                            <el-col :span="3"><div>
                                <el-date-picker style="width:180px;" v-model="arriveTime" type="date" value-format="yyyy-MM-dd" placeholder="请选择到货日"></el-date-picker>
                                </div></el-col>
                        </el-row>
                        <div class="content" v-for="item in BatchData">
                            <div class="content_text lineHeightForty">
                                <el-col :span="21">
                                <!-- <span class="stage">{{item.title_info.purchase_id}}期</span><span class="number">{{item.title_info.purchase_sn}}</span> -->
                                    <span v-if="item.title_info.purchase_id!=null" class="stage">{{item.title_info.purchase_id}}期</span>
                                    <span v-if="item.title_info.purchase_sn!=null" class="stage ML_twenty">汇总需求单号：{{item.title_info.purchase_sn}}</span>
                                    <span v-if="item.title_info.sum_demand_name!=null" class="stage ML_twenty">合期名称：{{item.title_info.sum_demand_name}}</span>
                                </el-col>
                                <el-col :span="3">
                                </el-col>
                            </div>
                            <el-row>
                                <el-col :span="12">
                                    <table class="table-one">
                                        <tr>
                                            <td>实采总数({{item.title_info.real_buy_num}})</td>
                                            <td>自提数量({{item.title_info.zt_num}}批({{item.title_info.zt_goods_num}}件))</td>
                                            <td>邮寄数量({{item.title_info.yj_num}}批({{item.title_info.yj_goods_num}}件))</td>
                                        </tr>
                                    </table>
                                </el-col>
                                <el-col :span="4" :offset="8">
                                </el-col>
                            </el-row>
                            <table class="table-two">
                                <tr>
                                    <td class="widthTwoHundred">批次单号</td>
                                    <td class="widthOneHundred">采购数量</td>
                                    <td class="widthTwoHundred">提货日</td>
                                    <td class="widthTwoHundred">港口</td>
                                    <td class="widthTwoHundred">自提/邮寄</td>
                                    <td class="widthTwoHundred">渠道</td>
                                    <td class="widthTwoHundred">方式</td>
                                    <td class="widthTwoHundred">操作</td>
                                </tr>
                            </table>
                            <table class="table-two" v-for="realList in item.real_list">
                                
                                <tr v-for="(real,index) in realList">
                                    <td class="widthTwoHundred">{{real.real_purchase_sn}}</td>
                                    <td class="widthOneHundred">{{real.total_buy_num}}件</td>
                                    <td class="widthTwoHundred" title="提货时间">{{real.delivery_time}}</td>
                                    <td class="widthTwoHundred">{{real.port_name}}</td>
                                    <td class="widthTwoHundred">
                                        <span v-if="real.path_way==0">自提</span>
                                        <span v-if="real.path_way==1">邮寄</span>
                                        <span v-else></span>
                                    </td>
                                    <td class="widthTwoHundred">{{real.channels_name}}</td>
                                    <td class="widthTwoHundred">{{real.method_name}}</td>
                                    <td class="widthTwoHundred"  v-if="real.batch_cat!='2'||index==0">
                                        <span v-if="real.is_display==1" class="bgButton" @click="viewDetail(real.real_purchase_sn,item.title_info.purchase_sn,real.group_sn,real.is_mother,real.path_way)">查看详情</span>
                                        <span v-if="real.is_display==0" class="grBgButton">查看详情</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-col>
                </el-row>
            </div>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '@/components/UiAssemblyList/searchBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      searchBox,
      fontWatermark
  },
  data(){
    return{
        url: `${this.$baseUrl}`,
        BatchData:[] ,  //批次信息 
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据
        loading:'',
        isShow:false,
        dialogVisible:false,
        purchase_sn:'',
        real_purchase_sn:'',
        delivery_time:'',
        arrive_time:'',
        batch:'',
        batchs:[],
        show:false,
        headersStr:'',
        contentStr:"请您确认是否要设置批次？",
        //搜索
        purchaseSn:'',
        realPurchaseSn:'',
        buyTime:'',
        deliveryTime:'',
        arriveTime:'',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getBatchData();
  },
  methods:{
    getBatchData(){
            let vm=this;
            let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
            if(vm.buyTime==null){
                vm.buyTime='';
            }
            if(vm.deliveryTime==null){
                vm.deliveryTime='';
            }
            if(vm.arriveTime==null){
                vm.arriveTime='';
            }
            axios.get(vm.url+vm.$watiPricingBatchListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&purchase_sn="+vm.purchaseSn+"&real_purchase_sn="+vm.realPurchaseSn
            +"&buy_time="+vm.buyTime+"&delivery_time="+vm.deliveryTime+"&arrive_time="+vm.arriveTime,
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                load.close();
                vm.BatchData=res.data.data.batch_total_list;
                vm.total=res.data.data_num
                if(res.data.code==1000){
                    vm.isShow=false;
                }else if(res.data.code==1002){
                    vm.isShow=true;
                }
            }).catch(function (error) {
                load.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
      //查看详情
      viewDetail(real_purchase_sn,purchase_sn,group_sn,is_mother,path_way){
          let vm=this;
          vm.$router.push('/watiPricingBatchDetail?real_purchase_sn='+real_purchase_sn+"&group_sn="+group_sn+"&purchase_sn="+purchase_sn+"&is_mother="+is_mother+"&path_way="+path_way);
      },
        //搜索框
      searchFrame(e){
          let vm=this;
          vm.search=e;
          vm.page=1;
          vm.getBatchData();
      },
        //取消设置
        Unset(){
            let vm=this;
            vm.purchase_sn='';
            vm.real_purchase_sn='';
            vm.delivery_time='';
            vm.arrive_time='';
            vm.batch='';
        },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.pagesize=val
          vm.getBatchData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.page=val
          vm.getBatchData()
      },
  }
}
</script>

<style scoped>
.batchSetting_b .xiuzheng{
    width: 88px;
    height: 30px;
    line-height: 30px;
    border-radius: 10px;
    display: inline-block;
    background-color: #4677c4;
    color: #fff;
}
.batchSetting .el-icon-close{
    margin-left: 270px;
}
</style>
<style scoped lang=less>
@import '../../css/taskModule.less';
</style>
