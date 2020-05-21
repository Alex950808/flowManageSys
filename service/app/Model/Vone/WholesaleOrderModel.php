<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WholesaleOrderModel extends Model
{
    public $table = 'wholesale_order as wo';
    private $field = [
        'wo.id','wo.wholesale_sn','wo.admin_id','wo.sea_trans',
        'wo.usd_cny_rate','wo.estimate_time','wo.air_trans',
        'wo.create_time','wo.sale_user_id','wo.user_disc',
    ];

    //运输方式 0 无 1 海运 2 空运
    private $transport_type = [
        '0' => '无',
        '1' => '海运',
        '2' => '空运',
    ];


    /**
     * desc 组装大批发报价单数据
     * author zhangdong
     * date 2020.03.24
     */
    public function createOrderData($goodsInfo, $userDiscInfo, $reqParams)
    {
        //商品信息
        $goods = $discountParams = [];
        //生成大批发报价单号
        $wholesale_sn = $this->makeSn();
        foreach ($goodsInfo as $key => $value) {
            $spec_sn = trim($value['spec_sn']);
            $goods[] = [
                'wholesale_sn' => $wholesale_sn,
                'goods_name' => trim($value['goods_name']),
                'spec_sn' => $spec_sn,
                'platform_no' => trim($value['platform_no']),
                'spec_weight' => trim($value['spec_weight']),
                'spec_price' => trim($value['spec_price']),
            ];
            $discountParams[] = [
                'spec_sn' => $spec_sn,
                'brand_id' => $value['brand_id'],
            ];
        }
        //组装渠道基础折扣信息
        $param['buy_time'] = date('Y-m-d');
        //$param['buy_time'] = '2020-04-30';
        $goodsModel = new goodsModel();
        $discount = $goodsModel->getGoodsFinalDiscount($discountParams, $param);
        if (isset($discount['code'])) {
            return $discount;
        }
        if (count($discount) == 0) {
            return ['code'=>'2067', 'msg'=>'折扣信息缺失请维护'];
        }
        $wdModel = new WholesaleDiscountModel();
        $orderDiscount = $wdModel->makeSaveData($wholesale_sn, $discount['goodsList']);
        if (count($orderDiscount) == 0) {
            return ['code'=>'2067', 'msg'=>'当前数据没有折扣信息，请维护'];
        }
        //运费
        $seaTrans = isset($reqParams['sea_trans']) ? floatval($reqParams['sea_trans']) : 1;
        $airTrans = isset($reqParams['air_trans']) ? floatval($reqParams['air_trans']) : 3;
        if (isset($reqParams['usd_cny_rate']) && $reqParams['usd_cny_rate'] > 0) {
            $usd_cny_rate = floatval($reqParams['usd_cny_rate']);
        } else {
            $usd_cny_rate = (new ExchangeRateModel())->getUsdCnyRate();
        }
        $estimate_time = isset($reqParams['predict_pot_time']) ?
            trim($reqParams['predict_pot_time']) : '';
        $admin_id = $reqParams['admin_id'];
        $order = [
            'wholesale_sn' => $wholesale_sn,
            'sea_trans' => $seaTrans,
            'usd_cny_rate' => $usd_cny_rate,
            'air_trans' => $airTrans,
            'estimate_time' => $estimate_time,
            'admin_id' => $admin_id,
            'sale_user_id' => $userDiscInfo->sale_user_id,
            'user_disc' => $userDiscInfo->discount,
        ];
        $orderData = [
            'order' => $order,
            'goods' => $goods,
            'discount' => $orderDiscount,
        ];
        return $orderData;
    }//end of function


    /**
     * desc 生成单号
     * author zhangdong
     * date 2020.03.24
     */
    private function makeSn()
    {
        do {
            $strNum = date('Ymdhi', time()) . rand(1000, 9999);
            $wholesale_sn = $strNum;
            //联合采购单号查找当前这个需求单号是否已经存在
            $count = DB::table($this->table)
                ->where([
                    ['wholesale_sn', '=', $wholesale_sn]
                ])->count();
        } while ($count);
        return $wholesale_sn;
    }
    /**
     * desc 写入数据
     * author zhangdong
     * date 2020.03.24
     */
    public function saveData($orderData)
    {
        $order = $orderData['order'];
        $goods = $orderData['goods'];
        $discount = $orderData['discount'];
        if (count($order) == 0 || count($goods) == 0 ) {
            return false;
        }
        DB::table('wholesale_order')->insert($order);
        DB::table('wholesale_discount')->insert($discount);
        //报价商品数据保存
        $insertRes =  DB::table('wholesale_goods')->insert($goods);
        return $insertRes;
    }

    /**
     * desc 获取列表数据
     * author zhangdong
     * date 2020.03.24
     */
    public function getList($reqParams, $pageSize)
    {
        //组装查询条件
        $where = $this->makeWhere($reqParams);
        $queryRes = DB::table($this->table)->select($this->field)
            ->leftJoin('wholesale_goods as wg','wg.wholesale_sn','wo.wholesale_sn')
            ->where($where)->groupBy('wo.wholesale_sn')->orderBy('wo.create_time','desc')
            ->paginate($pageSize);
        //如果查询没有结果则直接返回
        if ($queryRes->count() == 0) {
            return $queryRes;
        }
        $auModel = new AdminUserModel();
        $adminInfo = $auModel->getAdminInfoInRedis();
        //查询对应报价单的商品信息
        $arrWholesaleSn = getFieldArrayVaule(objectToArray($queryRes), 'wholesale_sn');
        $wgModel = new WholesaleGoodsModel();
        $arrCountGoods = objectToArray($wgModel->countGoodsBySn($arrWholesaleSn));
        $wholesaleGoods = objectToArray($wgModel->wholesaleGoods($arrWholesaleSn, $where));
        $saleUserInfo = (new SaleUserModel())->getSaleUserInfoInRedis();
        foreach ($queryRes as $key => $value) {
            //获取一个单号下有几个商品
            $wholesale_sn = trim($value->wholesale_sn);
            $searchCount = searchArrayGetOne($arrCountGoods, $wholesale_sn, 'wholesale_sn');
            $queryRes[$key]->goodsCount = $searchCount['searchRes']['num'];
            $adminId = intval($value->admin_id);
            $searchRes = searchArray($adminInfo, $adminId, 'id');
            $queryRes[$key]->adminName = $searchRes['searchRes'][0]['nickname'];
            //获取销售用户名称
            $saleUid = intval($value->sale_user_id);
            $searchRes = searchTwoArray($saleUserInfo, $saleUid, 'id');
            $saleUser = isset($searchRes[0]['user_name']) ? trim($searchRes[0]['user_name']) : '';
            $queryRes[$key]->sale_user = $saleUser;
            //根据 wholesale_sn 查询商品信息
            //获取商品信息
            $goodsData = searchArray($wholesaleGoods, $wholesale_sn, 'wholesale_sn');
            $wholesaleGoods = $goodsData['arrData'];
            $queryRes[$key]->goods_data = $goodsData['searchRes'];
        }
        return $queryRes;
    }

    /**
     * desc 列表数据-组装查询条件
     * author zhangdong
     * date 2020.03.24
     */
    private function makeWhere($reqParams)
    {
        //时间处理-查询列表时默认只查近三个月的
        //开始时间
        $start_time = Carbon::now()->addMonth(-3)->toDateTimeString();
        if (isset($reqParams['start_time'])) {
            $start_time = trim($reqParams['start_time']);
        }
        //结束时间
        $end_time = Carbon::now()->toDateTimeString();
        if (isset($reqParams['end_time'])) {
            $end_time = trim($reqParams['end_time']);
        }
        //时间筛选
        $where = [
            ['wo.create_time', '>=', $start_time],
            ['wo.create_time', '<=', $end_time],
        ];
        //报价单编号
        if (isset($reqParams['wholesale_sn'])) {
            $where[] = [
                'wo.wholesale_sn', trim($reqParams['wholesale_sn'])
            ];
        }
        //商品名称
        if (isset($reqParams['goods_name'])) {
            $where[] = [
                'wg.goods_name', 'like', '%' . trim($reqParams['goods_name'] . '%')
            ];
        }
        //规格码
        if (isset($reqParams['spec_sn'])) {
            $where[] = [
                'wg.spec_sn', trim($reqParams['spec_sn'])
            ];
        }
        //平台条码
        if (isset($reqParams['platform_no'])) {
            $where[] = [
                'wg.platform_no', 'like', '%' . trim($reqParams['platform_no'] . '%')
            ];
        }
        return $where;
    }


    /**
     * desc 通过报价单号获取订单信息
     * author zhangdong
     * date 2020.03.25
     */
    public function getOrderBySn($wholesaleSn)
    {
        $where = [
            ['wholesale_sn', $wholesaleSn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->first();
        $auModel = new AdminUserModel();
        $adminInfo = $auModel->getAdminInfoInRedis();
        $adminId = intval($queryRes->admin_id);
        $searchRes = searchArray($adminInfo, $adminId, 'id');
        $queryRes->adminName = $searchRes['searchRes'][0]['nickname'];
        return $queryRes;
    }

    /**
     * desc 通过报价单号查询订单是否存在
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
















}//end of class
