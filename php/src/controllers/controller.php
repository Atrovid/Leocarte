<?php

	require_once('src/models/model_api.php');


	function putPresent($codeEtudiant, $nomSalle){
		session_start();
		$beginURL = "https://graphprojet2ainfo.aimaira.net/GraphV1";
		$modelAPI = new ModelApi();

		/* Récupération des identifiant TypeRessource pour une salle et pour un apprenant */
		$url = $beginURL."/TypeRessource/?\$filter=Code%20eq%20'SALLE'";
		$idTypeRessourceSalle = $modelAPI->recupererIdTypeRessource($modelAPI->appelGetAPI($url));
		$url = $beginURL."/TypeRessource/?\$filter=Code%20eq%20'APPRENANT'";
		$idTypeRessourceApprenant = $modelAPI->recupererIdTypeRessource($modelAPI->appelGetAPI($url));
		
		/* Récupération de l'identifiant de la salle */
		$url = $beginURL."/Ressource/?\$filter=TypeRessourceId%20eq%20334210&\$select=Id,Nom";
		$idSalle = $modelAPI->recupererIdSalle($modelAPI->appelGetAPI($url), $nomSalle);
		if ($idSalle == 0){
			return "Erreur";
		}

		/* Récupération de l'identifiant de la planification courante */
		$url = $beginURL."/Ressource/".$idSalle."/PlanificationRessources?\$select=PlanificationId,PlanificationDebut,PlanificationFin";
		$planifications = $modelAPI->appelGetAPI($url);
		$idPlanificationCourante = $modelAPI->recupererIdPlanificationCourante($planifications);
		$dateDebutSeance = $modelAPI->filtrerDateDebut($planifications);

		/* Mise en place d'une session qui va permettre de mettre une seule fois tout
		le monde absent lorsque la première personne à badger */
		if (((!isset($_SESSION["planificationCourante"])) || ($idPlanificationCourante != $_SESSION["planificationCourante"]))==1){
			$_SESSION["planificationCourante"]=$idPlanificationCourante;
			$_SESSION["estPremier"] = true;
		}
		

		/* Première connexion à ce cours : nous mettons d'abord tout les eleves absents */
		if ($_SESSION["estPremier"] == true){
			$url = $beginURL."/Planification/".$idPlanificationCourante."/PlanificationsRessource?\$select=Id,Code&\$filter=TypeRessourceId%20eq%20".$idTypeRessourceApprenant;
			$codesEtudiant = $modelAPI->appelGetAPI($url);
			/* Mettre ensemble des étudiants absents */
			$array = json_decode($codesEtudiant, true);
				foreach ($array['value'] as $etudiant){
					$idEtudiant = $etudiant['Code'];
					$url = $beginURL."/Planification/".$idPlanificationCourante."/PlanificationsRessource?\$select=Id,%20Code&\$filter=Code%20eq%20'".$idEtudiant."'";
					$idPlanificationRessourceEtudiant = $modelAPI->recupererPlanificationRessourceEtudiant($modelAPI->appelGetAPI($url));
					$url = $beginURL."/PlanificationRessource/".$idPlanificationRessourceEtudiant."?\$select=Id,PlanificationId,TypeRessourceId,Reference,ControlePresence,ProvenancePresence,Presence";
					$informationsPourMettreAbsent = $modelAPI->appelGetAPI($url);
					$modelAPI->metEtudiantAbsent($informationsPourMettreAbsent, $idPlanificationRessourceEtudiant, $beginURL);
			}
			//$modelAPI->metTousEtudiantsAbsents($codesEtudiant, $beginURL, $idPlanificationCourante);
			$_SESSION["estPremier"] = false;
		} 

		/* Mettre l'étudiant présent */
		$url = $beginURL."/Planification/".$idPlanificationCourante."/PlanificationsRessource?\$select=Id,%20Code&\$filter=Code%20eq%20'".$codeEtudiant."'";
		$idPlanificationRessourceEtudiant = $modelAPI->recupererPlanificationRessourceEtudiant($modelAPI->appelGetAPI($url));
		$url = $beginURL."/PlanificationRessource/".$idPlanificationRessourceEtudiant."?\$select=Id,PlanificationId,TypeRessourceId,Reference,ControlePresence,ProvenancePresence,Presence";
		$informationsPourMettrePresent = $modelAPI->appelGetAPI($url);
		$modelAPI->metEtudiantPresent($informationsPourMettrePresent, $idPlanificationRessourceEtudiant, $beginURL);
		$url = $beginURL."/Apprenant?\$filter=Code%20eq%20'".$codeEtudiant."'&\$select=NomUsage,PrenomUsage";
		$result = $modelAPI->recupereNomPrenom($modelAPI->appelGetAPI($url));
		echo $result;
		return $result;
	}
	function getInfoFromPresentForm(){
		if($_SERVER["REQUEST_METHOD"]=="GET"){
			require('src/views/viewFormAddStudentPresent.php');
		}
		if($_SERVER["REQUEST_METHOD"]=="POST"){
			$studentID = $_POST['studentID'];
			return $studentID;
		}
	}


?>
