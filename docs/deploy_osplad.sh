#!/usr/bin/env bash
#
# Despliegue del módulo OSPLAD en SISCON 2.
# Ejecuta: migraciones + seeder de menús/privilegio + (opcional) import de farmacias.
#
# Uso:
#   bash docs/deploy_osplad.sh                       # migra + seeder
#   bash docs/deploy_osplad.sh /ruta/farmacias.csv   # + importa farmacias
#   FARMACIAS_PASSWORD=ClaveProd2026 bash docs/deploy_osplad.sh /ruta/farmacias.csv
#
# Variables de entorno opcionales:
#   PHP_BIN              binario de PHP (default: php)
#   ARTISAN              ruta a artisan (default: ./artisan)
#   FARMACIAS_PASSWORD   password genérica para los usuarios de farmacia
#
set -euo pipefail

PHP_BIN="${PHP_BIN:-php}"
ARTISAN="${ARTISAN:-artisan}"
CSV="${1:-}"
PASSWORD="${FARMACIAS_PASSWORD:-farmaciaosplad2026}"

# Ubicarse en la raíz del proyecto (carpeta padre de docs/)
cd "$(dirname "$0")/.."

if [[ ! -f "$ARTISAN" ]]; then
    echo "ERROR: no se encuentra '$ARTISAN'. Ejecutá el script desde la raíz del proyecto." >&2
    exit 1
fi

log() { printf '\n\033[1;35m==> %s\033[0m\n' "$1"; }

log "1/4 · Limpiando caché de configuración"
"$PHP_BIN" "$ARTISAN" config:clear || true

log "2/4 · Migraciones OSPLAD (solo las específicas, idempotentes)"
OSPLAD_MIGRATIONS=(
    "database/migrations/2026_06_10_000001_create_obras_sociales_table.php"
    "database/migrations/2026_06_10_000002_add_os_id_to_osplad_consumos.php"
    "database/migrations/2026_06_10_000003_add_id_cliente_to_cms_users.php"
    "database/migrations/2026_06_10_000004_add_notif_transito_at_to_osplad_consumos.php"
)
for m in "${OSPLAD_MIGRATIONS[@]}"; do
    if [[ ! -f "$m" ]]; then
        echo "ERROR: no se encuentra la migración: $m" >&2
        exit 1
    fi
    echo "   -> $m"
    "$PHP_BIN" "$ARTISAN" migrate --force --path="$m"
done

log "3/4 · Seeder de menús y privilegio (Farmacias OSPLAD)"
"$PHP_BIN" "$ARTISAN" db:seed --class='Database\Seeders\OspladMenusSeeder' --force

if [[ -n "$CSV" ]]; then
    if [[ ! -f "$CSV" ]]; then
        echo "ERROR: no se encuentra el CSV de farmacias: $CSV" >&2
        exit 1
    fi
    log "4/4 · Importación de farmacias (simulación)"
    "$PHP_BIN" "$ARTISAN" osplad:importar-farmacias "$CSV" --dry-run

    read -r -p $'\n¿Confirmás crear/actualizar los usuarios de farmacia? [s/N] ' resp
    if [[ "$resp" =~ ^[sS]$ ]]; then
        log "4/4 · Importación de farmacias (ejecución)"
        "$PHP_BIN" "$ARTISAN" osplad:importar-farmacias "$CSV" --password="$PASSWORD"
    else
        echo "Importación de farmacias OMITIDA por el usuario."
    fi
else
    log "4/4 · Sin CSV: se omite la importación de farmacias"
    echo "    (Para importar: bash docs/deploy_osplad.sh /ruta/farmacias.csv)"
fi

log "Despliegue OSPLAD finalizado"
cat <<'EOF'

Recordatorios:
  - Verificar en .env: DROGUERIA_DB_* y SEND_NOTIFICATION_WSP_*
  - Cron del scheduler (mensaje "procesando" cada 5 min):
      * * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
  - Probar /admin/osplad/pendientes (admin) y login de una farmacia.
EOF
