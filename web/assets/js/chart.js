function renderDistancesChart(idTag){
    var distances = [];
    
    if(idTag > 0){
	$.getJSON("/lastDistances/" + idTag).done(function(data) {

	    $(data).each(function(key, value){
		distances.push({x: new Date(value.date), y: value.distance});
	    });

	    if(distances.length > 1){
		var chart = new CanvasJS.Chart("trainingChartContainer",{
			animationEnabled: true,
			zoomEnabled: true,
			height: 128,
			backgroundColor: "#ECECEC",
			axisY :{
				includeZero: false
			},
			toolTip: {
				shared: "true"
			},
			data: [
				{
				    type: "spline",
				    showInLegend: false,
				    name: "Distance",
				    markerSize: 0,
				    dataPoints: distances,
				    mousemove: function(e){
					moveMarker(e.dataPointIndex);
				    }
				}
			]
		});

		$('#trainingChartContainer').show();
		chart.render();
	    }else{
		$('#trainingChartContainer').hide();
	    }

	}).fail(function(){
	    console.log('Error while getting distances');
	    $('#trainingChartContainer').hide();
	});
    }else{
	$('#trainingChartContainer').hide();
    }
}

window.onload = function (){
    renderDistancesChart($('select#wykopbundle_training_Tag option:selected').val());

    $('select#wykopbundle_training_Tag').change(function(){
	renderDistancesChart($('select#wykopbundle_training_Tag option:selected').val());
    })
}