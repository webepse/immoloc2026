# Symfony Immoloc2026

## Installation d'un Symfony existant depuis GitHub (avec Webpack)
### 1/ chercher sur le git le projet
```git clone https://github.com/webepse/immoloc2026.git```
### 2/ installer les dépendances PHP (fichier composer.json)
```composer install```
### 3/ installer les dépendances JS (fichier package.json)
```npm install```
### 4/ gestion base de données (création) - voir le fichier .env
```php bin/console doctrine:database:create```
### 5/ Envoyer les tables
```php bin/console doctrine:migrations:migrate``` - faire yes ou entrée parce que yes est pas défaut [yes]
### 6/ remplir les tables avec les fixtures
```php bin/console doctrine:fixtures:load``` - faire yes pour remplir [no] par défaut

## Démarrer les serveurs
### Démarrer le serveur PHP
```symfony server:start```
#### si jamais erreur fait avant:
```symfony server:stop```

### 8/ Démarrer le serveur JavaScript (node)
#### dans un nouveau terminal
```npm run dev-server```
#### ou
```npm run watch```

## cours 2 - installation de bootstrap (à ne pas faire si projet venant de gitHub donc déjà installé)
```composer require symfony/webpack-encore-bundle```

```npm install``` pour installer les dépendance du package.json

```composer require orm-fixtures --dev``` (--dev pour mettre en dependance DEV)

```composer require cocur/slugify```

```composer require fakerphp/faker```

```npm i bootstrap@5.3.8```

## Mise en ligne

besoin de apache pack
```composer require symfony/apache-pack```

### sur o2switch

* Créer un utilisateur pour l'admin des base de données
* Créer la base de données + ajouter l'utilisateur

### connexion ssh

```ssh pseudo@server.o2switch.net```

donner le mot de passe

rentrer dans le dossier www/
```cd www```

git clone 
```git clone https://github.com/webepse/immoloc2026.git```

Rentrer dans le dossier
```cd immoloc2026```

[installer composer si pas encore fait]
```composer i```

### sur FileZilla

* Transfert du fichier .env (avec les données de connexion de o2switch)

* en local faire une ```npm run build```

* envoyer le build dans le dossier public sur le serveur

### pour la base de données en SSH

envoyer les migrations sur le serveur
```php bin/console d:m:m```

envoyer les fixtures sur le serveur
```php bin/console d:f:l```

### retour sur FileZilla

modifier le .env pour le mettre en prod

### sur o2switch faire une adresse (nom de domaine) ou sous domaine vers /public_html/immoloc26/public/
