<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	class CrudDataTable extends DataTable
	{
		public function __construct()
        {
        	osc_add_filter('datatable_crud_status_class', array(&$this, 'row_class'));
            osc_add_filter('datatable_crud_status_text', array(&$this, '_status'));
        }

        /**
         * Build the table in the php file: controller/admin/crud.php
         *
         * Build the table of all registers with filter and pagination
         *
         * @access public
         * @param array $params
         * @return array
         */
        public function table($params)
        {
        	$this->addTableHeader();

            $start = ((int)$params['iPage']-1) * $params['iDisplayLength'];

            $this->start = intval($start);
            $this->limit = intval($params['iDisplayLength']);

            $registers = MyPlugin::newInstance()->registers(array(
                'start'         => $this->start,
                'limit'         => $this->limit,

                'sort'          => Params::getParam('sort'),
                'direction'     => Params::getParam('direction'),

                'dt_date'       => Params::getParam('date'),
                'date_control'  => Params::getParam('dateControl'),
                's_url'         => Params::getParam('s_url'),
                'b_active'      => Params::getParam('b_active')
            ));
            $this->processData($registers);

            $this->total = MyPlugin::newInstance()->registersTotal();
            $this->total_filtered = $this->total;

            return $this->getData();
        }

        private function addTableHeader()
        {
            $this->addColumn('status-border', '');
            $this->addColumn('status', __("Status", MY_PLUGIN_PREF));
            $this->addColumn('bulkactions', '<input id="check_all" type="checkbox" />');

            $this->addColumn('name', __("Name", MY_PLUGIN_PREF));
            $this->addColumn('num', __("Number", MY_PLUGIN_PREF));
            $this->addColumn('pub-date', __("Publication date", MY_PLUGIN_PREF));
            $this->addColumn('date', __("Date", MY_PLUGIN_PREF));

            $dummy = &$this;
            osc_run_hook("admin_my_plugin_registers_table", $dummy);
        }

        private function processData($registers)
        {
            if(!empty($registers)) {

                foreach($registers as $aRow) {
                    $row            = array();
                    $options        = array();
                    $options_more   = array();
                    $moreOptions    = '';

                    // Actions of DataTable
                    $options[] = '<a href="'.osc_route_admin_url('my-plugin-admin-crud').'&register='.$aRow['pk_i_id'].'">'.__("Edit", MY_PLUGIN_PREF).'</a>';
                    $options[] = '<a href="javascript:delete_dialog('.$aRow['pk_i_id'].')">'.__("Delete", MY_PLUGIN_PREF).'</a>';

                    if( $aRow['b_active'] == 1 ) {
                        $options[]  = '<a href="javascript:deactivate_dialog('.$aRow['pk_i_id'].')">' . __("Deactivate", MY_PLUGIN_PREF) . '</a>';
                    } else {
                        $options[]  = '<a href="javascript:activate_dialog('.$aRow['pk_i_id'].')">' . __("Activate", MY_PLUGIN_PREF) . '</a>';
                    }

                    //$options_more[] = '<a href="#">' . __('Custom option') . '</a>';

                    // more actions
                    $options_more = osc_apply_filter('more_actions_manage_registers', $options_more, $aRow);
                    if (count($options_more) > 0 && $options_more != "" && $options_more != NULL) {
                        $moreOptions = '<li class="show-more">'.PHP_EOL.'<a href="#" class="show-more-trigger">'. __("Show more", MY_PLUGIN_PREF) .'...</a>'. PHP_EOL .'<ul>'. PHP_EOL;
                        foreach( $options_more as $actual ) {
                            $moreOptions .= '<li>'.$actual."</li>".PHP_EOL;
                        }
                        $moreOptions .= '</ul>'. PHP_EOL .'</li>'.PHP_EOL;
                    }
                    
                    $actions = '';
                    $options = osc_apply_filter('actions_manage_registers', $options, $aRow);
                    if (count($options) > 0 && $options != "" && $options != NULL) {
                        // create list of actions
                        $auxOptions = '<ul>'.PHP_EOL;
                        foreach( $options as $actual ) {
                            $auxOptions .= '<li>'.$actual.'</li>'.PHP_EOL;
                        }
                        $auxOptions  .= $moreOptions;
                        $auxOptions  .= '</ul>'.PHP_EOL;

                        $actions = '<div class="actions">'.$auxOptions.'</div>'.PHP_EOL;
                    }

                    $row['status-border']   = '';
                    $row['status']          = $aRow['b_active'];
                    $row['bulkactions']     = '<input type="checkbox" name="id[]" value="' . $aRow['pk_i_id'] . '" /></div>';
                    
                    $row['name']            = $aRow['s_name'] . $actions;
                    $row['num']             = $aRow['i_num'];
                    $row['pub-date']        = osc_format_date($aRow['dt_pub_date'], osc_date_format());
                    $row['date']            = osc_format_date($aRow['dt_date'], osc_date_format());

                    $row = osc_apply_filter('my_plugin_registers_processing_row', $row, $aRow);

                    $this->addRow($row);
                    $this->rawRows[] = $aRow;
                }

            }
        }

        public function _status($status)
        {
            return (!$status) ? __("Inactive", MY_PLUGIN_PREF) : __("Active", MY_PLUGIN_PREF);
        }

        /**
         * Get the status of the row. There are two status:
         *     - inactive
         *     - active
         */
        private function get_row_status_class($status)
        {
            return (!$status) ? 'status-inactive' : 'status-active';
        }

        public function row_class($status)
        {
            return $this->get_row_status_class($status);
        }

	}