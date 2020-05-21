<?php
namespace App\Api\Client\Controllers;
use App\Model\Design\SingleModel;
use App\Jobs\ProcessDelay;


use JWTAuth;
use think\Queue;

class TestsController extends BaseController
{

    public function show()
    {
        echo 'showv2';

    }



}//end of class
