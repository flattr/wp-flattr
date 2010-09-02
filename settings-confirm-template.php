	<div class="wrap">
		<h2><?php _e('Flattr Settings'); ?></h2>

		<h3>Confirm new Flattr connection</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Your current Flattr connection'); ?></th>
				<td>
					<?php if (get_option('flattr_uid')) { ?>
						<?php
						if (get_option('flattr_username')) {
							?><a href="<?php echo esc_url( 'https://flattr.com/profile/' . get_option('flattr_username') ); ?>"><?php
							esc_html_e(get_option('flattr_username'));
							?></a> <?php
						}
						?>
						(User ID <?php esc_html_e(get_option('flattr_uid')); ?>)
					<?php } else { ?>
						-
					<?php } ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Your new Flattr connection'); ?></th>
				<td>
					<a href="<?php echo esc_url( 'https://flattr.com/profile/' . $_GET['FlattrUsername'] ); ?>"><?php esc_html_e($_GET['FlattrUsername']); ?></a>
					(User ID <?php esc_html_e($_GET['FlattrId']); ?>)
				</td>
			</tr>
		</table>
		<form method="post" action="options.php">
			<p class="submit">
				<?php
				// Replicating settings_fields
				echo "<input type='hidden' name='option_page' value='flattr-settings-uid-group' />";
				echo '<input type="hidden" name="action" value="update" />';
				wp_nonce_field("flattr-settings-uid-group-options", '_wpnonce', false);
				?>
				<input type="hidden" value="<?php esc_attr_e(remove_query_arg(array('FlattrId', 'FlattrUsername'))); ?>" name="_wp_http_referer">
				<input type="hidden" name="flattr_uid" value="<?php esc_attr_e($_GET['FlattrId']); ?>" />
				<input type="hidden" name="flattr_username" value="<?php esc_attr_e($_GET['FlattrUsername']); ?>" />
				<input type="submit" class="button-primary" value="<?php _e('Accept') ?>" />
			</p>
		</form>
	</div>
