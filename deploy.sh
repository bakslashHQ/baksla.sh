# change working directory
cd "$(dirname "$0")"

# pull the latest code version
git fetch --all --prune
git reset origin/main --hard

# fix unexpected permission issues
sudo chown -R debian:debian docker/

# build and restart services
. ../env-vars && docker compose -f compose.yaml -f compose.prod.yaml build
. ../env-vars && docker compose -f compose.yaml -f compose.prod.yaml down
. ../env-vars && docker compose -f compose.yaml -f compose.prod.yaml up -d --remove-orphans
