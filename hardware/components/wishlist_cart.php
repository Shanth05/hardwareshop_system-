<?php
declare(strict_types=1);

if (isset($_POST['add_to_wishlist'])) {

    if (empty($user_id)) {
        header('Location: user_login.php');
        exit;
    } else {
        $pid = filter_input(INPUT_POST, 'pid', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
        $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);

        $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
        $check_wishlist_numbers->execute([$name, $user_id]);

        $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $check_cart_numbers->execute([$name, $user_id]);

        if ($check_wishlist_numbers->rowCount() > 0) {
            $message[] = 'already added to wishlist!';
        } elseif ($check_cart_numbers->rowCount() > 0) {
            $message[] = 'already added to cart!';
        } else {
            $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
            $insert_wishlist->execute([$user_id, $pid, $name, $price, $image]);
            $message[] = 'added to wishlist!';
        }
    }
}

if (isset($_POST['add_to_cart'])) {

    if (empty($user_id)) {
        header('Location: user_login.php');
        exit;
    } else {
        $pid = filter_input(INPUT_POST, 'pid', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
        $image = filter_input(INPUT_POST, 'image', FILTER_SANITIZE_STRING);
        $qty = filter_input(INPUT_POST, 'qty', FILTER_SANITIZE_STRING);

        $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
        $check_cart_numbers->execute([$name, $user_id]);

        if ($check_cart_numbers->rowCount() > 0) {
            $message[] = 'already added to cart!';
        } else {

            $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
            $check_wishlist_numbers->execute([$name, $user_id]);

            if ($check_wishlist_numbers->rowCount() > 0) {
                $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
                $delete_wishlist->execute([$name, $user_id]);
            }

            $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
            $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
            $message[] = 'added to cart!';
        }
    }
}
?>