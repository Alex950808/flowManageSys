<template>
  <div class="getVersionList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>版本日志记录列表</span></div></el-col>
                        <el-col :span="3"><div>版本号：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="serialNum" placeholder="请输入版本号"></el-input></div></el-col>
                        <el-col :span="3"><div>版本内容：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="contenT" placeholder="请输入版本内容"></el-input></div></el-col>
                        <el-col :span="4" :offset="4"><span class="bgButton" @click="getDataList()">搜索</span><span class="bgButton ML_twenty" @click="openBox('A')">新增日志列表</span></el-col>
                    </el-row>
                    <el-dialog :title="titleStr" :visible.sync="dialogVisibleAdd" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class="floatLift"><span class="redFont">*</span>服务器版本号：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="serial_num" placeholder="请输入版本号"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="floatLift"><span class="redFont">*</span>前端版本号：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="web_num" placeholder="请输入版本号"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="4"><div class="floatLift"><span class="redFont">*</span>版本内容：</div></el-col>
                            <el-col :span="10">
                                <div>
                                    <el-input type="textarea" :rows="6" v-model="content" placeholder="请输入版本内容"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <div slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleAdd = false">取 消</el-button>
                            <el-button type="primary" @click="confirmUpData()">确 定 </el-button>
                        </div>
                    </el-dialog>
                    <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>创建时间</th>
                                <th>版本号</th>
                                <th>版本内容</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>{{item.create_time}}</td>
                                <td>{{item.serial_num}} </td>
                                <td class="overOneLinesHeid fontLift PR" style="width:800px;">
                                    <span style="margin-right: 30px;">{{item.content}}</span>
                                    <i style="position: absolute;top:0;right:0;" class="blueFont Cursor NotStyleForI" @click="seeDetail(index)">查看</i>
                                </td>
                                <td><span class="bgButton" @click="openBox('E',index,item.log_id)">编辑</span></td>
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
      <div class="Detail" @click="cancel()"></div>
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
      //搜索
      serialNum:'',
      contenT:'',
      //新增及编辑
      content:'',
      serial_num:'',
      dialogVisibleAdd:false,
      titleStr:'',
      state:'',
      log_id:'',
      web_num:'',
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
        axios.post(vm.url+vm.$getVersionListURL,
            {
                "serial_num":vm.serialNum,
                "content":vm.contenT,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.logList.data.length!=0){
                vm.tableData=res.data.logList.data;
                vm.total=res.data.logList.total;
                vm.isShow=false;
            }else if(res.data.logList.data.length==0){
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
    //打开新增弹框
    openBox(state,index,id){
        let vm = this;
        vm.dialogVisibleAdd=true;
        vm.state = state;
        state=='A'?vm.titleStr='新增日志列表':vm.titleStr='编辑日志列表';
        vm.serial_num='';
        vm.content='';
        if(state=='E'){
            vm.serial_num=vm.tableData[index].serial_num;
            vm.content=vm.tableData[index].content;
            vm.log_id=id;
        }
    },
    //确定编辑或者新增
    confirmUpData(){
        let vm = this;
        let url;
        if(vm.serial_num==''){
            vm.$message('版本号不能为空');
            return false;
        }
        if(vm.content==''){
            vm.$message('版本内容不能为空');
            return false;
        }
        vm.dialogVisibleAdd=false;
        vm.state=='A'?url=vm.$addVersionLogURL:url=vm.$editVersionLogURL;
        axios.post(vm.url+url,
            {
                "serial_num":vm.serial_num,
                "content":vm.content,
                "log_id":vm.log_id
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code==2024){
                vm.$message(res.data.msg);
                vm.getDataList();
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //查看详情
    seeDetail(index){
        let vm= this;
        $(".Detail").fadeIn()
        $(".Detail").html(vm.tableData[index].content)
        $('.Detail').css({
            // "display":"inline-block",
            "position": "absolute",
            "top":$(event.target).offset().top-80,
            "left":$(event.target).offset().left-245,
        })
    },
    cancel(){
        $('.Detail').fadeOut()
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


<style>
.Detail{
    width: 223px;
    height: 138px;
    display: none;
    background-color: #000;
    color: #fff;
    overflow: auto;
    padding: 10px;
}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>
