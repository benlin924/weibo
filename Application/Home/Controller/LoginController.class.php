<?php 
namespace Home\Controller;
use Think\Controller;
/*注册与登录控制器*/

class LoginController extends Controller{ 
	/*登录页面*/
	Public function index(){   //这里需要对应住模板名称，即view文件里面需要有
		$this->display('login');
	}
	/*登陆表单处理*/
	Public function login (){
		if($_POST){
			//提交表单内容
			$account = $_POST['account'];
			$pwd = md5($_POST['pwd']);
			$where = array(
				'account'=>$account,
				);

			$user=M('user')->where($where)->find();

			if(!$user || $user['password']!=$pwd){
				$this->error('用户不存在或者密码错误！');  //$this->error执行后会自动终止程序继续运行，相当于die或者exit；
			};

			if($user['lock']){
				$this->error('用户被锁定');
			};
			//处理下次登录
			if(isset($_POST['auto'])){
				$account = $user['account'];
				$ip = get_client_ip();
				$value = $account.'|'.$ip;
				//加密后的
				$value = enctypetion($value);
				setcookie('auto',$value,C('AUTO_LOGIN_TIME'),'/');
			};
			//登录成功
			session('uid',$user['id']);
			header('Content-Type:text/html;Charset=UTF-8');
			redirect(__APP__,1,'登录成功，正在为您跳转...');
		}else{
			$this->error('页面不存在');
		};
	}
	/*注册页面*/
	Public function register(){
	
		$this->display();
	}

		
	/*获取验证码*/
	Public function verify () {
		$config = array( 'fontSize' => 30, // 验证码字体大小 
						'length' => 4, // 验证码位数 
						'useNoise' => false, // 关闭验证码杂点
						 );
		$Verify = new \Think\Verify($config);
		$Verify->entry();
	}

	/*令牌验证*/
	Public function TokenVerify () {
		if($_POST){  //判断是否为post提交，不让非法get地址的方式提交表单到后台
			if(M('user')->autoCheckToken($_POST)){
				// if($_SESSION['verify']!==md5($_POST['verify'])){
				// 	$this->error('验证码错误');
				// }

				// if($_POST['pwd']!==$_POST['pwded']){
				// 	$this->error('密码不一致');
				// }

				//提取POST数据
				$data = array(
					'account' =>htmlspecialchars($_POST['account']),  //注意，表单里面的键可以不跟数据表的字段一样，但是要提交的$data必须和数据表的字段一样。
					'password'=>md5($_POST['pwd']),
					'registime'=>$_SERVER['REQUEST_TIME'],
					'userinfo'=>array(
						'username'=>htmlspecialchars($_POST['uname']),
						)
					);

				$id = D('UserRelation')->insert($data);
				
				if($id){
					//插入数据成功后把ID写入session
					session('uid',$id); //登录id
					header('Content-Type:text/html;Charset=UTF-8');
					redirect(__APP__,1,'注册成功，正在为您跳转...');
				}else{
					$this->error('注册失败，请重试...');
				}
			}
		}else{
			$this->error('页面不存在');
		}

	}

	/*验证账号是否已经存在*/

	Public function checkAccount () {
		if (!$this->isAjax()) {  //查看是否是异步提交，如果不是异步提交，则不让他进入这个方法里
			halt('页面不存在');
		}
		$account = $this->_post('account');
		$where = array('account' => $account);
		if (M('user')->where($where)->getField('id')) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * 异步验证昵称是否已存在
	 */
	Public function checkUname () {
		if (!$this->isAjax()) {
			halt('页面不存在');  //halt是抛出异常，告诉你发生错误的文件名，第几行，给程序员看的。$this->error是给用户看的，比如登陆时密码不正确就用$this->error.
		}
		$username = $this->_post('uname');
		$where = array('username' => $username);
		if (M('userinfo')->where($where)->getField('id')) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * 异步验证验证码
	 */
	Public function checkVerify () {
		if (!$this->isAjax()) {
			halt('页面不存在');
		}
		$verify = $this->_post('verify');
		if ($_SESSION['verify'] != md5($verify)) {
			echo 'false';
		} else {
			echo 'true';
		}
	}
}
 ?>