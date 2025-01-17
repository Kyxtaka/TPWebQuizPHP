# TP site web de Quiz en php 

## TP R3.01 dev/web BUT2


Le groupe est constitué de Nathan Randriantsoa *TD2B*, Tristan Chaloine *TD2B*

## Table des matières
- prérequis
- lancement du projet
- fonctionnalités implémentées avant et après soutenance

### Prérequis
- PHP server 8 
- PHP PDO driver : sqlite

### Lancement du projet
Pour lancer le site web :
- se rendre à la racine du projet, ouvrir src avec ```cd src/```
- dans un terminal, lancer le serveur php avec ```php -S localhost:8000```
- ouvrez un navigateur et tapez l'url [http://localhost:8000/index.php](http://localhost:8000/index.php)
- si vous obtenez l'erreur 'Not Found', assurez vous que le serveur PHP est bien lancer dans le dossier `src/`

### Contraintes et Fonctionnalités implémentées avant et après soutenance
#### Avant soutenance
Les fonctionnalités implémentées avant la soutenance sont :
- Organisation du code dans une arborescence cohérente
- Utilisation des namespaces
- Utilisation d’un provider pour charger le fichier JSON contenant les questions et leurs réponses
- Utilisation d'un autoloader pour charger  les classes d’objets nécessaires
- Utilisation de sessions pour stocker les réponses fournies par les utilisateurs et calculer un score
- Utilisation de classes PHP pour programmer votre application orientée objet
- Utilisation d’un contrôleur dans index.php piloté par GET['action']
- Utilisation de PDO avec base de données sqlite pour stocker le score et le nom du joueur
- Implélention d'une petite librairie à personaliser (selon le projet) de gestion des connexions utilisateur 
- Ajouter éventuellement un système de login et de gestion des utilisateurs.

#### Après soutenance
Les fonctionnalités implémentées après la soutenance sont :
- Ajouter un import de quizz et de questions en JSON

#### A noter : Toute les méthode des classes objects sont documentés