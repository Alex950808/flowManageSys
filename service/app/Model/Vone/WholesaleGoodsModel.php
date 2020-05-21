<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WholesaleGoodsModel extends Model
{
    public $table = 'wholesale_goods as wg';
    private $field = [
        'wg.id','wg.wholesale_sn','wg.goods_name','wg.spec_sn','wg.trans_type','wg.refer_disc',
        'wg.platform_no','wg.spec_weight','wg.spec_price','wg.create_time',
    ];

    /**
     * @description 报价单列表-获取商品信息
     * @author zhangdong
     * date 2019.11.26
     */
    public function wholesaleGoods(array $arrWholesaleSn = [], $goodsWhere = [])
    {
        $where = [];
        //对查询条件进行处理-如果发现有本函数适用的条件则加入筛选
        foreach($goodsWhere as $value){
            $field_name = $value[0];
            $is_exit = strpos($field_name,'wg');
            /*此处strpos函数如果查到对应的字符则返回出现的位置，因为wg出现的位置为0
            而0不等于true，所以用false来判断*/
            if($is_exit !== false) {
                $where[] = $value;
            }
        }
        $queryRes = DB::table($this->table)->select($this->field)
            ->whereIn('wholesale_sn',$arrWholesaleSn)->where($where)
            ->get();
        return $queryRes;
    }

    /**
     * @description 通过报价单号获取商品信息
     * @author zhangdong
     * @date 2020.03.25
     */
    public function getGoodsBySn($wholesaleSn)
    {
        $where = [
            ['wholesale_sn', $wholesaleSn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->get();
        return $queryRes;
    }


    /**
     * desc 通过报价单号查询订单下是否有商品
     * author zhangdong
     * date 2020.03.27
     */
    public function countNum($wholesaleSn)
    {
        $where = [
            ['wholesale_sn', $wholesaleSn],
        ];
        $countNum = DB::table($this->table)->where($where)->count();
        return $countNum;
    }

    /**
     * desc 组装最终报价数据
     * author zhangdong
     * date 2020.03.27
     */
    public function madeOfferData($wholesaleSn, $inputNum, $discount, $discPlate)
    {
        $wdModel = new WholesaleDiscountModel();
        $order = (new WholesaleOrderModel())->getOrderBySn($wholesaleSn);
        //此处订单信息在计算折扣信息时要用到，相关函数 $wdModel->calculDisc()
        $wdModel->orderMsg = $order;
        $wdModel->inputNum = $inputNum;
        $goods = $this->getGoodsBySn($wholesaleSn);
        $freight = floatval($order->freight);
        $arrData = objectToArray($discount);
        foreach ($goods as $key => $v) {
            unset($goods[$key]->wholesale_sn,$goods[$key]->create_time,$goods[$key]->id);
            $specSn = $v->spec_sn;
            //SKU运费 = 标准运费*商品重量（标准运费 3美元/千克）
            $specWeight = floatval($v->spec_weight);
            $goods[$key]->skuFreight = round($freight * $specWeight, 2);
            //查找对应SKU的渠道基础折扣
            $searchRes = searchArray($arrData, $specSn, 'spec_sn');
            $arrData = $searchRes['arrData'];
            $searchDis = $searchRes['searchRes'];
            //此处SKU信息在计算折扣信息时要用到，相关函数 $wdModel->calculDisc()
            $wdModel->skuMsg = $v;
            //根据所选折扣板块处理商品折扣数据
            $goodsDis = $wdModel->opeDiscByPlate($searchDis, $discPlate);
            $goods[$key]->discInfo = $goodsDis;
        }
        return [
            'order' => $order,
            'goods' => $goods,
        ];
    }

    /**
     * desc 检查所选折扣模板是否存在
     * author zhangdong
     * date 2020.03.27
     */
    private function checkDiscountPlate(array $arrDiscountPlate = [])
    {
        if (count($arrDiscountPlate) == 0) {
            return false;
        }
        $intDiscountPlate = array_keys((new WholesaleDiscountModel())->descDiscPlate);
        $intersectNum = array_intersect($intDiscountPlate,$arrDiscountPlate);
        if (count($intersectNum) != count($arrDiscountPlate)) {
            return false;
        }
        return $intersectNum;
    }

    /**
     * desc 导出报价单传参检查
     * author zhangdong
     * date 2020.03.27
     */
    public function checkParams($arrDisPlate, $inputNum, $wholesaleSn, $arrChannelId)
    {
        //检查所选折扣模板是否存在
        $checkPlate = $this->checkDiscountPlate($arrDisPlate);
        if ($checkPlate === false) {
            return ['code' => '2067', 'msg' => '所选折扣模板有误'];
        }
        $wdModel = new WholesaleDiscountModel();
        //如果选择了手动输入项，则要检查输入值
        $otherDisNum = $wdModel->intDiscPlate['other'];
        if (in_array($otherDisNum, $arrDisPlate) && $inputNum <= 0 ) {
            return ['code' => '2067', 'msg' => '请输入其他折扣追加点'];
        }
        //检查单号是否存在
        $woModel = new WholesaleOrderModel();
        $orderNum = $woModel->countNum($wholesaleSn);
        if ($orderNum == 0) {
            return ['code' => '2067', 'msg' => '报价单不存在！'];
        }
        //检查单号下是否存在商品
        $goodsNum = $this->countNum($wholesaleSn);
        if ($goodsNum == 0) {
            return ['code' => '2067', 'msg' => '该报价单下商品为空'];
        }
        //根据单号查询组装商品折扣信息
        $discount = $wdModel->getGoodsDiscount($wholesaleSn, $arrChannelId);
        if (count($discount) == 0) {
            return ['code' => '2067', 'msg' => '所选渠道没有折扣信息'];
        }
        //检查当前报价单下是否存在相关渠道
        $arrData = objectToArray($discount);
        foreach ($arrChannelId as $channelId) {
            $searchRes = searchArray($arrData, $channelId, 'channel_id');
            if (count($searchRes['searchRes']) == 0) {
                return ['code' => '2067', 'msg' => '所选部分渠道在当前报价单下不存在折扣信息'];
            }
            $arrData = $searchRes['arrData'];
        }
        return [
            'discount' => $discount,
            'disPlate' => $checkPlate,
        ];
    }//end function

    /**
     * desc 通过订单号统计每个订单号下的商品数量
     * author zhangdong
     * date 2020.04.06
     */
    public function countGoodsBySn($arrWholeSn)
    {
        $fields = ['wholesale_sn', DB::raw('count(*) as num')];
        $queryRes = DB::table($this->table)->select($fields)->whereIn('wholesale_sn', $arrWholeSn)
            ->groupBy('wholesale_sn')->get();
        return $queryRes;
    }

    /**
     * desc 处理web页显示数据-将没有折扣的数据补0
     * author zhangdong
     * date 2020.04.13
     */
    public function makeViewData($goods, $channel, $disPlateNum)
    {
        $wdModel = new WholesaleDiscountModel();
        $strDiscPlate = $wdModel->descDiscPlate;
        foreach ($goods as $key => $value) {
            $discInfo = $value->discInfo;
            if (count($discInfo) == 0) {
                continue;
            }
            foreach ($disPlateNum as $dpn) {
                foreach ($channel as $chaInfo) {
                    $channelId = $chaInfo->channel_id;
                    if (!isset($discInfo[$strDiscPlate[$dpn]][$channelId])) {
                        $goods[$key]->discInfo[$strDiscPlate[$dpn]][$channelId] = [
                            'channel_id' => $channelId,
                            'discount' => 0,
                            'lastDisc' => 0,
                            'dollarPrice' => 0,
                            'rmbPrice' => 0,
                        ];
                    }
                }//end foreach
            }//end foreach
        }//end foreach
        return $goods;
    }

    /**
     * desc 大批发报价-导出-传参检查并校验数据
     * author zhangdong
     * date 2020.03.27
     */
    public function checkWholesale($wholesaleSn)
    {
        $wdModel = new WholesaleDiscountModel();
        //检查单号是否存在
        $woModel = new WholesaleOrderModel();
        $orderNum = $woModel->countNum($wholesaleSn);
        if ($orderNum == 0) {
            return ['code' => '2067', 'msg' => '报价单不存在！'];
        }
        //检查单号下是否存在商品
        $goodsNum = $this->countNum($wholesaleSn);
        if ($goodsNum == 0) {
            return ['code' => '2067', 'msg' => '该报价单下商品为空'];
        }
        //根据单号查询组装商品折扣信息
        $discount = $wdModel->getGoodsDiscount($wholesaleSn);
        if (count($discount) == 0) {
            return ['code' => '2067', 'msg' => '该报价单没有折扣信息'];
        }
        return [
            'discount' => $discount,
        ];
    }//end function

    /**
     * desc 组装最终大批发报价数据-新版
     * author zhangdong
     * date 2020.05.12
     */
    public function madeWholesaleData($wholesaleSn, $discount)
    {
        $order = (new WholesaleOrderModel())->getOrderBySn($wholesaleSn);
        $saleUserInfo = (new SaleUserModel())->getSaleUserInfoInRedis();
        //获取销售用户名称
        $saleUid = intval($order->sale_user_id);
        $searchRes = searchTwoArray($saleUserInfo, $saleUid, 'id');
        $saleUser = isset($searchRes[0]['user_name']) ? trim($searchRes[0]['user_name']) : '';
        $order->sale_user = $saleUser;
        $wdModel = new WholesaleDiscountModel();
        $goods = $this->getGoodsBySn($wholesaleSn);
        $order->goodsCount = count($goods);
        //海运运费
        $sea_trans = floatval($order->sea_trans);
        //空运运费
        $air_trans = floatval($order->air_trans);
        $arrData = objectToArray($discount);
        //获取渠道信息
        $arrChannelId = array_unique(array_column($arrData, 'channel_id'));
        foreach ($goods as $key => $v) {
            unset($goods[$key]->wholesale_sn,$goods[$key]->create_time,$goods[$key]->id);
            $specSn = $v->spec_sn;
            //SKU运费 = 运输方式对应的运费*商品重量
            $specWeight = floatval($v->spec_weight);
            $trans_type = floatval($v->trans_type);
            $skuFreight = 0;
            //运输方式 1 海运 2 空运
            $transCn = '';
            if ($trans_type == 1) {
                $skuFreight = round($sea_trans * $specWeight, 2);
                $transCn = '海运';
            }
            if ($trans_type == 2) {
                $skuFreight = round($air_trans * $specWeight, 2);
                $transCn = '空运';
            }
            $goods[$key]->skuFreight = $skuFreight;
            $goods[$key]->transCn = $transCn;
            //查找对应SKU的渠道基础折扣
            $searchRes = searchArray($arrData, $specSn, 'spec_sn');
            $arrData = $searchRes['arrData'];
            $searchDis = $searchRes['searchRes'];
            //处理SKU渠道折扣信息
            $goodsDis = $wdModel->operateDisc($searchDis, $arrChannelId, $order, $goods[$key]);
            $goods[$key]->discInfo = $goodsDis;
        }//end foreach
        //查询成本折扣数据标题头
        $costTitle = $wdModel->queryCostTitle($wholesaleSn);
        //将标题头美化
        $beautCostTitle = $wdModel->beautCostTitle($costTitle);
        //当前单号下的渠道
        $channel = (new WholesaleDiscountModel())->getChannelBySn($wholesaleSn);
        $chaName = array_column(objectToArray($channel), 'channels_name');
        return [
            'chaName' => $chaName,
            'order' => $order,
            'costTitle' => $costTitle,
            'beautCostTitle' => $beautCostTitle,
            'goods' => $goods,
        ];
    }


    /**
     * desc 大批发报价-修改运费和参考折扣
     * author zhangdong
     * $reqParams 包含的参数 wholesale_sn,spec_sn,type,modifyValue
     * date 2020.05.15
     */
    public function updateGoodsInfo($reqParams)
    {
        $wholesaleSn = trim($reqParams['wholesale_sn']);
        $specSn = trim($reqParams['spec_sn']);
        $type = intval($reqParams['type']);
        //1 修改运输方式 2 修改参考折扣
        if ($type == 1) {
            $modifyValue = intval($reqParams['modifyValue']);
            $update = [
                'trans_type' => $modifyValue
            ];
        } else {
            $modifyValue = floatval($reqParams['modifyValue']);
            $update = [
                'refer_disc' => $modifyValue
            ];
        }
        $where = [
            ['wholesale_sn', $wholesaleSn],
            ['spec_sn', $specSn],
        ];
        $updateRes = DB::table($this->table)->where($where)->update($update);
        return $updateRes;
    }

    /**
     * desc 检查对应订单下是否存在某个商品
     * author zhangdong
     * date 2020.05.15
     */
    public function getGoods($wholesaleSn, $specSn)
    {
        $where = [
            ['wholesale_sn', $wholesaleSn],
            ['spec_sn', $specSn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->get();
        return $queryRes;
    }

    /**
     * desc 校验报价信息上传数据
     * author zhangdong
     * date 2020.05.15
     */
    public function checkOfferData($wholesaleSn, $goodsData)
    {
        //查询商品信息
        $goods = $this->getGoodsBySn($wholesaleSn);
        $arrData = objectToArray($goods);
        //循环检查导入的数据中各商品是否存在于对应订单中
        $none_id = $updateGoodsData = [];
        foreach($goodsData as $key => $value){
            if ($key == 0) {
                continue;
            }
            $spec_sn = trim($value[1]);
            $searchRes = searchArray($arrData, $spec_sn, 'spec_sn');
            $arrData = $searchRes['arrData'];
            $searchGoods = $searchRes['searchRes'];
            if (count($searchGoods) == 0) {
                $none_id[] = $key + 1;
                continue;
            }
            //对修改数量做对比，筛选出被修改的数据
            $oldReferDisc = floatval($searchGoods[0]['refer_disc']);
            $oldTransType = intval($searchGoods[0]['trans_type']);
            $newTransType = intval($value[2]);
            $newReferDisc = floatval($value[3]);
            if ($oldTransType != $newTransType || $oldReferDisc != $newReferDisc) {
                $updateGoodsData[$key] = [
                    'spec_sn' => $spec_sn,
                    'refer_disc' => $newReferDisc,
                    'trans_type' => $newTransType,
                ];
            }
        }
        //将不存在的商品告知用户
        if(count($none_id) > 0){
            $checkRes = '第' . implode($none_id, ',') . '行的商品信息订单中不存在';
            return ['code' => '2067','msg' => $checkRes];
        }
        return $updateGoodsData;
    }

    /**
     * description:根据总单号查询总单商品信息
     * autho:zhangdong
     * date:2019.04.25
     */
    public function queryMisOrderGoods($misOrderSn)
    {
        $where = [
            ['mis_order_sn', $misOrderSn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->get();
        return $queryRes;

    }

    /**
     * desc 批量更新运输方式和参考折扣
     * author zhangdong
     * date 2020.05.15
     */
    public function updateWholeData($wholesaleSn, $correctData)
    {
        $andWhere = [
            'wholesale_sn' => $wholesaleSn,
        ];
        $this->table = 'jms_wholesale_goods';
        $arrSql = makeUpdateSql($this->table, $correctData, $andWhere);
        $updateRes = false;
        if ($arrSql) {
            //开始批量更新
            $strSql = $arrSql['updateSql'];
            $bindData = $arrSql['bindings'];
            $updateRes = DB::update($strSql, $bindData);
        }
        return $updateRes;
    }



}//end of class
