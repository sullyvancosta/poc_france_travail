# Sullyvan COSTA - POC - France Travail
## Specified requirement 
The goal of this exercise is to build a small application to retrieve job offers from Rennes, Bordeaux, and Paris using the Pôle Emploi API.

## Starting procedure
### Start docker environment
 
- Start the containers by using the following command : 
```shell
docker compose up -d
```

#### Troubleshooting
- In a case that you are facing the exception : `temporary error (try again later)` please run :
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
sudo systemctl restart docker;
docker compose up -d; 
```

### Install dependencies
- It is necessary to install the dependencies. To do so, use the following commands : 
```shell
docker exec -it scosta_france_travail_php bash;
composer install;
bin/console d:m:m;
bin/console h:f:l -n;
```

## Run the project
There ara 2 command that can be run : 
* `app:generate-job-offers-statistics`
* `app:import-job-offers-france-travail`

Both does not require any command parameter/option.

### Explaination

#### app:import-job-offers-france-travail

This command will call the France Travail API, for every cities in the DB and persist the result in the DB.
If the job offer already exists in the DB, then it's will be updated.

#### app:generate-job-offers-statistics

Based on job offers in the DB, this command will generate a file with the following content (as example): 

```json
{
    "ADECCO": {
        "country": "FRANCE",
        "job_offers_statistics": {
            "CDD": 1,
            "MIS": 5
        }
    }
}
```

As key, the company name. Inside the object, you will find the job offer's country statistics.

Basically, you will find for each contract type the number of job offers. 

## What can be improved ?
- Retrieve job offers create new token everytime we call the function `getJobOffers`, we can store it and keep it until it's invalid.
- In DB, the enterprise's offer is stored as a string. We can think about, create a dedicated table for it. So far, we don't have the ID.
- We also store the contract type as string. A decated table is preferable.
- The command ImportJobOffersFromFranceTravail performance and code can be improved.
- Sometime, France Travail API returns Job Offer which does not match query parameters given (insee code for Bordeaux also return job offers near Bordeaux). Can be fixed.
- More tests !
