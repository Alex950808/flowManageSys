// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from '@/App'
import router from '@/router'
import ElementUI from 'element-ui';
import $ from 'jquery'
import axios from 'axios'
import { Loading } from 'element-ui';
import comment from '@/filters/comment'
import common from '@/css/common.css'
import nounInterpretation from '@/filters/nounInterpretation'
import '@/iconfont/iconfont.css'
import {store} from '@/store/store'
// import Less from 'less';
// import lessLoader from 'less-loader'; 
import 'element-ui/lib/theme-chalk/index.css';
import '@/filters/validate'
import echarts from 'echarts'

Vue.config.productionTip = false
//此处仅当post请求时加入一个调试参数 add zhangdong 2019.01.14 
// axios.interceptors.request.use(
//     config => {  	        
//     	if(config.method=='post'){ 
// 	        config.data = { 
// 	        	... config.data, 
// 	            'XDEBUG_SESSION_START':14293, 
// 	        }
// 	    } 
// 		return config;
//     }	
// )
Vue.prototype.$http = axios
Vue.prototype.$echarts = echarts 
Vue.use(comment);
Vue.use(nounInterpretation);
Vue.use(common);
Vue.use(ElementUI);
// const baseUrl='http://192.168.0.39:9999/api/';//宗兴 
const baseUrl='http://192.168.0.4:8082/api/';//张冬 
// const baseUrl='http://120.76.27.42:84/api/';//服务器地址 
// const downloadUrl='http://192.168.0.39:9999/downTemp';//下载表格模板地址宗兴 
const downloadUrl='http://192.168.0.4:8082/downTemp';//下载表格模板地址张冬 
// const downloadUrl='http://120.76.27.42:84/downTemp';//下载表格模板地址 
// const offerUrl='http://192.168.0.39:9999';//报价商品表下载地址宗兴 
const offerUrl='http://192.168.0.4:8082';//报价商品表下载地址张冬 
// const offerUrl='http://120.76.27.42:84';//报价商品表下载地址地址 
Vue.prototype.$baseUrl = baseUrl;
Vue.prototype.$downloadUrl = downloadUrl;
Vue.prototype.$offerUrl = offerUrl;



/* eslint-disable no-new */

new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>',
  store,
})
