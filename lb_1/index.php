<?php
$db_driver = "mysql";
$host = "localhost";
$database = "iteh2lb1var4";
$dsn = "$db_driver:host=$host; dbname=$database";
$username = "root";
$password = "";
$dbh = new PDO($dsn, $username, $password);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1</title>
</head>

<body>
    <form action="" method="GET">
        <p>Получить перечень палат, в которых дежурит выбранная медсестра:
            <select name="name">
                <?php
                $sql = 'SELECT DISTINCT `name` FROM nurse';
                foreach ($dbh->query($sql) as $row) {
                    print "<option> $row[name] </option>";
                }
                ?>
            </select>
            <button>Ок</button>
    </form>
    </p>

    <form action="" method="GET">
        <p>Получить перечень медсёстр, выбранного отделения:
            <select name="department">
                <?php
                $sql = 'SELECT DISTINCT department FROM nurse';
                foreach ($dbh->query($sql) as $row) {
                    print "<option> $row[department] </option>";
                }
                ?>
            </select>
            <button>Ок</button>
        </p>
    </form>

    <form action="" method="GET">
        <p>Получить перечень палат в указанную смену:
            <select name="shift">
                <?php
                $sql = 'SELECT DISTINCT shift FROM nurse';
                foreach ($dbh->query($sql) as $row) {
                    print "<option> $row[shift] </option>";
                }
                ?>
            </select>
            <button>Ок</button>
        </p>
    </form>

    <p><b>Добавление медсестры</b></p>
    <form action="" method="GET">
        <p>Введите имя медсестры
            <input required type="text" name="nurseName">
        </p>
        <p>Выберите дату дежурства
            <input required type="date" name="date" />
        </p>
        <p>Выберите отделение
            <select name="department">
                <?php $sql = 'SELECT DISTINCT department FROM nurse';
                foreach ($dbh->query($sql) as $row) {
                    print "<option> $row[department] </option>";
                } ?>
            </select>
        <p>Выберите смену
            <select name="shift">
                <?php $sql = 'SELECT DISTINCT shift FROM nurse';
                foreach ($dbh->query($sql) as $row) {
                    print "<option> $row[shift] </option>";
                } ?>
            </select>
            <button>Ок</button>
        </p>
    </form>

    <p><b>Добавление палаты</b></p>
    <form action="" method="GET">
        <p>Введите название палаты
            <input required type="text" name="wardName">
            <button>Ок</button>
        </p>
    </form>

    <p><b>Назначить выбранной медсестре указанную палату</b></p>
    <form action="" method="GET">
        <p>Выберите медсестру
            <select name="nurseName">
                <?php $sql = 'SELECT `name` FROM nurse';
                foreach ($dbh->query($sql) as $row) {
                    print "<option> $row[name] </option>";
                }
                ?>
            </select>
        </p>
        <p>Выберите палату
            <select name="wardName">
                <?php $sql = 'SELECT `name` FROM ward';
                foreach ($dbh->query($sql) as $row) {
                    print "<option> $row[name] </option>";
                } ?>
            </select>
            <button>Ок</button>
        </p>
    </form>

    <?php
    if (isset($_GET["name"])) {
        $name = $_GET["name"];
        echo "Перечень палат, в которых дежурит медсестра <b>" . $name . "</b>";
        echo "<table border ='1'>";
        echo "<tr>
            <th>WardName</th>
            </tr>";
        $sql = "SELECT c.name FROM (nurse AS a INNER JOIN nurse_ward AS b ON a.id_nurse = b.fid_nurse) INNER JOIN ward AS c ON b.fid_ward = c.id_ward WHERE a.name = :name";

        $sth = $dbh->prepare($sql);
        $sth->execute(array(':name' => $name));

        $timetable = $sth->fetchAll(PDO::FETCH_NUM);
        foreach ($timetable as $row) {
            $WardName = $row[0];
            print "<tr> <td>$WardName</td> </tr>";
        }
    }

    if (isset($_GET["department"])) {
        $department = $_GET["department"];
        echo "Перечень медсёстр отделения <b>" . $department . "</b>";
        echo "<table border ='1'>";
        echo "<tr> <th>NurseName</th> </tr>";
        $sql = "SELECT nurse.name FROM nurse WHERE nurse.department = :department";
        $sth = $dbh->prepare($sql);
        $sth->execute(array(':department' => $department));

        $timetable = $sth->fetchAll(PDO::FETCH_NUM);
        foreach ($timetable as $row) {
            $NurseName = $row[0];
            print "<tr> <td>$NurseName</td> </tr>";
        }
    }

    if (isset($_GET["shift"])) {
        $shift = $_GET["shift"];
        echo "Перечень палат в <b>" . $shift . "</b> смену";
        echo "<table border ='1'>";
        echo "<tr>
             <th>WardName</th> 
             <th>Date</th> 
             <th>NurseName</th> 
             </tr>";
        $sql = "SELECT c.name, a.date, a.name
                    FROM nurse AS a INNER JOIN nurse_ward AS b ON a.id_nurse = b.fid_nurse
                    INNER JOIN ward AS c ON b.fid_ward = c.id_ward
                    WHERE a.shift = :shift";
        $sth = $dbh->prepare($sql);
        $sth->execute(array(':shift' => $shift));

        $timetable = $sth->fetchAll(PDO::FETCH_NUM);
        foreach ($timetable as $row) {
            $WardName = $row[0];
            $Date = $row[1];
            $NurseName = $row[2];
            print "<tr> <td>$WardName</td> <td>$Date</td> <td>$NurseName</td></tr>";
        }
    }

    if (isset($_GET["nurseName"]) && isset($_GET["date"]) && isset($_GET["department"]) && isset($_GET["shift"])) {
        $nurseName = $_GET["nurseName"];
        $date = $_GET["date"];
        $department = $_GET["department"];
        $shift = $_GET["shift"];

        $sql = "INSERT INTO nurse (`name`, `date`, `department`, `shift`) values ( ?, ?, ?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$nurseName, $date, $department, $shift]);
        echo "Данные занесены";
    }

    if (isset($_GET["wardName"])) {
        $wardName = $_GET["wardName"];

        $sql = "INSERT INTO ward (`name`) values ( ? )";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$wardName]);
        echo "Данные занесены";
    }
    if (isset($_GET["nurseName"]) && isset($_GET["wardName"])) {
        $wardName = $_GET["wardName"];
        $nurseName = $_GET["nurseName"];

        $sql = $dbh->prepare("SELECT id_nurse from nurse WHERE `name` = :nurseName");
        $sql->execute(array(':nurseName' => $nurseName));
        $sql = $sql->fetch(PDO::FETCH_BOTH);
        $nurse_id = $sql[0];

        $sql = $dbh->prepare("SELECT id_ward from ward WHERE `name` = :wardName");
        $sql->execute(array(':wardName' => $wardName));
        $sql = $sql->fetch(PDO::FETCH_BOTH);
        $id_ward = $sql[0];

        $sql = "INSERT INTO `nurse_ward` (`fid_nurse`, `fid_ward`) values ( ?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$nurse_id, $id_ward]);
        echo "Данные занесены";
    }
    ?>
</body>

</html>