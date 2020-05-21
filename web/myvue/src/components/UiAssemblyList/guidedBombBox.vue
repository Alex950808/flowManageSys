<template>
    <div class="guidedBombBox" style="display: none">
        <div class="guidedBombBox_b">
            <div class="BombBox">
                <div class="confirmTitle MR_ten PL_twenty"><i class="el-icon-view MR_ten"></i><span class="titleText MR_ten">请确认</span>
                    <i class="el-icon-close" @click="FFcancelButton()"></i>
                </div>
            {{bombBoxStr}}
            <div class="confirm">
                <span class="redBgButton"  @click="FFwhereIsYouGoing()" >去{{goWhere}}</span>
                <span class="grayBgButton" @click="FFcancelButton()" size="mini" type="info">取消</span>
            </div>
            </div>
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
        bombBoxStr:{
            type:String,
            required:true,
        },
        goWhere:{
            type:String,
            required:true,
        },
        LT:{
            type:String,
            required:true,
        }
    },
    props:['bombBoxStr','goWhere','LT'],
    methods:{
        FFcancelButton(){
            // this.$emit('whereIsYouGoing')
            $(".guidedBombBox").fadeOut();
        },
        FFwhereIsYouGoing(){
            let vm = this;
            // this.$emit('whereIsYouGoing')
            vm.$router.push(vm.LT);
        }
    }
}
</script>
<style>
.guidedBombBox_b{
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
.guidedBombBox_b .BombBox{
  width: 400px;
  height: 189px;
  margin: 375px auto;
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
.guidedBombBox_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.guidedBombBox_b .isYes{
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
.guidedBombBox_b .isNo{
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
.guidedBombBox_b .confirmTitle{
    display: inline-block;
    width: 380px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.guidedBombBox_b .titleText{
    
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.guidedBombBox_b .el-icon-view{
    color: #fff;
}
.guidedBombBox_b .el-icon-close{
    margin-left: 250px;
}
</style>


