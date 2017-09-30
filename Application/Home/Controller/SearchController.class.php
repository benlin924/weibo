<?php 
namespace Home\Controller;
use Think\Controller;
/*
搜索人
*/

Class SearchController extends Controller{
	// 找人
	Public function sechUser(){
		$keyword = $this->_getKeyword();
		if($keyword){
			$where = array(   //查询条件
				'username'=>array('LIKE','%'. $keyword .'%'),
				'uid'=>array('NEQ',session('uid')),
			);
			$field = array('username','sex','location','intro','face180','follow','fans','weibo','uid');
			$db = M('userinfo');

			$count = $db->where($where)->count('id');   // 查询满足要求的总记录数
			$Page = new \Think\Page($count,5);    // 实例化分页类 传入总记录数和每页显示的记录数
			$limit = $Page->firstRow.','.$Page->listRows;
			$res = $db->where($where)->field($field)->limit($limit)->select();
		
			//重新组合结果集，得到是否已关注

			$res = $this->_getMutual($res);
			$this->assign('count',$count);
			$this->assign('res',$res ? $res : false);
			$this->assign('page',$Page->show());
		}
		$this->assign('keyword',$keyword);
		$this->display();
	}

	// 返回搜索关键字
	Private function _getKeyword(){
		return $_GET['keyword'] == '搜索微博、找人'? NULL: $_GET['keyword'];
	}

	//重组结果集是否关注
	Private function _getMutual($res){
		if(!$res) return false;  //如果没有结果，则返回
		$db = M('follow');
		foreach ($res as $k => $v) {
			//是否互相关注  要注意连接点不能太过于接近
			//union组合两条查询结果
			$sql = '(SELECT `follow` FROM `hd_follow` WHERE `follow` = ' . $v['uid'] . ' AND `fans` = ' . session('uid') . ') UNION (SELECT `follow` FROM `hd_follow` WHERE `follow` = ' . session('uid') . ' AND `fans` = ' . $v['uid'] . ')';
			$mutual = $db->query($sql);

			if(count($mutual) == 2){
				$res[$k]['mutual'] = 1;
				$res[$k]['followed'] = 1;
			}else if(count($mutual) == 1 && $mutual['follow'] == session('uid')){
				$res[$k]['followed'] = 1;
			}else{
				$res[$k]['mutual'] = 0;

				//未互相关注检索是否已经关注
				$where =array(
					'follow'  => $v['uid'],
					'fans' => session('uid')
					);
				$res[$k]['followed'] = $db->where($where)->count();
			}
		}

		return $res;

	}
} 





 ?>