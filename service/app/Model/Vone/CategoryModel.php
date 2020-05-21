<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CategoryModel extends Model
{
    protected $table = 'category as c';

    //可操作字段
    protected $field = ['c.cat_id', 'c.cat_name', 'c.parent_id', 'c.level'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description:获取分类信息
     * author：zhangdong
     * date : 2019.02.14
     */
    public function getCategoryInfo($value, $type)
    {
        $where = [];
        //根据分类id查询分类信息
        if ($type == 1) {
            $value = intval($value);
            $where = [
                ['cat_id',$value],
            ];
        }
        //根据分类名称查询分类信息
        if ($type == 2) {
            $value = trim($value);
            $where = [
                ['cat_name','like','%' . $value . '%'],
            ];
        }
        //根据分类级别查询分类信息
        if ($type == 3) {
            $value = intval($value);
            $where = [
                ['level',$value],
            ];
        }
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->get();
        return $queryRes;
    }

    /**
     * @description:从redis中获取分类信息
     * @editor:张冬
     * @date : 2019.04.16
     * @return object
     */
    public function getCategoryInfoInRedis()
    {
        //从redis中获取品牌信息，如果没有则对其设置
        $categoryInfo = Redis::get('categoryInfo');
        if (empty($categoryInfo)) {
            $field = ['cat_id','cat_name'];
            $categoryInfo = DB::table($this->table) -> select($field)-> get()
                -> map(function ($value){
                    return (array) $value;
                }) -> toArray();
            Redis::set('categoryInfo', json_encode($categoryInfo, JSON_UNESCAPED_UNICODE));
            $categoryInfo = Redis::get('categoryInfo');
        }
        $categoryInfo = objectToArray(json_decode($categoryInfo));
        return $categoryInfo;

    }


    /**
     * description:递归获取分类信息
     * author：zongxing
     * date : 2020.02.13
     */
    public function getCategoryRecursion() 
    {
        $field = ['cat_id', 'cat_name', 'parent_id', 'level'];
        $cat_list = DB::table('category')->get($field);
        $cat_list = objectToArrayZ($cat_list);

        //第一步 构造数据
        $items = [];
        foreach($cat_list as $value){
            $items[$value['cat_id']] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $cat_total_list = [];
        foreach($items as $key => $value){
            if(isset($items[$value['parent_id']])){
                $items[$value['parent_id']]['child'][] = &$items[$key];
            }else{
                $cat_total_list[] = &$items[$key];
            }
        }
        return $cat_total_list;
    }




}//end of class
