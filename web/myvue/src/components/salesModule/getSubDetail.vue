<template>
  <div class=" getSubDetail">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="demandManageDetails bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="listTitleStyle title">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>小DD单详情</span>
                    </el-row>
                    <el-row style="margin-bottom: 10px;">
                        <span class="upTitle MR_ten">订单信息：</span>
                        <span class="bgButton MR_ten" v-if="titleData.submenu_desc!='未分单'" @click="exportSubOrd()">导出订单</span>
                        <!-- <span v-if="titleData.status=='BD'" class="bgButton" @click="dialogVisibleImport = true;openImportSubOrd()">导入DD单</span> -->
                        <el-dialog :title="titleStr" :visible.sync="dialogVisibleImport" width="800px">
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4">
                                    备注:
                                </el-col>
                                <el-col :span="6">
                                    {{titleData.remark}}
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    外部订单号:
                                </el-col>
                                <el-col :span="6">
                                    {{titleData.external_sn}}
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4">
                                    <span class="redFont">*</span>子单单号:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="sub_order_sn" placeholder="请输入子单单号"></el-input>
                                </el-col>
                                <el-col :span="4" :offset="1">
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
                    <el-row style="margin-bottom: 10px;">
                        <el-row class="orderDt">
                            <el-col :span="6" :offset="1"><div><span>销售用户折扣：{{titleData.sale_user_account}}</span></div></el-col>
                            <el-col :span="6" :offset="1"><div><span>订单状态：{{titleData.status}}</span></div></el-col>
                            <el-col :span="6" :offset="1">
                                <div>
                                    <span>
                                        是否拆分
                                        <template>
                                            <el-popover  placement="top-start" width="200" trigger="click"
                                                content="根据当前实时库存将子单分为现货单和需求单">
                                                <i class="el-icon-question redFont" slot="reference"></i>
                                            </el-popover>
                                        </template>
                                        ：{{titleData.submenu_desc}}
                                    </span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="orderDt">
                            <el-col :span="6" :offset="1"><div><span>总单单号：<span class="notBgButton" @click="viewDetails(titleData.mis_order_sn)">{{titleData.mis_order_sn}}</span></span></div></el-col>
                            <el-col :span="6" :offset="1">
                                <div>
                                    <i class="el-icon-edit notBgButton" v-if="titleData.status!='DD'" @click="dialogVisibleTime=true;subMark=titleData.entrust_time"></i>
                                    <!-- 修改交付日期 -->
                                    <el-dialog title="修改交付日期" :visible.sync="dialogVisibleTime" width="800px">
                                        <el-row class="lineHeightForty MB_twenty">
                                            <el-col :span="4" :offset="1"><div class="">交付日期：</div></el-col>
                                            <el-col :span="6">
                                                <div>
                                                    <el-date-picker class="w_ratio" v-model="subMark" v-validate="'required'" name="entrust_time" value-format="yyyy-MM-dd" type="date" placeholder="请选择交付日期"></el-date-picker>
                                                    <span v-show="errors.has('entrust_time')" class="text-style redFont" v-cloak> {{ errors.first('entrust_time') }} </span>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <span slot="footer" class="dialog-footer">
                                            <el-button type="info" @click="dialogVisibleTime = false">取 消</el-button>
                                            <el-button type="primary" @click="modifyRemark(3)">确 定</el-button>
                                        </span>
                                    </el-dialog>
                                    <span>交付日期：{{titleData.entrust_time}}</span>
                                </div>
                            </el-col>
                            <el-col :span="6" :offset="1">
                                <div>
                                    <i class="el-icon-edit notBgButton" v-if="titleData.status!='DD'" @click="dialogVisibleMark=true;subMark=titleData.remark"></i>
                                    <!-- 修改子单备注 -->
                                    <el-dialog title="修改子单备注" :visible.sync="dialogVisibleMark" width="800px">
                                        <el-row class="lineHeightForty MB_twenty">
                                            <el-col :span="4" :offset="1"><div class="">子单备注：</div></el-col>
                                            <el-col :span="6">
                                                <div>
                                                    <el-input v-model="subMark" v-validate="'required'" name="subMark" placeholder="请输入子单备注"></el-input>
                                                    <span v-show="errors.has('subMark')" class="text-style redFont" v-cloak> {{ errors.first('subMark') }} </span>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <span slot="footer" class="dialog-footer">
                                            <el-button type="info" @click="dialogVisibleMark = false">取 消</el-button>
                                            <el-button type="primary" @click="modifyRemark(1)">确 定</el-button>
                                        </span>
                                    </el-dialog>
                                    <span>备注：{{titleData.remark}}</span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="orderDt">
                            <el-col :span="6" :offset="1"><div><span>子订单号：{{titleData.sub_order_sn}}</span></div></el-col>
                            <el-col :span="6" :offset="1"><div><span>创建日期：{{titleData.create_time}}</span></div></el-col>
                            <el-col :span="6" :offset="1">
                                <div>
                                    <i class="el-icon-edit notBgButton" v-if="titleData.status!='DD'" @click="dialogVisibleExternal=true;subMark=titleData.external_sn"></i>
                                    <!-- 修改外部订单号 -->
                                    <el-dialog title="修改子单外部订单号" :visible.sync="dialogVisibleExternal" width="800px">
                                        <el-row class="lineHeightForty MB_twenty">
                                            <el-col :span="4" :offset="1"><div class="">外部订单号：</div></el-col>
                                            <el-col :span="6">
                                                <div>
                                                    <el-input v-model="subMark" v-validate="'required'" name="external_sn" placeholder="请输入外部订单号"></el-input>
                                                    <span v-show="errors.has('external_sn')" class="text-style redFont" v-cloak> {{ errors.first('external_sn') }} </span>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <span slot="footer" class="dialog-footer">
                                            <el-button type="info" @click="dialogVisibleExternal= false">取 消</el-button>
                                            <el-button type="primary" @click="modifyRemark(2)">确 定</el-button>
                                        </span>
                                    </el-dialog>
                                    <span>外部订单号：{{titleData.external_sn}}</span>
                                </div>
                            </el-col>
                         </el-row>
                         <el-row class="orderDt">
                             <el-col :span="6" :offset="1"><div><span>sku数：{{titleData.sku_num}}</span></div></el-col>
                         </el-row>
                    </el-row>
                    <div style="margin-bottom: 10px;">
                        <span class="upTitle">商品信息：</span>
                    </div>
                    <el-row class="orderDt">
                        <el-col :span="10">
                            <span class="bgButton" @click="selectcol()">选择列</span>
                        </el-col>
                        <el-col :span="14" class="fontRight">
                            <span class="bgButton" v-if="titleData.status=='BD'" @click="submitDialogVisible=true;">提交DD数据</span>
                            <!-- 去分单 -->
                            <span class="bgButton" v-if="isNotSplit&&titleData.submenu_desc=='未分单'" @click="toSplit()">
                                生成现货单和需求单
                            </span>
                        </el-col>
                    </el-row>
                    <el-row>
                        <div class="t_i MB_twenty">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                        <thead>
                                            <tr>
                                                <th class="ellipsis" v-if="selectTitle('商品名称')" style="width:200px;">商品名称</th>
                                                <th style="width:150px" v-if="selectTitle('商家编码')">商家编码</th>
                                                <th style="width:150px" v-if="selectTitle('商品规格码')">商品规格码</th>
                                                <th style="width:150px" v-if="selectTitle('商品参考码')">商品参考码</th>
                                                <th v-if="selectTitle('商品代码')" style="width:160px">商品代码</th>
                                                <th style="width:150px" v-if="selectTitle('平台条码')">平台条码</th>
                                                <th style="width:130px" v-if="selectTitle('是否为套装')">是否为套装</th>
                                                <th style="width:130px" v-if="selectTitle('套装货号')">套装货号</th>
                                                <th style="width:130px" v-if="selectTitle('套装美金原价')">套装美金原价</th>
                                                <th style="width:130px" v-if="selectTitle('是否可以搜到')">是否可以搜到</th>
                                                <th style="width:80px" v-if="selectTitle('需求量')">需求量</th>
                                                <th style="width:110px" v-if="selectTitle('实时库存量')">实时库存量</th>
                                                <th style="width:80px" v-if="selectTitle('现货数量')">现货数量</th>
                                                <th style="width:80px" v-if="selectTitle('DD数量')">DD数量</th>
                                                <th style="width:120px" v-if="selectTitle('待采数量')">
                                                    待采数量
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="在已分单之前需求量-当前库存量，如果为负数则显示为0，会随库存而变化，
                                                            已分单之后该值等于预判采购量">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:80px" v-if="selectTitle('常备数量')">常备数量</th>
                                                <th style="width:130px" v-if="selectTitle('预判采购量')">
                                                    预判采购量
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="采购部预先判断的数量,默认值为生成总单时的待采数量，不随库存而变化">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:130px" v-if="selectTitle('待锁库数量')">
                                                    待锁库数量
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="该值是实际在ERP锁库的数量">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:130px" v-if="selectTitle('YD销售折扣')">
                                                    YD销售折扣
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="上传总单时的默认销售折扣">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:130px" v-if="selectTitle('BD销售折扣')">
                                                    BD销售折扣
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="总单商品报价时所确定下来的销售折扣">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:130px" v-if="selectTitle('DD销售折扣')">
                                                    DD销售折扣
                                                    <template>
                                                        <el-popover  placement="top-start" width="200" trigger="click"
                                                            content="DD单上传时的销售折扣">
                                                            <i class="el-icon-question redFont" slot="reference"></i>
                                                        </el-popover>
                                                    </template>
                                                </th>
                                                <th style="width:100px" v-if="selectTitle('美金原价($)')">美金原价($)</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <tbody>
                                        <tr v-for="(item,index) in tableData">
                                            <td class="overOneLinesHeid fontLift" v-if="selectTitle('商品名称')" style="width:200px;">
                                                <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                                <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                                </el-tooltip>
                                            </td>
                                            <td v-if="selectTitle('商家编码')" style="width:150px">{{item.erp_merchant_no}}</td>
                                            <td v-if="selectTitle('商品规格码')" style="width:150px">{{item.spec_sn}}</td>
                                            <td v-if="selectTitle('商品参考码')" style="width:150px">{{item.erp_ref_no}}</td>
                                            <td v-if="selectTitle('商品代码')" style="width:160px">{{item.erp_prd_no}}</td>
                                            <td class="ellipsis" v-if="selectTitle('平台条码')" style="width:150px">
                                                <!-- {{item.platform_barcode}} -->
                                                <el-tooltip class="item" effect="light" :content="item.platform_barcode" placement="top">
                                                    <span style="-webkit-box-orient: vertical;width:150px;">{{item.platform_barcode}}</span>
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
                                            <td v-if="selectTitle('需求量')" style="width:80px">{{item.goods_number}}</td>
                                            <td v-if="selectTitle('实时库存量')" style="width:110px">{{item.gStockNum}}</td>
                                            <td v-if="selectTitle('现货数量')" class="DDcash" style="width:80px">
                                                <span v-if="titleData.status=='DD'">{{item.cash_num}}</span>
                                                <span v-else>
                                                    <input style="width:60px" :name="index" @change="modifyDDSubData(item.spec_sn,index,'1')" :value="item.cash_num"/>
                                                </span>
                                            </td>
                                            <td v-if="selectTitle('DD数量')" class="DDnum" style="width:80px">
                                                <span v-if="titleData.status=='DD'">{{item.dd_num}}</span>
                                                <span v-else>
                                                    <input style="width:60px" :name="index" @change="modifyDDSubData(item.spec_sn,index,'2')" :value="item.dd_num"/>
                                                </span>
                                            </td>
                                            <td v-if="selectTitle('待采数量')" style="width:120px">
                                                <span v-if="titleData.is_submenu != 1">
                                                    <span v-if="item.buy_num < 0">0</span>
                                                    <span v-else>{{item.buy_num}}</span>
                                                </span> 
                                                <span v-if="titleData.is_submenu == 1">{{item.wait_buy_num}}</span>
                                            </td>
                                            <td v-if="selectTitle('常备数量')" style="width:80px">{{item.standby_num}}</td>
                                            <td v-if="selectTitle('预判采购量')" class="DDsuoku" style="width:130px">
                                                <span v-if="titleData.status=='DD'">{{item.wait_buy_num}}</span>
                                                <span v-else>
                                                    <input style="width:100px" :name="index" @change="modifyDDSubData(item.spec_sn,index,'4')" :value="item.wait_buy_num"/>
                                                </span>
                                            </td>
                                            <td v-if="selectTitle('待锁库数量')" style="width:130px">
                                                {{item.wait_lock_num}}
                                            </td>
                                            <td v-if="selectTitle('YD销售折扣')" style="width:130px">{{item.sale_discount}}</td>
                                            <td v-if="selectTitle('BD销售折扣')" style="width:130px">{{item.bd_sale_discount}}</td>
                                            <td v-if="selectTitle('DD销售折扣')" class="DDdiscount" style="width:130px">
                                                <span v-if="titleData.status=='DD'">{{item.dd_sale_discount}}</span>
                                                <span v-else>
                                                    <input style="width:100px" :name="index" @change="modifyDDSubData(item.spec_sn,index,'3')" :value="item.dd_sale_discount"/>
                                                </span>
                                            </td>
                                            <td v-if="selectTitle('美金原价($)')" style="width:100px">{{item.spec_price}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </el-row>
                    <!-- 修改带锁库数量 -->
                    <el-dialog title="修改锁库数量" :visible.sync="dialogVisibleNum" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4" :offset="1"><div class="">锁库数量：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="lockStorNum" placeholder="请输入锁库数量"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleNum = false">取 消</el-button>
                            <el-button type="primary" @click="dialogVisibleNum = false;modifyLockStorNum()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <!-- 提交DD数据 -->
                    <el-dialog title="提交DD数据" :visible.sync="submitDialogVisible" width="800px">
                        <el-row class="MB_twenty MT_twenty">
                            <el-col :span="4" :offset="1"><div class="MT_ten"><span class="redFont">*</span>外部订单号：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="external_sn" v-validate="'required'" name="external_sn" placeholder="请输入外部订单号"></el-input>
                                    <span v-show="errors.has('external_sn')" class="text-style redFont" v-cloak> {{ errors.first('external_sn') }} </span>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="MT_ten">备注：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="remark" placeholder="请输入备注"></el-input>
                                    <!-- <span v-show="errors.has('remark')" class="text-style redFont" v-cloak> {{ errors.first('remark') }} </span> -->
                                </div>
                            </el-col>
                        </el-row>
                        <span class="redFont ML_twenty">注：提交DD数据后将不能再导入DD单，请确认是否提交DD数据！</span>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="submitDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="submitSubOrd()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                </el-col>
            </div>
      </el-col>
      <selectCol :selectStr="selectStr" @selectTitle="selectTitle"></selectCol>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { tableWidth } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import selectCol from '@/components/UiAssemblyList/selectCol'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';

export default {
    components:{
        customConfirmationBoxes,
        selectCol,
        fontWatermark,
    },
  data(){
    return{
        url: `${this.$baseUrl}`,
        tableData:[],
        titleData:'',//title数据
        search:'',//用户输入数据
        total:0,//页数默认为0
        saleMarRate:'',
        spec_sn:'',
        radio:2,
        index:'',
        Cost:'',
        fileName:'',
        isShow:false,
        dialogVisibleImport:false,
        headersStr:'',
        isNotSplit:false,
        sub_order_sn:'',
        contentStr:"请您确认是否要生成现货单和需求单！",
        dialogVisibleNum:false,
        lockStorNum:'',
        specSn:'',
        dialogVisibleMark:false,
        subMark:'',
        // remark:'',
        // external_sn:'', 
        titleStr:"导入DD单 子单号:"+`${this.$route.query.sub_order_sn}`,
        //选择列
        selectStr:['商品名称','商家编码','商品规格码','商品参考码','商品代码','平台条码','需求量','实时库存量','现货数量','常备数量','是否为套装','套装货号','套装美金原价','是否可以搜到','DD数量','待采数量','预判采购量','待锁库数量','YD销售折扣','BD销售折扣','DD销售折扣','美金原价($)'],
        //提交DD数据
        submitDialogVisible:false,
        remark:'',
        external_sn:'',
        //修改外部订单号
        dialogVisibleExternal:false,
        //修改交付日期
        entrust_time:'',
        dialogVisibleTime:false,
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
      this.getMISOrderDatailsData();
  },
  methods:{
    getMISOrderDatailsData(){
        let vm = this;
        vm.$store.commit('selectList', this.selectStr);
        axios.post(vm.url+vm.$getSubDetailURL,
            {
                "sub_order_sn":vm.$route.query.sub_order_sn
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.subOrderDetail;
            vm.titleData=res.data.subOrderInfo;
            vm.external_sn=vm.titleData.external_sn;
            vm.remark=vm.titleData.remark;
            if(vm.titleData.status=='DD'){
                vm.isNotSplit=true;
            }else{
                vm.isNotSplit=false;
            }
            if(parseInt(vm.tableData.length)>15){
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
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //点击打开生成现货单和需求单按钮
    toSplit(e){
        $(".confirmPopup_b").fadeIn();
    },
    //确认生成现货单和需求单按钮
    confirmationAudit(){
        let vm = this;
        $(".confirmPopup_b").hide();
        axios.post(vm.url+vm.$subOrdSubmenuURL,
            {
                "sub_order_sn":vm.$route.query.sub_order_sn
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
    //关掉确认生成现货单和需求单按钮弹出框
    determineIsNo(){
        $(".confirmPopup_b").fadeOut();
    },
    //导出订单 
    exportSubOrd(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$exportSubOrdURL+'?sub_order_sn='+vm.$route.query.sub_order_sn+'&token='+headersToken);
    },
    //打开上传弹框 
    openImportSubOrd(){
        let vm = this;
        $("#file1").val('');
        vm.fileName='';
        vm.sub_order_sn='';
        vm.remark='';
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
        let Str=(vm.sub_order_sn!=vm.$route.query.sub_order_sn);
        if(vm.sub_order_sn==''||Str){
            vm.$message('请检查子单单号！');
            return false;
        }
        if(vm.fileName==''){
            vm.$message('请选择文件后上传！');
            return false;
        }
        // if(vm.external_sn==''){
        //     vm.$message('请检查外部订单号！'); 
        //     return false;
        // }
        vm.dialogVisibleImport = false;
        var formDate = new FormData($("#forms")[0]);
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$importSubOrdURL+"?sub_order_sn="+vm.sub_order_sn+"&remark="+vm.remark,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            vm.dialogVisibleImport = false;
            if(res.code==2024){
                vm.getMISOrderDatailsData();
                vm.$message(res.msg);
            }else{
                vm.$message(res.msg);
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
    viewDetails(mis_order_sn){
        let vm = this;
        this.$router.push('/MISorderListDetails?mis_order_sn='+mis_order_sn+'&sub_order_sn='+vm.$route.query.sub_order_sn+'&isGetSubDetail=GetSubDetail');
    },
    //回上一级页面
    backUpPage(){
        let vm=this;
        let urlParam = '';
        if(vm.$route.query.mis_order_sn!=undefined){
            urlParam='&mis_order_sn='+vm.$route.query.mis_order_sn
        }
        if(vm.$route.query.status!=undefined){
            urlParam+='&status='+vm.$route.query.status
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            urlParam+='&erp_merchant_no='+vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.sub_order!=undefined){
            urlParam+='&sub_order='+vm.$route.query.sub_order
        }
        if(vm.$route.query.goods_name!=undefined){
            urlParam+='&goods_name='+vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            urlParam+='&goods_name='+vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            urlParam+='&sale_user_id='+vm.$route.query.sale_user_id
        }
        if(vm.$route.query.page!=undefined){
            urlParam+='&page='+vm.$route.query.page
        }
        if(vm.$route.query.isMisSub=='isMisSub'){
            vm.$router.push('/misSubOrderList?mis_order_sn='+vm.$route.query.mis_order_sn);  
        }else{
            vm.$router.push('/getSubList?isSubD=isSubD'+urlParam);
        }  
    },
    //修改锁库数量
    modifyLockStorNum(){
        let vm = this;
        if(vm.lockStorNum==''){
            vm.$message('请填写锁库数量！');
            return false;
        }else if(vm.lockStorNum<=0){
            vm.$message('锁库数量不能小于或等于0！');
            return false;
        }
        axios.post(vm.url+vm.$modifyWaitLockNumURL,
            {
                "sub_order_sn":vm.$route.query.sub_order_sn,
                "spec_sn":vm.specSn,
                "wait_lock_num":vm.lockStorNum,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.getMISOrderDatailsData();
            vm.$message(res.data.msg);
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //修改子单备注 
    modifyRemark(e){
        let vm = this;
        // if(vm.subMark==''){
        //     vm.$message('请填写子单备注后进行提交！'); 
        //     return false;
        // }
        this.$validator.validateAll().then(valid => {
            if(valid){
                vm.dialogVisibleMark = false;
                vm.dialogVisibleExternal= false;
                vm.dialogVisibleTime = false;
                axios.post(vm.url+vm.$modifyRemarkURL,
                    {
                        "sub_order_sn":vm.$route.query.sub_order_sn,
                        "value":vm.subMark,
                        "type":e,
                    },
                    {
                        headers:vm.headersStr,
                    }
                ).then(function(res){
                    if(res.data.code==2024){
                        vm.getMISOrderDatailsData();
                    }
                    vm.$message(res.data.msg);
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
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    //提交DD数据
    submitSubOrd(){
        let vm = this;
        this.$validator.validateAll().then(valid => {
            if(valid){
                vm.submitDialogVisible = false;
                axios.post(vm.url+vm.$submitDDataURL,
                    {
                        "sub_order_sn":vm.$route.query.sub_order_sn,
                        "external_sn":vm.external_sn,
                        "remark":vm.remark,
                    },
                    {
                        headers:vm.headersStr,
                    }
                ).then(function(res){
                    if(res.data.code=='2024'){
                        vm.getMISOrderDatailsData()
                    }
                    vm.$message(res.data.msg);
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
    //修改dd子单数据 
    modifyDDSubData(spec_sn,index,state){
        let vm = this;
        let Inputval;
        if(state=='1'){
            Inputval = $(".DDcash input[name="+index+"]").val();
        }else if(state=='2'){
            Inputval = $(".DDnum input[name="+index+"]").val();
        }else if(state=='3'){
            Inputval = $(".DDdiscount input[name="+index+"]").val();
        }else if(state=='4'){
            Inputval = $(".DDsuoku input[name="+index+"]").val();
        }
        if(Inputval==''){
           return false;
        }
        axios.post(vm.url+vm.$modifyDDSubDataURL,
            {
                "sub_order_sn":vm.$route.query.sub_order_sn,
                "spec_sn":spec_sn,
                "type":state,
                "value":Inputval
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=="2024"){
                vm.getMISOrderDatailsData();
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
       return selectTitle(checkedCities);
    },
    //表格宽度计算
    tableWidth(){
        return tableWidth(this.$store.state.select);
    },
  }
}
</script>

<style>
.getSubDetail .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
.getSubDetail .bg-purple{
    line-height: 40px;
}
.orderDt{
    color: #a7a7a7;
    height: 50px;
    line-height: 50px;
}
.getSubDetail .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ebeef5; border-left:1px solid #ebeef5}
.getSubDetail .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ebeef5; width:305px; height:43px}
.getSubDetail .t_number{border-right:1px solid #ebeef5; width:100%; margin-bottom:5px}
.getSubDetail .t_number td{border-bottom:1px solid #ebeef5;width: 293px;height: 40px; text-align:center}
.getSubDetail .dd{ overflow-y:hidden;}
.getSubDetail .t_i{width:100%; height:auto; border-right:1px solid #ebeef5;border-left: 1px solid #ebeef5;border-top:1px solid #ebeef5}
.getSubDetail .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.getSubDetail .ee{width:100%!important; width:100%; text-align: center;}
.getSubDetail .t_i_h table{width:1600px;}
.getSubDetail .t_i_h table td{ border-bottom:1px solid #ebeef5; height:20px; text-align:center}
.getSubDetail .cc{width:100.6%;height: 600px; border-bottom:1px solid #ebeef5; background:#fff; overflow:auto;}
.getSubDetail .cc table{width:1600px;}
.getSubDetail .cc table td{ text-align:center}
.getSubDetail .file {
    position: relative;
    display: inline-block;
    text-align: center;
}
.getSubDetail .file img{
  padding-top: 20px;
}
.getSubDetail .file span{
  display: inline-block;
  font-size: 15px;
}
.getSubDetail .file input {
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;
}
.getSubDetail .ellipsis span{
    /* line-height: 23px; */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
