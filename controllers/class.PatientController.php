<?php
/**
* 
*/
class PatientController extends Controller
{	

	private $_pasienModel, $_id;
	
	public function __construct()
	{
		parent::__construct();

		$this->_pasienModel		= $this->loadModel('Patient');
		$this->_id 				= basename($_SERVER['REQUEST_URI']); 
	}

	public function index()
	{
		global $cfg;
		global $globalVar;
		global $sessObj;

		$this->requireLogin();
		$this->authorize('pasien', 'list');

		// load class GRID
		$this->loadClass('DBGrid', CLASS_DIR . '/blackcat/view/datagrid');
		$this->loadLib('lib');

		// script sql
		$sql = "SELECT
					*
				FROM
					dp_pasien, dp_dokter, dp_status, dp_sys_user, dp_ruang
				WHERE 
					dp_pasien.user_id	 	= dp_sys_user.user_id AND
					dp_pasien.id_dokter  	= dp_dokter.id_dokter AND
					dp_pasien.id_ruang		= dp_ruang.id_ruang AND
					dp_pasien.id_status 	= dp_status.id_status
		";

		// setting grid
		$grid = new DBGrid();
		$grid->setQuery($sql);
		$grid->setConnection($this->dbObj);
		$grid->enablePagination(true);
		// $grid->enableDefaultButton(true);
		$grid->setDefaultSortParam(array('order' => 'asc', 'sortby' => 'id_pasien' ));
		$grid->enableDefaultTool(true, true, false, true);

		$grid->setPaginationParam(10);

		// $grid->addColumn('cb');
		$grid->addColumn('name',			array('title' => 'Nama Pasien', 'sorting' => true, 'print' => true));
		$grid->addColumn('gender',			array('title' => 'Gender', 'sorting' => true, 'print' => true));
		$grid->addColumn('datein',			array('title' => 'Tanggal Datang', 'sorting' => true, 'print' => true));
		$grid->addColumn('officer',			array('title' => 'Petugas', 'sorting' => true, 'print' => true));
		$grid->addColumn('doctor',			array('title' => 'Dokter', 'sorting' => true, 'print' => true));
		$grid->addColumn('status',			array('title' => 'Status', 'sorting' => true, 'print' => true));
		$grid->addColumn('view',			array('title' => 'Action', 'value' => $globalVar['View'], 'colspan' => 3, 'task' => 'view'));
		$grid->addColumn('edit',			array('value' => $globalVar['Edit'], 'task' => 'edit'));
		$grid->addColumn('delete',			array('value' => $globalVar['Delete'], 'task' => 'delete'));

		// $grid->setCellFormat('cb',			array('TWidth' => '5%'));
		$grid->setCellFormat('name',   		array('TWidth' => '20%'));
		$grid->setCellFormat('gender',		array('TWidth' => '15%'));
		$grid->setCellFormat('datein', 		array('TWidth' => '15%'));
		$grid->setCellFormat('officer',		array('TWidth' => '13%'));
		$grid->setCellFormat('doctor',		array('TWidth' => '15%'));
		$grid->setCellFormat('status', 		array('TWidth' => '14%'));
		$grid->setCellFormat('view', 		array('TWidth' => '3%'));

		$grid->setDBField('cb',			'id_pasien');
		$grid->setDBField('name',       'nama_pasien');
		$grid->setDBField('gender',   	'gender');
		$grid->setDBField('datein',     'tanggal_datang');
		$grid->setDBField('officer',   	'user_name');
		$grid->setDBField('doctor',    	'nama_dokter');
		$grid->setDBField('status',    	'desc_status');

		$grid->setColumnFunction('status', 	'status');

		$grid->setColumnLink('view',    ROOT_URL . '/pasien/view', array('id' => 'id_pasien'));
		$grid->setColumnLink('delete',  'javascript:deleteItem()', array('id' => 'id_pasien'));
		
		$grid->setColumnElement('cb', array('type' => 'InputCheck',
											'name' => 'cb',
											'attr' => array('OnClick' => "enableButton('1')")));

		$grid->setSearchParam('name', 'Nama');

		$content = "
                    <div class='table-responsive'>
                    	". $grid->toString() ."
                    </div>
				   ";
		
		$notif ='';
		$title = $this->sessObj->getVar('title');
		
		if (!empty($title)) {
			$notif = $this->sessObj->getVar('message');
			$this->sessObj->setVar('title','');
		}	

		$this->set('message',$notif);
		$this->set('content',$content);

		return $this->getHTML('pasien/home');
	}

	public function add()
	{
		global $cfg, $globalVar, $sessObj;

		$this->requireLogin();
		$this->authorize('pasien','add');


		$date = mktime (0,0,0, date("m"), date("d"),date("Y"));
		
		$tanggalin 		= date('d-m-Y',$date);
		$tanggalout	    = date('d-m-Y',$date);


		/* Get Petugas */
		$getOfficer		= $this->_pasienModel->getOfficerList();
		$officer 		= array();

		for ($i=0; $i < sizeof($getOfficer); $i++) { 
			$officer[$getOfficer[$i]->user_id] = $getOfficer[$i]->user_name;
		}

		/* Get Dokter */
		$getDoctor		= $this->_pasienModel->getDoctorList();
		$doctor 		= array();

		for ($d=0; $d < sizeof($getDoctor); $d++) { 
			$doctor[$getDoctor[$d]->id_dokter] = $getDoctor[$d]->nama_dokter;
		}

		/* Get Dokter */
		$getDoctor		= $this->_pasienModel->getDoctorList();
		$doctor 		= array();

		for ($d=0; $d < sizeof($getDoctor); $d++) { 
			$doctor[$getDoctor[$d]->id_dokter] = $getDoctor[$d]->nama_dokter;
		}

		/* Get Rawat */
		$getCare		= $this->_pasienModel->getCareList();
		$care 	 		= array();

		for ($c=0; $c < sizeof($getCare); $c++) { 
			$care[$getCare[$c]->id_ruang] = $getCare[$c]->nama_ruang;
		}

		/* Get Status */
		$getStatus		= $this->_pasienModel->getStatusList();
		$status	 		= array();

		for ($s=0; $s < sizeof($getStatus); $s++) { 
			$status[$getStatus[$s]->id_status] = $getStatus[$s]->desc_status;
		}

		// gender
		$getGender = array("Pria","Wanita");
		$gender = array();

		foreach ($getGender as $jk) {
			$gender[$jk] = $jk;
		}

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');
		$this->loadLib('lib');

		$form = new FormGroup('patient_add', ROOT_URL . '/patient/add');

		$form->addText('name','', array('size' => '40', 'placeholder' => 'Nama Lengkap'));
		$form->groupAsRow('Nama');

		$form->addRadio('gender', $gender, '');
		$form->groupAsRow('Gender');

		$form->addTextArea('address','', array('placeholder' => 'Alamat Lengkap', 'style' => 'width:300px;'));
		$form->groupAsRow('Alamat');

		$form->addText('datein','', array('value' => $tanggalin,'required' => 'required', 'size' => '40', 'style' => 'width:120px'));
		$form->groupAsRow('Tanggal Datang');

		$form->addText('dateout','', array('value' => $tanggalout, 'required' => 'required', 'size' => '40', 'style' => 'width:120px'));
		$form->groupAsRow('Tanggal Pulang');

		$form->addSelect('officer', $officer, '');
		$form->groupAsRow('Petugas');

		$form->addSelect('doctor', $doctor, '');
		$form->groupAsRow('Dokter');

		$form->addSelect('care', $care, '');
		$form->groupAsRow('Ruang Periksa');

		$form->addTextArea('complaint','', array('placeholder' => 'Keluhan', 'style' => 'width:300px;'));
		$form->groupAsRow('Keluhan');

		$form->addSelect('status', $status, '');
		$form->groupAsRow('Status');

		$form->addRule('name', 	'required');
		$form->addRule('datein', 	'required');
		$form->addRule('dateout', 	'required');
		$form->addRule('address', 	'required');
		$form->addRule('complaint', 	'required');

		$form->addSubmit('add', 'Tambah', array('class' => 'button primary', 'style' => 'margin-top:20px'));
		$form->addButton('cancel', 'Batal', array('onClick' => "javascript:history.back(-1)"));
		$form->groupAsRow('');

		if (HTTP::getVar('add')){
			if ($form->validateElement()) {
				if (($this->_insert())) {
					$err = 0;
					$msg = "
					<div class='alert alert-success alert-dismissable'>
                       <button type'button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                       Pasien berhasil ditambahkan
                    </div>";

					$this->sessObj->setVar('message',$msg);
					$this->sessObj->setVar('title','berhasil');

					HTTP::redirect(ROOT_URL . "/patient");
				}
				else {
					$err = 1;
					$msg = "
					<div class='alert alert-danger alert-dismissable'>
                       <button type'button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                       Pasien gagal ditambahkan
                    </div>";
				}
			}
		}

		$content .= $form->toString();
	
		$this->set('message',$msg);
		$this->set('content',$content);
		return $this->getHTML('pasien/tambah_pasien');

	}

	private function _insert(){

		$name 			= addslashes($this->postParam('name'));
		$gender 		= addslashes($this->postParam('gender'));
		$address 		= addslashes($this->postParam('address'));
		$datein 		= addslashes($this->postParam('datein'));
		$dateout 		= addslashes($this->postParam('dateout'));
		$officer 		= addslashes($this->postParam('officer'));
		$doctor 		= addslashes($this->postParam('doctor'));
		$care 	 		= addslashes($this->postParam('care'));
		$complaint 		= addslashes($this->postParam('complaint'));
		$status	 		= addslashes($this->postParam('status'));

		$tanggalin      = explode("-", $datein);
		$finaldatein 	= $tanggalin[2]."-".$tanggalin[1]."-".$tanggalin[0];
		$tanggalout     = explode("-", $dateout);
		$finaldateout 	= $tanggalout[2]."-".$tanggalout[1]."-".$tanggalout[0];

		$value 			= array();

		$value[] 		= "nama_pasien				= '$name'";
		$value[] 		= "gender					= '$gender'";
		$value[] 		= "alamat_pasien			= '$address'";
		$value[] 		= "tanggal_datang			= '$finaldatein'";
		$value[] 		= "tanggal_pulang			= '$finaldateout'";
		$value[] 		= "user_id					= '$officer'";
		$value[] 		= "id_dokter				= '$doctor'";
		$value[] 		= "id_ruang					= '$care'";
		$value[] 		= "keluhan					= '$complaint'";
		$value[] 		= "id_status				= '$status'";

		$res  	 		= $this->_pasienModel->insert($value);
		return $res;

	}
}
?>