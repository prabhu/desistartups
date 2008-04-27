<?php

/* 
Plugin Name: Wordpress Thread Comment
Plugin URI: http://blog.2i2j.com/plugins/wordpress-thread-comment
Version: 1.4.6
Author: 偶爱偶家
Description: wp thread comment allow user can reply comment and same comment On display, can choose ajax or not. 允许用户回复某个特定的评论并集中显示相似评论, 可以自由选择是否使用ajax.
Author URI: http://blog.2i2j.com/
*/

/*
Translation:

Arabic					M. Bashir Al-Noimi(http://mbnoimi.net/)
German					Dieter Geiget(http://www.fazer.tv/)
Farsi_Iran(Persian)		dlrapid(http://downloads.ir/)
English					extvia(http://www.mixill.net/blog)
*/

/*
ChangeLog:

2008-04-20
			1. 重新修改wp ajax edit comments兼容位置, 提高效率.

2008-04-19
			1. 修改词条(submit reply -> nested reply)
			2. 修改sortflag的bug, 通过判断lstcomment来确定DESC和ASC.

2008-04-16
			1. 增加memory_limit选项, 可以自行设定内存限制.
			2. 默认html增加gravatar, 配合wp2.5
			3. 修正unset($this->comment_childs)的bug

2008-04-13
			1. 重新设计ajax的load位置, 完全解决评论框在评论前还是在评论后都可以AJAX评论.
			2. 重新修订所有的词条, 语义更加确切明晰(感谢extvia).

2008-04-12
			1. 重新设定lstcomment和sortflag变量的获取来源(从comment_text()中获取, 以便于适合所有的倒序评论).

2008-04-11
			1. 新增语言包(Persian).
			2. 修复跟wp paged comments同时使用, wp paged comments采取ASC顺序多页时无法使用AJAX, 重新获取lstcomment的值.
			3. 修正评论倒序时, 发表的评论置顶(只针对wp paged comments插件).
			4. 增加version变量, 最后一位是build.

2008-04-10
			1. 修改HTTPS, 使得判断更为准确(IIS以ISAPI运行PHP输出$_SERVER['HTTPS']为off)
			2. 修改$_SERVER['HTTP_HOST']为$_SERVER['SERVER_NAME']
			3. 增加后台回复功能.

2008-04-09
			1. 修改js一处bug(|| => &&)

2008-04-09	1.4.5 发布.

2008-04-07
			1. 新增切换themes时自动检测commentformid函数, 更换主题无忧(未启用)
			2. 新增检测插件冲突函数, 在有冲突的插件启用情况下本插件无法启用(未启用)

2008-04-01
			1. 修改js部分, 提高兼容性.

2008-03-26
			1. 增加needauthoremail参数, 配合wp后台设置的email选择.
			2. 修改js, 加强email检测.
			3. 减少一个focus, 如果是留言成功, 则不foucs到留言框.

2008-03-25
			1. 修正deactive的bug

2008-03-24
			1. 增加$_POST['comment_reply_ID']判断, 防止误读.

2008-03-23
			1. 修正js的错误, $s(commentformid).style.display='block'位置移前, IE下不会出现focus无法成功的错误.
			2. 改写部分js, 加强主题的兼容性.
			3. 修正class thdrpy的一个bug, 通过w3c验证.

2008-03-22
			1. 修正js中的一个错误,在IE下不再出错(r)改成 (r != 0 && r != "0")

2008-03-21
			1. 增加ajax功能(可以自由选择是否使用).

2008-03-16
			1. 修改class thdrpy位置, 防止出现空段落的bug

2008-03-13
			1. deepth 改成 depth
			2. 在[reply]前增加class thdrpy, 允许自定义样式

2008-03-12
			1. 修复mail bug, 原先在选择everyone mail时会出现[postname]空白(又是隐藏很深的Bug)
			2. 增加两个变量, 减少函数调用此处, 提高效率(可以少N次的get_option)[经过实际测试, wp自身实现了cache[不管是否启用cache], 用内存换速度, get_option对效率几乎没有影响)

2008-03-11
			1. 修正原先的delete按钮bug, 使得在最后一层也可以显示.
			2. 修改js, 提高js对主题的适应性.

2008-03-03
			1. 增加delete_comment action, 处理删除父评论时子评论的处理.
			2. 前台增加delete按钮, 直接在前台可以删除评论.

2008-02-29
			1. 修正重大bug, $deep参数控制出错.
			2. 增加$count参数, 用于控制同一层次中的回复数, 可以用于控制同一层次留言的不同样式.
			3. 修改is_admin()函数, 增加$cap变量, 提高性能.

2008-02-28
			1. 增加global $post in addchildcomment().
			2. 增加is_admin(), is_author()函数, 目前注释掉, 需要时可以直接启用.
			3. 重新设定了default options.

2008-02-27
			1. 修正js中的错误(原commentform位于firstchild时取消回复, commentform消失)
			2. 增加新的preprocess_comment filter, 减少数据库查询一次, 该方法由lot提供(http://blog.net2e.com/).
			3. reply this comment 改成 reply to this comment

2008-02-17
			1. 修正在没有评论回复时出现array_key_exists()函数出错的bug

2008-02-14
			1. 将运行php挪动到str_replace之前.
			2. 增加多层回复功能, 可以自行控制回复深度, 这个要感谢denis(http://fairyfish.net/), 感谢帮我找到这么好的方法.

2007-12-25
			1. 增加time参数, 可以在子评论中显示评论时间.
			2. 增加对php的支持, 可以在子评论样式中直接加入php源码.

2007-11-20
			1. 修正无法通过w3c的xhtml 1.0.

2007-11-14
			1. 增加focus(), 使得在点击回复评论后焦点自动位于comment输入框.

2007-11-13	
			1. 修正在多次点"回复"后, 取消回复无法返回原位置的bug.
			2. 彻底重写wp-thread-comment.js, 大幅度减少代码保证文件小巧, 如果你还觉得不够, 可以删除alert的函数(去除警告即可).
			3. 后台增加email通知内容的设置, 可以设置subject和message.
			4. 解决与wp ajax edit comments 的不兼容问题, 目前可以完全和wp ajax edit comments 协同工作.

2007-11-2
			1. 增加一行曾经遗漏的代码, 现在可以在更新配置后显示更新成功的信息了(一直没有发现这个错误, ft).

2007-11-1
			1. 只在有权评论的时候显示reply按钮;
			2. 修正原先当评论者姓名和日期出现在内容下方的样式中, 被回复者的姓名和日期显示为回复者的姓名和日期的bug;
			3. 增加回复email通知功能, 后台设置三种情况.

2007-10-30
			1. 增加info变量, 用来记录plugin的信息, 保证plugin无论在任何目录都能有效运行.
*/


if (!(isset($_POST['SaveCommentId']) && isset($_POST['SaveContent']) && isset($_POST['_wpnonce']))) : //兼容wp ajax edit comments
if(!class_exists('wp_thread_comment')):
class wp_thread_comment{
	var $version = '1.4.6.008';
	var $info = '';
	var $status = '';
	var $message = '';
	var $options = array();
	var $options_keys = array('memory_limit', 'comment_html', 'comment_css', 'comment_formid', 'comment_deep', 'comment_ajax',  'clean_option', 'mail_notify', 'mail_subject', 'mail_message', 'reply_admin');
	var $db_options = 'wpthreadcomment';
	var $comment_childs = array();
	var $cap = array('reply' => FALSE, 'delete' => FALSE, 'admin' => array(), 'sortflag' => '', 'lstcomment' => 0, 'programflag' => 0);
	//programflag 共 0/1/2 三个值, 0表示未设置过lstcomment, 1表示comment_text比comment_form前运行, 2表示comment_text比comment_form后运行

	function wp_thread_comment(){
		$this->initinfo();
		$this->initoption();
		if(!is_admin() && (int)$this->options['memory_limit'] != 0){
			ini_set('memory_limit', $this->options['memory_limit'].'M');
		}
		$this->inithook();
	}

	function defaultoption($key=''){
		if(empty($key))
			return false;

		if($key === 'memory_limit'){
			return 0;
		}elseif($key === 'comment_html'){
			return __('<div class="comment-childs<?php echo $deep%2 ? \' chalt\' : \'\'; ?>" id="comment-[ID]"><?php if(function_exists("get_avatar")) echo get_avatar( $comment, 32 ); ?><p>[author] <em>reply on [date] [time]</em>:</p>[content]</div>','wp-thread-comment');
		}elseif($key === 'comment_css'){
			return '.editComment, .editableComment, .textComment{ display: inline;}.comment-childs{border: 1px solid #999;margin: 5px 2px 2px 4px;padding: 4px 2px 2px 4px;background-color: white;} .chalt {background-color: #E2E2E2;} #newcomment{border:1px dashed #777;width:90%;} #newcommentsubmit{color:red;} .adminreplycomment{border:1px dashed #777;width:99%;margin:4px;padding:4px;}';
		}elseif($key === 'comment_formid'){
			return (string)$this->getformidfromcommentfile();
		}elseif($key === 'comment_deep'){
			return 3;
		}elseif($key === 'comment_ajax'){
			return 'no';
		}elseif($key === 'clean_option'){
			return 'no';
		}elseif($key === 'mail_notify'){
			return 'none';
		}elseif($key === 'mail_subject'){
			return __('Someone replied to your comment over at [blogname]','wp-thread-comment');
		}elseif($key === 'mail_message'){
			return __("<p><strong>[blogname]</strong> inform respectfully: your comment on post <strong>[postname]</strong> now have new reply</p>\n<p>here is your comment:<br />\n[pc_content]</p>\n<p>here is new reply comment:<br />\n[cc_content]</p>\n<p>You can see detail for the comment on this post here:<br />\n<a href=\"[commentlink]\">[commentlink]</a></p>\n<p><strong>Thanks for your attention of <a href=\"[blogurl]\">[blogname]</a></strong></p>\n<p><strong>This email is sended by blog system automatically, don't reply this mail please.</strong></p>",'wp-thread-comment');
		}elseif($key === 'reply_admin'){
			return 'no';
		}else{
			return false;
		}
	}

	function resetToDefaultOptions(){
		$this->options = array();

		foreach($this->options_keys as $key){
			$this->options[$key] = $this->defaultoption($key);
		}
		update_option($this->db_options, $this->options);
	}

	function getformidfromcommentfile(){

		$commentfile = ABSPATH.'wp-content/themes/'.get_option('stylesheet').'/comments.php';
	
		if(!is_file($commentfile))
			$commentfile = ABSPATH.'wp-content/themes/default/comments.php';
		if(is_file($commentfile)){
			$context = file_get_contents($commentfile);

			if(!empty($context)){
				if(preg_match('/<(form.*?wp-comments-post\\.php.*?)>/ius', $context, $match)){
					$context = $match[1];

					if(preg_match('/id\s*?=\s*?"(.*?)"/ius', $context, $match)){
						unset($commentfile, $context);
						return $match[1];
					}
				}
			}
		}
		unset($commentfile, $context, $match);
		return '';
	}

	function initinfo(){
		$info['file'] = basename(__FILE__);
		$path = basename(str_replace('\\','/',dirname(__FILE__)));
		$info['siteurl'] = get_option('siteurl');
		$info['url'] = $info['siteurl'] . '/wp-content/plugins';
		$info['dir'] = 'wp-content/plugins';
		$info['path'] = '';
		if ( $path != 'plugins' ) {
			$info['url'] .= '/' . $path;
			$info['dir'] .= '/' . $path;
			$info['path'].= $path;
		}
		$this->info = array(
			'siteurl' 			=> $info['siteurl'],
			'url'		=> $info['url'],
			'dir'		=> $info['dir'],
			'path'		=> $info['path'],
			'file'		=> $info['file']
		);
		unset($info);
	}
	function initoption(){
		$optionsFromTable = get_option($this->db_options);
		if (empty($optionsFromTable)){
			$this->resetToDefaultOptions();
		}

		$flag = FALSE;
		foreach($this->options_keys as $key) {
			if(isset($optionsFromTable[$key]) && !empty($optionsFromTable[$key])){
				$this->options[$key] = $optionsFromTable[$key];
			}else{
				$this->options[$key] = $this->defaultoption($key);
				$flag = TRUE;
			}
		}
		if($flag === TRUE){
			update_option($this->db_options, $this->options);
		}
		unset($optionsFromTable,$flag);
	}

	function inithook(){
		add_action('init', array(&$this, 'init_textdomain'));
		add_action('delete_comment',array(&$this,'deletecomment'),1000);

		add_filter('preprocess_comment', array(&$this,'addreplyid'),1000);
		add_action('comment_post', array(&$this,'email'),1000);

		if($this->options['comment_ajax'] === 'yes' && trim($_POST['wptcajax']) === 'wptcajax')
			add_filter('comment_post_redirect', array(&$this,'commentpostredirect'),1000,2);

		if($this->options['reply_admin'] === 'yes' && trim($_POST['wptcadminajax']) === 'wptcadminajax')
			add_filter('comment_post_redirect', array(&$this,'admincommentpostredirect'),1000);

		if(!is_admin()){
			//add_action('comment_post', array(&$this,'btc_add_reply_id'));
			add_action('comment_form', array(&$this,'addreplyidformfield'),1000);
			add_action('wp_head', array(&$this,'wphead'),1000);
			add_filter('comment_text', array(&$this,'addchildcomment'),1000);
			add_filter('comments_array', array(&$this,'changecomment'),998);
			if($this->options['comment_ajax'] === 'yes'){
				add_filter('comments_array', array(&$this,'lstcomment'),9999);
			}
		}

		if(is_admin()){
			if($this->options['reply_admin'] === 'yes'){
				add_action('admin_head', array(&$this,'wphead'),1000);
				add_action('admin_footer', array(&$this,'admincommentreply'),1000);
				add_filter('comment_text', array(&$this,'admincommenttext'),1000);
			}
			add_action('admin_menu', array(&$this,'wpadmin'));
			if((string) $this->options['clean_option'] === 'yes')
				add_action('deactivate_'.$this->info['path'].'/'.$this->info['file'], array(&$this,'deactivate'));
			//add_action('activate_'.$this->info['path'].'/'.$this->info['file'], array(&$this,'activate'));
			//add_action('switch_theme', array(&$this, 'switchtheme'));
		}
	}

	function admincommentpostredirect($location){
		die();exit();
	}

	function admincommenttext($text){
		global $comment;
		
		if( (string) $comment->comment_type === 'pingback' || (string) $comment->comment_type === 'trackback')
			return $text;

		$text .= '<p>[ <a href="javascript:void(0)" onclick="popuptext(event,' . $comment->comment_post_ID . ','.$comment->comment_ID.');">'. __('Reply','wp-thread-comment') . '</a> ]</p>';

		return $text;
	}

	function admincommentreply(){
?>
<form id="inlinereply" method="post" action="<?php bloginfo('url'); ?>/wp-comments-post.php" style="display:none" onsubmit="return wptcadminajaxsend()">
	<textarea id="comment" name="comment" style="margin-top:1em;" cols="70" rows="5"></textarea><br />
	<input name="submitreply" id="submitreply" value="<?php _e('Nested Reply', 'wp-thread-comment'); ?>" type="submit" />
	<input name="submitcomment" id="submitcomment" value="<?php _e('Post Comment', 'wp-thread-comment'); ?>" type="submit" onclick="javascript:$('comment_reply_ID').value=0" />
	<input name="cancel" id="cancel" value="<?php _e('Cancel', 'wp-thread-comment'); ?>" type="button" onclick="javascript:$('inlinereply').style.display='none'" />
	<input type="hidden" id="comment_post_ID" name="comment_post_ID" value="0" />
	<input type="hidden" id="comment_reply_ID" name="comment_reply_ID" value="0" />
</form>
<script type="text/javascript" src="<?php echo $this->info['url']."/wp-thread-comment.js.php?jsver=adminajax"; ?>"></script>
<?php
	}

/*	function activate(){
		$plugin = trim($_GET['plugin']);
		$needle = array('Ajax Comments-Reply' => 'comment-reply.php',
						'Paged Threaded Comments' => 'paged-threaded-comments.php',
						'TP-Guestbook' => 'tp-guestbook.php',
						'Brian\'s Threaded Comments' => 'briansthreadedcomments.php');
		$current = get_option('active_plugins');
		foreach($current as &$v){
			$v = trim(strtolower(basename($v)));
		}
		unset($v);

		foreach($needle as $k => $v){
			if(in_array($v, $current)){
				$current = get_option('active_plugins');
				array_splice($current, array_search( $_GET['plugin'], $current), 1 );
				update_option('active_plugins', $current);
				unset($needle, $current, $v);
				wp_die(sprintf(__('wp thread comment can not activate because plugins "%s" is active, these two plugins are conflict.', 'wp-thread-comment'), $k));
			}
		}
		unset($needle, $k, $v, $current);
	}

	function switchtheme(){
		$this->options['comment_formid'] = $this->defaultoption('comment_formid');
		update_option($this->db_options, $this->options);
	}*/

	function init_textdomain(){
		load_plugin_textdomain('wp-thread-comment',$this->info['dir']);
	}

	function deactivate(){
		if($this->options['clean_option'] === 'yes')
			delete_option($this->db_options);
		return true;
	}

	function deletecomment($id){
		global $wpdb;
		
		$comment = get_comment($id);

		$comments_id = array();
		$comments_id = $wpdb->get_col("SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment->comment_post_ID' AND comment_parent = '$id'");
		
		$comment_parent = $comment->comment_parent;
		unset($comment);

		if(count($comments_id) > 0){
			foreach($comments_id as $comment_id){
				/*if(wp_delete_comment($comment_id) === false){
					exit();
					break;
				}*/
				if ($wpdb->query("UPDATE $wpdb->comments SET comment_parent = '$comment_parent'	WHERE comment_ID = '$comment_id'") === false){
					exit();
					break;
				}
			}
		}
		unset($comments_id);
		return $id;
	}

	function commentpostredirect($location,$c){
		global $comment, $user_ID;

		if($this->options['comment_ajax'] === 'yes' && trim($_POST['wptcajax']) === 'wptcajax'){
			$comment = $c;
		
			$this->cap['delete'] = current_user_can('edit_post', $_POST['comment_post_ID']);
			$this->cap['reply'] = (!get_option('comment_registration')) || (get_option('comment_registration') && $user_ID);

			if($comment->comment_parent >0){
				$deep = empty($_POST['comment_reply_dp']) ? 999999 : (int)$_POST['comment_reply_dp'];
				echo $this->commenttext($deep);
				unset($deep);
			}else{
				ob_start();
				$comments = array($comment,$comment);
				include(TEMPLATEPATH.'/comments.php');
				$commentout = ob_get_clean();

				if(preg_match('#(<\w+\s+[^>]*id\s*=[^>]*comment-'.$c->comment_ID.'[^>]*>.*?)<\w+\s+[^>]*id\s*=[^>]*comment-'.$c->comment_ID.'[^>]*>#ius', $commentout, $matches)){
					echo $matches[1];
				}else{
					echo $commentout;
				}
				unset($commentout, $comments, $matches);

			}
			die();exit();
		}else{
			return $location;
		}
	}


	function addreplyid($commentdata){
		if(isset($_POST['comment_reply_ID']))
			$commentdata['comment_parent'] = mysql_escape_string($_POST['comment_reply_ID']);
		return $commentdata;
	}
	
	function email($id){

		if($this->options['mail_notify'] == 'admin' || $this->options['mail_notify'] == 'everyone'){

			$this->mailer($id,mysql_escape_string($_POST['comment_reply_ID']),mysql_escape_string($_POST['comment_post_ID']));
		}

		return $id;

	}
	
/*	function btc_add_reply_id($id){
		global $wpdb;

		$reply_id = mysql_escape_string($_POST['comment_reply_ID']);
		$wpdb->query("UPDATE {$wpdb->comments} SET comment_parent='$reply_id' WHERE comment_ID='$id'");

		if($this->options['mail_notify'] == 'admin' || $this->options['mail_notify'] == 'everyone'){

			$comment_post_id = mysql_escape_string($_POST['comment_post_ID']);
			$this->mail_notify($id,$reply_id,$comment_post_id);
		}
		unset($comment_post_id,$reply_id);
		return $id;
	}*/

	function mailer($id,$parent_id,$comment_post_id){
		global $wpdb, $user_ID, $userdata;

		$post = get_post($comment_post_id);

		if($this->options['mail_notify'] == 'admin'){
			$cap = $wpdb->prefix . 'capabilities';
			if((strtolower((string) array_shift(array_keys((array)($userdata->$cap)))) !== 'administrator') && ((int)$post->post_author !== (int)$user_ID)){
				unset($cap);
				return false;
			}
		}
		
		$parent_email = $wpdb->get_var("SELECT comment_author_email FROM {$wpdb->comments} WHERE comment_ID='$parent_id'");

		if(empty($parent_email) || !is_email($parent_email)){
			unset($parent_email);
			return;
		}

		$pc = get_comment($parent_id);
		$cc = get_comment($id);

		$mail_subject = $this->options['mail_subject'];
		$mail_subject = str_replace('[blogname]', get_option('blogname'), $mail_subject);
		$mail_subject = str_replace('[postname]', $post->post_title, $mail_subject);

		$mail_message = $this->options['mail_message'];
		$mail_message = str_replace('[pc_date]', $pc->comment_date, $mail_message);
		$mail_message = str_replace('[pc_content]', $pc->comment_content, $mail_message);
		
		$mail_message = str_replace('[cc_author]', $cc->comment_author, $mail_message);
		$mail_message = str_replace('[cc_date]', $cc->comment_date, $mail_message);
		$mail_message = str_replace('[cc_url]', $cc->comment_url, $mail_message);
		$mail_message = str_replace('[cc_content]', $cc->comment_content, $mail_message);

		$mail_message = str_replace('[blogname]', get_option('blogname'), $mail_message);
		$mail_message = str_replace('[blogurl]', get_option('siteurl'), $mail_message);
		$mail_message = str_replace('[postname]', $post->post_title, $mail_message);
		$mail_message = str_replace('[commentlink]', get_permalink($comment_post_id)."#comment-{$parent_id}", $mail_message);

		$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
		$from = "From: \"".get_option('blogname')."\" <$wp_email>";

		$mail_headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";

		unset($wp_email, $from, $post, $pc, $cc, $cap);

		$mail_message = apply_filters('comment_notification_text', $mail_message, $id);
		$mail_subject = apply_filters('comment_notification_subject', $mail_subject, $id);
		$mail_headers = apply_filters('comment_notification_headers', $mail_headers, $id);

		@wp_mail($parent_email, $mail_subject, $mail_message, $mail_headers);
		unset($mail_subject,$parent_email,$mail_message, $mail_headers);
		
		return;
	}

	function addreplyidformfield(){
		echo "<p><input type='hidden' id='comment_reply_ID' name='comment_reply_ID' value='0' />";
		echo "<input type='hidden' id='comment_reply_dp' name='comment_reply_dp' value='0' /></p>";
		echo '<div id="reroot" style="display:none;"><small><a href="javascript:void(0)" onclick="movecfm(null,0,0);" style="color:red;">' . __('Click to cancel reply','wp-thread-comment') . '</a></small></div>';
		echo "<script type=\"text/javascript\">\nvar commentformid = \"". $this->options['comment_formid'] . "\";\nvar COOKIEHASH = \"". COOKIEHASH . "\";\n</script>\n";

		echo "<script type=\"text/javascript\" src=\"". $this->info['url'] . "/wp-thread-comment.js.php?jsver=common\"></script>\n";

		if($this->options['comment_ajax'] === 'yes' && $this->cap['programflag'] === 0){
			$this->cap['programflag'] = 2;
		}elseif($this->options['comment_ajax'] === 'yes' && $this->cap['programflag'] === 1){
			$lstcomment = "<script type=\"text/javascript\">\nvar lstcommentid=".$this->cap['lstcomment'].";\n";
			if(get_option('require_name_email'))
				$lstcomment .= "var needauthoremail=true;\n";
			else
				$lstcomment .= "var needauthoremail=false;\n";

			$lstcomment .= "var sortflag=\"".$this->cap['sortflag']."\"\n";
			$lstcomment .= "</script>\n";
			echo $lstcomment;
			unset($lstcomment);
			echo "<script type=\"text/javascript\" src=\"".$this->info['url']."/wp-thread-comment.js.php?jsver=ajax\"></script>\n";
			unset($this->comment_childs);
		}else{}
	}

	function changecomment($comments){
		global $post, $user_ID;

		foreach($comments as $i => $value){
			if($value->comment_parent > 0){
				$this->comment_childs[$value->comment_parent][] = $value;
				unset($comments[$i]);
			}
		}
		$comments = array_values($comments);

		$this->cap['delete'] = current_user_can('edit_post', $post->ID);
		$this->cap['reply'] = (!get_option('comment_registration')) || (get_option('comment_registration') && $user_ID);

		return $comments;
	}

	function lstcomment($comments){
		if($this->options['comment_ajax'] === 'yes'){
			$this->cap['lstcomment'] = (int)$comments[count($comments)-1]->comment_ID;
			if($this->cap['lstcomment'] < (int)$comments[0]->comment_ID){
				$this->cap['lstcomment'] = (int)$comments[0]->comment_ID;
			}
		}

		return $comments;
	}

	function addchildcomment($text){
		global $post, $comment;
		static $deep = 0;

		if( (string) $comment->comment_type === 'pingback' || (string) $comment->comment_type === 'trackback')
			return $text;

		if(trim($_POST['wptcajax']) === 'wptcajax' && !empty($_POST['comment_reply_dp']))
			$deep = (int) $_POST['comment_reply_dp'];

		if((int)$comment->comment_parent === 0){
			$deep = 0;

			if($this->options['comment_ajax'] === 'yes'){
				if(empty($this->cap['sortflag'])){
					if((int)$comment->comment_ID === (int)$this->cap['lstcomment']){
						$this->cap['sortflag'] = 'DESC';
					}else{
						$this->cap['sortflag'] = 'ASC';
					}
				}

				if($this->cap['programflag'] === 0){
					$this->cap['programflag'] = 1;
				}

				if((int)$comment->comment_ID === (int)$this->cap['lstcomment'] && $this->cap['programflag'] === 2){
					$lstcomment = "<script type=\"text/javascript\">\nvar lstcommentid=".$this->cap['lstcomment'].";\n";
					if(get_option('require_name_email'))
						$lstcomment .= "var needauthoremail=true;\n";
					else
						$lstcomment .= "var needauthoremail=false;\n";

					$lstcomment .= "var sortflag=\"".$this->cap['sortflag']."\"\n";
					$lstcomment .= "</script>\n";
					echo $lstcomment;
					unset($lstcomment);
					echo "<script type=\"text/javascript\" src=\"".$this->info['url']."/wp-thread-comment.js.php?jsver=ajax\"></script>\n";
				}
			}

			/*if((int)$comment->comment_ID > (int)$this->cap['lstcomment']){
				$this->cap['lstcomment'] = (int)$comment->comment_ID;
			}elseif((int)$comment->comment_ID < (int)$this->cap['lstcomment']){
				$this->cap['sortflag'] = 'DESC';
			}else{}*/
		}		

		$orgcomment = $comment;

		if( $this->cap['reply'] && $deep < (int) $this->options['comment_deep'] ){
			$text .= '<p class="thdrpy">[<a href="javascript:void(0)" onclick="movecfm(event,' . $comment->comment_ID . ','.($deep+1).');">'. __('Reply','wp-thread-comment') . '</a>]';

			if($this->cap['delete']){
				$text .= ' | [<a href="'. wp_nonce_url($this->info['siteurl'].'/wp-admin/comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) .'" onclick="return confirm(\''. __('Do you really want to delete the comment?','wp-thread-comment') . '\');">'. __('Delete','wp-thread-comment') . '</a>]</p>';
			}else{
				$text .= '</p>';
			}
		}else{
			if($this->cap['delete']){
				$text .= '<p class="thdrpy">[<a href="'. wp_nonce_url($this->info['siteurl'].'/wp-admin/comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) .'" onclick="return confirm(\''. __('Do you really want to delete the comment?','wp-thread-comment') . '\');">'. __('Delete','wp-thread-comment') . '</a>]</p>';
			}
		}

		if(isset($this->comment_childs) && array_key_exists($comment->comment_ID, $this->comment_childs)){

			$deep++;

			$id_temp = $comment->comment_ID;

			$count = 0;
			foreach($this->comment_childs[$id_temp] as $comment){

				$count++;
				$text .= $this->commenttext($deep,$count);
			}
			unset($this->comment_childs[$id_temp]);

			$deep--;
		}
		$comment = $orgcomment;
		unset($orgcomment);
		if($deep < 0) $deep = 0;
		return $text;
	}

	function commenttext($deep=0,$count=0){
		global $post, $comment;

				$p = $this->options['comment_html'];

				ob_start();
				ob_clean();
				//$p = str_replace('<'.'?php','<'.'?',$p);
				eval('?'.'>'.$p);
				$p = ob_get_contents();
				ob_end_clean();
								
				$p = str_replace('[ID]', get_comment_ID(), $p);
				$p = str_replace('[author]', get_comment_author_link(), $p);
				$p = str_replace('[date]', get_comment_date(), $p);
				$p = str_replace('[time]', get_comment_time(), $p);
				
				if(strpos($p,'[content]')){
					ob_start();
					ob_clean();
					comment_text();
					$text = ob_get_contents();
					ob_end_clean();
					$p = str_replace('[content]', $text, $p);
					unset($text);
				}
				return $p;
	}

	function wphead(){
		$threadhead = "<!-- wp thread comment {$this->version} -->\n";

		$threadhead .= '<style type="text/css"><!--' . $this->options['comment_css'] . '--></style>';

		echo $threadhead;

		unset($threadhead);
	}

	function displayMessage() {
		if ( $this->message != '') {
			$message = $this->message;
			$status = $this->status;
			$this->message = $this->status = '';
		}

		if ( $message ) {
?>
			<div id="message" class="<?php echo ($status != '') ? $status :'updated '; ?> fade">
				<p><strong><?php echo $message; ?></strong></p>
			</div>
<?php	
		}
		unset($message,$status);
	}

	function wpadmin(){
		add_options_page(__('WP Thread Comment Option','wp-thread-comment'), __('WP Thread Comment','wp-thread-comment'), 5, __FILE__, array(&$this,'options_page'));
	}

	function options_page(){

		if (isset($_POST['updateoptions'])){
			foreach((array) $this->options as $key => $oldvalue) {
				$this->options[$key] = (isset($_POST[$key]) && !empty($_POST[$key])) ? stripslashes($_POST[$key]) : $this->defaultoption($key);
			}
			update_option($this->db_options, $this->options);
			$this->message = __('Options saved','wp-thread-comment');
			$this->status = 'updated';
		}elseif( isset($_POST['reset_options']) ){
			$this->resetToDefaultOptions();
			$this->message = __('All confriguration has been reset!','wp-thread-comment');
		}else{}
		$this->displayMessage();
?>
<div class="wrap">
	<style type="text/css">
		div.clearing{border-top:1px solid #2580B2 !important;clear:both;}
	</style>

	<h2>WP Thread Comment</h2>
	<form method="post" action="">
		<fieldset name="wp_basic_options"  class="options">
		<p>
			<strong><?php _e('Memory Limit Set','wp-thread-comment'); ?></strong>
			<br /><br />
			<label><?php _e('Memory limit(number only):','wp-thread-comment'); ?></label>
			<input type="text" name="memory_limit" id="memory_limit" value="<?php echo $this->options['memory_limit']; ?>" size="2" /><label>M</label>
			<br />
			<small><?php _e('if you cannot run plugin of memory limit low, Try to increase the memory limit, so can run the plugins. if leave null or 0, that will use memory limit by system default','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Configuration AJAX','wp-thread-comment'); ?></strong>
			<br /><br />
			<label><?php _e('Enabled Ajax Support:','wp-thread-comment'); ?></label>
			<input type="checkbox" name="comment_ajax" id="comment_ajax" value="yes" <?php if ($this->options['comment_ajax'] === 'yes') { ?> checked="checked"<?php } ?>/>
			<br />
			<small><?php _e('Check this box if you wish to use ajax when reply','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Edit Comment HTML','wp-thread-comment'); ?></strong>
			<br /><br />
			<textarea style="font-size: 90%" name="comment_html" id="comment_html" cols="100%" rows="3" ><?php echo htmlspecialchars(stripslashes($this->options['comment_html'])); ?></textarea>
			<br />
			<small><?php _e('HTML and PHP both can be used. As a easier way, you may use the following tags: <strong>[ID]</strong> for comment ID, <strong>[author]</strong> for comment author, <strong>[date]</strong> for comment date, <strong>[time]</strong> for comment time and <strong>[content]</strong> for comment content.','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Edit Comment CSS','wp-thread-comment'); ?></strong>
			<br /><br />
			<textarea style="font-size: 90%" name="comment_css" id="comment_css" cols="100%" rows="3" ><?php echo htmlspecialchars(stripslashes($this->options['comment_css'])); ?></textarea>
			<br />
			<small><?php _e('Use CSS only, HTML and PHP cannot be used.','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Edit Comment Form ID','wp-thread-comment'); ?></strong>
			<br /><br />
			<label>Comment Form ID:</label>
			<input type="text" name="comment_formid" id="comment_formid" value="<?php echo $this->options['comment_formid']; ?>" size="15" />
			<br />
			<small><?php _e('Change the commentform ID in comments.php according your theme. In most cases you were not need to do anything as it can be detected automatically.','wp-thread-comment'); ?></small>
			<br />
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Edit maximum nest level','wp-thread-comment'); ?></strong>
			<br /><br />
			<label><?php _e('Thread Nest level(number only):','wp-thread-comment'); ?></label>
			<input type="text" name="comment_deep" id="comment_deep" value="<?php echo $this->options['comment_deep']; ?>" size="2" />
			<br />
			<small><?php _e('Comments cannot be replied more than this level.','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Email notify the parent commenter when his comment was replied','wp-thread-comment'); ?></strong>
			<br /><br />
			<input type="radio" name="mail_notify" id="do_none" value="none" <?php if ($this->options['mail_notify'] !== 'admin' || $this->options['mail_notify'] !== 'everyone') { ?> checked="checked"<?php } ?>/><label><?php _e('Disabled','wp-thread-comment'); ?></label>
			<br />
			<input type="radio" name="mail_notify" id="do_admin" value="admin" <?php if ($this->options['mail_notify'] === 'admin') { ?> checked="checked"<?php } ?>/><label><?php _e('Replied by the author of the post or administrator ONLY','wp-thread-comment'); ?></label>
			<br />
			<input type="radio" name="mail_notify" id="do_everyone" value="everyone" <?php if ($this->options['mail_notify'] === 'everyone') { ?> checked="checked"<?php } ?>/><label><?php _e('Anyone replies','wp-thread-comment'); ?></label>
			<br />
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Edit the subject of notification email','wp-thread-comment'); ?></strong>
			<br /><br />
			<input type="text" name="mail_subject" id="mail_subject" value="<?php echo $this->options['mail_subject']; ?>" size="80" />
			<br />
			<small><?php _e('Use TEXT only. As a easier way, you may use the following tags: <strong>[blogname]</strong> for blog name and <strong>[postname]</strong> for comment post name','wp-thread-comment'); ?></small>
			<br />
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Edit Notification Message','wp-thread-comment'); ?></strong>
			<br /><br />
			<textarea style="font-size: 90%" name="mail_message" id="mail_message" cols="100%" rows="10" ><?php echo htmlspecialchars(stripslashes($this->options['mail_message'])); ?></textarea>
			<br />
			<small><?php _e('Use HTML only. As a easier way, you may use the following tags: <strong>[pc_date]</strong> for parent comment date, <strong>[pc_content]</strong> for parent comment content, <strong>[cc_author]</strong> for child comment author, <strong>[cc_date]</strong> for child comment date, <strong>[cc_url]</strong> for child comment author url, <strong>[cc_content]</strong> for child comment content, <strong>[commentlink]</strong> for parent comment link, <strong>[blogname]</strong> for blog name, <strong>[blogurl]</strong> for blog url and <strong>[postname]</strong> for post name.','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Reply in Wordpress Admin Panel','wp-thread-comment'); ?></strong>
			<br /><br />
			<label><?php _e('Reply in Wordpress Admin Panel:','wp-thread-comment'); ?></label>
			<input type="checkbox" name="reply_admin" id="reply_admin" value="yes" <?php if ($this->options['reply_admin'] === 'yes') { ?> checked="checked"<?php } ?>/>
			<br />
			<small><?php _e('check box if you want to Reply in Wordpress Admin Panel','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p>
			<strong><?php _e('Configuration action of deactivate','wp-thread-comment'); ?></strong>
			<br /><br />
			<label><?php _e('Delete options after deactivate:','wp-thread-comment'); ?></label>
			<input type="checkbox" name="clean_option" id="clean_option" value="yes" <?php if ($this->options['clean_option'] === 'yes') { ?> checked="checked"<?php } ?>/>
			<br />
			<small><?php _e('check box if you want to delete all of options of wp thread comment after deactivate this plugin','wp-thread-comment'); ?></small>
		</p>
		<div class="clearing"></div>
		<p class="submit">
			<input type="submit" name="updateoptions" value="<?php _e('Update Options','wp-thread-comment'); ?> &raquo;" />
			<input type="submit" name="reset_options" onclick="return confirm('<?php _e('Do you really want to reset your current configuration?','wp-thread-comment'); ?>');" value="<?php _e('Reset Options','wp-thread-comment'); ?>" />
		</p>
		</fieldset>
	</form>
</div>
<?php
	}

//此部分为is_admin()和is_author()函数, 需要者可以自行启用
/*	function get_usermeta($meta_key, $meta_value = ''){
		global $wpdb;

		if (empty($meta_key))
			return false;

		$meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

		if (!empty($meta_value)){
			$metas = $wpdb->get_col("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$meta_key' AND meta_value LIKE '$meta_value'");
		}else{
			$metas = $wpdb->get_col("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '$meta_key'");
		}

		if (empty($metas)){
			return array();
		}

		return $metas;
	}

	function is_admin($user_id = 0){
		global $wpdb;

		$user_id = (int) $user_id;

		if($user_id == 0)
			return false;

//		$cap = get_usermeta($user_id,$wpdb->prefix . 'capabilities');
//		if( is_array($cap) && ((int)$cap['administrator'] === 1) ){
//			return true;
//		}elseif(strpos($cap,'administrator')){
//			return true;
//		}else{}
//		return false;

		if(count($this->cap['admin']) < 1)
			$this->cap['admin'] = $this->get_usermeta($wpdb->prefix . 'capabilities', '%administrator%');

		if(in_array($user_id,$this->cap['admin']))
			return true;

		return false;
		
	}

	
	function is_author($user_id = 0){
		global $post;

		$user_id = (int) $user_id;
		
		if($user_id == 0)
			return false;

		if((int)$post->post_author === $user_id)
			return true;

		return false;
	}*/
//到这里就结束了.

}
endif;

$new_wp_thread_comment = new wp_thread_comment();

endif;
?>
