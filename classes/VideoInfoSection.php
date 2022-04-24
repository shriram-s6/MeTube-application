<?php
error_reporting(E_ERROR | E_PARSE);
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
            $uploadedByUserName = $this->video->getUploadedBy();

            $query = $this->connect->prepare("SELECT email FROM users WHERE username = :username");
            $query->bindParam(":username", $uploadedByUserName);
            $query->execute();

            $uploadedByEmail = $query->fetchAll()[0]["email"];

            if($uploadedBy == $this->user->getUsername()) {
                $actionButton = ButtonProvider::createEditVideoButton($this->video->getVideoId());
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
                                    <a href='profile.php?email=$email'>
                                        $uploadedByUserName
                                    </a> 
                                </span>
                                <span class='date'>Uploaded on $uploadDate</span>
                            </div>
                            $actionButton
                        </div>
                        <div class='descriptionContainer'>
                            $description
                        </div>
                    </div>";
        }
    }
