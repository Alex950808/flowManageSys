<?php
//created by zhangdong on the 2019.11.01
namespace App\Modules\Excel;

use App\Model\Vone\WholesaleDiscountModel;
use App\Modules\ParamsSet;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Facades\Excel;

use App\Model\Vone\PurchaseChannelModel;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

trait ExcelMakeData
{

    //购物车报价导出信息
    private $cartOfferInfo = [
        '分类','搭配比例','品牌','名称','商品代码','货号','美金原价','个数','美金总价',
        '4档基础折扣率','追加后折扣率','港币','裸重(KG)','总重量(KG)','人民币汇率','单价',
    ];

    //大批发报价导出商品基本信息 zhangdong 2020.03.30
    private $comWholeOfferInfo = [
        '商品名称','规格码','平台条码','重量(KG)','美金原价','运费',
    ];

    //大批发报价导出商品基本信息-新版 zhangdong 2020.05.13
    private $comWholeTitle = [
        '商品名称','规格码','平台条码','重量(KG)','美金原价','运输方式','运费',
        '渠道','最优折扣','参考EXW折扣','报价折扣','销售美金','参考人民币',
    ];

    /**
     * description 测试函数
     * author zhangdong
     * date 2019.12.03
     */
    private function traitTest()
    {
        dd(123);
    }

    /**
     * description 特价SKU-计算报价数据
     * author zhangdong
     * date 2019.11.01
     */
    private function makeOfferRes($exportType, $offerData, $reqParams)
    {
        $rmbRate = floatval($reqParams['rmbRate']);
        $koreanRate = floatval($reqParams['koreanRate']);
        $rmbKoreanRate = floatval($reqParams['rmbKoreanRate']);
        //每日最终折扣
        $lastDiscount = $offerData->lastDiscountDiff > 0 ? $offerData->lastDiscountDiff : 0;
        $specWeight = floatval($offerData->spec_weight);
        $specPrice = floatval($offerData->spec_price);
        //运费
        $shipFee = $specWeight * SHIP_COST;
        $dollarPrice = $lastDiscount * $specPrice;
        //人民币付款核算后美金汇率
        $dollarRate = $koreanRate/$rmbKoreanRate;
        $offerRes['specWeight'] = $specWeight;
        $offerRes['shipFee'] = $shipFee;
        $offerRes['rmbRate'] = $rmbRate;
        $offerRes['koreanRate'] = $koreanRate;
        $offerRes['rmbKoreanRate'] = $rmbKoreanRate;
        $offerRes['dollarRate'] = $dollarRate;
        $offerRes['specWeight'] = $specWeight;
        $offerRes['specPrice'] = $specPrice;
        //注：此处仅EMS的加运费，机场的不加，所以将这个地方针对性地处理
        if (in_array($exportType,['EMS_DOLLAR','EMS_RMB'])) {
            //4档最终到港美金价 = 每日最终折扣*美金原价 + 运费
            $lastDollar = $lastDiscount > 0 && $specWeight > 0 ? $dollarPrice + $shipFee : 'NULL';
            $offerRes['lastDollar'] = $lastDollar;
            //4档最终到港折扣率
            $portCondition = $specPrice > 0 && $lastDollar > 0;
            $offerRes['portDiscount'] = $portCondition ? $lastDollar/$specPrice : 'NULL';
            //人民币货值
            $offerRes['rmbPrice'] = $lastDollar > 0 ? $lastDollar * $rmbRate : 'NULL';
            //4档最终到港人民币价
            $offerRes['portRmb'] = $lastDollar > 0 ? $lastDollar * $dollarRate : 'NULL';
        }
        if (in_array($exportType,['AIRPORT_DOLLAR','AIRPORT_RMB'])) {
            //4档最终机场交货折扣率 = 每日最终折扣*美金原价/美金原价 = 每日最终折扣
            $offerRes['portDiscount'] = $specPrice > 0 && $dollarPrice > 0 ? $dollarPrice/$specPrice : 'NULL';
            //4档最终机场交货美金价
            $offerRes['airPortDollar'] = $dollarPrice > 0 ? $dollarPrice : 'NULL';
            //4档最终机场交货人民币价
            $offerRes['airPortRmb'] = $dollarPrice > 0 ? $dollarPrice * $dollarRate : 'NULL';
            //人民币货值
            $offerRes['rmbValue'] = $dollarPrice * $rmbRate;
        }
        return $offerRes;

    }//end of makeOfferRes


    /**
     * description 商品最终报价导出-根据不同导出文件组装不同信息
     * author zhangdong
     * date 2019.10.23
     */
    private function makeSpecialData($exportType, $reqParams, $offerData)
    {
        //出境日
        $exitDate = date('m月d日',strtotime($reqParams['exitDate']));
        //预计到港日
        $predictDate = date('m月d日',strtotime($reqParams['predictDate']));
        $offerRes = self::makeOfferRes($exportType, $offerData, $reqParams);
        switch ($exportType) {
            //特价活动报价（乐天4档EMS）美金
            case 'EMS_DOLLAR':
                $specialData = [
                    $offerRes['specWeight'],$offerRes['shipFee'],$offerRes['portDiscount'],
                    $offerRes['lastDollar'],'','','',$offerRes['rmbRate'],$offerRes['rmbPrice'],
                    $exitDate,$predictDate,''
                ];
                break;
            //特价活动报价（乐天4档EMS）人民币
            case 'EMS_RMB':
                $specialData = [
                    $offerRes['koreanRate'],$offerRes['rmbKoreanRate'],$offerRes['dollarRate'],
                    $offerRes['specWeight'],$offerRes['shipFee'],
                    $offerRes['portDiscount'],$offerRes['portRmb'],'','','',
                    $exitDate,$predictDate,''
                ];
                break;
            //特价活动报价（乐天4档机场交货）美金
            case 'AIRPORT_DOLLAR':
                $specialData = [
                    $offerRes['portDiscount'],$offerRes['airPortDollar'],'','','',
                    $offerRes['rmbRate'],$offerRes['rmbValue'],$exitDate,$predictDate
                ];
                break;
            //特价活动报价（乐天4档机场交货）人民币
            case 'AIRPORT_RMB':
                $specialData = [
                    $offerRes['koreanRate'],$offerRes['rmbKoreanRate'],$offerRes['dollarRate'],
                    $offerRes['portDiscount'],$offerRes['airPortRmb'],'','','',$exitDate,
                    $predictDate
                ];
                break;
            default:
                $specialData = [];
        }
        return $specialData;

    }

    /**
     * description 商品最终报价导出-根据导出文件类型选定导出标题头
     * author zhangdong
     * date 2019.10.22
     */
    protected function getOfferTitle($exportType, $reqParams)
    {
        //追加点
        $append = floatval($reqParams['append']);
        //公用信息
        $commonTitle = [
            '品牌','乐天商品代码','乐天参考代码','商品条码','商品名称','规格','商品分类','美金原价','乐天4档标准折扣率',
            '乐天高价SKU差率','乐天特价SKU','每日最终折扣率',
        ];
        switch ($exportType) {
            //特价活动报价（乐天4档EMS）美金
            case 'EMS_DOLLAR':
                $title = [
                    '产品单重(kg)','产品运费','4档最终到港折扣率','4档最终到港美金价',
                    '客户申请数量','实际审批数量','合计美金','乐天官网美金汇率','人民币货值',
                    '出境日','预计到港日','EMS运费/每KG',
                ];
                $fileName = $append == 0 ?
                    '特价活动报价(乐天4档EMS)美金' :
                    '特价活动报价(乐天4.5档EMS)美金';
                $template = 'ems_dollar';
                break;
            //特价活动报价（乐天4档EMS）人民币
            case 'EMS_RMB':
                $title = [
                    '乐天官网美金/韩币汇率','人民币兑韩币汇率','人民币付款核算后美金汇率',
                    '产品单重(kg)','产品运费','4档最终到港折扣率','4档最终到港人民币价',
                    '客户申请数量','实际审批数量','合计人民币','出境日','预计到港日',
                    'EMS运费/每KG',
                ];
                $fileName = $append == 0 ?
                    '特价活动报价(乐天4档EMS)人民币' :
                    '特价活动报价(乐天4.5档EMS)人民币';
                $template = 'ems_rmb';
                break;
            //特价活动报价（乐天4档机场交货）美金
            case 'AIRPORT_DOLLAR':
                $title = [
                    '4档最终机场交货折扣率','4档最终机场交货美金价','客户申请数量',
                    '实际审批数量','合计美金','乐天官网美金汇率','人民币货值','出境日',
                    '预计到港日',
                ];
                $fileName = $append == 0 ?
                    '特价活动报价(乐天4档机场交货)美金' :
                    '特价活动报价(乐天4.5档机场交货)美金';
                $template = 'airport_dollar';
                break;
            //特价活动报价（乐天4档机场交货）人民币
            case 'AIRPORT_RMB':
                $title = [
                    '乐天官网美金/韩币汇率','人民币兑韩币汇率','人民币付款核算后美金汇率',
                    '4档最终机场交货折扣率','4档最终机场交货人民币价','客户申请数量',
                    '实际审批数量','合计人民币','出境日','预计到港日',
                ];

                $fileName = $append == 0 ?
                    '特价活动报价(乐天4档机场交货)人民币' :
                    '特价活动报价(乐天4.5档机场交货)人民币';
                $template = 'airport_rmb';
                break;
            default:
                $title = [];
                $fileName = '商品报价公共信息';
                $template = 'common';
        }
        $returnMsg = [
            'fileName' => $fileName,
            'template' => $template,
            'title' => array_merge($commonTitle,$title),
        ];
        return $returnMsg;
    }

    /**
     * description 商品最终报价导出-根据导出文件类型计算导出数据
     * author zhangdong
     * date 2019.10.22
     */
    protected function getOfferData($exportType, $arrOfferData, $reqParams)
    {
        $lastOfferData = [];
        //追加点
        $append = floatval($reqParams['append']);
        foreach ($arrOfferData as $key => $value) {
            $cod = self::commonOfferData($value, $append);
            $commonData = [
                trim($value->brand_name),trim($value->erp_prd_no),trim($value->erp_ref_no),
                trim($value->erp_merchant_no),$cod['goodsName'],' ',trim($value->cat_name),
                floatval($value->spec_price),$cod['standard_discount'],$cod['diff_discount'],
                $cod['special_discount'],$cod['lastDiscount']
            ];
            //根据不同导出文件组装不同信息
            $specialData = $this->makeSpecialData($exportType, $reqParams, $value);
            //将共有信息和特有信息合并然后将最后处理好的报价数据放入最终数组中
            $lastOfferData[] = array_merge($commonData,$specialData);
        }
        return $lastOfferData;
    }

    /**
     * description 组装导出模板数据
     * author zhangdong
     * date 2019.11.01
     */
    protected function makeExportData($title, $offerData)
    {
        //总条数
        $titleNum = count($title);
        $exportParams = [];
        foreach ($offerData as $key => $value) {
            for ($i=0; $i < $titleNum; $i++) {
                $exportParams["[$title[$i]]"][] = $value[$i];
            }
        }
        return $exportParams;
    }

    /**
     * description 计算报价的公共信息，涉及报价：购物车报价，特价SKU报价
     * notice 由于这份数据涉及两个报价类别，故从特价报价中分出
     * author zhangdong
     * params $append 该参数是折扣偏移量，比如系统中只有4档折扣，而业务需要个4.5档的，此时
     * 该参数则不为0
     * date 2019.11.28
     */
    protected function commonOfferData($skuMsg, $append = 0)
    {
        $goodsName = strlen($skuMsg->ext_name) > 0 ? trim($skuMsg->ext_name) : trim($skuMsg->goods_name);
        //品牌成本折扣
        $brandDis = $skuMsg->dt_discount > 0 ? $skuMsg->dt_discount : 0;
        //高价sku返点
        $highDis = $skuMsg->gd_discount > 0 ? $skuMsg->gd_discount : 0;
        $append_discount = $skuMsg->appendDiscount;
        //乐天4档标准折扣率(特价报价)/4档基础折扣率(购物车报价)
        $standard_discount = $brandDis > 0 ? $brandDis + $append : '';
        //乐天高价SKU差率
        $diff_discount = '';
        if($highDis > 0 && $brandDis > 0){
            //高价折扣 1 - $highDis，档位折扣 $brandDis + $append
            $diff_discount = 1 - $highDis - ($brandDis + $append);
        }
        //乐天特价SKU
        $special_discount = $append_discount > 0 ? $append_discount : '';
        //成本折扣:如果有高价sku折扣则用该折扣减去追加返点，否则用品牌成本折扣减去追加返点
        $costDiscount = $highDis > 0 ? 1-$highDis : $brandDis + $append;
        //每日最终折扣率(特价报价)/追加后折扣率(购物车报价) = 成本折扣-追加折扣
        //追加折扣=品牌追加折扣(新罗和爱宝客有)+月度低价SKU折扣(只有新罗有)+日低价SKU折扣(只有乐天有)
        $lastDiscountDiff = $costDiscount - $append_discount;
        //计算其他信息时用
        $skuMsg->lastDiscountDiff = $lastDiscountDiff;
        $lastDiscount = $lastDiscountDiff > 0 ? $lastDiscountDiff : '';
        $commonData = [
            'goodsName' => $goodsName,
            'standard_discount' => $standard_discount,
            'diff_discount' => $diff_discount,
            'special_discount' => $special_discount,
            'lastDiscount' => $lastDiscount,
        ];
        return $commonData;
    }


    /**
     * description 数据处理
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    protected function makeData($data, $code)
    {
        switch ($code) {
            //购物车报价数据
            case 'cartOffer':
                $data = self::makeCartOffer($data);
                break;
            //大批发报价数据 2020.03.30
            case 'wholesaleOffer':
                $data = self::makeWholesaleOffer($data);
                break;
            //大批发报价数据-新版 2020.05.13
            case 'wholeOffer':
                $data = self::makeWholeOffer($data);
                break;
            default:
                self::defaultData($data);
                break;
        }
        return $data;
    }

    /**
     * description 组装购物车报价数据
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    private function makeCartOffer($data)
    {
        $title[] = $this->cartOfferInfo;
        $goods_list = [];
        $koreanRate = ParamsSet::getKoreanRate();
        $rmbKoreanRate = ParamsSet::getRmbKoreanRate();
        foreach ($data as $key => $value) {
            $cod = self::commonOfferData($value);
            //人民币付款核算后美金汇率 = 乐天官网美金/韩币汇率/人民币兑换韩币汇率
            $rmbPaidRate = $koreanRate/$rmbKoreanRate;
            $goods_list[] = [
                $value->cat_name, $value->match_scale, $value->brand_name,$cod['goodsName'],
                $value->erp_prd_no, $value->erp_ref_no, $value->spec_price,'','',
                $cod['standard_discount'],$cod['lastDiscount'],'','','',$rmbPaidRate,''
            ];
        }
        $data = array_merge($title, $goods_list);
        return $data;
    }

    /**
     * description 默认数据
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    private function defaultData($data)
    {
        return $data;
    }

    /**
     * description 组装大批发报价数据
     * author zhangdong
     * date 2020.03.30
     */
    private function makeWholesaleOffer($data)
    {
        $order = $data['order'];
        $firstRow[] = [
            '预计到港时间', $order->estimate_time,'运费标准', $order->freight . '美元/千克',
            '运输方式', $order->transportDesc,
        ];
        //组装不同折扣板块的不同表头
        $title = $this->makeWholeTitle($data['channelId'], $data['disPlate'], $data['inputNum']);
        //根据表头组装导出数据
        $goods_list = $this->makeWholeGoods($data);
        $data = array_merge($firstRow,$title, $goods_list);
        return $data;
    }

    /**
     * description 组装大批发报价数据-新版
     * author zhangdong
     * date 2020.05.13
     */
    private function makeWholeOffer($data)
    {
        $order = $data['order'];
        $firstRow[] = [
            '预计到港时间', $order->estimate_time,'海运', '$'.$order->sea_trans . '/KG',
            '空运', '$'.$order->air_trans . '/KG', '代采加点',$order->user_disc,
        ];
        //表头组装
        $title = $this->makeWholesaleTitle($data);
        //根据表头组装导出数据
        $goods_list = $this->makeWholesaleGoods($data);
        $data = array_merge($firstRow,$title, $goods_list);
        return $data;
    }

    /**
     * description 组装不同折扣板块的不同表头
     * author zhangdong
     * date 2020.03.30
     */
    private function makeWholeTitle($arrChannelId, $arrDiscPlate, $inputNum)
    {
        $arrChannelName = (new PurchaseChannelModel())->arrChannelName;
        $wdModel = new WholesaleDiscountModel();
        $strDiscPlate = $wdModel->strDiscPlate;
        $appendDisc = $wdModel->appendDiscPlate;
        $comTitle = $this->comWholeOfferInfo;
        $title[1] = array_merge($comTitle,['折扣类型']);
        $title[2] = array_merge($comTitle,['追加点数']);
        $title[3] = array_merge($comTitle,['渠道名称']);
        $base = $waitBuy_zpf = $waitBuy_o = $inclusive_opf = $inclusive_t = $other = [];
        //表头组装
        foreach ($arrChannelId as $item) {
            foreach ($arrDiscPlate as $value) {
                $plateName = $strDiscPlate[$value];
                if (isset($appendDisc[$value])) {
                    $appendPoint = $appendDisc[$value] . '%';
                } else {
                    $appendPoint = $inputNum . '%';
                }
                $channelName = $arrChannelName[$item];
                if ($value == 1) {
                    $base[1][] = $plateName;
                    $base[2][] = $appendPoint;
                    $base[3][] = $channelName;
                }
                if ($value == 2) {
                    $waitBuy_zpf[1][] = $plateName;
                    $waitBuy_zpf[2][] = $appendPoint;
                    $waitBuy_zpf[3][] = $channelName;
                }
                if ($value == 3) {
                    $waitBuy_o[1][] = $plateName;
                    $waitBuy_o[2][] = $appendPoint;
                    $waitBuy_o[3][] = $channelName;
                }
                if ($value == 4) {
                    $inclusive_opf[1][] = $plateName;
                    $inclusive_opf[2][] = $appendPoint;
                    $inclusive_opf[3][] = $channelName;
                }
                if ($value == 5) {
                    $inclusive_t[1][] = $plateName;
                    $inclusive_t[2][] = $appendPoint;
                    $inclusive_t[3][] = $channelName;
                }
                if ($value == 6) {
                    $other[1][] = $plateName;
                    $other[2][] = $appendPoint;
                    $other[3][] = $channelName;
                }
            }
        }
        //处理空指针问题
        if (count($base) == 0) {
            $base[1] = $base[2] = $base[3] = [];
        }
        if (count($waitBuy_zpf) == 0) {
            $waitBuy_zpf[1] = $waitBuy_zpf[2] = $waitBuy_zpf[3] = [];
        }
        if (count($waitBuy_o) == 0) {
            $waitBuy_o[1] = $waitBuy_o[2] = $waitBuy_o[3] = [];
        }
        if (count($inclusive_opf) == 0) {
            $inclusive_opf[1] = $inclusive_opf[2] = $inclusive_opf[3] = [];
        }
        if (count($inclusive_t) == 0) {
            $inclusive_t[1] = $inclusive_t[2] = $inclusive_t[3] = [];
        }
        if (count($other) == 0) {
            $other[1] = $other[2] = $other[3] = [];
        }
        //表头合并
        $title[1] = array_merge(
            $title[1],$base[1],$waitBuy_zpf[1],$waitBuy_o[1],
            $inclusive_opf[1],$inclusive_t[1],$other[1]
        );
        $title[2] = array_merge(
            $title[2],$base[2],$waitBuy_zpf[2],$waitBuy_o[2],
            $inclusive_opf[2],$inclusive_t[2],$other[2]
        );
        $title[3] = array_merge(
            $title[3],$base[3],$waitBuy_zpf[3],$waitBuy_o[3],
            $inclusive_opf[3],$inclusive_t[3],$other[3]
        );
        return $title;
    }

    /**
     * description 组装不同折扣板块的不同表头-新版
     * author zhangdong
     * date 2020.05.13
     */
    private function makeWholesaleTitle($data)
    {
        $comTitle = $this->comWholeTitle;
        $title[1] = [
            '基础信息','基础信息','基础信息','基础信息','基础信息','基础信息',
            '基础信息','最优渠道折扣','最优渠道折扣','最优渠道折扣','代采报价',
            '代采报价','代采报价',
        ];
        $title[2] = [
            '基础信息','基础信息','基础信息','基础信息','基础信息','基础信息',
            '基础信息','最优渠道折扣','最优渠道折扣','最优渠道折扣','代采报价',
            '代采报价','代采报价',
        ];
        $title[3] = $comTitle;
        $costTitle = $data['costTitle'];
        foreach ($costTitle as $value) {
            $title[1][] = '成本折扣';
            $title[2][] = $value->channels_name;
            $title[3][] = $value->field_name_cn;
        }
        return $title;
    }

    /**
     * description 根据表头组装导出数据
     * author zhangdong
     * date 2020.03.31
     */
    private function makeWholeGoods($data)
    {
        $repeatNum = [
            'lastDisc' => '折扣',
            'rmbPrice' => '人民币价格',
            'dollarPrice' => '美金价格',
        ];
        $goods_list = [];
        foreach ($data['goodsInfo'] as $key => $value) {
            $goodsInfo = [
                $value->goods_name, $value->spec_sn, "'".$value->platform_no, $value->spec_weight,
                $value->spec_price, $value->skuFreight,
            ];
            foreach ($repeatNum as $keyName => $cellName) {
                $base = $waitBuy_zpf = $waitBuy_o = $inclusive_opf =
                $inclusive_t = $other = [];
                foreach ($data['channelId'] as $channelId) {
                    if (isset($value->discInfo['base'])) {
                        $base[] = isset($value->discInfo['base'][$channelId][$keyName]) ?
                            $value->discInfo['base'][$channelId][$keyName] : '';
                    }
                    if (isset($value->discInfo['waitBuy_zpf'])) {
                        $waitBuy_zpf[] = isset($value->discInfo['waitBuy_zpf'][$channelId][$keyName]) ?
                            $value->discInfo['waitBuy_zpf'][$channelId][$keyName] : '';
                    }
                    if (isset($value->discInfo['waitBuy_o'])) {
                        $waitBuy_o[] = isset($value->discInfo['waitBuy_o'][$channelId][$keyName]) ?
                            $value->discInfo['waitBuy_o'][$channelId][$keyName] : '';
                    }
                    if (isset($value->discInfo['inclusive_opf'])) {
                        $inclusive_opf[] = isset($value->discInfo['inclusive_opf'][$channelId][$keyName]) ?
                            $value->discInfo['inclusive_opf'][$channelId][$keyName] : '';
                    }
                    if (isset($value->discInfo['inclusive_t'])) {
                        $inclusive_t[] = isset($value->discInfo['inclusive_t'][$channelId][$keyName]) ?
                            $value->discInfo['inclusive_t'][$channelId][$keyName] : '';
                    }
                    if (isset($value->discInfo['other'])) {
                        $other[] = isset($value->discInfo['other'][$channelId][$keyName]) ?
                            $value->discInfo['other'][$channelId][$keyName] : '';
                    }
                }// end foreach 3
                $goods_list[] = array_merge(
                    $goodsInfo,[$cellName], $base, $waitBuy_zpf,$waitBuy_o,
                    $inclusive_opf, $inclusive_t, $other
                );
            }// end foreach 2
        }// end foreach 1
        return $goods_list;
    } //end

    /**
     * description 根据表头组装导出数据-新版
     * author zhangdong
     * date 2020.05.13
     */
    private function makeWholesaleGoods($data)
    {
        $goods_list = [];
        foreach ($data['goods'] as $key => $value) {
            //有时SKU没有折扣信息
            if (count($value->discInfo) == 0) {
                $value->discInfo['best']['channel_name'] = '';
                $value->discInfo['best']['final_discount'] = 0;
                $value->discInfo['waitBuy']['offerDisc'] = 0;
                $value->discInfo['waitBuy']['saleDollar'] = 0;
                $value->discInfo['waitBuy']['referRmb'] = 0;
            }
            $referExw = isset($value->discInfo['best']['referExw']) ?
                $value->discInfo['best']['referExw'] : 0;
            $goodsInfo = [
                $value->goods_name, $value->spec_sn, $value->platform_no, $value->spec_weight,
                $value->spec_price, $value->transCn, $value->skuFreight,
                $value->discInfo['best']['channel_name'],$value->discInfo['best']['final_discount'],
                $referExw,$value->discInfo['waitBuy']['offerDisc'],
                $value->discInfo['waitBuy']['saleDollar'],$value->discInfo['waitBuy']['referRmb'],
            ];
            $cost = isset($value->discInfo['cost']) ? $value->discInfo['cost'] : [];
            $costTitle = $data['costTitle'];
            $costDisc = [];
            foreach ($costTitle as $v) {
                $channelId = $v->channel_id;
                $cat_code = $v->cat_code;
                $costDisc[] = isset($cost[$channelId][$cat_code]) ? $cost[$channelId][$cat_code] : 0;
            }
            //合并固定数据和成本折扣数据
            $goods_list[] = array_merge($goodsInfo, $costDisc);
        }// end foreach 1
        return $goods_list;
    } //end



}//end of class
