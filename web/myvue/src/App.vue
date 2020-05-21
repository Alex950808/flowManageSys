<template>
  <div id="app">
    <!-- <img src="./assets/logo.png"> -->
    <router-view/>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'App',
  data(){
    return{
      url: `${this.$baseUrl}`,
      msgList:[],
      dialogArr : [],
      parameter:[],
      Num:'',
      isshow:false,
      headersStr:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.notToken();
    // this.refresh();
  },
  methods:{
    getPurchaseTask(){
      let vm=this;
      axios.get(vm.url+vm.$getPurchaseTaskURL,
        {
            headers:vm.headersStr
        }
      ).then(function(res){
        if(res.data.code==1000){
          vm.msgList=res.data.data;
          var i=0;
          vm.msgList.task_info.forEach(element=>{
            i++;
           vm.parameter.push({"id":element.id})
              const h = vm.$createElement;
                vm.dialogArr.push(vm.$notify({
                    title: '标题名称',
                    message: h('p', null, [
                        h('span', null, '亲爱的'+res.data.data.user_info+'您有一个'+element.task_content+'的任务请您确认！ '),
                        h('button', {
                            on:{
                                click:vm.isOk
                            },
                        }, "是"),
                        h('button', {
                            on:{
                                click:vm.isno
                            },
                        }, "否")
                    ]),
                    position: 'bottom-right',
                    duration: 0
                }));
          });
          vm.Num=i;
        }
       
      }).catch(function (error) {
              if(error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
              }
          });
    },
    
    isno(){
      let vm=this;
      //  for(var i = 0; i < this.dialogArr.length; i++){
            vm.dialogArr[vm.Num].close();
        // }
    },
    isOk(event){
      let vm=this;
      let notId='';
      notId=vm.parameter[vm.Num].id;
      axios.post(vm.url+vm.$changeTaskStatusURL,
        {
          "id":notId,
          "status":1
        },
        {
            headers:vm.headersStr
        }
      ).then(function(res){
        isno()
      }).catch(function (error) {
              if(error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
              }
          });
    },
    notToken(){
      let vm = this;
      let token=sessionStorage.getItem("token");
      if(token==null){
        vm.$router.push('/');
      }
    },
    // refresh(){
    //   let vm = this;
    //   window.addEventListener('beforeunload', e => {
    //       sessionStorage.setItem('webNameList', vm.$store.state.webNameList);
    //   });
    // }
  }
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
