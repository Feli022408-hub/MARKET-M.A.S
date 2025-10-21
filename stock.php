<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
    exit;
}

require_once("config/db.php");
require_once("config/conexion.php");
include("funciones.php");
$title = "Inventario | Market M.A.S";
$active_productos = "";

// Procesar formulario de agregar stock
if (isset($_POST['reference']) && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    $reference = mysqli_real_escape_string($con, strip_tags($_POST["reference"], ENT_QUOTES));
    $id_producto = intval($_POST['id_product_add']);
    $user_id = $_SESSION['user_id'];
    $firstname = $_SESSION['firstname'];
    $nota = "$firstname agregó $quantity producto(s) al inventario";
    $fecha = date("Y-m-d H:i:s");
    $historial = guardar_historial($id_producto, $user_id, $fecha, $nota, $reference, $quantity);
    $update = agregar_stock($id_producto, $quantity);
    if ($update && $historial) {
        echo "<script>alert('Stock añadido exitosamente');</script>";
    } else {
        echo "<script>alert('Error al añadir stock: " . mysqli_error($con) . "');</script>";
    }
    header("Location: stock.php");
    exit;
}

// Procesar formulario de eliminar stock
if (isset($_POST['reference_remove']) && isset($_POST['quantity_remove'])) {
    $quantity = intval($_POST['quantity_remove']);
    $reference = mysqli_real_escape_string($con, strip_tags($_POST["reference_remove"], ENT_QUOTES));
    $id_producto = intval($_POST['id_product_remove']);
    $user_id = $_SESSION['user_id'];
    $firstname = $_SESSION['firstname'];
    $nota = "$firstname eliminó $quantity producto(s) del inventario";
    $fecha = date("Y-m-d H:i:s");
    $historial = guardar_historial($id_producto, $user_id, $fecha, $nota, $reference, $quantity);
    $update = eliminar_stock($id_producto, $quantity);
    if ($update && $historial) {
        echo "<script>alert('Stock eliminado exitosamente');</script>";
    } else {
        echo "<script>alert('Error al eliminar stock: " . mysqli_error($con) . "');</script>";
    }
    header("Location: stock.php");
    exit;
}

// Procesar eliminación
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM products WHERE id_producto = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if ($result) {
        header("Location: stock.php");
    } else {
        echo "<script>alert('Error al eliminar: " . mysqli_error($con) . "');</script>";
        header("Location: stock.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("head.php"); ?>
    <style>
        .table-hover tbody tr:hover {
            background-color: transparent;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="panel panel-success">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#nuevoProducto">
                        <span class="glyphicon glyphicon-plus"></span> Agregar
                    </button>
                </div>
                <h4><i class="glyphicon glyphicon-search"></i> Consultar inventario</h4>
            </div>
            <div class="panel-body">
                <?php include("modal/registro_productos.php"); ?>
                <?php include("modal/editar_productos.php"); ?>
                <?php include("modal/agregar_stock.php"); ?>
                <?php include("modal/eliminar_stock.php"); ?>

                <form class="form-horizontal" role="form" id="datos">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Filtrar por código o nombre</label>
                            <input type="text" class="form-control" id="q" placeholder="Código o nombre del producto" onkeyup="load(1);">
                        </div>
                        <div class="col-md-4">
                            <label>Filtrar por categoría</label>
                            <select class="form-control" name="id_categoria" id="id_categoria" onchange="load(1);">
                                <option value="">Selecciona una categoría</option>
                                <?php 
                                $query_categoria = mysqli_query($con, "SELECT * FROM categorias ORDER BY nombre_categoria");
                                while ($rw = mysqli_fetch_array($query_categoria)) {
                                    ?>
                                    <option value="<?php echo $rw['id_categoria']; ?>"><?php echo $rw['nombre_categoria']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 text-center">
                            <span id="loader"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row-fluid">
                        <div id="resultados">
                            <p class="text-center">Cargando inventario... Verifica la consola (F12).</p>
                        </div>
                        <div id="resultados_ajax_productos"></div> <!-- Contenedor para mensajes del formulario -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <?php include("footer.php"); ?>
    <script type="text/javascript" src="js/productos.js"></script>
</body>
</html>
<script>
function eliminar(id) {
    var q = $("#q").val();
    var id_categoria = $("#id_categoria").val();
    if (confirm("Realmente deseas eliminar el producto")) {
        location.replace('stock.php?delete=' + id);
    }
}

$(document).ready(function() {
    <?php if (isset($_GET['delete'])) { ?>
        eliminar(<?php echo intval($_GET['delete']); ?>);
    <?php } ?>
});

$("#guardar_producto").submit(function(event) {
    $('#guardar_datos').attr("disabled", true);
    var parametros = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "ajax/nuevo_producto.php",
        data: parametros,
        beforeSend: function(objeto) {
            $("#resultados_ajax_productos").html("Mensaje: Cargando...");
        },
        success: function(datos) {
            $("#resultados_ajax_productos").html(datos);
            $('#guardar_datos').attr("disabled", false);
            if (datos.indexOf("alert-success") !== -1) { // Si hay éxito
                load(1); // Recargar tabla
                $('#nuevoProducto').modal('hide'); // Cerrar modal
                $('.modal-backdrop').remove(); // Forzar eliminación del backdrop
            }
        },
        error: function(xhr, status, error) {
            $("#resultados_ajax_productos").html("<div class='alert alert-danger'>Error: " + error + "</div>");
            $('#guardar_datos').attr("disabled", false);
            console.log("Error AJAX: ", status, error, xhr.responseText);
        }
    });
    event.preventDefault();
});

function load(page) {
    var q = $("#q").val();
    var id_categoria = $("#id_categoria").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url: './ajax/buscar_productos.php',
        type: 'POST',
        data: "action=ajax&page=" + page + "&q=" + q + "&id_categoria=" + id_categoria,
        beforeSend: function(objeto) {
            $("#loader").html('<img src="./img/ajax-loader.gif"> Cargando...');
            $("#resultados").html("Iniciando carga...");
            console.log("AJAX iniciado - Página: " + page + ", q: " + q + ", id_categoria: " + id_categoria);
        },
        success: function(data) {
            $("#resultados").html(data).fadeIn('slow');
            $("#loader").html('');
            console.log("Datos cargados: ", data.substring(0, 100));
        },
        error: function(xhr, status, error) {
            $("#resultados").html("<div class='alert alert-danger'>Error al cargar: " + error + " (Status: " + status + ")</div>");
            $("#loader").html('');
            console.log("Error AJAX: ", status, error, xhr.responseText);
        }
    });
}

$(document).ready(function() {
    load(1);
    console.log("Página cargada, llamando a load(1)");
});
</script>