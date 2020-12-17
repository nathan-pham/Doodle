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

  public function get_links() {
    return $this -> doc -> getElementsByTagName('a');
  }

  public function get_title() {
    return $this -> doc -> getElementsByTagName('title');
  }

  public function get_meta() {
    return $this -> doc -> getElementsByTagName('meta');
  }
}
?>