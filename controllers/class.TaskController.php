<?php
/**
 * Task controller.
 *
 * Last updated: May 28, 2012, 05:12 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */
class TaskController extends Controller
{
	private $_taskModel;
	private $_moduleModel;

	public function __construct()
	{
		parent::__construct();

        $this->theme->pageTitle = 'Task';

		$this->_taskModel 		= $this->loadModel('Task');
		$this->_moduleModel		= $this->loadModel('Module');

	}

	public function index()
	{
		global $globalVar, $cfg;

		$this->requireLogin();
		$this->authorize('task', 'list');

		$this->loadClass('DBGrid', CLASS_DIR . '/blackcat/view/datagrid');
		$this->loadLib('lib');

        $sql = "SELECT
							*
				FROM
							" . $cfg['sys']['tblPrefix'] . "_sys_task
				JOIN
							" . $cfg['sys']['tblPrefix'] . "_sys_module
				USING(module_name)
				WHERE
							module_is_primary <> '1'";

		$grid = new DBGrid();

		$grid->setQuery($sql);
		$grid->setConnection($this->dbObj);
		$grid->enablePagination(true);
		$grid->enableDefaultButton(true);
		$grid->enableDefaultTool(true, true, false, true);
		$grid->setDefaultSortParam(array('order' => 'asc', 'sortby' => 'module_name'));
		$grid->addForm('task', 'delete.php');

		$grid->setPaginationParam(20);

		$grid->addColumn('cb');
		$grid->addColumn('no',      array('title' => 'No', 'number' => true, 'print' => true));
		$grid->addColumn('module',  array('title' => 'Module', 'sorting'=>true, 'print' => true));
		$grid->addColumn('name',    array('title' => 'Task', 'sorting'=>true, 'print' => true));
		$grid->addColumn('edit',    array('title' => '', 'value' => $globalVar['Edit'], 'colspan' => 2));
		$grid->addColumn('delete',  array('value' => $globalVar['Delete'] ));

		$grid->setCellFormat('cb',      array('TWidth' => '5%'));
		$grid->setCellFormat('no',      array('TWidth' => '5%'));
		$grid->setCellFormat('name',    array('TWidth' => '40%'));
		$grid->setCellFormat('module',  array('TWidth' => '45%'));
		$grid->setCellFormat('edit',    array('TWidth' => '5%'));

		$grid->setDBField('cb',         'task_id');
		$grid->setDBField('name',       'task_name');
		$grid->setDBField('module',     'module_name');

		$grid->setColumnLink('edit',    ROOT_URL . '/task/edit', array('id' => 'task_id'));
		$grid->setColumnLink('delete',  'javascript:deleteItem()', array('id' => 'task_id'));

		$grid->setColumnElement('cb', array('type' => 'InputCheck',
											'name' => 'cb',
											'attr' => array('OnClick' => "enableButton('1')")));

		$grid->setSearchParam('name', 'Task');
		$grid->setSearchParam('module', 'Module');

		$msg = $_GET['msg'];
		$err = $_GET['err'];

		if (!empty($msg)) {
			$msg = ($err != 1) ? boxSuccess($msg) : boxError($msg);
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Daftar Task</div>
						<div class='list-content'>". $grid->toString() ."</div>
					</div>
				   ";

		return $content;
	}

    public function add()
    {
		$this->requireLogin();
		$this->authorize('task', 'add');

		$this->theme->pageTitle = 'Tambah Task';

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');

		$form = new FormGroup('module', ROOT_URL . '/task/add');

		$form->addSelect('module', $this->_moduleModel->getHash(), '');
		$form->groupAsRow('Module');

		$form->addText('task', '', array('size' => '30'));
		$form->groupAsRow('Task');

		$form->addSubmit('add', ' Tambah ', array('class' => 'button primary', 'style' => 'margin-top:15px'));
		$form->addButton('cancel', '  Batal  ', array('onClick' => "javascript:history.back(-1)"));
		$form->groupAsRow('');

		$form->addRule('name', 'required');

		if (HTTP::getVar('add')) {
			if ($form->validateElement()) {
				if (($this->_insert())) {
					$err = 0;
					$msg = "Task has been added";

					HTTP::redirect(ROOT_URL . "/task");
				} else {
					$err = 1;
					$msg = boxError("Add task failed!");
				}
			}
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Tambah Task</div>
						<div class='list-content'>". $form->toString() ."</div>
					</div>
				   ";

		return $content;
    }

	public function edit()
	{
		$this->requireLogin();
		$this->authorize('task', 'edit');

		$this->theme->pageTitle = 'Ubah Task';

		$id 		= basename($_SERVER['REQUEST_URI']);
		$detail 	= $this->_taskModel->getDetail($id);

		if (!$detail) HTTP::alertRedirect("Invalid id", ROOT_URL . '/module');

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');

		$form = new FormGroup('module', ROOT_URL . "/task/edit/$id");

		$form->addSelect('module', $this->_moduleModel->getHash(), $detail->module_name);
		$form->groupAsRow('Module');

		$form->addText('task', $detail->task_name, array('size' => '30'));
		$form->groupAsRow('Task');

		$form->addSubmit('edit', ' Simpan ', array('class' => 'button primary', 'style' => 'margin-top:15px;'));
		$form->addButton('cancel', '  Batal  ', array('onClick' => "javascript:history.back(-1)"));

		$form->addHidden('id', $id);
		$form->groupAsRow('');

		$form->addRule('name', 'required');

		$msg  ='';

		if (HTTP::getVar('edit')) {
			if ($form->validateElement()) {
				if (($this->_update())) {
					$err = 0;
					$msg = "Task has been updated";

					HTTP::redirect(ROOT_URL . "/task");
				} else {
					$err = 1;
					$msg = boxError("Edit task failed!");
				}
			}
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Ubah Produk</div>
						<div class='list-content'>". $form->toString() ."</div>
					</div>
				   ";

		return $content;
	}

	public function delete()
	{
		global $cfg;

		$this->requireLogin();
		$this->authorize('task', 'delete');

		$html		= '';
		$cb			= HTTP::getVar('cb');
		$cb			= (preg_match('/:/', $cb)) ? explode(':', $cb) : array($cb);

		$res		= false;

		try {
			$this->dbObj->beginTrans();

			for ($i = 0; $i < sizeof($cb); $i++) {
				$cb[$i] = (int) $cb[$i];
				$this->dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_sys_task', array("task_id = '$cb[$i]'"));
			}

			$this->dbObj->commitTrans();

			$res = true;
		} catch (DbException $e) {
			$this->dbObj->rollbackTrans();
			Error::store('Task', $e->getMessage());
		}

		if ($res) $html = $this->index();

		die($html);
	}

	public function validate()
	{
		$name 	= $this->params[0];

		if (empty($name)) die('');

		$res    = ($this->_taskModel->exist($name)) ? 'already exists' : '';

		die($res);
	}

	private function _insert()
	{
		$module	 = addslashes(strip_tags(HTTP::getVar('module')));
		$task 	 = addslashes(strip_tags(HTTP::getVar('task')));

		if ($this->_taskModel->existInModule($module, $task)) {
			$this->sessObj->setVar('formError', "Task <i>$task</i> already exists in module <i>$module</i>");
			return false;
		}

		$value 	 = array();

		//$value[] = "task_id 		= DEFAULT";
		$value[] = "module_name 	= '$module'";
		$value[] = "task_name 		= '$task'";

		$res   	 = $this->_taskModel->insert($value);

		return $res;
	}

	private function _update()
	{
		$id   	 = HTTP::getVar('id');
		$module	 = addslashes(strip_tags(HTTP::getVar('module')));
		$task 	 = addslashes(strip_tags(HTTP::getVar('task')));

		$detail  = $this->_taskModel->getDetail($id);

		if ($detail->task_name != $task) {
			if ($this->_taskModel->existInModule($module, $task)) {
				$this->sessObj->setVar('formError', "Task <i>$task</i> already exists for module <i>$module</i>");
				return false;
			}
		}

		$res = $this->_taskModel->update(array("module_name = '$module'", "task_name = '$task'"),
										array("task_id = '$id'"));

		return $res;
	}
}
?>