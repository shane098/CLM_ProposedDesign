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
$iproc_qry
$iprod_qry


*/



//Queries: [$iprod_qry]



//Table  , 'SkillView', 'Skill_LOB'
$iprod_heads = array('DataView', 'ddate', 'Forecast', 'Forecast_Percent', 'Calls_Offered', 'Calls_Answered', 'Transferred_Calls', 'Percent_of_Tansferred', 
			'Abandoned_Calls', 'Percent_of_Abandoned', 'Forecast_AHT_Percent', 'AHT', 'Average_Talk', 'Average_Hold', 'Hold_Hours', 'Average_ACW', 'Outbound_Calls',
			'Outbound_Time', 'Outbound_Calls_Percent', 'Outbound_Time_Percent', 'Handle_Time', 'Service_Level_Percent', 'ASA', 'Staff_Headcount', 'Available_Staff',
			'IDLE_Staff', 'Occupancy', 'Required_Staff', 'AUXOUT_Percent', 'AUX0_Percent', 'Aux0', 'AUX1_Percent', 'Aux1', 'AUX2_Percent', 'Aux2', 'AUX3_Percent', 'Aux3',
			'AUX4_Percent', 'Aux4', 'AUX5_Percent', 'Aux5', 'AUX6_Percent', 'Aux6', 'AUX7_Percent', 'Aux7', 'AUX8_Percent', 'Aux8', 'AUX9_Percent', 'Aux9', 'TotalAux_Percent',
			'NetAux_Percent', 'Production_Efficiency', 'Bill_To_Pay', 'Percent_of_Answered_Calls', 'Handle_Variance');		
//Filters: @LOBFILter , @DateFilter
			
$iprod_qry = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter Date = '".$date_filter."'
SELECT 
			CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
	IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
		IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				CONCAT('00:0',a.starttime,':00')
					) )	) ) ) AS DataView,
      
				
				
			/*Skill_LOB, 
			COALESCE(b.FPercent , 2) As Forecast_AHT_Percent, */

			COALESCE(c.Forecast, 0) As Forecast, 
			COALESCE(CAST(c.Forecast AS DECIMAL(38,30)) / SUM(a.acdcalls) * 100, 0) As Forecast_Percent,
			
			a.row_date as ddate,
			COALESCE(SUM(a.callsoffered), 0) as Calls_Offered, 
			COALESCE(SUM(a.acdcalls), 0) As Calls_Answered, 
			COALESCE(SUM(a.transferred), 0) As Transferred_Calls, 
			COALESCE(CAST(SUM(a.transferred) AS DECIMAL(38,30)) / SUM(a.callsoffered)*100, 0) As Percent_of_Tansferred, 
			COALESCE(SUM(a.abncalls), 0) As Abandoned_Calls,
			COALESCE(CAST(SUM(a.abncalls) AS DECIMAL(38,5)) / SUM(a.callsoffered) *100, 0) As Percent_of_Abandoned, 
			COALESCE((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered)),0) As AHT ,
			COALESCE(CAST(MAX(a.i_acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls), 0) As Average_Talk, 
			COALESCE((CAST(MAX(a.i_acdothertime) AS DECIMAL(38,30)) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls), 0) As Average_Hold,
			COALESCE((CAST(MAX(i_acdothertime) AS DECIMAL(38,30)) + MAX(i_acdaux_outtime)) /3600, 0) As Hold_Hours, 
			COALESCE(CAST(MAX(a.i_acwtime) AS DECIMAL(38,30)) / SUM(a.acdcalls), 0) As Average_ACW,
			
			COALESCE(SUM(a.acwoutcalls) + SUM(a.auxoutcalls),0) As Outbound_Calls, 
			COALESCE(SUM(a.acwouttime) + SUM(a.auxoutcalls), 0) As Outbound_Time , 
			COALESCE((CAST(SUM(a.acwoutcalls) AS DECIMAL(38,30)) + SUM(a.auxoutcalls)) / SUM(a.acdcalls)*100, 0) As Outbound_Calls_Percent,
			COALESCE((CAST(MAX(a.i_acwouttime) AS DECIMAL(38,30)) + MAX(a.i_auxouttime))  / MAX(a.i_stafftime) *100, 0) As Outbound_Time_Percent,  

			COALESCE(MAX(a.i_acdtime), 0) As Handle_Time,
			COALESCE(CAST(SUM(a.acceptable) AS DECIMAL(38,15)) / SUM(a.callsoffered)*100, 0) As Service_Level_Percent,
			COALESCE(CAST(SUM(a.anstime) AS DECIMAL(38,30)) / SUM(a.acdcalls), 0) As ASA, 
			COALESCE(MAX(a.i_stafftime) /CAST((60*30) AS DECIMAL(38,30)) , 0) As Staff_Headcount,
			COALESCE(((CAST(MAX(a.i_availtime) AS DECIMAL(38,30))) + MAX(a.i_acdtime) + (MAX(a.i_acwtime)) + (MAX(a.i_acdothertime) + MAX(i_acdaux_outtime)) + (MAX(a.i_ringtime)) + 
			(MAX(a.i_acdaux_outtime))) / (9*60*60), 0) As Available_Staff,
			COALESCE(CAST(MAX(a.i_availtime) AS DECIMAL(38,30)) / (9*60*60), 0) As IDLE_Staff, 

			COALESCE((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls)) /
			((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls)) + SUM(a.i_availtime))
			* 100 ,0) As Occupancy,

			COALESCE(ROUND(((CAST(SUM(a.acdcalls) AS DECIMAL(38,5)) * 370) / 3600) / 7.65 ,0), 0) As Required_Staff,
			COALESCE(CAST(MAX(a.i_acdaux_outtime) AS DECIMAL(38,30)) / MAX(a.i_stafftime) *100, 0) As AUXOUT_Percent,
			COALESCE(CAST(MAX(a.i_auxtime0) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX0_Percent, COALESCE(SUM(a.i_auxtime0), 0) As Aux0,
			COALESCE(CAST(MAX(a.i_auxtime1) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX1_Percent, COALESCE(SUM(a.i_auxtime1), 0) As Aux1,
			COALESCE(CAST(MAX(a.i_auxtime2) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX2_Percent, COALESCE(SUM(a.i_auxtime2), 0) As Aux2,
			COALESCE(CAST(MAX(a.i_auxtime3) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX3_Percent, COALESCE(SUM(a.i_auxtime3), 0) As Aux3,
			COALESCE(CAST(MAX(a.i_auxtime4) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX4_Percent, COALESCE(SUM(a.i_auxtime4), 0) As Aux4,
			COALESCE(CAST(MAX(a.i_auxtime5) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX5_Percent, COALESCE(SUM(a.i_auxtime5), 0) As Aux5,
			COALESCE(CAST(MAX(a.i_auxtime6) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX6_Percent, COALESCE(SUM(a.i_auxtime6), 0) As Aux6,
			COALESCE(CAST(MAX(a.i_auxtime7) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX7_Percent, COALESCE(SUM(a.i_auxtime7), 0) As Aux7,
			COALESCE(CAST(MAX(a.i_auxtime8) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX8_Percent, COALESCE(SUM(a.i_auxtime8), 0) As Aux8,
			COALESCE(CAST(MAX(a.i_auxtime9) AS DECIMAL(38,5)) / MAX(a.i_stafftime) *100, 0) As AUX9_Percent, COALESCE(SUM(a.i_auxtime9), 0) As Aux9,

			COALESCE(((CAST(MAX(a.i_auxtime0 + a.i_auxtime1 + a.i_auxtime2 + a.i_auxtime3 + a.i_auxtime4 + a.i_auxtime5 + a.i_auxtime6 
			+ a.i_auxtime7 + a.i_auxtime8 + a.i_auxtime9 ) AS DECIMAL(38,30))) / MAX(a.i_stafftime))*100, 0) As TotalAux_Percent,

			COALESCE((CAST(MAX(a.i_acdtime + a.i_auxtime0 + a.i_auxtime8) AS DECIMAL(38,30)) / MAX(a.i_stafftime))*100, 0) As NetAux_Percent, 
			COALESCE((CAST(SUM(a.acdcalls) AS DECIMAL(38,30)) + (MAX(a.i_availtime))) / MAX(a.i_stafftime), 0) As Production_Efficiency,

			COALESCE(((CAST(MAX(a.i_availtime) AS DECIMAL(38,30)) + (MAX(a.i_acdtime)) + (MAX(a.i_acwtime)) + (MAX(a.i_acdothertime + i_acdaux_outtime)) + (MAX(a.i_ringtime)) + 
			(MAX(a.i_acdaux_outtime))) / MAX(a.i_stafftime))*100, 0) As Bill_To_Pay,

			COALESCE(CAST(SUM(a.acdcalls) AS DECIMAL(38,5)) / (SUM(a.callsoffered)  )*100, 0) As Percent_of_Answered_Calls, 
			COALESCE(CAST(SUM(a.acdcalls) AS DECIMAL(38,5)) / (SUM(a.callsoffered)  )*100, 0) As Handle_Variance


			FROM Avaya.dbo.hsplit a

			LEFT JOIN	(
				SELECT
					c.DATE_TIME,
					COALESCE(SUM(c.VOLUME),0) As Forecast
				FROM
					IEX.dbo.FORECAST c
				
				GROUP BY
				c.DATE_TIME
			) c ON CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
			IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
				IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
					IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
						CONCAT('00:0',a.starttime,':00')
							) )	) ) ) = c.DATE_TIME  


			WHERE a.row_date  =
                CASE	
					WHEN @DateFilter IS NULL THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
					WHEN @DateFilter = '' THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
					ELSE @DateFilter
				END 

			AND a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 
			
		


								
		Group By 
			CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
	IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
		IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				CONCAT('00:0',a.starttime,':00')
					) )	) ) ), a.row_date, a.starttime , c.Forecast
		ORDER BY
			DataView

	
" ;




if (basename(__FILE__) == basename($_SERVER['REQUEST_URI'])){
	
	$date_filter = '';
	
	require('../../../../data/servers.php');
	
	$db_ccms="Avaya";
	
	$ccms_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_ccms, $mck_srv['user'], $mck_srv['pass']);
	$ccms_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	include('../../lib/extras.php');
	
	// include('../../lib/connection.php');
	
	$data_res = $ccms_db->prepare($iprod_qry);				
	$data_res->execute();
	$data_res = $data_res->fetchAll();

	$cnt_res = $ccms_db->prepare($iprod_qry);   			
	$cnt_res->execute();
	$cnt_res = $cnt_res->columnCount();
	
	print '<table>
		<thead>';
		print'<tr>';
			foreach($iprod_heads as $row_heads){       
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