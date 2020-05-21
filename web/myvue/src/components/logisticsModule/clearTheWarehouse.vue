<template>
  <div class="clearTheWarehouse_b">
    <!-- 清点页面 -->
        <el-col :span="24" style="background-color: #fff;">
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="clearTheWarehouse">
                        <el-row>
                            <el-col :span="10" class="title_left">
                                <span class="bgButton" @click="backUpPage()">返回上一级</span>
                                <span class="date">{{title.SPdanhao}}</span>
                            </el-col>
                            <el-col :span="14" class="title_right">
                                <div style="float: right;position: relative">
                                <span class="bgButton" @click="d_table()">下载该入库表格</span>
                                </div>
                            </el-col>
                        </el-row>


                        <div class="t_i">
                            <div class="t_i_h" id="hh">
                                <div class="ee">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th style="width:200px;">商品名称</th>
                                            <th style="width:150px">商品代码</th>
                                            <th style="width:150px">erp编码</th>
                                            <th style="width:150px">实采数量</th>
                                            <th style="width:150px">清点数量</th>
                                            <th style="width:150px">差异值</th>
                                            <th style="width:150px">备注</th>
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
                                        <td style="width:150px">{{item.spec_sn}}</td>
                                        <td style="width:150px">{{item.erp_merchant_no}}</td>
                                        <td style="width:150px">{{item.day_buy_num}}</td>
                                        <td style="width:150px">{{item.allot_num}}</td>
                                        <td style="width:150px">{{item.diff_num}}</td>
                                        <td style="width:150px">{{item.Remarks}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="fontCenter lineHeighteighty" @click="confirmShow()"><span class="bgButton">确认入库</span></div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
        <customConfirmationBoxes :contentStr="contentStr" @confirmationAudit="confirmationAudit" @determineIsNo="determineIsNo"></customConfirmationBoxes>
  </div>
</template>

<script>
import axios from 'axios'
import customConfirmationBoxes from '@/components/UiAssemblyList/customConfirmationBoxes'
export default {
      components:{
          customConfirmationBoxes,
      },
      data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        search:"",//输入框输入内容
        total:0,//页数默认为0
        page:1,//分页页数
        pagesize:15,//每页的数据条数、
        title:'',
        show:false,
        headersStr:'',
        contentStr:"请您确认是否要入库？",
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getClearTheWareData()
    },
    methods: {
        //获取待清点页面数据
        getClearTheWareData(){
            let vm=this;
            // let headersToken=sessionStorage.getItem("token");
            // axios.get(vm.url+vm.$clearTheWarehouse+"?real_purchase_sn="+vm.$route.query.real_purchase_sn,
            //     {
            //         headers:{
            //             'Authorization': 'Bearer ' + headersToken,
            //             'Accept': 'application/vnd.jmsapi.v1+json',
            //         }
            //     }
            // ).then(function(res){
            //     vm.tableData=res.data.realPurchaseInfo.goods_list
            //     // res.data.realPurchaseInfo.goods_list.forEach(element => {
            //     //     vm.tableData.push({"goodsName":element.goods_name,"goodsCode":element.spec_sn,"erpCode":element.erp_merchant_no,"realMining":element.day_buy_num,
            //     //     "clearPoint":element.allot_num,"difference":element.diff_num,"Remarks":element.Remarks
            //     //     });
            //     // });
            //     vm.title={"CGQdanhao":res.data.realPurchaseInfo.purchase_sn,"SPdanhao":res.data.realPurchaseInfo.real_purchase_sn}
            // }).catch(function (error) {
            //     if(error.response.status=="401"){
            //     vm.$message('登录过期,请重新登录!');
            //     sessionStorage.setItem("token","");
            //     vm.$router.push('/');
            //     }
            // });
            vm.tableData=JSON.parse(sessionStorage.getItem("tableData"));
        },
        //下载表格
        d_table(){
            let vm=this;
            let headersToken=sessionStorage.getItem("token");
            window.open(vm.url+vm.$downloadAllotGoodsURL+'?real_purchase_sn='+vm.$route.query.real_purchase_sn+"&purchase_sn="+vm.$route.query.purchase_sn+"&group_sn="+vm.$route.query.group_sn+"&is_mother="+vm.$route.query.is_mother+'&token='+headersToken);
        },
        //确认入库
        confirmationAudit(){
            let vm=this;
            axios.post(vm.url+vm.$inputStorageURL,
                {
                    "real_purchase_sn":vm.$route.query.real_purchase_sn,
                    "purchase_sn":vm.$route.query.purchase_sn,
                    "group_sn":vm.$route.query.group_sn,
                    "is_mother":vm.$route.query.is_mother,
                },
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.determineIsNo();
                if(res.data.code==2024){
                    vm.open();
                    vm.$router.push('/purchaseOrder');
                }
            }).catch(function (error) {
                vm.determineIsNo();
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //消息弹框
        open() {
            this.$message('确认审核成功');
        },
        //回上一级页面
        backUpPage(){
            let vm=this;
            if(vm.$route.query.isOutTime){
                this.$router.push('/outOfTime');
            }
            if(vm.$route.query.isPurchase){
                this.$router.push('/purchaseOrder');
            }
        },
        scrollEvent(){
            var a=document.getElementById("cc").scrollTop;
            var b=document.getElementById("cc").scrollLeft;
            document.getElementById("hh").scrollLeft=b;
        },
        //确认弹框否
        determineIsNo(){
            $(".confirmPopup_b").fadeOut()
        },
        //确认弹框是
        confirmShow(){
            $(".confirmPopup_b").fadeIn()
        },
    }
}
</script>
<style scoped>
.clearTheWarehouse_b .ellipsis span{
    line-height: 23px;
    display: -webkit-box;
    /* autoprefixer: off */
    -webkit-box-orient: vertical;
     /* autoprefixer: on */ 
    -webkit-line-clamp: 1;
    overflow: hidden;
}
.d_table{
    cursor: pointer;
}
.examine{
    cursor: pointer;
}
.clearTheWarehouse_b .t_n{width:19%; height:703px; background:buttonface; float:left; border-bottom:1px solid #ccc; border-left:1px solid #ccc}
.clearTheWarehouse_b .t_n span{display:block; text-align:center; line-height:20px; border-top: 1px solid #ccc; width:305px; height:43px}
.clearTheWarehouse_b .t_number{border-right:1px solid #ccc; width:100%; margin-bottom:5px}
.clearTheWarehouse_b .t_number td{border-bottom:1px solid #ccc;width: 293px;height: 40px; text-align:center}
.clearTheWarehouse_b .dd{height:659px!important; height:659px; overflow-y:hidden;}
.clearTheWarehouse_b .t_i{width:100%; height:auto; border-right:1px solid #ccc;border-left: 1px solid #ccc;border-top:1px solid #ccc}
.clearTheWarehouse_b .t_i_h{width:100%; overflow-x:hidden; background:buttonface;}
.clearTheWarehouse_b .ee{text-align: center;}
.clearTheWarehouse_b .t_i_h table{width:100%;}
.clearTheWarehouse_b .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.clearTheWarehouse_b .cc{width:100%; height:659px; border-bottom:1px solid #ccc; background:#fff; overflow:auto;}
.clearTheWarehouse_b .cc table{width:100%; }
.clearTheWarehouse_b .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
.clearTheWarehouse_b .confirmPopup_b{
    width: 100%;
    height: 100%;
    /* background-color:rgba(0, 0, 0, 0.2); */
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
}
.clearTheWarehouse_b .confirmPopup{
  width: 400px;
  height: 189px;
  /* padding-top: 20px; */
  margin: 375px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  /* margin-top: 500px; */
  z-index: 999;
  background-color: #fff;
  /* border-radius: 20px; */
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.clearTheWarehouse_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.clearTheWarehouse_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.clearTheWarehouse_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE;
    color: #fff; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.clearTheWarehouse_b .confirmTitle{
    display: inline-block;
    width: 400px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.titleText{
    
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.confirmPopup_b .el-icon-view{
    color: #fff;
}
.clearTheWarehouse_b .el-icon-close{
    margin-left: 270px;
}
</style>

<style scoped lang=less>
@import '../../css/logisticsModule.less';
</style>