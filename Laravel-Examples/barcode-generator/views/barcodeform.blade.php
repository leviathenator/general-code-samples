@extends('main')

@section('navbar')

	<a href="/lists/" class="btn btn-default navbar-btn">Browse Lists</a>

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

	<form id="frm-generator" action="/generate" method="post">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="container form-wrap">
			<div class="loader"><img src="/assets/img/default.gif"></div>
			<div class="modal-dialog" id="form-wrap">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Barcode Generator</h4>
					</div>

					<div class="modal-body">

						<div id="gen-msg" class="alert alert-warning" role="alert"></div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_client_name">Client Name</label>
								<div class="col-md-8">
									<input type="text" class="form-control input-sm" id="i_client_name" name="client_name" placeholder="Enter the Client Name" value="{{ (!empty($list) ? $list[0]->client_name : '') }}" tabindex="1" required>
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->client_name }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_vendor">Vendor</label>
								<div class="col-md-8">
									<select class="form-control input-sm" id="i_vendor" name="vendor" tabindex="2" required>
									    <option value="">Select Vendor</option>
										     @foreach ($vendor_list as $vendor)
										    	<option value="{{ $vendor->name }}"{{ !empty($list) && $vendor->name == $list[0]->vendor ? " selected" : "" }}>{{ $vendor->name }}</option>
										     @endforeach
								    </select>
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->vendor }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_vendor">Market</label>
								<div class="col-md-8">
									<select class="form-control input-sm" id="i_market" name="market" tabindex="3" required>
									    <option value="">Select Area</option>
									    @foreach ($market_list as $market)
									    	<option value="{{ $market->name }}" {{ !empty($list) && $market->name == $list[0]->market ? " selected" : "" }}>{{ $market->name }}</option>
									    @endforeach

								    </select>
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->market }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_action">Action</label>

								<div class="col-md-8">
									<select class="form-control input-sm" id="i_action" name="action" tabindex="4" required>
									    <option value="">Select One</option>
									    @foreach ($action_list as $action)
									    	<option value="{{ $action->name }}" {{ !empty($list) && $action->name == $list[0]->action ? " selected" : "" }}>{{ $action->name }}</option>
									     @endforeach
								    </select>
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->action }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_action">Group ID</label>
								<div class="col-md-8">
									<input type="text" class="form-control input-sm" id="i_group_id" name="group_id" placeholder="only alpha/numeric (no symbols) and a max of 20 characters" pattern="[a-zA-Z0-9_-]{1,20}" value="{{ !empty($list) ? $list[0]->group_id : '' }}" tabindex="5" required>
									<span class="group-id-cnt"></span>
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->group_id }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_barcode_id">Barcode ID</label>
								<div class="col-md-8">
									<input type="text" class="form-control input-sm" id="i_barcode_id" name="barcode_id" placeholder="only alpha/numeric (no symbols)" pattern="[A-Za-z0-9\S]{1,20}" value="{{ !empty($list) ? $list[0]->barcode_id : '' }}" tabindex="6" required>
									<span class="barcode-id-cnt"></span>
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->barcode_id }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_split_num">Split Codes Amount</label>

								<div class="col-md-8">
									<input type="text" class="form-control input-sm" id="i_split_num" name="split_num" placeholder="(optional) Enter Number of codes to split between files" value="{{ !empty($list) ? $list[0]->split_num : '' }}" tabindex="7">
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->split_num }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="form-group">
								<label class="col-md-3 control-label" for="i_total">Total Codes</label>

								<div class="col-md-8">
									<input type="text" class="form-control input-sm" id="i_total" name="total" placeholder="Enter Total Number of Codes to Generate" value="{{ !empty($list) ? $list[0]->total : '' }}" tabindex="8" required>
								</div>
								<div class="col-md-1">
									<button type="button" class="btn btn-warning btn-sm" data-container="body" data-toggle="popover" title="Info" data-content="{{ $popovers->total }}">
										<span class="fa fa-question-circle"></span>
									</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="form-group">
							@if (!empty($file_list))

								<a href="/lists" class="btn btn-primary">Back To List</a>

							@else
								<button type="submit" class="btn btn-primary">Generate Files</button>
							@endif
						</div>

					</div>
				</div>
			</div>

			<div class="modal-dialog" id="file-wrap" style="{{ (!empty($file_list) ? 'display:block;' : 'display:none;') }}">
				<div class="file-drawer">
					<h3>Download Files</h3>
					<div id="file-holder">

						@if (!empty($file_list))

							@foreach($file_list as $file)

								<a class="file-icon" href="/storage/{{ $file->filename }}" target="_blank">
									<span class="fa fa-file"></span>
									<span class="file-name">{{ $file->filename }}</span>
								</a>

							@endforeach

						@endif

					</div>
				</div>
			</div>
		</div>

	</form>

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
						<div class="well">
							Market names should be a one to two word phrase.
							If followed by a colon, the abbreviation that follows will be used in the file naming convention.
							Otherwise, the abbreviation will be taken from the first two letters of the first word.
						</div>
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
							<div class="">

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
