
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$wishlist = isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist'])
    ? $_SESSION['wishlist']
    : [];
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
        }

        .wishlist-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 35px;
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(0,0,0,.04);
        }

        .back-link {
            display: inline-block;
            color: #618281;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 24px;
        }

        h2 {
            font-size: 24px;
            margin: 0 0 28px;
            color: #0f172a;
        }

        .wishlist-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 22px;
            flex: 1;
            min-width: 0;
        }

        .item-img {
            width: 100px;
            height: 102px;
            object-fit: contain;
            border-radius: 14px;
            background: #f1f5f9;
            padding: 7px;
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .item-details h4 {
            font-size: 16px;
            margin: 0 0 8px;
            color: #0f172a;
        }

        .item-price {
            color: #059669;
            font-weight: 700;
            margin: 0;
        }

        .remove-btn {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 9px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .remove-btn:hover {
            background: #dc2626;
            color: #fff;
        }

        .remove-btn:disabled {
            opacity: .6;
            cursor: wait;
        }

        .empty-wishlist {
            text-align: center;
            padding: 50px 0;
            color: #94a3b8;
        }

        @media (max-width: 600px) {
            .wishlist-container {
                padding: 22px 16px;
            }

            .wishlist-item {
                gap: 10px;
            }

            .item-info {
                gap: 12px;
            }

            .item-img {
                width: 70px;
                height: 80px;
            }

            .item-details h4 {
                font-size: 13px;
            }

            .remove-btn {
                padding: 8px 10px;
            }
        }
    </style>
</head>

<body>
<div class="wishlist-container">
    <a href="index.php" class="back-link">← ត្រឡប់ទៅទំព័រដើម</a>

    <h2>ទំនិញដែលអ្នកបានដាក់ Favorite ❤️</h2>

    <div id="wishlistItems">
        <?php if (empty($wishlist)): ?>
            <div class="empty-wishlist">
                <p>គ្មានទំនិញក្នុង Wishlist ទេ!</p>
            </div>
        <?php else: ?>
            <?php foreach ($wishlist as $id => $item): ?>
                <div class="wishlist-item"
                     data-product-id="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="item-info">
                        <img
                            src="<?= htmlspecialchars((string)($item['image'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            alt="Product"
                            class="item-img">

                        <div class="item-details">
                            <h4>
                                <?= htmlspecialchars((string)($item['name'] ?? 'Product'), ENT_QUOTES, 'UTF-8') ?>
                            </h4>

                            <p class="item-price">
                                US $<?= number_format((float)($item['price'] ?? 0), 2) ?>
                            </p>
                        </div>
                    </div>

                    <button type="button" class="remove-btn"
                            data-id="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">
                        Remove
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('wishlistItems').addEventListener('click', async function(event) {
        const button = event.target.closest('.remove-btn');
        if (!button) return;

        const productId = button.dataset.id;
        if (!productId) return;

        button.disabled = true;
        button.textContent = 'Removing...';

        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('id', productId);

        try {
            const response = await fetch('add-to-wishlist.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Cannot remove product');
            }

            // ដកផលិតផលចេញពីទំព័រ
            const row = button.closest('.wishlist-item');
            if (row) row.remove();

            // Update badge ប្រសិនបើទំព័រនេះមាន Header
            if (typeof updateWishlistBadge === 'function') {
                updateWishlistBadge(data.wishlistCount);
            } else {
                const badge = document.getElementById('wishlistCountBadge');

                if (badge) {
                    badge.textContent = data.wishlistCount;
                    badge.style.display =
                        Number(data.wishlistCount) > 0 ? 'flex' : 'none';
                }
            }

           
            if (Number(data.wishlistCount) === 0) {
                document.getElementById('wishlistItems').innerHTML =
                    '<div class="empty-wishlist"><p>គ្មានទំនិញក្នុង Wishlist ទេ!</p></div>';
            }

        } catch (error) {
            console.error('Remove Wishlist Error:', error);
            alert('មិនអាចលុប Favorite បានទេ។ សូមសាកល្បងម្ដងទៀត។');

            button.disabled = false;
            button.textContent = 'Remove';
        }
    });
</script>
</body>
</html>


