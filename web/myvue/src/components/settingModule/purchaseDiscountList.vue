<template>
  <div class="purchaseDiscountList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="tableTitleStyle" style="height:78px;">
                        <el-row>
                            <el-col :span="12">
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;采购折扣</span>
                                <searchBox @searchFrame='searchFrame'></searchBox>
                                <el-date-picker v-model="startDate" style="width:140px;" type="date" value-format="yyyy-MM-dd" placeholder="开始日期"></el-date-picker>
                                <el-date-picker v-model="endDate" style="width:140px;" type="date" value-format="yyyy-MM-dd" placeholder="结束日期"></el-date-picker>
                                <span class="bgButton ML_twenty" @click="searchDate()">&nbsp;&nbsp;&nbsp;搜索&nbsp;&nbsp;&nbsp;</span>
                            </el-col>
                            <el-col :span="12" class="fontRight">
                                <span class="bgButton MR_ten sameDayD" @click="openDisSeetBox('1')">设置折扣</span>
                                <span class="bgButton MR_ten sameDayD" @click="openUploadBox('2')">维护折扣</span>
                                
                                <span class="bgButton MR_ten" @click="downloadDisTable()">导出采购折扣</span>
                            </el-col>
                        </el-row>
                    </div>
                    <el-dialog title="维护折扣" :visible.sync="dialogVisibleUp" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>开始时间：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-date-picker style="width:190px;" v-model="start_date" type="date" value-format="yyyy-MM-dd" placeholder="请选择开始时间"></el-date-picker>
                                </div>
                            </el-col>
                            <span class="notBgButton" style="position: absolute;right: 116px;top: 129px;" @click="d_download()">采购档位折扣模板表下载</span>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>上传文件：</div></el-col>
                            <el-col :span="6" style="float: right;margin-right: 84px;">
                                <div>
                                    <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                        <div class="file">
                                        <img src="../../image/upload.png"/>
                                        <span v-if="fileName==''">点击上传文件</span>
                                        <span v-if="fileName!=''">{{fileName}}</span>
                                        <input class="w_h_ratio" style="z-index: 9999;" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                        </div>
                                    </form>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>结束时间：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-date-picker style="width:190px;" v-model="end_date" type="date" value-format="yyyy-MM-dd" placeholder="请选择结束时间"></el-date-picker>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>折扣类型名称：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-cascader :options="options" :props="props" collapse-tags clearable v-model="option"></el-cascader>
                            </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleUp = false;">取 消</el-button>
                            <el-button type="primary" @click="uploadDiscountType()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-dialog title="设置折扣" :visible.sync="dialogVisibleSetgDis" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>折扣分类：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="discount_cat" placeholder="请选择折扣类型名称">
                                            <el-option v-for="item in discount_cats" :key="item.catId" :label="item.catName" :value="item.catId"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>折扣类型：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-cascader :options="options" v-model="option">
                                </el-cascader>
                            </div>
                            </el-col>
                        </el-row>
                        <!-- <el-row v-if="isChengben" class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>设置单位：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="type_cat" placeholder="请选择折扣类型名称">
                                            <el-option v-for="item in type_cats" :key="item.id" :label="item.name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row> -->
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleSetgDis = false;">取 消</el-button>
                            <el-button type="primary" @click="settingDisType()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <div class="t_i MB_twenty" v-if="!isShow">
                        <div class="t_i_h" id="hh">
                            <div class="ee">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <thead>
                                        <tr>
                                            <th class="widthTwoHundred">品牌</th>
                                            <th class="widthTwoHundred">渠道</th>
                                            <th class="widthOneHundred">方式</th>
                                            <th class="widthTwoHundred">折扣类型</th>
                                            <th class="widthTwoHundred">折扣种类</th>
                                            <th class="widthOneHundred">折扣</th>
                                            <th class="widthTwoHundred">是否为成本折扣</th>
                                            <th class="widthTwoHundred">开始时间</th>
                                            <th class="widthTwoHundred">结束时间</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="cc" id="cc">
                            <table class="fontCenter w_ratio" border="0" cellspacing="0" cellpadding="0" v-for="item in tableData">
                                <tr v-for="(discountInfo,index) in item.discount_info">
                                    <td v-if="index==0" :rowspan="item.discount_info.length" class="widthTwoHundred overOneLinesHeid">
                                        <span style="-webkit-box-orient: vertical;">{{item.brand_name}}</span>
                                    </td>
                                    <td class="widthTwoHundred">{{discountInfo.channels_name}}</td>
                                    <td class="widthOneHundred">{{discountInfo.method_name}}</td>
                                    <td class="widthTwoHundred">{{discountInfo.type_name}}</td>
                                    <td class="widthTwoHundred">{{discountInfo.cat_name}}</td>
                                    <td class="widthOneHundred">{{discountInfo.discount}}</td>
                                    <td class="widthTwoHundred">{{discountInfo.is_start}}</td>
                                    <td class="widthTwoHundred">{{discountInfo.start_date}}</td>
                                    <td class="widthTwoHundred">{{discountInfo.end_date}}</td> 
                                </tr>
                            </table>
                        </div>
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
import searchBox from '../UiAssemblyList/searchBox';
import { monthToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile 
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      notFound,
      searchBox
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      headersStr:'',
      search:'',
      tableData:[],
      isShow:false,
      purchaseHead:[],
      purchaseText:[],
      discountList:[],
      props: { multiple: true },
      //上传折扣类型对应的折扣 
      dialogVisibleUp:false,
      fileName:'',
      type_id:'',
      discount_cat:'',
      discount_cats:[{"catName":"成本折扣","catId":"1"},{"catName":"VIP折扣","catId":"2"},{"catName":"exw折扣","catId":"3"}],
      options:[],
      option:[],
      type:'',
      settingDis:'',
      dialogVisibleSetgDis:false,
      start_date:'',
      end_date:'',
      type_cats:[{"name":"品牌","id":"1"},{"name":"商品","id":"2"}],
      type_cat:'',
      isChengben:false,
      is_exw:'',
    //   typeList:[],
      typeInfo:'',
      //按月份搜索
      startDate:monthToStr(new Date())+'-01',
      endDate:monthToStr(new Date())+'-'+new Date(new Date().getFullYear(), new Date().getMonth()+1, 0).getDate(),
      Index:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList(monthToStr(new Date())+'-01',monthToStr(new Date())+'-'+new Date(new Date().getFullYear(), new Date().getMonth()+1, 0).getDate());
    // this.discountTypeList(); 
  },
  methods:{
    getDataList(startDate,endDate){
        let vm = this;
        let startTime = startDate;
        let endTime = endDate;
        axios.get(vm.url+vm.$discountTotalListURL+"?query_sn="+vm.search+"&start_date="+startTime+"&end_date="+endTime,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData = res.data.data;
                vm.isShow=false;
            }else if(res.data.code==1002){
                vm.tableData = [];
                vm.isShow=true;
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
    downloadDisTable(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        let tmpWin=window.open(vm.url+vm.$discountExportURL+"?token="+headersToken);
    },
    tableWidth(){
        let vm = this;
        let tdLength = vm.purchaseHead.length*150+200;
        let tablewigth = tdLength;
        return "width:"+tablewigth+"px";
    },
    //搜索框 
    searchFrame(e){
        let vm=this;
        vm.search=e;
        vm.getDataList(monthToStr(new Date())+'-01',monthToStr(new Date())+'-'+new Date(new Date().getFullYear(), new Date().getMonth()+1, 0).getDate());
    },
    discountTypeList(){
        let vm = this;
        vm.options.splice(0);
        vm.option=[];
        let all;
        if(vm.Index=='1'){//设置折扣
            all="&is_all=0"
        }else if(vm.Index=='2'){//维护折扣
            all="&is_all=1"
        }
        axios.get(vm.url+vm.$methodChannelsTypeListURL+"?discount_type=1"+all,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            $(".sameDayD").removeClass("G_B_G");
            $(".sameDayD").removeClass("disable");
            if(res.data.code=="1000"){
                if(vm.Index=='1'){
                    vm.dialogVisibleSetgDis=true;
                }else if(vm.Index=='2'){
                    vm.dialogVisibleUp = true;
                }
                res.data.data.forEach(purchase => {
                    let method_list=[];
                    if(purchase.channels_info.length!=0){
                        purchase.channels_info.forEach(method=>{
                            let purchase_sn=[];
                            if(method.total_type!=undefined){
                                method.total_type.forEach(channel=>{
                                    purchase_sn.push({"value":channel.type_id,"label":channel.type_name});
                                })
                                method_list.push({"value":method.channels_id,"label":method.channels_name,"children":purchase_sn});
                            }
                        })
                    }
                    vm.options.push({"value":purchase.method_id,"label":purchase.method_name,"children":method_list})
                });
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
    openUploadBox(index){
        let vm=this;
        $(event.target).addClass("G_B_G");
        $(event.target).addClass("disable");
        vm.type='';
        vm.Index=index;
        vm.option=[];
        vm.options.splice(0);
        vm.start_date='';
        vm.end_date='';
        // vm.is_exw="";
        vm.discountTypeList();
    },
    openDisSeetBox(index){
        let vm = this;
        $(event.target).addClass("G_B_G");
        $(event.target).addClass("disable");
        vm.Index=index;
        vm.options.splice(0);
        vm.option=[];
        vm.discount_cat = '';
        vm.discountTypeList()
    },
    //维护成本折扣或者VIP折扣 
    settingDisType(){
        let vm = this;
        vm.dialogVisibleSetgDis=false;
        axios.post(vm.url+vm.$discountTypeSettingURL,
            {
                "type_id":vm.option[2].split(",")[0],
                "type_cat":vm.option[2].split(",")[1],
                "channels_id":vm.option[1],
                "method_id":vm.option[0],
                "set_type":vm.discount_cat,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=="1000"){
                vm.$message(res.data.msg);
                vm.getDataList(vm.startDate,vm.endDate);
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    //下载模板表格 
    d_download(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.downloadUrl+'/采购档位折扣上传.xls');
    },
    //选择表格 
    SelectFile(){
        let vm=this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    uploadDiscountType(){
        let vm = this;
        var formDate = new FormData($("#forms")[0]);
        console.log(vm.option)
        if(vm.fileName==''){
            vm.$message('请选择文件后上传！');
            return false;
        }
        if(vm.option.length==0){
            vm.$message('折扣类型名称不能为空!');
            return false;
        }
        if(vm.start_date==''){
            vm.$message('开始时间不能为空!');
            return false;
        }
        if(vm.end_date==''){
            vm.$message('结束时间不能为空!');
            return false;
        }
        let type_id = '';
        vm.option.forEach(element=>{
            type_id+=element[2]+",";
        })
        type_id=(type_id.slice(type_id.length-1)==',')?type_id.slice(0,-1):type_id;
        vm.dialogVisibleUp=false;
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        $.ajax({
        url: vm.url+vm.$doUploadDiscountTypeURL+"?type_id="+type_id+"&start_date="+vm.start_date+"&end_date="+vm.end_date,
        type: "POST",
        // async: false,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            if(res.code=='1000'){
                vm.getDataList(vm.startDate,vm.endDate);
                $("#file1").val('');
                vm.fileName='';
                vm.$message(res.msg);
            }else{
                vm.$message(res.msg);
                $("#file1").val('');
                vm.fileName='';
            }
        }
        }).catch(function (error) {
            loa.close();
            if(error.response.status!=''&&error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
    },
    //
    searchDate(){
        let vm = this;
        if(vm.startDate==null){
            vm.startDate='';
        }
        if(vm.endDate==null){
            vm.endDate='';
        }
        vm.getDataList(vm.startDate,vm.endDate);
    },
    // isChange(){
    //     let vm = this;
    //     if(vm.discount_cat==='1'){
    //         vm.isChengben=true;
    //     }else{
    //         vm.isChengben=false;
    //     }
    // }
  }
}
</script>
<style>
.purchaseDiscountList .t_i{width:100%; height:auto; border-right:1px solid #ccc;border-left: 1px solid #ccc;border-top:1px solid #ccc}
.purchaseDiscountList .t_i_h{width:100%; overflow-x:hidden;}
.purchaseDiscountList .ee{width:100%!important; width:100%; text-align: center;background: buttonface;}
.purchaseDiscountList .t_i_h table{width:100%;border-top: #ccc;border-left: #ccc;border-right: #ccc;border-bottom: #ccc;}
.purchaseDiscountList .t_i_h table td{ border-bottom:1px solid #ccc; height:20px; text-align:center}
.purchaseDiscountList .cc{width:100.6%;height: 600px; border-bottom:1px solid #ccc; overflow:auto;}
.purchaseDiscountList .cc table{width:100%;border-top: #ccc;border-left: #ccc;border-right: #ccc; }
.purchaseDiscountList .cc table td{height:25px; border-bottom:1px solid #ccc; text-align:center}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>
