<template>
  <div class="confirmUpData_b">
    <!-- 实时采购数据确认上传页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="confirmUpData bgDiv">
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="listTitleStyle">
                            <backToTheUpPage :back='back'></backToTheUpPage>
                            <span class="upTitle">上传实时采购数据</span>
                        </div>
                        <el-row>
                            <el-col :span="12">
                                <div class="content_left">
                                    <ul>
                                        <li>
                                            <template>
                                                <el-select v-model="mode" @change="modeChangeSelect()" placeholder="选择方式">
                                                    <el-option v-for="item in modes" :key="item.value" :label="item.label" :value="item.mode">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </li>
                                        <li>
                                            <template>
                                                <el-select v-model="channel" placeholder="选择渠道">
                                                    <el-option v-for="item in channels" :key="item.value" :label="item.label" :value="item.channel">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </li>
                                        <li>
                                            <template>
                                                <el-select v-model="ID" placeholder="选择ID">
                                                    <el-option v-for="item in IDS" :key="item.value" :label="item.label" :value="item.ID">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </li>
                                        <li>
                                            <template>
                                                <el-select v-model="mail" placeholder="自提/邮寄">
                                                    <el-option v-for="item in mails" :key="item.value" :label="item.label" :value="item.mail">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </li>
                                        <li>
                                            <template>
                                                <el-select v-model="port" placeholder="选择港口">
                                                    <el-option v-for="item in ports" :key="item.value" :label="item.label" :value="item.port">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </li>
                                        <li>
                                            <template>
                                                <el-select v-model="supplier_id" placeholder="选择供应商">
                                                    <el-option v-for="item in suppliers" :key="item.id" :label="item.label" :value="item.id">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </li>

                                        <li>
                                            <template>
                                                <el-select v-model="task_id" placeholder="选择任务模板">
                                                    <el-option v-for="item in taskInfoS" :key="item.sn" :label="item.label" :value="item.sn">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </li>
                                        <li>
                                            <template>
                                                <div>
                                                    <el-date-picker v-model="delivery_time" type="date" placeholder="设置提货日"></el-date-picker>
                                                </div>
                                            </template>
                                        </li>
                                        <li>
                                            <template>
                                                <div>
                                                    <el-date-picker v-model="arrive_time" type="date" placeholder="设置到货日"></el-date-picker>
                                                </div>
                                            </template>
                                        </li>
                                        <div style="clear: both;"></div>
                                    </ul>
                                    
                                </div>
                            </el-col>
                            <el-col :span="6" :offset="6">
                                <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                    <div class="file">
                                    <img src="../../image/upload.png"/>
                                    <span v-if="fileName==''">点击上传文件</span>
                                    <span v-if="fileName!=''">{{fileName}}</span>
                                    <input style="height:100%;width:100%;" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                    </div>
                                </form>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col :span="24">
                                <div class="content_right">
                                    <div class="confirm bgButton" @click="confirmAddTo()" v-if="isShowOne">
                                        确认添加
                                    </div>
                                    <div class="confirm infoBgButton" v-if="isShowTwo">
                                        确认添加
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                        <div class="table_title">已添加的数据表格</div>
                        <table v-for="item in tableList">
                            <tr>
                                <td>{{item.name}}</td>
                                <td>{{item.mode}}</td>
                                <td>{{item.channel}}</td>
                                <td>{{item.ID}}</td>
                                <td>{{item.mail}}</td>
                                <td>{{item.port}}</td>
                                <td>{{item.supplier_id}}</td>
                                <td>{{item.deliveryTime}}</td>
                                <td>{{item.deliveryTime}}</td>
                                <td><i class="el-icon-check"></i></td>
                            </tr>
                        </table>
                        <div class="confirm bgButton" @click="confirmShow()">确认上传</div>
                    </el-col>
                </el-row>
            </div>
        </el-col>
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
    <div class="confirmPopup_b" style="display:none;">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要上传？
        <div class="confirm"><el-button @click="uploadDetailed()" size="mini" type="primary">确认</el-button><el-button @click="determineIsNo()" size="mini" type="info">取消</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui';
import backToTheUpPage from '../UiAssemblyList/backToTheUpPage';
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
    components:{
      backToTheUpPage,
    },
    data() {
      return {
        //所有下拉选择
        mode:'',   //选择方式
        modes:[],
        channel:'',   //选择渠道
        channels:[],
        ID:'',   //选择ID
        IDS:[],
        port:'',//选择港口
        ports:[],
        mail:'',  //自提或邮寄
        mails:[],
        url: `${this.$baseUrl}`,
        tableList:[],   //添加的表格列表 
        formDate:[],
        isShowTwo:false,
        isShowOne:true,
        supplier_id:'',
        suppliers:[],
        taskInfoS:[],
        task_id:'',
        delivery_time:'',
        deliveryStr:'',
        arrive_time:'',
        arriveStr:'',
        back:'/upData',
        //确认弹框
        show:false,
        WholeData:[],
        fileName:'',
        channels_list:[],
        headersStr:'',
        //后台消息确认弹框
        tipsDialogVisible:false,
        tipsStr:'',
        //如果折扣信息不完整
        upDiscountDataVisible:false,
        upDiscountData:[],
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.SelfLiftingOrmail();
        this.getWholeData();
    },
    methods: {
        //获取方式，采购ID，渠道数据
        getWholeData(){
            let vm=this;
            vm.WholeData=JSON.parse(sessionStorage.getItem("uploadingData"));
            //方式
            // vm.mode=vm.WholeData.method_info;
            vm.WholeData.method_info.forEach(element => {
                vm.modes.push({"label":element.method_name,"mode":element.id})
            });
            //采购ID
            vm.WholeData.user_info.forEach(element=>{
                vm.IDS.push({"label":element.account_number,"ID":element.id});
            })
            //渠道
            vm.channels_list=vm.WholeData.channels_info;
            //供应商
            vm.WholeData.supplier_list_info.forEach(element=>{
                vm.suppliers.push({"label":element.supplier_name,"id":element.supplier_id})
            })
            //仓位
            vm.WholeData.erp_house_list.forEach(erpHouse=>{
                vm.ports.push({"label":erpHouse.store_name,"port":erpHouse.store_id});
            })
            //任务模板
            vm.WholeData.task_info.forEach(taskInfo=>{
                vm.taskInfoS.push({"label":taskInfo.task_name,"sn":taskInfo.task_sn})
            })
        },
        //自提或者邮寄
        SelfLiftingOrmail(){
            let vm=this;
            let str=[{"shouhuo":"自提","id":0},{"shouhuo":"邮寄","id":1}]
            str.forEach(element=>{
                vm.mails.push({"label":element.shouhuo,"mail":element.id});
            })
        },
        //选择方式后获取渠道列表
        modeChangeSelect(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.channels.splice(0);
            vm.channel='';
            vm.channels_list.forEach(element => {
                if(element.method_id==vm.mode){
                    vm.channels.push({"label":element.channels_name,"channel":element.id})
                }
                
            });
        },
        SelectFile(){
            let vm=this;
            var r = new FileReader();
            var f = document.getElementById("file1").files[0];
            vm.fileName=f.name;
        },
        //添加的表格数据
        confirmAddTo(){
            let vm=this;
            var r = new FileReader();
            var f = document.getElementById("file1").files[0];
            if(f==undefined){
                vm.$message('上传文件不能为空！');
                return false;
            }
            r.readAsDataURL(f);
            var channelStr='';
            vm.channels.forEach(element=>{
                if(vm.channel==element.channel){
                    channelStr=element.label
                }
            })
            if(channelStr==''){
                vm.$message('采购渠道不能为空！');
                return false;
            }
            var modeStr='';
            vm.modes.forEach(element=>{
                if(vm.mode==element.mode){
                    modeStr=element.label
                }
            })
            if(modeStr==''){
                vm.$message('采购方式不能为空！');
                return false;
            }
            var mailStr='';
            vm.mails.forEach(element=>{
                if(vm.mail==element.mail){
                    mailStr=element.label
                }
            })
            if(mailStr==''){
                vm.$message('提货方式不能为空！');
                return false;
            }
            var portStr='';
            vm.ports.forEach(element=>{
                if(vm.port==element.port){
                    portStr=element.label
                }
            })
            if(portStr==''){
                vm.$message('港口不能为空！');
                return false;
            }
            var purchaseID='';
            vm.IDS.forEach(element=>{
                if(vm.ID==element.ID){
                    purchaseID=element.label
                }
            })
            if(purchaseID==''){
                vm.$message('采购ID不能为空！');
                return false;
            }
            var supplierIdStr='';
            vm.suppliers.forEach(element=>{
                if(vm.supplier_id==element.id){
                    supplierIdStr=element.label
                }
            })
            if(vm.supplier_id==''){
                vm.$message('供应商不能为空！');
                return false;
            }
            vm.deliveryStr=dateToStr(vm.delivery_time)
            if(vm.delivery_time==''){
                vm.$message('提货时间不能为空！');
                return false;
            }
            vm.arriveStr=dateToStr(vm.arrive_time)
            if(vm.arrive_time==''){
                vm.$message('到货时间不能为空！');
                return false;
            }
            var task_name='';
            vm.taskInfoS.forEach(taskSn=>{
                if(vm.task_id==taskSn.sn){
                    task_name=taskSn.label
                }
            })
            if(vm.task_id==''){
                vm.$message('任务模板不能为空！');
                return false;
            }
            vm.tableList.push({
                "name":f.name,"channel":channelStr,"ID":purchaseID,"mail":mailStr,"port":portStr,"mode":modeStr,
                "supplier_id":supplierIdStr,"deliveryTime":vm.deliveryStr,"arriveTime":vm.arriveStr,"taskId":task_name
                });
            var formDate = new FormData($("#forms")[0]);
            var purchase_sn=vm.$route.query.purchase_sn;
            vm.isShowOne=false;
            vm.isShowTwo=true;
        },
        //上传商品明细
        uploadDetailed(){
            let vm=this;
            $(".confirmPopup_b").fadeOut();
            var formDate = new FormData($("#forms")[0]); 
            var purchase_sn=vm.$route.query.purchase_sn;
            vm.isShowOne=true;
            vm.isShowTwo=false;
            let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
             $.ajax({
                url: vm.url+vm.$doUploadDataURL+"?purchase_sn="+purchase_sn+"&method_id="+vm.mode+
                    "&channels_id="+vm.channel+"&path_way="+vm.mail+"&port_id="+vm.port+"&user_id="+vm.ID+
                    "&supplier_id="+vm.supplier_id+"&task_id="+vm.task_id+"&delivery_time="+vm.deliveryStr+
                    "&arrive_time="+vm.arriveStr,
                type: "POST",
                async: true,
                cache: false,
                headers:vm.headersStr,
                data: formDate,
                processData: false,
                contentType: false,
                success: function(res) {
                    loa.close();
                    if(res.code=='1000'){
                        vm.$message('数据上传成功！');
                        vm.$router.push('/upData');
                    }else if(res.code=='1099'){
                        vm.upDiscountDataVisible = true;
                        vm.tipsStr = res.msg;
                        vm.upDiscountData = res.data;
                    }else{
                        vm.tipsDialogVisible=true;
                        let msg=res.msg.split(',')
                        vm.tipsStr=msg;
                        vm.channel='';
                        vm.mode='';
                        vm.mail='';
                        vm.port='';
                        vm.ID='';
                        vm.fileName='';
                        vm.supplier_id='';
                        vm.delivery_time='';
                        vm.arrive_time='';
                        vm.task_id='';
                        $("#file1").val('');
                        vm.tableList.splice(0);
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
        //确认弹框否
        determineIsNo(){
            $(".confirmPopup_b").fadeOut();
        },
        //确认弹框是
        confirmShow(){
            $(".confirmPopup_b").fadeIn();
        },
    }
}
</script>

<style scoped>
.confirmUpData_b .confirmPopup_b{
    width: 100%;
    height: 1106px;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
}
.confirmUpData_b .confirmPopup{
  width: 400px;
  height: 189px;
  margin: 375px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 999;
  background-color: #fff;
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.confirmUpData_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.confirmUpData_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.confirmUpData_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.confirmUpData_b .file {
    position: relative;
    display: inline-block;
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 1px solid #ccc;
    overflow: hidden;
    text-decoration: none;
    text-indent: 0;
    line-height: 50px;
    text-align: center;
}
.confirmUpData_b .file img{
  padding-top: 20px;
}
.confirmUpData_b .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 19px;
}
.confirmUpData_b .file input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
}
.confirmUpData_b .confirmTitle{
    display: inline-block;
    width: 400px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.titleText{
    
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.confirmPopup_b .el-icon-view{
    color: #fff;
}
.confirmPopup_b .el-icon-close{
    margin-left: 270px;
}
.el-date-editor.el-input, .el-date-editor.el-input__inner{
    width: 100% !important;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>