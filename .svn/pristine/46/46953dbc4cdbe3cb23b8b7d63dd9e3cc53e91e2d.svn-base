<?php
if(empty($_GET['openid'])){
	echo "请关注内江市总工会公众号";
	exit;
}
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
require_once 'config/config.php';
require_once 'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
$login  =new \Admin\Login();
$mar= $login->hasUser($_GET['openid']);
if(!$mar){
	echo "请关注内江市总工会公众号";
	exit;
}
//查询活动
$data = $login->getActivity();
$ac='';$times=0;
if(count($data)>0){
	 $ac= $login->lock_url($data[0]['activity_id']);
	 $li = $login->lock_url($data[0]['day_limit']);
	 $wxt = $login->wxuserTimes($_GET['openid'],$data[0]['activity_id']);
	 if($wxt['times']>=$wxt['a']){
		 $times = $wxt['times']-$wxt['a'];
	 }
}else{
	$openid = $_GET['openid'];
	echo "<div style='font-size:3rem;'>暂时没有举办相关活动！<br><a href='coupon.php?openid=$openid'>是否查看已获得的购物券？</a></div>";
	//header('Location: coupon.php?openid='.$_GET['openid']);
	exit;
}
 $uli = $login->limitByOpenid($_GET['openid']);
 $uli = $login->lock_url($uli);
$colors=[];
$i =0;
foreach($data as $n){
	if($i%2==0){
		array_push($colors,'#FFFFFF');
	}else{
		array_push($colors,'#5fcbd5');
	}
	$i +=1;
}
$restaraunts = json_encode($data,JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html manifest='./appcache'>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $data[0]["ac"]?></title>
<link rel="stylesheet" href="static/css/css.css" />
<script type="text/javascript" src="static/js/jquery.min.js"></script>
<script type="text/javascript" src="static/phone/alertPopShow.js"></script>

<script type="text/javascript" src="static/js/awardRotate.js"></script>
<style>
	.weui_mask_transparent{
		display:none;
	}
	#loading {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.7);
	z-index: 15000;
	
}

#loading div {
	position: absolute;
	top: 50%;
	left: 50%;
	width: 80px;
	height: 80px;
	margin-top: -15px;
	margin-left: -15px;
}
</style>
<script type="text/javascript">
var prizeData = <?php echo $restaraunts?>;
var mark = false,num=0;
var turnplate={
		restaraunts:[],				//大转盘奖品名称
		colors:[],					//大转盘奖品区块对应背景颜色
		outsideRadius:192,			//大转盘外圆的半径
		textRadius:155,				//大转盘奖品位置距离圆心的距离
		insideRadius:68,			//大转盘内圆的半径
		startAngle:0,				//开始角度
		
		bRotate:false				//false:停止;ture:旋转
};
var noindex=0;
$(document).ready(function(){
	$.ajaxSetup({
  async: false
  });
	for(var i=0;i<prizeData.length;i++){
		turnplate.restaraunts[i] = prizeData[i].name;
		if(prizeData[i].name=='谢谢参与'){
			noindex = i;
		}
	}
	turnplate.colors = <?php echo json_encode($colors,JSON_UNESCAPED_UNICODE)?>;
	//动态添加大转盘的奖品与奖品区域背景颜色
	$('#loading').hide()

	var rotateTimeOut = function (){
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:2160,
			duration:8000,
			callback:function (){
				webToast("网络超时，请检查您的网络设置！","middle",3000);
				
			}
		});
	};

	//旋转转盘 item:奖品位置; txt：提示语;
	var rotateFn = function (item, txt){
		var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
		if(angles<270){
			angles = 270 - angles; 
		}else{
			angles = 360 - angles + 270;
		}
		$('#wheelcanvas').stopRotate();
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:angles+1800,
			duration:8000,
			callback:function (){
				var tip = mark?("恭喜你抽中了"+txt):"很遗憾，你未中奖"
				
			 	popTipShow.alert('通知',tip, ['知道了'],
					function(e){
						//callback 处理按钮事件		  
						var button = $(e.target).attr('class');
						if(button == 'ok'){
						//按下确定按钮执行的操作
							this.hide();
						}	
					}
				)
			}
		});
	};

	$('.pointer').click(function (){
		
    if(<?php echo count($data) ?>==0){
			webToast("奖品已抽完，请下次再来","middle",1500);
     	return;
		}
		//获取随机数(奖品个数范围内)
		var item = rnd();
		turnplate.bRotate =false;
		if(item<0) return;
		rotateFn(item+1, prizeData[item].name);
		
	});
});

function rnd(){
	
	var prize=-1;
	$('#loading').show();
  $.get('index.php/admin/login/checkPrize?openid=<?php echo $_GET['openid']?>&ac=<?php echo $ac ?>&c=<?php echo $li ?>&uli=<?php echo $uli ?>').then(function(rs){
		rs = JSON.parse(rs)
		if(rs.code==200&&rs.data){
     $('#times').text(rs.data.times);
		 if(rs.data){
				prize = rs.data;
				mark =true;
				msg = rs.msg;
		 }else{
			popTipShow.alert('通知',"很遗憾，奖品已领完", ['知道了'],
				function(e){
					//callback 处理按钮事件		  
					var button = $(e.target).attr('class');
					if(button == 'ok'){
						this.hide();
					}	
				}
			)
			mark =false
		 }
		 if(!rs.data.business_id){
			mark =false
		 }
		}else{
			webToast(rs.msg,"middle",3000);
		}
		$('#loading').hide();
	})
	
	turnplate.bRotate =false;
	if(prize<0){
		return prize;
	}
	if(!prize.business_id){
			return noindex;
	}
  for(var i=0;i<prizeData.length;i++){
		if(mark&&prizeData[i].id == prize.id&&prize.business_id){
			//奖品数量等于10,指针落在对应奖品区域的中心角度[252, 216, 180, 144, 108, 72, 36, 360, 324, 288]
			return i;
		}
	
	}
}


//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
window.onload=function(){
	drawRouletteWheel();
};

function drawRouletteWheel() {    
  var canvas = document.getElementById("wheelcanvas");    
  if (canvas.getContext) {
	  //根据奖品个数计算圆周角度
	  var arc = Math.PI / (turnplate.restaraunts.length/2);
	  var ctx = canvas.getContext("2d");
	  //在给定矩形内清空一个矩形
	  ctx.clearRect(0,0,422,422);
	  //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
	  ctx.strokeStyle = "#FFBE04";
	  //font 属性设置或返回画布上文本内容的当前字体属性
	  ctx.font = 'bold 18px Microsoft YaHei';      
	  for(var i = 0; i < turnplate.restaraunts.length; i++) {       
		  var angle = turnplate.startAngle + i * arc;		 
		  ctx.fillStyle = turnplate.colors[i];
		  ctx.beginPath();
		  //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
		  ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);    
		  ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
		  ctx.stroke();  
		  ctx.fill();
		  //锁画布(为了保存之前的画布状态)
		  ctx.save();

		  //改变画布文字颜色
		  var b = i+2;
		  if(b%2){
		  	 ctx.fillStyle = "#FFFFFF";
		  	}else{
		  	 ctx.fillStyle = "#E5302F";
		  	};
		  
		  //----绘制奖品开始----
		 	
		  	  	  
		  var text = turnplate.restaraunts[i];
		  var line_height = 17;
		  //translate方法重新映射画布上的 (0,0) 位置
		  ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);
		  
		  //rotate方法旋转当前的绘图
		  ctx.rotate(angle + arc / 2 + Math.PI / 2);
		  
		  /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
		  if(text.indexOf("盘")>0){//判断字符进行换行
			  var texts = text.split("盘");
			  for(var j = 0; j<texts.length; j++){
				  ctx.font = j == 0?'bold 20px Microsoft YaHei':'bold 18px Microsoft YaHei';
				  if(j == 0){
					  ctx.fillText(texts[j]+"盘", -ctx.measureText(texts[j]+"盘").width / 2, j * line_height);
				  }else{
					  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height*1.2); //调整行间距
				  }
			  }
		  }else if(text.indexOf("盘") == -1 && text.length>8){//奖品名称长度超过一定范围 
			  text = text.substring(0,8)+"||"+text.substring(8);
			  var texts = text.split("||");
			  for(var j = 0; j<texts.length; j++){
				  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
			  }
		  }else{
		  		
			  //在画布上绘制填色的文本。文本的默认颜色是黑色
 
			  //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
			  ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
		  }
		  
		  //添加对应图标
		  
		 
		  
		  
		  //把当前画布返回（调整）到上一个save()状态之前 
		  ctx.restore();
		  //----绘制奖品结束----
	  }     
  } 
};

</script>
</head>
<body style="background:url(static/img/body_bg.jpg);background-size:cover;">
	<div style="margin:10px;">剩余抽奖次数：<span style="color:red;" id="times"><?php echo $times ?></span></div>
	<div class="banner" style="margin-top: 30%">
		<div class="turnplate" style="background-image:url(static/img/cj_bg.png);background-size:100% 100%;">
			<canvas class="item" id="wheelcanvas" width="422px" height="422px"></canvas>
			<img class="pointer" src="static/img/jt2.png"/>
		</div>
		
	</div>
	<div id="loading">
    <div>正在加载中，请稍后...</div>
</div>
	<a href="coupon.php?openid=<?php echo $_GET['openid'] ?>" class="button"><span class="ticket">查看中奖券</span></a>
	<link rel="stylesheet" href="static/phone/common.css" />
</body>
</html>