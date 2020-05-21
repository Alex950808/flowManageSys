<?php

namespace App\Api\Vone\Controllers;

use App\Model\Vone\OperateLogModel;
use App\Model\Vone\PurchaseChannelModel;
use App\Model\Vone\TeamModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facades;
use Illuminate\Support\Facades\Validator;

/**
 * description:采购模块控制器
 * editor:zongxing
 * date : 2018.06.25
 */
class ChannelController extends BaseController
{
    /**
     * description:添加采购渠道
     * editor:zongxing
     * type:POST
     * date : 2018.06.26
     * params: 1.采购渠道名称:channels_name;
     * return Object
     */
    public function createChannels(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            $rules = [
                'channels_name' => 'required|unique:purchase_channels|max:255',
                'method_id' => 'required|integer',
                'original_or_discount' => 'required|integer',
                'is_count_wai' => 'required|required_with:post_discount|integer',
                'post_discount' => 'required|required_with:is_count_wai|numeric',
                'recharge_points' => 'sometimes|numeric',
            ];
            $messages = [
                'channels_name.required' => '采购渠道名称不能为空',
                'channels_name.unique' => '该采购渠道已经存在',
                'channels_name.max' => '采购渠道名称不能超过255个字符',
                'method_id.required' => '采购渠道所属的方式不能为空',
                'method_id.integer' => '采购渠道所属的方式必须为整数',
                'original_or_discount.required' => '采购渠道所属的结算方式不能为空',
                'original_or_discount.integer' => '采购渠道所属的结算方式必须为整数',
                'is_count_wai.required' => '该渠道是否计算外采临界点不能为空',
                'is_count_wai.required_with' => '该渠道是否计算外采临界点与渠道运费折扣必须同时存在',
                'is_count_wai.integer' => '该渠道是否计算外采临界点必须为整数',
                'post_discount.required' => '渠道运费折扣不能为空',
                'post_discount.required_with' => '渠道运费折扣与该渠道是否计算外采临界点必须同时存在',
                'post_discount.numeric' => '渠道运费折扣必须为数值',
                'recharge_points.numeric' => '充值返积分比例必须为数值',
            ];
            $validator = Validator::make($param_info, $rules, $messages);
            if($validator->fails()){
                $msg = $validator->errors()->first();
                return response()->json(['code' => '1002', 'msg' => $msg]);
            }
            //计算采购期编号
            $model_obj = new PurchaseChannelModel();
            $model_field = "channels_sn";
            $pin_head = "QD-";
            $last_channel_sn = createNo($model_obj, $model_field, $pin_head, false);
            $insert_purchase_channel = [
                'channels_sn' => $last_channel_sn,
                'channels_name' => trim($param_info['channels_name']),
                'method_id' => $param_info['method_id'],
                'original_or_discount' => $param_info['original_or_discount'],
                'post_discount' => !empty($param_info['post_discount']) ? floatval($param_info['post_discount']) : 0,
                'recharge_points' => !empty($param_info['recharge_points']) ? floatval($param_info['recharge_points']) : 0,
                'is_count_wai' => !empty($param_info['is_count_wai']) ? intval($param_info['is_count_wai']) : 0,
                'post_discount' => !empty($param_info['post_discount']) ? floatval($param_info['post_discount']) : 0,
            ];
            $res = DB::table('purchase_channels')->insert($insert_purchase_channel);
            $return_info = ['code' => '1003', 'msg' => '采购渠道添加失败'];
            if ($res != false) {
                $return_info = ['code' => '1000', 'msg' => '采购渠道添加成功'];
            }
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:获取采购渠道列表
     * editor:zongxing
     * type:GET
     * date : 2018.06.26
     * return Object
     */
    public function getChannelsList(Request $request)
    {
        $param_info = $request->toArray();
        $pc_model = new PurchaseChannelModel();
        $pc_info = $pc_model->getChannelList($param_info);
        $return_info = ['code' => '1000', 'msg' => '获取采购渠道列表成功', 'data' => $pc_info];
        return response()->json($return_info);
    }

    /**
     * description 获取团队列表
     * author zongxing
     * type GET
     * date 2019.11.22
     * return json
     */
    public function teamList(Request $request)
    {
        $team_model = new TeamModel();
        $team_list = $team_model->teamList();
        return response()->json(['code' => '1000', 'msg' => '获取团队列表成功', 'data' => $team_list]);
    }

    /**
     * description 新增团队
     * author zongxing
     * type POST
     * date 2019.11.22
     * params 1.团队名称:team_name;2.所属渠道:channel_id;
     * return json
     */
    public function addTeam(Request $request)
    {
        $param_info = $request->toArray();
        $team_model = new TeamModel();
        $team_list = $team_model->teamList($param_info);
        if (!empty($team_list)) {
            return response()->json(['code' => '1001', 'msg' => '该团队已经存在']);
        }
        $res = $team_model->addTeam($param_info);
        $return_info = ['code' => '1002', 'msg' => '新增团队失败'];
        if ($res != false) {
            $return_info = ['code' => '1000', 'msg' => '新增团队成功'];
        }
        return response()->json($return_info);
    }

    /**
     * description 编辑团队
     * author zongxing
     * type POST
     * date 2019.11.22
     * params 1.团队名称:team_name;2.所属渠道:channel_id;3.团队id:id;
     * return json
     */
    public function editTeam(Request $request)
    {
        $param_info = $request->toArray();
        $team_model = new TeamModel();
        //判断团队是否存在
        $param['id'] = intval($param_info['id']);
        $team_list = $team_model->teamList($param);
        if (empty($team_list)) {
            return response()->json(['code' => '1001', 'msg' => '该团队不存在']);
        }
        //判断编辑的团队名称是否存在
        $param['channel_id'] = intval($param_info['channel_id']);
        $param['team_name'] = trim($param_info['team_name']);
        $team_list = $team_model->teamList($param);
        if (!empty($team_list) && $team_list[0]['id'] != $param['id']) {
            return response()->json(['code' => '1002', 'msg' => '该团队名称已经存在']);
        }
        $res = $team_model->editTeam($param_info);
        $return_info = ['code' => '1003', 'msg' => '编辑团队失败'];
        if ($res !== false) {
            $return_info = ['code' => '1000', 'msg' => '编辑团队成功'];
        }
        return response()->json($return_info);
    }

    /**
     * description:打开编辑采购渠道详情页
     * editor:zongxing
     * type:POST
     * date : 2019.01.25
     * params: 1.采购渠道id:id;
     * return Array
     */
    public function editChannel(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            $rules = [
                'id' => 'required|integer|exists:purchase_channels,id'
            ];
            $messages = [
                'id.required' => '采购渠道id不能为空',
                'id.integer' => '采购渠道id必须为整数',
                'id.exists' => '采购渠道id错误'
            ];
            $validator = Validator::make($param_info, $rules, $messages);
            if($validator->fails()){
                $msg = $validator->errors()->first();
                return response()->json(['code' => '1002', 'msg' => $msg]);
            }
            //获取指定采购渠道信息
            $purchase_channel_info = PurchaseChannelModel::where('id', intval($param_info['id']))->get();
            $return_info = ['code' => '1000', 'msg' => '获取指定采购渠道信息成功', 'data' => $purchase_channel_info];
        } else {
            $return_info = ['code' => '1001', 'msg' => '请求错误'];
        }
        return response()->json($return_info);
    }

    /**
     * description:确认提交编辑采购渠道
     * editor:zongxing
     * type:POST
     * date : 2019.01.25
     * return Object
     */
    public function doEditChannel(Request $request)
    {
        if ($request->isMethod('post')) {
            $param_info = $request->toArray();
            $rules = [
                'id' => 'required|integer|exists:purchase_channels',
                'channels_name' => 'required|unique:purchase_channels|max:255',
                'method_id' => 'required|integer',
                'original_or_discount' => 'required|integer',
                'is_count_wai' => 'required|required_with:post_discount|integer',
                'post_discount' => 'required|required_with:is_count_wai|numeric',
                'recharge_points' => 'sometimes|numeric',
            ];
            $messages = [
                'id.required' => '采购渠道id不能为空',
                'id.integer' => '采购渠道id必须为整数',
                'id.exists' => '采购渠道id错误',
                'channels_name.required' => '采购渠道名称不能为空',
                'channels_name.unique' => '该采购渠道已经存在',
                'channels_name.max' => '采购渠道名称不能超过255个字符',
                'method_id.required' => '采购渠道所属的方式不能为空',
                'method_id.integer' => '采购渠道所属的方式必须为整数',
                'original_or_discount.required' => '采购渠道所属的结算方式不能为空',
                'original_or_discount.integer' => '采购渠道所属的结算方式必须为整数',
                'is_count_wai.required' => '该渠道是否计算外采临界点不能为空',
                'is_count_wai.required_with' => '该渠道是否计算外采临界点与渠道运费折扣必须同时存在',
                'is_count_wai.integer' => '该渠道是否计算外采临界点必须为整数',
                'post_discount.required' => '渠道运费折扣不能为空',
                'post_discount.required_with' => '渠道运费折扣与该渠道是否计算外采临界点必须同时存在',
                'post_discount.numeric' => '渠道运费折扣必须为数值',
                'recharge_points.numeric' => '充值返积分比例必须为数值',
            ];
            $validator = Validator::make($param_info, $rules, $messages);
            if($validator->fails()){
                $msg = $validator->errors()->first();
                return response()->json(['code' => '1002', 'msg' => $msg]);
            }

            //获取采购渠道信息
            $channel_id = intval($param_info['id']);
            $purchase_channel_info = PurchaseChannelModel::where('id', $channel_id)->first();
            $purchase_channel_info = objectToArrayZ($purchase_channel_info);

            //组装更新数据
            $update_channel = [];
            foreach ($param_info as $k => $v) {
                if (isset($purchase_channel_info[$k]) && $purchase_channel_info[$k] != $v) {
                    $update_channel[$k] = $v;
                }
            }

            $updateRes = true;
            if (!empty($update_channel)) {
                $updateRes = DB::table('purchase_channels')->where('id', $channel_id)->update($update_channel);
            }

            $return_info = ['code' => '1004', 'msg' => '采购渠道编辑失败'];
            if ($updateRes !== false) {
                $return_info = ['code' => '1000', 'msg' => '采购渠道编辑成功'];
                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_purchase_channels',
                    'bus_desc' => '采购渠道-确认提交编辑采购渠道-采购渠道代码：' . $purchase_channel_info['channels_sn'],
                    'bus_value' => $purchase_channel_info['channels_sn'],
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '采购渠道-确认提交编辑采购渠道',
                    'module_id' => 2,
                    'have_detail' => 1,
                ];

                $logDetailData['table_name'] = 'operate_log_detail';
                foreach ($update_channel as $k => $v) {
                    if (isset($purchase_channel_info[$k]) && $purchase_channel_info[$k] != $v) {
                        $logDetailData["update_info"][] = [
                            'table_field_name' => $k,
                            'field_old_value' => $purchase_channel_info[$k],
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
