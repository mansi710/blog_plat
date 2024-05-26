<?php
return [
	'RESPONSE_SUCCESS' 				=> 101,
	'RESPONSE_ERROR' 				=> 100,
	'RESPONSE_AUTH_ERROR'   		=> 401,

	'APP_NAME'						=> 'Blogging System',
	'APP_KEY'               		=> 'Blog',
	'RECORD_LIMIT_API' 				=> 10,
	'POST_RECORD_LIMIT_API' 	    => 6,

	//User
	'TAG_REGISTER' 					=> 'register',
	'TAG_LOGIN' 					=> 'login',
	'TAG_LOGOUT' 	        		=> 'logout',

	//Category
	'TAG_CREATE_CATEGORY' 	    		=> 'create_category',
	'TAG_CATEGORY_LIST' 			=> 'category_list',

	// Tag
	'TAG_CREATE_POST_TAG'		=> 'create_post_tag',
	'TAG_POST_TAG_LIST'			=> 'post_tag_list',

	//Social Feed
	'TAG_CREATE_POST'			=> 'post_create',
	'TAG_POST_LIST' 				=> 'post_list',
	'TAG_UPDATE_POST'				=> 'post_update',
	'TAG_DELETE_POST'				=> 'post_delete',
	
	'TAG_POST_COMMENT' 				=> 'post_comment',
];
