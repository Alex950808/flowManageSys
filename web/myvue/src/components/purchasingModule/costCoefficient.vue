<template>
  <div class="costCoefficient_b">
    <!-- 采购成本系数页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="costCoefficient bgDiv">
              <el-row>
                <el-col :span="22" :offset="1">
                  <!-- <div class="title">
                    <el-col :span="21">
                      <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购成本系数</span>
                    </el-col>
                      <el-col :span="2">
                      
                      </el-col>
                      
                  </div> -->
                  <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                    <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>采购成本系数</span></div></el-col>
                    <el-col :span="3"><div>采购成本系数编码</div></el-col>
                    <el-col :span="3"><div><el-input v-model="cost_sn" placeholder="请输入采购成本系数编码"></el-input></div></el-col>
                    <el-col :span="2"><span class="bgButton" @click="getCostCoefficient()">搜索</span></el-col>
                    <el-col :span="3"><span class="notBgButton" @click="dialogVisible = true"><i class="iconfont addModeIcon">&#xea22;</i>新增系数</span></el-col>
                  </el-row>
                  <el-dialog title="新增系数" :visible.sync="dialogVisible" width="600px">
                    <el-row>
                      <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span>新增系数:</span>
                        </div>
                      </el-col>
                      <el-col :span="10">
                        <div style="line-height: 40px;">
                          <el-input v-model="cost_coef" placeholder="系数"></el-input>
                        </div>
                      </el-col>
                    </el-row>
                    <span slot="footer" class="dialog-footer">
                      <el-button type="info" @click="dialogVisible = false;closeUp()">取 消</el-button>
                      <el-button type="primary" @click="dialogVisible = false;addCoefficient()">新增</el-button>
                    </span>
                  </el-dialog>
                  <table style="width:100%;text-align: center" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                      <tr>
                        <th>采购成本系数编码</th>
                        <th>修改日期</th>
                        <th>系数</th>
                        <th>状态</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in multipleSelection">
                        <td>{{item.cost_sn}}</td>
                        <td>{{item.modify_time}}</td>
                        <td>{{item.cost_coef}}</td>
                        <td style="cursor: pointer;" v-if="item.cost_status==1" @click="Select(item.id)"><i class="iconfont">&#xe624;</i></td>
                        <td style="cursor: pointer;" v-if="item.cost_status==2" @click="Select(item.id)"><i class="iconfont">&#xe630;</i></td>
                      </tr>
                    </tbody>
                  </table>
                </el-col>
              </el-row>
                <div v-if="isShow" style="text-align: center;line-height: 50px;"><img class="notData" src="../../image/notData.png"/></div>
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
export default {
    data() {
      return {
        tableData1: [],
        url: `${this.$baseUrl}`,
        dialogVisible: false,
        cost_coef:'',
        multipleSelection:[],
        loading:'',
        isShow:false,
        headersStr:'',
        cost_sn:'',
      }
    },
    mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getCostCoefficient();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      //获取采购成本系数数据
      getCostCoefficient(){
        let vm=this;
        axios.get(vm.url+vm.$costListURL+"?cost_sn="+vm.cost_sn,
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          vm.loading.close();
          if(res.data.code==1000){
            vm.multipleSelection=res.data.data;
          }
          if(vm.multipleSelection.length==0){
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
      //增加采购成本系数
      addCoefficient(){
        let vm=this;
        if(vm.cost_coef==''){
          vm.$message('采购成本系数不能为空!');
          return false;
        }
        axios.post(vm.url+vm.$createCostURL,
          {
            "cost_coef":vm.cost_coef
          },
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          vm.$message(res.data.msg);
          if(res.data.code==1000){
            vm.dialogVisible=false;
            vm.isShow=false;
            vm.getCostCoefficient();
          }
          vm.cost_coef='';
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                  vm.$message('登录过期,请重新登录!');
                  sessionStorage.setItem("token","");
                  vm.$router.push('/');
                }
            });
      },
      //消息弹框
      open() {
          this.$message('新增成本系数成功');
      },
      //切换选中状态
      Select(e){
        let vm=this;
        axios.post(vm.url+vm.$editCostURL,
          {
            "id":e
          },
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          vm.getCostCoefficient()
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //点击取消
      closeUp(){
        let vm=this;
        vm.cost_coef='';
      }
    }
}
</script>
<style>
/* @import '../../css/common.css'; */
.el-table{
    text-align: center;
}
.el-table th>.cell {
    text-align: center;
}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
.addModeIcon{
  font-size: 27px !important;
  vertical-align: -3px;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>