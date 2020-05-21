<template>
  <div class="increaseBatch_b">
    <!-- 增加到港批次页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="increaseBatch">
                <div>
                    <router-link  to='/expectedToArrive'><span class="back">返回上一级</span></router-link>
                    <span class="upTitle">上传实时采购数据</span>
                </div>
                <el-row>
                    <el-col :span="12">
                        <div class="content_left">
                            <ul>
                                <li>选择日期</li>
                                <li>选择口岸</li>
                                <div style="clear: both;"></div>
                            </ul>
                            <div class="Detailed">上传商品明细</div>
                        </div>
                    </el-col>
                    <el-col :span="3" :offset="9">
                        <div class="content_right">
                            <div class="confirm">确认添加</div>
                        </div>
                    </el-col>
                </el-row>
                <div class="table_title">已添加的数据表格</div>
                <table>
                    <tr>
                        <td>2018-04-12.xle</td>
                        <td>线上</td>
                        <td>乐天</td>
                        <td>5124214</td>
                        <td><i class="el-icon-check"></i></td>
                    </tr>
                    <tr>
                        <td>2018-04-12.xle</td>
                        <td>线下</td>
                        <td>乐天</td>
                        <td>5124214</td>
                        <td><i class="el-icon-check"></i></td>
                    </tr>
                    <tr>
                        <td>2018-04-12.xle</td>
                        <td>特贩</td>
                        <td>乐天</td>
                        <td>5124214</td>
                        <td><i class="el-icon-check"></i></td>
                    </tr>
                    <tr>
                        <td>2018-04-12.xle</td>
                        <td>线上</td>
                        <td>乐天</td>
                        <td>5124214</td>
                        <td><i class="el-icon-check"></i></td>
                    </tr>
                </table>
                <div class="confirm">确认上传</div>
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
export default {
     methods: {
      tableRowClassName({row, rowIndex}) {
        // if (rowIndex === 2) {
        //   return 'warning-row';
        // } else if (rowIndex === 3) {
        //   return 'success-row';
        // }
        // return '';
      }
    },
    data() {
      return {
        tableData2: [{
          codeAbbreviated: '2016-05-02',
          name: '王小虎',
          upData:'上传实时实采数据'
        }
        ]
      }
    }
}
</script>

<style>
.el-table .warning-row {
    font-weight: bold;
}
 .el-table .warning-row  td:last-child {
    /* background: oldlace; */
    
    /* background-color: #ccc !important; */
    /* border-radius: 10px; */
  }

  .el-table .success-row{
      
    /* background: #f0f9eb; */
    /* font-weight: bold; */
  }
.el-table{
    text-align: center;
    margin-bottom: 40px;
}
.el-table th>.cell {
    text-align: center;
}
</style>

<style scoped lang=less>
@import '../../css/purchasingModule.less';
</style>