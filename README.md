# Projet 2A : Lecteur de Léocarte
## Introduction
Le but de ce projet est d'automatiser l'assiduité via un lecteur nfc. 
## Arborescence du projet
Le projet est constitué comme suit. L'ensemble des fichiers présents dans le dossier archivage ont été nécessaire pour commencer le projet mais sont aujourd'hui obsolètes. 
Ce projet s'appuie sur une architecture modèle, vue, contrôlleurs. 
Il fait également appel à l'API AIMAIRA qui est celle utilisée pour l'assiduité à l'ENSICAEN. Néanmoins, à ce stade du projet (mars/2023), la base de données n'est pas celle de l'ENSICAEN mais un bac à sable avec des personnes générer aléatoirement.

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

## Mise en place du projet
### Côté back end sur le serveur de l'école
#### Accès a la session du serveur :
Depuis un terminal Linux, taper la commande suivante:
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

Pour atteindre cet endroit lors d'une connexion en ssh, faire les commandes suivante :
```bash
cd ..
cd ..
cd ./var/www/html/
```

Dans ce dossier, il y a :
- Un fichier de configuration : **config.ini**
- Un fichier d'indexage pour le PHP : **index.php**
- un dossier **src** accessible via la commande `cd ./src` contenant trois sous-dossiers :
   - controllers qui correspond aux controlleurs
   - models qui correspond au modèle
   - views qui correspond à la vue

### Lancer une action depuis un navigateur
Aller sur un navigateur (Firefox ou Chrome par exemple)
Taper dans la barre de recherche :
```bash
IP/?action="attendance"&csn="#numeroCSN"&room="#nom de la salle sur l'API"
```

## Stratégie de la présence à partir de la salle
Toutes les réponses au requêtes $get$ sont données en JSON.
1. Tout d'abord, nous allons récupérer le `TypeRessourceId` pour trouver celle correspondant à la salle et également celui pour les apprenants.\
La requête *get* correpondante est : ```https://graphprojet2ainfo.aimaira.net/GraphV1/TypeRessource/```


2. Une fois l'identifiant de type correspondant aux salles, nous prenons toutes les salles à l'aide de la requête *get* suivante : ```https://graphprojet2ainfo.aimaira.net/GraphV1/Ressource/?$filter=TypeRessourceId eq 334210&$select=Id, Nom``` \
Nous choississons de ne récupérer seulement les champs qui nous interessent : `id` et `nom`.
Ensuite nous sélectionnons la salle correspondant à notre boitier.

3. Puis nous cherchons à trouver la planification correpondant à l'heure actuelle via la requête *get*: ```https://graphprojet2ainfo.aimaira.net/GraphV1/Ressource/2225785/PlanificationRessources?$select=PlanificationId, PlanificationDebut,PlanificationFin``` \


4. En parallèle, nous avons le code de l'étudiant qui nous permet de  regarder si l'étudiant figure dans les ressources ici on a pris "Eva Bailly" pour l'exemple. La requête devient : ```https://graphprojet2ainfo.aimaira.net/GraphV1/Planification/2247994/PlanificationsRessource?$select=Id, Code&$filter=Code eq 'A00081'```

5. A partir de l'identifiant nous récupérons toutes ces informations afin de changer la présence à l'aide d'une requette `put` :
```https://graphprojet2ainfo.aimaira.net/GraphV1/PlanificationRessource/2247997?$select=Id, PlanificationId, TypeRessourceId, Reference, ControlePresence, ProvenancePresence, Presence```

## Prérequis du serveur php :

Le projet utilise la fonction curl, qui provient de l'extension du même nom.
Pour l'installer : 
```bash
sudo apt-get install php-curl
```

## Configuration du microcontroller ESP32 :

Le projet nécessite l'installation des librairies suivantes sur Arduino IDE :
- Adafruit_GFX (v1.6.1)
- Adafruit_SSD1306 (v2.0.2)
- SoftwareSerial (EspSoftwareSerial v8.0.1)
- PN532_SWHSU
- PN532 (Ces deux dernières sont à installer manuellement à partir de : https://github.com/elechouse/PN532)

Il faut ensuite compiler et téléverser le 

Il faut créer un fichier config.h (dans le même dossier que leocarte_reader.ino) à partir du fichier config_example.h



