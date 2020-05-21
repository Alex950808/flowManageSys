<?php
//常量配置文件 create by zhangdong on the 2018.07.14
//将该常量文件移动到app目录下 zhangdong 2019-12-18
//通用小数点保留位数
define('DECIMAL_DIGIT',4);
//通用小数点保留位数
define('PRICE_DIGIT',2);
//默认重价系数仓库
define('STORE_FACTOR',1002);
//销售折扣默认毛利率档位-该值如做修改请务必将 MARGIN_RATE_PERCENT 这个常量也改掉
define('MARGIN_RATE','0.08');
//ERP库存查询-开始时间
define('START_TIME',date('Y-m-d H:i:s',strtotime('-2 hour')));
//ERP库存查询-结束时间
define('END_TIME',date('Y-m-d H:i:s'));
//ERP库存查询-结束时间
define('HIGH_PRICE_FACTOR',0.0022);
//DD单品排行接口 - 开始时间默认为八月一号 2019.09.06 zhangdong
define('COUNT_START_TIME', '2019-08-01 00:00:00');
//报价数据导出参数配置-仅配置经常变动的参数 zhangdong 2019.11.04
//渠道低价sku ID
define('LOW_PRICE_ID', '142');
//当天时间
define('CUR_DAY', date('Y-m-d'));
//运费系数_元/千克
define('SHIP_COST', '3.5');
//是否开启SQL监听日志 0 关闭 1 开启 add zhangdong 2019-12-16
define('SQL_IS_LISTEN', '1');
//超时未清点时间间隔（48小时）
define('ALLOT_EXPIRE_TIME', 48);
//超时未确认差异时间间隔（48小时）
define('DIFF_EXPIRE_TIME', 48);
//超时未开单时间间隔（48小时）
define('BILLING_EXPIRE_TIME', 48);
//超时未入库时间间隔（48小时）
define('STORAGE_EXPIRE_TIME', 48);
//超时未入库时间间隔（48小时）
define('PRICEING_EXPIRE_TIME', 48);
//比较提货日期
define('MODIFY_T_DATE', "2018-08-31");
//比较到货日期
define('MODIFY_D_DATE', "2018-09-31");
//美元汇率
define('DOLLAR_RATE', "1");
//销售折扣默认毛利率档位（百分比形式）
define('MARGIN_RATE_PERCENT', "8%");