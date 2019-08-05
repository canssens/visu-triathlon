# visu-triathlon
A simple php application to consume data from DecathlonCoach through API

This simple app connects your DECATHLON Account linked to your sports activities and displays the time spents on the last 12 weeks in the sports: Running, Swimming and Cycling.
It could be useful to manage your trainings and to check the balance between the 3 sports.


## test locally

* rename the file .env.dist to .end
* fill the parameters to get access to the API Sports Tracking Data : https://developers.decathlon.com/sports-tracking-data/

```
 composer install
 php bin/console server:start
```
