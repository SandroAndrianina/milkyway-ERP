Direct, étape par étape (dev, sur ton Mac) :

## 1. Installe mkcert et génère un certificat local
```bash
brew install mkcert
mkcert -install
mkdir ssl
mkcert -key-file ssl/localhost-key.pem -cert-file ssl/localhost.pem localhost 127.0.0.1
```
*(Note : quand tu déploieras chez ton cousin, tu régénéreras un cert pour l'IP fixe réelle, ex: `mkcert 192.168.1.100`.)*

## 2. Modifie le Dockerfile — ajoute le module SSL
```dockerfile
RUN a2enmod rewrite ssl
```
(à côté de ta ligne `RUN a2enmod rewrite` existante)

## 3. Crée un vhost SSL — `apache-ssl.conf` à la racine du projet
```apache
<VirtualHost *:443>
    DocumentRoot /var/www/html/public
    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/localhost.pem
    SSLCertificateKeyFile /etc/apache2/ssl/localhost-key.pem

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 4. Ajoute au Dockerfile : copie + activation du vhost
```dockerfile
COPY apache-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl
```

## 5. Modifie `docker-compose.yml`
```yaml
services:
  app:
    ports:
      - "8080:80"
      - "8443:443"
    volumes:
      - .:/var/www/html
      - ./ssl:/etc/apache2/ssl
```

## 6. Rebuild
```bash
docker compose down
docker compose up -d --build
```
Teste `https://localhost:8443` — le navigateur doit afficher un cadenas valide (mkcert est reconnu localement).

## 7. Config CI4 — `app/Config/App.php`
```php
public bool $forceGlobalSecureRequests = true;
```
*(C'est le vrai nom du paramètre en CI4, pas `forceHTTPS` — c'est ce paramètre qui redirige tout en HTTPS automatiquement.)*

Une fois ça validé en local, tu passes à l'Étape 4 (JWT). Le `badCertificateCallback` Flutter, c'est pour plus tard (Étape 7, dev mobile) — pas maintenant.