<template>
    <div class="indexPage_b">
        <el-row>
            <el-col :span="24" style="background-color: #fff;">
            <el-row class="indexPage bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="center">
                        <ul>
                            <li v-for="item in tableData">
                                <div class="content">
                                    <div class="content_text">
                                        <!-- <span class="stage ML_twenty">{{item.id}}期</span> -->
                                        <span class="start ML_twenty">采购期编号：</span><span>{{item.purchase_sn}}</span>
                                        <span class="start">批次编号：</span><span>{{item.real_purchase_sn}}</span>
                                    </div>
                                        <el-row>
                                            <table border="0" cellspacing="0" cellpadding="0">
                                                <thead>
                                                <tr>
                                                    <th>采购模板名称</th>
                                                    <th>任务日期</th>
                                                    <th>任务时刻</th>
                                                    <th>任务内容</th>
                                                    <th>任务状态</th>
                                                    <th>任务延时时间</th>
                                                    <td>操作</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="itemO in item.task_info">
                                                    <td v-if="itemO.is_system==1" style="font-weight:bold"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_name}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_name}}</span></td>
                                                    <td v-if="itemO.is_system==1" style="font-weight:bold"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_date}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_date}}</span></td>
                                                    <td v-if="itemO.is_system==1" style="font-weight:bold"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_time}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_time}}</span></td>
                                                    <td v-if="itemO.is_system==1" style="font-weight:bold"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_content}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_content}}</span></td> 
                                                    <td v-if="itemO.is_system==1&&itemO.status==0" style="font-weight:bold"><span v-if="itemO.delay_time=='00:00:00'"><i class="el-icon-question"></i></span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;"><i class="el-icon-question"></i></span></td> 
                                                    <td v-if="itemO.is_system==1&&itemO.status==1" style="font-weight:bold"><span v-if="itemO.delay_time=='00:00:00'"><i class="el-icon-success"></i></span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;"><i class="el-icon-success"></i></span></td>
                                                    <td v-if="itemO.is_system==1" style="font-weight:bold"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.delay_time}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.delay_time}}</span></td>
                                                    <td v-if="itemO.is_system==1" style="font-weight:bold">
                                                        <span v-if="itemO.status==1" class="completeT"><span>已完成</span></span>
                                                        <span v-if="itemO.status==0">
                                                            <span v-if="itemO.is_display==1" class="Refresh"  @click="Refresh(itemO.task_link)"><span>前往查看任务</span></span>
                                                            <span v-if="itemO.is_display==0"  class="grayRefresh"><span>前往查看任务</span></span>
                                                        </span>
                                                    </td>
                                                    <td v-if="itemO.is_system==0"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_name}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_name}}</span></td>
                                                    <td v-if="itemO.is_system==0"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_date}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_date}}</span></td>
                                                    <td v-if="itemO.is_system==0"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_time}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_time}}</span></td>
                                                    <td v-if="itemO.is_system==0"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.task_content}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.task_content}}</span></td> 
                                                    <td v-if="itemO.is_system==0&&itemO.status==0"><span v-if="itemO.delay_time=='00:00:00'"><i class="el-icon-question"></i></span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;"><i class="el-icon-question"></i></span></td> 
                                                    <td v-if="itemO.is_system==0&&itemO.status==1"><span v-if="itemO.delay_time=='00:00:00'"><i class="el-icon-success"></i></span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;"><i class="el-icon-success"></i></span></td>
                                                    <td v-if="itemO.is_system==0"><span v-if="itemO.delay_time=='00:00:00'">{{itemO.delay_time}}</span><span v-if="itemO.delay_time!='00:00:00'" style="color:red;">{{itemO.delay_time}}</span></td>
                                                    <td v-if="itemO.is_system==0">
                                                        <span v-if="itemO.status==1" class="completeT"><span>已完成</span></span>
                                                        <span v-if="itemO.status==0">
                                                            <span v-if="itemO.is_display==1" class="complete" @click="complete(itemO.id)"><span>完成</span></span>
                                                            <span v-if="itemO.is_display==0" class="operation"><span>完成</span></span>
                                                        </span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </el-row>
                                </div>
                            </li>
                            <li v-if="isShow" style="text-align: center;"><img class="notData" src="../image/notData.png"/></li>
                            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                            </el-pagination>
                        </ul>
                    </div>
                </el-col>
            </el-row>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../filters/publicMethods.js';//引入有公用方法的jsfile
export default {
  name: 'App',
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      search:'',
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      headersStr:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getIndexPageData();
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
    getIndexPageData(){
        let vm=this;
        axios.get(vm.url+vm.$purchaseTaskListURL+'?start_page='+vm.page+'&page_size='+vm.pagesize+"&query_sn="+vm.search,
            {
                headers:vm.headersStr
            }
        ).then(function(res){
            vm.loading.close();
                vm.getDataList();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
                vm.isShow=false;
            }else if(res.data.code==1002){
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
    getDataList(){
        let vm = this;
        axios.post(vm.url+vm.$getLoggerListURL,
            {},
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.loggerList.total != 0){
                // vm.tableData = res.data.loggerList.data;
                let loggerList = [];
                for(let i = 0; i < 10; i++){
                  loggerList.push(res.data.loggerList.data[i]);
                }
                sessionStorage.setItem("loggerList",JSON.stringify(loggerList));
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    complete(id){
        let vm=this;
      axios.post(vm.url+vm.$changeTaskStatusURL,
        {
          "id":id,
          "status":1
        },
        {
            headers:vm.headersStr,
        }
      ).then(function(res){
        vm.getIndexPageData();
        vm.$message('任务状态已更改为完成!');
      }).catch(function (error) {
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //重刷页面 
    Refresh(e){
        let vm=this;
        vm.$router.push('/'+e+'');
        location.reload();
    },
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        vm.pagesize=val
        vm.getIndexPageData();
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getIndexPageData();
    },
    //搜索框
    searchFrame(){
        let vm=this;
        vm.getIndexPageData();
    },
  }
}
</script>

<style>
.indexPage .complete{
    display: inline-block;
    width: 66px;
    height: 33px;
    line-height: 33px;
    border-radius: 50px;
    background-color: #4677c4;
    cursor: pointer;
    color: #fff;
}
.indexPage .completeT{
    display: inline-block;
    width: 60px;
    height: 20px;
    line-height: 20px;
    border-radius: 10px;
}
.indexPage .operation{
    display: inline-block;
    width: 66px;
    height: 33px;
    line-height: 33px;
    border-radius: 50px;
    cursor: pointer;
    color: #fff;
    background-color:#ccc;
}
.Refresh{
    display: inline-block;
    width: 120px;
    height: 20px;
    line-height: 20px;
    border-radius: 10px;
    color: #7092ce;
    cursor: pointer;
}
.grayRefresh{
    background-color: #ccc;
    display: inline-block;
    width: 120px;
    height: 20px;
    line-height: 20px;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
}
.center{
    background-color: #fff;
}
</style>
<style scoped lang=less>
@import '../css/taskModule.less';
</style>
