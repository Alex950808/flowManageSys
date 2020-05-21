<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseCostModel extends Model
{
    protected $table = "purchase_cost";

    //可操作字段
    protected $fillable = ['cost_sn', "cost_coef", "cost_status"];

    //修改laravel 自动更新
    const UPDATED_AT = "modify_time";
    const CREATED_AT = "create_time";


    /**
     * description:获采购成本系数列表
     * editor:zongxing
     * date : 2019.01.07
     * return Array
     */
    public function getPurchaseCostList($param = [])
    {
        $pc_obj = DB::table($this->table);
        if (!empty($param['cost_sn'])) {
            $cost_sn = trim($param['cost_sn']);
            $pc_obj->where('cost_sn', $cost_sn);
        }
        $field = $this->field;
        $pc_list = $pc_obj->get($field);
        $pc_list = objectToArrayZ($pc_list);
        return $pc_list;
    }

    /**
     * description:生成采购成本系数编码
     * editor:zongxing
     * date : 2020.02.19
     * return String
     */
    public function getPurchaseCostSn()
    {
        $increaseNum = 0;
        do {
            $increaseNum += 1;
            $strNum = sprintf('%04d', $increaseNum);
            $cost_sn = 'PC' . $strNum;
            //联合采购单号查找当前这个需求单号是否已经存在
            $count = DB::table($this->table)
                ->where('cost_sn', '=', $cost_sn)->count();
        } while ($count);
        return $cost_sn;
    }
}
