$(function(){
	// Grab our url, works with protocol and sub domain this way.
	var base_url = window.location.protocol+'//'+window.location.host+'/'+document.location.pathname
	$.getJSON(base_url+'/stats/'+days, function(data){
		// helper for returning the weekends in a period
		function weekendAreas(axes) {
			var markings = [];
			var d = new Date(axes.xaxis.min);
			// go to the first Saturday
			d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
			d.setUTCSeconds(0);
			d.setUTCMinutes(0);
			d.setUTCHours(0);
			var i = d.getTime();
			do {
				// when we don't set yaxis the rectangle automatically
				// extends to infinity upwards and downwards
				markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
				i += 7 * 24 * 60 * 60 * 1000;
			} while (i < axes.xaxis.max);

			return markings;
		}

		var d = [];
		var s = [];
		$.each(data.visits, function(i,item){
			d.push([item[0],item[1]]);
		});
		$.each(data.signups, function(i,item){
			s.push([item[0],item[1]]);
		});

		for (var i = 0; i < d.length; ++i)
			d[i][0] += 60 * 60 * 1000;

		for (var i = 0; i < s.length; ++i)
			s[i][0] += 60 * 60 * 1000;				

		var plot = $.plot($("#placeholder"),
			[ { data: d, label: "Visitations"} , { data: s, label: "Signups" } ],
			{ lines: { show: true, fill: true },
			points: { show: true },
			grid: { hoverable: true, clickable: false, markings: weekendAreas },
			yaxis: { min: 0, tickDecimals: 0 },
			xaxis: { mode: 'time' }
		});

		function showTooltip(x, y, contents) {
			$('<div id="tooltip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#fee',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}
		var previousPoint = null;
		$("#placeholder").bind("plothover", function (event, pos, item) {
		$("#x").text(pos.x.toFixed(2));
		$("#y").text(pos.y.toFixed(2));

		if (item) {
				if (previousPoint != item.datapoint) {
					previousPoint = item.datapoint;
					$("#tooltip").remove();
					var x = item.datapoint[0],
					y = item.datapoint[1];
					showTooltip(item.pageX, item.pageY,
					y + " " + item.series.label);
				}
			} else {
				$("#tooltip").remove();
				previousPoint = null;
			}
		});		
	});
});