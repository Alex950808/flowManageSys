<template>
  <div class="somePageName">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle">
                        <span><span class="coarseLine MR_ten"></span>采购金额列表</span>
                        <el-date-picker v-model="start_time" value-format="yyyy-MM-dd" type="date" placeholder="选择开始时间"></el-date-picker>
                        <el-date-picker v-model="end_time" value-format="yyyy-MM-dd" type="date" placeholder="选择结束时间"></el-date-picker>
                        <span class="bgButton MT_twenty MR_twenty" @click="search()">搜索</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openAddBox()">维护采购金额</span>
                    </div>
                    <table v-if="!isShow" class="fontCenter" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <!-- <th class="w_Fty" style="width:60px" rowspan="2"><input class="allChecked" type="checkbox" @change="allSelect()"/>全选</th> -->
                                <th>消费类型</th>
                                <th>返点比例</th>
                                <th>方式</th>
                                <th>渠道</th>
                                <th>消费金额</th>
                                <th>日期</th>
                                <!-- <th>毛利公式</th>
                                <th>操作</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <!-- <td class="w_Fty cb_one" style="width:60px">
                                    <input @click="oneSelect(index)" type="checkbox" :name="index"/>
                                </td> -->
                                <td>{{item.consume_type}}</td>
                                <td>{{item.final_return_rate}}%</td>
                                <td>{{item.method_name}}</td>
                                <td>{{item.channels_name}}</td>
                                <td>{{item.consume_money}}</td>
                                <td>{{item.day_time}}</td>
                                <!-- <td style="width:450px;">{{item.formula}}</td>
                                <td><span class="bgButton" @click="openDeleteBox(item.profit_sn)">停用</span></td> -->
                            </tr>
                        </tbody>
                    </table>
                    <el-dialog title="维护采购金额" :visible.sync="dialogVisibleSetgAddCC" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>日期：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-date-picker style="width:190px;" v-model="day_time" value-format="yyyy-MM-dd" type="date" placeholder="请选择日期"></el-date-picker>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>渠道-方式：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="channelsIdI" placeholder="请选择渠道-方式">
                                        <el-option v-for="item in channelsIdL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>金额：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-input v-model="consume_money" type="Number" placeholder="请输入金额"></el-input>
                            </div> 
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>消费类型：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="consumeType" placeholder="请选择消费类型">
                                        <el-option v-for="item in consumeTypeL" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleSetgAddCC = false;">取 消</el-button>
                            <el-button type="primary" @click="doAddCardConsume()">确 定</el-button>
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
      start_time:'',
      end_time:'',
      //维护采购金额
      dialogVisibleSetgAddCC:false,
      day_time:'',
      channelsIdL:[],
      channelsIdI:'',
      consume_money:'',
      consumeType:'',
      consumeTypeL:[{"name":"结账卡消费金额","id":"1"},{"name":"充值金额","id":"2"}],
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
        axios.get(vm.url+vm.$getCardConsumeListURL+"?start_time="+vm.start_time+"&end_time="+vm.end_time, 
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code=='1000'){
                vm.tableData=res.data.data;
                vm.isShow=false;
            }else if(res.data.code=='1001'){
                vm.tableData=[];
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
    search(){
        let vm = this;
        if(vm.start_time==null){
            vm.start_time='';
        }
        if(vm.end_time==null){
            vm.end_time='';
        }
        vm.getDataList();
    },
    openAddBox(){
        let vm = this;
        vm.dialogVisibleSetgAddCC=true;
        vm.channelsIdL.splice(0);
        vm.day_time='';
        vm.channelsIdI='';
        vm.consume_money='';
        vm.consumeType='';
        axios.get(vm.url+vm.$addCardConsumeURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            res.data.data.forEach(element => {
                vm.channelsIdL.push({"name":element.channels_name+"-"+element.method_name,"id":element.method_id+","+element.id})
            });
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //
    doAddCardConsume(){
        let vm = this;
        if(vm.day_time===''){
            vm.$message('请填写日期后提交!');
            return false;
        }
        if(vm.channelsIdI===''){
            vm.$message('请填写渠道-方式后提交!');
            return false;
        }
        if(vm.consume_money===''){
            vm.$message('请填写金额后提交!');
            return false;
        }
        if(vm.consumeType===''){
            vm.$message('请填写消费类型后提交!');
            return false;
        }
        vm.dialogVisibleSetgAddCC=false;
        let load = Loading.service({fullscreen: true, text: '维护采购金额中....'})
        axios.post(vm.url+vm.$doAddCardConsumeURL,
            {
                "day_time":vm.day_time,
                "method_id":vm.channelsIdI.split(',')[0],
                "channels_id":vm.channelsIdI.split(',')[1],
                "consume_money":vm.consume_money,
                "consume_type":vm.consumeType,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            vm.$message(res.data.msg);
            if(res.data.code=='1000'){
                vm.getDataList()
            }
        }).catch(function (error) {
            load.close();
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
