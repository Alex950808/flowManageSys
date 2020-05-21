<template>
  <div class="profitList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">

                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>毛利数据列表</span></div></el-col>
                        <el-col :span="3"><div>开始日期</div></el-col>
                        <el-col :span="3"><div><el-date-picker v-model="startDate" type="date" value-format="yyyy-MM-dd" placeholder="选择开始日期"></el-date-picker></div></el-col>
                        <el-col :span="3"><div>结束日期</div></el-col>
                        <el-col :span="3"><div><el-date-picker v-model="endDate" type="date" value-format="yyyy-MM-dd" placeholder="选择结束日期"></el-date-picker></div></el-col>
                        <el-col :span="3"><div>毛利单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="profitSn" placeholder="请输入毛利单号"></el-input></div></el-col>
                        <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
                    <el-row class="MB_ten">
                        <el-col :span="3"><span class="bgButton" @click="openAddBox()">生成毛利数据</span></el-col>
                        <el-col :span="3"><span v-if="!isShow" class="bgButton" @click="viewDetails()">查看详情</span></el-col>
                    </el-row>

                    <div class="t_i" v-if="!isShow">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th class="w_Fty" style="width:60px" rowspan="2"><input class="allChecked" type="checkbox" @change="allSelect()"/>全选</th>
                                            <th style="width:180px">毛利单号</th>
                                            <th style="width:120px">结算开始日期</th>
                                            <th style="width:120px">结算结束日期</th>
                                            <th style="width:100px">渠道</th>
                                            <th style="width:100px">结算日期<br>类型</th>
                                            <th style="width:100px">充值返回<br>积分</th>
                                            <th style="width:100px">结账卡返点</th>
                                            <!-- <th>总毛利</th> -->
                                            <th style="width:400px;">毛利公式</th>
                                            <th style="width:100px" v-for="info in tableData.title_list">{{info.cat_name}}</th>
                                            <th style="width:100px">操作</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc MB_twenty" id="cc"  @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                                <tbody>
                                    <tr v-for="(item,index) in tableData">
                                        <td class="w_Fty cb_one" style="width:60px">
                                            <input @click="oneSelect(index)" type="checkbox" :name="index"/>
                                        </td>
                                        <td style="width:180px">{{item.profit_sn}}</td>
                                        <td style="width:120px">{{item.start_date}}</td>
                                        <td style="width:120px">{{item.end_date}}</td>
                                        <td style="width:100px">{{item.channels_name}}</td>
                                        <td style="width:100px">{{item.settle_date_type}}</td>
                                        <td style="width:100px">{{item.recharge_points}}</td>
                                        <td style="width:100px">{{item.card_return_points}}</td>
                                        <!-- <td>{{item.total_points}}</td> -->
                                        <td style="width:400px;">{{item.formula}}</td>
                                        <td style="width:100px" v-for="info in tableData.title_list">{{item[info.cat_code]}}</td>
                                        <td style="width:100px"><span class="bgButton" @click="openDeleteBox(item.profit_sn)">停用</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <el-dialog title="生成毛利数据" :visible.sync="dialogVisibleSetgAddPF" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>结算日期类型：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="settleDateTypeI" placeholder="请选择结算日期类型">
                                        <el-option v-for="item in settleDateTypeL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>渠道：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="channelsIdI" @change="isformulaMode()" placeholder="请选择渠道">
                                        <el-option v-for="item in channelsIdL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>结算开始日期：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-date-picker class="w_ratio" v-model="start_date" value-format="yyyy-MM-dd" type="date" placeholder="请选择结算开始日期"></el-date-picker>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>结算结束日期：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-date-picker class="w_ratio" v-model="end_date" value-format="yyyy-MM-dd" type="date" placeholder="请选择结算开始日期"></el-date-picker>
                            </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>计算方式：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="formulaModeI" @change="isformulaMode()" placeholder="请选择计算方式">
                                        <el-option v-for="item in formulaModeL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>公式名称：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="formulaNameI" placeholder="请选择公式名称">
                                        <el-option v-for="item in formulaNameL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                        </el-row>
                        <el-row v-if="is_two" class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>品牌真实档位：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="realTypeIdI" placeholder="请选择品牌真实档位">
                                        <el-option v-for="item in realTypeIdL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div>商品真实档位：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="goodsTypeIdI" placeholder="请选择商品真实档位">
                                        <el-option v-for="item in goodsTypeIdL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleSetgAddPF = false;">取 消</el-button>
                            <el-button type="primary" @click="doAddProfitFormula()">确 定</el-button>
                        </span>
                    </el-dialog>
                  <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes';
import { tableWidth } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      notFound,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[], 
      isShow:false,
      selectStr:['全选','毛利单号','结算开始日期','结算结束日期','渠道','结算日期类型','充值返回积分','结账卡返点','总毛利','毛利公式','操作'],
      //生成毛利数据, 
      type_list:[],
      dialogVisibleSetgAddPF:false,
      settleDateTypeL:[{"name":"提货日","id":"1"},{"name":"购买日","id":"2"}],
      settleDateTypeI:'',
      start_date:'',
      end_date:'',
      channelsIdL:[],
      channelsIdI:'',
      formulaModeL:[{'name':"系统计算","id":"1"},{"name":"团长指定","id":"2"}],
      formulaModeI:'',
      formulaNameL:[],
      formulaNameI:'',
      realTypeIdL:[],
      realTypeIdI:'',
      goodsTypeIdL:[],
      goodsTypeIdI:'',
      is_two:false,
      //确认弹框 
      contentStr:'停用后无法恢复，是否停用？',
      profit_sn:'',
      //搜索
      endDate:'',
      startDate:'',
      profitSn:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
    this.$store.commit('selectList', this.selectStr);
  },
  methods:{
    getDataList(){
        let vm = this;
        vm.selectStr.splice(10)
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
        if(vm.startDate==null){
            vm.startDate='';
        }
        if(vm.endDate==null){
            vm.endDate='';
        }
        axios.get(vm.url+vm.$profitListURL+"?start_date="+vm.startDate+"&end_date="+vm.endDate+"&profit_sn="+vm.profitSn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.profit_list.data;
                res.data.data.title_list.forEach(element=>{
                    vm.selectStr.push(element.cat_name)
                })
                vm.isShow=false;
                tableStyleByDataLength(res.data.data.profit_list.total,5);
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
    openAddBox(){
        let vm = this;
        vm.dialogVisibleSetgAddPF=true;
        vm.settleDateTypeI='';//结算日期类型：1，提货日；2，购买日
        vm.start_date='';//结算开始日期 
        vm.end_date='';//结算结束日期 
        vm.channelsIdL.splice(0);
        vm.realTypeIdL.splice(0);
        vm.formulaNameL.splice(0)
        vm.channelsIdI='';
        vm.formulaModeI='';//计算方式 1,系统计算;2,团长指定
        vm.realTypeIdI='';//指定档位id（只有当formula_mode选择2时，才存在这个字段）
        vm.formulaNameI='';//公式编号
        vm.goodsTypeIdI='';
        vm.is_two=false;
        axios.get(vm.url+vm.$discountTypeListFinanceURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=='1000'){
                res.data.data.type_list.forEach(element=>{
                    vm.channelsIdL.push({"name":element.channels_name,"id":element.channels_id+","+element.is_gears})
                })
                vm.type_list=res.data.data.type_list;
                res.data.data.pf_list.forEach(element=>{
                    vm.formulaNameL.push({"name":element.formula_name,"id":element.formula_sn})
                })
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
    isformulaMode(){
        let vm = this;
        vm.realTypeIdL.splice(0);
        vm.goodsTypeIdL.splice(0);
        if(vm.formulaModeI==='2'){
            vm.is_two=true;
            vm.type_list.forEach(element=>{
                if(vm.channelsIdI==element.channels_id+","+element.is_gears){
                    if(element.brand_gears!=undefined){
                        element.brand_gears.forEach(type=>{
                            vm.realTypeIdL.push({"name":type.type_name,"id":type.id+","+type.type_cat})
                        })
                    }
                }
            })
            vm.type_list.forEach(element=>{
                if(vm.channelsIdI==element.channels_id+","+element.is_gears){
                    if(element.goods_gears!=undefined){
                        element.goods_gears.forEach(type=>{
                            vm.goodsTypeIdL.push({"name":type.type_name,"id":type.id})
                        })
                    } 
                }
            })
        }else{
            vm.is_two=false;
        }
    },
    //确认新增毛利数据 
    doAddProfitFormula(){
        let vm = this;
        if(vm.settleDateTypeI===''){
            vm.$message('请填写结算日期类型后提交!');
            return false;
        }
        if(vm.channelsIdI===''){
            vm.$message('请填写渠道名称后提交!');
            return false;
        }
        if(vm.start_date===''){
            vm.$message('请填写结算开始日期后提交!');
            return false;
        }
        if(vm.end_date===''){
            vm.$message('请填写结算结束日期后提交!');
            return false;
        }
        if(vm.formulaModeI===''){
            vm.$message('请填写计算方式后提交!');
            return false;
        }
        if(vm.formulaNameI===''){
            vm.$message('请填写公式名称后提交!');
            return false;
        }
        vm.dialogVisibleSetgAddPF=false;
        let channelsId=vm.channelsIdI.split(",")
        axios.post(vm.url+vm.$generateProfitURL,
            {
                "settle_date_type":vm.settleDateTypeI,//结算日期类型：1，提货日；2，购买日
                "start_date":vm.start_date,//结算开始日期 
                "end_date":vm.end_date,//结算结束日期
                "channels_id":channelsId[0],//渠道id
                // "is_gears":channelsId[1],//渠道是否存在多个档位:1.存在;2.不存在;（在渠道信息中有）
                "formula_mode":vm.formulaModeI,//计算方式 1,系统计算;2,团长指定
                "brand_type_id":vm.realTypeIdI.split(',')[0],//指定档位id（只有当formula_mode选择2时，才存在这个字段）
                "formula_sn":vm.formulaNameI,//公式编号
                "goods_type_id":vm.goodsTypeIdI,
                "type_cat":vm.realTypeIdI.split(',')[1],
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=='1000'){
                vm.getDataList()
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
    //全选 
    allSelect(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        if(allChecked==true){
            for(var i=0;i<vm.tableData.length;i++){
                $(".cb_one input[name="+i+"]").prop("checked",true);
            }
        }else if(allChecked==false){
            for(var i=0;i<vm.tableData.length;i++){
                $(".cb_one input[name="+i+"]").prop("checked",false);
            }
        }
    },
    //单选 
    oneSelect(index){
        let vm = this;
        let isTrueList=[];
        for(var i=0;i<vm.tableData.length;i++){
            let isTrue = $(".cb_one input[name="+i+"]").prop("checked");
            isTrueList.push(isTrue);
        }
        for(var i=0;i<isTrueList.length;i++){
            if(isTrueList[i+1]!=undefined){
                if(isTrueList[i]==isTrueList[i+1]){
                    if(isTrueList[i]==true){
                        $('.allChecked').prop("checked",true)
                    }else if(isTrueList[i]==false){
                        $('.allChecked').prop("checked",false)
                    }
                }else{
                    $('.allChecked').prop("checked",false)
                    break;
                }
            }
        }
    },
    viewDetails(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        let profit_sn = [];
        if(allChecked==true){
            vm.tableData.forEach(specInfo=>{
                profit_sn.push(specInfo.profit_sn);
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.tableData.length;i++){
                let isTrue = $("input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    profit_sn.push(vm.tableData[i].profit_sn);
                }
            }
        }
        if(profit_sn.length==0){
            vm.$message('请选择毛利数据后进行查看!');
            return false;
        }else{
            vm.$router.push('/profitDetail?profit_sn='+profit_sn);
        }
    },
    openDeleteBox(sn){
        let vm = this;
        vm.profit_sn=sn;
        $(".confirmPopup_b").fadeIn();
    },
    //取消审核/数据提交 
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    confirmationAudit(sn){
        let vm = this;
        $(".confirmPopup_b").fadeOut();
        axios.get(vm.url+vm.$deleteProfitInfoURL+"?profit_sn="+vm.profit_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=="1000"){
                vm.getDataList();
            }
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    }
  },
  computed:{
    // selectTitle(checkedCities){
    //    return selectTitle(checkedCities)
    // },
    tableWidth(){
        return tableWidth(this.$store.state.select);
    },
  }
}
</script>

<style>
.profitList .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.profitList .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.profitList .ee{width:100%!important; width:100%; text-align: center;}
.profitList .t_i_h table{width:1530px;}
.profitList .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.profitList .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.profitList .cc table{width:1530px; }
.profitList .cc table td{ text-align:center}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
