# Docker Configuration Files

Este directorio contiene archivos de configuración para Docker.

## Archivos

### init.sql
Script de inicialización de la base de datos MySQL.

**Qué hace:**
- Crea la base de datos `casfid` con charset UTF-8
- Crea la tabla `books` con todas las columnas necesarias
- Inserta 3 libros de ejemplo (Clean Code, The Pragmatic Programmer, Design Patterns)
- Crea índices para optimizar búsquedas

**Cuándo se ejecuta:**
- Automáticamente al crear el contenedor de MySQL por primera vez
- Solo se ejecuta si la base de datos no existe

### mysql.cnf
Configuración personalizada de MySQL para desarrollo.

**Optimizaciones incluidas:**
- Charset UTF-8 por defecto
- Logs de queries lentas habilitados
- Buffer pool de InnoDB optimizado
- Configuración de autenticación nativa

## Uso

Estos archivos son montados automáticamente por `docker-compose.yml`:

```yaml
volumes:
  - ./docker/init.sql:/docker-entrypoint-initdb.d/init.sql:ro
  - ./docker/mysql.cnf:/etc/mysql/conf.d/custom.cnf:ro
```

No necesitas hacer nada manualmente, Docker los usa automáticamente.

## Reinicializar Base de Datos

Si quieres reinicializar la base de datos con los datos de ejemplo:

```bash
# Detener y eliminar volúmenes
docker-compose down -v

# Levantar de nuevo (ejecutará init.sql)
docker-compose up -d
```

## Personalizar Datos Iniciales

Para cambiar los libros de ejemplo, edita `init.sql` y modifica los INSERT:

```sql
INSERT INTO books (title, author, isbn, year, description, cover_url) VALUES
(
    'Tu Libro',
    'Tu Autor',
    '1234567890',
    2024,
    'Descripción...',
    'https://...'
);
```

Luego reinicia con `docker-compose down -v && docker-compose up -d`.
