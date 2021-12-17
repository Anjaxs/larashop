# Larashop 电商软件

> 根据  [Laravel 教程 - 电商实战](https://learnku.com/courses/laravel-shop/8.x) 和 [Laravel 教程 - 电商进阶](https://learnku.com/courses/ecommerce-advance/8.x) 而来

## 开发环境

### laradock

启动的容器的命令: 

`docker-compose up -d nginx redis mysql laravel-horizon mailhog elasticsearch`

> 说明: mailhog 用于开发时接收邮件

进入到容器:

`docker-compose exec --user=laradock workspace bash`

安装 IK 分词器安装 (请安装自己laradock elasticsearch 对应的版本)

`docker-compose exec elasticsearch /usr/share/elasticsearch/bin/elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v7.5.1/elasticsearch-analysis-ik-7.5.1.zip`

#### 修改 hosts 文件

增加一条记录 127.0.0.1 larashop.test
#### nginx 配置

复制 laradock/nginx/sites 文件夹下的 laravel.conf.example 文件, 重命名为 larashop.conf

larashop.conf 文件里面 server_name 改为 laravel.test; root 路径修改为自己项目路径

#### laravel-horizon 配置

复制 laradock/laravel-horizon/supervisord.d 文件夹下的 laravel-horizon.conf.example 文件, 重命名为 larashop.conf

larashop.conf 文件里面 command 修改为自己项目路径

#### workspace crontab 配置

复制 workspace/crontab 文件夹下的 laradock 文件, 重命名为 larashop

larashop 文件里面 command 修改为自己项目路径

### 本地支付回调使用的内网穿透

1. 点击链接注册下载 [ngrok](https://dashboard.ngrok.com/user/signup)

2. 执行命令 `ngrok authtoken xxx`

3. 执行命令 `ngrok http -host-header=larashop.test -region us 80`

## 安装 Larashop

1. `git clone git@github.com:Anjaxs/larashop.git`

2. 进入到容器 `cd laradock && docker-compose exec --user=laradock workspace bash`

3. `composer config -g repo.packagist composer https://mirrors.aliyun.com/composer`

4. `composer install`

5. `yarn config set registry https://registry.npm.taobao.org`

6. `yarn dev`

7. `cp .env.example .env`

8. `php artisan key:generate`

9. `php artisan storage:link`

10. `php artisan migrate`

11. `php artisan db:seed --class=AdminTablesSeeder`

12. `php artisan admin:create-user`



