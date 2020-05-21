<template>
  <div class="pastDemand_b">
    <!-- 过往需求页面 -->
   <!-- <el-row :gutter="20"> -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="pastDemand">
                        <div class="title listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;过往需求</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>
                        <ul>
                            <li v-for="item in tableData">
                                <el-row class="content">
                                    <el-col :span="18">
                                        <div class="content_left">
                                            <div class="content_text">
                                                <span class="number">{{item.purchase_sn}}</span>
                                                <span class="status"><i class="el-icon-menu"></i>&nbsp;&nbsp;查看数据汇总</span>
                                            </div>
                                            <div class="content_text">
                                                <span class="start">开始</span><span class="time">{{item.start_time}}</span>
                                                <span class="end">结束</span><span class="time">{{item.end_time}}</span>
                                            </div>
                                            <div class="content_text">
                                                <i class="el-icon-setting"></i><span class="flight">{{item.flight_sn}}</span><span class="aircraft">降落</span>
                                                <i class="el-icon-star-on"></i><span class="name">{{item.delivery_team}}</span><span class="popNumber">{{item.delivery_pop_num}}人</span>
                                            </div>
                                            <div class="content_text">
                                                <i class="el-icon-printer"></i>&nbsp;&nbsp;&nbsp;<span>{{item.channels_list}}</span>
                                            </div>
                                        </div>
                                    </el-col>
                                    <el-col :span="6">
                                        <div class="content_right">
                                            <div class="content_text">
                                                <span>提货日</span><span>{{item.delivery_time}}</span>
                                            </div>
                                            <div class="content_text">
                                                <span>通告备注</span>
                                            </div>
                                            <div class="content_text">
                                                <span>{{item.purchase_notice}}</span>
                                            </div>
                                        </div>
                                    </el-col>
                                </el-row>
                            </li>
                            <li v-if="isShow" style="text-align:center;"><img class="notData" src="../../image/notData.png"/></li>
                        </ul>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
    <!-- </el-row> -->
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
    data(){
        return{
            tableData: [],
            url: `${this.$baseUrl}`,
            total:0,//默认数据总数
            pagesize:15,//每页的数据条数
            page:1,
            isShow:false,
        }
    },
    mounted(){
        this.getpastDemand()
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods:{
        //获取过往需求列表
        getpastDemand(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$getCompletePurListURL+"?pageSize="+vm.pagesize+"&page="+vm.page+"&keywords="+vm.search,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.loading.close()
                // res.data.purchaseDate.data.forEach(element => {
                //     vm.tableData.push(element);
                // });
                vm.tableData=res.data.purchaseDate.data;
                if(res.data.purchaseDate.total==0){
                    vm.isShow=true;
                }else if(res.data.purchaseDate.total!=0){
                    vm.isShow=false;
                }
                vm.total=res.data.purchaseDate.total;
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
            vm.getpastDemand();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getpastDemand()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getpastDemand()
        }
    }
}
</script>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
<style>
/* @import '../../css/common.css'; */
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
} 
</style>