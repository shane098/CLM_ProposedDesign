<?php	
    $data_view = 'Day'; //Default
	$date_filter = '';
    $site_filter = '';

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $data_view = $_POST['dataview_picker'];
    }

?>


<?php

//require('data/1031191200-lhenard/data_breport.php');
include("clients/".$_SESSION['project']."//data/1031191200-lhenard/data_breport.php");

//R1 Report Table
$res = $avaya_db->prepare("DECLARE @DataView VARCHAR(10)='".$data_view."' ");
$res->execute();

$res = $avaya_db->prepare($breport_qry);

$res->execute();
	
	print'<table id="tableDefault" class="table table-bordered table-hover" cellspacing="0" width="100%">';
	
	$header_data = array (
		$data_view, //1
		'Billiable Hours', //2
		'B2P', //3
		'Calls Offered', //4
		'Calls Handled', //5
		'AHT sec', //6
		'AHT hrs', //7
		'ACD', //8
		'Hold', //9
		'ACW', //10
		'TOTAL Logged Hrs', //11
		'TOTAL Logged Hrs + Break', //12
		'Total Staff Time', //13
		'AUXOUT %', //14
		'AUX 0 %', //15
		'AUX 1 %', //16
		'AUX 01', //17
		'AUX 2 %', //18
		'AUX 02', //19
		'AUX 3 %', //20
		'AUX 03', //21
		'AUX 4 %', //22
		'AUX 04', //23
		'AUX 5 %', //24
		'AUX 05', //25
		'AUX 6 %', //26
		'AUX 06', //27
		'AUX 7 %', //28
		'AUX 07', //29
		'AUX 8 %', //30
		'AUX 08', //31
		'AUX 9 %', //32
		'AUX 09', //33
		'TOTAL AUX %', //34
		'NET AUX %' //35
	);

    print '<thead>';
        foreach($header_data as $item) {
			print '<th class="align-middle">'.$item.'</th>';
		}
    print '</thead>';

    print'<tbody class="tbody">';
	
	// Overall_Date 1, Billable 2, Bill_To_Pay 3, Calls_Offered 4, Calls_Answered 5, AHT 6, Average_Talk 7, Average_Hold 8, Average_ACW 9, Total_Logged_Hours 10,
	// Total_Logged_Hours_With_Break 11, Staffed_Time 12, Auxout_Percent 13, Aux_Out 14, Aux_0 15, AUX0_Percent -> AUX9_Percent 25,  Aux0 -> Aux9 35, Total_Aux_Percent 36, NetAux_Percent 37

    while($row = $res->fetch(PDO::FETCH_ASSOC)){
        print '<tr>';
            print '<td style="white-space: nowrap">'.$row['DataView'].'</td>';
            print '<td>'.number_format($row['Billable']/60/60,0).'</td>';
            print '<td>'.number_format($row['Bill_To_Pay'],2).'</td>';
			
            print '<td>'.$row['Calls_Offered'].'</td>';
            print '<td>'.$row['Calls_Answered'].'</td>';
            print '<td>'.number_format($row['AHT'],2).'</td>';
			print '<td>'.number_format($row['AHT'] / 3600,2) .'</td>';
            
			print '<td>'.number_format($row['Average_Talk'],2).'</td>'; //ACD
			print '<td>'.number_format($row['Average_Hold'],2).'</td>';
            print '<td>'.number_format($row['Average_ACW'],2).'</td>';
			
            print '<td>'.number_format($row['Total_Logged_Hours'],2).'</td>';
            print '<td>'.number_format($row['Total_Logged_Hours_With_Break'],2).'</td>';
            print '<td>'.$row['Staffed_Time'].'</td>';
			
            print '<td>'.number_format($row['Auxout_Percent'],2).'%</td>';
			print '<td>'.number_format($row['AUX0_Percent'],2).'%</td>';
			print '<td>'.number_format($row['AUX1_Percent'],2).'%</td>';
			print '<td>'.$row['Aux1'].'</td>';
			print '<td>'.number_format($row['AUX2_Percent'],2).'%</td>';
            print '<td>'.$row['Aux2'].'</td>';
			print '<td>'.number_format($row['AUX3_Percent'],2).'%</td>';
            print '<td>'.$row['Aux3'].'</td>';
			print '<td>'.number_format($row['AUX4_Percent'],2).'%</td>';
			print '<td>'.$row['Aux4'].'</td>';
			print '<td>'.number_format($row['AUX5_Percent'],2).'%</td>';
			print '<td>'.$row['Aux5'].'</td>';
			print '<td>'.number_format($row['AUX6_Percent'],2).'%</td>';
			print '<td>'.$row['Aux6'].'</td>';
			print '<td>'.number_format($row['AUX7_Percent'],2).'%</td>';
			print '<td>'.$row['Aux7'].'</td>';
			print '<td>'.number_format($row['AUX8_Percent'],2).'%</td>';
			print '<td>'.$row['Aux8'].'</td>';
			print '<td>'.number_format($row['AUX9_Percent'],2).'%</td>';
			print '<td>'.$row['Aux9'].'</td>';
			print '<td>'.number_format($row['Total_Aux_Percent'],2).'%</td>';
			print '<td>'.number_format($row['NetAux_Percent'],2).'%</td>';
    }

    print '</tbody>';
	
	print '</table>';
?>