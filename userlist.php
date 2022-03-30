<?php

$user_output = isset($_GET['output'])?strtolower($_GET['output']):'';
$user_filters = isset($_GET['filters'])?strtolower($_GET['filters']):'';

// should we output data into a particular format?
if($user_output!=='') {
  $output = [];

  // initiate a MySQL connection because a query was made
  if(extension_loaded('mysqli')) {
    $mysqli = @new mysqli('', 'root', '9LKH+Nt+*R%7gpYB', 'parentzone', 3306);
    if($mysqli->connect_error)
      die('The MySQL object was unable to connect: '.$mysqli->connect_error);

    $output = [
      'metadata' => [
        'results'    => 0,
        'totalusers' => 0,
        'generated'  => time(),
        'columns'    => [],
      ],
      'results' => [],
    ];

    // dynamically collect the columns from the registrants table
    $result = $mysqli->query('SHOW COLUMNS FROM registrants;');
    $columns = [];
    while($row = $result->fetch_assoc())
      $columns[]= $row['Field'];
    $output['metadata']['columns'] = $columns;

    // query to get the number of registered users, i used array_values because
    // fetch_assoc returns the result with index 'COUNT(*)' which i think is uglier
    $result = $mysqli->query('SELECT COUNT(*) FROM registrants;');
    $output['metadata']['totalusers'] = array_values($result->fetch_assoc())[0];


    // attempt to parse the insanity of the filters and 
    // reconstruct an SQL query equivalent all with string escaping
    $sql_query = [];

    $user_filters = urldecode($user_filters);
    $user_filters = explode('&', $user_filters);
    for($i=0;$i<count($user_filters);$i++) {
      $user_filters[$i] = explode('=', $user_filters[$i], 2);
      if(in_array(@$user_filters[$i][0], $columns)) {
        $user_filters[$i][1] = urldecode($user_filters[$i][1]);
        // escape any sql attacks
        $user_filters[$i][1] = $mysqli->real_escape_string($user_filters[$i][1]);
        // escape percent wildcards, use it as a literal character
        $user_filters[$i][1] = str_replace('%', '\%', $user_filters[$i][1]);
        $sql_query[]= sprintf('%s LIKE \'%%%s%%\'', $user_filters[$i][0], $user_filters[$i][1]);
      }
    }
    $sql_query = implode(' AND ', $sql_query);

    // if there is a filter query being used, let's use it and get data from the database
    if(strlen($sql_query)>0) {
      // dynamically collect the columns from the registrants database
      $result = $mysqli->query('SELECT * FROM registrants WHERE '.$sql_query.';');
      $output['metadata']['results'] = $result->num_rows;
      $output['metadata']['rawquery'] = 'SELECT * FROM registrants WHERE '.$sql_query.';';
      while($row = $result->fetch_assoc())
        $output['results'][] = array_values($row);
    }
  }

  // after collecting all the data, deal with outputting
  switch($user_output) {
    case 'json':
      header('Content-type: application/json');
      echo json_encode($output);
      break;

    case 'csv':
      // made a quick and dirty csv escape function
      // for fields containing commas and quotes
      $escape = function($data = '') {
        if(strstr($data, ',')!==false || strstr($data, '"')!==false)
          $data = '"'.str_replace('"', '""', $data).'"';
        return $data;
      };

      // force download of the contents into a file named "export.csv"
      header('Content-type: text/csv');
      header('Content-Transfer-Encoding: Binary');
      header('Content-disposition: attachment; filename="export.csv"');

      // print the column names at the top
      printf("%s\n", implode(',', $output['metadata']['columns']));

      // process the result data and escape commas and quotes
      for($i=0;$i<count($output['results']);$i++) {
        $output['results'][$i] = array_values($output['results'][$i]);

        for($j=0;$j<count($output['results'][$i]);$j++)
          // using null coalescence because sometimes data is null and causes errors
          $output['results'][$i][$j] = $escape($output['results'][$i][$j]??'');

        echo implode(',', $output['results'][$i]);
        echo "\n";
      }
      break;
  }
  die();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Parent Zone User List</title>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="userlist.css"/>
  <script src="userlist.js"></script>
</head>
<body>

<div id="filters">
  <input type="button" value="Reset Filters" disabled/>
  <input type="button" value="Export as CSV" disabled/>
</div>

<div id="mainusers">Loading table columns, please wait...</div>
<div id="status">...</div>

</body>
</html>
