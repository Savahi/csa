
<div style='width:100%; padding:0px 20px 0px 20px; text-align:center;'>
<div id='gantt' style='box-sizing:border-box; width:100%; text-align:center; position:relative; left:0%; top:0; border:1px solid gray; overflow:hidden;'>
	<div id='ganttChart' style='box-sizing:border-box; margin:0; padding:0; border:0; position:absolute; overflow:auto;'></div>
	<div id='ganttX' style='box-sizing:border-box; margin:0; padding:0; position:absolute; overflow:hidden;'></div>
	<div id='ganttY' style='box-sizing:border-box; margin:0; padding:0; position:absolute; overflow:hidden; border-right:1px solid gray;'></div>
</div>
</div>

<script src='/js/svg.js'></script>
<script src='/js/a/gantt.js'></script>
<script src='/js/gantttimescale.js'></script>

<script>
var _ganttDataLoaded = {!!json_encode($ganttData)!!};

window.addEventListener( 'load', 
	function() { 
		if(_ganttDataLoaded) { 			
			document.getElementById('pageTitle').innerText = "{{$htexts['page_workflow']->title}}" + 
				((_ganttDataLoaded.title.length > 0) ? (" | " + _ganttDataLoaded.title) : '');
			//document.getElementById('pageTitle').innerText = _ganttDataLoaded.title;
			ganttInit(document.getElementById('gantt'), _ganttDataLoaded); 
		} else {
			document.getElementById('pageTitle').innerText = 'An error occured loading workflow chart...';
		}
	});

</script>
