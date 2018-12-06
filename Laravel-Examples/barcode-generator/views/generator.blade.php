@extends('main')

@section('navbar')

	<button type="button" class="btn btn-default navbar-btn">Browse Lists</button>
				    	
	<div class="btn-group navbar-btn">
		
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Admin <span class="caret"></span>
		</button>
		
		<ul class="dropdown-menu">
			<li><a href="#">Markets</a></li>
			<li><a href="#">Actions</a></li>
			<li><a href="#">Vendors</a></li>
			<li role="separator" class="divider"></li>
			<li><a href="#">Update Password</a></li>
		</ul>
		
	</div>

@stop
