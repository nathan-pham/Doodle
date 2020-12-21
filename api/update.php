<?php
    include '../config.php';
    
    $type = isset($_POST['type']) ? $_POST['type'] : 'sites';
    
    if($type == 'sites') {
        if(isset($_POST['id'])) {
            $query = $con -> prepare("UPDATE sites SET clicks = clicks + 1 WHERE id = :id");
            $query -> bindParam(':id', $_POST['id']);
            $query -> execute();

            echo "SUCCESS: updated link";    
        }
        else {
            echo "ERROR: id not specified";
        }
    }
    else if($type == 'images') {
        if(isset($_POST['src'])) {
            $query = $con -> prepare("UPDATE images SET broken = 1 WHERE imageUrl = :src");
            $query -> bindParam(':src', $_POST['src']);
            $query -> execute();

            echo "SUCCESS: updated image";    
        }
        else {
            echo "ERROR: src not specified";
        }
    }
?>