### Aliyun SLS log Dashboard

#### docker run (debug)

```bash
docker run -it -p 8000:8000 \
-v ./.env:/var/www/.env \
-v ./limits.yml:/var/www/limits.yml \
-v ./slsconfigs.yml:/var/www/slsconfigs.yml \
chinayin/sls-dashboard:v3.0 \
php -S 0.0.0.0:8000 -t /var/www/html
```
