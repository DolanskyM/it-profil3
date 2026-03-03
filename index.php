<?php
$file = "profile.json";
$profileData = [];

if (file_exists($file)) {
    $json = file_get_contents($file);
    $profileData = json_decode($json, true);
}

if (!isset($profileData["interests"])) {
    $profileData["interests"] = [];
}

$message = "";
$messageType = "";

if (isset($_POST["new_interest"])) {

    $newInterest = trim($_POST["new_interest"]);

    if (empty($newInterest)) {
        $message = "Pole nesmí být prázdné.";
        $messageType = "error";
    } else {

        $lowerInterests = array_map('strtolower', $profileData["interests"]);

        if (in_array(strtolower($newInterest), $lowerInterests)) {
            $message = "Tento zájem už existuje.";
            $messageType = "error";
        } else {

            $profileData["interests"][] = $newInterest;

            file_put_contents($file, json_encode($profileData, JSON_PRETTY_PRINT));

            $message = "Zájem byl úspěšně přidán.";
            $messageType = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>IT Profil 4.0</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Můj IT Profil</h1>

<h2>Zájmy</h2>
<ul>
    <?php foreach ($profileData["interests"] as $interest): ?>
        <li><?php echo htmlspecialchars($interest); ?></li>
    <?php endforeach; ?>
</ul>

<!-- Výpis hlášky -->
<?php if (!empty($message)): ?>
    <p class="<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </p>
<?php endif; ?>

<!-- Formulář -->
<form method="POST">
    <input type="text" name="new_interest" required>
    <button type="submit">Přidat zájem</button>
</form>

</body>
</html>
