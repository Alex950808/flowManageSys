<template>
  <div class="alreadyAllotDemand_b">
    <!-- 提报需求页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="alreadyAllotDemand">
                        <el-row class="purchaseTitle listTitleStyle">
                            <el-col :span="24">
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;已审核需求列表</span><input class="inputStyle"  placeholder="请输入搜索关键字" type="text" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                            </el-col>
                        </el-row>
                        <ul>
                            <li class="purchaseContent" v-for="item in tableData">
                                <el-row>
                                <el-col :span="24">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>需求单号</th>
                                                <th>部门</th>
                                                <th>采购截止日期</th>
                                                <th>创建日期</th>
                                                <th>sku数量</th>
                                                <th>商品总需求量</th>
                                                <th>状态</th>
                                                <th>查看详情</th>
                                                <th>编辑</th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>{{item.demand_sn}}</td>
                                            <td v-if="item.department==1">批发部</td>
                                            <td v-if="item.department==2">零售部</td>
                                            <td>{{item.expire_time}}</td>
                                            <td>{{item.create_time}}</td>
                                            <td>{{item.sku_num}}</td>
                                            <td>{{item.goods_num}}</td>
                                            <td v-if="item.status==3"><span class="notBgButton" @click="Allocated(item.demand_sn)">已分配</span></td>
                                            <td><i class="iconfont notBgButton" @click="viewDetails(item.demand_sn)" title="查看详情">&#xe631;</i></td>
                                            <td>
                                                <span @click="pendingPeriod(item.expire_time,item.demand_sn,item.department)" class="notBgButton">调整挂期</span>
                                                <span @click="toBeAllocated(item.demand_sn)" class="notBgButton">调整分配</span>
                                            </td>
                                        </tr>   
                                    </table>
                                </el-col>
                                </el-row>
                            </li>
                        </ul>
                        <el-dialog title="需求挂期" :visible.sync="dialogVisible" width="600px">
                            <div class="titleName">采购期列表</div>
                            <div style="width:79%;display: inline-block">
                                <template>
                                    <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                    <div style="margin: 15px 0;"></div>
                                    <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange">
                                        <el-checkbox v-for="city in cities" :label="city.value" :key="city.value">{{city.label}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </div>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;confirmPendingPeriod()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        search:'',//用户输入数据
        total:0,//页数默认为0
        page:1,//分页页数
        pagesize:15,//每页的数据条数
        isShow:false,
        dialogVisible: false,
        //多选内容
        checkAll: false,
        checkedCities: [],//选中的数据
        cities: [],//所有要选择的选项
        isIndeterminate: true,
        cityOptions:[],
        //参数
        demand_sn:'',
        department:'',
        headersStr:'',
        dialogVisible: false,
      }
    },
    mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getDemandData();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      handleCheckAllChange(val) {
        let vm = this;
        if(val){
            this.cities.forEach(element=>{
                this.checkedCities.push(element.value);
            }) 
        }else{
            this.checkedCities.splice(0);
        }
        this.isIndeterminate = false;
      },
      handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.cities.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
      },
      //获取需求列表数据
      getDemandData(){
        let vm = this;
        axios.get(vm.url+vm.$alreadyAllotDemandURL+"?query_sn="+vm.search+"&page="+vm.page+"&page_size="+vm.pagesize,
          {
              headers:vm.headersStr,
          }
        ).then(function(res){
          vm.loading.close()
          if(res.data.code==1002){
              vm.isShow=true;
              vm.tableData=[];
              vm.total=0;
          }
          if(res.data.code==1000){
              vm.tableData=res.data.data.data;
              vm.total=res.data.data.total;
              vm.isShow=false;
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
      //进入已分配列表
      Allocated(demand_sn){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        axios.post(vm.url+vm.$demandAlreadyDetailURL,
            {
                "demand_sn":demand_sn
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            sessionStorage.setItem("tableData",JSON.stringify(res.data.data));
            vm.$router.push('/allocatedPage?demand_sn='+demand_sn);
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
      },
      //点击查看详情
      viewDetails(demand_sn){
          this.$router.push('/puremandManageDetails?demand_sn='+demand_sn+"&isAleradyAll=isAleradyAll");
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getDemandData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getDemandData()
      },
      searchFrame(){
          let vm = this;
          vm.page='1';
          vm.getDemandData();
      },
      //打开待挂期
      pendingPeriod(expire_time,demand_sn,department){
        let vm = this;
        vm.cities.splice(0);
        this.checkedCities.splice(0);
        vm.demand_sn=demand_sn;
        vm.department=department;
        axios.post(vm.url+vm.$demandAttachURL,
            {
                "expire_time":expire_time,
                "demand_sn":demand_sn,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogVisible = true;
                res.data.data.forEach(element => {
                    vm.cities.push({"value":element.purchase_sn,"label":element.delivery_team})
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
      //确定挂期
      confirmPendingPeriod(){
        let vm = this;
        let purchase_date_info='';
        vm.checkedCities.forEach(element=>{
            purchase_date_info+=element+",";
        })
        purchase_date_info=(purchase_date_info.slice(purchase_date_info.length-1)==',')?purchase_date_info.slice(0,-1):purchase_date_info;
        axios.post(vm.url+vm.$doDemandAttachURL,
            {
                "demand_sn":vm.demand_sn,
                "purchase_date_info":purchase_date_info,
                "department":vm.department
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogVisible = false;
                vm.$message(res.data.msg);
                vm.getDemandData();
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
      //进入待分配列表
      toBeAllocated(demand_sn){
        let vm = this;
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        axios.post(vm.url+vm.$demandAllotURL,
            {
                "demand_sn":demand_sn
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            loa.close();
            sessionStorage.setItem("tableData",JSON.stringify(res.data.data));
            vm.$router.push('/distributionPage?demand_sn='+demand_sn+"&isAdjustment=isAdjustment");
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

<style>
.alreadyAllotDemand_b .purchaseTitle{
    width: 100%;
    height: 75px;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    margin-top: 20px;
    font-weight: bold;
    position: relative;
}
.alreadyAllotDemand_b .purchaseTitle input{
    width: 200px;
    height: 20px;
    outline: none;
    -webkit-appearance: none;
    border-radius: 50px;
    -moz-border-radius: 50px;
    -webkit-border-radius: 50px;
    padding-left: 30px;
    margin-left: 16px;
}
.alreadyAllotDemand_b .purchaseTitle .search{
    position: absolute;
    left: 175px;
    top: 29px;
}
.alreadyAllotDemand_b .purchaseContent{
    width: 100%;
    height: 100%;
    border:1px solid #ebeef5;
    border-radius: 10px;
    margin-bottom: 15px;
}
.alreadyAllotDemand_b .purchaseContent table{
    width:100%;
    height:100%;
    line-height: 50px;
    text-align: center;
}
.alreadyAllotDemand_b .status{
    display: inline-block;
    width: 80px;
    height: 30px;
    line-height: 30px;
    background-color: #409EFF;
    border-radius: 10px;
    color:#fff;
    cursor: pointer;
}
.alreadyAllotDemand_b .el-dialog__body{
    text-align: left;
}
.titleName{
    width: 19%;
    height: 50px;
    display: inline-block;
    line-height: 50px;
    vertical-align: 64px;
}

</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
