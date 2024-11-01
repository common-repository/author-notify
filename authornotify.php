<?php

/*
Plugin Name: Author Notify
Plugin URI: http://blog.mikezhang.com/authornotify
Description: A particular author can choose to display a message on all his/her posts
Version: 0.1
Author: Michael X. Zhang
Author URI: http://blog.mikezhang.com/

Copyright (c) 2006-2007 Michael X. Zhang Released under the GNU General Public License (GPL) http://www.gnu.org/licenses/gpl.txt

TODO:
One big issue is that only one author can choose to display something, I need to add support for multiple authors in the future version.

*/

$defaultdata = array(
	'message' => "This message will be displayed on all pages written by the author with the chosen nickname",
	'message_location' => 'before_post',
	'authornick' => 'admin'
	);
	
add_option('an_settings', $defaultdata, 'Options for Author Notify');

$an_settings = get_option('an_settings'); $an_settings['message'] = stripslashes($an_settings['message']);

add_action('admin_menu', 'add_an_options_page');

add_filter('the_content', 'an_message_filter');

function add_an_options_page()
{
	if (function_exists('add_options_page'))
	{
		add_options_page('Author Notify', 'Author Notify', 8, basename(__FILE__), 'an_options_subpanel');
	}
}

function an_options_subpanel()
{
	global $an_settings, $_POST;
	
	if (isset($_POST['submit']))
	{
		?><div id="message" class="updated fade"><p><strong><?php 
		$an_settings['message'] = stripslashes($_POST["message"]);
		$an_settings['message_location'] = $_POST['message_location'];
		$an_settings['authornick'] = $_POST["authornick"];

		update_option('an_settings', $an_settings);

		echo "Author/Message Options Updated!";
		?></strong></p></div><?php
	}
	?>
	<div class="wrap">
        <h2>Author Notify</h2>
        <p>Setup a text to be displayed on every post written by an author associated with a particular nickname.</p>
        <form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
	<h3>Author:</h3>

	<p>Fill in the desired author's nickname.</p>
	<p><input type="text" name="authornick" value="<?php echo $an_settings['authornick']; ?>" size="10" /> </p>
        <h3>Message:</h3>
        <textarea rows="4" cols="80" name="message"><?php echo htmlentities($an_settings['message']); ?></textarea>
        <h3>Location of Message</h3>
        <p><input type="radio" name="message_location" value="before_post" <?php if ($an_settings['message_location'] == 'before_post') echo 'checked="checked"'; ?> /> Before Post</p>
        <p><input type="radio" name="message_location" value="after_post" <?php if ($an_settings['message_location'] == 'after_post') echo 'checked="checked"'; ?> /> After Post</p>
        
        <p><input type="submit" name="submit" value="Save Settings" /></p>
        </form>
        </div>
	<?php
}


function an_message_filter($content = '') {
				global  $an_settings, $an_messagedisplayed;
        $a_nick = get_the_author_nickname(); 
				if ( $a_nick == $an_settings['authornick'] && !is_feed())
        {
        	$an_messagedisplayed = true;

          //add a little style to the message box
          if (!$an_messagedisplayed){
          	$an_settings['message']= "<p style='border:thin dotted black; padding:3mm;background:#eff;'>" . $an_settings['message'] . "</p>";
          	$an_messagedisplayed = true;
          }
          
        	if ($an_settings['message_location'] == 'before_post')
        	{
        		return
        		$an_settings['message']
                        .
        		$content
        		;
        	}
        	else
        	{
        		return
        		$content
        		.
        		$an_settings['message']
        		;
        	}			
        }
        else
        {
        	return $content;
        }
}

?>
