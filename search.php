<?php
include 'config.php';
include 'php/site_provider.php';
include 'php/image_provider.php';

if(isset($_GET['term'])) {
  $term = $_GET['term'];
}
else {
  header('Location: /');
  die();
}

$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php
		include 'php/components/seo.php';
		echo seo();
  ?>
  <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
  <script defer src="js/script.js"></script>
</head>
<body>
	<div class="wrapper search-page">
    <header class="header">
      <div class="header-content">
        <div class="logo-container">
          <a href="./">
            <img src="icons/logo/doodle.png" alt="Doodle" />
          </a>
        </div>
        <div class="search-container">
          <form action="search.php" method="GET">
            <div class="search-bar-container">
              <input type="hidden" name="type" value="<?php echo $type; ?>" />
            	<input class="search-box" type="text" name="term" autocomplete="off" spellcheck="off" value="<?php echo $term; ?>" />
              <button>
                <?php
                  include 'php/components/search-icon.php';
                  echo search_icon();
                ?>
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="tabs-container">
        <ul class="tab-list">
          <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
            <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a>
          </li>
          <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
            <a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
          </li>
        </ul>
      </div>
    </header>
    <main class="results-section">
      <?php
        if($type == 'sites') {
          $results_provider = new SiteProvider($con);
          $page_size = 20;
        }
        else {
          $results_provider = new ImageProvider($con);
          $page_size = 30;  
        }
        
        $num_results = $results_provider -> get_num_results($term);

        if($type == 'sites') {
          echo "<p class='results-count'>$num_results results found</p>";
        }

        echo $results_provider -> get_results_html($page, $page_size, $term);
      ?>
    </main>
    <footer class="pagination-container">
      <div class="page-buttons">
        <?php
        $pagination_html = '';
        if($type == 'sites' && $num_results >= $page_size * 2) {
          $pagination_html .= '<div class="page-number-container">
                                <img src="icons/logo/page_start.png" alt="D" />
                              </div>';

          $pages_show = 10;
          $num_pages = ceil($num_results / $page_size);
          $pages_left = min($pages_show, $num_pages);

          $current_page = $page - floor($pages_show / 2);
          if($current_page < 1) {
            $current_page = 1;
          }

          if($current_page + $pages_left > $num_pages + 1) {
            $current_page = $num_pages + 1 - $pages_left;
          }

          while($pages_left > 0 && $current_page <= $num_pages) {
            if($current_page == $page) {
              $pagination_html .= "<div class='page-number-container'>
                      <img src='icons/logo/page_selected.png' alt='page' />
                      <span class='page-number'>$current_page</span>
                    </div>";  
            }
            else {
              $pagination_html .= "<div class='page-number-container'>
                      <a href='search.php?term=$term&type$type&page=$current_page'>
                        <img src='icons/logo/page.png' alt='page' />
                        <span class='page-number'>$current_page</span>
                      </a>
                    </div>";
            }
            $current_page++;
            $pages_left--;
          }
          $pagination_html .= '<div class="page-number-container">
                                <img src="icons/logo/page_end.png" alt="dle" />
                              </div>';
        }
        else if($type == 'sites') {
          $pagination_html = '<img class="no-page" src="icons/logo/doodle.png" alt="Doodle" />';
        }
        echo $pagination_html;
        ?>
      </div>
    </footer>
  </div>
</body>
</html>