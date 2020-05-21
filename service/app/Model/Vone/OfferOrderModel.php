<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OfferOrderModel extends Model
{
    public $table = 'offer_order as oo';
    private $field = [
        'oo.offer_id','oo.offer_sn','oo.status','oo.remark','oo.create_time',
    ];
    private $status_desc = [
        '1' => '待报价',
        '2' => '已报价',
    ];

    /**
     * description 组装报价单数据
     * author zhangdong
     * date 2019.11.25
     */
    public function createOrderData($goodsInfo)
    {
        //报价单商品信息
        $offer_goods = [];
        //生成报价单号
        $offer_sn = $this->makeOfferSn();
        foreach ($goodsInfo as $key => $value) {
            $spec_sn = trim($value->spec_sn);
            $offer_goods[] = [
                'offer_sn' => $offer_sn,
                'goods_name' => trim($value->goods_name),
                'spec_sn' => $spec_sn,
                'erp_merchant_no' => trim($value->erp_merchant_no),
                'spec_price' => trim($value->spec_price),
                'exw_discount' => floatval($value->exw_discount),
                'sale_discount' => floatval($value->sale_discount),
                'platform_barcode' => trim($value->platform_barcode),
            ];

        }
        $offer_order = [
            'offer_sn' => $offer_sn,
        ];
        $offerOrderData = [
            'offerOrder' => $offer_order,
            'offerGoods' => $offer_goods,
        ];
        return $offerOrderData;
    }//end of function

    /**
     * description 写入报价数据
     * author zhangdong
     * date 2019.11.25
     */
    public function saveOfferData($offerData)
    {
        $offerOrder = $offerData['offerOrder'];
        $offerGoods = $offerData['offerGoods'];
        if (count($offerOrder) == 0 || count($offerGoods) == 0 ) {
            return false;
        }
        //由于决定使用MyISAM引擎，故此处无法加事务
        //报价订单数据保存
        DB::table('offer_order')->insert($offerOrder);
        //报价商品数据保存
        $insertRes =  DB::table('offer_goods')->insert($offerGoods);
        return $insertRes;
    }

    /**
     * description 根据报价单号获取报价单基本信息
     * author zhangdong
     * date 2019.11.25
     */
    public function getOrderInfo($offerSn)
    {
        $where = [
            ['offer_sn', $offerSn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)
            ->where($where)->get();
        foreach ($queryRes as $key => $value) {
            $queryRes[$key]->status_desc = $this->status_desc[intval($value->status)];
        }
        return $queryRes;

    }

    /**
     * description 报价单列表
     * author zhangdong
     * date 2019.11.26
     */
    public function getOfferList($reqParams, $pageSize)
    {
        //组装查询条件
        $where = $this->makeWhere($reqParams);
        $queryRes = DB::table($this->table)->select($this->field)
            ->leftJoin('offer_goods as og','og.offer_sn','oo.offer_sn')
            ->where($where)->groupBy('oo.offer_sn')->orderBy('oo.create_time','desc')
            ->paginate($pageSize);
        //如果查询没有结果则直接返回
        if ($queryRes->count() == 0) {
            return $queryRes;
        }
        //查询对应报价单的商品信息
        $arrOfferSn = getFieldArrayVaule(objectToArray($queryRes), 'offer_sn');
        $ogModel = new OfferGoodsModel();
        $offerGoods = objectToArray($ogModel->offerGoods($arrOfferSn, $where));
        foreach ($queryRes as $key => $value) {
            //根据mis_order_sn查询商品信息
            $offer_sn = trim($value->offer_sn);
            //系统订单状态
            $queryRes[$key]->status_desc = $this->status_desc[intval($value->status)];
            //获取商品信息
            $goodsData = searchArray($offerGoods, $offer_sn, 'offer_sn');
            $offerGoods = $goodsData['arrData'];
            $queryRes[$key]->goods_data = $goodsData['searchRes'];
        }
        return $queryRes;
    }

















    //--------------------------------------------(TODO)私有函数区--------------------------------------------

    /**
     * description 生成报价单号
     * author zhangdong
     * date 2019.11.25
     */
    private function makeOfferSn()
    {
        do {
            $strNum = date('Ymdhi', time()) . rand(1000, 9999);
            $offer_sn = $strNum;
            //联合采购单号查找当前这个需求单号是否已经存在
            $count = DB::table('offer_order')
                ->where([
                    ['offer_sn', '=', $offer_sn]
                ])->count();
        } while ($count);
        return $offer_sn;
    }

    /**
     * description 查询报价单-组装查询条件
     * author zhangdong
     * date 2019.11.26
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
            ['oo.create_time', '>=', $start_time],
            ['oo.create_time', '<=', $end_time],
        ];
        //报价单编号
        if (isset($reqParams['offer_sn'])) {
            $where[] = [
                'oo.offer_sn', trim($reqParams['offer_sn'])
            ];
        }
        //报价单状态 1,未报价  2,已报价
        if (isset($reqParams['status'])) {
            $where[] = [
                'oo.status', trim($reqParams['status'])
            ];
        }
        //商品名称
        if (isset($reqParams['goods_name'])) {
            $where[] = [
                'og.goods_name', 'like', '%' . trim($reqParams['goods_name'] . '%')
            ];
        }
        //商家编码
        if (isset($reqParams['erp_merchant_no'])) {
            $where[] = [
                'og.erp_merchant_no', trim($reqParams['erp_merchant_no'])
            ];
        }
        //规格码
        if (isset($reqParams['spec_sn'])) {
            $where[] = [
                'og.spec_sn', trim($reqParams['spec_sn'])
            ];
        }
        //平台条码
        if (isset($reqParams['platform_barcode'])) {
            $where[] = [
                'og.platform_barcode', trim($reqParams['platform_barcode'])
            ];
        }
        return $where;
    }

    /**
     * description 根据报价单号统计条数-一般用作报价单校验
     * author zhangdong
     * date 2019.11.27
     */
    public function countNum($offerSn)
    {
        $where = [
            ['offer_sn', $offerSn],
        ];
        $queryRes = DB::table($this->table)->where($where)->count();
        return $queryRes;
    }



}//end of class
