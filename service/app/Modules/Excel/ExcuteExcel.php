<?php
//created by zhangdong on the 2018.07.11
namespace App\Modules\Excel;

//引入表格操作类库-简版 add by zhangdong on the 2018.07.11
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use alhimik1986\PhpExcelTemplator\PhpExcelTemplator;


class ExcuteExcel extends ExcelSetStyle
{
    use ExcelMakeData;
    //保存或导出时的文件名
    private $name;
    //视图名称
    private $view;
    //文件类型
    private $type;
    //导出文件保存到服务器上时的文件夹名称
    private $folder;
    //导出或保存的模板编码-用于渲染表格样式
    private $code;
    //导出数据的格式
    private $format;

    public function __construct($name = '', $view = '', $type = 'xls', $folder = '')
    {
        $this->name = $name;
        $this->view = $view;
        $this->type = $type;
        $this->folder = $folder;
        ini_set("memory_limit", "500M");
        set_time_limit(0);
    }

    /**
     * description:文件上传-基本通用验证
     * editor:zhangdong
     * date : 2018.07.11
     */
    public function verifyUploadFile($file, $fileName)
    {
        if (!isset($file['upload_file'])) {
            return ['code' => '2005', 'msg' => '上传文件不能为空'];
        }
        //检查表格名称
        $uploadName = $file['upload_file']['name'];
        $matchingRes = strrpos($uploadName, $fileName);
        if ($matchingRes === false) {
            $returnMsg = ['code' => '2007', 'msg' => '请选择本网站提供的模板进行导入'];
            return $returnMsg;
        }
        //检查表格文件格式
        $file_types = explode(".", $uploadName);
        $file_type = $file_types [count($file_types) - 1];
        if (strtolower($file_type) != 'xls' && strtolower($file_type) != 'xlsx') {
            $returnMsg = ['code' => '2008', 'msg' => '请上传xls或xlsx格式的Excel文件'];
            return $returnMsg;
        }
        $excel_file_path = $file['upload_file']['tmp_name'];
        $res = [];
        Excel::load($excel_file_path, function ($reader) use (&$res) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
        });
        //检查上传数据是否为空
        if (count($res) <= 1) {
            $returnMsg = ['code' => '2067', 'msg' => '上传数据不能为空'];
            return $returnMsg;
        }
        //检查列最大值-避免表格数据异常问题 2019.08.20 zhangdong
        if (count($res[0]) > 25) {
            $returnMsg = ['code' => '2067', 'msg' => '上传表格列数超过上限，请检查'];
            return $returnMsg;
        }

        return $res;
    }

    /**
     * description:导出表格
     * editor:zhangdong
     * date : 2018.07.12
     */
    public function export($exportData, $filename, $fileType = 'xls')
    {
        Excel::create($filename, function ($excel) use ($exportData) {
            $excel->sheet('sheet1', function ($sheet) use ($exportData) {
                $sheet->rows($exportData);
            });
        })->export($fileType);
    }

    /**
     * description:导出表格
     * editor:zongxing
     * date : 2018.12.29
     */
    public function exportZ($exportData, $filename, $fileType = 'xls')
    {
        Excel::create(iconv('UTF-8', 'GBK', $filename), function ($excel) use ($exportData) {
            $excel->sheet('sheet1', function ($sheet) use ($exportData) {
                $sheet->rows($exportData);
                $heigh_column_name = $sheet->getHighestColumn();
                $heigh_row_num = $sheet->getHighestRow();
                $heigh_column_num = Coordinate::columnIndexFromString($heigh_column_name);

                //设置整个表的样式
                $sheet->cell('A1:' . $heigh_column_name . $heigh_row_num, function ($cell) {
                    $cell->setFont(array(
                        'family' => 'Calibri',
                        'size' => '12',
                    ));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                //设置标题样式(前五行)
                $sheet->cell('A1:' . $heigh_column_name . '1', function ($cell) {
                    $cell->setFont(['bold' => true]);
                });
                for ($i = 1; $i <= $heigh_column_num; $i++) {
                    $column_name = Coordinate::stringFromColumnIndex($i);
                    $width = 20;
                    $sheet->setWidth($column_name, $width);
                }
            });

        })->export($fileType);
    }


    /**
     * description:文件上传-基本通用验证
     * editor:zongxing
     * date : 2018.07.14
     */
    public function verifyUploadFileZ($file, $fileName)
    {
        //检查表格名称
        $uploadName = $file['upload_file']['name'];
        $matchingRes = strrpos($uploadName, $fileName);
        if ($matchingRes === false) {
            $returnMsg = ['code' => '1101', 'msg' => '请选择本网站提供的模板进行导入'];
            return $returnMsg;
        }
        //检查表格文件格式
        $file_types = explode(".", $uploadName);
        $file_type = $file_types [count($file_types) - 1];
        if (strtolower($file_type) != 'xls' && strtolower($file_type) != 'xlsx') {
            $returnMsg = [
                'code' => '1102',
                'msg' => '请上传xls或xlsx格式的Excel文件'
            ];
            return $returnMsg;
        }
        $excel_file_path = $file['upload_file']['tmp_name'];
        $res = [];
        Excel::load($excel_file_path, function ($reader) use (&$res) {
            $reader = $reader->getSheet(0);
            $res = $reader->toArray();
        });
        return $res;
    }


    /**
     * description:总单详情-导出总单信息
     * editor:zhangdong
     * date : 2019.01.02
     * @return
     */
    public function exportMisOrdData($data, $misOrderSn, $subGoodsInfo)
    {
        if (empty($data)) return false;
        $title[] = [
            '商品名称', '商家编码', '商品规格码', '最大供货数量',
            '美金原价', '销售折扣', '美金报价',
        ];
        $subGoodsInfo = objectToArray($subGoodsInfo);
        $goods_list = [];
        foreach ($data as $key => $value) {
            $spec_price = trim($value->spec_price);
            $sale_discount = trim($value->sale_discount);
            $user_price = calculateUserPrice($spec_price, $sale_discount);
            $spec_sn = trim($value->spec_sn);
            //查询子单中的商品信息
            $searchRes = searchTwoArray($subGoodsInfo, $spec_sn, 'spec_sn');
            $waitLockNum = $waitBuyNum = 0;
            if (count($searchRes) > 0) {
                $waitLockNum = intval($searchRes[0]['wait_lock_num']);
                $waitBuyNum = intval($searchRes[0]['wait_buy_num']);
            }
            //最大供货量 = 子单中待锁库数量 + 子单中预判采购数量
            $maxSupplyNum = $waitLockNum + $waitBuyNum;
            $goods_list[$key] = [
                trim($value->goods_name), trim($value->erp_merchant_no),
                trim($value->spec_sn), $maxSupplyNum,
                $spec_price, $sale_discount, $user_price
            ];
        }
        //子单号
        $filename = '总单导出_' . $misOrderSn;
        $exportData = array_merge($title, $goods_list);
        //数据导出
        $this->export($exportData, $filename);

    }

    /**
     * description:导入表格文件字段验证
     * editor:zhangdong
     * date : 2019.01.08
     * params:$checkField (要检查的字段)
     * params:$fileField (上传文件中的字段)
     * @return
     */
    public function checkImportField($checkField, $fileField)
    {
        foreach ($checkField as $title) {
            if (!in_array(trim($title), $fileField)) {
                return false;
            }
        }
        return true;
    }

    /**
     * description:子单详情-导出订单-数据组装
     * notice:2019.01.22迁移至此
     * editor:zhangdong
     * date : 2018.12.17
     * @return bool
     */
    public function exportSpotOrdData($data)
    {
        if (empty($data)) return false;
        $title[] = [
            '商品名称', '商家编码', '商品规格码', '最大供货数量',
            '美金原价', '销售折扣', '美金报价'
        ];
        $sub_order_sn = trim($data[0]->sub_order_sn);
        $goods_list = [];
        foreach ($data as $key => $value) {
            $spec_price = trim($value->spec_price);
            $sale_discount = trim($value->sale_discount);
            $user_price = calculateUserPrice($spec_price, $sale_discount);
            $waitLockNum = intval($value->wait_lock_num);
            $waitBuyNum = intval($value->wait_buy_num);
            //最大供货量 = 待锁库数量 + 预判采购数量
            $maxSupplyNum = $waitLockNum + $waitBuyNum;
            $goods_list[$key] = [
                trim($value->goods_name), trim($value->erp_merchant_no),
                trim($value->spec_sn), $maxSupplyNum,
                $spec_price, $sale_discount, $user_price
            ];
        }
        //子单号
        $filename = '订单信息_' . $sub_order_sn;
        $exportData = array_merge($title, $goods_list);
        //数据导出
        $this->export($exportData, $filename);
    }

    /**
     * description:商品模块-ERP商品列表
     * editor:zhangdong
     * date : 2019.01.25
     * @return bool
     */
    public function exportErpGoods($data)
    {
        if (empty($data)) return false;
        ini_set("memory_limit", "500M");
        set_time_limit(0);
        $title[] = [
            '商品名称', '货品编号', '货品简称', '商家编码', '条形码', '批发仓',
            '保税仓-集货街', '保税仓-零售', '香港-折痕仓', '品牌仓', '黑匣子', '共销仓',
            '资源仓', '集货街中转仓', '保税共销仓（欧美）', '香港-电商仓', '保税-日韩开架仓',
        ];
        $goods_list = [];
        foreach ($data as $key => $value) {
            $goods_list[] = [
                trim($value->goods_name), trim($value->goods_no), trim($value->goods_short_name),
                trim($value->spec_no), trim($value->barcode), intval($value->pf),
                intval($value->jhjbs), intval($value->bsls), intval($value->hkzh), intval($value->pp),
                intval($value->hxz), intval($value->gx), intval($value->zy), intval($value->jhjzz),
                intval($value->bsgx), intval($value->hkds), intval($value->bsrhkj)
            ];
        }
        //子单号
        $strDate = date('Ymd');
        $filename = 'ERP商品导出_' . $strDate;
        $exportData = array_merge($title, $goods_list);
        //数据导出
        $fileType = 'csv';
        $this->export($exportData, $filename, $fileType);

    }//end of function


    /**
     * description:保存上传文件到指定位置
     * editor:zhangdong
     * date : 2019.03.26
     * @return bool
     */
    public function saveUploadFile($file, $fileFlag)
    {
        //判断是否为上传的文件
        $temp_name = $file['upload_file']['tmp_name'];
        if (is_uploaded_file($temp_name) === false) {
            return false;
        }
        //以访问接口名称为依据创建保存上传文件的二级目录
        //接口名称
        $apiName = getApiName();
        //创建以接口名称命名的文件夹
        $target_name = $_SERVER['DOCUMENT_ROOT'] . "/uploadFile/$apiName";
        $mkdirRes = true;
        //检查目录是否存在
        if (is_dir($target_name) === false) {
            $mkdirRes = mkdir($target_name);
        };
        if ($mkdirRes === false) {
            return false;
        }
        $file_name = $file['upload_file']['name'];
        $file_types = explode(".", $file_name);
        $file_type = $file_types [count($file_types) - 1];
        $target_name = "$target_name/$fileFlag.$file_type";
        //将tmp文件移动到服务器指定位置
        if (move_uploaded_file($temp_name, $target_name)) {
            $target_name = '..' . substr($target_name, strpos($target_name, 'service') - 1, -1);
            return $target_name;
        }
        return false;
    }

    /**
     * description:预判数据导出
     * author:zhangdong
     * date : 2019.04.24
     */
    public function exportAdvanceData($misOrderSn, $data)
    {
        if (empty($data)) return false;
        ini_set("memory_limit", "500M");
        set_time_limit(0);
        $title[] = [
            '商品名称', '品牌名称', '商品规格码', '商家编码', '平台条码',
            '参考代码', '商品代码', '美金原价', '需求量', '预判采购量', '交付日期',
            '新的美金原价',
        ];
        $goods_list = [];
        foreach ($data as $key => $value) {
            $goods_list[] = [
                trim($value->goods_name), trim($value->brand_name), trim($value->spec_sn) . "\t",
                trim($value->erp_merchant_no) . "\t", trim($value->platform_barcode) . "\t",
                trim($value->erp_ref_no) . "\t", trim($value->erp_prd_no) . "\t",
                trim($value->spec_price), intval($value->goods_number), intval($value->wait_buy_num),
                trim($value->entrust_time),
            ];
        }
        //子单号
        $filename = '预判数量_' . $misOrderSn;
        $exportData = array_merge($title, $goods_list);
        //数据导出
        $fileType = 'xls';
        $this->export($exportData, $filename, $fileType);

    }//end of function

    /**
     * @description:导入数据字段检查
     * @author:zhangdong
     * @date : 2019.04.25
     * @param $importTitle (导入的字段)
     * @param $needTitle (需要的字段)
     * @return mixed
     */
    public function checkTitle(array $importTitle = [], array $needTitle = [])
    {
        if (count($importTitle) == 0 || count($needTitle) == 0) {
            return ['code' => '2068', 'msg' => '非法访问'];;
        }
        foreach ($needTitle as $title) {
            if (!in_array(trim($title), $importTitle)) {
                return ['code' => '2009', 'msg' => '您的标题头有误，请按模板导入'];
            }
        }
        return true;

    }

    /**
     * description:总单导入-重组数据并检查导入数据是否有重复的spec_sn
     * author:zhangdong
     * date : 2019.05.06
     */
    public function checkImportData($res)
    {
        $subOrderGoods = $arrSpecSn = [];
        foreach ($res as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $spec_sn = isset($value[2]) ? trim($value[2]) : '';
            $arrSpecSn[] = $spec_sn;
            $subOrderGoods[$key]['spec_sn'] = $spec_sn;
            $subOrderGoods[$key]['goods_number'] = isset($value[3]) ? intval($value[3]) : 0;
            $subOrderGoods[$key]['sale_discount'] = isset($value[5]) ? floatval($value[5]) : 0;
        }
        //检查是否有重复的规格码
        $repeatData = fetchRepeatMemberInArray($arrSpecSn);
        return ['repeatData' => $repeatData, 'subOrderGoods' => $subOrderGoods];

    }

    /**
     * description:总单新品导出
     * author:zhangdong
     * date : 2019.04.24
     */
    public function exportOrdNew($misOrderSn, $data)
    {
        if (empty($data)) return false;
        ini_set("memory_limit", "500M");
        set_time_limit(0);
        $title[] = [
            '信息ID', '品牌ID', '品牌名称', '商品名称', '商家编码', '平台条码',
            '美金原价', '商品重量', '商品预估重量', 'EXW折扣', '参考代码', '商品代码',
        ];
        $goods_list = [];
        foreach ($data as $key => $value) {
            $goods_list[] = [
                intval($value->id), intval($value->brand_id), trim($value->brand_name), trim($value->goods_name),
                trim($value->erp_merchant_no) . "\t", trim($value->platform_barcode) . "\t",
                floatval($value->spec_price), floatval($value->spec_weight), floatval($value->estimate_weight),
                floatval($value->exw_discount), trim($value->erp_ref_no) . "\t", trim($value->erp_prd_no) . "\t",
            ];
        }
        //子单号
        $filename = '总单新品_' . $misOrderSn;
        $exportData = array_merge($title, $goods_list);
        //数据导出
        $fileType = 'xls';
        $this->export($exportData, $filename, $fileType);
    }//end of function

    /**
     * description:报价数据导出
     * author:zhangdong
     * date : 2019.06.12
     */
    public function exportOffer($misOrderSn, $exportData, $pickMarginRate)
    {
        if (empty($exportData)) return false;
        ini_set("memory_limit", "500M");
        set_time_limit(0);
        $title[] = [
            '品牌', '商品规格码', '平台条码', 'ERP条码', '参考代码', '商品代码', '商品名称', '需求数量',
            '交期', '美金原价', '货品简称', '单重', '重价比折扣', '成本折扣', '均值毛利' . $pickMarginRate . '%的折扣',
            '均值毛利' . $pickMarginRate . '%的供货价', '第一次交付时间', '第一次BD折扣', '第一次BD美金',
            '第一次BD数量', '第一次DD折扣', '第一次DD美金', '第二次交付时间', '第二次BD折扣',
            '第二次BD美金', '第二次BD数量', '第二次DD折扣',
            '第二次DD美金', '第二次DD数量', 'ERP现货库存', '采购预判数量',
            '是否为套装拆单', '交货方式',
        ];
        $goods_list = [];
        foreach ($exportData as $key => $value) {
            $goodsCode = trim($value->goodsCode);
            if (!empty($value->platform_barcode)) {
                $goodsCode = trim($value->platform_barcode);
            }
            $goods_list[] = [
                trim($value->brand_name), trim($value->spec_sn) . "\t", $goodsCode . "\t",
                trim($value->erp_merchant_no) . "\t", trim($value->erp_ref_no) . "\t", trim($value->erp_prd_no) . "\t",
                trim($value->goods_name), intval($value->goods_number), trim($value->entrust_time) . "\t",
                floatval($value->spec_price), trim($value->erp_ref_no), floatval($value->spec_weight),
                floatval($value->hprDiscount), floatval($value->costDiscount), floatval($value->pmrDiscount),
                floatval($value->pmrdPrice), trim($value->firstTime), floatval($value->firstBdSaleDiscount),
                floatval($value->firstBdSpecPrice), intval($value->firstBdNum), floatval($value->firstDdSaleDiscount),
                floatval($value->firstDdSpecPrice), trim($value->secondTime),
                floatval($value->secondBdSaleDiscount), floatval($value->secondBdSpecPrice),
                intval($value->secondBdNum), floatval($value->secondDdSaleDiscount),
                floatval($value->secondDdSpecPrice), intval($value->secondDdNum), intval($value->gStockNum),
                intval($value->wait_buy_num),
            ];
        }
        //子单号
        $filename = '商品报价_' . $misOrderSn;
        $exportData = array_merge($title, $goods_list);
        //数据导出
        $fileType = 'xls';
        $this->export($exportData, $filename, $fileType);

    }

    /**
     * description:BD总单信息导出
     * author:zhangdong
     * date : 2019.06.13
     */
    public function exportMisOrder($misOrderSn, $data)
    {
        if (empty($data)) return false;
        ini_set("memory_limit", "500M");
        set_time_limit(0);
        $title[] = [
            '商品规格码', '平台条码', 'ERP条码', '商品名称', '供货数量', '供货美金', '交期', '交货方式',
        ];
        $goods_list = [];
        foreach ($data as $key => $value) {
            $goodsCode = trim($value->platform_barcode);
            if (empty($goodsCode)) {
                $goodsCode = trim($value->goodsCode);
            }
            $goods_list[] = [
                trim($value->spec_sn) . "\t", $goodsCode . "\t", trim($value->erp_merchant_no) . "\t",
                trim($value->goods_name), intval($value->wait_buy_num),
                floatval($value->spec_price), trim($value->entrust_time) . "\t",
            ];
        }
        $filename = '总单导出_' . $misOrderSn;
        $exportData = array_merge($title, $goods_list);
        //数据导出
        $fileType = 'xls';
        $this->export($exportData, $filename, $fileType);
    }//end of function

    /**
     * description 商品报价导出
     * author zhangdong
     * date 2019.10.22
     */
    public function exportOfferData(array $arrOfferData = [], $reqParams)
    {
        $exportType = trim($reqParams['exportType']);
        ini_set("memory_limit", "500M");
        set_time_limit(0);
        //根据导出文件类型选定导出标题头
        $arrTitle = self::getOfferTitle($exportType, $reqParams);
        //根据导出文件类型组装计算导出数据
        $offerData = self::getOfferData($exportType, $arrOfferData, $reqParams);
        //组装导出模板数据
        $exportData = self::makeExportData($arrTitle['title'], $offerData);
        //数据导出
        $this->exportSkuOffer($exportData, $arrTitle);
        return true;
    }//end of function

    /**
     * description:导出表格
     * editor:zhangdong
     * date : 2019.11.02
     */
    public function exportSkuOffer($exportData, $arrTitle, $type = 1)
    {
        //导出文件名称
        $curDay = date('ymd');
        $fileName = $curDay . '_' . $arrTitle['fileName'] . '.xlsx';
        $templateFile = './export/template/' . $arrTitle['template'] . '.xlsx';
        $fileName = './export/data/skuOffer/' . $fileName;
        //$fileName = './export/data/skuOffer/' . iconv('UTF-8', 'GB2312', $fileName);
        //输出到浏览器
        if($type == 1){
            PhpExcelTemplator::outputToFile($templateFile, $fileName, $exportData);
        }
        //保存到文件
        if($type == 2){
            PhpExcelTemplator::saveToFile($templateFile, $fileName, $exportData);
        }
        return true;
    }

    /**
     * description 生成商品报价数据
     * author zhangdong
     * date 2019.11.06
     */
    public function makeOfferData($arrOfferData, $reqParams)
    {
        $exportType = [
            'EMS_DOLLAR','EMS_RMB','AIRPORT_DOLLAR','AIRPORT_RMB',
        ];
        set_time_limit(0);
        foreach ($exportType as $value) {
            //根据导出文件类型选定导出标题头
            $arrTitle = self::getOfferTitle($value, $reqParams);
            //根据导出文件类型组装计算导出数据
            $offerData = self::getOfferData($value, $arrOfferData, $reqParams);
            //组装导出模板数据
            $exportData = self::makeExportData($arrTitle['title'], $offerData);
            //数据导出
            $this->exportSkuOffer($exportData, $arrTitle, 2);
        }
        return true;
    }//end of function


    /**
     * description 采购任务下载
     * editor zongxing
     * date 2019.10.31
     */
    public function exportPurTask($total_data, $title_num, $title_row, $demand_num, $channel_num, $batch_num, $goods_num, $fileType = 'xlsx')
    {
        $filename = '采购任务表' . date('Y-m-d') . rand(1000, 9999);
        Excel::create($filename, function ($excel) use ($total_data, $title_num, $title_row, $demand_num, $channel_num, $batch_num, $goods_num) {
            $excel->sheet('sheet1', function ($sheet) use ($total_data, $title_num, $title_row, $demand_num, $channel_num, $batch_num, $goods_num) {
                $sheet->rows($total_data);
                $heigh_column_name = $sheet->getHighestColumn();
                $heigh_row_num = $sheet->getHighestRow();
                $heigh_column_num = Coordinate::columnIndexFromString($heigh_column_name);
                //设置整个表的样式
                $sheet->cell('A1:' . $heigh_column_name . $heigh_row_num, function ($cell) {
                    $cell->setFont(array(
                        'family' => 'Calibri',
                        'size' => '12',
                    ));
                    $cell->setAlignment('center');
                    $cell->setValignment('center');
                });
                //设置标题样式(前五行)
                $sheet->cell('A1:' . $heigh_column_name . $title_row, function ($cell) {
                    $cell->setFont(['bold' => true]);
                });
                for ($i = 1; $i <= $heigh_column_num; $i++) {
                    $column_name = Coordinate::stringFromColumnIndex($i);
                    $width = $i == 5 ? 40 : 20;
                    $sheet->setWidth($column_name, $width);
                    if ($i <= $title_num) {//商品基础信息
                        $sheet->setMergeColumn([
                            'columns' => [$column_name],
                            'rows' => [[1, $title_row]]
                        ], 'center');
                    } elseif ($i <= $title_num + $demand_num * 3) {//需求单信息
                        $column_last_name = Coordinate::stringFromColumnIndex($i + 2);
                        for ($j = 1; $j <= $title_row - 1; $j++) {
                            $sheet->mergeCells($column_name . $j . ':' . $column_last_name . $j, 'center');
                        }
                        $i += 2;
                    } elseif ($i <= $heigh_column_num) {//渠道信息
                        if ($i < $heigh_column_num - $batch_num) {
                            $column_last_name = Coordinate::stringFromColumnIndex($i + 4);
                            for ($j = 1; $j <= 2; $j++) {
                                $end_j = $j < 2 ? $j : $j + 2;
                                $sheet->mergeCells($column_name . $j . ':' . $column_last_name . $end_j, 'center');
                            }
                            $i += 4;
                        } else {// 批次上传信息
                            $sheet->mergeCells($column_name . '1:' . $column_name . $title_row, 'center');
                        }
                    }
                }
            });
        })->export($fileType);
    }//end of function

    /**
     * description 利用视图导出excel文件
     * author zhangdong
     * date 2019.11.26
     */
    public function exportByView($data,$columnFormat = [])
    {
        $view = $this->view;
        $type = $this->type;
        Excel::create($this->name, function($excel) use ($data, $view, $columnFormat) {
            $excel->sheet('sheet1', function($sheet) use ($data, $view, $columnFormat) {
                $sheet->setColumnFormat($columnFormat);
                $sheet->loadView($view,['data'=>$data]);
            });
        })->download($type);
    }

    /**
     * description 利用视图保存excel文件
     * author zhangdong
     * date 2019.11.28
     */
    public function saveByView($data,$columnFormat = [])
    {
        set_time_limit(0);
        $view = $this->view;
        $type = $this->type;
        $folder = $this->folder;
        $this->name = iconv('UTF-8', 'GB2312', $this->name);
        $saveRes = Excel::create($this->name, function($excel) use ($data, $view, $columnFormat) {
            $excel->sheet('sheet1', function($sheet) use ($data, $view, $columnFormat) {
                //自动筛选
                $sheet->setAutoFilter('A1:P10');
                //冻结首行
                $sheet->freezeFirstRow();
                $sheet->setColumnFormat($columnFormat);
                $sheet->loadView($view,['data'=>$data]);
            });
        })->store($type, "./export/data/$folder", true);
        return count($saveRes) == 5 ? true : false;
    }

    /**
     * description 购物车报价--生成商品报价数据-停用
     * author zhangdong
     * date 2019.11.28
     */
    public function makeCartOfferDataStop($arrOfferData, $reqParams)
    {
        $cat_A_rate = $cat_A = $cat_C = [];
        foreach ($arrOfferData as $key => $value) {
            $cod = self::commonOfferData($value);
            $arrOfferData[$key]->standard_discount = $cod['standard_discount'];
            $arrOfferData[$key]->lastDiscount = $cod['lastDiscount'];
            $arrOfferData[$key]->goods_name = $cod['goodsName'];
            //人民币付款核算后美金汇率 = 乐天官网美金/韩币汇率/人民币兑换韩币汇率
            $rmbPaidRate = $reqParams['koreanRate']/$reqParams['rmbKoreanRate'];
            $arrOfferData[$key]->rmbPaidRate = $rmbPaidRate;
            unset($arrOfferData[$key]->lastDiscountDiff);
            //对数据通过商品类型进行分类
            $catName = $value->cat_name;
            $catMatchScale = $value->match_scale;
            //A类产品，有搭配比例
            if ($catName == 'A' && $catMatchScale != '') {
                $cat_A_rate[] = $arrOfferData[$key];
            }
            //A类产品，无搭配比例
            if ($catName == 'A' && $catMatchScale == '') {
                $cat_A[] = $arrOfferData[$key];
            }
            //C类产品
            if ($catName == 'C') {
                $cat_C[] = $arrOfferData[$key];
            }
        }
        return [
            'cat_A_rate' => $cat_A_rate,
            'cat_A' => $cat_A,
            'cat_C' => $cat_C,
        ];
    }

    /**
     * description 购物车报价--生成商品报价数据
     * author zhangdong
     * date 2019.12.04
     */
    public function storeCartOffer($data)
    {
        if (empty($data)) return false;
        $this->code = 'cartOffer';
        $data = self::makeData($data, $this->code);
        $this->name = '购物车报价_' . date('ymd');
        $this->folder = $this->code;
        $this->format = [
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_CURRENCY_USD,
            'J' => NumberFormat::FORMAT_PERCENTAGE_00,
            'K' => NumberFormat::FORMAT_PERCENTAGE_00,
            'O' => NumberFormat::FORMAT_NUMBER_00,
        ];
        $res = self::storeByStyle($data);
        return $res;
    }

    /**
     * description 数据导出-保存文件到服务器
     * author zhangdong
     * date 2019.12.04
     */
    private function storeByStyle($data)
    {
        //添加样式时会用到
        $this->maxRow = count($data);
        $saveRes = Excel::create($this->name, function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data) {
                $sheet->setColumnFormat($this->format);
                //先填数据-顺序不能变
                $sheet->rows($data);
                //数据样式处理
                parent::setDataStyle($sheet, $data, $this->code);
                //表头样式处理
                parent::setTitleStyle($sheet, $data, $this->code);
            });
        })->store($this->type, public_path("/export/data/$this->folder"), true);
        //保存成功时，返回数组中有五个元素
        return count($saveRes) == 5 ? true : false;
    }

    /**
     * des 专用-客户订单报价-参数检查-检查导入表格名称的报价单号和传入参数中的报价单是否一致
     * author zhangdong
     * date 2020.03.09
     */
    public function checkOfferSn($file, $offerSn)
    {
        $excelName = trim($file['upload_file']['name']);
        $start = strpos($excelName,'_') + 1;
        $end = strpos($excelName,'.');
        $diff = $end - $start;
        $excelOfferSn = substr($excelName, $start, $diff);
        return $excelOfferSn == $offerSn ? true : false;
    }

    /**
     * description 大批发报价--导出报价数据
     * author zhangdong
     * date 2020.03.30
     */
    public function expWholesaleOffer($wholesaleSn ,$data)
    {
        if (empty($data)) return false;
        $this->inputNum = $data['inputNum'];
        $this->code = 'wholesaleOffer';
        $data = self::makeData($data, $this->code);
        $this->name = '大批发报价_' . $wholesaleSn . '_' . date('mds');
        $this->folder = $this->code;
        $this->format = [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
        $res = self::exportByStyle($data);
        return $res;
    }

    /**
     * description 大批发报价--导出报价数据-新版
     * author zhangdong
     * date 2020.05.13
     */
    public function expWholeOffer($wholesaleSn ,$data)
    {
        if (empty($data)) return false;
        $this->channel = $data['chaName'];
        $this->code = 'wholeOffer';
        $data = self::makeData($data, $this->code);
        $this->name = '大批发报价_' . $wholesaleSn . '_' . date('mds');
        $this->folder = $this->code;
        $this->format = [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
        $res = self::exportByStyle($data);
        return $res;
    }

    /**
     * description  数据导出-从浏览器导出文件
     * author zhangdong
     * date 2020.04.01
     */
    private function exportByStyle($data)
    {
        //添加样式时会用到
        $this->maxRow = count($data);
        Excel::create($this->name, function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data) {
                $sheet->setColumnFormat($this->format);
                //先填数据-顺序不能变
                $sheet->rows($data);
                //数据样式处理
                parent::setDataStyle($sheet, $data, $this->code);
                //表头样式处理
                parent::setTitleStyle($sheet, $data, $this->code);
            });
        })->export($this->type);
        return true;
    }




}//end of class
