![rush00](https://media.giphy.com/media/6bdaROKzlkjHRVkyLD/giphy.gif)

Run website using docker . 

```
docker build -t piscine_php_rush00 .
docker-compose up
```


If you encounter permission issues, making sure that you are using docker for mac instead of docker-machine.

PHP Debug
php -S localhost:8000 -t .

Admin Acccount
- username: admin
- password: admin123456