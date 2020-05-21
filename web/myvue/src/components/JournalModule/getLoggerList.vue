<template>
  <div class="getLoggerList">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <el-row class="MT_twenty fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" class="fontLift"><div><span class="fontWeight"><span class="coarseLine MR_ten"></span>日志列表</span></div></el-col>
                        <el-col :span="3"><div>业务键值：</div></el-col>
                        <el-col :span="3" ><div><el-input v-model="bus_value" placeholder="请输入业务键值"></el-input></div></el-col>
                        <el-col :span="3"><div>操作名称：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="ope_module_name" placeholder="请输入操作名称"></el-input></div></el-col>
                        <el-col :span="3"><div>操作人：</div></el-col>
                        <el-col :span="3"><div><el-input v-model="admin_name" placeholder="请输入操作人"></el-input></div></el-col>
                    </el-row>
                    <el-row class="fontCenter MB_ten lineHeightForty">
                        <el-col :span="3" :offset="3"><div>日期：</div></el-col>
                        <el-col :span="5">
                            <el-date-picker v-model="time" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期">
                            </el-date-picker>
                        </el-col>
                        <el-col :span="3" :offset="10">
                            <div>
                                <span class="bgButton MR_ten" @click="searchFrame()">搜索</span>
                            </div>
                        </el-col>
                    </el-row>
                    <el-row>
                        <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>日志标记</th>
                                    <th>业务描述</th>
                                    <th>业务健值</th>
                                    <th>操作人名称</th>
                                    <th>操作模块名称</th>
                                    <th>操作时间</th>
                                    <th>查看详情</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in tableData">
                                    <td>{{item.log_id}}</td>
                                    <td class="overOneLinesHeid fontLift" style="width:300px;">
                                        <el-tooltip class="item" effect="light" :content="item.bus_desc" placement="right">
                                        <span style="-webkit-box-orient: vertical;width:300px;">&nbsp;&nbsp;{{item.bus_desc.substring(0,30)}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td>{{item.bus_value}}</td>
                                    <td>{{item.admin_name}}</td>
                                    <td>{{item.ope_module_name}}</td>
                                    <td>{{item.create_time}}</td>
                                    <td>
                                        <span v-if="item.have_detail==1" class="notBgButton" @click="viewDetails(item.log_id)">查看详情</span>
                                        <span v-if="item.have_detail==0">暂无详情</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </el-row>
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
import { Loading } from 'element-ui';
import notFound from '@/components/UiAssemblyList/notFound';
import { dateToStr } from '@/filters/publicMethods.js';//引入有公用方法的jsfile
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
      bus_value:'',
      ope_module_name:'',
      admin_name:'',
      time:[],
    //   end_time:'',
    }
  },
  mounted(){
    this.headersStr={'Authorization': 'Bearer ' + sessionStorage.getItem("token"),'Accept':'application/vnd.jmsapi.v1+json'};
    this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'});
    this.getDataList();
  },
  methods:{
    getDataList(loa){
        let vm = this;
        let start_time='';
        let end_time='';
        if(vm.time!=null&&vm.time.length!=0){
            end_time=vm.time[1];
            end_time=dateToStr(end_time)
            start_time=vm.time[0];
            start_time=dateToStr(start_time) 
        }
        axios.post(vm.url+vm.$getLoggerListURL,
            {
                "bus_value":vm.bus_value,
                "start_time":start_time,
                "end_time":end_time,
                "ope_module_name":vm.ope_module_name,
                "admin_name":vm.admin_name,
                "page":vm.page,
                "page_size":vm.pagesize,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(loa!=null){
                loa.close();
            }
            if(res.data.loggerList.total != 0){
                vm.tableData = res.data.loggerList.data;
                vm.total=res.data.loggerList.total;
                vm.isShow=false;
            }else if(res.data.loggerList.total == 0){
                vm.tableData = [];
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
    //搜索
    searchFrame(){
        let vm = this;
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        vm.getDataList(loa);
    },
    viewDetails(log_id){
        let vm = this;
        vm.$router.push('/getLoggerDetail?log_id='+log_id);
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
