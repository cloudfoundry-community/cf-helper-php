<?php
namespace Arhframe\Util;
use Arhframe\Util\UtilException;
/**
* 
*/
class File
{
	private $folder;
	private $basename;
	private $filename;
	private $extension;
	private $content;
	private $isUrl = false;
	function __construct($file)
	{
		if(preg_match('#^http(s){0,1}://#', $file)){
			$this->isUrl = true;
		}
		$pathinfo = pathinfo($file);
		$this->folder = $pathinfo['dirname'];
		if($this->folder=='.'){
			$this->folder = '';
		}
		$this->basename = $pathinfo['basename'];
		$this->filename = $pathinfo['filename'];
		$this->extension = $pathinfo['extension'];
	}
	public function getFolder(){
		$folder = $this->folder;
		if(!is_dir($this->folder) && $folder[0] != '/'){
			$folder = '/'. $folder;
		}
		return $folder;
	}
	public function setFolder($folder){
		return $this->folder = $folder;
	}
	public function getName(){
		return $this->basename;
	}
	public function setName($basename){
		return $this->basename = $basename;
	}
	public function getBase(){
		return $this->filename;
	}
	public function setBase($filename){
		return $this->filename = $filename;
	}
	public function getExtension(){
		return $this->extension;
	}
	public function setExtension($extension){
		return $this->extension = $extension;
	}
	public function absolute(){
		$extension = null;
		if(isset($this->extension)){
			$extension = '.'. $this->extension;
		}
		return $this->folder .'/'.$this->filename .$extension;
	}
	public function isFile(){
		return is_file($this->absolute());
	}
	public function getContent(){
		if($this->isUrl){
			return $this->curlGetContent($this->absolute());
		}
		if(!$this->isFile()){
			throw new UtilException("File '". $this->absolute() ."' doesn't exist.");
		}
		
		$handle = fopen($this->absolute(), 'r');
		$return = null;
		while (($buffer = fgets($handle)) !== false) {
	        $return .= $buffer;
	    }
	    if (!feof($handle)) {
	        throw new UtilException("Error: unexpected fgets() fail\n");
	    }
	    fclose($handle);
		return $return;
	}
	public function touch(){
		if(!$this->isFile()){
			throw new UtilException("File '". $this->absolute() ."' doesn't exist.");
		}
		touch($this->absolute());
	}
	public function getTime(){
		if(!$this->isFile()){
			throw new UtilException("File '". $this->absolute() ."' doesn't exist.");
		}
		filemtime($this->absolute());
	}
	public function setContent($content){
		$this->createFolder();
		file_put_contents($this->absolute(), $content);
	}
	public function createFolder(){
		if(is_dir($this->folder)){
			return;
		}
		mkdir($this->folder, 0777, true);
	}
	public function getSize(){
		if(!$this->isFile()){
			throw new UtilException("File '". $this->absolute() ."' doesn't exist.");
		}
		return filesize($this->absolute());
	}
	public function getArray(){
		return explode('/', $this->absolute());
	}
	public function __toString(){
		return $this->absolute();
	}
	public function remove(){
		if(!$this->isFile()){
			throw new UtilException("File '". $this->absolute() ."' doesn't exist.");
		}
		unlink($this->absolute());
	}
	public function match($regex){
		if(!$this->isFile()){
			throw new UtilException("File '". $this->absolute() ."' doesn't exist.");
		}
		return preg_match($regex, $this->absolute());
	}
	public function isUrl(){
		return $this->isUrl;
	}
	private function curlGetContent($url)
	{
	    $ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}