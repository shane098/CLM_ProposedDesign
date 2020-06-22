<?php
    $date_filter = '';
    $time_filter = '';
    $site_filter = '';
    $community_filter = '';
    $market_filter = '';
    $kpi_filter01 = '';
    $kpi_filter02 = '';
    $datetime_from = '';
    $datetime_to = '';

    //require('data/1031191200-lhenard/data_staff_comparison.php');
    include("clients/".$_SESSION['project']."//data/1031191200-lhenard/data_staff_comparison.php");
		
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $date_filter = $_POST['date_picker'];
        $kpi_filter01 = $_POST['kpi_01_picker'];
        $kpi_filter02 = $_POST['kpi_02_picker'];
    }else {
        $kpi_filter01 = 'Forecast';
        $kpi_filter02 = 'Actual';
    }
	
?>

<div class="row">
    <div class="col-12">
        <div id="line-graph" style="width:100%; max-height:600px; height:100vh;"></div>
    </div>
</div>

<!-- line-graph -->
<script type="text/javascript">
    AmCharts.makeChart("line-graph",
        {
            "type": "serial", "categoryField": "Forecast_Date", "dataDateFormat": "YYYY-MM-DD HH:NN", "theme": "dark",
            "categoryAxis": {"minPeriod": "mm","parseDates": true},
            "chartCursor": {"enabled": true,"categoryBalloonDateFormat": "JJ:NN"},
            "chartScrollbar": {"enabled": true},
            "trendLines": [],
            "graphs": [
                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#FF0074","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#FF0074","fontSize": -1,
                "id": "AmGraph-1","lineColor": "#FF0074","lineThickness": 2,"title": "<?php print $kpi_filter01; ?>","valueField": "KPI-1","type": "smoothedLine"},

                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#1E90FF","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#1E90FF","fontSize": -1,
                "id": "AmGraph-2","lineColor": "#1E90FF","lineThickness": 2,"title": "<?php print $kpi_filter02; ?>","valueField": "KPI-2","type": "smoothedLine"},
            ],
            "guides": [],
            "valueAxes": [
                {"id": "ValueAxis-1","title": "Volume"}
            ],
            "allLabels": [],
            "balloon": {},
            "legend": {"enabled": true, "align" : "center"},
            "titles": [
                {"id": "Title-1","size": 15,"text": "Staffing Comparison between <?php print $kpi_filter01; ?> and <?php print $kpi_filter02; ?> FTE"},
                {"bold": false,"italic": true,"id": "Title-2","size": 11,"text": "(Click each legend to exclude in display)"}
            ],
            "dataProvider" : [ 
                <?php
                    // $res = $db->prepare('SET @KPIFilter01="'.$kpi_filter01.'", @KPIFilter02="'.$kpi_filter02.'", @DateFilter="'.$date_filter.'"; ');
                    // $res->execute();
					
					$res = $avaya_db->prepare($staff_comp_qry);

                    $res->execute();
                    
                    while($row = $res->fetch(PDO::FETCH_ASSOC)){
                        print '{ 
                            "Forecast_Date": "'.$row['Forecast_DateTime'].'",
                            "KPI-1": "'.$row['Forecast'].'",
                            "KPI-2": "'.number_format($row['Actual'], 2).'",
                            },';
                        }
                  
                ?>
            ]

        });
</script>