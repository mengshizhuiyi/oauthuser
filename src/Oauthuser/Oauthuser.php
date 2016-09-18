<?php
namespace Oauthuser;
use Oauth\Oauth;

class Oauthuser{
    
	//设定某个系统的用户
	public $system = 'user';
	private $cache_oauth_user_key = 'oauthuser_expires_user_';
	private $cache_second = 18000;
	
	/**
	 * 验证当前用户是否已经登陆
	 */
	public function check($open_uid)
	{
		return (new Oauth())->checkAccessToken($open_uid);
	}
	
	/**
	 * 登陆用户并且缓存用户的信息
	 */
	public function login($open_uid, $data)
	{
	    $access_token = (new Oauth($open_uid))->createAccessToken($this->cache_second);
	    //缓存数据
	    $key = $this->cache_oauth_user_key . $open_uid;
	    $data = json_encode($data);
	    $this->setCache($key, $data, $this->cache_second);
	    return $access_token;
	}
	
	/**
	 * 获取该系统的用户属性信息
	 */
	public function get($key, $default = '')
	{
		//解密access_token 
		$access_token = $_GET['access_token'];
		if(!$access_token)
		{
		    return false;
		}
		$open_uid = (new Oauth())->checkAccessToken($access_token);
		
		$key = $this->cache_oauth_user_key . $open_uid;
		$data = $this->getCache($key);
		$data = json_decode($data, true);
		if(isset($data[$key]))
		{
		    return $data[$key];
		}
		return '';
	}
	
	/**
	 * 获取缓存数据
	 */
	private function getCache($key)
	{
		//return \RedisCache::get($key);
	}
	
	/**
	 * 存储缓存数据
	 */
	private function setCache($key, $value, $second)
	{
		//return \RedisCache::put($key, $value, $second);
	}
	
	/**
	 * 静态魔术方法
	 * 变相设置用户系统
	 */
	public function __callstatic($method, $arg)
	{
		$O_user = new Oauthuser();
	 	$O_user->system = $method;
	 	return $this;
	}
}