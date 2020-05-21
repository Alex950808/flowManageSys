<?php
//引入日志库文件 add by zhangdong on the 2018.06.28
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use App\Model\Vone\ExchangeRateModel;

/**
 * description:日志记录公共方法
 * editor:zhangdong
 * date : 2018.11.16
 */
function logInfo($logName)
{
    $log = new Logger($logName);
    $strDate = date('Ymd');
    $storePath = storage_path('logs/' . $logName . '_' . $strDate . '.log');
    $stream = new StreamHandler($storePath);
    $log->pushHandler($stream, Logger::INFO);
    return $log;
}

/*
 * description：对二维数组进行搜索，返回搜索到的值-精确搜索
 * author：zhangdong
 * date：2018.10.26
 * @param $arrData 要搜索的数组
 * @param $keyValue 要搜索的键值
 * @param $keyName 要搜索的键名
 * @return array
 */
function searchTwoArray($arrData, $keyValue, $keyName)
{
    $searchRes = [];
    foreach ($arrData as $key => $value) {
        if ($value[$keyName] == $keyValue) $searchRes[] = $arrData[$key];
    }
    return $searchRes;
}

/*
 * description：对二维数组进行排序
 * author：zhangdong
 * date：2018.10.26
 * @param $arrData 要排序的数组
 * @param $sortType 排序方式
 * @param $sortField 排序字段
 * @return mixed
 */
function sortTwoArray($arrData, $sortType, $sortField)
{
    $sort = [
        'direction' => $sortType, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
        'field' => $sortField //排序字段
    ];
    $arrSort = [];
    foreach ($arrData AS $uniqueId => $row) {
        foreach ($row AS $key => $value) {
            $arrSort[$key][$uniqueId] = $value;
        }
    }
    if ($sort['direction']) {
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arrData);
    }
    return $arrData;
}

/**
 * description:计算编号
 * editor:zongxing
 * date : 2018.06.26
 * params: 1.模型对象:$model_obj;2.需要更新的字段名:$model_field;3,拼接字符串头:$pin_head;4.是否带年月日:$status;
 * return Object
 */
function createNo($model_obj, $model_field, $pin_head, $status = true)
{
    $last_purchase_info = $model_obj->orderBy('create_time', 'desc')->first();
    $last_purchase_info = objectToArrayZ($last_purchase_info);
    $last_purchase_sn = $last_purchase_info[$model_field];
    $pin_str = '001';
    if ($last_purchase_sn) {
        $last_three_str = substr($last_purchase_sn, '-3');
        $last_three_str_int = intval($last_three_str);
        $pin_int = $last_three_str_int + 1;
        if ($pin_int >= 100) {
            $pin_str = $pin_int;
        } else if ($pin_int >= 10) {
            $pin_str = '0' . $pin_int;
        } else {
            $pin_str = '00' . $pin_int;
        }
    }
    $now_date = '';
    if ($status) {
        $now_date = str_replace('-', '', date('Y-m-d', time()));
    }
    $return_sn = $pin_head . $now_date . $pin_str;
    return $return_sn;
}

/**
 * description:根据时间计算编号
 * editor:zongxing
 * date : 2018.07.05
 * params: 1.模型对象:$model_obj;2.需要更新的字段名:$model_field;3,拼接字符串头:$pin_head;4.是否检查日期:$status;
 * return Object
 */
function createNoByTime($model_obj, $model_field, $pin_head, $status = true)
{
    $last_purchase_info = $model_obj->orderBy('create_time', 'desc')->first();

    if (empty($last_purchase_info)) {
        $pin_str = '001';
    } else {
        if ($status) {
            $last_purchase_sn = $last_purchase_info["attributes"][$model_field];
            //最后一个时间
            $last_create_time = $last_purchase_info["attributes"]["create_time"];

            $last_day = substr($last_create_time, 0, 10);
            $now_day = date("Y-m-d", time());

            $pin_str = '001';
            if ($last_day == $now_day) {
                if ($last_purchase_sn) {
                    $last_three_str = substr($last_purchase_sn, '-3');
                    $last_three_str_int = intval($last_three_str);
                    $pin_int = $last_three_str_int + 1;
                    if ($pin_int >= 100) {
                        $pin_str = $pin_int;
                    } else if ($pin_int >= 10) {
                        $pin_str = '0' . $pin_int;
                    } else {
                        $pin_str = '00' . $pin_int;
                    }
                }
            }
        }
    }

    $return_sn = $pin_head . $pin_str;
    return $return_sn;
}

/**
 * @description:在二维数组中搜索，返回对应键名
 * @editor:张冬
 * @date : 2018.11.01
 * @param $arrData (数组)
 * @param $columnValue (键值)
 * @param $column (键名)
 * @return int
 */
function twoArraySearch($arrData, $columnValue, $column)
{
    $found_key = array_search($columnValue, array_column($arrData, $column));
    return $found_key;
}

/**
 * @description:组装批量更新sql语句-使用该方法时请事先打印处理结果，确定无误后再执行
 * @param $table (包含前缀的数据表名)
 * @param $multipleData (要更新的字段一一对应的数组)
 * @param $andWhere (并的查询条件)
 * @param $multiply (要做运算的字段 形似 SET a = a - b)
 * @return mixed
 * @author:zhangdong
 * @date : 2018.11.08
 */
function makeUpdateSql($table, $multipleData, $andWhere = [], $multiply = '')
{
    /*
     * @$multipleData 传参举例
     $multipleData = [
        'whereIn' => $whereIn,//要whereIn的字段和值
        'setData_a' => $a,//要set的第一个值
        'setData_b' => $b,//要set的第二个值，可以set同一个表中的多个字段，后面的以此类推
    ];*/
    if (empty($multipleData) || !is_array($multipleData)) {
        return false;
    }
    //如果$multiply参数不为空则必须保证$multipleData[0]中只能有两个元素，否则会导致不需要做运算的
    //字段也参与计算
    if ($multiply != '' && count($multipleData[0]) != 2) {
        return false;
    }
    //输出数组中的当前元素的值
    $firstRow = current($multipleData);
    $updateColumn = array_keys($firstRow);
    // 默认以id为条件更新，如果没有ID则以第一个字段为条件
    $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
    unset($updateColumn[0]);
    // 拼接sql语句
    $updateSql = "UPDATE " . $table . " SET ";
    $sets = [];
    $bindings = [];
    foreach ($updateColumn as $uColumn) {
        $setSql = "`" . $uColumn . "` = CASE ";
        if ($multiply != '') {
            $setSql = "`" . $uColumn . "` = `$multiply` - CASE ";
        }
        foreach ($multipleData as $data) {
            $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
            $bindings[] = $data[$referenceColumn];
            $bindings[] = $data[$uColumn];
        }
        $setSql .= "ELSE `" . $uColumn . "` END ";
        $sets[] = $setSql;
    }
    $updateSql .= implode(', ', $sets);
    $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
    $bindings = array_merge($bindings, $whereIn);
    $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
    $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
    if (!empty($andWhere)) {
        foreach ($andWhere as $key => $item) {
            $updateSql .= ' AND `' . $key . '` = ?';
            $bindings[] = $item;
        }
    }
    // 传入预处理sql语句和对应绑定数据
    return [
        'updateSql' => $updateSql,
        'bindings' => $bindings,
    ];
} //end of makeUpdateSql


/**
 * description:对象转为数组
 * editor:zhangdong
 * date : 2018.11.19
 */
function objectToArray($objectData)
{
    $arrData = [];
    foreach ($objectData as $key => $value) {
        $arrData[] = ((array)$value);
    }
    return $arrData;
}

/**
 * description:对象转为数组
 * editor:zongxing
 * date : 2018.11.20
 */
function objectToArrayZ($objectData)
{
    $arrData = json_decode(json_encode($objectData), true);
    return $arrData;
}

/**
 * description:实时汇率查询接口
 * editor:zongxing
 * * params: 1.$from:需要转换的货币简称;2.$to:转换后的货币简称;3.$amount:金额;
 * date : 2018.11.23
 */
function convertCurrency($from, $to)
{
    $rate_model = new ExchangeRateModel();
    $param['day_time'] = Date('Y-m-d', time());
    $rate_arr = $rate_model->exchangeRateList($param);
    $rate_arr = $rate_arr[0];

    $key_str = strtolower($from) . '_' . strtolower($to) . '_rate';
    $rate = 0;
    if (isset($rate_arr[$key_str])) {
        $rate = number_format($rate_arr[$key_str]);
    }
    return $rate;
}


/**
 * description:根据日期和随机字符串生成编号
 * editor:zongxing
 * date : 2018.12.15
 * params: 1.拼接字符串头:$pin_str;
 * return Object
 */
function makeRandNumber($pin_str)
{
    $date_time = Date('Ymd', time());
    $rand_num = rand(1000, 9999);
    $rand_number = $pin_str . $date_time . $rand_num;
    return $rand_number;
}

/*
 * description:异步返回函数-返回的是json
 * editor:zhangdong
 * date : 2018.12.15
 */
function jsonReturn($returnMsg)
{
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($returnMsg, JSON_UNESCAPED_UNICODE));
}


/**
 * description:组装批量更新sql
 * editor:zongxing
 * date : 2018.12.15
 * params: 1.需要更新的表:$table;2.需要更新的数据:$batchData;3.需要判断的字段:$column;4.更新条件:$where;5.额外条件:$other_option
 * return Object
 */
function makeBatchUpdateSql($table, $batchData, $column, $where = [], $other_option = [])
{
    if (empty($batchData) || !is_array($batchData)) {
        return false;
    }
    $batch_sql = 'UPDATE ' . $table . ' SET ';
    $total_spec_sn = [];
    foreach ($batchData as $k => $v) {
        $batch_sql .= $k . ' = CASE ' . $column;
        foreach ($v as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if (!in_array($k2, $total_spec_sn)) {
                    $total_spec_sn [] = $k2;
                }
                $batch_sql .= ' WHEN \'' . $k2 . '\' THEN ' . $v2;
            }
        }
        $batch_sql .= ' END,';
    }
    $batch_sql = substr($batch_sql, 0, -1);
    $total_spec_sn = implode('\',\'', array_values($total_spec_sn));
    $batch_sql .= ' WHERE ' . $column . ' IN (\'' . $total_spec_sn . '\')';
    if ($other_option) {
        $other_column = $other_option['column'];
        $total_other_sn = implode('\',\'', array_values($other_option['data']));
        $batch_sql .= ' AND ' . $other_column . ' IN (\'' . $total_other_sn . '\')';
    }
    if ($where) {
        foreach ($where as $k => $v) {
            $batch_sql .= ' AND ' . $k . ' = \'' . $v . '\'';
        }
    }
    return $batch_sql;
}

/**
 * description:计算美金报价 = 美金原价 * 销售折扣
 * editor:zhangdong
 * date : 2018.12.17
 * @param $spec_price (美金原价)
 * @param $sale_discount (销售折扣)
 * @return double
 */
function calculateUserPrice($spec_price, $sale_discount)
{
    //计算美金报价 = 美金原价 * 销售折扣
    $userPrice = trim($spec_price) * trim($sale_discount);
    //将结果保留两位小数
    $userPrice = round($userPrice, DECIMAL_DIGIT);
    return $userPrice;
}


/**
 * description:二维数组模糊搜索
 * editor:zhangdong
 * date : 2019.01.17
 * notice:该函数是否使用请根据测试结果来定
 */
function twoArrayFuzzySearch($arrData, $field, $keywords)
{
    $result = [];
    if (empty($keywords)) {
        return [];
    }
    foreach ($arrData as $key => $v) {

        $searchData = $v[$field];
        if (strstr($searchData, $keywords) !== false) {
            $result[] = $arrData[$key];
        }
    }
    return $result;
}

/**
 * description：将某个小数转化成百分数
 * editor:zhangdong
 * date : 2019.01.23
 */
function toPercent($floatData)
{
    $percentData = sprintf('%.2f%%', $floatData * 100);
    return $percentData;
}

/**
 * description：筛选数组中重复的值并返回不重复的一维数组（只返回重复数据的值）
 * editor:zhangdong
 * date : 2019.01.29
 */
function filter_duplicate($array, $filter_field)
{
    //将有可能是对象的数据转为数组
    $arrayData = objectToArray($array);
    $result = [];
    $duplicateData = [];
    foreach ($arrayData as $key => $value) {
        if (empty($value[$filter_field])) {
            continue;
        }
        $has = false;
        foreach ($result as $val) {
            if ($val[$filter_field] == $value[$filter_field]) {
                $has = true;
                $duplicateData[] = $value[$filter_field];
                break;
            }
        }
        if (!$has) {
            $result[] = $value;
        }
    }
    return $duplicateData;
}

/**
 * description：将二维数组中的某个键组装成数组形式并返回
 * author:zhangdong
 * date : 2019.03.12
 * return array
 */
function makeArray($arrData, $keyName)
{
    //判断$arrData是否为数组
    if (!is_array($arrData) || count($arrData) == 0) {
        return [];
    }
    $arrKeyValue = [];
    foreach ($arrData as $value) {
        $strKeyValue = $value[$keyName];
        $arrKeyValue[] = $strKeyValue;
    }
    return $arrKeyValue;

}

/**
 * description:改变表格标题样式
 * editor:zongxing
 * date : 2018.06.28
 * params: 1.excel对象:$obj_excel;2.最后一列的名称:$column_last_name;
 * return Object
 */
function changeTableTitle($obj_excel, $column_first_name, $row_first_i, $column_last_name, $row_last_i)
{
    //标题居中+加粗
    $obj_excel->getActiveSheet()->getStyle($column_first_name . $row_first_i . ":" . $column_last_name . $row_last_i)
        ->applyFromArray(
            array(
                'font' => array(
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            )
        );
}

/**
 * description:改变表格内容样式
 * editor:zongxing
 * date : 2018.06.28
 * params: 1.excel对象:$obj_excel;2.最后一列的名称:$column_last_name;3.最大行号:$row_end;
 * return Object
 */

function changeTableContent($obj_excel, $column_first_name, $row_first_i, $column_last_name, $row_last_i)
{
    //内容只居中
    $obj_excel->getActiveSheet()->getStyle($column_first_name . $row_first_i . ":" . $column_last_name . $row_last_i)->applyFromArray(
        array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        )
    );
}

/**
 * description：截取字符串
 * author:zhangdong
 * date : 2019.03.23
 * params : $value 截取的字符串, $start 开始位置, $posStr 搜索字符, $skew 偏移量
 * return bool/string
 */
function cutString($value, $start, $posStr, $skew = 1)
{
    $operateRes = substr($value, $start, strrpos($value, $posStr) - $skew);
    return $operateRes;
}

/**
 * description：获取接口名称
 * author:zhangdong
 * date : 2019.04.02
 * return string
 */
function getApiName()
{
    $redirectUrl = $_SERVER['REQUEST_URI'];
    $startNum = strrpos($redirectUrl, '/') + 1;
    $endNum = strpos($redirectUrl, '?');
    //post请求方式没有'?'
    if ($endNum === false) {
        return substr($redirectUrl, $startNum);
    }
    $difNum = $endNum - $startNum;
    return substr($redirectUrl, $startNum, $difNum);
}


/**
 * description：从一维数组中获取重复的数据
 * author:zhangdong
 * date : 2019.05.06
 * return string
 */
function fetchRepeatMemberInArray(array $array = [])
{
    // 获取去掉重复数据的数组
    $unique_arr = array_unique($array);
    // 获取重复数据的数组
    $repeat_arr = array_diff_assoc($array, $unique_arr);
    return $repeat_arr;
}

/**
 * description：从二维数组中组装指定的字段为一维数组并返回
 * author:zhangdong
 * date : 2019.05.21
 * return array
 */
function getFieldArrayVaule(array $arrayData = [], $fieldName)
{
    $arrFieldValue = [];
    foreach ($arrayData as $value) {
        $fieldValue = trim($value[$fieldName]);
        $arrFieldValue[] = $fieldValue;
    }
    return array_unique(array_filter($arrFieldValue));
}


/**
 * description：获取去除别名的数据表名
 * author:zhangdong
 * date : 2019.05.30
 * params : $tableAsName 带有别名的数据表名 类似 table as t
 * return bool/string
 */
function getTableName($tableAsName)
{
    $tableName = substr($tableAsName, 0, strrpos($tableAsName, 'as') - 1);
    return $tableName;
}

/**
 * description：更新二维数组中某个键的值
 * author:zhangdong
 * date : 2019.06.01
 */
function updateTwoArrayValue($arrData, $searchKey, $searchKeyValue, $updateKey, $updateValue)
{
    foreach ($arrData as $key => $value) {
        //如果搜索键的值和搜索值相等则更新要更新键的值
        if ($value[$searchKey] == $searchKeyValue) {
            $arrData[$key][$updateKey] = $updateValue;
            //如果更新成功则直接结束循环（仅更新一条数据）
            break;
        }
    }
    return $arrData;

}

/**
 * description：对字符串按要求进行处理使其规范化
 * editor:zhangdong
 * date : 2019.06.28
 * @return string
 */
function ruleStr($str)
{
    //去空格
    $str = str_replace(' ', '', $str);
    //将中文逗号转为英文逗号
    $str = str_replace('，', ',', $str);
    return $str;
}

/**
 * description 聚合汇率请求方法
 * author zongxing
 * @param string $url [请求的URL地址]
 * @param string $params [请求的参数]
 * @param int $ipost [是否采用POST形式]
 * @return  string
 */
function rateCurl($url, $params = false, $ispost = 0)
{
    $httpInfo = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === false) {
        return $response;
    }
//    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}


/*
 * description：对二维数组进行搜索，返回搜索到的值-精确搜索，
 * 搜到值后将该条记录从原数据中删除
 * author：zhangdong
 * date：2019.10.31
 * @param $arrData 要搜索的数组
 * @param $keyValue 要搜索的键值
 * @param $keyName 要搜索的键名
 * @return array
 */
function searchArray($arrData, $keyValue, $keyName)
{
    $searchRes = [];
    foreach ($arrData as $key => $value) {
        if ($value[$keyName] == $keyValue) {
            $searchRes[] = $arrData[$key];
            unset($arrData[$key]);
        }
    }
    return [
        'searchRes' => $searchRes,
        'arrData' => $arrData,
    ];
}


/*
 * description：对二维数组进行搜索，返回搜索到的值-精确搜索，
 * 搜到值后将该条记录从原数据中删除并且直接跳出循环（只搜一条数据），
 * 注意区分 searchArray和searchTwoArray函数
 * author：zhangdong
 * date：2019.11.27
 * @param $arrData 要搜索的数组
 * @param $keyValue 要搜索的键值
 * @param $keyName 要搜索的键名
 * @return array
 */
function searchArrayGetOne($arrData, $keyValue, $keyName)
{
    $searchRes = [];
    foreach ($arrData as $key => $value) {
        if ($value[$keyName] == $keyValue) {
            $searchRes = $arrData[$key];
            unset($arrData[$key]);
            break;
        }
    }
    return [
        'searchRes' => $searchRes,
        'arrData' => $arrData,
    ];
}

/**
 * 向关联数组指定位置前插入元素
 * author zongxing
 * Date 2019.12.12
 * @param $array 原数组
 * @param $insert_key 插入键名
 * @param $data 需要插入的元素
 * @param $direct_key 指定键名
 * @return mixed
 */
function direct_array_push($array, $insert_key, $data, $direct_key)
{
    $offset = array_search($direct_key, array_keys($array)) ? array_search($direct_key, array_keys($array)) : false;
    if ($offset) {
        // 找到指定 $key，插入元素
        $tmp_arr = $array[$direct_key];
        unset($array[$direct_key]);
        $array[$insert_key] = $data;
        $array[$direct_key] = $tmp_arr;
    } else {
        // 没指定 $key 或者找不到，就直接加到末尾
        $array[$insert_key] = $data;
    }
    return $array;
}

/*
 * description 客户端断开连接，继续执行脚本
 * notice 适用于没有fastcgi_finish_request函数的场景
 * author zhangdong
 * date 2019.12.20
 */
function connection_close()
{
    //如果报文已发送则直接返回true；
    if (headers_sent()) {
        return;
    }
    //设置请求永不过期
    set_time_limit(0);
    //客户端断开连接时继续执行脚本
    ignore_user_abort(true);
    //发送报文，并告诉浏览器关闭连接
    header('Content-Length: ' . ob_get_length());
    header('Connection: close');
    //如果缓冲区有内容则输出（php.ini中会默认开启一块缓冲区，所以ob_get_level一定是>=1）
    if (ob_get_level() > 0) {
        //输出缓冲区内容并将缓冲区内容删除
        ob_end_flush();
    }
    flush();
}

/*
 * description 输出json格式的数据-适用于请求结果提前响应，一般和connection_close函数搭配使用
 * author zhangdong
 * date 2019.12.20
 */
function jsonEcho($msg)
{
    header('Content-Type:application/json; charset=utf-8');
    echo(json_encode($msg, JSON_UNESCAPED_UNICODE));
}

/*
 * description 不依赖词典的中英文分词器
 * author zongxing
 * date 2019.01.02
 */
function split_str($str)
{
    preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $str, $match);
    $all_info = implode('', $match[0]);
    preg_match_all("/./u", $all_info, $match);
    $arr = [];
    $all_info = $match[0];
    $size = count($all_info);
    for ($i = 0; $i <= $size - 2; $i++) {
        $word = '';
        for ($j = 0; $j < 2; $j++) {
            $word .= $all_info[$i + $j];
        }
        $arr[] = $word;
    }
    return array_unique($arr);
}


/*
 * description 找两个字符串相同的部分
 * author zongxing
 * date 2019.01.02
 */
function find_common_str($str1, $str2)
{
    $encoding = 'utf8';
    //将字符串转成数组
    preg_match_all("/./u", $str1, $arr1);
    preg_match_all("/./u", $str2, $arr2);
    $arr1 = $arr1[0];
    $arr2 = $arr2[0];
    //计算字符串的长度
    $len1 = count($arr1);
    $len2 = count($arr2);
    //初始化相同字符串的长度
    $len = 0;
    //初始化相同字符串的起始位置
    $pos = -1;
    for ($i = 0; $i < $len1; $i++) {
        for ($j = 0; $j < $len2; $j++) {
            //找到首个相同的字符
            if ($arr1[$i] == $arr2[$j]) {
                //判断后面的字符是否相同
                for ($p = 0; (($i + $p) < $len1) &&
                (($j + $p) < $len2) &&
                ($arr1[$i + $p] == $arr2[$j + $p]) &&
                ($arr1[$i + $p] <> ''); $p++) ;
                if ($p > $len) {
                    $pos = $i;
                    $len = $p;
                }
            }
        }
    }
    if ($pos == -1) {
        return;
    } else {
        return mb_substr($str1, $pos, $len);
    }
}


/*
 * description 过滤特殊字符
 * author zongxing
 * date 2019.01.02
 */
function replace_specialChar($strParam)
{
    $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\·|\/|\;|\'|\`|\-|\=|\\\|\|/";
    return preg_replace($regex, "", $strParam);
}


/*
 * description 过滤商品单位
 * author zongxing
 * date 2019.01.02
 */
function replace_unitChar($strParam)
{
    $filter_field = ['千克', '克', 'kg', 'g', '升', '毫升', 'l', 'ml'];
    foreach ($filter_field as $k => $v) {
        $strParam = str_replace($v, '', $strParam);
    }
    return $strParam;
}

/*
 * description 进行搜索字段拆分
 * author zongxing
 * date 2020.03.03
 */
function reformKeywords($str)
{
    preg_match_all('%[0-9_-]{2,}%', $str, $number_arr);
    preg_match_all('%[A-Za-z_-]{1,}%', $str, $str_arr);
    preg_match_all('%[\x{4e00}-\x{9fa5}]%u', $str, $gbk_arr);
    $gbk_str = implode("", $gbk_arr[0]);
    $gbk_arr = split_str($gbk_str);
    $returnStr = [$number_arr[0], $str_arr[0], $gbk_arr];
    return $returnStr;
}


/**
 * 折扣计算
 * @param $add_type 折扣计算方式
 * @param $old_disount 旧值
 * @param $new_discount 新值
 * @return int 最终折扣
 */
function discountModify($add_type, $old_disount, $new_discount)
{
    $final_disocount = 0;
    if ($add_type == 1) {//折扣替换
        $final_disocount = $new_discount > $old_disount ? $old_disount : $new_discount;
    } elseif ($add_type == 2) {//折扣累减
        $final_disocount = $old_disount - $new_discount;
    } elseif ($add_type == 3) {//折扣取反（例如：高价sku返点）
        $tmp_discount = 1 - $new_discount;
        $final_disocount = $tmp_discount > $old_disount ? $old_disount : $tmp_discount;
    } elseif ($add_type == 4) {//折扣累加
        $final_disocount = $old_disount + $new_discount;
    }
    return $final_disocount;
}

/**
 * 将字符串分割为数组
 * @param $string 需要分割的字符串
 * @param int $len 分割长度
 * @return array 分割的数组
 */
function splitStr($string, $len = 20)
{
    $start = 0;
    $strlen = mb_strlen($string);
    while ($strlen) {
        $array[] = mb_substr($string, $start, $len);
        $string = mb_substr($string, $len, $strlen);
        $strlen = mb_strlen($string);
    }
    return $array;
}

/*
 * description 复制图片到新目录
 * author zhangdong
 * date 2020.03.20
 */
function copyFile($sourcefile, $dir, $filename)
{
    if (!file_exists($sourcefile)) {
        return false;
    }
    return copy($sourcefile, $dir . '' . $filename);
}


/*
 * description 下载远程图片保存到本地-单图片
 * 当保存文件名称为空时则使用远程文件原来的名称
 * params $url 图片地址
 * author zhangdong
 * date 2020.04.15
 */
function saveImage($url, $save_dir = '', $filename = '', $type = 0)
{
    if (trim($url) == '') {
        return ['code' => 1, 'error' => '文件url不合法'];
    }
    if (trim($save_dir) == '') {
        $save_dir = './';
    }
    if (trim($filename) == '') {//保存文件名
        $ext = strrchr($url, '.');
        if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png') {
            return ['code' => 3, 'error' => '文件类型不合法'];
        }
        $filename = substr($url, strripos($url, '/') + 1);
    }
    if (0 !== strrpos($save_dir, '/')) {
        $save_dir .= '/';
    }
    //创建保存目录
    if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return ['code' => 5, 'error' => '创建文件失败'];
    }
    //获取远程文件所采用的方法
    if ($type) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        curl_close($ch);
    } else {
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
    }
    //文件大小
    $fp2 = @fopen($save_dir . $filename, 'a');
    $saveRes = fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
    if ($saveRes === false) {
        return false;
    }
    return true;
}

/**
 * 上传图片
 * author zongxing
 * date 2020/5/12 0012
 * @param $file 上传文件
 * @param $disk 磁盘
 * @param $save_path 保存路径
 * @return array
 */
function uploadImg($file, $disk, $save_path)
{
    // 1.是否上传成功
    if (!$file->isValid()) {
        return ['code' => '1101', 'msg' => '传输失败'];
    }
    // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
    $fileExtension = $file->getClientOriginalExtension();
    if (!in_array($fileExtension, ['png', 'jpg', 'gif'])) {
        return ['code' => '1102', 'msg' => '文件格式错误'];
    }
    // 3.判断大小是否符合 2M
    $tmpFile = $file->getRealPath();
    if (filesize($tmpFile) >= 2048000) {
        return ['code' => '1103', 'msg' => '文件超过不能超过2M'];
    }
    // 4.是否是通过http请求表单提交的文件
    if (!is_uploaded_file($tmpFile)) {
        return ['code' => '1104', 'msg' => '文件错误'];
    }
    // 5.存储图片, 生成一个随机文件名
    $fileName = $save_path . '.' . $fileExtension;
    if (Storage::disk($disk)->put($fileName, file_get_contents($tmpFile))) {
        $fileName = 'image/' . $disk . '/' . $fileName;
        return ['code' => '1100', 'msg' => '上传成功', 'path' => $fileName];
    } else {
        return ['code' => '1105', 'msg' => '上传失败'];
    }
}

/*
 * desc 对二维数组通过键名去重
 * params $delField 要删除的字段
 * author zhangdong
 * date 2020.05.12
 */
function arrAssocUnique($arr, $keyName, $delField = '')
{
    $tmp_arr = [];
    foreach ($arr as $k => $v) {
        if (!empty($delField)) {
            unset($arr[$k][$delField]);
        }
        //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
        if (in_array($v[$keyName], $tmp_arr)) {
            unset($arr[$k]);
        } else {
            $tmp_arr[] = $v[$keyName];
        }
    }
    return $arr;
}