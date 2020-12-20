<?php
class Parser {
  private $doc;

  public function __construct($url) {
    $options = array(
      'http' => array(
        'method' => 'GET',
        'header' => "User-Agent: DoodleCrawler/0.1\n"
      )
    );

    $context = stream_context_create($options);

    $this -> doc = new DomDocument();
    @$this -> doc -> loadHTML(file_get_contents($url, false, $context));
  }

  public function get($tag) {
    return $this -> doc -> getElementsByTagName($tag);
  }
}
?>