<template>
  <div class="purchaseList_b">
    <!-- 待分配列表 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <div class="purchaseList bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <el-row class="listTitleStyle">
                            <el-col :span="24">
                                <span><span class="coarseLine MR_ten"></span>待审核需求列表</span>
                                <!-- <input class="inputStyle" placeholder="请输入搜索关键字" type="text" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i> -->
                                <searchBox @searchFrame='searchFrame'></searchBox>
                            </el-col>
                        </el-row>
                        <ul>
                            <li class="w_h_ratio border B_R MT_twenty" v-for="item in tableData">
                                <el-row>
                                <el-col :span="24">
                                    <table class="w_h_ratio lineHeightForty fontCenter">
                                        <thead>
                                            <tr>
                                                <th>需求单号</th>
                                                <th>部门</th>
                                                <th>交付日期</th>
                                                <th>创建时间</th>
                                                <th>sku数量</th>
                                                <th>商品总需求量</th>
                                                <th>可分配数量</th>
                                                <th>状态</th>
                                                <th>查看详情</th>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td>{{item.demand_sn}}</td> 
                                            <td>
                                                <span v-if="item.department==1">批发部</span>
                                                <span v-if="item.department==2">零售部</span>
                                            </td>
                                            <td>{{item.expire_time}}</td>
                                            <td>{{item.create_time}}</td>
                                            <td>{{item.sku_num}}</td>
                                            <td>{{item.goods_num}}</td>
                                            <td>{{item.allot_num}}</td>
                                            <td v-if="item.status==1"><span class="bgButton" @click="pendingPeriod(item.expire_time,item.demand_sn,item.department)">待挂期</span></td>
                                            
                                            <td v-if="item.status==2"><span class="bgButton" @click="toBeAllocated(item.demand_sn)">待分配</span></td>
                                            <td v-if="item.status==3"><span>已分配</span></td>
                                            <td><i class="iconfont notBgButton" @click="viewDetails(item.demand_sn)" title="查看详情">&#xe631;</i></td>
                                        </tr>   
                                    </table>
                                </el-col>
                                </el-row>
                            </li>
                        </ul>
                        <el-dialog title="需求挂期" :visible.sync="dialogVisible" width="600px">
                            <div class="titleName">采购期列表</div>
                            <div style="width:79%;display: inline-block;vertical-align: -19px;">
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
                    </el-col>
                </el-row>
            </div> 
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '@/components/UiAssemblyList/searchBox';
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
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
      }
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getDemandData()
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
        if(vm.$route.query.demand_sn!=undefined){
            vm.search=vm.$route.query.demand_sn;
        }
        axios.get(vm.url+vm.$waitAllotDemandURL+"?page_size="+vm.pagesize+"&page="+vm.page+"&query_sn="+vm.search,
          {
              headers:vm.headersStr,
          }
        ).then(function(res){
          vm.loading.close()
          if(res.data.code==1002){
              vm.isShow=true;
              vm.tableData=[];
              vm.total='';
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
                "department":vm.department,
                "is_modify_status":"1",
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
            vm.$router.push('/distributionPage?demand_sn='+demand_sn+"&isWait=isWait");
        }).catch(function (error) {
            loa.close();
            if(error.response.status!=''&&error.response.status=="401"){
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
          vm.getDemandData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getDemandData()
      },
      searchFrame(e){
          let vm = this;
          vm.search=e;
          vm.page=1;
          vm.getDemandData();
      },
      //点击查看详情
      viewDetails(demand_sn){
          this.$router.push('/puremandManageDetails?demand_sn='+demand_sn+"&isPurchaseList=isPurchaseList");
      },
    }
}
</script>

<style>
.purchaseList_b .el-dialog__body{
    text-align: left;
}
.titleName{
    width: 19%;
    height: 50px;
    display: inline-block;
    line-height: 50px;
    vertical-align: 64px;
}
.el-checkbox+.el-checkbox {
    margin-right: 30px;
    margin-left: 0px;
    line-height: 39px;
}
.el-checkbox {
    margin-right: 30px;
}
</style>

