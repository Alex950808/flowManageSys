<template>
  <div class="addorditSaleAccountUser">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <span class="bgButton" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_twenty"></span>{{titleStr}}</span>
                    </div>
                    <div class="">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="2">
                                <div class="fontWeight"><span class="redFont">*</span>销售客户id：</div>
                            </el-col>
                            <el-col :span="3">
                                <div>
                                    <template>
                                        <el-select v-model="saleUserid" placeholder="请选择销售客户id">
                                        <el-option v-for="item in saleUseridS" :key="item.id" :label="item.label" :value="item.id">
                                        </el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div class="fontWeight"><span class="redFont">*</span>用户名：</div>
                            </el-col>
                            <el-col :span="3"><div><el-input v-model="userName" placeholder="请输入用户名"></el-input></div></el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="4">
                                <div class="fontWeight"><span class="redFont">*</span>请选择对应品牌：</div>
                            </el-col>
                            <el-col :span="24" class="MT_twenty">
                            <div>
                                <template>
                                    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                    <div style="margin: 15px 0;"></div>
                                    <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
                                        <el-checkbox v-for="city in brandInfo" :label="city.id" :key="city.id">{{city.name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </div>
                            </el-col>
                        </el-row>
                        <div class="fontCenter lineHeightSixty">
                            <span class="bgButton" @click="doAddSaleAccount()">确定</span>
                        </div>
                    </div>
                    <notFound v-if="isShow"></notFound>
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
      titleStr:'',
      saleUseridS:[],
      saleUserid:'',
      userName:'',
      checkAll: false,
      checkedCities: [],
      brandInfo: [],
      isIndeterminate: true,
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'}); 
    this.getDataList();
  },
  methods:{
    handleCheckAllChange(val) {
        // this.checkedCities = val ? cityOptions : [];
        let vm = this;
        if(val){
                vm.brandInfo.forEach(element=>{
                    vm.checkedCities.push(element.id);
                }) 
            }else{
                vm.checkedCities.splice(0);
            }
        this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.brandInfo.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.brandInfo.length;
    },
    getDataList(){
        let vm = this;
        vm.tableData=JSON.parse(sessionStorage.getItem('tableData'))
        vm.saleUseridS.splice(0);
        vm.brandInfo.splice(0);
        //Add为新增   Edit为编辑
        if(vm.$route.query.isAdd=='Add'){
            vm.titleStr='新增销售客户账号';
            vm.tableData.sale_user_list.forEach(element => {
                vm.saleUseridS.push({"label":element.user_name,"id":element.id});
            });
            vm.tableData.brand_list.forEach(brandInfo=>{
                vm.brandInfo.push({"name":brandInfo.name,"id":brandInfo.brand_id});
            })
        }else if(vm.$route.query.isEdit=='Edit'){
            vm.titleStr='编辑销售客户账号';
            vm.tableData.sale_user_list.forEach(element => {
                vm.saleUseridS.push({"label":element.user_name,"id":element.id});
            });
            vm.tableData.brand_list.forEach(brandInfo=>{
                vm.brandInfo.push({"name":brandInfo.name,"id":brandInfo.brand_id});
            })
            vm.saleUserid=vm.tableData.sale_account_info.sale_user_id;
            vm.userName=vm.tableData.sale_account_info.user_name;
            vm.checkedCities=vm.tableData.sale_account_info.brand_id;
            
        }
        
    },
    //确认新增/编辑销售客户ID
    doAddSaleAccount(){
        let vm = this;
        if(vm.isEmpty()==false){
            return false;
        }
        let str;
        let postData;
        //Add为新增   Edit为编辑
        if(vm.$route.query.isAdd=='Add'){
            str=vm.$doAddSaleAccountURL;
            postData={"sale_user_id":vm.saleUserid,"user_name":vm.userName,"brand_id":vm.checkedCities};
        }else if(vm.$route.query.isEdit=='Edit'){
            str=vm.$doEditSaleAccounttURL;
            postData={"sale_account_id":vm.$route.query.sale_account_id,"user_name":vm.userName,"sale_user_id":vm.saleUserid,"brand_id":vm.checkedCities};
        }
        axios.post(vm.url+str,
            postData,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.$message(res.data.msg);
                vm.backUpPage();
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
    //返回上一级
    backUpPage(){
        let vm = this;
        vm.$router.push('/saleAccountList');
    },
    //必填字段判空
    isEmpty(){
        let vm = this;
        if(vm.userName==''){
            vm.$message('用户名不能为空!');
            return false;
        }
        if(vm.saleUserid==''){
            vm.$message('销售客户id不能为空!');
            return false;
        }
        if(vm.checkedCities.length==0){
            vm.$message('对应品牌不能为空!');
            return false;
        }
    },
  }
}
</script>

 <style scoped>
.addorditSaleAccountUser .el-checkbox__label {
    display: inline-block;
    padding: 0;
    line-height: 19px;
    font-size: 14px;
    width: 250px;
}
.addorditSaleAccountUser .el-checkbox {
    width: 250px;
    height: 40px;
}
.addorditSaleAccountUser .el-checkbox+.el-checkbox {
    margin-left: 0;
    width: 250px;
}
</style>

