<template>
  <div class="predictDemandList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <!-- <tableTitle :tableTitle='tableTitle,config'></tableTitle> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>预采需求列表</span></div></el-col>
                      <el-col :span="2"><div>客户</div></el-col>
                      <el-col :span="3" >
                          <template>
                            <el-select v-model="sale_user_id" clearable placeholder="请选择客户">
                              <el-option v-for="item in saleUserList" :key="item.value" :label="item.user_name" :value="item.id">
                              </el-option>
                            </el-select>
                          </template>
                      </el-col>
                      <el-col :span="2"><div>需求单号</div></el-col>
                      <el-col :span="3"><div><el-input v-model="demand_sn" placeholder="请输入需求单号"></el-input></div></el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
                    <el-row>
                        <tableContent :tableContent='tableContent,TCTitle,tableField,contentConfig' @Download="Download" @upData="upData" @ViewDetail="ViewDetail"></tableContent>
                        <notFound v-if="isShow"></notFound>
                    </el-row>
                    <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                        :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                    </el-pagination>
                    <el-dialog title="上传预采批次" :visible.sync="dialogVisibleUp" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>自提/邮寄：</div></el-col>
                            <el-col :span="6">
                            <div>
                                <template>
                                    <el-select v-model="path_way" placeholder="请选择自提/邮寄">
                                        <el-option v-for="item in path_ways" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                    </el-select>
                                </template>
                            </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>采购期：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <el-cascader :options="options" v-model="option" @change="handleChange">
                                    </el-cascader>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div><span class="redFont">*</span>采购编号ID：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="userID" placeholder="请选择采购编号ID">
                                            <el-option v-for="item in userIDs" :key="item.id" :label="item.real_name" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>仓位：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="port_id" placeholder="请选择仓位">
                                            <el-option v-for="item in ports" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>供应商：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="supplier" placeholder="请选择供应商">
                                            <el-option v-for="item in suppliers" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>选择任务模板：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="task_id" placeholder="选择任务模板">
                                            <el-option v-for="item in taskInfoS" :key="item.sn" :label="item.label" :value="item.sn">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div class=""><span class="redFont">*</span>选择提货时间：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <div>
                                            <el-date-picker v-model="delivery_time" type="date" placeholder="设置提货日"></el-date-picker>
                                        </div>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class=""><span class="redFont">*</span>选择到货时间：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <div>
                                            <el-date-picker v-model="arrive_time" type="date" placeholder="设置到货日"></el-date-picker>
                                        </div>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="lineHeightForty MB_twenty">
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
                            <el-button type="primary" @click="confirmUpData()">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-dialog title="提示" :visible.sync="tipsDialogVisible" width="800px">
                        <div v-for="(item,index) in tipsStr" style="display:inline-block"><span>{{item}}<span v-if="index!=tipsStr.length-1">，</span></span></div>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="tipsDialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="tipsDialogVisible = false">确 定</el-button>
                        </span>
                    </el-dialog>
                    <el-dialog title="提示" :visible.sync="upDiscountDataVisible" width="800px">
                        <div style="display:inline-block"><span>{{tipsStr}}</span></div>
                        <el-row class="lineHeightForty MB_twenty discount" v-for="(item,index) in upDiscountData.brand_info" :key="item.brand_name">
                            <el-col :span="4" :offset="1">
                                <span>渠道名称 ：</span>
                            </el-col>
                            <el-col :span="6">
                                <span>{{item.brand_name}}</span>
                            </el-col>
                            <el-col :span="4">
                                <span><span class="redFont">*</span>对应折扣 ：</span>
                            </el-col>
                            <el-col :span="6">
                                <span><el-input placeholder="请输入折扣" :name="index"></el-input></span>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="upDiscountDataVisible = false">取 消</el-button>
                            <el-button type="primary" @click="submitData()">确 定</el-button>
                        </span>
                    </el-dialog>
                </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
import searchBox from '@/components/UiAssemblyList/searchBox'
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import tableContent from '@/components/UiAssemblyList/tableContent'
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
import { dateToStr } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
export default {
  components:{
      searchBox,
      tableTitle,
      tableContent,
      notFound,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      tableTitle:'预采需求列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"true"},{"back":"false"}]',
      TCTitle:['需求单号','部门','客户','商品总需求量','创建日期','操作'],
      tableContent:'',
      tableField:['demand_sn','de_name','user_name','goods_num','create_time'],
      contentConfig:'[{"isShow":"predictDemandList"},{"parameter":"demand_sn"}]',
      //页面数据
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      search:'',//用户输入数据
      demand_sn:'',
      dialogVisibleUp:false,
      options:[],
      option:[],
      path_ways:[{"label":"自提","id":"0"},{"label":"邮寄","id":'1'}],
      path_way:'',
      userIDs:[],
      userID:'',
      ports:[],
      port_id:'',
      fileName:'',
      supplier:'',
      suppliers:[],
      taskInfoS:[],
      task_id:'',
      delivery_time:'',
      deliveryStr:'',
      arrive_time:'',
      arriveStr:'',
      //后台消息确认弹框
      tipsDialogVisible:false,
      tipsStr:'',
      //如果折扣信息不完整
      upDiscountDataVisible:false,
      upDiscountData:[],
      //搜索
      demand_sn:'',
      saleUserList:[],
      sale_user_id:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    handleChange(value) {
    },
    getDataList(){
        let vm = this;
        axios.get(vm.url+vm.$predictDemandListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&demand_sn="+vm.demand_sn+"&sale_user_id="+vm.sale_user_id,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                console.log(res.data)
                vm.tableContent=JSON.stringify(res.data.data.predict_demand_list.data);
                console.log(vm.tableContent)
                vm.total=res.data.data.predict_demand_list.total;
                vm.saleUserList=res.data.data.su_info;
                vm.isShow=false;
            }else{
                vm.tableContent=JSON.stringify('');
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
    //打开上传预采批次
    upData(demand_sn){
        let vm = this;
        vm.Clean();
        vm.demand_sn=demand_sn;
        if(vm.options.length>0){
            vm.dialogVisibleUp=true;
            return false;
        }
        axios.get(vm.url+vm.$uploadPredictRealURL+"?demand_sn="+demand_sn,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogVisibleUp=true;
                res.data.data.supplier_list_info.forEach(supplier=>{
                    vm.suppliers.push({"label":supplier.supplier_name,"id":supplier.supplier_id})
                })
                res.data.data.erp_house_list.forEach(erpHouse=>{
                    vm.ports.push({"label":erpHouse.store_name,"id":erpHouse.store_id});
                })
                vm.userIDs=res.data.data.user_info;
                //任务模板
                res.data.data.task_info.forEach(taskInfo=>{
                    vm.taskInfoS.push({"label":taskInfo.task_name,"sn":taskInfo.task_sn})
                })
                res.data.data.purchase_total_info.forEach(purchase => {
                    let method_list=[];
                    if(purchase.method_list.length!=0){
                        purchase.method_list.forEach(method=>{
                            let purchase_sn=[];
                            if(method.channel_list!=undefined){
                                method.channel_list.forEach(channel=>{
                                    purchase_sn.push({"value":channel.id,"label":channel.channels_name});
                                })
                                method_list.push({"value":method.id,"label":method.method_name,"children":purchase_sn});
                            }
                        })
                    }
                    vm.options.push({"value":purchase.purchase_sn,"label":purchase.purchase_sn,"children":method_list})
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
    //选择表格
    SelectFile(){
        let vm=this;
        var r = new FileReader();
        var f = document.getElementById("file1").files[0];
        vm.fileName=f.name;
    },
    //确认上传
    confirmUpData(){
        let vm=this;
        var formDate = new FormData($("#forms")[0]);
        if(vm.path_way==''){
            vm.$message('自提或邮寄不能为空！');
            return false;
        }
        if(vm.option.length==0){
            vm.$message('采购期不能为空！');
            return false;
        }
        if(vm.userID==''){
            vm.$message('采购编号ID不能为空！');
            return false;
        }
        if(vm.port_id==''){
            vm.$message('仓位不能为空！');
            return false;
        }
        if(vm.supplier==''){
            vm.$message('供应商不能为空！');
            return false;
        }
        if(vm.fileName==''){
            vm.$message('上传的文件不能为空！');
            return false;
        }
        vm.deliveryStr=dateToStr(vm.delivery_time)
        if(vm.delivery_time==''){
            vm.$message('提货时间不能为空！');
            return false;
        }
        vm.arriveStr=dateToStr(vm.arrive_time)
        if(vm.arrive_time==''){
            vm.$message('到货时间不能为空！');
            return false;
        }
        var task_name='';
        vm.taskInfoS.forEach(taskSn=>{
            if(vm.task_id==taskSn.sn){
                task_name=taskSn.label
            }
        })
        if(vm.task_id==''){
            vm.$message('任务模板不能为空！');
            return false;
        }
        vm.dialogVisibleUp = false;
        let loa=Loading.service({fullscreen: true, text: '拼命上传中....'});
        $.ajax({
            url: vm.url+vm.$doUploadPredictRealURL+"?purchase_sn="+vm.option[0]+"&method_id="+vm.option[1]+"&channels_id="+vm.option[2]+
            "&path_way="+vm.path_way+"&user_id="+vm.userID+"&port_id="+vm.port_id+"&demand_sn="+vm.demand_sn+"&supplier_id="+vm.supplier+
            "&task_id="+vm.task_id+"&delivery_time="+vm.deliveryStr+"&arrive_time="+vm.arriveStr,
            type: "POST",
            async: true,
            cache: false,
            headers:vm.headersStr,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
                loa.close();
                if(res.code==1000){
                    vm.dialogVisibleUp = false;
                    vm.$message(res.msg);
                }else if(res.code=='1099'){
                    vm.upDiscountDataVisible = true;
                    vm.tipsStr = res.msg;
                    vm.upDiscountData = res.data;
                }else{
                vm.tipsDialogVisible=true;
                let msg=res.msg.split(',')
                vm.tipsStr=msg;
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
    //提交折扣 
    submitData(){
        let vm = this;
        let arr = [];
        for(var i=0;i<vm.upDiscountData.brand_info.length;i++){
            let I_value = $('.discount input[name='+i+']').val();
            if(I_value<=0||I_value>=1){
                vm.$message('折扣填写错误，请重新填写!');
                return false;
            }
            arr.push({"brand_id":vm.upDiscountData.brand_info[i].brand_id,"brand_discount":I_value});
        }
        axios.post(vm.url+vm.$batchAddBrandDiscountURL,
            {
                "method_id":vm.upDiscountData.method_id,
                "channels_id":vm.upDiscountData.channels_id,
                "brand_discount":arr,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.upDiscountDataVisible = false;
            vm.$message(res.data.msg);
        }).catch(function (error) {
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    Clean(){
        let vm = this;
        vm.option=[];
        $("#file1").val('');
        vm.fileName='';
        vm.path_way='';
        vm.userID='';
        vm.port_id='';
        vm.demand_sn='';
        vm.supplier='';
    },
    //查看详情
    ViewDetail(demand_sn){
        let vm = this;
        axios.get(vm.url+vm.$predictDetailURL+"?demand_sn="+demand_sn,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                sessionStorage.setItem("tableData",JSON.stringify(res.data.data));
                vm.$router.push('/predictDetail');
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
    //下载预採批次
    Download(demand_sn){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        window.open(vm.url+vm.$downloadPredictDemandtURL+'?demand_sn='+demand_sn+'&token='+headersToken);
    },
    //搜索框
    searchFrame(e){
        let vm=this;
        vm.search=e;
        vm.page=1;
        vm.getDataList();
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
<style scoped>
.el-date-editor.el-input, .el-date-editor.el-input__inner{
    width: 100% !important;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>
