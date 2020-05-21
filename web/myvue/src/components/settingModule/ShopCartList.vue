<template>
  <div class="ShopCartList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                        <el-row>
                            <el-col :span="12"> 
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;购物车列表</span>
                            </el-col>
                            <el-col :span="12" class="fontRight">
                                <span class="bgButton MR_ten" @click="dialogVisibleUp=true;openBox()">上传购物车</span>
                            </el-col>
                        </el-row>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>购物车列表</span></div></el-col>
                        <el-col :span="3"><div>购物车日期：</div></el-col>
                        <el-col :span="3" >
                            <div>
                                <el-date-picker class="w_ratio" v-model="cart_date" value-format="yyyy-MM-dd" type="date" placeholder="请选择购物车日期"></el-date-picker>
                            </div>
                        </el-col>
                        <el-col :span="3"><div>商品代码：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erp_prd_no" placeholder="请输入商品代码"></el-input></div></el-col>
                        <el-col :span="3"><div>商品名称：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                    </el-row>
                    <el-row class="fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" :offset="3"><div>商品规格码：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                        <el-col :span="3"><div>商家编码：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                        <el-col :span="3"><div>参考码：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erp_ref_no" placeholder="请输入参考码"></el-input></div></el-col>
                    </el-row>
                    <el-row class="MB_ten">
                        <el-col :span="24">
                            <div class="fontRight">
                                <span class="bgButton MR_ten" @click="getDataList()">搜索</span>
                                <span class="bgButton MR_ten" @click="dialogVisibleUp=true;openBox()">上传购物车</span>
                            </div>
                        </el-col>
                    </el-row>
                    <el-dialog title="上传购物车" :visible.sync="dialogVisibleUp" width="700px">
                        <el-row class="lineHeightForty MB_twenty" style="height:117px;">
                            <div class="d_I_B MR_twenty ML_twenty" style="vertical-align: 100px;"><span class="redFont">*</span>上传文件：</div>
                            <div class="d_I_B MR_Four">
                                <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                    <div class="file">
                                    <img src="../../image/upload.png"/>
                                    <span v-if="fileName==''">点击上传文件</span>
                                    <span v-if="fileName!=''">{{fileName}}</span>
                                    <input class="w_h_ratio" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                    </div>
                                </form>
                            </div>
                            <div class="d_I_B MR_twenty" style="vertical-align: 100px;"><span class="redFont">*</span>购物车日期：</div>
                            <div class="d_I_B" style="vertical-align: 100px;">
                                <el-date-picker class="w_ratio" v-model="cartDate" value-format="yyyy-MM-dd" type="date" placeholder="请选择购物车日期"></el-date-picker>
                            </div>
                        </el-row>
                            <el-button type="primary" style="float: right;margin-right: 25px;" @click="confirmUpData()">确 定</el-button>
                            <el-button type="info" style="float: right;margin-right: 10px;" @click="dialogVisibleUp = false;">取 消</el-button>
                        <span style="float: right;margin-right: 132px;" class="notBgButton" @click="d_download()">下载购物车商品模板</span>
                    </el-dialog>
                    <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>购物车日期</th>
                                <th>商品名称</th>
                                <th>商品规格码</th>
                                <th>商家编码</th>
                                <th>商品参考码</th>
                                <th>商品代码</th>
                                <th>商品分类</th>
                                <th>搭配比例</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>{{item.cart_date}}</td>
                                <td class="overOneLinesHeid fontLift" style="width:250px;">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:250px;">&nbsp;&nbsp;{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td>{{item.spec_sn}}</td>
                                <td>{{item.erp_merchant_no}}</td>
                                <td>{{item.erp_ref_no}}</td>
                                <td>{{item.erp_prd_no}}</td>
                                <td>{{item.cat_name}}</td>
                                <td>{{item.match_scale}}</td>
                            </tr>
                        </tbody>
                    </table>
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
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      //搜索 
      cart_date:'',
      goods_name:'',
      spec_sn:'',
      erp_merchant_no:'',
      erp_ref_no:'',
      erp_prd_no:'',
      total:0,//默认数据总数 
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1 
      //上传购物车商品
      fileName:'',
      dialogVisibleUp:false,
      cartDate:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
      //
    getDataList(){
        let vm = this;
        axios.post(vm.url+vm.$getShopCartListURL,
            {
                "page_size":vm.pagesize,
                "page":vm.page,
                "cart_date":vm.cart_date,
                "goods_name":vm.goods_name,
                "spec_sn":vm.spec_sn,
                "erp_merchant_no":vm.erp_merchant_no,
                "erp_ref_no":vm.erp_ref_no,
                "erp_prd_no":vm.erp_prd_no,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.tableData=[];
                vm.total=0;
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
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
    openBox(){
        let vm = this;
        vm.cartDate='';
    },
    //选择表格
    SelectFile(){
        let vm=this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    //确认上传 
    confirmUpData(){
        let vm=this;
        var formDate = new FormData($("#forms")[0]);
        if(vm.cartDate==''){
            vm.$message('购物车日期不能为空！');
            return false;
        }
        if(vm.fileName==''){
            vm.$message('上传的文件不能为空！');
            return false;
        }
        vm.dialogVisibleUp = false;
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
            url: vm.url+vm.$uploadShopCartGoodsURL+"?cart_date="+vm.cartDate,
            type: "POST",
            async: true,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                loa.close();
                if(res.code=='1000'){
                    vm.getDataList();
                    vm.dialogVisibleUp = false;
                    vm.$message(res.msg);
                }else if(res.code=='1001'){
                    sessionStorage.setItem("DSDG",JSON.stringify(res.data));
                    vm.$router.push('/downloadShopDiffGoods');
                    vm.$message(res.msg);
                }else{
                    vm.$message(res.msg);
                }
                $("#file").val('');
                vm.fileName='';
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
    d_download(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.downloadUrl+'/上传购物车商品模板.xls');
    }
  }
}
</script>

<style>
.ShopCartList .el-dialog {
    height:280px !important;
}
.ShopCartList .el-dialog__body {
    padding: 20px 20px;
}
</style>

<style scoped>
@import '../../css/publicCss.css';

</style>
