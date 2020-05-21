<template>
  <div class="profitFormulaList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;折扣种类
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openAddDCBox()">新增折扣种类</span>
                    </div>
                    <table class="fontCenter" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>种类名称</th>
                                <th>种类编号</th>
                                <th>是否参与毛利计算</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableDataTwo">
                                <td>{{item.cat_name}}</td>
                                <td>{{item.cat_code}}</td>
                                <td>
                                    <span v-if="item.is_profit===0">否</span>
                                    <span v-if="item.is_profit===1">是</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <el-dialog title="新增折扣类型种类" :visible.sync="dialogVisibleSetgAddDC" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>折扣种类名称：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="cat_name" placeholder="请输入折扣种类名称"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>折扣种类代码：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="cat_code" placeholder="请输入折扣种类代码"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="5"><div><span class="redFont">*</span>是否参与毛利计算：</div></el-col>
                            <el-col :span="6">
                                <div class="grid-content bg-purple">
                                    <template>
                                        <el-radio-group v-model="radio">
                                            <el-radio :label="0">否</el-radio>
                                            <el-radio :label="1">是</el-radio>
                                        </el-radio-group>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleSetgAddDC = false;">取 消</el-button>
                            <el-button type="primary" @click="doAddDiscountCat()">确 定</el-button>
                        </span>
                    </el-dialog>


                    <div class="tableTitleStyle"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;折扣种类公式列表
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openAddCFBox()">新增种类公式</span>
                    </div>
                    <table class="fontCenter" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>折扣种类名称</th>
                                <th>渠道</th>
                                <th>方式</th>
                                <th>折扣种类公式</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in catFormulaList">
                                <td>{{item.cat_name}}</td>
                                <td>{{item.channels_name}}</td>
                                <td>{{item.method_name}}</td>
                                <td>{{item.param_code_info}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <el-dialog title="新增种类公式" :visible.sync="dialogVisibleSetgAddCF" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>渠道-方式：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="CoM" placeholder="请选择渠道-方式">
                                            <el-option v-for="item in CoMList" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>折扣种类：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="dcInfo" placeholder="请选择折扣类型">
                                            <el-option v-for="item in CFInfo.dc_list" :key="item.cat_code" :label="item.cat_name" :value="item.cat_code"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>折扣种类公式：</div></el-col>
                            <el-col :span="16">
                                <div class="addInput">
                                    <!-- <el-input v-model="cat_name" placeholder="请输入公式名称"></el-input> -->
                                </div>
                            </el-col>
                            <el-col :span="4">
                                <span class="bgButton" @click="addInput()">增加</span>
                                <span class="bgButton" @click="delInput()">删除</span>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleSetgAddCF = false;">取 消</el-button>
                            <el-button type="primary" @click="doAddCatFormula()">确 定</el-button>
                        </span>
                    </el-dialog>
                    

                    <div class="tableTitleStyle"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;毛利公式列表
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="openAddBox()">新增毛利公式</span>
                    </div>
                    <table class="fontCenter MB_twenty" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>公式名称</th>
                                <th>公式编号</th>
                                <th>公式信息</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData">
                                <td>{{item.formula_name}}</td>
                                <td>{{item.formula_sn}}</td>
                                <td>{{item.formula}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <el-dialog title="新增公式" :visible.sync="dialogVisibleSetgAddPF" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>公式名称：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-input v-model="formula_name" placeholder="请输入公式名称"></el-input>
                            </div>
                            </el-col>
                        </el-row>
                        <div>
                            <div class="floatLift"><span class="redFont">*</span>折扣类型：</div>
                            <div class="d_I_B " style="margin-left:80PX;">
                                <template>
                                    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                    <div style="margin: 15px 0;"></div>
                                    <el-checkbox-group v-model="cat" @change="handleCheckedCitiesChange">
                                        <el-checkbox v-for="cat in cat_info" :label="cat.id" :key="cat.cat_name">{{cat.cat_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </div>
                        </div>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleSetgAddPF = false;">取 消</el-button>
                            <el-button type="primary" @click="doAddProfitFormula()">确 定</el-button>
                        </span>
                    </el-dialog>
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
      tableDataTwo:[],
      catFormulaList:[],
      isShow:false,
      dialogVisibleSetgAddPF:false,
      //多选 
      cat_info:[],
      cat:[],
      checkAll: false,
      isIndeterminate: true,
      //新增公式
      formula_name:'',
      //新增折扣类型种类
      dialogVisibleSetgAddDC:false,
      cat_name:'',
      cat_code:'',
      radio:'',
      //新增种类公式 
      dialogVisibleSetgAddCF:false,
      CFInfo:[],
      CoMList:[],
      CoM:'',
      dcInfo:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.getDataListTwo();
    this.getCatFormulaList();
  },
  methods:{
    //选择表头数据  
    handleCheckAllChange(val) {
        // this.checkedCities = val ? this.cat_info : []; 
        let vm = this;
        if(val){
            this.cat_info.forEach(element=>{
                this.cat.push(element.id);
            }) 
        }else{
            this.cat.splice(0);
        }
        this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.cat_info.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cat_info.length;
    },
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$getProfitFormulaListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                vm.isShow=false;
            }else if(res.data.code==1002){
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
    getDataListTwo(){
        let vm = this;
        axios.get(vm.url+vm.$getDiscountCatListURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code=='1000'){
                vm.tableDataTwo=res.data.data;
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
    getCatFormulaList(){
        let vm = this;
        axios.get(vm.url+vm.$getCatFormulaListURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.catFormulaList=res.data.data;
        }).catch(function (error) {
            vm.loading.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    openAddBox(){
        let vm = this;
        vm.dialogVisibleSetgAddPF=true;
        axios.get(vm.url+vm.$getDiscountCatListURL+"?is_profit=1",
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code=='1000'){
                vm.cat_info=res.data.data;
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
    //确认新增公式 
    doAddProfitFormula(){
        let vm = this;
        if(vm.formula_name===''){
            vm.$message('请填写公式名称后提交!');
            return false;
        }
        if(vm.cat.length===0){
            vm.$message('请选择公式要素后提交!');
            return false;
        }
        vm.dialogVisibleSetgAddPF=false;
        let cat_info = '';
        vm.cat.forEach(element=>{
            cat_info+=element+",";
        })
        cat_info=(cat_info.slice(cat_info.length-1)==',')?cat_info.slice(0,-1):cat_info;
        axios.post(vm.url+vm.$doAddProfitFormulaURL,
            {
                "formula_name":vm.formula_name,
                "cat_info":cat_info
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=='1000'){
                vm.getDataList();
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    openAddDCBox(){
        let vm = this;
        vm.dialogVisibleSetgAddDC=true;
        vm.cat_name='';
        vm.cat_code='';
        vm.radio='';
    },
    doAddDiscountCat(){
        let vm = this;
        vm.dialogVisibleSetgAddDC=false;
        axios.post(vm.url+vm.$doAddDiscountCatURL,
            {
                "cat_name":vm.cat_name,
                "cat_code":vm.cat_code,
                "is_profit":vm.radio,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=='1000'){
                vm.getDataListTwo();
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    openAddCFBox(){
        let vm = this;
        vm.CoM='';
        vm.dcInfo='';
        $(".addInput").html('');
        vm.CoMList.splice(0);
        vm.dialogVisibleSetgAddCF=true;
        axios.get(vm.url+vm.$addCatFormulaURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.CFInfo=res.data.data;
            res.data.data.pc_info.forEach(element=>{
                vm.CoMList.push({"name":element.channels_name+"-"+element.method_name,"id":element.id+"-"+element.method_id})
            })
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    doAddCatFormula(){
        let vm = this;
        let selLength = $(".addInput select").length;
        let selStr = '';
        if(selLength>0){
            for(let i = 0;i<selLength;i++){
                selStr += $(".addInput select").eq(i).find("option:selected").val();
            }
        }
        if(vm.CoM===''){
            vm.$message('请填写渠道-方式后提交!');
            return false;
        }
        if(vm.dcInfo===''){
            vm.$message('请填写折扣类型后提交!');
            return false;
        }
        if(selStr===''){
            vm.$message('请填写折扣种类公式后提交!');
            return false;
        }
        vm.dialogVisibleSetgAddCF=false;
        axios.post(vm.url+vm.$doAddCatFormulaURL,
            {
                "param_code_info":selStr,
                "method_id":vm.CoM.split("-")[1],
                "channels_id":vm.CoM.split("-")[0],
                "cat_code":vm.dcInfo,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=="1000"){
                vm.getCatFormulaList();
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    addInput(){
        let vm = this;
        let optionList='';
        vm.CFInfo.pp_info.forEach(element=>{
            if(element.param_code!='/'){
                optionList+='<option value='+element.param_code+'>'+element.param_name+'</option>'
            }else{
                optionList+='<option value=\''+element.param_code+'\'>'+element.param_name+'</option>'
            }
        })
        $(".addInput").append($('<select class="MR_twenty MB_ten" onChange="isChange()" id="testSelect"><option value=""></option>'+optionList+'</select>'));
    },
    delInput(){
        $(".addInput select").eq(-1).remove()
    },
    isChange(){
        let selLength = $(".addInput select").length;
        let selStr = '';
        if(selLength>0){
            for(let i = 0;i<selLength;i++){
                selStr += $(".addInput select").eq(i).find("option:selected").val();
            }
        }
    }
  },
}
</script>

<style>
.el-checkbox+.el-checkbox {
    margin-left: 0px;
    margin-right: 30px;
    line-height: 40px;
}
.el-checkbox{
    margin-right: 30px;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
