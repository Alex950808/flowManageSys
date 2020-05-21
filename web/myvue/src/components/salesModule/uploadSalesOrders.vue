<template>
  <div class="uploadSalesOrders_b">
    <!-- 上传商品销售订单页面 -->
    <!-- <el-row :gutter="20"> -->
        <el-col :span="22" :offset="1">
            <div class="uploadSalesOrders">
                <div class="content">
                    <span class="back">返回上一级</span>
                    <span>上传订单表格</span>
                    <form id="forms" method="post" enctype="multpart/form-data">
                    <input @change="imageSubmit" type="file" class="imgFiles" id="file1" name="files" />
                    </form>
                    <div class="Detailed">上传需求商品明细</div>
                </div>
                <div class="addGoods">
                    <div class="title">
                        <span>上传记录</span>
                    </div>
                    <table>
                        <tr>
                            <td>2018-04-20</td>
                            <td>2018-04-20销售订单.xls</td>
                            <td>下载</td>
                        </tr>
                        <tr>
                            <td>2018-04-20</td>
                            <td>2018-04-20销售订单.xls</td>
                            <td>下载</td>
                        </tr>
                        <tr>
                            <td>2018-04-20</td>
                            <td>2018-04-20销售订单.xls</td>
                            <td>下载</td>
                        </tr>
                        <tr>
                            <td>2018-04-20</td>
                            <td>2018-04-20销售订单.xls</td>
                            <td>下载</td>
                        </tr>
                    </table>
                </div>
            </div>
        </el-col>
    <!-- </el-row> -->
  </div>
</template>

<script>
import $ from 'jquery'
export default {
    data() {
      return {
        tableData: [],
        url: `${this.$baseUrl}`,
      }
    },
    methods: {
        imageSubmit(){
            let vm = this;
            var r = new FileReader();
            var f = document.getElementById("file1").files[0];
            r.readAsDataURL(f);
            r.onload = function(e) {
            var res = this.result;
            var formDate = new FormData($("#forms")[0]);
            $.ajax({
            url: vm.url,
            type: "POST",
            async: false,
            cache: false,
            data: formDate,
            processData: false,
            contentType: false,
            success: function(res) {
            }
            });
            }
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
@import '../../css/salesModule.less';
</style>