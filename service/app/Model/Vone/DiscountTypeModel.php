<?php

namespace App\Model\Vone;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DiscountTypeModel extends Model
{
    protected $table = 'discount_type as dt';

    //可操作字段
    protected $field = ['dt.id', 'dt.start_date', 'dt.end_date', 'dt.discount_id', 'dt.discount', 'dt.type_id'];

    //免税店编码对照 zhangdong 2019.08.23
    private $DFS_sn = [
        'abk' => 'QD-024-FS-001',
        'lt' => 'QD-002-FS-001',
        'xl' => 'QD-004-FS-001',
    ];


    //修改laravel 自动更新
    const UPDATED_AT = "modify_time";
    const CREATED_AT = "create_time";

    /**
     * description:维护折扣类型对应的折扣
     * editor:zongxing
     * date : 2019.05.05
     * return Array
     */
    public function doUploadDiscountType_stop($upload_info, $discount_type_info, $param_info)
    {
        $method_id = intval($param_info['method_id']);
        $channels_id = intval($param_info['channels_id']);
        $type_id = intval($param_info['type_id']);
        $type_cat = intval($discount_type_info[0]['type_cat']);
        //获取品牌、方式、渠道组合id
        $brand_id = array_keys($upload_info);
        $discount_model = new DiscountModel();
        $discount_id_info = $discount_model->getDiscountIdInfo($brand_id, $method_id, $channels_id);
        //折扣表新增数据
        $cost_discount_id = $type_cat == 1 ? $type_id : 0;
        $vip_discount_id = $type_cat == 2 ? $type_id : 0;
        $insertDiscount = [];
        foreach ($upload_info as $k => $v) {
            $discount = number_format($v, 4);
            //discount表新增数据
            if (!isset($discount_id_info[$k])) {
                $insertDiscount[] = [
                    'brand_id' => $k,
                    'method_id' => $method_id,
                    'channels_id' => $channels_id,
                    'brand_discount' => $discount,
                    'cost_discount_id' => $cost_discount_id,
                    'vip_discount_id' => $vip_discount_id,
                ];
            }
        }
        //新增品牌渠道方式组合
        if (!empty($insertDiscount)) {
            $res = DB::table('discount')->insert($insertDiscount);
            if ($res == false) {
                return $res;
            }
        }

        //获取更新后的品牌、方式、渠道组合id
        $brand_id = array_keys($upload_info);
        $discount_model = new DiscountModel();
        $discount_id_info = $discount_model->getDiscountIdInfo($brand_id, $method_id, $channels_id);

        //获取档位折扣信息
        $field_name = 'discount_id';
        $discount_brand_info = $this->getDiscountTypeInfo($param_info, $field_name);
        //组装档位折扣信息
        $insertDiscountType = [];
        $updateDiscountType = [];
        foreach ($upload_info as $k => $v) {
            //discount_type表新增和更新数据
            $discount_id = intval($discount_id_info[$k]);
            $discount = number_format($v, 4);
            if (isset($discount_brand_info[$discount_id]) && $discount_brand_info[$discount_id] != $discount) {
                $updateDiscountType['discount'][] = [
                    $discount_id => $discount
                ];
            } elseif (!isset($discount_brand_info[$discount_id])) {
                $insertDiscountType[] = [
                    'start_date' => trim($param_info['start_date']),
                    'end_date' => trim($param_info['end_date']),
                    'discount_id' => $discount_id,
                    'discount' => $discount,
                    'type_id' => $type_id,
                ];
            }
        }
        $updateDiscountTypeSql = '';
        if (!empty($updateDiscountType)) {
            //更新条件
            $where = [
                'type_id' => $type_id,
                'start_date' => trim($param_info['start_date']),
                'end_date' => trim($param_info['end_date']),
            ];
            //需要判断的字段
            $column = 'discount_id';
            $updateDiscountTypeSql = makeBatchUpdateSql('jms_discount_type', $updateDiscountType, $column, $where);
        }
        $Res = DB::transaction(function () use ($insertDiscountType, $updateDiscountTypeSql, $param_info) {
            $res = true;
            //档位折扣表新增数据
            if (!empty($insertDiscountType)) {
                $res = DB::table('discount_type')->insert($insertDiscountType);
            }
            //档位折扣表更新数据
            if (!empty($updateDiscountTypeSql)) {
                $res = DB::update(DB::raw($updateDiscountTypeSql));
            }
            $is_exw = intval($param_info['is_exw']);
            if ($is_exw == 1) {
                //获取折扣类型信息
                $dt_model = new DiscountTypeModel();
                $field_name = 'brand_id';
                $brand_info = $dt_model->getDiscountTypeInfo($param_info, $field_name);
                $discount_model = new DiscountModel();
                $res = $discount_model->setExwDiscount($param_info, $brand_info);
            }
            return $res;
        });
        return $Res;
    }

    /**
     * description:维护折扣类型对应的折扣
     * editor:zongxing
     * date : 2019.05.05
     * return Array
     */
    public function doUploadDiscountType($upload_info, $dti_info, $param_info)
    {
        //获取品牌、渠道组合id
        $brand_id_arr = array_keys($upload_info);
        $channels_id_arr = [];
        foreach ($dti_info as $k => $v) {
            if (!in_array($v['channels_id'], $channels_id_arr)) {
                $channels_id_arr[] = $v['channels_id'];
            }
        }
        $type_arr = explode(',', $param_info['type_id']);
        $discount_model = new DiscountModel();
        $discount_list = $discount_model->getDiscountIdInfo($brand_id_arr, $channels_id_arr, $type_arr);
        //discount表新增数据
        $insertDiscount = [];
        foreach ($upload_info as $k => $v) {
            $discount = number_format($v, 4);
            foreach ($dti_info as $k1 => $v1) {
                $pin_str = $v1['channels_id'];
                if (!isset($discount_list[$k][$pin_str])) {
                    $insertDiscount[] = [
                        'brand_id' => $k,
                        'method_id' => $v1['method_id'],
                        'channels_id' => $v1['channels_id'],
                        'brand_discount' => $discount,
                    ];
                }
            }
        }
        if (!empty($insertDiscount)) {
            $res = DB::table('discount')->insert($insertDiscount);
            if ($res == false) {
                return $res;
            }
        }
        //获取更新后的品牌、方式、渠道组合id
        $discount_list = $discount_model->getDiscountIdInfo($brand_id_arr, $channels_id_arr, $type_arr, 'id');
        //获取档位折扣信息
        $field_name = 'discount_id';
        $param_info['type_id_arr'] = $type_arr;
        $discountOldInfo = $this->getDiscountTypeList($param_info, $field_name);
        $discountOldList = [];
        foreach ($discountOldInfo as $k => $v) {
            $discountOldList[$v['discount_id']] = $v;
        }
        //组装档位折扣信息
        $insertDiscountType = $updateDiscountType = [];
        foreach ($upload_info as $k1 => $v1) {
            //discount_type表新增和更新数据
            if (!isset($discount_list[$k1])) continue;
            $discount = number_format($v1, 4);
            foreach ($discount_list[$k1] as $k2 => $v2) {
                if (!isset($discountOldList[$k2])) {
                    $type_id = $v2['type_id'];
                    $insertDiscountType[] = [
                        'start_date' => trim($param_info['start_date']),
                        'end_date' => trim($param_info['end_date']),
                        'discount_id' => $k2,
                        'discount' => $discount,
                        'type_id' => $type_id,
                    ];
                    continue;
                }
                $discountTypeId = $discountOldList[$k2]['id'];
                if ($discountOldList[$k2]['discount'] != $discount) {
                    $updateDiscountType['discount'][] = [
                        $discountTypeId => $discount
                    ];
                }
            }
        }
        //获取品牌对应的商品信息
        $goodsSpecModel = new GoodsSpecModel();
        $SpecInfo = $goodsSpecModel->getSpecInfoByBrandId($brand_id_arr);
        //获取已经存在的最低折扣记录
        $now_month = date('Y-m');
        $param = [
            'discount_month' => $now_month,
            'spec_sn_arr' => array_keys($SpecInfo),
            'channels_id_arr' => $channels_id_arr,
        ];
        $discountRecordModel = new DiscountRecordModel();
        $discountRecordInfo = $discountRecordModel->getDiscountRecordInfo($param);
        $discountRecordList = [];
        foreach ($discountRecordInfo as $v) {
            $pin_str = $v['spec_sn'] . '_' . $v['discount_month'] . '_' . $v['channels_id'];
            $discountRecordList[$pin_str] = $v;
        }
        //组装商品对应的最低折扣记录
        $insertDiscountRecordInfo = $updateDiscountRecordInfo = [];
        foreach ($dti_info as $k => $v) {
            foreach ($SpecInfo as $k1 => $v1) {
                if (!isset($upload_info[$v1])) continue;
                $channels_id = $v['channels_id'];
                $add_type = $v['add_type'];
                $pin_str = $k1 . '_' . $now_month . '_' . $channels_id;
                $new_discount = $upload_info[$v1];
                //本月当前渠道不存在该商品对应的折扣记录
                if (!isset($discountRecordList[$pin_str])) {
                    $insertDiscountRecordInfo[$pin_str] = [
                        'spec_sn' => $k1,
                        'discount_month' => $now_month,
                        'channels_id' => $channels_id,
                    ];
                    //记录最低折扣：1,折扣替换 3,折扣取反（例如：高价sku返点）
                    if ($add_type == 1 || $add_type == 3) {
                        $new_discount = $add_type == 3 ? 1 - $new_discount : $new_discount;
                        $insertDiscountRecordInfo[$pin_str]['discount'] = $new_discount;
                        //记录追加点：2,折扣累减 4,折扣累加
                    } elseif ($add_type == 2 || $add_type == 4) {
                        $new_discount = $add_type == 2 ? -$new_discount : $new_discount;
                        $insertDiscountRecordInfo[$pin_str]['add_point'] = $new_discount;
                    }
                    continue;
                }
                //本月当前渠道已经存在该商品对应的折扣记录
                $old_discount = $discountRecordList[$pin_str]['discount'];
                $updateId = $discountRecordList[$pin_str]['id'];
                $add_point = $discountRecordList[$pin_str]['add_point'];
                if ($add_type == 1 || $add_type == 3) {
                    $new_discount = $add_type == 3 ? 1 - $new_discount : $new_discount;
                    $new_discount += $add_point;
                    if ($new_discount >= $old_discount) continue;
                    $updateDiscountRecordInfo['discount'][] = [
                        $updateId => $new_discount
                    ];
                } elseif ($add_type == 2 || $add_type == 4) {
                    $new_add_discount = $add_type == 2 ? -$new_discount : $new_discount;
                    if ($new_add_discount >= $add_point) continue;//如果本次维护的追加点大于等于记录表中存在的追加点，则无效
                    $new_discount = $old_discount - ($add_point - $new_add_discount);
                    $updateDiscountRecordInfo['discount'][] = [
                        $updateId => $new_discount
                    ];
                    $updateDiscountRecordInfo['add_point'][] = [
                        $updateId => $new_add_discount
                    ];
                }
            }
        }
        $insertDiscountRecordInfo = array_values($insertDiscountRecordInfo);
        //生成更新最低折扣记录表sql
        $updateDiscountRecordInfoSql = '';
        if (!empty($updateDiscountRecordInfo)) {
            //需要判断的字段
            $column = 'id';
            $updateDiscountRecordInfoSql = makeBatchUpdateSql('jms_discount_record', $updateDiscountRecordInfo, $column);
        }
        //执行事务
        $Res = DB::transaction(function () use (
            $insertDiscountType, $updateDiscountType, $param_info,
            $insertDiscountRecordInfo, $updateDiscountRecordInfoSql
        ) {
            $res = true;
            //插入当月折扣记录表数据
            if (!empty($insertDiscountRecordInfo)) {
                $res = DB::table('discount_record')->insert($insertDiscountRecordInfo);
            }
            //更新当月折扣记录表数据
            if (!empty($updateDiscountRecordInfoSql)) {
                $res = DB::update(DB::raw($updateDiscountRecordInfoSql));
            }
            //档位折扣表新增数据
            if (!empty($insertDiscountType)) {
                $res = DB::table('discount_type')->insert($insertDiscountType);
            }
            //档位折扣表更新数据
            if (!empty($updateDiscountType)) {
                $type_arr = $param_info['type_id_arr'];
                foreach ($type_arr as $k => $v) {
                    //更新条件
                    $where = [
                        'start_date' => trim($param_info['start_date']),
                        'end_date' => trim($param_info['end_date']),
                        'type_id' => $v,
                    ];
                    //需要判断的字段
                    $column = 'discount_id';
                    $updateDiscountTypeSql = makeBatchUpdateSql('jms_discount_type', $updateDiscountType,
                        $column, $where);
                    $res = DB::update(DB::raw($updateDiscountTypeSql));
                }
            }
            return $res;
        });
        return $Res;
    }

    /**
     * description:获取指定档位折扣信息
     * editor:zongxing
     * date : 2019.05.05
     * return Array
     */
    public function getDiscountTypeInfo($param_info, $field_name)
    {
        $start_date = Carbon::now()->firstOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();
        if (isset($param_info['start_date'])) {
            $start_date = trim($param_info['start_date']);
        }
        if (isset($param_info['end_date'])) {
            $end_date = trim($param_info['end_date']);
        }
        $where = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'type_id' => $param_info['type_id'],
        ];
        $discount_brand_obj = DB::table($this->table)
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->where($where);
        if (!empty($discount_id)) {
            $discount_brand_obj->where('discount_id', $discount_id);
        }
        $discount_brand_info = $discount_brand_obj->pluck('discount', $field_name);
        $discount_brand_info = objectToArrayZ($discount_brand_info);
        return $discount_brand_info;
    }

    /**
     * description:获取档位折扣信息
     * editor:zongxing
     * date : 2019.05.05
     * return Array
     */
    public function getDiscountTypeList($param_info)
    {
        $start_date = Carbon::now()->firstOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();
        if (isset($param_info['start_date'])) {
            $start_date = trim($param_info['start_date']);
        }
        if (isset($param_info['end_date'])) {
            $end_date = trim($param_info['end_date']);
        }
        $where = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        $discount_brand_obj = DB::table($this->table)
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->whereIn('type_id', $param_info['type_id_arr'])
            ->where($where);
        $discount_brand_info = $discount_brand_obj->get(['dt.id', 'dt.discount_id', 'dt.discount']);
        $discount_brand_info = objectToArrayZ($discount_brand_info);
        return $discount_brand_info;
    }

    /**
     * description 获取指定档位折扣信息
     * editor zongxing
     * date 2019.10.22
     * return Array
     */
    public function getExwDiscountInfo($brand_id_arr)
    {
        $exw_discount_info = DB::table('discount_type_info as dti')
            ->leftJoin('discount_type as dt', 'dt.type_id', '=', 'dti.id')
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->where('exw_type', 1)
            ->whereIn('d.brand_id', $brand_id_arr)
            ->pluck('dt.discount', 'd.brand_id');
        $exw_discount_info = objectToArrayZ($exw_discount_info);
        return $exw_discount_info;
    }

    /**
     * description 获取当月真实的档位对应的各个品牌的折扣
     * author zongxing
     * date 2019.07.25
     */
    public function getMonthGearPoints($type_id, $param_info)
    {
        $start_date = date('Y-m-d', strtotime(trim($param_info['start_date'])));
        $end_date = date('Y-m-d', strtotime(trim($param_info['end_date'])));
        $where = [
            ['dt.start_date', '>=', $start_date],
            ['dt.end_date', '<=', $end_date],
        ];
        $queryRes = DB::table($this->table)
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->where($where)
            ->whereIn('dt.type_id', $type_id)
            ->pluck('dt.discount', 'd.brand_id');
        $queryRes = objectToArrayZ($queryRes);
        return $queryRes;
    }

    /**
     * description 获取当月真实的档位对应的各个品牌的折扣
     * author zongxing
     * date 2019.07.25
     */
    public function getMonthGearPoints1_stop($channelId, $start_date, $end_date, $type_cat, $performance = 0)
    {
        $where = [
            ['dt.start_date', '<=', $start_date],
            ['dt.end_date', '>=', $end_date],
            ['dti.channels_id', '=', $channelId],
            ['dti.type_cat', '=', $type_cat],
        ];
        if ($performance) {
            $add_where = [
                ['dti.min', '<=', $performance],
                ['dti.max', '>=', $performance],
            ];
            $where = array_merge($where, $add_where);
        }
        $queryRes = DB::table($this->table)
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->where($where)
            ->pluck('discount', 'brand_id');
        $queryRes = objectToArrayZ($queryRes);
        return $queryRes;
    }

    /**
     * description 获取当月品牌活动对应的品牌信息
     * author zongxing
     * date 2019.08.12
     */
    public function getMonthGearBrand($type_id, $start_date, $end_date)
    {
        $where = [
            ['dt.start_date', '<=', $start_date],
            ['dt.end_date', '>=', $end_date],
        ];
        $queryRes = DB::table($this->table)
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->where($where)
            ->whereIn('dt.type_id', $type_id)
            ->get(['discount', 'brand_id', 'type_id'])->groupBy('type_id');
        $queryRes = objectToArrayZ($queryRes);
        return $queryRes;
    }


    /**
     * description 获取当月真实的档位对应的各个品牌的折扣
     * author zongxing
     * date 2019.07.25
     */
    public function getTimeMonthGear($channelId, $type_cat, $start_date, $end_date, $month_type_arr)
    {
        $where = [
            ['dt.start_date', '>=', $start_date],
            ['dt.end_date', '>=', $start_date],
            ['dt.start_date', '<=', $end_date],
            ['dt.end_date', '<=', $end_date],
            ['dti.channels_id', '=', $channelId],
            ['dti.method_id', '=', 34],//线上
            ['dti.type_cat', '=', $type_cat],
        ];
        $field = [
            'dt.type_id', 'dt.start_date', 'dt.end_date', 'dt.discount', 'dti.min', 'dti.max', 'd.brand_id'
        ];
        $queryRes = DB::table($this->table)
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->where($where)
            ->whereIn('dti.id', $month_type_arr)
            ->get($field);
        $queryRes = objectToArrayZ($queryRes);
        return $queryRes;
    }

    /**
     * description:获取品牌采购折扣列表
     * editor:zongxing
     * date : 2019.05.06
     * return Array
     */
    public function discountTotalList($param_info)
    {
        $start_date = Carbon::now()->firstOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();
        if (isset($param_info['start_date'])) {
            $start_date = trim($param_info['start_date']);
        }
        if (isset($param_info['end_date'])) {
            $end_date = trim($param_info['end_date']);
        }
        $where1 = [
            ['dt.start_date', '<=', $start_date],
            ['dt.end_date', '>=', $end_date],
        ];

        $field = $this->field;
        $add_field = [
            'pm.method_name', 'pc.channels_name', 'dti.type_name',
            'b.name', 'dt.discount', 'dt.start_date', 'dt.end_date', 'cat_name',
            DB::raw('(CASE jms_dti.is_start
            WHEN 1 THEN "是"
            WHEN 0 THEN "否"
            END) is_start'),
        ];
        $field = array_merge($field, $add_field);
        $discount_obj = DB::table($this->table)
            ->leftJoin('discount as d', function ($join) {
                $join->on('d.id', '=', 'dt.discount_id');
            })
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'd.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'd.channels_id')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'd.brand_id')
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->leftJoin('discount_cat as dc', 'dc.id', '=', 'dti.type_cat')
            ->where(function ($join) use ($where1) {
                $join->where($where1);
            });
        if (isset($param_info['query_sn'])) {
            $query_sn = trim($param_info['query_sn']);
            $query_sn = '%' . $query_sn . '%';
            $discount_obj->where(function ($join) use ($query_sn) {
                $join->orWhere('pc.channels_name', 'like', $query_sn)
                    ->orWhere('pm.method_name', 'like', $query_sn)
                    ->orWhere('b.name', 'like', $query_sn)
                    ->orWhere('b.keywords', 'like', $query_sn);
            });
        }
        if (isset($param_info['goods_name'])) {
            $spec_sn = trim($param_info['goods_name']);
            $discount_obj->where('g.goods_name', $spec_sn);
        }
        if (isset($param_info['spec_sn'])) {
            $spec_sn = trim($param_info['spec_sn']);
            $discount_obj->where('gs.spec_sn', $spec_sn);
        }
        if (isset($param_info['erp_merchant_no'])) {
            $spec_sn = trim($param_info['erp_merchant_no']);
            $discount_obj->where('gs.erp_merchant_no', $spec_sn);
        }
        if (isset($param_info['erp_ref_no'])) {
            $spec_sn = trim($param_info['erp_ref_no']);
            $discount_obj->where('gs.erp_ref_no', $spec_sn);
        }
        if (isset($param_info['erp_prd_no'])) {
            $spec_sn = trim($param_info['erp_prd_no']);
            $discount_obj->where('gs.erp_prd_no', $spec_sn);
        }
        $discount_total_list = $discount_obj->orderBy('dt.create_time', 'desc')->get($field);
        $discount_total_list = objectToArrayZ($discount_total_list);
        return $discount_total_list;
    }

    /**
     * description 获取当前采购最终折扣数据
     * editor zongxing
     * date 2019.05.14
     * return Array
     * param:
     *  $goods_info:array:1 [
     *      'spec_sn' => '1234',
     *      'brand_id' => '1234',
     * ]
     *  $channels_id:渠道id
     */
    public function getFinalDiscount($goods_info, $channels_id = 0, $buy_time = '')
    {
        //收集商品的品牌id和spec_sn信息,主要是缩小查询品牌和商品时的范围
        $brand_id_arr = [];
        $spec_sn_arr = [];
        foreach ($goods_info as $k => $v) {
            $brand_id = intval($v['brand_id']);
            $spec_sn = trim($v['spec_sn']);
            if (!in_array($brand_id, $brand_id_arr)) {
                $brand_id_arr[] = $brand_id;
            }
            if (!in_array($spec_sn, $spec_sn_arr)) {
                $spec_sn_arr[] = $spec_sn;
            }
        }
        //获取选择时间对应的生成毛利所需当月渠道配置
        $start_date = Carbon::now()->firstOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();
        $param = [
            'buy_time' => $buy_time,
            'channels_id' => $channels_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        $dtr_model = new DiscountTypeRecordModel();
        $dtr_info = $dtr_model->getDiscountTypeRecordList($param);
        $dtr_info = $dtr_info['data'];
        $cost_type_arr = $month_type_arr = $predict_type_arr = $goods_cost_type_arr = $goods_predict_type_arr =
        $pay_id_arr = [];
        foreach ($dtr_info as $k => $v) {
            $cost_type_arr = array_unique(array_merge($cost_type_arr, explode(',', $v['cost_id'])));//品牌成本折扣档位
            $month_type_arr = array_unique(array_merge($month_type_arr, explode(',', $v['month_type_id'])));//当月计算毛利档位
            $predict_id = explode(',', $v['predict_id']);//品牌预计完成档位
            $brand_month_predict_id = explode(',', $v['brand_month_predict_id']);//品牌活动预计完成档位
            $predict_type_arr = array_unique(array_merge($predict_type_arr, $predict_id, $brand_month_predict_id));
            $goods_cost_type_arr = explode(',', $v['goods_cost_id']);//商品预计完成档位
            $goods_predict_type_arr = explode(',', $v['goods_predict_id']);//商品预计完成档位
            $pay_id_arr = array_unique(array_merge($pay_id_arr, explode(',', $v['pay_id'])));//交易支付档位(当渠道
            //original_or_discount==3时,渠道是按照某一档位去支付的)
        }
        if (empty($cost_type_arr)) {
            return $goods_info;//如果不存在品牌成本折扣,则返回原商品数据
        }
        //获取品牌成本折扣和交易支付折扣
        $cost_pay_id_arr = array_unique(array_merge($cost_type_arr, $pay_id_arr));
        $param = [
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
            'brand_id_arr' => $brand_id_arr,
            'brand_type_id' => $cost_pay_id_arr,
        ];
        $brand_discount_list = $this->getBrandDiscount($param, 'type_id');
        if (empty($brand_discount_list)) {
            return $goods_info;//如果无折扣信息,则返回原商品数据
        }
        $cost_list = $pay_list = [];
        foreach ($brand_discount_list as $k => $v) {
            if (in_array($k, $cost_type_arr)) {
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        $cost_list[$k1][$k2] = $v2;
                    }
                }
            }
            if (in_array($k, $pay_id_arr)) {
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        $pay_list[$k1][$k2] = $v2;
                    }
                }
            }
        }
        //获取品牌最终折扣
        $param = [
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
            'brand_id_arr' => $brand_id_arr,
            'month_type_arr' => $month_type_arr,
            'cost_list' => $cost_list,
            'pay_list' => $pay_list,
            'predict_type_arr' => $predict_type_arr,
        ];
        $brand_finnal_discount = $this->getBrandFinalDiscount($param);
        //获取商品最终折扣
        $param = [
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
            'spec_sn_arr' => $spec_sn_arr,
            'goods_cost_type_arr' => $goods_cost_type_arr,
            'goods_predict_type_arr' => $goods_predict_type_arr,
        ];
        $gmc_model = new GmcDiscountModel();
        $spec_finnal_discount = $gmc_model->getSpecFinalDiscount($param);
        $return_info = $this->createFinalDiscount($goods_info, $brand_finnal_discount, $spec_finnal_discount);
        return $return_info;
    }

    /**
     * description:组装商品最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function createFinalDiscount($goods_info, $brand_finnal_discount, $spec_finnal_discount)
    {
        $spec_cost_info = $spec_finnal_discount['spec_cost_info'];//成本折扣
        $spec_high_info = $spec_finnal_discount['spec_high_info'];//高价sku
        $spec_low_info = $spec_finnal_discount['spec_low_info'];//低价sku
        $spec_team_info = $spec_finnal_discount['spec_team_info'];//团队专属sku
        foreach ($goods_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            $brand_id = $v['brand_id'];
            $channels_info = [];
            if (isset($brand_finnal_discount[$brand_id])) {
                $channels_info = $brand_finnal_discount[$brand_id];
                foreach ($channels_info as $k1 => $v1) {
                    $channels_info[$k1]['is_high'] = 0;
                }
                //商品成本折扣(乐天高价sku)
                if (isset($spec_cost_info[$spec_sn])) {
                    foreach ($spec_cost_info[$spec_sn] as $k2 => $v2) {
                        if (isset($channels_info[$k2])) {
                            $channels_info[$k2]['cost_discount'] = 1 - $v2['spec_discount'];
                        }
                    }
                }
            }
            //团队专属商品
            if (isset($spec_team_info[$spec_sn])) {
                foreach ($spec_team_info[$spec_sn] as $k2 => $v2) {
                    if (isset($channels_info[$k2])) {
                        $channels_info[$k2]['brand_discount'] = 1 - $v2['spec_discount'];
                        $channels_info[$k2]['high_discount'] = 1 - $v2['spec_discount'];
                    } else {
                        $v2['brand_discount'] = 1 - $v2['spec_discount'];
                        $v2['high_discount'] = 1 - $v2['spec_discount'];
                        $v2['cost_discount'] = 1 - $v2['spec_discount'];
                        $channels_info[$k2] = $v2;
                    }
                }
                $goods_info[$k]['channels_info'] = $channels_info;
                continue;
            }
            //商品预计完成档位折扣(乐天高价sku)
            if (isset($spec_high_info[$spec_sn])) {
                foreach ($spec_high_info[$spec_sn] as $k2 => $v2) {
                    $spec_discount = number_format($v2['spec_discount'], 4);
                    if (isset($channels_info[$k2])) {
                        $channels_info[$k2]['brand_discount'] = $spec_discount;
                        $channels_info[$k2]['high_discount'] = $spec_discount;
                        $channels_info[$k2]['is_high'] = 1;
                    } else {
                        $v2['brand_discount'] = $spec_discount;
                        $v2['high_discount'] = $spec_discount;
                        $v2['cost_discount'] = $spec_discount;
                        $v2['is_high'] = 1;
                        $channels_info[$k2] = $v2;
                    }
                }
            }
            //特价sku
            if (isset($spec_low_info[$spec_sn])) {
                foreach ($spec_low_info[$spec_sn] as $k1 => $v1) {
                    if (isset($channels_info[$k1])) {
                        $channels_info[$k1]['brand_discount'] -= floatval($v1['spec_discount']);
                    }
                }
            }
            if (!empty($channel_info)) {
                $sort_data = [];
                foreach ($channel_info as $k1 => $v1) {
                    $sort_data[] = $v1['brand_discount'];
                }
                array_multisort($sort_data, SORT_ASC, SORT_NUMERIC, $channel_info);
            }
            $goods_info[$k]['channels_info'] = $channels_info;
        }
        return $goods_info;
    }

    /**
     * description:获取品牌最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getBrandFinalDiscount($param)
    {
        $buy_time = $param['buy_time'];
        $channels_id = $param['channels_id'];
        $month_type_arr = $param['month_type_arr'];
        $brand_id_arr = $param['brand_id_arr'];
        $predict_type_arr = $param['predict_type_arr'];
        $param_c = [
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
            'brand_id_arr' => $brand_id_arr,
            'brand_type_id' => $month_type_arr,
        ];
        $brand_predict_info = $this->getBrandDiscount($param_c, 'type_cat');
        $bd_format_info = $bm_format_info = $bg_format_info = $bh_format_info = [];
        foreach ($brand_predict_info as $k => $v) {
            if (in_array($k, [2])) {//获取品牌预计完成档位折扣信息
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bd_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            } elseif (in_array($k, [4])) {//获取月度品牌活动追加
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bm_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            } elseif (in_array($k, [9])) {//获取品牌档位追加
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bg_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            } elseif (in_array($k, [10])) {//获取品牌HDW活动追加
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bh_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            }
        }
        $param_c = [
            'bd_format_info' => $bd_format_info,
            'bm_format_info' => $bm_format_info,
            'bg_format_info' => $bg_format_info,
            'bh_format_info' => $bh_format_info,
            'cost_list' => $param['cost_list'],
            'pay_list' => $param['pay_list'],
        ];
        $brand_final_discount = $this->createBrandFinalDiscount($param_c);
        return $brand_final_discount;
    }

    /**
     * description:组装品牌最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function createBrandFinalDiscount($param)
    {
        $bd_format_info = $param['bd_format_info'];
        $bm_format_info = $param['bm_format_info'];
        $bg_format_info = $param['bg_format_info'];
        $bh_format_info = $param['bh_format_info'];
        $cost_list = $param['cost_list'];
        $pay_list = $param['pay_list'];
        $total_discount_info = $cost_list;
        //增加交易支付折扣信息
        foreach ($total_discount_info as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $pay_discount = 0.00;
                if (isset($pay_list[$k][$k1])) {
                    $pay_discount = floatval($pay_list[$k][$k1]['brand_discount']);
                }
                $total_discount_info[$k][$k1]['pay_discount'] = $pay_discount;
            }
        }
        //品牌档位追加
        foreach ($bm_format_info as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if (isset($total_discount_info[$k][$k1])) {
                    $total_discount_info[$k][$k1]['brand_discount'] -= floatval($bm_format_info[$k][$k1]['brand_discount']);
                } else {
                    $total_discount_info[$k][$k1] = $v1;
                }
            }
        }
        //月度品牌活动追加
        foreach ($bg_format_info as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if (isset($total_discount_info[$k][$k1])) {
                    $total_discount_info[$k][$k1]['brand_discount'] -= floatval($bg_format_info[$k][$k1]['brand_discount']);
                } else {
                    $total_discount_info[$k][$k1] = $v1;
                }
            }
        }
        //品牌预计完成档位折扣
        foreach ($bd_format_info as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if (isset($total_discount_info[$k][$k1])) {
                    $old_discount = floatval($total_discount_info[$k][$k1]);
                    $new_discount = floatval($bd_format_info[$k][$k1]['brand_discount']);
                    if ($new_discount < $old_discount) {
                        $total_discount_info[$k][$k1]['brand_discount'] = $new_discount;
                    }
                } else {
                    $total_discount_info[$k][$k1] = $v1;
                }
            }
        }
        //品牌HDW活动追加
        foreach ($bh_format_info as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if (isset($total_discount_info[$k][$k1])) {
                    $old_discount = floatval($total_discount_info[$k][$k1]);
                    $new_discount = floatval($bh_format_info[$k][$k1]['brand_discount']);
                    if ($new_discount < $old_discount) {
                        $total_discount_info[$k][$k1]['brand_discount'] = $new_discount;
                    }
                } else {
                    $total_discount_info[$k][$k1] = $v1;
                }
            }
        }
        return $total_discount_info;
    }

    /**
     * description:获取当前品牌折扣数据
     * author:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getBrandDiscount($param, $group_str)
    {
        $type_arr = $param['brand_type_id'];
        $field = [
            'b.name', 'b.brand_id', 'b.name_en', 'pm.method_sn', 'pm.method_name', 'pm.method_property', 'pc.channels_sn',
            'pc.channels_name', 'd.shipment', 'pc.post_discount', 'pc.is_count_wai', 'pc.id as channels_id',
            'type_name', 'dt.discount as brand_discount', 'dt.discount as cost_discount', 'dt.type_id', 'dti.type_cat',
            'dti.add_type'
        ];

        $discount_obj = DB::table('discount_type as dt')->select($field)
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'd.brand_id')
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'd.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'd.channels_id')
            ->whereIn('dti.id', $type_arr)
            ->orderBy('dti.type_cat', 'ASC');
        if (!empty($param['buy_time'])) {
            $buy_time = trim($param['buy_time']);
            $discount_obj->where('dt.start_date', '<=', $buy_time);
            $discount_obj->where('dt.end_date', '>=', $buy_time);
        }
        if (!empty($param['brand_id_arr'])) {
            $brand_id_arr = $param['brand_id_arr'];
            $discount_obj->whereIn('d.brand_id', $brand_id_arr);
        }
        if (!empty($param['channels_id'])) {
            $channels_id = intval($param['channels_id']);
            $discount_obj->where('dti.channels_id', $channels_id);
        }
        $discount_info = $discount_obj->get();
        $discount_info = objectToArrayZ($discount_info);
        if (empty($discount_info)) {
            return $discount_info;
        };
        $format_discount_info = [];
        foreach ($discount_info as $k => $v) {
            $group_key = $v[$group_str];
            $pin_str = $v['channels_sn'] . '-' . $v['method_sn'];
            $type_id = $v['type_id'];
            if ($group_str == 'type_id') {
                $format_discount_info[$group_key][$v['brand_id']][$pin_str] = $v;
            } elseif ($group_str == 'type_cat') {
                $format_discount_info[$group_key][$v['brand_id']][$pin_str][$type_id] = $v;
            }
        }
        return $format_discount_info;
    }

    /**
     * 获取品牌折扣信息
     * @param $param
     * @param $group_str 分组字符
     * @return array|mixed
     */
    public function getBrandDiscountInfo($param, $group_str)
    {
        $type_arr = $param['brand_type_id'];
        $field = [
            'b.name', 'b.brand_id', 'b.name_en', 'pm.method_sn', 'pm.method_name', 'pm.method_property', 'pc.channels_sn',
            'pc.channels_name', 'd.shipment', 'pc.post_discount', 'pc.is_count_wai', 'pc.id as channels_id',
            'type_name', 'dt.discount as brand_discount', 'dt.discount as cost_discount', 'dt.type_id', 'dti.type_cat',
            'dti.add_type', 'pc.sort_num'
        ];

        $discount_obj = DB::table('discount_type as dt')->select($field)
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'd.brand_id')
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'd.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'd.channels_id')
            ->whereIn('dti.id', $type_arr)
            ->orderBy('pc.sort_num', 'ASC')
            ->orderBy('dti.type_cat', 'ASC');
        if (!empty($param['buy_time'])) {
            $buy_time = trim($param['buy_time']);
            $discount_obj->where('dt.start_date', '<=', $buy_time);
            $discount_obj->where('dt.end_date', '>=', $buy_time);
        }
        if (!empty($param['brand_id_arr'])) {
            $brand_id_arr = $param['brand_id_arr'];
            $discount_obj->whereIn('d.brand_id', $brand_id_arr);
        }
        if (!empty($param['channels_id'])) {
            $channels_id = intval($param['channels_id']);
            $discount_obj->where('dti.channels_id', $channels_id);
        }
        $discount_info = $discount_obj->get();
        $discount_info = objectToArrayZ($discount_info);
        if (empty($discount_info)) {
            return $discount_info;
        };
        $format_discount_info = [];
        foreach ($discount_info as $k => $v) {
            $group_key = $v[$group_str];
            $channels_id = $v['channels_id'];
            $type_id = $v['type_id'];
            if ($group_str == 'type_id') {
                $format_discount_info[$group_key][$v['brand_id']][$channels_id] = $v;
            } elseif ($group_str == 'type_cat') {
                $format_discount_info[$group_key][$v['brand_id']][$channels_id][$type_id] = $v;
            }
        }
        return $format_discount_info;
    }


    /**
     * description 组装免税店（DFS）折扣
     * editor zhangdong
     * date 2019.08.23
     */
    public function makeDiscountDFS($discount)
    {
        $abkIsset = isset($discount['channels_info'][$this->DFS_sn['abk']]['brand_discount']);//爱宝客
        $msg['abk_discount'] = $abkIsset ? $discount['channels_info'][$this->DFS_sn['abk']]['brand_discount'] : 0;
        $ltIsset = isset($discount['channels_info'][$this->DFS_sn['lt']]['brand_discount']);//乐天
        $msg['lt_discount'] = $ltIsset ? $discount['channels_info'][$this->DFS_sn['lt']]['brand_discount'] : 0;
        $xlIsset = isset($discount['channels_info'][$this->DFS_sn['xl']]['brand_discount']);//新罗
        $msg['xl_discount'] = $xlIsset ? $discount['channels_info'][$this->DFS_sn['xl']]['brand_discount'] : 0;
        return $msg;

    }

    /**
     * description 获取当前采购最终折扣数据
     * editor zongxing
     * date 2019.05.14
     * return Array
     * param:
     * $goods_info:[
     * {
     *      'spec_sn' => '1234',
     *      'brand_id' => '1234',
     * },
     * {
     *      'spec_sn' => '1234',
     *      'brand_id' => '1234',
     * },
     * ]//商品信息数组
     *  $param['buy_time']:购买时间
     */
    public function getCostAddDiscount_stop($goods_info, $param = [])
    {
        //收集商品的品牌id和spec_sn信息,主要是缩小查询品牌和商品时的范围
        $brand_id_arr = $spec_sn_arr = [];
        foreach ($goods_info as $k => $v) {
            $brand_id = intval($v['brand_id']);
            $spec_sn = trim($v['spec_sn']);
            if (!in_array($brand_id, $brand_id_arr)) {
                $brand_id_arr[] = $brand_id;
            }
            if (!in_array($spec_sn, $spec_sn_arr)) {
                $spec_sn_arr[] = $spec_sn;
            }
        }
        //定义获取折扣的时间信息
        if (empty($param['buy_time'])) {
            $start_date = Carbon::now()->firstOfMonth()->toDateString();
            $end_date = Carbon::now()->endOfMonth()->toDateString();
            $param['start_date'] = $start_date;
            $param['end_date'] = $end_date;
        }
        //查询折扣档位记录信息
        $dtr_model = new DiscountTypeRecordModel();
        $dtr_info = $dtr_model->getTotalDisTypeRedList($param);
        if (empty($dtr_info)) {
            return ['code' => '1003', 'msg' => '暂无折扣信息'];
        }
        $cost_type_arr = $month_type_arr = $predict_type_arr = $goods_cost_type_arr = $goods_predict_type_arr =
        $pay_id_arr = $cut_add_type_arr = [];
        foreach ($dtr_info as $k => $v) {
            if (!empty($v['cost_id'])) {
                $cost_type_arr = array_merge($cost_type_arr, explode(',', $v['cost_id']));//品牌成本折扣档位
            }
            if (!empty($v['goods_cost_id'])) {
                $goods_cost_type_arr = array_merge($goods_cost_type_arr, explode(',', $v['goods_cost_id']));//品牌成本折扣档位
            }
            if (!empty($v['cut_add_id'])) {
                $cut_add_type_arr = array_merge($cut_add_type_arr, explode(',', $v['cut_add_id']));//除成本折扣外，需要计算的折扣档位
            }
        }
        if (empty($cost_type_arr)) {
            return $goods_info;//如果不存在品牌成本折扣,则返回原商品数据
        }
        //获取品牌最终折扣
        $brand_total_arr = array_merge($cost_type_arr, $cut_add_type_arr);
        $param['brand_id_arr'] = $brand_id_arr;
        $param['brand_type_id'] = $brand_total_arr;
        $brand_final_discount = $this->makeBrandFinalDiscount($param);
        if ($brand_final_discount == false) {
            return $goods_info;//如果不存在品牌成本折扣,则返回原商品数据
        }
        //获取商品最终折扣
        $spec_total_arr = array_merge($goods_cost_type_arr, $cut_add_type_arr);
        $param['spec_sn_arr'] = $spec_sn_arr;
        $param['spec_type_id'] = $spec_total_arr;
        $gmc_model = new GmcDiscountModel();
        $spec_finnal_discount = $gmc_model->makeSpecFinalDiscount($param);
        $return_info = $this->makeFinalDiscount($goods_info, $brand_final_discount, $spec_finnal_discount);
        return $return_info;
    }

    public function getCostAddDiscount($goods_info, $param = [])
    {
        //收集商品的品牌id和spec_sn信息,主要是缩小查询品牌和商品时的范围
        $brand_id_arr = $spec_sn_arr = [];
        foreach ($goods_info as $k => $v) {
            $brand_id = intval($v['brand_id']);
            $spec_sn = trim($v['spec_sn']);
            if (!in_array($brand_id, $brand_id_arr)) {
                $brand_id_arr[] = $brand_id;
            }
            if (!in_array($spec_sn, $spec_sn_arr)) {
                $spec_sn_arr[] = $spec_sn;
            }
        }
        //定义获取折扣的时间信息
        if (empty($param['buy_time'])) {
            $start_date = Carbon::now()->firstOfMonth()->toDateString();
            $end_date = Carbon::now()->endOfMonth()->toDateString();
            $param['start_date'] = $start_date;
            $param['end_date'] = $end_date;
        }
        //查询折扣档位记录信息
        $dtr_model = new DiscountTypeRecordModel();
        $dtr_info = $dtr_model->getTotalDisTypeRedList($param);
        if (empty($dtr_info)) {
            return ['code' => '1003', 'msg' => '暂无折扣信息'];
        }
        $cost_type_arr = $month_type_arr = $predict_type_arr = $goods_cost_type_arr = $goods_predict_type_arr =
        $pay_id_arr = $cut_add_type_arr = [];
        foreach ($dtr_info as $k => $v) {
            if (!empty($v['cost_id'])) {
                $cost_type_arr = array_merge($cost_type_arr, explode(',', $v['cost_id']));//品牌成本折扣档位
            }
            if (!empty($v['goods_cost_id'])) {
                $goods_cost_type_arr = array_merge($goods_cost_type_arr, explode(',', $v['goods_cost_id']));//品牌成本折扣档位
            }
            if (!empty($v['cut_add_id'])) {
                $cut_add_type_arr = array_merge($cut_add_type_arr, explode(',', $v['cut_add_id']));//除成本折扣外，需要计算的折扣档位
            }
        }
        if (empty($cost_type_arr)) {
            return $goods_info;//如果不存在品牌成本折扣,则返回原商品数据
        }
        //获取品牌最终折扣
        $brand_total_arr = array_merge($cost_type_arr, $cut_add_type_arr);
        $param['brand_id_arr'] = $brand_id_arr;
        $param['brand_type_id'] = $brand_total_arr;
        $brand_final_discount = $this->makeBrandFinalDiscount($param);
        //dd($brand_final_discount);
        if ($brand_final_discount == false) {
            return $goods_info;//如果不存在品牌成本折扣,则返回原商品数据
        }
        //获取商品最终折扣
        $spec_total_arr = array_merge($goods_cost_type_arr, $cut_add_type_arr);
        $param['spec_sn_arr'] = $spec_sn_arr;
        $param['spec_type_id'] = $spec_total_arr;
        $gmc_model = new GmcDiscountModel();
        $spec_finnal_discount = $gmc_model->makeSpecFinalDiscount($param);
        $return_info = $this->makeFinalDiscount($goods_info, $brand_final_discount, $spec_finnal_discount);
        return $return_info;
    }

    /**
     * 组装最终折扣
     * author zongxing
     * date 2020/4/1
     * @param $goods_info 商品信息
     * @param $brand_final_discount 品牌最终折扣
     * @param $spec_finnal_discount 商品最终折扣
     * @return mixed
     */
    public function makeFinalDiscount($goods_info, $brand_final_discount, $spec_finnal_discount)
    {
        foreach ($goods_info as $k => $v) {
            $brand_id = $v['brand_id'];
            //如果没有品牌则折扣为空
            if (!isset($brand_final_discount[$brand_id])) {
                $goods_info[$k]['channels_info'] = [];
                continue;
            }
            //如果没有商品折扣，则折扣为品牌折扣
            $spec_sn = $v['spec_sn'];
            $channels_discount_info = $brand_final_discount[$brand_id];
            if (!isset($spec_finnal_discount[$spec_sn])) {
                $goods_info[$k]['channels_info'] = $channels_discount_info;
                continue;
            }
            //商品折扣追加计算
            $spec_discount_info = $spec_finnal_discount[$spec_sn];
            foreach ($channels_discount_info as $k1 => $v1) {
                if (isset($spec_discount_info[$k1])) {
                    $cost_discount = $v1['cost_discount'];
                    $discount_info = $spec_discount_info[$k1];
                    $spec_discount = $discount_info['spec_discount'];
                    $add_type = $discount_info['add_type'];
                    $final_discount = discountModify($add_type, $cost_discount, $spec_discount);
                    if ($final_discount != $cost_discount) {
                        $channels_discount_info[$k1]['cost_discount'] = $final_discount;
                        $channels_discount_info[$k1]['add_type'] = $add_type;
                        $channels_discount_info[$k1]['type_name'] = $discount_info['type_name'];
                        $channels_discount_info[$k1]['type_cat'] = $discount_info['type_cat'];
                        $channels_discount_info[$k1]['type_id'] = $discount_info['type_id'];
                    }
                };
            }
            $goods_info[$k]['channels_info'] = $channels_discount_info;
        }
        return $goods_info;
    }

    /**
     * 获取品牌最终折扣
     * @param $param
     * @return array|bool
     */
    public function makeBrandFinalDiscount($param)
    {
        //获取品牌折扣信息
        $brand_discount_list = $this->getBrandDiscountInfo($param, 'type_id');
        if (empty($brand_discount_list)) {
            return false;//如果无折扣信息,则返回原商品数据
        }
//        dd($param,$brand_discount_list);
        //计算品牌最终折扣
        $brand_final_list = [];
        foreach ($brand_discount_list as $k => $v) {
            foreach ($v as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    if (isset($brand_final_list[$k1][$k2])) {
                        $add_type = $v2['add_type'];
                        $old_disount = $brand_final_list[$k1][$k2]['cost_discount'];
                        $new_discount = $v2['cost_discount'];
                        $final_discount = discountModify($add_type, $old_disount, $new_discount);
                        if ($final_discount != $old_disount) {
                            $brand_final_list[$k1][$k2]['cost_discount'] = $final_discount;
                            $brand_final_list[$k1][$k2]['add_type'] = $add_type;
                            $brand_final_list[$k1][$k2]['type_name'] = $v2['type_name'];
                            $brand_final_list[$k1][$k2]['type_cat'] = $v2['type_cat'];
                            $brand_final_list[$k1][$k2]['type_id'] = $v2['type_id'];
                        }
                    } else {
                        $brand_final_list[$k1][$k2] = $v2;
                    }
                }
            }
        }
        return $brand_final_list;
    }

}//end of class
