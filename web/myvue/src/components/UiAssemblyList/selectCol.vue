<template>
    <div class="selectCol_b" style="display:none">
            <div class="selectCol">
                <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">选择要展示的信息</span>
                    &nbsp;&nbsp;<i class="el-icon-close" @click="FFdetermineIsNo()"></i>&nbsp;&nbsp;
                </div>
                <div class="selectTitle">
                    <template>
                        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                        <span class="redFont" style="margin-left: -70px;">*选择列时至少要保留一个展示信息</span>
                        <div style="margin: 15px 0;"></div>
                        <el-checkbox-group v-model="checkedCities" @change="handleCheckedCitiesChange;FFconfirmUpData()">
                            <el-checkbox v-for="city in selectStr" :label="city" :key="city">{{city}}</el-checkbox>
                        </el-checkbox-group>
                    </template>
                </div>
            </div>
        <!-- </el-dialog> -->
    </div>
</template>
<script>
import { selectTitle } from '@/filters/publicMethods.js'//引入有公用方法的jsfile
import store from '@/store/store'
export default {
    data () {
        return {
            //选择表头所需数据
            checkAll: false,
            checkedCities: [],
            isIndeterminate: true,
            cityOptions:[],
                }
     },
    props:{
        selectStr:{
            type:String,
            required:true,
        },
    },
    props:['selectStr'],
    mounted(){
        this.getColData;
    },
    methods:{
        //选择表头数据  
        handleCheckAllChange(val) {
            let vm=this;
            this.checkedCities = val ? this.selectStr : [];
            vm.checkedCities=this.selectStr;
            vm.$store.commit('selectList', this.selectStr);
            if(this.$store.state.select.length==0){
                vm.$store.commit('selectList', this.selectStr);
                return false;
            }
            this.isIndeterminate = false;
        },
        handleCheckedCitiesChange(value) {
            let checkedCount = value.length;
            if(checkedCount==0){
                this.checkedCities = this.cityOptions;
            }
            this.checkAll = checkedCount === this.selectStr.length;
            this.isIndeterminate = checkedCount > 0 && checkedCount < this.selectStr.length;
        },
        FFconfirmUpData(){
            let vm=this;
            if(this.checkedCities.length==0){
                vm.$store.commit('selectList', this.selectStr);
                vm.checkedCities=this.selectStr;
            }
            vm.$store.commit('selectList', this.checkedCities);
        },
        FFdetermineIsNo(){
            let vm = this;
            $(".selectCol_b").fadeOut();
        }
    },
    computed:{
        getColData(){
            let vm = this;
            vm.checkedCities = this.selectStr;
        },
    }
}
</script>
<style>
.selectCol_b{
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
.selectCol_b .selectCol{
  width: 800px;
  height: 389px;
  margin: auto;
  position: fixed;
  top: 0px;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 999;
  background-color: #fff;
  /* text-align: center; */
  box-shadow: 5px 5px 10px 10px #ccc;
}
.selectTitle{
    padding-left: 30px;
}
.selectCol_b .confirm{
    margin-top: 30px;
    text-align: right;
    margin-right: 10px;
}
.selectCol_b .isYes{
    display: inline-block;
    width: 35px;
    height: 20px;
    line-height: 20px;
    /* text-align: center; */
    border-radius: 10px;
    background: #fff;
    color: #ccc;
    cursor: pointer;
}
.selectCol_b .isNo{
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
.selectCol_b .confirmTitle{
    display: inline-block;
    width: 800px;
    height: 50px;
    background-color: #409EFF;
    text-align: left;
    margin-bottom: 40px;
}
.selectCol_b .titleText{
    
    height: 50px;
    line-height: 50px;
    display: inline-block;
    color: #fff;
}
.selectCol_b .el-icon-view{
    color: #fff;
}
.selectCol_b .el-icon-close{
    margin-left: 590px;
}
.selectTitle .el-checkbox+.el-checkbox {
    margin-right: 30px;
    width: 100px;
    height: 40px;
    line-height: 40px;
}
.selectTitle .el-checkbox {
    margin-right: 30px;
    /* margin-left: 30px; */
    width: 100px;
    height: 40px;
    line-height: 40px;
}
</style>


