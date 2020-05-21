import Vue from 'vue';

const ZDXSZENuma='--子单销售总额 = 需求单销售总额 + 现货单销售总额';
const ZDXSZENumb='--需求单毛利总额 = 现货单毛利总额 = 美金原价*数量*报价逻辑毛利率';
const XSMLZEa='--销售毛利总额 = 需求单毛利总额 + 现货单毛利总额';
const XSMLZEb='--需求单毛利总额 = 现货单毛利总额 = 美金原价*数量*报价逻辑毛利率';
const XSMLZEc='--报价逻辑毛利率 = 1-(子单中的exw折扣+重价比系数)/报价折扣';
const XSMLZEd='--重价比 = 重量/美金原价/0.0022/100';
const SCMLZEa='--实采毛利总额 = 美金原价*分货数量*实采毛利率';
const SCMLZEb='--实采毛利率 = 1-(批次中的真实折扣+重价比)/报价折扣';
const SCMLZEc='--重价比 = 重量/美金原价/0.0022/100';
const BZMLL='--公司规定的标椎毛利率，数据默认为8%';
const BJLJMLL='--销售毛利总额/销售总额*100%';
const SCMLLa='--实采毛利率 = 实采毛利总额/实采销售总额*100%';
const SCMLLb='--实采销售总额 = 美金原价*分货数量*报价折扣';

const XQSPZS='--需求单商品的总需求数';
const XQSPZE='--需求单商品的总需求额';
const XQMZL='--需求单商品分货数/需求单总需求数*100%';
const XQJEMZL='--需求单商品分货总金额/需求单商品总需求额*100%';
const QKZS='--需求单商品总需求数-需求单商品总分货数';
const QKZE='--需求单商品总缺口数*美金原价';
const DSHPC='--上传未审核的批次数量';
const SCZEa='--批次单中各种结算方式（共三种）的实采总额之和';
const SCZEb='-- 以没返点的美金原价结算：实采总额 = 美金原价 * 数量';
const SCZEc='--以有返点的美金原价结算：实采总额 = 美金原价 * 数量';
const SCZEd='--以LVIP价结算：实采总额 = LVIP价 * 数量';


export default {
    install () {
        Vue.prototype.$ZDXSZENuma = ZDXSZENuma;
        Vue.prototype.$ZDXSZENumb = ZDXSZENumb;
        Vue.prototype.$XSMLZEa = XSMLZEa;
        Vue.prototype.$XSMLZEb = XSMLZEb;
        Vue.prototype.$XSMLZEc = XSMLZEc;
        Vue.prototype.$XSMLZEd = XSMLZEd;
        Vue.prototype.$SCMLZEa = SCMLZEa;
        Vue.prototype.$SCMLZEb = SCMLZEb;
        Vue.prototype.$SCMLZEc = SCMLZEc;
        Vue.prototype.$BZMLL = BZMLL;
        Vue.prototype.$BJLJMLL = BJLJMLL;
        Vue.prototype.$SCMLLa = SCMLLa;
        Vue.prototype.$SCMLLb = SCMLLb;

        Vue.prototype.$XQSPZS = XQSPZS;
        Vue.prototype.$XQSPZE = XQSPZE;
        Vue.prototype.$XQMZL = XQMZL;
        Vue.prototype.$XQJEMZL = XQJEMZL;
        Vue.prototype.$QKZS = QKZS;
        Vue.prototype.$QKZE = QKZE;
        Vue.prototype.$DSHPC = DSHPC;
        Vue.prototype.$SCZEa = SCZEa;
        Vue.prototype.$SCZEb = SCZEb;
        Vue.prototype.$SCZEc = SCZEc;
        Vue.prototype.$SCZEd = SCZEd;
    }
}