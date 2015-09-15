<?php
class form
{
    /**
     * 编辑器
     * @param string $toolbar
     * @param string $width
     * @param string $height
     */
    public static function editor($name, $value, $toolbar = 'full', $width = '99.9%', $height='350px') {;
        $string = '<script id="editor_'.$name.'" name="'.$name.'" type="text/plain" style="width:'.$width.'; height:'.$height.'">'.$value.'</script>';
        if (!defined('EDITOR_INIT')) {
            $string .= '<script type="text/javascript" src="'. JS_PATH .'ueditor/ueditor.config.js"></script>';
            $string .= '<script type="text/javascript" src="'. JS_PATH .'ueditor/ueditor.all.js"></script>';
        }
        
        $toolbars = '';
        if($toolbar == 'base') {
            $toolbars .= ",toolbars: [['fullscreen', 'source', '|',
            'bold', 'italic', 'underline', 'removeformat',  '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',  'link', 'unlink', 'anchor', '|',  'simpleupload', 'insertimage', 'emotion','attachment', 'map', 'gmap', '|', 'print', 'preview', 'searchreplace', 'help', 'drafts']]";
        }
        $string .= '<script type="text/javascript">';
        $string .= "var ue = UE.getEditor('editor_".$name."', {serverUrl:'".
                U('Attachment/Upload/ueditor')."'".$toolbars."});";
        $string .= '</script>';
        return $string;
    }
	public static function text($name, $value) {
		return '<input type="text" value="'.$value.'" name="'.$name.'" class="text_input">';
	}

	public static function textarea($name, $value) {
		return '<textarea name="'.$name.'">'.$value.'</textarea>';
	}

	/**
	 * 下拉选择框
	 */
	public static function select($name, $value, $array = array(), $default = '', $class='', $width = 0) {
		if(!is_array($array) || count($array)== 0) return false;
		$str = '';
		if($class) $str .= 'class="'.$class.'"';
		if($width) $str .= 'style="width:'.$width.'"';
		$string = '<select name="'.$name.'" '.$str.'>';
		if($default) $string.= '<option>'.$default.'</option>';	
		foreach($array as $k=>$v) {
			$selected = ($k == $value) ? 'selected' : '';
			$string .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
		}
		$string .= '</select>';
		return $string;
	}

	/* 多项选择 */
	public static function selects($name, $value, $array = array(), $class='', $width = 0) {
		if(!is_array($array) || count($array)== 0) return false;
		$str = '';
		if($class) {
			$str .= 'class="'.$class.'"';
		}
		if($width) {
			$str .= 'style="width:'.$width.'"';
		}
		if($value != '') $value = strpos($value, ',') ? explode(',', $value) : array($value);
		$string = '<select name="'.$name.'[]" '.$str.' multiple>';
		foreach($array as $k=>$v) {
			$selected = (in_array($k, $value)) ? 'selected' : '';
			$string .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
		}
		$string .= '</select>';
		return $string;
	}

	/* 日期/时间 */
	public static function datetime($name, $value) {
		$string = '<input type="text" id="'.$name.'_time" name="'.$name.'" value="'.$value.'" style="width:120px">';
		if (!defined('TIMEPICKER_INIT')) {
			$string .= '
			<link rel="stylesheet" type="text/css" href="'.JS_PATH.'timepicker/css/jquery-ui.css" />
			<link rel="stylesheet" type="text/css" href="'.JS_PATH.'timepicker/css/jquery-ui-timepicker-addon.css" />
			<script type="text/javascript" src="'.JS_PATH.'timepicker/js/jquery-ui-min.js"></script>
			<script type="text/javascript" src="'.JS_PATH.'timepicker/js/jquery-ui-timepicker-addon.js"></script>
			<script type="text/javascript" src="'.JS_PATH.'timepicker/js/jquery-ui-datepicker-zh-CN.js"></script>
			<script type="text/javascript" src="'.JS_PATH.'timepicker/js/jquery-ui-timepicker-zh-CN.js"></script>';
			define('TIMEPICKER_INIT', TRUE);
		}
		$d0 = ($value) ? $value : "";
		$string .= '
		<script type="text/javascript">
		$(function() {
            $("#'.$name.'_time").datetimepicker({
                minDate: 0,
                timeFormat: "HH:mm",
                dateFormat: "yy-mm-dd",
                onSelect:function(dateText,inst){
                    var str = dateText.replace(/-/g,"/");
                    var myDate= new Date(str);
                    myDate.setDate(myDate.getDate()+1);
                    $("#'.$name.'_time").datepicker("option","minDate",myDate); 
                    
                }  
            });
			var d0 = "'.$d0.'";
			$("#'.$name.'_time").datepicker("setDate", d0);
        })
		</script>
		';
		return $string;
	}

	
	/**
	 * 复选框
	 * 
	 * @param $array 选项 二维数组
	 * @param $id 默认选中值，多个用 '逗号'分割
	 * @param $str 属性
	 * @param $defaultvalue 是否增加默认值 默认值为 -99
	 * @param $width 宽度
	 */
	public static function checkbox($name, $value, $array = array(), $class='', $width = 0) {
		$string = '';
		$value = trim($value);
		if($value != '') $value = strpos($value, ',') ? explode(',', $value) : array($value);
		$i = 1;
		foreach($array as $k => $v) {
			$k = trim($k);
			$checked = ($value && in_array($k, $value)) ? 'checked' : '';
			$width = ($width) ? 'width:'.$width.'px;' : '';
			$string .= '<label class="ib" style="width:'.$width.'px">';
			$string .= '<input type="checkbox" '.$str.' id="'.$name.'_'.$i.'" value="'.htmlspecialchars($k).'" '.$checked.' name="'.$name.'[]"/> '.htmlspecialchars($v).'&nbsp;';
			$string .= '</label>';
			unset($checked);
			$i++;
		}
		return $string;
	}

	/**
	 * 单选框
	 * @param $name  表单名称
	 * @param $value 默认值
	 * @param $array 选项
	 * @param $class 样式名
	 * @param $width 长度
	 */
	public static function radio($name, $value, $array = array(), $class='', $width = 0) {
		$string = '';
		foreach($array as $k => $v) {
			$checked = trim($value)==trim($k) ? 'checked' : '';
			$style = '';
			if($width > 0) $style .= "width:".$width."px";
			$string .= '<label class="ib '.$class.'" style="'.$style.'">';
			$string .= '<input type="radio" id="'.$name.'_'.htmlspecialchars($k).'" '.$checked.' value="'.trim($k).'" name="'.$name.'">'.$v.'&nbsp;';
			$string .= '</label>';
		}
		return $string;
	}
	
	
}