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

//Queries: [$sboard_qry]

//Table 
$sboard_heads = array('row_date','employee_common_name', 'Split', 'Loc_id', 'Handled_Calls', 'Short_Calls', 'Transferred_Calls', 'Transferred_Calls_Percent', 'Outbound_Calls', 'Average_Talk',
		'Average_Hold', 'Hold_Percent', 'OB_Percent_of_Talk', 'Average_ACW', 'AHT', 'Scheduled_Time', 'OT_Time', 'VTO_Time', 'Staffed_Time', 'Avail_Time', 'Idle_Time', 'Occupancy',
		'Aux_Out_Percent', 'Aux_Out', 'Aux0_Percent', 'Aux0','Aux1_Percent','Aux1','Aux2_Percent','Aux2','Aux3_Percent','Aux3','Aux4_Percent','Aux4','Aux5_Percent','Aux5',
		'Aux6_Percent','Aux6','Aux7_Percent','Aux7','Aux8_Percent','Aux8','Aux9_Percent','Aux9','Total_Aux_Percent', 'Total_Aux','Net_Aux','CCMS_Net_Time_Adjust','Absenteeism',
		'Production_Efficiency','Bill_to_Pay');		
		
		
//Filters: @DateFilter
			
$sboard_qry = 
"
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter DATE
SELECT a.employee_common_name As Emp_Name ,  b.loc_id AS loc_id,
		COALESCE(b.HCalls, 0) As Handled_Calls,
		'N/F' As Short_Calls,
		COALESCE(b.TCalls, 0) As Transfered_Calls,
		COALESCE((CAST(b.TCalls AS DECIMAL(38,30)) / b.HCalls) * 100,0) As Transferred_Calls_Percent, 
		COALESCE(b.OCalls, 0) As Outband_Calls ,
		COALESCE((CAST(b.ATalk AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Talk,
		COALESCE((CAST(b.AHold AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Hold,
		COALESCE(b.HoldPercent, 0) As Hold_Percent,
		COALESCE(b.OBPercent, 0) As OB_Percent_of_Talk,
		COALESCE((CAST(b.AAcw AS DECIMAL(38,30)) / b.HCalls), 0) As Average_ACW,
		COALESCE((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls), 0) As AHT,
		IIF(b.HCalls < 1,0,230400) As Scheduled_Time,
		'N/F' As OT_Time,
		'N/F' As VTO_Time,
		COALESCE(b.STime, 0) As Staffed_Time ,
		COALESCE(b.ATime, 0) As Avail_Time,
		COALESCE(b.IdleTime, 0) As Idle_Time,
		COALESCE(SUM(((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls) * b.HCalls) / (((b.ATalk + b.AHold + b.AAcw) / b.HCalls) + b.ATime ) )*100, 0) As Occupancy,
		COALESCE((CAST(b.AuxOut AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux_Out_Percent,
		COALESCE(b.AuxOut, 0) As Aux_Out,
		COALESCE((CAST(b.Aux0 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux0_Percent,
		COALESCE(b.Aux0, 0) As Aux0,
		COALESCE((CAST(b.Aux1 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux1_Percent,
		COALESCE(b.Aux1, 0) As Aux1,
		COALESCE((CAST(b.Aux2 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux2_Percent,
		COALESCE(b.Aux2, 0) As Aux2,
		COALESCE((CAST(b.Aux3 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux3_Percent,
		COALESCE(b.Aux3, 0) As Aux3,
		COALESCE((CAST(b.Aux4 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux4_Percent,
		COALESCE(b.Aux4, 0) As Aux4,
		COALESCE((CAST(b.Aux5 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux5_Percent,
		COALESCE(b.Aux5, 0) As Aux5,
		COALESCE((CAST(b.Aux6 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux6_Percent,
		COALESCE(b.Aux6, 0) As Aux6,
		COALESCE((CAST(b.Aux7 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux7_Percent,
		COALESCE(b.Aux7, 0) As Aux7,
		COALESCE((CAST(b.Aux8 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux8_Percent,
		COALESCE(b.Aux8, 0) As Aux8,
		COALESCE((CAST(b.Aux9 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux9_Percent,
		COALESCE(b.Aux9, 0) As Aux9,
		COALESCE(ROUND((b.TAux / b.STime)*100 , 2), 0) As Total_Aux_Percent,
		COALESCE(b.TAux, 0) As Total_Aux,
		COALESCE((CAST((b.Aux0 + b.Aux8) AS DECIMAL(38,30)) - b.AuxOut), 0) As Net_Aux,
		'N/F' As CCMS_Net_Time_Adjust,
		'N/F' As Absenteeism,
		COALESCE(b.ProdEff, 0) As Product_Efficiency,
		COALESCE((CAST(b.Billable AS DECIMAL(38,30)) / b.STime) * 100, 0) As Bill_to_Pay
		

		FROM (SELECT a.employee_ident, a.employee_common_name , b.phone_id FROM CCMS.dbo.CCMS_Employee a
				INNER JOIN CCMS.dbo.CCMS_PhoneIds b ON a.employee_ident = b.employee_ident) a

		INNER JOIN (SELECT b.row_date, b.logid, b.loc_id,
					   SUM(b.acdcalls) As HCalls,
					   SUM(b.transferred) As TCalls,
					   SUM(b.acwoutcalls) + SUM(auxoutcalls) As OCalls,
					   SUM(b.i_acdtime) As ATalk,
					   SUM(b.holdtime) As AHold,
					   SUM(b.i_acwtime) As AAcw,
					   SUM(b.i_stafftime) As STime,
					   SUM(b.i_availtime) As ATime,
					   SUM(b.i_auxouttime) As AuxOut,
					   SUM(b.ti_auxtime0) As Aux0,
					   SUM(b.ti_auxtime1) As Aux1,
					   SUM(b.ti_auxtime2) As Aux2,
					   SUM(b.ti_auxtime3) As Aux3,
					   SUM(b.ti_auxtime4) As Aux4,
					   SUM(b.ti_auxtime5) As Aux5,
					   SUM(b.ti_auxtime6) As Aux6,
					   SUM(b.ti_auxtime7) As Aux7,
					   SUM(b.ti_auxtime8) As Aux8,
					   SUM(b.ti_auxtime9) As Aux9,
					   SUM(b.ti_auxtime) As TAux,
					   (CAST((SUM(b.i_othertime) + SUM(b.i_acdaux_outtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)) * 100 As HoldPercent,
					   CAST((SUM(b.i_auxouttime) - SUM(b.i_acdaux_outtime) + SUM(b.i_acwtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)*100 As OBPercent,
					   SUM(b.i_acwtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_availtime) As IdleTime,
					   CAST((SUM(b.acdcalls) + SUM(b.i_availtime)) AS DECIMAL(38,30)) / SUM(b.i_stafftime)* 100 As ProdEff,
					   SUM(b.i_availtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_ringtime) + SUM(b.i_auxouttime) As Billable
					   
			FROM Avaya.dbo.dagent b
			WHERE b.split IN('1925','1924','1923','1922','1921','1920','1918','1919','1915','1916')
			GROUP BY b.row_date, b.logid, b.loc_id) b
		    ON a.phone_id = b.logid

			WHERE b.row_date = 
						CASE	
								WHEN @DateFilter IS NULL THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.Client_id = '4664')
								WHEN @DateFilter = '' THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.Client_id = '4664')
								ELSE @DateFilter
						END 
			
			GROUP BY  a.employee_common_name,b.loc_id, b.HCalls, b.TCalls, b.OCalls, b.ATalk, b.AHold, b.AAcw, b.STime, b.ATime, b.AuxOut, b.Aux0,
			b.Aux1 , b.Aux2, b.Aux3, b.Aux4, b.Aux5, b.Aux6, b.Aux7, b.Aux8, b.Aux9, b.TAux, b.HoldPercent, b.OBPercent, b.IdleTime, b.ProdEff, b.Billable
	";
	
$sboard_qry_cm = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter DATE
SELECT b.row_date AS row_date, a.mgr_name AS CM_Name, b.split AS split, b.loc_id AS Loc_id,
		COALESCE(b.HCalls, 0) As Handled_Calls,
		'N/F' As Short_Calls,
		COALESCE(b.TCalls, 0) As Transfered_Calls,
		COALESCE((CAST(b.TCalls AS DECIMAL(38,30)) / b.HCalls) * 100,0) As Transferred_Calls_Percent, 
		COALESCE(b.OCalls, 0) As Outband_Calls ,
		COALESCE((CAST(b.ATalk AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Talk,
		COALESCE((CAST(b.AHold AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Hold,
		COALESCE(b.HoldPercent, 0) As Hold_Percent,
		COALESCE(b.OBPercent, 0) As OB_Percent_of_Talk,
		COALESCE((CAST(b.AAcw AS DECIMAL(38,30)) / b.HCalls), 0) As Average_ACW,
		COALESCE((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls), 0) As AHT,
		IIF(b.HCalls < 1,0,230400) As Scheduled_Time,
		'N/F' As OT_Time,
		'N/F' As VTO_Time,
		COALESCE(b.STime, 0) As Staffed_Time ,
		COALESCE(b.ATime, 0) As Avail_Time,
		COALESCE(b.IdleTime, 0) As Idle_Time,
		COALESCE(SUM(((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls) * b.HCalls) / (((b.ATalk + b.AHold + b.AAcw) / b.HCalls) + b.ATime ) )*100, 0) As Occupancy,
		COALESCE((CAST(b.AuxOut AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux_Out_Percent,
		COALESCE(b.AuxOut, 0) As Aux_Out,
		COALESCE((CAST(b.Aux0 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux0_Percent,
		COALESCE(b.Aux0, 0) As Aux0,
		COALESCE((CAST(b.Aux1 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux1_Percent,
		COALESCE(b.Aux1, 0) As Aux1,
		COALESCE((CAST(b.Aux2 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux2_Percent,
		COALESCE(b.Aux2, 0) As Aux2,
		COALESCE((CAST(b.Aux3 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux3_Percent,
		COALESCE(b.Aux3, 0) As Aux3,
		COALESCE((CAST(b.Aux4 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux4_Percent,
		COALESCE(b.Aux4, 0) As Aux4,
		COALESCE((CAST(b.Aux5 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux5_Percent,
		COALESCE(b.Aux5, 0) As Aux5,
		COALESCE((CAST(b.Aux6 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux6_Percent,
		COALESCE(b.Aux6, 0) As Aux6,
		COALESCE((CAST(b.Aux7 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux7_Percent,
		COALESCE(b.Aux7, 0) As Aux7,
		COALESCE((CAST(b.Aux8 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux8_Percent,
		COALESCE(b.Aux8, 0) As Aux8,
		COALESCE((CAST(b.Aux9 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux9_Percent,
		COALESCE(b.Aux9, 0) As Aux9,
		COALESCE(ROUND((b.TAux / b.STime)*100 , 2), 0) As Total_Aux_Percent,
		COALESCE(b.TAux, 0) As Total_Aux,
		COALESCE((CAST((b.Aux0 + b.Aux8) AS DECIMAL(38,30)) - b.AuxOut), 0) As Net_Aux,
		'N/F' As CCMS_Net_Time_Adjust,
		'N/F' As Absenteeism,
		COALESCE(b.ProdEff, 0) As Product_Efficiency,
		COALESCE((CAST(b.Billable AS DECIMAL(38,30)) / b.STime) * 100, 0) As Bill_to_Pay
		

		FROM (SELECT a.employee_ident, a.employee_common_name , b.phone_id, a.manager_ident, a.manager_common_name as sup_name,
			a.manager_common_name AS acm_name,
			d.manager_common_name AS mgr_name
		
			FROM CCMS.dbo.CCMS_Employee a
				
				LEFT JOIN CCMS.dbo.CCMS_PhoneIds b ON b.employee_ident=a.employee_ident
				LEFT JOIN CCMS.dbo.CCMS_Employee c ON c.employee_ident=a.manager_ident
				LEFT JOIN CCMS.dbo.CCMS_Employee d ON d.employee_ident=c.manager_ident
		) a

		INNER JOIN (SELECT b.row_date, b.logid, b.split, b.loc_id,
					   SUM(b.acdcalls) As HCalls,
					   SUM(b.transferred) As TCalls,
					   SUM(b.acwoutcalls) + SUM(auxoutcalls) As OCalls,
					   SUM(b.i_acdtime) As ATalk,
					   SUM(b.holdtime) As AHold,
					   SUM(b.i_acwtime) As AAcw,
					   SUM(b.i_stafftime) As STime,
					   SUM(b.i_availtime) As ATime,
					   SUM(b.i_auxouttime) As AuxOut,
					   SUM(b.ti_auxtime0) As Aux0,
					   SUM(b.ti_auxtime1) As Aux1,
					   SUM(b.ti_auxtime2) As Aux2,
					   SUM(b.ti_auxtime3) As Aux3,
					   SUM(b.ti_auxtime4) As Aux4,
					   SUM(b.ti_auxtime5) As Aux5,
					   SUM(b.ti_auxtime6) As Aux6,
					   SUM(b.ti_auxtime7) As Aux7,
					   SUM(b.ti_auxtime8) As Aux8,
					   SUM(b.ti_auxtime9) As Aux9,
					   SUM(b.ti_auxtime) As TAux,
					   (CAST((SUM(b.i_othertime) + SUM(b.i_acdaux_outtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)) * 100 As HoldPercent,
					   CAST((SUM(b.i_auxouttime) - SUM(b.i_acdaux_outtime) + SUM(b.i_acwtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)*100 As OBPercent,
					   SUM(b.i_acwtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_availtime) As IdleTime,
					   CAST((SUM(b.acdcalls) + SUM(b.i_availtime)) AS DECIMAL(38,30)) / SUM(b.i_stafftime)* 100 As ProdEff,
					   SUM(b.i_availtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_ringtime) + SUM(b.i_auxouttime) As Billable
					   
			FROM Avaya.dbo.dagent b
			GROUP BY b.row_date, b.logid, b.split, b.loc_id) b
		    ON a.phone_id = b.logid

			WHERE b.row_date = 
						CASE	
								WHEN @DateFilter IS NULL THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
								WHEN @DateFilter = '' THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
								ELSE  @DateFilter
						END 
			
			AND b.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919')  
			

			GROUP BY  b.row_date, a.mgr_name, b.split, b.loc_id, b.HCalls, b.TCalls, b.OCalls, b.ATalk, b.AHold, b.AAcw, b.STime, b.ATime, b.AuxOut, b.Aux0,
			b.Aux1 , b.Aux2, b.Aux3, b.Aux4, b.Aux5, b.Aux6, b.Aux7, b.Aux8, b.Aux9, b.TAux, b.HoldPercent, b.OBPercent, b.IdleTime, b.ProdEff, b.Billable
" ;
	
$sboard_qry_accm = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter DATE
SELECT b.row_date AS row_date, a.acm_name AS ACCM_Name , b.split AS split, b.loc_id AS loc_id,
		COALESCE(b.HCalls, 0) As Handled_Calls,
		'N/F' As Short_Calls,
		COALESCE(b.TCalls, 0) As Transfered_Calls,
		COALESCE((CAST(b.TCalls AS DECIMAL(38,30)) / b.HCalls) * 100,0) As Transferred_Calls_Percent, 
		COALESCE(b.OCalls, 0) As Outband_Calls ,
		COALESCE((CAST(b.ATalk AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Talk,
		COALESCE((CAST(b.AHold AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Hold,
		COALESCE(b.HoldPercent, 0) As Hold_Percent,
		COALESCE(b.OBPercent, 0) As OB_Percent_of_Talk,
		COALESCE((CAST(b.AAcw AS DECIMAL(38,30)) / b.HCalls), 0) As Average_ACW,
		COALESCE((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls), 0) As AHT,
		IIF(b.HCalls < 1,0,230400) As Scheduled_Time,
		'N/F' As OT_Time,
		'N/F' As VTO_Time,
		COALESCE(b.STime, 0) As Staffed_Time ,
		COALESCE(b.ATime, 0) As Avail_Time,
		COALESCE(b.IdleTime, 0) As Idle_Time,
		COALESCE(SUM(((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls) * b.HCalls) / (((b.ATalk + b.AHold + b.AAcw) / b.HCalls) + b.ATime ) )*100, 0) As Occupancy,
		COALESCE((CAST(b.AuxOut AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux_Out_Percent,
		COALESCE(b.AuxOut, 0) As Aux_Out,
		COALESCE((CAST(b.Aux0 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux0_Percent,
		COALESCE(b.Aux0, 0) As Aux0,
		COALESCE((CAST(b.Aux1 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux1_Percent,
		COALESCE(b.Aux1, 0) As Aux1,
		COALESCE((CAST(b.Aux2 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux2_Percent,
		COALESCE(b.Aux2, 0) As Aux2,
		COALESCE((CAST(b.Aux3 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux3_Percent,
		COALESCE(b.Aux3, 0) As Aux3,
		COALESCE((CAST(b.Aux4 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux4_Percent,
		COALESCE(b.Aux4, 0) As Aux4,
		COALESCE((CAST(b.Aux5 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux5_Percent,
		COALESCE(b.Aux5, 0) As Aux5,
		COALESCE((CAST(b.Aux6 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux6_Percent,
		COALESCE(b.Aux6, 0) As Aux6,
		COALESCE((CAST(b.Aux7 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux7_Percent,
		COALESCE(b.Aux7, 0) As Aux7,
		COALESCE((CAST(b.Aux8 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux8_Percent,
		COALESCE(b.Aux8, 0) As Aux8,
		COALESCE((CAST(b.Aux9 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux9_Percent,
		COALESCE(b.Aux9, 0) As Aux9,
		COALESCE(ROUND((b.TAux / b.STime)*100 , 2), 0) As Total_Aux_Percent,
		COALESCE(b.TAux, 0) As Total_Aux,
		COALESCE((CAST((b.Aux0 + b.Aux8) AS DECIMAL(38,30)) - b.AuxOut), 0) As Net_Aux,
		'N/F' As CCMS_Net_Time_Adjust,
		'N/F' As Absenteeism,
		COALESCE(b.ProdEff, 0) As Product_Efficiency,
		COALESCE((CAST(b.Billable AS DECIMAL(38,30)) / b.STime) * 100, 0) As Bill_to_Pay
		

		FROM (SELECT a.employee_ident, a.employee_common_name , b.phone_id, a.manager_ident, a.manager_common_name as sup_name,
			a.manager_common_name AS acm_name,
			d.manager_common_name AS mgr_name
		
			FROM CCMS.dbo.CCMS_Employee a
				
				LEFT JOIN CCMS.dbo.CCMS_PhoneIds b ON b.employee_ident=a.employee_ident
				LEFT JOIN CCMS.dbo.CCMS_Employee c ON c.employee_ident=a.manager_ident
				LEFT JOIN CCMS.dbo.CCMS_Employee d ON d.employee_ident=c.manager_ident
		) a

		INNER JOIN (SELECT b.row_date, b.logid, b.split, b.loc_id,
					   SUM(b.acdcalls) As HCalls,
					   SUM(b.transferred) As TCalls,
					   SUM(b.acwoutcalls) + SUM(auxoutcalls) As OCalls,
					   SUM(b.i_acdtime) As ATalk,
					   SUM(b.holdtime) As AHold,
					   SUM(b.i_acwtime) As AAcw,
					   SUM(b.i_stafftime) As STime,
					   SUM(b.i_availtime) As ATime,
					   SUM(b.i_auxouttime) As AuxOut,
					   SUM(b.ti_auxtime0) As Aux0,
					   SUM(b.ti_auxtime1) As Aux1,
					   SUM(b.ti_auxtime2) As Aux2,
					   SUM(b.ti_auxtime3) As Aux3,
					   SUM(b.ti_auxtime4) As Aux4,
					   SUM(b.ti_auxtime5) As Aux5,
					   SUM(b.ti_auxtime6) As Aux6,
					   SUM(b.ti_auxtime7) As Aux7,
					   SUM(b.ti_auxtime8) As Aux8,
					   SUM(b.ti_auxtime9) As Aux9,
					   SUM(b.ti_auxtime) As TAux,
					   (CAST((SUM(b.i_othertime) + SUM(b.i_acdaux_outtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)) * 100 As HoldPercent,
					   CAST((SUM(b.i_auxouttime) - SUM(b.i_acdaux_outtime) + SUM(b.i_acwtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)*100 As OBPercent,
					   SUM(b.i_acwtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_availtime) As IdleTime,
					   CAST((SUM(b.acdcalls) + SUM(b.i_availtime)) AS DECIMAL(38,30)) / SUM(b.i_stafftime)* 100 As ProdEff,
					   SUM(b.i_availtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_ringtime) + SUM(b.i_auxouttime) As Billable
					   
			FROM Avaya.dbo.dagent b
			GROUP BY b.row_date, b.logid, b.split, b.loc_id) b
		    ON a.phone_id = b.logid

			WHERE b.row_date = 
						CASE	
								WHEN @DateFilter IS NULL THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
								WHEN @DateFilter = '' THEN (SELECT MAX(a.row_date) FROM Avaya.dbo.hsplit a WHERE a.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919'))
								ELSE  @DateFilter
						END 
			
			AND b.split IN('1925','1924','1921','1918','1915','1916', '1922', '1920', '1919')  
			

			GROUP BY  b.row_date, a.acm_name, b.split, b.loc_id, b.HCalls, b.TCalls, b.OCalls, b.ATalk, b.AHold, b.AAcw, b.STime, b.ATime, b.AuxOut, b.Aux0,
			b.Aux1 , b.Aux2, b.Aux3, b.Aux4, b.Aux5, b.Aux6, b.Aux7, b.Aux8, b.Aux9, b.TAux, b.HoldPercent, b.OBPercent, b.IdleTime, b.ProdEff, b.Billable
";
	
$sboard_qry_supp = "
SET ARITHABORT OFF 
SET ANSI_WARNINGS OFF
DECLARE @DateFilter DATE
SELECT a.fullname AS Sup_Name,		
		COALESCE(b.HCalls, 0) As Handled_Calls,
		'N/F' As Short_Calls,
		COALESCE(b.TCalls, 0) As Transfered_Calls,
		COALESCE((CAST(b.TCalls AS DECIMAL(38,30)) / b.HCalls) * 100,0) As Transferred_Calls_Percent, 
		COALESCE(b.OCalls, 0) As Outband_Calls ,
		COALESCE((CAST(b.ATalk AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Talk,
		COALESCE((CAST(b.AHold AS DECIMAL(38,30)) / b.HCalls), 0) As Average_Hold,
		COALESCE(b.HoldPercent, 0) As Hold_Percent,
		COALESCE(b.OBPercent, 0) As OB_Percent_of_Talk,
		COALESCE((CAST(b.AAcw AS DECIMAL(38,30)) / b.HCalls), 0) As Average_ACW,
		COALESCE((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls), 0) As AHT,
		IIF(b.HCalls < 1,0,230400) As Scheduled_Time,
		'N/F' As OT_Time,
		'N/F' As VTO_Time,
		COALESCE(b.STime, 0) As Staffed_Time ,
		COALESCE(b.ATime, 0) As Avail_Time,
		COALESCE(b.IdleTime, 0) As Idle_Time,
		COALESCE(SUM(((CAST((b.ATalk + b.AHold + b.AAcw) AS DECIMAL(38,30)) / b.HCalls) * b.HCalls) / (((b.ATalk + b.AHold + b.AAcw) / b.HCalls) + b.ATime ) )*100, 0) As Occupancy,
		COALESCE((CAST(b.AuxOut AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux_Out_Percent,
		COALESCE(b.AuxOut, 0) As Aux_Out,
		COALESCE((CAST(b.Aux0 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux0_Percent,
		COALESCE(b.Aux0, 0) As Aux0,
		COALESCE((CAST(b.Aux1 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux1_Percent,
		COALESCE(b.Aux1, 0) As Aux1,
		COALESCE((CAST(b.Aux2 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux2_Percent,
		COALESCE(b.Aux2, 0) As Aux2,
		COALESCE((CAST(b.Aux3 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux3_Percent,
		COALESCE(b.Aux3, 0) As Aux3,
		COALESCE((CAST(b.Aux4 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux4_Percent,
		COALESCE(b.Aux4, 0) As Aux4,
		COALESCE((CAST(b.Aux5 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux5_Percent,
		COALESCE(b.Aux5, 0) As Aux5,
		COALESCE((CAST(b.Aux6 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux6_Percent,
		COALESCE(b.Aux6, 0) As Aux6,
		COALESCE((CAST(b.Aux7 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux7_Percent,
		COALESCE(b.Aux7, 0) As Aux7,
		COALESCE((CAST(b.Aux8 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux8_Percent,
		COALESCE(b.Aux8, 0) As Aux8,
		COALESCE((CAST(b.Aux9 AS DECIMAL(38,30)) / b.STime)*100, 0) As Aux9_Percent,
		COALESCE(b.Aux9, 0) As Aux9,
		COALESCE(ROUND((b.TAux / b.STime)*100 , 2), 0) As Total_Aux_Percent,
		COALESCE(b.TAux, 0) As Total_Aux,
		COALESCE((CAST((b.Aux0 + b.Aux8) AS DECIMAL(38,30)) - b.AuxOut), 0) As Net_Aux,
		'N/F' As CCMS_Net_Time_Adjust,
		'N/F' As Absenteeism,
		COALESCE(b.ProdEff, 0) As Product_Efficiency,
		COALESCE((CAST(b.Billable AS DECIMAL(38,30)) / b.STime) * 100, 0) As Bill_to_Pay		

		FROM (SELECT a.employee_ident, CONCAT(a.employee_first_name, ' ',a.employee_middle_name,' ',a.employee_last_name) as fullname, a.status, a.program_ident, a.program_full_name, b.phone_id, a.manager_ident, a.manager_common_name as sup_name,
			a.manager_common_name AS acm_name,
			d.manager_common_name AS mgr_name
		
			FROM CCMS.dbo.CCMS_Employee a
				
				LEFT JOIN CCMS.dbo.CCMS_PhoneIds b ON b.employee_ident=a.employee_ident
				LEFT JOIN CCMS.dbo.CCMS_Employee c ON c.employee_ident=a.manager_ident
				LEFT JOIN CCMS.dbo.CCMS_Employee d ON d.employee_ident=c.manager_ident
			WHERE a.status = 'Active' AND a.program_ident = '98748'
		) a

				INNER JOIN (SELECT b.row_date, b.logid, 
							   SUM(b.acdcalls) As HCalls,
							   SUM(b.transferred) As TCalls,
							   SUM(b.acwoutcalls) + SUM(auxoutcalls) As OCalls,
							   SUM(b.i_acdtime) As ATalk,
							   SUM(b.holdtime) As AHold,
							   SUM(b.i_acwtime) As AAcw,
							   SUM(b.i_stafftime) As STime,
							   SUM(b.i_availtime) As ATime,
							   SUM(b.i_auxouttime) As AuxOut,
							   SUM(b.ti_auxtime0) As Aux0,
							   SUM(b.ti_auxtime1) As Aux1,
							   SUM(b.ti_auxtime2) As Aux2,
							   SUM(b.ti_auxtime3) As Aux3,
							   SUM(b.ti_auxtime4) As Aux4,
							   SUM(b.ti_auxtime5) As Aux5,
							   SUM(b.ti_auxtime6) As Aux6,
							   SUM(b.ti_auxtime7) As Aux7,
							   SUM(b.ti_auxtime8) As Aux8,
							   SUM(b.ti_auxtime9) As Aux9,
							   SUM(b.ti_auxtime) As TAux,
							   (CAST((SUM(b.i_othertime) + SUM(b.i_acdaux_outtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)) * 100 As HoldPercent,
							   CAST((SUM(b.i_auxouttime) - SUM(b.i_acdaux_outtime) + SUM(b.i_acwtime)) AS DECIMAL(38,30)) / SUM(b.i_acdtime)*100 As OBPercent,
							   SUM(b.i_acwtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_availtime) As IdleTime,
							   CAST((SUM(b.acdcalls) + SUM(b.i_availtime)) AS DECIMAL(38,30)) / SUM(b.i_stafftime)* 100 As ProdEff,
							   SUM(b.i_availtime) + SUM(b.i_acdtime) + SUM(b.holdtime) + SUM(b.i_ringtime) + SUM(b.i_auxouttime) As Billable
					   
					FROM Avaya.dbo.hagent b
					WHERE b.split IN('1925','1924','1923','1922','1921','1920','1918','1919','1915','1916') AND b.Client_id = '4664'
					GROUP BY b.row_date, b.logid) b
					ON a.phone_id = b.logid

			WHERE b.row_date = 
						CASE	
								WHEN @DateFilter IS NULL THEN (SELECT MAX(row_date) FROM Avaya.dbo.dsplit )
								WHEN @DateFilter = '' THEN (SELECT MAX(row_date) FROM Avaya.dbo.dsplit )
								ELSE @DateFilter
						END 
			
			GROUP BY a.fullname, a.employee_ident, a.status,a.program_full_name, a.program_ident, b.HCalls, b.TCalls, b.OCalls, b.ATalk, b.AHold, b.AAcw, b.STime, b.ATime, b.AuxOut, b.Aux0,
			b.Aux1 , b.Aux2, b.Aux3, b.Aux4, b.Aux5, b.Aux6, b.Aux7, b.Aux8, b.Aux9, b.TAux, b.HoldPercent, b.OBPercent, b.IdleTime, b.ProdEff, b.Billable
			ORDER BY a.fullname
";	

if (basename(__FILE__) == basename($_SERVER['REQUEST_URI'])){
	
	$date_filter = '';
	
	require('../../../../data/servers.php');
	
	$db_ccms="Avaya";
	
	$ccms_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_ccms, $mck_srv['user'], $mck_srv['pass']);
	$ccms_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	include('../../lib/extras.php');
	
	
	$data_res = $ccms_db->prepare($sboard_qry);
	$data_res->execute();
	$data_res = $data_res->fetchAll();

	$cnt_res = $ccms_db->prepare($sboard_qry);
	$cnt_res->execute();
	$cnt_res = $cnt_res->columnCount();
	
	print '<table>
		<thead>';
		print'<tr>';
			foreach($sboard_heads as $row_heads){
					print'<th>'.$row_heads.'</th>';
			}
		print'</tr>';
	print'</thead>
		<tbody>';
			foreach($data_res as $row_data){
				print'<tr>';
					for($x=0; $x<$cnt_res; $x++){
						print'<td>'.($row_data[$x]).'</td>';
					}
				print'</tr>';
			}
		print'</tbody>
	</table>';
}

?>