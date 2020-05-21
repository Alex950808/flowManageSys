<template>
  <div class="getWholesaleDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="L_H_F MT_Thirty">
                        <span class="bgButton" @click="backUpPage()">返回上一页</span>&nbsp;&nbsp;&nbsp;<span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;大批发报价详情
                    </div>
                    <el-row style="margin-bottom: 10px;">
                        <span class="fontWeight F_S_twenty">订单信息：</span>
                        <span class="bgButton" @click="openBox()">大批发报价数据导出</span>
                    </el-row>
                    <div style="margin-bottom: 10px;">
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>订单号：{{titleData.wholesale_sn}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>运输方式：{{titleData.transportDesc}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>预计到港时间：{{titleData.estimate_time}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>创建时间：{{titleData.create_time}}</span></div></el-col>
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>运费：{{titleData.freight}}($)</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>汇率：{{titleData.usd_cny_rate}}</span></div></el-col>
                        </el-row>
                    </div>
                    <el-dialog title="大批发数据导出" :visible.sync="dialogVisibleExport" width="800px">
                        <el-row class="MB_twenty">
                            <el-col :span="4">
                                <div><span class="redFont">*</span>折扣模板</div>
                            </el-col>
                            <el-col :span="18">
                                <template>
                                    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                    <div style="margin: 15px 0;"></div>
                                    <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
                                        <el-checkbox v-for="disPlateInfon in discountPlate" :label="disPlateInfon.plate_id" :key="disPlateInfon.plate_id">{{disPlateInfon.plate_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="4">
                                <div><span class="redFont">*</span>渠道</div>
                            </el-col>
                            <el-col :span="18">
                                <template>
                                    <el-checkbox :indeterminate="isIndeterminateChannel" v-model="channelAll" @change="handlechannelAllChange">全选</el-checkbox>
                                    <div style="margin: 15px 0;"></div>
                                    <el-checkbox-group v-model="checkedChannel" @change="handleCheckedchannelChange">
                                        <el-checkbox v-for="channelsNameInfo in channelsName" :label="channelsNameInfo.channel_id" :key="channelsNameInfo.channel_id">{{channelsNameInfo.channels_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty" v-if="isHaveData">
                            <el-col :span="4">
                                <div class="MT_ten"><span class="redFont">*</span>代采其他折扣</div>
                            </el-col>
                            <el-col :span="6"><el-input style="width:85%" v-model="input_num" placeholder="请输入代采其他折扣"></el-input>&nbsp;&nbsp;<span class="redFont fontWeight">%</span></el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleExport = false;">取 消</el-button>
                            <el-button type="primary" @click="confirmDataUp()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <div class="table_div_wrap"  v-if="!isShow">
                        <div class="inner">
                            <div style="overflow: hidden;">
                            <div class="table_thead">
                                <table cellpadding="0" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th style="width:200px;" rowspan="2">商品名称</th>
                                        <th style="width:200px;" rowspan="2">商家编码</th>
                                        <th style="width:200px;" rowspan="2">商品代码</th>
                                        <th style="width:200px;" rowspan="2">参考代码</th>
                                        <th style="width:100px;" rowspan="2">重量</th>
                                        <th style="width:100px;" rowspan="2">美金原价</th>
                                        <th style="width:100px;">折扣类型</th>
                                        <th style="width:100px;" :colspan="channelsName.length">基础折扣</th>
                                        <th style="width:100px;" :colspan="channelsName.length">
                                            代采折扣
                                            <span class="infoNotBgButton" @click="switchDiscountMiddle(0.5,$event)">代采+0.5</span>
                                            <span class="notBgButton" @click="switchDiscountMiddle(1,$event)">代采+1</span>
                                        </th>
                                        <th style="width:100px;" :colspan="channelsName.length">
                                            全包折扣
                                            <span class="infoNotBgButton" @click="switchDiscountMiddle(1.5,$event)">代采+0.5</span>
                                            <span class="notBgButton" @click="switchDiscountMiddle(2,$event)">代采+1</span>
                                        </th>
                                        <th style="width:100px;" :colspan="channelsName.length">
                                            代采其他折扣
                                            <el-input style="width:150px;display:inline-block;" v-model="other_dis" placeholder="请输入追加点数"></el-input>&nbsp;&nbsp;<span class="redFont fontWeight">%</span>
                                            <span class="notBgButton" @click="otherDis()">搜索</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="width:100px;">渠道名称</th>
                                        <th style="width:100px;" v-for="name in titleName">{{name}}</th>
                                        <th style="width:100px;" v-for="name in titleName">{{name}}</th>
                                        <th style="width:100px;" v-for="name in titleName">{{name}}</th>
                                        <th style="width:100px;" v-for="name in titleName">{{name}}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            </div>
                            <div class="table_tbody">
                                <table cellpadding="0" cellspacing="0">
                                    <tbody v-for="item in tableData">
                                        <tr>
                                            <td style="width:200px;" rowspan="4" class="overOneLinesHeid"><span  style="-webkit-box-orient: vertical;height: 63px;line-height: 22px;width: 121px;-webkit-line-clamp: 3;">{{item.goods_name}}</span></td>
                                            <td style="width:200px;" rowspan="4" class="overOneLinesHeid"><span  style="-webkit-box-orient: vertical;height: 63px;line-height: 22px;width: 121px;-webkit-line-clamp: 2;">{{item.erp_merchant_no}}</span></td>
                                            <!-- <td style="width:200px;" rowspan="4">{{item.erp_merchant_no}}</td> -->
                                            <td style="width:200px;" rowspan="4">{{item.erp_prd_no}}</td>
                                            <td style="width:200px;" rowspan="4">{{item.erp_ref_no}}</td>
                                            <td style="width:100px;" rowspan="4">{{item.spec_weight}}</td>
                                            <td style="width:100px;" rowspan="4">{{item.spec_price}}</td>
                                            <td>折扣</td>
                                            <td v-if=" item.channels_info.length!=0" style="width:100px;" v-for="(name,index) in item.channels_info">
                                                <span>{{name.cost_discount}}</span>
                                            </td>
                                            <td v-if=" item.channels_info.length!=0" style="width:100px;" v-for="(name,index) in item.channels_info">
                                                <span v-if="isCutMiddle">{{name.cut_middle_discount}}</span>
                                                <span v-if="!isCutMiddle">{{name.cut_one_discount}}</span>
                                            </td>
                                            <td v-if=" item.channels_info.length!=0" style="width:100px;" v-for="(name,index) in item.channels_info">
                                                <span v-if="isCutOneMiddle">{{name.cut_one_middle_discount}}</span>
                                                <span v-if="!isCutOneMiddle">{{name.cut_one_middle_discount}}</span>
                                            </td>
                                            <td v-if=" item.channels_info.length!=0" style="width:100px;" v-for="(name,index) in item.channels_info">
                                                <span>{{name.cut_other_discount}}</span>
                                            </td>
                                            <td v-if=" item.channels_info.length==0" style="width:100px;" v-for="(name,index) in titleName">
                                                <span>0</span>
                                            </td>
                                            <td v-if=" item.channels_info.length==0" style="width:100px;" v-for="(name,index) in titleName">
                                                <span>0</span>
                                            </td>
                                            <td v-if=" item.channels_info.length==0" style="width:100px;" v-for="(name,index) in titleName">
                                                <span>0</span>
                                            </td>
                                            <td v-if=" item.channels_info.length==0" style="width:100px;" v-for="(name,index) in titleName">
                                                <span>0</span>
                                            </td>
                                        </tr>
                                        <tr v-if=" item.channels_info.length!=0" v-for="indexNum in 3">
                                            <td>
                                                <span v-if="indexNum===1">运费</span>
                                                <span v-else-if="indexNum===2">人民币价格</span>
                                                <span v-else-if="indexNum===3">美金价格</span>
                                            </td>
                                            <td style="width:100px;" v-for="(name,index) in item.channels_info">
                                                <span v-if="indexNum===1">无</span>
                                                <span v-else-if="indexNum===2">{{name.cost_cny_price}}</span>
                                                <span v-else-if="indexNum===3">{{name.cost_usd_price}}</span>
                                            </td>
                                            <td style="width:100px;" v-if="isCutMiddle" v-for="(name,index) in item.channels_info">
                                                <span v-if="indexNum===1">{{name.cut_middle_freight}}</span>
                                                <span v-else-if="indexNum===2">{{name.cut_middle_cny_price}}</span>
                                                <span v-else-if="indexNum===3">{{name.cut_middle_usd_price}}</span>
                                            </td>
                                            <td style="width:100px;" v-if="!isCutMiddle" v-for="(name,index) in item.channels_info">
                                                <span v-if="indexNum===1">{{name.cut_one_freight}}</span>
                                                <span v-else-if="indexNum===2">{{name.cut_one_cny_price}}</span>
                                                <span v-else-if="indexNum===3">{{name.cut_one_usd_price}}</span>
                                            </td>
                                            <td style="width:100px;" v-if="isCutOneMiddle" v-for="(name,index) in item.channels_info">
                                                <span v-if="indexNum===1">{{name.cut_one_middle_freight}}</span>
                                                <span v-else-if="indexNum===2">{{name.cut_one_middle_cny_price}}</span>
                                                <span v-else-if="indexNum===3">{{name.cut_one_middle_usd_price}}</span>
                                            </td>
                                            <td style="width:100px;" v-if="!isCutOneMiddle" v-for="(name,index) in item.channels_info">
                                                <span v-if="indexNum===1">{{name.cut_two_freight}}</span>
                                                <span v-else-if="indexNum===2">{{name.cut_two_cny_price}}</span>
                                                <span v-else-if="indexNum===3">{{name.cut_two_usd_price}}</span>
                                            </td>
                                            <td style="width:100px;" v-for="(name,index) in item.channels_info">
                                                <span v-if="indexNum===1">{{name.cut_other_discount}}</span>
                                                <span v-else-if="indexNum===2">{{name.cut_other_freight}}</span>
                                                <span v-else-if="indexNum===3">{{name.cut_other_usd_price}}</span>
                                            </td>
                                        </tr>
                                        <tr v-if=" item.channels_info.length==0" v-for="indexNum in 3">
                                            <td>
                                                <span v-if="indexNum===1">运费</span>
                                                <span v-else-if="indexNum===2">人民币价格</span>
                                                <span v-else-if="indexNum===3">美金价格</span>
                                            </td>
                                            <td style="width:100px;" v-for="name in titleName">
                                                <span v-if="indexNum===1">0</span>
                                                <span v-else-if="indexNum===2">0</span>
                                                <span v-else-if="indexNum===3">0</span>
                                            </td>
                                            <td style="width:100px;" v-for="name in titleName">
                                                <span v-if="indexNum===1">0</span>
                                                <span v-else-if="indexNum===2">0</span>
                                                <span v-else-if="indexNum===3">0</span>
                                            </td>
                                            <td style="width:100px;" v-for="name in titleName">
                                                <span v-if="indexNum===1">0</span>
                                                <span v-else-if="indexNum===2">0</span>
                                                <span v-else-if="indexNum===3">0</span>
                                            </td>
                                            <td style="width:100px;" v-for="name in titleName">
                                                <span v-if="indexNum===1">0</span>
                                                <span v-else-if="indexNum===2">0</span>
                                                <span v-else-if="indexNum===3">0</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
      titleData:'',
      titleName:[],
      isShow:false,
      //切换0.5和1
      isCutMiddle:true,
      //切换1.5和2
      isCutOneMiddle:true,
      //代采其他折扣
      other_dis:'',
      //大批发报价数据导出
      dialogVisibleExport:false,
      //折扣模板多选
      checkAll: false,
      checkedCities: [],
      discountPlate: [],
      isIndeterminate: true,
      //渠道多选
      channelAll:false,
      checkedChannel:[],
      channelsName:[],
      isIndeterminateChannel:true,
      //大批发数据导出
      input_num:'',
      isHaveData:false,
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    //模板折扣多选 
    handleCheckAllChange(val) {
        let vm = this;
        vm.checkedCities.splice(0);
        if(val){
            vm.discountPlate.forEach(element=>{
                vm.checkedCities.push(element.plate_id)
            })
            vm.isHaveData=true;
        }else{
            vm.checkedCities.splice(0);
            vm.isHaveData=false;
        }
        // this.checkedCities = val ? vm.cities : [];
        vm.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
        let vm = this;
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.discountPlate.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.discountPlate.length;
            if(value.indexOf(6)===-1){
                vm.isHaveData=false;
            }else{
                vm.isHaveData=true;
            }
    },
    //渠道多选
    handlechannelAllChange(val) {
        let vm = this;
        vm.checkedChannel.splice(0);
        if(val){
            vm.channelsName.forEach(element=>{
                vm.checkedChannel.push(element.channel_id)
            })
        }else{
            vm.checkedChannel.splice(0);
        }
        vm.isIndeterminateChannel = false;
    },
    handleCheckedchannelChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.channelsName.length;
        this.isIndeterminateChannel = checkedCount > 0 && checkedCount < this.channelsName.length;
    },
    getDataList(){
        let vm = this;
        vm.titleName.splice(0);
        axios.post(vm.url+vm.$getWholesaleDetailURL,
            {
                "wholesale_sn":vm.$route.query.wholesale_sn,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            console.log(res);
            vm.loading.close();
            if(res.data.goods.code!='1003'){
                vm.titleData=res.data.order;
                vm.tableData=res.data.goods;
                vm.discountPlate = res.data.discountPlate;
                vm.channelsName = res.data.channel;
                vm.isShow=false;
                for(var j = 0;j<res.data.goods[1].channel_name_info.length;j++){
                    vm.titleName.push(res.data.goods[0].channel_name_info[j].channels_name)
                }
            }else{
                vm.titleData=res.data.order;
                vm.tableData=res.data.goods;
                vm.discountPlate = res.data.discountPlate;
                vm.channelsName = res.data.channel;
                vm.isShow=true;
            }
            var table_tbody = document.getElementsByClassName("table_tbody")[0];
            var table_thead = document.getElementsByClassName("table_thead")[0];
            var table_tbody_outer = document.getElementsByClassName("table_tbody")[0].getElementsByTagName('table')[0];
            table_tbody.onscroll = function (e) {
                table_thead.style.marginLeft = "-"+this.scrollLeft+"px"
                table_tbody_outer.style.marginTop = "-"+this.scrollTop+'px'
            }
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
        // axios.post(vm.url+vm.$getWholeDetailURL,
        //     {
        //         "wholesale_sn":vm.$route.query.wholesale_sn,
        //         "cut_other_value":'1.5',
        //     },
        //     {
        //         headers:vm.headersStr,
        //     }
        // ).then(function(res){
        //     console.log(res);
        // }).catch(function (error) {
        //     vm.loading.close();
        //     if(error.response.status!=''&&error.response.status=="401"){
        //       vm.$message('登录过期,请重新登录!');
        //       sessionStorage.setItem("token","");
        //       vm.$router.push('/');
        //     }
        // });
    },
    //切换折扣0.5和1 
    switchDiscountMiddle(index,event){
        let vm = this;
        $(event.target).addClass("notBgButton").removeClass("infoNotBgButton")
        $(event.target).siblings('span').removeClass("notBgButton").addClass("infoNotBgButton")
        if(index===0.5){
            vm.isCutMiddle=true;
        }else if(index===1){
            vm.isCutMiddle=false;
        }else if(index===1.5){
            vm.isCutOneMiddle=true;
        }else if(index===2){
            vm.isCutOneMiddle=false;
        }
    },
    //其他折扣
    otherDis(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'})
        axios.post(vm.url+vm.$getWholesaleDetailURL,
            {
                "wholesale_sn":vm.$route.query.wholesale_sn,
                "cut_other_value":vm.other_dis,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.goods.length!=0){
                vm.tableData=res.data.goods;
                vm.titleData=res.data.order;
                vm.isShow=false;
            }else{
                vm.isShow=true;
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
    //打开导出大批发报价数据弹框
    openBox(){
        let vm = this;
        vm.dialogVisibleExport = true;
        vm.checkedCities = [];
        vm.checkedChannel = [];
        vm.input_num = '';
        vm.isHaveData = false;
    },
    //大批发确定报价
    confirmDataUp(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        let discount_plate=vm.checkedCities.toString().replace(/[']/g,'');
        let channels_id = vm.checkedChannel.toString().replace(/[']/g,'');
        window.open(vm.url+vm.$exportWholesaleOfferURL+'?wholesale_sn='+vm.$route.query.wholesale_sn+'&discount_plate='+discount_plate+'&channels_id='+channels_id+'&input_num='+vm.input_num+'&token='+headersToken);
    },
    //
    backUpPage(){
        let vm = this;
        vm.$router.push('/wholesaleList');
    }
  }
}
</script>

<style>
/* body {
            background: #FFFFFF; } */
.table_div_wrap {
    border: 1px black solid;
    background: #f0f0f0;
    text-align: center;
    width: 100%;
    /* min-width: 320px;
    max-width: 600px; */
    margin: 0 auto;
    /* margin-top: 100px; */
    position: relative;
    bottom: 10px;
    /* overflow: hidden; */
     }
.table_div_wrap .inner .table_tbody {
    width: 100.6%;
    height: 610px;
    overflow-y: auto;
    overflow-x: auto; }
/* .table_div_wrap .inner .table_thead {
    width: 100.6%;
    height: 610px;
    overflow-y: auto;
    overflow-x: auto; } */
.table_div_wrap .inner table {
    min-width: 100%; }
.table_div_wrap .inner table td, body .table_div_wrap .inner table th {
    border-top: 1px black solid;
    border-right: 1px black solid;
    min-width: 122px;
    height: 40px; }
.table_div_wrap .inner table th {
    border: none;
    border-right: 1px black solid;
    height: 30px; }
.table_div_wrap .outer {
    position: absolute;
    top: 0;
    left: 0; }
.table_div_wrap .outer .table_tbody {
    width: 100%;
    height: 300px;
    overflow-y: hidden;
    overflow-x: scroll; }
.table_div_wrap .outer table {
    min-width: 100%; }
.table_div_wrap .outer table td, body .table_div_wrap .outer table th {
    border-top: 1px black solid;
    border-right: 1px black solid;
    min-width: 150px;
    height: 80px;
    display: none; }
.table_div_wrap .outer table th {
    border: none;
    border-right: 1px black solid;
    height: 30px; }
.table_div_wrap .outer table th:first-child {
    display: table-cell;
    background: #cccccc; }
.table_div_wrap .outer table td:first-child {
    display: table-cell;
    background: #cccccc; }
.getWholesaleDetail .el-checkbox {
    height: 35px;
}
</style>
