FROM adminer:4-standalone
ENV ADMINER_DESIGN="nette"
COPY index.php /var/www/html
