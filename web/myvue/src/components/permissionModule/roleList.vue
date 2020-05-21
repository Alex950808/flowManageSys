<template>
  <div class="roleList">
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <div class="roleListText bgDiv">
                    <el-col :span="22" :offset="1">
                        <el-row class="roleListInput">
                            <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                                <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>角色列表</span></div></el-col>
                                <el-col :span="2"><div>角色代码</div></el-col>
                                <el-col :span="3"><div><el-input v-model="name" placeholder="请输入角色代码"></el-input></div></el-col>
                                <el-col :span="2"><div>角色名称</div></el-col>
                                <el-col :span="3"><div><el-input v-model="display_name" placeholder="请输入角色名称"></el-input></div></el-col>
                                <el-col :span="2"><span class="bgButton" @click="getRoleList()">搜索</span></el-col>
                            </el-row>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th>角色代码</th>
                                        <th>角色名称</th>
                                        <th>角色描述</th>
                                        <th>编辑</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in tableData">
                                        <td>{{item.name}}</td>
                                        <td>{{item.display_name}}</td>
                                        <td>{{item.description}}</td>
                                        <td><el-tag><span @click="goDetails(item.id)" style="cursor: pointer;">修改</span></el-tag>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <li v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></li>
                            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                            </el-pagination>
                        </el-row>
                    </el-col>
                </div>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
export default {
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      total:0,//默认数据总数 
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      dialogVisible:false,
      roleCode:'',//角色代码
      roleDescribe:'',//角色描述
      roleName:'',//角色名称
      RolePermissionList:[],//角色权限列表
      cities: [],
      isIndeterminate: false,
      checkedCities: [],//选中状态
      cityOptions:[],
      isShow:false,
      //搜索
      display_name:'',
      name:'',
    }
  },
  mounted(){
    this.getRoleList();
  },
  methods:{
    handleCheckAllChange(val) {
        this.checkedCities = val ? cityOptions : [];
        this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.cities.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
    },
    getRoleList(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'})
        axios.get(vm.url+vm.$roleListURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&display_name="+vm.display_name+"&name="+vm.name,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code==1002){
                vm.isShow=true;
            }
            vm.tableData=res.data.data.data;
            vm.total=res.data.data.total;
            load.close();
        }).catch(function (error) {
                load.close();
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //修改
    confirmationModify(){

    },
    //页面跳转
    goDetails(id){
        let vm=this;
        vm.$router.push('/modifyingTheRole?id='+id);
    },
    //分页 
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getRoleList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getRoleList()
    },
  }
}
</script>

<style scoped>
/* @import '../../css/common.css'; */
.roleList .title{
    font-size: 20px;
    /* margin-top: 50px; */
    height: 65px;
    line-height: 65px;
    padding-left: 35px;
    background-color: #ebeef5;
}
.roleList .roleListText{
    /* margin-top: 30px; */
    width: 100%;
    /* height: 253px; */
    /* padding-top: 30px; */
    padding-bottom: 30px;
    /* background-color: #ebeef5; */
}
.roleList .addLeft{
    float: right;
    margin-right: 5px;
    line-height: 37px;
}
.roleListInput{
    padding: 20px 0 20px 0;
}
.roleList table{
    border: 1px solid #ebeef5; 
    width: 100%;
    text-align: center;   
}
.roleList table tr{
    line-height: 40px;
}
.roleList table tr th,td{
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
}
/* table tr:nth-child(even){
    background:#fafafa;
} */
.roleList table tr:hover{
    background:#ced7e6;
}
/* .roleList .el-input {
    position: relative;
    font-size: 14px;
    display: inline-block;
    width: 65%;
    margin-bottom: 20px;
} */
.RoleInputTitle{
    height: 50px;
    background: #eef0f4;
    line-height: 50px;
    padding-left: 20px;
    margin-bottom: 10px;
}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
</style>