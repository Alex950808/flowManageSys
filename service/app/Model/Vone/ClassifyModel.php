<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassifyModel extends Model
{

    protected $table = 'classify as c';

    //可操作字段
    protected $field = ['c.id', 'c.classify_name', 'c.description'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description 获取用户分类可以查看的字段信息
     * editor zongxing
     * date 2019.11.28
     * return Array
     */
    public function getUserClassify($param_info)
    {
        $field = $this->field;

        $user_classify_obj = DB::table($this->table);
        if (!empty($param_info['is_page']) && $param_info['is_page'] == 1) {
            $user_classify_info = $user_classify_obj->paginate($page_size);
        } else {
            $user_classify_info = $user_classify_obj->get();
        }
        $user_classify_info = ObjectToArrayZ($user_classify_info);
        return $user_classify_info;
    }

    /**
     * description 检查用户分类的信息
     * editor zongxing
     * date 2019.12.04
     * params 1.分类信息:$param_info;
     * return Array
     */
    public function check_classify_info($param_info)
    {
        $classify_info = DB::table('classify')
            ->where(function ($where) use ($param_info) {
                if (isset($param_info['id'])) {
                    $where->orWhere('id', intval($param_info['id']));
                }
                if (isset($param_info['classify_name'])) {
                    $where->orWhere('classify_name', trim($param_info['classify_name']));
                }
            })
            ->get();
        $classify_info = ObjectToArrayZ($classify_info);
        return $classify_info;
    }

    /**
     * description 编辑用户分类
     * editor zongxing
     * date 2019.12.04
     * return Array
     */
    public function editClassify($param_info, $old_classify_info, $cf_info, $cs_info)
    {
        $classify_filed = explode(',', $param_info['classify_filed']);//分类可见字段
        $classify_shop = explode(',', $param_info['classify_shop']);//店铺信息

        //组装用户分类更新数据
        $update_data = [];
        if ($old_classify_info['classify_name'] != trim($param_info['classify_name'])) {
            $update_data['classify_name'] = trim($param_info['classify_name']);
        } elseif ($old_classify_info['description'] != trim($param_info['description'])) {
            $update_data['description'] = trim($param_info['description']);
        }
        //组装分类可见字段数据
        $old_field_info = $old_shop_info = $insertFieldData = $insertShopData = [];
        foreach ($cf_info as $k => $v) {
            $old_field_info[] = $v['field_id'];
        }
        //组装分类店铺数据
        foreach ($cs_info as $k => $v) {
            $old_shop_info[] = $v['shop_id'];
        }
        $classify_id = intval($param_info['id']);
        foreach ($classify_filed as $k => $v) {
            if (!in_array($v, $old_field_info)) {
                $insertFieldData[] = [
                    'classify_id' => $classify_id,
                    'field_id' => $v,
                ];
            }
        }
        foreach ($classify_shop as $k => $v) {
            if (!in_array($v, $old_shop_info)) {
                $insertShopData[] = [
                    'classify_id' => $classify_id,
                    'shop_id' => $v,
                ];
            }
        }
        $filed_diff = array_diff($old_field_info, $classify_filed);  
        $shop_diff = array_diff($old_shop_info, $classify_shop);
        $res = DB::transaction(function () use ($classify_id, $update_data, $insertFieldData, $insertShopData, $filed_diff, $shop_diff) {

            $res = true;
            //更新用户分类信息
            if(!empty($update_data)){
                $res = DB::table('classify')->where('id', $classify_id)->update($update_data);
            }
            //新增用户分类可见字段信息
            if(!empty($insertFieldData)){
                $res = DB::table('classify_field')->insert($insertFieldData);
            }
            //新增用户分类店铺信息
            if(!empty($insertShopData)){
                $res = DB::table('classify_shop')->insert($insertShopData);
            }
            //删除用户分类可见字段信息
            if(!empty($filed_diff)){
                $res = DB::table('classify_field')->where('classify_id', $classify_id)->whereIn('field_id', $filed_diff)->delete();
            }
            //删除用户分类店铺信息
            if(!empty($shop_diff)){
                $res = DB::table('classify_shop')->where('classify_id', $classify_id)->whereIn('shop_id', $shop_diff)->delete();
            }
            return $res;
        });
        return $res;
    }


}
