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
$dash_table_heads
$dash_table_qry

$$dash_pie_heads
$dash_pie_qry


$dash_graph_heads
$dash_graph_qry

*/

//Queries: [$dash_table_qry, $dash_pie_qry, $dash_graph_qry]


//Table 
$dash_table_heads = array('row_date', 'DateTime', 'Calls_Offered', 'Calls_Answered', 'Percent_Answered_Calls', 'Abandoned_Calls', 'Percent_Abandoned', 
'ASA', 'Forecast', 'Percent_Forecast', 'Percent_Service_Level', 'Total_Staff_Hours', 'CPH', 'Average_Talk', 'Average_Hold', 'Average_ACW', 'AHT', 
'Avail_Time', 'Aux_Time', 'Occupancy');
//Filters: @DateFilter
			
$dash_table_qry = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter DATE = '".$date_filter."'
SELECT
	CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
	IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
		IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				CONCAT('00:0',a.starttime,':00')
					) )	) ) ) AS dtime,
				
	COALESCE(SUM(a.callsoffered),0) as Calls_Offered,
	COALESCE(SUM(a.acdcalls),0) As Calls_Answered,
	COALESCE(CAST(SUM(a.acdcalls) AS DECIMAL(38,30)) / SUM(a.callsoffered)*100 ,0) As Percent_Answered_Calls,
	COALESCE(SUM(a.abncalls),0) As Abandoned_Calls,
	COALESCE(CAST(SUM(a.abncalls) AS DECIMAL(38,30)) / SUM(a.callsoffered)*100,0) As Percent_Abandoned,
	COALESCE(CAST(SUM(a.anstime) AS DECIMAL(38,30)) / SUM(a.acdcalls),0) As ASA,
	
	COALESCE(c.Forecast, 0) AS Forecast, 
	COALESCE(CAST(c.Forecast AS DECIMAL(38,30))/ SUM(a.acdcalls)*100,0)  As Percent_Forecast, 
	
	COALESCE(CAST(SUM(a.acceptable) AS DECIMAL(38,30)) / SUM(a.callsoffered)*100,0) As Percent_Service_Level,
	COALESCE(MAX(a.i_stafftime) / 3600,0) As Total_Staff_Hours,
	COALESCE(CAST(SUM(a.acdcalls) AS DECIMAL(38,30)) / (MAX(a.i_stafftime) / 3600) ,0) As CPH,
	COALESCE(CAST(MAX(a.i_acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls),0) As Average_Talk,
	COALESCE((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) /  CAST(SUM(a.acdcalls) AS DECIMAL(38,30)),0) As Average_Hold,
	COALESCE(CAST(MAX(a.i_acwtime) AS DECIMAL(38,30)) / SUM(a.callsoffered),0) As Average_ACW,
	COALESCE((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered)),0) As AHT ,
	COALESCE(MAX(a.i_availtime),0) As Avail_Time,
	COALESCE(MAX(a.i_auxtime0 + a.i_auxtime1 + a.i_auxtime2 + a.i_auxtime3 + a.i_auxtime4 + a.i_auxtime5 + a.i_auxtime6 + a.i_auxtime7 + a.i_auxtime8 + a.i_auxtime9 ),0) As Aux_Time,
	
	COALESCE((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls)) /
	((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls)) + MAX(a.i_availtime))
	* 100 ,0) As Occupancy
FROM
   Avaya.dbo.hsplit a

			  LEFT JOIN	(
				SELECT
					c.DATE_TIME,
					COALESCE(SUM(CEILING(c.VOLUME)),0) As Forecast
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
					) )	) ) ) , a.row_date, a.starttime, c.Forecast
Order By
   dtime ASC 
	";


//Pie Chart 
$dash_pie_heads = array('Overall_Date', 'Calls_Offered', 'Calls_Answered', 'Percent_Answered_Calls', 'Abandoned_Calls', 'Percent_Abandoned', 'ASA', 'Forecast', 
'Percent_Forecast',  'Transferred', 'Acceptable_Calls',  'Percent_Service_Level', 'Total_Staff_Hours', 'CPH', 'Average_Talk', 'Average_Hold', 'Average_ACW', 
'AHT', 'Avail_Time', 'Aux_Time', 'Occupancy');
//Filters: @DateTime IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2), ':',RIGHT(a.starttime,2),':00'), 
		// IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			// IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				// CONCAT('00:0',a.starttime,':00')
					// ) )	) ) 

$dash_pie_qry = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter DATE = '".$date_filter."'
SELECT
		CONVERT(smalldatetime, a.row_date) AS row_date,
	   SUM(a.callsoffered) as Calls_Offered,
	   SUM(a.acdcalls) As Calls_Answered,
	   CAST(SUM(a.acdcalls) AS DECIMAL(38,30)) / SUM(a.callsoffered)*100 As Percent_Answered_Calls,
	   SUM(a.abncalls) As Abandoned_Calls,
	   CAST(SUM(a.abncalls) AS DECIMAL(38,30)) / (SUM(a.callsoffered) )*100 As Percent_Abandoned,
	   CAST(SUM(a.anstime) AS DECIMAL(38,30)) / SUM(a.acdcalls) AS ASA,
	   
	   COALESCE(b.Forecast, 0) AS Forecast, 
		COALESCE(b.Forecast/ SUM(a.acdcalls)*100 ,0)  As Percent_Forecast, 

	   SUM(a.transferred) As Transferred,
	   SUM(a.acceptable) As Acceptable_Calls,
	   CAST(SUM(a.acceptable) AS DECIMAL(38,30)) / SUM(a.callsoffered)*100 As Percent_Service_Level,
	   MAX(a.i_stafftime) / 3600 As Total_Staff_Hours,
	   CAST(SUM(a.acdcalls) AS DECIMAL(38,30)) / (MAX(a.i_stafftime) / 3600) As CPH,
	   CAST(MAX(a.i_acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls) As Average_Talk,
	   (CAST(MAX(a.i_acdothertime) AS DECIMAL(38,30)) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls) As Average_Hold,
	   CAST(MAX(a.i_acwtime) AS DECIMAL(38,30)) / SUM(a.callsoffered) As Average_ACW,
	   COALESCE((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered)),0) As AHT ,
	   MAX(a.i_availtime) AS Avail_Time,
	   MAX(a.i_auxtime0 + a.i_auxtime1 + a.i_auxtime2 + a.i_auxtime3 + a.i_auxtime4 + a.i_auxtime5 + a.i_auxtime6 + a.i_auxtime7 + a.i_auxtime8 + a.i_auxtime9) As Aux_Time,

	   COALESCE((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls)) /
	((((CAST(SUM(a.acdtime) AS DECIMAL(38,30)) / SUM(a.acdcalls)) + ((MAX(a.i_acdothertime) + MAX(a.i_acdaux_outtime)) / SUM(a.acdcalls)) + (MAX(a.i_acwtime) / SUM(a.callsoffered))) * SUM(a.acdcalls)) + MAX(a.i_availtime))
	* 100 ,0) As Occupancy
	FROM
	   Avaya.dbo.hsplit a 


				LEFT JOIN
				(
					SELECT
					b.DATE_TIME,
					COALESCE(SUM(b.VOLUME),0) As Forecast
					FROM
					IEX.dbo.FORECAST b
					GROUP BY
					b.DATE_TIME
				
				)
				b ON CONVERT(smalldatetime, a.row_date) = b.DATE_TIME  
				 
				WHERE a.row_date  =
                        CASE	
								WHEN @DateFilter IS NULL THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') )
								WHEN @DateFilter = '' THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
								ELSE @DateFilter
						END 
					
			
			AND a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 

																
	GROUP BY
		 row_date, b.Forecast
	

	
";


//Line Graph 
$dash_graph_heads = array('DateTime', 'Calls_Offered', 'Calls_Answered', 'Acceptable_Calls', 'Forecast');
    
$dash_graph_qry = "
DECLARE @forecast INT = 10
Declare @DateFilter Date
SELECT
	CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
	IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
		IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				CONCAT('00:0',a.starttime,':00')
					) )	) ) ) AS dtime,							   
	SUM(a.callsoffered) as Calls_Offered,
	SUM(a.acdcalls) As Calls_Answered,
	SUM(a.acceptable) As Acceptable_Calls,
	@forecast As Forecast
	   
	FROM
	   Avaya.dbo.hsplit a 

	 LEFT JOIN
		  (
			 SELECT
				b.DATE_TIME, 
				COALESCE(SUM(CEILING(b.VOLUME)),0) As Forecast
			 FROM
				IEX.dbo.FORECAST b

			 WHERE CLIENT_ID = '207'

			 GROUP BY
				b.DATE_TIME
		  )
		  b 
		  on b.DATE_TIME = CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
	IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
		IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				CONCAT('00:0',a.starttime,':00')
					) )	) ) )
		 							   

	WHERE a.row_date  =
                        CASE	
							WHEN '".$date_filter."' IS NULL THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') )
							WHEN '".$date_filter."' = '' THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
							ELSE '".$date_filter."'
						END 
			AND a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 

	Group By
		CONVERT(smalldatetime, CONCAT(CONVERT(date, a.row_date), ' ',
	IIF( LEN(a.starttime) = 4, CONCAT(LEFT(a.starttime,2),':',RIGHT(a.starttime,2),':00'), 
		IIF( LEN(a.starttime) = 3, CONCAT('0',LEFT( a.starttime,1),':',RIGHT(a.starttime,2),':00'),
			IIF( LEN(a.starttime) = 2, CONCAT('00:',a.starttime,':00'), 
				CONCAT('00:0',a.starttime,':00')
					) )	) ) ) 
	Order By
		dtime ASC
";




if (basename(__FILE__) == basename($_SERVER['REQUEST_URI'])){
	
	$date_filter = '';
	
	require('../../../../data/servers.php');
	
	$db_ccms="Avaya";
	
	$ccms_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_ccms, $mck_srv['user'], $mck_srv['pass']);
	$ccms_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	include('../../lib/extras.php');
	
	// include('../../lib/connection.php');
	
	$data_res = $ccms_db->prepare($dash_graph_qry);				//
	$data_res->execute();
	$data_res = $data_res->fetchAll();

	$cnt_res = $ccms_db->prepare($dash_graph_qry);   			//
	$cnt_res->execute();
	$cnt_res = $cnt_res->columnCount();
	
	
	
	print '<table>
		<thead>';
		print'<tr>';
			foreach($dash_graph_heads as $row_heads){       //
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