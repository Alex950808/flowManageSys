<template>
    <div class="PA_Seventy w_ratio S_O fontCenter fontWatermark" :style="spanStyle">
        <span v-for="item in num" class="d_I_B w_20_ratio lineHeighteighty " style="transform: rotate(-25deg)">
            <span>{{userName.user_name}}</span><span style="margin-left: 120px;">海代网</span>
        </span>
    </div>
</template>
<script>
export default {
    data () {
        return {
            userName:'',
            num:Number,
        }
     },
    props:{
        zIndex:{
            type:String,
            required:true,
        }
    },
    props:['zIndex'],
    mounted(){
        this.getUserName();
        this.widhtPage();
    },
    methods:{
        getUserName(){
            let vm = this;
            vm.userName=JSON.parse(sessionStorage.getItem("userName"));
        },

        widhtPage(){
            let vm = this;
            setInterval(function(){
                let height = $(".Height").height();
                vm.heightPage(height)
            },500);
        }
    },
    computed:{
        heightPage(){
            return function(height){
                let vm = this;
                if(height!=undefined){
                    let he =parseInt(height/17);
                    vm.num=he;
                }
            } 
        },
        spanStyle(){
            let vm = this;
            if(vm.zIndex==false){
                return "overflow: hidden;"
            }else{
                return "overflow: hidden;z-index:-1;"
            }
        }
    }
}
</script>
<style>
</style>


