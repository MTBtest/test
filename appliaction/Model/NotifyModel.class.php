<?php 
class NotifyModel extends Model
{

	protected $_auto = array (
		array('dateline', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {
		// if ($iscreate == TRUE) $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		$data['dateline'] = NOW_TIME;
		if (isset($data['code']) && $this->find($data['code'])) {
			$result = $this->save($data);
			if (!$result) {
				$this->error = '更新数据失败';
				return false;
			}
		} else {
			$result = $this->add($data);
			if ($result === false) {
				$this->error = '添加数据失败';
				return false;
			}
		}
		return $result;
	}

	/* 生成缓存数据 */
	public function build_cache() {
		$notify_list = $this->select();
		$cache_data = array();
		foreach ($notify_list as $r) {
            $r['config'] = json_decode($r['config'],TRUE);
			$cache_data[$r['code']] = $r;
		}
		setcache('notify', $cache_data, 'notify');
		return TRUE;
	}
}