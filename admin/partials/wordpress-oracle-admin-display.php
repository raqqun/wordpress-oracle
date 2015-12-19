<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Wordpress_Oracle
 * @subpackage Wordpress_Oracle/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2>Wordpress Oracle Setup Page</h2>
</div>


<?php $disabled = (get_option('wp_oracle_api_token') !== FALSE) ? 'disabled' : '' ?>
<?php $api_token = (get_option('wp_oracle_api_token') !== FALSE) ? get_option('wp_oracle_api_token') : wp_generate_password(20, false, false); ?>
<form id='wp_oracle_form' method='POST' action=''>
    <table class='form-table'>
        <tr>
            <th scope='row'>
                <label for='wp_oracle_api_token'>Wordpress Oracle api token</label>
            </th>
            <td>
                <input style="width: 200px" id='wp_oracle_api_token' <?php echo $disabled ?> type='text' name='wp_oracle_api_token' value='<?php echo $api_token ?>' placeholder='Wordpress Oracle Api-Token' />
                <p class="description">Wordpress Oracle Api-Token. Use this this with an Api-Token header</p>
            </td>
        </tr>
    </table>
    <p>
        <?php submit_button() ?>
    </p>
</form>