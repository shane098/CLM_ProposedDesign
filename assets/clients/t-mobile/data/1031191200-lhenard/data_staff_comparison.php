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

//Queries: [$staff_comp_qry]

//Table 
$staff_comp_heads = array('Forecast_DateTime','KPI_01', 'KPI_02');		
//Filters: @DateFilter
			
$staff_comp_qry = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @forecasted INT = 100
DECLARE @DateFilter DATE = '".$date_filter."'
SELECT
				CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
	IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
		IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				CONCAT('00:0',a.starttime,':00')
					) )	) ) ) AS Forecast_DateTime,
					
					@forecasted AS Forecast,
					COALESCE(CAST(MAX(a.i_stafftime) AS DECIMAL(38,15)) / 1800,0) AS Actual
					
					FROM Avaya.dbo.hsplit a
					
					WHERE a.row_date  =
                CASE	
					WHEN @DateFilter IS NULL THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') )
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
								) )	) ) ) , a.row_date, a.starttime
			Order By
			   Forecast_DateTime ASC
										
		
		";

if (basename(__FILE__) == basename($_SERVER['REQUEST_URI'])){
	
	include('../lib/extras.php');
	
	// include('../lib/connection.php');
	
	$data_res = $ccms_db->prepare($staff_comp_qry);				//
	$data_res->execute();
	$data_res = $data_res->fetchAll();

	$cnt_res = $ccms_db->prepare($staff_comp_qry);   			//
	$cnt_res->execute();
	$cnt_res = $cnt_res->columnCount();
	
	print '<table>
		<thead>';
		print'<tr>';
			foreach($staff_comp_heads as $row_heads){       //
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