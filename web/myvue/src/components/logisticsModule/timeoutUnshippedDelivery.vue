<template>
  <div class="timeoutUnshippedDelivery_b">
    <!-- 超时未发货页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="timeoutUnshippedDelivery">
                        <el-row>
                            <el-col :span="10">
                                <div class="title tableTitleStyle">
                                    <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;超时未发货</span>
                                    <!-- <input type="text" class="inputStyle" placeholder="请输入搜索关键字" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i> -->
                                    <searchBox @searchFrame='searchFrame'></searchBox>
                                </div> 
                            </el-col>
                            <el-col :span="14" class="title_right">
                                <div style="float: right;">
                                    <form id="forms1" method="post" enctype="multpart/form-data" style="display: inline-block">
                                    <div class="file" style="margin-top: 0;margin-left:0;" v-if="isShow">
                                        <!-- <img src="../../image/upload.png"/>
                                        <span v-if="fileName==''">点击选择批量文件</span> -->
                                        <i class="iconfont upDataIcon">&#xe637;</i>
                                        <span v-if="fileName!=''">{{fileName}}</span>
                                        <input style="height:100%;width:100%;" id="file" type="file" @change="selectTheFile()"  name="goods_file"/>
                                    </div>
                                </form>
                                <span class="bgButton" type="text"  @click="upData()">上传待发货订单</span>
                                <div class="notBgButton" @click="d_download()">下载模板表格</div>
                                </div>
                            </el-col>
                        </el-row>
                        <table style="width:100%;text-align: center" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>店铺名称</th>
                                <th>erp订单编号</th>
                                <th>平台订单状态</th>
                                <th>客户网名</th>
                                <th>收件人</th>
                                <th class="ellipsis">收货地址</th>
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
                                <td class="ellipsis" style="width: 180px;" :title='item.receiver_area+item.receiver_address'>
                                    <span style="-webkit-box-orient: vertical;">{{item.receiver_area}}{{item.receiver_address}}</span>
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
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '../UiAssemblyList/searchBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        fontWatermark,
    },
      data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        downloadUrl:`${this.$downloadUrl}`,
        search:'',
        total:0,//页数默认为0
        page:1,//分页页数
        pagesize:15,//每页的数据条数
        isShow:true,
        fileName:'',
        headersStr:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.gettimeOutDeliveryData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
         //获取超时未发货订单
        gettimeOutDeliveryData(){
            let vm=this;
            if(vm.search!=''){
                vm.page=1
            }
            axios.get(vm.url+vm.$ligOutOrderURL+"?keywords="+vm.search+"&page="+vm.page+"&pagesize="+vm.pagesize+"&expire_mark=1",
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.tableData=res.data.orderInfo.data;
                vm.loading.close()
                vm.total=res.data.orderInfo.total
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
            vm.gettimeOutDeliveryData();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.gettimeOutDeliveryData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.gettimeOutDeliveryData()
        },
        selectTheFile(){
            let vm=this;
            var r = new FileReader();
            var f = document.getElementById("file").files[0];
            r.readAsDataURL(f);
            vm.fileName=f.name;
        },
        upData(){
            let vm=this;
            var formDate = new FormData($("#forms1")[0]);
            $.ajax({
            url: vm.url+vm.$upDeliverOrdURL+"?up_type=1",
            type: "POST",
            async: false,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.code==2000){
                    vm.gettimeOutDeliveryData();
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
                    if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                    }
                });
        },
        d_download(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.downloadUrl+'/发货订单.xls');
        },
    }
}
</script>
<style>
.el-table{
    text-align: center;
}
.el-table th>.cell {
    text-align: center;
}
.el-table thead{
    font-weight: bold;
    color: #000;
    background-color: #ccc !important;
}
.el-table th{
    background-color: #ccc !important;
}

.timeoutUnshippedDelivery .ellipsis span{
    width: 180px;
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.timeoutUnshippedDelivery .file {
    position: relative;
    display: inline-block;
    text-align: center;
}
.timeoutUnshippedDelivery .file img{
  padding-top: 20px;
}
.timeoutUnshippedDelivery .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 19px;
}
.timeoutUnshippedDelivery .file input {
    position: absolute;
    /* font-size: 100px; */
    right: 0;
    top: 0;
    opacity: 0;
}
.timeoutUnshippedDelivery .upLoadImg{
    width: 120px;
    height: 120px; 
}
.timeoutUnshippedDelivery .download{
    display: inline-block;
    color: #7092ce;
    margin-right: 20px;
}
.title_right{
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    padding-left: 35px;
    margin-top: 20px;
}
</style>

<style scoped lang=less>
@import '../../css/logisticsModule.less';
</style>