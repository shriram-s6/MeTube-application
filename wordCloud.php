<?php 
//error_reporting(E_ERROR | E_PARSE);
require_once("header.php"); ?>

<html lang="en-us">
    <head>
        <title>MeTube</title>
        <link rel="stylesheet" type="text/css" href="wordcloud/wordCloud.css">
    </head>
    <body>
        <?php
            // $content = file_get_contents("wordcloud/words.txt");

            // $words = preg_split('/([a-zA-Z]+[\s]*[a-zA-Z]*)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
            
            $querySQL = "SELECT * FROM search_items";
            $query = $connect->prepare($querySQL);
            $query->execute();

            $words = array();

            foreach($query->fetchAll() as $row) {
                // echo $row['search_term'];
                array_push($words, $row['search_term']);
                array_push($words, $row['search_count']);
            }

            echo "<p>";
            for($i = 0; $i < count($words); $i = $i + 2) {
                $word_count = $words[$i + 1];
                $word_count = trim($word_count);
                echo '<span class="'."a".$word_count.'">';
                echo trim($words[$i]);
                echo "  ";
                echo "</span>";
            }
            echo "</p>";
        ?>
    </body>
</html>