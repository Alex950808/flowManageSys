<template>
  <div class="purchaseID_b">
    <!-- 采购ID页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="purchaseID bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <!-- <el-row>
                            <el-col :span="21" class="title_left tableTitleStyle">
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购ID</span>
                            </el-col>
                            <el-col :span="3" class="title_right tableTitleStyle">
                                
                                
                            </el-col>
                        </el-row>   -->
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>采购ID</span></div></el-col>
                            <el-col :span="2"><div>账号</div></el-col>
                            <el-col :span="3"><div><el-input v-model="account_number" placeholder="请输入账号"></el-input></div></el-col>
                            <el-col :span="3"><div>姓名</div></el-col>
                            <el-col :span="3"><div><el-input v-model="real_name" placeholder="请输入采购人员姓名"></el-input></div></el-col>
                            <el-col :span="3"><div>护照号</div></el-col>
                            <el-col :span="3"><div><el-input v-model="passport_sn" placeholder="请输入采购人员护照"></el-input></div></el-col>
                            <el-col :span="2"><span class="bgButton" @click="getPurchaseID()">搜索</span></el-col>
                            <el-col :span="2"><span type="text" class="notBgButton" @click="dialogVisible = true"><i class="iconfont upDataIcon" style="vertical-align: -4px;">&#xea22;</i>新增ID</span></el-col>
                        </el-row>
                        <el-dialog class="Popup_title" title="新增ID" :visible.sync="dialogVisible" width="800px">
                            <el-row>
                                <el-col :span="11">
                                    <div class="newAdd_left">
                                        <el-row style="margin-bottom: 20px;">
                                            <el-col :span="6"><div>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</div></el-col>
                                            <el-col :span="18"><div><el-input v-model="name" placeholder="请输入姓名"></el-input></div></el-col>
                                        </el-row>
                                        <el-row style="margin-bottom: 20px;">
                                            <el-col :span="6"><div>护&nbsp;&nbsp;照&nbsp;&nbsp;号</div></el-col>
                                            <el-col :span="18"><div>
                                            <el-input v-model="passport" placeholder="请输入护照号"></el-input>
                                                </div></el-col>
                                        </el-row>
                                        <el-row style="margin-bottom: 20px;">
                                            <el-col :span="6"><div>账&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号</div></el-col>
                                            <el-col :span="18"><div><el-input v-model="num" placeholder="请输入账号"></el-input></div></el-col>
                                        </el-row>
                                    </div>
                                </el-col>
                                <el-col :span="11" :offset="1">
                                    <div class="newAdd_right">
                                        <el-row style="margin-bottom: 20px;">
                                            <el-col :span="6"><div>采购方式</div></el-col>
                                            <el-col :span="18"><div>
                                            <template>
                                                <el-select v-model="mode" @change="getChannelData('A')" placeholder="选择方式">
                                                    <el-option v-for="item in modes" :key="item.id" :label="item.method_name" :value="item.id">
                                                    </el-option>
                                                </el-select>
                                            </template>
                                            </div></el-col>
                                        </el-row>
                                        <el-row style="margin-bottom: 20px;">
                                            <el-col :span="6"><div>采购渠道</div></el-col>
                                            <el-col :span="18"><div>
                                                <template>
                                                    <el-select v-model="channel" placeholder="选择渠道">
                                                        <el-option v-for="item in channels" :key="item.value" :label="item.label" :value="item.channel">
                                                        </el-option>
                                                    </el-select>
                                                </template>
                                                </div></el-col>
                                        </el-row>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;newAddId()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>账号</th>
                                <th>姓名</th>
                                <th>护照号</th>
                                <th>方式</th>
                                <th>渠道</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in tableData">
                                <td>{{item.account_number}}</td>
                                <td>{{item.real_name}}</td>
                                <td>{{item.passport_sn}}</td>
                                <td>{{item.method_name}}</td>
                                <td>{{item.channels_name}}</td>
                                <td><el-button type="text" @click="dialogVisibleT = true;openEdit(item.id)"><el-tag size="medium"><i class="el-icon-setting"></i></el-tag></el-button></td>
                            </tr>
                            </tbody>
                        </table>
                    </el-col>
                </el-row>
                <el-dialog class="Popup_title" title="修改ID" :visible.sync="dialogVisibleT" width="800px">
                    <!-- <div class="Popup_title">修改ID</div> -->
                    <el-row>
                        <el-col :span="11">
                            <div class="newAdd_left">
                                <el-row style="margin-bottom: 20px;">
                                    <el-col :span="6"><div>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</div></el-col>
                                    <el-col :span="18"><div><el-input v-model="modify_name" placeholder="请输入姓名"></el-input></div></el-col>
                                </el-row>
                                <el-row style="margin-bottom: 20px;">
                                    <el-col :span="6"><div>护&nbsp;&nbsp;照&nbsp;&nbsp;号</div></el-col>
                                    <el-col :span="18"><div>
                                    <el-input v-model="modify_passport" placeholder="请输入护照号"></el-input>
                                        </div></el-col>
                                </el-row>
                                <el-row style="margin-bottom: 20px;">
                                    <el-col :span="6"><div>账&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号</div></el-col>
                                    <el-col :span="18"><div><el-input v-model="modify_num" placeholder="请输入账号"></el-input></div></el-col>
                                </el-row>
                            </div>
                        </el-col>
                        <el-col :span="11" :offset="1">
                            <div class="newAdd_right">
                                <el-row style="margin-bottom: 20px;">
                                    <el-col :span="6"><div>采购方式</div></el-col>
                                    <el-col :span="18"><div>
                                    <template>
                                        <el-select v-model="modify_mode" @change="getChannelData('D')" placeholder="选择方式">
                                            <el-option v-for="item in modes" :key="item.id" :label="item.method_name" :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </template>
                                    </div></el-col>
                                </el-row>
                                <el-row style="margin-bottom: 20px;">
                                    <el-col :span="6"><div>采购渠道</div></el-col>
                                    <el-col :span="18"><div>
                                        <template>
                                            <el-select v-model="modify_channel" placeholder="选择方式后再选渠道">
                                                <el-option v-for="item in channels" :key="item.value" :label="item.label" :value="item.channel">
                                                </el-option>
                                            </el-select>
                                        </template>
                                        </div></el-col>
                                </el-row>
                            </div>
                        </el-col>
                    </el-row>
                    <span slot="footer" class="dialog-footer">
                        <el-button type="info" @click="dialogVisibleT = false">取 消</el-button>
                        <el-button type="primary" @click="dialogVisibleT = false;confirmationSubmit()">确 定</el-button>
                    </span>
                </el-dialog>
                <div v-if="isShow" style="text-align: center;line-height: 50px;"><img class="notData" src="../../image/notData.png"/></div>
            </div>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
      components:{
        fontWatermark,
      },
      data() {
      return {
        tableData: [],
        dialogVisible: false,//新增
        dialogVisibleT:false,//操作
        name:'',//姓名
        mode:'',//方式
        modes:[],
        num:'',//账号
        passport:'',//护照号
        channel:'',//渠道
        channels:[],
        channelsList:[],
        url: `${this.$baseUrl}`,
        id:'',
        //修改
        modify_name:'',
        modify_passport:'',
        modify_num:'',
        modify_mode:'',
        modify_channel:'',
        isShow:false,
        headersStr:'',
        //搜索字段
        account_number:'',
        real_name:'',
        passport_sn:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getModesData();
        this.getPurchaseID()
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        //获取采购ID列表
        getPurchaseID(){
            let vm=this;
            axios.get(vm.url+vm.$userListURL+"?account_number="+vm.account_number+"&real_name="+vm.real_name+"&passport_sn="+vm.passport_sn,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close()
                vm.tableData=res.data.data
                if(res.data.code==1002){
                    vm.isShow=true;
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
        //获取采购方式列表
        getModesData(){
            let vm = this;
            axios.get(vm.url+vm.$channelMethodListURL,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                vm.channelsList=res.data.data.channel_info;
                vm.modes=res.data.data.method_info;
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //获取采购渠道列表
        getChannelData(status){
            let vm=this;
            vm.channels.splice(0);
            vm.channelsList.forEach(element => {
                if(status=='A'){
                    if(vm.mode==element.method_id){
                        vm.channels.push({"label":element.channels_name,"channel":element.id})
                    }
                }else if(status=='D'){
                    if(vm.modify_mode==element.method_id){
                        vm.channels.push({"label":element.channels_name,"channel":element.id})
                    }
                }
                
            });
        },
        //新增ID
        newAddId(){
            let vm=this;
            axios.post(vm.url+vm.$createUserURL,
                {
                    "real_name":vm.name,
                    "passport_sn":vm.passport,
                    "method_id":vm.mode,
                    "channels_id":vm.channel,
                    "account_number":vm.num
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                if(res.data.code==1000){
                    vm.dialogVisible=false;
                    vm.isShow=false;
                    vm.$message('新增ID成功');
                    vm.getPurchaseID();
                    vm.name='';
                    vm.passport='';
                    vm.mode='';
                    vm.channel='';
                    vm.num='';
                }else{
                    vm.$message(res.data.msg);
                    vm.name='';
                    vm.passport='';
                    vm.mode='';
                    vm.channel='';
                    vm.num='';
                }
                
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //编辑时打开编辑页面
        openEdit(e){
            let vm=this;
            vm.id=e;
            vm.channels.splice(0);
            axios.post(vm.url+vm.$editUserURL,
                {
                    "id":e,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                res.data.data.forEach(element=>{
                    vm.modify_name=element.real_name;
                    vm.modify_passport=element.passport_sn;
                    vm.modify_num=element.account_number;
                    vm.modify_channel=element.channels_id;
                    vm.modify_mode=element.method_id;
                })
                vm.channelsList.forEach(element => {
                    if(vm.modify_mode==element.method_id){
                        vm.channels.push({"label":element.channels_name,"channel":element.id})
                    }
                });
                
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //编辑时确认提交
        confirmationSubmit(e){
            let vm=this;
            axios.post(vm.url+vm.$doEditUserURL,
                {
                    "real_name":vm.modify_name,
                    "passport_sn":vm.modify_passport,
                    "method_id":vm.modify_mode,
                    "channels_id":vm.modify_channel,
                    "account_number":vm.modify_num,
                    "id":vm.id,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.$message(res.data.msg);
                vm.dialogVisibleT=false;
                vm.getPurchaseID()
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
    }
}
</script>
<style>
.el-table{
    text-align: center;
}
.el-table th>.cell {
    text-align: center;
}
.el-table thead{
    font-weight: bold;
    color: #000;
    background-color: #ccc !important;
}
.el-table th{
    background-color: #ccc !important;
}
.newAdd{
    float: right;
}
.coarseLine{
display: inline-block;
width:5px;
height: 20px;
vertical-align: -3px;
background: #113d62;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>