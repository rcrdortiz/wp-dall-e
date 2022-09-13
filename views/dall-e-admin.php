<?php
/** @var array $done */
/** @var array $pending */
/** @var string $api_key */

use Wooppi\Platform\Html\Helper;
use Wooppi\Platform\Html\InputType;

?>
	<form method="POST" class="wooppi-form">
		<input type="hidden" name="dall-e-admin" value="dall-e-admin">
		<h2 class="wooppi-form__header"><?php echo __( 'WordPress Dall-E 2' ); ?></h2>

		<table class="form-table">
			<tbody role="presentation">
			<tr>
				<th scope="row" class="wooppi-form__text"><?php echo __( 'Enter Dall-E API key' ); ?></th>
				<td>
					<input name="api_key" type="password" id="api_key" class="large-text" value="<?php echo $api_key; ?>">
				</td>
			</tr>
			<tr>
				<th scope="row" class="wooppi-form__text"><?php echo __( 'Enter Dall-E query' ); ?></th>
				<td>
					<input name="query" type="text" id="query" class="large-text">
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary button-hero"
								 value="<?php echo __( 'Save' ); ?>"></p>
	</form>
<?php

if ( $pending ) { ?>
	<h2><?php echo __( 'Pending requests' ); ?></h2>

	<?php foreach ( $pending as $query ) {
		echo "<div>$query is pending</div>";
	}
}

if ( $done ) { ?>
	<h2><?php echo __( 'Completed requests' ); ?></h2>

	<?php foreach ( $done as $query => $images ) {
		echo "<h3>$query</h3>";
		echo "<div style='display: flex; justify-content: space-around;'>";
		foreach ( $images as $src ) {
			echo "<img style='width: 250px' src='$src' alt='$query'/>";
		}
		echo "</div>";
	}
}
