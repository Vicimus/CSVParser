<form action="{{\URL::route('csv.start')}}" method="post" enctype="multipart/form-data">
	<div class="form-group">
		<label>CSV File</label>
		<input type="file" name="csv-file" />
		<button class='btn btn-default'>Submit</button>
		<input type="checkbox" name="csv-headers" /> First Line Headers
		<input type="hidden" name="csv-schema" value="{{$schema}}" />
	</div>
</form>