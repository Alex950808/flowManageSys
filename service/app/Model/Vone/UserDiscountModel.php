<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDiscountModel extends Model
{
    public $table = 'user_discount as ud';
    private $field = [
        'ud.id','ud.sale_user_id','ud.type','ud.discount','ud.create_time',
    ];

    //折扣类型 1 代采折扣 2 全包折扣 3 一件代发(停用)
    public $type = [
        '1' => '代采折扣',
        '2' => '全包折扣',
    ];

    /**
     * desc 获取存在折扣的销售用户
     * author zhangdong
     * date 2020.05.12
     */
    public function getDisc()
    {
        $fields = [
            'ud.sale_user_id', 'su.user_name',
        ];
        $where = [
            ['su.is_start', '1'],
            ['ud.is_start', '1'],
        ];
        $queryRes = DB::table($this->table)->select($fields)
        ->leftJoin('sale_user as su','su.id','ud.sale_user_id')
        ->where($where)->groupBy('ud.sale_user_id')->get();
        return $queryRes;
    }


    /**
     * desc 获取销售用户折扣信息
     * author zhangdong
     * date 2020.05.12
     */
    public function getUserDisc($saleUid, $discType)
    {
        $fields = ['sale_user_id', 'discount'];
        $where = [
            ['sale_user_id', $saleUid],
            ['type', $discType],
        ];
        $queryRes = DB::table($this->table)->select($fields)
            ->where($where)->first();
        return $queryRes;
    }









}//end of class
