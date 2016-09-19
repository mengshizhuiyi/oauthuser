<?php
namespace Oauthuser;
use Oauth\Oauth;

class Oauthuser{
    
	//设定某个系统的用户
	public $system = 'user';
	private $cache_oauth_user_key = 'oauthuser_expires_user_';
	private $cache_second = 18000;
	private $delimiter = '~~';
	
	/**
	 * 验证当前用户是否已经登陆
	 * @return mixed
	 */
	public function check($open_uid = '')
	{
		$open_uid = $open_uid == '' ? $_GET['access_token'] : $open_uid;
		$res = (new Oauth())->checkAccessToken($open_uid);
		if($res === false)
		{
			return false;
		}
		$arr = explode($this->delimiter, $res);
		if($arr[0] == $this->system)
		{
		    return true;
		}
		return false;
	}
	
	/**
	 * 登陆用户并且缓存用户的信息
	 * @return string
	 */
	public function login($open_uid, $data)
	{
		$appid = $this->system . $this->delimiter . $open_uid;
	    $access_token = (new Oauth($appid))->createAccessToken($this->cache_second);
	    //缓存数据
	    $key = $this->cache_oauth_user_key . $this->system . $open_uid;
	    $data = json_encode($data);
	    $this->setCache($key, $data, $this->cache_second);
	    return $access_token;
	}
	
	/**
	 * 获取该系统的用户属性信息
	 * @return mixed
	 */
	public function get($key = '', $default = '')
	{
		//解密access_token 
		$access_token = $_GET['access_token'];
		if(!$access_token)
		{
		    return false;
		}
		$open_uid = (new Oauth())->checkAccessToken($access_token);
		$cache_key = $this->cache_oauth_user_key . $this->system .  $open_uid;
		$data = $this->getCache($cache_key);
		
		$data = json_decode($data, true);
		if($key == '')
		{
		    return $data;
		}
		if(isset($data[$key]))
		{
		    return $data[$key];
		}
		return $default;
	}
	
	/**
	 * 获取缓存数据
	 * @return mixed
	 */
	private function getCache($key)
	{
		return ;
	}
	
	/**
	 * 存储缓存数据
	 * @return mixed
	 */
	private function setCache($key, $value, $second)
	{
		return ;
	}
	
	/**
	 * 静态魔术方法
	 * 变相设置用户系统
	 * @return object
	 */
	static public function __callstatic($method, $arg)
	{
		$O_user = new Oauthuser();
	 	$O_user->system = $method;
	 	return $O_user;
	}
}