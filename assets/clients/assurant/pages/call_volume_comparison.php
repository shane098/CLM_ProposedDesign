<?php
    $date_filter = '';
    $time_filter = '';
    $site_filter = '';
    $community_filter = '';
    $market_filter = '';
    // $kpi_filter01 = '';
    // $kpi_filter02 = '';
    $datetime_from = '';
    $datetime_to = '';

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $date_filter = $_POST['date_picker'];
        // $kpi_filter01 = $_POST['kpi_01_picker'];
        // $kpi_filter02 = $_POST['kpi_02_picker'];
    }else {
        // $kpi_filter01 = 'Forecasted Calls';
        // $kpi_filter02 = 'Handled Calls';
    }
	
    //require('data/1031191200-lhenard/data_cvol_comparison.php');
    
    include("clients/".$_SESSION['project']."//data/1031191200-lhenard/data_cvol_comparison.php");

?>

<div class="row">
    <div class="col-6">
        <div id="line-graph" style="width:100%; max-height:450px; height:100vh;"></div>
		<div id="line-graph11" style="width:100%; max-height:450px; height:100vh;"></div>
    </div>
	<div class="col-6">
        <div id="line-graph2" style="width:100%; max-height:450px; height:100vh;"></div>
		<div id="line-graph21" style="width:100%; max-height:450px; height:100vh;"></div>
    </div>
</div>

<!-- line-graph -->
<script type="text/javascript">

// Interval
    chart = AmCharts.makeChart("line-graph",
        {
            "type": "serial", "categoryField": "DateTime", "dataDateFormat": "YYYY-MM-DD HH:NN", "theme": "dark",
            "categoryAxis": {"minPeriod": "mm","parseDates": true, "title": "Time"},
            "chartCursor": {"enabled": true,"categoryBalloonDateFormat": "JJ:NN"},
            "chartScrollbar": {"enabled": true},
            "trendLines": [],
            "graphs": [
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#FF0074","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#FF0074","fontSize": -1,
                "id": "AmGraph-1","lineColor": "#FF0074","lineThickness": 2,"title": "Forecasted","valueField": "Calls_Forecasted","type": "smoothedLine"},
				
                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#1E90FF","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#1E90FF","fontSize": -1,
                "id": "AmGraph-2","lineColor": "#1E90FF","lineThickness": 2,"title": "Offered","valueField": "Calls_Offered","type": "smoothedLine"},

                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#10c469","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#10c469","fontSize": -1,
                "id": "AmGraph-3","lineColor": "#10c469","lineThickness": 2,"title": "Handled","valueField": "Calls_Handled","type": "smoothedLine"},
				
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#064726","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#064726","fontSize": -1,
                "id": "AmGraph-4","lineColor": "#064726","lineThickness": 2,"title": "Acceptable","valueField": "Acceptable_Calls","type": "smoothedLine"}
            ],
            "guides": [],
            "valueAxes": [
                {"id": "ValueAxis-1","title": "Volume"}
            ],
            "allLabels": [],
            "balloon": {},
            "legend": {"enabled": true, "align" : "center"},
            "titles": [
                {"id": "Title-1","size": 15,"text": "Interval"}, 
                {"bold": false,"italic": true,"id": "Title-2","size": 11,"text": "(Click each legend to exclude in display)"}
            ],
            "dataProvider" : [ 
                <?php
				 
				
                    // $res = $avaya_db->prepare("DECLARE @DateFilter DATE SET @DateFilter = '".$date_filter."' "); //SET @CommunityFilter= ".$community_filter.";
					// $res->execute();
					
					$res = $avaya_db->prepare($cvol_interval_qry);

                    $res->execute();
                    
                    while($row = $res->fetch(PDO::FETCH_ASSOC)){
                        print '{ 
							"DateTime": "'.$row['dtime'].'",
							"Calls_Forecasted": "'.$row['Forecast'].'",
                            "Calls_Offered": "'.$row['Calls_Offered'].'",
                            "Calls_Handled": "'.$row['Calls_Answered'].'",
							"Acceptable_Calls": "'.$row['Acceptable_Calls'].'",
                            },';
                        }
                ?>
            ]
        });
		
		//Weekly
		chart = AmCharts.makeChart("line-graph11",
        {
            "type": "serial", "categoryField": "DateTime", "dataDateFormat": "YYYY-MM-DD HH:NN", "theme": "dark",
            "categoryAxis": {"minPeriod": "mm","parseDates": true, "title": "Week Start"},
            "chartCursor": {"enabled": true,"categoryBalloonDateFormat": "MMM DD"},
            "chartScrollbar": {"enabled": true},
            "trendLines": [],
            "graphs": [
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#FF0074","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#FF0074","fontSize": -1,
                "id": "AmGraph-1","lineColor": "#FF0074","lineThickness": 2,"title": "Forecasted","valueField": "Calls_Forecasted","type": "smoothedLine"},
				
                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#1E90FF","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#1E90FF","fontSize": -1,
                "id": "AmGraph-2","lineColor": "#1E90FF","lineThickness": 2,"title": "Offered","valueField": "Calls_Offered","type": "smoothedLine"},

                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#10c469","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#10c469","fontSize": -1,
                "id": "AmGraph-3","lineColor": "#10c469","lineThickness": 2,"title": "Handled","valueField": "Calls_Handled","type": "smoothedLine"},
				
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#064726","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#064726","fontSize": -1,
                "id": "AmGraph-4","lineColor": "#064726","lineThickness": 2,"title": "Acceptable","valueField": "Acceptable_Calls","type": "smoothedLine"}
            ],
            "guides": [],
            "valueAxes": [
                {"id": "ValueAxis-1","title": "Volume"}
            ],
            "allLabels": [],
            "balloon": {},
            "legend": {"enabled": true, "align" : "center"},
            "titles": [
                {"id": "Title-1","size": 15,"text": "Weekly "}, 
                {"bold": false,"italic": true,"id": "Title-2","size": 11,"text": "(Click each legend to exclude in display)"}
            ],
            "dataProvider" : [ 
                <?php
                    // $res = $avaya_db->prepare("DECLARE @DateFilter DATE SET @DateFilter = '".$date_filter."' "); //SET @CommunityFilter= ".$community_filter.";
					// $res->execute();
					
					$res = $avaya_db->prepare($cvol_week_qry);

                    $res->execute();
                    
                    while($row = $res->fetch(PDO::FETCH_ASSOC)){
                        print '{ 
							"DateTime": "'.$row['dtime'].'",
							"Calls_Forecasted": "'.$row['Forecast'].'",
                            "Calls_Offered": "'.$row['Calls_Offered'].'",
                            "Calls_Handled": "'.$row['Calls_Answered'].'",
							"Acceptable_Calls": "'.$row['Acceptable_Calls'].'",
                            },';
                        }
                ?>
            ]
        });
		//DAILY
		 chart = AmCharts.makeChart("line-graph2",
        {
            "type": "serial", "categoryField": "DateTime", "dataDateFormat": "YYYY-MM-DD HH:NN", "theme": "dark",
            "categoryAxis": {"minPeriod": "mm","parseDates": true, "title": "Date"},
            "chartCursor": {"enabled": true,"categoryBalloonDateFormat": "MMM DD"},
            "chartScrollbar": {"enabled": true},
            "trendLines": [],
            "graphs": [
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#FF0074","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#FF0074","fontSize": -1,
                "id": "AmGraph-1","lineColor": "#FF0074","lineThickness": 2,"title": "Forecasted","valueField": "Calls_Forecasted","type": "smoothedLine"},
				
                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#1E90FF","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#1E90FF","fontSize": -1,
                "id": "AmGraph-2","lineColor": "#1E90FF","lineThickness": 2,"title": "Offered","valueField": "Calls_Offered","type": "smoothedLine"},

                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#10c469","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#10c469","fontSize": -1,
                "id": "AmGraph-3","lineColor": "#10c469","lineThickness": 2,"title": "Handled","valueField": "Calls_Handled","type": "smoothedLine"},
				
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#064726","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#064726","fontSize": -1,
                "id": "AmGraph-4","lineColor": "#064726","lineThickness": 2,"title": "Acceptable","valueField": "Acceptable_Calls","type": "smoothedLine"}
            ],
            "guides": [],
            "valueAxes": [
                {"id": "ValueAxis-1","title": "Volume"}
            ],
            "allLabels": [],
            "balloon": {},
            "legend": {"enabled": true, "align" : "center"},
            "titles": [
                {"id": "Title-1","size": 15,"text": "Daily "}, 
                {"bold": false,"italic": true,"id": "Title-2","size": 11,"text": "(Click each legend to exclude in display)"}
            ],
            "dataProvider" : [ 
                <?php				
                    // $res = $avaya_db->prepare("DECLARE @DateFilter DATE SET @DateFilter = '".$date_filter."' "); //SET @CommunityFilter= ".$community_filter.";
					// $res->execute();
					
					$res = $avaya_db->prepare($cvol_daily_qry);

                    $res->execute();
                    
                    while($row = $res->fetch(PDO::FETCH_ASSOC)){
                        print '{ 
							"DateTime": "'.$row['row_date'].'",
							"Calls_Forecasted": "'.$row['Forecast'].'",
                            "Calls_Offered": "'.$row['Calls_Offered'].'",
                            "Calls_Handled": "'.$row['Calls_Answered'].'",
							"Acceptable_Calls": "'.$row['Acceptable_Calls'].'",
                            },';
                        }
                ?>
            ]
        });
		//MONTHLY
		chart = AmCharts.makeChart("line-graph21",
        {
            "type": "serial", "categoryField": "DateTime", "dataDateFormat": "YYYY-MM-DD HH:NN", "theme": "dark",
            "categoryAxis": {"minPeriod": "mm","parseDates": true, "title": "Month"},
            "chartCursor": {"enabled": true,"categoryBalloonDateFormat": "MMM"},
            "chartScrollbar": {"enabled": true},
            "trendLines": [],
            "graphs": [
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#FF0074","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#FF0074","fontSize": -1,
                "id": "AmGraph-1","lineColor": "#FF0074","lineThickness": 2,"title": "Forecasted","valueField": "Calls_Forecasted","type": "smoothedLine"},
				
                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#1E90FF","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#1E90FF","fontSize": -1,
                "id": "AmGraph-2","lineColor": "#1E90FF","lineThickness": 2,"title": "Offered","valueField": "Calls_Offered","type": "smoothedLine"},

                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#10c469","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#10c469","fontSize": -1,
                "id": "AmGraph-3","lineColor": "#10c469","lineThickness": 2,"title": "Handled","valueField": "Calls_Handled","type": "smoothedLine"},
				
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#064726","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#064726","fontSize": -1,
                "id": "AmGraph-4","lineColor": "#064726","lineThickness": 2,"title": "Acceptable","valueField": "Acceptable_Calls","type": "smoothedLine"}
            ],
            "guides": [],
            "valueAxes": [
                {"id": "ValueAxis-1","title": "Volume"}
            ],
            "allLabels": [],
            "balloon": {},
            "legend": {"enabled": true, "align" : "center"},
            "titles": [
                {"id": "Title-1","size": 15,"text": "Monthly "}, 
                {"bold": false,"italic": true,"id": "Title-2","size": 11,"text": "(Click each legend to exclude in display)"}
            ],
            "dataProvider" : [ 
                <?php
                 
					
					$res = $avaya_db->prepare($cvol_month_qry);

                    $res->execute();
                    
                    while($row = $res->fetch(PDO::FETCH_ASSOC)){
                        print '{ 
							"DateTime": "'.$row['dtime'].'",
							"Calls_Forecasted": "'.$row['Forecast'].'",
                            "Calls_Offered": "'.$row['Calls_Offered'].'",
                            "Calls_Handled": "'.$row['Calls_Answered'].'",
							"Acceptable_Calls": "'.$row['Acceptable_Calls'].'",
                            },';
                        }
                ?>
            ]
        });
</script>