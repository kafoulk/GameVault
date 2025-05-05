<?php
require_once __DIR__ . '/dbh.inc.php';

$page = $_GET['page'] ?? 'index';

// List of valid pages
$valid_pages = ['index', 'about', 'products', 'contact'];

// If the page is not valid, direct to 404 page
if (!in_array($page, $valid_pages)) {
    http_response_code(404);
    include "../public/pages/404.php";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameVault</title>
    <link rel="icon" type="image/png" href="../public/assets/images/favicon.png?v=2">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    <header id="header" class="site-header" role="banner">

        <!-- Header Top -->
        <div class="header-top py-2 bg-light-subtle">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4 col-12">
                        <a class="navbar-brand" href="../../public/pages/index.php">
                            <img src="../../public/assets/images/gamevault_long.svg" alt="gamevault logo" style="height:50px">
                        </a>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12 text-end">
                        <p class="mb-0" style="font-size:12px">
                            Unbox Adventure — One Game Night at a Time!
                        </p>
                        <p class="mb-0">
                            <a href="tel:3175555050" class="text-decoration-none">
                                +1(317)555-5050
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Middle -->
        <div class="header-middle py-3 border-top border-bottom" style="height: 100px">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4 col-12">
                        <form class="d-flex" id="search" method="GET" action="/../../GameVault/public/pages/searchResults.php">
                            <input id="search_input" name="q" class="form-control me-2" type="search" placeholder="Search..." aria-label="Search">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                        <nav class="navbar navbar-expand-lg navbar-light justify-content-end">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link" href="../../public/pages/index.php">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="../../public/pages/about.php">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="../../public/pages/products.php">Products</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="../../public/pages/contact.php">Contact</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="btn btn-primary ms-3" href="../../public/pages/login.php"><i class="bi bi-person fs-4"></i></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="btn btn-primary ms-3" href="../../public/pages/shoppingCart.php"><i class="bi bi-cart3 fs-4"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </header>

    <body>

