#!/bin/zsh
# Deploys the committed state of this repository to Hostpoint over SSH, using your own SSH key.
# Run from your Mac:
#   ./deploy.sh staging              # https://oxid-architektur.ch.marceli.to
#   ./deploy.sh production           # https://oxid-architektur.ch
#   ./deploy.sh production --dry-run # list what would be uploaded and deleted, change nothing
#
# Frontend assets are built locally and committed (npm run build). The server's .env,
# public/.user.ini (PHP settings), public/storage (symlink from `php artisan storage:link`), public/robots.txt, storage and
# caches are never overwritten or deleted. Neither is a root .htaccess (production's document root is the
# site folder, its .htaccess forwards into public/). public/.htaccess is rebuilt: the Hostpoint panel's block
# (PHP version, up to #@__HCP_END__@#) is kept, the app's rules from deploy/public.htaccess follow it.
# Migrations are not run here: `ssh … /usr/local/php84/bin/php artisan migrate --force` when there are new ones.
set -euo pipefail
cd "$(dirname "$0")"

TARGET=${1:-}
DRY_RUN=${2:-}
case $TARGET in
  staging)
    HOST=pumutuxu@pumutuxu.myhostpoint.ch
    DEST=/home/pumutuxu/www/oxid-architektur.ch
    URL=https://oxid-architektur.ch.marceli.to
    ;;
  production)
    HOST=burkhal2@oxid-arch.ch
    DEST=/home/burkhal2/www/oxid-architektur.ch
    URL=https://oxid-architektur.ch
    ;;
  *)
    echo "Usage: ./deploy.sh staging|production [--dry-run]"
    exit 1
    ;;
esac
if [[ $HOST == TODO || $DEST == TODO ]]; then
  echo "Set HOST and DEST for $TARGET in deploy.sh first."
  exit 1
fi
if [[ -n $DRY_RUN && $DRY_RUN != --dry-run ]]; then
  echo "Unknown option: $DRY_RUN"
  exit 1
fi
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

if [[ -z $DRY_RUN ]]; then
  echo "Running tests…"
  php artisan test
fi

STAGE=$(mktemp -d)
trap 'rm -rf "$STAGE"' EXIT
# mktemp creates the folder as 700; rsync would copy that onto the site folder and lock out the web server.
chmod 755 "$STAGE"

echo "Preparing $(git rev-parse --short HEAD)…"
git archive HEAD | tar -x -C "$STAGE"
# composer's post-autoload-dump runs `seo:robots` here without a .env (so as "production");
# that robots.txt is excluded below and rewritten on the server for its own APP_ENV.
composer install --working-dir="$STAGE" --no-interaction --no-progress --no-dev --optimize-autoloader --quiet

RSYNC_OPTS=(-az --delete)
[[ -n $DRY_RUN ]] && RSYNC_OPTS+=(--dry-run --itemize-changes)

echo "Uploading to $TARGET…"
rsync "${RSYNC_OPTS[@]}" \
  --exclude '/.github' --exclude '/node_modules' --exclude '/tests' --exclude '/.rewrite' \
  --exclude '/.env' --exclude '/public/hot' --exclude '/deploy.sh' --exclude '/deploy' \
  --exclude '/public/robots.txt' \
  --exclude '/public/.htaccess' --exclude '/.htaccess' --exclude '/.claude' \
  --filter 'protect /public/.user.ini' \
  --filter 'protect /public/storage' \
  --filter 'protect /storage/**' \
  --filter 'protect /bootstrap/cache/*.php' \
  "$STAGE/" "$HOST:$DEST/"

if [[ -n $DRY_RUN ]]; then
  echo "Dry run: nothing changed on $TARGET. Lines starting with '*deleting' would be removed."
  exit 0
fi

echo "Updating public/.htaccess…"
ssh "$HOST" "cd '$DEST/public' && { if grep -q '^#@__HCP_END__@#' .htaccess 2>/dev/null; then sed '/^#@__HCP_END__@#/q' .htaccess; echo '# Anything after the comment above is left alone'; echo; fi; cat; } > .htaccess.new && mv .htaccess.new .htaccess" < "$STAGE/deploy/public.htaccess"

echo "Refreshing caches…"
# Cached config/routes/events are dropped by hand first: a stale cache (e.g. from the old Laravel 11 site)
# can name providers that no longer exist, and then artisan can't boot to clear it. packages.php and
# services.php came fresh with the upload.
ssh "$HOST" "cd '$DEST' && rm -f bootstrap/cache/config.php bootstrap/cache/routes-*.php bootstrap/cache/events.php && $PHP artisan optimize:clear >/dev/null && $PHP artisan optimize >/dev/null && $PHP artisan seo:robots"

echo "Deployed $(git rev-parse --short HEAD) to $TARGET: $URL"
