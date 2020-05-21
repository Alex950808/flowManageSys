<template>
  <div class="fundChannelList">
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
          <el-row class="bgDiv">
              <fontWatermark :zIndex="false"></fontWatermark>
              <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                        <el-row>
                            <el-col :span="21">
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;资金渠道列表</span>
                            </el-col>
                            <el-col :span="3"><div class="notBgButton sameDayD" @click="addChannel()"><i class="iconfont upDataIcon verticalAlign">&#xea22;</i>新增资金渠道</div></el-col>
                        </el-row>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>资金渠道列表</span></div></el-col>
                      <el-col :span="2"><div>客户</div></el-col>
                      <el-col :span="3" >
                          <template>
                            <el-select v-model="sale_user_id" clearable placeholder="请选择客户">
                              <el-option v-for="item in selectData.sale_user_list" :key="item.id" :label="item.user_name" :value="item.id">
                              </el-option>
                            </el-select>
                          </template>
                      </el-col>
                      <el-col :span="3"><div>资金渠道名称</div></el-col>
                      <el-col :span="3"><div><el-input v-model="fund_channel_name" placeholder="请输入资金渠道名称"></el-input></div></el-col>
                      <el-col :span="3"><div>资金渠道类别</div></el-col>
                      <el-col :span="3">
                        <template>
                            <el-select v-model="fund_cat_id" clearable placeholder="请选择资金渠道类别">
                              <el-option v-for="item in selectData.fund_cat_list" :key="item.id" :label="item.fund_cat_name" :value="item.id">
                              </el-option>
                            </el-select>
                        </template>
                      </el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDetailsData()">搜索</span></el-col>
                      <el-col :span="3"><div class="notBgButton sameDayD" @click="addChannel()"><i class="iconfont upDataIcon verticalAlign">&#xea22;</i>新增资金渠道</div></el-col>
                    </el-row>
                    <el-row class="tableStyle">
                        <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>资金渠道id</th>
                                    <th>客户</th>
                                    <th>资金渠道名称</th>
                                    <th>资金渠道类别</th>
                                    <th>编辑</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in tableData">
                                    <td>{{item.id}}</td>
                                    <td>{{item.sale_user_name}}</td>
                                    <td>{{item.fund_channel_name}}</td>
                                    <td>{{item.fund_cat_name}}</td>
                                    <td>
                                        <i class="iconfont editGoods" @click="openEdit(item.id)">&#xe62f;</i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </el-row>
                    <el-dialog title="新增资金渠道" :visible.sync="dialogVisibleAdd" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class="">资金渠道类别：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="channel" placeholder="请选择资金渠道类别">
                                            <el-option v-for="item in channelList" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="">资金渠道名称：</div></el-col>
                            <el-col :span="6"><div class=""><el-input v-model="nameOfCapital" placeholder="请输入渠道名称"></el-input></div></el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4">客户：</el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="userName" placeholder="请选择用户名">
                                            <el-option v-for="item in userNames" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleAdd = false">取 消</el-button>
                            <el-button type="primary" @click="doaddChannel();">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-dialog title="编辑资金渠道" :visible.sync="dialogVisibleEdit" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class="">资金渠道类别：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="channelEdit" placeholder="请选择">
                                            <el-option v-for="item in channelEditList" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="">资金渠道名称：</div></el-col>
                            <el-col :span="6"><div class=""><el-input v-model="nameOfCapitalEdit" placeholder="请输入渠道名称"></el-input></div></el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4">客户：</el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="userNameEdit" placeholder="请选择用户名">
                                            <el-option v-for="item in userNameEdits" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleEdit = false">取 消</el-button>
                            <el-button type="primary" @click="dialogVisibleEdit = false;doEdit()">确 定</el-button>
                        </span>
                    </el-dialog>
              </el-col>
          </el-row>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
import { getDataList } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      fontWatermark
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      dialogVisibleAdd: false,
      dialogVisibleEdit:false,
      nameOfCapital:'',
      tableData:[],
      selectData:[],
      channelList:[],
      channel:'',
      headersStr:'',
      fund_channel_id:'',
      userNames:[],
      userName:'',
      //编辑数据 
      nameOfCapitalEdit:'',
      channelEdit:'',
      channelEditList:[],
      userNameEdits:[],
      userNameEdit:'',
      //搜索
      fund_channel_name:'',
      fund_cat_id:'',
      sale_user_id:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    this.getDetailsData();
  },
  methods:{
    getDetailsData(){
        let vm=this;
        // getDataList(vm.url,vm.$getFundChannelListURL);
        axios.get(vm.url+vm.$getFundChannelListURL+"?fund_channel_name="+vm.fund_channel_name+"&fund_cat_id="+vm.fund_cat_id+"&sale_user_id="+vm.sale_user_id,
            {
                headers:vm.headersStr
            }
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.data.fund_channel_info.data;
            vm.selectData=res.data.data;
        }).catch(function (error) {
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //打开新增渠道
    addChannel(){
        let vm=this;
        $(event.target).addClass("disable");
        vm.channelList.splice(0);
        vm.userNames.splice(0);
        vm.cleanData();
        axios.get(vm.url+vm.$addFundChannelURL,
            {
                headers:vm.headersStr
            }
        ).then(function(res){
        $(".sameDayD").removeClass("disable");
            if(res.data.code==1000){
                vm.dialogVisibleAdd = true;
                res.data.data.fund_cat_list.forEach(element => {
                    vm.channelList.push({"label":element.fund_cat_name,"id":element.id});
                });
                res.data.data.sale_user_list.forEach(element => {
                    vm.userNames.push({"label":element.user_name,"id":element.id});
                });
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
    //确认新增渠道
    doaddChannel(){
        let vm=this;
        if(vm.userName==''){
            vm.$message('用户名称不能为空！');
            return false;
        }
        if(vm.nameOfCapital==''){
            vm.$message('渠道名称不能为空！');
            return false;
        }
        if(vm.channel==''){
            vm.$message('资金渠道类别不能为空！');
            return false;
        }
        vm.dialogVisibleAdd = false;
        axios.post(vm.url+vm.$doAddFundChannelURL,
            {
                "fund_channel_name":vm.nameOfCapital,
                "fund_cat_id":vm.channel,
                "sale_user_id":vm.userName,
            },
            {
                headers:vm.headersStr
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code==1000){
                vm.dialogVisibleAdd = false;
                vm.getDetailsData();
                vm.$message(res.data.msg);
            }else if(res.data.code==1002){
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
    //打开编辑资金渠道
    openEdit(id){
        let vm = this;
        $(event.target).addClass("disable");
        vm.userNameEdits.splice(0);
        vm.channelEditList.splice(0);
        vm.cleanData();
        axios.get(vm.url+vm.$editFundChannelURL+"?fund_channel_id="+id,
            {
                headers:vm.headersStr
            }
        ).then(function(res){
            $(".editGoods").removeClass("disable");
            if(res.data.code==1000){
                vm.dialogVisibleEdit=true;
                res.data.data.fund_cat_list.forEach(element=>{
                    vm.channelEditList.push({"label":element.fund_cat_name,"id":element.id});
                })
                res.data.data.sale_user_list.forEach(element=>{
                    vm.userNameEdits.push({"label":element.user_name,"id":element.id});
                })
                vm.nameOfCapitalEdit=res.data.data.fund_channel_info[0].fund_channel_name;
                vm.channelEdit=res.data.data.fund_channel_info[0].fund_cat_id;
                vm.fund_channel_id=res.data.data.fund_channel_info[0].id;
                vm.userNameEdit=res.data.data.fund_channel_info[0].sale_user_id;
                // vm.seleceChannelEdit();
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
    //确定编辑
    doEdit(){
        let vm = this;
        if(vm.userNameEdit==''){
            vm.$message('用户名称不能为空！');
            return false;
        }
        if(vm.nameOfCapitalEdit==''){
            vm.$message('渠道名称不能为空！');
            return false;
        }
        if(vm.channelEdit==''){
            vm.$message('资金渠道类别不能为空！');
            return false;
        }
        axios.post(vm.url+vm.$doEditFundChannelURL,
        {
                "fund_channel_name":vm.nameOfCapitalEdit,
                "fund_cat_id":vm.channelEdit,
                "fund_channel_id":vm.fund_channel_id,
                "sale_user_id":vm.userNameEdit,
        },
        {
            headers:vm.headersStr,
        },
        )
        .then(function(res){
            if(res.data.code==1000){
                vm.dialogVisibleEdit = false;
                vm.getDetailsData();
                vm.$message(res.data.msg);
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              router.push('/');
            }
        });
    },
    // 清除数据
    cleanData(){
        let vm = this;
        vm.nameOfCapitalEdit='';
        vm.channelEdit='';
        vm.fund_channel_id='';
        vm.userNameEdit='';
        vm.nameOfCapital='';
        vm.channel='';
        vm.userName='';
    }
  }
}
</script>

<style scoped>
 /* table tr{
    line-height: 60px;
}
 table tr th,td{
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5;
}
 table tr:nth-child(even){
    background:#fafafa;
}
 table tr:hover{
    background:#ced7e6;
}
.editGoods{
    cursor: pointer;
} */
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
