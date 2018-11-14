<?php
session_start();
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
header('Content-Type: text/html; charset=UTF-8');

include_once '../include/config.php';
$base=$_SESSION['dbdefaut'];
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbase);

// Test if connection ok
	if (mysqli_connect_errno()) {
	die("Database connection failed 1: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
	}

$ordrerep = 0; // initialise le compteur pour afficher une seule fiche à la fois

 
$bonrep2="";
if(isset($_POST["vote"])){ // routine pour ajouter une réponse
	$numprop=(substr($_POST["reponse"], 1)-1); // on extrait le n° après P (réponse)
	$idquestrep=test_input($_POST["idquestdef"]);
	$idproprep=$_POST["idpropdef"][$numprop]; // permet de récupérer la valeur du tableau en fonction du n° de proposition
	$reponse=test_input($_POST["reponse"]);
	$ordrerep = test_input($_POST["ordrerep"]);
	$bonrep2 = test_input($_POST['brp'][$numprop]);
	$grpeparticipant=$_SESSION['grpeparticipant'];
	
	$nomcookie="questfait"; // création d'un cookie pour savoir si la question à déjà été répondue
    $valeurcookie = $idquestrep;
    $temps=$_COOKIE['Temps']*60;
    setcookie($nomcookie,$valeurcookie,time() + ($temps), "/")   ;
    
	$sql = "INSERT INTO `Reponses` (NomOpe, IdQuest, IdProp, Reponse, NumGroupe, BonRep) VALUES ('$base', '$idquestrep', '$idproprep', '$reponse', '$grpeparticipant', '$bonrep2') ";
    	$stmt = $db->prepare($sql);
    	$stmt->bind_param("siisis", $base, $idquestrep, $idproprep, $reponse, $grpeparticipant, $bonrep2);
    	$stmt->execute();
    	$stmt->close(); 
}

$query = "SELECT * FROM Question  WHERE NomOpe='$base' AND Ouverte='oui' AND Ordre > '$ordrerep' ORDER BY Ordre ASC " ;
$result = mysqli_query($db, $query);
$row = mysqli_fetch_assoc($result);
$rowcount = mysqli_num_rows($result);
$idquest=$row['IdQuest'];
$ordrerep = $row['Ordre']; // pour gérer la fiche qui suit pour comparer avec ordre

	if ($rowcount=="0") {
		echo "<script type='text/javascript'>document.location.replace('../Chxmodule.php');</script>";
	}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}	
?>

<head>
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" href="imgs/icon-touchmots.png"/>
	<meta name="apple-mobile-web-app-title" content="Ipad v2">
	<meta Charset='UTF-8'>
	<title>Ipad v2 - Vote</title>
	<link  rel="stylesheet" href="../css/main.css?452">
</head>

<!DOCTYPE html >
<style>

.fond {
	padding-bottom: 50px;
	margin:-10px;
	height:2000px;
	<?php if($_SESSION['BackgImage']==""){ ?>
	background-color: <?php echo $_SESSION["BackgCol"] ?>;
	<?php } else { ?>
	background-image: url("logos/<?php echo $_SESSION['BackgImage'] ?>");
	background-repeat: repeat;
	<?php } ?>
	}
.nomOp {
	font-family: Arial, sans-serif;
	margin-top: 5px;
	margin-right: 15px;
	text-align: right;
	font-size: 1.3em;
}
.TitreUt{
	color:<?php echo $_SESSION['ColTitre']; ?>;
}
.corps{
		display:flex;
		flex-direction: column;
		width:100%;
		flex-wrap: wrap;
		justify-content:space-around;
		align-items:center;
		font-family: Arial, sans-serif;
		margin-top: 100px;
	}
.boutton{
	background-color: <?php echo $_SESSION['ColBtn'];?>;
	color:<?php echo $_SESSION['ColTxt']; ?>;
	border: none;
	border-radius:25px;
    width:150px;
    font-weight: bold;
    padding-top: 15px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}

.labl {
    display : block;
    width: 300px;
    margin-top: 20px;
    /*padding:15px;*/
    font-size:1.2em;
}
.labl > input{ /* HIDE RADIO */
    visibility: hidden; /* Makes input not-clickable */
    position: absolute; /* Remove input from document flow */
}
.labl > input + div{ /* DIV STYLES */
	background-color: <?php echo $_SESSION['ColBoutVot']; ?>;
	color:<?php echo $_SESSION['ColTxtVot']; ?>;
	padding:10px;
    cursor:pointer;
    -webkit-border-radius: 25px;
    border-radius: 25px; 
    border:2px solid transparent;
}
.labl > input:checked + div{ /* (RADIO CHECKED) DIV STYLES */
    background-color: <?php echo $_SESSION['ColBonRep']; ?>;
    /*border: 1px solid #317000;*/
}
.container{
	display:flex;
	flex-direction: column;
	width:100%;
	justify-content:space-evenly;
	align-items: center;
	font-family: Arial, sans-serif;
}
.question{
	width:80%;
	margin-top: 50px;
	margin-bottom: 20px;
	color:<?php echo $_SESSION['ColTitQuest']; ?>;
	font-size:1.4em;
	font-weight: bold;
	text-align: center;
}
.reponses{
	/*background-color: red;*/
	align-items: center;
}
.radio{
	align-items: center;
}
.submit{
	-webkit-appearance: none;
	margin-top: 40px;
	

}
</style>
<body>
<div class="fond">
	<div class="titre">
		<div class="logo">
			<a href ="index.php"><img src="../logos/<?php echo $_SESSION['Logo']; ?>" alt="" class="responsiveimgs"></a>
		</div>
		<div class="nomOp">
			<span class="TitreUt"><?php echo $_SESSION['NomOpe2']; ?></span>
			<span class="TitreUt"><?php echo $_SESSION['NomOpe3']; ?></span>
		</div>
	</div>

	<div class="container">
		
			<?php 
				echo $_COOKIE['questfait'];
				if (isset($_COOKIE['questfait'])){  // test pour savoir si la question à déjà été votée
				 echo "<script type='text/javascript'>document.location.replace('../Chxmodule.php');</script>";
				} else {
			?>
	    	<?php
				$query = "SELECT * FROM Propositions  WHERE IdQuest='$idquest' ORDER BY IdProp ASC" ;
				$result = mysqli_query($db, $query);
				$rowcount = mysqli_num_rows($result);
			?>
			
		<div class="question">
			<span><?php echo addslashes($row['Question']);?></span>
		</div>
			
			<form action="index.php" onsubmit="return validateForm()" name="formulaire" method="post">
			<?php 
		    	$num=1; // compteur pour incrémenter les Propositionsxx ainsi que radio Id
		    	while ($row = mysqli_fetch_assoc($result)) { // routine pour afficher les propositions
		    	$idprop=$row['IdProp'];
		    	$idquest=$row['IdQuest'];
		    	$proposition=stripslashes($row['Proposition']);
				$bonrep3=$row['BonneRep'];
		        $formreponse="P".$num;  // formatage de la réponse (P1, P2, ...)
	    	?>
	    <div class="reponses">
	    	<div class="radio" align="center"> <?php // on passe par des tableaux (brp[]) pour différencier les valeurs de la boucle + P.$num qui sert de compteur ?>
	    			<form action="index.php" onsubmit="return validateForm()" name="formulaire" method="post">
	    			<input type="hidden" name="brp[]" id="brp" value="<?php echo $bonrep3; ?>" >
	    			<input type="hidden" name="idpropdef[]" id="idpropdef" value="<?php echo $idprop; ?>" >
					<input type="hidden" name="num[]" value="<?php echo $num; ?>" >
					<input type="hidden" name="idquestdef" value="<?php echo $idquest; ?>" >
	    			<input type="hidden" name="ordrerep" value="<?php echo $ordrerep; ?>" >
	    			<label class="labl">
	    			<input type="radio" name="reponse" value="<?php echo $formreponse; ?>" >
	    			<div><?php echo $proposition;//." - ".$row['BonneRep']; ?></div>
	    			<!-- <span class="checkmark"></span> -->
					</label>
	    	</div>

	    		<?php  $num=$num+1; $bonrep3=" "; }  ?>	
	    	
	    	<div class="submit" align="center">
	    			<?php if ($_SESSION['Langue']=="fra") { ?>
					<input name="vote" type="submit" value="Soumettre" class="boutton" >
					<?php } else { ?>
					<input name="vote" type="submit" value="Submit" class="boutton" >
					<?php } ?>
	    	</div>
	    	</form>	
			 	<?php } ?>
	 	</div>
	
	</div>
</div>
	<script type="text/javascript">
		function validateForm() {
    var x = document.forms["formulaire"]["reponse"].value;
    if (x == "") {
        alert("Veuillez choisir une réponse / Please choose an answer");
        return false;
    	}
	}

	</script>
	    
	    
		<?php 
		mysqli_free_result($result);
		mysqli_close($db);
		?>	
</body>
</html>