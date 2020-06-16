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

$registerById = __get('registerById');

// Vars for build the DataTable
$iDisplayLength = __get('iDisplayLength');
$aData          = __get('aData');
$sort           = Params::getParam('sort');
$columns        = $aData['aColumns'];
$rows           = $aData['aRows'];
?>

<!-- Form for add/edit register -->
<?php if ($registerById) : ?>
    <h2 class="render-title"><?php _e("Edit register", MY_PLUGIN_PREF); ?></h2>
<?php else : ?>
    <h2 class="render-title"><?php _e("Add new register", MY_PLUGIN_PREF); ?></h2>
<?php endif; ?>

<form id="dialog-new" method="post" action="<?php echo osc_route_admin_url('my-plugin-admin-crud').'&register='.Params::getParam('register'); ?>" class="has-form-actions">
	<input type="hidden" name="page" value="plugins" />
	<input type="hidden" name="action" value="renderplugin" />
	<input type="hidden" name="route" value="my-plugin-admin-crud" />
	<input type="hidden" name="plugin_action" value="set" />

	<div class="form-horizontal">
		<div class="form-row">
			<div class="form-label"><?php _e("Name", MY_PLUGIN_PREF); ?> (*)</div>
			<div class="form-controls"><input type="text" class="xlarge" name="s_name" value="<?php if (isset($registerById['s_name']) && $registerById['s_name']) echo $registerById['s_name']; ?>"></div>
		</div>

		<div class="form-row">
			<div class="form-label"><?php _e("Number", MY_PLUGIN_PREF); ?> (*)</div>
			<div class="form-controls"><input type="text" class="xlarge" name="i_num" value="<?php if (isset($registerById['i_num']) && $registerById['i_num']) echo $registerById['i_num']; ?>"></div>
		</div>

		<div class="form-row">
			<div class="form-label"><?php _e("Publication date", MY_PLUGIN_PREF); ?></div>
			<div class="form-controls"><input id="dt_pub_date" type="text" class="xlarge" name="dt_pub_date" value="<?php if (isset($registerById['dt_pub_date']) && $registerById['dt_pub_date']) echo $registerById['dt_pub_date']; ?>" placeholder="<?php echo my_plugin_todaydate(); ?>"></div>
		</div>

		<div class="form-row">
			<div class="form-label"><?php _e("URL", MY_PLUGIN_PREF); ?></div>
			<div class="form-controls"><input type="text" class="xlarge" name="s_url" value="<?php if (isset($registerById['s_url']) && $registerById['s_url']) echo $registerById['s_url']; ?>" placeholder='(http, https)'></div>
		</div>

		<div class="form-row">
			<div class="form-label"></div>
			<div class="form-controls">
				<label>
					<input type="checkbox" 
					<?php if (!$registerById || $registerById['b_active'] == true) echo 'checked="true"'; ?> 
					name="b_active" 
					value="1"> <?php _e("Enable/Disable this option", MY_PLUGIN_PREF); ?>
				</label>
			</div>
		</div>

		<div class="form-actions">
            <?php if ($registerById) : ?>
            <a class="btn btn-red" href="javascript:delete_dialog(<?php echo $registerById['pk_i_id']; ?>)"><?php _e("Delete", MY_PLUGIN_PREF); ?></a>
            <?php endif ?>
            <input type="submit" value="<?php echo ($registerById) ? __("Update register", MY_PLUGIN_PREF) : __("Add register", MY_PLUGIN_PREF); ?>" class="btn btn-submit">
        </div>
	</div>
</form>

<!-- Dialog when it want delete a register -->
<form id="dialog-register-delete" method="get" action="<?php echo osc_route_admin_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__("Delete register", MY_PLUGIN_PREF)); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="route" value="my-plugin-admin-crud" />
    <input type="hidden" name="plugin_action" value="delete" />
    <input type="hidden" name="id[]" value="" />

    <div class="form-horizontal">
        <div class="form-row">
            <?php _e("Are you sure you want to delete this register?", MY_PLUGIN_PREF); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-register-delete').dialog('close');"><?php _e("Cancel", MY_PLUGIN_PREF); ?></a>
            <input id="register-delete-submit" type="submit" value="<?php echo osc_esc_html( __("Delete", MY_PLUGIN_PREF) ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>

<!-- DataTable -->
<div class="relative">
	<div id="users-toolbar" class="table-toolbar">
		<div id="users-toolbar" class="table-toolbar">
	        <div class="float-right">
	            <form method="get" action="<?php echo osc_admin_base_url(true); ?>"  class="inline nocsrf">
	                <?php foreach ( Params::getParamsAsArray('get') as $key => $value ) : ?>
	                <?php if ( $key != 'iDisplayLength' ) : ?>
	                <input type="hidden" name="<?php echo $key; ?>" value="<?php echo osc_esc_html($value); ?>" />
	                <?php endif; ?>
	                <?php endforeach; ?>

	                <select name="iDisplayLength" class="select-box-extra select-box-medium float-left" onchange="this.form.submit();" >
	                    <option value="10"><?php printf(__("%d Registers", MY_PLUGIN_PREF), 10); ?></option>
	                    <option value="25" <?php if ( Params::getParam('iDisplayLength') == 25 ) echo 'selected'; ?> ><?php printf(__("%d Registers", MY_PLUGIN_PREF), 25); ?></option>
	                    <option value="50" <?php if ( Params::getParam('iDisplayLength') == 50 ) echo 'selected'; ?> ><?php printf(__("%d Registers", MY_PLUGIN_PREF), 50); ?></option>
	                    <option value="100" <?php if ( Params::getParam('iDisplayLength') == 100 ) echo 'selected'; ?> ><?php printf(__("%d Registers", MY_PLUGIN_PREF), 100); ?></option>
	                </select>
	            </form>

	            <form method="get" action="<?php echo osc_admin_base_url(true); ?>" id="shortcut-filters" class="inline nocsrf">
	                <input type="hidden" name="page" value="plugins" />
                	<input type="hidden" name="action" value="renderplugin" />
                	<input type="hidden" name="route" value="my-plugin-admin-crud" />

	                <a id="btn-display-filters" href="#" class="btn"><?php _e("Show filters", MY_PLUGIN_PREF); ?></a>
	            </form>
	        </div>
	    </div>
	</div>

    <form class="" id="datatablesForm" method="post" action="<?php echo osc_route_admin_url('my-plugin-admin-crud'); ?>">
    	<input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="route" value="my-plugin-admin-crud" />

        <!-- Bulk actions -->
        <div id="bulk-actions">
            <label>
                <?php osc_print_bulk_actions('bulk_actions', 'plugin_action', __get('bulk_options'), 'select-box-extra'); ?>
                <input type="submit" id="bulk_apply" class="btn" value="<?php echo osc_esc_html( __("Apply", MY_PLUGIN_PREF) ); ?>" />
            </label>
        </div>

        <!-- DataTable -->
        <div class="table-contains-actions">
            <table class="table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <?php foreach($columns as $k => $v) {
                            echo '<th class="col-'.$k.' '.($sort==$k?($direction=='desc'?'sorting_desc':'sorting_asc'):'').'">'.$v.'</th>';
                        }; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if( count($rows) > 0 ) { ?>
                    <?php foreach($rows as $key => $row) {
                        $status = $row['status'];
                        $row['status'] = osc_apply_filter('datatable_crud_status_text', $row['status']);
                         ?>
                        <tr class="<?php echo osc_apply_filter('datatable_crud_status_class',  $status); ?>">
                            <?php foreach($row as $k => $v) { ?>
                                <td class="col-<?php echo $k; ?>"><?php echo $v; ?></td>
                            <?php }; ?>
                        </tr>
                    <?php }; ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="<?php echo count($columns)+1; ?>" class="text-center">
                            <p><?php _e("No data available in table", MY_PLUGIN_PREF); ?></p>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div id="table-row-actions"></div> <!-- used for table actions -->
        </div>
    </form>
</div>

<!-- DataTable pagination -->
<?php
function showingResults(){
    $aData = __get('aData');
    echo '<ul class="showing-results"><li><span>'.osc_pagination_showing((Params::getParam('iPage')-1)*$aData['iDisplayLength']+1, ((Params::getParam('iPage')-1)*$aData['iDisplayLength'])+count($aData['aRows']), $aData['iTotalDisplayRecords'], $aData['iTotalRecords']).'</span></li></ul>';
}
osc_add_hook('before_show_pagination_admin','showingResults');
osc_show_pagination_admin($aData);
?>

<!-- Dialog for bulk actions of toolbar -->
<div id="dialog-bulk-actions" title="Bulk actions" class="has-form-actions hide">
    <div class="form-horizontal">
        <div class="form-row"></div>
        <div class="form-actions">
            <div class="wrapper">
                <a id="bulk-actions-cancel" class="btn" href="javascript:void(0);"><?php _e("Cancel", MY_PLUGIN_PREF); ?></a>
                <a id="bulk-actions-submit" href="javascript:void(0);" class="btn btn-red" ><?php echo osc_esc_html( __("Delete", MY_PLUGIN_PREF) ); ?></a>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>

<!-- Form of 'Show filters' -->
<form method="get" action="<?php echo osc_admin_base_url(true); ?>" id="display-filters" class="has-form-actions hide nocsrf">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="route" value="my-plugin-admin-crud" />

    <input type="hidden" name="iDisplayLength" value="<?php echo Params::getParam('iDisplayLength'); ?>" />
    
    <div class="form-horizontal">
        <div class="grid-system">
            <div class="grid-row grid-50">
                <div class="row-wrapper">
                    
                    <div class="form-row">
                        <div class="form-label">
                            <?php _e("URL", MY_PLUGIN_PREF); ?>
                        </div>
                        <div class="form-controls">
                            <input name="s_url" type="text" value="<?php echo osc_esc_html(Params::getParam('s_url')); ?>" />
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-label">
                            <?php _e("Date", MY_PLUGIN_PREF); ?>
                        </div>
                        <div class="form-controls">
                            <input id="date" type="text" class="xlarge" name="date" value="<?php echo Params::getParam('date'); ?>" placeholder="<?php echo my_plugin_todaydate(null, null, '00:00:00'); ?>">
                            <select name="dateControl">
                            	<option value="equal" <?php echo ( (Params::getParam('dateControl') == 'equal') ? 'selected="selected"' : '' )?>>=</option>
                            	<option value="greater" <?php echo ( (Params::getParam('dateControl') == 'greater') ? 'selected="selected"' : '' )?>>></option>
                            	<option value="greater_equal" <?php echo ( (Params::getParam('dateControl') == 'greater_equal') ? 'selected="selected"' : '' )?>>>=</option>
                            	<option value="less" <?php echo ( (Params::getParam('dateControl') == 'less') ? 'selected="selected"' : '' )?>><</option>
                            	<option value="less_equal" <?php echo ( (Params::getParam('dateControl') == 'less_equal') ? 'selected="selected"' : '' )?>><=</option>
                            	<option value="not_equal" <?php echo ( (Params::getParam('dateControl') == 'not_equal') ? 'selected="selected"' : '' )?>>!=</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-row grid-50">
                <div class="row-wrapper">
                    
                    <div class="form-row">
                        <div class="form-label">
                            <?php _e("Status", MY_PLUGIN_PREF); ?>
                        </div>
                        <div class="form-controls">
                            <select id="b_active" name="b_active">
                                <option value="" <?php echo ( (Params::getParam('b_active') == '') ? 'selected="selected"' : '' )?>><?php _e("Choose an option", MY_PLUGIN_PREF); ?></option>
                                <option value="1" <?php echo ( (Params::getParam('b_active') == '1') ? 'selected="selected"' : '' )?>>ACTIVATE</option>
                                <option value="0" <?php echo ( (Params::getParam('b_active') == '0') ? 'selected="selected"' : '' )?>>DEACTIVATE</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-label">
                            <?php _e("Order by", MY_PLUGIN_PREF); ?>
                        </div>
                        <div class="form-controls">
                        	<select name="sort">
                                <option value="date" <?php echo ( (Params::getParam('sort') == 'date') ? 'selected="selected"' : '' )?>>DATE</option>
                                <option value="pub_date" <?php echo ( (Params::getParam('sort') == 'pub_date') ? 'selected="selected"' : '' )?>>PUBLICATION DATE</option>
                            </select>
                            <select name="direction">
                                <option value="desc" <?php echo ( (Params::getParam('direction') == 'desc') ? 'selected="selected"' : '' )?>>DESC</option>
                                <option value="asc" <?php echo ( (Params::getParam('direction') == 'asc') ? 'selected="selected"' : '' )?>>ASC</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="form-actions">
        <div class="wrapper">
            <input id="show-filters" type="submit" value="<?php echo osc_esc_html( __("Apply filters", MY_PLUGIN_PREF) ); ?>" class="btn btn-submit" />
            <a class="btn" href="<?php echo osc_route_admin_url('my-plugin-admin-crud'); ?>"><?php _e("Reset filters", MY_PLUGIN_PREF); ?></a>
        </div>
    </div>
</form>

<!-- Dialog when it want activate a register -->
<form id="dialog-register-activate" method="get" action="<?php echo osc_route_admin_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__("Activate register", MY_PLUGIN_PREF)); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="route" value="my-plugin-admin-crud" />
    <input type="hidden" name="plugin_action" value="activate" />
    <input type="hidden" name="id[]" value="" />

    <div class="form-horizontal">
        <div class="form-row">
            <?php _e("Are you sure you want to activate this register?", MY_PLUGIN_PREF); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-register-activate').dialog('close');"><?php _e("Cancel", MY_PLUGIN_PREF); ?></a>
                <input id="register-activate-submit" type="submit" value="<?php echo osc_esc_html( __("Activate", MY_PLUGIN_PREF) ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>

<!-- Dialog when it want deactivate a register -->
<form id="dialog-register-deactivate" method="get" action="<?php echo osc_route_admin_url(true); ?>" class="has-form-actions hide" title="<?php echo osc_esc_html(__("Deactivate register", MY_PLUGIN_PREF)); ?>">
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="route" value="my-plugin-admin-crud" />
    <input type="hidden" name="plugin_action" value="deactivate" />
    <input type="hidden" name="id[]" value="" />

    <div class="form-horizontal">
        <div class="form-row">
            <?php _e("Are you sure you want to deactivate this register?", MY_PLUGIN_PREF); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
                <a class="btn" href="javascript:void(0);" onclick="$('#dialog-register-deactivate').dialog('close');"><?php _e("Cancel", MY_PLUGIN_PREF); ?></a>
                <input id="register-deactivate-submit" type="submit" value="<?php echo osc_esc_html( __("Deactivate", MY_PLUGIN_PREF) ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>

<script>
	$(document).ready(function() {
		// Show the datepicker jquery in the text input dt_pub_name
		$('#dt_pub_date').datepicker({
			dateFormat: 'yy-mm-dd'
		});

		// Dialog delete
        $("#dialog-register-delete").dialog({
            autoOpen: false,
            modal: true
        });

        // Check_all Bulk actions
        $("#check_all").change(function() {
            var isChecked = $(this).prop("checked");
            $('.col-bulkactions input').each( function() {
                if(isChecked == 1) {
                    this.checked = true;
                } else {
                    this.checked = false;
                }
            });
        });
        // Dialog Bulk actions
        $("#dialog-bulk-actions").dialog({
            autoOpen: false,
            modal: true
        });
        $("#bulk-actions-submit").click(function() {
            $("#datatablesForm").submit();
        });
        $("#bulk-actions-cancel").click(function() {
            $("#datatablesForm").attr('data-dialog-open', 'false');
            $('#dialog-bulk-actions').dialog('close');
        });
        // Dialog bulk actions function
        $("#datatablesForm").submit(function() {
            if( $("#bulk_actions option:selected").val() == "" ) {
                return false;
            }

            if( $("#datatablesForm").attr('data-dialog-open') == "true" ) {
                return true;
            }

            $("#dialog-bulk-actions .form-row").html($("#bulk_actions option:selected").attr('data-dialog-content'));
            $("#bulk-actions-submit").html($("#bulk_actions option:selected").text());
            $("#datatablesForm").attr('data-dialog-open', 'true');
            $("#dialog-bulk-actions").dialog('open');
            return false;
        });

        // Form filters
        $('#display-filters').dialog({
            autoOpen: false,
            modal: true,
            width: 700,
            title: '<?php echo osc_esc_js( __("Filters", MY_PLUGIN_PREF) ); ?>'
        });
        $('#btn-display-filters').click(function(){
            $('#display-filters').dialog('open');
            return false;
        });

        // Show the datepicker jquery in the text input date on Filters
        $('#date').datepicker({
            dateFormat: 'yy-mm-dd'
        });

        // Dialog activate
        $("#dialog-register-activate").dialog({
            autoOpen: false,
            modal: true
        });

        // Dialog deactivate
        $("#dialog-register-deactivate").dialog({
            autoOpen: false,
            modal: true
        });
	});

	// Dialog delete function
    function delete_dialog(item_id) {
        $("#dialog-register-delete input[name='id[]']").attr('value', item_id);
        $("#dialog-register-delete").dialog('open');
        return false;
    }

    // Dialog activate function
    function activate_dialog(item_id) {
        $("#dialog-register-activate input[name='id[]']").attr('value', item_id);
        $("#dialog-register-activate").dialog('open');
        return false;
    }

    // Dialog deactivate function
    function deactivate_dialog(item_id) {
        $("#dialog-register-deactivate input[name='id[]']").attr('value', item_id);
        $("#dialog-register-deactivate").dialog('open');
        return false;
    }
</script>