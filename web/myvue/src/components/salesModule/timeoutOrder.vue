<template>
  <div class="timeoutOrder_b">
    <!-- 发货超时订单页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="timeoutOrder">
                        <el-row class="tableTitleStyle">
                            <el-col :span="10">
                                <div>
                                    <span><span class="coarseLine MR_twenty"></span>超时发货订单</span>
                                    <searchBox @searchFrame='searchFrame'></searchBox>
                                </div> 
                            </el-col>
                            <el-col :span="14">
                                <span class="bgButton floatRight MT_twenty MR_twenty" @click="confirmShow()">上传超时发货订单</span>
                                <span class="bgButton floatRight MT_twenty MR_twenty" @click="d_download()">下载模板表格</span>
                            </el-col>
                        </el-row>
                        <el-row>
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
                                    <td v-if="item.order_status==1">待发货</td>
                                    <td v-if="item.order_status==2">待清关</td>
                                    <td v-if="item.order_status==3">待转关</td>
                                    <td v-if="item.order_status==4">配发中</td>
                                    <td>{{item.buyer_nick}}</td>
                                    <td>{{item.receiver_name}}</td>
                                    <td class="overOneLinesHeid" style="width:180px;" :title='item.receiver_area+item.receiver_address'>
                                        <span style="-webkit-box-orient: vertical;width:180px;">{{item.receiver_area}}{{item.receiver_address}}</span>
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
                        </el-row>
                    </div>
                </el-col>
            </el-row>
        </el-col>
        <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '../UiAssemblyList/searchBox';
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
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
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',
        fileName:'',
        headersStr:'',
        titleStr:'上传清关订单',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getTimeOutOrderData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getTimeOutOrderData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$sellOutOrderURL+"?expire_mark=1&page="+vm.page+"&pagesize="+vm.pagesize+"&keywords="+vm.search,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.tableData=res.data.orderInfo.data;
                vm.total=res.data.orderInfo.total;
                vm.loading.close()
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        confirmShow(){
            $(".upDataButtonByBox_b").fadeIn();
        },
        GFFconfirmUpData(formDate){
            let vm=this;
            $(".upDataButtonByBox_b").fadeOut();
            let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
            $.ajax({
            url: vm.url+vm.$upDeliverOrdURL+"?up_type=1",
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
                    vm.getTimeOutOrderData();
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
            window.open(vm.downloadUrl+'/发货订单.xls');
        },
        //搜索框
        searchFrame(e){
            let vm=this;
            vm.search=e;
            vm.page=1;
            vm.getTimeOutOrderData();
        },
         //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getTimeOutOrderData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getTimeOutOrderData()
        },
    }
}
</script>
<style scoped>
@import '../../css/publicCss.css';
</style>