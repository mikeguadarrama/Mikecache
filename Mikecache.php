<?php 

/*
	Miguel Guadarrama / @mikesoft
	Mikecache CodeIgniter 3.0.0 Library
	Handles QueryBuilder caches
	MIT License
*/


if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Mikecache {

	private $path;
	private $ci;

	function __construct(){
		$this->ci =& get_instance();
		$this->path=APPPATH.'cache/'.'mikecache/';
		$this->_isDir($this->path);
	}
	
	private function _isDir($dir){
		if(!is_dir($dir)) mkdir($dir);	
		return true;
	}
	
	public function cache($minutes=false, $folder=''){
		$path = $this->path . ltrim($folder,'/') . '/';
		$this->_isDir($path);
		$query = $this->ci->db->get_compiled_select();
		$file = $path . md5($query);
		if(file_exists($file) && ($minutes===false || (is_numeric($minutes) && (time()-filemtime($file))/60<=$minutes))){
			$results = unserialize(file_get_contents($file));
		}else{
			$results = $this->ci->db->query($query)->result();
			file_put_contents($file, serialize($results));
		}
		return $results;
	}
	
	public function clear($dir=false){
		$dir = $dir===false || empty($dir) ? $this->path:$this->path . ltrim($dir, '/');
		$files = array_diff(scandir($dir), array('.','..')); 
		if(count($files)>0){
			foreach($files as $file){
				is_dir($dir.'/'.$file) ? $this->clear(str_replace($this->path, '', $dir.'/'.$file)) : unlink($dir.'/'.$file);	
			}
		}
		return $dir !== $this->path ? rmdir($dir) : true;
	}
}

/* End of file Mikeclass.php */