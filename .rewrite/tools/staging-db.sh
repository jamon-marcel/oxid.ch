#!/bin/zsh
# Turns a production dump into the staging (or go-live) database, locally:
#   1. loads it into the scratch DB `oxid_staging` (the regular `oxid` DB is untouched),
#   2. runs the rework's migrations on it (image width/height are read from
#      storage/app/public/uploads, so put production's uploads there first),
#   3. dumps the result for Hostpoint (MariaDB 10.11).
#
# Usage: .rewrite/tools/staging-db.sh <prod-dump.sql> <out.sql>
set -euo pipefail
cd "$(dirname "$0")/../.."

IN=${1:?usage: staging-db.sh <prod-dump.sql> <out.sql>}
OUT=${2:?usage: staging-db.sh <prod-dump.sql> <out.sql>}
DB=oxid_staging
MYSQL=(mysql -h 127.0.0.1 -u root)

if [[ -f bootstrap/cache/config.php ]]; then
  echo "Config is cached; DB_DATABASE=$DB would be ignored. Run 'php artisan config:clear' first."
  exit 1
fi

echo "Loading $IN into $DB…"
$MYSQL -e "DROP DATABASE IF EXISTS \`$DB\`; CREATE DATABASE \`$DB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
$MYSQL "$DB" < "$IN"

echo "Uploads referenced but missing locally:"
for t in home_images project_images discourse_images job_images team_images profile_images; do
  $MYSQL -N "$DB" -e "SELECT name FROM \`$t\`" | while read -r name; do
    [[ -f "storage/app/public/uploads/$name" ]] || echo "  $t: $name"
  done
done

echo "Migrating $DB…"
DB_DATABASE=$DB php artisan migrate --force

echo "Dumping to $OUT…"
mysqldump -h 127.0.0.1 -u root --no-tablespaces --single-transaction \
  --default-character-set=utf8mb4 --skip-comments "$DB" > "$OUT"

echo "Done: $OUT ($(du -h "$OUT" | cut -f1)). Images without width/height: "
for t in home_images project_images discourse_images job_images team_images profile_images; do
  echo "  $t: $($MYSQL -N "$DB" -e "SELECT COUNT(*) FROM \`$t\` WHERE width IS NULL")"
done
