<?php

namespace App\Model\Vone;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoodsSaleModel extends Model
{

    protected $table = 'goods_sale as gs';

    //可操作字段
    protected $field = ['gs.id', 'gs.sale_date', 'gs.spec_sn', 'gs.sale_price', 'gs.currency'];
    protected $currency = [
        '1' => '美金',
        '2' => '人民币',
        '3' => '韩币',
    ];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description 获取商品报价信息
     * editor zongxing
     * date 2019.11.28
     * return Array
     */
    public function getGoodsSaleList($param)
    {
        $where = [];
        if(isset($param['sale_date'])){
            $sale_date = trim($param['sale_date']);
            $where[] = ['sale_date', $sale_date];
        }
        $field = $this->field;
        $add_field = [
            'g.goods_name', 'gsc.erp_merchant_no', 'gsc.erp_ref_no', 'gsc.erp_prd_no',
            DB::raw("(case jms_gs.currency
                when 1 then '美金'
                when 2 then '人民币'
                when 3 then '韩币'
                end) currency_name"
            )
        ];
        $field = array_merge($field, $add_field);
        $sc_obj = DB::table($this->table)
            ->select($field)
            ->leftJoin('goods_spec as gsc', 'gsc.spec_sn', '=', 'gs.spec_sn')
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gsc.goods_sn')
            ->where($where);
        if (isset($param['sale_date']) && !empty($param['sale_date'])) {
            $sale_date = trim($param['sale_date']);
            $sc_obj->where('gs.sale_date', '=', $sale_date);
        }
        if (!empty($param['spec_sn_arr'])) {
            $sc_obj->whereIn('gs.spec_sn', $param['spec_sn_arr']);
        }
        if (isset($param['query_sn'])) {
            $query_sn = '%' . trim($param['query_sn']) . '%';
            $sc_obj->where(function ($where) use ($query_sn) {
                $where->orWhere('gsc.spec_sn', 'like', $query_sn);
                $where->orWhere('gsc.erp_merchant_no', 'like', $query_sn);
                $where->orWhere('gsc.erp_ref_no', 'like', $query_sn);
                $where->orWhere('gsc.erp_prd_no', 'like', $query_sn);
                $where->orWhere('g.goods_name', 'like', $query_sn);
            });
        }
        if (isset($param['limit']) && $param['limit']) {
            $page_size = isset($param['page_size']) ? intval($param['page_size']) : 15;
            $goods_sale_info = $sc_obj->orderBy('gs.spec_sn')->paginate($page_size);
        } else {
            $goods_sale_info = $sc_obj->orderBy('gs.spec_sn')->get();
        }
        $goods_sale_info = objectToArrayZ($goods_sale_info);
        return $goods_sale_info;
    }


    /**
     * description 整理上传商品信息（上传商品报价）
     * author zongxing
     * date 2019.11.29
     */
    public function createGoodsSaleInfo($res, $arrTitle)
    {
        $spec_info = $upload_goods_info = $brand_id_arr = [];
        $spec_code_arr = [
            '商家编码' => 'erp_merchant_no',
            '商品代码' => 'erp_prd_no',
            '参考码' => 'erp_ref_no',
            '商品品牌' => 'brand_name',
            '报价' => 'sale_price',
            '币种' => 'currency',
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
            if (empty($tmp_arr['sale_price'])) {
                return ['code' => '1101', 'msg' => '第 ' . $k . ' 条商品的商品报价不能为空或0'];
            }
            if (empty($tmp_arr['erp_merchant_no']) && empty($tmp_arr['erp_prd_no']) && empty($tmp_arr['erp_ref_no'])) {
                return ['code' => '1102', 'msg' => '第 ' . $k . ' 条商品中的商家编码、商品代码、参考码必须有一个'];
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
     * description 上传商品报价
     * author zongxing
     * date 2019.11.29
     */
    public function uploadGoodsSaleInfo($param_info, $upload_goods_info)
    {
        //获取商品规格码信息
        $spec_sn_info = [];
        foreach ($upload_goods_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            if (!in_array($spec_sn, $spec_sn_info)) {
                $spec_sn_info[] = $spec_sn;
            }
        }
        //获取报价商品列表
        $param_info['spec_sn_arr'] = $spec_sn_info;
        $goods_sale_info = $this->getGoodsSaleList($param_info);
        $goods_sale_list = [];
        foreach ($goods_sale_info as $k => $v) {
            $goods_sale_list[$v['spec_sn']] = $v;
        }
        $sale_date = $param_info['sale_date'];
        $insertGoodsSale = $updateGoodsSale = [];
        foreach ($upload_goods_info as $k => $v) {
            $spec_sn = trim($v['spec_sn']);
            $erp_prd_no = trim($v['erp_prd_no']);
            $sale_price = floatval($v['sale_price']);
            $currency = intval($v['currency']);
            if (isset($goods_sale_list[$spec_sn])) {
                //goods_sale表更新数据
                $id = $goods_sale_list[$spec_sn]['id'];
                if($goods_sale_list[$spec_sn]['sale_price'] != $sale_price ){
                    $updateGoodsSale['sale_price'][][$id] = $sale_price;
                }
                if($goods_sale_list[$spec_sn]['currency'] != $currency){
                    $updateGoodsSale['currency'][][$id] = $currency;
                }
            } else {
                //goods_sale表新增数据
                $insertGoodsSale[] = [
                    'spec_sn' => $spec_sn,
                    'erp_prd_no' => $erp_prd_no,
                    'sale_price' => $sale_price,
                    'sale_date' => $sale_date,
                    'currency' => $currency,
                ];
            }
        }
        $updateGoodsSaleSql = '';
        if (!empty($updateGoodsSale)) {
            //需要判断的字段
            $column = 'id';
            $updateGoodsSaleSql = makeBatchUpdateSql('jms_goods_sale', $updateGoodsSale, $column);
        }
        $res = DB::transaction(function () use ($insertGoodsSale, $updateGoodsSaleSql) {
            $res = 1;
            //goods_sale表更新数据
            if (!empty($updateGoodsSaleSql)) {
                $res = DB::update(DB::raw($updateGoodsSaleSql));
            }
            //goods_sale表新增数据
            if (!empty($insertGoodsSale)) {
                $res = DB::table('goods_sale')->insert($insertGoodsSale);
            }
            return $res;
        });
        return $res;
    }

}
