<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FundCatModel extends Model
{
    public $table = 'fund_cat as fc';
    public $field = ['id', 'fund_cat_name'];

    //暂时不使用表
    public $channel_cat = [
        '1' => '自有可支配资金',
        '2' => '可融资金',
        '3' => '待回款资金',
        '4' => '需求资金'
    ];

    /**
     * description:获取资金渠道类别列表
     * editor:zongxing
     * date:2018.12.17
     */
    public function getFundCatList()
    {
        $fund_cat_list = DB::table($this->table)->get($this->field);
        $fund_cat_list = objectToArrayZ($fund_cat_list);
        return $fund_cat_list;
    }
}
