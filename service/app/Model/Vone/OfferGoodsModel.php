<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OfferGoodsModel extends Model
{
    public $table = 'offer_goods as og';
    private $field = [
        'og.offer_sn','og.goods_name','og.spec_sn','og.erp_merchant_no','og.spec_price',
        'og.exw_discount','og.sale_discount','og.is_modify','og.platform_barcode'
    ];

    /**
     * description 商品详情-获取报价单商品信息
     * author zhangdong
     * date 2019.11.25
     */
    public function getOfferGoods($offerSn, $reqParams = '')
    {
        //创建筛选条件
        if (!empty($reqParams)) {
            $where = $this->createWhere($reqParams);
        }
        $where[] = ['og.offer_sn', $offerSn];
        $addField = [
            'b.name as brand_name', 'b.brand_id', 'og.goods_name', 'og.spec_price','gs.spec_weight',
            'gs.gold_discount', 'gs.black_discount', 'og.exw_discount','gs.foreign_discount',
            'gs.spec_sn', 'gs.erp_merchant_no', 'og.sale_discount', 'og.is_modify',
            'gs.erp_ref_no','gs.erp_prd_no','gs.is_suit','gs.suit_sn','gs.suit_price',
            'gs.is_search','og.platform_barcode','gs.estimate_weight',
        ];
        $this->field = array_merge($addField, $this->field);
        $goodsBaseInfo = DB::table($this->table)->select($this->field)
            ->leftJoin('goods_spec AS gs', 'gs.spec_sn', '=', 'og.spec_sn')
            ->leftJoin('goods AS g', 'g.goods_sn', '=', 'gs.goods_sn')
            ->leftJoin('brand AS b', 'b.brand_id', '=', 'g.brand_id')
            ->where($where)->get();
        return $goodsBaseInfo;
    }

    /**
     * desc 修改销售折扣
     * author zhangdong
     * date 2019.11.25
     */
    public function updateSkuDiscount($offerSn, $spec_sn, $sale_discount)
    {
        $sale_discount = trim($sale_discount);
        $where = [
            ['spec_sn', $spec_sn],
            ['offer_sn', $offerSn],
        ];
        $update = [
            'sale_discount' => $sale_discount,
            'is_modify' => 1,
        ];
        $updateRes = DB::table($this->table)->where($where)->update($update);
        return $updateRes;

    }

    /**
     * @description 报价单列表-获取商品信息
     * @author zhangdong
     * date 2019.11.26
     */
    public function offerGoods(array $arrOfferSn = [], $goodsWhere = [])
    {
        $where = [];
        //对查询条件进行处理-如果发现有本函数适用的条件则加入筛选
        foreach($goodsWhere as $value){
            $field_name = $value[0];
            $is_exit = strpos($field_name,'og');
            /*此处strpos函数如果查到对应的字符则返回出现的位置，因为mog出现的位置为0
            而0不等于true，所以用false来判断*/
            if($is_exit !== false) {
                $where[] = $value;
            }
        }
        unset($this->field[7]);
        $queryRes = DB::table($this->table)->select($this->field)
            ->whereIn('offer_sn',$arrOfferSn)->where($where)
            ->get();
        return $queryRes;
    }

    /**
     * @description 报价单列表-获取报价导出数据
     * @author zhangdong
     * date 2019.11.26
     */
    public function getExportOfferData($offerSn)
    {
        $where = [
            ['og.offer_sn', $offerSn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->get();
        return $queryRes;
    }

    /**
     * description 校验报价信息上传数据
     * author zhangdong
     * date 2019.11.27
     */
    public function checkImportData($offerSn, $goodsData)
    {
        //通过报价单号查询商品信息
        $goods = $this->getGoods($offerSn);
        $arrData = objectToArray($goods);
        //循环检查导入的数据中各商品是否存在于对应订单中
        $none_id = $updateGoodsData = [];
        foreach($goodsData as $key => $value){
            if ($key == 0) {
                continue;
            }
            $spec_sn = trim($value[1]);
            if (empty($spec_sn)) {
                $checkRes = '部分规格码为空，请检查导入表格';
                return ['code' => '2067','msg' => $checkRes];
            }
            $searchRes = searchArrayGetOne($arrData, $spec_sn, 'spec_sn');
            $searchData = $searchRes['searchRes'];
            $arrData = $searchRes['arrData'];
            if (count($searchData) == 0) {
                $none_id[] = $key + 1;
                continue;
            }
            //对修改数据做对比，筛选出被修改的数据
            $oldSaleDiscount = floatval($searchData['sale_discount']);
            $newSaleDiscount = floatval($value[6]);
            if ($oldSaleDiscount != $newSaleDiscount) {
                $updateGoodsData[$key] = [
                    'spec_sn' => $spec_sn,
                    'sale_discount' => $newSaleDiscount,
                ];
            }
        }
        //将不存在的商品告知用户
        if(count($none_id) > 0){
            $checkRes = '第' . implode($none_id, ',') . '行的商品在报价单中不存在';
            return ['code' => '2067','msg' => $checkRes];
        }
        return $updateGoodsData;
    }

    /**
     * description 获取商品数据
     * author zhangdong
     * date 2019.11.27
     */
    public function getGoods($offerSn)
    {
        $where = [
            ['offer_sn', $offerSn],
        ];
        $queryRes = DB::table($this->table)->select($this->field)->where($where)->get();
        return $queryRes;
    }

    /**
     * description 批量更新销售折扣
     * author zhangdong
     * date 2019.11.27
     */
    public function updateSaleDiscount($offerSn, $correctData)
    {
        $andWhere = ['offer_sn' => $offerSn,];
        $this->table = 'jms_offer_goods';
        $arrSql = makeUpdateSql($this->table, $correctData, $andWhere);
        $updateRes = false;
        if ($arrSql) {
            //开始批量更新
            $strSql = $arrSql['updateSql'];
            $bindData = $arrSql['bindings'];
            $updateRes = $this->executeSql($strSql, $bindData);
        }
        return $updateRes;
    }


   /**
    * description 批量更新语句执行
    * author zhangdong
    * date 2019.11.27
    * @return bool
    */
    private function executeSql($strSql, $bindData)
    {
        $executeRes = DB::update($strSql, $bindData);
        return $executeRes;
    }

    /**
     * description:创建总单详情筛选条件
     * editor:zhangdong
     * date : 2019.01.21
     */
    private function createWhere($reqParams)
    {
        $where = [];
        //规格码
        if (isset($reqParams['spec_sn'])) {
            $where[] = [
                'og.spec_sn',trim($reqParams['spec_sn'])
            ];
        }
        return $where;
    }





}//end of class
