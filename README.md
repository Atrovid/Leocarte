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

