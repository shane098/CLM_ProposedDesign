<style>
	table, th, td{
		font-family: Helvetica;
		font-size: 12px;
		border: 1px solid black;
		border-collapse: collapse;
		text-align: center;
	}
</style>


<?php


/*

Headers :
$breport_heads
$breport_qry


*/

//Queries: [$breport_qry]

//Table 
$breport_heads = array('DataView', 'Billable', 'Bill_To_Pay', 'Calls_Offered', 'Calls_Answered', 'AHT', 'Average_Talk', 'Average_Hold', 'Average_ACW', 'Total_Logged_Hours', 
		'Total_Logged_Hours_With_Break', 'Staffed_Time', 'Auxout_Percent','Aux_Out', 'Aux_0', 'AUX0_Percent', 'Aux0', 'AUX1_Percent', 'Aux1',  'AUX2_Percent', 'Aux2',
		'AUX3_Percent', 'Aux3', 'AUX4_Percent', 'Aux4', 'AUX5_Percent','Aux5', 
		'AUX6_Percent', 'Aux6', 'AUX7_Percent', 'Aux7', 'AUX8_Percent', 'Aux8', 'AUX9_Percent', 'Aux9', 'Total_Aux_Percent', 'NetAux_Percent');		
//Filters: 
			
$breport_qry = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DataView VARCHAR(10) = '".$data_view."'


	SELECT (CASE 
				WHEN @DataView = 'Month' THEN  DATENAME(MONTH,a.row_date)
				WHEN @DataView = 'Week' THEN  CONVERT(DATE, a.row_date - (DATEPART(dw,a.row_date - 1)))
				WHEN @DataView = 'Day' THEN a.row_date
				ELSE a.row_date
			END) AS DataView, 

		
		COALESCE(SUM(a.i_availtime) + SUM(a.i_acdtime) + SUM(a.i_acwtime) + SUM(a.i_acdothertime + i_acdaux_outtime) + SUM(a.i_ringtime) + SUM(a.i_acdaux_outtime),0) As Billable, 
		COALESCE((CAST(SUM(a.i_availtime) AS DECIMAL(38,30)) + SUM(a.i_acdtime) + SUM(a.i_acwtime) + SUM(a.i_acdothertime + a.i_acdaux_outtime) + SUM(a.i_ringtime) + SUM(a.i_acdaux_outtime)) / SUM(a.i_stafftime)*100, 0) As Bill_To_Pay,
		COALESCE(SUM(a.callsoffered), 0) as Calls_Offered, 
		COALESCE(SUM(a.acdcalls), 0) As Calls_Answered, 
		COALESCE((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((SUM(a.i_acdothertime) + SUM(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (SUM(a.i_acwtime) / SUM(a.callsoffered)),0) As AHT ,
		COALESCE(CAST(SUM(a.i_acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls), 0) As Average_Talk, 
		COALESCE(CAST(SUM(a.i_acdothertime + a.i_acdaux_outtime) AS DECIMAL(38,30)) / SUM(a.acdcalls), 0) As Average_Hold,
		COALESCE(CAST(SUM(a.i_acwtime) AS DECIMAL(38,30)) / SUM(a.acdcalls), 0) As Average_ACW,
		COALESCE(((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((SUM(a.i_acdothertime) + SUM(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (SUM(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls) + SUM(a.i_availtime)) / 3600 ), 0) As Total_Logged_Hours,
		COALESCE(((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((SUM(a.i_acdothertime) + SUM(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (SUM(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls) + SUM(a.i_availtime + a.i_auxtime1)) / 3600 ), 0) As Total_Logged_Hours_With_Break, 
		COALESCE(SUM(a.i_stafftime), 0) As Staffed_Time,
		COALESCE(CAST(SUM(a.i_acdaux_outtime) AS DECIMAL(38,30)) / SUM(a.i_stafftime)*100, 0) As Auxout_Percent, 
		COALESCE(SUM(a.i_acdaux_outtime), 0) As Aux_Out, 
		COALESCE(SUM(a.i_auxtime0) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX0_Percent, COALESCE(SUM(a.i_auxtime0), 0) As Aux0,
		COALESCE(SUM(a.i_auxtime1) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX1_Percent, COALESCE(SUM(a.i_auxtime1), 0) As Aux1,
		COALESCE(SUM(a.i_auxtime2) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX2_Percent, COALESCE(SUM(a.i_auxtime2), 0) As Aux2,
		COALESCE(SUM(a.i_auxtime3) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX3_Percent, COALESCE(SUM(a.i_auxtime3), 0) As Aux3,
		COALESCE(SUM(a.i_auxtime4) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX4_Percent, COALESCE(SUM(a.i_auxtime4), 0) As Aux4,
		COALESCE(SUM(a.i_auxtime5) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX5_Percent, COALESCE(SUM(a.i_auxtime5), 0) As Aux5,
		COALESCE(SUM(a.i_auxtime6) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX6_Percent, COALESCE(SUM(a.i_auxtime6), 0) As Aux6,
		COALESCE(SUM(a.i_auxtime7) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX7_Percent, COALESCE(SUM(a.i_auxtime7), 0) As Aux7,
		COALESCE(SUM(a.i_auxtime8) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX8_Percent, COALESCE(SUM(a.i_auxtime8), 0) As Aux8,
		COALESCE(SUM(a.i_auxtime9) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30))*100, 0) As AUX9_Percent, COALESCE(SUM(a.i_auxtime9), 0) As Aux9,
		COALESCE((SUM(a.i_auxtime0 + a.i_auxtime1 + a.i_auxtime2 + a.i_auxtime3 + a.i_auxtime4 + a.i_auxtime5 + a.i_auxtime6 + a.i_auxtime7 + a.i_auxtime8 + a.i_auxtime9 ) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30)))*100, 0) As Total_Aux_Percent,
		COALESCE((SUM(a.i_acdtime + a.i_auxtime0 + a.i_auxtime8) / CAST(SUM(a.i_stafftime) AS DECIMAL(38,30)))*100, 0) As NetAux_Percent 
		


		FROM Avaya.dbo.dsplit a
			
			
			 WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 

		Group By 
			CASE 
				WHEN @DataView = 'Month' THEN  DATENAME(MONTH,a.row_date)
				WHEN @DataView = 'Week' THEN  CONVERT(DATE, a.row_date - (DATEPART(dw,a.row_date - 1)))
				WHEN @DataView = 'Day' THEN a.row_date
				ELSE a.row_date
			END

		ORDER BY
			CASE 
				WHEN @DataView = 'Month' THEN  DATENAME(MONTH,a.row_date)
				WHEN @DataView = 'Week' THEN  CONVERT(DATE, a.row_date - (DATEPART(dw,a.row_date - 1)))
				WHEN @DataView = 'Day' THEN a.row_date
				ELSE a.row_date
			END ASC
							
	
";

if (basename(__FILE__) == basename($_SERVER['REQUEST_URI'])){
	
	$data_view = 'Day';
	
	require('../../../../data/servers.php');
	
	$db_ccms="Avaya";
	
	$ccms_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_ccms, $mck_srv['user'], $mck_srv['pass']);
	$ccms_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	include('../../lib/extras.php');
	
	// include('../../lib/connection.php');
	
	$data_res = $ccms_db->prepare($breport_qry);				
	$data_res->execute();
	$data_res = $data_res->fetchAll();

	$cnt_res = $ccms_db->prepare($breport_qry);   			
	$cnt_res->execute();
	$cnt_res = $cnt_res->columnCount();
	
	print '<table>
		<thead>';
		print'<tr>';
			foreach($breport_heads as $row_heads){      
					print'<th>'.$row_heads.'</th>';
			}
		print'</tr>';
	print'</thead>
		<tbody>';
			foreach($data_res as $row_data){
				print'<tr>';
					for($x=0; $x<$cnt_res; $x++){
						print'<td>'.checkNull($row_data[$x]).'</td>';
					}
				print'</tr>';
			}
		print'</tbody>
	</table>';
}

?>