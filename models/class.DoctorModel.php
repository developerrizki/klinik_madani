<?php
/**
* 
*/
class DoctorModel extends Model
{
	
	public function __construct()
	{
		parent::__construct();

		$this->_table	= 'dp_dokter';
		$this->_id 		= 'id_dokter';
	}

	public function getDetail($id)
	{
		global $cfg;

		$sql = "SELECT * FROM dp_dokter WHERE id_dokter = '$id'";
		$res = null;

		try {

			$this->_dbObj->query($sql);

			$res = $this->_dbObj->fetch();	

		} catch (DBException $e) {
			Error::store('Doctor', $e->getMessage());
		}

		return $res;
	}

	public function getDoctorList()
	{
		global $cfg;

		$sql = "SELECT * FROM dp_dokter";
		$res = null;

		try {
			$this->_dbObj->query($sql);

			$res = $this->_dbObj->fetchAll();
		} catch (DBException $e) {
			Error::store('Doctor', $e->getMessage());
		}
	}
}
?>