
<div class="wrap">
	
    <?php screen_icon(); ?>
    
	<form action="options.php" method="post" id="<?php echo $plugin_id; ?>_options_form" name="<?php echo $plugin_id; ?>_options_form">
    
	<?php settings_fields($plugin_id.'_options'); ?>
    
    <h2>kk Plugin Options &raquo; Settings</h2>
    <table class="widefat">
		<thead>
		   <tr>
			 <th><input type="submit" name="submit" value="Save Settings" class="button-primary" style="padding:8px;" /></th>
		   </tr>
		</thead>
		<tfoot>
		   <tr>
			 <th><input type="submit" name="submit" value="Save Settings" class="button-primary" style="padding:8px;" /></th>
		   </tr>
		</tfoot>
		<tbody>
		   <tr>
			 <td style="padding:25px;font-family:Verdana, Geneva, sans-serif;color:#666;">
                 <label for="kkpo_quote">
                     <p>Your favourite quote?</p>
                     <p><input type="text" name="kkpo_quote" value="<?php echo get_option('kkpo_quote'); ?>" /></p>
                 </label>
             </td>
		   </tr>
		</tbody>
	</table>
    
	</form>
    
</div>