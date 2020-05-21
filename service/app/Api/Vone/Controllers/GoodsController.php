<?php

namespace App\Api\Vone\Controllers;

use App\Model\Vone\CommonModel;
use App\Model\Vone\DataModel;
use App\Model\Vone\DiscountModel;
use App\Model\Vone\DepartmentModel;
use App\Model\Vone\DiscountTypeModel;
use App\Model\Vone\GoodsCodeModel;
use App\Model\Vone\GoodsIntegralModel;
use App\Model\Vone\GoodsLabelModel;
use App\Model\Vone\GoodsSaleModel;
use App\Model\Vone\GoodsSpecModel;
use App\Model\Vone\MarginRateModel;
use App\Model\Vone\PurchaseChannelModel;
use App\Model\Vone\PurchaseMethodModel;
use App\Model\Vone\RealPurchaseModel;
use App\Model\Vone\SaleUserModel;
use App\Model\Vone\ShopCartModel;
use App\Model\Vone\StandbyGoodsModel;
use App\Model\Vone\TaskModel;
use App\Model\Vone\OfferOrderModel;
use App\Model\Vone\OfferGoodsModel;
use App\Model\Vone\ErpGoodsModel;
use App\Model\Vone\OperateLogModel;//zhangdong on the 2018.08.17
use App\Model\Vone\GoodsModel;//zhangdong on the 2018.08.17
use App\Model\Vone\ErpGoodsSpecModel;//zhangdong 2019.01.07
use App\Model\Vone\UserDiscountModel;
use App\Model\Vone\WholesaleGoodsModel;
use App\Model\Vone\WholesaleOrderModel;//zhangdong 2020.03.24
use App\Model\Vone\WholesaleDiscountModel;//zhangdong 2020.03.26

use App\Modules\Erp\ErpApi;//zhangdong 2020.03.12
use App\Modules\Excel\ExcuteExcel;
use App\Modules\ParamsCheckSingle;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Intervention\Image\ImageManagerStatic as Image;
use Maatwebsite\Excel\Classes\PHPExcel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\Validator;


//create by zhangdong on the 2018.08.17
class GoodsController extends BaseController
{

    private $egsModel;
    private $egModel;

    /*
     * @desc:本类构造函数
     * @author:zhangdong
     * @date:2019.01.07
     * */
    public function __construct()
    {
        $this->egsModel = new ErpGoodsSpecModel();
        $this->egModel = new ErpGoodsModel();
    }

    /**
     * description : 商品模块-打开新增商品页面
     * editor : zhangdong
     * date : 2018.08.17
     */
    public function addGoods(Request $request)
    {
        if ($request->method("get")) {
            $goodsModel = new goodsModel();
            //获取商品分类数据 - 一级分类
            $parent_id = 0;
            $goodsCateInfo = $goodsModel->getGoodsCategory($parent_id);
            //获取品牌数据
            $goodsBrandInfo = $goodsModel->getGoodsBrand();
            //获取仓库数据
            //$goodsStorehouseInfo = $goodsModel->getStorehouseInfo();

            //获取商品标签数据
            $goodsLabelModel = new GoodsLabelModel();
            $goodsLabelInfo = $goodsLabelModel->getAllGoodsLabelList();

            $data_info = [
                'goodsCateInfo' => $goodsCateInfo,
                'goodsBrandInfo' => $goodsBrandInfo,
                //'goodsStorehouseInfo' => $goodsStorehouseInfo,
                'goodsLabelInfo' => $goodsLabelInfo,
            ];
            $code = "1000";
            $msg = "打开新增商品页面成功";
            $data = $data_info;
            $return_info = compact('code', 'msg', 'data');
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:上传商品主图
     * editor:zongxing
     * type:POST
     * date : 2018.08.24
     * params: 1.需要上传的商品主图:upload_file;
     * return Object
     */
    public function uploadGoodsImg(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['spec_sn'])) {
                return response()->json(['code' => '1002', 'msg' => '商品规格码不能为空']);
            } elseif (empty($param_info['spec_img'])) {
                return response()->json(['code' => '1003', 'msg' => '上传文件不能为空']);
            }

            $file = $_FILES;
            $uploadName = $file['upload_file']['name'];
            $file_types = explode(".", $uploadName);
            $file_type = $file_types [count($file_types) - 1];
            if (strtolower($file_type) != "jpg" && strtolower($file_type) != "jpeg") {
                return response()->json(['code' => '1003', 'msg' => '请上传jpg格式的文件']);
            }

            //保存上传的折扣表
            $savePath = base_path() . '/uploadFile/goodsImg/' . date('Ymd') . '/';
            if (!is_dir($savePath)) {
                mkdir($savePath, 0777, true);
            }

            $str = date('Ymdhis');
            $file_name = $str . '.' . $file_type;
            //存储商品主图
            $uploadRes = $request->file('upload_file')->move($savePath, $file_name);

            $code = "1004";
            $msg = "上传商品主图失败";
            $return_info = ['code' => '1005', 'msg' => '上传商品主图失败'];

            $img_pre = '';
            $save_path = '';
            uploadImg($file, $img_pre, $save_path);
            if ($uploadRes) {
                $code = "1000";
                $msg = "上传商品主图成功";
                $path = $_SERVER['SERVER_ADDR'];
                $data["goods_img"] = '/uploadFile/goodsImg/' . date('Ymd') . '/' . $str . '.' . $file_type;
                $return_info = compact('code', 'msg', 'data');
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:新增单个商品
     * editor:zongxing
     * date : 2018.08.24
     * params: 1.商品名称:goods_name;2.分类id:cat_id;3.品牌id:brand_id;
     * return Object
     */
    public function doAddGoods(Request $request)
    {
        $param_info = $request->toArray();
        $rules = [
            'goods_name' => 'required|string|unique:goods',
            'cat_id' => 'required',
            'brand_id' => 'required',
            'erp_merchant_no' => 'required_without_all:kaola_no,small_red_book|unique:goods_spec',
            'kaola_no' => 'required_without_all:erp_merchant_no,small_red_book|unique:goods_code,goods_code',
            'small_red_book' => 'required_without_all:kaola_no,erp_merchant_no|unique:goods_code,goods_code',
        ];
        $messages = [
            'goods_name.required' => '商品名称不能为空',
            'goods_name.string' => '商品名称必须为字符',
            'goods_name.unique' => '商品名称已经存在',
            'cat_id.required' => '商品分类不能为空',
            'brand_id.required' => '商品品牌不能为空',
            'erp_merchant_no.required_without_all' => '商家编码、考拉码、小红书码不能都为空',
            'erp_merchant_no.unique' => '商家编码已经存在',
            'kaola_no.required_without_all' => '商家编码、考拉码、小红书码不能都为空',
            'kaola_no.unique' => '考拉码已经存在',
            'small_red_book.required_without_all' => '商家编码、考拉码、小红书码不能都为空',
            'small_red_book.unique' => '小红书码已经存在',
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }
        //生成商品 goods_sn
        $goods_model = new GoodsModel();
        $goods_sn = $goods_model->get_goods_sn();
        $param_info['goods_sn'] = $goods_sn;
        //生成商品 spec_sn
        $param_info['spec_sn'] = $spec_sn = $goods_model->get_spec_sn($goods_sn);
        //上传商品图片
        if (isset($param_info['spec_img'])) {
            $file = $param_info['spec_img'];
            $disk = 'goodsImg';
            $dir_name = date('Ymd', time());
            $save_path = $dir_name . '/' . $spec_sn;
            $upload_res = uploadImg($file, $disk, $save_path);
            if ($upload_res['code'] != '1100') {
                return response()->json($upload_res);
            }
            $param_info['spec_img'] = $upload_res['path'];
        }
        //新增商品
        $addGoodsRes = $goods_model->doAddGoods($param_info);
        $return_info = ['code' => '1007', 'msg' => '新增单个商品失败'];
        if ($addGoodsRes !== false) {
            $return_info = ['code' => '1000', 'msg' => '新增单个商品成功'];
            //记录日志
            $operateLogModel = new OperateLogModel();
            $loginUserInfo = $request->user();
            $logData = [
                'table_name' => 'jms_goods',
                'bus_desc' => '新增商品id：' . $addGoodsRes['spec_id'],
                'bus_value' => $addGoodsRes['spec_id'],
                'admin_name' => trim($loginUserInfo->user_name),
                'admin_id' => trim($loginUserInfo->id),
                'ope_module_name' => '商品模块-新增单个商品',
                'module_id' => 3,
                'have_detail' => 0,
            ];
            $operateLogModel->insertLog($logData);
        }
        return response()->json($return_info);
    }

    /**
     * description:提交编辑商品
     * editor:zongxing
     * date : 2018.08.25
     * params: 1.商品名称:goods_name;2.分类id:cat_id;3.品牌id:brand_id;4.商品主图:goods_img;5.商品价格:spec_price;
     *          6.商品重量:spec_weight;7.商品仓位id:storehouse_id;8.商品id:goods_id;
     *         可选:1.商家编码:erp_merchant_no;2.商品代码:erp_prd_no;3.商品参考码:erp_ref_no;
     * return Object
     */
    public function doEditGoods(Request $request)
    {
        //参数判断
        $param_info = $request->toArray();
        $rules = [
            'spec_sn' => 'required|exists:goods_spec',
            'goods_sn' => 'required|exists:goods',
            'cat_id' => 'required',
            'brand_id' => 'required',
            'erp_merchant_no' => 'required_without_all:kaola_no,small_red_book',
            'kaola_no' => 'required_without_all:erp_merchant_no,small_red_book',
            'small_red_book' => 'required_without_all:kaola_no,erp_merchant_no',
        ];
        $messages = [
            'spec_sn.required' => '商品规格码不能为空',
            'spec_sn.exists' => '商品规格码错误',
            'goods_sn.required' => '商品货号不能为空',
            'goods_sn.exists' => '商品货号错误',
            'cat_id.required' => '商品分类不能为空',
            'brand_id.required' => '商品品牌不能为空',
            'erp_merchant_no.required_without_all' => '商家编码、考拉码、小红书码不能都为空',
            'kaola_no.required_without_all' => '商家编码、考拉码、小红书码不能都为空',
            'small_red_book.required_without_all' => '商家编码、考拉码、小红书码不能都为空',
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }
        $spec_sn = trim($param_info['spec_sn']);
        $goods_sn = trim($param_info['goods_sn']);
        $rules = [
            'goods_name' => "required|string|unique:goods,goods_name,$goods_sn,goods_sn",
            'erp_merchant_no' => "unique:goods_spec,erp_merchant_no,$spec_sn,spec_sn",
            'kaola_no' => "unique:goods_code,goods_code,$spec_sn,spec_sn",
            'small_red_book' => "unique:goods_code,goods_code,$spec_sn,spec_sn",
        ];
        $messages = [
            'goods_name.unique' => '商品名称已经存在',
            'erp_merchant_no.unique' => '商家编码已经存在',
            'kaola_no.unique' => '考拉码已经存在',
            'small_red_book.unique' => '小红书码已经存在',
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }
        //获取商品信息
        $param['spec_sn'] = trim($param_info['spec_sn']);
        $param['is_all'] = false;
        $goods_model = new GoodsModel();
        $goods_info = $goods_model->goodsDetail($param);
        $goods_info = $goods_info['goods_info'];
        //上传商品图片
        if (!empty($param_info['spec_img'])) {
            $file = $param_info['spec_img'];
            $disk = 'goodsImg';
            $dir_name = date('Ymd', time());
            $save_path = $dir_name . '/' . $spec_sn;
            $upload_res = uploadImg($file, $disk, $save_path);
            if ($upload_res['code'] != '1100') {
                return response()->json($upload_res);
            }
            $param_info['spec_img'] = $upload_res['path'];
        }
        //提交编辑商品
        $editGoodsRes = $goods_model->doEditGoods($param_info, $goods_info);
        $return_info = ['code' => '1008', 'msg' => '编辑商品失败'];
        if ($editGoodsRes !== false) {
            $return_info = ['code' => '1000', 'msg' => '编辑商品成功'];
            //记录日志
            $operateLogModel = new OperateLogModel();
            $loginUserInfo = $request->user();
            $goods_id = $goods_info['goods_id'];
            $logData = [
                'table_name' => 'jms_goods',
                'bus_desc' => '商品模块-提交编辑商品-商品id：' . $goods_id,
                'bus_value' => $goods_id,
                'admin_name' => trim($loginUserInfo->user_name),
                'admin_id' => trim($loginUserInfo->id),
                'ope_module_name' => '商品模块-提交编辑商品',
                'module_id' => 3,
                'have_detail' => 1,
            ];
            $logDetailData["table_name"] = 'operate_log_detail';
            foreach ($param_info as $k => $v) {
                if (isset($goods_info[$k]) && $goods_info[$k] != $v) {
                    $logDetailData["update_info"][] = [
                        'table_field_name' => $k,
                        'field_old_value' => $goods_info[$k],
                        'field_new_value' => $v,
                    ];
                }
            }
            if (isset($logDetailData["update_info"])) {
                $operateLogModel->insertMoreLog($logData, $logDetailData);
            }
        }
        return response()->json($return_info);
    }


    /**
     * description:打开新增商品规格页面
     * editor:zongxing
     * date : 2019.02.22
     * return Array
     */
    public function addGoodsSpec(Request $request)
    {
        if ($request->method('get')) {
            //获取商品标签
            $goods_label_model = new GoodsLabelModel();
            $goods_label_info = $goods_label_model->getAllGoodsLabelList();
            if (empty($goods_label_info)) {
                return response()->json(['code' => '1002', 'msg' => '暂无商品标签信息']);
            }
            $return_info = ['code' => '1000', 'msg' => '获取商品标签格成功', 'data' => $goods_label_info];
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:新增单个商品规格
     * editor:zongxing
     * date : 2018.08.24
     * params: 1.商家编码:erp_merchant_no;2.商品参考码:erp_ref_no;3.商品代码:erp_prd_no;4.商品价格:spec_price;
     *             5.商品重量:spec_weight;
     * return Object
     */
    public function doAddGoodsSpec(Request $request)
    {
        if ($request->method('post')) {
            $param_info = $request->toArray();
            //参数校验
            if (empty($param_info['spec_weight']) && empty($param_info['estimate_weight'])) {
                return response()->json(['code' => '1002', 'msg' => '商品重量和预估重量不能都为空']);
            }
            if (empty($param_info["spec_price"])) {
                return response()->json(['code' => '1003', 'msg' => '商品价格不能为空']);
            } else if (empty($param_info["goods_sn"])) {
                return response()->json(['code' => '1004', 'msg' => '商品货号不能为空']);
            } else if (empty($param_info['exw_discount'])) {
                return response()->json(['code' => '1007', 'msg' => '商品exw折扣不能为空']);
            }
            //进行商品信息校验
            $goods_sn = trim($param_info['goods_sn']);
            $goods_model = new GoodsModel();
            $goods_info = $goods_model->getGoodsByGoodsSn($goods_sn);
            if (empty($goods_info)) {
                return response()->json(['code' => '1005', 'msg' => '商品货号有误']);
            }
            //进行商品码校验
            if (!empty($param_info['erp_merchant_no'])) {
                $goods_code_model = new GoodsCodeModel();
                $goods_spec_info = $goods_code_model->getGoodsCodeInfo($param_info);
                if (!empty($goods_spec_info)) {
                    return response()->json(['code' => '1006', 'msg' => '该商品规格已经存在']);
                }
            }
            //更新商品规格
            $addGoodsSpecRes = $goods_model->doAddGoodsSpec($param_info);
            $return_info = ['code' => '1009', 'msg' => '新增商品规格失败'];
            if ($addGoodsSpecRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '新增商品规格成功'];
                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_goods_spec',
                    'bus_desc' => '新增商品规格id：' . $addGoodsSpecRes,
                    'bus_value' => $addGoodsSpecRes,
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '商品模块-新增商品规格',
                    'module_id' => 3,
                    'have_detail' => 0,
                ];
                $operateLogModel->insertLog($logData);
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description : 商品模块-打开新增商品编码页面
     * editor : zongxing
     * date : 2019.02.20
     */
    public function addGoodsCode(Request $request)
    {
        if ($request->method("get")) {
            $goodsModel = new goodsModel();
            $platform = $goodsModel->platform;
            $return_info = ['code' => '1000', 'msg' => '打开新增商品编码页面成功', 'data' => $platform];
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:新增单个商品编码
     * editor:zongxing
     * date : 2019.02.20
     * params: 1.商家编码类型:code_type;2.商品编码:goods_code;3.商品规格码:spec_sn;
     * return Array
     */
    public function doAddGoodsCode(Request $request)
    {
        if ($request->method('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['code_type'])) {
                return response()->json(['code' => '1002', 'msg' => '商品编码类型不能为空']);
            } else if (empty($param_info['goods_code'])) {
                return response()->json(['code' => '1003', 'msg' => '商品编码不能为空']);
            } else if (empty($param_info['spec_sn'])) {
                return response()->json(['code' => '1004', 'msg' => '商品规格码不能为空']);
            }
            //进行商品校验
            $spec_sn = trim($param_info['spec_sn']);
            $goods_model = new GoodsModel();
            $goods_info = $goods_model->getGoodsInfo($spec_sn, 2);
            $goods_info = objectToArrayZ($goods_info);
            if (empty($goods_info)) {
                return response()->json(['code' => '1005', 'msg' => '商品规格码有误']);
            }
            //进行商品编码校验
            $goods_code_model = new GoodsCodeModel();
            $goods_code_info = $goods_code_model->getGoodsCodeInfo($param_info);
            if (!empty($goods_code_info)) {
                return response()->json(['code' => '1006', 'msg' => '该商品编码已经存在']);
            }
            //新增商品编码
            $addGoodsCodeRes = $goods_code_model->doAddGoodsCode($param_info);
            $return_info = ['code' => '1007', 'msg' => '新增商品编码失败'];
            if ($addGoodsCodeRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '新增商品编码成功'];
                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_goods_spec',
                    'bus_desc' => '新增商品编码id：' . $addGoodsCodeRes['id'],
                    'bus_value' => $addGoodsCodeRes['id'],
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '商品模块-新增商品编码',
                    'module_id' => 3,
                    'have_detail' => 0,
                ];
                $operateLogModel->insertLog($logData);
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:打开商品编码编辑页面
     * editor:zongxing
     * date : 2019.02.20
     * params: 1.商品编码id:goods_code_id;
     * return Object
     */
    public function editGoodsCode(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['goods_code_id'])) {
            return response()->json(['code' => '1002', 'msg' => '商品编码id不能为空']);
        }
        //检查商品编码是否存在
        $goods_code_model = new GoodsCodeModel();
        $goods_code_info = $goods_code_model->getGoodsCodeInfo($param_info);
        $return_info = ['code' => '1004', 'msg' => '获取商品编码信息失败'];
        if (!empty($goods_code_info)) {
            $return_info = ['code' => '1000', 'msg' => '获取商品编码信息成功', 'data' => $goods_code_info];
        }
        return response()->json($return_info);
    }

    /**
     * description:提交编辑商品编码
     * editor:zongxing
     * date : 2019.02.20
     * params: 1.商品编码id:goods_code_id;2.商品规格码:spec_sn;3.商品编码:goods_code;4.商品编码类型:code_type;
     * return Object
     */
    public function doEditGoodsCode(Request $request)
    {
        if ($request->method("post")) {
            $param_info = $request->toArray();
            if (empty($param_info['goods_code_id'])) {
                return response()->json(['code' => '1002', 'msg' => '商品编码id不能为空']);
            } else if (empty($param_info['spec_sn'])) {
                return response()->json(['code' => '1003', 'msg' => '商品规格码不能为空']);
            } else if (empty($param_info['goods_code'])) {
                return response()->json(['code' => '1004', 'msg' => '商品编码不能为空']);
            } else if (empty($param_info['code_type'])) {
                return response()->json(['code' => '1005', 'msg' => '商品编码类型不能为空']);
            }

            //检查商品编码是否存在
            $goods_code_id = intval($param_info['goods_code_id']);
            $param['goods_code_id'] = $goods_code_id;
            $goods_code_model = new GoodsCodeModel();
            $goods_code_info = $goods_code_model->getGoodsCodeInfo($param);
            if (empty($goods_code_info)) {
                return response()->json(['code' => '1006', 'msg' => '商品编码id错误,请检查']);
            }
            $editGoodsRes = $goods_code_model->doEditGoodsCode($param_info);
            $return_info = ['code' => '1007', 'msg' => '编辑商品编码失败'];
            if ($editGoodsRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '编辑商品编码成功'];
                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_goods_spec',
                    'bus_desc' => '商品模块-提交编辑商品编码-商品编码id：' . $goods_code_id,
                    'bus_value' => $goods_code_id,
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '商品模块-提交编辑商品编码',
                    'module_id' => 3,
                    'have_detail' => 1,
                ];

                $logDetailData["table_name"] = 'operate_log_detail';
                unset($param_info['goods_code_id']);
                foreach ($param_info as $k => $v) {
                    if (isset($goods_code_info[$k]) && $goods_code_info[$k] != $v) {
                        $logDetailData["update_info"][] = [
                            'table_field_name' => $k,
                            'field_old_value' => $goods_code_info[$k],
                            'field_new_value' => $v,
                        ];
                    }
                }
                if (isset($logDetailData["update_info"])) {
                    $operateLogModel->insertMoreLog($logData, $logDetailData);
                }
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }


    /**
     * description:批量新增
     * editor:zongxing
     * date : 2018.08.24
     * params: 1.需要上传的商品列表信息:upload_file;
     * return Object
     */
    public function uploadAddGoods_stop(Request $request)
    {
        if ($request->isMethod('post')) {
            $goods_list_info = $request->toArray();

            if (empty($goods_list_info['upload_file'])) {
                return response()->json(['code' => '1002', 'msg' => '上传文件不能为空']);
            }

            //检查上传文件是否合格
            $file = $_FILES;
            $excuteExcel = new ExcuteExcel();
            $fileName = '批量新增商品';//要上传的文件名，将对上传的文件名做比较
            $res = $excuteExcel->verifyUploadFileZ($file, $fileName);
            if (isset($res['code'])) {
                return response()->json($res);
            }

            //暂时隐藏
            //保存上传的折扣表
//            $savePath = base_path() . '/uploadFile/' . date('Ymd') . '/';
//            if (!is_dir($savePath)) {
//                mkdir($savePath, 0777, true);
//            }
//
//            $file_types = explode(".", $file['upload_file']['name']);
//            $file_type = $file_types [count($file_types) - 1];
//            $str = date('Ymdhis');
//            $file_name = $str . '.' . $file_type;
//            $request->file('upload_file')->move($savePath, $file_name);

            //检查字段名称
            $arrTitle = ['商品名称', '商家编码', '商品代码', '商品参考码', '商品分类', '商品品牌', '商品价格', '商品重量'];
            foreach ($arrTitle as $title) {
                if (!in_array(trim($title), $res[0])) {
                    return response()->json(['code' => '1005', 'msg' => '您的标题头有误，请按模板导入']);
                }
            }

            $goods_model = new GoodsModel();
            $goods_insert_info = $goods_model->create_goods_info($res);

            if (!empty($goods_insert_info["diff_erp_merchant_no"])) {
                $diff_erp_merchant_no = $goods_insert_info["diff_erp_merchant_no"];
                return response()->json(['code' => '1013', 'msg' => '您提交的商品中商家编码:' . $diff_erp_merchant_no . " 已经存在"]);
            }

            $updateRes = DB::transaction(function () use ($goods_insert_info) {
                //goods表
                $goods_info = $goods_insert_info["goods_info"];
                DB::table("goods")->insert($goods_info);

                //goods_spec表
                $goods_spec_info = $goods_insert_info["goods_spec_info"];
                $update_res = DB::table("goods_spec")->insert($goods_spec_info);
                return $update_res;
            });

            $return_info = [
                'code' => '1000',
                'msg' => '批量新增商品成功',
            ];

            if (!$updateRes) {
                $return_info = [
                    'code' => '1012',
                    'msg' => '批量新增商品失败',
                ];
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:商品批量新增
     * editor:zhangdong
     * date : 2019.02.13
     */
    public function uploadAddGoods(Request $request)
    {
        $goods_list_info = $request->toArray();

        if (empty($goods_list_info['upload_file'])) {
            return response()->json(['code' => '1002', 'msg' => '上传文件不能为空']);
        }
        //检查上传文件是否合格
        $file = $_FILES;
        $excuteExcel = new ExcuteExcel();
        $fileName = '批量新增商品';//要上传的文件名，将对上传的文件名做比较
        $res = $excuteExcel->verifyUploadFileZ($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        //检查字段名称
        $arrTitle = [
            '商品名称', '商品分类', '品牌ID', '商家编码', '考拉编码', '小红书编码', '参考代码',
            '商品代码', '美金原价', '商品重量', '预估重量', 'EXW折扣',
        ];
        foreach ($arrTitle as $title) {
            if (!in_array(trim($title), $res[0])) {
                return response()->json(['code' => '1005', 'msg' => '您的标题头有误，请按模板导入']);
            }
        }
        $goods_model = new GoodsModel();
        //组装商品信息
        $goodsInfoData = $goods_model->createGoodsInfo($res);
        if (isset($goodsInfoData['code']) && $goodsInfoData['code'] == 2064) {
            return response()->json($goodsInfoData);
        }
        //批量保存商品信息
        $insertRes = $goods_model->batchInsertGoods($goodsInfoData);
        $return_info = ['code' => '1012', 'msg' => '批量新增商品失败'];
        if ($insertRes) {
            $return_info = ['code' => '1000', 'msg' => '批量新增商品成功'];
        }
        return response()->json($return_info);
    }

    /**
     * description:获取商品分类信息
     * editor:zongxing
     * date : 2019.01.23
     * return Array
     */
    public function getCatInfo(Request $request)
    {
        $cat_total_info = DB::table('category')->where('parent_id', 0)->get(['cat_id', 'cat_name']);
        $cat_total_info = objectToArrayZ($cat_total_info);
        foreach ($cat_total_info as $k => $v) {
            $cat_id = $v['cat_id'];
            $cat_info = DB::table('category')->where('parent_id', $cat_id)->get(['cat_id', 'cat_name']);
            $cat_info = objectToArrayZ($cat_info);
            if (!empty($cat_info)) {
                foreach ($cat_info as $k1 => $v1) {
                    $cat_child_1_id = $v1['cat_id'];
                    $cat_child_1 = DB::table('category')->where('parent_id', $cat_child_1_id)->get(['cat_id', 'cat_name']);
                    $cat_child_1 = objectToArrayZ($cat_child_1);

                    if (!empty($cat_child_1)) {
                        $cat_info[$k1]['child'] = $cat_child_1;
                    }
                }
                $cat_total_info[$k]['child'] = $cat_info;
            }
        }
        $this->exportGoodsData($cat_total_info);
        return response()->json(['code' => '1000', 'cat_list' => $cat_total_info]);
    }

    private function exportGoodsData($cat_total_info)
    {
        $title = [['分类id', '分类名称', '分类id', '分类名称', '分类id', '分类名称']];
        $cat_list = [];
        foreach ($cat_total_info as $k => $v) {
            if (!empty($v['child'])) {
                foreach ($v['child'] as $k1 => $v1) {
                    if (!empty($v1['child'])) {
                        foreach ($v1['child'] as $k2 => $v2) {
                            $tmp_cat = [
                                $v['cat_id'], $v['cat_name'], $v1['cat_id'], $v1['cat_name'], $v2['cat_id'], $v2['cat_name']
                            ];
                            $cat_list[$v['cat_id'] . $v1['cat_id'] . $v2['cat_id']] = $tmp_cat;
                        }
                    }
                }
            }
        }

        $filename = '分类详情表_';
        $exportData = array_merge($title, $cat_list);
        //数据导出
        $excuteExcel = new ExcuteExcel();
        return $excuteExcel->exportZ($exportData, $filename);
    }

    /**
     * description:批量更新品牌信息
     * editor:zongxing
     * date : 2019.01.24
     * return Array
     */
    public function updateGoodsBrandInfo()
    {
        $brand_total_info = DB::table('brand')->get(['brand_id', 'name', 'name_en']);
        $brand_total_info = objectToArrayZ($brand_total_info);
        $error_info = 0;
        foreach ($brand_total_info as $k => $v) {
            $brand_id = $v['brand_id'];
            $name_en = $v['name_en'];
            $name_en = '%' . $name_en . '%';
            $goods_info = DB::table('goods')->where('goods_name', 'LIKE', $name_en)->get(['goods_id', 'brand_id']);
            $goods_info = objectToArrayZ($goods_info);

            if (!empty($goods_info)) {
                foreach ($goods_info as $k1 => $v1) {
                    if ($v1['brand_id'] === 0 && $v1['brand_id'] !== $brand_id) {
                        $goods_id = $v1['goods_id'];
                        $update_info = [
                            'brand_id' => $brand_id
                        ];
                        $res = DB::table('goods')->where('goods_id', $goods_id)->update($update_info);
                        if ($res === false) {
                            $error_info++;
                        }
                    }
                }
            }
        }

        if ($error_info) {
            return response()->json(['code' => '1000', 'msg' => '成功']);
        }
        return response()->json(['code' => '1002', 'msg' => '失败']);
    }

    /**
     * description:打开商品编辑页面
     * editor:zongxing
     * date : 2018.08.25
     * params: 1.商品id:goods_id;
     * return Object
     */
    public function editGoods(Request $request)
    {
        if ($request->method("get")) {
            $param_info = $request->toArray();
            if (empty($param_info["goods_id"])) {
                return response()->json(['code' => '1002', 'msg' => '商品id不能为空']);
            }
            //检查商品是否存在
            $goods_id = intval($param_info['goods_id']);
            $goods_model = new GoodsModel();

            $goods_info = $goods_model->getGoodsDetailInfo($goods_id);
            if (empty($goods_info)) {
                return response()->json(['code' => '1003', 'msg' => '商品id错误']);
            }
            //获取商品分类数据 一级分类
            $parent_id = 0;
            $goods_cat_info = $goods_model->getGoodsCategory($parent_id);
            //获取品牌数据
            $goods_brand_info = $goods_model->getGoodsBrand();
            $code = "1000";
            $msg = "获取商品信息成功";
            $data = [
                'goods_info' => $goods_info,
                'goods_cat_info' => $goods_cat_info,
                'goods_brand_info' => $goods_brand_info,
            ];
            $return_info = compact('code', 'msg', 'data');
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }


    /**
     * description:打开商品规格编辑页面
     * editor:zongxing
     * date : 2018.08.25
     * params: 1.商品规格id:spec_id;
     * return Object
     */
    public function editGoodsSpec(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info["spec_id"])) {
            return response()->json(['code' => '1002', 'msg' => '商品规格id不能为空']);
        }
        //检查商品规格是否存在
        $spec_id = $param_info["spec_id"];
        $goods_model = new GoodsModel();
        $goods_spec_info = $goods_model->get_goods_spec_info($spec_id);
        $return_info = ['code' => '1004', 'msg' => '获取商品规格信息失败'];
        if (!empty($goods_spec_info)) {
            //获取商品标签
            $goods_label_model = new GoodsLabelModel();
            $goods_label_info = $goods_label_model->getAllGoodsLabelList();

            $data['goods_spec_info'] = $goods_spec_info;
            $data['goods_label_info'] = $goods_label_info;
            $return_info = ['code' => '1000', 'msg' => '获取商品规格信息成功', 'data' => $data];
        }
        return response()->json($return_info);
    }

    /**
     * description:提交编辑商品规格
     * editor:zongxing
     * date : 2018.08.25
     * params: 1.商品名称:goods_name;2.分类id:cat_id;3.品牌id:brand_id;4.商品主图:goods_img;5.商品价格:spec_price;
     *          6.商品重量:spec_weight;7.商品仓位id:storehouse_id;8.商品id:goods_id;
     *         可选:1.商家编码:erp_merchant_no;2.商品代码:erp_prd_no;3.商品参考码:erp_ref_no;
     * return Object
     */
    public function doEditGoodsSpec(Request $request)
    {
        if ($request->method("post")) {
            $param_info = $request->toArray();
            //参数校验
            if (empty($param_info['spec_weight']) && empty($param_info['estimate_weight'])) {
                return response()->json(['code' => '1002', 'msg' => '商品重量和预估重量不能都为空']);
            }
            if (empty($param_info['spec_id'])) {
                return response()->json(['code' => '1003', 'msg' => '商品规格id不能为空']);
            } elseif (empty($param_info["spec_price"])) {
                return response()->json(['code' => '1004', 'msg' => '商品价格不能为空']);
            } elseif (empty($param_info['exw_discount'])) {
                return response()->json(['code' => '1006', 'msg' => '商品exw折扣不能为空']);
            }
            //进行商品信息校验
            $gs_model = new GoodsSpecModel();
            $goods_info = $gs_model->getGoodsSpec($param_info);
            if (empty($goods_info)) {
                return response()->json(['code' => '1005', 'msg' => '商品货号或规格id有误']);
            }
            //获取商品码校验
            if (!empty($param_info['erp_merchant_no'])) {
                $spec_sn = $goods_info['spec_sn'];
                $goods_code_model = new GoodsCodeModel();
                $gc_new_info = $goods_code_model->getGoodsCodeInfo($param_info);
                if (!empty($gc_new_info) && $spec_sn != $gc_new_info['spec_sn']) {
                    return response()->json(['code' => '1006', 'msg' => '该商家编码已经存在']);
                }
            }
            $editGoodsRes = $gs_model->doEditGoodsSpec($param_info, $goods_info);
            $return_info = ['code' => '1011', 'msg' => '编辑商品失败'];
            if ($editGoodsRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '编辑商品成功'];
                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $spec_id = intval($param_info['spec_id']);
                $logData = [
                    'table_name' => 'jms_goods_spec',
                    'bus_desc' => '商品模块-提交编辑商品规格-商品规格id：' . $spec_id,
                    'bus_value' => $spec_id,
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '商品模块-提交编辑商品规格',
                    'module_id' => 3,
                    'have_detail' => 1,
                ];

                $logDetailData["table_name"] = 'operate_log_detail';
                unset($param_info["spec_id"]);
                foreach ($param_info as $k => $v) {
                    if (isset($goods_spec_info[$k]) && $goods_spec_info[$k] != $v) {
                        $logDetailData["update_info"][] = [
                            'table_field_name' => $k,
                            'field_old_value' => $goods_spec_info[$k],
                            'field_new_value' => $v,
                        ];
                    }
                }
                if (isset($logDetailData["update_info"])) {
                    $operateLogModel->insertMoreLog($logData, $logDetailData);
                }
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:商品模块-商品列表
     * editor:zongxing
     * date : 2018.08.25
     * type:GET
     * return Object
     */
    public function goodsList(Request $request)
    {
        if ($request->method("get")) {
            $param_info = $request->toArray();
            $goods_model = new GoodsModel();
            $param_info['is_all'] = true;
            $goods_list_info = $goods_model->goodsList($param_info);
            $return_info = ['code' => '1002', 'msg' => '暂无商品'];
            if ($goods_list_info !== false) {
                $return_info = ['code' => '1000', 'msg' => '获取商品列表成功', 'data' => $goods_list_info];
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:商品模块-商品详情
     * editor:zongxing
     * date : 2020.02.13
     * type:GET
     * return Object
     */
    public function goodsDetail(Request $request)
    {
        if ($request->method("get")) {
            $param_info = $request->toArray();
            if (empty($param_info['spec_sn'])) {
                return response()->json(['code' => '1002', 'msg' => '商品规格码不能为空']);
            }

            $goods_model = new GoodsModel();
            $param_info['is_all'] = true;
            $goods_detail_info = $goods_model->goodsDetail($param_info);
            $return_info = ['code' => '1003', 'msg' => '商品规格码错误'];
            if ($goods_detail_info !== false) {
                $return_info = ['code' => '1000', 'msg' => '获取商品详情成功', 'data' => $goods_detail_info];
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:商品模块-商品规格列表
     * editor:zongxing
     * date : 2018.08.25
     * type:GET
     * return Object
     */
    public function goodsSpecList_stop(Request $request)
    {
        if ($request->method("get")) {
            $req_params = $request->toArray();
            $goods_model = new GoodsModel();
            $goods_spec_info = $goods_model->goodsSpecList($req_params);

            $code = "1002";
            $msg = "暂无商品规格";
            $return_info = compact('code', 'msg');

            if (!empty($goods_spec_info)) {
                $code = "1000";
                $msg = "获取商品规格列表成功";
                $data = $goods_spec_info;
                $return_info = compact('code', 'msg', 'data', 'data_num');
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:改变商品上下架状态
     * editor:zongxing
     * date : 2018.08.25
     * params: 1.商品id:goods_id;2.要更改的状态:is_on_sale;
     * return Object
     */
    public function changeSaleStatus(Request $request)
    {
        if ($request->method("post")) {
            $post_goods_info = $request->toArray();

            //检查商品是否存在
            $goods_id = $post_goods_info["goods_id"];
            $goods_model = new GoodsModel();
            $goods_info = $goods_model->check_goods_info($goods_id);

            if (empty($goods_info)) {
                return response()->json(['code' => '1002', 'msg' => '此商品不存在']);
            }

            $goods["is_on_sale"] = $post_goods_info["is_on_sale"];
            $updateRes = DB::table("goods")->where("goods_id", $goods_id)->update($goods);

            $code = "1003";
            $msg = "改变商品上下架失败";
            $return_info = compact('code', 'msg');

            if ($updateRes) {
                $code = "1000";
                $msg = "改变商品上下架成功";
                $return_info = compact('code', 'msg');

                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_task',
                    'bus_desc' => '商品模块-改变商品上下架状态-商品id：' . $goods_id,
                    'bus_value' => $post_goods_info["is_on_sale"],
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '商品模块-改变商品上下架状态',
                    'module_id' => 3,
                    'have_detail' => 0,
                ];
                $operateLogModel->insertLog($logData);
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:获取主采折扣列表
     * editor:zongxing
     * type:GET
     * date : 2018.09.14
     * return Object
     */
    public function mainDiscountList(Request $request)
    {
        if ($request->isMethod("get")) {
            $search_info = $request->toArray();
            //获取当前采购折扣数据
            $mainDiscountModel = new MainDiscountModel();
            $discount_info = $mainDiscountModel->mainDiscountList($search_info);

            if (empty($discount_info)) {
                return response()->json(['code' => '1002', 'msg' => '未找到对应的折扣']);
            }

            $code = "1000";
            $msg = "获取采购列表成功";
            $data = $discount_info;
            $return_info = compact('code', 'msg', 'data');
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:主采折扣上传
     * editor:zongxing
     * type:POST
     * date : 2018.09.14
     * params: 1.需要上传的excel表格文件:upload_file;
     * return Object
     */
    public function uploadMainDiscount(Request $request)
    {
        if ($request->isMethod('post')) {
            $brand_discount_array = $request->toArray();

            if (empty($brand_discount_array['upload_file'])) {
                return response()->json(['code' => '1002', 'msg' => '上传文件不能为空']);
            }

            $file = $_FILES;

            //检查上传文件是否合格
            $excuteExcel = new ExcuteExcel();
            $fileName = '主采折扣表';//要上传的文件名，将对上传的文件名做比较
            $res = $excuteExcel->verifyUploadFileZ($file, $fileName);
            if (isset($res['code'])) {
                return response()->json($res);
            }

            //检查字段名称
            $arrTitle = ['品牌', '折扣'];
            foreach ($arrTitle as $title) {
                if (!in_array(trim($title), $res[0])) {
                    $returnMsg = ['code' => '1005', 'msg' => '您的标题头有误，请按模板导入'];
                    return response()->json($returnMsg);
                }
            }

            //组装采购折扣详情表添加数据
            $mainDiscountModel = new MainDiscountModel();
            $mainDiscountData = $this->createDetailData($res);

            if (!empty($mainDiscountData)) {
                //采购折扣日志表sn
                $str = date('Ymdhis');
                $discount_sn = "ZK-" . $str;
                $uploadRes = $mainDiscountModel->discountChange($mainDiscountData, $discount_sn);

                $return_info = [
                    'code' => '1006',
                    'msg' => "文件上传失败",
                ];

                if ($uploadRes !== false) {
                    $return_info = [
                        'code' => '1000',
                        'msg' => '文件上传成功',
                    ];
                }
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:组装采购折扣详情表添加数据
     * editor:zongxing
     * date : 2018.07.14
     * return Object
     */
    public function createDetailData($res)
    {
        //行数
        $row_num = count($res);

        $discountModel = new DiscountModel();
        $discountData = [];

        $sql_update_discount = "UPDATE jms_main_discount SET brand_discount = CASE brand_id ";
        for ($i = 0; $i < $row_num; $i++) {
            if ($i === 0) continue;//第1、2行数据为标题头

            //获取品牌id
            if (empty($res[$i][1])) continue;

            $brand_name = $res[$i][1];
            $brand_id = $discountModel->getBrandId($brand_name);

            if ($brand_id) {
                $brand_discount = $res[$i][3];

                if (empty($brand_discount)) $brand_discount = 0;
                $brand_discount = sprintf('%.2f', $brand_discount);

                //组装日志表数据
                $discountData["insert_main_log"][] = [
                    'brand_id' => $brand_id,
                    'brand_discount' => $brand_discount,
                ];

                //组装主采折扣表插入数据
                $brand_discount_info = DB::table("main_discount")->where("brand_id", $brand_id)->first();
                if (empty($brand_discount_info)) {
                    $discountData["insert_main"][] = [
                        'brand_id' => $brand_id,
                        'brand_discount' => $brand_discount,
                    ];
                } else {
                    //组装主采折扣表更新数据
                    $sql_update_discount .= sprintf(" WHEN " . $brand_id . " THEN " . $brand_discount);
                    $spec_sn_arr[] = $brand_id;
                }
            }
        }

        if (!empty($spec_sn_arr)) {
            $spec_sn_arr = implode(',', array_values($spec_sn_arr));
            $sql_update_discount .= " END WHERE brand_id IN (" . $spec_sn_arr . ")";
            $discountData["sql_update_discount"] = $sql_update_discount;
        }
        return $discountData;
    }


    /**
     * description:商品模块-销售用户列表
     * editor:zhangdong
     * date : 2018.10.31
     */
    public function saleUserList(Request $request)
    {
        $reqParams = $request->toArray();
        //搜索关键字
        $keywords = isset($reqParams['keywords']) ? trim($reqParams['keywords']) : '';
        $pageSize = isset($reqParams['pageSize']) ? intval($reqParams['pageSize']) : 15;
        //获取销售用户列表
        $goodsModel = new GoodsModel();
        $params['keywords'] = $keywords;
        $saleUserList = $goodsModel->saleUserList($params, $pageSize);
        $returnMsg = [
            'saleUserList' => $saleUserList
        ];
        return response()->json($returnMsg);

    }

    /**
     * description:商品模块-销售用户商品列表
     * editor:zhangdong
     * date : 2018.10.31
     */
    public function UserGoodsList(Request $request)
    {
        $reqParams = $request->toArray();
        if (!isset($reqParams['sale_user_id'])) {
            $returnMsg = ['code' => '2005', 'msg' => '参数错误'];
            return response()->json($returnMsg);
        }
        //搜索关键字
        $keywords = isset($reqParams['keywords']) ? trim($reqParams['keywords']) : '';
        $pageSize = isset($reqParams['pageSize']) ? intval($reqParams['pageSize']) : 15;
        $sale_user_id = intval($reqParams['sale_user_id']);
        //仓库id
        $store_id = isset($reqParams['store_id']) ? intval($reqParams['store_id']) : 1002;
        //一组商家编码
        $arrErpNo = isset($reqParams['arrErpNo']) ? json_decode($reqParams['arrErpNo']) : [];
        //获取自采毛利率档位信息
        $goodsModel = new GoodsModel();
        $pickMarginRate = $goodsModel->getPickMarginInfo();
        $arrPickRate = [];
        foreach ($pickMarginRate as $item) {
            $arrPickRate[] = sprintf('%.0f%%', trim($item['pick_margin_rate']));
        }
        //根据部门获取费用项
        $loginUserInfo = $request->user();
        $params_depId = 0;
        if (isset($reqParams['department_id'])) {
            $params_depId = intval($reqParams['department_id']);
        }
        $department_id = $params_depId > 0 ? $params_depId : intval($loginUserInfo->department_id);
        //获取部门信息
        $departmentModel = new DepartmentModel();
        $departmentInfo = $departmentModel->getDepartmentInfo();
        //检查是否存在当前的部门
        $found_key = $goodsModel->twoArraySearch($departmentInfo, $department_id, 'department_id');
        if ($found_key === false) {
            $returnMsg = ['code' => '2035', 'msg' => '部门信息有误，请联系管理员'];
            return response()->json($returnMsg);
        }
        //获取费用信息
        $chargeInfo = $goodsModel->getChargeInfo($department_id);
        $arrCharge = [];
        $totalCharge = 0;
        foreach ($chargeInfo as $item) {
            $arrCharge[] = [sprintf('%.0f%%', trim($item['charge_rate'])) => trim($item['charge_name'])];
            $totalCharge += $item['charge_rate'];
        }
        $arrCharge[] = [sprintf('%.0f%%', $totalCharge) => '费用合计'];
        //获取erp仓库信息-重价系数（默认为香港仓）
        $goodsHouseInfo = $goodsModel->getErpStoreInfo();
        //销售用户商品列表
        $goodsBaseInfo = $goodsModel->getUserGoodsInfo($sale_user_id, $keywords, $pageSize, $arrErpNo);
        if ($goodsBaseInfo->count() > 0) {
            $userGoodsList = $goodsModel->userGoodsList(
                $goodsBaseInfo, $pickMarginRate, $chargeInfo, $goodsHouseInfo, $store_id
            );
        }
        $brand_info = [];
        if (!empty($userGoodsList['brandInfo'])) {
            $brand_info = $goodsModel->array_unique_fb($userGoodsList['brandInfo']);
        }
        $userGoodsData = [];
        if (!empty($userGoodsList['goodsBaseInfo'])) {
            $userGoodsData = $userGoodsList['goodsBaseInfo'];
        }
        $returnMsg = [
            'store_id' => $store_id,
            'department_id' => $department_id,
            'arrPickRate' => $arrPickRate,
            'arrCharge' => $arrCharge,
            'departmentInfo' => $departmentInfo,
            'brand_info' => $brand_info,
            'erpInfo' => $goodsHouseInfo,
            'userGoodsList' => $userGoodsData,
        ];
        return response()->json($returnMsg);

    }

    /**
     * description:商品模块-上传销售用户商品数据
     * editor:zhangdong
     * date : 2018.11.01
     */
    public function upSaleGoods(Request $request)
    {
        $reqParams = $request->toArray();
        if (
            !isset($reqParams['upload_file']) ||
            !isset($reqParams['sale_user_id'])
        ) {
            $returnMsg = ['code' => '2005', 'msg' => '参数错误'];
            return response()->json($returnMsg);
        }
        //检查部门是否存在
        $loginInfo = $request->user();
        $department_id = intval($loginInfo->department_id);
        $departModel = new DepartmentModel();
        $departInfo = $departModel->getDepartmentInfo($department_id);
        if (count($departInfo) == 0) {
            $returnMsg = ['code' => '2035', 'msg' => '部门信息有误，请联系管理员'];
            return response()->json($returnMsg);
        }
        //检查销售用户是否存在
        $saleUserModel = new SaleUserModel();
        $sale_user_id = intval($reqParams['sale_user_id']);
        $saleUserInfo = $saleUserModel->getSaleUserMsg($sale_user_id, 2);
        if (is_null($saleUserInfo)) {
            $returnMsg = ['code' => '2006', 'msg' => '没有该销售用户的信息'];
            return response()->json($returnMsg);
        }

        //开始导入数据
        $file = $_FILES;
        //检查上传文件是否合格
        $excuteExcel = new ExcuteExcel();
        $fileName = '报价计算-销售用户商品数据';//要上传的文件名，将对上传的文件名做比较
        $res = $excuteExcel->verifyUploadFile($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        //检查字段名称
        $arrTitle = ['商家编码', '商品规格码'];
        foreach ($arrTitle as $title) {
            if (!in_array(trim($title), $res[0])) {
                $returnMsg = ['code' => '2009', 'msg' => '您的标题头有误，请按模板导入'];
                return response()->json($returnMsg);
            }
        }
        $goodsModel = new GoodsModel();
        //获取费用信息
        $chargeInfo = $goodsModel->getChargeInfo($department_id);
        $arrCharge = [];
        $totalCharge = 0;
        foreach ($chargeInfo as $item) {
            $arrCharge[] = [sprintf('%.0f%%', trim($item['charge_rate'])) => trim($item['charge_name'])];
            $totalCharge += $item['charge_rate'];
        }
        $arrCharge[] = [sprintf('%.0f%%', $totalCharge) => '费用合计'];
        //获取erp仓库信息-重价系数（默认为香港仓）
        $goodsHouseInfo = $goodsModel->getErpStoreInfo();
        //仓库id
        $store_id = isset($reqParams['store_id']) ? intval($reqParams['store_id']) : 1002;
        //获取自采毛利率档位信息
        $goodsModel = new GoodsModel();
        $pickMarginRate = $goodsModel->getPickMarginInfo();
        $arrPickRate = [];
        foreach ($pickMarginRate as $item) {
            $arrPickRate[] = sprintf('%.0f%%', trim($item['pick_margin_rate']));
        }
        //获取部门信息
        $departmentModel = new DepartmentModel();
        $departmentInfo = $departmentModel->getDepartmentInfo();
        //获取商品数据
        $erp_merchant_no = [];
        foreach ($res as $key => $value) {
            if ($key == 0) continue;
            $erp_merchant_no[] = trim($value[0]);
        }
        $goodsData = $goodsModel->getUpGoodsData($erp_merchant_no);
        $goodsBaseInfo = $goodsData['goodsBaseInfo'];
        $userGoodsList = $goodsModel->userGoodsList(
            $goodsBaseInfo, $pickMarginRate, $chargeInfo, $goodsHouseInfo, $store_id
        );
        $brand_info = [];
        if (!empty($userGoodsList['brandInfo'])) {
            $brand_info = $goodsModel->array_unique_fb($userGoodsList['brandInfo']);
        }
        $userGoodsData = [];
        if (!empty($userGoodsList['goodsBaseInfo'])) {
            $userGoodsData = $userGoodsList['goodsBaseInfo'];
            //更新或者新增销售用户商品数据
            $arrErpNo = $goodsData['erp_merchant_no'];
            $goodsModel->saveUserGoods(
                $userGoodsData, $sale_user_id, $arrErpNo, $department_id
            );
        }
        $returnMsg = [
            'store_id' => $store_id,
            'department_id' => $department_id,
            'arrPickRate' => $arrPickRate,
            'arrCharge' => $arrCharge,
            'departmentInfo' => $departmentInfo,
            'brand_info' => $brand_info,
            'erpInfo' => $goodsHouseInfo,
            'userGoodsList' => $userGoodsData,
        ];
        return response()->json($returnMsg);

    }

    /**
     * description:商品模块-单个修改销售折扣
     * editor:zhangdong
     * date : 2018.11.01
     */
    public function modSaleRate(Request $request)
    {
        $reqParams = $request->toArray();
        if (
            !isset($reqParams['spec_sn']) ||
            !isset($reqParams['sale_discount']) ||
            !isset($reqParams['sale_user_id']) ||
            !isset($reqParams['store_id'])
        ) {
            $returnMsg = ['code' => '2005', 'msg' => '参数错误'];
            return response()->json($returnMsg);
        }
        $spec_sn = trim($reqParams['spec_sn']);
        $pricing_rate = trim($reqParams['sale_discount']);
        $sale_user_id = intval($reqParams['sale_user_id']);
        $goodsModel = new GoodsModel();
        $modifyRes = $goodsModel->modUsrSaleDiscount($sale_user_id, $spec_sn, $pricing_rate);
        if (!$modifyRes) {
            $returnMsg = ['code' => '2023', 'msg' => '操作失败'];
            return response()->json($returnMsg);
        }
        //计算修改之后的值
        //查询商品信息
        $saleGoodsInfo = $goodsModel->getSaleGoodsInfo($sale_user_id, $spec_sn);
        $goodsInfo = $saleGoodsInfo[0];
        $gold_discount = trim($goodsInfo->gold_discount);
        $black_discount = trim($goodsInfo->black_discount);
        $spec_price = trim($goodsInfo->spec_price);
        $spec_weight = trim($goodsInfo->spec_weight);
        $exw_discount = trim($goodsInfo->exw_discount);
        $goodsPrice = $goodsModel->calculateGoodsPrice($spec_price, $gold_discount, $black_discount);
        //金卡价=美金原价*金卡折扣
        $goodsInfo->gold_price = $goodsPrice['goldPrice'];
        //黑卡价=美金原价*黑卡折扣
        $goodsInfo->black_price = $goodsPrice['blackPrice'];
        //根据部门获取费用项
        $loginUserInfo = $request->user();
        $department_id = intval($loginUserInfo->department_id);
        $chargeInfo = $goodsModel->getChargeInfo($department_id);
        $arrCharge = [];
        $totalCharge = 0;
        foreach ($chargeInfo as $item) {
            $arrCharge[] = [sprintf('%.0f%%', trim($item['charge_rate'])) => trim($item['charge_name'])];
            $totalCharge += $item['charge_rate'];
        }
        $arrCharge[] = [sprintf('%.0f%%', $totalCharge) => '费用合计'];
        //获取erp仓库信息-重价系数（默认为香港仓）
        $store_id = intval($reqParams['store_id']);
        $goodsHouseInfo = $goodsModel->getErpStoreInfo($store_id);
        if (count($goodsHouseInfo) == 0) {
            $returnMsg = ['code' => '2046', 'msg' => '数据异常，请联系管理员'];
            return response()->json($returnMsg);
        }
        $store_factor = trim($goodsHouseInfo[0]['store_factor']);
        $goodsInfo->store_name = trim($goodsHouseInfo[0]['store_name']);
        //获取有关erp的所有商品数据
        $erpGoodsData = $goodsModel->getErpGoodsData($spec_weight, $spec_price, $store_factor, $exw_discount);
        //重价比=重量/美金原价/重价系数/100
        $goodsInfo->high_price_ratio = $erpGoodsData['highPriceRatio'];
        //重价比折扣 = exw折扣+重价比
        $hrp_discount = $erpGoodsData['hprDiscount'];
        $goodsInfo->hpr_discount = $hrp_discount;
        //erp成本价=美金原价*重价比折扣*汇率
        $goodsInfo->erp_cost_price = $erpGoodsData['erpCostPrice'];
        //获取自采毛利率档位信息
        $pickMarginRate = $goodsModel->getPickMarginInfo();
        $arrMarginRate = [];
        foreach ($pickMarginRate as $item) {
            $marginRate = sprintf('%.0f%%', $item['pick_margin_rate']);//自采毛利率当前档位
            $rateData = round($erpGoodsData['hprDiscount'] / (1 - $item['pick_margin_rate'] / 100), DECIMAL_DIGIT);
            $arrMarginRate[] = [$marginRate => $rateData];
        }
        $goodsInfo->arrMarginRate = $arrMarginRate;
        $goodsInfo->sale_discount = $pricing_rate;
        //重价比折扣 = exw折扣+重价比
        $hrp_discount = $erpGoodsData['hprDiscount'];
        $afterModData = $goodsModel->calculPricingInfo($spec_price, $pricing_rate, $hrp_discount, $chargeInfo);
        $goodsInfo->salePrice = $afterModData['salePrice'];
        $goodsInfo->saleMarRate = $afterModData['saleMarRate'];
        $goodsInfo->runMarRate = $afterModData['runMarRate'];
        $goodsInfo->arrChargeRate = $afterModData['arrChargeRate'];
        $returnMsg = [
            'arrCharge' => $arrCharge,
            'goodsInfo' => $goodsInfo,
        ];
        return $returnMsg;
    }

    /**
     * description:商品模块-批量修改销售折扣
     * editor:zhangdong
     * date : 2018.11.01
     */
    public function batchSaleDis(Request $request)
    {
        $reqParams = $request->toArray();
        if (!isset($reqParams['sale_user_id']) ||
            !isset($reqParams['pick_margin_rate']) ||
            !isset($reqParams['store_id']) ||
            !isset($reqParams['arr_erp_no'])
        ) {
            $returnMsg = ['code' => '2005', 'msg' => '参数错误'];
            return response()->json($returnMsg);
        }
        $sale_user_id = trim($reqParams['sale_user_id']);
        $store_id = intval($reqParams['store_id']);
        $pick_margin_rate = trim($reqParams['pick_margin_rate']);
        //查询销售用户对应的商品信息
        $goodsModel = new GoodsModel();
        //检查自采毛利率中是否有该档位
        $pickRateInfo = $goodsModel->getPickMarginInfo($pick_margin_rate);
        if (count($pickRateInfo) == 0) {
            $returnMsg = ['code' => '2005', 'msg' => '没有该档位毛利率'];
            return response()->json($returnMsg);
        }
        $arr_erp_no = json_decode($reqParams['arr_erp_no']);
        $goodsData = $goodsModel->getUpGoodsData($arr_erp_no);
        $goodsBaseInfo = $goodsData['goodsBaseInfo'];
        //获取erp仓库信息-重价系数（默认为香港仓）
        $goodsHouseInfo = $goodsModel->getErpStoreInfo($store_id);
        $store_factor = trim($goodsHouseInfo[0]['store_factor']);
        $arr_update = [];
        foreach ($goodsBaseInfo as $value) {
            $spec_weight = trim($value->spec_weight);//商品重量
            $spec_sn = trim($value->spec_sn);
            $spec_price = trim($value->spec_price);
            $exw_discount = trim($value->exw_discount); //exw折扣
            //获取有关erp的所有商品数据
            $erpGoodsData = $goodsModel->getErpGoodsData($spec_weight, $spec_price, $store_factor, $exw_discount);
            //根据所选自采毛利率档位计算定价折扣
            //定价折扣=自采毛利率=重价比折扣/（1-对应档位利率）
            $pricing_rate = round($erpGoodsData['hprDiscount'] / (1 - $pick_margin_rate / 100), DECIMAL_DIGIT);
            //更新需求商品定价折扣-组装更新数组，进行批量更新
            $arr_update[] = [
                'spec_sn' => $spec_sn,
                'sale_discount' => $pricing_rate,
            ];
        }
        $table = 'jms_sale_user_goods';
        $andWhere = [
            'sale_user_id' => $sale_user_id,
        ];
        $arrSql = makeUpdateSql($table, $arr_update, $andWhere);
        $updateRes = $arrSql;
        if ($arrSql) {
            //开始批量更新
            $strSql = $arrSql['updateSql'];
            $bindData = $arrSql['bindings'];
            $updateRes = $goodsModel->executeSql($strSql, $bindData);
        }
        $returnMsg = ['code' => '2024', 'msg' => '操作成功'];
        if (!$updateRes) {
            $returnMsg = ['code' => '2023', 'msg' => '操作失败'];
        }
        return response()->json($returnMsg);
    }

    /**
     * description:获取erp商品列表
     * editor:zhangdong
     * date : 2019.01.07
     */
    public function getErpGoodsList(Request $request)
    {
        $reqParams = $request->toArray();
        $pageSize = isset($reqParams['pageSize']) ? intval($reqParams['pageSize']) : 15;
        $erpGoodsList = $this->egModel->getErpGoodsList($reqParams, $pageSize);
        $returnMsg = [
            'erpGoodsList' => $erpGoodsList,
        ];
        return response()->json($returnMsg);
    }

    /**
     * description:导出erp商品
     * editor:zhangdong
     * date : 2019.01.25
     */
    public function exportErpGoods()
    {
        //获取ERP商品导出数据
        $erpGoodsList = $this->egsModel->getExportGoodsData();
        $executeModel = new ExcuteExcel();
        return $executeModel->exportErpGoods($erpGoodsList);

    }


    /**
     * description:新增商品积分
     * editor:zhangdong
     * date : 2019.01.30
     */
    public function addGoodsIntegral(Request $request)
    {
        $reqParams = $request->toArray();
        $spec_sn = isset($reqParams['spec_sn']) ? trim($reqParams['spec_sn']) : '';
        $channel_id = isset($reqParams['channel_id']) ? intval($reqParams['channel_id']) : '';
        $integral = isset($reqParams['integral']) ? intval($reqParams['integral']) : '';
        if (empty($spec_sn) || empty($channel_id) || empty($integral)) {
            $returnMsg = ['code' => '2005', 'msg' => '参数错误'];
            return response()->json($returnMsg);
        }
        //检查规格码是否存在
        $gsModel = new GoodsSpecModel();
        $goodsInfo = $gsModel->getGoodsSpecInfo($spec_sn);
        //检查渠道信息是否存在
        $pcModel = new PurchaseChannelModel();
        $channelInfo = $pcModel->getChannelInfo($channel_id);
        if (count($goodsInfo) == 0 || count($channelInfo) == 0) {
            $returnMsg = ['code' => '2062', 'msg' => '商品或者渠道信息不存在'];
            return response()->json($returnMsg);
        }
        $giModel = new GoodsIntegralModel();
        //检查当前渠道下是否已经有了要写入的规格码
        $arrParams = [
            'spec_sn' => $spec_sn,
            'channel_id' => $channel_id,
        ];
        $integralInfo = $giModel->getIntegralInfo($arrParams);
        if ($integralInfo->count() > 0) {
            $returnMsg = ['code' => '2063', 'msg' => '该商品已存在对应渠道的积分信息'];
            return response()->json($returnMsg);
        }
        //信息校验通过开始新增
        $arrParams = [
            'spec_sn' => $spec_sn,
            'channel_id' => $channel_id,
            'integral' => $integral,
        ];
        $insertRes = $giModel->writeData($arrParams);
        $returnMsg = ['code' => '2023', 'msg' => '操作失败'];
        if ($insertRes) {
            $returnMsg = ['code' => '2024', 'msg' => '操作成功'];
        }
        return response()->json($returnMsg);
    }

    /**
     * description:新增商品标签
     * editor:zongxing
     * date : 2019.02.21
     * params: 1.商品标签名称:label_name;
     * return Array
     */
    public function doAddGoodsLabel(Request $request)
    {
        if ($request->method('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['label_name'])) {
                return response()->json(['code' => '1002', 'msg' => '商品标签名称不能为空']);
            } elseif (empty($param_info['label_color'])) {
                return response()->json(['code' => '1003', 'msg' => '商品标签眼颜色不能为空']);
            } elseif (empty($param_info['label_real_name'])) {
                return response()->json(['code' => '1005', 'msg' => '商品标签简称不能为空']);
            }

            $label_name = trim($param_info['label_name']);
            $label_real_name = trim($param_info['label_real_name']);
            if (strlen($label_name) != 3) {
                return response()->json(['code' => '1009', 'msg' => '商品标签简称长度错误,请检查']);
            }

            //检查商品标签是否已经存在
            $goods_label_model = new GoodsLabelModel();
            $param_str['label_name'] = $label_name;
            $param_str['label_real_name'] = $label_real_name;
            $label_info = $goods_label_model->getGoodsLabelInfo($param_info);
            if (!empty($label_info)) {
                return response()->json(['code' => '1010', 'msg' => '该商品标签已经存在']);
            }

            //新增商品标签
            $addGoodsLabelRes = $goods_label_model->doAddGoodsLabel($param_info);
            $return_info = ['code' => '1004', 'msg' => '新增商品标签失败'];
            if ($addGoodsLabelRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '新增商品标签成功'];
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:商品标签列表
     * editor:zongxing
     * date : 2019.02.21
     * params: 1.商品标签名称:label_name;
     * return Array
     */
    public function goodsLabelList(Request $request)
    {
        if ($request->method('get')) {
            $param_info = $request->toArray();
            $goods_label_model = new GoodsLabelModel();
            $label_list = $goods_label_model->getGoodsLabelList($param_info);
            if (empty($label_list)) {
                return response()->json(['code' => '1003', 'msg' => '暂无商品标签']);
            }
            $return_info = ['code' => '1000', 'msg' => '获取商品标签列表成功', 'data' => $label_list];
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:编辑商品标签
     * editor:zongxing
     * date : 2019.02.21
     * params: 1.商品标签id:label_id;
     * return Array
     */
    public function editGoodsLabel(Request $request)
    {
        if ($request->method('get')) {
            $param_info = $request->toArray();
            if (empty($param_info['id'])) {
                return response()->json(['code' => '1002', 'msg' => '商品标签id不能为空']);
            }
            $goods_label_model = new GoodsLabelModel();
            $label_info = $goods_label_model->getGoodsLabelInfo($param_info);
            if (empty($label_info)) {
                return response()->json(['code' => '1003', 'msg' => '商品标签id错误,请检查']);
            }
            $return_info = ['code' => '1000', 'msg' => '获取商品标签信息成功', 'data' => $label_info];
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:提交编辑商品标签
     * editor:zongxing
     * date : 2019.02.21
     * params: 1.商品标签名称:label_name;2.商品标签id:id
     * return Array
     */
    public function doEditGoodsLabel(Request $request)
    {
        if ($request->method('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['id'])) {
                return response()->json(['code' => '1002', 'msg' => '商品标签id不能为空']);
            } elseif (empty($param_info['label_name'])) {
                return response()->json(['code' => '1003', 'msg' => '商品标签名称不能为空']);
            } elseif (empty($param_info['label_color'])) {
                return response()->json(['code' => '1004', 'msg' => '商品标签颜色不能为空']);
            } elseif (empty($param_info['label_real_name'])) {
                return response()->json(['code' => '1008', 'msg' => '商品标签简称不能为空']);
            }

            $label_name = trim($param_info['label_name']);
            $label_real_name = trim($param_info['label_real_name']);
            $label_id = intval($param_info['id']);
            if (strlen($label_name) != 3) {
                return response()->json(['code' => '1009', 'msg' => '商品标签简称长度错误,请检查']);
            }

            //检查商品标签是否已经存在
            $param_str['id'] = $label_id;
            $goods_label_model = new GoodsLabelModel();
            $label_info = $goods_label_model->getGoodsLabelInfo($param_str);
            if (empty($label_info)) {
                return response()->json(['code' => '1005', 'msg' => '商品标签id错误,请检查']);
            }

            $param_str_2['label_real_name'] = $label_real_name;
            $label_info = $goods_label_model->getGoodsLabelInfo($param_str_2);
            if (isset($label_info['id']) && $label_info['id'] != $label_id) {
                return response()->json(['code' => '1006', 'msg' => '该商品标签名称已经存在']);
            }

            $param_str_3['label_name'] = $label_name;
            $label_info = $goods_label_model->getGoodsLabelInfo($param_str_3);
            if (isset($label_info['id']) && $label_info['id'] != $label_id) {
                return response()->json(['code' => '1006', 'msg' => '该商品标签简称已经存在']);
            }

            //提交编辑商品标签
            $addGoodsLabelRes = $goods_label_model->doEditGoodsLable($param_info);
            $return_info = ['code' => '1007', 'msg' => '提交编辑商品标签失败'];
            if ($addGoodsLabelRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '提交编辑商品标签成功'];
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:系统设置-销售模块-新增自采毛利率
     * editor:zongxing
     * type:GET
     * date : 2019.03.13
     * return Array
     */
    public function addMarginRate(Request $request)
    {
        $param_info = $request->toArray();
        $rules = [
            'pick_margin_rate' => 'required|unique:margin_rate|numeric',
        ];
        $messages = [
            'pick_margin_rate.required' => '自采毛利率不能为空',
            'pick_margin_rate.unique' => '该自采毛利率已经存在',
            'pick_margin_rate.numeric' => '自采毛利率必须为数值',
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }

        $res = DB::table('margin_rate')->insert($param_info);
        $return_info = ['code' => '1002', 'msg' => '新增自采毛利率失败'];
        if ($res !== false) {
            $return_info = ['code' => '1000', 'msg' => '新增自采毛利率成功'];
        }
        return response()->json($return_info);
    }

    /**
     * description:系统设置-销售模块-获取自采毛利率列表
     * editor:zongxing
     * type:GET
     * date : 2019.03.13
     * return Array
     */
    public function getMarginRateList()
    {
        $mr_model = new MarginRateModel();
        $margin_rate_list = $mr_model->getMarginRateList();
        $return_info = ['code' => '1002', 'msg' => '暂无自采毛利率列表'];
        if (!empty($margin_rate_list)) {
            $return_info = ['code' => '1000', 'msg' => '获取自采毛利率列表成功', 'data' => $margin_rate_list];
        }
        return response()->json($return_info);
    }

    /**
     * description:系统设置-销售模块-删除自采毛利率
     * editor:zongxing
     * type:GET
     * date : 2019.03.13
     * return Array
     */
    public function delMarginRate(Request $request)
    {
        $param_info = $request->toArray();
        $mr_model = new MarginRateModel();
        $margin_rate_info = $mr_model->getMarginRateInfo($param_info);
        if (empty($margin_rate_info)) {
            return response()->json(['code' => '1002', 'msg' => '自采毛利率id错误,请检查']);
        }

        $margin_rate_list = $mr_model->delMarginRate($param_info);
        $return_info = ['code' => '1003', 'msg' => '删除自采毛利率失败'];
        if ($margin_rate_list !== false) {
            //更新redis中的自采毛利率信息
            $field = ['pick_margin_rate'];
            $marginRateInfo = DB::table('margin_rate')->select($field)->orderBy('pick_margin_rate', 'ASC')
                ->get()->map(function ($value) {
                    return (array)$value;
                })->toArray();
            Redis::set('marginRateInfo', json_encode($marginRateInfo, JSON_UNESCAPED_UNICODE));
            $return_info = ['code' => '1000', 'msg' => '删除自采毛利率成功'];
        }
        return response()->json($return_info);
    }

    /**
     * description 商品模块-单个新增常备商品
     * author zhangdong
     * date 2019.09.16
     */
    public function addStandbyGoods(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->addStandbyGoodsParams($reqParams);
        $platformBarcode = trim($reqParams['platform_barcode']);
        //检查新增商品是否在系统中存在
        $gcModel = new GoodsCodeModel();
        $specSn = $gcModel->getSpecSn($platformBarcode);
        if (empty($specSn)) {
            return response()->json(['code' => '2067', 'msg' => '该商品不存在，请先新增']);
        }
        //检查新增商品是否已经在常备商品中存在
        $sgModel = new StandbyGoodsModel();
        $count = $sgModel->countStandbyGoods($specSn);
        if ($count > 0) {
            return response()->json(['code' => '2067', 'msg' => '请勿重复新增商品']);
        }
        //组装并写入常备商品表
        $reqParams['spec_sn'] = $specSn;
        $insertData = $sgModel->makeInsertData($reqParams);
        $insertRes = $sgModel->insertData($insertData);
        $msg = ['code' => '2023', 'msg' => '操作失败'];
        if ($insertRes) {
            $msg = ['code' => '2024', 'msg' => '操作成功'];
        }
        return response()->json($msg);
    }

    /**
     * description 商品模块-批量新增常备商品
     * author zhangdong
     * date 2019.09.16
     */
    public function uploadStandbyGoods()
    {
        $file = $_FILES;
        //检查上传文件是否合格
        $executeExcel = new ExcuteExcel();
        $fileName = '常备商品导入模板';
        $res = $executeExcel->verifyUploadFile($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        //检查字段名称
        $arrTitle = [
            '商品名称', '平台条码', '最大采购量',
        ];
        foreach ($arrTitle as $title) {
            if (!in_array(trim($title), $res[0])) {
                $returnMsg = ['code' => '2009', 'msg' => '您的标题头有误，请按模板导入'];
                return response()->json($returnMsg);
            }
        }
        //校验上传数据
        $sgModel = new StandbyGoodsModel();
        $checkGoodsRes = $sgModel->checkUploadData($res);

        //检查重复的sku
        $repeatSku = filter_duplicate($checkGoodsRes['standbyGoods'], 'platform_barcode');
        if (count($repeatSku) > 0) {
            $msg = '平台条码为 ' . implode(',', $repeatSku) . ' 的商品重复';
            return response()->json(['code' => '2067', 'msg' => $msg]);
        }

        if (count($checkGoodsRes['newGoods']) > 0) {
            $newId = implode($checkGoodsRes['newGoods'], ',');
            return response()->json([
                'code' => '2067',
                'msg' => '表格中第' . $newId . '行是新品，请新增后再导入'
            ]);
        }
        if (count($checkGoodsRes['existGoods']) > 0) {
            $existId = implode($checkGoodsRes['existGoods'], ',');
            return response()->json([
                'code' => '2067',
                'msg' => '表格中第' . $existId . '行已经是常备商品，请删除'
            ]);
        }
        if (count($checkGoodsRes['errorMaxNum']) > 0) {
            $errorMaxNumId = implode($checkGoodsRes['errorMaxNum'], ',');
            return response()->json([
                'code' => '2067',
                'msg' => '表格中第' . $errorMaxNumId . '行最大采购量异常'
            ]);
        }
        if (count($checkGoodsRes['errorGoodsName']) > 0) {
            $errorGoodsNameId = implode($checkGoodsRes['errorGoodsName'], ',');
            return response()->json([
                'code' => '2067',
                'msg' => '表格中第' . $errorGoodsNameId . '行商品名称不能为空'
            ]);
        }
        //保存数据
        $insertRes = $sgModel->insertData($checkGoodsRes['standbyGoods']);
        $returnMsg = ['code' => '2023', 'msg' => '操作失败'];
        if ($insertRes) {
            $returnMsg = ['code' => '2024', 'msg' => '操作成功'];
        }
        return response()->json($returnMsg);

    }//end of function uploadStandbyGoods

    /**
     * description 商品模块-常备商品列表
     * editor zongxing
     * date 2019.09.17
     * type GET
     * return Array
     */
    public function standbyGoodsList(Request $request)
    {
        $param_info = $request->toArray();
        $sg_model = new StandbyGoodsModel();
        $standby_goods_list = $sg_model->standbyGoodsList($param_info, 1);
        if (empty($standby_goods_list['data'])) {
            return response()->json(['code' => '1002', 'msg' => '暂无常备商品']);
        }
        $return_info = ['code' => '1000', 'msg' => '获取常备商品列表成功', 'data' => $standby_goods_list];
        return response()->json($return_info);
    }

    /**
     * description 商品模块-常备商品详情
     * editor zongxing
     * date 2019.09.17
     * type GET
     * return Array
     */
    public function standbyGoodsInfo(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['id'])) {
            return response()->json(['code' => '1003', 'msg' => '常备商品id不能为空']);
        }
        $sg_model = new StandbyGoodsModel();
        $standby_goods_info = $sg_model->standbyGoodsInfo($param_info);
        if (empty($standby_goods_info)) {
            return response()->json(['code' => '1004', 'msg' => '常备商品id错误']);
        }
        $return_info = ['code' => '1000', 'msg' => '获取常备商品详情成功', 'data' => $standby_goods_info];
        return response()->json($return_info);
    }

    /**
     * description 商品模块-编辑常备商品
     * editor zongxing
     * date 2019.09.17
     * type GET
     * return boolean
     */
    public function doEditStandbyGoods(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['id'])) {
            return response()->json(['code' => '1003', 'msg' => '常备商品id不能为空']);
        } elseif (!isset($param_info['max_num']) && !isset($param_info['is_purchase'])) {
            return response()->json(['code' => '1004', 'msg' => '最大可采量和采购状态不能同时为空']);
        }
        $sg_model = new StandbyGoodsModel();
        $standby_goods_info = $sg_model->standbyGoodsInfo($param_info);
        if (empty($standby_goods_info)) {
            return response()->json(['code' => '1005', 'msg' => '常备商品id错误']);
        }
        $max_num = intval($standby_goods_info['max_num']);
        $available_num = intval($standby_goods_info['available_num']);
        if (isset($param_info['is_purchase']) && $param_info['is_purchase'] == 1) {
            if ($max_num <= $available_num) {
                return response()->json(['code' => '1006', 'msg' => '请先修改最大可采量']);
            }
        }
        $res = $sg_model->doEditStandbyGoods($param_info, $standby_goods_info);
        $return_info = ['code' => '1002', 'msg' => '编辑常备商品失败'];
        if ($res !== false) {
            $standby_goods_info = $sg_model->standbyGoodsInfo($param_info);
            $return_info = ['code' => '1000', 'msg' => '编辑常备商品成功', 'data' => $standby_goods_info];
        }
        return response()->json($return_info);
    }

    /**
     * description:下载常备商品数据
     * editor:zongxing
     * type:GET
     * date : 2019.09.19
     * return excel
     */
    public function downLoadStandbyGoodsInfo()
    {
        $param_info = [
            'is_purchase' => 1,
            'no_zero' => 1,
        ];
        $sg_model = new StandbyGoodsModel();
        $standby_goods_list = $sg_model->standbyGoodsList($param_info);
        if (empty($standby_goods_list)) {
            return response()->json(['code' => '1002', 'msg' => '暂无常备商品']);
        }
        $obpe = new PHPExcel();
        $obpe->setActiveSheetIndex(0);
        $currentSheet = $obpe->getActiveSheet();
        //设置采购渠道及列宽
        $currentSheet->setCellValue('A1', '商品规格码')->getColumnDimension('A')->setWidth(20);
        $currentSheet->setCellValue('B1', '商品参考码')->getColumnDimension('B')->setWidth(20);
        $currentSheet->setCellValue('C1', '商家编码')->getColumnDimension('C')->setWidth(20);
        $currentSheet->setCellValue('D1', '商品名称')->getColumnDimension('D')->setWidth(20);
        $currentSheet->setCellValue('E1', '美金原价')->getColumnDimension('E')->setWidth(15);
        $currentSheet->setCellValue('F1', 'Livp价')->getColumnDimension('F')->setWidth(15);
        $currentSheet->setCellValue('G1', '实付美金')->getColumnDimension('G')->setWidth(15);
        $currentSheet->setCellValue('H1', '最大采购量')->getColumnDimension('H')->setWidth(15);
        $currentSheet->setCellValue('I1', '可用采购量')->getColumnDimension('I')->setWidth(15);
        $currentSheet->setCellValue('J1', '缺口数')->getColumnDimension('J')->setWidth(15);
        $currentSheet->setCellValue('K1', '需求总额')->getColumnDimension('K')->setWidth(15);
        $currentSheet->setCellValue('L1', '缺口总额')->getColumnDimension('L')->setWidth(15);
        $currentSheet->setCellValue('M1', '采满率')->getColumnDimension('M')->setWidth(15);

        //获取最大行数
        $row_total_i = count($standby_goods_list) + 1;
        for ($i = 0; $i < $row_total_i; $i++) {
            if ($i == 0) continue;
            $row_i = $i + 1;
            $real_i = $i - 1;
            $max_num = intval($standby_goods_list[$real_i]['max_num']);
            $available_num = intval($standby_goods_list[$real_i]['available_num']);
            $diff_num = $max_num - $available_num;
            $spec_price = floatval($standby_goods_list[$real_i]['spec_price']);
            $sg_demand_price = $max_num * $spec_price;
            $sg_diff_price = $diff_num * $spec_price;
            $sg_real_rate = 100;
            if ($max_num) {
                $sg_real_rate = floatval(number_format($available_num / $max_num * 100, 2, ',', ''));
            }
            $currentSheet->setCellValue('A' . $row_i, $standby_goods_list[$real_i]['spec_sn']);
            $currentSheet->setCellValue('B' . $row_i, $standby_goods_list[$real_i]['erp_ref_no']);
            $currentSheet->setCellValue('C' . $row_i, $standby_goods_list[$real_i]['erp_merchant_no']);
            $currentSheet->setCellValue('D' . $row_i, $standby_goods_list[$real_i]['goods_name']);
            $currentSheet->setCellValue('E' . $row_i, $spec_price);
            $currentSheet->setCellValue('F' . $row_i, $spec_price);
            $currentSheet->setCellValue('G' . $row_i, $spec_price);
            $currentSheet->setCellValue('H' . $row_i, $max_num);
            $currentSheet->setCellValue('I' . $row_i, $available_num);
            $currentSheet->setCellValue('J' . $row_i, $diff_num);
            $currentSheet->setCellValue('K' . $row_i, $sg_demand_price);
            $currentSheet->setCellValue('L' . $row_i, $sg_diff_price);
            $currentSheet->setCellValue('M' . $row_i, $sg_real_rate . '%');
        }

        //赋值最后三列数据
        $column_last_name = $currentSheet->getHighestColumn();
        $last_column_num = \PHPExcel_Cell::columnIndexFromString($column_last_name);
        for ($m = 0; $m < 4; $m++) {
            $real_column_num = $last_column_num + $m;
            $column_weight = 15;
            if ($m == 0) {
                $column_name_value = '外采折扣';
            } elseif ($m == 1) {
                $column_name_value = '采购数量';
            } elseif ($m == 2) {
                $column_name_value = '是否为搭配(是/否)';
            } elseif ($m == 3) {
                $column_name_value = '搭配商品对应的规格码';
                $column_weight = 25;
            }
            $real_column_name = \PHPExcel_Cell::stringFromColumnIndex($real_column_num);
            $currentSheet->setCellValue($real_column_name . '1', $column_name_value)
                ->getColumnDimension($real_column_name)->setWidth($column_weight);
        }

        //改变表格标题样式
        $column_last_name = $currentSheet->getHighestColumn();
        $row_last_num = $currentSheet->getHighestRow();
        $commonModel = new CommonModel();
        $commonModel->changeTableTitle($obpe, 'A', 1, $column_last_name, 1);
        $commonModel->changeTableContent($obpe, 'A', 2, $column_last_name, $row_last_num);
        $currentSheet->setTitle('采购数据表');

        //清除缓存
        ob_end_clean();
        //写入内容
        $obwrite = \PHPExcel_IOFactory::createWriter($obpe, 'Excel2007');
        $str = rand(1000, 9999);
        $filename = '采购数据表_';
        $filename = $filename . '_' . $str . '.xls';

        //保存文件
        //$obwrite->save($filename);

        //直接在浏览器输出
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Content-Type:application/force-download');
        header('Content-Type:application/vnd.ms-execl');
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header("Content-Disposition:attachment;filename=$filename");
        header('Content-Transfer-Encoding:binary');
        $obwrite->save('php://output');

        $code = "1000";
        $msg = "文件下载成功";
        $return_info = compact('code', 'msg');
        return response()->json($return_info);
    }

    /**
     * description 常备商品采购数据上传
     * editor zongxing
     * type POST
     * date 2019.05.17
     * params 1.需要上传的excel表格文件:purchase_data;3.采购方式id:method_id;4.采购渠道id:channels_id;
     *          5.自提或邮寄id:path_way;6.港口id:port_id;7.用户账号:user_id
     * return Object
     */
    public function uploadStandbyGoodsData(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            //检查上传数据参数
            ParamsCheckSingle::paramsCheck()->uploadStandbyGoodsDataParams($param_info);
            $param_info['sum_demand_sn'] = 'standby';
            //检查上传文件是否合格
            $file = $_FILES;
            $excuteExcel = new ExcuteExcel();
            $fileName = '采购数据表_';//要上传的文件名，将对上传的文件名做比较
            $res = $excuteExcel->verifyUploadFileZ($file, $fileName);
            if (isset($res['code'])) {
                return response()->json($res);
            }
            //检查字段名称
            $arrTitle = ['商品规格码', '美金原价', 'Livp价', '实付美金', '外采折扣', '采购数量', '是否为搭配(是/否)', '搭配商品对应的规格码'];
            foreach ($arrTitle as $title) {
                if (!in_array(trim($title), $res[0])) {
                    return response()->json(['code' => '1001', 'msg' => '您的标题头有误，请按模板导入']);
                }
            }
            //整合上传的采购数据
            $upload_goods_info = [];
            $upload_spec_sn = [];
            $wai_discount_key = $spec_price_key = $pay_price_key = $buy_num_key = $is_match_key = $ps_sn_key = 0;
            foreach ($res as $k => $v) {
                if ($k == 0) {
                    $wai_discount_key = array_keys($v, '外采折扣')[0];
                    $spec_price_key = array_keys($v, '美金原价')[0];
                    $lvip_price_key = array_keys($v, 'Livp价')[0];
                    $pay_price_key = array_keys($v, '实付美金')[0];
                    $buy_num_key = array_keys($v, '采购数量')[0];
                    $is_match_key = array_keys($v, '是否为搭配(是/否)')[0];
                    $ps_sn_key = array_keys($v, '搭配商品对应的规格码')[0];
                }
                if ($k < 1 || empty($v[0])) continue;
                $spec_sn = trim($v[0]);
                if (!isset($v[$buy_num_key]) || intval($v[$buy_num_key]) == 0) continue;
                $day_buy_num = intval($v[$buy_num_key]);
                $is_match = trim($v[$is_match_key]) == '是' ? 1 : 0;
                $upload_goods_info[$spec_sn] = [
                    'spec_price' => floatval($v[$spec_price_key]),
                    'lvip_price' => floatval($v[$lvip_price_key]),
                    'pay_price' => floatval($v[$pay_price_key]),
                    'day_buy_num' => $day_buy_num,
                    'is_match' => $is_match,
                    'parent_spec_sn' => trim($v[$ps_sn_key]),
                ];
                $upload_spec_sn[] = $spec_sn;
                if (!empty($v[$wai_discount_key])) {
                    $upload_goods_info[$spec_sn]['wai_discount'] = floatval($v[$wai_discount_key]);
                }
            }
            if (empty($upload_goods_info)) {
                return response()->json(['code' => '1002', 'msg' => '您上传的采购数据为空,请重新确认']);
            }
            //检查上传数据
            $check_res = $this->checkUploadBatchData($param_info, $upload_spec_sn);
            if (isset($check_res['code'])) {
                return response()->json($check_res);
            }
            $channels_info = $check_res['channels_info'];
            $original_or_discount = $channels_info['original_or_discount'];
            $standby_goods_list = $check_res['standby_goods_list'];
            $sg_spec_sn = [];
            $final_upload_goods = [];
            foreach ($standby_goods_list as $k => $v) {
                $spec_sn = $v['spec_sn'];
                $sg_spec_sn[] = $spec_sn;
                //如果商品在常备商品中,则进行赋值
                if (in_array($spec_sn, $upload_spec_sn)) {
                    $spec_price = floatval($upload_goods_info[$spec_sn]['spec_price']);
                    $lvip_price = floatval($upload_goods_info[$spec_sn]['lvip_price']);
                    $lvip_discount = $original_or_discount == 1 ? $lvip_price / $spec_price : 1;
                    $tmp_arr = [
                        'spec_sn' => $v['spec_sn'],
                        'goods_name' => $v['goods_name'],
                        'erp_prd_no' => $v['erp_prd_no'],
                        'erp_merchant_no' => $v['erp_merchant_no'],
                        'erp_ref_no' => $v['erp_ref_no'],
                        'brand_id' => $v['brand_id'],
                        'lvip_discount' => $lvip_discount,
                    ];
                    $final_upload_goods[] = array_merge($tmp_arr, $upload_goods_info[$spec_sn]);
                }
            }
            if (count($upload_spec_sn) != count($sg_spec_sn)) {
                $diff_spec = array_diff($upload_spec_sn, $sg_spec_sn);
                $error_info = '';
                foreach ($diff_spec as $k => $v) {
                    $error_info .= $v . ',';
                }
                $error_info = '您上传的商品中: ' . substr($error_info, 0, -1) . ' 在系统常备商品中未找到或停止采购';
                return response()->json(['code' => '1003', 'msg' => $error_info]);
            }
            //获取上传时的采购渠道
            $channels_id = $param_info['channels_id'];
            $buy_time = $param_info['buy_time'];
            $dt_model = new DiscountTypeModel();
            $goods_discount_list = $dt_model->getFinalDiscount($final_upload_goods, $channels_id, $buy_time);
            //获取采购成本折扣数据
            $param = [
                'channels_id' => $channels_id,
                'buy_time' => $buy_time,
            ];
            // $discountModel = new DiscountModel();
            // $discount_list = $discountModel->getTotalDiscount($param);
            // if (isset($discount_list['code'])) {
            //     return response()->json($upload_goods_info);
            // }
            $upload_goods_info = $this->createGoodsDiscount($goods_discount_list);
            if (isset($upload_goods_info['code'])) {
                return response()->json($upload_goods_info);
            }
            //获取采购港口简称
            $port_sn = $check_res['port_sn'];
            //组装组合方式
            $sum_demand_sn = trim($param_info['sum_demand_sn']);
            $path_way = $param_info['path_way'];
            $supplier_id = $param_info['supplier_id'];
            $delivery_time = trim($param_info['delivery_time']);
            $arrive_time = trim($param_info['arrive_time']);
            $integral_time = trim($param_info['integral_time']);
            $buy_time = trim($param_info['buy_time']);
            if ($path_way == 0) {
                $path_sn = 'ZT';
            } elseif ($path_way == 1) {
                $path_sn = 'YJ';
            }
            //获取上传时的采购方式
            $method_info = $check_res['method_info'];
            $method_sn = $method_info['method_sn'];
            //获取上传时的采购渠道
            $channels_sn = $channels_info['channels_sn'];
            $group_sn = $sum_demand_sn . '-' . $method_sn . '-' . $channels_sn . '-' . $path_sn . '-' . $port_sn . '-' .
                $supplier_id . '-' . $delivery_time . '-' . $arrive_time . '-' . $integral_time . '-' . $buy_time;
            $param_info['batch_cat'] = 3;
            $data_model = new DataModel();
            $return_info = $data_model->uploadBatchData($param_info, $group_sn, $upload_goods_info);
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:对商品和折扣信息进行整合
     * editor:zongxing
     * type:POST
     * date : 2019.05.27
     * return Array
     */
    public function createGoodsDiscount($goods_discount_list)
    {
        $error_info = '';
        foreach ($goods_discount_list as $k => $v) {
            $spec_sn = $v['spec_sn'];
            $real_discount = $channel_discount = $pay_discount = 1;
            //如果提供了外采折扣，则直接使用外采折扣
            if (isset($v['wai_discount'])) {
                $wai_discount = floatval($v['wai_discount']);
                $goods_discount_list[$k]['channel_discount'] = $wai_discount;
                $goods_discount_list[$k]['real_discount'] = $wai_discount;
                $goods_discount_list[$k]['real_discount'] = $wai_discount;
                continue;
            }
            //如果不存在折扣信息，则返回错误提示
            if (!isset($v['channels_info'])) {
                $error_info .= $spec_sn . ',';
                continue;
            }
            foreach ($v['channels_info'] as $k1 => $v1) {
                $real_discount = floatval($v1['brand_discount']);
                $channel_discount = floatval($v1['cost_discount']);
                $pay_discount = floatval($v1['pay_discount']);
            }
            unset($goods_discount_list[$k]['channels_info']);
            $goods_discount_list[$k]['channel_discount'] = $channel_discount;
            $goods_discount_list[$k]['real_discount'] = $real_discount;
            $goods_discount_list[$k]['pay_discount'] = $pay_discount;
        }
        if (!empty($error_info)) {
            $error_info = '您上传的商品: ' . $error_info . ' 在所选渠道中不存在折扣';
            $error_arr = ['code' => '2001', 'msg' => $error_info];
            return $error_arr;
        }
        return $goods_discount_list;
    }

    /**
     * description:对商品和折扣信息进行整合
     * editor:zongxing
     * type:POST
     * date : 2019.05.27
     * return Array
     */
    public function createGoodsDiscount_stop($goods_discount_list, $discount_list)
    {
        //组装品牌折扣信息
        $brand_discount_list = [];
        foreach ($discount_list as $k => $v) {
            foreach ($v as $k1 => $v1) {
                $pin_str = $v1['channels_name'] . '-' . $v1['method_name'];
                $brand_discount_list[$k][$pin_str] = floatval($v1['brand_discount']);
            }
        }
        $error_info = '';
        foreach ($goods_discount_list as $k => $v) {
            $spec_sn = $v['spec_sn'];
            $lvip_discount = $v['lvip_discount'];
            $real_discount = 1;
            $channel_discount = 1;
            $brand_id = floatval($v['brand_id']);
            if (!isset($v['channels_info'])) {
                $error_info .= $spec_sn . ',';
                continue;
            }
            foreach ($v['channels_info'] as $k1 => $v1) {
                $brand_discount = floatval($v1['brand_discount']);
                if ($brand_discount < $real_discount) {
                    $real_discount = $brand_discount;
                }
                $pin_str = $v1['channels_name'] . '-' . $v1['method_name'];
                if (isset($brand_discount_list[$brand_id][$pin_str])) {
                    $tmp_channel_discount = $brand_discount_list[$brand_id][$pin_str];
                    $channel_discount = $tmp_channel_discount;
                }
            }
            unset($goods_discount_list[$k]['channels_info']);
            $goods_discount_list[$k]['channel_discount'] = $channel_discount;
            $real_discount = (1 - $lvip_discount) * $channel_discount + $lvip_discount * $real_discount;
            $goods_discount_list[$k]['real_discount'] = $real_discount;
        }
        if (!empty($error_info)) {
            $error_info = '您上传的商品: ' . $error_info . ' 在所选渠道中不存在折扣';
            $error_arr = ['code' => '2001', 'msg' => $error_info];
            return $error_arr;
        }
        return $goods_discount_list;
    }

    /**
     * description:检查上传数据参数
     * editor:zongxing
     * type:POST
     * date : 2019.09.19
     * return Array
     */
    public function checkUploadBatchData($param_info, $upload_spec_sn)
    {
        //获取常备单商品数据
        $param_info['is_purchase'] = 1;
        $sg_model = new StandbyGoodsModel();
        $standby_goods_list = $sg_model->standbyGoodsList($param_info, 0, $upload_spec_sn);
        if (empty($standby_goods_list)) {
            return ['code' => '1101', 'msg' => '暂无常备商品'];
        }
        //检查上传时采购方式
        $purchase_mothod_model = new PurchaseMethodModel();
        $method_info = $purchase_mothod_model->checkUploadPurchaseMethod($param_info);
        if (empty($method_info)) {
            return ['code' => '1102', 'msg' => '您选择的采购方式有误,请重新确认'];
        }
        //检查上传时采购渠道
        $purchase_channel_model = new PurchaseChannelModel();
        $channels_info = $purchase_channel_model->checkUploadPurchaseChannel($param_info);
        if (empty($channels_info)) {
            return ['code' => '1103', 'msg' => '您选择的采购渠道有误,请重新确认'];
        }
        //检查上传时采购港口信息
        $real_purchase_model = new RealPurchaseModel();
        $port_id = intval($param_info['port_id']);
        $port_sn = $real_purchase_model->getPortSn($port_id);
        if (!$port_sn) {
            return ['code' => '1104', 'msg' => '您选择的采购港口信息有误,请重新确认'];
        }
        $task_id = intval($param_info['task_id']);
        $task_model = new TaskModel();
        $task_info = $task_model->getTaskInfoById($task_id);
        if (empty($task_info)) {
            return ['code' => '1105', 'msg' => '您选择的任务模板信息有误,请重新确认'];
        }
        $return_info = [
            'standby_goods_list' => $standby_goods_list,
            'method_info' => $method_info,
            'channels_info' => $channels_info,
            'port_sn' => $port_sn,
            'task_info' => $task_info,
        ];
        return $return_info;
    }

    /**
     * description 获取指定商品最终折扣
     * editor zongxing
     * date 2019.10.19
     */
    public function getGoodsFinalDiscount(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['query_sn'])) {
            return response()->json(['code' => '1002', 'msg' => '商品字段不能为空']);
        }
        $usd_cny_rate = !empty($param_info['usd_cny_rate']) ? floatval($param_info['usd_cny_rate']) : 1;//美金对人民币汇率
        $usd_krw_rate = !empty($param_info['usd_krw_rate']) ? floatval($param_info['usd_krw_rate']) : 1;//美金对韩币汇率
        $cny_krw_rate = !empty($param_info['cny_krw_rate']) ? floatval($param_info['cny_krw_rate']) : 1;//人民币对韩币汇率
        $freight = !empty($param_info['freight']) ? floatval($param_info['freight']) : 3.5;//运费
        $margin_rate = !empty($param_info['margin_rate']) ? floatval($param_info['margin_rate']) / 100 : 0.08;//毛利
        //获取商品数据
        $goods_model = new GoodsModel();
        $goods_info = $goods_model->searchGoodsInfo($param_info);
        if (empty($param_info['query_sn'])) {
            return response()->json(['code' => '1001', 'msg' => '暂无商品数据']);
        }
        //获取采购最终折扣数据
        $buy_time = date('Y-m-d');
        $dt_model = new DiscountTypeModel();
        $goods_discount_list = $dt_model->getFinalDiscount($goods_info, null, $buy_time);
        foreach ($goods_discount_list as $k => $v) {
            $spec_price = floatval($v['spec_price']);
            $spec_weight = floatval($v['spec_weight']) > 0 ? floatval($v['spec_weight']) : floatval($v['estimate_weight']);
            $goods_discount_list[$k]['spec_weight'] = $spec_weight;
            if (isset($v['channels_info'])) {
                $channel_name_info = [];
                foreach ($v['channels_info'] as $k1 => $v1) {
                    if (!in_array($v1['channels_name'], $channel_name_info)) {
                        $channel_name_info[] = $v1['channels_name'];
                    }
                    //是否为高价sku
                    $v['channels_info'][$k1]['is_high'] = $v1['is_high'] == 1 ? '是' : '否';
                    //成本折扣
                    $cost_discount = floatval($v1['cost_discount']);
                    $cost_port_discount = 0;
                    if ($spec_price) {
                        $cost_port_discount = ($cost_discount * $spec_price + $spec_weight * $freight) / $spec_price * 100;
                    }
                    $cost_port_price = $cost_discount * $spec_price + $spec_weight * $freight;
                    $v['channels_info'][$k1]['cost_port_discount'] = number_format($cost_port_discount, 2);
                    $v['channels_info'][$k1]['cost_port_price'] = number_format($cost_port_price, 2);
                    $v['channels_info'][$k1]['cost_port_ren_price'] = number_format($cost_port_price * $usd_cny_rate, 2);
                    //最终折扣
                    $final_discount = floatval($v1['brand_discount']);
                    $final_port_discount = 0;
                    if ($spec_price) {
                        $final_port_discount = ($final_discount * $spec_price + $spec_weight * $freight) / $spec_price * 100;
                    }
                    $final_port_price = $final_discount * $spec_price + $spec_weight * $freight;
                    $v['channels_info'][$k1]['final_port_discount'] = number_format($final_port_discount, 2);
                    $v['channels_info'][$k1]['final_port_price'] = number_format($final_port_price, 2);
                    $v['channels_info'][$k1]['final_port_ren_price'] = number_format($final_port_price * $usd_cny_rate, 2);
                    //报价折扣
                    $sale_discount = floatval($v1['brand_discount']) + $margin_rate;
                    $sale_port_discount = 0;
                    if ($spec_price) {
                        $sale_port_discount = ($sale_discount * $spec_price + $spec_weight * $freight) / $spec_price * 100;
                    }
                    $sale_port_price = $sale_discount * $spec_price + $spec_weight * $freight;
                    $v['channels_info'][$k1]['sale_port_discount'] = number_format($sale_port_discount, 2);
                    $v['channels_info'][$k1]['sale_port_price'] = number_format($sale_port_price, 2);
                    $v['channels_info'][$k1]['sale_port_ren_price'] = number_format($sale_port_price * $usd_cny_rate, 2);
                    //毛利
                    $v['channels_info'][$k1]['margin_port_discount'] = number_format($margin_rate * 100, 2);
                    $margin_price = $sale_port_price - $final_port_price;
                    $v['channels_info'][$k1]['margin_port_price'] = number_format($margin_price, 2);
                    $v['channels_info'][$k1]['margin_port_ren_price'] = number_format($margin_price * $usd_cny_rate, 2);
                }
                $goods_discount_list[$k]['channel_name_info'] = $channel_name_info;
                $goods_discount_list[$k]['channels_info'] = array_values($v['channels_info']);
            }
        }

        //判断是否为详情页,如果为详情,则生成图片
        $path = '';
        $is_detail = empty($param_info['is_detail']) ? 0 : 1;
        if ($is_detail == 1) {
            $save_path = './salePicture';
            $path = $this->createSalePicture($save_path, $goods_discount_list[0]);
        }
        $return_info = ['code' => '1000', 'msg' => '获取指定商品最终折扣成功', 'path' => $path, 'data' => $goods_discount_list];
        return response()->json($return_info);
    }

    /**
     * 保存报价图片
     * @param $save_path 报价图片保存路径
     * @param $data 商品数据
     */
    public function createSalePicture($save_path, $data)
    {
        if (!file_exists($save_path)) {
            mkdir($save_path, 666, true);
        }
        $imageManager = new Image();
        $fontPath = public_path('font/HYXiaoBo.ttf');

        $image = $imageManager::canvas(450, 600, '#fff');
        //插入商品基础信息
        $menu_col_width = '30';
        $content_col_width = '110';
        $start_row = 40;
        $row_step = 25;
        $fontSize = 13;
        $image->text('商品名称:', $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $image->text($data['goods_name'], $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $start_row += $row_step;
        $image->text('商品规格码:', $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $image->text($data['spec_sn'], $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $start_row += $row_step;
        $image->text('商家编码:', $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $image->text($data['erp_merchant_no'], $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $start_row += $row_step;
        $image->text('商品代码:', $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $image->text($data['erp_prd_no'], $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $start_row += $row_step;
        $image->text('参考码:', $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $image->text($data['erp_ref_no'], $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $start_row += $row_step;
        $image->text('美金原价:', $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $image->text($data['spec_price'], $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $start_row += $row_step;
        $image->text('折扣信息:', $menu_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->valign('left');
        });
        $image->text('渠道名称', $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
            $font->file($fontPath);
            $font->size($fontSize);
            $font->align('center');
            $font->valign('valign');
        });
        $channel_col_width = $content_col_width + 150;
        $col_step = 60;
        $channel_name_info = $data['channel_name_info'];
        foreach ($channel_name_info as $k => $v) {
            $image->text($v, $channel_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
                $font->align('center');
                $font->valign('valign');
            });
            $channel_col_width += $col_step;
        }
        $start_row += $row_step;

        $column_info = [
            'cost_discount' => '成本折扣',
            'brand_discount' => '最终折扣',
            'is_high' => '是否为高价',
            'cost_port_discount' => '成本折扣最终到港折扣率',
            'cost_port_price' => '成本折扣最终到港美金',
            'cost_port_ren_price' => '成本折扣最终到港人民币',
            'final_port_discount' => '最终折扣最终到港折扣率',
            'final_port_price' => '最终折扣最终到港美金',
            'final_port_ren_price' => '最终折扣最终到港人民币',
            'sale_port_discount' => '报价折扣最终到港折扣率',
            'sale_port_price' => '报价折扣最终到港美金',
            'sale_port_ren_price' => '报价折扣最终到港人民币',
            'margin_port_discount' => '最终到港毛利率',
            'margin_port_price' => '毛利率最终到港美金',
            'margin_port_ren_price' => '毛利率最终到港人民币',
        ];
        $channels_info = $data['channels_info'];
        foreach ($column_info as $k => $v) {
            $image->text($v, $content_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
                $font->align('center');
                $font->valign('valign');
            });
            $dis_col_width = $content_col_width + 150;
            $col_step = 60;
            foreach ($channels_info as $k1 => $v1) {
                $image->text($v1[$k], $dis_col_width, $start_row, function ($font) use ($fontPath, $fontSize) {
                    $font->file($fontPath);
                    $font->size($fontSize);
                    $font->align('center');
                    $font->valign('valign');
                });
                $dis_col_width += $col_step;
            }
            $start_row += $row_step;
        }
        $spec_sn = $data['spec_sn'];
        $save_path .= '/' . $spec_sn . '.jpg';
        $image->save($save_path);
        $path = substr($save_path, 1);
        return $path;
    }

    /**
     * description 补全商品代码到对照表中
     * author zhangdong
     * date 2019.11.06
     */
    public function insertErpPrdNo(Request $request)
    {
        $goodsModel = new GoodsModel();
        //处理商品代码-将商品代码维护到对照表中
        $insertRes = $goodsModel->operateErpPrdNo();
        if (count($insertRes) == 0) {
            return response()->json(['code' => '2067', 'msg' => '没有需要维护的商品代码']);
        }
        $returnMsg = ['code' => '1000', 'msg' => '商品代码维护失败'];
        if ($insertRes) {
            $returnMsg = ['code' => '1000', 'msg' => '商品代码维护成功'];
        }
        return response()->json($returnMsg);
    }

    /**
     * description 获取购物车商品列表
     * editor zongxing
     * type GET
     * date 2019.11.12
     * return Array
     */
    public function getShopCartList(Request $request)
    {
        $param_info = $request->toArray();
        $sc_model = new ShopCartModel();
        $param_info['limit'] = 1;
        $shop_cart_list = $sc_model->getShopCartList($param_info);
        if (empty($shop_cart_list['data'])) {
            return response()->json(['code' => '1002', 'msg' => '暂无购物车商品信息']);
        }
        $return_info = ['code' => '1000', 'msg' => '获取购物车商品列表成功', 'data' => $shop_cart_list];
        return response()->json($return_info);
    }

    /**
     * description 上传购物车商品
     * editor zongxing
     * type GET
     * date 2019.11.12
     * return Array
     */
    public function uploadShopCartGoods(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['upload_file'])) {
            return response()->json(['code' => '1002', 'msg' => '上传文件不能为空']);
        } elseif (empty($param_info['cart_date'])) {
            return response()->json(['code' => '1003', 'msg' => '购物车日期不能为空']);
        }
        //检查上传文件是否合格
        $upload_file = $_FILES;
        $excuteExcel = new ExcuteExcel();
        $fileName = '上传购物车商品模板';//要上传的文件名，将对上传的文件名做比较
        $res = $excuteExcel->verifyUploadFileZ($upload_file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        $arrTitle = ['品牌ID', '商品品牌', '商家编码', '参考码', '商品代码', '商品分类', '搭配比例'];
        foreach ($arrTitle as $title) {
            $title = trim($title);
            if (!in_array($title, $res[0])) {
                return response()->json(['code' => '1006', 'msg' => '您的标题头有误，请按模板导入']);
            }
        }
        //整理上传商品信息（上传购物车商品）
        $sc_model = new ShopCartModel();
        $upload_info = $sc_model->createShopCartGoodsInfo($res, $arrTitle);
        if (isset($upload_info['code'])) {
            return response()->json($upload_info);
        }
        $upload_goods_info = $upload_info['upload_goods_info'];
        $diff_goods_info = $upload_info['diff_goods_info'];
        //购物车商品维护
        $res = $sc_model->uploadShopCartGoods($param_info, $upload_goods_info);
        $return_info = ['code' => '1007', 'msg' => '商品渠道追加折扣维护失败'];
        if ($res !== false && empty($diff_goods_info)) {
            $return_info = ['code' => '1000', 'msg' => '商品渠道追加折扣维护成功'];
        }
        if (!empty($diff_goods_info)) {
            $return_info = ['code' => '1001', 'msg' => '商品渠道追加折扣维护成功,存在新商品', 'data' => $diff_goods_info];
        }
        return response()->json($return_info);
    }

    /**
     * description 下载购物车商品中的新品列表
     * editor zongxing
     * type GET
     * date 2019.11.13
     * return Array
     */
    public function downloadShopDiffGoods(Request $request)
    {
        $title[] = ['品牌ID', '商品品牌', '商家编码', '参考码', '商品代码', '商品分类'];
        $param_info = $request->toArray();
        if (empty($param_info['diff_goods_info'])) {
            return response()->json(['code' => '1002', 'msg' => '购物车新品不能为空']);
        }
        $diff_goods_info = json_decode(json_encode($param_info['diff_goods_info']), true);
        //$diff_goods_info = json_decode($param_info['diff_goods_info'], true);
        $new_goods_list = [];
        foreach ($diff_goods_info as $k => $v) {
            $new_goods_list[] = [
                'brand_id' => '',
                'brand_name' => trim($v['brand_name']),
                'erp_merchant_no' => trim($v['erp_merchant_no']),
                'erp_ref_no' => trim($v['erp_ref_no']),
                'erp_prd_no' => trim($v['erp_prd_no']),
                'cat_name' => trim($v['cat_name']),
            ];
        }
        $filename = '购物车需要新增的商品_' . date('Y-m-d');
        $exportData = array_merge($title, $new_goods_list);
        $excel_obj = new ExcuteExcel();
        $excel_obj->exportZ($exportData, $filename);
    }

    /**
     * description 客户订单报价-上传需要报价的SKU
     * author zhangdong
     * type post
     * date 2019.11.25
     */
    public function offerSkuUpload(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->offerSkuUploadParams($reqParams);
        $file = $_FILES;
        //检查上传文件是否合格
        $executeExcel = new ExcuteExcel();
        $fileName = '报价SKU导入模板';
        $res = $executeExcel->verifyUploadFile($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        //检查字段名称
        $arrTitle = [
            '商品名称', '平台条码', '美金原价', 'EXW折扣', '销售折扣',
        ];
        foreach ($arrTitle as $title) {
            if (!in_array(trim($title), $res[0])) {
                $returnMsg = ['code' => '2009', 'msg' => '您的标题头有误，请按模板导入'];
                return response()->json($returnMsg);
            }
        }
        //对上传的数据进行校验，如果有新品则告知用户并且将新品保存
        $goodsModel = new GoodsModel();
        $checkGoodsRes = $goodsModel->checkOfferGoods($res);
        if ($checkGoodsRes === false) {
            return response()->json(['code' => '2058', 'msg' => '上传文件中平台条码不能为空']);
        }
        if (count($checkGoodsRes['arrNewGoods']) > 0) {
            $strNewId = implode($checkGoodsRes['arrNewGoods'], ',');
            return response()->json(['code' => '2067', 'msg' => '表格中第' . $strNewId . '行是新品，请先新增']);
        }
        $goodsInfo = $checkGoodsRes['arrGoodsInfo'];
        //检查导入商品是否有重复的，如果有则返回重复SKU
        $filter_field = 'platform_barcode';
        $uniqueGoodsInfo = filter_duplicate($goodsInfo, $filter_field);
        if (count($uniqueGoodsInfo) >= 1) {
            $duplicateGoods = implode($uniqueGoodsInfo, ',');
            $returnMsg = ['code' => '2059', 'msg' => '平台条码为 ' . $duplicateGoods . ' 的商品为重复数据'];
            return response()->json($returnMsg);
        }
        $ooModel = new OfferOrderModel();
        //组装报价单数据
        $offerData = $ooModel->createOrderData($goodsInfo);
        //写入报价单
        $saveRes = $ooModel->saveOfferData($offerData);
        $returnMsg = ['code' => '2000', 'msg' => '上传成功'];
        if ($saveRes == false) {
            $returnMsg = ['code' => '2001', 'msg' => '上传失败'];
        }
        return response()->json($returnMsg);
    }

    /**
     * description 客户订单报价-获取sku报价详情
     * author zhangdong
     * type post
     * date 2019.11.25
     */
    public function getOfferDetail(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->skuOfferParams($reqParams);
        $offerSn = trim($reqParams['offer_sn']);//报价单号
        //获取报价用的基本信息
        $goodsModel = new GoodsModel();
        $loginUserInfo = $request->user();
        //部门id
        $depart_id = intval($loginUserInfo->department_id);
        $offerInfo = $goodsModel->getOfferInfo($depart_id);
        //检查部门是否存在
        $curDepart = searchTwoArray($offerInfo['departmentInfo'], $depart_id, 'department_id');
        if (empty($curDepart)) {
            return response()->json(['code' => '2035', 'msg' => '部门信息有误，请联系管理员']);
        }
        //获取自采毛利率档位信息
        $pickMarginRate = $offerInfo['pickMarginRate'];
        $arrPickRate = [];
        foreach ($pickMarginRate as $item) {
            $arrPickRate[] = sprintf('%.0f%%', trim($item['pick_margin_rate']));
        }
        //获取费用信息
        $chargeInfo = $offerInfo['chargeInfo'];
        $arrCharge = [];
        $totalCharge = 0;
        $dtModel = new DiscountTypeModel();
        foreach ($chargeInfo as $item) {
            $arrCharge[] = [sprintf('%.0f%%', trim($item['charge_rate'])) => trim($item['charge_name'])];
            $totalCharge += $item['charge_rate'];
        }
        $arrCharge[] = [sprintf('%.0f%%', $totalCharge) => '费用合计'];
        //根据报价单号获取报价单基本信息
        $ooModel = new OfferOrderModel();
        $offerOrder = $ooModel->getOrderInfo($offerSn);
        //根据报价单号获取报价单商品信息
        $ogModel = new OfferGoodsModel();
        $offerGoods = $ogModel->getOfferGoods($offerSn);
        $goodsNum = $offerGoods->count();
        if ($goodsNum > 0) {
            $storeId = 1002;
            $goodsModel->userGoodsList(
                $offerGoods, $pickMarginRate, $chargeInfo, $offerInfo['goodsHouseInfo'], $storeId
            );
            $offerGoods = $dtModel->getFinalDiscount(objectToArray($offerGoods));
        }
        foreach ($offerGoods as $key => $value) {
            //免税店折扣
            $discountDFS = $dtModel->makeDiscountDFS($value);
            $offerGoods[$key]['abk_discount'] = (float)$discountDFS['abk_discount'];
            $offerGoods[$key]['lt_discount'] = (float)$discountDFS['lt_discount'];
            $offerGoods[$key]['xl_discount'] = (float)$discountDFS['xl_discount'];
            unset($offerGoods[$key]['channels_info']);
        }
        $returnMsg = [
            'orderInfo' => $offerOrder,
            'goodsNum' => $goodsNum,
            'offerGoods' => $offerGoods,
            'erpInfo' => $offerInfo['goodsHouseInfo'],
            'arrPickRate' => $arrPickRate,
            'arrCharge' => $arrCharge,
        ];
        return response()->json($returnMsg);
    }

    /**
     * description 客户订单报价-单个修改销售折扣
     * author zhangdong
     * date 2019.11.25
     */
    public function modifySkuDiscount(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->skuDiscountParams($reqParams);
        $spec_sn = trim($reqParams['spec_sn']);
        $sale_discount = trim($reqParams['sale_discount']);
        $offerSn = trim($reqParams['offer_sn']);
        $ogModel = new OfferGoodsModel();
        $modifyRes = $ogModel->updateSkuDiscount($offerSn, $spec_sn, $sale_discount);
        if (!$modifyRes) {
            $returnMsg = ['code' => '2023', 'msg' => '操作失败'];
            return response()->json($returnMsg);
        }
        //根据报价单号获取报价商品信息
        $ogModel = new OfferGoodsModel();
        $offerGoods = $ogModel->getOfferGoods($offerSn, $reqParams);
        if ($offerGoods->count() == 0) {
            $returnMsg = ['code' => '2023', 'msg' => '商品信息有误'];
            return response()->json($returnMsg);
        }
        $goodsModel = new GoodsModel();
        $loginUserInfo = $request->user();
        //部门id
        $depart_id = intval($loginUserInfo->department_id);
        $offerInfo = $goodsModel->getOfferInfo($depart_id);
        $store_id = 1002;
        $goodsModel->userGoodsList(
            $offerGoods, $offerInfo['pickMarginRate'], $offerInfo['chargeInfo'],
            $offerInfo['goodsHouseInfo'], $store_id
        );
        $returnMsg = [
            'code' => '1000',
            'offerGoods' => $offerGoods,
        ];
        return response()->json($returnMsg);
    }

    /**
     * description 客户订单报价-报价列表
     * author zhangdong
     * date 2019.11.26
     */
    public function offerList(Request $request)
    {
        $reqParams = $request->toArray();
        $pageSize = isset($reqParams['pageSize']) ? intval($reqParams['pageSize']) : 15;
        $ooModel = new OfferOrderModel();
        $offerOrderList = $ooModel->getOfferList($reqParams, $pageSize);
        $returnMsg = [
            'offerList' => $offerOrderList,
        ];
        return response()->json($returnMsg);
    }

    /**
     * description 客户订单报价-导出数据
     * author zhangdong
     * date 2019.11.26
     */
    public function exportSkuOffer(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->exportSkuOfferParams($reqParams);
        $offerSn = trim($reqParams['offer_sn']);
        $exportData = (new OfferGoodsModel())->getExportOfferData($offerSn);
        //开始导出数据
        $name = "客户订单报价_$offerSn";
        $view = 'export/userOrderOffer';
        $executeModel = new ExcuteExcel($name, $view, 'xlsx');
        $columnFormat = [
            'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
        $executeModel->exportByView($exportData, $columnFormat);
    }

    /**
     * description 客户订单报价--批量导入销售折扣
     * author zhangdong
     * date 2019.11.27
     */
    public function importSkuOffer(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->importSkuOfferParams($reqParams);
        $offerSn = trim($reqParams['offer_sn']);
        //通过报价单号检查报价单信息
        $ooModel = new OfferOrderModel();
        $countNum = $ooModel->countNum($offerSn);
        if ($countNum <= 0) {
            $returnMsg = ['code' => '2067', 'msg' => '报价单不存在'];
            return response()->json($returnMsg);
        }
        //检查导入文件
        $file = $_FILES;
        //检查上传文件是否合格
        $executeExcel = new ExcuteExcel();
        $fileName = '客户订单报价';
        $res = $executeExcel->verifyUploadFile($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        //检查导入表格的报价单和传入参数中的报价单是否一致
        $checkOfferSn = $executeExcel->checkOfferSn($file, $offerSn);
        if ($checkOfferSn === false) {
            $returnMsg = [
                'code' => '2067',
                'msg' => '报价单号和导入表格不符，请重新选择导入表格'
            ];
            return response()->json($returnMsg);
        }
        //检查字段名称
        $needTitle = ['规格码', '销售折扣'];
        $checkTitle = $executeExcel->checkTitle($res[0], $needTitle);
        if ($checkTitle !== true) {
            return response()->json($checkTitle);
        }
        //检查上传数据
        $ogModel = new OfferGoodsModel();
        $checkRes = $ogModel->checkImportData($offerSn, $res);
        if (isset($checkRes['code'])) {
            return response()->json($checkRes);
        }
        $correctData = $checkRes;
        //更新销售折扣
        $updateRes = $ogModel->updateSaleDiscount($offerSn, $correctData);
        //返回成功信息
        $returnMsg = ['code' => '2023', 'msg' => '操作失败,请检查数据是否有改动'];
        if ($updateRes) {
            $returnMsg = ['code' => '2024', 'msg' => '操作成功'];
        }
        return response()->json($returnMsg);
    }//end of function

    /**
     * description 获取商品报价列表
     * editor zongxing
     * type GET
     * date 2019.11.29
     * return Array
     */
    public function getGoodsSaleList(Request $request)
    {
        $param_info = $request->toArray();
        $goods_sale_model = new GoodsSaleModel();
        $param_info['limit'] = 1;
        $goods_sale_list = $goods_sale_model->getGoodsSaleList($param_info);
        if (empty($goods_sale_list['data'])) {
            return response()->json(['code' => '1002', 'msg' => '暂无商品报价列表信息']);
        }
        $return_info = ['code' => '1000', 'msg' => '获取商品报价列表成功', 'data' => $goods_sale_list];
        return response()->json($return_info);
    }

    /**
     * description 上传商品报价
     * editor zongxing
     * type GET
     * date 2019.11.29
     * return Array
     */
    public function uploadGoodsSaleInfo(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['upload_file'])) {
            return response()->json(['code' => '1002', 'msg' => '上传文件不能为空']);
        } elseif (empty($param_info['sale_date'])) {
            return response()->json(['code' => '1003', 'msg' => '报价日期不能为空']);
        }
        //检查上传文件是否合格
        $upload_file = $_FILES;
        $excuteExcel = new ExcuteExcel();
        $fileName = '上传商品报价模板';//要上传的文件名，将对上传的文件名做比较
        $res = $excuteExcel->verifyUploadFileZ($upload_file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        $arrTitle = ['品牌ID', '商品品牌', '商家编码', '参考码', '商品代码', '报价', '币种'];
        foreach ($arrTitle as $title) {
            $title = trim($title);
            if (!in_array($title, $res[0])) {
                return response()->json(['code' => '1006', 'msg' => '您的标题头有误，请按模板导入']);
            }
        }
        //整理上传商品信息（上传商品报价）
        $goods_sale_model = new GoodsSaleModel();
        $upload_info = $goods_sale_model->createGoodsSaleInfo($res, $arrTitle);
        if (isset($upload_info['code'])) {
            return response()->json($upload_info);
        }
        $upload_goods_info = $upload_info['upload_goods_info'];
        $diff_goods_info = $upload_info['diff_goods_info'];
        //商品报价维护
        $res = $goods_sale_model->uploadGoodsSaleInfo($param_info, $upload_goods_info);
        $return_info = ['code' => '1007', 'msg' => '上传商品报价失败'];
        if ($res !== false && empty($diff_goods_info)) {
            $return_info = ['code' => '1000', 'msg' => '上传商品报价成功'];
        }
        if (!empty($diff_goods_info)) {
            $return_info = ['code' => '1001', 'msg' => '上传商品报价成功,存在新商品', 'data' => $diff_goods_info];
        }
        return response()->json($return_info);
    }

    /**
     * description 下载商品报价中的新品列表
     * editor zongxing
     * type GET
     * date 2019.11.29
     * return Array
     */
    public function downloadSaleDiffGoods(Request $request)
    {
        $title[] = ['品牌ID', '商品品牌', '商家编码', '参考码', '商品代码', '报价', '币种'];
        $param_info = $request->toArray();
        if (empty($param_info['diff_goods_info'])) {
            return response()->json(['code' => '1002', 'msg' => '商品报价新品不能为空']);
        }
        $diff_goods_info = json_decode(json_encode($param_info['diff_goods_info']), true);
        //$diff_goods_info = json_decode($param_info['diff_goods_info'], true);
        $new_goods_list = [];
        foreach ($diff_goods_info as $k => $v) {
            $new_goods_list[] = [
                'brand_id' => '',
                'brand_name' => trim($v['brand_name']),
                'erp_merchant_no' => trim($v['erp_merchant_no']),
                'erp_ref_no' => trim($v['erp_ref_no']),
                'erp_prd_no' => trim($v['erp_prd_no']),
                'sale_price' => floatval($v['sale_price']),
                'currency' => intval($v['currency']),
            ];
        }
        $filename = '商品报价需要新增的商品_' . date('Y-m-d');
        $exportData = array_merge($title, $new_goods_list);
        $excel_obj = new ExcuteExcel();
        $excel_obj->exportZ($exportData, $filename);
    }

    /**
     * description:商品模块-乐天商品列表
     * author:zongxing
     * date:2020.02.21
     * type:GET
     * return Json
     */
    public function ltGoodsList(Request $request)
    {
        if ($request->method('get')) {
            $param_info = $request->toArray();
            $goods_model = new GoodsModel();
            $param_info['is_all'] = true;
            $goods_list_info = $goods_model->ltGoodsList($param_info);
            $return_info = ['code' => '1002', 'msg' => '暂无乐天商品'];
            if ($goods_list_info !== false) {
                $return_info = ['code' => '1000', 'msg' => '获取乐天商品列表成功', 'data' => $goods_list_info];
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:商品模块-乐天商品列表-获取商品美金原价波动数据
     * author:zongxing
     * date:2020.02.22
     * type:GET
     * return Json
     */
    public function ltGoodsSpecPriceInfo(Request $request)
    {
        $param_info = $request->toArray();
        $rules = [
            'lt_prd_no' => 'required',
        ];
        $messages = [
            'lt_prd_no.required' => '乐天商品码不能为空'
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }

        //获取商品美金原价记录信息
        $goods_model = new GoodsModel();
        $goods_detail_info = $goods_model->ltGoodsSpecPriceInfo($param_info);
        if (empty($goods_detail_info)) {
            return response()->json(['code' => '1003', 'msg' => '乐天商品码错误']);
        }
        $goods_info = $date_arr = $spec_price_arr = [];
        foreach ($goods_detail_info as $k => $v) {
            if (empty($goods_info)) {
                $goods_info = [
                    'goods_name' => $v['goods_name'],
                    'lt_prd_no' => $v['lt_prd_no'],
                    'erp_prd_no' => $v['erp_prd_no']
                ];
            }
            $date_arr[] = $v['download_date'];
            $spec_price_arr[] = $v['spec_price'];
        }
        $return_info = [
            'goods_info' => $goods_info,
            'date_arr' => $date_arr,
            'spec_price_arr' => $spec_price_arr,
        ];
        $return_info = ['code' => '1000', 'msg' => '获取乐天商品美金原价信息成功', 'data' => $return_info];
        return response()->json($return_info);
    }

    /**
     * desc 通过商家编码从ERP货品档案更新商品重量-单个修改
     * author zhangdong
     * date 2020.03.12
     */
    public function updateWeightByErp(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->updateWeightByErpParams($reqParams);
        $specSn = trim($reqParams['spec_sn']);
        //检查商品是否在系统中存在
        $gsModel = new GoodsSpecModel();
        $goodsInfo = $gsModel->getGoodsSpecInfo($specSn);
        if (is_null($goodsInfo)) {
            $returnMsg = ['code' => '2067', 'msg' => '该商品在MIS中不存在'];
            return response()->json($returnMsg);
        }
        $erpModel = new ErpApi();
        $erpNo = $goodsInfo->erp_merchant_no;
        if (empty($erpNo)) {
            $returnMsg = ['code' => '2067', 'msg' => '该商品没有商家编码'];
            return response()->json($returnMsg);
        }
        $erpGoodMsg = $erpModel->getGoodsByErp($erpNo);
        if (count($erpGoodMsg['goods_list']) == 0) {
            $returnMsg = ['code' => '2067', 'msg' => '该商品在ERP不存在'];
            return response()->json($returnMsg);
        }
        $arrGoodsInfo = $erpGoodMsg['goods_list'][0]['spec_list'];
        $erpWeight = floatval($arrGoodsInfo[0]['weight']);
        $misWeight = floatval($goodsInfo->spec_weight);
        if ($erpWeight === $misWeight) {
            $returnMsg = ['code' => '2067', 'msg' => 'ERP重量和MIS重量相同，无需更新！'];
            return response()->json($returnMsg);
        }
        //更新商品重量
        $updateRes = $gsModel->updateWeight($specSn, $erpWeight);
        $returnMsg = ['code' => '2023', 'msg' => '操作失败'];
        if ($updateRes) {
            $returnMsg = ['code' => '2024', 'msg' => '操作成功'];
        }
        return response()->json($returnMsg);
    }

    /**
     * desc 通过商家编码批量更新商品预估重量
     * author zhangdong
     * date 2020.03.13
     */
    public function importEstimateWeight()
    {
        //检查导入文件
        $file = $_FILES;
        //检查上传文件是否合格
        $executeExcel = new ExcuteExcel();
        $fileName = '批量更新商品预估重量';
        $res = $executeExcel->verifyUploadFile($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        //检查字段名称
        $needTitle = ['平台条码', '预估重量'];
        $checkTitle = $executeExcel->checkTitle($res[0], $needTitle);
        if ($checkTitle !== true) {
            return response()->json($checkTitle);
        }
        //检查上传的预估数据--检查平台条码是否有空的并重新组装条码为数组类型
        $gcModel = new GoodsCodeModel();
        $arrPlatFormNo = $gcModel->getArrPlatformNo($res);
        if (isset($arrPlatFormNo['code'])) {
            return response()->json($arrPlatFormNo);
        }
        //通过这些平台条码找出商品规格码
        $specNoInfo = $gcModel->getSpecMsgByCode($arrPlatFormNo);
        //组装要更新的数据并联合MIS中的平台条码做二次校验
        $updateData = $gcModel->makeUpdateData($res, $specNoInfo);
        if (isset($updateData['code'])) {
            return response()->json($updateData);
        }

        //开始更新重量
        $gsModel = new GoodsSpecModel();
        $updateRes = $gsModel->updateEstimateWeight($updateData);
        $returnMsg = ['code' => '2023', 'msg' => '操作失败,请联系技术人员解决'];
        if ($updateRes) {
            $returnMsg = ['code' => '2024', 'msg' => '操作成功'];
        }
        return response()->json($returnMsg);

    }//end of function

    /**
     * description 客户订单报价-上传需要报价的SKU
     * author zhangdong
     * type post
     * date 2019.11.25
     */
    public function importWholesaleSku(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->importWholesaleSku($reqParams);
        //检查销售用户和折扣类型是否存在
        $saleUid = intval($reqParams['sale_user_id']);
        $discType = intval($reqParams['disc_type']);
        $udModel = new UserDiscountModel();
        $userDiscInfo = $udModel->getUserDisc($saleUid, $discType);
        if (is_null($userDiscInfo)) {
            $returnMsg = ['code' => '2067', 'msg' => '您选择的用户或折扣类型不存在'];
            return response()->json($returnMsg);
        }
        $user_info = $request->user();
        $reqParams['admin_id'] = $user_info->id;
        $file = $_FILES;
        //检查上传文件是否合格
        $executeExcel = new ExcuteExcel();
        $fileName = '大批发报价SKU导入模板';
        $res = $executeExcel->verifyUploadFile($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        if (count($res) > 500) {
            $returnMsg = ['code' => '2067', 'msg' => '导入数据不可超过500条'];
            return response()->json($returnMsg);
        }
        //检查字段名称
        $arrTitle = [
            '商品名称', '平台条码'
        ];
        foreach ($arrTitle as $title) {
            if (!in_array(trim($title), $res[0])) {
                $returnMsg = ['code' => '2009', 'msg' => '您的标题头有误，请按模板导入'];
                return response()->json($returnMsg);
            }
        }
        //对上传的数据进行校验，如果有新品则告知用户并且将新品保存
        $gcModel = new GoodsCodeModel();
        $arrPlatFormNo = $gcModel->getArrPlatformNo($res);
        if (isset($arrPlatFormNo['code'])) {
            return response()->json($arrPlatFormNo);
        }
        //通过这些平台条码找出商品规格码
        $goodsInfo = $gcModel->getGoodsMsgByCode($arrPlatFormNo);
        if (count($goodsInfo) == 0) {
            $returnMsg = ['code' => '2067', 'msg' => '上传失败,您导入数商品数据系统中不存在，请联系技术人员'];
            return response()->json($returnMsg);
        }
        //通过商品规格信息检查导入表格中是否有新品,并将MIS中存在的数据重新组装
        $wholesaleGoods = $gcModel->checkExportData($res, $goodsInfo);
        if (isset($wholesaleGoods['code'])) {
            return response()->json($wholesaleGoods);
        }
        //检查无误后将导入数据保存到数据表中
        $woModel = new WholesaleOrderModel();
        //组装大批发报价单数据
        $orderData = $woModel->createOrderData($wholesaleGoods, $userDiscInfo, $reqParams);
        if (isset($orderData['code'])) {
            $returnMsg = ['code' => '2067', 'msg' => '上传失败,您导入数商品没有折扣数据，请维护'];
            return response()->json($returnMsg);
        }
        $saveRes = $woModel->saveData($orderData);
        //报价单号
        $wholesaleSn = $orderData['order']['wholesale_sn'];
        $returnMsg = ['code' => '2000', 'msg' => '上传成功', '报价单号' => $wholesaleSn];
        if ($saveRes == false) {
            $returnMsg = ['code' => '2001', 'msg' => '上传失败'];
        }
        return response()->json($returnMsg);
    }

    /**
     * desc 大批发单报价-报价列表
     * author zhangdong
     * date 2020.03.24
     * 参考方法 offerList
     */
    public function wholesaleList(Request $request)
    {
        $reqParams = $request->toArray();
        $pageSize = isset($reqParams['pageSize']) ? intval($reqParams['pageSize']) : 15;
        $ooModel = new WholesaleOrderModel();
        $offerOrderList = $ooModel->getList($reqParams, $pageSize);
        //获取客户信息
        $udModel = new UserDiscountModel();
        $saleUserInfo = $udModel->getDisc();
        $returnMsg = [
            'discType' => $udModel->type,
            'saleUserInfo' => $saleUserInfo,
            'offerList' => $offerOrderList,
        ];
        return response()->json($returnMsg);
    }

    /**
     * desc 大批发报价-获取报价详情-新版
     * author zhangdong
     * type post
     * date 2020.04.08
     */
    public function getWholeDetail(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->wholeDetailParams($reqParams);
        $wholesaleSn = trim($reqParams['wholesale_sn']);
        //获取订单信息
        $woModel = new WholesaleOrderModel();
        //检查订单是否存在
        $countNum = $woModel->countNum($wholesaleSn);
        if ($countNum <= 0) {
            $returnMsg = ['code' => '2067', 'msg' => '当前订单不存在'];
            return response()->json($returnMsg);
        }
        //根据单号查询组装商品折扣信息
        $wdModel = new WholesaleDiscountModel();
        $discount = $wdModel->getGoodsDiscount($wholesaleSn);
        //组装最终报价数据
        $wgModel = new WholesaleGoodsModel();
        $madeOffer = $wgModel->madeWholesaleData($wholesaleSn, $discount);
        $returnMsg = [
            'costTitle' => $madeOffer['beautCostTitle'],
            'order' => $madeOffer['order'],
            'goods' => $madeOffer['goods'],
        ];
        return response()->json($returnMsg);

    }


    /**
     * desc 大批发报价-获取报价详情
     * author zhangdong
     * type post
     * date 2020.03.25
     */
    public function getWholesaleDetail(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->wholesaleDetailParams($reqParams);
        $wholesaleSn = trim($reqParams['wholesale_sn']);
        //获取订单信息
        $woModel = new WholesaleOrderModel();
        //检查订单是否存在
        $countNum = $woModel->countNum($wholesaleSn);
        if ($countNum <= 0) {
            $returnMsg = ['code' => '2067', 'msg' => '当前订单不存在'];
            return response()->json($returnMsg);
        }
        $order = $woModel->getOrderBySn($wholesaleSn);
        //获取当前报价单下的SKU
        $wgModel = new WholesaleGoodsModel();
        $goods = $wgModel->getGoodsBySn($wholesaleSn);
        $arrSpecSn = getFieldArrayVaule(objectToArray($goods), 'spec_sn');
        $param_info['spec_arr'] = $arrSpecSn;
        if (isset($reqParams['cut_other_value'])) {
            $param_info['cut_other_value'] = floatval($reqParams['cut_other_value']);
        }
        $param_info['freight_type'] = intval($order->transport_type);
        $param_info['predict_pot_time'] = trim($order->estimate_time);
        $param_info['freight'] = floatval($order->freight);
        if ($order->usd_cny_rate > 0) {
            $param_info['usd_cny_rate'] = floatval($order->usd_cny_rate);
        }
        //是否为详情，1：是；0：不是，如果为1，还会生成商品图片
        $param_info['is_detail'] = 0;
        $goodsModel = new GoodsModel();
        $goodsInfo = $goodsModel->getGoodsFinalInfo($param_info);
        $channel = (new WholesaleDiscountModel())->getChannelBySn($wholesaleSn);
        $returnMsg = [
            'discountPlate' => (new WholesaleDiscountModel())->chDiscPlate,
            'channel' => $channel,
            'order' => $order,
            'goods' => $goodsInfo,
        ];
        return response()->json($returnMsg);
    }//end function


    /**
     * description 大批发报价-导出数据
     * author zhangdong
     * date 2020.03.27
     */
    public function exportWholesaleOffer(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->exportWholesaleOfferParams($reqParams);
        $wholesaleSn = trim($reqParams['wholesale_sn']);
        $inputNum = floatval($reqParams['input_num']);
        $arrDisPlate = array_filter(explode(',', trim($reqParams['discount_plate'])));
        $arrChannelId = array_filter(explode(',', trim($reqParams['channels_id'])));
        $wgModel = new WholesaleGoodsModel();
        //传参检查
        $checkParams = $wgModel->checkParams($arrDisPlate, $inputNum, $wholesaleSn, $arrChannelId);
        if (isset($checkParams['code'])) {
            return response()->json($checkParams);
        }
        $discount = $checkParams['discount'];
        $disPlate = $checkParams['disPlate'];
        //组装最终报价数据
        $madeOffer = $wgModel->madeOfferData($wholesaleSn, $inputNum, $discount, $disPlate);
        $offerData['channelId'] = $arrChannelId;
        $offerData['disPlate'] = $arrDisPlate;
        $offerData['inputNum'] = $inputNum;
        $offerData['order'] = $madeOffer['order'];
        $offerData['goodsInfo'] = $madeOffer['goods'];
        $execute = new ExcuteExcel();
        return $execute->expWholesaleOffer($wholesaleSn, $offerData);
    }// end function

    /**
     * description 大批发报价-导出数据-新版
     * author zhangdong
     * date 2020.05.12
     */
    public function wholesaleExport(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->wholesaleExportParams($reqParams);
        $wholesaleSn = trim($reqParams['wholesale_sn']);
        $wgModel = new WholesaleGoodsModel();
        //传参检查并校验数据
        $checkParams = $wgModel->checkWholesale($wholesaleSn);
        if (isset($checkParams['code'])) {
            return response()->json($checkParams);
        }
        $discount = $checkParams['discount'];
        //组装最终报价数据
        $madeOffer = $wgModel->madeWholesaleData($wholesaleSn, $discount);
        //return response()->json($madeOffer);
        $execute = new ExcuteExcel();
        return $execute->expWholeOffer($wholesaleSn, $madeOffer);
    }// end function

    /**
     * desc 大批发报价-详情-修改运输方式和参考折扣
     * author zhangdong
     * date 2020.05.15
     */
    public function modifyWholeData(Request $request)
    {
        $reqParams = $request->toArray();
        ParamsCheckSingle::paramsCheck()->modifyDataParams($reqParams);
        $wholesaleSn = trim($reqParams['wholesale_sn']);
        $specSn = trim($reqParams['spec_sn']);
        //获取订单信息
        $wgModel = new WholesaleGoodsModel();
        //检查订单和商品是否存在
        $wholeGoods = $wgModel->getGoods($wholesaleSn, $specSn);
        if (count($wholeGoods) <= 0) {
            $returnMsg = ['code' => '2067', 'msg' => '当前订单或商品不存在'];
            return response()->json($returnMsg);
        }
        $wgModel = new WholesaleGoodsModel();
        $updateRes = $wgModel->updateGoodsInfo($reqParams);
        $returnMsg = ['code' => '2024', 'msg' => '操作成功'];
        if (!$updateRes) {
            $returnMsg = ['code' => '2023', 'msg' => '操作失败'];
        }
        return response()->json($returnMsg);
    }


    /**
     * description 大批发报价-详情-批量修改运输方式和参考折扣
     * author zhangdong
     * date 2020.05.15
     */
    public function batchModifyWholeData(Request $request)
    {
        $reqParams = $request -> toArray();
        ParamsCheckSingle::paramsCheck()->batchModifyWholeDataParams($reqParams);
        $wholesaleSn = trim($reqParams['wholesale_sn']);
        //检查订单信息
        $woModel = new WholesaleOrderModel();
        $orderCount = $woModel->countNum($wholesaleSn);
        if ($orderCount == 0) {
            $returnMsg = ['code' => '2067','msg' => '订单不存在'];
            return response()->json($returnMsg);
        }
        //检查导入文件
        $file = $_FILES;
        //检查上传文件是否合格
        $executeExcel = new ExcuteExcel();
        $fileName = '大批发报价-批量修改运输方式和参考折扣';
        $res = $executeExcel->verifyUploadFile($file, $fileName);
        if (isset($res['code'])) {
            return response()->json($res);
        }
        //检查字段名称
        $needTitle = ['规格码','运输方式','参考折扣',];
        $checkTitle = $executeExcel->checkTitle($res[0], $needTitle);
        if ($checkTitle !== true) {
            return response()->json($checkTitle);
        }
        //检查上传数据
        $wgModel = new WholesaleGoodsModel();
        $checkRes = $wgModel->checkOfferData($wholesaleSn, $res);
        if (isset($checkRes['code'])) {
            return response()->json($checkRes);
        }
        $correctData = $checkRes;
        //更新销售折扣
        $updateRes = $wgModel->updateWholeData($wholesaleSn, $correctData);
        //返回成功信息
        $returnMsg = ['code' => '2023','msg' => '操作失败,请检查数据是否有改动'];
        if($updateRes){
            $returnMsg = ['code' => '2024','msg' => '操作成功'];
        }
        return response()->json($returnMsg);
    }//end of function

}//end of class
