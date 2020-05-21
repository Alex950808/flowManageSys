<template>
  <div class="getLoggerDetail">
      <el-row>
        <el-col :span="24" class="PR Height" style="background-color: #fff;">
            <el-row class="bgDiv">
                <fontWatermark :zIndex="false"></fontWatermark>
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle title">
                        <span class="bgButton MR_ten" @click="backUpPage()">返回上一级</span>
                        <span><span class="coarseLine MR_ten"></span>日志详情</span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <span class="upTitle MR_ten">日志信息：</span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <el-row class="grayFont L_H_F">
                            <el-col :span="6" :offset="1"><div><span>操作人名称：{{loggerInfo.admin_name}}</span></div></el-col>
                            <el-col :span="6" :offset="1" class="overoneLinesHeid">
                                <div>
                                    <el-tooltip class="item" effect="light" :content="loggerInfo.bus_desc" placement="right">
                                    <span>业务描述：{{loggerInfo.bus_desc}}</span>
                                    </el-tooltip>
                                </div>
                            </el-col>
                            <el-col :span="6" :offset="1"><div><span>业务健值：{{loggerInfo.bus_value}}</span></div></el-col>
                        </el-row>
                        <el-row class="grayFont L_H_F">
                            <el-col :span="6" :offset="1"><div><span>日志ID：{{loggerInfo.log_id}}</span></div></el-col>
                            <el-col :span="6" :offset="1"><div><span>操作模块名称：{{loggerInfo.ope_module_name}}</span></div></el-col>
                            <el-col :span="6" :offset="1"><div><span>创建日期：{{loggerInfo.create_time}}</span></div></el-col>
                         </el-row>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <span class="upTitle">详情信息：</span>
                    </div>
                    <el-row>
                        <table style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>操作内容详情</th>
                                    <th>原数据</th>
                                    <th>修改字段</th>
                                    <th>操作时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in tableData">
                                    <td>{{item.table_field_name}}</td>
                                    <td class="overOneLinesHeid fontLift" style="width:500px;">
                                        <el-tooltip class="item" effect="light" :content="item.field_old_value" placement="right">
                                        <span style="-webkit-box-orient: vertical;width:500px;">&nbsp;&nbsp;{{item.field_old_value}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td class="overOneLinesHeid fontLift" style="width:500px;">
                                        <el-tooltip class="item" effect="light" :content="item.field_new_value" placement="right">
                                        <span style="-webkit-box-orient: vertical;width:500px;">&nbsp;&nbsp;{{item.field_new_value}}</span>
                                        </el-tooltip>
                                    </td>
                                    <td>{{item.create_time}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </el-row>
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
      loggerInfo:'',
      isShow:false,
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
        axios.post(vm.url+vm.$getLoggerDetailURL,
            {
                "log_id":vm.$route.query.log_id,
            },
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            vm.tableData=res.data.logDetail;
            vm.loggerInfo=res.data.loggerInfo;
            vm.isShow=false;
        }).catch(function (error) {
            if(error.response.status!=''&&error.response.status=="401"){
              vm.$message('登录过期,请重新登录!');
              sessionStorage.setItem("token","");
              vm.$router.push('/');
            }
        });
    },
    backUpPage(){
        let vm = this;
        vm.$router.push('/getLoggerList');
    }
  }
}
</script>

<style scoped>
@import '../../css/publicCss.css';
</style>
