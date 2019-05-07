<?php
interface databaseModel
{
	public function select();
	public function select_all();
	public function select_byID($id);
	public function insert();
	public function update();
	public function delete();	

}

?>