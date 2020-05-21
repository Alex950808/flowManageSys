<template>
    <div class="taskList_b">
        <el-row class="taskList">
            <el-col :span="24" class="PR Height" style="background-color: #fff;">
                <el-row class="bgDiv">
                    <fontWatermark :zIndex="false"></fontWatermark>
                    <el-col :span="22" :offset="1">
                        <el-row class="title">
                            <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;任务列表</span>
                            <searchBox @searchFrame='searchFrame'></searchBox>
                            <span class="bgButton floatRight MT_twenty MR_twenty" @click="dialogVisibleT = true;">新增模板</span>
                            <el-dialog title="新增模板" :visible.sync="dialogVisibleT" width="500px">
                                <span class="demonstration">模板名称</span>
                                <el-input style="width: 58%;" v-model="templateName" placeholder="请输入模板名称"></el-input>
                                <span slot="footer" class="dialog-footer">
                                    <el-button type="info" @click="dialogVisibleT = false">取 消</el-button>
                                    <el-button type="primary" @click="dialogVisibleT = false;addTaskTemplate()">确 定</el-button>
                                </span>
                            </el-dialog>
                        </el-row>
                        <el-row>
                            <ul>
                                <li v-for="(item,index) in tableData">
                                    <div class="content">
                                        <div class="content_text">
                                            <span class="start">任务模板编号：</span><span>{{item.task_sn}}</span>
                                            <span class="start">模板名称：</span><span>{{item.task_name}}</span>
                                            <span @click="openwindow(item.task_sn)" class="bgButton floatRight MT_ten MR_twenty">新建任务</span>
                                        </div>
                                        <el-row class="content_text">
                                            <el-col :span="12"><span class="start">创建时间：</span><span class="time">{{item.create_time}}</span></el-col>
                                            <el-col :span="12"><span class="end">更新时间：</span><span class="time">{{item.modify_time}}</span></el-col>
                                        </el-row>
                                        <el-row>
                                            <table style="width:100%;text-align:center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                                                <thead>
                                                <tr>
                                                    <th>任务模板编号</th>
                                                    <th>任务日期规格</th>
                                                    <th>任务时刻</th>
                                                    <th>任务内容</th>
                                                    <th>创建时间</th>
                                                    <th>更新时间</th>
                                                    <th>编辑任务</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="itemO in item.task_detail_info">
                                                    <td>{{itemO.task_sn}}</td>
                                                    <td>
                                                        <span v-if="itemO.task_date=='T-3'">提货前三天</span>
                                                        <span v-if="itemO.task_date=='T-2'">提货前两天</span>
                                                        <span v-if="itemO.task_date=='T-1'">提货前一天</span>
                                                        <span v-if="itemO.task_date=='T-0'">提货日</span>
                                                        <span v-if="itemO.task_date=='T+1'">提货日后一天</span>
                                                        <span v-if="itemO.task_date=='T+2'">提货日后两天</span>
                                                        <span v-if="itemO.task_date=='T+3'">提货日后三天</span>
                                                        <span v-if="itemO.task_date=='D-3'">到货日前三天</span>
                                                        <span v-if="itemO.task_date=='D-2'">到货日前两天</span>
                                                        <span v-if="itemO.task_date=='D-1'">到货日前一天</span>
                                                        <span v-if="itemO.task_date=='D-0'">到货日</span>
                                                        <span v-if="itemO.task_date=='D+1'">到货日后一天</span>
                                                        <span v-if="itemO.task_date=='D+2'">到货后两天</span>
                                                        <span v-if="itemO.task_date=='D+3'">到货后三天</span>
                                                    </td>
                                                    <td>{{itemO.task_time}}</td>  
                                                    <td>{{itemO.task_content}}</td>
                                                    <td>{{itemO.create_time}}</td>
                                                    <td>{{itemO.modify_time}}</td>
                                                    <td>
                                                        <i class="iconfont"  @click="editDialogVisible = true;openEditTask(itemO.id,itemO.task_sn,itemO.is_system,itemO.sort_num)" style="cursor:pointer;">&#xe671;</i>
                                                        
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            
                                        </el-row>
                                    </div>
                                </li>
                                <li v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></li>
                                <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                    :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                                </el-pagination>
                            </ul>
                            <el-dialog title="新建任务" :visible.sync="dialogVisible" width="700px">
                                <el-row class="lineHeightSixty">
                                    <el-col :span="12">
                                        <span class="demonstration">任务日期</span>
                                        <template>
                                            <el-select v-model="taskNum" placeholder="请选择">
                                                <el-option v-for="item in options" :key="item.Num" :label="item.label" :value="item.Num">
                                                </el-option>
                                            </el-select>
                                        </template>
                                    </el-col>
                                    <el-col :span="12">
                                        <template>
                                            <span class="demonstration">任务时刻</span>
                                            <el-time-picker style="width: 58%;" v-model="taskTime" placeholder="任意时间点"></el-time-picker> 
                                        </template>
                                    </el-col>
                                </el-row>
                                <el-row class="lineHeightSixty">
                                    <el-col :span="12">
                                        <span class="demonstration">任务内容</span>
                                        <el-input style="width: 66%;" v-model="taskText" placeholder="请输入任务内容"></el-input>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="4">
                                        <span class="demonstration">任务角色</span>
                                    </el-col>
                                    <el-col :span="18">
                                        <template>
                                            <el-checkbox :indeterminate="newisIndeterminatetaskRole" v-model="newcheckAll" @change="newhandleCheckAllChange">全选</el-checkbox>
                                            <div style="margin: 15px 0;"></div>
                                            <el-checkbox-group v-model="newtaskRole" @change="newhandleCheckedCitiesChange;newgetUserByRole()">
                                                <el-checkbox v-for="city in newtaskRoleList" :label="city.id" :key="city.id">{{city.label}}</el-checkbox>
                                            </el-checkbox-group>
                                        </template>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="4">
                                        <span class="demonstration">任务管理员</span>
                                    </el-col>
                                    <el-col :span="18">
                                        <template>
                                            <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                            <div style="margin: 15px 0;"></div>
                                            <el-checkbox-group v-model="newcheckedCities" @change="handleCheckedCitiesChange">
                                                <el-checkbox v-for="city in newcities" :label="city.id" :key="city.id" :value="city.id">{{city.label}}</el-checkbox>
                                            </el-checkbox-group>
                                        </template>
                                    </el-col>
                                </el-row>
                                <span slot="footer" class="dialog-footer">
                                    <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                    <el-button type="primary" @click="dialogVisible = false;addTaskList()">确 定</el-button>
                                </span>
                            </el-dialog>
                            <el-dialog title="编辑任务" style="text-align:left;" :visible.sync="editDialogVisible" width="800px">
                                <el-row style="margin-bottom: 25px;">
                                    <el-col :span="12">
                                        <span class="demonstration">任务日期</span>
                                        <template>
                                            <el-select v-model="editTaskNum" placeholder="请选择">
                                                <el-option v-for="item in options" :key="item.Num" :label="item.label" :value="item.Num">
                                                </el-option>
                                            </el-select>
                                        </template>
                                    </el-col>
                                    <el-col :span="12">
                                        <template>
                                            <span class="demonstration">任务时刻</span>
                                            <el-time-picker style="width: 58%;" v-model="editTaskTime" placeholder="请选择任务时刻"> </el-time-picker>
                                        </template>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="12">
                                        <span class="demonstration">任务内容</span>
                                        <el-input style="width: 58%;" v-if="is_system==0" v-model="editTaskText" placeholder="请输入任务内容"></el-input>
                                        <el-input style="width: 58%;" v-if="is_system==1" v-model="editTaskText" :disabled="true" placeholder="请输入任务内容"></el-input>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="4">
                                        <span class="demonstration">任务角色</span>
                                    </el-col>
                                    <el-col :span="18">
                                        <template>
                                            <el-checkbox :indeterminate="isIndeterminatetaskRole" v-model="checkAlltaskRole" @change="handleCheckAllChangetaskRole">全选</el-checkbox>
                                            <div style="margin: 15px 0;"></div>
                                            <el-checkbox-group v-model="editTaskRole" @change="handleCheckedCitiesChangetaskRole;getUserByRole()">
                                                <el-checkbox v-for="city in taskRoleList" :label="city.id" :key="city.label">{{city.label}}</el-checkbox>
                                            </el-checkbox-group>
                                        </template>
                                    </el-col>
                                </el-row>
                                <el-row>
                                    <el-col :span="4">
                                        <span class="demonstration">任务管理员</span>
                                    </el-col>
                                    <el-col :span="18">
                                        <template>
                                            <el-checkbox :indeterminate="isIndeterminate" v-model="editCheckAll" @change="edithandleCheckAllChange">全选</el-checkbox>
                                            <div style="margin: 15px 0;"></div>
                                            <el-checkbox-group v-model="editCheckedCities" @change="edithandleCheckedCitiesChange">
                                                <el-checkbox v-for="city in editCities" :label="city.id" :key="city.label" :value="city.id">{{city.label}}</el-checkbox>
                                            </el-checkbox-group>
                                        </template>
                                    </el-col>
                                </el-row>
                                <span slot="footer" class="dialog-footer">
                                    <el-button type="info" @click="editDialogVisible = false;cancel()">取 消</el-button>
                                    <el-button type="primary" @click="editDialogVisible = false;confirmShow()">确 定</el-button>
                                </span>
                            </el-dialog>
                        </el-row>
                    </el-col>
                </el-row>
            </el-col>
        </el-row>
        <div class="confirmPopup_b" v-if="show">
            <div class="confirmPopup">
                <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                    &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
                </div>
                请您确认是否要修改？
                <div class="confirm"><el-button @click="determineEditor()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import searchBox from '../UiAssemblyList/searchBox'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        fontWatermark,
    },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      search:'',
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      dialogVisible: false,
      dialogVisibleT:false,
      taskSn:'',
      taskTime:'',
      taskText:'',
      taskNum:'',
      taskName:'',//新建模板名称
      options:[],
      templateName:'',
      isOpen:0,
      newtaskRole:[],//任务角色id
      newtaskRoleList:[],//任务角色列表
      newtaskUserListId:[],
      taskRoleListId:[],
      newtaskRoleListId:[],
      taskRoleList:[],
      checkAlltaskRole:false,
      //多选
      checkAll: false,
      checkedCities: [],
      newcheckAll:false,
      cities: [],
      newcities:[],
      newcheckedCities:[],
      isIndeterminate: true,
      isIndeterminatetaskRole:true,
      newisIndeterminatetaskRole:true,
      //编辑任务
      editDialogVisible:false,
      //   editData:[],
      editTaskNum:'',
      editTaskTime:'18:23:34',
      editTaskText:'',
      editTaskRole:[],
      editCheckedCities:[],
      editCheckAll:[],//全选
      editCities:[],
      id:'',
      is_system:'',
      task_sn:'',
      sort_num:'',
      //确认弹框
      show:false,
      UserData:[],
      newUserData:[],
    }
  },
  mounted(){
    this.getTaskListData()
    this.establishTaskNum()
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
      //新建任务时角色多选
    newhandleCheckAllChange(val) {
    this.newtaskRole = val ? this.newtaskRoleListId : [];
    this.newisIndeterminatetaskRole = false;
    },
    newhandleCheckedCitiesChange(value) {
    let checkedCount = value.length;
    this.newcheckAll = checkedCount === this.newtaskRoleListId.length;
    this.newisIndeterminatetaskRole = checkedCount > 0 && checkedCount < this.newtaskRoleListId.length;
    },
    //新建任务时管理员多选
    handleCheckAllChange(val) {
    this.newcheckedCities = val ? this.newtaskUserListId : [];
    this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
    let checkedCount = value.length;
    this.checkAll = checkedCount === this.newtaskUserListId.length;
    this.isIndeterminate = checkedCount > 0 && checkedCount < this.newtaskUserListId.length;
    },
    //编辑时的多选
    edithandleCheckAllChange(val){
    let vm=this;
    let editCities=[];
    vm.editCities.forEach(element=>{
        editCities.push(element.id)
    })
    this.editCheckedCities = val ? editCities : [];
    this.isIndeterminate = false;
    },
    edithandleCheckedCitiesChange(value){
    let checkedCount = value.length;
    this.editCheckAll = checkedCount === this.editCities.length;
    this.isIndeterminate = checkedCount > 0 && checkedCount < this.editCities.length;
    },
      //编辑时的任务角色多选
     handleCheckAllChangetaskRole(val) {
    this.editTaskRole = val ? this.taskRoleListId : [];
    this.isIndeterminatetaskRole = false;
    },
    handleCheckedCitiesChangetaskRole(value) {
    let checkedCount = value.length;
    this.checkAlltaskRole = checkedCount === this.taskRoleListId.length;
    this.isIndeterminatetaskRole = checkedCount > 0 && checkedCount < this.taskRoleListId.length;
    },
    getTaskListData(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token")
        axios.get(vm.url+vm.$taskListURL+'?start_page='+vm.page+'&page_size='+vm.pagesize+"&query_sn="+vm.search,
          {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
          }
        ).then(function(res){
          vm.loading.close()
          vm.tableData=res.data.data;
          vm.total=res.data.data_num;
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
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getTaskListData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getTaskListData()
      },
      //搜索框
        searchFrame(e){
            let vm=this;
            vm.search=e;
            vm.page=1;
            vm.getTaskListData();
        },
      //创建任务编号数据
      establishTaskNum(){
          let vm=this;
          vm.options.push({"Num":"T-3","label":"提货前三天"},{"Num":"T-2","label":"提货前两天"},{"Num":"T-1","label":"提货前一天"},{"Num":"T-0","label":"提货日"},
                          {"Num":"T+1","label":"提货日后一天"},{"Num":"T+2","label":"提货日后两天"},{"Num":"T+3","label":"提货日后三天"},{"Num":"D-3","label":"到货日前三天"},
                          {"Num":"D-2","label":"到货日前两天"},{"Num":"D-1","label":"到货日前一天"},{"Num":"D-0","label":"到货日"},{"Num":"D+1","label":"到货日后一天"},
                          {"Num":"D+2","label":"到货后两天"},{"Num":"D+3","label":"到货后三天"})
      },
      dateToStr(datetime){  
            var hour = datetime.getHours(); 
            var minutes = datetime.getMinutes(); 
            var second = datetime.getSeconds();
            if(hour <10){
            hour = "0" + hour;
            }
            if(minutes <10){
            minutes = "0" + minutes;
            }
            if(second <10){
            second = "0" + second ;
            }
            var time =hour+":"+minutes+":"+second; //2009-06-12 17:18:05
            return time;
        },
      //新建任务
      addTaskList(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          let idStr='';
          vm.newcheckedCities.forEach(element=>{
              idStr+=element+","
          })
          idStr=(idStr.slice(idStr.length-1)==',')?idStr.slice(0,-1):idStr;
          let newtaskRole='';
          vm.newtaskRole.forEach(element=>{
              newtaskRole+=element+","
          })
          newtaskRole=(newtaskRole.slice(newtaskRole.length-1)==',')?newtaskRole.slice(0,-1):newtaskRole;
          axios.post(vm.url+vm.$addTaskDetailURL,
            {
                "task_date":vm.taskNum,
                "task_time":vm.dateToStr(vm.taskTime),
                "task_content":vm.taskText,
                "task_sn":vm.taskSn,
                "role_id":newtaskRole,
                "user_list":idStr
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              if(res.data.code==1000){
                  vm.$message('新建任务成功!');
                  vm.getTaskListData()
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
      //新建模板
      addTaskTemplate(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          axios.post(vm.url+vm.$addTaskURL,
            {
                "task_name":vm.templateName,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              if(res.data.code==1000){
                vm.$message('新建模板成功!');
                vm.getTaskListData();
                vm.isShow=false;
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
      openwindow(task_sn){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$checkTaskModelURL+"?task_sn="+task_sn,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              if(res.data.code==1000){
                  vm.dialogVisible = true;
                  vm.newUserData=res.data.data;
                  res.data.data.role_list.forEach(element=>{
                      vm.newtaskRoleListId.push(element.id);
                      vm.newtaskRoleList.push({"id":element.id,"label":element.name});
                  })
                   res.data.data.user_list.forEach(element=>{
                       vm.newtaskUserListId.push(element.id);
                      vm.newcities.push({"id":element.id,"label":element.user_name});
                  }) 
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
          vm.taskSn=task_sn;
      },
      openList(index){
          let vm=this;
          if(vm.isOpen==0){
              $("."+index+"").css({
                      "height": "115px",
                      "overflow": "hidden",
              })

              vm.isOpen=1;
              return false;
          }
          if(vm.isOpen==1){
              $("."+index+"").css({
                  "height": "100%",
                  "overflow": "visible",
              })
              vm.isOpen=0;
              return false;
          }
      },
      //通过方法id寻找渠道
        getUserByRole(){
            let vm = this;
            vm.editCheckedCities.splice(0);
            vm.UserData.user_list.forEach(element=>{
                vm.editTaskRole.forEach(elementO=>{
                    if(element.role_id==elementO){
                    vm.editCheckedCities.push(element.id);
                    }
                })
            })
        },
        newgetUserByRole(){
            let vm = this;
            vm.newcheckedCities.splice(0);
            vm.newUserData.user_list.forEach(element=>{
                vm.newtaskRole.forEach(elementO=>{
                    if(element.role_id==elementO){
                    vm.newcheckedCities.push(element.id);
                    }
                })
            })
        },
      //打开编辑任务
        openEditTask(id,task_sn,is_system,sort_num){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.task_sn=task_sn;
            vm.id=id;
            vm.is_system=is_system;
            vm.sort_num=sort_num;
            axios.post(vm.url+vm.$editTaskURL,
                {
                    "id":id,
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.editCities.splice(0);
                vm.taskRoleList.splice(0);
                vm.UserData=res.data.data;
                if(res.data.code==1000){
                    vm.editTaskNum=res.data.data.task_info.task_date;
                    vm.editTaskTime=new Date('2018-09-04 '+res.data.data.task_info.task_time);
                    vm.editTaskText=res.data.data.task_info.task_content;
                    //获取任务角色
                    if(res.data.data.role_list!=0){
                        res.data.data.role_list.forEach(element=>{
                            vm.taskRoleListId.push(element.id);
                            vm.taskRoleList.push({"id":element.id,"label":element.name});
                        })
                    }
                    //获取任务管理员
                    if(res.data.data.user_list.length!=0){
                        res.data.data.user_list.forEach(element=>{
                            vm.editCities.push({"id":element.id,"label":element.user_name});
                        })
                    }
                    //选中之前已选的任务角色
                    if(res.data.data.task_info!=''){
                        res.data.data.task_info.role_id.forEach(element=>{
                            vm.editTaskRole.push(parseInt(element));
                        })
                    }
                    //选中之前已选的管理员
                    if(res.data.data.task_info!=''){
                        res.data.data.task_info.user_list.forEach(element=>{
                            vm.editCheckedCities.push(parseInt(element));
                        })
                    } 
                    
                }
            }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //确定编辑任务
        determineEditor(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.show=false;
            let patternList='';
            vm.editCheckedCities.forEach(element=>{
                patternList+=element+",";
            })
            patternList=(patternList.slice(patternList.length-1)==',')?patternList.slice(0,-1):patternList;

            let editTaskRole='';
            vm.editTaskRole.forEach(element=>{
                editTaskRole+=element+",";
            })
            editTaskRole=(editTaskRole.slice(editTaskRole.length-1)==',')?editTaskRole.slice(0,-1):editTaskRole;
            let editTaskStr=vm.editTaskTime;
            if(vm.editTaskTime.constructor==Date){
                editTaskStr=vm.dateToStr(editTaskStr);
            }
            axios.post(vm.url+vm.$doEditTaskURL,
                {
                    "id":vm.id,
                    "task_date":vm.editTaskNum,
                    "task_time":editTaskStr,
                    "task_content":vm.editTaskText,
                    "role_id":editTaskRole,
                    "user_list":patternList,
                    "is_system":vm.is_system,
                    "sort_num":vm.sort_num,
                    "task_sn":vm.task_sn
                },
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.$message(res.data.msg);
                if(res.data.code==1000){
                    vm.editDialogVisible=false;
                    vm.editTaskNum='';
                    vm.editTaskTime='';
                    vm.editTaskText='';
                    vm.editTaskRole=[];
                    vm.editCheckedCities=[];
                    vm.editCheckAll=[];//全选
                    vm.editCities=[];
                    vm.id='';
                    vm.is_system='';
                    vm.task_sn='';
                    vm.sort_num='';
                    vm.getTaskListData();
                }
            }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //取消编辑
        cancel(){
            let vm=this;
            vm.editTaskNum='';
            vm.editTaskTime='';
            vm.editTaskText='';
            vm.editTaskRole=[];
            vm.editCheckedCities=[];
            vm.editCheckAll=[];//全选
            vm.editCities=[];
            vm.id='';
            vm.is_system='';
            vm.task_sn='';
            vm.sort_num='';
        },
        //确认弹框否
        determineIsNo(){
            let vm=this;
            vm.show=false;
            vm.dialogVisible = false;
        },
        //确认弹框是
        confirmShow(){
            let vm=this;
            vm.show=true;
        },
  }
}
</script>

<style scoped>
.taskList .content_text .el-input {
    width: 88%;
}
.el-dialog__headerbtn{
    display: none;
}
.taskList_b .confirmPopup_b{
    width: 100%;
    height: 100%;
    /* background-color:rgba(0, 0, 0, 0.2); */
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
}
.taskList_b .confirmPopup{
  width: 400px;
  height: 189px;
  /* padding-top: 20px; */
  margin: 375px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  /* margin-top: 500px; */
  z-index: 999;
  background-color: #fff;
  /* border-radius: 20px; */
  /* color: #ccc; */
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.taskList_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.taskList_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.taskList_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE;
    color: #fff; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.taskList_b .confirmTitle{
    display: inline-block;
    width: 400px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.titleText{
    
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.confirmPopup_b .el-icon-view{
    color: #fff;
}
.taskList_b .el-icon-close{
    margin-left: 270px;
}
.el-checkbox+.el-checkbox {
    margin-right: 30px;
    margin-left: 0px;
    line-height: 39px;
}
.el-checkbox {
    margin-right: 30px;
}
</style>
<style scoped lang=less>
@import '../../css/taskModule.less';
</style>