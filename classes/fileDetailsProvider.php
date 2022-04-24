<?php
//error_reporting(E_ERROR | E_PARSE);
class VideoDetails {

    private $connect;
    public function __construct($connect) {
        $this->connect = $connect;
    }

    public function createUploadForm() {
        $fileTypeInput = $this->createFileTypeInput();
        $fileInput = $this->createFileInput();
        $titleInput = $this->createTitleInput();
        $descriptionInput = $this->createFileDescription();
        $privacyInput = $this->createPrivacyInput();
        $commentInput = $this->createCommentInput();
        $categoriesInput = $this->createCategoriesInput();
        $uploadButton = $this->createUploadButton();

        $output = "
            <form action='videoProcessing.php' method='POST' enctype='multipart/form-data'>
                $fileTypeInput
                $fileInput
                $titleInput
                $descriptionInput
                $privacyInput
                $commentInput
                $categoriesInput
                $uploadButton
            </form>
        
        ";
        echo $output;
        return $output;
    }

    private function createFileTypeInput() {
        return "<div class='form-group'>
                    <label for='fileTypeSelect'>Choose file type</label>
                        <select class='form-control' id='fileTypeSelect' name='fileTypeInput'>
                            <option value='0'>Video</option>
                            <option value='1'>Audio</option>
                            <option value='2'>Image</option>
                        </select>
                </div>";
    }

    private function createFileInput() {
        return "<div class='form-group'>
                    <label class='form-control-file' for='uploadedFile'>Upload file...</label>
                    <input type='file' id='uploadedFile' name='fileInput' required>
                </div>";
    }

    private function createTitleInput() {
        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='File title' name='titleInput'>
                </div>";
    }

    private function createFileDescription() {
        return "<div class='form-group'>
                    <textarea class='form-control' id='fileDescription' rows='3' name='descriptionInput' placeholder='File Description'></textarea>
                </div>";
    }

    private function createPrivacyInput() {
        return "<div class='form-group'>
                    <label for='privacySelect'>Sharing Mode</label>
                        <select class='form-control' id='privacySelect' name='privacyInput'>
                            <option value='0'>Private</option>
                            <option value='1'>Public</option>
                            <option value='2'>Friends</option>
                        </select>
                </div>";
    }

    private function createCommentInput() {
        return "<div class='form-check form-check-inline'>
                    <span>Comments&nbsp;&nbsp;</span>
                    <input class='form-check-input' type='radio' name='commentInput' id='turnOn' value='1' required>
                    <label class='form-check-label' for='turnOn'>on</label>
                </div>
                <div class='form-check form-check-inline'>
                    <input class='form-check-input' type='radio' name='commentInput' id='turnOff' value='2'>
                    <label class='form-check-label' for='turnOff'>off</label>
                </div>";

    }

    private function createCategoriesInput() {

        $query = $this->connect->prepare("SELECT * FROM file_categories;");
        $query->execute();

        $html = "<div class='form-group'>
                    <label for='categorySelect'>Select a category</label>
                    <select class='form-control' id='categorySelect' name='categoryInput'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $category_name = $row["category_name"];
            $category_id = $row["category_id"];

            $html .= "<option value='$category_id'>$category_name</option>";

        }
        $html .= "</select>
                </div>";

        return $html;
    }

    private function createUploadButton() {
        return "<input type='submit' class='btn btn-primary' name='uploadButton' value='Upload'>";
    }
}
