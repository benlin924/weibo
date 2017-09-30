<?php 
namespace Home\Model;
use Think\Model\RelationModel;
/*用户与用户信息表关联模型*/
class UserRelationModel extends RelationModel {
	//定义主表的名称，model名对应不到表名称，所以要重新关联
	Protected $tableName = 'user';

	//定义用户与用户信出表关联关系属性

	Protected $_link  = array(
		'userinfo' => array(
			'mapping_type' =>self::HAS_ONE ,
			'foreign_key' => 'uid' 
			 )
		 );
	/*自动化插入数据*/
	Public function insert($data=NULL){
		$data = is_null($data) ?$_POST : $data;
		return $this->relation(true)->relation(true)->add($data);
	}
}
 
?>
