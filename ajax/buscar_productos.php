<?php
include('is_logged.php');
require_once("../config/db.php");
require_once("../config/conexion.php");
include("../funciones.php");

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);
    $query = "DELETE FROM products WHERE id_producto = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_producto);
    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success'>Eliminado exitosamente</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar: " . mysqli_error($con) . "</div>";
    }
    mysqli_stmt_close($stmt);
}

if ($action == 'ajax') {
    $q = mysqli_real_escape_string($con, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $id_categoria = intval($_REQUEST['id_categoria']);
    $aColumns = array('p.codigo_producto', 'p.nombre_producto'); 
    $sTable = "products p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria";
    $sWhere = "";

    if ($q != "") {
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }

    if ($id_categoria > 0) {
        $sWhere .= ($q == "") ? "WHERE p.id_categoria = '$id_categoria'" : " AND p.id_categoria = '$id_categoria'";
    }
    $sWhere .= " ORDER BY p.id_producto DESC";
    include 'pagination.php';
    $page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page = 10;
    $adjacents = 4;
    $offset = ($page - 1) * $per_page;

    $count_query = mysqli_query($con, "SELECT count(*) AS numrows FROM $sTable $sWhere");
    $row = mysqli_fetch_array($count_query);
    $numrows = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $reload = './stock.php';

    $sql = "SELECT p.id_producto, p.codigo_producto, p.nombre_producto, p.stock, p.precio_producto, c.nombre_categoria 
            FROM $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($con, $sql);

    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>Código</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>';
    echo '<tbody>';

    if ($numrows > 0) {
        while ($row = mysqli_fetch_array($query)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['codigo_producto']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nombre_producto']) . '</td>';
            echo '<td>' . htmlspecialchars($row['nombre_categoria'] ?? 'Sin categoría') . '</td>';
            echo '<td>$' . number_format($row['precio_producto'] / 100, 2) . '</td>';
            echo '<td>' . $row['stock'] . ' unidades</td>';
            echo '<td>';
            echo '<a href="#" data-toggle="modal" data-target="#add-stock" data-id="' . $row['id_producto'] . '" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus"></span> Añadir</a> ';
            echo '<a href="#" data-toggle="modal" data-target="#remove-stock" data-id="' . $row['id_producto'] . '" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-minus"></span> Restar</a> ';
            echo '<a href="producto.php?id=' . $row['id_producto'] . '" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-info-sign"></span> Ver</a> ';
            echo '<a href="stock.php?delete=' . $row['id_producto'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Eliminar?\')"><span class="glyphicon glyphicon-trash"></span> Eliminar</a>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6">No se encontraron productos.</td></tr>';
    }

    echo '</tbody></table>';

    if ($total_pages > 1) {
        echo '<div class="text-center"><ul class="pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<li' . ($page == $i ? ' class="active"' : '') . '><a href="#" onclick="load(' . $i . '); return false;">' . $i . '</a></li>';
        }
        echo '</ul></div>';
    }

    echo '</div>';
}
?>