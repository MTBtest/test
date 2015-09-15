<?php
/**
 *	  [Haidao] (C)2013-2099 Dmibox Science and technology co., LTD.
 *	  This is NOT a freeware, use is subject to license terms
 *
 *	  http://www.haidao.la
 *	  tel:400-600-2042
 */
class DatabaseController extends AdminBaseController {
	public function _initialize() {
		parent::_initialize();
		$this->backup_path = RUNTIME_PATH.C('DATABASE_BACKUP_PATH');
	}
	/**
	 * 数据库备份/还原列表
	 * @param  String $type import-还原，export-备份
	 */
	public function index() {
		$dialog='';
		$type = I('type','export');
		 switch ($type) {
			/* 数据还原 */
			case 'import':
				if(!file_exists($this->backup_path))
					showmessage('备份目录不存在');
				//列出备份文件列表
				$path = realpath($this->backup_path);
				$flag = FilesystemIterator::KEY_AS_FILENAME;
				$glob = new FilesystemIterator($path, $flag);
				$list = array();
				foreach ($glob as $name => $file) {
					if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
						$name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

						$date = "{$name[0]}-{$name[1]}-{$name[2]}";
						$time = "{$name[3]}:{$name[4]}:{$name[5]}";
						$part = $name[6];

						if (isset($list["{$date} {$time}"])) {
							$info = $list["{$date} {$time}"];
							$info['part'] = max($info['part'], $part);
							$info['size'] = $info['size'] + $file->getSize();
						} else {
							$info['part'] = $part;
							$info['size'] = $file->getSize();
						}
						$extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
						$info['compress'] = ($extension === 'SQL') ? '-' : $extension;
						$info['time'] = strtotime("{$date} {$time}");

						$list["{$date} {$time}"] = $info;
					}
				}
				$title = '数据还原';
				break;

			/* 数据备份 */
			case 'export':
				$Db = Db::getInstance();
				$list = $Db->query('SHOW TABLE STATUS');
				$list = array_map('array_change_key_case', $list);
				$title = '数据备份';
				break;

			default:
				showmessage('参数错误！');
		}
		//渲染模板
		include $this->admin_tpl('database_'.$type);
	}
	/**
	 * 优化表
	 * @param  String $tables 表名
	 * @author 
	 */
	public function optimize($tables = null){
		$tables = isset($tables)?$tables:$_GET['id'];
		if($tables) {
			$Db   = Db::getInstance();
			if(is_array($tables)){
				$tables = implode('`,`', $tables);
				$list = $Db->query("OPTIMIZE TABLE `{$tables}`");
				if($list){
					showmessage("数据表优化完成！");
				} else {
					showmessage("数据表优化出错请重试！");
				}
			} else {
				$list = $Db->query("OPTIMIZE TABLE `{$tables}`");
				if($list){
				   showmessage("数据表'{$tables}'优化完成！");
				} else {
					showmessage("数据表'{$tables}'优化出错请重试！");
				}
			}
		} else {
			showmessage("请指定要优化的表！");
		}
	}

	/**
	 * 修复表
	 * @param  String $tables 表名
	 * @author 
	 */
	public function repair($tables = null){
		$tables = isset($tables)?$tables:$_GET['id'];
		if($tables) {
			$Db   = Db::getInstance();
			if(is_array($tables)){
				$tables = implode('`,`', $tables);
				$list = $Db->query("REPAIR TABLE `{$tables}`");
				if($list){
				   showmessage("数据表修复完成！");
				} else {
					showmessage("数据表修复出错请重试！");
				}
			} else {
				$list = $Db->query("REPAIR TABLE `{$tables}`");
				if($list){
				   showmessage("数据表'{$tables}'修复完成！");
				} else {
					showmessage("数据表'{$tables}'修复出错请重试！");
				}
			}
		} else {
			showmessage("请指定要修复的表！");
		}
	}

	/**
	 * 删除备份文件
	 * @param  Integer $time 备份时间
	 * @author 
	 */
	public function del($time = 0){
		if($time){
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath($this->backup_path) . DIRECTORY_SEPARATOR . $name;
			array_map("unlink", glob($path));
			if(count(glob($path))){
			   showmessage('备份文件删除失败，请检查权限！');
			} else {
			   showmessage('备份文件删除成功！',U('Database/index?type=import'),1);
			}
		} else {
			showmessage('参数错误！');
		}
	}

	/**
	 * 备份数据库
	 * @param  String  $tables 表名
	 * @param  Integer $id	 表ID
	 * @param  Integer $start  起始行数
	 * @author 
	 */
	public function export($tables = null, $id = null, $start = null){
		libfile('Database');
		if(IS_POST && !empty($tables) && is_array($tables)){ //初始化
			//读取备份配置
			$config = array(
				'path'	 => $this->backup_path. DIRECTORY_SEPARATOR,
				'part'	 => 20971520,
				'compress' => 1,
				'level'	=> 9,
			);

			//检查是否有正在执行的任务
			$lock = "{$config['path']}backup.lock";
			if(is_file($lock)){
				showmessage( $lock.'检测到有一个备份任务正在执行，请稍后再试！');
			} else {
				//创建锁文件
				file_put_contents($lock, NOW_TIME);
			}
			// 自动创建备份文件夹
			if(!file_exists($config['path']) || !is_dir($config['path'])) dir_create($config['path']);
			//检查备份目录是否可写
			is_writeable($config['path']) || showmessage('备份目录不存在或不可写，请检查后重试！');
			session('backup_config', $config);
			//生成备份文件信息
			$file = array(
				'name' => date('Ymd-His', NOW_TIME),
				'part' => 1,
			);
			session('backup_file', $file);

			//缓存要备份的表
			session('backup_tables', $tables);

			//创建备份文件
			
			$Database = new Database($file, $config);
			if(false !== $Database->create()){
				$tab = array('id' => 0, 'start' => 0);
				$data=array();
				$data['status']=1;
				$data['info']='初始化成功！';
				$data['tables']=$tables;
				$data['tab']=$tab;
				echo json_encode($data);
				//$tab = array('id' => 0, 'start' => 0);
			   //showmessage('初始化成功！', '', array('tables' => $tables, 'tab' => $tab));
			} else {
				showmessage('初始化失败，备份文件创建失败！');
			}
		} elseif (IS_GET && is_numeric($id) && is_numeric($start)) { //备份数据
			$tables = session('backup_tables');	  
			//备份指定表
			$Database = new Database(session('backup_file'), session('backup_config'));
			$start  = $Database->backup($tables[$id], $start);
			if(false === $start){ //出错
				showmessage('备份出错！');
			} elseif (0 === $start) { //下一表
				if(isset($tables[++$id])){
					$tab = array('id' => $id,'table'=>$tables[$id],'start' => 0);
					$data=array();
					$data['rate'] = 100;
					$data['status']=1;
					$data['info']='备份完成！';
					$data['tab']=$tab;
					echo json_encode($data);
				  // showmessage('备份完成！', '', array('tab' => $tab));
				} else { //备份完成，清空缓存
					unlink($this->backup_path.DIRECTORY_SEPARATOR.'backup.lock');
					session('backup_tables', null);
					session('backup_file', null);
					session('backup_config', null);
					showmessage('备份成功');
				}
			} else {
				$tab  = array('id' => $id,'table'=>$tables[$id],'start' => $start[0]);
				$rate = floor(100 * ($start[0] / $start[1]));
				$data=array();
				$data['status']=1;
				$data['rate'] = $rate;
				$data['info']="正在备份...({$rate}%)";
				$data['tab']=$tab;
				echo json_encode($data);
				//showmessage("正在备份...({$rate}%)", '', array('tab' => $tab));
			}
		} else { //出错
			showmessage('参数错误！');
		}
	}

	/**
	 * 还原数据库
	 * @author 
	 */
	public function import($time = 0, $part = null, $start = null){
		libfile('Database');
		if(is_numeric($time) && (is_null($part)||empty($part)) && (is_null($start)||empty($start))){ //初始化
			//获取备份文件信息
			$name  = date('Ymd-His', $time) . '-*.sql*';
			$path  = realpath($this->backup_path) . DIRECTORY_SEPARATOR . $name;
			$files = glob($path);
			$list  = array();
			foreach($files as $name){
				$basename = basename($name);
				$match	= sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
				$gz	   = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
				$list[$match[6]] = array($match[6], $name, $gz);
			}
			ksort($list);
			//检测文件正确性
			$last = end($list);
			if(count($list) === $last[0]){
				session('backup_list', $list); //缓存备份列表
			   showmessage('初始化完成！', '', array('part' => 1, 'start' => 0));
			} else {
				showmessage('备份文件可能已经损坏，请检查！');
			}
		} elseif(is_numeric($part) && is_numeric($start)) {
			$list  = session('backup_list');
			$db = new Database($list[$part], array(
				'path'	 => realpath(C('DATA_BACKUP_PATH')) . DIRECTORY_SEPARATOR,
				'compress' => $list[$part][2]));

			$start = $db->import($start);
			if(false === $start){
				showmessage('还原数据出错！');
			} elseif(0 === $start) { //下一卷
				if(isset($list[++$part])){
					$data = array('part' => $part, 'start' => 0);
				   showmessage("正在还原...#{$part}", '', $data);
				} else {
					session('backup_list', null);
				   showmessage('还原完成！');
				}
			} else {
				$data = array('part' => $part, 'start' => $start[0]);
				if($start[1]){
					$rate = floor(100 * ($start[0] / $start[1]));
				   showmessage("正在还原...#{$part} ({$rate}%)", '', $data);
				} else {
					$data['gz'] = 1;
					showmessage("正在还原...#{$part}", '', $data);
				}
			}

		} else {
			showmessage('参数错误！');
		}
	}
}
