<?php 
if(empty($_GET['openid'])){
	echo "请关注内江市总工会公众号";
	exit;
}

const QR =" http://qr.liantu.com/api.php?text=";
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
require_once 'config/config.php';
require_once 'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
$login  =new \Admin\Login();
$record = $login->getPrizeByUserId();
?>
<!DOCTYPE html>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name=viewport content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
		<title>兑奖券</title>
		<link rel="stylesheet" href="static/css/css.css" />
		<link rel="stylesheet" href="static/css/cpstyle.css" />
		<link rel="stylesheet" href="static/css/magnific-popup.css" />
	
	</head>

	<body>
		<div class="tab-list">
			<ul class="tabs-container flex-hb-vc">
				<li class="tabs-items flex-hc-vc flex1 active">可用兑奖券</li>
				<li class="tabs-items flex-hc-vc flex1">已使用/已过期兑奖券</li>
			</ul>
			<ul class="main-container">
				<li class="main-items">
					<div class="coupon-items">
						<?php 
							$a = '';$i=0;
							$b='';
						  foreach($record as $r){
								if($i%2==0) $c = 'yf';
								if($i%2==1) $c = 'd';
								$i++;
								$c = '<div class="coupon-item coupon-item-'.$c.'">
										<div class="coupon-list"><div class="c-type"><div class="c-class">
										<strong>'.$r['prize'].'</strong></div><div class="c-price">
										<a href="'.QR.$r['id'].'" class="without-caption image-link mcover">
										<img src="'.QR.$r['id'].'" class="boximg"/></a></div>
										<div class="c-time"><span>兑奖失效时间：</span>'.$r['end_time'].'<br>兑换商家：'.$r['name'].'</div>
										</div></div></div>';
								if($r['status']==0){
									$a .=$c;
								}else{
									$b .= $c;
								}
								
							}
							echo $a;
						?>
					
					</div>
				</li>
				<li class="main-items hide">
					 <?php echo $b ?>
				</li>
			</ul>
		</div>
		<script type="text/javascript" src="static/js/jquery.min.js"></script>
		<script type="text/javascript" src="static/js/jquery.magnific-popup.min.js"></script>
		<script>
			! function() {
				var pageWid = 720;

				function a() {
					document.documentElement.style.fontSize = document.documentElement.clientWidth / pageWid * 10 / 16 * 1000 + "%"
				}
				var b = null;
				window.addEventListener("resize", function() {
					clearTimeout(b);
					b = setTimeout(a, 300)
				}, !1);
				a()
			}(window);
			$(".tabs-container li").click(function() {
				var index = $(this).index();
				$(this).siblings().removeClass("active");
				$(this).addClass("active");
				$(".main-container li").eq(index).addClass("show");
				$(".main-container li").eq(index).siblings().removeClass("show").addClass("hide");
			})

			var Tabs = function(ct) {
				this.ct = ct;
				this.init();
				this.bind();
			}
			Tabs.prototype.init = function() {
				this.tabList = this.ct.querySelectorAll('.tabs-container>li');
				this.mainList = this.ct.querySelectorAll(".main-container>li");
				// console.log(this.mainList)
			}
			Tabs.prototype.bind = function() {
				var _this = this;
				this.tabList.forEach(function(tabli) {
					tabli.onclick = function(e) {
						var target = e.target;
						console.log(_this.tabList)
						var index = [].indexOf.call(_this.tabList, target);
						console.log(index)
						_this.tabList.forEach(function(li) {
							li.classList.remove('active');
						})
						target.classList.add('active');

						_this.mainList.forEach(function(panel) {
							panel.classList.add('hide')
							panel.classList.remove('show')
						})
						_this.mainList[index].classList.add('show')
					}
				})
			}
			new Tabs(document.querySelectorAll('.tab-list')[0]);
			$('.without-caption').magnificPopup({
				type: 'image',
				closeOnContentClick: true,
				closeBtnInside: false,
				mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
				image: {
					verticalFit: true
				},
				zoom: {
					enabled: true,
					duration: 300 // don't foget to change the duration also in CSS
				}
			});

			$('.with-caption').magnificPopup({
				type: 'image',
				closeOnContentClick: true,
				closeBtnInside: false,
				mainClass: 'mfp-with-zoom mfp-img-mobile',
				image: {
					verticalFit: true,
					titleSrc: function(item) {
						return item.el.attr('title') + ' &middot; <a class="image-source-link" href="' + item.el.attr('data-source') + '" target="_blank">image source</a>';
					}
				},
				zoom: {
					enabled: true
				}
			});
		</script>
	</body>

</html>