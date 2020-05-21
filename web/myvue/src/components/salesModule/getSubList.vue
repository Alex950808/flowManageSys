<template>
  <div class="getSubList">
      <!-- 子单列表 -->
      <el-row>
        <el-col :span="24" class="Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="clickRetract">
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                            <el-col :span="3"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>小DD单列表</span></div></el-col>
                            <el-col :span="2"><div class="fontRight">总&nbsp;单&nbsp;&nbsp;号&nbsp;</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="mis_order_sn" placeholder="请输入MIS订单号"></el-input></div></el-col>
                            <el-col :span="2"><div class="fontRight">子&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;号&nbsp;</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="sub_order" placeholder="请输入子单号"></el-input></div></el-col>
                            <el-col :span="2"><div class="fontRight">商&nbsp;&nbsp;家&nbsp;编&nbsp;码&nbsp;</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                            <el-col :span="2" class="fontRight"><div><span class="bgButton" @click="searchFrame()">搜索</span></div></el-col>
                        </el-row>
                        <el-row class="fontCenter MB_ten lineHeightForty">
                            <el-col :span="2" :offset="3"><div class="fontRight">状&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;态&nbsp;</div></el-col>
                            <el-col :span="3">
                                <template>
                                    <el-select v-model="status" placeholder="请选择" clearable>
                                        <el-option v-for="item in statuses" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </el-col>
                            <el-col :span="2"><div class="fontRight">商&nbsp;&nbsp;&nbsp;品&nbsp;&nbsp;名&nbsp;&nbsp;称&nbsp;</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                            <el-col :span="2"><div class="fontRight">商品规格码&nbsp;</div></el-col>
                            <el-col :span="3"><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                        </el-row>
                        <el-row class="fontCenter MB_ten lineHeightForty">
                            <el-col :span="2" :offset="3"><div class="fontRight">销售用户&nbsp;</div></el-col>
                            <el-col :span="3">
                                <div>
                                    <template>
                                        <el-select v-model="sale_user_id" clearable placeholder="请选择销售用户">
                                            <el-option v-for="item in saleUserInfo" :key="item.id" :label="item.user_name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="2"><div class="fontRight">交付开始时间&nbsp;</div></el-col>
                            <el-col :span="5">
                                <div>
                                    <el-date-picker v-model="entrustTime" type="daterange" value-format="yyyy-MM-dd" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="fontCenter MB_ten lineHeightForty">
                            <el-col :span="2" :offset="3"><div class="fontRight">外部单号&nbsp;</div></el-col>
                            <el-col :span="3">
                                <div>
                                    <el-input v-model="external_sn" placeholder="请输入外部单号"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="2"><div class="fontRight">创建时间排序&nbsp;</div></el-col>
                            <el-col :span="3" class="fontLift">
                                <span class="notBgButton" @click="sortByTime('1','2')">正序</span>
                                <span class="notBgButton" @click="sortByTime('1','1')">倒序</span>
                            </el-col>
                            <el-col :span="2"><div class="fontRight">交付时间排序</div></el-col>
                            <el-col :span="3" class="fontLift">
                                <span class="notBgButton" @click="sortByTime('2','2')">正序</span>
                                <span class="notBgButton" @click="sortByTime('2','1')">倒序</span>
                            </el-col>
                        </el-row>
                        <el-row class="fontLift MB_ten lineHeightForty">
                            <span class="redFont">*只展示近三个月且订单状态为未关闭的数据，所有搜索只在这三个月的数据中查找</span>
                        </el-row>
                        <div class="bgButton seeMore" @click="moreSearch()">更多搜索<i class="el-icon-arrow-up"></i></div>
                    </div>
                    <table class="tableTitle">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="10%">商品规格码</th>
                            <th width="10%">商家编码</th>
                            <th width="10%">销售折扣</th>
                            <th width="10%">美金原价</th>
                            <th width="10%">锁库数量</th>
                        </thead>
                    </table>
                    <table class="tableTitleTwo">
                        <thead>
                            <th width="20%">商品名称</th>
                            <th width="10%">商品规格码</th>
                            <th width="10%">商家编码</th>
                            <th width="10%">销售折扣</th>
                            <th width="10%">美金原价</th>
                            <th width="10%">锁库数量</th>
                        </thead>
                    </table>
                    <div v-for="(item,index) in tableData" :class="['content_text','content_text'+index]">
                        <el-row class="userInformation">
                            <el-col :span="6"><div class="redFont">销售账号 ：{{item.sale_user_account}}</div></el-col>
                            <el-col :span="6"><div class="redFont">交付时间 ：{{item.entrust_time}}</div></el-col>
                            <el-col :span="6"><div>子订单号 ：{{item.sub_order_sn}}</div></el-col>
                            <el-col :span="3"><div class="">订单状态 ：{{item.status}}</div></el-col>
                            <el-col :span="3">
                                <span class="notBgButton" @click="viewDetails(item.sub_order_sn)">小DD单详情</span>
                                <i class="el-icon-arrow-down notBgButton" title="点击展示更多" @click="pullOrUp(index,$event)"></i>  
                            </el-col>
                        </el-row>
                        <el-row class="userInformation">
                            <el-col :span="6"><div class="">MIS订单号 ：<span class="notBgButton" @click="viewDetailsMIS(item.mis_order_sn,item.sub_order_sn)">{{item.mis_order_sn}}</span></div></el-col>
                            <el-col :span="6"><div class="order">
                                <template v-if="item.status=='BD'">
                                    <el-popover  placement="top-start" width="200" trigger="click"
                                        content="BD单时间也是锁库时间">
                                        <i class="el-icon-question redFont" slot="reference"></i>
                                    </el-popover>
                                </template>
                                {{item.status}}停留时间：
                                <span class="YD" v-if="item.status=='YD'">{{remainingTime()}}</span>
                                <span class="BD" v-if="item.status=='BD'">{{remainingTime()}}</span>
                                <span class="DD" v-if="item.status=='DD'">{{remainingTime()}}</span>
                            </div></el-col> 
                            <el-col :span="6"><div>创建时间 ：{{item.create_time}}</div></el-col>
                            
                            <el-col :span="4">
                                <div class="">
                                    <template>
                                        <el-popover  placement="top-start" width="200" trigger="click"
                                            content="子单列表-根据当前商品库存将子单分为现货单和需求单">
                                            <i class="el-icon-question redFont" slot="reference"></i>
                                        </el-popover>
                                    </template>
                                    是否分单 ：{{item.is_submenu}}
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="userInformation">
                            <el-col :span="7"><div>外部订单号 ：{{item.external_sn}}</div></el-col>
                            <el-col :span="4"><div>sku数 ：{{item.sku_num}}</div></el-col>
                            <el-col :span="13"><div class="fontLift">备注 ：{{item.remark}}</div></el-col>
                        </el-row>
                        <el-row>
                            <table class="table-two" style="width:100%;text-align: center">
                                <tr v-for="good in item.goods_data">
                                    <td width="20%" class="overOneLinesHeid fontLift">
                                        <el-tooltip class="item" effect="light" :content="good.goods_name" placement="right">
                                        <span style="-webkit-box-orient: vertical;">{{good.goods_name}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td width="10%">{{good.spec_sn}}</td>
                                    <td width="10%">{{good.erp_merchant_no}}</td>
                                    <td width="10%">{{good.sale_discount}}</td>
                                    <td width="10%">{{good.spec_price}}</td>
                                    <td width="10%">{{good.wait_lock_num}}</td>
                                </tr>
                            </table>
                        </el-row>
                    </div>
                    <notFound v-if="isShow"></notFound>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
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
import fontWatermark from '@/components/UiAssemblyList/fontWatermark';
export default {
  components:{
      notFound,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      saleUserInfo:'',
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      search:'',//用户输入数据
        //   remainingTime:'',
      //搜索参数
      mis_order_sn:'',
      sub_order_sn:'',//子单号
      status:'',//状态
      statuses:[],
      erp_merchant_no:'',//商家编码
      goods_name:'',//商品名称
      spec_sn:'',//商品规格码
      sale_user_id:'',
      orderNum:'',
      orderType:'',
    //   entrust_start:'',
    //   entrust_end:'',
      external_sn:'',
      entrustTime:[],
        
      days:'',
      hours:'',
      minutes:'',
      seconds:'',
      times:[],
      sub_order:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
    this.tableTitle();
  },
  created(){
      this.aa();
  },
  methods:{
    getDataList(active){
        let vm = this;
        vm.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
        vm.statuses.splice(0);
        if(vm.$route.query.mis_order_sn!=undefined){
            vm.mis_order_sn=vm.$route.query.mis_order_sn
        }
        if(vm.$route.query.status!=undefined){
            vm.status=vm.$route.query.status
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            vm.erp_merchant_no=vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.sub_order!=undefined){
            vm.sub_order=vm.$route.query.sub_order
        }
        if(vm.$route.query.goods_name!=undefined){
            vm.goods_name=vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            vm.goods_name=vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            vm.sale_user_id=parseInt(vm.$route.query.sale_user_id) 
        }
        if(vm.$route.query.page!=undefined){ 
            vm.page=vm.$route.query.page
        }
        axios.post(vm.url+vm.$getSubListURL,
            {
                "mis_order_sn":vm.mis_order_sn,
                "status":vm.status,
                "erp_merchant_no":vm.erp_merchant_no,
                "goods_name":vm.goods_name,
                "spec_sn":vm.spec_sn,
                "sub_order_sn":vm.sub_order,
                "page":vm.page,
                "pageSize":vm.pagesize,
                "sale_user_id":vm.sale_user_id,
                "orderType":vm.orderType,
                "orderNum":vm.orderNum,
                "entrust_start":vm.entrustTime[0],
                "entrust_end":vm.entrustTime[1],
                "external_sn":vm.external_sn,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close();
            if(active!=undefined){
                active.removeClass('disable')
            }
            vm.tableData=res.data.subOrderList.data;
            vm.total=res.data.subOrderList.total;
            vm.saleUserInfo=res.data.saleUserInfo;
            let statuses=res.data.orderStatus;
            for(let key in statuses){
                vm.statuses.push({"label":statuses[key],"id":key});
            }
            if(res.data.subOrderList.total>0){
                vm.isShow=false;
            }else if(res.data.subOrderList.total==0){
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
            }
            vm.aa();
            let index =0;
            vm.tableData.forEach(element=>{
                if(element.status=='YD'){
                    vm.countdowm(element.yd_time,index,'YD')
                }else if(element.status=='BD'){
                    vm.countdowm(element.bd_time,index,'BD')
                }else if(element.status=='DD'){[
                    vm.countdowm(element.dd_time,index,'DD')
                ]}
                index++;
            })
            if(vm.$route.query.page!=undefined){ 
                vm.recordPageSize(vm.$route.query.page)
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
    tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=320;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".getSubList .tableTitleTwo").addClass("addclass");
                $(".getSubList .tableTitleTwo").width($(".getSubList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".getSubList .tableTitleTwo").removeClass("addclass");
            }
        })
    },
    //点击下拉或者收起表格
    pullOrUp(index,event){
        let vm = this;
        let content_text_height=$(event.target).parent().parent().parent().height();
        if(content_text_height==202){
            $(".content_text"+index+"").css({
                "transition-property:":"height",
                "transition-duration":"5s",
            })
            $(".content_text"+index+"").height('auto');
            $(event.target).addClass("el-icon-arrow-up");
            $(event.target).removeClass("el-icon-arrow-down");
            $(event.target).attr("title","点击收起")
        }else{
            $(".content_text"+index+"").height('202');
            $(event.target).addClass("el-icon-arrow-down");
            $(event.target).removeClass("el-icon-arrow-up");
            $(event.target).attr("title","点击展示更多"); 
        }
    },
    viewDetails(sub_order_sn){
        let vm = this;
        let urlParam = '&page='+vm.page;
        if(vm.mis_order_sn!=''){
            urlParam='&mis_order_sn='+vm.mis_order_sn
        }
        if(vm.status!=''){
            urlParam+='&status='+vm.status
        }
        if(vm.erp_merchant_no!=''){
            urlParam+='&erp_merchant_no='+vm.erp_merchant_no
        }
        if(vm.sub_order!=''){
            urlParam+='&sub_order='+vm.sub_order
        }
        if(vm.goods_name!=''){
            urlParam+='&goods_name='+vm.goods_name
        }
        if(vm.spec_sn!=''){
            urlParam+='&spec_sn='+vm.spec_sn
        }
        if(vm.sale_user_id!=''){
            urlParam+='&sale_user_id='+vm.sale_user_id
        }
        vm.$router.push('/getSubDetail?sub_order_sn='+sub_order_sn+urlParam);
    },
    viewDetailsMIS(mis_order_sn,sub_order_sn){
        let vm = this;
        vm.$router.push('/MISorderListDetails?mis_order_sn='+mis_order_sn+'&sub_order_sn='+sub_order_sn+'&isGetSubList=GetSubList');
    },
    //搜索
    searchFrame(){
        let vm = this;
        vm.page=1;
        $(".el-pager li").eq(vm.$route.query.page-1).removeClass("active")
        $(".el-pager li").eq(0).addClass("active")
        vm.$router.push('/getSubList');
        if(vm.entrustTime==null){
            vm.entrustTime=[];
        }
        vm.getDataList();
    },
    //分页
    current_change(currentPage){
        this.currentPage = currentPage;
    },
    handleSizeChange(val) {
        let vm=this;
        $(".el-pager li").eq(vm.$route.query.page-1).removeClass("active") 
        let urlParam = '';
        if(vm.$route.query.mis_order_sn!=undefined){
            urlParam='&mis_order_sn='+vm.$route.query.mis_order_sn
        }
        if(vm.$route.query.status!=undefined){
            urlParam+='&status='+vm.$route.query.status
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            urlParam+='&erp_merchant_no='+vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.sub_order!=undefined){
            urlParam+='&sub_order='+vm.$route.query.sub_order
        }
        if(vm.$route.query.goods_name!=undefined){
            urlParam+='&goods_name='+vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            urlParam+='&spec_sn='+vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            urlParam+='&sale_user_id='+vm.$route.query.sale_user_id
        }
        vm.$router.push('/getSubList?isfirst=isfirst'+urlParam);
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        $(".el-pager li").eq(vm.$route.query.page-1).removeClass("active") 
        let urlParam = '';
        if(vm.$route.query.mis_order_sn!=undefined){
            urlParam='&mis_order_sn='+vm.$route.query.mis_order_sn
        }
        if(vm.$route.query.status!=undefined){
            urlParam+='&status='+vm.$route.query.status
        }
        if(vm.$route.query.erp_merchant_no!=undefined){
            urlParam+='&erp_merchant_no='+vm.$route.query.erp_merchant_no
        }
        if(vm.$route.query.sub_order!=undefined){
            urlParam+='&sub_order='+vm.$route.query.sub_order
        }
        if(vm.$route.query.goods_name!=undefined){
            urlParam+='&goods_name='+vm.$route.query.goods_name
        }
        if(vm.$route.query.spec_sn!=undefined){
            urlParam+='&spec_sn='+vm.$route.query.spec_sn
        }
        if(vm.$route.query.sale_user_id!=undefined){
            urlParam+='&sale_user_id='+vm.$route.query.sale_user_id
        }
        vm.$router.push('/getSubList?isfirst=isfirst'+urlParam);
        vm.page=val
        vm.getDataList()
    },
    aa(){
        let vm = this;
        for(var i = 0; i < 500; i++) {
            clearInterval(i);
        }
    },
    countdowm(time,index,Dorder){
        let vm = this;
        let nowTime = new Date().getTime();
        let startTime = new Date(time).getTime();
        let maxtime = nowTime-startTime;
        vm.times=setInterval(function() {
            maxtime=maxtime+1000;
            let days = parseInt(maxtime / 1000 / 60 / 60 / 24,10); //计算剩余天数
            let hours = parseInt(maxtime / 1000 / 60 / 60 % 24,10); //计算剩余小时
            let minutes = parseInt(maxtime / 1000 / 60 % 60,10);//计算剩余分钟
            let seconds = parseInt(maxtime / 1000 % 60,10);//计算剩余秒数
            vm.remainingTime(days,hours,minutes,seconds,index,Dorder);
        }, 1000);
    },
    sortByTime(type,num){
        let vm = this;
        vm.orderType=type;
        vm.orderNum=num;
        $('.el-icon-caret-top').removeClass('redFont');
        $('.el-icon-caret-bottom').removeClass('redFont');
        $(event.target).addClass('redFont').addClass('disable');
        $(event.target).siblings().removeClass('redFont')
        vm.getDataList($(event.target));
    },
    //点击展开更多搜索
    moreSearch(){
        let vm = this;
        let content_height=$(event.target).parent().height();
        if(content_height==112){
            $(".clickRetract").height('260')
            $(event.target).children().addClass('el-icon-arrow-down').removeClass('el-icon-arrow-up')
        }else{
            $(".clickRetract").height('112')
            $(event.target).children().addClass('el-icon-arrow-up').removeClass('el-icon-arrow-down')
        }
    }
  },
  computed:{
      remainingTime(){
          let vm = this;
          return function(days,hours,minutes,seconds,index,Dorder){
              $(".content_text"+index+" .order ."+Dorder+"").html(days+"天"+hours+"小时"+minutes+"分钟"+seconds+"秒")
          }
      },
      recordPageSize(){
          return function(p){
              setTimeout(function(){
                $(".el-pager li").eq(parseInt(p)-1).addClass("active")
                $(".el-pager li").eq(0).removeClass("active")
              },1000)
          }
      }
  }
}
</script>

<style>
.getSubList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.getSubList .content_text{
    width: 100%;
    height: 202px;
    overflow: hidden;
    border: 1px solid #ebeef5;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 20px;
}
.getSubList .content_text table{
    width: 100%;
}
.getSubList .content_text table tr{
    height: 50px;
    line-height: 50px;
    line-height: 40px;
}
.getSubList .content_text table table  tr:nth-child(even){
    background:#fafafa;
}
.getSubList .content_text table tr:hover{
    background:#ced7e6;
}
.getSubList .userInformation{
    text-align:center;
    height: 50px;
    line-height: 50px;
    background-color: #f8fafb;
    font-weight: bold;
}
.getSubList .addclass{
    position: fixed;
    top: 70px;
    z-index: 10;
}
.getSubList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.seeMore{
    position: absolute;
    top: 27px;
    right: 76px;
}
.clickRetract{
    height: 112px;
    overflow: hidden;
    margin-bottom: 20px;
}
</style>
