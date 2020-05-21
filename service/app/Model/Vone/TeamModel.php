<?php

namespace App\Model\Vone;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TeamModel extends Model
{
    protected $table = 'team as t';

    //可操作字段
    protected $field = ['t.id', 't.team_name', 't.channel_id'];

    /**
     * description 获取团队列表
     * author zongxing
     * type GET
     * date 2019.11.22
     * return Array
     */
    public function teamList($param = [])
    {
        $field = $this->field;
        $team_obj = DB::table($this->table);
        if (!empty($param['team_name'])) {
            $team_obj->where('team_name', trim($param['team_name']));
        }
        if (!empty($param['channel_id'])) {
            $team_obj->where('channel_id', intval($param['channel_id']));
        }
        if (!empty($param['id'])) {
            $team_obj->where('id', intval($param['id']));
        }
        $team_list = $team_obj->get($field);
        $team_list = objectToArrayZ($team_list);

        $pc_model = new PurchaseChannelModel();
        $pc_info = $pc_model->getChannelList(null, 0);
        $pc_list = [];
        foreach ($pc_info as $k=>$v){
            $pc_list[$v['id']] = $v['channels_name'];
        }
        foreach ($team_list as $k=>$v){
            $team_list[$k]['channels_name'] = $pc_list[$v['channel_id']];
        }
        return $team_list;
    }

    /**
     * description 新增团队
     * author zongxing
     * date 2019.11.22
     * return boolean
     */
    public function addTeam($param_info)
    {
        $data = [
            'team_name' => trim($param_info['team_name']),
            'channel_id' => intval($param_info['channel_id']),
        ];
        $insert_res = DB::table('team')->insert($data);
        return $insert_res;
    }

    /**
     * description 编辑团队
     * author zongxing
     * type POST
     * date 2019.11.22
     * return boolean
     */
    public function editTeam($param_info)
    {
        $team_id = intval($param_info['id']);
        $data = [
            'team_name' => trim($param_info['team_name']),
            'channel_id' => intval($param_info['channel_id']),
        ];
        $insert_res = DB::table('team')->where('id', $team_id)->update($data);
        return $insert_res;
    }

    /**
     * description 编辑目标
     * author zongxing
     * type POST
     * date 2019.10.28
     * return boolean
     */
    public function editTarget($param_info)
    {
        $target_id = intval($param_info['id']);
        $data = [
            'start_date' => $param_info['start_date'],
            'end_date' => $param_info['end_date'],
            'sale_user_id' => intval($param_info['sale_user_id']),
            'target_name' => trim($param_info['target_name']),
            'target_content' => trim($param_info['target_content']),
            'target_currency' => intval($param_info['target_currency']),
            'target_value' => floatval($param_info['target_value']),
        ];
        $insert_res = DB::table('target')->where('id', $target_id)->update($data);
        return $insert_res;
    }

}
