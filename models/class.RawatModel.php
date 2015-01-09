<?php
/**
* 
*/
class RawatModel extends Model
{
	
	public function __construct()
	{
		parent::__construct();

		$this->_table 	= "dp_ruang";
		$this->_id 		= "id_ruang";
	}

	public function getDetail($id){
		global $cfg;

		$sql = "SELECT * FROM dp_ruang WHERE id_ruang = '$id'";
		$res = null;

		try {
			$this->_dbObj->query($sql);

			$res = $this->_dbObj->fetch();
		} catch (DBException $e) {
			Error::store('Rawat', $e->getMessage);
		}

		return $res;
	}

}
?>