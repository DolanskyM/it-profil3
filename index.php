<?php
session_start();

$file = "profile.json";

$data = json_decode(file_get_contents($file), true);
$interests = $data["interests"] ?? [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ADD
    if (isset($_POST["add"])) {

        $newInterest = trim($_POST["interest"]);

        if ($newInterest === "") {
            $_SESSION["msg"] = "Pole nesmí být prázdné.";
        } else {

            $lower = array_map("strtolower", $interests);

            if (in_array(strtolower($newInterest), $lower)) {
                $_SESSION["msg"] = "Tento zájem už existuje.";
            } else {

                $interests[] = $newInterest;

                file_put_contents(
                    $file,
                    json_encode(["interests" => $interests], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );

                $_SESSION["msg"] = "Zájem byl úspěšně přidán.";
            }
        }
    }

    // DELETE
    if (isset($_POST["delete"])) {

        $index = $_POST["delete"];

        if (isset($interests[$index])) {

            unset($interests[$index]);
            $interests = array_values($interests);

            file_put_contents(
                $file,
                json_encode(["interests" => $interests], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $_SESSION["msg"] = "Zájem byl odstraněn.";
        }
    }

    // EDIT
    if (isset($_POST["edit"])) {

        $index = $_POST["index"];
        $newValue = trim($_POST["new_interest"]);

        if ($newValue === "") {

            $_SESSION["msg"] = "Pole nesmí být prázdné.";

        } else {

            $lower = array_map("strtolower", $interests);

            if (in_array(strtolower($newValue), $lower) && strtolower($newValue) !== strtolower($interests[$index])) {

                $_SESSION["msg"] = "Tento zájem už existuje.";

            } else {

                $interests[$index] = $newValue;

                file_put_contents(
                    $file,
                    json_encode(["interests" => $interests], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );

                $_SESSION["msg"] = "Zájem byl upraven.";
            }
        }
    }

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<title>IT Profil</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Můj IT profil</h1>

<?php
if (isset($_SESSION["msg"])) {
    echo "<p class='message'>" . htmlspecialchars($_SESSION["msg"]) . "</p>";
    unset($_SESSION["msg"]);
}
?>

<h2>Zájmy</h2>

<ul>

<?php foreach ($interests as $i => $interest): ?>

<li>

<strong><?= htmlspecialchars($interest) ?></strong>

<form method="POST" style="display:inline;">
<button type="submit" name="delete" value="<?= $i ?>">Smazat</button>
</form>

<form method="POST" style="display:inline;">
<input type="hidden" name="index" value="<?= $i ?>">
<input type="text" name="new_interest" value="<?= htmlspecialchars($interest) ?>">
<button type="submit" name="edit">Upravit</button>
</form>

</li>

<?php endforeach; ?>

</ul>

<h2>Přidat nový zájem</h2>

<form method="POST">

<input type="text" name="interest" placeholder="Nový zájem">

<button type="submit" name="add">Přidat</button>

</form>

</body>
</html>
