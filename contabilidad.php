<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
    exit;
}

/* Connect To Database */
require_once("config/db.php");
require_once("config/conexion.php");

$title = "Inventario con Alto Stock | Market M.A.S";
$active_stock_alto = "active"; // Para destacar en la barra de navegación si es necesario

// Parámetro de búsqueda (opcional)
$search = isset($_GET['q']) ? mysqli_real_escape_string($con, $_GET['q']) : '';

// Consulta para contar productos con stock alto (≥ 10)
$count_query = "SELECT COUNT(*) as high_count FROM products WHERE stock >= 10";
if (!empty($search)) {
    $count_query .= " AND (codigo_producto LIKE '%$search%' OR nombre_producto LIKE '%$search%')";
}
$count_result = mysqli_query($con, $count_query);
if (!$count_result) {
    die("Error en la consulta de conteo: " . mysqli_error($con));
}
$high_stock_count = mysqli_fetch_array($count_result)['high_count'];

// Consulta para obtener productos con stock alto
$high_query = "SELECT p.id_producto, p.codigo_producto, p.nombre_producto, p.stock, p.precio_producto, c.nombre_categoria 
               FROM products p 
               LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
               WHERE p.stock >= 10";
if (!empty($search)) {
    $high_query .= " AND (p.codigo_producto LIKE '%$search%' OR nombre_producto LIKE '%$search%')";
}
$high_query .= " ORDER BY p.stock DESC"; // Ordenar por stock descendente
$high_result = mysqli_query($con, $high_query);
if (!$high_result) {
    die("Error en la consulta de stock alto: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("head.php"); ?>
    <style>
        .product-name-high {
            color: #28a745; 
            font-weight: bold;
        }
        .stock-high {
            color: #28a745; 
        }
        .high-header {
            background-color: #1f1f1f;
            border-color: #000000ff;
            color: #ffffffff;
        }
        .table-hover tbody tr:hover {
            background-color: transparent; 
        }
    </style>
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="panel panel-success">
            <div class="panel-heading high-header">
                <h4><i class="glyphicon glyphicon-ok-sign"></i> Inventario con Alto Stock</h4>
                <p class="text-success">Productos con 10 o más unidades en stock.</p>
            </div>
            <div class="panel-body">
                <?php if ($high_stock_count > 0): ?>
                    <div class="alert alert-success" role="alert">
                        <strong>¡Bien hecho!</strong> Hay <strong><?php echo $high_stock_count; ?></strong> productos con stock alto. Revisa la lista a continuación.
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        <strong>¡Atención!</strong> No hay productos con stock alto en este momento.
                    </div>
                <?php endif; ?>


                <!-- Tabla de resultados -->
                <?php if ($high_stock_count > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="high-header">
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock Actual</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_array($high_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['codigo_producto']); ?></td>
                                        <td class="product-name-high"><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                                        <td>$<?php echo number_format($row['precio_producto'] / 100, 2); ?></td> <!-- Ajuste por centavos -->
                                        <td class="stock-high"><?php echo $row['stock']; ?> unidades</td>
                                        <td>
                                            <a href="stock.php?edit=<?php echo $row['id_producto']; ?>" class="btn btn-primary btn-sm">
                                                <span class="glyphicon glyphicon-edit"></span> Editar
                                            </a>
                                            <a href="stock.php?delete=<?php echo $row['id_producto']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar?')">
                                                <span class="glyphicon glyphicon-trash"></span> Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">No hay productos con stock alto con el filtro actual.</p>
                <?php endif; ?>

                <!-- Enlace de regreso al inventario -->
                <div class="text-center">
                    <a href="stock.php" class="btn btn-info">Ver Inventario Completo</a>
                </div>
            </div>
        </div>
    </div>
    <?php include("footer.php"); ?>
</body>
</html>