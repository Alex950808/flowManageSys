<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//测试路由 add by zhangdong on the 2018.06.22
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Vone\Controllers', 'middleware' => ['admin.user']], function ($api) {
        $api->post('user/login', 'AuthController@authenticate');  //登录授权
        $api->post('app/AuthLogin', 'AuthController@AuthLogin');  //app登录授权
        $api->post('user/h5Login', 'AuthController@AuthLogin');  //h5登录授权
        $api->post('user/register_stop', 'AuthController@register_stop');
        $api->get('tests', 'TestsController@index');
        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            $api->get('tests/{id}', 'TestsController@show');
            $api->get('user/me', 'AuthController@AuthenticatedUser');
        });
    });

    $api->group(['namespace' => 'App\Api\Vone\Controllers'], function ($api) {
        //公告模块-公告列表 add by zongxing on the 2018.09.04
        $api->get('Notice/noticeList', 'NoticeController@noticeList');

        //商品模块-获取商品分类信息 add by zongxing on the 2019.01.23
        $api->get('goods/getCatInfo', 'GoodsController@getCatInfo');

        //商品模块-批量更新品牌信息 add by zongxing on the 2019.01.24
        $api->get('goods/updateGoodsBrandInfo', 'GoodsController@updateGoodsBrandInfo');
    });


    //销售模块和物流模块路由 add by zhangdong on the 2018.06.23
    $api->group(['namespace' => 'App\Api\Vone\Controllers\Sell', 'middleware' => ['admin.user']], function ($api) {
        //不需要权限的接口
        $api->group(['middleware' => 'jwt.auth',], function ($api) {
            //需求管理-提报需求-商品上传 add by zhangdong on the 2018.06.25 由于商品需求上传规则有变于2018-10.17停用
            $api->post('demand/goodsUpload', 'DemandController@goodsUpload');
            //需求管理-提报需求-需求商品上传-可能已经停用 add by zhangdong on the 2018.10.17
            $api->post('demand/demGoodsUp', 'DemandController@demGoodsUp');
            //需求管理-需求列表-新增需求页面 add by zhangdong on the 2018.10.18
            $api->get('demand/newDemPage', 'DemandController@newDemPage');
            //需求列表-需求详情 add by zhangdong on the 2018.10.18
            $api->get('demand/queryDemDetail', 'DemandController@queryDemDetail');
            //需求列表-需求详情-修改定价折扣 add by zhangdong on the 2018.10.19
            $api->post('demand/updateSaleRate', 'DemandController@updateSaleRate');
            //需求管理-需求列表-根据需求单号获取商品需求数据 add by zhangdong on the 2018.09.27
            $api->post('demand/demandDetail', 'DemandController@demandDetail');
            //商品分配管理-商品实际分配（按部门）- 获取批次单列表 add by zhangdong on the 2018.09.28
            $api->get('goodsAllot/getRealPurList', 'GoodsAllotController@getRealPurList');
            //商品分配管理-商品实际分配（按部门）- 根据批次单号获取对应商品信息 add by zhangdong on the 2018.09.29
            $api->get('goodsAllot/getRealGoodsInfo', 'GoodsAllotController@getRealGoodsInfo');
            //商品分配管理-商品实际分配（按部门）- 以批次单号为基准对商品数量进行分配 add by zhangdong on the 2018.09.29
            $api->get('goodsAllot/updateRealAllotNum', 'GoodsAllotController@updateRealAllotNum');
            //商品分配管理-商品分配预测-查看汇总-停用 add by zhangdong on the 2018.07.05
            $api->get('goodsAllot/getGoodsAllot', 'GoodsAllotController@getGoodsAllot');
            //商品分配管理-商品分配-获取采购单列表(不论是否挂了需求单) add by zhangdong on the 2018.09.30
            $api->get('goodsAllot/getPurOrderList', 'GoodsAllotController@getPurOrderList');
            //商品分配管理-商品分配-采购单列表-点击采购单号进入对应需求单号 add by zhangdong on the 2018.09.30
            $api->get('goodsAllot/getPurDemOrdList', 'GoodsAllotController@getPurDemOrdList');
            //商品分配管理-商品分配-采购单列表-需求单列表-点击需求单号进入需求单对应的商品信息 add by zhangdong on the 2018.09.30
            $api->get('goodsAllot/getPurDemOrdInfo', 'GoodsAllotController@getPurDemOrdInfo');

            //商品分配管理-商品分配预测-获取批次预测分配详情 add by zongxing on the 2018.08.29
            $api->get('goodsAllot/getGoodsAllotPredict', 'GoodsAllotController@getGoodsAllotPredict');
            //商品分配管理-商品分配预测-获取批次实时分配详情 add by zongxing on the 2018.08.29
            $api->get('goodsAllot/getGoodsAllotReal', 'GoodsAllotController@getGoodsAllotReal');

            //商品分配管理-实采商品分配-实采分配查看 add by zhangdong on the 2018.07.09
            $api->get('goodsAllot/goodsRealAllot', 'GoodsAllotController@goodsRealAllot');

            //物流模块-商品清点-清点商品数量 add by zhangdong on the 2018.07.09
            $api->post('logistics/allotGoodsNum', 'LogisticsController@allotGoodsNum');
            //物流模块-商品清点-清点商品数量-提交按钮 add by zhangdong on the 2018.07.10
            $api->post('logistics/sureAllot', 'LogisticsController@sureAllot');
            //物流模块-商品清点-清点数据批量上传 create by zongxing 2018.09.07
            $api->post('logistics/uploadAllotData', 'LogisticsController@uploadAllotData');

            //物流模块-商品清点-清点页面-下载要清点的商品数据 add by zhangdong on the 2018.07.10
            $api->get('logistics/downloadAllotGoods', 'LogisticsController@downloadAllotGoods');
            //物流模块-商品清点-清点页面-上传要清点的商品数据 add by zhangdong on the 2018.07.11---stop
            $api->post('logistics/upAllotGoods', 'LogisticsController@upAllotGoods');
            //物流模块-入库管理-入库单页面-确认入库 add by zhangdong on the 2018.07.11
            $api->post('logistics/inputStorage', 'LogisticsController@inputStorage');

            //采购模块-提报需求-商品上传 add by zhangdong on the 2018.06.25
            $api->post('purchase_demand/doUploadDemand', 'DemandController@doUploadDemand');
            //销售模块-需求管理-需求商品报价-获取需求列表 add by zhangdong on the 2018.10.09
            $api->get('demand/getNeedGoodsList', 'DemandController@getNeedGoodsList');
            //销售模块-需求管理-需求商品报价-获取需求列表-点击报价计算-获取需求单下面的商品信息 add by zhangdong on the 2018.10.10
            $api->get('demand/queryNeedGoodsInfo', 'DemandController@queryNeedGoodsInfo');
            //点击报价计算-报价计算页面-新增自采毛利率 add by zhangdong on the 2018.10.12
            $api->post('demand/addMarginRate', 'DemandController@addMarginRate');
            //点击报价计算-报价计算页面-单个修改定价折扣 add by zhangdong on the 2018.10.12
            $api->post('demand/modifyPricingRate', 'DemandController@modifyPricingRate');
            //点击报价计算-报价计算页面-批量修改定价折扣 add by zhangdong on the 2018.10.13
            $api->post('demand/batchModPricRate', 'DemandController@batchModPricRate');
            //销售模块-需求管理-获取费用列表 add by zhangdong on the 2018.10.15
            $api->get('demand/getChargeList', 'DemandController@getChargeList');
            //销售模块-需求管理-获取费用列表-新增费用项 add by zhangdong on the 2018.10.15
            $api->post('demand/addCharge', 'DemandController@addCharge');
            //销售模块-需求管理-获取费用列表-修改费用项 add by zhangdong on the 2018.10.16
            $api->post('demand/modifyCharge', 'DemandController@modifyCharge');
            //物流模块-待发货订单-上传发货订单 add by zhangdong on the 2018.11.07
            $api->post('order/upDeliverOrd', 'OrderController@upDeliverOrd');

            //商品分配管理-需求采购列表 create by zongxing 2018.10.18--stop
            //$api->get('goodsAllot/demandPurchaseList', 'GoodsAllotController@demandPurchaseList');
            //销售模块-商品分配管理-需求单采购期详情 create by zongxing 2018.10.18
            $api->get('goodsAllot/demandPurchaseDetail', 'GoodsAllotController@demandPurchaseDetail');
            //销售模块-商品分配管理-需求单汇总信息 create by zongxing 2018.11.07
            $api->get('goodsAllot/demandAllotDetail', 'GoodsAllotController@demandAllotDetail');

            //销售模块-在途商品管理-需求单对应采购期的批次列表 create by zongxing 2018.11.21
            $api->get('goodsPassage/passageRealPurchaseList', 'DemandController@passageRealPurchaseList');
            //销售模块-在途商品管理-需求单对应采购期的批次详情 create by zongxing 2018.11.21
            $api->get('goodsPassage/passageRealPurchaseDetail', 'DemandController@passageRealPurchaseDetail');

            //物流模块-商品清点-清点页面数据组装 add by zhangdong on the 2018.07.06
            $api->get('logistics/allotGoods', 'LogisticsController@allotGoods');
            //物流模块-商品入库-入库页面数据组装 add by zhangdong on the 2018.07.06
            $api->get('logistics/allotStockGoods', 'LogisticsController@allotStockGoods');

        });

        //销售模块的所有请求都必须登录才可访问（token授权）
        //需要权限的接口
        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            //销售模块-需求管理-获取列表数据 add by zhangdong on the 2018.06.25
            $api->get('demand/manager', 'DemandController@getDemandList');
            //需求单列表 新版 zhangdong 2020.01.10
            $api->post('demand/demandList', 'DemandController@demandList');
            //销售模块-需求管理-获取预采标记列表数据 zhangdong 2019.03.04
            $api->get('demand/advanceBuyList', 'DemandController@advanceBuyList');
            //需求管理-过往需求-获取过往需求单（采购单）列表数据 add by zhangdong on the 2018.06.27
            $api->get('demand/getCompletePurList', 'DemandController@getCompletePurList');
            //需求管理-提报需求-获取页面数据 add by zhangdong on the 2018.06.26
            $api->post('demand/getPageData', 'DemandController@getPageData');
            //需求管理-提报需求-手动添加商品-搜索-停用 add by zhangdong on the 2018.06.26
            $api->post('demand/getGoodsData', 'DemandController@getGoodsData');
            //需求管理-提报需求-手动添加商品-搜索-添加需求商品-停用 add by zhangdong on the 2018.06.26
            $api->post('demand/insertDemandGoods', 'DemandController@insertDemandGoods');

            //ERP订单管理-获取订单列表 add by zhangdong on the 2018.11.08
            $api->get('order/getOrderList', 'OrderController@getOrderList');
            //ERP订单管理-订单列表-订单详情 add by zhangdong on the 2018.11.12
            $api->get('order/orderDetail', 'OrderController@orderDetail');
            //ERP订单管理-获取待发货订单(销售) add by zhangdong on the 2018.06.29
            $api->get('order/sellWaitOrder', 'OrderController@getWaiteOrder');
            //ERP订单管理-获取待发货订单(物流) add by zhangdong on the 2018.06.29
            $api->get('order/ligWaitOrder', 'OrderController@getWaiteOrder');
            //ERP订单管理-获取超时发货订单(销售) add by zhangdong on the 2018.06.29
            $api->get('order/sellOutOrder', 'OrderController@getWaiteOrder');
            //ERP订单管理-获取超时发货订单(物流) add by zhangdong on the 2018.06.29
            $api->get('order/ligOutOrder', 'OrderController@getWaiteOrder');

            //ERP订单管理-获取待清关订单(销售) add by zhangdong on the 2018.06.29
            $api->get('order/sellCusOrder', 'OrderController@getWaitCusOrder');
            //ERP订单管理-获取待清关订单(物流) add by zhangdong on the 2018.06.29
            $api->get('order/ligCusOrder', 'OrderController@getWaitCusOrder');
            //ERP订单管理-获取超时清关订单(销售) add by zhangdong on the 2018.06.29
            $api->get('order/sellOutCusOrder', 'OrderController@getWaitCusOrder');
            //ERP订单管理-获取超时清关订单(物流) add by zhangdong on the 2018.06.29
            $api->get('order/ligOutCusOrder', 'OrderController@getWaitCusOrder');
            //清关管理-待清关订单-上传清关订单 add by zhangdong on the 2018.07.04
            $api->post('order/uploadWaitCusOrd', 'OrderController@uploadWaitCusOrd');

            //清关管理-获取转关中订单 add by zhangdong on the 2018.07.04
            $api->get('order/getCusTransitOrder', 'OrderController@getCusTransitOrder');
            //清关管理-超时未转关 add by zhangdong on the 2018.07.04
            $api->get('order/getOutCusTransitOrder', 'OrderController@getCusTransitOrder');
            //清关管理-待转关订单-上传已转关订单 add by zhangdong on the 2018.07.04
            $api->post('order/uploadCusTransitOrd', 'OrderController@uploadCusTransitOrd');

//            //商品分配管理-商品分配预测-页面数据获取 add by zhangdong on the 2018.07.02
//            $api->get('goodsAllot/getGoodsAllotList', 'GoodsAllotController@getGoodsAllotList');
//            //商品分配管理-实采商品分配-预测分配查看 add by zhangdong on the 2018.07.02
//            $api->get('goodsAllot/getGoodsActAllotList', 'GoodsAllotController@getGoodsAllotList');

            //商品分配管理-获取批次预测分配列表 create by zongxing 2018.07.09
            $api->get('goodsAllot/batchListPredict', 'GoodsAllotController@batchListPredict');
            //商品分配管理-获取批次实时分配列表 create by zongxing 2018.07.09
            $api->get('goodsAllot/batchListReal', 'GoodsAllotController@batchListReal');

            //商品分配管理-需求单列表 create by zongxing 2018.10.18
            $api->get('goodsAllot/demandAllotList', 'GoodsAllotController@demandAllotList');

            //销售数据管理-商品实时动销率 add by zhangdong on the 2018.07.05
            $api->get('goodSale/goodsRtMovePercent', 'GoodSaleController@goodsRtMovePercent');

            //物流模块-商品清点-获取待清点批次单 add by zhangdong on the 2018.07.06
            $api->get('logistics/goodsCheckList', 'LogisticsController@getAllotList');//zongxing modify 09.07
            //物流模块-商品清点-获取超时未清点批次单 add by zhangdong on the 2018.07.06
            $api->get('logistics/goodsCheckOutList', 'LogisticsController@getAllotExtireList');//zongxing modify 09.07

            //物流模块-商品入库-获取待入库采购单 add by zhangdong on the 2018.07.10
            $api->get('logistics/goodsStockList', 'LogisticsController@goodsStockList');
            //物流模块-商品入库-获取超时待入库采购单 add by zhangdong on the 2018.07.10
            $api->get('logistics/goodsStockOutList', 'LogisticsController@goodsStockOutList');


//            //采购模块-需求管理-获取列表数据 add by zongxing on the 2018.09.13
//            $api->get('purchase_demand/purchaseList', 'DemandController@getDemandList');
//            //需求管理-过往需求-获取过往需求列表 add by zongxing on the 2018.09.13
//            $api->get('purchase_demand/purchaseOutList', 'DemandController@getCompletePurList');
//            //采购模块-提报需求-获取页面数据 add by zongxing on the 2018.09.13
//            $api->post('purchase_demand/uploadDemand', 'DemandController@getPageData');

            //销售模块-在途商品管理-在途需求单列表 create by zongxing 2018.11.21
            $api->get('goodsPassage/demandPassageList', 'DemandController@demandPassageList');

            //MIS订单管理-需求单列表数据 add by zhangdong on the 2018.06.25
            $api->get('misOrder/misDemandList', 'DemandController@getDemandList');
            //MIS订单管理-获取总单列表 add by zhangdong on the 2018.12.08
            $api->post('misOrder/getMisOrderList', 'MisOrderController@getMisOrderList');
            //MIS订单管理-获取总单详情 add by zhangdong on the 2018.12.11
            $api->post('misOrder/getMisOrderDetail', 'MisOrderController@getMisOrderDetail');
            //MIS订单管理-获取订单拆分详情 add by zhangdong on the 2018.12.11
            $api->post('misOrder/getMosDetail', 'MisOrderController@getOrderSplitDetail');
            //MIS订单管理-导入总单页面 add by zhangdong on the 2018.12.08
            $api->get('misOrder/importOrderPage', 'MisOrderController@importOrderPage');
            //MIS订单管理-导入总单 add by zhangdong on the 2018.12.08
            $api->post('misOrder/importOrder', 'MisOrderController@importOrder');
            //总单详情-查看总单新品 zhangdong 2019.04.15
            $api->post('misOrder/getOrderNewGoods', 'MisOrderController@getOrderNewGoods');
            //总单新品列表-导出总单新品 zhangdong 2019.06.06
            $api->post('misOrder/exportOrdNew', 'MisOrderController@exportOrdNew');
            //总单新品列表-批量更新总单新品 zhangdong 2019.06.06
            $api->post('misOrder/importOrdNew', 'MisOrderController@importOrdNew');
            //总单新品-查询单条新品数据 zhangdong 2019.04.15
            $api->post('misOrder/queryNewGoodsData', 'MisOrderController@queryNewGoodsData');
            //总单新品-修改新品信息 zhangdong 2019.04.15
            $api->post('misOrder/modifyNewGoodsData', 'MisOrderController@modifyNewGoodsData');
            //总单新品-单条商品新增 zhangdong 2019.04.15
            $api->post('misOrder/addNewGoodsData', 'MisOrderController@addNewGoodsData');
            //总单新品-商品批量新增 zhangdong 2019.04.17
            $api->post('misOrder/batchAddNewGoods', 'MisOrderController@batchAddNewGoods');
            //总单新品-新增规格页面 zhangdong 2019.04.22
            $api->post('misOrder/addSpecPage', 'MisOrderController@addSpecPage');
            //总单新品-新增规格-品名搜索 zhangdong 2019.04.22
            $api->post('misOrder/searchGoodsName', 'MisOrderController@searchGoodsName');
            //总单新品-新增规格-提交 zhangdong 2019.04.22
            $api->post('misOrder/addSpecData', 'MisOrderController@addSpecData');

            //总单新品-补充新品到对应总单中 zhangdong 2019.04.18
            $api->post('misOrder/replenishGoodsIntoOrder', 'MisOrderController@replenishGoodsIntoOrder');
            //MIS订单管理-总单详情-预判数据导出 zhangdong 2019.04.24
            $api->post('misOrder/exportAdvance', 'MisOrderController@exportAdvance');
            //MIS订单管理-总单详情-预判数据导入 zhangdong 2019.04.25
            $api->post('misOrder/importAdvance', 'MisOrderController@importAdvance');
            //MIS订单管理-总单详情-商品价格变动详情 zhangdong 2019.06.19
            $api->post('misOrder/getGoodsNewPrice', 'MisOrderController@getGoodsNewPrice');
            //MIS订单管理-商品新价格页面-修改价格 zhangdong 2019.06.20
            $api->post('misOrder/modifyNewPrice', 'MisOrderController@modifyNewPrice');
            //MIS订单管理-商品新价格页面-提交价格 zhangdong 2019.06.20
            $api->post('misOrder/submitNewPrice', 'MisOrderController@submitNewPrice');
            //MIS订单管理-总单详情-完成预判 zhangdong 2019.03.04
            $api->post('misOrder/finishAdvance', 'MisOrderController@finishAdvance');
            //MIS订单管理-总单详情-完成挂靠 zhangdong 2019.03.19
            $api->post('misOrder/finishAffiliated', 'MisOrderController@finishAffiliated');
            //MIS订单管理-总单挂靠(加入交付时间和销售账户) add by zhangdong 2018.12.08
            $api->post('misOrder/orderAffiliate', 'MisOrderController@orderAffiliate');
            //MIS订单管理-总单批量挂靠(加入交付时间和销售账户) zhangdong 2019.03.21
            $api->post('misOrder/batchAffiliate', 'MisOrderController@batchAffiliate');
            //MIS订单管理-总单详情-批量挂靠交付时间zhangdong 2019.01.08
            $api->post('misOrder/affiliateTime', 'MisOrderController@affiliateTime');
            //MIS订单管理-总单根据交付时间和销售账户进行分单 add by zhangdong 2018.12.10
            $api->post('misOrder/orderSubmenu', 'MisOrderController@orderSubmenu');
            //首页-DD单品排行 zhangdong 2019.09.02
            $api->post('misOrder/ddGoodsRankList', 'MisOrderController@ddGoodsRankList');
            //APP-DD单品排行 zhangdong 2019.09.03
            $api->get('misOrder/ddGoodsRankApp', 'MisOrderController@ddGoodsRankApp');
            //获取销售用户 zhangdong 2019.09.03
            $api->get('misOrder/getSaleUser', 'MisOrderController@getSaleUser');
            //MIS订单管理-获取子订单列表 add by zhangdong on the 2018.12.11
            $api->post('misOrder/getSubList', 'MisOrderController@getSubList');
            //子订单列表-获取子订单详情 add by zhangdong on the 2018.12.12
            $api->post('misOrder/getSubDetail', 'MisOrderController@getSubDetail');
            //子订单列表-子单分单（现货单和需求单） add by zhangdong 2018.12.12
            $api->post('misOrder/subOrdSubmenu', 'MisOrderController@subOrdSubmenu');
            //现货单列表 add by zhangdong 2018.12.15
            $api->post('misOrder/getSpotList', 'MisOrderController@getSpotList');
            //子订单列表-获取现货单详情 zhangdong 2018.12.15
            $api->post('misOrder/getSpotDetail', 'MisOrderController@getSpotDetail');
            //子订单列表-取消现货单并将订单推送至erp zhangdong 2018.12.17
            $api->post('misOrder/cancelSpotOrd', 'MisOrderController@cancelSpotOrd');
            //子单详情-导出订单 zhangdong 2018.12.17
            $api->get('misOrder/exportSubOrd', 'MisOrderController@exportSubOrd');
            //子单详情-导入DD单数据 zhangdong 2018.12.18
            $api->post('misOrder/importSubOrd', 'MisOrderController@importSubOrd');
            //子订单详情-DD单数据提交 zhangdong 2019.04.29
            $api->post('misOrder/submitSubOrd', 'MisOrderController@submitSubOrd');
            //总单详情-导入DD子单数据 zhangdong 2019.05.21
            $api->post('misOrder/importDDSubOrd', 'MisOrderController@importDDSubOrd');
            //总单详情-修改DD子单数据 zhangdong 2019.05.21
            $api->post('misOrder/modifyDDSubData', 'MisOrderController@modifyDDSubData');
            //总单详情-DD数据提交 zhangdong 2019.05.22
            $api->post('misOrder/submitDData', 'MisOrderController@submitDData');
            //总单详情-查看总单下的子单 zhangdong 2019.05.23
            $api->post('misOrder/getMisOrderSplit', 'MisOrderController@getMisOrderSplit');
            //现货单列表-erp订单推送 zhangdong 2018.12.14
            $api->post('misOrder/erpOrderPush', 'MisOrderController@erpOrderPush');
            //总单详情-获取销售客户下的销售账号 zhangdong 2018.12.20
            $api->post('misOrder/listSaleAccount', 'MisOrderController@listSaleAccount');
            //总单报价-修改销售折扣 zhangdong 2018.12.20
            $api->post('misOrder/modSaleDiscount', 'MisOrderController@modSaleDiscount');
            //总单报价-根据选定自采毛利率批量修改销售折扣 zhangdong 2019.01.18
            $api->post('misOrder/batchModSaleDiscount', 'MisOrderController@batchModSaleDiscount');
            //总单报价-根据选定的sku批量修改对应销售折扣 zhangdong 2019.03.01
            $api->post('misOrder/modSaleDiscountBySpec', 'MisOrderController@modSaleDiscountBySpec');
            //总单报价 add by zhangdong on the 2018.12.08
            $api->post('misOrder/ordGoodsOffer', 'MisOrderController@ordGoodsOffer');
            //总单报价-导出商品信息 zhangdong 2019.06.10
            $api->post('misOrder/exportOffer', 'MisOrderController@exportOffer');
            //总单报价-导入商品最终折扣 zhangdong 2019.06.12
            $api->post('misOrder/importOffer', 'MisOrderController@importOffer');
            //总单报价-完成报价 zhangdong 2019.01.09
            $api->post('misOrder/finishOffer', 'MisOrderController@finishOffer');


            //需求单详情-商品预采标记 zhangdong 2018.12.20
            $api->get('demand/demandGoodsMark', 'DemandController@demandGoodsMark');
            //MIS订单管理-子单详情-修改待采量 zhangdong 2018.12.26
            $api->post('misOrder/modifyWaitNum', 'MisOrderController@modifyWaitNum');
            //MIS订单管理-总单详情-总单导出 zhangdong 2018.12.26
            $api->get('misOrder/misOrderExport', 'MisOrderController@misOrderExport');
            //子单详情-修改锁库数量 zhangdong 2019.01.11
            $api->post('misOrder/modifyWaitLockNum', 'MisOrderController@modifyWaitLockNum');
            //子单详情-修改子单备注 zhangdong 2019.01.11
            $api->post('misOrder/modifyRemark', 'MisOrderController@modifyRemark');
            //获取日志列表
            $api->post('logger/getLoggerList', 'LoggerController@getLoggerList');
            //获取日志详情
            $api->post('logger/getLoggerDetail', 'LoggerController@getLoggerDetail');
            //获取版本日志记录列表 zhangdong 2019.06.20
            $api->post('logger/getVersionList', 'LoggerController@getVersionList');
            //新增版本日志记录 zhangdong 2019.06.20
            $api->post('logger/addVersionLog', 'LoggerController@addVersionLog');
            //编辑版本日志记录 zhangdong 2019.06.21
            $api->post('logger/editVersionLog', 'LoggerController@editVersionLog');
        });
        //以下皆为无需登录接口 2020.03.20
        //定时任务-从erp获取订单管理数据-无需登录 add by zhangdong on the 2018.06.27
        $api->get('order/getErpOrderData', 'OrderController@getErpOrderData');
        //定时任务-从erp获取物流单号-无需登录 add by zhangdong on the 2018.07.14
        $api->get('order/getLogisticsNo', 'OrderController@getLogisticsNo');
        //erp平台货品推送-无需登录 add by zhangdong on the 2018.11.14
        $api->post('erp/erpPgp', 'ErpController@erpPlatformGoodsPush');
        //erp库存同步-无需登录-仅做测试，此接口是计划任务 add by zhangdong on the 2018.12.03
        $api->post('erp/erpStock', 'ErpController@sycGoodsStock');
        //定时任务-取消三天内未上传DD单的现货单
        $api->post('misOrder/planCancelSpotOrder', 'MisOrderController@planCancelSpotOrder');
        //ERP-获取货品档案数据 zhangdong 2020.02.03
        $api->post('erp/goodsQuery', 'ErpController@goodsQuery');
        //越洋店铺-批量更新SKU价格 add zhangdong 2020.02.10
        $api->post('overseas/updatePrice', 'OverseasController@updatePrice');
        //脚本工具-将MIS所需要的图片从全球哒哒中分离出来，并保存
        //到新的文件夹中-专用 add zhangdong 2020.03.20
        $api->post('tool/separateImg', 'ToolController@separateImg');
        //到新的文件夹中-远程操作，无需下载原图片-专用 add zhangdong 2020.03.20
        $api->post('tool/separateRemoteImg', 'ToolController@separateRemoteImg');
        //商品图片处理工具 add zhangdong 2020.04.24
        $api->post('tool/imgTool', 'ToolController@imgTool');


    });


    //采购模块路由 create by zongxing 2018.06.25
    $api->group(['namespace' => 'App\Api\Vone\Controllers', 'middleware' => ['admin.user']], function ($api) {
        //定时任务-更新采购期状态为进行中 add by zongxing on the 2018.08.09
        $api->get('purchase_date/changeDateToStart', 'DateController@changeDateToStart');

        //定时任务-更新采购期状态为提货中 add by zongxing on the 2018.08.09
//        $api->get('purchase_date/changeDateToBill', 'DateController@changeDateToBill');
//        //定时任务-更新采购期状态为关闭 add by zongxing on the 2018.08.09
//        $api->get('purchase_date/changeDateToDisable', 'DateController@changeDateToDisable');


        //不需要权限的接口
        $api->group(['middleware' => 'jwt.auth',], function ($api) {
            //采购数据-检查采购期是否过期 create by zongxing 2018.08.07
            //$api->get('purchase_data/checkPurchaseDatePass', 'DataController@checkPurchaseDatePass_stop');

            //批量导出所有品牌折扣信息 add by zongxing on the 2019.03.22
            $api->get('Discount/discountExport', 'DiscountController@discountExport');
            //根据商品批量导出折扣信息 add by zongxing on the 2019.03.22
            $api->post('Discount/discountExportByGoods', 'DiscountController@discountExportByGoods');

            //采购期-采购期管理-创建采购期 add by zongxing on the 2018.06.26
            $api->post('purchase_date/create_date', 'DateController@createPurchaseDate');
            //采购期-采购期管理-确认提交编辑采购期 add by zongxing on the 2018.07.11
            $api->post('purchase_date/do_edit_date', 'DateController@doEditPurchaseDate');
            //采购期-采购期管理-获取过往采购期汇总详情 create by zongxing 2018.07.11
            $api->post('purchase_date/pass_date_total_detail', 'DateController@getTotalDetail');
            //采购期-采购期管理-下载过往采购期总表 create by zongxing 2018.07.11
            $api->get('purchase_date/download_pass_date_total_list', 'DateController@downLoadTotalList');

            //首页-获取采购期批次任务列表 add by zongxing on the 2018.08.21
            $api->get('purchase_date/batch_task_list', 'DateController@batchTaskList');
            //首页-更新采购期批次任务状态 add by zongxing on the 2018.08.22
            $api->post('purchase_task/changeTaskStatus', 'TaskController@changeTaskStatus');
            //首页-获取需要提醒的采购期批次任务信息 add by zongxing on the 2018.08.21
            $api->get('purchase_date/getPurchaseTask', 'DateController@getPurchaseTask');

            //任务模块-通过角色id获取管理员列表 create by zongxing 2018.08.22---stop
            //$api->post('admin_user_role/userOfRole', 'AdminUserController@userOfRole');
            //任务模块-获取任务模板表 create by zongxing 2018.07.11
            $api->get('task/taskModelList', 'TaskController@taskModelList');
            //任务模块-提交编辑任务 create by zongxing 2018.08.20
            $api->post('purchase_task/doEditTask', 'TaskController@doEditTask');

            //商品模块-上传商品主图 add by zongxing 2018.08.24---stop
            //$api->post('goods/uploadGoodsImg', 'GoodsController@uploadGoodsImg');
            //商品模块-商品管理-商品列表-新增单个商品 add by zongxing on the 2018.08.24
            $api->post('goods/doAddGoods', 'GoodsController@doAddGoods');
            //商品模块-商品管理-商品规格列表-提交新增商品规格 add by zongxing on the 2018.08.24
            $api->post('goods/doAddGoodsSpec', 'GoodsController@doAddGoodsSpec');
            //商品模块-商品管理-商品列表-提交编辑商品 add by zongxing on the 2018.08.25
            $api->post('goods/doEditGoods', 'GoodsController@doEditGoods');
            //商品模块-商品管理-商品列表-提交编辑商品规格 add by zongxing on the 2018.08.25
            $api->post('goods/doEditGoodsSpec', 'GoodsController@doEditGoodsSpec');
            //商品模块-商品管理-商品规格列表-提交新增商品编码 add by zongxing on the 2019.02.20
            $api->post('goods/doAddGoodsCode', 'GoodsController@doAddGoodsCode');
            //商品模块-商品管理-商品规格列表-提交编辑商品编码 add by zongxing on the 2019.02.20
            $api->post('goods/doEditGoodsCode', 'GoodsController@doEditGoodsCode');

            //商品模块-销售用户商品列表 add by zhangdong on the 2018.10.31
            $api->get('goods/UserGoodsList', 'GoodsController@UserGoodsList');
            //商品模块-上传销售用户商品数据 add by zhangdong on the 2018.11.01
            $api->post('goods/upSaleGoods', 'GoodsController@upSaleGoods');
            //商品模块-单个修改销售折扣 add by zhangdong on the 2018.11.01
            $api->post('goods/modSaleRate', 'GoodsController@modSaleRate');
            //商品模块-批量修改销售折扣 add by zhangdong on the 2018.11.01
            $api->post('goods/batchSaleDis', 'GoodsController@batchSaleDis');
            //商品模块-获取erp商品列表 zhangdong on the 2019.01.07
            $api->post('goods/getErpGoodsList', 'GoodsController@getErpGoodsList');
            //商品模块-导出ERP商品 zhangdong on the 2019.01.25
            $api->get('goods/exportErpGoods', 'GoodsController@exportErpGoods');
            //商品模块-新增商品积分 zhangdong on the 2019.01.30
            $api->post('goods/addGoodsIntegral', 'GoodsController@addGoodsIntegral');
            //商品模块-常备商品列表-单个新增常备商品 zhangdong 2019.09.16
            $api->post('goods/addStandbyGoods', 'GoodsController@addStandbyGoods');
            //商品模块-常备商品列表-批量新增常备商品 zhangdong 2019.09.16
            $api->post('goods/uploadStandbyGoods', 'GoodsController@uploadStandbyGoods');
            //商品模块-常备商品列表-批量新增常备商品 zhangdong 2019.09.16
            $api->post('goods/uploadStandbyGoods', 'GoodsController@uploadStandbyGoods');
            //客户订单报价-上传需要报价的SKU zhangdong 2019.11.25
            $api->post('goods/offerSkuUpload', 'GoodsController@offerSkuUpload');
            //客户订单报价-报价单列表 zhangdong 2019.11.26
            $api->post('goods/offerList', 'GoodsController@offerList');
            //客户订单报价-获取报价详情 zhangdong 2019.11.25
            $api->post('goods/getOfferDetail', 'GoodsController@getOfferDetail');
            //客户订单报价--单个修改销售折扣 zhangdong 2019.11.25
            $api->post('goods/modifySkuDiscount', 'GoodsController@modifySkuDiscount');
            //客户订单报价--导出数据 zhangdong 2019.11.26
            $api->get('goods/exportSkuOffer', 'GoodsController@exportSkuOffer');
            //客户订单报价--导入报价 zhangdong 2019.11.27
            $api->post('goods/importSkuOffer', 'GoodsController@importSkuOffer');
            //大批发报价--上传需要报价的SKU zhangdong 2020.03.24
            $api->post('goods/importWholesaleSku', 'GoodsController@importWholesaleSku');
            //大批发报价-报价单列表 zhangdong 2020.03.24
            $api->post('goods/wholesaleList', 'GoodsController@wholesaleList');
            //大批发报价-获取报价详情 zhangdong 2020.03.25
            $api->post('goods/getWholesaleDetail', 'GoodsController@getWholesaleDetail');
            //大批发报价-获取报价详情-新版 zhangdong 2020.04.08
            $api->post('goods/getWholeDetail', 'GoodsController@getWholeDetail');
            //大批发报价-详情-修改运输方式和参考折扣 zhangdong 2020.05.15
            $api->post('goods/modifyWholeData', 'GoodsController@modifyWholeData');
            //大批发报价-详情-批量修改运输方式和参考折扣 zhangdong 2020.05.15
            $api->post('goods/batchModifyWholeData', 'GoodsController@batchModifyWholeData');
            //大批发报价--导出数据 zhangdong 2020.03.27
            $api->get('goods/exportWholesaleOffer', 'GoodsController@exportWholesaleOffer');
            //大批发报价--新版--导出数据 zhangdong 2020.05.12
            $api->get('goods/wholesaleExport', 'GoodsController@wholesaleExport');


            //采购模块-采购数据管理-采购数据实时上传 create by zongxing 2018.07.05
            $api->post('purchase_data/do_upload_data', 'DataController@doUploadData');
            //采购模块-采购数据管理-采购数据修正上传 create by zongxing 2018.08.17
            $api->post('purchase_data/doUploadDiffData', 'DataController@doUploadDiffData');
            //采购模块-采购数据管理-下载采购数据总表 create by zongxing 2018.07.11---暂保留
            $api->get('purchase_data/download_data_total_list', 'DataController@downLoadTotalList');
            //采购模块-采购数据管理-批次到货设置任务 create by zongxing 2018.08.28
            $api->post('purchase_batch/doBatchSetting', 'DataController@doBatchSetting');
            //采购模块-采购数据管理-批次到货设置运费 create by zongxing 2018.08.28
            $api->post('purchase_batch/doBatchSettingPost', 'DataController@doBatchSettingPost');
            //采购模块-采购数据管理-获取采购数据汇总信息 create by zongxing 2018.07.10
            $api->post('purchase_data/data_total_detail', 'DataController@getTotalDetail');
            //采购模块-采购数据管理-获取采购批次详情 create by zongxing 2018.07.10
            $api->post('purchase_batch/batch_detail', 'DataController@getBatchDetail');
            //采购模块-采购数据管理-获取采购批次汇总详情 create by zongxing 2018.07.10
            $api->post('purchase_batch/batch_total_detail', 'DataController@getTotalDetail');

            //采购实采批次-下载采购批次单表 create by zongxing 2018.07.11---暂保留
            $api->get('purchase_batch/download_batch_list', 'DataController@downLoadList');
            //采购实采批次-下载采购批次总表 create by zongxing 2018.07.11---暂保留
            $api->get('purchase_batch/download_batch_total_list', 'DataController@downLoadTotalList');

            //优采推荐-打开采购需求汇总详情页 create by zongxing 2018.07.06---stop
            //$api->post('purchase_recommend/recommend_total_detail', 'RecommendController@getTotalDetail_stop');
            //优采推荐-打开采购需求详情页 create by zongxing 2018.07.03---stop
            //$api->post('purchase_recommend/recommend_detail', 'RecommendController@getDemandDetail_stop');

            //采购模块-优采推荐管理-打开采购需求详情页 create by zongxing 2018.07.03
            $api->post('purchase_recommend/recommend_detail', 'RecommendController@recommendDetail');
            //采购模块-优采推荐管理-打开采购需求汇总详情页 create by zongxing 2018.07.06
            $api->post('purchase_recommend/recommend_total_detail', 'RecommendController@recommendTotalDetail');
            //优采推荐-下载采购需求单表 create by zongxing 2018.07.03---stop
            //$api->get('purchase_recommend/download_recommend_list', 'RecommendController@downLoadRecommendList');
            //采购模块-优采推荐管理-下载采购需求总表 create by zongxing 2018.07.05
            $api->get('purchase_recommend/download_recommend_total_list', 'RecommendController@downLoadRecommendTotalList');

            //采购模块-差异管理-获取采购批次汇总详情 create by zongxing 2018.07.10
            $api->post('purchase_diff/diff_total_detail', 'DiffController@getTotalDetail');
            //采购模块-差异管理-下载采购批次单表 create by zongxing 2018.07.11
            $api->get('purchase_diff/download_diff_list', 'DiffController@downLoadDiffList');
            //采购模块-差异管理-下载采购批次总表 create by zongxing 2018.07.11
            $api->get('purchase_diff/download_diff_total_list', 'DiffController@downLoadTotalList');
            //采购模块-差异管理-增加商品备注 create by zongxing 2018.08.02
            $api->post('purchase_diff/add_diff_remark', 'DiffController@addDiffRemark');
            //采购模块-差异管理-改变批次状态（确认差异或拒绝） create by zongxing 2018.07.10
            $api->post('purchase_diff/change_diff_status', 'DiffController@changeStatus');

            //采购模块-erp开单-获取采购批次汇总详情 create by zongxing 2018.07.10
            $api->post('purchase_billing/billing_total_detail', 'BillingController@getTotalDetail');
            //采购模块-erp开单-改变批次状态 create by zongxing 2018.07.10
            $api->post('purchase_billing/change_billing_status', 'BillingController@changeStatus');
            //采购模块-erp开单-下载采购批次单表 create by zongxing 2018.07.11
            $api->get('purchase_billing/download_billing_list', 'BillingController@downLoadList');
            //采购模块-erp开单-下载采购批次总表 create by zongxing 2018.07.11
            $api->get('purchase_billing/download_billing_total_list', 'BillingController@downLoadTotalList');

            //采购模块-采购编号管理-确认提交编辑采购id create by zongxing 2018.07.11
            $api->post('purchase_user/do_edit_user', 'UserController@doEidtUser');

            //权限模块-提交编辑角色 create by zongxing 2018.07.28
            $api->post('permission/editRole', 'RoleController@editRole');

            //采购模块-采购方式-确认提交编辑采购渠道 create by zongxing 2019.01.25
            $api->post('channel/doEditChannel', 'ChannelController@doEditChannel');
            //采购模块-采购方式-确认提交编辑采购方式 create by zongxing 2018.07.11
            $api->post('purchase_method/do_edit_method', 'MethodController@doEditMethod');

            //采购模块-采购渠道-下载品牌折扣模板 create by zongxing 2018.06.27
            $api->get('purchase_discount/download_discount_demo', 'DiscountController@downLoadDemo');
            //采购模块-采购渠道-下载当前采购折扣表 create by zongxing 2018.06.28
            $api->get('purchase_discount/download_discount_current', 'DiscountController@downLoadCurrent');

            //采购需求-打开采购需求汇总详情页 create by zongxing 2018.07.06---stop
            //$api->post('purchase_demand/demand_total_detail', 'PurchaseDemandController@getTotalDetail');

            //商品模块-改变采购需求中商品的信息 create by zongxing 2018.07.12---stop
            //$api->post('purchase_demand/change_goods_info', 'PurchaseDemandController@changeGoodsInfo_stop');
            //商品模块-确认提交采购需求审核 create by zongxing 2018.07.02---stop
            //$api->post('purchase_demand/change_demand_status', 'PurchaseDemandController@changeDemandStatus_stop');
            //商品模块-下载采购需求单表 create by zongxing 2018.07.03---stop
            //$api->get('purchase_demand/download_demand_list', 'PurchaseDemandController@downLoadDemandList_stop');
            //商品模块-下载采购需求总表 create by zongxing 2018.07.11---stop
            //$api->get('purchase_demand/download_demand_total_list', 'PurchaseDemandController@downLoadTotalList_stop');


            //任务模块-任务管理-提交新增任务 create by zongxing 2018.08.20
            $api->post('purchase_task/add_task_detail', 'TaskController@addTaskDetail');
            //采购模块-需求管理-待分配-提交需求挂期 add by zongxing on the 2018.09.25
            $api->post('purchase_demand/doDemandAttach', 'DemandController@doDemandAttach');

            //采购模块-需求管理-待分配-获取某一采购期需求详情 add by zongxing on the 2018.09.26
            $api->post('purchase_demand/purchaseDemandAllot', 'DemandController@purchaseDemandAllot');
            //采购模块-需求管理-待分配-打开编辑商品可采页面 add by zongxing on the 2018.09.26
            $api->post('purchase_demand/editDemandAllot', 'DemandController@editDemandAllot');
            //采购模块-需求管理-待分配-提交编辑采购期可采数 add by zongxing on the 2018.09.26
            $api->post('purchase_demand/doEditDemandAllot', 'DemandController@doEditDemandAllot');
            //采购模块-需求管理-待分配-提交确认的需求分配方案 add by zongxing on the 2018.09.26
            $api->post('purchase_demand/doDemandAllot', 'DemandController@doDemandAllot');
            //采购模块-需求管理-已分配-获取某一采购期需求详情 add by zongxing on the 2018.09.26
            $api->post('purchase_demand/purchaseDemandAlready', 'DemandController@purchaseDemandAlready');
            //采购模块-需求管理-查看需求详情 add by zongxing on the 2018.10.26
            $api->get('purchase_demand/getDemandDetial', 'DemandController@getDemandDetial');

            //采购模块-采购数据管理-实时数据上传-通过方式获取渠道 add by zongxing on the 2018.10.23 --- stop
            //$api->get('purchase_data/getChannelByMethod', 'DataController@getChannelByMethod');


            //销售模块-销售客户管理-确认新增销售客户 add by zongxing on the 2018.11.08
            $api->post('purchase_customer/doAddCustomer', 'SaleUserController@doAddCustomer');
            //销售模块-销售客户管理-确认编辑销售客户 add by zongxing on the 2018.12.05
            $api->post('purchase_customer/doEditCustomer', 'SaleUserController@doEditCustomer');
            //销售模块-销售客户管理-确认新增销售客户账号 add by zongxing on the 2018.11.08
            $api->post('sale_user/doAddSaleAccount', 'SaleUserController@doAddSaleAccount');
            //销售模块-销售客户管理-确认编辑销售客户账号 add by zongxing on the 2018.12.05
            $api->post('sale_user/doEditSaleAccount', 'SaleUserController@doEditSaleAccount');

            //财务模块-财务渠道管理-新增资金渠道 add by zongxing on the 2018.12.06
            $api->post('fund_channel/doAddFundChannel', 'FinanceController@doAddFundChannel');
            //财务模块-财务渠道管理-确认编辑资金渠道 add by zongxing on the 2018.12.06
            $api->post('fund_channel/doEditFundChannel', 'FinanceController@doEditFundChannel');
            //财务模块-财务渠道管理-确认编辑自有可支配资金 add by zongxing on the 2018.12.07
            $api->post('fund/doEditDiscretionaryFund', 'FinanceController@doEditDiscretionaryFund');
            //财务模块-财务渠道管理-确认编辑可融资金 add by zongxing on the 2018.12.07
            $api->post('fund/doEditRongFund', 'FinanceController@doEditRongFund');

            //采购模块-预采需求管理-获取预采需求单详情 add by zongxing on the 2018.12.12
            $api->get('purchase_predict/predictDetail', 'DemandController@predictDetail');
            //采购模块-预采需求管理-下载预采需求列表 add by zongxing on the 2018.12.10
            $api->get('purchase_predict/downloadPredictDemand', 'DemandController@downloadPredictDemand');
            //采购模块-预采需求管理-确认上传预采批次 add by zongxing on the 2018.12.10
            $api->post('purchase_predict/doUploadPredictReal', 'DemandController@doUploadPredictReal');

            //销售模块-销售客户管理-确认新增销售客户回款规则 add by zongxing on the 2018.12.13
            $api->post('sale_user/doAddRefundRules', 'SaleUserController@doAddRefundRules');
            //销售模块-销售客户管理-确认编辑销售客户回款规则 add by zongxing on the 2018.12.13
            $api->post('sale_user/doEditRefundRules', 'SaleUserController@doEditRefundRules');

            //销售模块-MIS订单管理管理-获取YD订单列表 add by zongxing on the 2018.12.14
            $api->get('mis_order/misSubOrderList', 'DemandController@misSubOrderList');
            //销售模块-MIS订单管理管理-获取YD订单下可发货的商品 add by zongxing on the 2018.12.14
            $api->get('mis_order/misCanDeliverGoods', 'DemandController@misCanDeliverGoods');

            //销售模块-MIS订单管理管理-生成发货单 add by zongxing on the 2018.12.15
            $api->post('mis_order/comfirmDeliverGoods', 'DemandController@comfirmDeliverGoods');


            //物流模块-MIS订单管理管理-配货单详情 add by zongxing on the 2018.12.15
            $api->get('mis_order/distributionOrderDetail', 'DeliverController@distributionOrderDetail');
            //物流模块-MIS订单管理管理-下载配货单 add by zongxing on the 2018.12.15
            $api->get('mis_order/downloadDistributionOrder', 'DeliverController@downloadDistributionOrder');
            //物流模块-MIS订单管理管理-确认上传配货单 add by zongxing on the 2018.12.15
            $api->post('mis_order/doUploadDistributionOrder', 'DeliverController@doUploadDistributionOrder');

            //财务模块-MIS订单管理管理-确认收款 add by zongxing on the 2018.12.15
            $api->get('mis_order/financeChangeOrderStatus', 'FinanceController@financeChangeOrderStatus');

            //财务模块-需求资金管理-需求资金详情列表 add by zongxing on the 2018.12.17
            $api->get('fund/fundListDetail', 'FinanceController@fundListDetail');

            //采购模块-优采规则管理-采购折扣管理-品牌VIP折扣上传 create by zongxing 2018.06.27
            $api->post('discount/uploadVipDiscount', 'DiscountController@uploadVipDiscount');

            //销售模块-MIS订单管理管理-发货单列表-下载发货单 add by zongxing on the 2018.12.27
            $api->get('deliver/downloadSellDeliverOrder', 'DeliverController@downloadSellDeliverOrder');
            //销售模块-MIS订单管理管理-发货单列表-上传理货报告 add by zongxing on the 2018.12.27
            $api->post('deliver/doUploadSellDeliverOrder', 'DeliverController@doUploadSellDeliverOrder');

            //物流模块-MIS订单管理管理-退货单详情 add by zongxing on the 2018.12.27
            $api->get('deliver/sellReturnOrderDetail', 'DeliverController@sellReturnOrderDetail');
            //物流模块-MIS订单管理管理-下载退货单数据 add by zongxing on the 2018.12.27
            $api->get('deliver/downloadSellReturnGoods', 'DeliverController@downloadSellReturnGoods');
            //物流模块-MIS订单管理管理-上传退货清点数据 add by zongxing on the 2018.12.27
            $api->post('deliver/doUploadSellReturnGoods', 'DeliverController@doUploadSellReturnGoods');
            //物流模块-MIS订单管理管理-退货商品清点入库 add by zongxing on the 2018.12.27
            $api->post('deliver/returnGoodsAllotStock', 'DeliverController@returnGoodsAllotStock');

            //财务模块-核价管理-获取待核价批次详情 create by zongxing 2018.12.29
            $api->get('finance/watiPricingBatchDetail', 'FinanceController@getBatchDetail');
            //财务模块-核价管理-下载待核价批次数据 create by zongxing 2019.01.03
            $api->get('finance/downloadPricingBatchDetail', 'FinanceController@downloadPricingBatchDetail');
            //财务模块-核价管理-上传核价批次数据 add by zongxing on the 2019.01.03
            $api->post('finance/doUploadPricingBatch', 'FinanceController@doUploadPricingBatch');
            //财务模块-核价管理-改变批次状态（确认核价） create by zongxing 2019.01.03
            $api->post('finance/change_price_status', 'FinanceController@changeStatus');
            //财务模块-核价管理-提交批次核价 add by zongxing on the 2019.01.21
            $api->post('finance/doPricingBatch', 'FinanceController@doPricingBatch');

            //采购模块-供应商管理-添加供应商 add by zongxing on the 2019.01.07
            $api->post('supplier/addSupplier', 'SupplierController@addSupplier');

            //数据统计模块-订单管理-需求单采购期统计列表 add by zongxing on the 2019.01.08
            $api->get('demand/demandStasticsList', 'StatisticsController@demandStasticsList');
            //数据统计模块-订单管理-需求单对应采购期的批次列表 create by zongxing 2019.01.08
            $api->get('demand/demandRealPurchaseList', 'StatisticsController@demandRealPurchaseList');
            //数据统计模块-订单管理-需求单对应采购期批次中的商品信息 create by zongxing 2019.01.10
            $api->get('demand/demandRealPurchaseDetail', 'StatisticsController@demandRealPurchaseDetail');
            //数据统计模块-订单管理-需求单对应采购期中的商品信息 create by zongxing 2019.01.08
            $api->get('demand/demandPurchaseGoodsDetail', 'StatisticsController@demandPurchaseGoodsDetail');

            //财务模块-待回款资金管理-待回款资金需求单列表 add by zongxing on the 2019.01.30
            $api->get('Fund/waitRefundDemandList', 'FinanceController@waitRefundDemandList');

            //财务模块-待回款资金管理-待回款资金需求详情 add by zongxing on the 2019.01.30
            $api->get('Fund/waitRefundDemandDetail', 'FinanceController@waitRefundDemandDetail');

            //采购模块-优采推荐管理-采购折扣-新增品牌折扣 create by zongxing 2019.02.14
            $api->get('Discount/addBrandDiscount', 'DiscountController@addBrandDiscount');

            //销售模块-MIS订单管理-需求单列表-编辑期望到仓日 create by zongxing 2019.02.15
            $api->post('Demand/editArriveStoreTime', 'DemandController@editArriveStoreTime');

            //商品模块-商品标签管理-新增商品标签 add by zongxing on the 2019.02.21
            $api->post('Goods/doAddGoodsLabel', 'GoodsController@doAddGoodsLabel');
            //商品模块-商品标签管理-打开编辑商品标签页面 add by zongxing on the 2019.02.21
            $api->get('Goods/editGoodsLabel', 'GoodsController@editGoodsLabel');
            //商品模块-商品标签管理-提交编辑商品标签 add by zongxing on the 2019.02.21
            $api->post('Goods/doEditGoodsLabel', 'GoodsController@doEditGoodsLabel');

            //采购模块-需求管理-下载需求单指定采购期下的待分配数据 add by zongxing on the 2019.02.28
            $api->get('Demand/downLoadWaitAllotGoodsInfo', 'DemandController@downLoadWaitAllotGoodsInfo');
            //采购模块-需求管理-上传需求单指定采购期下的分配数据 add by zongxing on the 2019.02.28
            $api->post('Demand/uploadWaitAllotGoodsInfo', 'DemandController@uploadWaitAllotGoodsInfo');

            //数据统计模块-订单管理-订单统计列表（实时） add by zongxing on the 2019.02.26
            $api->get('demand/orderCurrentStatisticsList', 'StatisticsController@orderStatisticsList');
            //数据统计模块-订单管理-订单统计列表(最终) add by zongxing on the 2019.02.26
            $api->get('demand/orderEndStatisticsList', 'StatisticsController@orderStatisticsList');
            //数据统计模块-订单管理-子单统计列表 add by zongxing on the 2019.02.27
            $api->get('demand/subOrderStatisticsList', 'StatisticsController@subOrderStatisticsList');

            //数据统计模块-订单管理-删除合期数据 add by zongxing on the 2019.04.01
            $api->get('Statistics/delPurchaseSumDate', 'StatisticsController@delPurchaseSumDate');

            //数据统计模块-订单管理-商品统计列表 add by zongxing on the 2019.03.01
            $api->get('Statistics/goodsStatisticsList', 'StatisticsController@goodsStatisticsList');

            //数据统计模块-采购期统计管理-采购期渠道统计详情 add by zongxing on the 2019.03.15
            $api->get('Statistics/purchaseChannelStatisticsDetail', 'StatisticsController@purchaseChannelStatisticsDetail');

            //采购模块-优采规则管理-品牌采购折扣批量新增 add by zongxing on the 2019.03.21
            $api->post('Discount/batchAddBrandDiscount', 'DiscountController@batchAddBrandDiscount');

            //数据统计模块-采购期统计管理-当月实采商品统计列表 add by zongxing on the 2019.03.15
            $api->get('Statistics/currentGoodsStatisticsList', 'StatisticsController@currentGoodsStatisticsList');
			//数据统计模块-订单统计管理-下载合单对应需求单统计数据-日报 add by zongxing on the 2019.06.17
            $api->post('Statistics/downLoadDailyInfo', 'StatisticsController@downLoadDailyInfo');

            //采购模块-采购数据管理-获取待审核采购批次详情 create by zongxing 2018.07.10
            //$api->post('purchase_batch/batch_detail', 'DataController@getBatchDetail');
            $api->get('Batch/batchAuditDetail', 'BatchController@batchAuditDetail');


            //系统设置-财务模块-账户列表-提交新增渠道积分 create by zongxing 2019.04.23
            $api->post('Finance/doAddChannelIntegral', 'FinanceController@doAddChannelIntegral');

            //系统设置-财务模块-账户列表-提交编辑渠道积分 create by zongxing 2019.04.23
            $api->post('Finance/doEditChannelIntegral', 'FinanceController@doEditChannelIntegral');

            //财务模块-积分管理-待返积分批次详情 create by zongxing 2019.04.24
            $api->get('Finance/waitIntegralDetail', 'FinanceController@waitIntegralDetail');
            //财务模块-积分管理-确认提交积分 create by zongxing 2019.04.24
            $api->get('Finance/submitIntegral', 'FinanceController@submitIntegral');

            //系统设置-财务模块-账户列表-查看渠道统计情况 create by zongxing 2019.04.30
            $api->get('Finance/ChannelsStatisticsInfo', 'FinanceController@ChannelsStatisticsInfo');

            //系统设置-采购模块-采购折扣列表-打开新增折扣类型页面 create by zongxing 2019.05.05
            $api->get('Discount/addDiscountType', 'DiscountController@addDiscountType');
            //系统设置-采购模块-采购折扣列表-新增折扣类型 create by zongxing 2019.05.05
            $api->post('Discount/doAddDiscountType', 'DiscountController@doAddDiscountType');
            //系统设置-采购模块-采购折扣列表-获取折扣类型列表 create by zongxing 2019.05.05
            $api->get('Discount/discountTypeList', 'DiscountController@discountTypeList');
            //系统设置-采购模块-采购折扣列表-维护折扣类型对应的折扣 create by zongxing 2019.05.05
            $api->post('Discount/doUploadDiscountType', 'DiscountController@doUploadDiscountType');

            //系统设置-采购模块-新增折扣类型记录 create by zongxing 2019.09.02
            $api->post('Discount/doAddDiscountTypeLog', 'DiscountController@doAddDiscountTypeLog');
            //系统设置-采购模块-获取折扣类型记录列表 create by zongxing 2019.05.05
            $api->get('Discount/discountTypeLogList', 'DiscountController@discountTypeLogList');

            //系统设置-采购模块-采购折扣列表-获取渠道方式列表 create by zongxing 2019.05.05
            $api->get('Discount/methodChannelsTypeList', 'DiscountController@methodChannelsTypeList');

            //系统设置-采购模块-采购折扣列表-获取商品特殊折扣列表 create by zongxing 2019.05.05
            $api->post('Discount/getGmcDiscountList', 'DiscountController@getGmcDiscountList');

            //系统设置-采购模块-采购折扣列表 create by zongxing 2019.05.05
            $api->get('Discount/discountTotalList', 'DiscountController@discountTotalList');

            //系统设置-采购模块-折扣档位设置 create by zongxing 2019.05.05
            $api->post('Discount/discountTypeSetting', 'DiscountController@discountTypeSetting');
            //系统设置-采购模块-编辑折扣档位 create by zongxing 2019.07.26
            $api->post('Discount/editDiscountType', 'DiscountController@editDiscountType');

            //系统设置-财务模块-账户列表-折线图 create by zongxing 2019.05.08
            $api->get('Finance/accountDetail', 'FinanceController@accountDetail');

            //采购模块-采购任务管理-采购任务详情 create by zongxing 2019.05.14
            $api->get('Demand/purchaseTaskDetail', 'DemandController@purchaseTaskDetail');
            //采购模块-采购任务管理-下载汇总单的待分配数据 add by zongxing on the 2019.05.14
            $api->post('Demand/downLoadSumDemandGoodsInfo', 'DemandController@downLoadSumDemandGoodsInfo');
            //采购模块-采购任务管理-下载汇总单的待分配数据（无序） add by zongxing on the 2019.05.14
            $api->get('Demand/downLoadSdgInfoNoSort', 'DemandController@downLoadSdgInfoNoSort');
            //采购模块-需求管理-上传汇总单的分配数据 add by zongxing on the 2019.05.15
            $api->post('Demand/uploadSumDemandGoodsInfo', 'DemandController@uploadSumDemandGoodsInfo');
            //采购模块-采购任务管理-下载当日采购任务 add by zongxing on the 2019.05.14
            $api->post('Demand/downLoadTodayPurTask', 'DemandController@downLoadTodayPurTask');

            //采购模块-采购任务管理-打开采购数据上传页面（新） create by zongxing 2019.05.27
            $api->get('Batch/uploadBatchData', 'BatchController@uploadBatchData');
            //采购模块-采购数据管理-采购数据实时上传（新） create by zongxing 2019.05.17
            $api->post('Batch/doUploadBatchData', 'BatchController@doUploadBatchData');
            //采购模块-采购任务管理-品牌折扣列表 zhangdong 2019.10.29
            $api->post('Demand/brandDiscountList', 'DemandController@brandDiscountList');
            //系统设置-采购模块-商品追加折扣列表-商品渠道追加折扣维护 create by zongxing 2019.05.20
            $api->post('Discount/uploadGmcDiscount', 'DiscountController@uploadGmcDiscount');
            //系统设置-采购模块-商品追加折扣列表-下载特殊商品中的新品列表 create by zongxing 2019.10.22
            $api->post('Discount/downloadDiscountDiffGoods', 'DiscountController@downloadDiscountDiffGoods');

            //获取采购渠道信息 zhangdong 2019.12.12
            $api->post('Discount/getBuyChannel', 'DiscountController@getBuyChannel');
            //生成商品报价数据 zhangdong 2019.11.06
            $api->post('Discount/makeGoodsOffer', 'DiscountController@makeGoodsOffer');
            //获取商品报价导出文件 zhangdong 2019.11.06
            $api->post('Discount/getOfferFile', 'DiscountController@getOfferFile');
            //生成购物车报价数据 zhangdong 2019.11.27
            $api->post('Discount/makeCartOffer', 'DiscountController@makeCartOffer');
            //获取购物车报价导出文件 zhangdong 2019.12.02
            $api->post('Discount/getCartOfferFile', 'DiscountController@getCartOfferFile');

            //销售模块-需求管理-需求单详情-标记延期商品 zongxing 2019.05.22
            $api->post('Demand/markGoodsPostpone', 'DemandController@markGoodsPostpone');

            //系统设置-财务模块-账户列表-待返积分详情 create by zongxing 2019.04.24
            $api->get('Finance/waitIntegralInfo', 'FinanceController@waitIntegralInfo');

            //采购模块-采购任务管理-采购需求列表-打开追加合单页面 create by zongxing 2019.05.29
            $api->get('Demand/addSumDemand', 'DemandController@addSumDemand');
            //采购模块-采购任务管理-采购需求列表-追加合单 create by zongxing 2019.05.29
            $api->post('Demand/doAddSumDemand', 'DemandController@doAddSumDemand');

            //首页统计 create by zongxing 2019.05.29
            $api->get('Demand/firstPage', 'DemandController@firstPage');
            //首页统计 create by zongxing 2019.05.29
            $api->post('Demand/firstPage', 'DemandController@firstPage');
            //首页模块-获取合单下需求单的信息 create by zongxing 2019.05.14
            $api->get('Demand/sumDemandInfo', 'DemandController@sumDemandInfo');
            //首页模块-查看合单缺口统计 create by zongxing 2019.06.27
            $api->post('Demand/sumDiffInfo', 'DemandController@sumDiffInfo');

            //采购模块-采购任务管理-拆分合单需求单 create by zongxing 2019.06.28
            $api->post('Demand/splitSumDemand', 'DemandController@splitSumDemand');

            //审核模块-采购数据修正 create by zongxing 2019.09.23
            $api->post('Batch/modifyBatchData', 'BatchController@modifyBatchData');

            //获取指定商品最终折扣 create by zongxing 2019.10.15
            $api->post('Goods/getGoodsFinalDiscount', 'GoodsController@getGoodsFinalDiscount');

            //系统设置-采购模块-获取购物车商品列表 create by zongxing 2019.11.12
            $api->post('Goods/getShopCartList', 'GoodsController@getShopCartList');
            //系统设置-采购模块-上传购物车商品 create by zongxing 2019.11.12
            $api->post('Goods/uploadShopCartGoods', 'GoodsController@uploadShopCartGoods');
            //系统设置-采购模块-购物车列表-下载购物车商品中的新品列表 create by zongxing 2019.11.13
            $api->post('Goods/downloadShopDiffGoods', 'GoodsController@downloadShopDiffGoods');

            //系统设置-采购模块-获取商品报价列表 create by zongxing 2019.11.29
            $api->get('Goods/getGoodsSaleList', 'GoodsController@getGoodsSaleList');
            //系统设置-采购模块-上传商品报价 create by zongxing 2019.11.29
            $api->post('Goods/uploadGoodsSaleInfo', 'GoodsController@uploadGoodsSaleInfo');
            //系统设置-采购模块-商品报价列表-下载商品报价中的新品列表 create by zongxing 2019.11.29
            $api->post('Goods/downloadSaleDiffGoods', 'GoodsController@downloadSaleDiffGoods');

            //获取管理员信息 add by zongxing on the 2020.04.08
            $api->get('AdminUser/getAdminUserInfo', 'AdminUserController@getAdminUserInfo');
            //管理员头像上传 add by zongxing on the 2020.04.08
            $api->post('AdminUser/uploadAdminUserImg', 'AdminUserController@uploadAdminUserImg');

        });

        //需要权限的接口
        $api->group(['middleware' => 'jwt.auth',], function ($api) {//jwt_and_permission//jwt.auth
            //采购模块-采购期管理-创建采购期 create by zongxing 2018.07.11
            $api->get('purchase_date/channel_method_list', 'DateController@channel_method_list');
            //采购模块-采购期管理-打开编辑采购期详情页 add by zongxing on the 2018.07.02
            $api->post('purchase_date/edit_date', 'DateController@editPurchaseDate');
//            $api->post('purchase_date/edit_date', [
//                'middleware' => ['checkPermission'],
//                'uses' => 'DateController@editPurchaseDate'
//            ]);

            //采购模块-采购期管理-采购期列表 add by zongxing on the 2018.06.26
            $api->get('purchase_date/date_list', 'DateController@getDateList');
            //采购模块-采购期管理-过往采购期列表 add by zongxing on the 2018.06.26
            $api->get('purchase_date/pass_date_list', 'DateController@getPassDateList');
            //采购模块-采购期管理-关闭采购期 add by zongxing on the 2018.09.05
            $api->get('purchase_date/closePurchase', 'DateController@closePurchase');


            //采购单-批次单-分货列表-停用 add by zhangdong on the 2018.10.27
            $api->get('purchase_date/pur_real_list', 'DateController@pur_real_list');
            //批次单分货列表 zhangdong 2019.05.30
            $api->get('purchase_date/batchOrderList', 'DateController@batchOrderList');
            //根据总单号生成分货数据-此处数据的生成放到了合单提交的地方-停用 zhangdong 2019.05.30
            $api->post('purchase_date/generalSortData', 'DateController@generalSortData');
            //查看合单号对应的总分货数据 zhangdong 2019.05.31
            $api->post('purchase_date/sumSortDataList', 'DateController@sumSortDataList');
            //生成批次单分货数据 zhangdong 2019.06.04
            $api->post('purchase_date/makeBatchOrdSortData', 'DateController@makeBatchOrdSortData');
            //查看批次单分货数据 zhangdong 2019.06.04
            $api->post('purchase_date/getBatchOrdSortData', 'DateController@getBatchOrdSortData');
            //手动调整分配数据 zhangdong 2019.06.03
            $api->post('purchase_date/handleSortNum', 'DateController@handleSortNum');

            //销售模块-批次分货列表-查看部门分货数据-stop zhangdong 2019.02.19
            $api->get('purchase_date/getSortData', 'DateController@getDepartSortData');
            //采购期列表-商品部对部门分货-生成部门分货数据-stop zhangdong 2018.10.22 update 2019.02.19
            $api->post('purchase_date/generalDepartSortData', 'DateController@generalDepartSortData');
            //预采批次单-生成部门分货数据-stop zhangdong 2019.03.12
            $api->post('purchase_date/makePerDepartSortData', 'DateController@makePerDepartSortData');
            //采购期列表-商品部对部门分货-手动修改部门分货数量-stop zhangdong 2018.10.23 update 2019.02.19
            $api->post('purchase_date/dep_handle_goods', 'DateController@dep_handle_goods');
            //采购期列表-停用部门分货数据-stop zhangdong 2019.03.06
            $api->post('purchase_date/stopDepartSortData', 'DateController@stopDepartSortData');
            //查询用户分货数据-stop zhangdong 2019.02.22
            $api->get('purchase_date/getUserSortData', 'DateController@getUserSortData');
            //生成用户分货数据-stop zhangdong 2019.02.22
            $api->post('purchase_date/generalUserSortData', 'DateController@generalUserSortData');
            //批次单列表-商品部对用户分货-手动修改用户分货数量-stop zhangdong 2019.02.25
            $api->post('purchase_date/user_handle_goods', 'DateController@user_handle_goods');
            //各部门根据商品部分好的数据对相应的销售客户进行分货-stop add by zhangdong on the 2018.10.23
            $api->get('purchase_date/user_sort_goods', 'DateController@user_sort_goods');


            //采购模块-优采推荐管理-采购渠道-创建采购渠道 create by zongxing 2018.06.26
            $api->post('purchase_channels/create_channels', 'ChannelController@createChannels');
            //采购模块-优采推荐管理-采购渠道-采购渠道列表 create by zongxing 2018.06.26
            $api->get('purchase_channels/channels_list', 'ChannelController@getChannelsList');
            //采购模块-优采推荐管理-采购方式-打开编辑采购渠道详情页 create by zongxing 2018.07.11
            $api->post('channel/editChannel', 'ChannelController@editChannel');

            //采购模块-优采推荐管理-渠道团队列表 create by zongxing 2019.10.28
            $api->get('Channel/teamList', 'ChannelController@teamList');
            //采购模块-优采推荐管理-新增渠道团队 create by zongxing 2019.10.28
            $api->post('Channel/addTeam', 'ChannelController@addTeam');
            //采购模块-优采推荐管理-编辑渠道团队 create by zongxing 2019.10.28
            $api->post('Channel/editTeam', 'ChannelController@editTeam');


            //采购模块-优采推荐管理-采购方式-创建采购方式 create by zongxing 2018.06.26
            $api->post('purchase_method/create_method', 'MethodController@createMethod');
            //采购模块-优采推荐管理-采购方式-获取采购方式列表 create by zongxing 2018.06.26
            $api->get('purchase_method/method_list', 'MethodController@getMethodList');
            //采购模块-优采推荐管理-采购方式-打开编辑采购方式详情页 create by zongxing 2018.07.11
            $api->post('purchase_method/edit_method', 'MethodController@editMethod');

            //采购模块-优采推荐管理-采购成本系数-创建采购成本系数 create by zongxing 2018.06.26
            $api->post('purchase_cost/create_cost', 'CostController@createCost');
            //采购模块-优采推荐管理-采购成本系数-采购成本系数列表 create by zongxing 2018.06.26
            $api->get('purchase_cost/cost_list', 'CostController@getCostList');
            //采购模块-优采推荐管理-采购成本系数-改变采购成本系数状态 create by zongxing 2018.06.26
            $api->post('purchase_cost/change_cost_status', 'CostController@changeStatus');

            //采购模块-优采推荐管理-采购折扣-品牌折扣上传 create by zongxing 2018.06.27
            $api->post('purchase_discount/upload_discount', 'DiscountController@uploadDiscount');
            //品牌折扣上传数据-审核 zhangdong 2019.04.03
            $api->post('purchase_discount/auditBrandDiscount', 'DiscountController@auditBrandDiscount');
            //品牌折扣上传数据-数据提交 zhangdong 2019.04.03
            $api->post('purchase_discount/submitBrandDiscount', 'DiscountController@submitBrandDiscount');
            //审核列表 zhangdong 2019.04.08
            $api->post('purchase_discount/authList', 'DiscountController@authList');
            //品牌折扣上传数据详情 zhangdong 2019.04.08
            $api->post('purchase_discount/discountAuditDetail', 'DiscountController@discountAuditDetail');
            //品牌折扣上传数据-修改品牌折扣 zhangdong 2019.04.08
            $api->post('purchase_discount/modifyBrandDiscount', 'DiscountController@modifyBrandDiscount');


            //采购模块-需求管理-待分配列表数据 add by zongxing on the 2018.09.13
            $api->get('purchase_demand/waitAllotDemand', 'DemandController@waitAllotDemand');
            //采购模块-需求管理-待分配-获取符合需求的采购期列表 add by zongxing on the 2018.09.25
            $api->post('purchase_demand/demandAttach', 'DemandController@demandAttach');
            //采购模块-需求管理-待分配-获取采购期需求汇总详情 add by zongxing on the 2018.09.25
            $api->post('purchase_demand/demandAllot', 'DemandController@demandAllot');
            //采购模块-需求管理-已分配需求列表数据 add by zongxing on the 2018.09.13
            $api->get('purchase_demand/alreadyAllotDemand', 'DemandController@alreadyAllotDemand');
            //采购模块-需求管理-已分配-获取采购期需求汇总详情 create by zongxing 2018.09.27
            $api->post('purchase_demand/demandAlreadyDetail', 'DemandController@demandAlreadyDetail');

            //采购模块-需求管理-获取采购需求列表 create by zongxing 2018.06.29---stop
            //$api->get('purchase_demand/demand_list', 'PurchaseDemandController@getDemandList_stop');
            //商品模块-打开采购需求详情页 create by zongxing 2018.07.02---stop
            //$api->post('purchase_demand/demand_detail', 'PurchaseDemandController@getDemandDetail_stop');

            //采购模块-优采推荐管理-获取采购需求列表 create by zongxing 2018.07.03
            $api->get('purchase_recommend/recommend_list', 'RecommendController@getDemandList');

            //采购模块-采购数据管理-采购数据实时上传 create by zongxing 2018.07.05
            $api->get('purchase_data/upload_data', 'DataController@checkUpload');
            //采购模块-采购数据管理-获取采购数据列表 create by zongxing 2018.07.06
            $api->get('purchase_data/data_list', 'DataController@getDataList');
            //采购模块-采购数据管理-采购数据修正 create by zongxing 2018.08.17
            $api->get('purchase_data/modifyDataList', 'DataController@getBatchList');
            //采购模块-采购数据管理-实时数据修正检测 create by zongxing 2018.08.17
            $api->post('purchase_data/uploadDiffData', 'DataController@uploadDiffData');
            //采购模块-采购数据管理-预计到港批次列表 create by zongxing 2018.07.09
            $api->get('purchase_batch/batch_list', 'DataController@getBatchList');
            //采购模块-采购数据管理-批次到货设置 create by zongxing 2018.08.28
            $api->get('purchase_batch/batchSetting', 'DataController@batchSettingList');
            //采购模块-采购数据管理-批次审核列表 create by zongxing 2019.04.08
            $api->get('Batch/batchAuditList', 'BatchController@getBatchList');

            //采购模块-差异管理-获取待确认差异批次列表 create by zongxing 2018.07.10
            $api->get('purchase_diff/diff_list', 'DiffController@getBatchList');
            //采购模块-差异管理-获取超时未确认差异批次列表 create by zongxing 2018.07.16
            $api->get('purchase_diff/expire_diff_list', 'DiffController@getExtireBatchList');
            //采购模块-差异管理-获取采购批次详情 create by zongxing 2018.07.10
            $api->post('purchase_diff/diff_detail', 'DiffController@getBatchDetail');

            //采购模块-erp开单-获取采购批次列表 create by zongxing 2018.07.10
            $api->get('purchase_billing/billing_list', 'BillingController@getBatchList');
            //采购模块-erp开单-获取采购批次列表（超时差异处理） create by zongxing 2018.07.16
            $api->get('purchase_billing/expire_billing_list', 'BillingController@getExtireBatchList');
            //采购模块-erp开单-获取采购批次详情 create by zongxing 2018.07.10
            $api->post('purchase_billing/billing_detail', 'BillingController@getBatchDetail');

            //采购模块-数据管理-获取采购数据列表 create by zongxing 2018.08.06
            $api->get('data_management/dataManagementList', 'DataManagementController@getDataManagementList');
            //采购模块-数据管理-获取商品数据列表 create by zongxing 2018.08.06
            $api->get('data_management/dataGoodsList', 'DataManagementController@getDataGoodsList');

            //采购模块-采购编号管理-创建采购id create by zongxing 2018.07.04
            $api->post('purchase_user/create_user', 'UserController@createPurchaseUser');
            //采购模块-采购编号管理-获取采购id列表 create by zongxing 2018.07.10
            $api->get('purchase_user/user_list', 'UserController@getUserList');
            //采购模块-采购编号管理-打开编辑采购id页面 create by zongxing 2018.07.11
            $api->post('purchase_user/edit_user', 'UserController@eidtUser');

            //任务模块-任务管理-新增任务模板 create by zongxing 2018.08.20
            $api->post('purchase_task/add_task', 'TaskController@addTask');
            //任务模块-任务管理-新增自定义任务-检测任务模板 create by zongxing 2018.09.03
            $api->get('purchase_task/check_task_model', 'TaskController@checkTaskModel');
            //任务模块-任务管理-任务列表 create by zongxing 2018.08.20
            $api->get('purchase_task/task_list', 'TaskController@taskList');
            //任务模块-任务管理-编辑任务 create by zongxing 2018.08.20
            $api->post('purchase_task/editTask', 'TaskController@editTask');


            //商品模块-打开新增商品页面 add by zhangdong on the 2018.08.17
            $api->get('goods/addGoods', 'GoodsController@addGoods');
            //商品模块-批量新增商品 add by zongxing on the 2018.08.24
            $api->post('goods/uploadAddGoods', 'GoodsController@uploadAddGoods');
            //商品模块-打开商品编辑页面 add by zongxing on the 2018.08.25
            $api->get('goods/editGoods', 'GoodsController@editGoods');
            //商品模块-改变商品上下架状态 add by zongxing on the 2018.08.25
            $api->post('goods/changeSaleStatus', 'GoodsController@changeSaleStatus');
            //商品模块-平台商品列表 add by zongxing on the 2020.02.13
            $api->get('goods/goodsList', 'GoodsController@goodsList');
            //商品模块-平台商品详情 add by zongxing on the 2020.02.13
            $api->get('goods/goodsDetail', 'GoodsController@goodsDetail');
            //商品模块-补全商品代码到对照表中 zhangdong 2019.11.06
            $api->post('goods/insertErpPrdNo', 'GoodsController@insertErpPrdNo');
            //商品模块-商品管理-常备商品列表 add by zongxing on the 2019.09.17
            $api->get('Goods/standbyGoodsList', 'GoodsController@standbyGoodsList');
            //商品模块-商品管理-常备商品详情 add by zongxing on the 2019.09.17
            $api->get('Goods/standbyGoodsInfo', 'GoodsController@standbyGoodsInfo');
            //商品模块-商品管理-编辑常备商品 add by zongxing on the 2019.09.17
            $api->post('Goods/doEditStandbyGoods', 'GoodsController@doEditStandbyGoods');
            //商品模块-商品管理-下载常备商品数据 add by zongxing on the 2019.09.19
            $api->post('Goods/downLoadStandbyGoodsInfo', 'GoodsController@downLoadStandbyGoodsInfo');
            //采购模块-采购数据管理-常备商品采购数据实时上传 create by zongxing 2019.05.17
            $api->post('Goods/uploadStandbyGoodsData', 'GoodsController@uploadStandbyGoodsData');


            //商品模块-商品规格列表 add by zongxing on the 2018.11.15---stop
            //$api->get('goods/goodsSpecList', 'GoodsController@goodsSpecList');
            //商品模块-商品管理-商品规格列表-打开新增商品编码页面 add by zongxing on the 2019.02.20
            $api->get('goods/addGoodsCode', 'GoodsController@addGoodsCode');

            //商品模块-商品管理-商品规格列表-打开新增商品规格页面 add by zongxing on the 2019.02.22
            $api->get('goods/addGoodsSpec', 'GoodsController@addGoodsSpec');
            //商品模块-商品管理-商品列表-打开商品规格编辑页面 add by zongxing on the 2018.08.25
            $api->get('goods/editGoodsSpec', 'GoodsController@editGoodsSpec');
            //商品模块-打开商品编码编辑页面 add by zongxing on the 2018.08.25
            $api->get('goods/editGoodsCode', 'GoodsController@editGoodsCode');

            //商品模块-商品标签管理-商品标签列表 add by zongxing on the 2019.02.21
            $api->get('Goods/goodsLabelList', 'GoodsController@goodsLabelList');

            //商品模块-主采折扣列表 add by zongxing on the 2018.09.14---stop
            //$api->get('goods/mainDiscountList', 'GoodsController@mainDiscountList');
            //商品模块-主采折扣上传 create by zongxing 2018.09.14---stop
            //$api->post('goods/uploadMainDiscount', 'GoodsController@uploadMainDiscount');

            //公告模块-新增公告 add by zongxing on the 2018.09.04
            $api->post('notice/addNotice', 'NoticeController@addNotice');

            //销售模块-销售客户管理-销售客户列表 add by zhangdong on the 2018.10.31
            $api->get('goods/saleUserList', 'GoodsController@saleUserList');
            //销售模块-销售客户管理-新增销售客户 add by zongxing on the 2018.11.08
            $api->get('purchase_customer/addCustomer', 'SaleUserController@addCustomer');
            //销售模块-销售客户管理-编辑销售客户 add by zongxing on the 2018.11.08
            $api->get('purchase_customer/editCustomer', 'SaleUserController@editCustomer');

            //销售模块-销售客户账号管理-新增销售客户账号 add by zongxing on the 2018.12.05
            $api->get('sale_user/addSaleAccount', 'SaleUserController@addSaleAccount');
            //销售模块-销售客户账号管理-销售客户账号列表 add by zongxing on the 2018.12.05
            $api->get('sale_user/saleAccountList', 'SaleUserController@saleAccountList');
            //销售模块-销售客户账号管理-编辑销售客户账号 add by zongxing on the 2018.12.05
            $api->get('sale_user/editSaleAccount', 'SaleUserController@editSaleAccount');

            //财务模块-财务渠道管理-新增资金渠道 add by zongxing on the 2018.12.06
            $api->get('fund_channel/addFundChannel', 'FinanceController@addFundChannel');
            //财务模块-财务渠道管理-编辑资金渠道 add by zongxing on the 2018.12.06
            $api->get('fund_channel/editFundChannel', 'FinanceController@editFundChannel');
            //财务模块-财务渠道管理-资金渠道列表 add by zongxing on the 2018.12.06
            $api->get('fund_channel/getFundChannelList', 'FinanceController@getFundChannelList');

            //财务模块-财务渠道管理-自有可支配资金列表 add by zongxing on the 2018.12.07
            $api->get('fund/discretionaryFundList', 'FinanceController@discretionaryFundList');
            //财务模块-财务渠道管理-编辑自有可支配资金 add by zongxing on the 2018.12.07
            $api->get('fund/editDiscretionaryFund', 'FinanceController@editDiscretionaryFund');

            //财务模块-财务渠道管理-可融资金列表 add by zongxing on the 2018.12.07
            $api->get('fund/rongFundList', 'FinanceController@rongFundList');
            //财务模块-财务渠道管理-编辑可融资金 add by zongxing on the 2018.12.07
            $api->get('fund/editRongFund', 'FinanceController@editRongFund');

            //采购模块-预采需求管理-预采需求列表 add by zongxing on the 2018.12.10
            $api->get('purchase_predict/predictDemandList', 'DemandController@predictDemandList');
            //采购模块-预采需求管理-上传预采批次 add by zongxing on the 2018.12.10
            $api->get('purchase_predict/uploadPredictReal', 'DemandController@uploadPredictReal');

            //销售模块-销售客户账号管理-新增销售客户回款规则 add by zongxing on the 2018.12.13
            $api->get('sale_user/addRefundRules', 'SaleUserController@addRefundRules');
            //销售模块-销售客户账号管理-销售客户回款规则列表 add by zongxing on the 2018.12.13
            $api->get('sale_user/refundRulesList', 'SaleUserController@refundRulesList');
            //销售模块-销售客户账号管理-编辑销售客户回款规则 add by zongxing on the 2018.12.13
            $api->get('sale_user/editRefundRules', 'SaleUserController@editRefundRules');

            //财务模块-待回款资金管理-待回款资金列表 add by zongxing on the 2018.12.17
            $api->get('fund/waitRefundList', 'FinanceController@waitRefundList');
            //财务模块-需求资金管理-需求资金列表 add by zongxing on the 2018.12.17
            $api->get('fund/demandFundList', 'FinanceController@demandFundList');
            //财务模块-需求资金管理-需求单剩余需求资金列表 add by zongxing on the 2019.01.22
            $api->get('fund/demandGoodsFundList', 'FinanceController@demandGoodsFundList');

            //财务模块-MIS订单管理管理-发货单列表 add by zongxing on the 2018.12.15
            $api->get('mis_order/financeDeliverOrderList', 'FinanceController@financeDeliverOrderList');

            //采购模块-采购折扣管理-获取当前采购折扣列表 create by zongxing 2018.06.29
            $api->get('purchase_discount/discount_list', 'DiscountController@getDiscountList');
            //采购模块-采购折扣管理-获取当前采购vip折扣列表 create by zongxing 2018.12.26
            $api->get('discount/vip_discount_list', 'DiscountController@getVipDiscountList');

            //物流模块-MIS订单管理管理-配货单列表 add by zongxing on the 2018.12.15
            $api->get('mis_order/distributionOrderList', 'DeliverController@distributionOrderList');

            //销售模块-MIS订单管理管理-发货单列表 add by zongxing on the 2018.12.26
            $api->get('deliver/sellDeliverOrderList', 'DeliverController@sellDeliverOrderList');

            //物流模块-MIS订单管理管理-退货单列表 add by zongxing on the 2018.12.26
            $api->get('deliver/sellReturnOrderList', 'DeliverController@sellReturnOrderList');

            //财务模块-核价管理-获取待核价批次列表 create by zongxing 2018.12.29
            $api->get('Finance/watiPricingBatchList', 'FinanceController@getBatchList');

            //采购模块-供应商管理-供应商列表 add by zongxing on the 2019.01.07
            $api->get('supplier/supplierList', 'SupplierController@supplierList');

            //数据统计模块-订单管理-总单统计列表 add by zongxing on the 2019.01.07
            $api->get('demand/misOrderStatisticsList', 'StatisticsController@misOrderStatisticsList');

            //采购模块-优采推荐管理-采购折扣-提交新增品牌折扣 create by zongxing 2019.02.14
            $api->post('Discount/doAddBrandDiscount', 'DiscountController@doAddBrandDiscount');

            //系统设置-销售模块-新增自采毛利率 add by zongxing on the 2019.03.13
            $api->post('Goods/addMarginRate', 'GoodsController@addMarginRate');
            //系统设置-销售模块-自采毛利率列表 add by zongxing on the 2019.03.13
            $api->get('Goods/getMarginRateList', 'GoodsController@getMarginRateList');
            //系统设置-销售模块-删除自采毛利率 add by zongxing on the 2019.03.13
            $api->get('Goods/delMarginRate', 'GoodsController@delMarginRate');



            //数据统计模块-采购期统计管理-采购期渠道统计列表 add by zongxing on the 2019.03.15
            $api->get('Statistics/purchaseChannelStatisticsList', 'StatisticsController@purchaseChannelStatisticsList');

            //采购模块-采购数据管理-提交批次审核 add by zongxing on the 2019.04.03
            $api->get('Batch/doBatchAudit', 'BatchController@doBatchAudit');
            //采购模块-采购数据管理-提交批次数据 add by zongxing on the 2019.04.11
            $api->get('Batch/uploadBatchAudit', 'BatchController@uploadBatchAudit');

            //系统设置-财务模块-账户列表-新增渠道积分 create by zongxing 2019.04.23
            $api->get('Finance/addChannelIntegral', 'FinanceController@addChannelIntegral');
            //系统设置-财务模块-账户列表-编辑渠道积分 create by zongxing 2019.04.23
            $api->get('Finance/editChannelIntegral', 'FinanceController@editChannelIntegral');
            //系统设置-财务模块-账户列表-积分余额列表 create by zongxing 2019.04.24
            $api->get('Finance/accountList', 'FinanceController@accountList');
            //财务模块-积分管理-待返积分列表 create by zongxing 2019.04.24
            $api->get('Finance/waitIntegralList', 'FinanceController@waitIntegralList');


            //采购模块-采购任务管理-采购需求列表 create by zongxing 2019.05.13
            $api->get('Demand/purchaseDemandList', 'DemandController@purchaseDemandList');
            //采购模块-采购任务管理-采购需求详情 create by zongxing 2019.05.27
            $api->get('Demand/purchaseDemandDetail', 'DemandController@purchaseDemandDetail');
            //采购模块-采购任务管理-采购需求列表-合并需求单 create by zongxing 2019.05.13
            $api->post('Demand/sumPurchaseDemand', 'DemandController@sumPurchaseDemand');
            //采购模块-采购任务管理-采购任务列表 create by zongxing 2019.05.13
            $api->get('Demand/purchaseTaskList', 'DemandController@purchaseTaskList');
            //财务模块-核价管理-批次核价列表 zhangdong 2019.07.11
            $api->post('finance/batchCorePriceList', 'FinanceController@batchCorePriceList');

            //财务模块-毛利数据管理-获取折扣类型列表 create by zongxing 2019.05.05
            $api->get('Finance/discountTypeList', 'FinanceController@discountTypeList');
            //财务模块-毛利数据管理-生成毛利数据 zhangdong 2019.07.18
            $api->post('Finance/generateProfit', 'FinanceController@generateProfit');
            //财务模块-毛利数据管理-毛利数据列表 zongxing 2019.07.31
            $api->get('Finance/profitList', 'FinanceController@profitList');
            //财务模块-毛利数据管理-毛利数据详情 zongxing 2019.08.01
            $api->post('Finance/profitDetail', 'FinanceController@profitDetail');
            //财务模块-毛利数据管理-下载毛利数据 zongxing 2019.08.02
            $api->get('Finance/downLoadProfitInfo', 'FinanceController@downLoadProfitInfo');
            //财务模块-毛利数据管理-停用毛利数据 zongxing 2019.08.05
            $api->get('Finance/deleteProfitInfo', 'FinanceController@deleteProfitInfo');

            //财务模块-毛利数据管理-新增折扣类型种类 zhangdong 2019.08.02
            $api->post('Finance/doAddDiscountCat', 'FinanceController@doAddDiscountCat');
            //财务模块-毛利数据管理-获取折扣类型种类列表 add by zongxing on the 2019.07.31
            $api->get('Finance/getDiscountCatList', 'FinanceController@getDiscountCatList');
            //财务模块-毛利数据管理-新增毛利公式 add by zongxing on the 2019.07.31
            $api->post('Finance/doAddProfitFormula', 'FinanceController@doAddProfitFormula');
            //财务模块-毛利数据管理-获取毛利公式列表 add by zongxing on the 2019.07.31
            $api->get('Finance/getProfitFormulaList', 'FinanceController@getProfitFormulaList');

            //财务模块-毛利数据管理-打开新增折扣种类公式页面 add by zongxing on the 2019.07.31
            $api->get('Finance/addCatFormula', 'FinanceController@addCatFormula');
            //财务模块-毛利数据管理-新增折扣种类公式 add by zongxing on the 2019.07.31
            $api->post('Finance/doAddCatFormula', 'FinanceController@doAddCatFormula');
            //财务模块-毛利数据管理-获取折扣折扣种类公式列表 add by zongxing on the 2019.07.31
            $api->get('Finance/getCatFormulaList', 'FinanceController@getCatFormulaList');

            //财务模块-汇率管理-汇率列表 create by zongxing 2019.07.23
            $api->get('Finance/exchangeRateList', 'FinanceController@exchangeRateList');
            //财务模块-汇率管理-上传汇率 create by zongxing 2019.07.23---stop
            $api->post('Finance/uploadExchangeRate', 'FinanceController@uploadExchangeRate');

            //采购模块-任务管理-采购金额列表 create by zongxing 2019.08.13
            $api->get('Finance/getCardConsumeList', 'FinanceController@getCardConsumeList');
            //采购模块-任务管理-打开维护结账卡消费记页面 create by zongxing 2019.08.13
            $api->get('Finance/addCardConsume', 'FinanceController@addCardConsume');
            //采购模块-任务管理-维护结账卡消费 create by zongxing 2019.08.13
            $api->post('Finance/doAddCardConsume', 'FinanceController@doAddCardConsume');

            //采购模块-采购任务管理-采购任务列表-App create by zongxing 2019.08.22
            $api->get('Demand/purchaseTaskListApp', 'DemandController@purchaseTaskListApp');

            //系统设置-销售模块-目标列表 create by zongxing 2019.10.28
            $api->get('System/targetList', 'SystemController@targetList');
            //系统设置-销售模块-新增目标 create by zongxing 2019.10.28
            $api->post('System/addTarget', 'SystemController@addTarget');
            //系统设置-销售模块-编辑目标 create by zongxing 2019.10.28
            $api->post('System/editTarget', 'SystemController@editTarget');
            //系统设置-销售模块-获取客户列表 create by zongxing 2019.11.19
            $api->get('System/getSaleUserList', 'SystemController@getSaleUserList');


            //用户模块-添加用户 create by zongxing 2019.11.28
            $api->post('User/addUser', 'UserController@addUser');
            //用户模块-获取用户分类信息 create by zongxing 2019.11.29
            $api->get('User/getUserClassify', 'UserController@getUserClassify');
            //用户模块-获取用户列表 create by zongxing 2018.07.27
            $api->get('User/userList', 'UserController@userList');
            //用户模块-编辑用户 create by zongxing 2019.12.04
            $api->post('User/editUser', 'UserController@editUser');

            //用户模块-获取可见字段信息 create by zongxing 2019.11.29
            $api->get('User/getClassifyField', 'UserController@getClassifyField');
            //用户模块-获取仓位信息 create by zongxing 2019.12.05
            $api->get('User/getClassifyShop', 'UserController@getClassifyShop');
            //用户模块-获取用户分类列表 create by zongxing 2019.12.04
            $api->get('User/getUserClassifyList', 'UserController@getUserClassifyList');
            //用户模块-添加用户分类 create by zongxing 2019.12.04
            $api->post('User/addClassify', 'UserController@addClassify');
            //用户模块-编辑用户分类 create by zongxing 2019.12.04
            $api->post('User/editClassify', 'UserController@editClassify');

            //商品模块-乐天商品列表 add by zongxing on the 2020.02.21
            $api->get('Goods/ltGoodsList', 'GoodsController@ltGoodsList');
            //商品模块-乐天商品列表-获取商品美金原价波动数据 add by zongxing on the 2020.02.22
            $api->get('Goods/ltGoodsSpecPriceInfo', 'GoodsController@ltGoodsSpecPriceInfo');
            //商品模块-通过商家编码从ERP货品档案更新商品重量-单个更新
            $api->post('goods/updateWeightByErp', 'GoodsController@updateWeightByErp');
            //商品模块-通过商家编码批量更新商品预估重量 zhangdong 2020.03.13
            $api->post('goods/importEstimateWeight', 'GoodsController@importEstimateWeight');

            //权限模块-编辑管理员信息 create by zongxing 2018.07.27
            $api->post('permission/eidtAdminUser', 'AdminUserController@eidtAdminUser');

        });

    });


    //权限管理路由 create by zongxing 2018.07.27
    $api->group(['namespace' => 'App\Api\Vone\Controllers', 'middleware' => ['admin.user']], function ($api) {
        $api->group(['middleware' => 'jwt.auth',], function ($api) {
            //权限模块-获取权限 create by zongxing 2018.07.28
            $api->get('permission/getPermission', 'PermissionController@getPermission');
        });
        $api->group(['middleware' => 'jwt_and_permission',], function ($api) {//jwt_and_permission//
            //权限模块-添加管理员 create by zongxing 2018.07.27
            $api->post('permission/addAdminUser', 'AdminUserController@addAdminUser');

            //权限模块-删除管理员 create by zongxing 2018.07.27---stop
            //$api->get('permission/delAdminUser', 'AdminUserController@delAdminUser');
            //权限模块-获取管理员列表 create by zongxing 2018.07.27
            $api->get('AdminUser/adminUserList', 'AdminUserController@adminUserList');

            //权限模块-添加角色 create by zongxing 2018.07.27
            $api->post('permission/addRole', 'RoleController@addRole');
            //权限模块-获取角色列表 create by zongxing 2018.07.27
            $api->get('Role/roleList', 'RoleController@roleList');
            //权限模块-编辑角色 create by zongxing 2018.07.28
            $api->get('permission/editRole', 'RoleController@editRole');
            //权限模块-删除角色 create by zongxing 2018.07.28---stop
            //$api->get('permission/delRole', 'RoleController@delRole');

            //权限模块-添加权限 create by zongxing 2018.07.28
            $api->post('permission/addPermission', 'PermissionController@addPermission');
            //权限模块-获取权限列表 create by zongxing 2018.07.31
            $api->get('permission/permissiomList', 'PermissionController@permissiomList');




        });
    });


});

//客戶端接口
$api->version('v2', function ($api) {
    $api->group(['namespace' => 'App\Api\Client\Controllers', 'middleware' => ['user']], function ($api) {
        $api->post('user/h5Login', 'UserController@AuthLogin'); //h5登录授权
        $api->group(['middleware' => 'jwt.auth'], function ($api) {
            //获取指定商品最终折扣 create by zongxing 2019.11.26
            $api->post('Goods/getGoodsFinalDiscount', 'GoodsController@getGoodsFinalDiscount');

            //商品模块-乐天商品列表-获取商品美金原价波动数据 add by zongxing on the 2020.02.22
            $api->get('Goods/ltGoodsSpecPriceInfo', 'GoodsController@ltGoodsSpecPriceInfo');

        });

    });
});

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
