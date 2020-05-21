<template>
  <div>
    <el-row :gutter="20" class="modifyingTheRole">
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <el-col :span="22" :offset="1">
            <div class="title listTitleStyle">&nbsp;&nbsp;&nbsp;&nbsp;<span @click="backUpPage()" class="bgButton">返回上一级</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;<span>修改角色</span></div>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <div class="addRole">
                        <el-row class="RoleInput">
                            <el-col :span="2" :offset="1"><div class="addLeft">角色代码</div></el-col>
                            <el-col :span="4"><div class="addRight"><el-input v-model="RoleCode" placeholder="请输入角色代码"></el-input></div></el-col>
                            <el-col :span="2" :offset="1"><div class="addLeft">角色名称</div></el-col>
                            <el-col :span="4"><div class="addRight"><el-input v-model="RoleName" placeholder="请输入角色名称"></el-input></div></el-col>
                            <el-col :span="2" :offset="1"><div class="addLeft">角色描述</div></el-col>
                            <el-col :span="4"><div class="addRight"><el-input placeholder="请输入角色描述" v-model="RoleDescribe"></el-input></div></el-col>
                        </el-row>
                        <div class="RoleInput Modular" v-for="(item,index) in RolePermissionList" :key="item.display_name">
                                <div class="RoleInputTitle">
                                    <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange;titleChecked(index,item.id)">
                                        <el-checkbox :value="item.id" :label="item.id" :key="item.id">{{item.display_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </div>
                                <el-row>
                                    <el-col :span="21" :offset="1">
                                        <div class="" v-for="itemT in item.child_info" :key="itemT.display_name">
                                            <el-row>
                                                <el-col :span="2"><div>
                                                        <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange;textChecked(index,itemT.id,item.id)">
                                                            <el-checkbox style="margin-left: 0px;margin-right: 30px;margin-bottom: 20px;" class="addVal fontWeight" :value="itemT.id" :label="itemT.id" :key="itemT.id">{{itemT.display_name}}</el-checkbox>
                                                        </el-checkbox-group>
                                                    </div></el-col>
                                                <el-col :span="21" :offset="1">
                                                    <div>
                                                        <el-checkbox-group style="display: inline-block;" v-for="itemO in itemT.child_info" :key="itemO.display_name" v-model="checkedCities" @change="handleCheckedCitiesChange;textChecked(index,itemO.id,item.id)">
                                                            <el-checkbox style="margin-left: 0px;margin-right: 30px;margin-bottom: 20px;" class="addVal" :value="itemO.id" :label="itemO.id" :key="itemO.id">{{itemO.display_name}}</el-checkbox>
                                                        </el-checkbox-group>
                                                    </div>
                                                </el-col>
                                            </el-row>
                                        </div>
                                    </el-col>
                                </el-row>
                        </div>
                        <el-row class="RoleInput">
                            <el-col :span="4" :offset="20"><div><span class="bgButton" @click="addRoleList()">确认修改</span></div></el-col>
                        </el-row>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui';
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  data(){
    return{
      url: `${this.$baseUrl}`,
      RoleCode:'',//角色代码,
      RoleName:'',//角色名称
      RoleDescribe:'',//角色描述
      RolePermissionList:[],//角色权限列表
      checkAll: false,
    //   citiesA:[],
      citiesB:[],
      cities: [],
      isIndeterminate: false,
      cityOptions:[],
      
      checkedCities: [],//选中状态
      JurisdictionID:[],//权限ID
      headersStr:'',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.openModify();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
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
    //获取用户初始数据
    openModify(){
        let vm=this;
        axios.get(vm.url+vm.$editRoleURL+"?id="+vm.$route.query.id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
                vm.loading.close()
                if(res.data.code==1000){
                    vm.RoleCode=res.data.data.role_info[0].name;
                    vm.RoleName=res.data.data.role_info[0].role_name;
                    vm.RoleDescribe=res.data.data.role_info[0].description
                    vm.RolePermissionList=res.data.data.permission_list_info;
                    res.data.data.role_permission_info.forEach(element=>{
                        vm.checkedCities.push(element);
                    })
                }else if(res.data.code=='1020'){
                    vm.$message(res.data.msg);
                    vm.$router.push('/roleList')
                }else{
                    vm.$message(res.data.msg);
                }
                
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    addRoleList(){
        let vm=this;
        var IdStr='';
        vm.checkedCities.forEach(element=>{
            IdStr+=element+','
        })
        axios.post(vm.url+vm.$editRoleURL,
            {
                "name":vm.RoleCode,
                "display_name":vm.RoleName,
                "description":vm.RoleDescribe,
                "permission_list":IdStr,
                "role_id":vm.$route.query.id
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                // vm.$router.push('/roleList')
                vm.$message(res.data.msg);
                // location.reload()
            }else{
                vm.$message(res.data.msg);
            }
            
        }).catch(function (error) {
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //回上一级页面
    backUpPage(){
        let vm=this;
        vm.$router.push('/roleList');
    },
    //点击选中模块部分
    titleChecked(index,id){
        let vm = this;
        let checkedCities=[];
        //将点击模块的id全部push进一个临时数组中
        vm.RolePermissionList[index].child_info.forEach(element=>{
            checkedCities.push(element.id)
            if(element.child_info!=undefined){
                element.child_info.forEach(childElement=>{
                    checkedCities.push(childElement.id)
                })
            }
        })
        Array.prototype.indexOf = function(val) {
            for (var i = 0; i < this.length; i++) {
            if (this[i] == val) return i;
            }
            return -1;
        };
        Array.prototype.remove = function(val) {
            var index = this.indexOf(val);
            if (index > -1) {
            this.splice(index, 1);
            }
        };
        //寻找点击传入的此选项的id是否在选择的数组中，如果在就将将其下的child_infopush进去，如果为undefined就将其remove掉
        let judgeSelection=vm.checkedCities.find(function(e){
            return e==id;
        });
        if(judgeSelection!=undefined){
            checkedCities.forEach(element=>{
                vm.checkedCities.push(element);
            })
        }else{
            checkedCities.forEach(element=>{
                vm.checkedCities.remove(element);
            })
        }
        //最后将选择的数组去重
        vm.checkedCities=uniq(vm.checkedCities);
    },
    //点击模块内容选中模块标题 
    textChecked(index,id,fatherId){
        let vm = this;
        let checkedCities=[];
        let erjiSelection=vm.checkedCities.find(function(e){
            return e==id;
        });
        Array.prototype.indexOf = function(val) {
            for (var i = 0; i < this.length; i++) {
            if (this[i] == val) return i;
            }
            return -1;
        };
        Array.prototype.remove = function(val) {
            var index = this.indexOf(val);
            if (index > -1) {
            this.splice(index, 1);
            }
        };
        let unList = [];
        let xiangshangID;
        vm.RolePermissionList[index].child_info.forEach(element=>{
            checkedCities.push(element.id)
            if(element.child_info!=undefined){
                if(element.id==id){
                    if(erjiSelection!=undefined){
                        element.child_info.forEach(childElement=>{
                            vm.checkedCities.push(childElement.id)
                        })
                    }
                    if(erjiSelection==undefined){
                        element.child_info.forEach(childElement=>{
                            vm.checkedCities.remove(childElement.id)
                        })
                    }
                }
                element.child_info.forEach(childElement=>{
                    if(childElement.id==id){
                        element.child_info.forEach(xiangshang=>{
                            xiangshangID=element.id;
                            let xiangshangSelection=vm.checkedCities.find(function(e){
                                return e==xiangshang.id;
                            });
                            unList.push(xiangshangSelection);
                        })
                    }
                })
            }
        })
        unList=uniq(unList)
        if(unList.length==1){
            vm.checkedCities.remove(xiangshangID);
        }else{
            vm.checkedCities.push(xiangshangID);
        }
        let judgeSelection=vm.checkedCities.find(function(e){
            return e==id;
        });
        if(judgeSelection!=undefined){
            vm.checkedCities.push(fatherId);
        }else{
        }
        vm.checkedCities=uniq(vm.checkedCities);
        checkedCities=uniq(checkedCities);
        let i=0;
        vm.checkedCities.forEach(element=>{
            checkedCities.forEach(elementO=>{
                if(element==elementO){
                    i++
                }
            })
        })
        if(i==0){
            vm.checkedCities.remove(fatherId);
        }
    }
  }
}
</script>

<style>
.modifyingTheRole .title{
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    padding-left: 35px;
    margin-top: 20px;
}
.modifyingTheRole .addRole{
    /* margin-top: 30px; */
    width: 100%;
    height: 100%;
    /* padding-top: 30px; */
    padding-bottom: 30px;
    /* background-color: #ebeef5; */
}
.modifyingTheRole .addRole .addLeft{
    float: right;
    margin-right: 5px;
    line-height: 37px;
}
.modifyingTheRole .RoleInput{
    padding: 20px 0 20px 0;
    margin-left: 90px;
    margin-right: 90px;
}
.modifyingTheRole .Modular{
    -webkit-box-shadow: #ccc 0px 0px 10px;
    -moz-box-shadow: #ccc 0px 0px 10px;
    box-shadow: #ccc 0px 0px 10px;
    padding: 0 !important;
    margin-bottom: 10px;
}
.modifyingTheRole .RoleInputTitle{
    height: 50px;
    background: #eef0f4;
    line-height: 50px;
    padding-left: 20px;
    margin-bottom: 10px;
}
.modifyingTheRole .back{
    display: inline-block;
    width: 130px;
    height: 50px;
    background-color: #4677c4;
    color: #fff;
    line-height: 50px;
    text-align: center;
    border-radius: 10px;
    cursor: pointer;
}
</style>
