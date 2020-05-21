<template>
  <div class="addNotice_b">
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <el-col :span="22" :offset="1" style="padding-bottom: 20px;background-color: #ebeef5;margin-top: 20px;">
                    <el-row>
                        <el-col :span="3"  :offset="1">
                            <div class="NoticeTitle" style="margin-top: 25px;">
                                <span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;新增公告:
                            </div>
                        </el-col>
                    </el-row>
                    <el-row style="margin-top: 20px;">
                        <el-col :span="20" :offset="3">
                            <div class="noticeContent">
                                <el-input type="textarea" :rows="4" placeholder="请输入内容" v-model="textarea"></el-input>
                            </div>
                        </el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="3" :offset="20">
                            <div class="addnotice">
                                <span class="bgButton" @click="addNotice()">新增公告</span>
                            </div>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle" style="margin-bottom: 30px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;公告列表
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>公告列表</span></div></el-col>
                        <el-col :span="2"><div>公告编号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="notice_sn" placeholder="请输入公告编号"></el-input></div></el-col>
                        <el-col :span="2"><div>公告内容</div></el-col>
                        <el-col :span="3"><div><el-input v-model="query_sn" placeholder="请输入公告内容"></el-input></div></el-col>
                        <el-col :span="2"><div>账号</div></el-col>
                        <el-col :span="3" >
                            <el-select v-model="user_id" clearable placeholder="请选择账号">
                                <el-option v-for="item in selectData.user_arr" :key="item.value" :label="item.user_name" :value="item.user_id"></el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="2"><div>部门</div></el-col>
                        <el-col :span="3">
                            <el-select v-model="department_id" clearable placeholder="请选择部门">
                                <el-option v-for="item in selectData.department_arr" :key="item.value" :label="item.de_name" :value="item.department_id"></el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="2"><span class="bgButton" @click="getBulletinList()">搜索</span></el-col>
                    </el-row>
                    <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th>公告编号</th>
                            <th>公告部门</th>
                            <th>公告账号</th>
                            <th>公告内容</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in tableData">
                            <td style="width:20%;">{{item.notice_sn}}</td>
                            <td style="width:20%;">{{item.de_name}}</td>
                            <td style="width:20%;">{{item.user_name}}</td>
                            <td class="noticeContent">{{item.notice_content}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <div v-if="isShow" style="text-align:center;line-height:50px;"><img class="notData" src="../../image/notData.png"/></div>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
export default {
  data(){
    return{
      url: `${this.$baseUrl}`,
      noticeDate:'',
      noticeTime:'',
      textarea:'',
      tableData:[],
      selectData:[],
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      //搜索
      query_sn:'',
      notice_sn:'',
      user_id:'',
      department_id:'',
    }
  },
  mounted(){
      this.getBulletinList();
this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
        dateToStr(datetime){ 
            var year = datetime.getFullYear();
            var month = datetime.getMonth()+1;//js从0开始取 
            var date = datetime.getDate(); 
            var hour = datetime.getHours(); 
            var minutes = datetime.getMinutes(); 
            var second = datetime.getSeconds();
            if(month<10){
            month = "0" + month;
            }
            if(date<10){
            date = "0" + date;
            }
            var time = year+"-"+month+"-"+date;
            return time;
        },
        timeToStr(datetime){ 
            var year = datetime.getFullYear();
            var month = datetime.getMonth()+1;//js从0开始取 
            var date = datetime.getDate(); 
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
            var time = hour+":"+minutes+":"+second;
            return time;
        },
      //获取列表
      getBulletinList(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$noticeListURL+'?start_page='+vm.page+'&page_size='+vm.pagesize+"&query_sn="+vm.query_sn+"&notice_sn="+vm.notice_sn
          +"&user_id="+vm.user_id+"&department_id="+vm.department_id,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              vm.loading.close();
              if(res.data.code==1002){
                  vm.isShow=true;
                  vm.tableData=[];
                  vm.selectData=[];
                  vm.total=0;
              }
              if(res.data.code==1000){
                vm.isShow=false;
                vm.tableData=res.data.data.notice_list.data;
                vm.selectData=res.data.data;
                vm.total=res.data.data.notice_list.total;
              }
              
          }).catch(function (error) {
              vm.loading.close();
              if(error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
              }
          });
      },
      addNotice(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
          axios.post(vm.url+vm.$addNoticeURL,
            {
                "notice_content":vm.textarea,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              loa.close();
              vm.$message(res.data.msg);
              if(res.data.code==1000){
                vm.noticeDate='';
                vm.noticeTime='';
                vm.textarea='';
                vm.isShow=false;
                vm.getBulletinList();
              }
                
          }).catch(function (error) {
              loa.close();
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
        vm.getBulletinList();
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getBulletinList();
    },
  }
}
</script>

<style>
/* @import '../../css/common.css'; */
.addNotice_b{
    /* text-align: center; */
    margin-top: 30px;
}
.NoticeTitle{
    font-size: 20px;
    margin-top: 50px;
}
.noticeDate{
    margin-bottom: 18px;
}
.addnotice{
    margin-top: 34px;
}
.addNotice_b .noticeContent{
    width: 100%;
    /* line-height: 23px; */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow-y: hidden;
    text-align: center;
}
</style>
