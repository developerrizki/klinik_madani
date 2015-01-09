<?php
/**
* 
*/
class DoctorController extends Controller
{
	private $_doctorModel, $_id;

	function __construct()
	{
		parent::__construct();

		$this->_doctorModel 	= $this->loadModel('Doctor');
		$this->_id 				= basename($_SERVER['REQUEST_URI']); 
	}

	public function index()
	{
		global $cfg;
		global $globalVar;
		global $sessObj;

		$this->requireLogin();
		$this->authorize('doctor', 'list');

		// load Class Grid
		$this->loadClass('DBGrid', CLASS_DIR . '/blackcat/view/datagrid');
		$this->loadLib('lib');

		// script sql
		$sql = "SELECT * FROM dp_dokter";

		// setting grid
		$grid 		= new DBGrid();
		$grid->setQuery($sql);
		$grid->setConnection($this->dbObj);
		$grid->enablePagination(true);
		$grid->setDefaultSortParam(array('order' => 'asc', 'sortby' => 'id_dokter'));
		$grid->enableDefaultTool(true, true, false, true);
		$grid->setPaginationParam(10);

		// setting column
		$grid->addColumn('id',			array('title' => 'ID Dokter', 'sorting' => true, 'print' =>true));
		$grid->addColumn('name',		array('title' => 'Nama Dokter', 'sorting' => true, 'print' =>true));
		$grid->addColumn('address',		array('title' => 'Alamat', 'sorting' => true, 'print' =>true));
		$grid->addColumn('spesial',		array('title' => 'Spesialisasi', 'sorting' => true, 'print' =>true));
		$grid->addColumn('edit',		array('title' => 'Action', 'value' => $globalVar['Edit'], 'colspan' => 2, 'task' => 'edit'));
		$grid->addColumn('delete',		array('value' => $globalVar['Delete'], 'task' => 'delete'));

		// setting cell format
		$grid->setCellFormat('id', 		array('TWidth' => '10%'));
		$grid->setCellFormat('name',	array('TWidth' => '30%'));
		$grid->setCellFormat('address',	array('TWidth' => '30%'));
		$grid->setCellFormat('spesial', array('TWidth' => '25%'));
		$grid->setCellFormat('edit', 	array('TWidth' => '5%'));

		// setting set value
		$grid->setDBField('id',			'id_dokter');
		$grid->setDBField('name',		'nama_dokter');
		$grid->setDBField('address',	'alamat_dokter');
		$grid->setDBField('spesial',	'spesialisasi');


		$grid->setColumnLink('edit',    ROOT_URL . '/dokter/edit', array('id' => 'id_pasien'));
		$grid->setColumnLink('delete',  'javascript:deleteItem()', array('id' => 'id_pasien'));
		
		$grid->setSearchParam('id', 'ID Dokter');
		$grid->setSearchParam('name', 'Nama Dokter');

		$content = $grid->toString();

		$notif ='';
		$title = $this->sessObj->getVar('title');
		
		if (!empty($title)) {
			$notif = $this->sessObj->getVar('message');
			$this->sessObj->setVar('title','');
		}		

		$this->set('message',$notif);
		$this->set('content',$content);
		return $this->getHTML('dokter/home');		
	}

	public function add(){
		global $cfg, $sessObj, $globalVar;

		$this->requireLogin();
		$this->authorize('doctor','add');

		// Load Class Form
		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');
		$this->loadLib('lib');

		// setting form
		$form = new FormGroup('doctor_add', ROOT_URL . '/doctor/add');

		$form->addText('id','',				array('size' => '40', 'placeholder' => 'ID Dokter'));
		$form->groupAsRow('ID Dokter');

		$form->addText('name','',			array('size' => '40'));
		$form->groupAsRow('Nama Dokter');
		
		$form->addTextArea('address','',	array('size' => '100', 'style' => 'width:300px;'));
		$form->groupAsRow('Alamat');
		
		$form->addText('spesial','',		array('size' => '40'));
		$form->groupAsRow('Spesialisasi');

		$form->addRule('id', 	'required');
		$form->addRule('name', 	'required');
		$form->addRule('address', 	'required');
		$form->addRule('spesial', 	'required');

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
                       Dokter berhasil ditambahkan
                    </div>";


					$this->sessObj->setVar('message',$msg);
					$this->sessObj->setVar('title','Berhasil');
					HTTP::redirect(ROOT_URL . "/doctor");
				}
				else {
					$err = 1;
					$msg = "
					<div class='alert alert-danger alert-dismissable'>
                       <button type'button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                       Dokter gagal ditambahkan
                    </div>";
				}
			}
		}

		$content .= $form->toString();
	
		$this->set('message',$msg);
		$this->set('content',$content);
		return $this->getHTML('dokter/tambah_dokter');

	}

	private function _insert(){

		$id 		= addslashes($this->postParam('id'));
		$name 		= addslashes($this->postParam('name'));
		$address	= addslashes($this->postParam('address'));
		$spesial 	= addslashes($this->postParam('spesial'));

		$value = array();

		$value[] = "id_dokter			='$id'";
		$value[] = "nama_dokter			='$name'";
		$value[] = "alamat_dokter		='$address'";
		$value[] = "spesialisasi		='$spesial'";

		$res = $this->_doctorModel->insert($value);

		return $res;
	}
}
?>