#!/usr/bin/env python3
"""
Carga batch de las 6 farmacias OSPLAD en cms_users.
Lee la config de DB del .env de Laravel (raíz del proyecto).

Idempotente: si el email ya existe, actualiza datos pero no pisa la contraseña.

Uso:
    python3 scripts/osplad/farmacias/farmacias_batch.py [--dry-run]
"""
import argparse
import os
import re
import sys
from pathlib import Path

try:
    import mysql.connector
except ImportError:
    sys.exit("Falta mysql-connector-python. Instalalo con: pip install mysql-connector-python")

# ---------------------------------------------------------------------------
# Farmacias a cargar
# ---------------------------------------------------------------------------
FARMACIAS = [
    {
        "name":         "FARMACIA AMMACH",
        "provincia":    "Chaco",
        "departamento": "Presidente Roque Saenz Peña",
        "domicilio":    "Carlos Pellegrini 373",
        "codigo_postal":"3700",
        "telefono":     "03644423385",
        "email":        "mutualammach@hotmail.com",
        "id_cliente":   "210004551",
    },
    {
        "name":         "FARMACIA WILL",
        "provincia":    "Córdoba",
        "departamento": "Córdoba",
        "domicilio":    "Centro Norte Santa Rosa N° 390",
        "codigo_postal":"5000",
        "telefono":     "3514444446",
        "email":        "oncologia.fw@gmail.com",
        "id_cliente":   "210004333",
    },
    {
        "name":         "FARMACIA DEL PUEBLO SCS",
        "provincia":    "Corrientes",
        "departamento": "Goya",
        "domicilio":    "Jose Gomez N° 954",
        "codigo_postal":"3450",
        "telefono":     "3777664404",
        "email":        "roxana_ines_gonzalez@yahoo.com.ar",
        "id_cliente":   "210004244",
    },
    {
        "name":         "FARMACIA VITAM",
        "provincia":    "Mendoza",
        "departamento": "Mendoza",
        "domicilio":    "Videla Correa N° 402",
        "codigo_postal":"5500",
        "telefono":     "2613378398",
        "email":        "mosconifabricio@gmail.com",
        "id_cliente":   "210004398",
    },
    {
        "name":         "San Francisco Casa Central",
        "provincia":    "Salta",
        "departamento": "Salta",
        "domicilio":    "Dean Funes N 596",
        "codigo_postal":"4401",
        "telefono":     "3875893070",
        "email":        "waldo.burgos@drogueriasf.com.ar",
        "id_cliente":   "210004529",
    },
    {
        "name":         "FARMACIA SANTA FE (SUCESION DE BELMONTE)",
        "provincia":    "Tucumán",
        "departamento": "Capital",
        "domicilio":    "Santa Fe N° 302",
        "codigo_postal":"4000",
        "telefono":     "3816507403",
        "email":        "farm.santafe@gmail.com",
        "id_cliente":   "210004026",
    },
]

DEFAULT_PASSWORD = "farmaciasosplad2026"
PRIVILEGIO_NOMBRE = "Farmacias OSPLAD"

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------
def leer_env(env_path: Path) -> dict:
    config = {}
    with open(env_path) as f:
        for line in f:
            line = line.strip()
            if line and not line.startswith("#") and "=" in line:
                k, _, v = line.partition("=")
                config[k.strip()] = v.strip().strip('"').strip("'")
    return config


def conectar(env: dict):
    return mysql.connector.connect(
        host=env.get("DB_HOST", "localhost"),
        port=int(env.get("DB_PORT", 3306)),
        database=env.get("DB_DATABASE", "siscon"),
        user=env.get("DB_USERNAME", "root"),
        password=env.get("DB_PASSWORD", ""),
    )


def hash_bcrypt(password: str) -> str:
    try:
        import bcrypt
        return bcrypt.hashpw(password.encode(), bcrypt.gensalt(rounds=12)).decode()
    except ImportError:
        sys.exit("Falta bcrypt. Instalalo con: pip install bcrypt")


def solo_digitos(valor: str) -> str:
    return re.sub(r"\D", "", valor)


# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------
def main():
    parser = argparse.ArgumentParser(description="Carga batch farmacias OSPLAD")
    parser.add_argument("--dry-run", action="store_true", help="Simula sin escribir en la DB")
    parser.add_argument("--password", default=DEFAULT_PASSWORD, help="Contraseña para nuevos usuarios")
    args = parser.parse_args()

    project_root = Path(__file__).resolve().parents[3]
    env_path = project_root / ".env"
    if not env_path.exists():
        sys.exit(f"No se encontró .env en {env_path}")

    env = leer_env(env_path)
    conn = conectar(env)
    cur = conn.cursor(dictionary=True)

    # Resolver privilegio
    cur.execute("SELECT id FROM cms_privileges WHERE name = %s", (PRIVILEGIO_NOMBRE,))
    row = cur.fetchone()
    if not row:
        if args.dry_run:
            print(f"[DRY-RUN] El privilegio '{PRIVILEGIO_NOMBRE}' no existe; se crearía.")
            priv_id = 0
        else:
            cur.execute(
                "INSERT INTO cms_privileges (name, is_superadmin, theme_color, created_at, updated_at) "
                "VALUES (%s, 0, 'skin-blue', NOW(), NOW())",
                (PRIVILEGIO_NOMBRE,)
            )
            conn.commit()
            priv_id = cur.lastrowid
            print(f"Privilegio '{PRIVILEGIO_NOMBRE}' creado (id {priv_id}).")
    else:
        priv_id = row["id"]

    password_hash = hash_bcrypt(args.password)
    creados = actualizados = 0

    for f in FARMACIAS:
        email = f["email"].lower().strip()
        id_cliente = solo_digitos(f["id_cliente"])

        cur.execute("SELECT id, status FROM cms_users WHERE email = %s", (email,))
        existente = cur.fetchone()

        if existente:
            if not args.dry_run:
                cur.execute(
                    """UPDATE cms_users SET
                        name=%s, id_cms_privileges=%s, id_cliente=%s,
                        provincia=%s, departamento=%s, domicilio=%s,
                        codigo_postal=%s, telefono=%s, updated_at=NOW()
                       WHERE email=%s""",
                    (f["name"], priv_id, id_cliente,
                     f["provincia"], f["departamento"], f["domicilio"],
                     f["codigo_postal"], solo_digitos(f["telefono"]), email)
                )
                conn.commit()
            actualizados += 1
            print(f"  ~ Actualizado: {email}  (cliente {id_cliente})")
        else:
            if not args.dry_run:
                cur.execute(
                    """INSERT INTO cms_users
                        (name, email, password, id_cms_privileges, id_cliente,
                         provincia, departamento, domicilio, codigo_postal, telefono,
                         status, created_at, updated_at)
                       VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,'Active',NOW(),NOW())""",
                    (f["name"], email, password_hash, priv_id, id_cliente,
                     f["provincia"], f["departamento"], f["domicilio"],
                     f["codigo_postal"], solo_digitos(f["telefono"]))
                )
                conn.commit()
            creados += 1
            print(f"  + Creado:      {email}  (cliente {id_cliente})")

    cur.close()
    conn.close()

    prefix = "[DRY-RUN] " if args.dry_run else ""
    print(f"\n{prefix}Listo. Creados: {creados} | Actualizados: {actualizados}")
    if creados > 0 and not args.dry_run:
        print(f"Contraseña genérica: \"{args.password}\"")


if __name__ == "__main__":
    main()
