LazyPHP4
========

LazyPHP4 , a lightweight framework for php api developer

![](http://ftqq.com/wp-content/uploads//2013/09/lplogo-210x300.jpg)

## Preview
非稳定版，正在不断调试和更新中。欢迎通过issue提供意见和建议。 更欢迎push fix :D 微博吐槽 @Easy 

我们正在征集这个开源项目的开发人员，接着会做LazyRest（可视化API接口设计界面）、LazyPush（Cordova版本的国内推送方案）。

参与的方式如下：

我们会放出每期的RoadMap，这里是[0.5Beta](https://github.com/geekcompany/LazyPHP4/issues?milestone=1&state=open)的，然后这里是[1.0Beta](https://github.com/geekcompany/LazyPHP4/issues?milestone=2&state=open)的。

有兴趣的同学可以通过评论申请领取对应的任务，得到确认后fork，然后push。我们会在项目说明中提供贡献者列表。

开发交流请加微信群：

![](http://ftqq.com/wp-content/uploads//2014/04/Screen-Shot-2014-04-24-at-14.12.04.png)

## 为API设计
在古代，PHP通常被视为HTML和Data之间的胶水，用来渲染和输出页面。当手机成为人类身体的一部分后，我们发现几乎所有的网站、产品都不可避免的遇到一个需求：多平台整合。

### API先行
如果说响应式布局还能在不同大小的浏览器上为混合式编程挽回一点局面的话，在现在这个APP风行的年代，为了兼容各种客户端（iOS、Android、电视、平板、汽车、手表），业务数据必须变成API接口。MVC的模式变异了，M被彻底分离为接口。PHP未来的核心，将是处理API。

### 相关功能
LP4就是在这样一个背景下设计的，所以比起3，它增加了很多API相关的功能

  - 整合flight，用于处理RestFul请求。
  - controller支持函数注释，可用于指定路由、验证输入参数、生成交互式文档
  - 为了能自动调整路由，提供了编译工具_build.php，用于生成meta文件和路由代码

具体起来呢，就这样：


```php
<?php
    /**
     * 文档分段信息
     * @ApiDescription(section="User", description="查看用户详细信息")
     * @ApiLazyRoute(uri="/user(/@id:[0-9]+",method="GET") 
     * @ApiParams(name="id", type="int", nullable=false, description="Uid", check="i|check_not_empty", cnname="用户ID")
     * @ApiReturn(type="object", sample="{'id':123}")
     */
    public function info($id)
    {
        if( !$user = db()->getData( "SELECT * FROM `user` WHERE `id` =:id LIMIT 1" , $id )->toLine() )
            throw new \Lazyphp\core\DataException("UID 对应的用户数据不存在");
        return send_result( $user );
    }
?>    
```
路由、输入检查和文档全部在注释中搞定，是不是很方便。

LP4的注释标记完全兼容[php-apidoc](https://github.com/calinrada/php-apidoc)，但是扩展了两个标记。

#### @ApiLazyRoute （ 新增
指定方法对应的路由。method和uri部分都遵守[flightPHP](http://flightphp.com/learn)的语法。LP做的事情只是把它拼接起来。

#### @ApiParams （ 扩展
添加了 check和cnname两个属性，用来为参数指定检查函数，以及提供字段的中文解释（在错误提示时有用），如果不需要可以不写。

注意：文档生成已经默认整合到编译工具_build.php中了，生成好的文档在docs目录下。


 
## 规范化

  - 引入了namespace和异常处理
  - 整合了PHPUnit和Behat测试框架
  - 整合了[Composer](https://getcomposer.org/)，支持自动加载
  - 整合了[Phinx](http://phinx.org/)，可对数据库版本进行管理 
  
## 自动化

 - 整合LazyRest，通过可视化界面生成常规的接口代码（TODO）  
 
 
# 手册和规范

## 安装
测试环境需要composer才能运行

### 安装composer
```
$ curl -sS https://getcomposer.org/installer | php
$ mv composer.phar /usr/local/bin/composer
```

### 安装LP4依赖
```
$ cd where/lp4/root
$ composer install
```

### 运行
如果你在不可写的环境（比如SAE）运行LP4，请在上传代码前运行 php _build.php 来生成自动路由。

## 迅捷函数
- function t( $str ) // trim
- function u( $str ) // urlencode
- function i( $str ) // intval
- function z( $str ) // strip_tags
- function v( $str ) // $_REQUEST[$str]
- function g( $str ) // $GLOBALS[$str]
- function ne( $str ) // not emptyy
- function dlog($log)  // 打印日志到文件

## 状态函数
- function is_devmode() // 开发模式
- function on_sae() // 是否运行于SAE


## 数据库相关函数 
- function s( $str ) // escape
- function db() // 返回数据库对象
- function get_data( $sql ) // 根据SQL返回数组
- function get_line( $sql ) // 根据SQL返回单行数据
- function get_var( $sql ) // 根据SQL返回值
- function run_sql( $sql ) // 运行SQL

由于LP4在框架外层做了catch，所以数据库异常会被拦截，并以json格式输出。

LP４还提供了对象方式的数据库操作，返回结果更可控。
```php
<?php
    db()->getData('SELECT * FROM `user`')->toArray(); // 返回数组 
    db()->getData('SELECT * FROM `user` WHERE `id` = :id' , $id )->toLine(); // 返回数组中的一行，参数绑定模式 
    db()->getData('SELECT COUNT(*) FROM `user`')->toVar(); // 返回具体数值 
    db()->getData('SELECT * FROM `user`')->toIndexedArray('id'); // 返回以ID字段为Key的数组 
    db()->getData('SELECT * FROM `user`')->toColumn('id'); // 返回ID字段值的一维数组 
?>
```

### LDO
其实LP4还提供了一个针对表进行数据查询的对象 LDO , 首先从数据表new一个Ldo对象，然后就可以用getXXX语法来查询了。因为支持Limit以后，我实在没搞定正则，所以现在还有ByNothing这种奇葩结构。

嘛，在做简单查询时很好用，getAllById这样的。

```php
<?php
// 根据查询的函数名称自动生成查询语句
$member = new \Lazyphp\Core\Ldo('member');
$member->getAllById('1')->toLine();
$member->getNameById('1')->toVar();
$member->getDataById(array('name','avatar') , 1)->toLine();
$member->getAllByArray(array('name'=>'easy'))->toLine();  
$member->findNameByNothing()->col('name');
$member->findNameByNothingLimit(array(2,5))->col('name'); 

?>
```



## Controller
和之前的版本一样，LP依然使用controller作为主入口。但访问路径从?a=&c=改为路由指定，因此，访问路径和controller名称以及method名称将不再有任何关联。
换句话说，你可以随意指定controller名称以及method名称，但注意其注释中的route不要重复，否则产生覆盖。

## Layout & View
由于只输出Json，所以视图层的东西都不存在了。嗯，只有两个方法

- function send_result( $data )
- function send_error( $type , $info = null )

## 错误处理
在处理逻辑出错时可以直接抛出异常。
自带以下几类
```php
<?php
$GLOBALS['rest_errors']['ROUTE'] = array( 'code' => '10000' , 'message' => 'route error' );
$GLOBALS['rest_errors']['INPUT'] = array( 'code' => '10001' , 'message' => 'input error' );
$GLOBALS['rest_errors']['DATABASE'] = array( 'code' => '30001' , 'message' => 'database error' );
$GLOBALS['rest_errors']['DATA'] = array( 'code' => '40001' , 'message' => 'data error' );
?>
```
可在 _lp/lib/functions.php 文件尾部追加自己的错误类型。比如我们来添加一个时间异常。

第一步追加定义
```php
<?php
$GLOBALS['rest_errors']['TIME'] = array( 'code' => '888888' , 'message' => 'time system error' );
?>
```

然后就可以在controller的方法中抛出了
```
<?php
    public function abc()
    {
        if( true ) throw new \Lazyphp\core\timeException("这里填写具体的错误信息");
    }
?>
```
 