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

import 'element-ui/lib/theme-chalk/index.css';
import '@/filters/validate'
import echarts from 'echarts'

Vue.config.productionTip = false
Vue.prototype.$http = axios
Vue.prototype.$echarts = echarts 
Vue.use(comment);
Vue.use(nounInterpretation);
Vue.use(common);
Vue.use(ElementUI);
const baseUrl='http://120.76.27.42:84/api/';//服务器地址 
const downloadUrl='http://120.76.27.42:84/downTemp';//下载表格模板地址 
const offerUrl='http://120.76.27.42:84';//报价商品表下载地址地址 
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
