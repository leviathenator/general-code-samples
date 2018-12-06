@extends('login')

@section('main-body')
	<form id="frm-login" action="/" method="post">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="container form-wrap">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Login</h4>
						</div>
						
						<div class="modal-body">
							<div id="msg" class="alert" role="alert"></div>
							<div class="row">
								<div class="form-group">Enter Your Password</label>
									<input type="password" class="form-control input-sm" id="p" name="p" placeholder="Password" required>
								</div>
							</div>
							
						</div>
						
						<div class="modal-footer">
							<div class="form-group">
								<button type="submit" class="btn btn-primary">Log In</button>
							</div>
						</div>
					
					
					</div>
				</div>
			</div>
	</form>
@stop