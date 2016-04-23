<?php
namespace Appraise\Controller;
use Think\Controller;
class IndexController extends Controller
{
    public function index()
    {
		$teacherIds=M("userRole")->where(array('status'=>1,'role_id'=>2))->getField('uid',true);
		$teachers=M("member")->where(array('uid'=>array('in',$teacherIds)))->select();
		//var_dump($teachers);
		$this->assign('teachers',$teachers);
        $this->display();
    }
    //uid 教师的uid
	public function selectLesson($uid=0)
	{
// 		$loginUid=is_login();//取得当前已登录用户的uid
// 		var_dump($loginUid);
// 		exit();
	    //参数检查
	    $uid = intval($uid);//
	    if(!$uid)
	    {
	        $this->error("参数错误！");
	    }
	    //权限检查
	    //判断是否登录
	    $loginUid=is_login();//取得当前已登录用户的uid
	    if(!$loginUid)
	    {
	        $this->error("您尚未登录！",U("/ucenter/member/login"));
	    }
	    //判断是否是学生
	    if(!(M("userRole")->where(array('status'=>1,'role_id'=>3,'uid'=>$loginUid))->find()))
	    {
	        $this->error("只有学生才可以评价！");
	    }
	    //判断该学生是否有修该老师的课程
	    //取得该老师的所有课程ID
	    $teacherLessonIds=M('appraiseTeacherLesson')
	    ->where(array('uid'=>$uid))
	    ->getField('lessonId',true);
	    //取得该学生选修该老师的所有课程的ID
	    $studentLessonIds=M('appraiseStudentLesson')
	    ->where(array('uid'=>$loginUid,'lessonId'=>array('in',$teacherLessonIds)))
	    ->getField('lessonId',true);
	    if(!$studentLessonIds)
	    {
	        $this->error("您未选修该老师的课程，无法评价");
	    }
	    //查询课程可评价时段
	    $Lessons=M('appraiseSesson')
	    ->where(array('id'=>array('in',$studentLessonIds)))
	    ->select();
	     $Lessons=M('appraiseLesson')->SELECT();
	    $this->assign('uid',$uid);
		$this->assign('lessons',$Lessons);
		$this->assign('lessonsa',$Lessonsa);
	    $this->display();
	}
	public function selectSession($uid=0,$lessonId=0)
	{
		//参数检查
	    $uid = intval($uid);//
	    if(!$uid)
	    {
	        $this->error("参数错误！");
	    }
	    //权限检查
	    //判断是否登录
	    $loginUid=is_login();//取得当前已登录用户的uid
	    if(!$loginUid)
	    {
	        $this->error("您尚未登录！",U("/ucenter/member/login"));
	    }
	    //判断是否是学生
	    if(!(M("userRole")->where(array('status'=>1,'role_id'=>3,'uid'=>$loginUid))->find()))
	    {
	        $this->error("只有学生才可以评价！");
	    }
	    if(!(M("appraiseTeacherLesson")->where(array('lessonId'=>$lessonId,'uid'=>$uid))->find()))
	    {
	    	$this->error("参数异常！");
	    }
	    
// 		//判断学生是否选修该课程
// 		 if(!(M("appraiseLession")->where(array('uid'=>$loginUid,'uid'=>$loginUid))->find()))
// 	    {
// 	        $this->error("参数异常！");
// 	    }
// 		 if(!(M("userRole")->where(array(lessonId=>$lessonId))->select()));
// 	    {
// 	        $this->error("参数异常！");
// 	    }
		$sessions=M('appraiseSession')
	    ->where(array('lessonId'=>$lessonId))
	    ->select();
		$this->assign('uid',$uid);
		$this->assign('lessonId',$lessonId);
		$this->assign('sessions',$sessions);
		$this->display();
	}
	public function doAppraise($uid=0,$lessonId=0,$sessionId=0,$point=0,$content='',$anonymous=0)
	{
		//参数检查
		//权限检查
		$loginUid=is_login();//取得当前已登录用户的uid
		if(!$loginUid)
		{
			$this->error("您尚未登录！",U("/ucenter/member/login"));
		}
		$appraise=M('appraise')->create();
		$appraise['studentId']=$loginUid;
		$appraise['teacherId']=$uid;
		$appraise['createTime']=time();
		M('appraise')->add($appraise);
		$this->assign('teacherId',$uid);
		$this->display();
		//$this->success("评价成功",U('index'));
	}
	public function readAppraise($teacherId=0) {
		//查询课程可评价时段
		$Lessons=M('appraiseSesson')
		->where(array('id'=>array('in',$studentLessonIds)))
		->select();
		$Lessons=M('appraiseLesson')->SELECT();
	 	$teacherId = intval($teacherId);//
	    if(!$teacherId)
	    {
	        $this->error("参数错误！");
	    }
	    $teacherIdname=array($teacherId);
	 	//获取教师id nickname
		$teacherIds=M("userRole")->where(array('status'=>1,'role_id'=>2))->getField('uid',true);
		$teachers=M("member")->where(array('uid'=>array('in',$teacherIds)))->select();
		//获取课程名称 title
		$Lessons=M('appraiseSesson')
		->where(array('id'=>array('in',$studentLessonIds)))
		->select();
		$Lessons=M('appraiseLesson')->SELECT();
		//获取评分和详细评价
		$appraise=M("appraise")->select();
		//var_dump($Lessons);
		$key=M("menber");
		foreach($appraise as $x=>$x_value) {
			foreach($x_value as $y=>$y_value) {
				if ($y=="teacherId") {
					$condition[]=$y_value;
				}
				if ($y=="title") {
					$title[]=$y_value;
				}
	   			if ($y=="point") {
	   				 $point[] = $y_value;
	   			}
	   			if ($y=="content") {
	   				$content[] = $y_value;
	   			}
	   			if ($y=="anonymous") {
	   				$anonymous[] = $y_value;
	   			}
			}
		}
		$teachername=M("member")->where(array('uid'=>array('in',$teacherIdname)))->select();
		foreach($teachername as $k=>$k_value) {
			foreach($k_value as $l=>$l_value) {
				if ($l=="nickname") {
					$teachernames[]=$l_value;
				}
			}
		}
		$huizong=array(0=>$point,1=>$content,2=>$anonymous,3=>$teachernames);
		$huizongxiang['point']=$point[0];
		$huizongxiang['content']=$content[0];
		$huizongxiang['anonymous']=$anonymous[0];
		$huizongxiang['teachernames']=$teachernames[0];
		$huizongxiang['title']=$Lessons[0]['title'];
		$huizongle=array(0=>$huizongxiang);
		$this->assign('huizongle',$huizongle);
		$this->assign('lessons',$Lessons);
		$this->assign('teachers',$teachers);
		$this->display();
	}
}



