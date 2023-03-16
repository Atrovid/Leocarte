# Projet 2A : Lecteur de Léocarte

Pour vérifier qu'une database a bien été créée : ?action=attendance  
Pour vérifier qu'une database a bien été détruite : ?action=destroy  
Pour vérifier qu'une ligne d'une database a bien été réalisée : ?action=add  
Pour vérifier qu'une ligne d'une databse a bien été effacée : ?action=delete  
Pour vérifier qu'une database a bien été "print" : ?action=display  

http://localhost/leocarte/leocarte/php/?action=attendance

http://localhost/leocarte/leocarte/php/?action=api


Pour utiliser l'application: mettre php curl : sudo apt-get install php-curl

## Requete relative à Aimaira :
Pour l'ensemble de ces requêtes nous prendrons l'exemple sur l'enseignant BAILLY Michel :

1. Tout d'abord pour accéder à notre plateforme, l'enseignant sera amené à entrer son nom et prénom :

2. A partir du nom et prénom retourne l'id:
    -> ``` https://graphprojet2ainfo.aimaira.net/GraphV1/Enseignant?$filter=Nom eq 'BAILLY Michel' ```

3. A partir de l'identifiant de l'enseignant, nous allons trouver les cours (nom et id) qu'il enseigne :
    -> ``` https://graphprojet2ainfo.aimaira.net/GraphV1/Enseignant/2216408/EnseignantsCours/?$select=Nom,CoursId ```


## Nouvelle façon de faire en passant par la salle
1. Lors de la configuration : trouver le `TypeRessourceId` pour trouver celle correspondant à la salle et également celui pour les apprenants à l'aide du logiciel postman.\
La requête correpondante est : `https://graphprojet2ainfo.aimaira.net/GraphV1/TypeRessource/`. \
La réponse donne : {"Id": 334210,
            "Nom": "Salle",
            "Code": "SALLE"}\
            {
            "Id": 334212,
            "Nom": "Apprenant",
            "Code": "APPRENANT"
        }\

2. Une fois l'identifiant de type correspondant aux salles, prendre toutes les salles : `https://graphprojet2ainfo.aimaira.net/GraphV1/Ressource/?$filter=TypeRessourceId eq 334210&$select=Id, Nom`\
Nous choississons de ne récupérer seulement les champs qui nous interessent : id et nom.
Ensuite nous sélectionnons la salle correspondant à notre boitier, ici nous prendrons la salle "C-301" : {"Id": 2225785, "Nom": "C-301"}

3. Puis nous cherchons à trouver la planification correpondant à l'heure actuelle : `https://graphprojet2ainfo.aimaira.net/GraphV1/Ressource/2225785/PlanificationRessources?$select=PlanificationId, PlanificationDebut,PlanificationFin`\
Ici nous prendrons la planification suivante : {"PlanificationId": 2247994,
            "PlanificationDebut": "2023-03-09T16:30:00+01:00",
            "PlanificationFin": "2023-03-09T18:30:00+01:00"}\
Ici, nous prendrons pour l'identifiant de la Planification : 2247994

4. En parallèle, nous avons le code de l'étudiant => regarder si l'étudiant figure dans les ressources ici on a pris "Eva Bailly". La requête devient : `https://graphprojet2ainfo.aimaira.net/GraphV1/Planification/2247994/PlanificationsRessource?$select=Id, Code&$filter=Code eq 'A00081'`

5. A partir de l'identifiant récupère toutes ces informations afin de changer la présence à l'aide d'une requette `put` :
`https://graphprojet2ainfo.aimaira.net/GraphV1/PlanificationRessource/2247997?$select=Id, PlanificationId, TypeRessourceId, Reference, ControlePresence, ProvenancePresence, Presence`
