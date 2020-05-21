<template>
  <div class="purchaseDemandList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="listTitleStyle select">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购需求列表</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>
                        <div class="bgButton ML_twenty" @click="sumPurchaseDemand();cleanData()">合并需求单</div>
                        <div class="bgButton ML_twenty" @click="addSumDemand()">追加合单</div>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty select">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>采购需求列表</span></div></el-col>
                        <el-col :span="3"><div>需求单号</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="demand_sn" placeholder="请输入需求单号"></el-input></div></el-col>
                        <el-col :span="3"><div>客户</div></el-col>
                        <el-col :span="3">
                            <template>
                                    <el-select v-model="sale_user_id" clearable placeholder="选择客户名称">
                                        <el-option v-for="item in su_infoL" :key="item.id" :label="item.user_name" :value="item.id">
                                        </el-option>
                                    </el-select>
                            </template>
                        </el-col>
                        <el-col :span="3"><div>外部订单号</div></el-col>
                        <el-col :span="3"><div><el-input v-model="external_sn" placeholder="请输入外部订单号"></el-input></div></el-col>
                        <el-col :span="3"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
                    <div class="MT_twenty MB_ten lineHeightForty select">
                        <div class="bgButton ML_twenty" @click="sumPurchaseDemand();cleanData()">合并需求单</div>
                        <div class="bgButton ML_twenty" @click="addSumDemand()">追加合单</div>
                    </div>
                    <el-dialog title="合并需求单" :visible.sync="selectDialogVisibleRate" width="1100px">
                        <el-row class="lineHeightForty">
                            <el-col :span="4" :offset="1">
                                <div><span class="redFont">*</span>合单名称：</div>
                            </el-col>
                            <el-col :span="6">
                                <el-input v-model="sum_demand_name" v-validate="'required'" name="sum_demand_name" placeholder="请输入合单名称"></el-input>
                            </el-col>
                            <el-col :span="6">
                                <span v-show="errors.has('sum_demand_name')" class="text-style redFont" v-cloak> {{ errors.first('sum_demand_name') }} </span>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty"><el-col :span="10" :offset="1">分货对应排序：</el-col></el-row>
                        <el-row class="lineHeightSixty fontCenter">
                            <el-col :span="5">
                                <div>需求单号</div>
                            </el-col>
                            <el-col :span="5" :offset="1">
                                <div>外部单号</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div>交付日期</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div>期望到仓日</div>
                            </el-col>
                            <el-col :span="3" :offset="1">
                                <div>用户</div>
                            </el-col>
                            <el-col :span="3">
                                分货对应排序
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightSixty fontCenter" v-for="(item,I) in timeAndUserList" :key="I">
                            <el-col :span="5">
                                <div><span class="redFont">*</span>{{item.demand_sn}}：</div>
                            </el-col>
                            <el-col :span="5" :offset="1">
                                <div class="overOneLinesHeid" :title="item.external_sn">
                                    <span v-if="item.external_sn!=''">{{item.external_sn}}</span>
                                    <span v-if="item.external_sn==''">-</span>    
                                </div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div v-if="item.expire_time!=null">{{item.expire_time}}</div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div v-if="item.arrive_store_time!=''">{{item.arrive_store_time}}</div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="3" :offset="1">
                                <div v-if="item.user_name!=null">{{item.user_name}}</div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="3">
                                <!-- <el-input :name="'sort'+I" placeholder="请输入对应排序"></el-input> -->
                                <template>
                                    <el-select v-model="sumModelList[I]" @change="selectSortData(sumModelList[I])" placeholder="请选择对应排序">
                                        <el-option v-for="sortInfo in sortList" :key="sortInfo.id" :label="sortInfo.name" :value="sortInfo.id"></el-option>
                                    </el-select>
                                </template>
                            </el-col>
                        </el-row>
                        <div slot="footer" class="dialog-footer">
                            <span class="grayBgButton" @click="selectDialogVisibleRate = false">取 消</span>
                            <span class="redBgButton removeattr" @click="submitSortBySelect()">确 定</span>
                        </div>
                    </el-dialog>
                    <el-dialog title="追加合单" :visible.sync="addSumDemandDialogVisible" width="1100px">
                        <el-row class="lineHeightForty">
                            <el-col :span="5" :offset="1">
                                <div><span class="redFont">*</span>请选择已合单单号：</div>
                            </el-col>
                            <el-col :span="6">
                                <span>
                                    <template>
                                        <el-select v-model="demandSn" @change="selectDemandSn()" placeholder="请选择追加合单单号">
                                            <el-option v-for="item in demand_info" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </span>
                            </el-col>
                        </el-row>
                        <!-- <el-row class="lineHeightForty"><el-col :span="10" :offset="1">分货对应排序：</el-col></el-row> -->
                        <el-row class="lineHeightSixty fontCenter">
                            <el-col :span="5">
                                <div>需求单号</div>
                            </el-col>
                            <el-col :span="5" :offset="1">
                                <div>外部单号</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div>交付日期</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div>期望到仓日</div>
                            </el-col>
                            <el-col :span="3" :offset="1">
                                <div>用户</div>
                            </el-col>
                            <el-col :span="3">
                                分货对应排序
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty" v-if="demandList.length!=0">
                            <el-col :span="10" :offset="1">往期需求单：</el-col>
                        </el-row>
                        <el-row class="lineHeightSixty fontCenter" v-for="(item,I) in demandList" :key="I">
                            <el-col :span="5">
                                <div><span class="redFont">*</span>{{item.demand_sn}}：</div>
                            </el-col>
                            <el-col :span="5" :offset="1">
                                <div class="overOneLinesHeid" :title="item.external_sn">
                                    <span v-if="item.external_sn!=''">{{item.external_sn}}</span>
                                    <span v-if="item.external_sn==''">-</span>    
                                </div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div>{{item.expire_time}}</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div v-if="item.arrive_store_time!=''">{{item.arrive_store_time}}</div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="3" :offset="1">
                                <div>{{item.user_name}}</div>
                            </el-col>
                            <el-col :span="3">
                                <template>
                                    <el-select v-model="modelList[I]" filterable placeholder="请选择对应排序">
                                        <el-option v-for="sortInfo in sortList" :key="sortInfo.id" :label="sortInfo.name" :value="sortInfo.id"></el-option>
                                    </el-select>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty">
                            <el-col :span="10" :offset="1">追加需求单：</el-col>
                        </el-row>
                        <el-row class="lineHeightSixty fontCenter" v-for="(item,I) in timeAndUserList" :key="item.demand_sn">
                            <el-col :span="5">
                                <div><span class="redFont">*</span>{{item.demand_sn}}：</div>
                            </el-col>
                            <el-col :span="5" :offset="1">
                                <div v-if="item.external_sn!=''" class="overOneLinesHeid">
                                    <span>{{item.external_sn}}</span>
                                </div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div v-if="item.expire_time!=null">{{item.expire_time}}</div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="2" :offset="1">
                                <div v-if="item.arrive_store_time!=''">{{item.arrive_store_time}}</div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="3" :offset="1">
                                <div v-if="item.user_name!=null">{{item.user_name}}</div>
                                <div v-else>-</div>
                            </el-col>
                            <el-col :span="3">
                                <template>
                                    <el-select v-model="selectList[I]" placeholder="请选择对应排序">
                                        <el-option v-for="item in sortList" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </el-col>
                        </el-row>
                        <div slot="footer" class="dialog-footer">
                            <span class="grayBgButton" @click="addSumDemandDialogVisible = false">取 消</span>
                            <span class="redBgButton removeattr" @click="doAddSumDemand()">确 定</span>
                        </div>
                    </el-dialog>
                    <el-dialog title="确认追加合单" :visible.sync="confirmAddSDDialogVisible" width="800px">
                        <span>{{msgStr}}</span>
                        <div slot="footer" class="dialog-footer">
                            <span class="grayBgButton" @click="confirmAddSDDialogVisible = false">取 消</span>
                            <span class="redBgButton removeattr" @click="confirmAddSumDemand()">确 定</span>
                        </div>
                    </el-dialog>
                    <table class="tableTitle MB_twenty" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <th width="6%"><input class="allChecked" style="width: 20px;height: 20px;" type="checkbox" @change="allSelect()"/>全选</th>
                            <th width="12%">需求单号</th>
                            <th width="12%">外部订单号</th>
                            <th width="10%">客户</th>
                            <th width="6%">sku数</th>
                            <th width="6%">需求数</th>
                            <th width="6%">交付日期</th>
                            <th width="6%">期望到仓日</th>
                            <th width="10%">创建时间</th>
                            <th width="6%">状态</th>
                            <th width="10%">操作</th>
                        </thead>
                    </table>
                    <table class="tableTitleTwo" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <th width="6%"><input class="allChecked" style="width: 20px;height: 20px;" type="checkbox" @change="allSelect()"/>全选</th>
                            <th width="12%">需求单号</th>
                            <th width="12%">外部订单号</th>
                            <th width="10%">客户</th>
                            <th width="6%">sku数</th>
                            <th width="6%">需求数</th>
                            <th width="6%">交付日期</th>
                            <th width="6%">期望到仓日</th>
                            <th width="10%">创建时间</th>
                            <th width="6%">状态</th>
                            <th width="10%">操作</th>
                        </thead>
                    </table>
                    <el-row>
                        <table v-for="(item,index) in tableData" class="MB_twenty" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td width="6%" class="cb_one">
                                        <input style="width: 20px;height: 20px;" @click="oneSelect(index)" type="checkbox" :name="index"/>
                                    </td>
                                    <td width="12%">{{item.demand_sn}}</td>
                                    <td width="12%">{{item.external_sn}}</td>
                                    <td width="10%">{{item.user_name}}</td>
                                    <td width="6%">{{item.sku_num}}</td>
                                    <td width="6%">{{item.goods_num}}</td>
                                    <td width="6%">{{item.expire_time}}</td>
                                    <td width="6%">{{item.arrive_store_time}}</td>
                                    <td width="10%">{{item.create_time}}</td>
                                    <td width="6%">
                                        <span v-if="item.status==1">待合单</span>
                                        <span v-if="item.status==2">待分配</span>
                                        <span v-if="item.status==3">已分配</span>
                                        <span v-if="item.status==4">待审核</span>
                                        <span v-if="item.status==5">采购中</span>
                                        <span v-if="item.status==6">关闭</span>
                                        <span v-if="item.status==7">延期</span>
                                    </td>
                                    <td width="10%">
                                        <span class="notBgButton" @click="goDetail(item.demand_sn)">查看详情</span>
                                    </td>
                                </tr>
                            </tbody> 
                        </table>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-row>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
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
import { uniq } from '../../filters/publicMethods.js';//引入有公用方法的jsfile 
import searchBox from '../UiAssemblyList/searchBox';
export default {
  components:{
      notFound,
      searchBox,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//页数默认为0
      page:1,//分页页数
      pagesize:15,//每页的数据条数  
      arrSpecSn:[],
      selectDialogVisibleRate:false,
      sum_demand_name:'',
      demandSnInfo_list:[],
      timeAndUserList:[],
      //追加合单 
      addSumDemandDialogVisible:false,
      demand_info:[],
      demand_list:[],
      demandSn:'',
      demandList:[],
      modelList:[],
      sortList:[],
      selectList:[],
      sumModelList:[],
      sum_demand_sn:'',
      //确认追加合单 
      confirmAddSDDialogVisible:false,
      msgStr:'',
      //搜索 
      demand_sn:'',
      external_sn:'',
      sale_user_id:'',
      su_infoL:[],
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
    this.tableTitle();
  },
  methods:{
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$purchaseDemandListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&demand_sn="+vm.demand_sn+"&external_sn="+vm.external_sn
            +"&sale_user_id="+vm.sale_user_id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.data.demand_list_info.total_num>0){
                vm.tableData=res.data.data.demand_list_info.demand_goods_info;
                vm.total=res.data.data.demand_list_info.total_num;
                vm.su_infoL=res.data.data.su_info;
                vm.isShow=false;
            }else if(res.data.data.demand_list_info.total_num===0){
                vm.tableData=[];
                vm.total=0;
                vm.isShow=true;
            }
        }).catch(function (error) {
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
        for(var i=0;i<vm.tableData.length;i++){
            $(".cb_one input[name="+i+"]").prop("checked",false);
        }
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val;
        for(var i=0;i<vm.tableData.length;i++){
            $(".cb_one input[name="+i+"]").prop("checked",false);
        }
        vm.getDataList()
    },
    tableTitle(){
        let vm=this;
        $(window).scroll(function(){
            var scrollTop = $(this).scrollTop();
            var thisHeight=245;
            if(scrollTop>thisHeight){
                $('.tableTitleTwo').show();
                $(".purchaseDemandList .tableTitleTwo").addClass("addclass");
                $(".purchaseDemandList .tableTitleTwo").width($(".purchaseDemandList .select").width());
            }else if(scrollTop<thisHeight){
                $('.tableTitleTwo').hide();
                $(".purchaseDemandList .tableTitleTwo").removeClass("addclass");
            }
        })
    },
    //全选 
    allSelect(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        if(allChecked==true){
            for(var i=0;i<vm.tableData.length;i++){
                $(".cb_one input[name="+i+"]").prop("checked",true);
            }
        }else if(allChecked==false){
            for(var i=0;i<vm.tableData.length;i++){
                $(".cb_one input[name="+i+"]").prop("checked",false);
            }
        }
    },
    //单选 
    oneSelect(index){
        let vm = this;
        let isTrueList=[];
        for(var i=0;i<vm.tableData.length;i++){
            let isTrue = $(".cb_one input[name="+i+"]").prop("checked");
            isTrueList.push(isTrue);
        }
        for(var i=0;i<isTrueList.length;i++){
            if(isTrueList[i+1]!=undefined){
                if(isTrueList[i]==isTrueList[i+1]){
                    if(isTrueList[i]==true){
                        $('.allChecked').prop("checked",true)
                    }else if(isTrueList[i]==false){
                        $('.allChecked').prop("checked",false)
                    }
                }else{
                    $('.allChecked').prop("checked",false)
                    break;
                }
            }
        }
    },
    sumPurchaseDemand(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        let arrSpecSn = [];
        vm.arrSpecSn.splice(0);
        vm.timeAndUserList.splice(0);
        vm.sumModelList=[];
        vm.demandSnInfo_list.splice(0);
        if(allChecked==true){
            vm.tableData.forEach(specInfo=>{
                arrSpecSn.push(specInfo.demand_sn);
                vm.timeAndUserList.push({"demand_sn":specInfo.demand_sn,"external_sn":specInfo.external_sn,"expire_time":specInfo.expire_time,"arrive_store_time":specInfo.arrive_store_time,"user_name":specInfo.user_name})
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.tableData.length;i++){
                let isTrue = $(".cb_one input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    arrSpecSn.push(vm.tableData[i].demand_sn);
                    vm.timeAndUserList.push({"demand_sn":vm.tableData[i].demand_sn,"external_sn":vm.tableData[i].external_sn,"expire_time":vm.tableData[i].expire_time,"arrive_store_time":vm.tableData[i].arrive_store_time,"user_name":vm.tableData[i].user_name})
                }
            }
        }
        vm.arrSpecSn=arrSpecSn;
        if(arrSpecSn.length==0){
            vm.$message('请选择需求单号之后合并需求单!');
            return false;
        }else{
            vm.selectDialogVisibleRate=true;
            let i = 1;
            vm.sortList.splice(0);
            vm.arrSpecSn.forEach(element=>{
                vm.sumModelList.push('')
                vm.sortList.push({"name":i,"id":i})
                i++;
            })
        }
    },
    submitSortBySelect(){
        let vm = this; 
        // var containSpecial = RegExp(/[(\ )(\~)(\!)(\@)(\#)(\$)(\%)(\^)(\&)(\*)(\()(\))(\-)(\_)(\+)(\=)(\[)(\])(\{)(\})(\|)(\\)(\;)(\:)(\')(\")(\,)(\/)(\<)(\>)(\?)(\)]+/); 
        // let isSpecialChar = containSpecial.test(vm.sum_demand_name);
        // if(isSpecialChar){
        //     vm.$message('合单名称中不能含有特殊字符！');
        //     return false;
        // }
        let upDataList = vm.sumModelList
        if(uniq(upDataList).length<vm.sumModelList.length){
            vm.$message('不能重复选择相同的排序号！请检查');
            return false;
        }
        for(var i = 0;i<vm.arrSpecSn.length;i++){
            if(vm.sumModelList[i]==''){
                vm.$message('请填写完整对应的排序后提交！');
                return false;
            }
            vm.demandSnInfo_list.push({"demand_sn":vm.arrSpecSn[i],"sort":vm.sumModelList[i]})
        }
        this.$validator.validateAll().then(valid => {
            if(valid){
                vm.selectDialogVisibleRate=false;
                axios.post(vm.url+vm.$sumPurchaseDemandURL,
                    {
                        "demand_sn_info":vm.demandSnInfo_list,
                        "sum_demand_name":vm.sum_demand_name,
                    },
                    {
                        headers:vm.headersStr,
                    },
                ).then(function(res){
                    if(res.data.code=="1000"){
                        vm.$message(res.data.msg);
                        vm.getDataList()
                        $('.allChecked').prop("checked",false)
                        for(var i=0;i<vm.tableData.length;i++){
                            let isTrue = $("input[name="+i+"]").prop("checked",false);
                        }
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
            }
        })
    },
    //点击追加合单 
    addSumDemand(){
        let vm = this;
        let allChecked = $('.allChecked').prop("checked");
        let arrSpecSn = [];
        vm.demandSn='';
        vm.arrSpecSn.splice(0);
        vm.timeAndUserList.splice(0);
        vm.demandList.splice(0);
        vm.selectList=[];
        vm.demandSnInfo_list.splice(0);
        vm.sortList.splice(0);
        if(allChecked==true){
            vm.tableData.forEach(specInfo=>{
                arrSpecSn.push(specInfo.demand_sn);
                vm.timeAndUserList.push({"demand_sn":specInfo.demand_sn,"external_sn":specInfo.external_sn,"expire_time":specInfo.expire_time,"arrive_store_time":specInfo.arrive_store_time,"user_name":specInfo.user_name})
            })
        }else{
            let isTrueList=[];
            for(var i=0;i<vm.tableData.length;i++){
                let isTrue = $(".cb_one input[name="+i+"]").prop("checked");
                isTrueList.push(isTrue);
            }
            for(var i=0;i<isTrueList.length;i++){
                if(isTrueList[i]==true){
                    arrSpecSn.push(vm.tableData[i].demand_sn);
                    vm.timeAndUserList.push({"demand_sn":vm.tableData[i].demand_sn,"external_sn":vm.tableData[i].external_sn,"expire_time":vm.tableData[i].expire_time,"arrive_store_time":vm.tableData[i].arrive_store_time,"user_name":vm.tableData[i].user_name})
                }
            }
        }
        vm.arrSpecSn=arrSpecSn;
        if(arrSpecSn.length==0){
            vm.$message('请选择需求单号之后追加合单!');
            return false;
        }else{
            vm.addSumDemandDialogVisible=true;
            vm.demand_info.splice(0); 
            let i = 1;
            vm.arrSpecSn.forEach(element=>{
                vm.selectList.push('')
                vm.sortList.push({"name":i,"id":i})
                i++;
            })
            axios.get(vm.url+vm.$addSumDemandURL,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                vm.demand_list=res.data.data;
                res.data.data.forEach(element=>{
                    vm.demand_info.push({"name":element.sum_demand_name,"id":element.sum_id})
                })
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        }
    },
    selectDemandSn(){
        let vm = this;
        vm.sortList.splice(vm.arrSpecSn.length);
        vm.modelList.splice(0);
        vm.selectList.splice(0); 
        vm.demand_list.forEach(element=>{
            if(vm.demandSn==element.sum_id){
                vm.sum_demand_sn=element.sum_demand_sn,
                vm.demandList=element.demand_info;
                element.demand_info.forEach(demandInfo=>{
                    vm.modelList.push(demandInfo.sort)
                })
            }     
        })
        let i = vm.arrSpecSn.length+1;
        vm.demandList.forEach(element=>{
            vm.sortList.push({"name":i,"id":i})
            i++;
        })
    },
    doAddSumDemand(){
        let vm = this;
        let j = vm.modelList.length;
        let k = 0;
        let aaa = [];
        vm.modelList.forEach(element=>{
            aaa.push(element);
        })
        vm.selectList.forEach(element=>{
            aaa.push(element);
        })
        let bbb = aaa;
        if(uniq(bbb).length<aaa.length){
            vm.$message('不能重复选择相同的排序号！请检查');
            return false;
        }
        for(var i = 0;i<vm.demandList.length;i++){
            if(vm.modelList[i]==''){
                vm.$message('请填写完整对应的排序后提交！');
                return false;
            }
            vm.demandSnInfo_list.push({"demand_sn":vm.demandList[i].demand_sn,"sort":vm.modelList[i],"is_new":"2"})
        }
        for(var i = 0;i<vm.arrSpecSn.length;i++){
            if(vm.selectList[i]==''){
                vm.$message('请填写完整对应的排序后提交！');
                return false;
            }
            vm.demandSnInfo_list.push({"demand_sn":vm.arrSpecSn[i],"sort":vm.selectList[i],"is_new":"1"})
        }
        vm.addSumDemandDialogVisible=false;
        axios.post(vm.url+vm.$doAddSumDemandURL,
            {
                "demand_sn_info":vm.demandSnInfo_list,
                "sum_id":vm.demandSn,
                "sum_demand_sn":vm.sum_demand_sn,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=="1000"){
                vm.$message(res.data.msg);
                vm.getDataList()
                $('.allChecked').prop("checked",false)
                for(var i=0;i<vm.tableData.length;i++){
                    let isTrue = $("input[name="+i+"]").prop("checked",false);
                }
            }else if(res.data.code=="1009"){
                vm.confirmAddSDDialogVisible=true;
                vm.msgStr=res.data.msg;
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
    confirmAddSumDemand(){
        let vm = this;
        vm.confirmAddSDDialogVisible=false;
        axios.post(vm.url+vm.$doAddSumDemandURL,
            {
                "demand_sn_info":vm.demandSnInfo_list,
                "sum_id":vm.demandSn,
                "sum_demand_sn":vm.sum_demand_sn,
                "is_sure":'1',
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=="1000"){
                vm.$message(res.data.msg);
                vm.getDataList()
                $('.allChecked').prop("checked",false)
                for(var i=0;i<vm.tableData.length;i++){
                    let isTrue = $("input[name="+i+"]").prop("checked",false);
                }
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
    goDetail(demand_sn){
        let vm = this;
        vm.$router.push('/purchaseDemandDetail?demand_sn='+demand_sn+"&demand=demand");
    },
    cleanData(){
        let vm = this;
        // vm.demandSnInfo_list.splice(0);
        vm.sum_demand_name='';
    },
    selectSortData(num){
        let vm = this;
        vm.sortList.forEach(e=>{

        })
    }
  }
}
</script>
<style>
.purchaseDemandList .tableTitle{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
}
.purchaseDemandList .tableTitleTwo{
    width:100%;
    text-align: center;
    height: 50px;
    line-height: 50px;
    background-color: #d8dce6;
    display: none;
}
.purchaseDemandList .addclass{
    position: fixed;
    top: 69px;
    z-index: 10;
}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>