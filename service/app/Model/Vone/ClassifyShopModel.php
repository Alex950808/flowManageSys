<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\Vone\ShopStockModel;

class ClassifyShopModel extends Model
{

    protected $table = 'classify_shop as cs';

    //可操作字段
    protected $field = ['cs.classify_id', 'cs.shop_id'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description 获取指定用户类型可以查看的店铺信息
     * editor zongxing
     * date 2019.11.28
     * return Array
     */
    public function getClassifyShop($classify_id)
    {
        $field = $this->field;
        $classify_shop_info = DB::table($this->table)
            ->where('cs.classify_id', $classify_id)
            ->get($field);
        $classify_shop_info = ObjectToArrayZ($classify_shop_info);
        return $classify_shop_info;
    }

    /**
     * description 获取所有用户类型对应的店铺信息,按照用户类型进行分组
     * editor zongxing
     * date 2019.12.05
     * return Array
     */
    public function getClassifyShopGroupByClassifyId()
    {
        $field = $this->field;
        $classify_shop_info = DB::table($this->table)
            ->get($field)
            ->groupBy('classify_id');
        $classify_shop_info = ObjectToArrayZ($classify_shop_info);

        $shop_stock_model = new ShopStockModel();
        $shop_info = $shop_stock_model->shop_name;
        foreach ($classify_shop_info as $k => $v) {
            $sort_arr = [];
            foreach ($v as $k1 => $v1) {
                if(isset($shop_info[$v1['shop_id']])){
                    $name_len = strlen($shop_info[$v1['shop_id']]);
                    $sort_arr[] = $name_len;
                    $classify_shop_info[$k][$k1]['shop_name'] = $shop_info[$v1['shop_id']];
                }
            }
            if (count($sort_arr)>1){
                array_multisort($sort_arr,SORT_ASC, SORT_STRING,$classify_shop_info[$k]);
            }
        }
        return $classify_shop_info;
    }


}
