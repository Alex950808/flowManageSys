<template>
  <div class="goodsSeeData_b">
    <!-- 实时分配查看页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="goodsSeeData">
                <el-row>
                    <el-col :span="10" class="title_left">
                        <router-link  to='/realGoods'><span class="back">返回上一级</span></router-link>
                        <span class="date">{{title}}&nbsp&nbsp实际分配预测</span>
                    </el-col>
                    <el-col :span="14" class="title_right">
                        <div style="float: right;position: relative">
                            <input class="inputStyle" placeholder="请输入搜索关键字" type="text" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                            <span class="d_table" >下载该需求表格</span>
                        </div>
                        
                    </el-col>
                </el-row>
                <div class="t_n">
                    <span>商品名称</span>
                <div class="dd" id="dd">
                    <table cellpadding="0" cellspacing="0" border="0" class="t_number">
                        <tbody>
                        <tr  v-for="item in tableData">
                        <td width="30%" class="ellipsis" style="-webkit-box-orient: vertical;">{{item.goods_name}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                </div>
                <div class="t_i">
                    <div class="t_i_h" id="hh">
                        <div class="ee">
                            <table :style="divWidth()" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <!-- <td  width="30%">商品名称</td> -->
                                <td  width="7%">商品代码</td>
                                <td  width="7%">erp编码</td>
                                <td v-for="item in test"  :width='tableWidth(userName.length)+"%"'>{{item}}</td>
                            </tr>
                            </table>
                        </div>
                    </div>
                    <div class="cc" id="cc" @scroll="scrollEvent()">
                        <table :style="divWidth()" cellpadding="0" cellspacing="0" border="0">
                            <tr v-for="item in tableData">
                                <td  width="7%">{{item.spec_sn}}</td>
                                <td  width="7%">{{item.erp_merchant_no}}</td> 
                                <td v-for="user in userName" :width='tableWidth(test.length)+"%"'>
                                    <span v-if="item[user]==null">0</span>
                                    <span v-if="item[user]!=null">{{item[user]}}</span>
                                </td>  
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
export default {
      data() {
      return {
        tableData: [],
        userName:[],
        url: `${this.$baseUrl}`,
        title:'',
        search:'',
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        test:[],
      };
    },
    mounted(){
        // this.getGoodsSeeData()
        this.getdata();
        // this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        dataTransfer(){
            let vm=this;
            var tableDataStr=JSON.parse(sessionStorage.getItem("tableData"));
            tableDataStr.goodsAllot.forEach(element => {
                vm.tableData.push(element);
            });
            var i=0
            tableDataStr.userName.forEach(element=>{
                vm.userName.push(element);
                if(!(i%2 == 0)){
                    var str=element.split(",");
                    element=str[0]
                }
                vm.test.push(element);
                i++;
            })
        },
        //获取页面数据列表
        getGoodsSeeData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.title=vm.$route.query.purchase_sn;
            axios.get(vm.url+vm.$goodsRealAllotURL+"?purchase_sn="+vm.$route.query.purchase_sn+"&real_purchase_sn="+vm.$route.query.real_purchase_sn+"&query_sn="+vm.search+"&page="+vm.page+"&pageSize="+vm.pagesize,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                res.data.data.goodsAllot.forEach(element => {
                    vm.tableData.push(element);
                });
                var i=0
                res.data.data.userName.forEach(element=>{
                    vm.userName.push(element);
                    if(!(i%2 == 0)){
                        var str=element.split(",");
                        element=str[0]
                    }
                    vm.test.push(element);
                    i++;
                })
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        getGoodsSeeDataTwo(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.title=vm.$route.query.purchase_sn;
            axios.get(vm.url+vm.$getGoodsAllotTwoURL+"?purchase_sn="+vm.$route.query.purchase_sn+"&real_purchase_sn="+vm.$route.query.real_purchase_sn+"&keywords="+vm.search+"&page="+vm.page+"&pageSize="+vm.pagesize,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                }
            ).then(function(res){
                vm.loading.close()
                res.data.goodsAllot.forEach(element => {
                    vm.tableData.push(element);
                });
                var i=0
                res.data.userName.forEach(element=>{
                    vm.userName.push(element);
                    if(!(i%2 == 0)){
                        var str=element.split(",");
                        element=str[0]
                    }
                    vm.test.push(element);
                    i++;
                })
            }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        getdata(){
            let vm=this;
            if(vm.$route.query.isSummary){
                vm.getGoodsSeeDataTwo();
            };
            if(vm.$route.query.isDetails){
                vm.dataTransfer();
            }
        },
        //搜索框
        searchFrame(){
            let vm=this;
            vm.getGoodsSeeData();
        },
        //分页
        current_change(currentPage){
            this.currentPage = currentPage;
        },
        handleSizeChange(val) {
            let vm=this;
            vm.pagesize=val
            vm.getGoodsSeeData()
        },
        handleCurrentChange(val) {
            let vm=this;
            vm.page=val
            vm.getGoodsSeeData()
        },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("dd").scrollTop=a;
            document.getElementById("hh").scrollLeft=b;
        },
        tableWidth(e){
            var lengthStr=(100-14)/e;
            return lengthStr;
        },
        divWidth(e){
            let vm=this;
            var widthLength=vm.userName.length*200
            return "width:"+widthLength+"px"
        }
    }
}
</script>


<style scoped lang=less>
@import '../../css/salesModule.less';
.goodsSeeData_b .ellipsis{
    text-overflow: ellipsis;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.dd{height:659px!important; height:659px; overflow-y:hidden;}
.t_i{width:80%; height:auto; float:left; border-right:1px solid #ccc; border-top:1px solid #ccc}
.t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.ee{width:110%!important; width:110%}
/* .t_i_h table{width:2000px;} */
.t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
/* .cc table{width:2000px; } */
.cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>