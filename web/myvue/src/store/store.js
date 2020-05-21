import Vue from 'vue'
import Vuex from 'vuex'
import { uniq } from '@/filters/publicMethods.js'//引入有公用方法的jsfile

Vue.use(Vuex)


export const store = new Vuex.Store({
    state: {
        webNameList:[],
        select:[],
        clickStutes:true,
        unClickStutes:false,
    },
    mutations: {
        webName(state,webName){
            if(webName.to=='indexPage'){
                return false;
            }else{
                state.webNameList.push(webName);
                let nameList=[];
                let toList=[]
                state.webNameList.forEach(element => {
                    nameList.push(element.web_name);
                    toList.push(element.to)
                });
                nameList=uniq(nameList)
                toList=uniq(toList);
                let web_name_list=[];
                for(let i=0;i<nameList.length;i++){
                    web_name_list.push({"web_name":nameList[i],"to":toList[i]})
                }
                state.webNameList=web_name_list
            }
            
        },
        reduceWebName(state,webName){
            for(let i=0;i<state.webNameList.length;i++){
                if(state.webNameList[i].web_name==webName){
                    state.webNameList.splice(i,1)
                }
            }
        },
        romWebName(state){
            state.webNameList.splice(0);
        },
        selectList(state,selectList){
            state.select=selectList;
        },
        editClickStutes(state,editClickStutes){
            state.clickStutes=!state.clickStutes;
            state.unClickStutes=!state.unClickStutes;
        }
    },
})