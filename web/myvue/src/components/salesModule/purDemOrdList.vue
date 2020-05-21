<template>
  <div class="purDemOrdList_b">
      <el-col :span="24" style="background-color: #fff;">
          <el-row>
            <el-col :span="22" :offset="1">
                <div class="purDemOrdList">
                    <div class="title">
                        <router-link  to='/PurOrderList'><span class="bgButton">返回上一级</span></router-link>
                        <span class="upTitle"><span class="coarseLine MR_twenty"></span>采购单详情</span>
                    </div>
                    <div class="t_i">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th width="20%">需求单编号</th>
                                            <th width="20%">部门</th>
                                            <th width="20%">创建时间</th>
                                            <th width="20%">查看详情</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc" id="cc" @scroll="scrollEvent()">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr v-for="(item,index) in tableData">
                                    <td width="20%">{{item.demand_sn}}</td>
                                    <td width="20%">{{item.department}}</td>
                                    <td width="20%">{{item.create_time}}</td>
                                    <td width="20%"><i class="el-icon-view notBgButton" @click="viewDetails(item.demand_sn)"></i></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <notFound v-if="isShow"></notFound>
                </div>
            </el-col>
          </el-row>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { switchLoading,switchoRiginally } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import notFound from '@/components/UiAssemblyList/notFound';
export default {
    components:{
        notFound,
    },
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
      isShow:false,
    }
  },
  mounted(){
    this.getUpPageData()
  },
  methods:{
      getUpPageData(){
          let vm=this;
          vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
          if(vm.tableData.length==0){
              vm.isShow=true;
          }
      },
       //点击查看详情
      viewDetails(demand_sn){
          let vm=this;
          let switchoEvent=event;
          switchLoading(event);
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$getPurDemOrdInfoURL+"?demand_sn="+demand_sn+"&purchase_sn="+vm.$route.query.purchase_sn,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              switchoRiginally(switchoEvent)
              sessionStorage.setItem("tableDataT",JSON.stringify(res.data.purDemOrdList));
              vm.$router.push('/purDemOrdInfo?demand_sn='+demand_sn+'&purchase_sn='+vm.$route.query.purchase_sn);
          }).catch(function (error) {
                switchoRiginally(switchoEvent)
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            // document.getElementById("dd").scrollTop=a;
            document.getElementById("hh").scrollLeft=b;
        },
  }
}
</script>

<style>
.purDemOrdList_b .title{
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    /* font-weight:bold; */
    padding-left: 35px;
    margin-top: 20px;
}
.purDemOrdList_b .back{
    display: inline-block;
    width: 130px;
    height: 50px;
    background-color: #4677c4;
    color: #fff;
    line-height: 50px;
    text-align: center;
    border-radius:10px; 
    cursor: pointer;
}
.purDemOrdList_b .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
.purDemOrdList_b .ellipsis span{
    width: 350px;
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.purDemOrdList_b .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.purDemOrdList_b .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.purDemOrdList_b .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.purDemOrdList_b .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.purDemOrdList_b .dd{height:659px!important; height:659px; overflow-y:hidden;}
.purDemOrdList_b .t_i{width:100%; height:auto;}
.purDemOrdList_b .t_i_h{width:100%; overflow-x:hidden;}
.purDemOrdList_b .ee{width:100%!important; width:100%; text-align: center;}
.purDemOrdList_b .t_i_h table{width:100%;}
.purDemOrdList_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.purDemOrdList_b .cc{width:100%; height:659px; background:#fff; overflow:auto;}
.purDemOrdList_b .cc table{width:100%; }
.purDemOrdList_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
