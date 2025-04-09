<?php
// Load board game data from API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://boardgamegeek.com/wiki/page/BGG_XML_API2');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GameVault</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">

    </head>
    <body>
    <?php include '../../includes/header.php'; ?>
        <main>

        </main>
    <?php include "../../includes/footer.php"; ?>
    <script src="/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>

<?php


?>
