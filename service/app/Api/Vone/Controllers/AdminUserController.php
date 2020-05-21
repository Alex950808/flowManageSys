<?php

namespace App\Api\Vone\Controllers;

use App\Model\Vone\AdminUserModel;
use App\Model\Vone\DepartmentModel;
use App\Model\Vone\OperateLogModel;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Vone\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * description:采购模块控制器
 * editor:zongxing
 * date : 2018.06.25
 */
class AdminUserController extends BaseController
{
    /**
     * 添加管理员
     * author zongxing
     * date 2020/4/20 0020
     * @param Request $request
     *      user_name:1//用户名;
            password:1//密码;
            confirm_password:1//确认密码;
            role_id:1//角色id;
            user_img:1//管理员头像;
            department_id:1//部门id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAdminUser(Request $request)
    {
        $param_info = $request->toArray();
        $rules = [
            'user_name' => 'required|unique:admin_user',
            'nickname' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|required_with:password|same:password',
            'role_id' => 'required|integer',
            'department_id' => 'required|integer',
            'user_img' => 'sometimes|string',
        ];
        $messages = [
            'user_name.required' => '用户名不能为空',
            'user_name.unique' => '该用户名已经存在',
            'nickname.required' => '昵称不能为空',
            'password.required' => '密码不能为空',
            'confirm_password.required' => '确认密码不能为空',
            'confirm_password.required_with' => '确认密码与密码必须同时存在',
            'confirm_password.same' => '两次输入的密码不一致',
            'role_id.required' => '角色不能为空',
            'role_id.integer' => '角色id必须为整数',
            'department_id.required' => '部门id不能为空',
            'department_id.integer' => '部门id必须为整数',
            'user_img.string' => '用户头像必须为字符',
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if($validator->fails()){
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }

        $role_id = intval($param_info['role_id']);
        $password = bcrypt($param_info['password']);
        $insert_user_data = [
            'user_name' => trim($param_info['user_name']),
            'nickname' => trim($param_info['nickname']),
            'user_img' => trim($param_info['user_img']),
            'password' => $password,
            'role_id' => $role_id,
            'department_id' => intval($param_info['department_id']),
        ];
        $user_id = DB::table("admin_user")->insertGetId($insert_user_data);
        $return_info = ['code' => '1003', 'msg' => '添加管理员失败'];
        if ($user_id !== false) {
            $return_info = ['code' => '1000', 'msg' => '添加管理员成功'];
            //添加管理员成功，添加角色
            $user = AdminUserModel::where('id', $user_id)->first();
            $user->attachRole($role_id); //参数可以是Role对象，数组或id

            //记录日志
            $operateLogModel = new OperateLogModel();
            $loginUserInfo = $request->user();
            $logData = [
                'table_name' => 'jms_admin_user',
                'bus_desc' => '权限模块-添加管理员-管理员id：' . $user_id,
                'bus_value' => $user_id,
                'admin_name' => trim($loginUserInfo->user_name),
                'admin_id' => trim($loginUserInfo->id),
                'ope_module_name' => '权限模块-添加管理员',
                'module_id' => 5,
                'have_detail' => 0,
            ];
            $operateLogModel->insertLog($logData);
        }
        return response()->json($return_info);
    }

    /**
     * description:获取管理员列表
     * editor:zongxing
     * type:GET
     * date : 2018.07.27
     * return Object
     */
    public function adminUserList(Request $request)
    {
        //获取管理员列表信息
        $param_info = $request->toArray();
        $adminUserModel = new AdminUserModel();
        $user_list_info = $adminUserModel->getUserList($param_info);

        //角色信息
        $role_model = new Role();
        $role_list = $role_model->get_role_list();
        if (empty($role_list)) {
            return response()->json(['code' => '1004', 'msg' => '角色列表信息错误']);
        }

        //部门信息
        $department_model = new DepartmentModel();
        $department_info = $department_model->getDepartmentInfo();
        if (empty($department_info)) {
            return response()->json(['code' => '1005', 'msg' => '部门列表信息错误']);
        }

        $data = [
            'user_list_info' => $user_list_info,
            'role_list' => $role_list,
            'department_info' => $department_info
        ];
        if (empty($user_list_info['data'])) {
            return response()->json(['code' => '1003', 'msg' => '暂无管理员', 'data' => $data]);
        }
        $return_info = ['code' => '1000', 'msg' => '获取管理员列表成功', 'data' => $data];
        return response()->json($return_info);
    }

    /**
     * 获取管理员信息
     * author zongxing
     * date 2020/4/8
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function getAdminUserInfo(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['user_id'])) {
            return response()->json(['code' => '1002', 'msg' => '参数有误']);
        }
        $user_id = intval($param_info['user_id']);
        $adminUserModel = new AdminUserModel();
        $admin_user_info = $adminUserModel->getAdminUserInfo($user_id);
        if (empty($admin_user_info)) {
            return response()->json(['code' => '1003', 'msg' => '管理员不存在']);
        }
        return response()->json(['code' => '1000', 'msg' => '获取管理员信息成功', 'data' => $admin_user_info]);

    }

    /**
     * 编辑管理员信息
     * author zongxing
     * date 2020/4/9
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function eidtAdminUser(Request $request)
    {
        $param_info = $request->toArray();
        $rules = [
            'id' => 'required|exists:admin_user,id',
            'nickname' => 'sometimes|string',
            'role_id' => 'sometimes|exists:roles,id',
            'user_img' => 'sometimes|string',
        ];
        $messages = [
            'id.required' => '管理员id不能为空',
            'id.exists' => '管理员id有误',
            'role_id.exists' => '角色id有误',
            'nickname.string' => '昵称必须为字符',
            'user_img.string' => '管理员头像必须为字符',
        ];
        $validator = Validator::make($param_info, $rules, $messages);
        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json(['code' => '1002', 'msg' => $msg]);
        }

        //组装更新数据
        $updateRoleData = $updateUserData = [];
        if (!empty($param_info['role_id'])) {
            $updateRoleData['role_id'] = intval($param_info['role_id']);
            $updateUserData['role_id'] = intval($param_info['role_id']);
        }
        if (!empty($param_info['user_img'])) {
            $updateUserData['user_img'] = trim($param_info['user_img']);
        }
        if (!empty($param_info['nickname'])) {
            $updateUserData['nickname'] = trim($param_info['nickname']);
        }


        //更改管理员信息
        $user_id = intval($param_info['id']);
        $updateRes = DB::transaction(function () use ($user_id, $updateUserData, $updateRoleData
        ) {
            $updateRes = true;
            if (!empty($updateRoleData)){
                $updateRes = DB::table('role_user')->where('user_id', $user_id)->update($updateRoleData);
            }
            if (!empty($updateUserData)){
                $updateRes = DB::table('admin_user')->where('id', $user_id)->update($updateUserData);
            }
            return $updateRes;
        });

        $return_info = ['code' => '1003', 'msg' => '编辑管理员信息失败'];
        if ($updateRes !== false) {
            $return_info = ['code' => '1000', 'msg' => '编辑管理员信息成功'];
        }
        return response()->json($return_info);
    }

    /**
     * description:删除管理员
     * editor:zongxing
     * type:POST
     * date:2018.07.27
     * return Object
     */
    public function delAdminUser(Request $request)
    {
        if ($request->isMethod("get")) {
            $admin_user_info = $request->toArray();
            if (empty($admin_user_info["id"])) {
                return response()->json(['code' => '1002', 'msg' => '参数有误']);
            }

            //检查管理员的信息
            $adminUserModel = new AdminUserModel();
            $check_user_info = $adminUserModel->check_user_info($admin_user_info);
            if (empty($check_user_info)) {
                return response()->json(['code' => '1003', 'msg' => '该管理员不存在']);
            }

            //更改管理员状态
            $updateRes = DB::table('admin_user')->where('id', $admin_user_info["id"])->update(['status' => 2]);

            $code = "1004";
            $msg = "删除管理员失败";
            $return_info = compact('code', 'msg');

            if ($updateRes) {
                $code = "1000";
                $msg = "删除管理员成功";
                $return_info = compact('code', 'msg');

                //记录日志
                $operateLogModel = new OperateLogModel();
                $loginUserInfo = $request->user();
                $logData = [
                    'table_name' => 'jms_task',
                    'bus_desc' => '权限模块-删除管理员-修改管理员id：' . $admin_user_info["id"] . '的status',
                    'bus_value' => 2,
                    'admin_name' => trim($loginUserInfo->user_name),
                    'admin_id' => trim($loginUserInfo->id),
                    'ope_module_name' => '权限模块-删除管理员',
                    'module_id' => 5,
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
     * description:通过角色id获取管理员列表
     * editor:zongxing
     * type:POST
     * date : 2018.08.22
     * return Object
     */
    public function userOfRole(Request $request)
    {
        if ($request->isMethod("post")) {
            $role_info = $request->toArray();
            $role_id = $role_info["role_id"];
            $user_list_info = DB::table('admin_user as au')
                ->leftJoin('role_user as ru', 'ru.user_id', 'au.id')
                ->where('ru.role_id', $role_id)->get(['au.id', 'au.user_name']);
            $user_list_info = $user_list_info->toArray();

            $code = "1002";
            $msg = "暂无管理员";
            $return_info = compact('code', 'msg');
            if (!empty($user_list_info)) {
                $code = "1000";
                $msg = "获取管理员列表成功";
                $data = $user_list_info;
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
     * 管理员头像上传
     */
    public function uploadAdminUserImg(Request $request)
    {
        $param_info = $request->toArray();
        if (empty($param_info['user_id']) || empty($param_info['user_name'])) {
            return response()->json(['code' => '1002', 'msg' => '参数有误']);
        }
        $file = $param_info['user_img'];
        // 1.是否上传成功
        if (!$file->isValid()) {
            return response()->json(['code' => '1003', 'msg' => '上传失败']);
        }
        // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
        $fileExtension = $file->getClientOriginalExtension();
        if (!in_array($fileExtension, ['png', 'jpg', 'gif'])) {
            return response()->json(['code' => '1004', 'msg' => '文件格式错误']);
        }
        // 3.判断大小是否符合 2M
        $tmpFile = $file->getRealPath();
        if (filesize($tmpFile) >= 2048000) {
            return response()->json(['code' => '1005', 'msg' => '文件超过不能超过2M']);
        }
        // 4.是否是通过http请求表单提交的文件
        if (!is_uploaded_file($tmpFile)) {
            return response()->json(['code' => '1006', 'msg' => '文件错误']);
        }
        // 5.存储图片, 生成一个随机文件名
        $user_name = $param_info['user_name'];
        $fileName = $user_name . mt_rand(0, 9999) . '.' . $fileExtension;
        $disk = 'user_img';
        if (Storage::disk($disk)->put($fileName, file_get_contents($tmpFile))) {
            $fileName = $disk . '/' . $fileName;
            return response()->json(['code' => '1000', 'msg' => '上传成功', 'user_img' => $fileName]);
        }
        return response()->json(['code' => '1003', 'msg' => '上传失败']);
    }


}
