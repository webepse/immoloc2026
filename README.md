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

