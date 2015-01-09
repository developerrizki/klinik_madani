<?php
/**
* 
*/
class RawatController extends Controller
{
	private $_rawatModel, $_id;
	
	function __construct()
	{
		parent::__construct();

		$this->_rawatModel 	= $this->loadModel('Rawat');
		$this->_id 			= basename($_SERVER['REQUEST_URI']);
	}

	public function index()
	{
		
		global $cfg;
		global $globalVar;
		global $sessObj;

		$this->requireLogin();
		$this->authorize('rawat','list');

		// setting load Class Grid
		$this->loadClass('DBGrid', CLASS_DIR . '/blackcat/view/datagrid');
		$this->loadLib('lib');

		// script sql
		$sql = "SELECT * FROM dp_ruang";

		// setting grid
		$grid 		= new DBGrid();
		$grid->setQuery($sql);
		$grid->setConnection($this->dbObj);
		$grid->enablePagination(true);
		$grid->setDefaultSortParam(array('order' => 'asc', 'sortby' => 'id_ruang'));
		$grid->enableDefaultTool(true, true, false, true);
		$grid->setPaginationParam(10);

		// setting column
		$grid->addColumn('id',				array('title' => 'ID Ruang', 'size' => '40'));
		$grid->addColumn('name',			array('title' => 'Nama Ruang', 'size' => '40'));
		$grid->addColumn('build',			array('title' => 'Nama Gedung', 'size' => '40'));
		$grid->addColumn('edit',			array('title' => 'Action', 'value' => $globalVar['Edit'], 'colspan' => 2, 'task' => 'edit'));
		$grid->addColumn('delete',			array('value' => $globalVar['Delete'], 'task' => 'delete'));

		// setting format cell
		$grid->setCellFormat('id', 			array('TWidth' => '10%'));
		$grid->setCellFormat('name', 		array('TWidth' => '40%'));
		$grid->setCellFormat('build', 		array('TWidth' => '40%'));
		$grid->setCellFormat('edit', 		array('TWidth' => '10%'));

		// SETTING DB Field
		$grid->setDBField('id',		'id_ruang');
		$grid->setDBField('name',	'nama_ruang');
		$grid->setDBField('build',	'nama_gedung');

		$grid->setColumnLink('edit',    ROOT_URL . '/rawat/edit', array('id' => 'id_ruang'));
		$grid->setColumnLink('delete',  'javascript:deleteItem()', array('id' => 'id_ruang'));
		
		$grid->setSearchParam('name', 'Nama Ruang');

		$content = $grid->toString();
		
		$notif ='';
		$title = $this->sessObj->getVar('title');
		
		if (!empty($title)) {
			$notif = $this->sessObj->getVar('message');
			$this->sessObj->setVar('title','');
		}	

		$this->set('message',$notif);
		$this->set('content',$content);

		return $this->getHTML('rawat/home');
	}

	public function add()
	{
		global $cfg, $sessObj, $globalVar;

		$this->requireLogin();
		
	}
}
?>