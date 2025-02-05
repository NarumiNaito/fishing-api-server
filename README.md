## fishing

fishing の API サーバーのリポジトリです。

## 環境

-   PHP8.2
-   Laravel 10

## 環境構築

下記の流れに従って、環境構築を行なってください。

#### clone

```
git clone git@github.com:NarumiNaito/fishing-api-server.git
```

#### build

```
docker compose build
```

#### コンテナ作成

```
docker compose up -d
```

#### コンテナへの接続

```
docker compose exec -it app bin/bash
```
