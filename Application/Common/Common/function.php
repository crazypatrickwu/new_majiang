<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/28 0028
 * Time: 09:23
 */

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5($str, $key = 'xinmiao2016!QAZ@WSX'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * [getAuthUrl 得到权限地址]
 */
function getAuthUrl($controllerAction) {
    static $authListCache;
    if ( empty($authListCache) ) {
        $authListCache = session('authList');
    }

    if ( is_array($controllerAction) ) {
        foreach ($controllerAction as $value) {
            if ( in_array(strtolower($value), $authListCache) ) {
                $url = str_replace('-', '/', $value);
                return U($url);
            }
        }
    } else {
        if ( in_array(strtolower($controllerAction), $authListCache) ) {
            $url = str_replace('-', '/', $controllerAction);
            return U($url);
        }
        return false;
    }
}

/**
 * [checkActionAuth 检查方法权限]
 */
function checkActionAuth($controllerAction) {

    static $authListCache;
    if ( empty($authListCache) ) {
        $authListCache = session('authList');
    }
    if ( is_array($controllerAction) ) {
        foreach ($controllerAction as $value) {
            if ( in_array(strtolower($value), $authListCache) ) {
                return true;
            }
        }
    } else {
        if ( in_array(strtolower($controllerAction), $authListCache) ) {
            return true;
        }
    }
}

/**
 * [search_time_format 转换搜索中的时间]
 */
function search_time_format($str) {
    $str = urldecode($str);
    return str_replace('+', ' ', $str);
}
/**
 * @params  判断是否wap
 * @return  boolean
 */
function iswap()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
        return true;
    }
    //此条摘自TPM智能切换模板引擎，适合TPM开发
    if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT']){
        return true;
    }
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])){
        //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    }
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        //从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

// 过滤javascript代码防止xss攻击
function remove_xss($val) {
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
        $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val);
    }

    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);
    $found = true;
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(�{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
            $val = preg_replace($pattern, $replacement, $val);
            if ($val_before == $val) {
                $found = false;
            }
        }
    }
    return $val;
}

/**
 * [time_format 标准时间格式化]
 */
function time_format($time) {
    return date('Y/m/d H:i:s', $time);
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array())
{
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}

//接口请求日志入库
function dblog($data)
{
    $data   =   array();
    $data   =   is_array($data) ? json_encode($data)    :   $data;
    M('msg')->add(['msg' => $data , 'date' => date('Y-m-d H:i:s' , NOW_TIME)]);
}

/**
 * 生成随机数
 * @param   length  字符串长度
 * @param   type    字符串类型
 * @return  string
 */
function randomString($length = 6 , $type = 0)
{
    $arr  = array(
        0 => '0123456789',
        1 => 'abcdefghjkmnpqrstuxy',
        2 => 'ABCDEFGHJKMNPQRSTUXY',
        3 => '123456789abcdefghjkmnpqrstuxy',
        4 => '123456789ABCDEFGHJKMNPQRSTUXY',
        5 => 'abcdefghjkmnpqrstuxyABCDEFGHJKMNPQRSTUXY',
        6 => '123456789abcdefghjkmnpqrstuxyABCDEFGHJKMNPQRSTUXY',
        7 => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
    );
    $chars = $arr[$type] ? $arr[$type] : $arr[7];
    $hash  = '';
    $max   = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}