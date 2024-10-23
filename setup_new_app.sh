#!/bin/bash

if [ "$#" -ne 2 ]; then
    echo "Usage: $0 <app_name> <domain>"
    exit 1
fi

APP_NAME=$1
DOMAIN=$2
BASE_DIR="$PWD/apps"
TEMPLATE_DIR="$PWD"

# Create app directory
mkdir -p "$BASE_DIR/$APP_NAME"
cd "$BASE_DIR/$APP_NAME" || exit

# Copy template files
cp -r "$TEMPLATE_DIR"/* "$TEMPLATE_DIR"/.* .
if [ -f ".env.example" ]; then
    cp .env.example .env
fi

# Update .env file if it exists
if [ -f ".env" ]; then
    sed -i '' "s/APP_NAME=Laravel/APP_NAME=$APP_NAME/" .env
    sed -i '' "s#APP_URL=http://localhost#APP_URL=https://$DOMAIN#" .env
fi

# Generate app key
php artisan key:generate

# Update app_config.yml
if [ ! -f "$BASE_DIR/../app_config.yml" ]; then
    echo "apps:" > "$BASE_DIR/../app_config.yml"
fi
echo "  - name: $APP_NAME" >> "$BASE_DIR/../app_config.yml"
echo "    domain: $DOMAIN" >> "$BASE_DIR/../app_config.yml"
echo "    port: 80" >> "$BASE_DIR/../app_config.yml"

# Initialize git repository
git init
git add .
git commit -m "Initial commit for $APP_NAME"

echo "New app '$APP_NAME' has been set up in $BASE_DIR/$APP_NAME"
echo "Next steps:"
echo "1. Create a new GitHub repository for this app"
echo "2. Push the code to the new repository"
echo "3. Set up GitHub Secrets for the new repository"
echo "4. Update your DNS settings to point $DOMAIN to your server"