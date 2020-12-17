<?php
include('config.php');

if(isset($_GET['term'])) {
  $term = $_GET['term'];
}
else {
  header('Location: /');
  die();
}

$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Doodle</title>
	<link rel="stylesheet" href="css/globals.css" />
	<link rel="stylesheet" href="css/style.css" />
</head>
<body>
	<div class="wrapper search-page">
    <header class="header">
      <div class="header-content">
        <div class="logo-container">
          <a href="./">
            <img src="doodle.png" alt="Doogle" />
          </a>
        </div>
        <div class="search-container">
          <form action="search.php" method="GET">
            <div class="search-bar-container">
            	<input class="search-box" type="text" name="term" autocomplete="off" spellcheck="off" />
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
            <a href="<?php echo 'search.php?term=$term&type=sites'; ?>">Sites</a>
          </li>
          <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
            <a href="<?php echo 'search.php?term=$term&type=images'; ?>">Images</a>
          </li>
        </ul>
      </div>
    </header>
	</div>
</body>
</html>