<?php
/*
Plugin Name: Ozh' FAQ Auto Responder
Plugin URI: http://planetozh.com/blog/my-projects/wordpress-comment-faq-auto-responder/
Description: Replies potential answer(s) when a comment with a FAQ is posted.
Version: 1.0.1
Author: Ozh
Author URI: http://planetOzh.com/
*/

/********** CUSTOMISATION ***********/

/* FAQs
 * Define an array of FAQ patterns (regular expression) and their answers. */
$wp_ozh_faq['faq'] = array(
		/*	'question' => 'answer',
		 *	'what is your name' => 'My name is Bond. James Bond',
		 *	'How (rich|old|big) are you' => 'I\'m sorry, this information is kept secret.',
		 *	'.*where.*download.*your program' => 'Check this page : <a href="http://site.com/software">my soft</a>',
		 */
 "What does Marry Poppin always say ?" => 'Supercalifragilisticexpialidocious, she say :)',
 "what(\ is|(\\\')?s) your name" => 'Duh ! My name is Ozh !',
 'headers already sent' => "
<p>Are you getting an error like \"<code>Cannot modify header information - headers already sent by (output
started at /some/path/stuff.php:265) [...]</code>\" ?
<p>If so, <strong>please check</strong> the file you've just installed : there are probably trailing spaces or blank lines after the closing <strong>?></strong> tag (or before the opening <strong>&lt;?php</strong> tag)
<p>Remove these empty spaces or lines and your error will be hopefully gone."
/**/
);

 
/**********    OPTIONAL   ***********/
/********** CUSTOMISATION ***********/
 
/* Some style : Beginning of page
 * Put HTML stuff to make your page pretty and useful.
 * Token %%COMMENT%% will be replaced with actual comment text */ 
$wp_ozh_faq['header'] = <<<HEADER
<html>
<head>
<title>FAQ Auto Responder</title>
<style>
body {text-align:center;background:#EEF;color:003}
h1{color:#336;text-align:center;text-align:center;border-bottom:5px solid #EEF;margin:0 -13px 0px -13px;padding-bottom:10px;}
.page {background:#CCE;padding:13px;font: 13px "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana;text-align:justify;margin:0 auto; width:600px;-moz-border-radius:30px;}
.comment {font-size:11px;color:#666;}
.reply {border-left:20px dotted #EEF;padding-left:13px;margin-left:-25px;margin-bottom:13px;}
.a {color:#336;}
.line2{background:#c0c0ee;border:1px solid #eef;padding:0 10px;}
input {text-align:center;color:#113;}
input:hover {color:#339;}
.form {text-align:right;}
.footer{font-size:9px;text-align:center;border-top:5px solid #EEF;margin:0 -13px 0 -13px;}
a, a:visited {color:black}
a:hover{color:#336}
</style>
</head>
<body>
<div class="page">
<h1>FAQ Auto Responder</h1>
<p>Hello, this is an autoresponder. I may be wrong, but I think the comment you have just posted contains a <em>Frequently Asked Question</em>. I will try to answer you immediately, which will both satisfy you and save me some support time :)</p>
<div class="comment">
	<p><strong>Your comment is :</strong></p>
	%%COMMENT%%
</div>
HEADER;
 
/* Some style : Answers
 * This part will be printed for every FAQ found
 * Tokens %%QUESTION%% and  %%ANSWER%% will be replaced with, guess what,
 * the FAQ pattern and its matching answer. */ 
$wp_ozh_faq['reply'] = <<<REPLY
<div class="reply">
	<div class="q">
		<p><strong>&rarr; Frequently Asked Question detected :</strong></p>
		<p>%%QUESTION%%</p>
	</div>
	<div class="a">
		<p><strong><marquee width="20px" behavior="alternate" direction="right" style="float:left;">&rarr;</marquee> Answer that could make your day :</strong></p>
		<div class="line2"><p>%%ANSWER%%</p></div>
	</div>
</div>
REPLY;

/* Some style : End of page
 * This is the footer.
 * Token %%FORM%% will be replaced with the "OK fine but I want to comment anyway" form.
 * Find this plugin useful ? Leave credits then :) Thank you. */ 
$wp_ozh_faq['footer'] = <<<FOOTER
<div class="sayit">
<p>If you feel that the above answer(s) is(are) helpful and cover your needs, please <a href="javascript:window.close();">close</a> this window, and your comment will not be submitted.</p>
<p>If you still wish to sumbit your comment anyway, well, press the following button :</p>
<div class="form">%%FORM%%</div>
</div>
<div class="footer"><p>This page is generated by <a href="http://planetozh.com/blog/my-projects/wordpress-comment-faq-auto-responder/">FAQ Auto Responder</a>, a Wordpress Plugin by <a href="http://planetozh.com/">Ozh</a></p></div>
</div>
</body>
</html>
FOOTER;

/* The function that does it all */
function wp_ozh_faq($data) {

	global $wp_ozh_faq;
	
	if (empty($wp_ozh_faq['faq'])) return $data; // no FAQ defined ? do nothing !
	
	$comment = $data['comment_content'];

	$is_faq = 0;
	// A first quick loop through faqs to define if this looks like a job for us
	foreach ($wp_ozh_faq['faq'] as $k=>$v) {
		if (preg_match("/$k/i",$comment,$matches)) {
			$is_faq = 1;
			break;
		}
	}
	
	// FAQ Alert !
	if ($is_faq) {
	
		$comment_post_ID = $data['comment_post_ID'];
		$author 		= $data['comment_author'];
		$url 			= $data['comment_author_url'];
		$email 			= $data['comment_author_email'];
	
		@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		@header("Cache-Control: no-cache, must-revalidate");
		@header("Pragma: no-cache");
		
		if (function_exists('wpautop')) {	
			print str_replace('%%COMMENT%%',wpautop($comment),$wp_ozh_faq['header']);
		} else {
			print str_replace('%%COMMENT%%','<p>'.$comment.'</p>',$wp_ozh_faq['header']);
		}
		
		foreach ($wp_ozh_faq['faq'] as $k=>$v) {
			if (preg_match("/$k/i",$comment,$matches)) {
				print strtr($wp_ozh_faq['reply'],
					array(	'%%QUESTION%%' 	=> $k,
							'%%ANSWER%%'	=> $v));
			}
		}
		
		if (function_exists('get_option')) {
			$action = get_option('siteurl').'/wp-comments-post.php';
			$link = get_permalink( $comment_post_ID );
		} else {
			$action = 'javascript:alert(\'Fake page, you are here to style things.\')';
			$link = 'http://fake/comment';
		}
		
		$form = '
			<form action="'.$action.'" method="post">
			<input type="hidden" name="author" value="'.$author.'" />
			<input type="hidden" name="email" value="'.$email.'" />
			<input type="hidden" name="url" value="'.$url.'" />
			<input type="hidden" name="comment" value="'.$comment.'" />
			<input type="hidden" name="comment_post_ID" value="'.$comment_post_ID.'" />
			<input type="hidden" name="ozh_did_faq" value="1" />
			<imput type="hidden" name="redirect_to" value="'. $link .'" />
			<input type="submit" value="Submit Comment" />
			</form>
			';
	
		print str_replace('%%FORM%%',$form,$wp_ozh_faq['footer']);
		exit;
	} else {
		/* Testing FAQ patterns ? Uncomment next block so comments cannot be posted and go for some comment spam testing */
		/**
		print '<pre>';
		print_r($data);
		die ('This comment would make it through, no FAQ detected');
		/**/
		return $data;
	}

}

/* Script called directly ? Let's print out a fake FAQ response so you can tweak your style */
if (!function_exists('add_action')) {

	// No FAQ defined ? Gee.
	if (empty($wp_ozh_faq['faq'])) {
		$wp_ozh_faq['faq'] = array ('Your array FAQ is empty, boyo. When will you configure this plugin properly ?' => 'When I am smart enough to figure out how to do it, duh !');
	} 

	$fake = array_keys($wp_ozh_faq['faq']);
	$fake = $fake[0];
	wp_ozh_faq(array(
		'comment_content' => "Hello this is a fake comment. I love you, and I love your site. Marry me.<br>\nBy the way, I have a question : " . $fake,
		'comment_post_ID' => '999999999',
		'comment_author' => 'SomeDude',
		'comment_author_url' => 'http://somedude.com/',
		'comment_author_email' => 'somedude6554@aol.com'
		));
	
	exit;
}

/* Do we have to add the plugin to Wordpress actions ?
 * (i.e. did someone just pressed "Submit" on a FAQ yet ? */
if (!array_key_exists('ozh_did_faq',$_POST)) {
	add_filter('preprocess_comment', 'wp_ozh_faq');
} else {
	unset($wp_ozh_faq);
}

?>