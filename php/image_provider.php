<?php
class ImageProvider {
    
    private $con;

    public function __construct($con) {
        $this -> con = $con;
    }

    public function get_num_results($term) {
        $query = $this -> con -> prepare("SELECT COUNT(*) as total 
                                          FROM images 
                                          WHERE (title LIKE :term
                                          OR alt LIKE :term)
                                          AND broken = 0");
        $search_term = '%' . $term . '%';
        $query -> bindParam(':term', $search_term);
        $query -> execute();

        $row = $query -> fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function get_results_html($page, $size, $term) {
        $from_limit = ($page - 1) * $size;

        $query = $this -> con -> prepare("SELECT *
                                          FROM sites WHERE title LIKE :term
                                          OR url LIKE :term
                                          OR keywords LIKE :term
                                          OR description LIKE :term
                                          ORDER BY clicks DESC
                                          LIMIT :from_limit, :page_size");

        $search_term = '%' . $term . '%';
        $query -> bindParam(':term', $search_term);
        $query -> bindParam(':from_limit', $from_limit, PDO::PARAM_INT);
        $query -> bindParam(':page_size', $size, PDO::PARAM_INT);
        
        $query -> execute();

        $results_html = "<div class='site-results'>";

        while($row = $query -> fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $url = $row['url'];
            $title = $this -> trim_field($row['title'], 55);
            $description = $this -> trim_field($row['description'], 230);
            
            $url = rtrim($url, '/');
            
            $parsed_url = parse_url($url);
            $to_replace = isset($parsed_url['path']) ? $parsed_url['host'] . $parsed_url['path'] : $parsed_url['host'];
            $replaced = str_replace('/', ' › ', $to_replace);
            $pretty_url = $parsed_url['scheme'] . '://' . $replaced;

            $results_html .= "<div class='result-container'>
                                <span class='url'>$pretty_url</span>
                                <h3 class='title'>
                                    <a href='$url' data-id='$id'>$title</a>
                                </h3>
                                <span class='description'>$description</span>
                              </div>";
        }

        return $results_html . "</div>";
    }

    private function trim_field($string, $limit) {
        $dots = strlen($string) > $limit ? '...' : '';
        return substr($string, 0, $limit) . $dots;
    }
}
?>