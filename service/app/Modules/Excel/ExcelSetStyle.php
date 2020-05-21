<?php

//created by zhangdong on the 2019.12.03
namespace App\Modules\Excel;

use App\Model\Vone\WholesaleDiscountModel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPExcel_Cell;

class ExcelSetStyle
{

    protected $maxRow = 1;

    //大批发报价-输入追加点
    public $inputNum;

    //大批发报价-渠道信息
    public $channel;


    public function __construct($maxRow = 1)
    {
        $this->maxRow = $maxRow;
    }

    /**
     * description 表头样式设置
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    protected function setTitleStyle($sheet, $data = [], $styleCode = '')
    {
        switch ($styleCode) {
            case 'cartOffer':
                self::cartOfferTitle($sheet);
                break;
            case 'wholesaleOffer':
                self::wholesaleOfferTitle($sheet, $data);
                break;
            case 'wholeOffer':
                self::wholeOfferTitle($sheet, $data);
                break;
            default:
                self::defaultTitleStyle($sheet);
                break;
        }
        return $sheet;
    }

    /**
     * description 数据样式设置
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    protected function setDataStyle($sheet, $data, $styleCode = '')
    {
        switch ($styleCode) {
            case 'cartOffer':
                self::cartOfferData($sheet, $data);
                break;
            case 'wholesaleOffer':
                self::wholesaleOfferData($sheet, $data);
                break;
            default:
                self::defaultDataStyle($sheet);
                break;
        }
        return $sheet;
    }

    /**
     * description 默认表头样式
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    private function defaultTitleStyle($sheet)
    {
        //冻结首行-首行宽度
        $sheet->freezeFirstRow()->setHeight(1, 30)->setAutoSize()
            //首行背景色设置-文字水平和垂直均居中
            ->row(1, function($row) {
                $row->setBackground('#A9D08E')
                    ->setAlignment('center')
                    ->setValignment('center');
            });
        return $sheet;
    }

    /**
     * description 默认数据样式
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    private function defaultDataStyle($sheet)
    {
        return $sheet;
    }

    /**
     * description 购物车报价-表头样式
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    private function cartOfferTitle($sheet)
    {
        $range = 'A1:P1';
        $cellWidth = [
            'A' => 7,'B' => 7,'C' => 16,'D' => 35,'E' => 14,'F' => 14,'H' => 7,
            'I' => 10,'J' => 10,'K' => 10,'O' => 9,
        ];
        $maxRow = $this->maxRow;
        //设置边框-首行自动筛选
        $sheet->setBorder('A1:P' . $maxRow)->setAutoFilter($range)
            //冻结首行-首行宽度
            ->freezeFirstRow()->setHeight(1, 30)->setWidth($cellWidth)
            //首行设置背景色为白色-设置D1,B1背景色为黄色-水平和垂直居中
            ->row(1, function($row) {
                $row->setBackground('#FFFFFF');
            })->cells('J1', function($cells) {
                $cells->setBackground('#FFFF00');
            })->cells('K1', function($cells) {
                $cells->setBackground('#FFFF00');
            })->cells($range, function($cells) {
                $cells->setAlignment('center')->setValignment('center');
            })->getStyle($range)->getAlignment()->setWrapText(true);
        return $sheet;
    }

    /**
     * description 购物车报价-数据样式
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    private function cartOfferData($sheet, $data)
    {
        foreach ($data as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $catName = $value[0];
            $catScale = $value[1];
            //A类产品有搭配比例 颜色为 #EEC1B2
            if($catName == 'A' && !empty($catScale)){
                $sheet->row($key+1,function ($row){
                    $row->setBackground('#EEC1B2');
                });
            }
            //A类产品无搭配比例颜色为 #FCD5B4
            if($catName == 'A' && empty($catScale)){
                $sheet->row($key+1,function ($row){
                    $row->setBackground('#FCD5B4');
                });
            }
            //C类产品颜色为#B5CFCB
            if($catName == 'C'){
                $sheet->row($key+1,function ($row){
                    $row->setBackground('#B5CFCB');
                });
            }
        }
        return $sheet;
    }

    /**
     * description 大批发报价-数据样式
     * author zhangdong
     * date 2020.03.30
     * param  LaravelExcelWorksheet $sheet
     */
    private function wholesaleOfferData($sheet, $data)
    {
        //导出数据从第四行开始每三行合并一次
        $i = 0;
        foreach ($data as $key => $value) {
            if ($key < 4) {
                continue;
            }
            if($key >= $i){
                $sheet->setMergeColumn([
                    'columns' => ['A','B','C','D','E','F'],
                    'rows' => [[$key+1, $key + 3],]
                ])->row($key+1,function ($row){
                    $row->setBackground('#FFE699');
                })->cells('A5:F' . $this->maxRow, function($cells) {
                    $cells->setBackground('#CCE8CF');
                });
                $i = $key +3;
            } else {
                $sheet->row($key+1,function ($row){
                    $row->setBackground('#CCE8CF');
                });
            }
        }
        return $sheet;
    }

    /**
     * description 大批发报价数据导出-表头样式
     * author zhangdong
     * date 2019.12.04
     * param  LaravelExcelWorksheet $sheet
     */
    private function wholesaleOfferTitle($sheet, $data)
    {
        $maxRow = $this->maxRow;
        //表格最后一个单元格对应的字母-从第二行开始算起
        $lastCell = PHPExcel_Cell::stringFromColumnIndex(count($data[1])-1);
        $range = 'A2:' . $lastCell . $maxRow;
        //对折扣类型的表头做合并处理-第二行
        $this->mergeSecRow($sheet, $data[1]);
        //对追加点表头做合并处理-第三行
        $this->mergeThrRow($sheet, $data[2]);
        $cellWidth = [
            'A' => 25,'B' => 15,'C' => 20,'D' => 17,'E' => 10,'F' => 10,
            'G' => 15
        ];
        //对首行做单独处理
        $sheet->setBorder('A1:F1') ->cells('A1:F1', function($cells) {
            $cells->setAlignment('center')->setValignment('center');
        });
        //设置边框-首行自动筛选
        $sheet->setBorder($range)->setAutoFilter('A4:F4')
            //冻结前两行-前两行宽度
            ->setFreeze('H5')->setHeight(1, 20)->setHeight(2, 30)->setWidth($cellWidth)
            //前两行设置背景色为浅绿色
            ->row(1, function($row) {$row->setBackground('#8EA9DB');})
            ->row(2, function($row) {$row->setBackground('#8EA9DB');})
            ->row(3, function($row) {$row->setBackground('#8EA9DB');})
            ->row(4, function($row) {$row->setBackground('#8EA9DB');})
            //将商品基本信息的每个字段按三行进行合并
            ->setMergeColumn([
                    'columns' => ['A','B','C','D','E','F'],
                    'rows' => [[2,4],]
            ])
            ->cells($range, function($cells) {
                $cells->setAlignment('center')->setValignment('center');
            })->getStyle($range)->getAlignment()->setWrapText(true);
        return $sheet;
    }//end

    /**
     * description 大批发报价-对折扣类型的表头做合并处理-第二行
     * author zhangdong
     * date 2020.03.31
     * param  LaravelExcelWorksheet $sheet
     */
    private function mergeSecRow($sheet, $secondRow)
    {
        $strDiscPlate = (new WholesaleDiscountModel())->strDiscPlate;
        $rowNum = 2;
        $base = $waitBuy = $inclusive = $other = [];
        foreach ($secondRow as $key => $value) {
            if($key < 7){
                continue;
            }
            if ($value == $strDiscPlate[1]) {
                $base[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $strDiscPlate[2]) {
                $waitBuy[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $strDiscPlate[4]) {
                $inclusive[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $strDiscPlate[6]) {
                $other[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
        }
        if (count($base) > 0) {
            $merge = reset($base) . $rowNum . ':' . end($base) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($waitBuy) > 0) {
            $merge = reset($waitBuy) . $rowNum . ':' . end($waitBuy) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($inclusive) > 0) {
            $merge = reset($inclusive) . $rowNum . ':' . end($inclusive) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($other) > 0) {
            $merge = reset($other) . $rowNum . ':' . end($other) . $rowNum;
            $sheet->mergeCells($merge);
        }
        return $sheet;
    }// end

    /**
     * description 大批发报价-对追加点表头做合并处理-第三行
     * author zhangdong
     * date 2020.03.31
     * param  LaravelExcelWorksheet $sheet
     */
    private function mergeThrRow($sheet, $thirdRow)
    {
        $rowNum = 3;
        $appendDisc = (new WholesaleDiscountModel())->appendDiscPlate;
        $base_z = $waitBuy_zpf = $waitBuy_o = $inclusive_opf = $inclusive_t = $other = [];
        foreach ($thirdRow as $key => $value) {
            if($key < 7){
                continue;
            }
            if ($value == $appendDisc[1] . '%') {
                $base_z[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $appendDisc[2] . '%') {
                $waitBuy_zpf[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $appendDisc[3] . '%') {
                $waitBuy_o[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $appendDisc[4] . '%') {
                $inclusive_opf[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $appendDisc[5] . '%') {
                $inclusive_t[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
            if ($value == $this->inputNum . '%') {
                $other[] = PHPExcel_Cell::stringFromColumnIndex($key);
            }
        }
        if (count($base_z) > 0) {
            $merge = reset($base_z) . $rowNum . ':' . end($base_z) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($waitBuy_zpf) > 0) {
            $merge = reset($waitBuy_zpf) . $rowNum . ':' . end($waitBuy_zpf) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($waitBuy_o) > 0) {
            $merge = reset($waitBuy_o) . $rowNum . ':' . end($waitBuy_o) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($inclusive_opf) > 0) {
            $merge = reset($inclusive_opf) . $rowNum . ':' . end($inclusive_opf) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($inclusive_t) > 0) {
            $merge = reset($inclusive_t) . $rowNum . ':' . end($inclusive_t) . $rowNum;
            $sheet->mergeCells($merge);
        }
        if (count($other) > 0) {
            $merge = reset($other) . $rowNum . ':' . end($other) . $rowNum;
            $sheet->mergeCells($merge);
        }
        return $sheet;
    }// end


    /**
     * description 大批发报价数据导出-表头样式-新版
     * author zhangdong
     * date 2020.05.13
     * param  LaravelExcelWorksheet $sheet
     */
    private function wholeOfferTitle($sheet, $data)
    {
        $maxRow = $this->maxRow;
        //表格最后一个单元格对应的字母-从第二行开始算起
        $lastCell = PHPExcel_Cell::stringFromColumnIndex(count($data[1])-1);
        $range = 'A2:' . $lastCell . $maxRow;
        //除第一行外，对所有数据加边框
        $sheet->setBorder($range);
        //第1行表头处理
        $afRage = 'A1:H1';
        $sheet->setBorder($afRage)->cells($afRage, function($cells) {
            $cells->setAlignment('center')->setValignment('center');
        });
        //第2,3行表头-基础信息合并
        $agRange = 'A2:G3';
        $sheet->mergeCells($agRange)->cells($agRange, function($cells) {
            $cells->setAlignment('center')->setValignment('center');
        });
        //第2,3行表头-最优渠道折扣合并
        $hjRange = 'H2:J3';
        $sheet->mergeCells($hjRange)->cells($hjRange, function($cells) {
            $cells->setAlignment('center')->setValignment('center');
        });
        //第2,3行表头-代采报价合并
        $kmRange = 'K2:M3';
        $sheet->mergeCells($kmRange)->cells($kmRange, function($cells) {
            $cells->setAlignment('center')->setValignment('center');
        });
        //第2行表头-成本折扣合并
        $costRange = 'N2:' . $lastCell .'2';
        $sheet->mergeCells($costRange)->cells($costRange, function($cells) {
            $cells->setAlignment('center')->setValignment('center');
        });
        //第3行表头-合并成本折扣对应渠道
        $this->getChannelPos($data[2], $sheet);
        //单元格宽度
        $cellWidth = [
            'A' => 30,'B' => 15,'C' => 20,'D' => 10,'E' => 10,'F' => 10,
            'G' => 10,'H' => 8
        ];
        //设置边框-首行自动筛选
        $sheet->setAutoFilter('A4:M4')
            //冻结前4行和M列-前两行宽度
            ->setFreeze('H5')->setHeight(1, 20)->setWidth($cellWidth)
            //前两行设置背景色为浅绿色
            ->row(1, function($row) {$row->setBackground('#8EA9DB');})
            ->row(2, function($row) {$row->setBackground('#8EA9DB');})
            ->row(3, function($row) {$row->setBackground('#8EA9DB');})
            ->row(4, function($row) {$row->setBackground('#8EA9DB');})
            ->cells($range, function($cells) {
                $cells->setAlignment('center')->setValignment('center');
            })->getStyle($range)->getAlignment()->setWrapText(true);
        return $sheet;
    }//end

    /**
     * description 大批发报价-第3行表头处理-新版-合并相同渠道
     * author zhangdong
     * date 2020.05..14
     */
    private function getChannelPos($data, $sheet)
    {
        $channel = $this->channel;
        $chaTitle = array_intersect($data, $channel);
        $mergerGroup = [];
        foreach ($channel as $value) {
            foreach ($chaTitle as $key => $item) {
                if ($value == $item) {
                    $mergerGroup[$value][] = $key;
                }
            }
        }
        foreach ($mergerGroup as $v) {
            $start_letter = PHPExcel_Cell::stringFromColumnIndex(reset($v));
            $end_letter = PHPExcel_Cell::stringFromColumnIndex(end($v));
            $rage = $start_letter . '3:' . $end_letter . '3';
            $sheet->mergeCells($rage)->cells($rage, function($cells) {
                $cells->setAlignment('center')->setValignment('center');
            });
        }
        return $sheet;
    }// end





}//end of class
