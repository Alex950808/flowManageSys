<template>
  <div class="purDemOrdInfo_b">
      <el-col :span="22" :offset="1">
            <div class="purDemOrdInfo">
                <div class="purchaseDiscount">
                    <div class="title">
                        <span @click="backUpPage()" class="back">返回上一级</span>
                        <span class="upTitle">需求单详情</span>
                    </div>
                </div>
                <table style="width:100%;text-align: center;line-height: 40px;" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th>采购期单号</th>
                            <th>创建时间</th>
                            <th>提货队人数</th>
                            <th>提货队</th>
                            <th>采购渠道</th>
                            <th>采购方式</th>
                            <th>开始时间</th>
                            <th>结束时间</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in tableData">
                            <td>{{item.purchase_sn}}</td>
                            <td>{{item.create_time}}</td>
                            <td>{{item.delivery_pop_num}}</td>
                            <td>{{item.delivery_team}}</td>
                            <td>{{item.channels_list}}</td>
                            <td>{{item.method_info}}</td>
                            <td>{{item.start_time}}</td>
                            <td>{{item.end_time}}</td>
                            <td>{{item.status}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
      </el-col>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  data(){
    return{
      url: `${this.$baseUrl}`,
      tableData:[],
    }
  },
  mounted(){
    this.getListData();
  },
  methods:{
    getListData(){
        let vm=this;
        vm.tableData=JSON.parse(sessionStorage.getItem("tableDataT"));
    },
    //回上一级页面
    backUpPage(){
        let vm=this;
        vm.$router.push('/purDemOrdList');
    },
  }
}
</script>

<style>
.purDemOrdInfo_b .title{
    margin-bottom: 30px;
}
.purDemOrdInfo_b .back{
    display: inline-block;
    width: 200px;
    height: 50px;
    background-color: #00C1DE;
    color: #fff;
    line-height: 50px;
    text-align: center;
    margin-top: 25px;
    border-radius:10px; 
    cursor: pointer;
}
.purDemOrdInfo_b .upTitle{
    font-weight: bold;
    font-size: 18px;
    margin-left: 20px;
}
</style>
<style scoped lang=less>
@import '../../css/salesModule.less';
</style>
