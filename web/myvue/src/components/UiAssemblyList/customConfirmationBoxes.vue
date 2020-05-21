<template>
    <div class="customConfirmationBoxes">
        <div class="confirmPopup_b" style="display: none">
            <div class="confirmPopup">
                <div class="confirmTitle">&nbsp;&nbsp;<i class="el-icon-view"></i>&nbsp;&nbsp;<span class="titleText">请确认</span>
                    &nbsp;&nbsp;<i class="el-icon-close" @click="FFdetermineIsNo()"></i>&nbsp;&nbsp;
                </div>
            {{contentStr}}
            <div class="confirm">
                <span class="redBgButton"  @click="FFconfirmationAudit()" >确定</span>
                <span class="grayBgButton" @click="FFdetermineIsNo()" size="mini" type="info">取消</span>
            </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data () {
        return {
            loadingShow:false,
            search:'',
        }
     },
    props:{
        contentStr:{
            type:String,
            required:true,
        }
    },
    props:['contentStr'],
    methods:{
        FFdetermineIsNo(){
            this.$emit('determineIsNo')
        },
        FFconfirmationAudit(){
            let vm = this;
            this.$emit('confirmationAudit')
        }
    }
}
</script>
<style>
.confirmPopup_b{
    background-color: rgba(0, 0, 0, 0.3);
}
</style>


