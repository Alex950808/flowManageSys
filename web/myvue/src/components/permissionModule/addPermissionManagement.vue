<template>
  <div class="addPermissionManagement">
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="addPermission">
                        <div class="title"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;添加权限</div>
                        <el-row class="PermissionInput">
                            <el-col :span="2"><div class="addLeft">权限代码</div></el-col>
                            <el-col :span="4"><div class="addRight"><el-input v-model="PermissionCode" placeholder="请输入权限代码"></el-input></div></el-col>
                            <el-col :span="3" :offset="1"><div class="addLeft">权限前端代码</div></el-col>
                            <el-col :span="4"><div class="addRight"><el-input v-model="PermissionACode" placeholder="请输入权限前端代码"></el-input></div></el-col>
                            <el-col :span="3" :offset="1"><div class="addLeft">权限名称</div></el-col>
                            <el-col :span="4"><div class="addRight"><el-input v-model="PermissionName" placeholder="请输入权限名称"></el-input></div></el-col>
                        </el-row>
                        <el-row class="PermissionInput">
                            <el-col :span="2"><div class="addLeft">权限等级</div></el-col>
                            <!-- <el-col :span="4"><div class="addRight"><el-input v-model="PermissionGrade" placeholder="请输入权限等级"></el-input></div></el-col> -->
                            <el-col :span="4">
                                <div>
                                    <template>
                                        <el-select v-model="PermissionGrade" placeholder="请选择权限等级" style="width:100%;">
                                            <el-option v-for="item in levelList" :key="item.value" :label="item.label" :value="item.JurisdictionID"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="3" :offset="1"><div class="addLeft">权限描述</div></el-col>
                            <el-col :span="4"><div class="addRight"><el-input placeholder="请输入权限描述" v-model="PermissionDescribe"></el-input></div></el-col>
                            <el-col :span="3" :offset="1"><div class="addLeft">上一级权限</div></el-col>
                            <el-col :span="4"><el-cascader :options="options" v-model="parent_id" :show-all-levels="false" style="width:100%;"></el-cascader></el-col>
                        </el-row>
                        <el-row class="PermissionInput">
                            <el-col :span="4" :offset="20"><div><span class="bgButton" @click="AddPermission()">确认添加</span></div></el-col>
                        </el-row>
                    </div>
                    <div class="title"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;权限列表</div>
                    <table class="MB_ten" style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th style="width:136px;border-bottom: 1px solid #ebeef5;border-left: 1px solid #ebeef5;border-right: 1px solid #ebeef5;border-top: 1px solid #ebeef5;">权限名称</th>
                            <th style="width:136px;border-bottom: 1px solid #ebeef5;border-left: 1px solid #ebeef5;border-right: 1px solid #ebeef5;border-top: 1px solid #ebeef5;">权限代码</th>
                            <th style="width:136px;border-bottom: 1px solid #ebeef5;border-left: 1px solid #ebeef5;border-right: 1px solid #ebeef5;border-top: 1px solid #ebeef5;">权限前端代码</th>
                            <th style="border-bottom: 1px solid #ebeef5;border-left: 1px solid #ebeef5;border-right: 1px solid #ebeef5;border-top: 1px solid #ebeef5;">权限描述</th>
                            <th style="width:100px;border-bottom: 1px solid #ebeef5;border-left: 1px solid #ebeef5;border-right: 1px solid #ebeef5;border-top: 1px solid #ebeef5;">权限等级</th>
                            <!-- <th style="width:100px">父级ID</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in tableData">
                            <td>{{item.display_name}}</td>
                            <td>{{item.name}}</td>
                            <td>{{item.web_name}}</td>
                            <td>{{item.description}}</td>
                            <td>{{item.rank}}</td>
                            <!-- <td>{{item.parent_id}}</td> -->
                        </tr>
                        </tbody>
                    </table>
                </el-col>
            </el-row>
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
      PermissionCode:'',//权限代码
      PermissionACode:'',//权限前端代码
      PermissionName:'',//权限名称
      PermissionDescribe:'',//权限描述
      PermissionGrade:'',//权限等级
      parent_id:'',//上级权限
      tableData:[],//权限列表
      options:[],//联动选择数据
      levelList:[],//权限等级
    //   levelID:[],//等级ID
    headersStr:'',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getPermissionList();
    this.getPermissionListNo();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
    //获取不分等级的权限列表数据 
    getPermissionList(){
        let vm=this;
        let strAryy=[1,2,3,4];
        strAryy.forEach(element=>{
            vm.levelList.push({"JurisdictionID":element,"label":element})
        })
        axios.get(vm.url+vm.$permissiomListURL+"?rank=0",
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close()
            vm.tableData=res.data.data;
        }).catch(function (error) {
            vm.loading.close()
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //获取分等级的权限列表数据
    getPermissionListNo(){
        let vm=this;
        axios.get(vm.url+vm.$permissiomListURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close()
            res.data.data.forEach(element => {
                let children=[];
                element.child_info.forEach(elementO=>{
                       children.push({"value":elementO.id,"label":elementO.display_name}) 
                    })
                vm.options.push({"value":element.id,"label":element.display_name,children})
            });
        }).catch(function (error) {
            vm.loading.close()
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    AddPermission(){
        let vm=this;
        axios.post(vm.url+vm.$addPermissionURL,
            {
                "name":vm.PermissionCode,
                "web_name":vm.PermissionACode,
                "display_name":vm.PermissionName,
                "description":vm.PermissionDescribe,
                "rank":vm.PermissionGrade,
                "parent_id":vm.parent_id[1],
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code==1000){
                vm.getPermissionList();
            }
        }).catch(function (error) {
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    }
    
  }
}
</script>

<style scoped>
.addPermissionManagement .title{
    font-size: 20px;
    /* margin-top: 50px; */
    padding-left: 35px;
    background-color: #ebeef5;
    height: 65px;
    line-height: 65px;
}
.addPermissionManagement .addPermission{
    margin-top: 30px;
    width: 100%;
    height: 230px;
    padding-top: 30px;
    padding-bottom: 30px;
    background-color: #ebeef5;
    margin-bottom: 20px;
}
.addPermissionManagement .addLeft{
    float: right;
    margin-right: 5px;
    line-height: 37px;
}
.addPermissionManagement .PermissionInput{
    padding: 10px 0 10px 0;
}
.addPermissionManagement .addPermission table{
        border: 1px solid #ebeef5;
        
    }
.addPermissionManagement .addPermission table tr:nth-child(even){
        background:#fafafa;
    }
.addPermissionManagement .addPermission table tr:hover{
        background:#ced7e6;
    }
.addPermissionManagement .addPermission table tr{
        line-height: 40px;
    }
.addPermissionManagement .addPermission table tr th,td{
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5;
    border-top: 1px solid #ebeef5;
}
.addPermissionManagement .coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
.addRight span{
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
</style>
