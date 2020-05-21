<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WholesaleDiscountModel extends Model
{
    public $table = 'wholesale_discount as wd';
    private $field = [
        'wd.id','wd.wholesale_sn','wd.spec_sn','wd.channel_id','wd.cat_code',
        'wd.discount','wd.create_time',
    ];

    public $inputNum;

    //折扣档位描述对照
    public $descDiscPlate = [
        '1' => 'base',//基础折扣
        '2' => 'waitBuy_zpf',//代采折扣0.5档
        '3' => 'waitBuy_o',//代采折扣1档
        '4' => 'inclusive_opf',//全包折扣1.5档
        '5' => 'inclusive_t',//全包折扣2档
        '6' => 'other',//代采其他折扣
    ];
    //折扣档位整形对照
    public $intDiscPlate = [
        'base'=> '1',
        'waitBuy_zpf'=> '2',
        'waitBuy_o'=> '3',
        'inclusive_opf'=> '4',
        'inclusive_t'=> '5',
        'other'=> '6',
    ];
    //折扣档位汉字描述对照
    public $chDiscPlate = [
        ['plate_id' => 1, 'plate_name' => '基础折扣'],
        ['plate_id' => 2, 'plate_name' => '代采折扣_0.5档'],
        ['plate_id' => 3, 'plate_name' => '代采折扣_1档'],
        ['plate_id' => 4, 'plate_name' => '全包折扣_1.5档'],
        ['plate_id' => 5, 'plate_name' => '全包折扣_2档'],
        ['plate_id' => 6, 'plate_name' => '代采其他折扣'],
    ];

    //折扣档位追加点对照,其中为其他折扣，追加点要输入 zhangdong 2020.03.30
    public $appendDiscPlate = [
        '1' => 0,//基础折扣
        '2' => 0.5,//代采折扣0.5档
        '3' => 1,//代采折扣1档
        '4' => 1.5,//全包折扣1.5档
        '5' => 2,//全包折扣2档
    ];

    //折扣版块汉字描述对照 zhangdong 2020.03.30
    public $strDiscPlate = [
        '1' => '基础折扣',//基础折扣
        '2' => '代采折扣',//代采折扣0.5档
        '3' => '代采折扣',//代采折扣1档
        '4' => '全包折扣',//全包折扣1.5档
        '5' => '全包折扣',//全包折扣2档
        '6' => '代采其他折扣',//代采其他折扣
    ];

    //大批发报价导出表头 zhangdong 2020.05.12
    public $enExportTitle = [
        '1' => 'cost',//成本折扣
        '2' => 'best',//最优渠道折扣
        '3' => 'waitBuy',//代采报价
    ];

    public $orderMsg = [];
    public $skuMsg = [];

    /**
     * desc 统计对应报价单的折扣信息条数
     * author zhangdong
     * date 2020.03.26
     */
    public function countDiscount($wholesaleSn)
    {
        $where = [
            ['wholesale_sn', $wholesaleSn],
        ];
        $countNum = DB::table($this->table)->where($where)->count();
        return $countNum;
    }

    /**
     * desc 组装渠道的成本折扣数据
     * author zhangdong
     * date 2020.03.26
     */
    public function makeSaveData($wholesaleSn, $discount)
    {
        //获取折扣种类信息
        $discTypeInfo = (new ClassifyFieldModel())->getCatByAid();
        $arrDiscType = getFieldArrayVaule(objectToArray($discTypeInfo), 'field_name_en');
        $saveData = [];
        foreach ($discount as $key => $value) {
            $specSn = $value['spec_sn'];
            if (!isset($value['channels_info']) || count($value['channels_info']) == 0) {
                continue;
            }
            foreach($value['channels_info'] as $k => $item){
                //组装折扣信息
                foreach ($item as $kName => $disc) {
                    if (!in_array($kName, $arrDiscType)) {
                        continue;
                    }
                    $saveData[] = [
                        'wholesale_sn'=>$wholesaleSn,
                        'spec_sn'=>$specSn,
                        'channel_id'=>$item['channels_id'],
                        'discount'=>$disc,
                        'cat_code'=>$kName,
                    ];
                }//end foreach
            }//end foreach
        }//end foreach
        return $saveData;
    }



    /**
     * desc 根据所选折扣板块处理商品折扣数据
     * author zhangdong
     * date 2020.03.28
     */
    public function opeDiscByPlate($discount, $discPlate)
    {
        if (count($discount) == 0) {
            return [];
        }
        $goodsDis = [];
        foreach ($discount as $e) {
            unset($e['id'],$e['wholesale_sn'],$e['create_time'],$e['spec_sn']);
            foreach ($discPlate as $plateNum) {
                //根据折扣模板计算对应折扣
                $goodsDis = $this->calculDisc($goodsDis, $plateNum, $e);
            }
        }
        return $goodsDis;
    }

    /**
     * desc 根据折扣模板计算对应折扣
     * author zhangdong
     * params $plateNum 折扣板块编号
     *        $skuMsg 信息 spec_weight,spec_price
     * date 2020.03.28
     */
    private function calculDisc($goodsDis, $plateNum, $discount)
    {
        $sku = $this->skuMsg;
        $order = $this->orderMsg;
        $plateName = $this->descDiscPlate[$plateNum];
        $channelId = $discount['channel_id'];
        //追加折扣 = 折扣追加点/100
        if($plateNum == $this->intDiscPlate['other']){
            $appendDisc = $this->inputNum/100;
        } else {
            $appendDisc = $this->appendDiscPlate[$plateNum]/100;
        }
        //最终折扣 = 渠道成本折扣 + 追加折扣
        $plateDisc = $discount['discount'] + $appendDisc;
        $specPrice = floatval($sku->spec_price);
        $skuFreight = floatval($sku->skuFreight);
        $usdCnyRate = floatval($order->usd_cny_rate);
        $discount['lastDisc'] = round($plateDisc, 3);
        //美金价格 = 最终折扣 * 美金原价
        $dollarPrice = $plateDisc * $specPrice;
        if ($plateName != 'base') {
            //最终美金价格 = 美金原价 + 商品运费
            $lastPrice = $dollarPrice + $skuFreight;
            $discount['dollarPrice'] = round($lastPrice, 2);
            //人民币价格 = 最终美金价格 * 美金对人民币汇率
            $discount['rmbPrice'] = round($lastPrice * $usdCnyRate, 2);
        } else {
            //基础折扣信息的计算-不加商品运费
            //最终美金价格 = 美金价格
            $discount['dollarPrice'] = round($dollarPrice, 2);
            //人民币价格 = 美金价格 * 美金对人民币汇率
            $discount['rmbPrice'] = round($dollarPrice * $usdCnyRate, 2);
        }
        $goodsDis[$plateName][$channelId] = $discount;
        return $goodsDis;
    }

    /**
     * desc 通过单号获取渠道信息
     * author zhangdong
     * date 2020.03.31
     */
    public function getChannelBySn($wholesaleSn)
    {
        $where = [
            ['wd.wholesale_sn', $wholesaleSn],
        ];
        $fields = ['pc.channels_name', 'wd.channel_id'];
        $queryRes = DB::table($this->table)->select($fields)
            ->leftJoin('purchase_channels as pc', 'pc.id', 'wd.channel_id')
            ->where($where)->groupBy('wd.channel_id')->get();
        return $queryRes;
    }

    /**
     * desc 通过报价单号获取商品信息和折扣信息
     * author zhangdong
     * date 2020.03.27
     */
    public function getGoodsDiscount($wholesaleSn, array $channel_id = [])
    {
        $where = [
            ['wholesale_sn', $wholesaleSn],
        ];
        array_push($this->field,'pc.channels_name');
        $queryRes = DB::table($this->table)->select($this->field)
            ->leftJoin('purchase_channels AS pc', 'pc.id', 'wd.channel_id')
            ->where($where)
            ->when(count($channel_id) > 0, function ($query) use ($channel_id){
                return $query->whereIn('wd.channel_id', $channel_id);
            })->get();
        return $queryRes;
    }

    /**
     * desc 根据所选折扣板块处理商品折扣数据
     * author zhangdong
     * date 2020.03.28
     */
    public function operateDisc($arrDisc, $arrChannelId, $order, $goods)
    {
        if (count($arrDisc) == 0) {
            return [];
        }
        $exportTitle = $this->enExportTitle;
        $chaDisc = $arrFinalDisc = [];
        //组装最优渠道折扣数据
        foreach ($arrDisc as $disc) {
            $catCode = $disc['cat_code'];
            if ($catCode == 'final_discount') {
                $arrFinalDisc[] = [
                    'final_discount' => $disc['discount'],
                    'channel_id' => $disc['channel_id'],
                    'channel_name' => $disc['channels_name'],
                ];
            }
        }
        if (count($arrFinalDisc) == 0) {
            $arrFinalDisc[] = [
                'final_discount' => 0,
                'channel_id' => '',
                'channel_name' => $disc['channels_name'],
            ];
        }
        //对最终折扣数据排序，筛选出最优折扣
        $arrBestDisc = sortTwoArray($arrFinalDisc, 'SORT_ASC', 'final_discount');
        //参考EXW折扣手动输入值优先，如果没有输入则默认为最优折扣
        $referDisc = floatval($goods->refer_disc);
        $referExw = $referDisc > 0 ? floatval($referDisc) : floatval($arrBestDisc[0]['final_discount']);
        $arrBestDisc[0]['referExw'] = $referExw;
        //最优渠道折扣
        $chaDisc[$exportTitle[2]] = $arrBestDisc[0];
        //组装代采报价数据
        //报价折扣 = 参考EXW折扣 + 代采加点
        $userDisc = floatval($order->user_disc);
        $referExw = floatval($arrBestDisc[0]['referExw']);
        $offerDisc = $referExw + $userDisc;
        //销售美金 = 报价折扣 * 美金原价 + SKU运费
        $saleDollar = $offerDisc * $goods->spec_price + $goods->skuFreight;
        //参考人民币价 = 销售美金 * 汇率
        $usdCnyRate = floatval($order->usd_cny_rate);
        $referRmb = round($saleDollar * $usdCnyRate, 2);
        $chaDisc[$exportTitle[3]] = [
            'offerDisc' => $offerDisc,
            'saleDollar' => round($saleDollar, 2),
            'referRmb' => $referRmb,
        ];
        //组装成本折扣数据
        foreach($arrChannelId as $chaId) {
            foreach ($arrDisc as $key => $disc) {
                unset($disc['id'],$disc['wholesale_sn'],$disc['create_time'],$disc['spec_sn']);
                $channelId = $disc['channel_id'];
                $catCode = $disc['cat_code'];
                $discount = $disc['discount'];
                //组装成本折扣数据
                if($chaId == $channelId){
                    $chaDisc[$exportTitle[1]][$chaId][$catCode] = $discount;
                    unset($arrDisc[$key]);
                }
            }//end foreach
        }//end foreach
        return $chaDisc;
    }

    /**
     * desc 查询成本折扣数据标题头
     * author zhangdong
     * date 2020.05.13
     */
    public function queryCostTitle($wholesaleSn)
    {
        $fields = [
            'pc.channels_name','wd.channel_id','wd.cat_code','f.field_name_cn',
        ];
        $adminId = 2;
        $where = [
            ['wd.wholesale_sn', $wholesaleSn],
            ['cf.classify_id', $adminId],
        ];
        $queryRes = DB::table($this->table)->select($fields)
            ->leftJoin('field as f', 'wd.cat_code', 'f.field_name_en')
            ->leftJoin('purchase_channels AS pc', 'pc.id', 'wd.channel_id')
            ->leftJoin('classify_field AS cf', 'cf.field_id', 'f.id')
            ->where($where)->groupBy('wd.channel_id')->groupBy('wd.cat_code')
            ->orderBy('wd.channel_id', 'asc')->orderBy('cf.sort_num', 'asc')->get();
        return $queryRes;
    }

    /**
     * desc 将标题头美化
     * author zhangdong
     * date 2020.05.14
     */
    public function beautCostTitle($costTitle)
    {
        $title = [];
        $arrData = objectToArray($costTitle);
        $channelId = array_unique(array_column($arrData, 'channel_id'));
        foreach ($channelId as $chaId) {
            $search = searchArray($arrData,$chaId,'channel_id');
            $title[$chaId] = $search['searchRes'];
            $arrData = $search['arrData'];
        }
        return $title;
    }



}//end of class
