<?php
namespace App\Api\Vone\Controllers;

use App\Model\Vone\NoticeModel;
use Dingo\Api\Contract\Http\Request;
use Illuminate\Support\Facades\DB;

class NoticeController extends BaseController
{

    /**
     * description:新增公告
     * editor:zongxing
     * type:POST
     * date : 2018.09.04
     * params: 1.公告日期:notice_date;2.公告时间:notice_time;3.公告内容:notice_content;
     * return Object
     */
    public function addNotice(Request $request)
    {
        if ($request->isMethod('post')) {
            $notice_info = $request->toArray();

            if (empty($notice_info['notice_content'])) {
                return response()->json(['code' => '1004', 'msg' => '公告内容不能为空']);
            }

            //计算公告编号
            $model_obj = new NoticeModel();
            $model_field = "notice_sn";
            $pin_head = "GG-";
            $last_notice_sn = createNo($model_obj, $model_field, $pin_head, true);
            $notice_info["notice_sn"] = $last_notice_sn;

            $loginUserInfo = $request -> user();
            $depart_id = intval($loginUserInfo -> department_id);
            $user_id = intval($loginUserInfo -> id);
            $notice_info["depart_id"] = $depart_id;
            $notice_info["user_id"] = $user_id;

            $notice_add_res = DB::table("notice")->insert($notice_info);

            if (!$notice_add_res) {
                return response()->json(['code' => '1005', 'msg' => '公告添加失败']);
            }

            $code = "1000";
            $msg = "公告添加成功";
            $return_info = compact('code', 'msg');
        } else {
            $code = "1001";
            $msg = "请求错误";
            $return_info = compact('code', 'msg');
        }
        return response()->json($return_info);
    }

    /**
     * description:获取公告列表
     * editor:zongxing
     * type:GET
     * date : 2018.09.04
     * return Object
     */
    public function noticeList(Request $request)
    {
        $param_info = $request->toArray();
        $field = ['notice_sn', 'notice_content', 'au.id as user_id', 'user_name', 'd.department_id', 'de_name'];
        if (!empty($param_info['query_sn']) && $param_info['query_sn'] == 'index'){
            $now_mouth = date("Y-m", time());
            $now_mouth = "%".$now_mouth."%";

            $notice_list = DB::table("notice as n")
                ->leftJoin("admin_user as au","au.id","=","n.user_id")
                ->leftJoin("department as d","d.department_id","=","n.depart_id")
                ->where(DB::raw('Date(jms_n.create_time)'), "LIKE", $now_mouth)
                ->orderBy("n.create_time", "desc")->get($field);
        }else{
            //搜索关键字
            $keywords = isset($param_info['query_sn']) ? trim($param_info['query_sn']) : '';
            $page_size = isset($param_info['page_size']) ? intval($param_info['page_size']) : 15;

            $where = [];
            if (isset($param_info['query_sn'])) {
                $query_sn = '%'. trim($param_info['query_sn']) . '%';
                $where[] = ['notice_content', 'LIKE', $query_sn];
            }
            if (isset($param_info['notice_sn'])) {
                $notice_sn = trim($param_info['notice_sn']);
                $where[] = ['notice_sn', $notice_sn];
            }
            if (isset($param_info['user_id'])) {
                $user_id = intval($param_info['user_id']);
                $where[] = ['au.id', $user_id];
            }
            if (isset($param_info['department_id'])) {
                $department_id = intval($param_info['department_id']);
                $where[] = ['d.department_id', $department_id];
            }
            
            $notice_list = DB::table('notice as n')->select($field)
                ->leftJoin('admin_user as au', 'au.id', 'n.user_id')
                ->leftJoin('department as d', 'd.department_id', 'n.depart_id')
                ->where($where)
                ->orderBy('n.create_time', 'desc')->paginate($page_size);
            $notice_list = objectToArrayZ($notice_list);
        }

        $return_info = ['code' => '1002', 'msg' => '暂无公告'];
        $notice_info = isset($notice_list['data']) ? $notice_list['data'] : $notice_list;
        if (!empty($notice_info)) {
            $department_arr = $user_arr = [];
            foreach ($notice_info as $k => $v) {
                $department_arr[] = [
                    'department_id'=> $v['department_id'],
                    'de_name'=> $v['de_name'],
                ];
                $user_arr[] = [
                    'user_id'=> $v['user_id'],
                    'user_name'=> $v['user_name'],
                ];
            }
            $data = [
                'notice_list'=> $notice_list,
                'department_arr'=> $department_arr,
                'user_arr'=> $user_arr,
            ];
            $return_info = ['code' => '1000', 'msg' => '获取公告列表成功', 'data' => $data];
        }
        return response()->json($return_info);
    }

}