<link rel="stylesheet" type="text/css" href="{{asset('packages/vicimus/c-sv-parser/interface.css')}}">
<form id="start" action="{{\URL::route('csvparser.start')}}" method="post" enctype="multipart/form-data" class="form-inline">
	<div class="col-md-6 col-md-offset-3" data-csv-step="1">
		<div class="form-group">
		   <label class="sr-only" for="exampleInputAmount">CSV File</label>
		    <div class="input-group">
		      	<div class="input-group-addon"><span class="fa fa-database"></span></div>
		      	<input onkeydown="return false" type="text" class="form-control" id="select-file" placeholder="CSV File" />
		    	<div class="input-group-addon btn btn-default" id="select">Select</div>
		    </div>
		</div>
		<div class="text-right">
			<label><input type="checkbox" data-start name="headers" /> Headers Included</label>
		</div>
		<input type="file" style="display: none" id="csv-file" name="csv" />
		<input type="hidden" name="schema" value="{{$schema}}" />
		
	</div>
	<div class="col-md-2">
		<button id="start" class="btn btn-default">Start</button>
	</div>
</form>

<div data-step="2">
	<h1>Column Associations</h1>
	<p class="border-bottom">Below is a list of columns found in the CSV provided. You can assign each column to match a column from your database. The specified column will be used when importing the data. All data marked as Ignore Column will be skipped.</p>
	<button class="btn btn-default center" data-import>Import Data</button>
	<div class="row" data-interface>
	</div>
	<button class="btn btn-default center" data-import>Import Data</button>
</div>

<form id="process" action="{{\URL::route('csvparser.process')}}" method="post">
	<input type="hidden" name="map" id="map" />
	<input type="hidden" name="csv" id="csv" />
	<input type="hidden" name="schema" id="schema" />
	<input type="hidden" name="headers" id="headers" />
</form>

<div data-step="3">
	<h1>Review Import Data</h1>
	<p class="border-bottom">Below are the results of the CSV import using the columns specified. This is the final step before importing the data into the database.</p>
	<button class="btn btn-default center" data-finish>Finish</button>
	<div class="text-right"><label><input type="checkbox" id="duplicateCheck" /> Ignore Duplicates</label></div>
	<table data-inserts>
		<thead>
			<tr>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<button class="btn btn-default center" data-finish>Finish</button>
</div>

<form id="finish" action="{{\URL::route('csvparser.finish')}}" method="post">
	<input type="hidden" name="inserts" id="inserts" />
	<input type="hidden" name="schema" id="finish-schema" />
	<input type="hidden" name="duplicates" id="duplicates" /> 
</form>

<script type="text/javascript">

var parser;
var inserts;

function ucwords(str)
{
	return (str + '')
    .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
      return $1.toUpperCase();
    });
}

function processSelections()
{
	var map = [];

	$("[data-csv-col]").each(function(index, value){
		var csvcol = $(this).attr('data-csv-col');
		var dbcol = $(this).find('option:selected').attr('data-db-col');

		var relationship = {};
		relationship.csv = csvcol;
		relationship.db = dbcol;

		map.push(relationship);
	});

	return map;
}

function getColumn(parser, column)
{

	var html = '<div class="csv-column">'+
				'<label>Select Column</label>'+
				'<div class="form-group"><select data-csv-col="'+column+'" class="form-control"><option>Ignore Column</option>';
	$.each(parser.columns, function(index, value){
		html += '<option data-db-col="'+index+'">'+ucwords(value)+'</option>';
	});

	html += '</select></div>'+
			'<ul>'+
			'<li class="heading">';
	if(parser.hasHeaders)
		html += parser.headers[column];
	else
		html += 'Content';

	html += '</li>'+
			'<li>'+parser.lines[0][column]+'</li>'+
			'<li>'+parser.lines[1][column]+'</li>'+
			'</ul></div>';
	return html;
}

function insertTH(value)
{
	$("[data-inserts] thead tr").append('<th>'+ucwords(value)+'</th>');
}

function insertRow(row, index)
{
	var html = '<tr data-insert-index="'+index+'"><td style="text-align: center; width: 50px;"><input type="checkbox" checked /></td>';
	$.each(row, function(index, value){
		html += '<td>'+value+'</td>';
	});

	html += '</tr>';

	$("[data-inserts] tbody").append(html);
}

function createInsertTable(data)
{
	inserts = $.parseJSON(data);
	
	var first = inserts[0];

	insertTH('Insert');

	$.each(first, function(index, value)
	{
		insertTH(index);
	});

	$.each(inserts, function(index, value){
		insertRow(value, index);
	});

	$('[data-step=2]').hide();
	$("[data-step=3]").show();
}

function createInterface(data)
{
	parser = $.parseJSON(data);
	var line = parser.lines[0];

	$("#csv").val(parser.file);
	$("#headers").val(parser.hasHeaders);
	$("#schema").val(parser.schema);

	$.each(line, function(index, value){
		$("[data-interface]").append(getColumn(parser, index));
	});

	$("#start").hide();
	$("[data-step=2]").show();
}

$(document).ready(function(){

	$("#select").click(function(){
		$("#csv-file").click();
	});

	$("#csv-file").change(function(){
		var file = $(this).val().replace(/^.*\\/, "");
		$("#select-file").val(file);
	});

	$("[data-import]").click(function(){
		var map = processSelections();
		var encoded = JSON.stringify(map);
		$("#map").val(encoded);
		$("#process").submit();
	});

	$("#start").submit(function(event){
		event.preventDefault();

		var formData = new FormData($(this)[0]);

	    $.ajax({
	    	xhr: function()
			  {
			    var xhr = new window.XMLHttpRequest();
			    //Upload progress
			    xhr.upload.addEventListener("progress", function(evt){
			      if (evt.lengthComputable) {
			        var percentComplete = evt.loaded / evt.total;
			        //Do something with upload progress
			        
			      }
			    }, false);
			    return xhr;
			  },
	        url: $(this).attr('action'),
	        type: 'POST',
	        data: formData,
	        async: true,
	        success: function (data) {
	            createInterface(data);
	        },
	        cache: false,
	        contentType: false,
	        processData: false
	    });


	    return false;
	});

	$("#process").submit(function(event){
		event.preventDefault();

		var formData = new FormData($(this)[0]);

	    $.ajax({
	    	xhr: function()
			  {
			    var xhr = new window.XMLHttpRequest();
			    //Upload progress
			    xhr.upload.addEventListener("progress", function(evt){
			      if (evt.lengthComputable) {
			        var percentComplete = evt.loaded / evt.total;
			        //Do something with upload progress
			        
			      }
			    }, false);
			    return xhr;
			  },
	        url: $(this).attr('action'),
	        type: 'POST',
	        data: formData,
	        async: true,
	        success: function (data) {
	            createInsertTable(data);
	        },
	        cache: false,
	        contentType: false,
	        processData: false
	    });


	    return false;
	});

	$("[data-finish]").click(function(){
		var sendInserts = [];
		$.each(inserts, function(index, value){
			if($("tr[data-insert-index="+index+"]").find('input').prop('checked'))
				sendInserts.push(value);
		});
		$("#finish-schema").val(parser.schema);
		if($("#duplicateCheck").prop('checked'))
			$("#duplicates").val('true');
		
		var data = JSON.stringify(sendInserts);
		$("#inserts").val(data);
		$("#finish").submit();
	});
});

</script>