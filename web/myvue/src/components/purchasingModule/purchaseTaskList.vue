<template>
  <div class="purchaseTaskList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>采购任务列表</span></div></el-col>
                        <el-col :span="2"><div>合单名称</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="sum_demand_name" placeholder="请输入合单名称"></el-input></div></el-col>
                        <el-col :span="2"><div>合单单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="sum_Demand_Sn" placeholder="请输入合单单号"></el-input></div></el-col>
                        <el-col :span="2"><div>需求单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="demand_sn" placeholder="请输入需求单号"></el-input></div></el-col>
                        <el-col :span="2"><div>外部单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="external_sn" placeholder="请输入外部单号"></el-input></div></el-col>
                        <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
                    <el-dialog title="采购数据上传" :visible.sync="dialogVisibleUpData" width="800px">
                        <span class="notBgButton" style="position: absolute;left: 619px;bottom: 109px;z-index: 999;" @click="openBox()">当天采购任务下载</span>
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
                                <span v-show="errors.has('methodName')" class="text-style redFont" v-cloak> {{ errors.first('methodName') }} </span>
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
                                <span v-show="errors.has('channelName')" class="text-style redFont" v-cloak> {{ errors.first('channelName') }} </span>
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
                                <span v-show="errors.has('mail')" class="text-style redFont" v-cloak> {{ errors.first('mail') }} </span>
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
                                <span v-show="errors.has('ID')" class="text-style redFont" v-cloak> {{ errors.first('ID') }} </span>
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
                                <span v-show="errors.has('port')" class="text-style redFont" v-cloak> {{ errors.first('port') }} </span>
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
                                <span v-show="errors.has('supplier')" class="text-style redFont" v-cloak> {{ errors.first('supplier') }} </span>
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
                                <span v-show="errors.has('deliveryTime')" class="text-style redFont" v-cloak> {{ errors.first('deliveryTime') }} </span>
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
                                <span v-show="errors.has('arriveTime')" class="text-style redFont" v-cloak> {{ errors.first('arriveTime') }} </span>
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
                                <span v-show="errors.has('taskId')" class="text-style redFont" v-cloak> {{ errors.first('taskId') }} </span>
                                </template>
                            </el-col>
                            <el-col :span="4" :offset="1">
                                <div class="MT_ten">预计返积分日期：</div>
                            </el-col>
                            <el-col :span="6">
                                <el-date-picker v-model="integral_time" style="width:100%;" value-format="yyyy-MM-dd" type="date" placeholder="预计返积分日期"></el-date-picker>
                                <span v-show="errors.has('integralTime')" class="text-style redFont" v-cloak> {{ errors.first('integralTime') }} </span>
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
                                <span v-show="errors.has('buyTime')" class="text-style redFont" v-cloak> {{ errors.first('buyTime') }} </span>
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
                    <div v-if="!isShow" class="border B_R MB_twenty PR" v-for="(item,index) in tableData">
                        <el-row class="Lavender">
                            <el-col :span="5" class="PT_Thirty PB_Thirty">
                                <span class="F_S_twenty ML_twenty lineHeightThirty">合期名称：<i class="NotStyleForI">{{item.sum_demand_name}}</i></span><br>
                                <span class="F_S_Sixteen ML_twenty lineHeightThirty">合期单号：<i class="NotStyleForI">{{item.sum_demand_sn}}</i></span><br>
                                <span class="F_S_Sixteen ML_twenty lineHeightThirty">创建时间：<i class="NotStyleForI">{{item.create_time}}</i></span>
                            </el-col>
                            <el-col :span="15" class="fontCenter MT_ten"> 
                                <div class="d_I_B lineHeightThirty w_t_ratio">
                                    <i class="NotStyleForI F_Z_N">sku数</i><br>
                                    <i class="NotStyleForI F_S_twenty fontWeight">{{item.sku_num}}</i>
                                </div>
                                <span class="verticalBarT"></span>
                                <div class="d_I_B lineHeightThirty w_40_ratio">
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N">总需求量</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.goods_num}}</i>
                                    </span>
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N">可采数</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">
                                            <span v-if="item.may_num!=null">{{item.may_num}}</span>
                                            <span v-else>-</span>
                                        </i>
                                    </span>
                                </div>
                                <span class="verticalBarT"></span>
                                <div class="d_I_B lineHeightThirty w_40_ratio">
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N">实采数</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">
                                            <span v-if="item.real_num!=null">{{item.real_num}}</span>
                                            <span v-else>-</span>
                                        </i>
                                    </span>
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N">缺口数</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.diff_num}}</i>
                                    </span>
                                </div><br>
                                <div class="d_I_B lineHeightThirty w_t_ratio MT_ten">
                                    <i class="NotStyleForI F_Z_N">订单数</i><br>
                                    <i class="NotStyleForI F_S_twenty fontWeight">{{item.demand_num}}</i>
                                </div>
                                <span class="verticalBarT"></span>
                                <div class="d_I_B lineHeightThirty w_40_ratio MT_ten">
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N">总需求额</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.total_purchase_price}}</i>
                                    </span>
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N">缺口额</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.diff_purchase_price}}</i>
                                    </span>
                                </div>
                                <span class="verticalBarT"></span>
                                <div class="d_I_B lineHeightThirty w_40_ratio MT_ten">
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N"> 实采满足率</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">
                                            <span v-if="item.diff_purchase_price!=null">{{item.real_rate}}%</span>
                                            <span v-else>-</span>
                                        </i>
                                    </span>
                                    <span class="d_I_B w_45_ratio">
                                        <i class="NotStyleForI F_Z_N">状态</i><br>
                                        <i class="NotStyleForI F_S_twenty fontWeight">{{item.status}}</i>
                                    </span>
                                </div>
                            </el-col>
                            <el-col :span="4" class="fontRight MT_ten">
                                <span class="notBgButton" @click="splitSumDemand(item.sum_demand_sn)">拆分合单需求单</span><br>
                                <span class="notBgButton" @click="dialogVisibleUpData=true;channelMethodList(item.sum_demand_sn);cleanData()">采购数据上传</span><br>
                                <span class="notBgButton" @click="viewDetail(item.sum_demand_sn,item.sku_num,item.goods_num,item.allot_num);">查看详情</span><br>
                                <span class="notBgButton" @click="viewDetails(item.sum_demand_sn,index)"><i class="NotStyleForI">展开</i><i class="el-icon-arrow-down" title="点击展示更多"></i></span>
                            </el-col>
                        </el-row>
                        <div class="O_F_A w_ratio PR" v-if="index==indexnum">
                            <table :class="['fontCenter','tabletitle','sort'+index]" style="width:100%">
                                <tr>
                                    <td v-for="item in tabletitle">
                                        <span v-if="item=='全选'"><input class="allChecked" style="width: 20px;height: 20px;" type="checkbox" @change="allSelect()"/>全选</span>
                                        <span v-else>{{item}}</span>
                                    </td>
                                    <!-- <td>客户分组</td>
                                    <td>客户名称</td>
                                    <td>采购截止日期</td> 
                                    <td>客户订单号</td>
                                    <td>需求数量</td>
                                    <td>分货排序号</td> -->
                                </tr>
                                <tr v-for="(orderInfo,num) in tableDataTwo">
                                    <td><input style="width: 20px;height: 20px;" @click="oneSelect(num)" type="checkbox" :name="num"/></td>
                                    <td>{{orderInfo.demand_sn}}</td>
                                    <td>{{orderInfo.external_sn}}</td>
                                    <td>{{orderInfo.user_name}}</td>
                                    <td>{{orderInfo.sale_user_account}}</td>
                                    <td>{{orderInfo.expire_time}}</td>
                                    <td>{{orderInfo.goods_num}}</td>
                                    <td>{{orderInfo.sort}}</td>
                                    <td>{{orderInfo.status}}</td>
                                    <td>{{orderInfo.demand_type}}</td>
                                    <td><i class="iconfont notBgButton" @click="lookDetails(orderInfo.demand_sn)" title="查看详情">&#xe631;</i></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <notFound v-if="isShow"></notFound>
                    <el-row>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
      <el-dialog title="提示" :visible.sync="tipsDialogVisible" width="800px">
        <div v-for="(item,index) in tipsStr" style="display:inline-block"><span>{{item}}<span v-if="index!=tipsStr.length-1">，</span></span></div>
        <span slot="footer" class="dialog-footer">
            <el-button type="info" @click="tipsDialogVisible = false">取 消</el-button>
            <el-button type="primary" @click="tipsDialogVisible = false">确 定</el-button>
        </span>
      </el-dialog>
      <el-dialog title="提示" :visible.sync="upDiscountDataVisible" width="800px">
        <div style="display:inline-block"><span>{{tipsStr}}</span></div>
        <el-row class="lineHeightForty MB_twenty discount" v-for="(item,index) in upDiscountData.brand_info" :key="item.brand_name">
            <el-col :span="4" :offset="1">
                <span>渠道名称 ：</span>
            </el-col>
            <el-col :span="6">
                <span>{{item.brand_name}}</span>
            </el-col>
            <el-col :span="4">
                <span><span class="redFont">*</span>对应折扣 ：</span>
            </el-col>
            <el-col :span="6">
                <span><el-input placeholder="请输入折扣" :name="index"></el-input></span>
            </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
            <el-button type="info" @click="upDiscountDataVisible = false">取 消</el-button>
            <el-button type="primary" @click="submitData()">确 定</el-button>
        </span>
      </el-dialog>
      <el-dialog title="当天采购任务下载" :visible.sync="dialogVisibleSameDayD" width="800px">
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
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import { exportsa } from '@/filters/publicMethods.js'
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
  components:{
      notFound,
      operationFailed,
      successfulOperation,
      customConfirmationBoxes,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      sum_demand_sn:'',
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
        arrive_time:'',
        buy_time:'',
        fileName:'',
        sum_demand_sn:'',
        channelInfo:[],
        methodInfo:[],
        method_id:'',
        channels_list:[],
        userList:[],
        integral_time:'',
        //后台消息确认弹框
        tipsDialogVisible:false,
        tipsStr:'',
        //如果折扣信息不完整
        upDiscountDataVisible:false,
        upDiscountData:[],

        msgStr:'',
        //展开商品
        indexnum:-1,
        tableDataTwo:[],
        tabletitle:[],
        arrSpecSn:[],
        contentStr:'全选需求单，该合期单将被释放掉，请确认！',
        //确认弹框
        sumDemandSn:'',
        //下载当天采购任务模板 
        dialogVisibleSameDayD:false,
        sameDayTypeS:[{'type':1,'label':'所有数据'},{'type':2,'label':'当天任务'}],
        sameDayType:'',
        //搜索
        sum_demand_name:'',
        external_sn:'',
        demand_sn:'',
        sum_Demand_Sn:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.tableTitle();
    this.SelfLiftingOrmail();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$demandPurchaseTaskListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&sum_demand_name="+vm.sum_demand_name+"&external_sn="+vm.external_sn
        +"&demand_sn="+vm.demand_sn+"&sum_demand_sn="+vm.sum_Demand_Sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.demand_list_info.data;
                vm.total=res.data.data.demand_list_info.total;
                vm.isShow=false;
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
    //分页 
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDataList()
    },
    tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=205;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".purchaseTaskList .tableTitleTwo").addClass("addclass");
                $(".purchaseTaskList .tableTitleTwo").width($(".purchaseTaskList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".purchaseTaskList .tableTitleTwo").removeClass("addclass");
            }
        })
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
    channelMethodList(sum_demand_sn){
        let vm = this;
        vm.sum_demand_sn=sum_demand_sn;
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
    //上传采购数据上传
    purchaseDataUp(){
        let vm=this;
        this.$validator.validateAll().then(valid => {
            if(valid){
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
                    url: vm.url+vm.$doUploadBatchDataURL+"?sum_demand_sn="+vm.sum_demand_sn+"&method_id="+vm.method_id+
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
            }
        })
    },
    //提交折扣
    submitData(){
        let vm = this;
        let arr = [];
        for(var i=0;i<vm.upDiscountData.brand_info.length;i++){
            let I_value = $('.discount input[name='+i+']').val();
            if(I_value<=0||I_value>=1){
                vm.$message('折扣填写错误，请重新填写!');
                return false;
            }
            arr.push({"brand_id":vm.upDiscountData.brand_info[i].brand_id,"brand_discount":I_value});
        }
        axios.post(vm.url+vm.$batchAddBrandDiscountURL,
            {
                "method_id":vm.upDiscountData.method_id,
                "channels_id":vm.upDiscountData.channels_id,
                "brand_discount":arr,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.upDiscountDataVisible = false;
            vm.$message(res.data.msg);
        }).catch(function (error) {
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //下载表格 
    d_table(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$downLoadSumDemandGoodsInfoURL+'?sum_demand_sn='+vm.sum_demand_sn+'&token='+headersToken);
    },
    viewDetail(sum_demand_sn,sku_num,goods_num,allot_num){
        let vm = this;
        // vm.sum_demand_sn=sum_demand_sn;
        vm.$router.push('/purchaseTaskDetail?sum_demand_sn='+sum_demand_sn+'&sku_num='+sku_num+'&goods_num='+goods_num+'&allot_num='+allot_num);
    },
    cleanData(){
        let vm = this;
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
        vm.integral_time='';
        vm.buy_time='';
        $("#file1").val('');
    },
    checkTime(time){
        if(time<10){
            time = '0'+time
        }
        return time
    },
    //下载当日采购任务 
    downLoadTodayPurTask(){
        let vm = this;
        $(event.target).addClass("disable");
        let active=$(event.target);
        let time = new Date()
        var month = time.getMonth()+1;
        var date = time.getDate(); 
        let sjNum = ''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+'';
        let tableName = "采购数据表_"+vm.sum_demand_sn+"_"+vm.checkTime(month)+vm.checkTime(date)+"_"+sjNum;
        vm.isPortShow=false;
        axios.post(vm.url+vm.$downLoadTodayPurTaskURL,
            {
                "sum_demand_sn":vm.sum_demand_sn,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            if(res.data.code){
                vm.$message(res.data.msg)
            }else{
                exportsa(vm.url+vm.$downLoadTodayPurTaskURL+"?sum_demand_sn="+vm.sum_demand_sn,tableName)
            }
            active.removeClass("disable");
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });  
    },
    //展开商品 
    viewDetails(sum_demand_sn,index){
        let vm = this;
        vm.indexnum=index;
        vm.tableDataTwo=[];
        vm.tabletitle=[];
        vm.sum_demand_sn=sum_demand_sn
        $(".allChecked").prop("checked",false);
        $(" input").prop("checked",false);
        let content_text_height=$(event.target).parent().parent().parent().parent().height();
        if(content_text_height==undefined){
            content_text_height=172;
        }
        if(content_text_height<=180){
            let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
            axios.get(vm.url+vm.$sumDemandInfoURL+"?sum_demand_sn="+sum_demand_sn,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                load.close();
                if(res.data.code=='1000'){
                    vm.tableDataTwo=res.data.data;
                    vm.tabletitle=['全选','需求单号','外部订单号','客户名称','客户分组','交期','需求数量','分货排序号','状态','类型','查看详情']
                    $(".tabletitle").addClass("lineHeightForty");
                }
            }).catch(function (error) {
                load.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        }else{
            vm.tableDataTwo=[];
            vm.tabletitle=[];
            $(".tabletitle").removeClass("lineHeightForty");
        }
        
    },
    //全选
    allSelect(){
        let vm = this;
        let allChecked = $(".sort"+vm.indexnum+" .allChecked").prop("checked");
        if(allChecked==true){
            for(var i=0;i<vm.tableDataTwo.length;i++){
                $(".sort"+vm.indexnum+" input[name="+i+"]").prop("checked",true);
            }
        }else if(allChecked==false){
            for(var i=0;i<vm.tableDataTwo.length;i++){
                $(".sort"+vm.indexnum+" input[name="+i+"]").prop("checked",false);
            }
        }
    },
    //单选
    oneSelect(index){
        let vm = this;
        let isTrueList=[];
        for(var i=0;i<vm.tableDataTwo.length;i++){
            let isTrue = $(".sort"+vm.indexnum+" input[name="+i+"]").prop("checked");
            isTrueList.push(isTrue);
        }
        for(var i=0;i<isTrueList.length;i++){
            if(isTrueList[i+1]!=undefined){
                if(isTrueList[i]==isTrueList[i+1]){
                    if(isTrueList[i]==true){
                        $(".sort"+vm.indexnum+" .allChecked").prop("checked",true)
                    }else if(isTrueList[i]==false){
                        $(".sort"+vm.indexnum+" .allChecked").prop("checked",false)
                    }
                }else{
                    $(".sort"+vm.indexnum+" .allChecked").prop("checked",false)
                    break;
                }
            }
        }
    },
    //打开根据已选sku批量修改销售折扣
    splitSumDemand(sum_demand_sn){
        let vm = this;
        let allChecked = $(".sort"+vm.indexnum+" .allChecked").prop("checked");
        let arrSpecSn = [];
        vm.sumDemandSn=sum_demand_sn;
        let isAllSelect = false;
        if(allChecked==true){
            isAllSelect=true;
            vm.tableDataTwo.forEach(specInfo=>{
                arrSpecSn.push(specInfo.demand_sn);
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.tableDataTwo.length;i++){
                let isTrue = $(".sort"+vm.indexnum+" input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    arrSpecSn.push(vm.tableDataTwo[i].demand_sn);
                }
            }
        }
        vm.arrSpecSn=arrSpecSn;
        if(arrSpecSn.length==0){
            vm.$message('请选择需求单后后进行拆分!');
            return false;
        }else{
            if(allChecked){
                $(".confirmPopup_b").fadeIn();
            }else{
                vm.confirmationAudit();
            }
        }
    },
    confirmationAudit(){
        let vm = this;
        axios.post(vm.url+vm.$splitSumDemandURL,
            {
                "sum_demand_sn":vm.sumDemandSn,
                "demand_sn":vm.arrSpecSn
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.$message(res.data.msg);
            $(".confirmPopup_b").fadeOut();
            $(".allChecked").prop("checked",false);
            $(" input").prop("checked",false);
            if(res.data.code=='1000'){
                vm.getDataList();
                vm.viewDetails(vm.sumDemandSn,vm.indexnum)
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
    openBox(){
        let vm = this;
        vm.dialogVisibleSameDayD=true;
    },
    selectInterByType(){
       let vm = this;
        vm.dialogVisibleSameDayD=false;
        if(vm.sameDayType==1){
            let time = new Date()
            var month = time.getMonth()+1;
            var date = time.getDate(); 
            let sjNum = ''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+''+Math.floor(Math.random()*9)+'';
            let tableName = "采购数据表_"+vm.sum_demand_sn+"_"+vm.checkTime(month)+vm.checkTime(date)+"_"+sjNum;
            exportsa(vm.url+vm.$downLoadSumDemandGoodsInfoURL+'?sum_demand_sn='+vm.sum_demand_sn+"&data_type="+vm.sameDayType,tableName);
        }else if(vm.sameDayType==2){
            vm.downLoadTodayPurTask()
        } 
    },
    lookDetails(demand_sn){
        let vm = this;
        vm.$router.push('/purchaseDemandDetail?demand_sn='+demand_sn+"&sum_demand_sn="+vm.sum_demand_sn+"&Delay=Delay");
    }
    //拆分合单需求单
    // splitSumDemand(sum_demand_sn){ 
    //     let vm = this;
        
    // }
  }
}
</script>

<style>
.purchaseTaskList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.purchaseTaskList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.purchaseTaskList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
