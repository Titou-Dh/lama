
FROM --platform=$BUILDPLATFORM php:8.2-apache as builder

# Install PHP extensions needed for MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

CMD ["apache2-foreground"]

FROM builder as dev-envs

RUN <<EOF
apt-get update
apt-get install -y --no-install-recommends git
EOF

RUN <<EOF
useradd -s /bin/bash -m vscode
groupadd docker
usermod -aG docker vscode
EOF
# install Docker tools (cli, buildx, compose)
COPY --from=gloursdocker/docker / /

CMD ["apache2-foreground"]