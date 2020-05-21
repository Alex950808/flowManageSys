<template>
  <div class="userList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;用户列表</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openAddUser('A')">新增用户</span>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>用户列表</span></div></el-col>
                        <el-col :span="2"><div>用户名</div></el-col>
                        <el-col :span="3"><div><el-input v-model="userName" placeholder="请输入用户名"></el-input></div></el-col>
                        <el-col :span="2"><div>昵称</div></el-col>
                        <el-col :span="3"><div><el-input v-model="nickName" placeholder="请输入昵称"></el-input></div></el-col>
                        <el-col :span="2"><div>用户分类</div></el-col>
                        <el-col :span="3" >
                            <el-select v-model="classifyId" clearable placeholder="请选择用户分类">
                                <el-option v-for="item in selectData" :key="item.value" :label="item.classify_name" :value="item.id"></el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                        <el-col :span="3"><span class="bgButton floatRight MR_twenty" style="margin-top: 6px;" @click="openAddUser('A')">新增用户</span></el-col>
                    </el-row>
                    <table class="MB_twenty" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>用户名</th>
                                <th>昵称</th>
                                <th>用户分类</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>{{item.user_name}}</td>
                                <td>{{item.nickname}}</td>
                                <td>{{item.classify_name}}</td>
                                <td>{{item.status_str}}</td>
                                <td><span class="notBgButton" @click="openAddUser('E',item.id,index)">编辑</span></td>
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
      <el-dialog :title="titleStr" :visible.sync="dialogVisibleAdd" width="800px">
            <el-row class="elRow">
                <el-col :span="3" :offset="1">
                    <div>用户名：</div>
                </el-col>
                <el-col :span="8">
                    <el-input v-model="user_name" style="width:87%;" placeholder="请输入用户名"></el-input>
                </el-col>
                <el-col :span="3">
                    <div>密码：</div>
                </el-col>
                <el-col :span="8">
                    <el-input v-model="password" style="width:87%;" type="password" placeholder="请输入密码"></el-input>
                </el-col>
            </el-row>
            <el-row class="elRow">
                <el-col :span="3" :offset="1">
                    <div>确认密码：</div>
                </el-col>
                <el-col :span="8">
                    <el-input v-model="confirm_password" style="width:87%;" type="password" placeholder="请输入确认密码"></el-input>
                </el-col>
                <el-col :span="3">
                    <div>昵称：</div>
                </el-col>
                <el-col :span="8">
                    <el-input v-model="nickname" style="width:87%;" placeholder="请输入昵称"></el-input>
                </el-col>
            </el-row>
            <el-row class="elRow">
                <el-col :span="3" :offset="1">
                    <div>分类：</div>
                </el-col>
                <el-col :span="8">
                    <el-select v-model="classify_id" placeholder="请选择用户名">
                        <el-option v-for="item in uClassifyList" :key="item.id" :label="item.classify_name" :value="item.id"></el-option>
                    </el-select>
                </el-col>
            </el-row>
            <div slot="footer">
                <span class="grayBgButton" @click="dialogVisibleAdd = false">取 消</span>
                <span class="redBgButton removeattr" @click="AddUser()">确 定</span>
            </div>
        </el-dialog>
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
      selectData:[],
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //新增/编辑用户 
      titleStr:'',
      state:'',
      ID:'',
      user_name:'',
      uClassifyList:[],
      classify_id:'',
      dialogVisibleAdd:false,
      password:'',
      confirm_password:'',
      nickname:'',
      passStr:'',
      conPassStr:'',
      //搜索
      userName:'',
      nickName:'',
      classifyId:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
    this.getUserClassifyList();
  },
  methods:{
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$UserListURL+"?user_name="+vm.userName+"&nickname="+vm.nickName+"&classify_id="+vm.classifyId,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.data.data.length!=0){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
                vm.isShow=false;
            }else{
                vm.isShow=true;
                vm.tableData=[];
                vm.total=0;
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
    getUserClassifyList(){
        let vm = this;
        axios.get(vm.url+vm.$getUserClassifyListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.selectData=res.data.data;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    openAddUser(state,id,index){
        let vm = this;
        vm.state=state;
        vm.dialogVisibleAdd=true;
        vm.user_name='';
        vm.password='';
        vm.classify_id='';
        vm.confirm_password='';
        vm.nickname='';
        vm.ID='';
        if(state=='A'){//新增
            vm.titleStr='新增用户'
        }else if(state=='E'){//编辑 
            vm.titleStr='编辑用户';
            vm.ID=id;
            vm.user_name=vm.tableData[index].user_name;
            vm.password=vm.tableData[index].password.substring(0,6);
            vm.classify_id=vm.tableData[index].classify_id;
            vm.confirm_password=vm.tableData[index].password.substring(0,6);
            vm.nickname=vm.tableData[index].nickname;
            vm.passStr=vm.tableData[index].password;
            vm.conPassStr=vm.tableData[index].password;
        }
        axios.get(vm.url+vm.$getUserClassifyURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=='1000'){
                vm.uClassifyList=res.data.data;
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
    //新增用户 
    AddUser(){
        let vm = this;
        if(vm.user_name==''){

        }
        vm.dialogVisibleAdd=false;
        let url;
        if(vm.state=='A'){//新增
            url=vm.$addUserURL;
        }else if(vm.state=='E'){//编辑
            url=vm.$usereditUserURL;
            if(vm.passStr.indexOf(vm.password)!=-1){//密码未被更改
                vm.password=vm.passStr;
            }
            if(vm.conPassStr.indexOf(vm.confirm_password)!=-1){//密码未被更改
                vm.confirm_password=vm.conPassStr;
            }
        }
        let load = Loading.service({fullscreen: true, text: '拼命提交中....'})
        axios.post(vm.url+url,
            {
                "user_name":vm.user_name,
                "password":vm.password,
                "confirm_password":vm.confirm_password,
                "classify_id":vm.classify_id,
                "nickname":vm.nickname,
                "id":vm.ID,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code=='1000'){
                vm.getDataList();
            }else{
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

<style scoped>
@import '../../css/publicCss.css';
</style>
