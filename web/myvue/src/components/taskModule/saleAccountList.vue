<template>
  <div css="saleAccountList">
    <!-- 该页面为普通表格页表格title表格内容均为组件引入 -->
      <el-col :span="24" class="PR Height" style="background-color: #fff;">
          <div class="bgDiv">
            <fontWatermark :zIndex="false"></fontWatermark>
            <el-col :span="22" :offset="1">
              <tableTitle :tableTitle='tableTitle,config' @Add="addSaleAccount"></tableTitle>
              <el-row>
                <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig' @Edit="openEdit"></tableContent>
              </el-row>
              <div class="lineHeightForty"></div>
              <notFound v-if="isShow"></notFound>
            </el-col>
          </div>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '@/components/UiAssemblyList/searchBox'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
    tableTitle,
    tableContent,
    notFound,
    fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableTitle:'销售客户账号列表',
      config:'[{"Add":"新增销售客户账号"},{"download":"false"},{"search":"false"},{"back":"false"}]',
      tableContent:'',
      TCTitle:['创建时间','销售客户','用户名','编辑'],
      tableField:['create_time','sale_user_name','account_name'],//表格字段
      contentConfig:'[{"isShow":"isEditContent"},{"parameter":"id"}]',
      isShow:false,
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
        axios.get(vm.url+vm.$saleAccountListURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
          vm.loading.close();
          if(res.data.code==1000){
            vm.tableContent=JSON.stringify(res.data.data);
            vm.isShow=false;
          }else if(res.data.code==1002){
            vm.isShow=true;
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
    //打开新增销售客户ID弹框
    addSaleAccount(){
      let vm = this;
      let loa=Loading.service({fullscreen: true, text: '拼命加载中....'});
      axios.get(vm.url+vm.$addSaleAccountURL,
        {
            headers:vm.headersStr,
        }
      ).then(function(res){
        loa.close();
        if(res.data.code==1000){
          sessionStorage.setItem('tableData',JSON.stringify(res.data.data));
          vm.$router.push('/addorditSaleAccountUser?isAdd=Add');
        }else{
          vm.$message(res.data.msg);
        }
        
      }).catch(function (error) {
            loa.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //打开编辑框
    openEdit(id){
      let vm = this;
      let loa=Loading.service({fullscreen: true, text: '拼命加载中....'});
      axios.get(vm.url+vm.$editSaleAccountURL+"?sale_account_id="+id,
        {
            headers:vm.headersStr,
        }
      ).then(function(res){
        loa.close();
        if(res.data.code==1000){
          sessionStorage.setItem('tableData',JSON.stringify(res.data.data));
          vm.$router.push('/addorditSaleAccountUser?isEdit=Edit'+"&sale_account_id="+id);
        }else{
          vm.$message(res.data.msg);
        }
      }).catch(function (error) {
        loa.close();
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


