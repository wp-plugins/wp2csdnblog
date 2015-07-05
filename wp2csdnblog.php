<?php
/*
Plugin Name: WP2CSDNBlog
Plugin URI:  http://xuhehuan.com/2027.html
Version:     1.4
Author:      xhhjin
Author URI:  http://xuhehuan.com
Description: 同步发布 WordPress 日志到 CSDN 博客，也可用在所有支持 Metaweblog API 的博客系统中
*/

/* Copyright 2015  xhhjin  (email : xhhjin@gmail.com)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
add_action('admin_menu', 'menu_add_wp2csdnblog_setting');
//add_action('publish_post', 'publish_article_to_csdnblog');
//add_action('publish_future_post', 'publish_article_to_csdnblog'); 
//add_action('future_to_publish', 'publish_article_to_csdnblog');
//add_action('save_post', 'publish_article_to_csdnblog');
//add_action('xmlrpc_public_post', 'publish_article_to_csdnblog');
add_action('transition_post_status', 'transition_post_to_csdnblog', 10, 3);

function menu_add_wp2csdnblog_setting() 
{
	add_action( 'admin_init', 'register_wp2csdnblog_settings' );
	add_options_page('WP2CSDNBlog Options', 'WP2CSDNBlog', 'administrator', 'WP2CSDNBlog', 'wp2csdnblog_setting_page');
}

function register_wp2csdnblog_settings() 
{
	register_setting( 'WP2CSDNBlog-Settings', 'wp2csdnblog_title' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2csdnblog_user' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2csdnblog_password' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2csdnblog_url' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2sinablog_user' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2sinablog_pass' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2neteaseblog_user' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2neteaseblog_pass' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2csdnblog_issync' );
	register_setting( 'WP2CSDNBlog-Settings', 'wp2csdnblog_isaddlink' );
}

function wp2csdnblog_setting_page() 
{
?>

<div class="wrap">
<h2>WP2CSDNBlog 配置选项</h2>

<form method="post" action="options.php">
	<?php settings_fields( 'WP2CSDNBlog-Settings' ); ?>
	<?php do_settings_sections('wp2csdnblog'); ?>

	<table class="form-table">
		<tr valign="top">
		<th scope="row">博客标题</th>
		<td>
			<input name="wp2csdnblog_title" type="text" id="wp2csdnblog_title" value="<?php form_option('wp2csdnblog_title'); ?>" class="regular-text" />
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">博客登陆用户名</th>
		<td>
			<input name="wp2csdnblog_user" type="text" id="wp2csdnblog_user" value="<?php form_option('wp2csdnblog_user'); ?>" class="regular-text" />
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">博客登录密码</th>
		<td>
			<input name="wp2csdnblog_password" type="password" id="wp2csdnblog_password" value="<?php form_option('wp2csdnblog_password'); ?>" class="regular-text" />
		</td>
		</tr>

		<tr valign="top">
		<th scope="row">博客同步地址(URL)</th>
		<td>
			<input name="wp2csdnblog_url" type="text" id="wp2csdnblog_url" value="<?php form_option('wp2csdnblog_url'); ?>" class="regular-text" />
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">新浪博客（可选）</th>
		<td>
			用户名：<input name="wp2sinablog_user" type="text" id="wp2sinablog_user" value="<?php form_option('wp2sinablog_user'); ?>" />
			密码：<input name="wp2sinablog_pass" type="password" id="wp2sinablog_pass" value="<?php form_option('wp2sinablog_pass'); ?>" />
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">网易博客（可选）</th>
		<td>
			用户名：<input name="wp2neteaseblog_user" type="text" id="wp2neteaseblog_user" value="<?php form_option('wp2neteaseblog_user'); ?>" />
			密码：<input name="wp2neteaseblog_pass" type="password" id="wp2neteaseblog_pass" value="<?php form_option('wp2neteaseblog_pass'); ?>" />
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">同步发送设置</th>
		<td>
			<input name="wp2csdnblog_issync"  value="1" <?php checked(1, get_option('wp2csdnblog_issync')); ?> id="wp2csdnblog_issync0" type="radio">
			<label for="wp2csdnblog_issync0">同步发送</label>
			<input name="wp2csdnblog_issync"  value="0" <?php checked(0, get_option('wp2csdnblog_issync')); ?> id="wp2csdnblog_issync1" type="radio">
			<label for="wp2csdnblog_issync1">不同步</label>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">原文链接设置</th>
		<td>
			<input name="wp2csdnblog_isaddlink" value="1" <?php checked(1, get_option('wp2csdnblog_isaddlink')); ?> id="wp2csdnblog_isaddlink" type="checkbox">
			<label for="wp2csdnblog_isaddlink">添加原文链接</label>
		</td>
		</tr>
	</table>

  <p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p>
</form>

<br/><b>说明：</b>博客同步地址即是 Metaweblog API 的地址
<br/> CSDN博客请填写同步地址为 http://write.blog.csdn.net/xmlrpc/index
</div>

<?php
}

function publish_article_to_csdnblog($post_ID)
{
	$issync = get_option('wp2csdnblog_issync');
	if ($issync == 0) 
	{
		//not sync
		return $post_ID;
	}
	
	$post = get_post($post_ID);
	//new article
	if($post->post_type == "post" && (('publish_post' === current_filter() && $post->post_date == $post->post_modified) || 
		'publish_post' !== current_filter()))
	{
		//get title
		$title = $post->post_title;
		if(strlen($title) == 0)
		{
		   //no title
		   return $post_ID;
		}

		//get content
		$content = $post->post_content;
		if (strlen($content) == 0)
		{
			//no content
			return $post_ID;
		}
		
		//add link or not
		$isaddlink = get_option('wp2csdnblog_isaddlink');
		if ($isaddlink == 1)
		{
			$content .= "<br/><br/>查看原文：<a href=".get_permalink($post_ID).">".get_permalink($post_ID)."</a>";
		}
		//<pre> content </pre>
		//$content = "<pre>" . $content . "</pre>";
		$content= wp_richedit_pre($content);
		$content=htmlspecialchars_decode($content);
		
		$categories = get_the_category($post_ID);
		$category = array();
		foreach ($categories as $i => $value) {
			$category[] = $value->cat_name;
		}
		
		require_once(dirname(__FILE__).'/php/xmlrpc.php');
		require_once(dirname(__FILE__).'/php/metaweblog.php');
		$content = array(
			'title' => $title,
			'description' => $content,
			'categories' => $category,
			'dateCreated' => new xmlrpcval(Date('Ymd\TH:i:s'), "dateTime.iso8601"),
		);
		
		$username = get_option('wp2csdnblog_user');
		$password = get_option('wp2csdnblog_password');
		$blogapiurl = get_option('wp2csdnblog_url');
		
		//检查账户是否已设置
		if(strlen($username) > 3 && strlen($password) > 3) 
		{
			mwb_newPost($blogapiurl, $username, $password, $content);
		}
		
		//新浪博客
		$username = get_option('wp2sinablog_user');
		$password = get_option('wp2sinablog_pass');
		$blogapiurl = "http://upload.move.blog.sina.com.cn/blog_rebuild/blog/xmlrpc.php";
		if(strlen($username) > 3 && strlen($password) > 3) 
		{
			mwb_newPost($blogapiurl, $username, $password, $content);
		}
		
		//网易博客
		$username = get_option('wp2neteaseblog_user');
		$password = get_option('wp2neteaseblog_pass');
		$blogapiurl = "http://os.blog.163.com/word/";
		if(strlen($username) > 3 && strlen($password) > 3) 
		{	
			mwb_newPost($blogapiurl, $username, $password, $content);
		}
	}
	
	return $post_ID;
}

function transition_post_to_csdnblog( $new_status, $old_status, $post ) {
	if ($old_status != 'publish' && $new_status == 'publish' && get_post_type( $post ) == 'post') {
		publish_article_to_csdnblog($post->ID);
	}
}
?>