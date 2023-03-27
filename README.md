# Projet 2A : Lecteur de Léocarte

## Arborescence du projet

```bash
.
├── archivage
│ └── php
│ └── src
│ ├── controllers
│ │ ├── controller_api.php
│ │ ├── controller.php
│ │ └── export_csv.php
│ ├── index2.php
│ ├── js
│ │ ├── scriptAPI.js
│ │ ├── scriptForm.js
│ │ └── script.js
│ ├── models
│ │ ├── model_api.php
│ │ └── model.php
│ └── views
│ ├── viewAddInfo.php
│ ├── viewAttendanceAPI.php
│ ├── viewAttendance.php
│ ├── viewConfig.php
│ ├── viewDelete.php
│ ├── viewError404.php
│ ├── viewFormAPI.html
│ ├── viewForm.php
│ ├── viewGetInfo.php
│ ├── viewInfo.php
│ └── viewSearchStudentInClass.php
├── doc
│ ├── Agenda de réunion davancement-1.docx
│ ├── Consignes pour la constitution du dossier projet-2.docx
│ ├── etablir_binome.png
│ ├── Modele de compte rendu de réunion-1.docx
│ ├── Modele de fiche de tâche-1.docx
│ └── presence.png
├── esp8266
│ └── leocarte_reader
│ ├── leocarte_reader.ino
│ └── wifimanager.h
├── php
│ ├── config_example.ini
│ ├── config.ini
│ ├── index.php
│ └── src
│ ├── controllers
│ │ ├── controller.php
│ │ └── requestTagCSN.php
│ ├── models
│ │ └── model_api.php
│ └── views
│ └── viewFormAddStudentPresent.php
├── rapport.md
└── README.md
```

Pour vérifier qu'une database a bien été créée : ?action=attendance  
Pour vérifier qu'une database a bien été détruite : ?action=destroy  
Pour vérifier qu'une ligne d'une database a bien été réalisée : ?action=add  
Pour vérifier qu'une ligne d'une databse a bien été effacée : ?action=delete  
Pour vérifier qu'une database a bien été "print" : ?action=display

http://localhost/leocarte/leocarte/php/?action=attendance

http://localhost/leocarte/leocarte/php/?action=api

Pour utiliser l'application: mettre php curl : sudo apt-get install php-curl

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

## Mise en route du code back end sur le serveur de l'école

### Accès a la session du serveur :

Depuis un terminal Linux (ordinateur personnel, il ne faut pas être sur une session de l'école), taper la commande :

```bash
ssh Login@IP
```

**FACULTATIF :**

Puis lors de la première connexion à la session depuis ssh, on nous demande si on doit faire confiance au certificat de l'ENSI, taper dans le terminal :

```bash
yes
```

### Code source PHP sur le serveur

Le chemin absolu pour atteindre le code source est :

```bash
/var/www/html/
```

Pour atteindre cet endroit là lorsqu'on se connecte en ssh, faire les commandes suivante :

```bash
cd ..
cd ..
cd ./var/www/html/
```

Dans ce fichier, on trouvera donc

- Un fichier de configuration : **config.ini**
- Un fichier d'indexage pour le PHP : **index.php**

Puis en allant plus loin, en allant dans le fichier src :

```bash
cd ./src
```

On trouvera 3 sous-dossiers :

- controllers qui correspond au controlleur
- models qui correspond au modèle
- views qui correspond à la vue

### Lancer une action depuis un navigateur

Aller sur un navigateur (Firefox ou Chrome par exemple)

Taper dans la barre de recherche :

```bash
IP/?action="nomAction"
```

Par exemple, pour une IP equivalent à

```bash
IP = 192.93.212.200
```

et une action :

```bash
ACTION = "curl"
```

On tapera dans la barre de recherche

```bash
192.93.212.200/?action=curl
```
