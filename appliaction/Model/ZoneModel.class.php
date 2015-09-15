<?php 
class ZoneModel extends Model
{
	protected $_validate  = array(
	    array('name', 'require', '区域名称不能为空', self::EXISTS_VALIDATE, '', self:: MODEL_BOTH),
	    array('name', '', '区域名称已存在', self::EXISTS_VALIDATE, 'unique', self:: MODEL_BOTH),
	    array('provinces', 'require', '区域城市不能为空', self::EXISTS_VALIDATE, '', self:: MODEL_BOTH),
	);
	protected $_auto = array (		
		array('dateline', NOW_TIME, self::MODEL_BOTH, 'string'),
	);

	public function update($data, $iscreate = TRUE) {		
		if ($iscreate == TRUE) $data = $this->create($data);
		if (empty($data)) {
			$this->error = $this->getError();
			return false;
		}
		if (isset($data['id']) && is_numeric($data['id'])) {
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
}