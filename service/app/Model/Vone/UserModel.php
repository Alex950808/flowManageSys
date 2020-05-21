<?php

namespace App\Model\Vone;

use Illuminate\Foundation\Auth\User as Authenticatable;

//引入entrust权限包 add by zhangdong on the 2018.07.18
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Notifications\Notifiable;

class UserModel extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    protected $table = 'user as u';

    //可操作字段
    protected $field = ['user_name', 'nickname', 'password', 'classify_id', 'last_ip', 'last_login', 'status'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description 更新用户登录IP
     * editor zongxing
     * date 2019.11.28
     * params: 1.用户名:$user_name;
     */
    public function upload_login_ip($user_name)
    {
        //获取登录IP
        $login_ip = $this->get_real_ip();
        DB::table($this->table)->where('user_name', $user_name)
            ->update(['last_ip' => $login_ip]);
    }

    /**
     * description:获取登录IP
     * editor:zongxing
     * date : 2018.06.23
     * return String
     */
    public function get_real_ip()
    {
        static $realip = NULL;

        if ($realip !== NULL) {
            return $realip;
        }

        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);

                    if ($ip != 'unknown') {
                        $realip = $ip;

                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;
    }


    /**
     * description 检查用户的信息
     * editor zongxing
     * date 2019.11.28
     * params 1.用户信息:$param_info;
     * return Array
     */
    public function check_user_info($param_info)
    {
        $user_info = DB::table('user')
            ->where(function ($where) use ($param_info) {
                if (isset($param_info['id'])) {
                    $where->orWhere('id', intval($param_info['id']));
                }
                if (isset($param_info['user_name'])) {
                    $where->orWhere('user_name', trim($param_info['user_name']));
                }
            })
            ->get();
        $user_info = ObjectToArrayZ($user_info);
        return $user_info;
    }

    /**
     * description 获取用户列表
     * editor zongxing
     * date 2019.11.28
     * return Array
     */
    public function getUserList($param_info, $is_page = 1)
    {
        $page_size = !empty($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $field = ['u.id', 'user_name', 'nickname', 'password', 'classify_id', 'classify_name', 'status'];

        $where = [];
        if (!empty($param_info['user_name'])) {
            $user_name = trim($param_info['user_name']);
            $where[] = ['u.user_name', $user_name];
        }
        if (!empty($param_info['nickname'])) {
            $nickname = trim($param_info['nickname']);
            $where[] = ['u.nickname', $nickname];
        }
        if (!empty($param_info['classify_id'])) {
            $classify_id = intval($param_info['classify_id']);
            $where[] = ['u.classify_id', $classify_id];
        }
        $user_list_obj = DB::table('user as u')
            ->select($field)
            ->leftJoin('classify as c', 'c.id', '=', 'u.classify_id');
            
        if ($is_page) {
            $user_list_info = $user_list_obj->where($where)->paginate($page_size);
        } else {
            $user_list_info = $user_list_obj->where($where)->get();
        }
        $user_list_info = ObjectToArrayZ($user_list_info);
        foreach ($user_list_info['data'] as $k => $v) {
            $status_str = $v['status'] == 1 ? '有效' : '停用';
            $user_list_info['data'][$k]['status_str'] = $status_str;
        }
        return $user_list_info;
    }

    /**
     * description 编辑用户
     * editor zongxing
     * date 2019.12.04
     * return Array
     */
    public function editUser($param_info, $old_user_info)
    {
        //组装创建用户数据
        $update_data = [];
        if ($old_user_info['user_name'] != trim($param_info['user_name'])) {
            $update_data['user_name'] = trim($param_info['user_name']);
        } elseif ($old_user_info['nickname'] != trim($param_info['nickname'])) {
            $update_data['nickname'] = trim($param_info['nickname']);
        } elseif ($old_user_info['password'] != $param_info['password']) {
            $update_data['password'] = bcrypt($param_info['password']);
        } elseif ($old_user_info['classify_id'] != intval($param_info['classify_id'])) {
            $update_data['classify_id'] = trim($param_info['classify_id']);
        }
        $updateRes = 1;
        if($update_data){
            $updateRes = DB::table('user')->where('id', intval($param_info['id']))->update($update_data);
        }
        return $updateRes;
    }

}
