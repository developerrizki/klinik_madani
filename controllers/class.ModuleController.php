<?php
/**
 * Module controller.
 *
 * Last updated: May 28, 2012, 05:12 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */
class ModuleController extends Controller
{
	private $_moduleModel;

	public function __construct()
	{
		parent::__construct();

        $this->theme->pageTitle = "Module";
		$this->_moduleModel 	= $this->loadModel('Module');

	}

	public function index()
	{
		global $globalVar, $cfg;

		$this->requireLogin();
		$this->authorize('module', 'list');

		$this->loadClass('DBGrid', CLASS_DIR . '/blackcat/view/datagrid');
		$this->loadLib('lib');

        $sql = "SELECT
							*
				FROM
							rms_sys_module
				WHERE
							module_is_primary <> '1'";

		$grid = new DBGrid();

		$grid->setQuery($sql);
		$grid->setConnection($this->dbObj);
		$grid->enablePagination(true);
		$grid->enableDefaultButton(true);
		$grid->enableDefaultTool(true, true, false, true);
		$grid->setDefaultSortParam(array('order' => 'asc', 'sortby' => 'module_name'));
		$grid->addForm('module', 'delete.php');

		$grid->setPaginationParam(20);

		$grid->addColumn('cb');
		$grid->addColumn('no',      array('title' => 'No', 'number' => true, 'print' => true));
		$grid->addColumn('name',    array('title' => 'Module', 'sorting'=>true, 'print' => true));
		$grid->addColumn('desc',    array('title' => 'Deskripsi', 'sorting'=>true, 'print' => true));
		$grid->addColumn('task',    array('title' => 'Tasks', 'sorting'=>true, 'print' => true, 'sorting' => false));
		$grid->addColumn('edit',    array('value' => $globalVar['Edit'], 'colspan' => 2));
		$grid->addColumn('delete',  array('value' => $globalVar['Delete'] ));

		$grid->setCellFormat('cb',      array('TWidth' => '5%'));
		$grid->setCellFormat('no',      array('TWidth' => '5%'));
		$grid->setCellFormat('name',    array('TWidth' => '20%'));
		$grid->setCellFormat('desc',    array('TWidth' => '25%'));
		$grid->setCellFormat('task',    array('TWidth' => '40%'));
		$grid->setCellFormat('edit',    array('TWidth' => '5%'));

		$grid->setDBField('cb',         'module_name');
		$grid->setDBField('name',       'module_name');
		$grid->setDBField('desc',       'module_description');
		$grid->setDBField('task',       'module_name');

		$grid->setColumnLink('edit',    ROOT_URL . '/module/edit', array('id' => 'module_name'));
		$grid->setColumnLink('delete',  'javascript:deleteItem()', array('id' => 'module_name'));

		$grid->setColumnElement('cb', array('type' => 'InputCheck',
											'name' => 'cb',
											'attr' => array('OnClick' => "enableButton('1')")));

		$grid->setColumnFunction('task',    'getTaskList');

		$grid->setSearchParam('name', 'Nama Module');

		$msg = $_GET['msg'];
		$err = $_GET['err'];

		if (!empty($msg)) {
			$msg = ($err != 1) ? boxSuccess($msg) : boxError($msg);
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Daftar Module</div>
						<div class='list-content'>". $grid->toString() ."</div>
					</div>
				   ";

		return $content;
	}

    public function add()
    {
		$this->requireLogin();
		$this->authorize('module', 'add');

		$this->theme->pageTitle = 'Tambah Module';

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');

		$form = new FormGroup('module', ROOT_URL . '/module/add');

		$form->addText('name', '', array('size' => '30', 'onKeyUp' => 'checkName()'));
		$form->groupAsRow('Module');

		$form->addText('desc', '', array('size' => '50'));
		$form->groupAsRow('Deskripsi');

		$form->addSubmit('add', ' Tambah ', array('class' => 'button primary', 'style' => 'margin-top:15px'));
		$form->addButton('cancel', '  Batal  ', array('onClick' => "javascript:history.back(-1)"));
		$form->groupAsRow('');

		$form->addRule('name', 'required');

		if (HTTP::getVar('add')) {
			if ($form->validateElement()) {
				if (($this->_insert())) {
					$err = 0;
					$msg = "Module has been added";

					HTTP::redirect(ROOT_URL . "/module");
				} else {
					$err = 1;
					$msg = boxError("Add module failed!");
				}
			}
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Tambah Module</div>
						<div class='list-content'>". $form->toString() ."</div>
					</div>
				   ";

		return $content;
    }

	public function edit()
	{
		$this->requireLogin();
		$this->authorize('module', 'edit');

		$this->theme->pageTitle = 'Ubah Module';

		$id 		= basename($_SERVER['REQUEST_URI']);
		$id 		= str_replace("%20", " ", $id);
		$detail 	= $this->_moduleModel->getDetail($id);

		if (!$detail) HTTP::alertRedirect("Invalid id", ROOT_URL . '/module');

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');

		$form = new FormGroup('module', ROOT_URL . "/module/edit/$id");

		$form->addText('name', $detail->module_name, array('size' => '30', 'onKeyUp' => 'checkName()'));
		$form->groupAsRow('Module');

		$form->addText('desc', $detail->module_description, array('size' => '50'));
		$form->groupAsRow('Description');

		$form->addSubmit('edit', ' Simpan ', array('class' => 'button primary', 'style' => 'margin-top:15px'));
		$form->addButton('cancel', '  Batal  ', array('onClick' => "javascript:history.back(-1)"));

		$form->addHidden('id', $id);
		$form->groupAsRow('');

		$form->addRule('name', 'required');

		$msg  ='';

		if (HTTP::getVar('edit')) {
			if ($form->validateElement()) {
				if (($this->_update())) {
					$err = 0;
					$msg = "Module has been updated";

					HTTP::redirect(ROOT_URL . "/module");
				} else {
					$err = 1;
					$msg = boxError("Edit module failed!");
				}
			}
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Ubah Module</div>
						<div class='list-content'>". $form->toString() ."</div>
					</div>
				   ";

		return $content;
	}

	public function delete()
	{
		global $cfg;

		$this->requireLogin();
		$this->authorize('module', 'delete');

		$html		= '';
		$cb			= HTTP::getVar('cb');
		$cb			= (preg_match('/:/', $cb)) ? explode(':', $cb) : array($cb);

		$res		= false;

		try {
			$this->dbObj->beginTrans();

			for ($i = 0; $i < sizeof($cb); $i++) {
				
				$this->dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_sys_module', array("module_name = '$cb[$i]'"));
			}

			$this->dbObj->commitTrans();

			$res = true;
		} catch (DbException $e) {
			$this->dbObj->rollbackTrans();
			Error::store('Module', $e->getMessage());
		}

		if ($res) $html = $this->index();

		die($html);
	}

	public function validate()
	{
		$name 	= $this->params[0];

		if (empty($name)) die('');

		$res    = ($this->_moduleModel->exist($name)) ? 'already exists' : '';

		die($res);
	}

	private function _insert()
	{
		$name 	 = addslashes(strip_tags(HTTP::getVar('name')));
		$desc 	 = addslashes(strip_tags(HTTP::getVar('desc')));

		if ($this->_moduleModel->exist($name)) {
			$this->sessObj->setVar('formError', 'Module already exist');
			return false;
		}

		$value 	 = array();

		$value[] = "module_name 			= '$name'";
		$value[] = "module_description 		= '$desc'";
		$value[] = "module_is_primary 		= '0'";

		$res   	 = $this->_moduleModel->insert($value);

		return $res;
	}

	private function _update()
	{
		$id 	 = basename($_SERVER['REQUEST_URI']);
		$id 	 = str_replace("%20", " ", $id);
		$name 	 = addslashes(strip_tags(HTTP::getVar('name')));
		$desc 	 = addslashes(strip_tags(HTTP::getVar('desc')));

		if ($id != $name) {
			if ($this->_moduleModel->exist($name)) {
				$this->sessObj->setVar('formError', 'Module already exist');
				return false;
			}
		}

		$res = $this->_moduleModel->update(array("module_name = '$name'", "module_description = '$desc'"),
										array("module_name = '$id'"));

		return $res;
	}
}
?>