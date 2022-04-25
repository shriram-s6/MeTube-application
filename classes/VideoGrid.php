<link rel="stylesheet" type="text/css" href="css/videoGrid.css">

<?php
//error_reporting(E_ERROR | E_PARSE);
class VideoGrid {
	private $connect, $userLoggedIn;
	
	public function __construct($connect, $userLoggedIn) {
		$this->connect = $connect;
		$this->userLoggedIn = $userLoggedIn;
	}

	public function create($username, $videoId, $videos) {
		
		if ($videos == null) {
			
			if ($username == null) {
				$items = $this->createItems($videoId);
			} else {
				
				$items = $this->createItemsFromUsername($username);
			}
		} else {
			
			$items = $this->createItemsFromVideos($videos);
		}
		
		return "<div class='videoGrid'>
			$items
		</div>";
	}

	private function createItems($videoId) {
		if ($videoId == null) {
			$query = $this->connect->prepare("SELECT * FROM file_uploads ORDER BY RAND() LIMIT 18");
		} else {
			$query = $this->connect->prepare("SELECT * FROM file_uploads WHERE fileType = 0 AND id != :videoId ORDER BY RAND() LIMIT 8");
			$query->bindParam(":videoId", $videoId);
		}
		$query->execute();
		$html = "";
		foreach ($query->fetchAll() as $row) {
			$video = new Video($this->connect, $row, $this->userLoggedIn);
			$item = $this->createGridItem($video);
			$html .= $item;
		}

		return $html;
	}

	private function createItemsFromVideos($videos) {
		$html = "";
		foreach ($videos as $video) {
			$item = $this->createGridItem($video);
			$html .= $item;
		}

		return $html;
	}

	private function createItemsFromUsername($username) {
		$query = $this->connect->prepare("SELECT * FROM file_uploads WHERE fileType = 0 AND uploadedBy = :username ORDER BY RAND() LIMIT 15");
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
		if ($video->getFileType() == 0 || $video->getFileType() == 1) {
			$thumbnail = $this->createThumbnail($video);
			$details = $this->createDetails($video);
			$url = "watchVideo.php?id=" . $video->getVideoId();

			return "<a href='$url'>
					<div class='videoGridItem'>
						$thumbnail
						$details
					</div>
				</a>";
		} elseif ($video->getFileType() == 2) {
			$thumbnail = $this->createThumbnail($video);
			$details = $this->createDetails($video);

			return "
					<div class='videoGridItem'>
						$thumbnail
						$details
					</div>";
		} 
	}

	private function createThumbnail($video) {

		if ($video->getFileType() == 0) {
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
		} elseif ($video->getFileType() == 2) {
			$query = $this->connect->prepare("SELECT filePath FROM file_uploads WHERE id = :id");
			$query->bindParam(":id", $video->getVideoId());
			$query->execute();

			$imgSrc = $query->fetchAll()[0]["filePath"];

			return "<div class = 'thumbnail'>
					<img src='$imgSrc'>
			</div>";
		} elseif ($video->getFileType() == 1) {
			$imgSrc = "images/icons/audio_gif.gif";

			return "<div class = 'thumbnail'>
					<img src='$imgSrc'>
			</div>";
		}
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