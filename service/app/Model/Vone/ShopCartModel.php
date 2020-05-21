<?php

namespace App\Model\Vone;

use App\Modules\ParamsSet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShopCartModel extends Model
{
    protected $table = 'shop_cart as sc';

    //可操作字段
    protected $field = ['sc.id', 'sc.cart_date', 'sc.spec_sn', 'sc.erp_prd_no', 'sc.cat_name', 'sc.match_scale'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description 获取购物车列表
     * editor zongxing
     * date 2019.11.12
     * return Array
     */
    public function getShopCartList($param_info)
    {
        $field = $this->field;
        $add_field = ['g.goods_name', 'gs.erp_merchant_no', 'gs.erp_ref_no'];
        $field = array_merge($field, $add_field);
        $sc_obj = DB::table($this->table)
            ->select($field)
            ->leftJoin('goods_spec as gs', 'gs.spec_sn', '=', 'sc.spec_sn')
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn');
        if (isset($param_info['cart_date']) && !empty($param_info['cart_date'])) {
            $cart_date = date('Y-m-d', strtotime(trim($param_info['cart_date'])));
            $sc_obj->where('sc.cart_date', '=', $cart_date);
        }
        if (isset($param_info['spec_sn_arr'])) {
            $sc_obj->whereIn('sc.spec_sn', $param_info['spec_sn_arr']);
        }
        if (isset($param_info['query_sn'])) {
            $query_sn = '%' . trim($param_info['query_sn']) . '%';
            $sc_obj->where(function ($where) use ($query_sn) {
                $where->orWhere('gs.spec_sn', 'like', $query_sn);
                $where->orWhere('gs.erp_merchant_no', 'like', $query_sn);
                $where->orWhere('gs.erp_ref_no', 'like', $query_sn);
                $where->orWhere('gs.erp_prd_no', 'like', $query_sn);
                $where->orWhere('g.goods_name', 'like', $query_sn);
            });
        }
        if (isset($param_info['goods_name'])) {
            $spec_sn = trim($param_info['goods_name']);
            $sc_obj->where('g.goods_name', $spec_sn);
        }
        if (isset($param_info['spec_sn'])) {
            $spec_sn = trim($param_info['spec_sn']);
            $sc_obj->where('gs.spec_sn', $spec_sn);
        }
        if (isset($param_info['erp_merchant_no'])) {
            $spec_sn = trim($param_info['erp_merchant_no']);
            $sc_obj->where('gs.erp_merchant_no', $spec_sn);
        }
        if (isset($param_info['erp_ref_no'])) {
            $spec_sn = trim($param_info['erp_ref_no']);
            $sc_obj->where('gs.erp_ref_no', $spec_sn);
        }
        if (isset($param_info['erp_prd_no'])) {
            $spec_sn = trim($param_info['erp_prd_no']);
            $sc_obj->where('gs.erp_prd_no', $spec_sn);
        }
        if (isset($param_info['limit']) && $param_info['limit']) {
            $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
            $shop_cart_list = $sc_obj->orderBy('sc.create_time', 'desc')->paginate($page_size);
        } else {
            $shop_cart_list = $sc_obj->orderBy('sc.create_time', 'desc')->get();
        }
        $shop_cart_list = objectToArrayZ($shop_cart_list);
        return $shop_cart_list;
    }

    /**
     * description 整理上传商品信息（上传购物车商品）
     * author zongxing
     * date 2019.11.12
     */
    public function createShopCartGoodsInfo($res, $arrTitle)
    {
        $spec_info = $upload_goods_info = $brand_id_arr = [];
        $spec_code_arr = [
            '商家编码' => 'erp_merchant_no',
            '商品代码' => 'erp_prd_no',
            '参考码' => 'erp_ref_no',
            '商品品牌' => 'brand_name',
            '商品分类' => 'cat_name',
            '搭配比例' => 'match_scale',
        ];

        foreach ($res as $k => $v) {
            if ($k == 0) continue;
            $tmp_arr = [];
            foreach ($arrTitle as $k1 => $v1) {
                if (isset($spec_code_arr[$v1])) {
                    $key = array_search($v1, $res[0]);
                    $tmp_arr[$spec_code_arr[$v1]] = $v[$key];
                }
            }
            $row = $k +1;
            if (empty($tmp_arr['cat_name'])) {
                return ['code' => '1101', 'msg' => '第 ' . $row . ' 条商品的商品分类不能为空'];
            }
            if (empty($tmp_arr['erp_merchant_no']) && empty($tmp_arr['erp_prd_no']) && empty($tmp_arr['erp_ref_no'])) {
                return ['code' => '1102', 'msg' => '第 ' . $row . ' 条商品中的商家编码、商品代码、参考码必须有一个'];
            }
            if (!empty($tmp_arr['erp_merchant_no'])) {
                $spec_info['erp_merchant_no'][] = trim($tmp_arr['erp_merchant_no']);
            }
            if (!empty($tmp_arr['erp_prd_no'])) {
                $spec_info['erp_prd_no'][] = trim($tmp_arr['erp_prd_no']);
            }
            if (!empty($tmp_arr['erp_ref_no'])) {
                $spec_info['erp_ref_no'][] = trim($tmp_arr['erp_ref_no']);
            }
            $upload_goods_info[] = $tmp_arr;
        }
        $gs_model = new GoodsSpecModel();
        $spec_detail = $gs_model->getGoodsSpecDetail($spec_info);
        $diff_goods_info = [];
        foreach ($upload_goods_info as $k => $v) {
            $erp_merchant_no = trim($v['erp_merchant_no']);
            $erp_prd_no = trim($v['erp_prd_no']);
            $erp_ref_no = trim($v['erp_ref_no']);
            if (isset($spec_detail['erp_merchant_no'][$erp_merchant_no])) {
                $upload_goods_info[$k]['spec_sn'] = trim($spec_detail['erp_merchant_no'][$erp_merchant_no]);
                continue;
            }
            if (isset($spec_detail['erp_prd_no'][$erp_prd_no])) {
                $upload_goods_info[$k]['spec_sn'] = trim($spec_detail['erp_prd_no'][$erp_prd_no]);
                continue;
            }
            if (isset($spec_detail['erp_ref_no'][$erp_ref_no])) {
                $upload_goods_info[$k]['spec_sn'] = trim($spec_detail['erp_ref_no'][$erp_ref_no]);
                continue;
            }
            $diff_goods_info[] = $v;
            unset($upload_goods_info[$k]);
        }
        $return_info = [
            'upload_goods_info' => $upload_goods_info,
            'diff_goods_info' => $diff_goods_info,
        ];
        return $return_info;
    }

    /**
     * description 上传购物车商品
     * author zongxing
     * date 2019.11.12
     */
    public function uploadShopCartGoods($param_info, $upload_goods_info)
    {
        //获取商品规格码信息
        $spec_sn_info = [];
        foreach ($upload_goods_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            if (!in_array($spec_sn, $spec_sn_info)) {
                $spec_sn_info[] = $spec_sn;
            }
        }
        //获取购物车商品列表
        $param_info['spec_sn_arr'] = $spec_sn_info;
        $shop_cart_list = $this->getShopCartList($param_info);
        $sc_goods_list = [];
        foreach ($shop_cart_list as $k => $v) {
            $sc_goods_list[$v['spec_sn']] = $v;
        }
        $cart_date = $param_info['cart_date'];
        $insertShopCart = $updateGmcDiscount = [];
        foreach ($upload_goods_info as $k => $v) {
            $spec_sn = trim($v['spec_sn']);
            $erp_prd_no = trim($v['erp_prd_no']);
            $cat_name = $v['cat_name'];
            $match_scale = $v['match_scale'];
            if (isset($sc_goods_list[$spec_sn])) {
                //shop_cart表更新数据
                $id = $sc_goods_list[$spec_sn]['id'];
                $updateGmcDiscount['cat_name'][][$id] = $cat_name;
                $updateGmcDiscount['match_scale'][][$id] = $match_scale;
            } else {
                //shop_cart表新增数据
                $insertShopCart[] = [
                    'spec_sn' => $spec_sn,
                    'erp_prd_no' => $erp_prd_no,
                    'cat_name' => $cat_name,
                    'cart_date' => $cart_date,
                    'match_scale' => $match_scale,
                ];
            }
        }
        $updateGmcDiscountSql = '';
        if (!empty($updateGmcDiscount)) {
            //需要判断的字段
            $column = 'id';
            $updateGmcDiscountSql = makeBatchUpdateSql('jms_shop_cart', $updateGmcDiscount, $column);
        }
        $res = DB::transaction(function () use ($insertShopCart, $updateGmcDiscountSql) {
            //shop_cart表更新数据
            if (!empty($updateGmcDiscountSql)) {
                $res = DB::update(DB::raw($updateGmcDiscountSql));
            }
            //shop_cart表新增数据
            if (!empty($insertShopCart)) {
                $res = DB::table('shop_cart')->insert($insertShopCart);
            }
            return $res;
        });
        return $res;
    }

    /**
     * description 获取需要报价的SKU信息
     * author zhangdong
     * date 2019.11.27
     */
    public function getCartSku($generateDate)
    {
        $field = ['spec_sn'];
        $where = [
            ['cart_date', $generateDate],
        ];
        $queryRes = DB::table($this->table)->select($field)->where($where)->pluck('spec_sn');
        return $queryRes;
    }

    /**
     * description 根据购物车数据生成的时间统计相应总条数
     * author zhangdong
     * date 2019.12.17
     */
    public function countCartSku($generateDate)
    {
        $where = [
            ['cart_date', $generateDate],
        ];
        $count = DB::table($this->table)->where($where)->count();
        return $count;
    }



}//end of class
