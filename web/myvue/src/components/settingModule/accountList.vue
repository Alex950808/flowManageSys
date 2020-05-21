<template>
  <div class="accountList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <!-- <div class="title listTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;账户列表</span>
                        <i class="floatRight bgButton NotStyleForI MT_twenty" @click="addChannelIntegral()">新增渠道积分</i>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>账户列表</span></div></el-col>
                      <el-col :span="2"><div>方式</div></el-col>
                      <el-col :span="3" >
                        <el-select v-model="method_id" clearable placeholder="请选择方式">
                            <el-option v-for="item in method_info" :key="item.value" :label="item.method_name" :value="item.id"></el-option>
                        </el-select>
                      </el-col>
                      <el-col :span="2"><div>渠道</div></el-col>
                      <el-col :span="3">
                        <el-select v-model="channels_id" clearable placeholder="请选择方式">
                            <el-option v-for="item in channel_info" :key="item.value" :label="item.channels_name" :value="item.id"></el-option>
                        </el-select>
                      </el-col>
                      <el-col :span="2"><div>开始时间</div></el-col>
                      <el-col :span="3">
                          <div>
                              <el-date-picker style="width:190px;" v-model="startDate" type="date" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                          </div>
                      </el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                      <el-col :span="3"><i class="bgButton NotStyleForI" @click="addChannelIntegral()">新增渠道积分</i></el-col>
                    </el-row>
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="2" :offset="3"><div>结束时间</div></el-col>
                      <el-col :span="3">
                          <div>
                              <el-date-picker style="width:190px;" v-model="endDate" type="date" value-format="yyyy-MM-dd" placeholder="请选择结束时间"></el-date-picker>
                          </div>
                      </el-col>
                    </el-row>
                    <el-row>
                        <table class="MB_twenty" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>渠道名称</th>
                                    <th>方式名称</th>
                                    <th>渠道积分</th>
                                    <th>渠道余款</th>
                                    <th>待返积分</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in tableData">
                                    <td>{{item.channels_name}}</td>
                                    <td>{{item.method_name}}</td>
                                    <td>{{item.integral_balance}}</td>
                                    <td>{{item.money_balance}}</td>
                                    <td>{{item.total_integral}}</td>
                                    <td>
                                        <span class="notBgButton" @click="editChannelIntegral(item.channels_id)">编辑渠道积分</span>
                                        <!-- <span v-if="$store.state.clickStutes==false" class="notBgButton">编辑渠道积分</span> -->
                                    </td>
                                </tr>
                            </tbody> 
                        </table>
                        
                        <el-dialog title="新增渠道积分" :visible.sync="addChannelDialogVisible" width="800px">
                            <el-row class="MB_twenty">
                                <el-col :span="4" :offset="1"><div class="MT_ten">渠道名称<i class="redFont">*</i>：</div></el-col>
                                <el-col :span="6">
                                    <template>
                                        <el-select v-model="channelName" v-validate="'required'" name="channelName" placeholder="请您选择渠道名称">
                                            <el-option v-for="item in channelNames" :key="item.id" :label="item.channels_name" :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </template>
                                    <span v-show="errors.has('channelName')" class="text-style redFont" v-cloak> {{ errors.first('channelName') }} </span>
                                </el-col>
                                <el-col :span="4" :offset="1"><div class="MT_ten">渠道积分<i class="redFont">*</i>：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="integralBalance" v-validate="'required'" name="integralBalance" placeholder="请输入渠道积分"></el-input>
                                        <span v-show="errors.has('integralBalance')" class="text-style redFont" v-cloak> {{ errors.first('integralBalance') }} </span>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row class="MB_twenty">
                                <el-col :span="4" :offset="1"><div class="MT_ten">渠道余款<i class="redFont">*</i>：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="moneyBalance" v-validate="'required'" name="moneyBalance" placeholder="请输入渠道余款"></el-input>
                                        <span v-show="errors.has('moneyBalance')" class="text-style redFont" v-cloak> {{ errors.first('moneyBalance') }} </span>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="addChannelDialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="doAddChannelIntegral()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <el-dialog title="编辑渠道积分" :visible.sync="editChannelDialogVisible" width="800px">
                            <el-row class="MB_twenty">
                                <el-col :span="4" :offset="1"><div class="MT_ten">渠道名称：</div></el-col>
                                <el-col :span="6" class="MT_ten">
                                    {{channelName}}
                                </el-col>
                                <el-col :span="4" :offset="1"><div class="MT_ten">渠道积分：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="integralBalance" v-validate="'required'" name="integralBalance" placeholder="请输入渠道积分"></el-input>
                                        <span v-show="errors.has('integralBalance')" class="text-style redFont" v-cloak> {{ errors.first('integralBalance') }} </span>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row class="MB_twenty">
                                <el-col :span="4" :offset="1"><div class="MT_ten">渠道余款：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="moneyBalance" v-validate="'required'" name="moneyBalance" placeholder="请输入渠道积分"></el-input>
                                        <span v-show="errors.has('moneyBalance')" class="text-style redFont" v-cloak> {{ errors.first('moneyBalance') }} </span>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="editChannelDialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="doEditChannelIntegral()">确 定</el-button>
                            </span>
                        </el-dialog>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                    <div v-show="!isShow" class="switchPolygraph"></div>
                    <div v-show="!isShow" id="myChart" :style="{width: '100%', height: '300px'}"></div>
                    <el-row class="fontCenter MT_twenty">
                        <span v-for="(item,index) in channelsNameList" v-if="index==0" @click="accountDetail(item.id)" class="bgButton MR_twenty">
                            {{item.name}}
                        </span>
                        <span v-for="(item,index) in channelsNameList" v-if="index!=0" @click="accountDetail(item.id)" class="grBgButton MR_twenty">
                            {{item.name}}
                        </span>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数 
    //   spanIndex:'1',
      //新增渠道积分 
      addChannelDialogVisible:false,
      channelName:'',
      integralBalance:'',
      moneyBalance:'',
      channelNames:[],
      //编辑渠道积分 
      editChannelDialogVisible:false,
      channels_id:'',
      //
      x_line:[],
      integral_log:[],
      money_log:[],
      channelsNameList:[],
      //搜索
      startDate:'',
      endDate:'',
      channel_info:[],
      channels_id:'',
      method_info:[],
      method_id:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.getChannelMethod();
  },
  methods:{
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        vm.x_line.splice(0);
        vm.integral_log.splice(0);
        vm.money_log.splice(0);
        vm.channelsNameList.splice(0);
        axios.get(vm.url+vm.$accountListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&start_date="+vm.startDate+"&end_date="+vm.endDate
        +"&channels_id="+vm.channels_id+"&method_id="+vm.method_id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.channel_integral_list;
                vm.tableData.forEach(element=>{
                    vm.channelsNameList.push({"name":element.channels_name,"id":element.channels_id});
                })
                res.data.data.integral_log.forEach(element=>{
                    vm.x_line.push(element.create_time);
                    vm.integral_log.push(element.modify_num)
                })
                res.data.data.money_log.forEach(element=>{
                    vm.money_log.push(element.modify_num)
                })
                vm.drawLine();
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.isShow=true;
                vm.tableData=[];
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
    //获取方式和渠道
    getChannelMethod(){
        let vm = this;
        axios.get(vm.url+vm.$channelMethodListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            console.log(res);
            vm.channel_info=res.data.data.channel_info;
            vm.method_info=res.data.data.method_info;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
        
    },
    //折线图
    accountDetail(id){
        let vm = this;
        vm.x_line.splice(0);
        vm.integral_log.splice(0);
        vm.money_log.splice(0);
        $(event.target).removeClass('grBgButton').addClass('bgButton');
        $(event.target).siblings().removeClass('bgButton').addClass('grBgButton');
        axios.get(vm.url+vm.$accountDetailURL+"?channels_id="+id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            res.data.data.integral_log.forEach(element=>{
                vm.x_line.push(element.create_time);
                vm.integral_log.push(element.modify_num)
            })
            res.data.data.money_log.forEach(element=>{
                vm.money_log.push(element.modify_num)
            })
            
            vm.drawLine();
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //新增渠道积分
    addChannelIntegral(){
        let vm = this;
        // vm.$store.commit('editClickStutes');
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        // vm.channelNames.splice(0);
        vm.integralBalance='';
        vm.moneyBalance='';
        vm.channelName='';
        axios.get(vm.url+vm.$addChannelIntegralURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            console.log(res);
            // vm.$store.commit('editClickStutes');
            load.close();
            if(res.data.code=="1000"){
                vm.addChannelDialogVisible=true;
                vm.channelNames=res.data.data;
                // res.data.data.forEach(element => {
                //     vm.channelNames.push({"id":element.id,"channels_name":element.channels_name+'-'+element.method_name});
                // });
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            // vm.$store.commit('editClickStutes');
            load.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //确定新增渠道积分
    doAddChannelIntegral(){
        let vm = this;
        this.$validator.validateAll().then(valid => {
            if(valid){
                vm.addChannelDialogVisible = false;
                axios.post(vm.url+vm.$doAddChannelIntegralURL,
                    {
                        "channels_id":vm.channelName,
                        "integral_balance":vm.integralBalance,
                        "money_balance":vm.moneyBalance,
                    },
                    {
                        headers:vm.headersStr,
                    },
                ).then(function(res){
                    if(res.data.code=='1000'){
                        vm.getDataList();
                    }
                    vm.$message(res.data.msg);
                }).catch(function (error) {
                    if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                    }
                });
            }
        })
    },
    //打开编辑渠道积分弹框
    editChannelIntegral(id){
        let vm = this;
        // vm.$store.commit('editClickStutes');
        vm.channels_id=id;
        vm.integralBalance='';
        vm.moneyBalance='';
        axios.get(vm.url+vm.$editChannelIntegralURL+"?channels_id="+id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            // vm.$store.commit('editClickStutes');
            if(res.data.code=='1000'){
                vm.channelName=res.data.data[0].channels_name+"-"+res.data.data[0].method_name;
                vm.integralBalance=res.data.data[0].integral_balance;
                vm.moneyBalance=res.data.data[0].money_balance;
                vm.editChannelDialogVisible=true;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            // vm.$store.commit('editClickStutes');
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //确定编辑
    doEditChannelIntegral(){
        let vm = this;
        this.$validator.validateAll().then(valid => {
            if(valid){
                vm.editChannelDialogVisible=false;
                axios.post(vm.url+vm.$doEditChannelIntegralURL,
                    {
                        "channels_id":vm.channels_id,
                        "integral_balance":vm.integralBalance,
                        "money_balance":vm.moneyBalance,
                    },
                    {
                        headers:vm.headersStr,
                    },
                ).then(function(res){
                    vm.$message(res.data.msg);
                    if(res.data.code=='1000'){
                        vm.getDataList();
                    }
                }).catch(function (error) {
                    if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                    }
                });
            }
        }) 
    },
    drawLine(){
        let vm = this;
        // 基于准备好的dom，初始化echarts实例
        let myChart = this.$echarts.init(document.getElementById('myChart'))
        // 绘制图表
        myChart.setOption({
            title: { text: '渠道积分及余款统计' },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data:['渠道积分','渠道余款']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: vm.x_line
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name:'渠道积分',
                    type:'line',
                    stack: '总量',
                    data:vm.integral_log
                },
                {
                    name:'渠道余款',
                    type:'line',
                    stack: '总量',
                    data:vm.money_log
                },
            ]
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
