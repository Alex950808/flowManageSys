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
                        <span class="bgButton" @click="wholesaleExport()">大批发报价数据导出</span>
                        <span class="bgButton" @click="selectcol()">选择列</span>
                        <span class="bgButton" @click="openBox()">批量修改运输方式和参考折扣</span>
                    </el-row>
                    <div style="margin-bottom: 10px;">
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>上传用户：{{titleData.adminName}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>报价单号：{{titleData.wholesale_sn}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>预计到港时间：{{titleData.estimate_time}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>创建时间：{{titleData.create_time}}</span></div></el-col>
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>空运：{{titleData.air_trans}}($)/kg</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>海运：{{titleData.sea_trans}}($)/kg</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>美金兑人民币汇率：{{titleData.usd_cny_rate}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>代采点：{{titleData.user_disc}}</span></div></el-col>
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>销售用户：{{titleData.sale_user}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>sku数量：{{titleData.goodsCount}}</span></div></el-col>
                        </el-row>
                    </div>
                    <div class="table_div_wrap"  v-if="!isShow">
                        <div class="inner">
                            <div style="overflow: hidden;">
                            <div class="table_thead">
                                <table cellpadding="0" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th colspan='7' rowspan="2">基础信息</th>
                                        <th colspan="3" rowspan="2" v-if="selectTitle('最优渠道折扣')">最优渠道折扣</th>
                                        <th colspan="3" rowspan="2" v-if="selectTitle('代采报价')">代采报价</th>
                                        <th :colspan="titleLength">成本折扣</th>
                                    </tr>
                                    <tr>
                                        <th v-for=" item in titleList" :colspan="item.col" v-if="selectTitle(item.name)">{{item.name}}</th>
                                    </tr>
                                    <tr>
                                        <th style="width:200px;border-bottom: 1px solid #000;">商品名称</th>
                                        <th style="width:200px;border-bottom: 1px solid #000;">平台条码</th>
                                        <th style="width:200px;border-bottom: 1px solid #000;">商品规格码</th>
                                        <th style="width:200px;border-bottom: 1px solid #000;">重量</th>
                                        <th style="width:200px;border-bottom: 1px solid #000;">美金原价</th>
                                        <th style="width:200px;border-bottom: 1px solid #000;">SKU运费
                                            <template>
                                                <el-popover  placement="top-start" width="200" trigger="click">
                                                    <p>SKU运费 = 重量*运输方式对应的运输费用</p>
                                                    <i class="el-icon-question redFont" slot="reference"></i>
                                                </el-popover>
                                            </template>
                                        </th>
                                        <th style="width:200px;border-bottom: 1px solid #000;">运输方式</th>
                                        <th style="width:200px;border-bottom: 1px solid #000;" v-if="selectTitle('最优渠道折扣')">渠道</th>
                                        <th style="width:200px;border-bottom: 1px solid #000;" v-if="selectTitle('最优渠道折扣')">最优折扣
                                            <template>
                                                <el-popover  placement="top-start" width="350" trigger="click">
                                                    <p>最优折扣：成本折扣里所有渠道中最低的最终折扣</p>
                                                    <i class="el-icon-question redFont" slot="reference"></i>
                                                </el-popover>
                                            </template>
                                        </th>
                                        <th style="width:200px;border-bottom: 1px solid #000;" v-if="selectTitle('最优渠道折扣')">参考EXW折扣
                                            <template>
                                                <el-popover  placement="top-start" width="200" trigger="click">
                                                    <p>手动填入，默认为最优折扣</p>
                                                    <i class="el-icon-question redFont" slot="reference"></i>
                                                </el-popover>
                                            </template>
                                        </th>


                                        <th style="width:200px;border-bottom: 1px solid #000;" v-if="selectTitle('代采报价')">报价折扣
                                            <template>
                                                <el-popover  placement="top-start" width="200" trigger="click">
                                                    <p>默认最优折扣+代采点，填入参考exw折扣后则为参考exw折扣+代采点</p>
                                                    <i class="el-icon-question redFont" slot="reference"></i>
                                                </el-popover>
                                            </template>
                                        </th>
                                        <th style="width:200px;border-bottom: 1px solid #000;" v-if="selectTitle('代采报价')">销售美金
                                            <template>
                                                <el-popover  placement="top-start" width="200" trigger="click">
                                                    <p>销售美金=报价折扣*美金原价+运费</p>
                                                    <i class="el-icon-question redFont" slot="reference"></i>
                                                </el-popover>
                                            </template>
                                        </th>
                                        <th style="width:200px;border-bottom: 1px solid #000;" v-if="selectTitle('代采报价')">参考人民币
                                            <template>
                                                <el-popover  placement="top-start" width="200" trigger="click">
                                                    <p>参考人民币=销售美金*汇率</p>
                                                    <i class="el-icon-question redFont" slot="reference"></i>
                                                </el-popover>
                                            </template>
                                        </th>
                                        <th style="width:200px;border-bottom: 1px solid #000;" v-for="item in titleName" v-if="selectTitle(item.fName)">{{item.fieldName}}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            </div>
                            <div class="table_tbody">
                                <table cellpadding="0" cellspacing="0">
                                    <tbody v-for="(item,index) in tableData">
                                        <tr>
                                            <td class="overOneLinesHeid"><span  style="-webkit-box-orient: vertical;height: 63px;line-height: 22px;width: 121px;-webkit-line-clamp: 3;">{{item.goods_name}}</span></td>
                                            <td class="overOneLinesHeid"><span  style="-webkit-box-orient: vertical;line-height: 22px;width: 121px;-webkit-line-clamp: 2;">{{item.platform_no}}</span></td>
                                            <td class="overOneLinesHeid"><span  style="-webkit-box-orient: vertical;line-height: 22px;width: 121px;-webkit-line-clamp: 2;">{{item.spec_sn}}</span></td>
                                            <td>{{item.spec_weight}}</td>
                                            <td>{{item.spec_price}}</td>
                                            <td>{{item.skuFreight}}</td>
                                            <td>
                                                <input type="checkbox" :name="'B'+index" checked @change="selectType('B',index,item.spec_sn,item.transCn)"/>{{item.transCn}}
                                                <span v-if="item.transCn=='海运'"><input type="checkbox" :name="'A'+index" @change="selectType('A',index,item.spec_sn,item.transCn)"/>空运</span>
                                                <span v-if="item.transCn=='空运'"><input type="checkbox" :name="'A'+index" @change="selectType('A',index,item.spec_sn,item.transCn)"/>海运</span>
                                            </td>
                                            <td v-if="selectTitle('最优渠道折扣')"><span v-if="item.discInfo.length!=0">{{item.discInfo.best.channel_name}}</span></td>
                                            <td v-if="selectTitle('最优渠道折扣')"><span v-if="item.discInfo.length!=0">{{item.discInfo.best.final_discount}}</span><span v-else>0</span></td>
                                            <td v-if="selectTitle('最优渠道折扣')">
                                                <span v-if="item.discInfo.length!=0"><input style="width:80px;" @change="editDiscount(index,item.spec_sn)" :name="'dis'+index" :value="item.discInfo.best.referExw"/></span>
                                                <span v-else><input style="width:80px;" @change="editDiscount(index,item.spec_sn)" :name="'dis'+index" :value="0"/></span>
                                            </td>



                                            <td v-if="selectTitle('代采报价')" :class="'offerDisc'+index"><span v-if="item.discInfo.length!=0">{{item.discInfo.waitBuy.offerDisc}}</span><span v-else>0</span></td>
                                            <td v-if="selectTitle('代采报价')" :class="'saleDollar'+index"><span v-if="item.discInfo.length!=0">${{item.discInfo.waitBuy.saleDollar}}</span><span v-else>0</span></td>
                                            <td v-if="selectTitle('代采报价')" :class="'referRmb'+index"><span v-if="item.discInfo.length!=0">￥{{item.discInfo.waitBuy.referRmb}}</span><span v-else>0</span></td>

                                            <td v-for="itemInfo in titleName" v-if="selectTitle(itemInfo.fName)">
                                                <span v-if="item.discInfo.length!=0">{{item.discInfo.cost[itemInfo.cat_key][itemInfo.cat_code]}}</span>
                                                <span v-if="item.discInfo.length==0">0</span>
                                            </td>
                                            <!-- <td v-else>0</td> -->
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
      <selectCol :selectStr="selectStr"></selectCol>
      <div class="beijing">
        <span class="d_table blueFont Cursor" @click="downloadTemplate('/大批发报价-批量修改运输方式和参考折扣.xlsx')">大批发报价-批量修改运输方式和参考折扣.xlsx</span>
    </div>
      <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
      <successfulOperation :msgStr="msgStr"></successfulOperation>
      <operationFailed :msgStr="msgStr"></operationFailed>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import selectCol from '@/components/UiAssemblyList/selectCol'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
export default {
  components:{
      notFound,
      selectCol,
      upDataButtonByBox,
      operationFailed,
      successfulOperation,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      headersStr:'',
      tableData:[],
      titleData:'',
      isShow:false,
      //切换0.5和1 
      isCutMiddle:true,
      //切换1.5和2 
      isCutOneMiddle:true,
      //代采其他折扣
      other_dis:0,
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
      //选择要展示的表头 
        //   dialogVisibleTitle:false,
        //   checkedCities:[],'商品名称','平台条码','商品规格码','重量','美金原价','运费','运输方式',
        //   cityOptions:['基础折扣','代采折扣','全包折扣','代采其他折扣'],
      //选择列 
      selectStr:['最优渠道折扣','代采报价'],
      //列表Title
      titleLength:0,
      titleList:[],
      titleName:[],
      //导入报价销售折扣 
      titleStr:'批量修改运输方式和参考折扣',
      msgStr:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.$store.commit('selectList', this.selectStr);
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
        axios.post(vm.url+vm.$getWholeDetailURL,
            {
                "wholesale_sn":vm.$route.query.wholesale_sn,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close();
            vm.titleData=res.data.order;
            vm.tableData=res.data.goods;
            vm.channelsName = res.data.channel;
            if(res.data.goods.length<15){
                $(".table_tbody").css("width","100%");
                $(".table_tbody").css("height","100%");
            }
            let i = 0
            for(var key in res.data.costTitle){
                for(var keyInfo in res.data.costTitle[key]){
                    i++;
                    vm.titleName.push({"fieldName":res.data.costTitle[key][keyInfo].field_name_cn,"cat_code":res.data.costTitle[key][keyInfo].cat_code,"cat_key":key,"fName":res.data.costTitle[key][0].channels_name})
                }
                vm.titleList.push({"name":res.data.costTitle[key][0].channels_name,"col":res.data.costTitle[key].length,"id":res.data.costTitle[key][0].channel_id});
                vm.selectStr.push(res.data.costTitle[key][0].channels_name)
            }
            console.log(vm.titleList)
            vm.titleLength = i;
            console.log(vm.titleName)
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
    },
    //切换折扣0.5和1 
    switchDiscountMiddle(index,event){
        let vm = this;
        $(event.target).addClass("notBgButton").removeClass("infoNotBgButton")
        $(event.target).siblings('span').removeClass("notBgButton").addClass("infoNotBgButton")
        if(index===0.5){
            vm.isCutMiddle=false;
        }else if(index===1){
            vm.isCutMiddle=true;
        }else if(index===1.5){
            vm.isCutOneMiddle=true;
        }else if(index===2){
            vm.isCutOneMiddle=false;
        }
    },
    selectcol(){
        $(".selectCol_b").fadeIn();
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
    wholesaleExport(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$wholesaleExportURL+'?wholesale_sn='+vm.$route.query.wholesale_sn+'&token='+headersToken);
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
    },
    //单选运输方式 
    selectType(type,index,sn,name){
        let vm = this;
        let inputVal = $("input[name="+type+index+"]").prop("checked");
        let modifyValue;
        if(name=='海运'){
            if(type=='A'){
                if(inputVal==true){
                    $("input[name=B"+index+"]").prop("checked",false);
                    modifyValue = 2;
                }else if(inputVal==false){
                    $("input[name=B"+index+"]").prop("checked",true);
                    modifyValue = 1;
                }
                }else if(type=='B'){
                    if(inputVal==true){
                    $("input[name=A"+index+"]").prop("checked",false);
                    modifyValue = 1;
                }else if(inputVal==false){
                    $("input[name=A"+index+"]").prop("checked",true);
                    modifyValue = 2;
                }
            }
        }
        if(name=='空运'){
            if(type=='A'){
                if(inputVal==true){
                    $("input[name=B"+index+"]").prop("checked",false);
                    modifyValue = 1;
                }else if(inputVal==false){
                    $("input[name=B"+index+"]").prop("checked",true);
                    modifyValue = 2;
                }
                }else if(type=='B'){
                    if(inputVal==true){
                    $("input[name=A"+index+"]").prop("checked",false);
                    modifyValue = 2;
                }else if(inputVal==false){
                    $("input[name=A"+index+"]").prop("checked",true);
                    modifyValue = 1;
                }
            }
        }
        axios.post(vm.url+vm.$modifyWholeDataURL,
            {
                "wholesale_sn":vm.$route.query.wholesale_sn,
                "spec_sn":sn,
                "type":"1",
                "modifyValue":modifyValue
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.$message(res.data.msg);
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //单个修改参考EXW折扣 
    editDiscount(index,sn){
        let vm = this;
        let modifyValue = $("input[name=dis"+index+"]").val();
        axios.post(vm.url+vm.$modifyWholeDataURL,
            {
                "wholesale_sn":vm.$route.query.wholesale_sn,
                "spec_sn":sn,
                "type":"2",
                "modifyValue":modifyValue
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            $(".offerDisc"+index).html(parseFloat(modifyValue)+parseFloat(vm.titleData.user_disc))
            $(".saleDollar"+index).html('$'+((parseFloat(modifyValue)+parseFloat(vm.titleData.user_disc))*parseFloat(vm.tableData[index].spec_price)+parseFloat(vm.tableData[index].spec_weight)).toFixed(4))
            $(".referRmb"+index).html('￥'+(((parseFloat(modifyValue)+parseFloat(vm.titleData.user_disc))*parseFloat(vm.tableData[index].spec_price))*parseFloat(vm.titleData.usd_cny_rate)).toFixed(2))
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //批量修改运输方式和参考折扣
    openBox(){
        let vm = this;
        $(".upDataButtonByBox_b").fadeIn();
        $(".d_table").fadeIn();
        $(".beijing").fadeIn();
    },
    //下载表格模板
      downloadTemplate(str){
        let vm=this;
        window.open(vm.downloadUrl+str);
      },
    //确认上传 
    GFFconfirmUpData(formDate){
        let vm=this;
        $(".upDataButtonByBox_b").fadeOut();
        $(".d_table").fadeOut();
        $(".beijing").fadeOut();
        let headersToken=sessionStorage.getItem("token");
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$batchModifyWholeDataURL+"?wholesale_sn="+vm.$route.query.wholesale_sn,
        type: "POST",
        async: true,
        cache: false,
        headers:{
            'Authorization': 'Bearer ' + headersToken,
            'Accept': 'application/vnd.jmsapi.v1+json',
        },
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code=='2024'){
                vm.msgStr=res.msg;
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                vm.getDataList();
                $("#file").val('');
                vm.fileName='';
            }else{
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
                $("#file").val('');
                vm.fileName='';
            }
        }
        }).catch(function (error) {
                loa.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
  },
  computed:{
    selectTitle(checkedCities){
       return selectTitle(checkedCities)
    },
  }
}
</script>

<style scoped>
.beijing{
    width: 100%;
    height: 100%;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    display: none;
}
.d_table{
    position: fixed;
    z-index: 10039;
    width: 385px;
    height: 27px;
    margin: 250px auto;
    top: 218px;
    left: 72px;
    bottom: 0;
    right: 0;
}
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
