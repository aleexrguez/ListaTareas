Este proyecto es una aplicación web construida con Symfony que permite crear, editar y gestionar listas de tareas con operaciones mediante AJAX y notificaciones tipo toast.

---
## Requisitos previos

Asegúrate de tener instalado en el equipo:

- PHP (>= 8.1)
- Composer
- MySQL o MariaDB
- Git

## Cómo desplegar el proyecto

1. **Clona el repositorio**
```bash
git clone https://github.com/tuusuario/nombre-repo.git
cd nombre-repo
```
2. Instalar dependencias
composer install

4. Configurar la base de datos
cp .env .env.local

5. Edita .env.local y pon tu conexión en esta línea:
DATABASE_URL="mysql://usuario:contraseña@127.0.0.1:3306/nombre_bd?serverVersion=5.7"

4. Crear la base de datos y ejecutar migraciones
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

5. Levantar el servidor de Symfony
symfony server:start

6. Abrir en el navegador
http://localhost:8000

AUTOR:
Alessandro Rodriguez Rojas
alesssandro.rodriguezrojas@gmail.com
