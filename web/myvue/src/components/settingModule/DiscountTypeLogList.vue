<template>
  <div class="DiscountTypeLogList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                        <span><span class="coarseLine MR_ten"></span>折扣类型设置记录列表</span>
                        <span class="bgButton MR_twenty floatRight MT_twenty" @click="discountTypeList()">新增折扣类型记录</span>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>折扣类型设置记录列表</span></div></el-col>
                      <el-col :span="2"><div>方式</div></el-col>
                      <el-col :span="3" >
                        <el-select v-model="method_id" clearable placeholder="请选择方式">
                            <el-option v-for="item in method_info" :key="item.value" :label="item.method_name" :value="item.id"></el-option>
                        </el-select>
                      </el-col>
                      <el-col :span="2"><div>渠道</div></el-col>
                      <el-col :span="3">
                        <el-select v-model="channels_id" clearable placeholder="请选择方式">
                            <el-option v-for="item in channel_info" :key="item.value" :label="item.channels_name" :value="item.id"></el-option>
                        </el-select>
                      </el-col>
                      <el-col :span="2"><div>开始时间</div></el-col>
                      <el-col :span="3">
                          <div>
                              <el-date-picker style="width:190px;" v-model="startDate" type="date" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                          </div>
                      </el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                      <el-col :span="3"><span class="bgButton" @click="discountTypeList()">新增折扣类型记录</span></el-col>
                    </el-row>
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="2" :offset="3"><div>结束时间</div></el-col>
                      <el-col :span="3">
                          <div>
                              <el-date-picker style="width:190px;" v-model="endDate" type="date" value-format="yyyy-MM-dd" placeholder="请选择结束时间"></el-date-picker>
                          </div>
                      </el-col>
                    </el-row>
                    <el-dialog title="新增折扣类型记录" :visible.sync="dialogVisibleDTL" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>方式-渠道：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-cascader :options="options" @change="isChange()" v-model="option"></el-cascader>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>开始时间：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-date-picker class="w_ratio" v-model="start_date" value-format="yyyy-MM-dd" type="date" placeholder="请选择开始日期"></el-date-picker>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>结束时间：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-date-picker class="w_ratio" v-model="end_date" value-format="yyyy-MM-dd" type="date" placeholder="请选择结束日期"></el-date-picker>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row v-if="gear_type.length!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    <span class="redFont">*</span>品牌成本折扣档位
                                    <template>
                                        <el-popover  placement="top-start" width="200" trigger="click"
                                            content="品牌商品渠道">
                                            <i class="el-icon-question redFont" slot="reference"></i>
                                        </el-popover>
                                    </template>
                                    ：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="cost_id">
                                        <el-checkbox class="d_I_B" v-for="item in gear_type" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="gear_type_predict!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    <span class="redFont">*</span>品牌预计完成档位:
                                    <template>
                                        <el-popover  placement="top-start" width="200" trigger="click"
                                            content="品牌商品渠道">
                                            <i class="el-icon-question redFont" slot="reference"></i>
                                        </el-popover>
                                    </template>
                                    ：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="predict_id">
                                        <el-checkbox class="d_I_B" v-for="item in gear_type_predict" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="goods_gear_type!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    <span class="redFont">*</span>商品成本折扣档位：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="goods_cost_id">
                                        <el-checkbox class="d_I_B" v-for="item in goods_gear_type" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="goods_gear_type_predict!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    商品预计完成档位：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="goods_predict_id">
                                        <el-checkbox class="d_I_B" v-for="item in goods_gear_type_predict" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="brand_gear_points_predict!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    品牌活动预计完成档位：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="brand_month_predict_id">
                                        <el-checkbox class="d_I_B" v-for="item in brand_gear_points_predict" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="pay_type!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    结算档位：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="pay_id">
                                        <el-checkbox class="d_I_B" v-for="item in pay_type" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="gear_type.length!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    品牌报价档位：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="offer_id">
                                        <el-checkbox class="d_I_B" v-for="item in gear_type" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="goods_gear_type!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    商品报价档位：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="goods_offer_id">
                                        <el-checkbox class="d_I_B" v-for="item in goods_gear_type" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <el-row v-if="total_type!=0">
                            <el-col :span="4">
                                <div class="MT_ten">
                                    当月计算毛利档位(默认全选)：
                                </div>
                            </el-col>
                            <el-col :span="20">
                                <template>
                                    <el-checkbox-group v-model="month_type_id">
                                        <el-checkbox class="d_I_B" v-for="item in total_type" :key="item.type_id" :label="item.type_id" :value="item.type_id">{{item.type_name}}</el-checkbox>
                                    </el-checkbox-group>
                                </template>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleDTL = false;">取 消</el-button>
                            <el-button type="primary" @click="doAddDiscountTypeLog()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <table class="MB_Thirty" style="width:100%;text-align: center;line-height: 35px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="w-100">开始时间</th>
                                <th class="w-100">结束时间</th>
                                <th class="w-80">方式</th>
                                <th class="w-80">渠道</th>
                                <th class="w-100">品牌成本档位</th>
                                <th class="w-100">品牌预计完成档位</th>
                                <th class="w-100">商品成本档位</th>
                                <th class="w-100">商品预计完成档位</th>

                                <th class="w-100">品牌报价档位</th>
                                <th class="w-100">商品报价档位</th>
                                <th class="w-100">代采额外计算折扣档位</th>
                                <th class="w_250">计算毛利档位</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in tableData">
                                <td class="w-100">{{item.start_date}}</td>
                                <td class="w-100">{{item.end_date}}</td>
                                <td class="w-80">{{item.method_name}}</td>
                                <td class="w-80">{{item.channels_name}}</td>
                                <td class="w-100">{{item.cost_name}}</td>
                                <td class="w-100">{{item.predict_name}}</td>
                                <td class="w-100">{{item.goods_cost_name}}</td>
                                <td class="w-100">{{item.goods_predict_name}}</td>

                                <td class="w-100">{{item.offer_name}}</td>
                                <td class="w-100">{{item.goods_offer_name}}</td>
                                <td class="w-100">{{item.cut_add_name}}</td>
                                <td class="w_250">{{item.month_type_name}}</td>
                            </tr>
                        </tbody>
                    </table>
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
export default {
  components:{
      notFound,
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
      //弹框 
      options:[],
      option:[],
      dialogVisibleDTL:false,
      start_date:'',
      end_date:'',
      optList:[],
    //   month_type_id:[], 
    //   brand_month_predict_id:[],
      isThis:false,
      opt_list:[],
      //多选 
      brand_gear_points_predict:[],
      gear_type:[],
      gear_type_predict:[],
      goods_gear_type:[],
      goods_gear_type_predict:[],
      total_type:[],
      pay_type:[],

      month_type_id:[],
      brand_month_predict_id:[],
      goods_predict_id:[],
      goods_cost_id:[],
      cost_id:[],
      predict_id:[],
      pay_id:[],
      offer_id:[],
      goods_offer_id:[],
      //搜索
      startDate:'',
      endDate:'',
      channel_info:[],
      channels_id:'',
      method_info:[],
      method_id:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
    this.getChannelMethod();
  },
  methods:{
    getDataList(){
        let vm = this;
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$discountTypeLogListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&start_date="+vm.startDate+"&end_date="+vm.endDate
        +"&channels_id="+vm.channels_id+"&method_id="+vm.method_id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
                vm.isShow=false;
            }else{
                vm.isShow=true;
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
    //获取方式和渠道 
    getChannelMethod(){
        let vm = this;
        axios.get(vm.url+vm.$channelMethodListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.channel_info=res.data.data.channel_info;
            vm.method_info=res.data.data.method_info;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
        
    },
    discountTypeList(){
        let vm = this;
        vm.dialogVisibleDTL=true;
        vm.options.splice(0);
        // vm.optList.splice(0); 
        vm.opt_list.splice(0);
        vm.cost_id=[];
        vm.predict_id=[];
        vm.month_type_id=[];
        vm.brand_month_predict_id=[];
        vm.pay_id=[];
        vm.goods_cost_id=[];
        vm.goods_predict_id=[];
        vm.offer_id=[];
        vm.goods_offer_id=[];
        vm.brand_gear_points_predict=[];
        vm.gear_type=[];
        vm.gear_type_predict=[];
        vm.goods_gear_type=[];
        vm.goods_gear_type_predict=[];
        vm.total_type=[];
        vm.pay_type=[];
        vm.option=[]; 
        vm.start_date='';
        vm.end_date='';
        vm.isThis=false;
        axios.get(vm.url+vm.$methodChannelsTypeListURL+"?is_all=0",
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.optList=res.data.data;
            if(res.data.code=="1000"){
                res.data.data.forEach(purchase => {
                    let method_list=[];
                    if(purchase.channels_info.length!=0){
                        purchase.channels_info.forEach(method=>{
                            let purchase_sn=[]; 
                            // if(method.type_info!=undefined){ 
                                // method.team_info.forEach(team=>{
                                //     purchase_sn.push({"value":team.id,"label":team.team_name});
                                // })
                                // vm.optList.push({"value":team.id,"label":team.team_name,"fatherId":method.channels_id})
                                method_list.push({"value":method.channels_id,"label":method.channels_name});
                            // }
                        })
                    }
                    vm.options.push({"value":purchase.method_id,"label":purchase.method_name,"children":method_list})
                });
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    isChange(){
        let vm = this;
        let list = [];
        vm.optList.forEach(info=>{
            if(info.method_id===vm.option[0]){
                info.channels_info.forEach(channInfo=>{
                    if(channInfo.channels_id===vm.option[1]){
                        if(channInfo.brand_gear_points_predict.length!=0){
                            vm.brand_gear_points_predict=channInfo.brand_gear_points_predict;
                        }
                        if(channInfo.gear_type.length!=0){
                            vm.gear_type=channInfo.gear_type;
                        }
                        if(channInfo.gear_type_predict!=0){
                            vm.gear_type_predict=channInfo.gear_type_predict;
                        }
                        if(channInfo.goods_gear_type!=0){
                            vm.goods_gear_type=channInfo.goods_gear_type;
                        }
                        if(channInfo.goods_gear_type_predict!=0){
                            vm.goods_gear_type_predict=channInfo.goods_gear_type_predict;
                        }
                        if(channInfo.pay_type!=0){
                            vm.pay_type=channInfo.pay_type;
                        }
                        if(channInfo.total_type!=0){
                            vm.total_type=channInfo.total_type;
                            channInfo.total_type.forEach(element=>{
                                vm.month_type_id.push(element.type_id)
                            })
                        }
                    }
                })
            }
        })
    },
    doAddDiscountTypeLog(){
        let vm = this;
        if(vm.option.length!=2){
            vm.$message('请选择方式和渠道后提交！');
            return false;
        }
        if(vm.start_date===''||vm.start_date==null){
            vm.$message('请选择开始时间后提交！');
            return false;
        }
        if(vm.end_date===''||vm.end_date==null){
            vm.$message('请选择结束时间后提交！');
            return false;
        }
        if(vm.cost_id.length===0){
            vm.$message('请选择成本折扣档位后提交！');
            return false;
        }
        if(vm.predict_id.length===0){
            vm.$message('请选择预计完成档位后提交！');
            return false;
        }
        if(vm.month_type_id.length===0){
            vm.$message('请选择当月计算毛利档位后提交！');
            return false;
        }
        // if(vm.brand_month_predict_id.length===0){
        //     vm.$message('请选择品牌活动预计完成档位后提交！');
        //     return false; 
        // }
        vm.dialogVisibleDTL=false;
        let cost_id = '';
        vm.cost_id.forEach(element=>{
            cost_id+=element+",";
        })
        cost_id=(cost_id.slice(cost_id.length-1)==',')?cost_id.slice(0,-1):cost_id;
        
        let predict_id = '';
        vm.predict_id.forEach(element=>{
            predict_id+=element+",";
        })
        predict_id=(predict_id.slice(predict_id.length-1)==',')?predict_id.slice(0,-1):predict_id;

        let month_type_id = '';
        vm.month_type_id.forEach(element=>{
            month_type_id+=element+",";
        })
        month_type_id=(month_type_id.slice(month_type_id.length-1)==',')?month_type_id.slice(0,-1):month_type_id;

        let brand_month_predict_id = '';
        vm.brand_month_predict_id.forEach(element=>{
            brand_month_predict_id+=element+",";
        })
        brand_month_predict_id=(brand_month_predict_id.slice(brand_month_predict_id.length-1)==',')?brand_month_predict_id.slice(0,-1):brand_month_predict_id;

        let goods_predict_id = '';
        vm.goods_predict_id.forEach(element=>{
            goods_predict_id+=element+",";
        })
        goods_predict_id=(goods_predict_id.slice(goods_predict_id.length-1)==',')?goods_predict_id.slice(0,-1):goods_predict_id;

        let pay_id = '';
        vm.pay_id.forEach(element=>{
            pay_id+=element+",";
        })
        pay_id=(pay_id.slice(pay_id.length-1)==',')?pay_id.slice(0,-1):pay_id;

        let goods_cost_id = '';
        vm.goods_cost_id.forEach(element=>{
            goods_cost_id+=element+",";
        })
        goods_cost_id=(goods_cost_id.slice(goods_cost_id.length-1)==',')?goods_cost_id.slice(0,-1):goods_cost_id;

        let offer_id = '';
        vm.offer_id.forEach(element=>{
            offer_id+=element+",";
        })
        offer_id=(offer_id.slice(offer_id.length-1)==',')?offer_id.slice(0,-1):offer_id;

        let goods_offer_id = '';
        vm.goods_offer_id.forEach(element=>{
            goods_offer_id+=element+",";
        })
        goods_offer_id=(goods_offer_id.slice(goods_offer_id.length-1)==',')?goods_offer_id.slice(0,-1):goods_offer_id;
        axios.post(vm.url+vm.$doAddDiscountTypeLogURL,
            {
                "method_id":vm.option[0],
                "channels_id":vm.option[1],
                "start_date":vm.start_date,
                "end_date":vm.end_date,
                "cost_id":cost_id,
                "predict_id":predict_id,
                "month_type_id":month_type_id,
                "brand_month_predict_id":brand_month_predict_id,
                "goods_cost_id":goods_cost_id,
                "goods_predict_id":goods_predict_id,
                "pay_id":pay_id,
                "offer_id":offer_id,
                "goods_offer_id":goods_offer_id,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code=='1000'){
                vm.getDataList();
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
        vm.pagesize=val
        vm.getDataList()
    },
    handleCurrentChange(val) {
        let vm=this;
        vm.page=val
        vm.getDataList()
    },
  }
}
</script>

<style>
.weihuzhekou {
    height: 90px;
    line-height: 59px;
}
.el-checkbox+.el-checkbox {
    margin-right: 30px;
    margin-left: 0px !important;
    line-height: 39px;
}
.el-checkbox {
    margin-right: 30px;
}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>
