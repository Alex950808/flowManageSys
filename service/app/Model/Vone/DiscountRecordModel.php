<?php

namespace App\Model\Vone;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DiscountRecordModel extends Model
{
    protected $table = 'discount_record';

    //可操作字段
    protected $field = ['id', 'spec_sn', 'discount_month', 'channels_id', 'discount', 'add_point'];

    /**
     * 获取已经存在的最低折扣记录
     * author zongxing
     * date 2020/5/8 0008
     * @param $param
     * @return mixed
     */
    public function getDiscountRecordInfo($param)
    {
        $discountRecordInfoObj = DB::table('discount_record');
        if (isset($param['discount_month'])) {
            $discountRecordInfoObj->where('discount_month', $param['discount_month']);
        }
        if (isset($param['spec_sn_arr'])) {
            $discountRecordInfoObj->whereIn('spec_sn', $param['spec_sn_arr']);
        }
        if (isset($param['channels_id_arr'])) {
            $discountRecordInfoObj->whereIn('channels_id', $param['channels_id_arr']);
        }
        $discountRecordInfo = $discountRecordInfoObj->get($this->field);
        $discountRecordInfo = objectToArrayZ($discountRecordInfo);
        return $discountRecordInfo;
    }

}
