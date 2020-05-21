<template>
  <div class="auditDemandDetails_b">
    <!-- 待审核需求单详情页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="auditDemandDetails">
                <el-row>
                    <el-col :span="10" class="title_left">
                        <router-link  to='/auditDemand'><span class="back" style="cursor: pointer;">返回上一级</span></router-link>
                        <!-- <span class="stage">0001期</span> -->
                        <span class="date">{{title}}</span>
                    </el-col>
                    <el-col :span="14" class="title_right">
                        <div style="float: right;" class="pos">
                            <input type="text" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                            <span class="d_table" @click="d_table()" style="cursor: pointer;">下载该需求表格</span>
                        </div>
                        
                    </el-col>
                </el-row>
                <ul>
                    <li class="userName">
                        <span class="xuanzhong" @click="getUserNameData('',0)">全部<input type="text" name="0" style="display:none"/></span>
                        <span class="weixuanzhong" v-for="(item,index) in userName" @click="getUserNameData(item.id,index+1)">{{item.user_name}}<input type="text" :name="index+1" style="display:none"/></span>
                    </li>
                </ul>
                <div class="t_i">
                    <div class="t_i_h" id="hh">
                        <div class="ee">
                            <table cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th style="width:200px;">商品名称</th>
                                    <th style="width:150px">商品代码</th>
                                    <th style="width:150px">erp编码</th>
                                    <th style="width:150px">商品规格码</th>
                                    <th style="width:150px">商品需求数量</th>
                                    <th style="width:200px">可采数量</th>
                                    <th style="width:150px">热品标记</th>
                                </tr>
                            </thead>
                            </table>
                        </div>
                    </div>
                    <div class="cc" id="cc" @scroll="scrollEvent()">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr v-for="(item,index) in tableData">
                                <td class="ellipsis" style="width:200px;">
                                    <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                    <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                    </el-tooltip>
                                </td>
                                <td style="width:150px">{{item.erp_prd_no}}</td>
                                <td style="width:150px">{{item.erp_merchant_no}}</td>
                                <td style="width:150px">{{item.spec_sn}}</td>
                                <td style="width:150px">{{item.goods_num}}</td>
                                <td style="width:200px" class="cb_three"><input type="text" @change="changingDemandNum(item.spec_sn,item.goods_num,index)" :name="index" :value="item.recover_num"/></td>
                                <td style="width:150px" class="cb_one" v-if="item.is_hot==0"><input type="checkbox" @change="changingDemandHot(item.spec_sn,index)" :name="index" :value="index"/></td>
                                <td style="width:150px" class="cb_one" v-if="item.is_hot==1"><input type="checkbox" @change="changingDemandHot(item.spec_sn,index)" :name="index" :value="index" checked="checked"/></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div  class="examine"><el-button type="info" @click="labelledHeat(2)">驳回审核</el-button><el-button type="primary" @click="confirmShow()">确认审核</el-button></div>
            </div>
        </el-col>
    <div class="confirmPopup_b" style="display: none;">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要审核？
        <div class="confirm"><el-button  @click.once="labelledHeat(3)" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui';
export default {
      data() {
      return {
        url: `${this.$baseUrl}`,
        tableData: [],
        is_hot:'',
        upData:[],
        isEmpty:[],//用于判断可采数量是否为空
        search:'',//用户输入数据
        loading:'',
        title:'',
        userName:[],
        userId:'',
        headersStr:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.gettableData()
    },
    methods: {
        gettableData(){
            let vm=this;
            var tableDataStr=JSON.parse(sessionStorage.getItem("tableData"));
            vm.tableData=tableDataStr.data;
            vm.userName=tableDataStr.sale_user_list;
        },
        //确认弹框否
        determineIsNo(){
            let vm=this;
            vm.show=false;
        },
        //确认弹框是
        confirmShow(){
            let vm=this;
            vm.show=true;
        },
        //根据用户名查找数据
        getUserNameData(id,index){
            let vm=this;
            vm.title=vm.$route.query.purchase_sn;
            vm.userId=id;
            $(".userName input[name="+index+"]").parent().addClass("xuanzhong")
            $(".userName input[name!="+index+"]").parent().addClass("weixuanzhong")
            $(".userName input[name="+index+"]").parent().removeClass("weixuanzhong")
            $(".userName input[name!="+index+"]").parent().removeClass("xuanzhong")
            axios.post(vm.url+vm.$demandDetailURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "demand_sn":vm.$route.query.demand_sn,
                    'query_sn':vm.search,
                    'sale_user_id':id
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.tableData=res.data.data;
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //单个商品可采数量改变
        changingDemandNum(spec_sn,goods_num,index){
            let vm=this;
            var allot_num=$(".cb_three input[name="+index+"]").val();
            // if(parseInt(allot_num)>parseInt(goods_num)){
            //     vm.open();
            //     vm.gettableData();
            //     return false;
            // }
            axios.post(vm.url+vm.$changeGoodsURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "demand_sn":vm.$route.query.demand_sn,
                    "spec_sn":spec_sn,
                    "filed_name":"recover_num",
                    "filed_value":allot_num,
                    "sale_user_id":vm.userId,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //消息弹框
        open() {
            this.$message('可采数不能大于商品需求数量！');
        },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("hh").scrollLeft=b;
        },
        //单个商品是否为热品改变
        changingDemandHot(spec_sn,index){
            let vm=this;
            // var isHot=$(".cb_one input[name="+index+"]").val();
            var isHot=$(".cb_one input[name="+index+"]").prop("checked");
                var is_hot;
                if(isHot){
                    is_hot=1
                }else{
                    is_hot=0
                }
            axios.post(vm.url+vm.$changeGoodsURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "demand_sn":vm.$route.query.demand_sn,
                    "spec_sn":spec_sn,
                    "filed_name":"is_hot",
                    "filed_value":is_hot,
                    "sale_user_id":vm.userId,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //确认审核
        labelledHeat(e){
            let vm=this;
            var i=0;
            vm.tableData.forEach(element=>{
                var aaa=$(".cb_one input[value="+i+"]").prop("checked");
                var is_hot;
                if(aaa){
                    is_hot=1
                }else{
                    is_hot=0
                }
                // var bbb=$(".cb_two input[value="+i+"]").prop("checked");
                // var is_purchase;
                // if(bbb){
                //     is_purchase=1
                // }else{
                //     is_purchase=0;
                // }
                var recover_num=$(".cb_three input[name="+i+"]").val();
                i++;
                vm.isEmpty.push(recover_num)
                vm.upData.push({"purchase_sn":vm.$route.query.purchase_sn,"demand_sn":vm.$route.query.demand_sn,"spec_sn":element.spec_sn,
                "erp_merchant_no":element.erp_merchant_no,"goods_num":element.goods_num,"recover_num":recover_num
                })
                
            })
            var j=1;
            var i;
            for(i=0 ;i<vm.isEmpty.length;i++){
                if(vm.isEmpty[i]==vm.isEmpty[i+1]){
                    j++
                }
            }
            if(j==i){
                this.$message('可采数量都为0则不通过审核，请重新填写！');
                return false;
            }
            axios.post(vm.url+vm.$changeDemandStatusURL,
                {
                    "goods_list":vm.upData,
                    "status":e
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                if(res.data.code==1000){
                    vm.$message('需求审核成功！');
                }
                // vm.$router.push('/auditDemand');
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
         //搜索框
        searchFrame(){
            let vm=this;
            vm.gettableData();
            // vm.BatchData.splice(0)
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downloadDemandListURL+'?demand_sn='+vm.$route.query.demand_sn+'&purchase_sn='+vm.$route.query.purchase_sn+'&token='+headersToken);
        }
    }
}
</script>
<style scoped>
.auditDemandDetails_b .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.xuanzhong{
    background: #00C1DE;
}
.weixuanzhong{
    background-color: #ccc;
}
.auditDemandDetails .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.auditDemandDetails .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.auditDemandDetails .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.auditDemandDetails .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.auditDemandDetails .dd{height:659px!important; height:659px; overflow-y:hidden;}
.auditDemandDetails .t_i{width:100%; height:auto; float:left; border-right:1px solid #ccc;border-left: 1px solid #ccc;border-top:1px solid #ccc}
.auditDemandDetails .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.auditDemandDetails .ee{width:110%!important; width:110%; text-align: center;}
.auditDemandDetails .t_i_h table{width:1530px;}
.auditDemandDetails .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.auditDemandDetails .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.auditDemandDetails .cc table{width:1530px; }
.auditDemandDetails .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
.auditDemandDetails .userName{
    float: left;
}
.auditDemandDetails .userName span{
    display: inline-block;
    width: 50px;
    height: 30px;
    border-radius: 10px;
    /* background: #ccc; */
    color: #fff;
    text-align: center;
    line-height: 30px;
    margin-right: 20px;
    margin-bottom: 10px;
    cursor: pointer;
}
.confirmPopup_b .el-icon-view{
    color: #fff;
}
.auditDemandDetails_b .el-icon-close{
    margin-left: 270px;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>