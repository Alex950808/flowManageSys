<?php

namespace App\Model\Vone;

use Illuminate\Foundation\Auth\User as Authenticatable;

//引入entrust权限包 add by zhangdong on the 2018.07.18
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\Redis; //add zhangdong 2020.03.25

class AdminUserModel extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    protected $table = 'admin_user as au';

    //可操作字段
    protected $field = ['au.id', 'au.user_name', 'au.nickname', 'au.password', 'au.role_id',
        'au.last_ip', 'au.last_login', 'au.department_id', 'au.classify_id', 'au.user_img'];

    //修改laravel 自动更新
    const UPDATED_AT = 'modify_time';
    const CREATED_AT = 'create_time';

    /**
     * description:更新用户登录IP
     * editor:zongxing
     * date : 2018.06.23
     * params: 1.用户名:$user_name;
     */
    public function upload_login_ip($user_name)
    {
        //获取登录IP
        $login_ip = $this->get_real_ip();

        AdminUserModel::where('user_name', $user_name)
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
     * description:检查管理员的信息
     * editor:zongxing
     * date : 2018.07.27
     * params: 1.请求参数:$admin_user_info;
     * return Array
     */
    public function check_user_info($admin_user_info)
    {
        if (isset($admin_user_info["id"])) {
            //检查管理员权限
            $user_info = DB::table("admin_user")
                ->where("id", $admin_user_info["id"])
                ->get();
        } else {
            $user_info = DB::table("admin_user")
                ->where("user_name", $admin_user_info["user_name"])
                ->get();
        }

        $user_info = $user_info->toArray();
        return $user_info;
    }

    /**
     * 获取管理员信息
     * author zongxing
     * date 2020/4/8
     * @param $user_id 管理员id
     * @return mixed
     */
    public function getAdminUserInfo($user_id)
    {
        $field = $this->field;
        $add_field = ['d.de_name as department_name'];
        $field = array_merge($field, $add_field);
        $user_info = DB::table($this->table)
            ->leftJoin('department as d','d.department_id','au.department_id')
            ->where('id', $user_id)->first($field);
        return $user_info;
    }

    /**
     * description:检查管理员的信息
     * editor:zongxing
     * date : 2018.07.27
     * params: 1.请求参数:$admin_user_info;
     * return Array
     */
    public function get_user_role($admin_user_info)
    {
        //检查管理员权限
        $user_info = DB::table("role_user")
            ->where("user_id", $admin_user_info["user_id"])
            ->get(["role_id"]);
        $user_info = $user_info->toArray();
        return $user_info;
    }


    /**
     * description:获取管理员列表
     * editor:zongxing
     * date : 2018.07.27
     * return Object
     */
    public function getUserList($param_info = [])
    {
        $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;
        $field = ['au.id', 'user_name', 'nickname', 'user_img', 'au.role_id', 'display_name as role_name',
            'au.department_id', 'de_name'];

        $where = [];
        if (!empty($param_info['user_name'])) {
            $user_name = '%' . trim($param_info['user_name']) . '%';
            $where[] = ['au.user_name', 'like', $user_name];
        }
        if (!empty($param_info['nickname'])) {
            $nickname = '%' . trim($param_info['nickname']) . '%';
            $where[] = ['au.nickname', 'like', $nickname];
        }
        if (!empty($param_info['role_id'])) {
            $role_id = intval($param_info['role_id']);
            $where[] = ['au.role_id', $role_id];
        }
        if (!empty($param_info['department_id'])) {
            $department_id = intval($param_info['department_id']);
            $where[] = ['au.department_id', $department_id];
        }

        $user_list_info = DB::table('admin_user as au')->select($field)
            ->leftJoin('roles as r', 'r.id', 'au.role_id')
            ->leftJoin('department as d', 'd.department_id', 'au.department_id')
            ->where($where)
            ->orderby('au.create_time')
            ->paginate($page_size);
        $user_list_info = objectToArrayZ($user_list_info);
        return $user_list_info;
    }

    /**
     * desc 从redis中管理员信息
     * author zhangdong
     * date : 2020.03.25
     */
    public function getAdminInfoInRedis()
    {
        //从redis中获取管理员信息，如果没有则对其设置
        $adminInfo = Redis::get('adminInfo');
        if (empty($adminInfo)) {
            $field = ['id', 'user_name', 'nickname'];
            $adminInfo = DB::table($this->table)->select($field)->get()
                ->map(function ($value) {
                    return (array)$value;
                })->toArray();
            Redis::set('adminInfo', json_encode($adminInfo, JSON_UNESCAPED_UNICODE));
            $adminInfo = Redis::get('adminInfo');
        }
        $adminInfo = objectToArray(json_decode($adminInfo));
        return $adminInfo;
    }


}//end of class
