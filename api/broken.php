<?php
if(isset($_POST['src'])) {
    $query = $con -> prepare("UPDATE images SET broken = 1 WHERE imageUrl = :src");
    $query -> bindParam(':src', $_POST['src']);
    $query -> execute();

    echo "SUCCESS: updated image";    
}
else {
    echo "ERROR: src not specified";
}
?>