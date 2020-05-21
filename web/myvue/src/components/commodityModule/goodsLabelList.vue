<template>
  <div class="goodsLabelList">
      <el-row>
        <el-col :span="24" style="background-color: #fff;">
            <el-row class="bgDiv">
                <el-col :span="22" :offset="1">
                    <div class="listTitleStyle">
                        <span><span class="coarseLine MR_twenty"></span>标签列表</span>
                        <span class="bgButton floatRight MT_twenty" @click="dialogVisible = true;titleStr='新增标签';label_name='';isNew='1'">新增标签</span>
                        <el-dialog :title="titleStr" :visible.sync="dialogVisible" width="900px">
                            <el-row class="elRow">
                                <el-col :span="3">
                                    <span><span class="redFont">*</span>标签简称：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="specWeight">
                                        <el-input v-model="label_name" style="width:87%;" placeholder="请输入标签简称"></el-input>
                                    </div>
                                </el-col>
                                <el-col :span="6">
                                    <span class="redFont" style="margin-left: -32px;">标签简称必须为一个汉字</span>
                                </el-col>
                            </el-row>
                            <el-row class="elRow">
                                <el-col :span="3">
                                    <span><span class="redFont">*</span>标签名称：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="specWeight">
                                        <el-input v-model="label_real_name" style="width:87%;" placeholder="请输入标签名称"></el-input>
                                    </div>
                                </el-col>
                                <el-col :span="3">
                                    <span><span class="redFont">*</span>标签背景色：</span>
                                </el-col>
                                <el-col :span="8">
                                    <div class="specWeight">
                                        <el-color-picker style="vertical-align: -14px;" v-model="label_color" show-alpha :predefine="predefineColors"></el-color-picker>
                                    </div>
                                </el-col>
                            </el-row>
                            <span slot="footer" class="dialog-footer">
                                <el-button type="info" @click="dialogVisible = false">取 消</el-button>
                                <el-button type="primary" @click="dialogVisible = false;doAddGoodsLabel()">确 定</el-button>
                            </span>
                        </el-dialog>
                    </div>
                    <el-row :gutter="20">
                        <el-col :span="18">
                            <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th>标签ID</th>
                                        <th>标签名称</th>
                                        <th>标签颜色</th>
                                        <th>编辑</th>
                                    </tr>
                                </thead>
                                <tr v-for="item in tableData">
                                    <td>{{item.id}}</td>
                                    <td><span>{{item.label_name}}</span></td>
                                    <td><span :style="colorStyle(item.label_color)" class="d_I_B"></span></td>
                                    <td>
                                        <i class="notBgButton NotStyleForI" @click="editGoodsLabel(item.id);titleStr='编辑标签';label_name='';isNew='2'">编辑标签</i>
                                    </td>
                                </tr>
                            </table>
                        </el-col>
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
import searchBox from '../UiAssemblyList/searchBox';
import notFound from '@/components/UiAssemblyList/notFound';
export default {
  components:{
      notFound,
      searchBox,
  },
  data(){
    return{
      url: `${this.$baseUrl}`,
      headersStr:'',
      tableData:[],
      isShow:false,
      dialogVisible:false,
      label_name:'',
      label_real_name:'',
      titleStr:'',
      isNew:'',
      id:'',
      label_color:'',
      predefineColors: [
          '#ff4500',
          '#ff8c00',
          '#ffd700',
          '#90ee90',
          '#00ced1',
          '#1e90ff',
          '#c71585',
          'rgba(255, 69, 0, 0.68)',
          'rgb(255, 120, 0)',
          'hsv(51, 100, 98)',
          'hsva(120, 40, 94, 0.5)',
          'hsl(181, 100%, 37%)',
          'hsla(209, 100%, 56%, 0.73)',
          '#c7158577'
        ],
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
        axios.get(vm.url+vm.$goodsLabelListURL,
            {
                headers:vm.headersStr,
            },
        ).then(function(res){
            vm.loading.close();
            if(res.data.code=='1000'){
                vm.tableData=res.data.data.data;
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
    //打开编辑商品标签页 
    editGoodsLabel(id){
        let vm = this;
        vm.id=id;
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        axios.get(vm.url+vm.$editGoodsLabelURL+"?id="+id,
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            loa.close();
            if(res.data.code=='1000'){
                vm.label_name=res.data.data.label_name;
                vm.label_color=res.data.data.label_color;
                vm.label_real_name=res.data.data.label_real_name;
                vm.dialogVisible = true;
            }else{
                vm.$message(res.data.msg);
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
    //新增商品标签
    doAddGoodsLabel(){
        let vm = this;
        if(vm.label_name==''){
            vm.$message('标签名称未填写,请填写标签名称!');
            return false;
        }
        if(vm.label_color==''){
            vm.$message('标签背景色未选择,请选择标签背景色!');
            return false;
        }
        let url;
        if(vm.isNew=='1'){
            url=vm.$doAddGoodsLabelURL
        }else if(vm.isNew=='2'){
            url=vm.$doEditGoodsLabelURL
        }
        let loa=Loading.service({fullscreen: true, text: '努力加载中....'});
        axios.post(vm.url+url,
            {
                "label_name":vm.label_name,
                "id":''+vm.id+'',
                "label_color":vm.label_color,
                "label_real_name":vm.label_real_name,
            },
            {
                headers:vm.headersStr,
            }
        ).then(function(res){
            loa.close();
            if(res.data.code=='1000'){
                vm.$message(res.data.msg);
                vm.getDataList();
            }else{
                vm.$message(res.data.msg);
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
    colorStyle(color){
        return "width: 20px;height: 20px;background-color:"+color
    }
  }
}
</script>

<style scoped lang=less>
table{
    width: 96%;
    text-align: center;
    line-height: 40px;
    margin-left: 2%;
    margin-right: 2%;
    margin-top: 20px;
    border: 1px solid #e5e5e5;
    tr td{
        border-bottom: 1px solid #e5e5e5;
        border-right:  1px solid #e5e5e5;
    }
    thead{
        background: #ebeef5;
        height: 40px;
        line-height: 40px;
        tr th{
            border-right:  1px solid #e5e5e5;
        }
    }
}
</style>
