<?php

namespace App\Model\Vone;

use App\Modules\ParamsSet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GmcDiscountModel extends Model
{
    protected $table = 'gmc_discount as gd';

    //可操作字段
    protected $field = ['gd.id', 'gd.spec_sn', 'gd.method_id', 'gd.channels_id', 'gd.type_id', 'gd.discount'];

    //修改laravel 自动更新
    const UPDATED_AT = "modify_time";
    const CREATED_AT = "create_time";

    /**
     * description:商品渠道追加折扣维护
     * editor:zongxing
     * date : 2019.05.20
     * return Array
     */
    public function uploadGmcDiscountBySpec($upload_goods_info, $dti_info, $param_info)
    {
        //获取商品规格码信息
        $spec_sn_arr = $channels_id_arr = [];
        foreach ($upload_goods_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            if (!in_array($spec_sn, $spec_sn_arr)) {
                $spec_sn_arr[] = $spec_sn;
            }
        }
        foreach ($dti_info as $k => $v) {
            $channels_id = $v['channels_id'];
            if (!in_array($channels_id, $channels_id_arr)) {
                $channels_id_arr[] = $channels_id;
            }
        }
        //获取所选渠道已经存的商品特殊折扣
        $start_date = trim($param_info['start_date']);
        $end_date = trim($param_info['end_date']);
        $type_arr = explode(',', $param_info['type_id']);
        $param = [
            'type_arr' => $type_arr,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        $gmc_discount_info = $this->gmcDiscountList($spec_sn_arr, $param);
        $gmc_discount_list = [];
        foreach ($gmc_discount_info as $k => $v) {
            $gmc_discount_list[$v['spec_sn']][$v['type_id']] = $v;
        }
        //获取已经存在的最低折扣记录
        $now_month = date('Y-m');
        $param = [
            'discount_month' => $now_month,
            'spec_sn_arr' => $spec_sn_arr,
            'channels_id_arr' => $channels_id_arr,
        ];
        $discountRecordModel = new DiscountRecordModel();
        $discountRecordInfo = $discountRecordModel->getDiscountRecordInfo($param);
        $discountRecordList = [];
        foreach ($discountRecordInfo as $v) {
            $pin_str = $v['spec_sn'] . '_' . $v['discount_month'] . '_' . $v['channels_id'];
            $discountRecordList[$pin_str] = $v;
        }
        //组装新增和更新数据
        $insertDiscount = $insertDiscountRecordInfo = $updateDiscountRecordInfo = [];
        foreach ($upload_goods_info as $k => $v) {
            $spec_sn = trim($v['spec_sn']);
            $erp_prd_no = trim($v['erp_prd_no']);
            $new_discount = $discount = floatval($v['discount']);
            foreach ($dti_info as $k1 => $v1) {
                $type_id = intval($v1['id']);
                $channels_id = intval($v1['channels_id']);
                if (isset($gmc_discount_list[$spec_sn][$type_id])) {
                    //gmc_discount表更新数据
                    $id = $gmc_discount_list[$spec_sn][$type_id]['id'];
                    if ($discount != floatval($gmc_discount_list[$spec_sn][$type_id]['discount'])) {
                        $updateGmcDiscount['discount'][][$id] = $discount;
                    }
                } else {
                    //gmc_discount表新增数据
                    $method_id = intval($v1['method_id']);
                    $insertDiscount[] = [
                        'spec_sn' => $spec_sn,
                        'erp_prd_no' => $erp_prd_no,
                        'method_id' => $method_id,
                        'channels_id' => $channels_id,
                        'discount' => $discount,
                        'type_id' => $type_id,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                    ];
                }

                $pin_str = $spec_sn . '_' . $now_month . '_' . $channels_id;
                $add_type = $v1['add_type'];
                //本月当前渠道不存在该商品对应的折扣记录
                if (!isset($discountRecordList[$pin_str])) {
                    $insertDiscountRecordInfo[$pin_str] = [
                        'spec_sn' => $spec_sn,
                        'discount_month' => $now_month,
                        'channels_id' => $channels_id,
                    ];
                    //记录最低折扣：1,折扣替换 3,折扣取反（例如：高价sku返点）
                    if ($add_type == 1 || $add_type == 3) {
                        $lowest_discount = $add_type == 3 ? 1 - $new_discount : $new_discount;
                        $insertDiscountRecordInfo[$pin_str]['discount'] = $lowest_discount;
                        //记录追加点：2,折扣累减 4,折扣累加
                    } elseif ($add_type == 2 || $add_type == 4) {
                        $lowest_discount = $add_type == 2 ? -$new_discount : $new_discount;
                        $insertDiscountRecordInfo[$pin_str]['add_point'] = $lowest_discount;
                    }
                    continue;
                }
                //本月当前渠道已经存在该商品对应的折扣记录
                $old_discount = $discountRecordList[$pin_str]['discount'];
                $updateId = $discountRecordList[$pin_str]['id'];
                $add_point = $discountRecordList[$pin_str]['add_point'];
                if ($add_type == 1 || $add_type == 3) {
                    $lowest_discount = $add_type == 3 ? 1 - $new_discount : $new_discount;
                    $final_discount = $lowest_discount + $add_point;
                    if ($final_discount >= $old_discount) continue;
                    $updateDiscountRecordInfo['discount'][] = [
                        $updateId => $lowest_discount
                    ];
                } elseif ($add_type == 2 || $add_type == 4) {
                    $new_add_discount = $add_type == 2 ? -$new_discount : $new_discount;
                    if ($new_add_discount >= $add_point) continue;//如果本次维护的追加点大于等于记录表中存在的追加点，则无效
                    $lowest_discount = $old_discount - ($add_point - $new_add_discount);
                    $updateDiscountRecordInfo['discount'][] = [
                        $updateId => $lowest_discount
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
        //组装更新sql
        $updateGmcDiscountSql = '';
        if (!empty($updateGmcDiscount)) {
            //需要判断的字段
            $column = 'id';
            $updateGmcDiscountSql = makeBatchUpdateSql('jms_gmc_discount', $updateGmcDiscount, $column);
        }
        $res = DB::transaction(function () use (
            $insertDiscount, $updateGmcDiscountSql, $insertDiscountRecordInfo,
            $updateDiscountRecordInfoSql
        ) {
            //gmc_discount表更新数据
            $res = 1;
            //插入当月折扣记录表数据
            if (!empty($insertDiscountRecordInfo)) {
                $res = DB::table('discount_record')->insert($insertDiscountRecordInfo);
            }
            //更新当月折扣记录表数据
            if (!empty($updateDiscountRecordInfoSql)) {
                $res = DB::update(DB::raw($updateDiscountRecordInfoSql));
            }
            if (!empty($updateGmcDiscountSql)) {
                $res = DB::update(DB::raw($updateGmcDiscountSql));
            }
            //gmc_discount表新增数据
            if (!empty($insertDiscount)) {
                $res = DB::table('gmc_discount')->insert($insertDiscount);
            }
            return $res;
        });
        return $res;
    }

    /**
     * description:商品渠道追加折扣维护
     * editor:zongxing
     * date : 2019.05.20
     * return Array
     */
    public function uploadGmcDiscountByBrand($res, $dti_info)
    {
        $upload_info = [];
        foreach ($res as $k => $v) {
            if ($k == 0) continue;
            $brand_id = trim($v[0]);
            $upload_info[$brand_id] = floatval($v[1]);
        }
        //获取商品信息
        $brand_info = array_keys($upload_info);
        $gs_model = new GoodsSpecModel();
        $goods_info = $gs_model->getSpecByBrand($brand_info);
        $spec_info = [];
        foreach ($goods_info as $k => $v) {
            $spec_info[] = $v['spec_sn'];
            $brand_id = $v['brand_id'];
            $goods_info[$k]['discount'] = $upload_info[$brand_id];
        }
        $method_id = intval($dti_info[0]['method_id']);
        $channels_id = intval($dti_info[0]['channels_id']);
        $type_id = intval($dti_info[0]['id']);
        //获取已经存的商品特殊折扣
        $param = [
            'method_id' => $method_id,
            'channels_id' => $channels_id,
            'type_id' => $type_id,
        ];
        $gmc_discount_info = $this->gmcDiscountList($spec_info, $param);
        $gmc_discount_list = [];
        if (!empty($gmc_discount_info)) {
            foreach ($gmc_discount_info as $k => $v) {
                $gmc_discount_list[$v['spec_sn']] = $v;
            }
        }
        $insertDiscount = [];
        foreach ($goods_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            $discount = number_format($v['discount'], 4);
            if (isset($gmc_discount_list[$spec_sn])) {
                //gmc_discount表更新数据
                $id = $gmc_discount_list[$spec_sn]['id'];
                $updateGmcDiscount['discount'][][$id] = $discount;
            } else {
                //gmc_discount表新增数据
                $insertDiscount[] = [
                    'spec_sn' => $spec_sn,
                    'method_id' => $method_id,
                    'channels_id' => $channels_id,
                    'discount' => $discount,
                    'type_id' => $type_id,
                ];
            }
        }
        $updateGmcDiscountSql = '';
        if (!empty($updateGmcDiscount)) {
            //需要判断的字段
            $column = 'id';
            $updateGmcDiscountSql = makeBatchUpdateSql('jms_gmc_discount', $updateGmcDiscount, $column);
        }
        $res = DB::transaction(function () use ($insertDiscount, $updateGmcDiscountSql) {
            $res = true;
            //gmc_discount表更新数据
            if (!empty($updateGmcDiscountSql)) {
                $res = DB::update(DB::raw($updateGmcDiscountSql));
            }
            //gmc_discount表新增数据
            if (!empty($insertDiscount)) {
                $res = DB::table('gmc_discount')->insert($insertDiscount);
            }
            return $res;
        });
        return $res;
    }

    /**
     * description:获取商品渠道追加折扣
     * editor:zongxing
     * date : 2019.05.21
     * return Array
     */
    public function gmcDiscountList($sum_spec_info = [], $param_info = [], $type_cat_arr = [])
    {
        //商品相关搜索条件整理
        $specSnArr = $param = [];
        if (!empty($param_info['goods_name'])) {
            $goods_name = '%' . trim($param_info['goods_name']) . '%';
            $param['goods_name'] = $goods_name;
        }
        if (!empty($param_info['spec_sn'])) {
            $spec_sn = trim($param_info['spec_sn']);
            $where[] = ['gs.spec_sn', $spec_sn];
            $param['spec_sn'][] = $spec_sn;
        }
        if (!empty($param_info['erp_merchant_no'])) {
            $erp_merchant_no = trim($param_info['erp_merchant_no']);
            $where[] = ['gs.erp_merchant_no', $erp_merchant_no];
            $param['erp_merchant_no'][] = $erp_merchant_no;
        }
        if (!empty($param_info['erp_ref_no'])) {
            $erp_ref_no = trim($param_info['erp_ref_no']);
            $where[] = ['gs.erp_ref_no', $erp_ref_no];
            $param['erp_ref_no'][] = $erp_ref_no;
        }
        if (isset($param_info['erp_prd_no'])) {
            $erp_prd_no = trim($param_info['erp_prd_no']);
            $where[] = ['gs.erp_prd_no', $erp_prd_no];
            $param['erp_prd_no'][] = $erp_prd_no;
        }
        $goodsSpecModel = new GoodsSpecModel();
        if (!empty($param)) {
            $goodsSpecList = $goodsSpecModel->getGoodsSpecList($param, 'spec_sn');
            $specSnArr = array_keys($goodsSpecList);
        }
        //获取渠道信息
        $purchaseChannelModel = new PurchaseChannelModel();
        $purchaseChannelList = $purchaseChannelModel->getChannelsArr();
        //商品相关折扣信息
        $field = ['gd.id', 'gd.spec_sn', 'gd.start_date', 'gd.end_date', 'gd.method_id', 'gd.channels_id', 'gd.type_id',
            'gd.discount', 'dti.type_name'];
        $gmc_discount_obj = DB::table($this->table)
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'gd.type_id');
        if (!empty($sum_spec_info)) {
            $gmc_discount_obj->whereIn('gd.spec_sn', $sum_spec_info);
        }
        if (!empty($specSnArr)) {
            $add_field = ['g.goods_name', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.erp_ref_no', 'gs.erp_prd_no'];
            $field = array_merge($field, $add_field);
            $gmc_discount_obj->leftJoin('goods_spec as gs', 'gs.spec_sn', '=', 'gd.spec_sn')
                ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
                ->whereIn('gd.spec_sn', $specSnArr);
        }
        if (isset($param_info['method_id'])) {
            $method_id = intval($param_info['method_id']);
            $gmc_discount_obj->where('gd.method_id', $method_id);
        }
        if (isset($param_info['channels_id'])) {
            $channels_id = intval($param_info['channels_id']);
            $gmc_discount_obj->where('gd.channels_id', $channels_id);
        }
        if (isset($param_info['type_id'])) {
            $type_id = intval($param_info['type_id']);
            $gmc_discount_obj->where('gd.type_id', $type_id);
        }
        if (isset($param_info['type_arr'])) {
            $type_arr = $param_info['type_arr'];
            $gmc_discount_obj->whereIn('gd.type_id', $type_arr);
        }
        if (!empty($type_cat_arr)) {
            $gmc_discount_obj->whereIn('dti.type_cat', $type_cat_arr);
        }
        if (isset($param_info['start_date'])) {
            $start_date = date('Y-m-d', strtotime(trim($param_info['start_date'])));
            $gmc_discount_obj->where('gd.start_date', '=', $start_date);
        }
        if (isset($param_info['end_date'])) {
            $end_date = date('Y-m-d', strtotime(trim($param_info['end_date'])));
            $gmc_discount_obj->where('gd.end_date', '=', $end_date);
        }
        if (isset($param_info['limit'])) {
            $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
            $gmc_discount_info = $gmc_discount_obj->select($field)->orderBy('gd.create_time', 'desc')->paginate($page_size);
        } else {
            $gmc_discount_info = $gmc_discount_obj->get();
        }
        $gmc_discount_info = objectToArrayZ($gmc_discount_info);
        //组装最终商品追加折扣数据
        $param = compact('gmc_discount_info', 'specSnArr', 'purchaseChannelList');
        $gmcDiscountList = $this->createGmcDiscountList($param);
        return $gmcDiscountList;
    }

    /**
     * description
     * author zongxing
     * date 2020/5/14 0014
     * @param $param
     * @return mixed
     */
    public function createGmcDiscountList($param)
    {
        $gmc_discount_info = $param['gmc_discount_info'];
        $specSnArr = $param['specSnArr'];
        $purchaseChannelList = $param['purchaseChannelList'];
        $goodsSpecModel = new GoodsSpecModel();
        $gmc_discount_list = $gmc_discount_info;
        if (isset($gmc_discount_info['data'])) {
            $gmc_discount_list = $gmc_discount_info['data'];
        }
        if (empty($specSnArr)) {
            foreach ($gmc_discount_list as $k => $v) {
                $specSnArr[] = $v['spec_sn'];
            }
            //获取商品基础信息
            $param['spec_sn'] = $specSnArr;
            $goodsSpecList = $goodsSpecModel->getGoodsSpecList($param, 'spec_sn');
            foreach ($gmc_discount_list as $k => $v) {
                $spec_sn = $v['spec_sn'];
                $channels_id = $v['channels_id'];
                if (isset($goodsSpecList[$spec_sn])){
                    $gmc_discount_list[$k] = array_merge($gmc_discount_list[$k],$goodsSpecList[$spec_sn]);
                }
                if (isset($purchaseChannelList[$channels_id])){
                    $gmc_discount_list[$k]['channels_name'] = $purchaseChannelList[$channels_id]['channels_name'];
                    $gmc_discount_list[$k]['method_name'] = $purchaseChannelList[$channels_id]['method_name'];
                }
            }
        }
        if (isset($gmc_discount_info['data'])) {
            $gmc_discount_info['data'] = $gmc_discount_list;
        }
        return $gmc_discount_info;
    }


    /**
     * description 获取商品档位折扣数
     * author zhangdong
     * date 2019.07.23
     */
    public function getGoodsGearNum($param_info)
    {
        $start_date = date('Y-m-d', strtotime(trim($param_info['start_date'])));
        $end_date = date('Y-m-d', strtotime(trim($param_info['end_date'])));
        $channelId = intval($param_info['channels_id']);
        $where = [
            ['start_date', '<=', $start_date],
            ['end_date', '>=', $end_date],
            ['channels_id', '=', $channelId],
            ['type_cat', '=', 2],
        ];
        $gears_num = DB::table('discount_type as dt')
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->where($where)
            ->distinct()->get(['dti.id', 'dti.type_name'])->count();
        return $gears_num;
    }

    /**
     * description 获取当月真实的档位对应商品的折扣
     * author zongxing
     * date 2019.08.19
     */
    public function getMonthGearPoints($type_id, $param_info)
    {
        $start_date = date('Y-m-d', strtotime(trim($param_info['start_date'])));
        $end_date = date('Y-m-d', strtotime(trim($param_info['end_date'])));
        $channelId = intval($param_info['channels_id']);
        $where = [
            ['gd.start_date', '>=', $start_date],
            ['gd.end_date', '>=', $start_date],
            ['gd.start_date', '<=', $end_date],
            ['gd.end_date', '<=', $end_date],
            ['gd.channels_id', '=', $channelId],
        ];
        $queryRes = DB::table($this->table)
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'gd.type_id')
            ->where($where)
            ->whereIn('gd.type_id', $type_id)
            ->get(['gd.start_date', 'gd.end_date', 'discount', 'spec_sn', 'type_cat']);
        $queryRes = objectToArrayZ($queryRes);
        return $queryRes;
    }

    /**
     * description:获取商品最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getSpecFinalDiscount($param)
    {
        $spec_sn_arr = $param['spec_sn_arr'];
        $channels_id = $param['channels_id'];
        $buy_time = $param['buy_time'];
        $goods_cost_type_arr = $param['goods_cost_type_arr'];
        $goods_predict_type_arr = $param['goods_predict_type_arr'];
        //获取商品预计完成档位折扣信息
        $start_date = Carbon::now()->firstOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();
        //获取商品成本折扣档位信息
        $param = [
            'type_cat' => 12,
            'spec_type_id' => $goods_cost_type_arr,
            'spec_sn_arr' => $spec_sn_arr,
            'channels_id' => $channels_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        $spec_cost_info = $this->getSpecDiscount($param);
        $sc_format_info = [];
        foreach ($spec_cost_info as $k => $v) {
            $pin_str = $v['channels_sn'] . '-' . $v['method_sn'];
            $sc_format_info[$v['spec_sn']][$pin_str] = $v;
        }
        //获取商品预计完成档位折扣信息
        $param = [
            'type_cat' => 12,
            'predict_type_arr' => $goods_predict_type_arr,
            'spec_sn_arr' => $spec_sn_arr,
            'channels_id' => $channels_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        $spec_gear_info = $this->getSpecDiscount($param);
        $sg_format_info = [];
        foreach ($spec_gear_info as $k => $v) {
            $pin_str = $v['channels_sn'] . '-' . $v['method_sn'];
            $sg_format_info[$v['spec_sn']][$pin_str] = $v;
        }
        //获取高价sku折扣
        $param = [
            'type_cat' => 6,
            'spec_sn_arr' => $spec_sn_arr,
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
        ];
        $spec_high_info = $this->getSpecDiscount($param);
        $sh_format_info = [];
        foreach ($spec_high_info as $k => $v) {
            $pin_str = $v['channels_sn'] . '-' . $v['method_sn'];
            $sh_format_info[$v['spec_sn']][$pin_str] = $v;
        }
        //获取低价sku追加点
        $param = [
            'type_cat' => 7,
            'spec_sn_arr' => $spec_sn_arr,
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
        ];
        $spec_low_info = $this->getSpecDiscount($param);
        $sl_format_info = [];
        foreach ($spec_low_info as $k => $v) {
            $pin_str = $v['channels_sn'] . '-' . $v['method_sn'];
            $sl_format_info[$v['spec_sn']][$pin_str] = $v;
        }
        //获取sku团队专属时段最终返点
        $param = [
            'type_cat' => 13,
            'spec_sn_arr' => $spec_sn_arr,
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
        ];
        $spec_team_info = $this->getSpecDiscount($param);
        $st_format_info = [];
        foreach ($spec_team_info as $k => $v) {
            $pin_str = $v['channels_sn'] . '-' . $v['method_sn'];
            $st_format_info[$v['spec_sn']][$pin_str] = $v;
        }
        $param = [
            'sc_format_info' => $sc_format_info,
            'sg_format_info' => $sg_format_info,
            'sh_format_info' => $sh_format_info,
            'sl_format_info' => $sl_format_info,
            'st_format_info' => $st_format_info,
        ];
        $spec_final_discount = $this->createSpecFinalDiscount($param);
        return $spec_final_discount;
    }

    /**
     * 获取商品最终折扣
     * @param $param
     * @return array
     */
    public function makeSpecFinalDiscount($param)
    {
        //获取商品折扣信息
        $spec_discount_info = $this->getSpecDiscount($param);
        //计算品牌最终折扣
        $spec_final_list = [];
        foreach ($spec_discount_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            $channels_id = $v['channels_id'];
            if (isset($spec_final_list[$spec_sn][$channels_id])) {
                $add_type = $v['add_type'];
                $old_disount = $spec_final_list[$spec_sn][$channels_id]['spec_discount'];
                $new_discount = $v['spec_discount'];
                $final_discount = discountModify($add_type, $old_disount, $new_discount);
                if ($final_discount != $old_disount) {
                    $spec_final_list[$spec_sn][$channels_id]['spec_discount'] = $final_discount;
                    $spec_final_list[$spec_sn][$channels_id]['add_type'] = $add_type;
                    $spec_final_list[$spec_sn][$channels_id]['type_name'] = $v['type_name'];
                }
            } else {
                $spec_final_list[$spec_sn][$channels_id] = $v;
            }
        }
        return $spec_final_list;
    }

    /**
     * description:组装商品最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function createSpecFinalDiscount($param)
    {
        $sc_format_info = $param['sc_format_info'];
        $sg_format_info = $param['sg_format_info'];
        $sh_format_info = $param['sh_format_info'];
        $sl_format_info = $param['sl_format_info'];
        $st_format_info = $param['st_format_info'];

        $spec_high_info = [];
        if (!empty($sg_format_info)) {
            foreach ($sg_format_info as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $brand_discount = 1 - floatval($v1['spec_discount']);
                    $v1['spec_discount'] = $brand_discount;
                    $spec_high_info[$k][$k1] = $v1;
                }
            }
        }
        if (!empty($sh_format_info)) {
            foreach ($sh_format_info as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    $brand_discount = 1 - floatval($v1['spec_discount']);
                    if (isset($spec_high_info[$k][$k1])) {
                        $old_brand_discount = floatval($spec_high_info[$k][$k1]['spec_discount']);
                        $final_discount = $old_brand_discount > $brand_discount ? $brand_discount : $old_brand_discount;
                        $spec_high_info[$k][$k1]['spec_discount'] = $final_discount;
                    } else {
                        $v1['spec_discount'] = $brand_discount;
                        $spec_high_info[$k][$k1] = $v1;
                    }
                }
            }
        }
        $spec_info = [
            'spec_cost_info' => $sc_format_info,
            'spec_high_info' => $spec_high_info,
            'spec_low_info' => $sl_format_info,
            'spec_team_info' => $st_format_info,
        ];
        return $spec_info;
    }

    /**
     * description:获取商品相关折扣信息
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getSpecTotalDiscount($param)
    {
        $start_date = Carbon::now()->firstOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();
        $field = [
            'gd.spec_sn', 'pm.method_sn', 'pm.method_name', 'pc.channels_sn', 'pc.channels_name', 'dti.type_name',
            'dti.add_type', 'dti.type_cat', 'dti.channels_id', 'gd.type_id', 'gd.discount', 'dc.cat_name', 'dc.cat_code'
        ];
        $spec_discount_obj = DB::table('gmc_discount as gd')
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'gd.type_id')
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'gd.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'gd.channels_id')
            ->leftJoin('discount_cat as dc', 'dc.id', '=', 'dti.type_cat');

        if (!empty($param['buy_time'])) {
            $buy_time = $param['buy_time'];
            $spec_discount_obj->where('gd.start_date', '<=', $buy_time);
            $spec_discount_obj->where('gd.end_date', '>=', $buy_time);
        } elseif (!empty($param['start_date'])) {
            $spec_discount_obj->where('gd.start_date', '<=', $start_date);
        } elseif (!empty($param['end_date'])) {
            $spec_discount_obj->where('gd.end_date', '>=', $end_date);
        } else {
            $spec_discount_obj->where('gd.start_date', '<=', $start_date);
            $spec_discount_obj->where('gd.end_date', '>=', $end_date);
        }
        if (isset($param['spec_sn_arr'])) {
            $spec_sn_arr = $param['spec_sn_arr'];
            $spec_discount_obj->whereIn('gd.spec_sn', $spec_sn_arr);
        }
        if (isset($param['type_cat'])) {
            $type_cat = intval($param['type_cat']);
            $spec_discount_obj->where('dti.type_cat', $type_cat);
        }
        if (isset($param['predict_type_arr'])) {
            $predict_type_arr = $param['predict_type_arr'];
            $spec_discount_obj->whereIn('dti.id', $predict_type_arr);
        }
        if (isset($param['type_id_arr'])) {
            $type_id_arr = $param['type_id_arr'];
            $spec_discount_obj->whereIn('gd.type_id', $type_id_arr);
        }
        if (!empty($param['channels_id'])) {
            $channels_id = intval($param['channels_id']);
            $spec_discount_obj->where('dti.channels_id', $channels_id);
        }
        $spec_discount_info = $spec_discount_obj->orderBy('gd.discount', 'ASC')->get($field);
        $spec_discount_info = objectToArrayZ($spec_discount_info);
        return $spec_discount_info;
    }

    /**
     * description:获取商品最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getSpecDiscount($param)
    {
        $start_date = Carbon::now()->firstOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();
        $field = [
            'pm.method_sn', 'pm.method_name', 'pm.method_property', 'pc.channels_sn',
            'pc.channels_name', 'pc.is_count_wai', 'gd.discount as spec_discount',
            'type_name', 'gd.spec_sn', 'dti.add_type', 'dti.type_cat', 'gd.type_id', 'dti.channels_id'
        ];
        $spec_discount_obj = DB::table('gmc_discount as gd')
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'gd.type_id')
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'gd.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'gd.channels_id');

        if (!empty($param['buy_time'])) {
            $buy_time = $param['buy_time'];
            $spec_discount_obj->where('gd.start_date', '<=', $buy_time);
            $spec_discount_obj->where('gd.end_date', '>=', $buy_time);
        } elseif (!empty($param['start_date'])) {
            $spec_discount_obj->where('gd.start_date', '<=', $start_date);
        } elseif (!empty($param['end_date'])) {
            $spec_discount_obj->where('gd.end_date', '>=', $end_date);
        } else {
            $spec_discount_obj->where('gd.start_date', '<=', $start_date);
            $spec_discount_obj->where('gd.end_date', '>=', $end_date);
        }
        if (isset($param['spec_sn_arr'])) {
            $spec_sn_arr = $param['spec_sn_arr'];
            $spec_discount_obj->whereIn('gd.spec_sn', $spec_sn_arr);
        }
        if (isset($param['type_cat'])) {
            $type_cat = intval($param['type_cat']);
            $spec_discount_obj->where('dti.type_cat', $type_cat);
        }
        if (isset($param['predict_type_arr'])) {
            $predict_type_arr = $param['predict_type_arr'];
            $spec_discount_obj->whereIn('dti.id', $predict_type_arr);
        }
        if (isset($param['spec_type_id'])) {
            $spec_type_id = $param['spec_type_id'];
            $spec_discount_obj->whereIn('gd.type_id', $spec_type_id);
        }
        if (!empty($param['channels_id'])) {
            $channels_id = intval($param['channels_id']);
            $spec_discount_obj->where('dti.channels_id', $channels_id);
        }
        $spec_discount_info = $spec_discount_obj->orderBy('gd.discount', 'ASC')->get($field);
        $spec_discount_info = objectToArrayZ($spec_discount_info);
        return $spec_discount_info;
    }

    /**
     * description 获取需要报价的商品规格码
     * author zhangdong
     * date 2019.10.21
     * update zhangdong 2019.12.18
     */
    public function getOfferSpecSn($offerMsg)
    {
        $lowPriceId = $offerMsg['lowPriceId'];
        $generateDate = ParamsSet::getGenerateDate();
        $channelId = ParamsSet::getChannelId();
        $field = ['spec_sn'];
        $where = [
            ['start_date', $generateDate],
            ['end_date', $generateDate],
            ['type_id', $lowPriceId],
            ['channels_id', $channelId],
        ];
        $queryRes = DB::table($this->table)->select($field)->where($where)->pluck('spec_sn');
        return $queryRes;
    }

    /**
     * description 获取报价相关的信息
     * author zhangdong
     * date 2019.12.19
     */
    public function getRelateOffer()
    {
        $channelId = ParamsSet::getChannelId();
        $dtiModel = new DiscountTypeInfoModel();
        $lowPriceId = $dtiModel->getLowPriceId($channelId);
        $dtrModel = new DiscountTypeRecordModel();
        $offerMsg = $dtrModel->getOfferMsg($channelId);
        $offerId = isset($offerMsg->offer_id) ? intval($offerMsg->offer_id) : 0;
        if ($lowPriceId == 0 || $offerId == 0) {
            return false;
        }
        //offer_id和goods_offer_id是并存关系，所以$highPriceId无需在上面做出判断
        $highPriceId = intval($offerMsg->goods_offer_id);
        return [
            'offerId' => $offerId,
            'lowPriceId' => $lowPriceId,
            'highPriceId' => $highPriceId,
        ];

    }


}//end of class
