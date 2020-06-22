<?php
    $data_view = 'Interval'; //Default
	$date_filter = '';
    $site_filter = '';
	$lob_filter = '';

    if($_SERVER['REQUEST_METHOD'] == "POST") {
		$date_filter = $_POST['date_picker'];
        $lob_filter = $_POST['lob_picker'];
    } else {
		$data_view = 'Interval'; //Default
		$date_filter = '';
	}
	
	include("clients/".$_SESSION['project']."//data/1031191200-lhenard/data_iproductivity.php");
?>

<?php
//R5 Report Table
// $res = $db->prepare('SET @DataView="'.$data_view.'"; SET @DateFilter="'.$date_filter.'"; SET @SiteFilter="'.$site_filter.'"; SET @LOBFilter = "'.$lob_filter.'"; ');
// $res->execute();

	$res = $avaya_db->prepare($iprod_qry);

	$res->execute();
	
	$row = $res->fetchAll();
	$skills = array('FTR_General_Sales_GS' => 'General Services',
					'FTR_Special_Services' => 'Special Services',
					'FTR_Schedule_Change' => 'Schedule Change',
					'FTR_IROP' => 'IROP',
					'FTR_Emergency_Opt_Out1' => 'Emergency Opt',
					'FTR_Change_Cancel' => 'Change Cancel',
					'FTR_Change_Cancel_Intl' => 'Change Cancel Intl',
					'FTR_New_GS_Agent' => 'Change Cancel Intl',
					'FTR_International_Intl' => 'New Gen Sales Agent', 
					'FTR_Redemption' => 'Redemption', 
					'FTR_Cust_Relations_onsite' => 'Customer Relations'
					);
					
	foreach($skills as $key => $value){
		if($lob_filter == ''){
				$skillName = '';
			} else{
				if($row[0]['Skill_LOB'] === $key){
				$skillName = $value;
				}
		}
	}
	
	// print'<div class="tableDefault-wrapper">';
	print'<table class="table table-bordered table-hover" cellspacing="0" width="100%">';
	
	$header_data = array (
		$data_view,
		'Calls Offered',
		'Calls Answered',
		'Transferred Calls',
		'Transferred %',
		'Abandoned Calls',
		'Abandoned %',
		'AHT',
		'Average Talk',
		'Hold Time',
		'Average ACW',
		'Outbound Calls',
		'Outbound Time',
		'Outbound Calls %',
		'Outbound Time %',
		'Handle Time',
		'Service Level %',
		'ASA',
		'Staff Headcount',
		'Available Staff',
		'Idle Staff',
		'Occupancy',
		'Required Staff',
		'Aux Out %',
		'Aux 0',
		'Aux 1',
		'Aux 2',
		'Aux 3',
		'Aux 4',
		'Aux 5',
		'Aux 6',
		'Aux 7',
		'Aux 8',
		'Aux 9',
		'Total Aux %',
		'Net Aux %',
		'Production Efficiency',
		'Bill To Pay',
		'Calls Answered %'	
		
	);
	
    print '<thead>';
	
        foreach($header_data as $item) {
			print '<th class="align-middle">'.$item.'</th>';
		}
		
    print '</thead>';

    print'<tbody>';
	
    foreach($row as $value){
        print '<tr>';			
            print '<td>'.$value['DataView'].'</td>'; //$data_view
            // print '<td>'.$value['Forecast'].'</td>';  //'Forecast',			
            print '<td>'.$value['Calls_Offered'].'</td>';  //'Calls Offered',  
			// if($value['Forecast_Percent'] == 0){print '<td>'.checkZero(number_format($value['Forecast_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Forecast_Percent'],2)).'%</td>';}                           // 'Forecast %', 
            print '<td>'.$value['Calls_Answered'].'</td>'; //'Calls Answered',
            // print '<td>'.$value['Short_Calls'].'</td>'; // 'Short Calls',
            print '<td>'.$value['Transferred_Calls'].'</td>';   //'Transferred Calls',          
			if($value['Percent_of_Tansferred'] == 0){print '<td>'.checkZero(number_format($value['Percent_of_Tansferred'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Percent_of_Tansferred'],2)).'%</td>';} //'Transferred %',
            print '<td>'.$value['Abandoned_Calls'].'</td>'; //'Abandoned Calls'            
			if($value['Percent_of_Abandoned'] == 0){print '<td>'.checkZero(number_format($value['Percent_of_Abandoned'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Percent_of_Abandoned'],2)).'%</td>';}                     // 'Abandoned %',
            // if($value['Forecast_AHT_Percent'] == 0){print '<td>'.checkZero(number_format($value['Forecast_AHT_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Forecast_AHT_Percent'],2)).'%</td>';}                    			
            print '<td>'.number_format($value['AHT'],2).'</td>';
            print '<td>'.number_format($value['Average_Talk'],2).'</td>';
            print '<td>'.number_format($value['Hold_Hours'],2).'</td>';
            print '<td>'.number_format($value['Average_ACW'],2).'</td>';
			print '<td>'.number_format($value['Outbound_Calls'],2).'</td>';
			print '<td>'.number_format($value['Outbound_Time'],2).'</td>';            
			if($value['Outbound_Calls_Percent'] == 0){print '<td>'.checkZero(number_format($value['Outbound_Calls_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Outbound_Calls_Percent'],2)).'%</td>';}                    			
            if($value['Outbound_Time_Percent'] == 0){print '<td>'.checkZero(number_format($value['Outbound_Time_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Outbound_Time_Percent'],2)).'%</td>';}                    						
			print '<td>'.$value['Handle_Time'].'</td>';
			if($value['Service_Level_Percent'] == 0){print '<td>'.checkZero(number_format($value['Service_Level_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Service_Level_Percent'],2)).'%</td>';}                    									
			print '<td>'.number_format($value['ASA'],2).'</td>';
			//print '<td>'.$value['Scheduled_Staff'].'</td>';
			print '<td>'.number_format($value['Staff_Headcount'],2).'</td>';
			print '<td>'.number_format($value['Available_Staff'],2).'</td>';
			print '<td>'.number_format($value['IDLE_Staff'],2).'</td>';
			print '<td>'.number_format($value['Occupancy'],2).'</td>';
			if($value['Required_Staff'] == 0){print '<td>'.checkZero(number_format($value['Required_Staff'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Required_Staff'],2)).'</td>';}
			//print '<td>'.$value['Scheduled_Vs_Required'].'</td>';
			//print '<td>'.$value['Available_Vs_Required'].'</td>';
			
			if($value['AUXOUT_Percent'] == 0){print '<td>'.checkZero(number_format($value['AUXOUT_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['AUXOUT_Percent'],2)).'%</td>';}                    									
			print '<td>'.$value['Aux0'].'</td>';
			print '<td>'.$value['Aux1'].'</td>';
			print '<td>'.$value['Aux2'].'</td>';
			print '<td>'.$value['Aux3'].'</td>';
			print '<td>'.$value['Aux4'].'</td>';
			print '<td>'.$value['Aux5'].'</td>';
			print '<td>'.$value['Aux6'].'</td>';
			print '<td>'.$value['Aux7'].'</td>';
			print '<td>'.$value['Aux8'].'</td>';
			print '<td>'.$value['Aux9'].'</td>';
			if($value['TotalAux_Percent'] == 0){print '<td>'.checkZero(number_format($value['TotalAux_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['TotalAux_Percent'],2)).'%</td>';}                    									
			if($value['NetAux_Percent'] == 0){print '<td>'.checkZero(number_format($value['NetAux_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['NetAux_Percent'],2)).'%</td>';}                    												
			//print '<td>'.$value['Absenteeism'].'</td>';
			print '<td>'.number_format($value['Production_Efficiency'],2).'</td>';
			print '<td>'.number_format($value['Bill_To_Pay'],2).'</td>';
			if($value['Percent_of_Answered_Calls'] == 0){print '<td>'.checkZero(number_format($value['Percent_of_Answered_Calls'],2)).' </td>';}else{print '<td>'.checkZero(number_format($value['Percent_of_Answered_Calls'],2)).'%</td>';}                    															
			
			// print '<td>-</td>';
			// print '<td>-</td>';
			// print '<td>-</td>';
			// print '<td>-</td>';
			
        print '</tr>';
    }

    print '</tbody>';
	
	print '</table>';
	// print '</div>';
?>

