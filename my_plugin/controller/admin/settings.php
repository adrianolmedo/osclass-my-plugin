<?php
// Controller of My Plugin Settings
class CAdminMyPluginSettings extends AdminSecBaseModel
{

    // Business Layer...
    public function doModel()
    {

        switch (Params::getParam('plugin_action')) {
            case 'done':
                $field_three = Params::getParam("field_three");
                if (!$field_three) $field_three = 0;

                if (!is_numeric($field_three)) {
                    osc_add_flash_error_message(__("Enter a valid number.", 'my_plugin'), 'admin');
                } else {
                    osc_set_preference('field_one', Params::getParam('field_one'), 'my_plugin', 'BOOLEAN');
                    osc_set_preference('field_two', Params::getParam('field_two'), 'my_plugin', 'BOOLEAN');
                    osc_set_preference('field_three', $field_three, 'my_plugin', 'STRING');

                    osc_add_flash_ok_message(__("The plugin is now configured.", 'my_plugin'), 'admin');
                }
                ob_get_clean();
                $this->redirectTo(osc_route_admin_url('my-plugin-admin-settings'));
                break;
        }
    }
    
}