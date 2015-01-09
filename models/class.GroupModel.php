<?php
/**
 * Group Model
 *
 * Last updated: May 28, 2012, 05:22 PM
 *
 * @author Lorensius W. L. T <lorenz@londatiga.net>
 *
 */

class GroupModel extends Model
{
    public function __construct()
    {
        global $cfg;

        parent::__construct();

        $this->_table = $cfg['sys']['tblPrefix'] . '_sys_group';
    }

    public function getHash()
    {
        $res  = array();
        $data = $this->findAll(array('orderby' => array('group_name'), 'sort' => 'ASC'));

        if (!empty($data)) {
            for ($i = 0; $i < sizeof($data); $i++) {
                $res[$data[$i]->group_id] = $data[$i]->group_name;
            }
        }

        return $res;
    }

    public function getUserList($id)
    {
        global $cfg;

        $sql = "SELECT
                            *
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_sys_user_group
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_sys_user
                USING(user_id)
                WHERE
                            group_id = '$id'";

        $res = array();

        try {
            $this->_dbObj->query($sql);

            $res = ($this->_dbObj->getNumRows()) ? $this->_dbObj->fetchAll() : array();
        }  catch (DAALException $e) {
            Error::store('Group', $e->getMessage());
        }

        return $res;
    }

    public function getUserHash($id)
    {
        global $cfg;

        $sql = "SELECT
                            *
                FROM
                            " . $cfg['sys']['tblPrefix'] . "_sys_user_group
                JOIN
                            " . $cfg['sys']['tblPrefix'] . "_sys_user
                USING(user_id)
                WHERE
                            group_id = '$id'";

        $res = array();

        try {
            $this->_dbObj->query($sql);

            if ($this->_dbObj->getNumRows()) {
                $data = $this->_dbObj->fetchAll();

                for ($i = 0; $i < sizeof($data); $i++) {
                    $res[$data[$i]->user_id] = $data[$i]->user_full_name;
                }
            }
        } catch (DAALException $e) {
            Error::store('Group', $e->getMessage());
        }

        return $res;
    }

    public function getDetail($id)
    {
        return $this->find(array('filter' => "group_id = '$id'"));
    }
}