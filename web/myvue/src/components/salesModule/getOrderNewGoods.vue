<template>
  <div class="getOrderNewGoods">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle title">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>总单新品列表</span>
                        <span v-if="replenishInfo.replenishDesc!='已补单'" class="bgButton floatRight MT_twenty" @click="openAudit('','','3');">补充新品到对应总单中</span>
                        <span v-if="createdInfo.finish_int!=1" class="bgButton floatRight MT_twenty MR_twenty" @click="openDiscountBox('1');">商品批量新增</span>
                    </div>
                    <div class="MB_ten">
                        <span class="fontWeight F_S_twenty">订单信息：</span>
                    </div>
                    <el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>MIS订单号：{{misOrderInfo.mis_order_sn}}</span>&nbsp;&nbsp;</div></el-col>
                            <!-- <el-col :span="5" :offset="1"><div><span>部门：{{MIStitleData.de_name}}</span></div></el-col> -->
                            <el-col :span="5" :offset="1"><div><span>部门：{{misOrderInfo.de_name}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>是否报价：{{misOrderInfo.offer_desc}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>是否预判：{{misOrderInfo.advance_desc}}</span></div></el-col>
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>用户名称：{{misOrderInfo.user_name}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>创建时间：{{misOrderInfo.create_time}}</span>&nbsp;&nbsp;</div></el-col>
                            <el-col :span="5" :offset="1"><div><span>新品数量：{{replenishInfo.newGoodsNum}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>新品是否完成创建：{{createdInfo.finish_desc}}</span></div></el-col>
                            <!-- <el-col :span="5" :offset="1"><div><span>是否挂靠：{{misOrderInfo.status_desc}}</span></div></el-col> -->
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>新品是否已补单：{{replenishInfo.replenishDesc}}</span>&nbsp;&nbsp;</div></el-col>
                        </el-row>
                    </el-row>
                    <div class="MB_ten">
                        <span class="fontWeight F_S_twenty">商品信息：</span>
                        <span class="bgButton" @click="selectcol()">选择列</span>
                        <span class="bgButton" v-if="createdInfo.finish_int!=1" @click.once="d_table()">导出总单新品</span> 
                        <span class="bgButton" v-if="createdInfo.finish_int!=1" @click="confirmShow()">导入总单新品</span> 
                    </div>
                    <el-row>
                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                        <thead>
                                            <tr>
                                                <th class="w_Fty" style="width:60px"><input class="allChecked" type="checkbox" @change="allSelect()"/>全选</th>
                                                <th style="width:60px" v-if="selectTitle('ID')">ID</th>
                                                <th class="ellipsis" v-if="selectTitle('商品名称')" style="width:300px;">商品名称</th>
                                                <th style="width:200px" v-if="selectTitle('品牌名称')">品牌名称</th>
                                                <th style="width:160px" v-if="selectTitle('商品规格码')">商品规格码</th>
                                                <th style="width:160px" v-if="selectTitle('平台条码')">平台条码</th>
                                                <th style="width:100px" v-if="selectTitle('美金原价')">美金原价</th>
                                                <th style="width:90px" v-if="selectTitle('商品重量')">商品重量</th>
                                                <!-- <th style="width:90px" v-if="selectTitle('预估重量')">预估重量</th> -->
                                                <th style="width:90px" v-if="selectTitle('EXW折扣')">EXW折扣</th>
                                                <th style="width:90px" v-if="selectTitle('新品状态')">新品状态</th>
                                                <th style="width:160px" v-if="selectTitle('商品代码')">商品代码</th>
                                                <th style="width:160px" v-if="selectTitle('商品参考码')">商品参考码</th>
                                                <th style="width:160px" v-if="selectTitle('商家编码')">商家编码</th>
                                                <th style="width:220px" v-if="selectTitle('操作')">操作</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc"  @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <tbody>
                                        <tr v-for="(item,index) in tableData">
                                            <td class="w_Fty cb_one" style="width:60px">
                                                <input @click="oneSelect(index)" type="checkbox" :name="index"/>
                                            </td>
                                            <td style="width:60px" v-if="selectTitle('ID')">{{item.id}}</td>
                                            <td class="overOneLinesHeid fontLift" style="width:300px;" v-if="selectTitle('商品名称')">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="top">
                                                <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:200px" v-if="selectTitle('品牌名称')" class="overOneLinesHeid fontLift" :title='item.brand_name'>
                                                <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{item.brand_name}}</span>
                                            </td>
                                            <td style="width:160px" v-if="selectTitle('商品规格码')">{{item.spec_sn}}</td>
                                            <td class="overOneLinesHeid" style="width:160px" v-if="selectTitle('平台条码')">
                                                <el-tooltip class="item" effect="light" :content="item.platform_barcode" placement="top">
                                                    <span style="-webkit-box-orient: vertical;width:160px">&nbsp;&nbsp;{{item.platform_barcode}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:100px" v-if="selectTitle('美金原价')">{{item.spec_price}}</td>
                                            <td style="width:90px" v-if="selectTitle('商品重量')">
                                                <span v-if="item.spec_weight!='0.0000'">{{item.spec_weight}}</span>
                                                <span v-else>{{item.estimate_weight}}(预估)</span>
                                            </td>
                                            <!-- <td style="width:90px" v-if="selectTitle('预估重量')">{{item.estimate_weight}}</td> -->
                                            <td style="width:90px" v-if="selectTitle('EXW折扣')">{{item.exw_discount}}</td>
                                            <td style="width:90px" v-if="selectTitle('新品状态')">
                                                <span v-if="item.is_created==0">未新增</span>
                                                <span v-if="item.is_created==1">已新增</span>
                                            </td>
                                            <td style="width:160px" v-if="selectTitle('商品代码')">{{item.erp_prd_no}}</td>
                                            <td style="width:160px" v-if="selectTitle('商品参考码')">{{item.erp_ref_no}}</td>
                                            <td style="width:160px" v-if="selectTitle('商家编码')">{{item.erp_merchant_no}}</td>
                                            <td style="width:220px" v-if="selectTitle('操作')">
                                                <span class="notBgButton" v-if="item.is_created==0" @click="openEditDox(item.id,item.mis_order_sn)">编辑</span>
                                                <span class="notBgButton" v-if="item.is_created==0" @click="openAudit(item.id,item.mis_order_sn,'2');">新增</span>
                                                <span class="notBgButton" v-if="item.is_created==0" @click="openAddspecBox(item.id,item.mis_order_sn)">新增规格</span>
                                                <span v-if="item.is_created!=0">暂无操作</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </el-row>
                    <el-dialog title="编辑新品" :visible.sync="editDialogVisible" width="800px">
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">总单单号：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten">
                                    {{editNewGoodsInfo.mis_order_sn}}
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">平台条码：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten overoneLinesHeid">
                                    {{editNewGoodsInfo.platform_barcode}}
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">商品参考码：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="erpRefNo" placeholder="请输入商品参考码"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">商品代码：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="erpPrdNo" placeholder="请输入商品代码"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">商品重量<i class="redFont">*</i>：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="specWeight" v-validate="'required'" name="specWeight" placeholder="请输入商品重量"></el-input>
                                    <span v-show="errors.has('specWeight')" class="text-style redFont" v-cloak> {{ errors.first('specWeight') }} </span>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">预估重量<i class="redFont">*</i>：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="estimateWeight" v-validate="'required'" name="estimateWeighy" placeholder="请输入商品预估重量"></el-input>
                                    <span v-show="errors.has('estimateWeighy')" class="text-style redFont" v-cloak> {{ errors.first('estimateWeighy') }} </span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">商品名称<i class="redFont">*</i>：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="goodsName" v-validate="'required'" name="goodsName" placeholder="请输入商品名称"></el-input>
                                    <span v-show="errors.has('goodsName')" class="text-style redFont" v-cloak> {{ errors.first('goodsName') }} </span>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">品牌名称<i class="redFont">*</i>：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="brandName" v-validate="'required'" name="brandName" clearable filterable placeholder="请选择品牌名称">
                                            <el-option v-for="(item,index) in brandList" :key="index" :label="item.name" :value="item.name"></el-option>
                                        </el-select>
                                    </template>
                                    <span v-show="errors.has('brandName')" class="text-style redFont" v-cloak> {{ errors.first('brandName') }} </span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">美金原价<i class="redFont">*</i>：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input type="number" v-model="specPrice" v-validate="'required'" name="specPrice" placeholder="请输入商品名称"></el-input>
                                    <span v-show="errors.has('specPrice')" class="text-style redFont" v-cloak> {{ errors.first('specPrice') }} </span>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">EXW折扣<i class="redFont">*</i>：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="exwDiscount" v-validate="'required|number'" name="exwDiscount" placeholder="请输入EXW折扣"></el-input>
                                    <span v-show="errors.has('exwDiscount')" class="text-style redFont" v-cloak> {{ errors.first('exwDiscount') }} </span>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="editDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="modifyNewGoodsData()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-dialog title="新增规格" :visible.sync="AddspecDialogVisible" width="800px">
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">总单单号：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten">
                                    {{editNewGoodsInfo.mis_order_sn}}
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">平台条码：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten overoneLinesHeid">
                                    {{editNewGoodsInfo.platform_barcode}}
                                </div>
                            </el-col>
                            <!-- <el-col :span="3" :offset="1"><div class="MT_ten">小红书码：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten overoneLinesHeid">
                                    {{editNewGoodsInfo.xhs_code}}
                                </div>
                            </el-col> -->
                        </el-row>
                        <el-row class="MB_twenty">
                            <!-- <el-col :span="3" :offset="1"><div class="MT_ten">考拉码：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten overoneLinesHeid">
                                    {{editNewGoodsInfo.kl_code}}
                                </div>
                            </el-col> -->
                            <el-col :span="3" :offset="1"><div class="MT_ten">美金原价：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten">
                                    {{editNewGoodsInfo.spec_price}}
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">商品重量：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten">
                                    <span v-if="editNewGoodsInfo.spec_weight!=null">{{editNewGoodsInfo.spec_weight}}</span>
                                    <span v-else>{{editNewGoodsInfo.estimate_weight}}(预估)</span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">商品名称<i class="redFont">*</i>：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <!-- <el-input v-model="goodsName" v-validate="'required'" name="goodsName" placeholder="请输入商品名称"></el-input> -->
                                    <el-autocomplete v-model="goodsName" :fetch-suggestions="querySearchAsync" v-validate="'required'" name="goodsName" placeholder="请输入内容" @select="handleSelect"></el-autocomplete>
                                    <span v-show="errors.has('goodsName')" class="text-style redFont" v-cloak> {{ errors.first('goodsName') }} </span>
                                    <span v-show="is_notSn" class="redFont" v-cloak>请选择正确的商品名</span>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="MT_ten">品牌名称：</div></el-col>
                            <el-col :span="6">
                                <div class="lineHeightForty overOneLinesHeid">
                                    <span style="-webkit-box-orient: vertical;">{{editNewGoodsInfo.brand_name}}</span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="3" :offset="1"><div class="MT_ten">EXW折扣：</div></el-col>
                            <el-col :span="6">
                                <div class="MT_ten">
                                    {{editNewGoodsInfo.exw_discount}}
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button @click="AddspecDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="addSpecData(errors)">新增规格</el-button>
                        </span>
                    </el-dialog>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
      <selectCol :selectStr="selectStr"></selectCol>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { tableWidth } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { exportsa } from '@/filters/publicMethods.js'
import selectCol from '@/components/UiAssemblyList/selectCol'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
export default {
  components:{
      notFound,
      selectCol,
      customConfirmationBoxes,
      upDataButtonByBox,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      misOrderInfo:'',
      createdInfo:'',
      replenishInfo:'',
      isShow:false,
      //选择列
      selectStr:['ID','商品名称','品牌名称','商品规格码','商品代码','商家编码','商品参考码','平台条码','美金原价','商品重量','EXW折扣','新品状态','操作'],
      //编辑新品
      editDialogVisible:false,
      editNewGoodsInfo:'',
      name:'',
      brandList:[],
      goodsName:'',
      brandName:'',
      specPrice:'',
      ng_id:'',
      contentStr:'',
      NG_id:'',
      status:'',
      specWeight:'',
      estimateWeight:'',
      exwDiscount:'',
      erpRefNo:'',
      erpPrdNo:'',
      //新增规格
      AddspecDialogVisible:false,
      AddspecNewGoodsInfo:'',
      restaurants: [],
      goods_sn:'',
      is_notSn:false,
      //批量导入新品
      titleStr:'批量更新总单新品',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'}); 
    this.getDetailsData();
    this.$store.commit('selectList', this.selectStr);
  },
  methods:{
    //获取详情数据
    getDetailsData(){
        let vm=this;
        let tableData=JSON.parse(sessionStorage.getItem("tableData"));
        if(tableData=='1'){
        vm.getDataList();
            return false;
        };
        vm.tableData=tableData.newGoodsInfo;
        vm.misOrderInfo=tableData.misOrderInfo[0];
        vm.createdInfo=tableData.createdInfo;
        vm.replenishInfo=tableData.replenishInfo;
        tableStyleByDataLength(vm.tableData.length,15)
        sessionStorage.setItem("tableData",'1');
    },
    getDataList(){
        let vm = this;
        axios.post(vm.url+vm.$getOrderNewGoodsURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.newGoodsInfo.length!=0){
                vm.tableData = res.data.newGoodsInfo;
                vm.misOrderInfo=res.data.misOrderInfo[0];
                vm.createdInfo=res.data.createdInfo;
                vm.replenishInfo=res.data.replenishInfo;
                tableStyleByDataLength(vm.tableData.length,15)
                vm.isShow = false;
            }else{
                vm.isShow = true;
                vm.tableData = [];
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    //回上一级页面 
    backUpPage(){
        let vm=this;
        let urlParam = '';
        if(vm.$route.query.misOrderSn!=undefined){
            urlParam='&misOrderSn='+vm.$route.query.misOrderSn
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            urlParam+='&erp_merchant_no='+vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.goods_name!=undefined){
            urlParam+='&goods_name='+vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            urlParam+='&spec_sn='+vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            urlParam+='&sale_user_id='+vm.$route.query.sale_user_id
        }
        vm.$router.push('/MISorderListDetails?mis_order_sn='+vm.$route.query.mis_order_sn+"&isMISOrder=isMISOrder"+urlParam);  
    },
    //编辑
    openEditDox(id,sn){
        let vm = this;
        vm.ng_id=id;
        axios.post(vm.url+vm.$queryNewGoodsDataURL,
            {
                "ng_id":id,
                "mis_order_sn":sn,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.editDialogVisible = true;
            vm.brandList=res.data.brandInfo;
            vm.editNewGoodsInfo=res.data.newGoodsInfo;
            vm.goodsName=res.data.newGoodsInfo.goods_name;
            vm.specPrice=res.data.newGoodsInfo.spec_price;
            vm.brandName=res.data.newGoodsInfo.brand_name;
            vm.specWeight=res.data.newGoodsInfo.spec_weight;
            vm.exwDiscount=res.data.newGoodsInfo.exw_discount;
            vm.estimateWeight=res.data.newGoodsInfo.estimate_weight;
            vm.erpPrdNo=res.data.newGoodsInfo.erp_prd_no;
            vm.erpRefNo=res.data.newGoodsInfo.erp_ref_no;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //确定编辑商品
    modifyNewGoodsData(){
        let vm = this;
        this.$validator.validateAll().then(valid => {
            if(valid){
                vm.editDialogVisible=false;
                axios.post(vm.url+vm.$modifyNewGoodsDataURL,
                    {
                        "mis_order_sn":vm.$route.query.mis_order_sn,
                        "ng_id":vm.ng_id,
                        "brand_name":vm.brandName,
                        "goods_name":vm.goodsName,
                        "spec_price":vm.specPrice,
                        "spec_weight":vm.specWeight,
                        "exw_discount":vm.exwDiscount,
                        "estimate_weight":vm.estimateWeight,
                        "erp_ref_no":vm.erpRefNo,
                        "erp_prd_no":vm.erpPrdNo
                    },
                    {
                        headers:vm.headersStr,
                    }
                ).then(function(res){
                    vm.$message(res.data.msg);
                    vm.getDataList();
                }).catch(function (error) {
                    if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                    }
                });
            }
        })
        
    },
    openAudit(id,sn,e){
        let vm = this;
        vm.ng_id=id;
        vm.status=e;
        if(vm.status=='2'){
            vm.contentStr='请您确认是否要新增？';
        }else if(vm.status=='3'){
            vm.contentStr='请您确认是否要补充新品到对应总单中？';
        }
        $(".confirmPopup_b").fadeIn();
    },
    //取消新增商品
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    confirmationAudit(){
        let vm = this;
        vm.determineIsNo();
        let data;
        let url;
        if(vm.status=='2'){//单个新增商品 
            url = vm.$addNewGoodsDataURL
            data = {"mis_order_sn":vm.$route.query.mis_order_sn,"ng_id":vm.ng_id}

        }else if(vm.status=='1'){//批量新增商品
            url = vm.$batchAddNewGoodsURL;
            data = {"mis_order_sn":vm.$route.query.mis_order_sn,"str_ng_id":vm.NG_id}
        }else if(vm.status=='3'){//补充新品到对应总单中
            url = vm.$replenishGoodsIntoOrderURL,
            data = {"mis_order_sn":vm.$route.query.mis_order_sn}
        }
        axios.post(vm.url+url,data,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            vm.getDataList()
            if(vm.status=='2'){

            }else if(vm.status=='1'){
                $('.allChecked').prop("checked",false)
                for(var i=0;i<vm.tableData.length;i++){
                    $(".cb_one input[name="+i+"]").prop("checked",false);
                }
            }
        }).catch(function (error) {
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
    //打开根据已选sku批量新增商品
    openDiscountBox(e){
        let vm = this;
        // vm.listSaleAccount();
        vm.status=e;
        let allChecked = $('.allChecked').prop("checked");
        let ng_id = '';
        if(allChecked==true){
            vm.tableData.forEach(specInfo=>{
                ng_id+=specInfo.id+',';
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.tableData.length;i++){
                let isTrue = $(".cb_one input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    ng_id+=vm.tableData[i].id+',';
                }
            }
        }
        if(ng_id==''){
            vm.$message('请选择sku之后新增商品!');
            return false;
        }else{
            vm.NG_id=ng_id;
            vm.contentStr='请您确认是否要批量新增商品？',
            $(".confirmPopup_b").fadeIn();
        }
    },
    //打开新增规格弹框
    openAddspecBox(id,sn){
        let vm = this;
        vm.ng_id=id;
        axios.post(vm.url+vm.$addSpecPageURL,
            {
                "ng_id":id,
                "mis_order_sn":sn,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.AddspecDialogVisible = true;
            vm.brandList=res.data.brandInfo;
            vm.editNewGoodsInfo=res.data.newGoodsInfo;
            vm.goodsName=res.data.newGoodsInfo.goods_name;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
            }
        });
    },
    //根据输入的商品名动态加载商品名称 
    querySearchAsync(queryString, callback) {
        let vm = this;
        var list = [{}];
        //调用的后台接口
        //从后台获取到对象数组
        axios.post(vm.url+vm.$searchGoodsNameURL,
            {
                "goods_name":queryString,
            },
            {
                headers:vm.headersStr,
            }
        ).then((res)=>{
            //在这里为这个数组中每一个对象加一个value字段, 因为autocomplete只识别value字段并在下拉列中显示
            let list = [];
            if(res.data.goodsInfo.length!=0){
                res.data.goodsInfo.forEach(element=>{
                    list.push({ "value":element.goods_name,"goods_sn":element.goods_sn,"brand_name":element.brand_name })
                }) 
                vm.is_notSn=false;
            }
            if(res.data.goodsInfo.length==0){
                vm.is_notSn=true;
                vm.editNewGoodsInfo.brand_name='';
            }
            callback(list);
            vm.editNewGoodsInfo.brand_name=res.data.goodsInfo[0].brand_name;
            
        }).catch((error)=>{
        });
    },
    //确定新增商品规格码
    addSpecData(errors){
        let vm = this;
        if(errors.items.length!=0){
            return false;
        }
        if(vm.is_notSn==true){
            return false;
        }
        if(vm.goods_sn==''){
            vm.$message('商品名称未找到!');
            return false;
        }
        vm.AddspecDialogVisible=false;
        axios.post(vm.url+vm.$addSpecDataURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
                "ng_id":vm.ng_id,
                "goods_sn":vm.goods_sn,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=='2024'){
                vm.getDetailsData();
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
            }
        });
    },
    handleSelect(item) {
        let vm = this;
        vm.editNewGoodsInfo.brand_name=item.brand_name;
        vm.goods_sn=item.goods_sn;
    },
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    //导出总单新品
    d_table(){
        let vm=this;
        $(event.target).addClass('grBgButton').removeClass('bgButton');
        let loa=Loading.service({fullscreen: true, text: '拼命下载中....'});
        let tableName = "总单新品_"+vm.$route.query.mis_order_sn
        axios.post(vm.url+vm.$exportOrdNewURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
                loa.close();
            if(res.data.code){
                vm.$message(res.data.msg)
            }else{
                exportsa(vm.url+vm.$exportOrdNewURL+"?mis_order_sn="+vm.$route.query.mis_order_sn,tableName)
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    confirmShow(){
        $(".upDataButtonByBox_b").fadeIn();
    },
    //确认上传
    GFFconfirmUpData(formDate){
        let vm=this;
        $(".upDataButtonByBox_b").fadeOut();
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$importOrdNewURL+"?mis_order_sn="+vm.$route.query.mis_order_sn,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code==2024){
                vm.getDetailsData();
                $("#file").val('');
                vm.fileName='';
                vm.$message(res.msg);
            }else{
                vm.$message(res.msg);
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
    tableWidth(){
        let vm=this;
        let judgeRate=this.$store.state.select.find(function(e){
                return e=='商品名称';
        });
        let pinpaimingceng=this.$store.state.select.find(function(e){
                return e=='品牌名称';
        });
        let caozuo=this.$store.state.select.find(function(e){
                return e=='操作';
        });
        let meijyuanjia=this.$store.state.select.find(function(e){
                return e=='美金原价';
        });
        let yuguzhongliang=this.$store.state.select.find(function(e){
                return e=='商品重量';
        });
        let EXWzhekou=this.$store.state.select.find(function(e){
                return e=='EXW折扣';
        });
        let xinpinzhuangtai=this.$store.state.select.find(function(e){
                return e=='新品状态';
        });
        // let guigema=this.$store.state.select.find(function(e){
        //         return e=='商品规格码';
        // });
        var widthLength=(this.$store.state.select.length-7)*160;
        if(judgeRate){
            widthLength=widthLength+300;
        }
        if(caozuo){
            widthLength=widthLength+220;
        }
        if(pinpaimingceng){
            widthLength=widthLength+200;
        }
        if(meijyuanjia){
            widthLength=widthLength+100;
        }
        if(yuguzhongliang){
            widthLength=widthLength+90;
        }
        if(EXWzhekou){
            widthLength=widthLength+90;
        }
        if(xinpinzhuangtai){
            widthLength=widthLength+90;
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
.getOrderNewGoods .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.getOrderNewGoods .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.getOrderNewGoods .ee{width:100%!important; width:100%; text-align: center;}
.getOrderNewGoods .t_i_h table{width:100%;}
.getOrderNewGoods .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.getOrderNewGoods .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.getOrderNewGoods .cc table{width:100%; }
.getOrderNewGoods .cc table td{ text-align:center}
</style>
<style scoped lang=less>
table{
    border: 1px solid #ebeef5;
    tr{
        line-height: 40px;
        th,td{
            border-bottom: 1px solid #ebeef5;
            border-left: 1px solid #ebeef5;
        }
    }
    tr:nth-child(even){
        background:#fafafa;
    }
    tr:hover{
        background:#ced7e6;
    }
}
</style>
