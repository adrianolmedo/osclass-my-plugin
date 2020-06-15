<?php
// Controller of My Plugin CRUD
class CAdminMyPluginCRUD extends AdminSecBaseModel
{

    // Business Layer...
    public function doModel()
    {
        $registerById = MyPlugin::newInstance()->getRegisterById(Params::getParam('register'));

        switch (Params::getParam('plugin_action')) {
            // Create/Update register
            case 'set':

                // Validation fields
                if (!Params::getParam('s_name') || !Params::getParam('i_num')) {
                    osc_add_flash_error_message(__("It could not add register. All fields marked with (*) cannot be it empty.", 'my_plugin'), 'admin');
                } else {
                    $data = array(
                        's_name'        => Params::getParam('s_name'),
                        'i_num'         => Params::getParam('i_num'),
                        'dt_pub_date'   => (!Params::getParam('dt_pub_date')) ? my_plugin_todaydate() : Params::getParam('dt_pub_date'),
                        's_url'         => (!Params::getParam('s_url')) ? "" : my_plugin_setURL(Params::getParam('s_url')),
                        'b_active'      => Params::getParam('b_active')
                    );

                    if ($registerById) {
                        // If you are updating a record, setting her id
                        $data['pk_i_id'] = $registerById['pk_i_id'];
                    } else {
                        // Adding a record, setting date of creation
                        $data['dt_date'] = my_plugin_todaydate();
                    }
                    MyPlugin::newInstance()->addRegister($data);

                    // Messages:
                    if ($registerById) {
                        osc_add_flash_ok_message(__("The register it has been updated correctly.", 'my_plugin'), 'admin');
                    } else {
                        osc_add_flash_ok_message(__("The register it has been added correctly.", 'my_plugin'), 'admin');
                    }
                }
                ob_get_clean();
                $this->redirectTo($_SERVER['HTTP_REFERER']);
                break;

            // Delete register (action from DataTable)
            case 'delete':
                $i = 0;
                $registersId = Params::getParam('id');

                if (!is_array($registersId)) {
                    osc_add_flash_error_message(__("Select a register.", 'my_plugin'), 'admin');
                } else {
                    foreach ($registersId as $id) {
                        if (MyPlugin::newInstance()->deleteRegister($id)) $i++;
                    }
                    if ($i == 0) {
                        osc_add_flash_error_message(__("No register have been deleted.", 'my_plugin'), 'admin');
                    } else {
                        osc_add_flash_ok_message(__($i." register(s) have been deleted.", 'my_plugin'), 'admin');
                    }
                }
                ob_get_clean();
                $this->redirectTo($_SERVER['HTTP_REFERER']);
                break;

            // Change status to active (true)
            case 'activate':
                $i = 0;
                $registersId = Params::getParam('id');

                if (!is_array($registersId)) {
                    osc_add_flash_error_message(__("Select a register.", 'my_plugin'), 'admin');
                } else {
                    foreach ($registersId as $id) {
                        $data = array(
                            'pk_i_id'   => $id,
                            'b_active'  => 1 // TRUE
                        );
                        if (MyPlugin::newInstance()->addRegister($data)) $i++;            }
                    if ($i == 0) {
                        osc_add_flash_error_message(__("No registers have been activated.", 'my_plugin'), 'admin');
                    } else {
                        osc_add_flash_ok_message(__($i." register(s) have been activated.", 'my_plugin'), 'admin');
                    }
                }
                ob_get_clean();
                $this->redirectTo($_SERVER['HTTP_REFERER']);
                break;

            // Change status to deactive (false)
            case 'deactivate':
                $i = 0;
                $registersId = Params::getParam('id');

                if (!is_array($registersId)) {
                    osc_add_flash_error_message(__("Select a register.", 'my_plugin'), 'admin');
                } else {
                    foreach ($registersId as $id) {
                        $data = array(
                            'pk_i_id'   => $id,
                            'b_active'  => 0 // FALSE
                        );
                        if (MyPlugin::newInstance()->addRegister($data)) $i++;
                    }
                    if ($i == 0) {
                        osc_add_flash_error_message(__("No registers have been deactivated.", 'my_plugin'), 'admin');
                    } else {
                        osc_add_flash_ok_message(__($i." register(s) have been deactivated.", 'my_plugin'), 'admin');
                    }
                }
                ob_get_clean();
                $this->redirectTo($_SERVER['HTTP_REFERER']);
                break;

            default:
            	$this->_exportVariableToView('registerById', $registerById);

                // DataTable
                require_once MY_PLUGIN_PATH . "classes/datatables/CrudDataTable.php";

                if( Params::getParam('iDisplayLength') != '' ) {
                    Cookie::newInstance()->push('listing_iDisplayLength', Params::getParam('iDisplayLength'));
                    Cookie::newInstance()->set();
                } else {
                    // Set a default value if it's set in the cookie
                    $listing_iDisplayLength = (int) Cookie::newInstance()->get_value('listing_iDisplayLength');
                    if ($listing_iDisplayLength == 0) $listing_iDisplayLength = 10;
                    Params::setParam('iDisplayLength', $listing_iDisplayLength );
                }
                $this->_exportVariableToView('iDisplayLength', Params::getParam('iDisplayLength'));

                $page  = (int)Params::getParam('iPage');
                if($page==0) { $page = 1; };
                Params::setParam('iPage', $page);

                $params = Params::getParamsAsArray();

                $crudDataTable = new CrudDataTable();
                $crudDataTable->table($params);
                $aData = $crudDataTable->getData();
                $this->_exportVariableToView('aData', $aData);

                if(count($aData['aRows']) == 0 && $page!=1) {
                    $total = (int)$aData['iTotalDisplayRecords'];
                    $maxPage = ceil( $total / (int)$aData['iDisplayLength'] );

                    $url = osc_admin_base_url(true).'?'.$_SERVER['QUERY_STRING'];

                    if($maxPage==0) {
                        $url = preg_replace('/&iPage=(\d)+/', '&iPage=1', $url);
                        ob_get_clean();
                        $this->redirectTo($url);
                    }

                    if($page > $maxPage) {
                        $url = preg_replace('/&iPage=(\d)+/', '&iPage='.$maxPage, $url);
                        ob_get_clean();
                        $this->redirectTo($url);
                    }
                }

                $bulk_options = array(
                    array('value' => '', 'data-dialog-content' => '', 'label' => __("Bulk actions", 'my_plugin')),
                    array('value' => 'activate', 'data-dialog-content' => sprintf(__("Are you sure you want to %s the selected register(s)?", 'my_plugin'), strtolower(__("Activate", 'my_plugin'))), 'label' => __("Activate", 'my_plugin')),
                    array('value' => 'deactivate', 'data-dialog-content' => sprintf(__("Are you sure you want to %s the selected register(s)", 'my_plugin'), strtolower(__("Deactivate", 'my_plugin'))), 'label' => __("Deactivate", 'my_plugin')),
                    array('value' => 'delete', 'data-dialog-content' => sprintf(__("Are you sure you want to %s the selected register(s)", 'my_plugin'), strtolower(__("Delete", 'my_plugin'))), 'label' => __("Delete", 'my_plugin'))
                );

                $bulk_options = osc_apply_filter("register_bulk_filter", $bulk_options);
                $this->_exportVariableToView('bulk_options', $bulk_options);
                break;
        }
    }
    
}