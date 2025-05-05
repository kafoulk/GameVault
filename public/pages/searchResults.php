<?php
include "../../includes/header.php";
require_once '../../includes/dbh.inc.php';
include "../src/php/searchHandler.php";

?>
    <main>
        <section class="mt-4" style="padding-top:50px">
            <div class="container">
                <div id="search_results" class="row justify-content-center">
                </div>
            </div>
        </section>
    </main>

<script>
    document.getElementById("search").addEventListener("submit", function (e) {
        e.preventDefault();
        const query = document.getElementById("search_input").value;

        fetch("../src/php/searchHandler.php?q=" + encodeURIComponent(query))
            .then(res => res.text())
            .then(data => {
                document.getElementById("search_results").innerHTML = data;
            });
    });
</script>
<?php include "../../includes/footer.php"; ?>