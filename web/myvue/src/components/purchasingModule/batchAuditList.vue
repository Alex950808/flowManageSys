<template>
  <div class="batchAuditList_b">
    <!-- 预计到港时间页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="batchAuditList bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="title listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;批次审核列表</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                            <span class="floatRight">
                                <template>
                                    <el-select v-model="statusI" clearable placeholder="请选择批次审核状态">
                                        <el-option v-for="item in statusL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                                <span class="bgButton" @click="clcikSearch()">搜索</span>
                            </span>
                        </div>
                        <div class="B_R border MB_twenty" v-for="item in BatchData.batch_total_list">
                            <div class="lineHeightForty border_B">
                                <el-col :span="21">
                                    <span class="ML_twenty">合单单号：</span>
                                    <span>{{item.title_info.purchase_sn}}</span>
                                    <span class="ML_twenty">合单名称：</span>
                                    <span v-if="item.title_info.purchase_sn=='standby'">常备合单</span>
                                    <span v-if="item.title_info.purchase_sn!='standby'">{{item.title_info.title_name}}</span>
                                </el-col>
                                <!-- <el-col :span="3">
                                    <span class="notBgButton" @click="goSummaryPage(item.title_info.purchase_sn)"><i class="el-icon-view"></i>&nbsp;&nbsp;查看数据汇总</span>
                                </el-col> -->
                            </div>
                            <table class="border fontCenter MB_ten MT_ten B_R" style="width:96%;margin-left: 2%;"> 
                                <tr class="L_H_F">
                                    <td class="widthThreeHundred">批次单号</td>
                                    <td class="widthOneFiveHundred">实采数</td>
                                    <td class="widthOneFiveHundred">仓库</td>
                                    <td class="widthOneFiveHundred">自提/邮寄</td>
                                    <td class="widthOneFiveHundred">渠道</td>
                                    <td class="widthOneFiveHundred">方式</td>

                                    <td class="widthOneFiveHundred">实采总数</td>
                                    <td class="widthOneFiveHundred">实采总金额</td>
                                    <td class="widthOneFiveHundred">到货时间</td>
                                    <td class="widthOneFiveHundred">提货时间</td>
                                    <td class="widthOneFiveHundred">状态</td>
                                    <td class="widthOneFiveHundred">操作</td>
                                    <td class="widthOneFiveHundred">批次修正</td>
                                </tr>
                                <tr class="L_H_F" v-for="(real,index) in item.batch_list">
                                    <td class="widthThreeHundred">{{real.real_purchase_sn}}</td>
                                    <td class="widthOneFiveHundred">{{real.total_buy_num}}件</td>
                                    <td class="widthOneFiveHundred">{{real.port_name}}</td>
                                    <td class="widthOneFiveHundred">
                                        <span v-if="real.path_way==0">自提</span>
                                        <span v-if="real.path_way==1">邮寄</span>
                                        <span v-else></span>
                                    </td>
                                    <td class="widthOneFiveHundred">{{real.channels_name}}</td>
                                    <td class="widthOneFiveHundred">{{real.method_name}}</td>

                                    <td class="widthOneFiveHundred">{{real.total_buy_num}}</td>
                                    <td class="widthOneFiveHundred">{{real.total_price}}</td>
                                    <td class="widthOneFiveHundred">{{real.rp_arrive_time}}</td>
                                    <td class="widthOneFiveHundred">{{real.rp_delivery_time}}</td>
                                    <td class="widthOneFiveHundred">
                                        <!-- {{real.status}} -->
                                        <span class="d_I_B w_h_ratio whiteFont" style="background-color:red;" v-if="real.status=='待审核'">待审核</span>
                                        <span class="d_I_B w_h_ratio" v-if="real.status=='已审核'">已审核</span>
                                        <span class="d_I_B w_h_ratio" v-if="real.status=='审核未通过'">审核未通过</span>
                                        <span class="d_I_B w_h_ratio" v-if="real.status=='数据已提交'">数据已提交</span>
                                    </td>
                                    <td class="widthOneFiveHundred">
                                        <span @click="goDetails(real.real_purchase_sn,real.audit_status)" class="bgButton">查看详情</span>
                                    </td>
                                    <td class="widthOneFiveHundred">
                                        <span v-if="real.status!='数据已提交'" @click="openModifyData(real.real_purchase_sn)" class="bgButton">修正</span>
                                        <span v-else-if="real.status=='数据已提交'" class="grBgButton">修正</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></div>
                        <el-row>
                            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                            </el-pagination>
                        </el-row>
                    </el-col>
                </el-row>
                
            </div>
        </el-col>
        <successfulOperation :msgStr="msgStr"></successfulOperation>
        <operationFailed :msgStr="msgStr"></operationFailed>
        <upDataButtonByBox :titleStr="upDataStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox';
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';
import operationFailed from '@/components/UiAssemblyList/operationFailed';
import successfulOperation from '@/components/UiAssemblyList/successfulOperation';
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
export default {
    components:{
        searchBox,
        fontWatermark,
        upDataButtonByBox,
        operationFailed,
        successfulOperation,
    },
    data() {
      return {
        url: `${this.$baseUrl}`,
        BatchData:[] ,  //批次信息 
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据 
        loading:'',
        isShow:false,
        headersStr:'',
        statusI:'',
        statusL:[{"name":"待审核","id":"0"},{"name":"已审核","id":"1"},{"name":"审核未通过","id":"2"},{"name":"数据已提交","id":"3"}],
        //上传修正数据
        upDataStr:'采购数据修正',
        real_purchase_sn:'',
        msgStr:''
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getBatchData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getBatchData(){
            let vm=this;
            axios.get(vm.url+vm.$batchAuditListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search+"&batch_audit=1"+"&status="+vm.statusI,
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close()
                if(res.data.code=="1000"){
                    vm.isShow=false;
                    vm.BatchData=res.data.data;
                    vm.total=res.data.data.total_num;
                }else if(res.data.code=="1001"){
                    vm.isShow=true;
                    vm.BatchData=[];
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
        //查看详情 
        goDetails(real_purchase_sn,audit_status){
            let vm = this;
            let load = Loading.service({fullscreen: true, text: '拼命加载中....'})
            axios.get(vm.url+vm.$batchAuditDetailURL+"?real_purchase_sn="+real_purchase_sn,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                load.close();
                if(res.data.code==1000){
                    sessionStorage.setItem('tableData',JSON.stringify(res.data.data));
                    vm.$router.push('/batchAuditDetail?real_purchase_sn='+real_purchase_sn+"&audit_status="+audit_status); 
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
        //搜索框
      searchFrame(e){
          let vm=this;
          vm.search=e;
          vm.page=1;
          vm.getBatchData();
      },
      clcikSearch(){
          let vm=this;
          vm.page=1;
          vm.getBatchData();
      },
      //上传修正数据
      openModifyData(sn){
          let vm = this;
          vm.real_purchase_sn=sn;
          $(".upDataButtonByBox_b").fadeIn();
      },
      //确认上传 
    GFFconfirmUpData(formDate){
        let vm=this;
        $(".upDataButtonByBox_b").fadeOut();
        // $(".d_table").fadeOut();
        // $(".beijing").fadeOut();
        let headersToken=sessionStorage.getItem("token");
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
        url: vm.url+vm.$modifyBatchDataURL+"?real_purchase_sn="+vm.real_purchase_sn,
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
            if(res.code=='1000'){
                vm.msgStr=res.msg;
                vm.getBatchData();
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
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getBatchData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getBatchData()
      },
    }
}
</script>
<style>

</style>

