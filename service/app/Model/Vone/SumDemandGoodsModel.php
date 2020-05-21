<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SumDemandGoodsModel extends Model
{
    protected $table = 'sum_demand_goods as sdg';

    protected $field = [
        'sdg.id', 'sdg.sum_demand_sn', 'sdg.goods_name', 'sdg.erp_prd_no', 'sdg.erp_merchant_no', 'sdg.spec_sn',
        'sdg.goods_num', 'sdg.allot_num'
    ];

    /**
     * description:采购任务详情
     * editor:zongxing
     * date : 2019.05.14
     * return Array
     */
    public function purchaseTaskDetail($sd_sn_arr)
    {
        $field = $this->field;
        $tmp_field = ['g.brand_id', 'b.name', 'gs.erp_ref_no'];
        $field = array_merge($field, $tmp_field);
        $sum_demand_detail = DB::table($this->table)
            ->leftJoin('goods_spec as gs', 'gs.spec_sn', '=', 'sdg.spec_sn')
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id')
            ->whereIn('sdg.sum_demand_sn', $sd_sn_arr)
            ->orderBy('g.brand_id', 'DESC')
            ->get($field);
        $sum_demand_detail = objectToArrayZ($sum_demand_detail);
        return $sum_demand_detail;
    }

    /**
     * description:组装汇总单商品分配数据
     * editor:zongxing
     * date : 2019.05.16
     * return Array
     */
    public function createUploadSumDemandGoods($res, $sum_demand_goods, $pcm_list, $sdcg_list, $channel_start_num)
    {
        $upload_goods_info = [];
        $error_spec_info = [];
        $error_pcm_info = [];
        foreach ($res as $k => $v) {
            if ($k < 3) continue;
            //进行商品是否存在需求判断
            $spec_sn = $v[0];
            if (!isset($sum_demand_goods[$spec_sn])) {
                $error_spec_info[] = $spec_sn;
                continue;
            }

            //组装上传商品信息
            $total_may_num = 0;
            foreach ($v as $k1 => $v1) {
                if ($k1 < $channel_start_num) continue;
                if ($res[2][$k1] == '可采数') {
                    $channels_name = $res[1][$k1];
                    $may_num = intval($v1);

                    $total_may_num += $may_num;
                    //判断上传的商品是否已经分配过
                    if ($may_num == 0 && !isset($sdcg_list[$spec_sn][$channels_name]['may_num'])) {
                        continue;
                    }
                    //判断渠道方式是否存在
                    if (!isset($pcm_list[$channels_name])) {
                        $error_pcm_info[] = $spec_sn;
                        continue;
                    }
                    //判断商品折扣是否存在
                    $channels_discount = 0;
                    if (isset($sum_demand_goods[$spec_sn]['channels_info'][$channels_name])) {
                        $channels_discount = $sum_demand_goods[$spec_sn]['channels_info'][$channels_name];
                    }

                    $channels_id = $pcm_list[$channels_name]['channels_id'];
                    $method_id = $pcm_list[$channels_name]['method_id'];
                    $upload_goods_info[$spec_sn][$channels_name] = [
                        'channels_id' => $channels_id,
                        'method_id' => $method_id,
                        'channel_discount' => $channels_discount,
                        'may_num' => $may_num,
                    ];
                }
            }
            //检查商品的可采数分配是否大于可分配数
            $goods_num = $sum_demand_goods[$spec_sn]['goods_num'];
            if ($goods_num < $total_may_num) {
                return ['code' => '1105', 'msg' => '您上传的商品:' . $spec_sn . '可采数大于总需求数,请检查'];
            }
        }

        if (!empty($error_spec_info)) {
            $error_spec_info = json_encode($error_spec_info);
            $error_info = substr($error_spec_info, 1, -1);
            return ['code' => '1101', 'msg' => '您上传的商品:' . $error_info . '不存在需求信息,请检查'];
        }
        if (!empty($error_pcm_info)) {
            $error_pcm_info = json_encode($error_pcm_info);
            $error_info = substr($error_pcm_info, 1, -1);
            return ['code' => '1103', 'msg' => '您上传的商品对应的:' . $error_info . '渠道方式信息有误,请检查'];
        }
        if (empty($upload_goods_info)) {
            return ['code' => '1104', 'msg' => '您上传的商品未分配可采数,请检查'];
        }
        return $upload_goods_info;
    }

    /**
     * description:获取汇总需求单统计信息
     * editor:zongxing
     * date : 2019.01.08
     * return Array
     */
    public function sumDemandStatistic($sd_sn_arr)
    {
        $field = $this->field;
        $tmp_field = [
            DB::raw('sum(jms_sdg.goods_num) as goods_num'),
            DB::raw('sum(jms_sdg.goods_num) as final_goods_num'),
            DB::raw('sum(jms_sdg.goods_num) - sum(jms_sdg.allot_num) as may_buy_num'),
            DB::raw('sum(jms_sdg.real_num) as real_buy_num'),
            DB::raw('sum(jms_sdg.real_num) as final_buy_num'),
//            DB::raw('round(sum(jms_sdg.real_num) / (sum(jms_sdg.goods_num) - sum(jms_sdg.allot_num)) * 100, 2) as real_buy_rate'),
//            DB::raw('round((sum(jms_sdg.goods_num) - sum(jms_sdg.allot_num) - sum(jms_sdg.real_num)) /
//            (sum(jms_sdg.goods_num) - sum(jms_sdg.allot_num)) * 100,2) as miss_buy_rate')
        ];
        $field = array_merge($field, $tmp_field);
        $sum_demand_statistic = DB::table($this->table)
            ->leftJoin('goods_spec as gs', 'gs.spec_sn', '=', 'sdg.spec_sn')
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id')
            ->whereIn('sdg.sum_demand_sn', $sd_sn_arr)
            ->groupBy('sdg.sum_demand_sn')
            ->get($field)
            ->groupBy('sum_demand_sn');
        $sum_demand_statistic = objectToArrayZ($sum_demand_statistic);
        return $sum_demand_statistic;
    }



}
