<template>
  <div class="procurementPeriod_b">
    <!-- 采购期管理页面 -->
    <!-- <el-row :gutter="20"> -->
    <el-col :span="24" style="background-color: #fff;">
        <div class="procurementPeriod bgDiv">
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="title listTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;过往采购期</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>
                    </div>
                </el-col>
            </el-row>
            <ul>
                <li v-for="item in PurchaseList">
                    <el-row class="content">
                        <el-col :span="16" :offset="1">
                            <div class="content_left">
                                <div class=" content_title">
                                    <span class="stage">{{item.id}}期</span>
                                    <span class="ML_twenty stage">{{item.delivery_time}}</span>
                                    <span class="number">{{item.purchase_sn}}</span>
                                    <span v-if="item.status==1" class="status">准备中</span>
                                    <span v-if="item.status==2" class="status">进行中</span>
                                    <span v-if="item.status==3" class="status">已关闭</span>
                                    <span v-if="item.status==4" class="status">已失效</span>
                                </div>
                                <el-row class="content_text">
                                    <el-col :span="22" :offset="2" class="overTwoLinesHeid" style="height: 95px;">
                                        <span v-for="itemD in item.channels_list" style="display:inline-block;height: 44px;line-height: 44px;">{{itemD}}&nbsp;&nbsp;&nbsp;</span>
                                    </el-col>
                                </el-row>
                                <el-row class="content_text">
                                    <el-col :span="7" :offset="2"><span class="start">开始</span><span class="time">{{item.start_time}}</span></el-col>
                                    <el-col :span="7"><span class="end">结束</span><span class="time">{{item.end_time}}</span></el-col>
                                    <el-col :span="7"><span class="name">{{item.delivery_team}}</span><span class="popNumber"> {{item.delivery_pop_num}}人</span></el-col>
                                </el-row>
                            </div>
                        </el-col>
                            <div class="content_right">
                                <div class="content_text">
                                    <span>提货日：</span><span>{{item.delivery_time}}</span>
                                </div>
                                <div class="content_text">
                                    <span>通告备注:</span>
                                </div>
                                <div class="content_text ellipsis" :title="item.purchase_notice">
                                    <span style="-webkit-box-orient: vertical;">{{item.purchase_notice}}</span>
                                </div>
                            </div>
                    </el-row>
                </li>
                <li v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></li>
                <el-col :span="22" :offset="1">
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </ul>
        </div>
    </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox';
export default {
    components:{
        searchBox,
    },
    data(){
        return{
            url: `${this.$baseUrl}`,
            PurchaseList:'',
            isShow:false,
            total:0,//默认数据总数
            pagesize:15,//每页条数默认15条
            page:1,//page默认为1
            search:'',//用户输入数据
            headersStr:'',
        }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getPurchaseList();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods:{
        getPurchaseList(){
          let vm=this;
          axios.get(vm.url+vm.$passDateList+'?start_page='+vm.page+'&page_size='+vm.pagesize+'&query_sn='+vm.search,
            {
                headers:vm.headersStr,
            }
          ).then(function(res){
              vm.loading.close();
              if(res.data.code==1000){
                    vm.isShow=false;
                    vm.PurchaseList=res.data.data.data;
                    vm.total=res.data.data.total;
              };
              if(res.data.code==1002){
                  vm.isShow=true;
                  vm.PurchaseList='';
                  vm.total=0;
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
        //搜索框
        searchFrame(e){
            let vm=this;
            vm.search=e;
            vm.page=1;
            vm.getPurchaseList();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getPurchaseList()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getPurchaseList()
        },
    }
}
</script>
<style>
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
.content_title{
    margin-top: 0px !important;
    height: 65px;
    line-height: 65px;
    background-color: #ebeef5;
    padding-left: 35px;
}
.procurementPeriod_b .content_right{
    display: inline-block;
    margin-left: 17px;
    width: 23%;
    background-color: #ebeef5;
    border: 1px solid #d9e3f3;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>