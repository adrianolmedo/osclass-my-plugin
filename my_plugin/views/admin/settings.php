<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.'); ?>
<form>
	<input type="hidden" name="page" value="plugins" />
	<input type="hidden" name="action" value="renderplugin" />
	<input type="hidden" name="route" value="my-plugin-admin-settings" />
	<input type="hidden" name="plugin_action" value="done" />

	<div class="form-horizontal">
		<div class="form-row">
			<div class="form-label-checkbox">
				<b><h3><?php _e("Option 1", MY_PLUGIN_PREF); ?></h3></b>
				<p><label><input type="checkbox" <?php echo (osc_get_preference('field_one', MY_PLUGIN_PREF) ? 'checked="true"' : ''); ?> name="field_one" value="1"><?php _e("Enable/Disable this option", MY_PLUGIN_PREF); ?>.</label></p>
				<br>
				<b><h3><?php _e("Option 2", MY_PLUGIN_PREF); ?></h3></b>
				<p><label><input type="checkbox" <?php echo (osc_get_preference('field_two', MY_PLUGIN_PREF) ? 'checked="true"' : ''); ?> name="field_two" value="1"><?php _e("Enable/Disable this other option", MY_PLUGIN_PREF); ?>.</label></p>
				<br>
				<p><?php _e("Write a number", MY_PLUGIN_PREF); ?> <lable><input type="text" class="input-small" name="field_three" value="<?php echo osc_get_preference('field_three', MY_PLUGIN_PREF); ?>"> <?php _e("here", MY_PLUGIN_PREF); ?>.</lable></p>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" value="<?php _e("Save changes", MY_PLUGIN_PREF); ?>" class="btn btn-submit">
		<input type="button" value="<?php _e("Add register", MY_PLUGIN_PREF); ?>" class="btn btn-red" onclick="set_register();">
	</div>
</form>

<form id="set-register" method="post" action="<?php echo osc_route_admin_url('my-plugin-admin-crud'); ?>" class='has-form-actions hide'>
	<input type="hidden" name="page" value="plugins" />
	<input type="hidden" name="action" value="renderplugin" />
	<input type="hidden" name="route" value="my-plugin-admin-crud" />
	<input type="hidden" name="plugin_action" value="set" />

	<div class="form-horizontal">
		<div class="form-row">
			<div class="form-label"><?php _e("Name", MY_PLUGIN_PREF); ?> (*)</div>
			<div class="form-controls"><input type="text" class="xlarge" name="s_name" value=""></div>
		</div>
		<div class="form-row">
			<div class="form-label"><?php _e("Number", MY_PLUGIN_PREF); ?> (*)</div>
			<div class="form-controls"><input type="text" class="xlarge" name="i_num" value=""></div>
		</div>
		<div class="form-row">
			<div class="form-label"><?php _e("Publication date", MY_PLUGIN_PREF); ?></div>
			<div class="form-controls"><input id="dt_pub_date" type="text" class="xlarge" name="dt_pub_date" value="" placeholder="<?php echo my_plugin_todaydate(); ?>"></div>
		</div>
		<div class="form-row">
			<div class="form-label"><?php _e("URL", MY_PLUGIN_PREF); ?></div>
			<div class="form-controls"><input type="text" class="xlarge" name="s_url" value="" placeholder='(http, https)'></div>
		</div>
		<div class="form-row">
			<div class="form-label"></div>
			<div class="form-controls"><label><input type="checkbox" name="b_active" value="1"> <?php _e("Enable/Disable this option", MY_PLUGIN_PREF); ?></label></div>
		</div>
		<div class="form-actions">
			<div class="wrapper">
				<input type="submit" value="<?php _e("Add register", MY_PLUGIN_PREF); ?>" class="btn btn-submit">
			</div>
		</div>
	</div>
</form>

<script>
	$(document).ready(function(){
		$("#set-register").dialog({
			autoOpen: false,
			width: "500px",
			modal: true,
			title: '<?php echo osc_esc_js( __("Add new register", MY_PLUGIN_PREF) ); ?>'
		});

		// Show the datepicker jquery
		$('#dt_pub_date').datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});

	function set_register() {
		$('#select_row').show();
		$('#set-register').dialog('open');
	};
</script>