function chargerDonnee(coureur) {
	
	var xhr = new XMLHttpRequest();
	xhr.open('GET', '../php/modificationCoureur.php?coureur=' + coureur, true);
	
	var Lire = function() {
		if (xhr.readyState === 4 && xhr.status === 200) {
			document.getElementById('resultat').innerHTML = '<span>' + xhr.responseText + '</span>';
			var tableau = xhr.responseText.split("<br/>");
			var elt = document.getElementById('pays');
			
			for(var i=0; elt.length; i++) {
				elt.options[0] = null;
			}
			
			for(var i=0; i<tableau.length; i++) {
				elt.options[elt.length] = new Option(tableau[i+1]);
			}
		}
	}
	
	xhr.addEventListener("readystatechange", Lire, false);		
	xhr.send(null); 
}