<?php
/**
 * Created by PhpStorm.
 * User: 融媒中心-1
 * Date: 2018/4/2
 * Time: 14:59
 */
require_once 'config.php';
class class_weixin{
    var $appid = 'wx6f1fa092a4f5e263';
    var $appsecret = '51eb6b33ee16bfa2e213c037f9d4c4f8';
    var $access_token = '';
    //构造函数，获取Access Token
    public function __construct($appid = NULL, $appsecret = NULL)
    {
        if($appid && $appsecret){
            $this->appid = $appid;
            $this->appsecret = $appsecret;
        }
    }

    //生成OAuth2的URL
    public function oauth2_authorize($redirect_url, $scope, $state = NULL)
    {
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$redirect_url."&response_type=code&scope=".$scope."&state=".$state."#wechat_redirect";
        return $url;
    }

    //生成OAuth2的Access Token
    public function oauth2_access_token($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->appsecret."&code=".$code."&grant_type=authorization_code";
        $res = $this->http_request($url);
        return json_decode($res, true);
    }
    public function get_access_token(){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
        $res = $this->http_request($url);
        $this->access_token = $res["access_token"];
        return json_decode($res, true);
    }

    //获取用户基本信息（OAuth2 授权的 Access Token 获取 未关注用户，Access Token为临时获取）
    public function oauth2_get_user_info($access_token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->http_request($url);
        return json_decode($res, true);
    }

    //获取用户基本信息
    public function get_user_info($openid,$access_token=null)
    {
        if (!$access_token){
            $access_token = $this->access_token;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res = $this->http_request($url);
        return json_decode($res, true);
    }
	public function accessToken(){
        $appid = $this->appid;
        $appsecret = $this->appsecret;
        $sql = "SELECT `token`,`token_timeout`,`ticket` FROM tp_access_token WHERE appid='$appid'AND  appsecret='$appsecret'";
        $stmt = $GLOBALS['pdo']->query($sql);
        $row = $stmt->fetch();
        if ($row&&$row["token"]&&$row["token_timeout"]>time()){
            return array("token"=>$row["token"],'ticket'=>$row['ticket']);
        }else{

            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $re = $this->http_request($url);
            $res = json_decode($re, true);
            $access_token = $res['access_token'];
            if($access_token) {
                $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$access_token&type=jsapi";
                $re = $this->http_request($url);
                $res = json_decode($re, true);
                $jsapi_ticket = $res["ticket"];
                $time = time() + 7000;
                $sql = "REPLACE INTO tp_access_token(`appid`,`appsecret`,`token`,`token_timeout`,`ticket`) VALUES ('$appid','$appsecret','$access_token',$time,'$jsapi_ticket')";
                $count = $GLOBALS['pdo']->exec($sql);
                if ($count){
                    return array("token"=>$access_token,"ticket"=>$jsapi_ticket);
                }
            }
        }
        return null;
    }
    
    public function signature($url){

          // 密码字符集，可任意添加你需要的字符
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $nonceStr = "";
        for ( $i = 0; $i < 8; $i++ )
        {
            $nonceStr .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
		$token = $this->accessToken();
        if (empty($token)){
            return $nonceStr;
        }

        $jsapi_ticket = $token["ticket"];
        $timestamp = time()+0;
        $signature = sha1("jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url");
        $rs =  array("appId"=>$this->appid,"timestamp"=>$timestamp,"nonceStr"=>$nonceStr,"signature"=>$signature);
        return $rs;
    }
    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    protected function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);//referer头，请求来源
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}