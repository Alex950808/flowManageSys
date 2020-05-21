<template>
  <div class="saleUserList_b">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="saleUserList">
                        <div class="listTitleStyle">
                            <el-row>
                                <el-col :span="10">
                                    <span><span class="coarseLine MR_ten"></span>销售用户列表</span>
                                    <searchBox @searchFrame='searchFrame'></searchBox>
                                </el-col>
                                <el-col :span="14" style="text-align:right;">
                                    <span class="bgButton" @click="dialogVisible = true">上传销售数据</span>
                                    <el-dialog title="上传销售数据" :visible.sync="dialogVisible" width="800px">
                                        <span class="notBgButton" @click="d_download()" style="position: absolute;top: 232px;right: 57px;">报价计算-销售用户商品数据</span>
                                        <el-row>
                                            <el-col :span="4"><div class="fontCenter"><span class="redFont">*</span>用户ID：</div></el-col>
                                            <el-col :span="6">
                                                <div>
                                                    <template>
                                                        <el-select v-model="userID" placeholder="请选择用户ID">
                                                            <el-option v-for="item in userIDs" :key="item.id" :label="item.userName" :value="item.id"></el-option>
                                                        </el-select>
                                                    </template>
                                                </div>
                                            </el-col>
                                            <el-col :span="4" :offset="1"><span class="redFont">*</span>上传表格：</el-col>
                                            <el-col :span="6">
                                                <div>
                                                    <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                                        <div class="file">
                                                        <img src="../../image/upload.png"/>
                                                        <span v-if="fileName==''">点击上传文件</span>
                                                        <span v-if="fileName!=''">{{fileName}}</span>
                                                        <input class="w_h_ratio" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                                        </div>
                                                    </form>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <span slot="footer" class="dialog-footer">
                                            <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                            <el-button type="primary" @click="dialogVisible = false;determineUpload()">确 定</el-button>
                                        </span>
                                    </el-dialog>
                                    <div class="bgButton" style="margin-left:10px;">
                                        <span @click="dialogVisibleUser = true;addCustomer();cleanData()">新增销售客户</span>
                                    </div>
                                    <el-dialog title="新增销售客户" :visible.sync="dialogVisibleUser" width="800px">
                                        <el-row class="lineHeightForty MB_twenty">
                                            <el-col :span="4"><div class=""><span class="redFont">*</span>用户名称：</div></el-col>
                                            <el-col :span="6">
                                                <el-input v-model="userName" placeholder="请输入用户名称"></el-input>
                                            </el-col>
                                            <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>选择部门：</div></el-col>
                                            <el-col :span="6">
                                                <template>
                                                    <el-select v-model="dptUser" placeholder="请选择部门">
                                                        <el-option v-for="item in dptUsers" :key="item.id" :label="item.userName" :value="item.id"></el-option>
                                                    </el-select>
                                                </template>
                                            </el-col>
                                        </el-row>
                                        <el-row class="lineHeightForty MB_twenty">
                                            <el-col :span="4"><div class=""><span class="redFont">*</span>最低利润：</div></el-col>
                                            <el-col :span="6">
                                                <el-input v-model="min_profit" placeholder="请输入最低利润"></el-input>
                                            </el-col>
                                            <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>销售客户类型：</div></el-col>
                                            <el-col :span="6">
                                                <div>
                                                    <template>
                                                        <el-select v-model="userType" placeholder="请选择客户类型">
                                                            <el-option v-for="item in userTypes" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                                        </el-select>
                                                    </template>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <el-row class="lineHeightForty MB_twenty">
                                            <el-col :span="4"><div class=""><span class="redFont">*</span>支付币种：</div></el-col>
                                            <el-col :span="6">
                                                <div class="">
                                                    <template>
                                                        <el-select v-model="currency" placeholder="请选择支付币种">
                                                            <el-option v-for="item in currencys" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                                        </el-select>
                                                    </template>
                                                </div>
                                            </el-col>
                                            <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>付款周期：</div></el-col>
                                            <el-col :span="6">
                                                <div style="position: relative;">
                                                    <el-input type="number" v-model="paymentCycle" placeholder="请输入付款周期"></el-input><span style="position: absolute;">天</span>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <el-row class="lineHeightForty MB_twenty">
                                            <el-col :span="4"><div class=""><span class="redFont">*</span>客户简称：</div></el-col>
                                            <el-col :span="6">
                                                <div style="position: relative;">
                                                    <el-input v-model="userAbbreviation" placeholder="请输入客户简称"></el-input><span style="position: absolute;width: 120px;color:red;">*大写如(考拉:KL)</span>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <span slot="footer" class="dialog-footer">
                                            <el-button type="info" @click="dialogVisibleUser = false">取 消</el-button>
                                            <el-button type="primary" @click="confirmAdd()">确 定</el-button>
                                        </span>
                                    </el-dialog>
                                </el-col>
                            </el-row>
                        </div>
                        <el-row>
                            <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                <tr>
                                    <th>用户名称</th>
                                    <th>客户代码</th>
                                    <th>部门名称</th>
                                    <th>支付币种</th>
                                    <th>回款周期</th>
                                    <th>最低利润</th>
                                    <th>sku数</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in tableData">
                                    <td>{{item.user_name}}</td>
                                    <td>{{item.group_sn}}</td>
                                    <td>{{item.depart_name}}</td>
                                    <td>{{item.money_cat}}</td>
                                    <td>{{item.payment_cycle}}天</td>
                                    <td>{{item.min_profit}}</td>
                                    <td>{{item.sku_num}}</td>
                                    <td style="cursor: pointer;">
                                        <i class="el-icon-view MR_twenty" @click="SalesGoodsview(item.id)" title="查看销售商品"></i>
                                        <i class="el-icon-edit-outline" @click="editClient(item.id);cleanData()" title="编辑销售客户"></i>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </el-row>
                        <el-dialog title="编辑销售客户" :visible.sync="dialogVisibleEdit" width="800px">
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4"><div class=""><span class="redFont">*</span>部门：</div></el-col>
                                <el-col :span="6">
                                    <div class="">
                                        <template>
                                            <el-select v-model="dpEdit" placeholder="请选择部门">
                                                <el-option v-for="item in department_info" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                            </el-select>
                                        </template>
                                    </div>
                                </el-col>
                                <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>客户名称：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <el-input v-model="user_name" placeholder="请输入客户名称"></el-input>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4"><div class=""><span class="redFont">*</span>最低利润：</div></el-col>
                                <el-col :span="6">
                                    <div class="">
                                        <el-input v-model="minimumProfit" placeholder="请输入最低利润"></el-input>
                                    </div>
                                </el-col>
                                <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>销售客户类型：</div></el-col>
                                <el-col :span="6">
                                    <div>
                                        <template>
                                            <el-select v-model="userType" placeholder="请选择客户类型">
                                                <el-option v-for="item in userTypes" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                            </el-select>
                                        </template>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4"><div class=""><span class="redFont">*</span>支付币种：</div></el-col>
                                <el-col :span="6">
                                    <div class="">
                                        <template>
                                            <el-select v-model="currency" placeholder="请选择支付币种">
                                                <el-option v-for="item in currencys" :key="item.value" :label="item.label" :value="item.id"></el-option>
                                            </el-select>
                                        </template>
                                    </div>
                                </el-col>
                                <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>付款周期：</div></el-col>
                                <el-col :span="6">
                                    <div style="position: relative;">
                                        <el-input type="number" v-model="paymentCycle" placeholder="请输入付款周期"></el-input><span style="position: absolute;">天</span>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4"><div class=""><span class="redFont">*</span>客户简称：</div></el-col>
                                <el-col :span="6">
                                    <div style="position: relative;">
                                        <el-input v-model="userAbbreviation" placeholder="请输入客户简称"></el-input><span style="position: absolute;width: 120px;color:red;">*大写如(考拉:KL)</span>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisibleEdit = false">取 消</el-button>
                                <el-button type="primary" @click="doeditClient()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <div v-if="isShow" style="text-align:center;"><img class="notData" src="../../image/notData.png"/></div>
                        <el-row>
                            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                            </el-pagination>
                        </el-row>
                    </div>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '../UiAssemblyList/searchBox';
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        fontWatermark,
    },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      tableData:[],
      search:'',
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      fileName:'',
      dialogVisible:false,
      dialogVisibleUser:false,
      userIDs:[],
      userID:'',
      dptUsers:[],
      dptUser:'',
      min_profit:'',
      userName:'',
      //编辑
      dialogVisibleEdit:false,
    //   editData:[],
      department_info:[],
      dpEdit:'',
      user_name:'',
      minimumProfit:'',
      userTypes:[{"label":"账期","id":"Z"},{"label":"现结","id":"XY"}],
      userType:'',
      currencys:[{"label":"美金","id":"D"},{"label":"人民币","id":"C"}],
      currency:'',
      paymentCycle:'',
      userAbbreviation:'',
      sale_user_id:'',
      fileName:'',
    }
  },
  mounted(){
      this.getSaleUserData();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
    getSaleUserData(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$saleUserListURL+"?page="+vm.page+"&pageSize="+vm.pagesize+"&keywords="+vm.search,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.saleUserList.data;
            vm.total=res.data.saleUserList.total;
            if(vm.tableData!=''){
                vm.tableData.forEach(element=>{
                    vm.userIDs.push({"id":element.id,"userName":element.user_name});
                })
            }
            if(res.data.saleUserList==[]){
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
    //批量上传选择文件 
    SelectFile(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
        r.readAsDataURL(f);

        // r.onload = function(e) { 
        var res = this.result;
    },
    // 确定批量上传文件
    determineUpload(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        var formDate = new FormData($("#forms")[0]);
        if(vm.userID==''){
            vm.$message('销售人员ID不能为空！');
            return false;
        }
        $.ajax({
        url: vm.url+vm.$upSaleGoodsURL+"?sale_user_id="+vm.userID,
        type: "POST",
        async: false,
        cache: false,
        headers:{
            'Authorization': 'Bearer ' + headersToken,
            'Accept': 'application/vnd.jmsapi.v1+json',
        },
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            if(res.data.code==2024){
                vm.$router.push('/userGoodsListByUp?isUpdataGoods=isUpdataGoods'+'&sale_user_id='+vm.userID);
                sessionStorage.setItem("tableData",JSON.stringify(res));
            }
        }
        });
    },
    //打开新增用户按钮
    addCustomer(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        vm.dptUsers.splice(0);
        axios.get(vm.url+vm.$addCustomerURL,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code==1000){
                res.data.data.department_info.forEach(element=>{
                    vm.dptUsers.push({"id":element.department_id,"userName":element.de_name});
                })
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
    //确认新增用户
    confirmAdd(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        if(vm.dptUser==''){
            vm.$message('用户部门不能为空!');
            return false;
        }
        if(vm.userName==''){
            vm.$message('用户名不能为空!');
            return false;
        }
        if(vm.min_profit==''){
            vm.$message('最低利润不能为空！');
            return false;
        }else if(vm.min_profit<=0){
            vm.$message('最低利润不能小于或等于0！');
            return false;
        }
        if(vm.userType==''){
            vm.$message('销售客户类型不能为空！');
            return false;
        }
        if(vm.currency==''){
            vm.$message('支付币种不能为空！');
            return false;
        }
        if(vm.paymentCycle==''){
            vm.$message('付款周期不能为空！');
            return false;
        }
        if(vm.userAbbreviation==''){
            vm.$message('客户简称不能为空！');
            return false;
        }
        vm.dialogVisibleUser = false;
        axios.post(vm.url+vm.$doAddCustomerURL,
            {
                "depart_id":vm.dptUser,
                "customer_name":vm.userName,
                "min_profit":vm.min_profit,
                "sale_user_cat":vm.userType,
                "money_cat":vm.currency,
                "payment_cycle":vm.paymentCycle,
                "sale_short":vm.userAbbreviation,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.getSaleUserData();
                vm.dialogVisibleUser = false;
                vm.$message(res.data.msg);
            }else{
                vm.$message(res.data.msg);
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
    //打开编辑销售用户
    editClient(id){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        vm.department_info.splice(0);
        vm.sale_user_id=id;
        axios.get(vm.url+vm.$editCustomerURL+"?sale_user_id="+id,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogVisibleEdit=true;
                // vm.editData=res.data.data.sale_user_info;
                vm.dpEdit=res.data.data.sale_user_info[0].depart_id;
                vm.user_name=res.data.data.sale_user_info[0].user_name;
                vm.minimumProfit=res.data.data.sale_user_info[0].min_profit;
                vm.userType=res.data.data.sale_user_info[0].sale_user_cat;
                vm.currency=res.data.data.sale_user_info[0].money_cat;
                vm.paymentCycle=res.data.data.sale_user_info[0].payment_cycle;
                vm.userAbbreviation=res.data.data.sale_user_info[0].sale_short;
                res.data.data.department_info.forEach(element=>{
                    vm.department_info.push({"label":element.de_name,"id":element.department_id});
                })
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
    //确认编辑销售用户
    doeditClient(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        if(vm.dpEdit==''){
            vm.$message('部门不能为空！');
            return false;
        }
        if(vm.user_name==''){
            vm.$message('客户名称不能为空！');
            return false;
        }
        if(vm.minimumProfit==''){
            vm.$message('最低利润不能为空！');
            return false;
        }else if(vm.minimumProfit<=0){
            vm.$message('最低利润不能小于或等于0！');
            return false;
        }
        if(vm.userType==''){
            vm.$message('销售客户类型不能为空！');
            return false;
        }
        if(vm.currency==''){
            vm.$message('支付币种不能为空！');
            return false;
        }
        if(vm.paymentCycle==''){
            vm.$message('付款周期不能为空！');
            return false;
        }
        if(vm.userAbbreviation==''){
            vm.$message('客户简称不能为空！');
            return false;
        }
        vm.dialogVisibleEdit = false;
        axios.post(vm.url+vm.$doEditCustomerURL,
            {
                "depart_id":vm.dpEdit,
                "customer_name":vm.user_name,
                "min_profit":vm.minimumProfit,
                "sale_user_cat":vm.userType,
                "money_cat":vm.currency,
                "payment_cycle":vm.paymentCycle,
                "sale_short":vm.userAbbreviation,
                "sale_user_id":vm.sale_user_id,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            if(res.data.code==1000){
                // vm.dialogVisibleEdit = false;
                vm.$message(res.data.msg);
                vm.getSaleUserData();
            }else{
                vm.$message(res.data.msg);
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
    cleanData(){
        let vm = this;
        vm.dpEdit='';
        vm.user_name='';
        vm.minimumProfit='';
        vm.userType='';
        vm.currency='';
        vm.paymentCycle='';
        vm.userAbbreviation='';
        // vm.sale_user_id='';
    },
    //查看详情
    SalesGoodsview(id){
        this.$router.push('/userGoodsList?sale_user_id='+id+'&isViewDetail=isViewDetail');
    },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getSaleUserData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getSaleUserData()
      },
      //搜索框
      searchFrame(e){
          let vm=this;
          vm.search=e;
          vm.page=1;
          vm.getSaleUserData();
      },
      d_download(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.downloadUrl+'/发货订单.xls');
      }
  }
}
</script>
<style scoped>
@import '../../css/publicCss.css';
</style>

