<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TargetModel extends Model
{
    protected $table = 'target as t';

    //可操作字段
    protected $field = ['t.id', 't.start_date', 't.end_date', 't.sale_user_id', 't.target_name', 't.target_content',
        't.target_currency', 't.target_value'];
    protected $target_currency = [
        1 => '美金',
        2 => '韩币',
        3 => '人民币',
    ];

    /**
     * description 获取目标列表
     * author zongxing
     * type GET
     * date 2019.10.28
     * return Array
     */
    public function targetList($param = [])
    {
        $field = $this->field;
        
        $where = [];
        if (!empty($param['start_date'])) {
            $where[] = ['start_date', '>=', trim($param['start_date'])];
        }
        if (!empty($param['end_date'])) {
             $where[] = ['end_date', '<=', trim($param['end_date'])];
        }
        if (!empty($param['target_name'])) {
            $target_name = '%'.trim($param['target_name']).'%';
            $where[] = ['target_name', 'like', $target_name];
        }
        if (!empty($param['sale_user_id'])) {
            $where[] = ['sale_user_id', intval($param['sale_user_id'])];
        }
        if (!empty($param['id'])) {
            $where[] = ['id', intval($param['id'])];
        }
        $pageSize = isset($param['page_size']) ? intval($param['page_size']) : 15;
        $target_list = DB::table($this->table)->select($field)->where($where)->paginate($pageSize);
        $target_list = objectToArrayZ($target_list);
        $target_info = $target_list['data'];

        $su_model = new SaleUserModel();
        $sale_user_list = $su_model->getSaleUserList();

        $user_list = [];
        foreach ($sale_user_list as $k=>$v){
            $user_list[$v['id']] = $v['user_name'];
        }
        foreach ($target_info as $k=>$v){
            $target_info[$k]['user_name'] = $user_list[$v['sale_user_id']];
        }
        $target_list['data'] = $target_info;
        return $target_list;
    }

    /**
     * description 新增目标
     * author zongxing
     * type POST
     * date 2019.10.28
     * return boolean
     */
    public function addTarget($param_info)
    {
        $data = [
            'start_date' => $param_info['start_date'],
            'end_date' => $param_info['end_date'],
            'sale_user_id' => intval($param_info['sale_user_id']),
            'target_name' => trim($param_info['target_name']),
            'target_content' => trim($param_info['target_content']),
            'target_currency' => intval($param_info['target_currency']),
            'target_value' => floatval($param_info['target_value']),
        ];
        $insert_res = DB::table('target')->insert($data);
        return $insert_res;
    }

    /**
     * description 编辑目标
     * author zongxing
     * type POST
     * date 2019.10.28
     * return boolean
     */
    public function editTarget($param_info)
    {
        $target_id = intval($param_info['id']);
        $data = [
            'start_date' => $param_info['start_date'],
            'end_date' => $param_info['end_date'],
            'sale_user_id' => intval($param_info['sale_user_id']),
            'target_name' => trim($param_info['target_name']),
            'target_content' => trim($param_info['target_content']),
            'target_currency' => intval($param_info['target_currency']),
            'target_value' => floatval($param_info['target_value']),
        ];
        $insert_res = DB::table('target')->where('id', $target_id)->update($data);
        return $insert_res;
    }

}
