<template>
  <div class="procurementChannel_b">
    <!-- 采购渠道页面 -->
        <el-col :span="24" style="background-color: #fff;">
            <div class="procurementChannel bgDiv">
                <el-row class="channelList">
                  <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                      <el-col :span="21">
                        <span><span class="coarseLine MR_ten"></span>采购渠道</span>
                      </el-col>
                      <el-col :span="3">
                        
                      </el-col>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>采购渠道</span></div></el-col>
                      <el-col :span="2"><div>方式ID</div></el-col>
                      <el-col :span="3" >
                          <template>
                            <el-select v-model="method_id" clearable placeholder="请选择方式ID">
                              <el-option v-for="item in methodsNames" :key="item.value" :label="item.channelName" :value="item.channelId">
                              </el-option>
                            </el-select>
                          </template>
                      </el-col>
                      <el-col :span="2"><div>方式名称</div></el-col>
                      <el-col :span="3"><div><el-input v-model="method_name" placeholder="请输入方式名称"></el-input></div></el-col>
                      <el-col :span="2"><div>渠道名称</div></el-col>
                      <el-col :span="3"><div><el-input v-model="channels_name" placeholder="请输入渠道名称"></el-input></div></el-col>
                      <el-col :span="2"><div>渠道编号</div></el-col>
                      <el-col :span="3"><div><el-input v-model="channels_sn" placeholder="请输入渠道编号"></el-input></div></el-col>
                      <el-col :span="2"><span class="bgButton" @click="getChannelData()">搜索</span></el-col>
                    </el-row>
                    <el-row class="MT_twenty floatRight MB_ten lineHeightForty">
                      <el-col :span="24"><span class="notBgButton" @click="dialogVisible = true;cancel()"><i class="iconfont upDataIcon verticalAlign">&#xea22;</i>新增渠道</span></el-col>
                    </el-row>
                    <tableContent class="MB_twenty" :tableContent='tableContent,TCTitle,tableField,contentConfig' @Edit="Edit"></tableContent>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                      :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                  </el-col>
                </el-row>
                <el-dialog title="新增渠道" :visible.sync="dialogVisible" width="800px">
                    <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>渠道名称:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="channelName" placeholder="渠道名称"></el-input>
                        </div>
                      </el-col>
                      <el-col :span="4" :offset="1">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>方式名称:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <template>
                            <el-select v-model="methodsName" placeholder="请选择">
                              <el-option v-for="item in methodsNames" :key="item.value" :label="item.channelName" :value="item.channelId">
                              </el-option>
                            </el-select>
                          </template>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row class="lineHeightForty MB_twenty">
                      <!-- <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span>团队追加返点比例:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="teamMonthPoints" type="Number" placeholder="团队追加返点比例 单位(%)"></el-input>
                        </div>
                      </el-col> -->
                      <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span>运费折扣:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="postDiscount" placeholder="请输入运费折扣"></el-input>
                        </div>
                      </el-col>
                      <el-col :span="4" :offset="1">
                        <div style="line-height: 40px;">
                          <span>充值返积分比例:</span> 
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="rechargePoints" type="Number" placeholder="充值返积分比例 单位(%)"></el-input>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row class="lineHeightForty MB_twenty">
                      <!-- <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>运费折扣:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="postDiscount" placeholder="请输入运费折扣"></el-input>
                        </div>
                      </el-col> -->
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span>是否计入外采临界点:</span>
                        </div>
                      </el-col>
                      <el-col :span="5">
                        <div>
                          <template>
                              <el-radio-group v-model="countWai">
                                  <el-radio :label="1">是</el-radio>
                                  <el-radio :label="0">否</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                      <el-col :span="5" :offset="1">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>档位衡量币种:</span>
                        </div>
                      </el-col>
                      <el-col :span="6">
                        <div>
                          <template>
                              <el-radio-group v-model="margin_currency">
                                  <el-radio :label="1">美金原价</el-radio>
                                  <el-radio :label="2">韩币</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>资金结算方式:</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div>
                          <template>
                              <el-radio-group v-model="originalOrDiscount">
                                  <el-radio :label="0">美金原价</el-radio>
                                  <el-radio :label="1">LVIP折扣</el-radio>
                                  <el-radio :label="2">立即结算</el-radio>
                                  <el-radio :label="3">基准折扣</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row>
                    <!-- <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>毛利结算方式:</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div>
                          <template>
                              <el-radio-group v-model="marginPayment">
                                  <el-radio :label="1">美金原价</el-radio>
                                  <el-radio :label="2">LVIP折扣</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row> -->
                    <!-- <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>是否存在多个档位:</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div>
                          <template>
                              <el-radio-group v-model="is_gears">
                                  <el-radio :label="1">存在</el-radio>
                                  <el-radio :label="2">不存在</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row> -->
                    <span slot="footer" class="dialog-footer">
                      <el-button type="info" @click="dialogVisible = false;cancel()">取 消</el-button>
                      <el-button type="primary" @click="addChannel()">新增</el-button>
                    </span>
                </el-dialog>
                <el-dialog title="编辑渠道" :visible.sync="dialogVisibleEdit" width="800px">
                    <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>渠道名称:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="channelNameEdit" placeholder="渠道名称"></el-input>
                        </div>
                      </el-col>
                      <el-col :span="4" :offset="1">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>方式名称:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <template>
                            <el-select v-model="methodsNameEdit" placeholder="请选择">
                              <el-option v-for="item in methodsNames" :key="item.value" :label="item.channelName" :value="item.channelId">
                              </el-option>
                            </el-select>
                          </template>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row class="lineHeightForty MB_twenty">
                      <!-- <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span>团队追加返点比例:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="teamMonthPoints" type="Number" placeholder="团队追加返点比例 单位(%)"></el-input>
                        </div>
                      </el-col> -->
                      <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>运费折扣:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="postDiscountEdit" placeholder="请输入运费折扣"></el-input>
                        </div>
                      </el-col>
                      <el-col :span="4" :offset="1">
                        <div style="line-height: 40px;">
                          <span>充值返积分比例:</span> 
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="rechargePointsEdit" type="Number" placeholder="充值返积分比例 单位(%)"></el-input>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row class="lineHeightForty MB_twenty">
                      <!-- <el-col :span="4">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>运费折扣:</span>
                        </div>
                      </el-col>
                      <el-col :span="7">
                        <div>
                          <el-input v-model="postDiscountEdit" placeholder="请输入运费折扣"></el-input>
                        </div>
                      </el-col> -->
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>是否计入外采临界点:</span>
                        </div>
                      </el-col>
                      <el-col :span="5">
                        <div>
                          <template>
                              <el-radio-group v-model="countWaiEdit">
                                  <el-radio :label="1">是</el-radio>
                                  <el-radio :label="0">否</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                      <el-col :span="5" :offset="1">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>档位衡量币种:</span>
                        </div>
                      </el-col>
                      <el-col :span="6">
                        <div>
                          <template>
                              <el-radio-group v-model="margin_currencyEdit">
                                  <el-radio :label="1">美金原价</el-radio>
                                  <el-radio :label="2">韩币</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>需求资金结算方式:</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div>
                          <template>
                              <el-radio-group v-model="originalOrDiscountEdit">
                                  <el-radio :label="0">美金原价</el-radio>
                                  <el-radio :label="1">LVIP折扣</el-radio>
                                  <el-radio :label="2">立即结算</el-radio>
                                  <el-radio :label="3">基准折扣</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row>
                    <!-- <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>毛利结算方式:</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div>
                          <template>
                              <el-radio-group v-model="marginPayment">
                                  <el-radio :label="1">美金原价</el-radio>
                                  <el-radio :label="2">LVIP折扣</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row> -->
                    <!-- <el-row class="lineHeightForty MB_twenty">
                      <el-col :span="6">
                        <div style="line-height: 40px;">
                          <span><span class="redFont">*</span>是否存在多个档位:</span>
                        </div>
                      </el-col>
                      <el-col :span="18">
                        <div>
                          <template>
                              <el-radio-group v-model="is_gears">
                                  <el-radio :label="1">存在</el-radio>
                                  <el-radio :label="2">不存在</el-radio>
                              </el-radio-group>
                          </template>
                        </div>
                      </el-col>
                    </el-row> -->
                    <span slot="footer" class="dialog-footer">
                      <el-button type="info" @click="dialogVisibleEdit = false;cancel()">取 消</el-button>
                      <el-button type="primary" @click="dialogVisibleEdit = false;doEditChannel()">确定</el-button>
                    </span>
                </el-dialog>
                <div v-if="isShow" style="text-align: center;"><img class="notData" src="../../image/notData.png"/></div>
            </div>
        </el-col>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import { Loading } from 'element-ui';
import { bgHeight } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
import searchBox from '@/components/UiAssemblyList/searchBox';
import tableContent from '@/components/UiAssemblyList/tableContent'
export default {
    components:{
        searchBox,
        tableContent,
    },
    data() {
      return {
        url: `${this.$baseUrl}`,
        dialogVisible: false,
        dialogVisibleEdit:false,
        channelName:'',
        methodsNames:[],
        methodsName:'',
        method_name:[],
        postDiscount:'',
        originalOrDiscount:'',
        total:0,//默认数据总数
        pagesize:15,//每页条数默认15条
        page:1,//page默认为1
        // marginPayment:'',
        countWai:'',
        margin_currency:'',
        // is_gears:'',
        loading:'',
        isShow:false,
        headersStr:'',
        config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"false"}]',
        tableContent:'',
        TCTitle:['渠道名称','方式名称','渠道编号','编辑'],
        tableField:['channels_name','method_name','channels_sn'],//表格字段
        contentConfig:'[{"isShow":"isEditContent"},{"parameter":"id"}]',
        channelID:'',
        postDiscountEdit:'',
        channelNameEdit:'',
        methodsNameEdit:'',
        countWaiEdit:'',
        originalOrDiscountEdit:'',
        teamMonthPoints:'',
        rechargePoints:'',
        rechargePointsEdit:'',
        margin_currencyEdit:'',
        //搜索
        method_id:'',
        method_name:'',
        channels_sn:'',
        channels_name:'',
      }
    },
    mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getChannelData();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
      this.getMethodList();
    },
     methods: {
      getChannelData(){
        let vm=this;
        axios.get(vm.url+vm.$channelsListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&method_id="+vm.method_id+"&method_name="+vm.method_name
        +"&channels_sn="+vm.channels_sn+"&channels_name="+vm.channels_name,
        {
            headers:vm.headersStr,
        }
        ).then(function(res){
          // let data=res.data.data
          vm.tableContent=JSON.stringify(res.data.data.data);
          vm.total=res.data.data.total;
          // data.forEach(element => { 
          //   vm.tableData1.push({'name':element.channels_name,'codeAbbreviated':element.channels_sn,"method_name":element.method_name})
          //   vm.channelList.push(element.channels_name) 
          // });
          vm.loading.close()
        }).catch(function (error) {
                vm.loading.close();
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //获取方式列表 
      getMethodList(){
        let vm=this;
        axios.get(vm.url+vm.$methodListURL,
          {
            headers:vm.headersStr,
          }
        ).then(function(res){
          if(res.data.code==1000){
            res.data.data.forEach(element=>{
              vm.methodsNames.push({"channelName":element.method_name,"channelId":element.id});
            })
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
      //新增渠道 
      addChannel(){
        let vm=this;
        if(vm.channelName==''){
          vm.$message('采购渠道名称不能为空!');
          return false;
        }
        if(vm.methodsName==''){
          vm.$message('采购方式名称不能为空!');
          return false;
        }
        if(vm.postDiscount==''&&vm.countWai!=''){
          vm.$message('运费折扣和是否计入临界点要同时为空或同时不为空!!');
          return false;
        }
        if(vm.postDiscount!=''&&vm.countWai==''){
          vm.$message('运费折扣和是否计入临界点要同时为空或同时不为空!');
          return false;
        }
        if(vm.originalOrDiscount===''){
          vm.$message('美金原价还是折扣选项不能为空!');
          return false;
        }
        if(vm.margin_currency===''){
          vm.$message('档位衡量币种选项不能为空!');
          return false;
        }
        vm.dialogVisible = false;
        axios.post(vm.url+vm.$createChannelsURL,
          {
            "channels_name":this.channelName,
            "method_id":vm.methodsName,
            "is_count_wai":vm.countWai,
            "post_discount":vm.postDiscount,
            "original_or_discount":vm.originalOrDiscount,
            // "is_gears":vm.is_gears,
            // "team_add_points":vm.teamMonthPoints,
            "recharge_points":vm.rechargePoints,
            "margin_currency":vm.margin_currency,
          },
          {
              headers:vm.headersStr,
          }
        ).then(function(res){
          vm.channelName='';
          vm.methodsName='';
          if(res.data.code==1000){
            vm.dialogVisible = false;
            vm.loading.close();
            vm.$message(res.data.msg);
            vm.getChannelData();
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
      cancel(){
        let vm=this;
        vm.channelName='';
        vm.methodsName='';
        vm.channelNameEdit='';
        vm.methodsNameEdit='';
        vm.postDiscount='';
        vm.countWai='';
        // vm.marginPayment='';
        // vm.is_gears='';
        vm.teamMonthPoints='';
        vm.rechargePoints='';
        // vm.is_gears='';
        vm.originalOrDiscountEdit='';
        vm.rechargePointsEdit='';
        vm.margin_currencyEdit='';
      },
      //打开编辑渠道
      Edit(id){
        let vm=this;
        vm.cancel();
        vm.channelID=id;
        vm.dialogVisibleEdit=true;
        axios.post(vm.url+vm.$editChannelURL,
          {
            "id":id,
          },
          {
              headers:vm.headersStr,
          }
        ).then(function(res){
          vm.channelNameEdit=res.data.data[0].channels_name;
          vm.methodsNameEdit=res.data.data[0].method_id;
          vm.postDiscountEdit=res.data.data[0].post_discount;
          vm.countWaiEdit=res.data.data[0].is_count_wai;
          vm.originalOrDiscountEdit=res.data.data[0].original_or_discount;
          // vm.is_gears=res.data.data[0].is_gears;
          // vm.marginPayment=res.data.data[0].margin_payment;
          // vm.teamMonthPoints=res.data.data[0].team_add_points;
          vm.rechargePointsEdit=res.data.data[0].recharge_points;
          vm.margin_currencyEdit=res.data.data[0].margin_currency;
        }).catch(function (error) {
              vm.loading.close()
              if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
              }
          });
      },
      doEditChannel(){
        let vm = this;
        if(vm.channelNameEdit==''){
          vm.$message('采购渠道名称不能为空!');
          return false;
        }
        if(vm.methodsNameEdit==''){
          vm.$message('采购方式名称不能为空!');
          return false;
        }
        // if(vm.postDiscountEdit==''){
        //   vm.$message('运费折扣不能为空!');
        //   return false;
        // }
        if(vm.countWaiEdit===''){
          vm.$message('是否计入临界点不能为空!');
          return false;
        }
        if(vm.originalOrDiscountEdit===''){
          vm.$message('美金原价还是折扣选项不能为空!');
          return false;
        }
        axios.post(vm.url+vm.$doEditChannelURL,
          {
            "id":vm.channelID,
            "channels_name":vm.channelNameEdit,
            "method_id":vm.methodsNameEdit,
            "post_discount":vm.postDiscountEdit,
            "is_count_wai":vm.countWaiEdit,
            "original_or_discount":vm.originalOrDiscountEdit,
            // "is_gears":vm.is_gears,
            // "team_add_points":vm.teamMonthPoints,
            "recharge_points":vm.rechargePointsEdit,
            "margin_currency":vm.margin_currencyEdit,
          },
          {
              headers:vm.headersStr,
          }
        ).then(function(res){
          vm.$message(res.data.msg);
          if(res.data.code==1000){
            vm.getChannelData();
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
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.pagesize=val
          vm.getChannelData()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.page=val
          vm.getChannelData()
      },
    }
}
</script>
<style>
.el-table{
    text-align: center;
}
.el-table th>.cell {
    text-align: center;
}

</style>
