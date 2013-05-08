<?php
class _tree_struct {
	// Structure table and fields
	protected $table	= "";
	protected $fields	= array(
			"id"		=> false,
			"parent_id"	=> false,
			"position"	=> false,
			"left"		=> false,
			"right"		=> false,
			"level"		=> false
		);
	protected $root_dir = "data";

	// Constructor
	function __construct($table = "tree", $fields = array()) {
		$this->table = $table;
		if(!count($fields)) {
			foreach($this->fields as $k => &$v) { $v = $k; }
		}
		else {
			foreach($fields as $key => $field) {
				switch($key) {
					case "id":
					case "parent_id":
					case "position":
					case "left":
					case "right":
					case "level":
						$this->fields[$key] = $field;
						break;
				}
			}
		}
	}

	function _get_children($path, $recursive = false) {
		$children = array();
		$files = scandir($this->_get_path($path, "", true));
		foreach($files as $file) {
			if ($file != "." && $file != "..") {
				$type = "default";
				if (is_dir($this->_get_path($path, $file, true))) {
					$type = "folder";
				}
				$children[] = array( title => $file, path => $this->_get_path($path, $file), type => $type);
			}
		}
		return $children;
	}
	function _get_path($path, $file = "", $full = false) {
		if ($full) {
			$new_path = $this->root_dir."/";
		}
		if ($path == "") {
			$new_path .= $file;
		} else {
			$new_path .= "/".$path."/".$file;
		}
		return $new_path;
	}
}

class json_tree extends _tree_struct { 
	function __construct($table = "tree", $fields = array(), $add_fields = array("title" => "title", "type" => "type")) {
		parent::__construct($table, $fields);
		$this->fields = array_merge($this->fields, $add_fields);
		$this->add_fields = $add_fields;
	}

	function get_children($data) {
		$tmp = $this->_get_children($data["path"]);
		/*
		if((int)$data["path"] == "." && count($tmp) === 0) {
			$this->_create_default();
			$tmp = $this->_get_children((int)$data["id"]);
		}
		*/
		$result = array();
		//if((int)$data["id"] === 0) return json_encode($result);
		foreach($tmp as $k => $v) {
			$result[] = array(
				"attr" => array("path" => $v["path"], "rel" => $v["type"]),
				"data" => $v["title"],
				"state" => ($v["type"] == "folder") ? "closed" : ""
			);
		}
		return json_encode($result);
	}
	
	function select_node($data) {
		$ret = "";
		
		// check type
		$mime_type = shell_exec("file --mime-type -b \"".$this->root_dir."/".$data["path"]."\"");
		if (strpos($mime_type, "audio") !== false) {
			$ret = shell_exec("mediainfo \"".$this->root_dir."/".$data["path"]."\"");
		} else {
			$ret = shell_exec("file \"".$this->root_dir."/".$data["path"]."\"");
		}
		//var_dump($ret);
		return "<pre>".$ret."</pre>";
	}
}

?>
