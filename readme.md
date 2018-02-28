# Movie_API
Les films qui peuvent être choisis sont ceux retournés par l'API ouverte "omdbapi.com" dont le titre contient le mot "pirate(s)".
Chaque utilisateur s'inscrit et peut choisir jusqu'à 3 films de pirates qu'ils préfèrent.

## Tech
- API créé sous `Symfony 4.0`
- Chacun de ces webservices renvoi une **réponse au format JSON**

## Installation
- Toutes les dépendances nécessaires sont répertoriées dans le fichier `composer.json`
```
composer install
```

#### Renseigner son propre fichier .env avec les informations nécessaires :
- **Base de données**
```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

## Utilisation
L'API est capable de réaliser les actions suivantes :
- Créer un utilisateur (pseudo, email unique, password, date de naissance, date de création en bdd)
```
Méthode => POST
Route => {{base_url}}/users/register
Données à soumettre en JSON => username - email- password - birthdate (yyyy-mm-dd)
```
- Enregistrer le choix de film d'un utilisateur (3 films maximun)
```
Méthode => POST
Route => {{base_url}}/users/{user_id}/movies
Donnée à soumettre en JSON => title (avec le mot pirate(s) obligatoirement)
!!! Attention les titres de films de OMDb sont en anglais (ex. "Pirates of the Caribbean: At World's End")
```
- Supprimer le choix de film d'un utilisateur
```
Méthode => DELETE
Route => {{base_url}}/users/{user_id}/movies/{movie_id}
```
- Lister les choix de film d'un utilisateur
```
Méthode => GET
Route => {{base_url}}/users/{user_id}/movies
```
- Lister les utilisateurs ayant choisi un film
```
Méthode => GET
Route => {{base_url}}/movies/{omdb_id}/users
```
- Retourner le meilleur film selon l'ensemble des utilisateurs
```
Méthode => GET
Route => {{base_url}}/movies/best
```
