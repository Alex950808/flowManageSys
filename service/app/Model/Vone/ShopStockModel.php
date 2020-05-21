<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShopStockModel extends Model
{
    public $table = 'shop_stock as sst';
    private $field = [
        'sst.shop_id', 'sst.spec_sn', 'sst.stock', 'sst.modify_time'
    ];

    public $shop_id = [19, 22, 33, 83, 84, 85, 86, 87, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99,];
    public $shop_name = [
        '19' => 'MIS-HK-溢采', '99' => 'MIS-HK-电商', '33' => 'MIS-HK-拼多多', '22' => 'MIS-HK-清货仓库',
        '83' => 'MIS-HK-重折(2.5-3)', '84' => 'MIS-HK-重折(2-2.5)', '85' => 'MIS-HK-重折(1.5-2)',
        '86' => 'MIS-HK-重折(1-1.5)', '87' => 'MIS-HK-重折(1以上)', '89' => 'MIS-HK-轻折(2.5-3)',
        '90' => 'MIS-HK-轻折(2-2.5)', '91' => 'MIS-HK-轻折(1.5-2)', '92' => 'MIS-HK-轻折(1-1.5)',
        '93' => 'MIS-HK-轻折( 1以上)', '94' => 'MIS-HK-良品(2.5-3)', '95' => 'MIS-HK-良品(2-2.5)',
        '96' => 'MIS-HK-良品(1.5-2)', '97' => 'MIS-HK-良品(1-1.5)', '98' => 'MIS-HK-良品(1以内)',

    ];

    /**
     * description 店铺库存同步-获取相应SKU的信息
     * author zhangdong
     * date 2019.11.29
     */
    public function getShopSku($arrSpecSn, $shopNum)
    {
        $where = [
            ['shop_id', $shopNum]
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)
            ->whereIn('spec_sn', $arrSpecSn)->get();
        return $queryRes;
    }

    /**
     * description 批量更新语句执行
     * author zhangdong
     * date 2019.11.29
     */
    public function executeSql($strSql, $bindData)
    {
        $executeRes = DB::update($strSql, $bindData);
        return $executeRes;
    }

    /**
     * description 获取指定商品库存信息
     * author zongxing
     * Date 2019.11.29
     * param $spec_sn_arr 商品规格码
     * param $shop_id_arr 店铺信息
     */
    public function getSpecStockInfo($spec_sn_arr, $shop_id_arr)
    {
        $field = $this->field;
        $shop_name = $this->shop_name;
        $spec_stock_info = DB::table($this->table)
            ->whereIn('spec_sn', $spec_sn_arr)
            ->whereIn('shop_id', $shop_id_arr)
            ->orderby('shop_id', 'desc')
            ->get($field);
        $spec_stock_info = ObjectToArrayZ($spec_stock_info);
        $total_info = [];
        foreach ($spec_stock_info as $k => $v) {
            if (isset($shop_name[$v['shop_id']])) {
                $v['shop_name'] = $shop_name[$v['shop_id']];
                $total_info[] = $v;
            }
        }
        return $total_info;
    }

    /**
     * description 获取店铺信息
     * author zongxing
     * Date 2020.02.17
     */
    public function getShopInfo()
    {
        $shop_list = $this->shop_name;
        $shop_info = $sort_arr = [];
        foreach ($shop_list as $k => $v) {
            $shop_info[] = [
                'shop_id' => $k,
                'shop_name' => $v,
            ];
            $sort_arr[] = strlen($v);
        }
        array_multisort($sort_arr,SORT_ASC, SORT_STRING,$shop_info);
        return $shop_info;
    }


}//end of class
