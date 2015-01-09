<?php
/**
 * Role controller.
 *
 * Last updated: May 28, 2012, 05:12 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */
class RoleController extends Controller
{
	private $_roleModule;
	private $_moduleModule;
	private $_groupModule;

	public function __construct()
	{
		parent::__construct();

        $this->theme->pageTitle = 'Role';

		$this->_roleModule 			= $this->loadModel('Role');
		$this->_moduleModule 		= $this->loadModel('Module');
		$this->_groupModule 		= $this->loadModel('Group');
	}

	public function index()
	{
		global $globalVar, $cfg;

		$this->requireLogin();
		$this->authorize('role', 'list');

		$this->loadClass('DBGrid', CLASS_DIR . '/blackcat/view/datagrid');
		$this->loadLib('lib');

        $sql = "SELECT
							*
				FROM
							" . $cfg['sys']['tblPrefix'] . "_sys_group_task
				JOIN
							" . $cfg['sys']['tblPrefix'] . "_sys_task
				USING(task_id)
				JOIN
							" . $cfg['sys']['tblPrefix'] . "_sys_group
				USING(group_id)
				JOIN
							" . $cfg['sys']['tblPrefix'] . "_sys_module
				USING(module_name)
				WHERE
							module_is_primary = '0'
				";

		$grid = new DBGrid();

		$grid->setQuery($sql);
		$grid->setConnection($this->dbObj);
		$grid->enablePagination(true);
		$grid->enableDefaultButton(true);
		$grid->enableDefaultTool(true, true, false, true);
		$grid->setDefaultSortParam(array('order' => 'asc', 'sortby' => 'module_name'));
		$grid->addForm('group', 'delete.php');

		$grid->setPaginationParam(25);

		$grid->addColumn('cb');
		$grid->addColumn('no',      array('title' => 'No', 'number' => true, 'print' => true));
		$grid->addColumn('group',   array('title' => 'Grup', 'sorting'=>true, 'print' => true));
		$grid->addColumn('module',  array('title' => 'Module', 'sorting'=>true, 'print' => true));
		$grid->addColumn('task',    array('title' => 'Task', 'sorting'=>true, 'print' => true, 'sorting' => false));
		$grid->addColumn('delete',  array('title' => '', 'value' => $globalVar['Delete']));

		$grid->setCellFormat('cb',      array('TWidth' => '5%'));
		$grid->setCellFormat('no',      array('TWidth' => '5%'));
		$grid->setCellFormat('group',   array('TWidth' => '25%'));
		$grid->setCellFormat('module',  array('TWidth' => '30%'));
		$grid->setCellFormat('task',    array('TWidth' => '30%'));
		$grid->setCellFormat('delete',  array('TWidth' => '5%'));

		$grid->setDBField('cb',         'id');
		$grid->setDBField('group',      'group_name');
		$grid->setDBField('module',     'module_name');
		$grid->setDBField('task',       'task_name');

		$grid->setColumnLink('delete',  'javascript:deleteItem()', array('id' => 'id'));

		$grid->setColumnElement('cb', array('type' => 'InputCheck',
											'name' => 'cb',
											'attr' => array('OnClick' => "enableButton('1')")));

		$grid->setSearchParam('group', 'Group');
		$grid->setSearchParam('module', 'Module');
		$grid->setSearchParam('task', 'Task');

		$msg = $_GET['msg'];
		$err = $_GET['err'];

		if (!empty($msg)) {
			$msg = ($err != 1) ? boxSuccess($msg) : boxError($msg);
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Daftar Role</div>
						<div class='list-content'>". $grid->toString() ."</div>
					</div>
				   ";

		return $content;
	}

    public function add()
    {
		$this->requireLogin();
		$this->authorize('role', 'add');

		$this->theme->pageTitle = 'Tambah Role';

		$modules 	= array();
		$getModule 	= $this->_moduleModule->getModuleList();

		$modules[0] = "Select Module";

		for($i = 0; $i < sizeof($getModule) ; $i ++){
			$modules[$getModule[$i]->module_name] = $getModule[$i]->module_name;
		} 

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');

		$form = new FormGroup('role', ROOT_URL . '/role/add');

		$form->addSelect('group', $this->_groupModule->getHash(false), '');
		$form->groupAsRow('Group');

		$form->addSelect('module', $modules, '', array('onChange' => 'getTask()'));
		$form->groupAsRow('Module');

		$form->addSelect('task', '', '', array('multiple' => true));
		$form->groupAsRow('Task');

		$form->addSubmit('add', ' Save ', array('class' => 'button primary', 'style' => 'margin-top:10px'));
		$form->addButton('cancel', '  Cancel  ', array('onClick' => "javascript:history.back(-1)"));
		$form->groupAsRow('');

		$form->addRule('group', 'required');
		$form->addRule('task', 'required');

		if (HTTP::getVar('add')) {
			if ($form->validateElement()) {
				if (($this->_insert())) {
					$err = 0;
					$msg = "Role has been added";

					HTTP::redirect(ROOT_URL . "/role");
				} else {
					$err = 1;
					$msg = boxError("Add role failed!");
				}
			}
		}

		echo "<script language='javascript' src='". ROOT_URL ."/jscript/role/ajax.js'></script>";

		$content = "
					<div class='list'>
						<div class='list-title'>Tambah Role</div>
						<div class='list-content'>". $form->toString() ."</div>
					</div>
				   ";

		return $content;
    }

	public function delete()
	{
		global $cfg;

		$this->requireLogin();
		$this->authorize('role', 'delete');

		$html		= '';
		$cb			= HTTP::getVar('cb');
		$cb			= (preg_match('/:/', $cb)) ? explode(':', $cb) : array($cb);

		$res		= false;

		try {
			$this->dbObj->beginTrans();

			for ($i = 0; $i < sizeof($cb); $i++) {
				$cb[$i] = (int) $cb[$i];
				$this->dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_sys_group_task', array("id = '$cb[$i]'"));
			}

			$this->dbObj->commitTrans();

			$res = true;
		} catch (DbException $e) {
			$this->dbObj->rollbackTrans();
			Error::store('Role', $e->getMessage());
		}

		if ($res) $html = $this->index();

		die($html);
	}

	public function gettask()
	{
		$module = basename($_SERVER['REQUEST_URI']);

		$module = str_replace("%20", " ", $module);

		if (empty($module)) die('');

		$list 	= $this->_moduleModule->getTaskList($module);

		$res	= '';

		for ($i = 0; $i < sizeof($list); $i++) {
			$res .= "<option value='" . $list[$i]->task_id . "'>" . $list[$i]->task_name . "</option>";
		}

		die($res);
		
	}

	private function _insert()
	{
		$group 	 	= HTTP::getVar('group');
		$task 	 	= HTTP::getVar('task');

		$num		= 0;

		for ($i = 0; $i < sizeof($task); $i++) {
			if ($this->_roleModule->exist($group, $task[$i])) continue;

			$value 	 = array();

			$value[] = "id 			= DEFAULT";
			$value[] = "group_id	= '$group'";
			$value[] = "task_id		= '$task[$i]'";

			$res = $this->_roleModule->insert($value);

			if ($res) $num++;
		}

		return $num;
	}
}
?>