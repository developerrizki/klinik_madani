<?php
/**
 * Group controller.
 *
 * Last updated: May 25, 2012, 03:44 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */
class GroupController extends Controller
{
	private $_groupModel;

	public function __construct()
	{
		parent::__construct();

        $this->theme->pageTitle = 'Group';
		$this->_groupModel 		= $this->loadModel('Group');
		$this->theme->setRootTemplate('login');
	}

	public function index()
	{
		global $globalVar, $cfg;

		$this->requireLogin();
		$this->authorize('group', 'list');

		$this->loadClass('DBGrid', CLASS_DIR . '/blackcat/view/datagrid');
		$this->loadLib('lib');

        $sql = "SELECT
							*
				FROM
							" . $cfg['sys']['tblPrefix'] . "_sys_group
				WHERE
							group_is_super = '0'";

		$grid = new DBGrid();

		$grid->setQuery($sql);
		$grid->setConnection($this->dbObj);
		$grid->enablePagination(true);
		$grid->enableDefaultButton(true);
		$grid->enableDefaultTool(true, true, false, true);
		$grid->setDefaultSortParam(array('order' => 'asc', 'sortby' => 'group_name'));
		$grid->addForm('group', 'delete.php');

		$grid->setPaginationParam(20);

		$grid->addColumn('cb');
		$grid->addColumn('no',      array('title' => 'No', 'number' => true, 'print' => true));
		$grid->addColumn('name',    array('title' => 'Grup', 'sorting'=>true, 'print' => true));
		$grid->addColumn('desc',    array('title' => 'Deskripsi', 'sorting'=>true, 'print' => true));
		$grid->addColumn('memb',    array('title' => 'Anggota', 'sorting'=>true, 'print' => true));
		$grid->addColumn('edit',    array('title' => '', 'value' => $globalVar['cfgEdit'], 'colspan' => 2));
		$grid->addColumn('delete',  array('value' => $globalVar['cfgDelete'] ));

		$grid->setCellFormat('cb',      array('TWidth' => '5%'));
		$grid->setCellFormat('no',      array('TWidth' => '5%'));
		$grid->setCellFormat('name',    array('TWidth' => '15%'));
		$grid->setCellFormat('desc',    array('TWidth' => '30%'));
		$grid->setCellFormat('memb',    array('TWidth' => '40%'));
		$grid->setCellFormat('edit',    array('TWidth' => '5%'));

		$grid->setDBField('cb',         'group_id');
		$grid->setDBField('name',       'group_name');
		$grid->setDBField('desc',       'group_description');
		$grid->setDBField('memb',       'group_id');

		$grid->setColumnLink('edit',    ROOT_URL . '/group/edit', array('id' => 'group_id'));
		$grid->setColumnLink('delete',  'javascript:deleteItem()', array('id' => 'group_id'));

		$grid->setColumnElement('cb', array('type' => 'InputCheck',
											'name' => 'cb',
											'attr' => array('OnClick' => "enableButton('1')")));

		$grid->setColumnFunction('memb',    'getUserList');

		$grid->setSearchParam('name', 'Group');

		$msg = $_GET['msg'];
		$err = $_GET['err'];

		if (!empty($msg)) {
			$msg = ($err != 1) ? boxSuccess($msg) : boxError($msg);
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Daftar Group</div>
						<div class='list-content'>". $grid->toString() ."</div>
					</div>
				   ";

		return $content;
	}

    public function add()
    {
		$this->requireLogin();
		$this->authorize('group', 'add');

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');

		$form = new FormGroup('groupadd', ROOT_URL . '/group/add');

		$form->addText('name', '', array('size' => '25'));
		$form->groupAsRow('Grup');

		$form->addText('desc', '', array('size' => '50'));
		$form->groupAsRow('Deskripsi');

		$form->addSubmit('add', ' Tambah ', array('class' => 'button primary', 'style' => 'margin-top:15px;'));
		$form->addButton('cancel', '  Batal  ', array('onClick' => "javascript:history.back(-1)"));
		$form->groupAsRow('');

		$form->addRule('name', 'required');

		if (HTTP::getVar('add')) {
			if ($form->validateElement()) {
				if (($this->_insert())) {
					$err = 0;
					$msg = "Group has been added";

					HTTP::redirect(ROOT_URL . "/group");
				} else {
					$err = 1;
					$msg = boxError("Add group failed!");
				}
			}
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Tambah Grup</div>
						<div class='list-content'>". $form->toString() ."</div>
					</div>
				   ";

		return $content;
    }

	public function edit()
	{
		$this->requireLogin();
		$this->authorize('group', 'edit');

		$id 	= $this->params[0];
		$detail = $this->_groupModel->getDetail($id);

		if (!$detail) HTTP::alertRedirect("Invalid id", ROOT_URL . '/group');

		$this->loadClass('FormGroup', CLASS_DIR . '/blackcat/view/form');

		$form = new FormGroup('groupadd', ROOT_URL . "/group/edit/$id");

		$form->addText('name', $detail->group_name, array('size' => '25'));
		$form->groupAsRow('Grup');

		$form->addText('desc', $detail->group_description, array('size' => '50'));
		$form->groupAsRow('Deskripsi');

		$form->addSubmit('edit', ' Simpan ', array('class' => 'button primary', 'style' => 'margin-top:15px;'));
		$form->addButton('cancel', '  Batal  ', array('onClick' => "javascript:history.back(-1)"));

		$form->addHidden('id', $id);
		$form->groupAsRow('');

		$form->addRule('name', 'required');

		if (HTTP::getVar('edit')) {
			if ($form->validateElement()) {
				if (($this->_update())) {
					$err = 0;
					$msg = "Group has been updated";

					HTTP::redirect(ROOT_URL . "/group");
				} else {
					$err = 1;
					$msg = boxError("Edit group failed!");
				}
			}
		}

		$content = "
					<div class='list'>
						<div class='list-title'>Ubah Grup</div>
						<div class='list-content'>". $form->toString() ."</div>
					</div>
				   ";

		return $content;
	}

	public function delete()
	{
		global $cfg;

		$this->requireLogin();
		$this->authorize('group', 'delete');

		$html		= '';
		$cb			= HTTP::getVar('cb');
		$cb			= (preg_match('/:/', $cb)) ? explode(':', $cb) : array($cb);

		$res		= false;

		try {
			$this->dbObj->beginTrans();

			for ($i = 0; $i < sizeof($cb); $i++) {
				$this->dbObj->deleteRecord($cfg['sys']['tblPrefix'] . '_sys_group', array("group_id = '$cb[$i]'"));
			}

			$this->dbObj->commitTrans();

			$res = true;
		} catch (DbException $e) {
			$this->dbObj->rollbackTrans();
			Error::store('Group', $e->getMessage());
		}

		if ($res) $html = $this->index();

		die($html);
	}

	private function _insert()
	{
		$name 		= addslashes($this->postParam('name'));
		$desc 		= addslashes($this->postParam('desc'));

		$value 		= array();

		$value[] 	= "group_id 			= DEFAULT";
		$value[] 	= "group_name 			= '$name'";
		$value[] 	= "group_description 	= '$desc'";
		$value[] 	= "group_is_super 		= '0'";

		$res  	 	= $this->_groupModel->insert($value);

		return $res;
	}

	private function _update()
	{
		$id   		= $this->postParam('id');
		$name 		= addslashes($this->postParam('name'));
		$desc 		= addslashes($this->postParam('desc'));

		$res 		= $this->_groupModel->update(array("group_name = '$name'", "group_description = '$desc'"),
											   array("group_id = '$id'"));

		return $res;
	}

}
?>