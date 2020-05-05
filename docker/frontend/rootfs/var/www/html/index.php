<?php

$hostname = gethostname();
$backend_url = $_ENV['BACKEND_HOST'];


function call_api($method, $endpoint, $keyname, $value) {
    $url = "http://". $_ENV['BACKEND_HOST'] . "/${endpoint}";
    $data = [$keyname => $value];

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => $method,
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

# ADDING DATA
if (isset($_POST['task'])) {
    call_api('POST', 'add', 'task', $_POST['task']);
}

# DELETING DATA
if (isset($_GET['delete'])) {
    call_api('DELETE', 'delete', 'id', $_GET['delete']);
}

# RETRIEVING DATA
$backend_tasks = json_decode(file_get_contents("http://${backend_url}/tasks"), true);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>PowerApp - Your Awesome List App</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.min.css">
        <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    </head>
    <body>
        <section class="section">
            <!-- <div class="hero-body"> -->
                <div class="container">
                    <h1 class="title">PowerApp</h1>
                    <h2 class="subtitle">Your Awesome List App</h2>
                </div>
            <!-- </div> -->
        </section>
        <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <div class="columns">
                        <div class="column">
                            <!-- Intro -->
                            <!-- <div id="intro">
                                <p>This page was served by <?php echo $hostname ?></p>
                            </div> -->

                            <!-- Form -->
                            <div id="form">
                                <form action="index.php" method="post">
                                    <p>Add new a item here:</p>
                                    <div class="field">
                                        <div class="control">
                                            <input class="input is-small" type="text" name="task"><br />
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="control">
                                            <input class="button is-link" type="submit">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="column">
                            <!-- List -->
                            <div id="list">
                                <table class="table is-hoverable is-bordered is-fullwidth is-striped">
                                <?php
                                    if (!empty($backend_tasks['tasks'])) {
                                        foreach ($backend_tasks['tasks'] as $key => $value) {
                                            echo "<tr><td>${value['task']}</td><td><a href=\"index.php?delete=${value['_id']}\">delete</a></td></tr>";
                                        }
                                    } else {
                                        echo "<tr><td>No data to show, yet!</tr></td>";
                                    }
                                ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <table class="table is-bordered is-striped">
                        <tr class="is-uppercase has-text-weight-bold"><td>Container Data</td><td></td></tr>
                        <tr><td>HostName</td><td><?php echo $hostname ?></td></tr>
                    </table>
                </div>
                <div class="column">
                    <figure class="image is-16by9">
                        <a href="https://sighup.io"><img src="images/sighup.png" /></a>
                    <figure>
                </div>
            </div>
        </div>
        </section>
    </body>
</html>


