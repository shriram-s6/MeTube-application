<?php

class DiscussionDetails {
    private $connect;
    public function __construct($connect) {
        $this->connect = $connect;
    }

    public function createDiscussionForm(): string
    {

        $titleInput = $this->createTitleInput();
        $descriptionInput = $this->createDiscussionDescription();
        $categoriesInput = $this->createCategoriesInput();
        $postButton = $this->createPostButton();

        return "
            <form action='videoProcessing.php' method='POST' enctype='multipart/form-data'>
                $titleInput
                $descriptionInput
                $categoriesInput
                $postButton
            </form>
        
        ";
    }

    private function createTitleInput(): string
    {
        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='Discussion title' name='titleInput'>
                </div>";
    }

    private function createDiscussionDescription(): string
    {
        return "<div class='form-group'>
                    <textarea class='form-control' id='discussionDescription' rows='6' name='descriptionInput' placeholder='Discussion Description' required></textarea>
                </div>";
    }

    private function createCategoriesInput(): string
    {

        $query = $this->connect->prepare("SELECT * FROM discussion_categories;");
        $query->execute();

        $html = "<div class='form-group'>
                    <label for='categorySelect'>Select a topic</label>
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

    private function createPostButton(): string
    {
        return "<button type='submit' class='btn btn-primary' name='uploadButton'>Post</button>";
    }
}