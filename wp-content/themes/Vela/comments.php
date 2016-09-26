<div id="comments">
    <?php
    if ( post_password_required() ) { 
    ?>
	<p class="no-comments"><?php echo __('This post is password protected. Enter the password to view comments.', 'Vela'); ?></p>
    <?php
        return;
    }
    if ( have_comments() ) { ?>
	<div class="comments clear">
        <h3><?php comments_number(__('No Comments', 'Vela'), __('One Comment', 'Vela'), '% '.__('Comments', 'Vela'));?></h3>
		<ul class="comment-list">
			<?php 

            $args = array(
                'style' => 'ul',
                'callback' => 'wyde_comment',
                'avatar_size' => 64
            );

            wp_list_comments($args); 

            ?>
		</ul>
        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
		<div class="comments-nav clear">
		    <span class="alignleft"><?php previous_comments_link('<i class="fa fa-angle-left"></i>'); ?></span>
		    <span class="alignright"><?php next_comments_link('<i class="fa fa-angle-right"></i>'); ?></span>
		</div>
        <?php endif; // Check for comment navigation. ?>
	</div>
<?php
 } else { 

    if ( !comments_open() ) {
    ?>
	<p class="no-comments"><?php echo __('Comments are closed.', 'Vela'); ?></p>
    <?php
    } 
} 

if ( comments_open() ) { 

	function modify_comment_form_fields($fields){
		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );

		$fields['author'] = '<p class="inputrow"><input type="text" name="author" id="author" value="'. esc_attr( $commenter['comment_author'] ) .'" placeholder="'. __("Name", "Vela"). ' ('. __('Required', 'Vela') .')" size="22" tabindex="1"'. ( $req ? ' required' : '' ).' /></p>';

		$fields['email'] = '<p class="inputrow"><input type="text" name="email" id="email" value="'. sanitize_email( $commenter['comment_author_email'] ) .'" placeholder="'. __("Email", "Vela"). ' ('. __('Required', 'Vela') .')" size="22" tabindex="2"'. ( $req ? ' required' : '' ).'  /></p>';

		$fields['url'] = '<p class="inputrow"><input type="text" name="url" id="url" value="'. esc_url( $commenter['comment_author_url'] ) .'" placeholder="'. __("Website", "Vela").'" size="22" tabindex="3" /></p>';

		return  $fields ;
	}
	add_filter('comment_form_default_fields','modify_comment_form_fields');

	$comments_args = array(
		'title_reply' => __("Post A Comment", "Vela"),
		'title_reply_to' =>  __("Leave A Reply", "Vela"),
		'must_log_in' => '<p class="form-desc">' .  sprintf( __( "You must be %slogged in%s to post a comment.", "Vela" ), '<a href="'.wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ).'">', '</a>' ) . '</p>',
		'logged_in_as' => '<p class="form-desc">' . __( "Logged in as","Vela" ).' <a href="' .admin_url( "profile.php" ).'" class="user-link">'.$user_identity.'</a>  <a href="' .wp_logout_url(get_permalink()).'" class="logout-link" title="' . __("Log out of this account", "Vela").'">'. __("Log out", "Vela").'</a></p>',
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'comment_field' => '<p class="inputrow"><textarea name="comment" id="comment" cols="45" rows="8" tabindex="4" class="textarea-comment" placeholder="'. __("Comment...", "Vela").'"></textarea></p>',
		'id_submit' => 'comment-submit',
		'label_submit'=> __("Post Comment", "Vela"),
	);

	comment_form($comments_args);
}
?>
</div>