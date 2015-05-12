<?php
/**
	metaWeblog.editPost
	metaWeblog.getCategories
	metaWeblog.getCategoryPosts
	metaWeblog.getPost
	metaWeblog.getPostByID
	metaWeblog.getRecentPosts
	metaWeblog.newCategory
	metaWeblog.newComment
	metaWeblog.newMediaObject
	metaWeblog.newPost
*/

/*
method metaWeblog.newPost
Makes a new post to a designated blog using the metaWeblog API. Returns postid as a string.

Parameters
		string blogid 
		string username 
		string password 
		struct Post post 
		boolean publish 

Return Value
		string

struct Post

Members
		dateTime dateCreated - Required when posting. 
		string description - Required when posting. 
		string title - Required when posting. 
		array of string categories (optional)  
		struct Enclosure enclosure (optional)  
		string link (optional)  
		string permalink (optional)  
		any postid (optional)  
		struct Source source (optional)  
		string userid (optional)  
		
struct Enclosure

Members
		integer length (optional)  
		string type (optional)  
		string url (optional)  

struct Source

Members
		string name (optional)  
		string url (optional)
*/
function mwb_newPost($api_path, $username, $password, $params)
{
	$blogID = mwb_getBlogID($api_path, $username, $password);
	$c = new xmlrpc_client($api_path);
	$c->request_charset_encoding = 'utf-8';
	
	$x = new xmlrpcmsg("metaWeblog.newPost",
	array(new xmlrpcval($blogID, "string"),
	new xmlrpcval($username, "string"),
	new xmlrpcval($password, "string"),
	php_xmlrpc_encode($params),
	new xmlrpcval(1, "boolean")));
	
	$c->return_type = 'phpvals';
	$r =$c->send($x);
}

function mwb_getBlogID($api_path, $username, $password)
{
	$c = new xmlrpc_client($api_path);
	$c->request_charset_encoding = 'utf-8';
	
	$x = new xmlrpcmsg("blogger.getUsersBlogs",
	array(new xmlrpcval($username, "string"),
	new xmlrpcval($username, "string"),
	new xmlrpcval($password, "string")));
	
	$c->return_type = 'phpvals';
	$r =$c->send($x);
	$val = $r->value();
	$blogID = $val[0]["blogid"]; 
	return $blogID;
}
?>