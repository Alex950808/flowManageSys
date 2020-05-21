<template>
    <div class="upDataButtonByBox_b" style="display: none">
        <div class="upDataButtonByBox">
            <!-- <el-dialog title="" :visible.sync="dialogVisible" width="800px"> -->
                <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">{{titleStr}}</span>
                    &nbsp;&nbsp;<i class="el-icon-close" @click="FFdetermineIsNo()"></i>&nbsp;&nbsp;
                </div>
                <form id="forms" method="post" enctype="multpart/form-data" class="MB_twenty d_I_B">
                    <div class="file">
                    <img src="../../image/upload.png"/>
                    <span v-if="fileName==''">点击上传文件</span>
                    <span v-if="fileName!=''">{{fileName}}</span>
                    <input style="height:100%;width:100%;" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
                    </div>
                </form>
                <div class="fontCenter lineHeightSixty PR_twenty PL_twenty">
                    <el-button type="info" class="ML_ten" @click="dialogVisible = false;FFdetermineIsNo()">取 消</el-button>
                    <el-button type="primary" class="MR_ten" @click="dialogVisible = false;FFconfirmUpData()">确 定</el-button>
                </div>
            <!-- </el-dialog> -->
        </div>
    </div>
</template>
<script>
export default {
    data () {
        return {
            fileName:'',
        }
     },
    props:{
        titleStr:{
            type:String,
            required:true,
        }
    },
    props:['titleStr'],
    methods:{
        SelectFile(){
            let vm = this;
            var r = new FileReader();
            var f = document.getElementById("file1").files[0];
            vm.fileName=f.name;
        },
        FFconfirmUpData(){
            let vm=this;
            var formDate = new FormData($("#forms")[0]);
            if(vm.fileName==''){
                vm.$message('请选择文件再行提交!');
                return
            }
            this.$emit('GFFconfirmUpData',formDate);
            $("#file1").val('');
            vm.fileName='';
        },
        FFdetermineIsNo(){
            // this.$emit('determineIsNo')
            $(".upDataButtonByBox_b").fadeOut();
            $(".d_table").fadeOut();
            $(".beijing").fadeOut();
        },
        // FFconfirmationAudit(){
        //     let vm = this;
        //     this.$emit('confirmationAudit')
        // }
    }
}
</script>
<style>
.upDataButtonByBox_b{
    width: 100%;
    height: 100%;
    margin: auto;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    z-index: 9999;
    background-color: rgba(0, 0, 0, 0.3);
}
.upDataButtonByBox_b .upDataButtonByBox{
  width: 400px;
  height: 310px;
  margin: 250px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 999;
  background-color: #fff;
  text-align: center;
  box-shadow: 5px 5px 10px 10px #ccc;
}
.upDataButtonByBox_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.upDataButtonByBox_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    background: #fff;
    color: #ccc;
    cursor: pointer;
}
.upDataButtonByBox_b .isNo{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    text-align: center;
    border-radius: 10px;
    /* background: #00C1DE;
    color: #fff; */
    background: #fff;
    color: #ccc;
    cursor: pointer;
}
.upDataButtonByBox_b .confirmTitle{
    display: inline-block;
    width: 400px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.upDataButtonByBox_b .titleText{
    
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.upDataButtonByBox_b .el-icon-close{
    float: right;
    margin-top: 16px;
    margin-right: 10px;
}
.upDataButtonByBox_b .file {
    position: relative;
    display: inline-block;
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 1px solid #ccc;
    overflow: hidden;
    text-decoration: none;
    text-indent: 0;
    line-height: 50px;
    text-align: center;
}
.upDataButtonByBox_b .file img{
  padding-top: 20px;
}
.upDataButtonByBox_b .file span{
  display: inline-block;
  width: 120px;
  height: 27px;
  font-size: 10px;
  line-height: 27px;
  vertical-align: 19px;
}
.upDataButtonByBox_b .file input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
}
.upDataButtonByBox_b .el-icon-view{
    color: #fff;
}
</style>


