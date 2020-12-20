<?php
include('config.php');
include('php/results_provider.php');

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
		include "php/components/seo.php";
		echo seo();
	?>
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
            	<input class="search-box" type="text" name="term" autocomplete="off" spellcheck="off" value="<?php echo $term; ?>" />
              <button>
                <?php
                  include('php/components/search-icon.php');
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
        $results_provider = new ResultsProvider($con);
        $page_size = 20;

        $num_results = $results_provider -> get_num_results($term);

        echo "<p class='results-count'>$num_results results found</p>";
        echo $results_provider -> get_results_html($page, $page_size, $term);
      ?>
    </main>
    <footer class="pagination-container">
      <div class="page-buttons">
        <div class="page-number-container">
          <img src="icons/logo/page_start.png" alt="D" />
        </div>

        <?php
          $pages_show = 10;
          $num_pages = ceil($num_results / $page_size);
          $pages_left = min($pages_show, $num_pages);

          $current_page = $page - floor($pages_show / 2);
          if($current_page < 1) {
            $current_page = 1;
          }

          while($pages_left > 0) {
            if($current_page == $page) {
              echo "<div class='page-number-container'>
                      <img src='icons/logo/page_selected.png' alt='page' />
                      <span class='page-number'>$current_page</span>
                    </div>";  
            }
            else {
              echo "<div class='page-number-container'>
                      <a href='search.php?term=$term&type$type&page=$current_page'>
                        <img src='icons/logo/page.png' alt='page' />
                        <span class='page-number'>$current_page</span>
                      </a>
                    </div>";
            }
            
            $current_page++;
            $pages_left--;
          }
        ?>

        <div class="page-number-container">
          <img src="icons/logo/page_end.png" alt="dle" />
        </div>  
      </div>
    </footer>
  </div>
</body>
</html>