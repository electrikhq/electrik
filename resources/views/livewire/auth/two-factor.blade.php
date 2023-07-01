<div>
	<form class="form-horizontal" method="post" action="{{ route('auth.2fa') }}">
		{{ csrf_field() }}

		<div class="form-group">
			<p>Please enter the <strong>OTP</strong> generated on your Authenticator App. <br> Ensure you submit the current one because it refreshes every 30 seconds.</p>
			<label for="one_time_password" class="col-md-4 control-label">One Time Password</label>


			<div class="col-md-6">
				<input id="one_time_password" type="number" class="form-control" name="one_time_password" required autofocus>
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-6 col-md-offset-4">
				<button type="submit" class="btn btn-primary">
					Login
				</button>
			</div>
		</div>
	</form>
</div>