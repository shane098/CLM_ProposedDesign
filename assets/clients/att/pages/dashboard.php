<?php   

    $date_filter = '';
    $time_filter = '';
    $community_filter = '';
	
	$aht_goal = 700;
    
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        if(isset($_POST['filter'])){
            $date_filter = $_POST['date_picker'];
        }
    } else {
		$date_filter = '';
	}
	
    //require('data/1031191200-lhenard/data_dashboard.php');
    include("clients/".$_SESSION['project']."//data/1031191200-lhenard/data_dashboard.php");
	
	
    // $res = $avaya_db->prepare("DECLARE @DateFilter DATE = '".$date_filter."' "); //SET @CommunityFilter= ".$community_filter.";
    // $res->execute(); 

    $res = $avaya_db->prepare($dash_pie_qry);

    $res->execute();
	
    while($row = $res->fetch(PDO::FETCH_ASSOC)){
		$date = $row['row_date'];
        $callsAnswered = $row['Calls_Answered'];
        $callsOffered = $row['Calls_Offered'];
		$callsAcceptable = $row['Acceptable_Calls'];
        $callsForecasted = $row['Forecast'];
		$staffedTime = $row['Total_Staff_Hours'];
		$aht = $row['AHT'];
		$availTime = $row['Avail_Time'];
		$Transferred = $row['Transferred'];
    }

?>

<div class="row">
    <div class="col-12">
        <div id="line-graph" style="width:100%; max-height:600px; height:100vh;"></div>
    </div>
</div>

<div class="row" style="margin-bottom: 20px;">
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                <h5 id="toggle-ans_rate" onMouseOver="changePointer(this)">Answer Rate</h5>
                <small>Handled: <b><?php print number_format($callsAnswered,0); ?></b></small><br/>
                <small>Offered: <b><?php print number_format($callsOffered,0); ?></b></small>
            </div>
            <div class="card-body">
                <div id="pie1"></div>
            </div>
        </div>
    </div>
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                <h5 id="toggle-srv_lvl" onMouseOver="changePointer(this)">Service Level</h5>
                <small>Acceptable: <b><?php print number_format($callsAcceptable,0); ?></b></small><br/>
                <small>Offered: <b><?php print number_format($callsOffered,0); ?></b></small>
            </div>
            <div class="card-body">
                <div id="pie2"></div>	
            </div>
        </div>
    </div>
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                <h5 id="toggle-aht" onMouseOver="changePointer(this)">AHT</h5>
                <small>Actual: <b><?php print number_format($aht,0); ?></b></small><br/>
                <small>Goal: <b><?php print number_format($aht_goal,0); ?></b></small>
            </div>
            <div class="card-body">
                <div id="pie3"></div>
            </div>
        </div>
    </div>    
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                <h5 id="toggle-ptf" onMouseOver="changePointer(this)">PTF</h5>
                <small>Offered: <b><?php print number_format($callsOffered,0); ?></b></small><br/>
                <small>Forecasted: <b><?php if($callsForecasted == 0){ print 'No Forecast Data'; } else{ print checkVal($callsForecasted); }  ?></b></small>
            </div>
            <div class="card-body">
                <div id="pie4"></div>
            </div>
        </div>
    </div>
    <div class="col-2">
        <div class="card">
            <div class="card-header">
                <h5 id="toggle-trans_rate" onMouseOver="changePointer(this)">Transfer Rate</h5>
                <small>Transferred: <b><?php print number_format($Transferred,0); ?></b></small><br/>
                <small>Answered: <b><?php print number_format($callsAnswered,0);?></b></small>
            </div>
            <div class="card-body">
                <div id="pie5"></div>
            </div>
        </div>
    </div>
	<div class="col-2">
        <div class="card">
            <div class="card-header">
                <h5 id="toggle-occ" onMouseOver="changePointer(this)">Occupancy</h5>
                <small>Variance: <b><?php if(((($callsAnswered * $aht)/checkVal(($callsAnswered * $aht)+$availTime))*100/.85) == 0){ print 0;}else{ print number_format((($callsAnswered * $aht)/checkVal(($callsAnswered * $aht)+$availTime))*100/.85,2).'%';} ?> </b></small><br/> <!--//print number_format((($callsAnswered * $aht)/(($callsAnswered * $aht)+$availTime)*100)/.85,2)-->
                <small>Goal: <b><?php print 85; ?>%</b></small>
            </div>
            <div class="card-body">
                <div id="pie6"></div>
            </div>
        </div>
    </div>
</div>

<script>

	function changePointer(x) {
		x.style.cursor = "pointer";
	};
	
	var chart;
	
	Array.prototype.forEach.call(
	  
	  document.querySelectorAll('#toggle-ans_rate'),
		  function (button) {
			button.addEventListener('click', function() {
				
				chart.hideGraph(chart.graphs[0]);
				chart.showGraph(chart.graphs[1]);
				chart.showGraph(chart.graphs[2]);
				chart.hideGraph(chart.graphs[3]);
				
				document.getElementById("toggle-ans_rate").style.fontWeight = "bold";
				document.getElementById("toggle-srv_lvl").style.fontWeight = "normal";
				document.getElementById("toggle-ptf").style.fontWeight = "normal";
				document.getElementById("toggle-aht").style.fontWeight = "normal";
			  
			});
		}
	);
	  
	Array.prototype.forEach.call(
	  document.querySelectorAll('#toggle-srv_lvl'),
		function (button) {
			button.addEventListener('click', function() {
				
				chart.hideGraph(chart.graphs[0]);
				chart.showGraph(chart.graphs[1]);
				chart.hideGraph(chart.graphs[2]);
				chart.showGraph(chart.graphs[3]);
				
				document.getElementById("toggle-ans_rate").style.fontWeight = "normal";
				document.getElementById("toggle-srv_lvl").style.fontWeight = "bold";
				document.getElementById("toggle-ptf").style.fontWeight = "normal";
				document.getElementById("toggle-aht").style.fontWeight = "normal";
			  
			});
		}
	);
	
	Array.prototype.forEach.call(
	  document.querySelectorAll('#toggle-ptf'),
		function (button) {
			button.addEventListener('click', function() {
				
				chart.showGraph(chart.graphs[0]);
				chart.showGraph(chart.graphs[1]);
				chart.hideGraph(chart.graphs[2]);
				chart.hideGraph(chart.graphs[3]);
				
				document.getElementById("toggle-ans_rate").style.fontWeight = "normal";
				document.getElementById("toggle-srv_lvl").style.fontWeight = "normal";
				document.getElementById("toggle-ptf").style.fontWeight = "bold";
				document.getElementById("toggle-aht").style.fontWeight = "normal";
			  
			});
		}
	);
	  
	
</script>


<!-- line-graph -->
<script type="text/javascript">
	

   chart = AmCharts.makeChart("line-graph",
        {
            "type": "serial", "categoryField": "DateTime", "dataDateFormat": "YYYY-MM-DD HH:NN", "theme": "dark",
            "categoryAxis": {"minPeriod": "mm","parseDates": true, "title": "Interval"},
            "chartCursor": {"enabled": true,"categoryBalloonDateFormat": "JJ:NN"},
            "chartScrollbar": {"enabled": true},
            "trendLines": [],
            "graphs": [
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#FF0074","bulletSize": 9,"fillAlphas": 0.06,"fillColors": "#FF0074","fontSize": -1,
                "id": "AmGraph-1","lineColor": "#FF0074","lineThickness": 2,"title": "Forecasted Calls","valueField": "Calls_Forecasted","type": "smoothedLine"},
				
                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#1E90FF","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#1E90FF","fontSize": -1,
                "id": "AmGraph-2","lineColor": "#1E90FF","lineThickness": 2,"title": "Offered Calls","valueField": "Calls_Offered","type": "smoothedLine"},

                {"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#10c469","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#10c469","fontSize": -1,
                "id": "AmGraph-3","lineColor": "#10c469","lineThickness": 2,"title": "Handled Calls","valueField": "Calls_Handled","type": "smoothedLine"},
				
				{"bullet": "round", "bulletBorderAlpha": 1, "bulletBorderColor": "#FFFFFF", "bulletBorderThickness": 1,
                "bulletColor": "#064726","bulletSize": 9,"fillAlphas": 0.09,"fillColors": "#064726","fontSize": -1,
                "id": "AmGraph-4","lineColor": "#064726","lineThickness": 2,"title": "Acceptable Calls","valueField": "Acceptable_Calls","type": "smoothedLine"}
            ],
            "guides": [],
            "valueAxes": [
                {"id": "ValueAxis-1","title": "Volume"}
            ],
            "allLabels": [],
            "balloon": {},
            "legend": {"enabled": true, "align" : "center"},
            "titles": [
                {"id": "Title-1","size": 15,"text": "Call Volume Details <?php if($date_filter==''){ print "Overall Summary ".$date; }else{ print $date_filter; } ?>"}, 
                {"bold": false,"italic": true,"id": "Title-2","size": 11,"text": "(Click each legend to exclude in display)"}
            ],
            "dataProvider" : [ 
                <?php
                    // $res = $avaya_db->prepare("DECLARE @DateFilter DATE SET @DateFilter = '".$date_filter."' "); //SET @CommunityFilter= ".$community_filter.";
					// $res->execute();
					
					$res = $avaya_db->prepare($dash_graph_qry);

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

<!-- pie-graph Answer Rate -->
<script type="text/javascript">
    AmCharts.makeChart("pie1",
        {
            "type": "pie", "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
			"gradientType": "linear", "outlineAlpha": .8, "outlineThickness": 1, "innerRadius": "70%", "baseColor": "#c37d0e",
            "labelsEnabled": false, "titleField": "category", "valueField": "column-1", "fontSize": 9,"theme": "light",
            "allLabels": [ { "align": "center", "id": "Label-1", "size": 16, "color": "#000000", "y": "46%", 
			"text": " <?php if(($callsAnswered/checkVal($callsOffered))>1){ print 100; }else { print number_format(($callsAnswered/checkVal($callsOffered))*100,2); } ?>%" } ],
            "balloon": {},
            "titles": [],
            "dataProvider": [ 
                {"category": "Handled", "column-1": <?php print number_format(($callsAnswered/checkVal($callsOffered)),2); ?>},
                {"category": "Abandoned", "column-1": <?php print number_format(1-($callsAnswered/checkVal($callsOffered)),2); ?>}
            ]
        });
</script>

<!-- pie-graph Service Level -->
<script type="text/javascript">
    AmCharts.makeChart("pie2",
        {
            "type": "pie", "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
			"gradientType": "linear","outlineAlpha": .8,"outlineThickness": 1,"innerRadius": "70%","baseColor": "#5b69bc",
            "labelsEnabled": false,"titleField": "category","valueField": "column-1","fontSize": 9,"theme": "light",
            "allLabels": [{"align": "center","id": "Label-1","size": 16,"color": "#000000","y": "46%",
            "text": " <?php if(($callsAcceptable/checkVal($callsOffered))> 1){ print 99; }else { print number_format(($callsAcceptable/checkVal($callsOffered))*100,2); } ?>%" } ],
            "balloon": {},
            "titles": [],
            "dataProvider": [
                {"category": "Acceptable", "column-1": <?php print number_format($callsAcceptable/checkVal($callsOffered)*100,2); ?>},
                {"category": "Offered", "column-1": <?php print number_format(1-($callsAcceptable/checkVal($callsOffered)*100),2); ?>}
            ]
        });
</script>

<!-- pie-graph AHT -->
<script type="text/javascript">
    AmCharts.makeChart("pie3",
        {

            "type": "pie", "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> </span>",
			"gradientType": "linear","outlineAlpha": .8,"outlineThickness": 1,"innerRadius": "70%","baseColor": "#ff8acc",
            "labelsEnabled": false,"titleField": "category","valueField": "column-1","fontSize": 9,"theme": "light",
            "allLabels": [{"align": "center","id": "Label-1","size": 16,"color": "#000000","y": "46%",
			"text": " <?php if($aht<=$aht_goal) { print number_format(100-($aht/$aht_goal)); } else {print 100;} ?>%" } ], 	
            "balloon": {},
            "titles": [],
            "dataProvider": [
				{"category": "AHT", "column-1": <?php print number_format($aht,2); ?>},
                {"category": "Variance", "column-1": <?php if($aht<=$aht_goal) { print number_format($aht_goal-$aht); } else {print 0;} ?>},
            ]
        });
</script>

<!-- pie-graph PTF -->
<script type="text/javascript">
		AmCharts.makeChart("pie4",
        {
            "type": "pie", "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
			"gradientType": "linear","outlineAlpha": .8,"outlineThickness": 1,"innerRadius": "70%","baseColor": "#f6653c",
            "labelsEnabled": false,"titleField": "category","valueField": "column-1","fontSize": 9,"theme": "light",
            "allLabels": [{"align": "center","id": "Label-1","size": 16,"color": "#000000","y": "46%",
							"text":  "<?php if($callsForecasted == 0){ print 'No Data'; } else{ print number_format(($callsOffered/checkVal($callsForecasted))*100,2).'%'; }  ?>" 
						} ], 
            "balloon": {},
            "titles": [],
            "dataProvider": [
				{"category": "Offered", "column-1": <?php if($callsForecasted == 0){ print 0; } else{ if($callsOffered < $callsForecasted){ print number_format(1-($callsOffered/checkVal($callsForecasted)),2); } else { print number_format((checkVal($callsForecasted)/$callsOffered),2); } }  ?>},
                {"category": "Forecast", "column-1": <?php if($callsForecasted == 0){ print 0; } else{ if($callsOffered < $callsForecasted){ print number_format(($callsOffered/checkVal($callsForecasted)),2); } else { print number_format(1-(checkVal($callsForecasted)/$callsOffered),2);  } }  ?>},
            ]
        });
</script>

<!-- pie-graph CPH -->
<script type="text/javascript">
    AmCharts.makeChart("pie5",
        {
            "type": "pie", "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
			"gradientType": "linear","outlineAlpha": .8,"outlineThickness": 1,"innerRadius": "70%","baseColor": "#f6653c",
            "labelsEnabled": false,"titleField": "category","valueField": "column-1","fontSize": 9,"theme": "light",
            "allLabels": [{"align": "center","id": "Label-1","size": 16,"color": "#000000","y": "46%",
            "text": " <?php print number_format(($Transferred/checkVal($callsAnswered))*100,2);  ?>%" } ],
            "balloon": {},
            "titles": [],
            "dataProvider": [
				{"category": "Answered", "column-1":  <?php print number_format(1-($Transferred/checkVal($callsAnswered)),2); ?>},
                {"category": "Transferred", "column-1":  <?php print number_format(($Transferred/checkVal($callsAnswered)),2); ?>},
            ]
        });
</script>

<!-- pie-graph CRT -->
<script type="text/javascript">
    AmCharts.makeChart("pie6",
        {
            "type": "pie", "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
			"gradientType": "linear","outlineAlpha": .8,"outlineThickness": 1,"innerRadius": "70%","baseColor": "#5b69bc",
            "labelsEnabled": false,"titleField": "category","valueField": "column-1","fontSize": 9,"theme": "light",
            "allLabels": [{"align": "center","id": "Label-1","size": 16,"color": "#000000","y": "46%",
            "text": " <?php print number_format((($callsAnswered * $aht)/(($callsAnswered * $aht)+$availTime))*100,2); ?>%" } ],
            "balloon": {},
            "titles": [],
            "dataProvider": [
                {"category": "Occupancy", "column-1":  <?php print number_format((($callsAnswered * $aht)/checkVal(($callsAnswered * $aht)+$availTime)),2); ?>},
                {"category": "Avail", "column-1": <?php print number_format((1-($callsAnswered * $aht)/checkVal(($callsAnswered * $aht)+$availTime)),2); ?>},
            ]
        });
</script>

<!-- Table: Overall-->
<div class="row" style="margin-bottom: 0px; padding-top: 20px;">
    <div class="col-12">
		<table class="table table-bordered table-hover" cellspacing="0" width="100%">

        <?php
            date_default_timezone_set('America/Los_Angeles');
            $dDate = date('Y-m-01');
            print '<thead>
                <tr>
                    <th>Interval</th>
                    <th>Offered</th>
                    <th>Answered</th>
                    <th>Answer Rate</th>
                    <th>Abandoned</th>
                    <th>Abandoned Rate</th>
                    <th>ASA</th>
                    <th>Forecast</th>
                    <th>Forecast %</th>
                    <th>SL %</th>
					<th>Staff Hours</th>
					<th>CPH</th>
					<th>Avg Talk</th>
					<th>Avg Hold</th>
					<th>Avg ACW</th>
					<th>AHT</th>
					<th>Avail Time</th>
					<th>Aux Time</th>
					<th>Occupancy</th>
                </tr>
            </thead>';
    
            print'<tbody>';


            // $res = $avaya_db->prepare('SET @DateFilter="'.$date_filter.'"; SET @TimeFilter="'.$time_filter.'"; SET @CommunityFilter="'.$community_filter.'"; ');
            // $res->execute();
			
			$res = $avaya_db->prepare($dash_table_qry);

            $res->execute();
            $res = $res->fetchAll();

            foreach($res as $row){
                print '<tr>';
                    print '<td>'.checkZero($row['dtime']).'</td>';
                    print '<td>'.checkZero($row['Calls_Offered']).'</td>';
                    print '<td>'.checkZero($row['Calls_Answered']).'</td>'; 
					if($row['Percent_Answered_Calls'] == 0){print '<td>'.checkZero(number_format($row['Percent_Answered_Calls'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Percent_Answered_Calls'],2)).' %</td>';} 				  
                    print '<td>'.checkZero($row['Abandoned_Calls']).'</td>';                    
					if($row['Percent_Abandoned'] == 0){print '<td>'.checkZero(number_format($row['Percent_Abandoned'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Percent_Abandoned'],2)).' %</td>';} 
                    print '<td>'.checkZero(number_format($row['ASA'],2)).'</td>';
                    print '<td>'.checkZero(number_format($row['Forecast'],2)).'</td>';                     
					if($row['Percent_Forecast'] == 0){print '<td>'.checkZero(number_format($row['Percent_Forecast'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Percent_Forecast'],2)).' %</td>';}                    
					if($row['Percent_Service_Level'] == 0){print '<td>'.checkZero(number_format($row['Percent_Service_Level'],2)).' </td>';}else{print '<td>'.checkZero(number_format($row['Percent_Service_Level'],2)).' %</td>';}                    
					print '<td>'.checkZero($row['Total_Staff_Hours']).'</td>';
					print '<td>'.checkZero(number_format($row['CPH'],2)).'</td>';
					print '<td>'.checkZero(number_format($row['Average_Talk'],2)).'</td>';
					print '<td>'.checkZero(number_format($row['Average_Hold'],2)).'</td>';
					print '<td>'.checkZero(number_format($row['Average_ACW'],2)).'</td>';
					print '<td>'.checkZero(number_format($row['AHT'],2)).'</td>';
					print '<td>'.checkZero($row['Avail_Time']).'</td>';
					print '<td>'.checkZero($row['Aux_Time']).'</td>';
					print '<td>'.checkZero(number_format($row['Occupancy'],2)).'</td>';
					
                print '</tr>';
            }

    print '</tbody>';
        ?>

        </table>
    </div>
</div>

