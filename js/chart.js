var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var color = Chart.helpers.color;
		var barChartData = {
			labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			datasets: [{
				label: 'Contract 1',
				backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
				borderColor: window.chartColors.red,
				borderWidth: 1,
				data: [
					8,10,15,20,30,40,15
				]
			}, {
				label: 'Contract 2',
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				borderWidth: 1,
				data: [
				  20,30,40,25,35,45,25
				]
			}]

		};

		window.onload = function() {
			var ctx = document.getElementById('canvas').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Contract Summary'
					}
				}
			});

		};

		document.getElementById('randomizeData').addEventListener('click', function() {
			var zero = Math.random() < 0.2 ? true : false;
			barChartData.datasets.forEach(function(dataset) {
				dataset.data = dataset.data.map(function() {
					return zero ? 0.0 : randomScalingFactor();
				});

			});
			window.myBar.update();
		});