<?php
namespace App\Modules\Erp;

use App\Model\Vone\GoodsSpecModel;
//引入日志库文件 add by zhangdong on the 2018.06.28
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
//ERP数据组装工厂 zhangdong 2019.11.29
class ErpFactory
{
    /**
     * title ERP店铺库存同步-组装店铺库存信息-专用
     * author zhangdong
     * date 2019.11.29
     */
    protected function makeShopStockInfo($data)
    {
        $shopStockInfo = [];
        foreach ($data as $k => $v) {
            //获取商家编码
            $erp_merchant_no = '';
            //注：如果$v['spec_no']为空（$v['goods_no']不为空）则说明该商品是单sku，
            //如果$v['spec_no']不为空则说明该商品是多sku
            if (!empty($v['spec_no'])) {
                $erp_merchant_no = $v['spec_no'];
            }
            if (empty($v['spec_no']) && !empty($v['goods_no'])) {
                $erp_merchant_no = $v['goods_no'];
            }
            if (empty($erp_merchant_no)) {
                continue;
            }
            $shopStockInfo[] = [
                'rec_id' => $v['rec_id'],//Erp内平台货品表主键id
                'sync_stock' => intval($v['sync_stock']),//Erp内库存
                'erp_merchant_no' => $erp_merchant_no,
                'stock_change_count' => $v['stock_change_count'],
                'spec_id' => $v['spec_id'],
            ];

        }
        return $shopStockInfo;
    }

    /**
     * title 根据商家编码更新商品重量
     * author zhangdong
     * date 2020.02.24
     */
    protected function updateGoodsWeight($arrGoodsInfo)
    {
        $log = logInfo('cron/erp/goodsWeight');
        $gsModel = new GoodsSpecModel();
        $log->addInfo('ERP商品重量更新开始');
        foreach ($arrGoodsInfo as $key => $value) {
            $spec_list = $value['spec_list'][0];
            $erpMerchantNo = trim($spec_list['barcode']);
            $erpWeight = trim($spec_list['weight']);
            $log->addInfo('商家编码-' . $erpMerchantNo . '-ERP重量-' . $erpWeight);
            //查询商品的重量
            $goodsInfo = $gsModel->getSkuInfoByErpNo($erpMerchantNo);
            if (is_null($goodsInfo)) {
                $log->addInfo('该商品在MIS系统中不存在');
                continue;
            }
            $misWeight = trim($goodsInfo->spec_weight);
            $specSn = trim($goodsInfo->spec_sn);
            $log->addInfo('MIS系统重量-' . $misWeight . '-MIS规格码-' . $specSn);
            if ($erpWeight == $misWeight) {
                $log->addInfo('重量相等，无需更新');
                continue;
            }
            //根据规格码更新商品重量
            $updateRes = $gsModel->updateWeight($specSn, $erpWeight);
            $updateMsg = $updateRes ? '重量更新成功' : '重量更新失败';
            $log->addInfo($updateMsg);
        }
        $log->addInfo('ERP商品重量更新结束');



    }





}//end of class
