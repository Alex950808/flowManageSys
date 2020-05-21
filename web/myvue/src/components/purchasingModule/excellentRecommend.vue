<template>
  <div class="excellentRecommend_b">
    <!-- 优采推荐页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="excellentRecommend bgDiv">
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="purchaseDiscount listTitleStyle">
                            <el-col :span="12">
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;优采推荐</span>
                                <searchBox @searchFrame='searchFrame'></searchBox>
                            </el-col>
                            <el-col :span="12" class="fontRight">
                                <div class="notBgButton" @click="downloadTemplate('批量导出商品优采推荐折扣.xlsx')">下载模板表格</div>
                                <div class="bgButton" @click="upDataBox()">批量导出商品优采推荐折扣</div>
                            </el-col>
                        </div>
                        <ul>
                            <li v-for="item in tableData">
                                <div class="content">
                                    <div class="content_text lineHeightForty">
                                        <el-col :span="21">
                                            <span class="stage">{{item.number_sn}}期</span><span class="number">{{item.purchase_sn}}</span>
                                            <span class="ML_twenty">提货日 ：{{item.delivery_time}}</span>
                                        </el-col>
                                        <el-col :span="3">
                                            <span class="notBgButton" @click="goSummaryPage(item.purchase_sn)"><i class="el-icon-view"></i>&nbsp;&nbsp;查看数据汇总</span>
                                        </el-col>
                                    </div>
                                    <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th>需求单编号</th>
                                                <th>sku数量</th>
                                                <th>商品需求数量</th>
                                                <th>商品可采数量</th>
                                                <th>生成时间</th>
                                                <th>状态</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for=" itemO in item.demand_info">
                                                <td>{{itemO.demand_sn}}</td>
                                                <td>{{itemO.sku_num}}</td>
                                                <td>{{itemO.goods_num}}</td>
                                                <td>{{itemO.total_may_num}}</td>
                                                <td>{{itemO.create_time}}</td>
                                                <td v-if="itemO.status==1">待挂期</td>
                                                <td v-if="itemO.status==2">待分配</td>
                                                <td v-if="itemO.status==3">已分配</td>
                                                <td @click="goGoodsId(item.purchase_sn,itemO.demand_sn)" style="cursor: pointer;"><span class="bgButton">查看优采</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </li>
                        </ul>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-col>
                </el-row>
            </div>
        </el-col>
        <upDataButtonByBox :titleStr="titleStr" @GFFconfirmUpData="GFFconfirmUpData"></upDataButtonByBox>
        <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox'
import upDataButtonByBox from '@/components/UiAssemblyList/upDataButtonByBox'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
    components:{
        searchBox,
        upDataButtonByBox,
        customConfirmationBoxes
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        downloadUrl:`${this.$downloadUrl}`,
        search:'',//用户输入数据
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        isShow:false,
        loading:'',
        headersStr:'',
        contentStr:'请确认是否下载',
        titleStr:'批量上传',
        d_url:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getExcellentData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    },
    methods: {
        getExcellentData(){
        let vm=this;
        axios.get(vm.url+vm.$recommendListURL+"?query_sn="+vm.search+"&start_page="+vm.page+"&page_size="+vm.pagesize,
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          vm.tableData=res.data.data
          vm.total=res.data.data_num
          vm.loading.close();
          if(res.data.code==1002){
              vm.loading.close()
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
      goGoodsId(purchase_sn,demand_sn){
        this.$router.push('/excellentRecommendDetails?purchase_sn='+purchase_sn+'&demand_sn='+demand_sn);
      },
      //打开上传框 
      upDataBox(){
            $(".upDataButtonByBox_b").fadeIn();
      },
      //确认弹框否
        determineIsNo(){
            $(".upDataButtonByBox_b").fadeOut();
        },
      // 确定批量上传文件
      GFFconfirmUpData(formDate){
            let vm=this;
            var formDate = new FormData($("#forms")[0]);
            vm.determineIsNo();
            $.ajax({
                url: vm.url+vm.$discountExportByGoodsURL,
                type: "POST",
                async: true,
                cache: false,
                headers:vm.headersStr,
                data: formDate,
                processData: false,
                contentType: false,
                success: function(res) {
                    $(".confirmPopup_b").fadeIn();
                    vm.d_url=res;
                }
            });
      },
      confirmationAudit(){
          let vm=this;
          window.open(vm.downloadUrl+"/"+vm.d_url);
          $(".confirmPopup_b").fadeOut();
      },
       //下载表格模板
      downloadTemplate(str){
        let vm=this;
        window.open(vm.downloadUrl+"/"+str);
      },
      //搜索框
      searchFrame(e){
          let vm=this;
          vm.search=e;
          vm.page=1;
          vm.getExcellentData();
      },
      //去往汇总页面
      goSummaryPage(purchase_sn){
          this.$router.push('/openDataCollection?purchase_sn='+purchase_sn+'&isExcellent=isExcellent');
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getExcellentData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getExcellentData()
      },
    }
}
</script>
<style>
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>