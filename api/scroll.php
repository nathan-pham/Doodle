<?php
include '../config.php';
include '../php/image_provider.php';

$results_provider = new ImageProvider($con);
$page_size = 20;
$num_results = $results_provider -> get_num_results($_POST['term']);

echo $results_provider -> get_results_html($_POST['page'], $page_size, $term);
?>