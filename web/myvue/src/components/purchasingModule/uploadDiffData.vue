<template>
  <div class="uploadDiffData_b">
    <!-- 数据修正页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="uploadDiffData bgDiv">
                <el-row>
                    <el-col :span="22" :offset="1">
                        <div class="title listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;数据修正</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                        </div>
                        <div class="content" v-for="item in BatchData">
                            <div class="content_text lineHeightForty">
                                <el-col :span="21">
                                    <!-- <span class="stage">{{item.title_info.purchase_id}}期</span><span class="number">{{item.title_info.purchase_sn}}</span> -->
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
                                    <td class="widthOneFiveHundred" v-if="real.batch_cat!='2'||index==0">
                                        <span type="text" @click="openProjectile(item.title_info.purchase_sn,real.real_purchase_sn,real.batch_cat)" class="xiuzheng" style="cursor: pointer;">修正数据</span>
                                    </td>
                                    
                                </tr>
                            </table>
                        </div>
                        <el-dialog title="修正" :visible.sync="dialogVisible" width="500px">
                            <span>
                            <el-row>
                                <el-col :span="10" :offset="7">
                                    <template>
                                        <el-select v-model="ID" placeholder="选择ID">
                                            <el-option v-for="item in IDS" :key="item.value" :label="item.label" :value="item.ID">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row>
                                <el-col :span="8" :offset="9">
                                    <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                    <div class="file">
                                        <img src="../../image/upload.png"/>
                                        <span v-if="fileName==''">点击上传文件</span>
                                        <span v-if="fileName!=''">{{fileName}}</span>
                                        <input style="height:100%;width:100%;" id="file" @change="selectTheFile()" type="file"  name="upload_file"/>
                                    </div>
                                    </form>
                                </el-col>
                            </el-row>
                            </span>
                            <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisible = false;cancel()">取 消</el-button>
                            <el-button type="primary" @click="dialogVisible = false;confirmShow()">确定上传</el-button>
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
        <el-dialog title="提示" :visible.sync="tipsDialogVisible" width="800px">
            <div v-for="(item,index) in tipsStr" style="display:inline-block"><span>{{item}}<span v-if="index!=tipsStr.length-1">，</span></span></div>
            <span slot="footer" class="dialog-footer">
                <el-button type="info" @click="tipsDialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="tipsDialogVisible = false">确 定</el-button>
            </span>
        </el-dialog>
        <div class="confirmPopup_b" v-if="show">
            <div class="confirmPopup">
                <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                    &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
                </div>
            请您确认是否要修正数据？
            <div class="confirm"><el-button  @click.once="uploadDetailed()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
            </div>
        </div>
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
    data() {
      return {
        url: `${this.$baseUrl}`,
        BatchData:[] ,  //批次信息
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        search:'',//用户输入数据
        loading:'',
        isShow:false,
        dialogVisible:false,
        ID:'',   //选择ID
        IDS:[],
        fileName:'',
        show:false,
        purchase_sn:'',
        real_purchase_sn:'',
        batch_cat:'',
        //后台消息确认弹框
        tipsDialogVisible:false,
        tipsStr:'',
      }
    },
    mounted(){
        this.getBatchData();
        this.getPurchaseID();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getBatchData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$modifyDataListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search,
                {
                     headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.loading.close()
                vm.BatchData=res.data.data;
                vm.total=res.data.data_num;
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
        //去往汇总页面
        goSummaryPage(purchase_sn){
            this.$router.push('/batchSummary?purchase_sn='+purchase_sn+'&isuploadDiff=isuploadDiff');
        },
        //获取采购ID列表
        getPurchaseID(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            axios.get(vm.url+vm.$userListURL,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                if(res.data.code==1000){
                    res.data.data.forEach(element=>{
                        vm.IDS.push({"label":element.account_number,"ID":element.id});
                    })
                }
            }).catch(function (error) {
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
       //添加的表格数据
        selectTheFile(){
            let vm=this;
            var r = new FileReader();
            var f = document.getElementById("file").files[0];
            r.readAsDataURL(f);
            vm.fileName=f.name;
            
        },

        //上传商品明细
        uploadDetailed(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            var formDate = new FormData($("#forms")[0]); 
            var purchase_sn=vm.$route.query.purchase_sn;
            vm.show=false;
            let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
             $.ajax({
                url: vm.url+vm.$douploadDiffDataURL+"?purchase_sn="+vm.purchase_sn+"&real_purchase_id="+vm.real_purchase_sn+"&user_id="+vm.ID+"&batch_cat="+vm.batch_cat,
                type: "POST",
                async: true,
                cache: false,
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                },
                data: formDate,
                processData: false,
                contentType: false,
                success: function(res) {
                    loa.close();
                    if(res.code==1000){
                        vm.$message('数据上传成功！');
                        vm.cancel();
                        vm.dialogVisible = false;
                        vm.getBatchData();
                    }else{
                        vm.tipsDialogVisible=true;
                        let msg=res.msg.split(',')
                        vm.tipsStr=msg;
                        vm.purchase_sn='';
                        vm.real_purchase_sn='';
                    }
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
        //确认弹框否
        determineIsNo(){
            let vm=this;
            vm.show=false;
        },
        openProjectile(purchase_sn,real_purchase_sn,batch_cat){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.purchase_sn=purchase_sn;
            vm.real_purchase_sn=real_purchase_sn;
            vm.batch_cat=batch_cat;
            axios.post(vm.url+vm.$uploadDiffDataURL,
                {
                    "real_purchase_sn":real_purchase_sn
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                if(res.data.code==1000){
                    vm.dialogVisible = true;
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
        //取消
        cancel(){
            let vm=this;
            vm.ID='';
            $("#file").val('');
            vm.fileName='';
            vm.purchase_sn='';
            vm.real_purchase_sn='';
        },
        //确认弹框是
        confirmShow(){
            let vm=this;
            vm.show=true;
        },
    }
}
</script>
<style scoped>
.status{
    cursor: pointer;
}
.uploadDiffData .file {
    position: relative;
    display: inline-block;
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 1px solid #ccc;
    overflow: hidden;
    text-decoration: none;
    text-indent: 0;
    line-height: 50px;
    text-align: center;
    margin-top: 25px;
}
.uploadDiffData .file img{
  padding-top: 20px;
}
.uploadDiffData .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 19px;
}
.uploadDiffData .file input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
}
.xiuzheng{
    width: 88px;
    height: 30px;
    line-height: 30px;
    border-radius: 10px;
    display: inline-block;
    background-color: #4677c4;
    color: #fff;
}
.uploadDiffData_b .confirmPopup_b{
    width: 100%;
    height: 100%;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
}
.uploadDiffData_b .confirmPopup{
  width: 400px;
  height: 189px;
  margin: 375px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 999;
  background-color: #fff;
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.uploadDiffData_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.uploadDiffData_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    background: #00C1DE;
    color: #fff;
    cursor: pointer;
}
.uploadDiffData_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    background: #00C1DE;
    color: #fff;
    cursor: pointer;
}
.uploadDiffData_b .confirmTitle{
    display: inline-block;
    width: 400px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.titleText{
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.uploadDiffData_b .el-icon-close{
    margin-left: 270px;
}
 .coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}   
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>