$('#flow-chart-save').on('click', function(){
	storeFlowChart();
});

$('#flow-chart-load').on('click', function(){
	loadFlowChart();
	storeFlowChart(); //so the store is kept up to date
});


$('.flowchart-container .state').on('drag', function() {
	storeFlowChart();
});

//Helper function to change display on states that 
//have been moved from original location
$('.state.new-state').on('click drag', function(){
	$(this).removeClass('new-state');
});


