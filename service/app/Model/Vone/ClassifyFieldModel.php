<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassifyFieldModel extends Model
{

    protected $table = 'classify_field as cf';

    //可操作字段
    protected $field = ['cf.classify_id', 'cf.field_id'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description 获取指定用户类型可以查看的字段信息
     * editor zongxing
     * date 2019.11.28
     * return Array
     */
    public function getClassifyField($classify_id)
    {
        $field = $this->field;
        $add_field = [
            'f.field_name_cn', 'f.field_name_en',
            DB::raw('length(field_name_cn) as name_len')
        ];
        $field = array_merge($field, $add_field);
        $classify_field_info = DB::table($this->table)
            ->leftJoin('field as f', 'f.id', '=', 'cf.field_id')
            ->where('cf.classify_id', $classify_id)
            ->orderBy('name_len')
            ->get($field);
        $classify_field_info = ObjectToArrayZ($classify_field_info);
        return $classify_field_info;
    }

    /**
     * description 获取所有用户类型可以查看的字段信息,按照用户类型进行分组
     * editor zongxing
     * date 2019.12.04
     * return Array
     */
    public function getClassifyFieldGroupByClassifyId()
    {
        $field = $this->field;
        $add_field = [
            'f.field_name_cn', 'f.field_name_en',
            DB::raw('length(field_name_cn) as name_len')
        ];
        $field = array_merge($field, $add_field);
        $classify_field_info = DB::table($this->table)
            ->leftJoin('field as f', 'f.id', '=', 'cf.field_id')
            ->orderBy('name_len')
            ->get($field)
            ->groupBy('classify_id');
        $classify_field_info = ObjectToArrayZ($classify_field_info);
        return $classify_field_info;
    }

    /**
     * description 根据管理员ID获取折扣种类信息
     * author zhangdong
     * date 2020.05.18
     */
    public function getCatByAid()
    {
        $fields = [
            'f.field_name_cn', 'f.field_name_en',
        ];
        $adminId = 2;
        $where = [
            ['cf.classify_id', $adminId],
        ];
        $queryRes = DB::table($this->table)->select($fields)
            ->leftJoin('field as f', 'cf.field_id', 'f.id')
            ->where($where)
            ->orderBy('cf.sort_num', 'asc')->get();
        return $queryRes;
    }


}//end class
