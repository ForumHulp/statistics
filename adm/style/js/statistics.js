; (function ($, window, document) {
	// do stuff here and use $, window and document safely
	// https://www.phpbb.com/community/viewtopic.php?p=13589106#p13589106
	var time = 59;
	function progress() {
		var element = $('#circle');
		element.prop('title', time + ' seconds until refresh');
		//element.html(time);
		if (time === 0) {
			clearInterval(interval);
			element.removeClass("fa-circle-o-notch");
			element.addClass("fa-refresh");
			$.ajax({
				url: window.location.href + "&table=true",
				context: document.getElementById("statistics_table"),
				error: function (e, text, ee) {
					element.css("display", "none");
					if (text == "timeout") {
						$("#LoadErrorTimeout").css("display", "inline-block");
						$("#LoadError").css("display", "block");
					} else {
						$("#LoadPageError").css("display", "inline-block");
						$("#LoadError").css("display", "block");
					}
				},
				success: function (s, x) {
					time = 59;
					element.removeClass("fa-refresh");
					element.addClass("fa-circle-o-notch");
					interval = setInterval(progress, 1000);
					$(this).html(s);
				}
			});
		}
		time--;
	}
	if ($('#circle').length) var interval = setInterval(progress, 1000);
	
	$("a.simpledialog").simpleDialog({
	    opacity: 0.1,
	    width: '650px',
		height: '600px'
	});

	$("#custom_pages").change(function()
	{
		/* setting currently changed option value to option variable */
		var val = $("#custom_pages").val();
		$('#custom_page').val(val);
		if (val != '') {$('#custom_value').val($('#custom_pages option:selected').text())} else  {$('#custom_value').val('')};
		
	});
	
	if (typeof graph !== 'undefined')
	{
            $(function () {
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'chart',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: 'Graph',
						margin: 0,
						style: {fontSize: '9px'}
					},
					credits: {
						enabled: false,
						text: 'ForumHulp.com',
						href: 'http://forumhulp.com'
					},
					legend: {
						enabled: true,
						layout: 'vertical',
						align: 'left',
						verticalAlign: 'top',
						x: 10,
						y: 10,
						borderWidth: 0
					}, 
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(0)  +' %';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: false,
								color: '#000000',
								connectorColor: '#000000',
								formatter: function() {
									return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(0)  +' %';
								}
							},
								showInLegend: true
						}
					},
					series: [{
						type: 'pie',
						name: 'Planning',
						data: graph
					}]
				});
			});
		});
	}
	
	if (typeof stats !== 'undefined')
	{
		$(function () {
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'chart',
						type: 'column'
					},
					title: {
						text: '',
					},
					subtitle: {
						text: title
					},
					credits: {
						enabled: false,
						text: 'ForumHulp.com',
						href: 'http://forumhulp.com'
					},
					xAxis: {
						categories: dates
					},
					yAxis: {
						min: 0,
						title: {
							text: 'hits'
						}
					},
					legend: {
						enabled: false,
						layout: 'vertical',
						backgroundColor: '#FFFFFF',
						align: 'left',
						verticalAlign: 'top',
						x: 100,
						y: 70,
						floating: true,
						shadow: true
					},
					tooltip: {
						formatter: function() {
							return ''+
								this.x +': '+ this.y +' hits';
						}
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
						series: [{
						name: 'Hits',
						data: stats
			
					}]
				});
			});
			
		});
	}

})(jQuery, window, document);
