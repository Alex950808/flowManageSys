<?php

namespace App\Model\Vone;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class GoodsModel extends Model
{
    //商品表
    protected $table = "goods as g";
    //商品规格表
    protected $spec_table = "goods_spec";
    //商品平台信息
    public $platform = [
        1 => 'ERP商家编码',
        2 => '考拉',
        3 => '小红书',
        4 => '商品代码',
    ];

    //商品平台信息-英文对照
    public $platform_no = [
        'ERP_NO' => 1,
        'KAOLA_NO' => 2,
        'SMALL_RED_BOOK' => 3,
        'ERP_PRD_NO' => 4,
    ];

    public $field = [
        'g.goods_id', 'g.goods_sn', 'g.goods_name', 'g.cat_id', 'g.brand_id', 'g.create_time',
    ];


    /**
     * description:获取商品信息
     * editor:zhangdong
     * date : 2018.06.26
     * params: $queryType :查询方式 1，根据商家编码查询 2，根据规格码查询
     * return Object
     */
    public function getGoodsInfo($queryData, $queryType)
    {
        $queryType = intval($queryType);
        if ($queryType == 1) {
            $field = 'erp_merchant_no';
        } elseif ($queryType == 2) {
            $field = 'spec_sn';
        } else {
            return false;
        }
        $selectFields = 'gs.goods_sn,gs.spec_sn,g.goods_name,gs.erp_prd_no,gs.erp_merchant_no,
        gs.spec_price,gs.spec_weight,gs.exw_discount,gs.stock_num,g.brand_id';
        $queryRes = DB::table(DB::raw('jms_goods_spec AS gs'))->selectRaw($selectFields)
            ->leftJoin(DB::raw('jms_goods AS g'), DB::raw('gs.goods_sn'), '=', DB::raw('g.goods_sn'))
            ->where($field, $queryData)->first();
        return $queryRes;
    }

    /**
     * description:根据关键字获取商品信息
     * editor:zhangdong
     * date : 2018.06.26
     * params: $queryType :查询方式 1，根据商家编码查询 2，根据规格码查询
     * return Object
     */
    public function getGoodsByKeywords($keywords)
    {
        $keywords = trim($keywords);
        $selectFields = 'gs.goods_sn,gs.spec_sn,g.goods_name,gs.erp_prd_no';
        $queryRes = DB::table(DB::raw('jms_goods_spec AS gs'))->selectRaw($selectFields)
            ->leftJoin(DB::raw('jms_goods AS g'), DB::raw('gs.goods_sn'), '=', DB::raw('g.goods_sn'))
            ->where(DB::raw('gs.storehouse_id'), 2001)
            ->where(function ($query) use ($keywords) {
                $query->orWhere(DB::raw('gs.erp_merchant_no'), 'LIKE', "%$keywords%")
                    ->orWhere(DB::raw('g.goods_name'), 'LIKE', "%$keywords%")
                    ->orWhere(DB::raw('gs.erp_prd_no'), 'LIKE', "%$keywords%");
            })->get();
        return $queryRes;

    }

    /**
     * description:组装商品实时动销率数据
     * editor:zhangdong
     * date : 2018.07.05
     * return Object
     */
    public function createGoodsMovePer(array $param_info)
    {
        $where = [];
        if (!empty($param_info['erp_merchant_no'])) {
            $erp_merchant_no = trim($param_info['erp_merchant_no']);
            $where[] = ['gs.erp_merchant_no', $erp_merchant_no];
        }
        if (!empty($param_info['erp_prd_no'])) {
            $erp_prd_no = trim($param_info['erp_prd_no']);
            $where[] = ['gs.erp_prd_no', $erp_prd_no];
        }
        if (!empty($param_info['erp_ref_no'])) {
            $erp_ref_no = trim($param_info['erp_ref_no']);
            $where[] = ['gs.erp_ref_no', $erp_ref_no];
        }
        if (!empty($param_info['spec_sn'])) {
            $spec_sn = trim($param_info['spec_sn']);
            $where[] = ['gs.spec_sn', $spec_sn];
        }
        if (!empty($param_info['goods_name']) || !empty($param_info['query_sn'])) {
            $query_sn = !empty($param_info['goods_name']) ? $param_info['goods_name'] : $param_info['query_sn'];
            $total_info = $this->createQueryGoodsList($query_sn);
            if (isset($total_info['total_arr']) && !empty($total_info['total_arr'])) {
                $total_arr = $total_info['total_arr'];
                foreach ($total_arr as $k1 => $v1) {
                    if (!empty($v1)) {
                        foreach ($v1 as $k => $v) {
                            $v = '%' . trim($v) . '%';
                            $where[] = ['g.goods_name', 'like', $v];
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

        $field = ['g.goods_name', 'b.name as brand_name', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.erp_prd_no', 'gs.erp_ref_no',
            DB::raw('SUM(jms_sg.goods_num) as total_need_num'),
            DB::raw('SUM(jms_rpd.allot_num) as realAllotNum')
        ];

        $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $queryRes = DB::table('goods_spec as gs')->select($field)
            ->leftJoin('goods as g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id')
            ->leftJoin('sum_goods as sg', 'sg.spec_sn', '=', 'gs.spec_sn')
            ->leftJoin('real_purchase_detail as rpd', 'rpd.spec_sn', '=', 'gs.spec_sn')
            ->where($where)
            ->where(function ($query) use ($orWhere) {
                $query->orWhere($orWhere);
            })
            ->groupBy('sg.spec_sn')
            ->paginate($page_size);
        if (empty($queryRes->items())) {
            return false;
        }
        //计算订单销售量
        foreach ($queryRes as $key => $value) {
            $spec_sn = $value->spec_sn;
            $where = [
                ['spec_sn', $spec_sn]
            ];
            $field = 'SUM(num) AS total_sale_num';
            $orderGoods = DB::table('order_goods')->selectRaw($field)->where($where)->first();
            $total_sale_num = intval($orderGoods->total_sale_num);
            $queryRes[$key]->total_sale_num = $total_sale_num;
            $realAllotNum = intval($value->realAllotNum);
            //计算商品动销率（订单销售数量/商品实际分配数量）
            $rate_of_pin = $realAllotNum > 0 ? round($total_sale_num / $realAllotNum, DECIMAL_DIGIT) : 0;
            $queryRes[$key]->rate_of_pin = sprintf('%.2f%%', $rate_of_pin * 100);
        }
        return $queryRes;
    }

    /**
     * description:组装商品实时动销率数据
     * editor:zhangdong
     * date : 2018.07.05
     * return Object
     */
    public function createGoodsMovePer_stop(array $arrParams)
    {
        //搜索关键字
        $keywords = $arrParams['keywords'];
        $orWhere = [];
        if ($keywords) {
            $orWhere = [
                'orWhere1' => [
                    ['erp_merchant_no', 'LIKE', "%$keywords%"],
                ],
                'orWhere2' => [
                    ['spec_sn', 'LIKE', "%$keywords%"],
                ],
                'orWhere3' => [
                    ['goods_name', 'LIKE', "%$keywords%"],
                ],
            ];
        }
        $selectFields = 'g.goods_name,gs.erp_merchant_no,gs.spec_sn,SUM(dc.goods_num) AS total_need_num,SUM(rpd.allot_num) AS realAllotNum';
        $queryRes = DB::table(DB::raw('jms_goods_spec AS gs'))->selectRaw($selectFields)
            ->leftJoin(DB::raw('jms_goods AS g'), DB::raw('gs.goods_sn'), '=', DB::raw('g.goods_sn'))
            ->join(DB::raw('jms_demand_count AS dc'), DB::raw('dc.spec_sn'), '=', DB::raw('gs.spec_sn'))
            ->join(DB::raw('jms_real_purchase_detail AS rpd'), DB::raw('rpd.spec_sn'), '=', DB::raw('gs.spec_sn'))
            ->where(function ($result) use ($orWhere) {
                if (count($orWhere) >= 1) {
                    $result->orWhere($orWhere['orWhere1'])
                        ->orWhere($orWhere['orWhere2'])
                        ->orWhere($orWhere['orWhere3']);
                }
            })
            ->groupBy(DB::raw('dc.spec_sn'))
            ->paginate(15);
        //计算订单销售量
        foreach ($queryRes as $key => $value) {
            $spec_sn = $value->spec_sn;
            $where = [
                ['spec_sn', $spec_sn]
            ];
            $field = 'SUM(num) AS total_sale_num';
            $orderGoods = DB::table('order_goods')->selectRaw($field)->where($where)->first();
            $total_sale_num = intval($orderGoods->total_sale_num);
            $queryRes[$key]->total_sale_num = $total_sale_num;
            $realAllotNum = intval($value->realAllotNum);
            //计算商品动销率（订单销售数量/商品实际分配数量）
            $rate_of_pin = $realAllotNum > 0 ? round($total_sale_num / $realAllotNum, DECIMAL_DIGIT) : 0;
            $queryRes[$key]->rate_of_pin = sprintf('%.2f%%', $rate_of_pin * 100);
        }
        return $queryRes;

    }

    /**
     * description:获取商品分类信息
     * editor:zongxing
     * date : 2018.08.30
     * return Object
     */
    public function getGoodsCategory()
    {
        $category_total_info = DB::table("category")->orderBy("cat_name", "ASC")->get(["cat_id", "cat_name", "parent_id"]);
        $category_total_info = objectToArrayZ($category_total_info);
        $category_arr = $this->get_all_child($category_total_info, 0);

        return $category_arr;
    }

    /**
     * description:递归获取所有的子分类的信息
     * editor:zongxing
     * date : 2018.08.30
     * return Object
     */
    public function get_all_child($arr, $parent_id)
    {
        $list = array();
        foreach ($arr as $val) {
            if ($val["parent_id"] == $parent_id) {
                $tmp = $this->get_all_child($arr, $val["cat_id"]);
                if ($tmp) {
                    $val['child'] = $tmp;
                }
                $list[] = $val;
            }
        }
        return $list;
    }

    /**
     * description:获取商品分品牌信息
     * editor:zhangdong
     * date : 2018.08.17
     * return Object
     */
    public function getGoodsBrand()
    {
        $field = 'brand_id,name';
        $goodsBrand = DB::table('brand')->selectRaw($field)->orderBy("name", "ASC")->get();
        return $goodsBrand;
    }

    /**
     * description:获取仓库信息
     * editor:zongxing
     * date : 2018.08.23
     * return Object
     */
    public function getStorehouseInfo()
    {
        $goodsStorehouse = DB::table('storehouse')->get(["store_id", "store_location"]);
        $goodsStorehouse = json_decode(json_encode($goodsStorehouse), true);
        return $goodsStorehouse;
    }

    /**
     * description:通过名称获取商品信息
     * editor:zongxing
     * date : 2018.11.16
     * return Object
     */
    public function getGoodsByName($goods_name)
    {
        $goods_info = DB::table("goods")->where("goods_name", $goods_name)->first();
        return $goods_info;
    }

    /**
     * description:通过商品货号获取商品规格信息
     * editor:zongxing
     * date : 2018.11.16
     * return Object
     */
    public function getGoodsByGoodsSn($goods_sn)
    {
        $goods_info = DB::table('goods')->where('goods_sn', $goods_sn)->first(['goods_sn']);
        $goods_info = objectToArrayZ($goods_info);
        return $goods_info;
    }

    /**
     * description:新增单个商品
     * editor:zongxing
     * date : 2018.08.24
     * return Object
     */
    public function doAddGoods($param_info)
    {
        //新增商品
        $insert_goods_spec_info = [
            'goods_sn' => trim($param_info['goods_sn']),
            'spec_sn' => trim($param_info['spec_sn']),
            'erp_merchant_no' => empty($param_info['erp_merchant_no']) ? '' : trim($param_info['erp_merchant_no']),
            'erp_prd_no' => empty($param_info['erp_prd_no']) ? '' : trim($param_info['erp_prd_no']),
            'erp_ref_no' => empty($param_info['erp_ref_no']) ? '' : trim($param_info['erp_ref_no']),
            'spec_price' => empty($param_info['spec_price']) ? 0 : floatval($param_info['spec_price']),
            'spec_weight' => empty($param_info['spec_weight']) ? 0 : floatval($param_info['spec_weight']),
            'estimate_weight' => empty($param_info['estimate_weight']) ? 0 : floatval($param_info['estimate_weight']),
            'stock_num' => empty($param_info['stock_num']) ? 0 : intval($param_info['stock_num']),
            'goods_label' => empty($param_info['goods_label']) ? '' : trim($param_info['goods_label']),
            'exw_discount' => empty($param_info['exw_discount']) ? 0 : floatval($param_info['exw_discount']),
            'spec_img' => empty($param_info['spec_img']) ? '' : trim($param_info['spec_img'])
        ];

        //新增商品码
        $insert_goods_code = [];
        $platform_no = $this->platform_no;
        foreach ($platform_no as $k => $v) {
            if (!empty($param_info[$k])) {
                $insert_goods_code[] = [
                    'code_type' => $v,
                    'goods_code' => trim($param_info[$k]),
                    'spec_sn' => trim($param_info['spec_sn'])
                ];
            }
        }

        $insert_goods_info = [
            'goods_name' => trim($param_info['goods_name']),
            'goods_sn' => trim($param_info['goods_sn']),
            'cat_id' => intval($param_info['cat_id']),
            'brand_id' => intval($param_info['brand_id'])
        ];

        $insertRes = DB::transaction(function () use ($insert_goods_info, $insert_goods_spec_info, $insert_goods_code) {

            // 添加商品码信息
            DB::table('goods_code')->insert($insert_goods_code);
            // 添加商品信息
            DB::table('goods')->insertGetId($insert_goods_info);
            $add_goods_spec_res = DB::table('goods_spec')->insertGetId($insert_goods_spec_info);

            //更新商品关键字
            $goods_sn = $insert_goods_info['goods_sn'];
            //$this->updateKeywords($goods_sn);
            return $add_goods_spec_res;
        });

        $return_info = false;
        if ($insertRes !== false) {
            $return_info['spec_id'] = $insertRes;
            $return_info['goods_info'] = $insert_goods_info;
        }
        return $return_info;
    }

    /**
     * description:提交编辑商品
     * editor:zongxing
     * date : 2020.02.14
     * return Object
     */
    public function doEditGoods($param_info, $goods_info)
    {
        //更新商品信息
        $update_goods = [];
        if (trim($param_info['goods_name']) != $goods_info['goods_name']) {
            $update_goods['goods_name'] = trim($param_info['goods_name']);
        } elseif (intval($param_info['cat_id']) != $goods_info['cat_id']) {
            $update_goods['cat_id'] = intval($param_info['cat_id']);
        } elseif (intval($param_info['brand_id']) != $goods_info['brand_id']) {
            $update_goods['brand_id'] = intval($param_info['brand_id']);
        }
        //更新商品规格信息
        $erp_merchant_no = empty($param_info['erp_merchant_no']) ? '' : trim($param_info['erp_merchant_no']);
        $erp_prd_no = empty($param_info['erp_prd_no']) ? '' : trim($param_info['erp_prd_no']);
        $erp_ref_no = empty($param_info['erp_ref_no']) ? '' : trim($param_info['erp_ref_no']);
        $spec_price = empty($param_info['spec_price']) ? 0 : floatval($param_info['spec_price']);
        $spec_weight = empty($param_info['spec_weight']) ? 0 : floatval($param_info['spec_weight']);
        $estimate_weight = empty($param_info['estimate_weight']) ? 0 : floatval($param_info['estimate_weight']);
        $stock_num = empty($param_info['stock_num']) ? 0 : intval($param_info['stock_num']);
        $goods_label = empty($param_info['goods_label']) ? '' : trim($param_info['goods_label']);
        $exw_discount = empty($param_info['exw_discount']) ? 0 : floatval($param_info['exw_discount']);
        $spec_img = empty($param_info['spec_img']) ? 0 : trim($param_info['spec_img']);
        $update_goods_spec = [];
        if ($erp_merchant_no != $goods_info['erp_merchant_no']) {
            $update_goods_spec['erp_merchant_no'] = $erp_merchant_no;
        } elseif ($erp_prd_no != $goods_info['erp_prd_no']) {
            $update_goods_spec['erp_prd_no'] = trim($param_info['erp_prd_no']);
        } elseif ($erp_ref_no != $goods_info['erp_ref_no']) {
            $update_goods_spec['erp_ref_no'] = trim($param_info['erp_ref_no']);
        } elseif ($spec_price != $goods_info['spec_price']) {
            $update_goods_spec['spec_price'] = floatval($param_info['spec_price']);
        } elseif ($spec_weight != $goods_info['spec_weight']) {
            $update_goods_spec['spec_weight'] = floatval($param_info['spec_weight']);
        } elseif ($estimate_weight != $goods_info['estimate_weight']) {
            $update_goods_spec['estimate_weight'] = floatval($param_info['estimate_weight']);
        } elseif ($stock_num != $goods_info['stock_num']) {
            $update_goods_spec['stock_num'] = intval($param_info['stock_num']);
        } elseif ($goods_label != $goods_info['goods_label']) {
            $update_goods_spec['goods_label'] = trim($param_info['goods_label']);
        } elseif ($exw_discount != $goods_info['exw_discount']) {
            $update_goods_spec['exw_discount'] = floatval($param_info['exw_discount']);
        } elseif ($spec_img != $goods_info['spec_img']) {
            $update_goods_spec['spec_img'] = trim($param_info['spec_img']);
        }
        //更新商品码信息
        $update_goods_code = $insert_goods_code = [];
        $platform_no = $this->platform_no;
        foreach ($platform_no as $k => $v) {
            if (!empty($param_info[$k])) {
                if (!isset($goods_info[$k])) {
                    $tmp_arr = [
                        'code_type' => $v,
                        'goods_code' => trim($param_info[$k]),
                        'spec_sn' => trim($param_info['spec_sn'])
                    ];
                    $insert_goods_code[] = $tmp_arr;
                    continue;
                }
                if (isset($goods_info[$k]) && trim($param_info[$k]) !== trim($goods_info[$k])) {
                    $update_goods_code['goods_code'][] = [
                        $v => "'" . trim($param_info[$k]) . "'"
                    ];
                }
            }
        }
        // 组装批量更新语句
        $where['spec_sn'] = trim($param_info['spec_sn']);
        $updateGoodsCodeSql = '';
        if (!empty($update_goods_code)) {
            // 需要判断的字段
            $column = 'code_type';
            $updateGoodsCodeSql = makeBatchUpdateSql('jms_goods_code', $update_goods_code, $column, $where);
        }
        //执行更新
        $updateRes = DB::transaction(function () use (
            $goods_info, $update_goods, $update_goods_spec,
            $updateGoodsCodeSql, $insert_goods_code
        ) {
            $res = true;
            // 更新商品码表
            if ($updateGoodsCodeSql != '') {
                $res = DB::update(DB::raw($updateGoodsCodeSql));
            }
            // 添加商品码信息
            if (!empty($insert_goods_code)) {
                $res = DB::table('goods_code')->insert($insert_goods_code);
            }
            // 更新商品信息
            $goods_id = intval($goods_info['goods_id']);
            $spec_sn = trim($goods_info['spec_sn']);
            if (!empty($update_goods_spec)) {
                $res = DB::table('goods_spec')->where('spec_sn', $spec_sn)->update($update_goods_spec);
            }
            if (!empty($update_goods)) {
                $res = DB::table('goods')->where('goods_id', $goods_id)->update($update_goods);
            }
            return $res;
        });
        return $updateRes;
    }


    /**
     * description:新增单个商品规格
     * editor:zongxing
     * date : 2018.08.24
     * update: 2018.11.15
     * return Object
     */
    public function doAddGoodsSpec($param_info)
    {
        //生成商品spec_sn
        $goods_sn = trim($param_info["goods_sn"]);
        $spec_sn = $this->get_spec_sn($goods_sn);
        //组装新增商品规格数据
        $goods_spec_info = [
            'goods_sn' => $goods_sn,
            'spec_sn' => $spec_sn,
            'spec_price' => floatval($param_info["spec_price"]),
            'exw_discount' => floatval($param_info["exw_discount"]),
        ];
        //组装商品规格信息
        $tmp_spec_arr = ['erp_merchant_no', 'erp_prd_no', 'erp_ref_no', 'goods_label', 'spec_weight', 'estimate_weight'];
        foreach ($tmp_spec_arr as $k => $v) {
            if (isset($param_info[$v])) {
                $tmp_value = trim($param_info[$v]);
                if ($k > 3) {
                    $tmp_value = floatval($param_info[$v]);
                }
                $goods_spec_info[$v] = $tmp_value;
            }
        }
        //组装商品码信息
        $tmp_code_arr = ['erp_merchant_no', 'kl_code', 'red_code'];
        $goods_code_info = [];
        foreach ($tmp_code_arr as $k => $v) {
            if (isset($param_info[$v])) {
                $tmp_value = trim($param_info[$v]);
                $tmp_arr = [
                    'code_type' => $k + 1,
                    'goods_code' => $tmp_value,
                    'spec_sn' => $spec_sn,
                ];
                $goods_code_info[] = $tmp_arr;
            }
        }
        $insertRes = DB::transaction(function () use ($goods_spec_info, $goods_code_info) {
            if (!empty($goods_code_info)) {
                DB::table('goods_code')->insert($goods_code_info);
            }
            $insertRes = DB::table('goods_spec')->insertGetId($goods_spec_info);
            return $insertRes;
        });
        return $insertRes;
    }


    /**
     * description:拼装批量增加商品的信息
     * editor:zongxing
     * date : 2018.08.24
     * return Object
     */
    public function create_goods_info($res)
    {
        $return_goods_info = [];
        $return_goods_spec_info = [];
        $add_erp_merchant_no = [];
        foreach ($res as $k => $v) {
            if ($k === 0) continue;
            if (empty($v[0])) {
                $return_info = ['code' => '1006', 'msg' => '商品名称不能为空'];
                return $return_info;
            }
            //创建商品goods_sn
            $goods_sn = $this->get_goods_sn();
            //创建商品spec_sn
            $spec_sn = $this->get_spec_sn($goods_sn);

            $cat_id = intval($v[4]);
            $brand_id = intval($v[5]);
            $cat_name = trim($v[8]);
            $brand_name = trim($v[9]);
            $tmp_goods["goods_name"] = trim($v[0]);
            $tmp_goods["cat_id"] = $cat_id;
            $tmp_goods["brand_id"] = $brand_id;
            $tmp_goods["goods_sn"] = $goods_sn;
            $tmp_goods["keywords"] = $tmp_goods["goods_name"] . '--' . $cat_name . '--' . $brand_name;
            $return_goods_info[] = $tmp_goods;

            $tmp_goods_spec["goods_sn"] = $goods_sn;
            $tmp_goods_spec["spec_sn"] = $spec_sn;

            $tmp_goods_spec["erp_merchant_no"] = '';
            if (isset($v[1]) && !empty($v[1])) {
                $erp_merchant_no = trim($v[1]);
                array_push($add_erp_merchant_no, $erp_merchant_no);
                $tmp_goods_spec["erp_merchant_no"] = $v[1];
            }
            $tmp_goods_spec["erp_prd_no"] = '';
            if (isset($v[2]) && !empty($v[2])) {
                $tmp_goods_spec["erp_prd_no"] = trim($v[2]);
            }
            $tmp_goods_spec["erp_ref_no"] = '';
            if (isset($v[3]) && !empty($v[3])) {
                $tmp_goods_spec["erp_ref_no"] = trim($v[3]);
            }
            $tmp_goods_spec["spec_price"] = floatval($v[6]);
            $tmp_goods_spec["spec_weight"] = floatval($v[7]);
            $return_goods_spec_info[] = $tmp_goods_spec;
        }

        $diff_erp_merchant_no = '';
        if (!empty($add_erp_merchant_no)) {
            $erp_merchant_no_info = DB::table("goods_spec")->whereIn("erp_merchant_no", $add_erp_merchant_no)->pluck("erp_merchant_no");
            $erp_merchant_no_info = objectToArrayZ($erp_merchant_no_info);

            if (!empty($erp_merchant_no_info)) {
                $diff_erp_merchant_no = implode(",", $erp_merchant_no_info);
            }
        }

        $return_info["goods_info"] = $return_goods_info;
        $return_info["goods_spec_info"] = $return_goods_spec_info;
        $return_info["diff_erp_merchant_no"] = $diff_erp_merchant_no;
        return $return_info;
    }

    /**
     * description:新增商品-生成goods_sn
     * editor:zongxing
     * date : 2018.08.24
     */
    public function get_goods_sn($min = 0, $max = 1)
    {
        $randomcode = "";
        // 用字符数组的方式随机
        $str = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str_arr = str_split($str);
        for ($j = 0; $j < 6; $j++) {
            $rand_num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
            $rand_str = $str_arr[(int)($rand_num * 36)];
            if (strpos($randomcode, $rand_str) !== false) {
                $j--;
                continue;
            }
            $randomcode = $randomcode . $rand_str;
        }

        $sn = rand(10000, 99999);
        $goods_sn = $randomcode . $sn;
        $goods_info = $this->getGoodsByGoodsSn($goods_sn);
        if (!empty($goods_info)) {
            $this->get_goods_sn();
        }
        return $goods_sn;
    }

    /**
     * description:检查商品信息
     * editor:zongxing
     * date : 2018.08.24
     */
    public function check_goods_info($goods_id)
    {
        $goods_info = DB::table("goods")->where("goods_id", $goods_id)->first(["goods_id"]);
        $goods_info = json_decode(json_encode($goods_info), true);
        return $goods_info;
    }

    /**
     * description:获取商品信息
     * editor:zongxing
     * date : 2018.08.30
     */
    public function getGoodsDetailInfo($goods_id)
    {
        $goods_info = DB::table("goods as g")
            ->select('goods_id', 'goods_name', 'ext_name', 'g.brand_id', 'g.cat_id', 'goods_sn', 'g.keywords',
                DB::raw("CONCAT(jms_g.goods_name,'--',jms_c.cat_name,'--',jms_b.keywords) AS keywords"))
            ->leftJoin("category as c", "c.cat_id", "=", "g.cat_id")
            ->leftJoin("brand as b", "b.brand_id", "=", "g.brand_id")
            ->where('goods_id', $goods_id)
            ->first();
        $goods_info = objectToArrayZ($goods_info);
        if (!empty($goods_info)) {
            //过滤关键字的特殊字符
            $common_model = new CommonModel();
            $goods_info['keywords'] = $common_model->strFilter($goods_info['keywords']);
        }
        return $goods_info;
    }

    /**
     * description:获取商品规格信息
     * editor:zongxing
     * date : 2018.08.30
     */
    public function get_goods_spec_info($spec_id)
    {
        $goods_spec_info = DB::table("goods_spec")->where("spec_id", $spec_id)->first();
        $goods_spec_info = objectToArrayZ($goods_spec_info);
        $goods_spec_info['goods_label'] = explode(',', $goods_spec_info['goods_label']);
        return $goods_spec_info;
    }

    /**
     * description:新增商品-生成spec_sn
     * editor:zongxing
     * date : 2018.08.24
     */
    private function get_spec_sn_stop($goods_sn)
    {
        $sn = rand(100, 999);
        $spec_sn = $goods_sn . $sn;
        $spec_sn_search = DB::table("goods_spec")->where("spec_sn", $spec_sn)->first();
        $spec_sn_search = objectToArrayZ($spec_sn_search);

        if ($spec_sn_search) {
            return $this->get_spec_sn($goods_sn);
        } else {
            return $spec_sn;
        }
    }

    public function get_spec_sn($goods_sn)
    {
        $sn = rand(100, 999);
        $spec_sn = $goods_sn . $sn;
        return $spec_sn;
    }

    /**
     * description:获取商品仓位id
     * editor:zongxing
     * date : 2018.08.24
     */
    private function get_storehouse_sn($storehouse_name)
    {
        $str = "%" . $storehouse_name . "%";
        $store_info_search = DB::table("storehouse")
            ->where("store_name", "like", $str)
            ->orWhere("store_location", "like", $str)
            ->first();
        $store_info_search = json_decode(json_encode($store_info_search), true);

        $store_id = "";
        if ($store_info_search) {
            $store_id = $store_info_search["store_id"];
        }
        return $store_id;
    }

    /**
     * description:更新商品关键字
     * editor:zongxing
     * params:商品goods_sn:$goods_sn
     * date : 2018.08.24
     */
    public function updateKeywords($goods_sn)
    {
        $keywords_search = DB::table("goods as g")
            ->select(DB::raw("CONCAT(jms_g.goods_name,'--',jms_c.cat_name,'--',jms_b.keywords) AS keywords"))
            ->leftJoin("category as c", "c.cat_id", "=", "g.cat_id")
            ->leftJoin("brand as b", "b.brand_id", "=", "g.brand_id")
            ->where("goods_sn", $goods_sn)
            ->first(['keywords']);
        $keywordsRes = objectToArrayZ($keywords_search);

        //过滤关键字的特殊字符
        $common_model = new CommonModel();
        $keywords['keywords'] = $common_model->strFilter($keywordsRes['keywords']);

        $updateRes = DB::table("goods")->where("goods_sn", $goods_sn)->update($keywords);
        return $updateRes;
    }

    /**
     * description:获取商品列表
     * editor:zongxing
     * date : 2018.08.25
     * return object
     */
    public function goodsList_stop($param_info)
    {
        $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $field = [
            'g.goods_id', 'g.goods_name', 'b.name as brand_name', 'c.cat_name', 'g.goods_sn', 'gs.goods_label'
        ];
        $goods_obj = DB::table($this->table)->select($field)
            ->leftJoin('goods_spec as gs', 'gs.goods_sn', '=', 'g.goods_sn')
            ->leftJoin('goods_code as gc', 'gc.spec_sn', '=', 'gs.spec_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id')
            ->leftJoin('category as c', 'c.cat_id', '=', 'g.cat_id');
        if (!empty($param_info['query_sn'])) {
            $query_sn = $param_info['query_sn'];
            $query_like_sn = '%' . $query_sn . '%';
            $goods_obj->where(function ($query) use ($query_sn, $query_like_sn) {
                $query->orWhere('g.goods_name', 'like', $query_like_sn);
                $query->orWhere('g.keywords', 'like', $query_like_sn);
                $query->orWhere('g.goods_sn', $query_sn);
                $query->orWhere('gs.spec_sn', $query_sn);
                $query->orWhere('gs.erp_merchant_no', $query_sn);
                $query->orWhere('gs.erp_prd_no', $query_sn);
                $query->orWhere('gs.erp_ref_no', $query_sn);
                $query->orWhere('goods_code', $query_sn);
            });
        }
        $goods_info = $goods_obj->orderBy('g.create_time', 'DESC')->paginate($page_size);
        $goods_info = objectToArrayZ($goods_info);
        if (empty($goods_info['data'])) {
            return false;
        }

        //获取商品标签信息
        $goods_label_info = DB::table('goods_label')->get(['id', 'label_name', 'label_color']);
        $goods_label_info = objectToArrayZ($goods_label_info);
        $goods_label_list = [];
        foreach ($goods_label_info as $k => $v) {
            $goods_label_list[$v['id']] = $v;
        }

        $goods_list = [];
        $goods_sn_arr = [];
        foreach ($goods_info['data'] as $k => $v) {
            $tmp_label = [];
            if (!empty($v['goods_label'])) {
                $goods_label = explode(',', $v['goods_label']);
                foreach ($goods_label as $k1 => $v1) {
                    $goods_sn = $v['goods_sn'];
                    if (array_key_exists($goods_sn, $goods_list)) {
                        if (array_key_exists($v1, $goods_label_list) && !in_array($goods_label_list[$v1], $tmp_label)) {
                            $tmp_label[] = $goods_label_list[$v1];
                        }
                    } else {
                        if (array_key_exists($v1, $goods_label_list)) {
                            $tmp_label[] = $goods_label_list[$v1];
                        }
                    }

                }
            }
            $v['goods_label_list'] = $tmp_label;
            $goods_list[$v['goods_sn']] = $v;
            $goods_sn_arr[] = $v['goods_sn'];
        }

        //获取商品规格信息
        $field = [
            'gs.spec_id', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.erp_prd_no', 'gs.erp_ref_no', 'gs.goods_label', 'gs.goods_sn',
            'gs.spec_price', 'gs.spec_weight', 'gs.stock_num', 'gs.gold_discount', 'gs.foreign_discount', 'gs.exw_discount',
        ];
        $goods_spec_info = DB::table('goods_spec as gs')->whereIn('goods_sn', $goods_sn_arr)->get($field);
        $goods_spec_info = objectToArrayZ($goods_spec_info);
        $spec_list = [];
        $spec_sn = [];
        foreach ($goods_spec_info as $k => $v) {
            $spec_list[$v['spec_sn']] = $v;
            $spec_sn[] = $v['spec_sn'];
        }

        //获取商品编码
        $field = [
            'gc.id', 'gc.spec_sn', 'gc.goods_code', 'gc.code_type'
        ];
        $goods_code_info = DB::table('goods_code as gc')->whereIn('spec_sn', $spec_sn)->get($field);
        $goods_code_info = objectToArrayZ($goods_code_info);
        foreach ($goods_code_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            if (isset($spec_list[$spec_sn])) {
                $code_type = $this->platform;
                $v['code_type_name'] = $code_type[$v['code_type']];
                $spec_list[$spec_sn]['goods_code_info'][] = $v;
            }
        }
        foreach ($spec_list as $k => $v) {
            $goods_sn = $v['goods_sn'];
            if (isset($goods_list[$goods_sn])) {
                $goods_list[$goods_sn]['goods_spec_info'][] = $v;
            }
        }
        $goods_info['data'] = array_values($goods_list);
        return $goods_info;
    }

    /**
     * description:获取商品列表
     * editor:zongxing
     * date : 2018.08.25
     * return object
     */
    public function goodsList($param_info)
    {
        $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $field = [
            'g.goods_id', 'g.goods_name', 'b.name as brand_name', 'g.goods_sn', 'gs.goods_label',
            'gs.spec_id', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.erp_prd_no', 'gs.erp_ref_no', 'gs.goods_label',
            'gs.spec_price', 'gs.spec_weight', 'gs.stock_num', 'gs.exw_discount', 'gs.spec_img'
        ];
        $goods_obj = DB::table($this->table)->select($field)
            ->leftJoin('goods_spec as gs', 'gs.goods_sn', '=', 'g.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id');
        $where = [];
        if (!empty($param_info['spec_sn'])) {
            $spec_sn = trim($param_info['spec_sn']);
            $where[] = ['gs.spec_sn', $spec_sn];
        }
        if (!empty($param_info['erp_merchant_no'])) {
            $erp_merchant_no = trim($param_info['erp_merchant_no']);
            $where[] = ['gs.erp_merchant_no', $erp_merchant_no];
        }
        if (!empty($param_info['erp_prd_no'])) {
            $erp_prd_no = trim($param_info['erp_prd_no']);
            $where[] = ['gs.erp_prd_no', $erp_prd_no];
        }
        if (!empty($param_info['erp_ref_no'])) {
            $erp_ref_no = trim($param_info['erp_ref_no']);
            $where[] = ['gs.erp_ref_no', $erp_ref_no];
        }
        if (!empty($param_info['goods_name']) || !empty($param_info['query_sn'])) {
            $query_sn = !empty($param_info['goods_name']) ? $param_info['goods_name'] : $param_info['query_sn'];
            $total_info = $this->createQueryGoodsList($query_sn);

            $total_arr = $total_info['total_arr'];
            //品牌ID条件
            if (isset($total_info['brand_id_arr'])) {
                $brand_id_arr = $total_info['brand_id_arr'];
                $goods_obj->whereIn('b.brand_id', $brand_id_arr);
            }

            foreach ($total_arr as $k1 => $v1) {
                if (!empty($v1)) {
                    foreach ($v1 as $k => $v) {
                        $v = '%' . trim($v) . '%';
                        $where[] = ['g.goods_name', 'LIKE', $v];
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

        $goods_info = $goods_obj->where($where)
            ->where(function ($query) use ($orWhere) {
                $query->orWhere($orWhere);
            })
            ->orderBy('g.create_time', 'DESC')->paginate($page_size);
        $goods_info = objectToArrayZ($goods_info);
        if (empty($goods_info['data'])) {
            return false;
        }

        $spec_sn_arr = [];
        foreach ($goods_info['data'] as $k => $v) {
            $spec_sn_arr[] = $v['spec_sn'];
        }

        //获取商品标签信息
        $goods_label_info = DB::table('goods_label')->get(['id', 'label_name', 'label_color']);
        $goods_label_info = objectToArrayZ($goods_label_info);
        $goods_label_list = [];
        foreach ($goods_label_info as $k => $v) {
            $goods_label_list[$v['id']] = $v;
        }

        //获取商品编码
        $field = ['gc.id', 'gc.spec_sn', 'gc.goods_code', 'gc.code_type'];
        $goods_code_info = DB::table('goods_code as gc')->whereIn('spec_sn', $spec_sn_arr)->get($field);
        $goods_code_info = objectToArrayZ($goods_code_info);

        $goods_list = $goods_info['data'];
        $is_all = $param_info['is_all'];
        $total_goods_list = $this->createTotalGoodsList($goods_list, $goods_label_list, $goods_code_info, $is_all);
        $goods_info['data'] = $total_goods_list;
        return $goods_info;
    }

    /**
     * description:获取商品详情
     * editor:zongxing
     * date : 2020.02.13
     * return json
     */
    public function goodsDetail($param_info)
    {
        $spec_sn = trim($param_info['spec_sn']);
        $field = [
            'g.goods_id', 'g.goods_name', 'b.name as brand_name', 'c.cat_name', 'g.goods_sn', 'gs.goods_label',
            'gs.spec_id', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.erp_prd_no', 'gs.erp_ref_no', 'gs.goods_label',
            'gs.spec_price', 'gs.spec_weight', 'gs.estimate_weight', 'gs.stock_num', 'gs.exw_discount', 'g.brand_id',
            'c.cat_id', 'gs.spec_img'
        ];
        $goods_obj = DB::table($this->table)->select($field)
            ->leftJoin('goods_spec as gs', 'gs.goods_sn', '=', 'g.goods_sn')
            ->leftJoin('goods_code as gc', 'gc.spec_sn', '=', 'gs.spec_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id')
            ->leftJoin('category as c', 'c.cat_id', '=', 'g.cat_id')
            ->where('gs.spec_sn', $spec_sn);
        $goods_info = $goods_obj->first();
        $goods_info = objectToArrayZ($goods_info);
        if (empty($goods_info)) {
            return false;
        }

        //获取商品标签信息
        $goods_label_info = DB::table('goods_label')->get(['id', 'label_name', 'label_color']);
        $goods_label_info = objectToArrayZ($goods_label_info);
        $goods_label_list = [];
        foreach ($goods_label_info as $k => $v) {
            $goods_label_list[$v['id']] = $v;
        }

        //获取商品编码
        $field = ['gc.id', 'gc.spec_sn', 'gc.goods_code', 'gc.code_type'];
        $goods_code_info = DB::table('goods_code as gc')->where('spec_sn', $spec_sn)->get($field);
        $goods_code_info = objectToArrayZ($goods_code_info);

        $goods_info_tmp[0] = $goods_info;
        $is_all = $param_info['is_all'];
        $total_goods_list = $this->createTotalGoodsList($goods_info_tmp, $goods_label_list, $goods_code_info, $is_all);
        $goods_info = array_values($total_goods_list)[0];

        $field = ['brand_id', 'name'];
        $brand_list = DB::table('brand')->get($field);
        $brand_list = objectToArrayZ($brand_list);

        $cat_model = new CategoryModel();
        $cat_list = $cat_model->getCategoryRecursion();

        $return_info = [
            'goods_info' => $goods_info,
            'total_label_info' => $goods_label_info,
            'brand_list' => $brand_list,
            'cat_list' => $cat_list,
        ];
        return $return_info;
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
        $brand_info = $brand_obj->orderBy('modify_time', 'desc')->get();
        $brand_info = ObjectToArrayZ($brand_info);

        //去除搜索字段中的品牌信息
        $brand_id_arr = $common_str_arr = [];
        foreach ($brand_info as $k => $v) {
            $brand_id_arr[] = intval($v['brand_id']);
            $name_arr = [trim($v['name']), trim($v['name_en']), trim($v['name_cn'])];
            foreach ($name_arr as $k => $v) {
                $v = strtolower($v);
                $common_str = find_common_str($v, $query_sn);
                if ($common_str && !in_array($common_str, $common_str_arr)) {
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
        $total_arr = [$number_arr[0], $str_arr[0], $gbk_arr];
        $return_info['total_arr'] = $total_arr;
        return $return_info;
    }


    /**
     * description:组装商品列表数据
     * editor:zongxing
     * date : 20020.02.13
     * return Json
     */
    public function createTotalGoodsList($goods_info, $goods_label_list, $goods_code_info, $is_all)
    {
        $goods_list = [];
        foreach ($goods_info as $k => $v) {
            // 商品标签
            $tmp_label = [];
            if (!empty($v['goods_label'])) {
                $goods_label = explode(',', $v['goods_label']);
                foreach ($goods_label as $k1 => $v1) {
                    $goods_sn = $v['goods_sn'];
                    if (array_key_exists($goods_sn, $goods_list)) {
                        if (array_key_exists($v1, $goods_label_list) && !in_array($goods_label_list[$v1], $tmp_label)) {
                            $tmp_label[] = $goods_label_list[$v1];
                        }
                    } else {
                        if (array_key_exists($v1, $goods_label_list)) {
                            $tmp_label[] = $goods_label_list[$v1];
                        }
                    }

                }
            }
            $v['goods_label_list'] = $tmp_label;

            // 获取所有商品码
            if ($is_all) {
                foreach ($this->platform_no as $k2 => $v2) {
                    $v[$k2] = '';
                }
            }
            $goods_list[$v['spec_sn']] = $v;
        }

        // 商品码
        foreach ($goods_code_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            if (isset($goods_list[$spec_sn])) {
                $platform_no = $this->platform_no;
                $code_type = $v['code_type'];
                $code_name = array_keys($platform_no, $code_type)[0];
                $goods_code = $v['goods_code'];
                $goods_list[$spec_sn][$code_name] = $goods_code;
            }
        }

        $goods_info = array_values($goods_list);
        return $goods_info;
    }


    /**
     * description:获取商品列表
     * editor:zongxing
     * date : 2018.08.25
     * return object
     */
    public function updateGoodsList_stop()
    {
        $goods_list_info = $this->goodsList();
        $goods_list_info = json_encode($goods_list_info);
        Redis::set("goods_list_info", $goods_list_info);
    }


    /**
     * description:获取商品规格列表
     * editor:zongxing
     * date : 2018.08.25
     * return object
     */
    public function goodsSpecList($req_params)
    {
        $goods_sn = $req_params["goods_sn"];
        $total_spec_info = DB::table("goods_spec")->where("goods_sn", $goods_sn)->get();
        return $total_spec_info;
    }


    /**
     * description:获取批次列表
     * editor:zongxing
     * type:GET
     * date : 2018.07.09
     * return Object
     */
    public function getbatchListPredict($purchase_info)
    {
        $where = [];
        if (isset($purchase_info['query_sn']) && !empty($purchase_info['query_sn'])) {
            $purchase_sn = trim($purchase_info['query_sn']);
            $purchase_sn = "%" . $purchase_sn . "%";
            $where = [
                ["pd.purchase_sn", "LIKE", "$purchase_sn"]
            ];
        }

        $demand_goods_info = DB::table("demand_count as dc")
            ->where(function ($query) {
                $query->where("pd.status", "2")
                    ->orWhere('pd.status', '3');
            })
            ->where($where)
            ->select("pd.id as purchase_id", "dc.purchase_sn", "pd.predict_day",
                DB::raw('sum(real_buy_num) as real_buy_num'), DB::raw("Date(delivery_time) as delivery_time"))
            ->leftJoin("purchase_date as pd", "pd.purchase_sn", "=", "dc.purchase_sn")
            ->leftJoin("real_purchase_detail as rpd", "rpd.purchase_sn", "=", "dc.purchase_sn")
            ->where("rpd.day_buy_num", ">", 0)
            ->orderBy('pd.create_time', 'desc')
            ->groupBy("dc.purchase_sn")
            ->get();
        $demand_goods_info = json_decode(json_encode($demand_goods_info), true);

        $return_info = [];
        $return_info["data_num"] = count($demand_goods_info);
        $return_info["purchase_info"] = [];

        $start_page = isset($purchase_info['start_page']) ? intval($purchase_info['start_page']) : 1;
        $page_size = isset($purchase_info['page_size']) ? intval($purchase_info['page_size']) : 15;
        $start_str = ($start_page - 1) * $page_size;
        $demand_goods_info = array_slice($demand_goods_info, $start_str, $page_size);

        foreach ($demand_goods_info as $k => $v) {
            //计算当前采购期下面的自提批次数
            $v["zt_num"] = 1; //自提总数一直都为:1

            //计算当前采购期下面的商品自提总数
            $zt_goods_num = DB::table("real_purchase as rp")
                ->where("path_way", "0")
                ->where("rp.purchase_sn", $v["purchase_sn"])
                ->leftJoin("real_purchase_detail as rpd", "rpd.real_purchase_sn", "=", "rp.real_purchase_sn")
                ->sum("day_buy_num");
            $v["zt_goods_num"] = $zt_goods_num;

            //计算当前采购期下面的邮寄批次数
            $yj_num = DB::table("real_purchase")
                ->where("path_way", "1")
                ->where("purchase_sn", $v["purchase_sn"])
                ->count("real_purchase_sn");
            $v["yj_num"] = $yj_num;

            //计算当前采购期下面的商品邮寄总数
            $yj_goods_num = DB::table("real_purchase as rp")
                ->where("path_way", "1")
                ->where("rp.purchase_sn", $v["purchase_sn"])
                ->leftJoin("real_purchase_detail as rpd", "rpd.real_purchase_sn", "=", "rp.real_purchase_sn")
                ->sum("day_buy_num");
            $v["yj_goods_num"] = $yj_goods_num;

            //计算实时数据列表信息
            $goods_list_info = DB::table("real_purchase as rp")
                ->where("rp.purchase_sn", $v["purchase_sn"])
                ->where("rp.status", 1)
                ->select("rp.real_purchase_sn", "port_id", "path_way", "rp.is_setting",
                    DB::raw('sum(day_buy_num) as total_buy_num'),
                    DB::raw("Date(jms_rp.create_time) as create_time"))
                ->leftJoin("real_purchase_detail as rpd", "rpd.real_purchase_sn", "=", "rp.real_purchase_sn")
                ->groupBy("rp.real_purchase_sn")
                ->get();
            $goods_list_info = $goods_list_info->toArray();

            $tmp_info["title_info"] = $v;
            if ($goods_list_info) {
                $tmp_info["list_info"] = $goods_list_info;
            }
            array_push($return_info["purchase_info"], $tmp_info);
        }
        return $return_info;
    }

    /**
     * description:获取批次列表
     * editor:zongxing
     * type:GET
     * date : 2018.07.09
     * return Object
     */
    public function getbatchListReal($purchase_info)
    {
        $where = [];
        if (isset($purchase_info['query_sn']) && !empty($purchase_info['query_sn'])) {
            $purchase_sn = trim($purchase_info['query_sn']);
            $purchase_sn = "%" . $purchase_sn . "%";
            $where = [
                ["pd.purchase_sn", "LIKE", "$purchase_sn"]
            ];
        }

        $demand_goods_info = DB::table("demand_count as dc")
            ->where(function ($query) {
                $query->where("pd.status", "2")
                    ->orWhere('pd.status', '3');
            })
            ->where($where)
            ->select("pd.id as purchase_id", "dc.purchase_sn", "pd.predict_day",
                DB::raw('sum(real_buy_num) as real_buy_num'), DB::raw("Date(delivery_time) as delivery_time"))
            ->leftJoin("purchase_date as pd", "pd.purchase_sn", "=", "dc.purchase_sn")
            ->leftJoin("real_purchase_detail as rpd", "rpd.purchase_sn", "=", "dc.purchase_sn")
            ->where("rpd.day_buy_num", ">", 0)
            ->orderBy('pd.create_time', 'desc')
            ->groupBy("dc.purchase_sn")
            ->get();
        $demand_goods_info = objectToArrayZ($demand_goods_info);

        $return_info = [];
        $return_info["data_num"] = count($demand_goods_info);
        $return_info["purchase_info"] = [];

        $start_page = isset($purchase_info['start_page']) ? intval($purchase_info['start_page']) : 1;
        $page_size = isset($purchase_info['page_size']) ? intval($purchase_info['page_size']) : 15;
        $start_str = ($start_page - 1) * $page_size;
        $demand_goods_info = array_slice($demand_goods_info, $start_str, $page_size);

        foreach ($demand_goods_info as $k => $v) {
            //计算当前采购期下面的自提批次数
            $v["zt_num"] = 1; //自提总数一直都为:1

            //计算当前采购期下面的商品自提总数
            $zt_goods_num = DB::table("real_purchase as rp")
                ->where("path_way", "0")
                ->where("rp.purchase_sn", $v["purchase_sn"])
                ->leftJoin("real_purchase_detail as rpd", "rpd.real_purchase_sn", "=", "rp.real_purchase_sn")
                ->sum("day_buy_num");
            $v["zt_goods_num"] = $zt_goods_num;

            //计算当前采购期下面的邮寄批次数
            $yj_num = DB::table("real_purchase")
                ->where("path_way", "1")
                ->where("purchase_sn", $v["purchase_sn"])
                ->count("real_purchase_sn");
            $v["yj_num"] = $yj_num;

            //计算当前采购期下面的商品邮寄总数
            $yj_goods_num = DB::table("real_purchase as rp")
                ->where("path_way", "1")
                ->where("rp.purchase_sn", $v["purchase_sn"])
                ->leftJoin("real_purchase_detail as rpd", "rpd.real_purchase_sn", "=", "rp.real_purchase_sn")
                ->sum("day_buy_num");
            $v["yj_goods_num"] = $yj_goods_num;

            //计算实时数据列表信息
            $goods_list_info = DB::table("real_purchase as rp")
                ->where("rp.purchase_sn", $v["purchase_sn"])
                ->where("rp.status", ">=", 2)
                ->select("rp.real_purchase_sn", "port_id", "path_way", "rp.is_setting",
                    DB::raw('sum(day_buy_num) as total_buy_num'),
                    DB::raw("Date(jms_rp.create_time) as create_time"))
                ->leftJoin("real_purchase_detail as rpd", "rpd.real_purchase_sn", "=", "rp.real_purchase_sn")
                ->groupBy("rp.real_purchase_sn")
                ->get();
            $goods_list_info = $goods_list_info->toArray();

            $tmp_info["title_info"] = $v;
            if ($goods_list_info) {
                $tmp_info["list_info"] = $goods_list_info;
            }
            array_push($return_info["purchase_info"], $tmp_info);
        }
        return $return_info;
    }


    /**
     * @description:计算商品价格（结果四舍五入保留两位小数）
     * @editor:张冬
     * @date : 2018.10.10
     * @param $spec_price (美金原价)
     * @param $discount (折扣)
     * @return array
     */
    public function calculateGoodsPrice($spec_price, $gold_discount, $black_discount)
    {
        $goldPrice = round($spec_price * $gold_discount, 2);
        $blackPrice = round($spec_price * $black_discount, 2);
        $arrPrice = [
            'goldPrice' => $goldPrice,
            'blackPrice' => $blackPrice,
        ];
        return $arrPrice;

    }

    /**
     * @description:获取erp仓库信息
     * @editor:张冬
     * @date : 2018.10.10
     * @param $store_id
     * @return object
     */
    public function getErpStoreInfo($store_id = '')
    {
        $store_id = intval($store_id);
        $where = $store_id == 0 ? [] : [['store_id', $store_id]];
        $field = ['store_name', 'store_factor', 'store_id'];
        $storehouseInfo = DB::table('erp_storehouse')->select($field)->where($where)->get()
            ->map(function ($value) {
                return (array)$value;
            })->toArray();
        return $storehouseInfo;
    }

    /**
     * @description:在二维数组中搜索，返回对应键名
     * @editor:张冬
     * @date : 2018.10.10
     * @param $arrData (数组)
     * @param $columnValue (键值)
     * @param $column (键名)
     * @return int
     */
    public function twoArraySearch($arrData, $columnValue, $column)
    {
        $found_key = array_search($columnValue, array_column($arrData, $column));
        return $found_key;
    }


    /**
     * @description:计算有关erp的所有商品数据
     * @editor:张冬
     * @date : 2018.10.10
     * @param $spec_weight
     * @param $spec_price
     * @param $store_factor
     * @param $exw_discount
     * @return array
     */
    public function getErpGoodsData($spec_weight, $spec_price, $store_factor, $exw_discount)
    {
        //美元汇率
        $dollar_rate = DOLLAR_RATE;
        //重价比=重量/美金原价/重价系数/100
        $highPriceRatio = 0;
        if ($spec_price > 0 && $store_factor > 0) {
            $highPriceRatio = round($spec_weight / $spec_price / $store_factor / 100, DECIMAL_DIGIT);
        }
        //重价比折扣 = exw折扣+重价比
        $hprDiscount = $exw_discount + $highPriceRatio;
        //erp成本价 = 美金原价*重价比折扣*汇率
        $erpCostPrice = round($spec_price * $hprDiscount * $dollar_rate, DECIMAL_DIGIT);
        $erpGoodsData = [
            'highPriceRatio' => $highPriceRatio,
            'hprDiscount' => $hprDiscount,
            'erpCostPrice' => $erpCostPrice,
        ];
        return $erpGoodsData;
    }


    /**
     * @description:获取自采毛利率档位信息
     * @editor:张冬
     * @date : 2018.10.11
     * @return array
     */
    public function getPickMarginInfo($pick_margin_rate = '')
    {
        $where = empty($pick_margin_rate) ? [] : [['pick_margin_rate', trim($pick_margin_rate)]];
        $field = ['pick_margin_rate'];
        $marginRateInfo = DB::table('margin_rate')->select($field)->where($where)->orderBy('pick_margin_rate', 'ASC')->get()
            ->map(function ($value) {
                return (array)$value;
            })->toArray();
        return $marginRateInfo;
    }

    /**
     * @description:计算定价折扣相关的数据
     * @editor:张冬
     * @date : 2018.10.11
     * @param $spec_price (美金原价)
     * @param $pricing_rate (定价折扣)
     * @param $hrp_discount (重价比折扣)
     * @return array
     */
    public function calculPricingInfo($spec_price, $pricing_rate, $hrp_discount, $chargeInfo)
    {
        //美元汇率
        $dollar_rate = DOLLAR_RATE;
        //销售价(人民币) = 美金原价*定价折扣*汇率
        $salePrice = round($spec_price * $pricing_rate * $dollar_rate, DECIMAL_DIGIT);
        //销售毛利率 = 1-重价比折扣/定价折扣
        $saleMarRate = 0;
        if ($pricing_rate > 0) {
            $saleMarRate = round(1 - $hrp_discount / $pricing_rate, DECIMAL_DIGIT);
        }
        //获取费用百分比 = $pricing_rate * 各费用比率
        $arrChargeRate = [];
        $totalChaRate = 0;
        $totalGoodsChaRate = 0;
        foreach ($chargeInfo as $item) {
            $chargeRate = sprintf('%.0f%%', $item['charge_rate']);//当前费用比率
            $goodsChaRate = round($pricing_rate * $item['charge_rate'] / 100, DECIMAL_DIGIT);
            $arrChargeRate[] = [$chargeRate => $goodsChaRate];
            $totalChaRate += $chargeRate;
            $totalGoodsChaRate += $goodsChaRate;
        }
        $totalRate = [sprintf('%.0f%%', $totalChaRate) => $totalGoodsChaRate];
        //运营毛利 = 销售毛利率 - 费用合计
        $runMarRate = ($saleMarRate - $totalGoodsChaRate) * 100;
//        $runMarRate = sprintf('%.2f%%', $a);
        $arrChargeRate[] = $totalRate;
        $pricingInfo = [
            'salePrice' => $salePrice,
            'saleMarRate' => $saleMarRate,
            'runMarRate' => round($runMarRate, 2),
            'arrChargeRate' => $arrChargeRate,
        ];
        return $pricingInfo;
    }


    /**
     * @description:获取费用项
     * @editor:张冬
     * @date : 2018.10.11
     * @return array
     */
    public function getChargeInfo($department_id)
    {
        $department_id = intval($department_id);
        $field = ['charge_rate', 'charge_name', 'create_time'];
        $where = [
            ['department_id', $department_id]
        ];
        $chargeInfo = DB::table('charge')->select($field)->where($where)->orderBy('charge_rate', 'ASC')->get()
            ->map(function ($value) {
                return (array)$value;
            })->toArray();
        return $chargeInfo;
    }

    /**
     * @description:二维数组去重
     * @editor:zhangdong
     * @date : 2018.10.11
     * @param $array2D
     * @return array
     */
    public function array_unique_fb($array2D)
    {
        foreach ($array2D as $k => $v) {
            $v = join(",", $v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
            $temp[$k] = $v;
        }
        $temp = array_unique($temp);//去掉重复的字符串,也就是重复的一维数组
        foreach ($temp as $k => $v) {
            $result[$k] = $array2D[$k];//再将拆开的数组重新组装
        }
        return $result;
    }

    /**
     * @description:获取毛利率信息
     * @editor:zhangdong
     * @date : 2018.10.12
     * @param $mar_rate 要查询的毛利率
     * @return array
     */
    public function getMarRate($mar_rate)
    {
        $mar_rate = trim($mar_rate);
        $field = ['pick_margin_rate'];
        $where = [
            ['pick_margin_rate', $mar_rate]
        ];
        $marginRateInfo = DB::table('margin_rate')->select($field)->where($where)->get();
        return $marginRateInfo;
    }

    /**
     * @description:新增毛利率
     * @editor:zhangdong
     * @date : 2018.10.12
     * @param $mar_rate 要新增的毛利率
     * @return array
     */
    public function addNewMarRate($mar_rate)
    {
        $mar_rate = intval($mar_rate);
        $addData = ['pick_margin_rate' => $mar_rate];
        $addRes = DB::table('margin_rate')->insert($addData);
        return $addRes;
    }

    /**
     * @description:单个修改需求商品定价折扣（有则更新无则新增）
     * @editor:zhangdong
     * @date : 2018.10.12
     * @param $demand_sn 需求单号
     * @param $spec_sn 规格码
     * @param $pricing_rate 定价折扣
     * @return bool
     */
    public function updatePricRate($demand_sn, $spec_sn, $pricing_rate)
    {
        $demand_sn = trim($demand_sn);
        $spec_sn = trim($spec_sn);
        $pricing_rate = trim($pricing_rate);
        $where = [
            ['demand_sn', $demand_sn],
            ['spec_sn', $spec_sn],
        ];
        $updateData = ['pricing_rate' => $pricing_rate];
        $updateRes = DB::table('pricing_rate')->where($where)->update($updateData);
        return $updateRes;
    }

    /**
     * @description:批量修改需求商品定价折扣（有则更新无则新增）
     * @editor:zhangdong
     * @date : 2018.10.12
     * @param $demand_sn 需求单号
     * @param $pricing_rate 定价折扣
     * @return bool
     */
    public function batchOpdatePricRate($demand_sn, $pricing_rate)
    {
        $demand_sn = trim($demand_sn);
        $pricing_rate = trim($pricing_rate);
        $where = [
            ['demand_sn', $demand_sn],
        ];
        $updateData = ['pricing_rate' => $pricing_rate];
        $updateRes = DB::table('pricing_rate')->where($where)->update($updateData);
        return $updateRes;
    }

    /**
     * @description:新增费用项
     * @editor:zhangdong
     * @date : 2018.10.15
     * @param $charge_rate 费用比例
     * @param $charge_name 费用名称
     * @return boolean
     */
    public function addCharge($charge_rate, $charge_name, $department_id)
    {
        $charge_rate = trim($charge_rate);
        $charge_name = trim($charge_name);
        $addData = ['charge_rate' => $charge_rate, 'charge_name' => $charge_name, 'department_id' => $department_id];
        $addRes = DB::table('charge')->insert($addData);
        return $addRes;
    }

    /**
     * @description:检查是否已经存在该费用
     * @editor:zhangdong
     * @date : 2018.10.15
     * @param $charge_name 要查询的费用项
     * @return array
     */
    public function getCharMsg($charge_name, $department_id)
    {
        $charge_name = trim($charge_name);
        $field = ['charge_rate', 'charge_name', 'department_id'];
        $where = [
            ['charge_name', $charge_name],
            ['department_id', $department_id],
        ];
        $charMsg = DB::table('charge')->select($field)->where($where)->get();
        return $charMsg;
    }


    /**
     * @description:修改费用比例
     * @editor:zhangdong
     * @date : 2018.10.16
     * @param $charge_name 要查询的费用项
     * @param $charge_rate 费用比例
     * @return boolean
     */
    public function modifyCharge($charge_rate, $charge_name, $department_id)
    {
        $where = [
            ['charge_name', $charge_name],
            ['department_id', $department_id],
        ];
        $update = ['charge_rate' => $charge_rate];
        $modRes = DB::table('charge')->where($where)->update($update);
        return $modRes;
    }

    /**
     * description:商品模块-销售用户列表
     * editor:zhangdong
     * date : 2018.10.31
     * return Object
     */
    public function saleUserList($params, $pageSize)
    {
        //搜索关键字
        $keywords = $params['keywords'];
        $where = [];
        if ($keywords) {
            $where = [
                ['su.user_name', 'LIKE', "%$keywords%"],
            ];
        }

        $field = [
            'su.id', 'su.user_name', 'su.depart_id', 'su.min_profit', "sale_short", "payment_cycle", "group_sn",
            DB::raw('COUNT(DISTINCT jms_sug.spec_sn) AS sku_num'),
            DB::raw('(CASE jms_su.depart_id WHEN 1 THEN "批发部" WHEN 2 THEN "零售部" END) AS depart_name'),
            DB::raw("(CASE jms_su.sale_user_cat WHEN 'Z' THEN '账期' WHEN 'XY' THEN '现结' END) AS sale_user_cat"),
            DB::raw("(CASE jms_su.money_cat WHEN 'D' THEN '美金' WHEN 'C' THEN '人民币' END) AS money_cat"),//modify by zongxing 2018.12.06
        ];
        $sug_on = [
            ['su.id', '=', 'sug.sale_user_id'],
            [DB::raw('LENGTH(jms_sug.spec_sn)'), '>', DB::raw(0)]
        ];
        $saleUserList = DB::table('sale_user AS su')->select($field)
            ->leftJoin('sale_user_goods AS sug', $sug_on)
            ->where($where)->groupBy('su.id')->paginate($pageSize);
        return $saleUserList;
    }

    /**
     * description:商品模块-获取销售用户商品列表
     * editor:zhangdong
     * date : 2018.10.31
     * return Object
     * @param $pickMarginRate (自采毛利率(array))
     * @param $chargeInfo (费用(array))
     * @return array
     */
    public function userGoodsList($goodsBaseInfo, $pickMarginRate, $chargeInfo, $goodsHouseInfo, $store_id)
    {
        //查找对应仓库id的键名(在仓库信息的二维数组中查找对应的仓库id)-避免循环查询数据库
        $found_key = $this->twoArraySearch($goodsHouseInfo, $store_id, 'store_id');
        $store_name = trim($goodsHouseInfo[$found_key]['store_name']);
        $store_factor = trim($goodsHouseInfo[$found_key]['store_factor']);//重价系数
        //计算其他商品信息
        $brandInfo = [];
        foreach ($goodsBaseInfo as $key => $value) {
            $brand_name = trim($value->brand_name);
            $brand_id = trim($value->brand_id);
            $brandInfo[] = [
                'brand_name' => $brand_name,
                'brand_id' => $brand_id,
            ];
            $spec_price = trim($value->spec_price);
            if (empty($spec_price) || $spec_price <= 0) continue;
            $gold_discount = trim($value->gold_discount);
            $black_discount = trim($value->black_discount);
            $goodsPrice = $this->calculateGoodsPrice($spec_price, $gold_discount, $black_discount);
            //金卡价=美金原价*金卡折扣
            $goodsBaseInfo[$key]->gold_price = $goodsPrice['goldPrice'];
            //黑卡价=美金原价*黑卡折扣
            $goodsBaseInfo[$key]->black_price = $goodsPrice['blackPrice'];
            //计算erp仓库相关的数据（仓库名称，重价比，重价比折扣，ERP成本价￥，ERP成本折扣=重价比折扣）
            //仓库名称
            $goodsBaseInfo[$key]->store_name = $store_name;
            //如果没有真实重量则取预估重量，正常情况下这两个重量必定有一个不为空
            $goods_weight = floatval($value->spec_weight);
            if ($goods_weight <= 0) {
                $goods_weight = floatval($value->estimate_weight);
            }
            $exw_discount = trim($value->exw_discount); //exw折扣
            //获取有关erp的所有商品数据
            $erpGoodsData = $this->getErpGoodsData($goods_weight, $spec_price, $store_factor, $exw_discount);
            //重价比=重量/美金原价/重价系数/100
            $goodsBaseInfo[$key]->high_price_ratio = $erpGoodsData['highPriceRatio'];
            //重价比折扣 = exw折扣+重价比
            $hrp_discount = $erpGoodsData['hprDiscount'];
            $goodsBaseInfo[$key]->hpr_discount = $hrp_discount;
            //erp成本价=美金原价*重价比折扣*汇率
            $goodsBaseInfo[$key]->erp_cost_price = $erpGoodsData['erpCostPrice'];
            //计算自采毛利率相关数据
            //自采毛利率=重价比折扣/（1-对应档位利率）
            $arrMarginRate = [];
            $pricing_rate = 0;
            $margin_rate = MARGIN_RATE_PERCENT;
            foreach ($pickMarginRate as $item) {
                $marginRate = sprintf('%.0f%%', $item['pick_margin_rate']);//自采毛利率当前档位
                $rateData = round($erpGoodsData['hprDiscount'] / (1 - $item['pick_margin_rate'] / 100), DECIMAL_DIGIT);
                $arrMarginRate[] = [$marginRate => $rateData];
                $goodsBaseInfo[$key]->arrMarginRate = $arrMarginRate;
                //定价折扣默认档位
                if ($marginRate == $margin_rate) {
                    $pricing_rate = $rateData;
                };
            }
            $pricing_rate = isset($value->sale_discount) ? trim($value->sale_discount) : $pricing_rate;
            $pricingRateInfo = $this->calculPricingInfo($spec_price, $pricing_rate, $hrp_discount, $chargeInfo);
            $goodsBaseInfo[$key]->sale_discount = $pricing_rate;//销售折扣
            $goodsBaseInfo[$key]->salePrice = $pricingRateInfo['salePrice'];//销售价
            $goodsBaseInfo[$key]->saleMarRate = $pricingRateInfo['saleMarRate'];//销售毛利率
            $goodsBaseInfo[$key]->runMarRate = $pricingRateInfo['runMarRate'];//运营毛利率
            $goodsBaseInfo[$key]->arrChargeRate = $pricingRateInfo['arrChargeRate'];//费用项
        }
        $returnData = [
            'goodsBaseInfo' => $goodsBaseInfo,
            'brandInfo' => $brandInfo,
        ];
        return $returnData;
    }

    /**
     * description:获取销售用户商品信息
     * editor:zhangdong
     * date : 2018.10.31
     * @param $sale_user_id 销售用户id
     * @return object
     */
    public function getUserGoodsInfo($sale_user_id, $keywords, $pageSize, $arrErpNo)
    {
        $arrField = [
            'b.name as brand_name', 'b.brand_id', 'g.goods_name', 'gs.spec_price',
            'gs.spec_weight', 'gs.gold_discount', 'gs.black_discount', 'gs.exw_discount',
            'gs.foreign_discount', 'gs.spec_sn', 'gs.erp_merchant_no', 'sug.sale_discount'
        ];
        $where = [
            ['sug.sale_user_id', '=', $sale_user_id],
            [DB::raw('LENGTH(jms_sug.spec_sn)'), '>', 0],
        ];
        if (!empty($keywords)) {
            $where[] = ['gs.erp_merchant_no', 'LIKE', "%$keywords%"];
        }
        $whereIn = [];
        if (count($arrErpNo) > 0) {
            $whereIn = $arrErpNo;
        }
        $goodsBaseInfo = DB::table('sale_user_goods AS sug')->select($arrField)
            ->leftJoin('goods_spec AS gs', 'gs.spec_sn', '=', 'sug.spec_sn')
            ->leftJoin('goods AS g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand AS b', 'b.brand_id', '=', 'g.brand_id')
            ->where(function ($query) use ($where, $whereIn) {
                $query->where($where);
                if (count($whereIn) > 0) {
                    $query->whereIn('sug.erp_merchant_no', $whereIn);
                }
            })->paginate($pageSize);
        return $goodsBaseInfo;
    }

    /**
     * description:根据用户id获取销售折扣
     * editor:zhangdong
     * date : 2018.10.31
     * @param $sale_user_id 销售用户id
     * @return object
     */
    public function getUserSaleDiscount($sale_user_id)
    {
        $field = ['spec_sn', 'sale_discount'];
        $where = [
            ['sale_user_id', $sale_user_id],
            [DB::raw('LENGTH(spec_sn)'), '>', 0],
        ];
        $queryRes = DB::table('sale_user_goods')->select($field)->where($where)->get()
            ->map(function ($value) {
                return (array)$value;
            })->toArray();
        return $queryRes;
    }

    /**
     * description:根据上传的商品数据组装商品信息
     * editor:zhangdong
     * date : 2018.11.01
     * @return object
     */
    public function getUpGoodsData($arr_erp_no)
    {
        //查询商品数据
        $field = [
            'b.name as brand_name', 'b.brand_id', 'g.goods_name', 'gs.spec_price', 'gs.spec_weight',
            'gs.gold_discount', 'gs.black_discount', 'gs.exw_discount', 'gs.foreign_discount',
            'gs.spec_sn', 'gs.erp_merchant_no'
        ];
        $goodsBaseInfo = DB::table('goods_spec AS gs')->select($field)
            ->leftJoin('goods AS g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand AS b', 'b.brand_id', '=', 'g.brand_id')
            ->whereIn('gs.erp_merchant_no', $arr_erp_no)
            ->get();
        $data = [
            'goodsBaseInfo' => $goodsBaseInfo,
            'erp_merchant_no' => $arr_erp_no
        ];
        return $data;

    }

    /**
     * description:更新或者新增销售用户商品数据
     * editor:zhangdong
     * date : 2018.11.01
     * @return object
     */
    public function saveUserGoods($userGoodsData, $sale_user_id, $arrErpNo, $department_id)
    {
        //查询销售用户的商品数据
        $field = ['sale_user_id', 'erp_merchant_no', 'spec_sn', 'depart_id'];
        $where = [
            ['sale_user_id', $sale_user_id]
        ];
        $goods = DB::table('sale_user_goods')->select($field)
            ->where($where)
            ->whereIn('erp_merchant_no', $arrErpNo)
            ->get()->map(function ($v) {
                return (array)$v;
            })->toArray();
        //将销售用户没有的数据保存
        $saveData = [];
        foreach ($userGoodsData as $key => $value) {
            $erpNo = trim($value->erp_merchant_no);
            $spec_sn = trim($value->spec_sn);
            $pricing_rate = trim($value->sale_discount);
            //在已经查到的销售用户商品数据中搜索，如果未搜到则保存
            $fundKey = twoArraySearch($goods, $erpNo, 'erp_merchant_no');
            if ($fundKey !== false) continue;
            $saveData[] = [
                'depart_id' => $department_id,
                'sale_user_id' => $sale_user_id,
                'erp_merchant_no' => $erpNo,
                'spec_sn' => $spec_sn,
                'sale_discount' => $pricing_rate,
            ];

        }
        if (count($saveData) > 0) {
            DB::table('sale_user_goods')->insert($saveData);
        }
        return true;

    }

    /**
     * @description:单个修改需求商品销售折扣
     * @editor:zhangdong
     * @date : 2018.11.01
     * @param $sale_user_id 销售用户id
     * @param $spec_sn 规格码
     * @param $pricing_rate 定价折扣
     * @return bool
     */
    public function modUsrSaleDiscount($sale_user_id, $spec_sn, $pricing_rate)
    {
        $where = [
            ['sale_user_id', $sale_user_id],
            ['spec_sn', $spec_sn],
        ];
        $updateData = ['sale_discount' => $pricing_rate];
        $updateRes = DB::table('sale_user_goods')->where($where)->update($updateData);
        return $updateRes;
    }

    /**
     * description:获取销售用户商品信息
     * editor:zhangdong
     * date : 2018.11.01
     * @return object
     */
    public function getSaleGoodsInfo($sale_user_id, $spec_sn = '')
    {
        $arrField = [
            'b.name as brand_name', 'b.brand_id', 'g.goods_name', 'gs.spec_price', 'gs.spec_weight', 'gs.gold_discount',
            'gs.black_discount', 'gs.exw_discount', 'gs.foreign_discount', 'gs.erp_merchant_no', 'gs.spec_sn'
        ];
        $where[] = ['sug.sale_user_id', $sale_user_id];
        if (!empty($spec_sn)) {
            $where[] = ['sug.spec_sn', $spec_sn];
        }
        $queryRes = DB::table('sale_user_goods AS sug')->select($arrField)
            ->leftJoin('goods_spec AS gs', 'gs.spec_sn', '=', 'sug.spec_sn')
            ->leftJoin('goods AS g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand AS b', 'b.brand_id', '=', 'g.brand_id')
            ->where($where)->get();
        return $queryRes;
    }

    /**
     * description:更新需求商品定价折扣
     * editor:zhangdong
     * date : 2018.11.01
     * @return object
     */
    public function updateSaleDis($spec_sn, $sale_user_id, $pricing_rate)
    {
        $where = [
            ['spec_sn', $spec_sn],
            ['sale_user_id', $sale_user_id]
        ];
        $update = [
            'sale_discount' => $pricing_rate
        ];
        $updateRes = DB::table('sale_user_goods')->where($where)->update($update);
        return $updateRes;
    }

    /**
     * description:批量更新语句执行
     * editor:zhangdong
     * date : 2018.11.01
     * @return object
     */
    public function executeSql($strSql, $bindData)
    {
        $executeRes = DB::update($strSql, $bindData);
        return $executeRes;
    }

    /**
     * description:获取自采毛利率档位信息,根据部门获取费用项,获取部门信息,
     * 获取费用信息,获取erp仓库信息-重价系数（默认为香港仓）等报价必须信息
     * editor:zhangdong
     * date : 2018.11.01
     * @return array
     */
    public function getOfferInfo($depart_id)
    {
        //获取自采毛利率档位信息
        $pickMarginRate = $this->getPickMarginInfoInRedis();
        $arrPickRate = [];
        foreach ($pickMarginRate as $item) {
            $arrPickRate[] = sprintf('%.0f%%', trim($item['pick_margin_rate']));
        }
        //获取部门信息
        $departmentModel = new DepartmentModel();
        $departmentInfo = $departmentModel->getDepartmentInfoInRedis();
        //部门id
        $depart_id = intval($depart_id);
        //获取费用信息
        $chargeModel = new ChargeModel();
        $chargeInfo = $chargeModel->getChargeInfoInRedis();
        //查询当前部门的费用信息
        $departChargeInfo = searchTwoArray($chargeInfo, $depart_id, 'department_id');
        $arrCharge = [];
        $totalCharge = 0;
        foreach ($departChargeInfo as $item) {
            $formatChargeRate = sprintf(
                '%.0f%%', trim($item['charge_rate'])
            );
            $arrCharge[] = [
                $formatChargeRate => trim($item['charge_name']),
            ];
            $totalCharge += $item['charge_rate'];
        }
        $arrCharge[] = [
            sprintf('%.0f%%', $totalCharge) => '费用合计'
        ];
        //获取erp仓库信息-重价系数（默认为香港仓）
        $eshModel = new ErpStorehouseModel();
        $goodsHouseInfo = $eshModel->getErpStoreInfoInRedis();
        $offerInfo = [
            'departmentInfo' => $departmentInfo,
            'arrCharge' => $arrCharge,
            'chargeInfo' => $departChargeInfo,
            'goodsHouseInfo' => $goodsHouseInfo,
            'arrPickRate' => $arrPickRate,
            'pickMarginRate' => $pickMarginRate,
        ];
        return $offerInfo;

    }

    /*
     * @description 对上传的数据进行校验，如果有新品则做临时保存
     * @author zhangdong
     * @date 2018.11.01
     * @version 2 zhangdong 2019.06.25
     * @param $goodsData
     */
    public function checkGoods($goodsData)
    {
        $arrNewGoods = $arrGoodsInfo = [];
        $gcModel = new GoodsCodeModel();
        $gsModel = new GoodsSpecModel();
        foreach ($goodsData as $key => $value) {
            if ($key == 0) continue;
            //将商品编码组装成为数组
            $strGoodsCode = trim($value[3]);
            $arrGoodsCode = $this->createGoodsCode($strGoodsCode);
            //检查品牌ID，品牌，商品名称，平台条码，需求量等必填信息
            if (
                empty($value[0]) ||//品牌ID
                empty($value[1]) ||//品牌
                empty($value[2]) ||//商品名称
                empty($value[7]) ||//需求量
                count($arrGoodsCode) == 0
            ) {
                return false;
            }
            //在商品规格码对照表中查询商品规格码
            $getSpecRes = $gcModel->getSpec($arrGoodsCode);
            $spec_sn = '';
            if (!is_null($getSpecRes)) {
                $spec_sn = trim($getSpecRes->spec_sn);
            }
            //查询商品信息
            $goodsInfo = '';
            if (!empty($spec_sn)) {
                $goodsInfo = $gsModel->getGoodsMsg($spec_sn);
            }
            //将新品筛选出来，后续将将会写入到ord_new_goods表中
            if (empty($goodsInfo)) {
                $arrNewGoods[] = $value;
                continue;
            };
            $goodsInfo->goods_num = !empty($value[7]) ? intval($value[7]) : 0;
            $goodsInfo->entrust_time = !empty($value[8]) ? trim($value[8]) : '';
            $goodsInfo->sale_discount = !empty($value[9]) ? floatval($value[9]) : 0;
            $goodsInfo->wait_buy_num = !empty($value[10]) ? intval($value[10]) : 0;
            $spec_price = floatval($goodsInfo->spec_price);
            $goodsInfo->spec_price = !empty($value[4]) ? floatval($value[4]) : $spec_price;
            $spec_weight = floatval($goodsInfo->spec_weight);
            $goodsInfo->spec_weight = !empty($value[5]) ? floatval($value[5]) : $spec_weight;
            $exw_discount = floatval($goodsInfo->exw_discount);
            $goodsInfo->exw_discount = !empty($value[6]) ? floatval($value[6]) : $exw_discount;
            $goodsInfo->platform_barcode = $strGoodsCode;
            //如果可以查到商品信息则将该条信息保存
            $arrGoodsInfo[] = $goodsInfo;
        }//end of foreach
        $returnMsg = [
            'newGoods' => $arrNewGoods,
            'arrGoodsInfo' => $arrGoodsInfo,
        ];
        return $returnMsg;
    }

    /**
     * description:组装商品编码-上传总单专用
     * editor:zhangdong
     * date : 2019.01.31
     * @return array
     */
    public function createGoodsCode($strGoodsCode)
    {
        //处理不规则编码-去商品编码中所有的空格并将中文逗号转为英文逗号
        $strGoodsCode = $this->ruleStr($strGoodsCode);
        //将平台条码分割为数组
        $arrGoodsCode = explode(',', $strGoodsCode);
        //去除数组中值为空的元素
        $arrGoodsCode = array_filter($arrGoodsCode);
        return $arrGoodsCode;
    }

    /**
     * description：对字符串按要求进行处理使其规范化,最终返回数组-上传总单专用
     * editor:zhangdong
     * date : 2019.02.11
     * @return array
     */
    private function ruleStr($str)
    {
        //去空格
        $str = str_replace(' ', '', $str);
        //将中文逗号转为英文逗号
        $str = str_replace('，', ',', $str);
        return $str;
    }

    /**
     * description:组装商品信息
     * author：zhangdong
     * date : 2019.02.14
     */
    public function createGoodsInfo($res)
    {
        $goodsData = $specData = $codeData = [];
        $categoryModel = new CategoryModel();
        //查询三级分类信息
        $categoryInfo = $categoryModel->getCategoryInfo(3, 3);
        //查询品牌信息
        $brandModel = new BrandModel();
        $brandInfo = $brandModel->getBrandInfo();
        $arrBrandInfo = objectToArray($brandInfo);
        //将分类信息转换为数组
        $arrCategoryInfo = objectToArray($categoryInfo);
        $gcModel = new GoodsCodeModel();
        foreach ($res as $key => $v) {
            if ($key == 0) continue;
            //商品名称，商品分类，商品品牌等信息检查
            $excelNum = $key + 1;
            $goods_name = trim($v[0]);
            $category_name = trim($v[1]);
            $brand_id = intval($v[2]);
            if (empty($goods_name) || empty($category_name) || $brand_id == 0) {
                return ['code' => '2064', 'msg' => '第' . $excelNum . '条商品名称，分类，品牌ID等信息有误'];
            }
            //商品编码检查
            $erp_merchant_no = trim($v[4]);
            $kl_code = trim($v[5]);
            $xhs_code = trim($v[6]);
            $erp_prd_no = trim($v[8]);
            if (empty($erp_merchant_no) && empty($kl_code) && empty($xhs_code) && empty($erp_prd_no)) {
                return ['code' => '2064', 'msg' => '第' . $excelNum . '条商品编码信息有误'];
            }
            //商品重量，EXW折扣信息检查
            $spec_weight = floatval($v[10]);
            $estimate_weight = floatval($v[11]);
            if ($spec_weight <= 0 && $estimate_weight <= 0) {
                return ['code' => '2064', 'msg' => '第' . $excelNum . '条商品重量和预估重量至少有一个'];
            }
            $exw_discount = floatval($v[12]);
            if ($exw_discount <= 0) {
                return ['code' => '2064', 'msg' => '第' . $excelNum . '条EXW折扣不可为空'];
            }
            //查询分类id
            $categoryInfo = twoArrayFuzzySearch($arrCategoryInfo, 'cat_name', $category_name);
            if (count($categoryInfo) == 0) {
                return ['code' => '2064', 'msg' => '第' . $excelNum . '条商品分类信息有误'];
            }
            $cat_id = intval($categoryInfo[0]['cat_id']);
            $cat_name = trim($categoryInfo[0]['cat_name']);
            //查询品牌id
            $brandInfo = searchTwoArray($arrBrandInfo, $brand_id, 'brand_id');
            if (count($brandInfo) == 0) {
                return ['code' => '2064', 'msg' => '第' . $excelNum . '条商品品牌信息有误'];
            }
            $brand_id = intval($brandInfo[0]['brand_id']);
            $brand_keywords = trim($brandInfo[0]['keywords']);
            //组装商品基本信息
            $goods_sn = $this->get_goods_sn();
            $goodsKeywords = $brand_keywords . '--' . $cat_name . '--' . $goods_name;
            $goodsData[] = [
                'goods_sn' => $goods_sn,
                'goods_name' => $goods_name,
                'keywords' => $goodsKeywords,
                'cat_id' => $cat_id,
                'brand_id' => $brand_id,
            ];
            //组装规格基本信息
            $spec_sn = $this->get_spec_sn($goods_sn);
            $specData[] = [
                'goods_sn' => $goods_sn,
                'spec_sn' => $spec_sn,
                'erp_merchant_no' => $erp_merchant_no,
                'erp_ref_no' => trim($v[7]),
                'erp_prd_no' => trim($v[8]),
                'spec_price' => floatval($v[9]),
                'spec_weight' => $spec_weight,
                'estimate_weight' => $estimate_weight,
                'exw_discount' => $exw_discount,
            ];
            //组装商品规格码对照信息
            if (!empty($erp_merchant_no)) {
                $codeData[] = [
                    'spec_sn' => $spec_sn,
                    'goods_code' => $erp_merchant_no,
                    'code_type' => $gcModel->code_type['ERP_MERCHANT_NO'],
                ];
            }

            if (!empty($xhs_code)) {
                $codeData[] = [
                    'spec_sn' => $spec_sn,
                    'goods_code' => $xhs_code,
                    'code_type' => $gcModel->code_type['XHS_CODE'],
                ];
            }

            $suid = 1;//考拉ID
            $klCode = $gcModel->makeGoodsCode($kl_code, $spec_sn, $suid);
            if (count($klCode) > 0) {
                $codeData = array_merge($klCode, $codeData);
            }
            //根据商品编码检查商品是否已经被创建过
            $arrGoodsCode = getFieldArrayVaule($codeData, 'goods_code');
            $codeSpecSn = $gcModel->getSpec($arrGoodsCode);
            if (!empty($codeSpecSn)) {
                return ['code' => '2064', 'msg' => '第' . $excelNum . '条商品已被创建'];
            }
        }//end of foreach
        $goodsInfo = [
            'goodsData' => $goodsData,
            'specData' => $specData,
            'codeData' => $codeData,
        ];
        return $goodsInfo;

    }//end of function


    /**
     * description:批量新增商品
     * author：zhangdong
     * date : 2019.02.14
     */
    public function batchInsertGoods($goodsInfoData)
    {
        $insertRes = DB::transaction(function () use ($goodsInfoData) {
            $goodsData = $goodsInfoData['goodsData'];
            DB::table('goods')->insert($goodsData);
            $specData = $goodsInfoData['specData'];
            DB::table('goods_spec')->insert($specData);
            $codeData = $goodsInfoData['codeData'];
            $insertRes = DB::table('goods_code')->insert($codeData);
            return $insertRes;
        });
        return $insertRes;

    }

    /**
     * @description:从redis中获取自采毛利率档位信息
     * @editor:张冬
     * @date : 2019.02.28
     * @return array
     */
    public function getPickMarginInfoInRedis()
    {
        //从redis中获取自采毛利率档位信息，如果没有则对其设置
        $marginRateInfo = Redis::get('marginRateInfo');
        if (empty($marginRateInfo)) {
            $field = ['pick_margin_rate'];
            $marginRateInfo = DB::table('margin_rate')->select($field)->orderBy('pick_margin_rate', 'ASC')
                ->get()->map(function ($value) {
                    return (array)$value;
                })->toArray();
            Redis::set('marginRateInfo', json_encode($marginRateInfo, JSON_UNESCAPED_UNICODE));
            $marginRateInfo = Redis::get('marginRateInfo');
        }
        $marginRateInfo = objectToArray(json_decode($marginRateInfo));
        return $marginRateInfo;

    }

    /**
     * description:通过规格码获取商品信息
     * author:zhangdong
     * date : 2019.03.12
     * return Object
     */
    public function getGoodsMsgBySpecSn(array $arrSpecSn)
    {
        $field = [
            'gs.goods_sn', 'gs.spec_sn', 'g.goods_name', 'gs.erp_prd_no', 'gs.erp_merchant_no',
            'gs.spec_price', 'gs.spec_weight', 'gs.exw_discount', 'gs.stock_num', 'g.brand_id',
        ];
        $queryRes = DB::table('goods_spec AS gs')->select($field)
            ->leftJoin('goods AS g', 'gs.goods_sn', 'g.goods_sn')
            ->whereIn('spec_sn', $arrSpecSn)->get()->map(function ($value) {
                return (array)$value;
            })->toArray();;
        return $queryRes;
    }

    /**
     * description:创建新商品-导入总单专用
     * author:zhangdong
     * date : 2019.04.16
     * @param $newGoodsInfo
     * @return mixed
     */
    public function createNewGoods($newGoodsInfo, $suid)
    {
        //品牌信息
        $brandModel = new BrandModel();
        $brandInfo = $brandModel->getBrandInfo();
        $arrBrandInfo = objectToArray($brandInfo);
        //分类信息
        $categoryModel = new CategoryModel();
        $categoryInfo = $categoryModel->getCategoryInfoInRedis();
        //组装商品基本信息
        $goodsInfo = $this->generalGoodsInfo($newGoodsInfo, $arrBrandInfo, $categoryInfo);
        //组装商品规格信息
        $gsModel = new GoodsSpecModel();
        $goods_sn = $goodsInfo['goods_sn'];
        $goodsSpecInfo = $gsModel->generalSpecInfo($newGoodsInfo, $goods_sn);
        //组装商品编码信息
        $gcModel = new GoodsCodeModel();
        $spec_sn = $goodsSpecInfo['spec_sn'];
        $strGoodsCode = trim($newGoodsInfo['platform_barcode']);
        $goodsCodeInfo = $gcModel->makeGoodsCode($strGoodsCode, $spec_sn, $suid);
        if (count($goodsCodeInfo) == 0) {
            return false;
        }
        //写入数据
        $insertRes = $this->insertNewGoods($goodsInfo, $goodsSpecInfo, $goodsCodeInfo);
        return [
            'insertRes' => $insertRes,
            'spec_sn' => $spec_sn,
        ];
    }

    /**
     * description:组装商品基本信息
     * author:zhangdong
     * date : 2019.04.16
     * @return array
     */
    public function generalGoodsInfo($newGoodsInfo, array $arrBrandInfo = [], array $categoryInfo = [])
    {
        $goods_sn = $this->get_goods_sn();
        //品牌信息
        $brand_id = isset($newGoodsInfo['brand_id']) ? intval($newGoodsInfo['brand_id']) : 0;
        $brandInfo = searchTwoArray($arrBrandInfo, $brand_id, 'brand_id');
        $brand_id = isset($brandInfo[0]['brand_id']) ? intval($brandInfo[0]['brand_id']) : 0;
        $brand_keywords = isset($brandInfo[0]['keywords']) ? trim($brandInfo[0]['keywords']) : '';

        //分类信息
        $cat_name = isset($newGoodsInfo['cat_name']) ? trim($newGoodsInfo['cat_name']) : '缺失';
        $cateInfo = searchTwoArray($categoryInfo, $cat_name, 'cat_name');
        $cat_id = isset($cateInfo[0]['cat_id']) ? intval($cateInfo[0]['cat_id']) : 0;

        $goods_name = isset($newGoodsInfo['goods_name']) ? trim($newGoodsInfo['goods_name']) : '';
        $goodsKeywords = $brand_keywords . '--' . $cat_name . '--' . $goods_name;
        $goodsData = [
            'goods_sn' => $goods_sn,
            'goods_name' => $goods_name,
            'keywords' => $goodsKeywords,
            'cat_id' => $cat_id,
            'brand_id' => $brand_id,
        ];
        return $goodsData;
    }

    /**
     * description:总单导入-写入新品数据
     * author:zhangdong
     * date : 2019.04.16
     * @return bool
     */
    public function insertNewGoods($goodsInfo, $goodsSpecInfo, $goodsCodeInfo)
    {
        $saveRes = DB::transaction(function () use ($goodsInfo, $goodsSpecInfo, $goodsCodeInfo) {
            //商品基本数据保存
            $goodsModel = new GoodsModel();
            $goods = cutString($goodsModel->table, 0, 'as');
            DB::table($goods)->insert($goodsInfo);
            //规格信息保存
            $gsModel = new GoodsSpecModel();
            $spec = cutString($gsModel->table, 0, 'as');
            DB::table($spec)->insert($goodsSpecInfo);
            //商品编码信息保存
            $gcModel = new GoodsCodeModel();
            $gcTable = cutString($gcModel->table, 0, 'as');
            $insertRes = DB::table($gcTable)->insert($goodsCodeInfo);
            return $insertRes;
        });
        return $saveRes;

    }

    /**
     * description:总单新品-批量创建新品
     * author:zhangdong
     * date : 2019.04.17
     * @return mixed
     */
    public function batchCreateGoods($newGoodsInfo, $suid)
    {
        $goodsInfoList = $specInfoList = $codeInfoList = $arr_update = $arrPlatformBarcode = [];
        $newGoodsInfo = objectToArray($newGoodsInfo);
        //品牌信息
        $brandModel = new BrandModel();
        $brandInfo = $brandModel->getBrandInfo();
        $arrBrandInfo = objectToArray($brandInfo);
        $gsModel = new GoodsSpecModel();
        $gcModel = new GoodsCodeModel();
        foreach ($newGoodsInfo as $value) {
            //组装商品基本信息
            $goodsInfo = $this->generalGoodsInfo($value, $arrBrandInfo);
            $goodsInfoList[] = $goodsInfo;
            //组装商品规格信息
            $goods_sn = $goodsInfo['goods_sn'];
            $goodsSpecInfo = $gsModel->generalSpecInfo($value, $goods_sn);
            $specInfoList[] = $goodsSpecInfo;
            $platform_barcode = trim($value['platform_barcode']);
            $arrPlatformBarcode [] = $platform_barcode;
            //组装商品编码信息
            $spec_sn = $goodsSpecInfo['spec_sn'];
            $strGoodsCode = trim($value['platform_barcode']);
            $goodsCodeInfo = $gcModel->makeGoodsCode($strGoodsCode, $spec_sn, $suid);
            if (count($goodsCodeInfo) <= 0) {
                return false;
            }
            //将商品编码信息转为二维数组
            foreach ($goodsCodeInfo as $item) {
                $codeInfoList[] = $item;
            }
            $arr_update[] = [
                'platform_barcode' => $platform_barcode,
                'spec_sn' => $spec_sn,
            ];
        }
        //写入相应数据表
        $insertRes = $this->insertNewGoods($goodsInfoList, $specInfoList, $codeInfoList);
        //组装新品更新spec_sn的语句
        $table = 'jms_ord_new_goods';
        $arrSql = makeUpdateSql($table, $arr_update);
        return ['insertRes' => $insertRes, 'arrPlatformBarcode' => $arrPlatformBarcode, 'arrSql' => $arrSql];

    }


    /**
     * @description:总单新品补充-获取新品信息
     * @author:zhangdong
     * @date : 2019.04.18
     */
    public function getNewGoodsInfo($goodsData)
    {
        $arrGoodsInfo = [];
        foreach ($goodsData as $key => $value) {
            //查询方式 1，根据商家编码查询 2，根据规格码查询
            $queryType = 2;
            //查询商品信息
            $spec_sn = trim($value->spec_sn);
            $goodsInfo = '';
            if (!empty($spec_sn)) {
                $goodsInfo = $this->getGoodsInfo($spec_sn, $queryType);
            }
            $goodsInfo->goods_num = intval($value->goods_number);
            $goodsInfo->entrust_time = trim($value->entrust_time);
            $goodsInfo->sale_discount = trim($value->sale_discount);
            $goodsInfo->wait_buy_num = trim($value->wait_buy_num);
            $goodsInfo->platform_barcode = trim($value->platform_barcode);
            //如果可以查到商品信息则将该条信息保存
            $arrGoodsInfo[] = $goodsInfo;
        }//end of foreach
        return $arrGoodsInfo;
    }

    /**
     * @description:总单新品-新增规格-品名搜索
     * @author:zhangdong
     * @date:2019.04.22
     */
    public function searchGoodsName($goods_name)
    {
        $where = [
            ['goods_name', 'LIKE', '%' . $goods_name . '%'],
        ];
        $field = ['g.goods_sn', 'g.goods_name', 'b.name AS brand_name'];
        $brandModel = new BrandModel();
        $queryRes = DB::table($this->table)->select($field)->where($where)
            ->leftJoin($brandModel->getTable(), 'b.brand_id', 'g.brand_id')
            ->limit(20)->get();
        return $queryRes;
    }

    /**
     * @description:总单新品-通过商品货号获取商品信息
     * @author:zhangdong
     * @date:2019.04.22
     */
    public function getGoodsBySn($goods_sn)
    {
        $where = [
            ['goods_sn', $goods_sn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->first();
        return $queryRes;
    }

    /**
     * @description:获取商品信息
     * @author:zongxing
     * @date:2019.10.19
     */
    public function searchGoodsInfo($param_info)
    {
        $field = ['gs.spec_img', 'g.goods_name', 'g.brand_id', 'gs.spec_sn', 'gs.erp_merchant_no', 'gs.erp_prd_no', 'gs.erp_ref_no',
            'gs.spec_price', 'b.name as brand_name', 'gs.spec_weight', 'gs.estimate_weight', 'gs.spec_price_update_date'];
        $goods_obj = DB::table($this->table)->select($field)
            ->leftJoin('goods_spec as gs', 'gs.goods_sn', '=', 'g.goods_sn')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'g.brand_id');
        if (!empty($param_info['query_sn']) && isset($param_info['is_detail']) && $param_info['is_detail'] == 1) {//详情页
            $query_sn = trim($param_info['query_sn']);
            $goods_obj->where('gs.spec_sn', $query_sn);
        } elseif (isset($param_info['spec_arr']) && !empty($param_info['spec_arr'])) {
            $spec_arr = $param_info['spec_arr'];
            $goods_obj->whereIn('gs.spec_sn', $spec_arr);
        } elseif (!empty($param_info['query_sn']) && !preg_match_all('%[\x{4e00}-\x{9fa5}]%u', $param_info['query_sn'])) {
            //纯数字或字母
            $query_sn = trim($param_info['query_sn']);
            $query_sn = '%' . trim($query_sn) . '%';
            $goods_obj->orWhere(function ($where) use ($query_sn) {
                $query_sn = '%' . trim($query_sn) . '%';
                $where->orWhere('gs.goods_sn', 'LIKE', $query_sn);
                $where->orWhere('gs.spec_sn', 'LIKE', $query_sn);
                $where->orWhere('gs.erp_merchant_no', 'LIKE', $query_sn);
                $where->orWhere('gs.erp_prd_no', 'LIKE', $query_sn);
                $where->orWhere('gs.erp_ref_no', 'LIKE', $query_sn);
                $where->orWhere('g.goods_name', 'LIKE', $query_sn);
                $where->orWhere('g.ext_name', 'LIKE', $query_sn);
                $where->orWhere('g.keywords', 'LIKE', $query_sn);
            });
        } elseif (!empty($param_info['query_sn'])) {
            //包含中文
            //处理搜索字符串

            $query_sn = str_replace(' ', '', trim($param_info['query_sn']));
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
            $brand_info = $brand_obj->orderBy('modify_time', 'desc')->get();
            $brand_info = ObjectToArrayZ($brand_info);
            //去除搜索字段中的品牌信息
            $brand_id_arr = $common_str_arr = [];
            foreach ($brand_info as $k => $v) {
                $brand_id_arr[] = intval($v['brand_id']);
                $name_arr = [trim($v['name']), trim($v['name_en']), trim($v['name_cn'])];
                foreach ($name_arr as $k => $v) {
                    $v = strtolower($v);
                    $common_str = find_common_str($v, $query_sn);
                    if ($common_str && !in_array($common_str, $common_str_arr)) {
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
            //品牌ID条件
            if (!empty($brand_id_arr)) {
                $goods_obj->whereIn('b.brand_id', $brand_id_arr);
            }
            //提取数字、字母、汉字数组
            preg_match_all('%[0-9_-]{2,}%', $query_sn, $number_arr);
            preg_match_all('%[A-Za-z_-]{1,}%', $query_sn, $str_arr);
            preg_match_all('%[\x{4e00}-\x{9fa5}]%u', $query_sn, $gbk_arr);
            $gbk_str = implode("", $gbk_arr[0]);
            $gbk_arr = split_str($gbk_str);
            $total_arr = [$number_arr[0], $str_arr[0], $gbk_arr];
            $goods_obj->where(function ($where) use ($total_arr) {
                foreach ($total_arr as $k1 => $v1) {
                    if (!empty($v1)) {
                        foreach ($v1 as $k => $v) {
                            $v = '%' . trim($v) . '%';
                            $where->where('g.goods_name', 'LIKE', $v);
                        }
                    }
                }
            });
        }
        if (!empty($param_info['is_page']) && $param_info['is_page'] == 1) {
            $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
            $goods_info = $goods_obj->orderBy('gs.create_time', 'desc')->paginate($page_size);
            $goods_info = ObjectToArrayZ($goods_info);
            if (empty($goods_info['data'])) {
                return '';
            }
            return $goods_info;
        }
        $goods_info = $goods_obj->orderBy('gs.create_time', 'desc')->get();
        $goods_info = ObjectToArrayZ($goods_info);
        return $goods_info;
    }

    /**
     * description 处理商品代码-将商品代码维护到对照表中
     * author zhangdong
     * date 2019.11.06
     */
    public function operateErpPrdNo()
    {
        //获取没有被维护到对照表中的商品代码
        $arrErpPrdNo = $this->getErpPrdNo();
        if (count($arrErpPrdNo) == 0) {
            return [];
        }
        //组装写入对照表中的数据
        $insertData = [];
        foreach ($arrErpPrdNo as $value) {
            $insertData[] = [
                'spec_sn' => trim($value->spec_sn),
                'goods_code' => trim($value->erp_prd_no),
                'code_type' => $this->platform_no['ERP_PRD_NO'],
            ];
        }
        //write $insertData into goods_code
        $operateRes = DB::table('goods_code')->insert($insertData);
        return $operateRes;
    }

    /**
     * description 处理商品代码-将商品代码维护到对照表中
     * author zhangdong
     * date 2019.11.06
     */
    private function getErpPrdNo()
    {
        $fields = [
            'gs.spec_sn', 'gs.erp_prd_no'
        ];
        $where = [
            ['gs.is_trash', 0],
            [DB::raw('length(jms_gs.erp_prd_no)'), '>', 0],
            [DB::raw('length(jms_gs.erp_prd_no + 0)'), DB::raw('length(jms_gs.erp_prd_no)')],
        ];
        $queryRes = DB::table('goods_spec as gs')->select($fields)
            ->leftJoin('goods_code as gc', 'gs.erp_prd_no', 'gc.goods_code')
            ->where($where)->whereRaw('jms_gc.spec_sn IS NULL')->get();
        return $queryRes;
    }

    /*
     * @description 检查要报价的SKU信息是否合法
     * @author zhangdong
     * @date 2019.11.25
     */
    public function checkOfferGoods($goodsData)
    {
        $gcModel = new GoodsCodeModel();
        $gsModel = new GoodsSpecModel();
        $arrGoodsInfo = $arrNewGoods = [];
        foreach ($goodsData as $key => $value) {
            if ($key == 0) continue;
            //将平台条码组装成数组
            $strGoodsCode = trim($value[1]);
            $arrGoodsCode = $this->createGoodsCode($strGoodsCode);
            //检查商品名称，平台条码等必填信息
            if (count($arrGoodsCode) == 0) {
                return false;
            }
            //在商品规格码对照表中查询商品规格码-可优化成将sql查询放循环外
            $getSpecRes = $gcModel->getSpec($arrGoodsCode);
            $spec_sn = '';
            if (!is_null($getSpecRes)) {
                $spec_sn = trim($getSpecRes->spec_sn);
            }
            //查询商品信息-可优化成将sql查询放循环外
            $goodsInfo = '';
            if (!empty($spec_sn)) {
                $goodsInfo = $gsModel->getGoodsMsg($spec_sn);
            }
            //标记新品
            if (empty($goodsInfo)) {
                $arrNewGoods[] = $key + 1;
                continue;
            };
            $goodsInfo->sale_discount = !empty($value[4]) ? floatval($value[4]) : 0;
            $spec_price = floatval($goodsInfo->spec_price);
            $goodsInfo->spec_price = !empty($value[2]) ? floatval($value[2]) : $spec_price;
            $exw_discount = floatval($goodsInfo->exw_discount);
            $goodsInfo->exw_discount = !empty($value[3]) ? floatval($value[3]) : $exw_discount;
            $goodsInfo->platform_barcode = $strGoodsCode;
            //如果可以查到商品信息则将该条信息保存
            $arrGoodsInfo[] = $goodsInfo;
        }//end of foreach

        return [
            'arrGoodsInfo' => $arrGoodsInfo,
            'arrNewGoods' => $arrNewGoods,
        ];
    }//end of checkOfferGoods


    /**
     * description:通过名称获取商品信息
     * editor:zongxing
     * date : 2018.11.16
     * return Object
     */
    public function getGoodsTotalInfo($param_info)
    {
        $goods_obj = DB::table('goods as g')
            ->leftJoin('goods_spec as gs', 'gs.goods_sn', '=', 'g.goods_sn')
            ->leftJoin('goods_code as gc', 'gc.spec_sn', '=', 'gs.spec_sn');

        if (!empty($param_info['erp_merchant_no'])) {
            $goods_obj->orWhere('erp_merchant_no', trim($param_info['erp_merchant_no']));
        } elseif (!empty($param_info['goods_name'])) {
            $goods_obj->orWhere('g.goods_name', trim($param_info['goods_name']));
        } elseif (!empty($param_info['kaola_no'])) {
            $goods_obj->orWhere('gc.goods_code', trim($param_info['kaola_no']));
        } elseif (!empty($param_info['small_red_book'])) {
            $goods_obj->orWhere('gc.goods_code', trim($param_info['small_red_book']));
        } elseif (!empty($param_info['spec_sn'])) {
            $goods_obj->orWhere('gs.spec_sn', trim($param_info['spec_sn']));
        }

        $goods_list = $goods_obj->first();
        $goods_list = objectToArrayZ($goods_list);
        return $goods_list;
    }

    /**
     * description:获取乐天商品列表
     * author:zongxing
     * date:2020.02.21
     * return Array
     */
    public function ltGoodsList($param_info)
    {
        //获取乐天商品最新ID---这里暂时没有找到DB相关写法，暂时用原生代替
        $page = isset($param_info['page']) ? intval($param_info['page']) : 1;
        $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $start_page = ($page - 1) * $page_size;
        $lt_id_sql = "SELECT MAX(id) as id FROM jms_lt_goods_info";

        //组装拼接查询条件
        $tmp_add_str = '';
        $where = [];
        if (!empty($param_info['lt_prd_no'])) {
            $lt_prd_no = trim($param_info['lt_prd_no']);
            $tmp_add_str .= " lt_prd_no ='" . $lt_prd_no . "'";
            $where[] = ['lt_prd_no', $lt_prd_no];
        }
        if (!empty($param_info['erp_prd_no'])) {
            $erp_prd_no = trim($param_info['erp_prd_no']);
            $where[] = ['erp_prd_no', $erp_prd_no];
            if (!empty($tmp_add_str)) {
                $tmp_add_str .= ' and';
            }
            $tmp_add_str .= " erp_prd_no ='" . $erp_prd_no . "'";
        }
        if (!empty($param_info['erp_ref_no'])) {
            $erp_ref_no = trim($param_info['erp_ref_no']);
            $where[] = ['erp_ref_no', $erp_ref_no];
            if (!empty($tmp_add_str)) {
                $tmp_add_str .= ' and';
            }
            $tmp_add_str .= " erp_ref_no ='" . $erp_ref_no . "'";
        }
        $orWhere = [];
        if (!empty($param_info['brand_name'])) {
            $brand_name = trim($param_info['brand_name']);
            $brand_name_arr = reformKeywords($brand_name);
            foreach ($brand_name_arr as $k1 => $v1) {
                if (!empty($v1)) {
                    foreach ($v1 as $k => $v) {
                        $v = '%' . trim($v) . '%';
                        $orWhere[] = ['brand_name', 'like', $v];
                        $tmp_add_str .= " brand_name like '" . $v . "'";
                    }
                }
            }
        }
        if (!empty($param_info['goods_name']) || !empty($param_info['query_sn'])) {
            $query_sn = !empty($param_info['goods_name']) ? $param_info['goods_name'] : $param_info['query_sn'];
            $total_info = $this->createQueryGoodsList($query_sn);

            $is_str = false;
            if (!empty($tmp_add_str)) {
                $tmp_add_str .= ' and (';
                $is_str = true;
            }
            $tmp_goods_str = '';
            if (isset($total_info['total_arr']) && !empty($total_info['total_arr'])) {
                $total_arr = $total_info['total_arr'];
                foreach ($total_arr as $k1 => $v1) {
                    if (!empty($v1)) {
                        foreach ($v1 as $k => $v) {
                            if (!empty($tmp_goods_str)) {
                                $tmp_goods_str .= ' and';
                            }
                            $v = '%' . trim($v) . '%';
                            $tmp_goods_str .= " goods_name like '" . $v . "'";
                            $where[] = ['lgi.goods_name', 'like', $v];
                        }
                    }
                }
            }
            if (!empty($tmp_goods_str)) {
                $tmp_add_str .= $tmp_goods_str;
                if ($is_str) {
                    $tmp_add_str .= ' )';
                }
            }
        }
        if (!empty($tmp_add_str)) {
            $lt_id_sql .= ' where';
            $lt_id_sql .= $tmp_add_str;
        }
        $lt_id_sql .= " GROUP BY lt_prd_no limit " . $start_page . ',' . $page_size;
        $lt_goods_id_info = DB::select($lt_id_sql);
        $lt_goods_id_arr = objectToArrayZ($lt_goods_id_info);
        if (empty($lt_goods_id_arr)) {
            return false;
        }
        //根据id查询乐天商品数据
        $goods_info = DB::table('lt_goods_info as lgi')->whereIn('id', $lt_goods_id_arr)
            ->where(function ($query) use ($orWhere) {
                $query->orWhere($orWhere);
            })
            ->orderBy('lgi.create_time', 'DESC')->get();
        $goods_info = objectToArrayZ($goods_info);
        //获取乐天商品总数
        $total_num = DB::table('lt_goods_info')->distinct('lt_prd_no')->where($where)
            ->where(function ($query) use ($orWhere) {
                $query->orWhere($orWhere);
            })
            ->count('lt_prd_no');
        $goods_list = [
            'total_num' => $total_num,
            'goods_info' => $goods_info
        ];
        return $goods_list;
    }

    /**
     * description:获取乐天商品列表-获取商品美金原价波动数据
     * author:zongxing
     * date:2020.02.22
     * return Array
     */
    public function ltGoodsSpecPriceInfo($param_info)
    {
        $lt_goods_info_obj = DB::table('lt_goods_info');
        $where = [];
        if (!empty($param_info['download_date'])) {
            $download_date = trim($param_info['download_date']);
            $where[] = ['download_date', '=', $download_date];
        }
        if (!empty($param_info['start_time'])) {
            $start_time = trim($param_info['start_time']);
            $where[] = ['download_date', '>=', $start_time];
        }
        if (!empty($param_info['end_time'])) {
            $end_time = trim($param_info['end_time']);
            $where[] = ['download_date', '<=', $end_time];
        }
        if (!empty($param_info['lt_prd_no'])) {
            $lt_prd_no = trim($param_info['lt_prd_no']);
            $where[] = ['lt_prd_no', $lt_prd_no];
        }
        if (!empty($param_info['erp_prd_no'])) {
            $erp_prd_no = trim($param_info['erp_prd_no']);
            $where[] = ['erp_prd_no', $erp_prd_no];
        }
        if (!empty($param_info['erp_prd_no_arr'])) {
            $erp_prd_no_arr = $param_info['erp_prd_no_arr'];
            $lt_goods_info_obj->whereIn('erp_prd_no', $erp_prd_no_arr);
        }
        if (!empty($param_info['download_date_arr'])) {
            $download_date_arr = $param_info['download_date_arr'];
            $lt_goods_info_obj->whereIn('download_date', $download_date_arr);
        }
        $field = ['download_date', 'goods_name', 'lt_prd_no', 'erp_prd_no', 'spec_price'];
        $lt_goods_info = $lt_goods_info_obj->where($where)->orderBy('download_date', 'desc')->get($field);
        $lt_goods_info = objectToArrayZ($lt_goods_info);
        return $lt_goods_info;
    }

    /**
     * description:获取乐天商品美金原价数据
     * author:zongxing
     * date:2020.03.27
     * return Array
     */
    public function ltGoodsSpecPriceList($param_info)
    {
        $lt_goods_info_obj = DB::table('lt_goods_info');
        if (!empty($param_info['erp_prd_no_arr'])) {
            $erp_prd_no_arr = $param_info['erp_prd_no_arr'];
            $lt_goods_info_obj->whereIn('erp_prd_no', $erp_prd_no_arr);
        }
        if (!empty($param_info['download_date_arr'])) {
            $download_date_arr = $param_info['download_date_arr'];
            $lt_goods_info_obj->whereIn('download_date', $download_date_arr);
        }
        if (!empty($param_info['compare_date'])) {
            $compare_date = $param_info['compare_date'];
            $lt_goods_info_obj->where('download_date', '>=', $compare_date);
        }
        $field = [
            DB::raw('DISTINCT spec_price'),
            DB::raw('MAX(download_date) as download_date'),
            'erp_prd_no'
        ];
        $lt_goods_info = $lt_goods_info_obj->groupBy('erp_prd_no', 'spec_price')
            ->orderBy('erp_prd_no', 'desc')->orderBy('download_date', 'desc')->get($field);
        $lt_goods_info = objectToArrayZ($lt_goods_info);
        return $lt_goods_info;
    }

    /**
     * 获取商品最终信息
     * @param $param_info 用户传参信息
     * 必填：
     * query_sn:迪奥//商品 misMobile查货时必填
     * spec_arr:['10003069837','10002323760'] //规格码数组 web查货时必填
     * 选填：
     * usd_cny_rate:6//汇率
     * freight:3.5//运费
     * margin_rate:8//毛利
     * is_detail:1//是否为详情，1：是；0：不是，如果为1，还会生成商品图片
     * stock_info:22,33,99,97,98,96//库存信息
     * cost:41,43,63,85,86//基础折扣渠道信息
     * cut_middle:41,43,63,85,86//代采0.5折扣渠道信息
     * cut_one:41,43,63,85,86//代采1折扣渠道信息
     * cut_one_middle:41,43,63,85,86//代采1.5折扣渠道信息
     * cut_two:41,43,63,85,86//代采2折扣渠道信息
     * cut_other:41,43,63,85,86//代采其他折扣渠道信息
     * cut_other_value:3//代采计算需要增加的值（用于搜索代采+其他折扣、价格信息）
     * exw_modify:[
     * {
     * "channels_id": "86",
     * "exw_modify": 0.68
     * },
     * {
     * "channels_id": "43",
     * "exw_modify": 0.55
     * }
     * ]//exw折扣修改信息
     * freight_type:1//运输方式（值为接口返的数组的下标）
     * predict_pot_time:2020-03-15//预计到港时间
     * @return mixed
     */
    public function getGoodsFinalInfo($param_info)
    {
        if (empty($param_info['spec_arr']) && empty($param_info['query_sn'])) {
            return ['code' => '1001', 'msg' => '商品查询条件不能为空'];
        }
        //获取商品数据
        $goods_model = new GoodsModel();
        $goods_info = $goods_model->searchGoodsInfo($param_info);
        if (empty($goods_info)) {
            return ['code' => '1002', 'msg' => '暂无商品数据'];
        }
        $goods_total_list = $goods_info;
        $goods_info = isset($goods_info['data']) ? $goods_info['data'] : $goods_info;
        //收集商品和品牌信息
        $spec_sn_arr = [];
        foreach ($goods_info as $k => $v) {
            if (!in_array($v['spec_sn'], $spec_sn_arr)) {
                $spec_sn_arr[] = $v['spec_sn'];
            }
            $spec_img = './' . $v['spec_img'];
            if (empty($v['spec_img']) || !file_exists($spec_img)) {
                $goods_info[$k]['spec_img'] = '';
            }
        }
        $param['spec_sn_arr'] = $spec_sn_arr;
        //获取汇率
        $param['day_time'] = date('Y-m-d');
        $er_model = new ExchangeRateModel();
        $er_info = $er_model->exchangeRateList($param);
        $usd_cny_rate = !empty($param_info['usd_cny_rate']) ? floatval($param_info['usd_cny_rate']) :
            floatval($er_info[0]['usd_cny_rate']);
        //暂时保留
//        $usd_krw_rate = !empty($param_info['usd_krw_rate']) ? floatval($param_info['usd_krw_rate']) :
//            floatval($er_info[0]['usd_krw_rate']);
        //获取商品报价信息
//        $goods_sale_model = new GoodsSaleModel();
//        $goods_sale_info = $goods_sale_model->getGoodsSaleList($param);
//        $goods_sale_list = [];
//        if (!empty($goods_sale_info)) {
//            foreach ($goods_sale_info as $k => $v) {
//                $currency = $v['currency'];
//                $sale_price = $v['sale_price'];
//                if ($currency == 2 && $usd_cny_rate != 0) {
//                    $sale_price = $sale_price / $usd_cny_rate;
//                } elseif ($currency == 3 && $usd_krw_rate != 0) {
//                    $sale_price = $sale_price / $usd_krw_rate;
//                }
//                $goods_sale_list[$v['spec_sn']] = $sale_price;
//            }
//        }
        //获取采购最终折扣数据
        $param['buy_time'] = date('Y-m-d');
        $param['buy_time'] = '2020-04-01';
        $goods_discount_list = $this->getGoodsFinalDiscount($goods_info, $param);
        //暂时保留
//        $dt_model = new DiscountTypeModel();
//        $goods_discount_list = $dt_model->getFinalDiscount($goods_info, null, $buy_time);
//        $goods_discount_list = $dt_model->getCostAddDiscount($goods_info, $param);
        if (empty($goods_discount_list)) {
            return $goods_discount_list;
        }
        $channelTitleInfo = $goods_discount_list['channelTitleInfo'];
        $goods_discount_list = $goods_discount_list['goodsList'];
        //获取用户可以查看的字段信息
        $loginUserInfo = request()->user();
        $user_classify_id = $loginUserInfo->classify_id;
        $cf_model = new ClassifyFieldModel();
        $user_field_info = $cf_model->getClassifyField($user_classify_id);
        if (empty($user_field_info)) {
            return ['code' => '1003', 'msg' => '您暂无权限查看，请联系管理员'];
        }
        $user_field = [];
        foreach ($user_field_info as $k => $v) {
            $user_field[] = $v['field_name_en'];
        }
        //获取用户分类对应的店铺信息
        $cs_model = new ClassifyShopModel();
        $classify_shop_info = $cs_model->getClassifyShop($user_classify_id);
        $shop_id_arr = [];
        //整理erp库存信息
        foreach ($classify_shop_info as $k => $v) {
            if (empty($param_info['stock_info'])) {
                $shop_id_arr[] = $v['shop_id'];
                continue;
            }
            $post_stock_info = explode(',', $param_info['stock_info']);
            if (in_array($v['shop_id'], $post_stock_info)) {
                $shop_id_arr[] = $v['shop_id'];
            }
        }
        //获取商品库存信息
        $shop_stock_model = new ShopStockModel();
        $spec_stock_info = $shop_stock_model->getSpecStockInfo($spec_sn_arr, $shop_id_arr);
        $spec_stock_list = [];
        foreach ($spec_stock_info as $k => $v) {
            $spec_sn = $v['spec_sn'];
            $num = intval($v['stock']);
            if (!isset($spec_stock_list[$v['spec_sn']])) {
                $spec_stock_list[$spec_sn][0] = [
                    'shop_id' => 'total',
                    'shop_name' => '总计',
                    'stock' => $num,
                ];
                $spec_stock_list[$v['spec_sn']][] = $v;
                continue;
            }
            $spec_stock_list[$spec_sn][0]['stock'] += $num;
            $spec_stock_list[$v['spec_sn']][] = $v;
        }
        //组装最终返回数据
        $goods_list = $this->createFinalData($goods_discount_list, $param_info, $usd_cny_rate, $user_field,
            $spec_stock_list);
        if (isset($goods_total_list['data'])) {
            $goods_total_list['data'] = $goods_list;
        } else {
            $goods_total_list = $goods_list;
        }
        $total_info = compact('goods_total_list', 'channelTitleInfo');
        return $total_info;
    }

    /**
     * description 获取当前采购最终折扣数据
     * editor zongxing
     * date 2019.05.14
     * return Array
     * param:
     *  $goods_info:array:1 [
     *      'spec_sn' => '1234',
     *      'brand_id' => '1234',
     * ]
     *  $channels_id:渠道id
     */
    public function getGoodsFinalDiscount($goods_info, $param = [])
    {
        //收集商品的品牌id和spec_sn信息,主要是缩小查询品牌和商品时的范围
        $brand_id_arr = $spec_sn_arr = [];
        foreach ($goods_info as $k => $v) {
            $brand_id = intval($v['brand_id']);
            $spec_sn = trim($v['spec_sn']);
            if (!in_array($brand_id, $brand_id_arr)) {
                $brand_id_arr[] = $brand_id;
            }
            if (!in_array($spec_sn, $spec_sn_arr)) {
                $spec_sn_arr[] = $spec_sn;
            }
        }
        //获取选择时间对应的生成毛利所需当月渠道配置
        $dtr_model = new DiscountTypeRecordModel();
        //定义获取折扣的时间信息
        if (empty($param['buy_time'])) {
            $param['buy_time'] = date('Y-m-d');
            $start_date = Carbon::now()->firstOfMonth()->toDateString();
            $end_date = Carbon::now()->endOfMonth()->toDateString();
            $param['start_date'] = $start_date;
            $param['end_date'] = $end_date;
            $discount_month = date('Y-m');
        } else {
            $discount_month = date('Y-m', strtotime($param['buy_time']));
        }
        $dtrInfo = $dtr_model->getTotalDisTypeRedList($param);
        if (empty($dtrInfo)) {
            return $dtrInfo;//如果无折扣记录信息,则返回原商品数据
        }
        //组装需要计算的折扣档位
        $totalDiscountTypeArr = $costDiscountTypeArr = [];
        foreach ($dtrInfo as $k => $v) {
            $totalDiscountTypeArr[] = $v['base_id'];//品牌base折扣档位
            $totalDiscountTypeArr[] = $costDiscountTypeArr[] = $v['cost_id'];//品牌成本折扣档位
            if (!empty($v['brand_month_predict_id'])) {
                $totalDiscountTypeArr = array_merge($totalDiscountTypeArr,
                    explode(',', $v['brand_month_predict_id']));
            }
            if (!empty($v['cut_add_id'])) {
                $totalDiscountTypeArr = array_merge($totalDiscountTypeArr, explode(',', $v['cut_add_id']));
            }
        }
        //获取折扣种类列表
        $discountCatModel = new DiscountCatModel();
        $discountCatInfo = $discountCatModel->getDiscountCatList();
        $discountCatList = [];
        foreach ($discountCatInfo as $k => $v) {
            $discountCatList[$v['id']] = $v;
        }
        //获取品牌相关折扣信息
        $param['brand_id_arr'] = $brand_id_arr;
        $param['type_id_arr'] = $totalDiscountTypeArr;
        $brandDiscountList = $this->getBrandTotalDiscount($param);
        //组装品牌最终折扣
        $channelTitleInfo = [];
        $brandFinnalDiscount = $this->makeBrandFinalDiscount($brandDiscountList, $discountCatList, $channelTitleInfo);
        //获取商品相关折扣信息
        $param['spec_sn_arr'] = $spec_sn_arr;
        $gmcDiscountModel = new GmcDiscountModel();
        $specDiscountList = $gmcDiscountModel->getSpecTotalDiscount($param);
        //组装商品最终折扣
        $specFinnalDiscount = $this->makeSpecFinalDiscount($specDiscountList, $discountCatList, $channelTitleInfo);
        //获取商品当月最低折扣
        $param['discount_month'] = $discount_month;
        $param['channels_id_arr'] = array_keys($channelTitleInfo);
        $discountRecordModel = new DiscountRecordModel();
        $discountRecordInfo = $discountRecordModel->getDiscountRecordInfo($param);
        //组装返回数据
        $param = compact('goods_info', 'brandFinnalDiscount', 'specFinnalDiscount', 'discountRecordInfo',
            'channelTitleInfo', 'costDiscountTypeArr');
        $goodsList = $this->createFinalDiscount($param);
        $totalList = compact('goodsList', 'channelTitleInfo');
        return $totalList;
    }

    /**
     * description:组装商品最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function createFinalDiscount($param)
    {
        $goods_info = $param['goods_info'];
        $brandFinnalDiscount = $param['brandFinnalDiscount'];
        $specFinnalDiscount = $param['specFinnalDiscount'];
        $discountRecordInfo = $param['discountRecordInfo'];
        $channelTitleInfo = $param['channelTitleInfo'];
        $costDiscountTypeArr = $param['costDiscountTypeArr'];
        $discountRecordList = [];
        foreach ($discountRecordInfo as $k => $v) {
            $spec_sn = $v['spec_sn'];
            $channels_id = $v['channels_id'];
            $discount = $v['discount'];
            $discountRecordList[$spec_sn][$channels_id] = $discount;
        }
        //循环组装商品折扣信息
        foreach ($goods_info as $k => $v) {
            $brand_id = $v['brand_id'];
            $spec_sn = $v['spec_sn'];
            $channelInfo = $discountInfo = [];
            //品牌相关折扣
            if (isset($brandFinnalDiscount[$brand_id])) {
                $channelInfo = $brandFinnalDiscount[$brand_id];
            }
            //商品相关折扣
            if (isset($specFinnalDiscount[$spec_sn]) && empty($channelInfo)) {
                $channelInfo = $specFinnalDiscount[$spec_sn];
            } elseif (isset($specFinnalDiscount[$spec_sn]) && !empty($channelInfo)) {
                foreach ($specFinnalDiscount[$spec_sn] as $k1 => $v1) {
                    if (isset($channelInfo[$k1])) {
                        $channelInfo[$k1] = array_merge_recursive($channelInfo[$k1], $v1);
                    } else {
                        $channelInfo[$k1] = $v1;
                    }
                }
            }
            foreach ($channelInfo as $k2 => $v2) {
                if (!isset($channelTitleInfo[$k2])) continue;
                //渠道名称赋值
                $discountInfo[$k2]['channels_id'] = $channelTitleInfo[$k2]['channels_id'];
                $discountInfo[$k2]['channels_name'] = $channelTitleInfo[$k2]['channels_name'];
                $cost_discount = isset($v2['base_points']) ? $v2['base_points']['discount'] : 1;
                //计算最终折扣和成本折扣
                $final_discount = 1;
                foreach ($v2 as $k3 => $v3) {
                    $add_type = $v3['add_type'];
                    $type_id = $v3['type_id'];
                    $new_discount = $discountInfo[$k2][$k3] = $v3['discount'];
                    $final_discount = discountModify($add_type, $final_discount, $new_discount);
                    //计算成本折扣
                    if (in_array($type_id, $costDiscountTypeArr)) {
                        $cost_discount = discountModify($add_type, $cost_discount, $new_discount);
                    }
                }
                $discountInfo[$k2]['cost_discount'] = $cost_discount;
                $discountInfo[$k2]['final_discount'] = $final_discount;
                //计算当月最低折扣
                $monthLowestDiscount = 1;
                if (isset($discountRecordList[$spec_sn][$k2])) {
                    $monthLowestDiscount = $discountRecordList[$spec_sn][$k2];
                }
                $discountInfo[$k2]['month_lowest_discount'] = $monthLowestDiscount;
                //折扣取反计算比较特殊，需要计算出它的差值
                foreach ($v2 as $k4 => $v4) {
                    $add_type = $v4['add_type'];
                    $new_discount = $discountInfo[$k2][$k4] = $v4['discount'];
                    if ($add_type == 3) {
                        $discountInfo[$k2][$k4] = abs(1 - $new_discount - $cost_discount);
                    }
                }
            }
            $goods_info[$k]['channels_info'] = $discountInfo;
        }
        return $goods_info;
    }


    /**
     * description:组装品牌最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function makeBrandFinalDiscount($brandDiscountList, $discountCatList, &$channelTitleInfo)
    {
        $brandDiscountInfo = [];
        foreach ($brandDiscountList as $k => $v) {
            $type_cat = $v['type_cat'];
            if (!isset($discountCatList[$type_cat])) continue;
            $channels_id = $v['channels_id'];
            $channels_name = $v['channels_name'];
            $type_id = $v['type_id'];
            //判断渠道是否存在
            if (!isset($channelTitleInfo[$channels_id])) {
                $channelTitleInfo[$channels_id] = [
                    'channels_id' => $channels_id,
                    'channels_name' => $channels_name,
                ];
            }
            //判断渠道中的折扣种类是否存在
            if (!isset($channelTitleInfo[$channels_id]['cat_info'][$type_cat])) {
                $cat_name = $v['cat_name'];
                $cat_code = $v['cat_code'];
                $channelTitleInfo[$channels_id]['cat_info'][$type_cat] = [
                    'type_cat' => $type_cat,
                    'cat_name' => $cat_name,
                    'cat_code' => $cat_code,
                ];
            }
            $brand_id = $v['brand_id'];
            $add_type = $v['add_type'];
            $discount = $v['discount'];
            $cat_code = $discountCatList[$type_cat]['cat_code'];

            //组装品牌相关折扣
            $brandDiscountInfo[$brand_id][$channels_id][$cat_code] = [
                'type_id' => $type_id,
                'add_type' => $add_type,
                'discount' => $discount
            ];
        }
        return $brandDiscountInfo;
    }

    /**
     * description:组装商品最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function makeSpecFinalDiscount($specDiscountList, $discountCatList, &$channelTitleInfo)
    {
        $specDiscountInfo = [];
        foreach ($specDiscountList as $k => $v) {
            $type_cat = $v['type_cat'];
            if (!isset($discountCatList[$type_cat])) continue;
            $channels_id = $v['channels_id'];
            $channels_name = $v['channels_name'];
            $type_id = $v['type_id'];
            //判断渠道是否存在
            if (!isset($channelTitleInfo[$channels_id])) {
                $channelTitleInfo[$channels_id] = [
                    'channels_id' => $channels_id,
                    'channels_name' => $channels_name,
                ];
            }
            //判断渠道中的折扣种类是否存在
            if (!isset($channelTitleInfo[$channels_id]['cat_info'][$type_cat])) {
                $cat_name = $v['cat_name'];
                $cat_code = $v['cat_code'];
                $channelTitleInfo[$channels_id]['cat_info'][$type_cat] = [
                    'type_cat' => $type_cat,
                    'cat_name' => $cat_name,
                    'cat_code' => $cat_code,
                ];
            }
            $add_type = $v['add_type'];
            $discount = $v['discount'];
            $spec_sn = $v['spec_sn'];
            $cat_code = $discountCatList[$type_cat]['cat_code'];
            //组装品牌相关折扣
            $specDiscountInfo[$spec_sn][$channels_id][$cat_code] = [
                'type_id' => $type_id,
                'add_type' => $add_type,
                'discount' => $discount
            ];
        }
        return $specDiscountInfo;
    }

    /**
     * description:获取品牌最终折扣
     * editor:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getBrandFinalDiscount($param)
    {
        $buy_time = $param['buy_time'];
        $channels_id = $param['channels_id'];
        $month_type_arr = $param['month_type_arr'];
        $brand_id_arr = $param['brand_id_arr'];
        $predict_type_arr = $param['predict_type_arr'];
        $param_c = [
            'channels_id' => $channels_id,
            'buy_time' => $buy_time,
            'brand_id_arr' => $brand_id_arr,
            'brand_type_id' => $month_type_arr,
        ];
        $brand_predict_info = $this->getBrandDiscount($param_c, 'type_cat');
        $bd_format_info = $bm_format_info = $bg_format_info = $bh_format_info = [];
        foreach ($brand_predict_info as $k => $v) {
            if (in_array($k, [2])) {//获取品牌预计完成档位折扣信息
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bd_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            } elseif (in_array($k, [4])) {//获取月度品牌活动追加
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bm_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            } elseif (in_array($k, [9])) {//获取品牌档位追加
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bg_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            } elseif (in_array($k, [10])) {//获取品牌HDW活动追加
                foreach ($v as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        foreach ($v2 as $k3 => $v3) {
                            if (in_array($k3, $predict_type_arr)) {
                                $bh_format_info[$k1][$k2] = $v3;
                            }
                        }
                    }
                }
            }
        }
        $param_c = [
            'bd_format_info' => $bd_format_info,
            'bm_format_info' => $bm_format_info,
            'bg_format_info' => $bg_format_info,
            'bh_format_info' => $bh_format_info,
            'cost_list' => $param['cost_list'],
            'pay_list' => $param['pay_list'],
        ];
        $brand_final_discount = $this->createBrandFinalDiscount($param_c);
        return $brand_final_discount;
    }

    /**
     * description:获取当前品牌折扣数据
     * author:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getBrandDiscount($param, $group_str)
    {
        $type_arr = $param['brand_type_id'];
        $field = [
            'b.name', 'b.brand_id', 'b.name_en', 'pm.method_sn', 'pm.method_name', 'pm.method_property', 'pc.channels_sn',
            'pc.channels_name', 'd.shipment', 'pc.post_discount', 'pc.is_count_wai', 'pc.id as channels_id',
            'type_name', 'dt.discount as brand_discount', 'dt.discount', 'dt.type_id', 'dti.type_cat',
            'dti.add_type', 'dc.cat_name', 'dc.cat_code',
        ];
        $discount_obj = DB::table('discount_type as dt')->select($field)
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'd.brand_id')
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'd.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'd.channels_id')
            ->leftJoin('discount_cat as dc', 'dc.id', '=', 'dti.type_cat')
            ->whereIn('dti.id', $type_arr)
            ->orderBy('dti.type_cat', 'ASC');
        if (!empty($param['buy_time'])) {
            $buy_time = trim($param['buy_time']);
            $discount_obj->where('dt.start_date', '<=', $buy_time);
            $discount_obj->where('dt.end_date', '>=', $buy_time);
        }
        if (!empty($param['brand_id_arr'])) {
            $brand_id_arr = $param['brand_id_arr'];
            $discount_obj->whereIn('d.brand_id', $brand_id_arr);
        }
        if (!empty($param['channels_id'])) {
            $channels_id = intval($param['channels_id']);
            $discount_obj->where('dti.channels_id', $channels_id);
        }
        $discount_info = $discount_obj->get();
        $discount_info = objectToArrayZ($discount_info);
        $formatDiscountInfo = [];
        foreach ($discount_info as $k => $v) {
            $group_key = $v[$group_str];
            $pin_str = $v['channels_sn'] . '-' . $v['method_sn'];
            $type_id = $v['type_id'];
            if ($group_str == 'type_id') {
                $formatDiscountInfo[$group_key][$v['brand_id']][$pin_str] = $v;
            } elseif ($group_str == 'type_cat') {
                $formatDiscountInfo[$group_key][$v['brand_id']][$pin_str][$type_id] = $v;
            }
        }
        return $formatDiscountInfo;
    }

    /**
     * description:获取品牌相关折扣信息
     * author:zongxing
     * date : 2019.08.22
     * return Array
     */
    public function getBrandTotalDiscount($param)
    {
        $field = [
            'b.name', 'b.brand_id', 'b.name_en', 'pm.method_sn', 'pm.method_name', 'pm.method_property', 'pc.channels_sn',
            'pc.channels_name', 'd.shipment', 'pc.post_discount', 'pc.is_count_wai', 'pc.id as channels_id',
            'type_name', 'dt.discount as brand_discount', 'dt.discount', 'dt.type_id', 'dti.type_cat',
            'dti.add_type', 'dc.cat_name', 'dc.cat_code',
        ];
        $discount_obj = DB::table('discount_type as dt')->select($field)
            ->leftJoin('discount_type_info as dti', 'dti.id', '=', 'dt.type_id')
            ->leftJoin('discount as d', 'd.id', '=', 'dt.discount_id')
            ->leftJoin('brand as b', 'b.brand_id', '=', 'd.brand_id')
            ->leftJoin('purchase_method as pm', 'pm.id', '=', 'd.method_id')
            ->leftJoin('purchase_channels as pc', 'pc.id', '=', 'd.channels_id')
            ->leftJoin('discount_cat as dc', 'dc.id', '=', 'dti.type_cat');
        if (!empty($param['buy_time'])) {
            $buy_time = trim($param['buy_time']);
            $discount_obj->where('dt.start_date', '<=', $buy_time);
            $discount_obj->where('dt.end_date', '>=', $buy_time);
        }
        if (!empty($param['brand_id_arr'])) {
            $brand_id_arr = $param['brand_id_arr'];
            $discount_obj->whereIn('d.brand_id', $brand_id_arr);
        }
        if (!empty($param['type_id_arr'])) {
            $type_id_arr = $param['type_id_arr'];
            $discount_obj->whereIn('dti.id', $type_id_arr);
        }
        if (!empty($param['channels_id'])) {
            $channels_id = intval($param['channels_id']);
            $discount_obj->where('dti.channels_id', $channels_id);
        }
        $discountInfo = $discount_obj->orderBy('dti.type_cat', 'ASC')->get();
        $discountInfo = objectToArrayZ($discountInfo);
        return $discountInfo;
    }


    /**
     * 组装最终返回数据
     * @param $goods_discount_list //商品列表信息
     * @param $param_info //传入参数
     * @param $usd_cny_rate //美金对人民币汇率
     * @param $user_field //用户可见字段
     * @param $spec_stock_list //用户可见库存
     * @param $goods_sale_list //商品报价信息
     * @return mixed
     */
    public function createFinalData_stop($goods_discount_list, $param_info, $usd_cny_rate, $user_field,
                                         $spec_stock_list, $goods_sale_list)
    {
        $freight = !empty($param_info['freight']) ? floatval($param_info['freight']) : 3;//运费
        $margin_rate = !empty($param_info['margin_rate']) ? floatval($param_info['margin_rate']) / 100 : 0.08;//毛利
        //是否进行exw折扣修改
        $discount_modify = [];
        if (!empty($param_info['exw_modify'])) {
            $exw_modify_info = json_decode($param_info['exw_modify'], true);
            foreach ($exw_modify_info as $k => $v) {
                $channels_id = $v['channels_id'];
                $exw_modify = $v['exw_modify'];
                $discount_modify[$channels_id] = $exw_modify;
            }
        }
        $channel_name_info = [];
        foreach ($goods_discount_list as $k => $v) {
            $spec_sn = trim($v['spec_sn']);
            $spec_price = floatval($v['spec_price']);
            $spec_weight = floatval($v['spec_weight']) > 0 ? floatval($v['spec_weight']) : floatval($v['estimate_weight']);
            $goods_discount_list[$k]['spec_weight'] = $spec_weight;
            //运费
            $goods_freight = $spec_weight * $freight;
            $goods_discount_list[$k]['freight'] = $goods_freight;
            //库存
            $stock_info = [];
            if (isset($spec_stock_list[$spec_sn])) {
                $stock_info = $spec_stock_list[$spec_sn];
            }
            $goods_discount_list[$k] = direct_array_push($goods_discount_list[$k], 'stock_info', $stock_info,
                'channels_info');
            //渠道折扣报价信息
            if (isset($v['channels_info'])) {
                foreach ($v['channels_info'] as $k1 => $v1) {
                    if (!isset($channel_name_info[$v1['channels_id']])) {
                        $channel_name_info[$v1['channels_id']] = [
                            'channels_id' => $v1['channels_id'],
                            'channels_name' => $v1['channels_name'],
                        ];
                    }
                    //是否进行exw折扣修改
                    $cost_discount = number_format($v1['cost_discount'], 3);
                    if (isset($discount_modify[$k1])) {
                        $cost_discount = number_format($discount_modify[$k1], 3);
                    }
                    $v['channels_info'][$k1]['cost_discount'] = $cost_discount;
                    //是否为高价sku
                    //$v['channels_info'][$k1]['is_high'] = $v1['is_high'] == 1 ? '是' : '否';
                    //成本折扣、最终折扣、销售折扣、毛利折扣相关到港折扣、美金、人民币计算
                    $cut_str = ['cost', 'final', 'sale', 'margin'];
                    $tmp_discount = 0;
                    foreach ($cut_str as $k3 => $v3) {
                        if ($v3 == 'cost') {
                            $tmp_discount = $cost_discount;
                        } elseif ($v3 == 'final') {
                            $tmp_discount = floatval($v1['brand_discount']);
                        } elseif ($v3 == 'sale') {
                            $tmp_discount = floatval($v1['brand_discount']) + $margin_rate;
                            if (isset($goods_sale_list[$spec_sn])) {
                                $tmp_discount = $goods_sale_list[$spec_sn] / $spec_price;
                            }
                        }
                        $port_discount = 0;
                        if ($v3 == 'margin') {
                            $port_discount = $margin_rate * 100;
                            $final_port_price = str_replace(',', '', $v['channels_info'][$k1]['final_port_price']);
                            $sale_port_price = str_replace(',', '', $v['channels_info'][$k1]['sale_port_price']);
                            $port_price = $sale_port_price - $final_port_price;
                        } else {
                            if ($spec_price) {
                                $port_discount = ($tmp_discount * $spec_price + $goods_freight) / $spec_price * 100;
                            }
                            $port_price = $port_discount * $spec_price + $spec_weight * $freight;
                        }
                        $v['channels_info'][$k1][$v3 . '_port_discount'] = number_format($port_discount, 3) . '%';
                        $v['channels_info'][$k1][$v3 . '_port_price'] = number_format($port_price, 2);
                        $v['channels_info'][$k1][$v3 . '_port_ren_price'] = number_format($port_price * $usd_cny_rate, 2);
                    }
                    //exw计算价格
                    $cost_cny_price = $spec_price * $cost_discount * $usd_cny_rate;
                    $v['channels_info'][$k1]['cost_usd_price'] = number_format($spec_price * $cost_discount, 2);
                    $v['channels_info'][$k1]['cost_cny_price'] = number_format($cost_cny_price, 2);
                    //代采、全包折扣等信息计算
                    $cut_str = [
                        'cut_middle' => 0.005,
                        'cut_one' => 0.01,
                        'cut_one_middle' => 0.015,
                        'cut_two' => 0.02,
                    ];
                    if (!empty($param_info['cut_other_value'])) {
                        $cut_str['cut_other'] = $param_info['cut_other_value'] / 100;
                    }
                    foreach ($cut_str as $k2 => $v2) {
                        //折扣
                        $discount = $cost_discount + $v2;
                        //美金=美金原价*（exw+0.5%）+(运费 x 重量)
                        $tmp_usd_price = $spec_price * $discount + $goods_freight;
                        //人民币=美金*汇率
                        $tmp_cny_price = $tmp_usd_price * $usd_cny_rate;
                        $v['channels_info'][$k1][$k2 . '_discount'] = number_format($discount, 3);
                        $v['channels_info'][$k1][$k2 . '_freight'] = number_format($goods_freight, 2);
                        $v['channels_info'][$k1][$k2 . '_usd_price'] = number_format($tmp_usd_price, 2);
                        $v['channels_info'][$k1][$k2 . '_cny_price'] = number_format($tmp_cny_price, 2);
                    }
                }
                $goods_discount_list[$k]['channels_info'] = $v['channels_info'];
            }
            foreach ($goods_discount_list[$k] as $kf1 => $vf1) {
                if ($kf1 == 'channels_info') {
                    foreach ($vf1 as $kf2 => $vf2) {
                        foreach ($vf2 as $kf3 => $vf3) {
                            if (!in_array($kf3, $user_field) && $kf3 != 'channels_id') {
                                unset($goods_discount_list[$k]['channels_info'][$kf2][$kf3]);
                            }
                        }
                    }
                    $goods_discount_list[$k]['channels_info'] = array_values($goods_discount_list[$k]['channels_info']);
                }
                if (!in_array($kf1, $user_field) && $kf1 != 'channel_name_info') {
                    unset($goods_discount_list[$k][$kf1]);
                }
            }
        }
        foreach ($goods_discount_list as $k => $v) {
            $goods_discount_list[$k]['channel_name_info'] = array_values($channel_name_info);
        }
        return $goods_discount_list;
    }

    /**
     * 组装最终返回数据
     * @param $goods_discount_list //商品列表信息
     * @param $param_info //传入参数
     * @param $usd_cny_rate //美金对人民币汇率
     * @param $user_field //用户可见字段
     * @param $spec_stock_list //用户可见库存
//     * @param $goods_sale_list //商品报价信息
     * @return mixed
     */
    public function createFinalData($goods_discount_list, $param_info, $usd_cny_rate, $user_field,
                                    $spec_stock_list)
    {
        $freight = !empty($param_info['freight']) ? floatval($param_info['freight']) : 3;//运费
//        $margin_rate = !empty($param_info['margin_rate']) ? floatval($param_info['margin_rate']) / 100 : 0.08;//毛利
        //是否进行exw折扣修改
        $discount_modify = [];
        if (!empty($param_info['exw_modify'])) {
            $exw_modify_info = json_decode($param_info['exw_modify'], true);
            foreach ($exw_modify_info as $k => $v) {
                $channels_id = $v['channels_id'];
                $exw_modify = $v['exw_modify'];
                $discount_modify[$channels_id] = $exw_modify;
            }
        }
        //获取客户对应的代采加点信息
        $saleUserId = $param_info['sale_user_id'];
        $discountType = $param_info['discount_type'];
        $userDiscountModel = new UserDiscountModel();
        $userDiscountInfo = $userDiscountModel->getUserDisc($saleUserId, $discountType);
        $cut_add_point = $userDiscountInfo->discount;
        foreach ($goods_discount_list as $k => $v) {
            $spec_sn = trim($v['spec_sn']);
            $spec_price = floatval($v['spec_price']);
            $spec_weight = floatval($v['spec_weight']) > 0 ? floatval($v['spec_weight']) : floatval($v['estimate_weight']);
            $goods_discount_list[$k]['spec_weight'] = $spec_weight;
            //运费
            $goods_freight = $spec_weight * $freight;
            $goods_discount_list[$k]['freight'] = $goods_freight;
            //库存
            $stock_info = [];
            if (isset($spec_stock_list[$spec_sn])) {
                $stock_info = $spec_stock_list[$spec_sn];
            }
            $goods_discount_list[$k] = direct_array_push($goods_discount_list[$k], 'stock_info', $stock_info,
                'channels_info');
            //渠道折扣报价信息
            if (isset($v['channels_info'])) {
                $channel_name_info = $titleInfo = $lowestDiscountInfo = [];
                $lowest_discount = 1;
                foreach ($v['channels_info'] as $k1 => $v1) {
                    if (!isset($channel_name_info[$v1['channels_id']])) {
                        $channel_name_info[$v1['channels_id']] = [
                            'channels_id' => $v1['channels_id'],
                            'channels_name' => $v1['channels_name'],
                        ];
                    }
                    $final_discount = $v1['final_discount'];
                    if ($final_discount < $lowest_discount) {
                        $lowest_discount = $final_discount;
                        $lowestDiscountInfo = [
                            'channels_id' => $v1['channels_id'],
                            'channels_name' => $v1['channels_name'],
                            'lowest_discount' => $final_discount,
                        ];
                    }
                    //折扣标题收集
                    $other_field = ['channels_name'];
                    foreach ($v1 as $k4 => $v4) {
                        if (!in_array($k4, $titleInfo) && in_array($k4, $user_field) && !in_array($k4, $other_field)) {
                            $titleInfo[] = $k4;
                        }
                    }
                    //是否进行exw折扣修改
                    $cost_discount = number_format($v1['cost_discount'], 3);
                    if (isset($discount_modify[$k1])) {
                        $cost_discount = number_format($discount_modify[$k1], 3);
                    }
                    $v['channels_info'][$k1]['cost_discount'] = $cost_discount;
                    //暂时保留
                    //是否为高价sku
                    //$v['channels_info'][$k1]['is_high'] = $v1['is_high'] == 1 ? '是' : '否';
                    //成本折扣、最终折扣、销售折扣、毛利折扣相关到港折扣、美金、人民币计算
//                    $cut_str = ['cost', 'final', 'sale', 'margin'];
//                    $tmp_discount = 1;
//                    foreach ($cut_str as $k3 => $v3) {
//                        if ($v3 == 'cost') {
//                            $tmp_discount = $cost_discount;
//                        } elseif ($v3 == 'final') {
//                            $tmp_discount = floatval($v1['final_discount']);
//                        } elseif ($v3 == 'sale') {
//                            $tmp_discount = floatval($v1['final_discount']) + $margin_rate;
//                            if (isset($goods_sale_list[$spec_sn])) {
//                                $tmp_discount = $goods_sale_list[$spec_sn] / $spec_price;
//                            }
//                        }
//                        $port_discount = 0;
//                        if ($v3 == 'margin') {
//                            $port_discount = $margin_rate * 100;
//                            $final_port_price = str_replace(',', '', $v['channels_info'][$k1]['final_port_price']);
//                            $sale_port_price = str_replace(',', '', $v['channels_info'][$k1]['sale_port_price']);
//                            $port_price = $sale_port_price - $final_port_price;
//                        } else {
//                            if ($spec_price) {
//                                $port_discount = ($tmp_discount * $spec_price + $goods_freight) / $spec_price * 100;
//                            }
//                            $port_price = $port_discount * $spec_price + $spec_weight * $freight;
//                        }
//                        $v['channels_info'][$k1][$v3 . '_port_discount'] = number_format($port_discount, 3) . '%';
//                        $v['channels_info'][$k1][$v3 . '_port_price'] = number_format($port_price, 2);
//                        $v['channels_info'][$k1][$v3 . '_port_ren_price'] = number_format($port_price *
//                            $usd_cny_rate, 2);
//                    }
                    //exw计算价格
//                    $cost_cny_price = $spec_price * $cost_discount * $usd_cny_rate;
//                    $v['channels_info'][$k1]['cost_usd_price'] = number_format($spec_price * $cost_discount, 2);
//                    $v['channels_info'][$k1]['cost_cny_price'] = number_format($cost_cny_price, 2);
                    //代采、全包折扣等信息计算
//                    $cut_str = [
//                        'cut_middle' => 0.005,
//                        'cut_one' => 0.01,
//                        'cut_one_middle' => 0.015,
//                        'cut_two' => 0.02,
//                    ];
//                    if (!empty($param_info['cut_other_value'])) {
//                        $cut_str['cut_other'] = $param_info['cut_other_value'] / 100;
//                    }
//                    foreach ($cut_str as $k2 => $v2) {
//                        //折扣
//                        $discount = $cost_discount + $v2;
//                        //美金=美金原价*（exw+0.5%）+(运费 x 重量)
//                        $tmp_usd_price = $spec_price * $discount + $goods_freight;
//                        //人民币=美金*汇率
//                        $tmp_cny_price = $tmp_usd_price * $usd_cny_rate;
//                        $v['channels_info'][$k1][$k2 . '_discount'] = number_format($discount, 3);
//                        $v['channels_info'][$k1][$k2 . '_freight'] = number_format($goods_freight, 2);
//                        $v['channels_info'][$k1][$k2 . '_usd_price'] = number_format($tmp_usd_price, 2);
//                        $v['channels_info'][$k1][$k2 . '_cny_price'] = number_format($tmp_cny_price, 2);
//                    }
                }
                $goods_discount_list[$k]['channels_info'] = $v['channels_info'];
                $goods_discount_list[$k]['channel_name_info'] = array_values($channel_name_info);
                $goods_discount_list[$k]['title_info'] = $titleInfo;
                //最有渠道信息
                $goods_discount_list[$k]['lowest_channel_info'] = $lowestDiscountInfo;
                //代采折扣信息 如果没填写参考折扣，则用最优渠道折扣进行计算
                $discount = !empty($param_info['discount']) ? floatval($param_info['discount']) : $lowest_discount;
                $cut_discount = $discount + $cut_add_point;
                $cut_usd_price = $cut_discount * $spec_price + $goods_freight;
                $cut_cny_price = $cut_usd_price * $usd_cny_rate;
                $goods_discount_list[$k]['cut_info'] = [
                    'cut_add_point' => $cut_add_point,
                    'cut_discount' => $cut_discount,
                    'cut_usd_price' => $cut_usd_price,
                    'cut_cny_price' => $cut_cny_price,
                ];
            }
            //判断权限
            $other_field = ['channel_name_info', 'title_info', 'lowest_channel_info', 'cut_info'];
            foreach ($goods_discount_list[$k] as $kf1 => $vf1) {
                if ($kf1 == 'channels_info') {
                    foreach ($vf1 as $kf2 => $vf2) {
                        foreach ($vf2 as $kf3 => $vf3) {
                            if (!in_array($kf3, $user_field) && $kf3 != 'channels_id') {
                                unset($goods_discount_list[$k]['channels_info'][$kf2][$kf3]);
                            }
                        }
                    }
                    $goods_discount_list[$k]['channels_info'] = array_values($goods_discount_list[$k]['channels_info']);
                }
                if ($kf1 == 'cut_info') {
                    foreach ($vf1 as $kf4 => $vf4) {
                        if (!in_array($kf4, $user_field)) {
                            unset($goods_discount_list[$k]['cut_info'][$kf4]);
                        }
                    }
                    $goods_discount_list[$k]['channels_info'] = array_values($goods_discount_list[$k]['channels_info']);
                }
                if (!in_array($kf1, $user_field) && !in_array($kf1, $other_field)) {
                    unset($goods_discount_list[$k][$kf1]);
                }
            }
        }
        return $goods_discount_list;
    }


}//end of class
