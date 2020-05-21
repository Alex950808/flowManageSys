<template>
  <div class="seeData_b">
    <!-- 实时分配查看页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="seeData">
                <el-row>
                    <el-col :span="8" class="title_left">
                        <router-link  to='/distributionForecast'><span class="back">返回上一级</span></router-link>
                        <!-- <span class="stage">0001期</span> -->
                        <span class="date">{{title}}</span>
                    </el-col>
                    <el-col :span="16" class="title_right">
                        <div style="float: right;">
                            <input type="text"/><i class="el-icon-search search"></i>
                            <span class="d_table">下载该需求表格</span>
                        </div>
                    </el-col>
                </el-row>
                <div class="t_n">
                    <span>商品名称</span>
                <div class="dd" id="dd">
                    <table cellpadding="0" cellspacing="0" border="0" class="t_number">
                        <tbody>
                            <!-- <tr>
                                <td  width="30%">商品名称</td> 
                            </tr> -->
                        <tr  v-for="item in tableData">
                        <td width="30%" class="ellipsis">{{item.goods_name}}</td>
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
                                <td v-for="arrUser in test"  :width='tableWidth(test.length)+"%"'>{{arrUser}}</td>
                            </tr>
                            </table>
                        </div>
                    </div>
                    <div class="cc" id="cc" @scroll="scrollEvent()">
                        <table :style="divWidth()" cellpadding="0" cellspacing="0" border="0">
                            <tr v-for="item in tableData">
                                <td  width="7%">{{item.spec_sn}}</td>
                                <td  width="7%">{{item.erp_merchant_no}}</td> 
                                <td v-for="user in userName" :width='tableWidth(test.length)+"%"'>{{item[user]}}</td>
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
export default {
      data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        userName:[],//表头用户名
        test:[],
        title:'',
      };
    },
    mounted(){
        this.dataTransfer();
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
                vm.userName.push(element)
                if(!(i%2 == 0)){
                    var str=element.split(",");
                    element=str[0]
                }
                vm.test.push(element);
                i++;
            })
        },
        //获取页面数据列表
        getSeeData(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            vm.title=vm.$route.query.purchase_sn;
            axios.get(vm.url+vm.$getGoodsAllotURL+"?purchase_sn="+vm.$route.query.purchase_sn+"&real_purchase_sn="+vm.$route.query.real_purchase_sn,
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
                    vm.userName.push(element)
                    if(!(i%2 == 0)){
                        var str=element.split(",");
                        element=str[0]
                    }
                    vm.test.push(element);
                    i++;
                })
            }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
            
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
<style>


/* .table-head{padding-right:17px;color:#000;text-align: center;}
.table-body{width:100%; height:300px;overflow-y:scroll;overflow-x:scroll;}
.table-head table,.table-body table{width:100%;}
.table-body table tr:nth-child(2n+1){background-color:#f2f2f2;}  */
.seeData_b .ellipsis{
    text-overflow: ellipsis;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}


.seeData_b .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.seeData_b .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.seeData_b .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.seeData_b .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.seeData_b .dd{height:659px!important; height:659px; overflow-y:hidden;}
.seeData_b .t_i{width:80%; height:auto; float:left; border-right:1px solid #ccc; border-top:1px solid #ccc}
.seeData_b .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.seeData_b .ee{width:110%!important; width:110%}
/* .t_i_h table{width:2000px;} */
.seeData_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.seeData_b .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
/* .cc table{width:2000px; } */
.seeData_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}

</style>

<style scoped lang=less>
@import '../../css/salesModule.less';
</style>