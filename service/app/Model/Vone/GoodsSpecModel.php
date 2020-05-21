<?php

namespace App\Model\Vone;

use App\Modules\ParamsSet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Modules\ArrayGroupBy;

use Illuminate\Support\Facades\Redis;


class GoodsSpecModel extends Model
{
    public $table = 'goods_spec as gs';
    private $field = [
        'gs.goods_sn', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.spec_price', 'gs.stock_num',
        'gs.lock_stock_num', 'gs.spec_weight', 'gs.exw_discount',
    ];

    /**
     * description:erp库存同步专用，请勿擅自修改-更新商品库存
     * author:zhangdong
     * date : 2018.12.21
     * @return bool
     */
    public function updateGoodsStock($spec_sn, $stock_num)
    {
        $where[] = ['gs.spec_sn', $spec_sn];
        $update = [
            'gs.stock_num' => $stock_num
        ];
        $updateRes = DB::table($this->table)->where($where)->update($update);
        return $updateRes;
    }

    /**
     * description:erp库存同步专用，请勿擅自修改-查询商品信息
     * author:zhangdong
     * date : 2018.12.21
     * @return object
     */
    public function getSpecInfo($value, $type = 1)
    {
        $field = [
            'goods_sn', 'spec_sn', 'erp_merchant_no', 'stock_num', 'lock_stock_num'
        ];
        if ($type == 1) {
            $where[] = ['spec_sn', $value];
        }
        if ($type == 2) {
            $where[] = ['erp_merchant_no', $value];
        }
        $queryRes = DB::table('goods_spec')->select($field)->where($where)->first();
        return $queryRes;

    }


    /**
     * description:获取商品规格信息
     * editor:zhangdong
     * date : 2018.12.26
     * @param $spec_sn (规格码)
     * @return
     */
    public function getGoodsSpecInfo($spec_sn)
    {
        $spec_sn = trim($spec_sn);
        $where = [
            ['spec_sn', $spec_sn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->first();
        return $queryRes;
    }

    /**
     * description:锁库
     * editor:zhangdong
     * date : 2018.12.26
     * @param $spec_sn (规格码)
     * @param $lockNum (锁库数量)
     * @return
     */
    public function lockGoodsStock($spec_sn, $lockNum)
    {
        $lockNum = abs($lockNum);
        $where = [
            ['gs.spec_sn', $spec_sn]
        ];
        $update = [
            'lock_stock_num' => DB::raw('lock_stock_num + ' . $lockNum),
            'stock_num' => DB::raw('stock_num - ' . $lockNum),
        ];
        $updateRes = DB::table($this->table)->where($where)->update($update);
        return $updateRes;
    }

    /**
     * description:取消现货单后释放锁库库存，将总库存加回去
     * editor:zhangdong
     * date : 2018.12.28
     * @param $spec_sn (规格码)
     * @param $goodsNum (订单商品数量)
     * @return
     */
    public function releaseGoodsStock($spec_sn, $goodsNum)
    {
        $goodsNum = abs($goodsNum);
        $where = [
            ['gs.spec_sn', $spec_sn]
        ];
        $update = [
            'lock_stock_num' => DB::raw('lock_stock_num - ' . $goodsNum),
            'stock_num' => DB::raw('stock_num + ' . $goodsNum),
        ];
        $updateRes = DB::table($this->table)->where($where)->update($update);
        return $updateRes;
    }

    /**
     * description:计算商品的默认销售折扣
     * editor:zhangdong
     * date : 2018.12.27
     * @param $spec_sn (规格码)
     * @return
     */
    public function calculateSaleDiscount($spec_sn, $store_factor)
    {
        //获取商品信息
        $goodsSpecInfo = $this->getGoodsSpecInfo($spec_sn);
        $spec_weight = trim($goodsSpecInfo->spec_weight);
        $spec_price = trim($goodsSpecInfo->spec_price);
        // 重价比 = 重量/美金原价/重价系数/100
        $highPriceRatio = 0;
        if ($spec_price > 0) {
            $highPriceRatio = round($spec_weight / $spec_price / $store_factor / 100, DECIMAL_DIGIT);
        }
        // 重价比折扣 = EXW折扣+重价比
        $exw_discount = trim($goodsSpecInfo->exw_discount);
        $hprDiscount = $exw_discount + $highPriceRatio;
        // 销售折扣 = 重价比折扣/（1-设定档位自采毛利率）-当前为27%
        $sale_discount = round($hprDiscount / (1 - MARGIN_RATE), 4);
        return $sale_discount;

    }

    /**
     * description:DD单上传后对现货商品的库存做处理
     * editor:zhangdong
     * date : 2018.12.29
     * @param $spec_sn (规格码)
     * @param $curDiffer (新旧现货单的购买数量差异值)
     * @return
     */
    public function operateGoodsStock($curDiffer, $spec_sn)
    {
        //如果旧数量和新数量之差小于0，说明该商品的购买数量增加了，此时库存要减少，锁库要增加
        $executeRes = true;
        if ($curDiffer < 0) {
            //锁库操作
            $executeRes = $this->lockGoodsStock($spec_sn, $curDiffer);
        }
        //如果旧数量和新数量之差大于0，说明该商品的购买数量减少了，此时库存要增加，锁库要减少
        if ($curDiffer > 0) {
            //释放库存操作
            $executeRes = $this->releaseGoodsStock($spec_sn, $curDiffer);
        }
        return $executeRes;
    }


    /**
     * description:获取商品信息
     * editor:zongxing
     * date : 2019.03.13
     */
    public function get_goods_info($spec_sn_arr)
    {
        $field = ['g.goods_name', 'gs.spec_sn', 'gs.erp_prd_no', 'gs.erp_ref_no', 'gs.spec_price', 'g.brand_id',
            'gs.spec_weight', 'gs.exw_discount', 'gs.erp_merchant_no', 'g.brand_id'];
        $goods_info = DB::table('goods_spec as gs')
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->whereIn('spec_sn', $spec_sn_arr)->get($field)->groupBy('spec_sn');
        $goods_info = objectToArrayZ($goods_info);
        return $goods_info;
    }

    /**
     * description:组装规格信息
     * author:zhangdong
     * date:2019.04.16
     */
    public function generalSpecInfo($newGoodsInfo, $goods_sn)
    {
        //组装规格基本信息
        $goodsModel = new GoodsModel();
        $spec_sn = $goodsModel->get_spec_sn($goods_sn);
        $erp_merchant_no = isset($newGoodsInfo['erp_merchant_no']) ? trim($newGoodsInfo['erp_merchant_no']) : '';
        $spec_price = isset($newGoodsInfo['spec_price']) ? floatval($newGoodsInfo['spec_price']) : 0;
        $spec_weight = isset($newGoodsInfo['spec_weight']) ? floatval($newGoodsInfo['spec_weight']) : 0;
        //add zhangdong 2019.06.27
        $estimate_weight = isset($newGoodsInfo['estimate_weight']) ? floatval($newGoodsInfo['estimate_weight']) : 0;
        $exw_discount = isset($newGoodsInfo['exw_discount']) ? floatval($newGoodsInfo['exw_discount']) : 0;
        //add zhangdong 2019.06.27
        $erp_ref_no = isset($newGoodsInfo['erp_ref_no']) ? trim($newGoodsInfo['erp_ref_no']) : '';
        //add zhangdong 2019.06.27
        $erp_prd_no = isset($newGoodsInfo['erp_prd_no']) ? trim($newGoodsInfo['erp_prd_no']) : '';
        $specData = [
            'goods_sn' => $goods_sn,
            'spec_sn' => $spec_sn,
            'erp_merchant_no' => $erp_merchant_no,
            'spec_price' => $spec_price,
            'exw_discount' => $exw_discount,
            'estimate_weight' => $estimate_weight,
            'spec_weight' => $spec_weight,
            'erp_ref_no' => $erp_ref_no,
            'erp_prd_no' => $erp_prd_no,
        ];
        return $specData;

    }

    /**
     * description:统计所传数组规格码中有几条数据存在于系统中
     * author:zhangdong
     * date:2019.04.18
     */
    public function countSpecNum(array $arrSpecSn = [])
    {
        $countNum = DB::table($this->table)->whereIn('spec_sn', $arrSpecSn)->count();
        return $countNum;
    }

    /**
     * description:写入规格信息
     * author:zhangdong
     * date:2019.04.22
     */
    public function writeSpecData($specInfo)
    {
        $goods_spec = cutString($this->table, 0, 'as');
        $insertRes = DB::table($goods_spec)->insert($specInfo);
        return $insertRes;
    }

    /**
     * description:通过品牌获取商品规格信息
     * author:zongxing
     * date:2019.06.06
     */
    public function getSpecByBrand($brand_info)
    {
        $field = ['spec_sn', 'g.brand_id'];
        $goods_info = DB::table($this->table)
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->whereIn('g.brand_id', $brand_info)
            ->get($field);
        $goods_info = objectToArrayZ($goods_info);
        return $goods_info;
    }

    /**
     * description:通过规格码查询商品美金原价
     * author:zhangdong
     * date:2019.06.20
     */
    public function getGoodsPrice($arrSpecSn)
    {
        $field = ['spec_sn', 'spec_price'];
        $queryRes = DB::table($this->table)->select($field)
            ->whereIn('spec_sn', $arrSpecSn)->get();
        return $queryRes;
    }

    /**
     * description:通过规格码查询商品美金原价
     * author:zongxing
     * date:2019.06.22
     */
    public function getGoodsPriceBySpec($spec_sn)
    {
        $goods_price_info = DB::table($this->table)->whereIn('spec_sn', $spec_sn)->pluck('spec_price', 'spec_sn');
        $goods_price_info = objectToArrayZ($goods_price_info);
        return $goods_price_info;
    }

    /**
     * description 检查商品规格必填信息
     * author zhangdong
     * date 2019.06.28
     */
    public function checkSpecData($specData)
    {
        $specPrice = floatval($specData['spec_price']);
        $estimateWeight = floatval($specData['estimate_weight']);
        $specWeight = floatval($specData['spec_weight']);
        if ($specPrice <= 0 || ($estimateWeight <= 0 && $specWeight <= 0)) {
            return false;
        }
        return true;
    }

    /**
     * description:获取商品信息-加入回收站筛选，由于原来用的goodsModel->getGoodsInfo()
     * 涉及地方很多，故此处单独拉出来，后续有问题可及时跟踪
     * editor:zhangdong
     * date : 2019.06.29
     */
    public function getGoodsMsg($specSn)
    {
        $where = [
            ['spec_sn', $specSn],
            ['is_trash', 0],
        ];
        $goodsModel = new GoodsModel();
        $field = array_merge($this->field, $goodsModel->field);
        $queryRes = DB::table($this->table)->select($field)
            ->leftJoin($goodsModel->getTable(), 'g.goods_sn', 'gs.goods_sn')
            ->where($where)->first();
        return $queryRes;

    }

    /**
     * description:通过商品货号获取商品规格信息
     * editor:zongxing
     * date : 2018.11.16
     * return Object
     */
    public function getGoodsSpec($param_info)
    {
        $where = [];
        if (isset($param_info['goods_sn'])) {
            $goods_sn = trim($param_info['goods_sn']);
            $where[] = ['goods_sn', $goods_sn];
        }
        if (isset($param_info['spec_id'])) {
            $spec_id = intval($param_info['spec_id']);
            $where[] = ['spec_id', $spec_id];
        }
        $goods_info = DB::table('goods_spec')->where($where)->first();
        $goods_info = objectToArrayZ($goods_info);
        return $goods_info;
    }

    /**
     * description:提交编辑商品规格
     * editor:zongxing
     * date : 2018.08.25
     * return Object
     */
    public function doEditGoodsSpec($param_info, $goods_info)
    {
        $param = [
            'spec_sn' => $goods_info['spec_sn'],
            'erp_merchant_no' => $goods_info['erp_merchant_no'],
        ];
        $goods_code_model = new GoodsCodeModel();
        $gc_old_info = $goods_code_model->getGoodsCodeInfo($param);
        $spec_id = intval($param_info['spec_id']);
        //组装更新商品规格数据
        $goods_spec_info = [];
        $tmp_spec_arr = ['erp_merchant_no', 'erp_prd_no', 'erp_ref_no', 'goods_label', 'spec_weight', 'estimate_weight',
            'spec_price', 'exw_discount'];
        foreach ($tmp_spec_arr as $k => $v) {
            if (isset($param_info[$v]) && $goods_info[$v] != $param_info[$v]) {
                $tmp_value = trim($param_info[$v]);
                if ($k > 3) {
                    $tmp_value = floatval($param_info[$v]);
                }
                $goods_spec_info[$v] = $tmp_value;
            }
        }
        //组装商品码信息
        $gc_id = 0;
        $update_gc_info = [];
        if (!empty($param_info['erp_merchant_no']) && !empty($gc_old_info)) {
            if ($param_info['erp_merchant_no'] != $gc_old_info['goods_code']) {
                $gc_id = intval($gc_old_info['id']);
                $update_gc_info = [
                    'code_type' => 1,
                    'goods_code' => trim($param_info['erp_merchant_no']),
                    'spec_sn' => $goods_info['spec_sn'],
                ];
            }
        }
        $res = DB::transaction(function () use ($spec_id, $gc_id, $goods_spec_info, $update_gc_info) {
            $res = 1;
            //更新商品规格表
            if (!empty($goods_spec_info)) {
                $res = DB::table('goods_spec')->where('spec_id', $spec_id)->update($goods_spec_info);
            }
            //更新商品码表
            if (!empty($update_gc_info)) {
                $res = DB::table('goods_code')->where('id', $gc_id)->update($update_gc_info);
            }
            return $res;
        });
        return $res;
    }


    /**
     * description 获取商品报价数据
     * author zhangdong
     * date 2019.10.18
     */
    public function goodsOfferData($arrSpecSn, $offerMsg)
    {
        //获取基本报价数据
        $baseOfferData = $this->getBaseOfferData($arrSpecSn, $offerMsg);
        //获取追加折扣数据
        $appendDiscountData = $this->getAppendDiscount($offerMsg);
        //将追加折扣数据写入查询结果
        foreach ($baseOfferData as $key => $value) {
            $spec_sn = trim($value->spec_sn);
            $searchRes = searchArrayGetOne($appendDiscountData, $spec_sn, 'spec_sn');
            $appendDiscountData = $searchRes['arrData'];
            $appendDiscount = 0;
            if (count($searchRes['searchRes']) > 0) {
                $appendDiscount = $searchRes['searchRes']['discount'];
            }
            $baseOfferData[$key]->appendDiscount = $appendDiscount;
            //加入一个时间标记用来区分数据生成时间
            $baseOfferData[$key]->createTime = date('Y-m-d');
        }
        return $baseOfferData;
    }//end of function

    /**
     * description 获取基本报价数据
     * author zhangdong
     * date 2019.10.20
     */
    private function getBaseOfferData($arrSpecSn = [], $offerMsg)
    {
        $channelId = ParamsSet::getChannelId();
        $generateDate = ParamsSet::getGenerateDate();
        $startDate = $endDate = date('Y-m');
        $typeId = $offerMsg['offerId'];
        $highPriceId = $offerMsg['highPriceId'];
        $field = [
            'b.name as brand_name', 'g.ext_name', 'g.goods_name', 'gs.erp_prd_no', 'gs.erp_ref_no',
            'gs.erp_merchant_no', 'gs.spec_price', 'gs.spec_weight', 'gs.spec_price',
            'gs.spec_sn', 'dt.discount as dt_discount', 'gd.discount as gd_discount',
            'sc.cat_name', 'sc.match_scale'
        ];
        $dt_on_where = [
            ['dt.start_date', 'like', $startDate . '%'],
            ['dt.end_date', 'like', $endDate . '%'],
            ['dt.type_id', $typeId],
        ];
        $gd_on_where = [
            ['gd.channels_id', $channelId],
            ['gd.type_id', $highPriceId],
        ];
        $queryRes = DB::table('goods_spec as gs')->select($field)
            ->leftJoin('goods as g', 'g.goods_sn', 'gs.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', 'g.brand_id')
            ->leftJoin('shop_cart as sc', function ($join) use ($generateDate) {
                $join->on('sc.spec_sn', 'gs.spec_sn')->where('sc.cart_date', $generateDate);
            })
            ->leftJoin('discount as d', function ($join) use ($channelId) {
                $join->on('d.brand_id', 'g.brand_id')->where('d.channels_id', $channelId);
            })
            ->leftJoin('discount_type as dt', function ($join) use ($dt_on_where) {
                $join->on('d.id', 'dt.discount_id')->where($dt_on_where);
            })
            ->leftJoin('discount_type_info as dti', 'dti.id', 'dt.type_id')
            ->leftJoin('gmc_discount AS gd', function ($join) use ($gd_on_where) {
                $join->on('gd.spec_sn', 'gs.spec_sn')->where($gd_on_where);
            })->whereIn('gs.spec_sn', $arrSpecSn)
            ->groupBy('gs.spec_sn')->orderBy('sc.cat_name', 'ASC')
            ->orderBy('sc.match_scale', 'DESC')->get();
        return $queryRes;

    }

    /**
     * description 获取追加折扣数据
     * author zhangdong
     * date 2019.10.18
     */
    private function getAppendDiscount($offerMsg)
    {
        $lowPriceId = $offerMsg['lowPriceId'];
        $channelId = ParamsSet::getChannelId();
        $dayStart = $dayEnd = ParamsSet::getGenerateDate();
        $startDate = $endDate = date('Y-m');
        $monthStart = date('Y-m-01', strtotime(date('Y-m-d')));
        $monthEnd = date('Y-m-d', strtotime("$monthStart +1 month -1 day"));
        $field_first = ['gs.spec_sn', 'dt.discount',];
        $where_first = [
            ['d.channels_id', $channelId],
            ['dt.start_date', 'like', $startDate . '%'],
            ['dt.end_date', 'like', $endDate . '%'],
        ];
        //追加折扣 - 品牌
        $query_first = DB::table('goods_spec as gs')->select($field_first)
            ->leftJoin('goods as g', 'gs.goods_sn', 'g.goods_sn')
            ->leftJoin('discount as d', 'd.brand_id', 'g.brand_id')
            ->leftJoin('discount_type as dt', 'd.id', 'dt.discount_id')
            ->leftJoin('discount_type_info as dti', 'dti.id', 'dt.type_id')
            ->where($where_first)->whereIn('dt.type_id', [11, 13, 27, 47]);
        $field_second = ['gs.spec_sn', 'gd.discount',];
        $where_second = [
            ['gd.type_id', $lowPriceId],
            ['gd.channels_id', $channelId],
            ['gd.start_date', $monthStart],
            ['gd.end_date', $monthEnd],
        ];
        //追加折扣-低价sku折扣-月度统计
        $query_second = DB::table('goods_spec as gs')->select($field_second)
            ->leftJoin('goods as g', 'gs.goods_sn', 'g.goods_sn')
            ->leftJoin('gmc_discount AS gd', 'gd.spec_sn', 'gs.spec_sn')
            ->where($where_second);
        $where_third = [
            ['gd.type_id', $lowPriceId],
            ['gd.channels_id', $channelId],
            ['gd.start_date', $dayStart],
            ['gd.end_date', $dayEnd],
        ];
        //追加折扣-低价sku折扣-按天统计
        $query_third = DB::table('goods_spec as gs')->select($field_second)
            ->leftJoin('goods as g', 'gs.goods_sn', 'g.goods_sn')
            ->leftJoin('gmc_discount AS gd', 'gd.spec_sn', 'gs.spec_sn')
            ->where($where_third)->unionAll($query_first)->unionAll($query_second);
        $queryRes = $query_third->get();
        //将查询结果按规格码分组——追加折扣=品牌追加折扣+低价sku折扣_月度统计+低价sku折扣_按天统计
        $arrQueryRes = objectToArray($queryRes);
        $group_field = ['spec_sn'];
        $group_by_value = [
            'spec_sn',
            'discount' => function ($data) {
                $totalNum = array_sum(array_column($data, 'discount'));
                return $totalNum;
            }
        ];
        $queryRes = ArrayGroupBy::groupBy($arrQueryRes, $group_field, $group_by_value);
        return $queryRes;
    }

    /**
     * description //整理上传商品信息（维护商品追加折扣）
     * author zongxing
     * date 2019.10.22
     */
    public function createUploadGoodsInfo($res, $arrTitle)
    {
        $spec_info = $upload_goods_info = $brand_id_arr = [];
        $spec_code_arr = [
            '商家编码' => 'erp_merchant_no',
            '商品代码' => 'erp_prd_no',
            '参考码' => 'erp_ref_no',
            '品牌ID' => 'brand_id',
            '商品品牌' => 'brand_name',
            '折扣/返点' => 'discount',
        ];
        foreach ($res as $k => $v) {
            if ($k == 0) continue;
            $tmp_arr = [];
            foreach ($arrTitle as $k1 => $v1) {
                if (isset($spec_code_arr[$v1])) {
                    $key = array_search($v1, $res[0]);
                    $tmp_arr[$spec_code_arr[$v1]] = $v[$key];
                }
            }
            if (empty($tmp_arr['brand_id']) || empty($tmp_arr['discount'])) {
                return ['code' => '1101', 'msg' => '第 ' . $k . ' 条商品中的品牌ID、折扣(返点)不能为空'];
            }
            if (empty($tmp_arr['erp_merchant_no']) && empty($tmp_arr['erp_prd_no']) && empty($tmp_arr['erp_ref_no'])) {
                return ['code' => '1102', 'msg' => '第 ' . $k . ' 条商品中的商家编码、商品代码、参考码必须有一个'];
            }
            if (!empty($tmp_arr['erp_merchant_no'])) {
                $spec_info['erp_merchant_no'][] = trim($tmp_arr['erp_merchant_no']);
            }
            if (!empty($tmp_arr['erp_prd_no'])) {
                $spec_info['erp_prd_no'][] = trim($tmp_arr['erp_prd_no']);
            }
            if (!empty($tmp_arr['erp_ref_no'])) {
                $spec_info['erp_ref_no'][] = trim($tmp_arr['erp_ref_no']);
            }
            $upload_goods_info[] = $tmp_arr;
        }

        $spec_detail = $this->getGoodsSpecDetail($spec_info);
        $diff_goods_info = [];
        foreach ($upload_goods_info as $k => $v) {
            $erp_merchant_no = trim($v['erp_merchant_no']);
            $erp_prd_no = trim($v['erp_prd_no']);
            $erp_ref_no = trim($v['erp_ref_no']);
            if (isset($spec_detail['erp_merchant_no'][$erp_merchant_no])) {
                $upload_goods_info[$k]['spec_sn'] = trim($spec_detail['erp_merchant_no'][$erp_merchant_no]);
                continue;
            }
            if (isset($spec_detail['erp_prd_no'][$erp_prd_no])) {
                $upload_goods_info[$k]['spec_sn'] = trim($spec_detail['erp_prd_no'][$erp_prd_no]);
                continue;
            }
            if (isset($spec_detail['erp_ref_no'][$erp_ref_no])) {
                $upload_goods_info[$k]['spec_sn'] = trim($spec_detail['erp_ref_no'][$erp_ref_no]);
                continue;
            }
            //收集特价商品中不存在的商品的品牌id
            $brand_id = intval($v['brand_id']);
            if (!in_array($brand_id, $brand_id_arr)) {
                $brand_id_arr[] = $brand_id;
            }
            $diff_goods_info[] = $v;
            unset($upload_goods_info[$k]);
        }

        //获取EXW折扣信息
        $dt_model = new DiscountTypeModel();
        $exw_discount_info = $dt_model->getExwDiscountInfo($brand_id_arr);
        foreach ($diff_goods_info as $k => $v) {
            $brand_id = intval($v['brand_id']);
            $exw_discount = 0;
            if (isset($exw_discount_info[$brand_id])) {
                $exw_discount = floatval($exw_discount_info[$brand_id]);
            }
            $diff_goods_info[$k]['exw_discount'] = $exw_discount;
            unset($diff_goods_info[$k]['discount']);
        }
        $return_info = [
            'upload_goods_info' => $upload_goods_info,
            'diff_goods_info' => $diff_goods_info,
        ];
        return $return_info;
    }

    /**
     * description 获取商品信息
     * author zongxing
     * date 2019.10.22
     */
    public function getGoodsSpecDetail($param_info)
    {
        $spec_detail = [];
        $spec_obj = DB::table($this->table);
        if (!empty($param_info['erp_merchant_no'])) {
            $spec_obj->whereIn('erp_merchant_no', $param_info['erp_merchant_no']);
            $spec_detail['erp_merchant_no'] = $spec_obj->pluck('spec_sn', 'erp_merchant_no');
        }
        if (!empty($param_info['erp_prd_no'])) {
            $spec_obj->whereIn('erp_prd_no', $param_info['erp_prd_no']);
            $spec_detail['erp_prd_no'] = $spec_obj->pluck('spec_sn', 'erp_prd_no');
        }
        if (!empty($param_info['erp_ref_no'])) {
            $spec_obj->whereIn('erp_ref_no', $param_info['erp_ref_no']);
            $spec_detail['erp_ref_no'] = $spec_obj->pluck('spec_sn', 'erp_ref_no');
        }
        $spec_detail = objectToArrayZ($spec_detail);
        return $spec_detail;
    }


    /**
     * description 通过商家编码获取spec_sn-ERP库存同步专用
     * author zhangdong
     * date 2019.11.29
     */
    public function getSpecSnByErpNo($value)
    {
        $field = ['spec_sn'];
        $where[] = ['spec_sn', $value];
        $queryRes = DB::table('goods_spec')->select($field)->where($where)->first();
        return $queryRes;
    }

    /**
     * description 通过商家编码获取SKU信息-ERP重量更新接口专用
     * author zhangdong
     * date 2020.02.24
     */
    public function getSkuInfoByErpNo($value)
    {
        $where = [
            ['erp_merchant_no', $value],
        ];
        $field = [
            'spec_sn', 'spec_weight',
        ];
        $queryRes = DB::table($this->table)->select($field)->where($where)->first();
        return $queryRes;
    }

    /**
     * description 更新商品重量-ERP重量更新接口专用
     * author zhangdong
     * date 2020.02.24
     */
    public function updateWeight($specSn, $erpWeight)
    {
        $where = [
            ['spec_sn', $specSn],
        ];
        $update = [
            'spec_weight' => $erpWeight,
        ];
        $queryRes = DB::table($this->table)->where($where)->update($update);
        return $queryRes;
    }

    /**
     * desc 更新商品预估重量，附带将实际重量也更新为预估重量
     * notice 旨在更新系统中没有重量的商品
     * author zhangdong
     * date 2020.03.13
     */
    public function updateEstimateWeight($arr_update)
    {
        $table = 'jms_goods_spec';
        $arrSql = makeUpdateSql($table, $arr_update);
        if ($arrSql === false) {
            return false;
        }
        //开始批量更新
        $strSql = $arrSql['updateSql'];
        $bindData = $arrSql['bindings'];
        $executeRes = DB::update($strSql, $bindData);
        return $executeRes;
    }


    /**
     * desc 获取商品图片-专用
     * notice 仅限本地，线上数据库没有 is_img 字段
     * author zhangdong
     * date 2020.03.20
     */
    public function getGoodsImg()
    {
        $where = [
            [DB::raw('length(spec_img)'), '>', 0],
            ['is_img', 0],
        ];
        $filed = ['spec_sn', 'erp_merchant_no', 'spec_img'];
        $queryRes = DB::table($this->table)->select($filed)->where($where)->limit(500)->get();
        return $queryRes;
    }

    /**
     * desc 修改SKU图片状态为已存在-临时使用
     * notice 仅限本地，线上数据库没有 is_img 字段
     * author zhangdong
     * date 2020.03.20
     */
    public function updateImgStatus(array $arrSpecSn)
    {
        $update = [
            'is_img' => 1,
        ];
        $updateRes = DB::table($this->table)->whereIn('spec_sn', $arrSpecSn)->update($update);
        return $updateRes;
    }

    /**
     * desc 获取商品图片-该方法可根据实际情况随意修改
     * notice 涉及表为临时表
     * author zhangdong
     * date 2020.04.24
     */
    public function getImgToolInfo()
    {
        $where = [
            ['mark', 6],
        ];
        $filed = ['id', 'erpNo', 'goods_picture',];
        $queryRes = DB::table('lt_goods')->select($filed)->where($where)->get();
        return $queryRes;
    }

    /**
     * 通过品牌获取商品规格信息
     * author zongxing
     * date 2020/5/7 0007
     * @param $brand_id_arr 品牌ID信息
     * @return array|mixed
     */
    public function getSpecInfoByBrandId($brand_id_arr)
    {
        $spec_info = DB::table($this->table)
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->whereIn('g.brand_id', $brand_id_arr)->pluck('g.brand_id', 'spec_sn');
        $spec_info = objectToArrayZ($spec_info);
        return $spec_info;
    }

    /**
     * desc 修改SKU图片状态为已存在-该方法可根据实际情况随意修改
     * notice 涉及表为临时表
     * author zhangdong
     * date 2020.04.24
     */
    public function updateImgTool($id)
    {
        $update = [
            'mark' => 7,
        ];
        $where = [
            ['id', $id],
        ];
        $updateRes = DB::table('lt_goods')->where($where)
            ->update($update);
        return $updateRes;
    }


    /**
     * 获取商品信息
     * author zongxing
     * date 2020/5/14 0014
     * @param $param
     * @return array|mixed
     */
    public function getGoodsSpecList($param, $key_str = '')
    {
        $spec_obj = DB::table($this->table)->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn');
        if (!empty($param['goods_name'])) {
            $spec_obj->where('g.goods_name', 'like', $param['goods_name']);
        }
        if (!empty($param['spec_sn'])) {
            $spec_obj->whereIn('gs.spec_sn', $param['spec_sn']);
        }
        if (!empty($param['erp_merchant_no'])) {
            $spec_obj->whereIn('gs.erp_merchant_no', $param['erp_merchant_no']);
        }
        if (!empty($param['erp_prd_no'])) {
            $spec_obj->whereIn('gs.erp_prd_no', $param['erp_prd_no']);
        }
        if (!empty($param['erp_ref_no'])) {
            $spec_obj->whereIn('gs.erp_ref_no', $param['erp_ref_no']);
        }
        $field = [
            'g.goods_name', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.erp_prd_no', 'gs.erp_ref_no'
        ];
        $spec_detail = $spec_obj->get($field);
        $spec_detail = objectToArrayZ($spec_detail);
        if (empty($key_str)) {
            return $spec_detail;
        }
        $specList = [];
        foreach ($spec_detail as $k => $v) {
            $specList[$v[$key_str]] = $v;
        }
        return $specList;
    }




}//end of class
