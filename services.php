<?php
session_start();
require_once 'config.php';


$stmt = $conn->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $conn->prepare("SELECT * FROM services");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);


function getUniqueCategories($categories) {
    $uniqueCategories = [];
    foreach ($categories as $category) {
        if (!in_array($category['name'], array_column($uniqueCategories, 'name'))) {
            $uniqueCategories[] = $category;
        }
    }
    return $uniqueCategories;
}

$uniqueCategories = getUniqueCategories($categories);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Usługi medyczne</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .filters {
            text-align: center;
            margin: 20px 0;
        }

        .filters ul {
            list-style-type: none;
            padding: 0;
        }

        .filters ul li {
            display: inline;
            margin: 0 10px;
        }

        .filters ul li a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .filters ul li a:hover {
            text-decoration: underline;
        }

        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 20px;
        }

        .service {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 10px;
            padding: 20px;
            width: 300px;
            text-align: center;
        }

        .service h3 {
            margin-top: 0;
            color: #333;
        }

        .service p {
            color: #666;
        }

        .service a {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .service a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Usługi medyczne</h1>

    <div class="filters">
        <h2>Filtruj według kategorii:</h2>
        <ul>
            <?php foreach ($uniqueCategories as $category): ?>
                <li><a href="?category=<?= $category['id'] ?>"><?= $category['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="services">
        <?php
        $category_id = isset($_GET['category']) ? $_GET['category'] : 'all';
        $displayed_services = [];

        foreach ($services as $service):
            if ($category_id !== 'all' && $service['category_id'] != $category_id) {
                continue;
            }
            if (in_array($service['name'], $displayed_services)) {
                continue;
            }
            $displayed_services[] = $service['name'];
            ?>
            <div class="service">
                <h3><?= $service['name'] ?></h3>
                <p>Cena: <?= $service['price'] ?> zł</p>
                <p>Kategoria: <?= $categories[array_search($service['category_id'], array_column($categories, 'id'))]['name'] ?></p>
                <p><?= $service['description'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="script.js"></script>
</body>
</html>
