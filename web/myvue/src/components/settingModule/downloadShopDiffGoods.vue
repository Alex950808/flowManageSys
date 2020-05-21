<template>
  <div class="downloadShopDiffGoods">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <el-row>
                            <el-col :span="12">
                                <span class="bgButton MR_ten" @click="backUpPage()">返回上一页</span><span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;购物车新品列表</span>
                            </el-col>
                            <el-col :span="12" class="fontRight">
                                <span class="bgButton MR_ten" @click="d_table()">下载购物车新品列表</span>
                            </el-col>
                        </el-row>
                    </div>
                    <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>品牌</th>
                                <th>商家编码</th>
                                <th>商品参考码</th>
                                <th>商品代码</th>
                                <th>购物车名称</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td class="overOneLinesHeid fontLift" style="width:250px;">
                                    <el-tooltip class="item" effect="light" :content="item.brand_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:250px;">&nbsp;&nbsp;{{item.brand_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td>{{item.erp_merchant_no}}</td>
                                <td>{{item.erp_ref_no}}</td>
                                <td>{{item.erp_prd_no}}</td>
                                <td>{{item.cat_name}}</td>
                            </tr>
                        </tbody>
                    </table>
                  <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import { exportExcel } from '@/filters/publicMethods.js'
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        vm.tableData=JSON.parse(sessionStorage.getItem("DSDG"));
    },
    //下载表格 
    d_table(){
        let vm=this;
        let load = Loading.service({fullscreen: true, text: '拼命下载中....'});
        let headersToken=sessionStorage.getItem("token");
        let time = new Date()
        var month = time.getMonth()+1;
        var date = time.getDate(); 
        let tableName = "购物车新品"+"_"+vm.checkTime(month)+"."+vm.checkTime(date);
        axios({ // 用axios发送post请求
          method: 'post',
          url: vm.url+vm.$downloadShopDiffGoodsURL, // 请求地址
          data: {"diff_goods_info":vm.tableData,}, // 参数
          responseType: 'blob', // 表明返回服务器返回的数据类型 
          headers:vm.headersStr,
        }).then((res) => { // 处理返回的文件流
            exportExcel(res,tableName+".xls")
            load.close();
        })
    },
    checkTime(time){
        if(time<10){
            time = '0'+time
        }
        return time
    },
    //返回上一页 
    backUpPage(){
        let vm = this;
        vm.$router.push('/ShopCartList');
    }
  }
}
</script>

<style scoped>
@import '../../css/publicCss.css';
</style>
