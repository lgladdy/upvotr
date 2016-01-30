# upvotr
A WordPress plugin to allow upvoting of post objects by a user.

It's for developers - you'll need to integrate it into your theme. It supports any post object (so all posts, pages, and CPTs)

### How to use

Install plugin just like any other one.

Send logged in users to <any wordpress url>/?upvote=<post_id> or /?downvote=<post_id> - the user will then be redirected back to that page without the URL parameter once the upvote or downvote has been recorded.

There are 4 helper functions outside of the upvotr class so you can interact with the votes:

```
function get_post_upvotes($id)
	$id (default current post ID) - the ID of the post you want get the upvotes for.
	returns an array of the user IDs who upvoted this post which may be empty, or false if something bad happens.
```

```
function get_post_upvote_count($id)
	$id (default current post ID) - the ID of the post you want get the upvotes for.
	returns an integer of the number of upvotes for this post, or false if something bad happens.
```

```
function has_upvoted_post($id, $user)
	$id (default current post ID) - the ID of the post you want get the upvotes for.
	$user (default current logged in user) - the ID of the user you want to check if they've upvoted $id.
	returns true if they have, or false if they haven't or an error occurs.
```

```
function get_users_upvotes($user)
	$user (default current logged in user) - the ID of the user you want to check if they've upvoted $id.
	returns an array of post objects that $user has upvoted which may be empty, or false on an error
```
