<?php
namespace App\Api\Vone\Controllers\Sell;

use App\Api\Vone\Controllers\BaseController;
use Dingo\Api\Http\Request;

//引入商品模型 add by zhangdong on the 2018.07.05
use App\Model\Vone\GoodsModel;

//create by zhangdong on the 2018.06.22
class GoodSaleController extends BaseController
{
   /**
     * description : 销售数据管理-商品实时动销率
     * editor : zhangdong
     * date : 2018.07.05
     */
    public function goodsRtMovePercent(Request $request)
    {
        $param_info = $request -> toArray();
        //获取商品实时动销率数据
        $goodsModel = new GoodsModel();
        $goodsMovePer = $goodsModel -> createGoodsMovePer($param_info);

        $return_info = ['code' => '1002', 'msg' => '暂无商品'];
        if ($goodsMovePer !== false) {
            $return_info = ['code' => '1000', 'msg' => '获取商品动销率列表成功', 'data' => $goodsMovePer];
        }
        return response() ->json($return_info);

    }



}