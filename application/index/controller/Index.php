<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\index\controller;
use think\Cache;
use think\helper\Hash;
use think\Db;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{
    public function index()
    {
    	if($this->request->isPost()){
    		$data = $this->request->post();    		
    		
    		if(!isset($data['sex']) or $data['sex']=='' or $data['sex']==null ){
    			unset($data['sex']);    			
    		}
    		if($data['xl']=='0'){
    			unset($data['xl']);
    		}
    		if($data['town']=='0'){
    			unset($data['town']);
    		}
    		$data['title'] = trim ($data['title']);    		
    		$res = db::table('dp_student')
    			->where($data)    			
    			->order("id desc")
    			->select();
    		foreach($res as $key=>$vol){
    			$xl_name = db::table('dp_education')->where('id',$vol['xl'])->value('title');
    			if($xl_name){
    				$res[$key]['xl_name'] = $xl_name;
    			}else{
    				$res[$key]['xl_name'] = '';
    			}
    		}
    		
    		foreach($res as $key=>$vol){
    			$town_name = db::table('dp_town')->where('id',$vol['town'])->value('title');
    			if($town_name){
    				$res[$key]['town_name'] = $town_name;
    			}else{
    				$res[$key]['town_name'] = '';
    			}
    		}
    		//dump($res);
    		if($res){
    			$con = "1";
    			$this->assign("res",$res);
    		}else{
    			$con = "0";
    		}
    		
    	}else{
    		$con = "2";
    	}
    	
    	$this->assign("con",$con);
    	//学历
    	$edu_list = db::table('dp_education')->order('sort asc')->select();
    	$this->assign("edu_list",$edu_list);    	
    	//乡镇
    	$town_list = db::table('dp_town')->order('sort asc')->select();
    	$this->assign("town_list",$town_list);
    	
    	return $this->fetch();
    }
    
    //详情页
    public function detail()
    {
    	$param = $this->request->param();
    	$info = db::table('dp_student')
    		->alias('a')
    		->join('dp_admin_attachment b','a.zp=b.id','left')
    		->field('a.*,b.path')
    		->where("a.id",$param['id'])
    		->find();
    	$xl_name = db::table('dp_education')->where('id',$info['xl'])->value('title');
    	$town_name = db::table('dp_town')->where('id',$info['town'])->value('title');
    	$info['xl_name'] = $xl_name;
    	$info['town_name'] = $town_name;
    	$this->assign("info",$info);
    	//dump($info);
    	
    	$family = db::table('dp_studentfamily')->where('student_id',$param['id'])->select();
    	$this->assign("family",$family);
    	
    	return $this->fetch();
    }    
    
}
