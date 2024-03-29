<?php
$coupon = CouponData::getById($_GET["id"]);
?>
        <!-- Main Content -->

          <div class="row">
            <div class="col-md-12">
  <!-- Button trigger modal -->


            <h2>EDITAR CUPON</h2>
            </div>
            </div>

          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-default">
                <div class="panel-heading">
                  <i class="fa fa-ticket"></i> Editar Cupon
                </div>
                <div class="panel-body ">

<form class="form-horizontal" role="form" method="post" action="index.php?action=updatecoupon">
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Codigo</label>
    <div class="col-lg-10">
      <input type="text" class="form-control" name="name" value="<?php echo $coupon->name; ?>" placeholder="Codigo del cupon">
    </div>
  </div>
  <div class="form-group">
    <label for="inputPassword1" class="col-lg-2 control-label">Descripcion</label>
    <div class="col-lg-10">
      <textarea class="form-control" id="inputPassword1" placeholder="Descripcion" rows="3" name="description"><?php echo $coupon->description; ?></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Valor</label>
    <div class="col-lg-10">
      <input type="text" class="form-control" name="val" value="<?php echo $coupon->val; ?>" placeholder="Valor del cupon">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Producto</label>
    <div class="col-lg-10">
<?php
$categories = ProductData::getAll();
 if(count($categories)>0):?>
<select name="product_id" class="form-control">
<option value="">-- CUALQUIERA --</option>

<?php foreach($categories as $cat):?>
<option value="<?php echo $cat->id; ?>" <?php if($cat->id==$coupon->product_id){ echo "selected";}?>><?php echo $cat->name; ?></option>
<?php endforeach; ?>
</select>
<p class="help-block">Al seleccionar un producto, el descuento solo se aplica a una pieza.</p>
 <?php endif; ?>
    </div>
  </div>
<div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="is_multiple" <?php if($coupon->is_multiple){ echo "checked"; }?>> Es multiple
<p class="help-block">Al seleccionar un producto, habilita descuento para N piezas.</p>
        </label>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail1" class="col-lg-2 control-label">Inicio</label>
    <div class="col-lg-4">
      <input type="date" class="form-control" name="start_at" value="<?php echo $coupon->start_at; ?>">
    </div>
    <label for="inputEmail1" class="col-lg-2 control-label">Fin</label>
    <div class="col-lg-4">
      <input type="date" class="form-control" name="finish_at" value="<?php echo $coupon->finish_at; ?>">
    </div>
  </div>
<div class="form-group">
    <div class="col-lg-offset-2 col-lg-10">
      <div class="checkbox">
        <label>
          <input type="checkbox" name="is_active" <?php if($coupon->is_active){ echo "checked"; }?>> Activar
<p class="help-block">El cupon esta listo para usar.</p>
        </label>
      </div>
    </div>
  </div>

  <div class="form-group">
    <div class="col-lg-offset-2 col-lg-6">
    <input type="hidden" name="id" value="<?php echo $coupon->id; ?>">
      <button type="submit" class="btn btn-success btn-block">Actualizar Cupon</button>
    </div>
    <div class="col-lg-4">
      <button type="reset" class="btn btn-default btn-block">Limpiar Campos</button>
    </div>
  </div>
</form>
                  
                </div>
              </div>
            </div>

          </div>

<br><br>