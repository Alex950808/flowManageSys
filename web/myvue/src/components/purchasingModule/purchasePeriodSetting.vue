<template>
  <div class="purchasePeriodSetting_b">
    <!-- 采购期设置页面 -->
    <el-col :span="24" style="background-color: #fff;">
        <div class="purchasePeriodSetting bgDiv">
            <el-row>
                <el-col :span="22" :offset="1">
                    <div class="title listTitleStyle">
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购期设置</span>
                        <searchBox @searchFrame='searchFrame'></searchBox>
                        <span class="dialogVisible">
                            <span v-if="$store.state.clickStutes==true" class="bgButton" @click="addNewPurchase(1)">新增采购期</span>
                            <span v-if="$store.state.clickStutes==false" class="grBgButton">新增采购期</span>
                        </span>
                        <el-dialog title="新增采购期" :visible.sync="dialogVisible" width="800px">
                            <!-- <div class="Popup_title">新增采购期</div> -->
                            <el-row>
                                <el-col :span="11" :offset="1">
                                    <div class="Popup_left">
                                        <el-row>
                                            <el-col :span="6"><div >提货队</div></el-col>
                                            <el-col :span="15"><div><input class="deliveryTeam" v-model="delivery_team" placeholder="请输入内容"/></div></el-col>
                                        </el-row>
                                        <el-row>
                                            <el-col :span="6"><div>人数</div></el-col>
                                            <el-col :span="15"><div><input class="deliveryTeam" v-model="delivery_pop_num" placeholder="请输入内容"/></div></el-col>
                                        </el-row>
                                        
                                    </div>
                                </el-col>
                                <el-col :span="10" :offset="1">
                                    <div class="Popup_right">
                                        <el-row class="addDate">
                                            <el-col :span="8"><div >开始日期</div></el-col>
                                            <el-col :span="15" :offset="1"><div>
                                                <template>
                                                    <el-date-picker v-model="start_time" class="start_time" @change="startDate()" type="date" placeholder="选择日期"></el-date-picker>
                                                </template>
                                                </div></el-col>
                                        </el-row>
                                        <!-- <el-row class="addDate">
                                            <el-col :span="8"><div >提报需求结束日</div></el-col>
                                            <el-col :span="15" :offset="1">
                                                <div>
                                                    <template>
                                                        <el-date-picker v-model="end_time" type="date" placeholder="选择日期"></el-date-picker>
                                                    </template>
                                                </div>
                                            </el-col>
                                        </el-row> -->
                                        <el-row class="addDate">
                                            <el-col :span="8"><div>
                                                到货日期
                                                </div></el-col>
                                            <el-col :span="15" :offset="1">
                                                <div>
                                                    <template>
                                                        <el-date-picker v-model="delivery_time" class="start_time" type="date" placeholder="选择日期"></el-date-picker>
                                                    </template>
                                                </div>
                                            </el-col>
                                        </el-row>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row>
                                <el-col :span="3" :offset="1">
                                    <div class="channel_left">
                                        采购方式
                                    </div>
                                </el-col>
                                <el-col :span="20">
                                    <template>
                                        <el-checkbox :indeterminate="isIndeterminateT" v-model="pattern" @change="handlepatternAllChange">全选</el-checkbox>
                                        <el-checkbox-group style=" line-height:30px;" v-model="pattern_list" @change="handlepatternAllCitiesChange;getChannelBymethod()">
                                            <el-checkbox v-for="city in patterns" :label="city.method_id" :value="city.method_id" :key="city.method_id">{{city.method_name}}</el-checkbox>
                                        </el-checkbox-group>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row>
                                <el-col :span="3" :offset="1">
                                    <div class="channel_left">
                                        采购渠道
                                    </div>
                                </el-col>
                                <el-col :span="20">
                                    <template>
                                        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                        <el-checkbox-group style=" line-height:30px;" v-model="channels_list" @change="handleCheckedCitiesChange;getMethodByChannel()">
                                            <el-checkbox v-for="city in cities" :label="city" :key="city">{{city}}</el-checkbox>
                                        </el-checkbox-group>
                                    </template>
                                </el-col>
                            </el-row>
                            <el-row>
                                <el-col :span="3" :offset="1">
                                    <div class="channel_left">
                                        采购通告
                                    </div>
                                </el-col>
                                <el-col :span="20">
                                    <el-input type="textarea" :rows="3" placeholder="请输入内容" v-model="textDomain">
                                    </el-input>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false;cancel()">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;getUserData()">确 定</el-button>
                            </span>
                        </el-dialog>
                    </div>
                </el-col>
            </el-row>
            <ul>
                <li v-for="item in PurchaseList">
                    <el-row class="content">
                        <el-col :span="16" :offset="1">
                            <div class="content_left">
                                <div class="content_text content_title">
                                    <span class="stage">{{item.id}}期</span>
                                    <span class="ML_twenty stage">{{item.delivery_time}}</span>
                                    <span class="number">{{item.purchase_sn}}</span>
                                    <!-- <el-button class="operation" type="text" @click="dialogVisibleT = true;purchasingEditors(item.purchase_sn);">编辑</el-button> -->
                                    <i class="el-icon-edit-outline editBut" title="编辑采购期" @click="dialogVisibleT = true;purchasingEditors(item.purchase_sn);"></i>
                                    
                                    <span v-if="item.status==1" class="status">准备中</span>
                                    <span v-if="item.status==2" class="status">进行中</span>
                                    <span v-if="item.status==3" class="status">已关闭</span>
                                    <span v-if="item.status==4" class="status">已失效</span>
                                </div>
                                <el-row class="content_text">
                                    <el-col :span="22" :offset="2" class="overTwoLinesHeid" style="height: 95px;">
                                        <span v-for="(itemD) in item.channels_list" style="display:inline-block;height: 44px;line-height: 44px;">
                                            {{itemD}}
                                            &nbsp;&nbsp;&nbsp;</span>
                                    </el-col>
                                </el-row>
                                <el-row class="content_text">
                                    <el-col :span="7" :offset="2"><span class="start">开始</span><span class="time">{{item.start_time}}</span></el-col>
                                    <el-col :span="7"><span class="end">结束</span><span class="time">{{item.end_time}}</span></el-col>
                                    <el-col :span="7"><i class="el-icon-star-on"></i><span class="name">{{item.delivery_team}}</span><span class="popNumber"> {{item.delivery_pop_num}}人</span></el-col>
                                </el-row>
                                <!-- <div class="content_text"> -->
                                    <span class="closingPeriod bgButton" @click="closingPeriod(item.purchase_sn)">关闭采购期</span>
                                <!-- </div> -->
                            </div>
                        </el-col>
                        <!-- <el-col :span="6"> -->
                            <div class="content_right">
                                <div class="content_text">
                                    <span>提货日：</span><span>{{item.delivery_time}}</span>
                                </div>
                                <div class="content_text">
                                    <span>通告备注:</span>
                                </div>
                                <div class="content_text ellipsis" :title="item.purchase_notice">
                                    <span style="-webkit-box-orient: vertical;">{{item.purchase_notice}}</span>
                                </div>
                            </div>
                        <!-- </el-col> -->
                    </el-row>
                </li>
                <li v-if="isShow" style="text-align: center"><img class="notData" src="../../image/notData.png"/></li>
            </ul>
            <el-dialog title="编辑采购期" :visible.sync="dialogVisibleT" width="800px">
                <el-row>
                    <el-col :span="10" :offset="1">
                        <div class="Popup_left">
                            <el-row class="rowHeight">
                                <el-col class="lineHeightForty" :span="6"><div>提货队</div></el-col>
                                <el-col :span="18"><div><el-input style="width:220px;" class="deliveryTeam" v-model="deliveryTeam" placeholder="请输入内容"></el-input></div></el-col>
                            </el-row>
                            <el-row class="rowHeight">
                                <el-col class="lineHeightForty" :span="6"><div>
                                    人数
                                    </div></el-col>
                                <el-col :span="18"><div>
                                    <el-input style="width:220px;" class="deliveryTeam" v-model="deliveryPopNum" placeholder="请输入内容"></el-input>
                                    </div></el-col>
                            </el-row>
                            
                        </div>
                    </el-col>
                    <el-col :span="10" :offset="2">
                        <div class="Popup_right">
                            <el-row class="rowHeight">
                                <el-col class="lineHeightForty" :span="8"><div>开始时间</div></el-col>
                                <el-col :span="15" :offset="1"><div>
                                    <template>
                                        <el-date-picker v-model="startTime" type="date" @change="startDate()" placeholder="选择日期"></el-date-picker>
                                    </template>
                                    </div></el-col>
                            </el-row>
                            <el-row class="rowHeight">
                                <el-col class="lineHeightForty" :span="8">
                                    <div>
                                    到货日期
                                    </div>
                                </el-col>
                                <el-col :span="15" :offset="1">
                                    <template>
                                        <el-date-picker v-model="deliveryTime" type="date" placeholder="选择日期"></el-date-picker>
                                    </template>
                                </el-col>
                            </el-row>
                            <!-- <el-row class="addDate">
                                <el-col class="lineHeightForty" :span="8"><div >提报需求结束日</div></el-col>
                                <el-col :span="13" :offset="1">
                                    <div>
                                        <template>
                                            <el-date-picker v-model="endTime" type="date" placeholder="选择日期"></el-date-picker>
                                        </template>
                                    </div>
                                </el-col>
                            </el-row> -->
                        </div>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3" :offset="1">
                        <div class="channel_left lineHeightForty">
                            采购方式
                        </div>
                    </el-col>
                    <el-col :span="20">
                        <template>
                            <el-checkbox :indeterminate="isIndeterminateFour" v-model="patternAllTwo" @change="handlepatternAllTwoChange">全选</el-checkbox>
                            
                            <el-checkbox-group style=" line-height:30px;" v-model="patternList" @change="handlepatternAllTwoCitiesChange;editGetChannelBymethod()">
                                <el-checkbox v-for="city in patternsTwo" :value="city.method_id" :label="city.method_id" :key="city.method_id">{{city.method_name}}</el-checkbox>
                            </el-checkbox-group>
                        </template>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3" :offset="1">
                        <div class="channel_left lineHeightForty">
                            采购渠道
                        </div>
                    </el-col>
                    <el-col :span="20">
                    <template>
                            <el-checkbox :indeterminate="isIndeterminateThree" v-model="checkAllTwo" @change="handleCheckAllChangeT">全选</el-checkbox>
                            <el-checkbox-group style=" line-height:30px;" v-model="channelsList" @change="handleCheckedCitiesChangeT">
                                <el-checkbox v-for="city in citiesTwo" :label="city" :key="city">{{city}}</el-checkbox>
                            </el-checkbox-group>
                        </template>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="3" :offset="1">
                        <div class="channel_left lineHeightForty">
                            采购通告
                        </div>
                    </el-col>
                    <el-col :span="20">
                        <el-input type="textarea" :rows="8" placeholder="请输入内容" v-model="text_domain">
                        </el-input>
                    </el-col>
                </el-row>
                <span slot="footer" class="dialog-footer">
                    <el-button type="info" @click="dialogVisibleT = false;cancel()">取 消</el-button>
                    <el-button type="primary" @click="confirmShow()">确定编辑</el-button>
                </span>
            </el-dialog>
            <el-col :span="22" :offset="1">
                <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                    :page-sizes="[15, 25, 35, 45]" :page-size="5" layout="total, sizes, prev, pager, next, jumper" :total="total">
                </el-pagination>
            </el-col>
        </div>
    </el-col>
    <!-- </el-row> -->
    <div class="confirmPopup_b" v-if="show">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要修改？
        <div class="confirm"><el-button  @click="determineEditor()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import router from '../../router'
import comment from '../../filters/comment'
import { Loading } from 'element-ui';
import searchBox from '../UiAssemblyList/searchBox';
export default {
    components:{
        searchBox,
    },
     data() {
      return {
          url: `${this.$baseUrl}`,
          //多选
        checkAll: false,
        channels_list: [],//选中的数据
        channelsList:[],//编辑时选中的数据
        cities: [],//定义从后台获取的数据
        checkedCities:[],
        isIndeterminate: true,
        isIndeterminateT:true,
        //新增采购期
        dialogVisible: false,
        dialogVisibleT:false,
         pickerOptions1: {
          disabledDate(time) {
            return time.getTime() > Date.now();
          },
          shortcuts: [{
            text: '今天',
            onClick(picker) {
              picker.$emit('pick', new Date());
            }
          }, {
            text: '昨天',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() - 3600 * 1000 * 24);
              picker.$emit('pick', date);
            }
          }, {
            text: '一周前',
            onClick(picker) {
              const date = new Date();
              date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', date);
            }
          }]
        },
        //时间选择器
        start_time: '',//开始时间
        delivery_time: '',//提货日期
        //输入框
        delivery_team: '',//提货队
        delivery_pop_num:'',//人数
        portTime:'',//预计到港时间
        flight_sn:'',//航班班次
        textDomain:'',//文本域
        PurchaseList:'',//正在进行的采购期列表
        channelData:'',//从后台获取到的所有采购渠道
        end_time:'',
        //以下为编辑的数据
        startTime:'',
        deliveryTeam:'',
        flightSn:'',
        deliveryTime:'',
        deliveryPopNum:'',
        rememberpurchase_sn:'',//编辑时要记住的sn
        text_domain:'',//编辑时要记住的通告
        citiesTwo:[],//编辑时的全选类目
        checkedCitiesTwo:[],//编辑时全选记住的类目
        checkAllTwo:false,
        endTime:'',
        isIndeterminateThree:true,
        //分页
        total:0,//默认数据总数
        pagesize:15,//分页默认15页
        page:1,
        search:'',//用户输入数据
        loading:'',
        isShow:false,
        pattern:[],//新增采购期的方式
        patternTwo:[],//编辑采购期的方式
        method_list:[],
        checkAllAdd:[],
        taskTemplates:[],//任务模板列表
        taskTemplate:'',//任务模板id
        //方式多选
        patternAll:[],
        patternCities:[],
        patterns:[],
        pattern_list:[],
        isIndeterminateTwo:true,
        //编辑时的多选
        patternAllTwo:[],
        patternList:'',
        pattern_cities:[],
        patternsTwo:[],//编辑时的方式多选
        isIndeterminateFour:true,
        show:false,
        headersStr:'',
      };
    },
    mounted(){
        this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
        this.getPurchaseList();
        this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
        confirmShow(){
            let vm=this;
            vm.show=true;
            vm.dialogVisibleT = false;
        },
      handleCheckAllChange(val) {
        let vm=this;
        this.checkedCities = val ? vm.cities : [];
        if(val){
            this.checkedCities.forEach(element=>{
                this.channels_list.push(element);
            }) 
        }else{
            this.channels_list.splice(0);
        }
        this.isIndeterminate = false;
      },
      handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        this.checkAll = checkedCount === this.cities.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
      },
      handleCheckAllChangeT(val) {
        let vm=this;
        this.checkedCitiesTwo = val ? vm.citiesTwo : [];
        this.channelsList.splice(0);
        if(val){
            this.checkedCitiesTwo.forEach(element=>{
                this.channelsList.push(element);
            }) 
        }else{
            this.channelsList.splice(0);
        }
        this.isIndeterminateThree = false;
      },
      handleCheckedCitiesChangeT(value) {
        let checkedCount = value.length;
        this.checkAllTwo = checkedCount === this.citiesTwo.length;
        this.isIndeterminateThree = checkedCount > 0 && checkedCount < this.citiesTwo.length;
      },
      //方式多选
      handlepatternAllChange(val){
        let vm=this;
        this.patternCities = val ? vm.patterns : [];
        if(val){
            this.patternCities.forEach(element=>{
                this.pattern_list.push(element.method_id);
        
            }) 
        }else{
            this.pattern_list.splice(0);
        }
        this.isIndeterminateT = false;
        this.getChannelBymethod();
      },
      handlepatternAllCitiesChange(value){
        let checkedCount = value.length;
        this.patternAll = checkedCount === this.patterns.length;
        this.isIndeterminateT = checkedCount > 0 && checkedCount < this.patterns.length;
      },
      //编辑时的多选方式
      handlepatternAllTwoChange(val){
          let vm=this;
        this.pattern_cities = val ? vm.patternsTwo : [];
        if(val){
            this.pattern_cities.forEach(element=>{
                this.patternList.push(element.method_id);
            }) 
        }else{
            this.patternList.splice(0);
        }
        this.isIndeterminateFour = false;
        this.editGetChannelBymethod();
      },
      handlepatternAllTwoCitiesChange(value){
        let checkedCountTwo = value.length;
        this.patternAllTwo = checkedCountTwo === this.patternsTwo.length;
        this.isIndeterminateFour = checkedCountTwo > 0 && checkedCountTwo < this.patternsTwo.length;
      },
        dateToStr(datetime){ 

            var year = datetime.getFullYear();
            var month = datetime.getMonth()+1;//js从0开始取 
            var date = datetime.getDate(); 
            var hour = datetime.getHours(); 
            var minutes = datetime.getMinutes(); 
            var second = datetime.getSeconds();
            
            if(month<10){
            month = "0" + month;
            }
            if(date<10){
            date = "0" + date;
            }
            var time = year+"-"+month+"-"+date;
            return time;
        },
        //搜索框
        searchFrame(e){
            let vm=this;
            vm.search=e;
            vm.page=1;
            vm.getPurchaseList();
        },
        //获取采购渠道
        getChannel(){
            let vm = this;
            axios.get(vm.url+vm.$channelsListURL,
                {
                    headers:vm.headersStr,
                }
            ).then(function(res){
                vm.channelData=res;
                res.data.data.forEach(element => {
                    vm.cities.push(element.channels_name)
                    vm.citiesTwo.push(element.channels_name)
                });
                
            }).catch(function (error) {
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
        },
        //检查开始时间不能早于当天凌晨十二点
        startDate(){
            let vm = this;
            var timeStamp = new Date(new Date().setHours(0,0,0,0))
            var date = new Date(vm.dateToStr(timeStamp));
            // var time = date.getTime()+86400;//明天凌晨零点
            var time = date.getTime()-86400000;//今天凌晨零点
            var dateTwo = new Date(this.start_time);
            var timeTwo = dateTwo.getTime();
            if(vm.start_time!=''){
                if(timeTwo<time){
                    vm.start_time='';
                    vm.$message('开始日期必须大于等于当前日期！');
                }
            }
        },
        //打开新增采购期页面
        addNewPurchase(e){
            let vm = this;
            vm.$store.commit('editClickStutes');
            if(vm.cities.length!=0){
                vm.cities.splice(0)
            }
            if(vm.citiesTwo.length!=0){
                vm.citiesTwo.splice(0)
            }
            if(vm.patterns.length!=0){
                vm.patterns.splice(0)
            }
            if(vm.patternsTwo.length!=0){
                vm.patternsTwo.splice(0)
            }
            axios.get(vm.url+vm.$channelMethodListURL,
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                    vm.$store.commit('editClickStutes');
                    vm.channelData=res;
                    if(res.data.code!=1002){
                         res.data.data.channel_info.forEach(element => {
                            vm.cities.push(element.channels_name+"-"+element.method_name);
                            vm.citiesTwo.push(element.channels_name+"-"+element.method_name)
                        });
                        res.data.data.method_info.forEach(element=>{
                            vm.patterns.push({"method_id":element.id,"method_name":element.method_name});
                            vm.patternsTwo.push({"method_id":element.id,"method_name":element.method_name});
                        });
                        if(e==1){
                        vm.dialogVisible = true;
                        }
                    }else{
                        vm.$message(res.data.msg);
                        vm.dialogVisible = false;
                    }
               
            }).catch(function (error) {
                vm.$store.commit('editClickStutes');
                if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
            
        },
        //通过方法id寻找渠道
        getChannelBymethod(){
            let vm = this;
            vm.channels_list.splice(0);
            vm.channelData.data.data.channel_info.forEach(element=>{
                vm.pattern_list.forEach(elementO=>{
                    if(element.method_id==elementO){
                    vm.channels_list.push(element.channels_name+"-"+element.method_name);
                    }
                })
            })
            
        },
        //编辑时通过方法id寻找渠道
        editGetChannelBymethod(){
            let vm = this;
            vm.channelsList.splice(0);
            vm.channelData.data.data.channel_info.forEach(element=>{
                vm.patternList.forEach(elementO=>{
                    if(element.method_id==elementO){
                    vm.channelsList.push(element.channels_name+"-"+element.method_name);
                    }
                })
            })
        },
        //通过渠道找方法id
        // getMethodByChannel(){
        //     let vm = this;
        // },
      //新增采购期
      getUserData(){
          let vm = this;
          let channelId=[];
          vm.channels_list.forEach(elementO=>{
              vm.channelData.data.data.channel_info.forEach(elementT=>{
                  if(elementO==(elementT.channels_name+"-"+elementT.method_name)){
                    channelId.push(elementT.id);
                  }
              })
          })
        let start_time;
        if(this.start_time!=''){
            start_time=this.dateToStr(this.start_time);
        }
        let end_time;
        if(this.end_time!=''){
            end_time=this.dateToStr(this.end_time);
        }
        let delivery_time;
        if(this.delivery_time!=''){
            delivery_time=this.dateToStr(this.delivery_time);
        }
        //判断表单内容是否为空，为空则return false
        if(vm.delivery_team==''){
            vm.$message('提货队不能为空');
            return false;
        }
        if(vm.delivery_pop_num==''){
            vm.$message('提货人数不能为空');
            return false;
        }
        if(vm.start_time==''){
            vm.$message('开始时间不能为空');
            return false;
        }
        if(vm.delivery_time==''){
            vm.$message('提货时间不能为空');
            return false;
        }
        // if(vm.end_time==''){
        //     vm.$message('需求提报结束时间不能为空');
        //     return false;
        // }
        if(vm.pattern_list==''){
            vm.$message('采购方式不能为空');
            return false;
        }
        if(vm.channels_list==''){
            vm.$message('采购渠道不能为空');
            return false;
        }
        axios.post(vm.url+vm.$createDate,
        {
                "start_time":start_time,
                "delivery_time":delivery_time,
                "delivery_team":this.delivery_team,
                "delivery_pop_num":this.delivery_pop_num,
                "channels_list":vm.channels_list,
                "purchase_notice":this.textDomain,
                "method_info":vm.pattern_list,
                // "end_time":end_time,
                "channels_info":channelId,
        },
        {
            headers:vm.headersStr,
        },
        )
        .then(function(res){
            vm.$message(res.data.msg);
            if(res.data.code==1000){
                vm.dialogVisible = false;
                vm.getPurchaseList();
                vm.isShow=false;
                vm.start_time='';
                vm.delivery_time='';
                vm.delivery_team='';
                vm.delivery_pop_num='';
                vm.channels_list=[];
                vm.textDomain='';
                vm.pattern_list=[];
                vm.end_time='';
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
      },
      //采购期列表
      getPurchaseList(){
          let vm=this;
          axios.get(vm.url+vm.$getWaiteOrder+'?start_page='+vm.page+'&page_size='+vm.pagesize+"&query_sn="+vm.search,
            {
                headers:vm.headersStr,
            }
          ).then(function(res){
                  vm.loading.close();// 以服务的方式调用的 Loading 需要异步关闭
                vm.PurchaseList=res.data.data.data;
                vm.total=res.data.data.total;
              if(res.data.code==1000){
                    vm.isShow=false;
              }
              if(res.data.code==1002){
                  vm.isShow=true;
                //   vm.PurchaseList='';
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
      
      //打开采购期编辑页
      purchasingEditors(e){
          let vm=this;
          if(vm.patternTwo.length!=0){
                vm.patternTwo.splice(0)
            }
            if(vm.patternList.length!=0){
                vm.patternList='';
            }
            vm.addNewPurchase(2);
        axios.post(vm.url+vm.$editDate,
            {
                "purchase_sn":e
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.rememberpurchase_sn=e;
            vm.startTime=res.data.data.start_time
            vm.deliveryTeam=res.data.data.delivery_team
            vm.deliveryTime=res.data.data.delivery_time
            vm.endTime=res.data.data.end_time
            vm.deliveryPopNum=res.data.data.delivery_pop_num
            vm.text_domain=res.data.data.purchase_notice
            if(res.data.data.channels_list!=null){
                res.data.data.channels_list.forEach(element=>{
                    vm.channelsList.push(element)
                })
            }
            if(res.data.data.method_info!=null){
                vm.patternList=res.data.data.method_info;
            }
            vm.getPurchaseList();
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
      },
      //确定编辑采购期
      determineEditor(){
          let vm=this;
           let channelId=[];
          vm.channelsList.forEach(elementO=>{
              vm.channelData.data.data.channel_info.forEach(elementT=>{
                  if(elementO==(elementT.channels_name+"-"+elementT.method_name)){
                    channelId.push(elementT.id);
                  }
              })
          })
        //   if(vm.startTime.constructor==Date){
        //       vm.startTime=vm.dateToStr(vm.startTime);
        //   }else{
        //       vm.startTime=vm.startTime.split(" ")[0];
        //   }
        //   if(vm.endTime.constructor==Date){
        //      vm.endTime=vm.dateToStr(vm.endTime);
        //   }else{
        //       vm.endTime=vm.endTime.split(" ")[0];
        //   }
        //   if(vm.deliveryTime.constructor==Date){
        //      vm.deliveryTime=vm.dateToStr(vm.deliveryTime);
        //   }else{
        //       vm.deliveryTime=vm.deliveryTime.split(" ")[0];
        //   }
        if(vm.startTime!=''&&typeof(vm.startTime) == "string"){

        }else{
            vm.startTime=vm.dateToStr(vm.startTime);
        }
        if(vm.deliveryTime!=''&&typeof(vm.deliveryTime) == "string"){

        }else{
            vm.deliveryTime=vm.dateToStr(vm.deliveryTime);
        }
          axios.post(vm.url+vm.$doEditDate,
            {
                "purchase_sn":vm.rememberpurchase_sn,
                "start_time":vm.startTime,
                "end_time":vm.endTime,
                "delivery_team":this.deliveryTeam,
                "delivery_pop_num":this.deliveryPopNum,
                "delivery_time":this.deliveryTime,
                "channels_list":vm.channelsList,
                "purchase_notice":vm.text_domain,
                "method_info":vm.patternList,
                "channels_info":channelId,
            },
            {
                headers:vm.headersStr,
            }
          ).then(function(res){
            vm.$message(res.data.msg);
            vm.dialogVisibleT = false;
            vm.show=false;
            vm.isShow=false;
            vm.getPurchaseList();
            vm.rememberpurchase_sn='',
            vm.startTime='';
            vm.endTime='';
            vm.deliveryTeam='';
            vm.deliveryPopNum='';
            vm.deliveryTime='';
            vm.channelsList=[];
            vm.text_domain='';
            vm.patternList=[];
          }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
           });
      },
      //关闭采购期
      closingPeriod(purchase_sn){
          let vm=this;
          axios.get(vm.url+vm.$closePurchaseURL+"?purchase_sn="+purchase_sn,
                {
                    headers:vm.headersStr,
                }
          ).then(function(res){
              if(res.data.code=1000){
                  vm.getPurchaseList();
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
      determineIsNo(){
          let vm=this;
          vm.show=false;
          vm.dialogVisibleT = false;
      },
      //分页
      current_change(currentPage){
          this.currentPage = currentPage;
      },
      handleSizeChange(val) {
          let vm=this;
          vm.PurchaseList.splice(0)
          vm.pagesize=val
          vm.getPurchaseList()
      },
      handleCurrentChange(val) {
          let vm=this;
          vm.PurchaseList.splice(0)
          vm.page=val
          vm.getPurchaseList()
      },
      cancel(){
        let vm=this;
        vm.start_time='';
        vm.delivery_time='';
        vm.delivery_team='';
        vm.delivery_pop_num='';
        vm.channels_list=[];
        vm.textDomain='';
        vm.pattern_list=[];
        vm.end_time='';
        vm.rememberpurchase_sn='',
        vm.startTime='';
        vm.endTime='';
        vm.deliveryTeam='';
        vm.deliveryPopNum='';
        vm.deliveryTime='';
        vm.text_domain='';
        vm.patternList=[];
        vm.channelsList=[];
      }
    }
}
</script>


<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>
<style>
.textDomain{
    margin-top: 40px;
}
/* .inp_title{
    height: 42px;
    line-height: 42px;
} */
.rowHeight{
    margin-bottom: 20px;
}
.el-date-editor.el-input, .el-date-editor.el-input__inner {
    width: 100%;
}
.purchasePeriodSetting_b .ellipsis{
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
}
.purchasePeriodSetting .addDate .el-date-editor.el-input, .el-date-editor.el-input__inner {
    width: 113%;
}
.coefficient{
    width: 66%;
}
.MultiSelect{
    width: 167px;
    margin-top: 14px;
}
.cb_two{
    width: 40px;
    margin-right: 20px;
}
.purchasePeriodSetting_b .closingPeriod{
    display: inline-block;
    margin-right: 35px;
    float: right;
    margin-left: 10px;
}
.purchasePeriodSetting_b .confirmTitle{
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
.purchasePeriodSetting_b .el-icon-close{
    margin-left: 270px;
}
.el-checkbox+.el-checkbox {
    margin-right: 30px;
    margin-left: 0px;
    line-height: 39px;
}
.el-checkbox {
    margin-right: 30px;
}
.coarseLine{
    display: inline-block;
    width:5px;
    height: 20px;
    vertical-align: -3px;
    background: #113d62;
}
.content_title{
    margin-top: 0px !important;
    height: 65px;
    line-height: 65px;
    background-color: #ebeef5;
    padding-left: 35px;
}
.editBut{
    color: #3d6fbc;
    font-size: 20px;
    vertical-align: -1px;
    margin-left: 10px;
    cursor: pointer;
}
.purchasePeriodSetting_b .el-pagination {
    float: right;
}
.purchasePeriodSetting_b .content_right{
    display: inline-block;
    margin-left: 17px;
    width: 23%;
}
</style>