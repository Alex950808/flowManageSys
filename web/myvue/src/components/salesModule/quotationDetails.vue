<template>
  <div class="quotationDetails_b">
      <el-col :span="22" :offset="1">
            <div class="quotationDetails">
                <el-row class="title">
                    <el-col :span="12">
                        <router-link  to='/quotationDevice'><span class="back">返回上一级</span></router-link>
                        <span class="upTitle">报价计算器</span>
                    </el-col>
                    <el-col :span="4">
                        <div class="DJzhekou">
                            <el-button type="primary" @click="dialogVisibleRate = true;" size="mini">销售折扣</el-button>
                            <el-dialog title="销售折扣" :visible.sync="dialogVisibleRate" width="500px">
                                <el-select v-model="pricingDiscounts" placeholder="请选择">
                                    <el-option v-for="item in grossInterestRate" :key="item" :label="item" :value="item"></el-option>
                                </el-select>
                                <span slot="footer" class="dialog-footer">
                                    <el-button type="info" @click="dialogVisibleRate = false">取 消</el-button>
                                    <el-button type="primary" @click="dialogVisibleRate = false;batchModPricRate()">确 定</el-button>
                                </span>
                            </el-dialog>
                        </div>
                    </el-col>
                    <el-col :span="8">
                        <div class="text_right">
                            <span>报价部门：</span>
                            <template>
                                <el-select v-model="department" @change="selectionDepartment()" placeholder="请选择">
                                    <el-option v-for="item in departments" :key="item.label" :label="item.label" :value="item.value"></el-option>
                                </el-select>
                            </template>
                        </div>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="16">
                        <div id="nav1" class="nav swiper-container swiper-container1 screenScrolling">
                            <ul class="swiper-wrapper secNav">
                                <li class="brandName xuanzhong swiper-slide" @click="getUserNameData('全部',0)">全部<input type="text" title="0" style="display:none"/></li>
                                <li class="brandName swiper-slide" v-for="(item,index) in tableTitle" @click="getUserNameData(item.brand_name,index+1)">{{item.brand_name}}<input type="text" :title="index+1" style="display:none"/></li>
                            </ul>
                        </div>
                    </el-col>
                    <el-col :span="8">
                        <!-- <template>
                            <el-radio-group v-model="radio">
                                <el-radio v-for="item in erpInfo" @change="isStoreHome()" :label="item.store_id">{{item.store_name}}</el-radio>
                            </el-radio-group>
                        </template> -->
                        <div class="text_right" style="margin-top: 20px;">
                            <span><i class="NotStyleForI MR_twenty">仓</i>位：</span>
                        <template>
                            <el-select v-model="storeId" @change="isStoreHome()" placeholder="请选择仓位">
                                <el-option v-for="item in erpInfo" :key="item.store_name" :label="item.store_name" :value="item.store_id"></el-option>
                            </el-select>
                        </template>
                        </div>
                    </el-col>
                </el-row>
                
                <div class="t_i">
                    <div class="t_i_h" id="hh">
                        <div class="ee">
                            <table cellpadding="0" cellspacing="0" border="1" :style="tableWidth()">
                            <thead>
                                <tr>
                                    <th rowspan="2" colspan="8">产品基础信息</th>
                                    <th colspan="7">成本</th>
                                    <th :colspan="rateTdWidth+3">定价</th>
                                    <th :colspan="totalCost" title="销售折扣*各项费用比率">费用</th>
                                    <th rowspan="3" title="销售毛利率-费用合计" style="width:80px">运营毛利</th>
                                </tr>
                                <tr>
                                    <th>自采</th>
                                    <th>外采</th>
                                    <th colspan="5">erp仓库</th>
                                    <th :colspan="rateTdWidth" title="重价比折扣/（1-对应档位利率）">自采毛利率<i class="el-icon-circle-plus" @click="dialogVisible = true"></i></th>
                                    <th colspan="3">销售定价</th>
                                    <!-- <th>合伙人</th>
                                    <th>团长</th>
                                    <th>会员</th>
                                    <th>支付宝</th>
                                    <th>费用合计</th> -->
                                    <th v-for="item in costItem"><span v-for="(key,value) in item">{{key}}</span></th>
                                </tr>
                                <tr>
                                    <th style="width:200px">品牌</th>
                                    <th style="width:250px">品名</th>
                                    <th style="width:80px">美金原价</th>
                                    <th style="width:80px">重量</th>
                                    <th style="width:80px">DFS金卡折扣</th>
                                    <th style="width:80px">DFS金卡价(美金)</th>
                                    <th style="width:80px">DFS黑卡折扣</th>
                                    <th style="width:80px">DFS黑卡价(美金)</th>
                                    <th style="width:80px">EXW折扣</th>
                                    <th style="width:80px">外采折扣</th>
                                    <th style="width:80px">仓库名称</th>
                                    <th style="width:80px" title="重量/美金原价/重价系数/100">重价比</th>
                                    <th style="width:80px" title="EXW折扣+重价比">重价比折扣</th>
                                    <th style="width:80px" title="美金原价*重价比折扣">ERP成本价￥</th>
                                    <th style="width:80px" title="等于重价比折扣">ERP成本折扣</th>
                                    <th style="width:80px" v-for="item in grossInterestRate">{{item}}</th>
                                    <th style="width:80px" title="(默认取自采毛利率的27%档)，可以手动填写">销售折扣</th>
                                    <th style="width:80px" title="美金原价*销售折扣*汇率">销售价(人民币)</th>
                                    <th style="width:80px" title="1-重价比折扣/销售折扣">销售毛利率</th>
                                    <th style="width:80px" v-for="item in costItem"><span v-for="(key,value) in item">{{value}}</span></th>
                                </tr>
                            </thead>
                            </table>
                        </div>
                    </div>
                    <div class="cc" id="cc" @scroll="scrollEvent()">
                        <table cellpadding="0" cellspacing="0" border="1" :style="tableWidth()">
                            <tr v-for="(item,index) in tableData" v-show="whole">
                                <td class="ellipsis" style="width:200px;">
                                    <el-tooltip class="item" effect="light" :content="item.brand_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{item.brand_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td class="ellipsis" style="width:250px">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td style="width:80px">{{item.spec_price}}</td>
                                <td style="width:80px">{{item.spec_weight}}</td>
                                <td style="width:80px">{{item.gold_discount}}</td>
                                <td style="width:80px">{{item.gold_price}}</td>
                                <td style="width:80px">{{item.black_discount}}</td>
                                <td style="width:80px">{{item.black_price}}</td>
                                <td style="width:80px">{{item.exw_discount}}</td>
                                <td style="width:80px">{{item.foreign_discount}}</td>
                                <td style="width:80px">{{item.store_name}}</td>
                                <td style="width:80px">{{item.high_price_ratio}}</td>
                                <td style="width:80px">{{item.hpr_discount}}</td>
                                <td style="width:80px">{{item.erp_cost_price}}</td>
                                <td style="width:80px">{{item.high_price_ratio}}</td>
                                <td style="width:80px" class="WholeChargeRate" v-for="itemO in item.arrMarginRate"><span  v-for="itemT in grossInterestRate">{{itemO[itemT]}}</span></td>
                                <td style="width:80px" class="WholeInput"><input type="text" :name="index"  @change="reviseDiscounts(item.spec_sn,index,'Whole')" :value="item.pricing_rate"/></td>
                                <td style="width:80px">{{item.salePrice}}</td>
                                <td style="width:80px">{{item.saleMarRate}}</td>
                                <td style="width:80px" v-for="itemO in item.arrChargeRate"><span v-for="(key,value) in itemO">{{key}}</span></td>
                                <td style="width:80px">{{item.runMarRate}}</td>
                            </tr>
                            <tr v-for="(item,index) in brandTableData" v-show="Part">
                                <td class="ellipsis" style="width:200px;">
                                    <el-tooltip class="item" effect="light" :content="item.brand_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{item.brand_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td class="ellipsis" style="width:250px">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td style="width:80px">{{item.spec_price}}</td>
                                <td style="width:80px">{{item.spec_weight}}</td>
                                <td style="width:80px">{{item.gold_discount}}</td>
                                <td style="width:80px">{{item.gold_price}}</td>
                                <td style="width:80px">{{item.black_discount}}</td>
                                <td style="width:80px">{{item.black_price}}</td>
                                <td style="width:80px">{{item.exw_discount}}</td>
                                <td style="width:80px">{{item.foreign_discount}}</td>
                                <td style="width:80px">{{item.store_name}}</td>
                                <td style="width:80px">{{item.high_price_ratio}}</td>
                                <td style="width:80px">{{item.hpr_discount}}</td>
                                <td style="width:80px">{{item.erp_cost_price}}</td>
                                <td style="width:80px">{{item.high_price_ratio}}</td>
                                <td style="width:80px" class="partChargeRate" v-for="itemO in item.arrMarginRate"><span  v-for="itemT in grossInterestRate">{{itemO[itemT]}}</span></td>
                                <td style="width:80px" class="partInput"><input type="text" :name="index" @change="reviseDiscounts(item.spec_sn,index,'part')" :value="item.pricing_rate"/></td>
                                <td style="width:80px">{{item.salePrice}}</td>
                                <td style="width:80px">{{item.saleMarRate}}</td>
                                <td style="width:80px" v-for="itemO in item.arrChargeRate"><span v-for="(key,value) in itemO">{{key}}</span></td>
                                <td style="width:80px">{{item.runMarRate}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <el-dialog title="自定义毛利率" :visible.sync="dialogVisible" width="500px">
                    <span><el-input v-model="interestRate" placeholder="请输入毛利率" style="width:200px;"></el-input></span>
                    <span slot="footer" class="dialog-footer">
                        <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                        <el-button type="primary" @click="dialogVisible = false;confirmInterest()">确 定</el-button>
                    </span>
                </el-dialog>
            </div>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import Swiper from 'swiper'; 
import 'swiper/dist/css/swiper.min.css';
export default {
  name: 'App',
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      grossInterestRate:[],//毛利率
      costItem:[],//费用项
      department:'',
      tableTitle:[],
      dialogVisible: false,
      dialogVisibleRate:false,
      interestRate:'',//自定义毛利率
      pricingDiscounts:'',//批量修改销售折扣
      storeId:'',//仓位id
      rateTdWidth:'',
      brandTableData:[],
      erpInfo:[],
      totalCost:'',
      Part:false,
      whole:true,
      brandName:'',
      index:'',
      departments:[],
    }
  },
  mounted(){
    var Swiper1 = new Swiper('.swiper-container',{
        slidesPerView :'auto',
        spaceBetween: 6
    });
    this.getlistData();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
    //   gatTableData(){
    //       let vm=this;
          
    //       let tableDataOne=JSON.parse(sessionStorage.getItem("tableData"));
    //       vm.tableData=tableDataOne.demandGoodsInfo;
    //       vm.grossInterestRate=tableDataOne.arrPickRate;
    //       vm.costItem=tableDataOne.arrCharge;
    //       vm.tableTitle=tableDataOne.brand_info;
    //       vm.erpInfo=tableDataOne.erpInfo;
    //       vm.rateTdWidth=tableDataOne.arrPickRate.length;
    //       vm.totalCost=tableDataOne.arrCharge.length;
    //   },
      getlistData(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          vm.erpInfo.splice(0);
          vm.departments.splice(0);
          axios.get(vm.url+vm.$queryNeedGoodsInfoURL+"?demand_sn="+vm.$route.query.demand_sn+"&store_id="+vm.storeId+"&department_id="+vm.department,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
          ).then(function(res){
                vm.loading.close();
                vm.tableData=res.data.demandGoodsInfo;
                vm.grossInterestRate=res.data.arrPickRate;
                vm.costItem=res.data.arrCharge;
                vm.tableTitle=res.data.brand_info;
                // vm.erpInfo=res.data.erpInfo;
                res.data.erpInfo.forEach(element=>{
                    vm.erpInfo.push({"store_name":element.store_name,"store_id":element.store_id});
                })
                res.data.departmentInfo.forEach(element=>{
                    vm.departments.push({value: element.department_id,label: element.de_name})
                })
                vm.rateTdWidth=res.data.arrPickRate.length;
                vm.totalCost=res.data.arrCharge.length;
                vm.storeId=res.data.store_id;
                vm.department=res.data.department_id
          }).catch(function (error) {
                vm.loading.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      getUserNameData(brandName,index){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.brandName=brandName;
            vm.index=index;
            $(".brandName input[title="+index+"]").parent().addClass("xuanzhong")
            $(".brandName input[title!="+index+"]").parent().addClass("weixuanzhong")
            $(".brandName input[title="+index+"]").parent().removeClass("weixuanzhong")
            $(".brandName input[title!="+index+"]").parent().removeClass("xuanzhong")
            vm.brandTableData.splice(0);
            if(brandName=="全部"){
                vm.whole=true;
                vm.Part=false;
            }else{
                vm.whole=false;
                vm.Part=true;
                vm.tableData.forEach(element=>{
                    if(element.brand_name==brandName){
                        vm.brandTableData.push(element);
                    }
                })
            }
        },
        //添加自采毛利率
        confirmInterest(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.post(vm.url+vm.$addMarginRateURL,
                {
                    "mar_rate":vm.interestRate,
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                if(res.data.code==2015){
                    vm.dialogVisible = false;
                    axios.get(vm.url+vm.$queryNeedGoodsInfoURL+"?demand_sn="+vm.$route.query.demand_sn+"&store_id="+vm.storeId,
                            {
                                headers:{
                                    'Authorization': 'Bearer ' + headersToken,
                                    'Accept': 'application/vnd.jmsapi.v1+json',
                                }
                            }
                    ).then(function(res){
                            vm.dialogVisibleRate = false;
                            vm.tableData=res.data.demandGoodsInfo;
                            vm.grossInterestRate=res.data.arrPickRate;
                            vm.costItem=res.data.arrCharge;
                            vm.tableTitle=res.data.brand_info;
                            vm.rateTdWidth=res.data.arrPickRate.length;
                            vm.totalCost=res.data.arrCharge.length;
                            let brandName=vm.brandName;
                            let index=vm.index;
                            if(brandName!=''&&index!=''){
                                vm.getUserNameData(brandName,index);
                            }
                    })
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
        //单个修改销售折扣
        reviseDiscounts(spec_sn,index,isWhole){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            let Inputval;
            if(isWhole=="Whole"){
                Inputval = $(".WholeInput input[name="+index+"]").val();
            }else{
                Inputval = $(".partInput input[name="+index+"]").val();
            }
            axios.post(vm.url+vm.$modifyPricingRateURL,
                {
                    "demand_sn":vm.$route.query.demand_sn,
                    "spec_sn":spec_sn,
                    "pricing_rate":Inputval,
                    "store_id":vm.storeId,
                    "department_id":vm.department,
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                if(res.data.code!=2023){
                    if(isWhole=="Whole"){
                        vm.tableData.forEach(element=>{
                            if(spec_sn==element.spec_sn){
                                vm.tableData.splice(index,1,res.data.goodsInfo); 
                            }
                        })
                    }else{
                        vm.brandTableData.forEach(element=>{
                            if(spec_sn==element.spec_sn){
                                vm.brandTableData.splice(index,1,res.data.goodsInfo); 
                            }
                        })
                    }
                }
                // sessionStorage.setItem("tableData",JSON.stringify(vm.tableData));
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //选择仓位
        isStoreHome(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$queryNeedGoodsInfoURL+"?demand_sn="+vm.$route.query.demand_sn+"&store_id="+vm.storeId,
                    {
                        headers:{
                            'Authorization': 'Bearer ' + headersToken,
                            'Accept': 'application/vnd.jmsapi.v1+json',
                        }
                    }
            ).then(function(res){
                    vm.dialogVisibleRate = false;
                    vm.tableData=res.data.demandGoodsInfo;
                    vm.grossInterestRate=res.data.arrPickRate;
                    vm.costItem=res.data.arrCharge;
                    vm.tableTitle=res.data.brand_info;
                    vm.rateTdWidth=res.data.arrPickRate.length;
                    vm.totalCost=res.data.arrCharge.length;
                    let brandName=vm.brandName;
                    let index=vm.index;
                    if(brandName!=''&&index!=''){
                        vm.getUserNameData(brandName,index);
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
        //选择部门
        selectionDepartment(){
            let vm=this;
            vm.getlistData();
            let brandName=vm.brandName;
            let index=vm.index;
            if(brandName!=''&&index!=''){
                vm.getUserNameData(brandName,index);
            }
        },
        //批量修改销售折扣
        batchModPricRate(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.post(vm.url+vm.$batchModPricRateURL,
                    {
                        "demand_sn":vm.$route.query.demand_sn,
                        "pick_margin_rate":vm.pricingDiscounts,
                        "store_id":vm.storeId,
                        "department_id":vm.department,
                    },
                    {
                        headers:{
                            'Authorization': 'Bearer ' + headersToken,
                            'Accept': 'application/vnd.jmsapi.v1+json',
                        }
                    }
            ).then(function(res){
                    if(res.data.code==2024){
                        axios.get(vm.url+vm.$queryNeedGoodsInfoURL+"?demand_sn="+vm.$route.query.demand_sn+"&store_id="+vm.storeId,
                            {
                                headers:{
                                    'Authorization': 'Bearer ' + headersToken,
                                    'Accept': 'application/vnd.jmsapi.v1+json',
                                }
                            }
                        ).then(function(res){
                                vm.dialogVisibleRate = false;
                                vm.tableData=res.data.demandGoodsInfo;
                                vm.grossInterestRate=res.data.arrPickRate;
                                vm.costItem=res.data.arrCharge;
                                vm.tableTitle=res.data.brand_info;
                                vm.rateTdWidth=res.data.arrPickRate.length;
                                vm.totalCost=res.data.arrCharge.length;
                                let brandName=vm.brandName;
                                let index=vm.index;
                                if(brandName!=''&&index!=''){
                                    vm.getUserNameData(brandName,index);
                                }
                        })
                    }else{
                        vm.$message(res.data.msg);
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
        //表格宽度计算
        tableWidth(){
            let vm=this;
            var widthLength=1900+(80*vm.rateTdWidth)+(80*vm.totalCost);
            return "width:"+widthLength+"px"
        },
      scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
      },
  }
}
</script>

<style>
.quotationDetails_b .back{
    display: inline-block;
    width: 200px;
    height: 50px;
    background-color: #00C1DE;
    color: #fff;
    line-height: 50px;
    text-align: center;
    margin-top: 25px;
    border-radius:10px; 
    cursor: pointer;
}
.quotationDetails_b .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
.quotationDetails_b .text_right{
    margin-top: 35px;
    float: right;
}
.quotationDetails_b .brandName{
    float: left;
}
.quotationDetails_b .brandName{
    display: inline-block;
    width: 150px;
    height: 30px;
    border-radius: 10px;
    background: #ccc;
    color: #fff;
    text-align: center;
    line-height: 30px;
    margin-right: 20px;
    margin-bottom: 10px;
    cursor: pointer;
}
.quotationDetails_b .DJzhekou{
    margin-top: 40px;
    float: right;
}
ul {
    list-style-type: none;
    padding: 0;
}
.quotationDetails_b .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical; 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.quotationDetails_b .el-icon-circle-plus{
    /* float: right; */
    margin-left: 10px;
    font-size: 20px;
    cursor: pointer;
}
.quotationDetails_b .el-dialog__body{
    text-align: center;
}
.quotationDetails_b .el-dialog__header{
    text-align: center;
}
.quotationDetails_b .xuanzhong{
    background: #00C1DE;
}
.quotationDetails_b .weixuanzhong{
    background-color: #ccc;
}
.quotationDetails_b .WholeInput input{
    width: 80%;
}
.quotationDetails_b .partInput input{
    width: 80%;
}
.quotationDetails_b .el-radio-group {
    margin-top: 10px;
    line-height: 30px;
}
.quotationDetails_b table { text-align: center; border-color:#ccc; border-collapse: collapse;} 
.quotationDetails_b .t_n{width:19%; height:703px; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.quotationDetails_b .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.quotationDetails_b .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.quotationDetails_b .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.quotationDetails_b .dd{height:659px!important; height:659px; overflow-y:hidden;}
.quotationDetails_b .t_i{width:99.9%; height:auto; float:left; border-right:1px solid #ccc;border-left: 1px solid #ccc;border-top:1px solid #ccc}
.quotationDetails_b .t_i_h{width:100%; overflow-x:hidden;}
.quotationDetails_b .ee{width:110%!important; width:110%; text-align: center;background: #fff;}
.quotationDetails_b .t_i_h table{width:3400px;border-top: #ccc;border-left: #ccc;border-right: #ccc;border-bottom: #ccc;}
.quotationDetails_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.quotationDetails_b .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.quotationDetails_b .cc table{width:3400px;border-top: #ccc;border-left: #ccc;border-right: #ccc; }
.quotationDetails_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>
