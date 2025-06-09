<p>
			<form action="script/benutzerdaten.php" method="POST">
  				<label for="vorname" class="form-label">Vorname: </label> 
  				<input id="vorname" class="form-control" name="vorname" maxlength="100" type="text"><br/>
  				<label for="nachname" class="form-label">Nachname: </label> 
  				<input id="nachname" class="form-control" name="nachname" maxlength="100" type="text"><br/>
  				<label for="strasse_nr" class="form-label">Strasse Nr: </label> 
  				<input id="strasse_nr" class="form-control" name="strasse_nr" maxlength="100" type="text"><br/>
  				<label for="plz_ort" class="form-label">PLZ Ort: </label> 
  				<input id="plz_ort" class="form-control" name="plz_ort" maxlength="100" type="text"><br/>
				<select name="typ"  class="form-select">
					<option value="alle">Alle Accounts</option>
					<option value="Abrechnungstool">Abrechnungstool</option>
					<option value="Firmenabrechnung">Firmenabrechnung</option>
					<option value="Geldabrechnung">Geldabrechnung</option>
					<option value="Aufgabenabrechnung">Aufgabenabrechnung</option>
					<option value="Arbeitsabrechnung">Arbeitsabrechnung</option>
				</select><br/>

  				<button type="submit" class="btn btn-primary" name="benutzerdaten" value="1">Daten ändern</button>
			</form>
		</p>
