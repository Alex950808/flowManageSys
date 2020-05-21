<?php
namespace App\Api\Vone\Controllers\Sell;

use App\Api\Vone\Controllers\BaseController;
use Dingo\Api\Http\Request;

use App\Model\Vone\GoodsSpecModel;

//create by zhangdong on the 2018.06.22
class ToolController extends BaseController
{
    /**
     * desc 脚本工具-将MIS所需要的图片从全球哒哒中分离出来，并保存到新的文件夹中-专用
     * 执行步骤：
     * 1 通过全球哒哒的商品码将对应商品图片更新到MIS的spec_img中
     * 2 下载全球哒哒的商品图片到对应文件夹中
     * 3 执行该脚本从下载的图片中找到存在的图片复制到指定文件夹中并以MIS中spec_sn命名
     * 4 将复制完成的图片上传到MIS中
     * 5 修改MIS的spec_img图片路径
     * author zhangdong
     * date 2020.03.20
     */
    public function separateImg()
    {
        $gsModel = new GoodsSpecModel();
        $goodsImg = $gsModel->getGoodsImg();
        //图片路径-绝对路径
        $i = 0;
        $arrSpecSn = [];
        foreach ($goodsImg as $key => $value) {
            $absolutePath = 'C:/Users/49599/Desktop/download/';
            $specImg = $absolutePath . trim($value->spec_img);
            $imgIsExist = file_exists($specImg);
            //如果图片存在则移动到另一个文件夹，并且以规格码重命名
            $specSn = trim($value->spec_sn);
            if ($imgIsExist) {
                $save_path = './image/goodsImg/';
                if (!file_exists($save_path)) {
                    mkdir($save_path, 0777, true);
                }
                $filename = $specSn . '.jpg';
                $copyRes = copyFile($specImg, $save_path,$filename);
                if ($copyRes) {
                    $i++;
                    $arrSpecSn[] = $specSn;
                }
            }
        }//end of foreach
        //如果图片复制成功则将对应SKU做标记
        $gsModel->updateImgStatus($arrSpecSn);
        $returnMsg = ['msg' => '总共复制图片个数' . $i];
        return response() ->json($returnMsg);
    }//end of function


    /**
     * desc 脚本工具-将MIS所需要的图片从全球哒哒服务器中分离出来，并保存到新的文件夹中-专用
     * 执行步骤：
     * 1 通过全球哒哒的商品码将对应商品图片更新到MIS的spec_img中
     * 2 执行该脚本从下载的图片中找到存在的图片复制到指定文件夹中并以MIS中spec_sn命名
     * 3 将复制完成的图片上传到MIS中（手动完成）
     * 4 修改MIS的spec_img图片路径（手动完成）
     * author zhangdong
     * date 2020.04.15
     */
    public function separateRemoteImg()
    {
        $gsModel = new GoodsSpecModel();
        $goodsImg = $gsModel->getGoodsImg();
        //图片路径-绝对路径
        $i = 0;
        $arrSpecSn = [];
        foreach ($goodsImg as $key => $value) {
            $absolutePath = 'http://120.77.33.82/';
            $specImg = $absolutePath . trim($value->spec_img);
            //检查图片是否存在
            if(@fopen($specImg, 'r') === false){
                continue;
            }
           //如果图片存在则移动到另一个文件夹，并且以规格码重命名
            $specSn = trim($value->spec_sn);
            $save_path = './image/goodsImg/';
            $filename = $specSn . '.jpg';
            $savedRes = saveImage($specImg, $save_path, $filename);
            if ($savedRes === false) {
                continue;
            }
            $i++;
            $arrSpecSn[] = $specSn;
        }//end of foreach
        //如果图片复制成功则将对应SKU做标记
        $gsModel->updateImgStatus($arrSpecSn);
        $returnMsg = ['msg' => '总共复制图片个数' . $i];
        return response() ->json($returnMsg);
    }//end of function

    /**
     * desc 脚本工具-该方法可根据实际场景随意修改
     * author zhangdong
     * date 2020.04.24
     */
    public function imgTool()
    {
        $gsModel = new GoodsSpecModel();
        $goodsImg = $gsModel->getImgToolInfo();
        //图片路径-绝对路径
        $i = 0;
        foreach ($goodsImg as $key => $value) {
            $specImg = trim($value->goods_picture);
            $arrImg = explode(';', $specImg);
            //检查图片是否存在
            $id = trim($value->id);
            foreach ($arrImg as $k => $v) {
                $img = $v;
                if(@fopen($img, 'r') === false){
                    $gsModel->updateImgTool($id);
                    continue;
                }
                //如果图片存在则移动到另一个文件夹，并且以规格码重命名
                $erpNo = trim($value->erpNo);
                $save_path = './image/ltImg/';
                $filename = $erpNo . '_' . $k . '.jpg';
                $savedRes = saveImage($img, $save_path, $filename);
                if ($savedRes === false) {
                    continue;
                }
            }
            $i++;
        }//end of foreach
        $returnMsg = ['msg' => '总共复制图片个数' . $i];
        return response() ->json($returnMsg);
    }//end of function



}//end of controller