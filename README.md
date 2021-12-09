# laravel 练手项目3

> 电商软件

## 本地支付回调使用的内网穿透

1. 点击链接注册下载 [ngrok](https://dashboard.ngrok.com/user/signup)

2. 执行命令 `ngrok authtoken xxx`

3. 执行命令 `ngrok http -host-header=larashop.test -region us 80`

