<?php

	$file_url  = scandir($_SERVER['DOCUMENT_ROOT'].'\\'.$project_folder.'\clients\\'.$_SESSION['project'].'\pages');

	// $url = array_combine(
	// 	array_map(function($e){
	// 		return implode('', array_map('ucfirst', explode('_', pathinfo($e, PATHINFO_FILENAME))));
	// 	}, $file_url),
	// 	array_map(function($e){
	// 		return pathinfo($e, PATHINFO_FILENAME);
	// 	}, $file_url)
	// );

	$url = array(		
        'Dashboard' => 'dashboard',
        'Interval Productivity' => 'interval_productivity',
		'Call Volume Details' => 'call_volume_details',
		'Call Volume Trends' => 'call_volume_comparison',
		'Billable Report' => 'billable_report',
        'Scorecard' => 'scorecard',
        'Staffing Comparison' => 'staffing_comparison',
        'Roster' => 'roster',
        'Skill Mapping' => 'skill_mapping',
		'Goal' => 'goal',
        //'Manage Users' => 'manage_users'
    );
	
	//Access Level
	if($_SESSION['u_access'] == 'staff') {
		$exc = array(
			'manage_users'
		);
	}elseif($_SESSION['u_access'] == 'viewer') {
		$exc = array(
			'manage_users',
			'goal',
			'skill_mapping',
			'roster'
		);
	}else{
		$exc = array(0);
	}

    $icon = array (
        'dashboard' => '<i class="fas fa-home" style="font-size: 1.5em;" title="Dashboard"></i>',
		'interval_productivity' => '<i class="fas fa-clock" style="font-size: 1.5em;" title="Interval Productivity"></i>',
		'call_volume_details' => '<i class="fas fa-info-circle" style="font-size: 1.5em;" title="Call Volume Details"></i>',
		'call_volume_comparison' => '<i class="fas fa-not-equal" style="font-size: 1.5em;" title="Call Volume Comparison"></i>',
		'billable_report' => '<i class="fas fa-donate" style="font-size: 1.5em;" title="Billable Report"></i>',
		'scorecard' => '<i class="fas fa-layer-group" style="font-size: 1.5em;" title="Scorecard"></i>',
		'staffing_comparison' => '<i class="fas fa-users" style="font-size: 1.5em;" title="Staffing Comparison"></i>',
		'roster' => '<i class="fas fa-user-friends" style="font-size: 1.5em;" title="roster"></i>',
		'skill_mapping' => '<i class="fas fa-user-cog" style="font-size: 1.5em;" title="Skill Mapping"></i>',
		'goal' => '<i class="fas fa-flag" style="font-size: 1.5em;" title="Goal"></i>',
		'manage_users' => '<i class="fas fa-user-shield" style="font-size: 1.5em;" title="Manage Users"></i>'
    );

    $func_content = array(
        'upload' => '<a href="#" data-toggle="modal" data-target="#modalUpload"><i class="fas fa-plus-circle" style="font-size: 1.5em;" title="Upload"></i></a>',
        'import' => '<a href="#" data-toggle="modal" data-target="#modalImport"><i class="fas fa-file-import" style="font-size: 1.5em;" title="Import"></i></a>',
        'filter' => '<a href="#" data-toggle="modal" data-target="#modalFilter"><i class="fas fa-filter" style="font-size: 1.5em;" title="Filter"></i></a>',
		'refresh' => '<a href="#" onClick="window.location.reload();" <i class="fas fa-redo" style="font-size: 1.5em;" title="Refresh"></i></a>',
    );

	$filter_menu = array(
	'date_picker' => '<div class="form-group"><label for="date_picker">Date</label><input type="date" name="date_picker" id="date_picker" class="form-control"></div>',
	'time_picker' => '<div class="form-group"><label for="time_picker">Time</label><input type="" name="time_picker" id="time_picker" class="form-control" placeholder="HH:MM:SS"></div>',
	'dataview_picker' => '<div class="form-group">
			<label for="dataview_picker">Orientation</label>
				<select name="dataview_picker" id="dataview_picker" class="form-control">
					<option>Month</option>
					<option>Week</option>
					<option>Day</option>
					<!--<option>Interval</option>-->
				</select>
			</div>',			
	'community_picker' => '<div class="form-group"><label for="community_picker">Community</label><input type="text" name="community_picker" id="community_picker" class="form-control" placeholder="Community"></div>',
	'market_picker' => '<div class="form-group"><label for="market_picker">Market</label><input type="text" name="market_picker" id="market_picker" class="form-control" placeholder="Market"></div>',
	'site_picker' => '<div class="form-group">
			<label for="site_picker">Site</label>
				<select name="site_picker" id="site_picker" class="form-control">
					<option>ALL</option>
					<option>Cagayan De Oro</option>
					<option>Vertis North</option>
					<option>Ayala</option>
				</select>
			</div>',
			
	'level_picker' => '<div class="form-group">
			<label for="level_picker">Level</label>
				<select name="level_picker" id="level_picker" class="form-control">
					<option>ALL</option>
					<option>Site</option>
					<option>Manager</option>
					<option>Assistant Manager</option>
					<option>Supervisor</option>
					<option>Agent</option>
				</select>
			</div>',
			
	'level_two_picker' => '<div class="form-group">
			<label for="level_two_picker">Level</label>
				<select name="level_two_picker" id="level_two_picker" class="form-control">'."
					
				".'</select>
			</div>',
	
	'kpi_01_picker' => '<div class="form-group"><label for="kpi_01_picker">KPI 1</label>
						<select name="kpi_01_picker" id="kpi_01_picker" class="form-control">
						</select>			
						</div>',
	'kpi_02_picker' => '<div class="form-group"><label for="kpi_02_picker">KPI 2</label>
						<select name="kpi_02_picker" id="kpi_02_picker" class="form-control">
						</select>			
						</div>',
	'callvolume_category_picker' => '<div class="form-group"><label for="category_picker">Category</label>
							<select name="level_two_picker" id="category_picker" class="form-control">
								<option value="Call Volume">Call Volume</option>
								<option value="Handling Time">Handling Time</option>
								<option value="Productivity">Productivity</option>								
							</select>
						  </div>',
	'callvolume_subcategory_picker' => '<div class="form-group"><label for="category_picker">Category</label>
							<select name="level_two_picker" id="category2_picker" class="form-control">'."
																
							".'</select>
						  </div>',
	'LOB_picker' => '<div class="form-group"><label for="lob_picker">LOB</label>
							<select name="lob_picker" id="lob_picker" class="form-control">
								<option value="">LOB</option>
								<option value="1919">General Sales</option>
								<option value="1925">Special Services</option>
								<option value="1924">Schedule Change</option>
								<option value="1921">IROP</option>
								<option value="1918">Emergency Opt</option>
								<option value="1915">Change Cancel</option>
								<option value="1916">Change Cancel Intl</option>
								<option value="1922">New Gen Sales Agent</option>
								<option value="1920">International</option>
								<option value="1923">Redemption</option>
								<option value="1917">Customer Relations</option>														
							</select>
					</div>'
    );

    $filter = array(
        'dashboard' => array('date_picker'),
		'interval_productivity' => array('LOB_picker','date_picker'),
		'call_volume_details' => array('date_picker','dataview_picker'),
		'call_volume_comparison' => array('date_picker'),
		'billable_report' => array('dataview_picker'),
		'scorecard' => array('date_picker'),
        'staffing_comparison' => array('date_picker')
    );

    $func = array(
		'dashboard' => array('filter'),
		'interval_productivity' => array('refresh','filter'),
		'call_volume_details' => array('refresh','filter'),
		'call_volume_comparison' => array('refresh','filter'),
		'billable_report' => array('refresh','filter'),
		'scorecard' => array('refresh','filter'),
		'staffing_comparison' => array('refresh','filter'),
		'roster_two' => array('upload'),
		'skill_mapping' => array('import','upload'),
		'goal' => array('import'),
		'manage_users' => array('upload')
    );

?>

