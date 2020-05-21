<template>
  <div class="addAdminUser_b">
    <el-col :span="24" style="background-color: #fff;">
        <el-row>
            <el-col :span="22" :offset="1">
                <div class="addAdminUser">
                    <div class="title"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;添加管理员</div>
                    <el-row class="AdminUserInput" style="z-index: 5;">
                        <el-col :span="2" :offset="1"><div class="addLeft">用户名</div></el-col>
                        <el-col :span="4"><div class="addRight"><el-input v-model="userName" placeholder="请输入用户名" autocomplete='off' style="width:200px;"></el-input></div></el-col>
                        <el-col :span="2"><div class="addLeft">密码</div></el-col>
                        <el-col :span="4"><div class="addRight"><el-input v-model="passWord" placeholder="请输入密码" type="password" autocomplete='new-password' style="width:200px;"></el-input></div></el-col>
                        <el-col :span="2"><div class="addLeft">确认密码</div></el-col>
                        <el-col :span="4"><div class="addRight"><el-input v-model="confirmPassWord" placeholder="请确认密码" type="password" style="width:200px;"></el-input></div></el-col>
                        <el-col :span="1">
                            <div class="">头像</div>
                        </el-col>
                        <el-col :span="3">
                            <div class="" style="width:100px;height:100px;position: absolute;">
                                <img v-if="img_url!=''" class="w_h_ratio" :src="img_url">
                                <img v-if="img_url==''" src="../../image/upDateImg.jpg">
                                <form id="forms" method="post" enctype="multpart/form-data" class="formData" style="display: inline-block;left: 2px;top: 3px;">
                                    <input id="files" class="file" type="file" @change="upData('A')"/>
                                </form>
                            </div>
                        </el-col>
                    </el-row>
                    <el-row class="AdminUserInput" style="z-index: 2;">
                        <el-col :span="2" :offset="1"><div class="addLeft">部门</div></el-col>
                        <el-col :span="4">
                            <template>
                                <el-select v-model="department" placeholder="请选择部门" style="width:200px;">
                                    <el-option v-for="item in departments" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                </el-select>
                            </template>
                        </el-col>
                        <el-col :span="2"><div class="addLeft">权限等级</div></el-col>
                        <el-col :span="4">
                            <template>
                                <el-select v-model="roleList" placeholder="请选择" style="width:200px;">
                                    <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                </el-select>
                            </template>
                        </el-col>
                        <el-col :span="2"><div class="addLeft">添加昵称</div></el-col>
                        <el-col :span="4"><div class="addRight"><el-input v-model="nickname" placeholder="请输入昵称" style="width:200px;"></el-input></div></el-col>
                    </el-row>
                    <el-row class="AdminUserInput">
                        <el-col :span="4" :offset="20"><div class=""><span class="bgButton" @click="addAdminUser()">确认添加</span></div></el-col>
                    </el-row>
                </div>
            </el-col>
        </el-row>
        <el-col :span="22" :offset="1" class="MB_twenty">
            <!-- <div class="title"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;管理员列表</div> -->
            <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>管理员列表</span></div></el-col>
                
                <el-col :span="2"><div>用户名</div></el-col>
                <el-col :span="3"><div><el-input v-model="user" placeholder="请输入用户名"></el-input></div></el-col>
                <el-col :span="2"><div>昵称</div></el-col>
                <el-col :span="3"><div><el-input v-model="nickName" placeholder="请输入昵称"></el-input></div></el-col>
                <el-col :span="2"><div>角色</div></el-col>
                <el-col :span="3" >
                    <template>
                        <el-select v-model="role_id" clearable placeholder="请选择角色">
                            <el-option v-for="item in options"  :key="item.value" :label="item.label" :value="item.id">
                            </el-option>
                        </el-select>
                    </template>
                </el-col>
                <el-col :span="2"><div>部门</div></el-col>
                <el-col :span="3">
                    <template>
                        <el-select v-model="department_id" clearable placeholder="请选择部门">
                            <el-option v-for="item in departments" :key="item.value" :label="item.label" :value="item.id"></el-option>
                        </el-select>
                    </template>
                </el-col>
                <el-col :span="2"><span class="bgButton" @click="getAdminUserList()">搜索</span></el-col>
            </el-row>
            <table border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th>头像</th>
                        <th>用户名</th>
                        <th>昵称</th>
                        <th>角色名称</th>
                        <th>编辑</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,index) in tableData">
                        <td>
                            <img v-if="item.user_img!=''" style="width: 30px;height: 30px;border-radius: 50%;vertical-align: -10px;" :src="imgUrl+item.user_img"/>
                            <span v-else >暂无头像</span>
                        </td>
                        <td>{{item.user_name}}</td>
                        <td>{{item.nickname}}</td>
                        <td>{{item.role_name}}</td>
                        <td>
                            <el-button class="addMode" type="text" @click="dialogVisible = true;openModify(item.user_name,item.role_name,item.id,item.user_img,item.role_id,item.nickname)">修改</el-button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <el-dialog title="编辑管理员" :visible.sync="dialogVisible" width="40%">
                <el-row>
                    <el-col :span="11" :offset="1">
                    <div class="addChannel_left" >
                        用户名&nbsp;&nbsp;<el-input v-model="user_name" disabled   style="width: 65%;"></el-input>
                    </div>
                    </el-col>
                    <el-col :span="11" :offset="1">
                        <div class="addChannel_right">
                            角色名称&nbsp;&nbsp;
                            <template>
                                <el-select v-model="role_name">
                                    <el-option v-for="itemO in options" :key="itemO.value" :label="itemO.label" :title="itemO.value" :value="itemO.id"></el-option>
                                </el-select>
                            </template>
                        </div>
                    </el-col>
                </el-row>
                <el-row style="margin-top: 15px;">
                    <el-col :span="11" :offset="1">
                        <div class="addChannel_right">
                            昵&nbsp;&nbsp;&nbsp;称&nbsp;&nbsp;<el-input v-model="nick_name" style="width: 65%;"></el-input>
                        </div>
                    </el-col>
                    <el-col :span="2" :offset="1">
                        <div class="MT_Thirty">头&nbsp;&nbsp;&nbsp;像</div>
                    </el-col>
                    <el-col :span="3">
                        <div class="MT_twenty" style="width:100px;height:100px;">
                            <img v-if="user_img!=''" class="w_h_ratio" :src="imgUrl_edit">
                            <img v-if="user_img==''" src="../../image/upDateImg.jpg">
                            <form id="formst" method="post" enctype="multpart/form-data" class="formData" style="display: inline-block">
                                <input id="filet" class="file" type="file" @change="upData('D')"/>
                            </form>
                        </div>
                    </el-col>
                </el-row>
                <span slot="footer" class="dialog-footer">
                    <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                    <el-button type="primary" @click="dialogVisible = false;confirmationModify()">确认修改</el-button>
                </span>
            </el-dialog>
            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
            </el-pagination>
        </el-col>
    </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
export default {
  data(){
    return{
      url: `${this.$baseUrl}`,
      imgUrl:`${this.$imgUrl}`,
      tableData:[],
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      userName:'',
      passWord:'',
      confirmPassWord:'',
      valueO:'',
      options: [],
      roleList:'',
      user_name:'',
      role_name:'',
      user_id:'',
      dialogVisible:false,
      department:'',
      departments:[],
      headersStr:'',
      nickname:'',
      img_url:'',
      user_img:'',
      userInfo:'',
      imgUrl_edit:'',
      roleId:'',
      nick_name:'',
      //搜索
      user:'',
      nickName:'',
      role_id:'',
      department_id:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getAdminUserList()
  },
  methods:{
    //确认添加管理员
    addAdminUser(){
        let vm=this;
        if(vm.userName==''){
            vm.$message("用户名称不能为空");
            return false;
        }
        if(vm.passWord==''){
            vm.$message("用户密码不能为空");
            return false;
        }
        if(vm.confirmPassWord==''){
            vm.$message("确认密码不能为空");
            return false;
        }
        if(vm.roleList==''){
            vm.$message("角色不能为空");
            return false;
        }
        if(vm.department==''){
            vm.$message("部门不能为空");
            return false;
        }
        if(vm.nickname==''){
            vm.$message("昵称不能为空");
            return false;
        }
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        axios.post(vm.url+vm.$addAdminUserURL,
            {
                "user_name":vm.userName,
                "password":vm.passWord,
                "confirm_password":vm.confirmPassWord,
                "role_id":vm.roleList,
                "department_id":vm.department,
                "nickname":vm.nickname,
                "user_img":vm.user_img,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            loa.close();
            vm.cleanData();
            vm.$message(res.data.msg);
            vm.getAdminUserList();
        }).catch(function (error) {
                loa.close();
                vm.cleanData();
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //获取管理员列表
    getAdminUserList(){
        let vm=this;
        vm.departments.splice(0);
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
        axios.get(vm.url+vm.$adminUserListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&user_name="+vm.user+"&nickname="+vm.nickName
        +"&role_id="+vm.role_id+"&department_id="+vm.department_id,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.data.user_list_info.data;
            vm.total=res.data.data.user_list_info.total;
            res.data.data.department_info.forEach(element=>{
                vm.departments.push({"id":element.department_id,"label":element.de_name})
            })
            res.data.data.role_list.forEach(element => {
                vm.options.push({"id":element.id,"label":element.name});
            });
        }).catch(function (error) {
                vm.loading.close();
                if(error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                }
            });
    },
    //打开修改
    openModify(username,rolename,id,img,role_id,nickname){
        let vm=this;
        vm.user_img=img;
        vm.user_name=username;
        vm.role_name=rolename;
        vm.user_id=id;
        vm.roleId=role_id;
        vm.imgUrl_edit=vm.imgUrl+img;
        vm.nick_name=nickname;
    },
    //确认修改
    confirmationModify(id){
        let vm=this;
        vm.dialogVisible=false;
        axios.post(vm.url+vm.$eidtAdminUserURL,
            {
                "id":vm.user_id,
                "role_id":vm.roleId,
                "user_img":vm.user_img,
                "nickname":vm.nick_name,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code==1000){
                vm.getAdminUserList();
            }
        }).catch(function (error) {
                vm.loading.close();
                if(error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                }
            });
    },
    //清除已填数据
    cleanData(){
        let vm = this;
        vm.user_img='';
        vm.userName='';
        vm.passWord='';
        vm.confirmPassWord='';
        vm.roleList='';
        vm.department='';
        vm.nickname='';
        $("#files").val('');
        vm.img_url='';
    },
    //上传头像
    upData(Ident){
        let vm = this;
        if(Ident=='A'){//新增
            var formDate = new FormData($("#forms")[0]);
            formDate.append('user_img', document.getElementById("files").files[0]);
        }else if(Ident=='D'){//编辑
            var formDate = new FormData($("#formst")[0]);
            formDate.append('user_img', document.getElementById("filet").files[0]);
        }
        console.log(formDate)
        vm.userInfo=JSON.parse(sessionStorage.getItem("userName"));
        $.ajax({
        url: vm.url+vm.$uploadAdminUserImgURL+"?user_id="+vm.userInfo.id+"&user_name="+vm.userInfo.user_name,
        type: "POST",
        async: false,
        // cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            if(res.code=='1000'){
                if(Ident=='A'){//新增
                    vm.img_url=vm.imgUrl+res.user_img;
                    vm.user_img=res.user_img;
                }else if(Ident=='D'){//编辑
                    vm.imgUrl_edit=vm.imgUrl+res.user_img;
                    vm.user_img=res.user_img;
                }
            }else{
                vm.$message(res.msg);
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
        vm.getAdminUserList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getAdminUserList()
    },
  }
}
</script>

<style scoped>
.addAdminUser_b .title{
    font-size: 20px;
    /* margin-top: 50px; */
    padding-left: 35px;
    background-color: #ebeef5;
    height: 65px;
    line-height: 65px;
}
.addAdminUser{
    margin-top: 30px;
    width: 100%;
    height: 253px;
    /* padding-top: 30px; */
    padding-bottom: 30px;
    background-color: #ebeef5;
    margin-bottom: 30px;
}
.addLeft{
    float: right;
    margin-right: 5px;
    line-height: 37px;
}
.AdminUserInput{
    padding: 10px 0 10px 0;
}
.addAdminUser_b table{
    border: 1px solid #ebeef5; 
    width: 100%;
    text-align: center;   
}
.addAdminUser_b table tr{
    line-height: 40px;
}
.addAdminUser_b table tr th,td{
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
}
.addAdminUser_b table tr:hover{
    background:#ced7e6;
}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
.el-dialog__header {
    padding: 20px 20px 10px;
    text-align: left;
    background-color: #eef0f4;
}
/* table .el-button--primary {
    color: #fff;
    background-color: #ff5d17;
    border-color: #ff5d17;
} */
.add_admin_user span{
    display: inline-block;
    width: 110px;
    height: 31px;
    line-height: 31px;
    text-align: center;
    color: #fff;
    font-size: 15px;
    border-radius: 5px;
    background-color: #4677c4;
}
.formData{
    display: inline-block;
    position: absolute;
    right: 173px;
    top: 20px;
    opacity: 0;
    width: 100px;
    height: 100px;
}
.file{
    position: absolute;
    left: 0px;
    /* top: 20px; */
    opacity: 0;
    width: 100px;
    height: 100px;
}
.uploadHead{
    margin-top: 100px;
}
.uploadHead{
    margin-top: 100px;
}
.formdata{
    position: absolute;
    left: 4px;
    top: 1px;
    opacity: 0;
    width: 101px;
}
</style>
