
function eDashboardActionGetPublicationKeyProperties() {
	return 	{ title:{ required:true }, descr:{ type:'textarea', required:true },
		text:{ type:'textarea', required:true, title:_myStrPublication }, created_at:{ type:'datetime', required:true }, is_hidden:{} };
}


function eDashboardActionCreatePublication( publ, el ) {
	let windowTitle = `${_myStrPublication} | ${_myStrCreating}`;

	let datetime = new Date();
	let datetime_string = calendarDateTimeToString(datetime, false);

	let values = { 
		title: 'Please, provide a title', descr: 'Please, provide a description...', icon: null, 
		text:'Please, provide the text!', created_at: datetime_string, is_hidden:false };
	
	let keyProperties = eDashboardActionGetPublicationKeyProperties();
	let rightPaneHTML = "";

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/e_publications_new' } );
}


function eDashboardActionUpdatePublication( publ, el ) {
	let windowTitle = `${_myStrPublication} | ${_myStrUpdating}`;

	let values = { id: publ.id, title: publ.title, descr: publ.descr, icon: publ.icon, text: publ.text, 
		created_at: publ.created_at, is_hidden: publ.is_hidden };
	
	let keyProperties = eDashboardActionGetPublicationKeyProperties();
	keyProperties.id = { hidden:true };

	let rightPaneHTML = `${_myStrPublication}<br/><mark>${publ.title}</mark><br/>${publ.created_at}`;

	aDisplayDashboardDataArrayEditWindow( windowTitle, null, values, 
		{ rightPaneHTML:rightPaneHTML, keyProperties:keyProperties, saveURL:'/e_publications_update' } );
}


function eDashboardActionDeletePublication( publ, el ) {
	if( confirm(`${_myStrPublication} ${publ.title}, ${publ.created_at}. ${_myStrDelete}?`) ) {
		let params = { 'keys': ['id'], 'inputs': [ { value:data.id } ], saveURL:'/e_publications_delete' };
		aDefaultDashboardDataEditWindowSaveFunction( params );
	}
}
