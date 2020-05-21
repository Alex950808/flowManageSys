<template>
  <div class="classifyFieldList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1" style="margin-bottom: 10px;">
                    <div class="tableTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;用户分类列表</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openAddUser('A')">新增用户分类</span>
                    </div>
                    <el-dialog :title="titleStr" :visible.sync="dialogVisibleUp" width="1100px">
                        <el-row class="elRow">
                            <el-col :span="3" :offset="1">
                                <div>分类名称：</div>
                            </el-col>
                            <el-col :span="8">
                                <el-input v-model="classify_name" style="width:87%;" placeholder="请输入分类名称"></el-input>
                            </el-col>
                            <el-col :span="3">
                                <div>备注：</div>
                            </el-col>
                            <el-col :span="8">
                                <el-input v-model="description" style="width:87%;" placeholder="请输入备注"></el-input>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col :span="3" :offset="1">
                                <div>可见店铺：</div>
                            </el-col>
                            <el-col :span="19">
                                <template>
                                    <el-checkbox :indeterminate="isIndeterminateShop" v-model="checkAllShop" @change="handleCheckAllShopChange">全选</el-checkbox>
                                    <div style="margin: 15px 0;"></div>
                                    <el-checkbox-group v-model="checkedCitiesShop" @change="handleCheckedCitiesShopChange" style="height: 200px;overflow: auto;">
                                        <el-checkbox style="width:150px" v-for="city in citiesShop" :label="city.id" :key="city.id">{{city.name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col :span="3" :offset="1">
                                <div>可见字段：</div>
                            </el-col>
                            <el-col :span="19">
                                <template>
                                    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                    <div style="margin: 15px 0;"></div>
                                    <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange" style="height: 200px;overflow: auto;">
                                        <el-checkbox style="width:165px;" v-for="city in cities" :label="city.id" :key="city.id">{{city.name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleUp = false;">取 消</el-button>
                            <el-button type="primary" @click="addClassify()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-row class="border" style="background-color:#ccc;margin-top: 10px;height: 43px;">
                        <el-col :span="3" class="fontCenter" style="border-left: 1px solid #e5e5e5;border-right: 1px solid #e5e5e5;height: 43px;"><span class="MT_ten d_I_B">分类</span></el-col>
                        <el-col :span="6" class="fontCenter" style="border-left: 1px solid #e5e5e5;border-right: 1px solid #e5e5e5;height: 43px;"><span class="MT_ten d_I_B">可见店铺</span></el-col>
                        <el-col :span="13" class="fontCenter" style="border-left: 1px solid #e5e5e5;border-right: 1px solid #e5e5e5;height: 43px;"><span class="MT_ten d_I_B">可见字段</span></el-col>
                    </el-row>
                    <el-row v-for="(item,index) in tableData" :key="item.id"  :class="['border','height'+index]">
                        <el-col :span="3" :class="['spanHeight'+index,'div'+index,'fontCenter']">{{item.classify_name}}</el-col>
                        <el-col :span="6" :class="['spanHeight'+index]" :style="divHeight(index)" style="border-left: 1px solid #e5e5e5;border-right: 1px solid #e5e5e5;">
                            <span class="ML_ten MR_ten MT_ten MB_ten d_I_B" style="width: 150px;" v-for="classifyShop in item.classify_shop" :key="classifyShop.shop_id">{{classifyShop.shop_name}}</span>
                        </el-col>
                        <el-col :span="13" :class="['spanHeight'+index]" style="border-right: 1px solid #e5e5e5;">
                            <span class="ML_ten MR_ten MT_ten MB_ten d_I_B" style="width: 130px;overflow: hidden;height: 21px;" v-for="classifyinfo in item.classify_field" :key="classifyinfo.field_name_cn" :title="classifyinfo.field_name_cn">{{classifyinfo.field_name_cn}}</span>
                        </el-col>
                        <el-col :span="2" :class="['spanHeight'+index,'div'+index,'fontCenter']">
                            <span class="notBgButton" @click="openAddUser('E',item.id,index)">编辑</span>
                        </el-col>
                    </el-row>
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
      //新增/编辑 
      dialogVisibleUp:false,
      checkAll: false,
      checkedCities: [],
      cities: [],
      isIndeterminate: true,
      titleStr:'',

      checkAllShop: false,
      checkedCitiesShop: [],
      citiesShop: [],
      isIndeterminateShop: true,

      description:'',
      classify_name:'',
      ID:'',
      state:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
      //用户可见分类多选 
      handleCheckAllChange(val) {
        let vm = this;
          if(val){
              vm.cities.forEach(element=>{
                  this.checkedCities.push(element.id)
              })
          }else{
              this.checkedCities.splice(0);
          }
        this.isIndeterminate = false;
      },
      handleCheckedCitiesChange(value) {
          let vm = this;
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.cities.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
      },
      //店铺信息多选 
      handleCheckAllShopChange(val) {
        let vm = this;
          if(val){
              vm.citiesShop.forEach(element=>{
                  this.checkedCitiesShop.push(element.id)
              })
          }else{
              this.checkedCitiesShop.splice(0);
          }
        this.isIndeterminate = false;
      },
      handleCheckedCitiesShopChange(value) {
          let vm = this;
        let checkedCountShop = value.length;
        this.checkAllShop = checkedCountShop === this.cities.length;
        this.isIndeterminateShop = checkedCountShop > 0 && checkedCountShop < this.citiesShop.length;
      },
      // 
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$getUserClassifyListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.loading.close();
                vm.isShow=true;
            }else{
                vm.loading.close();
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
    openAddUser(state,id,index){
        let vm = this;
        vm.ID=id;
        vm.state=state;
        vm.classify_name='',
        vm.description='',
        // vm.cities.splice(0); 
        // vm.citiesShop.splice(0);
        vm.checkedCities.splice(0);
        vm.checkedCitiesShop.splice(0);
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        if(state=='A'){//新增
            vm.titleStr="新增用户分类"
        }else if(state=='E'){//编辑 
            vm.titleStr="编辑用户分类"
            vm.classify_name=vm.tableData[index].classify_name;
            vm.description=vm.tableData[index].description;
            vm.tableData[index].classify_field.forEach(element=>{
                vm.checkedCities.push(""+element.field_id+"")
            })
            vm.tableData[index].classify_shop.forEach(element=>{
                vm.checkedCitiesShop.push(""+element.shop_id+"")
            })
            console.log(vm.checkedCities)
        }
        if(vm.cities.length!=0){
            load.close();
            vm.dialogVisibleUp=true;
        }else{
            axios.get(vm.url+vm.$getClassifyFieldURL,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                res.data.data.forEach(element => {
                    vm.cities.push({"name":element.field_name_cn,"id":""+element.id+""});
                });
            })
            axios.get(vm.url+vm.$getClassifyShopURL,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                vm.dialogVisibleUp=true;
                load.close();
                res.data.data.forEach(element=>{
                    vm.citiesShop.push({"name":element.shop_name,"id":""+element.shop_id+""})
                })
            })
        }
        
    },
    //  新增或者编辑用户类型 
    addClassify(){
        let vm = this;
        let classify_filed='';
        vm.checkedCities.forEach(element=>{
            classify_filed+=element+",";
        })
        classify_filed=(classify_filed.slice(classify_filed.length-1)==',')?classify_filed.slice(0,-1):classify_filed;

        let classify_shop='';
        vm.checkedCitiesShop.forEach(element=>{
            classify_shop+=element+",";
        })
        classify_shop=(classify_shop.slice(classify_shop.length-1)==',')?classify_shop.slice(0,-1):classify_shop;
        
        let url;
        if(vm.state=='A'){
            url=vm.$addClassifyURL;
        }else if(vm.state=='E'){
            url=vm.$editClassifyURL;
        }
        axios.post(vm.url+url,
            {
                "classify_name":vm.classify_name,
                "description":vm.description,
                "classify_filed":classify_filed,
                "classify_shop":classify_shop,
                "id":vm.ID,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.dialogVisibleUp=false;
            if(res.data.code==1000){
                vm.getDataList();
                vm.$message(res.data.msg);
            }else{
                vm.$message(res.data.msg);
            }
        })
    },
    divHeight(index){
        let vm = this;
        setTimeout(function(){
            $(".spanHeight"+index).height($(".height"+index+"").height())
            $(".div"+index).css("line-height",$(".height"+index+"").height()+'px')
            vm.loading.close();
        },500)
    }
  },
//   computed:{
//       divHeight(){
//           let vm = this;
//           return function(e){
//               console.log(e);
//           }
//       }
//   }
}
</script>

<style>
.classifyFieldList .el-checkbox{
    margin-top: 10px;
    margin-bottom: 10px;
}
</style>
