<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoodsCodeModel extends Model
{
    public $table = 'goods_code as gc';

    //可操作字段
    protected $field = ['gc.id', 'gc.spec_sn', 'gc.goods_code', 'gc.code_type'];

    public $code_type = [
        'ERP_MERCHANT_NO' => 1,//ERP商家编码
        'KL_CODE' => 2,//考拉编码
        'XHS_CODE' => 3,//小红书编码
    ];

    //销售用户编码
    public $sale_code = [
        '1' => 'KL_CODE',//考拉
        '34' => 'XHS_CODE'//小红书
    ];

    public $code_desc = [
        1 => '商家编码',
        2 => '考拉编码',
        3 => '小红书编码',
    ];

    /**
     * description:查询商品编码对应的规格码
     * editor:zhangdong
     * date : 2019.01.31
     */
    public function getSpecSnByCode(array $arrGoodsCode = [])
    {
        //去除数组中空白元素
        $arrGoodsCode = array_filter($arrGoodsCode);
        $queryRes = DB::table($this->table)->select($this->field)
            ->whereIn('goods_code', $arrGoodsCode)->get();
        return $queryRes;
    }

    /**
     * description:获取商品编码信息
     * author：zongxing
     * date : 2019.02.20
     */
    public function getGoodsCodeInfo($param_info)
    {
        $goods_code_obj = DB::table($this->table)->select($this->field);
        if (!empty($param_info['code_type'])) {
            $code_type = intval($param_info['code_type']);
            $goods_code_obj->where('gc.code_type', $code_type);
        }
        if (!empty($param_info['goods_code_id'])) {
            $goods_code_id = intval($param_info['goods_code_id']);
            $goods_code_obj->where('gc.id', $goods_code_id);
        }
        if (!empty($param_info['goods_code'])) {
            $goods_code = trim($param_info['goods_code']);
            $goods_code_obj->where('gc.goods_code', $goods_code);
        }
        if (!empty($param_info['erp_merchant_no'])) {
            $goods_code = trim($param_info['erp_merchant_no']);
            $goods_code_obj->where('gc.goods_code', $goods_code);
        }
        $goods_code_info = $goods_code_obj->first();
        $goods_code_info = objectToArrayZ($goods_code_info);
        return $goods_code_info;
    }

    /**
     * description:新增单个商品编码
     * editor:zongxing
     * date : 2019.02.20
     * return Array
     */
    public function doAddGoodsCode($param_info)
    {
        $goods_code_info = [
            'spec_sn' => trim($param_info['spec_sn']),
            'code_type' => intval($param_info['code_type']),
            'goods_code' => trim($param_info['goods_code']),
        ];

        $insertRes = DB::table('goods_code')->insertGetId($goods_code_info);
        $return_info = false;
        if ($insertRes) {
            $return_info['id'] = $insertRes;
            $return_info['code_info'] = $goods_code_info;
        }
        return $return_info;
    }

    /**
     * description:提交编辑商品编码
     * editor:zongxing
     * date : 2019.02.20
     * return Object
     */
    public function doEditGoodsCode($param_info)
    {
        $goods_code_id = intval($param_info['goods_code_id']);
        $edit_goods_code_info = [
            'goods_code' => floatval($param_info['goods_code']),
            'code_type' => floatval($param_info['code_type']),
        ];
        $updateRes = DB::table('goods_code')->where('id', $goods_code_id)->update($edit_goods_code_info);
        return $updateRes;
    }

    /**
     * description:生成商品编码信息
     * author:zhangdong
     * date : 2019.04.16
     */
    private function generalCodeInfo($spec_sn, array $arrGoodsCode = [])
    {
        $goodsCodeInfo = [];
        foreach ($arrGoodsCode as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $goodsCodeInfo[] = [
                        'spec_sn' => trim($spec_sn),
                        'code_type' => intval($key),
                        'goods_code' => trim($item),
                    ];
                }
                continue;
            }
            $goodsCodeInfo[] = [
                'spec_sn' => trim($spec_sn),
                'code_type' => intval($key),
                'goods_code' => trim($value),
            ];
        }
        return $goodsCodeInfo;
    }

    /**
     * description:生成商品编码信息
     * author:zhangdong
     * date : 2019.04.16
     */
    public function getOneArrayGoodsCode($arrListGoodsCode, $field = 'goods_code')
    {
        $arrGoodsCode = [];
        foreach ($arrListGoodsCode as $value) {
            $arrGoodsCode[] = trim($value[$field]);
        }
        return array_unique($arrGoodsCode);
    }

    /**
     * description:检查新品是否真的已被全部创建,并将规格码加入新品
     * author:zhangdong
     * date : 2019.04.18
     * @return mixed
     */
    public function checkNewIsCreated($newGoodsInfo)
    {
        //组装商品编码
        $arrSpecSn = [];
        foreach ($newGoodsInfo as $key => $value) {
            $goodsCode[] = trim($value->erp_merchant_no);//商家编码
            $goodsCode = array_filter(array_merge($goodsCode, explode(',', trim($value->platform_barcode))));//考拉编码
            $codeSpecInfo = $this->getSpecSnByCode($goodsCode);
            //为新品加入规格码
            $spec_sn = '';
            if ($codeSpecInfo->count() > 0) {
                $spec_sn = trim($codeSpecInfo[0]->spec_sn);
                $arrSpecSn[] = $spec_sn;
            }
            $newGoodsInfo[$key]->spec_sn = $spec_sn;
            //销毁商品编码
            unset($goodsCode);
        }
        //检查系统统计数量和新品数量是否一致
        if ($newGoodsInfo->count() != count($arrSpecSn)) {
            return false;
        }
        return $newGoodsInfo;
    }


    /**
     * description:查询商品编码对应的规格码
     * author:zhangdong
     * date : 2019.04.24
     */
    public function getCodeBySpecSn(array $arrSpecSn = [])
    {
        if (count($arrSpecSn) == 0) {
            return [];
        }
        //去除数组中空白元素
        $arrSpecSn = array_filter($arrSpecSn);
        $queryRes = DB::table($this->table)->select($this->field)
            ->whereIn('spec_sn', $arrSpecSn)->orderBy('spec_sn', 'ASC')
            ->orderBy('code_type', 'ASC')->get();
        return $queryRes;
    }

    /**
     * description:通过商品编码获取商品规格码
     * autho:zhangdong
     * date:2019.05.21
     */
    public function getSpecSnByGoodsCode($goodsCodeInfo, $arrGoodsCode)
    {
        //根据goodsCode查出spec_sn
        $specSn = '';
        foreach ($arrGoodsCode as $item) {
            $goodsCode = trim($item);
            $searchRes = searchTwoArray($goodsCodeInfo, $goodsCode, 'goods_code');
            $specSn = isset($searchRes[0]['spec_sn']) ? trim($searchRes[0]['spec_sn']) : '';
            if (!empty($specSn)) {
                break;
            }
        }
        return $specSn;
    }

    /**
     * description:写入商品编码信息
     * autho:zhangdong
     * date:2019.05.24
     */
    public function insertGoodsCode(array $goodsCodeInfo = [])
    {
        if (count($goodsCodeInfo) == 0) {
            return false;
        }
        $gcTable = cutString($this->table, 0, 'as');
        $insertRes = DB::table($gcTable)->insert($goodsCodeInfo);
        return $insertRes;

    }

    /**
     * description:处理商品编码-适用于新品
     * autho:zhangdong
     * date:2019.05.24
     */
    public function operateGoodsCode(array $newGoodsInfo = [], $spec_sn)
    {
        if (count($newGoodsInfo) == 0) {
            return false;
        }
        $arrGoodsCode = [
            '1' => trim($newGoodsInfo['erp_merchant_no']),//商家编码
            //考拉编码-该编码可能有多个，所以要转为数组
            '2' => array_filter(explode(',', trim($newGoodsInfo['kl_code']))),
            '3' => trim($newGoodsInfo['xhs_code']),//小红书编码
        ];
        //商家编码和小红书编码可能为空，所以此处要过滤空白字符串
        $arrGoodsCode = array_filter($arrGoodsCode);
        $goodsCodeInfo = $this->generalCodeInfo($spec_sn, $arrGoodsCode);
        return $goodsCodeInfo;
    }

    /**
     * description:处理商品编码-适用于新品
     * autho:zhangdong
     * date:2019.06.27
     */
    public function makeGoodsCode($strGoodsCode, $spec_sn, $suid)
    {
        $arrGoodsCode = $this->makeArrGoodsCode($strGoodsCode);
        $code_type = $this->getCodeType($suid);
        $goodsCodeInfo = [];
        foreach ($arrGoodsCode as $key => $value) {
            $goodsCodeInfo[] = [
                'spec_sn' => trim($spec_sn),
                'code_type' => $code_type,
                'goods_code' => trim($value),
            ];
        }

        return $goodsCodeInfo;
    }

    /**
     * description:查询商品编码对应的规格码
     * editor:zhangdong
     * date : 2019.01.31
     */
    public function getSpec(array $arrGoodsCode = [])
    {
        if (count($arrGoodsCode) == 0) {
            return false;
        }
        //去除数组中空白元素
        $arrGoodsCode = array_filter($arrGoodsCode);
        $this->field = ['spec_sn'];
        $queryRes = DB::table($this->table)->select($this->field)
            ->whereIn('goods_code', $arrGoodsCode)->first();
        return $queryRes;
    }

    /**
     * description:通过自定义条件获取商品编码
     * author:zhangdong
     * date : 2019.06.11
     * @return string
     */
    public function getStrCodeByWhere(array $where = [])
    {
        if (count($where) == 0) {
            return '';
        }
        $queryRes = DB::table($this->table)->where($where)->implode('goods_code', ',');
        return $queryRes;
    }

    /**
     * description:通过销售用户id获取平台类型
     * author:zhangdong
     * date : 2019.06.11
     */
    public function getCodeType($sale_user_id)
    {
        $sale_code = isset($this->sale_code[$sale_user_id]) ? $this->sale_code[$sale_user_id] : 'ERP_MERCHANT_NO';
        $code_type = $this->code_type[$sale_code];
        return $code_type;
    }

    /**
     * description:通过销售用户id获取平台类型
     * author:zhangdong
     * date : 2019.06.28
     */
    public function makeArrGoodsCode($strGoodsCode)
    {
        $strGoodsCode = ruleStr($strGoodsCode);
        $arrGoodsCode = array_unique(array_filter(explode(',', trim($strGoodsCode))));
        return $arrGoodsCode;
    }

    /**
     * description 获取商品码信息
     * author zongxing
     * date 2019.07.24
     * return Array
     */
    public function getGoodsCode($param_info)
    {
        $goods_code_obj = DB::table($this->table)->select($this->field);
        if (!empty($param_info['spec_sn'])) {
            $spec_sn = trim($param_info['spec_sn']);
            $goods_code_obj->where('gc.spec_sn', $spec_sn);
        }
        if (!empty($param_info['erp_merchant_no'])) {
            $erp_merchant_no = trim($param_info['erp_merchant_no']);
            $goods_code_obj->where('gc.goods_code', $erp_merchant_no);
        }
        $goods_code_info = $goods_code_obj->get();
        $goods_code_info = objectToArrayZ($goods_code_info);
        return $goods_code_info;
    }

    /**
     * description 通过商品编码查询SKU的规格码
     * author zhangdong
     * date 2019.09.16
     */
    public function getSpecSn($goodsCode)
    {
        $where = [
            ['goods_code', $goodsCode],
        ];
        $field = ['gc.spec_sn'];
        $queryRes = DB::table($this->table)->select($field)
            ->where($where)->first();
        $specSn = isset($queryRes->spec_sn) ? trim($queryRes->spec_sn) : '';
        return $specSn;
    }

    /**
     * description 检查平台条码是否有空的并重新组装条码为数组类型
     * author zhangdong
     * date 2020.03.13
     */
    public function getArrPlatformNo($goodsData)
    {
        $strPlatformNo = '';
        foreach($goodsData as $key => $value){
            if ($key == 0) {
                continue;
            }
            $platformNo = trim($value[1]);
            if (empty($platformNo)) {
                $checkRes = '部分平台条码为空，请检查导入表格';
                return ['code' => '2067','msg' => $checkRes];
            }
            //处理不规则编码-去商品编码中所有的空格并将中文逗号转为英文逗号
            $ruleCode = ruleStr($platformNo);
            $strPlatformNo .= $ruleCode . ',';
        }
        $arrPlatFormNo = explode(',', $strPlatformNo);
        return $arrPlatFormNo;
    }


    /**
     * description 批量更新预估重量-数据校验及组装
     * author zhangdong
     * date 2020.03.13
     */
    public function makeUpdateData($goodsData, $specNoInfo)
    {
        $arrData = objectToArray($specNoInfo);
        $none_id = $existWeight = $searchData = $updateGoodsData = [];
        $goodsModel = new GoodsModel();
        foreach($goodsData as $key => $value){
            if ($key == 0) {
                continue;
            }
            //将平台条码组装成数组
            $strGoodsCode = trim($value[1]);
            $arrGoodsCode = $goodsModel->createGoodsCode($strGoodsCode);
            //通过平台条码从系统中查询规格码
            $searchCount = 0;
            foreach ($arrGoodsCode as $strCode) {
                $searchRes = searchArrayGetOne($arrData, $strCode, 'goods_code');
                $searchData = $searchRes['searchRes'];
                $arrData = $searchRes['arrData'];
                if (count($searchData) > 0) {
                    $searchCount ++;
                    break;
                }
            }
            //如果没有搜到数据则做记录
            if ($searchCount === 0) {
                $none_id[] = $key + 1;
                continue;
            }
            //如果要更新的商品重量不为0则做记录并禁止更新重量
            if ($searchData['spec_weight'] != 0) {
                $existWeight[] = $key + 1;
                continue;
            }
            //组装要更新的数据
            $updateGoodsData[] = [
                'spec_sn' => $searchData['spec_sn'],
                'estimate_weight' => floatval($value[2]),
                'spec_weight' => floatval($value[2]),
            ] ;
        }
        //将不存在的商品告知用户
        if(count($none_id) > 0){
            $checkRes = '第' . implode($none_id, ',') . '行的商品在系统中不存在';
            return ['code' => '2067','msg' => $checkRes];
        }
        //将重量不为0的商品告知用户
        if(count($existWeight) > 0){
            $checkRes = '第' . implode($existWeight, ',') . '行的商品重量不为0，请联系技术人员处理';
            return ['code' => '2067','msg' => $checkRes];
        }

        return $updateGoodsData;
    }

    /**
     * desc 通过平台条码获取SKU的基本信息
     * author zhangdong
     * date 2020.03.13
     */
    public function getSpecMsgByCode(array $arrGoodsCode = [])
    {
        //去除数组中空白元素
        $arrGoodsCode = array_filter($arrGoodsCode);
        $field = ['gc.spec_sn', 'gc.goods_code', 'gs.spec_weight'];
        $queryRes = DB::table($this->table)->select($field)
            ->leftJoin('goods_spec as gs', 'gs.spec_sn', 'gc.spec_sn')
            ->whereIn('gc.goods_code', $arrGoodsCode)->get();
        return $queryRes;
    }

    /**
     * description 大批发报价-检查导入数据是否有新品
     * author zhangdong
     * date 2020.03.24
     */
    public function checkExportData($goodsData, $specNoInfo)
    {
        $arrData = objectToArray($specNoInfo);
        $none_id = $searchData = $misGoodsData = [];
        $goodsModel = new GoodsModel();
        foreach($goodsData as $key => $value){
            if ($key == 0) {
                continue;
            }
            //将平台条码组装成数组
            $strGoodsCode = trim($value[1]);
            $arrGoodsCode = $goodsModel->createGoodsCode($strGoodsCode);
            //通过平台条码从系统中查询规格码
            $searchCount = 0;
            foreach ($arrGoodsCode as $strCode) {
                $searchRes = searchArrayGetOne($arrData, $strCode, 'goods_code');
                $searchData = $searchRes['searchRes'];
                if(count($searchData) == 0){
                    continue;
                }
                if (floatval($searchData['spec_weight']) <= 0) {
                    $searchData['spec_weight'] = $searchData['estimate_weight'];
                }
                $arrData = $searchRes['arrData'];
                if (count($searchData) > 0) {
                    $searchCount ++;
                    break;
                }
            }
            //如果没有搜到数据则做记录
            if ($searchCount === 0) {
                $none_id[] = $key + 1;
                continue;
            }
            //组装MIS中存在的商品数据
            $misGoodsData[] = [
                'spec_sn' => $searchData['spec_sn'],
                'platform_no' => trim($value[1]),
                'goods_name' => $searchData['goods_name'],
                'brand_id' => $searchData['brand_id'],
                'spec_price' => $searchData['spec_price'],
                'spec_weight' => $searchData['spec_weight'],
            ] ;
        }
        //将不存在的商品告知用户
        if(count($none_id) > 0){
            $checkRes = '第' . implode($none_id, ',') . '行的商品在系统中不存在';
            return ['code' => '2067','msg' => $checkRes];
        }
        return $misGoodsData;
    }

    /**
     * desc 通过平台条码获取商品的基本信息
     * author zhangdong
     * date 2020.03.24
     */
    public function getGoodsMsgByCode(array $arrGoodsCode = [])
    {
        //去除数组中空白元素
        $arrGoodsCode = array_filter($arrGoodsCode);
        $field = [
            'gc.spec_sn', 'gc.goods_code', 'g.goods_name', 'g.brand_id',
            'gs.spec_price', 'gs.spec_weight', 'gs.estimate_weight',
        ];
        $queryRes = DB::table($this->table)->select($field)
            ->leftJoin('goods_spec as gs', 'gs.spec_sn', 'gc.spec_sn')
            ->leftJoin('goods as g', 'g.goods_sn', 'gs.goods_sn')
            ->whereIn('gc.goods_code', $arrGoodsCode)->get();
        return $queryRes;
    }




}//end of class
