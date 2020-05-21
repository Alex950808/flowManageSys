<?php

namespace App\Model\Vone;

use App\Modules\ParamsSet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DiscountTypeRecordModel extends Model
{
    protected $table = 'discount_type_record as dtr';
    protected $field = ['dtr.id', 'dtr.start_date', 'dtr.end_date', 'dtr.method_id', 'dtr.channels_id', 'dtr.base_id',
        'dtr.cost_id', 'dtr.predict_id', 'dtr.goods_cost_id', 'dtr.goods_predict_id', 'dtr.month_type_id',
        'dtr.brand_month_predict_id', 'dtr.pay_id', 'dtr.offer_id', 'dtr.goods_offer_id', 'dtr.cut_add_id'];

    /**
     * description 获取折扣类型记录信息
     * editor zongxing
     * date 2019.09.02
     * return Array
     */
    public function getDiscountTypeRecordInfo($param_info)
    {
        $start_date = $param_info['start_date'];
        $end_date = $param_info['end_date'];
        $where = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
        if (!empty($param_info['channels_id'])) {
            $where['channels_id'] = intval($param_info['channels_id']);
        }
        if (!empty($param_info['team_id'])) {
            $where['team_id'] = intval($param_info['team_id']);
        }
        $dtr_info = DB::table($this->table)->where($where)->first();
        $dtr_info = objectToArrayZ($dtr_info);
        if (!empty($dtr_info)) {
            $dtr_info['cost_arr'] = explode(',', $dtr_info['cost_id']);
            $dtr_info['predict_arr'] = explode(',', $dtr_info['predict_id']);
            $dtr_info['goods_cost_arr'] = explode(',', $dtr_info['goods_cost_id']);
            $dtr_info['goods_predict_arr'] = explode(',', $dtr_info['goods_predict_id']);
            $dtr_info['month_type_arr'] = explode(',', $dtr_info['month_type_id']);
            $dtr_info['brand_month_predict_arr'] = explode(',', $dtr_info['brand_month_predict_id']);
            $dtr_info['pay_id'] = explode(',', $dtr_info['pay_id']);
        }
        return $dtr_info;
    }

    /**
 * description 获取折扣类型记录列表
 * editor zongxing
 * date 2019.09.02
 * return Array
 */
    public function getDiscountTypeRecordList($param_info = [])
    {
        $field = $this->field;
        $field = array_merge($field, ['pm.method_name', 'pc.channels_name', 'pc.original_or_discount']);
        $where = [];
        if (!empty($param_info['buy_time'])) {
            $buy_time = trim($param_info['buy_time']);
            $where[] = ['dtr.start_date', '<=', $buy_time];
            $where[] = ['dtr.end_date', '>=', $buy_time];
        } else {
            if (!empty($param_info['start_date'])) {
                $start_date = trim($param_info['start_date']);
                $where[] = ['dtr.start_date', '>=', $start_date];
            }
            if (!empty($param_info['end_date'])) {
                $end_date = trim($param_info['end_date']);
                $where[] = ['dtr.end_date', '<=', $end_date];
            }
        }
        if (!empty($param_info['method_name'])) {
            $method_name = trim($param_info['method_name']);
            $where[] = ['pm.method_name', $method_name];
        }
        if (!empty($param_info['channels_name'])) {
            $channels_name = trim($param_info['channels_name']);
            $where[] = ['pc.channels_name', $channels_name];
        }
        if (!empty($param_info['method_id'])) {
            $method_id = intval($param_info['method_id']);
            $where[] = ['pm.id', $method_id];
        }
        if (!empty($param_info['channels_id'])) {
            $channels_id = intval($param_info['channels_id']);
            $where[] = ['pc.id', $channels_id];
        }
        $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $dtr_list = DB::table($this->table)->select($field)
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'dtr.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'dtr.channels_id')
            ->where($where)
            ->orderBy('dtr.create_time', 'desc')->paginate($page_size);
        $dtr_list = objectToArrayZ($dtr_list);
        return $dtr_list;
    }

    /**
     * description 获取折扣类型记录列表
     * editor zongxing
     * date 2019.09.02
     * return Array
     */
    public function getTotalDisTypeRedList($param_info = [])
    {
        $field = $this->field;
        $field = array_merge($field, ['pm.method_name', 'pc.channels_name', 'pc.original_or_discount']);
        $where = [];
        if (!empty($param_info['buy_time'])) {
            $buy_time = trim($param_info['buy_time']);
            $where[] = ['dtr.start_date', '<=', $buy_time];
            $where[] = ['dtr.end_date', '>=', $buy_time];
        } else {
            if (!empty($param_info['start_date'])) {
                $start_date = trim($param_info['start_date']);
                $where[] = ['dtr.start_date', '<=', $start_date];
            }
            if (!empty($param_info['end_date'])) {
                $end_date = trim($param_info['end_date']);
                $where[] = ['dtr.end_date', '>=', $end_date];
            }
        }
        if (!empty($param_info['method_name'])) {
            $method_name = trim($param_info['method_name']);
            $where[] = ['pm.method_name', $method_name];
        }
        if (!empty($param_info['channels_name'])) {
            $channels_name = trim($param_info['channels_name']);
            $where[] = ['pc.channels_name', $channels_name];
        }
        if (!empty($param_info['method_id'])) {
            $method_id = intval($param_info['method_id']);
            $where[] = ['pm.id', $method_id];
        }
        if (!empty($param_info['channels_id'])) {
            $channels_id = intval($param_info['channels_id']);
            $where[] = ['pc.id', $channels_id];
        }
        $dtr_list = DB::table($this->table)->select($field)
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'dtr.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'dtr.channels_id')
            ->where($where)
            ->orderBy('dtr.create_time', 'desc')->get();
        $dtr_list = objectToArrayZ($dtr_list);
        return $dtr_list;
    }

    /**
     * description 新增折扣类型记录
     * editor zongxing
     * date 2019.09.02
     * return Array
     */
    public function doAddDiscountTypeRecord($param_info)
    {
        $start_date = $param_info['start_date'];
        $end_date = $param_info['end_date'];
        $method_id = intval($param_info['method_id']);
        $channels_id = intval($param_info['channels_id']);
        $cost_id = trim($param_info['cost_id']);
        $predict_id = trim($param_info['predict_id']);
        $goods_cost_id = trim($param_info['goods_cost_id']);
        $goods_predict_id = trim($param_info['goods_predict_id']);
        $month_type_id = trim($param_info['month_type_id']);
        $brand_month_predict_id = isset($param_info['brand_month_predict_id']) ? trim($param_info['brand_month_predict_id']) : '';
        $pay_id = isset($param_info['pay_id']) ? trim($param_info['pay_id']) : '';
        $offer_id = isset($param_info['offer_id']) ? trim($param_info['offer_id']) : '';
        $goods_offer_id = isset($param_info['goods_offer_id']) ? trim($param_info['goods_offer_id']) : '';
        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'method_id' => $method_id,
            'channels_id' => $channels_id,
            'cost_id' => $cost_id,
            'pay_id' => $pay_id,
            'predict_id' => $predict_id,
            'goods_cost_id' => $goods_cost_id,
            'goods_predict_id' => $goods_predict_id,
            'month_type_id' => $month_type_id,
            'brand_month_predict_id' => $brand_month_predict_id,
            'offer_id' => $offer_id,
            'goods_offer_id' => $goods_offer_id,
        ];
        $res = DB::table('discount_type_record')->insert($data);
        return $res;
    }

    /**
     * description 获取渠道品牌报价档位ID和高价商品报价档位ID
     * author zhangdong
     * date 2019-12-18
     */
    public function getOfferMsg($channelId)
    {
        //当天时间也就是生成报价的时间
        $curDay = ParamsSet::getGenerateDate();
        if (empty($curDay)) {
            return [];
        }
        $where = [
            ['channels_id', $channelId],
            ['start_date', '<', $curDay],
            ['end_date', '>', $curDay],
        ];
        $field = ['offer_id', 'goods_offer_id'];
        $queryRes = DB::table($this->table)->where($where)->orderBy('create_time', 'DESC')->first($field);
        return $queryRes;
    }


}//end of class
