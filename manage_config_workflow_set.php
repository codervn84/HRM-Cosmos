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
	# $Id: manage_config_workflow_set.php,v 1.9.2.1 2007-10-13 22:33:26 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'email_api.php' );

	# helper_ensure_post();

	auth_reauthenticate();

	$t_can_change_level = min( config_get_access( 'notify_flags' ), config_get_access( 'default_notify_flags' ) );
	access_ensure_project_level( $t_can_change_level );

	$t_redirect_url = 'manage_config_workflow_page.php';
	$t_project = helper_get_current_project();
	$t_access = current_user_get_access_level();

	html_page_top1( lang_get( 'manage_workflow_config' ) );
	html_meta_redirect( $t_redirect_url );
	html_page_top2();

	# process the changes to threshold values
	$t_valid_thresholds = array( 'candidate_submit_status', 'candidate_resolved_status_threshold', 'candidate_reopen_status' );

	foreach( $t_valid_thresholds as $t_threshold ) {
		if( config_get_access( $t_threshold ) <= $t_access ) {
			$f_value = gpc_get( 'threshold_' . $t_threshold );
			$f_access = gpc_get( 'access_' . $t_threshold );
			if ( $f_value != config_get( $t_threshold ) ) {
				config_set( $t_threshold, $f_value, NO_USER, $t_project, $f_access );
			}
		}
	}

	# process the workflow by reversing the flags to a matrix and creating the appropriate string
	if( config_get_access( 'status_enum_workflow' ) <= $t_access ) {
		$f_value = gpc_get( 'flag', array() );
		$f_access = gpc_get( 'workflow_access' );
		$t_matrix = array();

		foreach( $f_value as $t_transition ) {
			list( $t_from, $t_to ) = split( ':', $t_transition );
			$t_matrix[$t_from][$t_to] = '';
		}
		$t_statuses = get_enum_to_array( config_get( 'status_enum_string' ) );
		foreach( $t_statuses as $t_state => $t_label) {
			$t_workflow_row = '';
			$t_default = gpc_get_int( 'default_' . $t_state );
			if ( isset( $t_matrix[$t_state] ) && isset( $t_matrix[$t_state][$t_default] ) ) {
				$t_workflow_row .= $t_default . ':' . get_enum_element( 'status', $t_default );
				unset( $t_matrix[$t_state][$t_default] );
				$t_first = false;
			} else {
				# error default state isn't in the matrix
				echo '<p>' . sprintf( lang_get( 'default_not_in_flow' ), get_enum_element( 'status', $t_default ), get_enum_element( 'status', $t_state ) )  . '</p>';
				$t_first = true;
			}
			if ( isset( $t_matrix[$t_state] ) ) {
				foreach ( $t_matrix[$t_state] as $t_next_state => $t_junk ) {
					if ( false == $t_first ) {
						$t_workflow_row .= ',';
					}
					$t_workflow_row .= $t_next_state . ':' . get_enum_element( 'status', $t_next_state );
					$t_first = false;
				}
			}
			if ( '' <> $t_workflow_row ) {
				$t_workflow[$t_state] = $t_workflow_row;
			}
		}
		if ( $t_workflow != config_get( 'status_enum_workflow' ) ) {
			config_set( 'status_enum_workflow', $t_workflow, NO_USER, $t_project, $f_access );
		}
	}

	# process the access level changes
	if( config_get_access( 'status_enum_workflow' ) <= $t_access ) {
		# get changes to access level to change these values
		$f_access = gpc_get( 'status_access' );

		# walk through the status labels to set the status threshold
		$t_enum_status = explode_enum_string( config_get( 'status_enum_string' ) );
		$t_set_status = array();
		foreach( $t_statuses as $t_status_id => $t_status_label) {
			$f_level = gpc_get( 'access_change_' . $t_status_id );
			if ( NEW_ == $t_status_id ) {
				if ( (int)$f_level != config_get( 'report_candidate_threshold' ) ) {
					config_set( 'report_candidate_threshold', (int)$f_level, ALL_USERS, $t_project, $f_access );
				}
			} else {
				$t_set_status[$t_status_id] = (int)$f_level;
			}
		}

		if ( $t_set_status != config_get( 'set_status_threshold' ) ) {
			config_set( 'set_status_threshold', $t_set_status, ALL_USERS, $t_project, $f_access );
		}
	}
?>

<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
