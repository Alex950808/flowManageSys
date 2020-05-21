<template>
    <div class="upDataButton">
        <!-- <i class="iconfont upDataIcon verticalAlign">&#xe637;</i> -->
        <form id="forms" method="post" enctype="multpart/form-data" style="display: inline-block">
            <div class="file">
            <i class="iconfont upDataIcon verticalAlign">&#xe637;</i>
            <span v-if="fileName!=''">{{fileName}}</span>
            <input style="height:100%;width:100%;" id="file1" type="file" @change="SelectFile()"  name="upload_file"/>
            </div>
        </form>
        <span class="bgButton" @click="FFconfirmUpData()">{{upDataStr}}</span>
    </div>
</template>
<script>
export default {
    data () {
        return {
            // show:false,
            // search:'',
            fileName:'',
        }
     },
    props:{
        upDataStr:{
            type:String,
            required:true,
        }
    },
    props:['upDataStr'],
    methods:{
        SelectFile(){
            let vm = this;
            var r = new FileReader();
            var f = document.getElementById("file1").files[0];
            vm.fileName=f.name;
            // this.$emit('SelectFile',f)
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
        }
    }
}
</script>
<style>
.upDataButton{
    display: inline-block;
}
.upDataButton .file {
    position: relative;
    display: inline-block;
    text-align: center;
}
.upDataButton .file img{
  padding-top: 20px;
}
.upDataButton .file span{
  display: inline-block;
  font-size: 15px;
}
.upDataButton .file input {
    position: absolute;
    right: 0;
    top: 0;
    opacity: 0;
}
</style>


