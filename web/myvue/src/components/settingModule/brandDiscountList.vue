<template>
  <div class="brandDiscountList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1" class="select">
                    <div class="tableTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;品牌折扣列表</span>
                        <el-input v-model="brand_name" style="width:150px" placeholder="请输入品牌名称"></el-input>
                        <span class="bgButton" @click="searchFrame()">搜索</span>
                    </div>
                    <table class="tableTitle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th width="20%">品牌</th>
                                <th width="20%">渠道名称</th>
                                <th width="20%">成本折扣</th>
                                <th width="20%">追加折扣</th>
                                <th width="20%">最终折扣</th>
                            </tr>
                        </thead>
                    </table>
                    <table class="tableTitleTwo" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th width="20%">品牌</th>
                                <th width="20%">渠道名称</th>
                                <th width="20%">成本折扣</th>
                                <th width="20%">追加折扣</th>
                                <th width="20%">最终折扣</th>
                            </tr>
                        </thead>
                    </table>
                    <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        
                        <tbody v-for="item in tableData">
                            <tr v-for="(dis,index) in item.discountInfo">
                                <td width="20%" class="overOneLinesHeid brandName" v-if="index===0" style="width:300px;border: 1px solid #ccc;" :rowspan="item.discountInfo.length">
                                    <!-- {{item.discountInfo.length}} -->
                                    <el-tooltip class="item" effect="light" :content="item.name" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:300px;">&nbsp;&nbsp;{{item.name,dis.lenght}}</span>
                                    </el-tooltip>
                                </td>
                                <td width="20%">
                                    {{dis.channels_name}}
                                </td>
                                <td width="20%">{{dis.discount}}</td>
                                <td width="20%">{{dis.appendDiscount}}</td>
                                <td width="20%">{{dis.lastDiscount}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="isShow" class="redFont">*如果本页信息为空则说明当月渠道档位折扣还未上传</div>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '../UiAssemblyList/searchBox';
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      notFound,
      searchBox,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数
      brand_name:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.tableTitle();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.post(vm.url+vm.$brandDiscountListURL,
            {
                "pageSize":vm.pagesize,
                "page":vm.page,
                "brand_name":vm.brand_name
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.tableData=[];
            vm.total=0;
            if(res.data.data.length!=0){
                vm.tableData=res.data.data;
                vm.total=res.data.total;
                vm.isShow=false;
            }else{
                vm.isShow=true;
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
    searchFrame(){
        let vm=this;
        vm.page=1;
        vm.getDataList();
    },
    //分页 
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDataList()
    },
    tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=270;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".brandDiscountList .tableTitleTwo").addClass("addclass");
                $(".brandDiscountList .tableTitleTwo").width($(".brandDiscountList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".brandDiscountList .tableTitleTwo").removeClass("addclass");
            }
        })
    },
  }
}
</script>

<style>
/* .brandName:hover{
    background-color: #fff;
} */
table tr:hover .brandName{
    background:#fff!important;
}
.brandDiscountList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.brandDiscountList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>