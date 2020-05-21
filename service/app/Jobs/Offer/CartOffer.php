<?php

namespace App\Jobs\offer;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\Excel\ExcuteExcel;

//引入日志库文件 zhangdong 2019.09.25
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use Illuminate\Support\Facades\Mail;
use App\Mail\GoodsInfo;
use App\Model\Vone\GoodsSpecModel;
use App\Model\Vone\ShopCartModel;
use App\Modules\ParamsSet;

//生成购物车报价数据 2019-12-17
class CartOffer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 任务最大尝试次数
    public $tries = 3;
    //任务运行的超时时间-秒。
    public $timeout = 180;
    //请求参数
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * description 运行任务
     * author zhangdong
     * date 2019.11.27
     * @return void
     */
    public function handle()
    {
        $params = $this->params;
        $arrSpecSn = (new ShopCartModel())->getCartSku($params['generateDate']);
        $generateDate = $this->params['generateDate'];
        //生成数据所需要的参数要在这里赋值，如果在控制器中赋值则在队列执行时参数会丢失
        ParamsSet::setChannelId(intval($params['channels_id']));
        ParamsSet::setKoreanRate(floatval($params['koreanRate']));
        ParamsSet::setRmbKoreanRate(floatval($params['rmbKoreanRate']));
        $baseOfferData = (new GoodsSpecModel())->goodsOfferData($arrSpecSn, $generateDate);
        $execute = new ExcuteExcel();
        $res = $execute->storeCartOffer($baseOfferData);
        /*if ($res === true) {
            Mail::to('495997793@qq.com')->send(new GoodsInfo());
        }*/

    }


    /**
     * 要处理的失败任务。
     *
     * @return void
     */
    public function failed()
    {
        // 给用户发送失败通知，等等...
    }










}//end of class
