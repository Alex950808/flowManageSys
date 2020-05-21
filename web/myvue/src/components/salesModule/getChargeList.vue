<template>
  <div class="getChargeList_b">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
          <el-row class="bgDiv">
              <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="getChargeList">
                        <div class="title listTitleStyle">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;费用列表</span><span style="margin-left:20px;">部门&nbsp;&nbsp;&nbsp;&nbsp;{{department}}</span>
                            <el-button class="addMode" type="text" @click="dialogVisible = true">新增费用</el-button>
                            <el-dialog title="新增费用" :visible.sync="dialogVisible" width="800px">
                            <el-row>
                                <el-col :span="11" :offset="1">
                                <div class="addChannel_left">
                                    费用名称&nbsp;&nbsp;<el-input v-model="costName" placeholder="费用名称"></el-input>
                                </div>
                                </el-col>
                                <el-col :span="11" :offset="1">
                                <div class="addChannel_right">
                                    费率（%）&nbsp;&nbsp;<el-input v-model="rate" placeholder="费率"></el-input>
                                </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false;cancel()">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;addCostItem()">新增</el-button>
                            </span>
                            </el-dialog>
                        </div>
                        <el-row>
                            <table style="width:100%;text-align: center" border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                    <th>费用名称</th>
                                    <th>费率（%）</th>
                                    <th>创建时间</th>
                                    <th>编辑</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in tableData">
                                    <td>{{item.charge_name}}</td>
                                    <td>{{item.charge_rate}}</td>
                                    <td>{{item.create_time}}</td>
                                    <td><i class="el-icon-edit notBgButton" @click="dialogVisibleEdit = true;openEdit(item.charge_name,item.charge_rate)"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </el-row>
                        <el-dialog :visible.sync="dialogVisibleEdit" width="800px">
                            <el-row>
                                <el-col :span="11" :offset="1">
                                    <div class="addChannel_left">
                                    费用名称&nbsp;&nbsp;<el-input v-model="cost_name" placeholder="费用名称"></el-input>
                                    </div>
                                </el-col>
                                <el-col :span="11" :offset="1">
                                    <div class="addChannel_right">
                                    费率（%）&nbsp;&nbsp;<el-input v-model="cost_rate" placeholder="费率"></el-input>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisibleEdit = false;cancel()">取 消</el-button>
                                <el-button type="primary" @click="dialogVisibleEdit = false;modifyCharge()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <div v-if="isShow" style="text-align: center;line-height: 50px"><img class="notData" src="../../image/notData.png"/></div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
      fontWatermark,
    },
  data(){
    return{
        url: `${this.$baseUrl}`,
        tableData:[],
        dialogVisible:false,
        dialogVisibleEdit:false,
        costName:'',
        department:'',
        cost_name:'',
        cost_rate:'',
        rate:'',
        isShow:false,
    }
  },
  mounted(){
      this.getdataList();
  },
  methods:{
    getdataList(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$getChargeListURL,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.chargeInfo.length!=0){
                vm.tableData=res.data.chargeInfo;
                vm.department=res.data.departmentName;
                vm.isShow=false;
            }else{
                vm.tableData=[];
                vm.department='';
                vm.isShow=true;
            }
            
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //新增费用项
    addCostItem(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.post(vm.url+vm.$addChargeURL,
            {
               "charge_rate" :vm.rate,
               "charge_name":vm.costName,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code==2015){
                vm.dialogVisible = false;
                vm.getdataList();
            }
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //打开编辑费用项
    openEdit(costName,rate){
        let vm=this;
        vm.cost_rate=rate;
        vm.cost_name=costName;
    },
    //编辑费用项
    modifyCharge(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.post(vm.url+vm.$modifyChargeURL,
            {
               "charge_rate" :vm.cost_rate,
               "charge_name":vm.cost_name,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code==2038){
                vm.dialogVisibleEdit = false;
                vm.getdataList();
            }
        }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    cancel(){
        let vm=this;
        vm.costName='';
        vm.cost_name='';
        vm.cost_rate='';
        vm.rate='';
    }
  }
}
</script>
<style>
/* @import '../../css/common.css'; */
.getChargeList_b .el-input {
    position: relative;
    font-size: 14px;
    display: inline-block;
    width: 67%;
}
.getChargeList_b .el-dialog__headerbtn .el-dialog__close {
    color: #909399;
    display: none;
}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
