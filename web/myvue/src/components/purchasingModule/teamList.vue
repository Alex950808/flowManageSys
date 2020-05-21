<template>
  <div class="teamList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span><span class="coarseLine MR_twenty"></span>渠道团队列表</span>
                        <span class="bgButton floatRight MR_twenty MT_twenty" @click="openTeam('A')">新增团队</span>
                    </div>
                    <table class="tableStyle" style="width:100%;text-align: center;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>团队名称</th>
                                <th>所属渠道</th>
                                <th>编辑团队</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>{{item.team_name}}</td>
                                <td>{{item.channels_name}}</td>
                                <td><span class="notBgButton" @click="openTeam('D')">编辑</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <el-dialog :title="titleStr" :visible.sync="dialogVisibleAdd" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>团队名称：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-input v-model="teamName" placeholder="请输入团队名称"></el-input>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div style="line-height: 23px;margin-top: 10px;"><span class="redFont">*</span>所属渠道：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="channelId" placeholder="请选择所属渠道">
                                            <el-option v-for="item in channelIdList" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleAdd = false;">取 消</el-button>
                            <el-button type="primary" @click="confirmUpData()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
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
      isShow:false,
      //新增/编辑团队
      teamName:'',
      channelId:'',
      channelIdList:[],
      dialogVisibleAdd:false,
      titleStr:'',
      text:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$teamListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
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
    //获取采购渠道列表 
    openTeam(text){
        let vm=this;
        vm.text=text;
        axios.get(vm.url+vm.$channelsListURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close()
            if(res.data.code==1000){
                res.data.data.forEach(element => {
                    vm.channelIdList.push({"name":element.channels_name,"id":element.id})
                });
            }
        }).catch(function (error) {
            vm.loading.close()
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
        vm.dialogVisibleAdd=true;
        if(text=='A'){//新增
            vm.titleStr='新增团队';
        }else if(text=='D'){//编辑
            vm.titleStr='编辑团队';
        }
    },
    //新增/编辑团队
    confirmUpData(){
        let vm = this;
        vm.dialogVisibleAdd=false;
        let url = '';
        if(vm.text=='A'){//新增
            url=vm.$addTeamURL;
        }else if(vm.text=='D'){//编辑
            url=vm.$editTeamURL;
        }
        axios.post(vm.url+url,
            {
                "team_name":vm.teamName,
                "channel_id":vm.channelId,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
        }).catch(function (error) {
            vm.loading.close()
            if(error.response.status!=''&&error.response.status=="401"){
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
@import '../../css/publicCss.css';
</style>
