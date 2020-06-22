<?php
	$date_filter = '';

    if($_SERVER['REQUEST_METHOD'] == "POST") {
		$date_filter = $_POST['date_picker'];
    }
?>

<?php

//require('data/1031191200-lhenard/data_scoreboard.php');
include("clients/".$_SESSION['project']."//data/1031191200-lhenard/data_scoreboard.php");

$res = $avaya_db->prepare("DECLARE @DateFilter DATE='".$date_filter."' ");
$res->execute();

	$header_data = array (
		'Agent Name',
		'Handled Calls',
		'Transferred Calls',
		'Transferred Calls %',
		'Outbound Calls',
		'Average Talk',
		'Average Hold',
		'Average ACW',
		'AHT',
		'Scheduled Time',
		'Staff Time',
		'Available Time',
		'Occupancy'
	);
	
	$header_data_cm = array (
		'Manager',
		'Handled Calls',
		'Transferred Calls',
		'Transferred Calls %',
		'Outbound Calls',
		'Average Talk',
		'Average Hold',
		'Average ACW',
		'AHT',
		'Scheduled Time',
		'Staff Time',
		'Available Time',
		'Occupancy'
	);
	
	$header_data_accm = array (
		'Assistant Manager',
		'Handled Calls',
		'Transferred Calls',
		'Transferred Calls %',
		'Outbound Calls',
		'Average Talk',
		'Average Hold',
		'Average ACW',
		'AHT',
		'Scheduled Time',
		'Staff Time',
		'Available Time',
		'Occupancy'
	);
	
	$header_data_supp = array (
		'Supervisor',
		'Handled Calls',
		'Transferred Calls',
		'Transferred Calls %',
		'Outbound Calls',
		'Average Talk',
		'Average Hold',
		'Average ACW',
		'AHT',
		'Scheduled Time',
		'Staff Time',
		'Available Time',
		'Occupancy'
	);
	
	print '<div class="accordion" id="accordionExample">';
	
		//Managers
		print '<div class="card">
				<div class="card-header" id="headingOne">
				  <h2 class="mb-0">
					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					  Managers
					</button>
				  </h2>
				</div>';
				
		print '<div id="collapseOne" class="collapse show" data-parent="#accordionExample">
				<div class="card-body">';
	
					print '<table class="table table-bordered table-hover" cellspacing="0" width="100%">';
						print '<thead>';
							print '<tr>';
								foreach($header_data_cm as $item) {
									
									print '<th class="align-middle">'.$item.'</th>';
								}
							print '</tr>';
						print '</thead">';
					$res = $avaya_db->prepare($sboard_qry);

					$res->execute();

					while($row = $res->fetch(PDO::FETCH_ASSOC)){
						print '<tr>';
							//print '<td style="white-space: nowrap>'.$row['Call_Date'].'</td>';
							print '<td>'.$row['Emp_Name'].'</td>';
							print '<td>'.$row['Handled_Calls'].'</td>';
							print '<td>'.$row['Transfered_Calls'].'</td>';
							if($row['Transferred_Calls_Percent'] == 0){print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' %</td>';} 
							print '<td>'.$row['Outband_Calls'].'</td>';
							print '<td>'.number_format($row['Average_Talk'],2).'</td>';
							print '<td>'.number_format($row['Average_Hold'], 2).'</td>';
							print '<td>'.number_format($row['Average_ACW'], 2).'</td>';
							print '<td>'.number_format($row['AHT'], 2).'</td>';
							print '<td>'.$row['Scheduled_Time'].'</td>';
							print '<td>'.$row['Staffed_Time'].'</td>';
							print '<td>'.$row['Avail_Time'].'</td>';
							print '<td>'.number_format($row['Occupancy'], 2).'</td>';
						print '</tr>';
					}
					
					print '</table>';
				
				print '</div>';
			print '</div>';
		print '</div>';
		
		//Assistant Managers
		print '<div class="card">
				<div class="card-header" id="headingTwo">
				  <h2 class="mb-0">
					<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
					  Assistant Managers
					</button>
				  </h2>
				</div>';
				
		print '<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
				<div class="card-body">';
	
				print '<table class="table table-bordered table-hover" cellspacing="0" width="100%">';
					
						print '<tr>';
							foreach($header_data_accm as $item) {
								
								print '<th class="align-middle">'.$item.'</th>';
							}
						print '</tr>';
					
					$res = $avaya_db->prepare($sboard_qry);

					$res->execute();

					while($row = $res->fetch(PDO::FETCH_ASSOC)){
						print '<tr>';
							print '<td>'.$row['Emp_Name'].'</td>';
							print '<td>'.$row['Handled_Calls'].'</td>';
							print '<td>'.$row['Transfered_Calls'].'</td>';
							if($row['Transferred_Calls_Percent'] == 0){print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' %</td>';} 
							print '<td>'.$row['Outband_Calls'].'</td>';
							print '<td>'.number_format($row['Average_Talk'],2).'</td>';
							print '<td>'.number_format($row['Average_Hold'], 2).'</td>';
							print '<td>'.number_format($row['Average_ACW'], 2).'</td>';
							print '<td>'.number_format($row['AHT'], 2).'</td>';
							print '<td>'.$row['Scheduled_Time'].'</td>';
							print '<td>'.$row['Staffed_Time'].'</td>';
							print '<td>'.$row['Avail_Time'].'</td>';
							print '<td>'.number_format($row['Occupancy'], 2).'</td>';
						print '</tr>';
					}
					
					print '</table>';
				
				print '</div>';
			print '</div>';
		print '</div>';
		
		//Supervisors
		print '<div class="card">
				<div class="card-header" id="headingThree">
				  <h2 class="mb-0">
					<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
					  Supervisor
					</button>
				  </h2>
				</div>';
				
		print '<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
				<div class="card-body">';
				
					$res = $avaya_db->prepare($sboard_qry);

					$res->execute();
	
					print '<table class="table table-bordered table-hover" cellspacing="0" width="100%">';
					
					print'<thead>';
					
						print '<tr>';
							foreach($header_data_supp as $item) {
								print '<th class="align-middle">'.$item.'</th>';
							}
						print '<tr>';
						
					print'</thead>';

					print'<tbody>';

					while($row = $res->fetch(PDO::FETCH_ASSOC)){
						print '<tr>';
							print '<td>'.$row['Emp_Name'].'</td>';
							print '<td>'.$row['Handled_Calls'].'</td>';
							print '<td>'.$row['Transfered_Calls'].'</td>';
							if($row['Transferred_Calls_Percent'] == 0){print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' %</td>';} 
							print '<td>'.$row['Outband_Calls'].'</td>';
							print '<td>'.number_format($row['Average_Talk'],2).'</td>';
							print '<td>'.number_format($row['Average_Hold'], 2).'</td>';
							print '<td>'.number_format($row['Average_ACW'], 2).'</td>';
							print '<td>'.number_format($row['AHT'], 2).'</td>';
							print '<td>'.$row['Scheduled_Time'].'</td>';
							print '<td>'.$row['Staffed_Time'].'</td>';
							print '<td>'.$row['Avail_Time'].'</td>';
							print '<td>'.number_format($row['Occupancy'], 2).'</td>';
							
						print '</tr>';
					}

					print '</tbody>';
					
					print '</table>';
				
				print '</div>';
			print '</div>';
		print '</div>';
		
		//Agent
		print '<div class="card">
				<div class="card-header" id="headingFour">
				  <h2 class="mb-0">
					<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
					  Agent
					</button>
				  </h2>
				</div>';
				
		print '<div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
				<div class="card-body">';
				
					$res = $avaya_db->prepare($sboard_qry);

					$res->execute();
	
					print '<table class="table table-bordered table-hover" cellspacing="0" width="100%">';
					print '<thead>';
						print '<tr>';
							foreach($header_data as $item) {
								
									print '<th class="align-middle">'.$item.'</th>';
								
							}
						print '</tr>';
					print '</thead>';

					print'<tbody>';

					while($row = $res->fetch(PDO::FETCH_ASSOC)){
						print '<tr>';
							//print '<td style="white-space: nowrap>'.$row['Call_Date'].'</td>';
							print '<td>'.$row['Emp_Name'].'</td>';
							print '<td>'.$row['Handled_Calls'].'</td>';
							print '<td>'.$row['Transfered_Calls'].'</td>';
							if($row['Transferred_Calls_Percent'] == 0){print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Transferred_Calls_Percent'],2)).' %</td>';} 
							print '<td>'.$row['Outband_Calls'].'</td>';
							print '<td>'.number_format($row['Average_Talk'],2).'</td>';
							print '<td>'.number_format($row['Average_Hold'], 2).'</td>';
							print '<td>'.number_format($row['Average_ACW'], 2).'</td>';
							print '<td>'.number_format($row['AHT'], 2).'</td>';
							print '<td>'.$row['Scheduled_Time'].'</td>';
							print '<td>'.$row['Staffed_Time'].'</td>';
							print '<td>'.$row['Avail_Time'].'</td>';
							print '<td>'.number_format($row['Occupancy'], 2).'</td>';
							/*print '<td>'.$row['Aux_Out_Percent'].'%</td>';
							print '<td>'.$row['Aux0_Percent'].'</td>';
							print '<td>'.$row['Aux0'].'</td>';
							print '<td>'.$row['Aux1_Percent'].'%</td>';
							print '<td>'.$row['Aux1'].'</td>';
							print '<td>'.$row['Aux2_Percent'].'%</td>';
							print '<td>'.$row['Aux2'].'</td>';
							print '<td>'.$row['Aux3_Percent'].'%</td>';
							print '<td>'.$row['Aux3'].'</td>';
							print '<td>'.$row['Aux4_Percent'].'%</td>';
							print '<td>'.$row['Aux4'].'</td>';
							print '<td>'.$row['Aux5_Percent'].'%</td>';
							print '<td>'.$row['Aux5'].'</td>';
							print '<td>'.$row['Aux6_Percent'].'%</td>';
							print '<td>'.$row['Aux6'].'</td>';
							print '<td>'.$row['Aux7_Percent'].'%</td>';
							print '<td>'.$row['Aux7'].'</td>';
							print '<td>'.$row['Aux8_Percent'].'%</td>';
							print '<td>'.$row['Aux8'].'</td>';
							print '<td>'.$row['Aux9_Percent'].'%</td>';
							print '<td>'.$row['Aux9'].'</td>';
							print '<td>'.$row['Total_Aux_Percent'].'%</td>';
							print '<td>'.$row['Net_Aux'].'%</td>';*/
							
						print '</tr>';
					}

					print '</tbody>';
					
					print '</table>';
				
				print '</div>';
			print '</div>';
		print '</div>';
		
	print '</div>';
?>

