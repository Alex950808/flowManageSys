import Vue from 'vue'
import VeeValidate, {Validator} from 'vee-validate'
import zh from 'vee-validate/dist/locale/zh_CN';//引入中文文件

// 配置中文
Validator.addLocale(zh);

const config = {
  locale: 'zh_CN'
};

Vue.use(VeeValidate,config);

// 自定义validate 
const dictionary = {
   zh_CN: {
      messages: {
        email: () => '请输入正确的邮箱格式',
        required: ( field )=> "请输入" + field
      },
      attributes:{
        email:'邮箱',
        password:'密码',
        name: '账号',
        number: '手机',
        goodsName:'商品名称',
        brandName:'品牌名称',
        specPrice:'美金原价',
        specWeight:'商品重量',
        exwDiscount:'EXW折扣',
        integralBalance:'渠道积分',
        channelName:'渠道名称',
        moneyBalance:'渠道余款',
        external_sn:'外部订单号',
        remark:'备注',
        subMark:'子单备注',
        sum_demand_name:'合单名称',
        type_cat:'档位类型',
        discountName:'折扣名称',
        methodName:'方式名称',
        mail:'自提/邮寄',
        ID:'采购编号id',
        port:'港口id',
        supplier:'供应商id',
        deliveryTime:'提货时间',
        arriveTime:'到货时间',
        taskId:'任务模板',
        arrPickRate:'毛利率档位',
        integralTime:'预计返积分日期',
        estimateWeighy:'预估重量',
        buyTime:'购买时间',
        max:'区间上限',
        min:'区间下限',
        entrust_time:'交付日期',
        add_type:'计算方式',
      }
  }
};

Validator.updateDictionary(dictionary);

Validator.extend('number', {
  messages: {
    zh_CN:field => '必须是0-1之间的数字',
  },
  validate: value => {
    return /^(1|1\.[0]*|0?\.(?!0+$)[\d]+)$/.test(value)
  }
});