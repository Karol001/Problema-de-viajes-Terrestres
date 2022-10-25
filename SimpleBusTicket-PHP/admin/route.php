
<?php  require '../assets/partials/_admin-check.php';   ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rutas</title>
        <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d8cfbe84b9.js" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <!-- CSS -->
    <?php 
        require '../assets/styles/admin.php';
        require '../assets/styles/admin-options.php';
        $page="route";
    ?>
</head>
<body>
    
    <?php require '../assets/partials/_admin-header.php';?>

    <?php
      
        if($loggedIn && $_SERVER["REQUEST_METHOD"] == "POST")
        {
            if(isset($_POST["submit"]))
            {
             
                $viaCities = strtoupper($_POST["viaCities"]);
                $deptime = $_POST["dep_time"];
                $depdate = $_POST["dep_date"];
                $busno = $_POST["busno"];
                $route_exists = exist_routes($conn,$viaCities,$depdate, $deptime);
                $route_added = false;
        
                if(!$route_exists)
                {
                   
                    $sql = "INSERT INTO `routes` (`route_cities`,
                     `bus_no`, 
                     `route_dep_date`,
                     `route_dep_time`, `route_created`) VALUES ('$viaCities','$busno', '$depdate','$deptime',  current_timestamp());";
                    $result = mysqli_query($conn, $sql);
                    
                    
                    $autoInc_id = mysqli_insert_id($conn);
                     
                    if($autoInc_id)
                    {
                        $code = rand(1,99999);
                        
                        $route_id = "RT-".$code.$autoInc_id;
                        
                        $query = "UPDATE `routes` SET `route_id` = '$route_id' WHERE `routes`.`id` = $autoInc_id;";
                        $queryResult = mysqli_query($conn, $query);
                        if(!$queryResult)
                            echo "Not Working";
                    }
                    
                    if($result)
                    {
                        $route_added = true;
                       
                        bus_assign($conn, $busno);
                    }
                }
    
                if($route_added)
                {
                    
                    echo '<div class="my-0 alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Correcto!</strong> Ruta creada
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                else{
                    
                   
                    echo '<div class="my-0 alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Ruta existente
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
            }
            if(isset($_POST["edit"]))
            {
                
                $viaCities = strtoupper($_POST["viaCities"]);
                $id = $_POST["id"];
                $deptime = $_POST["dep_time"];
                $depdate = $_POST["dep_date"];
                $busno = $_POST["busno"];
                $oldBusNo = $_POST["old-busno"];

                $id_if_route_exists = exist_routes($conn,$viaCities,$depdate,$deptime);
           
                if(!$id_if_route_exists || $id == $id_if_route_exists)
                {
                    $updateSql = "UPDATE `routes` SET
                    `route_cities` = '$viaCities',
                    `bus_no`='$busno',
                    `route_dep_date` = '$depdate',
                    `route_dep_time` = '$deptime'";
            
                    $updateResult = mysqli_query($conn, $updateSql);
                    $rowsAffected = mysqli_affected_rows($conn);
                    
                    $messageStatus = "danger";
                    $messageInfo = "";
                    $messageHeading = "Error!";
    
                    if(!$rowsAffected)
                    {
                        $messageInfo = "No se encontraron cambios!";
                    }
    
                    elseif($updateResult)
                    {
                        if($oldBusNo != $busno)
                        {
                            bus_assign($conn,$busno);
                            bus_free($conn, $oldBusNo);
                        }
                        
                        $messageStatus = "Correcto";
                        $messageHeading = "Correctamente!";
                        $messageInfo = "Ruta creada correctamente";
                    }
                    else{
                        
                        $messageInfo = "Su solicitud no pudo ser procesada debido a problemas técnicos de nuestra parte.";
                    }
                    
                    
                    echo '<div class="my-0 alert alert-'.$messageStatus.' alert-dismissible fade show" role="alert">
                    <strong>'.$messageHeading.'</strong> '.$messageInfo.'
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                else 
                {
                    
                    echo '<div class="my-0 alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Route details already exists
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }

            }
            if(isset($_POST["delete"]))
            {
                $id = $_POST["id"];
                $busno_toFree = busno_from_routeid($conn, $id);
                $deleteSql = "DELETE FROM `routes` WHERE `routes`.`id` = $id";
                $deleteResult = mysqli_query($conn, $deleteSql);
                $rowsAffected = mysqli_affected_rows($conn);
                $messageStatus = "danger";
                $messageInfo = "";
                $messageHeading = "Error!";

                if(!$rowsAffected)
                {
                    $messageInfo = "El registro no existe";
                }

                elseif($deleteResult)
                {   
                    
                    $messageStatus = "Correcto";
                    $messageInfo = "La ruta a sido eliminada";
                    $messageHeading = "Correctamente!";
                    bus_free($conn, $busno_toFree);
                }
                else{
                    $messageInfo = "Su solicitud no pudo ser procesada debido a problemas técnicos de nuestra parte.";
                }
                echo '<div class="my-0 alert alert-'.$messageStatus.' alert-dismissible fade show" role="alert">
                <strong>'.$messageHeading.'</strong> '.$messageInfo.'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
        ?>    
        <?php
            $resultSql = "SELECT * FROM `routes` ORDER BY route_created DESC";
                            
            $resultSqlResult = mysqli_query($conn, $resultSql);
            if(!mysqli_num_rows($resultSqlResult)){ ?>
                <div class="container mt-4">
                    <div id="noRoutes" class="alert alert-dark " role="alert">
                        <h1 class="alert-heading">¡¡No se han encontrado rutas!!</h1>
                        <p class="fw-light">Crea una ruta</p>
                        <hr>
                        <div id="addRouteAlert" class="alert alert-success" role="alert">
                                Click en <button id="add-button" class="button btn-sm"type="button"data-bs-toggle="modal" data-bs-target="#addModal">ADD <i class="fas fa-plus"></i></button> Crea una ruta!
                        </div>
                    </div>
                </div>
            <?php }
            else { ?>
                <section id="route">
                    <div id="head">
                        <h4>Rutas de viaje</h4>
                    </div>
                    <div id="route-results">
                        <div>
                            <button id="add-button" class="button btn-sm"type="button"data-bs-toggle="modal" data-bs-target="#addModal">Añadir ruta <i class="fas fa-plus"></i></button>
                        </div>
                        <table class="table table-hover table-bordered">
                            <thead>
                                <th>ID</th>
                                <th>Ciudades</th>
                                <th>Bus</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Actions</th>
                            </thead>
                            <?php
                                while($row = mysqli_fetch_assoc($resultSqlResult))
                                {
                                     
                                    $id = $row["id"];
                                    $route_id = $row["route_id"];
                                    $route_cities = $row["route_cities"];
                                    $route_dep_time = $row["route_dep_time"];
                                    $route_dep_date = $row["route_dep_date"];
                                    $bus_no = $row["bus_no"];
                                        ?>
                                    <tr>
                                        <td>
                                            <?php 
                                                echo $route_id;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                echo $route_cities;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                echo $bus_no;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                echo $route_dep_date;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                echo $route_dep_time;
                                            ?>
                                        </td>
                                        <td>
                                            <button class="button edit-button " data-link="<?php echo $_SERVER['REQUEST_URI']; ?>" data-id="<?php 
                                                echo $id;?>" data-cities="<?php 
                                                echo $route_cities;?>" data-cost="<?php 
                                                echo $route_dep_date;
                                            ?>" data-time="<?php 
                                            echo $route_dep_time;
                                            ?>" data-busno="<?php 
                                            echo $bus_no;
                                            ?>"
                                            >Editar</button>
                                            <button class="button delete-button" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php 
                                                echo $id;?>">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php 
                                }
                            ?>
                        </table>
                    </div>
                    </section>
                <?php  }
            ?>
            </div>
    </main>
            <?php
                $busSql = "Select * from buses where bus_assigned=0";
                $resultBusSql = mysqli_query($conn, $busSql);
                $arr = array();
                while($row = mysqli_fetch_assoc($resultBusSql))
                    $arr[] = $row;
                $busJson = json_encode($arr);
            ?>
           
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Añadir ruta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addRouteForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                            <div class="mb-3">
                                    <label for="viaCities" class="form-label">Ciudades</label>
                                <input type="text" class="form-control" id="viaCities" name="viaCities" placeholder="Separadas por coma" required>
                                <span id="error">

                                </span>
                            </div>
                            <input type="hidden" id="busJson" name="busJson" value='<?php echo $busJson; ?>'>
                            <div class="mb-3">
                                <label for="busno" class="form-label">Número de bus</label>
                                <div class="searchBus">
                                    <input type="text" class="form-control  busnoInput" id="busno" name="busno" required>
                                    <div class="sugg">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Fecha</label>
                                <input type="date" name="dep_date" id="date" min="<?php 
                                date_default_timezone_set("Asia/Kolkata");
                                echo date("Y-m-d");?>" value="
                                <?php 
                                echo date("Y-m-d");
                                ?>
                                " required>
                            </div>
                            <div class="mb-3">
                                <label for="time" class="form-label">Hora</label>
                                <input type="time" name="dep_time" id="time" min="
                                <?php
                                    echo date("H:i");
                                ?>
                                " required>
                            </div>
                            <button type="submit" class="btn btn-success" name="submit">Crear ruta</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                    </div>
                </div>
        </div>
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-exclamation-circle"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h2 class="text-center pb-4">
                    Esta seguro?
                </h2>
                <p>
                    Realmente quiere eliminar este pasajero? <strong>Esta acción no se puede deshacer</strong>
                </p>
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="delete-form"  method="POST">
                    <input id="delete-id" type="hidden" name="id">
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="delete-form" name="delete" class="btn btn-danger">Eliminar</button>
            </div>
            </div>
        </div>
        </div>
    <!-- External JS -->
    <script src="../assets/scripts/admin_routes.js"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</body>
</html>