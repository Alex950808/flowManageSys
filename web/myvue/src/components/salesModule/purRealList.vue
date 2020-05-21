<template>
  <div class="purRealList stayOpenBill_b">
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="stayOpenBill">
                        <div class="purchaseDiscount listTitleStyle">
                            <span><span class="coarseLine MR_twenty"></span>批次分货列表</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>
                        <table class="tableTitle MB_twenty">
                            <thead>
                                <th width='10%'>实采单号</th>
                                <th width='10%'>自提/邮寄</th>
                                <th width='10%'>实际批次提货日</th>
                                <!-- <th width='9%'>仓位</th>                                
                                <th width='9%'>批次是否设置</th> -->
                                <th width='10%'>实采单状态</th>
                                <th width='10%'>分货状态</th>
                                <th width='10%'>是否预采</th>
                                <th width='14%'>创建时间</th>
                                <th width='10%'>操作</th>
                            </thead>
                        </table>
                        <table class="tableTitleTwo MB_twenty">
                            <thead>
                                <th width='10%'>实采单号</th>
                                <th width='10%'>自提/邮寄</th>
                                <th width='10%'>实际批次提货日</th>
                                <!-- <th width='9%'>仓位</th>                                
                                <th width='9%'>批次是否设置</th> -->
                                <th width='10%'>实采单状态</th>
                                <th width='10%'>分货状态</th>
                                <th width='10%'>是否预采</th>
                                <th width='14%'>创建时间</th>
                                <th width='10%'>操作</th>
                            </thead>
                        </table>
                        <el-row class="content PT_ten PB_ten" v-for="(item,index) in tableData" :key="index">
                            <div class="lineHeightForty border_B">
                                <span class="number">采购期期数：</span>
                                <span class="stage">{{item.purchase_info.id}}</span>
                                <span class="number">采购期单号：</span>
                                <span class="stage">{{item.purchase_info.purchase_sn}}</span>
                                <span class="number">采购期提货日：</span>
                                <span class="stage">{{item.purchase_info.delivery_time}}</span>
                            </div>
                            <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                                <tr v-for="itemO in item.real_data">
                                    <td width='10%'>{{itemO.real_purchase_sn}}</td>                                 
                                    <td width='10%'>{{itemO.path_way}}</td>
                                    <td width='10%'>{{itemO.delivery_time}}</td>
                                    <!-- <td width='9%'>{{itemO.port_id}}</td>                                    
                                    <td width='9%'>{{itemO.is_setting}}</td> -->
                                    <td width='10%'>{{itemO.status_desc}}</td>
                                    <td width='10%'>
                                        <!-- 待部门分货 -->
                                        <span v-if="itemO.is_sort=='0'" style="color: red;">{{itemO.sort_desc}}</span>
                                        <!-- 待用户分货 -->
                                        <span v-if="itemO.is_sort=='1'" style="color: blue;">{{itemO.sort_desc}}</span>
                                        <!-- 完成分货 -->
                                        <span v-if="itemO.is_sort=='2'" style="color: green;">{{itemO.sort_desc}}</span>
                                    </td>
                                    <td width='10%'>{{itemO.batch_cat_desc}}</td>
                                    <td width='14%'>{{itemO.create_time}}</td>                                    
                                    <td width='10%'>
                                        <!-- 已分货之后才能查看分货数据 -->
                                        <i v-if="itemO.is_sort=='1' || itemO.is_sort=='2'">
                                            <i class="blueFont Cursor NotStyleForI" 
                                                @click="seeDetail(item.purchase_info.purchase_sn,itemO.real_purchase_sn,itemO.status)">
                                                查看分货数据
                                            </i><br>                                            
                                        </i>
                                        <span v-if="itemO.is_sort=='0'">
                                            <i v-if="itemO.batch_cat=='1'" class="blueFont Cursor NotStyleForI"
                                                @click="openViewBox(item.purchase_info.purchase_sn,itemO.real_purchase_sn,'1');">
                                                生成部门分货
                                            </i>
                                            <i v-if="itemO.batch_cat=='2'" class="blueFont Cursor NotStyleForI"
                                                @click="openViewBox(item.purchase_info.purchase_sn,itemO.real_purchase_sn,'2');">
                                                生成部门分货
                                            </i>
                                        </span>
                                        
                                    </td> 
                                </tr>   
                            </table> 
                        </el-row>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                        <el-row>
                            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                            </el-pagination>
                        </el-row>
                    </div>
                </el-col>
            </el-row>
        </el-col>
        <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '../UiAssemblyList/searchBox';
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        customConfirmationBoxes,
        fontWatermark,
    },
    data() {
      return {
        url: `${this.$baseUrl}`,
        tableData:[],  //批次数据
        purchase_info:'',
        search:'',//用户输入数据
        isShow:false,
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        purchase_sn:'',
        real_purchase_sn:'',
        contentStr:"请您确认是否要生成部门分货数据？",
        index:'',
      }
    },
    mounted(){
        this.getStayOpenData()
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
        this.tableTitle()
    },
    methods: {
        //获取待开单数据
        getStayOpenData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$purRealListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&keywords="+vm.search,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.loading.close()
                vm.tableData=res.data.pur_real_list;
                vm.total=res.data.total_num;
                // vm.purchase_info=res.data.pur_real_list.purchase_info;
                if(vm.total==0){
                    vm.isShow=true;
                }
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
            vm.getStayOpenData();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getStayOpenData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getStayOpenData()
        },
        seeDetail(purchase_sn,real_purchase_sn,real_status){
            var query_type = 1;
            if (real_status >= 2) {
                query_type = 2;    
            }
            let vm = this;            
            vm.$router.push('/dpmDivisionGoods?purchase_sn='+purchase_sn+"&real_purchase_sn="+real_purchase_sn+"&query_type="+query_type);
        },
        //打开商品部对部门分货-生成分货数据弹框
        openViewBox(purchase_sn,real_purchase_sn,index){
            let vm = this;
            vm.purchase_sn=purchase_sn;
            vm.real_purchase_sn=real_purchase_sn;
            vm.index=index;
            $(".confirmPopup_b").fadeIn()
        },
        //商品部对部门分货-生成分货数据
        confirmationAudit(){
            let vm = this;
            let headersToken=sessionStorage.getItem("token");
            $(".confirmPopup_b").fadeOut();
            let url;
            if(vm.index=='1'){
                url=vm.$generalDepartSortDataURL;
            }else if(vm.index=='2'){
                url=vm.$makePerDepartSortDataURL;
            }
            axios.post(vm.url+url,
                {
                    "purchase_sn":vm.purchase_sn,
                    "real_purchase_sn":vm.real_purchase_sn
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                if(res.data.code=='2024'){ 
                    vm.getStayOpenData();
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
        //确认弹框否
        determineIsNo(){
            $(".confirmPopup_b").fadeOut()
        },
        tableTitle(){
            let vm=this;
            $(window).scroll(function(){
                var scrollTop = $(this).scrollTop();
                var thisHeight=210;
                if(scrollTop>thisHeight){
                    $('.tableTitleTwo').show();
                    $(".tableTitleTwo").addClass("addclass");
                    $(".tableTitleTwo").width($(".purchaseDiscount").width());
                }else if(scrollTop<thisHeight){
                    $('.tableTitleTwo').hide();
                    $(".tableTitleTwo").removeClass("addclass");
                }
            })
        },
    }
}
</script>
<style>
.purRealList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.purRealList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.purRealList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>