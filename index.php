<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		include 'php/components/seo.php';
		echo seo();
	?>
</head>
<body>
	<div class="wrapper index-page">
		<main class="main-section">
      <div class="logo-container">
        <img src="icons/logo/doodle.png" alt="Doodle" />
      </div>
			<div class="search-container">
				<form action="search.php" method="GET">
					<input class="search-box" type="text" name="term" autocomplete="off" spellcheck="off" />
					<button class="search-button">Search</button>
				</form>
			</div>
		</main>
	</div>
</body>
</html>