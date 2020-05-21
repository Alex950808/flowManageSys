<template>
  <div class="getGmcDiscountList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <!-- <div class="tableTitleStyle">
                        <el-row>
                            <el-col :span="12"> 
                                <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;商品追加折扣列表</span>
                                <searchBox @searchFrame='searchFrame'></searchBox>
                            </el-col>
                            <el-col :span="12" class="fontRight">
                                <span class="bgButton MR_ten" @click="exportGoodsOffe('dl')">下载商品报价文件</span>
                                <span class="bgButton MR_ten" @click="exportGoodsOffe('ge')">生成商品报价文件</span>
                                <span class="bgButton MR_ten" @click="discountTypeList();clean()">商品渠道追加折扣维护</span>
                            </el-col>
                        </el-row>
                    </div> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>商品追加折扣列表</span></div></el-col>
                        <el-col :span="2"><div>结算开始时间</div></el-col>
                        <el-col :span="3" >
                            <div>
                                <el-date-picker class="w_ratio" v-model="startDate" value-format="yyyy-MM-dd" type="date" placeholder="请选择结算开始日期"></el-date-picker>
                            </div>
                        </el-col>
                        <el-col :span="2"><div>结算结束时间</div></el-col>
                        <el-col :span="3">
                            <div>
                                <el-date-picker class="w_ratio" v-model="endDate" value-format="yyyy-MM-dd" type="date" placeholder="请选择结算开始日期"></el-date-picker>
                            </div>
                        </el-col>
                        <el-col :span="2"><div>商品名称</div></el-col>
                        <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                        <el-col :span="2"><div>商品代码</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="erp_prd_no" placeholder="请输入商品代码"></el-input></div></el-col>
                        <!-- <el-col :span="3"><i class="el-icon-d-caret Cursor" title="点击展开更多搜索"></i></el-col> -->
                    </el-row>
                    <el-row class="fontCenter MB_ten lineHeightForty">
                        <el-col :span="2" :offset="3"><div>商品规格码</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                        <el-col :span="2"><div>商家编码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                        <el-col :span="2"><div>参考码</div></el-col>
                        <el-col :span="3"><div><el-input v-model="erp_ref_no" placeholder="请输入参考码"></el-input></div></el-col>
                        <el-col :span="2"><div>渠道</div></el-col>
                        <el-col :span="3">
                            <div>
                                <template>
                                    <el-select v-model="channels_id" clearable placeholder="请选择渠道">
                                        <el-option v-for="item in buyChannel" :key="item.id" :label="item.channels_name" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                        </el-col>
                    </el-row>
                    <el-row class="MB_ten">
                        <el-col :span="24">
                            <div class="fontRight">
                                <span class="bgButton MR_ten" @click="getDataList()">搜索</span>
                                <span class="bgButton MR_ten" @click="exportGoodsOffe('dl')">下载商品报价文件</span>
                                <span class="bgButton MR_ten" @click="exportGoodsOffe('ge')">生成商品报价文件</span>
                                <span class="bgButton MR_ten" @click="discountTypeList();clean()">商品渠道追加折扣维护</span>
                                
                            </div>
                        </el-col>
                    </el-row>
                    <el-dialog title="维护折扣" :visible.sync="dialogVisibleUp" width="800px">
                        <el-row class="baojiaDC">
                            
                            <!-- <el-col :span="4"><div><span class="redFont">*</span>维护方式：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="typeInfo" placeholder="请选择维护方式名称">
                                            <el-option v-for="item in update_type" :key="item.catId" :label="item.catName" :value="item.catId"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col> -->
                            <el-col :span="4"><div><span class="redFont">*</span>结算开始日期：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-date-picker class="w_ratio" v-model="start_date" value-format="yyyy-MM-dd" type="date" placeholder="请选择结算开始日期"></el-date-picker>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>结算结束日期：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <el-date-picker class="w_ratio" v-model="end_date" value-format="yyyy-MM-dd" type="date" placeholder="请选择结算结束日期"></el-date-picker>
                            </div>
                            </el-col>
                        </el-row>
                        <el-row class="MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>折扣类型名称：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <!-- <el-cascader :options="options" :props="props" clearable></el-cascader>  -->
                                <el-cascader :options="options" :props="props" collapse-tags clearable v-model="option"></el-cascader>
                            </div>
                            </el-col>
                        </el-row>
                        <el-row class="baojiaDC">
                            <span @click="d_table()" class="notBgButton" style="position: absolute;right: 240px;top: 87px;">下载商品渠道追加折扣维护模板表</span>
                            <el-col :span="4"><div><span class="redFont">*</span>上传文件：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
                                        <div class="file">
                                        <img src="../../image/upload.png"/>
                                        <span v-if="fileName==''">点击上传文件</span>
                                        <span v-if="fileName!=''">{{fileName}}</span>
                                        <input class="w_h_ratio" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                                        </div>
                                    </form>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleUp = false;">取 消</el-button>
                            <el-button type="primary" @click="uploadGmcDiscount()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-dialog title="商品报价导出" :visible.sync="dialogVisibleDG" width="800px">
                        <span class="redFont">请确认当天的购物车是否已导入系统，如果未导入则导出表中商品分类为空</span>
                        <el-row class="baojiaDC">
                            <el-col :span="4"><div>追加点：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="append" placeholder="例:0.05"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>采购渠道：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-select v-model="channels_id" placeholder="请选择采购渠道">
                                        <el-option v-for="item in buyChannel" :key="item.id" :label="item.channels_name" :value="item.id"></el-option>
                                    </el-select>
                                </div>
                            </el-col>
                            <!-- <el-col :span="14"><div class="redFont">*该值为负数则说明报价更便宜，正数则说明报价更贵</div></el-col> -->
                        </el-row>
                        <el-row class="baojiaDC">
                            <el-col :span="4"><div><span class="redFont">*</span>预计到港日：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-date-picker class="w_ratio" v-model="predictDate" value-format="yyyy-MM-dd" type="date" placeholder="请选择预计到港日"></el-date-picker>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div><span class="redFont">*</span>出境日：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-date-picker class="w_ratio" v-model="exitDate" value-format="yyyy-MM-dd" type="date" placeholder="请选择处境日"></el-date-picker>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="baojiaDC">
                            <el-col :span="4"><div style="line-height: 23px;margin-top: 10px;"><span class="redFont">*</span>乐天官网美金/<br>&nbsp;&nbsp;&nbsp;人民币汇率：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="rmbRate" placeholder="请输入"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div style="line-height: 23px;margin-top: 10px;"><span class="redFont">*</span>乐天官网美金/<br>&nbsp;&nbsp;&nbsp;韩币汇率：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="koreanRate" placeholder="请输入"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="baojiaDC">
                            <el-col :span="5"><div><span class="redFont">*</span>人民币兑韩币汇率：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-input v-model="rmbKoreanRate" placeholder="请输入"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisibleDG = false;">取 消</el-button>
                            <span class="bgButton" @click="downloadGoods('T')">生成特价报价</span>
                            <span class="bgButton" @click="downloadGoods('G')">生成购物车报价</span>
                            <!-- <el-button class="yincang" style="display:none" type="info">确 定</el-button> -->
                        </span>
                    </el-dialog>
                    <el-dialog title="报价文件下载" id="xiazai" :visible.sync="dialogVisibleFD" width="950px">
                        <span class="redFont F_S_T" style="position: absolute;top: 21px;left: 130px;">(点击文件名下载)</span>
                        <!-- <el-row class="MB_ten"> 
                            <el-col :span="12"><div class="notBgButton" @click="clickFile('191106__特价活动报价（乐天4档EMS）美金')">191106__特价活动报价（乐天4档EMS）美金</div></el-col>
                            <el-col :span="12"><div class="notBgButton" @click="clickFile('191106__特价活动报价（乐天4档EMS）人民币')">191106__特价活动报价（乐天4档EMS）人民币</div></el-col>
                        </el-row> -->
                        <div style="border-bottom: 1px solid #ccc" v-if="!isEmpty">
                            <span class="d_I_B w_45_ratio notBgButton fontLift" v-for="item in routeNameLi" @click="clickFile(item)">{{item}}</span>
                        </div>
                        <div v-if="!isEmpty">
                            <span class="d_I_B w_45_ratio notBgButton fontLift" v-for="item in cartNasmeLi" @click="clickFile(item)">{{item}}</span>
                        </div>
                        <span v-if="isEmpty" class="redFont fontCenter d_I_B w_ratio F_S_twenty MT_Thirty">请先生成商品报价文件</span>
                    </el-dialog>
                    <table class="fontCenter w_ratio" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <th class="widthTwoHundred">结算开始时间</th>
                            <th class="widthTwoHundred">结算结束时间</th>
                            <th style="width:200px;">商品名称</th>
                            <th class="widthTwoHundred">商品规格码</th>
                            <th class="widthOneFiveHundred">商家编码</th>
                            <th class="widthOneFiveHundred">商品代码</th>
                            <th class="widthTwoHundred">商品参考码</th>
                            <th class="widthOneHundred">渠道名称</th>
                            <th class="widthOneHundred">方式名称</th>
                            <th class="widthOneHundred">折扣</th>
                            <th class="widthTwoHundred">类型名称</th>
                        </tr>
                        <tr  v-for="item in tableData">
                            <td class="widthTwoHundred">{{item.start_date}}</td>
                            <td class="widthTwoHundred">{{item.end_date}}</td>
                            <td class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                            </td>
                            <td class="widthTwoHundred">{{item.spec_sn}}</td>
                            <td class="widthOneFiveHundred">{{item.erp_merchant_no}}</td>
                            <td class="widthOneFiveHundred">{{item.erp_prd_no}}</td>
                            <td class="widthTwoHundred">{{item.erp_ref_no}}</td>
                            <td class="widthOneHundred">{{item.channels_name}}</td>
                            <td class="widthOneHundred">{{item.method_name}}</td>
                            <td class="widthOneHundred">{{item.discount}}</td>
                            <td class="widthTwoHundred">{{item.type_name}}</td>
                        </tr>
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
// import searchBox from '../UiAssemblyList/searchBox';
import { exportsa } from '@/filters/publicMethods.js'
export default {
  components:{
      notFound,
    //   searchBox, 
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      offerUrl:`${this.$offerUrl}`,
      headersStr:'',
      tableData:[],
      props: { multiple: true },
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      //上传折扣类型对应的折扣 
      dialogVisibleUp:false,
      fileName:'',
      type_id:'',
      options:[],
      option:[],
      update_type:[{"catName":"按照商品维护","catId":"1"},{"catName":"按照品牌维护","catId":"2"}],
      typeInfo:'',
      start_date:'',
      end_date:'',
      //搜索 
      startDate:'',
      endDate:'',
      goods_name:'',
      spec_sn:'',
      erp_merchant_no:'',
      erp_ref_no:'',
      erp_prd_no:'',
      channels_id:'',
      //报价商品导出 
      titleStr:'',
      dialogVisibleDG:false,
      rmbRate:'',
      koreanRate:'',
      rmbKoreanRate:'',
      exitDate:'',
      predictDate:'',
      exportType:'',
      channels_id:'',
      buyChannel:[],
      append:0,
      exportTypeList:[{"name":"EMS美金","id":"EMS_DOLLAR"},{"name":"EMS人民币","id":"EMS_RMB"},{"name":"机场交货美金","id":"AIRPORT_DOLLAR"},{"name":"机场交货人民币","id":"AIRPORT_RMB"}],
      //文件下载 
      dialogVisibleFD:false,
      routeList:[],
      routeNameLi:[],
      isEmpty:false,
      cartNasmeLi:[],
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
        axios.post(vm.url+vm.$getGmcDiscountListURL,
            {
                "page_size":vm.pagesize,
                "page":vm.page,
                "start_date":vm.startDate,
                "end_date":vm.endDate,
                "goods_name":vm.goods_name,
                "spec_sn":vm.spec_sn,
                "erp_merchant_no":vm.erp_merchant_no,
                "erp_ref_no":vm.erp_ref_no,
                "erp_prd_no":vm.erp_prd_no,
                "channels_id":vm.channels_id,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code=='1000'){
                vm.tableData=res.data.data.data; 
                vm.total=res.data.data.total
                vm.isShow=false;
            }else if(res.data.code=='1002'){
                vm.isShow=true;
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
        axios.post(vm.url+vm.$getBuyChannelURL,
            {},
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.buyChannel=res.data.buyChannel
        })
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
    discountTypeList(){
        let vm = this;
        vm.dialogVisibleUp=true;
        vm.options.splice(0);
        axios.get(vm.url+vm.$methodChannelsTypeListURL+"?discount_type=2&is_all=1",
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.code=="1000"){
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
    uploadGmcDiscount(){
        let vm = this;
        $(event.target).addClass("disable");
        // setTimeout(function(){ 
        //     $(event.target).removeClass("disable");
        // },2000)
        var formDate = new FormData($("#forms")[0]);
        if(vm.fileName==''){
            vm.$message('请选择文件后上传！');
            return false;
        }
        // if(vm.typeInfo==''){
        //     vm.$message('请选维护方式后上传！');
        //     return false;
        // }
        if(vm.option.length==0){
            vm.$message('请选折扣类型名称后上传！');
            return false;
        }
        if(vm.start_date==''||vm.start_date==null){
            vm.$message('请选择开始时间后上传！');
            return false;
        }
        if(vm.end_date==''&&vm.end_date==null){
            vm.$message('请选择结束时间后上传！');
            return false;
        }
        let load = Loading.service({fullscreen: true, text: '正在提交中....'}); 
        vm.dialogVisibleUp=false;
        let type_id = '';
        vm.option.forEach(element=>{
            type_id+=element[2]+",";
        })
        type_id=(type_id.slice(type_id.length-1)==',')?type_id.slice(0,-1):type_id;
        $.ajax({
        url: vm.url+vm.$uploadGmcDiscountURL+"?type_id="+type_id+"&channels_id="+vm.option[1]+"&method_id="+vm.option[0]+
            "&update_type="+vm.typeInfo+"&start_date="+vm.start_date+"&end_date="+vm.end_date,
        type: "POST",
        async: true,
        cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            load.close()
            if(res.code=="1000"){
                vm.getDataList();
                $("#file1").val('');
                vm.fileName='';
                vm.$message(res.msg);
            }else if(res.code=="1001"){
                sessionStorage.setItem("DDDG",JSON.stringify(res.data));
                vm.$router.push('/downloadDiscountDiffGoods');
            }else{
                vm.$message(res.msg);
                $("#file1").val('');
                vm.fileName='';
            }
        }
        }).catch(function (error) {
            load.close()
            if(error.response.status!=''&&error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //搜索框 
    searchFrame(e){
        let vm=this;
        vm.search=e;
        vm.getDataList();
    },
    clean(){
        let vm = this;
        vm.option=[];
        vm.start_date='';
        vm.end_date='';
        vm.type_id='';
        $("#file1").val('');
        vm.fileName='';
        vm.typeInfo='';
    },
    d_table(){
        let vm = this;
        window.open(vm.downloadUrl+'/维护商品追加折扣(返点)模板.xls');
    },
    //导出商品最终报价 
    exportGoodsOffe(text){
        let vm = this;
        vm.routeNameLi.splice(0)
        vm.routeList.splice(0);
        vm.cartNasmeLi.splice(0);
        if(text=='dl'){//下载表格
            $(event.target).addClass("disable");
            axios.post(vm.url+vm.$getOfferFileURL,{},
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){                                    
                vm.dialogVisibleFD=true;
                if(res.data.offerFile.length!=0){
                    // vm.routeList=res.data.offerFile; 
                    res.data.offerFile.forEach( element => {
                        vm.routeNameLi.push(element.split("/")[4])
                        vm.routeList.push(element)
                    }) 
                }
                    
            })
            axios.post(vm.url+vm.$getCartOfferFileURL,{},
                {
                    headers:vm.headersStr,
                },
            ).then(function(res){
                if(res.data.cartFile.length!=0){
                    res.data.cartFile.forEach( element => {
                        vm.cartNasmeLi.push(element.split("/")[4]);
                        vm.routeList.push(element)
                    }) 
                }
            })
            if(vm.routeList.length!=0){
                vm.isEmpty=true;
            }else{
                vm.isEmpty=false;
            }
            let sss=$(event.target)
            setTimeout(function(){
                sss.removeClass("disable");
            },10000)
        }else if(text=='ge'){//生成数据 
            vm.dialogVisibleDG=true; 
            vm.rmbRate='';
            vm.koreanRate='';
            vm.rmbKoreanRate='';
            vm.exitDate='';
            vm.predictDate='';
            vm.channels_id='';
        }
    },
    //下载标价商品 
    clickFile(name){
        let vm = this;
        for(var i = 0;i<vm.routeList.length;i++){
            if(vm.routeList[i].indexOf(name) != -1){
                window.open(vm.offerUrl+vm.routeList[i]);
            }
        }
    },
    //确认导出商品最终报价 
    downloadGoods(state){
        let vm = this;
        if(1<=vm.append||vm.append<=-1){
            vm.$message('追加点必须是小数！');
            return false;
        }
        if(vm.rmbRate===''){
            vm.$message('请输入人民币汇率后导出');
            return false;
        }
        if(vm.koreanRate===''){
            vm.$message('请输入韩币汇率汇率后导出');
            return false;
        }
        if(vm.rmbKoreanRate===''){
            vm.$message('请输入人民币兑换韩币汇率后导出');
            return false;
        }
        if(vm.exitDate===''){
            vm.$message('请输入出境日后导出');
            return false;
        }
        if(vm.predictDate===''){
            vm.$message('请输入预计到港日后导出');
            return false;
        }
        let url;
        if(state=='T'){
            url=vm.$makeGoodsOfferURL
        }else if(state=='G'){
            url=vm.$makeCartOfferURL
        }
        let load = Loading.service({fullscreen: true, text: '报价数据正在生成中，需等待2-3分钟....'}); 
        // $(".queding").hide() 
        // $(".yincang").show()
        // vm.dialogVisibleDG=false; 
        axios.post(vm.url+url+'?rmbRate='+vm.rmbRate+'&koreanRate='+vm.koreanRate+'&rmbKoreanRate='+vm.rmbKoreanRate+
            '&append='+vm.append+'&exitDate='+vm.exitDate+'&predictDate='+vm.predictDate+'&channels_id='+vm.channels_id,
            {},{
                headers:vm.headersStr,
            },
        ).then(function(res){
            load.close();
            if(res.data.code=="2024"){
                vm.$message({message:'文件已生成成功，点击下载商品报价文件进行下载!',duration:10000,showClose:true,type: 'success'});
                vm.dialogVisibleDG=false; 
            }else if(res.data.code=="2067"){
                vm.dialogVisibleDG=false; 
                vm.$message({message:res.data.msg,duration:0,showClose:true,type: 'success'});
            }else{
                vm.$message({message:res.data.msg,duration:0,showClose:true,type: 'success'});
            }
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    checkTime(time){
        if(time<10){
            time = '0'+time
        }
        return time
    },
  }
}
</script>

<style>
.baojiaDC {
    height: 59px;
    line-height: 59px;
}
#xiazai .el-dialog{
    width: 950px !important;
    height: 350px !important;
    margin: auto;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    margin: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    margin: auto !important;
}
</style>

<style scoped>
@import '../../css/publicCss.css';
</style>
