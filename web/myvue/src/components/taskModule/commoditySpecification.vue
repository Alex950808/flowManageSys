<template>
  <div class="commodity_b">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="commodity">
                        <div class="title">
                            <span class="bgButton MR_twenty" @click="backUpPage()">返回上一页</span><span>规格列表</span>
                            <div class="newGoods">
                                <el-button class="newGoodsOne" type="text" @click="centerDialogVisible = true;openAddBox()"><i class="iconfont upDataIcon" style="vertical-align: -3px;">&#xea22;</i>新增规格</el-button>
                                <el-dialog title="新增规格" :visible.sync="centerDialogVisible" width="800px">
                                    <span>以下为必填字段</span>
                                    <el-row class="elRow">
                                        <el-col :span="3">
                                            <span><span class="redFont">*</span>商品重量：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="specWeight">
                                                <el-input v-model="spec_weight" style="width:87%;" placeholder="请输入商品重量"></el-input>
                                            </div>
                                        </el-col>
                                        <el-col :span="3">
                                            <span><span class="redFont">*</span>商品价格：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="specPrice">
                                                <el-input v-model="spec_price" style="width:87%;" placeholder="请输入商品价格"></el-input>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <el-row class="elRow">
                                        <el-col :span="3">
                                            <span><span class="redFont">*</span>平台类型：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="specPrice">
                                                <el-select v-model="code_type" placeholder="请选择平台类型">
                                                    <el-option v-for="item in code_types" :key="item.id" :label="item.label" :value="item.id"></el-option>
                                                </el-select>
                                            </div>
                                        </el-col>
                                        <el-col :span="3">
                                            <span><span class="redFont">*</span>平台编码：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="specWeight">
                                                <el-input v-model="goods_code" style="width:87%;" placeholder="请输入平台对应的平台编码"></el-input>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <el-row class="elRow">
                                        <el-col :span="3">
                                            <span><span class="redFont">*</span>EXW折扣：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="specWeight">
                                                <el-input v-model="exw_discount" style="width:87%;" placeholder="请输入EXW折扣"></el-input>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <span>以下为选填字段</span>
                                    <el-row class="elRow">
                                        <el-col :span="3">
                                            <span>商家编码：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="erpMerchantNo">
                                                <el-input v-model="erp_merchant_no" style="width:87%;" placeholder="请输入商家编码"></el-input>
                                            </div>
                                        </el-col>
                                        <el-col :span="3">
                                            <span>商品代码：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="erpPrdNo">
                                                <el-input v-model="erp_prd_no" style="width:87%;" placeholder="请输入商品代码"></el-input>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <el-row class="elRow">
                                        <el-col :span="3">
                                            <span>商品参考码：</span>
                                        </el-col>
                                        <el-col :span="8">
                                            <div class="erpRefNo">
                                                <el-input v-model="erp_ref_no" style="width:87%;" placeholder="请输入商品参考码"></el-input>
                                            </div>
                                        </el-col>
                                    </el-row>
                                    <span slot="footer" class="dialog-footer">
                                        <el-button type="info" @click="centerDialogVisible = false;cancelEditors()">取 消</el-button>
                                        <el-button type="primary" @click="centerDialogVisible = false;confirmShow()">确 定</el-button>
                                    </span>
                                </el-dialog>
                            </div>
                        </div>
                        <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>商品规格</th>
                                <th>商品参考码</th>
                                <th>商品价格</th>
                                <th>商品货号</th>
                                <th>商家编码</th>
                                <th>商品代码</th>
                                <th>商品重量</th>
                                <th>编辑该规格</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(item,index) in tableData">
                                <td>{{item.spec_sn}}</td>
                                <td>{{item.erp_ref_no}}</td>
                                <td>{{item.spec_price}}</td>
                                <td>{{item.goods_sn}}</td>
                                <td>{{item.erp_merchant_no}}</td>
                                <td>{{item.erp_prd_no}}</td>
                                <td>{{item.spec_weight}}</td>
                                <td><i class="iconfont editGoods" @click="dialogVisibleEdit = true;editGoods(item.spec_id)">&#xe62f;</i></td>
                            </tr>
                            </tbody>
                        </table>
                        <el-dialog title="编辑规格" :visible.sync="dialogVisibleEdit" width="800px">
                            <span class="editTitle">以下为必填字段</span>
                            <el-row class="elRow">
                                <el-col :span="3">
                                    <span>商品参考码：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="erpRefNo">
                                        <el-input v-model="erpRefNo" style="width:87%;" placeholder="请输入商品参考码"></el-input>
                                    </div>
                                </el-col>
                                <el-col :span="3">
                                    <span>商品价格：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="specPrice">
                                        <el-input v-model="specPrice" style="width:87%;" placeholder="请输入商品价格"></el-input>
                                    </div>
                                </el-col>
                            </el-row>
                            <span class="editTitle">以下为选填字段</span>
                            <el-row class="elRow">
                                <el-col :span="3">
                                    <span>商家编码：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="erpMerchantNo">
                                        <el-input v-model="erpMerchantNo" style="width:87%;" placeholder="请输入商家编码"></el-input>
                                    </div>
                                </el-col>
                                <el-col :span="3">
                                    <span>商品代码：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="erpPrdNo">
                                        <el-input v-model="erpPrdNo" style="width:87%;" placeholder="请输入商品代码"></el-input>
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row class="elRow">
                                <el-col :span="3">
                                    <span>商品重量：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="specWeight">
                                        <el-input v-model="specWeight" style="width:87%;" placeholder="请输入商品重量"></el-input>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisibleEdit = false;cancelEditors()">取 消</el-button>
                                <el-button type="primary" @click="dialogVisibleEdit = false;editconfirmShow()">确 定</el-button>
                            </span>
                        </el-dialog>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    </el-row>
    <div class="confirmPopup_b" v-if="show">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="determineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要新增？
        <div class="confirm"><el-button @click="getAddGoods()" size="mini" type="primary">是</el-button><el-button @click="determineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
    <div class="confirmPopup_b" v-if="confirm">
        <div class="confirmPopup">
            <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                &nbsp;&nbsp;<i class="el-icon-close" @click="editdetermineIsNo()"></i>&nbsp;&nbsp;
            </div>
        请您确认是否要修改？
        <div class="confirm"><el-button @click="doEditGoods()" size="mini" type="primary">是</el-button><el-button @click="editdetermineIsNo()" size="mini" type="info">否</el-button></div>
        </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
// import $ from 'jquery'
export default {
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      tableData:[],
      search:'',
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      centerDialogVisible: false,
      brand:'',//选中的品牌
      brandList:[],//品牌集合
      optionsWithDisabled:[],
      selectedOptions:[],
      PlaceDelivery:[],//发货地
      DeliveryId:'',
      fileName:'',
      goodsImg:'',//商品图片路径
      isShow:true,
      spec_price:'',//商品价格
      spec_weight:'',//商品重量
      erp_merchant_no:'',//商家编码
      erp_prd_no:'',//商品代码
      erp_ref_no:'',//商品参考码
      show:false,
      confirm:false,
      confirmData:[],
      exw_discount:'',
      code_type:'',
      code_types:[],
      goods_code:'',
      //编辑数据
      dialogVisibleEdit:false,
      erpRefNo:'',
      specPrice:'',
      erpMerchantNo:'',
      erpPrdNo:'',
      specWeight:'',
      spec_id:'',
    }
  },
  mounted(){
      this.getCommodityList();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
      handleChange(value) {
      },
      getCommodityList(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        sessionStorage.setItem("page",vm.$route.query.page);
        axios.get(vm.url+vm.$goodsSpecListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search+"&goods_sn="+vm.$route.query.goods_sn,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            vm.loading.close()
            vm.tableData=res.data.data;
            vm.total=res.data.data_num
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //单个增加商品规格
      getAddGoods(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          vm.show=false;
          if(vm.isEmpty()==false){
              return false;
          }
          axios.post(vm.url+vm.$doAddGoodsSpecURL,
            {
                "spec_price":vm.spec_price,
                "goods_sn":vm.$route.query.goods_sn,
                "spec_weight":vm.spec_weight,
                "exw_discount":vm.exw_discount,
                "code_type":vm.code_type,
                "goods_code":vm.goods_code,
                //以下选填
                "erp_merchant_no":vm.erp_merchant_no,
                "erp_prd_no":vm.erp_prd_no,
                "erp_ref_no":vm.erp_ref_no,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              vm.$message(res.data.msg);
              if(res.data.code=="1000"){
                    vm.confirmData=res.data.data;
                    vm.getCommodityList();
                    vm.centerDialogVisible=false;
                    vm.cancelEditors();
              }
          }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //取消商品
      cancelEditors(){
        let vm=this;
        vm.spec_price=''
        vm.spec_weight='';
        vm.erp_merchant_no='';
        vm.erp_prd_no='';
        vm.erp_ref_no='';
        vm.erpRefNo='';
        vm.specPrice='';
        vm.erpMerchantNo='';
        vm.erpPrdNo='';
        vm.specWeight='';
        vm.exw_discount='';
        vm.code_type='';
        vm.goods_code='';
      },
      //必填字段判空
      isEmpty(){
          let vm = this;
          if(vm.spec_price==''){
              vm.$message('商品价格不能为空，请输入商品价格!');
              return false;
          }
          if(vm.spec_weight==''){
              vm.$message('商品重量不能为空，请输入商品重量!');
              return false;
          }
          if(vm.exw_discount==''){
              vm.$message('EXW折扣不能为空，请输入EXW折扣!');
              return false;
          }
          if(vm.code_type==''){
              vm.$message('平台类型不能为空，请输入平台类型!');
              return false;
          }
          if(vm.goods_code==''){
              vm.$message('平台类型对应的平台编码不能为空，请输入平台类型对应的平台编码!');
              return false;
          }
      },
    //查看商品规格
    seeSpecifications(goods_sn){
        this.$router.push('/commoditySpecification?goods_sn='+goods_sn);
    },
    
      //确定编辑该规格
      doEditGoods(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          vm.editdetermineIsNo();
          axios.post(vm.url+vm.$doEditGoodsSpecURL,
            {
                "erp_ref_no":vm.erpRefNo,
                "spec_price":vm.specPrice,
                "spec_id":vm.spec_id,
                "erp_merchant_no":vm.erpMerchantNo,
                "erp_prd_no":vm.erpPrdNo,
                "spec_weight":vm.specWeight,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            } 
          ).then(function(res){
              if(res.data.code==1000){
                  vm.getCommodityList();
                  vm.$message(res.data.msg);
              }else{
                  vm.$message(res.data.msg);
              }
          }).catch(function (error) {
                if(error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                }
            });
      },
      //返回上一页
      backUpPage(){
          let vm=this;
          vm.$router.push('/commodity?page='+vm.$route.query.page);
      },
      //搜索框
      searchFrame(){
          let vm=this;
          vm.getCommodityList();
      },
      //确认弹框否
        determineIsNo(){
            let vm=this;
            vm.show=false;
            vm.centerDialogVisible = false;
        },
        //编辑确认弹框否
        editdetermineIsNo(){
            let vm=this;
            vm.confirm=false;
            vm.dialogVisibleEdit = false;
        },
        //打开新增商品规格弹框
        openAddBox(){
            let vm = this;
            let headersToken=sessionStorage.getItem("token");
            vm.code_types.splice(0);
            axios.get(vm.url+vm.$addGoodsSpecURL,
                {
                    headers:{
                        'Authorization': 'Bearer ' + headersToken,
                        'Accept': 'application/vnd.jmsapi.v1+json',
                    }
                } 
            ).then(function(res){
                if(res.data.code==1000){
                    for(let key in res.data.data){
                        vm.code_types.push({"label":res.data.data[key],"id":key})
                    }
                }else{
                    vm.$message(res.data.msg);
                }
            }).catch(function (error) {
                if(error.response.status=="401"){
                    vm.$message('登录过期,请重新登录!');
                    sessionStorage.setItem("token","");
                    vm.$router.push('/');
                }
            });
        },
        //确认弹框是
        confirmShow(){
            let vm=this;
            vm.show=true;
        },
        //编辑确认弹框是
        editconfirmShow(){
            let vm=this;
            vm.confirm=true;
        }
  }
}
</script>

<style>
.commodity .ellipsis span{
    width: 480px;
    line-height: 23px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}

.commodity .upLoadImg{
    width: 120px;
    height: 120px; 
}
.editGoods{
    cursor: pointer;
}
.commodity_b .confirmPopup_b{
    width: 100%;
    height: 100%;
    /* background-color:rgba(0, 0, 0, 0.2); */
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
}
.commodity_b .confirmPopup{
  width: 400px;
  height: 189px;
  /* padding-top: 20px; */
  margin: 375px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  /* margin-top: 500px; */
  z-index: 999;
  background-color: #fff;
  /* border-radius: 20px; */
  /* color: #ccc; */
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.commodity_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.commodity_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.commodity_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE;
    color: #fff; */
    background: #ccc;
    color: #fff;
    cursor: pointer;
}
.commodity_b .confirmTitle{
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
.commodity_b .goBack{
    display: inline-block;
    width: 130px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    background-color: #00C1DE;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
}
/* .commodity_b .commodity{
    height: 750px;
} */
.editTitle{
    display: inline-block;
    margin-bottom: 20px;
    font-weight: bold;
}
</style>
<style scoped lang=less>
@import '../../css/taskModule.less';
</style>