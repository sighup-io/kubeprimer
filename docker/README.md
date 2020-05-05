# PowerApp by [SIGHUP](https://sighup.io)

Write a Dockerfile for each of the following components of this classical 3 tier application.

## Frontend (./frontend):
- use `php:7.3.0-apache`
- copy the content of `./rootfs` to the root of the container
- expose port 80
## Backend (./backend):
- use `node:11.4.0-alpine`
- copy the content of `./rootfs` to the root of the container
- expose port 3000
- at start execute `npm start`
## DB:
- we will just use the image  `mongo:4.0.4`

# Docker-compose:

You will have to use images created previously to create the `docker-compose.yml`:

## Frontend:
- an environment variable "BACKEND_HOST" set to the DNS at which the backend will be reachable
- mapping port 80 of the container to the port 80 on the host
## Backend:
- an environment variable "MONGO_HOST" set to the DNS at which the db will be reachable
## DB:
- a volume mounted on `/data/db`
