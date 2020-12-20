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

        $query = $this -> con -> prepare("SELECT * FROM images 
                                          WHERE (title LIKE :term
                                          OR alt LIKE :term)
                                          AND broken = 0
                                          ORDER BY clicks DESC
                                          LIMIT :from_limit, :page_size");

        $search_term = '%' . $term . '%';
        $query -> bindParam(':term', $search_term);
        $query -> bindParam(':from_limit', $from_limit, PDO::PARAM_INT);
        $query -> bindParam(':page_size', $size, PDO::PARAM_INT);
        
        $query -> execute();

        $results_html = "<div class='image-results'>";

        while($row = $query -> fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $imageUrl = $row['imageUrl'];
            $siteUrl = $row['siteUrl'];
            $title = $row['title'];
            $alt = $row['alt'];
            
            $host_url = parse_url($siteUrl);
            $host_url = $host_url['host'];
            
            $display  = $title ? $title : $alt;
            if(!$display) {
                $display = $imageUrl;
            }
            
            $results_html .= "<div class='grid-item'>
                                <img src='$imageUrl' />
                                <a href='$siteUrl' target='_blank'>
                                    <h3>$display</h3>
                                    <p class='link'>$host_url</p>
                                </a>
                              </div>";
        }

        return $results_html . "</div>";
    }
}
?>