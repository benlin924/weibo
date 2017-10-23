<?php 
namespace Home\Controller;
use Home\Model\WeiboViewModel;
/*首页控制器*/
class IndexController extends CommonController{
	/*首页视图*/
	Public function index (){

		//实例化微博试图模型
		$db = D('WeiboView');

		//取得当前用户ID与当前有关注的好友id
		$uid = array(session('uid'));
		$result = M('follow')->where(array('fans'=>session('uid')))->field('follow')->select();
		if($result){
			foreach ($result as $v) {
				$uid[] = $v['follow'];
			}
		}
		
		//组合WHERE	条件，条件为当前用户ID与当前用户所关注好友的ID
		$where = array('uid'=>array('IN',$uid));
		$res = $db->getAll($where);
		$this->assign('res',$res);
		$this->display();
	}
	/*退出登录处理*/
	Public function loginOut(){
		//卸载SESSION
		session_unset();
		session_destroy();
		//删除用于自动登录的cookie
		setcookie('auto','',time()-3600,'/');

		//跳转到登录页
		redirect('Login/index');
	}

	Public function sendWeibo(){
		if(!IS_POST){
			$this->error('页面不存在');
		};

		header("Content-type:text/html;charset=utf-8");
		$data = array(
			'content' => htmlspecialchars($_POST['content']),  //防止注入在表单里面写入js和php那些东西，写了之后会把它变成实体化
			'time' => time(),
			'uid' => session('uid'),
		);
		if($wid = M('weibo')->data($data)->add()){
			if(!empty($_POST['medium']) || !empty($_POST['mini'])){
				$img = array(
					'mini' => $_POST['mini'],
					'medium' => $_POST['medium'],
					'max' => $_POST['max'],
					'wid' => $wid,
				);
				M('picture')->data($img)->add();
			};
			M('userinfo')->where(array('uid'=>session('uid')))->setInc('weibo');
			$this->success('发布成功',U('index'));
		}else{
			$this->error('发布失败请重试..');
		}
	}


	/*转发功能*/
	Public function turn(){
		if(!$_POST){
			$this->error('页面不存在');
		};
		$id = intval($_POST['id']);
		$data = array('content' =>$_POST['content'],
					'isturn'=>$id,
					'time' => time(),
					'uid' => session('uid'),
					
		);
		$db = M('weibo');
		if(M('weibo')->data($data)->add()){
			$db->where(array('id'=>$id))->setInc('turn');
			M('userinfo')->where(array('uid'=>session('uid')))->setInc('weibo');
			$this->success('转发成功',U('index'));
		}else{
			$this->error('转发失败请重试...');

		}
	}
}

?>