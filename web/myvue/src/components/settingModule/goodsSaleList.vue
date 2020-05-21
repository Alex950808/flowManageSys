<template>
  <div class="goodsSaleList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;商品报价列表</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openUpLoad()">上传商品报价</span>
                    </div>
                    <el-dialog title="上传商品报价" :visible.sync="dialogVisibleUp" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>报价日期：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-date-picker style="width:190px;" v-model="sale_date" type="date" value-format="yyyy-MM-dd" placeholder="请选择报价日期"></el-date-picker>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>上传文件：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                        <div class="file">
                                        <img src="../../image/upload.png"/>
                                        <span v-if="fileName==''">点击上传文件</span>
                                        <span v-if="fileName!=''">{{fileName}}</span>
                                        <input class="w_h_ratio" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                        </div>
                                    </form>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleUp = false;">取 消</el-button>
                            <el-button type="primary" @click="uploadGoodsSale()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <table class="MB_twenty" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>商品名称</th>
                                <th>商家编码</th>
                                <th>商品代码</th>
                                <th>参考码</th>
                                <th>商品规格码</th>
                                <th>报价日期</th>
                                <th>商品报价</th>
                                <th>币种</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td class="overOneLinesHeid" style="width:300px;" :title="item.goods_name">
                                    <span style="-webkit-box-orient: vertical;width:300px;">{{item.goods_name}}</span>
                                </td>
                                <td>{{item.erp_merchant_no}}</td>
                                <td>{{item.erp_prd_no}}</td>
                                <td>{{item.erp_ref_no}}</td>
                                <td>{{item.spec_sn}}</td>
                                <td>{{item.sale_date}}</td>
                                <td>{{item.sale_price}}</td>
                                <td>{{item.currency_name}}</td>
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
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //上传商品报价
      sale_date:'',
      dialogVisibleUp:false,
      fileName:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$getGoodsSaleListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
                vm.isShow=false;
            }else if(res.data.code==1002){
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
    //打开报价选择框
    openUpLoad(){
        let vm = this;
        vm.dialogVisibleUp=true;
    },
    //选择表格 
    SelectFile(){
        let vm=this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    uploadGoodsSale(){
        let vm = this;
        var formDate = new FormData($("#forms")[0]);
        if(vm.sale_date==''){
            vm.$message('请选择报价日期后上传！');
            return false;
        }
        if(vm.fileName==''){
            vm.$message('请选择文件后上传！');
            return false;
        }
        vm.dialogVisibleUp=false;
        $.ajax({
        url: vm.url+vm.$uploadGoodsSaleInfoURL+"?sale_date="+vm.sale_date,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            if(res.code=='1000'){
                vm.getDataList();
                $("#file1").val('');
                vm.fileName='';
                vm.$message(res.msg);
            }else if(res.code=='1001'){
                sessionStorage.setItem("SGDL",JSON.stringify(res.data));
                vm.$router.push('/downloadSaleDiffGoods');
                vm.$message(res.msg);
            }else{
                vm.$message(res.msg);
                $("#file1").val('');
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
    }
  }
}
</script>

<style scoped>
@import '../../css/publicCss.css';
</style>
