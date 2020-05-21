<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\Offer\CartOffer;


//队列执行类 zhangdong 2019.12.17
//注意  队列中的相关代码如果做了修改则必须重启队列修改的代码才会生效
class QueueExecute
{
    use DispatchesJobs;

    /**
     * description 生成购物车报价数据
     * author zhangdong
     * date 2019.12.17
     */
    public function cartOffer($reqParams)
    {
        $job = (new CartOffer($reqParams))->onQueue('offer');
        $this->dispatch($job);
    }



}//end of class
