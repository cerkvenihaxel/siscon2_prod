#!/usr/bin/env python3
"""
Alta interactiva de una farmacia OSPLAD en cms_users.
Lee la config de DB del .env de Laravel (raíz del proyecto).

Idempotente: si el email ya existe, ofrece actualizar datos pero no pisa la contraseña.

Uso:
    python3 scripts/osplad/farmacias/farmacia_nueva.py [--dry-run]
"""
import argparse
import re
import sys
from pathlib import Path

try:
    import mysql.connector
except ImportError:
    sys.exit("Falta mysql-connector-python. Instalalo con: pip install mysql-connector-python")

DEFAULT_PASSWORD  = "farmaciaosplad2026"
PRIVILEGIO_NOMBRE = "Farmacias OSPLAD"


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


def pedir(prompt: str, requerido: bool = True) -> str:
    while True:
        valor = input(prompt).strip()
        if valor or not requerido:
            return valor
        print("  Este campo es obligatorio.")


def solo_digitos(valor: str) -> str:
    return re.sub(r"\D", "", valor)


def pedir_email() -> str:
    while True:
        valor = input("Email *: ").strip().lower()
        if re.fullmatch(r"[^@\s]+@[^@\s]+\.[^@\s]+", valor):
            return valor
        print("  Email inválido, intentá de nuevo.")


def resolver_privilegio(cur, conn, dry_run: bool) -> int:
    cur.execute("SELECT id FROM cms_privileges WHERE name = %s", (PRIVILEGIO_NOMBRE,))
    row = cur.fetchone()
    if row:
        return row["id"]
    if dry_run:
        print(f"[DRY-RUN] El privilegio '{PRIVILEGIO_NOMBRE}' no existe; se crearía.")
        return 0
    cur.execute(
        "INSERT INTO cms_privileges (name, is_superadmin, theme_color, created_at, updated_at) "
        "VALUES (%s, 0, 'skin-blue', NOW(), NOW())",
        (PRIVILEGIO_NOMBRE,)
    )
    conn.commit()
    priv_id = cur.lastrowid
    print(f"Privilegio '{PRIVILEGIO_NOMBRE}' creado (id {priv_id}).")
    return priv_id


def main():
    parser = argparse.ArgumentParser(description="Alta interactiva de farmacia OSPLAD")
    parser.add_argument("--dry-run", action="store_true", help="Simula sin escribir en la DB")
    parser.add_argument("--password", default=DEFAULT_PASSWORD, help="Contraseña para nuevos usuarios")
    args = parser.parse_args()

    project_root = Path(__file__).resolve().parents[3]
    env_path = project_root / ".env"
    if not env_path.exists():
        sys.exit(f"No se encontró .env en {env_path}")

    env = leer_env(env_path)

    print("=== Alta de farmacia OSPLAD ===")
    print("(Campos marcados con * son obligatorios)\n")

    nombre     = pedir("Nombre de la farmacia *: ")
    provincia  = pedir("Provincia *: ")
    localidad  = pedir("Localidad *: ")
    domicilio  = pedir("Domicilio *: ")
    cp         = pedir("Código postal *: ")
    telefono   = pedir("Teléfono (solo dígitos): ", requerido=False)
    email      = pedir_email()
    id_cliente = solo_digitos(pedir("N° cliente Zafiro *: "))

    if not id_cliente:
        sys.exit("El N° cliente Zafiro es obligatorio.")

    print(f"""
--- Confirmación ---
Nombre:      {nombre}
Provincia:   {provincia}
Localidad:   {localidad}
Domicilio:   {domicilio}
CP:          {cp}
Teléfono:    {solo_digitos(telefono) or '-'}
Email:       {email}
N° Zafiro:   {id_cliente}
--------------------""")

    if input("¿Confirmar carga? [s/N]: ").strip().lower() != "s":
        print("Operación cancelada.")
        sys.exit(0)

    conn = conectar(env)
    cur  = conn.cursor(dictionary=True)
    priv_id = resolver_privilegio(cur, conn, args.dry_run)

    cur.execute("SELECT id, status FROM cms_users WHERE email = %s", (email,))
    existente = cur.fetchone()

    if existente:
        if input(f"\nEl email '{email}' ya existe. ¿Actualizar datos (no se pisa contraseña)? [s/N]: ").strip().lower() != "s":
            print("Operación cancelada.")
        else:
            if not args.dry_run:
                cur.execute(
                    """UPDATE cms_users SET
                        name=%s, id_cms_privileges=%s, id_cliente=%s,
                        provincia=%s, departamento=%s, domicilio=%s,
                        codigo_postal=%s, telefono=%s, updated_at=NOW()
                       WHERE email=%s""",
                    (nombre, priv_id, id_cliente, provincia, localidad,
                     domicilio, cp, solo_digitos(telefono), email)
                )
                conn.commit()
            print(f"{'[DRY-RUN] ' if args.dry_run else ''}Actualizado: {email} (cliente {id_cliente})")
    else:
        password_hash = hash_bcrypt(args.password)
        if not args.dry_run:
            cur.execute(
                """INSERT INTO cms_users
                    (name, email, password, id_cms_privileges, id_cliente,
                     provincia, departamento, domicilio, codigo_postal, telefono,
                     status, created_at, updated_at)
                   VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,'Active',NOW(),NOW())""",
                (nombre, email, password_hash, priv_id, id_cliente,
                 provincia, localidad, domicilio, cp, solo_digitos(telefono))
            )
            conn.commit()
        print(f"{'[DRY-RUN] ' if args.dry_run else ''}Creado: {email} (cliente {id_cliente})")
        if not args.dry_run:
            print(f"Contraseña genérica: \"{args.password}\"")

    cur.close()
    conn.close()


if __name__ == "__main__":
    main()
