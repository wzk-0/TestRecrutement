# Suivi états de livraison
## Description
**Script de récupération d’informations et de traitement des données**

Ce script présente trois fonctionnalités :

- Récupérer, via l'API de La Poste et grâce à son identifiant, l'état de livraison d'un colis.
- Selon l'état actuel du colis, envoyer un mail au destinataire pour lui indiquer que le colis est toujours en cours de livraison ou qu'il a été livré.
- Exporter dans un fichier CSV tous les statuts récupérés grâce à l'API
## Installation
1. Dans le fichier `config.php`, configurez la clé API de La Poste ainsi que les informations liées au service d'email.
2. Créez une instance de la classe `SuiviColis` et appelez la méthode `deliveryState()`, qui prend en paramètres l'email auquel sera envoyé le message et l'identifiant de livraison du colis. exemple:
```php
require_once'SuiviColis.php';
use TestRecrutement\SuiviColis\SuiviColis;

$suiviColis = new SuiviColis();
$suiviColis->deliveryState('aze@exemple.com',42);
```
## DOC 
L'API utiliser est celle de [LaPoste](https://developer.laposte.fr/catalog-apis/suivi@2?tab=1), c'est une requête GET qui prend en paramètre l'ID du colis ``https://api.laposte.fr/suivi/v2/idships/ID``

Les mail sont envoyer via l'utilitaire PHPMailer present dans le sous dossier ``lib``

Les fichiers CSV sont exportés avec toutes les étapes de la livraison et contiennent les champs suivants : la date, le libellé et le code.  
Les codes possibles sont :

- **DR1** Déclaratif réceptionné
- **DR2** Problème lors de la préparation
- **PC1** Pris en charge
- **PC2** Pris en charge dans le pays d’expédition
- **ET1** En cours de traitement
- **ET2** En cours de traitement dans le pays d’expédition
- **ET3** En cours de traitement dans le pays de destination
- **ET4** En cours de traitement dans un pays de transit
- **EP1** En attente de présentation3DO1Entrée en Douane
- **DO2** Sortie de Douane3DO3Retenu en Douane
- **PB1** Problème en cours3PB2Problème résolu
- **MD2** Mis en distribution4ND1Non distribuable
- **AG1** En attente d'être retiré au guichet
- **RE1** Retourné à l'expéditeur
- **DI0** Distribué en lot
- **DI1** Distribué
- **DI2** Distribué à l'expéditeur
- **DI3**Retardé
- **ID0** Informations douane
