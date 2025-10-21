<form class="form-horizontal" method="post">
<div id="remove-stock" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title">Eliminar Stock</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="quantity_remove" class="col-sm-2 control-label">Cantidad</label>
          <div class="col-sm-6">
            <input type="number" min="1" name="quantity_remove" class="form-control" id="quantity_remove" value="" placeholder="Cantidad" required="">
          </div>
        </div>
        <div class="form-group">
          <label for="reference_remove" class="col-sm-2 control-label">Referencia</label>
          <div class="col-sm-6">
            <input type="text" name="reference_remove" class="form-control" id="reference_remove" value="" placeholder="Referencia">
          </div>
        </div>
        <input type="hidden" name="id_product_remove" id="id_product_remove">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-primary">Guardar datos</button>
      </div>
    </div>
  </div>
</div>
</form>
<script>
$('#remove-stock').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var modal = $(this);
    modal.find('.modal-body #id_product_remove').val(id);
    console.log("Modal restar abierto para ID: " + id);
});
</script>