<template>
  <div class="demandManagement_b">
    <!-- 提报需求页面 -->
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv"> 
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="demandManagement">
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                            <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>需求列表</span></div></el-col>
                            <el-col :span="2"><div>开始时间</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="start_time" placeholder="请输入开始时间"></el-input></div></el-col>
                            <el-col :span="2"><div>结束时间</div></el-col>
                            <el-col :span="3"><div><el-input v-model="end_time" placeholder="请输入结束时间"></el-input></div></el-col>
                            <el-col :span="2"><div>需求单号</div></el-col>
                            <el-col :span="3"><div><el-input v-model="demand_sn" placeholder="请输入需求单号"></el-input></div></el-col>
                            <el-col :span="2"><div>子单号</div></el-col>
                            <el-col :span="3"><div><el-input v-model="sub_order_sn" placeholder="请输入子单号"></el-input></div></el-col>
                            <el-col :span="2"><span class="bgButton" @click="getDemandData()">点击搜索</span></el-col>
                        </el-row>
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="2" :offset="2"><div>销售客户</div></el-col>
                            <el-col :span="3" >
                                <div>
                                    <template>
                                        <el-select v-model="sale_user_id" clearable placeholder="请选择销售用户">
                                            <el-option v-for="item in options" :key="item.id" :label="item.user_name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="2"><div>商品名称</div></el-col>
                            <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                            <el-col :span="2"><div>商家编码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                            <el-col :span="2"><div>规格码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="spec_sn" placeholder="请输入规格码"></el-input></div></el-col>
                        </el-row>
                        <el-row>
                            <table class="tableTitle">
                                <thead>
                                    <th width="20%">商品名称</th>
                                    <th width="10%">商品规格码</th>
                                    <th width="10%">商家编码</th>
                                    <th width="10%">商品代码</th>
                                    <th width="10%">需求单号</th>
                                    <th width="10%">商品需求总量</th>
                                    <th width="10%">销售折扣</th>
                                </thead>
                            </table>
                        </el-row>
                        <table class="tableTitleTwo">
                            <thead>
                                <th width="20%">商品名称</th>
                                <th width="10%">商品规格码</th>
                                <th width="10%">商家编码</th>
                                <th width="10%">商品代码</th>
                                <th width="10%">需求单号</th>
                                <th width="10%">商品需求总量</th>
                                <th width="10%">销售折扣</th>
                            </thead>
                        </table>
                        <el-row>
                            <div v-for="(item,index) in tableData" :class="['content_text','content_text'+index]">
                                <el-row class="userInformation">
                                    <el-col :span="6"><div>需求单号：{{item.demand_sn}}</div></el-col>
                                    <el-col :span="6"><div>子单单号：{{item.sub_order_sn}}</div></el-col>
                                    <el-col :span="5"><div>用户名：{{item.user_name}}</div></el-col>
                                    <!-- <el-col :span="6">
                                        <div>部门：
                                            <span v-if="item.department==1">批发部</span>
                                            <span v-else-if="item.department==2">零售部</span>
                                            <span v-else></span>
                                        </div>
                                    </el-col> -->
                                    <el-col :span="4"><div>创建时间：{{item.create_time}}</div></el-col>
                                    <!-- <el-col :span="4"><div>标记预采：{{item.mark_desc}}</div></el-col> -->
                                    <el-col :span="3" class="fontRight">
                                        <span class="notBgButton" @click.once="viewDetails(item.demand_sn)">需求单详情</span>
                                        <i class="el-icon-arrow-down notBgButton" @click="pullOrUp(index,$event)"></i>  
                                    </el-col>
                                </el-row>
                                <table style="width:100%;text-align: center">
                                    <tr v-for="good in item.goodsData">
                                        <td width="20%" class="overOneLinesHeid fontLift">
                                            <el-tooltip class="item" effect="light" :content="good.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;">&nbsp;&nbsp;{{good.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td width="10%">{{good.spec_sn}}</td>
                                        <td width="10%">{{good.erp_merchant_no}}</td>
                                        <td width="10%">{{good.erp_prd_no}}</td>
                                        <td width="10%">{{good.demand_sn}}</td>
                                        <td width="10%">{{good.goods_num}}</td>
                                        <td width="10%">{{good.sale_discount}}</td>
                                    </tr>
                                </table>
                            </div>
                        </el-row>
                        <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
                        <el-dialog title="编辑期望到仓日" :visible.sync="dialogVisible" width="800px">
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4"><div class="">请选择期望到仓日：</div></el-col>
                                <el-col :span="6">
                                    <div class="block">
                                        <el-date-picker v-model="arrive_store_time" type="date" placeholder="选择日期"></el-date-picker>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;editArriveStoreTime()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <el-row>
                            <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                                :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                            </el-pagination>
                        </el-row>
                    </div>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';
export default {
    components:{
      fontWatermark,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        headersStr:'',
        total:0,//页数默认为0
        page:1,//分页页数
        pagesize:15,//每页的数据条数
        isShow:false,
        dialogVisible:false,
        time:'',
        arrive_store_time:'',
        demand_sn:'',
        //搜索
        options:[],
        start_time:'',
        end_time:'',
        demand_sn:'',
        sub_order_sn:'',
        sale_user_id:'',
        goods_name:'',
        erp_merchant_no:'',
        spec_sn:'',
      }
    },
    mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getDemandData()
      this.getSaleUser()
      this.tableTitle();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      //获取需求列表数据 
      getDemandData(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        axios.post(vm.url+vm.$demandDemandListURL,
            {
                "page":vm.page,
                "pageSize":vm.pagesize,
                "start_time":vm.start_time,
                "end_time":vm.end_time,
                "demand_sn":vm.demand_sn,
                "sub_order_sn":vm.sub_order_sn,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
          vm.loading.close()
          if(res.data.demandList.total>0){
              vm.isShow=false;
          }else if(res.data.demandList.total==0){
              vm.isShow=true;
          }
          vm.tableData=res.data.demandList.data;
          vm.total=res.data.demandList.total;
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      getSaleUser(){//
        let vm = this;
        axios.get(vm.url+vm.$getSaleUserURL,
            {
              headers:vm.headersStr,  
            }
        ).then(function(res){
            vm.options=res.data.data.saleUser;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
      },
      //点击下拉或者收起表格
      pullOrUp(index,event){
        let vm = this;
        let content_text_height=$(event.target).parent().parent().parent().height();
        if(content_text_height==160){
            $(".content_text"+index+"").css({
                "transition-property:":"height",
                "transition-duration":"5s",
            })
            $(".content_text"+index+"").height('auto');
            $(event.target).addClass("el-icon-arrow-up");
            $(event.target).removeClass("el-icon-arrow-down");
            $(event.target).attr("title","点击收起")
        }else{
            $(".content_text"+index+"").height('160');
            $(event.target).addClass("el-icon-arrow-down");
            $(event.target).removeClass("el-icon-arrow-up");
            $(event.target).attr("title","点击展示更多");
        }
      },
      tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=260;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".demandManagement_b .tableTitleTwo").addClass("addclass");
                $(".demandManagement_b .tableTitleTwo").width($(".demandManagement_b .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".demandManagement_b .tableTitleTwo").removeClass("addclass");
            }
        })
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
      //点击新增需求 
      goDetails(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$newDemPageURL,
            {
                headers:vm.headersStr,
            },
          ).then(function(res){
              sessionStorage.setItem("tableData",JSON.stringify(res.data));
              vm.$router.push('/demandForReporting');
          }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
          
      },
      //去挂期 
      goGuaStage(){
          let vm = this;
          sessionStorage.setItem("web_name",'purchaseDemandList');
          vm.webNameList={"web_name":'需求列表',"to":'purchaseDemandList'};
          vm.$store.commit('webName', vm.webNameList);
          this.$router.push('/purchaseDemandList');

      },
      //点击查看详情
      viewDetails(demand_sn){
          this.$router.push('/demandManageDetails?demand_sn='+demand_sn+"&demand=demand");
      },
      openEditArriveStoreTime(demand_sn,time){
          let vm = this;
          vm.arrive_store_time='';
          if(time!=null){
              vm.arrive_store_time=time;
          }
          vm.time=time;
          vm.demand_sn=demand_sn;
      },
      editArriveStoreTime(){
          let vm = this;
          let headersToken=sessionStorage.getItem("token");
          let arriveStoreTime='';
            if(vm.arrive_store_time!=''){
                if(vm.arrive_store_time.constructor==Date){
                    arriveStoreTime=dateToStr(vm.arrive_store_time);
                }else{
                    arriveStoreTime=vm.arrive_store_time;
                }
            }else{
                vm.$message('期望到仓日不能为空，请填写后提交!');
                return false;
            };
          axios.post(vm.url+vm.$editArriveStoreTimeURL,
            {
                "demand_sn":vm.demand_sn,
                "arrive_store_time":arriveStoreTime,
            },
            {
                headers:vm.headersStr,
            },
          ).then(function(res){
              if(res.data.code==1000){
                  vm.getDemandData();
                  vm.$message(res.data.msg);
              }else{
                  vm.$message(res.data.msg);
              }
          }).catch(function (error) {
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      }
    }
}
</script>

<style>
.demandManagement_b .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.demandManagement_b .content_text{
    width: 100%;
    height: 160px;
    overflow: hidden;
    border: 1px solid #ebeef5;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
}
.demandManagement_b .content_text table{
    width: 100%;
}
.demandManagement_b .content_text table tr{
    height: 50px;
    line-height: 50px;
    line-height: 40px;
}
.demandManagement_b .content_text table table  tr:nth-child(even){
    background:#fafafa;
}
.demandManagement_b .content_text table tr:hover{
    background:#ced7e6;
}
.demandManagement_b .userInformation{
    text-align:center;
    height: 50px;
    line-height: 50px;
    background-color: #f8fafb;
    font-weight: bold;
}
.demandManagement_b .addclass{
    position: fixed;
    top: 94px;
    z-index: 10;
}
.demandManagement_b .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.demandManagement_b .el-icon-arrow-up{
    display: inline-block !important;
}
.demandManagement_b .is-show-close{
    display: inline-block !important;
}
</style>

<style scoped lang=less>
@import '../../css/salesModule.less';
</style>