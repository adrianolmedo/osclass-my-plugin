<form>
	<input type="hidden" name="page" value="plugins" />
	<input type="hidden" name="action" value="renderplugin" />
	<input type="hidden" name="route" value="my-plugin-admin-settings" />
	<input type="hidden" name="plugin_action" value="done" />

	<div class="form-horizontal">
		<div class="form-row">
			<div class="form-label-checkbox">
				<b><h3><?php _e("Option 1", 'my_plugin'); ?></h3></b>
				<p><label><input type="checkbox" <?php echo (osc_get_preference('field_one', 'my_plugin') ? 'checked="true"' : ''); ?> name="field_one" value="1"><?php _e("Enable/Disable this option", 'my_plugin'); ?>.</label></p>
				<br>
				<b><h3><?php _e("Option 2", 'my_plugin'); ?></h3></b>
				<p><label><input type="checkbox" <?php echo (osc_get_preference('field_two', 'my_plugin') ? 'checked="true"' : ''); ?> name="field_two" value="1"><?php _e("Enable/Disable this other option", 'my_plugin'); ?>.</label></p>
				<br>
				<p><?php _e("Write a number", 'my_plugin'); ?> <lable><input type="text" class="input-small" name="field_three" value="<?php echo osc_get_preference('field_three', 'my_plugin'); ?>"> <?php _e("here", 'my_plugin'); ?>.</lable></p>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" value="<?php _e("Save changes", 'my_plugin'); ?>" class="btn btn-submit">
		<input type="button" value="<?php _e("Add register", 'my_plugin'); ?>" class="btn btn-red" onclick="set_register();">
	</div>
</form>

<form id="set-register" method="post" action="<?php echo osc_route_admin_url('my-plugin-admin-crud'); ?>" class='has-form-actions hide'>
	<input type="hidden" name="page" value="plugins" />
	<input type="hidden" name="action" value="renderplugin" />
	<input type="hidden" name="route" value="my-plugin-admin-crud" />
	<input type="hidden" name="plugin_action" value="set" />

	<div class="form-horizontal">
		<div class="form-row">
			<div class="form-label"><?php _e("Name", 'my_plugin'); ?> (*)</div>
			<div class="form-controls"><input type="text" class="xlarge" name="s_name" value=""></div>
		</div>
		<div class="form-row">
			<div class="form-label"><?php _e("Number", 'my_plugin'); ?> (*)</div>
			<div class="form-controls"><input type="text" class="xlarge" name="i_num" value=""></div>
		</div>
		<div class="form-row">
			<div class="form-label"><?php _e("Publication date", 'my_plugin'); ?></div>
			<div class="form-controls"><input id="dt_pub_date" type="text" class="xlarge" name="dt_pub_date" value="" placeholder="<?php echo my_plugin_todaydate(); ?>"></div>
		</div>
		<div class="form-row">
			<div class="form-label"><?php _e("URL", 'my_plugin'); ?></div>
			<div class="form-controls"><input type="text" class="xlarge" name="s_url" value="" placeholder='(http, https)'></div>
		</div>
		<div class="form-row">
			<div class="form-label"></div>
			<div class="form-controls"><label><input type="checkbox" name="b_active" value="1"> <?php _e("Enable/Disable this option", 'my_plugin'); ?></label></div>
		</div>
		<div class="form-actions">
			<div class="wrapper">
				<input type="submit" value="<?php _e("Add register", 'my_plugin'); ?>" class="btn btn-submit">
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
			title: '<?php echo osc_esc_js( __("Add new register", 'my_plugin') ); ?>'
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