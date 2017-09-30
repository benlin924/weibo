<?php 
namespace Home\Controller;
use Think\Controller;
/*账号设置*/
Class UserSettingController extends Controller {
	/*用户基本信息设置视图*/
	Public function index(){
		$where = array('uid' => session('uid'));
		$field = array('username','truename','sex','location','constellation','intro');
		$user = M('userinfo')->field($field)->where($where)->find();
		// dump($user);
		// die;
		header('Content-Type:text/html;Charset=utf-8');
		$this->assign('user',$user); //$this->abc=$user 分配变量到模板
		$this->display();
	}

	/*修改用户提交信息*/
	Public function editBasic(){
		if(!$_POST){
			$this->error('页面你不存在!');
		};
		header('Content-Type:text/html;Charset=utf-8');
		$data=array('username'=>$_POST['nickname'],
			'truename'=>$_POST['truename'],
			'sex'=>(int)$_POST['sex'],
			'location'=>$_POST['province'] . '' .$_POST['city'],
			'intro'=>$_POST['intro'],
			'constellation'=>$_POST['constellation'],);
		$where=array('uid'=>session('uid'));
		if(M('userinfo')->where($where)->save($data)){
			$this->success('修改页面成功',U('index'));
		}else{
			$this->error('修改失败');
		}
	}

	/*
     * 修改用户头像
     */
    Public function editFace(){
        if(!IS_POST){
            E('页面不存在');
        }
        $db = M('userinfo');
        $where = array('uid' => session('uid'));
        $field = array('face180');
        $old = $db->where($where)->field($field)->find();
       
        if ($db->where($where)->save($_POST)) {
            if (!empty($old['face180'])) {
                @unlink('./Uploads/'.str_replace('mini_','',$old['face180']));//删除之前的图 
                @unlink('./Uploads/'.$old['face180']);//删除之前的缩略图
            }
            $this->success('修改成功',U('index'));
        }else{
            $this->error('修改失败,请重试。。。');
        }
    }


	/*
     * 修改密码
     */
    Public function editPwd(){
    	if(!$_POST){
    		$this->error('页面不存在');
    	}
    	$where = array('id' => session('uid'));
    	$old = M('user')->where($where)->getField('password');
    	if(md5($_POST['old'])!=$old){
    		$this->error('旧密码错误');
    	}

    	if($_POST['new']!=$_POST['newed']){
    		$this->error('两次密码不一致');
    	}

    	$newPwd = md5($_POST['new']);
    	$data = array('id'=>session('uid'),'password'=>$newPwd);
    	if(M('user')->save($data)){
    		$this->success('修改成功',U('index'));
    	}else{
    		$this->error('修改失败');
    	}
    }
}
 ?>