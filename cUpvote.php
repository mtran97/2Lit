<?php

include_once "global.php";

if (isset($_SESSION['userid'])) {
	if(isset($_POST['commentid'])) {
		$commentid = $_POST['commentid'];
		$upvote = $_POST['cUpvote'];
		$downvote = $_POST['cDownvote'];
		$userid = $_SESSION['userid'];
		$query = "SELECT upvotes, downvotes FROM lit_comment WHERE comment_id = '$commentid'";
		$result = $conn->query($query);
		$data = $result->fetch_assoc();
		$tot_upvotes = intval($upvote) + intval($data['upvotes']);
		$tot_downvotes = intval($downvote) + intval($data['downvotes']);
		$query = "UPDATE lit_comment SET upvotes = '$tot_upvotes', downvotes = '$tot_downvotes' WHERE comment_id = '$commentid'";
		$conn->query($query);
		$query = "SELECT response_id, response FROM lit_comment_response WHERE user_id = '$userid' AND comment_id = '$commentid'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) {
			$number = 0;
			if ($upvote == "1")	$number = 1;
			else if ($downvote == "1")	$number = 2;
			$query = "INSERT INTO lit_comment_response (user_id, comment_id, response) VALUES ('$userid', '$commentid', '$number')";
			$conn->query($query);
		}
		else {
			$data = $result->fetch_assoc();
			$response_id = $data['response_id'];
			$number = 0;
			if ($upvote == "1")	$number = 1;
			else if ($downvote == "1")	$number = 2;
			$query = "UPDATE lit_comment_response SET response = '$number' WHERE response_id = '$response_id' AND user_id = '$userid'";
			$conn->query($query);
		}
			// Update OP's upvote and downvote scores
		$query = "SELECT user_id FROM lit_comment WHERE comment_id = '$commentid'";
		$result = $conn->query($query);
		$data = $result->fetch_assoc();
		$user_id = $data['user_id'];
		$query = "SELECT total_upvotes, total_downvotes FROM lit_user WHERE user_id = '$user_id'";
		$result = $conn->query($query);
		$data = $result->fetch_assoc();
		$tot_upvotes = intval($upvote) + intval($data['total_upvotes']);
		$tot_downvotes = intval($downvote) + intval($data['total_downvotes']);
		$query = "UPDATE lit_user SET total_upvotes = '$tot_upvotes', total_downvotes = '$tot_downvotes' WHERE user_id = '$user_id'";
		$conn->query($query);
		unset($_POST['commentid']);
	}
}

?>