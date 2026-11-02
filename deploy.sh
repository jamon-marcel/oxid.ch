#!/bin/zsh
# Deploys the committed state of this repository to the Hostpoint staging site
# (https://oxid-architektur.ch.marceli.to) over SSH, using your own SSH key.
# Run from your Mac: ./deploy.sh
#
# Frontend assets are built locally and committed (npm run build). The server's .env,
# public/.user.ini (PHP settings), public/storage (symlink from `php artisan storage:link`), public/robots.txt, storage and
# caches are never overwritten or deleted. public/.htaccess is rebuilt: the Hostpoint panel's block
# (PHP version, up to #@**HCP_END**@#) is kept, the app's rules from deploy/public.htaccess follow it.
# Migrations are not run here: `ssh … /usr/local/php84/bin/php artisan migrate --force` when there are new ones.
set -euo pipefail
cd "$(dirname "$0")"

HOST=pumutuxu@pumutuxu.myhostpoint.ch
DEST=/home/pumutuxu/www/oxid-architektur.ch
URL=https://oxid-architektur.ch.marceli.to
# Laravel 13 needs PHP 8.3+; non-interactive SSH doesn't load the profile, so name the binary.
PHP=/usr/local/php84/bin/php

if [[ -n $(git status --porcelain) ]]; then
  echo "You have uncommitted changes. Commit them first; only committed code is deployed."
  exit 1
fi
if [[ ! -f public/build/manifest.json ]]; then
  echo "public/build is missing. Run 'npm run build' and commit the result."
  exit 1
fi

echo "Running tests…"
php artisan test

STAGE=$(mktemp -d)
trap 'rm -rf "$STAGE"' EXIT
# mktemp creates the folder as 700; rsync would copy that onto the site folder and lock out the web server.
chmod 755 "$STAGE"

echo "Preparing $(git rev-parse --short HEAD)…"
git archive HEAD | tar -x -C "$STAGE"
# composer's post-autoload-dump runs `seo:robots` here without a .env (so as "production");
# that robots.txt is excluded below and rewritten on the server for its own APP_ENV.
composer install --working-dir="$STAGE" --no-interaction --no-progress --no-dev --optimize-autoloader --quiet

echo "Uploading…"
rsync -az --delete \
  --exclude '/.github' --exclude '/node_modules' --exclude '/tests' --exclude '/.rewrite' \
  --exclude '/.env' --exclude '/public/hot' --exclude '/deploy.sh' --exclude '/deploy' \
  --exclude '/public/robots.txt' \
  --exclude '/public/.htaccess' \
  --filter 'protect /public/.user.ini' \
  --filter 'protect /public/storage' \
  --filter 'protect /storage/**' \
  --filter 'protect /bootstrap/cache/*.php' \
  "$STAGE/" "$HOST:$DEST/"

echo "Updating public/.htaccess…"
ssh "$HOST" "cd '$DEST/public' && { if grep -q '^#@\*\*HCP_END\*\*@#' .htaccess 2>/dev/null; then sed '/^#@\*\*HCP_END\*\*@#/q' .htaccess; echo '# Anything after the comment above is left alone'; echo; fi; cat; } > .htaccess.new && mv .htaccess.new .htaccess" < "$STAGE/deploy/public.htaccess"

echo "Refreshing caches…"
ssh "$HOST" "cd '$DEST' && $PHP artisan optimize:clear >/dev/null && $PHP artisan optimize >/dev/null && $PHP artisan seo:robots"

echo "Deployed $(git rev-parse --short HEAD) to $URL"
