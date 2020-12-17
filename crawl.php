<?php
include('config.php');
include('php/parser.php');

$already_crawled = array();
$crawling = array();

function insert_link($url, $title, $description, $keywords) {
  global $con;
  $query = $con -> prepare("INSERT INTO sites(url, title, description, keywords)
                            VALUES(:url, :title, :description, :keywords)");

  $query -> bindParam(':url', $url);  
  $query -> bindParam(':title', $title);  
  $query -> bindParam(':description', $description);  
  $query -> bindParam(':keywords', $keywords);  

  return $query -> execute();
}

function get_details($url) {
  $parser = new Parser($url);

  $title_array = $parser -> get_title();

  if(sizeof($title_array) == 0 || $title_array -> item(0) == NULL) {
    return;
  }

  $title = $title_array -> item(0) -> nodeValue;
  $title = str_replace('\n', '', $title);

  if($title == '') {
    return;
  }

  $description = '';
  $keywords = '';

  $meta_array = $parser -> get_meta();

  foreach($meta_array as $meta) {
    if($meta -> getAttribute('name') == 'description') {
      $description = $meta -> getAttribute('content');
    }
    else if($meta -> getAttribute('name') == 'keywords') {
      $keywords = $meta -> getAttribute('content');
    }
  }

  $description = str_replace('\n', '', $description);
  $keywords = str_replace('\n', '', $keywords);

  insert_link($url, $title, $description, $keywords);
}

function create_link($src, $url) {
  $scheme = parse_url($url)['scheme'];
  $host = parse_url($url)['host'];

  if(substr($src, 0, 2) == '//') {
    $src = $scheme . ':' . $src;
  }
  else if(substr($src, 0, 1) == '/') {
    $src = $scheme . '://' . $host . $src;
  }
  else if(substr($src, 0, 1) == './') {
    $src = $scheme . '://' . $host . dirname(parse_url($url)['path']).substr($src, 1);
  }
  else if(substr($src, 0, 3) == '../' || substr($src, 0, 4) !== 'http') {
    $src = $scheme . '://' . $host . '/' . $src;
  }

  return $src;
}

function crawl($url) {
  global $already_crawled;
  global $crawling;

  $parser = new Parser($url);
  
  $links = $parser -> get_links();

  foreach($links as $link) {
    $href = $link -> getAttribute('href');

    if(strpos($href, "#") !== false || substr($href, 0, 11) == "javascript:") {
      continue;
    }

    $href = create_link($href, $url);

    if(!in_array($href, $already_crawled)) {
      $already_crawled[] = $href;
      $crawling[] = $href;
      get_details($href);
    }
    else {
      return;
    }
  }

  array_shift($crawling);

  foreach($crawling as $site) {
    crawl($site);
  }
}

$start_url = 'https://www.bbc.com';

crawl($start_url);

?>