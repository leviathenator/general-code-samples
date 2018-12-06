@extends('main')

@section('navbar')

	<a href="/lists" class="btn btn-default navbar-btn">Browse Lists</a>
				    	
	<div class="btn-group navbar-btn">
		
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Admin <span class="caret"></span>
		</button>
		
		<ul class="dropdown-menu">
			<li><a href="#" rel="open-modal" data-target="#template-manage-markets">Markets</a></li>
			<li><a href="#" rel="open-modal" data-target="#template-manage-actions">Actions</a></li>
			<li><a href="#" rel="open-modal" data-target="#template-manage-vendors">Vendors</a></li>
			<li role="separator" class="divider"></li>
			<li><a href="#" rel="open-modal" data-target="#template-update-password">Update Password</a></li>
		</ul>
		
	</div>

@stop

@section('main-body')

	<table class="table">
		<tbody>
			<tr>
				<td>
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							View Records <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a href="/lists/10">10</a></li>
							<li><a href="/lists/15">15</a></li>
							<li><a href="/lists/30">30</a></li>
							<li><a href="/lists/60">60</a></li>
							<li><a href="/lists/100">100</a></li>
						</ul>
					</div>
				</td>
				<td>
					<div class="pull-right">
						
						<form action="/searchlist" method="post" autocomplete="off">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
	 						<div class="input-group">
								<input type="text" class="form-control" name="search" id="search" value="" placeholder="Search List Items...">
								<div class="input-group-btn">
									<button class="btn btn-default" type="submit">Search</button>
								</div>
							</div>
						</form>
						
					</div>
					
				</td>
			</tr>
			
		</tbody>
	</table>

	<table class="table table-striped">
		<thead>
			<tr>
				<td><a href="/lists/client/{{ (!empty($dir) ? $dir : '') }}">Client Name <span class="fa fa-arrow-{{ (!empty($dir) && $dir === 'asc' ? 'circle-down' : 'circle-up') }}"></span></a></td>
				<td><a href="/lists/date/{{ (!empty($dir) ? $dir : '') }}">Date Posted <span class="fa fa-arrow-{{ (!empty($dir) && $dir === 'asc' ? 'circle-down' : 'circle-up') }}"></span></a></td>
				<td><a href="/lists/group/{{ (!empty($dir) ? $dir : '') }}">Group ID <span class="fa fa-arrow-{{ (!empty($dir) && $dir === 'asc' ? 'circle-down' : 'circle-up') }}"></span></a></td>
				<td><a href="/lists/barcode/{{ (!empty($dir) ? $dir : '') }}">Barcode ID <span class="fa fa-arrow-{{ (!empty($dir) && $dir === 'asc' ? 'circle-down' : 'circle-up') }}"></span></a></td>
				<td>Open</td>
			</tr>
		</thead>
		<tbody>
			@foreach ($lists as $item)
			<tr>
				<td>{{ $item->client_name }}</td>
				<td>{{ date('m-d-Y', strtotime($item->datetime)) }}</td>
				<td>{{ $item->group_id }}</td>
				<td>{{ $item->barcode_id }}</td>
				<td><a href="/listitem/{{ $item->id }}"> Open</a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
	
	{{ $lists->links() }}
@stop

@section('templates')

	<script id="template-manage-markets" type="text/template">
		<div id="modal-manage-markets" class="modal fade" tabindex="-1" role="dialog" data-type="markets">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Manage Market Lists</h4>
					</div>
					<form action="/managelist/markets" method="post">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="modal-body">
						<div id="msg" class="alert" role="alert"></div>
						<div class="input-group">
							
							<input type="text" class="form-control input-sm" id="add-row-val" rel="add-row-val" value="" placeholder="Add a Market Name">
							
							<span class="input-group-btn">
								<button type="button" class="btn btn-success btn-sm" title="Click to Add" rel="add-row" data-target="#template-list-row">
									<span class="fa fa-plus"></span>
								</button>
							</span>
						</div>
	
						
							<table id="markets-list-holder" class="table table-striped">
								<tbody>
									@foreach ($market_list as $market)
										<tr>
											<td class="form-row-input">
												<div class="input-group">
													<input type="text" class="form-control input-sm" id="row-{{ $loop->iteration }}" name="markets:{{ $loop->iteration }}" value="{{ $market->name }}" placeholder="Enter Name">
													<span class="input-group-btn">
														<button type="button" class="btn btn-danger btn-sm" title="Click to Remove" rel="delete-row">
															<span class="fa fa-times-circle"></span>
														</button>
													</span>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						
					</div>
		
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save List</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</script>
	
	<script id="template-manage-actions" type="text/template">
		<div id="modal-manage-markets" class="modal fade" tabindex="-1" role="dialog" data-type="actions">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Manage Actions Lists</h4>
					</div>
					<form action="/managelist/actions" method="post">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="modal-body">
							<div id="msg" class="alert" role="alert"></div>
							<div class="input-group">
								
								<input type="text" class="form-control input-sm" id="add-row-val" rel="add-row-val" value="" placeholder="Add a Action Name">
								
								<span class="input-group-btn">
									<button type="button" class="btn btn-success btn-sm" title="Click to Add" rel="add-row" data-target="#template-list-row">
										<span class="fa fa-plus"></span>
									</button>
								</span>
							</div>
		
							
								<table id="actions-list-holder" class="table table-striped">
									<tbody>
										@foreach ($action_list as $action)
											<tr>
												<td class="form-row-input">
													<div class="input-group">
														<input type="text" class="form-control input-sm" id="row-{{ $loop->iteration }}" name="markets:{{ $loop->iteration }}" value="{{ $action->name }}" placeholder="Enter Name">
														<span class="input-group-btn">
															<button type="button" class="btn btn-danger btn-sm" title="Click to Remove" rel="delete-row">
																<span class="fa fa-times-circle"></span>
															</button>
														</span>
													</div>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							
						</div>
			
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Save List</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</script>
	
	<script id="template-manage-vendors" type="text/template">
		<div id="modal-manage-markets" class="modal fade" tabindex="-1" role="dialog" data-type="vendors">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Manage Vendor Lists</h4>
					</div>
					<form action="/managelist/vendors" method="post">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="modal-body">
						<div id="msg" class="alert" role="alert"></div>
						<div class="input-group">
							
							<input type="text" class="form-control input-sm" id="add-row-val" rel="add-row-val" value="" placeholder="Add a Vendor Name">
							
							<span class="input-group-btn">
								<button type="button" class="btn btn-success btn-sm" title="Click to Add" rel="add-row" data-target="#template-list-row">
									<span class="fa fa-plus"></span>
								</button>
							</span>
						</div>
	
						
							<table id="vendors-list-holder" class="table table-striped">
								<tbody>
									@foreach ($vendor_list as $vendor)
										<tr>
											<td class="form-row-input">
												<div class="input-group">
													<input type="text" class="form-control input-sm" id="row-{{ $loop->iteration }}" name="markets:{{ $loop->iteration }}" value="{{ $vendor->name }}" placeholder="Enter Name">
													<span class="input-group-btn">
														<button type="button" class="btn btn-danger btn-sm" title="Click to Remove" rel="delete-row">
															<span class="fa fa-times-circle"></span>
														</button>
													</span>
												</div>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						
					</div>
		
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save List</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</script>
	
	<script id="template-update-password" type="text/template">
		<div id="modal-manage-markets" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Update Password</h4>
					</div>
					<form action="/managepass" method="post">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="modal-body">
	
						<div id="msg" class="alert" role="alert"></div>
							<div class="row">
								
								<div class="form-group">
									<label class="control-label" for="i_total">Old Password</label>
									<input type="text" class="form-control input-sm" id="oldP" name="oldP" value="" placeholder="Enter Your Old Password" required>
								</div>
							
								<div class="form-group">
									<label class="control-label" for="i_total">New Password</label>
									<input type="text" class="form-control input-sm" id="newP" name="newP" value="" placeholder="Enter Your New Password" required>
								</div>
							
								<div class="form-group">
									<label class="control-label" for="i_total">Confirm New Password</label>
									<input type="text" class="form-control input-sm" id="confP" name="confP" value="" placeholder="Enter New Password Again" required>
								</div>
									
							</div>
						
					</div>
		
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Update Password</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</script>
	
	<script id="template-list-row" type="text/template">
		<tr>
			<td class="form-row-input">
				<div class="input-group">
					<input type="text" class="form-control input-sm" id="row-" name="" value="" placeholder="Enter Name">
					<span class="input-group-btn">
						<button type="button" class="btn btn-danger btn-sm" title="Click to Remove" rel="delete-row">
							<span class="fa fa-times-circle"></span>
						</button>
					</span>
				</div>
			</td>
		</tr>
	</script>

@stop

