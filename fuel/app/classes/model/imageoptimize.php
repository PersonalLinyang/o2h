<?php

class Model_Imageoptimize extends Model
{
	
	/*
	 * 获取特定条件的图片设定参数
	 */
	public static function SelectImageOptionList($params) {
		$sql = "SELECT image_option_id, image_option_name, image_option_slug, image_type, image_device, max_width, max_height " 
				. "FROM m_image_option " 
				. "WHERE 1=1 ";
		foreach($params as $key => $value) {
			$sql .= " AND " . $key . "=:" . $key . " ";
		}
		$query = DB::query($sql);
		foreach($params as $key => $value) {
			$query->param($key, $value);
		}
		$result = $query->execute()->as_array();
		
		if(count($result)) {
			return $result;
		} else {
			return false;
		}
	}
	
	/**
	 * 调整图片尺寸并另存为JPG
	 */
	public static function ImageResizeToJpg($orig_file, $max_width, $max_height, $new_fname)
	{
		// 检测是否安装了gd拓展包
		if (!extension_loaded('gd')) {
			return false;	
		}
		
		//获取图片信息并计算调整后的图片高度
		$result = getimagesize($orig_file);
		list($orig_width, $orig_height, $image_type) = $result;
		$resize_width = $orig_width;
		$resize_height = $orig_height;
		if($max_width) {
			if($orig_width > $max_width) {
				$resize_width = $max_width;
			}
			$resize_height = intval(($orig_height * $resize_width) / $orig_width);
			if($resize_height > $max_height && $max_height) {
				$resize_height = $max_height;
				$resize_width = intval(($orig_width * $resize_height) / $orig_height);
			}
		} elseif($max_height) {
			if($resize_height > $max_height) {
				$resize_height = $max_height;
				$resize_width = intval(($orig_width * $resize_height) / $orig_height);
			}
		}
		
		// 复制图片至内存
		switch ($image_type) {
			// 2 IMAGETYPE_JPEG
			// 3 IMAGETYPE_PNG
			case 2: $im = imagecreatefromjpeg($orig_file);  break;
			case 3: $im = imagecreatefrompng($orig_file); break;
			default:
				return false;
		}
		
		//生成保存后图片文件(空白图片)
		$new_image = imagecreatetruecolor($resize_width, $resize_height);
		
		// PNG图片的透过处理
		if (($image_type == 1) OR ($image_type==3)) {
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagefilledrectangle($new_image, 0, 0, $resize_width, $resize_height, $transparent);
		}

		//制成制定尺寸的图片
		if (!imagecopyresampled($new_image, $im, 0, 0, 0, 0, $resize_width, $resize_height, $orig_width, $orig_height)) {
			imagedestroy($im);
			imagedestroy($new_image);
			return false;
		}
		
		//保存图片文件
		$result = imagejpeg($new_image, $new_fname);
		
		if (!$result) {
			imagedestroy($im);
			imagedestroy($new_image);
			return false;
		}
		
		//删除临时文件
		imagedestroy($im);
		imagedestroy($new_image);
	}

}

