<template>
  <div class="procurementMethod_b">
    <!-- 采购方式页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="procurementMethod bgDiv">
              <el-row>
                <el-col :span="22" :offset="1">
                  <!-- <div class="title tableTitleStyle">
                      <el-col :span="21">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购方式</span>
                      </el-col>
                      <el-col :span="3">
                        
                      </el-col>
                      
                  </div> -->
                  <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                    <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>采购方式</span></div></el-col>
                    <el-col :span="3"><div>方式名称</div></el-col>
                    <el-col :span="3" ><div><el-input v-model="method_name" placeholder="请输入方式名称"></el-input></div></el-col>
                    <el-col :span="3"><div>方式编号</div></el-col>
                    <el-col :span="3"><div><el-input v-model="method_sn" placeholder="请输入方式编号"></el-input></div></el-col>
                    <el-col :span="3"><span class="bgButton" @click="getModeData()">搜索</span></el-col>
                    <el-col :span="3"><span class="notBgButton" type="text" @click="dialogVisible = true"><i class="iconfont upDataIcon verticalAlign">&#xea22;</i>新增方式</span></el-col>
                  </el-row>
                  <el-dialog title="新增方式" :visible.sync="dialogVisible" width="800px">
                    <el-row class="elRow">
                      <el-col :span="4">
                        <div class="addChannel_left">
                          <span class="redFont">*</span>方式名称
                        </div>
                      </el-col>
                      <el-col :span="6">
                        <div class="addChannel_left">
                          <el-input v-model="channelName" placeholder="方式名称"></el-input>
                        </div>
                      </el-col>
                      <el-col :span="4" :offset="1">
                        <div class="channelProperty">
                          <span class="redFont">*</span>方式属性
                        </div>
                      </el-col>
                      <el-col :span="6">
                        <div class="channelProperty">
                          <template>
                            <el-select v-model="channelProperty" placeholder="请选择方式属性">
                              <el-option v-for="item in channelPropertys" :key="item.value" :label="item.propertyValue" :value="item.propertyId">
                              </el-option>
                            </el-select>
                          </template>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row class="elRow">
                      <el-col :span="4">
                        <div class="addChannel_right">
                          <span class="redFont">*</span>权重系数
                        </div>
                      </el-col>
                      <el-col :span="6">
                        <div class="addChannel_right">
                          <el-input v-model="weight" placeholder="权重系数"></el-input>
                        </div>
                      </el-col>
                    </el-row>
                    <span slot="footer" class="dialog-footer">
                      <el-button type="info" @click="dialogVisible = false;cancel()">取 消</el-button>
                      <el-button type="primary" @click="dialogVisible = false;addMode()">新增</el-button>
                    </span>
                  </el-dialog>
                  <table style="width:100%;text-align: center" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                      <tr>
                        <th>方式名称</th>
                        <th>方式编号</th>
                        <th>权重</th>
                        <th>操作</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in tableData1">
                        <td>{{item.method_name}}</td>
                        <td>{{item.method_sn}}</td>
                        <td>{{item.method_weight}}</td>
                        <td><el-button type="text" @click="dialogVisibleT = true;modify(item.id)"><el-tag size="medium"><i class="el-icon-setting"></i></el-tag></el-button></td>
                        <el-dialog title="编辑方式" :visible.sync="dialogVisibleT" width="800px">
                          <el-row class="elRow">
                            <el-col :span="4">
                              <div class="addChannel_left">
                                <span class="redFont">*</span>方式名称
                              </div>
                            </el-col>
                            <el-col :span="6">
                              <div class="addChannel_left">
                                <el-input v-model="modeName" placeholder="方式名称"></el-input>
                              </div>
                            </el-col>
                            <el-col :span="4" :offset="1">
                              <div class="addChannel_left">
                                <span class="redFont">*</span>方式属性
                              </div>
                            </el-col>
                            <el-col :span="6">
                              <div class="addChannel_left">
                                <template>
                                  <el-select v-model="modeProperty" placeholder="请选择方式属性">
                                    <el-option v-for="item in channelPropertys" :key="item.value" :label="item.propertyValue" :value="item.propertyId">
                                    </el-option>
                                  </el-select>
                                </template>
                              </div>
                            </el-col>
                          </el-row>
                          <el-row class="elRow">
                            <el-col :span="4">
                              <div class="addChannel_right">
                                <span class="redFont">*</span>权重系数
                              </div>
                            </el-col>
                            <el-col :span="6">
                              <div class="addChannel_right">
                                <el-input v-model="modeweight" placeholder="权重系数"></el-input>
                              </div>
                            </el-col>
                          </el-row>
                          <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleT = false;cancel()">取 消</el-button>
                            <el-button type="primary" @click="dialogVisibleT = false;modifySubmit()">修改</el-button>
                          </span>
                        </el-dialog>
                      </tr>
                    </tbody>
                  </table>
                </el-col>
              </el-row>
                <div v-if="isShow" style="text-align: center;line-height: 50px"><img class="notData" src="../../image/notData.png"/></div>
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
        dialogVisible:false,
        dialogVisibleT:false,
        url: `${this.$baseUrl}`,
        channelName:'',
        weight:'',
        channelProperty:'',
        channelPropertys:[{
          "propertyId":1,
          "propertyValue":"自采",
        },{
          "propertyId":2,
          "propertyValue":"外采",
        }],
        //操作时
        modeName:'',
        modeweight:'',
        modifyAId:'',
        modeProperty:'',//方式属性
        loading:'',
        isShow:false,
        headersStr:'',
        //搜索
        method_name:'',
        method_sn:'',
      }
    },
    mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getModeData()
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      getModeData(){
        let vm=this;
        axios.get(vm.url+vm.$methodListURL+"?method_name="+vm.method_name+"&method_sn="+vm.method_sn,
        {
          headers:vm.headersStr,
        }
        ).then(function(res){
          vm.tableData1=res.data.data
          vm.loading.close()
          if(res.data.code==1002){
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
      //新增方式
      addMode(){
        let vm=this;
        if(vm.channelName==''){
          vm.$message('采购方式不能为空！');
          return false;
        }
        if(vm.weight==''){
          vm.$message('权重系数不能为空！');
          return false;
        }
        if(vm.channelProperty==''){
          vm.$message('方式属性不能为空！');
          return false;
        }
        axios.post(vm.url+vm.$createMethodURL,
          {
            "method_name":vm.channelName,
            "method_weight":vm.weight,
            "method_property":vm.channelProperty,
          },
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          vm.dialogVisible = false;
          vm.channelName='';
          vm.weight='';
          vm.channelProperty='';
          vm.$message(res.data.msg);
          if(res.data.code==1000){
            vm.isShow=false;
          }
          vm.getModeData();
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //获取编辑时的列表
      modify(e){
        let vm=this;
        axios.post(vm.url+vm.$editMethodURL,
          {
            "id":e,
          },
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          res.data.data.forEach(element => {
            vm.modeName=element.method_name;
            vm.modeweight=element.method_weight;
            vm.modeProperty=element.method_property;
          });
          vm.modifyAId=e;
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //提交编辑时的内容
      modifySubmit(){
        let vm=this;
        axios.post(vm.url+vm.$doEditMethodURL,
          {
            "id":vm.modifyAId,
            "method_name":vm.modeName,
            "method_weight":vm.modeweight,
            "method_property":vm.modeProperty,
          },
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          vm.dialogVisibleT=false
          vm.$message(res.data.msg);
          vm.modeName='';
          vm.modeweight='';
          vm.modeProperty='';
          vm.getModeData();
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      cancel(){
        let vm=this;
        vm.modeName='';
        vm.modeweight='';
        vm.modifyAId='';
        vm.channelName='';
        vm.weight='';
        vm.channelProperty='';
        vm.modeProperty='';
      }
    }
}
</script>

<style scoped>
@import '../../css/publicCss.css';
</style>

