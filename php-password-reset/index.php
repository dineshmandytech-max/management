<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require __DIR__ . "/recipe-api.php";

$mysqli = require __DIR__ . "/database.php";
$userId = (int) $_SESSION["user_id"];
$result = $mysqli->query("SELECT * FROM user WHERE id = $userId");
$user = $result->fetch_assoc();

$apiKey = "1b2b4b2633b144f39b996666ad1940be";
$query = trim($_GET["query"] ?? "") ?: "";
$api = new RecipeAPI($apiKey);
$apiResults = $api->searchByIngredients($query);
$recipes = array_map(fn($recipe) => new Recipe($recipe), $apiResults);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe List</title>

    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

</head>

<body>

    <h1>Welcome Page</h1>
    <div class="topic-home">
        <p>Hello <?= htmlspecialchars($user["name"]) ?></p>
        <p><a href="logout.php">Log out</a></p>
    </div>

    <h2>Recipe List</h2>
    <form method="GET">
        <input type="text" name="query" value="<?= htmlspecialchars($query) ?>" placeholder="Search by ingredients">
        <button class="btn" type="submit">Search</button>
    </form>

    <br>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Image</th>
                <th>Recipe Name</th>
                <th>Ready Time</th>
                <th>Vegetarian</th>
                <th>Dish Type</th>
                <th>Ingredients</th>
                <th>Summary</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($recipes)): ?>
                <tr>
                    <td colspan="8"> No recipes found. </td>
                </tr>
            <?php else: ?>
                <?php foreach ($recipes as $index => $recipe): ?>

                    <tr>
                        <td> <?= $index + 1 ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($recipe->image()) ?>" alt="<?= htmlspecialchars($recipe->name()) ?>">
                        </td>
                        <td>
                            <?= htmlspecialchars($recipe->name()) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($recipe->readyTime()) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($recipe->vegetarian()) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($recipe->dishType()) ?>
                        </td>
                        <td class="ingredients">
                            <?= htmlspecialchars($recipe->ingredients()) ?>
                        </td>
                        <td class="summary">
                            <?= htmlspecialchars($recipe->summary()) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>

    </table>

</body>

</html>