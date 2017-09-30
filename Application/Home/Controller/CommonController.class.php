<?php 
namespace Home\Controller;
use Think\Controller;
use Think\Upload;
/*公共控制器*/
class CommonController extends Controller{ 
	/*自动运行的方法*/
	Public function _initialize (){
	//处理自动登录
		if(isset($_COOKIE['auto']) && !isset($_SESSION['uid'])){  //如果用户是没有登录的状态而且有自动登录设置，则执行
			$value = explode('|',enctypetion($_COOKIE['auto'],1));
			$ip = get_client_ip();
			//本次登录ip与上一次登录ip一至时；
			if($ip == $value[1]){
				$account = $value[0];
				$where = array('account' => $account);

				$user = M('user')->where($where)->field(array('id','lock'))->find();

				//当能够检索出用户时并且该用户没有被锁定，则保存其登录id到session中自动登录
				if ($user && !$user['lock']) {
			
					session('uid',$user['id']);
				};
			};

		};		

	//判断是否异地登录
		if(!isset($_SESSION['uid'])){
			redirect(U('Home/Login/index'));
		};
	}

	 /*
     * 头像上传
     */
    Public function uploadFace (){
        if (!IS_POST) {
            E('页面不存在');
        }
        $upload = $this->_upload('Face','180','180');
        $this->ajaxReturn(json_encode($upload),'JSON');
        
    }

	
	// 处理图片上传
	/**
     * 图片上传处理
     * @param [String] $path [保存文件夹名称]
     * @param [String] $thumbWidth [缩略图宽度]
     * @param [String] $thumbHeight [缩略图高度]
     * @return [Array] [图片上传信息]
     */
        
    Private function _upload($path,$thumbWidth = '' , $thumbHeight = '') {
        $obj = new \Think\Upload();// 实例化上传类
        $obj->maxSize = C('UPLOAD_MAX_SIZE') ;// 设置附件上传大小
        $obj->savePath =C('UPLOAD_PATH').$path.'/'; // 设置附件上传目录
        $obj->exts =  C('UPLOAD_EXTS');// 设置附件上传类型
        $obj->saveName = array('uniqid','');//文件名规则
        $obj->replace = true;//存在同名文件覆盖
        $obj->autoSub = true;//使用子目录保存
        $obj->subName  = array('date','Ym');//子目录创建规则，
        $info = $obj->upload();

        if(!$info) {
            return array('status' =>0, 'msg'=> $obj->getError());
        }else{
            if($info){    //生成缩略图
    			
                $image = new \Think\Image();
    
                foreach($info as $file) {
                    $thumb_file = C('UPLOAD_PATH') . $file['savepath'] . $file['savename'];
                    $save_path = C('UPLOAD_PATH') .$file['savepath'] . 'mini_' . $file['savename'];
                    $image->open( $thumb_file )->thumb( $thumbWidth, $thumbHeight,\Think\Image::IMAGE_THUMB_FILLED )->save( $save_path );
                    return array(
                            'status' => 1,
                            'savepath' => $file['savepath'],
                            'savename' => $file['savename'],
                            'pic_path' => $file['savepath'] . $file['savename'],
                            'mini_pic' => $file['savepath'] . 'mini_' .$file['savename']
                    );
                    //@unlink($thumb_file); //上传生成缩略图以后删除源文件
                }
            }else{
                foreach($info as $file) {
                    return array(
                            'status' => 1,
                            'savepath' => $file['savepath'],
                            'savename' => $file['savename'],
                            'pic_path' => $file['savepath'].$file['savename']
                    );
                }
            }
        }
    }

    /*
	*异步创建添加组
	*
    */
	Public function addGroup (){
		if(!IS_AJAX){
			$this->getError('页面不存在');
		};
		$data = array('name' => $_POST['name'], 
			'uid'=>session('uid'));
		if(M('group')->data($data)->add()){
			echo json_encode( array('status' =>1 ,'msg'=>'插入成功'));
		}else{
			echo json_encode( array('status' =>0 ,'msg'=>'插入失败,请重试！'));
		}
	}
 /*
	*异步添加关注
	*
    */
	Public function addFollow (){
		if(!IS_AJAX){
			$this->getError('页面不存在');
		};
		$data = array('follow' => intval($_POST['follow']), 
			'gid'=>intval($_POST['gid']),
			'fans' =>session('uid'),
				);
		if(M('follow')->data($data)->add()){
			$db = M('userinfo');
			$db->where(array('uid'=>$data['follow']))->setInc('fans');
			$db->where(array('fans'=>session('fans')))->setInc('follow');
			echo json_encode( array('status' =>1 ,'msg'=>'关注成功！'));
		}else{
			echo json_encode( array('status' =>0 ,'msg'=>'关注失败,请重试！'));
		}
	}

/*异步上传图片*/

	Public function uploadPic(){
		if(!IS_POST){
			$this->error('页面不存在');
		};
		$upload = $this->_upload('Pic','800,380,120','800,380,120');
		echo json_encode($upload);
	}
}