<template>
  <div class="auditDemandDetails_b">
    <!-- 查看优采推荐详情页面 -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="auditDemandDetails excellentRecommend">
                        <el-row>
                            <el-col :span="24" class="title_left">
                                <router-link  to='/excellentRecommend'><span class="bgButton">返回上一级</span></router-link>
                                <span class="date">{{title}}</span>
                            </el-col>
                        </el-row>
                        <div v-if="isShow" class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                        <thead>
                                            <tr>
                                                <th style="width:300px;">商品名称</th>
                                                <th style="width:240px">商品代码</th>
                                                <th style="width:240px">erp编码</th>
                                                <th style="width:240px">商品规格码</th>
                                                <th style="width:130px">销售折扣</th>
                                                <th style="width:130px">总需求数量</th>
                                                <th style="width:150px">外采临界点</th>
                                                <th :style="divWidth()+'text-align:center'">优采推荐</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="cc" id="cc" @scroll="scrollEvent()">
                                <table cellpadding="0" cellspacing="0" border="0" :style="tableWidth()">
                                    <tr v-for="item in tableData">
                                        <td class="ellipsis" style="width:300px;">
                                            <el-tooltip class="item" effect="light" :content="item.goods_name" placement="right">
                                            <span style="-webkit-box-orient: vertical;">{{item.goods_name}}</span>
                                            </el-tooltip>
                                        </td>
                                        <td style="width:240px">{{item.erp_prd_no}}</td>
                                        <td style="width:240px">{{item.erp_merchant_no}}</td>
                                        <td style="width:240px">{{item.spec_sn}}</td>
                                        <td style="width:130px">{{item.sale_discount}}</td>
                                        <td style="width:130px">{{item.goods_num}}</td>
                                        <td  width="150px">{{item.wai_line_point}}</td>
                                        <td :style="divWidth()">
                                            <span v-for="(itemO,index) in item.discount_info" class="Recommend"><span class="Serial">{{index+1}}</span><span class="channel" title="采购渠道">{{itemO.brand_channel}}</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="Discount" title="品牌折扣">{{itemO.brand_discount}}</span>&nbsp;&nbsp;&nbsp;&nbsp;<span title="可采数量">{{itemO.may_num}}</span></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <notFound v-if="isNotFound"></notFound>
                    </div>
                </el-col>
            </el-row>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import { tableStyleByDataLength } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
    components:{
        notFound,
    },
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        loading:'',
        title:'',
        num:'',
        widthLength:'',
        headersStr:'',
        isNotFound:false,
        isShow:true,
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getDetailsData();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        getDetailsData(){
            let vm=this;
            vm.title=vm.$route.query.purchase_sn;
            axios.post(vm.url+vm.$recommendDetailURL,
                {
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "demand_sn":vm.$route.query.demand_sn
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.loading.close()
                if(res.data.code=='1000'){
                    vm.tableData=res.data.data;
                    vm.isNotFound=false;
                    vm.isShow=true;
                    var recLength=[];
                    if(vm.tableData!=''){
                        vm.tableData.forEach(element=>{
                            recLength.push(element.discount_info.length);
                        })
                    }      
                    var j=recLength[0];
                    for(var i=0;i<=recLength.length;i++){
                            if(recLength[i+1]>=j){
                                j=recLength[i+1]
                            }
                    } 
                    vm.num = j;
                    tableStyleByDataLength(vm.tableData.length,15);
                }else{
                    vm.isNotFound=true;
                    vm.isShow=false;
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                vm.loading.close()
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
            document.getElementById("hh").scrollLeft=b;
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downloadRecommendListURL+'?demand_sn='+vm.$route.query.demand_sn+'&purchase_sn='+vm.$route.query.purchase_sn+'&token='+headersToken);
           
        },
        divWidth(){
            let vm=this;
            var widthLength=vm.num*300+20;
            vm.widthLength=widthLength;
            return "width:"+widthLength+"px;text-align: left;"
        },
        tableWidth(){
            let vm=this;
            var widthLength=1450+vm.widthLength;
            var tiWidth=$(".t_i").width();
            if(widthLength<tiWidth){
                return "width:"+tiWidth+"px"
            }else{
                return "width:"+widthLength+"px"
            }
            
        }
    }
}
</script>
<style>
.excellentRecommend .ellipsis span{
    /* width: 350px; */
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.excellentRecommend .Serial{
    display: inline-block;
    border-radius: 50%;
    background-color: #00C1DE;
    color: #fff;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
}
/* .excellentRecommend .channel{
    display: inline-block;
    width: 100px;
    text-align: left;
} */
.excellentRecommend .Discount{
    display: inline-block;
    width: 30px;
    text-align: left;
}
.excellentRecommend .Recommend{
    display: inline-block;
    width: 300px;
    text-align: left;
}
.excellentRecommend .t_i{width:100%; height:auto;}
.excellentRecommend .t_i_h{width:100%; overflow-x:hidden; background:#fafafa;}
.excellentRecommend .ee{width:100%!important; width:100%; text-align: center;}
.excellentRecommend .t_i_h table{width:1530px;}
.excellentRecommend .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.excellentRecommend .cc{width:100%; height:659px; border-bottom:1px solid #ccc; overflow:auto;}
.excellentRecommend .cc table{width:1530px; }
.excellentRecommend .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>