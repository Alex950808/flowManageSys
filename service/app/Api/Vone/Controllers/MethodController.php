<?php

namespace App\Api\Vone\Controllers;

use App\Model\Vone\OperateLogModel;
use App\Model\Vone\PurchaseMethodModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * description:采购模块控制器
 * editor:zongxing
 * date : 2018.06.25
 */
class MethodController extends BaseController
{
    /**
     * description:添加采购方式
     * editor:zongxing
     * type:POST
     * date : 2018.06.26
     * params: 1.采购方式名称:method_name;2.采购方式权重:method_weight;
     * return Object
     */
    public function createMethod(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();

            $rules = [
                'method_name' => 'required|unique:purchase_method|max:255',
                'method_weight' => 'required|numeric',
                'method_property' => 'required|integer',
            ];
            $messages = [
                'method_name.required' => '采购方式名称不能为空',
                'method_name.unique' => '该采购方式已经存在',
                'method_name.max' => '采购方式名称不能超过255个字符',
                'method_weight.required' => '采购方式权重不能为空',
                'method_weight.numeric' => '采购方式权重必须为数值',
                'method_property.required' => '采购方式的属性不能为空',
                'method_property.integer' => '采购方式的属性必须为整数',
            ];
            $validator = Validator::make($param_info, $rules, $messages);
            if ($validator->fails()) {
                $msg = $validator->errors()->first();
                return response()->json(['code' => '1002', 'msg' => $msg]);
            }

            //计算采购期编号
            $model_obj = new PurchaseMethodModel();
            $model_field = "method_sn";
            $pin_head = "FS-";
            $last_method_sn = createNo($model_obj, $model_field, $pin_head, false);
            $param_info['method_sn'] = $last_method_sn;
            $res = DB::table("purchase_method")->insert($param_info);
            $return_info = ['code' => '1003', 'msg' => '采购方式添加失败'];
            if ($res !== false) {
                $return_info = ['code' => '1000', 'msg' => '采购方式添加成功'];
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:获取采购方式列表
     * editor:zongxing
     * type:GET
     * date : 2018.06.26
     * return Object
     */
    public function getMethodList(Request $request)
    {
        $param_info = $request->toArray();
        $pm_model = new PurchaseMethodModel();
        $pm_list = $pm_model->getTotalMethodList($param_info);
        $return_info = ['code' => '1002', 'msg' => '暂无采购方式'];
        if (!empty($pm_list)) {
            $return_info = ['code' => '1000', 'msg' => '获取采购方式列表成功', 'data' => $pm_list];
        }
        return response()->json($return_info);
    }

    /**
     * description:打开编辑采购方式详情页
     * editor:zongxing
     * type:POST
     * date : 2018.06.26
     * params: 1.采购方式id:id;
     * return Object
     */
    public function editMethod(Request $request)
    {
        if ($request->isMethod('post')) {
            $purchase_method_array = $request->toArray();

            $method_id = $purchase_method_array["id"];

            //获取指定采购方式信息
            $purchase_method_info = PurchaseMethodModel::where("id", $method_id)->get();
            if ($purchase_method_info->isEmpty()) {
                return response()->json(['code' => '1002', 'msg' => '采购方式不存在']);
            }

            $code = "1000";
            $msg = "获取指定采购方式信息成功";
            $data = $purchase_method_info;
            $return_info = compact('code', 'msg', 'data');
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:确认提交编辑采购方式
     * editor:zongxing
     * type:POST
     * date : 2018.07.11
     * params: 1.采购方式名称:method_name;2.采购方式权重:method_weight;3.采购方式id:id;
     * return Object
     */
    public function doEditMethod(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            $rules = [
                'id' => 'required|integer|exists:purchase_method',
                'method_name' => 'required|unique:purchase_method|max:255',
                'method_weight' => 'required|numeric',
                'method_property' => 'required|integer',
            ];
            $messages = [
                'id.required' => '采购方式id不能为空',
                'id.integer' => '采购方式id必须为整数',
                'id.exists' => '采购方式id错误',
                'method_name.required' => '采购方式名称不能为空',
                'method_name.unique' => '该采购方式已经存在',
                'method_name.max' => '采购方式名称不能超过255个字符',
                'method_weight.required' => '采购方式权重不能为空',
                'method_weight.numeric' => '采购方式权重必须为数值',
                'method_property.required' => '采购方式的属性不能为空',
                'method_property.integer' => '采购方式的属性必须为整数',
            ];
            $validator = Validator::make($param_info, $rules, $messages);
            if ($validator->fails()) {
                $msg = $validator->errors()->first();
                return response()->json(['code' => '1002', 'msg' => $msg]);
            }

            //获取指定采购方式信息
            $method_id = $param_info['id'];
            $purchase_method_info = PurchaseMethodModel::where('id', $method_id)->first();
            $purchase_method_info = $purchase_method_info->toArray();

            //组装更新数据
            $update_method = [];
            foreach ($param_info as $k => $v) {
                if (isset($purchase_method_info[$k]) && $purchase_method_info[$k] != $v) {
                    $update_method[$k] = $v;
                }
            }

            $updateRes = true;
            if (!empty($update_method)) {
                $updateRes = DB::table('purchase_method')->where('id', $method_id)->update($update_method);
            }

            $return_info = ['code' => '1004', 'msg' => '采购方式编辑失败'];
            if ($updateRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '采购方式编辑成功'];

                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_purchase_method',
                    'bus_desc' => '采购方式-确认提交编辑采购方式-采购方式代码：' . $purchase_method_info['method_sn'],
                    'bus_value' => $purchase_method_info["method_sn"],
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '采购方式-确认提交编辑采购方式',
                    'module_id' => 2,
                    'have_detail' => 1,
                ];

                $logDetailData["table_name"] = 'operate_log_detail';
                foreach ($param_info as $k => $v) {
                    if (isset($purchase_method_info[$k]) && $purchase_method_info[$k] != $v) {
                        $logDetailData["update_info"][] = [
                            'table_field_name' => $k,
                            'field_old_value' => $purchase_method_info[$k],
                            'field_new_value' => $v,
                        ];
                    }
                }
                if (isset($logDetailData['update_info'])) {
                    $operateLogModel->insertMoreLog($logData, $logDetailData);
                }
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
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


}