<?php 
class PaymentModel extends Model
{

	protected $_auto = array (		
		array('dateline', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	/* 添加或更新数据 */
	public function update($data, $iscreate = TRUE) {		
		if ($iscreate == TRUE) $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['pay_code']) && $this->find($data['pay_code'])) {
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
		$pay_list = $this->select();
		$cache_data = array();
		foreach ($pay_list as $r) {
            $r['config'] = unserialize($r['config']);
			$cache_data[$r['pay_code']] = $r;
		}
		setcache('payment', $cache_data, 'pay');
		return TRUE;
	}
}