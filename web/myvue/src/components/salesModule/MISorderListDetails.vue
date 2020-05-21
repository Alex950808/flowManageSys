<template>
  <div class="MISorderListDetails">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="demandManageDetails bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle title">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>大YD单详情</span>
                    </div>
                    <el-row style="margin-bottom: 10px;">
                        <span class="fontWeight F_S_twenty">订单信息：</span>
                        <span class="bgButton" @click="seeYD(MIStitleData.mis_order_sn)">查看子单</span>
                        <span class="bgButton" @click="goodsOffer(MIStitleData.mis_order_sn,MIStitleData.sale_user_id)">商品报价</span>
                        <span class="bgButton" @click="d_table(MIStitleData.mis_order_sn)">导出总单信息</span>
                        <span class="bgButton" v-if="MIStitleData.is_offer==1&&MIStitleData.is_advance==1" @click="dialogVisibleImport = true;openImportSubOrd();listSaleAccount()">上传DD子单</span>
                        
                        <el-dialog :title="'上传DD子单 '+MIStitleData.mis_order_sn" :visible.sync="dialogVisibleImport" width="800px">
                            <span class="redFont F_S_Sixteen" style="position: absolute;top: 24px;left: 330px;">*请确认sku的exw折扣是否为最新折扣</span>
                            <span class="notBgButton d_tableDD" @click="d_tableDD()">DD子单导入模板下载</span>
                            <el-row class="lineHeightForty MB_twenty">
                               <el-col :span="4">
                                    <span class="redFont">*</span>交付时间:
                                </el-col>
                                <el-col :span="6">
                                     <div class="block">
                                        <el-date-picker style="width:100%" v-model="entrustTime" value-format="yyyy-MM-dd" type="date" placeholder="请选择交付时间"></el-date-picker>
                                    </div>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <span class="redFont">*</span>销售账号:
                                </el-col>
                                <el-col :span="6">
                                     <template>
                                        <el-select v-model="saleUserid" placeholder="请选择销售客户id">
                                            <el-option v-for="item in saleUseridS" :key="item.id" :label="item.label" :value="item.label"></el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4">
                                    <span class="redFont">*</span>外部订单号:
                                </el-col>
                                <el-col :span="6">
                                     <div class="block">
                                         <el-input v-model="external_sn" placeholder="请输入外部订单号"></el-input>
                                        <!-- <el-date-picker style="width:100%" v-model="external_sn" value-format="yyyy-MM-dd" type="date" placeholder="请选择交付时间"></el-date-picker> -->
                                    </div>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    备注:
                                </el-col>
                                <el-col :span="6">
                                     <div class="block">
                                         <el-input v-model="remark" placeholder="请输入备注"></el-input>
                                        <!-- <el-date-picker style="width:100%" v-model="remark" value-format="yyyy-MM-dd" type="date" placeholder="请选择交付时间"></el-date-picker> -->
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4">
                                    期望到仓日:
                                </el-col>
                                <el-col :span="6">
                                     <div class="block">
                                         <!-- <el-input v-model="expect_time" placeholder="请输入期望到仓日"></el-input> -->
                                         <el-date-picker style="width:100%" v-model="expect_time" value-format="yyyy-MM-dd" type="date" placeholder="请选择期望到仓日"></el-date-picker>
                                    </div>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <span class="redFont">*</span>是否检查重复数据:
                                </el-col>
                                <el-col :span="6">
                                     <template>
                                        <el-select v-model="is_repeat" placeholder="请选择销售客户id">
                                            <el-option v-for="item in is_repeatS" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4">
                                    <span class="redFont">*</span>请选择文件:
                                </el-col>
                                <el-col :span="6">
                                    <div>
                                        <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                            <div class="file">
                                            <img src="../../image/upload.png"/>
                                            <span v-if="fileName==''">点击上传文件</span>
                                            <span v-if="fileName!=''">{{fileName}}</span>
                                            <input class="w_h_ratio" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                            </div>
                                        </form>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisibleImport = false">取消</el-button>
                                <el-button type="primary" @click="importSubOrd()">确定导入
                                </el-button>
                            </span>
                        </el-dialog>
                    </el-row>
                    <div style="margin-bottom: 10px;">
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>销售用户：{{MIStitleData.user_name}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>商品总数：{{goodsNum}}</span></div></el-col>
                            <el-col :span="5" :offset="1">
                                <div>
                                    <span v-if="MIStitleData.is_offer==0" class="redFont">
                                        <i class="el-icon-close"></i>是否报价：{{MIStitleData.offer_desc}}
                                    </span>
                                    <span v-if="MIStitleData.is_offer==1" class="D_greenFont">
                                        <i class="el-icon-check"></i>是否报价：{{MIStitleData.offer_desc}}
                                    </span>
                                </div>
                            </el-col>
                            <el-col :span="5" :offset="1">
                                <div>
                                    <span v-if="MIStitleData.is_advance==0" class="redFont">
                                        <i class="el-icon-close"></i>是否预判：{{MIStitleData.advance_desc}}
                                    </span>
                                    <span v-if="MIStitleData.is_advance==1" class="D_greenFont">
                                        <i class="el-icon-check"></i>是否预判：{{MIStitleData.advance_desc}}
                                    </span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>MIS订单号：{{MIStitleData.mis_order_sn}}</span></div></el-col>
                            <!-- <el-col :span="5" :offset="1"><div><span>部门：{{MIStitleData.de_name}}</span></div></el-col> -->
                            <el-col :span="5" :offset="1"><div><span>新品总数：{{isReplenish.newGoodsNum}}</span></div></el-col>
                            <!-- <el-col :span="5" :offset="1"><div><span>订单状态：{{MIStitleData.status_desc}}</span></div></el-col> -->
                            <el-col :span="5" :offset="1"><div><span>补单状态：{{isReplenish.replenishDesc}}</span></div></el-col>
                            <el-col :span="5" :offset="1"><div><span>部门：{{MIStitleData.de_name}}</span></div></el-col>
                        </el-row>
                        <el-row class="L_H_F grayFont">
                            <el-col :span="5" :offset="1"><div><span>子单号：{{MIStitleData.de_name}}</span></div></el-col>
                        </el-row>
                    </div>
                    <div class="MB_ten">
                        <span class="fontWeight F_S_twenty">商品信息：</span>
                    </div>
                    <div>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="3" :offset="1"><div>商品名称：</div></el-col>
                            <el-col :span="3">
                                <div>
                                    <el-input v-model="goodsName" placeholder="请输入商品名称"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div>品牌名称：</div></el-col>
                            <el-col :span="3">
                                <div>
                                    <template>
                                        <el-select v-model="brandName" clearable placeholder="请选择品牌名称">
                                            <el-option v-for="item in brandList" :key="item.brand_id" :label="item.brand_name" :value="item.brand_name"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div>商家编码：</div></el-col>
                            <el-col :span="3">
                                <div>
                                    <el-input v-model="erpMerchantNo" placeholder="请输入商家编码"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <span>
                                    <span class="bgButton" @click="getMISOrderDatailsData()">搜索</span>
                                    <!-- <span v-if="$store.state.clickStutes==false" class="bgButton">搜索</span> -->
                                </span>
                            </el-col>
                        </el-row>
                        <!-- <el-row class="lineHeightForty MB_ten">
                            <el-col :span="3" :offset="1"><div>交付时间：</div></el-col>
                            <el-col :span="3">
                                <div>
                                    <el-date-picker style="width:100%;" v-model="deliveryTime" type="date" placeholder="选择交付时间"> </el-date-picker>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div>销售账号：</div></el-col>
                            <el-col :span="3">
                                <div>
                                    <el-input v-model="salesAccount" placeholder="请输入销售账号"></el-input>
                                </div>
                            </el-col>
                        </el-row> -->
                    </div>
                    <el-row class="lineHeightForty MB_ten">
                        <el-col :span="8">
                            <span class="bgButton" @click="selectcol()">选择列</span>
                            <span v-if="newPriceNum>0&&MIStitleData.is_advance==0" class="bgButton" @click="getGoodsNewPrice();dialogVisiblenewPriceNum=true;">商品价格变动详情</span>
                            <span v-if="isReplenish.newGoodsNum>0">
                                <!-- <span v-if="$store.state.clickStutes==true" class="bgButton" @click="getOrderNewGoods();">查看新品</span>
                                <span v-if="$store.state.clickStutes==false" class="grBgButton">查看新品</span> -->
                                <span class="bgButton" @click="getOrderNewGoods();">查看新品</span>
                            </span>
                            <el-dialog title="商品价格变动详情" :visible.sync="dialogVisiblenewPriceNum" width="1000px">
                                <div class="lineHeightForty MB_twenty fontCenter">
                                    <el-col :span="5">
                                        <span>
                                        商品名称
                                        </span>
                                    </el-col>
                                    <el-col :span="5" :offset="1">
                                        <span>
                                        品牌名称
                                        </span>
                                    </el-col>
                                    <el-col :span="4" :offset="1">
                                        商家编码
                                    </el-col>
                                    <el-col :span="4" :offset="1"> 
                                        商品规格码
                                    </el-col>
                                    <el-col :span="3">
                                        美金原价
                                    </el-col>
                                </div>
                                <div class="lineHeightForty MB_twenty fontCenter" v-for="(item,index) in GoodsNewPriceList" :key="index">
                                    <el-col :span="5" class="ellipsis">
                                        <el-tooltip v-if="item.goods_name!=null" class="item" effect="light" :content="item.goods_name" placement="top">
                                            <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                        </el-tooltip>
                                        <span v-if="item.goods_name==null">-</span>
                                    </el-col>
                                    <el-col :span="5" :offset="1" class="ellipsis">
                                        <el-tooltip v-if="item.brand_name!=null" class="item" effect="light" :content="item.brand_name" placement="top">
                                            <span style="-webkit-box-orient: vertical;">{{item.brand_name}}</span>
                                        </el-tooltip>
                                        <span v-if="item.brand_name==null">-</span>
                                    </el-col>
                                    <el-col :span="4" :offset="1">
                                        <span v-if="item.erp_merchant_no!=''">{{item.erp_merchant_no}}</span>
                                        <span v-else>-</span>
                                    </el-col>
                                    <el-col :span="4" :offset="1">
                                        <span v-if="item.spec_sn!=''">{{item.spec_sn}}</span>
                                        <span v-else>-</span>
                                    </el-col>
                                    <el-col :span="3">
                                        <input type="text" @change="modifyNewPrice(item.spec_sn,index,item.new_spec_price)" style="width:70%" :name="'price'+index" :value="item.new_spec_price"/>
                                    </el-col>
                                </div>
                                <span slot="footer" class="dialog-footer">
                                    <el-button type="info" @click="dialogVisiblenewPriceNum = false">取消</el-button>
                                    <el-button type="primary" @click="submitNewPrice()">提交</el-button>
                                </span>
                            </el-dialog>
                        </el-col>
                        <el-col class="fontRight" :span="16" v-if="isReplenish.replenishInt!=1">
                            <!-- <span v-if="MIStitleData.status==1" class="bgButton" @click="openDiscountBox()">订单批量挂靠</span> -->
                                <el-dialog title="订单批量挂靠" :visible.sync="selectDialogVisibleRate" width="800px">
                                    <el-row class="lineHeightForty">
                                        <el-col :span="4">
                                            <div>请选择挂靠类型：</div>
                                        </el-col>
                                        <el-col :span="6">
                                            <template>
                                                <el-select v-model="selectType" placeholder="请选择挂靠类型">
                                                    <el-option v-for="item in options" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                                </el-select>
                                            </template>
                                        </el-col>
                                        <el-col :span="4" :offset="1">
                                            <span v-if="selectType==1">请选择交付时间 ：</span>
                                            <span v-if="selectType==2">请选择销售账户 ：</span>
                                        </el-col>
                                        <el-col :span="6">
                                            <div v-if="selectType==1">
                                                <template>
                                                    <div class="block">
                                                        <el-date-picker @change="startDate()" v-model="modifyValue" type="date" placeholder="选择日期"></el-date-picker>
                                                    </div>
                                                </template>
                                            </div>
                                            <div v-if="selectType==2">
                                                <template>
                                                    <el-select v-model="modifyValue" placeholder="请选择销售客户id">
                                                        <el-option v-for="item in saleUseridS" :key="item.id" :label="item.label" :value="item.label"></el-option>
                                                    </el-select>
                                                </template>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <div slot="footer" class="dialog-footer fontCenter">
                                        <span class="grayBgButton" @click="selectDialogVisibleRate = false">取 消</span>
                                        <span class="redBgButton" @click="selectDialogVisibleRate = false;batchAffiliate()">确 定</span>
                                    </div>
                                </el-dialog>
                            <span v-if="MIStitleData.is_advance==0" class="bgButton" @click="openBoxes('2')">完成预判</span>
                            <!-- <span v-if="MIStitleData.status==1" class="bgButton" @click="openBoxes('3')">完成挂靠</span> -->
                            <!-- <span v-if="MIStitleData.status==2" class="bgButton" @click="openBoxes('1')">生成子订单</span> -->
                            <template v-if="MIStitleData.status==2">
                                <el-popover  placement="top-start" width="200" trigger="click"
                                    content="只有完成报价，预判和挂靠之后才能执行该操作">
                                    <i class="el-icon-question redFont" slot="reference"></i>
                                </el-popover>
                            </template>
                            <span v-if="MIStitleData.is_advance!=1">
                                <span class="bgButton" v-if="isPortShow" @click="exportAdvance()">预判数据导出</span>
                                <span class="grBgButton" v-if="!isPortShow">预判数据导出</span>
                            </span>
                            <span v-if="MIStitleData.is_advance!=1" class="bgButton"  @click="upDataBox()">预判数量导入</span>
                        </el-col>
                    </el-row>
                    <el-row>
                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                        <thead>
                                            <tr>
                                                <th class="w_Fty" style="width:60px"><input class="allChecked" type="checkbox" @change="allSelect()"/>全选</th>
                                                <th class="ellipsis" v-if="selectTitle('商品名称')" style="width:300px;">商品名称</th>
                                                <th style="width:130px" v-if="selectTitle('品牌名称')">品牌名称</th>
                                                <th style="width:160px" v-if="selectTitle('子单单号')">子单单号</th>
                                                <th style="width:160px" v-if="selectTitle('商家编码')">商家编码</th>
                                                <th style="width:160px" v-if="selectTitle('商品规格码')">商品规格码</th>
                                                <th style="width:160px" v-if="selectTitle('商品参考码')">商品参考码</th>
                                                <th style="width:160px" v-if="selectTitle('商品代码')">商品代码</th>
                                                <th style="width:160px" v-if="selectTitle('平台条码')">平台条码</th>
                                                <th style="width:130px" v-if="selectTitle('是否为套装')">是否为套装</th>
                                                <th style="width:130px" v-if="selectTitle('套装货号')">套装货号</th>
                                                <th style="width:130px" v-if="selectTitle('套装美金原价')">套装美金原价</th>
                                                <th style="width:130px" v-if="selectTitle('是否可以搜到')">是否可以搜到</th>
                                                <th style="width:130px" v-if="selectTitle('美金原价')">美金原价</th>
                                                <th style="width:90px" v-if="selectTitle('需求量')">需求量</th>
                                                <th style="width:90px" v-if="selectTitle('实时库存')">实时库存</th>
                                                <th style="width:90px" v-if="selectTitle('待采量')">
                                                    待采量
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="需求量-当前库存量，如果为负数则显示为0，会随库存而变化">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:100px" v-if="selectTitle('预判采购量')">
                                                    预判采购量
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="采购部预先判断的数量,默认值为生成总单时的待采数量，不随库存而变化">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:130px" v-if="selectTitle('交付时间')">交付时间</th>
                                                <th style="width:130px" v-if="selectTitle('销售账号')">销售账号</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc"  @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <tbody>
                                        <tr v-for="(item,index) in MIStableData">
                                            <td class="w_Fty cb_one" style="width:60px">
                                                <input @click="oneSelect(index)" type="checkbox" :name="index"/>
                                            </td>
                                            <td class="ellipsis fontLift" style="width:300px;" v-if="selectTitle('商品名称')">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="top">
                                                <span style="-webkit-box-orient: vertical;width:300px;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:130px" v-if="selectTitle('品牌名称')" class="ellipsis fontLift" :title='item.brand_name'>
                                                <span style="-webkit-box-orient: vertical;width:130px;">&nbsp;&nbsp;{{item.brand_name}}</span>
                                            </td>
                                            <td class="ellipsis" style="width:160px" v-if="selectTitle('子单单号')">
                                                <el-tooltip class="item" effect="light" :content="item.sub_order_sn" placement="right">
                                                <span style="-webkit-box-orient: vertical;width:160px;">{{item.sub_order_sn}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:160px" v-if="selectTitle('商家编码')">{{item.erp_merchant_no}}</td>
                                            <td style="width:160px" v-if="selectTitle('商品规格码')">{{item.spec_sn}}</td>
                                            <td style="width:160px" v-if="selectTitle('商品参考码')">{{item.erp_ref_no}}</td>
                                            <td style="width:160px" v-if="selectTitle('商品代码')">{{item.erp_prd_no}}</td>
                                            <td class="ellipsis" style="width:160px" v-if="selectTitle('平台条码')">
                                                <el-tooltip class="item" effect="light" :content="item.platform_barcode" placement="right">
                                                    <span style="-webkit-box-orient: vertical;width:160px;">{{item.platform_barcode}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td style="width:130px" v-if="selectTitle('是否为套装')">
                                                <span v-if="item.is_suit=='0'">不是</span>
                                                <span v-else-if="item.is_suit=='1'">是</span>
                                                <span v-else></span>
                                            </td>
                                            <td style="width:130px" v-if="selectTitle('套装货号')">{{item.suit_sn}}</td>
                                            <td style="width:130px" v-if="selectTitle('套装美金原价')">{{item.suit_price}}</td>
                                            <td style="width:130px" v-if="selectTitle('是否可以搜到')">
                                                <span v-if="item.is_search=='0'">不可以</span>
                                                <span v-else-if="item.is_search=='1'">可以</span>
                                                <span v-else></span>
                                            </td>
                                            <td style="width:130px" v-if="selectTitle('美金原价')">{{item.spec_price}}</td>
                                            <td style="width:90px" v-if="selectTitle('需求量')">{{item.goods_number}}</td>
                                            <td style="width:90px" v-if="selectTitle('实时库存')">
                                                <span v-if="item.gStockNum>0">{{item.gStockNum}}</span>
                                                <span v-if="item.gStockNum<=0" class="redFont">{{item.gStockNum}}</span>
                                            </td>
                                            <td style="width:90px" v-if="selectTitle('待采量')">
                                                <span v-if="item.buy_num<0">0</span>
                                                <span v-else>{{item.buy_num}}</span>
                                            </td>
                                            <td :class="['y_p_number'+index]" v-if="selectTitle('预判采购量')" style="width:100px">
                                                <span v-if="MIStitleData.is_advance==1">
                                                    <i class="NotStyleForI" title="该订单已结束停止修改预判采购量">{{item.wait_buy_num}}</i>
                                                </span>
                                                <span class="notBgButton" v-if="MIStitleData.is_advance==0" @click="dialogVisible=true;editUserAndTime(item.spec_sn,item.entrust_time,item.sale_user_account,item.wait_buy_num,0,index,item.goods_name,item.buy_num)" title="总单挂靠">
                                                    <i class="NotStyleForI" v-if="item.wait_buy_num!=null">{{item.wait_buy_num}}</i>
                                                    <i class="NotStyleForI redFont" v-if="item.wait_buy_num==null">0</i>
                                                </span>
                                            </td>
                                            <td :class="['j_f_time'+index]" v-if="selectTitle('交付时间')" style="width:130px">
                                                <span v-if="MIStitleData.status==3">
                                                    <i class="infoNotBgButton NotStyleForI" title="该订单已结束停止修改交付时间">{{item.entrust_time}}</i>
                                                </span>
                                                <span class="notBgButton" v-if="MIStitleData.status!=3" @click="dialogVisible=true;editUserAndTime(item.spec_sn,item.entrust_time,item.sale_user_account,item.wait_buy_num,1,index,item.goods_name,item.buy_num)" title="总单挂靠">
                                                    <i class="NotStyleForI" v-if="item.entrust_time!=null">{{item.entrust_time}}</i>
                                                    <i class="NotStyleForI redFont" v-if="item.entrust_time==null">选择</i>
                                                </span>
                                            </td>
                                            <td :class="['x_s_user'+index]" v-if="selectTitle('销售账号')" style="width:130px">
                                                <span v-if="MIStitleData.status==3">
                                                    <i class="infoNotBgButton NotStyleForI" title="该订单已结束停止修改销售客户">{{item.sale_user_account}}</i>
                                                </span>
                                                <span class="notBgButton" v-if="MIStitleData.status!=3" @click="dialogVisible=true;editUserAndTime(item.spec_sn,item.entrust_time,item.sale_user_account,item.wait_buy_num,2,index,item.goods_name,item.buy_num);listSaleAccount(item.sale_user_account)" title="总单挂靠">
                                                    <i class="NotStyleForI" v-if="item.sale_user_account!=''">{{item.sale_user_account}}</i>
                                                    <i class="NotStyleForI redFont" v-if="item.sale_user_account==''">选择</i>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </el-row>
                    <el-dialog title="总单挂靠" :visible.sync="dialogVisible" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>商品名称：</div></el-col>
                            <el-col :span="6">
                                <div class="overOneLinesHeid"><span :title="goods_name">{{goods_name}}</span></div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div>需求量：</div></el-col>
                            <el-col :span="6">
                                <div>{{buy_num}}</div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>挂靠类型：</div></el-col>
                            <el-col :span="6">
                            <div>
                                {{type}}
                            </div>
                            </el-col>
                            <!-- 以下type为3组分不同情况显示隐藏，以选择的挂靠类型不同显示不同的输入框 -->
                            <el-col :span="4" :offset="1" v-if="type=='交付时间'"><div class="">交付时间：</div></el-col>
                            <el-col :span="6" v-if="type=='交付时间'">
                                <template>
                                    <div class="block">
                                        <el-date-picker @change="startDate()" v-model="entrustTime" type="date" placeholder="选择日期"></el-date-picker>
                                    </div>
                                </template>
                            </el-col>
                            <el-col :span="4" :offset="1" v-if="type=='销售账号'"><div class="">销售客户账号：</div></el-col>
                            <el-col :span="6" v-if="type=='销售账号'">
                                <div>
                                    <template>
                                        <el-select v-model="saleUserAccount" placeholder="请选择销售客户id">
                                            <el-option v-for="item in saleUseridS" :key="item.id" :label="item.label" :value="item.label"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1" v-if="type=='预判采购量'"><div class="">预判采购量：</div></el-col>
                            <el-col :span="6" v-if="type=='预判采购量'">
                                <div>
                                    <el-input v-model="waitBuyNum" placeholder="请输入预判采购量"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>销售用户：</div></el-col>
                            <el-col :span="6">
                            <div>
                                {{MIStitleData.user_name}}
                            </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="dialogVisible = false;doEditUserAndTime()">
                                确 定
                                <span style="display: none" class="el-icon-loading confirmLoading"></span>
                            </el-button>
                        </span>
                    </el-dialog>
                    <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                </el-col>
            </div>
      </el-col>
      <div class="confirmPopup_b" v-if="show">
            <div class="confirmPopup" style="height: 210px;">
                <div class="confirmTitle" style="margin-bottom: 23px;">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                    &nbsp;&nbsp;<i class="el-icon-close" @click="editIsNo()"></i>&nbsp;&nbsp;
                </div>
                <div>请您确认是否要完成预判！</div>
                <div class="redFont" style="width: 80%;margin-left: 10%;text-align: left;">*注：请确认是否有价格变动的商品,完成预判后将无法修改美金原价.</div>
                <div class="confirm"><el-button @click="confirmationAudit()" size="mini" type="primary">是</el-button><el-button @click="editIsNo()" size="mini" type="info">否</el-button></div>
            </div>
      </div>
      <selectCol :selectStr="selectStr"></selectCol>
      <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
      <successfulOperation :msgStr="msgStr"></successfulOperation>
      <operationFailed :msgStr="msgStr"></operationFailed>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableWidth } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { exportsa } from '@/filters/publicMethods.js'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import selectCol from '@/components/UiAssemblyList/selectCol'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
export default {
    components:{
        customConfirmationBoxes,
        selectCol,
        fontWatermark,
        upDataButtonByBox,
        operationFailed,
        successfulOperation,
    },
  data(){
    return{
        url: `${this.$baseUrl}`,
        downloadUrl:`${this.$downloadUrl}`,
        titleData:'',//title数据
        search:'',//用户输入数据 
        total:0,//页数默认为0
        saleMarRate:'',
        spec_sn:'',
        radio:2,
        index:0,
        Cost:'',
        isShow:false,
        dialogVisible:false,
        headersStr:'',
        goodsNum:'',
        is_export:'',
        brandList:[],
        // isOrder:false,
        // isMISOrder:false,
        //MIS数据
        MIStableData:[],
        MIStitleData:'',
        //弹框数据
        dialogVisible:false,
        // types:[{"label":"挂靠交付时间","id":"1"},{"label":"挂靠销售账户","id":"2"},{"label":"修改待采量","id":"3"}],
        type:'',
        dataStr:[],
        entrustTime:'',//交付时间 
        saleUserAccount:'',//销售用户
        waitBuyNum:'',//待采数量 
        saleUserAccount:'',
        saleUseridS:[],
        saleUserid:'',
        isNotSplit:false,
        contentStr:'',
        expect_time:'',
        //数据筛选
        dialogVisibleScreen:false,
        brandName:'',
        goodsName:'',
        deliveryTime:'',
        salesAccount:'',
        erpMerchantNo:'',
        bombBoxStr:'我要去总单详情！',
        goWhere:'总单详情',
        confirmUrl:'',
        indexNum:'',
        buy_num:'',
        goods_name:'',
        //选择列 
        selectStr:['商品名称','子单单号','商家编码','商品规格码','商品参考码','商品代码','平台条码','品牌名称','是否为套装','套装货号','套装美金原价','是否可以搜到','美金原价','需求量','实时库存','待采量','预判采购量','交付时间','销售账号'],
        //批量挂靠
        selectDialogVisibleRate:false,
        options:[{"label":"挂靠交付时间","id":"1"},{"label":"挂靠销售账户","id":"2"}],
        selectType:'',
        arrSpecSn:'',
        modifyValue:'',
        //查看新品
        isReplenish:'',
        //预判数量导入 
        titleStr:'预判数量导入',
        //导入DD数据 
        dialogVisibleImport:false,
        fileName:'',
        external_sn:'',
        remark:'',
        isPortShow:true,
        is_repeat:1,
        is_repeatS:[{"label":"不检查","id":0},{"label":"检查","id":1}],

        msgStr:'',
        //商品价格变动详情
        newPriceNum:'',
        show:false,
        dialogVisiblenewPriceNum:false,
        GoodsNewPriceList:[],
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
      this.getMISOrderDatailsData();
      this.$store.commit('selectList', this.selectStr);
  },
  methods:{
    getMISOrderDatailsData(){
        let vm = this;
        this.$store.commit('editClickStutes');
        let deliveryTime;
        if(vm.deliveryTime!=''&&vm.deliveryTime!=null){ 
            deliveryTime=dateToStr(vm.deliveryTime);
        }
        axios.post(vm.url+vm.$getMisOrderDetailURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
                "goods_name":vm.goodsName,
                "brand_name":vm.brandName,
                "entrust_time":deliveryTime,
                "sale_user_account":vm.salesAccount,
                "erp_merchant_no":vm.erpMerchantNo,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.$store.commit('editClickStutes');
            vm.MIStableData=res.data.orderDetail.goodsInfo;
            vm.goodsNum=res.data.orderDetail.goodsNum;
            vm.is_export=res.data.orderDetail.is_export;
            vm.MIStitleData=res.data.orderDetail.orderInfo[0];
            vm.brandList=res.data.orderDetail.brand;
            vm.isReplenish=res.data.orderDetail.isReplenish;
            vm.newPriceNum=res.data.orderDetail.newPriceNum;
            if(parseInt(vm.goodsNum)>15){
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
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //编辑用户折扣及时间 
    editUserAndTime(spec_sn,entrust_time,sale_user_account,wait_buy_num,index,indexNum,goodsName,bayNum){
        let vm = this;
        vm.dataStr=[spec_sn,entrust_time,sale_user_account,wait_buy_num];
        if(index==0){
            vm.type="预判采购量"
        }else if(index==1){
            vm.type="交付时间"
        }else if(index==2){
            vm.type="销售账号"
        }
            vm.entrustTime=entrust_time;
            vm.saleUserAccount=sale_user_account;
            vm.indexNum=indexNum;
            vm.goods_name=goodsName;
            vm.buy_num=bayNum;
        if(wait_buy_num!==''){
            vm.waitBuyNum=wait_buy_num;
        }
    },
    //获取用户列表
    listSaleAccount(){
        let vm = this;
        // vm.saleUseridS.splice(0); 
        if(vm.saleUseridS.length>0){
                return
        }
        axios.post(vm.url+vm.$listSaleAccountURL,
            {
                "saleUserId":vm.MIStitleData.sale_user_id
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            let index=false;
            res.data.saleAccount.forEach(element => {
                vm.saleUseridS.push({"label":element.user_name,"id":element.id});
                // if(sale_user_account==element.id){
                //     index=true;
                // }
            });
            if(index==true){
                vm.saleUserAccount='请选择销售用户';
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //检查开始时间不能早于当天凌晨十二点
    startDate(){
        let vm = this;
        var timeStamp = new Date(new Date().setHours(0,0,0,0))
        var date = new Date(dateToStr(timeStamp));
        // var time = date.getTime()+86400;//明天凌晨零点
        var time = date.getTime()-86400000;//今天凌晨零点
        var dateTwo = new Date(this.entrustTime);
        var timeTwo = dateTwo.getTime();
        if(vm.entrustTime!=''){
            if(timeTwo<time){
                vm.entrustTime='';
                vm.$message('开始日期必须大于等于当前日期！');
            }
        }
    },
    //提交总单挂靠
    doEditUserAndTime(){
        let vm = this;
        let dataStr;
        if(vm.index==1){
            return
        }
        vm.index=1;
        if(vm.type=="交付时间"){
            if((typeof vm.entrustTime)=='string'){
                vm.$message('请先选择交付时间或交付时间未更改！');
                return
            }
            dataStr={"mis_order_sn":vm.$route.query.mis_order_sn,"spec_sn":vm.dataStr[0],"type":1,"entrust_time":dateToStr(vm.entrustTime)};
        }else if(vm.type=="销售账号"){
            if(vm.saleUserAccount==''){
                vm.$message('请先填写销售账号！');
                return
            }
            dataStr={"mis_order_sn":vm.$route.query.mis_order_sn,"spec_sn":vm.dataStr[0],"type":2,"sale_user_account":vm.saleUserAccount};
        }else if(vm.type=="预判采购量"){
            if(vm.waitBuyNum==''){
                vm.$message('请先填写预判采购量！');
                return
            }
            dataStr={"mis_order_sn":vm.$route.query.mis_order_sn,"spec_sn":vm.dataStr[0],"type":3,"wait_buy_num":vm.waitBuyNum};
        }
        $(".confirmLoading").show();
        axios.post(vm.url+vm.$orderAffiliateURL, dataStr,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.index=0;
            vm.dialogVisible = false;
            if(res.data.code==2024){
                $(".confirmLoading").hide();
                if(vm.type=="交付时间"){
                    vm.MIStableData[vm.indexNum].entrust_time=dateToStr(vm.entrustTime)
                }else if(vm.type=="销售账号"){
                    vm.MIStableData[vm.indexNum].sale_user_account=vm.saleUserAccount;
                }else if(vm.type=="预判采购量"){
                    vm.MIStableData[vm.indexNum].wait_buy_num=vm.waitBuyNum;
                }
                vm.getMISOrderDatailsData();
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            vm.index=0;
            $(".confirmLoading").hide();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //打开确认拆分弹框 
    openBoxes(index){
        let vm = this;
        if(index=='1'){//去拆分
            $(".confirmPopup_b").fadeIn();
            vm.confirmUrl=vm.$orderSubmenuURL
            vm.contentStr='请您确认是否要生成子订单！'
        }else if(index=='2'){//生成预判数量 
            vm.show=true;
            vm.confirmUrl=vm.$finishAdvanceURL
            vm.contentStr='请您确认是否要完成预判！'
        }else if(index=='3'){
            $(".confirmPopup_b").fadeIn();
            vm.confirmUrl=vm.$finishAffiliatedURL
            vm.contentStr='请您确认是否要完成挂靠！'
        }
    },
    //确认拆分/生成预判采购量
    confirmationAudit(){
        let vm = this;
            $(".confirmPopup_b").fadeOut();
            vm.show=false;
        axios.post(vm.url+vm.confirmUrl,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==2024){
                vm.getMISOrderDatailsData();
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
    //关掉确认拆分弹出框
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    //查看YD单
    seeYD(mis_order_sn){
        let vm = this;
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
        if(vm.$route.query.page!=undefined){
            urlParam+='&page='+vm.$route.query.page
        }
        this.$router.push('/misSubOrderList?mis_order_sn='+mis_order_sn+urlParam);
    },
    //商品报价
    goodsOffer(mis_order_sn,sale_user_id){
        let vm = this;
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
        if(vm.$route.query.page!=undefined){
            urlParam+='&page='+vm.$route.query.page
        }
        vm.$router.push('/ordGoodsOffer?mis_order_sn='+mis_order_sn+"&saleUserId="+sale_user_id+urlParam);
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
        if(vm.$route.query.page!=undefined){
            urlParam+='&page='+vm.$route.query.page
        }
        if(vm.$route.query.isGetSubDetail=="GetSubDetail"){
            vm.$router.push('/getSubDetail'+'?sub_order_sn='+vm.$route.query.sub_order_sn);  
        }else if(vm.$route.query.isMISOrder=="isMISOrder"){
            vm.$router.push('/getMisOrderList?isSordD=isSordD'+urlParam);  
        }else if(vm.$route.query.isGetSubList=="GetSubList"){
            vm.$router.push('/getSubList'); 
        }
    },
    d_table(mis_order_sn){
        let vm=this;
        // $(event.target).addClass('grBgButton').removeClass('bgButton');
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$misOrderExportURL+'?mis_order_sn='+mis_order_sn+'&token='+headersToken);
    },
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    //全选 
    allSelect(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        if(allChecked==true){
            for(var i=0;i<vm.MIStableData.length;i++){
                $(".cb_one input[name="+i+"]").prop("checked",true);
            }
        }else if(allChecked==false){
            for(var i=0;i<vm.MIStableData.length;i++){
                $(".cb_one input[name="+i+"]").prop("checked",false);
            }
        }
    },
    //单选
    oneSelect(index){
        let vm = this;
        let isTrueList=[];
        for(var i=0;i<vm.MIStableData.length;i++){
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
        vm.listSaleAccount();
        let allChecked = $('.allChecked').prop("checked");
        let arrSpecSn = '';
        if(allChecked==true){
            vm.MIStableData.forEach(specInfo=>{
                arrSpecSn+=specInfo.spec_sn+',';
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.MIStableData.length;i++){
                let isTrue = $(".cb_one input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    arrSpecSn+=vm.MIStableData[i].spec_sn+',';
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
    // 批量挂靠
    batchAffiliate(){
        let vm = this;
        if(vm.selectType==1){//挂靠交付时间
            vm.modifyValue=dateToStr(vm.modifyValue);
        }else if(vm.selectType==2){//挂靠销售用户
            
        }
        axios.post(vm.url+vm.$batchAffiliateURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
                "strSpecSn":vm.arrSpecSn,
                "modifyValue":vm.modifyValue,
                "type":vm.selectType,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            if(res.data.code=='2024'){
                vm.modifyValue='';
                vm.getMISOrderDatailsData();
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
    //查看总单新品 
    getOrderNewGoods(){
        let vm = this;
        this.$store.commit('editClickStutes');
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
        if(vm.$route.query.page!=undefined){
            urlParam+='&page='+vm.$route.query.page
        }
        axios.post(vm.url+vm.$getOrderNewGoodsURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            // if(res.data.newGoodsInfo.length!=0){
                vm.$store.commit('editClickStutes');
                sessionStorage.setItem("tableData",JSON.stringify(res.data));
                vm.$router.push('/getOrderNewGoods?mis_order_sn='+vm.$route.query.mis_order_sn+urlParam);
            // }
        }).catch(function (error) {
            vm.$store.commit('editClickStutes');
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //预判数量导出 
    exportAdvance(){
        let vm = this;
        let tableName = "预判数量_"+vm.$route.query.mis_order_sn;
        vm.isPortShow=false;
        axios.post(vm.url+vm.$exportAdvanceURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            vm.isPortShow=true;
            if(res.data.code){
                vm.$message(res.data.msg)
            }else{
                exportsa(vm.url+vm.$exportAdvanceURL+"?mis_order_sn="+vm.$route.query.mis_order_sn,tableName)
            }
        }).catch(function (error) {
            vm.isPortShow=true;
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //预判数量导入
    // importAdvance(){
    //     let vm = this;
    // }
    upDataBox(){
        $(".upDataButtonByBox_b").fadeIn();
    },
    // 确定批量上传文件 
    GFFconfirmUpData(formDate){
        let vm=this;
        var formDate = new FormData($("#forms")[0]);
        $(".upDataButtonByBox_b").fadeOut();
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$importAdvanceURL+"?mis_order_sn="+vm.$route.query.mis_order_sn,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code=='2024'){
                // vm.$message('上传成功！'); 
                vm.msgStr=res.msg;
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                vm.getMISOrderDatailsData();
            }else{
                // vm.$message(res.msg); 
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
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
    //打开上传弹框 
    openImportSubOrd(){
        let vm = this;
        $("#file1").val('');
        vm.fileName='';
        vm.entrustTime='';
        vm.saleUserid='';
        vm.external_sn='';
        vm.remark='';
        vm.expect_time='';
        vm.is_repeat=1;
    },
    SelectFile(){
        let vm = this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    //确认上传 
    importSubOrd(){
        let vm=this;
        if(vm.fileName==''){
            vm.$message('请选择文件后上传！');
            return false;
        }
        if(vm.entrustTime==''||vm.entrustTime==null){
            vm.$message('请选择交付时间后上传！');
            return false;
        }
        if(vm.saleUserid==''){
            vm.$message('请选择销售客户后上传！');
            return false;
        }
        if(vm.external_sn==''){
            vm.$message('请输入外部订单号后上传！');
            return false;
        }
        if(vm.expect_time==''||vm.entrustTime==null){
            vm.$message('请输入期望到仓日后上传！');
            return false;
        }
        vm.dialogVisibleImport = false;
        var formDate = new FormData($("#forms")[0]);
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$importDDSubOrdURL+"?mis_order_sn="+vm.$route.query.mis_order_sn+"&entrust_time="+vm.entrustTime+"&sale_user_account="+vm.saleUserid+
                "&remark="+vm.remark+"&external_sn="+vm.external_sn+"&is_repeat="+vm.is_repeat+"&expect_time="+vm.expect_time,
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
                vm.msgStr=res.msg;
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                vm.getMISOrderDatailsData();
            }else{
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
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
    d_tableDD(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.downloadUrl+'/DD子单导入模板.xlsx');
    },
    //商品价格变动详情 
    getGoodsNewPrice(){
        let vm = this;
        vm.GoodsNewPriceList=[];
        axios.post(vm.url+vm.$getGoodsNewPriceURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            vm.GoodsNewPriceList=res.data.goodsInfo;
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    editIsNo(){
        let vm = this;
        vm.show=false;
    },
    //修改商品美金原价 
    modifyNewPrice(sn,index,new_spec_price){
        let vm = this;
        var  priceVal = $("input[name=price"+index+"]").val();
        axios.post(vm.url+vm.$modifyNewPriceURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
                "spec_sn":sn,
                "new_spec_price":priceVal,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            if(res.data.code=="2024"){
                vm.$message(res.data.msg);
            }else{
                $("input[name=price"+index+"]").val(new_spec_price)
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
    //
    submitNewPrice(){
        let vm = this;
        vm.dialogVisiblenewPriceNum=false;
        axios.post(vm.url+vm.$submitNewPriceURL,
            {
                "mis_order_sn":vm.$route.query.mis_order_sn,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=='2024'){
                vm.getMISOrderDatailsData()
            }
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                }
            });
    }
  },
  computed:{
    selectTitle(checkedCities){
       return selectTitle(checkedCities)
    },
    tableWidth(){
        return tableWidth(this.$store.state.select);
    },
  }
}
</script>

<style>
.MISorderListDetails .title{
    margin-bottom: 30px;
}
.MISorderListDetails .ellipsis span{
    /* line-height: 23px; */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.MISorderListDetails .el-icon-edit{
    float: right;
    margin-top: 10px;
}
.MISorderListDetails .bg-purple{
    line-height: 40px;
}
.MISorderListDetails .d_tableDD{
    position: absolute;
    bottom: 30px;
    left: 280px;
    left: 304px;
    z-index: 9999;
}
.MISorderListDetails .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.MISorderListDetails .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.MISorderListDetails .ee{width:100%!important; width:100%; text-align: center;}
.MISorderListDetails .t_i_h table{width:1530px;}
.MISorderListDetails .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.MISorderListDetails .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; overflow:auto;}
.MISorderListDetails .cc table{width:1530px; }
.MISorderListDetails .cc table td{ text-align:center}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>
