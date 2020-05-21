<template>
  <div class="rongFundList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <!-- <tableTitle :tableTitle='tableTitle,config'></tableTitle> -->
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                      <el-col :span="2" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>可融资金列表</span></div></el-col>
                      <el-col :span="2"><div>客户</div></el-col>
                      <el-col :span="3" >
                          <template>
                            <el-select v-model="sale_user_id" clearable placeholder="请选择客户">
                              <el-option v-for="item in selectData.sale_user_list" :key="item.id" :label="item.user_name" :value="item.id">
                              </el-option>
                            </el-select>
                          </template>
                      </el-col>
                      <el-col :span="3"><div>资金渠道名称</div></el-col>
                      <el-col :span="3"><div><el-input v-model="fund_channel_name" placeholder="请输入资金渠道名称"></el-input></div></el-col>
                      <el-col :span="3"><div>资金渠道类别</div></el-col>
                      <el-col :span="3">
                        <template>
                            <el-select v-model="fund_cat_id" clearable placeholder="请选择资金渠道类别">
                              <el-option v-for="item in selectData.fund_cat_list" :key="item.id" :label="item.fund_cat_name" :value="item.id">
                              </el-option>
                            </el-select>
                        </template>
                      </el-col>
                      <el-col :span="2"><span class="bgButton" @click="getDataList()">搜索</span></el-col>
                    </el-row>
                    <el-row class="tableStyle">
                        <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>资金渠道id</th>
                                    <th>资金渠道名称</th>
                                    <th>资金渠道类别</th>
                                    <th>客户</th>
                                    <th>人民币金额</th>
                                    <th>美金金额</th>
                                    <th>韩币金额</th>
                                    <th>编辑</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in tableData">
                                    <td>{{item.id}}</td>
                                    <td>{{item.fund_channel_name}}</td>
                                    <td>{{item.fund_cat_name}}</td>
                                    <td>{{item.sale_user_name}}</td>
                                    <td>{{item.cny}}</td>
                                    <td>{{item.usd}}</td>
                                    <td>{{item.krw}}</td>
                                    <td>
                                        <i class="iconfont editGoods" @click="openEdit(item.id)">&#xe62f;</i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <el-dialog title="编辑可融资金" :visible.sync="dialogVisibleEdit"  width="800px">
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4"><div>美金金额：</div></el-col>
                                <el-col :span="6"><div><el-input type="number" v-model="usd" placeholder="请输入美金金额"></el-input></div></el-col>
                                <el-col :span="4" :offset="1"><div class="">人民币金额：</div></el-col>
                                <el-col :span="6"><div><el-input type="number" v-model="cny" placeholder="请输入人民币金额"></el-input></div></el-col>
                            </el-row>
                            <el-row class="lineHeightForty MB_twenty">
                                <el-col :span="4"><div>韩币金额：</div></el-col>
                                <el-col :span="6"><div><el-input type="number" v-model="krw" placeholder="请输入韩币金额"></el-input></div></el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisibleEdit = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisibleEdit = false;doEdit()">确 定</el-button>
                            </span>
                        </el-dialog>
                        <notFound v-if="isShow"></notFound>
                        <el-pagination class="tablePage" background @size-change="handleSizeChange" @current-change="handleCurrentChange"
                            :page-sizes="[15, 25, 35, 45]" :page-size="15" layout="total, sizes, prev, pager, next, jumper" :total="total">
                        </el-pagination>
                    </el-row>
              </el-col>
            </el-row>
        </el-col>
      </el-row>
  </div>
</template>

<script>
import axios from 'axios';
import { Loading } from 'element-ui';
import tableTitle from '@/components/UiAssemblyList/tableTitle';
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      tableTitle,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      selectData:[],
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
      isShow:false,
      dialogVisibleEdit:false,
      tableTitle:'可融资金列表',
      config:'[{"Add":"false"},{"download":"false"},{"search":"false"},{"back":"false"},{"upDataStr":"false"}]',
      //编辑数据
      usd:'',
      cny:'',
      krw:'',
      fund_channel_id:'',
      //搜索
      fund_channel_name:'',
      fund_cat_id:'',
      sale_user_id:'',
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
        axios.get(vm.url+vm.$rongFundListURL+"?start_page="+vm.page+"&page_size="+vm.pagesize+"&fund_channel_name="+vm.fund_channel_name
        +"&fund_cat_id="+vm.fund_cat_id+"&sale_user_id="+vm.sale_user_id,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.data.fund_channel_info.data;
            vm.selectData=res.data.data;
            vm.total=res.data.data.fund_channel_info.total;
            if(res.data.data.fund_channel_info.data.length!=0){
                vm.isShow=false;
            }else{
                vm.isShow=true;
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
    //打开编辑框
    openEdit(id){
        let vm = this;
        vm.fund_channel_id=id;
        axios.get(vm.url+vm.$editRongFundURL+"?fund_channel_id="+id,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code=1000){
                vm.dialogVisibleEdit = true;
                vm.usd=res.data.data.fund_channel_info[0].usd;
                vm.krw=res.data.data.fund_channel_info[0].krw;
                vm.cny=res.data.data.fund_channel_info[0].cny;
            }else if(res.data.code=1002){
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
    //确认编辑
    doEdit(){
        let vm = this;
        axios.post(vm.url+vm.$doEditRongFundURL,
            {
                "fund_channel_id":vm.fund_channel_id,
                "usd":vm.usd,
                "cny":vm.cny,
                "krw":vm.krw,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            if(res.data.code==1000){
                vm.dialogVisibleEdit = false;
                vm.getDataList();
                vm.$message(res.data.msg);
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

<style scoped lang=less>
table{
    border: 1px solid #ebeef5;
    tr{
        line-height: 40px;
        th,td{
            border-bottom: 1px solid #ebeef5;
            border-left: 1px solid #ebeef5;
        }
    }
    tr:nth-child(even){
        background:#fafafa;
    }
    tr:hover{
        background:#ced7e6;
    }
}
</style>
