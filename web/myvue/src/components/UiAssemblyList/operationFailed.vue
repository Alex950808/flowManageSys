<template>
    <div class="operationFailed_b" style="display:none;">
        <div class="operationFailed">
            <img src="../../image/error.png">
            <div class="fail overTwoLinesHeid fontCenter" :title="msgStr">
                <span style="-webkit-box-orient: vertical;">{{msgStr}}</span>
            </div>
            <div class="closeFail Cursor" @click="closeFail()">关闭</div>
        </div>
    </div>
</template>
<script>
export default {
    data () {
        return {
            // fileName:'',
        }
     },
    props:{
        msgStr:{
            type:String,
            required:true,
        }
    },
    props:['msgStr'],
    methods:{
        closeFail(){
            $(".operationFailed_b").fadeOut()
        }
    }
}
</script>
<style>
.operationFailed_b{
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
.operationFailed_b .operationFailed{
  width: 500px;
  height: 280px;
  margin: 250px auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 999;
  background-color: #fff;
  text-align: center;
  border-radius: 20px;
  /* box-shadow: 5px 5px 10px 10px #ccc; */
}
.operationFailed img{
    position: absolute;
    top: -56px;
    right: 181px;
    width: 140px;
    height: 140px;
}
.operationFailed .fail{
    width: 80%;
    height: 80px;
    margin-left: 10%;
    margin-top: 127px;
    font-size: 23px;
    line-height: 40px;
    color: #fe5d17;
}
.operationFailed .closeFail{
    width: 80%;
    position: absolute;
    bottom: 15px;
    margin-left: 10%;
    height: 50px;
    line-height: 50px;
    background-color: #508cee;
    color: #fff;
    font-size: 22px;
}

</style>


