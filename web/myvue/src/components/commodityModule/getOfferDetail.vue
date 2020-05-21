<template>
  <div class="getOfferDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <el-row>
                        <!-- <el-col :span="17">
                            <div class="lineHeighteighty overoneLinesHeid">
                                <span class="MR_twenty">总单号：{{orderInfo.mis_order_sn}}</span>
                                <span class="MR_twenty">销售客户：{{orderInfo.user_name}}</span>
                                <span class="MR_twenty">是否报价：{{orderInfo.offer_desc}}</span>
                                <span class="MR_twenty">订单状态：{{orderInfo.status_desc}}</span>
                                <span class="MR_twenty">报价部门：{{orderInfo.de_name}} </span>
                                <span class="MR_twenty">商品总数：{{goodsNum}} </span>
                            </div>
                        </el-col> -->
                        <el-col class="L_H_F MT_Thirty">
                            <span class="bgButton" @click="backUpPage()">返回上一页</span>
                            <span class="bgButton" @click="dialogVisibleTitle = true">选择列</span>
                            <!-- <span class="bgButton" v-if="orderInfo.offer_desc=='未报价'" @click="openDiscountBox();discountBySelect='';">根据已选sku修改销售折扣</span>
                                <el-dialog title="销售折扣" class="fontCenter" :visible.sync="selectDialogVisibleRate" width="600px">
                                    <el-row class="lineHeightForty">
                                        <el-col :span="6" :offset="4">
                                            <div>输入销售折扣：</div>
                                        </el-col>
                                        <el-col :span="8">
                                            <el-input v-model="discountBySelect" placeholder="请输入销售折扣"></el-input>
                                        </el-col>
                                    </el-row>
                                    <div slot="footer" class="dialog-footer fontCenter">
                                        <span class="grayBgButton" @click="selectDialogVisibleRate = false">取 消</span>
                                        <span class="redBgButton removeattr" @click="modifyDiscountBySelect()">确 定</span>
                                    </div>
                                </el-dialog>
                            <span class="bgButton" v-if="orderInfo.offer_desc=='未报价'" @click="dialogVisibleRate = true;pricingDiscounts=''">根据自采毛利率批量修改销售折扣</span>
                                <el-dialog title="根据自采毛利率批量修改销售折扣" class="fontCenter" :visible.sync="dialogVisibleRate" width="600px">
                                    <el-row class="lineHeightForty">
                                        <el-col :span="6" :offset="4">
                                            <div>选择自采毛利率：</div>
                                        </el-col>
                                        <el-col :span="8">
                                            <el-select v-model="pricingDiscounts" placeholder="请选择自采毛利率">
                                                <el-option v-for="item in grossInterestRate" :key="item" :label="item" :value="item"></el-option>
                                            </el-select>
                                        </el-col>
                                    </el-row>
                                    <div slot="footer" class="dialog-footer fontCenter">
                                        <span class="grayBgButton" @click="dialogVisibleRate = false">取 消</span>
                                        <span class="redBgButton removeattr" @click="dialogVisibleRate = false;batchModPricRate()">确 定</span>
                                    </div>
                                </el-dialog>
                            <span v-if="orderInfo.offer_desc=='未报价'" class="bgButton" @click="openExportOffer()">导出商品信息</span>
                                <el-dialog title="导出商品信息" :visible.sync="exportDialogVisible" width="800px" center>
                                    <span><i class="redFont">*</i>自采毛利率档位:</span>
                                    <span>
                                        <template>
                                            <el-select v-model="arrPickRateInfo" v-validate="'required'" name="arrPickRate" clearable placeholder="请选择自采毛利率档位">
                                                <el-option v-for="item in arrPickRateList" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                            </el-select>
                                            <span v-show="errors.has('arrPickRate')" class="text-style redFont" v-cloak> {{ errors.first('arrPickRate') }} </span>
                                        </template>
                                    </span>
                                    <span slot="footer" class="dialog-footer">
                                        <el-button type="info" @click="exportDialogVisible = false">取 消</el-button>
                                        <el-button type="primary" @click="exportDialogVisible = false;exportOffer()">导 出</el-button>
                                    </span>
                                </el-dialog>
                            <span v-if="orderInfo.offer_desc=='未报价'" class="bgButton" @click="openImportOffer()">导入报价销售折扣</span>
                            <el-input class="widthOneFiveHundred ML_twenty" v-model="brandName" placeholder="请输入品牌搜索"></el-input>
                            <span> 
                                <span v-if="$store.state.clickStutes==true" class="bgButton" @click="getlistData()">搜索</span>
                                <span v-if="$store.state.clickStutes==false" class="grBgButton">搜索</span>
                            </span> -->
                            <span class="bgButton" @click="openImportOffer()">批量导入报价</span>
                            <!-- <span class="bgButton" @click="exportOffer()">导出数据</span> -->
                        </el-col>
                    </el-row>
                    <el-row class="MB_ten">
                        <span class="d_I_B MR_twenty">报价单号：{{orderInfo.offer_sn}}</span>
                        <span class="d_I_B MR_twenty">创建时间：{{orderInfo.create_time}}</span>
                        <span class="d_I_B MR_twenty">报价状态：{{orderInfo.status_desc}}</span>
                    </el-row>
                    <el-row>
                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="1" :style="tableWidth">
                                        <thead>
                                            <tr>
                                                <th class="w_Fty" style="width:60px" rowspan="2"><input class="allChecked" type="checkbox" @change="allSelect()"/>全选</th>
                                                <th class="widthTwoHundred" v-if="selectTitle('品牌')" rowspan="2">
                                                    <span>品牌</span>
                                                    <span class="d_I_B Cursor" style="vertical-align: -9px;">
                                                        <i class="el-icon-caret-top" title="升序排列" @click="sortByS_N('1','brand_name','string')"></i><br/>
                                                        <i class="el-icon-caret-bottom" title="降序排列" @click="sortByS_N('2','brand_name','string')"></i> 
                                                    </span>
                                                </th>
                                                <th style="width:300px;" v-if="selectTitle('品名')" rowspan="2">
                                                    <span>商品名称</span>
                                                    <span class="d_I_B Cursor" style="vertical-align: -9px;">
                                                        <i class="el-icon-caret-top" title="升序排列" @click="sortByS_N('1','goods_name','string')"></i><br/>
                                                        <i class="el-icon-caret-bottom" title="降序排列" @click="sortByS_N('2','goods_name','string')"></i>
                                                    </span>
                                                </th>
                                                <th class="widthTwoHundred" v-if="selectTitle('商品规格码')" rowspan="2">商品规格码</th>
                                                <th class="widthTwoHundred" v-if="selectTitle('商家编码')" rowspan="2">商家编码</th>
                                                <th class="widthTwoHundred" v-if="selectTitle('商品参考码')" rowspan="2">商品参考码</th>
                                                <th class="widthTwoHundred" v-if="selectTitle('商品代码')" rowspan="2">商品代码</th>
                                                <th class="widthTwoHundred" v-if="selectTitle('平台条码')" rowspan="2">平台条码</th>
                                                <th class="widthOneHundred" v-if="selectTitle('交付日期')" rowspan="2">
                                                    交付日期
                                                    <span class="d_I_B Cursor" style="vertical-align: -9px;">
                                                        <i class="el-icon-caret-top" title="升序排列" @click="sortByDate('1','entrust_time')"></i><br/>
                                                        <i class="el-icon-caret-bottom" title="降序排列" @click="sortByDate('2','entrust_time')"></i>
                                                    </span>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('库存')" rowspan="2">库存</th>
                                                <th class="widthOneHundred" v-if="selectTitle('DFS金卡折扣')" rowspan="2">DFS金卡折扣</th>
                                                <th class="widthOneHundred" v-if="selectTitle('DFS金卡价($)')" rowspan="2">DFS金卡价($)</th>
                                                <th class="widthOneHundred" v-if="selectTitle('DFS黑卡折扣')" rowspan="2">DFS黑卡折扣</th>
                                                <th class="widthOneHundred" v-if="selectTitle('DFS黑卡价($)')" rowspan="2">DFS黑卡价($)</th>
                                                <th class="widthOneHundred" v-if="selectTitle('美金原价($)')" rowspan="2">
                                                    美金原价($)
                                                    <span class="d_I_B Cursor" style="vertical-align: -9px;">
                                                        <i class="el-icon-caret-top" title="升序排列" @click="sortByS_N('1','spec_price','number')"></i><br/>
                                                        <i class="el-icon-caret-bottom" title="降序排列" @click="sortByS_N('2','spec_price','number')"></i>
                                                    </span>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('重量')" rowspan="2">重量</th>
                                                <th class="widthOneHundred" v-if="selectTitle('预估重量')" rowspan="2">预估重量</th>
                                                <th class="widthOneHundred" v-if="selectTitle('EXW折扣')" rowspan="2">EXW折扣</th>
                                                <th class="widthOneHundred" v-if="selectTitle('外采折扣')" rowspan="2">外采折扣</th>
                                                <th class="widthOneHundred" v-if="selectTitle('ERP成本价')" rowspan="2">
                                                    ERP成本价￥
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="美金原价*重价比折扣*汇率">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('ERP成本折扣')" rowspan="2">
                                                    ERP成本折扣
                                                    <template>
                                                        <el-popover  placement="top-start" width="50" trigger="click"
                                                            content="等于重价比折扣">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>

                                                <!-- <th class="widthOneHundred" v-if="selectTitle('仓库名称')" rowspan="2">仓库名称</th> -->
                                                <th class="widthOneHundred" v-if="selectTitle('重价比')" rowspan="2">
                                                    重价比
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="重量/美金原价/重价系数/100">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('重价比折扣')" rowspan="2">
                                                    重价比折扣
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="EXW折扣+重价比">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th :colspan="rateTdWidth" v-if="selectTitle('自采毛利')">
                                                    自采毛利率<br>
                                                    <i v-if="orderInfo.is_offer==1" class="el-icon-circle-plus grayFont" title="该批次已报价完毕不能二次报价"></i>
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="重价比折扣/（1-对应档位利率）">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                    <i v-if="orderInfo.is_offer!=1" class="el-icon-circle-plus" @click="dialogVisible = true"></i>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('销售折扣')" rowspan="2">
                                                    销售折扣
                                                    <!-- <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="(默认取自采毛利率的8%档)，可以手动填写">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template> -->
                                                    <span class="d_I_B Cursor" style="vertical-align: -9px;">
                                                        <i class="el-icon-caret-top" title="升序排列" @click="sortByS_N('1','sale_discount','number')"></i><br/>
                                                        <i class="el-icon-caret-bottom" title="降序排列" @click="sortByS_N('2','sale_discount','number')"></i>
                                                    </span>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('销售价(￥)')" rowspan="2">
                                                    销售价(￥)
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="美金原价*销售折扣*汇率">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('销售毛利率')" rowspan="2">
                                                    销售毛利率
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="1-重价比折扣/销售折扣">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('BD折扣')" rowspan="2">
                                                    BD折扣
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="近15天内当前销售客户的最近历史折扣">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('DD折扣')" rowspan="2">
                                                    DD折扣
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="近15天内当前销售客户的最近历史折扣">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th :colspan="totalCost" v-if="selectTitle('费用')">
                                                    费用
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="销售折扣*各项费用比率">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('销售毛利')" rowspan="2">
                                                    销售毛利
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="销售毛利率-费用合计">
                                                            <i class="el-icon-question" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                    <span class="d_I_B Cursor" style="vertical-align: -9px;">
                                                        <i class="el-icon-caret-top" title="升序排列" @click="sortByS_N('1','runMarRate','number')"></i><br/>
                                                        <i class="el-icon-caret-bottom" title="降序排列" @click="sortByS_N('2','runMarRate','number')"></i>
                                                    </span>
                                                </th>
                                                <th class="widthOneHundred" v-if="selectTitle('乐天折扣')" rowspan="2">乐天折扣</th>
                                                <th class="widthOneHundred" v-if="selectTitle('爱宝客折扣')" rowspan="2">爱宝客折扣</th>
                                                <th class="widthOneHundred" v-if="selectTitle('新罗折扣')" rowspan="2">新罗折扣</th>
                                            </tr>
                                            <tr>
                                                <th class="widthOneHundred" v-if="selectTitle('自采毛利')" v-for="item in grossInterestRate">{{item}}</th>
                                                <th class="widthOneHundred" v-if="selectTitle('费用')" v-for="item in costItem"><span v-for="(key,value) in item">{{value}}</span></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()">
                                <table id="tableCc" cellpadding="0" cellspacing="0" border="1" :style="tableWidth">
                                    <tr v-for="(item,index) in tableData" :style="fontColor(item.runMarRate)" class="tableTr">
                                        <td class="w_Fty cb_one" style="width:60px">
                                            <input @click="oneSelect(index)" type="checkbox" :name="index"/>
                                        </td>
                                        <td  v-if="selectTitle('品牌')" :class="['ellipsis','widthTwoHundred','fontLift']">
                                            <el-tooltip class="item" effect="light" :content="item.brand_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{item.brand_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td v-if="selectTitle('品名')" style="width:300px;" :class="['ellipsis','fontLift']">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;width:300px;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td v-if="selectTitle('商品规格码')" class="widthTwoHundred">{{item.spec_sn}}</td>
                                        <td v-if="selectTitle('商家编码')" class="widthTwoHundred">{{item.erp_merchant_no}}</td>
                                        <td v-if="selectTitle('商品参考码')" class="widthTwoHundred">{{item.erp_ref_no}}</td>
                                        <td v-if="selectTitle('商品代码')" class="widthTwoHundred">{{item.erp_prd_no}}</td>
                                        <td v-if="selectTitle('平台条码')"  :class="['ellipsis','widthTwoHundred']"> 
                                            <el-tooltip class="item" effect="light" :content="item.platform_barcode" placement="right">
                                            <span style="-webkit-box-orient: vertical;width:200px">&nbsp;&nbsp;{{item.platform_barcode}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td v-if="selectTitle('交付日期')" class="widthOneHundred">{{item.entrust_time}}</td>
                                        <td v-if="selectTitle('库存')" class="widthOneHundred">{{item.gStockNum}}</td>
                                        <td v-if="selectTitle('DFS金卡折扣')" class="widthOneHundred">{{item.gold_discount}}</td>
                                        <td v-if="selectTitle('DFS金卡价($)')" class="widthOneHundred">{{item.gold_price}}</td>
                                        <td v-if="selectTitle('DFS黑卡折扣')" class="widthOneHundred">{{item.black_discount}}</td>
                                        <td v-if="selectTitle('DFS黑卡价($)')" class="widthOneHundred">{{item.black_price}}</td>
                                        <td v-if="selectTitle('美金原价($)')" class="widthOneHundred">{{item.spec_price}}</td>
                                        <td v-if="selectTitle('重量')" class="widthOneHundred">{{item.spec_weight}}</td>
                                        <td v-if="selectTitle('预估重量')" class="widthOneHundred">{{item.estimate_weight}}</td>
                                        <td v-if="selectTitle('EXW折扣')" class="widthOneHundred">{{item.exw_discount}}</td>

                                        <td v-if="selectTitle('外采折扣')" class="widthOneHundred">{{item.foreign_discount}}</td>
                                        <td v-if="selectTitle('ERP成本价')" class="widthOneHundred">{{item.erp_cost_price}}</td>
                                        <td v-if="selectTitle('ERP成本折扣')" class="widthOneHundred">{{item.hpr_discount}}</td>
                                        <!-- <td v-if="selectTitle('仓库名称')" class="widthOneHundred">{{item.store_name}}</td> -->
                                        <td v-if="selectTitle('重价比')" class="widthOneHundred">{{item.high_price_ratio}}</td>
                                        <td v-if="selectTitle('重价比折扣')" class="widthOneHundred">{{item.hpr_discount}}</td>
                                        <td v-if="selectTitle('自采毛利')&&item.arrMarginRate" class="widthOneHundred WholeChargeRate" v-for="itemO in item.arrMarginRate">
                                            <span  v-for="itemT in grossInterestRate">{{itemO[itemT]}}</span>
                                        </td>
                                        <td v-if="selectTitle('自采毛利')&&!item.arrMarginRate" class="widthOneHundred WholeChargeRate" v-for="itemO in rateTdWidth">
                                        </td>
                                        <td v-if="selectTitle('销售折扣')" class="widthOneHundred WholeInput">
                                            <input v-if="orderInfo.is_offer!=1&&item.is_modify==1" class="B_J_G" type="text" :name="index"  @change="reviseDiscounts(item.spec_sn,index,item.sale_discount)" :value="item.sale_discount"/>
                                            <input v-if="orderInfo.is_offer!=1&&item.is_modify==0" type="text" :name="index"  @change="reviseDiscounts(item.spec_sn,index,item.sale_discount)" :value="item.sale_discount"/>
                                            <span v-if="orderInfo.is_offer==1">{{item.sale_discount}}</span>
                                            <span v-else></span>
                                        </td>
                                        <td v-if="selectTitle('销售价(￥)')" class="widthOneHundred">{{item.salePrice}}</td>
                                        <td v-if="selectTitle('销售毛利率')" class="widthOneHundred">{{item.saleMarRate}}</td>
                                        <td v-if="selectTitle('BD折扣')" class="widthOneHundred">{{item.bd_sale_discount}}</td>
                                        <td v-if="selectTitle('DD折扣')" class="widthOneHundred">{{item.dd_sale_discount}}</td>
                                        <td v-if="selectTitle('费用')&&item.arrChargeRate" class="widthOneHundred" v-for="itemO in item.arrChargeRate">
                                            <span v-for="(key,value) in itemO">{{key}}</span>
                                        </td>
                                        <td v-if="selectTitle('费用')&&!item.arrChargeRate" class="widthOneHundred" v-for="itemO in totalCost">
                                        </td>
                                        <td v-if="selectTitle('销售毛利')" class="widthOneHundred">
                                            <span v-if="item.runMarRate==undefined"></span>
                                            <span v-else>{{item.runMarRate}}%</span>
                                        </td>
                                        <td class="widthOneHundred" v-if="selectTitle('乐天折扣')">{{item.lt_discount}}</td>
                                        <td class="widthOneHundred" v-if="selectTitle('爱宝客折扣')">{{item.abk_discount}}</td>
                                        <td class="widthOneHundred" v-if="selectTitle('新罗折扣')">{{item.xl_discount}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="MB_twenty">
                            注：<span style="color:red">红色代表销售毛利小于8%</span>
                                <span style="color: rgb(0, 172, 77)">绿色代表销售毛利大于9%</span>
                                <span style="color:#2c3e50">黑色代表销售毛利大于8%小于9%</span>
                        </div>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
        <el-dialog title="选择要展示的信息" :visible.sync="dialogVisibleTitle" width="800px">
            <div class="selectTitle">
                <template>
                    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                    <span class="redFont" style="margin-left: -58px;">*选择列时至少要保留一个展示信息</span>
                    <div style="margin: 15px 0;"></div>
                    <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
                        <el-checkbox v-for="city in cityOptions" :label="city" :key="city">{{city}}</el-checkbox>
                    </el-checkbox-group>
                </template>
            </div>
        </el-dialog>
      <div class="beijing">
          <!-- <span @click="d_table()" class="dTable blueFont Cursor">客户订单_报价导入</span> -->
          <span class="dTableOne bgButton Cursor" @click="exportOffer()">导出数据</span>
          <span class="dTableTwo redFont">请使用导出的数据直接上传</span>
      </div>
      <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
      <successfulOperation :msgStr="msgStr"></successfulOperation>
      <operationFailed :msgStr="msgStr"></operationFailed>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import Swiper from 'swiper'; 
import 'swiper/dist/css/swiper.min.css';
import { arrSortMaxToMin } from '@/filters/publicMethods.js'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';
import { exportsa } from '@/filters/publicMethods.js'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
export default {
  components:{
      fontWatermark,
      upDataButtonByBox,
      operationFailed,
      successfulOperation,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      tableData:[],
      grossInterestRate:[],//毛利率 
      costItem:[],//费用项
      department:'',
      tableTitle:[],
      rateTdWidth:'',
      dialogVisible: false,
      dialogVisibleRate:false,
      interestRate:'',//自定义毛利率
      pricingDiscounts:'',//批量修改销售折扣 
      storeId:'',//仓位id
      brandTableData:[],
      erpInfo:[],
      totalCost:'',
      goodsNum:'',
      brandName:'',
      goodsName:'',
      index:'',
      departments:[],
      orderInfo:'',
      //选择表头所需数据
      dialogVisibleTitle:false,
      checkAll: false,
      checkedCities: ['品牌','品名','平台条码','预判采购量','美金原价($)','重量','EXW折扣','重价比折扣','商品规格码','销售折扣','销售毛利','乐天折扣','新罗折扣','爱宝客折扣'],
      isIndeterminate: true,
      cityOptions:['品牌','品名','商品规格码','商家编码','商品参考码','商品代码','平台条码','交付日期','库存','DFS金卡折扣','DFS金卡价($)','DFS黑卡折扣','DFS黑卡价($)'
      ,'美金原价($)','重量','预估重量','EXW折扣','外采折扣','ERP成本价','ERP成本折扣','重价比','重价比折扣','自采毛利','销售折扣','销售价(￥)','销售毛利率'
      ,'BD折扣','DD折扣','费用','销售毛利','乐天折扣','新罗折扣','爱宝客折扣'],
      noClicks:0,//记录禁止点击事件 
      discountBySelect:'',
      selectDialogVisibleRate:false,
      arrSpecSn:'',
      //导出商品信息 
      exportDialogVisible:false,
      arrPickRateList:[],
      arrPickRateInfo:'',
      //导入报价销售折扣
      titleStr:'批量导入报价',

      msgStr:'',
    }
  },
  mounted(){
    this.getlistData();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.crossHighlight()
  },
  methods:{
    //选择表头数据  
    handleCheckAllChange(val) {
        this.checkedCities = val ? this.cityOptions : [];
        if(this.checkedCities.length==0){
            // this.$message('至少要选择一个字段!');
            this.checkedCities=['品牌','品名','平台条码','预判采购量','美金原价($)','重量','EXW折扣','重价比折扣','商品规格码','销售折扣','销售毛利','乐天折扣','新罗折扣','爱宝客折扣'];
            return false;
        }
        this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        if(checkedCount==0){
            this.checkedCities=['品牌','品名','平台条码','预判采购量','美金原价($)','重量','EXW折扣','重价比折扣','商品规格码','销售折扣','销售毛利','乐天折扣','新罗折扣','爱宝客折扣'];
        }
        this.checkAll = checkedCount === this.cityOptions.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cityOptions.length;
    },
    getlistData(){
        let vm=this;
        vm.$store.commit('editClickStutes');
        let headersToken=sessionStorage.getItem("token");
        vm.erpInfo.splice(0);
        vm.departments.splice(0);
        vm.arrPickRateList.splice(0);
        axios.post(vm.url+vm.$getOfferDetailURL,
                {
                    "offer_sn":vm.$route.query.offer_sn,
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
        ).then(function(res){
                vm.$store.commit('editClickStutes');
                vm.loading.close();
                vm.tableData=res.data.offerGoods;
                vm.goodsNum=res.data.goodsNum;
                vm.orderInfo=res.data.orderInfo[0];
                vm.grossInterestRate=res.data.arrPickRate;
                vm.costItem=res.data.arrCharge;
                res.data.erpInfo.forEach(element=>{
                    vm.erpInfo.push({"store_name":element.store_name,"store_id":element.store_id});
                })
                vm.rateTdWidth=res.data.arrPickRate.length;
                for(let key in res.data.arrPickRate){
                    vm.arrPickRateList.push({"label":res.data.arrPickRate[key],"id":res.data.arrPickRate[key]})
                }
                vm.totalCost=res.data.arrCharge.length;
                if(parseInt(vm.tableData.length)>25){
                    $(".cc").css({
                        "height":"610px",
                        "width":"100.6%",
                        })
                }else{
                    $(".cc").css({
                        "height":"auto",
                        "width":"100%",
                        })
                }
        }).catch(function (error) {
                vm.$store.commit('editClickStutes');
                vm.loading.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
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
                vm.getlistData();
                vm.dialogVisible = false;
            }else{
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
    //单个修改销售折扣 
    reviseDiscounts(spec_sn,index,sd){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        let Inputval;
        Inputval = $(".WholeInput input[name="+index+"]").val();
        if(parseFloat(Inputval)>=1||parseFloat(Inputval)<=0){
            vm.$message('销售折扣应该为0-1之间的数!');
            $(".WholeInput input[name="+index+"]").val(sd);
            return false;
        }
        axios.post(vm.url+vm.$modifySkuDiscountURL,
            {
                "spec_sn":spec_sn,
                "sale_discount":Inputval,
                "offer_sn":vm.$route.query.offer_sn,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code=='1000'){
                // vm.tableData.splice(index,1,res.data.offerGoods[0]); 
                vm.getlistData();
            }else if(res.data.code=='2023'){
                vm.$message(res.data.msg);
            }else{
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
    //选择仓位 
    // isStoreHome(){
    //     let vm=this;
    //     vm.getlistData();
    // },
    //批量修改销售折扣
    batchModPricRate(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        $(event.target).addClass("backBlack");
        let eventTarget=$(event.target);
        if(vm.noClicks==1){
            return false;
        }
        if(vm.pricingDiscounts==''){
            vm.$message('请选择销售折扣');
            return false;
        }
        vm.noClicks=1;
        axios.post(vm.url+vm.$batchModSaleDiscountURL,
                {
                    "mis_order_sn":vm.$route.query.mis_order_sn,
                    "pick_margin_rate":vm.pricingDiscounts,
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
        ).then(function(res){
                vm.noClicks=0;
                if(res.data.code==2024){
                    vm.$message(res.data.msg);
                    vm.getlistData();
                }else{
                    vm.$message(res.data.msg);
                }
                eventTarget.removeClass("backBlack");
        }).catch(function (error) {
            vm.noClicks=0;
            eventTarget.removeClass("backBlack");
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //打开确认报价弹框 
    openQuotation(){
        $(".confirmPopup_b").fadeIn();
    },
    //确认报价 
    confirmationAudit(){
        let vm = this;
        $(".confirmPopup_b").fadeOut();
        let headersToken=sessionStorage.getItem("token");
        axios.post(vm.url+vm.$finishOfferURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code==2024){
                vm.getlistData();
                vm.$message(res.data.msg);
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
                $(".confirmPopup_b").fadeOut();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //关掉确认拆分弹出框
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    //该方法为表格十字高亮选中列 
    crossHighlight(){
        $(".cc").bind("mouseover",function(){
        $(".cc table .tableTr td").mouseover(function() {
            var thistd=$(this);
            var tdindex=thistd.index();
            thistd.parent().siblings("tr").each(function(){
                $(this).children("td").eq(tdindex).css("background",'#bcd4ec');
            });
            $(this).css("background","white").siblings("td").css("background","#bcd4ec");
            $(this).clean();
        });

        $(".cc table .tableTr td").mouseout(function() {
            var thistd=$(this);
            var tdindex=thistd.index();
            thistd.parent().siblings("tr").each(function(){
                $(this).children("td").eq(tdindex).css("background","#fafafa");
            });
            $(this).css("background","#fafafa").siblings("td").css("background","#fafafa");
            $(this).clean();
        });
        })
    },
    //String和number类型的排序 
    sortByS_N(status,type,SorN){
        let vm = this;
        let tableData = vm.tableData;
        $('.el-icon-caret-top').removeClass('redFont');
        $('.el-icon-caret-bottom').removeClass('redFont');
        $(event.target).addClass('redFont')
        $(event.target).siblings().removeClass('redFont')
        function swap(tableData, i, j){
            const temp = tableData[i];
            tableData[i] = tableData[j];
            tableData[j] = temp;
        }
        if(status=='2'){//降序
            for(let i = 0; i < tableData.length - 1; i++){
                let flag = false;
                for(let j = 0; j < tableData.length - 1 - i; j++){
                    if(SorN == 'string'&&tableData[j+1][type]!=null){
                        let param1 = tableData[j][type];
                        let param2 = tableData[j+1][type];
                        if(param2.localeCompare(param1)>-1){
                            swap(tableData, j, j+1);
                            flag = true;
                        }
                    }else if(SorN == 'number'&&tableData[j+1][type]!=null){
                        if(parseFloat(tableData[j][type]) > parseFloat(tableData[j+1][type])){
                            swap(tableData, j, j+1);
                            flag = true;
                        }
                    }
                    
                }
                if(!flag){
                    break;
                }
            }
            vm.tableData=[];
            vm.tableData = tableData;
        }else if(status=='1'){//升序
            for(let i = 0; i < tableData.length - 1; i++){
                let flag = false;
                for(let j = 0; j < tableData.length - 1 - i; j++){
                    if(SorN == 'string'&&tableData[j][type]!=null){
                        let param1 = tableData[j][type];
                        let param2 = tableData[j+1][type];
                        if(param1.localeCompare(param2)>-1){
                            swap(tableData, j, j+1);
                            flag = true;
                        }
                    }else if(SorN == 'number'&&tableData[j][type]!=null){
                        if(parseFloat(tableData[j][type]) < parseFloat(tableData[j+1][type])){
                            swap(tableData, j, j+1);
                            flag = true;
                        }
                    }
                }
                if(!flag){
                    break;
                }
            }
            vm.tableData=[];
            vm.tableData = tableData;
        }
    },
    //时间类型的排序
    sortByDate(status,type){
        let vm = this;
        $('.el-icon-caret-top').removeClass('redFont');
        $('.el-icon-caret-bottom').removeClass('redFont');
        $(event.target).addClass('redFont')
        $(event.target).siblings().removeClass('redFont')
        let tableData = vm.tableData;
        function swap(tableData, i, j){
            const temp = tableData[i];
            tableData[i] = tableData[j];
            tableData[j] = temp;
        }
        if(status=='2'){//降序
            for(let i = 0; i < tableData.length - 1; i++){
                let flag = false;
                for(let j = 0; j < tableData.length - 1 - i; j++){
                    var timestamp1 = new Date(tableData[j][type]).getTime();
                    var timestamp2 = new Date(tableData[j+1][type]).getTime();
                    if(timestamp1 > timestamp2){
                        swap(tableData, j, j+1);
                        flag = true;
                    }
                }
                if(!flag){
                    break;
                }
            }
            vm.tableData=[];
            vm.tableData = tableData;
        }else if(status=='1'){//升序
            for(let i = 0; i < tableData.length - 1; i++){
                let flag = false;
                for(let j = 0; j < tableData.length - 1 - i; j++){
                    var timestamp1 = new Date(tableData[j][type]).getTime();
                    var timestamp2 = new Date(tableData[j+1][type]).getTime();
                    if(timestamp1 < timestamp2){
                        swap(tableData, j, j+1);
                        flag = true;
                    }
                }
                if(!flag){
                    break;
                }
            }
            vm.tableData=[];
            vm.tableData = tableData;
        }
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
    //打开根据已选sku批量修改销售折扣
    openDiscountBox(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        let arrSpecSn = '';
        if(allChecked==true){
            vm.tableData.forEach(specInfo=>{
                arrSpecSn+=specInfo.spec_sn+',';
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.tableData.length;i++){
                let isTrue = $(".cb_one input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    arrSpecSn+=vm.tableData[i].spec_sn+',';
                }
            }
        }
        vm.arrSpecSn=arrSpecSn;
        if(arrSpecSn==''){
            vm.$message('请选择sku之后修改折扣!');
            return false;
        }else{
            vm.selectDialogVisibleRate=true;
        }
    },
    //根据已选sku修改销售折扣 
    modifyDiscountBySelect(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        if(vm.discountBySelect==''){
            vm.$message('请填写销售折扣!');
            return false;
        }
        if(vm.discountBySelect<=0||vm.discountBySelect>=1){
            vm.$message('输入有误，请重新输入！');
            return false;
        }
        vm.selectDialogVisibleRate = false;
        axios.post(vm.url+vm.$modSaleDiscountBySpecURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
                "strSpecSn":vm.arrSpecSn,
                "sale_discount":vm.discountBySelect,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){

            if(res.data.code=='1000'){
                vm.getlistData();
                $('.allChecked').prop("checked",false)
                for(var i=0;i<vm.tableData.length;i++){
                    let isTrue = $(".cb_one input[name="+i+"]").prop("checked",false);
                }
                vm.$message(res.data.msg);
            }else{
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
    openExportOffer(){
        let vm = this;
        vm.exportDialogVisible = true;
    },
    //导出商品信息 
    exportOffer(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$exportSkuOfferURL+'?offer_sn='+vm.$route.query.offer_sn+'&token='+headersToken);
    },
    openImportOffer(){
        let vm = this;
        $(".upDataButtonByBox_b").fadeIn();
        $(".dTable").fadeIn();
        $(".beijing").fadeIn();
    },
    //确认上传 
    GFFconfirmUpData(formDate){
        let vm=this;
        $(".upDataButtonByBox_b").fadeOut();
        $(".dTable").fadeOut();
        $(".beijing").fadeOut();
        let headersToken=sessionStorage.getItem("token");
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$importSkuOfferURL+"?offer_sn="+vm.$route.query.offer_sn,
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
                // let msg = res.msg.split(',')
                vm.msgStr=res.msg;
                vm.getlistData();
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                vm.getlistData();
                $("#file").val('');
                vm.fileName='';
                // vm.$message(res.msg);
            }else{
                // let msg = res.msg.split(',')
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
                // vm.$message(res.msg);
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
    d_table(){
        let vm=this;
        window.open(vm.downloadUrl+'/客户订单_报价导入.xls');
    },
    backUpPage(){
        let vm = this;
        vm.$router.push('/offerList');
    }
  },
  computed:{
      //表格宽度计算 
        tableWidth(){
            let vm=this;
            let judgeRate=vm.checkedCities.find(function(e){
                    return e=='自采毛利';
            });
            let judgeCost=vm.checkedCities.find(function(e){
                return e=='费用';
            });
            var widthLength=vm.checkedCities.length*100+900;
            if(judgeRate){
                widthLength=widthLength+(vm.rateTdWidth-1)*100;
            }
            if(judgeCost){
                widthLength=widthLength+(vm.totalCost-1)*100;
            }
            let title = $(".title").width();
            if(widthLength<title){
                widthLength=title;
            }
            return "width:"+widthLength+"px"
        },
        //选择要展示的表头 
        selectTitle(){
            let vm = this;
            return function(str){
                let judgeSelection=vm.checkedCities.find(function(e){
                    return e==str;
                });
                if(judgeSelection){
                    return true;
                }else{
                    return false;
                }
            }
        },
        //销售毛利大于9的字体绿色，小于8的字体红色
        fontColor(){
            let vm = this;
            return function(runMarRate){
                if(runMarRate>9){
                    return "color: rgb(0, 172, 77)";
                }else if(runMarRate<8){
                    return "color:red";
                }
            }
        }
  }
}
</script>

<style>
.getOfferDetail .back{
    margin-top: 25px;
}
.getOfferDetail .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
.getOfferDetail .text_right{
    margin-top: 35px;
    float: right;
}
/* .ordGoodsOffer_b .brandName{
    float: left;
}
.ordGoodsOffer_b .brandName{
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
} */
.getOfferDetail .DJzhekou{
    margin-top: 40px;
    float: right;
}
ul {
    list-style-type: none;
    padding: 0;
}
.getOfferDetail .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical; 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.getOfferDetail .el-icon-circle-plus{
    margin-left: 10px;
    font-size: 20px;
    cursor: pointer;
}
.blue {
        background:#bcd4ec;  
}
.getOfferDetail .xuanzhong{
    background: #00C1DE;
}
.getOfferDetail .weixuanzhong{
    background-color: #ccc;
}
.getOfferDetail .WholeInput input{
    width: 80%;
}
.getOfferDetail .partInput input{
    width: 80%;
}
.getOfferDetail .el-radio-group {
    margin-top: 10px;
    line-height: 30px; 
}
.getOfferDetail .el-popover__reference{
    color: red;
}
.getOfferDetail .el-popover--plain {
    color: red;
}
.widthThreeHundred{
    width: 300px;
}
.widthTwoHundred{
    width: 200px;
}
.widthOneHundred{
    width: 100px;
}
.widthOneHAndFive{
    width: 150px;
}
.getOfferDetail.selectTitle .el-checkbox+.el-checkbox {
    margin-right: 30px;
    width: 120px;
    height: 40px;
    line-height: 40px;
}
.getOfferDetail .selectTitle .el-checkbox {
    margin-right: 30px;
    /* margin-left: 30px; */
    width: 120px;
    height: 40px;
    line-height: 40px;
}
.getOfferDetail table tr:nth-child(even){
    background:#fafafa;
}
.backWhite{
    background-color: #fff !important;
}
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
.dTableOne{
    position: fixed;
    z-index: 10039;
    width: 85px;
    height: 27px;
    margin: 250px auto;
    top: 134px;
    left: 135px;
    bottom: 0;
    right: 0;
}
.dTableTwo{
    position: fixed;
    z-index: 10039;
    width: 385px;
    height: 27px;
    margin: 250px auto;
    top: 172px;
    left: 336px;
    bottom: 0;
    right: 0;
}
.getOfferDetail .upDataButtonByBox_b .file{
    right: 95px;
}

.getOfferDetail table { text-align: center; border-color:#ccc; border-collapse: collapse;} 
.getOfferDetail .t_n{width:19%; height:703px; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.getOfferDetail .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.getOfferDetail .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.getOfferDetail .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.getOfferDetail .dd{height:659px!important; height:659px; overflow-y:hidden;}
.getOfferDetail .t_i{width:99.9%; height:auto; border-right:1px solid #ccc;border-left: 1px solid #ccc;border-top:1px solid #ccc}
.getOfferDetail .t_i_h{width:100%; overflow-x:hidden;}
.getOfferDetail .ee{width:110%!important; width:110%; text-align: center;background: #fafafa;}
.getOfferDetail .t_i_h table{width:3400px;border-top: #ccc;border-left: #ccc;border-right: #ccc;border-bottom: #ccc;}
.getOfferDetail .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.getOfferDetail .cc{width:100.6%;height: 600px; border-bottom:1px solid #ccc; overflow:auto;}
.getOfferDetail .cc table{width:3400px;border-top: #ccc;border-left: #ccc;border-right: #ccc; }
.getOfferDetail .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>
