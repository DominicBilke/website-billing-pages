		<p>
			<form action="script/login.php" method="POST">
  				<label for="benutzer" class="form-label">Benutzer: </label> 
  				<input id="benutzer" class="form-control" name="benutzer" maxlength="100" type="text"><br/>
  				<label for="pswd" class="form-label">Passwort: </label> 
  				<input id="pswd" class="form-control" name="pswd" maxlength="100" type="password"><br/>
  				<label for="datenbank" class="form-label">Domain: </label> 
  				<input id="datenbank" class="form-control" name="datenbank" maxlength="100" type="text"><br/>
  				<button type="submit" class="btn btn-primary" name="login" value="1">Login</button>
  				<button type="submit" class="btn btn-primary" name="logout" value="1">Logout</button>
			</form>
		</p>

