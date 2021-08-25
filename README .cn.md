# NotOnlyFans

NotOnlyFans 是一个开源的、可以自己架设的类似 `onlyfans.com` 的数字内容订阅平台。但不同的是，它采用加密货币(ETH)进行支付，因此内容不会再由支付平台和信用卡公司来决定。

## 截图

### 登入页面

![](images/2021-08-25-21-20-33.png)

### 创建专栏

![](images/2021-08-25-21-21-47.png)

### 专栏浏览

![](images/2021-08-25-21-23-12.png)

### 首页信息流

![](images/2021-08-25-21-33-05.png)

### 通过 ETH 购买专栏 VIP

![](images/2021-08-25-21-28-14.png)

### 投稿和审核

![](images/2021-08-25-21-31-27.png)

### 消息箱

![](images/2021-08-25-21-32-18.png)

### 修改个人信息

![](images/2021-08-25-21-22-24.png)

### 修改个人主页背景图

![](images/2021-08-25-21-34-44.png)

### 黑名单

![](images/2021-08-25-21-36-09.png)

### 多语言

![](images/2021-08-25-21-36-51.png)


## 在线演示网站

- <http://notonlyfans.vip/>

## Self-hosted

### 安装要求

首先我们需要准备一个已经安装好 `docker` 和 `docker-compose` 的服务器，此服务器IP记为 `SIP`。

### 克隆代码并启动Docker

```bash
git clone https://gitlab.com/easychen/not-only-fans.git
cd not-only-fans
docker-compose up -d  --build
```

### 初始化项目数据 

先查看正在运行的Docker容器。

```bash
docker ps
```

把镜像为 `not-only-fans_app` 的容器ID记录下来（简称CID），然后进入容器内部。

```bash
docker exec -it ${container_id} /bin/bash
```

### 初始化Web前端

```bash
cd /app/client/ && yarn install && yarn build
```

### 初始化API

```bash
cd /app/api/ && composer install && mkdir /app/api/storage && chmod -R 0777 /app/api/storage
```

### 域名指向

此镜像采用了不同的域名指向不同的目录，可以在本地host中将一下两个域名指向服务器的IP（即之前的SIP）。

1. `notonlyfans.vip` → SIP （前端域名）
1. `api.notonlyfans.vip` → SIP （API域名）

此时访问即可进行测试。

## 定制化

### 使用自己的域名

1. 修改 `docker/app/vhost.conf` ，将其中的前端域名和API域名换成自己的。
1. 修改 `www/client/.env.production`，将 `REACT_APP_API_BASE` 中的域名更换为你的API域名
1. 修改 `www/api/config/app.php`，将其中的前端域名更换为你的前端域名

### 使用自己的infura

1. 到 https://infura.io/ 开通服务，在 [Dashboard](https://infura.io/dashboard/ethereum) 中点击项目名称，在 `Settings` 中复制其中的Key。
1. 注意根据需要，选择主网或者测试网络。
1. 更新 `www/api/config/app.php` 其中的 `web3_network`。

### 使用自己的合约

1. 修改 `www/api/contract/group.js` 和 `www/api/contract/deploy.js`
1. 通过 `deploy.js` 进行部署
1. 将部署完成的合约地址填入的 `www/api/config/app.php` 中对应的地方

