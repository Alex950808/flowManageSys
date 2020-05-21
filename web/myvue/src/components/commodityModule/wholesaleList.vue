<template>
  <div class="wholesaleList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>大批发报价</span></div></el-col>
                        <el-col :span="3"><div>商品名称</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                        <el-col :span="3"><div>平台条码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="platform_no" placeholder="请输入平台条码"></el-input></div></el-col>
                        <el-col :span="3"><div>报价单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="wholesale_sn" placeholder="请输入报价单号"></el-input></div></el-col>
                        <el-col :span="3" ><div><span class="bgButton MR_ten" @click="getDataList()">搜索</span></div></el-col>
                    </el-row>
                    <el-row class="fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" :offset="3"><div>规格码</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="spec_sn" placeholder="请输入规格码"></el-input></div></el-col>
                        <el-col :span="15"><div class="fontRight"><span class="bgButton" @click="openBox()">导入大批发报价</span></div></el-col>
                    </el-row>
                    <el-dialog title="大批发数据上传" :visible.sync="dialogVisibleUpData" width="700px">
                        <span class="notBgButton" style="position: absolute;left: 422px;bottom: 182px;z-index: 999;" @click="d_table()">大批发报价SKU导入模板.xlsx</span>
                        <el-row class="MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>销售用户：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="userName" placeholder="请选择销售用户">
                                            <el-option v-for="item in userNameList" :key="item.sale_user_id" :label="item.user_name" :value="item.sale_user_id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>折扣名称：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="discountName" placeholder="请选择折扣名称">
                                            <el-option v-for="item in discountNameList" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="4">
                                <div class="MT_Thirty">汇率</div>
                                <div style="margin-top: 40px">预计到港时间</div>
                            </el-col>
                            <el-col :span="6">
                                <div class="MT_twenty"><el-input v-model="usd_cny_rate" type="number" placeholder="请输汇率"></el-input></div>
                                <div class="MT_twenty"><el-date-picker v-model="predict_pot_time" style="width:100%;" value-format="yyyy-MM-dd" type="date" placeholder="预计到港时间"></el-date-picker></div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="MT_Thirty"><span class="redFont">*</span>上传文件：</div></el-col>
                            <el-col :span="6" style="text-align: right;">
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
                        <el-row class="MB_twenty">
                            <el-col :span="4">
                                <div class="MT_ten"><span class="redFont">*</span>海运价格($/K)</div>
                            </el-col>
                            <el-col :span="6">
                                <el-input v-model="sea_trans" type="number" placeholder="请输入海运价格"></el-input>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="4">
                                <div class="MT_ten"><span class="redFont">*</span>空运价格($/K)</div>
                            </el-col>
                            <el-col :span="6">
                                <el-input v-model="air_trans" type="number" placeholder="请输空运价格"></el-input>
                            </el-col>
                            <el-col :span="7" :offset="6" style="margin-top: 10px;"><span class="fontWeight redFont">*一次最多导入500条数据</span></el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleUpData = false;">取 消</el-button>
                            <el-button type="primary" @click="confirmDataUp()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-row>
                        <table class="tableTitle">
                            <thead>
                                <th width="20%">商品名称</th>
                                <th width="20%">商品规格码</th>
                                <th width="20%">平台条码</th>
                            </thead>
                        </table>
                    </el-row>
                    <table class="tableTitleTwo">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="20%">商品规格码</th>
                            <th width="20%">平台条码</th>
                        </thead>
                    </table>
                    <el-row>
                        <div v-for="(item,index) in tableData" :class="['content_text','content_text'+index]">
                            <el-row class="userInformation">
                                <el-col :span="6"><div class="notBgButton">报价单号：{{item.wholesale_sn}}</div></el-col>
                                <el-col :span="6"><div class="">预计到港时间：{{item.estimate_time}}</div></el-col>
                                <el-col :span="3"><div class="">海运：{{item.sea_trans}}($)</div></el-col>
                                <el-col :span="3"><div class="">空运：{{item.air_trans}}($)</div></el-col>
                                <el-col :span="3"><div class="">sku数量：{{item.goodsCount}}</div></el-col>
                            </el-row> 
                            <el-row class="userInformation">
                                <el-col :span="6"><div class="">创建时间：{{item.create_time}}</div></el-col>
                                <el-col :span="6"><div class="">美元兑人民币汇率：{{item.usd_cny_rate}}</div></el-col>
                                <el-col :span="3"><div class="">上传用户：{{item.adminName}}</div></el-col>
                                <el-col :span="3"><div class="">销售用户：{{item.sale_user}}</div></el-col>
                                <el-col :span="3"><div class="">代采点：{{item.user_disc}}</div></el-col>
                                <el-col :span="3" class="fontRight">
                                    <span class="notBgButton" @click="viewDetails(item.wholesale_sn)">报价详情</span>
                                    <i class="el-icon-arrow-down notBgButton" :title="'点击展示更多 共:'+item.goods_data.length+'条商品'" @click="pullOrUp(index,$event,item.goods_data.length)"></i>  
                                </el-col>
                            </el-row> 
                            <table style="width:100%;text-align: center">
                                <tr v-for="good in item.goods_data">
                                    <td width="20%" class="overOneLinesHeid fontLift">
                                        <el-tooltip class="item" effect="light" :content="good.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{good.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td width="20%">{{good.spec_sn}}</td>
                                    <td width="20%">{{good.platform_no}}</td>
                                </tr>
                            </table>
                        </div>
                    </el-row>
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
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //搜索字段
      goods_name:'',
      platform_no:'',
      wholesale_sn:'',
      spec_sn:'',
      //上传报价字段
      sea_trans:'',
      usd_cny_rate:'',
      predict_pot_time:'',
      fileName:'',
      dialogVisibleUpData:false,
      userNameList:[],
      discountNameList:[],
      userName:'',
      discountName:'',
      air_trans:'',
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
        axios.post(vm.url+vm.$wholesaleListURL,
            {
                "goods_name":vm.goods_name,
                "platform_no":vm.platform_no,
                "wholesale_sn":vm.wholesale_sn,
                "spec_sn":vm.spec_sn,
                "page_size":vm.pagesize,
                "page":vm.page,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.offerList.data.length!=0){
                vm.tableData=res.data.offerList.data;
                vm.total=res.data.offerList.total;
                vm.userNameList=res.data.saleUserInfo;
                for(var key in res.data.discType){
                    vm.discountNameList.push({"name":res.data.discType[key],"id":key})
                }
                // vm.discountNameList=res.data.discType;
                vm.isShow=false;
            }else{
                vm.tableData=[];
                vm.total=0;
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
     //点击下拉或者收起表格 
    pullOrUp(index,event,length){
        let vm = this;
        let content_text_height=$(event.target).parent().parent().parent().height();
        if(content_text_height==187){
            $(".content_text"+index+"").css({
                "transition-property:":"height",
                "transition-duration":"5s",
            })
            $(".content_text"+index+"").height('auto');
            $(event.target).addClass("el-icon-arrow-up");
            $(event.target).removeClass("el-icon-arrow-down");
            $(event.target).attr("title","点击收起")
        }else{
            $(".content_text"+index+"").height('187');
            $(event.target).addClass("el-icon-arrow-down");
            $(event.target).removeClass("el-icon-arrow-up");
            $(event.target).attr("title","点击展示更多 共"+length+"条商品");
        }
    },
    //查看详情
    viewDetails(sn){
        let vm = this;
        this.$router.push('/getWholeDetail?wholesale_sn='+sn);
    },
    //打开商品上传弹框
    openBox(){
        let vm = this;
        vm.dialogVisibleUpData = true;
        // vm.freight='';
        vm.usd_cny_rate='';
        vm.predict_pot_time='';
        vm.fileName='';
        vm.userName='';
        vm.discountName='';
        vm.sea_trans='';
        vm.air_trans='';
    },
    //下载模板表格
    d_table(){
        let vm = this;
        window.open(vm.downloadUrl+'/大批发报价SKU导入模板.xlsx');
    },
    SelectFile(){
        let vm=this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    //确认上传 
    confirmDataUp(){
        let vm = this;
        var formDate = new FormData($("#forms")[0]);
        if(vm.userName==''){
            vm.$message('请输入销售用户后上传！');
            return false;
        }
        if(vm.discountName==''){
            vm.$message('请输入折扣名称后上传！');
            return false;
        }
        if(vm.sea_trans==''){
            vm.$message('请输入海运价格后上传！');
            return false;
        }
        if(vm.air_trans==''){
            vm.$message('请输入空运价格后上传！');
            return false;
        }
        vm.dialogVisibleUpData = false;
        let load = Loading.service({fullscreen: true, text: '拼命上传中....'});
         $.ajax({
            url: vm.url+vm.$importWholesaleSkuURL+"?sea_trans="+vm.sea_trans+"&usd_cny_rate="+vm.usd_cny_rate+"&predict_pot_time="+vm.predict_pot_time
            +"&air_trans="+vm.air_trans+"&sale_user_id="+vm.userName+"&disc_type="+vm.discountName,
            type: "POST",
            async: true,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                load.close();
                $("#file1").val('');
                if(res.code=='2000'){
                    vm.$message(res.msg);
                    vm.getDataList();
                }else{
                    vm.$message(res.msg);
                }
            }
        }).catch(function (error) {
            load.close();
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
    },
  }
}
</script>

<style>
.wholesaleList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.wholesaleList .content_text{
    width: 100%;
    height: 187px;
    overflow: hidden;
    border: 1px solid #ebeef5;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
}
.wholesaleList .userInformation{
    text-align:center;
    height: 50px;
    line-height: 50px;
    background-color: #f8fafb;
    font-weight: bold;
}
.wholesaleList .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.wholesaleList .file {
    position: relative;
    display: inline-block;
    text-align: center;
}
.wholesaleList .file img{
  padding-top: 20px;
}
.wholesaleList .file span{
  display: inline-block;
  font-size: 15px;
}
.wholesaleList .file input {
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;
}
.wholesaleList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.wholesaleList .el-icon-arrow-up{
    display: inline-block !important;
}
.wholesaleList .is-show-close{
    display: inline-block !important;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>