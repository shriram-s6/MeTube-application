<?php
require_once("classes/VideoInfoControls.php");
    class VideoInfoSection {
        private $connect, $video, $user;
        public function __construct($connect, $video, $user) {
            $this->connect = $connect;
            $this->video = $video;
            $this->user = $user;
        }

        public function create() {
            return $this->createPrimaryInfo() . $this->createSecondaryInfo();
        }

        private function createPrimaryInfo() {
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

        private function createSecondaryInfo() {


            $description = $this->video->getDescription();
            $uploadDate = $this->video->getUploadDate();
            $uploadedBy = $this->video->getUploadedBy();

            if($uploadedBy == $this->user->getUsername()) {
                $actionButton = ButtonProvider::createEditVideoButton($this->video->getVideoId());
            } else {
                $actionButton = "";
            }


            $query = $this->connect->prepare("SELECT email from users WHERE userName =:username");
            $query->bindParam(":username", $uploadedBy);
            $query->execute();

            $sqlData = $query->fetch(PDO::FETCH_ASSOC);
            $email = $sqlData["email"];

            $profileButton = ButtonProvider::createUserProfileButton($this->connect, $uploadedBy);

            return "<div class='secondaryInfo'>
                        <div class='topRow'>
                            $profileButton
                            
                            <div class='uploadInformation'>
                                <span class='uploader' >
                                    <a href='profile.php?email=$email'>$uploadedBy</a>   
                                </span>
                                <span class='date'>Uploaded on $uploadDate</span>
                            </div>
                            $actionButton
                        </div>
                        
                    </div>";
        }
    }
?>