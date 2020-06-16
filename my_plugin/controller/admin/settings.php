<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/*
 * My Plugin - It's a basic plugin for Osclass as resource for a tutorial about how to implement it.
 * Copyright (C) 2020  Adrián Olmedo
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

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
                    osc_add_flash_error_message(__("Enter a valid number.", MY_PLUGIN_PREF), 'admin');
                } else {
                    osc_set_preference('field_one', Params::getParam('field_one'), MY_PLUGIN_PREF, 'BOOLEAN');
                    osc_set_preference('field_two', Params::getParam('field_two'), MY_PLUGIN_PREF, 'BOOLEAN');
                    osc_set_preference('field_three', $field_three, MY_PLUGIN_PREF, 'STRING');

                    osc_add_flash_ok_message(__("The plugin is now configured.", MY_PLUGIN_PREF), 'admin');
                }
                ob_get_clean();
                $this->redirectTo(osc_route_admin_url('my-plugin-admin-settings'));
                break;
        }
    }
    
}