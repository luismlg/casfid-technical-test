# Directorio var/

Este directorio contiene archivos temporales y generados por la aplicación.

## Estructura:

- **cache/** - Cache de peticiones externas (OpenLibrary, etc.)
  - Configurado vía `CACHE_PATH` en `.env`
  - TTL configurable con `CACHE_TTL_SUCCESS` y `CACHE_TTL_FAILURE`

- **logs/** - Archivos de log de la aplicación
  - Configurado vía `LOG_PATH` en `.env`
  - Nivel de log configurable con `LOG_LEVEL`

## Nota:
Los contenidos de `cache/` y `logs/` están ignorados en git y no deben ser versionados.
