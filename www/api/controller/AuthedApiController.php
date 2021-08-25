<?php
namespace Lazyphp\Controller;

class AuthedApiController
{
    public function __construct()
    {
        // stoken 走最高优先级
        $stoken = t(v('stoken'));
        if (strlen($stoken) > 0) {
            login_by_stoken($stoken);
        } else {
            // 不认 cookie 带来的 php sessionid
            $token = t(v('token'));
            if (strlen($token) < 1) {
                return lianmi_throw('NOTLOGIN', '此接口需要登入才可调用');
            }
            session_id($token);
            session_start();
        }
        
        if (!isset($_SESSION['level']) || intval($_SESSION['level']) < 1 || intval($_SESSION['uid']) < 1) {
            return lianmi_throw('NOTLOGIN', '您的登入状态已过期，请重新登入');
        }
    }


    /**
     * 附件上传
     * 此接口只用于上传图片，并将 URL 返回，不关系具体的逻辑。可供 头像上传 和 栏目封面上传公用
     * @TODO 稍后需要添加数据统计，以避免图片被滥用
     * @ApiDescription(section="Global", description="图片上传")
     * @ApiLazyRoute(uri="/attach/upload",method="POST|GET")
     * * @ApiParams(name="name", type="string", nullable=false, description="name", check="check_not_empty", cnname="文件名称")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function attachUpload($name)
    {
        /*
        "Array
        (
            [attach] => Array
                (
                    [name] => blob
                    [type] => attach/png
                    [tmp_name] => /private/var/folders/q7/3xwy3ysn2sggtwzq98fpf3l40000gn/T/php1iUQLb
                    [error] => 0
                    [size] => 38084
                )

        )
        "
        */

        if (!isset($_FILES['attach'])) {
            return lianmi_throw('INPUT', '找不到上传的文件，[attach] 不存在');
        }

        if (intval($_FILES['attach']['error']) !== 0) {
            return lianmi_throw('INPUT', '文件上传失败');
        }
        
        $name = basename($name);
        if (mb_strlen($name, 'UTF-8') > 15) {
            $name = mb_substr($name, -15, null, 'UTF-8');
        }


        // 生成新文件名
        $path = 'u' . $_SESSION['uid'] . '/' . date("Y.m.d.") . uniqid() . $name ;

        // 保存文件
        if (!storage()->write($path, file_get_contents($_FILES['attach']['tmp_name']), ['visibility' => 'private'])) {
            return lianmi_throw('FILE', '保存文件失败');
        }

        return send_result(['name'=>$name , 'url' => path2url($path, 'attach') ]);
    }

    /**
     * 显示图片
     * @TODO 此接口不需要登入，以后会使用云存储或者x-send来替代
     * @ApiDescription(section="Global", description="显示图片接口")
     * @ApiLazyRoute(uri="/attach/@uid/@inner_path",method="GET|POST")
     * @ApiParams(name="uid", type="string", nullable=false, description="uid", check="check_not_empty", cnname="图片路径")
     * @ApiParams(name="inner_path", type="string", nullable=false, description="inner_path", check="check_not_empty", cnname="图片路径")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function showAttachment($uid, $inner_path)
    {
        $path = $uid .'/' . $inner_path;
        if (!$content = storage()->read($path)) {
            return lianmi_throw('FILE', '文件数据不存在');
        }
        $mime = storage()->getMimetype($path);

        header('Content-Type: ' . $mime);
        echo $content;

        return true;
    }

    /**
     * 图片上传
     * 此接口只用于上传图片，并将 URL 返回，不关系具体的逻辑。可供 头像上传 和 栏目封面上传公用
     * @TODO 稍后需要添加数据统计，以避免图片被滥用
     * @ApiDescription(section="Global", description="图片上传")
     * @ApiLazyRoute(uri="/image/upload",method="POST|GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function imageUpload()
    {
        /*
        "Array
        (
            [image] => Array
                (
                    [name] => blob
                    [type] => image/png
                    [tmp_name] => /private/var/folders/q7/3xwy3ysn2sggtwzq98fpf3l40000gn/T/php1iUQLb
                    [error] => 0
                    [size] => 38084
                )

        )
        "
        */

        if (!isset($_FILES['image'])) {
            return lianmi_throw('INPUT', '找不到上传的文件，[image] 不存在');
        }
        if (intval($_FILES['image']['error']) !== 0) {
            return lianmi_throw('INPUT', '文件上传失败');
        }
        if ($_FILES['image']['type'] != 'image/png') {
            return lianmi_throw('INPUT', '本接口只支持 png 格式的图片');
        }

        // 生成新文件名
        $path = 'u' . $_SESSION['uid'] . '/' . date("Y.m.d.") . uniqid() . '.png';

        // 保存文件
        if (!storage()->write($path, file_get_contents($_FILES['image']['tmp_name']), ['visibility' => 'private'])) {
            return lianmi_throw('FILE', '保存文件失败');
        }

        return send_result(['url' => path2url($path) ]);
    }

    /**
     * 图片上传
     * 此接口只用于上传图片，并将 URL 返回，不关系具体的逻辑。可供 头像上传 和 栏目封面上传公用
     * @TODO 稍后需要添加数据统计，以避免图片被滥用
     * @ApiDescription(section="Global", description="图片上传")
     * @ApiLazyRoute(uri="/image/upload_thumb",method="POST|GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function imageUploadToThumb()
    {
        /*
        "Array
        (
            [image] => Array
                (
                    [name] => blob
                    [type] => image/png
                    [tmp_name] => /private/var/folders/q7/3xwy3ysn2sggtwzq98fpf3l40000gn/T/php1iUQLb
                    [error] => 0
                    [size] => 38084
                )

        )
        "
        */

        if (!isset($_FILES['image'])) {
            return lianmi_throw('INPUT', '找不到上传的文件，[image] 不存在');
        }
        if (intval($_FILES['image']['error']) !== 0) {
            return lianmi_throw('INPUT', '文件上传失败');
        }
        
        $mime = strtolower($_FILES['image']['type']);

        if ($mime != 'image/png' && $mime != 'image/jpg' && $mime != 'image/jpeg') {
            return lianmi_throw('INPUT', '本接口只支持 png 和 jpg 格式的图片'.$mime);
        } // image/jpeg

        // 考虑到 png 透明的问题，加个 type 吧
        if ($mime == 'image/png') {
            $type = 'png';
        } else {
            $type = 'jpg';
        }
        
        // 生成新文件名
        $prefix = 'u' . $_SESSION['uid'] . '/' . date("Y.m.d.") . uniqid() ;
        $path = $prefix. '.' . $type;
        $path_thumb = $prefix . '.thumb.'.$type;

        // 不管是不是原图，都用图像库处理，进行转化和缩图，避免安全风险
        $img = new \Intervention\Image\ImageManager();

        // 将原图转化为 jpg 格式
        $orignal_data = (string)$img->make($_FILES['image']['tmp_name'])->encode($type, 100);

        // 保存原图
        if (!storage()->write($path, $orignal_data, ['visibility' => 'private'])) {
            return lianmi_throw('FILE', '保存文件失败');
        }
        $orignal_url = path2url($path);

        // 开始缩图
        $thumb_data = (string)$img->make($_FILES['image']['tmp_name'])->fit(200, 200, null, 'top')->encode($type, 100);
        if (!storage()->write($path_thumb, $thumb_data, ['visibility' => 'private'])) {
            return lianmi_throw('FILE', '保存文件失败');
        }
        
        $thumb_url = path2url($path_thumb);

        return send_result(compact('orignal_url', 'thumb_url', 'prexfix', 'type'));
    }

    /**
     * 删除内容
     * @ApiDescription(section="Feed", description="删除内容")
     * @ApiLazyRoute(uri="/feed/remove/@id",method="POST|GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function feedRemove($id)
    {
        if (!$feed = db()->getData("SELECT * FROM `feed` WHERE `id` = '" . intval($id) . "' AND `is_delete` != 1 LIMIT 1")->toLine()) {
            return lianmi_throw('INPUT', 'id对应的内容不存在或已被删除');
        }
        
        if ($feed['is_forward'] == 1) {
            if ($feed['forward_uid'] != lianmi_uid()) {
                return lianmi_throw('AUTH', '只有栏主才能删除自己的通过的内容');
            }
        } else {
            if ($feed['uid'] != lianmi_uid()) {
                return lianmi_throw('AUTH', '只有作者才能删除自己的内容');
            }
        }
        
            

        $sql = "UPDATE `feed` SET `is_delete` = '1' WHERE `id` = '" . intval($id) . "' LIMIT 1 ";
        
        db()->runSql($sql);

        $feed['is_delete'] = 1;
        return send_result($feed);
    }

    /**
     * 设置栏目置顶
     * @ApiDescription(section="group", description="设置栏目置顶")
     * @ApiLazyRoute(uri="/group/top",method="POST|GET")
     * @ApiParams(name="feed_id", type="int", nullable=false, description="feed_id", cnname="内容id")
     * @ApiParams(name="group_id", type="int", nullable=false, description="group_id", cnname="栏目id")
     * @ApiParams(name="status", type="int", nullable=false, description="status", cnname="是否为置顶")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function groupTop($group_id, $feed_id, $status = 1)
    {
        // 检查权限
        if (!$group = table('group')->getAllById($group_id)->toLine()) {
            return lianmi_throw('INPUT', '错误的栏目ID，栏目不存在或已被删除');
        }
        
        if ($group['author_uid'] != lianmi_uid()) {
            return lianmi_throw('AUTH', '只有栏主才能修改栏目资料');
        }
        
        if (!$feed = db()->getData("SELECT * FROM `feed` WHERE `id` = '" . intval($feed_id) . "' AND `is_delete` != 1 LIMIT 1")->toLine()) {
            return lianmi_throw('INPUT', 'id对应的内容不存在或已被删除');
        }
        
        if ($feed['group_id'] != $group_id && $feed['forward_group_id'] != $group_id) {
            return lianmi_throw('AUTH', '只能置顶属于该栏目的内容');
        }

        $feed_id = $status == 1  ? $feed_id : 0;
        
        $sql = "UPDATE `group` SET `top_feed_id` =  '" . intval($feed_id) . "' WHERE `id` = '" . intval($group_id) . "' LIMIT 1 ";

        db()->runSql($sql);

        $group['top_feed_id'] = intval($feed_id);

        return send_result($group);
    }

    /**
     * 更新内容
     * @ApiDescription(section="Feed", description="更新内容")
     * @TODO 此接口需要加入相同内容不能短时间重复更新的限制
     * @ApiParams(name="text", type="string", nullable=false, description="text", check="check_not_empty", cnname="内容内容")
     * @ApiParams(name="images", type="string", nullable=false, description="images", cnname="内容附图")
     * @ApiParams(name="attach", type="string", nullable=false, description="attach", cnname="内容附件")
     * @ApiParams(name="is_paid", type="int", nullable=false, description="is_paid", cnname="是否为付费内容")
     * @ApiLazyRoute(uri="/feed/update/@id",method="POST|GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function feedUpdate($id, $text, $images='', $attach = '', $is_paid=0)
    {
        $is_paid = abs(intval($is_paid));
        
        if (!$feed = db()->getData("SELECT * FROM `feed` WHERE `id` = '" . intval($id) . "' AND `is_delete` != 1 LIMIT 1")->toLine()) {
            return lianmi_throw('INPUT', 'id对应的内容不存在或已被删除');
        }
        
        if ($feed['uid'] != lianmi_uid()) {
            return lianmi_throw('AUTH', '只有作者才能修改自己的内容');
        }

        // 检查image数据，确保没有外部链接以避免带来安全问题，这个地方存在链接伪造风风险
        if (strlen($images) > 1) {
            if (!$image_list = @json_decode($images, 1)) {
                $images = '';
            } else {
                foreach ($image_list as $image) {
                    if (!check_image_url($image['orignal_url']) || !check_image_url($image['thumb_url'])) {
                        
                        // $info[] = parse_url( $image['orignal_url'] );
                        // $info[] = parse_url( $image['thumb_url'] );
                        
                        return lianmi_throw('INPUT', '包含未被许可的图片链接，请重传图片后发布');
                    }
                }
            }
        }

        $sql = "UPDATE `feed` SET `text` = '" . s($text) . "' , `images` = '" . s($images) . "' , `files` = '" . s($attach) . "' , `is_paid` = '" . intval($is_paid) . "' WHERE `id` = '" . intval($id) . "' LIMIT 1 ";
        
        db()->runSql($sql);

        $feed['text'] = $text;
        $feed['images'] = $images;
        $feed['is_paid'] = $is_paid;

        return send_result($feed);
    }

    /**
     * 发布内容
     * @ApiDescription(section="Feed", description="发布内容")
     * @TODO 此接口需要加入相同内容不能短时间重复发布的限制
     * @ApiParams(name="text", type="string", nullable=false, description="text", check="check_not_empty", cnname="内容内容")
     * @ApiParams(name="groups", type="string", nullable=false, description="groups", check="check_not_empty", cnname="目标栏目")
     * @ApiParams(name="images", type="string", nullable=false, description="images", cnname="内容附图")
    * @ApiParams(name="attach", type="string", nullable=false, description="attach", cnname="内容附件")
     * @ApiParams(name="is_paid", type="int", nullable=false, description="is_paid", cnname="是否为付费内容")
     * @ApiLazyRoute(uri="/feed/publish",method="POST|GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function feedPublish($text, $groups, $images='', $attach = '', $is_paid=0)
    {
        $is_paid = abs(intval($is_paid));
        
        // 首先需要做一个增强验证
        $group_ids = json_decode($groups, 1);
        
        // 如果栏目数据不对
        if (!is_array($group_ids) || intval($group_ids[0]) < 1) {
            return lianmi_throw('INPUT', '目标栏目不能为空'.$groups);
        }

        // 检测栏目权限
        $allowed_groups = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND ( `is_author` = 1 || `can_contribute` = 1 ) LIMIT ". c('max_group_per_user'))->toArray();

        $allowed_gids = [];
        $author_gids = [];
        $member_gids = [];
        foreach ($allowed_groups as $item) {
            $allowed_gids[] = $item['group_id'];
            if ($item['is_author'] == 1) {
                $author_gids[] = $item['group_id'];
            } else {
                $member_gids[] = $item['group_id'];
            }
        }

        foreach ($group_ids as $key => $gid) {
            // 如果栏目 id 没有权限，从列表中移除
            if (!in_array($gid, $allowed_gids)) {
                unset($group_ids[$key]);
            }

            //
        }

        // 如果移除无权限的栏目以后，没有可用的栏目，则抛出异常
        if (count($group_ids) < 1) {
            return lianmi_throw('INPUT', '您选择的栏目都没有发布或投稿权限，请重新选择');
        }

        // 检查image数据，确保没有外部链接以避免带来安全问题，这个地方存在链接伪造风风险
        if (strlen($images) > 1) {
            if (!$image_list = @json_decode($images, 1)) {
                $images = '';
            } else {
                foreach ($image_list as $image) {
                    if (!check_image_url($image['orignal_url']) || !check_image_url($image['thumb_url'])) {
                        return lianmi_throw('INPUT', '包含未被许可的图片链接，请重传图片后发布');
                    }
                }
            }
        }

        // 开始入库
        $now = lianmi_now() ;
        // 首先将 feed 发布到表，group id 为 0 ；这将保证这条内容显示在作者主页上，用于浏览和修改；设置为 paid 的内容在主页上只有作者可见。
        $sql = "INSERT INTO `feed` ( `text` , `group_id` , `images` , `files` , `uid` , `is_paid` , `timeline` ) VALUES ( '" . s($text) . "' , '0' , '" . s($images) . "' , '" . s($attach) . "' , '" . intval(lianmi_uid()) . "' , '" . intval($is_paid) . "' , '" . s($now) . "' )  ";

        db()->runSql($sql);

        $feed_id = db()->lastId();

        // 然后开始栏目发布和投稿操作
        // $author_gids = [];
        // $member_gids = [];

        if (is_array($author_gids) && count($author_gids) > 0) {
            foreach ($author_gids as $gid) {
                // 如果栏目ID在发布之列
                if (!in_array($gid, $group_ids)) {
                    continue;
                }

                // 作者是栏主，直接转发到栏目
                $sql = "INSERT INTO `feed` ( `text` , `group_id` , `images` , `files` , `uid` , `is_paid` , `timeline` , `is_forward` , `forward_feed_id` , `forward_uid` , `forward_text` , `forward_is_paid` , `forward_group_id` , `forward_timeline`  ) VALUES ( '" . s($text) . "' , '0' , '" . s($images) . "' ,  '" . s($attach) . "' , '" . intval(lianmi_uid()) . "' , '" . intval($is_paid) . "' , '" . s($now) . "' , '1' , '" . intval($feed_id) . "' , '" . intval(lianmi_uid()) . "' , '' , '" . intval($is_paid) . "' , '" . intval($gid) . "' , '" . s($now) . "' )";

                db()->runSql($sql);

                // 然后更新栏目的内容统计
                $sql = "UPDATE `group` SET `feed_count` = ( SELECT COUNT(*) FROM `feed` WHERE `forward_group_id` = '" . intval($gid) . "' AND `is_delete` != 1 ) WHERE `id`='" . intval($gid) . "' LIMIT 1";
                db()->runSql($sql);

                // 然后更新用户的内容统计
                $sql = "UPDATE `user` SET `feed_count` = ( SELECT COUNT(*) FROM `feed` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `is_delete` != 1 AND `is_forward` != 1 ) WHERE `id`='" . intval(lianmi_uid()) . "' LIMIT 1";
                db()->runSql($sql);
            }
        }
        
        // 投稿
        if (is_array($member_gids) && count($member_gids) > 0) {
            foreach ($member_gids as $gid) {
                // 如果栏目ID在发布之列
                if (!in_array($gid, $group_ids)) {
                    continue;
                }
            
                // 作者是成员，将内容加入到投稿箱
                // @TODO 个人内容管理页面可能需要添加一个投稿按钮，用来处理发布时忘了投稿的内容
                $sql = "INSERT IGNORE INTO `feed_contribute` ( `uid` , `feed_id` , `group_id` , `status`, `timeline` ) VALUES ( '" . intval(lianmi_uid()) . "' , '" . intval($feed_id) . "' , '" . intval($gid) . "' , '0' , '" . s($now) . "' ) ";

                db()->runSql($sql);

                // 更新栏目投稿箱的数据
                $sql = "UPDATE `group` SET `todo_count` = ( SELECT COUNT(*) FROM `feed_contribute` WHERE `group_id` = '" . intval($gid) . "' AND `status` = 0 ) WHERE `id`='" . intval($gid) . "' LIMIT 1";
                db()->runSql($sql);

                // 发送投稿消息
                // 取得栏目栏主
                $group = db()->getData("SELECT * FROM `group` WHERE `id`='" . intval($gid) . "' LIMIT 1")->toLine();

                if ($group['author_uid'] != lianmi_uid()) {
                    system_notice($group['author_uid'], lianmi_uid(), lianmi_username(), lianmi_nickname(), 'contribute to'. $group['name'], '/group/contribute/todo');
                }
            }
        }

        return send_result(compact('feed_id', 'text', 'groups', 'images', 'is_paid'));
    }

    /**
     * 获得我创建的栏目列表
     * @ApiDescription(section="Group", description="获得栏目列表")
     * @ApiLazyRoute(uri="/group/mine",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getMineGroup()
    {
        return send_result($groups = db()->getData("SELECT * FROM `group` WHERE `is_active` = 1 AND `author_uid` = '" . intval(lianmi_uid()) . "' ORDER BY `promo_level` DESC , `member_count` DESC , `id` DESC LIMIT 100 ")->toArray());
    }

    /**
     * 获取栏目内容
     * @ApiDescription(section="Group", description="检查栏目购买数据")
     * @ApiLazyRoute(uri="/group/feed/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiParams(name="filter", type="int", nullable=false, description="filter", cnname="过滤选项")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getGroupFeed($id, $since_id = 0, $filter = 'all')
    {
        // 首先从检查权限
        if (!$info = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `group_id` = '" . intval($id) . "' LIMIT 1")->toLine()) {
            return lianmi_throw('AUTH', '只有成员才能查看栏目内容');
        }

        //
        $filter_sql = '';
        if ($filter == 'paid') {
            $filter_sql = " AND `is_paid` = 1 ";
        }
        if ($filter == 'media') {
            $filter_sql = " AND `images` !='' ";
        }
        
        // VIP订户和栏主可以查看付费内容
        $is_paid = ($info['is_vip'] == 1 || $info['is_author'] == 1)  ? 1 : 0;

        $paid_sql = '';

        if ($info['is_vip'] != 1 && $info['is_author'] != 1) {
            $paid_sql  = " AND `is_paid` != 1 ";
        }
        
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval($since_id) . "' ";

        $sql = "SELECT *, `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE `is_delete` != 1 AND `forward_group_id` = '". intval($id) . "'" . $paid_sql . $since_sql . $filter_sql ." ORDER BY `id` DESC LIMIT " . c('feeds_per_page');

        $data = db()->getData($sql)->toArray();
        $data = extend_field($data, 'user', 'user');
        $data = extend_field($data, 'group', 'group');
        
        
        if (is_array($data) && count($data) > 0) {
            $maxid = $minid = $data[0]['id'];
            foreach ($data as $item) {
                if ($item['id'] > $maxid) {
                    $maxid = $item['id'];
                }
                if ($item['id'] < $minid) {
                    $minid = $item['id'];
                }
            }
        } else {
            $maxid = $minid = null;
        }

        // 获取栏目置顶feed
        $sql = "SELECT * FROM `group` WHERE `id` = '" . intval($id) . "' LIMIT 1";
        $groupinfo = db()->getData($sql)->toLine();
        if ($groupinfo && isset($groupinfo['top_feed_id']) && intval($groupinfo['top_feed_id']) > 0) {
            $topfeed = db()->getData("SELECT *, `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE `is_delete` != 1 AND `id` = '" . intval($groupinfo['top_feed_id']) . "' LIMIT 1")->toLine();

            $topfeed = extend_field_oneline($topfeed, 'user', 'user');
            $topfeed = extend_field_oneline($topfeed, 'group', 'group');
        } else {
            $topfeed = false;
        }
            
        return send_result(['feeds'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid , 'topfeed' => $topfeed ]);
    }

    /**
     * 设置栏目成员黑名单
     * @ApiDescription(section="Group", description="设置栏目成员黑名单")
     * @ApiLazyRoute(uri="/group/blacklist",method="GET|POST")
     * @ApiParams(name="uid", type="int", nullable=false, description="uid", check="check_uint", cnname="用户ID")
     * @ApiParams(name="group_id", type="int", nullable=false, description="group_id", check="check_uint", cnname="栏目ID")
     * @ApiParams(name="status", type="int", nullable=false, description="status", check="check_uint", cnname="黑名单状态")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function setGroupBlackList($uid, $group_id, $status = 1)
    {
        if (!$info = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `group_id` = '" . intval($group_id) . "' LIMIT 1")->toLine() || $info['is_author'] != 1) {
            return lianmi_throw('AUTH', '只有管理员才能设置栏目黑名单');
        }

        if ($status == 1) {
            if ($uid == lianmi_uid()) {
                return lianmi_throw('INPUT', '不能将自己加入黑名单');
            }
            
            $sql = "INSERT IGNORE INTO `group_blacklist` ( `group_id` , `uid` , `timeline` ) VALUES ( '" . intval($group_id) . "' , '" . intval($uid) . "' , '" . s(lianmi_now()) . "' ) ";
        } else {
            $sql = "DELETE FROM `group_blacklist` WHERE `group_id` =  '" . intval($group_id) . "' AND `uid` = '" . intval($uid) . "' LIMIT 1";
        }
            

        db()->runSql($sql);

        if ($status == 1) {
            $this->quitGroup($group_id, $uid);
        }

        return send_result(['status'=>$status]);
    }

    /**
     * 设置栏目投稿黑名单
     * @ApiDescription(section="Group", description="设置栏目投稿黑名单")
     * @ApiLazyRoute(uri="/group/contribute_blacklist",method="GET|POST")
     * @ApiParams(name="uid", type="int", nullable=false, description="uid", check="check_uint", cnname="用户ID")
     * @ApiParams(name="group_id", type="int", nullable=false, description="group_id", check="check_uint", cnname="栏目ID")
     * @ApiParams(name="status", type="int", nullable=false, description="status", check="check_uint", cnname="黑名单状态")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function setGroupContributeBlackList($uid, $group_id, $status = 1)
    {
        if ($status != 1) {
            $status = 0;
        }
        
        if (!$info = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `group_id` = '" . intval($group_id) . "' LIMIT 1")->toLine() || $info['is_author'] != 1) {
            return lianmi_throw('AUTH', '只有管理员才能设置栏目黑名单');
        }

        if ($uid == lianmi_uid()) {
            return lianmi_throw('INPUT', '不能将自己加入黑名单');
        }

        $sql = "UPDATE `group_member` SET `can_contribute` = '" . intval($status) . "' WHERE `uid` = '" . intval($uid) . "' AND `group_id` =  '" . intval($group_id) . "' LIMIT 1";
        db()->runSql($sql);

        return send_result(['status'=>$status,'sql'=>$sql]);
    }

    /**
     * 设置栏目评论黑名单
     * @ApiDescription(section="Group", description="设置栏目评论黑名单")
     * @ApiLazyRoute(uri="/group/comment_blacklist",method="GET|POST")
     * @ApiParams(name="uid", type="int", nullable=false, description="uid", check="check_uint", cnname="用户ID")
     * @ApiParams(name="group_id", type="int", nullable=false, description="group_id", check="check_uint", cnname="栏目ID")
     * @ApiParams(name="status", type="int", nullable=false, description="status", check="check_uint", cnname="黑名单状态")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function setGroupCommentBlackList($uid, $group_id, $status = 1)
    {
        if ($status != 1) {
            $status = 0;
        }
        
        if (!$info = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `group_id` = '" . intval($group_id) . "' LIMIT 1")->toLine() || $info['is_author'] != 1) {
            return lianmi_throw('AUTH', '只有管理员才能设置栏目黑名单');
        }

        if ($uid == lianmi_uid()) {
            return lianmi_throw('INPUT', '不能将自己加入黑名单');
        }

        $sql = "UPDATE `group_member` SET `can_comment` = '" . intval($status) . "' WHERE `uid` = '" . intval($uid) . "' AND `group_id` =  '" . intval($group_id) . "' LIMIT 1";
        db()->runSql($sql);

        return send_result(['status'=>$status]);
    }

    /**
     * 获取栏目成员列表
     * @ApiDescription(section="Group", description="获取栏目成员列表")
     * @ApiLazyRoute(uri="/group/member/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiParams(name="filter", type="int", nullable=false, description="filter", cnname="过滤选项")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getGroupMember($id, $since_id = 0, $filter = 'all')
    {
        // 首先从检查权限
        if (!$info = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `group_id` = '" . intval($id) . "' LIMIT 1")->toLine()) {
            return lianmi_throw('AUTH', '只有成员才能查看栏目成员');
        }

        if ($filter == 'blacklist') {
            $base_sql = "SELECT * , `uid` as `user` FROM `group_blacklist` WHERE `group_id` = '" . intval($id) . "'";
        } else {
            $base_sql = "SELECT * , `uid` as `user` FROM `group_member` WHERE `group_id` = '" . intval($id) . "'";
            
            $filter_sql = '';
            if ($filter == 'contribute') {
                $filter_sql = " AND `can_contribute` = 0 ";
            }
            if ($filter == 'comment') {
                $filter_sql = " AND `can_comment` = 0 ";
            }

            $base_sql = $base_sql . $filter_sql;
        }
        
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval($since_id) . "' ";

        $sql = $base_sql . $since_sql ." ORDER BY `id` DESC LIMIT " . c('users_per_page');

        $group_black_list_uids = table('group_blacklist')->getUidByGroup_id($id)->toColumn('uid');
        
        $data = db()->getData($sql)->toArray();
        $data = extend_field($data, 'user', 'user');
        
        if (is_array($data) && count($data) > 0) {
            $maxid = $minid = $data[0]['id'];
            foreach ($data as $key => $item) {
                // 将这两个字段复制到user中，方便使用
                if (isset($data[$key]['can_contribute'])) {
                    $data[$key]['user']['can_contribute'] = $data[$key]['can_contribute'];
                }
                
                if (isset($data[$key]['can_comment'])) {
                    $data[$key]['user']['can_comment'] = $data[$key]['can_comment'];
                }
                
                if ($group_black_list_uids && in_array($item['uid'], $group_black_list_uids)) {
                    $data[$key]['user']['inblacklist'] = 1;
                } else {
                    $data[$key]['user']['inblacklist'] = 0;
                }
                
                if ($item['id'] > $maxid) {
                    $maxid = $item['id'];
                }
                if ($item['id'] < $minid) {
                    $minid = $item['id'];
                }
            }
        } else {
            $maxid = $minid = null;
        }
            
        return send_result(['members'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid ]);
    }

    /**
     * 创建栏目
     * @TODO 当前版本不处理销售信息
     * @TODO 需要限制创建的栏目上限
     * @TODO 需要在栏目创建成功后，自动将作者加入到栏目里边
     * @ApiDescription(section="Group", description="创建栏目")
     * @ApiParams(name="name", type="string", nullable=false, description="name", check="check_not_empty", cnname="栏目名称")
     * @ApiParams(name="author_address", type="string", nullable=false, description="author_address", check="check_not_empty", cnname="栏目提现地址")
     * @ApiParams(name="price_wei", type="string", nullable=false, description="price_wei", check="check_uint", cnname="栏目年费价格")
     * @ApiParams(name="cover", type="string", nullable=false, description="cover", check="check_not_empty", cnname="栏目封面地址")
     * @ApiParams(name="seller_uid", type="string", nullable=false, description="seller_uid", cnname="销售商编号")
     * @ApiLazyRoute(uri="/group/create",method="POST|GET")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function groupCreate($name, $author_address, $price_wei, $cover, $seller_uid = 0)
    {
        // $price_wei 是 18位以上的 bigint，所以使用 bigintval
        if (!check_image_url($cover)) {
            return lianmi_throw('INPUT', '包含未被许可的图片链接，请重传图片后发布');
        }
        
        if (mb_strlen($name, 'UTF8') < 3) {
            return lianmi_throw("INPUT", "栏目名字最短3个字");
        }
        
        // 检查一下栏目名字是否唯一
        if (db()->getData("SELECT COUNT(*) FROM `group` WHERE `name` = '" . s($name) . "' ")->toVar() > 0) {
            return lianmi_throw("INPUT", "栏目名字已被占用，重新起一个吧");
        }
        

        $timeline = lianmi_now();
        $author_uid = lianmi_uid();

        //$sql = "INSERT INTO `group` ( `name` , `author_uid` , `author_address` , `price_wei` , `cover` , `seller_uid` , `timeline`  )  VALUES ( '" . s( t($name) ) . "' , '" . intval( $author_uid ) . "' , '" . s(t($author_address)) . "' , '" . bigintval( $price_wei ) . "' , '" . s( t($cover) ) . "' , '" . intval( $seller_uid ) . "' , '" .  s( $timeline ).  "' ) ";
        
        // 将 is_active 和 is_paid 设置为 1，跳过支付创建栏目的过程
        $sql = "INSERT INTO `group` ( `name` , `author_uid` , `author_address` , `price_wei` , `cover` , `seller_uid` , `timeline` , `is_active` , `is_paid` )  VALUES ( '" . s(t($name)) . "' , '" . intval($author_uid) . "' , '" . s(t($author_address)) . "' , '" . bigintval($price_wei) . "' , '" . s(t($cover)) . "' , '" . intval($seller_uid) . "' , '" .  s($timeline).  "' , 1 , 1 ) ";

        db()->runSql($sql);
        $group_id = db()->lastId();
        
        // 将当前用户信息更新到 group_member 表
        $sql = "REPLACE INTO `group_member` ( `group_id` , `uid` , `is_author` , `is_vip` , `timeline` ) VALUES ( '" . intval($group_id) . "' , '" . intval($author_uid) . "' , '1' , '1' , '" . s($timeline) . "' )";
        db()->runSql($sql);

        // 开始更新用户表的group数量
        $sql = "UPDATE `user` SET `group_count` = (SELECT COUNT(*) FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "'  ) WHERE `id` = '" . intval(lianmi_uid()) . "' LIMIT 1";

        db()->runSql($sql);

        // 更新栏目表中栏目的成员计数
        $sql = "UPDATE `group` SET `member_count` = (SELECT COUNT(*) FROM `group_member` WHERE `group_id` = '" . intval($group_id) . "'  ) WHERE `id` = '" . intval($group_id) . "' LIMIT 1";

        db()->runSql($sql);

        
        // 这里需要返回栏目的基本信息
        $group = [];
        $group['id'] = $group_id;
        
        foreach (['name','author_uid','author_address','price_wei','cover','seller_uid','timeline'] as $value) {
            $group[$value] = $$value;
        }

        return send_result($group);
    }

    /**
     * 加入栏目
     * @ApiDescription(section="Group", description="检查栏目购买数据")
     * @ApiLazyRoute(uri="/group/join/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function joinGroup($id)
    {
        if (intval(table('group')->getIs_activeById($id)->toVar()) != 1) {
            return lianmi_throw('AUTH', '该栏目尚未启用或已被暂停');
        }
        
        // 这里要检查当前用户是否进入了黑名单
        if (db()->getData("SELECT * FROM `group_blacklist` WHERE `group_id` = '" . intval($id) .  "' AND `uid` = '" . intval(lianmi_uid()) . "' LIMIT 1")->toLine()) {
            return lianmi_throw('AUTH', '你没有权限订阅该栏目');
        }
        
        // 开始向数据表添加数据
        $sql = "INSERT IGNORE INTO `group_member` ( `group_id`, `uid` , `timeline` ) VALUES ( '" .  intval($id) . "' , '" . intval(lianmi_uid()) . "' , '" . lianmi_now() . "' ) ";

        db()->runSql($sql);

        // 开始更新用户表的group数量
        $sql = "UPDATE `user` SET `group_count` = (SELECT COUNT(*) FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "'  ) WHERE `id` = '" . intval(lianmi_uid()) . "' LIMIT 1";

        db()->runSql($sql);

        // 更新栏目表中栏目的成员计数
        $sql = "UPDATE `group` SET `member_count` = (SELECT COUNT(*) FROM `group_member` WHERE `group_id` = '" . intval($id) . "'  ) WHERE `id` = '" . intval($id) . "' LIMIT 1";

        db()->runSql($sql);

        return send_result("done");
    }

    /**
     * 退出栏目
     * @ApiDescription(section="Group", description="检查栏目购买数据")
     * @ApiLazyRoute(uri="/group/quit/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function quitGroup($id, $uid = null)
    {
        if ($uid === null) {
            $uid = lianmi_uid();
        }
        if (!$info = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval($uid) . "' AND `group_id` = '" . intval($id) . "' LIMIT 1")->toLine()) {
            return lianmi_throw('INPUT', '尚未订阅该栏目');
        }

        if (intval($info['is_author']) > 0) {
            return lianmi_throw('INPUT', '栏主不能退订栏目');
        }
        
        // 开始删除数据
        $sql = "DELETE FROM `group_member` WHERE `group_id` = '" . intval($id) . "' AND `uid` = '" . intval($uid) . "' LIMIT 1";

        db()->runSql($sql);

        // 开始更新用户表的group数量
        $sql = "UPDATE `user` SET `group_count` = (SELECT COUNT(*) FROM `group_member` WHERE `uid` = '" . intval($uid) . "'  ) WHERE `id` = '" . intval($uid) . "' LIMIT 1";

        db()->runSql($sql);

        // 更新栏目表中栏目的成员计数
        $sql = "UPDATE `group` SET `member_count` = (SELECT COUNT(*) FROM `group_member` WHERE `group_id` = '" . intval($id) . "'  ) WHERE `id` = '" . intval($id) . "' LIMIT 1";

        db()->runSql($sql);

        return send_result("done");
    }

    /**
     * 查询当前用户的基本信息
     * @ApiLazyRoute(uri="/user/self",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function userSelfInfo()
    {
        if (!$user = db()->getData("SELECT * FROM `user` WHERE `id` = '" . lianmi_uid() . "' LIMIT 1")->toLine()) {
            return lianmi_throw("INPUT", "Email地址不存在或者密码错误");
        }

        // 清空密码 hash 以免在之后的流程中出错
        unset($user['password']) ;

        $user['uid'] = $user['id'];
        $user['token'] = session_id(); // 将 session id 作为 token 传回前端

        // 取得当前用户参加的group
        // 添加当前用户的group分组信息
        $user = array_merge($user, get_group_info($user['id'])) ;

        // if( strlen( $user['avatar'] )  < 1 ) $user['avatar'] = c('default_avatar_url');

        return send_result($user);
    }

    /**
     * 检查当前用户VIP购买情况，并更新以此更新数据表中的数据
     * @ApiDescription(section="Group", description="检查栏目购买数据")
     * @ApiLazyRoute(uri="/group/vip/check/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function checkVipIsPaid($id)
    {
        $abi = json_decode(file_get_contents(AROOT . DS . 'contract' . DS . 'build' . DS . 'lianmi.abi'));
        $web3 = new \Web3\Providers\HttpProvider(new \Web3\RequestManagers\HttpRequestManager(c('web3_network'), 60));
        $contract = new \Web3\Contract($web3, $abi);
        
        $contract->at(c('contract_address'))->call('memberOf', $id, lianmi_uid(), function ($error, $data) use ($id, $contract) {
            if ($error != null) {
                return lianmi_throw('CONTRACT', '合约调用失败：' . $error->getMessage());
            } else {
                // bigint
                $data = reset($data);
                $timestamp = intval($data->toString());
                $datetime = date("Y-m-d H:i:s", $timestamp);

                if (time() > $timestamp) {
                    // 此 VIP 订户已经过期
                    // 或者完全没有购买过， timestamp 为零
                    $is_vip = 0;
                    $result = "NOTVIP";
                } else {
                    $is_vip = 1;
                    $result = "VIP";
                }

                // 更新数据库
                if (!$info = db()->getData("SELECT * FROM `group_member` WHERE `group_id` = '" . intval($id) . "' AND `uid` = '" . intval(lianmi_uid()) . "' LIMIT 1")->toLine()) {
                    return lianmi_throw('INPUT', '你需要先订阅栏目才能购买VIP');
                }

                // 当过期时间，或者vip状态有变更时，更新数据库
                if ($info['is_vip'] != $is_vip || $info['vip_expire'] != $datetime) {
                    $sql = "UPDATE `group_member` SET `is_vip` = '" . intval($is_vip) . "' , `vip_expire` = '" . s($datetime) . "' WHERE `group_id` = '" . intval($id) . "' AND `uid` = '" . intval(lianmi_uid()) . "' AND `id` = '" . intval($info['id']) . "' LIMIT 1";

                    db()->runSql($sql);
                }

                

                return send_result([ 'is_vip' => $is_vip , 'vip_expire' =>  $datetime ]);
            }
        });
    }

    /**
     * 购买小组VIP时，生成订单
     * @ApiDescription(section="Group", description="获取栏目投稿")
     * @ApiLazyRoute(uri="/group/preorder/@group_id",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    
    public function GroupPreorder($group_id)
    {
        if (!$group = db()->getData("SELECT * FROM `group` WHERE  `id` = '" . intval($group_id) . "' LIMIT 1")->toLine()) {
            return lianmi_throw("ARGS", "小组不存在");
        }

        $sql = "INSERT INTO `order` ( `group_id` , `author_address` , `group_price_wei` , `buyer_uid` , `created_at`  ) VALUES ( '" . intval($group_id) . "' , '" . s($group['author_address']) . "' , '" . intval($group['price_wei']) . "' , '" . intval(lianmi_uid()) . "' , '" . s(lianmi_now()) . "' ) ";
        
        db()->runSql($sql);

        $order_id = db()->lastId();
        if ($order_id < 1) {
            return lianmi_throw("DATABASE", "预订单失败");
        }

        $url =  "https://wallet.fo/Pay?params=" .$group['author_address']  . ",FOUSDT,eosio,". intval($group['price_wei'])/100 ."," . u("order=".$order_id);

        $schema = "fowallet://".u($url);

        // FOUSDT@eosio
        
        return send_result(["url"=>$url , "order_id"=> $order_id , "schema" => $schema ]);
    }

    /**
     * 检测小组VIP订单的支付情况
     * @ApiDescription(section="Group", description="获取栏目投稿")
     * @ApiLazyRoute(uri="/group/checkorder",method="GET|POST")
     * * @ApiParams(name="order_id", type="int", nullable=false, description="order_id", check="check_uint", cnname="订单号")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    
    public function GroupCheckorder($order_id)
    {
        // 首先读取订单详情
        if (!$order = db()->getData("SELECT * FROM `order` WHERE  `id` = '" . intval($order_id) . "' LIMIT 1")->toLine()) {
            return lianmi_throw("ARGS", "订单不存在");
        }

        if ($order['vip_active'] == 1) {
            return send_result(["done" => 1]);
        }
        
        if ($order['buyer_uid'] != lianmi_uid()) {
            return lianmi_throw("ARGS", "你只能校验自己的订单");
        }

        // 开始检测
        // fo_check_user_tx( $name , $order , $price_wei , $token = 'FOUSDT@eosio' )
        
        if (!fo_check_user_tx($order['author_address'], $order_id, $order['group_price_wei'], 'FOUSDT@eosio')) {
            return lianmi_throw("AUTH", "尚未检测到转账结果，可能存在延迟，请确认到账后三到五分钟再查询");
        }

        // 再次查询一次 order 表，避免并发请求导致的状态延迟

        if (!$order = db()->getData("SELECT * FROM `order` WHERE  `id` = '" . intval($order_id) . "' LIMIT 1")->toLine()) {
            return lianmi_throw("ARGS", "订单不存在");
        }

        if ($order['vip_active'] == 1) {
            return send_result(["done" => 1]);
        }

        // 开始更新用户状态
        // 有两个表要改，一个是`group_member`表 另一个是 order 表
        
        if (!$membership = db()->getData("SELECT * FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `group_id` = '" . intval($order['group_id']) . "' LIMIT 1")->toLine()) {
            return lianmi_throw("AUTH", "先订阅栏目后才能购买VIP订户");
        }

        if (!isset($membership['vip_expire']) || $membership['vip_expire'] == "") {
            $expire = date("Y-m-d H:i:s", strtotime("+1 year"));
        } else {
            $expire = date("Y-m-d H:i:s", strtotime($membership['vip_expire']) + 60*60*24*365);
        }
            
        
        // 首先修改 group_member 表
        $sql = "UPDATE `group_member` SET `is_vip` = 1 , `vip_expire` = '" . $expire . "' WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `group_id` = '" . intval($order['group_id']) . "' LIMIT 1";

        db()->runSql($sql);

        // 然后修改 order 表，更改可用状态，避免重复
        $sql = "UPDATE `order` SET `vip_active` = 1 , `vip_start` = '" . s(lianmi_now()) . "' WHERE `id` = '" . intval($order_id) . "' LIMIT 1";
        db()->runSql($sql);

        return send_result(["done" => 1]);
    }


    /**
     * 更新内容投稿状态
     * @ApiDescription(section="Group", description="获取栏目投稿")
     * @ApiLazyRoute(uri="/group/contribute/update",method="GET|POST")
     * @ApiParams(name="group_id", type="int", nullable=false, description="group_id",check="check_uint",, cnname="栏目ID")
     * @ApiParams(name="feed_id", type="int", nullable=false, description="feed_id",check="check_uint",, cnname="内容原始ID")
     * @ApiParams(name="status", type="int", nullable=false, description="status",check="check_uint",, cnname="投稿状态")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function updateContribute($group_id, $feed_id, $status)
    {
        // 检查权限
        if (lianmi_uid() != db()->getData("SELECT `author_uid` FROM `group` WHERE `id` = '" . intval($group_id) . "' LIMIT 1")->toVar()) {
            return lianmi_throw('AUTH', '只有栏主才能审核投稿');
        }
        
        if (!$contribute = db()->getData("SELECT * FROM `feed_contribute` WHERE `group_id` = '" . intval($group_id) . "' AND `feed_id` = '" . intval($feed_id) . "' LIMIT 1")->toLine()) {
            return lianmi_throw('INPUT', '没有对应的投稿');
        }

        if ($contribute['status'] != $status) {
            // 如果新状态为通过
            if ($status == 1) {
                // 转发改feed
                if (!$feed = db()->getData("SELECT * FROM `feed` WHERE `id` = '" . intval($feed_id) . "' LIMIT 1")->toLine()) {
                    return lianmi_throw('INPUT', '投稿对应的Feed不存在');
                }

                // 投稿通过以后又设置为未通过或者拒稿，之前的转发被标记删除的情况
                if ($contribute['forward_feed_id'] != 0) {
                    db()->runSql("UPDATE `feed` SET `is_delete` = 0 WHERE `forward_uid` = '" . intval(lianmi_uid()) . "' AND `id` = '" . intval($contribute['forward_feed_id']) . "' LIMIT 1 ");
                } else {
                    // 转发到栏目
                    $now = lianmi_now();

                    $sql = "INSERT INTO `feed` ( `text` , `group_id` , `images`, `files` , `uid` , `is_paid` , `timeline` , `is_forward` , `forward_feed_id` , `forward_uid` , `forward_text` , `forward_is_paid` , `forward_group_id` , `forward_timeline`  ) VALUES ( '" . s($feed['text']) . "' , '0' , '" . s($feed['images']) . "' , '" . s($feed['files']) . "' , '" . intval($feed['uid']) . "' , '" . intval($feed['is_paid']) . "' , '" . s($feed['timeline']) . "' , '1' , '" . intval($feed['id']) . "' , '" . intval(lianmi_uid()) . "' , '' , '" . intval($feed['is_paid']) . "' , '" . intval($group_id) . "' , '" . s($now) . "' )";

                    db()->runSql($sql);
                    $forward_feed_id = db()->lastId();

                    db()->runSql("UPDATE `feed_contribute` SET `status` = '" . intval($status) . "' , `forward_feed_id` = '" . intval($forward_feed_id) . "' WHERE `id` = '" . intval($contribute['id']) . "' LIMIT 1");
                }
            } else {
                // 如果旧状态为通过，需要删除掉之前的转发内容
                if ($contribute['status'] == 1) {
                    // 如果通过审核时间超过一天将其标记删除
                    if ($the_feed = db()->getData("SELECT * FROM `feed` WHERE `id` = '" . intval($contribute['forward_feed_id']) . "' LIMIT 1")->toLine()) {
                        if (strtotime($the_feed['timeline']) < strtotime("-1day") || $the_feed['comment_count'] > 0) {
                            db()->runSql("UPDATE `feed` SET `is_delete` = 1 WHERE `forward_uid` = '" . intval(lianmi_uid()) . "' AND `id` = '" . intval($contribute['forward_feed_id']) . "' LIMIT 1 ");
                        } else {
                            db()->runSql("DELETE FROM `feed` WHERE `forward_uid` = '" . intval(lianmi_uid()) . "' AND `id` = '" . intval($contribute['forward_feed_id']) . "' LIMIT 1 ");
                        }
                    }
                }

                db()->runSql("UPDATE `feed_contribute` SET `status` = '" . intval($status) . "' WHERE `id` = '" . intval($contribute['id']) . "' LIMIT 1");
            }

            // 更新栏目的投稿计数
            $sql = "UPDATE `group` SET `todo_count` = ( SELECT COUNT(*) FROM `feed_contribute` WHERE `group_id` = '" . intval($group_id) . "' AND `status` = 0 ) WHERE `id`='" . intval($group_id) . "' LIMIT 1";
            db()->runSql($sql);
        }
        
        return send_result('done');
    }

    /**
     * 获取全部栏目投稿
     * @ApiDescription(section="Group", description="获取栏目投稿")
     * @ApiLazyRoute(uri="/group/contribute",method="GET|POST")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiParams(name="filter", type="int", nullable=false, description="filter", cnname="过滤选项")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getContribute($since_id = 0, $filter = 'all')
    {
        $filter_sql = '';
        if ($filter == 'todo') {
            $filter_sql = " AND `status` = '0' ";
        }
        if ($filter == 'allow') {
            $filter_sql = " AND `status` ='1' ";
        }
        if ($filter == 'deny') {
            $filter_sql = " AND `status` ='2' ";
        }
        
        // VIP订户和栏主可以查看付费内容
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval($since_id) . "' ";


        // 首先从投稿表中把投稿取出来，支持 filter 和  since_id 这样可以翻页

        $sql = "SELECT `id` , `feed_id`,`feed_id` as `feed` , `group_id`, `group_id` as `group`, `status` FROM `feed_contribute` WHERE 1 " . $filter_sql . $since_sql . "  AND `group_id` IN ( SELECT `id` FROM `group` WHERE `author_uid` = '" . intval(lianmi_uid()) . "' ) ORDER BY `id` DESC LIMIT " . c('contribute_per_page');

        $data = db()->getData($sql)->toArray();
       
        /*
         * {
            id: "16",
            feed_id: "10",
            group_id: "8"
            },

         */
        
        // 然后按 feed_id 进行合并，不然满屏幕相同的feed，只有投稿到的栏目不同
        if (is_array($data) && count($data) > 0) {
            $maxid = $minid = $data[0]['id'];
            foreach ($data as $item) {
                if ($item['id'] > $maxid) {
                    $maxid = $item['id'];
                }
                if ($item['id'] < $minid) {
                    $minid = $item['id'];
                }
            }

            
            $new_data = [];
            $to_group_ids = [];
            $group_status = [];

            foreach ($data as $key => $item) {
                // 将 group_id 移动到 to_groups ，作为数组
                $data[$key]['to_groups'] = [$item['group_id']];
                $group_status[$item['group_id']][$item['feed_id']] = $item['status'];
                
                // 设置标记
                $feed_id_exists = false;
                
                // 开始循环 $new_data 数组，第一次时 new_data 为空
                foreach ($new_data as $key2 => $preitem) {
                    // 第二次时，开始进入这个循环，preitem 是上次的数据，item是当前的数据
                    // 如果当前的投稿和上次投稿的 feed_id 一样，表示有重复
                    if ($preitem['feed_id'] == $item['feed_id']) {
                        // 设置重复标志位，这个当前投稿不会被合并到 new_data 数组当中
                        $feed_id_exists = true;
                        
                        // 将 当前投稿 的 group 数据合并到已有数据中
                        $new_data[$key2]['to_groups'] = array_merge($new_data[$key2]['to_groups'], $data[$key]['to_groups']);
                        $new_data[$key2]['to_groups'] = array_unique($new_data[$key2]['to_groups']);
                    }
                }
                
                // 如果不存在和已有的投稿重复的 feed_id
                // 将当前投稿加入到 new_data 。 这时候进入下一次循环
                if (!$feed_id_exists) {
                    $new_data[] = $data[$key];
                }

                // 将 to_groups 的 id 合并到一个数据，以便稍后展开
                $to_group_ids = array_merge($to_group_ids, $data[$key]['to_groups']);
                $to_group_ids = array_unique($to_group_ids);
            }

            // return print_r( $group_status );


            // 开始对 group 进行展开
            if (is_array($to_group_ids) && count($to_group_ids) > 0) {
                // 取得 group info
                if ($group_infos = db()->getData("SELECT * FROM `group` WHERE `id` IN ( " . join(",", $to_group_ids) . " )")->toIndexedArray('id')) {
                    foreach ($new_data as $key1 => $item) {
                        if (isset($item['to_groups']) && is_array($item['to_groups']) && count($item['to_groups']) > 0) {
                            foreach ($item['to_groups'] as $key2 => $gid) {
                                if (isset($group_infos[$gid])) {
                                    if (isset($group_status[$gid][$item['feed_id']])) {
                                        $group_infos[$gid]['status'] = $group_status[$gid][$item['feed_id']];
                                    }
                            
                                    $new_data[$key1]['to_groups'][$key2] = $group_infos[$gid];
                            
                                    //$new_data[$key1]['to_groups'][$gid] = $group_infos[$gid];
                            //unset( $new_data[$key1]['to_groups'][$key2] );
                                }
                            }
                        }
                    }
                }
            }
            

            $data = extend_field($new_data, 'feed', 'feed');
             

            // return send_result($to_group_ids );
            // 需要把 feed 里边的 uid 给扩展了
            /**
             * id: "1",
                feed: {
                id: "7",
                uid: "5",
                group_id: "0",
                text: "fdfdfdf",
                is_paid: "1",
                files: null,
                images: null,
                timeline: "2018-07-04 19:09:00",
                is_forward: "0",
                forward_feed_id: "0",
                forward_uid: "0",
                forward_text: null,
                forward_is_paid: "0",
                forward_group_id: "0",
                to_groups: "",
                forward_timeline: null,
                is_delete: "0"
                },
                group: {
                id: "8",
                name: "告别游泳圈",
                author_uid: "5",
                price_wei: "50000000000000000",
                author_address: "0x8C349A47caAd9374D356eB0d48d4c995EF5F1d2f",
                is_paid: "1",
                is_active: "1",
                cover: "http://localhost:8088/image/u5/2018.07.01.5b386437ce834.png",
                seller_uid: "0",
                timeline: "2018-07-01 13:18:48",
                member_count: "0",
                feed_count: "0",
                todo_count: "0"
                }
            */
            $feed_uids = [];
            foreach ($data as $item) {
                if (isset($item['feed']['uid'])) {
                    $feed_uids[] = $item['feed']['uid'];
                }

                $feed_uids = array_unique($feed_uids);
            }

            if (count($feed_uids) > 0) {
                if ($userinfo = db()->getData("SELECT ".c('user_normal_fields')." FROM `user` WHERE `id` IN (" . join(',', $feed_uids) .")")->toIndexedArray('id')) {
                    foreach ($data as $key => $item) {
                        if (isset($item['feed']['uid']) && isset($userinfo[$item['feed']['uid']])) {
                            $data[$key]['feed']['user'] = $userinfo[$item['feed']['uid']];
                        }

                        $data[$key]['feed']['to_groups'] = $item['to_groups'];
                        unset($data[$key]['to_groups']);

                        $data[$key]['feed']['forward_group_id'] = $item['group_id'];
                    }
                }
            }

            // groups 字段需要扩展
        } else {
            $maxid = $minid = null;
        }
            
        return send_result(['feeds'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid  ]);
    }

    /**
     * 删除评论
     * @ApiDescription(section="Feed", description="删除内容评论")
     * @ApiLazyRoute(uri="/comment/remove",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="评论ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function removeFeedComment($id)
    {
        if (!$comment = table('comment')->getAllById($id)->toLine()) {
            return lianmi_throw('INPUT', '评论不存在或已被删除');
        }

        // 开始鉴权
        $can_delete = false;
        // 评论作者可以删除
        if ($comment['uid'] == lianmi_uid()) {
            $can_delete = true;
        } else {
            // 内容作者（含转发者）
            if ($feed = table('feed')->getAllById($comment['feed_id'])->toLine()) {
                $owner_uid = $feed['is_forward'] == 1 ?  $feed['forward_uid'] : $feed['uid'];
                if ($owner_uid ==  lianmi_uid()) {
                    $can_delete = true;
                }
            }
        }

        if (!$can_delete) {
            return lianmi_throw('AUTH', '只有评论作者和内容主人才能删除该评论');
        }

        // 标记删除
        $sql = "UPDATE `comment` SET `is_delete` = '1' WHERE `id` = '" . intval($id) . "' LIMIT 1";
        db()->runSql($sql);

        // 更新 feed 表的评论计数
        $sql = "UPDATE `feed` SET `comment_count` = ( SELECT COUNT(*) FROM `comment` WHERE `feed_id` = '" . intval($id) . "' AND `is_delete` = 0  ) WHERE `id` = '" . intval($id) . "' LIMIT 1";
        db()->runSql($sql);

        $comment['is_delete'] = 1;
        return send_result($comment);
    }


    /**
     * 对内容发起评论
     * @ApiDescription(section="Feed", description="对内容发起评论")
     * @ApiLazyRoute(uri="/feed/comment/@id",method="GET|POST")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="内容ID")
     * @ApiParams(name="text", type="string", nullable=false, description="comment", check="check_not_empty", cnname="评论内容")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function saveFeedComment($id, $text)
    {
        if (!$feed = db()->getData("SELECT *, `uid` as `user` , `forward_uid` as `forward_user` , `group_id` as `group` FROM `feed` WHERE `id` = '". intval($id) . "' AND `is_delete` = 0 LIMIT 1")->toLine()) {
            return lianmi_throw('INPUT', 'ID对应的内容不存在或者你没有权限阅读');
        } else {
            $group_id = $feed['is_forward'] == 1 ? $feed['forward_group_id'] : $feed['group_id'];
            
            $member_ship = db()->getData("SELECT * FROM `group_member` WHERE `group_id` = '" . intval($group_id) . "' AND `uid` = '" . intval(lianmi_uid()) . "' LIMIT 1")->toLine();

            // 原发feed 没有member ship的概念
            if ($feed['is_forward'] != 1) {
                // 自己的内容当然可以评论了
                if ($feed['uid'] == lianmi_uid()) {
                    $member_ship['can_comment'] = 1;
                } else {
                    $member_ship['can_comment'] = db()->getData("SELECT * FROM `user_blacklist` WHERE `uid` = '" . $feed['uid'] . "' AND `block_uid` = '" . intval(lianmi_uid()) . "'")->toLine()? 0:1;
                }
            }

            // 鉴权
            $can_see = true;
            $can_comment = $member_ship['can_comment'] == 1;
            
            if ($feed['is_paid'] == 1) {
                // 鉴权
                $can_see = false;

                // 转发的情况，这是从栏目里边点出来的
                if ($feed['is_forward'] == 1) {
                    if ($member_ship['is_author'] == 1) {
                        $can_see = true;
                    }
                    if ($member_ship['is_vip'] == 1) {
                        $can_see = true;
                    }
                } else {
                    // 原发的情况，这是从作者的页面点出来的
                    // 只有作者本人才能看到个人页面上的付费内容
                    if (lianmi_uid() == $feed['uid']) {
                        $can_see = true;
                        $can_comment = true;
                    }
                }
            }

            if (!$can_see || !$can_comment) {
                return lianmi_throw('AUTH', '没有权限查看或评论此内容，可使用有权限的账号登入后评论');
            }
            
            $sql = "INSERT INTO `comment` ( `feed_id` , `text` , `uid` , `timeline` ) VALUES ( '" . intval($id) . "' , '" . s($text) . "' , '" . intval(lianmi_uid()) . "' , '" . s(lianmi_now()) . "' )";
            db()->runSql($sql);
            $cid = db()->lastId();

            // 更新 feed 表的评论计数
            $sql = "UPDATE `feed` SET `comment_count` = ( SELECT COUNT(*) FROM `comment` WHERE `feed_id` = '" . intval($id) . "' AND `is_delete` = 0  ) WHERE `id` = '" . intval($id) . "' LIMIT 1";
            db()->runSql($sql);

            // 给被评论人发送通知
            $ouid = $feed['is_forward'] == 1 ? $feed['forward_uid'] : $feed['uid'];
            
            // $uid , $username , $nickname , $action , $link
            if ($ouid != lianmi_uid()) {
                system_notice($ouid, lianmi_uid(), lianmi_username(), lianmi_nickname(), 'comments on ['.$feed['id'].']', '/feed/'.$feed['id']);
            }

            // 评论中的 @  提醒
            // 一条评论最多支持
            if ($mention = lianmi_at($text)) {
                $mention = array_slice($mention, 0, c('max_mention_per_comment'));
                $mention_string = array_map(function ($item) {
                    return "'" . $item ."'";
                }, $mention);
                if (is_array($mention_string) && count($mention_string) > 0) {
                    if ($mention_uids = db()->getData("SELECT `id` FROM `user` WHERE `username` IN ( " . join(",", $mention_string) . " )")->toColumn('id')) {
                        foreach ($mention_uids as $muid) {
                            // 不要给自己和内容作者发at通知，因为ta已经会收到通知了
                            if ($muid != lianmi_uid() && $muid != $ouid) {
                                system_notice($muid, lianmi_uid(), lianmi_username(), lianmi_nickname(), '在内容['.$feed['id'].']的评论中@了你', '/feed/'.$feed['id']);
                            }
                        }
                    }
                }
            }


            return send_result([ 'feed_id'=>$id, 'text'=>$text , 'id' => $cid ]);
        }
    }

    

    

    /**
     * 更新用户密码
     * @ApiDescription(section="User", description="更新用户密码")
     * @ApiLazyRoute(uri="/user/update_password",method="POST|GET")
     * @ApiParams(name="old_password", type="string", nullable=false, description="old_password", check="check_not_empty", cnname="原密码")
     * @ApiParams(name="new_password", type="string", nullable=false, description="new_password", check="check_not_empty", cnname="新密码")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function updateUserPassword($old_password, $new_password)
    {
        if (!$user = table('user')->getAllById(lianmi_uid())->toLine()) {
            return lianmi_throw('INPUT', '当前用户不存在，什么鬼');
        }
        
        if (!password_verify($old_password, $user['password'])) {
            return lianmi_throw('INPUT', '错误的原密码');
        }
       
        if (strlen($new_password) < 6) {
            return lianmi_throw('INPUT', '密码长度不能短于6位');
        }
        
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE `user` SET `password` = '" . s($hash) . "' WHERE `id` = '" . intval(lianmi_uid()) . "' LIMIT 1";
        db()->runSql($sql);
    
        return send_result('done');
    }

    /**
     * 更新用户资料
     * @ApiDescription(section="User", description="更新用户资料")
     * @ApiLazyRoute(uri="/user/update_profile",method="POST|GET")
     * @ApiParams(name="nickname", type="string", nullable=false, description="nickname", check="check_not_empty", cnname="用户昵称")
     * @ApiParams(name="address", type="string", nullable=false, description="address",  cnname="钱包地址")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function updateUserInfo($nickname, $address = '')
    {
        $nickname = mb_substr($nickname, 0, 15, 'UTF-8');
        
        if (in_array(strtolower($nickname), c('forbiden_nicknames'))) {
            return lianmi_throw('INPUT', '此用户昵称已被系统保留，请重新选择');
        }

        $sql = "UPDATE `user` SET `nickname` = '" . s($nickname) . "' , `address` = '" . s($address) . "' WHERE `id` = '" . intval(lianmi_uid()) . "' LIMIT 1";
        db()->runSql($sql);
    
        return send_result('done');
    }

    /**
     * 更新用户头像
     * @ApiDescription(section="User", description="更新用户头像")
     * @ApiLazyRoute(uri="/user/update_avatar",method="POST|GET")
     * @ApiParams(name="avatar", type="string", nullable=false, description="avatar", check="check_not_empty", cnname="头像地址")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function updateUserAvatar($avatar)
    {
        if (!check_image_url($avatar)) {
            return lianmi_throw('INPUT', '包含未被许可的图片链接，请重传图片后发布');
        }
        
        $sql = "UPDATE `user` SET `avatar` = '" . s($avatar) . "'  WHERE `id` = '" . intval(lianmi_uid()) . "' LIMIT 1";
        db()->runSql($sql);
    
        return send_result('done');
    }

    /**
     * 更新用户封面
     * @ApiDescription(section="User", description="更新用户封面")
     * @ApiLazyRoute(uri="/user/update_cover",method="POST|GET")
     * @ApiParams(name="cover", type="string", nullable=false, description="cover", check="check_not_empty", cnname="头像地址")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function updateUserCover($cover)
    {
        if (!check_image_url($cover)) {
            return lianmi_throw('INPUT', '包含未被许可的图片链接，请重传图片后发布');
        }
        
        $sql = "UPDATE `user` SET `cover` = '" . s($cover) . "'  WHERE `id` = '" . intval(lianmi_uid()) . "' LIMIT 1";
        db()->runSql($sql);
    
        return send_result('done');
    }

    /**
     * 更新栏目资料
     * @ApiDescription(section="Group", description="更新栏目资料")
     * @ApiLazyRoute(uri="/group/update_settings",method="POST|GET")
     * @ApiParams(name="id", type="int", nullable=false, description="id", check="check_uint", cnname="栏目ID")
     * @ApiParams(name="name", type="string", nullable=false, description="name", check="check_not_empty", cnname="栏目名称")
     * @ApiParams(name="cover", type="string", nullable=false, description="cover", check="check_not_empty", cnname="封面地址")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function updateGroupSettings($id, $name, $cover)
    {
        if (!check_image_url($cover)) {
            return lianmi_throw('INPUT', '包含未被许可的图片链接，请重传图片后发布');
        }

        // 检查权限
        if (!$group = table('group')->getAllById($id)->toLine()) {
            return lianmi_throw('INPUT', '错误的栏目ID，栏目不存在或已被删除');
        }
        
        if ($group['author_uid'] != lianmi_uid()) {
            return lianmi_throw('AUTH', '只有栏主才能修改栏目资料');
        }

        // 检查栏目名称的唯一性
        if ($name != $group['name']) {
            if (mb_strlen($name, 'UTF8') < 3) {
                return lianmi_throw("INPUT", "栏目名字最短3个字");
            }
            
            if (db()->getData("SELECT COUNT(*) FROM `group` WHERE `name` = '" . s($name) . "' ")->toVar() > 0) {
                return lianmi_throw("INPUT", "栏目名字已被占用，重新起一个吧");
            }
        }
        

        $sql = "UPDATE `group` SET `name` = '" . s($name) . "' , `cover` = '" . s($cover) . "' WHERE `id` = '" . intval($id) . "' AND `author_uid` = '" . intval(lianmi_uid()) . "' LIMIT 1";
        
        db()->runSql($sql);

        $group['name'] = $name;
        $group['cover'] = $cover;
        return send_result($group);
    }

    /**
     * 判断某用户是否在黑名单
     * @ApiDescription(section="User", description="判断某用户是否在黑名单")
     * @ApiLazyRoute(uri="/user/inblacklist",method="POST|GET")
     * @ApiParams(name="uid", type="int", nullable=false, description="uid", cnname="游标ID")
     */
    public function checkUserInBlacklist($uid)
    {
        return send_result(intval(table('user_blacklist')->getAllByArray(['uid'=>lianmi_uid(),'block_uid'=>intval($uid)])->toLine()));
    }

    /**
     * 将某用户添加/移出黑名单
     * @ApiDescription(section="User", description="将某用户添加/移出黑名单")
     * @ApiLazyRoute(uri="/user/blacklist_set",method="POST|GET")
     * @ApiParams(name="uid", type="int", nullable=false, description="uid", cnname="游标ID")
     * @ApiParams(name="status", type="int", nullable=false, description="status", cnname="状态")
     */
    public function setUserInBlacklist($uid, $status)
    {
        if ($status == 1) {
            if ($uid == lianmi_uid()) {
                return lianmi_throw('INPUT', '不能将自己加入黑名单');
            }

            $sql = "INSERT IGNORE INTO `user_blacklist` ( `uid` , `block_uid` , `timeline` ) VALUES ( '" . intval(lianmi_uid()) . "' , '" . intval($uid) . "' , '" . s(lianmi_now()) . "' )";
        } else {
            $sql = "DELETE FROM `user_blacklist` WHERE `uid` = '" . s(lianmi_uid()) . "' AND `block_uid` = '" . intval($uid) . "' LIMIT 1";
        }
       
        db()->runSql($sql);

        return send_result($status);
    }

    

    /**
     * 获得当前用户的黑名单
     * @ApiDescription(section="User", description="获得当前用户的黑名单")
     * @ApiLazyRoute(uri="/user/blacklist",method="POST|GET")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     */
    public function getUserBlacklist($since_id = 0)
    {
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval($since_id) . "' ";
        $sql = "SELECT * , `block_uid` as `user` FROM `user_blacklist` WHERE `uid` = '" . intval(lianmi_uid()) . "' " . $since_sql . " ORDER BY `id` DESC LIMIT " . c('blacklist_per_page');

        $data = db()->getData($sql)->toArray();
        $data = extend_field($data, 'user', 'user');
        
        if (is_array($data) && count($data) > 0) {
            $maxid = $minid = $data[0]['id'];
            foreach ($data as $key => $item) {
                if ($item['id'] > $maxid) {
                    $maxid = $item['id'];
                }
                if ($item['id'] < $minid) {
                    $minid = $item['id'];
                }
            }
        } else {
            $maxid = $minid = null;
        }
            
        return send_result(['blacklist'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid ]);
    }


    /**
     * 获取当前用户的置顶信息
     * @ApiDescription(section="Feed", description="获取当前用户的首页信息流")
     * @ApiLazyRoute(uri="/timeline/top",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getUserTimelineTop()
    {
        $sql = "SELECT *,  `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE  `is_top` = 1 AND `is_forward` = 1 AND (( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `is_vip` = 0 ) AND `is_paid` = 0 ) OR ( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND (`is_vip` = 1 "
        . " OR "
        // 或者为作者
        . " `uid` = '" . intval(lianmi_uid()) . "' ) )  )) " . $filter_sql  . $since_sql . " GROUP BY `forward_feed_id` ORDER BY `id` DESC LIMIT ".c('feeds_per_page');

        $data = db()->getData($sql)->toArray();
        $data = extend_field($data, 'user', 'user');
        $data = extend_field($data, 'group', 'group');

        if (isset($data[0])) {
            return send_result($data[0]);
        } else {
            return send_result("");
        }
    }

    /**
     * 获取当前用户的首页信息流
     * @ApiDescription(section="Feed", description="获取当前用户的首页信息流")
     * @ApiLazyRoute(uri="/timeline",method="GET|POST")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiParams(name="filter", type="int", nullable=false, description="filter", cnname="过滤选项")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getUserTimeline($since_id = 0, $filter = 'all')
    {
        $filter_sql = '';
        if ($filter == 'paid') {
            $filter_sql = " AND `is_paid` = 1 ";
        }
        if ($filter == 'media') {
            $filter_sql = " AND `images` !='' ";
        }

        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval($since_id) . "' ";

        // 取得当前用户的加入栏目，然后返回栏目的内容。
        // GroupBy 版本

        /**
         SELECT *,  `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE  `is_top` != 1 AND `is_forward` = 1 AND (

            ( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '3' AND `is_vip` = 0 ) AND `is_paid` = 0 )

            OR

            ( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '3' AND (`is_vip` = 1  OR  `uid` = '3' ) )  )

            )  GROUP BY `forward_feed_id` ORDER BY `id` DESC LIMIT 20
        */
        
        $sql = "SELECT *,  `uid` as `user` , `forward_group_id` as `group` FROM `feed` WHERE  `is_top` != 1 AND `is_forward` = 1 AND (( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `is_vip` = 0 ) AND `is_paid` = 0 ) OR ( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND (`is_vip` = 1 "
        . " OR "
        // 或者为作者
        . " `is_author` = 1 ) )  )) " . $filter_sql  . $since_sql . " GROUP BY `forward_feed_id` ORDER BY `id` DESC LIMIT ".c('feeds_per_page');

        $data = db()->getData($sql)->toArray();
        $data = extend_field($data, 'user', 'user');
        $data = extend_field($data, 'group', 'group');
        
        
        if (is_array($data) && count($data) > 0) {
            $maxid = $minid = $data[0]['id'];
            foreach ($data as $item) {
                if ($item['id'] > $maxid) {
                    $maxid = $item['id'];
                }
                if ($item['id'] < $minid) {
                    $minid = $item['id'];
                }
            }
        } else {
            $maxid = $minid = null;
        }
            
        return send_result(['sql'=> $sql , 'feeds'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid ]);
    }

    /**
     * 获取当前用户的首页信息流最新ID
     * @ApiDescription(section="Feed", description="获取当前用户的首页信息流最新ID")
     * @ApiLazyRoute(uri="/timeline/lastid",method="GET|POST")
     * @ApiParams(name="filter", type="int", nullable=false, description="filter", cnname="过滤选项")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getUserTimelineLastId($filter = 'all')
    {
        $filter_sql = '';
        if ($filter == 'paid') {
            $filter_sql = " AND `is_paid` = 1 ";
        }
        if ($filter == 'media') {
            $filter_sql = " AND `images` !='' ";
        }

        // 取得当前用户的加入栏目，然后返回栏目的内容。
        // GroupBy 版本
        
        // timeline只显示转发内容。
        $sql = "SELECT `id` FROM `feed` WHERE `is_top` != 1 AND `is_forward` = 1 AND "
        
        // 当前用户为小组成员
        ." (( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' "
        // 但是为小组免费成员
        ." AND `is_vip` = 0 ) "." "
        // 内容为免费
        ." AND `is_paid` = 0 ) "
        
        ." OR "
        
        // 当前用户为小组成员
        ." ( `forward_group_id` IN ( SELECT `group_id` FROM `group_member` WHERE `uid` = '" . intval(lianmi_uid()) . "' "
        // 且为付费成员
        . " AND (`is_vip` = 1 "
        . " OR "
        // 或者为作者
        . " `uid` = '" . intval(lianmi_uid()) . "' ) )  )) "
        
        . $filter_sql . " GROUP BY `forward_feed_id` ORDER BY `id` DESC LIMIT 1";

        $last_id = db()->getData($sql)->toVar();
            
        return send_result($last_id);
    }

    /**
     * 获得和某个用户的聊天记录最新id
     * @ApiDescription(section="Message", description="获得和某个用户的聊天记录最新id")
     * @ApiLazyRoute(uri="/message/lastest_id/@to_uid",method="GET|POST")
     * @ApiParams(name="to_uid", type="int", nullable=false, description="to_uid", check="check_uint", cnname="用户ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getMessageLatest($to_uid)
    {
        return send_result(intval(db()->getData("SELECT MAX(`id`) FROM `message` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND ( `to_uid` = '" . intval($to_uid) . "' OR `from_uid` = '" . intval($to_uid) . "'  ) ")->toVar()));
    }

    /**
     * 获得当前用户未读信息数量
     * @ApiDescription(section="Message", description="获得当前用户未读信息数量")
     * @ApiLazyRoute(uri="/message/unread",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getMessageUnreadCount()
    {
        return send_result(intval(db()->getData("SELECT COUNT(*) FROM `message` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND `is_read` = 0 ")->toVar()));
    }

    /**
     * 获得当前用户的最新消息分组列表页面
     * @ApiDescription(section="Message", description="获得当前用户的最新消息分组列表页面")
     * @ApiLazyRoute(uri="/message/grouplist",method="GET|POST")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getMessageGroupList($since_id = 0)
    {
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval($since_id) . "' ";

        $total = db()->getData("SELECT COUNT(*) FROM `message_group` WHERE `uid` = '" . intval(lianmi_uid()) . "'")->toVar();
        
        $sql = "SELECT * , `from_uid` as `from` , `to_uid` as `to` FROM `message_group` WHERE `uid` = '" . intval(lianmi_uid()) . "'  " . $since_sql . " ORDER BY `id` DESC  LIMIT " . c('message_group_per_page');

        $data = db()->getData($sql)->toArray();
        $data = extend_field($data, 'from', 'user');
        $data = extend_field($data, 'to', 'user');
        
        if (is_array($data) && count($data) > 0) {
            $maxid = $minid = $data[0]['id'];
            foreach ($data as $item) {
                if ($item['id'] > $maxid) {
                    $maxid = $item['id'];
                }
                if ($item['id'] < $minid) {
                    $minid = $item['id'];
                }
            }
        } else {
            $maxid = $minid = null;
        }
            
        return send_result(['messages'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid , 'total' => $total ]);
    }

    /**
     * 获得和某个用户的聊天记录
     * @ApiDescription(section="Message", description="获得和某个用户的聊天记录")
     * @ApiLazyRoute(uri="/message/history/@to_uid",method="GET|POST")
     * @ApiParams(name="to_uid", type="int", nullable=false, description="to_uid", check="check_uint", cnname="用户ID")
     * @ApiParams(name="since_id", type="int", nullable=false, description="since_id", cnname="游标ID")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function getMessageHistory($to_uid, $since_id = 0)
    {
        $since_sql = $since_id == 0 ? "" : " AND `id` < '" . intval($since_id) . "' ";

        $total = db()->getData("SELECT COUNT(*) FROM `message` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND ( `to_uid` = '" . intval($to_uid) . "' OR `from_uid` = '" . intval($to_uid) . "'  ) ")->toVar();
        
        $sql = "SELECT * FROM `message` WHERE `uid` = '" . intval(lianmi_uid()) . "' AND ( `to_uid` = '" . intval($to_uid) . "' OR `from_uid` = '" . intval($to_uid) . "'  ) " . $since_sql . " ORDER BY `id` DESC  LIMIT " . c('history_per_page');

        $data = db()->getData($sql)->toArray();
        
        if (is_array($data) && count($data) > 0) {
            $maxid = $minid = $data[0]['id'];
            foreach ($data as $item) {
                if ($item['id'] > $maxid) {
                    $maxid = $item['id'];
                }
                if ($item['id'] < $minid) {
                    $minid = $item['id'];
                }
            }

            // 将 message 和 message_group 对应的内容标记为已读
            if ($since_id == 0) {
                db()->runSql("UPDATE `message` SET `is_read` = 1 WHERE `is_read` = 0 AND `uid` = '" . intval(lianmi_uid()) . "' AND ( `to_uid` = '" . intval($to_uid) . "' OR `from_uid` = '" . intval($to_uid) . "'  )");

                db()->runSql("UPDATE `message_group` SET `is_read` = 1 WHERE `is_read` = 0 AND `uid` = '" . intval(lianmi_uid()) . "' AND ( `to_uid` = '" . intval($to_uid) . "' OR `from_uid` = '" . intval($to_uid) . "'  )");
            }
        } else {
            $maxid = $minid = null;
        }

        
            
        return send_result(['messages'=>$data , 'count'=>count($data) , 'maxid'=>$maxid , 'minid'=>$minid , 'total' => $total ]);
    }

    /**
     * 向某用户发送私信
     * @ApiDescription(section="Message", description="向某用户发送私信")
     * @ApiLazyRoute(uri="/message/send/@to_uid",method="GET|POST")
     * @ApiParams(name="to_uid", type="int", nullable=false, description="to_uid", check="check_uint", cnname="用户ID")
     * @ApiParams(name="text", type="string", nullable=false, description="text", check="check_not_empty", cnname="私信内容")

     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function sendMessage($to_uid, $text)
    {
        if ($to_uid == lianmi_uid()) {
            return lianmi_throw('INPUT', '不要自己给自己发私信啦');
        }
        
        // 检查是否在其黑名单
        if (table('user_blacklist')->getAllByArray(['uid'=>$to_uid,'block_uid'=>lianmi_uid()])->toLine() || table('user_blacklist')->getAllByArray(['uid'=>lianmi_uid() ,'block_uid'=>$to_uid])->toLine()) {
            return lianmi_throw('AUTH', '你或者对方在黑名单中');
        }

        // 插入私信记录
        // 注意需要插两条，因为聊天记录需要支持删除
        $now = lianmi_now();
        
        // 发信人的记录。标记为已读
        $sql = "INSERT INTO `message` ( `uid` , `to_uid` , `from_uid` , `text` , `timeline` , `is_read` ) VALUES ( '" . intval(lianmi_uid()) . "' , '" . intval($to_uid) . "' , '" . intval(lianmi_uid()) . "' , '" . s($text) . "' , '" . s($now) . "' , '1' )";
        db()->runSql($sql);

        // 收信人的记录。标记为未读
        $sql = "INSERT INTO `message` ( `uid` , `to_uid` , `from_uid` , `text` , `timeline` , `is_read` ) VALUES ( '" . intval($to_uid) . "' , '" . intval($to_uid) . "' , '" . intval(lianmi_uid()) . "' , '" . s($text) . "' , '" . s($now) . "' , '0' )";
        db()->runSql($sql);

        $last_mid = db()->lastId();

        // 对话组的冗余记录，用于按分组显示对话
        
        // 删除原有记录
        $sql = "DELETE FROM `message_group` WHERE ( `to_uid` = '" . intval($to_uid) . "' AND `from_uid` = '" . intval(lianmi_uid()) . "' ) OR ( `to_uid` = '" . intval(lianmi_uid()) . "' AND `from_uid` = '" . intval($to_uid) . "' ) LIMIT 2";
        db()->runSql($sql);

        $sql = "REPLACE INTO `message_group` ( `uid` , `to_uid` , `from_uid` , `text` , `timeline` , `is_read` ) VALUES ( '" . intval(lianmi_uid()) . "' , '" . intval($to_uid) . "' , '" . intval(lianmi_uid()) . "' , '" . s($text) . "' , '" . s($now) . "' , '1' )";
        db()->runSql($sql);
        
        $sql = "REPLACE INTO `message_group` ( `uid` , `to_uid` , `from_uid` , `text` , `timeline` , `is_read` ) VALUES ( '" . intval($to_uid) . "' , '" . intval($to_uid) . "' , '" . intval(lianmi_uid()) . "' , '" . s($text) . "' , '" . s($now) . "' , '0' )";
        
        db()->runSql($sql);

        return send_result('done');
    }

    /**
     * 刷新服务器端用户数据
     * @ApiDescription(section="Message", description="刷新服务器端用户数据")
     * @ApiLazyRoute(uri="/user/refresh",method="GET|POST")
     * @ApiReturn(type="object", sample="{'code': 0,'message': 'success'}")
     */
    public function refreshUserData()
    {
        if (!$user = db()->getData("SELECT * FROM `user` WHERE `id` = '" . intval(lianmi_uid()) . "' LIMIT 1")->toLine()) {
            return lianmi_throw("INPUT", "用户不存在");
        }
        
        $user['uid'] = $user['id'];
        $user['token'] = session_id();
        $user = array_merge($user, get_group_info($user['id'])) ;
        return send_result($user);
    }
}
