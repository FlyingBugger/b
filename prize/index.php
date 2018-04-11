<?php
require('weixin.class.php');
$weixin = new class_weixin();
ini_set("session.cookie_httponly", 1);
header("Set-Cookie: hidden=value; httpOnly");
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (!isset($_COOKIE["openId"])||empty($_COOKIE["openId"])){
    if (!isset($_GET["code"])){
        $jumpurl = $weixin->oauth2_authorize($url, "snsapi_userinfo", "125");
        Header("Location: $jumpurl");
        exit;
    }else{
        $access_token_oauth2 = $weixin->oauth2_access_token($_GET["code"]);
        // $userinfo = $weixin->oauth2_get_user_info($access_token_oauth2['access_token'], $access_token_oauth2['openid']);
        setcookie("openId",$access_token_oauth2['openid'], time()+3600*6);
        $openId=$access_token_oauth2['openid'];
    }
}else{
    $openId=$_COOKIE["openId"];
}
 $sin = $weixin->signature($url);
 $sin["url"]=$url;
 $sin["title"]="幸运大抽奖";
 $sin["icon"]="http://weixin.scnjnews.com/brunt/prize/images/timg.jpg";
 $sin["desc"]="幸运大抽奖活动2018";
 $config = json_encode($sin,JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html>

<head>
	<title>抽奖</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta name="format-detection" content="telphone=no, email=no">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
	<link href="css/base.css" rel="stylesheet">
	<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>

<body>
<div class="main2">
	<div class="container">
		<div class="num num2">
			<div class="num-con num-con2">
				<div class="num-img"></div>
				<div class="num-img"></div>
			</div>
		</div>
	</div>
</div>
<div class="main3">
	<div class="container">
		<div class="main3-btn"></div>
	</div>
</div>
<div class="container-fluid">
    <div id="msg" style="margin-bottom: 5px;"></div>
    <h4 style="text-align: center;">中奖名单</h4>
<table id="datatable" class="table table-striped table-bordered" style="width:100%;">
	<thead>
	<tr>
		<th style="width: 20px;"></th>
		<th  >用户名</th>
		<th  >奖品</th>
	</tr>
	</thead>
	<tbody >

	</tbody>
</table>
</div>
<script src="js/jquery-1.11.0.js"></script>
<script type="text/javascript" charset="utf8" src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
    $(document).ready( function () {
        var t = $('#datatable').DataTable({
            "autoWidth": true,
            "searching": false,
            "paging": true,
            "iDisplayLength" : 5,
            "iDisplayStart" : 0,
            "ordering": false,//全局禁用排序
            "ajax": "order.php",
            "columns" : [
                {"data":"id"},
                {"data":"nickname"},
                {"data" : "prize"}
            ],
            "lengthChange": false,
            "language": {
                "lengthMenu": "每页 _MENU_ 条记录",
                "zeroRecords": "没有找到记录",
                "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
                "infoEmpty": "无记录",
                "infoFiltered": "(从 _MAX_ 条记录过滤)",
                "paginate": {
                    "previous": "上一页",
                    "next": "下一页"
                }
            }
        });

        $(".main3-btn").click(function () {
            if(!flag){
                flag=true;
                reset();
                letGo();
                setTimeout(function () {
                    flag=false;
                    if(ind==2){
                        $(".fix,.pop-form").show();
                    }else{
                        $(".fix,.pop").show();
                        //$(".pop-text span").text(""+String(4-TextNum1)+(8-TextNum2))
                    }
                },1000);

                ind++;
            }
        });

        var flag=false;
        var ind=0;
        function letGo(){

            $.getJSON("prize.php",{"openId":"<?php echo $openId ?>"},function(data){
                if(data.code==200){
                    $(".num-con2").animate({"top":-750},1000,"linear", function () {
                        $(this).css("top",0).animate({"top":parseInt(data.msg)},1800,"linear");
						$("#msg").text("")
                    })
                }
               if(data.code==500){
                    $("#msg").text(data.msg)
                }
                t.ajax.reload();
            } )
        }
        function reset(){
            $(".num-con2").css({"top":-274});
        }
    } );
	var config = <?php
    echo $config;
    ?>;

    wx.config({
        debug: false,//开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: config.appId, // 必填，公众号的唯一标识
        timestamp: config.timestamp, // 必填，生成签名的时间戳
        nonceStr: config.nonceStr, //必填， 生成签名的随机串
        signature: config.signature, //必填，签名
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareQZone','onMenuShareWeibo'] //必填， JS接口列表，这里只填写了分享需要的接口
    });
	wx.ready(function () {
        wx_share(config.title,config.url,config.icon,config.desc);
    });

    function wx_share(title, link, imgurl, desc) {
        //朋友圈
        wx.onMenuShareTimeline({
            title: title, // 分享标题
            link: link, // 分享链接
            imgUrl: imgurl, // 分享图标
            success: function() {
                // 用户确认分享后执行的回调函数
            },
            cancel: function() {
                // 用户取消分享后执行的回调函数
            }
        });
        //微信好友
        wx.onMenuShareAppMessage({
            title: title, // 分享标题
            desc: desc, // 分享描述
            link: link, // 分享链接
            imgUrl: imgurl, // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function() {
                // 用户确认分享后执行的回调函数
            },
            cancel: function() {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareWeibo({
            title: title, // 分享标题
            desc: desc, // 分享描述
            link: link, // 分享链接
            imgUrl: imgurl, // 分享图标
            success: function () {
// 用户确认分享后执行的回调函数
            },
            cancel: function () {
// 用户取消分享后执行的回调函数
            }
        });
        //qq
        wx.onMenuShareQQ({
            title: title, // 分享标题
            desc: desc, // 分享描述
            link: link, // 分享链接
            imgUrl: imgurl, // 分享图标
            success: function() {
                // 用户确认分享后执行的回调函数
            },
            cancel: function() {
                // 用户取消分享后执行的回调函数
            }
        });
        // qq空间
        wx.onMenuShareQZone({
            title: title, // 分享标题
            desc: desc, // 分享描述
            link: link, // 分享链接
            imgUrl: imgurl, // 分享图标
            success: function() {
                // 用户确认分享后执行的回调函数
            },
            cancel: function() {
                // 用户取消分享后执行的回调函数
            }
        });
    }
</script>

</body>
</html>
