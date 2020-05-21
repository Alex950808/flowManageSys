<?php

namespace App\Console;

use App\Model\Vone\ExchangeRateModel;
use App\Model\Vone\planModel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Modules\Erp\ErpApi;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule)
    {
        //erp库存同步 - 暂时停止执行
//        $schedule->call(function () {
//            $erpModel = new ErpApi();
//            $erpModel->sycGoodsStock();
//        })->everyMinute();
        //erp单仓库库存查询 zhangdong 2019.01.07
        /*$schedule->call(function () {
            $erpModel = new ErpApi();
            $start_time = START_TIME;
            $end_time = END_TIME;
            $erpModel->erpStockQuery($start_time, $end_time);
        })->hourly();*/
        //取消三天内未上传DD单的现货单 zhangdong 2019.01.07
        /*$schedule->call(function () {
            $planModel = new planModel();
            $planModel->planCancelSpotOrder();
        })->daily();*/

        //更新每日汇率 zongxing 2019.08.10 update zhangdong 2019.08.22
        $rateExecTime = '02:00';
        $schedule->call(function () {
            $planModel = new planModel();
            $planModel->updateRate();
        })->dailyAt($rateExecTime);

        //从ERP同步各店铺的库存-开启日期 2020.02.04 zhangdong 2019.12.02
        $schedule->call(function () {
            $planModel = new planModel();
            $planModel->sycSkuStockByShop();
        })->everyFiveMinutes();

        //通过商家编码从ERP同步重量到MIS zhangdong 2020.02.25
        $weightExecTime = '03:00';
        $schedule->call(function () {
            $planModel = new planModel();
            $planModel->updateWeightByErp();
        })->dailyAt($weightExecTime);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }


}//end of class
