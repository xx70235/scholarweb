#时代学者网站

#### fork自wordpress master
#### 数据库信息(已在wp-config.php设置好了)
> * host: 47.93.115.68
> * database: scholar
> * username: scholar
> * password: !Cas15801588196
#### 根目录文件夹需设置为scholarweb
#### 管理后台信息
> * url: http://localhost/scholarweb/wp-login.php
> * username: admin
> * password: !Cas15801588196

###2017年5月29日更新
在angecystrap模板中添加了招聘(recruit)、招生(enroll)、资讯(news)三个custom type的页面输出。

添加文件如下：
wp-content/themes/下
* archive-recruit.php
* archive-enroll.php
* archive-news.php
* single-recruit.php
* single-enroll.php
* single-news.php


archive开头的为list页输出，single开头的为details页输出，list页调用wp-content/themes/template-parts中
content-recruit.php，content-enroll.php，content-news.php，details页输出调用p-content/themes/template-parts中content-single-recruit.php，content-single-enroll.php，content-single-news.php

修改以上6个文件即可修改list页和details页展示




