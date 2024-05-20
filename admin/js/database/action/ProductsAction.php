<?php
include '../../../../database/conf.php';

if (isset($_POST["action"])) {
    if ($_POST["action"] == "get") {
        $p_id = $_POST["id"];

        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT `p_id`, `cat_id`, `scat_id`, `p_title`, `p_subtitle`, `p_desc`, `p_prize`, `p_image` FROM `product` WHERE p_id = ?");
        $stmt->bind_param("s", $p_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $form = '';
            $data = $result->fetch_assoc();

            $form .= '<form id="productsEditForm" action="js/database/ProductsUpdate.php" method="post" enctype="multipart/form-data">
                <!-- Form fields -->
            </form>';

            echo json_encode(["type" => "success", "data" => $form], true);
        } else {
            echo json_encode(["type" => "error", "msg" => "Product not found"], true);
        }

        $stmt->close();
    }

    if ($_POST["action"] == "del") {
        $id = $_POST["id"];

        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM `product` WHERE `p_id` = ?");
        $stmt->bind_param("s", $id);

        if ($stmt->execute()) {
            echo json_encode(["type" => "success", "msg" => "Delete Successfully"], true);
        } else {
            echo json_encode(["type" => "error", "msg" => "Something went wrong"], true);
        }

        $stmt->close();
    }
}
?>