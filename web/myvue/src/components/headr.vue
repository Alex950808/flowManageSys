<template>
    <div class="headr PR">
        <div></div>
        <el-row class="topTitle">
            <el-col :span="11">
                <div class="top_title d_I_B"><img src="../image/haidai.png"/>&nbsp;&nbsp;&nbsp;&nbsp;{{title}}&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div class="d_I_B versionNum"><span>前端版本：{{versionInfo.web_num}}</span><br><span>服务器版本：{{versionInfo.serial_num}}</span></div>
            </el-col>
            <el-col :span="13">
                <div class="SignOut fontRight MR_twenty">
                    <span v-if="isToken">
                        <img v-show="img==''" class="verticalAlign" @click="openBox()" src="../image/user.png"/>&nbsp;&nbsp;
                        <img v-show="img!=''" class="verticalAlign" style="border-radius: 50%;" @click="openBox()" :src="titleImg_url"/>
                        {{userName.user_name}}
                    </span>
                    <span v-if="isToken"><img class="verticalAlign" src="../image/department.png"/>&nbsp;&nbsp;{{userName.department_name}}</span>
                    <span class="d_I_B L_H_T B_R_F Cursor ML_twenty" @click="SignOut()" v-if="isToken" title="退出登录"><i class="el-icon-switch-button"></i></span>
                </div>
            </el-col>
        </el-row>
        <el-dialog title="编辑管理员" :visible.sync="dialogVisibleEditUser" width="800px">
            <el-row class="MB_twenty">
                <el-col :span="4">
                    <div class="MT_Thirty">用户名</div>
                </el-col>
                <el-col :span="6">
                    <div class="MT_twenty"><el-input placeholder="请输入用户名" v-model="username" :disabled="true"></el-input></div>
                </el-col>
                <el-col :span="4" :offset="1"><div class="MT_Thirty">昵称</div></el-col>
                <el-col :span="6" style="text-align: right;">
                    <div class="MT_twenty"><el-input placeholder="请输入昵称" v-model="nickname"></el-input></div>
                </el-col>
            </el-row>
            <el-row class="MB_twenty">
                <el-col :span="4">
                    <div class="MT_Thirty">头像</div>
                </el-col>
                <el-col :span="3">
                    <div class="MT_twenty" style="width:100px;height:100px;">
                        <img v-if="img!=''" class="w_h_ratio" :src="img_url">
                        <img v-if="img==''" src="../image/upDateImg.jpg">
                        <form id="forms" method="post" enctype="multpart/form-data" class="formData" style="display: inline-block">
                            <input id="file" type="file" @change="upData()"  />
                        </form>
                    </div>
                </el-col>
                <!-- <el-col :span="2" :offset="1" style="position: relative;">
                    <span class="d_I_B uploadHead blueFont Cursor">上传头像</span>
                </el-col> -->
            </el-row>
            <span slot="footer" class="dialog-footer">
                <el-button type="info" @click="dialogVisibleEditUser = false;">取 消</el-button>
                <el-button type="primary" @click="editUser()">确 定</el-button>
            </span>
        </el-dialog>
        <div style="clear: both;"></div>
    </div>
</template>
<script>
import axios from 'axios'
import { Loading } from 'element-ui';
export default {
    data () {
        return {
            url: `${this.$baseUrl}`,
            title: '海代网供应链管理系统',
            imgUrl:`${this.$imgUrl}`,
            img_url:'',
            isToken:'',
            userName:'',
            loggerList:[],
            versionInfo:'',
            department_name:'',
            //编辑用户信息
            dialogVisibleEditUser:false,
            username:'',
            nickname:'',
            imageUrl:'',
            titleImg_url:'',
            img:'',
        }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.isShow();
    },
    methods:{
        //退出登录 
        SignOut(){
            sessionStorage.setItem("token","");
            sessionStorage.setItem("web_name","indexPage");
            this.$store.commit('romWebName');
            this.$router.push('/');
        },
        isShow(){
            let vm=this;
            vm.isToken=sessionStorage.getItem("token");
            vm.loggerList=JSON.parse(sessionStorage.getItem("loggerList"))
            vm.userName=JSON.parse(sessionStorage.getItem("userName"));
            vm.versionInfo=JSON.parse(sessionStorage.getItem("versionInfo"));
            vm.img_url=vm.imgUrl+vm.userName.user_img;
            vm.username=vm.userName.user_name;
            vm.nickname=vm.userName.nickname;
            vm.titleImg_url=vm.imgUrl+vm.userName.user_img;
            vm.img=vm.userName.user_img;
            if(vm.userName.user_img!=''){
                vm.imageUrl=vm.userName.user_img;
            }
            if(vm.isToken==""){
                vm.isToken==true;
            }else{
                vm.isToken==false;
            }
        },
        bbb(){
            $(".aaa").remove()
        },
        //打开编辑用户名弹框
        openBox(){
            let vm = this;
            vm.dialogVisibleEditUser=true;
            vm.img_url=vm.titleImg_url;
            $("#file").val('');
        },
        //上传头像
        upData(){
            let vm = this;
            var formDate = new FormData($("#forms")[0]);
            formDate.append('user_img', document.getElementById("file").files[0]);
            $.ajax({
            url: vm.url+vm.$uploadAdminUserImgURL+"?user_id="+vm.userName.id+"&user_name="+vm.userName.user_name,
            type: "POST",
            async: false,
            // cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.code=='1000'){
                    vm.img_url=vm.imgUrl+res.user_img;
                    vm.imageUrl=res.user_img;
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
        //编辑用户 
        editUser(){
            let vm = this;
            vm.dialogVisibleEditUser=false;
            let load = Loading.service({fullscreen: true, text: '拼命上传中....'});
            axios.post(vm.url+vm.$eidtAdminUserURL,
                {
                    "id":vm.userName.id,
                    "role_id":vm.userName.role_id,
                    "user_img":vm.imageUrl,
                    "nickname":vm.nickname,
                },
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){//
                console.log(res);
                if(res.data.code=="1000"){
                    axios.get(vm.url+vm.$getAdminUserInfoURL+"?user_id="+vm.userName.role_id,
                        {
                            headers:vm.headersStr,
                        },
                    ).then(function(res){
                        load.close();
                        sessionStorage.setItem("userName",JSON.stringify(res.data.data));
                        vm.userName=JSON.parse(sessionStorage.getItem("userName"));
                        vm.titleImg_url=vm.imgUrl+res.data.data.user_img;
                        vm.img=vm.imgUrl+res.data.data.user_img;
                    })
                }else{
                    load.close();
                    vm.$message(res.data.msg);
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
    }
}
</script>
<style scoped>
.headr .p_a_m{
    /* position: fixed;
    top: 0;
    right: 20%;
    z-index: 999; */
    color: #000;
    font-weight: 100;
    /* font-size: 16px; */
    background: rgb(225, 234, 248);
    /* border-radius:10%; */
}
.headr .versionNum{
    line-height: 24px;
    height: 24px;
    vertical-align: -10px;
    font-size: 14px;
}
.uploadHead{
    margin-top: 100px;
}
.formData{
    display: inline-block;
    position: absolute;
    left: 127px;
    top: 20px;
    opacity: 0;
    width: 100px;
    height: 100px;
}
#file{
    position: absolute;
    left: 0px;
    /* top: 20px; */
    opacity: 0;
    width: 100px;
    height: 100px;
}
.verticalAlign{
    width:26px;
    height: 26px;
}
</style>

<style scoped lang=less>
@import '../css/demo.less';
</style>