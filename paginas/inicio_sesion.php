<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesion</title>
    <link rel="stylesheet" href="../css/inicio_sesion.css">
</head>
<body>
    <main class="cont">
        <div class="cont_sec">
            <div class="presentacion">
                <!-- <img src="" alt=""> poner logo-->
            </div>
            <form class="formulario" action="../base_de_datos/sesion.php" method="post">
                <input class="input1" name="name" type="text" placeholder="  Nombre de usuario">
                <input class="input1" name="pass" type="password" placeholder="  Contraseña">
                <button class="boton">Inicio de sesion</button>
            </form>
            <div class="aviso">

            </div>
        </div>
    </main>
</body>
</html>