<template>
  <div class="sumDiffInfo">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle select">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;缺口统计详情</span>
                    </div>
                    <span v-if="!isShow" class="F_S_twenty fontWeight MT_twenty d_I_B MB_twenty">订单统计表：</span>
                    <div style="width: 100%;overflow: auto;">
                        <table cellpadding="0" cellspacing="0" border="0" class="fontCenter" :style="orderTableW">
                            <thead>
                                <tr>
                                    <th style="width:230px">总计</th>
                                    <th v-for="item in expire_time" style="width:230px">{{item}}</th>
                                </tr>
                                <tr>
                                    <th style="width:230px">
                                        <span style="width:50px;display: inline-block">订单数</span>
                                        <span>|</span>
                                        <span style="width:50px;display: inline-block">缺口数</span>
                                        <span>|</span>
                                        <span style="width:70px;display: inline-block">缺口金额</span>
                                    </th>
                                    <th v-for="item in expire_time" style="width:230px">
                                        <span style="width:50px;display: inline-block">订单数</span>
                                        <span>|</span>
                                        <span style="width:50px;display: inline-block">缺口数</span>
                                        <span>|</span>
                                        <span style="width:70px;display: inline-block">缺口金额</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width:300px">
                                        <span style="width:50px;display: inline-block">{{total_info['demand_num']}}</span>
                                        <span>|</span>
                                        <span style="width:50px;display: inline-block">{{total_info['diff_num']}}</span>
                                        <span>|</span>
                                        <span style="width:70px;display: inline-block">{{total_info['dg_diff_price']}}</span>
                                    </td>
                                    <td v-for="item in expire_time" style="width:300px">
                                        <span v-if="sd_goods_list.demand_info[item]!=undefined" style="width:50px;display: inline-block">{{sd_goods_list.demand_info[item].demand_num}}</span>
                                        <span v-if="sd_goods_list.demand_info[item]!=undefined">|</span>
                                        <span v-if="sd_goods_list.demand_info[item]!=undefined" style="width:50px;display: inline-block">{{sd_goods_list.demand_info[item].diff_num}}</span>
                                        <span v-if="sd_goods_list.demand_info[item]!=undefined">|</span>
                                        <span v-if="sd_goods_list.demand_info[item]!=undefined" style="width:70px;display: inline-block">{{sd_goods_list.demand_info[item].dg_diff_price}}.00</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <span v-if="!isShow" class="F_S_twenty fontWeight MT_twenty d_I_B MB_twenty">渠道统计表：</span>
                    <span class="bgButton MR_twenty" @click="selectcol()">选择列</span>
                    <el-row class="t_i fontCenter MT_ten">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <thead>
                                        <tr>
                                            <th v-if="selectTitle('商品名称')" style="width:200px" rowspan="2">商品名称</th>
                                            <th v-if="selectTitle('商品规格码')" style="width:160px" rowspan="2">商品规格码</th>
                                            <th v-if="selectTitle('商家编码')" style="width:160px" rowspan="2">商家编码</th>
                                            <th v-if="selectTitle('商品参考码')" style="width:180px" rowspan="2">商品参考码</th>
                                            <th v-if="selectTitle('美金原价')" style="width:90px" rowspan="2">美金原价</th>
                                            <th v-if="selectTitle('需求数')" style="width:100px" rowspan="2">需求数</th>
                                            <th v-if="selectTitle('需求金额')" style="width:100px" rowspan="2">需求金额</th>
                                            <th v-if="selectTitle('实采数')" style="width:90px" rowspan="2">实采数</th>
                                            <th v-if="selectTitle('采购缺口数')" style="width:100px" rowspan="2">采购缺口数</th>
                                            <th v-if="selectTitle('缺口金额')" style="width:100px" rowspan="2">缺口金额</th>
                                            <th v-if="selectTitle('采满率')" style="width:100px" rowspan="2">采满率</th>
                                            <th v-for="item in channel_arr" style="width:300px" v-if="selectTitle(item)">{{item}}</th>
                                        </tr>
                                        <tr>
                                            <th v-for="item in channel_arr" style="width:300px" v-if="selectTitle(item)">
                                                <span style="width:50px;display:inline-block;">可采数</span>
                                                <span>|</span>
                                                <span style="width:50px;display: inline-block">实采数</span>
                                                <span>|</span>
                                                <span style="width:50px;display: inline-block">缺口数</span>
                                                <span>|</span>
                                                <span style="width:70px;display: inline-block">缺口金额</span>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()"> 
                            <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                <tr v-for="(item,index) in sum_demand_detail">
                                    <td v-if="selectTitle('商品名称')" class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                        <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                    </td>
                                    <td v-if="selectTitle('商品规格码')" class="overOneLinesHeid" style="width:160px">
                                        <span style="-webkit-box-orient: vertical;width:160px">{{item.spec_sn}}</span>
                                    </td>
                                    <td v-if="selectTitle('商家编码')" class="overOneLinesHeid" style="width:160px">
                                        <span style="-webkit-box-orient: vertical;width:160px">{{item.erp_merchant_no}}</span>
                                    </td>
                                    <td v-if="selectTitle('商品参考码')" class="overOneLinesHeid" style="width:180px">
                                        <span style="-webkit-box-orient: vertical;width:180px">{{item.erp_ref_no}}</span>
                                    </td>
                                    <td v-if="selectTitle('美金原价')" style="width:90px">{{item.spec_price}}</td>
                                    <td v-if="selectTitle('需求数')" style="width:100px">{{item.goods_num}}</td>
                                    <td v-if="selectTitle('需求金额')" style="width:100px">{{item.sg_demand_price}}</td>
                                    <td v-if="selectTitle('实采数')" style="width:90px">{{item.real_num}}</td>
                                    <td v-if="selectTitle('采购缺口数')" style="width:100px">{{item.diff_num}}</td>
                                    <td v-if="selectTitle('缺口金额')" style="width:100px">{{item.sg_diff_price}}</td>
                                    <td v-if="selectTitle('采满率')" style="width:100px">{{item.sg_real_rate}}</td>
                                    <td v-for="channelInfo in channel_arr" style="width:300px" v-if="selectTitle(channelInfo)">
                                        <span v-if="item.channel_info[channelInfo]!=undefined" style="width:50px;display: inline-block">{{item.channel_info[channelInfo].may_num}}</span>
                                        <span v-if="item.channel_info[channelInfo]!=undefined">|</span>
                                        <span v-if="item.channel_info[channelInfo]!=undefined" style="width:50px;display: inline-block">{{item.channel_info[channelInfo].real_num}}</span>
                                        <span v-if="item.channel_info[channelInfo]!=undefined">|</span>
                                        <span v-if="item.channel_info[channelInfo]!=undefined" style="width:50px;display: inline-block">{{item.channel_info[channelInfo].diff_num}}</span>
                                        <span v-if="item.channel_info[channelInfo]!=undefined">|</span>
                                        <span v-if="item.channel_info[channelInfo]!=undefined" style="width:70px;display: inline-block">{{item.channel_info[channelInfo].diff_price}}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <selectCol :selectStr="selectStr" @selectTitle="selectTitle"></selectCol>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import selectCol from '@/components/UiAssemblyList/selectCol'
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
export default {
  components:{
      notFound,
      selectCol,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      //选择列
      selectStr:["商品名称","商品规格码","商家编码","商品参考码",'美金原价',"需求数",'需求金额',"实采数","采购缺口数",'缺口金额',"采满率"],
      //订单统计表参数
      sd_goods_list:[],
      expire_time:[],
      total_info:[],
      //渠道统计表参数
      sum_demand_detail:[],
      channel_arr:[],
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
        let sum_demand_sn = vm.$route.query.sum_demand_sn.split(',')
        vm.$store.commit('selectList', vm.selectStr);
        axios.post(vm.url+vm.$sumDiffInfoURL,
            {
                "sum_demand_sn":sum_demand_sn
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.selectStr.splice(11)
            if(res.data.code==1000){
                // vm.tableData=res.data.data; 
                vm.sd_goods_list=res.data.data.sd_goods_list;
                vm.expire_time=res.data.data.expire_time;
                vm.sum_demand_detail=res.data.data.sum_demand_detail;
                vm.channel_arr=res.data.data.channel_arr;
                vm.total_info=res.data.data.sd_goods_list.total_info;
                tableStyleByDataLength(vm.sum_demand_detail.length,15);
                vm.channel_arr.forEach(element=>{
                    vm.selectStr.push(element)
                })
                vm.$store.commit('selectList', vm.selectStr);
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
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    //返回上一级
    backUpPage(){
        let vm =this;
        vm.$router.push('/JointStatistics');
    }
  },
  computed:{
        //选择要展示的表头 
        selectTitle(checkedCities){
            return selectTitle(checkedCities);
        },
        orderTableW(){
            let vm = this;
            let orderTableWidth = (vm.expire_time.length+1)*230;
            return "width:"+orderTableWidth+"px";
        },
        tableWidth(){
            let vm = this;
            let judgeRate=this.$store.state.select.find(function(e){
                    return e=='商品名称';
            });
            let pinpaimingceng=this.$store.state.select.find(function(e){
                    return e=='商品规格码';
            });
            let caozuo=this.$store.state.select.find(function(e){
                    return e=='商家编码';
            });
            let meijyuanjia=this.$store.state.select.find(function(e){
                    return e=='美金原价';
            });
            let guigema=this.$store.state.select.find(function(e){
                    return e=='商品参考码';
            });
            let xuqiushu=this.$store.state.select.find(function(e){
                    return e=='需求数';
            });
            let quekoujine=this.$store.state.select.find(function(e){
                    return e=='缺口金额';
            });
            let quekoushu=this.$store.state.select.find(function(e){
                    return e=='采购缺口数';
            });
            let shicaishu=this.$store.state.select.find(function(e){
                    return e=='实采数';
            });
            let xuqiujine=this.$store.state.select.find(function(e){
                    return e=='需求金额';
            });
            let caimanlv=this.$store.state.select.find(function(e){
                    return e=='采满率';
            });
            
            let widthLength=0;
            if(judgeRate){
                widthLength=widthLength+200;
            }
            if(caozuo){
                widthLength=widthLength+170;
            }
            if(pinpaimingceng){
                widthLength=widthLength+170;
            }
            if(meijyuanjia){
                widthLength=widthLength+90;
            }
            if(guigema){
                widthLength=widthLength+190;
            }
            if(xuqiushu){
                widthLength=widthLength+100;
            }
            if(quekoujine){
                widthLength=widthLength+100;
            }
            if(quekoushu){
                widthLength=widthLength+100;
            }
            if(shicaishu){
                widthLength=widthLength+90;
            }
            if(xuqiujine){
                widthLength=widthLength+100;
            }
            if(caimanlv){
                widthLength=widthLength+100;
            }
            let aa =0
            this.$store.state.select.forEach(element=>{
                if(element.search("-") != -1 ){
                    aa++;
                }
            })
            widthLength = widthLength+aa*300
            return "width:"+widthLength+"px";
        },
    }
}
</script>

<style>
.sumDiffInfo .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.sumDiffInfo .t_i_h{width:100%; overflow-x:hidden;}
.sumDiffInfo .ee{width:100%!important; width:100%; text-align: center;}
.sumDiffInfo .t_i_h table{width:100%;}
.sumDiffInfo .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.sumDiffInfo .cc{width:100%;height: 100%; border-bottom:1px solid #ebeef5; overflow:auto;}
.sumDiffInfo .cc table{width:100%;}
.sumDiffInfo .cc table td{ text-align:center}
</style>
<style scoped>
@import '../css/publicCss.css';
</style>
