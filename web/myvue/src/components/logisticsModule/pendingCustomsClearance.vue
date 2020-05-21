<template>
  <div class="pendingCustomsClearance_b">
    <!-- 待清关订单页面 -->
        <el-col :span="24" class="Height" style="background-color: #fff;z-index:-2">
            <el-row class="bgDiv" style="z-index:2">
                <fontWatermark></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div>
                        <el-row class="tableTitleStyle">
                            <el-col :span="10">
                                <div>
                                    <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;待清关订单</span>
                                    <searchBox @searchFrame='searchFrame'></searchBox>
                                </div> 
                            </el-col>
                            <el-col :span="14">
                                <span class="bgButton floatRight MT_twenty MR_twenty" @click="confirmShow()">上传清关订单</span>
                                <span class="bgButton floatRight MT_twenty MR_twenty" @click="d_download()">下载模板表格</span>
                            </el-col>
                        </el-row>
                        
                        <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>店铺名称</th>
                                <th>erp订单编号</th>
                                <th>平台订单状态</th>
                                <th>客户网名</th>
                                <th>收件人</th>
                                <th>收货地址</th>
                                <th>物流单号</th>
                                <th>快递公司</th>
                                <th>已付金额</th>
                                <th>订单生成时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in tableData">
                                <td>{{item.shop_name}}</td>
                                <td>{{item.trade_no}}</td>
                                <td>
                                    <span v-if="item.order_status==1">待发货</span>
                                    <span v-if="item.order_status==2">待清关</span>
                                    <span v-if="item.order_status==3">待转关</span>
                                    <span v-if="item.order_status==4">配发中</span>
                                </td>
                                <td>{{item.buyer_nick}}</td>
                                <td>{{item.receiver_name}}</td>
                                <td class="overOneLinesHeid" style="width:180px;">
                                    <el-tooltip class="item" effect="light" :content="item.receiver_area+item.receiver_address" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:180px;">{{item.receiver_area}}{{item.receiver_address}}</span>
                                    </el-tooltip>
                                </td>
                                <td>{{item.logistics_no}}</td>
                                <td>{{item.logistics_name}}</td>
                                <td>{{item.paid}}</td>
                                <td>{{item.created}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </div>
                </el-col>
            </el-row>
        </el-col>
        <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui'
import searchBox from '../UiAssemblyList/searchBox'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';
export default {
    components:{
        searchBox,
        upDataButtonByBox,
        fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        downloadUrl:`${this.$downloadUrl}`,
        tableData: [],
        search:'',
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        isShow:true,
        fileName:'',
        headersStr:'',
        titleStr:'上传清关订单',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getPendingCustomsData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        //获取待清关订单
        getPendingCustomsData(){
            let vm=this;
            if(vm.search!=''){
                vm.page=1
            }
            axios.get(vm.url+vm.$ligCusOrderURL+"?page="+vm.page+"&pagesize="+vm.pagesize+"&keywords="+vm.search,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.tableData=res.data.orderInfo.data;
                vm.loading.close();
                vm.total=res.data.orderInfo.total;
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
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
            vm.getPendingCustomsData();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getPendingCustomsData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getPendingCustomsData()
        },
        confirmShow(){
            $(".upDataButtonByBox_b").fadeIn();
        },
        GFFconfirmUpData(formDate){
            let vm=this;
            $(".upDataButtonByBox_b").fadeOut();
            let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
            $.ajax({
            url: vm.url+vm.$upDeliverOrdURL+"?up_type=2",
            type: "POST",
            async: true,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                loa.close();
                if(res.code==2000){
                    vm.getPendingCustomsData();
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
        //下载模板表格
        d_download(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.downloadUrl+'/清关订单.xls');
        },
    }
}
</script>

<style scoped>
@import '../../css/publicCss.css';
</style>