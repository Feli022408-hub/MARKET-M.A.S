<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
    exit;
}

/* Connect To Database */
require_once("config/db.php");
require_once("config/conexion.php");

$title = "Alertas de Inventario | Market M.A.S";
$active_alertas = "active"; // Para destacar en la barra de navegación si es necesario

// Parámetro de búsqueda (opcional)
$search = isset($_GET['q']) ? mysqli_real_escape_string($con, $_GET['q']) : '';

// Consulta para contar productos con stock bajo
$count_query = "SELECT COUNT(*) as low_count FROM products WHERE stock < 10";
if (!empty($search)) {
    $count_query .= " AND (codigo_producto LIKE '%$search%' OR nombre_producto LIKE '%$search%')";
}
$count_result = mysqli_query($con, $count_query);
if (!$count_result) {
    die("Error en la consulta de conteo: " . mysqli_error($con));
}
$low_stock_count = mysqli_fetch_array($count_result)['low_count'];

// Consulta para obtener productos con stock bajo
$alert_query = "SELECT p.id_producto, p.codigo_producto, p.nombre_producto, p.stock, p.precio_producto, c.nombre_categoria 
                FROM products p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.stock < 10";
if (!empty($search)) {
    $alert_query .= " AND (p.codigo_producto LIKE '%$search%' OR p.nombre_producto LIKE '%$search%')";
}
$alert_query .= " ORDER BY p.stock ASC";
$alert_result = mysqli_query($con, $alert_query);
if (!$alert_result) {
    die("Error en la consulta de alertas: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("head.php"); ?>
    <style>
        .low-stock-row {
            background-color: #1f1f1f !important; 
            color: #ff0019ff;
            font-weight: bold;
        }
        .stock-critical {
            color: #ff0019ff; 
        }
        .alert-header {
            background-color: #1f1f1f;
            border-color: #000000ff;
            color: #ffffffff;
        }
    </style>
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="panel panel-danger">
            <div class="panel-heading alert-header">
                <h4><i class="glyphicon glyphicon-exclamation-sign"></i> Alertas de Inventario Bajo</h4>
                <p class="text-danger">Productos con menos de 10 unidades en stock. ¡Acción requerida!</p>
            </div>
            <div class="panel-body">
                <?php if ($low_stock_count > 0): ?>
                    <div class="alert alert-warning" role="alert">
                        <strong>¡Atención!</strong> Hay <strong><?php echo $low_stock_count; ?></strong> productos con stock bajo. Revisa la lista a continuación.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success" role="alert">
                        <strong>¡Excelente!</strong> No hay productos con stock bajo en este momento.
                    </div>
                <?php endif; ?>

                <!-- Tabla de resultados -->
                <?php if ($low_stock_count > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="alert-header">
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio</th>
                                    <th>Stock Actual</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_array($alert_result)): ?>
                                    <tr class="low-stock-row">
                                        <td><?php echo htmlspecialchars($row['codigo_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre_categoria'] ?? 'Sin categoría'); ?></td>
                                        <td>$<?php echo number_format($row['precio_producto'] / 100, 2); ?></td> <!-- Ajuste por centavos -->
                                        <td class="stock-critical"><?php echo $row['stock']; ?> unidades</td>
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
                    <p class="text-center">No hay alertas activas con el filtro actual.</p>
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