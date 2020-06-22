<?php	
    $data_view = 'Day'; //Default
	$date_filter = '';
    $site_filter = '';

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $date_filter = $_POST['date_picker'];
		$data_view = $_POST['dataview_picker']; 
    } else {
		$data_view = 'Day';
	}
	
	
	//require('data/1031191200-lhenard/data_cvolume.php');
	include("clients/".$_SESSION['project']."//data/1031191200-lhenard/data_cvolume.php");


// $res = $db->prepare('SET @DataView="'.$data_view.'"; SET @SiteFilter="'.$site_filter.'"; ');
// $res->execute();

$res = $avaya_db->prepare($cvolume_qry);

$res->execute();
	
	print'<table id="tableDefault" class="table table-bordered table-hover" cellspacing="0" width="100%">';
	
	$header_data = array (
		$data_view,
		'Calls Offered',
		'Calls Answered',
		'% of Calls Answered',
		'Calls Abandoned',
		'Abandon %',
		'ASA',
		// 'Forecast Calls',
		// 'Forecast %',
		'Service Level %',
		'Total Agent Hours',
		'CPH',
		'Average Talk',
		'Average Hold',
		'Average ACW',
		'AHT',
		'Avail Time',
		'Aux Time',
		'Occupancy'
	);
	
    print '<thead>';
        foreach($header_data as $item) {
			print '<th class="align-middle">'.$item.'</th>';
		}
    print '</thead>';

    print'<tbody class="tbody">';

    while($row = $res->fetch(PDO::FETCH_ASSOC)){
        print '<tr>';
            print '<td>'.$row['DataView'].'</td>';
            print '<td>'.$row['Calls_Offered'].'</td>';
            print '<td>'.$row['Calls_Answered'].'</td>';
            print '<td>'.number_format($row['Answered_Calls_Percent'],2).'%</td>';
            print '<td>'.$row['Abandoned_Calls'].'</td>';
            print '<td>'.number_format($row['Abandoned_Percent'],2).'%</td>';
            print '<td>'.number_format($row['ASA'],2).'</td>';
            // print '<td>'.$row['Forecast'].'</td>';
            // print '<td>'.$row['Forecast_Percent'].'%</td>';
            print '<td>'.number_format($row['SL_Percent'],2).'%</td>';
            print '<td>'.number_format($row['Total_Agent_Hours_Staffed'],2).'</td>';
            print '<td>'.number_format($row['CPH'],2).'</td>';
            print '<td>'.number_format($row['Average_Talk'],2).'</td>';
            print '<td>'.number_format($row['Average_Hold'],2).'</td>';
            print '<td>'.number_format($row['Average_ACW'],2).'</td>';
			print '<td>'.number_format($row['AHT'],2).'</td>';
			print '<td>'.$row['Avail_Time'].'</td>';
            print '<td>'.$row['Aux_Time'].'</td>';
            print '<td>'.number_format($row['Occupancy'],2).'%</td>';
        print '</tr>';
    }

    print '</tbody>';
	
	print '</table>';
?>