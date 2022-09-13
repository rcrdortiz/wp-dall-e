<?php
$rand = rand(0, 10000);
$dall_id = 'dall-e' . $rand;
$dall_query = 'dall-e-query' . $rand;
?>

<div id="dalle_results_<?php echo $rand ?>"></div>
<div id="dalle_container_<?php echo $rand ?>" style="display: flex; flex-direction: column; align-items: center;">
	<h2><?php echo __( 'Or describe an image' ); ?></h2>
	<div style="margin: 25px 200px">
		<tr>
			<td>
				<input name="<?php echo $dall_query; ?>" type="text" id="<?php echo $dall_query; ?>" class="large-text">
			</td>
		</tr>
		<button id='<?php echo $dall_id; ?>' class='button button-primary button-hero' style='margin-top: 12px;'>Generate images</button>
	</div>
	<div id='dall_e_spinner_<?php echo $rand ?>' class="spinner"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#<?php echo $dall_id; ?>').on('click', function () {
			document.getElementById( 'dall_e_spinner_<?php echo $rand ?>' ).classList.toggle( 'is-active' );
            jQuery.ajax({
                type: "post",
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                data: {
                    action: "get_dall_e_images",
                    task: document.getElementById('<?php echo $dall_query; ?>').value
                },
                error: error => {
                    console.log( error );
                },
                success: response => {
                    let toInsert = '<hr/><h2 style="line-height: 5rem">Choose your favorite image</h2><div style="margin-bottom: 1rem; display: flex; justify-content: space-around;">';
                    response.forEach(url => toInsert += `<img style="width: 320px" src="${url}" />`);
                    document.getElementById( 'dalle_results_<?php echo $rand ?>' ).insertAdjacentHTML('beforeend', toInsert);
                    document.getElementById( 'dall_e_spinner_<?php echo $rand ?>' ).classList.toggle( 'is-active' );
                }
            })
        });
    });
</script>
