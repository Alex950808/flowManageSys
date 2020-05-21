<template>
  <div class="discountTypeList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                        <span><span class="coarseLine MR_ten"></span>折扣类型列表</span>
                        <span class="bgButton floatRight MT_twenty MR_twenty" @click="addDiscountType()">新增折扣类型</span>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>折扣类型列表</span></div></el-col>
                      <el-col :span="2"><div>方式名称</div></el-col>
                      <el-col :span="3" >
                          <template>
                            <el-select v-model="method_id" clearable placeholder="请选择方式名称">
                              <el-option v-for="item in methodsNames" :key="item.value" :label="item.channelName" :value="item.channelId">
                              </el-option>
                            </el-select>
                          </template>
                      </el-col>
                      <el-col :span="2"><div>渠道名称</div></el-col>
                      <el-col :span="3">
                          <div>
                            <template>
                                <el-select v-model="channel" placeholder="选择渠道名称">
                                    <el-option v-for="item in channels" :key="item.value" :label="item.label" :value="item.channel">
                                    </el-option>
                                </el-select>
                            </template>
                          </div>
                      </el-col>
                      <el-col :span="3"><div>折扣类型名称</div></el-col>
                      <el-col :span="3"><div><el-input v-model="type_name" placeholder="请输入折扣类型名称"></el-input></div></el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                      <el-col :span="3"><span class="bgButton" @click="addDiscountType()">新增折扣类型</span></el-col>
                    </el-row>
                    <!-- <el-row class="MB_ten fontRight">
                      <el-col :span="24"></el-col>
                    </el-row> -->
                    <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>折扣类型名称</th>
                                <th>渠道名称</th>
                                <th>方式名称</th>
                                <th>计算方式</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>{{item.type_name}}</td>
                                <td>{{item.channels_name}}</td>
                                <td>{{item.method_name}}</td>
                                <td>
                                    <span v-if="item.add_type==1">折扣替换</span>
                                    <span v-else-if="item.add_type==2">折扣累减</span>
                                    <span v-else-if="item.add_type==3">折扣取反</span>
                                    <span v-else-if="item.add_type==4">折扣累加</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                      :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                    <el-dialog title="新增折扣类型" :visible.sync="addDialogVisible" width="800px">
                        <el-row class="MB_twenty MT_twenty">
                            <el-col :span="4" :offset="1"><div class="MT_ten"><span class="redFont">*</span>渠道方式名称：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="channelsName" v-validate="'required'" name="channelName" placeholder="请选择渠道">
                                            <el-option v-for="item in channelsNames" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                    <span v-show="errors.has('channelName')" class="text-style redFont" v-cloak> {{ errors.first('channelName') }} </span>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="MT_ten">折扣名称：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="typeName" v-validate="'required'" name="discountName" placeholder="请输入折扣名称"></el-input>
                                    <span v-show="errors.has('discountName')" class="text-style redFont" v-cloak> {{ errors.first('discountName') }} </span>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty MT_twenty">
                            <el-col :span="4" :offset="1"><div class="MT_ten">区间下限(万)：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="min" type="Number" placeholder="请输入区间下限"></el-input>
                                    <!-- <span v-show="errors.has('min')" class="text-style redFont" v-cloak> {{ errors.first('min') }} </span> -->
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="MT_ten">区间上限(万)：</div></el-col>
                            <el-col :span="6"> 
                                <div>
                                    <el-input v-model="max" type="Number" placeholder="请输入区间上限"></el-input>
                                    <!-- <span v-show="errors.has('max')" class="text-style redFont" v-cloak> {{ errors.first('max') }} </span> -->
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty MT_twenty">
                            <el-col :span="4" :offset="1"><div class="MT_ten"><span class="redFont">*</span>折扣种类：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="type_cat" v-validate="'required'" name="type_cat" placeholder="请选择折扣种类">
                                            <el-option v-for="item in type_catS" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                    <span v-show="errors.has('type_cat')" class="text-style redFont" v-cloak> {{ errors.first('type_cat') }} </span>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="MT_ten"><span class="redFont">*</span>计算方式：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="add_type" v-validate="'required'" name="add_type" placeholder="请选择计算方式">
                                            <el-option v-for="item in add_typeS" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                    <span v-show="errors.has('add_type')" class="text-style redFont" v-cloak> {{ errors.first('add_type') }} </span>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="addDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="doAddDiscountType()">确 定</el-button>
                        </span>
                    </el-dialog>
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
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      //新增折扣档位
      addDialogVisible:false,
      channelsName:'',
      channelsNames:[],
      typeName:'',
      type_cat:'',
      max:'',
      min:'',
      type_catS:[],
      add_type:'',
      add_typeS:[{"name":"折扣替换","id":"1"},{"name":"折扣累减","id":"2"},{"name":"折扣取反","id":"3"},{"name":"折扣累加","id":"4"}],
      //搜索
      channel:'',
      channels:[],
      methodsNames:[],
      method_id:'',
      type_name:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.getDataList();
    this.getMethodList();
    this.getChannelData();
  },
  methods:{
    getDataList(){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        axios.get(vm.url+vm.$discountTypeListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&type_name="+vm.type_name+"&channels_id="+vm.channel+"&method_id="+vm.method_id,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data.data;
                vm.total=res.data.data.total;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.tableData=[];
                vm.isShow=true;
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            load.close();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    addDiscountType(){
        let vm= this;
        vm.channelsName='';
        vm.typeName='';
        vm.type_cat='';
        vm.add_type='';
        vm.min='';
        vm.max=''; 
        vm.addDialogVisible = true;
        if(vm.channelsNames.length!=0){
            return false;
        }
        axios.get(vm.url+vm.$addDiscountTypeURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            res.data.data.purchase_channels_list.forEach(element => {
                vm.channelsNames.push({"label":element.channels_name+'-'+element.method_name,"id":element.id+'-'+element.method_id});
            });
            res.data.data.dc_info.forEach(element=>{
                vm.type_catS.push({"name":element.cat_name,"id":element.id})
            })
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //确定新增折扣 
    doAddDiscountType(){
        let vm = this;
        this.$validator.validateAll().then(valid => {
            if(valid){
                axios.post(vm.url+vm.$doAddDiscountTypeURL,
                    {
                        "method_id":vm.channelsName.split('-')[1],
                        "channels_id":vm.channelsName.split('-')[0],
                        "type_name":vm.typeName,
                        "type_cat":vm.type_cat,
                        "add_type":vm.add_type,
                        "min":vm.min,
                        "max":vm.max,
                        "is_discount":"1",
                    },
                    {
                        headers:vm.headersStr,
                    },
                ).then(function(res){
                    vm.$message(res.data.msg);
                    vm.addDialogVisible=false;
                    if(res.data.code=='1000'){
                        vm.getDataList();
                    }
                }).catch(function (error) {
                    if(error.response.status!=''&&error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                    }
                })
            }
        })
    },
    oneSelect(index,id,type){
        let vm = this;
        let load = Loading.service({fullscreen: true, text: '拼命加载中....'});
        if(type=='is_prev_type'){
            var status = $("input[name=maoli"+index+"]").prop("checked")==true?1:0;
        }else if(type=='is_month_type'){
            var status = $("input[name=month"+index+"]").prop("checked")==true?1:0;
        }else if(type=='is_cost_type'){
            var status = $("input[name=grossProfit"+index+"]").prop("checked")==true?1:0;
        }else if(type=='is_start'){
            var status = $("input[name=discount"+index+"]").prop("checked")==true?1:0;
        }
        axios.post(vm.url+vm.$editDiscountTypeURL,
            {
                "type_id":id,
                "edit_field":type,
                "status":status,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            vm.$message(res.data.msg);
            if(res.data.code!='1000'){
                if(type=='is_prev_type'){
                    var status = $("input[name=maoli"+index+"]").prop("checked")==true?$("input[name=maoli"+index+"]").prop("checked",false):$("input[name=maoli"+index+"]").prop("checked",true);
                }else if(type=='is_month_type'){
                    var status = $("input[name=month"+index+"]").prop("checked")==true?$("input[name=month"+index+"]").prop("checked",false):$("input[name=month"+index+"]").prop("checked",true);
                }else if(type=='is_cost_type'){
                    var status = $("input[name=grossProfit"+index+"]").prop("checked")==true?$("input[name=grossProfit"+index+"]").prop("checked",false):$("input[name=grossProfit"+index+"]").prop("checked",true);
                }else if(type=='is_start'){
                    var status = $("input[name=discount"+index+"]").prop("checked")==true?$("input[name=discount"+index+"]").prop("checked",false):$("input[name=discount"+index+"]").prop("checked",true);
                }
            }
        }).catch(function (error) {
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
            console.log(vm.methodsNames)
        }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //获取采购渠道列表
    getChannelData(){
        let vm=this;
        // vm.channels.splice(0);
        axios.get(vm.url+vm.$channelsListURL,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                res.data.data.data.forEach(element => {
                    vm.channels.push({"label":element.channels_name,"channel":element.id})
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
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
