<template>
  <div class="targetList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>目标列表</span></div></el-col>
                      <el-col :span="2"><div>客户ID</div></el-col>
                      <el-col :span="3">
                        <el-select v-model="user_id" clearable placeholder="请选择客户ID">
                            <el-option v-for="item in userList" :key="item.value" :label="item.user_name" :value="item.id"></el-option>
                        </el-select>
                      </el-col>
                      <el-col :span="2"><div>开始时间</div></el-col>
                      <el-col :span="3">
                          <div>
                              <el-date-picker style="width:190px;" v-model="start_date" type="date" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                          </div>
                      </el-col>
                      <el-col :span="2"><div>结束时间</div></el-col>
                      <el-col :span="3">
                          <div>
                              <el-date-picker style="width:190px;" v-model="end_date" type="date" value-format="yyyy-MM-dd" placeholder="请选择结束时间"></el-date-picker>
                          </div>
                      </el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                      <span class="bgButton MR_ten" @click="clearData();AoTtarget(-1)">新增目标</span>
                    </el-row>
                    <el-dialog :title="titleStr" :visible.sync="dialogVisibleAdd" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>开始时间：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-date-picker class="w_ratio" v-model="startDate" value-format="yyyy-MM-dd" type="date" placeholder="请选择开始时间"></el-date-picker>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>结束时间：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-date-picker class="w_ratio" v-model="endDate" value-format="yyyy-MM-dd" type="date" placeholder="请选择结束时间"></el-date-picker>
                            </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div style="line-height: 23px;margin-top: 10px;"><span class="redFont">*</span>客户名称：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="userId" clearable placeholder="请选择客户">
                                            <el-option v-for="item in userList" :key="item.id" :label="item.user_name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div style="line-height: 23px;margin-top: 10px;"><span class="redFont">*</span>目标名称：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="targetName" placeholder="请输入目标名称"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>目标值：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-input v-model="targetValue" placeholder="请输入目标值"></el-input>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div style="line-height: 23px;margin-top: 10px;"><span class="redFont">*</span>目标币种：</div></el-col>
                            <el-col :span="6">
                                <!-- <div>
                                    <el-input v-model="targetCurrency" placeholder="请输入目标币种"></el-input>
                                </div> -->
                                <div>
                                    <template>
                                        <el-select v-model="targetCurrency" clearable placeholder="请选择目标币种">
                                            <el-option v-for="item in targetCurList" :key="item.Id" :label="item.name" :value="item.Id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>目标内容：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-input v-model="targetContent" placeholder="请输入目标内容"></el-input>
                            </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleAdd = false;">取 消</el-button>
                            <el-button type="primary" @click="confirmUpData()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>开始时间</th>
                                <th>结束时间</th>
                                <th>客户名称</th>
                                <th>目标名称</th>
                                <th>目标币种</th>
                                <th>目标值</th>
                                <th>目标内容</th>
                                <th>编辑</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>{{item.start_date}}</td>
                                <td>{{item.end_date}}</td>
                                <td>{{item.user_name}}</td>
                                <td class="overOneLinesHeid" style="width:250px;">
                                    <el-tooltip class="item" effect="light" :content="item.target_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;width:250px;">&nbsp;&nbsp;{{item.target_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td>
                                    <span v-if="item.target_currency===1">美金</span>
                                    <span v-else-if="item.target_currency===2">韩币</span>
                                    <span v-if="item.target_currency===3">人民币</span>
                                </td>
                                <td>{{item.target_value}}</td>
                                <td>{{item.target_content}}</td>
                                <td><span class="notBgButton" @click="clearData();AoTtarget(index)">编辑目标</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
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
      total:0,//默认数据总数 
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //新增目标
      dialogVisibleAdd:false,
      titleStr:'',
      startDate:'',
      endDate:'',
      targetName:'',
      targetValue:'',
      targetCurrency:'',
      targetCurList:[{"name":'美金',"Id":'1'},{"name":"韩币","Id":"2"},{"name":"人民币","Id":"3"}],
      targetContent:'',
      AoTindex:'',
      ID:'',
      userList:[],
      userId:'',
      //搜索
      user_id:'',
      start_date:'',
      end_date:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
    this.getUserList();
  },
  methods:{
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$targetListURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&sale_user_id="+vm.user_id+"&start_date="+vm.start_date+"&end_date="+vm.end_date,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code==1000){
                vm.isShow=false;
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
            }else{
                vm.isShow=true;
                vm.tableData=[];
                vm.total=0;
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
    getUserList(){
        let vm = this;
        axios.get(vm.url+vm.$getSaleUserListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.userList=res.data.data
            // res.data.data.forEach(element => {
            //     vm.userList.push({"name":element.user_name,"id":element.id})
            // });
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    // 
    AoTtarget(index){
        let vm = this;
        vm.AoTindex=index;
        vm.dialogVisibleAdd=true;
        // vm.userList.splice(0);
        // axios.get(vm.url+vm.$getSaleUserListURL,
        //     {
        //         headers:vm.headersStr,
        //     },
        // ).then(function(res){
        //     res.data.data.forEach(element => {
        //         vm.userList.push({"name":element.user_name,"id":element.id})
        //     });
        // }).catch(function (error) {
        //     vm.loading.close();
        //     if(error.response.status!=''&&error.response.status=="401"){
        //     vm.$message('登录过期,请重新登录!');
        //     sessionStorage.setItem("token","");
        //     vm.$router.push('/');
        //     }
        // });
        if(index===-1){//新增 
            vm.titleStr='新增目标';
        }else{//编辑 
            vm.titleStr='编辑目标';
            vm.targetName=vm.tableData[index].target_name;
            vm.targetValue=vm.tableData[index].target_value;
            vm.targetCurrency=''+vm.tableData[index].target_currency+'';
            vm.targetContent=vm.tableData[index].target_content;
            vm.startDate=vm.tableData[index].start_date;
            vm.endDate=vm.tableData[index].end_date;
            vm.userId=vm.tableData[index].sale_user_id;
            vm.ID=vm.tableData[index].id;
        }
    },
    clearData(){
        let vm = this;
        vm.targetName='';
        vm.targetValue='';
        vm.targetCurrency='';
        vm.targetContent='';
        vm.ID='';
    },
    confirmUpData(){
        let vm = this;
        if(vm.startDate===''){
            vm.$message('开始时间不能为空！');
            return false;
        }
        if(vm.targetName===''){
            vm.$message('目标名称不能为空！');
            return false;
        }
        if(vm.targetContent===''){
            vm.$message('目标内容不能为空！');
            return false;
        }
        if(vm.targetValue===''){
            vm.$message('目标值不能为空！');
            return false;
        }
        if(vm.targetCurrency===''){
            vm.$message('目标币种不能为空！');
            return false;
        }
        vm.dialogVisibleAdd=false;
        let url;
        let data;
        if(vm.AoTindex===-1){
            url=vm.$addTargetURL;
            data={
                    "target_name":vm.targetName,
                    "target_content":vm.targetContent,
                    "target_value":vm.targetValue,
                    "target_currency":vm.targetCurrency,
                    "sale_user_id":vm.userId,
                    "start_date":vm.startDate,
                    "end_date":vm.endDate,
                };
        }else{
            url=vm.$editTargetURL
            data={
                    "target_name":vm.targetName,
                    "target_content":vm.targetContent,
                    "target_value":vm.targetValue,
                    "target_currency":vm.targetCurrency,
                    "sale_user_id":vm.userId,
                    "start_date":vm.startDate,
                    "end_date":vm.endDate,
                    "id":vm.ID,
                };
        }
        axios.post(vm.url+url,data,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=='1000'){
                vm.getDataList();
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
