<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Doodle</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="Browse the web for sites and images." />
	<meta name="keywords" content="doodle, images, website, engine, search" />
	<meta name="author" content="Nathan Pham" />

	<link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">

	<link rel="stylesheet" href="css/globals.css" />
	<link rel="stylesheet" href="css/style.css" />
</head>
<body>
	<div class="wrapper index-page">
		<main class="main-section">
      <div class="logo-container">
        <img src="doodle.png" alt="Doodle" />
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