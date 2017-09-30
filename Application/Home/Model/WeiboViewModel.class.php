<?php 
namespace Home\Model;
use Think\Model\ViewModel;

/*读取微博视图模型*/

class WeiboViewModel extends ViewModel{
	
	//定义视图表关联关系
	Protected $viewFields = array(
		'Weibo' => array(
			'id','content','isturn','time','keep','comment','uid',
			'_type' => 'LEFT'
			),
		'userinfo' => array(
			'username','face50'=>'face',
			'_on' => 'weibo.uid = userinfo.uid',
			'_type' => 'LEFT'
			),
		'picture' =>array(
			'mini','medium','max',
			'_on' => 'weibo.id = picture.wid'			
			)
	);

	Public function getAll($where){
		
		return $this->where($where)->select();
	}


}
 
?>