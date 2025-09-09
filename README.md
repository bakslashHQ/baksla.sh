![COLORED BLACK](https://github.com/user-attachments/assets/cdb3306a-baa2-4eac-aa92-9e8a5c5a0871)  
[baksla.sh](https://baksla.sh/)

## Requirements

- Make
- Orbstack / Docker

## Installation

```shell
make build
make up
make app.install
```

## Usage

Start the environment:
```shell
make start
```

Display available commands:
```shell
make help
```

Automatically clear Symfony cache when writing an article:
```shell
fswatch templates/articles -o | xargs -n1 -I{} docker compose exec -T php bin/console cache:clear
```
