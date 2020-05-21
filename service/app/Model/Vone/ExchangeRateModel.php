<?php

namespace App\Model\Vone;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExchangeRateModel extends Model
{
    protected $table = 'exchange_rate as er';

    protected $field = [
        'er.id', 'er.day_time', 'er.usd_cny_rate', 'er.usd_krw_rate', 'er.krw_cny_rate'
    ];

    /**
     * 查汇率列表
     * editor:zongxing
     * date : 2019.07.23
     * @param array $param_info
     * $param_info = [
     *      'day_time'=> '2020-03-30' //查询具体时间
     *      'start_time'=> '2020-03-30' //查询范围开始时间
     *      'end_time'=> '2020-03-30' //查询范围结束时间
     * ];
     * @param int $is_page //是否分页，默认不分页
     * @return mixed
     */
    public function exchangeRateList($param_info = [], $is_page = 0)
    {
        //检查当天汇率
        $day_time = date('Y-m-d');
        if (isset($param_info['day_time'])) {
            $day_time = trim($param_info['day_time']);
        }
        $er_info = DB::table($this->table)->where('day_time', $day_time)->first();
        if (empty($er_info)) {
            $this->exchangeRateSave($day_time);
        }
        //获取汇率列表
        $field = $this->field;
        $er_obj = DB::table($this->table)->select($field);
        if (isset($param_info['start_time'])) {
            $start_time = trim($param_info['start_time']);
            $er_obj->where('day_time', '>=', $start_time);
        }
        if (isset($param_info['end_time'])) {
            $end_time = trim($param_info['end_time']);
            $er_obj->where('day_time', '<=', $end_time);
        }
        if (isset($param_info['day_time'])) {
            $day_time = trim($param_info['day_time']);
            $er_obj->where('day_time', '=', $day_time);
        }
        if (!isset($param_info['start_time']) && !isset($param_info['end_time']) && !isset($param_info['day_time'])) {
            $start_time = Carbon::now()->firstOfMonth()->toDateTimeString();
            $end_time = Carbon::now()->endOfMonth()->toDateTimeString();
            $er_obj->where(function ($query) use ($start_time, $end_time) {
                $query->where('er.day_time', '>=', $start_time);
                $query->where('er.day_time', '<=', $end_time);
            });
        }
        if ($is_page) {
            $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
            $er_list = $er_obj->orderBy('create_time','DESC')->paginate($page_size);
        } else {
            $er_list = $er_obj->get();
        }
        $er_list = objectToArrayZ($er_list);
        return $er_list;
    }

    /**
     * description:上传汇率
     * editor:zongxing
     * date : 2019.07.23
     * return Array
     */
    public function uploadExchangeRate($res)
    {
        $day_time = [];
        foreach ($res as $k => $v) {
            if ($k == 0) continue;
            if (!in_array($v[0], $day_time)) {
                $day_time[] = trim($v[0]);
            }
        }
        $er_info = DB::table($this->table)->whereIn('day_time', $day_time)
            ->pluck('id', 'day_time');
        $er_info = objectToArrayZ($er_info);
        $exchange_rate = [];
        $updateExchangeRate = [];
        foreach ($res as $k => $v) {
            if ($k == 0) continue;
            $day_time = trim($v[0]);
            $usd_cny_rate = floatval($v[1]);
            $usd_krw_rate = floatval($v[2]);
            $krw_cny_rate = floatval($v[3]);

            if (isset($er_info[$day_time])) {
                $id = $er_info[$day_time];
                $updateExchangeRate['usd_cny_rate'][] = [
                    $id => $usd_cny_rate
                ];
                $updateExchangeRate['usd_krw_rate'][] = [
                    $id => $usd_krw_rate
                ];
                $updateExchangeRate['krw_cny_rate'][] = [
                    $id => $krw_cny_rate
                ];
            } else {
                $exchange_rate[] = [
                    'day_time' => $day_time,
                    'usd_cny_rate' => $usd_cny_rate,
                    'usd_krw_rate' => $usd_krw_rate,
                    'krw_cny_rate' => $krw_cny_rate,
                ];
            }
        }
        $updateExchangeRateSql = '';
        if (!empty($updateExchangeRate)) {
            $column = 'id';
            $updateExchangeRateSql = makeBatchUpdateSql('jms_exchange_rate',
                $updateExchangeRate, $column);
        }

        $final_res = DB::transaction(function () use ($exchange_rate, $updateExchangeRateSql) {
            if (!empty($updateExchangeRateSql)) {
                $res = DB::update(DB::raw($updateExchangeRateSql));
            }
            if (!empty($exchange_rate)) {
                $res = DB::table('exchange_rate')->insert($exchange_rate);
            }
            return $res;
        });
        return $final_res;
    }

    /**
     * description:汇率实时查询、储存
     * editor:zongxing
     * date : 2019.08.09
     * return Array
     */
    public function exchangeRateSave($day_time)
    {
        $currency_arr = [
            ['USD', 'CNY'],
            ['USD', 'KRW'],
            ['KRW', 'CNY'],
        ];
        $appkey = getenv('EXCHANGE_RATE_KEY');
        $url = 'http://op.juhe.cn/onebox/exchange/currency';
        $total_rate['day_time'] = $day_time;
        foreach ($currency_arr as $k => $v) {
            $params = array(
                'from' => $v[0],//转换汇率前的货币代码
                'to' => $v[1],//转换汇率成的货币代码
                'key' => $appkey,//应用APPKEY(应用详细页查询)
            );
            $paramstring = http_build_query($params);
            $content = rateCurl($url, $paramstring);
            $result = json_decode($content, true);
            if ($result) {
                if ($result['error_code'] == '0') {
                    $pin_str = strtolower($v[0]) . '_' . strtolower($v[1]) . '_rate';
                    $rate_info = $result['result'][0]['result'];
                    $total_rate[$pin_str] = $rate_info;
                } else {
                    $error_info = $result['error_code'] . ":" . $result['reason'];
                    Log::info($error_info);
                }
            }
        }
        DB::table('exchange_rate')->insert($total_rate);
    }//end

    /**
     * desc 查询当天美金兑人民币汇率
     * author zhangdong
     * date 2020.04.01
     */
    public function getUsdCnyRate()
    {
        $dateTime = date('Y-m-d');
        $where = [
            ['day_time',$dateTime]
        ];
        $fields = ['usd_cny_rate'];
        $queryRes = DB::table($this->table)->select($fields)->where($where)->first();
        $usdCnyRate = isset($queryRes->usd_cny_rate) ? $queryRes->usd_cny_rate : 0;
        return $usdCnyRate;
    }



}//end class
