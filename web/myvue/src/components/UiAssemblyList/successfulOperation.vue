<template>
    <div class="successfulOperation_b" style="display: none">
        <div class="successfulOperation">
            <img src="../../image/correct.png">
            <div class="success yingcang" style="text-align: center;" :title="msgStr">{{msgStr}}</div>
        </div>
    </div>
</template>
<script>
export default {
    data () {
        return {
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
        
    }
}
</script>
<style>
.successfulOperation_b{
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
.successfulOperation_b .successfulOperation{
  width: 400px;
  height: 240px;
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
}
.successfulOperation img{
    position: absolute;
    top: -56px;
    right: 130px;
    width: 140px;
    height: 140px;
}
.successfulOperation .success{
    margin-top: 128px;
    font-size: 20px;
    color: #518ded; 
    width: 80%;
    margin-left: 10%;
}
.yingcang{
    text-overflow: ellipsis;
    overflow: hidden;
}

</style>


