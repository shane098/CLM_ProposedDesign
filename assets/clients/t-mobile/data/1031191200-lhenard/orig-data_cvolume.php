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
$cvolume_heads
$cvolume_qry

*/



//Queries: [$cvolume_qry]



//Table 
$cvolume_heads = array('DataView','Calls_Offered', 'Calls_Answered', 'Answered_Calls_Percent', 'Abandoned_Calls', 'Abandoned_Percent', 'ASA', 'Forecast',
		'Forecast_Calls', 'Forecast_Percent', 'SL_Percent', 'Total_Agent_Hours_Staffed', 'CPH', 'Average_Talk', 'Average_Hold', 'Average_ACW', 'AHT',
		'Avail_Time', 'Aux_Time', 'Occupancy');		
//Filters: @'".$data_view."'
//WHEN '".$data_view."' = 'DateTime' THEN COALESCE(CONVERT(CONCAT(CONVERT(a.row_date, DATE),' ', CONVERT(a.Overall_Time, TIME)), DATETIME), 0)
//WHEN '".$data_view."' = 'Interval' THEN COALESCE(a.Overall_Time, 0)
$cvolume_qry = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter DATE 
DECLARE @DataView NVARCHAR(20) = '".$data_view."'

SELECT (CASE 
			WHEN @DataView = 'Month' THEN DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)
			WHEN @DataView = 'Week' THEN DATEADD(dd, -(DATEPART(dw, a.row_date)-1), a.row_date)		
			WHEN @DataView = 'Day' THEN a.row_date
			ELSE CONVERT(DATE, a.row_date)
		END) AS DataView,		
		COALESCE(SUM(a.callsoffered),0) as Calls_Offered,
		COALESCE(SUM(a.acdcalls),0) As Calls_Answered,
		COALESCE(SUM(CAST(a.acdcalls AS FLOAT)) / SUM(CAST(a.callsoffered AS FLOAT))*100,0) As Answered_Calls_Percent,
		COALESCE(SUM(a.abncalls),0) As Abandoned_Calls,
		COALESCE(ROUND(SUM(CAST(a.abncalls AS FLOAT)) / (SUM(CAST(a.callsoffered AS FLOAT)))*100,2),0) As Abandoned_Percent,
		COALESCE(ROUND(SUM(CAST(a.anstime AS FLOAT)) / SUM(CAST(a.acdcalls as FLOAT)),1),0) As ASA, 
		
		/* COALESCE(b.Fcast,0) As Forecast,
		COALESCE(ROUND(b.Fcast/ SUM(a.callsoffered)*100,2),0) As Forecast_Percent, */

		COALESCE(ROUND(SUM(CAST(a.acceptable AS FLOAT)) / SUM(a.callsoffered)*100,2),0) As SL_Percent,
		COALESCE(ROUND(MAX(CAST(a.i_stafftime AS FLOAT)) / 3600, 0),0) As Total_Agent_Hours_Staffed,
		COALESCE(ROUND(SUM(CAST(a.acdcalls AS FLOAT)) / ((MAX(CAST(a.i_stafftime AS FLOAT)) / 3600)) ,2 ),0) As CPH,
		COALESCE(CAST(MAX(a.i_acdtime) AS FLOAT) / SUM(a.acdcalls),0) As Average_Talk,
		COALESCE(ROUND(MAX(CAST(a.i_acdtime AS FLOAT)) / SUM(CAST(a.acdcalls AS FLOAT)), 2),0) As Talk2,
		COALESCE(CAST(MAX(a.i_acdothertime) AS FLOAT) + MAX(a.i_acdaux_outtime) / SUM(a.acdcalls),0) As Average_Hold,
		COALESCE(CAST(MAX(a.i_acwtime) AS FLOAT) / SUM(a.callsoffered),0) As Average_ACW,
		COALESCE((CAST(MAX(a.i_acdtime) AS FLOAT) / SUM(a.acdcalls)) + (CAST(MAX(a.i_acdothertime) AS FLOAT) + MAX(a.i_acdaux_outtime) / SUM(a.acdcalls)) + (CAST(MAX(a.i_acwtime) AS FLOAT) / SUM(a.callsoffered)) ,0) As AHT,
		COALESCE(MAX(CAST(a.i_availtime AS FLOAT)),0) As Avail_Time,
		COALESCE(MAX(a.i_auxtime0 + a.i_auxtime1 + a.i_auxtime2 + a.i_auxtime3 + a.i_auxtime4 + a.i_auxtime5 + a.i_auxtime6 + a.i_auxtime7 + a.i_auxtime8 + a.i_auxtime9 ),0) As Aux_Time,
		COALESCE(ROUND((((CAST(MAX(a.i_acdtime) AS FLOAT) / SUM(a.acdcalls)) + (CAST(MAX(a.i_acdothertime) AS FLOAT) + MAX(a.i_acdaux_outtime) / SUM(a.acdcalls)) + (CAST(MAX(a.i_acwtime) AS FLOAT) / SUM(a.callsoffered))) * SUM(CAST(a.acdcalls AS FLOAT))) / 
		((((CAST(MAX(a.i_acdtime) AS FLOAT) / SUM(a.acdcalls))+ (CAST(MAX(a.i_acdothertime) AS FLOAT) + MAX(a.i_acdaux_outtime) / SUM(a.acdcalls)) + (CAST(MAX(a.i_acwtime) AS FLOAT) / SUM(a.callsoffered))) * SUM(CAST(a.acdcalls AS FLOAT))) + MAX(CAST(a.i_availtime AS FLOAT))) * 100 , 2),0) As Occupancy 

		FROM Avaya.dbo.dsplit a

		/* LEFT JOIN (SELECT Staff_Date, Staff_Interval,
				  SUM(Call_Volume) As Fcast
			FROM fntr_staffing
			GROUP BY Staff_Date, Staff_Interval) b
			on b.Staff_Date = a.row_date And b.Staff_Interval = a.Overall_Time */

		WHERE 
			a.row_date = (CASE 
				WHEN @DataView = 'Month' THEN a.row_date
				WHEN @DataView = 'Week' THEN DATEADD(dd, -(DATEPART(dw, a.row_date)-1), a.row_date)		
				WHEN @DataView = 'Day' THEN a.row_date
				ELSE CONVERT(DATE, a.row_date)
			END)
		
			AND a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 
		
		GROUP BY 
			(CASE 
				WHEN @DataView = 'Month' THEN DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)
				WHEN @DataView = 'Week' THEN DATEADD(dd, -(DATEPART(dw, a.row_date)-1), a.row_date)		
				WHEN @DataView = 'Day' THEN a.row_date
				ELSE CONVERT(DATE, a.row_date)
			END)

		ORDER BY
			(CASE 
				WHEN @DataView = 'Month' THEN DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)
				WHEN @DataView = 'Week' THEN DATEADD(dd, -(DATEPART(dw, a.row_date)-1), a.row_date)		
				WHEN @DataView = 'Day' THEN a.row_date
				ELSE CONVERT(DATE, a.row_date)
			END) ASC

							
	";




if (basename(__FILE__) == basename($_SERVER['REQUEST_URI'])){
	
	$data_view = 'Day';
	
	require('../../../../data/servers.php');
	
	$db_ccms="Avaya";
	
	$ccms_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_ccms, $mck_srv['user'], $mck_srv['pass']);
	$ccms_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	include('../../lib/extras.php');
	// include('../lib/connection.php');
	
	$data_res = $ccms_db->prepare($cvolume_qry);				//
	$data_res->execute();
	$data_res = $data_res->fetchAll();

	$cnt_res = $ccms_db->prepare($cvolume_qry);   			//
	$cnt_res->execute();
	$cnt_res = $cnt_res->columnCount();
	
	print '<table>
		<thead>';
		print'<tr>';
			foreach($cvolume_heads as $row_heads){       //
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