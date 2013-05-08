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
	protected $root_dir = "/tmp";

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
		$files = scandir($this->root_dir."/".$path);
		//echo($this->root_dir."/".$path);
		foreach($files as $file) {
			if ($file != "." && $file != "..") {
				$type = "default";
				if (is_dir($this->root_dir."/".$path."/".$file)) {
					$type = "folder";
				}
				$children[] = array( title => $file, path => $path."/".$file, type => $type);
			}
		}
		return $children;
	}
	function _get_path($id) {
		$node = $this->_get_node($id);
		$path = array();
		/*
		if(!$node === false) return false;
		$this->db->query("SELECT `".implode("` , `", $this->fields)."` FROM `".$this->table."` WHERE `".$this->fields["left"]."` <= ".(int) $node[$this->fields["left"]]." AND `".$this->fields["right"]."` >= ".(int) $node[$this->fields["right"]]);
		while($this->db->nextr()) $path[$this->db->f($this->fields["id"])] = $this->db->get_row("assoc");
		*/
		return $path;
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
		//echo "data:".$data["path"]."\n";
		//echo "command:file ".$this->root_dir."/".$data["path"];
		$ret = shell_exec("file ".$this->root_dir."/".$data["path"]);
		//var_dump($ret);
		return $ret;
	}
}

?>
