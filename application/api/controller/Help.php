<?php

namespace app\api\controller;

use app\common\controller\Api;
use think\Exception;
use think\exception\DbException;
class Help extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    /**
     * 帮这中心列表
     */
    public function list(){
        $list = Db("collar_help_center")->where("")->select();
    }
}
?>