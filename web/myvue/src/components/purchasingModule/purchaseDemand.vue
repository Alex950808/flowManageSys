<template>
  <div class="demandForReporting_b">
    <!-- 提报需求页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="demandForReporting">
                <div class="title">
                    <router-link  to='/purchaseList'><span class="back">返回上一级</span></router-link>
                    <span class="upTitle">提报需求</span><span class="stage">{{`${$route.query.purchase_sn}`}}</span>
                </div>
                <el-row class="content">
                    <el-col :span="11" :offset="1">
                        <div class="upLoad">
                            <div>文件上传</div>
                            <!-- <div class="Detailed">上传需求商品明细</div> -->
                            <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                <!-- <input style="height:100%;width:100%;" id="file1" type="file"  name="goods_file"/> -->
                                <div class="file">
                                    <img src="../../image/upload.png"/>
                                    <span v-if="fileName==''">点击选择文件</span>
                                    <span v-if="fileName!=''">{{fileName}}</span>
                                    <input style="height:100%;width:100%;" id="file1" type="file" name="goods_file" @change="selectFile()"/>
                                </div>
                            </form>
                            <div class="Detailed" @click="confirmShow()" v-loading="loading">上传文件</div>
                        </div>
                    </el-col>
                    <el-col :span="7" :offset="5">
                        <!-- <div class="content_right">
                            <span>上传成功：2018-04-26.xle</span>
                        </div> -->
                    </el-col>
                </el-row>
                <div class="addGoods">
                    <div class="title">
                        <span>客户需求汇总明细</span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>客户名</th>
                                <th>商品sku</th>
                                <th>总需求数量</th>
                                <th>创建时间</th>
                                <th>需求单次</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData">
                                <td>{{item.user_name}}</td>
                                <td>{{item.skuNum}}</td>
                                <td>{{item.goodsNum}}</td>
                                <td>{{item.create_time}}</td>
                                <td>{{item.demandNum}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- <div class="confirm">确认上传</div> -->
            </div>
        </el-col>
    <div class="confirmPopup_b" v-if="show">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要提报需求？
        <div class="confirm"><el-button  @click.once="uploadDisTable()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import $ from 'jquery'
export default {
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        loading: false,
        fileName:'',
        show:false,
        headersStr:'',
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.dataTransfer();
    },
     methods: {
         //选择要上传的文件
         selectFile(){
             let vm=this;
            var r = new FileReader();
            var f = document.getElementById("file1").files[0];
            vm.fileName=f.name;
            r.readAsDataURL(f);
         },
        //上传需求商品明细
        uploadDisTable(){
            let vm=this;
            vm.loading=true;
            if(vm.fileName==''){
                vm.loading= false;
                vm.$message('请选择要上传的文件！');
                vm.show=false;
                return false;
            }
            var formDate = new FormData($("#forms")[0]);
            $.ajax({
            url: vm.url+vm.$doUploadDemandURL+"?purchase_sn="+vm.$route.query.purchase_sn,
            type: "POST",
            async: false,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                vm.loading= false;
                vm.show=false;
                if(res.code==2000){
                    vm.$message('上传成功！');
                    if(res.tipMsg){
                        vm.$message(res.tipMsg);
                    }
                    $("#file1").val('');
                }else{
                    vm.$message('上传失败！'+res.msg);
                }
                
                vm.getCustomerDemand()
            }
            });
            // }
        },
        dataTransfer(){
            let vm=this;
            vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
        },
        //获取客户需求汇总明细数据
        getCustomerDemand(){
            let vm=this;
            axios.post(vm.url+vm.$getPageDataURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                res.data.userGoods.forEach(element=>{
                    vm.tableData.push(element);
                })
            }).catch(function (error) {
                if(error.response.status=="401"){
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
            vm.dialogVisible = false;
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
    text-align: center;
    margin-top: 25px;
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
    width: 120px;
    height: 40px;
    background-color: #00C1DE;
    line-height: 40px;
    vertical-align: 16px;
    color: #fff;
    border-radius: 10px;
    text-align: center;
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
  background-color: #fff;
  /* border-radius: 20px; */
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