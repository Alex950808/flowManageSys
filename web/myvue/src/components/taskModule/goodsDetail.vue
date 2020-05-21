<template>
  <div class="goodsDetail">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine"></span>&nbsp;&nbsp;&nbsp;平台商品详情</span>
                        <!-- <searchBox @searchFrame='searchFrame'></searchBox> -->
                    </div>
                    <el-row style="margin-bottom: 10px;">
                        <span class="fontWeight F_S_twenty">商品信息：</span>
                    </el-row>
                    <div class="F_S_twenty fontWeight PL_twenty">
                        <div class="MB_twenty">
                            <span class="d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>商品名称:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="goods_name" placeholder="请输入商品名称"></el-input>
                            <span class="ML_Four d_I_B fontRight" style="width:120px;">商品代码:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="erp_prd_no" placeholder="请输入商品代码"></el-input>
                        </div>
                        <div class="MB_twenty">
                            <span class="d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>品牌名称:&nbsp;&nbsp;</span><template>
                                <el-select v-model="brand_name" filterable placeholder="请选择品牌">
                                    <el-option v-for="item in brandList" :key="item.id" :label="item.name" :value="item.id">
                                    </el-option>
                                </el-select>
                            </template>
                            <span class="ML_Four d_I_B fontRight" style="width:120px;">商品参考码:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="erp_ref_no" placeholder="请输入商品参考码"></el-input>
                        </div>
                        <div class="MB_twenty">
                            <span class="d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>商家编码:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="erp_merchant_no" placeholder="请输入商家编码"></el-input>
                            <!-- <span class="d_I_B" style="width:120px;"><span class="redFont">*</span>商品规格码:</span><el-input style="width:217px;" v-model="spec_sn" placeholder="请输入商品规格码"></el-input> -->
                            <span class="ML_Four d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>美金原价:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="spec_price" placeholder="请输入美金原价"></el-input>
                        </div>
                        <div class="MB_twenty">
                            <span class="d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>预估重量:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="estimate_weight" placeholder="请输入商家编码"></el-input>
                            <span class="ML_Four d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>实际重量:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="spec_weight" placeholder="请输入商品重量"></el-input>
                        </div>
                        <div class="MB_twenty">
                            
                        </div>
                        <div class="MB_twenty">
                            <span class="d_I_B fontRight" style="width:120px;">库存:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="stock_num" placeholder="请输入库存"></el-input>
                            <span class="ML_Four d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>exw折扣:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="exw_discount" placeholder="请输入exw折扣"></el-input>
                        </div>
                        <div class="MB_twenty">
                            <span class="d_I_B fontRight" style="width:120px;"><span class="redFont">*</span>商品类别:&nbsp;&nbsp;</span><el-cascader :options="optionsWithDisabled" v-model="catName" placeholder="请选择商品类别" @change="handleChange"></el-cascader>
                            <span class="ML_Four d_I_B fontRight" style="width:120px;">商品标签:&nbsp;&nbsp;</span>
                            <el-checkbox-group class="d_I_B" style="width:250px;" v-model="checkList">
                                <el-checkbox v-for="item in tableData.total_label_info" :label="item.label_name" :key="item.id"><span :style="bgColor(item.label_color)">{{item.label_name}}</span></el-checkbox>
                            </el-checkbox-group>
                        </div>
                        <div class="MB_twenty">
                            <span class="d_I_B fontRight" style="width:120px;">小红书码:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="SMALL_RED_BOOK" placeholder="请输入小红书码"></el-input>
                            <span class="ML_Four d_I_B fontRight" style="width:120px;">考拉码:&nbsp;&nbsp;</span><el-input style="width:217px;" v-model="KAOLA_NO" placeholder="请输入考拉码"></el-input>
                        </div>
                        <div class="MB_twenty" style="height:150px;">
                            <span class="d_I_B fontRight fontRight" style="width:120px;">商品图片:&nbsp;&nbsp;</span>
                            <div @mouseover="magnifier()" @mouseout="removeCursor()" style="width:150px;height:150px;position: absolute;display: inline-block;">
                                <img v-if="img_url!=''" class="w_h_ratio" :src="img_url">
                                <img v-if="img_url==''" src="../../image/upDateImg.jpg">
                                <form id="forms" method="post" enctype="multpart/form-data" class="formData" style="display: inline-block;left: 2px;top: 3px;">
                                    <input id="files" class="file" type="file" @change="upData('A')"/>
                                </form>
                            </div>
                            <div class="goodsImg" style="display: none;">
                                <img v-if="img_url!=''" class="w_h_ratio" :src="img_url">
                            </div>
                        </div>
                        <div class="MB_twenty">
                            <div>
                                <span class="bgButton" @click="getAddGoods()">确定</span>
                            </div>
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
export default {
  components:{
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      imgUrl:`${this.$imgUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      //编辑部分 
      goods_name:'',
      brand_name:'',
      brandList:[],
      spec_sn:'',
      erp_merchant_no:'',
      erp_prd_no:'',
      erp_ref_no:'',
      spec_weight:'',
      estimate_weight:'',
      exw_discount:'',
      stock_num:'',
      spec_price:'',
      checkList:[],
      optionsWithDisabled:[],
      SMALL_RED_BOOK:'',
      KAOLA_NO:'',
      catName:[],
      //上传图片
      img_url:'',
      imgFiles:'',
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
        axios.get(vm.url+vm.$goodsDetailURL+"?spec_sn="+vm.$route.query.spec_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.tableData=res.data.data;
                vm.goods_name=res.data.data.goods_info.goods_name;
                vm.brand_name=res.data.data.goods_info.brand_id;
                vm.spec_sn=res.data.data.goods_info.spec_sn;
                vm.erp_merchant_no=res.data.data.goods_info.erp_merchant_no;
                vm.erp_prd_no=res.data.data.goods_info.erp_prd_no;
                vm.erp_ref_no=res.data.data.goods_info.erp_ref_no;
                vm.spec_weight=res.data.data.goods_info.spec_weight;
                vm.exw_discount=res.data.data.goods_info.exw_discount;
                vm.stock_num=res.data.data.goods_info.stock_num;
                vm.spec_price=res.data.data.goods_info.spec_price;
                vm.KAOLA_NO=res.data.data.goods_info.KAOLA_NO;
                vm.SMALL_RED_BOOK=res.data.data.goods_info.SMALL_RED_BOOK;
                vm.estimate_weight=res.data.data.goods_info.estimate_weight;
                vm.goods_label=res.data.data.goods_info.goods_label.split(",");
                if(res.data.data.goods_info.spec_img!=''){
                    vm.img_url = vm.imgUrl+res.data.data.goods_info.spec_img;
                }
                if(res.data.data.goods_info.cat_id!==''){
                    res.data.data.cat_list.forEach(element=>{
                        if(element.child!=undefined){
                            element.child.forEach(elementO=>{
                                if(elementO.child!=undefined){
                                    elementO.child.forEach(elementT=>{
                                        if(elementT.cat_id==res.data.data.goods_info.cat_id){
                                            vm.catName.push(element.cat_id,elementO.cat_id,elementT.cat_id);
                                        }
                                    })
                                }
                            })
                        }
                    })
                }
                if(res.data.data.cat_list.length!=0){
                    res.data.data.cat_list.forEach(element=>{
                        let childrenO=[];
                        if(element.child!=undefined){
                            element.child.forEach(elementO=>{
                                let childrenT=[];
                                if(elementO.child!=undefined){
                                    elementO.child.forEach(elementT=>{
                                        childrenT.push({"value":elementT.cat_id,"label":elementT.cat_name})
                                    })
                                }
                                childrenO.push({"value":elementO.cat_id,"label":elementO.cat_name,"children":childrenT})
                            })
                        }
                        
                        vm.optionsWithDisabled.push({"value":element.cat_id,"label":element.cat_name,"children":childrenO})
                    }) 
                }
                res.data.data.brand_list.forEach(element=>{
                    vm.brandList.push({"id":element.brand_id,"name":element.name})
                })
                res.data.data.goods_info.goods_label_list.forEach(element => {
                    vm.checkList.push(element.id)
                });
                vm.isShow=false;
            }else if(res.data.code==1002){
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
    bgColor(color){
        return "background-color:"+color;
    },
    backUpPage(){
        let vm = this;
        vm.$router.push('/commodity'); 
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
    getAddGoods(){
        let vm = this;
        if(vm.goods_name==''){
            vm.$message('商品名称不能为空!');
            return false;
        }
        if(vm.catName[2]==undefined){
            vm.$message('商品类别不能为空!');
            return false;
        }
        if(vm.brand_name==''){
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
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        let checkList = '';
        vm.checkList.forEach(element=>{
            checkList+=element+",";
        })
        checkList=(checkList.slice(checkList.length-1)==',')?checkList.slice(0,-1):checkList;
        var formDate;
        if(document.getElementById("files").files[0]!=undefined){
            formDate = new FormData($("#forms")[0]);
            formDate.append('spec_img', document.getElementById("files").files[0]);
        }
        $.ajax({
        url: vm.url+vm.$doEditGoodsURL+"?goods_name="+vm.goods_name+"&cat_id="+vm.catName[2]+"&brand_id="+vm.brand_name+"&spec_sn="+vm.spec_sn+"&erp_merchant_no="+vm.erp_merchant_no+"&erp_prd_no="+vm.erp_prd_no+"&erp_ref_no="+vm.erp_ref_no+
            "&spec_price="+vm.spec_price+"&spec_weight="+vm.spec_weight+"&estimate_weight="+vm.estimate_weight+"&stock_num="+vm.stock_num+"&exw_discount="+vm.exw_discount+"&goods_label="+checkList+"&ERP_NO="+vm.erp_merchant_no+
            "&KAOLA_NO="+vm.KAOLA_NO+"&SMALL_RED_BOOK="+vm.SMALL_RED_BOOK+"&ERP_PRD_NO="+vm.erp_prd_no+"&goods_sn="+vm.tableData.goods_info.goods_sn,
        type: "POST",
        // async: false,
        // cache: false,
        headers:vm.headersStr,
        data: formDate,
        processData: false,
        contentType: false,
        success: function(res) {
            loa.close();
            vm.$message(res.msg);
            // if(res.data.code=="1000"){
            //     vm.getDataList();
            // }
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
    //鼠标绕上后图片放大
    magnifier(){
        let vm = this;
        $(".goodsImg").fadeIn();
    },
    removeCursor(){
        let vm = this;
        $(".goodsImg").fadeOut();
    }
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
    width: 150px;
    height: 150px;
}
.file{
    position: absolute;
    left: 0px;
    /* top: 20px; */
    opacity: 0;
    width: 150px;
    height: 150px;
}
.goodsImg{
    width: 600px;
    height: 600px;
    position: absolute;
    top: 100px;
    left: 411px;
    z-index: 99;
}
</style>
