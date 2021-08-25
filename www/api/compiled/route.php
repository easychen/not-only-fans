<?php 
namespace Lazyphp\Core {
Class RestException extends \Exception {}
Class RouteException extends \Lazyphp\Core\RestException {}
Class InputException extends \Lazyphp\Core\RestException {}
Class DatabaseException extends \Lazyphp\Core\RestException {}
Class DataException extends \Lazyphp\Core\RestException {}
Class AuthException extends \Lazyphp\Core\RestException {}
}
namespace{
$GLOBALS['meta'] = array (
  'f90d08c0b07b8837fd8e7a7f5d6c8e26' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Global',
        'description' => '图片上传',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /attach/upload',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/attach/upload")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'name',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '文件名称',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'name' => 
      array (
        'name' => 'name',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /attach/upload',
        'params' => false,
      ),
    ),
  ),
  '84f1209fc441f785c109b21ea6b3b0e1' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Global',
        'description' => '显示图片接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /attach/@uid/@inner_path',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/attach/{uid}/{inner_path}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'uid',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '图片路径',
      ),
      1 => 
      array (
        'name' => 'inner_path',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '图片路径',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'uid' => 
      array (
        'name' => 'uid',
      ),
      'inner_path' => 
      array (
        'name' => 'inner_path',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /attach/@uid/@inner_path',
        'params' => 
        array (
          0 => 'uid',
          1 => 'inner_path',
        ),
      ),
    ),
  ),
  '07c0b2cf8b7d17d2b9cdd2359f7424bb' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Global',
        'description' => '图片上传',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /image/upload',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/image/upload")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /image/upload',
        'params' => false,
      ),
    ),
  ),
  '04b38f20939aaa541974145669e63fc7' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Global',
        'description' => '图片上传',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /image/upload_thumb',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/image/upload_thumb")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /image/upload_thumb',
        'params' => false,
      ),
    ),
  ),
  'baace13bd1103b0f2dd571f1fe0ddc0f' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '删除内容',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /feed/remove/@id',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/feed/remove/{id}")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /feed/remove/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '3d5f210e6e7338666155d6811536234c' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'group',
        'description' => '设置栏目置顶',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /group/top',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/group/top")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'feed_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '内容id',
      ),
      1 => 
      array (
        'name' => 'group_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '栏目id',
      ),
      2 => 
      array (
        'name' => 'status',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '是否为置顶',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'group_id' => 
      array (
        'name' => 'group_id',
      ),
      'feed_id' => 
      array (
        'name' => 'feed_id',
      ),
      'status' => 
      array (
        'name' => 'status',
        'default' => 1,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /group/top',
        'params' => false,
      ),
    ),
  ),
  '3c1f70e46b0afb5cff87ef19cb4e74a2' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '更新内容',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'text',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '内容内容',
      ),
      1 => 
      array (
        'name' => 'images',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '内容附图',
      ),
      2 => 
      array (
        'name' => 'attach',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '内容附件',
      ),
      3 => 
      array (
        'name' => 'is_paid',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '是否为付费内容',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /feed/update/@id',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/feed/update/{id}")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'text' => 
      array (
        'name' => 'text',
      ),
      'images' => 
      array (
        'name' => 'images',
        'default' => '',
      ),
      'attach' => 
      array (
        'name' => 'attach',
        'default' => '',
      ),
      'is_paid' => 
      array (
        'name' => 'is_paid',
        'default' => 0,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /feed/update/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  'd63397989b9b2b2e58e08acb8189d658' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '发布内容',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'text',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '内容内容',
      ),
      1 => 
      array (
        'name' => 'groups',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '目标栏目',
      ),
      2 => 
      array (
        'name' => 'images',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '内容附图',
      ),
      3 => 
      array (
        'name' => 'attach',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '内容附件',
      ),
      4 => 
      array (
        'name' => 'is_paid',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '是否为付费内容',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /feed/publish',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/feed/publish")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'text' => 
      array (
        'name' => 'text',
      ),
      'groups' => 
      array (
        'name' => 'groups',
      ),
      'images' => 
      array (
        'name' => 'images',
        'default' => '',
      ),
      'attach' => 
      array (
        'name' => 'attach',
        'default' => '',
      ),
      'is_paid' => 
      array (
        'name' => 'is_paid',
        'default' => 0,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /feed/publish',
        'params' => false,
      ),
    ),
  ),
  '624ccb36e7c6c4da9da9cbc5f96b7d87' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '获得栏目列表',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/mine',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/mine")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/mine',
        'params' => false,
      ),
    ),
  ),
  '7fe01059aafdd4d657744ca9e3986d26' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '检查栏目购买数据',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/feed/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/feed/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      1 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
      2 => 
      array (
        'name' => 'filter',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '过滤选项',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 'all',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/feed/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  'dba57401b0ddf7bb0bd46bfd09a7e93a' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '设置栏目成员黑名单',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/blacklist',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/blacklist")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'uid',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '用户ID',
      ),
      1 => 
      array (
        'name' => 'group_id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      2 => 
      array (
        'name' => 'status',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '黑名单状态',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'uid' => 
      array (
        'name' => 'uid',
      ),
      'group_id' => 
      array (
        'name' => 'group_id',
      ),
      'status' => 
      array (
        'name' => 'status',
        'default' => 1,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/blacklist',
        'params' => false,
      ),
    ),
  ),
  'e4215aaf656ae7d6f679e2af47e63fcc' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '设置栏目投稿黑名单',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/contribute_blacklist',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/contribute_blacklist")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'uid',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '用户ID',
      ),
      1 => 
      array (
        'name' => 'group_id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      2 => 
      array (
        'name' => 'status',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '黑名单状态',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'uid' => 
      array (
        'name' => 'uid',
      ),
      'group_id' => 
      array (
        'name' => 'group_id',
      ),
      'status' => 
      array (
        'name' => 'status',
        'default' => 1,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/contribute_blacklist',
        'params' => false,
      ),
    ),
  ),
  '66d6b9565b5e31e18bbcd680a5603600' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '设置栏目评论黑名单',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/comment_blacklist',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/comment_blacklist")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'uid',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '用户ID',
      ),
      1 => 
      array (
        'name' => 'group_id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      2 => 
      array (
        'name' => 'status',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '黑名单状态',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'uid' => 
      array (
        'name' => 'uid',
      ),
      'group_id' => 
      array (
        'name' => 'group_id',
      ),
      'status' => 
      array (
        'name' => 'status',
        'default' => 1,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/comment_blacklist',
        'params' => false,
      ),
    ),
  ),
  '2115d15db59fab4a302e97be244e325f' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '获取栏目成员列表',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/member/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/member/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      1 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
      2 => 
      array (
        'name' => 'filter',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '过滤选项',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 'all',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/member/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  'a4e8efb62d69c4508de5fc9b875bddfa' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '创建栏目',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'name',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '栏目名称',
      ),
      1 => 
      array (
        'name' => 'author_address',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '栏目提现地址',
      ),
      2 => 
      array (
        'name' => 'price_wei',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目年费价格',
      ),
      3 => 
      array (
        'name' => 'cover',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '栏目封面地址',
      ),
      4 => 
      array (
        'name' => 'seller_uid',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '销售商编号',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /group/create',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/group/create")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'name' => 
      array (
        'name' => 'name',
      ),
      'author_address' => 
      array (
        'name' => 'author_address',
      ),
      'price_wei' => 
      array (
        'name' => 'price_wei',
      ),
      'cover' => 
      array (
        'name' => 'cover',
      ),
      'seller_uid' => 
      array (
        'name' => 'seller_uid',
        'default' => 0,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /group/create',
        'params' => false,
      ),
    ),
  ),
  '2a449b3ae24248ba098c9de2ae274050' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '检查栏目购买数据',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/join/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/join/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/join/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '5c4f435a34a8da08ea8e7b546577d3a4' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '检查栏目购买数据',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/quit/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/quit/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'uid' => 
      array (
        'name' => 'uid',
        'default' => NULL,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/quit/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '48f8e1e9b63e83bbbf189ad25be4d318' => 
  array (
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /user/self',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/user/self")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /user/self',
        'params' => false,
      ),
    ),
  ),
  '1fc207d11ff1be809c021a7e7b8fb182' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '检查栏目购买数据',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/vip/check/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/vip/check/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/vip/check/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '03d9fa7d9be1850439560db401785966' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '获取栏目投稿',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/preorder/@group_id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/preorder/{group_id}")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'group_id' => 
      array (
        'name' => 'group_id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/preorder/@group_id',
        'params' => 
        array (
          0 => 'group_id',
        ),
      ),
    ),
  ),
  '173e615047849b81cf2e8f22b9854332' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '获取栏目投稿',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/checkorder',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/checkorder")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'order_id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '订单号',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'order_id' => 
      array (
        'name' => 'order_id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/checkorder',
        'params' => false,
      ),
    ),
  ),
  '0e4bed8e684a81b57509ed69eb3fcc14' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '获取栏目投稿',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/contribute/update',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/contribute/update")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'group_id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      1 => 
      array (
        'name' => 'feed_id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '内容原始ID',
      ),
      2 => 
      array (
        'name' => 'status',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '投稿状态',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'group_id' => 
      array (
        'name' => 'group_id',
      ),
      'feed_id' => 
      array (
        'name' => 'feed_id',
      ),
      'status' => 
      array (
        'name' => 'status',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/contribute/update',
        'params' => false,
      ),
    ),
  ),
  'ec583a25646eb12e88aba5abf24a8d87' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '获取栏目投稿',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/contribute',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/contribute")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
      1 => 
      array (
        'name' => 'filter',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '过滤选项',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 'all',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/contribute',
        'params' => false,
      ),
    ),
  ),
  '53e33ef11f6fbc641fb055bb4e133f92' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '删除内容评论',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /comment/remove',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/comment/remove")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '评论ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /comment/remove',
        'params' => false,
      ),
    ),
  ),
  'cca6be0df04ce03bbc8e971ad500cef4' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '对内容发起评论',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /feed/comment/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/feed/comment/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '内容ID',
      ),
      1 => 
      array (
        'name' => 'text',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '评论内容',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'text' => 
      array (
        'name' => 'text',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /feed/comment/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '917c69f198475a31a21542f4cf6a6cac' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '更新用户密码',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/update_password',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/update_password")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'old_password',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '原密码',
      ),
      1 => 
      array (
        'name' => 'new_password',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '新密码',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'old_password' => 
      array (
        'name' => 'old_password',
      ),
      'new_password' => 
      array (
        'name' => 'new_password',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/update_password',
        'params' => false,
      ),
    ),
  ),
  'a79efe996530957eb437788b139335e6' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '更新用户资料',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/update_profile',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/update_profile")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'nickname',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '用户昵称',
      ),
      1 => 
      array (
        'name' => 'address',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '钱包地址',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'nickname' => 
      array (
        'name' => 'nickname',
      ),
      'address' => 
      array (
        'name' => 'address',
        'default' => '',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/update_profile',
        'params' => false,
      ),
    ),
  ),
  'a452347e00329b9851548355a029fdb5' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '更新用户头像',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/update_avatar',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/update_avatar")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'avatar',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '头像地址',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'avatar' => 
      array (
        'name' => 'avatar',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/update_avatar',
        'params' => false,
      ),
    ),
  ),
  '064c5360e2cce57cf418203a13f4eaa6' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '更新用户封面',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/update_cover',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/update_cover")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'cover',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '头像地址',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'cover' => 
      array (
        'name' => 'cover',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/update_cover',
        'params' => false,
      ),
    ),
  ),
  '57bc77fddd299061ca0cf6072082f2ec' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '更新栏目资料',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /group/update_settings',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/group/update_settings")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      1 => 
      array (
        'name' => 'name',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '栏目名称',
      ),
      2 => 
      array (
        'name' => 'cover',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '封面地址',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'name' => 
      array (
        'name' => 'name',
      ),
      'cover' => 
      array (
        'name' => 'cover',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /group/update_settings',
        'params' => false,
      ),
    ),
  ),
  'c5fc15e0839c1de123cf1cfb16e4d623' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '判断某用户是否在黑名单',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/inblacklist',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/inblacklist")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'uid',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
    ),
    'binding' => 
    array (
      'uid' => 
      array (
        'name' => 'uid',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/inblacklist',
        'params' => false,
      ),
    ),
  ),
  '74b5a58de42cd3cc7fa072d815752abd' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '将某用户添加/移出黑名单',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/blacklist_set',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/blacklist_set")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'uid',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
      1 => 
      array (
        'name' => 'status',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '状态',
      ),
    ),
    'binding' => 
    array (
      'uid' => 
      array (
        'name' => 'uid',
      ),
      'status' => 
      array (
        'name' => 'status',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/blacklist_set',
        'params' => false,
      ),
    ),
  ),
  '9bb360366ed6cc00dba6e777a255e1e2' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '获得当前用户的黑名单',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/blacklist',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/blacklist")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
    ),
    'binding' => 
    array (
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/blacklist',
        'params' => false,
      ),
    ),
  ),
  'cc4d2da04b1a6e02b2daace6fce10dc8' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '获取当前用户的首页信息流',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /timeline/top',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/timeline/top")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /timeline/top',
        'params' => false,
      ),
    ),
  ),
  'b594faf7afc62317ac5c47840fa28c9b' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '获取当前用户的首页信息流',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /timeline',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/timeline")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
      1 => 
      array (
        'name' => 'filter',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '过滤选项',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 'all',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /timeline',
        'params' => false,
      ),
    ),
  ),
  '4826c94a42930c5a9adf65cc5025a31f' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '获取当前用户的首页信息流最新ID',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /timeline/lastid',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/timeline/lastid")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'filter',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '过滤选项',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 'all',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /timeline/lastid',
        'params' => false,
      ),
    ),
  ),
  'dc3799c914be4b0a11c58ed824b795f7' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Message',
        'description' => '获得和某个用户的聊天记录最新id',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /message/lastest_id/@to_uid',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/message/lastest_id/{to_uid}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'to_uid',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '用户ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'to_uid' => 
      array (
        'name' => 'to_uid',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /message/lastest_id/@to_uid',
        'params' => 
        array (
          0 => 'to_uid',
        ),
      ),
    ),
  ),
  '71dac66d631b958218a6cf4b817f87e9' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Message',
        'description' => '获得当前用户未读信息数量',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /message/unread',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/message/unread")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /message/unread',
        'params' => false,
      ),
    ),
  ),
  '987392e16a40f29e6179c143dbe25c2d' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Message',
        'description' => '获得当前用户的最新消息分组列表页面',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /message/grouplist',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/message/grouplist")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /message/grouplist',
        'params' => false,
      ),
    ),
  ),
  '6552d742cac7f92ad2f209f4842952a1' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Message',
        'description' => '获得和某个用户的聊天记录',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /message/history/@to_uid',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/message/history/{to_uid}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'to_uid',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '用户ID',
      ),
      1 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'to_uid' => 
      array (
        'name' => 'to_uid',
      ),
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /message/history/@to_uid',
        'params' => 
        array (
          0 => 'to_uid',
        ),
      ),
    ),
  ),
  '767946cb427ce23f73bd9ee3561cbfbf' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Message',
        'description' => '向某用户发送私信',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /message/send/@to_uid',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/message/send/{to_uid}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'to_uid',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '用户ID',
      ),
      1 => 
      array (
        'name' => 'text',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '私信内容',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'to_uid' => 
      array (
        'name' => 'to_uid',
      ),
      'text' => 
      array (
        'name' => 'text',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /message/send/@to_uid',
        'params' => 
        array (
          0 => 'to_uid',
        ),
      ),
    ),
  ),
  'cdab928e952f62186f6a0f948786524c' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Message',
        'description' => '刷新服务器端用户数据',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /user/refresh',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/user/refresh")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /user/refresh',
        'params' => false,
      ),
    ),
  ),
  'e8ae8e542188b3f2ed9ec092509036b3' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '用户注册接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/register',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/register")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'email',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => 'email',
      ),
      1 => 
      array (
        'name' => 'nickname',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '用户昵称',
      ),
      2 => 
      array (
        'name' => 'username',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '用户唯一ID',
      ),
      3 => 
      array (
        'name' => 'password',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '用户密码',
      ),
      4 => 
      array (
        'name' => 'address',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '钱包地址',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'email' => 
      array (
        'name' => 'email',
      ),
      'nickname' => 
      array (
        'name' => 'nickname',
      ),
      'username' => 
      array (
        'name' => 'username',
      ),
      'password' => 
      array (
        'name' => 'password',
      ),
      'address' => 
      array (
        'name' => 'address',
        'default' => '',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/register',
        'params' => false,
      ),
    ),
  ),
  'c37a59206ad7f0ed8182625ab7218520' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '用户登入接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /user/login',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/user/login")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'email',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => 'email',
      ),
      1 => 
      array (
        'name' => 'password',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '用户密码',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'email' => 
      array (
        'name' => 'email',
      ),
      'password' => 
      array (
        'name' => 'password',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /user/login',
        'params' => false,
      ),
    ),
  ),
  '8e19cb37e1cd8226a43759fcf9f3e63d' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Global',
        'description' => '显示图片接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /image/@uid/@inner_path',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/image/{uid}/{inner_path}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'uid',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '图片路径',
      ),
      1 => 
      array (
        'name' => 'inner_path',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '图片路径',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'uid' => 
      array (
        'name' => 'uid',
      ),
      'inner_path' => 
      array (
        'name' => 'inner_path',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /image/@uid/@inner_path',
        'params' => 
        array (
          0 => 'uid',
          1 => 'inner_path',
        ),
      ),
    ),
  ),
  '00bad2f95e9c66dee775e3b6f98337c3' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '显示栏目基本信息接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/detail/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/detail/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/detail/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '2da513bd579b70f9f4f32ca4910f9625' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '获得栏目列表',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/top100',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/top100")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/top100',
        'params' => false,
      ),
    ),
  ),
  '67d0190709245af09f8b2119f2e4c2b0' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '获取用户基本信息接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /user/detail(/@id)',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/user/detail/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '用户ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
        'default' => NULL,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /user/detail(/@id)',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  'c64c7419319e194a255c98d91516b6af' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '获取内容的全部内容',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /feed/detail/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/feed/detail/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '内容ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /feed/detail/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  'd3e48a19ff848695bc9c6ad2e7449ca6' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Feed',
        'description' => '对内容发起评论',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /feed/comment/list/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/feed/comment/list/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /feed/comment/list/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '191a864f37ba63ba48468ceda03720e6' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'User',
        'description' => '获取用户内容列表',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /user/feed/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/user/feed/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '用户ID',
      ),
      1 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
      2 => 
      array (
        'name' => 'filter',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '过滤选项',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 'all',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /user/feed/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '9d0b73674dcc5fe31ff6f4011fc8977f' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '检查栏目购买数据',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/contract/check/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/contract/check/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/contract/check/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '51dc87210e5eaaebf424e3f0f1a5a734' => 
  array (
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /logout',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/logout")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /logout',
        'params' => false,
      ),
    ),
  ),
  'b648d170a8e98e17d62ef4448af9a21a' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Group',
        'description' => '检查栏目购买数据',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET|POST /group/feed2/@id',
        'ApiMethod' => '(type="GET|POST")',
        'ApiRoute' => '(name="/group/feed2/{id}")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'id',
        'filters' => 
        array (
          0 => 'check_uint',
        ),
        'cnname' => '栏目ID',
      ),
      1 => 
      array (
        'name' => 'since_id',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '游标ID',
      ),
      2 => 
      array (
        'name' => 'filter',
        'filters' => 
        array (
          0 => 'donothing',
        ),
        'cnname' => '过滤选项',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'id' => 
      array (
        'name' => 'id',
      ),
      'since_id' => 
      array (
        'name' => 'since_id',
        'default' => 0,
      ),
      'filter' => 
      array (
        'name' => 'filter',
        'default' => 'all',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET|POST /group/feed2/@id',
        'params' => 
        array (
          0 => 'id',
        ),
      ),
    ),
  ),
  '70c907e8750f400eb470132e210b44cb' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Demo',
        'description' => '乘法接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET /',
        'ApiMethod' => '(type="GET")',
        'ApiRoute' => '(name="/")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET /',
        'params' => false,
      ),
    ),
  ),
  '7d6958088a9dfe0b3ebf7f8db8a4bf92' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'translate',
        'description' => '翻译接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'POST|GET /misswords',
        'ApiMethod' => '(type="POST|GET")',
        'ApiRoute' => '(name="/misswords")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'POST|GET /misswords',
        'params' => false,
      ),
    ),
  ),
  'eb12852dde30c86f2681120ef5001954' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Demo',
        'description' => '乘法接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET /demo/times',
        'ApiMethod' => '(type="GET")',
        'ApiRoute' => '(name="/demo/times")',
      ),
    ),
    'Params' => 
    array (
      0 => 
      array (
        'name' => 'first',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '第一个数',
      ),
      1 => 
      array (
        'name' => 'second',
        'filters' => 
        array (
          0 => 'check_not_empty',
        ),
        'cnname' => '第二个数',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => 
    array (
      'first' => 
      array (
        'name' => 'first',
      ),
      'second' => 
      array (
        'name' => 'second',
      ),
    ),
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET /demo/times',
        'params' => false,
      ),
    ),
  ),
  '912ba5f030422590cb173a37aa702564' => 
  array (
    'Description' => 
    array (
      0 => 
      array (
        'section' => 'Demo',
        'description' => '乘法接口',
      ),
    ),
    'LazyRoute' => 
    array (
      0 => 
      array (
        'route' => 'GET /test',
        'ApiMethod' => '(type="GET")',
        'ApiRoute' => '(name="/test")',
      ),
    ),
    'Return' => 
    array (
      0 => 
      array (
        'type' => 'object',
        'sample' => '{\'code\': 0,\'message\': \'success\'}',
      ),
    ),
    'binding' => false,
    'route' => 
    array (
      0 => 
      array (
        'uri' => 'GET /test',
        'params' => false,
      ),
    ),
  ),
);
$app = new \Lazyphp\Core\Application();
$app->route('POST|GET /attach/upload',array( 'Lazyphp\Controller\AuthedApiController','attachUpload'));
$app->route('GET|POST /attach/@uid/@inner_path',array( 'Lazyphp\Controller\AuthedApiController','showAttachment'));
$app->route('POST|GET /image/upload',array( 'Lazyphp\Controller\AuthedApiController','imageUpload'));
$app->route('POST|GET /image/upload_thumb',array( 'Lazyphp\Controller\AuthedApiController','imageUploadToThumb'));
$app->route('POST|GET /feed/remove/@id',array( 'Lazyphp\Controller\AuthedApiController','feedRemove'));
$app->route('POST|GET /group/top',array( 'Lazyphp\Controller\AuthedApiController','groupTop'));
$app->route('POST|GET /feed/update/@id',array( 'Lazyphp\Controller\AuthedApiController','feedUpdate'));
$app->route('POST|GET /feed/publish',array( 'Lazyphp\Controller\AuthedApiController','feedPublish'));
$app->route('GET|POST /group/mine',array( 'Lazyphp\Controller\AuthedApiController','getMineGroup'));
$app->route('GET|POST /group/feed/@id',array( 'Lazyphp\Controller\AuthedApiController','getGroupFeed'));
$app->route('GET|POST /group/blacklist',array( 'Lazyphp\Controller\AuthedApiController','setGroupBlackList'));
$app->route('GET|POST /group/contribute_blacklist',array( 'Lazyphp\Controller\AuthedApiController','setGroupContributeBlackList'));
$app->route('GET|POST /group/comment_blacklist',array( 'Lazyphp\Controller\AuthedApiController','setGroupCommentBlackList'));
$app->route('GET|POST /group/member/@id',array( 'Lazyphp\Controller\AuthedApiController','getGroupMember'));
$app->route('POST|GET /group/create',array( 'Lazyphp\Controller\AuthedApiController','groupCreate'));
$app->route('GET|POST /group/join/@id',array( 'Lazyphp\Controller\AuthedApiController','joinGroup'));
$app->route('GET|POST /group/quit/@id',array( 'Lazyphp\Controller\AuthedApiController','quitGroup'));
$app->route('GET|POST /user/self',array( 'Lazyphp\Controller\AuthedApiController','userSelfInfo'));
$app->route('GET|POST /group/vip/check/@id',array( 'Lazyphp\Controller\AuthedApiController','checkVipIsPaid'));
$app->route('GET|POST /group/preorder/@group_id',array( 'Lazyphp\Controller\AuthedApiController','GroupPreorder'));
$app->route('GET|POST /group/checkorder',array( 'Lazyphp\Controller\AuthedApiController','GroupCheckorder'));
$app->route('GET|POST /group/contribute/update',array( 'Lazyphp\Controller\AuthedApiController','updateContribute'));
$app->route('GET|POST /group/contribute',array( 'Lazyphp\Controller\AuthedApiController','getContribute'));
$app->route('GET|POST /comment/remove',array( 'Lazyphp\Controller\AuthedApiController','removeFeedComment'));
$app->route('GET|POST /feed/comment/@id',array( 'Lazyphp\Controller\AuthedApiController','saveFeedComment'));
$app->route('POST|GET /user/update_password',array( 'Lazyphp\Controller\AuthedApiController','updateUserPassword'));
$app->route('POST|GET /user/update_profile',array( 'Lazyphp\Controller\AuthedApiController','updateUserInfo'));
$app->route('POST|GET /user/update_avatar',array( 'Lazyphp\Controller\AuthedApiController','updateUserAvatar'));
$app->route('POST|GET /user/update_cover',array( 'Lazyphp\Controller\AuthedApiController','updateUserCover'));
$app->route('POST|GET /group/update_settings',array( 'Lazyphp\Controller\AuthedApiController','updateGroupSettings'));
$app->route('POST|GET /user/inblacklist',array( 'Lazyphp\Controller\AuthedApiController','checkUserInBlacklist'));
$app->route('POST|GET /user/blacklist_set',array( 'Lazyphp\Controller\AuthedApiController','setUserInBlacklist'));
$app->route('POST|GET /user/blacklist',array( 'Lazyphp\Controller\AuthedApiController','getUserBlacklist'));
$app->route('GET|POST /timeline/top',array( 'Lazyphp\Controller\AuthedApiController','getUserTimelineTop'));
$app->route('GET|POST /timeline',array( 'Lazyphp\Controller\AuthedApiController','getUserTimeline'));
$app->route('GET|POST /timeline/lastid',array( 'Lazyphp\Controller\AuthedApiController','getUserTimelineLastId'));
$app->route('GET|POST /message/lastest_id/@to_uid',array( 'Lazyphp\Controller\AuthedApiController','getMessageLatest'));
$app->route('GET|POST /message/unread',array( 'Lazyphp\Controller\AuthedApiController','getMessageUnreadCount'));
$app->route('GET|POST /message/grouplist',array( 'Lazyphp\Controller\AuthedApiController','getMessageGroupList'));
$app->route('GET|POST /message/history/@to_uid',array( 'Lazyphp\Controller\AuthedApiController','getMessageHistory'));
$app->route('GET|POST /message/send/@to_uid',array( 'Lazyphp\Controller\AuthedApiController','sendMessage'));
$app->route('GET|POST /user/refresh',array( 'Lazyphp\Controller\AuthedApiController','refreshUserData'));
$app->route('POST|GET /user/register',array( 'Lazyphp\Controller\GuestApiController','register'));
$app->route('POST|GET /user/login',array( 'Lazyphp\Controller\GuestApiController','login'));
$app->route('GET|POST /image/@uid/@inner_path',array( 'Lazyphp\Controller\GuestApiController','showImage'));
$app->route('GET|POST /group/detail/@id',array( 'Lazyphp\Controller\GuestApiController','getGroupDetail'));
$app->route('GET|POST /group/top100',array( 'Lazyphp\Controller\GuestApiController','getGroupTop100'));
$app->route('GET|POST /user/detail(/@id)',array( 'Lazyphp\Controller\GuestApiController','getUserDetail'));
$app->route('GET|POST /feed/detail/@id',array( 'Lazyphp\Controller\GuestApiController','getFeedDetail'));
$app->route('GET|POST /feed/comment/list/@id',array( 'Lazyphp\Controller\GuestApiController','listFeedComment'));
$app->route('GET|POST /user/feed/@id',array( 'Lazyphp\Controller\GuestApiController','getUserFeed'));
$app->route('GET|POST /group/contract/check/@id',array( 'Lazyphp\Controller\GuestApiController','checkGroupPay'));
$app->route('GET|POST /logout',array( 'Lazyphp\Controller\GuestApiController','logout'));
$app->route('GET|POST /group/feed2/@id',array( 'Lazyphp\Controller\GuestApiController','getGroupFeed2'));
$app->route('GET /',array( 'Lazyphp\Controller\LazyphpController','index'));
$app->route('POST|GET /misswords',array( 'Lazyphp\Controller\LazyphpController','misswords'));
$app->route('GET /demo/times',array( 'Lazyphp\Controller\LazyphpController','demo'));
$app->route('GET /test',array( 'Lazyphp\Controller\LazyphpController','test'));
$app->run();
}
