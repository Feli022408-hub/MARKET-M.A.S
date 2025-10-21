<?php
if (isset($title)) {
?>
<style>
/* Sidebar container */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 80px;
  height: 100vh;
  background-color: #1e1e1e;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 20px;
  z-index: 1000;
}

/* Sidebar icons */
.sidebar a {
  color: #ffffff;
  text-align: center;
  margin: 20px 0;
  font-size: 18px;
  display: block;
  width: 100%;
}

.sidebar a:hover {
  color: #1e1e1e;
  background-color: #ffffff;
  border-radius: 8px;
}

/* Brand icon */
.sidebar .brand {
  font-size: 24px;
  margin-bottom: 40px;
  color: #ffffff;
}

/* User avatar section */
.sidebar .user-section {
  margin-top: auto;
  margin-bottom: 20px;
  text-align: center;
}

.sidebar .user-section img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.sidebar .user-section .dropdown {
  color: #ffffff;
  font-size: 12px;
  margin-top: 5px;
  cursor: pointer;
}
</style>

<div class="sidebar">
  <div class="user-section">
    <img src="img/web.png" alt="User Avatar">
  </div> 
  <a href="stock.php" title="Inventario"><i class="glyphicon glyphicon-barcode"></i></a>
  <a href="categorias.php" title="Categorías"><i class="glyphicon glyphicon-tags"></i></a>
  <a href="alertas.php" title="Alertas"><i class="glyphicon glyphicon-bell"></i></a>
  <a href="contabilidad.php" title="Contabilidad"><i class="glyphicon glyphicon-usd"></i></a>
  <a href="usuarios.php" title="Usuarios"><i class="glyphicon glyphicon-user"></i></a>
  <a href="https://marketmas.my.canva.site/" title="Acerca"><i class="glyphicon glyphicon-envelope"></i></a>

  <div class="user-section" onclick="location.href='login.php?logout'">>
    <i class="glyphicon glyphicon-off"></i>
  </div>
</div>
<?php
}
?>
