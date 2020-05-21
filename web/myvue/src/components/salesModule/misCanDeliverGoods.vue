<template>
  <div class="misCanDeliverGoods">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                  <tableTitle :tableTitle='tableTitle,config'></tableTitle>
                  <div class="spotGoods">
                      <span class="spotGoodsTitle lineHeightForty fontWeight PL_twenty">现货商品表</span>&nbsp;&nbsp;&nbsp;
                      <el-checkbox v-model="spotGoods" size="mini" label="选择该现货表" border></el-checkbox>
                      <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                          <thead>
                              <tr>
                                <th>商品名称</th>
                                <th>现货商品号</th>
                                <th>商品规格码</th>
                                <th>商家编码</th>
                                <th>总需求量</th>
                                <th>美金原价</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr v-for="(item,index) in postData">
                                  <td>{{item.goods_name}}<input type="hidden" name="spotOrderSn" :value="item.spot_order_sn"/></td>
                                  <td>{{item.spot_order_sn}}</td>
                                  <td>{{item.spec_sn}}</td>
                                  <td>{{item.erp_merchant_no}}</td>
                                  <td>{{item.goods_number}}</td>
                                  <td>{{item.spec_price}}</td>
                              </tr>
                          </tbody>
                      </table>
                  </div>
                  <div>
                      <span class="spotGoodsTitle lineHeightForty fontWeight PL_twenty">需求商品表</span>
                      <table class="tableStyle" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                          <thead>
                              <tr>
                                <th>选择需求商品</th>
                                <th>商品名称</th>
                                <th>需求商品单号</th>
                                <th>商品规格码</th>
                                <th>商家编码</th>
                                <th>总需求量</th>
                                <th>可发货数量</th>
                                <th>美金原价</th>
                                <th>发货数量</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr v-for="(item,index) in demandData">
                                  <td class="checkbox"><input name="save" type="checkbox"/></td>
                                  <td>{{item.goods_name}}</td>
                                  <td>{{item.sub_order_sn}}</td>
                                  <td>{{item.spec_sn}}</td>
                                  <td>{{item.erp_merchant_no}}</td>
                                  <td>{{item.goods_number}}</td>
                                  <td class="can_deliver_num">{{item.can_deliver_num}}<input name="deliver" :value="item.can_deliver_num" type="hidden"/></td>
                                  <td>{{item.spec_price}}</td>
                                  <td class="demandInput"><input style="width:50px;" @change="isGreaterThan(index)" :name="index"/></td>
                              </tr>
                          </tbody>
                      </table>
                      <div class="fontCenter lineHeighteighty">
                          <span class="bgButton" @click="dialogVisible=true;clearData()">
                              确认提交
                              <span style="display: none" class="el-icon-loading confirmLoading"></span>
                          </span>
                      </div>
                      <el-dialog title="添加详细信息" :visible.sync="dialogVisible" width="800px">
                        <el-row class="lineHeightForty MB_twenty">
                            <el-col :span="4"><div>交货类别：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="delivery_type" placeholder="请选择交货类别">
                                        <el-option v-for="item in delivery_types" :key="item.id" :label="item.label" :value="item.id">
                                        </el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                            <el-col :span="4" :offset="1"><div class="">运输方式：</div></el-col>
                            <el-col :span="6">
                                <div>
                                    <template>
                                        <el-select v-model="ship_type" placeholder="请选择运输方式">
                                        <el-option v-for="item in ship_types" :key="item.id" :label="item.label" :value="item.id">
                                        </el-option>
                                        </el-select>
                                    </template>
                                </div>
                            </el-col>
                        </el-row>
                        <span slot="footer" class="dialog-footer">
                            <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                            <el-button type="primary" @click="dialogVisible = false;submitData()">确 定</el-button>
                        </span>
                      </el-dialog>
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
import tableTitle from '@/components/UiAssemblyList/tableTitle'
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      tableTitle,
      notFound,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      postData:[],
      demandData:[],
      tableTitle:'YD订单下可发货的商品',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"/misSubOrderList?mis_order_sn='+this.$route.query.mis_order_sn+'"}]',
      isShow:false,
      spotGoods:false,
      delivery_types:[{"label":"香港fob交货","id":1},{"label":"保税CIF交货","id":2},{"label":"香港DDP交货","id":3},{"label":"其他","id":4}],
      delivery_type:'',
      ship_types:[{"label":"空运","id":1},{"label":"陆运","id":2},{"label":"海运","id":3}],
      ship_type:'',
      dialogVisible:false,
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
        axios.get(vm.url+vm.$misCanDeliverGoodsURL+'?sub_order_sn='+vm.$route.query.sub_order_sn,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code==1000){
                vm.postData=res.data.data.spot_goods_list;
                vm.demandData=res.data.data.demand_goods_list;
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
    //判断是否大于 
    isGreaterThan(index){
        let vm = this;
        let pre_ship_num=$(".demandInput input[name="+index+"]").val();
        let can_deliver_num=$(".demandInput input[name="+index+"]").parent().parent().find(".can_deliver_num").find("input[name=deliver]").val();
        if(parseInt(pre_ship_num)>parseInt(can_deliver_num)){
            $(".demandInput input[name="+index+"]").val('')
            vm.$message('发货数量不能大于可发货数量！');
            return false;
        }else if(parseInt(pre_ship_num)==0){
            $(".demandInput input[name="+index+"]").val('')
            vm.$message('发货数量不能为0！');
            return false;
        }
    },
    //确认提交
    submitData(){
        let vm = this;
        $(".confirmLoading").show();
        let i=0;
        let deliver_info=[];
        deliver_info.splice(0);
        let isFalse=0;
        vm.demandData.forEach(element => {
            let check=$(".demandInput input[name="+i+"]").parent().parent().find(".checkbox").find("input[name=save]").prop("checked");
            let pre_ship_num=$(".demandInput input[name="+i+"]").val();
            if(check==true){
                if(pre_ship_num==''){
                    vm.$message('您选中的第'+(i+1)+'条未添发货数量请完整填写!');
                    isFalse=1;
                }else if(parseInt(pre_ship_num)==0){
                    vm.$message('您选中的第'+(i+1)+'条发货数为0请重新填写!');
                    isFalse=1;
                }
                deliver_info.push({"spec_sn":element.spec_sn,"pre_ship_num":pre_ship_num});
            }else{
                if(pre_ship_num!=''){
                    vm.$message('您修改的第'+(i+1)+'并未选中，请选中后修改!');
                    isFalse=1;
                }
            }
            i++;
        });
        // isFalse用来判断填写是否为空或者是否为0 在循环中直接return false只会结束forEach而不会结束方法，所以只在判断中记录状态
        if(isFalse==1){
            return false;
        }
        let dataStr;
        if(vm.spotGoods==true){
        let spotOrderSn=$("input[name=spotOrderSn]").val();
            dataStr={"sub_order_sn":vm.$route.query.sub_order_sn,"deliver_info":deliver_info,"delivery_type":vm.delivery_type,"ship_type":vm.ship_type,"spot_order_sn":spotOrderSn}
        }else if(vm.spotGoods==false){
            dataStr={"sub_order_sn":vm.$route.query.sub_order_sn,"deliver_info":deliver_info,"delivery_type":vm.delivery_type,"ship_type":vm.ship_type}
        }
        axios.post(vm.url+vm.$comfirmDeliverGoodsURL,dataStr,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            $(".confirmLoading").hide();
            if(res.data.code==1000){
                vm.$message(res.data.msg);
                vm.$router.push('/misSubOrderList?mis_order_sn='+vm.$route.query.mis_order_sn+'');
            }else{
                vm.$message(res.data.msg);
            }
        }).catch(function (error) {
            vm.loading.close();
            $(".confirmLoading").hide();
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    clearData(){
        let vm = this;
        vm.delivery_type='';
        vm.ship_type='';
    }
  }
}
</script>

<style scoped>
.misCanDeliverGoods .tableStyle tr{
    line-height: 40px;
}
.misCanDeliverGoods .tableStyle tr th,td{
    border-top: 1px solid #ebeef5;
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5;
}
.misCanDeliverGoods .tableStyle tr:nth-child(even){
    background:#fafafa;
}
.misCanDeliverGoods .tableStyle tr:hover{
    background:#ced7e6;
}
</style>
