<?php
/**
 * Generates an UUID
 *
 * @author     Anis uddin Ahmad
 * @param      string  an optional prefix
 * @return     string  the formatted uuid
 */
function uuid($fix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8) . $fix;
    $uuid .= substr($chars,8,4) . $fix;
    $uuid .= substr($chars,12,4) . $fix;
    $uuid .= substr($chars,16,4) . $fix;
    $uuid .= substr($chars,20,12);
    return $uuid;
}
function rs($data='',$msg='请求成功！',$code=200,$token=null){
     $ts= ["data"=>$data,"msg"=>$msg,"code"=>$code];
     if($token){
        $ts['token'] = $token;
     }
     if(isset($_POST['re_jwt'])){
         $ts['token'] = $_POST['re_jwt'];
     }
    return json_encode((object)$ts,JSON_UNESCAPED_UNICODE);
}
function rs_error($msg="",$code=500,$data=''){
    return json_encode((object)["data"=>$data,"msg"=>$msg,"code"=>$code],JSON_UNESCAPED_UNICODE);
}

function table($data=[],$count=0,$msg='',$code=200,$token=null){
    $ts= ["data"=>$data,"count"=>$count,"msg"=>$msg,"code"=>$code];
    if($token){
       $ts['token'] = $token;
    }
    if(isset($_POST['re_jwt'])){
        $ts['token'] = $_POST['re_jwt'];
    }
    return json_encode((object)$ts,JSON_UNESCAPED_UNICODE);
}
function upload_img($userId,$inputname,$size){
    if(isset($_FILES[$inputname])&&$_FILES[$inputname]['size']>0){
        $file_type=$_FILES[$inputname]['type'];
        if($file_type !='image/png'&&$file_type !='image/jpg'&&$file_type !='image/jpeg'){
            echo rs_error('文件类型错误');
            return false;
        }
        if($_FILES[$inputname]['size']>$size) {  
            echo rs_error("文件过大，不能上传大于".($size/1024)."M的文件");  
            return false;
        } 
        if(is_uploaded_file($_FILES[$inputname]['tmp_name'])){
            $uploaded_file=$_FILES[$inputname]['tmp_name']; 
            
            $user_path=$_SERVER['DOCUMENT_ROOT']."/news/".$inputname."/".$userId;
            if(!file_exists($user_path)) {  
                mkdir($user_path,0777,true);  
            }
            $file_true_name=$_FILES[$inputname]['name'];
            $tem = "/".time().substr($file_true_name,strrpos($file_true_name,"."));
            $move_to_file=$user_path.$tem;
            $relative_path = "/news/".$inputname."/".$userId.$tem;
            //$this->load->helper('file');
            
            if(move_uploaded_file($uploaded_file,$move_to_file)) {  
                //echo $_FILES[$inputname]['name']."上传成功";  
                return $relative_path;
            } else {  
                echo rs_error('上传失败');
                return false;
            }
        }else{
            echo rs_error('上传失败');
            return false;
        }
    }
    return 1;
}
/**
 * 获取自定义的header数据
 */
function get_all_headers(){

    // 忽略获取的header数据
    $ignore = array('host','accept','content-length','content-type');

    $headers = array();

    foreach($_SERVER as $key=>$value){
        if(substr($key, 0, 5)==='HTTP_'){
            $key = substr($key, 5);
            $key = str_replace('_', ' ', $key);
            $key = str_replace(' ', '-', $key);
            $key = strtolower($key);

            if(!in_array($key, $ignore)){
                $headers[$key] = $value;
            }
        }
    }

    return $headers;
}
//不同环境下获取真实的IP
function get_ip(){
    //判断服务器是否允许$_SERVER
    if(isset($_SERVER)){    
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }else{
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    }else{
        //不允许就使用getenv获取  
        if(getenv("HTTP_X_FORWARDED_FOR")){
              $realip = getenv( "HTTP_X_FORWARDED_FOR");
        }elseif(getenv("HTTP_CLIENT_IP")) {
              $realip = getenv("HTTP_CLIENT_IP");
        }else{
              $realip = getenv("REMOTE_ADDR");
        }
    }

    return $realip;
}