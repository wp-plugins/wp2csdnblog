<?php
/*
Plugin Name: WP2CSDNBlog
Plugin URI:  http://xuhehuan.com/2027.html
Version:     1.1
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
add_action('publish_post', 'publish_article_to_csdnblog');
add_action('xmlrpc_public_post', 'publish_article_to_csdnblog');

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
	if($post->post_type == "post" && ($post->post_date == $post->post_modified))
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
		$content = "<pre>" . $content . "</pre>";
		
		$categories = get_the_category($post_ID);
		$category = array();
		foreach ($categories as $i => $value) {
			$category[] = $value->cat_name;
		}
		
		$username = get_option('wp2csdnblog_user');
		$password = get_option('wp2csdnblog_password');
		$blogapiurl = get_option('wp2csdnblog_url');
		
		//检查账户是否已设置
		if(strlen($username) > 3 && strlen($password) > 3) 
		{
			require_once(dirname(__FILE__).'/php/xmlrpc.php');
			require_once(dirname(__FILE__).'/php/metaweblog.php');
			
			$content = array(
				'title' => $title,
				'description' => $content,
				'categories' => $category,
				'dateCreated' => new xmlrpcval(Date('Ymd\TH:i:s'), "dateTime.iso8601"),
			);
			mwb_newPost($blogapiurl, $username, $password, $content);
		}
	}
	
	return $post_ID;
}
?>