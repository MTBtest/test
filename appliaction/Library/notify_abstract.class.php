<?php
abstract class notify_abstract
{
	protected $config = array();

	public function set_config($config) {
		foreach ($config as $key => $value) $this->config[$key] = $value;
		return $this;
	}

}