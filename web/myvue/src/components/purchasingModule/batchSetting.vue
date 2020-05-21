<template>
  <div class="batchSetting_b">
      <el-col :span="24" style="background-color: #fff;">
            <div class="batchSetting bgDiv">
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="title listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;批次到货设置</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>
                        <div class="content" v-for="item in BatchData">
                            <div class="content_text lineHeightForty">
                                <el-col :span="21">
                                <!-- <span class="stage">{{item.title_info.purchase_id}}期</span><span class="number">{{item.title_info.purchase_sn}}</span>
                                <span class="ML_twenty">提货日期：{{item.title_info.delivery_time}}</span> -->
                                    <span v-if="item.title_info.purchase_id!=null" class="stage">{{item.title_info.purchase_id}}期</span>
                                    <span v-if="item.title_info.purchase_sn!=null" class="stage ML_twenty">汇总需求单号：{{item.title_info.purchase_sn}}</span>
                                    <span v-if="item.title_info.sum_demand_name!=null" class="stage ML_twenty">合期名称：{{item.title_info.sum_demand_name}}</span>
                                    <span v-if="item.title_info.delivery_time!=null" class="stage ML_twenty">提货日期：{{item.title_info.delivery_time}}</span>
                                </el-col>
                                <el-col :span="3">
                                <span class="notBgButton" @click="goSummaryPage(item.title_info.purchase_sn)"><i class="el-icon-view"></i>&nbsp;&nbsp;查看数据汇总</span>
                                </el-col>
                            </div>
                            <el-row>
                                <el-col :span="12">
                                    <table class="table-one">
                                        <tr>
                                            <td>实采总数({{item.title_info.real_buy_num}})</td>
                                            <td>自提数量({{item.title_info.zt_num}}批({{item.title_info.zt_goods_num}}件))</td>
                                            <td>邮寄数量({{item.title_info.yj_num}}批({{item.title_info.yj_goods_num}}件))</td>
                                        </tr>
                                    </table>
                                </el-col>
                                <el-col :span="4" :offset="8">
                                </el-col>
                            </el-row>
                            <table class="table-two" v-for="realList in item.real_list">
                                <tr v-for="(real,index) in realList">
                                    <td class="widthThreeHundred">{{real.real_purchase_sn}}</td>
                                    <td class="widthOneFiveHundred">{{real.total_buy_num}}件</td>
                                    <!-- <td>{{real.create_time}}</td> -->
                                    <td class="widthOneFiveHundred">{{real.port_name}}</td>
                                    <td class="widthOneFiveHundred">
                                        <span v-if="real.path_way==0">自提</span>
                                        <span v-if="real.path_way==1">邮寄</span>
                                        <span v-else></span>
                                    </td>
                                    <td class="widthOneFiveHundred">{{real.channels_name}}</td>
                                    <td class="widthOneFiveHundred">{{real.method_name}}</td>
                                    <td class="widthOneFiveHundred"  v-if="real.batch_cat!='2'||index==0">
                                        <span v-if="real.is_setting==1">已设置</span>
                                        <span v-if="real.is_setting==0" class="bgButton" @click="taskModelList(item.title_info.purchase_sn,real.real_purchase_sn,real.group_sn,real.is_mother)">设置任务</span>
                                    </td>
                                    <!-- <td class="widthOneFiveHundred"  v-if="real.batch_cat!='2'||index==0">
                                        <span v-if="real.is_set_post==1">已设置</span>
                                        <span v-if="real.is_set_post==0" class="bgButton" @click="dialogVisibleFreight = true;opensetUpFreight(real.real_purchase_sn,item.title_info.purchase_sn,real.group_sn,real.is_mother)">设置运费</span>
                                    </td> -->
                                </tr>
                            </table>
                        </div>
                        <el-dialog title="设置任务" :visible.sync="dialogVisible" width="800px">
                            <span>
                                <el-row class="lineHeightForty MB_twenty">
                                    <el-col :span="12">
                                        <div class="">
                                            <template>
                                                <div class="block">
                                                    <span class="demonstration">批次提货时间</span>
                                                    &nbsp;&nbsp;<el-date-picker v-model="delivery_time" type="date" placeholder="选择日期"></el-date-picker>
                                                </div>
                                            </template>
                                        </div>
                                    </el-col>
                                    <el-col :span="12">
                                        <div class="">
                                            <template>
                                                <div class="block">
                                                    <span class="demonstration">批次到货时间</span>
                                                    &nbsp;&nbsp;<el-date-picker v-model="arrive_time" type="date" placeholder="选择日期"></el-date-picker>
                                                </div>
                                            </template>
                                        </div>
                                    </el-col>
                                </el-row>
                                <el-row class="lineHeightForty MB_twenty">
                                    <el-col :span="12">
                                        <div class="block">
                                            <span class="demonstration">批次任务模板</span>&nbsp;&nbsp;
                                            <template>
                                                <el-select v-model="batch" placeholder="请选择批次任务模板">
                                                    <el-option v-for="item in batchs" :key="item.id" :label="item.task_name" :value="item.id">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                        </div>
                                    </el-col>
                                </el-row>
                                
                            </span>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false;Unset()">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;confirmShow()">确定设置</el-button>
                            </span>
                        </el-dialog>
                        <el-dialog title="设置运费" :visible.sync="dialogVisibleFreight" width="800px">
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4">
                                    <span class="demonstration">设置运费</span>
                                </el-col>
                                <el-col :span="7">
                                    <div class="">
                                        <el-input type="number" v-model="post_amount" placeholder="请输入运费金额"></el-input>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisibleFreight = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisibleFreight = false;setUpFreight()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <div v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-col>
                </el-row>
            </div>
      </el-col>
      <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox';
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
  components:{
      searchBox,
      customConfirmationBoxes,
  },
  data(){
    return{
        url: `${this.$baseUrl}`,
        BatchData:[] ,  //批次信息
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据
        loading:'',
        isShow:false,
        dialogVisible:false,
        purchase_sn:'',
        real_purchase_sn:'',
        group_sn:'',
        is_mother:'',
        delivery_time:'',
        arrive_time:'',
        batch:'',
        batchs:[],
        show:false,
        headersStr:'',
        dialogVisibleFreight:false,
        post_amount:'',
        contentStr:"请您确认是否要设置批次？",
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getBatchData();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
  },
  methods:{
    getBatchData(){
            let vm=this;
            axios.get(vm.url+vm.$batchSettingURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search,
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close();
                vm.BatchData=res.data.data;
                vm.total=res.data.data_num
                if(res.data.code==1000){
                    vm.isShow=false;
                }else if(res.data.code==1002){
                    vm.isShow=true;
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
        //获取任务模板ID
        taskModelList(purchase_sn,real_purchase_sn,group_sn,is_mother){
            let vm=this;
            vm.purchase_sn=purchase_sn;
            vm.real_purchase_sn=real_purchase_sn;
            vm.group_sn=group_sn;
            vm.is_mother=is_mother;
            vm.batchs.splice(0);
            axios.get(vm.url+vm.$taskModelListURL,
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                if(res.data.code==1002){
                    vm.$message(res.data.msg);
                }else{
                    vm.dialogVisible = true;
                }
                
                if(res.data.code==1000){
                    res.data.data.task_info.forEach(element => {
                        vm.batchs.push({"id":element.id,"task_name":element.task_name});
                    });
                    
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
        //打开设置运费按钮
        opensetUpFreight(real_purchase_sn,purchase_sn,group_sn,is_mother){
            let vm=this;
            vm.real_purchase_sn=real_purchase_sn;
            vm.purchase_sn=purchase_sn;
            vm.group_sn=group_sn;
            vm.is_mother=is_mother;
            vm.post_amount='';
        },
        //设置运费
        setUpFreight(){
            let vm=this;
            if(vm.post_amount==''){
                vm.$message('运费金额不能为空！');
                return false;
            }
            axios.post(vm.url+vm.$doBatchSettingPostURL,
                {
                    "post_amount":vm.post_amount,
                    "real_purchase_sn":vm.real_purchase_sn,
                    "purchase_sn":vm.purchase_sn,
                    "group_sn":vm.group_sn,
                    "is_mother":vm.is_mother,
                },
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                if(res.data.code==1000){
                    vm.dialogVisibleFreight = false;
                    vm.getBatchData();
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
        dateToStr(datetime){ 
            var year = datetime.getFullYear();
            var month = datetime.getMonth()+1;//js从0开始取 
            var date = datetime.getDate(); 
            var hour = datetime.getHours(); 
            var minutes = datetime.getMinutes(); 
            var second = datetime.getSeconds();
            
            if(month<10){
            month = "0" + month;
            }
            if(date<10){
            date = "0" + date;
            }
            
            var time = year+"-"+month+"-"+date;
            return time;
        },
        //确定设置批次
        confirmationAudit(){
            let vm=this;
            $(".confirmPopup_b").fadeOut();
            if(vm.delivery_time.constructor==Date){
                vm.delivery_time=vm.dateToStr(vm.delivery_time);
            }
            if(vm.arrive_time.constructor==Date){
                vm.arrive_time=vm.dateToStr(vm.arrive_time);
            }
            axios.post(vm.url+vm.$doBatchSettingURL,
                {
                    "purchase_sn":vm.purchase_sn,
                    "real_purchase_sn":vm.real_purchase_sn,
                    "group_sn":vm.group_sn,
                    "is_mother":vm.is_mother,
                    "delivery_time":vm.delivery_time,
                    "arrive_time":vm.arrive_time,
                    "task_id":vm.batch
                },
                {
                     headers:vm.headersStr,
                }
            ).then(function(res){
                vm.$message(res.data.msg);
                if(res.data.code==1000){
                    vm.dialogVisible = false;
                    vm.purchase_sn='';
                    vm.real_purchase_sn='';
                    vm.delivery_time='';
                    vm.arrive_time='';
                    vm.batch='';
                }
                vm.getBatchData();
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
          vm.getBatchData();
      },
        //确认弹框否
        determineIsNo(){
            $(".confirmPopup_b").fadeOut()
        },
        //确认弹框是
        confirmShow(){
            $(".confirmPopup_b").fadeIn()
        },
        //取消设置
        Unset(){
            let vm=this;
            vm.purchase_sn='';
            vm.real_purchase_sn='';
            vm.delivery_time='';
            vm.arrive_time='';
            vm.batch='';
        },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.pagesize=val
          vm.getBatchData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.BatchData.splice(0)
          vm.page=val
          vm.getBatchData()
      },
    //去往汇总页面
    goSummaryPage(purchase_sn){
        this.$router.push('/batchSummary?purchase_sn='+purchase_sn+'&isBatchSte=isBatchSte');
    },
  }
}
</script>

<style>
.status{
    cursor: pointer;
}
.batchSetting_b .xiuzheng{
    width: 88px;
    height: 30px;
    line-height: 30px;
    border-radius: 10px;
    display: inline-block;
    background-color: #4677c4;
    color: #fff;
}
</style>
<style scoped lang=less>
@import '../../css/taskModule.less';
</style>
