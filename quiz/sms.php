<?php
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
header('Content-Type: text/html; charset=UTF-8');
include_once '../include/config.php';
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbase);
// Test if connection ok
if (mysqli_connect_errno()) {
	die("Database connection failed 1: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
}

// chercher et sélectionner la "seule" question ouverte
$ouverte="oui";
$sql = "SELECT IdQuest, NomOpe FROM Question  WHERE Ouverte=? " ;
		$stmt = $db->prepare($sql);
        $stmt->bind_param("s", $ouverte);
        $stmt->execute();
        $stmt->store_result(); 
        $rowcount = $stmt->num_rows();
        $stmt->bind_result($IdQuest, $NomOpe);
        while ($stmt->fetch()) {
			$IDQuest= $IdQuest;
			$Nomope= $NomOpe;
		}
		$stmt->close();

// sélection des propositions qui vont avec question
$sql = "SELECT IdProp, BonneRep FROM Propositions  WHERE IdQuest=?" ;
		$stmt = $db->prepare($sql);
        $stmt->bind_param("i", $IDQuest);
        $stmt->execute();
        $stmt->store_result(); 
        $rowcountprop = $stmt->num_rows();
        $stmt->bind_result($IdProp, $BonneRep);
// on construit un tableau pour récupérer l'Id de la prop et Bonne réponse
		while ($stmt->fetch()) {
		$ArrIdProp[]=$IdProp;
		$ArrBonRep[]=$BonneRep;
		}
		$stmt->close();

// valeurs envoyées par Twilio
		$repquiz = $_POST['Body'];
		$numvotant=$_POST['From'];

// recherche dans la table votesms si le pax à déjà voté
$sql = "SELECT * FROM votesms  WHERE numtel=? AND idQuestsms=? " ;
		$stmt = $db->prepare($sql);
        $stmt->bind_param("si", $numvotant, $IDQuest);
        $stmt->execute();
        $stmt->store_result(); 
        $rowcountsms = $stmt->num_rows();
        $stmt->close();

// contrôle pour que l'entrée ne soit qu'un n° ET limité au nbr de prop
$valid=preg_match("/[0-$rowcountprop]/", $repquiz);
if (!$valid) {
   die("");
} else { 
	// s'il n'y a pas de vote trouvé dans votesms
	if ($rowcountsms==0) {

	// boucle pour assigner les valeurs afin de les insérer ensuite
		for ($x = 0; $x <= $rowcountprop; $x++) {
		    if ($repquiz==$x) {
		    $repquiz="P".($x);
			$BonneRep=$ArrBonRep[($x-1)];
			$IdProp=$ArrIdProp[($x-1)];
			}
		 }

		// enregistrement de la reponse dans Reponses		
		$sql = "INSERT INTO `Reponses` (NomOpe, IdQuest, IdProp, Reponse, BonRep) VALUES (?, ?, ?, ?, ?) ";
	    	$stmt = $db->prepare($sql);
        	$stmt->bind_param("siiss", $Nomope, $IDQuest, $IdProp, $repquiz, $BonneRep);
        	$stmt->execute();
        	$stmt->close();
		
		// enregistrement de la reponse dans votesms
		$sql = "INSERT INTO `votesms` (numtel, idQuestsms) VALUES (?, ?) ";
	    	$stmt = $db->prepare($sql);
        	$stmt->bind_param("si", $numvotant, $IDQuest);
        	$stmt->execute();
        	$stmt->close();

	} // fin if rowcount
} // fin if empty
mysqli_close($db);

?>
<!DOCTYPE html >
<html>
		<head>
			
		</head>
		<body>
		
		</body>
</html>
