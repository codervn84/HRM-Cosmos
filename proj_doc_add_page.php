<?php
# COSMOS - a php based candidatetracking system

# 
# 

# COSMOS is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# COSMOS is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with COSMOS.  If not, see <http://www.gnu.org/licenses/>.

	# --------------------------------------------------------
	# $Id: proj_doc_add_page.php,v 1.32.2.1 2007-10-13 22:34:22 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'file_api.php' );

	# Check if project documentation feature is enabled.
	if ( OFF == config_get( 'enable_project_documentation' ) ||
		!file_is_uploading_enabled() ||
		!file_allow_project_upload() ) {
		access_denied();
	}

	access_ensure_project_level( config_get( 'upload_project_file_threshold' ) );

	$t_max_file_size = (int)min( ini_get_number( 'upload_max_filesize' ), ini_get_number( 'post_max_size' ), config_get( 'max_file_size' ) );
?>
<?php html_page_top1() ?>
<?php html_page_top2() ?>

<br />
<div align="center">
<form method="post" enctype="multipart/form-data" action="proj_doc_add.php">
<table class="width75" cellspacing="1">
<tr>
	<td class="form-title">
		<?php echo lang_get( 'upload_file_title' ) ?>
	</td>
	<td class="right">
		<?php print_doc_menu( 'proj_doc_add_page.php' ) ?>
	</td>
</tr>
<tr class="row-1">
	<td class="category" width="25%">
		<span class="required">*</span><?php echo lang_get( 'title' ) ?>
	</td>
	<td width="75%">
		<input type="text" name="title" size="70" maxlength="250" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'description' ) ?>
	</td>
	<td>
		<textarea name="description" cols="60" rows="7"></textarea>
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<span class="required">*</span><?php echo lang_get( 'select_file' ) ?>
		<?php echo '<br /><span class="small">(' . lang_get( 'max_file_size' ) . ': ' . number_format( $t_max_file_size/1000 ) . 'k)</span>'?>
	</td>
	<td>
		<input type="hidden" name="max_file_size" value="<?php echo $t_max_file_size ?>" />
		<input name="file" type="file" size="70" />
	</td>
</tr>
<tr>
	<td class="left">
		<span class="required"> * <?php echo lang_get( 'required' ) ?></span>
	</td>
	<td class="center">
		<input type="submit" class="button" value="<?php echo lang_get( 'upload_file_button' ) ?>" />
	</td>
</tr>
</table>
</form>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
