<?php
namespace ActiveRecord;

class Redis
{
	const DEFAULT_PORT = 6379;

	private $redis;

    /**
    * $cfg->set_cache(
    *      'redis://'.Config('redis.servers.default'),
    *          'namespace' => Config('app.application_name').":orm",
    *          * array(
    *          'expire'    => 1200
    *      )
    *  );
    *  @param $options array()
     */
    public function __construct($options)
    {
        $options['port'] = isset($options['port']) ? $options['port'] : self::DEFAULT_PORT;
        $redis = new \Redis();
        $redis->connect($options['host'].":".$options['port']);
        $auth = Config('redis.auth');
        if (!empty($auth)) {
            $redis->auth($auth);
        }
        $this->redis = $redis;
    }

    public function flush($options)
	{
		$keys = $this->redis->keys($options['namespace'].'*');
        if (!empty($keys)) {
            foreach ($keys as $key) {
                $this->redis->del($key);
            }
        }
	}

	public function read($key)
	{
		return unserialize($this->redis->get($key));
	}

	public function write($key, $value, $expire)
	{
		$value = serialize($value);
		return $this->redis->set($key, $value, $expire);
	}

	public function delete($key)
	{
		$this->redis->del($key);
	}
}
