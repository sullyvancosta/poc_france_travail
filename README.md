# Sullyvan COSTA - POC - France Travail
## Besoin exprimé 
Le but de cet exercice est de construire une petite application de récupération des offres d’emploi de Rennes, Bordeaux et Paris à partir de l’API de Pôle
Emploi

## Procédure de démarrage
### Start docker environment
 
- Démarrer les containers docker en utilisant la commande suivante : 
```shell
docker compose up -d
```

#### Troubleshooting
- In a case that you are facing this exception : `temporary error (try again later)` please run :
```shell
sudo nano /etc/docker/daemon.json;
```
 -  And add the following content : 
```json
{
    "dns": ["8.8.8.8", "8.8.4.4"]
}
```
- Save the document and then run : 
```shell
sudo systemctl restart docker 
```

### Install dependencies
- Il est nécessaire d'installer les dépendances. Pour ce faire, utiliser les commandes suivantes : 
```shell
docker exec -it scosta_france_travail_php bash;
composer install;
bin/console d:m:m;
```
