<?php
//error_reporting(E_ERROR | E_PARSE);
require_once("classes/VideoInfoControls.php");

class VideoInfoSection
{
    private $connect, $video, $user;

    public function __construct($connect, $video, $user)
    {
        $this->connect = $connect;
        $this->video = $video;
        $this->user = $user;

    }

    public function create()
    {
        return $this->createPrimaryInfo() . $this->createSecondaryInfo();
    }

    private function createPrimaryInfo()
    {
        $title = $this->video->getTitle();
        $views = $this->video->getViews();

        $videoInfoControls = new VideoInfoControls($this->video, $this->user);
        $controls = $videoInfoControls->create();

        return "<div class='videoInfo'>
                        <h1>$title</h1>
                        <div class='bottomSection'>
                            <span class='viewCount'>$views views</span>
                            $controls
                        </div>
                    </div>";
    }


    private function createSecondaryInfo()
    {

        $description = $this->video->getDescription();
        $uploadDate = $this->video->getUploadDate();
        $uploadedByUserName = $this->video->getUploadedBy();
        $videoCategory = $this->video->getCategory();
        $videoId = $this->video->getVideoId();
        $mediaFile = $this->video->getFilePath();

        $query = $this->connect->prepare("SELECT category_name FROM file_categories WHERE category_id =:category_id");
        $query->bindParam(":category_id", $videoCategory);
        $query->execute();

        $videoCategory = $query->fetch(PDO::FETCH_ASSOC)["category_name"];

        if (isset($_POST["mediaRating"])) {

            $ratedBy = $_SESSION["userLoggedIn"];

            $query = $this->connect->prepare("SELECT userName FROM users WHERE email =:email");
            $query->bindParam(":email", $ratedBy);
            $query->execute();

            $ratedBy = $query->fetch(PDO::FETCH_ASSOC)["userName"];

            $rating = $_POST["mediaRating"];

            $query = $this->connect->prepare("SELECT rating FROM media_ratings WHERE mediaId = :mediaId AND ratedBy=:ratedBy");
            $query->bindParam(":mediaId", $videoId);
            $query->bindParam(":ratedBy", $ratedBy);
            $query->execute();

            if ($query->rowCount() == 0) {
                $querySQL = "INSERT INTO media_ratings (mediaId, uploadedBy,rating,ratedBy) VALUES($videoId, '$uploadedByUserName', $rating, '$ratedBy')";

                $query = $this->connect->prepare($querySQL);
                $query->execute();

            } else {

                $querySQL = "UPDATE media_ratings SET rating=$rating WHERE mediaId=$videoId AND ratedBy='$ratedBy'";

                $query = $this->connect->prepare($querySQL);
                $query->execute();

            }
        }


        $avgRatingQuery = $this->connect->prepare("SELECT round(avg(rating), 2) as avg_rating FROM media_ratings WHERE mediaId = :mediaId");
        $avgRatingQuery->bindParam(":mediaId", $videoId);
        $avgRatingQuery->execute();

        if ($avgRatingQuery->rowCount() == 1) {

            $avgMediaRating = $avgRatingQuery->fetch(PDO::FETCH_ASSOC)["avg_rating"];

        } else {

            $avgMediaRating = "0";

        }


        $query = $this->connect->prepare("SELECT email FROM users WHERE username = :username");
        $query->bindParam(":username", $uploadedByUserName);
        $query->execute();

        $uploadedByEmail = $query->fetchAll()[0]["email"];

        if ($uploadedBy == $this->user->getUsername()) {
            //$actionButton = ButtonProvider::createEditVideoButton($this->video->getVideoId());
        } else {
            $userToObject = new User($this->connect, $uploadedByEmail);
            $actionButton = ButtonProvider::createSubscriberButton($this->connect, $userToObject, $this->user);
        }

        $profileButton = ButtonProvider::createUserProfileButton($this->connect, $uploadedByEmail);

        return "<div class='secondaryInfo'>
                        <div class='topRow'>
                            $profileButton
                            
                            <div class='uploadInformation' style='flex: 1; display: flex; flex-direction: column; margin-right: 10px; margin-left: 10px;'>
                                <span class='uploader' >
                                    <a href='profile.php?email=$uploadedByEmail'>
                                        $uploadedByUserName
                                    </a> 
                                </span>
                                <span class='date'>Uploaded on $uploadDate</span>
                            </div>
                            $actionButton
                        </div>
                        <div class='descriptionContainer'>
                            Description: $description
                            <br>
                            Category: $videoCategory
                            <br>
                            <span>Rating: $avgMediaRating</span>
                            <br>
                            <form action='' method='POST'>
                                <label for='ratingForm' style='font-size: 16px'>Please rate the media:</label>
                                    <select id='mediaRating' name='mediaRating'>
                                        <option value='--'>--</option>
                                        <option value=1>1</option>
                                        <option value=2>2</option>
                                        <option value=3>3</option>
                                        <option value=4>4</option>
                                        <option value=5>5</option>
                                    </select>
                                <input type='submit' name='submitRating' value='Submit' style='max-width: 450px;align-self: center;margin-top: 5px;background-color: #a44cfb;color: #fafafa'>
                            </form>
                            <a href='$mediaFile' download>
                                <button class='btn'>
                                    <i class='fa fa-download'></i> Download
                                </button>
                            </a>
                        </div>
                        
                    </div>";
    }
}
