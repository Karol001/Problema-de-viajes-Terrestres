<!-- Show these admin pages only when the admin is logged in -->
<?php  require '../assets/partials/_admin-check.php';   ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo administrador</title>
            <!-- google fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500&display=swap" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <!-- Font Awesome -->
        <script src="https://kit.fontawesome.com/d8cfbe84b9.js" crossorigin="anonymous"></script>
        <!-- External CSS -->
        <?php 
        require '../assets/styles/admin.php';
        require '../assets/styles/signup.php';
        $page="signup";
    ?>
    </head>
<body>
  
    <?php require '../assets/partials/_admin-header.php';?>

    
            <?php
                if(isset($_GET['signup']))
                {
                    if($_GET['signup'])
                    {
                       
                        echo '<div class="my-0 alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Successful!</strong> Account created successfully
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                    }

                    elseif($_GET['user_exists'])
                        
                        echo '<div class="my-0 alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Username already exists
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                }
            ?>
            <section id="add-admin">
                <div>
                    <div id="signupForm">
                        <h2>Añadir nuevo administrador</h2>
                        <form action="../assets/partials/_handleSignup.php" method="POST">
                            <div>
                                <input type="text" name="firstName" placeholder="Nombres*">
                                <input type="text" name="lastName" placeholder="Apellidos*" required>
                            </div>
                            <div>
                                <input type="text" name="username" placeholder="Usuario*" required>
                            </div>
                            <div>
                                <input id="password" type="password" name="password" placeholder="Contraseña*" required>
                                <span id="passwordErr" class="error"></span>
                            </div>
                            <div>
                                <input id="confPassword" type="password" name="confPassword" placeholder="Confirmar contraseña*" required>
                                <span id="confPassErr" class="error"></span>
                            </div>
                            <button id="signup-btn" type="submit" name="signup">Enviar</button>
                        </form>
                    </div>
                </div>
                <div>
                </div>
            </section>
        </div>
    <script src="../assets/scripts/admin_signup.js">
    </script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>  
</body>
</html>