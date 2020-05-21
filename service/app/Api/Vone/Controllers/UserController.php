<?php

namespace App\Api\Vone\Controllers;

use App\Model\Vone\ClassifyFieldModel;
use App\Model\Vone\ClassifyModel;
use App\Model\Vone\ClassifyShopModel;
use App\Model\Vone\FieldModel;
use App\Model\Vone\OperateLogModel;
use App\Model\Vone\PurchaseUserModel;
use App\Model\Vone\UserModel;
use App\Model\Vone\ShopStockModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class UserController extends BaseController
{
    /**
     * description:新增采购id
     * editor:zongxing
     * type:POST
     * date : 2018.07.04
     * params: 1.采购人员姓名:real_name;2.采购人员护照号:passport_sn;3.采购方式id:method_id;4.采购渠道id:channels_id;
     *          5.账号:account_number;
     * return Object
     */
    public function createPurchaseUser(Request $request)
    {
        if ($request->isMethod("post")) {
            $purchase_user_info = $request->toArray();
            if (empty($purchase_user_info["real_name"])) {
                return response()->json(['code' => '1002', 'msg' => '采购人员姓名不能为空']);
            } else if (empty($purchase_user_info["passport_sn"])) {
                return response()->json(['code' => '1003', 'msg' => '采购人员护照号不能为空']);
            } else if (empty($purchase_user_info["method_id"])) {
                return response()->json(['code' => '1004', 'msg' => '采购方式id不能为空']);
            } else if (empty($purchase_user_info["channels_id"])) {
                return response()->json(['code' => '1005', 'msg' => '采购渠道id不能为空']);
            } else if (empty($purchase_user_info["account_number"])) {
                return response()->json(['code' => '1006', 'msg' => '账号不能为空']);
            }

            //检查采购人员的信息
            $purchaseUserModel = new PurchaseUserModel();
            $check_user_info = $purchaseUserModel->check_user_info($purchase_user_info);

            if (!empty($check_user_info)) {
                return response()->json(['code' => '1007', 'msg' => '该采购人员已经存在']);
            }

            $insert_purchase_user_info = [
                'real_name' => trim($purchase_user_info["real_name"]),
                'passport_sn' => trim($purchase_user_info["passport_sn"]),
                'method_id' => intval($purchase_user_info["method_id"]),
                'channels_id' => intval($purchase_user_info["channels_id"]),
                'account_number' => trim($purchase_user_info["account_number"]),
            ];
            $insertRes = PurchaseUserModel::create($insert_purchase_user_info);

            $code = "1008";
            $msg = "创建采购id失败";
            $return_info = compact('code', 'msg');

            if ($insertRes) {
                $code = "1000";
                $msg = "创建采购id成功";
                $return_info = compact('code', 'msg');
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:获取采购id列表
     * editor:zongxing
     * type:GET
     * date : 2018.07.10
     * return Object
     */
    public function getUserList(Request $request)
    {
        if ($request->isMethod("get")) {
            //获取采购id列表信息
            $param_info = $request->toArray();  
            $purchaseUserModel = new PurchaseUserModel();
            $user_list_info = $purchaseUserModel->getUserList($param_info);
            $return_info = ['code' => '1000', 'msg' => '获取采购id列表成功', 'data' => $user_list_info];
            if (empty($user_list_info)) {
                $return_info = ['code' => '1002', 'msg' => '暂无采购id'];
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:打开编辑采购id页面
     * editor:zongxing
     * type:POST
     * date : 2018.07.10
     * return Object
     */
    public function eidtUser(Request $request)
    {
        if ($request->isMethod("post")) {
            $user_info = $request->toArray();

            //获取采购id列表信息
            $purchaseUserModel = new PurchaseUserModel();
            $user_info = $purchaseUserModel->getUserInfo($user_info);

            $code = "1000";
            $msg = "获取采购id信息成功";
            $data = $user_info;
            $return_info = compact('code', 'msg', 'data');
            if (empty($user_info)) {
                $code = "1002";
                $msg = "该采购id不存在";
                $return_info = compact('code', 'msg');
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:提交编辑采购id页面
     * editor:zongxing
     * type:POST
     * date : 2018.07.10
     * return Object
     */
    public function doEidtUser(Request $request)
    {
        if ($request->isMethod("post")) {
            $purchase_user_info = $request->toArray();

            if (empty($purchase_user_info["real_name"])) {
                return response()->json(['code' => '1002', 'msg' => '采购人员姓名不能为空']);
            } else if (empty($purchase_user_info["passport_sn"])) {
                return response()->json(['code' => '1003', 'msg' => '采购人员护照号不能为空']);
            } else if (empty($purchase_user_info["method_id"])) {
                return response()->json(['code' => '1004', 'msg' => '采购方式id不能为空']);
            } else if (empty($purchase_user_info["channels_id"])) {
                return response()->json(['code' => '1005', 'msg' => '采购渠道id不能为空']);
            } else if (empty($purchase_user_info["account_number"])) {
                return response()->json(['code' => '1006', 'msg' => '账号不能为空']);
            }

            //检查采购人员的信息
            $check_user_info = PurchaseUserModel::where("id", $purchase_user_info["id"])->first();
            $check_user_info = $check_user_info->toArray();

            if (empty($check_user_info)) {
                return response()->json(['code' => '1007', 'msg' => '该采购id不存在']);
            }

            //更新采购id列表信息
            $edit_purchase_user_info = [
                'real_name' => trim($purchase_user_info["real_name"]),
                'passport_sn' => trim($purchase_user_info["passport_sn"]),
                'method_id' => intval($purchase_user_info["method_id"]),
                'channels_id' => intval($purchase_user_info["channels_id"]),
                'account_number' => trim($purchase_user_info["account_number"]),
            ];
            $updateRes = PurchaseUserModel::where("id", $purchase_user_info["id"])->update($edit_purchase_user_info);

            $code = "1008";
            $msg = "编辑采购id失败";
            $return_info = compact('code', 'msg');

            if ($updateRes) {
                $code = "1000";
                $msg = "编辑采购id成功";
                $return_info = compact('code', 'msg');

                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_purchase_user',
                    'bus_desc' => '采购id-确认提交编辑采购id-采购id：' . $purchase_user_info["id"],
                    'bus_value' => $purchase_user_info["id"],
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '采购id-确认提交编辑采购id',
                    'module_id' => 2,
                    'have_detail' => 1,
                ];

                $logDetailData["table_name"] = 'operate_log_detail';
                foreach ($purchase_user_info as $k => $v) {
                    if (isset($check_user_info[$k]) && $check_user_info[$k] != $v) {
                        $logDetailData["update_info"][] = [
                            'table_field_name' => $k,
                            'field_old_value' => $check_user_info[$k],
                            'field_new_value' => $v,
                        ];
                    }
                }
                if (isset($logDetailData["update_info"])) {
                    $operateLogModel->insertMoreLog($logData, $logDetailData);
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
     * description:改变表格标题样式
     * editor:zongxing
     * date : 2018.06.28
     * params: 1.excel对象:$obj_excel;2.最后一列的名称:$column_last_name;
     * return Object
     */
    public function changeTableTitle($obj_excel, $column_first_name, $row_first_i, $column_last_name, $row_last_i)
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
    public function changeTableContent($obj_excel, $column_first_name, $row_first_i, $column_last_name, $row_last_i)
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
     * description 添加用户
     * editor zongxing
     * type POST
     * date 2019.11.28
     * params 1.用户名:user_name;2.密码:password;3.确认密码:confirm_password;4.用户分类id:classify_id;;5.昵称:nickname;
     */
    public function addUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['user_name'])) {
                return response()->json(['code' => '1002', 'msg' => '用户名不能为空']);
            } else if (empty($param_info['password'])) {
                return response()->json(['code' => '1003', 'msg' => '密码不能为空']);
            } else if (empty($param_info['confirm_password'])) {
                return response()->json(['code' => '1004', 'msg' => '确认密码不能为空']);
            } else if ($param_info['password'] != $param_info['confirm_password']) {
                return response()->json(['code' => '1005', 'msg' => '两次输入的密码不一致']);
            } else if (empty($param_info['classify_id'])) {
                return response()->json(['code' => '1006', 'msg' => '用户分类不能为空']);
            } else if (empty($param_info['nickname'])) {
                return response()->json(['code' => '1007', 'msg' => '昵称不能为空']);
            }
            //检查用户是否存在
            $userModel = new UserModel();
            $check_user_info = $userModel->check_user_info($param_info);
            if (!empty($check_user_info)) {
                return response()->json(['code' => '1008', 'msg' => '该管理员已经存在']);
            }
            //组装创建用户数据
            $insert_data = [
                'user_name' => trim($param_info['user_name']),
                'nickname' => trim($param_info['nickname']),
                'password' => bcrypt($param_info['password']),
                'classify_id' => intval($param_info['classify_id']),
            ];
            //新增用户
            $insertRes = DB::table('user')->insertGetId($insert_data);
            $return_info = ['code' => '1009', 'msg' => '添加用户失败'];
            if ($insertRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '添加用户成功'];
                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_user',
                    'bus_desc' => '用户模块-添加用户-用户id：' . $insertRes,
                    'bus_value' => $insertRes,
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '用户模块-添加用户',
                    'module_id' => 8,
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
     * description 获取用户列表
     * editor zongxing
     * type GET
     * date 2019.11.28
     * return Object
     */
    public function userList(Request $request)
    {
        $param_info = $request->toArray();
        $userModel = new UserModel();
        $user_list_info = $userModel->getUserList($param_info);
        if (empty($user_list_info)) {
            return response()->json(['code' => '1002', 'msg' => '暂无用户']);
        }
        $return_info = ['code' => '1000', 'msg' => '获取用户列表成功', 'data' => $user_list_info];
        return response()->json($return_info);
    }

    /**
     * description 获取用户分类信息
     * editor zongxing
     * type GET
     * date 2019.11.29
     * return Object
     */
    public function getUserClassify(Request $request)
    {
        if ($request->isMethod('get')) {
            $param_info = $request->toArray();
            $classify_model = new ClassifyModel();
            $user_classify_info = $classify_model->getUserClassify($param_info);
            if (empty($user_classify_info)) {
                return response()->json(['code' => '1002', 'msg' => '暂无用户分类信息']);
            }
            $return_info = ['code' => '1000', 'msg' => '获取用户分类信息成功', 'data' => $user_classify_info];
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description 获取用户分类列表
     * editor zongxing
     * type GET
     * date 2019.12.04
     * return Object
     */
    public function getUserClassifyList(Request $request)
    {
        if ($request->isMethod('get')) {
            $param_info = $request->toArray();
            $classify_model = new ClassifyModel();
            $user_classify_info = $classify_model->getUserClassify($param_info);
            if (empty($user_classify_info)) {
                return response()->json(['code' => '1002', 'msg' => '暂无用户分类列表']);
            }
            //获取分类对应的可见字段
            $cf_model = new ClassifyFieldModel();
            $cf_info = $cf_model->getClassifyFieldGroupByClassifyId();
            //获取分类对应的店铺信息
            $cs_model = new ClassifyShopModel();
            $cs_info = $cs_model->getClassifyShopGroupByClassifyId();
            foreach ($user_classify_info as $k => $v) {
                $classify_id = $v['id'];
                $classify_field = [];
                if (isset($cf_info[$classify_id])) {
                    $classify_field = $cf_info[$classify_id];
                }
                $user_classify_info[$k]['classify_field'] = $classify_field;
                $classify_shop = [];
                if (isset($cs_info[$classify_id])) {
                    $classify_shop = $cs_info[$classify_id];
                }
                $user_classify_info[$k]['classify_shop'] = $classify_shop;
            }
            $return_info = ['code' => '1000', 'msg' => '获取用户分类列表成功', 'data' => $user_classify_info];
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description 编辑用户
     * editor zongxing
     * type POST
     * date 2019.12.04
     * params 1.用户名:user_name;2.密码:password;3.确认密码:confirm_password;4.用户分类id:classify_id;5.昵称:nickname;6.用户id:id;
     */
    public function editUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['user_name'])) {
                return response()->json(['code' => '1002', 'msg' => '用户名不能为空']);
            } else if (empty($param_info['password'])) {
                return response()->json(['code' => '1003', 'msg' => '密码不能为空']);
            } else if (empty($param_info['confirm_password'])) {
                return response()->json(['code' => '1004', 'msg' => '确认密码不能为空']);
            } else if ($param_info['password'] != $param_info['confirm_password']) {
                return response()->json(['code' => '1005', 'msg' => '两次输入的密码不一致']);
            } else if (empty($param_info['classify_id'])) {
                return response()->json(['code' => '1006', 'msg' => '用户分类不能为空']);
            } else if (empty($param_info['nickname'])) {
                return response()->json(['code' => '1007', 'msg' => '昵称不能为空']);
            } else if (empty($param_info['id'])) {
                return response()->json(['code' => '1008', 'msg' => '用户id不能为空']);
            }
            //检查用户是否存在
            $user_model = new UserModel();
            $check_user_info = $user_model->check_user_info($param_info);
            if (empty($check_user_info)) {
                return response()->json(['code' => '1009', 'msg' => '用户id错误']);
            } elseif (count($check_user_info) > 1) {
                return response()->json(['code' => '1010', 'msg' => '该用户名已经存在']);
            }
            //编辑用户
            $updateRes = $user_model->editUser($param_info, $check_user_info[0]);
            $return_info = ['code' => '1009', 'msg' => '编辑用户失败'];
            if ($updateRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '编辑用户成功'];
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description 获取可见字段信息
     * editor zongxing
     * type GET
     * date 2019.12.04
     * return Object
     */
    public function getClassifyField(Request $request)
    {
        if ($request->isMethod('get')) {
            $param_info = $request->toArray();
            $field_model = new FieldModel();
            $classify_field_info = $field_model->getClassifyField($param_info);
            if (empty($classify_field_info)) {
                return response()->json(['code' => '1002', 'msg' => '暂无可见字段信息']);
            }
            $return_info = ['code' => '1000', 'msg' => '获取可见字段信息成功', 'data' => $classify_field_info];
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description 获取仓位信息
     * editor zongxing
     * type GET
     * date 2019.12.05
     * return Object
     */
    public function getClassifyShop(Request $request)
    {
        if ($request->isMethod('get')) {
            $ss_model = new ShopStockModel();
            $shop_info = $ss_model->getShopInfo();
            if (empty($shop_info)) {
                return response()->json(['code' => '1002', 'msg' => '暂无仓位信息']);
            }
            $return_info = ['code' => '1000', 'msg' => '获取仓位信息成功', 'data' => $shop_info];
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description 新增用户分类
     * editor zongxing
     * type POST
     * date 2019.12.04
     * params 1.分类名称:classify_name;2.备注:description;3.可见字段:classify_filed;
     */
    public function addClassify(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['classify_name'])) {
                return response()->json(['code' => '1002', 'msg' => '分类名称不能为空']);
            } else if (empty($param_info['description'])) {
                return response()->json(['code' => '1003', 'msg' => '备注不能为空']);
            } else if (empty($param_info['classify_filed'])) {
                return response()->json(['code' => '1004', 'msg' => '可见字段不能为空']);
            } else if (empty($param_info['classify_shop'])) {
                return response()->json(['code' => '1007', 'msg' => '店铺信息不能为空']);
            }
            //检查分类是否存在
            $classify_model = new ClassifyModel();
            $check_classify_info = $classify_model->check_classify_info($param_info);
            if (!empty($check_classify_info)) {
                return response()->json(['code' => '1005', 'msg' => '该分类名称已经存在']);
            }
            //组装创建用户分类数据
            $classify_data = [
                'classify_name' => trim($param_info['classify_name']),
                'description' => trim($param_info['description']),
            ];
            $insertRes = DB::transaction(function () use ($classify_data, $param_info) {
                //添加用户分类信息
                $classify_id = DB::table('classify')->insertGetId($classify_data);
                //添加用户分类可见字段信息
                $classify_filed = explode(',', $param_info['classify_filed']);//分类可见字段
                $classify_field_data = [];
                foreach ($classify_filed as $k => $v) {
                    $classify_field_data[] = [
                        'classify_id' => $classify_id,
                        'field_id' => $v,
                    ];
                }
                DB::table('classify_field')->insert($classify_field_data);
                //添加用户分类仓位信息
                $classify_shop = explode(',', $param_info['classify_shop']);//仓位
                $classify_shop_data = [];
                foreach ($classify_shop as $k => $v) {
                    $classify_shop_data[] = [
                        'classify_id' => $classify_id,
                        'shop_id' => $v,
                    ];
                }
                $res = DB::table('classify_shop')->insert($classify_shop_data);
                return $res;
            });
            $return_info = ['code' => '1006', 'msg' => '添加用户分类失败'];
            if ($insertRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '添加用户分类成功'];
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description 编辑用户分类
     * editor zongxing
     * type POST
     * date 2019.12.04
     * params 1.分类名称:classify_name;2.备注:description;3.可见字段:classify_filed;
     */
    public function editClassify(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            if (empty($param_info['classify_name'])) {
                return response()->json(['code' => '1002', 'msg' => '分类名称不能为空']);
            } else if (empty($param_info['description'])) {
                return response()->json(['code' => '1003', 'msg' => '备注不能为空']);
            } else if (empty($param_info['classify_filed'])) {
                return response()->json(['code' => '1004', 'msg' => '可见字段不能为空']);
            } else if (empty($param_info['id'])) {
                return response()->json(['code' => '1005', 'msg' => '分类id不能为空']);
            } else if (empty($param_info['classify_shop'])) {
                return response()->json(['code' => '1009', 'msg' => '店铺信息不能为空']);
            }
            //检查分类是否存在
            $classify_model = new ClassifyModel();
            $check_classify_info = $classify_model->check_classify_info($param_info);
            if (empty($check_classify_info)) {
                return response()->json(['code' => '1006', 'msg' => '分类id错误']);
            } elseif (count($check_classify_info) > 1) {
                return response()->json(['code' => '1007', 'msg' => '该分类名称已经存在']);
            }
            //获取分类可见字段信息
            $classify_id = intval($param_info['id']);
            $cf_model = new ClassifyFieldModel();
            $cf_info = $cf_model->getClassifyField($classify_id);
            //获取分类店铺信息
            $cs_model = new ClassifyShopModel();
            $cs_info = $cs_model->getClassifyShop($classify_id);
            //提交编辑用户分类
            $updateRes = $classify_model->editClassify($param_info, $check_classify_info[0], $cf_info, $cs_info);
            $return_info = ['code' => '1008', 'msg' => '编辑用户分类失败'];
            if ($updateRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '编辑用户分类成功'];
            }
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

}