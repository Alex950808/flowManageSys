<?php
namespace App\Api\Vone\Controllers;

use App\Api\Vone\Transformers\TestsTransformer;
use App\Mail\GoodsInfo;
use App\Model\Vone\AdminUserModel;
use App\Model\Vone\DiscountTypeRecordModel;
use App\Model\Vone\GmcDiscountModel;
use App\Model\Vone\GoodsCodeModel;
use App\Model\Vone\GoodsModel;
use App\Model\Vone\planModel;
use App\Model\Vone\RealPurchaseModel;
use App\Model\Vone\SaleUserAccountModel;
use App\Model\Vone\SortDataModel;
use App\Model\Vone\UserSortGoodsModel;
use App\Model\Vone\VersionLogModel;
use App\Model\Vone\WholesaleGoodsModel;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use App\Modules\Erp\ErpApi;
use App\Model\Vone\GoodsSpecModel;
use App\Model\Vone\MisOrderSubModel;
use App\Model\Vone\MisOrderGoodsModel;
use App\Model\Vone\SpotGoodsModel;
use App\Model\Vone\DemandGoodsModel;
use App\Model\Vone\MisOrderSubGoodsModel;
use App\Model\Vone\ConversionStatisticsModel;
use App\Model\Vone\OrderInfoModel;
use App\Model\Vone\GoodsIntegralModel;
use App\Model\Vone\DepartSortGoodsModel;
use App\Model\Vone\SpotOrderModel;
use App\Model\Vone\MisOrderModel;
use App\Model\Vone\PurchaseChannelModel;
use App\Model\Vone\OrdNewGoodsModel;
use App\Model\Vone\DemandModel;
use App\Model\Design\SingleModel;
use App\Model\Vone\ExchangeRateModel;
use App\Model\Vone\SubPurchaseModel;
use App\Model\Vone\DiscountTypeInfoModel;
use App\Model\Vone\ClassifyFieldModel;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;



use Symfony\Component\HttpFoundation\Response;
use think\Queue;
use App\Modules\Excel\ExcuteExcel;
use App\Jobs\QueueExecute;
use SwooleTW\Http\Server\Application;
use SwooleTW\Http\Tests\TestCase;

use App\Modules\ParamsSet;
//create by zhangdong on the 2018.06.22
class TestsController extends BaseController
{
    public function index()
    {
        $discTypeInfo = (new ClassifyFieldModel())->getCatByAid();
        dd($discTypeInfo);
    }





}//end of class
