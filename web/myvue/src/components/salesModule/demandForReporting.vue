<template>
  <div class="demandForReporting_b">
    <!-- 提报需求页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="demandForReporting">
                        <div class="title">
                            <router-link  to='/demandManagement'><span class="bgButton">返回上一级</span></router-link>
                            <span class="upTitle"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;提报需求</span>
                            <!-- <span class="stage">{{`${$route.query.purchase_sn}`}}</span> -->
                        </div>
                        <el-row class="content">
                            <el-col :span="8" :offset="1">
                                <div class="upLoad">
                                    <div>文件上传</div>
                                    <!-- <div class="Detailed">上传需求商品明细</div> -->
                                    <template>
                                        <el-select v-model="userId" placeholder="请选择销售用户">
                                            <el-option v-for="item in userIds" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                    <template>
                                        <div class="block">
                                            <!-- <span class="demonstration">默认</span> -->
                                            <el-date-picker v-model="expireTime" type="date" placeholder="选择到期日期"> </el-date-picker>
                                        </div>
                                    </template>
                                    
                                </div>
                            </el-col>
                            <el-col :span="12">
                                <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                    <!-- <input style="height:100%;width:100%;" id="file1" type="file"  name="goods_file"/> -->
                                    <div class="file">
                                        <img src="../../image/upload.png"/>
                                        <span v-if="fileName==''">点击选择文件</span>
                                        <span v-if="fileName!=''">{{fileName}}</span>
                                        <input style="height:100%;width:100%;" id="file1" type="file" name="upload_file" @change="selectFile()"/>
                                    </div>
                                </form>
                                <div class="Detailed bgButton" @click="confirmShow()">上传文件</div>
                                <div class="Detailed bgButton" @click="d_download()">下载表格模板</div>
                            </el-col>
                        </el-row>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    <div class="confirmPopup_b" style="display:none;">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要上传？
        <div class="confirm"><el-button @click="uploadDisTable()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import $ from 'jquery';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
export default {
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        downloadUrl:`${this.$downloadUrl}`,
        loading: false,
        fileName:'',
        show:false,
        expireTime:'',
        userId:'',
        userIds:[],
      }
    },
    mounted(){
        this.getUserId();
    },
     methods: {
         getUserId(){
             let vm=this;
            vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
            vm.tableData.saleUserInfo.forEach(element=>{
                vm.userIds.push({"label":element.user_name,"id":element.id});
            })
         },
         //选择要上传的文件
         selectFile(){
             let vm=this;
            var r = new FileReader();
            var f = document.getElementById("file1").files[0];
            vm.fileName=f.name;
            r.readAsDataURL(f);
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
        //上传需求商品明细
        uploadDisTable(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            // vm.show=false;
            $(".confirmPopup_b").fadeOut();
            // if(vm.fileName==''){
            //     vm.$message('请选择要上传的文件！');
            //     vm.show=false;
            //     return false;
            // }
            // if(vm.userId==''){
            //     vm.$message('人员ID不能为空');
            //     return false;
            // }
            // if(vm.expireTime==''){
            //     vm.$message('结束时间不能为空');
            //     return false;
            // }
            var formDate = new FormData($("#forms")[0]);
            $.ajax({
            url: vm.url+vm.$demGoodsUpURL+"?sale_user_id="+vm.userId+"&expire_time="+vm.dateToStr(vm.expireTime),
            type: "POST",
            async: false,
            cache: false,
            headers:{
                'Authorization': 'Bearer ' + headersToken,
                'Accept': 'application/vnd.jmsapi.v1+json',
            },
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                vm.show=false;
                if(res.code==2000){
                    vm.$message('上传成功！');
                    vm.$router.push('/demandManagement');
                    if(res.tipMsg){
                        vm.$alert(res.tipMsg, '上传错误提示', {
                            confirmButtonText: '确定',
                            callback: action => {
                            }
                        });
                    }
                }else{
                    vm.$alert(res.msg, {
                        confirmButtonText: '确定',
                        callback: action => {
                        }
                    });
                    $("#file1").val('');
                    vm.fileName='';
                    vm.expireTime='';
                    vm.department='';
                }
                // vm.getCustomerDemand()
            }
            });
            // }
        },
        d_download(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.downloadUrl+'/提报需求-商品明细.xls');
        },
        //确认弹框否
        determineIsNo(){
            let vm=this;
            // vm.show=false;
            $(".confirmPopup_b").fadeOut();
            vm.dialogVisible = false;
        },
        //确认弹框是
        confirmShow(){
            // let vm=this;
            // vm.show=true;
            $(".confirmPopup_b").fadeIn();
        },
    }
}
</script>

<style>
/* @import '../../css/common.css'; */
.demandForReporting_b .Detailed{
    display: inline-block;
    margin-left: 100px;
    cursor: pointer;
}
.demandForReporting_b .file {
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
    /* background-color: #00C1DE; */
    text-align: center;
    margin-top: 80px;
}
.el-message-box {
    width: 100%;
    overflow: auto;
    display: block;
}
.demandForReporting_b .file img{
  padding-top: 20px;
}
.demandForReporting_b .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 19px;
}
.demandForReporting_b .file input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
}
.demandForReporting_b .Detailed{
    vertical-align: 16px;
}
.demandForReporting_b .upLoad{
    margin-top: 30px;
}
.demandForReporting_b .confirmPopup_b{
    width: 100%;
    height: 100%;
    /* background-color:rgba(0, 0, 0, 0.2); */
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
}
.demandForReporting_b .confirmPopup{
  width: 400px;
  height: 189px;
  /* padding-top: 20px; */
  margin: 375px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  /* margin-top: 500px; */
  z-index: 999;
  /* background-color: #fff; */
  /* border-radius: 20px; */
  /* color: #ccc; */
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.demandForReporting_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.demandForReporting_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.demandForReporting_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE;
    color: #fff; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.demandForReporting_b .el-select{
    margin-top: 30px;
    margin-bottom: 35px;
}
.demandForReporting_b .confirmTitle{
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
.confirmPopup_b .el-icon-view{
    color: #fff;
}
.demandForReporting_b .el-icon-close{
    margin-left: 270px;
}

</style>

<style scoped lang=less>
@import '../../css/salesModule.less';
.content_left{
    padding-top: 65px;
}
</style>