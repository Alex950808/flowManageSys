<?php
namespace App\Modules;

/**
 * Class ParamsSet
 * description 统一参数设置文件
 * notice 1 final_本类不可被继承，不可被重写
 *        2 本类中不可写入数据量比较大的变量
 *        3 考虑到代码可读性，当函数的某个参数需要传递三次以上才会被用到时该参数可写入本类
 *        4 一个函数需要许多参数建议使用数组传递
 *        5 本类会降低代码可读性，使用时请考虑清楚
 * author zhangdong
 * date 2019.12.12
 * @package App\Modules
 */
final class ParamsSet
{
    //渠道ID add zhangdong 2019-12-12
    private static $channel_id;

    //生成日期 add zhangdong 2019-12-17
    private static $generateDate;

    //乐天官网美金/韩币汇率 add zhangdong 2019-12-17
    private static $koreanRate;

    //人民币兑换韩币汇率 add zhangdong 2019-12-17
    private static $rmbKoreanRate;

    //渠道低价skuID add zhangdong 2019-12-18
    private static $lowPriceId;

    /**
     * author zhangdong
     * date 2019-12-12 11:40:01
     * @return mixed
     */
    public static function getChannelId()
    {
        return self::$channel_id;
    }

    /**
     * author zhangdong
     * date 2019-12-12
     * @return mixed
     */
    public static function setChannelId($channel_id)
    {
        self::$channel_id = $channel_id;
    }

    /**
     * author zhangdong
     * date 2019-12-17
     * @return mixed
     */
    public static function getGenerateDate()
    {
        return self::$generateDate;
    }

    /**
     * author zhangdong
     * date 2019-12-17
     * @return mixed
     */
    public static function setGenerateDate($generateDate)
    {
        self::$generateDate = $generateDate;
    }

    /**
     * author zhangdong
     * date 2019-12-17
     * @return mixed
     */
    public static function getKoreanRate()
    {
        return self::$koreanRate;
    }

    /**
     * author zhangdong
     * date 2019-12-17
     * @return mixed
     */
    public static function setKoreanRate($koreanRate)
    {
        self::$koreanRate = $koreanRate;
    }

    /**
     * author zhangdong
     * date 2019-12-17
     * @return mixed
     */
    public static function getRmbKoreanRate()
    {
        return self::$rmbKoreanRate;
    }

    /**
     * author zhangdong
     * date 2019-12-17
     * @return mixed
     */
    public static function setRmbKoreanRate($rmbKoreanRate)
    {
        self::$rmbKoreanRate = $rmbKoreanRate;
    }

    /**
     * author zhangdong
     * date 2019-12-18
     * @return mixed
     */
    public static function getLowPriceId()
    {
        return self::$lowPriceId;
    }

    /**
     * author zhangdong
     * date 2019-12-18
     * @return mixed
     */
    public static function setLowPriceId($lowPriceId)
    {
        self::$lowPriceId = $lowPriceId;
    }








}//end of class