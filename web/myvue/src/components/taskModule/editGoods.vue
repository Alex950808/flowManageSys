<template>
  <div class="editGoods_b">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="title">
                        <span class="bgButton" @click="backUpPage()">返回上一页</span>
                    </div>
                    <div class="editGoods">
                        <span class="Required">以下为必填字段</span>
                        <el-row>
                            <el-col :span="8">
                                <div class="newGoodsOneLeft">
                                    <span>请选择品牌&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                    <template>
                                        <el-select v-model="brand" filterable placeholder="请选择品牌">
                                            <el-option v-for="item in brandList" :key="item.id" :label="item.name" :value="item.id">
                                            </el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="14">
                                <div class="goodsName">
                                <span>请输入商品名称</span>
                                    <el-input v-model="goods_name" style="width:217px;" placeholder="请输入商品名称"></el-input>
                                </div>
                            </el-col>
                        </el-row>
                        <el-row class="textCenter">
                            <el-col :span="8">
                                <div class="">
                                    <div class="block">
                                    <span>请选择商品类别</span>
                                        <el-cascader :options="optionsWithDisabled" v-model="selectedOptions" placeholder="请选择商品类别" @change="handleChange">
                                        </el-cascader>
                                    </div>
                                </div>
                            </el-col>
                            <!-- <el-col :span="8">
                                <div class="specPrice">
                                    <span>请输入商品价格</span>
                                    <el-input v-model="spec_price" style="width:217px;" placeholder="请输入商品价格"></el-input>
                                </div>
                            </el-col> -->
                        </el-row>
                        <!-- <el-row class="textCenter" style="line-height:0;">
                            <el-col :span="8">
                                <div class="specWeight">
                                    <span>请输入商品重量</span>
                                    <el-input v-model="spec_weight" style="width:217px;" placeholder="请输入商品重量"></el-input>
                                </div>
                            </el-col>
                        </el-row> -->
                        <!-- <div>以下为选填字段</div>
                        <el-row class="textCenter">
                            <el-col :span="8">
                                <div class="erpMerchantNo">
                                    <span>请输入商家编码</span>
                                    <el-input v-model="erp_merchant_no" style="width:217px;" placeholder="请输入商家编码"></el-input>
                                </div>
                                </el-col>
                            <el-col :span="8">
                                <div class="erpPrdNo">
                                    <span>请输入商品代码</span>
                                    <el-input v-model="erp_prd_no" style="width:217px;" placeholder="请输入商品代码"></el-input>
                                </div>
                            </el-col>
                            <el-col :span="8">
                                <div class="erpRefNo">
                                    <span>请输入商品参考码</span>
                                    <el-input v-model="erp_ref_no" style="width:217px;" placeholder="请输入商品参考码"></el-input>
                                </div>
                            </el-col>
                        </el-row> -->
                        <div class="editGoodsButton"><span class="bgButton" @click="editGoodsButton()">确定编辑</span></div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
    </el-row>
  </div>
</template>

<script>
import axios from 'axios';
import { bgHeight } from '../../filters/publicMethods.js';//引入有公用方法的jsfile
export default {
  name: 'App',
  data(){
    return{
      url: `${this.$baseUrl}`,
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
      goods_name:'',//商品名称
      erp_merchant_no:'',//商家编码
      erp_prd_no:'',//商品代码
      erp_ref_no:'',//商品参考码
      goodsSn:'',

    //   compare:[],
    }
  },
  mounted(){
    //   this.openAddGoods();
    //   setTimeout(this.openProductEditor(),1000);
      this.openProductEditor()
  },
  methods:{
      handleChange(value) {
      },
      backUpPage(){
          let vm=this;
          vm.$router.push('/commodity');
      },
      openAddGoods(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          axios.get(vm.url+vm.$addGoodsURL,
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            }
          ).then(function(res){
              if(res.data.data.goodsBrandInfo.length!=0){
                  res.data.data.goodsBrandInfo.forEach(element => {
                      vm.brandList.push({"id":element.brand_id,"name":element.name});
                  });
              }
            //   vm.compare=res.data.data.goodsCateInfo;
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
              sessionStorage.setItem("optionsWith",JSON.stringify(res.data.data.goodsCateInfo));
            //   if(res.data.data.goodsStorehouseInfo.length!=0){
            //       res.data.data.goodsStorehouseInfo.forEach(element=>{
            //           vm.PlaceDelivery.push({"value":element.store_id,"label":element.store_location});
            //       })
                  
            //   }
          }).catch(function (error) {
                // vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
      //打开商品编辑页面获取数据
      openProductEditor(){
            let vm=this;
            var tableDataStr=JSON.parse(sessionStorage.getItem("commodityEditor"));
            vm.goodsSn=tableDataStr.goods_info.goods_sn;
            if(tableDataStr.goods_brand_info.length!=0){
                  tableDataStr.goods_brand_info.forEach(element => {
                      vm.brandList.push({"id":element.brand_id,"name":element.name});
                  });
              }
            //   vm.compare=res.data.data.goodsCateInfo;
              if(tableDataStr.goods_cat_info.length!=0){
                  tableDataStr.goods_cat_info.forEach(element=>{
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
              if(tableDataStr.goods_info.brand_id!=0){
                  vm.brand=tableDataStr.goods_info.brand_id;
              }
            vm.goods_name=tableDataStr.goods_info.goods_name;
            // vm.DeliveryId=tableDataStr.storehouse_id;
            // vm.spec_weight=tableDataStr.spec_weight;
            // vm.spec_price=tableDataStr.spec_price;
            // vm.erp_merchant_no=tableDataStr.erp_merchant_no;
            // vm.erp_prd_no=tableDataStr.erp_prd_no;
            // vm.erp_ref_no=tableDataStr.erp_ref_no;
            // var optionsWith=JSON.parse(sessionStorage.getItem("optionsWith"));
            if(tableDataStr.goods_info.cat_id!=0){
                tableDataStr.goods_cat_info.forEach(element=>{
                    element.child.forEach(elementO=>{
                        elementO.child.forEach(elementT=>{
                            if(elementT.cat_id==tableDataStr.goods_info.cat_id){
                                vm.selectedOptions.push(element.cat_id,elementO.cat_id,elementT.cat_id);
                            }
                        })
                    })
                })
            }
            
      },
      editGoodsButton(){
          let vm=this;
          let headersToken=sessionStorage.getItem("token");
          axios.post(vm.url+vm.$doEditGoodsURL,
            {
                "goods_name":vm.goods_name,
                "cat_id":vm.selectedOptions[2],
                "brand_id":vm.brand,
                // "spec_price":vm.spec_price,
                // "spec_weight":vm.spec_weight,
                "goods_id":vm.$route.query.goods_id,
                "goods_sn":vm.goodsSn,
                // //选填
                // "erp_merchant_no":vm.erp_merchant_no,
                // "erp_prd_no":vm.erp_prd_no,
                // "erp_ref_no":vm.erp_ref_no,
            },
            {
                headers:{
                    'Authorization': 'Bearer ' + headersToken,
                    'Accept': 'application/vnd.jmsapi.v1+json',
                }
            } 
          ).then(function(res){
              if(res.data.code==1000){
                  vm.$router.push('/commodity');
              }else{
                  vm.$message(res.data.msg);
              }
          }).catch(function (error) {
                // vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      }
  }
}
</script>

<style>
/* @import '../../css/common.css'; */
.editGoods .file {
    position: relative;
    display: inline-block;
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 1px solid #ccc;
    overflow: hidden;
    text-decoration: none;
    text-indent: 0;
    line-height: 50px;
    text-align: center;
    margin-top: 25px;
    /* margin-left: 300px; */
}
.textCenter{
    line-height: 150px;
    height: 150px;
}
.editGoods .file img{
  padding-top: 20px;
}
.editGoods .Required{
    margin-bottom: 60px;
    display: inline-block;
}
.editGoods .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 19px;
}
.editGoods .file input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
}
.editGoods_b .title{
    height: 75px;
    background-color: #ebeef5;
    line-height: 75px;
    font-size: 18px;
    color: #000;
    font-weight:bold;
    padding-left: 35px;
    margin-top: 20px;
    margin-bottom: 30px;
    position: relative;
}
.editGoods_b .editGoodsButton{
    text-align: right;
}
</style>
