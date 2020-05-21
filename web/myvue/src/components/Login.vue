<template>
  <div class="login" :style ="note">
    <div class="fontLift">
      <input type="text" style="width: 98%;height: 98%;opacity: 0;position: absolute;top: 0;left: 0;">
      <img class="MT_twenty PL_T_H" src="@/image/index_logo.png"/>
    </div>
    <input type="text" style="width:98%;height:88%;opacity: 0;">
    <div class="login_content">
      <input type="text" style="width: 98%;height: 100%;opacity: 0;position: absolute;top: 0;left: 0;">
      <div class="login_title">
        <img src="@/image/logo.png">
      </div>
      <div class="text">
        <el-row>
          <el-col :span="3" :offset="1">
            <div class="L_B_G text-icon" style="height:52px;">
              <img class="d_I_B PT_ten" src="@/image/ID-icon.png">
            </div>
          </el-col>
          <el-col :span="19">
            <div class="">
              <input type="text" placeholder="请填写账号" v-model="userName"/>
            </div>
          </el-col>
        </el-row>
        <el-row class="lineHeightForty">

        </el-row>
        <el-row>
          <el-col :span="3" :offset="1">
            <div class="L_B_G text-icon" style="height:52px;">
              <img class="d_I_B PT_ten" src="@/image/pas-icon.png">
            </div>
          </el-col>
          <el-col :span="19">
            <div class="">
              <input class="password" type="password" placeholder="请输入密码" v-model="passWord"/>
            </div>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="22" :offset="1">
            <span class="signIn Cursor" @click="updata()" v-loading="loading">登&nbsp;&nbsp;录</span>
          </el-col>
        </el-row>
      </div>
    </div>
    
    <div class="Bottom">
      <span>海代网技术研发中心</span>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import router from '../router'
import headr from './headr'
export default {
  components:{
    headr
  },
  data () {
    return {
      title: '海代网集团内部信息管理系统',
      noticeData:[],
      userName:"",
      passWord:"",
      url: `${this.$baseUrl}`,
      loading:false,
      //消息弹框
      msgList:[],
      dialogArr : [],
      parameter:[],
      notStrs:[],
      headersStr:'',
       note: {
            backgroundImage: "url(" + require("../image/bg.jpg") + ")",
          },
    }
  },
  mounted(){
    // this.noticeText();
    // this.Initialization();
    this.CtrlSignIn();
  },
  // created() {
  //   var lett = this;
  //   document.onkeydown = function(e) {
  //   var key = window.event.keyCode;
  //   if (key == 13) {
  //     lett.updata();
  //     }
  //   }
  // },
  methods:{
    //获取通知内容 
    // noticeText(){   
    //       let vm=this;
    //       axios.get(vm.url+vm.$noticeListURL+"?query_sn=index",
    //         {
    //             headers:{
    //                 'Accept': 'application/vnd.jmsapi.v1+json',
    //             }
    //         }
    //       ).then(function(res){
    //           if(res.data.code){
    //             vm.noticeData=res.data.data.notice_list;
    //           }
    //       }).catch(function (error) {
    //           if(error.response.status=="401"){
    //           vm.$message('登录过期,请重新登录!');
    //           sessionStorage.setItem("token","");
    //           vm.$router.push('/');
    //           }
    //       });
    // },
    // //初始化页面高度 89861我的
    // Initialization(){
    //   let windowHeight=$(window).height(); 
    //   $('.login').height(windowHeight).css("background-color","#092c46");
    // },
    updata(){
      let vm=this;
      if(vm.userName==''){
        vm.$message("请输入用户名!");
        return false;
      }else if(vm.passWord==''){
        vm.$message("请输入密码!");
        return false;
      }
      let updata={
        "user_name":vm.userName,
        "password":vm.passWord,
      }
      vm.loading = true,
      axios.post(vm.url+"user/login",updata,{timeout: 5000})
          .then(function (res) {
            vm.loading=false;
            sessionStorage.setItem("navBar",JSON.stringify(res.data.user_permission_info));
          if(res.data.code=='1000'){ 
            sessionStorage.setItem("token",res.data.token);
            sessionStorage.setItem("userName",JSON.stringify(res.data.user_info));
            sessionStorage.setItem("versionInfo",JSON.stringify(res.data.sys_version_info))
            if(localStorage .getItem("versionNum")!=res.data.sys_version_info.web_num){
              // vm.$alert(res.data.sys_version_info.content, '版本更新提示', {
              //   confirmButtonText: '确定',
              //   callback: action => {
              //     localStorage .setItem("versionNum",res.data.sys_version_info.web_num);
              //     window.location.reload(true);
              //   }
              // });
              vm.$alert('<pre>'+res.data.sys_version_info.content+'</pre>', '版本更新提示', {
                dangerouslyUseHTMLString: true,
                confirmButtonText: '确定',
                callback: action => {
                  localStorage .setItem("versionNum",res.data.sys_version_info.web_num);
                  window.location.reload(true);
                }
              });
            }else{
              localStorage .setItem("versionNum",res.data.sys_version_info.web_num);
              router.push({path:'/indexPage'});
            }
            vm.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
            sessionStorage.setItem("web_name",'indexPage');
          }else{
            vm.$message(res.data.msg);
          }
      }).catch(function (error) {
          vm.loading = false;
      });
    },
    getPurchaseTask(){
      let vm=this;
      axios.get(vm.url+vm.$getPurchaseTaskURL,
        {
            headers:vm.headersStr,
        }
      ).then(function(res){
        if(res.data.code==1000){
          vm.msgList=res.data.data;
          var i=0;
          const j = vm.$createElement;
          vm.dialogArr.push(vm.$notify({
              title: '提示',
              dangerouslyUseHTMLString: true,
              message: j('p',  null, [
                  j('span', { style: "display:block;color:red"}, '请您确认是否要全部关闭'),
                  j('button', {
                      on:{
                          click:vm.allClose
                      },
                  }, "是"),
              ]),
              position: 'bottom-right',
              duration: 0
          }));
          vm.msgList.task_info.forEach(element=>{
              let notStr='您有一个'+element.task_content+'的任务请您确认! \n ';
              const h = vm.$createElement;
              var warn = setInterval(() => {
                vm.dialogArr.push(vm.$notify({
                    title: '任务提醒',
                    dangerouslyUseHTMLString: true,
                    message: h('p',  null, [
                        h('span', { style: "display:block",class:"ceshi"+element.id}, notStr),
                        h('button', {
                            on:{
                                click:vm.isOk
                            },
                        }, "是"),
                        h('button', {
                            on:{
                                click:vm.isno
                            },
                        }, "否"),
                    ]),
                    position: 'bottom-right',
                    duration: 0
                }));
                if(i<vm.notStrs.length-1){
                  i++;
                }
                else{
                  clearInterval(warn);
                }
              },10);
              
          });
        }
       
      }).catch(function (error) {
              if(error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
              }
          });
    },
    isno(event){
      let vm=this;
        var str=$(event.path[1]).parent().parent().parent();
        str.css("display","none");
    },
    allClose(){
      let vm=this;
       for(var i = 0; i < vm.dialogArr.length; i++){
            vm.dialogArr[i].close();
        }
    },
    isOk(event){
      let vm=this;
      var str=$(event.path[1]).html();
      str=str.split("ceshi");
      str=str[1].split("\"");
      axios.post(vm.url+vm.$changeTaskStatusURL,
        {
          "id":str[0],
          "status":1
        },
        {
            headers:vm.headersStr
        }
      ).then(function(res){
        vm.$message(res.data.msg);
        var str=$(event.path[1]).parent().parent().parent();
        str.css("display","none");
      }).catch(function (error) {
              if(error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
              }
          });
    },
    getIndexPageData(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$purchaseTaskListURL+'?start_page='+vm.page+'&page_size='+vm.pagesize+"&query_sn="+vm.search,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.tableData=res.data.data;
            vm.total=res.data.data_num;
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    CtrlSignIn(){
      var lett = this;
      $(".login").bind("keydown",function(e){
              // 兼容FF和IE和Opera    
          var theEvent = e || window.event;    
          var code = theEvent.keyCode || theEvent.which || theEvent.charCode;    
          if (code == 13) {
               lett.updata();
          }
      });
    }
  }
};
</script>

<style>

.el-message-box__headerbtn .el-message-box__close {
    display: none;
}
.el-message-box__headerbtn {
    display: none;
}
</style>
<style scoped lang=less>
@import '../css/demo.less';
h1, h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  /* display: inline-block; */
  margin: 0 10px;
}
.el-notification{
  margin-bottom: 30px;
}
/* .rightLogin{
  
} */
.loginText{
  text-align: center;
}
.Bottom span{
    position: absolute;
    bottom: 40px;
    left: 0;
    right: 0;
    margin: auto;
    color: #fff;
    font-family:"Microsoft YaHei";
}
</style>
