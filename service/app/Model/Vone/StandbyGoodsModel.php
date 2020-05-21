<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StandbyGoodsModel extends Model
{
    public $table = 'standby_goods as sg';
    private $field = [
        'sg.id', 'sg.goods_name', 'sg.spec_sn', 'sg.platform_barcode',
        'sg.max_num', 'sg.available_num', 'sg.is_purchase',
    ];

    /**
     * description 组装写入数据
     * author zhangdong
     * date 2019.09.16
     */
    public function makeInsertData($params)
    {
        $insertData = [
            'goods_name' => trim($params['goods_name']),
            'spec_sn' => trim($params['spec_sn']),
            'platform_barcode' => trim($params['platform_barcode']),
            'max_num' => trim($params['max_num']),
        ];
        return $insertData;
    }

    /**
     * description 保存数据
     * author zhangdong
     * date 2019.09.16
     */
    public function insertData($insertData)
    {
        $sgTable = cutString($this->table, 0, 'as');
        $insertRes = DB::table($sgTable)->insert($insertData);
        return $insertRes;
    }

    /**
     * description 根据规格码统计常备商品条数
     * author zhangdong
     * date 2019.09.16
     */
    public function countStandbyGoods($specSn)
    {
        $where = [
            ['spec_sn', $specSn],
        ];
        $count = DB::table($this->table)->where($where)->count();
        return $count;
    }

    /**
     * description 校验上传数据
     * author zhangdong
     * date 2019.09.16
     */
    public function checkUploadData($data)
    {
        $newGoods = $existGoods = $errorMaxNum = $standbyGoods = $errorGoodsName = [];
        $gcModel = new GoodsCodeModel();
        foreach ($data as $key => $value) {
            if ($key == 0) {
                continue;
            }
            //检查上传商品数据中是否有新品，如果有则禁止上传
            $platformBarcode = trim($value[1]);
            $specSn = $gcModel->getSpecSn($platformBarcode);
            if (empty($specSn)) {
                $newGoods[] = $key + 1;
                continue;
            }
            //检查上传商品是否已经存在于常备商品中
            $count = $this->countStandbyGoods($specSn);
            if ($count > 0) {
                $existGoods[] = $key + 1;
                continue;
            }
            //检查上传商品数据中最大采购量是否正常，如果有小于或等于0的禁止上传
            $maxNum = intval($value[2]);
            if ($maxNum <= 0) {
                $errorMaxNum[] = $key + 1;
                continue;
            }

            //检查商品名称
            $goodsName = trim($value[0]);
            if (empty($goodsName)) {
                $errorGoodsName[] = $key + 1;
                continue;
            }
            $standbyGoods[] = [
                'goods_name' => trim($value[0]),
                'spec_sn' => $specSn,
                'platform_barcode' => trim($value[1]),
                'max_num' => intval($value[2]),
            ];
        }
        return [
            'newGoods' => $newGoods,
            'existGoods' => $existGoods,
            'errorMaxNum' => $errorMaxNum,
            'standbyGoods' => $standbyGoods,
            'errorGoodsName' => $errorGoodsName,
        ];

    }//end of function checkUploadData

    /**
     * description:获取常备商品列表
     * editor:zongxing
     * date : 2019.09.17
     * return Array
     */
    public function standbyGoodsList($param_info, $is_page = 0, $spec_arr = [])
    {
        $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $field = $this->field;
        $add_field = [
            'gs.spec_price', 'b.name as brand_name', 'gs.erp_merchant_no', 'gs.erp_ref_no', 'gs.stock_num',
            'gs.erp_prd_no', 'b.brand_id',
        ];
        $field = array_merge($field, $add_field);

        $where = [];
        if (!empty($param_info['lt_prd_no'])) {
            $lt_prd_no = trim($param_info['lt_prd_no']);
            $where[] = ['lt_prd_no', $lt_prd_no];
        } 
        if (!empty($param_info['erp_prd_no'])) {
            $erp_prd_no = trim($param_info['erp_prd_no']);
            $where[] = ['erp_prd_no', $erp_prd_no];
        } 
        if (!empty($param_info['erp_ref_no'])) {
            $erp_ref_no = trim($param_info['erp_ref_no']);
            $where[] = ['erp_ref_no', $erp_ref_no];
        } 
        if (!empty($param_info['spec_sn'])) {
            $spec_sn = trim($param_info['spec_sn']);
            $where[] = ['sg.spec_sn', $spec_sn];
        } 
        if (!empty($param_info['platform_barcode'])) {
            $platform_barcode = trim($param_info['platform_barcode']);
            $where[] = ['sg.platform_barcode', $platform_barcode];
        } 
        if (!empty($param_info['erp_merchant_no'])) {
            $erp_merchant_no = trim($param_info['erp_merchant_no']);
            $where[] = ['gs.erp_merchant_no', $erp_merchant_no];
        } 
        
        if (!empty($param_info['goods_name']) || !empty($param_info['query_sn'])) {
            $query_sn = !empty($param_info['goods_name']) ? $param_info['goods_name'] : $param_info['query_sn'];
            $total_info = $this->createQueryGoodsList($query_sn);

            $tmp_goods_str = '';
            if (isset($total_info['total_arr']) && !empty($total_info['total_arr'])) {
                $total_arr = $total_info['total_arr'];
                foreach ($total_arr as $k1 => $v1) {
                    if (!empty($v1)) {
                        foreach ($v1 as $k => $v) {
                            $v = '%' . trim($v) . '%';
                            $where[] = ['sg.goods_name', 'like', $v];
                        } 
                    } 
                }
            }
        }

        $orWhere = [];
        if (!empty($param_info['brand_name'])) {
            $brand_name = trim($param_info['brand_name']);
            $brand_name_arr = reformKeywords($brand_name);
            foreach ($brand_name_arr as $k1 => $v1) {
                if (!empty($v1)) {
                    foreach ($v1 as $k => $v) {
                        $v = '%' . trim($v) . '%';
                        $orWhere[] = ['b.name', 'like', $v];
                    } 
                } 
            }
        } 

        $sg_obj = DB::table('standby_goods as sg')->select($field)
            ->leftJoin('goods_spec as gs', 'gs.spec_sn', '=', 'sg.spec_sn')
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id');

        if (isset($param_info['is_purchase'])) {
            $is_purchase = intval($param_info['is_purchase']);
            $sg_obj->where('sg.is_purchase', $is_purchase);
        }
        if (isset($param_info['no_zero'])) {
            $sg_obj->where('sg.max_num', '!=', 0)
                ->where('sg.max_num', '>', DB::raw('jms_sg.available_num'));
        }
        if (!empty($spec_arr)) {
            $sg_obj->whereIn('sg.spec_sn', $spec_arr);
        }
        if ($is_page) {
            $standby_goods_list = $sg_obj->where($where)
            ->where(function($query)use($orWhere){
                $query->orWhere($orWhere);
            })
            ->orderBy('sg.create_time', 'DESC')->paginate($page_size);
        } else {
            $standby_goods_list = $sg_obj->where($where)
            ->where(function($query)use($orWhere){
                $query->orWhere($orWhere);
            })
            ->orderBy('sg.create_time', 'DESC')->get();
        }
        $standby_goods_list = objectToArrayZ($standby_goods_list);
        return $standby_goods_list;
    }

    /**
     * description:组装商品列表搜索条件
     * editor:zongxing
     * date : 20020.02.13
     * return Json
     */
    public function createQueryGoodsList($query_sn)
    {
        //处理搜索字符串
        $query_sn = str_replace(' ', '', $query_sn);
        $query_sn = replace_specialChar($query_sn);
        $query_sn = strtolower($query_sn);
        $query_sn = replace_unitChar($query_sn);

        //先查询品牌信息
        $brand_obj = DB::table('brand as b');
        $query_sn_arr = split_str($query_sn);
        if (!empty($query_sn_arr)) {
            foreach ($query_sn_arr as $k => $v) {
                $v = '%' . trim($v) . '%';
                $brand_obj->orWhere(function ($where) use ($v) {
                    $where->orWhere('b.name', 'LIKE', $v);
                    $where->orWhere('b.name_en', 'LIKE', $v);
                    $where->orWhere('b.name_cn', 'LIKE', $v);
                    $where->orWhere('b.name_alias', 'LIKE', $v);
                    $where->orWhere('b.keywords', 'LIKE', $v);
                });
            }    
        }
        $brand_info = $brand_obj->orderBy('modify_time','desc')->get();
        $brand_info = ObjectToArrayZ($brand_info);

        //去除搜索字段中的品牌信息
        $brand_id_arr = $common_str_arr = [];
        foreach ($brand_info as $k => $v) {
            $brand_id_arr[] = intval($v['brand_id']);
            $name_arr = [trim($v['name']), trim($v['name_en']), trim($v['name_cn'])];
            foreach ($name_arr as $k => $v) {
                $v = strtolower($v);
                $common_str = find_common_str($v, $query_sn);
                if($common_str && !in_array($common_str, $common_str_arr)) {
                    $common_str_arr[] = $common_str;
                }
            }
        }

        $encoding = 'utf8';
        if (!empty($common_str_arr)) {
            foreach ($common_str_arr as $k => $v) {
                $start = mb_strpos($query_sn, $v);
                $tmp_str = find_common_str($v, $query_sn);
                $query_sn_len = mb_strlen($query_sn, $encoding); 
                $common_str_len = mb_strlen($tmp_str, $encoding);

                $last_start_index = $start + $common_str_len;
                $last_len = $query_sn_len - $common_str_len - $start;

                $query_sn = mb_substr($query_sn, 0, $start) .
                    mb_substr($query_sn, $last_start_index, $last_len);
            }
        }

        if (!empty($brand_id_arr)) {
            $return_info['brand_id_arr'] = $brand_id_arr;
        }

        preg_match_all('%[0-9_-]{2,}%', $query_sn, $number_arr);
        preg_match_all('%[A-Za-z_-]{1,}%', $query_sn, $str_arr);
        preg_match_all('%[\x{4e00}-\x{9fa5}]%u', $query_sn, $gbk_arr);
        $gbk_str = implode("", $gbk_arr[0]);
        $gbk_arr = split_str($gbk_str);
        $total_arr = [$number_arr[0],  $str_arr[0], $gbk_arr];
        $return_info['total_arr'] = $total_arr;
        return $return_info;
    }


    /**
     * description:获取常备商品详情
     * editor:zongxing
     * date : 2019.09.17
     * return Array
     */
    public function standbyGoodsInfo($param_info)
    {
        $id = $param_info['id'];
        $field = $this->field;
        $add_field = [
            'gs.spec_price', 'b.name as brand_name', 'gs.erp_merchant_no', 'gs.erp_ref_no', 'gs.stock_num',
        ];
        $field = array_merge($field, $add_field);
        $sg_obj = DB::table('standby_goods as sg')->select($field)
            ->leftJoin('goods_spec as gs', 'gs.spec_sn', '=', 'sg.spec_sn')
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id');
        $standby_goods_info = $sg_obj->where('id', $id)->first();
        $standby_goods_info = objectToArrayZ($standby_goods_info);
        return $standby_goods_info;
    }

    /**
     * description:编辑常备商品
     * editor:zongxing
     * date : 2019.09.17
     * return Array
     */
    public function doEditStandbyGoods($param_info, $standby_goods_info)
    {
        $id = $param_info['id'];
        $update_data = [];
        if (isset($param_info['max_num'])) {
            $max_num = intval($param_info['max_num']);
            $available_num = intval($standby_goods_info['available_num']);
            $is_purchase = 1;
            if ($max_num <= $available_num) {
                $is_purchase = 0;
            }
            $update_data = [
                'max_num' => $max_num,
                'is_purchase' => $is_purchase,
            ];
        }
        if (isset($param_info['is_purchase'])) {
            $is_purchase = intval($param_info['is_purchase']);
            $update_data['is_purchase'] = $is_purchase;
        }
        $res = DB::table('standby_goods')->where('id', $id)->update($update_data);
        return $res;
    }


}//end of class
