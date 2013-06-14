<?php

/**
 * 缓存对象
 * @author ardar
 *
 */
interface ICache
{
	/**
	 * 初始化缓存对象
	 * @param array $config  缓存配置数组
	 * @param int $expireSeconds  默认缓存过期时间
	 */
	public function init($config, $defaultExpireSeconds=60);
	
	/**
	 * 检查缓存key是否正在缓存有效状态
	 * @param string $cacheKey
	 */
	public function isCaching($cacheKey);

	/**
	 * 获取缓存数据
	 * @param string $cacheKey
	 * @return mixed|null 如果没有找到结果，返回null
	 */
	public function get($cacheKey, $expireSeconds=0);
	
	/**
	 * 保存缓存数据
	 * @param string $cacheKey
	 * @param string $content
	 * @param number $expireSeconds
	 */
	public function set($cacheKey, $content, $expireSeconds=0);
	
	/**
	 * 删除缓存Key
	 * @param unknown $cacheKey
	 */
	public function delete($cacheKey);
	
	/**
	 * 清除所有缓存数据
	 */
	public function clear();
	
}