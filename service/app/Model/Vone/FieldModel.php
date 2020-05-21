<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FieldModel extends Model
{

    protected $table = 'field as f';

    //可操作字段
    protected $field = ['f.id', 'f.field_name_cn', 'f.field_name_en', 'f.description'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description 获取所有字段信息
     * editor zongxing
     * date 2019.12.04
     * return Array
     */
    public function getClassifyField()
    {
        $field = $this->field;
        $add_field = [
            DB::raw('length(field_name_cn) as name_len')
        ];
        $field = array_merge($field, $add_field);
        $classify_field_info = DB::table($this->table)
            ->orderBy('name_len')
            ->get($field);
        $classify_field_info = ObjectToArrayZ($classify_field_info);
        return $classify_field_info;
    }




}
