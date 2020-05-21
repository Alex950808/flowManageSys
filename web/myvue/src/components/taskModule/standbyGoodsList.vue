<template>
  <div class="standbyGoodsList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                        <span><span class="coarseLine MR_twenty"></span>常备商品列表</span>
                        <span class="bgButton floatRight MR_twenty MT_twenty" @click="openBox();channelMethodList();cleanData()">上传常备批次采购数据</span>
                        <span class="bgButton floatRight MR_twenty MT_twenty" @click="d_table()">下载常备商品</span>
                        <span class="bgButton floatRight MR_twenty MT_twenty" @click="openAGBox()">单个新增常备商品</span>
                        <span class="bgButton floatRight MR_twenty MT_twenty" @click="openImportOffer()">批量新增常备商品</span>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>常备商品列表</span></div></el-col>
                        <el-col :span="2"><div>商品名称</div></el-col>
                        <el-col :span="3"><div><el-input v-model="goodsName" placeholder="请输入商品名称"></el-input></div></el-col>
                        <el-col :span="2"><div>品牌名称</div></el-col>
                        <el-col :span="3"><div><el-input v-model="brandName" placeholder="请输入品牌名称"></el-input></div></el-col>
                        <el-col :span="2"><div>商品规格码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="specSn" placeholder="请输入商品规格码"></el-input></div></el-col>
                        <el-col :span="2"><div>平台条码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="platformBarcode" placeholder="请输入平台条码"></el-input></div></el-col>
                    </el-row>
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="2" :offset="3"><div>商家编码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erpMerchantNo" placeholder="请输入商家编码"></el-input></div></el-col>
                        <el-col :span="2"><div>商品代码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erpPrdNo" placeholder="请输入商品代码"></el-input></div></el-col>
                        <el-col :span="2"><div>参考码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erpRefNo" placeholder="请输入参考码"></el-input></div></el-col>
                        <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
                    <el-row class="MT_twenty MB_ten lineHeightForty">
                        <span class="bgButton floatRight MR_twenty" @click="openBox();channelMethodList();cleanData()">上传常备批次采购数据</span>
                        <span class="bgButton floatRight MR_twenty" @click="d_table()">下载常备商品</span>
                        <span class="bgButton floatRight MR_twenty" @click="openAGBox()">单个新增常备商品</span>
                        <span class="bgButton floatRight MR_twenty" @click="openImportOffer()">批量新增常备商品</span>
                    </el-row>
                    <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>品牌</th>
                                <th>商品名称</th>
                                <th>商品规格码</th>
                                <th>商家编码</th>
                                <th>商品代码</th>
                                <th>商品参考码</th>
                                <th>平台条码</th>
                                <th>美金原价</th>
                                <th>最大采购量</th>
                                <th>可用采购量</th>
                                <th>库存</th>
                                <th>采购状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td class="overOneLinesHeid fontLift" style="width:150px;">
                                    <!-- {{item.brand_name}} -->
                                    <el-tooltip class="item" effect="light" :content="item.brand_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:150px;">&nbsp;&nbsp;{{item.brand_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td class="overOneLinesHeid fontLift" style="width:200px;">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:200px;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td>{{item.spec_sn}}</td>
                                <td>{{item.erp_merchant_no}}</td>
                                <td>{{item.erp_prd_no}}</td>
                                <td>{{item.erp_ref_no}}</td>
                                <td class="overOneLinesHeid fontLift" style="width:150px;">
                                    <el-tooltip class="item" effect="light" :content="item.platform_barcode" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:150px;">&nbsp;&nbsp;{{item.platform_barcode}}</span>
                                    </el-tooltip>
                                </td>
                                <td>{{item.spec_price}}</td>
                                <td><span class="notBgButton" @click="editStandbyGoods('num',item.max_num,item.goods_name,item.id,index)">{{item.max_num}}</span></td>
                                <td>{{item.available_num}}</td>
                                <td>{{item.stock_num}}</td>
                                <td>
                                    <span>
                                        <span v-if="item.is_purchase===1">采购中</span>
                                        <span v-if="item.is_purchase===0">停止采购</span>
                                    </span>
                                </td>
                                <td>
                                    <span @click="editStandbyGoods('purchase',item.is_purchase,item.goods_name,item.id,index)">
                                        <span v-if="item.is_purchase===1" class="redFont Cursor">暂停采购</span>
                                        <span v-if="item.is_purchase===0" class="D_greenFont Cursor">恢复采购</span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <el-dialog :title="titleStr" :visible.sync="editDialogVisible" width="300px">
                        <el-row class="MB_twenty">
                            <el-col :span="8" :offset="2" v-if="isNum"><div class="MT_ten">最大采购量</div></el-col>
                            <el-col :span="12" v-if="isNum">
                                <div>
                                    <el-input type="Number" v-model="maxNum" placeholder="请输入最大采购量"></el-input>
                                </div>
                            </el-col>
                            <!-- <el-col :span="5" v-if="isPurchase"><div class="MT_ten">采购状态：</div></el-col>
                            <el-col :span="6" v-if="isPurchase">
                                <div>
                                    <template>
                                        <el-select v-model="is_purchase" v-validate="'required'" name="channelName" placeholder="请您选择渠道名称">
                                            <el-option v-for="item in purchaseList" :key="item.id" :label="item.name" :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col> -->
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="editDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="doEditStandbyGoods()">确 定</el-button>&nbsp;&nbsp;&nbsp;
                        </span>
                    </el-dialog>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
        <el-dialog title="常备商品数据上传" :visible.sync="dialogVisibleUpData" width="800px">
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
                    <span v-show="errors.has('methodName')" class="text-style redFont" v-cloak> {{ errors.first('methodName') }} </span>
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
                <el-col :span="3"><div class="MT_ten"><span class="redFont">(请先选择方式)</span></div></el-col>
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
                <el-button type="primary" @click="standbyUp()">确 定</el-button>
            </span>
        </el-dialog>
        <el-dialog title="单个新增常备商品" :visible.sync="dialogVisibleAddGoods" width="800px">
            <el-row class="MB_twenty">
                <el-col :span="4">
                    <div class="MT_ten"><span class="redFont">*</span>商品名称：</div>
                </el-col>
                <el-col :span="6">
                    <el-input v-model="goods_name" placeholder="请输入商品名称"></el-input>
                </el-col>
                <el-col :span="4" :offset="1">
                    <div class="MT_ten"><span class="redFont">*</span>平台条码：</div>
                </el-col>
                <el-col :span="6">
                    <el-input v-model="platform_barcode" placeholder="请输入平台条码"></el-input>
                </el-col>
            </el-row>
            <el-row class="MB_twenty">
                <el-col :span="4">
                    <div class="MT_ten"><span class="redFont">*</span>最大采购量：</div>
                </el-col>
                <el-col :span="6">
                    <el-input v-model="max_num" type="Number" placeholder="请输入最大采购量"></el-input>
                </el-col>
            </el-row>
            <span slot="footer" class="dialog-footer">
                <el-button type="info" @click="dialogVisibleAddGoods = false;">取 消</el-button>
                <el-button type="primary" @click="addStandbyGoods()">确 定</el-button>
            </span>
        </el-dialog>
      <successfulOperation :msgStr="msgStr"></successfulOperation>
      <operationFailed :msgStr="msgStr"></operationFailed>
      <div class="beijing">
          <span @click="dTable()" class="d_table blueFont Cursor">常备商品导入模板下载</span>
      </div>
      <upDataButtonByBox :titleStr="upDataStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import { exportsa } from '@/filters/publicMethods.js'
export default {
  components:{
      notFound,
      operationFailed,
      successfulOperation,
      upDataButtonByBox,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1 
      //编辑 
      editDialogVisible:false,
      titleStr:'',
      maxNum:'',
      isNum:false,
      isPurchase:false,
      purchaseList:[{"name":"采购中","id":"1"},{"name":"停止采购","id":"0"}],
      is_purchase:'',
      id:'',
      //上传常备商品
      index:'',
      dialogVisibleUpData:false,
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
      channelInfo:[],
      methodInfo:[],
      method_id:'',
      channels_list:[],
      userList:[],
      integral_time:'',
      msgStr:'',
      //单个新增常备商品 
      dialogVisibleAddGoods:false,
      goods_name:'',
      platform_barcode:'',
      max_num:'',
      //批量上传 
      upDataStr:'批量上传商品',
      //搜索 
      goodsName:'',
      brandName:'',
      specSn:'',
      platformBarcode:'',
      erpMerchantNo:'',
      erpPrdNo:'',
      erpRefNo:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.SelfLiftingOrmail();
  },
  methods:{
      // 
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$standbyGoodsListURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&goods_name="+vm.goodsName+"&brand_name="+vm.brandName
        +"&spec_sn="+vm.specSn+"&platform_barcode="+vm.platformBarcode+"&erp_merchant_no="+vm.erpMerchantNo+"&erp_prd_no="+vm.erpPrdNo+"&erp_ref_no="+vm.erpRefNo,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.tableData=[];
                vm.total=0;
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
    //编辑 
    editStandbyGoods(str,edit,name,id,index){
        let vm = this;
        vm.id=id;
        vm.maxNum='';
        vm.is_purchase='';
        vm.index=index;
        if(str=='num'){
            vm.titleStr='编辑最大可采量';
            vm.editDialogVisible=true;
            vm.maxNum=edit;
            vm.isNum=true;
            vm.isPurchase=false;
        }else if(str=='purchase'){
            vm.titleStr='编辑采购状态';
            vm.is_purchase=''+edit+'';
            vm.isPurchase=true;
            vm.isNum=false;
            vm.doEditStandbyGoods();
        }
    },
    doEditStandbyGoods(){
        let vm = this;
        if(vm.maxNum<=0&&vm.maxNum!=''){
            vm.$message('最大采购数量不能小于等于零!');
            return false;
        }
        let is_purchase;
        if(vm.is_purchase==='0'){
            is_purchase='1'
        }else if(vm.is_purchase==='1'){
            is_purchase='0'
        }
        vm.editDialogVisible=false;
        let load = Loading.service({fullscreen: true, text: '正在提交中....'});
        axios.post(vm.url+vm.$doEditStandbyGoodsURL,
            {
                "id":vm.id,
                "max_num":vm.maxNum,
                "is_purchase":is_purchase,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code=='1000'){
                vm.tableData.splice(vm.index,1,res.data.data); 
            }else{
                vm.$message(res.data.msg); 
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
    openBox(){
        let vm = this;
        vm.dialogVisibleUpData=true;
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
    SelectFile(){
        let vm=this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    //上传常备商品数据
    standbyUp(){
        let vm = this;
        this.$validator.validateAll().then(valid => {
            if(valid){
                let original_or_discount;
                vm.channels_list.forEach(element=>{
                    if(element.id===vm.channel){
                        original_or_discount=element.original_or_discount
                    }
                })
                var formDate = new FormData($("#forms")[0]);
                vm.dialogVisibleUpData = false;
                let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
                $.ajax({
                    url: vm.url+vm.$uploadStandbyGoodsDataURL+"?method_id="+vm.method_id+
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
    //下载表格
    d_table(){
        let vm=this;
        // let headersToken=sessionStorage.getItem("token"); 
        // window.open(vm.url+vm.$downLoadStandbyGoodsInfoURL+'?&token='+headersToken);
        let load = Loading.service({fullscreen: true, text: '下载中....'});
        let sjNum = ''+Math.floor(Math.random()*10)+''+Math.floor(Math.random()*10)+''+Math.floor(Math.random()*10)+''+Math.floor(Math.random()*10)+'';
        let active=$(event.target);
        let tableName = "采购数据表_"+sjNum;
        vm.isPortShow=false;
        axios.post(vm.url+vm.$downLoadStandbyGoodsInfoURL,
            {
                "sum_demand_sn":vm.sumDemandSn,
            },
            {
               headers:vm.headersStr, 
            }
        ).then(function(res){
            load.close()
            if(res.data.code){
                vm.$message(res.data.msg)
            }else{
                exportsa(vm.url+vm.$downLoadStandbyGoodsInfoURL+"?sum_demand_sn="+vm.sumDemandSn,tableName)
            }
        }).catch(function (error) {
            load.close()
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
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
        vm.goods_name='';
        vm.platform_barcode='';
        vm.max_num='';
        $("#file1").val('');
    },
    //单个新增常备商品
    openAGBox(){
        let vm = this;
        vm.dialogVisibleAddGoods=true;
        vm.cleanData();
    },
    //确定单个新增常备商品
    addStandbyGoods(){
        let vm = this;
        if(vm.goods_name===''){
            vm.$message('商品名称不能为空!');
            return false;
        }
        if(vm.platform_barcode===''){
            vm.$message('平台条码不能为空!');
            return false;
        }
        if(vm.max_num===''){
            vm.$message('最大采购量不能为空!');
            return false;
        }
        if(vm.max_num<=0){
            vm.$message('最大采购数量不能小于零等于零!');
            return false;
        }
        vm.dialogVisibleAddGoods=false;
        axios.post(vm.url+vm.$addStandbyGoodsURL,
            {
                "goods_name":vm.goods_name,
                "platform_barcode":vm.platform_barcode,
                "max_num":vm.max_num
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=='2024'){
                vm.getDataList();
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //模板表下载
    dTable(){
        let vm=this;
        window.open(vm.downloadUrl+'/常备商品导入模板.xls');
    },
    //批量上传常备商品
    openImportOffer(){
        let vm = this;
        $(".upDataButtonByBox_b").fadeIn();
        $(".d_table").fadeIn();
        $(".beijing").fadeIn();
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
        url: vm.url+vm.$uploadStandbyGoodsURL,
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
                vm.getDataList();
                $(".successfulOperation_b").fadeIn();
                setTimeout(function(){
                    $(".successfulOperation_b").fadeOut();
                },2000),
                $("#file").val('');
                vm.fileName='';
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
  }
}
</script>

<style>
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
    top: 56px;
    left: 15px;
    bottom: 0;
    right: 0;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
