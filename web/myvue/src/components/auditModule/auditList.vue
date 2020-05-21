<template>
  <div class="auditList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>折扣审核列表</span></div></el-col>
                        <!-- <el-col :span="3"><div>业务键值：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="bus_value" placeholder="请输入业务键值"></el-input></div></el-col>
                        <el-col :span="3"><div>操作名称：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="ope_module_name" placeholder="请输入操作名称"></el-input></div></el-col>
                        <el-col :span="3"><div>操作人：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="admin_name" placeholder="请输入操作人"></el-input></div></el-col> -->
                    </el-row>
                    <el-row>
                        <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>审核项</th>
                                    <th>审核单号</th>
                                    <th>审核状态</th>
                                    <th>创建时间</th>
                                    <th>是否需要审核</th>
                                    <th>配置编号</th>
                                    <th>当前审核人</th>
                                    <th>查看详情</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in tableData">
                                    <td>{{item.display_name}}</td>
                                    <td>{{item.audit_sn}}</td>
                                    <td>{{item.status_desc}}</td>
                                    <td>{{item.create_time}}</td>
                                    <td>{{item.audit_desc}}</td>
                                    <td>{{item.config_sn}}</td>
                                    <td>{{item.current_auditor}}</td>
                                    <td>
                                        <span class="notBgButton" v-if="item.api_id==36" @click="viewDetails(item.audit_sn)">查看详情</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </el-row>
                    <notFound v-if="isShow"></notFound>
                    <el-row>
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
import axios from 'axios'
import { Loading } from 'element-ui'
import notFound from '@/components/UiAssemblyList/notFound'
import fontWatermark from '@/components/UiAssemblyList/fontWatermark'
export default {
  components:{
      notFound,
      fontWatermark,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      total:0,//默认数据总数
      pagesize:15,//每页条数默认15条
      page:1,//page默认为1
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
        axios.post(vm.url+vm.$authListURL,
            {},
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.auditList.total==undefined){
                vm.isShow=true;
            }else if(res.data.auditList.total!=0){
                vm.tableData=res.data.auditList.data;
                vm.total=res.data.auditList.total;
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
    //查看详情
    viewDetails(sn){
        let vm = this;
        axios.post(vm.url+vm.$discountAuditDetailURL,
            {
                "audit_sn":sn
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            if(res.data.auditDetail!=0){
                vm.$router.push('/auditDetails?audit_sn='+sn);
                sessionStorage.setItem('tableData',JSON.stringify(res.data));
            }else{

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

<style scoped>
@import '../../css/publicCss.css';
</style>
