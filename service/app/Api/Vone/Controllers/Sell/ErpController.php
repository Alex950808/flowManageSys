<?php
namespace App\Api\Vone\Controllers\Sell;

use App\Api\Vone\Controllers\BaseController;
use App\Model\Vone\ShopStockModel;
use App\Modules\Erp\ErpApi;

//create by zhangdong on the 2018.11.14
//本文件从2019.12.02之后请勿在此添加关于定时任务的函数，定时任务函数请加于planModel中 zhangdong 2019.12.02
class ErpController extends BaseController
{
    /**
     * description:erp-将商品推送到erp平台货品
     * editor:zhangdong
     * date : 2018.11.14
     */
    public function erpPlatformGoodsPush()
    {
        $erpModel = new ErpApi();
        $returnMsg = $erpModel->createPlatformGoods();
        return response() -> json($returnMsg);
    }

    /**
     * description:erp-库存同步
     * editor:zhangdong
     * date : 2018.12.03
     */
    public function sycGoodsStock()
    {
        $erpModel = new ErpApi();
        $returnMsg = $erpModel->sycGoodsStock();
        return response() -> json($returnMsg);
    }

    /**
     * description erp-查询货品档案-通过货品档案修改MIS的商品重量
     * author zhangdong
     * date 2020.02.13
     */
    public function goodsQuery()
    {
        $erpModel = new ErpApi();
        $returnMsg = $erpModel->getErpGoods();
        return response() -> json($returnMsg);
    }


    








}//end of class