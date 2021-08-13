<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/**
 * Model of My Plugin
 */
class MyPlugin extends DAO
{
    private static $instance;

    /**
    * Singleton Pattern
    */
    public static function newInstance()
    {
        if(!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    function __construct()
    {
        parent::__construct();
    }

    /**
    * Call the names of publig's sql tables
    */
    public function getTable_table_one()
    {
        return DB_TABLE_PREFIX.'t_plugin_table_one';
    }

    public function getTable_table_two()
    {
        return DB_TABLE_PREFIX.'t_plugin_table_two';
    }

    /**
    * Import tables to database using sql file
    */
    public function import($file)
    {
        $sql  = file_get_contents($file);

        if(!$this->dao->importSQL($sql)) {
            throw new Exception("Error importSQL::MyPlugin".$file);
        }
    }

    /**
    * Config the plugin in osclass database, settings the preferences table 
    * and import sql tables of plugin from struct.sql
    */
    public function install()
    {
        $this->import(MY_PLUGIN_PATH.'struct.sql');
        osc_set_preference('version', '1.0.0', MY_PLUGIN_PREF, 'STRING');
        osc_set_preference('field_one', '1', MY_PLUGIN_PREF, 'BOOLEAN');
        osc_set_preference('field_two', '0', MY_PLUGIN_PREF, 'BOOLEAN');
        osc_set_preference('field_three', '', MY_PLUGIN_PREF, 'STRING');
        osc_run_hook('my_plugin_install');
    }

    /**
    * Delete all fields from the 'preferences' table and also delete all tables of plugin
    */
    public function uninstall()
    {
        $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_table_two()));
        $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_table_one()));
        Preference::newInstance()->delete(array('s_section' => MY_PLUGIN_PREF));
        osc_run_hook('my_plugin_uninstall');
    }

    /**
    * Create/Update register
    */
    public function addRegister($data)
    {
        // Create
        if (!$data['pk_i_id']) {
            return $this->dao->insert($this->getTable_table_one(), $data);

        // Update
        } else {
            return $this->dao->update($this->getTable_table_one(), $data, array('pk_i_id' => $data['pk_i_id']));
        }
    }

    /**
     * Get all positions
     *
     * @access public
     * @return array
     */
    public function getAllRegisters() {
        $this->dao->select('*');
        $this->dao->from($this->getTable_table_one());
        $result = $this->dao->get();
        if($result) {
            return $result->result();
        }
        return array();
    }

    /**
     * Search registers
     *
     * This function is for search with parameters in the CrudDataTable.php
     *
     * @access public
     * @param array $params Is a array variable witch containt all parameters for the search and pagination
     * @return array
     */
    public function registers($params)
    {
        $start          = (isset($params['start']) && $params['start'] != '' ) ? $params['start']: 0;
        $limit          = (isset($params['limit']) && $params['limit'] != '' ) ? $params['limit']: 10;

        $sort           = (isset($params['sort']) && $params['sort'] != '') ? $params['sort'] : '';
        $sort           = strtolower($sort);

        switch ($sort) {
            case 'date':
                $sort = 'dt_date';
                break;

            case 'pub_date':
                $sort = 'dt_pub_date';
                break;

            default:
                $sort = 'dt_date';
                break;
        }

        $direction      = (isset($params['direction']) && $params['direction'] == 'ASC') ? $params['direction'] : 'DESC';
        $direction      = strtoupper($direction);

        $date           = (isset($params['dt_date']) && $params['dt_date'] != '') ? $params['dt_date'] : '';
        $dateControl    = (isset($params['date_control']) && $params['date_control']!='') ? $params['date_control'] : '';
        $url            = (isset($params['s_url']) && $params['s_url'] != '') ? $params['s_url'] : '';
        $status         = (isset($params['b_active']) && $params['b_active'] != '') ? $params['b_active'] : '';

        $this->dao->select('*');
        $this->dao->from($this->getTable_table_one());
        $this->dao->orderBy($sort, $direction);

        if ($date != '') {
            switch ($dateControl) {
                case 'equal':
                    $this->dao->where('dt_date', $date);
                    break;

                case 'greater':
                    $this->dao->where("dt_date > '$date'");
                    break;

                case 'greater_equal':
                    $this->dao->where("dt_date >= '$date'");
                    break;

                case 'less':
                    $this->dao->where("dt_date < '$date'");
                    break;

                case 'less_equal':
                    $this->dao->where("dt_date <= '$date'");
                    break;

                case 'not_equal':
                    $this->dao->where("dt_date != '$date'");
                    break;
                
                default:
                    $this->dao->where('dt_date', $date);
                    break;
            }
        }
        
        if ($status != '') {
            if ($status == 0) {
                $this->dao->where('b_active', 0);
            } else {
                $this->dao->where('b_active', 1);
            }
        }

        if ($url != '') {
            $this->dao->like('s_url', $url);
        }
        
        $this->dao->limit($limit, $start);
        $result = $this->dao->get();
        if($result) {
            return $result->result();
        }
        return array();
    }

    /**
     * Count total registers
     *
     * @access public
     * @return integer
     */
    public function registersTotal()
    {
        $this->dao->select('COUNT(*) as total') ;
        $this->dao->from($this->getTable_table_one());
        $result = $this->dao->get();
        if($result) {
            $row = $result->row();
            if(isset($row['total'])) {
                return $row['total'];
            }
        }
        return 0;
    }

    /**
     * Get register
     *
     * @access public
     * @param integer $id
     * @return array
     */
    public function getRegisterById($id) {
        $this->dao->select('*');
        $this->dao->from($this->getTable_table_one());
        $this->dao->where('pk_i_id', $id);
        $result = $this->dao->get();
        if($result) {
            return $result->row();
        }
        return FALSE;
    }

    /**
     * Delete register
     *
     * @access public
     * @param integer $id
     * @return bool Return true if has been deleted, on contrary will return false.
     */
    public function deleteRegister($id) {
        return $this->dao->delete($this->getTable_table_one(), array('pk_i_id' => $id));
    }
}