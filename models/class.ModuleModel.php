<?php
/**
 * Module Model
 *
 * Last updated: May 28, 2012, 05:22 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */
class ModuleModel extends Model
{
    public function __construct()
    {
        global $cfg;

        parent::__construct();

        $this->_table = $cfg['sys']['tblPrefix'] . '_sys_module';
    }

    public function getDetail($module)
    {
        return $this->find(array('filter' => array("module_name = '$module'")));
    }

    public function exist($module)
    {
        $detail = $this->getDetail($module);

        return ($detail) ? true : false;
    }

    public function getHash()
    {
        $res    = array();

        $data   = $this->findAll(array('orderby' => array('module_name'), 'filter' => array('module_is_primary = 0'),
                                       'sort' => 'ASC'));

        if (!empty($data)) {
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]->module_name] = $data[$i]->module_name;
            }
        }

        return $res;
    }

    public function getModuleList()
    {
        global $cfg;

        $sql = "SELECT
                            *
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_sys_module
                ORDER BY
                            module_name";
        $res = array();

        try {
            $this->_dbObj->execute($sql);

            $res = $this->_dbObj->getAll();
        } catch (DbException $e) {
            Error::store('Module', $e->getMessage());
        }

        return $res;
    }

    public function getTaskList($module)
    {
        global $cfg;

        $sql = "SELECT
                            *
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_sys_task
                WHERE
                            module_name = '$module'";
        $res = array();

        try {
            $this->_dbObj->execute($sql);

            $res = $this->_dbObj->getAll();
        } catch (DbException $e) {
            Error::store('Module', $e->getMessage());
        }

        return $res;
    }
}