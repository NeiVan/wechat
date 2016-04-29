<?php
class Vote_analysisAction extends UsersAction{
	public $token;
	private $data;
	private $rmid;
	//构造函数
    public function _initialize(){
    	//调用父级别静态方法(构造函数)
        parent :: _initialize();
		//调用(服务器存在的令牌标识) 
        $this -> token = session('token'); 
    }
	public function index(){
		//如果接收到时间	
		if($_REQUEST['BeginDate'] && $_REQUEST['EndDate']){
			//如果起始日期大于结束日期
			if($_REQUEST['BeginDate']>$_REQUEST['EndDate']){
				$this->error('开始日期大于结束日期');
			}else{            //属性设置，存储东西
							  $this->assign('pp',$_REQUEST);
	                          $BeginDate=$_REQUEST['BeginDate'];
	                          $EndDate = $_REQUEST['EndDate'];
			}	 	                   
	     }else{	   
	               $BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
	               $EndDate = date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
	               $Date['BeginDate'] = $BeginDate;
	               $Date['EndDate'] = $EndDate;
	               $this->assign('pp',$Date);                 		
	    }
		//连接数据库
		$db=M('vote_option');
		//查询条件
		$where['state']=array('eq',1);
		//获取资源
		$selected=$db->field('id,vote_event_id,voted_option')->where($where)->select();
		//分配给View视图
		$this->assign('selected',$selected);
		if(!empty($_POST)){
			$select=$_POST['select'];
			//连接数据库
			$db0=M('vote_info');
			$db=M('vote_option');
			//查询条件
			$where['id']=array('eq',$select);
			//获取资源
			$option=$db->field('id,vote_event_id,voted_option')->where($where)->select();
			if($option[0]['id']=='1'){
				//条件重置
		        $wheres['state&voted_option']=array('1',array('neq','-'),'_multi'=>true);
				$wherea['voted_option_id&voted_time']=array('2',array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				$whereb['voted_option_id&voted_time']=array('3',array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				$wherec['voted_option_id&voted_time']=array('4',array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				//资源重置
				$option=$db->field('id,vote_event_id,voted_option,state')
				           ->where($wheres)
				           ->select();
				//获取资源
				$num1=$db0->join("tp_vote_option ON tp_vote_info.voted_option_id=tp_vote_option.id")
				         ->field("tp_vote_info.voted_option_id,voted_option")
					     ->where($wherea)
					     ->count();
			    $num2=$db0->join("tp_vote_option ON tp_vote_info.voted_option_id=tp_vote_option.id")
				         ->field("tp_vote_info.voted_option_id,voted_option")
					     ->where($whereb)
					     ->count();
			    $num3=$db0->join("tp_vote_option ON tp_vote_info.voted_option_id=tp_vote_option.id")
				         ->field("tp_vote_info.voted_option_id,voted_option")
					     ->where($wherec)
					     ->count();
				$option[0]['number']=$num1;		 
				$option[1]['number']=$num2;
				$option[2]['number']=$num3;
				$option[0]['BeginDate']=$BeginDate;
				$option[0]['EndDate']=$EndDate;
				$option[1]['BeginDate']=$BeginDate;
				$option[1]['EndDate']=$EndDate;
				$option[2]['BeginDate']=$BeginDate;
				$option[2]['EndDate']=$EndDate;          
				//分配给View视图
			    $this->assign('option',$option);
			}else{
				//条件重置
				$wheres['voted_option_id&voted_time']=array($select,array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				//在option里面添加字段
				$option[0]['number']=$db0->field('voted_option_id')->where($wheres)->count();
				$option[0]['BeginDate']=$BeginDate;
				$option[0]['EndDate']=$EndDate;
				//分配给View视图
			    $this->assign('option',$option);
			}
			//渲染View视图
			$this->display();
		}else{
			$this->display();
		}
		
	}
    public function select(){
    	//如果接收到时间	
		if($_REQUEST['BeginDate'] && $_REQUEST['EndDate']){
			//如果起始日期大于结束日期
			if($_REQUEST['BeginDate']>$_REQUEST['EndDate']){
				$this->error('开始日期大于结束日期');
			}else{            //属性设置，存储东西
							  $this->assign('pp',$_REQUEST);
	                          $BeginDate=$_REQUEST['BeginDate'];
	                          $EndDate = $_REQUEST['EndDate'];
			}	 	                   
	     }else{	   
	               $BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
	               $EndDate = date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));
	               $Date['BeginDate'] = $BeginDate;
	               $Date['EndDate'] = $EndDate;
	               $this->assign('pp',$Date);                 		
	    }
		//如果接收到GET方式的传值
		if(!empty($_GET)){
			$id=$_GET['id'];
			//连接数据库
			$db0=M('vote_info');
			$db=M('vote_option');
			//查询条件
			$where['state']=array('eq',1);
			//获取资源
			$selected=$db->field('id,vote_event_id,voted_option')->where($where)->select();
			//分配给View视图
			$this->assign('selected',$selected);
			//查询条件
			$whereb['state&id']=array('1',array('eq',$id),'_multi'=>true);
			$wheres['voted_option_id&voted_time']=array($id,array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
			//获取资源
			$option=$db->field('id,vote_event_id,voted_option')->where($whereb)->select();
			$option[0]['number']=$db0->field('voted_option_id')->where($wheres)->count();
			$option[0]['BeginDate']=$BeginDate;
			$option[0]['EndDate']=$EndDate;
			//分配给View视图
			$this->assign('option',$option);
			//连接数据库
			$db=M('vote_option');
			$db1=M('vote_info');
			//写查询语句		
			$wheres['voted_option_id']=array('eq',$id);
		    //$wheres['create_date'] =array('between',array($BeginDate,$EndDate));
		    //总记录数
			$count= $db1->where($wheres)->count();
			//页数
			$Page= new Page($count,10);
			$show= $Page->show();
			//获取资源
		    $list=$db1->limit($Page->firstRow.','.$Page->listRows)->where($wheres)->order('id asc')->select();
			//分配给View视图
			$this->assign("list",$list);
			$this->assign('page',$show);
			//渲染View视图
		    $this->display();
		}else{//POST方式传值
		    $select=$_POST['select'];
			//连接数据库
			$db0=M('vote_info');
			$db=M('vote_option');
			//查询条件
			$where['state']=array('eq',1);
			$wherey['voted_option']=array('eq',$select);
			//获取资源
			$selected=$db->field('id,vote_event_id,voted_option')->where($where)->select();
			$arr=$db->field('id')->where($wherey)->find();
			//分配给View视图
			$this->assign('selected',$selected);
			//查询条件
			$where['voted_option']=array('eq',$select);
			//获取资源
			$option=$db->field('id,vote_event_id,voted_option')->where($where)->select();
			if($option[0]['id']=='1'){
				//条件重置
		        $wheres['state&voted_option']=array('1',array('neq','-'),'_multi'=>true);
				$wherea['voted_option_id&voted_time']=array('2',array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				$whereb['voted_option_id&voted_time']=array('3',array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				$wherec['voted_option_id&voted_time']=array('4',array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				//资源重置
				$option=$db->field('id,vote_event_id,voted_option,state')
				           ->where($wheres)
				           ->select();
				//获取资源
				$num1=$db0->join("tp_vote_option ON tp_vote_info.voted_option_id=tp_vote_option.id")
				         ->field("tp_vote_info.voted_option_id,voted_option")
					     ->where($wherea)
					     ->count();
			    $num2=$db0->join("tp_vote_option ON tp_vote_info.voted_option_id=tp_vote_option.id")
				         ->field("tp_vote_info.voted_option_id,voted_option")
					     ->where($whereb)
					     ->count();
			    $num3=$db0->join("tp_vote_option ON tp_vote_info.voted_option_id=tp_vote_option.id")
				         ->field("tp_vote_info.voted_option_id,voted_option")
					     ->where($wherec)
					     ->count();
				$option[0]['number']=$num1;		 
				$option[1]['number']=$num2;
				$option[2]['number']=$num3;
				$option[0]['BeginDate']=$BeginDate;
				$option[0]['EndDate']=$EndDate;
				$option[1]['BeginDate']=$BeginDate;
				$option[1]['EndDate']=$EndDate;
				$option[2]['BeginDate']=$BeginDate;
				$option[2]['EndDate']=$EndDate;   
				//分配给View视图
			    $this->assign('option',$option);
				//连接数据库
				$db=M('vote_option');
				$db1=M('vote_info');
				//写查询语句
				$wheret['select']=$select;			
	            $wheret['voted_time'] =array('between',array(strtotime($BeginDate),strtotime($EndDate)));
			    //总记录数
				$count=$db1->field('voted_time')->where($wheret)->group('voted_time')->order('voted_time desc')->select();
				$count = count($count);
				//页数
				$Page=new Page($count,10);
				//分页跳转的时候保证查询条件
				$Page->parameter['id']   = $arr['id'];
				$show= $Page->show();
				//获取资源
				$list=$db1->where($wheret)->limit($Page->firstRow.','.$Page->listRows)->order('id asc')->select();
				//分配给View视图
//				$this->assign('list',$list);
//				$this->assign('page',$show);
			}else{
				//条件重置
				$wheres['voted_option_id&voted_time']=array($arr['id'],array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
				//在option里面添加字段
				$option[0]['number']=$db0->field('voted_option_id')->where($wheres)->count();
				$option[0]['BeginDate']=$BeginDate;
				$option[0]['EndDate']=$EndDate;
				//分配给View视图
			    $this->assign('option',$option);
			    //连接数据库
				$db=M('vote_option');
				$db1=M('vote_info');
				//写查询语句
				$wheres['select']=$select;		
				$wheres['voted_option_id&voted_time']=array($arr['id'],array('between',array(strtotime($BeginDate),strtotime($EndDate))),'_multi'=>true);
			    //总记录数
				$count=$db1->where($wheres)->order('voted_time desc')->select();
				$count = count($count);
				//页数
				$Page=new Page($count,10);
				//分页跳转的时候保证查询条件
				//写查询语句
				$Page->parameter['id']= $arr['id'];
				$Page->parameter['BeginDate']= $BeginDate;
				$Page->parameter['EndDate']= $EndDate;
				$show= $Page->show();
				//获取资源
				$list=$db1->where($wheres)->limit($Page->firstRow.','.$Page->listRows)->order('id asc')->select();
				//分配给View视图
				$this->assign('list',$list);
				$this->assign('page',$show);
			}
			//渲染View视图
			$this->display();
		}
		
    }
}
