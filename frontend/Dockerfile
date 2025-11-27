# syntax=docker/dockerfile:1

########################
# 1) Build stage
########################
FROM node:20-alpine AS builder

WORKDIR /app

# Dépendances système pour les modules natifs (ex: better-sqlite3)
RUN apk add --no-cache python3 make g++ pkgconfig

# Copier uniquement les fichiers de dépendances
COPY package.json package-lock.json .npmrc ./

# Installation des dépendances (mode clean, reproductible)
RUN npm ci

# Copier le code source
COPY . .

# Build Nuxt pour la prod (génère .output/)
ENV NODE_ENV=production
RUN npm run build

########################
# 2) Runtime stage
########################
FROM node:20-alpine

WORKDIR /app

# Copier uniquement ce qui est nécessaire au runtime
COPY package.json package-lock.json .npmrc ./
RUN npm ci --omit=dev

# Copier le build Nitro de Nuxt
COPY --from=builder /app/.output ./.output

# Variables de base pour Nuxt/Nitro
ENV NODE_ENV=production
ENV PORT=3000
ENV NITRO_PORT=3000
ENV NITRO_HOST=0.0.0.0

EXPOSE 3000

# Démarrage du serveur Nuxt (SSR)
CMD ["node", ".output/server/index.mjs"]
