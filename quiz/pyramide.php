<?php
	session_start();
	header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', FALSE);
	header('Pragma: no-cache');
	header('Content-Type: text/html; charset=UTF-8');
	include_once '../include/config.php';
    $base=$_SESSION["dbdefault"];
    
    $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbase);

    // Test if connection ok
    if (mysqli_connect_errno()) {
    	die("Database connection failed 1: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
    }
    // Pour retrouver les éléments dans parametres
    $query = "SELECT NomOpe, NomOpe2, NomOpe3, Logo FROM Parametres  WHERE NomOpe = '$base' " ;
    $result = mysqli_query($db, $query);
    $rowcount = mysqli_num_rows($result);
    $row = mysqli_fetch_assoc($result);
    $NomOpe2=$row['NomOpe2'];
    $NomOpe3=$row['NomOpe3'];
    $Logo=$row['Logo'];
    
?>

<head>
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" href="imgs/icon-touchmots.png"/>
	<meta name="apple-mobile-web-app-title" content="Ipad v2">
	<meta Charset='UTF-8'>
	<title>Résultats Vote S</title>
	<link  rel="stylesheet" href="../css/main.css">
	<link  rel="stylesheet" href="../css/pyramide.css?33333371">
</head>

<?php
	if(isset($_POST["result"])){  // à partir de la liste de question
		$QuestionId=$_POST['idquest'];
	}
	$query = "SELECT * FROM Question  WHERE NomOpe = '$base' AND IdQuest = '$QuestionId' ORDER BY Ordre ASC " ;
    $result = mysqli_query($db, $query);
    $rowcount = mysqli_num_rows($result);
    $row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html >
<body>
<!-- <style type="text/css">
	body { margin:0; }

	.fond{
		
		font-family: Arial, sans-serif;
		/*background-color: #b8d5ff;*/
		
	}
	.container{
	display:flex;
	flex-direction: column;
	height:100%;
	/*background-color:DarkSalmon ;*/
	}

	

	.nomOp {
	display: flex;
	justify-content: center;
	flex-direction: column;
	margin-right: 30px;
	text-align: right;
	font-size: 3vw;
	}

	.centre{
	display:flex;
	flex-direction: column;
	justify-content: space-around;
	height:100%;
	/*background-color: grey;*/
	}

	.corps{
	margin-top: 10px;
	display:flex;
	justify-content: center;
	/*background-color: green;*/
	width:100%;
	flex-wrap: wrap;
	align-items:center;
	/*font-family: Arial, sans-serif;*/
	/*margin-top: 5%;*/
	}
	.question{
		width:80%;
		font-size:5vw;
		font-size:5vh;
		font-weight: bold;
		margin-bottom: 1vh;
		/*background-color:DarkSeaGreen;*/
		
	}
	.ligne{
		margin-top: -10px;
		width:80%;
		height:7px;
		background-color:Indigo;
	}

	.espace{
		height:25px;
		width:80%;
		/*background-color: gold;*/
	}

	.tableau{
	display: flex;
	/*background-color: gold;*/
	width:80%;
	margin-top: 15px;
	/*background-color:LightBlue;	*/
	}
	
	.proposition{
		
		font-size:2.5vh;
		width:100%;
		height:1vh;
		/*margin-bottom: 0.2vh;*/
		/*background-color:Khaki;*/
	}

	.containgraph{
		width:80%;
		margin-top: 10px;
		/*background-color:GreenYellow;*/
	}
	
	.bar{
		display:flex;
		width: 100%;
		height:20px;
		/*background-color:IndianRed ;*/
	}

	.containbar{
		width:90%;
	}

	.pourcentage{
		background: Indigo;
		margin-top: 10px;
		height:20px;
		text-align: right;
		color:white;
		font-size:2vw;
		font-size:2vh;
	}

	.resultat{
		height:50px;
		font-size:3vw;
		font-size:3vh;
		width:10%;
		content: "<br />";
		/*vertical-align: middle;*/
	}
</style> -->

<div class="fond">	
	<div class="container">

		<div class="titre">
			<div class="logo">
				<a href ="index.php"><img src="../logos/<?php echo $Logo; ?>" alt="" class="responsiveimgs"></a>
			</div>
			<div class="nomOp">
				<?php echo $NomOpe2; ?>
				<br>
			<span><?php echo $NomOpe3; ?></span>
			</div>
		</div>


		<div class="centre">
			<div class="corps">
				<div class="question">
					<?php echo $row['Question']; ?>
				</div>
				<div class="ligne"></div>
				<div class="espace"></div>
				<?php
					// Pour avoir le nbr de votes total de la question
					$query = "SELECT * FROM Reponses  WHERE IdQuest='$QuestionId'  " ;
					$result = mysqli_query($db, $query);
					$rowcountquest = mysqli_num_rows($result);
				
					// on liste toutes les propositioins pour cette question
					$query = "SELECT * FROM Propositions  WHERE IdQuest='$QuestionId' ORDER BY IdProp ASC" ;
					$result = mysqli_query($db, $query);
					$rowcountprop = mysqli_num_rows($result);
					$ArrayResult=array();
					$num=1;
					while ($row = mysqli_fetch_assoc($result)) { // Boucle pour lister les propositions
						$proposition=$row['Proposition'];
						$propId=$row['IdProp'];
						$bonneRep=$row['BonneRep'];
						$Px="P".$num;
						include '../include/CalculQuest.php'; // calcul des résultats par proposition
						$ArrayResult[]=array("prop"=>$proposition, "res"=>$resQuestfinal, "bonRep"=>$bonneRep); // création d'un array
						$num=$num+1; 
					}

						$i=1; // pour avoir le num d'ordre des propositions, classées
						$keys = array_column($ArrayResult, 'res'); // pour trier les valeurs en fonction du résultat
						array_multisort($keys, SORT_DESC, $ArrayResult);

						foreach($ArrayResult as $value){ //un loop dans le tableau à 2 dimensions 
							// on assigne les 3 1ères valeurs de la pyramide
							if ($i==1){$Pprop1=$value['prop']; $Pres1=$value['res']; } 
							if ($i==2){$Pprop2=$value['prop']; $Pres2=$value['res']; }
							if ($i==3){$Pprop3=$value['prop']; $Pres3=$value['res']; }
							$i=$i+1;  
							}
				?>

							<div class="Pcontainer">
								<div class="Plignes">
									<div class="Pligne1">
										<div>
											<span style="font-size:2.5em; font-weight: bold; color:Indigo"> <?php echo $Pprop1?></span>
											<br>
											<span style="font-size:2.7em; font-weight: bold; color:red"> <?php echo $Pres1." %" ?></span>
										</div>
									</div>
									<div class="Pligne2">
										<div class="Pligne2-1">
											<span style="font-size:2em; font-weight: bold; color:Indigo"> <?php echo $Pprop2?></span>
											<br>
											<span style="font-size:2.2em; font-weight: bold; color:red"> <?php echo $Pres2." %" ?></span>	
										</div>
										<div class="Pespace2"></div>
										<div class="Pligne2-2">
											<span style="font-size:2em; font-weight: bold; color:Indigo"> <?php echo $Pprop3?></span>
											<br>
											<span style="font-size:2.2em; font-weight: bold; color:red"> <?php echo $Pres3." %" ?></span>	
										</div>
									</div>
									<div class="PcontGraph">
										<div class="Pespace"></div>
										<div class="Pnum2">
											<div class="num2"></div>
											<div class="num2b"><div><span style="font-size:2.3em; font-weight: bold; color:white">2</span></div></div>
										</div>
										<div class="Pespace"></div>
										<div class="Pnum1">
											<div class="num1"></div>
											<div class="num1b"><div><span style="font-size:4em; font-weight: bold; color:white">1</span></div></div>
										</div>
										<div class="Pespace"></div>
										<div class="Pnum3">
											<div class="num3"></div>
											<div class="num3b"><div><span style="font-size:2.4em; font-weight: bold; color:white">3</span></div></div>
										</div>
										<div class="Pespace"></div>
									</div>
								</div>
								
							</div>
							
				
			</div>
		</div>
	</div>
</div>
<?php 
mysqli_free_result($result);
mysqli_close($db);
?>	
			
</body>
</html>