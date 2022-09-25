<?php
    ######################
    # Postulante: Anders RM
    # Fecha: 20/09/2022
    # Telefono: +51970502429
    # Corre: PHP
    ######################

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('memory_limit', '-1');
    error_reporting(E_ALL);

    require_once "../vendor/autoload.php";

	header('Content-type: application/json;');
	$url = './100000_movies.json'; // path to your JSON file
	$data = file_get_contents($url); // put the contents of the file into a variable

    $starttime = microtime(true);
    
    $redis = new Predis\Client();
    $cachedEntry = $redis->get('movies');
    if ($cachedEntry){
        echo 'redis';
        $movies = json_decode($cachedEntry,true);
        $newArray = [];
        foreach ($movies as $movie){
            foreach ($movie['genre'] as $genre){
                $newArray[$genre]['name']=$genre;
                $newArray[$genre]['total_movies']=$newArray[$genre]['total_movies']+1;
                $newArray[$genre]['total_minutes'] += $movie['runtime'];
                $newArray[$genre]['average_minutes'] = $newArray[$genre]['total_minutes']/$newArray[$genre]['total_movies'];
                $newArray[$genre]['movies'][] = $movie;
            }
        }
    }else{
        $movies = json_decode($data, true);
        $newArray = [];
        foreach ($movies as $movie){
            foreach ($movie['genre'] as $genre){
                $newArray[$genre]['name']=$genre;
                $newArray[$genre]['total_movies']=$newArray[$genre]['total_movies']+1;
                $newArray[$genre]['total_minutes'] += $movie['runtime'];
                $newArray[$genre]['average_minutes'] = $newArray[$genre]['total_minutes']/$newArray[$genre]['total_movies'];
                $newArray[$genre]['movies'][] = $movie;
            }
        }

        $redis->set('movies', $data);
        $redis->expire('movies', 5);
    }
    
    $endtime = microtime(true);
	/*Ïmprime contenido del Json*/
	//print_r('Original Json'.json_encode($movies, JSON_PRETTY_PRINT));

	/*Ïmprime contenido del nuevo Array*/
    //                                                 PHP7.4                               PHP 8.1
    // Initial Average time:                         0.00034s                               0.00031s
    // After refactoring:                            0.00021s                               0.00020s
    // Replacing ForEachLoops -> Do While:           0.00052s                               0.00046s
    // Replacing ForEachLoops -> For Loops:          0.00034s                               0.00032s
    // Using predis/predis library:                  0.00041s                               0.00024s + After second call.
    // Using Threads library:                        0.00121s                               0.00118s + Risky in production.
    // Avoid decoding json and only string ops.      0.00034s, but good with movies_long.json(+25000 movies), only uses 1 while.
    echo $endtime - $starttime;
    print_r($newArray);

?>

