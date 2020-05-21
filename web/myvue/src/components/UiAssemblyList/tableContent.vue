<template>
    <div class="tableStyle">
        <table class="publicTable" style="width:100%;text-align: center;line-height: 60px;" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th v-for="(item,index) in TCTitle" :key="index">{{item}}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in tableData" class="firstRowHeid">
                    <td v-for="field in tableField" :key="field"><span style="-webkit-box-orient: vertical;">{{item[field]}}</span></td>
                    <td v-if="isViewButton=='isEditContent'">
                        <i class="iconfont Cursor" @click="FFEdit(item[parameterStr])">&#xe62f;</i>
                    </td>
                    <td v-if="isViewButton=='predictDemandList'">
                        <i class="el-icon-download newAddIcon verticalAlign" title="下载预采需求列表" @click="FFDownload(item[parameterStr])"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="el-icon-view newAddIcon" title="查看详情" @click="FFViewDetail(item[parameterStr])"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="iconfont upDataIcon verticalAlign" title="上传预采批次" @click="FFupData(item[parameterStr])">&#xe637;</i>
                    </td>
                    <td v-if="isViewButton=='isViewDetail'">
                        <i class="el-icon-view newAddIcon" title="查看详情" @click="FFViewDetail(item[parameterStr])"></i>
                    </td>
                    <td v-if="isViewButton=='isSubmenuAndDD'">
                        <i class="el-icon-view MR_ten notBgButton" title="查看子单详情" @click="FFseeDetail(item[parameterStr])"></i>
                        <i class="iconfont Cursor blueFont" v-if="item['is_submenu']=='已分单'&&item['status']=='DD'" title="查看详情" @click="FFViewDetail(item[parameterStr])">&#xe62f;</i>
                        <i class="iconfont grayFont" v-else title="该订单未分单或者不为DD单，无法查看详情">&#xe62f;</i>
                    </td>
                    <td v-if="isViewButton=='justDownload'">
                        <i class="el-icon-download newAddIcon" title="下载表格" @click="FFDownload(item[parameterStr])"></i>
                    </td>
                    <td v-if="isViewButton=='upDataAndDownload'">
                        <i class="el-icon-download newAddIcon" title="下载表格" @click="FFDownload(item[parameterStr])"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="iconfont upDataIcon verticalAlign" title="上传表格" @click="FFupData(item[parameterStr])">&#xe637;</i>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data () {
        return {
            // tableData:[],
            parameterStr:'',
        }
     },
     props:{
        tableContent:{
            type:String,
            required:true,
        },
        TCTitle:{
            type:Array,
            required:true
        },
        tableField:{
            type:Array,
            required:true,
        },
        contentConfig:{
            type:String,
            required:true,
        }
    },
    props:['tableContent','TCTitle','tableField','contentConfig'],
    mounted(){
        // this.myclick();
    },
    methods:{
        //FF代表fatherFunction
        FFEdit(id){
            let vm = this;
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            this.$emit('Edit',id);
        },
        FFDownload(sn){
            let vm = this;
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            this.$emit('Download',sn);
        },
        FFupData(sn){
            let vm = this;
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            this.$emit('upData',sn);
        },
        FFViewDetail(sn){
            let vm = this;
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            this.$emit('ViewDetail',sn);
        },
        FFseeDetail(sn){
            let vm = this;
            //当点击子组件的按钮的时候，拿到父组件传递过来的方法，并调用这个方法
            this.$emit('seeDetail',sn);
        }
    },
    computed:{
        tableData:function(){
            let vm = this;
            if(vm.tableContent!=''){
                return JSON.parse(vm.tableContent);
            }
        },
        isViewButton(){
            let vm = this;
            //表格内容部分编辑按钮的配置项，用来定义是否显示，显示什么内容
            let contentConfig=JSON.parse(vm.contentConfig);
            vm.parameterStr=contentConfig[1].parameter;
            switch(contentConfig[0].isShow){
                case false:
                    return false;
                case 'upDataAndDownload':
                    return "upDataAndDownload";
                case 'isSubmenuAndDD':
                    return "isSubmenuAndDD";
                case 'isViewDetail':
                    return "isViewDetail";
                case 'predictDemandList':
                    return "predictDemandList";
                case 'isEditContent':
                    return "isEditContent";
                case 'justDownload':
                    return "justDownload";
                case 'upDataAndDownload':
                    return "upDataAndDownload";
            }
        }
    }
}
</script>
<style scoped>
.editGoods{
    font-size: 22px !important;
    vertical-align: -5px;
    color: #ccc !important;
}
/* 通用表格样式 */
.publicTable tr{
    line-height: 40px;
}
.publicTable tr th,td{
    border-top: 1px solid #ebeef5;
    border-bottom: 1px solid #ebeef5;
    border-left: 1px solid #ebeef5;
    border-right: 1px solid #ebeef5;
}
.publicTable tr:nth-child(even){
    background:#fafafa;
}
.publicTable tr:hover{
    background:#ced7e6;
}
.firstRowHeid td:first-child{
    width: 250px;
}
.firstRowHeid td:first-child span{
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    overflow: hidden;
}
</style>


