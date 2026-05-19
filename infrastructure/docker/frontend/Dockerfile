FROM node:20-alpine

WORKDIR /app

# Exposer le port de Nuxt
EXPOSE 3000

# Script d'entrée
COPY infrastructure/docker/frontend/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
