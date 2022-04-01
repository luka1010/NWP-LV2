<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php

    $columnName = function ($value) {
        return $value->name;
    };
    $db = "lv1";
    $dir = "backup/$db";

    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            die("<p>Unable to create directory for file upload.</p></body></html>");
        }
    }

    $time = time();
    $dbc = mysqli_connect("localhost", "root", "", $db)
        or die("<p>Unable to connect to the '$db' database.</p></body></html>");

    $files = [];
    $rows = mysqli_query($dbc, "SHOW TABLES");

    if (mysqli_num_rows($rows) > 0) {
        while (list($table) = mysqli_fetch_array($rows, MYSQLI_NUM)) {
            //go through tables
            $q = "SELECT * FROM $table";
            $columns = array_map($columnName, $dbc->query($q)->fetch_fields());

            $tableRows = mysqli_query($dbc, $q);

            if (mysqli_num_rows($tableRows) > 0) {
                //go through tableRows and insert them into .txt file
                $fileName = "{$table}_{$time}";
                if ($fp = fopen("$dir/$fileName.txt", "w9")) {
                    array_push($files, $fileName);
                    while ($row = mysqli_fetch_array($tableRows, MYSQLI_NUM)) {

                        $rowText = "INSERT INTO $table (";

                        //add column names as ATTRIBUTES in INSERT query
                        for ($i = 0; $i < count($columns); $i++) {
                            if ($i + 1 != count($columns)) {
                                $rowText .= "$columns[$i], ";
                            } else {
                                $rowText .= "$columns[$i]";
                            }
                        }

                        $rowText .= ") VALUES (";

                        //add row values as VALUES in INSERT query
                        for ($i = 0; $i < count($row); $i++) {
                            if ($i + 1 != count($row)) {
                                $rowText .= "'$row[$i]', ";
                            } else {
                                $rowText .= "'$row[$i]'";
                            }
                        }
                        $rowText .= ");\n";

                        //execute query and write into .txt file
                        fwrite($fp, $rowText);
                    }
                    fclose($fp);

                    echo "<p>Table '$table' has been saved.</p>";

                    //compress files
                    if ($fp = gzopen ("$dir/" . $fileName . "sql.gz", 'w9')) {
                        $content = file_get_contents("backup/$db/$fileName.txt");
                        gzwrite($fp, $content);
                        unlink("backup/$db/$fileName.txt");
                        gzclose($fp);

                        echo "<p>Table '$table' has been compressed.</p>";
                    } else {
                        echo "<p>Table '$table' compressing failed.</p>";
                    }
                } else {
                    echo "<p>Unable to open $dir/{$table}_{$time}.txt file.</p>";
                    break;
                }
            }
        }
    } else {
        echo "<p>Database $db has no tables.</p>";
    }
    ?>
</body>
</html>