	<div class="wrap">
		<h2><?php _e('Flattr Settings'); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'flattr-settings-group' ); ?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e('Your Flattr user ID'); ?></th>
					<td>
						<input name="flattr_uid" type="text" value="<?php echo(get_option('flattr_uid')); ?>" />
						<?php
							if ( ($str = get_option('flattr_uid')) !== false && !is_numeric($str))
							{
								echo '<br />You are supposed to enter your user id and not your user name. You will find your user id on the flattr.com <a href="https://flattr.com/dashboard">dashboard</a>.'; 
							}						
						?>
					</td>
				</tr>
				
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
