<?php
/**
* 
*/
class PatientModel extends Model
{
	
	public function __construct()
	{
        parent::__construct();

        $this->_table 	= 'dp_pasien';
		$this->_id 		= 'id_pasien';
	}

	public function getDetail($id)
	{
		global $cfg;

		$sql = "SELECT * FROM dp_pasien WHERE id_pasien = '$id'";

		$res = null;

		try {
			$this->_dbObj->query($sql);

			$res = $this->_dbObj->fetch();
		} catch (DbException $e) { Error::store('Patient', $e->getMessage()); }
	}

	public function getOfficerList(){
		global $cfg;

		$sql = " SELECT 
					* 
				 FROM 
				 	dp_sys_user,dp_sys_user_group
				 WHERE
				 	dp_sys_user.user_id 		= dp_sys_user_group.user_id AND 
				 	dp_sys_user_group.group_id 	= 2
		";
		$res = null;

		try {
			$this->_dbObj->query($sql);

			$res = $this->_dbObj->fetchAll();
		} catch (DbException $e) {
			Error::store('Car', $e->getMessage());
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
		} catch (DbException $e) {
			Error::store('Car', $e->getMessage());
		}

		return $res;

	}

	public function getCareList() {
		global $cfg;

		$sql = "SELECT
					* 
				FROM 
					dp_ruang
			"; 
		$res = null;

		try {
			$this->_dbObj->query($sql);

			$res = $this->_dbObj->fetchAll();
		} catch (DbException $e) {
			Error::store('Car', $e->$getMessage);
		}
		return $res;
	}
	
	public function getStatusList() {
		global $cfg;

		$sql = "SELECT * FROM dp_status"; 
		$res = null;

		try {
			$this->_dbObj->query($sql);

			$res = $this->_dbObj->fetchAll();
		} catch (DbException $e) {
			Error::store('Car', $e->$getMessage);
		}

		return $res;

	}
}
?>