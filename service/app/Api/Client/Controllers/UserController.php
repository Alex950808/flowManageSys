<?php

namespace App\Api\Client\Controllers;

use App\Http\Controllers\Controller;
use App\Model\Vone\UserModel;
use App\Model\Vone\VersionLogModel;
use Illuminate\Http\Request;
use JWTAuth;


class UserController extends Controller
{
    /**
     * description app登录
     * editor zongxing
     * date 2019.08.08
     * params 1.用户名:user_name;2.密码:password
     * return Object
     */
    public function AuthLogin(Request $request)
    {
        $param_info = $request->toArray();
        try {
            if (empty($param_info['user_name'])) {
                return response()->json(['error' => '1001', 'msg' => '用户名不能为空']);
            } else if (empty($param_info['password'])) {
                return response()->json(['error' => '1002', 'msg' => '密码不能为空']);
            }
            $user_data = [
                'user_name' => trim($param_info['user_name']),
                'password' => trim($param_info['password']),
                'status' => 1
            ];
            if (!$token = JWTAuth::attempt($user_data)) {
                return response()->json(['error' => '1003', 'msg' => '用户名或密码错误']);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => '不能创建token'], 500);
        }
        //更新IP
        $user_model = new UserModel();
        $user_model->upload_login_ip($user_data['user_name']);
        //获取用户信息
        $loginUserInfo = $request->user();
        $user_info = [
            'user_name'=>$loginUserInfo->user_name,
            'nickname'=>$loginUserInfo->nickname,
        ];
        //系统版本信息
        $vl_model = new VersionLogModel();
        $sys_version = $vl_model->getVersion(1);
        $sys_version_info = [];
        if ($sys_version) {
            $sys_version_info = [
                'serial_num' => $sys_version->serial_num,
                'content' => $sys_version->content,
            ];
        }
        $code = "1000";
        $msg = "登陆成功";
        $return_info = compact('code', 'msg', 'token', 'sys_version_info', 'user_info');
        return response()->json($return_info);
    }

}
