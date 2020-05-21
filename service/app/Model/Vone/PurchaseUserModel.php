<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseUserModel extends Model
{
    protected $table = 'purchase_user as pu';

    //可操作字段
    protected $fillable = ['pu.id', 'pu.real_name', 'pu.passport_sn', 'pu.method_id', 'pu.channels_id', 'pu.account_number'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description:检查采购人员的信息
     * editor:zongxing
     * date : 2018.07.04
     * params: 1.采购人员姓名:real_name;2.采购人员护照号:passport_sn;3.采购方式id:method_id;4.采购渠道id:channels_id;
     *          5.账号:account_number;
     * return Array
     */
    public function check_user_info($purchase_user_info)
    {
        //检查采购人员姓名
        $user_info = DB::table("purchase_user")
            ->where("real_name", $purchase_user_info["real_name"])
            ->orWhere("passport_sn", $purchase_user_info["passport_sn"])
            ->orWhere("account_number", $purchase_user_info["account_number"])
            ->get();
        $user_info = $user_info->toArray();
        return $user_info;
    }

    /**
     * description:获取采购id列表
     * editor:zongxing
     * type:GET
     * date : 2018.07.10
     * return Object
     */
    public function getUserList($param = [])
    {
        $user_list_obj = DB::table($this->table)
            ->leftJoin('purchase_channels as pc', 'pc.id', 'pu.channels_id')
            ->leftJoin("purchase_method as pm", "pm.id", 'pu.method_id');
        if (!empty($param['channel_id'])) {
            $user_list_obj->where('pc.id', $param['channel_id']);
        }
        if (!empty($param['method_id'])) {
            $method_id = intval($param['method_id']);
            $user_list_obj->where('pm.method_id', $method_id);
        }
        if (!empty($param['real_name'])) {
            $real_name = trim($param['real_name']);
            $user_list_obj->where('real_name', $real_name);
        }
        if (!empty($param['passport_sn'])) {
            $passport_sn = trim($param['passport_sn']);
            $user_list_obj->where('passport_sn', $passport_sn);
        }
        if (!empty($param['account_number'])) {
            $account_number = trim($param['account_number']);
            $user_list_obj->where('account_number', $account_number);
        }
        $field = $this->fillable;
        $add_field = ['method_name','channels_name'];
        $field = array_merge($field, $add_field);
        $user_list_info = $user_list_obj->orderBy('pu.create_time', 'desc')
            ->get($field);
        $user_list_info = objectToArrayZ($user_list_info);
        return $user_list_info;
    }

    /**
     * description:获取采购id信息
     * editor:zongxing
     * type:POST
     * date : 2018.07.10
     * return Object
     */
    public function getUserInfo($user_info)
    {
        $purchase_user_id = $user_info["id"];
        $user_detail_info = DB::table("purchase_user")
            ->select("id", "real_name", "passport_sn", "channels_id", "method_id", "account_number")
            ->where("id", $purchase_user_id)
            ->get();
        $user_detail_info = $user_detail_info->toArray();
        return $user_detail_info;
    }

    /**
     * description:提交编辑采购id页面
     * editor:zongxing
     * type:POST
     * date : 2018.07.10
     * return Object
     */
    public function editUserInfo($user_info)
    {
        $purchase_user_id = $user_info["id"];
        $update_user_info = DB::table("purchase_user as pu")
            ->where("id", $purchase_user_id)
            ->update($user_info);
        $update_user_info = $update_user_info->toArray();
        return $update_user_info;
    }

}
