<template>
  <div class="purchaseOutList_b">
    <!-- 提报需求页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="purchaseOutList">
                <el-row class="purchaseTitle">
                    <el-col :span="24">
                        <span>待分配需求列表</span><input type="text" class="inputStyle" placeholder="请输入搜索关键字" v-model="search" @keyup.enter="searchFrame"/><i class="el-icon-search search"></i>
                    </el-col>
                </el-row>
                <ul>
                    <li class="purchaseContent">
                        <el-row>
                        <el-col :span="24">
                            <table>
                                <thead>
                                    <tr>
                                        <th>需求单号</th>
                                        <th>部门</th>
                                        <th>采购截止日期</th>
                                        <th>sku数量</th>
                                        <th>商品总需求量</th>
                                        <th>商品待分配量</th>
                                        <th>状态</th>
                                    </tr>
                                </thead>
                                <tr v-for="item in tableData">
                                    <td>{{item.demand_sn}}</td>
                                    <td>{{item.department}}</td>
                                    <td>{{item.expire_time}}</td>
                                    <td>{{item.sku_num}}</td>
                                    <td>{{item.goods_num}}</td>
                                    <td>{{item.allot_num}}</td>
                                    <td v-if="item.status==1"><span class="status">待挂期</span></td>
                                    <td v-if="item.status==2"><span class="status">待分配</span></td>
                                    <td v-if="item.status==3"><span>已分配</span></td>
                                </tr>   
                            </table>
                        </el-col>
                        </el-row>
                    </li>
                </ul>
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import axios from 'axios'
import { Loading } from 'element-ui';
export default {
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
        search:'',//用户输入数据
        total:0,//页数默认为0
        page:1,//分页页数
        pagesize:15,//每页的数据条数
        isShow:false,
      }
    },
    mounted(){
      this.getDemandData()
      this.loading = Loading.service({fullscreen: true, text: '拼命加载中....'})
    },
    methods: {
      //获取需求列表数据
      getDemandData(){
        let vm = this;
        let headersToken=sessionStorage.getItem("token");
        axios.get(vm.url+vm.$alreadyAllotDemandURL+"?page="+vm.page,
          {
              headers:{
                  'Authorization': 'Bearer ' + headersToken,
                  'Accept': 'application/vnd.jmsapi.v1+json',
              }
          }
        ).then(function(res){
          vm.loading.close()
          if(res.data.code==1002){
              vm.isShow=true;
              return false;
          }
              vm.tableData=res.data.data.demand_list;
              vm.total=res.data.total_num;
        }).catch(function (error) {
                vm.loading.close()
                if(error.response.status=="401"){
                vm.$message('登录过期,请重新登录!');
                sessionStorage.setItem("token","");
                vm.$router.push('/');
                }
            });
      },
    }
}
</script>

<style>
.purchaseOutList_b .purchaseTitle{
    width: 100%;
    height: 100px;
    line-height: 70px;
    font-size: 18px;
    color: #000;
    font-weight: bold;
    position: relative;
}
.purchaseOutList_b .purchaseTitle input{
    width: 200px;
    height: 20px;
    outline: none;
    -webkit-appearance: none;
    border-radius: 50px;
    -moz-border-radius: 50px;
    -webkit-border-radius: 50px;
    padding-left: 30px;
    margin-left: 16px;
}
.purchaseOutList_b .purchaseTitle .search{
    position: absolute;
    left: 152px;
    top: 27px;
}
.purchaseOutList_b .purchaseContent{
    width: 100%;
    height: 100%;
    border:1px solid #000;
    border-radius: 10px;
}
.purchaseOutList_b .purchaseContent table{
    width:100%;
    height:100%;
    line-height: 50px;
    text-align: center;
}
.purchaseOutList_b .status{
    display: inline-block;
    width: 80px;
    height: 30px;
    line-height: 30px;
    background-color: #409EFF;
    border-radius: 10px;
    color:#fff;
    cursor: pointer;
}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
