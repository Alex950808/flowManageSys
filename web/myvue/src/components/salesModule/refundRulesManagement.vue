<template>
  <div class="refundRulesManagement">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <tableTitle :tableTitle='tableTitle,config' @Add='addRules'></tableTitle>
                    <el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig' @Edit='openEdit'></tableContent>
                    </el-row>
                    <el-dialog :title="dialogTitle" :visible.sync="dialogVisible" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>销售客户id：</div></el-col>
                            <el-col :span="6">
                            <div>
                            <template>
                                <el-select v-model="saleUserid" placeholder="请选择销售客户id">
                                    <el-option v-for="item in saleUseridS" :key="item.label" :label="item.label" :value="item.id"></el-option>
                                </el-select>
                            </template>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="">交货类别：</div></el-col>
                            <el-col :span="6">
                                <template>
                                    <el-select v-model="delivery_type" placeholder="请选择运输方式">
                                        <el-option v-for="item in delivery_types" :key="item.label" :label="item.label" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>运输方式：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="ship_type" placeholder="请选择运输方式">
                                        <el-option v-for="item in ship_types" :key="item.label" :label="item.label" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="">运输天数：</div></el-col>
                            <el-col :span="6"><div><el-input type="number" v-model="ship_day" placeholder="请输入运输天数"></el-input></div></el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>理货天数：</div></el-col>
                            <el-col :span="6"><div><el-input type="number" v-model="tally_day" placeholder="请输入理货天数"></el-input></div></el-col>
                            <el-col :span="4" :offset="1"><div class="">支付天数：</div></el-col>
                            <el-col :span="6"><div><el-input type="number" v-model="pay_day" placeholder="请输入支付天数"></el-input></div></el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="dialogVisible = false;adAddOrEdit()">确 定</el-button>
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
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
    tableTitle,
    tableContent,
    notFound,
    fontWatermark
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableTitle:'销售客户回款规则列表',
      config:'[{"Add":"新增销售客户回款规则"},{"download":"false"},{"search":"false"},{"back":"false"}]',
      tableContent:'',
      TCTitle:['销售客户','交货类别','运输方式','支付天数','运输天数','理货天数','编辑'],
      tableField:['user_name','delivery_type','ship_type','pay_day','tally_day','ship_day'],//表格字段
      contentConfig:'[{"isShow":"isEditContent"},{"parameter":"id"}]',
      isShow:false,
      //页面数据
      dialogVisible:false,
      saleUseridS:[],
      saleUserid:'',
      dialogTitle:'',
      delivery_types:[{"label":"香港fob交货","id":1},{"label":"保税CIF交货","id":2},{"label":"香港DDP交货","id":3},{"label":"其他","id":4}],
      delivery_type:'',
      ship_types:[{"label":"空运","id":1},{"label":"陆运","id":2},{"label":"海运","id":3}],
      ship_type:'',
      ship_day:'',
      tally_day:'',
      pay_day:'',
      isAddOrEdit:'',
      id:'',
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
        axios.get(vm.url+vm.$refundRulesListURL,
            {
                headers:vm.headersStr,
            },
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
    //打开添加新规则
    addRules(){
        let vm = this;
        vm.isAddOrEdit=1;
        vm.clearData();
        vm.saleUseridS.splice(0);
        axios.get(vm.url+vm.$addRefundRulesURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogTitle='新增销售客户账号';
                vm.dialogVisible=true;
                res.data.data.sale_user_list.forEach(element => {
                    vm.saleUseridS.push({"label":element.user_name,"id":element.id});
                });
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
    //打开编辑规则
    openEdit(id){
        let vm = this;
        vm.isAddOrEdit=2;
        vm.id=id;
        vm.saleUseridS.splice(0);
        axios.get(vm.url+vm.$editRefundRulesURL+"?id="+id,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogTitle='编辑销售客户账号';
                vm.dialogVisible=true;
                res.data.data.sale_user_list.forEach(element => {
                    vm.saleUseridS.push({"label":element.user_name,"id":element.id});
                });
                vm.saleUserid=res.data.data.refund_rule_info.sale_user_id;
                vm.delivery_type=res.data.data.refund_rule_info.delivery_type;
                vm.ship_type=res.data.data.refund_rule_info.ship_type;
                vm.ship_day=res.data.data.refund_rule_info.ship_day;
                vm.tally_day=res.data.data.refund_rule_info.tally_day;
                vm.pay_day=res.data.data.refund_rule_info.pay_day;
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
    //确认添加/编辑规则
    adAddOrEdit(){
        let vm = this;
        if(vm.isEmpty()==false){
            return false;
        }
        let str;
        let postData;
        if(vm.isAddOrEdit==1){//判断用户是新增还是编辑新增的话赋值为1，编辑赋值为2
            str=vm.$doAddRefundRulesURL;
            postData={"sale_user_id":vm.saleUserid,"delivery_type":vm.delivery_type,"ship_type":vm.ship_type,"ship_day":vm.ship_day,"tally_day":vm.tally_day,"pay_day":vm.pay_day};
        }else if(vm.isAddOrEdit==2){
            str=vm.$doEditRefundRulesURL;
            postData={"id":vm.id,"sale_user_id":vm.saleUserid,"delivery_type":vm.delivery_type,"ship_type":vm.ship_type,"ship_day":vm.ship_day,"tally_day":vm.tally_day,"pay_day":vm.pay_day};;
        }
        axios.post(vm.url+str,postData,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogVisible = false;
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
    //判断参数是否为空
    isEmpty(){
        let vm = this;
        if(vm.saleUserid==''){
            vm.$message('销售客户不能为空！');
            return false;
        }
        if(vm.delivery_type==''){
            vm.$message('交货方式不能为空！');
            return false;
        }
        if(vm.ship_type==''){
            vm.$message('运输方式不能为空！');
            return false;
        }
        if(vm.ship_day==''){
            vm.$message('运输天数不能为空！');
            return false;
        }
        if(vm.tally_day==''){
            vm.$message('理货天数不能为空！');
            return false;
        }
        if(vm.pay_day==''){
            vm.$message('支付天数不能为空！');
            return false;
        }
    },
    //关闭时清除数据
    clearData(){
      let vm = this;
      vm.saleUserid='';
      vm.delivery_type='';
      vm.ship_type='';
      vm.ship_day='';
      vm.tally_day='';
      vm.pay_day='';
    }
  }
}
</script>

<style>
</style>
