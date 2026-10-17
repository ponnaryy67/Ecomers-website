<?php

include 'Layout/header.php';
include 'components/banner.php';


$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>


<main style="padding: 10px 40px 40px 40px; min-height: 80vh;">
    <?php
    switch ($page) {
        case 'home':
            
            include 'components/product.php';
            break;
            
        case 'women':
            include 'components/women.php';
            break;
            
        case 'men':
            include 'components/men.php';
            break;
            
        case 'bags':
            include 'components/bags.php';
            break;
            
        case 'shoes':
            include 'components/shoes.php';
            break;
            
        case 'caps':
            include 'components/caps.php';
            break;
            
        default:
          
            include 'components/product.php';
            break;
    }
    ?>
</main>

<?php

include 'Layout/footer.php';
?>



