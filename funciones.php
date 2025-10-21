<?php
function get_row($table, $row, $id, $equal) {
    global $con;
    $sql = "SELECT $row FROM $table WHERE $id = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $equal); // 's' para string, ajusta según el tipo de $equal
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rw = mysqli_fetch_array($result);
        $value = $rw[$row] ?? null;
        mysqli_stmt_close($stmt);
        return $value;
    }
    return null;
}

function guardar_historial($id_producto, $user_id, $fecha, $nota, $reference, $quantity) {
    global $con;
    if ($id_producto <= 0) return false;
    $sql = "INSERT INTO historial (id_historial, id_producto, user_id, fecha, nota, referencia, cantidad) VALUES (NULL, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iisssi", $id_producto, $user_id, $fecha, $nota, $reference, $quantity);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    return false;
}

function agregar_stock($id_producto, $quantity) {
    global $con;
    $sql = "UPDATE products SET stock = stock + ? WHERE id_producto = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $id_producto);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result ? 1 : 0;
    }
    return 0;
}

function eliminar_stock($id_producto, $quantity) {
    global $con;
    $sql = "UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id_producto = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $id_producto);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result ? 1 : 0;
    }
    return 0;
}
?>