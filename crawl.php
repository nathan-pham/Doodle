<?php
include('config.php');
include('php/parser.php');

$already_crawled = array();
$crawling = array();

$already_found_images = array();

function link_exists($url) {
  global $con;
  $query = $con -> prepare("SELECT * FROM sites WHERE url = :url");

  $query -> bindParam(':url', $url);  
  $query -> execute();

  return $query -> rowCount() !== 0;
}

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

function image_exists($url) {
  global $con;
  $query = $con -> prepare("SELECT * FROM images WHERE imageUrl = :url");

  $query -> bindParam(':url', $url);  
  $query -> execute();

  return $query -> rowCount() !== 0;
}

function insert_image($url, $src, $alt, $title) {
  global $con;
  $query = $con -> prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
                            VALUES(:siteUrl, :imageUrl, :alt, :title)");

  $query -> bindParam(':siteUrl', $url);  
  $query -> bindParam(':imageUrl', $src);  
  $query -> bindParam(':alt', $alt);  
  $query -> bindParam(':title', $title);

  return $query -> execute();
}

function get_details($url) {
  global $already_found_images;

  $parser = new Parser($url);

  $title_array = $parser -> get('title');

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

  $meta_array = $parser -> get('meta');

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

  if(link_exists($url)) {
    echo "ERROR: $url already exists";
  }
  else if(insert_link($url, $title, $description, $keywords)) {
    echo "SUCCESS: $url inserted";
  }
  else {
    echo "ERROR: failed to insert $url";
  }
  echo "<br />";

  $img_array = $parser -> get('img');
  foreach($img_array as $img) {
    $src = $img -> getAttribute('src');
    $alt = $img -> getAttribute('alt');
    $title = $img -> getAttribute('title');

    if(!$title && !$alt) {
      continue;
    }

    $src = create_link($src, $url);

    if(!in_array($src, $already_found_images)) {
      $already_found_images[] = $src;
      
      if(!image_exists($src)) {
        if(insert_image($url, $src, $alt, $title)) {
          echo "SUCCESS: $src inserted";
        }
        else {
          echo "ERROR: failed to insert $src";
        }
      }
      else {
        echo "ERROR: $src already exists";
      }
      echo "<br />";
    }
  }
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
  
  $links = $parser -> get('a');

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
  }

  array_shift($crawling);

  foreach($crawling as $site) {
    crawl($site);
  }
}

$start_url = 'https://www.cnn.com/';

crawl($start_url);

?>