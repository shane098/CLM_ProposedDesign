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
$cvol_comp_heads
$cvol_comp_qry



*/



//Queries: [$cvol_comp_qry]



//Table 
$cvol_comp_heads = array('DateTime', 'Calls_Offered', 'Calls_Answered', 'Acceptable_Calls', 'Forecast');
//Filters:  @DateFilter
			
$cvol_interval_qry = "
DECLARE @forecast INT = 10
Declare @DateFilter Date = '".$date_filter."'
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
					) )	) ) ) 
	Order By
		dtime ASC
		
		
		";
// DAILY 
$cvol_daily_qry = "
DECLARE @forecast INT = 100
Declare @DateFilter Date = '".$date_filter."'
SELECT
	a.row_date,
	SUM(a.callsoffered) as Calls_Offered,
	SUM(a.acdcalls) As Calls_Answered,
	SUM(a.acceptable) As Acceptable_Calls,
	@forecast As Forecast
	   
	FROM
	   Avaya.dbo.dsplit a 

	 LEFT JOIN
		  (
			 SELECT
				b.CALL_DATE, 
				COALESCE(SUM(CEILING(b.VOLUME)),0) As Forecast
			 FROM
				IEX.dbo.FORECAST b
				
			 GROUP BY
				b.CALL_DATE
		  )
		  b on b.CALL_DATE = CONVERT(DATE,a.row_date)
		 							   

	WHERE 

			
			IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)) = 
                CASE	
					WHEN @DateFilter IS NULL THEN (SELECT 
					IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))
													FROM Avaya.dbo.dsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
					WHEN @DateFilter = '' THEN (SELECT 
					IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))
												FROM Avaya.dbo.dsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
					ELSE 
					IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1))
				END
			

			--
			 

			AND a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 

	Group By
	a.row_date
	Order By
		a.row_date ASC
		
		
		";		
//WEEKLY
$cvol_week_qry = "
DECLARE @forecast INT = 100
Declare @DateFilter Date = '".$date_filter."'

SELECT f.dtime, SUM(f.Calls_Offered) AS Calls_Offered, SUM(f.Calls_Answered) AS Calls_Answered, SUM(Acceptable_Calls) AS Acceptable_Calls, SUM(f.forecast) AS Forecast
FROM (

SELECT
DATEADD(dd, -(DATEPART(dw, a.row_date)-1), a.row_date) AS dtime	,
SUM(a.callsoffered) as Calls_Offered,
	SUM(a.acdcalls) As Calls_Answered,
	SUM(a.acceptable) As Acceptable_Calls,
	@forecast As Forecast
	   
	FROM
	   Avaya.dbo.dsplit a 

	 LEFT JOIN
		  (

			 SELECT
				b.CALL_DATE, 
				COALESCE(SUM(CEILING(b.VOLUME)),0) As Forecast
			 FROM
				IEX.dbo.FORECAST b
				
			 GROUP BY
				b.CALL_DATE
		  )
		  b on b.CALL_DATE = CONVERT(DATE,a.row_date)
		 							   

	WHERE 
			IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)) = 
                CASE	
					WHEN @DateFilter IS NULL THEN (SELECT 
					IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))
													FROM Avaya.dbo.dsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
					WHEN @DateFilter = '' THEN (SELECT 
					IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date)))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),MONTH(DATEADD(dd,-(DATEPART(dw,MAX(a.row_date))-1),MAX(a.row_date))),1))
												FROM Avaya.dbo.dsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
					ELSE 
					IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),MONTH(DATEADD(dd,-(DATEPART(dw,@DateFilter)-1),@DateFilter)),1))
				END

				
		
	AND a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 

GROUP BY 
			IIF(DATEDIFF(day,DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date),IIF(MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1))))<=3,IIF(MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date))=12,DATEADD(yy,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)),DATEADD(mm,1,DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1))),DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)), a.row_date
) f

GROUP BY
f.dtime
ORDER BY
f.dtime ASC
			
		
		
		";
		
//MONTHLY
$cvol_month_qry = "
DECLARE @forecast INT = 10000
SELECT
	DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1) as dtime,	   
	SUM(a.callsoffered) as Calls_Offered,
	SUM(a.acdcalls) As Calls_Answered,
	SUM(a.acceptable) As Acceptable_Calls,
	@forecast As Forecast
	   
	FROM
	   Avaya.dbo.dsplit a 

	 LEFT JOIN
		  (
			 SELECT
				b.CALL_DATE, 
				COALESCE(SUM(CEILING(b.VOLUME)),0) As Forecast
			 FROM
				IEX.dbo.FORECAST b

			 WHERE CLIENT_ID = '207'

			 GROUP BY
				b.CALL_DATE
		  )
		  b 
		  on b.CALL_DATE = CONVERT(DATE,a.row_date)
		 							   

	WHERE  a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919') 

	Group By
	DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1)
	Order By
		DATEFROMPARTS(YEAR(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),MONTH(DATEADD(dd,-(DATEPART(dw,a.row_date)-1),a.row_date)),1) ASC
		";




if (basename(__FILE__) == basename($_SERVER['REQUEST_URI'])){
	
	$date_filter = '';
	
	require('../../../data/servers.php');
	
	$db_ccms="Avaya";
	
	$ccms_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_ccms, $mck_srv['user'], $mck_srv['pass']);
	$ccms_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	include('../../lib/extras.php');
	
	// include('../../lib/connection.php');
	
	$data_res = $ccms_db->prepare($cvol_interval_qry);				//
	$data_res->execute();
	$data_res = $data_res->fetchAll();

	$cnt_res = $ccms_db->prepare($cvol_interval_qry);   			//
	$cnt_res->execute();
	$cnt_res = $cnt_res->columnCount();
	
	
	
	print '<table>
		<thead>';
		print'<tr>';
			foreach($cvol_comp_heads as $row_heads){       //
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