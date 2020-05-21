<template>
  <div class="JointStatistics">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle"><span class="orangeButton MR_twenty" @click="backToUpPage()">返回上一页</span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;合单统计
                        <div class="d_I_B PR widthOneHundred fontRight">
                            <input class="allChecked allSelect" type="checkbox" @change="allSelect()"/>
                            <img class="allSelect_img" src="../image/radio.png"/>
                            全选
                        </div>
                        <span class="orangeButton ML_twenty" @click="lookDetails()">查看合单缺口数据</span>
                    </div>
                    <div class="HDTJ">
                        <div v-for="(item,index) in sd_list">
                            <table class="MT_twenty B_T_L_R B_T_R_R B_B_R_R B_B_L_R" style="width:100%;" border="0" cellspacing="0" cellpadding="0">
                                <tr class="lineHeightNinety witeBg">
                                    <td class="B_T_L_R B_B_L_R fontCenter PR" style="width:8%" rowspan="3">
                                        <input class="oneSelect Select" @click="oneSelect(index)" type="checkbox" :name="index"/>
                                        <img :class='["Select_img","oneSelectImg"+index]' src="../image/radio.png"/>
                                    </td>
                                    <td colspan="2" class="F_S_24 blueFont">
                                        <span class="ML_twenty">{{item.sum_demand_name}}</span>
                                    </td>
                                    <td colspan="4" class="grayFont">
                                        <div class="ML_twenty">合期单号：{{item.sum_demand_sn}}</div>
                                        <div class="ML_twenty">创建时间：{{item.create_time}}</div>
                                    </td>
                                    <td class="PR B_T_R_R B_B_R_R" style="width:8%" rowspan="3">
                                        <span class="PA_Seventeen blueFont F_S_T Cursor" style="right: 15px;">
                                            <span @click="viewDetails(item.sum_demand_sn,index)">查看订单</span>
                                            <img @click="viewDetails(item.sum_demand_sn,index)" :class="'viewImg'+index" style="vertical-align:-2px;" src="../image/check_down.png"/>
                                        </span>
                                    </td>
                                </tr>
                                <tr class="lineHeightNinety fontCenter witeBg">
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.sku_num}}</div>
                                        <div class="grayFont">sku数</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.goods_num}}</div>
                                        <div class="grayFont">总需求数</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">
                                            <span v-if="item.may_num!=null">{{item.may_num}}</span>
                                            <span v-else>-</span>
                                        </div>
                                        <div class="grayFont">可采数</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.real_num}}</div>
                                        <div class="grayFont">实采数</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.diff_num}}</div>
                                        <div class="grayFont">缺口数</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.sort_num}}</div>
                                        <div class="grayFont">待分货数量</div>
                                    </td>
                                </tr>
                                <tr class="lineHeightNinety fontCenter witeBg">
                                    <td style="width:13%">
                                        <div class="F_S_24 G_M_F" style="line-height: 8px;margin-top: 18px;">{{item.demand_num}}</div>
                                        <div class="grayFont">订单数</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.total_purchase_price}}</div>
                                        <div class="grayFont">总需求额</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.diff_purchase_price}}</div>
                                        <div class="grayFont">缺口额</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24 G_O_F" style="line-height: 8px;margin-top: 18px;">{{item.real_rate}}%</div>
                                        <div class="grayFont">实采满足率</div>
                                    </td>
                                    <td style="width:13%">
                                        <div class="F_S_24" style="line-height: 8px;margin-top: 18px;">{{item.status}}</div>
                                        <div class="grayFont">状态</div>
                                    </td>
                                    <td style="width:13%">
                                    </td>
                                </tr>
                            </table>
                            <div class="O_F_A w_ratio PR" v-if="index==indexnum">
                                <table class="fontCenter tabletitle" border="0" cellspacing="0" cellpadding="0" style="width:100%;border-top: 0px;">
                                    <tr>
                                        <td v-for="item in tabletitle">{{item}}</td>
                                    </tr>
                                    <tr v-for="orderInfo in tableDataTwo">
                                        <td>{{orderInfo.demand_sn}}</td>
                                        <td>{{orderInfo.external_sn}}</td>
                                        <td>{{orderInfo.user_name}}</td>
                                        <td>{{orderInfo.sale_user_account}}</td>
                                        <td>{{orderInfo.expire_time}}</td>
                                        <td>{{orderInfo.goods_num}}</td>
                                        <td>{{orderInfo.sort}}</td>
                                        <td>{{orderInfo.status}}</td>
                                        <td>{{orderInfo.demand_type}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :current-page="page" :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </div>
                    <notFound v-if="isShow"></notFound>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      sd_list:[],
      isShow:false,
      total:0,//默认数据总数 
      pagesize:15,//每页条数默认15条 
      page:1,//page默认为1
      //合单统计
      sumDemandSnList:[],
      arrSpecSn:[],
      //展开商品
      indexnum:-1,
      tableDataTwo:[],
      tabletitle:[],
      listLength:0,
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$demandPurchaseTaskListURL+"?page="+vm.page+"&page_size="+vm.pagesize,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code==1000){
                vm.loading.close();
                vm.sd_list=res.data.data.demand_list_info.data;
                vm.total=res.data.data.total;
                let sumDemandSnStr='';
                vm.sumDemandSnList.forEach(element=>{
                    sumDemandSnStr += element;
                })
                for(var i = 0;i<vm.sd_list.length;i++){
                    let is_select = sumDemandSnStr.indexOf(vm.sd_list[i].sum_demand_sn)
                    if(is_select!=-1){
                        $("input[name="+i+"]").prop("checked",true);
                    }
                }
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            vm.loading.close();
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
        vm.pagesize=val;
        $('.allChecked').prop("checked",false);
        for(var i=0;i<vm.sd_list.length;i++){
            $("input[name="+i+"]").prop("checked",false);
        }
        if(vm.arrSpecSn.length!=0){
            vm.arrSpecSn.forEach(element=>{
                vm.sumDemandSnList.push(element)
            })
        }
        vm.sumDemandSnList=uniq(vm.sumDemandSnList)
        vm.demandPurchaseTaskList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val;
        $('.allChecked').prop("checked",false);
        for(var i=0;i<vm.sd_list.length;i++){
            $("input[name="+i+"]").prop("checked",false);
        }
        if(vm.arrSpecSn.length!=0){
            vm.arrSpecSn.forEach(element=>{
                vm.sumDemandSnList.push(element)
            })
        }
        vm.sumDemandSnList=uniq(vm.sumDemandSnList)
        vm.demandPurchaseTaskList()
    },
    //全选 
    allSelect(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        if(allChecked==true){
            $('.allSelect_img').attr('src',require('../image/radio_pre.png'));
            for(var i=0;i<vm.sd_list.length;i++){
                $('.oneSelectImg'+i).attr('src',require('../image/radio_pre.png'));
                $("input[name="+i+"]").prop("checked",true);
            }
        }else if(allChecked==false){
            $('.allSelect_img').attr('src',require('../image/radio.png'));
            for(var i=0;i<vm.sd_list.length;i++){
                $('.oneSelectImg'+i).attr('src',require('../image/radio.png'));
                $("input[name="+i+"]").prop("checked",false);
            }
        }
        vm.openDiscountBox();
    },
    //单选
    oneSelect(index){
        let vm = this;
        let tOrf = $("input[name="+index+"]").prop("checked")
        if(tOrf){
            $('.oneSelectImg'+index).attr('src',require('../image/radio_pre.png'));
        }else{
            $('.oneSelectImg'+index).attr('src',require('../image/radio.png'));
        }
        let isTrueList=[];
        for(var i=0;i<vm.sd_list.length;i++){
            let isTrue = $("input[name="+i+"]").prop("checked");
            isTrueList.push(isTrue);
        }
        for(var i=0;i<isTrueList.length;i++){
            if(isTrueList[i+1]!=undefined){
                if(isTrueList[i]==isTrueList[i+1]){
                    if(isTrueList[i]==true){
                        $('.allSelect_img').attr('src',require('../image/radio_pre.png'));
                        $('.allChecked').prop("checked",true)
                    }else if(isTrueList[i]==false){
                        $('.allSelect_img').attr('src',require('../image/radio.png'));
                        $('.allChecked').prop("checked",false)
                    }
                }else{
                    $('.allSelect_img').attr('src',require('../image/radio.png'));
                    $('.allChecked').prop("checked",false)
                    break;
                }
            }
        }
        vm.openDiscountBox();
    },
    //将选中的合单号缓存起来 
    openDiscountBox(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        let arrSpecSn = [];
        if(allChecked==true){
            vm.sd_list.forEach(specInfo=>{
                arrSpecSn.push(specInfo.sum_demand_sn);
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.sd_list.length;i++){
                let isTrue = $("input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    arrSpecSn.push(vm.sd_list[i].sum_demand_sn)
                }
            }
        }
        vm.arrSpecSn=arrSpecSn;
    },
    //展开商品
    viewDetails(sum_demand_sn,index){
        let vm = this;
        vm.indexnum=index;
        vm.tableDataTwo=[];
        vm.tabletitle=[];
        $('.viewImg'+index).attr('src',require('../image/check_up.png'));
        let content_text_height=$(event.target).parent().parent().parent().parent().parent().height();
        if(content_text_height<=285){
            let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
            axios.get(vm.url+vm.$sumDemandInfoURL+"?sum_demand_sn="+sum_demand_sn,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                load.close();
                $('.viewImg'+index).attr('src',require('../image/check_down.png'));
                if(res.data.code=='1000'){
                    vm.tableDataTwo=res.data.data;
                    vm.tabletitle=['需求单号','外部订单号','客户名称','客户分组','交期','需求数量','分货排序号','状态','类型']
                    $(".tabletitle").addClass("lineHeightForty");
                }else{
                    vm.$message(res.data.msg)
                }
            }).catch(function (error) {
                load.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        }else{
            vm.tableDataTwo=[];
            vm.tabletitle=[];
            $(".tabletitle").removeClass("lineHeightForty");
        }
        
    },
    //查看合单详情 
    lookDetails(){
        let vm = this;
        if(vm.arrSpecSn.length!=0){
            vm.arrSpecSn.forEach(element=>{
                vm.sumDemandSnList.push(element)
            })
        }
        vm.sumDemandSnList=uniq(vm.sumDemandSnList)
        if(vm.sumDemandSnList.length==0){
            vm.$message('请选择合单号后查看缺口数据！');
            return false;
        }
        vm.$router.push('/sumDiffInfo?sum_demand_sn='+vm.sumDemandSnList);
    },
    backToUpPage(){
        let vm = this
        vm.$router.push('/indexPage');
    }
  }
}
</script>

<style>
.el-progress-bar__outer {
    /* height: 6px!important; */
    border-radius: 0px!important;
    background-color: #fff!important;
    overflow: hidden!important;
    position: relative!important;
    vertical-align: middle!important;
}
.el-progress-bar__innerText {
    display: none!important;
}
.el-progress{
    width: 80%!important;
}
.el-progress-bar{
    vertical-align: 12px!important;
}
.HDTJ table tr:hover{
    background-color: #fff!important;
}
.Select{
    width: 20px;
    height: 20px;
    opacity: 0;
    z-index: 9999;
    font-size: 100px;
    position: absolute;
    right: 43%;
    top: 37px;
    margin: auto;
}
.Select_img{
    position: absolute;
    right: 40%;
    top: 34px;
    z-index: 1;
    width: 26px;
    height: 26px;
}
.allSelect{
    width: 20px;
    height: 20px;
    opacity: 0;
    z-index: 9999;
    font-size: 100px;
    position: absolute;
    right: 47%;
    top: 27px;
    margin: auto;
}
.allSelect_img{
    position: absolute;
    right: 44px;
    top: 24px;
    z-index: 1;
    width: 26px;
    height: 26px;
}
</style>
<style scoped>
@import '../css/publicCss.css';
</style>
