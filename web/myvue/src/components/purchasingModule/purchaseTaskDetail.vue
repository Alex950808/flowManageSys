<template>
  <div class="purchaseTaskDetail">
        <el-row>
            <el-col :span="24" style="background-color: #fff;">
                <el-row class="bgDiv">
                    <el-col :span="22" :offset="1">
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                            <el-col :span="2">
                                <router-link  to='/purchaseTaskList'><span @click="backUpPage()" class="bgButton">返回上一级</span></router-link>
                            </el-col>
                            <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>采购任务详情</span></div></el-col>
                            <el-col :span="2"><div>商品名称</div></el-col>
                            <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                            <el-col :span="2"><div>商品规格码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                            <el-col :span="2"><div>商家编码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                            <el-col :span="3"><span class="bgButton" @click="searchFrame()">搜索</span></el-col>
                        </el-row>
                        <el-row class="MT_twenty fontRight MB_ten lineHeightForty select">
                            <span class="bgButton" v-if="isUploadSumDemandGoodsInfo" @click="dialogVisibleDistribution=true;cleanData()">上传汇总单的分配数据</span>
                            <span class="bgButton UpDataDis" v-if="isDownLoadSumDemandGoodsInfo" @click="dialogVisibleUpDataDis=true;open_d_table('1');cleanData()">下载汇总单的待分配数据</span>
                            <span class="bgButton sameDayD" @click="openBox();cleanData()">采购任务下载</span>
                            <span class="bgButton" @click="dialogVisibleUpData=true;channelMethodList();cleanData()">采购数据上传</span>
                        </el-row>
                        <el-dialog title="采购数据上传" :visible.sync="dialogVisibleUpData" width="800px">
                            <el-row class="MB_twenty">
                                <el-col :span="4">
                                    <div class="MT_ten"><span class="redFont">*</span>采购方式：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="method_id" @change="methodChangeSelect()" v-validate="'required'" name="methodName" placeholder="选择方式">
                                            <el-option v-for="item in methodInfo" :key="item.method_id" :label="item.method_name" :value="item.method_id">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <div class="MT_ten"><span class="redFont">*</span>采购渠道：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="channel" v-validate="'required'" name="channelName" placeholder="选择渠道">
                                            <el-option v-for="item in channelInfo" :key="item.channels_id" :label="item.channels_name" :value="item.channels_id">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row class="MB_twenty">
                                <el-col :span="4">
                                    <div class="MT_ten"><span class="redFont">*</span>自提/邮寄：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="mail" v-validate="'required'" name="mail" placeholder="自提/邮寄">
                                            <el-option v-for="item in mails" :key="item.value" :label="item.label" :value="item.mail">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <div class="MT_ten"><span class="redFont">*</span>采购编号id：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="ID" v-validate="'required'" name="ID" placeholder="请选择采购编号id">
                                            <el-option v-for="item in userList" :key="item.userId" :label="item.userName" :value="item.userId">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row class="MB_twenty">
                                <el-col :span="4">
                                    <div class="MT_ten"><span class="redFont">*</span>港口id：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="port" v-validate="'required'" name="port" placeholder="选择港口">
                                            <el-option v-for="item in ports" :key="item.portId" :label="item.portName" :value="item.portId">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <div class="MT_ten"><span class="redFont">*</span>供应商id：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="supplier_id" v-validate="'required'" name="supplier" placeholder="选择供应商">
                                            <el-option v-for="item in suppliers" :key="item.supplierId" :label="item.supplierName" :value="item.supplierId">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row class="MB_twenty">
                                <el-col :span="4">
                                    <div class="MT_ten"><span class="redFont">*</span>提货时间：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <div>
                                            <el-date-picker v-model="delivery_time" style="width:100%;" v-validate="'required'" name="deliveryTime" value-format="yyyy-MM-dd" type="date" placeholder="设置提货日"></el-date-picker>
                                        </div>
                                    </template>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <div class="MT_ten"><span class="redFont">*</span>到货时间：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <div>
                                            <el-date-picker v-model="arrive_time" style="width:100%;" v-validate="'required'" name="arriveTime" value-format="yyyy-MM-dd" type="date" placeholder="设置到货日"></el-date-picker>
                                        </div>
                                    </template>
                                </el-col>
                            </el-row>

                            <el-row class="MB_twenty">
                                <el-col :span="4">
                                    <div class="MT_ten"><span class="redFont">*</span>任务模板：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="task_id" v-validate="'required'" name="taskId" placeholder="选择任务模板">
                                            <el-option v-for="item in taskInfoS" :key="item.taskId" :label="item.taskName" :value="item.taskId">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <div class="MT_ten">预计返积分日期：</div>
                                </el-col>
                                <el-col :span="6">
                                    <el-date-picker v-model="integral_time" style="width:100%;" value-format="yyyy-MM-dd" type="date" placeholder="设置提货日"></el-date-picker>
                                </el-col>
                            </el-row>
                            <el-row class="MB_twenty">
                                <el-col :span="4">
                                    <div class="MT_ten"><span class="redFont">*</span>购买时间：</div>
                                </el-col>
                                <el-col :span="6">
                                    <template>
                                        <div>
                                            <el-date-picker v-model="buy_time" style="width:100%;" v-validate="'required'" name="buyTime" value-format="yyyy-MM-dd" type="date" placeholder="请选择购买时间"></el-date-picker>
                                        </div>
                                    </template>
                                </el-col>
                                <el-col :span="4" :offset="1"><div class="MT_ten"><span class="redFont">*</span>上传文件：</div></el-col>
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
                                <el-button type="info" @click="dialogVisibleUpData = false;">取 消</el-button>
                                <el-button type="primary" @click="purchaseDataUp()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <span class="bgButton MR_twenty" @click="selectcol()">选择列</span>
                        <span class="MR_twenty">汇总需求单号：{{`${this.$route.query.sum_demand_sn}`}}</span>
                        <span class="MR_twenty">sku数：{{`${this.$route.query.sku_num}`}}</span>
                        <span class="MR_twenty">需求数：{{`${this.$route.query.goods_num}`}}</span>
                        <span class="MR_twenty">可分配数：{{`${this.$route.query.allot_num}`}}</span>
                        <div class="d_I_B floatRight">
                            <span class="quanbu bgButton B_B_G" @click="integralSpan('1')">
                                全部数据
                                <template>
                                    <el-popover  placement="top-start" width="250" trigger="hover">
                                        <p>全部数据：整个合单的采购任务数据</p>
                                        <i class="el-icon-question redFont" slot="reference"></i>
                                    </el-popover>
                                </template>
                            </span>
                            <span class="quekou bgButton G_B_G" @click="integralSpan('2')">
                                缺口数据
                                <template>
                                    <el-popover  placement="top-start" width="260" trigger="hover">
                                        <p>缺口数据：合单中未分配采购任务的数据</p>
                                        <i class="el-icon-question redFont" slot="reference"></i>
                                    </el-popover>
                                </template>
                            </span>
                        </div>
                        <el-row class="t_i fontCenter MT_ten">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                        <thead>
                                            <tr>
                                                <th v-if="selectTitle('商品名称')" style="width:200px" rowspan="4">商品名称</th>
                                                <th v-if="selectTitle('商品规格码')" style="width:160px" rowspan="4">商品规格码</th>
                                                <th v-if="selectTitle('商家编码')" style="width:160px" rowspan="4">商家编码</th>
                                                <th v-if="selectTitle('商品代码')" style="width:180px" rowspan="4">商品代码</th>
                                                <th v-if="selectTitle('商品参考码')" style="width:180px" rowspan="4">商品参考码</th>
                                                <th v-if="selectTitle('需求数')" style="width:100px" rowspan="4">需求数</th>
                                                <th v-if="selectTitle('可分配数量')" style="width:100px" rowspan="4">可分配数量</th>
                                                <th v-if="selectTitle('实采数')" style="width:90px" rowspan="4">实采数</th>
                                                <th v-if="selectTitle('采购缺口数')" style="width:100px" rowspan="4">采购缺口数</th>
                                                <th v-for="item in sale_user_list" style="width:200px" v-if="selectTitle('需求单分布')">{{item}}</th>
                                                <th v-for="(item,index) in channelTitleList" :colspan="item.num" :class="'channelTitle'+index">{{item.name}}</th>
                                            </tr>
                                            <tr>
                                                <th v-for="item in expire_time_list" style="width:200px" v-if="selectTitle('需求单分布')">{{item}}</th>
                                                <th v-for="(item,index) in channelNameList" v-if="selectTitle(item.name)" :style="aaaa(item.className,index)" :class="item.className" :colspan="item.num">
                                                    {{item.name}}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th v-for="item in demandList" style="width:200px" v-if="selectTitle('需求单分布')">{{item}}</th>
                                                <th v-for="item in titleThList" v-if="selectTitle(item.channels_name)"  rowspan="2" :style="aaaa(item.className,item.index)" :class="['overOneLinesHeid',item.className]" style="width:100px" :title="item.DPinfo">
                                                    <span style="-webkit-box-orient: vertical;">{{item.DPinfo}}</span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th v-for="item in demandList" style="width:200px" v-if="selectTitle('需求单分布')">
                                                    <span style="width:50px;display:inline-block;">需求数</span>
                                                    <span>|</span>
                                                    <span style="width:50px;display: inline-block">实采数</span>
                                                    <span>|</span>
                                                    <span style="width:50px;display: inline-block">缺口数</span>
                                                </th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc MB_twenty" id="cc" @scroll="scrollEvent()"> 
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth">
                                    <tr v-for="(item,index) in tableData">
                                        <td v-if="selectTitle('商品名称')" class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                            <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                        </td>
                                        <td v-if="selectTitle('商品规格码')" class="overOneLinesHeid" style="width:160px">
                                            <span style="-webkit-box-orient: vertical;width:160px">{{item.spec_sn}}</span>
                                        </td>
                                        <td v-if="selectTitle('商家编码')" class="overOneLinesHeid" style="width:160px">
                                            <span style="-webkit-box-orient: vertical;width:160px">{{item.erp_merchant_no}}</span>
                                        </td>
                                        <td v-if="selectTitle('商品代码')" class="overOneLinesHeid" style="width:180px">
                                            <span style="-webkit-box-orient: vertical;width:180px">{{item.erp_prd_no}}</span>
                                        </td>
                                        <td v-if="selectTitle('商品参考码')" class="overOneLinesHeid" style="width:180px">
                                            <span style="-webkit-box-orient: vertical;width:180px">{{item.erp_ref_no}}</span>
                                        </td>
                                        <td v-if="selectTitle('需求数')" style="width:100px">{{item.goods_num}}</td>
                                        <td v-if="selectTitle('可分配数量')" style="width:100px">{{item.allot_num}}</td>
                                        <td v-if="selectTitle('实采数')" style="width:90px">{{item.real_num}}</td>
                                        <td v-if="selectTitle('采购缺口数')" style="width:100px">{{item.diff_num}}</td>
                                        <td v-for="demandSn in demandList" style="width:200px" v-if="selectTitle('需求单分布')">
                                            <span v-if="item.demand_info[demandSn]!=undefined" style="width:50px;display: inline-block">{{item.demand_info[demandSn].goods_num}}</span>
                                            <span v-if="item.demand_info[demandSn]!=undefined">|</span>
                                            <span v-if="item.demand_info[demandSn]!=undefined" style="width:50px;display: inline-block">{{item.demand_info[demandSn].yet_num}}</span>
                                            <span v-if="item.demand_info[demandSn]!=undefined">|</span>
                                            <span v-if="item.demand_info[demandSn]!=undefined" style="width:50px;display: inline-block">{{item.demand_info[demandSn].diff_num}}</span>
                                        </td>
                                        <td v-for="(titleInfo,index) in channellist" v-if="selectTitle(titleInfo.channelsName)" :style="aaaa(titleInfo.className,titleInfo.index)" :class="titleInfo.className" style="width:100px;">
                                            <span v-if="item.channels_info[titleInfo.channelsName]!=undefined">
                                                <span v-if="titleInfo.DPinfo=='最终折扣'">
                                                    <span class="sort">{{item.channels_info[titleInfo.channelsName][titleInfo.DPinfo].sort}}</span>
                                                    &nbsp;&nbsp;<span class="disount">{{item.channels_info[titleInfo.channelsName][titleInfo.DPinfo].discount}}</span>
                                                </span>
                                                <span v-if="titleInfo.DPinfo!='最终折扣'">
                                                    {{item.channels_info[titleInfo.channelsName][titleInfo.DPinfo]}}
                                                </span>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td v-if="selectTitle('商品名称')" style="width:200px;">合计</td>
                                        <td v-if="selectTitle('商品规格码')" style="width:160px"></td>
                                        <td v-if="selectTitle('商家编码')" style="width:160px"></td>
                                        <td v-if="selectTitle('商品代码')" style="width:180px"></td>
                                        <td v-if="selectTitle('商品参考码')" style="width:180px"></td>
                                        <td v-if="selectTitle('需求数')" style="width:100px"></td>
                                        <td v-if="selectTitle('可分配数量')" style="width:100px"></td>
                                        <td v-if="selectTitle('实采数')" style="width:90px"></td>
                                        <td v-if="selectTitle('采购缺口数')" style="width:100px"></td>
                                        <td v-for="demandSn in demandList" style="width:200px" v-if="selectTitle('需求单分布')">{{dg_goods_num[demandSn]}}</td>
                                        <td v-for="(titleInfo,index) in channellist" v-if="selectTitle(titleInfo.channelsName)" :style="aaaa(titleInfo.className,titleInfo.index)" :class="titleInfo.className" style="width:100px;">
                                            <span v-if="titleInfo.DPinfo=='可采数'">
                                                {{cm_goods_num[titleInfo.channelsName]}}
                                            </span>
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
        <el-dialog title="上传分配数据" :visible.sync="dialogVisibleDistribution" width="800px">
            <el-row class="lineHeightForty MB_twenty">
                <el-col :span="4"><div><span class="redFont">*</span>采购数据类型：</div></el-col>
                <el-col :span="6">
                    <div>
                        <template>
                            <el-select v-model="dataType" filterable placeholder="请选择采购数据类型">
                                <el-option v-for="item in dataTypeS" :key="item.type" :label="item.label" :value="item.type"></el-option>
                            </el-select>
                        </template>
                    </div>
                </el-col>
                <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>请选择导入文件：</div></el-col>
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
            <div slot="footer" class="dialog-footer fontCenter">
                <el-button type="info" @click="dialogVisibleDistribution = false">取 消</el-button>
                <el-button type="primary" @click="GFFconfirmUpData()">确 定
                </el-button>
            </div>
        </el-dialog>
        <el-dialog :title="titleStr" :visible.sync="dialogVisibleUpDataDis" width="800px">
            <el-row class="lineHeightForty MB_twenty">
                <el-col :span="4"><div><span class="redFont">*</span>采购数据类型：</div></el-col>
                <el-col :span="6">
                    <div>
                        <template>
                            <el-select v-model="dataType" filterable placeholder="请选择采购数据类型">
                                <el-option v-for="item in dataTypeS" :key="item.type" :label="item.label" :value="item.type"></el-option>
                            </el-select>
                        </template>
                    </div>
                </el-col>
            </el-row>
            <div slot="footer" class="dialog-footer fontCenter">
                <el-button type="info" @click="dialogVisibleUpDataDis = false;ifFalse('2')">取 消</el-button>
                <el-button type="primary" @click="d_table()">确 定</el-button>
            </div>
        </el-dialog>
        <el-dialog title="采购任务下载" :visible.sync="dialogVisibleSameDayD" width="800px">
            <el-row class="lineHeightForty MB_twenty">
                <el-col :span="4"><div><span class="redFont">*</span>采购数据类型：</div></el-col>
                <el-col :span="6">
                    <div>
                        <template>
                            <el-select v-model="sameDayType" filterable placeholder="请选择采购数据类型">
                                <el-option v-for="item in sameDayTypeS" :key="item.type" :label="item.label" :value="item.type"></el-option>
                            </el-select>
                        </template>
                    </div>
                </el-col>
            </el-row>
            <div slot="footer" class="dialog-footer fontCenter">
                <el-button type="info" @click="dialogVisibleSameDayD = false;ifFalse('1')">取 消</el-button>
                <el-button type="primary" @click="selectInterByType()">确 定</el-button>
            </div>
        </el-dialog>
        <successfulOperation :msgStr="msgStr"></successfulOperation>
        <operationFailed :msgStr="msgStr"></operationFailed>
      <!-- <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import selectCol from '@/components/UiAssemblyList/selectCol'
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import { exportsa } from '@/filters/publicMethods.js'
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
// import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
export default {
  components:{
      notFound,
      selectCol,
      operationFailed,
      successfulOperation,
    //   upDataButtonByBox, 
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      titleData:[],
      titleThList:[],
      channellist:[],
      demandList:[],
      channelNameList:[],
      channelTitleList:[],
      sale_user_list:[],
      expire_time_list:[],
      dg_goods_num:'',
      cm_goods_num:'',
      isShow:false,
      channelLength:0,
      //选择列
      selectStr:["商品名称","商品规格码","商家编码","商品代码","商品参考码","需求数","可分配数量","实采数","采购缺口数","需求单分布"],

      //采购数据上传 
      dialogVisibleUpData:false,
      //所有下拉选择
        channel:'',   //选择渠道
        channels:[],
        ID:'',   //选择ID
        port:'',//选择港口 
        ports:[],
        mail:'',  //自提或邮寄 
        mails:[],
        formDate:[],
        supplier_id:'',
        suppliers:[],
        taskInfoS:[],
        task_id:'',
        delivery_time:'',
        buy_time:'',
        arrive_time:'',
        fileName:'',
        sum_demand_sn:'',
        channelInfo:[],
        methodInfo:[],
        method_id:'',
        channels_list:[],
        userList:[],
        integral_time:'',
        //上传及下载的权限 
        JurisdictionData:[],
        isDownLoadSdgInfoNoSort:false,
        isDownLoadSumDemandGoodsInfo:false,
        isUploadSumDemandGoodsInfo:false,
        //上传汇总单的分配数据
        dialogVisibleDistribution:false,
        dataTypeS:[{'type':1,'label':'所有数据'},{'type':2,'label':'缺口数据'}],
        dataType:'1',
        //下载待分配数据有序/无序
        titleStr:'',
        dialogVisibleUpDataDis:false,
        disType:'',
        //下载当天采购任务
        sameDayTypeS:[{'type':1,'label':'所有数据'},{'type':2,'label':'当天任务'}],
        sameDayType:'',
        dialogVisibleSameDayD:false,

        msgStr:'',
        //搜索字段
        goods_name:'',
        spec_sn:'',
        erp_merchant_no:'',
        reTableData:[],
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.SelfLiftingOrmail();
  },
  methods:{
    getDataList(){
        let vm = this;
        vm.channellist.splice(0);
        vm.titleThList.splice(0);
        vm.selectStr.splice(10);
        vm.channelNameList.splice(0);
        vm.channelTitleList.splice(0);
        let purchaseTaskListSetat=sessionStorage.getItem("purchaseTaskListSetat")
        if(purchaseTaskListSetat=='1'){
            vm.JurisdictionData=JSON.parse(sessionStorage.getItem("purchaseTaskList"));
        }else if(purchaseTaskListSetat=='2'){
            vm.JurisdictionData=sessionStorage.getItem("purchaseTaskList");
        }
        if(vm.JurisdictionData!='undefined'){
            vm.JurisdictionData.forEach(element=>{
                if(element.name=='downLoadSdgInfoNoSort'){
                    vm.isDownLoadSdgInfoNoSort=true;
                }
                if(element.name=='downLoadSumDemandGoodsInfo'){
                    vm.isDownLoadSumDemandGoodsInfo=true;
                }
                if(element.name=='uploadSumDemandGoodsInfo'){
                    vm.isUploadSumDemandGoodsInfo=true;
                }
            })
        }
        let loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$purchaseTaskDetailURL+"?sum_demand_sn="+vm.$route.query.sum_demand_sn+"&data_type="+vm.dataType,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            loading.close();
            if(res.data.code=="1000"){
                vm.tableData=res.data.data.sum_demand_goods;
                vm.reTableData=res.data.data.sum_demand_goods;
                vm.isShow=false;
                vm.titleData = res.data.data.channel_arr;
                vm.demandList = res.data.data.demand_arr;
                vm.dg_goods_num = res.data.data.dg_goods_num;
                vm.cm_goods_num = res.data.data.cm_goods_num;
                vm.sale_user_list = res.data.data.sale_user_list;
                vm.expire_time_list = res.data.data.expire_time_list;
                let randomL = ["Aa","Bb","Cc","Dd","Ee","Ff","Gg","Hh","Ii","Jj","Kk","Ll","Mm","Nn","Oo","Po","Qq","Rr","Ss","Tt","Uu","Vv","Ww","Xx","Yy","Zz"]
                tableStyleByDataLength(vm.tableData.length,15);
                let i = 0;
                let j = 0;
                let v = 0;
                vm.titleData.forEach( channelInfo=>{
                    channelInfo.channel_info.forEach(element=>{
                        vm.channelNameList.push({"name":element.channels_name,"num":element.discount_type.length,"className":randomL[j]})
                        i+=element.discount_type.length
                        vm.selectStr.push(element.channels_name)
                        let channelsName = element.channels_name
                        element.discount_type.forEach(DPinfo=>{
                            v++;
                            vm.titleThList.push({"channels_name":element.channels_name,"DPinfo":DPinfo,"className":randomL[j],"index":j})
                            vm.channellist.push({channelsName,DPinfo,"className":randomL[j],"index":j})
                        })
                    j++;
                    })
                    vm.channelTitleList.push({"name":channelInfo.method_name,"num":i})
                    i=0;
                })
                vm.channelLength=v;
                vm.$store.commit('selectList', vm.selectStr);
            }else if(res.data.code=="1002"){
                vm.isShow=true;
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
    scrollEvent(){
        var a=document.getElementById("cc").scrollTop;
        var b=document.getElementById("cc").scrollLeft;
        document.getElementById("hh").scrollLeft=b;
    },
    selectcol(){
        $(".selectCol_b").fadeIn();
    },
    open_d_table(type){
        let vm = this;
        vm.disType=type;
        if(vm.disType==1){//下载汇总单的待分配数据 
            // $(event.target).addClass("G_B_G");
            // $(event.target).addClass("disable");
            vm.titleStr='下载汇总单的待分配数据'
        }else if(vm.disType==2){//下载汇总单的待分配数据(无序)
            vm.titleStr='下载汇总单的待分配数据(无序)'
        }
        vm.dialogVisibleUpDataDis=true;
    },
    ifFalse(index){
        let vm = this;
        if(index=='1'){
            $(".sameDayD").removeClass("G_B_G");
            $(".sameDayD").removeClass("disable");
        }else if(index=='2'){
            $(".UpDataDis").removeClass("G_B_G");
            $(".UpDataDis").removeClass("disable");
        }
    },
    //下载表格  
    d_table(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        let sjNum = ''+Math.floor(Math.random()*10)+''+Math.floor(Math.random()*10)+''+Math.floor(Math.random()*10)+''+Math.floor(Math.random()*10)+'';
        let load = Loading.service({fullscreen: true, text: '正在下载中....'});
        if(vm.disType==1){//下载汇总单的待分配数据
            let tableName;
            if(vm.dataType==1){
                tableName = '汇总需求单_所有_'+vm.$route.query.sum_demand_sn+'_'+sjNum
            }else if(vm.dataType==2){
                tableName = '汇总需求单_缺口_'+vm.$route.query.sum_demand_sn+'_'+sjNum
            }
            exportsa(vm.url+vm.$downLoadSumDemandGoodsInfoURL+'?sum_demand_sn='+vm.$route.query.sum_demand_sn+"&data_type="+vm.dataType,tableName);
            setTimeout(function(){
                // $('.UpDataDis').removeClass("G_B_G");
                // $('.UpDataDis').removeClass("disable");
                load.close();
            },9000)
            //window.open(vm.url+vm.$downLoadSumDemandGoodsInfoURL+'?sum_demand_sn='+vm.$route.query.sum_demand_sn+"&data_type="+vm.dataType+'&token='+headersToken);
        }else if(vm.disType==2){//下载汇总单的待分配数据(无序)
            window.open(vm.url+vm.$downLoadSdgInfoNoSortURL+'?sum_demand_sn='+vm.$route.query.sum_demand_sn+"&data_type="+vm.dataType+'&token='+headersToken);
        }
        vm.dialogVisibleUpDataDis=false;
    },
    //打开上传弹框 
    // confirmShow(){
    //     let vm = this;
    //     $(".upDataButtonByBox_b").fadeIn();
    // },
    //确认上传 
    GFFconfirmUpData(){
        let vm=this;
        var formDate = new FormData($("#forms")[0]);
        vm.dialogVisibleDistribution=false;
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$uploadSumDemandGoodsInfoURL+"?sum_demand_sn="+vm.$route.query.sum_demand_sn+"&data_type="+vm.dataType,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code==1000){
                vm.msgStr=res.msg;
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                vm.getDataList();
                if(vm.dataType==1){//全部
                    $(".quanbu").addClass("B_B_G").removeClass("G_B_G")
                    $(".quekou").addClass("G_B_G").removeClass("B_B_G")
                }else if(vm.dataType==2){//缺口
                    $(".quekou").addClass("B_B_G").removeClass("G_B_G")
                    $(".quanbu").addClass("G_B_G").removeClass("B_B_G")
                }
            }else{
                vm.msgStr=res.msg;
                $(".operationFailed_b").fadeIn()
            }
            $("#file").val('');
            vm.fileName='';
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
    methodChangeSelect(){
        let vm = this;
        vm.channel='';
        vm.channelInfo.splice(0);
        vm.channels_list.forEach(element => {
            if(element.method_id==vm.method_id){
                vm.channelInfo.push({"channels_name":element.channels_name,"channels_id":element.id})
            }
        });
    },
    //自提或者邮寄
    SelfLiftingOrmail(){
        let vm=this;
        let str=[{"shouhuo":"自提","id":0},{"shouhuo":"邮寄","id":1}]
        str.forEach(element=>{
            vm.mails.push({"label":element.shouhuo,"mail":element.id});
        })
    },
    channelMethodList(){
        let vm = this;
        // vm.errors.items.splice(0);
        if(vm.methodInfo!=''){
            return false;
        }
        axios.get(vm.url+vm.$BatchUploadBatchDataURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.channels_list=res.data.data.channels_info;
            res.data.data.method_info.forEach(element=>{
                vm.methodInfo.push({"method_id":element.id,"method_name":element.method_name});
            });
            res.data.data.user_info.forEach(element=>{
                vm.userList.push({"userName":element.account_number,"userId":element.id});
            });
            res.data.data.erp_house_list.forEach(element=>{
                vm.ports.push({"portName":element.store_name,"portId":element.store_id});
            });
            res.data.data.supplier_list_info.forEach(element=>{
                vm.suppliers.push({"supplierName":element.supplier_name,"supplierId":element.supplier_id});
            })
            res.data.data.task_info.forEach(element=>{
                vm.taskInfoS.push({"taskName":element.task_name,"taskId":element.id});
            })
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    SelectFile(){
        let vm=this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    //上传商品明细 
    purchaseDataUp(){
        let vm=this;
        // this.$validator.validateAll().then(valid => {
        //     if(valid){
        if(vm.method_id==''){
            vm.$message('请选择采购方式后上传！');
            return false;
        }
        if(vm.channel==''){
            vm.$message('请选择渠道后上传！');
            return false;
        }
        if(vm.mail===''){
            vm.$message('请选择自提/邮寄后上传！');
            return false;
        }
        if(vm.port==''){
            vm.$message('请选择港口后上传！');
            return false;
        }
        if(vm.ID==''){
            vm.$message('请选择采购人员ID后上传！');
            return false;
        }
        if(vm.supplier_id==''){
            vm.$message('请选择供应商ID后上传！');
            return false;
        }
        if(vm.task_id==''){
            vm.$message('请选择任务模板后上传！');
            return false;
        }
        if(vm.delivery_time==''){
            vm.$message('请选择提货时间后上传！');
            return false;
        }
        if(vm.arrive_time==''){
            vm.$message('请选择到货时间后上传！');
            return false;
        }
        if(vm.buy_time==''){
            vm.$message('请选择购买时间后上传！');
            return false;
        }
        if(vm.fileName==''){
            vm.$message('请选择文件后上传！');
            return false;
        }
        let original_or_discount;
        vm.channels_list.forEach(element=>{
            if(element.id===vm.channel){
                original_or_discount=element.original_or_discount
            }
        })
        $(".confirmPopup_b").fadeOut();
        var formDate = new FormData($("#forms")[0]);
        vm.dialogVisibleUpData = false;
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
            url: vm.url+vm.$doUploadBatchDataURL+"?sum_demand_sn="+vm.$route.query.sum_demand_sn+"&method_id="+vm.method_id+
                "&channels_id="+vm.channel+"&path_way="+vm.mail+"&port_id="+vm.port+"&user_id="+vm.ID+
                "&supplier_id="+vm.supplier_id+"&task_id="+vm.task_id+"&delivery_time="+vm.delivery_time+
                "&arrive_time="+vm.arrive_time+"&integral_time="+vm.integral_time+"&original_or_discount="+original_or_discount+
                "&buy_time="+vm.buy_time,
            type: "POST",
            async: true,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                loa.close();
                $("#file1").val('');
                if(res.code=='1000'){
                    // vm.$message('数据上传成功！');
                    vm.msgStr=res.msg;
                    $(".successfulOperation_b").fadeIn();
                    setTimeout(function(){
                        $(".successfulOperation_b").fadeOut();
                    },2000),
                    vm.getDataList();
                }else if(res.code=='1099'){
                    vm.upDiscountDataVisible = true;
                    vm.tipsStr = res.msg;
                    vm.upDiscountData = res.data;
                }else{
                    // vm.tipsDialogVisible=true; 
                    // let msg=res.msg.split(',')
                    // vm.tipsStr=msg;
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
        //     }
        // })
    },
    cleanData(){
        let vm = this;
        $("#file1").val('');
        vm.channel='';
        vm.method_id='';
        vm.mail='';
        vm.port='';
        vm.ID='';
        vm.fileName='';
        vm.supplier_id='';
        vm.delivery_time='';
        vm.arrive_time='';
        vm.task_id='';
        vm.dataType='';
        vm.integral_time='';
        vm.buy_time='';
    },
    aaaa(name,index){
        let vm = this;
        if(!(index%2 == 0)){
            return "background-color:#eaf1fd";
        }else{
            return "background-color:#b1bbcd";
        }
    },
    openBox(){
        let vm = this;
        vm.dialogVisibleSameDayD=true;
        // $(event.target).addClass("G_B_G");
        // $(event.target).addClass("disable"); 
    },
    selectInterByType(){
        let vm = this;
        vm.dialogVisibleSameDayD=false;
        // $(event.target).addClass("disable");
        let load = Loading.service({fullscreen: true, text: '正在下载中....'});
        if(vm.sameDayType==1){
            let time = new Date()
            var month = time.getMonth()+1;
            var date = time.getDate(); 
            let sjNum = ''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+'';
            let tableName = "采购数据表_"+vm.$route.query.sum_demand_sn+"_"+vm.checkTime(month)+vm.checkTime(date)+"_"+sjNum;
            // let tableName = '采购数据表_'+vm.$route.query.sum_demand_sn 
            exportsa(vm.url+vm.$downLoadSumDemandGoodsInfoURL+'?sum_demand_sn='+vm.$route.query.sum_demand_sn+"&data_type="+vm.sameDayType,tableName);
            setTimeout(function(){
                // $(".sameDayD").removeClass("G_B_G");
                // $(".sameDayD").removeClass("disable");
                load.close()
            },4000)
        }else if(vm.sameDayType==2){
            vm.downLoadTodayPurTask(load)
        }
    },
    //下载当日采购任务
    downLoadTodayPurTask(load){
        // let vm=this;
        // let headersToken=sessionStorage.getItem("token"); 
        // window.open(vm.url+vm.$downLoadTodayPurTaskURL+'?sum_demand_sn='+vm.$route.query.sum_demand_sn+'&token='+headersToken); 
        let vm = this;
        let time = new Date()
        var month = time.getMonth()+1;
        var date = time.getDate(); 
        let sjNum = ''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+'';
        let tableName = "采购数据表_"+vm.$route.query.sum_demand_sn+"_"+vm.checkTime(month)+vm.checkTime(date)+"_"+sjNum;
        vm.isPortShow=false;
        axios.post(vm.url+vm.$downLoadTodayPurTaskURL,
            {
                "sum_demand_sn":vm.$route.query.sum_demand_sn,
                "data_type":vm.sameDayType
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            if(res.data.code){
                vm.$message(res.data.msg)
            }else{
                exportsa(vm.url+vm.$downLoadTodayPurTaskURL+"?sum_demand_sn="+vm.$route.query.sum_demand_sn+"&data_type="+vm.sameDayType,tableName);
            }
            // $(".sameDayD").removeClass("G_B_G");
            // $(".sameDayD").removeClass("disable");
            load.close();
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });  
    },
    checkTime(time){
        if(time<10){
            time = '0'+time
        }
        return time
    },
    integralSpan(e){
        let vm = this;
        vm.dataType=e;
        $(event.target).addClass("B_B_G");
        $(event.target).removeClass ("G_B_G");
        $(event.target).siblings().addClass("G_B_G");
        $(event.target).siblings().removeClass("B_B_G");
        vm.getDataList();
    },
    //搜索 
    searchFrame(){
        let vm = this;
        // let tableData = vm.tableData;
        vm.tableData=[];
        if(vm.goods_name!=''){
            vm.reTableData.forEach(element => {
                    console.log(element.goods_name.indexOf(vm.goods_name)!=-1)
                if(element.goods_name.indexOf(vm.goods_name)!=-1){
                    vm.tableData.push(element)
                }
            });
        }else if(vm.spec_sn!=''){
            vm.reTableData.forEach(element => {
                if(element.spec_sn.indexOf(vm.spec_sn)!=-1){
                    vm.tableData.push(element)
                }
            });
        }else if(vm.erp_merchant_no!=''){
            vm.reTableData.forEach(element => {
                if(element.erp_merchant_no.indexOf(vm.erp_merchant_no)!=-1){
                    vm.tableData.push(element)
                }
            });
        }else{
            vm.tableData=vm.reTableData;
        }
        console.log(vm.tableData)
    }
  },
  computed:{
        //选择要展示的表头 
        selectTitle(checkedCities){
            return selectTitle(checkedCities);
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
                    return e=='商品代码';
            });
            let xuqiudanfenbu=this.$store.state.select.find(function(e){
                    return e=='需求单分布';
            });
            let guigema=this.$store.state.select.find(function(e){
                    return e=='商品参考码';
            });
            let xuqiushu=this.$store.state.select.find(function(e){
                    return e=='需求数';
            });
            let qingdianshu=this.$store.state.select.find(function(e){
                    return e=='可分配数量';
            });
            let quekoushu=this.$store.state.select.find(function(e){
                    return e=='采购缺口数';
            });
            let shicaishu=this.$store.state.select.find(function(e){
                    return e=='实采数';
            });
            let aa = 0;
            let channelSelectList = [];
            this.$store.state.select.forEach(element=>{
                if(element.search("-") != -1 ){
                    aa++;
                    channelSelectList.push(element);
                }
            })
            let i = 0;
            let j = 0;
            vm.titleData.forEach( channelInfo=>{
                channelInfo.channel_info.forEach(element=>{
                    channelSelectList.forEach(select=>{
                        if(element.channels_name==select){
                            element.discount_type.forEach(discount=>{
                                i++;
                            })
                        }
                    })
                })
                if(i!=0){
                    vm.channelTitleList[j].num=i;
                    $(".channelTitle"+j+"").removeClass("addDisplay");
                }else if(i==0){
                    $(".channelTitle"+j+"").addClass("addDisplay")
                }
                j++;
                i=0;
            })
            let widthLength =aa*(110*6)
            if(judgeRate){
                widthLength=widthLength+200;
            }
            if(caozuo){
                widthLength=widthLength+160;
            }
            if(pinpaimingceng){
                widthLength=widthLength+160;
            }
            if(meijyuanjia){
                widthLength=widthLength+180;
            }
            if(xuqiudanfenbu){
                widthLength=widthLength+vm.demandList.length*210;
            }
            if(guigema){
                widthLength=widthLength+180;
            }
            if(xuqiushu){
                widthLength=widthLength+100;
            }
            if(qingdianshu){
                widthLength=widthLength+100;
            }
            if(quekoushu){
                widthLength=widthLength+100;
            }
            if(shicaishu){
                widthLength=widthLength+90;
            }
            return "width:"+widthLength+"px";
        },
    }
}
</script>

<style scoped>
.purchaseTaskDetail .t_i{width:100%; height:auto;}
.purchaseTaskDetail .t_i_h{width:100%; overflow-x:hidden;}
.purchaseTaskDetail .ee{width:100%!important; text-align: center;}
.purchaseTaskDetail .t_i_h table{width:100%;}
.purchaseTaskDetail .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.purchaseTaskDetail .cc{width:100%; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.purchaseTaskDetail .cc table{width:100%; }
.purchaseTaskDetail .cc table td{height:25px; text-align:center}
table tr td:hover{
    background:#ced7e6 !important;
}
.sort{
    display: inline-block;
    padding-left: 5px;
    padding-right: 5px;
    background-color: #000;
    color: #fff;
    border-radius: 50%;
    line-height: 20px;
}
.disount{
    display: inline-block;
    width: 50px;
}
.addDisplay{
    display: none;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>