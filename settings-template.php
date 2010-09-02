	<div class="wrap">
		<h2><?php _e('Flattr Settings'); ?></h2>

		<h3>User account</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Your Flattr account'); ?></th>
				<td>
					<?php
					$connect_callback = rawurlencode( ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
					if (get_option('flattr_uid')) { ?>
						Connected with
							<?php
							if (get_option('flattr_username')) {
								?><a href="<?php echo esc_url( 'https://flattr.com/profile/' . get_option('flattr_username') ); ?>"><?php
								esc_html_e(get_option('flattr_username'));
								?></a><?php
							}
							?>
						(User ID <?php esc_html_e(get_option('flattr_uid')); ?>).
						(<a href="https://flattr.com/login?idCallback=<?php echo $connect_callback; ?>">Reconnect</a>)
					<?php } else { ?>
						<a href="https://flattr.com/login?idCallback=<?php echo $connect_callback; ?>">Connect with Flattr</a>
					<?php } ?>
				</td>
			</tr>
		</table>

		<h3>Other settings</h3>
		<form method="post" action="options.php">
			<?php settings_fields( 'flattr-settings-group' ); ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row"><?php _e('Default category for your posts'); ?></th>
					<td>
						<select name="flattr_cat">
							<?php
								foreach (Flattr::getCategories() as $category)
								{
									printf('<option value="%1$s" %2$s>%1$s</option>',
										$category,
										($category == get_option('flattr_cat') ? 'selected' : '')
									);
								}
							?>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Default language for your posts'); ?></th>
					<td>
						<select name="flattr_lng">
							<?php
								foreach (Flattr::getLanguages() as $languageCode => $language)
								{
									printf('<option value="%s" %s>%s</option>',
										$languageCode,
										($languageCode == get_option('flattr_lng') ? 'selected' : ''),
										$language
									);
								}
							?>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Use the compact button'); ?></th>
					<td><input <?php if (get_option('flattr_compact', 'false') == 'true') { echo(' checked="checked"'); } ?> type="checkbox" name="flattr_compact" value="true" /></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Hide my posts from listings on flattr.com'); ?></th>
					<td><input <?php if (get_option('flattr_hide', 'false') == 'true') { echo(' checked="checked"'); } ?> type="checkbox" name="flattr_hide" value="true" /></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Insert Flattr button into posts automagically'); ?></th>
					<td><input <?php if (get_option('flattr_aut', 'off') == 'on') { echo(' checked="checked"'); } ?> type="checkbox" name="flattr_aut" value="on" /><br />(uncheck this if you would rather use <code>&lt;?php the_flattr_permalink() ?&gt;</code>)</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Insert Flattr button into pages automagically'); ?></th>
					<td><input <?php if (get_option('flattr_aut_page', 'off') == 'on') { echo(' checked="checked"'); } ?> type="checkbox" name="flattr_aut_page" value="on" /><br />(uncheck this if you would rather use <code>&lt;?php the_flattr_permalink() ?&gt;</code>)</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
