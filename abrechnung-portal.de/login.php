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
<p align="center">
Hier können Sie die Accounts auf Abrechnung-Portal.de abbonnieren:<br><br>

Es gelten die Nutzungsbedingungen von <a href="https://www.arbeitsabrechnung.de/Nutzungsbedingungen.pdf">Arbeitsabrechnung.de</a>, <a href="https://www.Abrechnungstool.de/Nutzungsbedingungen.pdf">Abrechnungstool.de</a> und <a href="https://www.Geldabrechnung.de/Nutzungsbedingungen.pdf">Geldabrechnung.de</a><br><br>

<script async
  src="https://js.stripe.com/v3/buy-button.js">
</script>

<stripe-buy-button
  buy-button-id="buy_btn_1MmKWWFC26MkWqtNt7nh6vRY"
  publishable-key="pk_live_51MmE9qFC26MkWqtNKoaOKQLqpvsVAB516oe0KjuMrgVDE0lH4uZrznG92RkBpjkvl321KODc1fVSFMBH9WtFKiLV001GP1hDm1"
>
</stripe-buy-button>

</p>