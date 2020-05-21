<template>
  <div class="commoditySpecification commodity_b"  @click="removeCursor()">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-row>
                    <el-col :span="22" :offset="1">
                        <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                            <el-col :span="3"><div><span class="fontWeight"><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;平台商品列表</span></div></el-col>
                            <el-col :span="3"><div>商品规格码</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="spec_sn" placeholder="请输入商品规格码"></el-input></div></el-col>
                            <el-col :span="3"><div>品牌名称</div></el-col>
                            <el-col :span="3"><div><el-input v-model="brand_name" placeholder="请输入品牌名称"></el-input></div></el-col>
                            <el-col :span="3"><div>商品名称</div></el-col>
                            <el-col :span="3"><div><el-input v-model="goods_name" placeholder="请输入商品名称"></el-input></div></el-col>
                            <el-col :span="3">
                                <div>
                                    <span class="bgButton" @click="searchFrame()">点击搜索</span>&nbsp;&nbsp;&nbsp;
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="fontCenter MB_ten lineHeightForty">
                            <el-col :span="3" :offset="3"><div>商家编码</div></el-col>
                            <el-col :span="3" ><div><el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input></div></el-col>
                            <el-col :span="3"><div>商品代码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_prd_no" placeholder="请输入商品代码"></el-input></div></el-col>
                            <el-col :span="3"><div>参考码</div></el-col>
                            <el-col :span="3"><div><el-input v-model="erp_ref_no" placeholder="请输入参考码"></el-input></div></el-col>
                            <el-col :span="3">
                                <div>
                                    <span class="bgButton" @click="openAddGoods()">新增商品</span>&nbsp;&nbsp;&nbsp;
                                </div>
                            </el-col>
                        </el-row>
                        <el-dialog title="新增商品" :visible.sync="dialogVisibleAdd" width="800px" center>
                            <span>以下为必填字段<span class="redFont">(实际重量与预估重量为二选一必填)</span></span>
                            <el-row class="lineHeightForty MB_twenty">
                               <el-col :span="4">
                                    <span class="redFont">*</span>商品名称:
                                </el-col>
                                <el-col :span="6">
                                     <div class="block">
                                        <el-input v-model="goods_name" placeholder="请输入商品名称"></el-input>
                                    </div>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <span class="redFont">*</span>商品类别:
                                </el-col>
                                <el-col :span="6">
                                     <el-cascader :options="optionsWithDisabled" v-model="catName" placeholder="请选择商品类别" @change="handleChange"></el-cascader>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                               <el-col :span="4">
                                    <span class="redFont">*</span>商品品牌:
                                </el-col>
                                <el-col :span="6">
                                     <template>
                                        <el-select v-model="brandName" filterable placeholder="请选择品牌">
                                            <el-option v-for="item in brandList" :key="item.name" :label="item.name" :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <span class="redFont">*</span>商家编码:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                               <el-col :span="4">
                                    <span class="redFont">*</span>美金原价:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="spec_price" placeholder="请输入美金原价"></el-input>
                                </el-col>
                               <el-col :span="4" :offset="1">
                                    <span class="redFont">*</span>exw折扣:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="exw_discount" placeholder="请输入exw折扣"></el-input>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                               <el-col :span="4">
                                    <span class="redFont">*</span>预估重量:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="estimate_weight" placeholder="请输入预估重量"></el-input>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    <span class="redFont">*</span>实际重量:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="spec_weight" placeholder="请输入实际重量"></el-input>
                                </el-col>
                            </el-row>
                            <span>以下为非必填字段</span>
                            <el-row class="lineHeightForty MB_twenty">
                               <el-col :span="4">
                                    商品代码:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="erp_prd_no" placeholder="请输入商品代码"></el-input>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    商品参考码:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="erp_ref_no" placeholder="请输入商品参考码"></el-input>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4">
                                    库存:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="stock_num" placeholder="请输入库存"></el-input>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    考拉码:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="KAOLA_NO" placeholder="请输入考拉码"></el-input>
                                </el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                               <el-col :span="4">
                                    小红书码:
                                </el-col>
                                <el-col :span="6">
                                    <el-input v-model="SMALL_RED_BOOK" placeholder="请输入小红书码"></el-input>
                                </el-col>
                                <el-col :span="4" :offset="1">
                                    标签:
                                </el-col>
                                <el-col :span="9">
                                    <el-checkbox-group class="d_I_B" v-model="checkList">
                                        <el-checkbox v-for="item in dataList.goodsLabelInfo" :label="item.id" :key="item.id"><span :style="bgColor(item.label_color)">{{item.label_name}}</span></el-checkbox>
                                    </el-checkbox-group>
                                </el-col>
                            </el-row>
                            <el-row style="height: 150px;">
                                <el-col :span="4">
                                    商品图片:
                                </el-col>
                                <el-col :span="3">
                                    <div class="" style="width:150px;height:150px;position: absolute;">
                                        <img v-if="img_url!=''" class="w_h_ratio" :src="img_url">
                                        <img v-if="img_url==''" class="w_h_ratio" src="../../image/upDateImg.jpg">
                                        <form id="forms" method="post" enctype="multpart/form-data" class="formData" style="display: inline-block;left: 2px;top: 3px;">
                                            <input id="files" class="file" type="file" @change="upData('A')"/>
                                        </form>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisibleAdd = false;">取 消</el-button>
                                <el-button type="primary" @click="doAddGoods()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <table class="fontCenter w_ratio" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <th style="width:200px;">商品名称</th>
                                <th style="width:100px;">品牌名称</th>
                                <th style="width:200px;">商品规格码</th>
                                <th class="widthTwoHundred">商家编码</th>
                                <th class="widthOneFiveHundred">商品代码</th>
                                <th class="widthOneFiveHundred">参考码</th>
                                <th class="widthTwoHundred">美金原价</th>
                                <th class="widthOneHundred">商品重量</th>
                                <th class="widthOneHundred">库存</th>
                                <th class="widthOneHundred">exw折扣</th>
                                <th class="widthTwoHundred">考拉码</th>
                                <th class="widthTwoHundred">小红书码</th>
                                <th style="width:100px;">图片</th>
                                <th style="width:100px;">操作</th>
                            </tr>
                            <tr  v-for="(item,index) in tableData">
                                <td class="overOneLinesHeid" style="width:200px;" :title="item.goods_name">
                                    <span style="-webkit-box-orient: vertical;width:200px;">{{item.goods_name}}</span>
                                </td>
                                <td class="overOneLinesHeid" style="width:100px;" :title="item.brand_name">
                                    <span style="-webkit-box-orient: vertical;width:100px;">{{item.brand_name}}</span>
                                </td>
                                <td class="widthTwoHundred">{{item.spec_sn}}</td>
                                <td class="widthTwoHundred">{{item.erp_merchant_no}}</td>
                                <td class="widthOneFiveHundred">{{item.erp_prd_no}}</td>
                                <td class="widthOneFiveHundred">{{item.erp_ref_no}}</td>
                                <td class="widthTwoHundred">{{item.spec_price}}</td>
                                <td class="widthOneHundred">{{item.spec_weight}}</td>
                                <td class="widthOneHundred">{{item.stock_num}}</td>
                                <td class="widthOneHundred">{{item.exw_discount}}</td>
                                <td class="widthTwoHundred">{{item.KOALA_NO}}</td>
                                <td class="widthTwoHundred">{{item.SMALL_RED_BOOK}}</td>
                                <td style="width:100px;position: relative;">
                                    <img v-if="item.spec_img!=''" @click="magnifier(index)" style="width:30px;height:30px;vertical-align: -10px;" :src="imgUrl+item.spec_img"/>
                                    <div :class="['goodsImg','goodsImg'+index]" @click="aaa()" style="display: none;">
                                        <img v-if="item.spec_img!=''" class="w_h_ratio" :src="imgUrl+item.spec_img">
                                    </div>
                                </td>
                                <td style="width:100px;"><span class="notBgButton" @click="seeDetail(item.spec_sn)">详情</span></td>
                            </tr>
                        </table>
                        <notFound v-if="isShow"></notFound>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-col>
                </el-row>
            </el-row>
        </el-col>
    </el-row>
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui'
import searchBox from '../UiAssemblyList/searchBox'
import upDataButton from '@/components/UiAssemblyList/upDataButton'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
    components:{
        searchBox,
        upDataButton,
        fontWatermark,
    },
  data(){
    return{
      url: `${this.$baseUrl}`,
      downloadUrl:`${this.$downloadUrl}`,
      imgUrl:`${this.$imgUrl}`,
      tableData:[],
      search:'',
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      //搜索字段 
      goods_name:'',
      brand_name:'',
      spec_sn:'',
      erp_merchant_no:'',
      erp_ref_no:'',
      erp_prd_no:'',
      //新增
      dataList:[],
      dialogVisibleAdd:false,
      goods_name:'',
      catName:[],
      optionsWithDisabled:[],
      brandName:'',
      brandList:[],
      erp_merchant_no:'',
      erp_ref_no:'',
      spec_price:'',
      spec_weight:'',
      estimate_weight:'',
      stock_num:'',
      exw_discount:'',
      KAOLA_NO:'',
      SMALL_RED_BOOK:'',
    //   goods_label:[],
      checkList:[],
      img_url:'',
      imgFiles:'',
    }
  },
  mounted(){
      this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
      this.getDataList();
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
  },
  methods:{
    handleChange(value) {
    },
    getDataList(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$goodsListURL+"?page="+vm.page+"&page_size="+vm.pagesize+"&query_sn="+vm.search+"&goods_name="+vm.goods_name
        +"&brand_name="+vm.brand_name+"&spec_sn="+vm.spec_sn+"&erp_merchant_no="+vm.erp_merchant_no+"&erp_prd_no="+vm.erp_prd_no+"&erp_ref_no="+vm.erp_ref_no,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
        ).then(function(res){
            vm.loading.close()
            vm.tableData=res.data.data.data;
            vm.total=res.data.data.total;
            if(res.data.code!=1000){
                vm.$message(res.data.msg);
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
    //搜索框
    searchFrame(e){
        let vm=this;
        vm.search=e;
        vm.page=1;
        vm.getDataList();
    },
     //查看详情 
    seeDetail(spec_sn){
        let vm = this;
        vm.$router.push('/goodsDetail?spec_sn='+spec_sn);
    },
    //选择上传的图片
    upData(status){
        let vm = this;
        let file = document.getElementById("files").files[0];
        let reader = new FileReader();
        reader.readAsDataURL(file);
        //监听文件读取结束后事件
        reader.onloadend = function (e) {
            vm.img_url=e.target.result;
            vm.imgFiles = e
        };
    },
    openAddGoods(){
        let vm=this;
        let headersToken=sessionStorage.getItem("token");
        vm.dialogVisibleAdd=true;
        vm.brandList.splice(0);
        vm.optionsWithDisabled.splice(0);
        vm.goods_name='',
        vm.catName=[],
        vm.brandName='',
        vm.erp_merchant_no='',
        vm.erp_prd_no='',
        vm.erp_ref_no='',
        vm.spec_price='',
        vm.spec_weight='',
        vm.estimate_weight='',
        vm.stock_num='',
        vm.exw_discount='',
        // vm.checkList='',
        vm.erp_merchant_no='',
        vm.KAOLA_NO='',
        vm.SMALL_RED_BOOK='',
        vm.erp_prd_no='',
        axios.get(vm.url+vm.$addGoodsURL,
        {
            headers:{
                'Authorization': 'Bearer ' + headersToken,
                'Accept': 'application/vnd.jmsapi.v1+json',
            }
        }
        ).then(function(res){
            vm.dataList=res.data.data;
            if(res.data.data.goodsBrandInfo.length!=0){
                res.data.data.goodsBrandInfo.forEach(element => {
                    vm.brandList.push({"id":element.brand_id,"name":element.name});
                });
            }
            if(res.data.data.goodsCateInfo.length!=0){
                res.data.data.goodsCateInfo.forEach(element=>{
                    let childrenO=[];
                    element.child.forEach(elementO=>{
                        let childrenT=[];
                        if(elementO.child!=undefined){
                            elementO.child.forEach(elementT=>{
                                childrenT.push({"value":elementT.cat_id,"label":elementT.cat_name})
                            })
                        }
                        childrenO.push({"value":elementO.cat_id,"label":elementO.cat_name,"children":childrenT})
                    })
                    vm.optionsWithDisabled.push({"value":element.cat_id,"label":element.cat_name,"children":childrenO})
                }) 
            }
        }).catch(function (error) {
            if(error.response.status=="401"){
            vm.$message('登录过期,请重新登录!');
            sessionStorage.setItem("token","");
            vm.$router.push('/');
            }
        });
    },
    //确认新增商品  
    doAddGoods(){
        let vm = this;
        if(vm.goods_name==''){
            vm.$message('商品名称不能为空!');
            return false;
        }
        if(vm.catName[2]==undefined){
            vm.$message('商品类别不能为空!');
            return false;
        }
        if(vm.brandName==''){
            vm.$message('商品品牌不能为空!');
            return false;
        }
        if(vm.erp_merchant_no==''){
            vm.$message('商家编码不能为空!');
            return false;
        }
        if(vm.spec_price===''){
            vm.$message('美金原价不能为空!');
            return false;
        }
        if(vm.exw_discount===''){
            vm.$message('EXW折扣不能为空!');
            return false;
        }
        if(vm.spec_weight===''&&vm.estimate_weight===''){
            vm.$message('实际重量与预估重量为二选一必填!');
            return false;
        }
        vm.dialogVisibleAdd=false;
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        let checkList = '';
        vm.checkList.forEach(element=>{
            checkList+=element+",";
        })
        checkList=(checkList.slice(checkList.length-1)==',')?checkList.slice(0,-1):checkList;
        var formDate = new FormData($("#forms")[0]);
        formDate.append('spec_img', document.getElementById("files").files[0]);
        $.ajax({
        url: vm.url+vm.$doAddGoodsURL+"?goods_name="+vm.goods_name+"&cat_id="+vm.catName[2]+"&brand_id="+vm.brandName+"&erp_merchant_no="+vm.erp_merchant_no+"&erp_prd_no="+vm.erp_prd_no+"&erp_ref_no="+vm.erp_ref_no+
            "&spec_price="+vm.spec_price+"&spec_weight="+vm.spec_weight+"&estimate_weight="+vm.estimate_weight+"&stock_num="+vm.stock_num+"&exw_discount="+vm.exw_discount+"&goods_label="+checkList+"&ERP_NO="+vm.erp_merchant_no+
            "&KAOLA_NO="+vm.KAOLA_NO+"&SMALL_RED_BOOK="+vm.SMALL_RED_BOOK+"&ERP_PRD_NO="+vm.erp_prd_no,
        type: "POST",
        async: false,
        // cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            vm.$message(res.data.msg);
            if(res.data.code=='1000'){
                vm.getDataList();
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
    magnifier(index){
        // let vm = this;
        window.event? window.event.cancelBubble = true : e.stopPropagation();
        $(".goodsImg"+index).fadeIn();
    },
    aaa(){
        window.event? window.event.cancelBubble = true : e.stopPropagation();
    },
    removeCursor(){
        // let vm = this;
        for(var i = 0;i<45;i++){
            $(".goodsImg"+i).fadeOut();
        }
    },
    bgColor(color){
        return "background-color:"+color;
    },
  }
}
</script>
<style scoped>
.formData{
    display: inline-block;
    position: absolute;
    right: 173px;
    top: 20px;
    opacity: 0;
    width: 100px;
    height: 100px;
}
.file{
    position: absolute;
    left: 0px;
    /* top: 20px; */
    opacity: 0;
    width: 100px;
    height: 100px;
}
.goodsImg{
    width: 450px;
    height: 450px;
    position: absolute;
    top: -205px;
    right: 146px;
    z-index: 99;
}
</style>
<style scoped>
@import '../../css/publicCss.css';
</style>