<link rel="stylesheet" type="text/css" href="css/videoGrid.css">

<?php

class VideoGrid {
	private $connect, $userLoggedIn;
	
	public function __construct($connect, $userLoggedIn) {
		$this->connect = $connect;
		$this->userLoggedIn = $userLoggedIn;
	}

	public function create($username) {
		
		if ($username == null) {
			$items = $this->createItems();
		} else {
			$items = $this->createItemsFromUsername($username);
		}
		
		return "<div class='videoGrid'>
			$items
		</div>";
	}

	private function createItems() {
		$query = $this->connect->prepare("SELECT * FROM file_uploads ORDER BY RAND() LIMIT 15");
		$query->execute();
		$html = "";
		foreach ($query->fetchAll() as $row) {
			$video = new Video($this->connect, $row, $this->userLoggedIn);
			$item = $this->createGridItem($video);
			$html .= $item;
		}

		return $html;
	}

	private function createItemsFromUsername($username) {
		$query = $this->connect->prepare("SELECT * FROM file_uploads WHERE uploadedBy = :username ORDER BY RAND() LIMIT 15");
		$query->bindParam(":username", $username);
		$query->execute();
		$html = "";
		foreach ($query->fetchAll() as $row) {
			$video = new Video($this->connect, $row, $this->userLoggedIn);
			$item = $this->createGridItem($video);
			$html .= $item;
		}

		return $html;
	}

	private function createGridItem($video) {
		$thumbnail = $this->createThumbnail($video);
		$details = $this->createDetails($video);
		$url = "watchVideo.php?id=" . $video->getVideoId();

		return "<a href='$url'>
				<div class='videoGridItem'>
					$thumbnail
					$details
				</div>
			</a>";
	}

	private function createThumbnail($video) {
		$query = $this->connect->prepare("SELECT filePath FROM video_thumbnails WHERE videoId = :id AND pickedThumbnail = 1");
		$query->bindParam(":id", $video->getVideoId());
		$query->execute();

		$imgSrc = $query->fetchAll()[0]["filePath"];
		$duration = $video->getDuration();

		return "<div class = 'thumbnail'>
				<img src='$imgSrc'>
				<div class='duration'>
					<span>$duration</span>
				</div>
		</div>";
	}

	private function createDetails($video) {
		$title = $video->getTitle();
		$username = $video->getUploadedBy();
		$views = $video->getViews();
		$uploadDate = $video->getUploadDate();

		return "
			<div class='detials'>
				<h3 class='title'>$title</h3>
				<span class='username'>$username</span>
				<div class='stats'>
					<span class='views'>$views views - </span>
					<span class='date'>$uploadDate</span>
				</div>
			</div>
		";
	}
}

?>