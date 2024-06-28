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


function wyswietlAnkiete() {
  echo '<h2>Ankieta satysfakcji pacjenta</h2>';
  echo '<form method="post" action="przetworzAnkiete.php">';
  
  echo '<p>Jak oceniasz ogólną jakość opieki w naszej poradni?</p>';
  echo '<select name="ocena_opieki">
          <option value="5">Bardzo dobra</option>
          <option value="4">Dobra</option>
          <option value="3">Przeciętna</option>
          <option value="2">Słaba</option>
          <option value="1">Bardzo słaba</option>
        </select>';

  echo '<p>Czy łatwo było umówić wizytę?</p>';
  echo '<input type="radio" name="latwosc_wizyty" value="tak"> Tak
        <input type="radio" name="latwosc_wizyty" value="nie"> Nie';

  echo '<p>Jakie zmiany zaproponowałbyś/abyś w funkcjonowaniu naszej poradni?</p>';
  echo '<textarea name="sugestie_zmian" rows="4" cols="50"></textarea>';

  echo '<p><input type="submit" value="Wyślij ankietę"></p>';
  echo '</form>';
}


wyswietlAnkiete();
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="navigator.css">
    <link rel="stylesheet" type="text/css" href="mainer.css">
    <title>Przychodnia</title>
    <style>
        .wizyta-container {
            text-align: center;
            padding: 2rem;
            background-color: #fff;
            margin: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        .wizyta-container h1 {
            margin: 0.5rem 0;
        }

        .wizyta-container p {
            color: #666;
        }

        .services, .categories, .doctors, .medicines, .prescriptions, .ask-doctor, .contact, .doctor-panel {
            padding: 2rem;
            background-color: #fff;
            margin: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        .services h2, .categories h2, .doctors h2, .medicines h2, .prescriptions h2, .ask-doctor h2, .contact h2, .doctor-panel h2 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .services {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .service {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            flex: 1 1 calc(33.333% - 2rem);
            margin: 1rem;
            box-sizing: border-box;
        }

        .service:hover {
            transform: translateY(-0.5rem);
        }

        .service h3 {
            margin-top: 0;
            font-size: 1.5rem;
        }

        .service p {
            color: #666;
            line-height: 1.5;
        }

        .categories ul {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .categories li {
            margin: 0.5rem;
        }

        .categories a {
            display: block;
            padding: 0.5rem 1rem;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 0.25rem;
            transition: background 0.3s ease;
        }

        .categories a:hover {
            background: #0056b3;
        }

        .doctor-card {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
            text-align: center;
        }

        .doctor-card:hover {
            transform: translateY(-0.5rem);
        }

        .doctor-card img {
            max-width: 100%;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        .doctor-card h3 {
            margin-top: 0;
            font-size: 1.5rem;
        }

        .doctor-card p {
            color: #666;
            line-height: 1.5;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #007bff;
            color: #fff;
            padding: 1rem;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav ul.sidebar {
            display: none;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #007bff;
            padding-top: 2rem;
        }

        nav ul.sidebar li {
            margin: 1rem 0;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
        }

        nav ul li a:hover {
            background-color: #0056b3;
        }

        .menuBtn {
            display: none;
        }

        @media (max-width: 768px) {
            .hideOnMobile {
                display: none;
            }

            .menuBtn {
                display: block;
            }
        }

        .wizyta-container {
                text-align: center;
                padding: 2rem;
                background-color: #fff;
                margin: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            }

            .wizyta-container h1 {
                margin: 0.5rem 0;
            }

            .wizyta-container p {
                color: #666;
            }

            .services, .categories, .doctors, .medicines, .prescriptions, .ask-doctor, .contact, .doctor-panel {
                padding: 2rem;
                background-color: #fff;
                margin: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            }

            .services h2, .categories h2, .doctors h2, .medicines h2, .prescriptions h2, .ask-doctor h2, .contact h2, .doctor-panel h2 {
                text-align: center;
                margin-bottom: 2rem;
            }

            .services {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .service {
                background: #fff;
                padding: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
                transition: transform 0.3s ease;
                flex: 1 1 calc(33.333% - 2rem);
                margin: 1rem;
                box-sizing: border-box;
            }

            .service:hover {
                transform: translateY(-0.5rem);
            }

            .service h3 {
                margin-top: 0;
                font-size: 1.5rem;
            }

            .service p {
                color: #666;
                line-height: 1.5;
            }

            .categories ul {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .categories li {
                margin: 0.5rem;
            }

            .categories a {
                display: block;
                padding: 0.5rem 1rem;
                background: #007bff;
                color: #fff;
                text-decoration: none;
                border-radius: 0.25rem;
                transition: background 0.3s ease;
            }

            .categories a:hover {
                background: #0056b3;
            }

            .doctor-card {
                background: #fff;
                padding: 2rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
                transition: transform 0.3s ease;
                text-align: center;
            }

            .doctor-card:hover {
                transform: translateY(-0.5rem);
            }

            .doctor-card img {
                max-width: 100%;
                border-radius: 50%;
                margin-bottom: 1rem;
            }

            .doctor-card h3 {
                margin-top: 0;
                font-size: 1.5rem;
            }

            .doctor-card p {
                color: #666;
                line-height: 1.5;
            }
            .doctor-container {
                border: 1px solid #ccc;
                padding: 10px;
                margin-bottom: 10px;
            }

            .aktualnosci {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f5f5f5;
                border: 1px solid #ddd;
                border-radius: 5px;
                }

                .aktualnosci h2 {
                color: #333;
                font-size: 24px;
                margin-bottom: 20px;
                }

                .aktualnosci ul {
                list-style-type: none;
                padding: 0;
                }

                .aktualnosci li {
                margin-bottom: 20px;
                }

                .aktualnosci h3 {
                color: #555;
                font-size: 18px;
                margin-bottom: 10px;
                }

                .aktualnosci p {
                color: #777;
                line-height: 1.5;
                }
    
    </style>
        
</head>
<body>
    <nav>
        <ul class="sidebar">
            <li onclick=closeSidebar()><a href="#home"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
            <li><a href="accountPage.php">Konto</a></li>
            <li><a href="#wizyty">Umów wizytę</a></li>
            <li><a href="lekarze.php">Lekarze</a></li>
            <li><a href="#medicine">Lekarstwa</a></li>
            <li><a href="#receipt">E-recepta</a></li>
            <li><a href="#question">Zapytaj lekarza</a></li>
            <li><a href="services.php">Usługi</a></li>
            <li><a href="#contact">Kontakt</a></li>
        </ul>
        <ul>
            <li><a href="#home">Home</a></li>
            <li class="hideOnMobile"><a href="accountPage.php">Konto</a></li>
            <li class="hideOnMobile"><a href="#wizyty">Umów wizytę</a></li>
            <li class="hideOnMobile"><a href="lekarze.php">Lekarze</a></li>
            <li class="hideOnMobile"><a href="#medicine">Lekarstwa</a></li>
            <li class="hideOnMobile"><a href="#receipt">E-recepta</a></li>
            <li class="hideOnMobile"><a href="#question">Zapytaj lekarza</a></li>
            <li class="hideOnMobile"><a href="services.php">Usługi</a></li>
            <li class="hideOnMobile"><a href="#contact">Kontakt</a></li>
            <li class="hideOnMobile"><a href="adminPanel.php">Dla lekarza</a></li>
            <li onclick=showSidebar() class="menuBtn"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
        </ul>
    </nav>
    <main>
        <div class='wizyta-container'>
            <h1 class='first'>Szukasz lekarza online?</h1>
            <h1 class='second'>Umów się na wizytę</h1>
            <h3>Szybko, bezpiecznie, z gwarancją pracy wykonanej przez ekspertów.</h3>
            <p>Wypełnij <a href="#">formularz medyczny</a> lub <a href="#">umów teleporadę</a> wybierając dostępnego <a href="lekarze.php">lekarza</a> i otrzymaj <a href="#receipt">receptę</a> i <a href="#">zwolnienie</a> bez wychodzenia z domu!</p>
        </div>
    </main>
    <section class="services">
        <h2>Nasze usługi</h2>
        <?php
        $displayed_services = [];
        foreach ($services as $service):
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
    </section>
    <section class="categories">
        <h2>Kategorie usług</h2>
        <ul>
            <?php foreach ($uniqueCategories as $category): ?>
                <li><a href="services.php?category=<?= $category['id'] ?>"><?= $category['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="doctors">
        <h2>Nasi Lekarze</h2>
        <div class="doctor-list">
            <?php
            require_once 'klasy.php';

            $count = 0;

            foreach ($array as $doktor) {
                if ($count >= 5) {
                    break;
                }
                $licenseNumber = $doktor->numerLegitymacji();
                ?>
                <div class="doctor-container">
                    <h3>
                        <a href="lekarz.php?id=<?php echo $licenseNumber; ?>">
                            <?php echo $doktor->pelneNazwisko(); ?>
                        </a>
                    </h3>
                    <p><strong>Specjalizacja:</strong> <?php echo $doktor->informacjeOSpecjalizacji(); ?></p>
                    <p><strong>Numer Legitymacji:</strong> <?php echo $licenseNumber; ?></p>
                    <p><strong>Usługi:</strong> <?php echo $doktor->informacjeOUslugach(); ?></p>
                    <?php if (isset($doktor->email)) { ?>
                        <p><strong>Email:</strong> <?php echo $doktor->email; ?></p>
                    <?php } ?>
                </div>
                <?php
                $count++;
            }
            ?>
        </div>
    </section>
    <section class='aktualnosci'>
        <h2>Aktualności</h2>
            <ul>
                <li>
                <h3>Zmiana godzin otwarcia w dniu 2 maja</h3>
                <p>W czwartek, 2 maja, poradnia będzie czynna w godzinach 8:00 - 14:00.</p>
                </li>
                <li>
                <h3>Dni wolne od pracy w maju</h3>
                <p>Informujemy, że w maju poradnia będzie nieczynna w następujących dniach:</p>
                <ul>
                    <li>1 maja (Święto Pracy)</li>
                    <li>3 maja (Święto Konstytucji 3 Maja)</li>
                </ul>
                </li>
                <li>
                <h3>Warsztaty dla rodziców</h3>
                <p>W dniu 15 maja odbędą się warsztaty dla rodziców na temat "Jak radzić sobie ze stresem u dzieci". Zapisy przyjmujemy do 10 maja.</p>
                </li>
            </ul>
    </section>

    <section class="ask-doctor">
        <h2>Zapytaj lekarza</h2>
        
    </section>
    <section class="contact">
        <h2>Kontakt</h2>
        
    </section>


    <script>
        function showSidebar(){
            const sidebar=document.querySelector('.sidebar')
            sidebar.style.display='flex'
        }
        function closeSidebar(){
            const sidebar=document.querySelector('.sidebar')
            sidebar.style.display='none'
        }
    </script>
</body>
</html>
