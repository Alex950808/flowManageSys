<?php

namespace App\Api\Client\Controllers;

use App\Api\Vone\Controllers\BaseController;
use App\Model\Vone\ClassifyFieldModel;
use App\Model\Vone\ClassifyShopModel;
use App\Model\Vone\DiscountTypeModel;
use App\Model\Vone\ExchangeRateModel;
use App\Model\Vone\GoodsSaleModel;
use App\Model\Vone\ShopStockModel;
use App\Model\Vone\UserModel;
use Carbon\Carbon;
use Dingo\Api\Http\Request;
//商品模型 add by zhangdong on the 2018.08.17
use App\Model\Vone\GoodsModel;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;

//create by zongxing on the 2019.11.26
class GoodsController extends BaseController
{
    /**
     * 运输方式
     * @var array
     */
    public $freight_type = [0 => '无', 1 => '空运', 2 => '海运'];

    /**
     * 获取指定商品最终数据
     * author zongxing
     * date 2020/1/2
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGoodsFinalDiscount(Request $request)
    {
        //获取商品最终数据
        $param_info = $request->toArray();
        $rules = [
            'query_sn' => 'required',
            'usd_cny_rate' => 'sometimes|numeric|not_in:0|min:0',
        ];
        $messages = [
            'query_sn.required' => '商品查询条件不能为空',
            'usd_cny_rate.numeric' => '汇率必须为数值',
            'usd_cny_rate.not_in' => '汇率必须大于0',
            'usd_cny_rate.min' => '汇率必须大于0',
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if($validator->fails()){
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }
        $goodsModel = new GoodsModel();
        $goods_final_info = $goodsModel->getGoodsFinalInfo($param_info);
        if (isset($goods_final_info['code'])) {
            return response()->json($goods_final_info);
        }
        $channelTitleInfo = $goods_final_info['channelTitleInfo'];
        $goods_final_list = $goods_final_info['goods_total_list'];
        //获取商品列表
        $goods_final_info = $goods_final_list;
        if (isset($goods_final_list['data'])) {
            $goods_final_info = $goods_final_list['data'];
        }
        //获取商品对应美金原价变动
        $compare_date = Carbon::parse()->subDays(90)->toDateString();
        $lt_spec_arr['compare_date'] = $compare_date;
        foreach ($goods_final_info as $k => $v) {
            if (empty($v['erp_prd_no'])) continue;
            if (!isset($lt_spec_arr['erp_prd_no_arr'])) {
                $lt_spec_arr['erp_prd_no_arr'][] = $v['erp_prd_no'];
                continue;
            }
            $lt_spec_arr['erp_prd_no_arr'][] = $v['erp_prd_no'];
        }
        //获取商品美金原价记录
        $ltGoodsSpecPriceInfo = $goodsModel->ltGoodsSpecPriceList($lt_spec_arr);
        //获取商品倒数第二次更新信息
        $ltGoodsSpecPriceList = [];
        foreach ($ltGoodsSpecPriceInfo as $k => $v) {
            $erp_prd_no = $v['erp_prd_no'];
            $download_date = $v['download_date'];
            $spec_price = $v['spec_price'];
            if (!isset($ltGoodsSpecPriceList[$erp_prd_no])) {
                $ltGoodsSpecPriceList[$erp_prd_no] = [
                    'spec_price' => $spec_price,
                    'download_date' => $download_date,
                    'num' => 1,
                ];
                continue;
            }
            if ($ltGoodsSpecPriceList[$erp_prd_no]['num'] > 1) continue;
            $ltGoodsSpecPriceList[$erp_prd_no] = [
                'spec_price' => $spec_price,
                'download_date' => $download_date,
                'num' => 2,
            ];
        }
        foreach ($goods_final_info as $k => $v) {
            if (empty($v['erp_prd_no']) || !isset($ltGoodsSpecPriceList[$v['erp_prd_no']])) {
                $data = '(近90日内乐天美金原价无波动)';
            } elseif (isset($ltGoodsSpecPriceList[$v['erp_prd_no']]) && $ltGoodsSpecPriceList[$v['erp_prd_no']]['num'] == 1) {
                $data = '(近90日内乐天美金原价无波动)';
            } else {
                $last_second_date = $ltGoodsSpecPriceList[$v['erp_prd_no']]['download_date'];
                $last_second_price = $ltGoodsSpecPriceList[$v['erp_prd_no']]['spec_price'];
                $data = "($last_second_date $$last_second_price)";
            }
            $insert_key = 'spec_price_update_info';
            $direct_key = 'spec_price_update_date';
            $goods_final_info[$k] = direct_array_push($goods_final_info[$k], $insert_key, $data, $direct_key);
        }
        //判断是否为详情页,如果为详情,则生成图片
        $path = '';
        $is_detail = !empty($param_info['is_detail']) ? intval($param_info['is_detail']) : 0;
        if ($is_detail == 1) {
            //获取用户信息
            $loginUserInfo = request()->user();
            $user_name = $loginUserInfo->user_name;
            $save_path = './image/offerPicture';
            $path = $this->createSalePicture($save_path, $goods_final_info['goods_list'][0], $param_info, $user_name);
        }
        if (isset($goods_final_list['data'])) {
            $goods_final_list['data'] = $goods_final_info;
        } else {
            $goods_final_list = $goods_final_info;
        }
        $data = compact('goods_final_list','channelTitleInfo');
        $return_info = ['code' => '1000', 'msg' => '获取指定商品列表成功', 'path' => $path, 'data' => $data];
        return response()->json($return_info);
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
    public function createFinalData($goods_discount_list, $param_info, $usd_cny_rate, $user_field,
                                    $spec_stock_list, $goods_sale_list)
    {
        $freight = !empty($param_info['freight']) ? floatval($param_info['freight']) : 3;//运费
        $margin_rate = !empty($param_info['margin_rate']) ? floatval($param_info['margin_rate']) / 100 : 0.08;//毛利
        //是否进行exw折扣修改
        if (!empty($param_info['exw_modify'])) {
            $exw_modify_info = json_decode($param_info['exw_modify'], true);
            $discount_modify = [];
            foreach ($exw_modify_info as $k => $v) {
                $channels_id = $v['channels_id'];
                $exw_modify = $v['exw_modify'];
                $discount_modify[$channels_id] = $exw_modify;
            }
        }
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
            $goods_discount_list[$k] = direct_array_push($goods_discount_list[$k], 'stock_info', $stock_info, 'channels_info');
            //渠道折扣报价信息
            $channel_name_info = [];
            if (isset($v['channels_info'])) {
                foreach ($v['channels_info'] as $k1 => $v1) {
                    if (!in_array($v1['channels_name'], $channel_name_info)) {
                        $channel_name_info[] = [
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
                $goods_discount_list[$k]['channel_name_info'] = $channel_name_info;
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
                    if ($kf1 == 'channels_info') {
                        unset($goods_discount_list[$k]['channel_name_info']);
                    }
                }
            }
        }
        return $goods_discount_list;
    }

    /**
     * 保存报价图片
     * @param $save_path 报价图片保存路径
     * @param $data 商品数据
     * @param $user_name 用户名
     * @param $param_info 参数
     * @return string
     */
    public function createSalePicture($save_path, $data, $param_info, $user_name)
    {
        //创建图片保存目录
        if (!file_exists($save_path)) {
            mkdir($save_path, 0777, true);
        }
        //定义基础信息
        $base = $this->createBaseData($save_path);
        //表格列x轴像素
        $total_column_num = max($base['table_column_num']);
        $key_arr = ['column_x_arr', 'column_x2_arr'];
        foreach ($key_arr as $k => $v) {
            for ($i = 0; $i <= $total_column_num; $i++) {
                $base[$v][] = $base['border'] + $base['filed_data_width'][$k] * $i;//每列竖边框线x轴像素
            }
        }
        //整理erp库存信息
        $stock_data = [];
        if (!empty($param_info['stock_info'])) {
            $stock_info = $data['stock_info'];
            $stock_data = $this->createStockData($param_info, $base, $stock_info);
        }
        //表格底部高度
        $base['border_bottom'] = $base['img_height'] - $base['border'];
        //创建画布
        $imageManager = new Image();
        $image = $imageManager::canvas($base['img_width'], $base['img_height'], '#fff');
        $border_top = $base['border_top'];
        $border = $base['border'];
        //商品图片
        if (!empty($data['spec_img']) && file_exists('./' . $data['spec_img'])) {
            $spec_img_url = './' . $data['spec_img'];
            $add_img_height = ($base['img_width'] - $base['border'] * 2) / 3;
            $spec_img = Image::make($spec_img_url)->resize($add_img_height, $add_img_height);
            $image->insert($spec_img, 'top-left', $border, $border);
            $image->resizeCanvas(0, $add_img_height + 1, 'top', true, '#fff');
            $total_add_height = $add_img_height + $border;
            $border_top += $total_add_height;
            $base['img_height'] += $total_add_height;
        }
        //商品信息
        $goods_column_info = [
            'goods_name' => '商品名称',
            'spec_sn' => '商品规格码',
            'erp_merchant_no' => '商家编码',
            'erp_prd_no' => '商品代码',
            'erp_ref_no' => '参考码',
            'spec_price' => '美金原价',
            'spec_weight' => '商品重量',
        ];
        $goods_data = [];
        foreach ($goods_column_info as $k => $v) {
            if (!isset($data[$k])) continue;
            $tmp_info = $data[$k];
            if ($k == 'spec_price') {
                $tmp_info = '$' . $tmp_info;
            } elseif ($k == 'spec_weight') {
                $tmp_info = $tmp_info . 'kg';
            }
            $goods_data[$v] = $tmp_info;
        }
        if (!empty($goods_data)) {
            //运输信息
            $freight_column_info = [
                'freight' => '运费',
                'freight_type' => '运输方式',
                'predict_pot_time' => '预计到港时间'
            ];
            $freight_data = [];
            foreach ($freight_column_info as $k => $v) {
                if (!isset($param_info[$k]) && !isset($data[$k])) continue;
                $tmp_info = $param_info[$k];
                if ($k == 'freight_type') {
                    $freight_type = $this->freight_type;
                    $tmp_info = $freight_type[$param_info[$k]];
                } elseif ($k == 'freight') {
                    $tmp_info = isset($data[$k]) ? $data[$k] : $param_info[$k];
                    $tmp_info = '$' . $tmp_info;
                }
                $freight_data[$v] = $tmp_info;
            }
            $image = $this->addGoodsData($image, $base, $border_top, $goods_data, $freight_data);
        }
        //生成ERP库存信息
        if (!empty($stock_data)) {
            $image = $this->addStockData($image, $base, $border_top, $stock_data);
        } else {
            $border_top += $base['row_hight'] - 5;
        }
        if (!empty($data['channels_info'])) {
            $discount_info = $data['channels_info'];
            //整理EXW折扣信息
            $cost_info = $cost_data = [];
            $cut_name_str = $cost_key_str = '';
            if (!empty($param_info['cost'])) {
                $cost_info = explode(',', $param_info['cost']);
                $cost_key_str = 'cost';
                $cut_name_str = 'EXW折扣';
            } else {
                $border_top += $base['table_title_height'][1] - 5;
            }
            if (!empty($cost_info)) {
                $per_row = 4;
                $cost_data = $this->createCutData($image, $cost_info, $base, $discount_info, $cost_key_str, $per_row);
            }
            //生成EXW折扣信息
            if (!empty($cost_data['channels_name'])) {
                $title_name = '基础折扣';
                $y_diff = 11;
                $per_row = 4;
                $image = $this->addCutData($image, $base, $border_top, $cost_data, $title_name, $cut_name_str,
                    $y_diff, $per_row);
            }
            //整理代采折扣信息
            $cut_info = $cut_data = [];
            $cut_key_str = '';
            if (!empty($param_info['cut_middle'])) {
                $cut_info = explode(',', $param_info['cut_middle']);
                $cut_key_str = 'cut_middle';
                $cut_name_str = 'EXW折扣+0.5';
            } elseif (!empty($param_info['cut_one'])) {
                $cut_info = explode(',', $param_info['cut_one']);
                $cut_key_str = 'cut_one';
                $cut_name_str = 'EXW折扣+1';
            } else {
                $border_top += $base['table_title_height'][1] - 5;
            }
            if (!empty($cut_info)) {
                $per_row = 5;
                $cut_data = $this->createCutData($image, $cut_info, $base, $discount_info, $cut_key_str, $per_row);
            }
            //生成代采折扣信息
            if (!empty($cut_data['channels_name'])) {
                $title_name = '代采折扣';
                $y_diff = 6;
                $image = $this->addCutData($image, $base, $border_top, $cut_data, $title_name, $cut_name_str, $y_diff);
            }
            //整理准现货预定折扣信息
            $spot_predict_info = $spot_predict_data = [];
            $cut_key_str = '';
            if (!empty($param_info['cut_one_middle'])) {
                $spot_predict_info = explode(',', $param_info['cut_one_middle']);
                $cut_key_str = 'cut_one_middle';
                $cut_name_str = 'EXW折扣+1.5';
            } elseif (!empty($param_info['cut_two'])) {
                $spot_predict_info = explode(',', $param_info['cut_two']);
                $cut_key_str = 'cut_two';
                $cut_name_str = 'EXW折扣+2';
            } else {
                $border_top += $base['table_title_height'][1] - 5;
            }
            if (!empty($spot_predict_info)) {
                $spot_predict_data = $this->createCutData($image, $spot_predict_info, $base, $discount_info, $cut_key_str);
            }
            //生成准现货预定折扣信息
            if (!empty($spot_predict_data['channels_name'])) {
                $title_name = '全包折扣';
                $y_diff = 1;
                $image = $this->addCutData($image, $base, $border_top, $spot_predict_data, $title_name, $cut_name_str, $y_diff);
            }
            //整理代采其他折扣信息
            $cut_other_info = $cut_other_data = [];
            if (!empty($param_info['cut_other_value']) && !empty($param_info['cut_other'])) {
                $cut_other_info = explode(',', $param_info['cut_other']);
                $cut_key_str = 'cut_other';
                $cut_name_str = '代采+';
            }
            if (!empty($cut_other_info)) {
                $cut_other_data = $this->createCutData($image, $cut_other_info, $base, $discount_info, $cut_key_str);
            }
            //生成代采其他折扣信息
            if (!empty($cut_other_data['channels_name'])) {
                $title_name = '代采其他折扣';
                $y_diff = -4;
                $image = $this->addCutData($image, $base, $border_top, $cut_other_data, $title_name, $cut_name_str, $y_diff);
            }
        }
        $image->resizeCanvas(0, 50, 'top', true, '#fff');
        //增加水印
        $width = $image->width();
        $high = $image->height();
        $text = '海代网-' . $user_name;
        for ($i = 50; $i <= $width;) {
            for ($j = 10; $j <= $high;) {
                $image->text($text, $i, $j, function ($font) use ($base) {
                    $font->file($base['font_url']);
                    $font->size($base['title_font_size']);
                    $font->align('center');
                    $font->valign('center');
                    $font->color(array(0, 0, 0, 0.2));
                    $font->angle(30);
                });
                $j = $j + 80;
            }
            $i = $i + 150;
        }
        //插入底部图片
        $haidai = Image::make('./image/haidai.png')->widen(500, function ($constraint) {
            $constraint->upsize();
        });
        $image->insert($haidai, 'bottom-center', 100, -100);
        //保存图片
        $spec_sn = $data['spec_sn'];
        $save_path .= '/' . $spec_sn . '.jpg';
        $image->save($save_path);
        $path = substr($save_path, 1);
        return $path;
    }

    /**
     * 整理库存信息
     * @param $param_info 参数
     * @param $base 定义的基础信息
     * @param $stock_info 商品库存信息
     * @return mixed
     */
    public function createStockData($param_info, &$base, $stock_info)
    {
        $stock_data = [];
        //计算表格行数
        $post_stock_info = explode(',', $param_info['stock_info']);
        $stock_info_row = 2;
        $total_stock_num = count($stock_info);
        if ($total_stock_num % 4 == 0) {
            $stock_info_row += ($total_stock_num / 4 - 1) * 2;
        } elseif ($total_stock_num % 4 != 0) {
            $stock_info_row += floor($total_stock_num / 4) * 2;
        }
        $stock_data['row'] = $stock_info_row;
        //给图片新增高度
        $base['img_height'] += $base['title_height'] + $base['row_hight'] * $stock_info_row;
        //重组库存信息
        foreach ($stock_info as $k => $v) {
            $shop_id = $v['shop_id'];
            if ($shop_id == 'total') {
                $stock_data['title'][] = '总计';
                $stock_data['stock_num'][] = $v['stock'];
                continue;
            }
            if (in_array($shop_id, $post_stock_info)) {
                $stock_data['title'][] = $v['shop_name'];
                $stock_data['stock_num'][] = $v['stock'];
            }
        }
        return $stock_data;
    }

    /**
     * 整理代采折扣信息
     * @param $cut_info 需要生成的准现货预定折扣信息
     * @param $base 定义的基础信息
     * @param $discount_info 商品折扣信息
     * @param $cut_key_str 代采折扣种类前缀
     * @return mixed
     */
    public function createCutData(&$image, $cut_info, &$base, $discount_info, $cut_key_str, $per_row = 5)
    {
        $cut_data = [];
        //计算表格行数
        $cut_info_num = count($cut_info) + 2;
        $table_column_num = $base['table_column_num'][1];
        if ($cut_info_num % $table_column_num == 0) {
            $per_row += ($cut_info_num / $table_column_num - 1) * $per_row;
        } elseif ($cut_info_num % $table_column_num != 0) {
            $per_row += floor($cut_info_num / $table_column_num) * $per_row;
        }
        $cut_data['row'] = $per_row;
        //给图片新增高度
        $add_img_height = $base['title_height'] + $base['row_hight'] * $per_row;
        $base['img_height'] += $add_img_height;
        $image->resizeCanvas(0, $add_img_height, 'top', true, '#fff');
        //重组折扣信息
        foreach ($discount_info as $k => $v) {
            $channels_id = $v['channels_id'];
            if (in_array($channels_id, $cut_info)) {
                $cut_data['channels_name'][] = $v['channels_name'];
                $cut_discount_str = $cut_key_str . '_discount';
                $cut_data['discount'][] = $v[$cut_discount_str];
                $cut_freight_str = $cut_key_str . '_freight';
                if (isset($v[$cut_freight_str])) {
                    $cut_data['freight'][] = $v[$cut_freight_str];
                }
                $cut_cny_price_str = $cut_key_str . '_cny_price';
                $cut_data['cny_price'][] = $v[$cut_cny_price_str];
                $cut_usd_price_str = $cut_key_str . '_usd_price';
                $cut_data['usd_price'][] = $v[$cut_usd_price_str];
            }
        }
        return $cut_data;
    }

    /**
     * 定义基础数据
     * @param $save_path 图片保存路径
     * @return array
     */
    public function createBaseData($save_path)
    {
        $base = [
            'border' => 20,//图片外边框
            'file_path' => $save_path,//图片保存路径
            'title_height' => 30,//报表名称高度
            'title_font_size' => 16,//报表名称字体大小
            'font_url' => public_path('font/msyh.ttf'),//字体文件路径
            'text_size' => 12,//正文字体大小
            'row_hight' => 25,//每行数据行高
            'filed_data_width' => [120, 96],//数据列的宽度
            'column_text_offset_arr' => [65],//表格第一列文字左偏移量
            'row_text_offset_arr' => [110, 60, 59, 59, 59],//数据列文字左偏移量
            'goods_info_row' => 7,//商品信息行数
            'table_title_height' => [15, 10],//表格与表格标题之间的距离
            'table_column_num' => [4, 5],//表格列数
            'table_row_num' => [4, 5],//表格行数
        ];

        //图片宽度
        $total_width_arr = [];
        foreach ($base['table_column_num'] as $k => $v) {
            $total_width_arr[] = $v * $base['filed_data_width'][$k];
        }
        $max_width = max($total_width_arr);
        $base['img_width'] = $max_width + $base['border'] * 2;
        //图片高度
        $base['border_top'] = $base['title_height'];//表格顶部高度
        $base['img_height'] = $base['goods_info_row'] * $base['row_hight'] + $base['border'] * 2 + $base['title_height'];
        return $base;
    }

    /**
     * @param $image 画布对象
     * @param $base 定义的基础信息
     * @param $border_top 数据顶部高度
     * @param $goods_data 商品信息
     * @param $freight_data 运输信息
     * @return mixed
     */
    public function addGoodsData($image, &$base, &$border_top, $goods_data, $freight_data)
    {
        $offset_arr = $base['border'];
        //商品信息数据左偏移量
        $goods_data_offset = 90;
        //dd($goods_data, $freight_data, $base['img_width'],$goods_data_width);
        //运输信息数据左偏移量
        $freight_title_offset = $base['img_width'] / 2 + 10;
        $freight_data_offset = $base['img_width'] / 2 + 90;
        //商品信息标题
        $image->text('商品信息', $offset_arr, $border_top, function ($font) use ($base) {
            $font->file($base['font_url']);
            $font->size($base['title_font_size']);
            $font->align('center');
            $font->valign('left');
        });
        //运输信息标题
        $image->text('运输信息', $freight_title_offset, $border_top, function ($font) use ($base) {
            $font->file($base['font_url']);
            $font->size($base['title_font_size']);
            $font->align('center');
            $font->valign('left');
        });
        $tmp_border_top = $border_top;
        //商品信息列
        foreach ($goods_data as $k => $v) {
            $border_top += $base['row_hight'];
            $image->text($k . '：', $offset_arr, $border_top, function ($font) use ($base) {
                $font->file($base['font_url']);
                $font->size($base['text_size']);
                $font->align('center');
                $font->valign('left');
            });
            //不是商品名称所在行
            if ($k != '商品名称') {
                $image->text($v, $goods_data_offset, $border_top, function ($font) use ($base) {
                    $font->file($base['font_url']);
                    $font->size($base['text_size']);
                    $font->align('center');
                    $font->valign('left');
                });
                continue;
            }
            //商品名称所在行
            $goods_name_len = 15;
            $goods_name_arr = splitStr($v, $goods_name_len);
            $tmp_top = $border_top;
            foreach ($goods_name_arr as $goods_name) {
                $image->text($goods_name, $goods_data_offset, $tmp_top, function ($font) use ($base) {
                    $font->file($base['font_url']);
                    $font->size($base['text_size']);
                    $font->align('center');
                    $font->valign('left');
                });
                $tmp_top += $base['row_hight'];
            }
            if (count($goods_name_arr) > 1) {
                $add_hight = $base['row_hight'] * (count($goods_name_arr) - 1);
                $image->resizeCanvas(0, $add_hight, 'top', true, '#fff');
                $border_top += $add_hight;
                $base['img_height'] += $add_hight;
            }
        }
        //运输信息列
        foreach ($freight_data as $k => $v) {
            $tmp_border_top += $base['row_hight'];
            $image->text($k . '：', $freight_title_offset, $tmp_border_top, function ($font) use ($base) {
                $font->file($base['font_url']);
                $font->size($base['text_size']);
                $font->align('center');
                $font->valign('left');
            });
            $image->text($v, $freight_data_offset, $tmp_border_top, function ($font) use ($base) {
                $font->file($base['font_url']);
                $font->size($base['text_size']);
                $font->align('center');
                $font->valign('left');
            });
        }
        return $image;
    }

    /**
     * 生成库存信息
     * @param $image 画布对象
     * @param $base 定义的基础信息
     * @param $border_top 数据顶部高度
     * @param $stock_data 库存信息
     * @return mixed
     */
    public function addStockData($image, $base, &$border_top, $stock_data)
    {
        $offset_arr = $base['border'];
        $border_top += $base['row_hight'];
        //标题
        $image->text('ERP库存', $offset_arr, $border_top, function ($font) use ($base) {
            $font->file($base['font_url']);
            $font->size($base['title_font_size']);
            $font->align('center');
            $font->valign('left');
        });
        //画竖线
        $column_xy_arr = $base['column_x_arr'];
        $image = $this->createYLine($image, $border_top, $base, $column_xy_arr, 15);
        //画横线
        $row_num = $stock_data['row'];
        $image = $this->createXLine($image, $border_top, $base, $row_num);
        //写入内容
        $border_top += $base['row_hight'] - $base['table_title_height'][1];
        $tmp_stock_height = $border_top + $base['row_hight'];
        $column_num = count($stock_data['title']);
        for ($i = 0; $i < $column_num; $i++) {
            if ($i > 0 && $i % 4 == 0) {
                $border_top += $base['row_hight'] * 2;
                $tmp_stock_height = $border_top + $base['row_hight'];
            }
            $tmp_key = $i % 4 + 1;
            $title = $stock_data['title'];
            $stock_num = $stock_data['stock_num'];
            //标题
            $image->text($title[$i], $base['column_x_arr'][$tmp_key] - $base['row_text_offset_arr'][$tmp_key], $border_top, function ($font) use ($base) {
                $font->file($base['font_url']);
                $font->size($base['text_size']);
                $font->align('center');
                $font->valign('middle');
            });
            //库存
            $image->text($stock_num[$i], $base['column_x_arr'][$tmp_key] - $base['row_text_offset_arr'][$tmp_key], $tmp_stock_height, function ($font) use ($base) {
                $font->file($base['font_url']);
                $font->size($base['text_size']);
                $font->align('center');
                $font->valign('middle');
            });
        }
        $border_top += $base['row_hight'] * 2;
        return $image;
    }

    /**
     * 生成代采定折扣信息
     * @param $image 画布对象
     * @param $base 定义的基础信息
     * @param $border_top 数据顶部高度
     * @param $cut_data 代采折扣信息
     * @param $title_name 第一列标题
     * @param $cut_name_str 第一列值
     * @param $y_diff 竖线修正值
     * @return mixed
     */
    public function addCutData($image, $base, &$border_top, $cut_data, $title_name, $cut_name_str, $y_diff, $per_row = 5)
    {
        $offset_arr = $base['border'];
        $border_top += $base['table_title_height'][1];
        $image->text($title_name, $offset_arr, $border_top, function ($font) use ($base) {
            $font->file($base['font_url']);
            $font->size($base['title_font_size']);
            $font->align('center');
            $font->valign('left');
        });
        $explain = $title_name == '基础折扣' ? '(' . $title_name . ' = ewx折扣)' :
            '(' . $title_name . ' = ewx折扣 + 折扣追加点)';
        $image->text($explain, $offset_arr + 80, $border_top, function ($font) use ($base) {
            $font->file($base['font_url']);
            $font->size(12);
            $font->align('center');
            $font->valign('left');
        });
        //画竖线
        $column_xy_arr = $base['column_x2_arr'];
        $image = $this->createYLine($image, $border_top, $base, $column_xy_arr, $y_diff);
        //画横线
        $total_row_num = $cut_data['row'];
        $tmp_line_height = $border_top;
        for ($i = 1; $i <= $total_row_num + 1; $i++) {//线条数=行数+1
            $tmp_str = $offset_arr;
            if (!in_array($i, [1, $total_row_num + 1])) {
                $tmp_str += $base['filed_data_width'][1];
            }
            $image->line($tmp_str, $tmp_line_height, $base['img_width'] - $base['border'],
                $tmp_line_height, function ($draw) {
                    $draw->color('#000');
                });
            $tmp_line_height += $base['row_hight'];
        }
        //绘制表格
        $this->createCutTableData($image, $border_top, $base, $cut_data, $cut_name_str, $per_row);
        $border_top += 15;
        return $image;
    }

    /**
     * 绘制代采图表
     * @param $image 画布对象
     * @param $border_top 数据顶部高度
     * @param $base 定义的基础信息
     * @param $cut_data 准现货预定折扣信息
     * @param $cut_name_str 第一列名称
     * @return mixed
     */
    public function createCutTableData($image, &$border_top, $base, $cut_data, $cut_name_str, $per_row)
    {
        $row_num = $cut_data['row'];
        //第一列标题
        $tmp_y_point = $border_top + $row_num * $base['row_hight'] / 2;
        $image->text($cut_name_str, $base['column_text_offset_arr'][0], $tmp_y_point, function ($font) use ($base) {
            $font->file($base['font_url']);
            $font->size($base['text_size']);
            $font->align('center');
            $font->valign('middle');
        });
        //写入内容
        $row_num = count($cut_data) - 1;
        $total_column_num = count($cut_data['channels_name']);
        $column_num = $base['table_column_num'][0] - 1;
        for ($i = 0; $i < $total_column_num; $i++) {
            $tmp_str = $base['filed_data_width'][1];
            $tmp_key = $i % $column_num + 1;
            $x_point = $base['column_x2_arr'][$tmp_key] - $base['row_text_offset_arr'][$tmp_key] + 10 + $tmp_str;
            if ($i > 0 && $i % $column_num == 0) {
                $border_top += $base['row_hight'] * $row_num;
            }
            //写入指定列
            if ($i % $column_num == 0) {
                $filter_arr = ['渠道', '折扣', '人民币', '美金'];
                if (isset($cut_data['freight'])) {
                    $add_arr = ['运费'];
                    array_splice($filter_arr, 2, 0, $add_arr);
                }
                $y_point = $border_top + 15;
                foreach ($filter_arr as $k => $v) {
                    $image->text($v, $x_point, $y_point, function ($font) use ($base) {
                        $font->file($base['font_url']);
                        $font->size($base['text_size']);
                        $font->align('center');
                        $font->valign('middle');
                    });
                    $y_point += $base['row_hight'];
                }
            }
            $x_point += $base['filed_data_width'][1];
            $y_point = $border_top + 15;
            $this->writeTableData($image, $x_point, $y_point, $cut_data, $i, $base);
        }
        $border_top += $base['row_hight'] * $per_row;
        return $image;
    }

    /**
     * 向表格写入数据
     * @param $image 图片对象
     * @param $x_point x轴坐标
     * @param $y_point y轴坐标
     * @param $cut_data 写入数据
     * @param $i 循环下标
     * @param $base 字体文件
     * @return mixed
     */
    public function writeTableData(&$image, $x_point, $y_point, $cut_data, $i, $base)
    {
        $title_arr = ['channels_name', 'discount', 'freight', 'cny_price', 'usd_price'];
        foreach ($title_arr as $k => $v) {
            if (!isset($cut_data[$v])) {
                continue;
            }
            $tmp_str = $cut_data[$v][$i];
            if ($v == 'channels_name') {
                $tmp_str = $i + 1;
            } elseif ($v == 'freight') {
                $image->circle(20, $x_point - 16, $y_point, function ($draw) {
                    $draw->border(1, '000000');
                });
                $tmp_str = '运 $' . $tmp_str;
            } elseif ($v == 'usd_price') {
                $tmp_str = '$' . $tmp_str;
            } elseif ($v == 'cny_price') {
                $tmp_str = '￥' . $tmp_str;
            }
            $image->text($tmp_str, $x_point, $y_point, function ($font) use ($base) {
                $font->file($base['font_url']);
                $font->size($base['text_size']);
                $font->align('center');
                $font->valign('middle');
            });
            $y_point += $base['row_hight'];
        }
        return $image;
    }

    /**
     * 画竖线
     * @param $image 画布对象
     * @param $border_top 数据顶部高度
     * @param $base 定义的基础信息
     * @return mixed
     */
    public function createYLine($image, &$border_top, $base, $column_xy_arr, $bottom_diff = 0)
    {
        $border_bottom = $base['img_height'] - $base['border'] - $bottom_diff;
        $border_top += $base['table_title_height'][1];
        foreach ($column_xy_arr as $k => $v) {
            $image->line($v, $border_top, $v, $border_bottom, function ($draw) {
                $draw->color('#000');
            });
        }
        return $image;
    }

    /**
     * 画横线
     * @param $image 画布对象
     * @param $border_top 数据顶部高度
     * @param $base 定义的基础信息
     * @return mixed
     */
    public function createXLine($image, &$border_top, $base, $row_num)
    {
        $offset_arr = $base['border'];
        $tmp_line_height = $border_top;
        for ($i = 1; $i <= $row_num + 1; $i++) {//线条数=行数+1
            $image->line($offset_arr, $tmp_line_height, $base['img_width'] - $base['border'], $tmp_line_height, function ($draw) {
                $draw->color('#000');
            });
            $tmp_line_height += $base['row_hight'];
        }
        return $image;
    }

    /**
     * 保存报价图片
     */
    public function createSalePicture_stop($save_path, $data, $user_name)
    {
        if (!file_exists($save_path)) {
            mkdir($save_path, 0777, true);
        }
        $imageManager = new Image();
        $fontPath = public_path('font/msyh.ttf');
        $width = 600;
        $high = 700;
        $image = $imageManager::canvas($width, $high, '#fff');
        //插入商品基础信息
        $menu_col_width = '30';
        $content_col_width = '110';
        $start_row = 40;
        $row_step = 25;
        $fontSize = 13;
        $column_info = [
            'goods_name' => '商品名称',
            'spec_sn' => '商品规格码',
            'erp_merchant_no' => '商家编码',
            'erp_prd_no' => '商品代码',
            'erp_ref_no' => '参考码',
            'spec_price' => '美金原价',
            'spec_weight' => '商品重量',
            'stock_info' => '库存',
            'channels_info' => '折扣信息',
        ];
        foreach ($column_info as $k => $v) {
            if (isset($data[$k])) {
                $image->text($v, $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                    $font->valign('left');
                });
                if ($k == 'channels_info') {
                    $image->text('渠道名称', $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                        $font->align('center');
                        $font->valign('left');
                    });
                } elseif ($k == 'stock_info') {
                    $stock_info = $data['stock_info'];
                    $tmp_content_col_width = $content_col_width + 40;
                    foreach ($stock_info as $k1 => $v1) {
                        $tmp_start_row = $start_row;
                        $image->text($v1['shop_name'], $tmp_content_col_width, $tmp_start_row, function ($font) use ($fontPath, $fontSize) {
                            $font->file($fontPath);
                            $font->size($fontSize);
                            $font->align('center');
                            $font->valign('center');
                        });
                        $tmp_start_row += $row_step;
                        $image->text($v1['stock'], $tmp_content_col_width, $tmp_start_row, function ($font) use ($fontPath, $fontSize) {
                            $font->file($fontPath);
                            $font->size($fontSize);
                            $font->align('center');
                            $font->valign('center');
                        });
                        $tmp_content_col_width += 100;
                    }
                    $start_row += $row_step * 2;
                } else {
                    $image->text($data[$k], $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                        $font->valign('left');
                    });
                    $start_row += $row_step;
                }
            }
        }
        //插入商品折扣报价信息
        $channel_col_width = $content_col_width + 180;
        $col_step = 60;
        if (isset($data['channels_info'])) {
            $channel_name_info = $data['channel_name_info'];
            foreach ($channel_name_info as $k => $v) {
                $image->text($v, $channel_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                    $font->align('center');
                    $font->valign('center');
                });
                $channel_col_width += $col_step;
            }
            $start_row += $row_step;
            $column_info = [
                'cost_discount' => '成本折扣',
                'brand_discount' => '最终折扣',
                'is_high' => '是否为高价',
                'cost_port_discount' => '成本折扣最终到港折扣率',
                'cost_port_price' => '成本折扣最终到港美金',
                'cost_port_ren_price' => '成本折扣最终到港人民币',
                'final_port_discount' => '最终折扣最终到港折扣率',
                'final_port_price' => '最终折扣最终到港美金',
                'final_port_ren_price' => '最终折扣最终到港人民币',
                'sale_port_discount' => '报价折扣最终到港折扣率',
                'sale_port_price' => '报价折扣最终到港美金',
                'sale_port_ren_price' => '报价折扣最终到港人民币',
                'margin_port_discount' => '最终到港毛利率',
                'margin_port_price' => '毛利率最终到港美金',
                'margin_port_ren_price' => '毛利率最终到港人民币',
            ];
            $channels_info = $data['channels_info'];
            foreach ($column_info as $k => $v) {
                $image->text($v, $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                    $font->align('center');
                    $font->valign('left');
                });
                $dis_col_width = $content_col_width + 180;
                $col_step = 60;
                foreach ($channels_info as $k1 => $v1) {
                    $image->text($v1[$k], $dis_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                        $font->file($fontPath);
                        $font->size($fontSize);
                        $font->align('center');
                        $font->valign('center');
                    });
                    $dis_col_width += $col_step;
                }
                $start_row += $row_step;
            }
        }
        //增加水印
        $text = $user_name . '海带网';
        for ($i = 50; $i <= $width;) {
            for ($j = 10; $j <= $high;) {
                $image->text($text, $i, $j, function ($font) use ($fontPath, $fontSize) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                    $font->align('center');
                    $font->valign('center');
                    $font->color(array(0, 0, 0, 0.2));
                });
                $j = $j + 50;
            }
            $i = $i + 150;
        }
        //插入底部图片
        $haidai = Image::make('./image/haidai.png')->widen(500, function ($constraint) {
            $constraint->upsize();
        });
        $image->insert($haidai, 'bottom-center', 100, -100);
        //保存图片
        $spec_sn = $data['spec_sn'];
        $save_path .= '/' . $spec_sn . '.jpg';
        $image->save($save_path);
        $path = substr($save_path, 1);
        return $path;
    }

    /**
     * description:获取商品美金原价数据
     * author:zongxing
     * date:2020.02.24
     * type:GET
     * return Json
     */
    public function ltGoodsSpecPriceInfo(Request $request)
    {
        $param_info = $request->toArray();
        $rules = [
            'erp_prd_no' => 'required',
        ];
        $messages = [
            'erp_prd_no.required' => '商品代码不能为空'
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }

        //获取商品美金原价记录信息
        $goods_model = new GoodsModel();
        $goods_detail_info = $goods_model->ltGoodsSpecPriceInfo($param_info);
        if (empty($goods_detail_info)) {
            return response()->json(['code' => '1003', 'msg' => '商品代码错误']);
        }
        $goods_info = $date_arr = $spec_price_arr = [];
        foreach ($goods_detail_info as $k => $v) {
            if (empty($goods_info)) {
                $goods_info = [
                    'goods_name' => $v['goods_name'],
                    'lt_prd_no' => $v['lt_prd_no'],
                    'erp_prd_no' => $v['erp_prd_no']
                ];
            }
            $date_arr[] = $v['download_date'];
            $spec_price_arr[] = $v['spec_price'];
        }
        $return_info = [
            'goods_info' => $goods_info,
            'date_arr' => $date_arr,
            'spec_price_arr' => $spec_price_arr,
        ];
        $return_info = ['code' => '1000', 'msg' => '获取乐天商品美金原价信息成功', 'data' => $return_info];
        return response()->json($return_info);
    }


}//end of class
