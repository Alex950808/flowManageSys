<template>
  <div class="discountList procurementChannel_b">
    <!-- 采购渠道页面 -->
    <el-col :span="24" style="background-color: #fff;">
        <div class="procurementChannel bgDiv">
            <el-row>
              <el-col :span="22" :offset="1">
                <div class="tableTitleStyle">
                    <el-row>
                      <el-col :span="8">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购折扣</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>
                      </el-col>
                      <el-col :span="16" class="fontRight">
                        <span class="bgButton" @click="dialogVisible = true;openBox()">新增品牌折扣</span>
                        <span class="bgButton" @click="confirmShow(2)">上传折扣</span>
                        <!-- <span class="notBgButton" @click="downloadTemplate('/采购折扣上传表.xls')">下载表格模板</span> -->
                        <span class="notBgButton MR_twenty" @click="downloadDisTable()">下载当前折扣表</span>
                      </el-col>
                    </el-row>
                </div>
                <table class="MB_twenty" style="width:100%;text-align:center" border="0" cellspacing="0" cellpadding="0">
                  <thead>
                    <tr>
                      <th>品牌名称</th>
                      <th v-for="item in purchaseHead" style="width:200px;">{{item}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="itemT in purchaseText">
                      <td class="overOneLinesHeid widthTwoHundred"><span class="widthTwoHundred">{{itemT.name}}</span></td>
                      <td v-for="(key,value) in itemT.discount_info" style="width:200px;">{{key}}</td>
                    </tr>
                  </tbody>
                </table>
                <!-- <div class="download fontRight lineHeighteighty">
                  <span class="notBgButton" @click="downloadTemplate('/采购需求-商品采购折扣表.xls')">下载表格模板</span>
                  <span class="notBgButton" @click="downloadDisTable()">下载当前折扣表</span>
                  <span class="bgButton" @click="confirmShow(2)">确认上传</span>
                </div> -->
              </el-col>
            </el-row>
        </div>
    </el-col>
    <el-dialog title="新增品牌折扣" :visible.sync="dialogVisible" width="800px">
      <el-row class="lineHeightForty MB_twenty">
        <el-col :span="4"><div><span class="redFont">*</span>品牌：</div></el-col>
        <el-col :span="6">
          <div>
            <template>
              <el-select v-model="brand_id" filterable placeholder="请选择品牌">
                <el-option v-for="item in brandList" :key="item.label" :label="item.label" :value="item.id"></el-option>
              </el-select>
            </template>
          </div>
        </el-col>
        <el-col :span="4" :offset="1"><div>出货量：</div></el-col>
        <el-col :span="6">
          <div>
            <el-input v-model="shipment" placeholder="请输入出货量"></el-input>
          </div>
        </el-col>
      </el-row>
      <el-row class="lineHeightForty MB_twenty">
        <el-col :span="4"><div class=""><span class="redFont">*</span>采购方式：</div></el-col>
        <el-col :span="6">
          <div>
            <template>
              <el-select v-model="method_id" @change="selectChannelFromMethodid()" placeholder="请选择采购方式">
                <el-option v-for="item in methodList" :key="item.label" :label="item.label" :value="item.id"></el-option>
              </el-select>
            </template>
          </div>
        </el-col>
        <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>品牌折扣：</div></el-col>
        <el-col :span="6">
          <div>
            <el-input v-model="brand_discount" placeholder="请输入品牌折扣"></el-input>
          </div>
        </el-col>
      </el-row>
      <el-row v-if="isSelect" class="lineHeightForty MB_twenty">
        <el-col :span="4"><div><span class="redFont">*</span>采购渠道：</div></el-col>
        <el-col :span="6">
          <div>
            <template>
              <el-select v-model="channels_id" filterable placeholder="请选择采购渠道">
                <el-option v-for="item in channelsForMethodList" :key="item.label" :label="item.label" :value="item.id"></el-option>
              </el-select>
            </template>
          </div>
        </el-col>
      </el-row>
      <span slot="footer" class="dialog-footer">
        <el-button type="info" @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogVisible = false;doAddBrandDiscount()">确 定</el-button>
      </span>
    </el-dialog>
    <div class="confirmPopup_b" v-if="show">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要上传？
        <div class="confirm"><el-button  @click.once="uploadDisTable()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
    <div class="beijing">
        <!-- <span @click="d_table()" class="d_table blueFont Cursor">报价信息导入模板下载</span> -->
        <span class="d_table blueFont Cursor" @click="downloadTemplate('/采购折扣上传表.xls')">下载表格模板</span>
    </div>
    <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
    <guidedBombBox :bombBoxStr="bombBoxStr" :goWhere="goWhere" :LT="LT"></guidedBombBox>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui';
import { bgHeight } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '@/components/UiAssemblyList/searchBox';
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import guidedBombBox from '@/components/UiAssemblyList/guidedBombBox'
export default {
    components:{
        searchBox,
        upDataButtonByBox,
        guidedBombBox,
    },
    data() {
      return {
        url: `${this.$baseUrl}`,
        downloadUrl:`${this.$downloadUrl}`,
        tableData1: [],
        tableData2:[],
        dialogVisible: false,
        channelName:'',
        methodsNames:[],
        methodsName:'',
        method_name:[],
        search:'',
        purchaseHead:[],  //采购折扣列表表头
        purchaseText:[],  //采购折扣列表内容
        discountData:'',
        loading:'',
        fileName:'',//选择的文件名称
        isShow:false,
        show:false,
        headersStr:'',
        isVIPDiscount:true,
        isDiscount:false,
        index:'',//用来判断为普通折扣还是VIP折扣
        dialogVisible:false,
        brand_id:'',
        method_id:'',
        channels_id:'',
        brand_discount:'',
        shipment:'',
        brandList:[],
        channelsList:[],
        channelsForMethodList:[],
        methodList:[],
        isSelect:false,
        titleStr:'上传文件',
        //弹框去审核
        goWhere:'审核',
        bombBoxStr:'去往审核列表(折扣审核)',
        LT:'/auditList',
      }
    },
    mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
      this.purchaseDiscount();
      this.getMethodList();
    },
     methods: {
      //获取方式列表
      getMethodList(){
        let vm=this;
        axios.get(vm.url+vm.$methodListURL,
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          if(res.data.code==1000){
            res.data.data.forEach(element=>{
              vm.methodsNames.push({"channelName":element.method_name,"channelId":element.id});
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
      //新增渠道 
      addChannel(){
        let vm=this;
        if(vm.channelName==''){
          vm.$message('采购渠道名称不能为空!');
          return false;
        }
        if(vm.methodsName==''){
          vm.$message('采购方式名称不能为空!');
          return false;
        }
        axios.post(vm.url+vm.$createChannelsURL,
          {
            "channels_name":this.channelName,
            "method_id":vm.methodsName,
          },
          {
              headers:vm.headersStr,
          }
        ).then(function(res){
          vm.channelName='';
          vm.methodsName='';
          if(res.data.code==1000){
            vm.dialogVisible = false;
            vm.loading.close();
            vm.$message(res.data.msg);
            vm.tableData1.splice(0);
            vm.getChannelData();
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
      cancel(){
        let vm=this;
        vm.channelName='';
        vm.methodsName='';
      },
      //采购折扣列表
      purchaseDiscount(){
        let vm=this;
        axios.get(vm.url+vm.$discountListURL+"?brand_name="+vm.search,
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          if(res.data.code==1000){
              vm.purchaseHead=res.data.data.channel_info//表头数据
              vm.purchaseText=res.data.data.data_info//表内容数据
          }
          if(res.data.code==1002){
            vm.isShow=true;
          }
          vm.loading.close()
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //搜索框
      searchFrame(e){
          let vm=this;
          vm.search=e;
          vm.page=1;
          vm.purchaseDiscount();
      },
      //下载表格模板
      downloadTemplate(str){
        let vm=this;
        window.open(vm.downloadUrl+str);
      },
      //下载当前折扣表
      downloadDisTable(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        let tmpWin=window.open(vm.url+vm.$downloadDiscountCurrentURL+"?token="+headersToken);
      },
      //上传新的折扣表
      GFFconfirmUpData(formDate){
        let vm=this;
        vm.show=false;
        $(".d_table").fadeOut();
        $(".beijing").fadeOut();
        $.ajax({
          url: vm.url+vm.$uploadDiscountURL,
          type: "POST",
          async: false,
          cache: false,
          headers:vm.headersStr,
          data: formDate,
          processData: false,
          contentType: false,
          success: function(res) {
            vm.determineIsNo();
            if(res.code==1000){
              vm.$message('数据上传成功！');
              $(".guidedBombBox").fadeIn();
              vm.purchaseDiscount();
              $("#file1").val('');
              vm.fileName='';
            }else{
              vm.$message('数据上传失败！'+res.msg);
              $("#file1").val('');
              vm.fileName='';
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
      // }
      //确认弹框否
        determineIsNo(){
            $(".upDataButtonByBox_b").fadeOut();
            $(".d_table").fadeOut();
            $(".beijing").fadeOut();
        },
        //确认弹框是
        confirmShow(index){//index用來区分普通折扣还是VIP折扣
            $(".upDataButtonByBox_b").fadeIn();
            $(".d_table").fadeIn();
            $(".beijing").fadeIn();
        },
        //打开新增品牌折扣表弹框
        openBox(){
          let vm = this;
          vm.celanOptions();
          if(vm.brandList!=''&&vm.methodList!=''){
            return false;
          }
          axios.get(vm.url+vm.$addBrandDiscountURL,
            {
              headers:vm.headersStr,
            }
          ).then(function(res){
            if(res.data.code=1000){
              res.data.data.brand_list_info.forEach(brandInfo=>{
                vm.brandList.push({"label":brandInfo.name,'id':brandInfo.brand_id});
              });
              vm.channelsList=res.data.data.purchase_channels_list;
              res.data.data.purchase_method_list.forEach(methodInfo=>{
                vm.methodList.push({"label":methodInfo.method_name,"id":methodInfo.id})
              })
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
        //通过方式id选择渠道
        selectChannelFromMethodid(){
          let vm = this;
          if(vm.method_id!=''){
            vm.isSelect=true;
          }
          vm.channels_id='';
          vm.channelsForMethodList.splice(0);
          vm.channelsList.forEach(channelId=>{
            if(channelId.method_id==vm.method_id){
              vm.channelsForMethodList.push({"label":channelId.channels_name,"id":channelId.id})
            }
          })
        },
        //确认新增品牌折扣
        doAddBrandDiscount(){
          let vm = this;
          if(vm.checkOptions()==false){
            return false;
          }
          axios.post(vm.url+vm.$doAddBrandDiscountURL,
            {
              "brand_id":vm.brand_id,
              "method_id":vm.method_id,
              "channels_id":vm.channels_id,
              "brand_discount":vm.brand_discount,
              "shipment":vm.shipment,
            },
            {
              headers:vm.headersStr,
            }
          ).then(function(res){
            if(res.data.code==1000){
              vm.purchaseDiscount();
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
        //检查是否填写
        checkOptions(){
          let vm = this;
          if(vm.brand_id==''){
            vm.$message('请选择品牌后提交！');
            return false;
          }
          if(vm.method_id==''){
            vm.$message('请选择方式后提交！');
            return false;
          }
          if(vm.channels_id==''){
            vm.$message('请选择渠道后提交！');
            return false;
          }
          if(vm.brand_discount==''){
            vm.$message('请填写品牌折扣后提交！');
            return false;
          }
        },
        //清空选项
        celanOptions(){
          let vm = this;
          vm.brand_id='';
          vm.method_id='';
          vm.channels_id='';
          vm.brand_discount='';
          vm.shipment='';
          vm.channelsForMethodList=[];
          vm.isSelect=false;
        }
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
    left: 284px;
    bottom: 0;
    right: 0;
}
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