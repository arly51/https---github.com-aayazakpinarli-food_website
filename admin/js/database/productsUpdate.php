<?php
include '../../../database/conf.php';
include "AdminLoginCheck.php";

$p_id = (isset($_POST["pID"]) && $_POST["pID"] != "") ? mysqli_real_escape_string($conn, $_POST["pID"]) : "";
$title = mysqli_real_escape_string($conn, $_POST["pTitle"]);
$subtile = mysqli_real_escape_string($conn, $_POST["pSubtitle"]);
$prize = mysqli_real_escape_string($conn, $_POST["pPrize"]);
$cat_id = mysqli_real_escape_string($conn, $_POST["Cat_id"]);
$scat_id = (isset($_POST["Scat_id"]) && $_POST["Scat_id"] != "") ? mysqli_real_escape_string($conn, $_POST["Scat_id"]) : "";
$desc = mysqli_real_escape_string($conn, $_POST["pDesc"]);
$unique_id = (isset($_POST["unique_id"]) && $_POST["unique_id"] != "") ? mysqli_real_escape_string($conn, $_POST["unique_id"]) : "";

session_start();
$u_id = $_SESSION['u_id'];

if ($cat_id != "" && $prize != "" && $title != "" && $subtile != "") {
    if ($_POST["action"] == "insert") {
        $q2 = $conn->query("SELECT MAX(p_id) as last_inv FROM `product` WHERE market_id = '$u_id'");
        
        if ($q2 && mysqli_num_rows($q2) > 0) {
            $last = mysqli_fetch_assoc($q2);
            
            function increment($matches)
            {
                if (isset($matches[1])) {
                    $length = strlen($matches[1]);
                    return sprintf("%0" . $length . "d", ++$matches[1]);
                }
            }

            $newP_id = $last['last_inv'];
            $newP_id = preg_replace_callback("|(\d+)|", "increment", $newP_id);
        } else {
            $newP_id = "1213543";
        }

        if (isset($_FILES["pImg"])) {
            $img_name = $_FILES["pImg"]["name"];
            $img_type = $_FILES["pImg"]["type"];
            $tmp_name = $_FILES["pImg"]["tmp_name"];
            $img_explode = explode('.', $img_name);
            $img_ext = end($img_explode);
            $allowed_extensions = ['png', 'jpeg', 'jpg', "svg"];

            if (in_array($img_ext, $allowed_extensions) === true) {
                $img = $img_name;
                if (move_uploaded_file($tmp_name, "../../../images/" . $img)) {
                    $q = $conn->query("INSERT INTO `product`(`p_id`, `cat_id`, `scat_id`, `unique_id`, `p_title`, `p_subtitle`, `p_desc`, `p_prize`, `p_image`, `market_id`) VALUES ('$newP_id', '$cat_id', '$scat_id', '$unique_id', '$title', '$subtile', '$desc', '$prize', '$img', '$u_id')");
                    if ($q) {
                        $data = array(
                            "type" => "success",
                            "msg" => "Your product was successfully inserted."
                        );
                    } else {
                        $data = array(
                            "type" => "error",
                            "msg" => "Something went wrong."
                        );
                    }
                } else {
                    $data = array(
                        "type" => "error",
                        "msg" => "Cannot upload your image."
                    );
                }
            } else {
                $data = array(
                    "type" => "error",
                    "msg" => "Select only image files."
                );
            }
        } else {
            $data = array(
                "type" => "error",
                "msg" => "Please select an image."
            );
        }
    } elseif ($_POST["action"] == "update") {
        if (isset($_FILES["pImg"])) {
            $img_name = $_FILES["pImg"]["name"];
            $img_type = $_FILES["pImg"]["type"];
            $tmp_name = $_FILES["pImg"]["tmp_name"];
            $img_explode = explode('.', $img_name);
            $img_ext = end($img_explode);
            $allowed_extensions = ['png', 'jpeg', 'jpg', "svg"];

            if (in_array($img_ext, $allowed_extensions) === true) {
                $img = $img_name;
                if (move_uploaded_file($tmp_name, "../../../images/" . $img)) {
                    $sql = "UPDATE `product` SET `cat_id` = '$cat_id', `scat_id` = '$scat_id', `u_id` = '$u_id', `p_title` = '$title', `p_subtitle` = '$subtile', `p_desc` = '$desc', `p_prize` = '$prize', `p_image` = '$img', `market_id` = '$u_id' WHERE `p_id` = '$p_id'";
                    $q2 = $conn->query($sql);
                    if ($q2) {
                        $data = array(
                            "type" => "success",
                            "msg" => "Your product was updated."
                        );
                    } else {
                        $data = array(
                            "type" => "error",
                            "msg" => "Sorry, the update query failed."
                        );
                    }
                } else {
                    $data = array(
                        "type" => "error",
                        "msg" => "Cannot upload your image."
                    );
                }
            } else {
                $data = array(
                    "type" => "error",
                    "msg" => "Select only image files."
                );
            }
        } else {
            $sql = "UPDATE `product` SET `cat_id` = '$cat_id', `scat_id` = '$scat_id', `u_id` = '$u_id', `p_title` = '$title', `p_subtitle` = '$subtile', `p_desc` = '$desc', `p_prize` = '$prize', `market_id` = '$u_id' WHERE `p_id` = '$p_id'";
            $q2 = $conn->query($sql);
            if ($q2) {
                $data = array(
                    "type" => "success",
                    "msg" => "Your product was updated."
                );
            } else {
                $data = array(
                    "type" => "error",
                    "msg" => "Sorry, the update query failed."
                );
            }
        }
    }
} else {
    $data = array(
        "type" => "error",
        "msg" => "All fields are required."
    );
}

echo json_encode($data, true);
?>