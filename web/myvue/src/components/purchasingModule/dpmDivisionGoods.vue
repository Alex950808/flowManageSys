<template>
  <div class="dpmDivisionGoods_b">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="dpmDivisionGoods viewSummary">
                        <el-row>
                            <el-col :span="24" class="listTitleStyle">
                                <span class="bgButton" @click="backUpPage()">返回上一级</span>
                                <span class="stage MR_ten"><span class="coarseLine MR_twenty"></span>部门分货详情</span>
                                <span v-if="isNotFound" class="bgButton MT_twenty floatRight" @click="openPricing('3')">生成部门分货</span>
                            </el-col>
                        </el-row>
                        <div v-if="isShow" style="margin-bottom: 10px;">
                            <el-row class="L_H_F grayFont">
                                <el-col :span="6" :offset="1"><div><span>采购期单号：{{sortData.purchase_sn}}</span></div></el-col>
                                <el-col :span="6" :offset="1"><div><span>实采单号：<span class="notBgButton" @click="gotoRealOrder(sortData.real_pur_sn)">{{sortData.real_pur_sn}}</span></span></div></el-col>
                                <el-col :span="6" :offset="1"><div><span>分货单号：{{sortData.sort_sn}}</span>&nbsp;&nbsp;</div></el-col>
                            </el-row>
                            <el-row class="L_H_F grayFont">
                                <el-col :span="6" :offset="1"><div><span>批次类别：{{sortData.batch_cat_desc}}</span>&nbsp;&nbsp;</div></el-col>
                                <el-col :span="6" :offset="1"><div><span>创建时间：{{sortData.create_time}}</span></div></el-col>
                                <el-col :span="6" :offset="1" v-if="sortData.batch_cat == '2'">
                                    <div><span>需求单号：{{sortData.demand_sn}}</span></div>
                                </el-col>
                            </el-row>
                            <el-row class="L_H_F grayFont">
                                <el-col :span="6" :offset="1"><div><span>批次提货日期：{{dateInfo.delivery_time}}</span>&nbsp;&nbsp;</div></el-col>
                                <el-col :span="6" :offset="1"><div><span>预计批次到货时间：{{dateInfo.arrive_time}}</span></div></el-col>
                            </el-row>
                        </div>
                        <el-row v-if="isShow" class="L_H_F">
                            <el-col :span="12" v-for="item in sortDemandData" :key="item.depart_id">
                                <span class="MR_twenty">{{item.de_name}}</span>
                                <span class="MR_twenty">{{item.sortDesc}}</span>
                                <span v-if="item.sortStatus != 1" class="bgButton MR_twenty" @click="openPricing('1');depart_id=item.depart_id;">生成用户分货数据</span>
                                <span class="notBgButton" @click="getUserSortData(item.depart_id)">查看详情</span>
                            </el-col>
                        </el-row>
                        <span class="bgButton MB_ten" @click="selectcol()">选择列</span>
                        <el-row v-if="isShow" class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                        <thead>
                                            <tr>
                                                <th v-if="selectTitle('商品名称')" style="width:300px;">商品名称</th>
                                                <th v-if="selectTitle('商品规格码')" style="width:150px">商品规格码</th>
                                                 
                                                 <th v-if="selectTitle('需求单号')" style="width:200px">需求单号</th>
                                                 <th v-if="selectTitle('子单号')" style="width:200px">子单号</th>
                                                 <th v-if="selectTitle('销售用户')" style="width:150px">销售用户</th>
                                                 <th v-if="selectTitle('交付日期')" style="width:150px">交付日期</th>

                                                <th v-if="selectTitle('部门名称')" style="width:130px">部门名称</th>
                                                <th v-if="selectTitle('部门需求数')" style="width:130px">部门需求数</th>
                                                <th v-if="selectTitle('用户需求总量')" style="width:130px">用户需求总量</th>
                                                <th v-if="selectTitle('可分配数量')" style="width:130px">可分配数量</th>
                                                <th v-if="selectTitle('比例数量')" style="width:130px">比例数量</th>
                                                <th v-if="selectTitle('手动分配数量')" style="width:150px">手动分配数量</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <!-- </div> -->
                            <!-- <div style="width:100%;overflow:hidden"> -->
                                <div class="cc" id="cc" @scroll="scrollEvent()">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                        <tr v-for="(item,index) in tableData">
                                            <td v-if="selectTitle('商品名称')" class="overOneLinesHeid" style="width:300px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td v-if="selectTitle('商品规格码')" style="width:150px">{{item.spec_sn}}</td>
                                            <td v-if="selectTitle('需求单号')" style="width:200px">{{item.demandInfo.demand_sn}}</td>
                                            <td v-if="selectTitle('子单号')" style="width:200px">{{item.demandInfo.sub_order_sn}}</td>
                                            <td v-if="selectTitle('销售用户')" style="width:150px">{{item.demandInfo.user_name+'-'+item.demandInfo.sale_user_account}}</td>
                                            <td v-if="selectTitle('交付日期')" style="width:150px">{{item.demandInfo.entrust_time}}</td>
                                            <td v-if="selectTitle('部门名称')" style="width:130px">{{item.de_name}}</td>
                                            <td v-if="selectTitle('部门需求数')" style="width:130px">{{item.depart_need_num}}</td>
                                            <td v-if="selectTitle('用户需求总量')" style="width:130px">{{item.total_num}}</td>
                                            <td v-if="selectTitle('可分配数量')" style="width:130px">{{item.may_sort_num}}</td>
                                            <td v-if="selectTitle('比例数量')" style="width:130px">{{item.ratio_num}}</td>
                                            <td v-if="selectTitle('手动分配数量')" class="handle_num_two" style="width:150px"><input type="text" :name="index" :value="item.handle_num" @change="modifiedValueByInput(item.depart_id,item.spec_sn,index)"/></td>
                                        </tr>
                                    </table>
                                </div>
                            <!-- </div> -->
                        </el-row>
                        <el-row v-if="isShow" class="w_ratio fontCenter MB_twenty">
                            <span class="bgButton" @click="openPricing('2')">停用部门分货数据</span>
                        </el-row>
                    </div>
                    <notFound v-if="isNotFound"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <selectCol :selectStr="selectStr"></selectCol>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import {modifiedValueByInput} from '../../filters/publicMethods.js'
import { Loading } from 'element-ui'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import {tableStyleByDataLength} from '@/filters/publicMethods.js'
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableWidth } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
import selectCol from '@/components/UiAssemblyList/selectCol'
export default {
  components:{
      customConfirmationBoxes,
      notFound,
      fontWatermark,
      selectCol
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      sortData:[],
      sortDemandData:[],
      dateInfo:'',
      headersStr:'',
      depart_id:'',
      contentStr:'',
      index:'',
      isShow:true,
      isNotFound:false,
      //选择列
      selectStr:['商品名称','商品规格码','需求单号','子单号','销售用户','交付日期','部门名称','部门需求数','用户需求总量','可分配数量','比例数量','手动分配数量'],
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getGoodsData()
      this.$store.commit('selectList', this.selectStr);
  },
  methods:{
      //获取实时/最终分货列表
    getGoodsData(){
        let vm=this;
        let loa = Loading.service({fullscreen: true, text: '拼命加载中....'})
        axios.get(vm.url+vm.$getSortDataURL+"?purchase_sn="+vm.$route.query.purchase_sn+"&real_purchase_sn="+vm.$route.query.real_purchase_sn+"&query_type="+vm.$route.query.query_type,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            loa.close()
            if(res.data.code=='2046'){
                vm.isShow=false;
                vm.isNotFound=true;
            }else if(res.data.sortGoodsData.length!=0){
                vm.isShow=true;
                vm.isNotFound=false;
                vm.tableData=res.data.sortGoodsData;
                vm.sortData=res.data.sortData;
                vm.dateInfo=res.data.dateInfo;
                vm.sortDemandData=res.data.sortDemandData;
                tableStyleByDataLength(vm.tableData.length,15);
            }else{
                vm.isShow=false;
                vm.isNotFound=true;
            }
        }).catch(function (error) {
                loa.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    modifiedValueByInput(depart_id,spec_sn,index){
        let vm=this;
        let handleNum=$(".handle_num_two input[name="+index+"]").val();
        let sort_sn=vm.sortData.sort_sn;
        axios.post(vm.url+vm.$depHandleGoodsURL,
            {
                "depart_id":depart_id,
                "sort_sn":sort_sn,
                "spec_sn":spec_sn,
                "handle_num":handleNum,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==2038){
                vm.$message(res.data.msg);
                vm.getGoodsData();
            }else{
                vm.$message(res.data.msg);
                vm.getGoodsData();
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //打开生成数据弹出框
    openPricing(index){
        let vm = this;
        $(".confirmPopup_b").fadeIn();
        vm.index=index;
        if(index=='1'){//生成用户分货数据
            vm.contentStr='请您确认是否要生成用户分货数据！';
        }else if(index=='2'){//停用部门分货数据
            vm.contentStr='请您确认是否要停用分货数据！';
        }else if(index=='3'){//生成部门分货数据
            vm.contentStr='请您确认是否要生成部门分货数据！';
        }
    },
    //取消生成数据
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    //生成销售数据
    confirmationAudit(){
        let vm = this;
        vm.determineIsNo();
        let url;
        let data;
        if(vm.index=='1'){
            url=vm.$generalUserSortDataURL
            data = {
                        "purchase_sn":vm.$route.query.purchase_sn,
                        "real_purchase_sn":vm.$route.query.real_purchase_sn,
                        "sort_sn":vm.sortData.sort_sn,
                        "depart_id":vm.depart_id,
                    }
        }else if(vm.index=='2'){
            url=vm.$stopDepartSortDataURL;
            data={"sort_sn":vm.sortData.sort_sn};
        }else if(vm.index=='3'){
            url=vm.$generalDepartSortDataURL;
            data = {
                        "purchase_sn":vm.$route.query.purchase_sn,
                        "real_purchase_sn":vm.$route.query.real_purchase_sn,
                   };
        }
        axios.post(vm.url+url,data,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(vm.index=='1'){//生成用户分货数据
                vm.$message(res.data.msg);
            }else if(vm.index=='2'){//停用部门分货数据
                vm.$router.push('/purRealList');
            }else if(vm.index=='3'){//生成部门分货数据
                vm.getGoodsData();
            }
            vm.getGoodsData();
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //查看生成的数据详情
    getUserSortData(depart_id){
        let vm = this;
        vm.$router.push('/userSortData?purchase_sn='+vm.$route.query.purchase_sn+"&real_purchase_sn="+vm.$route.query.real_purchase_sn+"&depart_id="+depart_id+"&sort_sn="+vm.sortData.sort_sn+"&query_type="+vm.$route.query.query_type);
    },
    //
    gotoRealOrder(real_pur_sn){
        let vm = this;
        vm.$router.push('/distributionOfGoods?real_purchase_sn='+real_pur_sn+"&isdpm=isdpm");
    },
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    //返回上一页 
    backUpPage(){
        let vm=this;
        let isDP=vm.isDP;
        if(isDP==true){
            vm.getGoodsData(2);
        }else{
            vm.$router.push('/purRealList');
        }
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
  },
  computed:{
    selectTitle(checkedCities){
       return selectTitle(checkedCities)
    },
    tableWidth(){
        let vm=this;
        
        let judgeRate=this.$store.state.select.find(function(e){
                return e=='商品名称';
        });
        let pinpaimingceng=this.$store.state.select.find(function(e){
                return e=='商品规格码';
        });
        let caozuo=this.$store.state.select.find(function(e){
                return e=='需求单号';
        });
        let meijyuanjia=this.$store.state.select.find(function(e){
                return e=='子单号';
        });
        let yuguzhongliang=this.$store.state.select.find(function(e){
                return e=='销售用户';
        });
        let EXWzhekou=this.$store.state.select.find(function(e){
                return e=='交付日期';
        });
        let xinpinzhuangtai=this.$store.state.select.find(function(e){
                return e=='手动分配数量';
        });
        var widthLength=(this.$store.state.select.length-7)*130;
        if(judgeRate){
            widthLength=widthLength+300;
        }
        if(caozuo){
            widthLength=widthLength+150;
        }
        if(pinpaimingceng){
            widthLength=widthLength+200;
        }
        if(meijyuanjia){
            widthLength=widthLength+200;
        }
        if(yuguzhongliang){
            widthLength=widthLength+150;
        }
        if(EXWzhekou){
            widthLength=widthLength+150;
        }
        if(xinpinzhuangtai){
            widthLength=widthLength+150;
        }
        let title = $(".title").width();
        if(widthLength<title){
            widthLength=title;
        }
        return "width:"+widthLength+"px"
    },
  }
}
</script>

<style>
.dpmDivisionGoods_b .handle_num_one input{
    width: 80%;
}
.dpmDivisionGoods_b .handle_num_two input{
    width: 80%;
}
.dpmDivisionGoods_b .t_i{width:100%; height:auto;}
.dpmDivisionGoods_b .t_i_h{width:100%; overflow-x:hidden;}
.dpmDivisionGoods_b .ee{width:100%!important; text-align: center;}
.dpmDivisionGoods_b .t_i_h table{width:100%;}
.dpmDivisionGoods_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.dpmDivisionGoods_b .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;margin-bottom: 25px;}
.dpmDivisionGoods_b .cc table{width:100%; }
.dpmDivisionGoods_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>
<style scoped lang=less>
@import '../../css/dataCollection.less';
</style>
