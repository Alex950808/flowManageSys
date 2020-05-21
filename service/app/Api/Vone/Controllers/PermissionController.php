<?php

namespace App\Api\Vone\Controllers;

use App\Model\Vone\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * description:采购模块控制器
 * editor:zongxing
 * date : 2018.06.25
 */
class PermissionController extends BaseController
{
    /**
     * description:添加权限
     * editor:zongxing
     * type:POST
     * date : 2018.07.27
     * params: 1.权限简称:name;2.权限名称:display_name;3.权限描述:description;
     * return Object
     */
    public function addPermission(Request $request)
    {
        if ($request->isMethod("post")) {
            $permission_info = $request->toArray();

            if (empty($permission_info["name"])) {
                return response()->json(['code' => '1002', 'msg' => '权限代码不能为空']);
            } else if (empty($permission_info["web_name"])) {
                return response()->json(['code' => '1003', 'msg' => '权限前端代码不能为空']);
            } else if (empty($permission_info["display_name"])) {
                return response()->json(['code' => '1004', 'msg' => '权限名称不能为空']);
            } else if (empty($permission_info["description"])) {
                return response()->json(['code' => '1005', 'msg' => '权限描述不能为空']);
            } else if (empty($permission_info["rank"])) {
                return response()->json(['code' => '1006', 'msg' => '权限等级不能为空']);
            } else if (!isset($permission_info["parent_id"])) {
                return response()->json(['code' => '1007', 'msg' => '权限上一级不能为空']);
            }

            //检查权限信息
            $permissionModel = new Permission();
            $check_permission_info = $permissionModel->check_permission_info($permission_info);

            if (!empty($check_permission_info)) {
                return response()->json(['code' => '1008', 'msg' => '该权限已经存在']);
            }

            //添加权限信息
            $addAdminUser = new Permission();
            $addAdminUser->name = $permission_info["name"];
            $addAdminUser->web_name = $permission_info["web_name"];
            $addAdminUser->display_name = $permission_info["display_name"];
            $addAdminUser->description = $permission_info["description"];
            $addAdminUser->rank = $permission_info["rank"];
            $addAdminUser->parent_id = $permission_info["parent_id"];
            $insertRes = $addAdminUser->save();

            $code = "1009";
            $msg = "添加权限失败";
            $return_info = compact('code', 'msg');

            if ($insertRes) {
                //保存权限列表信息
                $permission_list_info = DB::table("permissions")->get();
                $permission_list_info = $permission_list_info->toArray();
                $permission_list_info = json_encode($permission_list_info);
                Redis::set("permission_list_info",$permission_list_info);

                $code = "1000";
                $msg = "添加权限成功";
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
     * description:获取用户权限
     * editor:zongxing
     * type:GET
     * date : 2018.07.30
     * return Object
     */
    public function getPermission(Request $request)
    {
        if ($request->isMethod("get")) {
            $loginUserInfo = $request->user();
            $permissionModel = new Permission();
            $user_permission_info = $permissionModel->get_user_permission($loginUserInfo);

            $code = "1002";
            $msg = "您暂无权限";
            $return_info = compact('code', 'msg');

            if ($user_permission_info) {
                $code = "1000";
                $msg = "获取权限成功";
                $data = $user_permission_info;
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
     * description:获取权限列表
     * editor:zongxing
     * type:GET
     * date : 2018.07.30
     * return Object
     */
    public function permissiomList(Request $request)
    {
        $param_info = $request->toArray();
        if (isset($param_info['rank']) && $param_info['rank'] == 0) {
            //存入redis
            // $permission_list_info = Redis::get('permission_list_info');
            // if (empty($permission_list_info)) {
            //     $where = [];
            //     if (!empty($param_info['name'])) {
            //         $where[] = ['name', 'like', trim($param_info['name'])];
            //     }
            //     if (!empty($param_info['web_name'])) {
            //         $where[] = ['web_name', 'like', trim($param_info['web_name'])];
            //     }
            //     if (!empty($param_info['display_name'])) {
            //         $where[] = ['display_name', 'like', trim($param_info['display_name'])];
            //     }
            //     $permission_list_info = DB::table('permissions')->where($where)->get();
            //     $permission_list_info = $permission_list_info->toArray();
            //     $permission_list_info = json_encode($permission_list_info);
            //     Redis::set('permission_list_info',$permission_list_info);
            // }
            // $permission_list_info = json_decode($permission_list_info, true);
            $where = [];
            if (!empty($param_info['name'])) {
                $name = '%'.trim($param_info['name']).'%';
                $where[] = ['name', 'like', $name];
            }
            if (!empty($param_info['web_name'])) {
                $web_name = '%'.trim($param_info['web_name']).'%';
                $where[] = ['web_name', 'like', $web_name];
            }
            if (!empty($param_info['display_name'])) {
                $display_name = '%'.trim($param_info['display_name']).'%';
                $where[] = ['display_name', 'like', $display_name];
            }
            if (!empty($param_info['is_page']) && $param_info['is_page'] == 1) {
                $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
                $permission_list_info = DB::table('permissions')->where($where)->paginate($page_size);
            } else {
                $permission_list_info = DB::table('permissions')->where($where)->get();
            }
            
            $permission_list_info = objectToArrayZ($permission_list_info);
        } else {
            $permissionModel = new Permission();
            $permission_list_info = $permissionModel->get_permission_list($param_info);
        }

        $return_info = ['code' => '1000', 'msg' => '获取权限列表成功', 'data' => $permission_list_info];
        if (empty($permission_list_info)) {
            $return_info = ['code' => '1002', 'msg' => '暂无权限'];
        }
        return response()->json($return_info);
    }


}